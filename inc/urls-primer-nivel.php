<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Todas las URL en primer nivel: dominio/slug/ (y dominio/<ruta>/slug/)
 * ══════════════════════════════════════════════════════════════════════════
 *
 * DECISIÓN (2026-10-02): páginas, entradas, categorías y etiquetas viven en
 * el primer nivel de su ruta. Nada de /2026/09/22/…, /category/…, /tag/… ni
 * /servicios/…:
 *
 *   /envio-de-equipaje/            antes /servicios/envio-de-equipaje/
 *   /ec/cuanto-demora-…-a-ecuador/ antes /ec/2026/09/22/cuanto-demora-…/
 *   /tramites-de-aduana/           antes /tag/aduanas/
 *   /ec/guias-de-destinos-ecuador/ antes /ec/category/destinos-ecuador/
 *
 * Cómo:
 *   1) Entradas: estructura /%postname%/. Las URL con fecha responden 301.
 *   2) Categorías y etiquetas: el enlace pierde la base y cada término tiene
 *      su regla de reescritura. Las reglas entran por los filtros
 *      `category_rewrite_rules` / `post_tag_rewrite_rules`, así Polylang les
 *      añade el prefijo de cada ruta (/ec/, /co/…) igual que a las suyas.
 *      Las URL con base siguen resolviendo y WordPress las lleva con 301.
 *   3) Servicios: en la base siguen siendo hijas de /servicios/ (el código
 *      las busca por esa ruta), pero su URL pública es /slug/ y la antigua
 *      responde 301.
 *   4) Las fichas que ya declaraban otra canónica y colgaban de un padre
 *      (/destinos/ecuador/, /ec/destinos-ecuador/colombia/…) llevan a esa
 *      canónica con 301. Los administradores las siguen viendo para editarlas.
 *
 * Choques: al quitar las bases, un término y una página no pueden compartir
 * slug. Los términos que chocaban se renombran (grenvios_upn_renombres()) y
 * todas las búsquedas por el slug antiguo se traducen solas.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* 2: regenera las reglas sin los términos que chocan con páginas (y reintenta
 * los renombres, que en producción no se habían aplicado). */
/* 3: reglas de /envios-internacionales/ (antes /destinos/); 4: también /<ruta>/envios-internacionales/. */
define( 'GRENVIOS_UPN_V', 4 );

/* ─────────────────────────────────────────────────────────────────────────
 * 0) Renombres de términos que chocaban con páginas o entre taxonomías
 *    Una clave que termina en «-» es un prefijo (etiquetas de país).
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_upn_renombres() {
	return array(
		'category' => array(
			'destinos' => 'guias-de-destinos',          // página /destinos/ y espejos destinos-<ruta>
		),
		'post_tag' => array(
			'aduanas'              => 'tramites-de-aduana',      // categoría «aduanas»
			'envios-para-empresas' => 'guias-para-empresas',     // página
			'rastreo-de-envios'    => 'seguimiento-de-envios',   // página
			'peso-volumetrico'     => 'calculo-de-peso-volumetrico', // servicio
			'envios-a-'            => 'guias-envios-a-',         // fichas /<ruta>/envios-a-<país>/
		),
	);
}

/* Sufijos de ruta («ecuador», «estados-unidos»…) con los que se copian los términos. */
function grenvios_upn_sufijos() {
	static $s = null;
	if ( $s !== null ) return $s;
	$s = array();
	if ( function_exists( 'pll_languages_list' ) && function_exists( 'grenvios_etq_sufijo' ) ) {
		foreach ( pll_languages_list() as $l ) $s[] = grenvios_etq_sufijo( $l );
	}
	return $s;
}

/* Slug vigente de un término a partir del antiguo. Idempotente. */
function grenvios_upn_slug_nuevo( $slug, $tax ) {
	$mapa = grenvios_upn_renombres();
	if ( ! is_string( $slug ) || $slug === '' || empty( $mapa[ $tax ] ) ) return $slug;
	foreach ( $mapa[ $tax ] as $viejo => $nuevo ) {
		if ( substr( $viejo, -1 ) === '-' ) {
			if ( strpos( $slug, $viejo ) === 0 ) return $nuevo . substr( $slug, strlen( $viejo ) );
			continue;
		}
		if ( $slug === $viejo ) return $nuevo;
		if ( strpos( $slug, $viejo . '-' ) === 0 && in_array( substr( $slug, strlen( $viejo ) + 1 ), grenvios_upn_sufijos(), true ) ) {
			return $nuevo . substr( $slug, strlen( $viejo ) );
		}
	}
	return $slug;
}

/* get_term_by( 'slug' ), tax_query por slug, etc. pasan por get_terms(): el
 * código que todavía pide «aduanas» recibe «tramites-de-aduana». */
