<?php
/* Solo terminal. */
if ( PHP_SAPI !== 'cli' ) { http_response_code( 404 ); exit; }
/**
 * Cobertura del panel «Editar página»: ¿cada texto que se VE en la página está
 * en algún campo del panel?
 *
 *   php cobertura-panel.php [host] [ruta]        ruta: pe (defecto) | cu | todas
 *
 * Por cada página (como administrador, en serie, sin cargar el servidor):
 *   · recoge los textos visibles del contenido (h1–h4, p, li, botones, celdas de
 *     cabecera de tarjeta…), sin cabecera, pie, menús ni los propios paneles;
 *   · recoge los valores de todos los campos del panel (textos, listas,
 *     repeaters, datos del país, ajustes globales);
 *   · un texto está «cubierto» si aparece dentro de algún valor del panel.
 *
 * Se excluyen a propósito los bloques que NO son contenido de la página sino
 * navegación o datos de otro sitio: enlazado «Continúa tu envío», «Del blog» y
 * «Guías que te pueden ayudar» (títulos de entradas), tablas de destinos (datos
 * del gestor), índice «En esta página» y el cotizador. Se listan aparte por si
 * se quiere revisar.
 *
 * Salida: por página, % de cobertura y los textos no editables. Código 1 si
 * alguna página tiene textos sin cubrir.
 */
libxml_use_internal_errors( true );
$host = $argv[1] ?? 'greenvios.localhost';
$ruta = $argv[2] ?? 'pe';
$_SERVER['HTTP_HOST'] = $host; $_SERVER['REQUEST_SCHEME'] = 'http'; $_SERVER['REQUEST_URI'] = '/';
define( 'WP_USE_THEMES', false );
require realpath( __DIR__ . '/../../../../../../../wp-load.php' );

