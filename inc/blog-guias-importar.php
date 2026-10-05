<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Importador de guías del blog + enlaces entre guías
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Toma las guías de inc/blog-guias-contenido.php y las lleva a WordPress:
 *
 *   1) Crea o actualiza la entrada MAESTRA de cada guía (ruta principal), con
 *      su categoría y su página de dinero (clúster).
 *   2) Sincroniza las copias de cada ruta de país: mismo texto, su categoría en
 *      esa ruta, su página de dinero EN ESA RUTA y el bloque por país al final,
 *      que es lo que hace que la copia de Chile no sea la de Perú con otro
 *      prefijo (inc/paises-contenido.php, conjunto `_post`).
 *   3) Crea las copias que falten con el duplicador de rutas del tema.
 *
 * Es idempotente: se puede ejecutar tantas veces como se retoque un texto.
 * Se lanza desde WP-CLI/PHP: grenvios_guias_importar().
 *
 * ENLACES ENTRE GUÍAS: en el texto se escriben como %P:slug-de-la-guia% y se
 * resuelven al pintar contra la versión de la guía en la ruta activa. Un
 * enlace fijo a /2026/09/22/… se rompería en las rutas, donde la copia tiene
 * otro slug (…-a-chile) y otra fecha.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Entrada maestra de una guía por su slug (cualquier estado), o null.
 *
 * El idioma se comprueba a mano y no con el argumento `lang` de la consulta:
 * ese filtro lo pone Polylang al arrancar el frontend y NO está activo cuando
 * la importación corre por CLI. Con `lang` a secas, una guía cuyo maestro y
 * copia comparten slug —«que-se-puede-enviar-a-venezuela» en la ruta principal
 * y en /ve/— devolvía la copia, y el importador actualizaba la copia creyendo
 * que era el maestro: el maestro se quedaba con el texto viejo. */
function grenvios_guia_maestra( $slug ) {
	$posts = get_posts( array(
		'post_type'        => 'post',
		'post_status'      => array( 'publish', 'draft', 'pending', 'private' ),
		'name'             => $slug,
		'numberposts'      => 20,
		'suppress_filters' => false,
		'lang'             => '',          // sin filtro: se elige abajo
	) );
	if ( ! $posts ) return null;
	if ( ! function_exists( 'pll_get_post_language' ) || ! function_exists( 'grenvios_i18n_default' ) ) return $posts[0];

	$default = grenvios_i18n_default();
	foreach ( $posts as $p ) {
		if ( (string) pll_get_post_language( $p->ID ) === $default ) return $p;
	}
	return null;   // existe con ese slug, pero no en la ruta principal
}

/* Página de dinero (id) por su ruta, con respaldo bajo /servicios/ y /destinos/. */
function grenvios_guia_pilar_id( $path ) {
	foreach ( array( $path, 'servicios/' . $path, 'destinos/' . $path ) as $c ) {
		$pg = get_page_by_path( $c );
		if ( $pg && $pg->post_status === 'publish' ) return (int) $pg->ID;
	}
	return 0;
}

/* ── Interruptor: durante la importación mandamos nosotros sobre el slug ──
 *
 * `wp_update_post()` pasa SIEMPRE por `wp_unique_post_slug()`, también cuando
 * no se toca el slug. Un maestro y su copia de ruta comparten slug de forma
 * legítima —las distingue el prefijo /ve/—, y como el filtro de Polylang que
 * permite esa convivencia no está activo en CLI, WordPress le colgaba un «-2»
 * a la entrada que se guardara después. Cada importación creaba 63 sufijos que
 * había que reparar a continuación: un bucle.
 *
 * Con esto, mientras dura la importación el slug que pedimos es el que queda.
 * Fuera de la importación, WordPress sigue comportándose como siempre. */
function grenvios_slug_libre( $activar = null ) {
	static $on = false;
	if ( $activar !== null ) $on = (bool) $activar;
	return $on;
}

add_filter( 'wp_unique_post_slug', function ( $slug, $post_id, $status, $type, $parent, $original ) {
	if ( $type !== 'post' || ! grenvios_slug_libre() ) return $slug;
	return $original !== '' ? $original : $slug;
}, 10, 6 );

/* Escribe el slug de una entrada sin pasar por la validación de unicidad.
 *
 * Hace falta porque un maestro y su copia de ruta comparten slug de forma
 * legítima —lo que distingue las URLs es el prefijo /cl/, /ve/…— y Polylang lo
 * permite en el frontend. Pero `wp_update_post()` en CLI no tiene ese filtro
 * activo, ve el slug ocupado y le cuelga un «-2»… al que se actualice después,
 * que en la práctica era el MAESTRO. Aquí se comprueba el choque por idioma a
 * mano y se escribe directo, dejando redirigida la URL anterior.
 *
 * Devuelve false solo si otra entrada DEL MISMO idioma ya usa ese slug. */
