<?php
/**
 * Grenvíos — Personalizador (Customizer)
 * Sidebar nativo de WordPress (Apariencia → Personalizar) para editar SIN tocar código:
 *   · Logos e identidad
 *   · Imágenes de contenido (equipo, testimonios, respaldos, banderas, ilustraciones)
 *   · FONDOS de secciones (los que viven en los CSS) — se sobrescriben con <style> en wp_head
 *   · Datos de contacto (teléfono, WhatsApp, correo, dirección, horario) — única fuente de verdad
 *
 * Cómo funciona el reemplazo de imágenes:
 *   Cada imagen del tema se referencia por su archivo en /assets/img/<archivo>. Si el cliente
 *   sube un reemplazo en el Customizer, en TODO render (partials, contenido editable de la
 *   página, destinos y FAQ data-driven) se cambia la URL del asset por la subida. Así una sola
 *   subida actualiza la imagen en todas las páginas donde aparece.
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ─────────────────────────────────────────────────────────────
 * Registro maestro de imágenes editables: archivo => [label, group]
 * group: logo | hero | fondos | contenido | patrones
 * ───────────────────────────────────────────────────────────── */
function grenvios_media_registry() {
	return array(
		// ── Logos e identidad ──
		'logo.svg'             => array( 'Logo principal (cabecera y pie de página)', 'logo' ),
		'favicon.png'          => array( 'Favicon (ícono de la pestaña del navegador)', 'logo' ),

		// ── Portada / Hero ──
		'hero-background.jpg'  => array( 'Fondo de la sección hero (portada)', 'hero' ),
		'hero-delivery-boy.png'=> array( 'Ilustración del repartidor (hero)', 'hero' ),
		'hero-home.jpg'        => array( 'Portada · Foto del carrusel (hero)', 'hero' ),
		'slider-bg.jpg'        => array( 'Imagen para compartir en redes (Open Graph)', 'hero' ),
		'slider-truck.png'     => array( 'Camión del slider', 'hero' ),
		'slider-container.png' => array( 'Contenedor del slider', 'hero' ),
		'slider-badge.png'     => array( 'Insignia / sello del slider', 'hero' ),

		// ── Fondos de secciones (CSS) ──
		'content-bg-3.jpg'     => array( 'Fondo: franja de llamada a la acción (foto bajo velo vino)', 'fondos' ),
		'content-bg-5.jpg'     => array( 'Fondo: sección de cotización', 'fondos' ),
		'content-bg-7.jpg'     => array( 'Fondo: servicios y CTA (varias secciones)', 'fondos' ),
		'page-banner.jpg'      => array( 'Fondo: banner superior de páginas internas', 'fondos' ),
		'footer-shape.png'     => array( 'Fondo decorativo del pie de página', 'fondos' ),

		// ── Imágenes de contenido ──
		'team-1.jpg'           => array( 'Equipo — foto 1', 'contenido' ),
		'team-2.jpg'           => array( 'Equipo — foto 2', 'contenido' ),
		'team-3.jpg'           => array( 'Equipo — foto 3', 'contenido' ),
		'team-4.jpg'           => array( 'Equipo — foto 4', 'contenido' ),
		'comment-1.jpg'        => array( 'Testimonio — avatar 1', 'contenido' ),
		'comment-2.jpg'        => array( 'Testimonio — avatar 2', 'contenido' ),
		'comment-3.jpg'        => array( 'Testimonio — avatar 3', 'contenido' ),
		'comment-4.jpg'        => array( 'Testimonio — avatar 4', 'contenido' ),
		'sponsor-01.png'       => array( 'Respaldo / aliado — logo 1', 'contenido' ),
		'sponsor-02.png'       => array( 'Respaldo / aliado — logo 2', 'contenido' ),
		'sponsor-03.png'       => array( 'Respaldo / aliado — logo 3', 'contenido' ),
		'sponsor-04.png'       => array( 'Respaldo / aliado — logo 4', 'contenido' ),
		'sponsor-05.png'       => array( 'Respaldo / aliado — logo 5', 'contenido' ),
		'sponsor-06.png'       => array( 'Respaldo / aliado — logo 6', 'contenido' ),
		'banner-add.jpg'       => array( 'Imagen de banner promocional', 'contenido' ),

		// ── Patrones e ilustraciones decorativas (avanzado) ──
		'texture.png'          => array( 'Textura del camino del slider', 'patrones' ),
		'wave-pattern.png'     => array( 'Patrón de ondas (varias secciones)', 'patrones' ),
		'map-pattern.png'      => array( 'Patrón de mapa (contadores / pestañas)', 'patrones' ),
		'truck-pattern.png'    => array( 'Patrón de camión (promos)', 'patrones' ),
		'ship-pattern.png'     => array( 'Patrón de barco (promos)', 'patrones' ),
		'plane-pattern.png'    => array( 'Patrón de avión (promos)', 'patrones' ),
		'line-pattern.png'     => array( 'Patrón de líneas (texto en movimiento)', 'patrones' ),
		'process-line.png'     => array( 'Línea del proceso', 'patrones' ),
		'corner-shape-red.png' => array( 'Forma de esquina (vino)', 'patrones' ),
		'corner-shape-blue.png'=> array( 'Forma de esquina (secundaria)', 'patrones' ),
		'forklift.png'         => array( 'Ilustración montacargas (CTA)', 'patrones' ),
		'truck-1.png'          => array( 'Camión animado 1', 'patrones' ),
		'truck-2.png'          => array( 'Camión animado 2', 'patrones' ),
		'truck-3.png'          => array( 'Camión animado 3', 'patrones' ),
		'truck.svg'            => array( 'Ícono de camión (subrayado de títulos)', 'patrones' ),
	);
}

/* Fondos CSS: archivo => selectores que lo usan (para sobrescribir con <style>). */
function grenvios_bg_selectors() {
	return array(
		'content-bg-3.jpg'      => array( '.cta-wrapper' ),
		'content-bg-5.jpg'      => array( '.quote-section' ),
		'content-bg-7.jpg'      => array( '.service-section .bg-half', '.project-section .bg-half.white', '.cta-2 .cta-wrapper', '.quote-section.quote-2' ),
		'hero-background.jpg'   => array( '.hero-section' ),
		'page-banner.jpg'       => array( '.page-header' ),
		'footer-shape.png'      => array( '.footer-wrapper' ),
		'texture.png'           => array( '.slider-road' ),
		'slider-container.png'  => array( '.container-img' ),
		'slider-truck.png'      => array( '.slider-truck' ),
		'slider-badge.png'      => array( '.slider-badge' ),
		'wave-pattern.png'      => array( '.founder-card', '.pricing-item:before', '.service-content' ),
		'map-pattern.png'       => array( '.counter-wrap', '.feature-tab .tab-inner', '.map-pattern' ),
		'truck-pattern.png'     => array( '.promo-item-wrapper .promo-item' ),
		'ship-pattern.png'      => array( '.promo-item-wrapper .col-lg-4:nth-child(2) .promo-item' ),
		'plane-pattern.png'     => array( '.promo-item-wrapper .col-lg-4:nth-child(3) .promo-item' ),
		'line-pattern.png'      => array( '.running-text' ),
		'process-line.png'      => array( '.process-wrapper' ),
		'corner-shape-red.png'  => array( '.branch-item:before', '.corner-shape' ),
		'corner-shape-blue.png' => array( '.branch-item:after' ),
		'forklift.png'          => array( '.cta-2 .cta-wrapper .cta-men' ),
		'truck-1.png'           => array( '.running-truck .truck' ),
		'truck-2.png'           => array( '.running-truck .truck-2' ),
		'truck-3.png'           => array( '.running-truck .truck-3' ),
	);
}

/* ID de setting del Customizer para un archivo de imagen. */
function grenvios_img_setting_id( $file ) {
	return 'grenvios_img_' . preg_replace( '/[^a-z0-9]+/', '_', strtolower( $file ) );
}

/* URL efectiva de una imagen: la subida en el Customizer o, por defecto, la del tema. */
function grenvios_img_url( $file ) {
	$custom = get_theme_mod( grenvios_img_setting_id( $file ), '' );
	if ( $custom ) return $custom;
	return get_template_directory_uri() . '/assets/img/' . $file;
}

/* ─────────────────────────────────────────────────────────────
 * Datos de contacto editables (única fuente de verdad → grenvios_biz())
 * ───────────────────────────────────────────────────────────── */
function grenvios_biz_defaults() {
	return array(
		'name'      => 'Grenvíos',
		'phone'     => '900 612 836',
		'phone_tel' => '+51900612836',
		'wa_number' => '51900612836',
		'email'     => 'info@grenvios.com',
		'address'   => 'Jr. Callao 220, Cercado de Lima',
		'city'      => 'Lima',
		'country'   => 'PE',
		'hours'     => 'Lunes a viernes de 9:00 a 18:00 h · Sábados de 9:00 a 13:00 h',
	);
}

/* Campos de contacto expuestos en el Customizer (key => label). */
function grenvios_biz_fields() {
	return array(
		'name'      => 'Nombre de la marca',
		'phone'     => 'Teléfono (mostrado)',
		'phone_tel' => 'Teléfono para llamar (ej. +51900612836)',
		'wa_number' => 'WhatsApp internacional sin signos (ej. 51900612836)',
		'email'     => 'Correo electrónico',
		'address'   => 'Dirección',
		'city'      => 'Ciudad de origen de los envíos',
		'hours'     => 'Horario de atención',
	);
}

/* ─────────────────────────────────────────────────────────────
 * Reemplazo de imágenes en cualquier HTML renderizado
 * ───────────────────────────────────────────────────────────── */
/* Mapa urlOriginal => urlPersonalizada para los slots que el cliente cambió. */
function grenvios_media_overrides() {
	static $map = null;
	if ( $map !== null ) return $map;
	$map  = array();
	$base = get_template_directory_uri() . '/assets/img/';
	foreach ( grenvios_media_registry() as $file => $meta ) {
		$custom = get_theme_mod( grenvios_img_setting_id( $file ), '' );
		if ( $custom ) $map[ $base . $file ] = $custom;
	}
	return $map;
}

/* Aplica TODOS los overrides de render a un bloque de HTML:
 *   1) tokens de texto {{clave}} → valor del Customizer (o texto por defecto)
 *   2) URLs de imágenes del tema → imágenes subidas en el Customizer
 * Se mantiene el nombre por compatibilidad con las llamadas existentes. */
function grenvios_apply_media_overrides( $html ) {
	if ( ! is_string( $html ) || $html === '' ) return $html;
	grenvios_perf_mark( 'media: inicio' );
	$html = grenvios_apply_text_tokens( $html );
	grenvios_perf_mark( 'media: tokens' );
	$map  = grenvios_media_overrides();
	if ( ! empty( $map ) ) {
		$html = str_replace( array_keys( $map ), array_values( $map ), $html );
	}
	/* Filtro `grenvios_html_final`: última pasada sobre CUALQUIER bloque que el
	 * tema pinte, lleve tokens o no. Se necesita aparte de `grenvios_text_html`
	 * porque aquel vive dentro de grenvios_apply_text_tokens(), que se salta el
	 * HTML sin «{{» y por tanto no ve las tablas ni las tarjetas ya montadas. */
	grenvios_perf_mark( 'media: overrides' );
	$html = apply_filters( 'grenvios_html_final', $html );
	grenvios_perf_mark( 'media: html_final' );
	return $html;
}

/* ─────────────────────────────────────────────────────────────
 * Override de FONDOS (CSS) vía <style> en el <head>
 * ───────────────────────────────────────────────────────────── */
add_action( 'wp_head', function () {
	$rules = array();
	foreach ( grenvios_bg_selectors() as $file => $selectors ) {
		$custom = get_theme_mod( grenvios_img_setting_id( $file ), '' );
		if ( ! $custom ) continue;
		$rules[] = implode( ',', $selectors ) . '{background-image:url(' . esc_url( $custom ) . ') !important}';
	}
	if ( $rules ) {
		echo "<style id=\"grenvios-bg-overrides\">\n" . implode( "\n", $rules ) . "\n</style>\n";
	}
}, 99 );

/* ─────────────────────────────────────────────────────────────
 * Registro del Customizer (panel + secciones + controles)
 * ───────────────────────────────────────────────────────────── */
