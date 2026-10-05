<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  El blog, por ruta de país
 * ══════════════════════════════════════════════════════════════════════════
 *
 * El blog de cada ruta salía vacío. Polylang filtra las entradas por «idioma»,
 * así que en `/cu/guias-para-enviar-a-cuba/` no aparecía ninguna: todas las
 * entradas pertenecen a la ruta principal. Un blog vacío en cada país es peor que
 * no tener blog, y además deja sin contenido a la única página de la ruta pensada
 * para atraer tráfico de cola larga.
 *
 * Se resuelve en dos piezas:
 *
 * 1) **Duplicar las entradas a cada ruta**, igual que las páginas, con su bloque
 *    de contenido por país y bajo la misma compuerta: mientras la copia sea
 *    idéntica a su original sale con `noindex` y fuera del sitemap. Una guía de
 *    embalaje copiada nueve veces sin tocar no entra al índice.
 *
 * 2) **Un respaldo para lo que aún no se ha duplicado**: si la ruta no tiene
 *    entradas propias, su blog muestra las del sitio principal en vez de una
 *    página en blanco. Se ve contenido desde el primer día.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ─────────────────────────────────────────────────────────────────────────
 * 1) Duplicar las entradas a una ruta
 * ───────────────────────────────────────────────────────────────────────── */

function grenvios_ruta_posts_maestros() {
	$args = array(
		'post_type'   => 'post',
		'post_status' => array( 'publish', 'draft' ),
		'numberposts' => -1,
	);
	if ( function_exists( 'grenvios_i18n_active' ) && grenvios_i18n_active() ) {
		$args['lang'] = grenvios_i18n_default();
	}
	$out = array();
	foreach ( get_posts( $args ) as $p ) $out[ $p->ID ] = $p;
	return $out;
}

/* Slug de la copia de una entrada. Mismo problema que con las páginas: dos
 * entradas no pueden compartir slug, así que en vez del «-2» que pondría
 * WordPress se le añade el país, que además es la palabra que se busca. */
function grenvios_ruta_post_slug( $base, $lang ) {
	$pais = function_exists( 'grenvios_sede_destino_nombre' ) ? grenvios_sede_destino_nombre( $lang ) : '';
	$pais = sanitize_title( remove_accents( $pais ) );
	if ( $pais === '' ) return $base;
	if ( $base === $pais || substr( $base, -strlen( '-' . $pais ) ) === '-' . $pais ) return $base;
	/* El país ya va dentro («enviar-a-ecuador-en-fechas-clave»): sin esto salía
	 * «…-en-fechas-clave-a-ecuador», con el país dos veces. */
	if ( strpos( '-' . $base . '-', '-' . $pais . '-' ) !== false ) return $base;
	return apply_filters( 'grenvios_ruta_post_slug', $base . '-a-' . $pais, $base, $lang );
}

function grenvios_ruta_duplicar_posts( $lang ) {
	$res = array( 'creadas' => 0, 'saltadas' => 0 );
	if ( ! function_exists( 'pll_set_post_language' ) ) return $res;

	$master = grenvios_i18n_default();
	if ( $lang === $master ) return $res;

	grenvios_ruta_duplicar_terminos( $lang );

	foreach ( grenvios_ruta_posts_maestros() as $id => $p ) {
		if ( (int) pll_get_post( $id, $lang ) ) { $res['saltadas']++; continue; }

		/* Entradas que tratan de UN país (meta `grenvios_solo_pais` = slug del
		 * destino): solo viven en su ruta. Copiar «Enviar encomiendas a
		 * familiares en Cuba» a la ruta de Chile no tendría sentido. Su copia la
		 * crea inc/blog-paises-importar.php; aquí se saltan. */
		$solo = (string) get_post_meta( $id, 'grenvios_solo_pais', true );
		if ( $solo !== '' && ( ! function_exists( 'grenvios_sede_destino_propio' ) || grenvios_sede_destino_propio( $lang ) !== $solo ) ) {
			$res['saltadas']++; continue;
		}

		$nuevo = wp_insert_post( array(
			'post_type'    => 'post',
			'post_status'  => $p->post_status,
			'post_title'   => $p->post_title,
			'post_name'    => grenvios_ruta_post_slug( $p->post_name, $lang ),
			'post_content' => $p->post_content,
			'post_excerpt' => $p->post_excerpt,
		), true );
		if ( is_wp_error( $nuevo ) ) continue;

		foreach ( get_post_meta( $id ) as $k => $v ) {
			if ( strpos( $k, 'grenvios_' ) !== 0 ) continue;
			update_post_meta( $nuevo, $k, maybe_unserialize( $v[0] ) );
		}
		if ( has_post_thumbnail( $id ) ) set_post_thumbnail( $nuevo, get_post_thumbnail_id( $id ) );
		if ( function_exists( 'grenvios_i18n_stamp_master' ) ) grenvios_i18n_stamp_master( $nuevo, $p->post_name );

		// Secciones del país, para que la copia no sea la entrada de Perú con otro
		// prefijo. Usa el conjunto propio de las entradas (ver la matriz).
		if ( function_exists( 'grenvios_pais_aplicar' ) ) {
			grenvios_pais_aplicar( $nuevo, '_post', $lang );
		}

		pll_set_post_language( $nuevo, $lang );

		/* Las categorías se asignan DESPUÉS de fijar el idioma.
		 *
		 * Al revés no funciona: Polylang filtra los términos por el idioma del
		 * post y, mientras el post no tiene idioma, descarta la categoría de la
		 * ruta y deja la del sitio principal. El síntoma era que todas las
		 * categorías de `/cu/` marcaban 0 entradas y su archivo daba 404. */
		$cats = grenvios_ruta_terminos_de( $id, $lang );
		if ( $cats ) wp_set_post_categories( $nuevo, $cats );
		$tr = function_exists( 'pll_get_post_translations' ) ? pll_get_post_translations( $id ) : array();
		$tr[ $master ] = $id;
		$tr[ $lang ]   = $nuevo;
		pll_save_post_translations( $tr );

		$res['creadas']++;
	}
	return $res;
}

