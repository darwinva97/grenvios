<?php
/**
 * Grenvíos — Traducción de ENLACES INTERNOS.
 *
 * El diseño trae los enlaces escritos a mano en las plantillas
 * (`HOMEURL/servicios/envio-internacional-de-paquetes/`) y también dentro de los
 * bloques repetibles. Si no se tocan, la versión en inglés enlazaría a URLs
 * españolas: el usuario saltaría de idioma a media navegación y Google vería un
 * sitio con enlaces cruzados entre versiones (uno de los fallos que más daña un
 * SEO multiidioma).
 *
 * Aquí se reescribe CUALQUIER enlace interno a la versión del idioma activo,
 * conservando el ancla (#terrestre) y los parámetros (?guia=123). Si esa página
 * todavía no está traducida, el enlace se queda en español: mejor una página
 * real en otro idioma que un 404.
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;


/* RAÍZ REAL DEL SITIO.
 *
 * No se puede usar home_url() para esto. Con Polylang y prefijo de directorio,
 * en el frontend home_url() devuelve la portada del IDIOMA ACTIVO —y si esa
 * portada es una página, incluye su slug: «/cu/envios-a-cuba»—. Comparando
 * contra eso, un enlace a «/cotizar/» no empieza por la raíz y se descarta como
 * enlace externo, así que se quedaba sin traducir y sacaba al usuario de su
 * ruta. Se lee de la opción, que Polylang no filtra. */
function grenvios_i18n_site_root() {
	static $root = null;
	if ( $root !== null ) return $root;
	$root = untrailingslashit( (string) get_option( 'home' ) );
	if ( $root === '' ) $root = untrailingslashit( network_home_url() );
	return $root;
}

/* Convierte una URL interna a la versión del idioma activo. */
function grenvios_i18n_localize_url( $url, $lang = null ) {
	$url  = (string) $url;
	$lang = $lang ? $lang : grenvios_i18n_current();
	if ( $url === '' || ! grenvios_i18n_active() || grenvios_i18n_is_default( $lang ) ) return $url;

	// Solo enlaces internos (absolutos del propio dominio o relativos).
	$home = grenvios_i18n_site_root();
	if ( preg_match( '#^(mailto:|tel:|javascript:|data:)#i', $url ) ) return $url;
	if ( preg_match( '#^https?://#i', $url ) && strpos( $url, $home ) !== 0 ) return $url;   // externo
	if ( $url === '#' || $url[0] === '#' ) return $url;                                      // ancla interna

	$rel = ( strpos( $url, $home ) === 0 ) ? substr( $url, strlen( $home ) ) : $url;
	$rel = '/' . ltrim( $rel, '/' );

	// Separa ancla y query para reponerlos al final.
	$tail = '';
	foreach ( array( '#', '?' ) as $sep ) {
		$pos = strpos( $rel, $sep );
		if ( $pos !== false ) { $tail = substr( $rel, $pos ) . $tail; $rel = substr( $rel, 0, $pos ); }
	}
	$path = trim( $rel, '/' );

	// Portada
	if ( $path === '' ) {
		$langs = grenvios_i18n_langs();
		return ( isset( $langs[ $lang ] ) ? untrailingslashit( $langs[ $lang ]['url'] ) : $home ) . '/' . $tail;
	}

	/* Si ya viene con prefijo de ruta, se respeta TAL CUAL.
	 *
	 * Antes se le quitaba el prefijo y se volvía a resolver contra el idioma
	 * activo, así que un enlace deliberado a otra ruta —la tabla de plazos, que
	 * enlaza Argentina, Chile, España…— acababa apuntando a la ruta actual: en
	 * /cu/ las cinco filas iban a /cu/envios-a-cuba/. Un enlace que ya nombra su
	 * ruta es intencionado y no se toca. */
	$parts = explode( '/', $path );
	if ( isset( grenvios_i18n_langs()[ $parts[0] ] ) ) return $url;

	$tid = grenvios_i18n_page_translation_by_path( $path, $lang );
	if ( ! $tid ) return $url;   // aún sin traducir: se deja el enlace español

	return untrailingslashit( get_permalink( $tid ) ) . '/' . $tail;
}