$admin  = get_users( array( 'role' => 'administrator', 'number' => 1 ) );
$cookie = LOGGED_IN_COOKIE . '=' . wp_generate_auth_cookie( $admin[0]->ID, time() + 7200, 'logged_in' );
$args   = array( 'post_type' => 'page', 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids' );
$args['lang'] = $ruta === 'todas' ? '' : $ruta;
$ids = get_posts( $args );
/* Tercer argumento opcional: solo las URL que contengan ese texto (p. ej. «destinos»). */
$filtro = $argv[3] ?? '';
if ( $filtro !== '' ) $ids = array_values( array_filter( $ids, function ( $id ) use ( $filtro ) { return strpos( get_permalink( $id ), $filtro ) !== false; } ) );

/* Sede: los valores del panel llevan {{origen_ciudad}}; la página, «Lima». */
$tok = function ( $s ) { return function_exists( 'grenvios_sede_tokens_apply' ) ? grenvios_sede_tokens_apply( $s ) : $s; };
$norm = function ( $s ) {
	$s = html_entity_decode( wp_strip_all_tags( (string) $s ), ENT_QUOTES, 'UTF-8' );
	$s = preg_replace( '/[«»"“”]/u', '', $s );
	return mb_strtolower( trim( preg_replace( '/\s+/u', ' ', $s ) ) );
};
$excluir = '[contains(concat(" ",normalize-space(@class)," ")," grenvios-related ") or contains(@class,"gr-bep") or contains(@class,"grenvios-guides") or contains(@class,"gr-toc") or contains(@class,"gr-table-wrap") or contains(@class,"grenvios-table-wrap") or contains(@class,"gr-hq") or contains(@class,"dest-cotiza") or @id="nep-panel" or @id="gr-fe-panel" or @id="nep-fab" or @id="wpadminbar" or contains(@class,"blog-card") or contains(@class,"grenvios-guide-card") or self::header or self::footer or self::nav or self::form]';

$total_txt = 0; $total_ok = 0; $malas = array();
foreach ( $ids as $n => $id ) {
	$url = get_permalink( $id );
	$ch  = curl_init( $url );
	curl_setopt_array( $ch, array( CURLOPT_RETURNTRANSFER => 1, CURLOPT_COOKIE => $cookie, CURLOPT_TIMEOUT => 90 ) );
	$h = curl_exec( $ch );
	if ( curl_getinfo( $ch, CURLINFO_HTTP_CODE ) !== 200 || ! $h ) { $malas[ $url ] = array( 'HTTP ' . curl_getinfo( $ch, CURLINFO_HTTP_CODE ) ); continue; }
	$d = new DOMDocument(); $d->loadHTML( '<?xml encoding="utf-8"?>' . $h ); $x = new DOMXPath( $d );

	if ( ! $x->query( "//*[@id='nep-panel']" )->length ) { $malas[ $url ] = array( 'SIN PANEL «Editar página»' ); continue; }

	/* Valores del panel. */
	$blob = '';
	foreach ( $x->query( "//*[@id='nep-panel']//input[@type='text' or @type='hidden' or not(@type)] | //*[@id='nep-panel']//textarea" ) as $el ) {
		$v = $el->nodeName === 'textarea' ? $el->textContent : $el->getAttribute( 'value' );
		$blob .= ' ¶ ' . $norm( $tok( $v ) );
	}
	/* Los textos globales (pie, barra superior) y los generados por la sede. */
	$blob .= ' ¶ ' . $norm( $tok( '{{origen_ciudad}} {{origen_pais}}' ) );

	/* Textos visibles del contenido. */
	$sin = array(); $cnt = 0;
	$q = "//body//*[self::h1 or self::h2 or self::h3 or self::h4 or self::p or self::li or self::dt or self::dd or self::blockquote or (self::a and contains(@class,'default-btn')) or (self::span and (contains(@class,'dest-fact') or contains(@class,'sub-heading')))][not(ancestor-or-self::*$excluir)]";
	foreach ( $x->query( $q ) as $el ) {
		/* Solo el texto propio si tiene hijos de bloque (evita contar dos veces). */
		if ( $x->query( './/h1|.//h2|.//h3|.//h4|.//p|.//li', $el )->length ) continue;
		$t = $norm( $el->textContent );
		if ( mb_strlen( $t ) < 12 ) continue;
		$cnt++;
		$aguja = mb_substr( $t, 0, 60 );
		if ( strpos( $blob, $aguja ) !== false ) continue;
		/* Frases largas editadas en dos campos: probar la mitad final. */
		if ( mb_strlen( $t ) > 80 && strpos( $blob, mb_substr( $t, -50 ) ) !== false ) continue;
		/* Elementos compuestos (título + texto en campos distintos, p. ej. las
		 * garantías del hero): cubierto si cada parte está en el panel. */
		$partes = array();
		foreach ( $x->query( './/text()', $el ) as $hijo ) { $pt = $norm( $hijo->nodeValue ); if ( mb_strlen( $pt ) >= 3 ) $partes[] = $pt; }
		if ( count( $partes ) > 1 ) {
			$todas = true;
			foreach ( $partes as $pt ) if ( strpos( $blob, mb_substr( $pt, 0, 60 ) ) === false ) { $todas = false; break; }
			if ( $todas ) continue;
		}
		$sin[] = mb_substr( $t, 0, 90 );
	}
	$total_txt += $cnt; $total_ok += $cnt - count( $sin );
	if ( $sin ) $malas[ $url ] = array_values( array_unique( $sin ) );
	fwrite( STDERR, sprintf( "%3d/%d %s %s\n", $n + 1, count( $ids ), $sin ? '✗' : '✓', str_replace( 'http://' . $host, '', $url ) ) );
}

printf( "\n== COBERTURA DEL PANEL (ruta %s): %d páginas · %d textos · %.1f %% editables\n", $ruta, count( $ids ), $total_txt, $total_txt ? 100 * $total_ok / $total_txt : 0 );
if ( ! $malas ) { echo "Todo el contenido visible es editable desde el panel.\n"; exit( 0 ); }
echo count( $malas ) . " páginas con textos que NO se pueden editar:\n";
foreach ( $malas as $u => $ts ) {
	echo "\n" . str_replace( 'http://' . $host, '', $u ) . '  (' . count( $ts ) . ")\n";
	foreach ( $ts as $t ) echo "   · $t\n";
	if ( count( $ts ) > 12 ) echo '   … y ' . ( count( $ts ) - 12 ) . " más\n";
}
exit( 1 );
