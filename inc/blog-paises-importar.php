<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Importador de las guías por país
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Para cada destino del gestor y cada plantilla de inc/blog-paises-contenido.php:
 *
 *   1) Maestra en la ruta principal: categoría, página de dinero (la ficha
 *      /destinos/<país>/ o la página de servicio que corresponda) y la marca
 *      `grenvios_solo_pais`, que impide que el duplicador general la copie a
 *      las otras rutas.
 *   2) Copia SOLO en la ruta de ese país, con su categoría allí, su página de
 *      dinero allí y el bloque por país al final. Con Polylang las dos quedan
 *      enlazadas como traducciones, así que el selector de país y los
 *      sitemaps las tratan como cualquier otra entrada.
 *
 * Idempotente: se relanza tras retocar una plantilla o una nota y actualiza
 * las 2 versiones de cada guía. grenvios_guias_pais_importar().
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Ruta (lang) cuyo destino propio es este slug, o ''. */
function grenvios_bp_lang_de( $slug ) {
	if ( ! function_exists( 'grenvios_i18n_langs' ) || ! function_exists( 'grenvios_sede_destino_propio' ) ) return '';
	foreach ( grenvios_i18n_langs() as $l => $_ ) {
		if ( function_exists( 'grenvios_es_ruta_pais' ) && ! grenvios_es_ruta_pais( $l ) ) continue;
		if ( grenvios_sede_destino_propio( $l ) === $slug ) return $l;
	}
	return '';
}

/* Sufijo del país de origen para el slug del maestro: «desde-peru». */
function grenvios_bp_origen_sufijo() {
	$pais = function_exists( 'grenvios_sede_tokens_apply' ) ? grenvios_sede_tokens_apply( '{{origen_pais}}' ) : 'Peru';
	$pais = sanitize_title( remove_accents( $pais ) );
	return $pais !== '' ? 'desde-' . $pais : 'desde-origen';
}

/* Localiza una guía por su clave, no por su slug.
 *
 * El maestro y la copia de ruta ya no comparten slug (ver abajo), así que
 * buscar por slug dejaría de encontrar al maestro en cuanto se le renombra.
 * La clave —el slug «lógico» de la plantilla— se guarda como meta en las dos
 * versiones y no cambia nunca. */
function grenvios_guia_por_clave( $clave, $lang ) {
	$posts = get_posts( array(
		'post_type'        => 'post',
		'post_status'      => array( 'publish', 'draft', 'pending', 'private' ),
		'meta_key'         => 'grenvios_guia_key',
		'meta_value'       => $clave,
		'numberposts'      => 20,
		'suppress_filters' => false,
		'lang'             => '',
	) );
	foreach ( $posts as $p ) {
		if ( ! function_exists( 'pll_get_post_language' ) ) return $p;
		if ( (string) pll_get_post_language( $p->ID ) === $lang ) return $p;
	}
	return null;
}

/* $solo: claves de plantilla a procesar (vacío = todas). Permite rehacer unas
 * pocas guías sin reescribir las demás. */