add_filter( 'get_terms_args', function ( $args, $taxonomies ) {
	if ( empty( $args['slug'] ) || count( (array) $taxonomies ) !== 1 ) return $args;
	$tax = reset( $taxonomies );
	if ( $tax !== 'category' && $tax !== 'post_tag' ) return $args;
	if ( is_array( $args['slug'] ) ) {
		$args['slug'] = array_map( function ( $s ) use ( $tax ) { return grenvios_upn_slug_nuevo( $s, $tax ); }, $args['slug'] );
	} else {
		$args['slug'] = grenvios_upn_slug_nuevo( $args['slug'], $tax );
	}
	return $args;
}, 1, 2 );

/* Aplica los renombres en la base (una vez). Devuelve cuántos cambió. */
function grenvios_upn_renombrar_terminos() {
	$n = 0;
	foreach ( array_keys( grenvios_upn_renombres() ) as $tax ) {
		$terms = get_terms( array( 'taxonomy' => $tax, 'hide_empty' => false, 'lang' => '' ) );
		if ( is_wp_error( $terms ) ) continue;
		foreach ( $terms as $t ) {
			$nuevo = grenvios_upn_slug_nuevo( $t->slug, $tax );
			if ( $nuevo === $t->slug ) continue;
			$r = wp_update_term( $t->term_id, $tax, array( 'slug' => $nuevo ) );
			if ( ! is_wp_error( $r ) ) $n++;
		}
	}
	return $n;
}

/* ─────────────────────────────────────────────────────────────────────────
 * 1) Puesta en marcha (una vez por versión): estructura, renombres, reglas
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_upn_activar() {
	global $wp_rewrite;
	if ( get_option( 'permalink_structure' ) !== '/%postname%/' ) {
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
	}
	$n = grenvios_upn_renombrar_terminos();
	update_option( 'grenvios_upn_v', GRENVIOS_UPN_V );
	flush_rewrite_rules( false );
	if ( function_exists( 'grenvios_cache_bump' ) ) grenvios_cache_bump();
	return $n;
}
add_action( 'admin_init', function () {
	if ( (int) get_option( 'grenvios_upn_v' ) >= GRENVIOS_UPN_V ) return;
	grenvios_upn_activar();
} );

/* ─────────────────────────────────────────────────────────────────────────
 * 2) Categorías y etiquetas sin base
 * ───────────────────────────────────────────────────────────────────────── */
add_filter( 'term_link', function ( $url, $term, $tax ) {
	if ( $tax !== 'category' && $tax !== 'post_tag' ) return $url;
	return preg_replace( '~^(https?://[^/]+/(?:[a-z]{2}/)?)(?:category|tag)/~', '$1', $url, 1 );
}, 99, 3 );

function grenvios_upn_reglas_terminos( $tax, $var ) {
	global $wpdb;
	$slugs = get_terms( array( 'taxonomy' => $tax, 'hide_empty' => false, 'lang' => '', 'fields' => 'id=>slug' ) );
	if ( is_wp_error( $slugs ) || ! $slugs ) return array();
	/* Gana la página: estas reglas van antes que las de páginas, y un término con
	 * el slug de una página (la categoría «destinos», o «destinos-ecuador» frente
	 * a /ec/destinos-ecuador/) la dejaba en 404. */
	$paginas = $wpdb->get_col( "SELECT DISTINCT post_name FROM {$wpdb->posts} WHERE post_type='page' AND post_status='publish'" );
	$slugs   = array_diff( array_values( $slugs ), (array) $paginas );
	if ( ! $slugs ) return array();
	$alt = implode( '|', array_map( function ( $s ) { return preg_quote( $s, '#' ); }, $slugs ) );
	return array(
		'(' . $alt . ')/page/?([0-9]{1,})/?$' => 'index.php?' . $var . '=$matches[1]&paged=$matches[2]',
		'(' . $alt . ')/?$'                   => 'index.php?' . $var . '=$matches[1]',
	);
}
/* Prioridad 1: antes que Polylang, que añade a estas reglas el prefijo de ruta. */
add_filter( 'category_rewrite_rules', function ( $rules ) {
	return array_merge( grenvios_upn_reglas_terminos( 'category', 'category_name' ), $rules );
}, 1 );
add_filter( 'post_tag_rewrite_rules', function ( $rules ) {
	return array_merge( grenvios_upn_reglas_terminos( 'post_tag', 'tag' ), $rules );
}, 1 );

/* Un término nuevo o renombrado necesita su regla: se regeneran al final de la
 * petición, una sola vez aunque una importación cree cien. */
