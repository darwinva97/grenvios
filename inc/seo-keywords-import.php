<?php
/**
 * Grenvíos — Importador de investigación de keywords (Google Keyword Planner).
 *
 * Sube el CSV que exporta Keyword Planner y hace tres cosas que a mano cuestan
 * horas y se hacen mal:
 *
 *   1. LIMPIA. Descarta las keywords que no se pueden trabajar:
 *      · marcas de la competencia (dhl, fedex, olva, servientrega…): no se
 *        rankean y el tráfico no convierte;
 *      · términos de OTROS mercados (carta documento → Argentina, chilexpress →
 *        Chile, correos/certificada → España, servientrega → Colombia).
 *      Este segundo filtro es el importante: un export sin restringir la
 *      ubicación mezcla la demanda de cinco países y hace creer que hay volumen
 *      donde no lo hay.
 *
 *   2. AGRUPA por intención y propone a qué página del sitio pertenece cada
 *      keyword, según el mapa pillar-cluster del tema.
 *
 *   3. ASIGNA. Con un clic escribe la keyword objetivo de cada página y guarda
 *      las secundarias (las que van en H2, en el cuerpo y en las FAQ).
 *
 * El resultado queda guardado, así que sirve de documento de trabajo: qué se
 * descartó, por qué, y qué keyword quedó en cada página.
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

const GRENVIOS_KW2_META = 'grenvios_seo_kw_secundarias';

/* ══════════════════════════════════════
   REGLAS DE LIMPIEZA
   Editables desde la pantalla: cada sitio tiene sus competidores.
══════════════════════════════════════ */
function grenvios_kw_filters() {
	$d = array(
		'marcas'   => "dhl\nfedex\nups\ntnt\nolva\nserpost\nshalom\nurbano\nscharff\nsavar\nservientrega\ninterrapidisimo\ninter rapidisimo\nchilexpress\nstarken\ncorreos de\ncorreos certificado\nandreani\noca\ndeprisa\nseur\nmrw\naramex\nestafeta\ncoordinadora\navianca\nlatam\nglovo\n472",
		'mercados' => "carta documento\ncarta certificada\ncertificado correos\nacuse de recibo\nburofax\nchile\ncolombia\nmexico\nméxico\nargentina\nespaña\nespana\ncedula\ncédula\ndni\nreino unido\nsanto domingo",
		'minvol'   => 0,
	);
	$s = get_option( 'grenvios_kw_filters', array() );
	return array_merge( $d, is_array( $s ) ? $s : array() );
}

function grenvios_kw_filter_list( $key ) {
	$f = grenvios_kw_filters();
	$out = array();
	foreach ( preg_split( '/[\r\n]+/', (string) $f[ $key ] ) as $t ) {
		$t = trim( mb_strtolower( $t ) );
		if ( $t !== '' ) $out[] = $t;
	}
	return $out;
}

/* Motivo de descarte de una keyword ('' si es usable).
 * La coincidencia es por PALABRA COMPLETA, no por subcadena: con subcadena,
 * "ups" descartaria "grupos" y "oca" descartaria "colocar". Los terminos de
 * varias palabras ("carta documento") se buscan como frase. */
function grenvios_kw_discard_reason( $kw ) {
	$k = ' ' . mb_strtolower( remove_accents( $kw ) ) . ' ';
	foreach ( array( 'marcas' => 'marca de la competencia', 'mercados' => 'otro mercado' ) as $lista => $motivo ) {
		foreach ( grenvios_kw_filter_list( $lista ) as $m ) {
			$m = remove_accents( $m );
			if ( $m === '' ) continue;
			if ( preg_match( '/(?<![\w])' . preg_quote( $m, '/' ) . '(?![\w])/u', $k ) ) return $motivo;
		}
	}
	return '';
}

