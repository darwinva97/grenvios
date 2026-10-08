<?php
/**
 * Imagen destacada por defecto en todas las páginas y entradas (2026-10-08).
 *
 * Hasta ahora ninguna página tenía imagen destacada: el tema elegía una foto de
 * ejemplo al pintar, pero en el editor de WordPress («Set featured image») no
 * aparecía nada y el cliente no podía ver ni cambiar cuál se usaba al compartir
 * el enlace o en las tarjetas.
 *
 * Ahora se ASIGNA de verdad:
 *   1) Las fotos de ejemplo del tema se suben una vez a la Biblioteca de medios
 *      (sin «bodega», que lleva el logo de otra empresa).
 *   2) Cada página y entrada publicada SIN imagen destacada recibe la foto que
 *      mejor encaja con su título y su slug (grenvios_ej_por_tema): la ciudad
 *      del país en las fichas, documentos en las de documentos…
 *   3) Las que se creen o guarden después sin imagen, igual.
 *   Nunca se toca una que ya tenga imagen: lo que el cliente ponga, manda.
 *
 * Se hace por tandas en el panel de administración (200 por carga) para no
 * agotar el tiempo de PHP con casi 1.400 contenidos.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'GRENVIOS_DESTACADAS_V', 1 );

/* Nombre de foto de ejemplo => ID de adjunto (sube las que falten). */
function grenvios_dest_medios() {
	static $hecho = null;
	if ( $hecho !== null ) return $hecho;
	$m = get_option( 'grenvios_dest_medios', array() );
	if ( ! is_array( $m ) ) $m = array();
	if ( ! function_exists( 'grenvios_ej_banco' ) ) return $m;
	foreach ( grenvios_ej_banco() as $n ) {
		if ( $n === 'bodega' ) continue;
		if ( ! empty( $m[ $n ] ) && get_post( (int) $m[ $n ] ) ) continue;
		$f = get_template_directory() . '/assets/img/ejemplo/' . $n . '.webp';
		if ( ! file_exists( $f ) ) continue;
		$up = wp_upload_bits( 'grenvios-' . $n . '.webp', null, file_get_contents( $f ) );
		if ( ! empty( $up['error'] ) ) continue;
		$id = wp_insert_attachment( array(
			'post_mime_type' => 'image/webp',
			'post_title'     => 'Grenvíos · ' . ucfirst( str_replace( '-', ' ', $n ) ),
			'post_status'    => 'inherit',
		), $up['file'] );
		if ( ! $id || is_wp_error( $id ) ) continue;
		require_once ABSPATH . 'wp-admin/includes/image.php';
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $up['file'] ) );
		update_post_meta( $id, '_wp_attachment_image_alt', '' );   // decorativa
		$m[ $n ] = (int) $id;
	}
	update_option( 'grenvios_dest_medios', $m, false );
	return $hecho = $m;
}

/* Foto que corresponde a un contenido (ID de adjunto o 0). */
function grenvios_dest_para( $post ) {
	$post = get_post( $post );
	if ( ! $post || ! function_exists( 'grenvios_ej_por_tema' ) ) return 0;
	$texto = $post->post_title . ' ' . str_replace( '-', ' ', $post->post_name );
	/* «sobre nosotros» no es correspondencia: la regla «sobre» → foto de sobres. */
	$texto = preg_replace( '~\bsobre nosotros\b~iu', 'nosotros equipo', $texto );
	$url   = grenvios_ej_por_tema( $texto, 'almacen', true );
	$n     = basename( (string) wp_parse_url( $url, PHP_URL_PATH ), '.webp' );
	if ( $n === 'bodega' ) $n = 'almacen';
	$m = grenvios_dest_medios();
	return isset( $m[ $n ] ) ? (int) $m[ $n ] : ( isset( $m['almacen'] ) ? (int) $m['almacen'] : 0 );
}

function grenvios_dest_asignar( $post_id ) {
	if ( has_post_thumbnail( $post_id ) ) return false;
	/* La portada principal ya comparte la foto real de su carrusel. */
	if ( (int) $post_id === (int) get_option( 'page_on_front' ) ) return false;
	$id = grenvios_dest_para( $post_id );
	return $id ? (bool) set_post_thumbnail( $post_id, $id ) : false;
}

/* Tanda en el panel hasta que no quede ninguna sin imagen. */
add_action( 'admin_init', function () {
	if ( wp_doing_ajax() || ! current_user_can( 'manage_options' ) ) return;
	if ( (int) get_option( 'grenvios_destacadas_v' ) >= GRENVIOS_DESTACADAS_V ) return;
	$ids = get_posts( array(
		'post_type'        => array( 'page', 'post' ),
		'post_status'      => 'publish',
		'numberposts'      => 200,
		'fields'           => 'ids',
		'lang'             => '',
		'suppress_filters' => true,
		'meta_query'       => array( array( 'key' => '_thumbnail_id', 'compare' => 'NOT EXISTS' ) ),
	) );
	foreach ( $ids as $id ) grenvios_dest_asignar( (int) $id );
	if ( count( $ids ) < 200 ) {
		update_option( 'grenvios_destacadas_v', GRENVIOS_DESTACADAS_V );
		if ( function_exists( 'grenvios_cache_bump' ) ) grenvios_cache_bump();
	}
} );

/* Lo nuevo (o lo que se guarde sin imagen) también la recibe. */
add_action( 'save_post', function ( $post_id, $post ) {
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) return;
	if ( ! in_array( $post->post_type, array( 'page', 'post' ), true ) || $post->post_status !== 'publish' ) return;
	grenvios_dest_asignar( $post_id );
}, 20, 2 );
