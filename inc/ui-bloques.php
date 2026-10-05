<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  UI de bloques: patrones visuales para las secciones pintadas por PHP
 *  (skill .claude/skills/grenvios-ui)
 * ══════════════════════════════════════════════════════════════════════════
 *
 * PROBLEMA (revisión 2026-09-28): las páginas creadas con el motor de
 * inc/paginas-servicios-extra.php —servicios nuevos, clúster, regiones— se
 * pintaban todas con el mismo patrón: título centrado, párrafo y tarjetas
 * blancas de texto. Sin imágenes, sin iconos, sin ritmo, sin animación.
 *
 * Aquí viven los patrones que las sustituyen. Todos reutilizan lo que el tema
 * ya tiene: `section-heading` + `text-anim` (GSAP anima el título al entrar),
 * `wow fade-in-*` con `data-wow-delay` (WOW.js revela al hacer scroll), la
 * rejilla de Bootstrap y los tokens de color. El CSS propio está en
 * assets/css/gr-bloques.css.
 *
 *   intro        → bloque dividido: texto a un lado, imagen enmarcada al otro
 *   panels       → tarjetas con icono, aparición escalonada
 *   lista        → rejilla de vistos en dos columnas
 *   pasos        → línea de tiempo numerada (horizontal en escritorio)
 *   definiciones → glosario en tarjetas
 *   destinos     → tabla en tarjeta, con banderas
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ── Estilos ──────────────────────────────────────────────────────────── */
add_action( 'wp_enqueue_scripts', function () {
	$f = get_template_directory() . '/assets/css/gr-bloques.css';
	if ( file_exists( $f ) ) {
		wp_enqueue_style( 'grenvios-bloques', get_template_directory_uri() . '/assets/css/gr-bloques.css', array( 'logisko-style' ), (string) filemtime( $f ) );
	}
}, 20 );

/* ── Imágenes ─────────────────────────────────────────────────────────────
 * Solo fotos reales del tema e ilustraciones de marca: nunca rellenos de la
 * plantilla (skill grenvios-diseno). */
function grenvios_ui_img( $nombre ) {
	$mapa = array(
		'avion'      => 'hero-home.jpg',
		'camion'     => 'destino-hero.jpg',
		'repartidor' => 'cta-repartidor-4.webp',
		'documentos' => 'ilustraciones/documentos.svg',
		'embalaje'   => 'ilustraciones/embalaje.svg',
		'precio'     => 'ilustraciones/precio.svg',
	);
	$f = isset( $mapa[ $nombre ] ) ? $mapa[ $nombre ] : $nombre;
	return get_template_directory_uri() . '/assets/img/' . $f;
}

/* Imagen por defecto del bloque de introducción de cada página. */
function grenvios_ui_img_pagina( $slug ) {
	$m = array(
		'carga-aerea-internacional'              => 'avion',
		'carga-terrestre-internacional'          => 'camion',
		'envio-express-internacional'            => 'avion',
		'traduccion-oficial-de-documentos'       => 'documentos',
		'envios-a-sudamerica'                    => 'camion',
		'envios-a-norteamerica'                  => 'avion',
		'envios-a-centroamerica-y-el-caribe'     => 'avion',
		'envios-a-europa'                        => 'avion',
		'envio-de-correspondencia-internacional' => 'documentos',
		'envio-de-muestras-comerciales'          => 'embalaje',
		'envio-de-celulares-y-laptops'           => 'embalaje',
		'envio-de-regalos-al-extranjero'         => 'repartidor',
		'envio-de-artesanias-al-extranjero'      => 'embalaje',
		'glosario-de-envios-internacionales'     => 'documentos',
	);
	$m = apply_filters( 'grenvios_ui_img_pagina_mapa', $m );   // inc/ui-global.php suma el resto de páginas
	return grenvios_ui_img( isset( $m[ $slug ] ) ? $m[ $slug ] : 'camion' );
}

/* ── Iconos por tema ──────────────────────────────────────────────────────
 * Las tarjetas no tienen campo de icono: se elige por el tema del título, así
 * que cambiar el texto en el panel también puede cambiar el icono. */
