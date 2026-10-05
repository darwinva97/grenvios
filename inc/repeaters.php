<?php
/**
 * Grenvíos — Repeaters (contenido dinámico: añadir/quitar/reordenar ítems)
 * Patrón portado del tema de referencia (Notaría) y adaptado a la arquitectura
 * de partials con tokens de Grenvíos.
 *
 * - Almacenamiento: array en post-meta `grenvios_rep_<key>` (por página).
 * - Render: en los partials `content-*.html` se coloca un token {{REP:clave}}
 *   que se reemplaza por el HTML generado de los ítems (grenvios_render_repeater).
 * - Edición: el panel del editor (inc/page-editor.php) pinta cada repeater como
 *   un acordeón con ítems add/quitar/subir/bajar; el JS (page-editor.js) los
 *   recolecta y los envía al guardar; el REST los guarda en post-meta.
 *
 * Para añadir un repeater nuevo:
 *   1) define sus subcampos y entrada en grenvios_repeater_schema($slug)
 *   2) sus valores por defecto en grenvios_repeater_defaults($slug)
 *   3) su markup en grenvios_render_repeater($key)
 *   4) coloca {{REP:clave}} en el partial donde van los ítems
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ══════════════════════════════════════
   LECTURA DE UN REPEATER (post-meta por página, con defaults)
══════════════════════════════════════ */
function grenvios_repeater( $key, $default = null ) {
	if ( $default === null ) {
		if ( $key === 'page_faq' && function_exists( 'grenvios_page_faqs' ) ) {
			// Las preguntas por defecto vienen del tema (grenvios_page_faqs), por página.
			$default = array();
			foreach ( grenvios_page_faqs( grenvios_current_slug() ) as $f ) {
				$default[] = array( 'q' => $f[0], 'a' => $f[1] );
			}
		} else {
			$defs    = grenvios_repeater_defaults( grenvios_current_slug() );
			$default = isset( $defs[ $key ] ) ? $defs[ $key ] : array();
		}
	}
	$pid = function_exists( 'grenvios_editor_post_id' ) ? grenvios_editor_post_id() : 0;
	if ( $pid ) {
		$raw = get_post_meta( $pid, 'grenvios_rep_' . $key, true );
		if ( is_array( $raw ) ) $default = $raw;   // guardado (aunque sea [] => vacío a propósito)
	}
	/* Filtro `grenvios_repeater_items`: permite reescribir los ítems al pintarlos.
	 * Lo usa inc/paises-home-hero.php para que el carrusel de la portada de cada
	 * ruta hable de SU país. Recibe lo guardado si lo hay, o lo que trae el tema. */
	return apply_filters( 'grenvios_repeater_items', $default, $key );
}

/* Resuelve un enlace de ítem: ruta relativa "/x/" => home_url; http/#/mailto/tel tal cual. */
function grenvios_rep_link( $url ) {
	// Filtro `grenvios_rep_link`: el multiidioma lo usa para apuntar el enlace a
	// la pagina del idioma activo (ver inc/i18n-links.php).
	$url = apply_filters( 'grenvios_rep_link', trim( (string) $url ) );
	if ( $url === '' ) return '';
	if ( preg_match( '~^(https?:|#|mailto:|tel:|//)~i', $url ) ) return $url;
	if ( $url[0] === '/' ) return grenvios_url_base() . $url;
	return $url;
}

/* ══════════════════════════════════════
   ESQUEMA DE REPEATERS POR PÁGINA
   (qué repeaters aparecen y qué subcampos tiene cada ítem)
══════════════════════════════════════ */
function grenvios_repeater_schema( $slug ) {
	$list = grenvios_repeater_schema_base( $slug );
	// FAQ editable en TODA página que tenga preguntas (se renderiza como sección propia).
	if ( function_exists( 'grenvios_page_faqs' ) && grenvios_page_faqs( $slug ) ) {
		$list[] = array(
			'key'        => 'page_faq',
			'section'    => 'faq',
			'label'      => '❓ Preguntas frecuentes',
			'sel'        => '.grenvios-faq-section',
			'item_label' => 'Pregunta',
			'add_label'  => 'Agregar pregunta',
			'fields'     => array(
				'q' => array( 'label' => 'Pregunta',  'type' => 'text' ),
				'a' => array( 'label' => 'Respuesta', 'type' => 'textarea' ),
			),
		);
	}
	return apply_filters( 'grenvios_repeater_schema', $list, $slug );
}

