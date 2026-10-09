<?php
/**
 * Grenvíos — functions.php
 * Tema basado en el template Logisko, adaptado a la arquitectura web y SEO
 * definida en grenvios-arquitectura-seo.md (modelo pillar–cluster).
 *
 * - Encola el CSS/JS REAL del template en su orden exacto.
 * - Renderiza el HTML real clonado (header / footer / content-*) con reemplazo de tokens.
 * - Auto-crea las páginas con la arquitectura Grenvíos (Servicios y Destinos como pilares).
 * - SEO nativo: title tags, meta descriptions, canonical, OpenGraph, Schema.org y breadcrumbs.
 * - Botón flotante de WhatsApp en todas las páginas.
 * - Destinos renderizados por datos (un solo template para los 9 países).
 *
 * Tokens en los .html de template-parts/:
 *   ARKDINURI → URI del tema  (assets reales locales)
 *   HOMEURL   → home_url() sin barra final  (enlaces internos del menú/páginas)
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'LOGISKO_VER', '3.26.0' );

/* Versión del REGISTRO DE PÁGINAS: súbela al añadir páginas nuevas para que se
 * creen solas en los sitios ya instalados. */
define( 'GRENVIOS_PAGES_V', '4' );

/* Personalizador (Customizer): edición de imágenes, fondos y contacto sin código */
require_once get_template_directory() . '/inc/customizer.php';

/* Editor de Página en línea: panel en el sitio que edita el texto de cada página
 * (post-meta por página). Reemplaza la edición de textos por Customizer. */
require_once get_template_directory() . '/inc/page-editor.php';
require_once get_template_directory() . '/inc/editor-entradas.php';

/* Repeaters: contenido dinámico (añadir/quitar/reordenar ítems) por página.
 * Renderiza los tokens {{REP:clave}} de los partials. */
require_once get_template_directory() . '/inc/repeaters.php';

/* Seguimiento de envíos: CPT "Envíos" (rastreo manual) + consulta pública por guía. */
require_once get_template_directory() . '/inc/tracking.php';

/* Destinos: gestión de países (agregar/editar/eliminar) — crea la página con textos
 * genéricos y los muestra en el menú Destinos del encabezado (token DESTINOSMENU). */
require_once get_template_directory() . '/inc/destinos.php';
require_once get_template_directory() . '/inc/destinos-secciones.php';
require_once get_template_directory() . '/inc/destinos-hero.php';
require_once get_template_directory() . '/inc/hero-paginas.php';

/* Ajustes globales del sitio (colores, redes, encabezado y pie) editables desde el panel. */
require_once get_template_directory() . '/inc/globals.php';

/* ── MULTIIDIOMA (Polylang) ──────────────────────────────────────────────────
 * i18n.php           núcleo: idiomas, slug maestro, selector de idioma
 * i18n-strings.php   diccionario de los textos fijos del diseño
 * i18n-translate.php motor de traducción automática (Claude / DeepL / Google)
 * i18n-seo.php       hreflang, og:locale, canonical y sitemap por idioma
 * i18n-admin.php     pantalla "Traducciones" (traducir todo / por página)
 * El tema funciona igual si Polylang no está instalado: se comporta como
 * un sitio monolingüe en español. */
require_once get_template_directory() . '/inc/i18n.php';
require_once get_template_directory() . '/inc/i18n-strings.php';
require_once get_template_directory() . '/inc/i18n-translate.php';
require_once get_template_directory() . '/inc/i18n-links.php';
require_once get_template_directory() . '/inc/i18n-seo.php';
require_once get_template_directory() . '/inc/i18n-admin.php';

/* Sedes: cada idioma de Polylang puede ser una operación con país de origen,
 * teléfono, dirección y redes propias. Con un solo idioma queda inerte. */
require_once get_template_directory() . '/inc/sedes.php';
require_once get_template_directory() . '/inc/sedes-contenido.php';
require_once get_template_directory() . '/inc/sedes-seo.php';
require_once get_template_directory() . '/inc/sedes-admin.php';
require_once get_template_directory() . '/inc/paises-rutas.php';
require_once get_template_directory() . '/inc/paises-contenido.php';
require_once get_template_directory() . '/inc/paises-cabecera.php';
require_once get_template_directory() . '/inc/paises-blog.php';
require_once get_template_directory() . '/inc/paises-blog-hub.php';
require_once get_template_directory() . '/inc/paises-espejos.php';
require_once get_template_directory() . '/inc/paises-home-destino.php';
require_once get_template_directory() . '/inc/paises-home-hero.php';
require_once get_template_directory() . '/inc/paises-textos.php';
require_once get_template_directory() . '/inc/paises-mas-contenido.php';
require_once get_template_directory() . '/inc/paises-secciones-extra.php';
require_once get_template_directory() . '/inc/paises-secciones-extra2.php';
require_once get_template_directory() . '/inc/home-seo.php';
require_once get_template_directory() . '/inc/home-destinos.php';
require_once get_template_directory() . '/inc/destinos-seo.php';
require_once get_template_directory() . '/inc/destinos-ciudades.php';
require_once get_template_directory() . '/inc/destinos-datos-practicos.php';
// Índice «En esta página»: fila ESTÁTICA, sin sticky (el cliente pidió quitar el pegado, 2026-09-28).
require_once get_template_directory() . '/inc/destinos-indice.php';
require_once get_template_directory() . '/inc/destinos-textos-editables.php';
require_once get_template_directory() . '/inc/diseno-tarjetas.php';
require_once get_template_directory() . '/inc/servicios-mas.php';
require_once get_template_directory() . '/inc/paginas-contenido-seo.php';
require_once get_template_directory() . '/inc/contenido-ampliacion.php';
require_once get_template_directory() . '/inc/contenido-ampliacion-2.php';
require_once get_template_directory() . '/inc/seo-entradas.php';
require_once get_template_directory() . '/inc/blog-guias-contenido.php';
require_once get_template_directory() . '/inc/blog-guias-importar.php';
require_once get_template_directory() . '/inc/blog-guias-ampliacion.php';
require_once get_template_directory() . '/inc/blog-guias-nuevas.php';
require_once get_template_directory() . '/inc/blog-paises-contenido.php';
require_once get_template_directory() . '/inc/blog-paises-importar.php';
require_once get_template_directory() . '/inc/blog-paises-ampliacion.php';
// Tres guías locales por país (dirección, medicinas/alimentos/baterías, productos peruanos).
require_once get_template_directory() . '/inc/blog-paises-locales.php';
// Guías de apoyo por país: una por servicio de la ruta (carga, empresas, compras, seguro, vía).
require_once get_template_directory() . '/inc/blog-paises-apoyo.php';
// Seis guías más por país (seguimiento, apostilla, caja, estudiantes, destinatario, pedir desde allí).
require_once get_template_directory() . '/inc/blog-paises-mas.php';
// Guías temáticas por país: 3 ciudades principales y 9 productos con regla propia.
require_once get_template_directory() . '/inc/blog-paises-tematicas.php';
// Detalle de entrada v2: cabecera, respuesta rápida, índice, lateral, autor (single.php).
require_once get_template_directory() . '/inc/blog-detalle.php';
// Rendimiento (CSS diferido, medidas de imagen), accesibilidad y og:image real.
require_once get_template_directory() . '/inc/rendimiento-a11y.php';
// Diseño v3 de rutas: bloques del país, formulario de solicitud al pie y cabecera del blog por país.
require_once get_template_directory() . '/inc/rutas-diseno.php';
require_once get_template_directory() . '/inc/blog-faq-schema.php';
require_once get_template_directory() . '/inc/cobertura-origen.php';
require_once get_template_directory() . '/inc/peru-seo.php';
require_once get_template_directory() . '/inc/herramienta-volumetrico.php';
require_once get_template_directory() . '/inc/peru-seo-secciones.php';
require_once get_template_directory() . '/inc/blog-etiquetas.php';
require_once get_template_directory() . '/inc/blog-en-paginas.php';
require_once get_template_directory() . '/inc/cache-html.php';
require_once get_template_directory() . '/inc/seo-keywords-defecto.php';
require_once get_template_directory() . '/inc/seo-titulos.php';
require_once get_template_directory() . '/inc/seo-entradillas.php';
require_once get_template_directory() . '/inc/seo-jerarquia.php';
require_once get_template_directory() . '/inc/destinos-hub.php';
require_once get_template_directory() . '/inc/home-mas.php';
require_once get_template_directory() . '/inc/peru-seo-ampliacion.php';
require_once get_template_directory() . '/inc/paginas-nuevas-seo.php';
require_once get_template_directory() . '/inc/ui-bloques.php';
require_once get_template_directory() . '/inc/secciones-visuales.php';
require_once get_template_directory() . '/inc/ui-global.php';
// Estilo v2 (maquetas del cliente) en todo el sitio; cada página puede volver al clásico desde el panel.
require_once get_template_directory() . '/inc/estilo-v2.php';
// Fotos de ejemplo en lugar de los rellenos grises de la plantilla (se sustituyen al subir una propia).
require_once get_template_directory() . '/inc/imagenes-ejemplo.php';
require_once get_template_directory() . '/inc/imagenes-destacadas.php';
// Intro de servicio v2: antetítulo, iconos, botones, foto con tarjeta y cita (maqueta carga internacional).
require_once get_template_directory() . '/inc/servicio-intro-v2.php';
// SEO: /destinos/<país>/ cede la canónica a la ficha de la ruta del país (evita canibalización).
require_once get_template_directory() . '/inc/seo-canibalizacion.php';
// Perfil editorial por país (aduana, documento, dirección, reglas) y FAQ propias de cada ruta.
require_once get_template_directory() . '/inc/paises-perfil.php';
// Contenido local por país en cada página de la ruta (sanitario, aduana, apostilla, horario, feriados, ruta, clima, unidades).
require_once get_template_directory() . '/inc/paises-paginas-locales.php';
// Cinco páginas nuevas en primer nivel (courier en Lima, ropa, repuestos, libros, mudanzas).
require_once get_template_directory() . '/inc/paginas-primer-nivel.php';
// URL de primer nivel: entradas, categorías, etiquetas y servicios sin base; sitemap agrupado.
require_once get_template_directory() . '/inc/urls-primer-nivel.php';
require_once get_template_directory() . '/inc/paginas-servicios-extra.php';
require_once get_template_directory() . '/inc/paginas-servicios-extra2.php';
require_once get_template_directory() . '/inc/paginas-cluster.php';
require_once get_template_directory() . '/inc/paginas-regiones.php';
require_once get_template_directory() . '/inc/perf-debug.php';
require_once get_template_directory() . '/inc/editor-cobertura.php';
if ( is_admin() ) require_once get_template_directory() . '/inc/paises-contenido-admin.php';
if ( is_admin() ) require_once get_template_directory() . '/inc/paises-admin-columna.php';

/* ── SEO DE CONTENIDO ────────────────────────────────────────────────────────
 * seo-keywords.php  palabra clave objetivo por pagina + auditoria on-page
 * seo-enlazado.php  enlazado interno automatico y bloque de enlaces relacionados */
require_once get_template_directory() . '/inc/seo-keywords.php';
require_once get_template_directory() . '/inc/seo-enlazado.php';
require_once get_template_directory() . '/inc/paginas-herramientas.php';
require_once get_template_directory() . '/inc/paginas-contenido.php';
require_once get_template_directory() . '/inc/paginas-combinadas.php';
require_once get_template_directory() . '/inc/paginas-seo-extra.php';
require_once get_template_directory() . '/inc/blog-guias.php';
require_once get_template_directory() . '/inc/contenido-guias.php';
require_once get_template_directory() . '/inc/editor-frontend.php';
require_once get_template_directory() . '/inc/redirecciones.php';
require_once get_template_directory() . '/inc/seo-keywords-import.php';
require_once get_template_directory() . '/inc/seo-clusters.php';

/* Datos de negocio (centralizados para branding, contacto y schema).
 * Los valores se pueden editar desde Apariencia → Personalizar → Datos de contacto;
 * si no se editan, se usan los de grenvios_biz_defaults().
 *
 * Filtro `grenvios_biz`: lo usa inc/sedes.php para servir el teléfono, la
 * dirección y el WhatsApp de la SEDE activa (país de origen). Sin sedes, o en
 * la sede maestra, devuelve exactamente lo de siempre. */
function grenvios_biz( $sede = null ) {
	$sede     = $sede !== null ? $sede : ( function_exists( 'grenvios_sede' ) ? grenvios_sede() : '' );
	$defaults = grenvios_biz_defaults();
	$biz      = $defaults;
	foreach ( array_keys( grenvios_biz_fields() ) as $key ) {
		$val = get_theme_mod( 'grenvios_biz_' . $key, $defaults[ $key ] );
		if ( $val !== '' ) $biz[ $key ] = $val;
	}
	return apply_filters( 'grenvios_biz', $biz, $sede );
}

/* ─────────────────────────────────────────────────────────────
 * 1) Theme supports
 * ───────────────────────────────────────────────────────────── */
