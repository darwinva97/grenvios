<?php
/**
 * Grenvíos — Guías: blog, categorías y etiquetas.
 *
 * La capa de contenido que alimenta los clusters (ver inc/seo-clusters.php).
 * Aquí se monta para que sirva al SEO y no solo para «tener un blog»:
 *
 *   · CATEGORÍAS que calcan los pilares del sitio. Se crean solas, con
 *     descripción propia (una categoría sin descripción es una página vacía) y
 *     ya asociadas a la página de dinero que refuerzan.
 *   · ETIQUETAS para organizar transversalmente (por país, por tipo de envío).
 *   · GUÍAS RELACIONADAS al final de cada entrada, primero por etiqueta y luego
 *     por categoría: es lo que mantiene al lector dentro del cluster.
 *   · MIGAS DE PAN y schema `Article` en cada guía.
 *
 * SOBRE LAS ETIQUETAS: en un sitio pequeño, los archivos de etiqueta suelen
 * tener dos o tres entradas y compiten contra la categoría y contra las propias
 * guías. Por eso se usan para navegar, pero salen del índice de Google
 * (`noindex, follow`, en inc/seo-clusters.php). Si alguna etiqueta llega a
 * tener contenido real y descripción propia, se puede indexar marcándola en
 * Ajustes → Etiquetas de Grenvíos.
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ══════════════════════════════════════
   1) CATEGORÍAS QUE CALCAN LOS PILARES
══════════════════════════════════════ */
function grenvios_categorias_base() {
	return array(
		'guias-de-envio' => array(
			'nombre' => 'Guías de envío',
			'desc'   => 'Cómo preparar, medir, embalar y despachar un envío internacional desde {{origen_pais}}. Lo que conviene saber antes de llevar el paquete a la agencia.',
			'pilar'  => 'servicios/envio-internacional-de-paquetes',
		),
		'documentos' => array(
			'nombre' => 'Documentos y trámites',
			'desc'   => 'Envío de documentos legales al extranjero, apostilla, traducción oficial y requisitos por país. Qué se necesita y cuánto demora cada trámite.',
			'pilar'  => 'servicios/envio-internacional-de-documentos',
		),
		'aduanas' => array(
			'nombre' => 'Aduanas y restricciones',
			'desc'   => 'Qué permite y qué prohíbe la aduana de cada país, límites de valor, impuestos y los motivos por los que un envío se queda retenido.',
			'pilar'  => 'que-se-puede-enviar',
		),
		'destinos' => array(
			'nombre' => 'Destinos',
			'desc'   => 'Particularidades de cada país al que enviamos: plazos, formas de entrega, restricciones propias y consejos para que el envío llegue sin trabas.',
			'pilar'  => 'destinos',
		),
		'empresas' => array(
			'nombre' => 'Envíos para empresas',
			'desc'   => 'Logística internacional para negocios: documentación de exportación, envíos recurrentes, tarifas por volumen y control de costos.',
			'pilar'  => 'envios-para-empresas',
		),
	);
}

/* Crea las categorías la primera vez y las asocia a su pilar. Idempotente. */
add_action( 'admin_init', function () {
	if ( get_option( 'grenvios_cats_v' ) === '1' ) return;

	$map = function_exists( 'grenvios_category_pillar_option' ) ? grenvios_category_pillar_option() : array();

	foreach ( grenvios_categorias_base() as $slug => $c ) {
		$term = get_term_by( 'slug', $slug, 'category' );
		if ( ! $term ) {
			$new = wp_insert_term( $c['nombre'], 'category', array( 'slug' => $slug, 'description' => $c['desc'] ) );
			if ( is_wp_error( $new ) ) continue;
			$term_id = (int) $new['term_id'];
		} else {
			$term_id = (int) $term->term_id;
			if ( trim( (string) $term->description ) === '' ) {
				wp_update_term( $term_id, 'category', array( 'description' => $c['desc'] ) );
			}
		}
		// Asociación con la página de dinero que refuerza.
		if ( empty( $map[ $term_id ] ) ) {
			$page = get_page_by_path( $c['pilar'] );
			if ( $page ) $map[ $term_id ] = (int) $page->ID;
		}
	}
	update_option( 'grenvios_cat_pillar', $map, false );
	update_option( 'grenvios_cats_v', '1', false );
}, 20 );

