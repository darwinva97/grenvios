<?php
/* Solo terminal: si la carpeta del tema se sube a un servidor, esto no debe
 * poder ejecutarse desde el navegador. */
if ( PHP_SAPI !== 'cli' ) { http_response_code( 404 ); exit; }
/**
 * Auditoría completa de Grenvíos: recorre TODAS las páginas y entradas publicadas
 * dos veces —como visitante y como administrador— y resume lo que está mal.
 *
 *   php auditar-sitio.php [host] [limite]
 *   php auditar-sitio.php greenvios.localhost        (todas)
 *   php auditar-sitio.php greenvios.localhost 20     (las 20 primeras, prueba rápida)
 *
 * Visitante: código HTTP, errores PHP en el HTML, H1 único, title, meta description,
 *            canonical, schema, tokens {{…}} sin resolver, preguntas repetidas en el
 *            FAQPage, palabras de contenido y enlaces internos rotos.
 * Admin:     que cada página tenga el panel «Editar página» (o el de guías en las
 *            entradas), que el banner sea la primera sección y que ninguna sección
 *            salga vacía.
 *
 * Usa 4 peticiones en paralelo como máximo: con más, MySQL de Laragon se queda sin
 * memoria y aparecen falsos fallos (código 0).
 */
libxml_use_internal_errors( true );
$host  = $argv[1] ?? 'greenvios.localhost';
$limit = (int) ( $argv[2] ?? 0 );
$gr_load = realpath( __DIR__ . '/../../../../../../../wp-load.php' );
if ( ! $gr_load ) { fwrite( STDERR, "No encuentro wp-load.php\n" ); exit( 1 ); }

$_SERVER['HTTP_HOST'] = $host; $_SERVER['REQUEST_SCHEME'] = 'http'; $_SERVER['REQUEST_URI'] = '/';
define( 'WP_USE_THEMES', false );
require $gr_load;

