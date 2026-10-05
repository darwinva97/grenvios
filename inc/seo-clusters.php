<?php
/**
 * Grenvíos — Clusters de contenido e higiene de indexación.
 *
 * PROBLEMA QUE RESUELVE
 * Las páginas que venden (servicios, destinos, empresas, cotizar) compiten por
 * keywords comerciales muy disputadas. Ganarlas con la propia página rara vez
 * basta: hacen falta contenidos de apoyo que capten las búsquedas informativas
 * («cómo calcular el peso volumétrico», «qué no se puede enviar a Chile») y que
 * empujen su autoridad hacia UNA página de dinero concreta.
 *
 * CÓMO SE MODELA AQUÍ
 *   · Cada guía (entrada) declara qué **página de dinero refuerza**.
 *   · La guía enlaza automáticamente a esa página (enlace contextual + bloque
 *     de cierre), con la keyword de destino como texto ancla.
 *   · La página de dinero muestra sus guías: «Guías relacionadas».
 *   · Cada categoría se asocia a un pilar, para que su archivo no sea una
 *     página vacía sino la entrada al cluster.
 *
 * REGLA DE ORO: una guía empuja a UNA sola página de dinero. Si enlaza a cinco,
 * reparte su autoridad en migajas y no mueve ninguna.
 *
 * HIGIENE DE INDEXACIÓN
 * Un sitio pequeño no puede permitirse decenas de archivos vacíos (etiquetas,
 * fechas, autor) compitiendo con sus propias páginas. Aquí se marcan como
 * `noindex` — no se ocultan al usuario, simplemente no entran al índice.
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

const GRENVIOS_CLUSTER_META = 'grenvios_cluster_target';

/* ══════════════════════════════════════
   1) PÁGINAS DE DINERO
   Las que pueden ser destino de un cluster. Todo lo demás (nosotros, contacto,
   FAQ) no necesita apoyo: no es donde se decide la venta.
══════════════════════════════════════ */
function grenvios_money_pages() {
	$slugs = array(
		'servicios',
		'envio-internacional-de-documentos',
		'envio-internacional-de-paquetes',
		'carga-internacional',
		'apostilla-y-traduccion',
		'envios-para-empresas',
		'envio-de-equipaje',
		'peso-volumetrico',
		'que-se-puede-enviar',
		'envio-de-compras',
		'tiempos-de-entrega',
		'recojo-a-domicilio-lima',
		'cotizar',
		'destinos',
	);
	if ( function_exists( 'grenvios_destinos' ) ) {
		foreach ( array_keys( grenvios_destinos() ) as $d ) $slugs[] = $d;
	}
	$out = array();
	foreach ( $slugs as $s ) {
		$page = get_page_by_path( $s );
		if ( ! $page ) {
			// Las hijas viven bajo su pilar.
			foreach ( array( 'servicios/', 'destinos/' ) as $pref ) {
				$page = get_page_by_path( $pref . $s );
				if ( $page ) break;
			}
		}
		if ( ! $page || $page->post_status !== 'publish' ) continue;
		// En multiidioma, cada idioma refuerza sus propias páginas.
		if ( function_exists( 'grenvios_i18n_active' ) && grenvios_i18n_active() ) {
			$lang = grenvios_i18n_current();
			$tid  = grenvios_i18n_translation_id( $page->ID, $lang );
			if ( $tid ) $page = get_post( $tid );
		}
		$out[ (int) $page->ID ] = $page->post_title;
	}
	return $out;
}

/* Página de dinero que refuerza una guía (0 si no se asignó). */
function grenvios_cluster_target( $post_id ) {
	return (int) get_post_meta( (int) $post_id, GRENVIOS_CLUSTER_META, true );
}

/* Guías que refuerzan una página de dinero. */
function grenvios_cluster_posts( $page_id, $limit = 6 ) {
	$args = array(
		'post_type'   => 'post',
		'post_status' => 'publish',
		'numberposts' => (int) $limit,
		'meta_key'    => GRENVIOS_CLUSTER_META,
		'meta_value'  => (int) $page_id,
	);
	if ( function_exists( 'grenvios_i18n_active' ) && grenvios_i18n_active() ) {
		$args['lang'] = grenvios_i18n_current();
	}
	return get_posts( $args );
}

