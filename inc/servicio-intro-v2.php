<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Intro de servicio v2 (maqueta del cliente 2026-10-03, skill grenvios-landing)
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Convierte la intro dividida de una página de servicio (`section.gr-bq-intro`)
 * y su entradilla (`section.gr-ent`) en un solo bloque comercial:
 *
 *   antetítulo con raya · título grande · párrafo · tres iconos con etiqueta ·
 *   párrafo de apoyo · dos botones  |  foto con marco rosado y tarjeta
 *   «Soluciones a medida»
 *   franja con la entradilla: cita a la izquierda, píldora vino a la derecha.
 *
 * Los textos del título y del párrafo son los de la página (no se reescriben);
 * lo nuevo (antetítulo, tres etiquetas, apoyo, botones, tarjeta) tiene campos en
 * el panel. La última frase de la entradilla sale en la píldora vino.
 *
 * Para añadir otra página: una entrada en grenvios_si_paginas().
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function grenvios_si_paginas() {
	return apply_filters( 'grenvios_si_paginas', array(
		'carga-internacional' => array(
			'eyebrow' => 'Carga para empresas',
			'feats'   => "Aéreo y terrestre | fa-plane\nCarga consolidada | fa-boxes-stacked\nGestión documental | fa-file-lines",
			'apoyo'   => 'Para importadores, comercios y empresas que necesitan mover carga pesada con seguimiento profesional.',
			'btn1'    => 'Cotizar carga internacional',
			'btn2'    => 'Hablar con un asesor',
			'card_t'  => 'Soluciones a medida',
			'card'    => 'Según peso, volumen y tipo de mercancía.',
		),
	) );
}

add_filter( 'grenvios_text_registry', function ( $reg ) {
	foreach ( grenvios_si_paginas() as $slug => $d ) {
		if ( ! isset( $reg[ $slug ] ) ) continue;
		$reg[ $slug ]['sections']['si_intro'] = array(
			'label'           => 'Introducción · Antetítulo, iconos y botones',
			'_no_token_check' => true,
			'sel'             => '.gr-ci',
			'fields'          => array(
				'si_eyebrow' => array( 'Antetítulo', 'text', $d['eyebrow'] ),
				'si_feats'   => array( 'Tres datos con icono (una línea por dato: Texto | icono Font Awesome)', 'textarea', $d['feats'] ),
				'si_apoyo'   => array( 'Párrafo de apoyo', 'textarea', $d['apoyo'] ),
				'si_btn1'    => array( 'Botón principal (lleva a Cotizar)', 'text', $d['btn1'] ),
				'si_btn2'    => array( 'Botón secundario (abre WhatsApp)', 'text', $d['btn2'] ),
				'si_card_t'  => array( 'Tarjeta sobre la foto · Título', 'text', $d['card_t'] ),
				'si_card'    => array( 'Tarjeta sobre la foto · Texto', 'text', $d['card'] ),
			),
		);
	}
	return $reg;
}, 41 );

add_filter( 'grenvios_html_final', function ( $html ) {
	if ( is_admin() || ! is_string( $html ) || strpos( $html, 'gr-bq-intro' ) === false || strpos( $html, 'gr-ci' ) !== false ) return $html;
	$slug = function_exists( 'grenvios_current_slug' ) ? grenvios_current_slug() : '';
	$pags = grenvios_si_paginas();
	if ( ! isset( $pags[ $slug ] ) ) return $html;
	$d = $pags[ $slug ];

	$a = strpos( $html, '<section class="gr-bq gr-bq-intro' );
	$b = $a === false ? false : strpos( $html, '</section>', $a );
	if ( $b === false ) return $html;
	$sec = substr( $html, $a, $b + 10 - $a );

	if ( ! preg_match( '~<h2[^>]*>(.*?)</h2>~s', $sec, $mh ) ) return $html;
	if ( ! preg_match( '~<div class="gr-bq-lead[^>]*>(.*?)</div>~s', $sec, $ml ) ) return $html;
	$img = preg_match( '~<img src="([^"]+)"~', $sec, $mi ) ? $mi[1] : '';

	$f = function ( $k, $def ) { return function_exists( 'grenvios_field' ) ? grenvios_field( $k, $def ) : $def; };
	$base = function_exists( 'grenvios_url_base' ) ? grenvios_url_base() : home_url();
	$biz  = function_exists( 'grenvios_biz' ) ? grenvios_biz() : array();
	$wa   = isset( $biz['wa_number'] ) ? preg_replace( '/\D/', '', (string) $biz['wa_number'] ) : '';

	$feats = '';
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $f( 'si_feats', $d['feats'] ) ) as $l ) {
		$l = trim( $l );
		if ( $l === '' ) continue;
		$p  = array_map( 'trim', explode( '|', $l, 2 ) );
		$ic = isset( $p[1] ) && preg_match( '/^fa-[a-z0-9-]+$/', $p[1] ) ? $p[1] : 'fa-circle-check';
		$feats .= '<li><span class="gr-ci-ic" aria-hidden="true"><i class="fa-solid ' . esc_attr( $ic ) . '"></i></span><span class="gr-ci-ft">' . esc_html( $p[0] ) . '</span></li>';
	}

	$apoyo = trim( (string) $f( 'si_apoyo', $d['apoyo'] ) );
	$msg   = rawurlencode( 'Hola, quiero hablar con un asesor sobre ' . wp_strip_all_tags( $mh[1] ) . '.' );
	$btns  = '<a class="gr-ci-b1" href="' . esc_url( $base . '/cotizar/' ) . '">' . esc_html( $f( 'si_btn1', $d['btn1'] ) ) . ' <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>'
		. ( $wa !== '' ? '<a class="gr-ci-b2" href="https://wa.me/' . esc_attr( $wa ) . '?text=' . $msg . '" rel="nofollow noopener" target="_blank">' . esc_html( $f( 'si_btn2', $d['btn2'] ) ) . '</a>' : '' );

	$foto = $img !== ''
		? '<figure class="gr-ci-foto wow fade-in-right" data-wow-delay="150ms"><img src="' . esc_url( $img ) . '" alt="" loading="lazy" decoding="async">'
			. '<div class="gr-ci-card"><span class="gr-ci-card-ic" aria-hidden="true"><i class="fa-solid fa-route"></i></span>'
			. '<p><strong>' . esc_html( $f( 'si_card_t', $d['card_t'] ) ) . '</strong><span>' . esc_html( $f( 'si_card', $d['card'] ) ) . '</span></p></div></figure>'
		: '';

	$nueva = '<section class="gr-bq gr-bq-intro gr-ci padding"><div class="container">'
		. '<div class="gr-ci-grid"><div class="gr-ci-tx">'
		. '<p class="gr-ci-eye">' . esc_html( $f( 'si_eyebrow', $d['eyebrow'] ) ) . '</p>'
		. '<h2 class="gr-ci-t">' . $mh[1] . '</h2>'
		. '<div class="gr-ci-lead">' . $ml[1] . '</div>'
		. ( $feats !== '' ? '<ul class="gr-ci-feats">' . $feats . '</ul>' : '' )
		. ( $apoyo !== '' ? '<p class="gr-ci-apoyo">' . esc_html( $apoyo ) . '</p>' : '' )
		. '<div class="gr-ci-btns">' . $btns . '</div></div>'
		. $foto . '</div></div></section>';
	return substr( $html, 0, $a ) . $nueva . substr( $html, $b + 10 );
}, 44 );