function grenvios_ui_icono( $texto, $respaldo = '' ) {
	/* Primero el título: el texto de la tarjeta menciona de todo («objetos con
	 * batería», «vía aérea») y daba iconos que no tenían que ver. */
	if ( $respaldo !== '' ) {
		$i = grenvios_ui_icono( $texto );
		return $i !== 'fa-solid fa-circle-check' ? $i : grenvios_ui_icono( $respaldo );
	}
	$t = remove_accents( mb_strtolower( wp_strip_all_tags( (string) $texto ) ) );
	$reglas = array(
		'admite|productos'                          => 'fa-solid fa-boxes-stacked',
		'valor|declar'                              => 'fa-solid fa-tags',
		'minimo|exento|umbral'                      => 'fa-solid fa-scale-balanced',
		'uso comercial|comercial'                   => 'fa-solid fa-store',
		'seguro|cobertura|cubre'                    => 'fa-solid fa-shield-halved',
		'recojo|recoge|domicilio'                   => 'fa-solid fa-house-circle-check',
		'volumen|peso'                              => 'fa-solid fa-weight-scale',
		'aduana|revisa|inspeccion'                  => 'fa-solid fa-building-shield',
		'iva|impuesto|arancel|precio|cobra|tarifa'  => 'fa-solid fa-receipt',
		'bateria|power bank'                        => 'fa-solid fa-battery-three-quarters',
		'avion|aerea|aereo|vuela'                   => 'fa-solid fa-plane-departure',
		'carretera|terrestre|camion|volumen'        => 'fa-solid fa-truck-fast',
		'documento|traduccion|apostilla|receta'     => 'fa-solid fa-file-signature',
		'direccion|codigo postal|ciudad'            => 'fa-solid fa-map-location-dot',
		'fecha|plazo|urgen|rapid|prisa|tiempo'      => 'fa-solid fa-stopwatch',
		'regalo|textil|ropa'                        => 'fa-solid fa-gift',
		'ceramica|fragil|piedra|madera|plata|joya'  => 'fa-solid fa-gem',
		'muestra|pedido|factura|vend'               => 'fa-solid fa-box-open',
		'correo|carta|sobre|courier'                => 'fa-solid fa-envelope-open-text',
		'nuevo|usado|equipo|celular|laptop'         => 'fa-solid fa-laptop',
		'permiso|restring|prohib|no conviene|no ac' => 'fa-solid fa-triangle-exclamation',
		'lo decide|institucion|oficial|certific'    => 'fa-solid fa-stamp',
		'simple'                                    => 'fa-solid fa-language',
		'mercancia|carga'                           => 'fa-solid fa-boxes-stacked',
	);
	foreach ( $reglas as $patron => $icono ) {
		if ( preg_match( '/(' . $patron . ')/u', $t ) ) return $icono;
	}
	return 'fa-solid fa-circle-check';
}

/* Retraso escalonado para WOW (ms). */
function grenvios_ui_delay( $i, $paso = 110, $base = 100 ) {
	return ( $base + $i * $paso ) . 'ms';
}

/* ── Cabecera de sección ──────────────────────────────────────────────────
 * `text-anim` activa la animación del título de main.js (GSAP) al entrar. */
function grenvios_ui_cabecera( $sub, $titulo, $texto = '', $centro = true ) {
	return '<div class="section-heading gr-bq-head' . ( $centro ? ' text-center' : '' ) . '">'
		. ( trim( (string) $sub ) !== '' ? '<h3 class="sub-heading">' . esc_html( $sub ) . '</h3>' : '' )
		. '<h2 class="text-anim" data-effect="fade-in-bottom" data-delay="0.1" data-duration="0.9">' . wp_kses_post( $titulo ) . '</h2>'
		. ( trim( (string) $texto ) !== '' ? '<p class="gr-bq-sub">' . wp_kses_post( $texto ) . '</p>' : '' )
		. '</div>';
}

/* ── Bloques ──────────────────────────────────────────────────────────── */
function grenvios_ui_intro( $sub, $titulo, $texto, $img, $badge = '' ) {
	$media = $img
		? '<div class="col-lg-6"><figure class="gr-bq-media wow fade-in-right" data-wow-delay="150ms">'
			. '<img src="' . esc_url( $img ) . '" alt="" loading="lazy" decoding="async">'
			. ( $badge !== '' ? '<figcaption class="gr-bq-badge"><i class="fa-solid fa-location-dot" aria-hidden="true"></i> ' . esc_html( $badge ) . '</figcaption>' : '' )
			. '</figure></div>'
		: '';
	return '<section class="gr-bq gr-bq-intro padding"><div class="container"><div class="row align-items-center gy-5">'
		. '<div class="' . ( $img ? 'col-lg-6' : 'col-lg-8 offset-lg-2 text-center' ) . '">'
		. grenvios_ui_cabecera( $sub, $titulo, '', ! $img )
		. '<div class="gr-bq-lead wow fade-in-bottom" data-wow-delay="100ms"><p>' . wp_kses_post( $texto ) . '</p></div>'
		. '</div>' . $media . '</div></div></section>';
}