function grenvios_repeater_schema_base( $slug ) {

	/* ── Conjuntos de subcampos reutilizables ── */
	$service_fields = array(
		'img'   => array( 'label' => 'Imagen',        'type' => 'image',    'hint' => 'Foto de la tarjeta (si la dejas vacía usa la del diseño).' ),
		'icon'  => array( 'label' => 'Ícono',         'type' => 'text',     'hint' => 'Clase del ícono. Ej: logis logis-package · fa-solid fa-box' ),
		'title' => array( 'label' => 'Título',        'type' => 'text',     'hint' => 'Nombre del servicio' ),
		'text'  => array( 'label' => 'Descripción',   'type' => 'textarea', 'hint' => 'Texto corto de la tarjeta' ),
		'more'  => array( 'label' => 'Texto del enlace', 'type' => 'text',  'hint' => 'Ej: Leer más' ),
		'link'  => array( 'label' => 'Enlace',        'type' => 'text',     'hint' => 'Ruta interna (ej: /servicios/carga-internacional/) o URL completa.' ),
	);
	$testi_fields = array(
		'img'  => array( 'label' => 'Foto',     'type' => 'image',    'hint' => 'Foto del cliente (opcional).' ),
		'name' => array( 'label' => 'Nombre',   'type' => 'text',     'hint' => 'Nombre del cliente' ),
		'city' => array( 'label' => 'Ciudad',   'type' => 'text',     'hint' => 'Ej: Lima' ),
		'date' => array( 'label' => 'Fecha',    'type' => 'text',     'hint' => 'Estilo Google. Ej: hace 2 semanas' ),
		'text' => array( 'label' => 'Reseña',   'type' => 'textarea', 'hint' => 'El testimonio del cliente' ),
	);
	$card_fields = array(   // tarjeta ícono + título + texto
		'icon'  => array( 'label' => 'Ícono', 'type' => 'text',     'hint' => 'Clase del ícono. Ej: fa-solid fa-shield-halved' ),
		'title' => array( 'label' => 'Título', 'type' => 'text' ),
		'text'  => array( 'label' => 'Texto',  'type' => 'textarea' ),
	);
	$card_html_fields = array(
		'icon'  => array( 'label' => 'Ícono', 'type' => 'text' ),
		'title' => array( 'label' => 'Título', 'type' => 'text' ),
		'text'  => array( 'label' => 'Texto',  'type' => 'html', 'hint' => 'Admite enlaces y negrita.' ),
	);
	$step_fields = array(   // tarjeta numerada (sin ícono editable)
		'title' => array( 'label' => 'Título', 'type' => 'text' ),
		'text'  => array( 'label' => 'Texto',  'type' => 'textarea' ),
	);
	$li_icon_fields = array( // <li> de lista con ícono editable
		'icon' => array( 'label' => 'Ícono', 'type' => 'text', 'hint' => 'Ej: fa-solid fa-plane' ),
		'text' => array( 'label' => 'Texto', 'type' => 'text' ),
	);
	$li_fields = array( 'text' => array( 'label' => 'Texto', 'type' => 'text' ) ); // <li> ícono fijo
	$li_html_fields = array( 'text' => array( 'label' => 'Texto', 'type' => 'html', 'hint' => 'Admite enlaces.' ) );
	$option_fields = array(
		'label' => array( 'label' => 'Texto visible', 'type' => 'text' ),
		'value' => array( 'label' => 'Valor que se envía', 'type' => 'text', 'hint' => 'Suele ser igual al texto visible.' ),
	);

	switch ( $slug ) {
		case 'envios-para-empresas':
			return array(
				array( 'key' => 'emp_soluciones', 'section' => 'soluciones', 'label' => '🏢 Qué resolvemos', 'sel' => '.srv-section', 'item_label' => 'Solución', 'add_label' => 'Agregar solución', 'fields' => $card_fields,
					'tpl' => '<div class="col-lg-3 col-md-6"><div class="promo-item"><i class="%icon%"></i><div class="promo-content"><h3>%title%</h3><p>%text%</p></div></div></div>' ),
				array( 'key' => 'emp_cuenta', 'section' => 'cuenta', 'label' => '📋 Ventajas de la cuenta corporativa', 'sel' => '.service-features', 'item_label' => 'Ventaja', 'add_label' => 'Agregar ventaja', 'fields' => $li_icon_fields,
					'tpl' => '<li><i class="%icon%"></i>%text%</li>' ),
				array( 'key' => 'emp_sectores', 'section' => 'sectores', 'label' => '🏭 Sectores que atendemos', 'sel' => '.service-details', 'item_label' => 'Sector', 'add_label' => 'Agregar sector', 'fields' => $li_icon_fields,
					'tpl' => '<li><i class="%icon%"></i>%text%</li>' ),
				array( 'key' => 'emp_pasos', 'section' => 'pasos', 'label' => '🔢 Cómo empezamos (pasos)', 'sel' => '.srv-trio', 'item_label' => 'Paso', 'add_label' => 'Agregar paso', 'fields' => $step_fields,
					'tpl' => '<div class="col-lg-4 col-md-6"><div class="rast-state-card"><h3>%title%</h3><p>%text%</p></div></div>' ),
				array( 'key' => 'emp_docs', 'section' => 'docs', 'label' => '🗂️ Documentos que revisamos', 'sel' => '.service-details', 'item_label' => 'Documento', 'add_label' => 'Agregar documento', 'fields' => $li_icon_fields,
					'tpl' => '<li><i class="%icon%"></i>%text%</li>' ),
			);

		case 'home':
			return array(
				array( 'key' => 'home_slides', 'section' => 'hero', 'label' => '🖼️ Carrusel (diapositivas)', 'sel' => '.slider-section', 'item_label' => 'Diapositiva', 'add_label' => 'Agregar diapositiva',
					'fields' => array(
						'img'     => array( 'label' => 'Imagen de fondo', 'type' => 'image' ),
						'img2'    => array( 'label' => 'Imagen 2 (contenedor)', 'type' => 'image' ),
						'img3'    => array( 'label' => 'Imagen 3 (camión)', 'type' => 'image' ),
						'tagline' => array( 'label' => 'Frase superior', 'type' => 'text' ),
						'title'   => array( 'label' => 'Título', 'type' => 'html', 'hint' => 'Admite <br> y <span>…</span>' ),
						'text'    => array( 'label' => 'Descripción', 'type' => 'html', 'hint' => 'Admite <br>' ),
						'btn'     => array( 'label' => 'Texto del botón', 'type' => 'text' ),
						'link'    => array( 'label' => 'Enlace del botón', 'type' => 'text', 'hint' => 'Ej: /cotizar/' ),
						'btn2'    => array( 'label' => 'Enlace secundario · Texto', 'type' => 'text' ),
						'btn2link'=> array( 'label' => 'Enlace secundario · URL', 'type' => 'text', 'hint' => 'Ej: /destinos/' ),
						'chip1'   => array( 'label' => 'Garantía 1 (bajo el texto)', 'type' => 'text' ),
						'chip2'   => array( 'label' => 'Garantía 2', 'type' => 'text' ),
						'chip3'   => array( 'label' => 'Garantía 3', 'type' => 'text' ),
						'proof_n' => array( 'label' => 'Prueba social · Cifra', 'type' => 'text', 'hint' => 'Ej: +10.000 envíos gestionados' ),
						'proof_t' => array( 'label' => 'Prueba social · Texto', 'type' => 'text', 'hint' => 'Ej: por personas y empresas' ),
					),
					'tpl' => '<div class="swiper-slide"><div class="slider-img-wrap"><div class="slider-img"><img src="%img%" alt="%tagline%"></div><div class="slider-road"></div><div class="corner-shape" data-animation="fade-in-left" data-duration="1.5s" data-delay="0.3s"></div><div class="container-img" style="background-image:url(%img2%)" data-animation="fade-in-top" data-duration="1.5s" data-delay="0.8s"></div><div class="slider-truck" style="background-image:url(%img3%)" data-animation="truck-animation-right" data-duration="1.5s" data-delay="0.5s"></div></div><div class="slider-content-wrap d-flex align-items-center text-left"><div class="container"><div class="slider-content"><div class="slider-caption medium"><div class="inner-layer"><div data-animation="fade-in-bottom" data-delay="0.3s">%tagline%</div></div></div><div class="slider-caption big"><div class="inner-layer"><div data-animation="fade-in-bottom" data-delay="0.5s">%title%</div></div></div><div class="slider-caption small"><div class="inner-layer"><div data-animation="fade-in-bottom" data-delay="0.7s" data-duration="1s">%text%</div></div></div><ul class="slider-chips" data-animation="fade-in-bottom" data-delay="0.8s"><li><i class="fa-solid fa-shield-halved"></i>%chip1%</li><li><i class="fa-solid fa-globe"></i>%chip2%</li><li><i class="fa-solid fa-user-group"></i>%chip3%</li></ul><div class="slider-btn"><a href="%link%" class="default-btn" data-animation="fade-in-bottom" data-delay="0.9s">%btn% <i class="fa-solid fa-arrow-right"></i></a><a href="%btn2link%" class="slider-link" data-animation="fade-in-bottom" data-delay="1s">%btn2%</a></div><div class="slider-proof" data-animation="fade-in-bottom" data-delay="1.1s"><span class="slider-proof-av"><img src="ARKDINURI/assets/img/team-1.jpg" alt="" loading="lazy"><img src="ARKDINURI/assets/img/team-2.jpg" alt="" loading="lazy"><img src="ARKDINURI/assets/img/team-3.jpg" alt="" loading="lazy"></span><span class="slider-proof-tx"><strong>%proof_n%</strong>%proof_t%</span></div></div></div></div></div>' ),
				array( 'key' => 'home_hq_tipos', 'section' => 'heroform', 'label' => '📦 Cotizador del hero · Tipo de envío', 'sel' => '#hq-type', 'item_label' => 'Tipo', 'add_label' => 'Agregar tipo', 'fields' => $option_fields,
					'tpl' => '<option value="%value%">%label%</option>' ),
				array( 'key' => 'home_hq_origenes', 'section' => 'heroform', 'label' => '📍 Cotizador del hero · Desde (orígenes)', 'sel' => '#hq-origin', 'item_label' => 'Origen', 'add_label' => 'Agregar origen', 'fields' => $option_fields,
					'tpl' => '<option value="%value%">%label%</option>' ),
				array( 'key' => 'home_services',     'section' => 'services',     'label' => '🗂️ Tarjetas de Servicios', 'sel' => '.service-section',     'item_label' => 'Servicio', 'add_label' => 'Agregar servicio', 'fields' => $service_fields ),
				array( 'key' => 'home_testimonials', 'section' => 'testimonials', 'label' => '⭐ Testimonios',            'sel' => '.testimonial-section', 'item_label' => 'Reseña',   'add_label' => 'Agregar reseña',  'fields' => $testi_fields ),
				array( 'key' => 'home_qq_countries', 'section' => 'quickquote', 'label' => '🌎 Cotizador · Países', 'sel' => '#cotiza-rapido', 'item_label' => 'País', 'add_label' => 'Agregar país', 'fields' => $option_fields,
					'tpl' => '<option value="%value%">%label%</option>' ),
				array( 'key' => 'home_qq_types', 'section' => 'quickquote', 'label' => '📦 Cotizador · Tipos de envío', 'sel' => '#cotiza-rapido', 'item_label' => 'Tipo', 'add_label' => 'Agregar tipo', 'fields' => $option_fields,
					'tpl' => '<option value="%value%">%label%</option>' ),
			);

		case 'destinos':
			return array(
				array( 'key' => 'dest_cards', 'section' => 'intro', 'label' => '🗂️ Tarjetas de países', 'sel' => '.projects-section', 'item_label' => 'País', 'add_label' => 'Agregar país',
					'fields' => array(
						'img'      => array( 'label' => 'Imagen', 'type' => 'image' ),
						'icon'     => array( 'label' => 'Ícono', 'type' => 'text', 'hint' => 'Ej: logis logis-airplane-flying · logis logis-truck-2' ),
						'category' => array( 'label' => 'Modalidad (etiqueta)', 'type' => 'text', 'hint' => 'Ej: Aéreo y terrestre' ),
						'name'     => array( 'label' => 'País', 'type' => 'text' ),
						'time'     => array( 'label' => 'Tiempo de entrega', 'type' => 'text' ),
						'link'     => array( 'label' => 'Enlace', 'type' => 'text', 'hint' => 'Ej: /destinos/ecuador/' ),
						'btn'      => array( 'label' => 'Texto del botón', 'type' => 'text' ),
					),
					/* Tarjeta sin foto: las imágenes de la vitrina eran de relleno y la
					 * tarjeta no decía nada útil. Ahora trae lo que se compara entre
					 * países —vía, plazo, entrega y ciudades—, sacado del gestor de
					 * destinos (inc/destinos.php → grenvios_repeater_extras). */
					'tpl' => '<div class="col-lg-4 col-md-6"><article class="gr-dcard"%_attrs%><span class="gr-dcard-iso" aria-hidden="true">%_iso%</span><div class="gr-dcard-head">%_flag%<span class="gr-dcard-mode">%_modo_ic% %category%</span></div><h3 class="gr-dcard-name"><a href="%link%">%name%</a></h3>%_chip%<ul class="gr-dcard-facts">%_facts%</ul>%_ciudades%<span class="gr-dcard-cta" aria-hidden="true">%btn% <i class="fa-solid fa-arrow-right"></i></span></article></div>' ),
				array( 'key' => 'dest_otros', 'section' => 'otros', 'label' => '🌐 Otros destinos (tabla)', 'sel' => '.destinos-table', 'item_label' => 'País', 'add_label' => 'Agregar país',
					'fields' => array(
						'icon'   => array( 'label' => 'Ícono', 'type' => 'text', 'hint' => 'Ej: logis logis-airplane-flying' ),
						'pais'   => array( 'label' => 'País', 'type' => 'text' ),
						'tiempo' => array( 'label' => 'Tiempo estimado', 'type' => 'text' ),
						'link'   => array( 'label' => 'Enlace del botón', 'type' => 'text', 'hint' => 'Ej: /cotizar/' ),
						'btn'    => array( 'label' => 'Texto del botón', 'type' => 'text' ),
					),
					'tpl' => '<li class="gr-otro"><a href="%link%">%_flag%<span class="gr-otro-pais">%pais%</span><span class="gr-otro-tiempo"><i class="fa-regular fa-clock"></i> %tiempo%</span><span class="gr-otro-btn">%btn% <i class="fa-solid fa-arrow-right"></i></span></a></li>' ),
			);

		case 'nosotros':
			return array(
				array( 'key' => 'nos_valores', 'section' => 'valores', 'label' => '💎 Valores', 'sel' => '.blog-section.bg-grey', 'item_label' => 'Valor', 'add_label' => 'Agregar valor', 'fields' => $card_fields,
					'tpl' => '<div class="grenvios-info-card"><div class="info-icon"><i class="%icon%"></i></div><h3>%title%</h3><p>%text%</p></div>' ),
				array( 'key' => 'nos_cert', 'section' => 'certificaciones', 'label' => '📜 Respaldo institucional', 'sel' => '.grenvios-cert-list', 'item_label' => 'Entidad', 'add_label' => 'Agregar entidad', 'fields' => $li_fields,
					'tpl' => '<li><i class="fa-solid fa-certificate"></i>%text%</li>' ),
				array( 'key' => 'nos_porque', 'section' => 'porque', 'label' => '✅ ¿Por qué elegirnos?', 'sel' => '.service-section .promo-list', 'item_label' => 'Diferencial', 'add_label' => 'Agregar diferencial', 'fields' => $card_fields,
					'tpl' => '<li class="wow fade-in-bottom" data-wow-delay="%delay%"><i class="%icon%"></i><div class="promo-content"><h3>%title%</h3><p>%text%</p></div></li>' ),
			);

		case 'servicios':
			return array(
				array( 'key' => 'serv_cards', 'section' => 'services', 'label' => '🗂️ Tarjetas de Servicios', 'sel' => '.service-section', 'item_label' => 'Servicio', 'add_label' => 'Agregar servicio', 'fields' => $service_fields,
					'tpl' => '<div class="col-xl-3 col-md-6"><div class="service-item wow fade-in-bottom" data-wow-delay="%delay%"><div class="service-thumb"><img src="%img%" alt="%title%"></div><div class="service-content"><div class="service-icon"><i class="%icon%"></i></div><h3><a href="%link%">%title%</a></h3><p>%text%</p><a class="read-more" href="%link%">%more%</a></div></div></div>' ),
				array( 'key' => 'serv_promos', 'section' => 'cta', 'label' => '🏷️ Tarjetas inferiores (CTA)', 'sel' => '.promo-item-wrapper', 'item_label' => 'Tarjeta', 'add_label' => 'Agregar tarjeta', 'fields' => $card_fields,
					'tpl' => '<div class="col-lg-4"><div class="promo-item"><i class="%icon%"></i><div class="promo-content"><h3>%title%</h3><p>%text%</p></div></div></div>' ),
			);

		case 'envio-internacional-de-documentos':
			return array(
				array( 'key' => 'doc_docs', 'section' => 'documentos', 'label' => '📄 Documentos que puedes enviar', 'sel' => '.service-features', 'item_label' => 'Documento', 'add_label' => 'Agregar documento', 'fields' => $li_fields,
					'tpl' => '<li><i class="fa-solid fa-thumbs-up"></i>%text%</li>' ),
				array( 'key' => 'doc_tiempos', 'section' => 'tiempos', 'label' => '⏱️ Tiempos de entrega', 'sel' => '.service-features', 'item_label' => 'Tiempo', 'add_label' => 'Agregar tiempo', 'fields' => $li_fields,
					'tpl' => '<li><i class="fa-solid fa-thumbs-up"></i>%text%</li>' ),
			);

		case 'envio-internacional-de-paquetes':
			return array(
				array( 'key' => 'paq_modalidades', 'section' => 'modalidades', 'label' => '✈️ Aéreo vs Terrestre', 'sel' => '.service-features', 'item_label' => 'Punto', 'add_label' => 'Agregar punto', 'fields' => $li_icon_fields,
					'tpl' => '<li><i class="%icon%"></i>%text%</li>' ),
				array( 'key' => 'paq_tarifas', 'section' => 'tabla', 'label' => '💲 Tabla de impuestos por país', 'sel' => '.grenvios-rate-table', 'item_label' => 'País', 'add_label' => 'Agregar país',
					'fields' => array(
						'pais'     => array( 'label' => 'País', 'type' => 'text' ),
						'impuesto' => array( 'label' => 'Impuesto aprox.', 'type' => 'text' ),
						'tiempo'   => array( 'label' => 'Tiempo estimado', 'type' => 'text' ),
						'entrega'  => array( 'label' => 'Entrega', 'type' => 'text' ),
					),
					'tpl' => '<tr><td>%pais%</td><td>%impuesto%</td><td>%tiempo%</td><td>%entrega%</td></tr>' ),
				array( 'key' => 'paq_destinos', 'section' => 'destinos', 'label' => '📍 Destinos terrestres', 'sel' => '.service-details', 'item_label' => 'Destino', 'add_label' => 'Agregar destino', 'fields' => $li_html_fields,
					'tpl' => '<li><i class="fa-solid fa-location-dot"></i>%text%</li>' ),
			);

		case 'carga-internacional':
			return array(
				array( 'key' => 'carga_modalidades', 'section' => 'modalidades', 'label' => '🚚 Modalidades de carga', 'sel' => '.service-features', 'item_label' => 'Punto', 'add_label' => 'Agregar punto', 'fields' => $li_icon_fields,
					'tpl' => '<li><i class="%icon%"></i>%text%</li>' ),
				array( 'key' => 'carga_docs', 'section' => 'docs', 'label' => '🗂️ Documentación requerida', 'sel' => '.service-details', 'item_label' => 'Documento', 'add_label' => 'Agregar documento', 'fields' => $li_icon_fields,
					'tpl' => '<li><i class="%icon%"></i>%text%</li>' ),
			);

		case 'apostilla-y-traduccion':
			return array(
				array( 'key' => 'apos_docs', 'section' => 'docs', 'label' => '📄 Documentos que apostillamos', 'sel' => '.service-features', 'item_label' => 'Documento', 'add_label' => 'Agregar documento', 'fields' => $li_fields,
					'tpl' => '<li><i class="fa-solid fa-thumbs-up"></i>%text%</li>' ),
				array( 'key' => 'apos_trad', 'section' => 'trad', 'label' => '🌐 Traducción oficial', 'sel' => '.service-features', 'item_label' => 'Ítem', 'add_label' => 'Agregar ítem', 'fields' => $li_fields,
					'tpl' => '<li><i class="fa-solid fa-thumbs-up"></i>%text%</li>' ),
				array( 'key' => 'apos_how', 'section' => 'how', 'label' => '🔢 Cómo funciona (pasos)', 'sel' => '.service-details', 'item_label' => 'Paso', 'add_label' => 'Agregar paso', 'fields' => $li_fields,
					'tpl' => '<li><i class="fa-solid fa-check"></i>%text%</li>' ),
			);

		case 'cotizar':
			return array(
				array( 'key' => 'cot_countries', 'section' => 'form', 'label' => '🌎 Opciones · País de destino', 'sel' => '#q-country', 'item_label' => 'País', 'add_label' => 'Agregar país', 'fields' => $option_fields,
					'tpl' => '<option value="%value%">%label%</option>' ),
				array( 'key' => 'cot_types', 'section' => 'form', 'label' => '📦 Opciones · Tipo de envío', 'sel' => '#q-type', 'item_label' => 'Tipo', 'add_label' => 'Agregar tipo', 'fields' => $option_fields,
					'tpl' => '<option value="%value%">%label%</option>' ),
				array( 'key' => 'cot_steps', 'section' => 'steps', 'label' => '🔢 Pasos (3 pasos)', 'sel' => '.grenvios-info-grid', 'item_label' => 'Paso', 'add_label' => 'Agregar paso', 'fields' => $step_fields,
					'tpl' => '<div class="grenvios-info-card"><div class="info-icon"><i class="fa-solid fa-%n%"></i></div><h3>%title%</h3><p>%text%</p></div>' ),
			);

		case 'rastreo-de-envios':
			return array(
				array( 'key' => 'rast_info_cards', 'section' => 'info', 'label' => 'ℹ️ Tarjetas de información', 'sel' => '.grenvios-info-grid', 'item_label' => 'Tarjeta', 'add_label' => 'Agregar tarjeta', 'fields' => $card_fields,
					'tpl' => '<div class="grenvios-info-card"><div class="info-icon"><i class="%icon%"></i></div><h3>%title%</h3><p>%text%</p></div>' ),
				array( 'key' => 'rast_estados', 'section' => 'estados', 'label' => '📦 Estados del envío', 'sel' => '.process-section', 'item_label' => 'Estado', 'add_label' => 'Agregar estado', 'fields' => $card_fields,
					'tpl' => '<div class="col-lg-4 col-md-6"><div class="rast-state-card wow fade-in-bottom" data-wow-delay="%delay%"><span class="rast-state-ic"><i class="%icon%"></i></span><h3>%title%</h3><p>%text%</p></div></div>' ),
			);

		case 'contacto':
			return array(
				array( 'key' => 'cont_types', 'section' => 'form', 'label' => '📦 Opciones · Tipo de envío', 'sel' => '#shipping_type', 'item_label' => 'Tipo', 'add_label' => 'Agregar tipo', 'fields' => $option_fields,
					'tpl' => '<option value="%value%">%label%</option>' ),
				array( 'key' => 'cont_cards', 'section' => 'cards', 'label' => '🤝 Tarjetas de atención', 'sel' => '.blog-section.bg-grey', 'item_label' => 'Tarjeta', 'add_label' => 'Agregar tarjeta', 'fields' => $card_html_fields,
					'tpl' => '<div class="grenvios-info-card"><div class="info-icon"><i class="%icon%"></i></div><h3>%title%</h3><p>%text%</p></div>' ),
			);
	}
	return array();
}