function grenvios_upn_marcar_flush() { $GLOBALS['grenvios_upn_flush'] = true; }
foreach ( array( 'created_category', 'edited_category', 'delete_category', 'created_post_tag', 'edited_post_tag', 'delete_post_tag' ) as $h ) {
	add_action( $h, 'grenvios_upn_marcar_flush' );
}
add_action( 'shutdown', function () {
	if ( ! empty( $GLOBALS['grenvios_upn_flush'] ) && (int) get_option( 'grenvios_upn_v' ) >= GRENVIOS_UPN_V ) flush_rewrite_rules( false );
} );

/* ─────────────────────────────────────────────────────────────────────────
 * 3) Servicios en primer nivel
 * ───────────────────────────────────────────────────────────────────────── */
/* post_name => ID de las hijas publicadas de /servicios/ (ruta principal). */
function grenvios_upn_servicios() {
	static $m = null;
	if ( $m !== null ) return $m;
	$m = array();
	global $wpdb;
	$padre = (int) $wpdb->get_var( "SELECT ID FROM {$wpdb->posts} WHERE post_type='page' AND post_status='publish' AND post_name='servicios' AND post_parent=0 LIMIT 1" );
	if ( ! $padre ) return $m;
	foreach ( $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_name FROM {$wpdb->posts} WHERE post_type='page' AND post_status='publish' AND post_parent=%d", $padre ) ) as $r ) {
		$m[ $r->post_name ] = (int) $r->ID;
	}
	return $m;
}

add_filter( 'page_link', function ( $link, $post_id ) {
	$p = get_post( $post_id );
	if ( ! $p ) return $link;
	$s = grenvios_upn_servicios();
	if ( isset( $s[ $p->post_name ] ) && $s[ $p->post_name ] === (int) $p->ID ) {
		return trailingslashit( get_option( 'home' ) ) . $p->post_name . '/';
	}
	return $link;
}, 99, 2 );

add_action( 'init', function () {
	$s = array_keys( grenvios_upn_servicios() );
	if ( ! $s ) return;
	$alt = implode( '|', array_map( 'preg_quote', $s ) );
	add_rewrite_rule( '^(' . $alt . ')/?$', 'index.php?pagename=servicios/$matches[1]', 'top' );
}, 20 );

/* Enlaces escritos a mano con /servicios/<hija>/ o con fecha: a su URL nueva. */
add_filter( 'grenvios_html_final', function ( $html ) {
	if ( ! is_string( $html ) || $html === '' ) return $html;
	$home = preg_quote( untrailingslashit( get_option( 'home' ) ), '~' );
	$s = array_keys( grenvios_upn_servicios() );
	if ( $s && strpos( $html, '/servicios/' ) !== false ) {
		$alt = implode( '|', array_map( function ( $x ) { return preg_quote( $x, '~' ); }, $s ) );
		$r = preg_replace( '~(href=["\'](?:' . $home . ')?)/servicios/(' . $alt . ')/~', '$1/$2/', $html );
		if ( is_string( $r ) ) $html = $r;
	}
	if ( preg_match( '~/20\d\d/\d\d/\d\d/~', $html ) ) {
		$r = preg_replace( '~(href=["\']' . $home . '/(?:[a-z]{2}/)?)20\d\d/\d\d/\d\d/([a-z0-9-]+/)~', '$1$2', $html );
		if ( is_string( $r ) ) $html = $r;
	}
	return $html;
}, 60 );

