<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  «Artículos por país» — el índice que reúne el blog de todas las rutas
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Cada ruta tiene su propio blog, y eso está bien para el visitante que ya está
 * en `/cu/`: solo ve guías de Cuba. Pero deja un hueco. Quien llega buscando
 * «guía para enviar a Bolivia» no está en ninguna ruta todavía, y desde el sitio
 * principal no hay forma de ver qué se ha escrito de cada destino: los artículos
 * están repartidos en nueve blogs que no se cruzan.
 *
 * Esta página los junta **agrupados por país**, que es la señal que interesa: un
 * bloque «Artículos sobre envíos a Bolivia» con sus entradas es una sección
 * temática clara, y reparte enlaces internos hacia el blog de cada ruta.
 *
 * NO se duplica a las rutas, por la misma razón que el hub `/destinos/`: una
 * versión en `/cu/` de «artículos de todos los países» compite con el original y
 * no aporta nada que el blog de Cuba no dé ya.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

const GRENVIOS_HUB_BLOG_SLUG = 'articulos-por-pais';

/* ─────────────────────────────────────────────────────────────────────────
 * 1) La página
 * ───────────────────────────────────────────────────────────────────────── */

/* Se crea sola la primera vez que hay más de una ruta: antes no tiene sentido
 * —agruparía por país un solo país— y sería una página vacía indexada. */
function grenvios_hub_blog_asegurar() {
	if ( ! function_exists( 'grenvios_sedes' ) ) return 0;
	if ( count( grenvios_sedes() ) < 2 ) return 0;

	$p = get_page_by_path( GRENVIOS_HUB_BLOG_SLUG );
	if ( $p ) return (int) $p->ID;

	$id = wp_insert_post( array(
		'post_type'    => 'page',
		'post_status'  => 'publish',
		'post_title'   => 'Artículos por país',
		'post_name'    => GRENVIOS_HUB_BLOG_SLUG,
		'post_content' => '',
	) );
	if ( is_wp_error( $id ) ) return 0;

	if ( function_exists( 'pll_set_post_language' ) && function_exists( 'grenvios_i18n_default' ) ) {
		pll_set_post_language( $id, grenvios_i18n_default() );
	}
	update_post_meta( $id, 'grenvios_seo_title', 'Guías de Envío por País: Aduana, Plazos y Precios' );
	update_post_meta( $id, 'grenvios_seo_desc', 'Todas nuestras guías de envío internacional, ordenadas por país de destino: qué se puede enviar, cuánto demora y qué pide cada aduana.' );
	flush_rewrite_rules( false );
	return (int) $id;
}
/* Se comprueba al entrar al admin, no en el frontend: es una consulta por
 * petición y en el frontend no aporta nada. La página aparece la primera vez que
 * la clienta abre WordPress después de crear su segunda ruta. */
add_action( 'admin_init', function () {
	if ( ! current_user_can( 'edit_pages' ) ) return;
	grenvios_hub_blog_asegurar();
} );

/* Fuera del duplicador: es un índice global, no una página de destino. */
add_filter( 'grenvios_ruta_paginas', function ( $paginas ) {
	foreach ( $paginas as $id => $p ) {
		if ( $p->post_name === GRENVIOS_HUB_BLOG_SLUG ) unset( $paginas[ $id ] );
	}
	return $paginas;
}, 10, 1 );

/* ─────────────────────────────────────────────────────────────────────────
 * 2) El listado
 * ───────────────────────────────────────────────────────────────────────── */

/* Entradas de cada ruta, agrupadas. Devuelve solo los países que tienen algo
 * publicado: un bloque «Artículos sobre Venezuela (0)» es ruido. */
function grenvios_hub_blog_datos() {
	$out = array();
	if ( ! function_exists( 'grenvios_sedes' ) ) return $out;

	foreach ( grenvios_sedes() as $lang => $s ) {
		$posts = get_posts( array(
			'post_type'   => 'post',
			'post_status' => 'publish',
			'numberposts' => (int) apply_filters( 'grenvios_hub_blog_por_pais', 6, $lang ),
			'lang'        => $lang,
		) );
		if ( ! $posts ) continue;

		/* La ruta principal NO es un destino: es el origen. «Artículos sobre
		 * envíos a Perú» describiría un servicio que no se presta. Sus entradas
		 * son las guías que valen para cualquier destino. */
		$es_master = function_exists( 'grenvios_i18n_default' ) && grenvios_i18n_default() === $lang;
		if ( $es_master ) {
			$nombre = grenvios_t( 'todos los destinos' );
			$titulo = grenvios_t( 'Guías generales, válidas para cualquier destino' );
		} else {
			$nombre = function_exists( 'grenvios_col_pais_nombre' )
				? grenvios_col_pais_nombre( $lang )
				: ( isset( $s['name'] ) ? $s['name'] : $lang );
			$titulo = sprintf( grenvios_t( 'Artículos sobre envíos a %s' ), $nombre );
		}

		$out[ $lang ] = array(
			'nombre' => $nombre,
			'titulo' => $titulo,
			'master' => $es_master,
			'posts'  => $posts,
			'blog'   => grenvios_hub_blog_url( $lang ),
			'total'  => (int) count( get_posts( array(
				'post_type' => 'post', 'post_status' => 'publish',
				'numberposts' => -1, 'fields' => 'ids', 'lang' => $lang,
			) ) ),
		);
	}
	return $out;
}

