<?php
/**
 * Grenvíos — Ajustes GLOBALES del sitio (visibles en todas las páginas)
 * Editables desde el panel flotante "Editar página":
 *   · Colores de marca (principal + oscuro) → variables CSS
 *   · Redes sociales (agregar/quitar por enlace; vacío = oculto)
 *   · Barra superior del encabezado (ícono/enlace extra)
 *   · Pie de página (logo, texto "acerca de", copyright)
 *
 * Almacenamiento: una sola opción `grenvios_globals` (site-wide), no post-meta.
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* Valores por defecto de los ajustes globales. */
function grenvios_globals_defaults() {
	$logo = function_exists( 'grenvios_img_url' ) ? grenvios_img_url( 'logo.svg' ) : get_template_directory_uri() . '/assets/img/logo.svg';
	$wa   = '51900612836';
	if ( function_exists( 'grenvios_biz' ) ) {
		$biz = grenvios_biz();
		$wa  = preg_replace( '/\D/', '', (string) $biz['wa_number'] );
	}
	return array(
		// Colores de marca
		'color_primary'    => '#5e2129',
		'color_dark'       => '#481920',
		// Redes sociales (URL; vacío = oculto)
		'social_facebook'  => 'https://www.facebook.com/Grenviios/',
		'social_instagram' => 'https://www.instagram.com/grenvioss/',
		'social_tiktok'    => 'https://www.tiktok.com/@grenvios',
		'social_whatsapp'  => 'https://wa.me/' . $wa,
		'social_youtube'   => '',
		'social_linkedin'  => '',
		'social_x'         => '',
		// Barra superior del encabezado (ícono/enlace extra)
		'topbar_icon'      => '',
		'topbar_text'      => '',
		'topbar_url'       => '',
		// Llamada final «¿Listo para enviar?» (inc/seo-enlazado.php), igual en todas las páginas
		'cta_title'        => '¿Listo para enviar?',
		'cta_text'         => 'Cotiza en minutos según el peso, el volumen y el destino de tu envío. Sin compromiso.',
		'cta_btn'          => 'Cotizar mi envío',
		'cta_wa'           => 'Escribir por WhatsApp',
		'cta_img'          => '',   // vacío = la foto del repartidor del tema (cta-repartidor-4.webp)
		// Bloque «Continúa tu envío» (inc/seo-enlazado.php)
		'rel_title'        => 'Continúa tu envío',
		'rel_sub'          => 'Encuentra el servicio, destino o guía que necesitas.',
		'rel_btn_cotizar'  => 'Cotizar mi envío',
		'rel_btn_rastrear' => 'Rastrear envío',
		'rel_btn_wa'       => 'Escribir por WhatsApp',
		'rel_sub_c'        => 'Encuentra el siguiente paso sin volver a buscar.',
		'rel_card_t'       => '¿Listo para enviar?',
		'rel_card'         => 'Cotiza con peso, medidas y destino.',
		// Pie de página
		'footer_logo'      => $logo,
		'footer_about'     => 'Grenvíos conecta personas y empresas con el mundo: envíos internacionales de documentos, paquetes y carga, vía aérea y terrestre, desde Lima.',
		'footer_copyright' => 'Grenvíos. Todos los derechos reservados.',
	);
}

/* Todos los ajustes globales (opción + defaults).
 *
 * Filtro `grenvios_globals`: lo usa inc/sedes.php para que cada SEDE (país de
 * origen) tenga sus propias redes sociales y su propio pie. La caché se indexa
 * por sede porque el editor puede pasar de una a otra en la misma petición. */
function grenvios_globals( $sede = null ) {
	$sede = $sede !== null ? $sede : ( function_exists( 'grenvios_sede' ) ? grenvios_sede() : '' );

	static $cache = array();
	if ( isset( $cache[ $sede ] ) ) return $cache[ $sede ];

	$saved = get_option( 'grenvios_globals', array() );
	if ( ! is_array( $saved ) ) $saved = array();
	$all = array_merge( grenvios_globals_defaults(), $saved );

	return $cache[ $sede ] = apply_filters( 'grenvios_globals', $all, $sede );
}

/* Un ajuste global concreto. */
function grenvios_g( $key, $default = '' ) {
	$g = grenvios_globals();
	return isset( $g[ $key ] ) && $g[ $key ] !== '' ? $g[ $key ] : $default;
}