/* ─────────────────────────────────────────────────────────────────────────
 * 4) Redirecciones 301 de las URL antiguas
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_upn_ruta_pedida() {
	$p = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH ) : '';
	$base = (string) wp_parse_url( get_option( 'home' ), PHP_URL_PATH );   // home_url() lleva /ec/ en las rutas
	if ( $base !== '' && strpos( $p, $base ) === 0 ) $p = substr( $p, strlen( $base ) );
	return trim( $p, '/' );
}

/* Entrada por slug actual o antiguo (WordPress guarda los antiguos en _wp_old_slug). */
function grenvios_upn_entrada_por_slug( $slug ) {
	global $wpdb;
	$id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_type='post' AND post_status='publish' AND post_name=%s LIMIT 1", $slug ) );
	if ( ! $id ) {
		$id = (int) $wpdb->get_var( $wpdb->prepare(
			"SELECT p.ID FROM {$wpdb->postmeta} m INNER JOIN {$wpdb->posts} p ON p.ID=m.post_id
			 WHERE m.meta_key='_wp_old_slug' AND m.meta_value=%s AND p.post_type='post' AND p.post_status='publish' LIMIT 1", $slug ) );
	}
	return $id;
}

add_action( 'template_redirect', function () {
	if ( is_admin() || get_query_var( 'grenvios_sitemap' ) ) return;
	$ruta = grenvios_upn_ruta_pedida();

	/* Archivo pedido con la base antigua (/category/…, /tag/…, slug viejo) → su URL. */
	if ( ( is_category() || is_tag() ) && ! is_feed() ) {
		$t = get_queried_object();
		$u = ( $t && ! is_wp_error( $t ) ) ? get_term_link( $t ) : '';
		if ( is_string( $u ) && $u !== '' ) {
			$paged = (int) get_query_var( 'paged' );
			if ( $paged > 1 ) $u = trailingslashit( $u ) . 'page/' . $paged . '/';
			if ( trim( (string) wp_parse_url( $u, PHP_URL_PATH ), '/' ) !== $ruta ) {
				wp_safe_redirect( $u, 301, 'Grenvios' );
				exit;
			}
		}
		return;
	}

	/* /ec/destinos-ecuador/ → /ec/envios-internacionales/. */
	if ( is_page() && in_array( (int) get_queried_object_id(), grenvios_upn_hubs_ruta(), true ) ) {
		$u = get_permalink( (int) get_queried_object_id() );
		if ( $u && trim( (string) wp_parse_url( $u, PHP_URL_PATH ), '/' ) !== $ruta ) {
			wp_safe_redirect( $u, 301, 'Grenvios' );
			exit;
		}
	}

	/* /destinos/[…] → /envios-internacionales/[…] (las fichas con otra canónica
	 * las lleva el bloque de abajo directamente a su ficha de ruta). */
	if ( is_page() && ( $ruta === 'destinos' || strpos( $ruta, 'destinos/' ) === 0 ) ) {
		$id = (int) get_queried_object_id();
		$c  = function_exists( 'grenvios_canib_ficha_de' ) ? grenvios_canib_ficha_de( $id ) : '';
		$u  = get_permalink( $id );
		if ( $c === '' && $u && trim( (string) wp_parse_url( $u, PHP_URL_PATH ), '/' ) !== $ruta ) {
			wp_safe_redirect( $u, 301, 'Grenvios' );
			exit;
		}
	}

	/* Servicio pedido como /servicios/<slug>/ → /<slug>/. */
	if ( is_page() && strpos( $ruta, 'servicios/' ) === 0 ) {
		$u = get_permalink( (int) get_queried_object_id() );
		if ( $u && trim( (string) wp_parse_url( $u, PHP_URL_PATH ), '/' ) !== $ruta ) {
			wp_safe_redirect( $u, 301, 'Grenvios' );
			exit;
		}
	}

	/* Ficha con padre que declara otra canónica → esa canónica. */
	if ( is_singular( 'page' ) && ! is_user_logged_in() ) {
		$id = (int) get_queried_object_id();
		$p  = get_post( $id );
		if ( $p && $p->post_parent ) {
			$c = '';
			if ( function_exists( 'grenvios_espejo_es' ) && grenvios_espejo_es( $id ) && function_exists( 'grenvios_espejo_canonica' ) ) $c = grenvios_espejo_canonica( $id );
			if ( $c === '' && function_exists( 'grenvios_canib_ficha_de' ) ) $c = grenvios_canib_ficha_de( $id );
			if ( $c !== '' && untrailingslashit( $c ) !== untrailingslashit( get_permalink( $id ) ) ) {
				wp_safe_redirect( $c, 301, 'Grenvios' );
				exit;
			}
		}
		return;
	}

	if ( ! is_404() ) return;

	/* Copias borradas que Google tenía indexadas (2026-10-09):
	 *   /x-2/, /ar/x-3-a-argentina/ → la página sin el sufijo, si existe;
	 *   /sample-page/, /ar/sample-page-a-argentina/ → la portada de su ruta.
	 * Un 301 conserva lo ganado en vez de un 404. */
	if ( preg_match( '~^((?:[a-z]{2}/)?)(?:sample-page|hello-world)(?:-a-[a-z-]+)?$~', $ruta, $m ) ) {
		wp_safe_redirect( home_url( '/' . $m[1] ), 301, 'Grenvios' );
		exit;
	}
	if ( preg_match( '~^(.*?)-[234]((?:-(?:a|para)-[a-z-]+)?)$~', $ruta, $m ) ) {
		$base = untrailingslashit( function_exists( 'grenvios_i18n_site_root' ) ? grenvios_i18n_site_root() : get_option( 'home' ) );
		$cand = $base . '/' . $m[1] . $m[2] . '/';
		if ( url_to_postid( $cand ) ) {
			wp_safe_redirect( $cand, 301, 'Grenvios' );
			exit;
		}
		/* En una ruta el slug del país no siempre es «-a-X» (apostilla «-para-X»):
		 * se busca la página de Perú y su versión en esa ruta. */
		if ( preg_match( '~^([a-z]{2})/(.+)$~', $m[1], $r ) && function_exists( 'pll_get_post' ) ) {
			$master = get_page_by_path( $r[2] );
			if ( ! $master && ( $s = get_posts( array( 'post_type' => 'page', 'name' => $r[2], 'numberposts' => 1, 'lang' => '', 'suppress_filters' => true ) ) ) ) $master = $s[0];
			$t = $master ? (int) pll_get_post( $master->ID, $r[1] ) : 0;
			if ( $t && get_post_status( $t ) === 'publish' ) {
				wp_safe_redirect( get_permalink( $t ), 301, 'Grenvios' );
				exit;
			}
		}
	}

	/* /[ruta/]AAAA/MM/DD/slug/ → la entrada. */
	if ( preg_match( '~^(?:[a-z]{2}/)?20\d\d/\d\d/\d\d/([^/]+)$~', $ruta, $m ) ) {
		$id = grenvios_upn_entrada_por_slug( $m[1] );
		if ( $id ) { wp_safe_redirect( get_permalink( $id ), 301, 'Grenvios' ); exit; }
	}

	/* /[ruta/]category|tag/<slug antiguo>/ → el término renombrado. */
	if ( preg_match( '~^(?:[a-z]{2}/)?(category|tag)/([^/]+)(?:/page/(\d+))?$~', $ruta, $m ) ) {
		$tax  = $m[1] === 'tag' ? 'post_tag' : 'category';
		$slug = grenvios_upn_slug_nuevo( $m[2], $tax );
		$t    = get_terms( array( 'taxonomy' => $tax, 'slug' => $slug, 'hide_empty' => false, 'lang' => '', 'number' => 1 ) );
		if ( $t && ! is_wp_error( $t ) ) {
			$u = get_term_link( $t[0] );
			if ( ! is_wp_error( $u ) ) { wp_safe_redirect( $u, 301, 'Grenvios' ); exit; }
		}
	}
}, 1 );

/* ─────────────────────────────────────────────────────────────────────────
 * 5) Títulos y descripciones propios de los archivos del blog por ruta
 *    Salían como «Destinos – greenvios»: el mismo title en las diez rutas.
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_upn_pais_ruta() {
	$l = function_exists( 'pll_current_language' ) ? pll_current_language() : '';
	$d = function_exists( 'grenvios_i18n_default' ) ? grenvios_i18n_default() : '';
	if ( $l === '' || $l === $d || ! function_exists( 'grenvios_sede_destino_nombre' ) ) return '';
	return (string) grenvios_sede_destino_nombre( $l );
}

add_filter( 'pre_get_document_title', function ( $title ) {
	if ( ! is_category() && ! is_tag() ) return $title;
	$t = get_queried_object();
	if ( ! $t || is_wp_error( $t ) ) return $title;
	$nombre = trim( preg_replace( '/\s*[-–—:]\s*' . preg_quote( grenvios_upn_pais_ruta(), '/' ) . '$/u', '', $t->name ) );
	$pais   = grenvios_upn_pais_ruta();
	if ( $pais !== '' && stripos( $nombre, $pais ) === false ) $nombre .= ' para enviar a ' . $pais;
	elseif ( $pais === '' && stripos( $nombre, 'envío' ) === false && stripos( $nombre, 'envio' ) === false ) $nombre .= ': guías de envío desde Perú';
	$pag = is_paged() ? ' (página ' . (int) get_query_var( 'paged' ) . ')' : '';
	/* Máximo 60 caracteres: se acorta la coletilla antes que el nombre. */
	if ( mb_strlen( $nombre . $pag . ' | Grenvíos' ) > 60 ) $nombre = str_replace( ': guías de envío desde Perú', ': guías desde Perú', $nombre );
	if ( mb_strlen( $nombre . $pag . ' | Grenvíos' ) > 60 ) $nombre = preg_replace( '/:.*$/u', '', $nombre );
	return $nombre . $pag . ' | Grenvíos';
}, 30 );