/* ══════════════════════════════════════
   VALORES POR DEFECTO (migración del contenido actual del partial)
══════════════════════════════════════ */
function grenvios_repeater_defaults( $slug ) {
	$uri = untrailingslashit( get_template_directory_uri() );
	$img = function_exists( 'grenvios_img_url' ) ? 'grenvios_img_url' : null;
	$g = function ( $file ) use ( $uri, $img ) { return $img ? call_user_func( $img, $file ) : $uri . '/assets/img/' . $file; };

	switch ( $slug ) {
		case 'envios-para-empresas':
			return array(
				'emp_soluciones' => array(
					array( 'icon' => 'fa-solid fa-clock', 'title' => 'Envíos urgentes', 'text' => 'Repuestos, muestras y documentos que no pueden esperar: salida el mismo día por vía aérea.' ),
					array( 'icon' => 'fa-solid fa-boxes-stacked', 'title' => 'Envíos recurrentes', 'text' => 'Días fijos de recojo en tu almacén y una tarifa estable, sin cotizar cada despacho.' ),
					array( 'icon' => 'fa-solid fa-file-invoice-dollar', 'title' => 'Facturación ordenada', 'text' => 'Factura electrónica a nombre de la empresa y consolidado mensual de todos los envíos.' ),
					array( 'icon' => 'fa-solid fa-clipboard-check', 'title' => 'Aduana sin sorpresas', 'text' => 'Revisamos la documentación y las restricciones del país de destino antes de despachar.' ),
				),
				'emp_cuenta' => array(
					array( 'icon' => 'fa-solid fa-thumbs-up', 'text' => 'Tarifa preferencial según tu volumen y frecuencia mensual' ),
					array( 'icon' => 'fa-solid fa-thumbs-up', 'text' => 'Un asesor asignado que conoce tus rutas y tus tiempos' ),
					array( 'icon' => 'fa-solid fa-thumbs-up', 'text' => 'Recojo programado en tu local, almacén o proveedor en Lima' ),
					array( 'icon' => 'fa-solid fa-thumbs-up', 'text' => 'Factura electrónica y trabajo contra orden de compra' ),
					array( 'icon' => 'fa-solid fa-thumbs-up', 'text' => 'Consolidado mensual de envíos para tu control interno' ),
				),
				'emp_sectores' => array(
					array( 'icon' => 'fa-solid fa-shirt', 'text' => 'Textil y confecciones: muestras y pedidos a Estados Unidos y Europa' ),
					array( 'icon' => 'fa-solid fa-gear', 'text' => 'Industria y minería: repuestos urgentes y piezas de reemplazo' ),
					array( 'icon' => 'fa-solid fa-flask', 'text' => 'Laboratorios y salud: muestras y documentación técnica' ),
					array( 'icon' => 'fa-solid fa-laptop', 'text' => 'Comercio electrónico: envíos a clientes finales en la región' ),
					array( 'icon' => 'fa-solid fa-scale-balanced', 'text' => 'Estudios legales y notarías: documentos apostillados al extranjero' ),
				),
				'emp_pasos' => array(
					array( 'title' => '1. Nos cuentas tu operación', 'text' => 'Cuántos envíos al mes, a qué países, con qué peso y qué urgencia. Con esos datos ya podemos trabajar.' ),
					array( 'title' => '2. Te enviamos una propuesta', 'text' => 'Tarifa por destino y modalidad, plazos comprometidos y condiciones de recojo. Sin compromiso.' ),
					array( 'title' => '3. Abrimos la cuenta y empezamos', 'text' => 'Solo con el RUC y un contacto responsable. Al mes siguiente ajustamos la tarifa con tus envíos reales.' ),
				),
				'emp_docs' => array(
					array( 'icon' => 'fa-solid fa-file-invoice', 'text' => 'Factura comercial con valor declarado correcto' ),
					array( 'icon' => 'fa-solid fa-file-lines', 'text' => 'Ficha técnica del producto, cuando la aduana la exige' ),
					array( 'icon' => 'fa-solid fa-barcode', 'text' => 'Partida arancelaria del producto' ),
					array( 'icon' => 'fa-solid fa-triangle-exclamation', 'text' => 'Revisión de productos restringidos en el país de destino' ),
					array( 'icon' => 'fa-solid fa-money-bill-wave', 'text' => 'Límites de valor libres de impuestos del destino' ),
				),
			);

		case 'home':
			return array(
				'home_slides' => array(
					array( 'img' => $g( 'hero-home.jpg' ), 'img2' => $g( 'slider-container.png' ), 'img3' => $g( 'slider-truck.png' ), 'tagline' => 'Courier internacional · {{origen_pais}}', 'title' => 'Envíos internacionales <br>desde {{origen_ciudad}} a más de <br><span>30 países</span>', 'text' => 'Envía documentos, paquetes y carga desde {{origen_pais}} con atención personalizada y seguimiento durante todo el recorrido. <br>Te ayudamos a elegir la mejor opción para que llegue seguro, a tiempo y sin complicaciones.', 'btn' => 'Cotizar mi envío', 'link' => '/cotizar/', 'btn2' => 'Ver destinos', 'btn2link' => '/destinos/', 'chip1' => 'Envíos seguros', 'chip2' => 'Más de 30 destinos', 'chip3' => 'Soluciones para personas y empresas', 'proof_n' => '+10.000 envíos gestionados', 'proof_t' => 'por personas y empresas' ),
					array( 'img' => $g( 'hero-home.jpg' ), 'img2' => $g( 'slider-container.png' ), 'img3' => $g( 'slider-truck.png' ), 'tagline' => 'Tu camino confiable hacia el mundo', 'title' => '¡Documentos, paquetes y <br>carga al <span>mundo!</span>', 'text' => 'Llevamos tus envíos a más de 30 países en América, <br>Europa, Asia y África.', 'btn' => 'Cotiza tu envío', 'link' => '/cotizar/', 'btn2' => 'Ver destinos', 'btn2link' => '/destinos/', 'chip1' => 'Envíos seguros', 'chip2' => 'Más de 30 destinos', 'chip3' => 'Soluciones para personas y empresas', 'proof_n' => '+10.000 envíos gestionados', 'proof_t' => 'por personas y empresas' ),
					array( 'img' => $g( 'hero-home.jpg' ), 'img2' => $g( 'slider-container.png' ), 'img3' => $g( 'slider-truck.png' ), 'tagline' => 'Tu camino confiable hacia el mundo', 'title' => '¡También apostillamos <br>y <span>traducimos!</span>', 'text' => 'No solo movemos tu documento: lo legalizamos para el mundo. <br>Apostilla y traducción profesional al inglés e italiano.', 'btn' => 'Conoce el servicio', 'link' => '/servicios/apostilla-y-traduccion/', 'btn2' => 'Ver destinos', 'btn2link' => '/destinos/', 'chip1' => 'Envíos seguros', 'chip2' => 'Más de 30 destinos', 'chip3' => 'Soluciones para personas y empresas', 'proof_n' => '+10.000 envíos gestionados', 'proof_t' => 'por personas y empresas' ),
				),
				'home_services' => array(
					array( 'img' => $g( 'post-1.jpg' ), 'icon' => 'logis logis-package',        'title' => 'Envío de Documentos',  'text' => 'Envía contratos, títulos y documentos importantes al extranjero de forma rápida y segura, con seguimiento incluido.', 'more' => 'Leer más', 'link' => '/servicios/envio-internacional-de-documentos/' ),
					array( 'img' => $g( 'post-2.jpg' ), 'icon' => 'logis logis-airplane-flying', 'title' => 'Envío de Paquetes',    'text' => 'Encomiendas, regalos y productos hacia más de 30 países, con embalaje seguro y opciones aéreas y terrestres.',          'more' => 'Leer más', 'link' => '/servicios/envio-internacional-de-paquetes/' ),
					array( 'img' => $g( 'post-3.jpg' ), 'icon' => 'logis logis-truck-2',         'title' => 'Carga Internacional',  'text' => 'Soluciones de carga aérea y terrestre para empresas y emprendedores, con asesoría aduanera y logística completa.',        'more' => 'Leer más', 'link' => '/servicios/carga-internacional/' ),
				),
				'home_testimonials' => array(
					array( 'img' => $g( 'team-1.jpg' ), 'name' => 'Carla Mendoza',   'city' => 'Lima',     'date' => 'hace 2 semanas', 'text' => 'Envié documentos a Estados Unidos y llegaron antes de lo previsto. El rastreo me dio mucha tranquilidad y la atención fue excelente. Totalmente recomendados.' ),
					array( 'img' => $g( 'team-2.jpg' ), 'name' => 'Javier Rojas',    'city' => 'Arequipa', 'date' => 'hace 1 mes', 'text' => 'Mando paquetes a Chile para mi negocio y siempre llegan bien embalados y a tiempo. El recojo a domicilio me ahorra muchísimo tiempo. Un servicio muy confiable.' ),
					array( 'img' => $g( 'team-3.jpg' ), 'name' => 'Lucía Fernández', 'city' => 'Trujillo', 'date' => 'hace 3 meses', 'text' => 'Envié un paquete a mi familia en España y la asesoría sobre aduanas fue clave. Todo el proceso fue claro y el envío llegó en perfecto estado. Muy agradecida.' ),
				),
				'home_hq_tipos' => array(
					array( 'label' => 'Documentos', 'value' => 'Documentos' ),
					array( 'label' => 'Paquete', 'value' => 'Paquete' ),
					array( 'label' => 'Carga', 'value' => 'Carga' ),
					array( 'label' => 'Mudanza o equipaje', 'value' => 'Mudanza o equipaje' ),
				),
				'home_hq_origenes' => array(
					array( 'label' => '{{origen_ciudad}}, {{origen_pais}}', 'value' => '{{origen_ciudad}}, {{origen_pais}}' ),
					array( 'label' => 'Otra ciudad del {{origen_pais}}', 'value' => 'Otra ciudad del {{origen_pais}}' ),
				),
				'home_qq_countries' => array(
					array( 'label' => 'Ecuador', 'value' => 'Ecuador' ), array( 'label' => 'Colombia', 'value' => 'Colombia' ),
					array( 'label' => 'Chile', 'value' => 'Chile' ), array( 'label' => 'Bolivia', 'value' => 'Bolivia' ),
					array( 'label' => 'Argentina', 'value' => 'Argentina' ), array( 'label' => 'Estados Unidos', 'value' => 'Estados Unidos' ),
					array( 'label' => 'España', 'value' => 'España' ), array( 'label' => 'Venezuela', 'value' => 'Venezuela' ),
					array( 'label' => 'Cuba', 'value' => 'Cuba' ), array( 'label' => 'Otro', 'value' => 'Otro' ),
				),
				'home_qq_types' => array(
					array( 'label' => 'Documento', 'value' => 'Documento' ),
					array( 'label' => 'Paquete', 'value' => 'Paquete' ),
					array( 'label' => 'Carga', 'value' => 'Carga' ),
				),
			);

		case 'destinos':
			return array(
				'dest_cards' => array(
					array( 'img' => $g( 'content-bg-1.jpg' ), 'icon' => 'logis logis-airplane-flying', 'category' => 'Aéreo y terrestre', 'name' => 'Ecuador', 'time' => 'Entrega estimada: 8-10 días', 'link' => '/destinos/ecuador/', 'btn' => 'Ver condiciones' ),
					array( 'img' => $g( 'content-bg-2.jpg' ), 'icon' => 'logis logis-airplane-flying', 'category' => 'Aéreo y terrestre', 'name' => 'Colombia', 'time' => 'Entrega estimada: 10-15 días', 'link' => '/destinos/colombia/', 'btn' => 'Ver condiciones' ),
					array( 'img' => $g( 'content-bg-3.jpg' ), 'icon' => 'logis logis-truck-2', 'category' => 'Aéreo y terrestre (a domicilio)', 'name' => 'Chile', 'time' => 'Entrega estimada: 10-20 días', 'link' => '/destinos/chile/', 'btn' => 'Ver condiciones' ),
					array( 'img' => $g( 'content-bg-4.jpg' ), 'icon' => 'logis logis-airplane-flying', 'category' => 'Aéreo y terrestre', 'name' => 'Bolivia', 'time' => 'Entrega estimada: 10-15 días', 'link' => '/destinos/bolivia/', 'btn' => 'Ver condiciones' ),
					array( 'img' => $g( 'content-bg-5.jpg' ), 'icon' => 'logis logis-airplane-flying', 'category' => 'Aéreo y terrestre', 'name' => 'Argentina', 'time' => 'Entrega estimada: 10-20 días', 'link' => '/destinos/argentina/', 'btn' => 'Ver condiciones' ),
					array( 'img' => $g( 'content-bg-6.jpg' ), 'icon' => 'logis logis-airplane-flying', 'category' => 'Aéreo', 'name' => 'Estados Unidos', 'time' => 'Entrega estimada: 4-6 días', 'link' => '/destinos/estados-unidos/', 'btn' => 'Ver condiciones' ),
					array( 'img' => $g( 'content-bg-7.jpg' ), 'icon' => 'logis logis-airplane-flying', 'category' => 'Aéreo', 'name' => 'España', 'time' => 'Entrega estimada: 4-7 días', 'link' => '/destinos/espana/', 'btn' => 'Ver condiciones' ),
					array( 'img' => $g( 'content-bg-8.jpg' ), 'icon' => 'logis logis-airplane-flying', 'category' => 'Aéreo', 'name' => 'Venezuela', 'time' => 'Entrega estimada: 15 días', 'link' => '/destinos/venezuela/', 'btn' => 'Ver condiciones' ),
					array( 'img' => $g( 'content-bg-1.jpg' ), 'icon' => 'logis logis-airplane-flying', 'category' => 'Aéreo', 'name' => 'Cuba', 'time' => 'Entrega estimada: 14 días', 'link' => '/destinos/cuba/', 'btn' => 'Ver condiciones' ),
				),
				'dest_otros' => array(
					array( 'icon' => 'logis logis-airplane-flying', 'pais' => 'Brasil', 'tiempo' => '12-18 días', 'link' => '/cotizar/', 'btn' => 'Cotizar' ),
					array( 'icon' => 'logis logis-airplane-flying', 'pais' => 'Panamá', 'tiempo' => '5-8 días', 'link' => '/cotizar/', 'btn' => 'Cotizar' ),
					array( 'icon' => 'logis logis-airplane-flying', 'pais' => 'Costa Rica', 'tiempo' => '6-9 días', 'link' => '/cotizar/', 'btn' => 'Cotizar' ),
					array( 'icon' => 'logis logis-airplane-flying', 'pais' => 'Puerto Rico', 'tiempo' => '7-10 días', 'link' => '/cotizar/', 'btn' => 'Cotizar' ),
					array( 'icon' => 'logis logis-airplane-flying', 'pais' => 'Uruguay', 'tiempo' => '12-20 días', 'link' => '/cotizar/', 'btn' => 'Cotizar' ),
					array( 'icon' => 'logis logis-airplane-flying', 'pais' => 'Paraguay', 'tiempo' => '12-18 días', 'link' => '/cotizar/', 'btn' => 'Cotizar' ),
					array( 'icon' => 'logis logis-airplane-flying', 'pais' => 'Canadá', 'tiempo' => '5-8 días', 'link' => '/cotizar/', 'btn' => 'Cotizar' ),
					array( 'icon' => 'logis logis-airplane-flying', 'pais' => 'México', 'tiempo' => '6-9 días', 'link' => '/cotizar/', 'btn' => 'Cotizar' ),
					array( 'icon' => 'logis logis-airplane-flying', 'pais' => 'Italia', 'tiempo' => '5-8 días', 'link' => '/cotizar/', 'btn' => 'Cotizar' ),
					array( 'icon' => 'logis logis-airplane-flying', 'pais' => 'Francia', 'tiempo' => '5-8 días', 'link' => '/cotizar/', 'btn' => 'Cotizar' ),
					array( 'icon' => 'logis logis-airplane-flying', 'pais' => 'Alemania', 'tiempo' => '5-8 días', 'link' => '/cotizar/', 'btn' => 'Cotizar' ),
					array( 'icon' => 'logis logis-airplane-flying', 'pais' => 'China', 'tiempo' => '7-12 días', 'link' => '/cotizar/', 'btn' => 'Cotizar' ),
					array( 'icon' => 'logis logis-airplane-flying', 'pais' => 'Japón', 'tiempo' => '7-12 días', 'link' => '/cotizar/', 'btn' => 'Cotizar' ),
					array( 'icon' => 'logis logis-airplane-flying', 'pais' => 'Australia', 'tiempo' => '8-14 días', 'link' => '/cotizar/', 'btn' => 'Cotizar' ),
				),
			);

		case 'nosotros':
			return array(
				'nos_valores' => array(
					array( 'icon' => 'fa-solid fa-shield-halved',   'title' => 'Confianza y seguridad',        'text' => 'Trabajamos con los más altos estándares para que cada paquete, carga o documento llegue a su destino intacto y seguro.' ),
					array( 'icon' => 'fa-solid fa-clock',           'title' => 'Eficiencia y puntualidad',     'text' => 'Optimizamos nuestros procesos logísticos y de gestión para garantizar entregas y trámites en los plazos acordados.' ),
					array( 'icon' => 'fa-solid fa-layer-group',     'title' => 'Integralidad',                 'text' => 'Desde el transporte hasta la traducción y el apostillado, resolvemos todas tus necesidades internacionales en un solo lugar.' ),
					array( 'icon' => 'fa-solid fa-bullseye-pointer','title' => 'Precisión y profesionalismo',  'text' => 'En traducciones y legalización cada detalle cuenta: nos comprometemos con la exactitud y la excelencia en cada trámite.' ),
					array( 'icon' => 'fa-solid fa-earth-americas',  'title' => 'Conectividad sin fronteras',   'text' => 'Trabajamos con mentalidad global para unir culturas, mercados y personas en América, Europa y Asia.' ),
					array( 'icon' => 'fa-solid fa-handshake-angle', 'title' => 'Compromiso con el cliente',    'text' => 'Escuchamos tus necesidades y nos adaptamos para brindar un servicio humano, cercano y personalizado.' ),
				),
				'nos_cert' => array(
					array( 'text' => 'Colegio de Traductores del Perú' ),
					array( 'text' => 'Ministerio de Relaciones Exteriores (MRE)' ),
					array( 'text' => 'Cámara de Comercio' ),
					array( 'text' => 'Ministerio de Educación' ),
					array( 'text' => 'Ministerio de Transportes y Comunicaciones' ),
				),
				'nos_porque' => array(
					array( 'icon' => 'logis logis-truck-2',         'title' => 'Vía terrestre a países vecinos', 'text' => 'Una alternativa más económica que los grandes couriers para enviar a Ecuador, Colombia y Chile.' ),
					array( 'icon' => 'logis logis-loader',          'title' => 'Recojo a domicilio en Lima',     'text' => 'Pasamos por tu paquete donde estés en Lima para que tú no tengas que moverte.' ),
					array( 'icon' => 'logis logis-airplane-flying', 'title' => 'Seguimiento de tu envío',        'text' => 'Sigue tu envío en cada etapa con seguimiento por número de guía con apoyo de un asesor.' ),
					array( 'icon' => 'logis logis-railway',         'title' => 'Asesoría en aduanas y embalaje', 'text' => 'Te orientamos en la documentación y el embalaje correcto para evitar demoras.' ),
				),
			);

		case 'servicios':
			return array(
				'serv_cards' => array(
					array( 'img' => $g( 'post-1.jpg' ), 'icon' => 'logis logis-airplane-flying', 'title' => 'Envío de Documentos',  'text' => 'Documentos legales, títulos, DNI y pasaportes con servicio puerta a puerta. Entrega en 4 a 7 días hábiles según destino.', 'more' => 'Leer más', 'link' => '/servicios/envio-internacional-de-documentos/' ),
					array( 'img' => $g( 'post-2.jpg' ), 'icon' => 'logis logis-loader',          'title' => 'Envío de Paquetes',    'text' => 'Paquetes, equipaje y compras por vía aérea y terrestre. El cobro se calcula por peso o por volumen, según convenga.',        'more' => 'Leer más', 'link' => '/servicios/envio-internacional-de-paquetes/' ),
					array( 'img' => $g( 'post-3.jpg' ), 'icon' => 'logis logis-truck-2',         'title' => 'Carga Internacional',  'text' => 'Desde 20 kg hasta grandes volúmenes. Transporte aéreo y terrestre pensado para empresas y envíos comerciales.',              'more' => 'Leer más', 'link' => '/servicios/carga-internacional/' ),
					array( 'img' => $g( 'post-4.jpg' ), 'icon' => 'fa-solid fa-stamp',           'title' => 'Apostilla y Traducción','text' => 'Legalizamos y traducimos tus documentos para que sean válidos en el extranjero. Apostilla, traducción profesional y envío en un solo lugar.', 'more' => 'Leer más', 'link' => '/servicios/apostilla-y-traduccion/' ),
				),
				'serv_promos' => array(
					array( 'icon' => 'logis logis-airplane-flying', 'title' => 'Envío de Documentos',  'text' => 'Servicio puerta a puerta en 4 a 7 días hábiles.' ),
					array( 'icon' => 'logis logis-loader',          'title' => 'Envío de Paquetes',    'text' => 'Aéreo y terrestre, cobro por peso o volumen.' ),
					array( 'icon' => 'logis logis-truck-2',         'title' => 'Carga Internacional',  'text' => 'Desde 20 kg hasta grandes volúmenes.' ),
				),
			);

		case 'envio-internacional-de-documentos':
			return array(
				'doc_docs' => array(
					array( 'text' => 'Documentos legales y notariales' ),
					array( 'text' => 'Títulos y certificados académicos' ),
					array( 'text' => 'DNI y pasaportes' ),
					array( 'text' => 'Contratos y poderes' ),
					array( 'text' => 'Expedientes y trámites' ),
					array( 'text' => 'Correspondencia oficial' ),
				),
				'doc_tiempos' => array(
					array( 'text' => 'América: 4 a 6 días hábiles' ),
					array( 'text' => 'Europa: 4 a 6 días hábiles' ),
					array( 'text' => 'Asia: 6 a 7 días hábiles' ),
					array( 'text' => 'Entrega segura puerta a puerta' ),
				),
			);

		case 'envio-internacional-de-paquetes':
			return array(
				'paq_modalidades' => array(
					array( 'icon' => 'fa-solid fa-plane', 'text' => 'Aéreo: la opción más rápida, ideal para envíos urgentes a cualquier continente' ),
					array( 'icon' => 'fa-solid fa-plane', 'text' => 'Aéreo: recomendado para destinos lejanos como Europa, Asia y Norteamérica' ),
					array( 'icon' => 'fa-solid fa-truck', 'text' => 'Terrestre: la alternativa más económica para países vecinos' ),
					array( 'icon' => 'fa-solid fa-truck', 'text' => 'Terrestre: ideal para envíos a Sudamérica con mayor volumen' ),
				),
				'paq_tarifas' => array(
					array( 'pais' => 'Colombia',  'impuesto' => '20 %',   'tiempo' => '10–15 días hábiles', 'entrega' => 'Agencia local' ),
					array( 'pais' => 'Ecuador',   'impuesto' => '18 %',   'tiempo' => '8–10 días hábiles',  'entrega' => 'Agencia local' ),
					array( 'pais' => 'Chile',     'impuesto' => '29,5 %', 'tiempo' => '10–20 días hábiles', 'entrega' => 'A domicilio' ),
					array( 'pais' => 'Bolivia',   'impuesto' => '15 %',   'tiempo' => '10–15 días hábiles', 'entrega' => 'Agencia local' ),
					array( 'pais' => 'Argentina', 'impuesto' => '20 %',   'tiempo' => '10–20 días hábiles', 'entrega' => 'Agencia local' ),
				),
				'paq_destinos' => array(
					array( 'text' => 'Envíos a <a href="' . grenvios_url_base() . '/destinos/ecuador/">Ecuador</a> por vía terrestre' ),
					array( 'text' => 'Envíos a <a href="' . grenvios_url_base() . '/destinos/colombia/">Colombia</a> con tarifas competitivas' ),
					array( 'text' => 'Envíos a <a href="' . grenvios_url_base() . '/destinos/chile/">Chile</a>, rápidos y económicos' ),
				),
			);

		case 'carga-internacional':
			return array(
				'carga_modalidades' => array(
					array( 'icon' => 'fa-solid fa-plane', 'text' => 'Carga aérea desde 100 kg, desglosable según tu necesidad' ),
					array( 'icon' => 'fa-solid fa-plane', 'text' => 'Ideal para mercancía urgente o de alto valor' ),
					array( 'icon' => 'fa-solid fa-truck', 'text' => 'Carga terrestre de 500 a 800 kg' ),
					array( 'icon' => 'fa-solid fa-truck', 'text' => 'Opción económica para envíos de gran volumen a la región' ),
				),
				'carga_docs' => array(
					array( 'icon' => 'fa-solid fa-file-invoice', 'text' => 'Boleta o factura de la mercancía' ),
					array( 'icon' => 'fa-solid fa-file-lines',   'text' => 'Ficha técnica del producto' ),
				),
			);

		case 'apostilla-y-traduccion':
			return array(
				'apos_docs' => array(
					array( 'text' => 'Títulos y certificados académicos' ),
					array( 'text' => 'Partidas de nacimiento, matrimonio y defunción' ),
					array( 'text' => 'Poderes y documentos notariales' ),
					array( 'text' => 'Antecedentes penales y policiales' ),
					array( 'text' => 'Certificados de estudios y notas' ),
					array( 'text' => 'Documentos comerciales y constancias' ),
				),
				'apos_trad' => array(
					array( 'text' => 'Traducción profesional al inglés' ),
					array( 'text' => 'Traducción profesional al italiano' ),
					array( 'text' => 'Respeto del formato original' ),
					array( 'text' => 'Documentación lista para su trámite' ),
				),
				'apos_how' => array(
					array( 'text' => 'Nos cuentas qué documento tienes y en qué país lo vas a usar.' ),
					array( 'text' => 'Verificamos si requiere apostilla, traducción o ambas.' ),
					array( 'text' => 'Gestionamos la legalización y/o traducción profesional.' ),
					array( 'text' => 'Si lo deseas, lo enviamos a tu destino con nuestro servicio internacional.' ),
				),
			);

		case 'cotizar':
			return array(
				'cot_countries' => array(
					array( 'label' => 'Ecuador', 'value' => 'Ecuador' ),
					array( 'label' => 'Colombia', 'value' => 'Colombia' ),
					array( 'label' => 'Chile', 'value' => 'Chile' ),
					array( 'label' => 'Bolivia', 'value' => 'Bolivia' ),
					array( 'label' => 'Argentina', 'value' => 'Argentina' ),
					array( 'label' => 'Estados Unidos', 'value' => 'Estados Unidos' ),
					array( 'label' => 'España', 'value' => 'España' ),
					array( 'label' => 'Venezuela', 'value' => 'Venezuela' ),
					array( 'label' => 'Cuba', 'value' => 'Cuba' ),
					array( 'label' => 'Otro', 'value' => 'Otro' ),
				),
				'cot_types' => array(
					array( 'label' => 'Documentos', 'value' => 'Documentos' ),
					array( 'label' => 'Paquete', 'value' => 'Paquete' ),
					array( 'label' => 'Carga', 'value' => 'Carga' ),
				),
				'cot_steps' => array(
					array( 'title' => 'Cuéntanos tu envío', 'text' => 'Indícanos el país de destino, el tipo de envío (documentos, paquete o carga) y el peso o las dimensiones de tu paquete.' ),
					array( 'title' => 'Recibe tu cotización', 'text' => 'Calculamos el costo según peso real o volumétrico, destino y modalidad (aérea o terrestre), y te respondemos en minutos.' ),
					array( 'title' => 'Programa tu recojo', 'text' => 'Coordinamos el recojo a domicilio en Lima o nos visitas en Jr. Callao 220. Nosotros nos encargamos del resto.' ),
				),
			);

		case 'rastreo-de-envios':
			return array(
				'rast_info_cards' => array(
					array( 'icon' => 'fa-solid fa-location-dot', 'title' => 'Estado por número de guía',  'text' => 'Con tu número de guía, un asesor te confirma en qué etapa va tu envío de forma rápida y clara.' ),
					array( 'icon' => 'fa-solid fa-bell',         'title' => 'Te avisamos en cada etapa',  'text' => 'Te mantenemos informado cuando tu paquete cambia de estado, especialmente al ingresar y salir de aduana.' ),
					array( 'icon' => 'fa-brands fa-whatsapp',    'title' => 'Soporte por WhatsApp',       'text' => '¿Dudas con tu seguimiento? Un asesor te ayuda a ubicar tu envío internacional al instante.' ),
				),
				'rast_estados' => array(
					array( 'icon' => 'fa-light fa-tag',              'title' => 'Etiqueta creada',                  'text' => 'Registramos tu envío y generamos su número de guía. Tu paquete está listo para iniciar su recorrido.' ),
					array( 'icon' => 'fa-light fa-warehouse',        'title' => 'Salió del centro de recolección',  'text' => 'Tu paquete fue procesado en nuestra sede de Lima y despachado hacia su destino.' ),
					array( 'icon' => 'fa-light fa-truck-fast',       'title' => 'En tránsito',                      'text' => 'Tu envío avanza hacia su país de destino por vía aérea o terrestre.' ),
					array( 'icon' => 'fa-light fa-building-columns', 'title' => 'En proceso de aduana',             'text' => 'El envío se encuentra en revisión y despacho aduanero para su liberación e ingreso al destino.' ),
					array( 'icon' => 'fa-light fa-circle-pause',     'title' => 'Retenido',                         'text' => 'El envío quedó momentáneamente retenido en aduana. Si se necesita algún documento, te lo informamos.' ),
					array( 'icon' => 'fa-light fa-box-check',        'title' => 'Listo para la entrega',            'text' => 'Tu paquete fue liberado y está listo para entregarse a domicilio o en la agencia local de destino.' ),
					array( 'icon' => 'fa-light fa-circle-check',     'title' => 'Entregado',                        'text' => 'Tu envío llegó a su destino y fue entregado de forma segura al destinatario.' ),
				),
			);

		case 'contacto':
			return array(
				'cont_types' => array(
					array( 'label' => 'Documento', 'value' => 'Documento' ),
					array( 'label' => 'Paquete', 'value' => 'Paquete' ),
					array( 'label' => 'Carga', 'value' => 'Carga' ),
				),
				'cont_cards' => array(
					array( 'icon' => 'fa-brands fa-whatsapp',           'title' => 'Respuesta inmediata',         'text' => 'Escríbenos por WhatsApp al 900 612 836 y un asesor resuelve tus dudas y te ayuda a <a href="' . grenvios_url_base() . '/cotizar/">cotizar tu envío</a> al instante.' ),
					array( 'icon' => 'fa-solid fa-house-circle-check',  'title' => 'Recojo a domicilio en Lima',  'text' => 'Coordinamos el recojo de tu <a href="' . grenvios_url_base() . '/servicios/envio-internacional-de-paquetes/">paquete</a> donde estés en Lima, sin que tengas que trasladarte.' ),
					array( 'icon' => 'fa-solid fa-file-shield',         'title' => 'Asesoría en aduanas',         'text' => 'Te orientamos en la documentación y el embalaje para que tu <a href="' . grenvios_url_base() . '/servicios/">envío internacional</a> a cualquiera de nuestros <a href="' . grenvios_url_base() . '/destinos/">destinos</a> llegue sin demoras.' ),
				),
			);
	}
	return array();
}