/* Redes sociales disponibles: clave => [ etiqueta, clase de ícono ]. */
function grenvios_social_fields() {
	return array(
		'facebook'  => array( 'Facebook',      'fa-brands fa-facebook-f' ),
		'instagram' => array( 'Instagram',     'fa-brands fa-instagram' ),
		'tiktok'    => array( 'TikTok',        'fa-brands fa-tiktok' ),
		'whatsapp'  => array( 'WhatsApp',      'fa-brands fa-whatsapp' ),
		'youtube'   => array( 'YouTube',       'fa-brands fa-youtube' ),
		'linkedin'  => array( 'LinkedIn',      'fa-brands fa-linkedin-in' ),
		'x'         => array( 'X (Twitter)',   'fa-brands fa-x-twitter' ),
	);
}

/* HTML de la lista de redes sociales para 'header' o 'footer' (vacío si no hay ninguna). */
function grenvios_social_html( $ctx = 'footer' ) {
	$items = '';
	foreach ( grenvios_social_fields() as $key => $meta ) {
		$url = grenvios_g( 'social_' . $key, '' );
		if ( ! $url ) continue;
		$items .= '<li><a href="' . esc_url( $url ) . '" target="_blank" rel="noopener" aria-label="'
			. esc_attr( $meta[0] . ' de Grenvíos' ) . '"><i class="' . esc_attr( $meta[1] ) . '"></i></a></li>';
	}
	if ( $items === '' ) return '';
	$class = ( $ctx === 'header' ) ? 'header-social-share' : 'social-share';
	return '<ul class="' . $class . '">' . $items . '</ul>';
}

/* HTML del ítem extra de la barra superior del encabezado (vacío si no se configuró). */
function grenvios_topbar_extra_html() {
	$icon = grenvios_g( 'topbar_icon', '' );
	$text = grenvios_g( 'topbar_text', '' );
	$url  = grenvios_g( 'topbar_url', '' );
	if ( $icon === '' && $text === '' ) return '';
	$inner = '';
	if ( $icon ) $inner .= '<i class="' . esc_attr( $icon ) . '"></i>';
	if ( $url ) {
		$inner .= '<a href="' . esc_url( $url ) . '">' . esc_html( $text ) . '</a>';
	} elseif ( $text ) {
		$inner .= esc_html( $text );
	}
	return '<li>' . $inner . '</li>';
}

/* Copyright del pie con el año actual. */
function grenvios_footer_copyright_html() {
	$txt = grenvios_g( 'footer_copyright', 'Grenvíos. Todos los derechos reservados.' );
	return '© ' . esc_html( date_i18n( 'Y' ) ) . ' ' . esc_html( $txt );
}

/* Enlaces legales de la barra inferior del pie. Solo salen si la página
 * existe y está publicada: un enlace del pie que lleva a un 404 se repite en
 * todas las páginas del sitio. La de privacidad es la que WordPress tenga
 * marcada en Ajustes → Privacidad; la de términos, la de slug
 * «terminos-y-condiciones». */
function grenvios_footer_legal_html() {
	$links = array();
	$terms = get_page_by_path( 'terminos-y-condiciones' );
	if ( $terms && get_post_status( $terms ) === 'publish' ) {
		$links[] = '<a href="' . esc_url( get_permalink( $terms ) ) . '">Términos y Condiciones</a>';
	}
	$priv = function_exists( 'get_privacy_policy_url' ) ? get_privacy_policy_url() : '';
	if ( $priv !== '' ) {
		$links[] = '<a href="' . esc_url( $priv ) . '">Política de Privacidad</a>';
	}
	return $links ? '<span>' . implode( '</span><span>', $links ) . '</span>' : '';
}

/* ── Colores de marca → variables CSS (sobrescriben common-style.css) ── */
add_action( 'wp_head', function () {
	$rules = '';
	$p = grenvios_g( 'color_primary', '' );
	$d = grenvios_g( 'color_dark', '' );
	if ( $p ) $rules .= '--primary-color:' . sanitize_hex_color( $p ) . ';';
	if ( $d ) $rules .= '--primary-dark:'  . sanitize_hex_color( $d ) . ';';
	if ( $rules ) echo '<style id="grenvios-colors">:root{' . $rules . '}</style>' . "\n";
}, 99 );

