<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Hero a pantalla completa en TODAS las páginas interiores
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Decisión de la clienta (2026-09-14): el hero de su maqueta —foto a sangre con
 * el camión, antesala, H1 en dos líneas, entradilla, tres garantías y dos
 * botones— va en destinos, servicios, nosotros, contacto y el resto de páginas.
 * Las homes (Perú y la portada de cada ruta) conservan su carrusel.
 *
 * Sustituye a la cabecera clásica `.page-header` por los dos caminos por los
 * que el tema la pinta:
 *   · plantillas .html  → se reescribe el bloque en `grenvios_partial_html`,
 *     antes de resolver los {{campos}}, así que antesala y título siguen siendo
 *     los mismos campos editables de cada página;
 *   · páginas PHP       → logisko_page_banner() llama a grenvios_hero_pagina().
 *
 * Las fichas de destino usan su propia versión con datos del país
 * (inc/destinos-hero.php); el marcado y el CSS son los mismos.
 *
 * Entradilla: la meta description de la página (cada copia de ruta tiene la
 * suya, con su país). Garantías y botones: editables por página desde el panel
 * («Hero · texto, garantías y botones»).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Campos por página con sus valores por defecto. */
function grenvios_hero_pagina_defaults() {
	$lead = '';
	if ( function_exists( 'grenvios_seo_for_slug' ) && function_exists( 'grenvios_current_slug' ) ) {
		$seo  = grenvios_seo_for_slug( grenvios_current_slug() );
		$lead = isset( $seo[1] ) ? (string) $seo[1] : '';
	}
	return array(
		'pg_hero_lead' => $lead,
		'pg_hf1_t'     => 'Seguro',
		'pg_hf1_d'     => 'Tu carga en buenas manos.',
		'pg_hf2_t'     => 'A tiempo',
		'pg_hf2_d'     => 'Cumplimos lo que prometemos.',
		'pg_hf3_t'     => 'Todo el Perú',
		'pg_hf3_d'     => 'Recojo y despacho en {{origen_ciudad}}.',
		'pg_hero_btn1' => 'Cotizar envío',
		'pg_hero_btn2' => 'Rastrea tu pedido',
	);
}

function grenvios_hero_pf( $key ) {
	$defs = grenvios_hero_pagina_defaults();
	return function_exists( 'grenvios_field' ) ? grenvios_field( $key, $defs[ $key ] ) : $defs[ $key ];
}

/* Marcado común. $eyebrow y $title pueden traer {{campos}} sin resolver. */
function grenvios_hero_full( $a ) {
	$home = function_exists( 'grenvios_url_base' ) ? grenvios_url_base() : untrailingslashit( home_url() );
	$img  = ! empty( $a['img'] ) ? $a['img'] : get_template_directory_uri() . '/assets/img/destino-hero.jpg';

	$h  = '<section class="gr-dhero gr-dhero--pagina">';
	$h .= '<div class="gr-dhero-media" aria-hidden="true"><img src="' . esc_url( $img ) . '" alt="" fetchpriority="high" decoding="async"></div>';
	$h .= '<div class="container"><div class="gr-dhero-content">';
	if ( trim( wp_strip_all_tags( (string) $a['eyebrow'] ) ) !== '' ) {
		$h .= '<p class="gr-dhero-eyebrow">' . $a['eyebrow'] . '</p>';
	}
	$h .= '<h1 class="gr-dhero-title">' . $a['title'] . '</h1>';
	if ( trim( (string) $a['lead'] ) !== '' ) {
		$h .= '<p class="gr-dhero-lead">' . esc_html( $a['lead'] ) . '</p>';
	}

	$feats = array(
		array( 'fa-solid fa-shield-halved', grenvios_hero_pf( 'pg_hf1_t' ), grenvios_hero_pf( 'pg_hf1_d' ) ),
		array( 'fa-regular fa-clock',       grenvios_hero_pf( 'pg_hf2_t' ), grenvios_hero_pf( 'pg_hf2_d' ) ),
		array( 'fa-solid fa-paper-plane',   grenvios_hero_pf( 'pg_hf3_t' ), grenvios_hero_pf( 'pg_hf3_d' ) ),
	);
	$lis = '';
	foreach ( $feats as $f ) {
		if ( trim( (string) $f[1] ) === '' ) continue;
		$lis .= '<li><span class="gr-dhero-ic"><i class="' . esc_attr( $f[0] ) . '"></i></span>'
			. '<span class="gr-dhero-ft"><strong>' . esc_html( $f[1] ) . '</strong>' . esc_html( $f[2] ) . '</span></li>';
	}
	if ( $lis !== '' ) $h .= '<ul class="gr-dhero-feats">' . $lis . '</ul>';

	$h .= '<div class="gr-dhero-btns">'
		. '<a href="' . esc_url( $home . '/cotizar/' ) . '" class="gr-dhero-btn"><i class="fa-solid fa-box"></i> ' . esc_html( grenvios_hero_pf( 'pg_hero_btn1' ) ) . ' <i class="fa-solid fa-arrow-right gr-dhero-arrow"></i></a>'
		. '<a href="' . esc_url( $home . '/rastreo-de-envios/' ) . '" class="gr-dhero-btn gr-dhero-btn--line"><i class="fa-solid fa-location-dot"></i> ' . esc_html( grenvios_hero_pf( 'pg_hero_btn2' ) ) . '</a>'
		. '</div>';

	$h .= '</div></div>';
	if ( ! empty( $a['after'] ) ) $h .= $a['after'];   // JSON-LD de las migas
	$h .= '</section>';
	$h .= grenvios_hero_fit_script();
	// {{origen_ciudad}} y compañía: las páginas PHP no pasan por el resolvedor de plantillas.
	return function_exists( 'grenvios_sede_tokens_apply' ) ? grenvios_sede_tokens_apply( $h ) : $h;
}

