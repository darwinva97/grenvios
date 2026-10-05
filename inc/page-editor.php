<?php
/**
 * Grenvíos — Editor de Página en línea (inline page editor)
 * Panel lateral para administradores que edita el contenido de CADA página
 * directamente sobre el sitio y lo guarda en post-meta (`grenvios_<clave>`)
 * vía REST. Adaptado del patrón del tema de referencia (Notaría).
 *
 * Fuente de campos: grenvios_text_registry() (página → secciones → campos),
 * la MISMA estructura que ya define los textos parametrizados. Los textos se
 * insertan en las plantillas como tokens {{clave}} y se resuelven en render.
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ══════════════════════════════════════
   LECTURA DE UN CAMPO (post-meta por página, con default del registro)
   - Si el campo se guardó (aunque sea vacío) se respeta lo guardado.
   - Si nunca se guardó, cae al valor por defecto.
══════════════════════════════════════ */
function grenvios_field( $key, $default = '' ) {
	/* Filtro `grenvios_campo_valor`: lo usa el módulo de rutas de país para que
	 * la cabecera de una copia nombre su destino (ver inc/paises-cabecera.php).
	 * Recibe el valor ya resuelto, la clave y el valor por defecto. */
	return apply_filters( 'grenvios_campo_valor', grenvios_field_crudo( $key, $default ), $key, $default );
}

function grenvios_field_crudo( $key, $default = '' ) {
	// Portada estática (Inicio): meta sobre page_on_front
	if ( is_front_page() && ! is_home() ) {
		$pid = (int) get_option( 'page_on_front' );
		if ( $pid && metadata_exists( 'post', $pid, 'grenvios_' . $key ) ) {
			return get_post_meta( $pid, 'grenvios_' . $key, true );
		}
		return $default;
	}
	// Página del blog (listado de entradas = page_for_posts): meta sobre esa página.
	if ( is_home() && get_option( 'page_for_posts' ) ) {
		$pid = (int) get_option( 'page_for_posts' );
		if ( $pid && metadata_exists( 'post', $pid, 'grenvios_' . $key ) ) {
			return get_post_meta( $pid, 'grenvios_' . $key, true );
		}
		return $default;
	}
	// Páginas y entradas
	if ( is_page() || is_single() ) {
		$pid = (int) get_the_ID();
		if ( $pid && metadata_exists( 'post', $pid, 'grenvios_' . $key ) ) {
			return get_post_meta( $pid, 'grenvios_' . $key, true );
		}
	}
	return $default;
}

/* ══════════════════════════════════════
   FONDOS / BANNER POR PÁGINA
   Cada slot: clave => [ etiqueta, selector CSS a sobrescribir ].
   El valor (URL) se guarda en post-meta y se aplica SOLO en esa página
   con un <style> en el <head>, encima del fondo por defecto del diseño.
══════════════════════════════════════ */
function grenvios_page_bg_fields() {
	$banner = 'Banner superior — imagen de fondo';
	$sel    = '.page-header';
	return array(
		'destinos'                          => array( 'dest_bg_banner'  => array( $banner, $sel ) ),
		'nosotros'                          => array( 'nos_bg_banner'   => array( $banner, $sel ) ),
		'servicios'                         => array( 'serv_bg_banner'  => array( $banner, $sel ) ),
		'envio-internacional-de-documentos' => array( 'doc_bg_banner'   => array( $banner, $sel ) ),
		'envio-internacional-de-paquetes'   => array( 'paq_bg_banner'   => array( $banner, $sel ) ),
		'carga-internacional'               => array( 'carga_bg_banner' => array( $banner, $sel ) ),
		'apostilla-y-traduccion'            => array( 'apos_bg_banner'  => array( $banner, $sel ) ),
		'envios-para-empresas'              => array( 'emp_bg_banner'   => array( $banner, $sel ) ),
		'cotizar'                           => array( 'cot_bg_banner'   => array( $banner, $sel ) ),
		'rastreo-de-envios'                 => array( 'rast_bg_banner'  => array( $banner, $sel ) ),
		'contacto'                          => array( 'cont_bg_banner'  => array( $banner, $sel ) ),
		'preguntas-frecuentes'              => array( 'pf_bg_banner'    => array( $banner, $sel ) ),
	);
}

/* Aplica los fondos por página (override CSS solo en la página actual). */
add_action( 'wp_head', function () {
	$slug = grenvios_current_slug();
	$bgs  = grenvios_page_bg_fields();
	if ( ! isset( $bgs[ $slug ] ) ) return;
	$rules = array();
	foreach ( $bgs[ $slug ] as $key => $bf ) {
		$url = grenvios_field( $key, '' );
		if ( ! $url ) continue;
		$rules[] = $bf[1] . '{background-image:url(' . esc_url( $url ) . ') !important}';
	}
	if ( $rules ) {
		echo "<style id=\"grenvios-page-bg\">\n" . implode( "\n", $rules ) . "\n</style>\n";
	}
}, 100 );