/* ── Guardado de los ajustes globales (desde el REST del editor) ── */
function grenvios_globals_save( $data ) {
	if ( ! is_array( $data ) || ! current_user_can( 'edit_theme_options' ) && ! current_user_can( 'edit_posts' ) ) return;
	// Cada sede guarda en su propia opción; la maestra, en la de siempre.
	$option   = function_exists( 'grenvios_globals_option' ) ? grenvios_globals_option() : 'grenvios_globals';
	$defaults = grenvios_globals_defaults();
	$saved    = get_option( $option, array() );
	if ( ! is_array( $saved ) ) $saved = array();
	foreach ( $data as $key => $val ) {
		$key = sanitize_key( $key );
		if ( ! array_key_exists( $key, $defaults ) ) continue;   // solo claves conocidas
		$val = (string) $val;
		if ( $key === 'color_primary' || $key === 'color_dark' ) {
			$clean = sanitize_hex_color( $val );
			if ( ! $clean ) $clean = $defaults[ $key ];
		} elseif ( strpos( $key, 'social_' ) === 0 || $key === 'footer_logo' || $key === 'topbar_url' || $key === 'cta_img' ) {
			$clean = esc_url_raw( $val );
		} elseif ( $key === 'footer_about' || $key === 'cta_text' ) {
			$clean = sanitize_textarea_field( $val );
		} else {
			$clean = sanitize_text_field( $val );
		}
		$saved[ $key ] = $clean;
	}
	update_option( $option, $saved );
}

/* ── Render de las secciones GLOBALES en el panel flotante ── */
function grenvios_render_global_field( $key, $label, $type, $current, $hint = '' ) {
	$fid = 'nep-g-' . preg_replace( '/[^a-z0-9_]+/', '', strtolower( $key ) );
	ob_start(); ?>
	<div class="nep-field<?php echo $type === 'image' ? ' nep-field--img' : ''; ?>">
		<label for="<?php echo esc_attr( $fid ); ?>"><?php echo esc_html( $label ); ?></label>
		<?php if ( $hint ) : ?><span class="nep-hint"><?php echo esc_html( $hint ); ?></span><?php endif; ?>
		<?php if ( $type === 'image' ) : ?>
			<div class="nep-img-wrap">
				<div class="nep-img-preview" <?php if ( $current ) echo 'style="background-image:url(' . esc_url( $current ) . ')"'; ?>>
					<?php if ( ! $current ) echo '<i class="fa-solid fa-image"></i>'; ?>
				</div>
				<input type="hidden" id="<?php echo esc_attr( $fid ); ?>" data-global-key="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_url( $current ); ?>" />
				<div class="nep-img-btns">
					<button type="button" class="nep-img-pick" data-target="<?php echo esc_attr( $fid ); ?>"><i class="fa-solid fa-upload"></i> <?php echo $current ? 'Cambiar' : 'Seleccionar'; ?></button>
					<button type="button" class="nep-img-remove" data-target="<?php echo esc_attr( $fid ); ?>" style="<?php echo $current ? '' : 'display:none'; ?>"><i class="fa-solid fa-trash"></i> Quitar</button>
				</div>
			</div>
		<?php elseif ( $type === 'color' ) : ?>
			<input type="color" id="<?php echo esc_attr( $fid ); ?>" class="nep-color" data-global-key="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $current ); ?>" />
		<?php elseif ( $type === 'textarea' ) : ?>
			<textarea id="<?php echo esc_attr( $fid ); ?>" data-global-key="<?php echo esc_attr( $key ); ?>" rows="3"><?php echo esc_textarea( $current ); ?></textarea>
		<?php else : ?>
			<input type="text" id="<?php echo esc_attr( $fid ); ?>" data-global-key="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $current ); ?>" />
		<?php endif; ?>
	</div>
	<?php return ob_get_clean();
}

function grenvios_render_global_accordion( $label, $sel, $fields_html ) {
	ob_start(); ?>
	<div class="nep-accordion nep-accordion--global" data-sel="<?php echo esc_attr( $sel ); ?>">
		<button class="nep-acc-header" type="button"><span><?php echo esc_html( $label ); ?></span><i class="fa-solid fa-chevron-down"></i></button>
		<div class="nep-acc-body"><div class="nep-grid"><?php echo $fields_html; ?></div></div>
	</div>
	<?php return ob_get_clean();
}

