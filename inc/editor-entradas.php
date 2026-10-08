<?php
/**
 * Panel «Editar página» en las entradas del blog: imagen, título y extracto.
 *
 * Son los tres datos de la tarjeta de la entrada en los listados (blog de cada
 * ruta, «Del blog», guías del clúster) y de la cabecera de la propia entrada.
 * La imagen es la imagen destacada de WordPress: así la usan todas esas
 * tarjetas, el og:image y el schema sin tocar nada más. Cada país tiene su
 * propia copia de la entrada, así que cada una lleva su imagen.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

function grenvios_ee_es_entrada( $post_id ) {
	return $post_id && get_post_type( $post_id ) === 'post';
}

/* ID de adjunto a partir de la URL que devuelve la biblioteca de medios, que
 * puede ser la de un tamaño recortado (foto-1024x683.jpg). */
function grenvios_ee_adjunto( $url ) {
	$id = attachment_url_to_postid( $url );
	if ( ! $id ) $id = attachment_url_to_postid( preg_replace( '~-\d+x\d+(?=\.[a-z0-9]+$)~i', '', $url ) );
	if ( ! $id ) $id = attachment_url_to_postid( preg_replace( '~-scaled(?=\.[a-z0-9]+$)~i', '', $url ) );
	return (int) $id;
}

add_action( 'grenvios_editor_secciones', function ( $slug, $post_id = 0, $render_field = null ) {
	if ( ! is_callable( $render_field ) || ! grenvios_ee_es_entrada( $post_id ) ) return;
	$p   = get_post( $post_id );
	$img = (string) get_the_post_thumbnail_url( $post_id, 'large' );
	echo '<div class="nep-accordion" data-sel=".gr-bd-hero"><button class="nep-acc-header" type="button"><span>🖼️ Esta entrada: imagen, título y extracto</span><i class="fa-solid fa-chevron-down"></i></button>'
		. '<div class="nep-acc-body"><div class="nep-grid">'
		. $render_field( 'post_imagen', 'Imagen de la entrada', 'image', $img, 'Sale en la tarjeta de los listados del blog, en la cabecera de esta entrada y al compartirla. Solo cambia la de esta ruta.' )
		. $render_field( 'post_titulo', 'Título', 'text', $p->post_title )
		. $render_field( 'post_extracto', 'Extracto (texto de la tarjeta)', 'textarea', $p->post_excerpt, 'Vacío = se usa el principio del artículo.' )
		. '</div></div></div>';
}, 5, 3 );

add_action( 'grenvios_editor_guardar', function ( $request, $post_id ) {
	if ( ! grenvios_ee_es_entrada( $post_id ) ) return;
	$f = $request->get_param( 'fields' );
	if ( ! is_array( $f ) ) return;

	if ( array_key_exists( 'post_imagen', $f ) ) {
		$url = esc_url_raw( (string) $f['post_imagen'] );
		if ( $url === '' ) {
			delete_post_thumbnail( $post_id );
		} else {
			$id = grenvios_ee_adjunto( $url );
			if ( $id ) set_post_thumbnail( $post_id, $id );
		}
		delete_post_meta( $post_id, 'grenvios_post_imagen' );   // la fuente es la imagen destacada
	}

	$cambios = array();
	if ( isset( $f['post_titulo'] ) && trim( (string) $f['post_titulo'] ) !== '' ) $cambios['post_title'] = sanitize_text_field( (string) $f['post_titulo'] );
	if ( isset( $f['post_extracto'] ) ) $cambios['post_excerpt'] = sanitize_textarea_field( (string) $f['post_extracto'] );
	if ( $cambios ) wp_update_post( array( 'ID' => $post_id ) + $cambios );
	delete_post_meta( $post_id, 'grenvios_post_titulo' );
	delete_post_meta( $post_id, 'grenvios_post_extracto' );
}, 10, 2 );
