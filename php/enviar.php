<?php
/**
 * Envío común de los formularios que no pasan por WordPress (cotizador y
 * contacto). Antes usaban mail() de PHP directamente: en el hosting el
 * servidor respondía «¡Gracias!» pero el correo no llegaba, y no quedaba copia.
 * Ahora cargan WordPress, envían con wp_mail() —igual que el formulario de
 * solicitud, que sí llega— y guardan cada envío en el panel («Solicitudes»).
 * Si WordPress no se puede cargar, se vuelve a mail() como antes.
 */

/* Carga WordPress subiendo carpetas hasta encontrar wp-load.php. */
function grenvios_form_cargar_wp() {
	if ( function_exists( 'wp_mail' ) ) return true;
	$dir = __DIR__;
	for ( $i = 0; $i < 8; $i++ ) {
		$dir = dirname( $dir );
		if ( file_exists( $dir . '/wp-load.php' ) ) {
			if ( ! defined( 'WP_USE_THEMES' ) ) define( 'WP_USE_THEMES', false );
			require_once $dir . '/wp-load.php';
			return function_exists( 'wp_mail' );
		}
	}
	return false;
}

/* Envía y guarda. Devuelve true si el correo salió (o si al menos quedó guardado). */
function grenvios_form_enviar( $asunto, $cuerpo, $nombre, $email, $tipo ) {
	if ( ! grenvios_form_cargar_wp() ) {
		$h  = "From: Grenvios <info@grenvios.com>\r\n";
		if ( $email !== '' ) $h .= "Reply-To: $nombre <$email>\r\n";
		$h .= "MIME-Version: 1.0\r\nContent-Type: text/plain; charset=UTF-8\r\n";
		$a = function_exists( 'mb_encode_mimeheader' ) ? mb_encode_mimeheader( $asunto, 'UTF-8' ) : $asunto;
		return mail( 'info@grenvios.com', $a, $cuerpo, $h );
	}

	$origen = isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( $_SERVER['HTTP_REFERER'] ) ) : '';
	if ( $origen !== '' ) $cuerpo .= "\nEnviado desde: $origen\n";

	/* Copia en el panel (tipo «gr_solicitud», el mismo del formulario de solicitud). */
	$id = 0;
	if ( post_type_exists( 'gr_solicitud' ) ) {
		$id = (int) wp_insert_post( array(
			'post_type'    => 'gr_solicitud',
			'post_status'  => 'private',
			'post_title'   => sanitize_text_field( $tipo . ' · ' . $nombre ),
			'post_content' => sanitize_textarea_field( $cuerpo ),
		) );
	}

	/* Mismos destinatarios que el formulario de solicitud: el correo principal y,
	 * si es distinto, el de la sede del país. */
	$para = function_exists( 'grenvios_rd_destinatario' ) ? grenvios_rd_destinatario() : array( 'info@grenvios.com' );
	$headers = array();
	if ( is_email( $email ) ) $headers[] = 'Reply-To: ' . $nombre . ' <' . $email . '>';
	$ok = wp_mail( $para, $asunto, $cuerpo, $headers );
	if ( $id ) update_post_meta( $id, '_gr_correo_enviado', $ok ? 1 : 0 );
	return $ok || $id > 0;
}