function grenvios_ui_tarjetas( $items ) {
	$n   = count( $items );
	$col = $n >= 4 ? 'col-md-6 col-xl-3' : ( $n === 3 ? 'col-md-6 col-lg-4' : 'col-md-6' );
	$h   = '<div class="row g-4 gr-bq-cards">';
	foreach ( $items as $i => $it ) {
		$h .= '<div class="' . $col . '"><article class="gr-bq-card wow fade-in-bottom" data-wow-delay="' . grenvios_ui_delay( $i ) . '">'
			. '<span class="gr-bq-ic"><i class="' . esc_attr( grenvios_ui_icono( $it[0], $it[1] ) ) . '" aria-hidden="true"></i></span>'
			. '<h3>' . esc_html( $it[0] ) . '</h3><p>' . wp_kses_post( $it[1] ) . '</p></article></div>';
	}
	return $h . '</div>';
}

function grenvios_ui_checks( $items ) {
	$h = '<ul class="gr-bq-checks">';
	foreach ( $items as $i => $t ) {
		$h .= '<li class="wow fade-in-bottom" data-wow-delay="' . grenvios_ui_delay( $i, 70 ) . '"><span class="gr-bq-check-ic"><i class="fa-solid fa-check" aria-hidden="true"></i></span><span>' . esc_html( $t ) . '</span></li>';
	}
	return $h . '</ul>';
}

function grenvios_ui_pasos( $items ) {
	$h = '<ol class="gr-bq-pasos" style="--pasos:' . count( $items ) . '">';
	foreach ( $items as $i => $p ) {
		$h .= '<li class="wow fade-in-bottom" data-wow-delay="' . grenvios_ui_delay( $i, 130 ) . '">'
			. '<span class="gr-bq-paso-n">' . sprintf( '%02d', $i + 1 ) . '</span>'
			. '<h3>' . esc_html( rtrim( $p[0], '.' ) ) . '</h3><p>' . esc_html( $p[1] ) . '</p></li>';
	}
	return $h . '</ol>';
}

function grenvios_ui_glosario( $lineas ) {
	$h = '<dl class="gr-bq-glos">';
	foreach ( $lineas as $i => $linea ) {
		$par = array_map( 'trim', explode( ':', $linea, 2 ) );
		if ( ! isset( $par[1] ) ) continue;
		$h .= '<div class="gr-bq-term wow fade-in-bottom" data-wow-delay="' . grenvios_ui_delay( $i % 4, 70 ) . '" id="' . esc_attr( sanitize_title( $par[0] ) ) . '">'
			. '<dt>' . esc_html( $par[0] ) . '</dt><dd>' . esc_html( $par[1] ) . '</dd></div>';
	}
	return $h . '</dl>';
}

/* Banderas para la tabla de destinos (Polylang trae los PNG). */
function grenvios_ui_bandera( $pais ) {
	$c = array(
		'ecuador' => 'ec', 'colombia' => 'co', 'chile' => 'cl', 'bolivia' => 'bo', 'argentina' => 'ar',
		'estados unidos' => 'us', 'espana' => 'es', 'venezuela' => 've', 'cuba' => 'cu', 'canada' => 'ca',
		'mexico' => 'mx', 'panama' => 'pa', 'costa rica' => 'cr', 'puerto rico' => 'pr', 'brasil' => 'br',
		'uruguay' => 'uy', 'paraguay' => 'py', 'italia' => 'it', 'francia' => 'fr', 'alemania' => 'de',
		'china' => 'cn', 'japon' => 'jp', 'australia' => 'au', 'peru' => 'pe',
	);
	$k = remove_accents( mb_strtolower( trim( wp_strip_all_tags( $pais ) ) ) );
	if ( ! isset( $c[ $k ] ) ) return '';
	if ( function_exists( 'grenvios_bandera_img' ) ) return grenvios_bandera_img( $c[ $k ], 'gr-bq-flag', 20 );
	return '<img class="gr-bq-flag" src="' . esc_url( content_url( 'plugins/polylang/flags/' . $c[ $k ] . '.png' ) ) . '" alt="" width="18" height="12" loading="lazy">';
}

/* Añade la bandera delante del nombre de cada país en una tabla ya pintada. */
function grenvios_ui_tabla_con_banderas( $html ) {
	return preg_replace_callback( '~<th scope="row">(<a [^>]*>)?([^<]+)~u', function ( $m ) {
		return '<th scope="row">' . grenvios_ui_bandera( $m[2] ) . $m[1] . $m[2];
	}, $html );
}
