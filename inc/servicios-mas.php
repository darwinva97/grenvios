<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  /servicios/ · bloque «Más servicios» (revisión UX + SEO, 2026-09-28)
 * ══════════════════════════════════════════════════════════════════════════
 *
 * El hub de servicios solo mostraba cuatro tarjetas (documentos, paquetes,
 * carga, apostilla). Los otros diez servicios del sitio —encomiendas,
 * medicinas, equipaje, compras, alimentos, correspondencia, muestras,
 * celulares, regalos y artesanías— no tenían ningún enlace desde aquí: quien
 * buscaba «enviar medicinas» en el menú Servicios no lo encontraba, y la
 * página padre no pasaba autoridad a sus hijas.
 *
 * Tarjetas compactas en dos columnas (icono · título · una línea), justo
 * debajo de las cuatro principales. Solo en la ruta principal: estas páginas
 * no existen en las rutas de país. Textos editables en el panel.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Servicio => [ icono, título por defecto, línea por defecto ]. Ruta = /servicios/<slug>/. */
function grenvios_sm_items() {
	/* Filtro `grenvios_sm_items`: las páginas de primer nivel (inc/paginas-primer-nivel.php) se suman aquí. */
	return apply_filters( 'grenvios_sm_items', array(
		'encomiendas-internacionales'            => array( 'fa-solid fa-gift',          'Encomiendas internacionales', 'Ropa, regalos y productos peruanos para tu familia, con seguimiento hasta la entrega.' ),
		'envio-de-equipaje'                      => array( 'fa-solid fa-suitcase',      'Envío de equipaje',           'Manda tu equipaje por delante y viaja ligero: más barato que el exceso en el aeropuerto.' ),
		'envio-de-compras'                       => array( 'fa-solid fa-bag-shopping',  'Envío de compras',            'Compra en varias tiendas de Perú y recíbelo todo junto en un solo envío.' ),
		'envio-de-medicinas-al-extranjero'       => array( 'fa-solid fa-prescription-bottle-medical', 'Envío de medicinas', 'Con receta, en su envase original y hacia los destinos que las admiten.' ),
		'envio-de-alimentos'                     => array( 'fa-solid fa-mug-hot',       'Envío de alimentos',          'Café, panetón y productos peruanos envasados de fábrica.' ),
		'envio-de-regalos-al-extranjero'         => array( 'fa-solid fa-cake-candles',  'Envío de regalos',            'Que llegue a la fecha: qué elegir, cómo declararlo y con cuánto margen enviarlo.' ),
		'envio-de-artesanias-al-extranjero'      => array( 'fa-solid fa-palette',       'Envío de artesanías',         'Cerámica, textiles y retablos embalados según su material, para regalar o vender.' ),
		'envio-de-celulares-y-laptops'           => array( 'fa-solid fa-laptop',        'Celulares y laptops',         'Electrónica con batería de litio: qué vía admite y cómo prepararla.' ),
		'envio-de-correspondencia-internacional' => array( 'fa-solid fa-envelope',      'Correspondencia',             'Cartas, tarjetas e invitaciones con número de guía hasta que se entregan.' ),
		'envio-de-muestras-comerciales'          => array( 'fa-solid fa-box-open',      'Muestras comerciales',        'La primera impresión de tu producto ante un cliente de otro país.' ),
		'carga-aerea-internacional'              => array( 'fa-solid fa-plane-departure', 'Carga aérea',               'La vía rápida para tu mercancía, y la única fuera de Sudamérica.' ),
		'carga-terrestre-internacional'          => array( 'fa-solid fa-truck-fast',    'Carga terrestre',             'Más volumen por menos hacia los países vecinos, con el impuesto pagado en origen.' ),
		'envio-express-internacional'            => array( 'fa-solid fa-stopwatch',     'Envío express',               'Destinos ordenados por plazo y lo que de verdad acorta un envío urgente.' ),
		'traduccion-oficial-de-documentos'       => array( 'fa-solid fa-language',      'Traducción oficial',          'Qué traducción te piden, en qué orden va con la apostilla y cómo enviarla.' ),
	) );
}

function grenvios_sm_campos() {
	$f = array(
		'sm_eyebrow' => array( 'Antetítulo', 'text', 'Para cada tipo de envío' ),
		'sm_title'   => array( 'Título', 'html', 'Más servicios según <span class="hl">lo que envías</span>' ),
		'sm_text'    => array( 'Texto', 'textarea', 'Algunos envíos tienen reglas propias de embalaje, documentación o aduana. Elige el tuyo y te contamos lo que necesitas saber antes de cotizar.' ),
	);
	$n = 0;
	foreach ( grenvios_sm_items() as $slug => $it ) {
		$n++;
		$f[ 'sm_' . $n . '_t' ] = array( 'Servicio ' . $n . ' · título', 'text', $it[1] );
		$f[ 'sm_' . $n . '_x' ] = array( 'Servicio ' . $n . ' · texto', 'textarea', $it[2] );
	}
	return $f;
}