add_action( 'after_setup_theme', function () {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array( 'height' => 155, 'width' => 541, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	register_nav_menus( array( 'primary' => __( 'Menú principal', 'grenvios' ) ) );
} );

/* Clase de body por slug (permite estilos específicos por página, ej. cotizar) */
add_filter( 'body_class', function ( $classes ) {
	$slug = grenvios_current_slug();
	if ( $slug ) $classes[] = 'grenvios-' . sanitize_html_class( $slug );
	return $classes;
} );

/* ─────────────────────────────────────────────────────────────
 * 2) Encolado de estilos y scripts REALES (orden idéntico al template)
 * ───────────────────────────────────────────────────────────── */
/* WordPress imprime su propio <link rel="canonical"> en wp_head, y el tema ya
 * imprime uno que sí conoce las rutas de país y las páginas espejo (filtro
 * `grenvios_canonical`). Con los dos activos, cada página salía con DOS
 * canónicas: hoy apuntan al mismo sitio, pero en cuanto una diverja —una URL
 * con parámetros, una espejo— Google descarta las dos. Se queda la del tema. */
remove_action( 'wp_head', 'rel_canonical' );

/* Preconexión a Google Fonts: ahorra el ida y vuelta de DNS/TLS antes de pedir
 * la tipografía, que es render-blocking por definición. */
add_action( 'wp_head', function () {
	echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "
";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "
";
}, 1 );

add_action( 'wp_enqueue_scripts', function () {
	$uri = get_template_directory_uri();

	/* ── CSS (mismo orden que el <head> original) ── */
	$styles = array(
		'logisko-bootstrap'   => '/assets/css/bootstrap.min.css',
		'logisko-animate'     => '/assets/css/animate.min.css',
		'logisko-keyframe'    => '/assets/css/keyframe-animation.css',
		'logisko-fontawesome' => '/assets/lib/font-awesome-pro/css/fontawesome.min.css',
		'logisko-icons'       => '/assets/css/logistic-icons.min.css',
		'logisko-odometer'    => '/assets/css/odometer.min.css',
		'logisko-nice-select' => '/assets/css/nice-select.css',
		'logisko-swiper'      => '/assets/css/swiper.min.css',
		'logisko-venobox'     => '/assets/css/venobox.min.css',
		'logisko-slider'      => '/assets/css/slider.css',
		'logisko-common'      => '/assets/css/common-style.css',
		'logisko-main'        => '/assets/css/main.css',
	);
	$prev = array();
	foreach ( $styles as $handle => $path ) {
		$f = get_template_directory() . $path;   // versión = fecha del fichero (ver scripts)
		wp_enqueue_style( $handle, $uri . $path, $prev, file_exists( $f ) ? (string) filemtime( $f ) : LOGISKO_VER );
		$prev = array( $handle );
	}
	/* Tipografía del sitio (Poppins, la de la maqueta). Se carga como hoja propia,
	 * antes de style.css, en vez de con un @import dentro del CSS: un @import
	 * bloquea el render hasta que baja el archivo. */
	wp_enqueue_style( 'grenvios-fuente', 'https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400;1,600&display=swap', array(), null );
	$prev[] = 'grenvios-fuente';

	wp_enqueue_style( 'logisko-style', get_stylesheet_uri(), $prev, (string) filemtime( get_stylesheet_directory() . '/style.css' ) );

	/* Idiomas de escritura derecha-a-izquierda (arabe, hebreo): hoja adicional
	 * que solo se carga cuando el idioma activo es RTL. */
	if ( is_rtl() ) {
		wp_enqueue_style( 'logisko-rtl', $uri . '/assets/css/rtl.css', array( 'logisko-style' ), LOGISKO_VER );
	}

	/* ── jQuery bundled del template bajo el handle 'jquery' ── */
	wp_deregister_script( 'jquery' );
	wp_register_script( 'jquery', $uri . '/assets/js/vendor/jquary-3.6.0.min.js', array(), '3.6.0', true );

	/* ── JS (orden exacto, en footer). Cadena de dependencias estricta. ── */
	$scripts = array(
		'logisko-modernizr'  => '/assets/js/vendor/modernizr-2.8.3-respond-1.4.2.min.js',
		'logisko-bootstrap'  => '/assets/js/vendor/bootstrap.min.js',
		'logisko-popper'     => '/assets/js/vendor/popper.min.js',
		'logisko-gsap'       => '/assets/lib/gsap/gsap.min.js',
		'logisko-scrolltrig' => '/assets/lib/gsap/ScrollTrigger.min.js',
		'logisko-splittype'  => '/assets/lib/gsap/split-type.min.js',
		'logisko-lenis'      => '/assets/js/vendor/lenis.min.js',
		'logisko-odometer'   => '/assets/js/vendor/odometer.min.js',
		'logisko-niceselect' => '/assets/js/vendor/jquery.nice-select.min.js',
		'logisko-waypoints'  => '/assets/js/vendor/waypoints.min.js',
		'logisko-venobox'    => '/assets/js/vendor/venobox.min.js',
		'logisko-swiper'     => '/assets/js/vendor/swiper.min.js',
		'logisko-wow'        => '/assets/js/vendor/wow.min.js',
		'logisko-mailchimp'  => '/assets/js/mailchimp.js',
		'logisko-quoteform'  => '/assets/js/quote-form.js',
		'logisko-contact'    => '/assets/js/contact.js',
		'logisko-mainjs'     => '/assets/js/main.js',
		'logisko-heroquote'  => '/assets/js/hero-quote.js',
	);
	$prev = array( 'jquery' );
	foreach ( $scripts as $handle => $path ) {
		/* Versión = fecha del fichero: con la constante fija, un main.js cambiado
		 * seguía saliendo de la caché del navegador. */
		$f = get_template_directory() . $path;
		wp_enqueue_script( $handle, $uri . $path, $prev, file_exists( $f ) ? (string) filemtime( $f ) : LOGISKO_VER, true );
		$prev = array( $handle );
	}

	wp_localize_script( 'logisko-mainjs', 'logiskoVars', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'home'    => untrailingslashit( home_url() ),
	) );

	/* El hero de la portada mide «pantalla menos cabecera»: aquí se publica el
	 * alto real de la cabecera en --gr-header-h (ver style.css). */
	wp_add_inline_script( 'logisko-mainjs', <<<'JS'
(function () {
	var set = function () {
		var h = document.querySelector('.main-header');
		if (h) document.documentElement.style.setProperty('--gr-header-h', h.offsetHeight + 'px');
	};
	set();
	window.addEventListener('resize', set);
	window.addEventListener('load', set);
	if (window.ResizeObserver) {
		var h = document.querySelector('.main-header');
		if (h) new ResizeObserver(set).observe(h);
	}
})();
JS
	);
} );

/* ─────────────────────────────────────────────────────────────
 * 3) Render de partials clonados con reemplazo de tokens
 * ───────────────────────────────────────────────────────────── */
/* Raíz del sitio para construir enlaces internos.
 *
 * `home_url()` NO sirve: con Polylang y prefijo de directorio devuelve la portada
 * del idioma activo con su slug («/cu/envios-a-cuba»), de modo que $home.'/cotizar/'
 * produce /cu/envios-a-cuba/cotizar/ en vez de dejar que el localizador lo
 * convierta en /cu/cuanto-cuesta-enviar-a-cuba/.
 *
 * Se usa solo donde se ARMA un enlace interno. Para «Inicio» de las migas y para
 * los @id de schema sí se quiere la portada de la ruta: ahí sigue `home_url()`. */
function grenvios_url_base() {
	return function_exists( 'grenvios_i18n_site_root' )
		? grenvios_i18n_site_root()
		: untrailingslashit( home_url() );
}

/* Devuelve el HTML de un partial con los tokens resueltos (o false si no existe). */
function grenvios_partial_raw( $name ) {
	$file = get_template_directory() . '/template-parts/' . $name . '.html';
	if ( ! file_exists( $file ) ) return false;
	$uri  = untrailingslashit( get_template_directory_uri() );
	/* OJO: no `home_url()`. Con Polylang y prefijo de directorio, en el frontend
	 * devuelve la portada del IDIOMA ACTIVO con su slug —«/cu/envios-a-cuba»—, así
	 * que HOMEURL/contacto/ salía como /cu/envios-a-cuba/contacto/: TODO el menú de
	 * cada ruta apuntaba a URLs largas y no canónicas, sin usar los slugs de país.
	 * Con la raíz real el enlace sale /contacto/ y el localizador lo convierte en
	 * /cu/contacto-envios-a-cuba/. */
	$home = function_exists( 'grenvios_i18n_site_root' )
		? grenvios_i18n_site_root()
		: untrailingslashit( home_url() );
	grenvios_perf_mark( 'partial ' . $name . ': inicio' );
	$html = file_get_contents( $file );
	$html = str_replace( 'ARKDINURI', $uri,  $html );
	$html = str_replace( 'HOMEURL',   $home, $html );
	// Contacto editable (Customizer → grenvios_biz): WhatsApp, tel: y teléfono mostrado.
	$biz  = grenvios_biz();
	$wa   = preg_replace( '/\D/', '', (string) $biz['wa_number'] );   // solo dígitos para wa.me
	$html = str_replace( 'https://wa.me/51900612836', 'https://wa.me/' . $wa, $html );
	$html = str_replace( 'tel:{{contacto_tel}}', 'tel:' . $biz['phone_tel'], $html );
	$html = str_replace( '>{{contacto_telefono}}<', '>' . esc_html( $biz['phone'] ) . '<', $html );
	// Ajustes globales (encabezado/pie): redes, ícono de barra superior, logo y textos del pie.
	if ( function_exists( 'grenvios_g' ) ) {
		$html = str_replace( 'SOCIALHEADER', grenvios_social_html( 'header' ), $html );
		$html = str_replace( 'SOCIALFOOTER', grenvios_social_html( 'footer' ), $html );
		$html = str_replace( 'TOPBAREXTRA',  grenvios_topbar_extra_html(), $html );
		$html = str_replace( 'FOOTERLOGO',   esc_url( grenvios_g( 'footer_logo', $uri . '/assets/img/logo.svg' ) ), $html );
		$html = str_replace( 'FOOTERABOUT',  esc_html( grenvios_g( 'footer_about', '' ) ), $html );
		$html = str_replace( 'FOOTERCOPY',   grenvios_footer_copyright_html(), $html );
		$html = str_replace( 'FOOTERLEGAL',  function_exists( 'grenvios_footer_legal_html' ) ? grenvios_footer_legal_html() : '', $html );
	}
	// Submenú Destinos del encabezado (data-driven: incluye los países agregados por el admin).
	if ( function_exists( 'grenvios_destinos_menu_html' ) ) {
		grenvios_perf_mark( 'partial ' . $name . ': tokens' );
		$html = str_replace( 'DESTINOSMENU', grenvios_destinos_menu_html(), $html );
		grenvios_perf_mark( 'partial ' . $name . ': menu destinos' );
	}
	/* Cotizador del hero: vive en su propio partial para poder repetirlo en las
	 * páginas de país sin duplicar el formulario. El país lo pone el contexto
	 * (inc/paises-home-hero.php), así que el markup es siempre el mismo. */
	if ( strpos( $html, 'HEROQUOTEFORM' ) !== false && $name !== 'hero-quote' ) {
		$form = grenvios_partial_raw( 'hero-quote' );
		$html = str_replace( 'HEROQUOTEFORM', $form === false ? '' : $form, $html );
	}
	/* Filtro `grenvios_partial_html`: punto ÚNICO por el que pasa todo el HTML del
	 * diseño antes de resolver los tokens {{campo}}. El multiidioma lo aprovecha
	 * para traducir los textos fijos (menús, botones, pie) y para inyectar el
	 * selector de idioma, sin tocar los .html del tema. */
	grenvios_perf_mark( 'partial ' . $name . ': resto' );
	$html = apply_filters( 'grenvios_partial_html', $html, $name );
	grenvios_perf_mark( 'partial ' . $name . ': filtro i18n' );
	return $html;
}

/* HTML del cotizador ya montado (tokens resueltos), para los renders en PHP.
 * El destino lo decide el contexto de la página: ver grenvios_hq_pais(). */
function grenvios_hq_form_html() {
	$html = grenvios_partial_raw( 'hero-quote' );
	return $html === false ? '' : grenvios_apply_media_overrides( $html );
}

function logisko_part( $name ) {
	$html = grenvios_partial_raw( $name );
	if ( $html === false ) return false;
	$html = grenvios_apply_media_overrides( $html ); // markup del template + imagenes del Customizer
	// Filtro `grenvios_content_html`: solo el CONTENIDO de la pagina (nunca el
	// encabezado ni el pie). Lo usa el enlazado interno automatico.
	if ( strpos( $name, 'content-' ) === 0 ) $html = apply_filters( 'grenvios_content_html', $html );
	echo $html;
	return true;
}

/* Imprime el contenido EDITABLE de la página actual (post_content) procesando bloques,
 * sin wpautop/wptexturize para no alterar el HTML del diseño ni los scripts inline.
 * Devuelve false si la página no tiene contenido (para caer al partial). */
function grenvios_render_editable() {
	// Las páginas con texto parametrizado se gestionan desde el Customizer:
	// se renderiza el partial con tokens, no el post_content.
	if ( grenvios_is_parametrized( grenvios_current_slug() ) ) return false;
	$post = get_post();
	if ( ! $post || trim( (string) $post->post_content ) === '' ) return false;
	// El bloque de país se imprime aparte (grenvios_pais_render), así que aquí se
	// quita: si no, las páginas que sí vuelcan post_content lo mostrarían dos veces.
	$cont = function_exists( 'grenvios_pais_sin_bloque' )
		? grenvios_pais_sin_bloque( $post->post_content )
		: $post->post_content;
	echo apply_filters( 'grenvios_content_html', grenvios_apply_media_overrides( do_blocks( $cont ) ) );
	return true;
}

/* Vuelca el HTML de cada partial al contenido de su página (bloque wp:html) para que
 * sea EDITABLE desde el editor de WordPress. Solo rellena si la página está vacía
 * (no pisa ediciones del cliente). Excluye destinos y FAQ (son data-driven). */
function grenvios_seed_editable_pages( $ids ) {
	$dest = grenvios_destinos();
	foreach ( $ids as $slug => $pid ) {
		if ( empty( $pid ) ) continue;
		if ( isset( $dest[ $slug ] ) ) continue;            // destinos: data-driven
		if ( $slug === 'preguntas-frecuentes' ) continue;   // FAQ: data-driven
		if ( grenvios_is_parametrized( $slug ) ) continue;  // texto vía Customizer (tokens)
		$html = grenvios_partial_raw( 'content-' . $slug );
		if ( $html === false ) continue;
		$post = get_post( $pid );
		if ( ! $post || trim( (string) $post->post_content ) !== '' ) continue; // ya tiene contenido
		wp_update_post( array(
			'ID'           => $pid,
			'post_content' => "<!-- wp:html -->\n" . $html . "\n<!-- /wp:html -->",
		) );
	}
}

function logisko_render_content( $slug ) { return logisko_part( 'content-' . $slug ); }

/* Banner reutilizable (.page-header) con breadcrumbs, para blog/single/archive */
function logisko_page_banner( $eyebrow, $title, $crumbs = array(), $bg = '' ) {
	/* Filtro `grenvios_cabecera`: antesala y título de las páginas que pintan su
	 * cabecera desde PHP. Mismo propósito que `grenvios_campo_valor` para las que
	 * la pintan desde plantilla. */
	list( $eyebrow, $title ) = apply_filters( 'grenvios_cabecera', array( $eyebrow, $title ) );
	/* Hero a pantalla completa de la maqueta (inc/hero-paginas.php). */
	if ( function_exists( 'grenvios_hero_pagina' ) ) {
		echo grenvios_hero_pagina( $eyebrow, $title, $crumbs, (string) $bg );
		return;
	}
	?>
	<section class="page-header"<?php if ( $bg ) echo ' style="background-image:url(' . esc_url( $bg ) . ')"'; ?>>
		<div class="container">
			<div class="page-header-info text-center">
				<h4><?php echo esc_html( $eyebrow ); ?></h4>
				<h1><?php echo wp_kses_post( $title ); ?></h1>
				<?php logisko_breadcrumbs( $crumbs ); ?>
			</div>
		</div>
	</section>
<?php }

/* ─────────────────────────────────────────────────────────────
 * 4) Arquitectura de páginas Grenvíos (pillar–cluster)
 *    Cada entrada: slug => [título, SEO title, meta description, padre]
 * ───────────────────────────────────────────────────────────── */
