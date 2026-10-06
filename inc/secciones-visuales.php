<?php
/**
 * Secciones «solo texto» → diseño v3: cabecera con icono a la izquierda y el
 * cuerpo en tarjeta a la derecha, el mismo de los bloques del país.
 *
 * Normativa y lista de tipos: skill grenvios-secciones. Solo envuelve: los
 * textos y las clases originales no cambian, así que el panel («Editar página»
 * y los textos dt) los sigue reconociendo.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ¿La sección ya tiene un elemento visual? Un visto o una flecha no cuentan. */
function grenvios_sv_tiene_visual( $html ) {
	if ( preg_match( '~<(?:img|svg|table|figure|ol|iframe|video|picture)\b|background-image~i', $html ) ) return true;
	/* Rejillas de tarjetas: ya tienen forma propia. */
	if ( preg_match( '~class="[^"]*\b(?:rast-state-card|srv-panel|srv-two-grid|srv-trio-grid|gr-bq-card|dest-card)\b~', $html ) ) return true;
	if ( preg_match_all( '~<i class="([^"]*)"~', $html, $m ) ) {
		foreach ( $m[1] as $c ) {
			if ( ! preg_match( '~\bfa-(?:check|xmark|arrow|angle|chevron)~', $c ) ) return true;
		}
	}
	return false;
}

/* Tipos de sección que se convierten (los que salían como título + texto). */
function grenvios_sv_es_candidata( $clases ) {
	$c = ' ' . $clases . ' ';
	if ( preg_match( '~\b(?:gr-pv3|gr-sv|gr-faq|faq)\b~', $c ) ) return false;
	if ( preg_match( '~\b(?:gr-bq--lista|gr-bq--definiciones|dest-seo-sec|gr-pf--dir)\b~', $c ) ) return true;
	/* srv-section o srv-intro sin más clases que las de espaciado y fondo. */
	$resto = trim( preg_replace( '~\b(?:srv-section|srv-intro|bg-grey|padding(?:-top|-bottom)?)\b~', '', $c ) );
	return $resto === '' && preg_match( '~\b(?:srv-section|srv-intro)\b~', $c );
}

/* Icono y antetítulo de la cabecera: primero por la intención del título
 * (prohibido, requisitos, glosario…); si no, el mapa general del tema. */
function grenvios_sv_intencion( $titulo ) {
	$t = remove_accents( mb_strtolower( $titulo ) );
	$propios = array(
		'no envi|no se envi|prohib|no admite|no va |no acept|limita|retiene|retenid' => array( 'fa-solid fa-ban', 'Restricciones' ),
		'glosario|palabra|termino|significa|vocabulario'                    => array( 'fa-solid fa-book-open', 'Glosario' ),
		'checklist|necesitas|requisito|antes de|prepara'                    => array( 'fa-solid fa-clipboard-list', 'Antes de despachar' ),
		'como funciona|paso|proceso|tramo|camino|transporte|seguimiento'    => array( 'fa-solid fa-route', 'Cómo funciona' ),
		'correspond|carta|sobre'                                            => array( 'fa-solid fa-envelope-open-text', 'Correspondencia' ),
		'direccion|codigo postal'                                           => array( 'fa-solid fa-map-location-dot', 'Datos del destinatario' ),
	);
	foreach ( $propios as $k => $v ) if ( preg_match( '~\\b(?:' . $k . ')~u', $t ) ) return $v;
	$ic = function_exists( 'grenvios_ui_icono' ) ? grenvios_ui_icono( $titulo ) : '';
	if ( $ic === '' || strpos( $ic, 'fa-circle-check' ) !== false ) $ic = 'fa-solid fa-circle-info';
	if ( strpos( $ic, 'fa-solid' ) === false ) $ic = 'fa-solid ' . $ic;
	return array( $ic, function_exists( 'grenvios_rd_etiqueta' ) ? grenvios_rd_etiqueta( $titulo ) : '' );
}

