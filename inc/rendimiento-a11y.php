<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Rendimiento, accesibilidad e imagen para compartir (2026-10-02)
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Revisión con .claude/skills/grenvios-diseno/scripts/a11y-perf.py sobre una
 * muestra de cada tipo de página:
 *
 *   SEO   og:image era el relleno slider-bg.jpg en casi todo el sitio.
 *   PERF  17 hojas de estilo bloqueantes; 6–47 imágenes sin width/height
 *         por página (saltos de maquetación, CLS).
 *   A11Y  botones de búsqueda y «volver arriba» sin nombre, campos sin
 *         etiqueta (buscador, medidas del cotizador), flecha de paginación sin
 *         texto.
 *
 * Todo se hace sobre el HTML final (`grenvios_html_final`) con patrones
 * exactos, sin tocar las plantillas de la maqueta.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ─────────────────────────────────────────────────────────────────────────
 * 1) Imagen para compartir: nunca el relleno
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_ra_es_relleno( $u ) {
	if ( function_exists( 'grenvios_ej_es_relleno' ) && grenvios_ej_es_relleno( $u ) ) return true;
	return (bool) preg_match( '~/(slider-bg|page-banner|content-bg[^/]*|post-\d+[^/]*|hero-background[^/]*)\.(jpe?g|png|webp)~i', (string) $u );
}

/* Foto de ejemplo por el tema de una página o entrada. */
function grenvios_ra_img_de( $id ) {
	$id = (int) $id;
	if ( $id && get_post_type( $id ) === 'post' && function_exists( 'grenvios_bd_img' ) ) return grenvios_bd_img( $id );
	if ( function_exists( 'grenvios_ej_por_tema' ) ) {
		$t = $id ? get_the_title( $id ) . ' ' . get_post_field( 'post_name', $id ) : '';
		return grenvios_ej_por_tema( $t, 'almacen' );
	}
	return '';
}

/* og:image / twitter:image: la de la página si es real; si no, la del tema. */
add_filter( 'grenvios_og_image', function ( $u ) {
	if ( $u && ! grenvios_ra_es_relleno( $u ) ) return $u;
	$id = is_singular() ? (int) get_queried_object_id() : 0;
	$r  = grenvios_ra_img_de( $id );
	return $r ? $r : $u;
} );

/* Sitemap de imágenes: lo mismo, por ID. */
add_filter( 'grenvios_sitemap_image', function ( $u, $post_id ) {
	if ( $u && ! grenvios_ra_es_relleno( $u ) ) return $u;
	$r = grenvios_ra_img_de( $post_id );
	return $r ? $r : $u;
}, 10, 2 );

/* ─────────────────────────────────────────────────────────────────────────
 * 2) CSS no crítico sin bloquear el render
 *    Animaciones (las dispara WOW al hacer scroll), contador, lightbox y el
 *    estilo de los selects: nada de eso se ve en la primera pantalla.
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_ra_css_diferido() {
	return array( 'logisko-animate', 'logisko-keyframe', 'logisko-odometer', 'logisko-venobox', 'logisko-nice-select' );
}
add_filter( 'style_loader_tag', function ( $tag, $handle ) {
	if ( is_admin() || ! in_array( $handle, grenvios_ra_css_diferido(), true ) ) return $tag;
	if ( strpos( $tag, 'onload=' ) !== false ) return $tag;
	$async = preg_replace( "~media=(['\"])[^'\"]*\\1~", "media='print' onload=\"this.media='all';this.onload=null\"", $tag, 1 );
	if ( ! is_string( $async ) || $async === $tag ) return $tag;
	/* La copia de <noscript> sin id: dos elementos no pueden compartirlo. */
	$sin_id = preg_replace( '~\sid=([\'"])[^\'"]*\1~', '', $tag, 1 );
	return $async . '<noscript>' . trim( is_string( $sin_id ) ? $sin_id : $tag ) . '</noscript>' . "\n";
}, 10, 2 );

