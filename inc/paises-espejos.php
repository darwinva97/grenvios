<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Páginas espejo: Destinos, sus fichas y «Artículos por país» en cada ruta
 * ══════════════════════════════════════════════════════════════════════════
 *
 * El duplicador deja fuera el árbol de Destinos y el índice de artículos, y en
 * el listado de páginas de WordPress esas once páginas salían con «+» en todas
 * las columnas de país: sin versión en ninguna ruta.
 *
 * El motivo de dejarlas fuera sigue en pie. Dentro de /cu/, la ficha «Chile» es
 * una página de envíos a Chile, igual que /cl/envios-a-chile/ y que
 * /destinos/chile/. Como páginas normales serían 99 duplicados (11 × 9 rutas)
 * compitiendo con las buenas por la misma búsqueda.
 *
 * Así que se crean como ESPEJOS:
 *
 *   · Existen en cada ruta, enlazadas como traducción: desaparecen los «+» y la
 *     ruta queda completa y editable.
 *   · Su canónica apunta a la página real de ese destino: la portada de la ruta
 *     del país si existe, si no la ficha del sitio principal. Google consolida
 *     en la buena en vez de repartir.
 *   · Quedan fuera del sitemap.
 *   · Los menús y enlaces NO las usan: el buscador de traducciones del tema las
 *     ignora, así que «Chile» en el menú de /cu/ sigue llevando a /cl/.
 *   · No reciben bloque de contenido por país ni cabecera de ruta: en /cu/ la
 *     ficha de Chile habla de Chile, no de Cuba.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

const GRENVIOS_ESPEJO_META = '_grenvios_espejo';   // ID de la página maestra

function grenvios_espejo_es( $post_id ) {
	return $post_id && (int) get_post_meta( (int) $post_id, GRENVIOS_ESPEJO_META, true ) > 0;
}

/* Páginas maestras que se espejan: el hub, sus hijas y el índice de artículos. */
function grenvios_espejo_maestras() {
	$out = array();
	$hub = get_page_by_path( 'destinos' );
	if ( $hub ) {
		$out[] = $hub;
		foreach ( get_pages( array( 'child_of' => $hub->ID, 'post_status' => array( 'publish', 'draft' ), 'sort_column' => 'menu_order' ) ) as $h ) {
			$out[] = $h;
		}
	}
	if ( defined( 'GRENVIOS_HUB_BLOG_SLUG' ) ) {
		$art = get_page_by_path( GRENVIOS_HUB_BLOG_SLUG );
		if ( $art ) $out[] = $art;
	}
	return apply_filters( 'grenvios_espejo_maestras', $out );
}

