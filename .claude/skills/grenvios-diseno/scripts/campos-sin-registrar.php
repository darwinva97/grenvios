<?php
/* Solo terminal: si la carpeta del tema se sube a un servidor, esto no debe
 * poder ejecutarse desde el navegador. */
if ( PHP_SAPI !== 'cli' ) { http_response_code( 404 ); exit; }
/**
 * Lista los textos que el tema pinta con grenvios_tf() o grenvios_seo_extra_lista()
 * pero que NO están en el registro del panel (grenvios_text_fields()): se ven en la
 * web y no se pueden editar desde «Editar página».
 *
 *   php campos-sin-registrar.php [host]
 *
 * Salida vacía = todo registrado. Las claves que terminan en un número y se usan
 * como prefijo ($p[0] . '_t') salen como falso positivo si están registradas con
 * sus sufijos: se comprueba abajo.
 */
$host = $argv[1] ?? 'greenvios.localhost';
$_SERVER['HTTP_HOST'] = $host; $_SERVER['REQUEST_SCHEME'] = 'http'; $_SERVER['REQUEST_URI'] = '/';
define( 'WP_USE_THEMES', false );
require realpath( __DIR__ . '/../../../../../../../wp-load.php' );

$reg   = array_flip( array_keys( grenvios_text_fields() ) );
$found = array();
foreach ( glob( get_template_directory() . '/inc/*.php' ) as $f ) {
	$c = file_get_contents( $f ); $b = basename( $f );
	preg_match_all( '/grenvios_tf\(\s*\'([a-z0-9_]+)\'/', $c, $m );
	foreach ( $m[1] as $k ) $found[ $k ][ $b ] = 1;
	preg_match_all( '/,\s*\'([a-z0-9]+_lista[0-9]*)\'\s*\)/', $c, $m );
	foreach ( $m[1] as $k ) $found[ $k ][ $b ] = 1;
	// Helpers de páginas nuevas: grenvios_pn_intro/sec_open/panel/nota('clave', …)
	preg_match_all( '/grenvios_pn_(?:intro|panel|nota)\(\s*\'([a-z0-9_]+)\'/', $c, $m );
	foreach ( $m[1] as $k ) $found[ $k ][ $b ] = 1;
}
$falta = array();
foreach ( $found as $k => $fs ) {
	if ( isset( $reg[ $k ] ) ) continue;
	if ( isset( $reg[ $k . '_t' ] ) && isset( $reg[ $k . '_x' ] ) ) continue;   // prefijo de paso
	$falta[ implode( ',', array_keys( $fs ) ) ][] = $k;
}
foreach ( $falta as $f => $ks ) echo "$f (" . count( $ks ) . '): ' . implode( ' ', $ks ) . "\n";
exit( $falta ? 1 : 0 );