/* La entradilla (`section.gr-ent`) llega en otro bloque que la intro: se mueve a
 * la franja de cita cuando se ve la página entera. */
add_filter( 'grenvios_html_final', function ( $html ) {
	if ( is_admin() || ! is_string( $html ) || strpos( $html, 'gr-ci-grid' ) === false || strpos( $html, 'class="gr-ent"' ) === false || strpos( $html, 'gr-ci-cita' ) !== false ) return $html;
	$re_ent = '~<section class="gr-ent"><div class="container"><p class="gr-ent-p">(.*?)</p></div></section>~s';
	if ( ! preg_match( $re_ent, $html, $me ) ) return $html;

	$txt = trim( $me[1] );
	$cita = $txt; $pild = '';
	$pos = mb_strrpos( rtrim( $txt ), '. ' );
	if ( $pos !== false && substr_count( $txt, '. ' ) >= 1 ) {
		$cita = trim( mb_substr( $txt, 0, $pos + 1 ) );
		$pild = trim( mb_substr( $txt, $pos + 2 ) );
	}
	$franja = '<div class="gr-ci-cita wow fade-in-bottom" data-wow-delay="150ms"><span class="gr-ci-q" aria-hidden="true">&ldquo;</span><p>' . $cita . '</p>'
		. ( $pild !== '' ? '<span class="gr-ci-pild"><i class="fa-regular fa-file-lines" aria-hidden="true"></i><span>' . $pild . '</span></span>' : '' ) . '</div>';

	$html = preg_replace( $re_ent, '', $html, 1 );
	$a = strpos( $html, 'gr-ci-grid' );
	$a = $a === false ? false : strpos( $html, '</div></div></section>', $a );
	if ( $a === false ) return $html;
	return substr( $html, 0, $a ) . '</div>' . $franja . '</div></section>' . substr( $html, $a + strlen( '</div></div></section>' ) );
}, 45 );


/* ══════════════════════════════════════════════════════════════════════════
 *  Dos paneles de servicio v2 (maqueta 2026-10-03): «Prepara tu carga…»
 * ══════════════════════════════════════════════════════════════════════════
 * Reordena la sección de dos paneles (`srv-two-grid`: modalidades + documentos)
 * a partir de sus propios datos (los repetidores del panel): cada modalidad
 * pasa a tarjeta con título, píldora de peso y descripción; cada documento, a
 * fila con icono; los datos de contacto salen del propio texto. Lo nuevo
 * (cabecera, subtítulos, barra de resumen, descripciones de los documentos)
 * tiene campos en el panel. */
function grenvios_cm_paginas() {
	return apply_filters( 'grenvios_cm_paginas', array(
		'carga-internacional' => array(
			'eyebrow'   => 'Carga internacional',
			'titulo'    => 'Prepara tu carga para un despacho sin contratiempos',
			'sub'       => 'Elige la modalidad según tu volumen y reúne los documentos clave desde el inicio.',
			'p1_t'      => 'El transporte que mejor se adapta a tu mercancía',
			'p1_s'      => 'Ajustamos la vía al peso, el volumen y la urgencia de cada carga.',
			'p2_t'      => 'Documentación requerida',
			'p2_s'      => 'Para gestionar tu carga internacional con agilidad y conforme a la normativa aduanera, prepara:',
			'resumen'   => 'Aérea: rapidez | Terrestre: ahorro para gran volumen',
			'docs_desc' => "Acredita el valor declarado\nDescribe composición, uso y características",
			'dudas_t'   => '¿Tienes dudas?',
			'dudas'     => 'Nuestro equipo te asesora durante todo el proceso documental.',
			'btn'       => 'Consultar con un asesor',
		),
	) );
}

add_filter( 'grenvios_text_registry', function ( $reg ) {
	foreach ( grenvios_cm_paginas() as $slug => $d ) {
		if ( ! isset( $reg[ $slug ] ) ) continue;
		$reg[ $slug ]['sections']['cm_extra'] = array(
			'label'           => 'Modalidades y documentación · Cabecera y textos del diseño',
			'_no_token_check' => true,
			'sel'             => '.gr-cm',
			'fields'          => array(
				'cm_eyebrow'   => array( 'Antetítulo', 'text', $d['eyebrow'] ),
				'cm_titulo'    => array( 'Título', 'text', $d['titulo'] ),
				'cm_sub'       => array( 'Subtítulo', 'text', $d['sub'] ),
				'cm_p1_t'      => array( 'Panel 1 · Título', 'text', $d['p1_t'] ),
				'cm_p1_s'      => array( 'Panel 1 · Subtítulo', 'text', $d['p1_s'] ),
				'cm_p2_t'      => array( 'Panel 2 · Título', 'text', $d['p2_t'] ),
				'cm_p2_s'      => array( 'Panel 2 · Subtítulo', 'text', $d['p2_s'] ),
				'cm_resumen'   => array( 'Barra de resumen (Etiqueta: texto | Etiqueta: texto)', 'text', $d['resumen'] ),
				'cm_docs_desc' => array( 'Descripción de cada documento (una línea por documento, en orden)', 'textarea', $d['docs_desc'] ),
				'cm_dudas_t'   => array( 'Caja de contacto · Título', 'text', $d['dudas_t'] ),
				'cm_dudas'     => array( 'Caja de contacto · Texto', 'text', $d['dudas'] ),
				'cm_btn'       => array( 'Botón (abre WhatsApp)', 'text', $d['btn'] ),
			),
		);
	}
	return $reg;
}, 42 );