/* ══════════════════════════════════════
   RENDER (HTML de los ítems de un repeater)
   Devuelve el markup que reemplaza al token {{REP:clave}} en el partial.

   Dos vías:
   (a) GENÉRICA por plantilla: si la entrada del schema trae 'tpl' (HTML con
       marcadores %campo%), se genera cada ítem con strtr. Marcadores extra
       disponibles: %delay% (100ms,300ms,…) y %i% (índice). Para enlaces, el
       subcampo 'link'/'url' o tipo image/_url se resuelve con esc_url (+ ruta
       relativa → home_url); 'icon' se escapa como atributo; el resto admite
       HTML básico (wp_kses_post).
   (b) PERSONALIZADA: home_services / home_testimonials (markup con lógica).
══════════════════════════════════════ */
function grenvios_render_repeater( $key ) {
	$items = grenvios_repeater( $key );
	if ( ! is_array( $items ) || ! $items ) return '';

	// (a) Vía genérica por plantilla.
	$rep = null;
	foreach ( grenvios_repeater_schema( grenvios_current_slug() ) as $r ) {
		if ( $r['key'] === $key ) { $rep = $r; break; }
	}
	if ( $rep && ! empty( $rep['tpl'] ) ) {
		$fields = isset( $rep['fields'] ) ? $rep['fields'] : array();
		// La vitrina de tarjetas del hub /destinos/ agrega automáticamente los
		// países añadidos por el admin (gestor de Destinos), sin duplicar.
		if ( $key === 'dest_cards' && function_exists( 'grenvios_dest_merge_auto_cards' ) ) {
			$items = grenvios_dest_merge_auto_cards( $items );
		}
		/* Datos calculados por el tema (bandera, plazo, entrega, ciudades) que la
		 * plantilla usa como %_clave%. Llegan como HTML ya escapado; lo que venga
		 * guardado con «_» se descarta, porque no lo escribe el editor. */
		foreach ( $items as $i => $it ) {
			if ( is_array( $it ) ) {
				foreach ( array_keys( $it ) as $k ) if ( is_string( $k ) && $k !== '' && $k[0] === '_' ) unset( $items[ $i ][ $k ] );
			}
		}
		$items = apply_filters( 'grenvios_repeater_extras', $items, $key );
		$out = '';
		foreach ( $items as $i => $it ) {
			$map = array(
				'%delay%' => ( $i * 200 + 100 ) . 'ms',
				'%i%'     => $i,
				'%n%'     => $i + 1,
			);
			if ( preg_match_all( '/%(_[a-z0-9_]+)%/', $rep['tpl'], $ph ) ) {
				foreach ( array_unique( $ph[1] ) as $x ) {
					$map[ '%' . $x . '%' ] = isset( $it[ $x ] ) ? (string) $it[ $x ] : '';
				}
			}
			foreach ( $fields as $sub => $def ) {
				$val  = isset( $it[ $sub ] ) ? (string) $it[ $sub ] : '';
				$type = isset( $def['type'] ) ? $def['type'] : 'text';
				if ( $type === 'image' || $sub === 'link' || $sub === 'url' || substr( $sub, -4 ) === '_url' ) {
					$resolved = ( $sub === 'link' || $sub === 'url' || substr( $sub, -4 ) === '_url' ) ? grenvios_rep_link( $val ) : $val;
					$map[ '%' . $sub . '%' ] = esc_url( $resolved );
				} elseif ( $sub === 'icon' || substr( $sub, -5 ) === '_icon' ) {
					$map[ '%' . $sub . '%' ] = esc_attr( $val );
				} elseif ( $type === 'html' ) {
					$map[ '%' . $sub . '%' ] = wp_kses_post( $val );
				} elseif ( $type === 'textarea' ) {
					$map[ '%' . $sub . '%' ] = wp_kses_post( $val );
				} else {
					$map[ '%' . $sub . '%' ] = esc_html( $val );
				}
			}
			$out .= strtr( $rep['tpl'], $map );
		}
		/* Las plantillas pueden apuntar a imágenes del tema con ARKDINURI, igual
		 * que los partials: aquí no ha pasado por grenvios_partial_raw(). */
		return str_replace( 'ARKDINURI', untrailingslashit( get_template_directory_uri() ), $out );
	}

	// (b) Vía personalizada.
	$uri = untrailingslashit( get_template_directory_uri() );
	$out = '';

	switch ( $key ) {

		case 'home_services':
			foreach ( $items as $i => $it ) {
				$img   = ! empty( $it['img'] )  ? $it['img'] : $uri . '/assets/img/post-1.jpg';
				$icon  = ! empty( $it['icon'] ) ? $it['icon'] : 'logis logis-package';
				$title = isset( $it['title'] ) ? $it['title'] : '';
				$text  = isset( $it['text'] )  ? $it['text']  : '';
				$more  = isset( $it['more'] ) && $it['more'] !== '' ? $it['more'] : 'Leer más';
				$link  = grenvios_rep_link( isset( $it['link'] ) ? $it['link'] : '' );
				$delay = ( $i * 200 + 100 ) . 'ms';
				$href  = $link ? ' href="' . esc_url( $link ) . '"' : '';
				$out .= '<div class="swiper-slide">'
					. '<div class="service-item wow fade-in-bottom" data-wow-delay="' . esc_attr( $delay ) . '">'
					. '<div class="service-thumb"><img src="' . esc_url( $img ) . '" alt="' . esc_attr( wp_strip_all_tags( $title ) ) . '"></div>'
					. '<div class="service-content">'
					. '<div class="service-icon"><i class="' . esc_attr( $icon ) . '"></i></div>'
					. '<h3><a' . $href . '>' . wp_kses_post( $title ) . '</a></h3>'
					. '<p>' . wp_kses_post( $text ) . '</p>'
					. ( $link ? '<a class="read-more"' . $href . '>' . esc_html( $more ) . '</a>' : '' )
					. '</div></div></div>';
			}
			return $out;

		case 'home_testimonials':
			// Logo "G" oficial de Google (4 colores) en SVG inline.
			$g_logo = '<svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">'
				. '<path fill="#4285F4" d="M45.12 24.5c0-1.56-.14-3.06-.4-4.5H24v8.51h11.84c-.51 2.75-2.06 5.08-4.39 6.64v5.52h7.11c4.16-3.83 6.56-9.47 6.56-16.17z"/>'
				. '<path fill="#34A853" d="M24 46c5.94 0 10.92-1.97 14.56-5.33l-7.11-5.52c-1.97 1.32-4.49 2.1-7.45 2.1-5.73 0-10.58-3.87-12.31-9.07H4.34v5.7C7.96 41.07 15.4 46 24 46z"/>'
				. '<path fill="#FBBC05" d="M11.69 28.18C11.25 26.86 11 25.45 11 24s.25-2.86.69-4.18v-5.7H4.34C2.85 17.09 2 20.45 2 24s.85 6.91 2.34 9.88l7.35-5.7z"/>'
				. '<path fill="#EA4335" d="M24 10.75c3.23 0 6.13 1.11 8.41 3.29l6.31-6.31C34.91 4.18 29.93 2 24 2 15.4 2 7.96 6.93 4.34 14.12l7.35 5.7c1.73-5.2 6.58-9.07 12.31-9.07z"/>'
				. '</svg>';
			$stars = str_repeat( '<i class="fa-solid fa-star"></i>', 5 );
			foreach ( $items as $it ) {
				$img  = ! empty( $it['img'] ) ? $it['img'] : $uri . '/assets/img/team-1.jpg';
				$name = isset( $it['name'] ) ? $it['name'] : '';
				$city = isset( $it['city'] ) ? $it['city'] : '';
				$date = isset( $it['date'] ) ? $it['date'] : '';
				$text = isset( $it['text'] ) ? $it['text'] : '';
				$sub  = trim( $city ) !== '' ? 'Local Guide · ' . esc_html( $city ) : 'Reseña en Google';
				$date_html = trim( $date ) !== '' ? '<span class="gr-date">' . esc_html( $date ) . '</span>' : '';
				$out .= '<div class="swiper-slide">'
					. '<div class="google-review">'
					. '<div class="gr-top">'
					. '<img class="gr-avatar" src="' . esc_url( $img ) . '" alt="' . esc_attr( wp_strip_all_tags( $name ) ) . '" loading="lazy">'
					. '<div class="gr-id"><h3 class="gr-name">' . esc_html( $name ) . '</h3>'
					. '<span class="gr-sub">' . $sub . '</span></div>'
					. '<span class="gr-glogo">' . $g_logo . '</span>'
					. '</div>'
					. '<div class="gr-rating"><span class="gr-stars">' . $stars . '</span>' . $date_html . '</div>'
					. '<p class="gr-text">' . wp_kses_post( $text ) . '</p>'
					. '</div></div>';
			}
			return $out;
	}
	return '';
}