/* Traducción de la página cuya ruta española es $path (cacheado por petición). */
function grenvios_i18n_page_translation_by_path( $path, $lang ) {
	static $cache = array();
	$k = $lang . '|' . $path;
	if ( isset( $cache[ $k ] ) ) return $cache[ $k ];

	$page = get_page_by_path( $path );
	if ( ! $page ) return $cache[ $k ] = 0;

	// Si la ruta ya apuntaba a una traducción, se resuelve por su maestra.
	$master = grenvios_i18n_master_id( $page->ID );
	$tid    = grenvios_i18n_translation_id( $master, $lang );
	if ( $tid && get_post_status( $tid ) !== 'publish' ) $tid = 0;

	return $cache[ $k ] = (int) $tid;
}

/* Reescribe TODOS los href internos de un bloque de HTML a la ruta activa. */
/* Los enlaces con `hreflang` (el selector de país) apuntan a propósito a OTRA
 * ruta y no se tocan: sin esta excepción, «Perú» —la raíz del sitio— se
 * reescribía a la portada de la ruta activa y en /cu/ volvía a /cu/. */
function grenvios_i18n_localize_html( $html ) {
	if ( ! is_string( $html ) || $html === '' ) return $html;
	if ( is_admin() || grenvios_i18n_is_default() || ! grenvios_i18n_active() ) return $html;
	if ( function_exists( 'grenvios_cache_html' ) ) {
		return grenvios_cache_html( 'i18n-links', $html, grenvios_i18n_current(), 'grenvios_i18n_localize_html_raw' );
	}
	return grenvios_i18n_localize_html_raw( $html );
}

/* El trabajo de verdad, sin caché: reescribe cada enlace a la URL de la ruta. */
function grenvios_i18n_localize_html_raw( $html ) {
	$home = preg_quote( grenvios_i18n_site_root(), '#' );
	return preg_replace_callback(
		'#(href=")(' . $home . '/[^"]*|/[^"]*)(")(?!\s+hreflang=)#i',
		function ( $m ) { return $m[1] . esc_url( grenvios_i18n_localize_url( $m[2] ) ) . $m[3]; },
		$html
	);
}

/* ── 1) Enlaces escritos en las plantillas del diseño ── */
add_filter( 'grenvios_partial_html', 'grenvios_i18n_localize_html', 8 );
/* Y el mismo trabajo sobre el HTML que los renders data-driven (país, FAQ, blog,
 * CTA, enlazado interno) imprimen directamente sin pasar por un partial: sin esto
 * sus enlaces salían sin traducir y sacaban al usuario de su ruta. */
add_filter( 'grenvios_html_final', 'grenvios_i18n_localize_html', 8 );

/* ── 2) Enlaces de los bloques repetibles (tarjetas, botones, listas) ── */
add_filter( 'grenvios_rep_link', 'grenvios_i18n_localize_url', 10, 1 );

/* ── 3) Enlaces escritos dentro del contenido (guías y páginas editables) ──
 *      Una guía traducida enlazaría a las páginas españolas si no se reescriben.
 *      Se hace sobre el HTML ya renderizado, no sobre lo guardado, para que el
 *      texto original en español siga intacto en la base de datos. */
add_filter( 'the_content', function ( $html ) {
	if ( is_admin() || grenvios_i18n_is_default() || ! grenvios_i18n_active() ) return $html;
	$home = preg_quote( grenvios_i18n_site_root(), '#' );
	return preg_replace_callback(
		'#(href=")(' . $home . '/[^"]*|/[^"]*)(")(?!\s+hreflang=)#i',
		function ( $m ) { return $m[1] . esc_url( grenvios_i18n_localize_url( $m[2] ) ) . $m[3]; },
		$html
	);
}, 30 );

/* ── 4) Menús de WordPress: Polylang ya sirve el menú del idioma, pero si la
 *      clienta reutiliza el mismo menú en todos los idiomas, los enlaces se
 *      corrigen igualmente. ── */
add_filter( 'nav_menu_link_attributes', function ( $atts ) {
	if ( ! empty( $atts['href'] ) ) $atts['href'] = grenvios_i18n_localize_url( $atts['href'] );
	return $atts;
}, 20 );
