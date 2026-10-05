<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Tarjetas de servicio sin foto de relleno (revisión UX, 2026-09-28)
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Las tarjetas de servicio (Inicio y /servicios/) traían por defecto las
 * imágenes de la plantilla —rectángulos grises con «1000X650»—, que nunca se
 * sustituyeron por fotos reales (ver ASSETS-IMAGENES.md). Un rectángulo de
 * relleno en la primera tarjeta que ve el cliente resta más confianza que no
 * poner imagen.
 *
 * Mientras la foto sea la de relleno, la tarjeta se pinta sin foto: icono
 * dentro de la tarjeta, la barra de marca arriba y la misma altura que sus
 * vecinas. En cuanto se sube una foto real (panel «Editar página» o
 * Personalizar → Imágenes), vuelve a salir sola: se compara la URL final,
 * después de aplicar esas sustituciones.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Archivos de la plantilla que son relleno, no fotos. */
function grenvios_img_relleno_patron() {
	return '(?:post-\d+|content-bg-\d+|page-banner|slider-bg|hero-background|testimonial-bg|banner-add)\.(?:jpe?g|png|webp)';
}

add_filter( 'grenvios_html_final', function ( $html ) {
	if ( ! is_string( $html ) || strpos( $html, 'service-thumb' ) === false ) return $html;
	$re = '~(<div class="service-item)([^"]*)("[^>]*>)\s*<div class="service-thumb">\s*<img[^>]+src="[^"]*/assets/img/' . grenvios_img_relleno_patron() . '"[^>]*>\s*</div>~i';
	return preg_replace( $re, '$1$2 service-item--sin-foto$3', $html );
}, 25 );

/* La figura del repartidor de las llamadas a la acción (Servicios, Nosotros,
 * Contacto) es por defecto una SILUETA GRIS de la plantilla, que además se monta
 * sobre la sección de arriba. Mientras sea esa, no se pinta: la llamada queda
 * con su texto y sus botones. Si se sube una figura real, vuelve a salir. */
add_filter( 'grenvios_html_final', function ( $html ) {
	if ( ! is_string( $html ) || strpos( $html, 'delivery-men-2.png' ) === false ) return $html;
	return preg_replace( '~<div class="cta-men[^"]*"[^>]*background-image:\s*url\(\s*[\'"]?[^)\'"]*/assets/img/delivery-men-2\.png[\'"]?\s*\)[^>]*>\s*</div>~i', '', $html );
}, 25 );

add_action( 'wp_head', function () {
	if ( is_admin() ) return;
	echo '<style id="grenvios-tarjetas-css">'
		. '.service-item--sin-foto{height:100%;box-shadow:0 14px 40px rgba(0,0,0,.05);border:1px solid #efeae6}'
		. '.service-item--sin-foto .service-content{padding:34px 32px 30px;height:100%;display:flex;flex-direction:column}'
		. '.service-item--sin-foto .service-content:before{top:0;left:0;transform:none;width:64px}'
		. '.service-item--sin-foto:hover .service-content:before{width:120px}'
		. '.service-item--sin-foto .service-content i{position:static;width:68px;height:68px;font-size:32px;border:0;box-shadow:none;margin:0 0 22px;border-radius:var(--gr-r-md,12px)}'
		. '.service-item--sin-foto .service-content p{flex:1 1 auto}'
		. '.service-item--sin-foto .service-content .read-more{align-self:flex-start}'
		. '@media(max-width:767px){.service-item--sin-foto .service-content{padding:28px 24px 26px}}'
		. '</style>';
}, 104 );