function grenvios_pages() {
	$pages = array(
		// Principales
		'home' => array(
			'title' => 'Inicio',
			'seo'   => 'Grenvíos | Envíos Internacionales de Paquetes y Carga',
			'desc'  => 'Envíos internacionales de documentos, paquetes y carga a América, Europa, Asia y África. Cotiza en línea y rastrea tu envío con Grenvíos.',
			'parent'=> '',
		),
		'nosotros' => array(
			'title' => 'Nosotros',
			'seo'   => 'Nosotros | Grenvíos — Transporte Internacional Confiable',
			'desc'  => 'Conoce a Grenvíos: conectamos personas y empresas en el mundo con envíos aéreos y terrestres seguros, rápidos y confiables desde {{origen_ciudad}}.',
			'parent'=> '',
		),
		'servicios' => array(
			'title' => 'Servicios',
			'seo'   => 'Servicios de Envío Internacional | Grenvíos',
			'desc'  => 'Descubre los servicios de Grenvíos: envío de documentos, paquetes y carga internacional vía aérea y terrestre a más de 30 países.',
			'parent'=> '',
		),
		'envio-internacional-de-documentos' => array(
			'title' => 'Envío de Documentos',
			'seo'   => 'Envío de Documentos Internacional | Grenvíos',
			'desc'  => 'Envía documentos legales, títulos, DNI y pasaportes a nivel internacional en 4 a 7 días hábiles. Servicio seguro puerta a puerta.',
			'parent'=> 'servicios',
		),
		'envio-internacional-de-paquetes' => array(
			'title' => 'Envío de Paquetes',
			'seo'   => 'Envío de Paquetes Internacional Aéreo y Terrestre | Grenvíos',
			'desc'  => 'Envía paquetes, equipaje y compras desde {{origen_ciudad}} a Ecuador, Colombia, Chile, Bolivia, Argentina y más. Cotiza por peso o volumen.',
			'parent'=> 'servicios',
		),
		'carga-internacional' => array(
			'title' => 'Carga Internacional',
			'seo'   => 'Carga Internacional Aérea y Terrestre | Grenvíos',
			'desc'  => 'Transporte de carga internacional desde 20 kg hasta grandes volúmenes. Servicio aéreo y terrestre adaptado a tu negocio.',
			'parent'=> 'servicios',
		),
		'apostilla-y-traduccion' => array(
			'title' => 'Apostilla y Traducción',
			'seo'   => 'Apostilla y Traducción de Documentos | Grenvíos',
			'desc'  => 'Apostillado y traducción oficial de documentos para trámites en el extranjero: títulos, partidas, poderes y certificados. Servicio profesional desde {{origen_ciudad}}.',
			'parent'=> 'servicios',
		),
		'destinos' => array(
			'title' => 'Destinos',
			'seo'   => 'Destinos de Envío Internacional desde {{origen_pais}} | Grenvíos',
			'desc'  => 'Enviamos a más de 30 países en América, Europa, Asia y África. Consulta tiempos de entrega y cotiza tu envío internacional con Grenvíos.',
			'parent'=> '',
		),
		// Página B2B: los envíos de empresa tienen otra intención de búsqueda
		// ("envíos internacionales para empresas", "courier corporativo") y otro
		// ticket. Con una sola página de servicios se perdía esa demanda.
		'envios-para-empresas' => array(
			'title' => 'Envíos para Empresas',
			'seo'   => 'Envíos Internacionales para Empresas | Grenvíos',
			'desc'  => 'Envíos internacionales para empresas desde {{origen_pais}}: cuenta corporativa, tarifas por volumen, carga aérea y terrestre, y asesoría en documentación de exportación.',
			'parent'=> '',
		),
		'cotizar' => array(
			'title' => 'Cotizar',
			'seo'   => 'Cotiza tu Envío Internacional | Tarifas Grenvíos',
			'desc'  => 'Calcula el costo de tu envío internacional según peso, volumen y destino. Cotización personalizada y gratuita en minutos, por WhatsApp o formulario, con Grenvíos.',
			'parent'=> '',
		),
		'rastreo-de-envios' => array(
			'title' => 'Rastrea tu Envío',
			'seo'   => 'Rastrea tu Envío | Seguimiento Grenvíos',
			'desc'  => 'Ingresa tu número de guía y un asesor te confirma el estado de tu envío por WhatsApp: en tránsito, en aduana o entregado.',
			'parent'=> '',
		),
		'preguntas-frecuentes' => array(
			'title' => 'Preguntas Frecuentes',
			'seo'   => 'Preguntas Frecuentes sobre Envíos Internacionales | Grenvíos',
			'desc'  => 'Resolvemos tus dudas sobre envíos internacionales: qué se puede enviar, tiempos de entrega, impuestos por país y recojo a domicilio en {{origen_ciudad}}.',
			'parent'=> '',
		),
		'contacto' => array(
			'title' => 'Contacto',
			'seo'   => 'Contacto | Grenvíos — {{origen_ciudad}}, {{origen_pais}}',
			'desc'  => 'Escríbenos por WhatsApp, llama al {{contacto_telefono}} o visítanos en {{contacto_direccion}}. Atención de lunes a viernes y sábados por la mañana.',
			'parent'=> '',
		),
		'blog' => array(
			'title' => 'Blog',
			'seo'   => 'Blog | Grenvíos — Noticias y novedades de envíos internacionales',
			'desc'  => 'Noticias, guías y novedades sobre envíos internacionales de documentos, paquetes y carga con Grenvíos.',
			'parent'=> '',
		),
	);

	/* Filtro `grenvios_pages`: los módulos registran sus páginas sin tocar este
	 * archivo (ver inc/paginas-herramientas.php). */
	return apply_filters( 'grenvios_pages', $pages );
}

/* Países con página propia. Fusiona los FIJOS del tema con los agregados por el admin
 * (opción `grenvios_destinos_custom`, gestionados en inc/destinos.php). Data-driven. */
function grenvios_destinos() {
	$fixed  = grenvios_destinos_fixed();
	$custom = function_exists( 'grenvios_destinos_custom' ) ? grenvios_destinos_custom() : array();
	// Los fijos mandan si hubiera colisión de slug.
	// Filtro `grenvios_destinos`: lo usa el multiidioma para servir las fichas de
	// país traducidas (la CLAVE del array sigue siendo el slug español = maestro).
	return apply_filters( 'grenvios_destinos', array_merge( $custom, $fixed ) );
}

/* Los 9 países que vienen con el tema, con textos propios optimizados. */
function grenvios_destinos_fixed() {
	return array(
		'ecuador' => array(
			'title'   => 'Ecuador',
			'continente' => 'América',
			'seo'     => 'Envíos a Ecuador: Aéreo y Terrestre | Grenvíos',
			'desc'    => 'Envíos a Ecuador desde Lima: paquetes, documentos y carga por vía aérea o terrestre (Huaquillas), con entrega en 8 a 10 días hábiles.',
			'kw'      => 'envíos a ecuador',
			'tiempo'  => '8 a 10 días hábiles',
			'modos'   => 'Aéreo y terrestre',
			'entrega' => 'En agencia local',
			'restr'   => 'La vía terrestre ingresa por la frontera de Huaquillas. En envíos terrestres se aplica un impuesto aproximado del 18 % sobre el valor declarado en la boleta o factura, que se cancela en {{origen_ciudad}}; en destino solo se retira el envío.',
			'lead'    => 'Conecta con Ecuador de forma rápida y económica. Nuestra ruta terrestre por Huaquillas es ideal para paquetes y carga, mientras que la vía aérea acelera la entrega de documentos urgentes.',
			'servicio'=> 'envio-internacional-de-paquetes',
		),
		'colombia' => array(
			'title'   => 'Colombia',
			'continente' => 'América',
			'seo'     => 'Envíos a Colombia: Aéreo y Terrestre | Grenvíos',
			'desc'    => 'Envíos a Colombia desde Lima: paquetes y documentos en 10 a 15 días hábiles por vía terrestre, o más rápido por aérea. Retiro en agencia local.',
			'kw'      => 'envíos a colombia',
			'tiempo'  => '10 a 15 días hábiles',
			'modos'   => 'Aéreo y terrestre',
			'entrega' => 'En agencia local',
			'restr'   => 'En envíos terrestres se aplica un impuesto aproximado del 20 % sobre el valor declarado, que se cancela en {{origen_ciudad}}; en destino solo se retira el envío en la agencia local. La vía aérea es la opción más veloz para documentos.',
			'lead'    => 'Enviar a Colombia es sencillo con Grenvíos. Elige la vía terrestre para ahorrar en paquetes y carga, o la aérea cuando necesitas rapidez.',
			'servicio'=> 'envio-internacional-de-paquetes',
		),
		'chile' => array(
			'title'   => 'Chile',
			'continente' => 'América',
			'seo'     => 'Envíos a Chile: Aéreo y Terrestre a Domicilio | Grenvíos',
			'desc'    => 'Envíos a Chile desde Lima con entrega a domicilio: 10 a 20 días hábiles por vía terrestre, o más rápido por vía aérea. Cotiza en minutos.',
			'kw'      => 'envíos a chile',
			'tiempo'  => '10 a 20 días hábiles',
			'modos'   => 'Aéreo y terrestre',
			'entrega' => 'A domicilio',
			'restr'   => 'Una de nuestras rutas con entrega directa a domicilio. En envíos terrestres se aplica un impuesto aproximado del 29,5 % sobre el valor declarado, que se cancela en {{origen_ciudad}}.',
			'lead'    => 'A Chile llegamos hasta la puerta de tu destinatario. La vía terrestre es perfecta para paquetes y mudanzas pequeñas; la aérea, para entregas rápidas.',
			'servicio'=> 'envio-internacional-de-paquetes',
		),
		'bolivia' => array(
			'title'   => 'Bolivia',
			'continente' => 'América',
			'seo'     => 'Envíos a Bolivia: Aéreo y Terrestre | Grenvíos',
			'desc'    => 'Envíos a Bolivia desde Lima: paquetes, documentos y medicinas por vía terrestre o aérea, en 10 a 15 días hábiles, con retiro en agencia local.',
			'kw'      => 'envíos a bolivia',
			'tiempo'  => '10 a 15 días hábiles',
			'modos'   => 'Aéreo y terrestre',
			'entrega' => 'En agencia local',
			'restr'   => 'Aceptamos envío de medicinas y alimentos sellados por vía terrestre. En envíos terrestres se aplica un impuesto aproximado del 15 % sobre el valor declarado, que se cancela en {{origen_ciudad}}; en destino solo se retira el envío en la agencia local.',
			'lead'    => 'Enviamos paquetes, documentos y medicinas a Bolivia con total seguridad. Escoge la vía terrestre para mejor precio o la aérea para mayor rapidez.',
			'servicio'=> 'envio-internacional-de-paquetes',
		),
		'argentina' => array(
			'title'   => 'Argentina',
			'continente' => 'América',
			'seo'     => 'Envíos a Argentina: Aéreo y Terrestre | Grenvíos',
			'desc'    => 'Envíos a Argentina desde Lima: paquetes y carga en 10 a 20 días hábiles por vía terrestre, o más rápido por aérea, con retiro en agencia local.',
			'kw'      => 'envíos a argentina',
			'tiempo'  => '10 a 20 días hábiles',
			'modos'   => 'Aéreo y terrestre',
			'entrega' => 'En agencia local',
			'restr'   => 'En envíos terrestres se aplica un impuesto aproximado del 20 % sobre el valor declarado, que se cancela en {{origen_ciudad}}; en destino solo se retira el envío en la agencia local.',
			'lead'    => 'Llega a Argentina con tarifas competitivas. La vía terrestre es la favorita para paquetes y carga; la aérea acelera documentos y envíos urgentes.',
			'servicio'=> 'envio-internacional-de-paquetes',
		),
		'estados-unidos' => array(
			'title'   => 'Estados Unidos',
			'continente' => 'América',
			'seo'     => 'Envíos a Estados Unidos: Aéreo Puerta a Puerta | Grenvíos',
			'desc'    => 'Envíos a Estados Unidos desde Lima: documentos, paquetes y carga por vía aérea en 4 a 6 días hábiles, con entrega puerta a puerta.',
			'kw'      => 'envíos a estados unidos',
			'tiempo'  => '4 a 6 días hábiles',
			'modos'   => 'Aéreo',
			'entrega' => 'Puerta a puerta',
			'restr'   => 'Servicio aéreo expreso con entrega puerta a puerta en las principales ciudades.',
			'lead'    => 'A Estados Unidos enviamos documentos, paquetes y carga por vía aérea con uno de los tiempos más rápidos del mercado: entre 4 y 6 días hábiles, puerta a puerta.',
			'servicio'=> 'envio-internacional-de-documentos',
		),
		'espana' => array(
			'title'   => 'España',
			'continente' => 'Europa',
			'seo'     => 'Envíos a España: Aéreo Puerta a Puerta | Grenvíos',
			'desc'    => 'Envíos a España desde {{origen_ciudad}}: documentos, paquetes y carga por vía aérea en 4 a 7 días hábiles, con apostilla y traducción si las necesitas.',
			'kw'      => 'envíos a españa',
			'tiempo'  => '4 a 7 días hábiles',
			'modos'   => 'Aéreo',
			'entrega' => 'Puerta a puerta',
			'restr'   => 'Servicio aéreo a toda la península. Ideal para documentos legales, paquetes y compras.',
			'lead'    => 'Conecta {{origen_pais}} con España en pocos días. Nuestro servicio aéreo entrega documentos, paquetes y carga de forma segura y rápida.',
			'servicio'=> 'envio-internacional-de-documentos',
		),
		'venezuela' => array(
			'title'   => 'Venezuela',
			'continente' => 'América',
			'seo'     => 'Envíos a Venezuela: Aéreo Puerta a Puerta | Grenvíos',
			'desc'    => 'Envíos a Venezuela desde {{origen_ciudad}}: paquetes y documentos por vía aérea en 15 días hábiles, con entrega puerta a puerta. Cotiza tu envío en minutos.',
			'kw'      => 'envíos a venezuela',
			'tiempo'  => '15 días hábiles',
			'modos'   => 'Aéreo',
			'entrega' => 'Puerta a puerta',
			'restr'   => 'Servicio aéreo confiable para reconectar familias. Entrega puerta a puerta.',
			'lead'    => 'Hacemos llegar tus paquetes y documentos a Venezuela de forma segura por vía aérea, con entrega puerta a puerta.',
			'servicio'=> 'envio-internacional-de-paquetes',
		),
		'cuba' => array(
			'title'   => 'Cuba',
			'continente' => 'América',
			'seo'     => 'Envíos a Cuba: Paquetes y Medicinas | Grenvíos',
			'desc'    => 'Envíos a Cuba desde {{origen_ciudad}}: paquetes, documentos y medicinas con receta por vía aérea en 14 días hábiles. Cotiza tu envío internacional.',
			'kw'      => 'envíos a cuba',
			'tiempo'  => '14 días hábiles',
			'modos'   => 'Aéreo',
			'entrega' => 'En agencia local',
			'restr'   => 'Aceptamos medicinas acompañadas de su receta médica. Servicio aéreo especializado.',
			'lead'    => 'A Cuba enviamos paquetes, documentos y medicinas con receta por vía aérea, con tiempos de entrega de alrededor de 14 días hábiles.',
			'servicio'=> 'envio-internacional-de-paquetes',
		),
	);
}

/* Países adicionales listados en el hub /destinos/ (sin página propia todavía) */
function grenvios_destinos_extra() {
	return array(
		'Brasil'       => '12-18 días', 'Panamá'   => '5-8 días',  'Costa Rica' => '6-9 días',
		'Puerto Rico'  => '7-10 días',  'Uruguay'  => '12-20 días', 'Paraguay'   => '12-18 días',
		'Canadá'       => '5-8 días',   'México'   => '6-9 días',   'Italia'     => '5-8 días',
		'Francia'      => '5-8 días',   'Alemania' => '5-8 días',   'China'      => '7-12 días',
		'Japón'        => '7-12 días',  'Australia'=> '8-14 días',
	);
}

/* ─────────────────────────────────────────────────────────────
 * 5) SEO nativo: title tag, meta description, canonical, OpenGraph
 * ───────────────────────────────────────────────────────────── */
function grenvios_current_slug() {
	if ( is_front_page() ) return 'home';
	if ( is_home() ) return 'blog';   // página de entradas (listado del blog)
	if ( is_page() ) {
		// MULTIIDIOMA: una página traducida tiene su propio slug (/en/contact/),
		// pero plantillas, registro de textos, fondos, destinos y FAQ se
		// resuelven SIEMPRE por el slug español ("slug maestro").
		if ( function_exists( 'grenvios_canonical_slug' ) ) {
			return grenvios_canonical_slug( get_queried_object_id() );
		}
		return get_post_field( 'post_name', get_queried_object_id() );
	}
	return '';
}
function grenvios_seo_for_slug( $slug, $post_id = 0 ) {
	// 1) Title/description guardados en la propia pagina: es lo que rellena el
	//    traductor en cada idioma (y lo que puede ajustar el cliente a mano),
	//    asi que manda sobre los textos por defecto del tema.
	// OJO: en un archivo (categoría, etiqueta) get_queried_object_id() devuelve un
	// term_id, NO un post. Leer post-meta con él es incorrecto y puede acertar por
	// casualidad en el post cuyo ID coincida. Solo se consulta en contenido singular.
	$post_id = $post_id ? (int) $post_id : ( is_singular() ? (int) get_queried_object_id() : 0 );

	// La página del blog no es `is_singular()`, así que sin esto se quedaba sin
	// leer su propio title y description y salía con los de la versión maestra.
	if ( ! $post_id && is_home() && ! is_front_page() ) {
		$post_id = (int) get_option( 'page_for_posts' );
		if ( $post_id && function_exists( 'pll_get_post' ) && function_exists( 'grenvios_i18n_current' ) ) {
			$tid = (int) pll_get_post( $post_id, grenvios_i18n_current() );
			if ( $tid ) $post_id = $tid;
		}
	}
	if ( $post_id ) {
		$mt = (string) get_post_meta( $post_id, 'grenvios_seo_title', true );
		$md = (string) get_post_meta( $post_id, 'grenvios_seo_desc', true );
		if ( $mt !== '' || $md !== '' ) {
			$base = grenvios_seo_defaults_for_slug( $slug );
			/* Filtro `grenvios_seo_guardado`: lo usa inc/paises-cabecera.php para que
			 * la copia de una ruta que sigue con el title literal de Perú hable de
			 * su país (lo editado a mano se respeta). */
			return (array) apply_filters( 'grenvios_seo_guardado', array( $mt !== '' ? $mt : $base[0], $md !== '' ? $md : $base[1] ), $post_id, $slug );
		}
	}
	return grenvios_seo_defaults_for_slug( $slug );
}