function grenvios_fijar_slug( $post_id, $deseado, $lang = '' ) {
	global $wpdb;
	$post_id = (int) $post_id;
	$actual  = get_post_field( 'post_name', $post_id );
	if ( $actual === $deseado ) return true;
	if ( $lang === '' && function_exists( 'pll_get_post_language' ) ) $lang = (string) pll_get_post_language( $post_id );

	$ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_name = %s AND post_type = 'post' AND ID <> %d", $deseado, $post_id ) );
	foreach ( $ids as $oid ) {
		if ( ! function_exists( 'pll_get_post_language' ) ) return false;
		if ( (string) pll_get_post_language( (int) $oid ) === $lang ) return false;   // choque real
	}
	$wpdb->update( $wpdb->posts, array( 'post_name' => $deseado ), array( 'ID' => $post_id ) );
	if ( $actual && $actual !== $deseado ) add_post_meta( $post_id, '_wp_old_slug', $actual );
	clean_post_cache( $post_id );
	return true;
}

/* Repara de una pasada los slugs con sufijo «-N» que dejó el problema anterior. */
function grenvios_reparar_slugs() {
	global $wpdb;
	$res = array( 'reparados' => 0, 'conflictos' => 0 );
	$ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish' AND post_name REGEXP '-[0-9]+$'" );
	foreach ( $ids as $id ) {
		$name = get_post_field( 'post_name', (int) $id );
		if ( ! preg_match( '/^(.*)-\d+$/', $name, $m ) ) continue;
		if ( grenvios_fijar_slug( (int) $id, $m[1] ) ) $res['reparados']++;
		else $res['conflictos']++;
	}
	return $res;
}

function grenvios_guias_importar() {
	if ( ! function_exists( 'grenvios_guias_contenido' ) ) return array( 'error' => 'sin contenido' );
	grenvios_slug_libre( true );

	$res     = array( 'maestras_creadas' => 0, 'maestras_actualizadas' => 0, 'copias_actualizadas' => 0, 'copias_creadas' => 0 );
	$multi   = function_exists( 'grenvios_i18n_active' ) && grenvios_i18n_active() && function_exists( 'pll_get_post' );
	$default = $multi ? grenvios_i18n_default() : '';
	$home    = untrailingslashit( home_url() );
	$autor   = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
	$autor   = $autor ? (int) $autor[0] : 1;

	$tok = function ( $t ) { return function_exists( 'grenvios_sede_tokens_apply' ) ? grenvios_sede_tokens_apply( $t ) : $t; };

	$rutas = array();
	if ( $multi && function_exists( 'grenvios_es_ruta_pais' ) ) {
		foreach ( grenvios_i18n_langs() as $l => $_ ) if ( $l !== $default && grenvios_es_ruta_pais( $l ) ) $rutas[] = $l;
	}

	foreach ( grenvios_guias_contenido() as $slug => $g ) {
		$html  = str_replace( '%H%', $home, trim( $g['html'] ) );
		$pilar = grenvios_guia_pilar_id( $g['pilar'] );
		$cat   = get_term_by( 'slug', $g['categoria'], 'category' );

		/* ── 1) Maestra ─────────────────────────────────────────────── */
		$m = grenvios_guia_maestra( $slug );
		$datos = array(
			'post_type'    => 'post',
			'post_status'  => 'publish',
			'post_title'   => $tok( $g['titulo'] ),
			'post_name'    => $slug,
			'post_content' => $html,
			'post_excerpt' => $tok( $g['extracto'] ),
			'post_author'  => $autor,
		);
		if ( $m ) {
			$datos['ID'] = $m->ID;
			unset( $datos['post_name'] );   // el slug se fija aparte, ver grenvios_fijar_slug()
			// La maestra no lleva bloque de país; si alguna vez lo tuvo, se limpia.
			wp_update_post( $datos );
			$mid = (int) $m->ID;
			grenvios_fijar_slug( $mid, $slug, $default );
			$res['maestras_actualizadas']++;
		} else {
			$mid = (int) wp_insert_post( $datos );
			if ( ! $mid ) continue;
			if ( $multi ) pll_set_post_language( $mid, $default );
			$res['maestras_creadas']++;
		}
		if ( $cat ) wp_set_post_categories( $mid, array( (int) $cat->term_id ) );
		if ( $pilar ) update_post_meta( $mid, GRENVIOS_CLUSTER_META, $pilar );

		/* ── 2) Copias existentes por ruta ──────────────────────────── */
		foreach ( $rutas as $lang ) {
			$tid = (int) pll_get_post( $mid, $lang );
			if ( ! $tid ) continue;
			wp_update_post( array(
				'ID'           => $tid,
				'post_status'  => 'publish',
				'post_title'   => $tok( $g['titulo'] ),
				'post_content' => $html,
				'post_excerpt' => $tok( $g['extracto'] ),
			) );
			if ( function_exists( 'grenvios_ruta_terminos_de' ) ) {
				$cats = grenvios_ruta_terminos_de( $mid, $lang );
				if ( $cats ) wp_set_post_categories( $tid, $cats );
			}
			// La página de dinero DE ESA RUTA, no la de Perú: es lo que consulta
			// «Guías que te pueden ayudar» al pintar la página del país.
			$pt = ( $pilar && function_exists( 'grenvios_i18n_translation_id' ) ) ? (int) grenvios_i18n_translation_id( $pilar, $lang ) : 0;
			if ( $pt ) update_post_meta( $tid, GRENVIOS_CLUSTER_META, $pt );
			// Bloque por país al final, sobre el texto nuevo.
			if ( function_exists( 'grenvios_pais_aplicar' ) ) grenvios_pais_aplicar( $tid, '_post', $lang );
			$res['copias_actualizadas']++;
		}
	}

	/* ── 3) Copias que faltan ───────────────────────────────────────── */
	if ( $multi && function_exists( 'grenvios_ruta_duplicar_posts' ) ) {
		foreach ( $rutas as $lang ) {
			$r = grenvios_ruta_duplicar_posts( $lang );
			$res['copias_creadas'] += (int) $r['creadas'];
		}
		// Las recién creadas heredan la página de dinero de Perú: se corrige.
		foreach ( grenvios_guias_contenido() as $slug => $g ) {
			$m = grenvios_guia_maestra( $slug ); if ( ! $m ) continue;
			$pilar = grenvios_guia_pilar_id( $g['pilar'] ); if ( ! $pilar ) continue;
			foreach ( $rutas as $lang ) {
				$tid = (int) pll_get_post( $m->ID, $lang ); if ( ! $tid ) continue;
				$pt  = function_exists( 'grenvios_i18n_translation_id' ) ? (int) grenvios_i18n_translation_id( $pilar, $lang ) : 0;
				if ( $pt ) update_post_meta( $tid, GRENVIOS_CLUSTER_META, $pt );
			}
		}
	}

	grenvios_slug_libre( false );

	// Caché del bloque de enlazado: cambian las guías, cambia lo que enlaza.
	global $wpdb;
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_gr_rel_%' OR option_name LIKE '_transient_timeout_gr_rel_%'" );

	return $res;
}