add_filter( 'grenvios_html_final', function ( $html ) {
	if ( is_admin() || ! is_string( $html ) || strpos( $html, 'srv-two-grid' ) === false || strpos( $html, 'gr-cm' ) !== false ) return $html;
	$slug = function_exists( 'grenvios_current_slug' ) ? grenvios_current_slug() : '';
	$pags = grenvios_cm_paginas();
	if ( ! isset( $pags[ $slug ] ) ) return $html;
	$d = $pags[ $slug ];

	$g = strpos( $html, 'srv-two-grid' );
	$a = strrpos( substr( $html, 0, $g ), '<section' );
	$b = $a === false ? false : strpos( $html, '</section>', $a );
	if ( $b === false ) return $html;
	$sec = substr( $html, $a, $b + 10 - $a );

	$trozos = preg_split( '~<div class="srv-panel[^"]*"[^>]*>~', $sec );
	if ( count( $trozos ) !== 3 ) return $html;

	$panel = function ( $t ) {
		$o = array( 'titulo' => '', 'intro' => '', 'items' => array(), 'cierre' => '' );
		if ( preg_match( '~<h3[^>]*>(.*?)</h3>~s', $t, $m ) ) $o['titulo'] = trim( wp_strip_all_tags( $m[1] ) );
		if ( preg_match( '~</h3>\s*<p>(.*?)</p>~s', $t, $m ) ) $o['intro'] = trim( wp_strip_all_tags( $m[1] ) );
		if ( preg_match_all( '~<li>(?:<i class="([^"]*)"></i>)?(.*?)</li>~s', $t, $m, PREG_SET_ORDER ) ) {
			foreach ( $m as $x ) $o['items'][] = array( 'ic' => $x[1], 'tx' => trim( wp_strip_all_tags( $x[2] ) ) );
		}
		if ( preg_match( '~</ul>\s*</div>\s*<p>(.*?)</p>~s', $t, $m ) ) $o['cierre'] = $m[1];
		return $o;
	};
	$p1 = $panel( $trozos[1] ); $p2 = $panel( $trozos[2] );
	if ( ! $p1['items'] || ! $p2['items'] ) return $html;

	$f   = function ( $k, $def ) { return function_exists( 'grenvios_field' ) ? grenvios_field( $k, $def ) : $def; };
	$cap = function ( $s ) { $s = trim( $s ); return $s === '' ? '' : mb_strtoupper( mb_substr( $s, 0, 1 ) ) . mb_substr( $s, 1 ); };

	/* Modalidades: cada elemento con camión o avión abre una tarjeta; los siguientes se suman a su descripción. */
	$cards = array();
	foreach ( $p1['items'] as $it ) {
		$abre = preg_match( '/plane|truck/', $it['ic'] ) && preg_match( '/^carga\b/iu', $it['tx'] );
		if ( $abre || ! $cards ) {
			$ic = strpos( $it['ic'], 'truck' ) !== false ? 'fa-truck' : 'fa-plane';
			$titulo = $it['tx']; $chip = ''; $desc = array();
			if ( preg_match( '~^(.*?)\s+(desde|de)\s+(\d[\d.,]*(?:\s*(?:a|-|–)\s*\d[\d.,]*)?\s*kg)(.*)$~iu', $it['tx'], $m ) ) {
				$titulo = trim( $m[1] ); $chip = $cap( $m[2] ) . ' ' . $m[3];
				$resto = trim( $m[4], " ,.;" );
				if ( $resto !== '' ) $desc[] = $cap( $resto );
			}
			$cards[] = array( 'ic' => $ic, 'titulo' => $titulo, 'chip' => $chip, 'desc' => $desc );
		} else {
			$cards[ count( $cards ) - 1 ]['desc'][] = $cap( $it['tx'] );
		}
	}
	$h1 = '';
	foreach ( $cards as $i => $c ) {
		$h1 .= '<div class="gr-cm-mod' . ( $i === 0 ? ' is-destacada' : '' ) . '"><span class="gr-cm-ic" aria-hidden="true"><i class="fa-solid ' . esc_attr( $c['ic'] ) . '"></i></span>'
			. '<div class="gr-cm-mod-tx"><strong>' . esc_html( $c['titulo'] ) . '</strong>'
			. ( $c['desc'] ? '<p>' . implode( '', array_map( function ( $x ) { return '<span class="gr-cm-l">' . esc_html( $x ) . '</span>'; }, $c['desc'] ) ) . '</p>' : '' ) . '</div>'
			. ( $c['chip'] !== '' ? '<span class="gr-cm-chip">' . esc_html( $c['chip'] ) . '</span>' : '' ) . '</div>';
	}

	/* Resumen: «Etiqueta: texto | Etiqueta: texto». */
	$resumen = '';
	foreach ( explode( '|', (string) $f( 'cm_resumen', $d['resumen'] ) ) as $r ) {
		$r = trim( $r ); if ( $r === '' ) continue;
		$pp = explode( ':', $r, 2 );
		$resumen .= isset( $pp[1] ) ? '<span><strong>' . esc_html( trim( $pp[0] ) ) . ':</strong> ' . esc_html( trim( $pp[1] ) ) . '</span>' : '<span>' . esc_html( $r ) . '</span>';
	}

	/* Documentos con su descripción (campo del panel, en orden). */
	$descs = preg_split( '/\r\n|\r|\n/', (string) $f( 'cm_docs_desc', $d['docs_desc'] ) );
	$h2 = '';
	foreach ( $p2['items'] as $i => $it ) {
		$ds = isset( $descs[ $i ] ) ? trim( $descs[ $i ] ) : '';
		$h2 .= '<div class="gr-cm-doc"><span class="gr-cm-ic gr-cm-ic--doc" aria-hidden="true"><i class="fa-regular fa-file-lines"></i></span>'
			. '<div><strong>' . esc_html( $it['tx'] ) . '</strong>' . ( $ds !== '' ? '<span>' . esc_html( $ds ) . '</span>' : '' ) . '</div></div>';
	}

	/* Contacto: sale de los enlaces del texto de cierre; si no hay, de los datos del sitio. */
	$mail = ''; $tel = ''; $telh = '';
	if ( preg_match( '~mailto:([^"]+)"~', $p2['cierre'], $m ) ) $mail = $m[1];
	if ( preg_match( '~tel:([^"]+)"[^>]*>(.*?)</a>~', $p2['cierre'], $m ) ) { $telh = $m[1]; $tel = wp_strip_all_tags( $m[2] ); }
	$biz = function_exists( 'grenvios_biz' ) ? grenvios_biz() : array();
	if ( $mail === '' && ! empty( $biz['email'] ) ) $mail = $biz['email'];
	if ( $tel === '' && ! empty( $biz['phone'] ) ) { $tel = $biz['phone']; $telh = '+' . preg_replace( '/\D/', '', $tel ); }
	$wa = isset( $biz['wa_number'] ) ? preg_replace( '/\D/', '', (string) $biz['wa_number'] ) : '';
	$contacto = ( $mail !== '' ? '<a href="mailto:' . esc_attr( $mail ) . '"><i class="fa-regular fa-envelope" aria-hidden="true"></i> ' . esc_html( $mail ) . '</a>' : '' )
		. ( $tel !== '' ? '<a href="tel:' . esc_attr( $telh ) . '"><i class="fa-solid fa-phone" aria-hidden="true"></i> ' . esc_html( $tel ) . '</a>' : '' );

	$nueva = '<section class="srv-section gr-cm padding"><div class="container">'
		. '<div class="section-heading text-center mb-40"><p class="sub-heading">' . esc_html( $f( 'cm_eyebrow', $d['eyebrow'] ) ) . '</p>'
		. '<h2>' . esc_html( $f( 'cm_titulo', $d['titulo'] ) ) . '</h2><p class="gr-cm-sub">' . esc_html( $f( 'cm_sub', $d['sub'] ) ) . '</p></div>'
		. '<div class="gr-cm-grid">'
		. '<div class="gr-cm-panel wow fade-in-bottom" data-wow-delay="100ms"><div class="gr-cm-top"><span class="gr-cm-ic gr-cm-ic--g" aria-hidden="true"><i class="fa-solid fa-boxes-stacked"></i></span><span class="gr-cm-tag">01 · ' . esc_html( mb_strtoupper( $p1['titulo'] ) ) . '</span></div>'
		. '<h3>' . esc_html( $f( 'cm_p1_t', $d['p1_t'] ) ) . '</h3><p class="gr-cm-ps">' . esc_html( $f( 'cm_p1_s', $d['p1_s'] ) ) . '</p>'
		. '<div class="gr-cm-mods">' . $h1 . '</div>'
		. ( $resumen !== '' ? '<div class="gr-cm-resumen"><i class="fa-solid fa-chart-simple" aria-hidden="true"></i>' . $resumen . '</div>' : '' ) . '</div>'
		. '<div class="gr-cm-panel wow fade-in-bottom" data-wow-delay="210ms"><div class="gr-cm-top"><span class="gr-cm-ic gr-cm-ic--g" aria-hidden="true"><i class="fa-regular fa-file-circle-check"></i></span><span class="gr-cm-tag">02 · DOCUMENTACIÓN</span></div>'
		. '<h3>' . esc_html( $f( 'cm_p2_t', $d['p2_t'] ) ) . '</h3><p class="gr-cm-ps">' . esc_html( $f( 'cm_p2_s', $d['p2_s'] ) ) . '</p>'
		. '<div class="gr-cm-docs">' . $h2 . '</div>'
		. '<div class="gr-cm-dudas"><span class="gr-cm-dudas-ic" aria-hidden="true"><i class="fa-solid fa-headset"></i></span>'
		. '<p><strong>' . esc_html( $f( 'cm_dudas_t', $d['dudas_t'] ) ) . '</strong><span>' . esc_html( $f( 'cm_dudas', $d['dudas'] ) ) . '</span></p>'
		. ( $contacto !== '' ? '<div class="gr-cm-contacto">' . $contacto . '</div>' : '' ) . '</div>'
		. ( $wa !== '' ? '<a class="gr-cm-btn" href="https://wa.me/' . esc_attr( $wa ) . '?text=' . rawurlencode( 'Hola, tengo dudas sobre la documentación de carga internacional.' ) . '" rel="nofollow noopener" target="_blank">' . esc_html( $f( 'cm_btn', $d['btn'] ) ) . ' <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>' : '' )
		. '</div></div></div></section>';
	return substr( $html, 0, $a ) . $nueva . substr( $html, $b + 10 );
}, 46 );