/* ══════════════════════════════════════
   2) CAJA EN LA EDICIÓN DE LA GUÍA
══════════════════════════════════════ */
add_action( 'add_meta_boxes', function () {
	add_meta_box( 'grenvios-cluster', 'Cluster SEO — ¿a qué página refuerza?', 'grenvios_cluster_metabox', 'post', 'side', 'high' );
} );

function grenvios_cluster_metabox( $post ) {
	wp_nonce_field( 'grenvios_cluster', 'grenvios_cluster_nonce' );
	$sel = grenvios_cluster_target( $post->ID );
	?>
	<p><select name="grenvios_cluster_target" style="width:100%">
		<option value="0">— Ninguna (no recomendado) —</option>
		<?php foreach ( grenvios_money_pages() as $id => $title ) : ?>
			<option value="<?php echo (int) $id; ?>" <?php selected( $sel, $id ); ?>><?php echo esc_html( $title ); ?></option>
		<?php endforeach; ?>
	</select></p>
	<p class="description">
		Esta guía enlazará automáticamente a esa página y aparecerá listada en ella.
		<strong>Elige una sola:</strong> si una guía apunta a cinco páginas, no refuerza ninguna.
	</p>
	<?php
}

add_action( 'save_post_post', function ( $post_id ) {
	if ( ! isset( $_POST['grenvios_cluster_nonce'] ) || ! wp_verify_nonce( $_POST['grenvios_cluster_nonce'], 'grenvios_cluster' ) ) return;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;
	$v = isset( $_POST['grenvios_cluster_target'] ) ? (int) $_POST['grenvios_cluster_target'] : 0;
	if ( $v ) update_post_meta( $post_id, GRENVIOS_CLUSTER_META, $v );
	else delete_post_meta( $post_id, GRENVIOS_CLUSTER_META );
	delete_transient( 'grenvios_links_graph' );
} );

/* ══════════════════════════════════════
   3) ENLACE AUTOMÁTICO DE LA GUÍA A SU PÁGINA DE DINERO
   Se añade al final del contenido de la entrada, con la keyword de la página
   destino como texto ancla (que es la señal que Google lee).
══════════════════════════════════════ */
add_filter( 'the_content', function ( $content ) {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) return $content;

	$target = grenvios_cluster_target( get_the_ID() );
	if ( ! $target || get_post_status( $target ) !== 'publish' ) return $content;

	$t   = function ( $s ) { return function_exists( 'grenvios_t' ) ? grenvios_t( $s ) : $s; };
	$kw  = function_exists( 'grenvios_seo_kw' ) ? grenvios_seo_kw( $target ) : get_the_title( $target );
	$url = get_permalink( $target );

	$box  = '<aside class="grenvios-cluster-cta">';
	$box .= '<p><strong>' . esc_html( $t( '¿Necesitas este servicio?' ) ) . '</strong> ';
	$box .= esc_html( $t( 'Revisa la página de' ) ) . ' <a href="' . esc_url( $url ) . '">' . esc_html( $kw ) . '</a> ';
	$box .= esc_html( $t( 'o cotiza tu envío en minutos.' ) ) . '</p></aside>';

	return $content . $box;
}, 20 );

/* ══════════════════════════════════════
   4) BLOQUE "GUÍAS RELACIONADAS" EN LA PÁGINA DE DINERO
   Se imprime desde page.php (junto al bloque de enlaces relacionados). Solo
   aparece si esa página tiene guías: nunca una sección vacía.
══════════════════════════════════════ */
function grenvios_render_cluster_guides() {
	if ( ! is_page() ) return;
	$posts = grenvios_cluster_posts( (int) get_queried_object_id() );
	if ( empty( $posts ) ) return;

	$t = function ( $s ) { return function_exists( 'grenvios_t' ) ? grenvios_t( $s ) : $s; };

	echo '<section class="grenvios-guides"><div class="container">';
	echo '<h2 class="grenvios-guides-title">' . esc_html( $t( 'Guías que te pueden ayudar' ) ) . '</h2>';
	echo '<div class="row">';
	foreach ( $posts as $p ) {
		echo '<div class="col-lg-4 col-md-6"><article class="grenvios-guide-card">';
		echo function_exists( 'grenvios_ej_miniatura' ) ? grenvios_ej_miniatura( $p ) : '';
		echo '<h3><a href="' . esc_url( get_permalink( $p ) ) . '">' . esc_html( $p->post_title ) . '</a></h3>';
		$ex = wp_strip_all_tags( get_the_excerpt( $p ) );
		if ( $ex ) echo '<p>' . esc_html( wp_trim_words( $ex, 22 ) ) . '</p>';
		echo '</article></div>';
	}
	echo '</div></div></section>';
}