/* ══════════════════════════════════════
   2) ETIQUETAS: navegación sí, índice no (salvo excepción)
══════════════════════════════════════ */
function grenvios_tags_indexables() {
	$v = get_option( 'grenvios_tags_index', array() );
	return is_array( $v ) ? $v : array();
}

/* La regla general (noindex) vive en inc/seo-clusters.php; aquí se levanta para
 * las etiquetas que el administrador haya marcado como indexables. */
add_filter( 'grenvios_noindex_tag', function ( $noindex ) {
	if ( ! is_tag() ) return $noindex;
	$id = (int) get_queried_object_id();
	return in_array( $id, grenvios_tags_indexables(), true ) ? false : $noindex;
} );

/* Casilla en la edición de la etiqueta. */
add_action( 'post_tag_edit_form_fields', function ( $term ) {
	$on = in_array( (int) $term->term_id, grenvios_tags_indexables(), true );
	?>
	<tr class="form-field">
		<th scope="row"><label for="grenvios_tag_index">Indexar en Google</label></th>
		<td>
			<label><input type="checkbox" name="grenvios_tag_index" id="grenvios_tag_index" value="1" <?php checked( $on ); ?>>
				Permitir que esta etiqueta aparezca en Google</label>
			<p class="description">
				Por defecto las etiquetas <strong>no</strong> se indexan: con pocas entradas son páginas
				delgadas que compiten contra tus propias guías. Actívala solo si esta etiqueta tiene
				varias entradas <em>y</em> una descripción propia que aporte algo.
			</p>
		</td>
	</tr>
	<?php
} );

add_action( 'edited_post_tag', function ( $term_id ) {
	if ( ! current_user_can( 'manage_categories' ) ) return;
	$list = grenvios_tags_indexables();
	$id   = (int) $term_id;
	$on   = ! empty( $_POST['grenvios_tag_index'] );
	$list = array_values( array_diff( $list, array( $id ) ) );
	if ( $on ) $list[] = $id;
	update_option( 'grenvios_tags_index', $list, false );
} );

/* ══════════════════════════════════════
   3) GUÍAS RELACIONADAS AL FINAL DE CADA ENTRADA
   Primero por etiqueta (más específica) y se completa por categoría.
══════════════════════════════════════ */
function grenvios_guias_relacionadas( $post_id, $limite = 3 ) {
	$excluir = array( (int) $post_id );
	$out     = array();

	$tags = wp_get_post_terms( $post_id, 'post_tag', array( 'fields' => 'ids' ) );
	if ( $tags && ! is_wp_error( $tags ) ) {
		$out = get_posts( array(
			'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => $limite,
			'post__not_in' => $excluir, 'tag__in' => $tags,
		) );
	}
	if ( count( $out ) < $limite ) {
		$cats = wp_get_post_terms( $post_id, 'category', array( 'fields' => 'ids' ) );
		if ( $cats && ! is_wp_error( $cats ) ) {
			foreach ( $out as $p ) $excluir[] = (int) $p->ID;
			$mas = get_posts( array(
				'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => $limite - count( $out ),
				'post__not_in' => $excluir, 'category__in' => $cats,
			) );
			$out = array_merge( $out, $mas );
		}
	}
	return $out;
}