/* ══════════════════════════════════════════════════════════════════════════
 *  Cierre de servicio v2 (maqueta 2026-10-03): «Solución logística integral»
 * ══════════════════════════════════════════════════════════════════════════
 * Tres bloques al final de la página, todos desde su propio contenido:
 *   · `srv-trio`: antetítulo con rayas, título con la última palabra en vino,
 *     párrafo (con su enlace) y los accesos como tarjetas con flecha;
 *   · `srv-features`: tira de garantías (estilo v2 en gr-v2.css);
 *   · `cta-section` del servicio: franja vino con repartidor y los botones
 *     «Cotizar mi carga» y «WhatsApp». */
function grenvios_cc_paginas() {
	return apply_filters( 'grenvios_cc_paginas', array(
		'carga-internacional' => array(
			'eyebrow' => 'Tu operación, de principio a fin',
			'btn1'    => 'Cotizar mi carga',
			'btn2'    => 'WhatsApp',
		),
	) );
}

add_filter( 'grenvios_text_registry', function ( $reg ) {
	foreach ( grenvios_cc_paginas() as $slug => $d ) {
		if ( ! isset( $reg[ $slug ] ) ) continue;
		$reg[ $slug ]['sections']['cc_extra'] = array(
			'label'           => 'Cierre · Antetítulo y botones de la franja final',
			'_no_token_check' => true,
			'sel'             => '.gr-cc',
			'fields'          => array(
				'cc_eyebrow' => array( 'Antetítulo de «Solución logística»', 'text', $d['eyebrow'] ),
				'cc_btn1'    => array( 'Franja final · Botón 1 (Cotizar)', 'text', $d['btn1'] ),
				'cc_btn2'    => array( 'Franja final · Botón 2 (WhatsApp)', 'text', $d['btn2'] ),
			),
		);
	}
	return $reg;
}, 43 );

