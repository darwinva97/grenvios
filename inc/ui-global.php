<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  UI global: la skill grenvios-ui aplicada a TODAS las páginas
 * ══════════════════════════════════════════════════════════════════════════
 *
 * INVENTARIO (2026-09-28): unas 30 páginas de la ruta principal —y sus copias
 * en las nueve rutas— se pintaban con los patrones planos del tema: intro
 * centrada (`section.srv-intro`), paneles de solo texto (`div.srv-panel`),
 * listas `ul.check-list` y cabeceras `div.srv-head` sin animación. Además, los
 * bloques «Del blog» y «Continúa tu envío», que salen en casi todas, eran
 * texto plano.
 *
 * Esas páginas las pintan muchos módulos distintos (herramientas, contenido,
 * SEO extra, páginas nuevas, partials…). En vez de reescribir cada uno, aquí se
 * transforma su HTML con reglas acotadas a esos patrones exactos:
 *
 *   intro centrada   → intro dividida con imagen (grenvios_ui_intro)
 *   srv-head         → section-heading con título animado (GSAP)
 *   srv-panel        → tarjeta con icono, elevación y aparición (gr-bq-card)
 *   ul.check-list    → rejilla de vistos (gr-bq-checks)
 *   pasos            → aparición escalonada
 *   Del blog / Continúa tu envío → tarjetas animadas
 *
 * Solo cambia marcado y clases: los textos son los mismos y siguen saliendo de
 * sus campos. Cada regla exige el patrón exacto, así que es idempotente (el
 * filtro corre sobre bloques y luego sobre la página entera) y no toca lo que
 * no reconoce: paneles con formulario o calculadora, partials con otra forma.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Imagen de la intro de cada página (se suma a la del motor). */
add_filter( 'grenvios_ui_img_pagina_mapa', function ( $m ) {
	return $m + array(
		'embalaje-para-envios-internacionales'          => 'embalaje',
		'envio-de-medicinas-al-extranjero'              => 'documentos',
		'encomiendas-internacionales'                   => 'repartidor',
		'cuanto-cuesta-enviar-un-paquete-al-extranjero' => 'embalaje',
		'carga-internacional'                           => 'camion',
		'apostilla-y-traduccion'                        => 'documentos',
		'peso-volumetrico'                              => 'embalaje',
		'envio-de-equipaje'                             => 'avion',
		'envio-de-compras'                              => 'repartidor',
		'envio-de-alimentos'                            => 'embalaje',
		'tiempos-de-entrega'                            => 'avion',
		'envios-para-empresas'                          => 'camion',
		'que-se-puede-enviar'                           => 'embalaje',
		'como-enviar-un-paquete-al-extranjero'          => 'repartidor',
		'recojo-a-domicilio-lima'                       => 'repartidor',
		'aduanas-e-impuestos'                           => 'documentos',
		'seguro-de-envios'                              => 'embalaje',
		'envios-desde-provincias'                       => 'camion',
		'envio-internacional-de-documentos'             => 'documentos',
		'envio-internacional-de-paquetes'               => 'embalaje',
	);
} );

/* La imagen de la intro se cambia en el panel («Imagen de la introducción»). */
add_filter( 'grenvios_text_registry', function ( $reg ) {
	$mapa = apply_filters( 'grenvios_ui_img_pagina_mapa', array() );
	foreach ( array_keys( $mapa ) as $slug ) {
		if ( ! isset( $reg[ $slug ] ) || in_array( $slug, function_exists( 'grenvios_pse_slugs' ) ? grenvios_pse_slugs() : array(), true ) ) continue;
		$reg[ $slug ]['sections']['ui_intro'] = array(
			'label'           => 'Imagen de la introducción',
			'_no_token_check' => true,
			'sel'             => '.gr-bq-intro',
			'fields'          => array( 'ui_intro_img' => array( 'Imagen (vacía = la del diseño)', 'image', '' ) ),
		);
	}
	return $reg;
}, 40 );

add_filter( 'grenvios_html_final', 'grenvios_ui_global', 40 );

function grenvios_ui_global( $html ) {
	if ( is_admin() || ! is_string( $html ) || $html === '' ) return $html;
	$original = $html;
	$html     = grenvios_ui_global_aplicar( $html );
	/* Red de seguridad: si una expresión falla, preg_* devuelve null y la
	 * página se quedaría sin contenido. En ese caso, el HTML original. */
	return is_string( $html ) && $html !== '' ? $html : $original;
}

