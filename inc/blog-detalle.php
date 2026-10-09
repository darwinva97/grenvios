<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Detalle de entrada del blog v2 (single.php)
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Revisión (2026-10-02, captura de /ec/enviar-ropa-y-calzado-a-ecuador/):
 *   · el fondo del banner era un relleno de la plantilla («1920X645»);
 *   · el H1 salía en Mayúsculas De Cada Palabra (text-transform del tema);
 *   · sin índice, sin tiempo de lectura, sin imagen ni autor reconocible
 *     («prueba», fecha en inglés: el paquete es_PE no está instalado);
 *   · dos bloques de preguntas frecuentes seguidos (la guía y el bloque de país);
 *   · formulario de comentarios en inglés.
 *
 * Lo que hace este módulo (todo lo usa single.php):
 *   · cabecera con foto del tema, miga, categoría, entradilla y metadatos;
 *   · «Respuesta rápida» (el extracto) arriba: es lo que Google toma para el
 *     fragmento destacado;
 *   · índice «En este artículo» generado de los H2, con anclas;
 *   · columna lateral: cotizar, el servicio que la guía apoya (su pilar) y
 *     más guías del mismo país o tema;
 *   · caja de autor «Equipo Grenvíos» (E-E-A-T) y fecha de actualización;
 *   · comentarios en español; schema BlogPosting con autor organización.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Fecha en español sin depender del paquete de idioma. */
