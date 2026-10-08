<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Portada principal: más servicios y un ejemplo de precio con números
 * ══════════════════════════════════════════════════════════════════════════
 *
 * 1) MÁS SERVICIOS. El carrusel de la portada solo nombra documentos,
 *    paquetes y carga. Equipaje, compras, alimentos, apostilla, empresas,
 *    recojo, provincias y seguro tienen página propia y la portada no los
 *    enlazaba: ni el visitante los descubría ni esas páginas recibían
 *    autoridad desde la home. Se añade una retícula con ancla descriptiva.
 *
 * 2) EJEMPLO DE PRECIO. «El precio no sale de la balanza» explicaba la
 *    fórmula del peso volumétrico sin un solo número. Ahora la acompaña una
 *    caja real, medida y pesada, antes y después de ajustar el embalaje. Se
 *    calcula con el mismo divisor que la calculadora del sitio
 *    (grenvios_peso_divisor), así que si cambia, cambia aquí también.
 *
 * Solo en la ruta principal: en las de país la portada habla de un destino.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function grenvios_hm_activa() {
	if ( is_admin() ) return false;
	/* También en la portada de cada país: mismo diseño que la de Perú. */
	return function_exists( 'grenvios_current_slug' ) && grenvios_current_slug() === 'home';
}

function grenvios_hm_servicios() {
	return array(
		array( 'fa-suitcase-rolling', 'Envío de equipaje',          '/servicios/envio-de-equipaje/',       'Manda tus maletas por delante y evita el exceso de equipaje del aeropuerto.' ),
		array( 'fa-bag-shopping',     'Envío de compras',           '/servicios/envio-de-compras/',        'Juntamos tus pedidos de varias tiendas en un solo bulto y pagas un flete.' ),
		array( 'fa-jar',              'Envío de alimentos',         '/servicios/envio-de-alimentos/',      'Productos peruanos envasados, según lo que admite la aduana de cada país.' ),
		array( 'fa-stamp',            'Apostilla y traducción',     '/servicios/apostilla-y-traduccion/',  'Legalizamos títulos, partidas y poderes antes de enviarlos al extranjero.' ),
		array( 'fa-building',         'Envíos para empresas',       '/envios-para-empresas/',              'Tarifas por volumen, recojos programados y documentación de exportación.' ),
		array( 'fa-house',            'Recojo a domicilio en Lima', '/recojo-a-domicilio-lima/',           'Pasamos por tu paquete, lo pesamos y lo despachamos. No cambia el plazo.' ),
		array( 'fa-map-location-dot', 'Envíos desde provincias',    '/envios-desde-provincias/',           'Tu paquete llega a nuestra oficina de Lima y desde ahí sale al extranjero.' ),
		array( 'fa-shield-check',     'Seguro de envíos',           '/seguro-de-envios/',                  'Cobertura calculada sobre el valor declarado del contenido.' ),
	);
}