/* Textos SEO por defecto del tema (idioma maestro).
 *
 * Filtro `grenvios_seo_defaults`: lo usa inc/paginas-combinadas.php para dar
 * título y meta description propios a las páginas por país, que no están en el
 * registro de páginas porque se crean una a una. */
function grenvios_seo_defaults_for_slug( $slug ) {
	$pages = grenvios_pages();
	$dest  = grenvios_destinos();

	if ( isset( $pages[ $slug ] ) )    $base = array( $pages[ $slug ]['seo'], $pages[ $slug ]['desc'] );
	elseif ( isset( $dest[ $slug ] ) ) $base = array( $dest[ $slug ]['seo'], $dest[ $slug ]['desc'] );
	else                               $base = array( '', '' );

	/* El filtro se aplica SIEMPRE, no solo cuando el tema no tiene nada escrito.
	 * Antes salía por `return` en las dos primeras ramas, así que un módulo no
	 * podía mejorar el title de una página existente y la pantalla «SEO por
	 * página» auditaba un title distinto del que se servía en el frontend. */
	return (array) apply_filters( 'grenvios_seo_defaults', $base, $slug );
}

add_filter( 'pre_get_document_title', function ( $title ) {
	$slug = grenvios_current_slug();
	list( $seo, $desc ) = grenvios_seo_for_slug( $slug );
	return $seo ? $seo : $title;
}, 20 );

/* Imagen para compartir en redes (og:image / twitter:image). Prioridad, para TODAS
 * las páginas:
 *   1) Imagen destacada de WordPress (la que se sube en la página/entrada).
 *   2) Hero/banner que el cliente puso en el editor de esa página:
 *      - destino-país  → imagen de fondo del banner (dst_hero_img)
 *      - demás páginas → fondo del banner por página (<prefijo>_bg_banner)
 *      - Inicio        → primera imagen del carrusel (repeater home_slides)
 *   3) Imagen por defecto del tema (slider-bg.jpg, editable en el Customizer).  */
/* Filtro `grenvios_og_image`: inc/rendimiento-a11y.php cambia el relleno por una foto real. */
function grenvios_og_image() {
	return (string) apply_filters( 'grenvios_og_image', grenvios_og_image_base() );
}
/* Imagen destacada de una página o entrada; si es la copia de una ruta de país
 * y no tiene la suya, la de su original (Perú). Así basta con ponerla una vez
 * y cada país puede cambiarla por la suya. */
function grenvios_imagen_destacada( $id, $tam = 'full' ) {
	$id = (int) $id;
	if ( ! $id ) return '';
	if ( has_post_thumbnail( $id ) ) return (string) get_the_post_thumbnail_url( $id, $tam );
	if ( function_exists( 'grenvios_i18n_master_id' ) ) {
		$m = (int) grenvios_i18n_master_id( $id );
		if ( $m && $m !== $id && has_post_thumbnail( $m ) ) return (string) get_the_post_thumbnail_url( $m, $tam );
	}
	return '';
}

/* Imagen destacada de la página con esa ruta («servicios/envio-de-equipaje»,
 * «destinos/ecuador»), en su versión del país actual si existe. Para los
 * listados que enlazan a páginas: así muestran la imagen que se le puso. */
function grenvios_imagen_de_ruta( $ruta, $tam = 'large' ) {
	$p = get_page_by_path( trim( (string) $ruta, '/' ) );
	if ( ! $p ) return '';
	$id = (int) $p->ID;
	if ( function_exists( 'pll_get_post' ) && function_exists( 'grenvios_i18n_current' ) ) {
		$t = (int) pll_get_post( $id, grenvios_i18n_current() );
		if ( $t ) $id = $t;
	}
	return grenvios_imagen_destacada( $id, $tam );
}

/* Página que se está viendo (también la del blog, que no es «singular»). */
function grenvios_og_post_id() {
	if ( is_singular() ) return (int) get_queried_object_id();
	if ( is_home() && ! is_front_page() && get_option( 'page_for_posts' ) ) {
		$id = (int) get_option( 'page_for_posts' );
		if ( function_exists( 'pll_get_post' ) && function_exists( 'grenvios_i18n_current' ) ) {
			$t = (int) pll_get_post( $id, grenvios_i18n_current() );
			if ( $t ) $id = $t;
		}
		return $id;
	}
	return 0;
}

function grenvios_og_image_base() {
	// 1) Imagen destacada de la página o entrada (o la de su original de Perú).
	$u = grenvios_imagen_destacada( grenvios_og_post_id() );
	if ( $u !== '' ) return $u;
	$slug = grenvios_current_slug();

	// 2a) Página de destino-país → imagen de fondo del banner del hero
	if ( function_exists( 'grenvios_destinos' ) ) {
		$dd = grenvios_destinos();
		if ( isset( $dd[ $slug ] ) ) {
			$h = grenvios_field( 'dst_hero_img', '' );
			if ( $h ) return $h;
		}
	}
	// 2b) Resto de páginas → fondo del banner por página (.page-header)
	if ( function_exists( 'grenvios_page_bg_fields' ) ) {
		$bg = grenvios_page_bg_fields();
		if ( isset( $bg[ $slug ] ) ) {
			foreach ( $bg[ $slug ] as $key => $def ) {
				$h = grenvios_field( $key, '' );
				if ( $h ) return $h;
				break;
			}
		}
	}
	// 2c) Inicio → primera imagen del carrusel
	if ( $slug === 'home' && function_exists( 'grenvios_repeater' ) ) {
		$slides = grenvios_repeater( 'home_slides' );
		if ( is_array( $slides ) && ! empty( $slides[0]['img'] ) ) return $slides[0]['img'];
	}
	// 3) Default del tema
	return grenvios_img_url( 'slider-bg.jpg' );
}

add_action( 'wp_head', function () {
	$slug = grenvios_current_slug();
	list( $seo, $desc ) = grenvios_seo_for_slug( $slug );

	if ( ! $desc && is_singular() ) {
		$desc = wp_strip_all_tags( get_the_excerpt() );
	}
	// Archivos (categoría/etiqueta): una categoría con 3+ entradas o con
	// descripción escapa al noindex de inc/seo-clusters.php y llega a Google. Sin
	// esto salía sin meta description. Se usa la descripción del término y, si no
	// la hay, una frase construida con su nombre.
	if ( ! $desc && ( is_category() || is_tag() || is_tax() ) ) {
		$term = get_queried_object();
		if ( $term && ! is_wp_error( $term ) ) {
			$td = wp_strip_all_tags( (string) $term->description );
			/* En una ruta, la misma descripción salía en los diez países (duplicado
			 * para Google): se antepone el país y se deja en 160 caracteres. */
			$cp = function_exists( 'grenvios_cab_pais' ) ? grenvios_cab_pais() : '';
			if ( $td !== '' && $cp !== '' ) {
				$td = 'Guías para enviar a ' . $cp . '. ' . $td;
				if ( mb_strlen( $td ) > 160 ) {
					$td = mb_substr( $td, 0, 157 );
					$td = mb_substr( $td, 0, (int) mb_strrpos( $td, ' ' ) ) . '…';
				}
			}
			$desc = $td !== ''
				? $td
				: sprintf(
					grenvios_t( 'Guías y artículos sobre %s de Grenvíos: consejos prácticos, requisitos y plazos para tus envíos internacionales desde {{origen_pais}}.' ),
					$term->name
				);
		}
	}
	/* Filtro `grenvios_meta_description`: lo usa inc/seo-entradas.php para que
	 * las entradas no salgan con el extracto entero (300 caracteres) y nombren
	 * su país en las rutas. */
	$desc = (string) apply_filters( 'grenvios_meta_description', $desc, $slug );
	if ( $desc ) $desc = wp_html_excerpt( $desc, 300, '…' );
	// Los tokens de sede ({{origen_ciudad}}, {{contacto_telefono}}…) no pasan por
	// grenvios_apply_text_tokens() aquí: esto es <head>, no plantilla. Sin esta
	// línea salían en crudo dentro del meta description y del OpenGraph.
	if ( function_exists( 'grenvios_sede_tokens_apply' ) ) {
		$seo  = grenvios_sede_tokens_apply( $seo );
		$desc = grenvios_sede_tokens_apply( $desc );
	}
	if ( is_singular() ) {
		$canonical = get_permalink();
	} elseif ( is_home() && get_option( 'page_for_posts' ) ) {
		$canonical = get_permalink( (int) get_option( 'page_for_posts' ) );
	} elseif ( is_front_page() ) {
		$canonical = home_url( '/' );
	} else {
		$canonical = home_url( add_query_arg( null, null ) );
	}

	/* Filtro `grenvios_canonical`: las páginas espejo de las rutas de país
	 * (inc/paises-espejos.php) apuntan su canónica a la página real del destino. */
	$canonical = apply_filters( 'grenvios_canonical', $canonical );

	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
	echo '<link rel="canonical" href="' . esc_url( $canonical ) . '">' . "\n";
	// Las entradas del blog son "article": cambia cómo las presentan las redes
	// sociales y es lo que esperan los validadores de OpenGraph.
	echo '<meta property="og:type" content="' . ( is_singular( 'post' ) ? 'article' : 'website' ) . '">' . "\n";
	if ( is_singular( 'post' ) ) {
		echo '<meta property="article:published_time" content="' . esc_attr( get_the_date( 'c' ) ) . '">' . "\n";
		echo '<meta property="article:modified_time" content="' . esc_attr( get_the_modified_date( 'c' ) ) . '">' . "\n";
	}
	echo '<meta property="og:site_name" content="Grenvíos">' . "\n";
	echo '<meta property="og:title" content="' . esc_attr( $seo ? $seo : wp_get_document_title() ) . '">' . "\n";
	if ( $desc ) echo '<meta property="og:description" content="' . esc_attr( $desc ) . '">' . "\n";
	echo '<meta property="og:url" content="' . esc_url( $canonical ) . '">' . "\n";

	// Imagen para compartir (OpenGraph + Twitter): imagen destacada, hero/banner de la
	// página o imagen por defecto del tema. (Ver grenvios_og_image()).
	$og_image = grenvios_og_image();
	echo '<meta property="og:image" content="' . esc_url( $og_image ) . '">' . "\n";
	echo '<meta property="og:image:alt" content="' . esc_attr( $seo ? $seo : 'Grenvíos — Envíos Internacionales' ) . '">' . "\n";
	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
	echo '<meta name="twitter:image" content="' . esc_url( $og_image ) . '">' . "\n";
}, 5 );

/* ─────────────────────────────────────────────────────────────
 * 5.b) Sitemap.xml nativo (sin plugins) en /sitemap.xml
 *      Incluye portada, todas las páginas publicadas y los posts.
 * ───────────────────────────────────────────────────────────── */
add_action( 'init', function () {
	add_rewrite_rule( '^sitemap\.xml$', 'index.php?grenvios_sitemap=1', 'top' );
	// Un sitemap por ruta de pais: /sitemap-cu.xml, /sitemap-es.xml...
	add_rewrite_rule( '^sitemap-([a-z]{2,6})\.xml$', 'index.php?grenvios_sitemap=$matches[1]', 'top' );

	/* WordPress añade barra final a todo lo que no reconoce como archivo y
	 * devolvía 301 de /sitemap.xml a /sitemap.xml/. Search Console acepta la
	 * redirección, pero la URL que se envía debe responder 200 directamente. */
	add_filter( 'redirect_canonical', function ( $redirect ) {
		return get_query_var( 'grenvios_sitemap' ) ? false : $redirect;
	} );
} );
// Desactiva el sitemap nativo (/wp-sitemap.xml) para dejar una sola fuente: /sitemap.xml
add_filter( 'wp_sitemaps_enabled', '__return_false' );
add_filter( 'query_vars', function ( $vars ) {
	$vars[] = 'grenvios_sitemap';
	return $vars;
} );