/* ══════════════════════════════════════
   MAPA INTENCIÓN → PÁGINA
   Se evalúa en orden: la primera regla que encaja gana, así que las reglas
   específicas van antes que las genéricas.
══════════════════════════════════════ */
function grenvios_kw_intent_rules() {
	$rules = array(
		array( 'slug' => 'envios-para-empresas',               'pats' => array( 'empresa', 'pyme', 'corporativ', 'negocio', 'comercial' ) ),
		array( 'slug' => 'apostilla-y-traduccion',             'pats' => array( 'apostill', 'legaliza', 'traduc', 'haya' ) ),
		array( 'slug' => 'carga-internacional',                'pats' => array( 'carga', 'flete', 'contenedor', 'exportar', 'importar', 'aduana', 'arancel', 'pallet', 'tonelada' ) ),
		array( 'slug' => 'envio-internacional-de-documentos',  'pats' => array( 'documento', 'carta', 'sobre', 'correspondencia', 'papeles', 'pasaporte', 'titulo', 'certificad' ) ),
		array( 'slug' => 'envio-internacional-de-paquetes',    'pats' => array( 'paquete', 'encomienda', 'caja', 'equipaje', 'compras', 'mercancia', 'paqueteria' ) ),
		array( 'slug' => 'rastreo-de-envios',                  'pats' => array( 'rastre', 'seguimiento', 'track', 'guia', 'numero de envio' ) ),
		array( 'slug' => 'cotizar',                            'pats' => array( 'precio', 'costo', 'coste', 'cuanto cuesta', 'tarifa', 'cotiz', 'calcul' ) ),
		array( 'slug' => 'preguntas-frecuentes',               'pats' => array( 'se puede', 'que puedo', 'como enviar', 'requisito', 'prohibid', 'restriccion' ) ),
	);
	// Países con página propia: la intención geográfica manda sobre la de servicio.
	if ( function_exists( 'grenvios_destinos' ) ) {
		$geo = array();
		foreach ( grenvios_destinos() as $slug => $d ) {
			$name = mb_strtolower( remove_accents( isset( $d['title'] ) ? $d['title'] : $slug ) );
			$geo[] = array( 'slug' => $slug, 'pats' => array( $name ) );
		}
		$rules = array_merge( $geo, $rules );
	}
	return apply_filters( 'grenvios_kw_intent_rules', $rules );
}

/* Página propuesta para una keyword ('' si ninguna encaja). */
function grenvios_kw_suggest_slug( $kw ) {
	$k = mb_strtolower( remove_accents( $kw ) );
	foreach ( grenvios_kw_intent_rules() as $r ) {
		foreach ( $r['pats'] as $p ) {
			if ( strpos( $k, remove_accents( $p ) ) !== false ) return $r['slug'];
		}
	}
	return '';
}

/* ══════════════════════════════════════
   LECTURA DEL CSV
   Keyword Planner exporta en UTF-16 con tabuladores; otras herramientas usan
   UTF-8 con comas o punto y coma. Se detectan los tres casos.
══════════════════════════════════════ */
function grenvios_kw_parse_csv( $path ) {
	$raw = file_get_contents( $path );
	if ( $raw === false ) return new WP_Error( 'grenvios_kw', 'No se pudo leer el archivo.' );

	// UTF-16 (con o sin BOM) → UTF-8
	if ( substr( $raw, 0, 2 ) === "\xFF\xFE" || substr( $raw, 0, 2 ) === "\xFE\xFF" || substr_count( substr( $raw, 0, 200 ), "\x00" ) > 20 ) {
		$raw = mb_convert_encoding( $raw, 'UTF-8', 'UTF-16' );
	}
	$raw = preg_replace( '/^\xEF\xBB\xBF/', '', $raw );

	$lines = preg_split( '/\r\n|\r|\n/', $raw );
	$lines = array_values( array_filter( $lines, function ( $l ) { return trim( $l ) !== ''; } ) );
	if ( count( $lines ) < 2 ) return new WP_Error( 'grenvios_kw', 'El archivo no tiene filas de datos.' );

	// Keyword Planner antepone 2-3 líneas de cabecera antes de la tabla real.
	$sep = "\t";
	$head = 0;
	foreach ( $lines as $i => $l ) {
		foreach ( array( "\t", ';', ',' ) as $s ) {
			$cols = array_map( 'trim', explode( $s, mb_strtolower( $l ) ) );
			if ( count( $cols ) >= 2 && ( in_array( 'keyword', $cols, true ) || in_array( 'palabra clave', $cols, true ) ) ) {
				$sep = $s; $head = $i; break 2;
			}
		}
		if ( $i > 5 ) break;
	}

	$cols = array_map( function ( $c ) { return mb_strtolower( trim( $c, " \"'" ) ); }, explode( $sep, $lines[ $head ] ) );
	$ikw = $ivol = $icomp = -1;
	foreach ( $cols as $i => $c ) {
		if ( $ikw < 0 && ( $c === 'keyword' || $c === 'palabra clave' || strpos( $c, 'keyword' ) === 0 ) ) $ikw = $i;
		if ( $ivol < 0 && ( strpos( $c, 'volum' ) !== false || strpos( $c, 'searches' ) !== false || strpos( $c, 'búsquedas' ) !== false ) ) $ivol = $i;
		if ( $icomp < 0 && ( strpos( $c, 'compet' ) !== false ) ) $icomp = $i;
	}
	if ( $ikw < 0 ) $ikw = 0;

	$out = array();
	foreach ( array_slice( $lines, $head + 1 ) as $l ) {
		$p  = explode( $sep, $l );
		$kw = isset( $p[ $ikw ] ) ? trim( $p[ $ikw ], " \"'" ) : '';
		if ( $kw === '' ) continue;
		$vol = ( $ivol >= 0 && isset( $p[ $ivol ] ) ) ? (int) preg_replace( '/\D/', '', $p[ $ivol ] ) : 0;
		$cmp = ( $icomp >= 0 && isset( $p[ $icomp ] ) ) ? trim( $p[ $icomp ], " \"'" ) : '';
		$out[] = array( 'kw' => $kw, 'vol' => $vol, 'comp' => $cmp );
	}
	return $out;
}

