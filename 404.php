<?php
/**
 * 404 — renderiza el contenido real de 404-error.html.
 * @package logisko
 */
get_header();
if ( ! logisko_render_content( '404' ) ) {
	echo '<section class="error-page padding"><div class="container"><div class="error-wrapper text-center">';
	echo '<h2>' . esc_html( grenvios_t( '¡Vaya! No encontramos esta página.' ) ) . '</h2>';
	echo '<a href="' . esc_url( home_url( '/' ) ) . '" class="default-btn">' . esc_html( grenvios_t( 'Volver al inicio' ) ) . '</a>';
	echo '</div></div></section>';
}
get_footer();