add_action( 'wp_head', function () {
	if ( is_admin() ) return;
	echo '<style id="grenvios-cluster-css">'
		. '.grenvios-guides{padding:10px 0 40px}'
		. '.grenvios-guides-title{font-size:24px;margin-bottom:22px}'
		. '.grenvios-guide-card{margin-bottom:26px}'
		. '.grenvios-guide-card h3{font-size:17px;line-height:1.35;margin:10px 0 6px}'
		. '.grenvios-guide-card p{opacity:.8;font-size:14px;margin:0}'
		. '.grenvios-guide-img img{border-radius:8px;width:100%;height:auto}'
		. '.grenvios-cluster-cta{margin:28px 0;padding:16px 20px;border-left:3px solid currentColor;background:rgba(0,0,0,.04)}'
		. '.grenvios-cluster-cta p{margin:0}'
		. '.grenvios-cluster-pillar{padding:24px 0 0}'
		. '.grenvios-cluster-pillar p{margin:0 0 8px;font-size:16px}'
		. '.grenvios-cluster-desc{opacity:.8;max-width:70ch}'
		. '</style>';
}, 103 );

/* ══════════════════════════════════════
   5) CATEGORÍA → PILAR
   Cada categoría de guías se asocia a una página pilar. Así su archivo deja de
   ser una lista suelta y se convierte en la puerta de entrada al cluster.
══════════════════════════════════════ */
function grenvios_category_pillar_option() {
	$o = get_option( 'grenvios_cat_pillar', array() );
	return is_array( $o ) ? $o : array();
}

/* Campo en la edición de la categoría. */
add_action( 'category_edit_form_fields', function ( $term ) {
	$map = grenvios_category_pillar_option();
	$sel = isset( $map[ $term->term_id ] ) ? (int) $map[ $term->term_id ] : 0;
	?>
	<tr class="form-field">
		<th scope="row"><label for="grenvios_cat_pillar">Pilar que refuerza</label></th>
		<td>
			<select name="grenvios_cat_pillar" id="grenvios_cat_pillar">
				<option value="0">— Ninguno —</option>
				<?php foreach ( grenvios_money_pages() as $id => $title ) : ?>
					<option value="<?php echo (int) $id; ?>" <?php selected( $sel, $id ); ?>><?php echo esc_html( $title ); ?></option>
				<?php endforeach; ?>
			</select>
			<p class="description">El archivo de esta categoría enlazará a esa página como destino principal del cluster.</p>
		</td>
	</tr>
	<?php
} );

add_action( 'edited_category', function ( $term_id ) {
	if ( ! isset( $_POST['grenvios_cat_pillar'] ) || ! current_user_can( 'manage_categories' ) ) return;
	$map = grenvios_category_pillar_option();
	$v   = (int) $_POST['grenvios_cat_pillar'];
	if ( $v ) $map[ (int) $term_id ] = $v;
	else unset( $map[ (int) $term_id ] );
	update_option( 'grenvios_cat_pillar', $map, false );
} );

/* Pilar de la categoría que se está viendo. */
function grenvios_current_category_pillar() {
	if ( ! is_category() ) return 0;
	$map = grenvios_category_pillar_option();
	$id  = (int) get_queried_object_id();
	return isset( $map[ $id ] ) ? (int) $map[ $id ] : 0;
}

