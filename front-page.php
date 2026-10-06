<?php
/**
 * Front page (Home Default) — renderiza el contenido real de index.html.
 * @package logisko
 */
get_header();

/* La portada de cada ruta de país se sirve por aquí, no por page.php: Polylang
 * fija una página de inicio por idioma. Sin el búfer y sin el bloque de país,
 * la portada de Chile y la de Ecuador eran el mismo texto palabra por palabra. */
ob_start();

/* La home de cada país (/ar/, /cu/) es una home como la de Perú, más su bloque
 * de contenido por país. La página de destino del país vive aparte, en
 * /ar/envios-a-argentina/ (inc/paises-home-destino.php). */
if ( ! grenvios_render_editable() ) {
	logisko_render_content( 'home' );
}
if ( function_exists( 'grenvios_ent_render' ) ) grenvios_ent_render( 'home' );

/* Secciones de la maqueta (precio, tabla de rutas, destinos uno por uno y
 * cómo elegir): en todas las portadas, la de Perú y la de cada país, para que
 * tengan el mismo diseño (inc/home-seo.php, inc/home-destinos.php). */
if ( function_exists( 'grenvios_home_seo_render' ) ) grenvios_home_seo_render();
if ( function_exists( 'grenvios_hd_render' ) ) grenvios_hd_render();

/* Después, el contenido propio de cada país (en Perú no hay bloque). */
if ( function_exists( 'grenvios_pais_render' ) ) grenvios_pais_render();

grenvios_render_cluster_guides();

/* La portada no era destino de ninguna guía, asi que no tenia un solo enlace
 * al blog. Ahora abre con lo ultimo de SU ruta (inc/blog-en-paginas.php). */
if ( function_exists( 'grenvios_bep_render' ) ) grenvios_bep_render( 'home', 6 );

grenvios_render_related_links( 'home' );
grenvios_render_cta( 'home' );
grenvios_render_page_faqs( 'home' );

echo apply_filters( 'grenvios_html_final', ob_get_clean() );

get_footer();