/* ─────────────────────────────────────────────────────────────────────────
 * 1 bis) Las categorías también viajan a la ruta
 *
 * Sin esto el filtro de categorías del blog enviaba a `/category/uncategorized/`
 * —el archivo del sitio principal— y sacaba al visitante de su ruta. Y si se le
 * ponía el prefijo a mano, `/cu/category/uncategorized/` daba 404: Polylang solo
 * crea el archivo de una categoría en los idiomas donde esa categoría existe.
 * ───────────────────────────────────────────────────────────────────────── */

function grenvios_ruta_duplicar_terminos( $lang ) {
	if ( ! function_exists( 'pll_set_term_language' ) ) return 0;
	$master = grenvios_i18n_default();
	if ( $lang === $master ) return 0;

	$sufijo = function_exists( 'grenvios_sede_destino_nombre' ) ? grenvios_sede_destino_nombre( $lang ) : '';
	$sufijo = sanitize_title( remove_accents( $sufijo ) );
	if ( $sufijo === '' ) $sufijo = $lang;

	$n = 0;
	foreach ( get_terms( array( 'taxonomy' => 'category', 'hide_empty' => false, 'lang' => $master ) ) as $t ) {
		if ( is_wp_error( $t ) ) continue;
		if ( (int) pll_get_term( $t->term_id, $lang ) ) continue;

		// El slug lleva el país por la misma razón que el de las páginas: dos
		// términos no pueden compartir slug aunque estén en rutas distintas.
		$nuevo = wp_insert_term( $t->name, 'category', array(
			'slug'        => $t->slug . '-' . $sufijo,
			'description' => $t->description,
		) );
		if ( is_wp_error( $nuevo ) ) continue;

		pll_set_term_language( $nuevo['term_id'], $lang );
		$tr = function_exists( 'pll_get_term_translations' ) ? pll_get_term_translations( $t->term_id ) : array();
		$tr[ $master ] = $t->term_id;
		$tr[ $lang ]   = $nuevo['term_id'];
		pll_save_term_translations( $tr );
		$n++;
	}
	return $n;
}

/* Categorías equivalentes en la ruta, para asignárselas a la copia de una entrada. */
function grenvios_ruta_terminos_de( $post_id, $lang ) {
	$out = array();
	if ( ! function_exists( 'pll_get_term' ) ) return $out;
	foreach ( wp_get_post_categories( $post_id ) as $tid ) {
		$t = (int) pll_get_term( $tid, $lang );
		if ( $t ) $out[] = $t;
	}
	return $out;
}

/* ─────────────────────────────────────────────────────────────────────────
 * 2) Respaldo: una ruta sin entradas propias muestra las del sitio principal
 * ───────────────────────────────────────────────────────────────────────── */

function grenvios_ruta_tiene_posts( $lang ) {
	static $cache = array();
	if ( isset( $cache[ $lang ] ) ) return $cache[ $lang ];
	$q = get_posts( array(
		'post_type' => 'post', 'post_status' => 'publish',
		'numberposts' => 1, 'fields' => 'ids', 'lang' => $lang,
	) );
	return $cache[ $lang ] = ! empty( $q );
}

add_action( 'pre_get_posts', function ( $q ) {
	if ( is_admin() || ! $q->is_main_query() ) return;
	if ( ! $q->is_home() && ! $q->is_archive() ) return;
	if ( ! function_exists( 'grenvios_i18n_active' ) || ! grenvios_i18n_active() ) return;

	$lang = grenvios_i18n_current();
	if ( ! $lang || ! function_exists( 'grenvios_es_ruta_pais' ) || ! grenvios_es_ruta_pais( $lang ) ) return;
	if ( grenvios_i18n_default() === $lang ) return;

	/* Solo si la ruta no tiene NINGUNA entrada propia. En cuanto se le duplica o
	 * escribe una, manda el filtro normal de Polylang y el blog pasa a ser el de
	 * ese país: el respaldo no puede mezclar las dos cosas. */
	if ( grenvios_ruta_tiene_posts( $lang ) ) return;
	$q->set( 'lang', '' );
}, 20 );

/* Aviso honesto en el listado: si lo que se está viendo es el respaldo, se dice.
 * Sin esto parece que el país ya tiene su blog escrito. */
add_action( 'grenvios_blog_antes_listado', function () {
	if ( ! function_exists( 'grenvios_i18n_active' ) || ! grenvios_i18n_active() ) return;
	$lang = grenvios_i18n_current();
	if ( ! $lang || grenvios_i18n_default() === $lang ) return;
	if ( ! function_exists( 'grenvios_es_ruta_pais' ) || ! grenvios_es_ruta_pais( $lang ) ) return;
	if ( grenvios_ruta_tiene_posts( $lang ) ) return;

	$pais = function_exists( 'grenvios_sede_destino_nombre' ) ? grenvios_sede_destino_nombre( $lang ) : '';
	if ( $pais === '' ) return;
	echo '<p class="gr-blog-aviso">Estas guías son generales para todos nuestros destinos. '
		. 'Las específicas de ' . esc_html( $pais ) . ' están en camino.</p>';
} );