/* ══════════════════════════════════════
   ANCLAS DE SECCIÓN (para "llevar" a la sección al editar)
   Inyecta id="ged-<seccion>" en el elemento que envuelve el primer texto de cada
   sección del registro, para que al hacer clic en el editor la página haga scroll
   hasta esa sección. Solo se añade para administradores (no ensucia el HTML público).
══════════════════════════════════════ */
function grenvios_inject_section_anchors( $html, $slug ) {
	if ( ! $slug || ! function_exists( 'grenvios_text_registry' ) ) return $html;
	$reg = grenvios_text_registry();
	if ( ! isset( $reg[ $slug ] ) ) return $html;
	foreach ( $reg[ $slug ]['sections'] as $sk => $sec ) {
		$id = 'ged-' . $sk;
		if ( strpos( $html, 'id="' . $id . '"' ) !== false ) continue;   // ya existe
		foreach ( $sec['fields'] as $fk => $f ) {
			$type = isset( $f[1] ) ? $f[1] : 'text';
			if ( $type === 'image' ) continue;                            // evita atributos src
			$tok = '{{' . $fk . '}}';
			$pos = strpos( $html, $tok );
			if ( $pos === false ) continue;
			$prev = $pos > 0 ? $html[ $pos - 1 ] : '>';
			if ( $prev === '"' || $prev === "'" ) continue;               // token dentro de un atributo
			$lt = strrpos( substr( $html, 0, $pos ), '<' );               // tag de apertura que lo envuelve
			if ( $lt === false || ( isset( $html[ $lt + 1 ] ) && $html[ $lt + 1 ] === '/' ) ) continue;
			$gt = strpos( $html, '>', $lt );
			if ( $gt === false ) continue;
			$sp = strpos( $html, ' ', $lt );
			$insert = ( $sp !== false && $sp < $gt ) ? $sp : $gt;
			$html = substr( $html, 0, $insert ) . ' id="' . $id . '"' . substr( $html, $insert );
			break;
		}
	}
	return $html;
}

/* ID del post que se está editando en la página actual (0 = ninguno editable). */
function grenvios_editor_post_id() {
	if ( is_front_page() && ! is_home() ) return (int) get_option( 'page_on_front' );
	// Página del blog (listado de entradas = page_for_posts): editar su hero.
	if ( is_home() && get_option( 'page_for_posts' ) ) return (int) get_option( 'page_for_posts' );
	if ( is_page() || is_single() )       return (int) get_the_ID();
	return 0;
}

/* ══════════════════════════════════════
   GUARDADO REST  (POST grenvios/v1/save-page)
══════════════════════════════════════ */
add_action( 'rest_api_init', function () {
	register_rest_route( 'grenvios/v1', '/save-page', array(
		'methods'             => 'POST',
		'callback'            => 'grenvios_rest_save_page',
		'permission_callback' => function () { return current_user_can( 'edit_posts' ); },
	) );
} );

function grenvios_rest_save_page( WP_REST_Request $request ) {
	$post_id = (int) $request->get_param( 'post_id' );
	$fields  = $request->get_param( 'fields' );
	if ( ! $post_id || ! is_array( $fields ) ) {
		return new WP_REST_Response( array( 'success' => false, 'message' => 'Datos inválidos.' ), 400 );
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return new WP_REST_Response( array( 'success' => false, 'message' => 'Sin permiso.' ), 403 );
	}

	$allowed_html = array(
		'strong' => array(), 'b' => array(), 'em' => array(), 'i' => array(),
		'span'   => array( 'class' => array(), 'style' => array() ),
		'br'     => array(), 'p' => array( 'class' => array() ),
		'a'      => array( 'href' => array(), 'target' => array(), 'rel' => array() ),
		/* Listas y tablas con su clase: las secciones de contenido (gr-pseo-list,
		 * gr-pseo-steps, gr-table) perdían el diseño al guardarlas desde el panel. */
		'ul'     => array( 'class' => array() ), 'ol' => array( 'class' => array() ), 'li' => array(),
		'blockquote' => array( 'class' => array() ), 'h3' => array(), 'h4' => array(),
		'div'    => array( 'class' => array() ),
		'table'  => array( 'class' => array() ), 'thead' => array(), 'tbody' => array(), 'tr' => array(),
		'th'     => array( 'scope' => array() ), 'td' => array(),
	);
	$types = array();
	foreach ( grenvios_text_fields() as $k => $f ) $types[ $k ] = $f['type'];
	// Campos sintéticos (secciones de Destino/FAQ que no viven en el registro) que aceptan HTML básico.
	foreach ( array( 'dst_hero_title', 'dst_intro_title', 'dst_info_links', 'dst_cta_title', 'pf_title', 'blog_title' ) as $k ) if ( ! isset( $types[ $k ] ) ) $types[ $k ] = 'html';

	foreach ( $fields as $key => $value ) {
		$key  = sanitize_key( $key );
		$type = isset( $types[ $key ] ) ? $types[ $key ] : 'text';
		// Textos de las secciones automáticas de las fichas (inc/destinos-textos-editables.php).
		if ( strpos( $key, 'dt_' ) === 0 ) $type = 'html';
		if ( $type === 'image' || strpos( $key, '_bg_' ) !== false || substr( $key, -4 ) === '_img' || substr( $key, -4 ) === '_url' ) {
			$clean = esc_url_raw( $value );
		} elseif ( $type === 'html' ) {
			$clean = wp_kses( (string) $value, $allowed_html );
		} elseif ( $type === 'textarea' ) {
			$clean = sanitize_textarea_field( (string) $value );
		} else {
			$clean = sanitize_text_field( (string) $value );
		}
		update_post_meta( $post_id, 'grenvios_' . $key, $clean );
	}

	/* ── Repeaters (contenido dinámico: tarjetas, testimonios, …) ── */
	$repeaters = $request->get_param( 'repeaters' );
	if ( is_array( $repeaters ) ) {
		foreach ( $repeaters as $rkey => $items ) {
			$rkey = sanitize_key( $rkey );
			if ( ! is_array( $items ) ) continue;
			$clean_items = array();
			foreach ( $items as $item ) {
				if ( ! is_array( $item ) ) continue;
				$row = array();
				foreach ( $item as $sub => $val ) {
					$sub = sanitize_key( $sub );
					if ( $sub === 'img' || substr( $sub, -4 ) === '_img' || $sub === 'link' || substr( $sub, -4 ) === '_url' ) {
						$row[ $sub ] = esc_url_raw( (string) $val );
					} else {
						$row[ $sub ] = wp_kses( (string) $val, $allowed_html );
					}
				}
				if ( $row ) $clean_items[] = $row;
			}
			update_post_meta( $post_id, 'grenvios_rep_' . $rkey, array_values( $clean_items ) );
		}
	}

	/* ── Ajustes GLOBALES del sitio (colores, redes, encabezado, pie) ── */
	$globals = $request->get_param( 'globals' );
	if ( is_array( $globals ) && function_exists( 'grenvios_globals_save' ) ) {
		grenvios_globals_save( $globals );
	}

	/* Guardados de otros módulos (datos del país: inc/editor-cobertura.php). */
	do_action( 'grenvios_editor_guardar', $request, $post_id );

	return new WP_REST_Response( array( 'success' => true ), 200 );
}

