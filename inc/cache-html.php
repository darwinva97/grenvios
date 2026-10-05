<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Caché de las transformaciones de HTML
 * ══════════════════════════════════════════════════════════════════════════
 *
 * MEDIDO ANTES DE TOCAR NADA (con ?gr_perf=1, en /cl/cuanto-cuesta-enviar-a-chile/):
 *
 *     1,42 s en total · 221 consultas · 0,28 s de SQL
 *       0,18 s  wp_head()
 *       0,08 s  submenú de destinos del encabezado
 *       0,17 s  traducción de textos fijos del encabezado
 *       0,16 s  partial del contenido (incluye el cotizador del hero)
 *       0,10 s  traducción de textos fijos del contenido
 *
 * Es decir: el 90 % del tiempo era PHP, no base de datos, y la mitad de ese PHP
 * se iba en tres transformaciones que se repiten en CADA visita y que siempre
 * devuelven lo mismo para la misma entrada:
 *
 *   · traducir los textos fijos del diseño a la ruta activa
 *   · reescribir los enlaces a las URL de esa ruta
 *   · reescribir los enlaces a /destinos/ hacia la ficha de cada país
 *
 * Las tres son funciones puras de (HTML de entrada, ruta). Aquí se memorizan:
 * misma entrada, misma salida, sin recorrer el HTML otra vez.
 *
 * QUÉ **NO** SE CACHEA, a propósito
 *   · Nada si hay sesión iniciada: quien edita tiene que ver lo que acaba de
 *     guardar, sin excepciones.
 *   · El selector de idioma, que apunta a la traducción de la página ACTUAL:
 *     cachearlo congelaría ese enlace en la primera página que se visitara.
 *   · Bloques grandes (más de 64 KB): son el cuerpo completo de una página, y
 *     guardarlos llenaría la tabla de opciones para ahorrar milisegundos.
 *
 * INVALIDACIÓN: la clave lleva la versión del tema y un número que se mueve
 * cada vez que se guarda una entrada, una página, un término o una opción del
 * tema. Un cambio de contenido, de menú o de destino sirve HTML nuevo en la
 * siguiente visita; no hay que acordarse de vaciar nada.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Versión de la caché: cambia con el tema y con cualquier guardado. */
function grenvios_cache_ver() {
	static $v = null;
	if ( $v === null ) {
		$n = get_option( 'grenvios_cache_ver' );
		$v = ( defined( 'LOGISKO_VER' ) ? LOGISKO_VER : '0' ) . '-' . ( $n ? $n : '1' );
	}
	return $v;
}

function grenvios_cache_bump() {
	update_option( 'grenvios_cache_ver', (string) time(), false );
}

add_action( 'save_post',      'grenvios_cache_bump' );
add_action( 'deleted_post',   'grenvios_cache_bump' );
add_action( 'edited_term',    'grenvios_cache_bump' );
add_action( 'created_term',   'grenvios_cache_bump' );
add_action( 'delete_term',    'grenvios_cache_bump' );
add_action( 'switch_theme',   'grenvios_cache_bump' );
add_action( 'customize_save_after', 'grenvios_cache_bump' );
add_action( 'updated_option', function ( $opcion ) {
	if ( strpos( (string) $opcion, 'grenvios_' ) === 0 && $opcion !== 'grenvios_cache_ver' ) {
		grenvios_cache_bump();
	}
} );

/**
 * Memoriza el resultado de una transformación pura de HTML.
 *
 * @param string   $bucket Nombre de la transformación (entra en la clave).
 * @param string   $html   HTML de entrada.
 * @param string   $extra  Lo que la haga distinta además del HTML (la ruta).
 * @param callable $fn     La transformación de verdad.
 */
function grenvios_cache_html( $bucket, $html, $extra, $fn ) {
	if ( ! is_string( $html ) || $html === '' ) return $html;

	/* Fuera de la caché: administración, sesión iniciada y bloques grandes. */
	$largo = strlen( $html );
	if ( $largo > 65536 || is_admin() || is_user_logged_in() ) return $fn( $html );

	/* Dentro de la misma petición, el encabezado y el pie repiten la misma
	 * entrada varias veces: eso se resuelve en memoria, sin tocar la base. */
	static $mem = array();
	$clave = $bucket . '|' . $extra . '|' . md5( $html );
	if ( isset( $mem[ $clave ] ) ) return $mem[ $clave ];

	$t = 'gr_h_' . md5( $clave . '|' . grenvios_cache_ver() );
	$hit = get_transient( $t );
	if ( is_string( $hit ) ) return $mem[ $clave ] = $hit;

	$out = $fn( $html );
	if ( is_string( $out ) ) {
		set_transient( $t, $out, 12 * HOUR_IN_SECONDS );
		$mem[ $clave ] = $out;
	}
	return $out;
}

/* Limpieza: los transients caducan solos, pero una versión nueva deja atrás los
 * de la anterior. Una vez al día se barren los que ya no sirven. */
add_action( 'grenvios_cache_limpiar', function () {
	global $wpdb;
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_gr_h_%' OR option_name LIKE '_transient_timeout_gr_h_%'" );
} );

add_action( 'init', function () {
	if ( ! wp_next_scheduled( 'grenvios_cache_limpiar' ) ) {
		wp_schedule_event( time() + DAY_IN_SECONDS, 'daily', 'grenvios_cache_limpiar' );
	}
} );