function grenvios_ui_global_aplicar( $html ) {

	/* 1) Intro centrada → dividida con imagen. Solo la primera de la página. */
	if ( strpos( $html, '<section class="srv-intro padding-top">' ) !== false ) {
		$html = preg_replace_callback(
			'~<section class="srv-intro padding-top">\s*<div class="container">\s*<div class="srv-lead text-center">\s*<h2>([^<]*(?:<(?!/?h2\b)[^<]*)*)</h2>\s*<p>([^<]*(?:<(?!/?p\b)[^<]*)*)</p>\s*</div>\s*</div>\s*</section>~',
			function ( $m ) {
				$slug = function_exists( 'grenvios_current_slug' ) ? grenvios_current_slug() : '';
				$img  = trim( (string) grenvios_field( 'ui_intro_img', '' ) );
				if ( $img === '' ) $img = grenvios_ui_img_pagina( $slug );
				return grenvios_ui_intro( '', $m[1], $m[2], $img, '' );
			},
			$html, 1
		);
	}

	/* 2) Cabeceras srv-head → section-heading con título animado. */
	if ( strpos( $html, '<div class="srv-head' ) !== false ) {
		$html = preg_replace(
			'~<div class="srv-head( text-center)?">(\s*)<h2>~',
			'<div class="srv-head section-heading gr-bq-head$1">$2<h2 class="text-anim" data-effect="fade-in-bottom" data-delay="0.1" data-duration="0.9">',
			$html
		);
	}

	/* 3) Paneles de texto → tarjeta con icono. Se salta el panel que lleve un
	 *    formulario o la calculadora (no debe elevarse al pasar el ratón). */
	if ( strpos( $html, '<div class="srv-panel">' ) !== false ) {
		/* Se mira lo que sigue a cada panel ANTES de convertirlo (hasta el
		 * siguiente panel): si lleva formulario o la calculadora, se deja como
		 * está. Nada de «convertir y deshacer» con un patrón enorme: PHP no lo
		 * compilaba, devolvía null y la página se quedaba sin contenido. */
		$n = 0;
		$html = preg_replace_callback(
			'~<div class="srv-panel">(\s*)<h3 class="srv-panel-title">([^<]*)</h3>~',
			function ( $m ) use ( &$n, $html ) {
				$pos   = $m[0][1];
				$sig   = strpos( $html, '<div class="srv-panel', $pos + 10 );
				$tramo = substr( $html, $pos, ( $sig === false ? 2500 : min( 2500, $sig - $pos ) ) );
				if ( strpos( $tramo, 'gr-pv-calc' ) !== false || strpos( $tramo, '<form' ) !== false || strpos( $tramo, '<input' ) !== false ) {
					return $m[0][0];
				}
				return '<div class="srv-panel gr-bq-card wow fade-in-bottom" data-wow-delay="' . ( 100 + ( $n++ % 4 ) * 110 ) . 'ms">' . $m[1][0]
					. '<span class="gr-bq-ic"><i class="' . esc_attr( grenvios_ui_icono( $m[2][0] ) ) . '" aria-hidden="true"></i></span>'
					. '<h3 class="srv-panel-title">' . $m[2][0] . '</h3>';
			},
			$html, -1, $cuenta, PREG_OFFSET_CAPTURE
		);
	}
	/* 4) ul.check-list → rejilla de vistos. */
	if ( strpos( $html, '<ul class="check-list">' ) !== false ) {
		$html = preg_replace_callback( '~<ul class="check-list">(.*?)</ul>~s', function ( $m ) {
			$i  = 0;
			$li = preg_replace_callback( '~<li>\s*<i class="fa-solid fa-check"></i>\s*~', function () use ( &$i ) {
				return '<li class="wow fade-in-bottom" data-wow-delay="' . ( 80 + ( $i++ % 6 ) * 70 ) . 'ms"><span class="gr-bq-check-ic"><i class="fa-solid fa-check" aria-hidden="true"></i></span><span>';
			}, $m[1] );
			$li = preg_replace( '~(<span class="gr-bq-check-ic">.*?</span><span>)(.*?)</li>~s', '$1$2</span></li>', $li );
			return '<ul class="gr-bq-checks">' . $li . '</ul>';
		}, $html );
	}

	/* 5) Pasos: aparición escalonada. */
	foreach ( array( 'gr-pseo-steps', 'srv-steps' ) as $cls ) {
		if ( strpos( $html, 'class="' . $cls . '"' ) === false ) continue;
		$html = preg_replace_callback( '~<(ol|ul) class="' . $cls . '">(.*?)</\1>~s', function ( $m ) use ( $cls ) {
			$i = 0;
			$in = preg_replace_callback( '~<li>~', function () use ( &$i ) {
				return '<li class="wow fade-in-bottom" data-wow-delay="' . ( 80 + ( $i++ % 6 ) * 90 ) . 'ms">';
			}, $m[2] );
			return '<' . $m[1] . ' class="' . $cls . '">' . $in . '</' . $m[1] . '>';
		}, $html );
	}

	/* 6) «Del blog» y «Continúa tu envío»: tarjetas animadas. */
	if ( strpos( $html, 'gr-bep-card"' ) !== false ) {
		$i = 0;
		$html = preg_replace_callback( '~<article class="grenvios-guide-card gr-bep-card">~', function () use ( &$i ) {
			return '<article class="grenvios-guide-card gr-bep-card wow fade-in-bottom" data-wow-delay="' . ( 100 + ( $i++ % 3 ) * 120 ) . 'ms">';
		}, $html );
		$html = str_replace( '<section class="gr-bep padding-bottom"><div class="container"><div class="section-heading mb-30"><h2>',
			'<section class="gr-bep padding-bottom"><div class="container"><div class="section-heading mb-30"><h2 class="text-anim" data-effect="fade-in-bottom" data-delay="0.1" data-duration="0.9">', $html );
	}
	if ( strpos( $html, '<div class="grenvios-related-group">' ) !== false ) {
		$i = 0;
		$html = preg_replace_callback( '~<div class="grenvios-related-group">~', function () use ( &$i ) {
			return '<div class="grenvios-related-group wow fade-in-bottom" data-wow-delay="' . ( 100 + ( $i++ % 3 ) * 120 ) . 'ms">';
		}, $html );
	}

	/* 7) Banderas en las tablas antiguas (restricciones por país, comparativas,
	 *    tarifas). Solo si la primera celda es un país conocido; idempotente
	 *    porque tras la bandera ya no hay texto justo después de la celda. */
	if ( strpos( $html, '<table' ) !== false && function_exists( 'grenvios_ui_bandera' ) ) {
		$html = preg_replace_callback(
			'~(<tr(?:\s[^>]*)?>\s*<t[dh](?:\s[^>]*)?>)((?:<a\s[^>]*>)?(?:<strong>)?)([^<]{3,40})~u',
			function ( $m ) {
				$f = grenvios_ui_bandera( $m[3] );
				return $f === '' ? $m[0] : $m[1] . $f . $m[2] . $m[3];
			},
			$html
		);
	}

	return $html;
}