function grenvios_bd_fecha( $ts ) {
	$m = array( 1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre' );
	$ts = (int) $ts;
	return (int) gmdate( 'j', $ts ) . ' de ' . $m[ (int) gmdate( 'n', $ts ) ] . ' de ' . gmdate( 'Y', $ts );
}

/* Imagen de la entrada: destacada o foto de ejemplo por tema (nunca un relleno). */
function grenvios_bd_img( $id ) {
	if ( has_post_thumbnail( $id ) ) {
		$u = get_the_post_thumbnail_url( $id, 'large' );
		if ( $u && ( ! function_exists( 'grenvios_ej_es_relleno' ) || ! grenvios_ej_es_relleno( $u ) ) ) return $u;
	}
	return function_exists( 'grenvios_ej_por_tema' ) ? grenvios_ej_por_tema( get_the_title( $id ), 'embalaje', true ) : '';
}

function grenvios_bd_minutos( $html ) {
	$n = count( preg_split( '/\s+/u', trim( wp_strip_all_tags( (string) $html ) ) ) );
	return max( 1, (int) round( $n / 200 ) );
}

/* Añade anclas a los H2 y devuelve [html, índice]. */
function grenvios_bd_indice( $html ) {
	$toc = array(); $usados = array();
	$html = preg_replace_callback( '~<h2(\s[^>]*)?>(.*?)</h2>~s', function ( $m ) use ( &$toc, &$usados ) {
		$attrs = isset( $m[1] ) ? $m[1] : '';
		$txt   = trim( wp_strip_all_tags( $m[2] ) );
		if ( $txt === '' ) return $m[0];
		/* La llamada final del bloque de país («¿Envías a X?») no es un apartado. */
		$en_indice = ( mb_stripos( $txt, '¿Envías a' ) !== 0 );
		if ( preg_match( '~\sid=["\']([^"\']+)~', $attrs, $mm ) ) {
			$id = $mm[1];
		} else {
			$id = sanitize_title( remove_accents( $txt ) );
			$b = $id; $i = 2;
			while ( isset( $usados[ $id ] ) ) $id = $b . '-' . $i++;
			$attrs .= ' id="' . esc_attr( $id ) . '"';
		}
		$usados[ $id ] = 1;
		if ( $en_indice ) $toc[] = array( $id, $txt );
		return '<h2' . $attrs . '>' . $m[2] . '</h2>';
	}, (string) $html );
	return array( is_string( $html ) ? $html : '', $toc );
}

/* Quita el bloque «Preguntas frecuentes sobre <país>» si la guía ya trae las suyas. */
function grenvios_bd_sin_faq_doble( $html ) {
	if ( strpos( $html, 'gr-post-faq' ) === false || strpos( $html, 'gr-faq-item' ) === false ) return $html;
	$r = preg_replace_callback( '~<section\b[^>]*gr-pais-sec[^>]*>.*?</section>~s', function ( $m ) {
		return strpos( $m[0], 'gr-faq-item' ) !== false ? '' : $m[0];
	}, $html );
	return is_string( $r ) ? $r : $html;
}

/* Respuesta rápida: la primera pregunta frecuente de la guía y su respuesta. */
function grenvios_bd_rapida( $html ) {
	if ( ! preg_match( '~gr-post-faq-item[^>]*>\s*<h3[^>]*>(.*?)</h3>\s*<p[^>]*>(.*?)</p>~s', (string) $html, $m ) ) return '';
	return '<p class="gr-bd-rapida-q">' . esc_html( wp_strip_all_tags( $m[1] ) ) . '</p><p>' . esc_html( wp_strip_all_tags( $m[2] ) ) . '</p>';
}

/* Migas de una entrada: Inicio › Blog › Categoría › título (en la ruta actual). */
function grenvios_bd_migas( $id ) {
	$c = array( 'Inicio' => home_url( '/' ) );
	$blog = (int) get_option( 'page_for_posts' );
	if ( $blog && function_exists( 'pll_get_post' ) && function_exists( 'pll_get_post_language' ) ) {
		$t = (int) pll_get_post( $blog, (string) pll_get_post_language( $id ) );
		if ( $t ) $blog = $t;
	}
	if ( $blog ) $c['Blog'] = get_permalink( $blog );
	$cats = get_the_category( $id );
	if ( $cats ) $c[ $cats[0]->name ] = get_category_link( $cats[0] );
	$c[ wp_strip_all_tags( get_the_title( $id ) ) ] = '';
	return $c;
}

/* País de la ruta actual (vacío en la principal). */
function grenvios_bd_pais() {
	return function_exists( 'grenvios_se_pais' ) ? grenvios_se_pais() : '';
}

/* Página que la guía refuerza (su pilar), en la ruta actual. */
function grenvios_bd_pilar( $id ) {
	$pid = defined( 'GRENVIOS_CLUSTER_META' ) ? (int) get_post_meta( $id, GRENVIOS_CLUSTER_META, true ) : 0;
	return ( $pid && get_post_status( $pid ) === 'publish' ) ? $pid : 0;
}

/* Más guías: del mismo país en una ruta; de la misma categoría en la principal. */
function grenvios_bd_mas( $id, $n = 5 ) {
	$args = array( 'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => $n, 'post__not_in' => array( $id ), 'orderby' => 'rand' );
	$lang = function_exists( 'pll_get_post_language' ) ? (string) pll_get_post_language( $id ) : '';
	if ( $lang !== '' ) $args['lang'] = $lang;
	if ( grenvios_bd_pais() !== '' ) {
		$args['meta_key'] = 'grenvios_solo_pais';
	} else {
		$cats = wp_get_post_categories( $id );
		if ( $cats ) $args['category__in'] = $cats;
		/* En la ruta principal, solo guías generales: las de país ceden su canónica a su ruta. */
		$args['meta_query'] = array( array( 'key' => 'grenvios_solo_pais', 'compare' => 'NOT EXISTS' ) );
	}
	return get_posts( $args );
}

/* ── Comentarios en español ─────────────────────────────────────────── */
add_filter( 'comment_form_defaults', function ( $d ) {
	if ( ! is_singular( 'post' ) ) return $d;
	$d['title_reply']          = '¿Tienes una duda sobre este envío?';
	$d['title_reply_to']       = 'Responder a %s';
	$d['cancel_reply_link']    = 'Cancelar';
	$d['label_submit']         = 'Publicar comentario';
	$d['comment_notes_before'] = '<p class="comment-notes">Tu correo no se publicará. Los campos con * son obligatorios. Si tu duda es sobre un envío concreto, <a href="' . esc_url( home_url( '/contacto/' ) ) . '">escríbenos directamente</a>.</p>';
	$d['comment_field']        = '<p class="comment-form-comment"><label for="comment">Comentario *</label><textarea id="comment" name="comment" class="form-control" rows="5" required></textarea></p>';
	return $d;
} );
add_filter( 'comment_form_default_fields', function ( $f ) {
	if ( ! is_singular( 'post' ) ) return $f;
	$c = wp_get_current_commenter();
	$f['author'] = '<p class="comment-form-author"><label for="author">Nombre *</label><input id="author" name="author" type="text" class="form-control" value="' . esc_attr( $c['comment_author'] ) . '" required></p>';
	$f['email']  = '<p class="comment-form-email"><label for="email">Correo *</label><input id="email" name="email" type="email" class="form-control" value="' . esc_attr( $c['comment_author_email'] ) . '" required></p>';
	unset( $f['url'] );
	if ( isset( $f['cookies'] ) ) $f['cookies'] = '<p class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"> <label for="wp-comment-cookies-consent">Recordar mi nombre y correo para el próximo comentario.</label></p>';
	return $f;
} );

/* ── Estilos ────────────────────────────────────────────────────────── */
add_action( 'wp_enqueue_scripts', function () {
	if ( ! is_singular( 'post' ) ) return;
	$f = get_template_directory() . '/assets/css/gr-blog.css';
	if ( file_exists( $f ) ) wp_enqueue_style( 'gr-blog', get_template_directory_uri() . '/assets/css/gr-blog.css', array(), (string) filemtime( $f ) );
}, 30 );

/* Paleta del blog: listado, categorías, etiquetas y detalle de la entrada. Las
 * cabeceras conservan su color (assets/css/gr-paleta-blog.css solo toca el
 * contenido). Prioridad alta para cargar después del resto de hojas. */
add_action( 'wp_enqueue_scripts', function () {
	if ( ! ( ( is_home() && ! is_front_page() ) || is_category() || is_tag() || is_singular( 'post' ) ) ) return;
	$f = get_template_directory() . '/assets/css/gr-paleta-blog.css';
	if ( file_exists( $f ) ) wp_enqueue_style( 'gr-paleta-blog', get_template_directory_uri() . '/assets/css/gr-paleta-blog.css', array(), (string) filemtime( $f ) );
}, 99 );

/* ── Schema: autor organización e imagen real ───────────────────────── */
add_filter( 'grenvios_blogposting_node', function ( $node, $id ) {
	$home = untrailingslashit( home_url() );
	$node['author'] = array( '@type' => 'Organization', 'name' => 'Grenvíos', '@id' => $home . '/#organization' );
	$img = grenvios_bd_img( $id );
	if ( $img ) $node['image'] = $img;
	return $node;
}, 10, 2 );