/* Ajuste de alto (pantalla menos cabecera). Una sola vez por página. */
function grenvios_hero_fit_script() {
	static $hecho = false;
	if ( $hecho ) return '';
	$hecho = true;
	return '<script>(function(){function f(){var h=document.querySelector(".main-header"),a=document.getElementById("wpadminbar");'
		. 'document.documentElement.style.setProperty("--gr-dhero-top",((h?h.offsetHeight:0)+(a?a.offsetHeight:0))+"px");}'
		. 'f();window.addEventListener("resize",f);window.addEventListener("load",f);})();</script>';
}

/* Foto del hero: el «fondo del banner» que la clienta suba para esa página. */
function grenvios_hero_img_pagina( $slug ) {
	if ( ! function_exists( 'grenvios_page_bg_fields' ) ) return '';
	$bgs = grenvios_page_bg_fields();
	if ( empty( $bgs[ $slug ] ) ) return '';
	foreach ( $bgs[ $slug ] as $key => $_ ) return (string) grenvios_field( $key, '' );
	return '';
}

/* Camino PHP: lo llama logisko_page_banner(). */
function grenvios_hero_pagina( $eyebrow, $title, $crumbs = array(), $bg = '' ) {
	ob_start();
	if ( function_exists( 'logisko_breadcrumbs' ) ) logisko_breadcrumbs( $crumbs );
	$migas = preg_replace( '~<nav class="breadcrumbs".*?</nav>~s', '', ob_get_clean() );

	$slug = function_exists( 'grenvios_current_slug' ) ? grenvios_current_slug() : '';
	return grenvios_hero_full( array(
		'eyebrow' => esc_html( $eyebrow ),
		'title'   => wp_kses_post( $title ),
		'lead'    => grenvios_hero_pf( 'pg_hero_lead' ),
		'img'     => $bg !== '' ? $bg : grenvios_hero_img_pagina( $slug ),
		'after'   => $migas,
	) );
}

/* Camino plantillas: reescribe el bloque `.page-header` del .html. */
add_filter( 'grenvios_partial_html', function ( $html, $name ) {
	if ( strpos( (string) $name, 'content-' ) !== 0 || strpos( $html, 'class="page-header' ) === false ) return $html;
	$slug = substr( $name, 8 );
	return preg_replace_callback( '~<section class="page-header"[^>]*>(.*?)</section>~s', function ( $m ) use ( $slug ) {
		$eyebrow = preg_match( '~<h4[^>]*>(.*?)</h4>~s', $m[1], $x ) ? trim( $x[1] ) : '';
		$title   = preg_match( '~<h1[^>]*>(.*?)</h1>~s', $m[1], $y ) ? trim( $y[1] ) : '';
		if ( $title === '' ) return $m[0];
		return grenvios_hero_full( array(
			'eyebrow' => $eyebrow,
			'title'   => $title,
			'lead'    => grenvios_hero_pf( 'pg_hero_lead' ),
			'img'     => grenvios_hero_img_pagina( $slug ),
		) );
	}, $html, 1 );
}, 20, 2 );

/* Panel del editor: entradilla, garantías y botones del hero en cada página
 * interior (las fichas de destino tienen los suyos; la home, su carrusel).
 * Devuelve solo los campos: inc/page-editor.php los mete en la sección del
 * banner, para que todo el hero se edite en un único bloque y el primero. */
function grenvios_hero_pagina_campos_html( $slug, $render_field ) {
	if ( is_front_page() || ! is_callable( $render_field ) ) return '';
	$dest = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();
	if ( isset( $dest[ $slug ] ) ) return '';
	$defs = grenvios_hero_pagina_defaults();
	$lab  = array(
		'pg_hero_lead' => array( 'Entradilla (vacía = la meta description de la página)', 'textarea' ),
		'pg_hf1_t' => array( 'Garantía 1 · título', 'text' ), 'pg_hf1_d' => array( 'Garantía 1 · texto', 'text' ),
		'pg_hf2_t' => array( 'Garantía 2 · título', 'text' ), 'pg_hf2_d' => array( 'Garantía 2 · texto', 'text' ),
		'pg_hf3_t' => array( 'Garantía 3 · título', 'text' ), 'pg_hf3_d' => array( 'Garantía 3 · texto', 'text' ),
		'pg_hero_btn1' => array( 'Botón · Cotizar', 'text' ), 'pg_hero_btn2' => array( 'Botón · Rastrear', 'text' ),
	);
	$out = '';
	foreach ( $lab as $k => $l ) $out .= $render_field( $k, $l[0], $l[1], grenvios_field( $k, $defs[ $k ] ) );
	return $out;
}