/* URL del blog de una ruta (la traducción de la página de entradas). */
function grenvios_hub_blog_url( $lang ) {
	$pid = (int) get_option( 'page_for_posts' );
	if ( ! $pid ) return '';
	if ( function_exists( 'pll_get_post' ) ) {
		$t = (int) pll_get_post( $pid, $lang );
		if ( $t ) return get_permalink( $t );
	}
	return get_permalink( $pid );
}

function grenvios_hub_blog_tarjeta( $p ) {
	$img = has_post_thumbnail( $p->ID )
		? get_the_post_thumbnail( $p->ID, 'medium_large', array( 'alt' => esc_attr( get_the_title( $p->ID ) ) ) )
		: '<img src="' . esc_url( function_exists( 'grenvios_ej_por_tema' ) ? grenvios_ej_por_tema( get_the_title( $p->ID ), 'embalaje' ) : get_template_directory_uri() . '/assets/img/post-1.jpg' ) . '" alt="' . esc_attr( get_the_title( $p->ID ) ) . '">';

	return '<div class="col-lg-4 col-md-6"><article class="blog-card">'
		. '<div class="blog-card-thumb"><a href="' . esc_url( get_permalink( $p->ID ) ) . '">' . $img . '</a></div>'
		. '<div class="blog-card-body"><div class="blog-card-meta">'
		. '<span><i class="fa-regular fa-calendar"></i> ' . esc_html( get_the_date( '', $p->ID ) ) . '</span></div>'
		. '<h3 class="blog-card-title"><a href="' . esc_url( get_permalink( $p->ID ) ) . '">'
		. esc_html( get_the_title( $p->ID ) ) . '</a></h3>'
		. '<p>' . esc_html( wp_trim_words( get_the_excerpt( $p->ID ), 18 ) ) . '</p>'
		. '<a href="' . esc_url( get_permalink( $p->ID ) ) . '" class="read-more">'
		. esc_html( grenvios_t( 'Leer más' ) ) . ' <i class="fa-solid fa-arrow-right"></i></a>'
		. '</div></article></div>';
}