/* Canónica de los archivos: la URL del término (antes copiaba la pedida). */
add_filter( 'grenvios_canonical', function ( $url ) {
	if ( ! is_category() && ! is_tag() ) return $url;
	$t = get_queried_object();
	$u = ( $t && ! is_wp_error( $t ) ) ? get_term_link( $t ) : '';
	if ( ! is_string( $u ) || $u === '' ) return $url;
	$paged = (int) get_query_var( 'paged' );
	return $paged > 1 ? trailingslashit( $u ) . 'page/' . $paged . '/' : $u;
}, 5 );

/* Las etiquetas de país de la ruta principal («Envíos a Ecuador») reúnen
 * entradas cuyo tema ya posiciona la ficha /<ruta>/envios-a-<país>/: se siguen
 * enlazando, pero no compiten en el índice. */
function grenvios_upn_etq_pais( $t ) {
	return $t && ! is_wp_error( $t ) && isset( $t->slug ) && strpos( (string) $t->slug, 'guias-envios-a-' ) === 0;
}
add_filter( 'grenvios_noindex_tag', function ( $noindex ) {
	return grenvios_upn_etq_pais( get_queried_object() ) ? true : $noindex;
}, 30 );

/* ─────────────────────────────────────────────────────────────────────────
 * 6) Sitemap agrupado
 *
 *   /sitemap.xml                       índice
 *   /sitemap-<ruta>-paginas.xml        páginas de esa ruta
 *   /sitemap-<ruta>-entradas.xml       entradas del blog
 *   /sitemap-<ruta>-categorias.xml     archivos de categoría indexables
 *   /sitemap-<ruta>-etiquetas.xml      archivos de etiqueta indexables
 *
 * Search Console muestra la cobertura de cada hoja: se ve de un vistazo si
 * Google indexa el blog de Chile o solo sus páginas. /sitemap-<ruta>.xml
 * sigue respondiendo (todo junto) para no romper lo ya enviado.
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_upn_grupos() {
	return array( 'paginas' => 'Páginas', 'entradas' => 'Entradas del blog', 'categorias' => 'Categorías', 'etiquetas' => 'Etiquetas' );
}

/* Categorías con contenido suficiente para indexarse (misma regla que el noindex de seo-clusters). */
add_filter( 'grenvios_sitemap_urls', function ( $urls, $lang = '' ) {
	$terms = get_terms( array( 'taxonomy' => 'category', 'hide_empty' => true, 'lang' => (string) $lang ) );
	if ( is_wp_error( $terms ) ) return $urls;
	foreach ( $terms as $t ) {
		if ( $t->slug === 'uncategorized' || $t->slug === 'sin-categoria' ) continue;
		if ( (int) $t->count < 3 && trim( (string) $t->description ) === '' ) continue;
		$u = get_term_link( $t );
		if ( is_wp_error( $u ) ) continue;
		$urls[] = array( 'loc' => $u, 'priority' => '0.5', 'changefreq' => 'weekly', 'tipo' => 'categorias' );
	}
	return $urls;
}, 12, 2 );

