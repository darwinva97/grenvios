<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Medidor de rendimiento, a petición
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Añadiendo `?gr_perf=1` a cualquier URL, la página termina con un comentario
 * HTML que dice cuánto ha tardado, cuántas consultas ha hecho y cuáles han sido
 * las cinco más lentas. No se activa nunca solo: sin el parámetro no cuesta ni
 * una comprobación por petición más allá de un `isset()`.
 *
 * Existe porque optimizar a ciegas es perder el tiempo: en este tema el coste
 * está repartido entre el bloque por país, el enlazado interno y los bloques del
 * blog, y sin números no se sabe cuál de los tres hay que tocar.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Marcas de tiempo dentro de la plantilla. La función existe siempre —las
 * plantillas la llaman sin comprobar nada— pero no hace nada si el medidor está
 * apagado, que es el caso en el 100 % de las visitas reales. */
function grenvios_perf_mark( $etiqueta ) {
	if ( ! isset( $_GET['gr_perf'] ) ) return;
	global $grenvios_perf_marks;
	if ( ! is_array( $grenvios_perf_marks ) ) $grenvios_perf_marks = array();
	$grenvios_perf_marks[] = array( $etiqueta, microtime( true ) );
}

if ( ! isset( $_GET['gr_perf'] ) ) return;

if ( ! defined( 'SAVEQUERIES' ) ) define( 'SAVEQUERIES', true );

add_action( 'shutdown', function () {
	global $wpdb;
	$t = timer_stop( 0, 3 );
	$n = isset( $wpdb->queries ) ? count( $wpdb->queries ) : 0;

	echo "\n<!-- grenvios perf: {$t}s · {$n} consultas\n";

	global $grenvios_perf_marks;
	if ( ! empty( $grenvios_perf_marks ) ) {
		$prev = null;
		foreach ( $grenvios_perf_marks as $m ) {
			if ( $prev !== null ) printf( "  %-22s %0.3fs
", $prev[0], $m[1] - $prev[1] );
			$prev = $m;
		}
	}

	if ( $n ) {
		$q = $wpdb->queries;
		usort( $q, function ( $a, $b ) { return $b[1] <=> $a[1]; } );
		$total = 0;
		foreach ( $wpdb->queries as $x ) $total += $x[1];
		echo '  sql total: ' . number_format( $total, 3 ) . "s\n";
		foreach ( array_slice( $q, 0, 5 ) as $x ) {
			$sql = preg_replace( '/\s+/', ' ', substr( $x[0], 0, 160 ) );
			$fn  = preg_replace( '/\s+/', ' ', substr( (string) $x[2], 0, 120 ) );
			echo '  ' . number_format( $x[1], 4 ) . "s  {$sql}\n      {$fn}\n";
		}
	}
	echo "-->\n";
}, 99999 );

/* ─────────────────────────────────────────────────────────────────────────
 *  Desglose de wp_head
 *
 *  wp_head() es la parte más cara de la cabecera y es una caja negra: dentro
 *  hay veinte funciones del núcleo, del tema y de los plugins. Con el medidor
 *  encendido se envuelve cada una para saber cuál cuesta, que es la única
 *  forma de optimizar sin adivinar.
 * ───────────────────────────────────────────────────────────────────────── */
add_action( 'wp', function () {
	global $wp_filter, $grenvios_perf_head;
	if ( empty( $wp_filter['wp_head'] ) ) return;
	$grenvios_perf_head = array();

	foreach ( $wp_filter['wp_head']->callbacks as $prio => $cbs ) {
		foreach ( $cbs as $id => $cb ) {
			$fn = $cb['function'];
			if ( is_string( $fn ) )            $nombre = $fn;
			elseif ( is_array( $fn ) )         $nombre = ( is_object( $fn[0] ) ? get_class( $fn[0] ) : (string) $fn[0] ) . '::' . $fn[1];
			else                               $nombre = 'closure@' . $prio;

			remove_action( 'wp_head', $fn, $prio );
			add_action( 'wp_head', function () use ( $fn, $nombre ) {
				global $grenvios_perf_head;
				$t0 = microtime( true );
				call_user_func( $fn );
				$d  = microtime( true ) - $t0;
				if ( ! isset( $grenvios_perf_head[ $nombre ] ) ) $grenvios_perf_head[ $nombre ] = 0;
				$grenvios_perf_head[ $nombre ] += $d;
			}, $prio, 0 );
		}
	}
}, 1 );

add_action( 'shutdown', function () {
	global $grenvios_perf_head;
	if ( empty( $grenvios_perf_head ) ) return;
	arsort( $grenvios_perf_head );
	echo "\n<!-- grenvios perf · wp_head\n";
	$i = 0;
	foreach ( $grenvios_perf_head as $n => $t ) {
		if ( $t < 0.002 || $i++ > 9 ) continue;
		printf( "  %0.3fs  %s\n", $t, $n );
	}
	echo "-->\n";
}, 99998 );