add_filter( 'grenvios_html_final', function ( $html ) {
	if ( is_admin() || ! is_string( $html ) || strpos( $html, 'gr-cc' ) !== false ) return $html;
	$slug = function_exists( 'grenvios_current_slug' ) ? grenvios_current_slug() : '';
	$pags = grenvios_cc_paginas();
	if ( ! isset( $pags[ $slug ] ) ) return $html;
	$d = $pags[ $slug ];
	$f = function ( $k, $def ) { return function_exists( 'grenvios_field' ) ? grenvios_field( $k, $def ) : $def; };

	/* 1) srv-trio → «Solución logística integral» */
	if ( strpos( $html, 'srv-trio' ) !== false && strpos( $html, 'srv-cards-2' ) !== false ) {
		$a = strpos( $html, '<section class="srv-trio' );
		$b = $a === false ? false : strpos( $html, '</section>', $a );
		if ( $b !== false ) {
			$sec = substr( $html, $a, $b + 10 - $a );
			if ( preg_match( '~<h2[^>]*>(.*?)</h2>~s', $sec, $mh ) && preg_match( '~</h2>\s*<p>(.*?)</p>~s', $sec, $mp )
				&& preg_match_all( '~<div class="srv-card"><span class="srv-card-ic"><i class="([^"]+)"></i></span><h3>(.*?)</h3></div>~s', $sec, $mc, PREG_SET_ORDER ) ) {
				$tit = trim( wp_strip_all_tags( $mh[1] ) );
				$pos = mb_strrpos( $tit, ' ' );
				$tit_h = $pos !== false ? esc_html( mb_substr( $tit, 0, $pos ) ) . ' <span class="gr-cc-hl">' . esc_html( mb_substr( $tit, $pos + 1 ) ) . '</span>' : esc_html( $tit );
				$links = '';
				foreach ( $mc as $c ) {
					$href = preg_match( '~href="([^"]+)"~', $c[2], $mm ) ? $mm[1] : '#';
					$links .= '<a class="gr-cc-link wow fade-in-bottom" href="' . esc_url( html_entity_decode( $href ) ) . '"><span class="gr-cc-ic" aria-hidden="true"><i class="' . esc_attr( $c[1] ) . '"></i></span>'
						. '<span class="gr-cc-tx">' . esc_html( trim( wp_strip_all_tags( $c[2] ) ) ) . '</span><i class="fa-solid fa-arrow-right gr-cc-go" aria-hidden="true"></i></a>';
				}
				$nueva = '<section class="srv-trio gr-cc gr-cc-trio padding"><div class="container"><div class="gr-cc-cab text-center">'
					. '<p class="gr-cc-eye">' . esc_html( $f( 'cc_eyebrow', $d['eyebrow'] ) ) . '</p>'
					. '<h2>' . $tit_h . '</h2><p class="gr-cc-lead">' . wp_kses_post( $mp[1] ) . '</p></div>'
					. '<div class="gr-cc-links">' . $links . '</div></div></section>';
				$html = substr( $html, 0, $a ) . $nueva . substr( $html, $b + 10 );
			}
		}
	}

	/* 2) CTA final del servicio → franja vino con repartidor */
	if ( strpos( $html, 'srv-cta-actions' ) !== false ) {
		$g = strpos( $html, 'srv-cta-actions' );
		$a = strrpos( substr( $html, 0, $g ), '<section' );
		$b = $a === false ? false : strpos( $html, '</section>', $a );
		if ( $b !== false ) {
			$sec = substr( $html, $a, $b + 10 - $a );
			if ( preg_match( '~<h2[^>]*>(.*?)</h2>~s', $sec, $mh ) && preg_match( '~</h2>\s*<p>(.*?)</p>~s', $sec, $mp ) && preg_match_all( '~<a href="([^"]+)"~', $sec, $ma ) && count( $ma[1] ) >= 1 ) {
				$cot = html_entity_decode( $ma[1][0] );
				$wa  = isset( $ma[1][1] ) ? html_entity_decode( $ma[1][1] ) : '';
				$nueva = '<section class="cta-section gr-cc gr-cc-cta padding"><div class="container"><div class="row cta-wrapper gx-0"><div class="col-lg-8">'
					. '<div class="section-heading white"><h2>' . $mh[1] . '</h2><p>' . wp_kses_post( $mp[1] ) . '</p>'
					. '<div class="btn-wrapper"><a class="default-btn" href="' . esc_url( $cot ) . '">' . esc_html( $f( 'cc_btn1', $d['btn1'] ) ) . ' <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>'
					. ( $wa !== '' ? '<a class="default-btn gr-cc-wa" href="' . esc_url( $wa ) . '" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> ' . esc_html( $f( 'cc_btn2', $d['btn2'] ) ) . '</a>' : '' )
					. '</div></div></div></div></div></section>';
				$html = substr( $html, 0, $a ) . $nueva . substr( $html, $b + 10 );
			}
		}
	}
	return $html;
}, 47 );