/* ── %P:slug% y %G:slug% → enlace a esa guía en la ruta activa ─────────── */
add_filter( 'the_content', function ( $html ) {
	if ( strpos( $html, '%G:' ) !== false ) $html = str_replace( '%G:', '%P:', $html );   // mismo resolutor
	/* Al guardar la entrada, wp_kses toma «%P:» por un protocolo no permitido y
	 * lo borra: el enlace queda como href="slug%". Era así en 132 entradas y
	 * todos esos enlaces entre guías apuntaban a una URL relativa rota. Se
	 * devuelve a la forma original para que el resolutor de abajo lo trate. */
	if ( strpos( $html, '%"' ) !== false ) $html = preg_replace( '/href="([a-z0-9-]+)%"/', 'href="%P:$1%"', $html );
	if ( strpos( $html, '%P:' ) === false ) return $html;
	return preg_replace_callback( '/%P:([a-z0-9-]+)%/', function ( $m ) {
		static $cache = array();
		$slug = $m[1];
		if ( isset( $cache[ $slug ] ) ) return $cache[ $slug ];
		$post = grenvios_guia_maestra( $slug );
		/* Respaldo por clave de guía: una guía de país puede tener un slug
		 * público distinto de su clave («cuanto-demora-un-envio-a-ecuador» se
		 * publica como «por-que-se-retrasa-…»). Se busca la clave sin el sufijo
		 * de origen, y si no, el slug antiguo. */
		if ( ! $post && function_exists( 'grenvios_guia_por_clave' ) && function_exists( 'grenvios_i18n_default' ) ) {
			$suf   = function_exists( 'grenvios_bp_origen_sufijo' ) ? '-' . grenvios_bp_origen_sufijo() : '';
			$clave = ( $suf !== '' && substr( $slug, -strlen( $suf ) ) === $suf ) ? substr( $slug, 0, -strlen( $suf ) ) : $slug;
			$post  = grenvios_guia_por_clave( $clave, grenvios_i18n_default() );
		}
		if ( ! $post && function_exists( 'grenvios_upn_entrada_por_slug' ) ) {
			$oid  = grenvios_upn_entrada_por_slug( $slug );
			$post = $oid ? get_post( $oid ) : null;
		}
		if ( ! $post ) return $cache[ $slug ] = home_url( '/blog/' );
		$id = (int) $post->ID;
		if ( function_exists( 'pll_get_post' ) && function_exists( 'grenvios_i18n_current' ) ) {
			$t = (int) pll_get_post( $id, grenvios_i18n_current() );
			if ( $t ) $id = $t;
		}
		return $cache[ $slug ] = get_permalink( $id );
	}, $html );
}, 7 );