function grenvios_render_guias_relacionadas() {
	if ( ! is_singular( 'post' ) ) return;
	$posts = grenvios_guias_relacionadas( get_the_ID() );
	if ( empty( $posts ) ) return;

	$t = function ( $s ) { return function_exists( 'grenvios_t' ) ? grenvios_t( $s ) : $s; };

	echo '<section class="grenvios-guides"><div class="container">';
	echo '<h2 class="grenvios-guides-title">' . esc_html( $t( 'Sigue leyendo' ) ) . '</h2><div class="row">';
	foreach ( $posts as $p ) {
		echo '<div class="col-lg-4 col-md-6"><article class="grenvios-guide-card">';
		echo function_exists( 'grenvios_ej_miniatura' ) ? grenvios_ej_miniatura( $p ) : '';
		echo '<h3><a href="' . esc_url( get_permalink( $p ) ) . '">' . esc_html( $p->post_title ) . '</a></h3>';
		$ex = wp_strip_all_tags( get_the_excerpt( $p ) );
		if ( $ex ) echo '<p>' . esc_html( wp_trim_words( $ex, 20 ) ) . '</p>';
		echo '</article></div>';
	}
	echo '</div></div></section>';
}

/* ══════════════════════════════════════
   4) SCHEMA Article EN CADA GUÍA
══════════════════════════════════════ */
add_action( 'wp_head', function () {
	if ( ! is_singular( 'post' ) ) return;
	$b   = function_exists( 'grenvios_biz' ) ? grenvios_biz() : array();
	$img = get_the_post_thumbnail_url( get_the_ID(), 'full' );

	$data = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'Article',
		'headline'         => wp_strip_all_tags( get_the_title() ),
		'description'      => wp_strip_all_tags( get_the_excerpt() ),
		'datePublished'    => get_the_date( 'c' ),
		'dateModified'     => get_the_modified_date( 'c' ),
		'mainEntityOfPage' => get_permalink(),
		'author'           => array( '@type' => 'Organization', 'name' => isset( $b['name'] ) ? $b['name'] : get_bloginfo( 'name' ) ),
		'publisher'        => array( '@type' => 'Organization', 'name' => isset( $b['name'] ) ? $b['name'] : get_bloginfo( 'name' ) ),
	);
	if ( $img ) $data['image'] = $img;

	echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n";
}, 12 );

/* ══════════════════════════════════════
   5) LA PÁGINA DE GUÍAS
   El sitio no tiene «blog de noticias»: tiene guías. Se renombra la página de
   entradas y se le da un texto propio, para que el archivo no sea un listado
   anónimo sino la entrada al contenido de apoyo.
══════════════════════════════════════ */
add_filter( 'grenvios_pages', function ( $pages ) {
	if ( isset( $pages['blog'] ) ) {
		$pages['blog']['title'] = 'Guías';
		$pages['blog']['seo']   = 'Guías de Envíos Internacionales desde {{origen_pais}} | Grenvíos';
		$pages['blog']['desc']  = 'Guías prácticas sobre envíos internacionales desde {{origen_pais}}: cómo se calcula el precio, qué se puede enviar, plazos por país y trámites de aduana.';
	}
	return $pages;
} );

/* Formulario de comentarios con las clases del diseño (.comment-form .form-control
 * en common-style.css). Sin ellas WordPress pintaba un textarea de 45 columnas que
 * se salía de la pantalla en móvil y campos sin el estilo del tema. */
add_filter( 'comment_form_defaults', function ( $d ) {
	$d['comment_field'] = str_replace( '<textarea ', '<textarea class="form-control comment" ', $d['comment_field'] );
	$d['class_submit']  = trim( ( isset( $d['class_submit'] ) ? $d['class_submit'] : 'submit' ) . ' default-btn' );
	return $d;
} );
add_filter( 'comment_form_default_fields', function ( $f ) {
	foreach ( $f as $k => $html ) {
		if ( $k === 'cookies' ) continue;
		$f[ $k ] = preg_replace( '/<input (?![^>]*type="checkbox")/', '<input class="form-control" ', $html, 1 );
	}
	return $f;
} );