/* ══════════════════════════════════════════════════════════════════════════
 *  Sección de contenido con factores v2 (maqueta 2026-10-03): «Qué define el precio…»
 * ══════════════════════════════════════════════════════════════════════════
 * Rehace la primera sección de contenido SEO de la página (`gr-pseo--n1`) con
 * su propio texto: antetítulo con raya, título (con el acento en vino), primer
 * párrafo, tarjeta de tres factores, «Así acompañamos tu carga» con los puntos
 * de la lista, aviso con la frase del enlace y botón; a la derecha, la foto con
 * tarjeta. El segundo párrafo se conserva completo, plegado bajo el aviso. */
function grenvios_pv_paginas() {
	return apply_filters( 'grenvios_pv_paginas', array(
		'carga-internacional' => array(
			'fact_t'  => 'Tres factores que forman el costo',
			'factores'=> "Peso total | Se compara el peso real con el volumétrico. | fa-weight-hanging\nVolumen | El espacio que ocupa la carga consolidada. | fa-cube\nTipo de mercancía | Define requisitos y documentación. | fa-file-lines",
			'acomp_t' => 'Así acompañamos tu carga',
			'aviso_2' => 'La factura debe coincidir con el contenido.',
			'mas'     => 'Leer más sobre la documentación',
			'btn'     => 'Cotizar mi carga',
			'card_t'  => 'Cotización clara',
			'card'    => 'Peso, volumen y mercancía considerados desde el inicio.',
		),
	) );
}

add_filter( 'grenvios_text_registry', function ( $reg ) {
	foreach ( grenvios_pv_paginas() as $slug => $d ) {
		if ( ! isset( $reg[ $slug ] ) ) continue;
		$reg[ $slug ]['sections']['pv_extra'] = array(
			'label'           => 'Qué define el precio · Factores, aviso y botón',
			'_no_token_check' => true,
			'sel'             => '.gr-pv',
			'fields'          => array(
				'pv_fact_t'  => array( 'Tarjeta de factores · Título', 'text', $d['fact_t'] ),
				'pv_factores'=> array( 'Factores (una línea por factor: Título | descripción | icono fa-…)', 'textarea', $d['factores'] ),
				'pv_acomp_t' => array( 'Título de la lista «Así acompañamos…»', 'text', $d['acomp_t'] ),
				'pv_aviso_2' => array( 'Aviso · Segunda línea', 'text', $d['aviso_2'] ),
				'pv_mas'     => array( 'Texto del desplegable del segundo párrafo', 'text', $d['mas'] ),
				'pv_btn'     => array( 'Botón (lleva a Cotizar)', 'text', $d['btn'] ),
				'pv_card_t'  => array( 'Tarjeta sobre la foto · Título', 'text', $d['card_t'] ),
				'pv_card'    => array( 'Tarjeta sobre la foto · Texto', 'text', $d['card'] ),
			),
		);
	}
	return $reg;
}, 44 );

add_filter( 'grenvios_html_final', function ( $html ) {
	if ( is_admin() || ! is_string( $html ) || strpos( $html, 'gr-pseo--n1' ) === false || strpos( $html, 'gr-pv' ) !== false ) return $html;
	$slug = function_exists( 'grenvios_current_slug' ) ? grenvios_current_slug() : '';
	$pags = grenvios_pv_paginas();
	if ( ! isset( $pags[ $slug ] ) ) return $html;
	$d = $pags[ $slug ];

	$marca = 'gr-pseo--' . $slug . ' gr-pseo--n1';
	$g = strpos( $html, $marca );
	if ( $g === false ) return $html;
	$a = strrpos( substr( $html, 0, $g ), '<section' );
	$b = $a === false ? false : strpos( $html, '</section>', $a );
	if ( $b === false ) return $html;
	$sec = substr( $html, $a, $b + 10 - $a );

	if ( ! preg_match( '~<p class="sub-heading">(.*?)</p><h2>(.*?)</h2>~s', $sec, $mh ) ) return $html;
	if ( ! preg_match( '~<div class="gr-pseo-body">(.*?)</div></div><aside~s', $sec, $mb ) ) return $html;
	$body = $mb[1];
	if ( ! preg_match_all( '~<p>(.*?)</p>~s', preg_replace( '~<ul.*?</ul>~s', '', $body ), $mp ) || count( $mp[1] ) < 1 ) return $html;
	if ( ! preg_match_all( '~<li[^>]*>\s*<strong>(.*?)</strong>(.*?)</li>~s', $body, $ml, PREG_SET_ORDER ) || count( $ml ) < 2 ) return $html;
	$img = preg_match( '~<img src="([^"]+)"~', $sec, $mi ) ? $mi[1] : '';

	$f = function ( $k, $def ) { return function_exists( 'grenvios_field' ) ? grenvios_field( $k, $def ) : $def; };
	$base = function_exists( 'grenvios_url_base' ) ? grenvios_url_base() : home_url();

	/* Factores. */
	$fact = '';
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $f( 'pv_factores', $d['factores'] ) ) as $i => $l ) {
		$l = trim( $l ); if ( $l === '' ) continue;
		$p = array_map( 'trim', explode( '|', $l ) );
		$ic = isset( $p[2] ) && preg_match( '/^fa-[a-z0-9-]+$/', $p[2] ) ? $p[2] : 'fa-circle-check';
		$fact .= '<li class="gr-pv-fa"><span class="gr-pv-n">' . sprintf( '%02d', $i + 1 ) . '</span><span class="gr-pv-fic" aria-hidden="true"><i class="fa-solid ' . esc_attr( $ic ) . '"></i></span>'
			. '<strong>' . esc_html( $p[0] ) . '</strong>' . ( isset( $p[1] ) ? '<span>' . esc_html( $p[1] ) . '</span>' : '' ) . '</li>';
	}

	/* «Así acompañamos…»: los puntos de la lista de la sección. */
	$iconos = array( 'fa-box', 'fa-warehouse', 'fa-location-dot', 'fa-circle-check' );
	$acomp = '';
	foreach ( $ml as $i => $x ) {
		$acomp .= '<li class="gr-pv-ac"><span class="gr-pv-n">' . sprintf( '%02d', $i + 1 ) . '</span><span class="gr-pv-aic" aria-hidden="true"><i class="fa-solid ' . esc_attr( $iconos[ min( $i, 3 ) ] ) . '"></i></span>'
			. '<strong>' . esc_html( trim( preg_replace( '/[.:]\s*$/u', '', wp_strip_all_tags( $x[1] ) ) ) ) . '</strong><span>' . wp_kses_post( trim( $x[2] ) ) . '</span></li>';
	}

	/* Aviso: la frase del enlace del segundo párrafo + una segunda línea del panel. */
	$aviso1 = '';
	$p2 = isset( $mp[1][1] ) ? $mp[1][1] : '';
	if ( $p2 !== '' && preg_match( '~<a [^>]*>(.*?)</a>~s', $p2, $mm ) ) {
		$aviso1 = trim( wp_strip_all_tags( $mm[1] ) );
		$aviso1 = mb_strtoupper( mb_substr( $aviso1, 0, 1 ) ) . mb_substr( $aviso1, 1 ) . '.';
	}
	$aviso = $aviso1 !== '' ? '<div class="gr-pv-aviso"><span class="gr-pv-aviso-ic" aria-hidden="true"><i class="fa-regular fa-circle-info"></i></span><p><strong>' . esc_html( $aviso1 ) . '</strong><span>' . esc_html( $f( 'pv_aviso_2', $d['aviso_2'] ) ) . '</span></p></div>' : '';
	$mas = $p2 !== '' ? '<details class="gr-pv-mas"><summary>' . esc_html( $f( 'pv_mas', $d['mas'] ) ) . '</summary><p>' . wp_kses_post( $p2 ) . '</p></details>' : '';

	$foto = $img !== ''
		? '<figure class="gr-pv-foto wow fade-in-left" data-wow-delay="150ms"><img src="' . esc_url( $img ) . '" alt="" loading="lazy" decoding="async">'
			. '<div class="gr-pv-card"><span class="gr-pv-card-ic" aria-hidden="true"><i class="fa-solid fa-cube"></i></span><p><strong>' . esc_html( $f( 'pv_card_t', $d['card_t'] ) ) . '</strong><span>' . esc_html( $f( 'pv_card', $d['card'] ) ) . '</span></p></div></figure>'
		: '';

	$nueva = '<section class="gr-pseo gr-pseo--' . esc_attr( $slug ) . ' gr-pseo--n1 gr-pv padding-bottom"><div class="container"><div class="gr-pv-grid"><div class="gr-pv-tx">'
		. '<p class="gr-pv-eye">' . $mh[1] . '</p><h2>' . $mh[2] . '</h2>'
		. '<div class="gr-pv-lead"><p>' . wp_kses_post( $mp[1][0] ) . '</p></div>'
		. ( $fact !== '' ? '<div class="gr-pv-caja"><h3>' . esc_html( $f( 'pv_fact_t', $d['fact_t'] ) ) . '</h3><ul class="gr-pv-fgrid">' . $fact . '</ul></div>' : '' )
		. '<h3 class="gr-pv-h3">' . esc_html( $f( 'pv_acomp_t', $d['acomp_t'] ) ) . '</h3><ul class="gr-pv-acomp">' . $acomp . '</ul>'
		. $aviso . $mas
		. '<a class="gr-pv-btn" href="' . esc_url( $base . '/cotizar/' ) . '">' . esc_html( $f( 'pv_btn', $d['btn'] ) ) . ' <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>'
		. '</div>' . $foto . '</div></div></section>';
	return substr( $html, 0, $a ) . $nueva . substr( $html, $b + 10 );
}, 48 );


