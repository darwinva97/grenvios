<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Canibalización entre la ficha de /destinos/<país>/ y la de su ruta
 * ══════════════════════════════════════════════════════════════════════════
 *
 * AUDITORÍA (2026-10-02): /destinos/argentina/ y /ar/envios-a-argentina/ son
 * la misma ficha (Polylang las enlaza como traducción) con un 93 % de texto en
 * común, y las dos se declaraban canónicas de sí mismas. Google veía dos URL
 * para «envíos a Argentina» y repartía la autoridad entre ellas. Igual en los
 * nueve países.
 *
 * Los enlaces internos ya llevaban a la de la ruta (inc/paises-rutas.php →
 * grenvios_rutas_reescribir_destinos), así que la buena es esa —y además es
 * de primer nivel dentro de su ruta—. Aquí se completa:
 *
 *   · /destinos/<país>/ declara como canónica la ficha de la ruta del país;
 *   · y sale del sitemap.
 *
 * La página sigue existiendo (el panel, el diseño y los enlaces antiguos
 * funcionan), pero ya no compite. Si un país no tiene ruta propia, su ficha de
 * /destinos/ sigue siendo la canónica.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* URL de la ficha de la ruta para una página /destinos/<país>/ del sitio principal. */
function grenvios_canib_ficha_de( $post_id ) {
	$p = get_post( $post_id );
	if ( ! $p || $p->post_type !== 'page' || ! $p->post_parent ) return '';
	$padre = get_post( $p->post_parent );
	if ( ! $padre || $padre->post_name !== 'destinos' || $padre->post_parent ) return '';
	/* Solo la versión del sitio principal: las copias de cada ruta ya son espejos. */
	if ( function_exists( 'pll_get_post_language' ) && function_exists( 'pll_default_language' ) ) {
		$l = pll_get_post_language( $post_id );
		if ( $l && $l !== pll_default_language() ) return '';
	}
	if ( ! function_exists( 'grenvios_ficha_destino_url' ) ) return '';
	$f = grenvios_ficha_destino_url( $p->post_name );
	return ( $f !== '' && untrailingslashit( $f ) !== untrailingslashit( get_permalink( $post_id ) ) ) ? $f : '';
}

add_filter( 'grenvios_canonical', function ( $url ) {
	if ( ! is_singular( 'page' ) ) return $url;
	$f = grenvios_canib_ficha_de( (int) get_queried_object_id() );
	return $f !== '' ? $f : $url;
}, 30 );

add_filter( 'grenvios_sitemap_urls', function ( $urls ) {
	foreach ( $urls as $i => $u ) {
		if ( ! empty( $u['post_id'] ) && grenvios_canib_ficha_de( (int) $u['post_id'] ) !== '' ) unset( $urls[ $i ] );
	}
	return array_values( $urls );
}, 55 );

/* ══════════════════════════════════════════════════════════════════════════
 *  Blog: una sola versión indexable de cada guía (2026-10-02)
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Medido con 5-gramas y el nombre del país enmascarado:
 *
 *   · Guía general copiada en una ruta («Cómo embalar un paquete… a Ecuador»):
 *     92 % igual a la de Colombia y 71 % a la original. Son la misma guía con
 *     el país en el título → canónica a la original de la ruta principal. Se
 *     siguen viendo en el blog de la ruta (sirven al visitante) pero no
 *     compiten en Google.
 *   · Guía de un país («Qué retiene la aduana de Ecuador»): la maestra vive en
 *     la ruta principal con «-desde-peru» y la copia en /ec/ lleva además el
 *     bloque del país. La buena es la de la ruta del país → la maestra declara
 *     esa canónica.
 *
 * Así cada ruta posiciona solo lo que es suyo (sus guías de país y las locales
 * de inc/blog-paises-locales.php) y la principal, las guías generales.
 */
function grenvios_canib_entrada( $post_id ) {
	$post_id = (int) $post_id;
	if ( get_post_type( $post_id ) !== 'post' || ! function_exists( 'pll_get_post_language' ) ) return '';
	$def  = function_exists( 'pll_default_language' ) ? pll_default_language() : '';
	$lang = (string) pll_get_post_language( $post_id );
	$solo = (string) get_post_meta( $post_id, 'grenvios_solo_pais', true );
	$tr   = function_exists( 'pll_get_post_translations' ) ? pll_get_post_translations( $post_id ) : array();

	if ( $solo !== '' ) {
		/* Guía de país: la maestra apunta a su copia en la ruta del país. */
		if ( $lang !== $def ) return '';
		foreach ( $tr as $l => $tid ) {
			if ( $l === $def || (int) $tid === $post_id ) continue;
			if ( get_post_status( (int) $tid ) === 'publish' ) return get_permalink( (int) $tid );
		}
		return '';
	}
	/* Guía general en una ruta: apunta a la original. */
	if ( $lang === $def || empty( $tr[ $def ] ) ) return '';
	$o = (int) $tr[ $def ];
	return ( $o && $o !== $post_id && get_post_status( $o ) === 'publish' ) ? get_permalink( $o ) : '';
}

add_filter( 'grenvios_canonical', function ( $url ) {
	if ( ! is_singular( 'post' ) ) return $url;
	$c = grenvios_canib_entrada( (int) get_queried_object_id() );
	return $c !== '' ? $c : $url;
}, 30 );

add_filter( 'grenvios_sitemap_urls', function ( $urls ) {
	foreach ( $urls as $i => $u ) {
		if ( ! empty( $u['post_id'] ) && grenvios_canib_entrada( (int) $u['post_id'] ) !== '' ) unset( $urls[ $i ] );
	}
	return array_values( $urls );
}, 56 );