/* Tipo de cada URL, y fuera las etiquetas de país. */
add_filter( 'grenvios_sitemap_urls', function ( $urls ) {
	$out = array();
	foreach ( $urls as $u ) {
		if ( empty( $u['tipo'] ) ) {
			if ( ! empty( $u['post_id'] ) ) {
				$u['tipo'] = get_post_type( (int) $u['post_id'] ) === 'post' ? 'entradas' : 'paginas';
			} elseif ( isset( $u['priority'] ) && $u['priority'] === '0.4' ) {
				$u['tipo'] = 'etiquetas';   // las añade inc/blog-etiquetas.php
			} else {
				$u['tipo'] = 'paginas';
			}
		}
		if ( $u['tipo'] === 'etiquetas' && preg_match( '~/guias-envios-a-[^/]+/$~', (string) $u['loc'] ) ) continue;
		$out[] = $u;
	}
	return $out;
}, 99 );

add_action( 'init', function () {
	add_rewrite_rule( '^sitemap-([a-z]{2,6}-(?:paginas|entradas|categorias|etiquetas))\.xml$', 'index.php?grenvios_sitemap=$matches[1]', 'top' );
	add_rewrite_rule( '^sitemap\.xsl$', 'index.php?grenvios_sitemap=xsl', 'top' );
} );

function grenvios_upn_sitemap_xml_cabecera() {
	header( 'Content-Type: application/xml; charset=UTF-8' );
	header( 'X-Robots-Tag: noindex, follow' );
	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	echo '<?xml-stylesheet type="text/xsl" href="' . esc_url( home_url( '/sitemap.xsl' ) ) . '"?>' . "\n";
}

/* Prioridad 5: antes del sitemap de functions.php (prioridad 10), que sigue
 * sirviendo /sitemap-<ruta>.xml completo. */