/* URLs del sitemap de UNA ruta. $lang vacio = todas (sitio de un solo idioma). */
function grenvios_sitemap_urls( $lang = '' ) {
	$front_id = (int) get_option( 'page_on_front' );
	$urls     = array();
	$multi    = function_exists( 'grenvios_i18n_active' ) && grenvios_i18n_active();

	// Portada de la ruta.
	if ( $lang !== '' && $multi ) {
		$langs = grenvios_i18n_langs();
		if ( isset( $langs[ $lang ] ) ) {
			// Con post_id, para que la compuerta pueda dejarla fuera igual que a
			// las demás: si la portada del país es una copia sin tocar, anunciarla
			// mientras se sirve con `noindex` es contradictorio.
			$fid = $front_id && function_exists( 'pll_get_post' ) ? (int) pll_get_post( $front_id, $lang ) : 0;
			$urls[] = array(
				'loc' => $langs[ $lang ]['url'], 'priority' => '1.0', 'changefreq' => 'daily',
				'post_id' => $fid ? $fid : null,
				// Sin esto la portada de cada ruta era la única URL del sitemap
				// sin <lastmod>, justo la que más veces se recorre.
				'lastmod' => $fid ? get_post_modified_time( 'c', true, $fid ) : '',
			);
		}
	} else {
		$urls[] = array(
			'loc' => home_url( '/' ), 'priority' => '1.0', 'changefreq' => 'daily',
			'lastmod' => $front_id ? get_post_modified_time( 'c', true, $front_id ) : '',
		);
		if ( $multi ) {
			foreach ( grenvios_i18n_langs() as $gl ) {
				if ( $gl['default'] ) continue;
				$fid = $front_id && function_exists( 'pll_get_post' ) ? (int) pll_get_post( $front_id, $gl['slug'] ) : 0;
				$urls[] = array(
					'loc' => $gl['url'], 'priority' => '1.0', 'changefreq' => 'daily',
					'lastmod' => $fid ? get_post_modified_time( 'c', true, $fid ) : '',
				);
			}
		}
	}

	$scope = ( $lang !== '' ) ? $lang : '';   // '' en Polylang = todos los idiomas

	/* Portadas de TODAS las rutas, no solo la maestra. Cada ruta tiene su propia
	 * copia de la página de inicio y ya se añade arriba como portada; sin esto
	 * volvía a salir en el listado y la URL quedaba duplicada en el sitemap. */
	$portadas = array();
	if ( $front_id ) {
		$portadas[ $front_id ] = true;
		if ( $multi && function_exists( 'pll_get_post' ) ) {
			foreach ( grenvios_i18n_langs() as $gl ) {
				$fid = (int) pll_get_post( $front_id, $gl['slug'] );
				if ( $fid ) $portadas[ $fid ] = true;
			}
		}
	}

	// Páginas publicadas (excepto la portada, ya incluida)
	$pages = get_posts( array( 'post_type' => 'page', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'menu_order', 'order' => 'ASC', 'lang' => $scope ) );
	foreach ( $pages as $p ) {
		if ( isset( $portadas[ (int) $p->ID ] ) ) continue;
		$urls[] = array(
			'loc'        => get_permalink( $p ),
			'post_id'    => (int) $p->ID,
			'lastmod'    => get_post_modified_time( 'c', true, $p ),
			'priority'   => ( $p->post_parent ? '0.7' : '0.8' ),
			'changefreq' => 'weekly',
		);
	}

	// Entradas del blog publicadas
	$posts = get_posts( array( 'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => -1, 'lang' => $scope ) );
	foreach ( $posts as $p ) {
		$urls[] = array(
			'loc'        => get_permalink( $p ),
			'post_id'    => (int) $p->ID,
			'lastmod'    => get_post_modified_time( 'c', true, $p ),
			'priority'   => '0.6',
			'changefreq' => 'monthly',
		);
	}

	// Filtro `grenvios_sitemap_urls`: excluye URLs que no queremos que Google
	// rastree (rutas de país aún sin contenido propio, combinaciones vacías).
	$urls = apply_filters( 'grenvios_sitemap_urls', $urls, $lang );

	if ( function_exists( 'grenvios_i18n_sitemap_alternates' ) ) {
		$urls = grenvios_i18n_sitemap_alternates( $urls );
	}
	return $urls;
}

/* Rutas que tienen sitemap propio. Vacío = el sitio no está dividido por país. */
function grenvios_sitemap_rutas() {
	if ( ! function_exists( 'grenvios_i18n_active' ) || ! grenvios_i18n_active() ) return array();
	$langs = grenvios_i18n_langs();
	return count( $langs ) > 1 ? array_keys( $langs ) : array();
}

/* Imagen principal de una página, para el sitemap de imágenes.
 *
 * Misma cadena que grenvios_og_image() —destacada, luego el hero que el cliente
 * puso en el editor, luego la del tema— pero por ID en vez de sobre la página
 * que se está pintando, porque el sitemap las recorre todas de una vez. Si una
 * página de país no tiene imagen propia, hereda la que corresponda: ninguna URL
 * del sitemap se queda sin imagen. */
function grenvios_sitemap_image( $post_id ) {
	return (string) apply_filters( 'grenvios_sitemap_image', grenvios_sitemap_image_base( $post_id ), $post_id );
}
function grenvios_sitemap_image_base( $post_id ) {
	$post_id = (int) $post_id;
	if ( ! $post_id ) return '';

	// 1) Imagen destacada de la página (o la de su original de Perú).
	$u = grenvios_imagen_destacada( $post_id );
	if ( $u !== '' ) return $u;

	// 2) Hero o banner puesto en el editor de esa página.
	foreach ( get_post_meta( $post_id ) as $k => $v ) {
		if ( strpos( $k, 'grenvios_' ) !== 0 ) continue;
		if ( $k !== 'grenvios_dst_hero_img' && substr( $k, -10 ) !== '_bg_banner' ) continue;
		$u = is_array( $v ) ? reset( $v ) : $v;
		if ( is_string( $u ) && preg_match( '~^https?://~i', $u ) ) return $u;
	}

	// 3) La del tema.
	return function_exists( 'grenvios_img_url' ) ? grenvios_img_url( 'slider-bg.jpg' ) : '';
}

add_action( 'template_redirect', function () {
	$q = get_query_var( 'grenvios_sitemap' );
	if ( ! $q ) return;

	header( 'Content-Type: application/xml; charset=UTF-8' );
	$rutas = grenvios_sitemap_rutas();

	/* ── ÍNDICE: /sitemap.xml con un sitemap por país ──
	 * Search Console acepta el índice y luego deja ver la cobertura de cada
	 * hijo por separado, que es la única forma de saber si Google está
	 * indexando la ruta de Cuba o solo la de Perú. Con un único sitemap de 200
	 * URLs mezcladas ese dato no se puede leer. */
	if ( $q === '1' && $rutas ) {
		echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
		foreach ( $rutas as $slug ) {
			$u = grenvios_sitemap_urls( $slug );
			if ( ! $u ) continue;   // ruta entera sin nada indexable: no se anuncia
			$last = '';
			foreach ( $u as $x ) if ( ! empty( $x['lastmod'] ) && $x['lastmod'] > $last ) $last = $x['lastmod'];
			echo "\t<sitemap>\n";
			echo "\t\t<loc>" . esc_url( home_url( '/sitemap-' . $slug . '.xml' ) ) . "</loc>\n";
			if ( $last ) echo "\t\t<lastmod>" . esc_html( $last ) . "</lastmod>\n";
			echo "\t</sitemap>\n";
		}
		echo '</sitemapindex>';
		exit;
	}

	// /sitemap.xml en un sitio de una sola ruta, o /sitemap-<pais>.xml
	$lang = ( $q === '1' ) ? '' : (string) $q;
	if ( $lang !== '' && ! in_array( $lang, $rutas, true ) ) {
		status_header( 404 );
		echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"></urlset>";
		exit;
	}

	$urls = grenvios_sitemap_urls( $lang );

	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">' . "\n";
	foreach ( $urls as $u ) {
		echo "\t<url>\n";
		echo "\t\t<loc>" . esc_url( $u['loc'] ) . "</loc>\n";
		// Google exige que TODAS las versiones de idioma se declaren entre si.
		if ( ! empty( $u['alt'] ) ) {
			foreach ( $u['alt'] as $hl => $href ) {
				echo "\t\t<xhtml:link rel=\"alternate\" hreflang=\"" . esc_attr( $hl ) . "\" href=\"" . esc_url( $href ) . "\" />\n";
			}
		}
		// Imagen principal de la página, para que entre en Google Imágenes.
		if ( ! empty( $u['post_id'] ) && function_exists( 'grenvios_sitemap_image' ) ) {
			$img = grenvios_sitemap_image( (int) $u['post_id'] );
			if ( $img ) echo "\t\t<image:image><image:loc>" . esc_url( $img ) . "</image:loc></image:image>\n";
		}
		if ( ! empty( $u['lastmod'] ) ) echo "\t\t<lastmod>" . esc_html( $u['lastmod'] ) . "</lastmod>\n";
		echo "\t\t<changefreq>" . esc_html( $u['changefreq'] ) . "</changefreq>\n";
		echo "\t\t<priority>" . esc_html( $u['priority'] ) . "</priority>\n";
		echo "\t</url>\n";
	}
	echo '</urlset>';
	exit;
} );

/* robots.txt virtual: apunta al sitemap y protege el admin */
add_filter( 'robots_txt', function ( $output, $public ) {
	if ( ! $public ) return $output; // sitio en "no indexar": no tocar
	$lines = array(
		'User-agent: *',
		'Allow: /',
		'Disallow: /wp-admin/',
		'Allow: /wp-admin/admin-ajax.php',
		'',
		'Sitemap: ' . home_url( '/sitemap.xml' ),
		'',
	);
	return implode( "\n", $lines );
}, 10, 2 );

/* ─────────────────────────────────────────────────────────────
 * 6) Schema.org (JSON-LD) según el tipo de página
 * ───────────────────────────────────────────────────────────── */
add_action( 'wp_head', function () {
	$b    = grenvios_biz();
	$slug = grenvios_current_slug();
	$home = untrailingslashit( home_url() );
	$graph = array();

	// Organization / LocalBusiness siempre
	$org = array(
		'@type'       => 'LocalBusiness',
		'@id'         => $home . '/#organization',
		'name'        => $b['name'],
		'url'         => $home . '/',
		'telephone'   => $b['phone_tel'],
		'email'       => $b['email'],
		'address'     => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => $b['address'],
			'addressLocality' => $b['city'],
			'addressCountry'  => $b['country'],
		),
		'areaServed'  => 'Worldwide',
		'priceRange'  => '$$',
		'sameAs'      => array(
			'https://www.facebook.com/Grenviios/',
			'https://www.instagram.com/grenvioss/',
			'https://www.tiktok.com/@grenvios',
		),
		'openingHoursSpecification' => array(
			array(
				'@type'     => 'OpeningHoursSpecification',
				'dayOfWeek' => array( 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday' ),
				'opens'     => '09:00',
				'closes'    => '18:00',
			),
			array(
				'@type'     => 'OpeningHoursSpecification',
				'dayOfWeek' => 'Saturday',
				'opens'     => '09:00',
				'closes'    => '13:00',
			),
		),
	);
	/* Filtro `grenvios_schema_organization`: lo usa inc/sedes-seo.php para que
	 * cada sede emita su propio negocio (identificador, URL, ciudad y redes) en
	 * vez de que las diez declaren ser la misma oficina de {{origen_ciudad}}. */
	$org     = (array) apply_filters( 'grenvios_schema_organization', $org );
	$graph[] = $org;

	// WebSite (identidad del sitio para buscadores). El publisher apunta al
	// mismo @id que acabe teniendo la organización, que cambia por sede.
	$site_url = isset( $org['url'] ) ? $org['url'] : $home . '/';
	$lang_tag = function_exists( 'grenvios_i18n_hreflang' ) && function_exists( 'grenvios_i18n_current' )
		? grenvios_i18n_hreflang( grenvios_i18n_current() )
		: 'es-PE';
	$graph[] = array(
		'@type'      => 'WebSite',
		'@id'        => untrailingslashit( $site_url ) . '/#website',
		'url'        => $site_url,
		'name'       => $b['name'],
		'inLanguage' => $lang_tag,
		'publisher'  => array( '@id' => isset( $org['@id'] ) ? $org['@id'] : $home . '/#organization' ),
	);

	// Service en páginas de servicios
	$pages = grenvios_pages();
	if ( isset( $pages[ $slug ] ) && $pages[ $slug ]['parent'] === 'servicios' ) {
		$graph[] = array(
			'@type'       => 'Service',
			'name'        => $pages[ $slug ]['title'],
			'description' => $pages[ $slug ]['desc'],
			'provider'    => array( '@id' => isset( $org['@id'] ) ? $org['@id'] : $home . '/#organization' ),
			'areaServed'  => 'Worldwide',
			'serviceType' => 'Envío internacional',
		);
	}
	// Service en páginas de destino
	$dest = grenvios_destinos();
	if ( isset( $dest[ $slug ] ) ) {
		$graph[] = array(
			'@type'       => 'Service',
			'name'        => 'Envíos a ' . $dest[ $slug ]['title'],
			'description' => $dest[ $slug ]['desc'],
			'provider'    => array( '@id' => isset( $org['@id'] ) ? $org['@id'] : $home . '/#organization' ),
			'areaServed'  => $dest[ $slug ]['title'],
			'serviceType' => 'Envío internacional',
		);
	}
	echo '<script type="application/ld+json">' . wp_json_encode( array(
		'@context' => 'https://schema.org',
		'@graph'   => $graph,
	), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}, 6 );

/* Schema Article/BlogPosting en cada entrada del blog */
add_action( 'wp_head', function () {
	if ( ! is_singular( 'post' ) ) return;
	$id   = get_queried_object_id();
	$home = untrailingslashit( home_url() );
	$img  = has_post_thumbnail( $id ) ? get_the_post_thumbnail_url( $id, 'full' ) : grenvios_img_url( 'slider-bg.jpg' );
	$node = array(
		'@context'         => 'https://schema.org',
		'@type'            => 'BlogPosting',
		'mainEntityOfPage' => get_permalink( $id ),
		'headline'         => get_the_title( $id ),
		'description'      => wp_strip_all_tags( get_the_excerpt( $id ) ),
		'image'            => $img,
		'datePublished'    => get_the_date( 'c', $id ),
		'dateModified'     => get_the_modified_date( 'c', $id ),
		'author'           => array( '@type' => 'Person', 'name' => get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $id ) ) ),
		'publisher'        => array( '@id' => $home . '/#organization' ),
	);
	/* Filtro `grenvios_blogposting_node`: autor «Grenvíos» e imagen real (inc/blog-detalle.php). */
	$node = (array) apply_filters( 'grenvios_blogposting_node', $node, $id );
	echo '<script type="application/ld+json">' . wp_json_encode( $node, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}, 7 );

/* ─────────────────────────────────────────────────────────────
 * 7) Breadcrumbs visibles + BreadcrumbList schema
 *    $crumbs: array de [label => url]; el último es la página actual.
 * ───────────────────────────────────────────────────────────── */
function logisko_breadcrumbs( $crumbs = array() ) {
	$home = untrailingslashit( home_url() );

	if ( empty( $crumbs ) ) {
		// Construir automáticamente desde la jerarquía de la página
		$crumbs = array( apply_filters( 'grenvios_miga_inicio', grenvios_t( 'Inicio' ) ) => $home . '/' );
		if ( is_page() ) {
			$id        = get_queried_object_id();
			$ancestors = array_reverse( get_post_ancestors( $id ) );
			foreach ( $ancestors as $aid ) {
				$crumbs[ get_the_title( $aid ) ] = get_permalink( $aid );
			}
			$crumbs[ get_the_title( $id ) ] = '';
		}
	}

	if ( count( $crumbs ) < 2 ) return;

	echo '<nav class="breadcrumbs" aria-label="Breadcrumb"><ul class="breadcrumb-list">';
	$items = array();
	$pos   = 1;
	$last  = array_key_last( $crumbs );
	foreach ( $crumbs as $label => $url ) {
		$is_last = ( $label === $last );
		if ( $url && ! $is_last ) {
			echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
		} else {
			echo '<li><span aria-current="page">' . esc_html( $label ) . '</span></li>';
		}
		$items[] = array(
			'@type'    => 'ListItem',
			'position' => $pos,
			'name'     => $label,
			'item'     => $url ? $url : ( is_singular() ? get_permalink() : $home . '/' ),
		);
		$pos++;
	}
	echo '</ul></nav>';

	echo '<script type="application/ld+json">' . wp_json_encode( array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $items,
	), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>';
}

/* Preguntas frecuentes (compartidas por el partial y el schema FAQPage) */
function grenvios_faqs() {
	return array(
		array( '¿Puedo enviar alimentos?', 'Por vía aérea no está permitido. Por vía terrestre sí aceptamos alimentos sellados (no perecibles) hacia Ecuador y Bolivia.' ),
		array( '¿Qué no se puede enviar?', 'Por vía aérea está prohibido enviar productos de consumo, líquidos, cremas, objetos con batería interna, televisores y dinero. Por vía terrestre (excepto Chile) sí se pueden enviar líquidos, cremas, productos de consumo sellados no perecibles y objetos con batería.' ),
		array( '¿Puedo enviar medicina?', 'Solo a Cuba por vía aérea presentando la receta médica, y por vía terrestre hacia Ecuador y Bolivia.' ),
		array( '¿Qué debo presentar para hacer mi envío?', 'La boleta o factura de la mercancía que vas a enviar.' ),
		array( '¿Puedo enviar prendas de vestir?', 'Sí. Si son de marca nacional puedes enviar la cantidad que desees. Si son originales o réplicas de marcas reconocidas (Adidas, Nike, Puma, etc.), solo se admiten dos prendas por marca o réplica.' ),
		array( '¿Hacen recojos en {{origen_ciudad}}?', 'Sí. Solicita la recogida a domicilio —en tu casa, trabajo o proveedor— en {{origen_ciudad}}. Tiene un costo que depende del distrito.' ),
		array( '¿Puedo enviar desde provincia?', 'Sí. Puedes enviarlo desde una agencia local de transporte a nuestra sede en {{origen_ciudad}}, y desde aquí lo despachamos a su destino internacional.' ),
		array( '¿El pago es contra entrega?', 'No. Todos los envíos deben cancelarse en {{origen_ciudad}} antes de ser despachados. En destino el cliente solo retira su envío.' ),
		array( '¿Se cobra por peso o por volumen?', 'Se considera el mayor entre el peso real y el peso volumétrico (alto × largo × ancho en cm ÷ 5000).' ),
		array( '¿Cuáles son los pasos para hacer un envío?', 'Localiza nuestra oficina o solicita tu recojo; arma tu paquete (en oficina puedes adquirir una caja adecuada); si pides recojo a domicilio, pésalo y toma las medidas de alto, largo y ancho en cm. Una vez aprobada la cotización, coordinamos el envío y te enviamos la etiqueta o guía para pegarla a cada paquete. Cuando llegue a nuestro centro te enviamos la factura final y los datos bancarios para el pago.' ),
	);
}

/* FAQ efectivas de la página de Preguntas Frecuentes: las editadas en el repeater
 * "page_faq" (post-meta) o, si no se editaron, la lista global del tema. */
function grenvios_faq_pairs() {
	$items = function_exists( 'grenvios_repeater' ) ? grenvios_repeater( 'page_faq' ) : array();
	$faqs  = array();
	foreach ( (array) $items as $it ) {
		$q = isset( $it['q'] ) ? $it['q'] : ( isset( $it[0] ) ? $it[0] : '' );
		$a = isset( $it['a'] ) ? $it['a'] : ( isset( $it[1] ) ? $it[1] : '' );
		if ( trim( (string) $q ) === '' && trim( (string) $a ) === '' ) continue;
		$faqs[] = array( $q, $a );
	}
	return $faqs ? $faqs : grenvios_faqs();
}

/* ─────────────────────────────────────────────────────────────
 * FAQ por página: cada página tiene sus propias preguntas frecuentes
 * (contenido + keywords + schema FAQPage) para reforzar SEO.
 * Las páginas de destino generan FAQs dinámicas con sus propios datos.
 * ───────────────────────────────────────────────────────────── */
/* Resuelve los tokens de sede en un juego de FAQs (pregunta y respuesta).
 * Las FAQ alimentan a la vez el HTML y el JSON-LD, así que si no se resuelven
 * aquí los tokens acaban dentro del schema que lee Google. */
function grenvios_faqs_tokens( $faqs ) {
	if ( ! is_array( $faqs ) || ! function_exists( 'grenvios_sede_tokens_apply' ) ) return $faqs;
	foreach ( $faqs as $i => $f ) {
		if ( ! is_array( $f ) ) continue;
		foreach ( $f as $j => $txt ) {
			if ( is_string( $txt ) ) $faqs[ $i ][ $j ] = grenvios_sede_tokens_apply( $txt );
		}
	}
	return $faqs;
}

function grenvios_page_faqs( $slug ) {
	/* Filtro `grenvios_page_faqs`: lo usa el módulo de rutas de país para que las
	 * preguntas de una copia hablen de SU destino (ver inc/paises-mas-contenido.php).
	 * Va antes de resolver los tokens, así que las respuestas admiten {{…}}. */
	$faqs = apply_filters( 'grenvios_page_faqs', grenvios_page_faqs_raw( $slug ), $slug );
	return grenvios_faqs_tokens( $faqs );
}

function grenvios_page_faqs_raw( $slug ) {
	// Página de Preguntas Frecuentes: sus FAQ por defecto son la lista global del tema
	// (así el repeater "page_faq" queda editable también en esta página).
	if ( $slug === 'preguntas-frecuentes' ) return grenvios_faqs();

	// Destinos: FAQs únicas por país a partir de sus datos (evita contenido duplicado)
	$dest = grenvios_destinos();
	if ( isset( $dest[ $slug ] ) ) {
		$d = $dest[ $slug ];
		$t = $d['title'];
		return array(
			array( "¿Cuánto demora un envío a {$t}?", "El tiempo estimado de entrega a {$t} es de {$d['tiempo']}, según la modalidad disponible ({$d['modos']}) y la ciudad de destino. Para documentos urgentes, la vía aérea es la opción más rápida." ),
			array( "¿Cómo recibo mi envío en {$t}?", "La forma de entrega en {$t} es: " . mb_strtolower( $d['entrega'] ) . ". " . $d['restr'] ),
			array( "¿Qué puedo enviar a {$t}?", "Puedes enviar documentos, paquetes, equipaje, compras y carga comercial a {$t}. Algunos productos tienen restricciones de aduana según su contenido; nuestro equipo te orienta antes de enviar." ),
			array( "¿Cómo cotizo mi envío a {$t}?", "Solicita tu cotización en línea desde la página de cotización o escríbenos por WhatsApp al {{contacto_telefono}}. Te respondemos en minutos con el precio según peso, volumen y destino." ),
		);
	}

	$faqs = array(
		'peso-volumetrico' => array(
			array( '¿Cómo se calcula el peso volumétrico?', 'Se multiplican alto × largo × ancho en centímetros y se divide entre 5000. El resultado está en kilos. Si ese número es mayor que el peso real de la balanza, el envío se cobra por el volumétrico.' ),
			array( '¿Por qué se cobra por volumen y no solo por peso?', 'Porque en el avión y en el camión el espacio es limitado. Una caja grande y liviana ocupa el mismo lugar que una pequeña y pesada, así que todas las empresas de envío cobran el mayor de los dos pesos.' ),
			array( '¿Puedo bajar el precio reduciendo la caja?', 'Sí, y suele ser el ajuste más rentable. Si la caja va holgada, unos centímetros menos reducen el peso volumétrico y por tanto el costo. En nuestra oficina puedes adquirir una caja del tamaño adecuado.' ),
			array( '¿Las medidas son con la caja cerrada?', 'Sí. Mide la caja ya armada y cerrada, por su parte más ancha, incluyendo cualquier abultamiento. Si el paquete no es rectangular, toma la medida máxima de cada lado.' ),
		),
		'que-se-puede-enviar' => array(
			array( '¿Puedo enviar alimentos?', 'Por vía aérea no está permitido. Por vía terrestre sí aceptamos alimentos sellados no perecibles hacia Ecuador y Bolivia.' ),
			array( '¿Puedo enviar celulares, laptops o cosas con batería?', 'Por vía aérea no: los objetos con batería interna están prohibidos. Por vía terrestre sí se pueden enviar, salvo a Chile.' ),
			array( '¿Puedo enviar medicinas?', 'Solo a Cuba por vía aérea presentando la receta médica, y por vía terrestre hacia Ecuador y Bolivia.' ),
			array( '¿Qué pasa si envío algo no permitido?', 'La aduana puede retener o destruir el envío, y el costo no se devuelve. Por eso revisamos el contenido contigo antes de despachar: es mejor perder cinco minutos que perder el paquete.' ),
			array( '¿Puedo enviar prendas de marca?', 'Sí. Si son de marca nacional, la cantidad que desees. Si son originales o réplicas de marcas reconocidas, solo se admiten dos prendas por marca.' ),
		),
		'envio-de-equipaje' => array(
			array( '¿Sale más barato que pagar exceso de equipaje?', 'En la mayoría de los casos sí, sobre todo a partir de la segunda maleta. Depende del peso, el volumen y el destino: cotízalo y compáralo con lo que cobra tu aerolínea.' ),
			array( '¿Puedo enviar mi maleta tal cual?', 'Sí, la maleta se envía como bulto. Te recomendamos envolverla o protegerla; en la oficina te ayudamos con el embalaje.' ),
			array( '¿El equipaje personal paga impuestos?', 'El equipaje personal usado suele tener un trato aduanero distinto al de la mercancía nueva, pero cada país fija sus propios límites de valor. Te indicamos qué declarar según tu destino.' ),
			array( '¿Recogen en mi domicilio en {{origen_ciudad}}?', 'Sí. Coordinamos el recojo en tu casa, hotel o trabajo dentro de {{origen_ciudad}}. Tiene un costo que depende del distrito.' ),
		),
		'envios-para-empresas' => array(
			array( '¿Qué necesita mi empresa para abrir una cuenta con Grenvíos?', 'Solo el RUC de la empresa y un contacto responsable de los envíos. Coordinamos una tarifa según tu volumen y frecuencia mensual, y te asignamos un asesor que atiende todos tus despachos.' ),
			array( '¿Hacen tarifas por volumen?', 'Sí. A partir de un volumen o una frecuencia mensual estable trabajamos con tarifas preferenciales, distintas de las del envío puntual. Cuéntanos cuántos envíos haces al mes y a qué destinos para armarte una propuesta.' ),
			array( '¿Emiten factura y trabajan con órdenes de compra?', 'Sí. Emitimos factura electrónica a nombre de la empresa y podemos trabajar contra orden de compra, con consolidado mensual de los envíos realizados.' ),
			array( '¿Me ayudan con la documentación de exportación?', 'Sí. Te orientamos sobre la factura comercial, la ficha técnica del producto, las restricciones de aduana del país de destino y los límites de valor, antes de que la mercancía salga de {{origen_ciudad}}.' ),
			array( '¿Pueden recoger la mercancía en mi almacén?', 'Sí, coordinamos el recojo en tu local, almacén o proveedor en {{origen_ciudad}}. Para envíos recurrentes fijamos días de recojo para que no tengas que solicitarlo cada vez.' ),
		),
		'home' => array(
			array( '¿Qué tipos de envíos internacionales realizan?', 'En Grenvíos enviamos documentos, paquetes, equipaje, compras y carga internacional desde {{origen_ciudad}}, {{origen_pais}}, hacia América, Europa, Asia y África, por vía aérea y terrestre.' ),
			array( '¿A qué países puedo enviar?', 'Llegamos a más de 30 países. Tenemos rutas destacadas a Ecuador, Colombia, Chile, Bolivia, Argentina, Estados Unidos, España, Venezuela y Cuba, entre muchos otros destinos.' ),
			array( '¿Cómo calculo el costo de mi envío?', 'El costo depende del peso real o el peso volumétrico (el mayor), el destino y la modalidad. Puedes solicitar una cotización en línea y te respondemos en minutos.' ),
			array( '¿Puedo rastrear mi envío?', 'Sí. Con tu número de guía, un asesor te confirma el estado de tu envío por WhatsApp: en tránsito, en aduana o entregado. Pronto habilitaremos también el seguimiento en línea.' ),
		),
		'nosotros' => array(
			array( '¿Dónde está ubicada Grenvíos?', 'Estamos en {{contacto_direccion}}, Perú. Atendemos de lunes a viernes de 9:00 a 18:00 h y sábados de 9:00 a 13:00 h, y coordinamos recojos a domicilio en {{origen_ciudad}}.' ),
			array( '¿Por qué elegir Grenvíos para mis envíos internacionales?', 'Combinamos rutas aéreas y terrestres, ofrecemos seguimiento puerta a puerta, asesoría en aduanas y atención por WhatsApp, con tarifas competitivas frente a los grandes couriers.' ),
			array( '¿Atienden a empresas y a personas?', 'Sí. Trabajamos tanto con envíos personales (documentos, paquetes, compras) como con carga comercial para negocios e importadores.' ),
		),
		'servicios' => array(
			array( '¿Qué servicios de envío internacional ofrecen?', 'Ofrecemos envío de documentos, envío de paquetes, carga internacional y apostilla y traducción de documentos, todos con cobertura internacional aérea y terrestre.' ),
			array( '¿Cuál es la diferencia entre paquete y carga?', 'Los paquetes son envíos personales o de menor volumen; la carga internacional aplica a partir de 20 kg y grandes volúmenes, con documentación comercial. Si tienes dudas, te ayudamos a clasificar tu envío.' ),
			array( '¿Hacen recojo a domicilio?', 'Sí. Coordinamos el recojo de tu envío a domicilio en {{origen_ciudad}} o puedes acercarte a nuestra oficina en {{contacto_direccion}}.' ),
		),
		'envio-internacional-de-documentos' => array(
			array( '¿Qué documentos puedo enviar al extranjero?', 'Documentos legales y notariales, títulos y certificados académicos, DNI, pasaportes, contratos, poderes y correspondencia oficial. No se admite dinero en efectivo, tarjetas ni cheques.' ),
			array( '¿Cuánto demora el envío de documentos internacional?', 'Por vía aérea, entre 4 y 6 días hábiles a América y Europa, y 6 a 7 días a Asia, con entrega puerta a puerta.' ),
			array( '¿El sobre va protegido?', 'Sí. Cada envío de documentos se entrega dentro de un sobre A4 sellado que protege tu documentación durante todo el trayecto.' ),
			array( '¿Necesito apostillar o traducir mis documentos?', 'Depende del trámite y el país de destino. También ofrecemos el servicio de apostilla y traducción de documentos para que tu documentación sea válida al llegar.' ),
		),
		'envio-internacional-de-paquetes' => array(
			array( '¿Cómo se calcula el precio de un paquete internacional?', 'Se cobra por el peso real o el peso volumétrico (alto × largo × ancho ÷ 5000), el que sea mayor, más el destino y la modalidad (aérea o terrestre).' ),
			array( '¿Puedo enviar equipaje y compras personales?', 'Sí. Enviamos equipaje, compras y encomiendas personales. También recibimos compras en {{origen_ciudad}} para consolidarlas y enviarlas a tu destino.' ),
			array( '¿Qué modalidad conviene, aérea o terrestre?', 'La vía terrestre es más económica y ideal para países vecinos; la aérea es más rápida. Te asesoramos según el destino, el peso y la urgencia.' ),
		),
		'carga-internacional' => array(
			array( '¿Desde qué peso aplica la carga internacional?', 'La carga internacional aplica a partir de 20 kg y hasta grandes volúmenes, tanto por vía aérea como terrestre.' ),
			array( '¿Qué documentación necesito para enviar carga?', 'Generalmente boleta o factura comercial y ficha técnica del producto. Te indicamos los requisitos exactos según el destino y el tipo de mercancía.' ),
			array( '¿Manejan carga para empresas e importadores?', 'Sí. Diseñamos soluciones de carga adaptadas a negocios, con asesoría aduanera y tarifas por volumen.' ),
		),
		'apostilla-y-traduccion' => array(
			array( '¿Qué es la apostilla de un documento?', 'Es una certificación que valida la autenticidad de un documento público para que tenga efecto legal en otro país miembro del Convenio de La Haya. Es habitual para títulos, partidas y poderes.' ),
			array( '¿Qué documentos puedo apostillar?', 'Títulos y certificados académicos, partidas de nacimiento, matrimonio y defunción, poderes y documentos notariales, antecedentes penales y documentos comerciales, entre otros.' ),
			array( '¿En qué idiomas traducen?', 'Realizamos traducción profesional en los pares de idiomas más solicitados, como inglés e italiano, manteniendo el formato y la validez legal del documento.' ),
			array( '¿Pueden apostillar, traducir y enviar el documento?', 'Sí. Reunimos legalización, traducción y envío internacional en un solo lugar, para que recibas tu documento listo para el trámite en su destino.' ),
		),
		'destinos' => array(
			array( '¿A cuántos países envían?', 'Enviamos a más de 30 países en América, Europa, Asia y África, combinando rutas aéreas y terrestres desde {{origen_ciudad}}.' ),
			array( '¿Qué países tienen envío terrestre?', 'La vía terrestre está disponible principalmente para países vecinos como Ecuador, Colombia, Chile, Bolivia y Argentina, ideal para paquetes y carga a mejor precio.' ),
			array( '¿Mi país no está en la lista, igual pueden enviar?', 'Sí. Además de los destinos con página propia, cubrimos muchos países más. Escríbenos por WhatsApp y te confirmamos tiempos y tarifa para tu destino.' ),
		),
		'cotizar' => array(
			array( '¿Cuánto cuesta cotizar mi envío?', 'La cotización es gratuita y sin compromiso. Completas el formulario o nos escribes por WhatsApp y te enviamos el precio según peso, volumen y destino.' ),
			array( '¿Qué datos necesito para cotizar?', 'El país de destino, el tipo de envío (documentos, paquete o carga), el peso aproximado y, si es posible, las dimensiones del paquete para calcular el peso volumétrico.' ),
			array( '¿En cuánto tiempo recibo mi cotización?', 'Respondemos en minutos en horario de atención (lunes a viernes de 9:00 a 18:00 h y sábados de 9:00 a 13:00 h). Por WhatsApp la atención suele ser inmediata.' ),
		),
		'rastreo-de-envios' => array(
			array( '¿Cómo rastreo mi envío?', 'Ingresa tu número de guía en la página "Rastrea tu Envío" y un asesor te confirma el estado por WhatsApp: en tránsito, en aduana o entregado.' ),
			array( '¿Dónde encuentro mi número de guía?', 'Tu número de guía te lo entregamos al confirmar y despachar tu envío. Si no lo tienes a mano, escríbenos por WhatsApp y te ayudamos.' ),
			array( '¿Qué significa que mi envío está "en aduana"?', 'Significa que tu envío está en proceso de revisión o liberación aduanera en el país de destino. Es un paso normal; te orientamos si se requiere algún documento.' ),
		),
		'contacto' => array(
			array( '¿Cómo puedo contactar a Grenvíos?', 'Por WhatsApp o teléfono al {{contacto_telefono}}, por correo a info@grenvios.com, o visitándonos en {{contacto_direccion}}.' ),
			array( '¿Cuál es el horario de atención?', 'Atendemos de lunes a viernes de 9:00 a 18:00 h y los sábados de 9:00 a 13:00 h. Por WhatsApp respondemos de forma rápida dentro del horario de atención.' ),
			array( '¿Hacen recojo a domicilio en {{origen_ciudad}}?', 'Sí. Coordinamos el recojo de tu envío a domicilio en {{origen_ciudad}}; solo escríbenos para agendar la hora.' ),
		),
	);

	return isset( $faqs[ $slug ] ) ? $faqs[ $slug ] : array();
}

/* Render de la sección de FAQ de una página + schema FAQPage */
function grenvios_render_page_faqs( $slug ) {
	if ( $slug === 'preguntas-frecuentes' ) return false; // esa página ya es el FAQ global
	if ( empty( grenvios_page_faqs( $slug ) ) ) return false;
	// Preguntas: repeater editable por página (post-meta) o, si no se editó, las del tema.
	$items = function_exists( 'grenvios_repeater' ) ? grenvios_repeater( 'page_faq' ) : array();
	$faqs  = array();
	foreach ( (array) $items as $it ) {
		$q = isset( $it['q'] ) ? $it['q'] : ( isset( $it[0] ) ? $it[0] : '' );
		$a = isset( $it['a'] ) ? $it['a'] : ( isset( $it[1] ) ? $it[1] : '' );
		if ( trim( (string) $q ) === '' && trim( (string) $a ) === '' ) continue;
		$faqs[] = array( $q, $a );
	}
	/* Preguntas propias de la ficha de destino (inc/destinos-secciones.php): antes
	 * salían en un segundo bloque de FAQ; van en este, primero y sin repetir. */
	if ( ! empty( $GLOBALS['grenvios_dsec_faq_visibles'] ) ) {
		$clave  = function ( $q ) { return mb_strtolower( trim( wp_strip_all_tags( (string) $q ) ) ); };
		$vistas = array();
		foreach ( $faqs as $f ) $vistas[ $clave( $f[0] ) ] = true;
		$extra = array();
		foreach ( (array) $GLOBALS['grenvios_dsec_faq_visibles'] as $f ) {
			if ( isset( $vistas[ $clave( $f[0] ) ] ) ) continue;
			$vistas[ $clave( $f[0] ) ] = true;
			$extra[] = array( $f[0], wp_strip_all_tags( $f[1] ) );
		}
		$faqs = array_merge( $extra, $faqs );
	}
	if ( empty( $faqs ) ) return false;
	$pf_sub   = function_exists( 'grenvios_field' ) ? grenvios_field( 'pf_sub', 'Preguntas frecuentes' ) : 'Preguntas frecuentes';
	$pf_title = function_exists( 'grenvios_field' ) ? grenvios_field( 'pf_title', 'Resolvemos tus <span class="hl">dudas</span>' ) : 'Resolvemos tus <span class="hl">dudas</span>';
	$uri  = untrailingslashit( get_template_directory_uri() );
	$base = 'pf-' . sanitize_html_class( $slug );
	ob_start();
	?>
	<section class="grenvios-faq-section blog-section padding" id="preguntas-frecuentes">
		<div class="container">
			<div class="section-heading text-center">
				<h3 class="sub-heading is-border"><?php echo esc_html( $pf_sub ); ?><span class="sh-underline"><img class="sh-truck" src="<?php echo esc_url( $uri . '/assets/img/truck.svg' ); ?>" alt="truck"></span></h3>
				<h2><?php echo wp_kses_post( $pf_title ); ?></h2>
			</div>
			<div class="row">
				<div class="col-lg-10 mx-auto">
					<div class="accordion faq-accordion row-gap" id="<?php echo esc_attr( $base ); ?>">
						<?php $i = 0; foreach ( $faqs as $f ) : $i++; $show = ( $i === 1 ); ?>
							<div class="accordion-item">
								<h3 class="accordion-header" id="<?php echo esc_attr( $base . '-h' . $i ); ?>">
									<button class="accordion-button<?php echo $show ? '' : ' collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo esc_attr( $base . '-c' . $i ); ?>" aria-expanded="<?php echo $show ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $base . '-c' . $i ); ?>"><?php echo esc_html( $f[0] ); ?></button>
								</h3>
								<div id="<?php echo esc_attr( $base . '-c' . $i ); ?>" class="accordion-collapse collapse<?php echo $show ? ' show' : ''; ?>" aria-labelledby="<?php echo esc_attr( $base . '-h' . $i ); ?>" data-bs-parent="#<?php echo esc_attr( $base ); ?>">
									<div class="accordion-body"><p><?php echo esc_html( $f[1] ); ?></p></div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php
	echo grenvios_apply_media_overrides( ob_get_clean() );

	/* Filtro `grenvios_faq_schema_propio`: para que otra sección de la página se
	 * haga cargo del marcado. Dos bloques FAQPage en la misma URL es un error —
	 * Google puede descartar los dos—, y en las páginas de destino hay dos FAQ
	 * visibles: estas preguntas y las del país. El módulo de destinos devuelve
	 * false aquí y emite un único FAQPage con las dos tandas juntas. */
	if ( ! apply_filters( 'grenvios_faq_schema_propio', true, $slug ) ) return true;

	// Schema FAQPage de esta página
	$entities = array();
	foreach ( $faqs as $f ) {
		$entities[] = array(
			'@type'          => 'Question',
			'name'           => $f[0],
			'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $f[1] ),
		);
	}
	echo '<script type="application/ld+json">' . wp_json_encode( array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => $entities,
	), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
	return true;
}

add_action( 'wp_head', function () {
	if ( grenvios_current_slug() !== 'preguntas-frecuentes' ) return;
	$entities = array();
	foreach ( grenvios_faq_pairs() as $f ) {
		$entities[] = array(
			'@type'          => 'Question',
			'name'           => $f[0],
			'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $f[1] ),
		);
	}
	echo '<script type="application/ld+json">' . wp_json_encode( array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => $entities,
	), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}, 8 );

/* BreadcrumbList (schema) automático para páginas con partial y posts.
 * Los Destinos lo emiten dentro de logisko_breadcrumbs(), así que se excluyen. */
add_action( 'wp_head', function () {
	if ( is_front_page() ) return;
	$slug = grenvios_current_slug();
	$dest = grenvios_destinos();
	if ( isset( $dest[ $slug ] ) ) return; // ya lo emite el render del destino

	$home  = untrailingslashit( home_url() );
	$items = array( array( '@type' => 'ListItem', 'position' => 1, 'name' => grenvios_t( 'Inicio' ), 'item' => $home . '/' ) );
	$pos   = 2;

	if ( is_page() ) {
		$id = get_queried_object_id();
		foreach ( array_reverse( get_post_ancestors( $id ) ) as $aid ) {
			$items[] = array( '@type' => 'ListItem', 'position' => $pos++, 'name' => get_the_title( $aid ), 'item' => get_permalink( $aid ) );
		}
		$items[] = array( '@type' => 'ListItem', 'position' => $pos++, 'name' => get_the_title( $id ), 'item' => get_permalink( $id ) );
	} else {
		return;
	}

	echo '<script type="application/ld+json">' . wp_json_encode( array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => $items,
	), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}, 7 );

/* ─────────────────────────────────────────────────────────────
 * 8) Botón flotante de WhatsApp (todas las páginas)
 * ───────────────────────────────────────────────────────────── */
add_action( 'wp_footer', function () {
	$b  = grenvios_biz();
	$wa = 'https://wa.me/' . $b['wa_number'] . '?text=' . rawurlencode( 'Hola Grenvíos, quiero cotizar un envío internacional.' );
	echo '<a href="' . esc_url( $wa ) . '" class="grenvios-whatsapp" target="_blank" rel="noopener" aria-label="Escríbenos por WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>';
}, 20 );

/* ─────────────────────────────────────────────────────────────
 * 9) Render data-driven de una página de Destino
 * ───────────────────────────────────────────────────────────── */
function grenvios_render_destino( $slug, $args = array() ) {
	$dest = grenvios_destinos();
	if ( ! isset( $dest[ $slug ] ) ) return false;
	$d    = $dest[ $slug ];
	$home = grenvios_url_base();
	$uri  = untrailingslashit( get_template_directory_uri() );
	$wa   = 'https://wa.me/' . grenvios_biz()['wa_number'] . '?text=' . rawurlencode( 'Hola, quiero cotizar un envío a ' . $d['title'] . '.' );
	ob_start();
	?>
	<?php
	/* Hero a pantalla completa y «Nuestras soluciones»: inc/destinos-hero.php. */
	// `hero => false`: la portada de una ruta pone arriba el hero de la home.
	if ( ! isset( $args['hero'] ) || $args['hero'] ) grenvios_dhero_render( $slug, $d );
	grenvios_dhero_soluciones( $slug, $d );
	?>

	<section class="dest-section padding">
		<div class="container">
			<?php
			/* Datos rápidos en una sola franja y, debajo, «¿Qué puedes enviar?» en dos
			 * columnas: tarjetas numeradas a la izquierda; foto + aviso a la derecha. */
			$facts = array(
				array( 'fa-solid fa-plane-departure', grenvios_field( 'dst_fact_modos_k', 'Modalidad' ), grenvios_field( 'dst_modos', $d['modos'] ) ),
				array( 'fa-regular fa-clock', grenvios_field( 'dst_fact_tiempo_k', 'Tiempo de entrega' ), grenvios_field( 'dst_tiempo', $d['tiempo'] ) ),
				array( 'fa-solid fa-box-open', grenvios_field( 'dst_fact_entrega_k', 'Forma de entrega' ), grenvios_field( 'dst_entrega', $d['entrega'] ) ),
			);
			$envios = array(
				array( 'fa-regular fa-file-lines', grenvios_field( 'dst_enviar_li1', 'Documentos legales, títulos y trámites' ) ),
				array( 'fa-solid fa-box', grenvios_field( 'dst_enviar_li2', 'Paquetes, equipaje y compras personales' ) ),
				array( 'fa-solid fa-truck-ramp-box', grenvios_field( 'dst_enviar_li3', 'Carga comercial para tu negocio' ) ),
				array( 'fa-solid fa-location-crosshairs', grenvios_field( 'dst_enviar_li4', 'Seguimiento de tu envío por número de guía' ) ),
			);
			$env_img = trim( (string) grenvios_field( 'dst_enviar_img', '' ) );
			if ( $env_img === '' ) $env_img = function_exists( 'grenvios_ej_img' ) ? grenvios_ej_img( 'embalaje' ) : ( function_exists( 'grenvios_ui_img' ) ? grenvios_ui_img( 'embalaje' ) : '' );
			$pages = grenvios_pages();
			$serv_title = isset( $pages[ $d['servicio'] ] ) ? $pages[ $d['servicio'] ]['title'] : 'Envío internacional';
			?>
			<div class="dest-facts wow fade-in-bottom" data-wow-delay="100ms">
				<?php foreach ( $facts as $f ) : ?>
				<div class="dest-fact">
					<span class="dest-fact-ic"><i class="<?php echo esc_attr( $f[0] ); ?>" aria-hidden="true"></i></span>
					<span class="dest-fact-tx">
						<span class="dest-fact-k"><?php echo esc_html( $f[1] ); ?></span>
						<span class="dest-fact-v"><?php echo esc_html( $f[2] ); ?></span>
					</span>
				</div>
				<?php endforeach; ?>
			</div>

			<div class="dest-send-wrap">
				<div class="dest-send">
					<p class="dest-send-sub"><?php echo esc_html( grenvios_field( 'dst_enviar_sub', 'Qué puedes enviar' ) ); ?></p>
					<h2><?php echo esc_html( grenvios_field( 'dst_enviar_title', '¿Qué puedes enviar a ' . $d['title'] . '?' ) ); ?></h2>
					<p><?php echo esc_html( grenvios_field( 'dst_enviar_intro', 'Con Grenvíos puedes enviar documentos, paquetes, equipaje, compras y carga. Cada modalidad se adapta al peso, volumen y urgencia de tu envío.' ) ); ?></p>
					<div class="dest-send-grid">
						<?php foreach ( $envios as $i => $e ) : ?>
						<div class="dest-send-card wow fade-in-bottom" data-wow-delay="<?php echo 100 + ( $i % 2 ) * 110; ?>ms"><span class="dest-send-ic"><i class="<?php echo esc_attr( $e[0] ); ?>" aria-hidden="true"></i></span><p><?php echo esc_html( $e[1] ); ?></p></div>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="dest-send-lado">
					<?php if ( $env_img !== '' ) : ?>
					<figure class="dest-send-foto wow fade-in-right" data-wow-delay="150ms"><img src="<?php echo esc_url( $env_img ); ?>" alt="" loading="lazy" decoding="async"></figure>
					<?php endif; ?>
					<div class="dest-note">
						<div class="dest-note-ic"><i class="fa-solid fa-circle-info"></i></div>
						<div>
							<h2><?php echo esc_html( grenvios_field( 'dst_info_title', 'Información importante' ) ); ?></h2>
							<p><?php echo esc_html( grenvios_field( 'dst_restr', $d['restr'] ) ); ?></p>
							<p><?php echo wp_kses_post( grenvios_field( 'dst_info_links',
								'¿Listo para enviar a ' . esc_html( $d['title'] ) . '? Conoce los detalles de '
								. '<a href="/servicios/' . $d['servicio'] . '/">' . esc_html( $serv_title ) . ' a ' . esc_html( $d['title'] ) . '</a>, '
								. 'revisa nuestros <a href="/servicios/">servicios de envío internacional</a> '
								. 'o <a href="/cotizar/">solicita tu cotización</a> ahora mismo.' ) ); ?></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<?php
	/* Punto de enganche para las secciones por país: precio, plazos, restricciones, documentación y cobertura.
	 * Las pinta inc/destinos-secciones.php; si no hay dato, no hay sección. */
	do_action( 'grenvios_destino_tras_info', $slug, $d );
	?>
	<section class="dest-proc-section bg-grey padding">
		<div class="container">
			<div class="srv-head text-center">
				<h2><?php echo esc_html( grenvios_field( 'dst_como_title', '¿Cómo funciona?' ) ); ?></h2>
			</div>
			<div class="dest-proc">
				<div class="dest-proc-step"><span class="dest-proc-n">1</span><span class="dest-proc-ic"><i class="fa-solid fa-comments"></i></span><h3><?php echo esc_html( grenvios_field( 'dst_como_s1_t', 'Solicita tu envío' ) ); ?></h3><p><?php echo esc_html( grenvios_field( 'dst_como_s1_d', 'Por WhatsApp, web o llamada.' ) ); ?></p></div>
				<div class="dest-proc-step"><span class="dest-proc-n">2</span><span class="dest-proc-ic"><i class="fa-solid fa-box-open"></i></span><h3><?php echo esc_html( grenvios_field( 'dst_como_s2_t', 'Entrega tu envío' ) ); ?></h3><p><?php echo esc_html( grenvios_field( 'dst_como_s2_d', 'En nuestra oficina o solicitamos recojo.' ) ); ?></p></div>
				<div class="dest-proc-step"><span class="dest-proc-n">3</span><span class="dest-proc-ic"><i class="fa-solid fa-truck-fast"></i></span><h3><?php echo esc_html( grenvios_field( 'dst_como_s3_t', 'Transporte' ) ); ?></h3><p><?php echo esc_html( grenvios_field( 'dst_como_s3_d', 'Envío por vía aérea o terrestre según tu elección.' ) ); ?></p></div>
				<div class="dest-proc-step"><span class="dest-proc-n">4</span><span class="dest-proc-ic"><i class="fa-solid fa-house-circle-check"></i></span><h3><?php echo esc_html( grenvios_field( 'dst_como_s4_t', 'Entrega en destino' ) ); ?></h3><p><?php echo esc_html( grenvios_field( 'dst_como_s4_d', 'Recibes tu envío de forma segura.' ) ); ?></p></div>
			</div>
		</div>
	</section>

	<?php
	/* Punto de enganche para las secciones por país: comparativa de modalidades.
	 * Las pinta inc/destinos-secciones.php; si no hay dato, no hay sección. */
	do_action( 'grenvios_destino_tras_proceso', $slug, $d );
	?>
	<?php
	/* «Por qué elegirnos»: barras, imagen enmarcada y cuatro tarjetas numeradas.
	 * Lo pinta inc/destinos-secciones.php. */
	grenvios_dsec_why( $slug, $d );
	?>

	<?php
	/* Punto de enganche para las secciones por país: enlaces internos, guías del blog y preguntas frecuentes.
	 * Las pinta inc/destinos-secciones.php; si no hay dato, no hay sección. */
	do_action( 'grenvios_destino_antes_cta', $slug, $d );
	?>
	<section class="cta-section padding-bottom">
		<div class="container">
			<div class="row cta-wrapper gx-0 align-items-center">
				<div class="col-lg-7">
					<div class="section-heading white mb-0">
						<h2><?php echo wp_kses_post( grenvios_field( 'dst_cta_title', '¿Listo para enviar a ' . esc_html( $d['title'] ) . '?' ) ); ?></h2>
						<p><?php echo esc_html( grenvios_field( 'dst_cta_text', 'Cotiza tu envío en minutos y te asesoramos sin compromiso.' ) ); ?></p>
					</div>
				</div>
				<div class="col-lg-5">
					<div class="srv-cta-actions">
						<a href="<?php echo esc_url( $home . '/cotizar/' ); ?>" class="default-btn btn-light"><?php echo esc_html( grenvios_field( 'dst_btn_cotizar', 'Cotizar mi envío' ) ); ?></a>
						<a href="<?php echo esc_url( $wa ); ?>" target="_blank" rel="noopener" class="default-btn"><i class="fa-brands fa-whatsapp"></i> <?php echo esc_html( grenvios_field( 'dst_btn_wa', 'WhatsApp' ) ); ?></a>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php
	echo grenvios_apply_media_overrides( ob_get_clean() );
	return true;
}

/* Render del listado de Blog en una página normal con slug "blog"
 * (hero editable + filtro de categorías + tarjetas de entradas + paginación). */
function grenvios_render_blog() {
	$eyebrow = grenvios_field( 'blog_eyebrow', get_theme_mod( 'grenvios_blog_eyebrow', 'Blog' ) );
	$title   = grenvios_field( 'blog_title', get_theme_mod( 'grenvios_blog_title', 'Guías de envíos <span>internacionales</span>' ) );
	$bg      = grenvios_field( 'blog_img', get_theme_mod( 'grenvios_blog_bg', '' ) );
	logisko_page_banner( $eyebrow, $title, array(), $bg );

	$paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
	$q = new WP_Query( array(
		'post_type'           => 'post',
		'post_status'         => 'publish',
		'posts_per_page'      => 10,
		'paged'               => $paged,
		'ignore_sticky_posts' => true,
	) );
	$blog_url = get_permalink();
	$cats = get_categories( array( 'hide_empty' => true ) );
	$tpl = untrailingslashit( get_template_directory_uri() );
	?>
	<section class="blog-section padding">
		<div class="container">
			<?php if ( ! empty( $cats ) ) : ?>
				<div class="blog-filter">
					<a href="<?php echo esc_url( $blog_url ); ?>" class="blog-filter-btn is-active">Todas</a>
					<?php foreach ( $cats as $c ) : ?>
						<a href="<?php echo esc_url( get_category_link( $c->term_id ) ); ?>" class="blog-filter-btn"><?php echo esc_html( $c->name ); ?></a>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( $q->have_posts() ) : ?>
				<div class="row gy-4 blog-grid">
					<?php while ( $q->have_posts() ) : $q->the_post(); ?>
						<div class="col-lg-4 col-md-6">
							<article <?php post_class( 'blog-card' ); ?>>
								<div class="blog-card-thumb">
									<a href="<?php the_permalink(); ?>">
										<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'medium_large', array( 'alt' => esc_attr( get_the_title() ) ) ); } else { ?><img src="<?php echo esc_url( function_exists( 'grenvios_ej_por_tema' ) ? grenvios_ej_por_tema( get_the_title(), 'embalaje' ) : $tpl . '/assets/img/post-1.jpg' ); ?>" loading="lazy" alt="<?php echo esc_attr( get_the_title() ); ?>"><?php } ?>
									</a>
									<?php $pc = get_the_category(); if ( ! empty( $pc ) ) : ?>
										<a class="blog-card-cat" href="<?php echo esc_url( get_category_link( $pc[0]->term_id ) ); ?>"><?php echo esc_html( $pc[0]->name ); ?></a>
									<?php endif; ?>
								</div>
								<div class="blog-card-body">
									<div class="blog-card-meta">
										<span><i class="fa-regular fa-calendar"></i> <?php echo esc_html( get_the_date() ); ?></span>
										<span><i class="fa-regular fa-user"></i> <?php echo esc_html( get_the_author() ); ?></span>
									</div>
									<h3 class="blog-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
									<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
									<a href="<?php the_permalink(); ?>" class="read-more">Leer más <i class="fa-solid fa-arrow-right"></i></a>
								</div>
							</article>
						</div>
					<?php endwhile; ?>
				</div>
				<div class="logisko-pagination text-center mt-50">
					<?php
					$big = 999999999;
					echo paginate_links( array(
						'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
						'format'    => '?paged=%#%',
						'current'   => $paged,
						'total'     => $q->max_num_pages,
						'mid_size'  => 1,
						'prev_text' => '<i class="fa-solid fa-angle-left"></i>',
						'next_text' => '<i class="fa-solid fa-angle-right"></i>',
					) );
					?>
				</div>
			<?php else : ?>
				<div class="text-center"><p>No hay entradas para mostrar por ahora.</p></div>
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>
		</div>
	</section>
	<?php
	return true;
}