/* ══════════════════════════════════════════════════════════════════════════
 *  Sección de pasos v2 (maqueta 2026-10-03): «Cómo dejar tu carga lista para salir»
 * ══════════════════════════════════════════════════════════════════════════
 * Rehace la segunda sección de contenido SEO (`gr-pseo--n2`) con su propio
 * texto: foto con tarjeta a la izquierda; a la derecha antetítulo, título con el
 * acento en vino, introducción, «Checklist de despacho» con los pasos unidos por
 * una línea (número, icono, título y descripción), caja con la frase final y
 * botón. El enlace de la frase final se conserva. */
function grenvios_pd_paginas() {
	return apply_filters( 'grenvios_pd_paginas', array(
		'carga-internacional' => array(
			'check_t'  => 'Checklist de despacho',
			'iconos'   => 'fa-file-lines, fa-clipboard-list, fa-box, fa-boxes-stacked, fa-file-circle-check',
			'caja_t'   => '¿Envíos frecuentes?',
			'caja_l'   => 'Conoce los envíos para empresas',
			'btn'      => 'Coordinar mi despacho',
			'card_t'   => 'Preparación comercial',
			'card'     => 'Lista antes de salir de Lima.',
		),
	) );
}

add_filter( 'grenvios_text_registry', function ( $reg ) {
	foreach ( grenvios_pd_paginas() as $slug => $d ) {
		if ( ! isset( $reg[ $slug ] ) ) continue;
		$reg[ $slug ]['sections']['pd_extra'] = array(
			'label'           => 'Antes del despacho · Checklist, caja y botón',
			'_no_token_check' => true,
			'sel'             => '.gr-pd',
			'fields'          => array(
				'pd_check_t' => array( 'Título del checklist', 'text', $d['check_t'] ),
				'pd_iconos'  => array( 'Iconos de los pasos (separados por comas, fa-…)', 'text', $d['iconos'] ),
				'pd_caja_t'  => array( 'Caja final · Título', 'text', $d['caja_t'] ),
				'pd_caja_l'  => array( 'Caja final · Texto del enlace', 'text', $d['caja_l'] ),
				'pd_btn'     => array( 'Botón (lleva a Cotizar)', 'text', $d['btn'] ),
				'pd_card_t'  => array( 'Tarjeta sobre la foto · Título', 'text', $d['card_t'] ),
				'pd_card'    => array( 'Tarjeta sobre la foto · Texto', 'text', $d['card'] ),
			),
		);
	}
	return $reg;
}, 45 );

