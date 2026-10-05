<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Jerarquía de encabezados: los antetítulos no son H3
 * ══════════════════════════════════════════════════════════════════════════
 *
 * La maqueta pinta el antetítulo de cada sección («Nuestros servicios de
 * envío», «Cómo leer estas rutas») como `<h3 class="sub-heading">` ENCIMA de su
 * `<h2>`. Para un buscador eso es un H3 huérfano antes de cada H2: el esquema de
 * la página empieza cada sección por el nivel equivocado y con una frase de
 * relleno como encabezado.
 *
 * El antetítulo pasa a ser un párrafo con la misma clase. El aspecto no cambia
 * (style.css → «Portada: textos que parecían encabezados»).
 *
 * Se corrige a la salida, no solo en los .html del tema, porque las páginas
 * editables guardan una copia de ese HTML en post_content y no se debe pisar lo
 * que el cliente haya editado.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function grenvios_jerarquia_antetitulos( $html ) {
	if ( ! is_string( $html ) || strpos( $html, 'sub-heading' ) === false ) return $html;
	$html = preg_replace(
		'~<h3(\s+class="sub-heading[^"]*"[^>]*)>(.*?)</h3>~s',
		'<p$1>$2</p>',
		$html
	);
	/* El camión del antetítulo es decorativo (y está oculto en todo el sitio):
	 * su alt «truck», en inglés, se leía como contenido de la imagen. */
	return preg_replace( '~(<img\s+class="sh-truck"[^>]*?)\salt="[^"]*"~', '$1 alt=""', $html );
}
add_filter( 'grenvios_html_final', 'grenvios_jerarquia_antetitulos', 20 );
add_filter( 'grenvios_content_html', 'grenvios_jerarquia_antetitulos', 20 );