/* ══════════════════════════════════════
   6) HIGIENE DE INDEXACIÓN
   Qué entra al índice y qué no. Un archivo de etiqueta con dos entradas no
   posiciona: solo diluye el sitio y se come presupuesto de rastreo.
══════════════════════════════════════ */
add_action( 'wp_head', function () {
	$noindex = false;
	$motivo  = '';

	// Las etiquetas salen del índice salvo que se marquen como indexables en su
	// edición (ver inc/blog-guias.php).
	if ( is_tag() )                { $noindex = apply_filters( 'grenvios_noindex_tag', true ); $motivo = 'archivo de etiqueta'; }
	elseif ( is_author() )         { $noindex = true; $motivo = 'archivo de autor'; }
	elseif ( is_date() )           { $noindex = true; $motivo = 'archivo por fecha'; }
	elseif ( is_search() )         { $noindex = true; $motivo = 'resultados de búsqueda'; }
	elseif ( is_attachment() )     { $noindex = true; $motivo = 'página de adjunto'; }
	elseif ( is_paged() && ( is_category() || is_home() ) ) {
		// Página 2, 3… de un listado: el contenido real ya está en las entradas.
		$noindex = true; $motivo = 'listado paginado';
	}
	// Categoría sin descripción y con menos de 3 entradas: página delgada.
	elseif ( is_category() ) {
		$term = get_queried_object();
		if ( $term && (int) $term->count < 3 && trim( (string) $term->description ) === '' ) {
			$noindex = true; $motivo = 'categoría con poco contenido';
		}
	}

	if ( ! $noindex ) return;
	echo '<meta name="robots" content="noindex, follow">' . "\n";
	echo '<!-- Grenvíos: fuera del índice (' . esc_html( $motivo ) . '); los enlaces sí se siguen -->' . "\n";
}, 3 );

/* ══════════════════════════════════════
   7) LAS GUÍAS ENTRAN EN EL GRAFO DE ENLACES
   Para que «SEO por página» cuente los enlaces que recibe cada página de dinero
   desde el contenido de apoyo.
══════════════════════════════════════ */
add_filter( 'grenvios_links_extra', function ( $edges ) {
	$args = array( 'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => -1, 'meta_key' => GRENVIOS_CLUSTER_META );
	if ( function_exists( 'grenvios_i18n_active' ) && grenvios_i18n_active() ) $args['lang'] = '';
	foreach ( get_posts( $args ) as $p ) {
		$t = grenvios_cluster_target( $p->ID );
		if ( $t ) $edges[] = array( (int) $p->ID, (int) $t );
	}
	return $edges;
}, 10, 1 );

/* ══════════════════════════════════════
   8) INFORME: páginas de dinero sin contenido de apoyo
   Se muestra en «SEO por página». Es la lista de trabajo de contenido.
══════════════════════════════════════ */
function grenvios_cluster_report() {
	$out = array();
	foreach ( grenvios_money_pages() as $id => $title ) {
		$out[ $id ] = array(
			'title' => $title,
			'guias' => count( grenvios_cluster_posts( $id, 50 ) ),
		);
	}
	return $out;
}

/* Cabecera del archivo de categoria: enlace al pilar del cluster, para que el
 * archivo no sea una lista suelta sino la puerta de entrada al tema. */
function grenvios_render_category_pillar() {
	$pid = grenvios_current_category_pillar();
	if ( ! $pid || get_post_status( $pid ) !== 'publish' ) return;

	$t  = function ( $s ) { return function_exists( 'grenvios_t' ) ? grenvios_t( $s ) : $s; };
	$kw = function_exists( 'grenvios_seo_kw' ) ? grenvios_seo_kw( $pid ) : get_the_title( $pid );

	echo '<section class="grenvios-cluster-pillar"><div class="container">';
	echo '<p>' . esc_html( $t( 'Todas estas guías son sobre' ) ) . ' <a href="' . esc_url( get_permalink( $pid ) ) . '"><strong>' . esc_html( $kw ) . '</strong></a>. ';
	echo esc_html( $t( 'Si ya sabes lo que necesitas, ve directo al servicio.' ) ) . '</p>';

	$desc = trim( (string) category_description() );
	if ( $desc ) echo '<div class="grenvios-cluster-desc">' . wp_kses_post( $desc ) . '</div>';
	echo '</div></section>';
}

/* Textos de los bloques de cluster. */
add_filter( 'grenvios_i18n_extra_strings', function ( $textos ) {
	foreach ( array(
		'Guías que te pueden ayudar', 'Sigue leyendo', '¿Necesitas este servicio?',
		'Revisa la página de', 'o cotiza tu envío en minutos.',
		'Todas estas guías son sobre', 'Si ya sabes lo que necesitas, ve directo al servicio.',
	) as $t ) $textos[] = $t;
	return $textos;
} );