/* ══════════════════════════════════════
   TOKENS {{REP:clave}} → HTML
   Se engancha desde grenvios_apply_text_tokens() (inc/customizer.php).
══════════════════════════════════════ */
function grenvios_apply_repeater_tokens( $html ) {
	if ( ! is_string( $html ) || strpos( $html, '{{REP:' ) === false ) return $html;
	return preg_replace_callback( '/\{\{REP:([a-z0-9_]+)\}\}/', function ( $m ) {
		return grenvios_render_repeater( $m[1] );
	}, $html );
}

/* ══════════════════════════════════════
   UI DEL PANEL — subcampo y ítem de repeater
══════════════════════════════════════ */
/** Renderiza un subcampo (text/textarea/image) dentro de un ítem de repeater. */
function grenvios_nep_subfield( $sub, $def, $value ) {
	$type  = isset( $def['type'] ) ? $def['type'] : 'text';
	$label = isset( $def['label'] ) ? $def['label'] : $sub;
	$hint  = isset( $def['hint'] ) ? $def['hint'] : '';
	?>
	<div class="nep-field<?php echo $type === 'image' ? ' nep-field--img' : ''; ?>">
	  <label><?php echo esc_html( $label ); ?></label>
	  <?php if ( $hint ) : ?><span class="nep-hint"><?php echo esc_html( $hint ); ?></span><?php endif; ?>
	  <?php if ( $type === 'textarea' ) : ?>
		<textarea data-rep-sub="<?php echo esc_attr( $sub ); ?>" rows="3"><?php echo esc_textarea( $value ); ?></textarea>
	  <?php elseif ( $type === 'image' ) : ?>
		<div class="nep-img-wrap">
		  <div class="nep-img-preview" <?php if ( $value ) echo 'style="background-image:url(' . esc_url( $value ) . ')"'; ?>>
			<?php if ( ! $value ) echo '<i class="fa-solid fa-image"></i>'; ?>
		  </div>
		  <input type="hidden" data-rep-sub="<?php echo esc_attr( $sub ); ?>" value="<?php echo esc_url( $value ); ?>" />
		  <div class="nep-img-btns">
			<button type="button" class="nep-img-pick"><i class="fa-solid fa-upload"></i> <?php echo $value ? 'Cambiar' : 'Seleccionar'; ?></button>
			<button type="button" class="nep-img-remove" style="<?php echo $value ? '' : 'display:none'; ?>"><i class="fa-solid fa-trash"></i> Quitar</button>
		  </div>
		</div>
	  <?php else : ?>
		<input type="text" data-rep-sub="<?php echo esc_attr( $sub ); ?>" value="<?php echo esc_attr( $value ); ?>" />
	  <?php endif; ?>
	</div>
	<?php
}

/** Renderiza un ítem completo de repeater (cabecera + subcampos). */
function grenvios_nep_rep_item( $cfg, $values, $index ) {
	$num = $index + 1;
	?>
	<div class="nep-rep-item">
	  <div class="nep-rep-item-head">
		<span class="nep-rep-item-title"><?php echo esc_html( $cfg['item_label'] ); ?> <?php echo (int) $num; ?></span>
		<div class="nep-rep-item-tools">
		  <button type="button" class="nep-rep-up" title="Subir"><i class="fa-solid fa-arrow-up"></i></button>
		  <button type="button" class="nep-rep-down" title="Bajar"><i class="fa-solid fa-arrow-down"></i></button>
		  <button type="button" class="nep-rep-remove" title="Quitar"><i class="fa-solid fa-trash"></i></button>
		</div>
	  </div>
	  <div class="nep-grid">
		<?php foreach ( $cfg['fields'] as $sub => $def ) {
			$sv = isset( $values[ $sub ] ) ? $values[ $sub ] : '';
			grenvios_nep_subfield( $sub, $def, $sv );
		} ?>
	  </div>
	</div>
	<?php
}
