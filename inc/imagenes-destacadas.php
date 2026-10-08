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

/* 2: recorre todo por ID (la v1 saltaba las que tenían _thumbnail_id vacío o a 0). */
define( 'GRENVIOS_DESTACADAS_V', 2 );

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
	$t = (int) get_post_thumbnail_id( $post_id );
	if ( $t && get_post( $t ) && wp_attachment_is_image( $t ) ) return false;   // ya tiene una imagen válida
	/* La portada principal ya comparte la foto real de su carrusel. */
	if ( (int) $post_id === (int) get_option( 'page_on_front' ) ) return false;
	$id = grenvios_dest_para( $post_id );
	return $id ? (bool) set_post_thumbnail( $post_id, $id ) : false;
}

/* Tanda en el panel: recorre TODAS las páginas y entradas por ID y comprueba
 * de verdad si tienen imagen (has_post_thumbnail). Buscar «sin _thumbnail_id»
 * no basta: hay copias con el campo vacío, a 0 o apuntando a una imagen
 * borrada, y esas se quedaban sin imagen. */
add_action( 'admin_init', function () {
	if ( wp_doing_ajax() || ! current_user_can( 'manage_options' ) ) return;
	if ( (int) get_option( 'grenvios_destacadas_v' ) >= GRENVIOS_DESTACADAS_V ) return;
	if ( ! grenvios_dest_medios() ) return;   // sin fotos subidas no hay qué asignar (ver aviso)
	global $wpdb;
	$desde = (int) get_option( 'grenvios_destacadas_cursor', 0 );
	$ids   = $wpdb->get_col( $wpdb->prepare(
		"SELECT ID FROM {$wpdb->posts} WHERE post_type IN ('page','post') AND post_status='publish' AND ID > %d ORDER BY ID ASC LIMIT 300", $desde
	) );
	foreach ( $ids as $id ) grenvios_dest_asignar( (int) $id );
	if ( $ids ) update_option( 'grenvios_destacadas_cursor', (int) end( $ids ), false );
	if ( count( $ids ) < 300 ) {
		update_option( 'grenvios_destacadas_v', GRENVIOS_DESTACADAS_V );
		delete_option( 'grenvios_destacadas_cursor' );
		if ( function_exists( 'grenvios_cache_bump' ) ) grenvios_cache_bump();
	}
} );

/* Al abrir una página o entrada en el editor, si no tiene imagen, se le pone ya. */
add_action( 'load-post.php', function () {
	$id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0;
	if ( $id && current_user_can( 'edit_post', $id ) && get_post_status( $id ) === 'publish' && in_array( get_post_type( $id ), array( 'page', 'post' ), true ) ) {
		grenvios_dest_asignar( $id );
	}
} );

/* El editor de bloques guarda «featured_media» DESPUÉS de save_post: se vuelve a
 * comprobar cuando ya terminó. */
foreach ( array( 'page', 'post' ) as $tipo ) {
	add_action( 'rest_after_insert_' . $tipo, function ( $post ) {
		if ( $post && $post->post_status === 'publish' ) grenvios_dest_asignar( $post->ID );
	} );
}

/* Si las fotos de ejemplo no se pudieron subir, que se vea. */
add_action( 'admin_notices', function () {
	if ( ! current_user_can( 'manage_options' ) || grenvios_dest_medios() ) return;
	echo '<div class="notice notice-warning"><p><strong>Grenvíos:</strong> no se pudieron subir a la Biblioteca de medios las fotos de ejemplo para las imágenes destacadas. Revisa que la carpeta de subidas tenga permisos de escritura y que el servidor admita imágenes WebP.</p></div>';
} );

/* Lo nuevo (o lo que se guarde sin imagen) también la recibe. */
add_action( 'save_post', function ( $post_id, $post ) {
	if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) return;
	if ( ! in_array( $post->post_type, array( 'page', 'post' ), true ) || $post->post_status !== 'publish' ) return;
	grenvios_dest_asignar( $post_id );
}, 20, 2 );