function grenvios_guias_pais_importar( $solo = array() ) {
	if ( ! function_exists( 'grenvios_destinos' ) || ! function_exists( 'grenvios_bp_plantillas' ) ) return array( 'error' => 'faltan módulos' );
	if ( function_exists( 'grenvios_slug_libre' ) ) grenvios_slug_libre( true );

	$res     = array( 'maestras_creadas' => 0, 'maestras_actualizadas' => 0, 'copias_creadas' => 0, 'copias_actualizadas' => 0, 'sin_ruta' => array() );
	$multi   = function_exists( 'grenvios_i18n_active' ) && grenvios_i18n_active() && function_exists( 'pll_get_post' );
	$default = $multi ? grenvios_i18n_default() : '';
	$home    = untrailingslashit( home_url() );
	$autor   = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
	$autor   = $autor ? (int) $autor[0] : 1;
	$notas   = grenvios_bp_notas();
	/* Título y extracto se guardan ya resueltos: aparecen en listados y tarjetas,
	 * donde el filtro de tokens de the_content no llega. */
	$tok = function ( $t ) { return function_exists( 'grenvios_sede_tokens_apply' ) ? grenvios_sede_tokens_apply( $t ) : $t; };

	foreach ( grenvios_destinos() as $slug => $dest ) {
		$d = function_exists( 'grenvios_pais_datos' ) ? grenvios_pais_datos( $slug ) : array();
		if ( ! $d ) continue;
		$n = isset( $notas[ $slug ] ) ? $notas[ $slug ] : array( 'quien' => 'familias, estudiantes y negocios', 'via' => '', 'consejo' => '' );
		$lang = $multi ? grenvios_bp_lang_de( $slug ) : '';
		if ( $multi && $lang === '' ) $res['sin_ruta'][] = $slug;

		foreach ( grenvios_bp_plantillas( $d, $n ) as $pslug => $g ) {
			if ( $solo && ! in_array( $pslug, (array) $solo, true ) ) continue;
			$html  = str_replace( '%H%', $home, trim( $g['html'] ) );
			$pilar = function_exists( 'grenvios_guia_pilar_id' ) ? grenvios_guia_pilar_id( $g['pilar'] ) : 0;
			$cat   = get_term_by( 'slug', $g['categoria'], 'category' );

			/* ── Slugs: el maestro y la copia NO pueden coincidir ─────
			 *
			 * En siete de las nueve plantillas el slug ya termina con el país
			 * («cuanto-demora-un-envio-a-ecuador»), así que la copia de /ec/
			 * quería el mismo slug que el maestro. Con la misma fecha, la URL
			 * casaba con las DOS entradas y la plantilla pintaba el artículo
			 * dos veces, con dos H1. El maestro pasa a llevar el sufijo del
			 * país de origen —«…-desde-peru»—, que además es como se busca
			 * desde aquí; la copia se queda con el slug limpio, que ya va
			 * diferenciado por el prefijo /ec/ de su ruta. */
			/* 'slug' propio: la clave no cambia nunca (la guardan las dos
			 * versiones), pero el slug público puede apuntar a otra búsqueda. */
			$base_slug    = ! empty( $g['slug'] ) ? $g['slug'] : $pslug;
			$copia_slug   = ( $multi && $lang !== '' && function_exists( 'grenvios_ruta_post_slug' ) ) ? grenvios_ruta_post_slug( $base_slug, $lang ) : $base_slug;
			$maestro_slug = ( $copia_slug === $base_slug ) ? $base_slug . '-' . grenvios_bp_origen_sufijo() : $base_slug;

			/* ── Maestra ────────────────────────────────────────────── */
			$m = grenvios_guia_por_clave( $pslug, $default );
			if ( ! $m && function_exists( 'grenvios_guia_maestra' ) ) {
				$m = grenvios_guia_maestra( $maestro_slug );
				if ( ! $m ) $m = grenvios_guia_maestra( $pslug );   // primera migración
			}
			$datos = array(
				'post_type' => 'post', 'post_status' => 'publish', 'post_author' => $autor,
				'post_title' => $tok( $g['titulo'] ), 'post_name' => $maestro_slug,
				'post_content' => $html, 'post_excerpt' => $tok( $g['extracto'] ),
			);
			if ( $m ) {
				$datos['ID'] = $m->ID;
				unset( $datos['post_name'] );   // igual que arriba: el slug se fija aparte
				wp_update_post( $datos );
				$mid = (int) $m->ID;
				grenvios_fijar_slug( $mid, $maestro_slug, $default );
				$res['maestras_actualizadas']++;
			}
			else {
				$mid = (int) wp_insert_post( $datos );
				if ( ! $mid ) continue;
				if ( $multi ) pll_set_post_language( $mid, $default );
				$res['maestras_creadas']++;
			}
			update_post_meta( $mid, 'grenvios_solo_pais', $slug );
			update_post_meta( $mid, 'grenvios_guia_key', $pslug );
			if ( $cat )   wp_set_post_categories( $mid, array( (int) $cat->term_id ) );
			if ( $pilar ) update_post_meta( $mid, GRENVIOS_CLUSTER_META, $pilar );

			/* ── Copia en SU ruta ───────────────────────────────────── */
			if ( ! $multi || $lang === '' ) continue;
			$tid = (int) pll_get_post( $mid, $lang );
			if ( ! $tid ) { $tc = grenvios_guia_por_clave( $pslug, $lang ); if ( $tc ) $tid = (int) $tc->ID; }
			$dc  = array(
				'post_type' => 'post', 'post_status' => 'publish', 'post_author' => $autor,
				'post_title' => $tok( $g['titulo'] ), 'post_content' => $html, 'post_excerpt' => $tok( $g['extracto'] ),
			);
			$deseado = $copia_slug;
			if ( $tid ) { $dc['ID'] = $tid; wp_update_post( $dc ); $res['copias_actualizadas']++; }
			else {
				/* Se inserta con un slug provisional y se renombra DESPUÉS de fijar el
				 * idioma: hasta entonces WordPress no sabe que la copia es de otra
				 * ruta, ve el slug ocupado por la maestra y le cuelga un «-2». */
				$dc['post_name'] = $deseado . '-tmp-' . $lang;
				$tid = (int) wp_insert_post( $dc );
				if ( ! $tid ) continue;
				pll_set_post_language( $tid, $lang );
				if ( function_exists( 'grenvios_i18n_stamp_master' ) ) grenvios_i18n_stamp_master( $tid, $pslug );
				$tr = pll_get_post_translations( $mid ); $tr[ $default ] = $mid; $tr[ $lang ] = $tid;
				pll_save_post_translations( $tr );
				$res['copias_creadas']++;
			}
			// Slug limpio (también repara las copias creadas antes con «-2»).
			grenvios_fijar_slug( $tid, $deseado, $lang );
			update_post_meta( $tid, 'grenvios_solo_pais', $slug );
			update_post_meta( $tid, 'grenvios_guia_key', $pslug );
			if ( function_exists( 'grenvios_ruta_terminos_de' ) ) {
				$cats = grenvios_ruta_terminos_de( $mid, $lang );
				if ( $cats ) wp_set_post_categories( $tid, $cats );
			}
			$pt = ( $pilar && function_exists( 'grenvios_i18n_translation_id' ) ) ? (int) grenvios_i18n_translation_id( $pilar, $lang ) : 0;
			if ( $pt ) update_post_meta( $tid, GRENVIOS_CLUSTER_META, $pt );
			if ( function_exists( 'grenvios_pais_aplicar' ) ) grenvios_pais_aplicar( $tid, '_post', $lang );
		}
	}

	if ( function_exists( 'grenvios_slug_libre' ) ) grenvios_slug_libre( false );

	global $wpdb;
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_gr_rel_%' OR option_name LIKE '_transient_timeout_gr_rel_%'" );
	return $res;
}