function grenvios_hm_servicios_html() {
	/* Diseño de la maqueta del cliente (2026-10-01, skill grenvios-landing):
	 * tarjeta grande con foto a la izquierda (el servicio más consultado), tres
	 * tarjetas pequeñas, una franja vino con foto (empresas) y tres más. Los
	 * textos de cada servicio los hace editables inc/destinos-textos-editables.php;
	 * fotos, distintivo y botones tienen campo propio en el panel. */
	$base  = function_exists( 'grenvios_url_base' ) ? grenvios_url_base() : home_url();
	$f     = function ( $k, $d ) { return function_exists( 'grenvios_field' ) ? grenvios_field( $k, $d ) : $d; };
	$ej    = function ( $n ) { return function_exists( 'grenvios_ej_img' ) ? grenvios_ej_img( $n ) : ''; };
	/* Foto: la del campo; si no, la imagen destacada del servicio enlazado; si no, una de ejemplo. */
	$dest  = function ( $ruta ) { return function_exists( 'grenvios_imagen_de_ruta' ) ? grenvios_imagen_de_ruta( $ruta ) : ''; };
	$svs   = grenvios_hm_servicios();
	$img1  = trim( (string) $f( 'home_mas_img1', '' ) ); if ( $img1 === '' ) $img1 = $dest( $svs[0][2] ); if ( $img1 === '' ) $img1 = $ej( 'equipaje' );
	$img5  = trim( (string) $f( 'home_mas_img5', '' ) ); if ( $img5 === '' ) $img5 = $dest( $svs[4][2] ); if ( $img5 === '' ) $img5 = $ej( 'almacen-pasillo' );
	$badge = $f( 'home_mas_badge', 'Más consultado' );
	$btn1  = $f( 'home_mas_btn1', 'Ver servicio' );
	$btn5  = $f( 'home_mas_btn5', 'Soluciones para empresas' );

	$items = '';
	foreach ( grenvios_hm_servicios() as $i => $s ) {
		$n   = sprintf( '%02d', $i + 1 );
		$url = esc_url( $base . $s[2] );
		$ic  = '<span class="gr-mas-ic" aria-hidden="true"><i class="fa-regular ' . esc_attr( $s[0] ) . '"></i></span>';
		$tx  = '<span class="gr-mas-tx"><strong>' . esc_html( $s[1] ) . '</strong><span>' . esc_html( $s[3] ) . '</span></span>';
		$num = '<span class="gr-mas-n" aria-hidden="true">' . $n . '</span>';
		if ( $i === 0 ) {
			$items .= '<li class="gr-mas-li gr-mas-li--grande wow fade-in-left" data-wow-delay="100ms"><a class="gr-mas-item gr-mas-item--grande" href="' . $url . '">'
				. ( $img1 !== '' ? '<span class="gr-mas-foto"><img src="' . esc_url( $img1 ) . '" alt="" decoding="async">'
					. '<span class="gr-mas-badge"><i class="fa-regular fa-chart-simple" aria-hidden="true"></i> ' . esc_html( $badge ) . '</span></span>' : '' )
				. '<span class="gr-mas-cuerpo"><span class="gr-mas-top">' . $ic . $num . '</span>' . $tx
				. '<span class="default-btn gr-mas-btn">' . esc_html( $btn1 ) . ' <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span></span>'
				. '</a></li>';
		} elseif ( $i === 4 ) {
			$items .= '<li class="gr-mas-li gr-mas-li--ancha wow fade-in-bottom" data-wow-delay="150ms"><a class="gr-mas-item gr-mas-item--ancha" href="' . $url . '"'
				. ( $img5 !== '' ? ' style="--gr-mas-foto:url(' . esc_url( $img5 ) . ')"' : '' ) . '>'
				. '<span class="gr-mas-cuerpo"><span class="gr-mas-top">' . $ic . $num . '</span>' . $tx
				. '<span class="gr-mas-btn gr-mas-btn--linea">' . esc_html( $btn5 ) . ' <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span></span>'
				. '</a></li>';
		} else {
			$items .= '<li class="gr-mas-li wow fade-in-bottom" data-wow-delay="' . ( 100 + ( $i % 4 ) * 90 ) . 'ms"><a class="gr-mas-item" href="' . $url . '">'
				. '<span class="gr-mas-top">' . $ic . $num . '<i class="fa-regular fa-arrow-right gr-mas-go" aria-hidden="true"></i></span>' . $tx
				. '</a></li>';
		}
	}
	return '<section class="gr-mas gr-mas--v2 padding-bottom"><div class="container">'
		. '<div class="gr-mas-head">'
		. '<div class="section-heading"><p class="sub-heading">Más servicios</p>'
		. '<h2>Otros envíos que resolvemos <span class="hl">desde Lima</span></h2></div>'
		. '<p class="gr-mas-intro">Además de documentos, paquetes y carga, estos son los casos que más nos traen al mostrador. Cada uno tiene su página con precios, plazos y lo que conviene preparar.</p>'
		. '</div>'
		. '<ul class="gr-mas-grid gr-mas-grid--v2">' . $items . '</ul>'
		. '</div></section>';
}

/* Panel: fotos, distintivo y botones del bloque (los textos de los servicios
 * ya salen solos por el mecanismo automático). */
add_filter( 'grenvios_text_registry', function ( $reg ) {
	if ( ! isset( $reg['home'] ) ) return $reg;
	if ( ! is_admin() && function_exists( 'grenvios_hq_pais' ) && grenvios_hq_pais() !== '' ) return $reg;
	$reg['home']['sections']['home_mas'] = array(
		'label'           => 'Inicio · Otros envíos (fotos y botones)',
		'_no_token_check' => true,
		'sel'             => '.gr-mas',
		'fields'          => array(
			'home_mas_img1'  => array( 'Tarjeta grande · Foto (vacía = maletas de ejemplo)', 'image', '' ),
			'home_mas_badge' => array( 'Tarjeta grande · Distintivo', 'text', 'Más consultado' ),
			'home_mas_btn1'  => array( 'Tarjeta grande · Botón', 'text', 'Ver servicio' ),
			'home_mas_img5'  => array( 'Franja de empresas · Foto (vacía = almacén de ejemplo)', 'image', '' ),
			'home_mas_btn5'  => array( 'Franja de empresas · Botón', 'text', 'Soluciones para empresas' ),
		),
	);
	return $reg;
}, 45 );