/* Render data-driven de la página de Preguntas Frecuentes */
function grenvios_render_faq() {
	$home = grenvios_url_base();
	$uri  = untrailingslashit( get_template_directory_uri() );
	$pf_sub   = function_exists( 'grenvios_field' ) ? grenvios_field( 'pf_sub', 'Resolvemos tus dudas' ) : 'Resolvemos tus dudas';
	$pf_title = function_exists( 'grenvios_field' ) ? grenvios_field( 'pf_title', 'Preguntas frecuentes sobre <span class="hl">envíos internacionales</span>' ) : 'Preguntas frecuentes sobre <span class="hl">envíos internacionales</span>';
	$faqs     = grenvios_faq_pairs();
	ob_start();
	?>
	<?php
	/* Mismo hero que el resto de páginas (inc/hero-paginas.php). */
	logisko_page_banner( $pf_sub, $pf_title, array(
		apply_filters( 'grenvios_miga_inicio', grenvios_t( 'Inicio' ) ) => $home . '/',
		grenvios_t( 'Preguntas Frecuentes' ) => '',
	) );
	?>

	<section class="grenvios-faq-section blog-section padding">
		<div class="container">
			<div class="row">
				<div class="col-lg-8 sm-padding">
					<div class="accordion faq-accordion row-gap" id="faq-accordion">
						<?php $i = 0; foreach ( $faqs as $f ) :
							$i++; $show = ( $i === 1 ); ?>
							<div class="accordion-item">
								<h2 class="accordion-header" id="heading<?php echo $i; ?>">
									<button class="accordion-button<?php echo $show ? '' : ' collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $i; ?>" aria-expanded="<?php echo $show ? 'true' : 'false'; ?>" aria-controls="collapse<?php echo $i; ?>"><?php echo esc_html( $f[0] ); ?></button>
								</h2>
								<div id="collapse<?php echo $i; ?>" class="accordion-collapse collapse<?php echo $show ? ' show' : ''; ?>" aria-labelledby="heading<?php echo $i; ?>" data-bs-parent="#faq-accordion">
									<div class="accordion-body"><p><?php echo esc_html( $f[1] ); ?></p></div>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="col-lg-4 sm-padding">
					<div class="sidebar-widget">
						<div class="grenvios-tracking">
							<h2>¿Aún tienes dudas?</h2>
							<p>Escríbenos por WhatsApp y te respondemos al instante.</p>
							<a href="https://wa.me/<?php echo esc_attr( grenvios_biz()['wa_number'] ); ?>" target="_blank" rel="noopener" class="default-btn mt-20"><i class="fa-brands fa-whatsapp"></i> Chatear ahora</a>
						</div>
					</div>
					<div class="sidebar-widget">
						<div class="widget-title"><h3>Enlaces útiles</h3></div>
						<ul class="category-list">
							<li><a href="<?php echo esc_url( $home . '/cotizar/' ); ?>">Cotizar un envío</a></li>
							<li><a href="<?php echo esc_url( $home . '/rastreo-de-envios/' ); ?>">Rastrea tu envío</a></li>
							<li><a href="<?php echo esc_url( $home . '/servicios/' ); ?>">Nuestros servicios</a></li>
							<li><a href="<?php echo esc_url( $home . '/destinos/' ); ?>">Destinos</a></li>
							<li><a href="<?php echo esc_url( $home . '/contacto/' ); ?>">Contacto</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php
	echo grenvios_apply_media_overrides( ob_get_clean() );
	return true;
}