add_filter( 'grenvios_html_final', function ( $html ) {
	if ( is_admin() || ! is_string( $html ) || strpos( $html, 'gr-pseo--n2' ) === false || strpos( $html, 'gr-pd' ) !== false ) return $html;
	$slug = function_exists( 'grenvios_current_slug' ) ? grenvios_current_slug() : '';
	$pags = grenvios_pd_paginas();
	if ( ! isset( $pags[ $slug ] ) ) return $html;
	$d = $pags[ $slug ];

	$g = strpos( $html, 'gr-pseo--' . $slug . ' gr-pseo--n2' );
	if ( $g === false ) return $html;
	$a = strrpos( substr( $html, 0, $g ), '<section' );
	$b = $a === false ? false : strpos( $html, '</section>', $a );
	if ( $b === false ) return $html;
	$sec = substr( $html, $a, $b + 10 - $a );

	if ( ! preg_match( '~<p class="sub-heading">(.*?)</p><h2>(.*?)</h2>~s', $sec, $mh ) ) return $html;
	if ( ! preg_match( '~<div class="gr-pseo-body">(.*?)</div></div><aside~s', $sec, $mb ) ) return $html;
	$body = $mb[1];
	if ( ! preg_match( '~^\s*<p>(.*?)</p>~s', $body, $ml ) ) return $html;
	if ( ! preg_match_all( '~<li[^>]*>\s*<strong>(.*?)</strong>(.*?)</li>~s', $body, $mi, PREG_SET_ORDER ) || count( $mi ) < 2 ) return $html;
	$cierre = preg_match( '~</ol>\s*<p>(.*?)</p>\s*$~s', $body, $mc ) ? $mc[1] : '';
	$img = preg_match( '~<img src="([^"]+)"~', $sec, $mim ) ? $mim[1] : '';

	$f = function ( $k, $def ) { return function_exists( 'grenvios_field' ) ? grenvios_field( $k, $def ) : $def; };
	$base = function_exists( 'grenvios_url_base' ) ? grenvios_url_base() : home_url();
	$cap = function ( $s ) { $s = trim( $s ); return $s === '' ? '' : mb_strtoupper( mb_substr( $s, 0, 1 ) ) . mb_substr( $s, 1 ); };
	$lead = preg_replace( '/:\s*$/u', '.', trim( wp_strip_all_tags( $ml[1] ) ) );

	$iconos = array_values( array_filter( array_map( 'trim', explode( ',', (string) $f( 'pd_iconos', $d['iconos'] ) ) ), function ( $x ) { return preg_match( '/^fa-[a-z0-9-]+$/', $x ); } ) );
	$pasos = '';
	foreach ( $mi as $i => $x ) {
		$tit = trim( preg_replace( '/[.:]\s*$/u', '', wp_strip_all_tags( $x[1] ) ) );
		$ds  = $cap( preg_replace( '/^\s*(y|,)\s+/iu', '', trim( $x[2] ) ) );
		$ic  = isset( $iconos[ $i ] ) ? $iconos[ $i ] : 'fa-circle-check';
		$pasos .= '<li class="gr-pd-paso wow fade-in-bottom" data-wow-delay="' . ( 100 + $i * 80 ) . 'ms"><span class="gr-pd-num">' . ( $i + 1 ) . '</span>'
			. '<div class="gr-pd-card"><span class="gr-pd-ic" aria-hidden="true"><i class="fa-regular ' . esc_attr( $ic ) . '"></i></span>'
			. '<div class="gr-pd-ptx"><strong>' . esc_html( $tit ) . '</strong><span>' . wp_kses_post( $ds ) . '</span></div></div></li>';
	}

	/* Caja final: título del panel + texto tras los dos puntos de la frase + enlace de la frase. */
	$caja = '';
	if ( $cierre !== '' && preg_match( '~<a href="([^"]+)"~', $cierre, $ma ) ) {
		$txt = trim( wp_strip_all_tags( $cierre ) );
		$pos = mb_strpos( $txt, ': ' );
		$resto = $pos !== false ? $cap( trim( mb_substr( $txt, $pos + 2 ) ) ) : $txt;
		$caja = '<div class="gr-pd-caja"><span class="gr-pd-caja-ic" aria-hidden="true"><i class="fa-regular fa-calendar"></i></span>'
			. '<p><strong>' . esc_html( $f( 'pd_caja_t', $d['caja_t'] ) ) . '</strong><span>' . esc_html( $resto ) . '</span></p>'
			. '<a class="gr-pd-caja-l" href="' . esc_url( html_entity_decode( $ma[1] ) ) . '">' . esc_html( $f( 'pd_caja_l', $d['caja_l'] ) ) . ' <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div>';
	}

	$foto = $img !== ''
		? '<figure class="gr-pd-foto wow fade-in-right" data-wow-delay="150ms"><img src="' . esc_url( $img ) . '" alt="" loading="lazy" decoding="async">'
			. '<div class="gr-pd-fcard"><span class="gr-pd-fcard-ic" aria-hidden="true"><i class="fa-solid fa-cube"></i></span><p><strong>' . esc_html( $f( 'pd_card_t', $d['card_t'] ) ) . '</strong><span>' . esc_html( $f( 'pd_card', $d['card'] ) ) . '</span></p></div></figure>'
		: '';

	$nueva = '<section class="gr-pseo gr-pseo--' . esc_attr( $slug ) . ' gr-pseo--n2 gr-pd padding-bottom"><div class="container"><div class="gr-pd-grid">' . $foto
		. '<div class="gr-pd-tx"><p class="gr-pd-eye">' . $mh[1] . '</p><h2>' . $mh[2] . '</h2><p class="gr-pd-lead">' . esc_html( $lead ) . '</p>'
		. '<div class="gr-pd-cab"><span class="gr-pd-pill"><i class="fa-solid fa-list-ul" aria-hidden="true"></i> ' . esc_html( $f( 'pd_check_t', $d['check_t'] ) ) . '</span><span class="gr-pd-n">' . count( $mi ) . ' pasos</span></div>'
		. '<ol class="gr-pd-pasos">' . $pasos . '</ol>' . $caja
		. '<a class="gr-pd-btn" href="' . esc_url( $base . '/cotizar/' ) . '">' . esc_html( $f( 'pd_btn', $d['btn'] ) ) . ' <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>'
		. '</div></div></div></section>';
	return substr( $html, 0, $a ) . $nueva . substr( $html, $b + 10 );
}, 49 );