function grenvios_sm_html() {
	$c    = grenvios_sm_campos();
	$v    = function ( $k ) use ( $c ) { return grenvios_field( $k, $c[ $k ][2] ); };
	$base = function_exists( 'grenvios_url_base' ) ? grenvios_url_base() : home_url();
	$out  = '';
	$n    = 0;
	foreach ( grenvios_sm_items() as $slug => $it ) {
		$n++;
		/* Solo las que existen: bajo /servicios/ o en primer nivel. */
		$ruta = get_page_by_path( 'servicios/' . $slug ) ? 'servicios/' . $slug : ( get_page_by_path( $slug ) ? $slug : '' );
		if ( $ruta === '' ) continue;
		$url  = esc_url( $base . '/' . $ruta . '/' );
		$out .= '<div class="col-lg-6 wow fade-in-bottom" data-wow-delay="' . ( 80 + ( ( $n - 1 ) % 6 ) * 70 ) . 'ms"><a class="gr-sm-card" href="' . $url . '">'
			. '<span class="gr-sm-ic"><i class="' . esc_attr( $it[0] ) . '" aria-hidden="true"></i></span>'
			. '<span class="gr-sm-tx"><strong>' . esc_html( $v( 'sm_' . $n . '_t' ) ) . '</strong><span>' . esc_html( $v( 'sm_' . $n . '_x' ) ) . '</span></span>'
			. '<i class="fa-solid fa-arrow-right gr-sm-go" aria-hidden="true"></i></a></div>';
	}
	if ( $out === '' ) return '';
	return '<section class="gr-sm padding"><div class="container">'
		. '<div class="section-heading text-center mb-40">'
		. '<h3 class="sub-heading">' . esc_html( $v( 'sm_eyebrow' ) ) . '</h3>'
		. '<h2>' . wp_kses_post( $v( 'sm_title' ) ) . '</h2>'
		. '<p>' . esc_html( $v( 'sm_text' ) ) . '</p></div>'
		. '<div class="row g-3">' . $out . '</div></div></section>';
}

/* Justo después de las cuatro tarjetas principales. Idempotente. */
add_filter( 'grenvios_html_final', function ( $html ) {
	if ( is_admin() || ! is_string( $html ) || strpos( $html, 'class="gr-sm ' ) !== false ) return $html;
	if ( ! function_exists( 'grenvios_current_slug' ) || grenvios_current_slug() !== 'servicios' ) return $html;
	if ( function_exists( 'grenvios_hq_pais' ) && grenvios_hq_pais() !== '' ) return $html;
	$marca = '<!--/.service-section-->';
	$pos   = strpos( $html, $marca );
	if ( $pos === false ) return $html;
	$bloque = grenvios_sm_html();
	return $bloque === '' ? $html : substr( $html, 0, $pos + strlen( $marca ) ) . $bloque . substr( $html, $pos + strlen( $marca ) );
}, 26 );

/* Panel: sección propia en /servicios/. */
add_filter( 'grenvios_text_registry', function ( $reg ) {
	if ( ! isset( $reg['servicios'] ) ) return $reg;
	$sec = array( 'mas_servicios' => array( 'label' => 'Servicios · Más servicios', 'sel' => '.gr-sm', '_no_token_check' => true, 'fields' => grenvios_sm_campos() ) );
	/* Detrás del listado de tarjetas, que es donde se ve. */
	$nuevo = array();
	foreach ( $reg['servicios']['sections'] as $k => $s ) {
		$nuevo[ $k ] = $s;
		if ( $k === 'services' ) $nuevo += $sec;
	}
	$reg['servicios']['sections'] = $nuevo + $sec;
	return $reg;
}, 30 );

add_action( 'wp_head', function () {
	if ( is_admin() ) return;
	echo '<style id="grenvios-sm-css">'
		. '.gr-sm-card{display:flex;align-items:center;gap:18px;height:100%;padding:20px 22px;background:#fff;border:1px solid #efeae6;border-radius:var(--gr-r-lg,16px);box-shadow:0 10px 28px -20px rgba(0,0,0,.25);text-decoration:none;transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease}'
		. '.gr-sm-card:hover,.gr-sm-card:focus-visible{transform:translateY(-2px);border-color:var(--primary-color,#5e2129);box-shadow:0 18px 36px -22px rgba(94,33,41,.45)}'
		. '.gr-sm-ic{flex:0 0 auto;width:54px;height:54px;border-radius:var(--gr-r-md,12px);background:var(--bg-grey,#f8f5f1);color:var(--primary-color,#5e2129);display:inline-flex;align-items:center;justify-content:center;font-size:22px}'
		. '.gr-sm-tx{flex:1 1 auto;display:flex;flex-direction:column;gap:3px;min-width:0}'
		. '.gr-sm-tx strong{font-size:17px;line-height:1.3;color:var(--heading-color,#0c0c0c)}'
		. '.gr-sm-tx span{font-size:14.5px;line-height:1.5;color:var(--body-color,#666)}'
		. '.gr-sm-go{flex:0 0 auto;color:var(--primary-color,#5e2129);transition:transform .2s ease}'
		. '.gr-sm-card:hover .gr-sm-go{transform:translateX(4px)}'
		. '@media(max-width:575px){.gr-sm-card{padding:16px;gap:14px}.gr-sm-ic{width:46px;height:46px;font-size:19px}}'
		. '@media(prefers-reduced-motion:reduce){.gr-sm-card,.gr-sm-go{transition:none}}'
		. '</style>';
}, 105 );
