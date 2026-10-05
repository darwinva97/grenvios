<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Estilo v2: el lenguaje visual de las maquetas del cliente, en todo el sitio
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Las maquetas de la ficha de destino (2026-09-28) marcan la dirección visual:
 * antetítulo en mayúsculas con raya, títulos grandes y apretados, tarjetas
 * blancas con icono en cuadrado rosa y número, fondos rosados suaves, avisos
 * con borde vino y franjas vino de cierre.
 *
 * Este módulo lo aplica a los componentes compartidos por todas las páginas
 * (srv-*, gr-bq-*, dest-seo-*, gr-pseo, gr-nota…) SOLO con CSS
 * (assets/css/gr-v2.css), acotado a `body.gr-v2`. Así:
 *   - no cambia ningún texto ni campo del panel;
 *   - cada página puede volver al diseño anterior desde «Editar página»
 *     (acordeón «🎨 Diseño de esta página» → Clásico), sin tocar código.
 *
 * Skill: .claude/skills/grenvios-v2 (patrones, cómo aplicarlos y verificar).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ¿Qué diseño usa la página actual? 'v2' (por defecto) o 'clasico'. */
function grenvios_estilo_pagina() {
	$v = function_exists( 'grenvios_field' ) ? trim( (string) grenvios_field( 'estilo_pagina', '' ) ) : '';
	return $v === 'clasico' ? 'clasico' : 'v2';
}

add_filter( 'body_class', function ( $classes ) {
	if ( ! is_admin() && grenvios_estilo_pagina() === 'v2' ) $classes[] = 'gr-v2';
	return $classes;
} );

add_action( 'wp_enqueue_scripts', function () {
	$f = get_template_directory() . '/assets/css/gr-v2.css';
	if ( file_exists( $f ) ) {
		$dep = wp_style_is( 'grenvios-bloques', 'registered' ) ? array( 'grenvios-bloques' ) : array();
		wp_enqueue_style( 'grenvios-v2', get_template_directory_uri() . '/assets/css/gr-v2.css', $dep, (string) filemtime( $f ) );
	}
	/* Páginas que venden: secciones con foto, miniaturas, fondos y animación (skill grenvios-landing). */
	$l = get_template_directory() . '/assets/css/gr-landing.css';
	if ( file_exists( $l ) ) {
		wp_enqueue_style( 'grenvios-landing', get_template_directory_uri() . '/assets/css/gr-landing.css', array( 'grenvios-v2' ), (string) filemtime( $l ) );
	}
}, 30 );

/* Panel: selector del diseño de la página (en todas las páginas). */
add_action( 'grenvios_editor_secciones', function ( $slug, $post_id = 0, $render_field = null ) {
	$v = grenvios_estilo_pagina();
	echo '<div class="nep-accordion"><button class="nep-acc-header" type="button"><span>🎨 Diseño de esta página</span><i class="fa-solid fa-chevron-down"></i></button>'
		. '<div class="nep-acc-body"><div class="nep-grid"><div class="nep-field">'
		. '<label for="nep-f-estilo_pagina">Estilo visual</label>'
		. '<span class="nep-hint">«Nuevo» usa el diseño de las maquetas (tarjetas numeradas, fondos rosados, franjas vino). «Clásico» vuelve al anterior solo en esta página. Guarda y recarga para verlo.</span>'
		. '<select id="nep-f-estilo_pagina" data-field-key="estilo_pagina">'
		. '<option value="v2"' . selected( $v, 'v2', false ) . '>Nuevo (maquetas)</option>'
		. '<option value="clasico"' . selected( $v, 'clasico', false ) . '>Clásico</option>'
		. '</select></div></div></div></div>';
}, 99, 3 );