/* ─────────────────────────────────────────────────────────────
 * 10) Auto-setup al activar: crea páginas con jerarquía + portada
 * ───────────────────────────────────────────────────────────── */
add_action( 'after_switch_theme', function () {
	$defs = grenvios_pages();
	$ids  = array();

	// 1ª pasada: páginas sin padre
	foreach ( $defs as $slug => $p ) {
		if ( $p['parent'] !== '' ) continue;
		$ids[ $slug ] = grenvios_ensure_page( $slug, $p['title'], 0 );
	}
	// 2ª pasada: hijas
	foreach ( $defs as $slug => $p ) {
		if ( $p['parent'] === '' ) continue;
		$parent_id = isset( $ids[ $p['parent'] ] ) ? $ids[ $p['parent'] ] : 0;
		$ids[ $slug ] = grenvios_ensure_page( $slug, $p['title'], $parent_id );
	}
	// Páginas de destino (hijas de "destinos")
	$destinos_id = isset( $ids['destinos'] ) ? $ids['destinos'] : 0;
	foreach ( grenvios_destinos() as $slug => $d ) {
		$ids[ $slug ] = grenvios_ensure_page( $slug, $d['title'], $destinos_id );
	}
	update_option( 'show_on_front', 'page' );
	if ( ! empty( $ids['home'] ) ) update_option( 'page_on_front', $ids['home'] );
	if ( ! empty( $ids['blog'] ) ) update_option( 'page_for_posts', $ids['blog'] );
	update_option( 'grenvios_blog_created', 1 );

	// Permalinks "bonitos" (necesarios para los slugs SEO)
	$structure = get_option( 'permalink_structure' );
	if ( empty( $structure ) ) update_option( 'permalink_structure', '/%postname%/' );

	// Vuelca el HTML de cada página a su contenido editable (editor de WordPress)
	grenvios_seed_editable_pages( $ids );

	// Menú principal
	grenvios_build_menu( $ids );

	flush_rewrite_rules();
} );