/* Procesa las filas: limpia, agrupa y ordena. */
function grenvios_kw_process( $rows ) {
	$out = array();
	foreach ( $rows as $r ) {
		$reason = grenvios_kw_discard_reason( $r['kw'] );
		$r['descarte'] = $reason;
		$r['slug']     = $reason ? '' : grenvios_kw_suggest_slug( $r['kw'] );
		$out[] = $r;
	}
	usort( $out, function ( $a, $b ) {
		if ( $a['vol'] === $b['vol'] ) return strcmp( $a['kw'], $b['kw'] );
		return $b['vol'] - $a['vol'];
	} );
	return $out;
}

/* ══════════════════════════════════════
   KEYWORDS SECUNDARIAS POR PÁGINA
══════════════════════════════════════ */
function grenvios_kw_secundarias( $post_id ) {
	$v = get_post_meta( (int) $post_id, GRENVIOS_KW2_META, true );
	return is_array( $v ) ? $v : array();
}

/* ══════════════════════════════════════
   PANTALLA
══════════════════════════════════════ */
add_action( 'admin_menu', function () {
	add_submenu_page(
		'grenvios-seo', 'Importar keywords', 'Importar keywords', 'manage_options',
		'grenvios-kw-import', 'grenvios_kw_import_page'
	);
}, 20 );

/* Guardar filtros */
add_action( 'admin_post_grenvios_kw_filters', function () {
	if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Sin permiso.' );
	check_admin_referer( 'grenvios_kw_filters' );
	update_option( 'grenvios_kw_filters', array(
		'marcas'   => sanitize_textarea_field( wp_unslash( isset( $_POST['marcas'] ) ? $_POST['marcas'] : '' ) ),
		'mercados' => sanitize_textarea_field( wp_unslash( isset( $_POST['mercados'] ) ? $_POST['mercados'] : '' ) ),
		'minvol'   => isset( $_POST['minvol'] ) ? (int) $_POST['minvol'] : 0,
	), false );
	// Reprocesa la importación guardada con los filtros nuevos.
	$data = get_option( 'grenvios_kw_import', array() );
	if ( ! empty( $data['rows'] ) ) {
		$data['rows'] = grenvios_kw_process( $data['rows'] );
		update_option( 'grenvios_kw_import', $data, false );
	}
	wp_safe_redirect( admin_url( 'admin.php?page=grenvios-kw-import&gr_msg=filtros' ) );
	exit;
} );