function grenvios_sv_seccion( $m ) {
	$clases = trim( $m[1] );
	$inner  = $m[2];
	if ( ! grenvios_sv_es_candidata( $clases ) || grenvios_sv_tiene_visual( $inner ) ) return $m[0];
	/* Preguntas frecuentes y llamadas con botones tienen su propio diseño. */
	if ( preg_match( '~gr-faq-item|accordion|btn-group|<form\b~', $inner ) ) return $m[0];

	if ( ! preg_match( '~^\s*<div class="container">\s*<div class="([^"]*\b(?:srv-head|section-heading|srv-lead)\b[^"]*)">(.*?)</div>(.*)</div>\s*$~s', $inner, $p ) ) return $m[0];
	$cab = $p[2];
	$cuerpo = trim( $p[3] );
	if ( ! preg_match( '~<h2\b[^>]*>.*?</h2>~s', $cab, $h ) ) return $m[0];
	$h2  = $h[0];
	$tit = trim( wp_strip_all_tags( $h2 ) );

	$sub = '';
	if ( preg_match( '~<p class="sub-heading[^"]*">(.*?)</p>~s', $cab, $s ) ) $sub = trim( $s[1] );
	/* Párrafos de la cabecera (entradilla de la sección): abren la tarjeta. */
	$intro = trim( preg_replace( array( '~<p class="sub-heading[^"]*">.*?</p>~s', '~<h2\b[^>]*>.*?</h2>~s' ), '', $cab ) );
	if ( $cuerpo === '' && $intro === '' ) return $m[0];

	list( $ic, $eti ) = grenvios_sv_intencion( $tit );
	if ( $sub === '' ) {
		list( , $pais ) = function_exists( 'grenvios_rd_pais' ) ? grenvios_rd_pais() : array( '', '' );
		$sub = $eti !== '' ? $eti . ( $pais !== '' ? ' · ' . $pais : '' ) : ( $pais !== '' ? 'Envíos a ' . $pais : '' );
		$sub = esc_html( $sub );
	}

	/* Lo que NO se envía no lleva vistos: un visto dice «sí». */
	$no = $eti === 'Restricciones';
	if ( $no ) $cuerpo = str_replace( 'fa-solid fa-check"', 'fa-solid fa-xmark"', $cuerpo );

	/* Una lista o un glosario ya son tarjetas: van sin la tarjeta blanca de fondo. */
	$libre = $intro === '' && preg_match( '~^<(?:ul|dl)\b~', $cuerpo );
	$tipo  = preg_match( '~\bsrv-intro\b~', $clases ) ? ' gr-sv--intro' : '';

	return '<section class="' . esc_attr( $clases ) . ' gr-pv3 gr-sv' . $tipo . ( $no ? ' gr-sv--no' : '' ) . '"><div class="container"><div class="gr-pv3-grid">'
		. '<header class="gr-pv3-head wow fade-in-bottom" data-wow-delay="100ms"><span class="gr-pv3-ic" aria-hidden="true"><i class="' . esc_attr( $ic ) . '"></i></span>'
		. ( $sub !== '' ? '<p class="gr-pv3-sub">' . $sub . '</p>' : '' )
		. $h2 . '</header>'
		. '<div class="gr-pv3-body gr-sv-body' . ( $libre ? ' gr-sv-body--libre' : '' ) . ' wow fade-in-bottom" data-wow-delay="200ms">' . $intro . $cuerpo . '</div>'
		. '</div></div></section>';
}

function grenvios_sv_filtrar( $html ) {
	if ( ! is_string( $html ) || strpos( $html, '<section' ) === false ) return $html;
	if ( function_exists( 'grenvios_estilo_pagina' ) && grenvios_estilo_pagina() === 'clasico' ) return $html;
	$r = preg_replace_callback( '~<section class="([^"]*)">(.*?)</section>~s', 'grenvios_sv_seccion', $html );
	return is_string( $r ) ? $r : $html;
}
/* Después de los rediseños de servicio-intro-v2 (prio. 44–49), que parten del marcado original. */
add_filter( 'grenvios_html_final', 'grenvios_sv_filtrar', 60 );
add_filter( 'grenvios_pais_bloque_html', 'grenvios_sv_filtrar', 20 );