/* ─────────────────────────────────────────────────────────────────────────
 * 3) width/height en las imágenes locales (CLS)
 *    Las medidas se leen del archivo una vez y se guardan en una opción.
 *    `:where(img){height:auto}` (abajo) mantiene la proporción cuando el CSS
 *    fija el ancho: es lo mismo que hace WordPress con sus imágenes.
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_ra_dims( $src ) {
	static $cache = null, $sucio = false;
	if ( $cache === null ) {
		$cache = get_option( 'grenvios_img_dims', array() );
		if ( ! is_array( $cache ) ) $cache = array();
		add_action( 'shutdown', function () use ( &$cache, &$sucio ) { if ( $sucio ) update_option( 'grenvios_img_dims', $cache, false ); } );
	}
	$home = untrailingslashit( get_option( 'home' ) );
	$src  = html_entity_decode( (string) $src );
	if ( strpos( $src, $home . '/wp-content/' ) !== 0 ) return null;
	$rel  = strtok( substr( $src, strlen( $home ) ), '?' );
	if ( array_key_exists( $rel, $cache ) ) return $cache[ $rel ];
	$file = ABSPATH . ltrim( $rel, '/' );
	$dim  = null;
	if ( is_file( $file ) ) {
		if ( preg_match( '~\.svg$~i', $file ) ) {
			$svg = (string) @file_get_contents( $file, false, null, 0, 2048 );
			if ( preg_match( '~viewBox=["\'][\d.\-]+\s+[\d.\-]+\s+([\d.]+)\s+([\d.]+)~', $svg, $m ) ) $dim = array( (int) round( $m[1] ), (int) round( $m[2] ) );
		} else {
			$i = @getimagesize( $file );
			if ( $i && $i[0] && $i[1] ) $dim = array( (int) $i[0], (int) $i[1] );
		}
	}
	$cache[ $rel ] = $dim; $sucio = true;
	return $dim;
}

function grenvios_ra_img_dims_html( $html ) {
	if ( ! is_string( $html ) || stripos( $html, '<img' ) === false ) return $html;
	$r = preg_replace_callback( '~<img\b[^>]*>~i', function ( $m ) {
		$tag = $m[0];
		if ( preg_match( '~\swidth=~i', $tag ) && preg_match( '~\sheight=~i', $tag ) ) return $tag;
		if ( ! preg_match( '~\ssrc=(["\'])([^"\']+)\1~i', $tag, $s ) ) return $tag;
		$d = grenvios_ra_dims( $s[2] );
		if ( ! $d ) return $tag;
		$add = '';
		if ( ! preg_match( '~\swidth=~i', $tag ) )  $add .= ' width="' . $d[0] . '"';
		if ( ! preg_match( '~\sheight=~i', $tag ) ) $add .= ' height="' . $d[1] . '"';
		return preg_replace( '~\s*/?>$~', $add . '$0', $tag, 1 );
	}, $html );
	return is_string( $r ) ? $r : $html;
}

/* ─────────────────────────────────────────────────────────────────────────
 * 4) Nombres accesibles que faltaban
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_ra_a11y_html( $html ) {
	if ( ! is_string( $html ) || $html === '' ) return $html;
	$fijos = array(
		'<button id="popup-search-button" type="submit" name="submit">' => '<button id="popup-search-button" type="submit" name="submit" aria-label="Buscar">',
		'<button id="scroll-top" class="scroll-to-top">'                 => '<button id="scroll-top" class="scroll-to-top" aria-label="Volver arriba">',
		'<input id="popup-search" type="text" name="s"'                  => '<input id="popup-search" type="text" name="s" aria-label="Buscar en el sitio"',
	);
	$html = strtr( $html, $fijos );

	/* Campos con placeholder y sin etiqueta asociada: el placeholder como nombre. */
	$r = preg_replace_callback( '~<(input|textarea)\b([^>]*)>~i', function ( $m ) use ( $html ) {
		$at = $m[2];
		if ( preg_match( '~\s(aria-label|aria-labelledby|title)=~i', $at ) ) return $m[0];
		if ( preg_match( '~type=["\'](hidden|submit|button|checkbox|radio)~i', $at ) ) return $m[0];
		if ( ! preg_match( '~placeholder=(["\'])([^"\']+)\1~i', $at, $ph ) ) return $m[0];
		if ( preg_match( '~\sid=(["\'])([^"\']+)\1~i', $at, $id ) && strpos( $html, 'for="' . $id[2] . '"' ) !== false ) return $m[0];
		return '<' . $m[1] . ' aria-label="' . esc_attr( html_entity_decode( $ph[2] ) ) . '"' . $at . '>';
	}, $html );
	if ( is_string( $r ) ) $html = $r;

	/* Paginación con solo flecha. */
	$html = str_replace(
		array( '<a class="next page-numbers"', '<a class="prev page-numbers"' ),
		array( '<a aria-label="Página siguiente" class="next page-numbers"', '<a aria-label="Página anterior" class="prev page-numbers"' ),
		$html
	);
	return $html;
}

/* ─────────────────────────────────────────────────────────────────────────
 * Aplicación a la página ENTERA
 *    `grenvios_html_final` solo recibe trozos de cada plantilla (el listado
 *    del blog y la entrada quedaban fuera), así que estas dos correcciones van
 *    en un búfer propio que ve todo el documento.
 * ───────────────────────────────────────────────────────────────────────── */
add_action( 'template_redirect', function () {
	if ( is_admin() || is_feed() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) return;
	if ( get_query_var( 'grenvios_sitemap' ) ) return;
	ob_start( function ( $html ) {
		if ( ! is_string( $html ) || stripos( $html, '<html' ) === false ) return $html;
		return grenvios_ra_a11y_html( grenvios_ra_img_dims_html( $html ) );
	} );
}, 1 );

/* ─────────────────────────────────────────────────────────────────────────
 * 5) CSS mínimo: proporción de imágenes y texto solo para lectores de pantalla
 * ───────────────────────────────────────────────────────────────────────── */
add_action( 'wp_head', function () {
	echo '<style id="gr-ra">:where(img){height:auto}.screen-reader-text{position:absolute!important;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border:0}</style>' . "\n";
}, 99 );