add_action( 'customize_register', function ( $wp_customize ) {

	/* Panel contenedor */
	$wp_customize->add_panel( 'grenvios_panel', array(
		'title'       => 'Grenvíos — Imágenes y contenido',
		'description' => 'Cambia logos, imágenes, fondos y datos de contacto sin tocar código. Los textos largos de cada página se editan en Páginas → (la página) → Editar.',
		'priority'    => 10,
	) );

	/* ── Secciones de imágenes por grupo ── */
	$groups = array(
		'logo'      => array( 'Logos e identidad', 'Logo del sitio y favicon.' ),
		'hero'      => array( 'Portada / Hero', 'Imágenes y fondo de la sección principal de la portada.' ),
		'fondos'    => array( 'Fondos de secciones', 'Imágenes de fondo de las secciones (se aplican en todo el sitio).' ),
		'contenido' => array( 'Imágenes de contenido', 'Equipo, testimonios, respaldos, banderas y banners.' ),
		'patrones'  => array( 'Patrones e ilustraciones (avanzado)', 'Elementos decorativos. Cámbialos solo si sabes lo que haces.' ),
	);
	foreach ( $groups as $gkey => $g ) {
		$wp_customize->add_section( 'grenvios_sec_' . $gkey, array(
			'title'       => $g[0],
			'description' => $g[1],
			'panel'       => 'grenvios_panel',
		) );
	}

	/* Controles de imagen (uno por archivo) */
	foreach ( grenvios_media_registry() as $file => $meta ) {
		list( $label, $group ) = $meta;
		$sid = grenvios_img_setting_id( $file );
		$wp_customize->add_setting( $sid, array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, $sid, array(
			'label'       => $label,
			'description' => $file,
			'section'     => 'grenvios_sec_' . $group,
		) ) );
	}

	/* ── Sección: Datos de contacto ── */
	$wp_customize->add_section( 'grenvios_sec_contacto', array(
		'title'       => 'Datos de contacto',
		'description' => 'Teléfono, WhatsApp, correo, dirección y horario. Se usan en el pie de página, el botón de WhatsApp y los datos para Google (Schema).',
		'panel'       => 'grenvios_panel',
	) );
	$defaults = grenvios_biz_defaults();
	foreach ( grenvios_biz_fields() as $key => $label ) {
		$sid = 'grenvios_biz_' . $key;
		$wp_customize->add_setting( $sid, array(
			'default'           => $defaults[ $key ],
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( $sid, array(
			'label'   => $label,
			'section' => 'grenvios_sec_contacto',
			'type'    => 'text',
		) );
	}

	/* ── Sección: Blog (solo el hero es editable; las tarjetas salen de las entradas) ── */
	$wp_customize->add_section( 'grenvios_sec_blog', array(
		'title'       => 'Blog · Encabezado',
		'description' => 'Antetítulo y título del encabezado (hero) de la página del blog. Las tarjetas se generan automáticamente desde tus entradas (posts).',
		'panel'       => 'grenvios_panel',
	) );
	$wp_customize->add_setting( 'grenvios_blog_eyebrow', array(
		'default'           => 'Blog',
		'sanitize_callback' => 'sanitize_text_field',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'grenvios_blog_eyebrow', array(
		'label'   => 'Antetítulo',
		'section' => 'grenvios_sec_blog',
		'type'    => 'text',
	) );
	$wp_customize->add_setting( 'grenvios_blog_title', array(
		'default'           => 'Noticias y <span>novedades</span>',
		'sanitize_callback' => 'wp_kses_post',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( 'grenvios_blog_title', array(
		'label'       => 'Título (usa <span>…</span> para la palabra en color)',
		'section'     => 'grenvios_sec_blog',
		'type'        => 'text',
	) );
	$wp_customize->add_setting( 'grenvios_blog_bg', array(
		'default'           => '',
		'sanitize_callback' => 'esc_url_raw',
		'transport'         => 'refresh',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'grenvios_blog_bg', array(
		'label'       => 'Imagen de fondo del banner',
		'description' => 'Déjalo vacío para mantener el fondo del diseño.',
		'section'     => 'grenvios_sec_blog',
	) ) );
} );

/* ═════════════════════════════════════════════════════════════
 * TEXTOS DE PÁGINA — parametrizados por página → sección → campo
 * Las plantillas content-*.html usan tokens {{clave}} que aquí se
 * registran como controles del Customizer y se reemplazan en render.
 * Estructura del registro:
 *   slug => [ 'label', 'priority', 'sections' => [
 *       secKey => [ 'label', 'fields' => [
 *           clave => [ label, tipo(text|textarea|html), default ] ] ] ] ]
 * ═════════════════════════════════════════════════════════════ */
function grenvios_text_registry() {
	return apply_filters( 'grenvios_text_registry', array(
		'tiempos-de-entrega' => array(
			'label'    => 'Tiempos de Entrega',
			'priority' => 49,
			'sections' => array(
				'hero' => array(
					'label'  => 'Tiempos · Banner',
					'fields' => array(
						'tool_eyebrow' => array( 'Frase superior', 'text', 'Plazos reales' ),
						'tool_title' => array( 'Título H1', 'text', 'Tiempos de entrega de envíos internacionales, destino por destino' ),
					),
				),
				'intro' => array(
					'label'  => 'Tiempos · Introducción',
					'fields' => array(
						'te_h2' => array( 'Título', 'text', 'Los plazos dependen del destino y de la vía, no solo de la distancia' ),
						'te_intro' => array( 'Texto', 'textarea', 'Un envío aéreo a un país vecino puede llegar antes que uno terrestre a la ciudad de al lado, y la aduana puede sumar días que no dependen del transporte. Estos son los tiempos con los que trabajamos, contados en días hábiles desde el despacho en {{origen_ciudad}}.' ),
						'te_nota' => array( 'Nota bajo la tabla', 'textarea', 'Los plazos son estimados en días hábiles y no incluyen el tiempo que la aduana del país de destino pueda retener un envío para revisión. Si tu envío es urgente, dínoslo al cotizar: hay rutas más rápidas según el destino.' ),
					),
				),
				'vias' => array(
					'label'  => 'Tiempos · Aérea y terrestre',
					'fields' => array(
						'te_aereo_title' => array( 'Título · Aérea', 'text', 'Vía aérea' ),
						'te_aereo_text' => array( 'Texto · Aérea', 'textarea', 'Es la opción rápida y la única disponible para destinos fuera de Sudamérica. Tiene más restricciones de contenido: nada de líquidos, alimentos ni objetos con batería.' ),
						'te_terr_title' => array( 'Título · Terrestre', 'text', 'Vía terrestre' ),
						'te_terr_text' => array( 'Texto · Terrestre', 'textarea', 'Más económica y con menos restricciones —admite líquidos, alimentos sellados y objetos con batería—, pero solo llega a países de la región y tarda más.' ),
					),
				),
			),
		),
		'como-enviar-un-paquete-al-extranjero' => array(
			'label'    => 'Cómo Enviar un Paquete',
			'priority' => 50,
			'sections' => array(
				'hero' => array(
					'label'  => 'Cómo enviar · Banner',
					'fields' => array(
						'tool_eyebrow' => array( 'Frase superior', 'text', 'Paso a paso' ),
						'tool_title' => array( 'Título H1', 'text', 'Cómo enviar un paquete al extranjero' ),
					),
				),
				'intro' => array(
					'label'  => 'Cómo enviar · Introducción',
					'fields' => array(
						'ce_h2' => array( 'Título', 'text', 'Siete pasos, de la caja a la entrega' ),
						'ce_intro' => array( 'Texto', 'textarea', 'Enviar al extranjero desde {{origen_pais}} no tiene misterio, pero sí un orden. Si lo sigues, tu envío no se queda en aduana ni te cuesta más de lo previsto.' ),
						'ce_links_title' => array( 'Título de la sección final', 'text', 'Lo que necesitas saber antes de empezar' ),
					),
				),
			),
		),
		'recojo-a-domicilio-lima' => array(
			'label'    => 'Recojo a Domicilio',
			'priority' => 51,
			'sections' => array(
				'hero' => array(
					'label'  => 'Recojo · Banner',
					'fields' => array(
						'tool_eyebrow' => array( 'Frase superior', 'text', 'Lima y alrededores' ),
						'tool_title' => array( 'Título H1', 'text', 'Recojo de envíos a domicilio en {{origen_ciudad}}' ),
					),
				),
				'intro' => array(
					'label'  => 'Recojo · Introducción',
					'fields' => array(
						'rd_h2' => array( 'Título', 'text', 'No hace falta que vengas: lo recogemos donde estés' ),
						'rd_intro' => array( 'Texto', 'textarea', 'Recogemos tu paquete en casa, en tu oficina o directamente donde tu proveedor, dentro de {{origen_ciudad}}. Lo pesamos, lo medimos y lo despachamos al extranjero. Es la forma más cómoda de enviar si no puedes acercarte al Cercado.' ),
					),
				),
				'detalle' => array(
					'label'  => 'Recojo · Paneles',
					'fields' => array(
						'rd_como_title' => array( 'Título · Cómo se coordina', 'text', 'Cómo se coordina' ),
						'rd_costo' => array( 'Nota sobre el costo', 'textarea', 'El recojo tiene un costo que depende del distrito y se suma al del envío. Para clientes con envíos frecuentes fijamos días de recojo y una tarifa cerrada.' ),
						'rd_of_title' => array( 'Título · Oficina', 'text', 'O acércate a la oficina' ),
						'rd_prov' => array( 'Nota · Provincia', 'textarea', 'Si envías desde provincia, mándalo por una agencia de transporte local a nuestra sede en {{origen_ciudad}} y desde aquí lo despachamos a su destino internacional.' ),
					),
				),
			),
		),
		'envio-de-compras' => array(
			'label'    => 'Envío de Compras',
			'priority' => 52,
			'sections' => array(
				'hero' => array(
					'label'  => 'Compras · Banner',
					'fields' => array(
						'tool_eyebrow' => array( 'Frase superior', 'text', 'Compras en {{origen_pais}}' ),
						'tool_title' => array( 'Título H1', 'text', 'Envío de compras al extranjero desde {{origen_ciudad}}' ),
					),
				),
				'intro' => array(
					'label'  => 'Compras · Introducción',
					'fields' => array(
						'ec_h2' => array( 'Título', 'text', 'Compra en {{origen_pais}} aunque no estés aquí' ),
						'ec_intro' => array( 'Texto', 'textarea', 'Muchas tiendas peruanas no envían al extranjero. Compras, pones nuestra dirección de {{origen_ciudad}} como destino, y cuando llega lo despachamos a tu país. Si compras en varias tiendas, juntamos todo en un solo envío y pagas un solo flete.' ),
					),
				),
				'detalle' => array(
					'label'  => 'Compras · Paneles',
					'fields' => array(
						'ec_como_title' => array( 'Título · Cómo funciona', 'text', 'Cómo funciona' ),
						'ec_ojo_title' => array( 'Título · Lo que conviene saber', 'text', 'Lo que conviene saber' ),
					),
				),
			),
		),
		'peso-volumetrico' => array(
			'label'    => 'Peso Volumétrico',
			'priority' => 46,
			'sections' => array(
				'hero' => array(
					'label'  => 'Peso volumétrico · Banner',
					'fields' => array(
						'tool_eyebrow' => array( 'Frase superior', 'text', 'Cómo se cobra tu envío' ),
						'tool_title' => array( 'Título H1', 'text', 'Peso volumétrico' ),
					),
				),
				'intro' => array(
					'label'  => 'Peso volumétrico · Introducción',
					'fields' => array(
						'pv_h2' => array( 'Título', 'text', 'Se cobra el mayor entre el peso real y el peso volumétrico' ),
						'pv_intro' => array( 'Texto', 'textarea', 'Un paquete grande y liviano ocupa el mismo espacio en el avión que uno pequeño y pesado. Por eso, en todos los envíos internacionales el precio se calcula sobre el mayor de los dos pesos: el que marca la balanza y el que ocupa el paquete. Aquí puedes calcularlo tú mismo antes de cotizar.' ),
					),
				),
				'calc' => array(
					'label'  => 'Peso volumétrico · Calculadora',
					'fields' => array(
						'pv_calc_title' => array( 'Título', 'text', 'Calcula tu envío' ),
						'pv_calc_intro' => array( 'Texto', 'textarea', 'Mide la caja ya cerrada, por su parte más ancha, y en centímetros.' ),
						'pv_divisor' => array( 'Divisor de la fórmula', 'text', '5000' ),
						'pv_r_btn' => array( 'Texto del botón', 'text', 'Cotizar este envío' ),
						'pv_l_alto'  => array( 'Etiqueta · Alto', 'text', 'Alto (cm)' ),
						'pv_l_largo' => array( 'Etiqueta · Largo', 'text', 'Largo (cm)' ),
						'pv_l_ancho' => array( 'Etiqueta · Ancho', 'text', 'Ancho (cm)' ),
						'pv_l_peso'  => array( 'Etiqueta · Peso real', 'text', 'Peso real (kg)' ),
						'pv_r_vol'   => array( 'Resultado · Peso volumétrico', 'text', 'Peso volumétrico' ),
						'pv_r_real'  => array( 'Resultado · Peso real', 'text', 'Peso real' ),
						'pv_r_cobra' => array( 'Resultado · Se cobra por', 'text', 'Se cobra por' ),
					),
				),
				'formula' => array(
					'label'  => 'Peso volumétrico · Fórmula y ejemplo',
					'fields' => array(
						'pv_form_title' => array( 'Título', 'text', 'La fórmula' ),
						'pv_form_text' => array( 'Texto', 'textarea', 'Las medidas van en centímetros y el resultado sale en kilos. El divisor depende de la modalidad: en vía aérea se usa 5000 y en vía terrestre puede variar; te lo confirmamos al cotizar.' ),
						'pv_ej_title' => array( 'Título del ejemplo', 'text', 'Un ejemplo real' ),
						'pv_ej_text' => array( 'Ejemplo', 'textarea', 'Una caja de 40 × 30 × 30 cm con ropa pesa 5 kg en la balanza. Su peso volumétrico es 40 × 30 × 30 ÷ 5000 = 7,2 kg. Como 7,2 es mayor que 5, el envío se cobra por 7,2 kg.' ),
						'pv_ej_tip' => array( 'Consejo', 'textarea', 'Consejo: si la caja te queda holgada, reducirla unos centímetros baja el peso volumétrico y por tanto el precio. Es el ajuste más rentable antes de enviar.' ),
					),
				),
				'tabla' => array(
					'label'  => 'Peso volumétrico · Tabla de ejemplos',
					'fields' => array(
						'pv_tabla_title' => array( 'Título', 'text', 'Ejemplos de cajas frecuentes' ),
						'pv_tabla_intro' => array( 'Texto', 'textarea', 'Medidas habituales y el peso por el que terminarías pagando.' ),
					),
				),
			),
		),
		'que-se-puede-enviar' => array(
			'label'    => 'Qué se Puede Enviar',
			'priority' => 47,
			'sections' => array(
				'hero' => array(
					'label'  => 'Qué se puede enviar · Banner',
					'fields' => array(
						'tool_eyebrow' => array( 'Frase superior', 'text', 'Antes de enviar' ),
						'tool_title' => array( 'Título H1', 'text', 'Qué se puede enviar al extranjero' ),
					),
				),
				'intro' => array(
					'label'  => 'Qué se puede enviar · Introducción',
					'fields' => array(
						'qe_h2' => array( 'Título', 'text', 'Lo que puedes enviar depende de la vía y del país de destino' ),
						'qe_intro' => array( 'Texto', 'textarea', 'La mayoría de los envíos que se quedan retenidos no fallan por el transporte, sino por llevar algo que esa vía o esa aduana no admite. Esta es la lista con la que trabajamos; si tienes dudas con un artículo concreto, consúltanos antes de armar el paquete.' ),
					),
				),
				'paises' => array(
					'label'  => 'Qué se puede enviar · Tabla por país',
					'fields' => array(
						'qe_pais_title' => array( 'Título', 'text', 'Restricciones por país de destino' ),
						'qe_pais_intro' => array( 'Texto', 'textarea', 'Cada aduana tiene sus propias reglas y sus propios plazos. Estos son los países con página propia; entra en el tuyo para ver el detalle.' ),
					),
				),
			),
		),
		'envio-de-equipaje' => array(
			'label'    => 'Envío de Equipaje',
			'priority' => 48,
			'sections' => array(
				'hero' => array(
					'label'  => 'Equipaje · Banner',
					'fields' => array(
						'tool_eyebrow' => array( 'Frase superior', 'text', 'Equipaje y mudanza personal' ),
						'tool_title' => array( 'Título H1', 'text', 'Envío de equipaje al extranjero' ),
					),
				),
				'intro' => array(
					'label'  => 'Equipaje · Introducción',
					'fields' => array(
						'eq_h2' => array( 'Título', 'text', 'Manda tu equipaje por separado y viaja ligero' ),
						'eq_intro' => array( 'Texto', 'textarea', 'El exceso de equipaje en el avión suele costar más que enviar esa misma maleta por courier. Si te mudas, vuelves a tu país o compraste más de lo que cabe en tu vuelo, nosotros lo despachamos desde {{origen_ciudad}} y lo recibes en destino.' ),
					),
				),
				'detalle' => array(
					'label'  => 'Equipaje · Paneles',
					'fields' => array(
						'eq_casos_title' => array( 'Título · Para quién es', 'text', 'Para quién es' ),
						'eq_como_title' => array( 'Título · Cómo funciona', 'text', 'Cómo funciona' ),
						'eq_nota' => array( 'Nota aduanera', 'textarea', 'El equipaje personal usado suele tener un trato aduanero distinto al de la mercancía nueva. Te indicamos qué declarar en cada caso.' ),
						'eq_paises_title' => array( 'Título · Países', 'text', 'A dónde enviamos equipaje' ),
					),
				),
			),
		),
		'envios-para-empresas' => array(
			'label'    => 'Envíos para Empresas',
			'priority' => 45,
			'sections' => array(
				'hero' => array(
					'label'  => 'Empresas · Banner superior',
					'fields' => array(
						'emp_hero_subtitle'  => array( 'Frase superior', 'text', 'Soluciones corporativas' ),
						'emp_hero_title'     => array( 'Título H1', 'text', 'Envíos internacionales para empresas' ),
						'emp_hero_bc_inicio' => array( 'Miga de pan · Inicio', 'text', 'Inicio' ),
						'emp_hero_bc_current'=> array( 'Miga de pan · Página actual', 'text', 'Envíos para Empresas' ),
					),
				),
				'intro' => array(
					'label'  => 'Empresas · Introducción',
					'fields' => array(
						'emp_main_title' => array( 'Título', 'text', 'Tu operación de envíos, resuelta desde {{origen_ciudad}}' ),
						'emp_main_intro' => array( 'Texto', 'textarea', 'Si tu empresa envía muestras, repuestos, documentos o mercancía al extranjero de forma frecuente, no necesitas cotizar cada envío desde cero. Trabajamos con cuenta corporativa, tarifas por volumen y un asesor asignado que conoce tus rutas y tus tiempos.' ),
					),
				),
				'soluciones' => array(
					'label'  => 'Empresas · Qué resolvemos',
					'fields' => array(
						'emp_soluciones_title' => array( 'Título', 'text', 'Qué resolvemos para tu empresa' ),
						'emp_soluciones_intro' => array( 'Texto', 'textarea', 'Cuatro necesidades que se repiten en casi todas las empresas que exportan o envían al extranjero desde {{origen_pais}}.' ),
					),
				),
				'cuenta' => array(
					'label'  => 'Empresas · Cuenta corporativa',
					'fields' => array(
						'emp_cuenta_title' => array( 'Título', 'text', 'Cuenta corporativa' ),
						'emp_cuenta_intro' => array( 'Texto', 'textarea', 'Se abre con el RUC de la empresa y un contacto responsable. Desde ahí, cada envío se coordina sin volver a negociar precio.' ),
					),
				),
				'sectores' => array(
					'label'  => 'Empresas · Sectores que atendemos',
					'fields' => array(
						'emp_sectores_title' => array( 'Título', 'text', 'Sectores que atendemos' ),
						'emp_sectores_intro' => array( 'Texto', 'textarea', 'Cada sector tiene sus propias restricciones de aduana y sus propios plazos críticos. Estos son los que más movemos.' ),
					),
				),
				'pasos' => array(
					'label'  => 'Empresas · Cómo empezamos',
					'fields' => array(
						'emp_pasos_title' => array( 'Título', 'text', 'Cómo empezamos a trabajar juntos' ),
						'emp_pasos_intro' => array( 'Texto', 'textarea', 'Sin contratos largos ni volúmenes mínimos obligatorios: empezamos con tus envíos reales y ajustamos la tarifa con los datos del primer mes.' ),
					),
				),
				'docs' => array(
					'label'  => 'Empresas · Documentación de exportación',
					'fields' => array(
						'emp_docs_title' => array( 'Título', 'text', 'Documentación de exportación' ),
						'emp_docs_intro' => array( 'Texto', 'textarea', 'La mayoría de los envíos que se quedan retenidos en aduana no fallan por el transporte, sino por un documento mal emitido. Esto es lo que revisamos contigo antes de despachar.' ),
						'emp_docs_nota'  => array( 'Nota final', 'textarea', 'Cada país de destino tiene sus propios límites de valor y sus productos restringidos. Te confirmamos los del tuyo antes de que la mercancía salga de {{origen_ciudad}}.' ),
					),
				),
				'volumen' => array(
					'label'  => 'Empresas · Tarifas por volumen (CTA)',
					'fields' => array(
						'emp_volumen_title' => array( 'Título', 'text', 'Tarifas por volumen' ),
						'emp_volumen_intro' => array( 'Texto', 'textarea', 'El precio de un envío puntual no es el precio de cincuenta envíos al mes. Si tienes frecuencia, la tarifa cambia.' ),
						'emp_volumen_texto' => array( 'Texto secundario', 'textarea', 'Cuéntanos cuántos envíos haces al mes, a qué países y con qué peso promedio. Con esos tres datos te armamos una propuesta cerrada, sin compromiso.' ),
						'emp_volumen_btn'   => array( 'Texto del botón', 'text', 'Solicitar propuesta para mi empresa' ),
					),
				),
			),
		),
		'home' => array(
			'label'    => 'Inicio',
			'priority' => 10,
			'sections' => array(
				'hero' => array(
					'label'  => 'Inicio · Hero (carrusel principal)',
					'fields' => array(
						'home_sr_h1' => array( 'Título H1 (oculto, SEO)', 'text', 'Envíos internacionales desde {{origen_pais}}: paquetes, documentos y carga desde {{origen_ciudad}}' ),
						'home_hero_tagline' => array( 'Frase superior (común a las 3 diapositivas)', 'text', 'Tu camino confiable hacia el mundo' ),
						'home_hero1_title'  => array( 'Diapositiva 1 · Título', 'html', '¡Envíos internacionales de <br>paquetes y <span>carga!</span>' ),
						'home_hero1_text'   => array( 'Diapositiva 1 · Descripción', 'html', 'Llevamos tus envíos a más de 30 países en América, <br>Europa, Asia y África.' ),
						'home_hero1_btn'    => array( 'Diapositiva 1 · Texto del botón', 'text', 'Cotiza tu envío' ),
						'home_hero2_title'  => array( 'Diapositiva 2 · Título', 'html', '¡Documentos, paquetes y <br>carga al <span>mundo!</span>' ),
						'home_hero2_text'   => array( 'Diapositiva 2 · Descripción', 'html', 'Llevamos tus envíos a más de 30 países en América, <br>Europa, Asia y África.' ),
						'home_hero2_btn'    => array( 'Diapositiva 2 · Texto del botón', 'text', 'Cotiza tu envío' ),
						'home_hero3_title'  => array( 'Diapositiva 3 · Título', 'html', '¡También apostillamos <br>y <span>traducimos!</span>' ),
						'home_hero3_text'   => array( 'Diapositiva 3 · Descripción', 'html', 'No solo movemos tu documento: lo legalizamos para el mundo. <br>Apostilla y traducción profesional al inglés e italiano.' ),
						'home_hero3_btn'    => array( 'Diapositiva 3 · Texto del botón', 'text', 'Conoce el servicio' ),
						'home_hero1_img'    => array( 'Diapositiva 1 · Imagen', 'image', grenvios_img_url( 'hero-home.jpg' ) ),
						'home_hero2_img'    => array( 'Diapositiva 2 · Imagen', 'image', grenvios_img_url( 'hero-home.jpg' ) ),
						'home_hero3_img'    => array( 'Diapositiva 3 · Imagen', 'image', grenvios_img_url( 'hero-home.jpg' ) ),
					),
				),
				'heroform' => array(
					'label'  => 'Inicio · Cotizador del hero',
					'fields' => array(
						'home_hq_title'         => array( 'Título de la tarjeta (lo que va en <span>…</span> sale en color)', 'html', 'Cotiza tu <span>envío internacional</span>' ),
						'home_hq_sub'           => array( 'Subtítulo', 'text', 'Recibe una estimación rápida y sin compromiso.' ),
						'home_hq_l_origen'      => array( 'Origen · Etiqueta', 'text', 'Origen' ),
						'home_hq_ph_desde'      => array( 'Origen · Texto inicial del desplegable', 'text', 'Ciudad de origen' ),
						'home_hq_origen_valor'  => array( 'Origen · Opción preseleccionada', 'text', '{{origen_ciudad}}, {{origen_pais}}' ),
						'home_hq_l_destino'     => array( 'Destino · Etiqueta', 'text', 'Destino' ),
						'home_hq_ph_destino'    => array( 'Destino · Placeholder', 'text', 'País o ciudad' ),
						'home_hq_destino_valor' => array( 'Destino · Valor precargado (en las rutas de país se rellena solo)', 'text', '' ),
						'home_hq_l_tipo'        => array( 'Tipo de envío · Etiqueta', 'text', 'Tipo de envío' ),
						'home_hq_ph_tipo'       => array( 'Tipo de envío · Texto inicial', 'text', 'Documento, paquete o carga' ),
						'home_hq_l_peso'        => array( 'Peso · Etiqueta', 'text', 'Peso aproximado' ),
						'home_hq_ph_peso'       => array( 'Peso · Placeholder', 'text', 'kg' ),
						'home_hq_l_nombre'      => array( 'Nombre · Etiqueta', 'text', 'Nombre y apellido' ),
						'home_hq_ph_nombre'     => array( 'Nombre · Placeholder', 'text', 'Nombre y apellido' ),
						'home_hq_l_contacto'    => array( 'Contacto · Etiqueta', 'text', 'WhatsApp o email' ),
						'home_hq_ph_contacto'   => array( 'Contacto · Placeholder', 'text', 'WhatsApp o email' ),
						'home_hq_mas'           => array( 'Texto del desplegable de datos opcionales', 'text', 'Añadir más detalles del envío (opcional)' ),
						'home_hq_l_ciudad'      => array( 'Opcional · Ciudad de destino (etiqueta)', 'text', 'Ciudad de destino' ),
						'home_hq_ph_ciudad'     => array( 'Opcional · Ciudad de destino (placeholder)', 'text', 'Ciudad de entrega' ),
						'home_hq_l_postal'      => array( 'Opcional · Código postal (etiqueta)', 'text', 'Código postal' ),
						'home_hq_ph_postal'     => array( 'Opcional · Código postal (placeholder)', 'text', 'Código postal del destino' ),
						'home_hq_l_detalles'    => array( 'Opcional · Detalles (etiqueta)', 'text', 'Detalles del envío' ),
						'home_hq_ph_detalles'   => array( 'Opcional · Detalles (placeholder)', 'textarea', 'Medidas (largo × ancho × alto en cm) y contenido del envío. No se admiten líquidos, alimentos, medicinas, perfumes, dinero ni mascotas.' ),
						'home_hq_consent'       => array( 'Texto de la casilla de consentimiento', 'text', 'Acepto ser contactado para recibir mi cotización.' ),
						'home_hq_btn'           => array( 'Texto del botón', 'text', 'Solicitar cotización' ),
						'home_hq_note'          => array( 'Nota bajo el botón', 'text', 'Respuesta en menos de 24 horas hábiles' ),
						'home_hq_b1_t'          => array( 'Garantía 1 · Título', 'text', 'Envíos seguros' ),
						'home_hq_b1_d'          => array( 'Garantía 1 · Texto', 'text', 'Protegemos tu carga' ),
						'home_hq_b2_t'          => array( 'Garantía 2 · Título', 'text', 'Atención personalizada' ),
						'home_hq_b2_d'          => array( 'Garantía 2 · Texto', 'text', 'Soporte experto' ),
						'home_hq_b3_t'          => array( 'Garantía 3 · Título', 'text', 'Cobertura global' ),
						'home_hq_b3_d'          => array( 'Garantía 3 · Texto', 'text', 'Más de 30 destinos' ),
					),
				),
				'promo' => array(
					'label'  => 'Inicio · Promoción del mes (oculta por defecto)',
					'fields' => array(
						'home_promo_tag'   => array( 'Etiqueta', 'text', 'Promoción del mes' ),
						'home_promo_title' => array( 'Título', 'text', 'Título de la promoción' ),
						'home_promo_text'  => array( 'Texto', 'textarea', 'Describe aquí la promoción vigente (descuento, ruta, fecha límite…).' ),
						'home_promo_btn'   => array( 'Texto del botón', 'text', 'Aprovéchala ahora' ),
					),
				),
				'about' => array(
					'label'  => 'Inicio · Nosotros / Transporte confiable',
					'fields' => array(
						'home_about_imginfo'      => array( 'Etiqueta sobre la imagen', 'text', 'Desde {{origen_ciudad}} al mundo' ),
						'home_about_img1'         => array( 'Imagen 1', 'image', grenvios_img_url( 'content-bg-1.jpg' ) ),
						'home_about_img2'         => array( 'Imagen 2', 'image', grenvios_img_url( 'post-2.jpg' ) ),
						'home_about_img3'         => array( 'Imagen 3 (repartidor)', 'image', grenvios_img_url( 'delivery-man.png' ) ),
						'home_about_sub'          => array( 'Subtítulo', 'text', 'Quiénes somos' ),
						'home_about_title'        => array( 'Título', 'html', 'Envíos internacionales desde Perú, <span class="hl">de principio a fin</span>' ),
						'home_about_text'         => array( 'Texto principal', 'textarea', 'Somos un courier peruano especializado en envíos internacionales desde {{origen_pais}}: documentos, paquetes, equipaje y carga desde {{origen_ciudad}} a más de 30 destinos. Coordinamos transporte aéreo y terrestre, recojo a domicilio y entrega puerta a puerta, con revisión del contenido antes de despachar y seguimiento en cada etapa.' ),
						'home_about_promo1_title' => array( 'Ventaja 1 · Título', 'text', 'Rastreo con número de guía' ),
						'home_about_promo1_text'  => array( 'Ventaja 1 · Texto', 'textarea', 'Sigue tu envío paso a paso desde que sale hasta su entrega final.' ),
						'home_about_promo2_title' => array( 'Ventaja 2 · Título', 'text', 'Aéreo y terrestre' ),
						'home_about_promo2_text'  => array( 'Ventaja 2 · Texto', 'textarea', 'Elige la modalidad que mejor se adapte a tu envío y presupuesto.' ),
						'home_about_check1'       => array( 'Lista · Ítem 1', 'text', 'Recojo a domicilio en {{origen_ciudad}} y entrega puerta a puerta' ),
						'home_about_check2'       => array( 'Lista · Ítem 2', 'text', 'Embalaje seguro para documentos, paquetes y carga frágil' ),
						'home_about_check3'       => array( 'Lista · Ítem 3', 'text', 'Seguimiento de tus envíos con actualizaciones' ),
						'home_about_btn'          => array( 'Texto del botón', 'text', 'Conócenos' ),
						'home_about_call'         => array( 'Texto junto al teléfono', 'text', '¿Tienes dudas?' ),
					),
				),
				'services' => array(
					'label'  => 'Inicio · Servicios',
					'fields' => array(
						'home_serv_sub'    => array( 'Subtítulo', 'text', 'Nuestros servicios' ),
						'home_serv_title'  => array( 'Título', 'html', 'Qué enviamos al extranjero: <br><span class="hl">documentos, paquetes y carga</span>' ),
						'home_serv_text'   => array( 'Texto introductorio', 'html', 'Tres servicios con la misma revisión en origen: comprobamos contigo el contenido antes de embalar, <br>que es lo que evita las retenciones en la aduana de destino.' ),
						// Las tarjetas de servicios se editan como repeater (añadir/quitar) → sección "🗂️ Tarjetas de Servicios".
					),
				),
				'running' => array(
					'label'  => 'Inicio · Texto en movimiento',
					'fields' => array(
						'home_run1' => array( 'Frase 1', 'text', 'Envíos seguros' ),
						'home_run2' => array( 'Frase 2', 'text', 'Más de 30 destinos' ),
						'home_run3' => array( 'Frase 3', 'text', 'Aéreo y terrestre' ),
						'home_run4' => array( 'Frase 4', 'text', 'Cotiza en minutos' ),
					),
				),
				'destinos' => array(
					'label'  => 'Inicio · Destinos destacados',
					'fields' => array(
						'home_dest_sub'   => array( 'Subtítulo', 'text', 'Destinos destacados' ),
						'home_dest_title' => array( 'Título', 'html', 'Destinos más solicitados <br>desde <span class="hl">{{origen_ciudad}}</span>' ),
						'home_dest_text'  => array( 'Texto introductorio', 'html', 'Conoce algunos de los países a los que llevamos tus <br>documentos, paquetes y carga.' ),
						'home_dest_btn'   => array( 'Texto del botón', 'text', 'Ver todos los destinos' ),
						'home_dest_bg'    => array( 'Imagen de fondo de la sección', 'image', grenvios_img_url( 'content-bg-4.jpg' ) ),
						'home_dest1_img' => array( 'Destino 1 · Imagen', 'image', grenvios_img_url( 'post-1.jpg' ) ),
						'home_dest2_img' => array( 'Destino 2 · Imagen', 'image', grenvios_img_url( 'post-2.jpg' ) ),
						'home_dest3_img' => array( 'Destino 3 · Imagen', 'image', grenvios_img_url( 'post-3.jpg' ) ),
						'home_dest4_img' => array( 'Destino 4 · Imagen', 'image', grenvios_img_url( 'post-4.jpg' ) ),
						'home_dest5_img' => array( 'Destino 5 · Imagen', 'image', grenvios_img_url( 'post-5.jpg' ) ),
						'home_dest1_cat'  => array( 'Destino 1 · Categoría', 'text', 'Sudamérica' ),
						'home_dest1_name' => array( 'Destino 1 · Nombre', 'text', 'Ecuador' ),
						'home_dest2_cat'  => array( 'Destino 2 · Categoría', 'text', 'Sudamérica' ),
						'home_dest2_name' => array( 'Destino 2 · Nombre', 'text', 'Colombia' ),
						'home_dest3_cat'  => array( 'Destino 3 · Categoría', 'text', 'Sudamérica' ),
						'home_dest3_name' => array( 'Destino 3 · Nombre', 'text', 'Chile' ),
						'home_dest4_cat'  => array( 'Destino 4 · Categoría', 'text', 'Norteamérica' ),
						'home_dest4_name' => array( 'Destino 4 · Nombre', 'text', 'Estados Unidos' ),
						'home_dest5_cat'  => array( 'Destino 5 · Categoría', 'text', 'Europa' ),
						'home_dest5_name' => array( 'Destino 5 · Nombre', 'text', 'España' ),
					),
				),
				'counters' => array(
					'label'  => 'Inicio · Contadores',
					'fields' => array(
						'home_count1_label' => array( 'Contador 1 · Etiqueta', 'text', 'Países atendidos' ),
						'home_count2_label' => array( 'Contador 2 · Etiqueta', 'text', 'Días de tránsito mínimo' ),
						'home_count3_label' => array( 'Contador 3 · Etiqueta', 'text', 'Envíos entregados' ),
					),
				),
				'features' => array(
					'label'  => 'Inicio · Logística (pestañas)',
					'fields' => array(
						'home_feat_sub'          => array( 'Subtítulo', 'text', 'Tu envío en buenas manos' ),
						'home_feat_title'        => array( 'Título', 'html', 'Por qué enviar <br>con <span class="hl">Grenvíos</span>' ),
						'home_feat_text'         => array( 'Texto introductorio', 'html', 'Combinamos transporte aéreo y terrestre, embalaje seguro y <br>seguimiento en cada etapa para cada envío.' ),
						'home_feat_bg'           => array( 'Imagen de fondo de la sección', 'image', grenvios_img_url( 'content-bg-2.jpg' ) ),
						'home_feat_promo1_title' => array( 'Ventaja 1 · Título', 'text', 'Embalaje profesional' ),
						'home_feat_promo1_text'  => array( 'Ventaja 1 · Texto', 'html', 'Protegemos documentos, paquetes <br>y carga frágil antes de despachar.' ),
						'home_feat_promo2_title' => array( 'Ventaja 2 · Título', 'text', 'Cobertura global' ),
						'home_feat_promo2_text'  => array( 'Ventaja 2 · Texto', 'html', 'Más de 30 destinos en América, <br>Europa, Asia y África.' ),
						'home_feat_tab1_label'   => array( 'Pestaña 1 · Etiqueta', 'text', 'Seguimiento de tu envío' ),
						'home_feat_tab1_title'   => array( 'Pestaña 1 · Título', 'text', 'Seguimiento de tu envío' ),
						'home_feat_tab1_text'    => array( 'Pestaña 1 · Texto', 'textarea', 'Sigue cada etapa de tu envío con actualizaciones desde la recolección hasta la entrega.' ),
						'home_feat_tab1_li1'     => array( 'Pestaña 1 · Ítem 1', 'text', 'Seguimiento por número de guía' ),
						'home_feat_tab1_li2'     => array( 'Pestaña 1 · Ítem 2', 'text', 'Notificaciones en cada etapa' ),
						'home_feat_tab1_li3'     => array( 'Pestaña 1 · Ítem 3', 'text', 'Soporte por WhatsApp y correo' ),
						'home_feat_tab1_btn'     => array( 'Pestaña 1 · Botón', 'text', 'Rastrear envío' ),
						'home_feat_img1'         => array( 'Pestaña 1 · Imagen', 'image', grenvios_img_url( 'post-1.jpg' ) ),
						'home_feat_tab2_label'   => array( 'Pestaña 2 · Etiqueta', 'text', 'Operación global' ),
						'home_feat_tab2_title'   => array( 'Pestaña 2 · Título', 'text', 'Operación global' ),
						'home_feat_tab2_text'    => array( 'Pestaña 2 · Texto', 'textarea', 'Coordinamos rutas aéreas y terrestres hacia más de 30 países con aliados confiables.' ),
						'home_feat_tab2_li1'     => array( 'Pestaña 2 · Ítem 1', 'text', 'Cobertura en 4 continentes' ),
						'home_feat_tab2_li2'     => array( 'Pestaña 2 · Ítem 2', 'text', 'Asesoría aduanera por destino' ),
						'home_feat_tab2_li3'     => array( 'Pestaña 2 · Ítem 3', 'text', 'Embalaje seguro para carga frágil' ),
						'home_feat_tab2_btn'     => array( 'Pestaña 2 · Botón', 'text', 'Ver destinos' ),
						'home_feat_img2'         => array( 'Pestaña 2 · Imagen', 'image', grenvios_img_url( 'post-2.jpg' ) ),
					),
				),
				'process' => array(
					'label'  => 'Inicio · Proceso de envío',
					'fields' => array(
						'home_proc_sub'    => array( 'Subtítulo', 'text', 'Nuestro proceso de envío' ),
						'home_proc_title'  => array( 'Título', 'html', 'Cómo enviar tu paquete <br><span class="hl">en 4 pasos</span>' ),
						'home_proc_text'   => array( 'Texto introductorio', 'html', 'Del presupuesto a la entrega, sin trámites que tengas que resolver tú. <br>¿Es tu primer envío? Lee la <a href="/como-enviar-un-paquete-al-extranjero/">guía para enviar un paquete al extranjero</a>.' ),
						'home_proc1_title' => array( 'Paso 1 · Título', 'text', 'Cotiza' ),
						'home_proc1_text'  => array( 'Paso 1 · Texto', 'textarea', 'Solicita tu cotización con destino, peso y tipo de envío.' ),
						'home_proc2_title' => array( 'Paso 2 · Título', 'text', 'Embala y registra' ),
						'home_proc2_text'  => array( 'Paso 2 · Texto', 'textarea', 'Preparamos y embalamos tu envío y generamos tu guía.' ),
						'home_proc3_title' => array( 'Paso 3 · Título', 'text', 'En tránsito' ),
						'home_proc3_text'  => array( 'Paso 3 · Texto', 'textarea', 'Tu envío viaja por vía aérea o terrestre con seguimiento en cada etapa.' ),
						'home_proc4_title' => array( 'Paso 4 · Título', 'text', 'Entregado' ),
						'home_proc4_text'  => array( 'Paso 4 · Texto', 'textarea', 'Recibimos confirmación de entrega puerta a puerta en destino.' ),
					),
				),
				'cta' => array(
					'label'  => 'Inicio · Llamada a la acción',
					'fields' => array(
						'home_cta_sub'          => array( 'Subtítulo', 'text', 'Envíos seguros al mundo' ),
						'home_cta_title'        => array( 'Título', 'html', '¿Listo para enviar<br>al <span class="hl">mundo?</span>' ),
						'home_cta_text'         => array( 'Texto bajo el título', 'text', 'Cotiza tu envío en minutos y recíbelo donde estés.' ),
						'home_cta_btn'          => array( 'Texto del botón', 'text', 'Cotizar ahora' ),
						'home_cta_men'          => array( 'Imagen del repartidor (se apoya abajo a la derecha y sobresale por arriba)', 'image', grenvios_img_url( 'cta-repartidor-4.webp' ) ),
						'home_cta_img_alt'      => array( 'Imagen · texto alternativo', 'text', 'Repartidor de Grenvíos con una caja lista para enviar' ),
						'home_cta_promo1_title' => array( 'Tarjeta 1 · Título', 'text', 'Envío terrestre' ),
						'home_cta_promo1_text'  => array( 'Tarjeta 1 · Texto', 'textarea', 'Rutas confiables hacia países vecinos.' ),
						'home_cta_promo2_title' => array( 'Tarjeta 2 · Título', 'text', 'Envío aéreo' ),
						'home_cta_promo2_text'  => array( 'Tarjeta 2 · Texto', 'textarea', 'Rapidez para tus envíos a cualquier continente.' ),
						'home_cta_promo3_title' => array( 'Tarjeta 3 · Título', 'text', 'Recojo a domicilio' ),
						'home_cta_promo3_text'  => array( 'Tarjeta 3 · Texto', 'textarea', 'Recogemos tu envío donde estés en {{origen_ciudad}}.' ),
					),
				),
				'testimonials' => array(
					'label'  => 'Inicio · Testimonios',
					'fields' => array(
						'home_testi_sub'   => array( 'Subtítulo', 'text', 'Testimonios de clientes' ),
						'home_testi_title' => array( 'Título', 'html', '¡Opiniones de nuestros <span class="hl">clientes!</span>' ),
						'home_testi_text'  => array( 'Texto introductorio', 'html', 'La confianza de quienes ya enviaron con Grenvíos <br>hacia distintos países del mundo.' ),
						'home_testi_cargo' => array( 'Imagen del contenedor (ilustración)', 'image', grenvios_img_url( 'testimonial-bg.jpg' ) ),
						// Las reseñas se editan como repeater (añadir/quitar) → sección "⭐ Testimonios".
						'home_google_text'   => array( 'Texto de la insignia de Google', 'text', 'Opiniones verificadas en Google' ),
						'home_google_rating' => array( 'Google · Puntuación', 'text', '4.9' ),
						'home_google_count'  => array( 'Google · Nº de reseñas', 'text', '127' ),
						'home_google_url'    => array( 'Google · Enlace "Escribir reseña"', 'text', 'https://www.google.com/search?q=Grenv%C3%ADos+env%C3%ADos+internacionales+opiniones' ),
					),
				),
			),
		),
		'nosotros' => array(
			'label'    => 'Nosotros',
			'priority' => 20,
			'sections' => array(
				'operamos' => array(
					'label'  => 'Nosotros · Dónde operamos',
					'fields' => array(
						'nos_operamos_title' => array( 'Título', 'text', 'A dónde llegamos' ),
						'nos_operamos_text' => array( 'Texto', 'textarea', 'Trabajamos estas rutas de forma regular desde {{origen_ciudad}}. Entra en el país que te interesa y verás los plazos reales, las modalidades disponibles y qué admite su aduana.' ),
					),
				),
				'hero' => array(
					'label'  => 'Nosotros · Banner',
					'fields' => array(
						'nos_hero_eyebrow' => array( 'Etiqueta superior', 'text', 'Nosotros' ),
						'nos_hero_title' => array( 'Título', 'html', 'Grenvíos, tu <span>courier internacional en {{origen_ciudad}}</span>' ),
						'nos_hero_breadcrumb_home' => array( 'Breadcrumb inicio', 'text', 'Inicio' ),
						'nos_hero_breadcrumb_current' => array( 'Breadcrumb actual', 'text', 'Nosotros' ),
					),
				),
				'about' => array(
					'label'  => 'Nosotros · Presentación',
					'fields' => array(
						'nos_img_1' => array( 'Imagen 1', 'image', grenvios_img_url( 'content-bg-1.jpg' ) ),
						'nos_img_2' => array( 'Imagen 2', 'image', grenvios_img_url( 'post-2.jpg' ) ),
						'nos_img_3' => array( 'Imagen 3', 'image', grenvios_img_url( 'delivery-man.png' ) ),
						'nos_about_imginfo' => array( 'Texto sobre imagen', 'text', 'Desde {{origen_ciudad}} al mundo' ),
						'nos_about_subheading' => array( 'Subtítulo', 'text', 'Transporte internacional confiable' ),
						'nos_about_title' => array( 'Título', 'html', '¡Envíos internacionales seguros <br>desde <span class="hl">{{origen_ciudad}}, {{origen_pais}}!</span>' ),
						'nos_about_text' => array( 'Descripción', 'textarea', 'Somos una empresa peruana de envíos internacionales con sede en {{origen_ciudad}}. Conectamos personas y empresas con el mundo mediante servicios aéreos y terrestres seguros, rápidos y confiables, con cobertura a más de 30 países.' ),
						'nos_about_promo1_title' => array( 'Promo 1 · Título', 'text', 'Rapidez garantizada' ),
						'nos_about_promo1_text' => array( 'Promo 1 · Texto', 'textarea', 'Tiempos de tránsito optimizados por vía aérea y terrestre a tu destino.' ),
						'nos_about_promo2_title' => array( 'Promo 2 · Título', 'text', 'Atención personalizada' ),
						'nos_about_promo2_text' => array( 'Promo 2 · Texto', 'textarea', 'Te acompañamos en cada etapa del envío con asesoría cercana y clara.' ),
						'nos_about_check1' => array( 'Check 1', 'text', 'Seguridad en cada paquete que viaja con nosotros' ),
						'nos_about_check2' => array( 'Check 2', 'text', 'Cobertura a más de 30 países en todo el mundo' ),
						'nos_about_check3' => array( 'Check 3', 'text', 'Transporte internacional confiable, aéreo y terrestre' ),
						'nos_about_btn' => array( 'Botón cotizar', 'text', 'Cotizar envío' ),
						'nos_about_callinfo' => array( 'Info de llamada', 'html', '¿Tienes preguntas? <span><a href="tel:{{contacto_tel}}">{{contacto_telefono}}</a></span>' ),
					),
				),
				'historia' => array(
					'label'  => 'Nosotros · Historia, Misión y Visión',
					'fields' => array(
						'nos_historia_sub' => array( 'Antetítulo', 'text', 'Quiénes somos' ),
						'nos_historia_title' => array( 'Título', 'text', 'Nuestra historia' ),
						'nos_historia_text' => array( 'Texto historia', 'html', 'Grenvíos nació con una misión clara: conectar personas, negocios y sueños sin importar las distancias. Nos especializamos en el transporte internacional de <a href="HOMEURL/servicios/envio-internacional-de-paquetes/">paquetes</a>, <a href="HOMEURL/servicios/envio-internacional-de-documentos/">documentos</a> y <a href="HOMEURL/servicios/carga-internacional/">carga</a>, uniendo de manera eficiente a toda América, Europa y Asia con cobertura a más de 30 <a href="HOMEURL/destinos/">destinos</a>. Para ofrecerte una solución verdaderamente integral, no solo movemos tus documentos, sino que los legalizamos para el mundo: gestionamos trámites de <a href="HOMEURL/servicios/apostilla-y-traduccion/">apostilla y traducción</a> profesional al inglés e italiano. En Grenvíos eliminamos cualquier barrera logística y burocrática para que tu mundo llegue más lejos.' ),
						'nos_historia_mision_title' => array( 'Misión · Título', 'text', 'Misión' ),
						'nos_historia_mision_text' => array( 'Misión · Texto', 'textarea', 'Facilitar la conexión global de personas y empresas a través de soluciones integrales en transporte internacional aéreo y terrestre de carga, paquetes y documentos hacia América, Europa y Asia, complementado con servicios especializados de traducción y gestión legal. Nos comprometemos a derribar barreras logísticas y burocráticas, garantizando seguridad, puntualidad y confianza en cada entrega.' ),
						'nos_historia_vision_title' => array( 'Visión · Título', 'text', 'Visión' ),
						'nos_historia_vision_text' => array( 'Visión · Texto', 'textarea', 'Ser reconocidos como el aliado logístico y de gestión internacional líder en el mercado, expandiendo continuamente nuestra red global de conexiones entre América, Europa y Asia. Nos proyectamos como una empresa referente en innovación y excelencia operativa, capaz de resolver de manera integral el transporte, la legalización y la traducción de documentos, haciendo que el mundo sea cada vez más accesible para nuestros clientes.' ),
					),
				),
				'valores' => array(
					'label'  => 'Nosotros · Valores',
					'fields' => array(
						'nos_valores_subheading' => array( 'Subtítulo', 'text', 'Nuestros valores' ),
						'nos_valores_title' => array( 'Título', 'html', 'Lo que nos mueve en cada <span class="hl">envío</span>' ),
						'nos_valores_card1_title' => array( 'Valor 1 · Título', 'text', 'Confianza y seguridad' ),
						'nos_valores_card1_text' => array( 'Valor 1 · Texto', 'textarea', 'Trabajamos con los más altos estándares para que cada paquete, carga o documento llegue a su destino intacto y seguro.' ),
						'nos_valores_card2_title' => array( 'Valor 2 · Título', 'text', 'Eficiencia y puntualidad' ),
						'nos_valores_card2_text' => array( 'Valor 2 · Texto', 'textarea', 'Optimizamos nuestros procesos logísticos y de gestión para garantizar entregas y trámites en los plazos acordados.' ),
						'nos_valores_card3_title' => array( 'Valor 3 · Título', 'text', 'Integralidad' ),
						'nos_valores_card3_text' => array( 'Valor 3 · Texto', 'textarea', 'Desde el transporte hasta la traducción y el apostillado, resolvemos todas tus necesidades internacionales en un solo lugar.' ),
						'nos_valores_card4_title' => array( 'Valor 4 · Título', 'text', 'Precisión y profesionalismo' ),
						'nos_valores_card4_text' => array( 'Valor 4 · Texto', 'textarea', 'En traducciones y legalización cada detalle cuenta: nos comprometemos con la exactitud y la excelencia en cada trámite.' ),
						'nos_valores_card5_title' => array( 'Valor 5 · Título', 'text', 'Conectividad sin fronteras' ),
						'nos_valores_card5_text' => array( 'Valor 5 · Texto', 'textarea', 'Trabajamos con mentalidad global para unir culturas, mercados y personas en América, Europa y Asia.' ),
						'nos_valores_card6_title' => array( 'Valor 6 · Título', 'text', 'Compromiso con el cliente' ),
						'nos_valores_card6_text' => array( 'Valor 6 · Texto', 'textarea', 'Escuchamos tus necesidades y nos adaptamos para brindar un servicio humano, cercano y personalizado.' ),
					),
				),
				'certificaciones' => array(
					'label'  => 'Nosotros · Respaldo institucional',
					'fields' => array(
						'nos_cert_subheading' => array( 'Subtítulo', 'text', 'Respaldo institucional' ),
						'nos_cert_title' => array( 'Título', 'html', 'Trabajamos con el respaldo de <span class="hl">entidades oficiales</span>' ),
						'nos_cert_text' => array( 'Descripción', 'textarea', 'Nuestros trámites de legalización, traducción y transporte se gestionan ante instituciones reconocidas.' ),
						'nos_cert_item1' => array( 'Entidad 1', 'text', 'Colegio de Traductores de {{origen_pais}}' ),
						'nos_cert_item2' => array( 'Entidad 2', 'text', 'Ministerio de Relaciones Exteriores (MRE)' ),
						'nos_cert_item3' => array( 'Entidad 3', 'text', 'Cámara de Comercio' ),
						'nos_cert_item4' => array( 'Entidad 4', 'text', 'Ministerio de Educación' ),
						'nos_cert_item5' => array( 'Entidad 5', 'text', 'Ministerio de Transportes y Comunicaciones' ),
					),
				),
				'porque' => array(
					'label'  => 'Nosotros · ¿Por qué elegirnos?',
					'fields' => array(
						'nos_porque_subheading' => array( 'Subtítulo', 'text', '¿Por qué elegir Grenvíos?' ),
						'nos_porque_title' => array( 'Título', 'html', 'Lo que nos hace <br><span class="hl">diferentes</span>' ),
						'nos_porque_text' => array( 'Descripción', 'html', 'Más que un courier: una empresa peruana que combina cobertura aérea y terrestre <br>para ofrecerte un transporte internacional confiable.' ),
						'nos_porque_promo1_title' => array( 'Diferencial 1 · Título', 'text', 'Vía terrestre a países vecinos' ),
						'nos_porque_promo1_text' => array( 'Diferencial 1 · Texto', 'textarea', 'Una alternativa más económica que los grandes couriers para enviar a Ecuador, Colombia y Chile.' ),
						'nos_porque_promo2_title' => array( 'Diferencial 2 · Título', 'text', 'Recojo a domicilio en {{origen_ciudad}}' ),
						'nos_porque_promo2_text' => array( 'Diferencial 2 · Texto', 'textarea', 'Pasamos por tu paquete donde estés en {{origen_ciudad}} para que tú no tengas que moverte.' ),
						'nos_porque_promo3_title' => array( 'Diferencial 3 · Título', 'text', 'Seguimiento de tu envío' ),
						'nos_porque_promo3_text' => array( 'Diferencial 3 · Texto', 'textarea', 'Sigue tu envío en cada etapa con seguimiento por número de guía con apoyo de un asesor.' ),
						'nos_porque_promo4_title' => array( 'Diferencial 4 · Título', 'text', 'Asesoría en aduanas y embalaje' ),
						'nos_porque_promo4_text' => array( 'Diferencial 4 · Texto', 'textarea', 'Te orientamos en la documentación y el embalaje correcto para evitar demoras.' ),
					),
				),
				'running' => array(
					'label'  => 'Nosotros · Texto en movimiento',
					'fields' => array(
						'nos_running_item1' => array( 'Ítem 1', 'text', 'Envíos internacionales desde {{origen_ciudad}}' ),
						'nos_running_item2' => array( 'Ítem 2', 'text', 'Transporte internacional confiable' ),
						'nos_running_item3' => array( 'Ítem 3', 'text', 'Cobertura a más de 30 países' ),
						'nos_running_item4' => array( 'Ítem 4', 'text', 'Aéreo y terrestre, seguro y rápido' ),
					),
				),
				'counter' => array(
					'label'  => 'Nosotros · Contadores',
					'fields' => array(
						'nos_counter_item1_count' => array( 'Contador 1 · Número', 'text', '30' ),
						'nos_counter_item1_label' => array( 'Contador 1 · Etiqueta', 'text', 'Países atendidos' ),
						'nos_counter_item2_count' => array( 'Contador 2 · Número', 'text', '10' ),
						'nos_counter_item2_label' => array( 'Contador 2 · Etiqueta', 'text', 'Años de experiencia' ),
						'nos_counter_item3_count' => array( 'Contador 3 · Número', 'text', '5000' ),
						'nos_counter_item3_label' => array( 'Contador 3 · Etiqueta', 'text', 'Clientes satisfechos' ),
					),
				),
				'cta' => array(
					'label'  => 'Nosotros · Llamado a la acción',
					'fields' => array(
						'nos_cta_subheading' => array( 'Subtítulo', 'text', '¿Listo para enviar?' ),
						'nos_cta_title' => array( 'Título', 'html', '¡Tu paquete al mundo<br>con total <span class="hl">confianza!</span>' ),
						'nos_cta_btn_whatsapp' => array( 'Botón WhatsApp', 'text', 'WhatsApp' ),
							'nos_cta_men' => array( 'Imagen del repartidor (ilustración)', 'image', grenvios_img_url( 'delivery-men-2.png' ) ),
					),
				),
			),
		),
		'servicios' => array(
			'label'    => 'Servicios',
			'priority' => 30,
			'sections' => array(
				'hero' => array(
					'label'  => 'Servicios · Banner',
					'fields' => array(
						'serv_hero_eyebrow' => array( 'Antetítulo', 'text', 'Servicios' ),
						'serv_hero_title' => array( 'Título', 'html', 'Servicios de <span>envío internacional</span>' ),
						'serv_hero_breadcrumb_home' => array( 'Breadcrumb · Inicio', 'text', 'Inicio' ),
						'serv_hero_breadcrumb_current' => array( 'Breadcrumb · Actual', 'text', 'Servicios' ),
					),
				),
				'services' => array(
					'label'  => 'Servicios · Listado',
					'fields' => array(
						'serv_services_eyebrow' => array( 'Antetítulo', 'text', 'Servicios de envío internacional' ),
						'serv_services_title' => array( 'Título', 'html', '¡Enviamos tus documentos, paquetes <br>y carga al <span class="hl">mundo!</span>' ),
						'serv_services_subtitle' => array( 'Subtítulo', 'html', 'Desde {{origen_ciudad}} ofrecemos soluciones de envío internacional aéreo y terrestre <br>para personas y empresas, con cobertura a más de 30 países.' ),
						'serv_img_1' => array( 'Imagen tarjeta 1', 'image', grenvios_img_url( 'post-1.jpg' ) ),
						'serv_card1_title' => array( 'Tarjeta 1 · Título', 'text', 'Envío de Documentos' ),
						'serv_card1_text' => array( 'Tarjeta 1 · Texto', 'textarea', 'Documentos legales, títulos, DNI y pasaportes con servicio puerta a puerta. Entrega en 4 a 7 días hábiles según destino.' ),
						'serv_card1_link' => array( 'Tarjeta 1 · Enlace', 'text', 'Leer más' ),
						'serv_img_2' => array( 'Imagen tarjeta 2', 'image', grenvios_img_url( 'post-2.jpg' ) ),
						'serv_card2_title' => array( 'Tarjeta 2 · Título', 'text', 'Envío de Paquetes' ),
						'serv_card2_text' => array( 'Tarjeta 2 · Texto', 'textarea', 'Paquetes, equipaje y compras por vía aérea y terrestre. El cobro se calcula por peso o por volumen, según convenga.' ),
						'serv_card2_link' => array( 'Tarjeta 2 · Enlace', 'text', 'Leer más' ),
						'serv_img_3' => array( 'Imagen tarjeta 3', 'image', grenvios_img_url( 'post-3.jpg' ) ),
						'serv_card3_title' => array( 'Tarjeta 3 · Título', 'text', 'Carga Internacional' ),
						'serv_card3_text' => array( 'Tarjeta 3 · Texto', 'textarea', 'Desde 20 kg hasta grandes volúmenes. Transporte aéreo y terrestre pensado para empresas y envíos comerciales.' ),
						'serv_card3_link' => array( 'Tarjeta 3 · Enlace', 'text', 'Leer más' ),
						'serv_img_4' => array( 'Imagen tarjeta 4', 'image', grenvios_img_url( 'post-4.jpg' ) ),
						'serv_card4_title' => array( 'Tarjeta 4 · Título', 'text', 'Apostilla y Traducción' ),
						'serv_card4_text' => array( 'Tarjeta 4 · Texto', 'textarea', 'Legalizamos y traducimos tus documentos para que sean válidos en el extranjero. Apostilla, traducción profesional y envío en un solo lugar.' ),
						'serv_card4_link' => array( 'Tarjeta 4 · Enlace', 'text', 'Leer más' ),
					),
				),
				'destinos' => array(
					'label'  => 'Servicios · Destinos',
					'fields' => array(
						'serv_destinos_eyebrow' => array( 'Antetítulo', 'text', 'Destinos que cubrimos' ),
						'serv_destinos_title' => array( 'Título', 'html', '¡Enviamos a más de <br>30 <span class="hl">países!</span>' ),
						'serv_destinos_subtitle' => array( 'Subtítulo', 'textarea', 'Llevamos tus envíos a Ecuador, Colombia, Chile, Estados Unidos, España y muchos destinos más, combinando rutas aéreas y terrestres.' ),
						'serv_destinos_item1' => array( 'Ítem 1', 'text', 'Sudamérica: Ecuador, Colombia, Chile, Bolivia y más' ),
						'serv_destinos_item2' => array( 'Ítem 2', 'text', 'Norteamérica: Estados Unidos y Canadá' ),
						'serv_destinos_item3' => array( 'Ítem 3', 'text', 'Europa: España, Italia y otros destinos' ),
						'serv_destinos_btn' => array( 'Botón', 'text', 'Ver destinos' ),
						/* Por defecto, las dos fotos reales del tema (avión en pista y camión
						 * con la marca): la de la plantilla era un relleno «1100X1000». */
						'serv_img_5' => array( 'Imagen principal', 'image', grenvios_img_url( 'hero-home.jpg' ) ),
						'serv_img_6' => array( 'Imagen secundaria', 'image', grenvios_img_url( 'destino-hero.jpg' ) ),
					),
				),
				'cta' => array(
					'label'  => 'Servicios · CTA',
					'fields' => array(
						'serv_cta_eyebrow' => array( 'Antetítulo', 'text', '¿Listo para enviar?' ),
						'serv_cta_title' => array( 'Título', 'html', '¡Cotiza tu envío<br>internacional <span class="hl">hoy!</span>' ),
						'serv_cta_btn_primary' => array( 'Botón cotizar', 'text', 'Cotizar envío' ),
						'serv_cta_btn_whatsapp' => array( 'Botón WhatsApp', 'text', 'WhatsApp' ),
							'serv_cta_men' => array( 'Imagen del repartidor (ilustración)', 'image', grenvios_img_url( 'delivery-men-2.png' ) ),
						'serv_promo1_title' => array( 'Promo 1 · Título', 'text', 'Envío de Documentos' ),
						'serv_promo1_text' => array( 'Promo 1 · Texto', 'text', 'Servicio puerta a puerta en 4 a 7 días hábiles.' ),
						'serv_promo2_title' => array( 'Promo 2 · Título', 'text', 'Envío de Paquetes' ),
						'serv_promo2_text' => array( 'Promo 2 · Texto', 'text', 'Aéreo y terrestre, cobro por peso o volumen.' ),
						'serv_promo3_title' => array( 'Promo 3 · Título', 'text', 'Carga Internacional' ),
						'serv_promo3_text' => array( 'Promo 3 · Texto', 'text', 'Desde 20 kg hasta grandes volúmenes.' ),
					),
				),
			),
		),
		'destinos' => array(
			'label'    => 'Destinos',
			'priority' => 35,
			'sections' => array(
				'hero' => array(
					'label'  => 'Destinos · Banner',
					'fields' => array(
						'dest_hero_eyebrow' => array( 'Antetítulo', 'text', 'Destinos' ),
						'dest_hero_title' => array( 'Título', 'html', 'Destinos de envíos internacionales desde <span>{{origen_ciudad}}</span>' ),
						'dest_hero_bc_home' => array( 'Breadcrumb · Inicio', 'text', 'Inicio' ),
						'dest_hero_bc_current' => array( 'Breadcrumb · Actual', 'text', 'Destinos' ),
					),
				),
				'intro' => array(
					'label'  => 'Destinos · Introducción y tarjetas',
					'fields' => array(
						'dest_intro_sub' => array( 'Subtítulo', 'text', 'Destinos de envío internacional' ),
						'dest_intro_title' => array( 'Título', 'html', 'Nuestras rutas de envío, <span class="hl">país por país</span>' ),
						'dest_intro_text' => array( 'Texto', 'textarea', 'Desde {{origen_ciudad}} llevamos tus envíos a más de 30 países en América, Europa, Asia y África, por vía aérea y terrestre. Elige tu destino y conoce las modalidades y tiempos de entrega.' ),
					),
				),
				'otros' => array(
					'label'  => 'Destinos · Otros destinos (tabla)',
					'fields' => array(
						'dest_otros_sub' => array( 'Subtítulo', 'text', 'Otros destinos' ),
						'dest_otros_title' => array( 'Título', 'html', 'También enviamos a <span>muchos más países</span>' ),
						'dest_otros_text' => array( 'Texto', 'textarea', 'Estos destinos no cuentan con página propia, pero realizamos envíos internacionales de forma regular. Solicita tu cotización personalizada.' ),
						'dest_otros_th_pais' => array( 'Tabla · Encabezado País', 'text', 'País de destino' ),
						'dest_otros_th_tiempo' => array( 'Tabla · Encabezado Tiempo', 'text', 'Tiempo estimado' ),
						'dest_otros_th_cotizar' => array( 'Tabla · Encabezado Cotización', 'text', 'Cotización' ),
					),
				),
				'cta' => array(
					'label'  => 'Destinos · Llamado a la acción',
					'fields' => array(
						'dest_cta_sub' => array( 'Subtítulo', 'text', '¿No encuentras tu país?' ),
						'dest_cta_title' => array( 'Título', 'html', 'Escríbenos y te ayudamos con tu <span class="hl">envío internacional</span>' ),
						'dest_cta_text' => array( 'Texto', 'textarea', 'Cubrimos más de 30 países desde {{origen_ciudad}}. Cuéntanos tu destino y te damos una cotización a medida.' ),
						'dest_cta_wa' => array( 'Botón WhatsApp', 'text', 'Escribir por WhatsApp' ),
						'dest_cta_btn' => array( 'Botón Cotizar', 'text', 'Cotizar mi envío' ),
					),
				),
			),
		),
		'envio-internacional-de-documentos' => array(
			'label'    => 'Envío de Documentos',
			'priority' => 40,
			'sections' => array(
				'hero' => array(
					'label'  => 'Documentos · Banner',
					'fields' => array(
						'doc_hero_eyebrow' => array( 'Antetítulo', 'text', 'Servicios' ),
						'doc_hero_title' => array( 'Título', 'html', 'Envío internacional de <span>documentos</span>' ),
						'doc_hero_bc_inicio' => array( 'Breadcrumb · Inicio', 'text', 'Inicio' ),
						'doc_hero_bc_servicios' => array( 'Breadcrumb · Servicios', 'text', 'Servicios' ),
						'doc_hero_bc_current' => array( 'Breadcrumb · Actual', 'text', 'Envío de documentos' ),
					),
				),
				'intro' => array(
					'label'  => 'Documentos · Introducción',
					'fields' => array(
						'doc_img_1' => array( 'Imagen destacada (alt)', 'image', grenvios_img_url( 'content-bg-1.jpg' ) ),
						'doc_intro_title' => array( 'Título', 'text', 'Envío internacional de documentos desde {{origen_ciudad}}' ),
						'doc_intro_p1' => array( 'Párrafo 1', 'textarea', 'En Grenvíos gestionamos el envío de documentos internacional de forma segura, rápida y con seguimiento puerta a puerta. Trasladamos tu documentación importante desde {{origen_ciudad}}, {{origen_pais}} hacia cualquier destino de América, Europa y Asia, con la garantía de un servicio confiable y trato profesional en cada etapa del trayecto.' ),
						'doc_intro_p2' => array( 'Párrafo 2', 'textarea', 'Cada envío se entrega dentro de un sobre A4 sellado que protege tus documentos durante todo el recorrido. Nos encargamos del recojo, el embalaje seguro y la entrega final directamente en la dirección del destinatario.' ),
					),
				),
				'documentos' => array(
					'label'  => 'Documentos · Qué puedes enviar',
					'fields' => array(
						'doc_docs_title' => array( 'Título', 'text', '¿Qué documentos puedes enviar?' ),
						'doc_docs_intro' => array( 'Intro', 'textarea', 'Aceptamos todo tipo de documentación personal, académica y empresarial. Entre los documentos más enviados se encuentran:' ),
						'doc_docs_item1' => array( 'Ítem 1', 'text', 'Documentos legales y notariales' ),
						'doc_docs_item2' => array( 'Ítem 2', 'text', 'Títulos y certificados académicos' ),
						'doc_docs_item3' => array( 'Ítem 3', 'text', 'DNI y pasaportes' ),
						'doc_docs_item4' => array( 'Ítem 4', 'text', 'Contratos y poderes' ),
						'doc_docs_item5' => array( 'Ítem 5', 'text', 'Expedientes y trámites' ),
						'doc_docs_item6' => array( 'Ítem 6', 'text', 'Correspondencia oficial' ),
					),
				),
				'restricciones' => array(
					'label'  => 'Documentos · Restricciones',
					'fields' => array(
						'doc_restric_title' => array( 'Título', 'text', 'Restricciones del servicio' ),
						'doc_restric_p1' => array( 'Párrafo (inicio)', 'html', 'Por seguridad y normativa aduanera, el servicio de envío de documentos <strong>no admite dinero en efectivo, tarjetas bancarias ni cheques</strong>. Si necesitas trasladar bienes u objetos, te recomendamos nuestro servicio de' ),
						'doc_restric_link' => array( 'Enlace · paquetería', 'text', 'paquetería internacional' ),
						'doc_restric_p2' => array( 'Párrafo (final)', 'text', ', ideal para ese tipo de mercancía.' ),
					),
				),
				'tiempos' => array(
					'label'  => 'Documentos · Tiempos de entrega',
					'fields' => array(
						'doc_tiempos_title' => array( 'Título', 'text', 'Tiempos de entrega' ),
						'doc_tiempos_intro' => array( 'Intro', 'textarea', 'Trabajamos con rutas aéreas que garantizan plazos competitivos hacia los principales destinos:' ),
						'doc_tiempos_item1' => array( 'Ítem 1', 'text', 'América: 4 a 6 días hábiles' ),
						'doc_tiempos_item2' => array( 'Ítem 2', 'text', 'Europa: 4 a 6 días hábiles' ),
						'doc_tiempos_item3' => array( 'Ítem 3', 'text', 'Asia: 6 a 7 días hábiles' ),
						'doc_tiempos_item4' => array( 'Ítem 4', 'text', 'Entrega segura puerta a puerta' ),
					),
				),
				'destinos' => array(
					'label'  => 'Documentos · Destinos solicitados',
					'fields' => array(
						'doc_destinos_title' => array( 'Título', 'text', 'Destinos más solicitados' ),
						'doc_destinos_intro' => array( 'Intro', 'textarea', 'Gracias a nuestras rutas aéreas, los envíos de documentos hacia Norteamérica y Europa son especialmente ágiles. Conoce las condiciones de nuestros destinos rápidos:' ),
						'doc_destinos_item1_pre' => array( 'Ítem 1 · texto inicial', 'text', 'Envíos a ' ),
						'doc_destinos_item1_link' => array( 'Ítem 1 · enlace', 'text', 'Estados Unidos' ),
						'doc_destinos_item1_post' => array( 'Ítem 1 · texto final', 'text', ', rápidos por vía aérea' ),
						'doc_destinos_item2_pre' => array( 'Ítem 2 · texto inicial', 'text', 'Envíos a ' ),
						'doc_destinos_item2_link' => array( 'Ítem 2 · enlace', 'text', 'España' ),
						'doc_destinos_item2_post' => array( 'Ítem 2 · texto final', 'text', ', conexión directa con Europa' ),
						'doc_destinos_item3_pre' => array( 'Ítem 3 · texto inicial', 'text', 'Consulta todos nuestros ' ),
						'doc_destinos_item3_link' => array( 'Ítem 3 · enlace', 'text', 'destinos disponibles' ),
					),
				),
				'legal' => array(
					'label'  => 'Documentos · Validez legal',
					'fields' => array(
						'doc_legal_title' => array( 'Título', 'text', '¿Tu documento necesita validez legal en el extranjero?' ),
						'doc_legal_p1' => array( 'Párrafo (inicio)', 'textarea', 'Si vas a presentar tu documento ante una entidad de otro país, suele requerir legalización. Con nuestro servicio de' ),
						'doc_legal_link' => array( 'Enlace · apostilla', 'text', 'apostilla y traducción de documentos' ),
						'doc_legal_p2' => array( 'Párrafo (final)', 'text', ' lo dejamos listo y válido antes de enviarlo, todo en un solo lugar.' ),
					),
				),
				'contacto' => array(
					'label'  => 'Documentos · Contacto',
					'fields' => array(
						'doc_contacto_pre' => array( 'Texto inicial', 'text', '¿Tienes dudas sobre tu envío? Escríbenos a ' ),
						'doc_contacto_email' => array( 'Email', 'text', 'info@grenvios.com' ),
						'doc_contacto_mid1' => array( 'Texto intermedio 1', 'text', ', llámanos al ' ),
						'doc_contacto_phone' => array( 'Teléfono', 'text', '{{contacto_telefono}}' ),
						'doc_contacto_mid2' => array( 'Texto intermedio 2', 'text', ' o revisa nuestras ' ),
						'doc_contacto_faq' => array( 'Enlace · FAQ', 'text', 'preguntas frecuentes' ),
						'doc_contacto_end' => array( 'Texto final', 'text', '.' ),
					),
				),
				'cta' => array(
					'label'  => 'Documentos · CTA',
					'fields' => array(
						'doc_como_title' => array( 'Cómo funciona · título', 'text', '¿Cómo funciona?' ),
							'doc_como_s1_t' => array( 'Paso 1 · título', 'text', 'Recojo de tus documentos' ),
							'doc_como_s1_d' => array( 'Paso 1 · texto', 'textarea', 'Coordinamos el recojo en la dirección que indiques.' ),
							'doc_como_s2_t' => array( 'Paso 2 · título', 'text', 'Sobre A4 sellado y seguro' ),
							'doc_como_s2_d' => array( 'Paso 2 · texto', 'textarea', 'Tus documentos viajan protegidos en sobre A4 sellado.' ),
							'doc_como_s3_t' => array( 'Paso 3 · título', 'text', 'Envío vía aérea' ),
							'doc_como_s3_d' => array( 'Paso 3 · texto', 'textarea', 'Utilizamos rutas aéreas rápidas y confiables.' ),
							'doc_como_s4_t' => array( 'Paso 4 · título', 'text', 'Entrega puerta a puerta' ),
							'doc_como_s4_d' => array( 'Paso 4 · texto', 'textarea', 'Entregamos directamente en la dirección del destinatario.' ),
							'doc_no_intro' => array( 'Restricción · aviso', 'textarea', 'Por seguridad y normativa aduanera, no está permitido enviar:' ),
							'doc_no1' => array( 'No permitido 1', 'text', 'Dinero en efectivo' ),
							'doc_no2' => array( 'No permitido 2', 'text', 'Tarjetas bancarias' ),
							'doc_no3' => array( 'No permitido 3', 'text', 'Cheques' ),
							'doc_como_title_btn' => array( 'Restricción · botón', 'text', 'Conoce nuestros servicios' ),
							'doc_destinos_btn' => array( 'Destinos · botón', 'text', 'Ver todos los destinos' ),
							'doc_legal_f1' => array( 'Validez · ítem 1', 'text', 'Apostilla de documentos' ),
							'doc_legal_f2' => array( 'Validez · ítem 2', 'text', 'Traducción certificada' ),
							'doc_legal_f3' => array( 'Validez · ítem 3', 'text', 'Listo para entidades extranjeras' ),
							'doc_legal_btn' => array( 'Validez · botón', 'text', 'Más información' ),
							'doc_feat1_t' => array( 'Garantía 1 · título', 'text', 'Envíos seguros' ),
							'doc_feat1_d' => array( 'Garantía 1 · texto', 'text', 'Protegemos tus documentos en cada etapa del trayecto.' ),
							'doc_feat2_t' => array( 'Garantía 2 · título', 'text', 'Puerta a puerta' ),
							'doc_feat2_d' => array( 'Garantía 2 · texto', 'text', 'Sin complicaciones, llegamos hasta la dirección final.' ),
							'doc_feat3_t' => array( 'Garantía 3 · título', 'text', 'Cobertura internacional' ),
							'doc_feat3_d' => array( 'Garantía 3 · texto', 'text', 'Enviamos a América, Europa y Asia.' ),
							'doc_feat4_t' => array( 'Garantía 4 · título', 'text', 'Seguimiento en tiempo real' ),
							'doc_feat4_d' => array( 'Garantía 4 · texto', 'text', 'Consulta el estado de tu envío cuando quieras.' ),
							'doc_cta_title' => array( 'CTA · título', 'text', '¿Necesitas enviar un documento hoy?' ),
							'doc_cta_text' => array( 'CTA · texto', 'text', 'Realiza tu cotización en minutos y recibe atención personalizada.' ),
							'doc_cta_cotizar' => array( 'Botón Cotizar', 'text', 'Cotizar' ),
						'doc_cta_whatsapp' => array( 'Botón WhatsApp', 'text', 'WhatsApp' ),
					),
				),
			),
		),
		'envio-internacional-de-paquetes' => array(
			'label'    => 'Envío de Paquetes',
			'priority' => 50,
			'sections' => array(
				'hero' => array(
					'label'  => 'Paquetes · Banner',
					'fields' => array(
						'paq_hero_subtitle' => array( 'Subtítulo', 'text', 'Servicios' ),
						'paq_hero_title' => array( 'Título', 'html', 'Envío internacional de paquetes, <span>aéreo y terrestre</span>' ),
						'paq_hero_breadcrumb_inicio' => array( 'Breadcrumb · Inicio', 'text', 'Inicio' ),
						'paq_hero_breadcrumb_servicios' => array( 'Breadcrumb · Servicios', 'text', 'Servicios' ),
						'paq_hero_breadcrumb_actual' => array( 'Breadcrumb · Actual', 'text', 'Envío de paquetes' ),
					),
				),
				'intro' => array(
					'label'  => 'Paquetes · Introducción',
					'fields' => array(
						'paq_img_1' => array( 'Imagen destacada', 'image', grenvios_img_url( 'content-bg-2.jpg' ) ),
						'paq_intro_title' => array( 'Título', 'text', 'Envío internacional de paquetes desde {{origen_ciudad}}' ),
						'paq_intro_text' => array( 'Texto', 'textarea', 'En Grenvíos enviamos tus paquetes al exterior por vía aérea y terrestre desde {{origen_ciudad}}, {{origen_pais}}. Ya sea que necesites rapidez o el costo más económico, contamos con la modalidad ideal para cada tipo de envío, con embalaje seguro, seguimiento y entrega puerta a puerta.' ),
					),
				),
				'modalidades' => array(
					'label'  => 'Paquetes · Aéreo vs Terrestre',
					'fields' => array(
						'paq_modalidades_title' => array( 'Título', 'text', 'Aéreo vs. Terrestre: ¿cuál elegir?' ),
						'paq_modalidades_text' => array( 'Texto', 'textarea', 'Cada modalidad responde a una necesidad distinta. Estas son sus principales diferencias:' ),
						'paq_modalidades_aereo_1' => array( 'Aéreo · Punto 1', 'text', 'Aéreo: la opción más rápida, ideal para envíos urgentes a cualquier continente' ),
						'paq_modalidades_aereo_2' => array( 'Aéreo · Punto 2', 'text', 'Aéreo: recomendado para destinos lejanos como Europa, Asia y Norteamérica' ),
						'paq_modalidades_terrestre_1' => array( 'Terrestre · Punto 1', 'text', 'Terrestre: la alternativa más económica para países vecinos' ),
						'paq_modalidades_terrestre_2' => array( 'Terrestre · Punto 2', 'text', 'Terrestre: ideal para envíos a Sudamérica con mayor volumen' ),
					),
				),
				'volumetrico' => array(
					'label'  => 'Paquetes · Peso volumétrico',
					'fields' => array(
						'paq_volumetrico_title' => array( 'Título', 'text', '¿Cómo se calcula el peso volumétrico?' ),
						'paq_volumetrico_text' => array( 'Texto', 'textarea', 'El costo de un envío no depende solo del peso real de la balanza, sino también del espacio que ocupa el paquete. Por eso aplicamos la fórmula del peso volumétrico:' ),
						'paq_volumetrico_item_1' => array( 'Punto 1', 'text', 'Peso volumétrico = (alto × largo × ancho en cm) / 5000' ),
						'paq_volumetrico_item_2' => array( 'Punto 2', 'text', 'Se cobra el mayor valor entre el peso real y el peso volumétrico' ),
						'paq_volumetrico_text2' => array( 'Texto con teléfono', 'html', 'De esta forma, un paquete grande pero liviano se tarifa según el volumen que ocupa en el transporte. Si tienes dudas, calculamos el peso de tu envío sin compromiso al <a href="tel:{{contacto_tel}}">{{contacto_telefono}}</a>.' ),
					),
				),
				'terrestre' => array(
					'label'  => 'Paquetes · Envíos terrestres',
					'fields' => array(
						'paq_terrestre_title' => array( 'Título', 'text', 'Envíos terrestres: impuestos y tiempos por país' ),
						'paq_terrestre_text' => array( 'Texto introductorio', 'html', 'En los envíos terrestres se aplica un impuesto sobre el valor declarado en la boleta o factura de la mercancía, que varía según el país de destino. <strong>El impuesto se cancela en {{origen_ciudad}} junto con el envío</strong>; en destino el cliente solo retira su paquete (en Chile la entrega es a domicilio):' ),
						'paq_terrestre_text2' => array( 'Texto posterior a la tabla', 'textarea', 'Los porcentajes son referenciales sobre el valor declarado y te los confirmamos al cotizar, para que conozcas el costo total sin sorpresas. En la modalidad terrestre puedes enviar celulares, alimentos sellados, líquidos, artesanía, laptops, repuestos, equipaje, productos textiles, maquillaje y más.' ),
					),
				),
				'tabla' => array(
					'label'  => 'Paquetes · Tabla de impuestos',
					'fields' => array(
						'paq_tabla_th_pais' => array( 'Encabezado · País', 'text', 'País' ),
						'paq_tabla_th_impuesto' => array( 'Encabezado · Impuesto', 'text', 'Impuesto aprox.' ),
						'paq_tabla_th_tiempo' => array( 'Encabezado · Tiempo', 'text', 'Tiempo estimado' ),
						'paq_tabla_th_entrega' => array( 'Encabezado · Entrega', 'text', 'Entrega' ),
						'paq_tabla_pais_colombia' => array( 'País · Colombia', 'text', 'Colombia' ),
						'paq_tabla_pais_ecuador' => array( 'País · Ecuador', 'text', 'Ecuador' ),
						'paq_tabla_pais_chile' => array( 'País · Chile', 'text', 'Chile' ),
						'paq_tabla_pais_bolivia' => array( 'País · Bolivia', 'text', 'Bolivia' ),
						'paq_tabla_pais_argentina' => array( 'País · Argentina', 'text', 'Argentina' ),
						'paq_tabla_imp_20' => array( 'Impuesto · 20 %', 'text', '20 %' ),
						'paq_tabla_imp_18' => array( 'Impuesto · 18 %', 'text', '18 %' ),
						'paq_tabla_imp_295' => array( 'Impuesto · 29,5 %', 'text', '29,5 %' ),
						'paq_tabla_imp_15' => array( 'Impuesto · 15 %', 'text', '15 %' ),
						'paq_tabla_tiempo_10_15' => array( 'Tiempo · 10–15 días', 'text', '10–15 días hábiles' ),
						'paq_tabla_tiempo_8_10' => array( 'Tiempo · 8–10 días', 'text', '8–10 días hábiles' ),
						'paq_tabla_tiempo_10_20' => array( 'Tiempo · 10–20 días', 'text', '10–20 días hábiles' ),
						'paq_tabla_entrega_agencia' => array( 'Entrega · Agencia local', 'text', 'Agencia local' ),
						'paq_tabla_entrega_domicilio' => array( 'Entrega · A domicilio', 'text', 'A domicilio' ),
					),
				),
				'equipaje' => array(
					'label'  => 'Paquetes · Equipaje y compras',
					'fields' => array(
						'paq_equipaje_title' => array( 'Título', 'text', 'Equipaje y compras en {{origen_ciudad}}' ),
						'paq_equipaje_text' => array( 'Texto', 'textarea', '¿Compraste en {{origen_ciudad}} o dejaste equipaje en {{origen_pais}}? Recibimos tus compras y equipaje en nuestras instalaciones para enviarlos al exterior. Consolidamos, embalamos y despachamos tus pertenencias de forma segura hacia tu país de residencia.' ),
						'paq_img_2' => array( 'Imagen galería 1', 'image', grenvios_img_url( 'content-bg-3.jpg' ) ),
						'paq_img_3' => array( 'Imagen galería 2', 'image', grenvios_img_url( 'content-bg-4.jpg' ) ),
					),
				),
				'destinos' => array(
					'label'  => 'Paquetes · Destinos terrestres',
					'fields' => array(
						'paq_destinos_title' => array( 'Título', 'text', 'Destinos terrestres más solicitados' ),
						'paq_destinos_text' => array( 'Texto', 'textarea', 'Nuestros envíos terrestres son especialmente convenientes hacia los países vecinos de la región:' ),
						'paq_destinos_item_1' => array( 'Destino 1', 'html', 'Envíos a <a href="HOMEURL/destinos/ecuador/">Ecuador</a> por vía terrestre' ),
						'paq_destinos_item_2' => array( 'Destino 2', 'html', 'Envíos a <a href="HOMEURL/destinos/colombia/">Colombia</a> con tarifas competitivas' ),
						'paq_destinos_item_3' => array( 'Destino 3', 'html', 'Envíos a <a href="HOMEURL/destinos/chile/">Chile</a>, rápidos y económicos' ),
					),
				),
				'contacto' => array(
					'label'  => 'Paquetes · Enlaces y contacto',
					'fields' => array(
						'paq_relacionados_text' => array( 'Servicios relacionados', 'html', '¿Tu envío supera los 20 kg o es mercancía para tu negocio? Revisa nuestro servicio de <a href="HOMEURL/servicios/carga-internacional/">carga internacional</a>. Si solo necesitas enviar papeles o trámites, mira el <a href="HOMEURL/servicios/envio-internacional-de-documentos/">envío de documentos</a>.' ),
						'paq_contacto_text' => array( 'Contacto', 'html', 'Conoce más en nuestras <a href="HOMEURL/preguntas-frecuentes/">preguntas frecuentes</a> o escríbenos a <a href="mailto:info@grenvios.com">info@grenvios.com</a>.' ),
						'paq_destinos_btn' => array( 'Destinos · botón', 'text', 'Ver todos los destinos' ),
							'paq_feat1_t' => array( 'Garantía 1 · título', 'text', 'Embalaje seguro' ),
							'paq_feat1_d' => array( 'Garantía 1 · texto', 'text', 'Protegemos cada paquete para que llegue intacto.' ),
							'paq_feat2_t' => array( 'Garantía 2 · título', 'text', 'Aéreo y terrestre' ),
							'paq_feat2_d' => array( 'Garantía 2 · texto', 'text', 'Elige rapidez o el costo más económico.' ),
							'paq_feat3_t' => array( 'Garantía 3 · título', 'text', 'Puerta a puerta' ),
							'paq_feat3_d' => array( 'Garantía 3 · texto', 'text', 'Recojo y entrega sin complicaciones.' ),
							'paq_feat4_t' => array( 'Garantía 4 · título', 'text', 'Seguimiento en tiempo real' ),
							'paq_feat4_d' => array( 'Garantía 4 · texto', 'text', 'Consulta el estado de tu envío cuando quieras.' ),
							'paq_cta_title' => array( 'CTA · título', 'text', '¿Listo para enviar tu paquete?' ),
							'paq_cta_text' => array( 'CTA · texto', 'text', 'Cotiza tu envío en minutos y recibe atención personalizada.' ),
							'paq_cta_cotizar' => array( 'Botón · Cotizar', 'text', 'Cotizar' ),
						'paq_cta_whatsapp' => array( 'Botón · WhatsApp', 'text', 'WhatsApp' ),
					),
				),
			),
		),
		'carga-internacional' => array(
			'label'    => 'Carga Internacional',
			'priority' => 60,
			'sections' => array(
				'hero' => array(
					'label'  => 'Carga · Banner',
					'fields' => array(
						'carga_hero_subtitle' => array( 'Subtítulo', 'text', 'Servicios' ),
						'carga_hero_title' => array( 'Título', 'html', 'Carga internacional <span>aérea y terrestre</span>' ),
						'carga_hero_bc_inicio' => array( 'Breadcrumb Inicio', 'text', 'Inicio' ),
						'carga_hero_bc_servicios' => array( 'Breadcrumb Servicios', 'text', 'Servicios' ),
						'carga_hero_bc_current' => array( 'Breadcrumb actual', 'text', 'Carga internacional' ),
					),
				),
				'main' => array(
					'label'  => 'Carga · Introducción',
					'fields' => array(
						'carga_img_1' => array( 'Imagen destacada', 'image', grenvios_img_url( 'hanging-container.png' ) ),
						'carga_main_title1' => array( 'Título', 'text', 'Carga internacional para empresas y negocios' ),
						'carga_main_intro' => array( 'Párrafo', 'textarea', 'En Grenvíos movemos grandes volúmenes de mercancía desde {{origen_ciudad}}, {{origen_pais}} hacia el resto del mundo. Nuestro servicio de carga internacional aérea y terrestre está orientado a empresas, importadores y negocios que necesitan trasladar cargas pesadas con respaldo logístico profesional y documentación en regla.' ),
					),
				),
				'modalidades' => array(
					'label'  => 'Carga · Modalidades de carga',
					'fields' => array(
						'carga_modalidades_title' => array( 'Título', 'text', 'Modalidades de carga' ),
						'carga_modalidades_intro' => array( 'Párrafo', 'textarea', 'Adaptamos la modalidad de transporte al volumen y la urgencia de tu mercancía:' ),
						'carga_modalidades_aerea1' => array( 'Aérea ítem 1', 'text', 'Carga aérea desde 100 kg, desglosable según tu necesidad' ),
						'carga_modalidades_aerea2' => array( 'Aérea ítem 2', 'text', 'Ideal para mercancía urgente o de alto valor' ),
						'carga_modalidades_terrestre1' => array( 'Terrestre ítem 1', 'text', 'Carga terrestre de 500 a 800 kg' ),
						'carga_modalidades_terrestre2' => array( 'Terrestre ítem 2', 'text', 'Opción económica para envíos de gran volumen a la región' ),
					),
				),
				'docs' => array(
					'label'  => 'Carga · Documentación requerida',
					'fields' => array(
						'carga_docs_title' => array( 'Título', 'text', 'Documentación requerida' ),
						'carga_docs_intro' => array( 'Párrafo', 'textarea', 'Para gestionar tu carga internacional de forma ágil y conforme a la normativa aduanera, necesitamos la siguiente documentación:' ),
						'carga_docs_item1' => array( 'Documento 1', 'text', 'Boleta o factura de la mercancía' ),
						'carga_docs_item2' => array( 'Documento 2', 'text', 'Ficha técnica del producto' ),
						'carga_docs_contact' => array( 'Párrafo contacto', 'html', 'Nuestro equipo te asesora en todo el proceso documentario para que tu carga llegue sin contratiempos. Escríbenos a <a href="mailto:info@grenvios.com">info@grenvios.com</a> o llámanos al <a href="tel:{{contacto_tel}}">{{contacto_telefono}}</a>.' ),
					),
				),
				'solucion' => array(
					'label'  => 'Carga · Solución logística integral',
					'fields' => array(
						'carga_solucion_title' => array( 'Título', 'text', 'Solución logística integral' ),
						'carga_solucion_intro' => array( 'Párrafo', 'html', 'Coordinamos el recojo, consolidación, despacho y seguimiento de tu carga de principio a fin. Si tu volumen es menor, te recomendamos nuestro servicio de <a href="HOMEURL/servicios/envio-internacional-de-paquetes/">envío de paquetes internacional</a>, perfecto para envíos más pequeños y personales.' ),
						'carga_solucion_destinos' => array( 'Ítem destinos', 'html', 'Revisa todos nuestros <a href="HOMEURL/destinos/">destinos disponibles</a>' ),
						'carga_solucion_faq' => array( 'Ítem preguntas frecuentes', 'html', 'Consulta condiciones en nuestras <a href="HOMEURL/preguntas-frecuentes/">preguntas frecuentes</a>' ),
					),
				),
				'cta' => array(
					'label'  => 'Carga · Llamada a la acción',
					'fields' => array(
						'carga_feat1_t' => array( 'Garantía 1 · título', 'text', 'Carga aérea y terrestre' ),
						'carga_feat1_d' => array( 'Garantía 1 · texto', 'text', 'La modalidad ideal según peso, volumen y urgencia.' ),
						'carga_feat2_t' => array( 'Garantía 2 · título', 'text', 'Asesoría aduanera' ),
						'carga_feat2_d' => array( 'Garantía 2 · texto', 'text', 'Te acompañamos en la documentación y el proceso.' ),
						'carga_feat3_t' => array( 'Garantía 3 · título', 'text', 'Carga asegurada' ),
						'carga_feat3_d' => array( 'Garantía 3 · texto', 'text', 'Tu mercancía protegida durante todo el trayecto.' ),
						'carga_feat4_t' => array( 'Garantía 4 · título', 'text', 'Atención personalizada' ),
						'carga_feat4_d' => array( 'Garantía 4 · texto', 'text', 'Un asesor dedicado para tu operación de carga.' ),
						'carga_cta_title' => array( 'CTA · título', 'text', '¿Listo para mover tu carga al mundo?' ),
						'carga_cta_text' => array( 'CTA · texto', 'text', 'Cotiza tu carga internacional y recibe asesoría sin compromiso.' ),
						'carga_cta_cotizar' => array( 'Botón Cotizar', 'text', 'Cotizar' ),
						'carga_cta_whatsapp' => array( 'Botón WhatsApp', 'text', 'WhatsApp' ),
					),
				),
			),
		),
		'apostilla-y-traduccion' => array(
			'label'    => 'Apostilla y Traducción',
			'priority' => 70,
			'sections' => array(
				'hero' => array(
					'label'  => 'Apostilla · Banner',
					'fields' => array(
						'apos_hero_eyebrow' => array( 'Antetítulo', 'text', 'Servicios' ),
						'apos_hero_title' => array( 'Título', 'html', 'Apostilla y traducción de <span>documentos</span>' ),
						'apos_hero_bc_inicio' => array( 'Breadcrumb · Inicio', 'text', 'Inicio' ),
						'apos_hero_bc_servicios' => array( 'Breadcrumb · Servicios', 'text', 'Servicios' ),
						'apos_hero_bc_current' => array( 'Breadcrumb · Actual', 'text', 'Apostilla y traducción' ),
					),
				),
				'media' => array(
					'label'  => 'Apostilla · Imagen',
					'sel'    => '.service-featured-img',
					'fields' => array(
						'apos_img_1' => array( 'Imagen destacada', 'image', grenvios_img_url( 'content-bg-1.jpg' ) ),
					),
				),
				'intro' => array(
					'label'  => 'Apostilla · Introducción',
					'fields' => array(
						'apos_intro_title' => array( 'Título', 'text', 'Documentos con validez legal en el extranjero' ),
						'apos_intro_p1' => array( 'Párrafo 1', 'html', 'Antes de enviar tus documentos al extranjero, muchos trámites exigen que estén <strong>apostillados</strong> y, en algunos casos, <strong>traducidos oficialmente</strong>. En Grenvíos te acompañamos en todo el proceso: legalizamos y traducimos tu documentación para que sea válida en el país de destino y, si lo necesitas, la enviamos lista para entregar.' ),
						'apos_intro_p2' => array( 'Párrafo 2', 'textarea', 'Así reúnes legalización, traducción y envío internacional en un solo lugar, sin tener que coordinar varios proveedores ni perder tiempo en colas.' ),
					),
				),
				'what' => array(
					'label'  => 'Apostilla · Qué es',
					'fields' => array(
						'apos_what_title' => array( 'Título', 'text', '¿Qué es la apostilla?' ),
						'apos_what_p1' => array( 'Párrafo', 'textarea', 'La apostilla es una certificación que valida la autenticidad de un documento público para que tenga efecto legal en otro país miembro del Convenio de La Haya. Es el requisito más común para títulos, partidas y poderes que se usarán en el extranjero.' ),
					),
				),
				'docs' => array(
					'label'  => 'Apostilla · Documentos',
					'fields' => array(
						'apos_docs_title' => array( 'Título', 'text', 'Documentos que apostillamos' ),
						'apos_docs_intro' => array( 'Introducción', 'textarea', 'Gestionamos la apostilla de la documentación más solicitada para trámites internacionales:' ),
						'apos_docs_li1' => array( 'Ítem 1', 'text', 'Títulos y certificados académicos' ),
						'apos_docs_li2' => array( 'Ítem 2', 'text', 'Partidas de nacimiento, matrimonio y defunción' ),
						'apos_docs_li3' => array( 'Ítem 3', 'text', 'Poderes y documentos notariales' ),
						'apos_docs_li4' => array( 'Ítem 4', 'text', 'Antecedentes penales y policiales' ),
						'apos_docs_li5' => array( 'Ítem 5', 'text', 'Certificados de estudios y notas' ),
						'apos_docs_li6' => array( 'Ítem 6', 'text', 'Documentos comerciales y constancias' ),
					),
				),
				'trad' => array(
					'label'  => 'Apostilla · Traducción oficial',
					'fields' => array(
						'apos_trad_title' => array( 'Título', 'text', 'Traducción oficial' ),
						'apos_trad_p1' => array( 'Párrafo', 'html', 'Cuando el país de destino lo requiere, traducimos tus documentos al idioma necesario con traductores profesionales. Trabajamos los pares de idiomas más solicitados —<strong>inglés</strong> e <strong>italiano</strong>, entre otros— manteniendo el formato y la validez legal del documento original.' ),
						'apos_trad_li1' => array( 'Ítem 1', 'text', 'Traducción profesional al inglés' ),
						'apos_trad_li2' => array( 'Ítem 2', 'text', 'Traducción profesional al italiano' ),
						'apos_trad_li3' => array( 'Ítem 3', 'text', 'Respeto del formato original' ),
						'apos_trad_li4' => array( 'Ítem 4', 'text', 'Documentación lista para su trámite' ),
					),
				),
				'how' => array(
					'label'  => 'Apostilla · Cómo funciona',
					'fields' => array(
						'apos_how_title' => array( 'Título', 'text', '¿Cómo funciona el servicio?' ),
						'apos_how_intro' => array( 'Introducción', 'text', 'Te lo dejamos simple en cuatro pasos:' ),
						'apos_how_li1' => array( 'Paso 1', 'text', 'Nos cuentas qué documento tienes y en qué país lo vas a usar.' ),
						'apos_how_li2' => array( 'Paso 2', 'text', 'Verificamos si requiere apostilla, traducción o ambas.' ),
						'apos_how_li3' => array( 'Paso 3', 'text', 'Gestionamos la legalización y/o traducción profesional.' ),
						'apos_how_li4' => array( 'Paso 4', 'text', 'Si lo deseas, lo enviamos a tu destino con nuestro servicio internacional.' ),
					),
				),
				'combo' => array(
					'label'  => 'Apostilla · Cierre y CTA',
					'fields' => array(
						'apos_combo_title' => array( 'Título', 'text', 'Apostilla, traducción y envío en un solo lugar' ),
						'apos_combo_p1' => array( 'Párrafo 1', 'html', 'Una vez listo tu documento, lo combinamos con nuestro <a href="HOMEURL/servicios/envio-internacional-de-documentos/">envío de documentos internacional</a> para que llegue seguro y a tiempo a destinos como <a href="HOMEURL/destinos/estados-unidos/">Estados Unidos</a>, <a href="HOMEURL/destinos/espana/">España</a> y muchos <a href="HOMEURL/destinos/">países más</a>.' ),
						'apos_combo_p2' => array( 'Párrafo 2', 'html', '¿Tienes dudas sobre tu trámite? Escríbenos a <a href="mailto:info@grenvios.com">info@grenvios.com</a>, llámanos al <a href="tel:{{contacto_tel}}">{{contacto_telefono}}</a> o revisa nuestras <a href="HOMEURL/preguntas-frecuentes/">preguntas frecuentes</a>.' ),
						'apos_combo_btn' => array( 'Combo · botón', 'text', 'Cotizar apostilla + envío' ),
						'apos_feat1_t' => array( 'Garantía 1 · título', 'text', 'Apostilla del Convenio de La Haya' ),
						'apos_feat1_d' => array( 'Garantía 1 · texto', 'text', 'Legalizamos tu documento para el extranjero.' ),
						'apos_feat2_t' => array( 'Garantía 2 · título', 'text', 'Traducción certificada' ),
						'apos_feat2_d' => array( 'Garantía 2 · texto', 'text', 'Traductores oficiales en varios idiomas.' ),
						'apos_feat3_t' => array( 'Garantía 3 · título', 'text', 'Válido ante entidades' ),
						'apos_feat3_d' => array( 'Garantía 3 · texto', 'text', 'Listo para presentar en el país de destino.' ),
						'apos_feat4_t' => array( 'Garantía 4 · título', 'text', 'Todo en un solo lugar' ),
						'apos_feat4_d' => array( 'Garantía 4 · texto', 'text', 'Apostilla, traducción y envío internacional juntos.' ),
						'apos_cta_title' => array( 'CTA · título', 'text', '¿Necesitas apostillar y enviar tu documento?' ),
						'apos_cta_text' => array( 'CTA · texto', 'text', 'Lo dejamos listo y válido antes de enviarlo. Cotiza sin compromiso.' ),
						'apos_cta_cotizar' => array( 'Botón Cotizar', 'text', 'Cotizar' ),
						'apos_cta_whatsapp' => array( 'Botón WhatsApp', 'text', ' WhatsApp' ),
					),
				),
			),
		),
		'cotizar' => array(
			'label'    => 'Cotizar',
			'priority' => 80,
			'sections' => array(
				'hero' => array(
					'label'  => 'Cotizar · Banner',
					'fields' => array(
						'cot_hero_subtitle' => array( 'Subtítulo', 'text', 'Cotización' ),
						'cot_hero_title' => array( 'Título', 'html', 'Cotizar envío internacional <span>en minutos</span>' ),
						'cot_hero_breadcrumb_home' => array( 'Breadcrumb · Inicio', 'text', 'Inicio' ),
						'cot_hero_breadcrumb_current' => array( 'Breadcrumb · Actual', 'text', 'Cotizar' ),
					),
				),
				'form' => array(
					'label'  => 'Cotizar · Formulario',
					'fields' => array(
						'cot_form_heading' => array( 'Encabezado', 'text', 'Cotizar envío internacional' ),
						'cot_form_intro' => array( 'Introducción', 'textarea', 'Calcula el costo de tu envío según peso, volumen y destino. Te respondemos en minutos.' ),
						'cot_form_origin_value' => array( 'Origen · Valor por defecto', 'text', '{{origen_ciudad}}, {{origen_pais}}' ),
						'cot_form_ph_name' => array( 'Placeholder · Nombre', 'text', 'Tu nombre completo' ),
						'cot_form_ph_phone' => array( 'Placeholder · Teléfono', 'text', 'Tu número de contacto' ),
						'cot_form_ph_email' => array( 'Placeholder · Correo', 'text', 'tucorreo@ejemplo.com' ),
						'cot_form_ph_city' => array( 'Placeholder · Ciudad', 'text', 'Ciudad de entrega' ),
						'cot_form_ph_postal' => array( 'Placeholder · Código postal', 'text', 'Código postal del destino' ),
						'cot_form_ph_weight' => array( 'Placeholder · Peso', 'text', 'Ej. 5' ),
						'cot_form_ph_value' => array( 'Placeholder · Valor mercancía', 'text', 'Según boleta o factura' ),
						'cot_form_ph_height' => array( 'Placeholder · Alto', 'text', 'Alto' ),
						'cot_form_ph_length' => array( 'Placeholder · Largo', 'text', 'Largo' ),
						'cot_form_ph_width' => array( 'Placeholder · Ancho', 'text', 'Ancho' ),
						'cot_form_ph_message' => array( 'Placeholder · Detalles', 'text', 'Cuéntanos el contenido y cualquier detalle de tu envío' ),
						'cot_form_label_name' => array( 'Etiqueta · Nombre', 'text', 'Nombre' ),
						'cot_form_label_phone' => array( 'Etiqueta · Teléfono', 'text', 'Teléfono' ),
						'cot_form_label_email' => array( 'Etiqueta · Correo', 'text', 'Correo' ),
						'cot_form_label_origin' => array( 'Etiqueta · Origen', 'text', 'Origen' ),
						'cot_form_label_country' => array( 'Etiqueta · País de destino', 'text', 'País de destino' ),
						'cot_form_country_placeholder' => array( 'Opción · Placeholder país', 'text', 'Selecciona un país' ),
						'cot_form_label_type' => array( 'Etiqueta · Tipo de envío', 'text', 'Tipo de envío' ),
						'cot_form_type_placeholder' => array( 'Opción · Placeholder tipo', 'text', 'Selecciona una opción' ),
						'cot_form_label_city' => array( 'Etiqueta · Ciudad de destino', 'text', 'Ciudad de destino' ),
						'cot_form_label_postal' => array( 'Etiqueta · Código postal', 'text', 'Código postal' ),
						'cot_form_label_weight' => array( 'Etiqueta · Peso (kg)', 'text', 'Peso (kg)' ),
						'cot_form_label_value' => array( 'Etiqueta · Valor de la mercancía', 'text', 'Valor de la mercancía' ),
						'cot_form_label_measures' => array( 'Etiqueta · Medidas del paquete', 'text', 'Medidas del paquete (cm)' ),
						'cot_form_label_message' => array( 'Etiqueta · Detalles adicionales', 'text', 'Detalles adicionales' ),
						'cot_form_submit' => array( 'Botón · Enviar', 'text', 'Solicitar cotización' ),
					),
				),
				'aside' => array(
					'label'  => 'Cotizar · Lateral (peso volumétrico)',
					'fields' => array(
						'cot_aside_title' => array( 'Título', 'text', '¿Cómo se calcula el peso volumétrico?' ),
						'cot_aside_p1' => array( 'Párrafo', 'textarea', 'En los envíos internacionales se cobra por el peso real o el peso volumétrico, el que sea mayor. El peso volumétrico se calcula con las dimensiones del paquete:' ),
						'cot_aside_formula' => array( 'Fórmula', 'text', 'Alto (cm) × Largo (cm) × Ancho (cm) ÷ 5000 = peso volumétrico (kg)' ),
						'cot_aside_check_1' => array( 'Lista · Paso 1', 'text', 'Mide cada lado del paquete en centímetros.' ),
						'cot_aside_check_2' => array( 'Lista · Paso 2', 'text', 'Multiplica alto, largo y ancho.' ),
						'cot_aside_check_3' => array( 'Lista · Paso 3', 'text', 'Divide el resultado entre 5000.' ),
						'cot_aside_check_4' => array( 'Lista · Paso 4', 'text', 'Compara con el peso real y usa el mayor.' ),
						'cot_aside_services' => array( 'Párrafo · Enlaces de servicios', 'html', '¿No sabes qué servicio elegir? Revisa el <a href="HOMEURL/servicios/envio-internacional-de-paquetes/">envío de paquetes</a>, el <a href="HOMEURL/servicios/envio-internacional-de-documentos/">envío de documentos</a> o la <a href="HOMEURL/servicios/carga-internacional/">carga internacional</a>, y consulta los tiempos por país en nuestros <a href="HOMEURL/destinos/">destinos</a>.' ),
					),
				),
				'call' => array(
					'label'  => 'Cotizar · Llamada / WhatsApp',
					'fields' => array(
						'cot_call_label' => array( 'Etiqueta llamada', 'text', 'Llámanos para cotizar' ),
						'cot_call_phone' => array( 'Teléfono', 'text', '{{contacto_telefono}}' ),
						'cot_shared_whatsapp_btn' => array( 'Botón · WhatsApp', 'text', 'Cotizar por WhatsApp' ),
					),
				),
				'steps' => array(
					'label'  => 'Cotizar · 3 pasos',
					'fields' => array(
						'cot_steps_subheading' => array( 'Subtítulo', 'text', 'Cotizar es muy fácil' ),
						'cot_steps_title' => array( 'Título', 'html', 'Cotizar envío internacional <span class="hl">en 3 pasos</span>' ),
						'cot_step1_title' => array( 'Paso 1 · Título', 'text', 'Cuéntanos tu envío' ),
						'cot_step1_text' => array( 'Paso 1 · Texto', 'textarea', 'Indícanos el país de destino, el tipo de envío (documentos, paquete o carga) y el peso o las dimensiones de tu paquete.' ),
						'cot_step2_title' => array( 'Paso 2 · Título', 'text', 'Recibe tu cotización' ),
						'cot_step2_text' => array( 'Paso 2 · Texto', 'textarea', 'Calculamos el costo según peso real o volumétrico, destino y modalidad (aérea o terrestre), y te respondemos en minutos.' ),
						'cot_step3_title' => array( 'Paso 3 · Título', 'text', 'Programa tu recojo' ),
						'cot_step3_text' => array( 'Paso 3 · Texto', 'textarea', 'Coordinamos el recojo a domicilio en {{origen_ciudad}} o nos visitas en {{contacto_direccion}}. Nosotros nos encargamos del resto.' ),
					),
				),
				'cta' => array(
					'label'  => 'Cotizar · CTA WhatsApp',
					'fields' => array(
						'cot_cta_subheading' => array( 'Subtítulo', 'text', '¿Prefieres cotizar por WhatsApp?' ),
						'cot_cta_title' => array( 'Título', 'html', 'Escríbenos y recibe tu cotización en <span class="hl">minutos</span>' ),
						'cot_cta_text' => array( 'Texto', 'textarea', 'Cuéntanos el destino, el peso y el contenido de tu envío. Un asesor te atenderá al instante.' ),
					),
				),
			),
		),
		'rastreo-de-envios' => array(
			'label'    => 'Rastrea tu Envío',
			'priority' => 90,
			'sections' => array(
				'hero' => array(
					'label'  => 'Rastreo · Banner',
					'fields' => array(
						'rast_hero_subtitle' => array( 'Subtítulo', 'text', 'Seguimiento' ),
						'rast_hero_title' => array( 'Título', 'html', 'Rastreo de envíos <span>internacionales</span>' ),
						'rast_hero_breadcrumb_home' => array( 'Breadcrumb · Inicio', 'text', 'Inicio' ),
						'rast_hero_breadcrumb_current' => array( 'Breadcrumb · Actual', 'text', 'Rastreo de envíos' ),
					),
				),
				'form' => array(
					'label'  => 'Rastreo · Formulario',
					'fields' => array(
						'rast_form_title' => array( 'Título', 'text', 'Consulta el estado de tu envío' ),
						'rast_form_text' => array( 'Texto', 'textarea', 'Ingresa tu número de guía y consulta al instante en qué etapa está tu envío.' ),
						'rast_form_placeholder' => array( 'Campo (placeholder)', 'text', 'Número de guía' ),
						'rast_form_button' => array( 'Botón', 'text', 'Consultar mi envío' ),
					),
				),
				'info' => array(
					'label'  => 'Rastreo · Información',
					'fields' => array(
						'rast_info_title' => array( 'Título', 'text', 'Seguimiento de tu envío internacional' ),
						'rast_info_text' => array( 'Texto', 'textarea', 'En Grenvíos acompañamos tu envío internacional desde {{origen_ciudad}} hacia cualquier destino de América, Europa, Asia y África. Con tu número de guía, un asesor te confirma en qué etapa se encuentra tu paquete, documento o carga: etiqueta creada, en tránsito, en proceso de aduana, listo para la entrega o entregado. Así sabes en todo momento dónde está tu envío y cuándo llegará a su destino.' ),
						'rast_info_card1_title' => array( 'Tarjeta 1 · Título', 'text', 'Estado por número de guía' ),
						'rast_info_card1_text' => array( 'Tarjeta 1 · Texto', 'textarea', 'Con tu número de guía, un asesor te confirma en qué etapa va tu envío de forma rápida y clara.' ),
						'rast_info_card2_title' => array( 'Tarjeta 2 · Título', 'text', 'Te avisamos en cada etapa' ),
						'rast_info_card2_text' => array( 'Tarjeta 2 · Texto', 'textarea', 'Te mantenemos informado cuando tu paquete cambia de estado, especialmente al ingresar y salir de aduana.' ),
						'rast_info_card3_title' => array( 'Tarjeta 3 · Título', 'text', 'Soporte por WhatsApp' ),
						'rast_info_card3_text' => array( 'Tarjeta 3 · Texto', 'textarea', '¿Dudas con tu seguimiento? Un asesor te ayuda a ubicar tu envío internacional al instante.' ),
					),
				),
				'estados' => array(
					'label'  => 'Rastreo · Estados del envío',
					'fields' => array(
						'rast_estados_subtitle' => array( 'Subtítulo', 'text', 'Estados del envío' ),
						'rast_estados_title' => array( 'Título', 'html', 'Conoce cada etapa de tu <span class="hl">paquete</span>' ),
						'rast_estados_text' => array( 'Texto', 'textarea', 'Hacemos seguimiento de tus envíos internacionales desde {{origen_ciudad}} hasta su destino final.' ),
						'rast_estados_item1_title' => array( 'Estado 1 · Título', 'text', 'Etiqueta creada' ),
						'rast_estados_item1_text' => array( 'Estado 1 · Texto', 'textarea', 'Registramos tu envío y generamos su número de guía. Tu paquete está listo para iniciar su recorrido.' ),
						'rast_estados_item2_title' => array( 'Estado 2 · Título', 'text', 'Salió del centro de recolección' ),
						'rast_estados_item2_text' => array( 'Estado 2 · Texto', 'textarea', 'Tu paquete fue procesado en nuestra sede de {{origen_ciudad}} y despachado hacia su destino.' ),
						'rast_estados_item3_title' => array( 'Estado 3 · Título', 'text', 'En tránsito' ),
						'rast_estados_item3_text' => array( 'Estado 3 · Texto', 'textarea', 'Tu envío avanza hacia su país de destino por vía aérea o terrestre.' ),
						'rast_estados_item4_title' => array( 'Estado 4 · Título', 'text', 'En proceso de aduana' ),
						'rast_estados_item4_text' => array( 'Estado 4 · Texto', 'textarea', 'El envío se encuentra en revisión y despacho aduanero para su liberación e ingreso al destino.' ),
						'rast_estados_item5_title' => array( 'Estado 5 · Título', 'text', 'Retenido' ),
						'rast_estados_item5_text' => array( 'Estado 5 · Texto', 'textarea', 'El envío quedó momentáneamente retenido en aduana. Si se necesita algún documento, te lo informamos.' ),
						'rast_estados_item6_title' => array( 'Estado 6 · Título', 'text', 'Listo para la entrega' ),
						'rast_estados_item6_text' => array( 'Estado 6 · Texto', 'textarea', 'Tu paquete fue liberado y está listo para entregarse a domicilio o en la agencia local de destino.' ),
						'rast_estados_item7_title' => array( 'Estado 7 · Título', 'text', 'Entregado' ),
						'rast_estados_item7_text' => array( 'Estado 7 · Texto', 'textarea', 'Tu envío llegó a su destino y fue entregado de forma segura al destinatario.' ),
					),
				),
				'cta' => array(
					'label'  => 'Rastreo · CTA',
					'fields' => array(
						'rast_cta_title' => array( 'Título', 'text', '¿No tienes tu número de guía?' ),
						'rast_cta_text' => array( 'Texto', 'html', 'Escríbenos por WhatsApp y te ayudamos a ubicar tu envío internacional desde {{origen_ciudad}}. ¿Aún no envías? <a href="HOMEURL/cotizar/">Cotiza tu envío</a> o resuelve tus dudas en las <a href="HOMEURL/preguntas-frecuentes/">preguntas frecuentes</a>.' ),
						'rast_cta_button' => array( 'Botón', 'text', 'Escríbenos por WhatsApp' ),
					),
				),
			),
		),
		'contacto' => array(
			'label'    => 'Contacto',
			'priority' => 100,
			'sections' => array(
				'hero' => array(
					'label'  => 'Contacto · Banner',
					'fields' => array(
						'cont_hero_eyebrow' => array( 'Antetítulo', 'text', 'Contacto' ),
						'cont_hero_title' => array( 'Título', 'html', 'Agencia de envíos internacionales <span>en {{origen_ciudad}}</span>' ),
						'cont_hero_breadcrumb_home' => array( 'Breadcrumb · Inicio', 'text', 'Inicio' ),
						'cont_hero_breadcrumb_current' => array( 'Breadcrumb · Actual', 'text', 'Contacto' ),
					),
				),
				'map' => array(
					'label'  => 'Contacto · Mapa',
					'sel'    => '.map-wrapper',
					'fields' => array(
						'cont_map_query' => array( 'Mapa · URL embed de Google', 'text', 'https://www.google.com/maps?q=Jr.+Callao+220,+Cercado+de+Lima,+Per%C3%BA&output=embed' ),
					),
				),
				'form' => array(
					'label'  => 'Contacto · Formulario',
					'fields' => array(
						'cont_form_eyebrow' => array( 'Antetítulo', 'text', 'Envíanos un mensaje' ),
						'cont_form_title' => array( 'Título', 'html', 'Escríbenos sin <span class="hl">compromiso</span>' ),
						'cont_form_text' => array( 'Texto', 'html', 'Resolvemos tus dudas sobre envíos internacionales desde {{origen_ciudad}} y te ayudamos a <br> cotizar el mejor servicio para tu paquete.' ),
						'cont_form_type_placeholder' => array( 'Tipo de envío · Opción inicial', 'text', 'Selecciona una opción' ),
						'cont_form_ph_firstname' => array( 'Placeholder · Nombre', 'text', 'Nombre' ),
						'cont_form_ph_lastname' => array( 'Placeholder · Apellido', 'text', 'Apellido' ),
						'cont_form_ph_email' => array( 'Placeholder · Correo', 'text', 'Correo' ),
						'cont_form_ph_phone' => array( 'Placeholder · Teléfono', 'text', 'Teléfono' ),
						'cont_form_ph_country' => array( 'Placeholder · País de destino', 'text', 'País de destino' ),
						'cont_form_ph_content' => array( 'Placeholder · Contenido', 'text', 'Ej. documentos, ropa, repuestos…' ),
						'cont_form_ph_weight' => array( 'Placeholder · Peso', 'text', 'Peso en kg' ),
						'cont_form_ph_dimensions' => array( 'Placeholder · Medidas', 'text', 'Ej. 30 × 40 × 20' ),
						'cont_form_ph_message' => array( 'Placeholder · Mensaje', 'text', 'Mensaje' ),
						'cont_form_label_firstname' => array( 'Etiqueta · Nombre', 'text', 'Nombre' ),
						'cont_form_label_lastname' => array( 'Etiqueta · Apellido', 'text', 'Apellido' ),
						'cont_form_label_email' => array( 'Etiqueta · Correo', 'text', 'Correo' ),
						'cont_form_label_phone' => array( 'Etiqueta · Teléfono', 'text', 'Teléfono' ),
						'cont_form_label_country' => array( 'Etiqueta · País de destino', 'text', 'País de destino' ),
						'cont_form_label_shipping_type' => array( 'Etiqueta · Tipo de envío', 'text', 'Tipo de envío' ),
						'cont_form_label_content' => array( 'Etiqueta · Contenido del envío', 'text', 'Contenido del envío' ),
						'cont_form_label_weight' => array( 'Etiqueta · Peso', 'text', 'Peso aproximado (kg)' ),
						'cont_form_label_dimensions' => array( 'Etiqueta · Medidas', 'text', 'Medidas (alto × largo × ancho en cm)' ),
						'cont_form_label_message' => array( 'Etiqueta · Mensaje', 'text', 'Mensaje' ),
						'cont_form_submit' => array( 'Botón enviar', 'text', 'Enviar mensaje' ),
					),
				),
				'info' => array(
					'label'  => 'Contacto · Datos de contacto',
					'fields' => array(
						'cont_info_eyebrow' => array( 'Antetítulo', 'text', '¿Necesitas ayuda?' ),
						'cont_info_title' => array( 'Título', 'html', 'Contáctanos <br><span class="hl">en {{origen_ciudad}}</span>' ),
						'cont_info_text' => array( 'Texto', 'html', 'Estamos para coordinar tus envíos internacionales <br>de forma rápida y segura.' ),
						'cont_info_phone_label' => array( 'Etiqueta · Teléfono', 'text', 'Teléfono' ),
						'cont_info_phone_value' => array( 'Teléfono · Valor visible', 'text', '{{contacto_telefono}}' ),
						'cont_info_email_label' => array( 'Etiqueta · Correo', 'text', 'Correo' ),
						'cont_info_email_value' => array( 'Correo · Valor visible', 'text', 'info@grenvios.com' ),
						'cont_info_address_label' => array( 'Etiqueta · Dirección', 'text', 'Dirección' ),
						'cont_info_address_value' => array( 'Dirección · Valor', 'text', '{{contacto_direccion}}' ),
						'cont_info_hours_label' => array( 'Etiqueta · Horario', 'text', 'Horario' ),
						'cont_info_hours_value' => array( 'Horario · Valor', 'text', 'Lun. a vie. 9:00–18:00 h · Sáb. 9:00–13:00 h' ),
						'cont_info_whatsapp_btn' => array( 'Botón WhatsApp', 'text', 'Escríbenos por WhatsApp' ),
					),
				),
				'cta' => array(
						'label'  => 'Contacto · Llamado a la acción',
						'fields' => array(
							'cont_cta_subheading'   => array( 'Subtítulo', 'text', '¿Listo para enviar?' ),
							'cont_cta_title'        => array( 'Título', 'html', '¡Tu paquete al mundo<br>con total <span class="hl">confianza!</span>' ),
							'cont_cta_btn'          => array( 'Botón cotizar', 'text', 'Cotizar ahora' ),
							'cont_cta_btn_whatsapp' => array( 'Botón WhatsApp', 'text', 'WhatsApp' ),
							'cont_cta_men'          => array( 'Imagen del repartidor (ilustración)', 'image', grenvios_img_url( 'delivery-men-2.png' ) ),
						),
					),
					'cards' => array(
					'label'  => 'Contacto · Tarjetas de atención',
					'fields' => array(
						'cont_cards_eyebrow' => array( 'Antetítulo', 'text', 'Atención cercana' ),
						'cont_cards_title' => array( 'Título', 'html', 'Te acompañamos en cada <span class="hl">envío internacional</span>' ),
						'cont_card1_title' => array( 'Tarjeta 1 · Título', 'text', 'Respuesta inmediata' ),
						'cont_card1_text' => array( 'Tarjeta 1 · Texto', 'html', 'Escríbenos por WhatsApp al {{contacto_telefono}} y un asesor resuelve tus dudas y te ayuda a <a href="HOMEURL/cotizar/">cotizar tu envío</a> al instante.' ),
						'cont_card2_title' => array( 'Tarjeta 2 · Título', 'text', 'Recojo a domicilio en {{origen_ciudad}}' ),
						'cont_card2_text' => array( 'Tarjeta 2 · Texto', 'html', 'Coordinamos el recojo de tu <a href="HOMEURL/servicios/envio-internacional-de-paquetes/">paquete</a> donde estés en {{origen_ciudad}}, sin que tengas que trasladarte.' ),
						'cont_card3_title' => array( 'Tarjeta 3 · Título', 'text', 'Asesoría en aduanas' ),
						'cont_card3_text' => array( 'Tarjeta 3 · Texto', 'html', 'Te orientamos en la documentación y el embalaje para que tu <a href="HOMEURL/servicios/">envío internacional</a> a cualquiera de nuestros <a href="HOMEURL/destinos/">destinos</a> llegue sin demoras.' ),
					),
				),
			),
		),
	) );
}

/* ¿La página (slug) tiene texto parametrizado por Customizer? */
function grenvios_is_parametrized( $slug ) {
	$reg = grenvios_text_registry();
	return $slug && isset( $reg[ $slug ] );
}

/* Aplana el registro: clave => [ label, tipo, default, page, section ]. */
function grenvios_text_fields() {
	static $flat = null;
	if ( $flat !== null ) return $flat;
	$flat = array();
	foreach ( grenvios_text_registry() as $slug => $page ) {
		foreach ( $page['sections'] as $secKey => $sec ) {
			foreach ( $sec['fields'] as $key => $f ) {
				$flat[ $key ] = array(
					'label'   => $f[0],
					'type'    => $f[1],
					'default' => $f[2],
					'page'    => $slug,
					'section' => $secKey,
				);
			}
		}
	}
	return $flat;
}

/* Valor efectivo de un campo de texto: el guardado en post-meta (editor de página)
 * o el valor por defecto del registro. La lectura/guardado vive en inc/page-editor.php. */
function grenvios_text_value( $key ) {
	$fields = grenvios_text_fields();
	if ( ! isset( $fields[ $key ] ) ) return '';
	return grenvios_field( $key, $fields[ $key ]['default'] );
}

/* Reemplaza los tokens {{clave}} de un HTML por sus valores (+ repeaters {{REP:clave}}). */
function grenvios_apply_text_tokens( $html ) {
	if ( strpos( $html, '{{' ) === false ) return $html;

	grenvios_perf_mark( 'tok: inicio' );
	// Anclas de sección para el editor (solo admins): permiten "llevar" a la sección.
	if ( function_exists( 'grenvios_inject_section_anchors' ) && function_exists( 'current_user_can' ) && current_user_can( 'edit_posts' ) ) {
		$html = grenvios_inject_section_anchors( $html, grenvios_current_slug() );
	}
	/* Se resuelven SOLO los tokens que aparecen en este HTML.
	 *
	 * El registro tiene 900 campos y cada valor pasa por post-meta y por los
	 * filtros de contenido por país: armar el mapa entero costaba 0,20 s por
	 * página, y un partial usa una docena de tokens. Aquí se leen del HTML los
	 * que hay, se resuelven esos y se guardan por página y ruta, que es de lo
	 * único de lo que dependen. El resto no se toca nunca. */
	static $cache = array();
	$ck = (int) ( function_exists( 'get_queried_object_id' ) ? get_queried_object_id() : 0 )
		. '|' . ( function_exists( 'grenvios_i18n_current' ) ? grenvios_i18n_current() : '' );
	if ( ! isset( $cache[ $ck ] ) ) $cache[ $ck ] = array();

	$map = array();
	if ( preg_match_all( '/\{\{([A-Za-z0-9_:-]+)\}\}/', $html, $encontrados ) ) {
		$campos = grenvios_text_fields();
		foreach ( array_unique( $encontrados[1] ) as $clave ) {
			if ( array_key_exists( $clave, $cache[ $ck ] ) ) {
				$map[ '{{' . $clave . '}}' ] = $cache[ $ck ][ $clave ];
				continue;
			}
			if ( ! isset( $campos[ $clave ] ) ) continue;     // lo resuelven otros filtros
			$valor = grenvios_text_value( $clave );
			$cache[ $ck ][ $clave ] = $valor;
			$map[ '{{' . $clave . '}}' ] = $valor;
		}
	}

	/* Filtro `grenvios_text_tokens`: lo usan inc/sedes-contenido.php (origen) y
	 * inc/paises-home-hero.php (destino de la ruta). Reciben el mapa de los
	 * tokens presentes, que es justo sobre los que pueden actuar. */
	$map = (array) apply_filters( 'grenvios_text_tokens', $map );

	if ( $map ) $html = strtr( $html, $map );
	grenvios_perf_mark( 'tok: strtr' );

	/* Los textos editables pueden traer enlaces escritos como HOMEURL/… (así
	 * vienen los valores por defecto de varios campos). grenvios_partial_raw()
	 * sustituye HOMEURL ANTES de meter los textos, así que esos quedaban
	 * literales: href="HOMEURL/cotizar/" es una ruta relativa que da 404. Se
	 * resuelve aquí con la misma raíz que usa el partial. */
	if ( strpos( $html, 'HOMEURL' ) !== false ) {
		$root = function_exists( 'grenvios_i18n_site_root' ) ? grenvios_i18n_site_root() : untrailingslashit( home_url() );
		$html = str_replace( 'HOMEURL', $root, $html );
	}

	// Repeaters dinámicos: {{REP:clave}} → HTML generado de los ítems.
	if ( function_exists( 'grenvios_apply_repeater_tokens' ) ) {
		$html = grenvios_apply_repeater_tokens( $html );
	}
	/* Filtro `grenvios_text_html`: última pasada sobre el HTML ya montado.
	 * Hace falta porque strtr() sustituye en UNA sola pasada: un token que
	 * venga DENTRO de un texto ya sustituido o de un repeater no lo alcanza.
	 * Ahí es donde inc/sedes-contenido.php resuelve {{origen_ciudad}}. */
	grenvios_perf_mark( 'tok: repeaters' );

	$html = apply_filters( 'grenvios_text_html', $html );

	grenvios_perf_mark( 'tok: text_html' );

	return $html;
}

/* Sanitizador según el tipo de campo de texto. */
function grenvios_sanitize_text_field_by_type( $type ) {
	if ( $type === 'html' ) {
		return function ( $value ) {
			return wp_kses( $value, array(
				'br'     => array(),
				'span'   => array( 'class' => array() ),
				'strong' => array(), 'b' => array(),
				'em'     => array(), 'i' => array(),
				'a'      => array( 'href' => array(), 'target' => array(), 'rel' => array() ),
			) );
		};
	}
	if ( $type === 'textarea' ) return 'sanitize_textarea_field';
	return 'sanitize_text_field';
}

/* NOTA: los TEXTOS de página ya NO se editan desde el Customizer. Se editan con el
 * Editor de Página en línea (inc/page-editor.php), que guarda en post-meta por página.
 * El registro grenvios_text_registry() de arriba es la fuente compartida de campos. */