/* ══════════════════════════════════════
   ENCOLADO (solo administradores)
══════════════════════════════════════ */
add_action( 'wp_enqueue_scripts', function () {
	if ( ! current_user_can( 'edit_posts' ) ) return;
	if ( ! grenvios_editor_post_id() ) return;
	$uri = get_template_directory_uri();
	wp_enqueue_media();
	wp_enqueue_style( 'grenvios-page-editor', $uri . '/assets/css/page-editor.css', array(), LOGISKO_VER );
	// Versión por fecha del archivo: con la versión fija del tema, el navegador seguía usando el JS viejo tras un cambio.
	wp_enqueue_script( 'grenvios-page-editor', $uri . '/assets/js/page-editor.js', array(), (string) filemtime( get_template_directory() . '/assets/js/page-editor.js' ), true );
}, 100 );

/* ══════════════════════════════════════
   PANEL DEL EDITOR (en el footer, solo administradores)
══════════════════════════════════════ */
add_action( 'wp_footer', function () {
	if ( ! current_user_can( 'edit_posts' ) ) return;
	$post_id = grenvios_editor_post_id();
	if ( ! $post_id ) return;

	/* `grenvios_editor_slug`: qué registro de campos usa el panel. La portada de
	 * una ruta de país se sirve con la plantilla de su destino, no con la de la
	 * home, y tiene que ofrecer los campos que de verdad pinta. */
	$slug = apply_filters( 'grenvios_editor_slug', grenvios_current_slug(), $post_id );
	$reg  = grenvios_text_registry();

	// Página del BLOG (listado de entradas, sin partial): hero editable (antetítulo,
	// título e imagen de fondo). Valores por defecto = los del Personalizador.
	if ( ! isset( $reg[ $slug ] ) && $slug === 'blog' ) {
		$reg['blog'] = array( 'sections' => array(
			'hero' => array(
				'label'           => 'Blog · Banner (encabezado)',
				'sel'             => '.gr-dhero',
				'_no_token_check' => true,
				'fields'          => array(
					'blog_eyebrow' => array( 'Antetítulo', 'text', get_theme_mod( 'grenvios_blog_eyebrow', 'Blog' ) ),
					'blog_title'   => array( 'Título', 'html', get_theme_mod( 'grenvios_blog_title', 'Noticias y <span>novedades</span>' ) ),
					'blog_img'     => array( 'Imagen de fondo del banner', 'image', '' ),
				),
			),
		) );
	}

	// Páginas de DESTINO por país (data-driven, sin partial): sección sintética editable
	// con los datos del país como valores por defecto (mismas claves que usa el render).
	if ( ! isset( $reg[ $slug ] ) && function_exists( 'grenvios_destinos' ) ) {
		$dd = grenvios_destinos();
		if ( isset( $dd[ $slug ] ) ) {
			$c = $dd[ $slug ];
			$dp = function_exists( 'grenvios_pages' ) ? grenvios_pages() : array();
			$hd = function_exists( 'grenvios_dhero_defaults' ) ? grenvios_dhero_defaults( $c ) : array();
			$serv_title = isset( $dp[ $c['servicio'] ] ) ? $dp[ $c['servicio'] ]['title'] : 'Envío internacional';
			$reg[ $slug ] = array( 'sections' => array(
				'destino_hero' => array(
					'label'           => 'Destino · Hero (pantalla completa)',
					'sel'             => '.gr-dhero',
					'_no_token_check' => true,
					'fields'          => array(
						'dst_intro_sub'   => array( 'Antesala (arriba del título)', 'text', $hd['dst_intro_sub'] ),
						'dst_intro_title' => array( 'Título H1 (lo que va en <span> sale en la 2.ª línea, en color)', 'html', $hd['dst_intro_title'] ),
						'dst_lead'        => array( 'Entradilla', 'textarea', $hd['dst_lead'] ),
						'dst_hf1_t'       => array( 'Garantía 1 · título', 'text', $hd['dst_hf1_t'] ),
						'dst_hf1_d'       => array( 'Garantía 1 · texto', 'text', $hd['dst_hf1_d'] ),
						'dst_hf2_t'       => array( 'Garantía 2 · título', 'text', $hd['dst_hf2_t'] ),
						'dst_hf2_d'       => array( 'Garantía 2 · texto', 'text', $hd['dst_hf2_d'] ),
						'dst_hf3_t'       => array( 'Garantía 3 · título', 'text', $hd['dst_hf3_t'] ),
						'dst_hf3_d'       => array( 'Garantía 3 · texto', 'text', $hd['dst_hf3_d'] ),
						'dst_hero_btn1'   => array( 'Botón · Cotizar', 'text', $hd['dst_hero_btn1'] ),
						'dst_hero_btn2'   => array( 'Botón · Rastrear', 'text', $hd['dst_hero_btn2'] ),
						'dst_hero_img'    => array( 'Foto de fondo (mejor horizontal, con el motivo a la derecha)', 'image', '' ),
					),
				),
				'destino_soluciones' => array(
					'label'           => 'Destino · Nuestras soluciones',
					'sel'             => '.gr-dsol',
					'_no_token_check' => true,
					'fields'          => array(
						'dst_sol_eyebrow' => array( 'Antesala', 'text', $hd['dst_sol_eyebrow'] ),
						'dst_sol_title'   => array( 'Título', 'text', $hd['dst_sol_title'] ),
						'dst_sol_text'    => array( 'Texto', 'textarea', $hd['dst_sol_text'] ),
						'dst_sol1_t'      => array( 'Tarjeta 1 · título', 'text', $hd['dst_sol1_t'] ),
						'dst_sol1_d'      => array( 'Tarjeta 1 · texto', 'text', $hd['dst_sol1_d'] ),
						'dst_sol2_t'      => array( 'Tarjeta 2 · título', 'text', $hd['dst_sol2_t'] ),
						'dst_sol2_d'      => array( 'Tarjeta 2 · texto', 'text', $hd['dst_sol2_d'] ),
						'dst_sol3_t'      => array( 'Tarjeta 3 · título', 'text', $hd['dst_sol3_t'] ),
						'dst_sol3_d'      => array( 'Tarjeta 3 · texto', 'text', $hd['dst_sol3_d'] ),
						'dst_sol4_t'      => array( 'Tarjeta 4 · título', 'text', $hd['dst_sol4_t'] ),
						'dst_sol4_d'      => array( 'Tarjeta 4 · texto', 'text', $hd['dst_sol4_d'] ),
						'dst_sol1_b'      => array( 'Tarjeta 1 · etiqueta', 'text', $hd['dst_sol1_b'] ),
						'dst_sol2_b'      => array( 'Tarjeta 2 · etiqueta', 'text', $hd['dst_sol2_b'] ),
						'dst_sol3_b'      => array( 'Tarjeta 3 · etiqueta', 'text', $hd['dst_sol3_b'] ),
						'dst_sol4_b'      => array( 'Tarjeta 4 · etiqueta', 'text', $hd['dst_sol4_b'] ),
						'dst_sol_ver'     => array( 'Texto del enlace de las tarjetas', 'text', $hd['dst_sol_ver'] ),
						'dst_sol_img'     => array( 'Foto (vacía = la del repartidor)', 'image', '' ),
					),
				),
				'destino' => array(
					'label'           => 'Destino · ' . $c['title'],
					'sel'             => '.dest-section',
					'_no_token_check' => true,
					'fields'          => array(
						'dst_fact_modos_k' => array( 'Etiqueta · Modalidad', 'text', 'Modalidad' ),
						'dst_modos'        => array( 'Modalidad (valor)', 'text', $c['modos'] ),
						'dst_fact_tiempo_k'=> array( 'Etiqueta · Tiempo de entrega', 'text', 'Tiempo de entrega' ),
						'dst_tiempo'       => array( 'Tiempo de entrega (valor)', 'text', $c['tiempo'] ),
						'dst_fact_entrega_k'=> array( 'Etiqueta · Forma de entrega', 'text', 'Forma de entrega' ),
						'dst_entrega'      => array( 'Forma de entrega (valor)', 'text', $c['entrega'] ),
						'dst_enviar_sub'   => array( '"¿Qué puedes enviar?" · Antetítulo', 'text', 'Qué puedes enviar' ),
						'dst_enviar_title' => array( '"¿Qué puedes enviar?" · Título', 'text', '¿Qué puedes enviar a ' . $c['title'] . '?' ),
						'dst_enviar_intro' => array( '"¿Qué puedes enviar?" · Texto', 'textarea', 'Con Grenvíos puedes enviar documentos, paquetes, equipaje, compras y carga. Cada modalidad se adapta al peso, volumen y urgencia de tu envío.' ),
						'dst_enviar_li1'   => array( 'Lista · Ítem 1', 'text', 'Documentos legales, títulos y trámites' ),
						'dst_enviar_li2'   => array( 'Lista · Ítem 2', 'text', 'Paquetes, equipaje y compras personales' ),
						'dst_enviar_li3'   => array( 'Lista · Ítem 3', 'text', 'Carga comercial para tu negocio' ),
						'dst_enviar_li4'   => array( 'Lista · Ítem 4', 'text', 'Seguimiento de tu envío por número de guía' ),
						'dst_enviar_img'   => array( '"¿Qué puedes enviar?" · Foto (vacía = foto de ejemplo de embalaje)', 'image', '' ),
						'dst_info_title'   => array( '"Información importante" · Título', 'text', 'Información importante' ),
						'dst_restr'        => array( 'Información importante · Texto', 'textarea', $c['restr'] ),
						'dst_info_links'   => array( 'Información importante · Párrafo con enlaces', 'html', '¿Listo para enviar a ' . $c['title'] . '? Conoce los detalles de <a href="/servicios/' . $c['servicio'] . '/">' . $serv_title . ' a ' . $c['title'] . '</a>, revisa nuestros <a href="/servicios/">servicios de envío internacional</a> o <a href="/cotizar/">solicita tu cotización</a> ahora mismo.' ),
					),
				),
				'destino_precio' => array(
					'label'           => 'Destino · Imágenes de precio y ciudades',
					'sel'             => '.dest-precio-foto',
					'_no_token_check' => true,
					'fields'          => array(
						'dst_precio_img' => array( 'Imagen (vacía = ilustración de caja en balanza)', 'image', '' ),
						'dst_ciudades_img' => array( 'Foto de «Ciudades a las que llegamos» (vacía = foto de ejemplo del país)', 'image', '' ),
					),
				),
				'destino_como' => array(
					'label'           => 'Destino · ¿Cómo funciona?',
					'sel'             => '.dest-proc-section',
					'_no_token_check' => true,
					'fields'          => array(
						'dst_como_title' => array( 'Título', 'text', '¿Cómo funciona?' ),
						'dst_como_s1_t'  => array( 'Paso 1 · título', 'text', 'Solicita tu envío' ),
						'dst_como_s1_d'  => array( 'Paso 1 · texto', 'textarea', 'Por WhatsApp, web o llamada.' ),
						'dst_como_s2_t'  => array( 'Paso 2 · título', 'text', 'Entrega tu envío' ),
						'dst_como_s2_d'  => array( 'Paso 2 · texto', 'textarea', 'En nuestra oficina o solicitamos recojo.' ),
						'dst_como_s3_t'  => array( 'Paso 3 · título', 'text', 'Transporte' ),
						'dst_como_s3_d'  => array( 'Paso 3 · texto', 'textarea', 'Envío por vía aérea o terrestre según tu elección.' ),
						'dst_como_s4_t'  => array( 'Paso 4 · título', 'text', 'Entrega en destino' ),
						'dst_como_s4_d'  => array( 'Paso 4 · texto', 'textarea', 'Recibes tu envío de forma segura.' ),
					),
				),
				/* «Por qué elegirnos» con barras de progreso, imagen enmarcada y cuatro
				 * tarjetas numeradas. Las barras vienen VACÍAS a propósito: un «98 % de
				 * entregas a tiempo» es una cifra que solo puede poner quien la mide, y
				 * mientras no se rellene el bloque de barras no se pinta. */
				'destino_why' => array(
					'label'           => 'Destino · Por qué elegirnos',
					'sel'             => '.dest-why-section',
					'_no_token_check' => true,
					'fields'          => array(
						'dst_why_eyebrow'    => array( 'Etiqueta superior', 'text', 'Por qué elegirnos' ),
						'dst_why_title'      => array( 'Título', 'html', 'Tu operador de confianza para enviar a <span>' . $c['title'] . '</span>' ),
						'dst_why_text'       => array( 'Texto', 'textarea', 'Somos un operador peruano y la ruta a ' . $c['title'] . ' la gestionamos de principio a fin: recogemos en {{origen_ciudad}}, despachamos, seguimos el envío y te avisamos cuando tu destinatario lo recibe. Sin intermediarios que se pasen la responsabilidad.' ),
						'dst_why_img'        => array( 'Imagen (mejor una foto propia: almacén, equipo, despacho)', 'image', '' ),
						'dst_why_bar1_label' => array( 'Barra 1 · etiqueta (vacía = no se muestra)', 'text', '' ),
						'dst_why_bar1_val'   => array( 'Barra 1 · porcentaje (0-100)', 'text', '' ),
						'dst_why_bar2_label' => array( 'Barra 2 · etiqueta', 'text', '' ),
						'dst_why_bar2_val'   => array( 'Barra 2 · porcentaje (0-100)', 'text', '' ),
						'dst_why1'           => array( 'Tarjeta 1', 'text', 'Seguimiento en tiempo real' ),
						'dst_why2'           => array( 'Tarjeta 2', 'text', 'Precio cerrado antes de despachar' ),
						'dst_why3'           => array( 'Tarjeta 3', 'text', 'Envío con seguro' ),
						'dst_why4'           => array( 'Tarjeta 4', 'text', 'Entrega en ' . $c['tiempo'] ),
					),
				),
				'destino_cta' => array(
					'label'           => 'Destino · CTA final',
					'sel'             => '.cta-section',
					'_no_token_check' => true,
					'fields'          => array(
						'dst_cta_title' => array( 'Título del CTA', 'html', '¿Listo para enviar a ' . $c['title'] . '?' ),
						'dst_cta_text'  => array( 'Texto del CTA', 'textarea', 'Cotiza tu envío en minutos y te asesoramos sin compromiso.' ),
						'dst_btn_cotizar' => array( 'Botón · Cotizar', 'text', 'Cotizar mi envío' ),
						'dst_btn_wa'      => array( 'Botón · WhatsApp', 'text', 'WhatsApp' ),
					),
				),
			) );
		}
	}

	// En la portada de una ruta el hero es el carrusel de la home, no el del destino.
	if ( is_front_page() && isset( $reg[ $slug ]['sections']['destino_hero'] ) ) unset( $reg[ $slug ]['sections']['destino_hero'] );

	// Páginas sin textos parametrizados pero con FAQ editable (p. ej. preguntas-frecuentes):
	// se habilita el editor solo con la sección sintética de Preguntas frecuentes.
	if ( ! isset( $reg[ $slug ] ) ) {
		if ( function_exists( 'grenvios_page_faqs' ) && grenvios_page_faqs( $slug ) ) {
			$reg[ $slug ] = array( 'sections' => array() );
		} else {
			return;                                   // página aún no parametrizada
		}
	}
	$sections = $reg[ $slug ]['sections'];

	// Sección sintética de Preguntas frecuentes (el FAQ se renderiza por PHP, no vive en el
	// partial): encabezados editables + el repeater page_faq se agrupa aquí. Sus campos se
	// eximen del auto-ocultado por token (no hay token {{}} en el partial).
	if ( function_exists( 'grenvios_page_faqs' ) && grenvios_page_faqs( $slug ) ) {
		$is_pf = ( $slug === 'preguntas-frecuentes' );
		// En la página de Preguntas Frecuentes, antetítulo/título son el BANNER (hero) y se
		// editan en su propia sección "Banner"; el acordeón FAQ solo lleva la lista de preguntas.
		if ( $is_pf ) {
			$sections = array_merge( array(
				'hero' => array(
					'label'           => 'Banner (encabezado)',
					'sel'             => '.gr-dhero',
					'_no_token_check' => true,
					'fields'          => array(
						'pf_sub'   => array( 'Antetítulo', 'text', 'Preguntas frecuentes' ),
						'pf_title' => array( 'Título', 'html', 'Resolvemos tus <span class="hl">dudas</span>' ),
					),
				),
			), $sections );
		}
		$sections['faq'] = array(
			'label'           => '❓ Preguntas frecuentes',
			'sel'             => '.grenvios-faq-section',
			'_no_token_check' => true,
			'fields'          => $is_pf ? array() : array(
				'pf_sub'   => array( 'Subtítulo', 'text', 'Preguntas frecuentes' ),
				'pf_title' => array( 'Título', 'html', 'Resolvemos tus <span class="hl">dudas</span>' ),
			),
		);
	}

	/* El panel sigue el orden de la página: el banner/hero va siempre primero
	 * (la entradilla SEO se registra delante y lo dejaba en segundo lugar). */
	foreach ( $sections as $sk => $sec ) {
		$sel = isset( $sec['sel'] ) ? $sec['sel'] : '';
		if ( $sk === 'hero' || $sk === 'destino_hero' || $sel === '.page-header' || $sel === '.gr-dhero' ) {
			$sections = array( $sk => $sec ) + $sections;
			break;
		}
	}
	// La entradilla SEO se pinta justo debajo del hero: va segunda.
	if ( isset( $sections['entradilla'] ) ) {
		$primera  = array_slice( $sections, 0, 1, true );
		$ent      = array( 'entradilla' => $sections['entradilla'] );
		$sections = ( key( $primera ) === 'entradilla' ) ? $sections : $primera + $ent + $sections;
	}
	?>
<button id="nep-fab" aria-label="Editar página">
  <i class="fa-solid fa-pen"></i><span>Editar página</span>
</button>

<div id="nep-panel" role="dialog" aria-label="Editor de Página">
  <div id="nep-header">
    <span id="nep-title"><i class="fa-solid fa-pen-to-square"></i> Editar Página</span>
    <div id="nep-header-right">
      <span id="nep-status"></span>
      <button id="nep-save"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
      <button id="nep-close" aria-label="Cerrar editor">&times;</button>
    </div>
  </div>
  <div id="nep-body">
    <?php
    /* Cada SECCIÓN es un único acordeón que agrupa TODO su contenido:
       textos + imágenes + (si la sección tiene lista) el repeater + (en el banner)
       el fondo de la página. Auto-ocultado: los campos cuyo token {{clave}} ya no
       está en el partial (migrados a repeaters) no se muestran. */
    $partial     = grenvios_partial_raw( 'content-' . $slug );
    $has_partial = is_string( $partial );

    // Repeaters agrupados por su sección.
    $all_reps = function_exists( 'grenvios_repeater_schema' ) ? grenvios_repeater_schema( $slug ) : array();
    $reps_by_section = array();
    foreach ( $all_reps as $rep ) {
      $sk = isset( $rep['section'] ) ? $rep['section'] : '__none__';
      $reps_by_section[ $sk ][] = $rep;
    }
    // Fondos de banner por página → van dentro de la sección "hero" (.page-header).
    $bgs       = grenvios_page_bg_fields();
    $bg_fields = isset( $bgs[ $slug ] ) ? $bgs[ $slug ] : array();

    /* Render de un campo normal (texto/imagen/textarea). */
    $render_field = function ( $key, $label, $type, $current, $hint = '' ) {
      $fid = 'nep-f-' . esc_attr( $key );
      $is_area = ( $type === 'textarea' || $type === 'html' );
      ob_start(); ?>
        <div class="nep-field<?php echo $type === 'image' ? ' nep-field--img' : ''; ?>">
          <label for="<?php echo $fid; ?>"><?php echo esc_html( $label ); ?></label>
          <?php if ( $type === 'html' ) : ?><span class="nep-hint">Puedes usar &lt;br&gt; y &lt;span class="hl"&gt;…&lt;/span&gt;.</span><?php endif; ?>
          <?php if ( $hint ) : ?><span class="nep-hint"><?php echo esc_html( $hint ); ?></span><?php endif; ?>
          <?php if ( $type === 'image' ) : ?>
            <div class="nep-img-wrap">
              <div class="nep-img-preview" <?php if ( $current ) echo 'style="background-image:url(' . esc_url( $current ) . ')"'; ?>>
                <?php if ( ! $current ) echo '<i class="fa-solid fa-image"></i>'; ?>
              </div>
              <input type="hidden" id="<?php echo $fid; ?>" data-field-key="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_url( $current ); ?>" />
              <div class="nep-img-btns">
                <button type="button" class="nep-img-pick" data-target="<?php echo $fid; ?>"><i class="fa-solid fa-upload"></i> <?php echo $current ? 'Cambiar' : 'Seleccionar'; ?></button>
                <button type="button" class="nep-img-remove" data-target="<?php echo $fid; ?>" style="<?php echo $current ? '' : 'display:none'; ?>"><i class="fa-solid fa-trash"></i> Quitar</button>
              </div>
            </div>
          <?php elseif ( $is_area ) : ?>
            <textarea id="<?php echo $fid; ?>" data-field-key="<?php echo esc_attr( $key ); ?>" rows="3"><?php echo esc_textarea( $current ); ?></textarea>
          <?php else : ?>
            <input type="text" id="<?php echo $fid; ?>" data-field-key="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $current ); ?>" />
          <?php endif; ?>
        </div>
      <?php return ob_get_clean();
    };

    /* Render del bloque de un repeater (lista de ítems add/quitar/ordenar), SIN acordeón propio. */
    $render_rep = function ( $rep ) {
      $items = grenvios_repeater( $rep['key'] );
      ob_start(); ?>
      <div class="nep-rep" data-rep-key="<?php echo esc_attr( $rep['key'] ); ?>" data-rep-itemlabel="<?php echo esc_attr( $rep['item_label'] ); ?>" data-sel="<?php echo esc_attr( isset( $rep['sel'] ) ? $rep['sel'] : '' ); ?>">
        <div class="nep-rep-head"><i class="fa-solid fa-layer-group"></i> <?php echo esc_html( $rep['label'] ); ?></div>
        <div class="nep-rep-list"><?php foreach ( $items as $i => $values ) grenvios_nep_rep_item( $rep, $values, $i ); ?></div>
        <button type="button" class="nep-rep-add"><i class="fa-solid fa-plus"></i> <?php echo esc_html( isset( $rep['add_label'] ) ? $rep['add_label'] : 'Agregar' ); ?></button>
        <template class="nep-rep-tpl"><?php grenvios_nep_rep_item( $rep, array(), 0 ); ?></template>
      </div>
      <?php return ob_get_clean();
    };

    // Entradilla, garantías y botones del hero (inc/hero-paginas.php): van en la
    // sección del banner; si la página no tiene una, se abre su propio bloque arriba.
    $hero_html = function_exists( 'grenvios_hero_pagina_campos_html' ) ? grenvios_hero_pagina_campos_html( $slug, $render_field ) : '';
    $es_hero   = function ( $seckey, $sel ) { return $seckey === 'hero' || $sel === '.page-header' || $sel === '.gr-dhero'; };
    $hay_hero  = false;
    foreach ( $sections as $sk => $sec ) if ( $es_hero( $sk, isset( $sec['sel'] ) ? $sec['sel'] : '' ) ) { $hay_hero = true; break; }
    if ( $hero_html !== '' && ! $hay_hero ) : ?>
    <div class="nep-accordion" data-sel=".gr-dhero, .page-header">
      <button class="nep-acc-header" type="button"><span>Banner · texto, garantías y botones</span><i class="fa-solid fa-chevron-down"></i></button>
      <div class="nep-acc-body"><div class="nep-grid"><?php echo $hero_html; ?></div></div>
    </div>
    <?php $hero_html = ''; endif;

    foreach ( $sections as $seckey => $sec ) :
      $sel = ( isset( $sec['sel'] ) && $sec['sel'] ) ? $sec['sel'] : '#ged-' . $seckey;
      // Páginas pintadas por PHP no llevan el ancla #ged-hero: el banner se busca por su clase.
      if ( $seckey === 'hero' && empty( $sec['sel'] ) ) $sel = '#ged-hero, .gr-dhero, .page-header';

      // 1) Campos de texto/imagen de la sección.
      $fields_html = '';
      $skip_token_check = ! empty( $sec['_no_token_check'] );
      foreach ( $sec['fields'] as $key => $f ) {
        if ( ! $skip_token_check && $has_partial && strpos( $partial, '{{' . $key . '}}' ) === false ) continue;
        list( $label, $type, $default ) = $f;
        $fields_html .= $render_field( $key, $label, $type, grenvios_field( $key, $default ) );
      }

      // 2) Fondo del banner (solo en la sección hero/.page-header).
      $bg_html = '';
      if ( $hero_html !== '' && $es_hero( $seckey, $sel ) ) {
        $bg_html  .= $hero_html;
        $hero_html = '';
      }
      if ( $bg_fields && $es_hero( $seckey, $sel ) ) {
        foreach ( $bg_fields as $bkey => $bf ) {
          $bg_html .= $render_field( $bkey, $bf[0], 'image', grenvios_field( $bkey, '' ), 'Déjalo vacío para mantener el fondo del diseño.' );
        }
        $bg_fields = array();
      }

      // 3) Repeater(s) de la sección (lista de ítems).
      $reps_html = '';
      if ( isset( $reps_by_section[ $seckey ] ) ) {
        foreach ( $reps_by_section[ $seckey ] as $rep ) $reps_html .= $render_rep( $rep );
        unset( $reps_by_section[ $seckey ] );
      }

      if ( $fields_html === '' && $bg_html === '' && $reps_html === '' ) continue; ?>
    <div class="nep-accordion" data-sel="<?php echo esc_attr( $sel ); ?>">
      <button class="nep-acc-header" type="button">
        <span><?php echo esc_html( function_exists( 'grenvios_sede_tokens_apply' ) ? grenvios_sede_tokens_apply( $sec['label'] ) : $sec['label'] ); ?></span>
        <i class="fa-solid fa-chevron-down"></i>
      </button>
      <div class="nep-acc-body">
        <?php if ( $fields_html || $bg_html ) : ?><div class="nep-grid"><?php echo $fields_html . $bg_html; ?></div><?php endif; ?>
        <?php echo $reps_html; ?>
      </div>
    </div>
    <?php endforeach; ?>

    <?php
    /* Seguridad: repeaters cuya sección no existiera, y fondo si no hubo sección hero. */
    foreach ( $reps_by_section as $reps ) : foreach ( $reps as $rep ) : ?>
    <div class="nep-accordion" data-sel="<?php echo esc_attr( isset( $rep['sel'] ) ? $rep['sel'] : '' ); ?>">
      <button class="nep-acc-header" type="button"><span><?php echo esc_html( $rep['label'] ); ?></span><i class="fa-solid fa-chevron-down"></i></button>
      <div class="nep-acc-body"><?php echo $render_rep( $rep ); ?></div>
    </div>
    <?php endforeach; endforeach; ?>
    <?php if ( $bg_fields ) : ?>
    <div class="nep-accordion" data-sel=".page-header">
      <button class="nep-acc-header" type="button"><span>Fondos de la página</span><i class="fa-solid fa-chevron-down"></i></button>
      <div class="nep-acc-body"><div class="nep-grid"><?php foreach ( $bg_fields as $bkey => $bf ) echo $render_field( $bkey, $bf[0], 'image', grenvios_field( $bkey, '' ), 'Déjalo vacío para mantener el fondo del diseño.' ); ?></div></div>
    </div>
    <?php endif; ?>

    <?php /* Secciones que añaden otros módulos (datos del país de la ruta). */
    do_action( 'grenvios_editor_secciones', $slug, $post_id, $render_field ); ?>

    <?php /* Ajustes GLOBALES del sitio (colores, redes, encabezado, pie) — en todas las páginas. */
    if ( function_exists( 'grenvios_render_global_sections' ) ) echo grenvios_render_global_sections(); ?>
  </div>
</div>

<script>
window.grenviosEditorCfg = {
  postId:  <?php echo (int) $post_id; ?>,
  nonce:   <?php echo wp_json_encode( wp_create_nonce( 'wp_rest' ) ); ?>,
  restUrl: <?php echo wp_json_encode( rest_url( 'grenvios/v1/save-page' ) ); ?>,
  restUrlAlt: <?php echo wp_json_encode( add_query_arg( 'rest_route', '/grenvios/v1/save-page', home_url( '/' ) ) ); ?>
};
</script>
	<?php
}, 50 );
