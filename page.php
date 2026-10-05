<?php
/**
 * Page — enrutador de plantillas Grenvíos.
 *
 * Orden de resolución del CONTENIDO:
 *   1) Destino (país) → render data-driven.
 *   2) Página de herramienta (peso volumétrico, qué se puede enviar, equipaje).
 *   3) Preguntas frecuentes y blog → data-driven.
 *   4) Contenido editable del cliente (post_content).
 *   5) Partial del diseño content-{slug}.html.
 *   6) Fallback básico.
 *
 * Y DESPUÉS, para todas por igual: guías del cluster, enlazado interno,
 * llamada a la acción y FAQ con su schema. Al estar fuera del if, una página
 * nueva no puede quedarse sin enlazado por olvido.
 *
 * @package grenvios
 */
grenvios_perf_mark( 'arranque wp' );
get_header();
grenvios_perf_mark( 'cabecera' );

// Slug MAESTRO (español): la página traducida usa la misma plantilla que su
// original aunque su URL esté traducida. Ver inc/i18n.php.
$slug = function_exists( 'grenvios_canonical_slug' )
	? grenvios_canonical_slug( get_the_ID() )
	: get_post_field( 'post_name', get_the_ID() );

$es_listado = false;   // el blog trae su propio pie de listado

/* Todo el cuerpo se arma en un búfer y sale por `grenvios_html_final`: los
 * renders data-driven imprimen directamente, así que es el único punto donde
 * se les puede traducir los enlaces a la ruta activa. */
ob_start();
grenvios_perf_mark( 'inicio' );

/* Búfer propio del cuerpo principal: las páginas pintadas por PHP no pasan por
 * `grenvios_content_html`, y su entradilla SEO caía al final del contenido, pegada
 * a «Del blog». Aquí se coloca justo debajo del hero, como en el resto. */
ob_start();

if ( grenvios_render_destino( $slug ) ) {
	// País: plantilla data-driven con sus plazos, restricciones y FAQ propias.
} elseif ( apply_filters( 'grenvios_render_page_early', false, $slug ) ) {
	// Páginas data-driven de los módulos: herramientas y contenido.
} elseif ( $slug === 'preguntas-frecuentes' && grenvios_render_faq() ) {
	// FAQ + schema sincronizado.
} elseif ( $slug === 'blog' && grenvios_render_blog() ) {
	$es_listado = true;
} else {
	// 1º: contenido editable del cliente. 2º: partial. 3º: fallback básico.
	if ( ! grenvios_render_editable() && ! logisko_render_content( $slug ) ) {
		logisko_page_banner( get_the_title(), get_the_title() );
		echo '<section class="blog-section padding"><div class="container"><div class="logisko-entry-content">';
		while ( have_posts() ) { the_post(); the_content(); }
		echo '</div></div></section>';
	}
}

$grenvios_main = ob_get_clean();
echo function_exists( 'grenvios_ent_tras_hero' ) ? grenvios_ent_tras_hero( $grenvios_main ) : $grenvios_main;

/* Secciones propias de este país, lo único que distingue esta página de la
 * misma página en otra ruta. Va pegado al contenido y antes del enlazado. */
grenvios_perf_mark( 'cuerpo' );

/* Entradilla con la keyword. Normalmente ya se inyectó debajo del hero desde
 * `grenvios_content_html`; esto solo actúa en las páginas data-driven
 * (inc/seo-entradillas.php). */
if ( function_exists( 'grenvios_ent_render' ) ) grenvios_ent_render( $slug );

if ( function_exists( 'grenvios_pais_render' ) ) grenvios_pais_render();
grenvios_perf_mark( 'bloque pais' );

/* Contenido propio de las páginas delgadas de la ruta principal
 * (inc/paginas-contenido-seo.php). */
if ( function_exists( 'grenvios_pseo_render' ) ) grenvios_pseo_render( $slug );
grenvios_perf_mark( 'secciones seo' );

/* Cobertura en origen: distritos de la sede y ciudades del país
 * (inc/cobertura-origen.php). */
if ( function_exists( 'grenvios_co_render' ) ) grenvios_co_render( $slug );

/* Calculadora de peso volumétrico (inc/herramienta-volumetrico.php). */
if ( function_exists( 'grenvios_hv_render' ) ) grenvios_hv_render( $slug );
grenvios_perf_mark( 'herramientas' );

if ( ! $es_listado ) {
	grenvios_render_cluster_guides();          // guías que declaran esta página
	grenvios_perf_mark( 'guias cluster' );
	if ( function_exists( 'grenvios_bep_render' ) ) grenvios_bep_render( $slug );   // blog por tema (inc/blog-en-paginas.php)          // guías que refuerzan esta página
	grenvios_perf_mark( 'blog en pagina' );
	grenvios_render_related_links( $slug );    // enlazado pillar-cluster
	grenvios_perf_mark( 'enlazado' );
	grenvios_render_cta( $slug );              // llamada a la acción
	grenvios_render_page_faqs( $slug );        // FAQ + schema FAQPage
	grenvios_perf_mark( 'cta + faq' );
	/* Cierre de página: formulario de solicitud (inc/rutas-diseno.php). */
	do_action( 'grenvios_pagina_cierre', $slug );
}

$grenvios_cuerpo = ob_get_clean();
grenvios_perf_mark( 'buffer' );
echo apply_filters( 'grenvios_html_final', $grenvios_cuerpo );
grenvios_perf_mark( 'html final' );

get_footer();
grenvios_perf_mark( 'pie' );