/* Devuelve el HTML de todas las secciones globales (para inyectar al final del panel). */
function grenvios_render_global_sections() {
	$out  = '<div class="nep-global-note"><i class="fa-solid fa-globe"></i> Ajustes del sitio (se aplican en todas las páginas)</div>';

	// 🎨 Colores
	$c  = grenvios_render_global_field( 'color_primary', 'Color principal (marca)', 'color', grenvios_g( 'color_primary', '#5e2129' ) );
	$c .= grenvios_render_global_field( 'color_dark', 'Color principal oscuro (hover/degradado)', 'color', grenvios_g( 'color_dark', '#481920' ) );
	$out .= grenvios_render_global_accordion( '🎨 Colores del sitio', 'header.main-header', $c );

	// 🔗 Redes sociales
	$s = '';
	foreach ( grenvios_social_fields() as $key => $meta ) {
		$s .= grenvios_render_global_field( 'social_' . $key, $meta[0], 'text', grenvios_g( 'social_' . $key, '' ), 'Pega el enlace. Déjalo vacío para ocultar este ícono.' );
	}
	$out .= grenvios_render_global_accordion( '🔗 Redes sociales', '.header-social-share', $s );

	// 🔼 Barra superior del encabezado
	$t  = grenvios_render_global_field( 'topbar_icon', 'Ícono (clase FontAwesome)', 'text', grenvios_g( 'topbar_icon', '' ), 'Ej: fa-solid fa-clock · fa-regular fa-star. Déjalo vacío para no mostrarlo.' );
	$t .= grenvios_render_global_field( 'topbar_text', 'Texto', 'text', grenvios_g( 'topbar_text', '' ) );
	$t .= grenvios_render_global_field( 'topbar_url', 'Enlace (opcional)', 'text', grenvios_g( 'topbar_url', '' ) );
	$out .= grenvios_render_global_accordion( '🔼 Barra superior del encabezado', '.top-header', $t );

	// 📣 Llamada final (bloque «¿Listo para enviar?» al pie de casi todas las páginas)
	$c2  = grenvios_render_global_field( 'cta_title', 'Título', 'text', grenvios_g( 'cta_title', '¿Listo para enviar?' ) );
	$c2 .= grenvios_render_global_field( 'cta_text', 'Texto', 'textarea', grenvios_g( 'cta_text', 'Cotiza en minutos según el peso, el volumen y el destino de tu envío. Sin compromiso.' ) );
	$c2 .= grenvios_render_global_field( 'cta_btn', 'Botón · Cotizar', 'text', grenvios_g( 'cta_btn', 'Cotizar mi envío' ) );
	$c2 .= grenvios_render_global_field( 'cta_wa', 'Botón · WhatsApp', 'text', grenvios_g( 'cta_wa', 'Escribir por WhatsApp' ) );
	$c2 .= grenvios_render_global_field( 'cta_img', 'Imagen (vacía = la foto del repartidor)', 'image', grenvios_g( 'cta_img', '' ) );
	$out .= grenvios_render_global_accordion( '📣 Llamada final «¿Listo para enviar?»', '.grenvios-cta', $c2 );

	// 🧭 «Continúa tu envío» (bloque de enlaces al pie de casi todas las páginas)
	$c3  = grenvios_render_global_field( 'rel_title', 'Título', 'text', grenvios_g( 'rel_title', 'Continúa tu envío' ) );
	$c3 .= grenvios_render_global_field( 'rel_sub', 'Subtítulo', 'text', grenvios_g( 'rel_sub', 'Encuentra el servicio, destino o guía que necesitas.' ) );
	$c3 .= grenvios_render_global_field( 'rel_btn_cotizar', 'Acceso · Cotizar', 'text', grenvios_g( 'rel_btn_cotizar', 'Cotizar mi envío' ) );
	$c3 .= grenvios_render_global_field( 'rel_btn_rastrear', 'Acceso · Rastrear', 'text', grenvios_g( 'rel_btn_rastrear', 'Rastrear envío' ) );
	$c3 .= grenvios_render_global_field( 'rel_btn_wa', 'Acceso · WhatsApp', 'text', grenvios_g( 'rel_btn_wa', 'Escribir por WhatsApp' ) );
	$c3 .= grenvios_render_global_field( 'rel_sub_c', 'Subtítulo (versión compacta)', 'text', grenvios_g( 'rel_sub_c', 'Encuentra el siguiente paso sin volver a buscar.' ) );
	$c3 .= grenvios_render_global_field( 'rel_card_t', 'Tarjeta vino · Título', 'text', grenvios_g( 'rel_card_t', '¿Listo para enviar?' ) );
	$c3 .= grenvios_render_global_field( 'rel_card', 'Tarjeta vino · Texto', 'text', grenvios_g( 'rel_card', 'Cotiza con peso, medidas y destino.' ) );
	$out .= grenvios_render_global_accordion( '🧭 «Continúa tu envío»', '.grenvios-related', $c3 );

	// 🦶 Pie de página
	$f  = grenvios_render_global_field( 'footer_logo', 'Logo del pie de página', 'image', grenvios_g( 'footer_logo', '' ) );
	$f .= grenvios_render_global_field( 'footer_about', 'Texto de presentación', 'textarea', grenvios_g( 'footer_about', '' ) );
	$f .= grenvios_render_global_field( 'footer_copyright', 'Texto de copyright (el año se agrega solo)', 'text', grenvios_g( 'footer_copyright', '' ) );
	$out .= grenvios_render_global_accordion( '🦶 Pie de página', '.footer-section', $f );

	return $out;
}