/* Subir CSV */
add_action( 'admin_post_grenvios_kw_upload', function () {
	if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Sin permiso.' );
	check_admin_referer( 'grenvios_kw_upload' );

	if ( empty( $_FILES['csv']['tmp_name'] ) ) {
		wp_safe_redirect( admin_url( 'admin.php?page=grenvios-kw-import&gr_msg=nofile' ) );
		exit;
	}
	$rows = grenvios_kw_parse_csv( $_FILES['csv']['tmp_name'] );
	if ( is_wp_error( $rows ) ) {
		wp_safe_redirect( admin_url( 'admin.php?page=grenvios-kw-import&gr_msg=error' ) );
		exit;
	}
	update_option( 'grenvios_kw_import', array(
		'file' => sanitize_file_name( $_FILES['csv']['name'] ),
		'when' => current_time( 'mysql' ),
		'rows' => grenvios_kw_process( $rows ),
	), false );

	wp_safe_redirect( admin_url( 'admin.php?page=grenvios-kw-import&gr_msg=ok' ) );
	exit;
} );

/* Aplicar: escribe keyword objetivo + secundarias en cada página */
add_action( 'admin_post_grenvios_kw_apply', function () {
	if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Sin permiso.' );
	check_admin_referer( 'grenvios_kw_apply' );

	$data = get_option( 'grenvios_kw_import', array() );
	if ( empty( $data['rows'] ) ) {
		wp_safe_redirect( admin_url( 'admin.php?page=grenvios-kw-import' ) );
		exit;
	}
	$por_pagina = array();
	foreach ( $data['rows'] as $r ) {
		if ( $r['descarte'] || ! $r['slug'] ) continue;
		$por_pagina[ $r['slug'] ][] = $r;
	}

	$n = 0;
	foreach ( $por_pagina as $slug => $rows ) {
		$page = grenvios_kw_page_by_slug( $slug );
		if ( ! $page ) continue;

		// Principal = la de mayor volumen; el resto, secundarias.
		$principal = $rows[0]['kw'];
		$sec       = array();
		foreach ( array_slice( $rows, 1, 30 ) as $r ) $sec[] = array( 'kw' => $r['kw'], 'vol' => $r['vol'], 'comp' => $r['comp'] );

		// La keyword objetivo NO se pisa si ya se definió a mano: una decisión
		// humana vale más que el mayor volumen del export.
		if ( ! grenvios_seo_kw_is_set( $page->ID ) ) update_post_meta( $page->ID, GRENVIOS_KW_META, $principal );
		update_post_meta( $page->ID, GRENVIOS_KW2_META, $sec );
		$n++;
	}
	delete_transient( 'grenvios_link_map' );
	wp_safe_redirect( admin_url( 'admin.php?page=grenvios-kw-import&gr_msg=aplicado&n=' . $n ) );
	exit;
} );

/* Busca la página de un slug, esté donde esté en la jerarquía. */
function grenvios_kw_page_by_slug( $slug ) {
	foreach ( array( '', 'servicios/', 'destinos/' ) as $pref ) {
		$p = get_page_by_path( $pref . $slug );
		if ( $p && $p->post_status === 'publish' ) return $p;
	}
	return null;
}

