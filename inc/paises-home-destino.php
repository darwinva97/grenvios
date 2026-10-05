<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Home del país en /ar/ y página de destino en /ar/envios-a-argentina/
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Decisión de la clienta (2026-09-14):
 *
 *   dominio.com/ar/                     → HOME de Argentina (carrusel de la home)
 *   dominio.com/ar/envios-a-argentina/  → DESTINO Argentina (hero del camión)
 *
 * Antes eran la misma página: la portada que Polylang asignaba a la ruta tenía
 * el slug «envios-a-argentina» y /ar/ redirigía a ella. Aquí se separan:
 *
 *   1. Se crea una página «Inicio» en la ruta, traducción de la home de Perú.
 *      Pasa a ser la portada del idioma y, con la opción `redirect_lang` de
 *      Polylang, se sirve en /ar/ sin redirigir.
 *   2. La antigua portada (/ar/envios-a-argentina/) se enlaza como traducción de
 *      /destinos/argentina/ y se marca con ese slug maestro: page.php la pinta
 *      con la plantilla de destino.
 *   3. El espejo que ocupaba ese hueco (/ar/destinos-argentina/argentina/) se
 *      borra: la ficha de Argentina en su ruta es ahora la página real.
 *
 * Idempotente. Se ejecuta tras duplicar una ruta nueva (acción
 * `grenvios_ruta_duplicada`) y una vez para las rutas existentes.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

const GRENVIOS_DESTINO_RUTA_META = '_grenvios_destino_ruta';

function grenvios_ruta_separar_home( $lang ) {
	$res = array( 'lang' => $lang, 'hecho' => false, 'nota' => '' );
	if ( ! function_exists( 'pll_get_post' ) || ! function_exists( 'pll_save_post_translations' ) ) { $res['nota'] = 'sin Polylang'; return $res; }
	if ( $lang === grenvios_i18n_default() || ! grenvios_es_ruta_pais( $lang ) ) { $res['nota'] = 'no es ruta'; return $res; }

	$home_m = (int) get_option( 'page_on_front' );
	$dslug  = function_exists( 'grenvios_sede_destino_propio' ) ? grenvios_sede_destino_propio( $lang ) : '';
	$dest_m = $dslug !== '' ? get_page_by_path( 'destinos/' . $dslug ) : null;
	if ( ! $home_m || ! $dest_m ) { $res['nota'] = 'falta home o destino maestro'; return $res; }

	$portada = (int) pll_get_post( $home_m, $lang );
	if ( ! $portada ) { $res['nota'] = 'la ruta no tiene portada'; return $res; }
	if ( get_post_meta( $portada, GRENVIOS_DESTINO_RUTA_META, true ) ) { $res['nota'] = 'ya separada'; return $res; }

	$pais = sanitize_title( remove_accents( (string) grenvios_sede_destino_nombre( $lang ) ) );

	/* 1) Nueva home de la ruta. */
	$vieja = get_post( $portada );
	$home  = wp_insert_post( array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => 'Inicio',
		'post_name'    => 'inicio-' . ( $pais !== '' ? $pais : $lang ),
		'post_content' => $vieja->post_content,
	), true );
	if ( is_wp_error( $home ) ) { $res['nota'] = $home->get_error_message(); return $res; }
	foreach ( get_post_meta( $portada ) as $k => $v ) {
		if ( strpos( $k, 'grenvios_' ) !== 0 ) continue;          // campos y carrusel de la home
		update_post_meta( $home, $k, maybe_unserialize( $v[0] ) );
	}
	grenvios_i18n_stamp_master( $home, 'home' );
	pll_set_post_language( $home, $lang );
	$tr = pll_get_post_translations( $home_m );
	$tr[ $lang ] = $home;
	pll_save_post_translations( $tr );

	/* 3) Fuera el espejo que ocupaba la traducción del destino en esta ruta. */
	$espejo = (int) pll_get_post( $dest_m->ID, $lang );
	if ( $espejo && $espejo !== $portada && function_exists( 'grenvios_espejo_es' ) && grenvios_espejo_es( $espejo ) ) {
		wp_delete_post( $espejo, true );
	}

	/* 2) La antigua portada pasa a ser la página de destino del país. */
	grenvios_i18n_stamp_master( $portada, $dest_m->post_name );
	$trd = pll_get_post_translations( $dest_m->ID );
	$trd[ $lang ] = $portada;
	pll_save_post_translations( $trd );
	update_post_meta( $portada, GRENVIOS_DESTINO_RUTA_META, $dslug );

	/* Al sacar la antigua portada del grupo de traducciones de la home, Polylang
	 * puede dejar ese grupo roto (medido: se quedaba solo con Perú y las rutas
	 * perdían su portada). Se vuelve a guardar completo, con las homes nuevas. */
	$tr = pll_get_post_translations( $home_m );
	$tr[ grenvios_i18n_default() ] = $home_m;
	foreach ( grenvios_sedes() as $l => $_s ) {
		if ( $l === grenvios_i18n_default() ) continue;
		$h = get_posts( array( 'post_type' => 'page', 'post_status' => 'publish', 'numberposts' => 1, 'lang' => $l,
			'meta_key' => GRENVIOS_MASTER_META, 'meta_value' => 'home', 'fields' => 'ids' ) );
		if ( $h ) $tr[ $l ] = (int) $h[0];
	}
	$tr[ $lang ] = $home;
	pll_save_post_translations( $tr );

	// Bloque de país: la home lo recibe; la de destino ya es íntegramente del país.
	if ( function_exists( 'grenvios_pais_aplicar' ) ) {
		grenvios_pais_aplicar( $home, 'home', $lang );
		grenvios_pais_aplicar( $portada, $dest_m->post_name, $lang );
	}

	$res['hecho'] = true;
	$res['home'] = $home;
	$res['destino'] = $portada;
	return $res;
}

/* /ar/ sirve la portada directamente (sin saltar a /ar/inicio-argentina/). */
function grenvios_pll_home_sin_redireccion() {
	$o = get_option( 'polylang' );
	if ( is_array( $o ) && empty( $o['redirect_lang'] ) ) {
		$o['redirect_lang'] = 1;
		update_option( 'polylang', $o );
		// Polylang guarda la URL de portada de cada idioma en caché.
		if ( function_exists( 'PLL' ) && isset( PLL()->model ) && method_exists( PLL()->model, 'clean_languages_cache' ) ) PLL()->model->clean_languages_cache();
		flush_rewrite_rules( false );
	}
}

add_action( 'grenvios_ruta_duplicada', function ( $lang ) {
	grenvios_pll_home_sin_redireccion();
	grenvios_ruta_separar_home( $lang );
	flush_rewrite_rules( false );
}, 30 );

/* La home también responde en /ar/inicio-argentina/: una sola URL, /ar/. */
add_action( 'template_redirect', function () {
	if ( is_admin() || ! function_exists( 'pll_home_url' ) || ! is_page() ) return;
	// Con la URL con slug WordPress no la trata como portada: se compara el ID.
	$lang = function_exists( 'grenvios_i18n_current' ) ? grenvios_i18n_current() : '';
	if ( $lang === '' || $lang === grenvios_i18n_default() ) return;
	if ( (int) get_queried_object_id() !== (int) pll_get_post( (int) get_option( 'page_on_front' ), $lang ) ) return;
	$dest = pll_home_url( $lang );
	$pide = strtok( (string) wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ), '?' );
	if ( untrailingslashit( (string) wp_parse_url( $dest, PHP_URL_PATH ) ) !== untrailingslashit( $pide ) ) {
		wp_safe_redirect( $dest, 301 );
		exit;
	}
}, 1 );