/* Asegura la página "Blog" como página de entradas, incluso al ACTUALIZAR el
 * tema (cuando no se dispara after_switch_theme). Idempotente y de una sola vez. */
add_action( 'admin_init', function () {
	// Repara una sola vez las reglas de reescritura (arregla /wp-json/ "bonito"
	// que rompe el guardado del editor con "Unexpected token '<'").
	if ( ! get_option( 'grenvios_flush_v3' ) ) {
		flush_rewrite_rules();
		update_option( 'grenvios_flush_v3', 1 );
	}
	// Páginas añadidas después de la instalación inicial: se crean una sola vez
	// (reactivar el tema no siempre ocurre en un sitio en producción).
	// Crea las páginas del registro que aún no existan (páginas añadidas en una
	// actualización del tema). La versión sube al registrar páginas nuevas.
	if ( get_option( 'grenvios_pages_v' ) !== GRENVIOS_PAGES_V && function_exists( 'grenvios_ensure_page' ) ) {
		$defs = grenvios_pages();
		$ids  = array();
		foreach ( $defs as $psl => $pdef ) {
			if ( $pdef['parent'] !== '' ) continue;
			$ids[ $psl ] = grenvios_ensure_page( $psl, $pdef['title'], 0 );
		}
		foreach ( $defs as $psl => $pdef ) {
			if ( $pdef['parent'] === '' ) continue;
			$par = isset( $ids[ $pdef['parent'] ] ) ? $ids[ $pdef['parent'] ] : 0;
			grenvios_ensure_page( $psl, $pdef['title'], $par );
		}
		update_option( 'grenvios_pages_v', GRENVIOS_PAGES_V );
		flush_rewrite_rules();
	}
	if ( get_option( 'grenvios_blog_created' ) ) return;
	if ( ! function_exists( 'grenvios_ensure_page' ) ) return;
	$bid = grenvios_ensure_page( 'blog', 'Blog', 0 );
	if ( $bid ) {
		if ( ! get_option( 'page_for_posts' ) ) update_option( 'page_for_posts', $bid );
		update_option( 'grenvios_blog_created', 1 );
	}
} );

/* Blog: 10 entradas por página en el listado, categorías, etiquetas y búsqueda. */
add_action( 'pre_get_posts', function ( $q ) {
	if ( is_admin() || ! $q->is_main_query() ) return;
	if ( $q->is_home() || $q->is_category() || $q->is_tag() || $q->is_archive() || $q->is_search() ) {
		$q->set( 'posts_per_page', 10 );
	}
} );

function grenvios_ensure_page( $slug, $title, $parent_id ) {
	$ex = get_page_by_path( $slug );
	/* Una hija (servicios/x, destinos/x) no aparece buscando «x» suelto: sin esto
	 * cada subida de GRENVIOS_PAGES_V creaba otra copia (x-2, x-3, x-4). */
	if ( ! $ex && $parent_id ) {
		$hit = get_posts( array(
			'post_type'        => 'page',
			'name'             => $slug,
			'post_parent'      => (int) $parent_id,
			'post_status'      => array( 'publish', 'draft', 'pending', 'private' ),
			'numberposts'      => 1,
			'suppress_filters' => true,
		) );
		if ( $hit ) $ex = $hit[0];
	}
	if ( $ex ) {
		if ( $parent_id && (int) $ex->post_parent !== (int) $parent_id ) {
			wp_update_post( array( 'ID' => $ex->ID, 'post_parent' => $parent_id ) );
		}
		return $ex->ID;
	}
	$pid = wp_insert_post( array(
		'post_title'  => $title,
		'post_name'   => $slug,
		'post_status' => 'publish',
		'post_type'   => 'page',
		'post_parent' => $parent_id,
	) );
	return ( $pid && ! is_wp_error( $pid ) ) ? $pid : 0;
}

function grenvios_build_menu( $ids ) {
	$menu_name = 'Principal';
	$menu = wp_get_nav_menu_object( $menu_name );
	if ( ! $menu ) {
		$menu_id = wp_create_nav_menu( $menu_name );
	} else {
		$menu_id = $menu->term_id;
		// limpiar items previos para reconstruir
		foreach ( wp_get_nav_menu_items( $menu_id ) as $it ) wp_delete_post( $it->ID, true );
	}

	$add = function ( $slug, $parent = 0 ) use ( $menu_id, $ids ) {
		if ( empty( $ids[ $slug ] ) ) return 0;
		return wp_update_nav_menu_item( $menu_id, 0, array(
			'menu-item-title'     => get_the_title( $ids[ $slug ] ),
			'menu-item-object'    => 'page',
			'menu-item-object-id' => $ids[ $slug ],
			'menu-item-type'      => 'post_type',
			'menu-item-status'    => 'publish',
			'menu-item-parent-id' => $parent,
		) );
	};

	$add( 'home' );
	$add( 'nosotros' );
	$serv = $add( 'servicios' );
	$add( 'envio-internacional-de-documentos', $serv );
	$add( 'envio-internacional-de-paquetes', $serv );
	$add( 'carga-internacional', $serv );
	$add( 'apostilla-y-traduccion', $serv );
	$dest = $add( 'destinos' );
	foreach ( array_keys( grenvios_destinos() ) as $cslug ) $add( $cslug, $dest );
	$add( 'peso-volumetrico', $serv );
	$add( 'envio-de-equipaje', $serv );
	$add( 'envio-de-compras', $serv );
	$add( 'que-se-puede-enviar' );
	$add( 'tiempos-de-entrega' );
	$add( 'como-enviar-un-paquete-al-extranjero' );
	$add( 'recojo-a-domicilio-lima' );
	$add( 'envios-para-empresas' );
	$add( 'cotizar' );
	$add( 'rastreo-de-envios' );
	$add( 'contacto' );

	$loc = get_theme_mod( 'nav_menu_locations', array() );
	$loc['primary'] = $menu_id;
	set_theme_mod( 'nav_menu_locations', $loc );
}