function grenvios_kw_import_page() {
	if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Sin permiso.' );
	$data = get_option( 'grenvios_kw_import', array() );
	$rows = isset( $data['rows'] ) ? $data['rows'] : array();
	$f    = grenvios_kw_filters();
	$msg  = isset( $_GET['gr_msg'] ) ? sanitize_key( $_GET['gr_msg'] ) : '';

	$usables = array_filter( $rows, function ( $r ) { return ! $r['descarte']; } );
	$vol_ok  = array_sum( array_map( function ( $r ) { return $r['vol']; }, $usables ) );
	$vol_all = array_sum( array_map( function ( $r ) { return $r['vol']; }, $rows ) );
	?>
	<div class="wrap">
		<h1>Importar investigación de keywords</h1>

		<?php if ( $msg === 'ok' ) : ?><div class="notice notice-success"><p>Archivo procesado.</p></div><?php endif; ?>
		<?php if ( $msg === 'filtros' ) : ?><div class="notice notice-success"><p>Filtros guardados y datos reprocesados.</p></div><?php endif; ?>
		<?php if ( $msg === 'aplicado' ) : ?><div class="notice notice-success"><p>Keywords asignadas a <?php echo (int) $_GET['n']; ?> páginas.</p></div><?php endif; ?>
		<?php if ( $msg === 'error' ) : ?><div class="notice notice-error"><p>No se pudo leer el archivo. Debe ser el CSV de Keyword Planner (o cualquier CSV con una columna «Keyword»).</p></div><?php endif; ?>
		<?php if ( $msg === 'nofile' ) : ?><div class="notice notice-warning"><p>No se seleccionó ningún archivo.</p></div><?php endif; ?>

		<div class="notice notice-info inline" style="margin:14px 0;padding:10px 14px">
			<p style="margin:0 0 6px"><strong>Antes de exportar en Keyword Planner, comprueba esto</strong> — si no, los datos no sirven:</p>
			<ol style="margin:0 0 0 20px;list-style:decimal">
				<li><strong>Ubicación: Perú</strong> (no «todos los países»). Es el error que más se comete: mezcla la demanda de España, Chile, Argentina y Colombia, y los volúmenes altos resultan ser de otro país.</li>
				<li><strong>Idioma: español.</strong></li>
				<li>Exporta un archivo <strong>por cluster</strong> (documentos, paquetes, carga, apostilla, destinos): una sola semilla solo devuelve keywords de su propio tema.</li>
				<li>Sin campaña activa, los volúmenes salen en rangos (50 / 500 / 5.000): sirven para <em>ordenar</em>, no como cifras exactas.</li>
			</ol>
		</div>

		<h2>1 · Subir archivo</h2>
		<form method="post" enctype="multipart/form-data" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="grenvios_kw_upload">
			<?php wp_nonce_field( 'grenvios_kw_upload' ); ?>
			<input type="file" name="csv" accept=".csv,text/csv" required>
			<?php submit_button( 'Procesar', 'primary', 'submit', false ); ?>
			<?php if ( ! empty( $data['file'] ) ) : ?>
				<span class="description" style="margin-left:10px">Último: <code><?php echo esc_html( $data['file'] ); ?></code> (<?php echo esc_html( $data['when'] ); ?>)</span>
			<?php endif; ?>
		</form>

		<?php if ( $rows ) : ?>
			<h2>2 · Resultado</h2>
			<p>
				<strong><?php echo count( $rows ); ?></strong> keywords ·
				usables <strong><?php echo count( $usables ); ?></strong> (<?php echo esc_html( number_format_i18n( $vol_ok ) ); ?> de volumen) ·
				descartadas <strong><?php echo count( $rows ) - count( $usables ); ?></strong>
				(<?php echo esc_html( number_format_i18n( $vol_all - $vol_ok ) ); ?> de volumen).
			</p>
			<?php if ( $vol_all && ( $vol_all - $vol_ok ) / $vol_all > 0.4 ) : ?>
				<div class="notice notice-warning inline" style="margin:8px 0"><p>
					Más del 40 % del volumen es descarte. Suele significar que la exportación
					<strong>no se restringió a Perú</strong> o que la semilla trajo demasiadas marcas de la competencia.
					Revisa la exportación antes de decidir la estructura del sitio con estos datos.
				</p></div>
			<?php endif; ?>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin:14px 0">
				<input type="hidden" name="action" value="grenvios_kw_apply">
				<?php wp_nonce_field( 'grenvios_kw_apply' ); ?>
				<button class="button button-primary" onclick="return confirm('Se escribirá la keyword objetivo (solo en las páginas que aún no la tengan definida a mano) y las secundarias. ¿Continuar?')">
					Asignar keywords a las páginas
				</button>
				<span class="description">No pisa las keywords que ya definiste a mano.</span>
			</form>

			<h3>Agrupadas por página</h3>
			<?php
			$por = array();
			foreach ( $usables as $r ) {
				$k = $r['slug'] ? $r['slug'] : '(sin página)';
				$por[ $k ][] = $r;
			}
			uasort( $por, function ( $a, $b ) { return count( $b ) - count( $a ); } );
			?>
			<table class="widefat striped" style="max-width:1100px">
				<thead><tr><th style="width:26%">Página propuesta</th><th style="width:8%">Kw</th><th style="width:10%">Volumen</th><th>Principal y secundarias</th></tr></thead>
				<tbody>
				<?php foreach ( $por as $slug => $rs ) :
					$page = ( $slug !== '(sin página)' ) ? grenvios_kw_page_by_slug( $slug ) : null;
					$vol  = array_sum( array_map( function ( $r ) { return $r['vol']; }, $rs ) ); ?>
					<tr>
						<td>
							<?php if ( $page ) : ?>
								<strong><?php echo esc_html( $page->post_title ); ?></strong><br><code><?php echo esc_html( $slug ); ?></code>
							<?php else : ?>
								<em><?php echo esc_html( $slug ); ?></em>
								<?php if ( $slug !== '(sin página)' ) : ?><br><span style="color:#b32d2e">la página no existe</span><?php endif; ?>
							<?php endif; ?>
						</td>
						<td><?php echo count( $rs ); ?></td>
						<td><?php echo esc_html( number_format_i18n( $vol ) ); ?></td>
						<td>
							<strong><?php echo esc_html( $rs[0]['kw'] ); ?></strong>
							<span class="description">(<?php echo (int) $rs[0]['vol']; ?> · <?php echo esc_html( $rs[0]['comp'] ); ?>)</span><br>
							<span style="opacity:.8;font-size:12px"><?php
								$sec = array_map( function ( $r ) { return $r['kw']; }, array_slice( $rs, 1, 12 ) );
								echo esc_html( implode( ' · ', $sec ) );
								if ( count( $rs ) > 13 ) echo ' … +' . ( count( $rs ) - 13 );
							?></span>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<h3 style="margin-top:22px">Descartadas <span class="description">(no se asignan a ninguna página)</span></h3>
			<table class="widefat striped" style="max-width:820px">
				<thead><tr><th style="width:45%">Keyword</th><th style="width:15%">Volumen</th><th>Motivo</th></tr></thead>
				<tbody>
				<?php $d = array_filter( $rows, function ( $r ) { return (bool) $r['descarte']; } );
				foreach ( array_slice( $d, 0, 60 ) as $r ) : ?>
					<tr><td><?php echo esc_html( $r['kw'] ); ?></td><td><?php echo esc_html( number_format_i18n( $r['vol'] ) ); ?></td><td><?php echo esc_html( $r['descarte'] ); ?></td></tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<?php if ( count( $d ) > 60 ) : ?><p class="description">Se muestran 60 de <?php echo count( $d ); ?>.</p><?php endif; ?>
		<?php endif; ?>

		<h2 style="margin-top:26px">3 · Filtros de limpieza</h2>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="grenvios_kw_filters">
			<?php wp_nonce_field( 'grenvios_kw_filters' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="marcas">Marcas de la competencia</label></th>
					<td><textarea name="marcas" id="marcas" rows="5" class="large-text code"><?php echo esc_textarea( $f['marcas'] ); ?></textarea>
						<p class="description">Una por línea. No se puede posicionar por la marca de otro, y ese tráfico no convierte.</p></td>
				</tr>
				<tr>
					<th scope="row"><label for="mercados">Términos de otros mercados</label></th>
					<td><textarea name="mercados" id="mercados" rows="5" class="large-text code"><?php echo esc_textarea( $f['mercados'] ); ?></textarea>
						<p class="description">Una por línea. Ejemplos reales: <code>carta documento</code> es una figura legal argentina; <code>carta certificada</code> y <code>correos</code> son de España; <code>chilexpress</code> y <code>starken</code>, de Chile.</p></td>
				</tr>
			</table>
			<?php submit_button( 'Guardar filtros y reprocesar' ); ?>
		</form>
	</div>
	<?php
}

/* ══════════════════════════════════════
   LAS SECUNDARIAS SE VEN AL EDITAR LA PÁGINA
══════════════════════════════════════ */
add_action( 'add_meta_boxes', function () {
	add_meta_box( 'grenvios-kw2', 'Keywords secundarias (de la investigación)', function ( $post ) {
		$sec = grenvios_kw_secundarias( $post->ID );
		if ( ! $sec ) {
			echo '<p class="description">Sin datos todavía. Súbelos en <strong>SEO por página → Importar keywords</strong>.</p>';
			return;
		}
		echo '<p class="description">Van en los H2, en el cuerpo y en las FAQ de esta página. <strong>No</strong> se repiten como keyword objetivo de otra página.</p>';
		echo '<table class="widefat striped"><thead><tr><th>Keyword</th><th style="width:90px">Volumen</th><th style="width:110px">Competencia</th></tr></thead><tbody>';
		foreach ( $sec as $s ) {
			echo '<tr><td>' . esc_html( $s['kw'] ) . '</td><td>' . esc_html( number_format_i18n( $s['vol'] ) ) . '</td><td>' . esc_html( $s['comp'] ) . '</td></tr>';
		}
		echo '</tbody></table>';
	}, 'page', 'normal', 'default' );
}, 20 );