function grenvios_hub_blog_render() {
	$datos = grenvios_hub_blog_datos();
	if ( ! $datos ) return false;

	/* El filtro es del lado del cliente a propósito.
	 *
	 * Con recarga haría falta una URL por país (`?pais=cu`), y eso son nueve URLs
	 * que muestran subconjuntos del mismo contenido: exactamente el tipo de página
	 * que Google marca como duplicada y que además diluye la que sí queremos
	 * posicionar. Filtrando en el navegador hay una sola URL y Google ve el
	 * listado completo. */
	if ( function_exists( 'logisko_page_banner' ) ) {
		logisko_page_banner(
			grenvios_tf( 'hub_eyebrow', 'Guías de envío' ),
			grenvios_tf( 'hub_title', 'Guías de envío por <span>país de destino</span>' )
		);
	}

	$h = '<section class="blog-section padding"><div class="container">'
		. '<p class="gr-hub-intro">' . esc_html( grenvios_tf( 'hub_intro',
			'Cada destino tiene sus propias reglas de aduana, sus plazos y su lista de lo que no se puede enviar. Elige el país al que envías y lee solo lo que te afecta.'
		) ) . '</p>';

	$h .= '<div class="blog-filter" id="gr-hub-filtro">'
		. '<button type="button" class="blog-filter-btn is-active" data-pais="">'
		. esc_html( grenvios_tf( 'hub_todos', 'Todos los países' ) ) . '</button>';
	foreach ( $datos as $lang => $d ) {
		$h .= '<button type="button" class="blog-filter-btn" data-pais="' . esc_attr( $lang ) . '">'
			. esc_html( $d['master'] ? grenvios_t( 'Generales' ) : $d['nombre'] ) . ' <span class="gr-hub-n">' . (int) $d['total'] . '</span></button>';
	}
	$h .= '</div>';

	foreach ( $datos as $lang => $d ) {
		$h .= '<div class="gr-hub-grupo" data-pais="' . esc_attr( $lang ) . '">'
			. '<h2 class="gr-hub-titulo">' . esc_html( $d['titulo'] ) . '</h2>'
			. ( ! $d['master'] && function_exists( 'grenvios_hub_blog_intro' ) ? grenvios_hub_blog_intro( $lang ) : '' )
			. '<div class="row gy-4 blog-grid">';
		foreach ( $d['posts'] as $p ) $h .= grenvios_hub_blog_tarjeta( $p );
		$h .= '</div>';

		if ( $d['blog'] && $d['total'] > count( $d['posts'] ) ) {
			$h .= '<p class="gr-hub-mas"><a href="' . esc_url( $d['blog'] ) . '" class="read-more">'
				. esc_html( sprintf( grenvios_t( 'Ver las %d guías de %s' ), $d['total'], $d['nombre'] ) )
				. ' <i class="fa-solid fa-arrow-right"></i></a></p>';
		} elseif ( $d['blog'] ) {
			$h .= '<p class="gr-hub-mas"><a href="' . esc_url( $d['blog'] ) . '" class="read-more">'
				. esc_html( sprintf( grenvios_t( 'Blog de %s' ), $d['nombre'] ) )
				. ' <i class="fa-solid fa-arrow-right"></i></a></p>';
		}
		$h .= '</div>';
	}

	$h .= '<p class="gr-hub-vacio" hidden>' . esc_html( grenvios_tf( 'hub_vacio', 'Todavía no hay guías para ese país.' ) ) . '</p>';
	$h .= '</div></section>';

	echo $h;                              // ya escapado pieza a pieza
	grenvios_hub_blog_assets();
	return true;
}

function grenvios_hub_blog_assets() {
	?>
<style id="gr-hub-css">
.gr-hub-intro{max-width:70ch;margin:0 0 26px;opacity:.85}
.gr-hub-grupo{margin-bottom:46px}
.gr-hub-titulo{font-size:26px;margin:0 0 20px;padding-bottom:10px;border-bottom:1px solid rgba(0,0,0,.08)}
.gr-hub-n{opacity:.55;font-size:12px;margin-left:4px}
.gr-hub-mas{margin-top:16px}
.gr-hub-grupo[hidden],.gr-hub-vacio[hidden]{display:none}
</style>
<script id="gr-hub-js">
(function(){
  var f=document.getElementById('gr-hub-filtro'); if(!f) return;
  var grupos=document.querySelectorAll('.gr-hub-grupo'),
      vacio=document.querySelector('.gr-hub-vacio');
  f.addEventListener('click',function(e){
    var b=e.target.closest('.blog-filter-btn'); if(!b) return;
    var pais=b.getAttribute('data-pais'), visibles=0;
    f.querySelectorAll('.blog-filter-btn').forEach(function(x){x.classList.toggle('is-active',x===b);});
    grupos.forEach(function(g){
      var ok=!pais||g.getAttribute('data-pais')===pais;
      g.hidden=!ok; if(ok) visibles++;
    });
    if(vacio) vacio.hidden=visibles>0;
  });
})();
</script>
	<?php
}

/* Se engancha al despachador de páginas del tema, como el resto de páginas
 * data-driven (destinos, FAQ). */
add_filter( 'grenvios_render_page_early', function ( $hecho, $slug ) {
	if ( $hecho || $slug !== GRENVIOS_HUB_BLOG_SLUG ) return $hecho;
	return grenvios_hub_blog_render();
}, 10, 2 );

/* ─────────────────────────────────────────────────────────────────────────
 * 3) Cómo se llega
 *
 * No se toca el menú del diseño: se enlaza desde donde tiene sentido, que es
 * cualquier listado del blog. Desde el blog principal, para ver qué hay de cada
 * destino; desde el de una ruta, para saltar al de otro país.
 * ───────────────────────────────────────────────────────────────────────── */
add_action( 'grenvios_blog_antes_listado', function () {
	$p = get_page_by_path( GRENVIOS_HUB_BLOG_SLUG );
	if ( ! $p || $p->post_status !== 'publish' ) return;
	if ( (int) get_queried_object_id() === (int) $p->ID ) return;

	echo '<p class="gr-hub-enlace"><a class="read-more" href="' . esc_url( get_permalink( $p->ID ) ) . '">'
		. esc_html( grenvios_t( 'Ver todas las guías ordenadas por país' ) )
		. ' <i class="fa-solid fa-arrow-right"></i></a></p>';
}, 20 );