add_action( 'template_redirect', function () {
	$q = (string) get_query_var( 'grenvios_sitemap' );
	if ( $q === '' ) return;
	$rutas = function_exists( 'grenvios_sitemap_rutas' ) ? grenvios_sitemap_rutas() : array();

	if ( $q === 'xsl' ) {
		header( 'Content-Type: text/xsl; charset=UTF-8' );
		echo grenvios_upn_sitemap_xsl();
		exit;
	}

	/* Índice agrupado: por ruta, una hoja por tipo con algo dentro. */
	if ( $q === '1' && $rutas ) {
		grenvios_upn_sitemap_xml_cabecera();
		echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		foreach ( $rutas as $r ) {
			$por = array();
			foreach ( grenvios_sitemap_urls( $r ) as $u ) {
				$g = $u['tipo'];
				if ( ! isset( $por[ $g ] ) ) $por[ $g ] = '';
				if ( ! empty( $u['lastmod'] ) && $u['lastmod'] > $por[ $g ] ) $por[ $g ] = $u['lastmod'];
			}
			foreach ( array_keys( grenvios_upn_grupos() ) as $g ) {
				if ( ! isset( $por[ $g ] ) ) continue;
				echo "\t<sitemap>\n\t\t<loc>" . esc_url( home_url( '/sitemap-' . $r . '-' . $g . '.xml' ) ) . "</loc>\n";
				if ( $por[ $g ] ) echo "\t\t<lastmod>" . esc_html( $por[ $g ] ) . "</lastmod>\n";
				echo "\t</sitemap>\n";
			}
		}
		echo '</sitemapindex>';
		exit;
	}

	if ( ! preg_match( '~^([a-z]{2,6})-(paginas|entradas|categorias|etiquetas)$~', $q, $m ) ) return;
	if ( ! in_array( $m[1], $rutas, true ) ) {
		status_header( 404 );
		grenvios_upn_sitemap_xml_cabecera();
		echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>';
		exit;
	}
	grenvios_upn_sitemap_xml_cabecera();
	echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
	foreach ( grenvios_sitemap_urls( $m[1] ) as $u ) {
		if ( $u['tipo'] !== $m[2] ) continue;
		echo "\t<url>\n\t\t<loc>" . esc_url( $u['loc'] ) . "</loc>\n";
		if ( ! empty( $u['post_id'] ) && function_exists( 'grenvios_sitemap_image' ) ) {
			$img = grenvios_sitemap_image( (int) $u['post_id'] );
			if ( $img ) echo "\t\t<image:image><image:loc>" . esc_url( $img ) . "</image:loc></image:image>\n";
		}
		if ( ! empty( $u['lastmod'] ) )    echo "\t\t<lastmod>" . esc_html( $u['lastmod'] ) . "</lastmod>\n";
		if ( ! empty( $u['changefreq'] ) ) echo "\t\t<changefreq>" . esc_html( $u['changefreq'] ) . "</changefreq>\n";
		if ( ! empty( $u['priority'] ) )   echo "\t\t<priority>" . esc_html( $u['priority'] ) . "</priority>\n";
		echo "\t</url>\n";
	}
	echo '</urlset>';
	exit;
}, 5 );

/* Hoja de estilo para leer el sitemap en el navegador: el cliente ve qué hay
 * en cada grupo sin abrir XML crudo. Google la ignora. */