function grenvios_ruta_duplicar_espejos( $lang ) {
	$res = array( 'creadas' => 0, 'saltadas' => 0 );
	if ( ! function_exists( 'pll_set_post_language' ) ) return $res;
	$master = grenvios_i18n_default();
	if ( $lang === $master ) return $res;

	$pais = function_exists( 'grenvios_sede_destino_nombre' ) ? grenvios_sede_destino_nombre( $lang ) : '';
	$pais = sanitize_title( remove_accents( $pais ) );
	if ( $pais === '' ) $pais = $lang;

	$hub     = get_page_by_path( 'destinos' );
	$hub_id  = $hub ? (int) $hub->ID : 0;
	$hub_cp  = $hub_id ? (int) pll_get_post( $hub_id, $lang ) : 0;

	foreach ( grenvios_espejo_maestras() as $p ) {
		if ( (int) pll_get_post( $p->ID, $lang ) ) { $res['saltadas']++; continue; }

		/* Slugs: el hub y el índice necesitan uno propio porque están en la raíz,
		 * donde dos páginas no pueden compartir slug aunque sean de rutas
		 * distintas. Las fichas cuelgan del hub de su ruta, así que conservan el
		 * suyo: /cu/destinos-cuba/chile/. */
		$parent = 0;
		if ( $p->ID === $hub_id ) {
			$slug = 'destinos-' . $pais;
		} elseif ( $hub_id && (int) $p->post_parent === $hub_id ) {
			if ( ! $hub_cp ) $hub_cp = (int) pll_get_post( $hub_id, $lang );
			if ( ! $hub_cp ) { $res['saltadas']++; continue; }   // el hub va primero
			$parent = $hub_cp;
			$slug   = $p->post_name;
		} else {
			$slug = $p->post_name . '-' . $pais;
		}

		$nuevo = wp_insert_post( array(
			'post_type'    => 'page',
			'post_status'  => $p->post_status,
			'post_title'   => $p->post_title,
			'post_name'    => $slug,
			'post_content' => $p->post_content,
			'post_excerpt' => $p->post_excerpt,
			'post_parent'  => $parent,
			'menu_order'   => $p->menu_order,
		), true );
		if ( is_wp_error( $nuevo ) ) continue;

		foreach ( get_post_meta( $p->ID ) as $k => $v ) {
			if ( strpos( $k, 'grenvios_' ) !== 0 ) continue;
			update_post_meta( $nuevo, $k, maybe_unserialize( $v[0] ) );
		}
		update_post_meta( $nuevo, GRENVIOS_ESPEJO_META, (int) $p->ID );
		if ( function_exists( 'grenvios_i18n_stamp_master' ) ) grenvios_i18n_stamp_master( $nuevo, $p->post_name );

		pll_set_post_language( $nuevo, $lang );
		$tr = function_exists( 'pll_get_post_translations' ) ? pll_get_post_translations( $p->ID ) : array();
		$tr[ $master ] = $p->ID;
		$tr[ $lang ]   = $nuevo;
		pll_save_post_translations( $tr );

		if ( $p->ID === $hub_id ) $hub_cp = (int) $nuevo;
		$res['creadas']++;
	}
	if ( $res['creadas'] ) flush_rewrite_rules( false );
	return $res;
}

/* URL real a la que consolida un espejo. */
function grenvios_espejo_canonica( $post_id ) {
	$m = (int) get_post_meta( (int) $post_id, GRENVIOS_ESPEJO_META, true );
	if ( ! $m ) return '';
	$mp = get_post( $m );
	if ( ! $mp ) return '';

	$hub = get_page_by_path( 'destinos' );
	if ( $hub && (int) $mp->post_parent === (int) $hub->ID ) {
		// Ficha de un país: su página de destino en la ruta de ese país.
		if ( function_exists( 'grenvios_ficha_destino_url' ) ) {
			$f = grenvios_ficha_destino_url( $mp->post_name );
			if ( $f !== '' && untrailingslashit( $f ) !== untrailingslashit( get_permalink( (int) $post_id ) ) ) return $f;
		}
		// Respaldo: la portada de la ruta de ese país.
		if ( function_exists( 'grenvios_sedes' ) && function_exists( 'grenvios_sede_destino_propio' ) ) {
			foreach ( grenvios_sedes() as $l => $s ) {
				if ( grenvios_sede_destino_propio( $l ) === $mp->post_name && ! empty( $s['url'] ) ) return $s['url'];
			}
		}
	}
	return get_permalink( $mp );
}

/* ─────────────────────────────────────────────────────────────────────────
 * Integración con el resto del tema
 * ───────────────────────────────────────────────────────────────────────── */

// Canónica hacia la página real.
add_filter( 'grenvios_canonical', function ( $url ) {
	if ( ! is_singular( 'page' ) ) return $url;
	$id = (int) get_queried_object_id();
	if ( ! grenvios_espejo_es( $id ) ) return $url;
	$c = grenvios_espejo_canonica( $id );
	return $c !== '' ? $c : $url;
} );

// Fuera del sitemap.
add_filter( 'grenvios_sitemap_urls', function ( $urls ) {
	foreach ( $urls as $i => $u ) {
		if ( ! empty( $u['post_id'] ) && grenvios_espejo_es( (int) $u['post_id'] ) ) unset( $urls[ $i ] );
	}
	return array_values( $urls );
}, 50 );

// Tras duplicar una ruta, se crean sus espejos.
add_action( 'grenvios_ruta_duplicada', 'grenvios_ruta_duplicar_espejos', 20 );