/* Ejemplo con números. Cajas elegidas para que el ajuste cambie qué peso se
 * cobra: sin ajustar manda el volumétrico; ajustada, manda la balanza. */
function grenvios_hm_ejemplo_html() {
	$div = function_exists( 'grenvios_peso_divisor' ) ? (int) grenvios_peso_divisor() : 5000;
	if ( $div <= 0 ) $div = 5000;
	$real = 8;
	$cajas = array(
		array( 'Caja sin ajustar', 50, 40, 30 ),
		array( 'Caja ajustada',    40, 35, 25 ),
	);
	$fmt = function ( $n ) { return rtrim( rtrim( number_format( $n, 1, ',', '.' ), '0' ), ',' ); };

	/* Caja isométrica con sus medidas, dibujada a escala relativa. */
	$caja_svg = function ( $l, $a, $h ) {
		$k  = 180 / max( $l, $a, $h );              // escala: el lado mayor mide 180
		$L  = $l * $k; $A = $a * $k * .55; $H = $h * $k;
		$ox = 84; $oy = 30 + $A;                   // esquina frontal izquierda superior
		$dx = $A * .9;                             // desplazamiento en profundidad
		$W  = 430; $Hh = 30 + $A + $H + 54;
		$p  = function ( $x, $y ) { return round( $x, 1 ) . ',' . round( $y, 1 ); };
		$x0 = $ox; $x1 = $ox + $L; $yt = $oy; $yb = $oy + $H;
		$cara_f = $p( $x0, $yt ) . ' ' . $p( $x1, $yt ) . ' ' . $p( $x1, $yb ) . ' ' . $p( $x0, $yb );
		$cara_t = $p( $x0, $yt ) . ' ' . $p( $x0 + $dx, $yt - $A ) . ' ' . $p( $x1 + $dx, $yt - $A ) . ' ' . $p( $x1, $yt );
		$cara_l = $p( $x1, $yt ) . ' ' . $p( $x1 + $dx, $yt - $A ) . ' ' . $p( $x1 + $dx, $yb - $A ) . ' ' . $p( $x1, $yb );
		$cx = ( $x0 + $x1 ) / 2;
		$q = '"';
		return '<svg class="gr-ej-caja" viewBox="0 0 ' . $W . ' ' . round( $Hh ) . '" role="img" aria-label="Caja de ' . $l . ' por ' . $a . ' por ' . $h . ' centímetros">'
			. '<polygon points=' . $q . $cara_t . $q . ' fill="#e3c39f"/>'
			. '<polygon points=' . $q . $cara_f . $q . ' fill="#d2a978"/>'
			. '<polygon points=' . $q . $cara_l . $q . ' fill="#b48656"/>'
			. '<line x1=' . $q . $cx . $q . ' y1=' . $q . $yt . $q . ' x2=' . $q . $cx . $q . ' y2=' . $q . $yb . $q . ' stroke="#f1e4d6" stroke-width="6" opacity=".9"/>'
			. '<line x1=' . $q . $cx . $q . ' y1=' . $q . $yt . $q . ' x2=' . $q . round( $cx + $dx, 1 ) . $q . ' y2=' . $q . round( $yt - $A, 1 ) . $q . ' stroke="#f1e4d6" stroke-width="6" opacity=".9"/>'
			. '<g fill="none" stroke="#5e2129" stroke-width="1.5" stroke-dasharray="3 4">'
			. '<line x1=' . $q . $x0 . $q . ' y1=' . $q . ( $yb + 14 ) . $q . ' x2=' . $q . $x1 . $q . ' y2=' . $q . ( $yb + 14 ) . $q . '/>'
			. '<line x1=' . $q . ( $x0 - 14 ) . $q . ' y1=' . $q . $yt . $q . ' x2=' . $q . ( $x0 - 14 ) . $q . ' y2=' . $q . $yb . $q . '/>'
			. '<line x1=' . $q . ( $x1 + 10 ) . $q . ' y1=' . $q . ( $yb + 10 ) . $q . ' x2=' . $q . round( $x1 + $dx + 10, 1 ) . $q . ' y2=' . $q . round( $yb - $A + 10, 1 ) . $q . '/>'
			. '</g>'
			. '<g fill="#5e2129" font-family="Poppins, sans-serif" font-size="15" font-weight="600">'
			. '<text x=' . $q . $cx . $q . ' y=' . $q . ( $yb + 36 ) . $q . ' text-anchor="middle">' . $l . ' cm</text>'
			. '<text x=' . $q . ( $x0 - 22 ) . $q . ' y=' . $q . round( ( $yt + $yb ) / 2 + 5, 1 ) . $q . ' text-anchor="end">' . $h . ' cm</text>'
			. '<text x=' . $q . round( $x1 + $dx / 2 + 28, 1 ) . $q . ' y=' . $q . round( $yb - $A / 2 + 26, 1 ) . $q . '>' . $a . ' cm</text>'
			. '</g></svg>';
	};

	$cols = array(); $cobrado = array();
	foreach ( $cajas as $c ) {
		$vol  = $c[1] * $c[2] * $c[3] / $div;
		$cob  = max( $vol, $real );
		$cobrado[] = $cob;
		$manda = $vol > $real ? 'manda el volumétrico' : 'manda la balanza';
		$cols[] = '<div class="gr-ej-col">'
			. '<p class="gr-ej-name">' . esc_html( $c[0] ) . '</p>'
			. '<p class="gr-ej-calc">' . $c[1] . ' × ' . $c[2] . ' × ' . $c[3] . ' cm ÷ ' . number_format( $div, 0, ',', '.' ) . ' = <strong>' . $fmt( $vol ) . ' kg</strong></p>'
			. '<div class="gr-ej-cuerpo">' . $caja_svg( $c[1], $c[2], $c[3] )
			. '<p class="gr-ej-cob"><span>Se cobran</span><strong>' . $fmt( $cob ) . ' kg</strong><em>' . esc_html( $manda ) . '</em></p></div>'
			. '</div>';
	}
	$ahorro = $cobrado[0] - $cobrado[1];
	$medio  = $ahorro > 0
		? '<div class="gr-ej-ahorro"><p><span>Ahorras</span><strong>' . $fmt( $ahorro ) . ' kg</strong><em>de peso cobrado</em></p><i class="fa-regular fa-arrow-right-long" aria-hidden="true"></i></div>'
		: '';

	return '<figure class="gr-ej gr-ej--v2 wow fade-in-bottom" data-wow-delay="100ms" aria-label="Ejemplo de cálculo del peso cobrado">'
		. '<figcaption class="gr-ej-head"><span>Ejemplo real</span><span>Contenido: ' . $real . ' kg en balanza</span></figcaption>'
		. '<div class="gr-ej-grid">' . $cols[0] . $medio . $cols[1] . '</div>'
		. ( $ahorro > 0 ? '<p class="gr-ej-foot">Ajustar la caja al contenido baja <strong>' . $fmt( $ahorro ) . ' kg</strong> de peso cobrado, sin sacar nada de dentro.</p>' : '' )
		. '</figure>';
}

/* Se inserta en la pasada final (no en `grenvios_content_html`): el HTML de
 * la portada llega a ese filtro por más de un camino y aquí se ve completo. */
add_filter( 'grenvios_html_final', function ( $html ) {
	if ( ! grenvios_hm_activa() || ! is_string( $html ) ) return $html;
	$marca = '<!--/.service-section-->';
	if ( strpos( $html, $marca ) === false || strpos( $html, 'class="gr-mas ' ) !== false ) return $html;
	return str_replace( $marca, $marca . grenvios_hm_servicios_html(), $html );
}, 30 );

/* El ejemplo va dentro de la sección de precio (inc/home-seo.php), debajo del
 * texto y antes de los botones. */
add_filter( 'grenvios_html_final', function ( $html ) {
	if ( ! grenvios_hm_activa() || ! is_string( $html ) || strpos( $html, 'gr-home-precio' ) === false ) return $html;
	if ( strpos( $html, 'class="gr-ej"' ) !== false ) return $html;
	$i = strpos( $html, 'gr-home-precio' );
	$j = strpos( $html, '<div class="btn-group">', $i );
	if ( $j === false ) return $html;
	return substr( $html, 0, $j ) . grenvios_hm_ejemplo_html() . substr( $html, $j );
}, 30 );