/* Cookie de administrador (2 h) y lista de URL. */
$admin  = get_users( array( 'role' => 'administrator', 'number' => 1 ) );
$cookie = LOGGED_IN_COOKIE . '=' . wp_generate_auth_cookie( $admin[0]->ID, time() + 7200, 'logged_in' );
$ids    = get_posts( array( 'post_type' => array( 'page', 'post' ), 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids', 'lang' => '' ) );
$rows   = array();
foreach ( $ids as $id ) $rows[] = array( get_post_type( $id ), $id, get_permalink( $id ) );
if ( $limit ) $rows = array_slice( $rows, 0, $limit );

function gr_fetch_all( $urls, $cookie = '' ) {
	$out = array(); $mh = curl_multi_init(); $q = $urls; $act = array();
	$add = function () use ( &$q, &$act, $mh, $cookie ) {
		if ( ! $q ) return;
		$u  = array_shift( $q );
		$ch = curl_init( $u );
		curl_setopt_array( $ch, array( CURLOPT_RETURNTRANSFER => 1, CURLOPT_TIMEOUT => 120, CURLOPT_COOKIE => $cookie ) );
		curl_multi_add_handle( $mh, $ch ); $act[ (int) $ch ] = array( $ch, $u );
	};
	for ( $i = 0; $i < 4; $i++ ) $add();
	do {
		curl_multi_exec( $mh, $run ); curl_multi_select( $mh, 1 );
		while ( $in = curl_multi_info_read( $mh ) ) {
			list( $ch, $u ) = $act[ (int) $in['handle'] ]; unset( $act[ (int) $in['handle'] ] );
			$out[ $u ] = array( curl_getinfo( $ch, CURLINFO_HTTP_CODE ), curl_multi_getcontent( $ch ) );
			curl_multi_remove_handle( $mh, $ch ); $add();
		}
	} while ( $run || $act || $q );
	return $out;
}

$urls = array_column( $rows, 2 );
fwrite( STDERR, 'Visitante: ' . count( $urls ) . " URL…\n" );
$pub = gr_fetch_all( $urls );
fwrite( STDERR, "Administrador…\n" );
$adm = gr_fetch_all( $urls, $cookie );

$prob = array(); $links = array(); $words = array();
$mal  = function ( $tipo, $u ) use ( &$prob ) { $prob[ $tipo ][] = $u; };
foreach ( $rows as $r ) {
	list( $type, $id, $u ) = $r;
	list( $code, $h ) = $pub[ $u ];
	if ( $code !== 200 ) { $mal( "HTTP $code", $u ); continue; }
	if ( preg_match( '/<b>(Warning|Fatal error|Notice|Deprecated)<\/b>|critical error/', $h ) ) $mal( 'Error PHP en el HTML', $u );
	$d = new DOMDocument(); $d->loadHTML( '<?xml encoding="utf-8"?>' . $h ); $x = new DOMXPath( $d );
	$h1 = $x->query( '//h1' )->length;
	if ( $h1 !== 1 ) $mal( "H1 distinto de 1 ($h1)", $u );
	if ( trim( $x->evaluate( 'string(//title)' ) ) === '' ) $mal( 'Sin title', $u );
	$desc = trim( $x->evaluate( "string(//meta[@name='description']/@content)" ) );
	if ( $desc === '' ) $mal( 'Sin meta description', $u );
	elseif ( mb_strlen( $desc ) < 70 || mb_strlen( $desc ) > 170 ) $mal( 'Meta description fuera de 70–170', $u );
	if ( $x->query( "//link[@rel='canonical']" )->length !== 1 ) $mal( 'Canonical ausente o doble', $u );
	if ( ! $x->query( "//script[@type='application/ld+json']" )->length ) $mal( 'Sin schema', $u );
	if ( preg_match( '/\{\{[a-z0-9_]+\}\}/', $h ) ) $mal( 'Token {{…}} visible al público', $u );
	$qs = array();
	foreach ( $x->query( "//script[@type='application/ld+json']" ) as $s ) {
		$j = json_decode( $s->textContent, true );
		array_walk_recursive( $j, function () {} );
		$walk = function ( $n ) use ( &$walk, &$qs ) { if ( ! is_array( $n ) ) return; if ( ( $n['@type'] ?? '' ) === 'Question' ) $qs[] = mb_strtolower( trim( $n['name'] ) ); foreach ( $n as $v ) $walk( $v ); };
		$walk( $j );
	}
	if ( count( $qs ) !== count( array_unique( $qs ) ) ) $mal( 'Pregunta repetida en el FAQPage', $u );
	$txt = '';
	foreach ( $x->query( "//body//text()[not(ancestor::script) and not(ancestor::style) and not(ancestor::header) and not(ancestor::footer) and not(ancestor::nav)]" ) as $t ) $txt .= ' ' . $t->nodeValue;
	$words[ $u ] = str_word_count( preg_replace( '/[^\p{L}\s]/u', ' ', $txt ), 0, 'áéíóúñÁÉÍÓÚÑüÜ' );
	foreach ( $x->query( '//a[@href]' ) as $a ) {
		$raw = trim( $a->getAttribute( 'href' ) );
		/* Enlace relativo sin barra («slug%», «pagina/»): casi siempre un marcador
		 * %P:/%H% que no se resolvió. Así estuvieron rotos 132 enlaces entre guías. */
		if ( $raw !== '' && ! preg_match( '~^(https?:|/|#|mailto:|tel:|javascript:|data:)~i', $raw ) ) $mal( 'Enlace relativo sospechoso (marcador sin resolver)', $u . '  →  ' . $raw );
		$hr = preg_replace( '/#.*$/', '', $raw );
		if ( $hr !== '' && $hr[0] === '/' && ( $hr[1] ?? '' ) !== '/' ) $hr = 'http://' . $host . $hr;
		if ( strpos( $hr, 'http://' . $host ) === 0 ) $links[ $hr ][ $u ] = 1;
	}

	/* Panel del administrador. */
	list( $acode, $ah ) = $adm[ $u ];
	if ( $acode !== 200 ) { $mal( "Admin HTTP $acode", $u ); continue; }
	if ( $type === 'post' ) { if ( strpos( $ah, 'id="gr-fe-panel"' ) === false ) $mal( 'Guía sin panel de edición', $u ); continue; }
	if ( strpos( $ah, 'id="nep-panel"' ) === false ) { $mal( 'Página sin panel «Editar página»', $u ); continue; }
	$ad = new DOMDocument(); $ad->loadHTML( '<?xml encoding="utf-8"?>' . $ah ); $ax = new DOMXPath( $ad );
	$accs = $ax->query( "//div[@id='nep-panel']//div[contains(@class,'nep-accordion')]" );
	$first = $accs->length ? trim( $ax->evaluate( 'string(.//button/span)', $accs->item( 0 ) ) ) : '';
	if ( ! preg_match( '/Banner|Hero|hero/u', $first ) ) $mal( 'El panel no empieza por el banner', $u );
	foreach ( $accs as $acc ) {
		$n = $ax->query( './/*[@data-field-key or @data-global-key or @data-pais-key or @data-rep-key]', $acc )->length;
		if ( ! $n ) $mal( 'Sección vacía en el panel: ' . trim( $ax->evaluate( 'string(.//button/span)', $acc ) ), $u );
	}
}

/* Enlaces internos que no son ninguna de las URL auditadas. */
$extra = array_diff( array_keys( $links ), $urls );
foreach ( gr_fetch_all( array_values( $extra ) ) as $l => $res ) {
	if ( $res[0] >= 400 || $res[0] === 0 ) $mal( "Enlace roto ({$res[0]}) $l", array_key_first( $links[ $l ] ) );
}

echo "\n== AUDITORÍA: " . count( $rows ) . ' URL (' . count( array_filter( $rows, fn( $r ) => $r[0] === 'page' ) ) . ' páginas, ' . count( array_filter( $rows, fn( $r ) => $r[0] === 'post' ) ) . " entradas)\n";
if ( ! $prob ) echo "Sin problemas.\n";
foreach ( $prob as $tipo => $us ) {
	echo "\n[" . count( $us ) . "] $tipo\n";
	foreach ( array_slice( $us, 0, 8 ) as $x ) echo "    $x\n";
}
asort( $words );
echo "\nPáginas más delgadas (palabras de contenido):\n";
foreach ( array_slice( $words, 0, 8, true ) as $u => $w ) echo "    $w  $u\n";
exit( $prob ? 1 : 0 );