function grenvios_upn_sitemap_xsl() {
	return '<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:s="http://www.sitemaps.org/schemas/sitemap/0.9">
<xsl:output method="html" encoding="UTF-8" indent="yes"/>
<xsl:template match="/">
<html lang="es"><head><meta charset="UTF-8"/><title>Sitemap · Grenvíos</title>
<style>body{font-family:Poppins,system-ui,sans-serif;margin:0;padding:32px 16px;background:#f8f5f1;color:#333}main{max-width:1100px;margin:0 auto}h1{color:#5e2129;font-size:26px;margin:0 0 6px}p{color:#666;margin:0 0 20px}table{width:100%;border-collapse:collapse;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 6px 24px rgba(94,33,41,.08)}th{background:#5e2129;color:#fff;text-align:left;padding:10px 14px;font-size:13px}td{padding:9px 14px;border-top:1px solid #eee;font-size:13px;word-break:break-all}a{color:#5e2129}tr:hover td{background:#fbf7f4}</style></head>
<body><main>
<xsl:choose>
<xsl:when test="s:sitemapindex">
<h1>Sitemap de Grenvíos</h1><p><xsl:value-of select="count(s:sitemapindex/s:sitemap)"/> grupos: páginas, entradas, categorías y etiquetas de cada ruta.</p>
<table><tr><th>Grupo</th><th>Última modificación</th></tr>
<xsl:for-each select="s:sitemapindex/s:sitemap"><tr><td><a href="{s:loc}"><xsl:value-of select="s:loc"/></a></td><td><xsl:value-of select="substring(s:lastmod,1,10)"/></td></tr></xsl:for-each>
</table></xsl:when>
<xsl:otherwise>
<h1>Sitemap de Grenvíos</h1><p><xsl:value-of select="count(s:urlset/s:url)"/> URL · <a href="/sitemap.xml">volver al índice</a></p>
<table><tr><th>URL</th><th>Modificada</th><th>Prioridad</th></tr>
<xsl:for-each select="s:urlset/s:url"><tr><td><a href="{s:loc}"><xsl:value-of select="s:loc"/></a></td><td><xsl:value-of select="substring(s:lastmod,1,10)"/></td><td><xsl:value-of select="s:priority"/></td></tr></xsl:for-each>
</table></xsl:otherwise>
</xsl:choose>
</main></body></html>
</xsl:template>
</xsl:stylesheet>';
}

/* ─────────────────────────────────────────────────────────────────────────
 * 5) Una URL, una entrada
 *    La ruta principal y la ruta «pe» de Polylang pueden tener dos entradas con
 *    el mismo slug (p. ej. «como-enviar-tus-compras-hechas-en-peru»). La URL las
 *    encontraba a las dos y single.php pintaba la página dos veces: dos H1, dos
 *    FAQ y, primero, la del otro idioma. Se queda la que vive en esta dirección.
 * ───────────────────────────────────────────────────────────────────────── */
add_filter( 'the_posts', function ( $posts, $q ) {
	if ( is_admin() || ! $q->is_main_query() || ! $q->is_singular() || count( (array) $posts ) < 2 ) return $posts;
	$pedida = untrailingslashit( (string) wp_parse_url( isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '', PHP_URL_PATH ) );
	foreach ( $posts as $p ) {
		if ( untrailingslashit( (string) wp_parse_url( get_permalink( $p ), PHP_URL_PATH ) ) === $pedida ) return array( $p );
	}
	return array( $posts[0] );
}, 10, 2 );

/* ─────────────────────────────────────────────────────────────────────────
 * 6) /destinos/ se llama /envios-internacionales/ (2026-10-08)
 *    Como el menú («Envíos Internacionales»). En la base sigue siendo la página
 *    «destinos» con sus hijas: el código las busca por esa ruta
 *    (get_page_by_path( 'destinos/…' )). Solo cambia la URL pública.
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_upn_base_destinos() { return 'envios-internacionales'; }

/* Hub de cada ruta: lang => ID de su copia del hub (/ec/destinos-ecuador/). */
function grenvios_upn_hubs_ruta() {
	static $m = null;
	if ( $m !== null ) return $m;
	$m   = array();
	$hub = get_page_by_path( 'destinos' );
	if ( ! $hub || ! function_exists( 'pll_languages_list' ) || ! function_exists( 'pll_get_post' ) ) return $m;
	foreach ( pll_languages_list() as $l ) {
		$t = (int) pll_get_post( $hub->ID, $l );
		if ( $t && $t !== (int) $hub->ID && get_post_status( $t ) === 'publish' ) $m[ $l ] = $t;
	}
	return $m;
}

add_action( 'init', function () {
	$b = grenvios_upn_base_destinos();
	add_rewrite_rule( '^' . $b . '/?$', 'index.php?pagename=destinos', 'top' );
	add_rewrite_rule( '^' . $b . '/(.+?)/?$', 'index.php?pagename=destinos/$matches[1]', 'top' );
	/* /ec/envios-internacionales/ → el hub de la ruta de Ecuador. */
	foreach ( grenvios_upn_hubs_ruta() as $l => $id ) {
		add_rewrite_rule( '^' . preg_quote( $l, '#' ) . '/' . $b . '/?$', 'index.php?page_id=' . $id . '&lang=' . $l, 'top' );
	}
}, 20 );

add_filter( 'page_link', function ( $link, $post_id ) {
	$l = array_search( (int) $post_id, grenvios_upn_hubs_ruta(), true );
	if ( $l !== false ) {
		$root = untrailingslashit( function_exists( 'grenvios_i18n_site_root' ) ? grenvios_i18n_site_root() : get_option( 'home' ) );
		return $root . '/' . $l . '/' . grenvios_upn_base_destinos() . '/';
	}
	$uri = get_page_uri( $post_id );
	if ( $uri !== 'destinos' && strpos( (string) $uri, 'destinos/' ) !== 0 ) return $link;
	$r = preg_replace( '~^(https?://[^/]+(?:/[^/]+)*?)/destinos(/|$)~', '$1/' . grenvios_upn_base_destinos() . '$2', $link, 1 );
	return is_string( $r ) ? $r : $link;
}, 99, 2 );

/* Enlaces escritos a mano (plantillas, guías, contenido guardado, migas en
 * JSON-LD): se cambian sobre la página entera, al final, para no interferir
 * con la reescritura de /destinos/<país>/ a la ficha de cada ruta. */
function grenvios_upn_destinos_html( $html ) {
	if ( ! is_string( $html ) || strpos( $html, 'destinos' ) === false ) return $html;
	$root = untrailingslashit( function_exists( 'grenvios_i18n_site_root' ) ? grenvios_i18n_site_root() : get_option( 'home' ) );
	$b    = grenvios_upn_base_destinos();
	$html = str_replace(
		array( $root . '/destinos/', str_replace( '/', '\/', $root ) . '\/destinos\/', 'href="/destinos/', "href='/destinos/" ),
		array( $root . '/' . $b . '/', str_replace( '/', '\/', $root ) . '\/' . $b . '\/', 'href="/' . $b . '/', "href='/" . $b . '/' ),
		$html
	);
	return $html;
}
add_action( 'template_redirect', function () {
	if ( is_admin() || is_feed() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) return;
	ob_start( 'grenvios_upn_destinos_html' );
}, 2 );
