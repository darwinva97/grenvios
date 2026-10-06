<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Portada: contenido que faltaba y reparto de autoridad al resto del sitio
 * ══════════════════════════════════════════════════════════════════════════
 *
 * AUDITORÍA DE LA PORTADA (antes de este archivo)
 *
 *   · 1.096 palabras de contenido propio. La de una ruta de país tiene 1.853
 *     gracias al bloque por país; la principal se había quedado corta para la
 *     consulta más competida del negocio.
 *   · 33 enlaces internos, pero solo a 13 destinos distintos. Enlazaba a 5 de
 *     los 9 países (faltaban Argentina, Bolivia, Venezuela y Cuba) y a NINGUNA
 *     de las páginas de apoyo: peso volumétrico, qué se puede enviar, tiempos
 *     de entrega, aduanas, recojo a domicilio, equipaje, compras, empresas…
 *   · `grenvios_render_related_links( 'home' )` ya se llamaba desde
 *     front-page.php, pero 'home' NO estaba en el mapa de enlazado: la página
 *     con más autoridad del sitio no repartía nada.
 *   · Anclas pobres donde sí había enlace: «Leer más», «Ver destinos»,
 *     «Sudamérica». No dicen a Google de qué va la página de destino.
 *   · Sin ninguna sección sobre PRECIO, que es la intención de búsqueda que más
 *     tráfico mueve («cuánto cuesta enviar a…»).
 *
 * LO QUE AÑADE
 *
 *   1) La portada al mapa de enlazado interno, con anclas que son la keyword
 *      objetivo de cada página (el módulo las saca de grenvios_seo_kw).
 *   2) Una tabla de destinos con plazo y modalidad reales, que enlaza a los
 *      NUEVE países con ancla «Envíos a X». Sale de los datos del gestor de
 *      destinos, así que ni se inventa ni se queda desactualizada.
 *   3) Una sección de precio —peso real frente a volumétrico— que enlaza a
 *      cotizar y a la página de peso volumétrico.
 *   4) Tres preguntas más en el bloque de FAQ (que además es el FAQPage del
 *      schema), con la intención de búsqueda que faltaba.
 *
 * En las rutas de país estas secciones NO se pintan: allí ese trabajo ya lo
 * hace el bloque por país (inc/paises-contenido.php), y repetirlo sería
 * contenido duplicado dentro de la propia página.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ¿Estamos en la portada principal (no en la de una ruta de país)? */
function grenvios_home_seo_activa() {
	if ( is_admin() || ! function_exists( 'grenvios_current_slug' ) ) return false;
	/* También en la portada de cada país: mismo diseño que la de Perú. */
	return grenvios_current_slug() === 'home';
}

/* Portada principal (Perú), no la de una ruta de país. */
function grenvios_home_es_principal() {
	return ! function_exists( 'grenvios_hq_pais' ) || grenvios_hq_pais() === '';
}

/* ═══════════════════════════════════════════════════════════════════
 * 1) La portada reparte autoridad
 * ═════════════════════════════════════════════════════════════════ */
add_filter( 'grenvios_related_map', function ( $mapa ) {
	$dest = function_exists( 'grenvios_destinos' ) ? array_keys( grenvios_destinos() ) : array();

	$mapa['home'] = array(
		'Nuestros servicios' => array(
			'servicios/envio-internacional-de-documentos',
			'servicios/envio-internacional-de-paquetes',
			'servicios/carga-internacional',
			'servicios/apostilla-y-traduccion',
		),
		'Destinos más solicitados' => array_map(
			function ( $s ) { return 'destinos/' . $s; },
			array_slice( $dest, 0, 6 )
		),
		'Antes de enviar' => array(
			'servicios/peso-volumetrico',
			'que-se-puede-enviar',
			'tiempos-de-entrega',
			'aduanas-e-impuestos',
			'recojo-a-domicilio-lima',
		),
		'Guías y casos frecuentes' => array(
			'como-enviar-un-paquete-al-extranjero',
			'envio-de-compras',
			'envio-de-equipaje',
			'seguro-de-envios',
			'envios-desde-provincias',
		),
		'Siguiente paso' => array(
			'cotizar',
			'rastreo-de-envios',
			'envios-para-empresas',
			'preguntas-frecuentes',
			'contacto',
			'blog',
		),
	);
	return $mapa;
} );

/* ═══════════════════════════════════════════════════════════════════
 * TEXTOS EDITABLES DE LAS DOS SECCIONES
 * Igual que el resto del sitio: lo de aquí es el valor por defecto y el panel
 * «Editar página» de la portada permite cambiarlo. Las filas de la tabla NO
 * se escriben aquí: salen del gestor de destinos, que ya es editable.
 * ═════════════════════════════════════════════════════════════════ */
function grenvios_home_seo_textos() {
	return array(
		'home_rutas_sub'    => array( 'Rutas · Antetítulo', 'text', 'Rutas desde {{origen_ciudad}}' ),
		'home_rutas_title'  => array( 'Rutas · Título', 'html', 'Plazos y modalidad de cada <span class="hl">destino</span>' ),
		'home_rutas_intro'  => array( 'Rutas · Texto introductorio', 'textarea', 'Estos son los tiempos con los que trabajamos, contados en días hábiles desde el despacho en {{origen_ciudad}}. La aduana del país de destino puede sumar días que no dependen del transporte.' ),
		'home_precio_sub'   => array( 'Precio · Antetítulo', 'text', 'Cuánto cuesta enviar' ),
		'home_precio_title' => array( 'Precio · Título', 'html', 'El precio no sale de la balanza, <span class="hl">sale del espacio</span>' ),
		'home_precio_body'  => array( 'Precio · Cuerpo', 'html', '<p>En un envío internacional se cobra el <strong>mayor</strong> entre el peso real y el peso volumétrico: alto × largo × ancho en centímetros, dividido entre 5000. Por eso una caja grande y liviana puede costar más que una pequeña y pesada, y por eso ajustar el embalaje es la forma más rápida de bajar el precio de tu envío desde {{origen_ciudad}}.</p><p>Sobre ese peso influyen dos cosas más: la modalidad —la vía aérea se paga por rapidez y la terrestre por volumen— y el país de destino, con sus propios impuestos sobre el valor declarado. Te decimos los dos antes de despachar, nunca después.</p>' ),
		'home_precio_list'  => array( 'Precio · Lista de consejos', 'html', '<li><strong>Mide la caja cerrada.</strong> Ya armada y por su parte más ancha: esos centímetros son los que se cobran.</li><li><strong>Declara el valor real.</strong> Es la base del impuesto y del seguro; declarar de menos deja el envío sin cobertura.</li><li><strong>Pregunta por la vía terrestre.</strong> Para bultos voluminosos que no corren prisa suele ser bastante más barata.</li><li><strong>Junta tus envíos.</strong> Varias compras en un solo bulto pagan un solo flete.</li>' ),
	);
}

add_filter( 'grenvios_text_registry', function ( $reg ) {
	if ( ! isset( $reg['home'] ) ) return $reg;
	/* Estas secciones solo se pintan en la ruta principal (en las de país ese
	 * trabajo lo hace el bloque por país), así que tampoco se ofrecen para
	 * editar allí: un campo que no se ve en la página confunde más que ayuda.
	 * En el admin y en las peticiones REST sí se registran, para que el guardado
	 * conozca el tipo de cada campo. */
	if ( ! is_admin() && function_exists( 'grenvios_hq_pais' ) && grenvios_hq_pais() !== '' ) return $reg;

	$t = grenvios_home_seo_textos();

	/* Dos secciones, una por bloque, para que el botón de «llevar a la sección»
	 * salte al sitio correcto. `_no_token_check`: estos campos se pintan desde
	 * PHP, no como tokens {{campo}} del partial. */
	$reg['home']['sections']['homeseo_precio'] = array(
		'label'  => 'Inicio · Cuánto cuesta enviar',
		'_no_token_check' => true,
		'sel'    => '.gr-home-precio',
		'fields' => array(
			'home_precio_sub'   => $t['home_precio_sub'],
			'home_precio_title' => $t['home_precio_title'],
			'home_precio_body'  => $t['home_precio_body'],
			'home_precio_list'  => $t['home_precio_list'],
			'home_precio_img'   => array( 'Precio · Foto (vacía = foto de ejemplo de la caja)', 'image', '' ),
			'home_precio_badge' => array( 'Precio · Fórmula sobre la foto', 'text', 'alto × largo × ancho ÷ 5.000' ),
		),
	);
	$reg['home']['sections']['homeseo_rutas'] = array(
		'label'  => 'Inicio · Plazos por destino',
		'_no_token_check' => true,
		'sel'    => '.gr-home-rutas',
		'fields' => array(
			'home_rutas_sub'   => $t['home_rutas_sub'],
			'home_rutas_title' => $t['home_rutas_title'],
			'home_rutas_intro' => $t['home_rutas_intro'],
			'home_rutas_img'   => array( 'Rutas · Imagen (vacía = avión en pista)', 'image', '' ),
			'home_rutas_g1'    => array( 'Rutas · Título del grupo terrestre', 'text', 'Sudamérica' ),
			'home_rutas_g2'    => array( 'Rutas · Título del grupo aéreo', 'text', 'Internacional' ),
			'home_rutas_dest'  => array( 'Rutas · Etiqueta de la ruta destacada', 'text', 'Ruta destacada' ),
			'home_rutas_pie'   => array( 'Rutas · Texto del pie', 'text', '¿Tu destino no está en la lista? Trabajamos más de 30 países:' ),
			'home_rutas_link'  => array( 'Rutas · Enlace del pie', 'text', 'consulta todos los destinos' ),
			'home_rutas_btn'   => array( 'Rutas · Botón del pie', 'text', 'pide tu cotización.' ),
		),
	);
	return $reg;
} );

/* ═══════════════════════════════════════════════════════════════════
 * 2) Tabla de destinos con plazos reales
 *
 * Cumple tres funciones a la vez: contenido único (los plazos no los tiene
 * nadie más), enlace a los nueve países con ancla de keyword, y respuesta a
 * «cuánto demora un envío a …» sin salir de la portada.
 * ═════════════════════════════════════════════════════════════════ */
function grenvios_home_tabla_destinos() {
	if ( ! function_exists( 'grenvios_destinos' ) ) return '';
	$dest = grenvios_destinos();
	if ( ! $dest ) return '';

	/* Diseño de la maqueta del cliente (2026-10-01, skill grenvios-landing):
	 * cabecera + imagen; dos paneles (rutas con vía terrestre / solo aéreas)
	 * con filas de destino, vía, plazo y entrega; la ruta terrestre más rápida
	 * sale destacada; pie rosado con enlace y botón. Todos los datos salen del
	 * gestor de destinos: aquí no se escribe ningún plazo a mano. */
	$t     = grenvios_home_seo_textos();
	$sub   = grenvios_field( 'home_rutas_sub',   $t['home_rutas_sub'][2] );
	$tit   = grenvios_field( 'home_rutas_title', $t['home_rutas_title'][2] );
	$intro = grenvios_field( 'home_rutas_intro', $t['home_rutas_intro'][2] );
	$img   = trim( (string) grenvios_field( 'home_rutas_img', '' ) );
	if ( $img === '' ) $img = get_template_directory_uri() . '/assets/img/hero-home.jpg';
	$g1    = grenvios_field( 'home_rutas_g1', 'Sudamérica' );
	$g2    = grenvios_field( 'home_rutas_g2', 'Internacional' );
	$etq   = grenvios_field( 'home_rutas_dest', 'Ruta destacada' );
	$pie   = grenvios_field( 'home_rutas_pie', '¿Tu destino no está en la lista? Trabajamos más de 30 países:' );
	$lnk   = grenvios_field( 'home_rutas_link', 'consulta todos los destinos' );
	$btn   = grenvios_field( 'home_rutas_btn', 'pide tu cotización.' );

	$grupos = array( 'terr' => array(), 'aire' => array() );
	foreach ( $dest as $slug => $d ) {
		$terr = stripos( (string) $d['modos'], 'terrestre' ) !== false;
		$grupos[ $terr ? 'terr' : 'aire' ][ $slug ] = $d;
	}
	/* Destacada: la ruta terrestre de menor plazo (primer número del campo). */
	$destacada = ''; $min = PHP_INT_MAX;
	foreach ( $grupos['terr'] as $slug => $d ) {
		if ( preg_match( '/\d+/', (string) $d['tiempo'], $m ) && (int) $m[0] < $min ) { $min = (int) $m[0]; $destacada = $slug; }
	}

	$fila = function ( $slug, $d ) use ( $destacada, $etq ) {
		$url  = function_exists( 'grenvios_ficha_destino_url' ) ? grenvios_ficha_destino_url( $slug ) : grenvios_url_base() . '/destinos/' . $slug . '/';
		$aire = stripos( (string) $d['modos'], 'aére' ) !== false || stripos( (string) $d['modos'], 'aere' ) !== false;
		$terr = stripos( (string) $d['modos'], 'terrestre' ) !== false;
		$via  = ( $aire ? '<i class="fa-solid fa-plane" aria-hidden="true"></i>' : '' )
			. ( $aire && $terr ? '<span class="gr-rt-mas">+</span>' : '' )
			. ( $terr ? '<i class="fa-solid fa-truck" aria-hidden="true"></i>' : '' );
		$tiempo = trim( (string) $d['tiempo'] );
		if ( preg_match( '/^(\d+(?:\s*(?:a|-|–)\s*\d+)?)\s*(.*)$/u', $tiempo, $m ) ) {
			$plazo = '<strong>' . esc_html( $m[1] ) . '</strong><span>' . esc_html( $m[2] ) . '</span>';
		} else {
			$plazo = '<strong>' . esc_html( $tiempo ) . '</strong>';
		}
		$ent  = trim( (string) $d['entrega'] );
		$casa = stripos( $ent, 'domicilio' ) !== false || stripos( $ent, 'puerta' ) !== false;
		$ic_e = $casa ? 'fa-house' : 'fa-building';
		$es_d = $slug === $destacada;
		return '<a class="gr-rt-fila' . ( $es_d ? ' is-destacada' : '' ) . '" href="' . esc_url( $url ) . '">'
			. '<span class="gr-rt-dest"><strong>Envíos a ' . esc_html( $d['title'] ) . '</strong>'
			. ( $es_d ? '<span class="gr-rt-etq"><i class="fa-regular fa-star" aria-hidden="true"></i> ' . esc_html( $etq ) . '</span>' : '' ) . '</span>'
			. '<span class="gr-rt-via"><span class="gr-rt-via-ic">' . $via . '</span><span>' . esc_html( $d['modos'] ) . '</span></span>'
			. '<span class="gr-rt-plazo">' . $plazo . '</span>'
			. '<span class="gr-rt-ent"><i class="fa-regular ' . $ic_e . '" aria-hidden="true"></i> ' . esc_html( $ent ) . '</span>'
			. '</a>';
	};
	$panel = function ( $titulo, $icono, $items, $retardo ) use ( $fila ) {
		if ( ! $items ) return '';
		$h = '<div class="gr-rt-panel wow fade-in-bottom" data-wow-delay="' . $retardo . 'ms">'
			. '<div class="gr-rt-cab"><span class="gr-rt-cab-ic" aria-hidden="true"><i class="fa-regular ' . $icono . '"></i></span><h3>' . esc_html( $titulo ) . '</h3></div>'
			. '<div class="gr-rt-lista">';
		foreach ( $items as $slug => $d ) $h .= $fila( $slug, $d );
		return $h . '</div></div>';
	};

	return '<section class="gr-home-rutas gr-home-rutas--v2 padding-bottom"><div class="container">'
		. '<div class="gr-rt-top">'
		. '<div class="section-heading mb-0">'
		. ( trim( (string) $sub ) !== '' ? '<h3 class="sub-heading">' . esc_html( $sub ) . '</h3>' : '' )
		. '<h2>' . wp_kses_post( $tit ) . '</h2>'
		. ( trim( (string) $intro ) !== '' ? '<p>' . wp_kses_post( $intro ) . '</p>' : '' )
		. '</div>'
		. '<figure class="gr-rt-foto wow fade-in-right" data-wow-delay="150ms"><img src="' . esc_url( $img ) . '" alt="" loading="lazy" decoding="async"></figure>'
		. '</div>'
		. '<div class="gr-rt-grid">'
		. $panel( $g1, 'fa-earth-americas', $grupos['terr'], 100 )
		. $panel( $g2, 'fa-globe', $grupos['aire'], 200 )
		. '</div>'
		. '<div class="gr-rutas-pie gr-rt-pie wow fade-in-bottom" data-wow-delay="150ms"><span class="gr-rt-pie-ic" aria-hidden="true"><i class="fa-regular fa-comment"></i></span>'
		. '<p>' . esc_html( $pie ) . ' <a href="' . esc_url( grenvios_url_base() . '/destinos/' ) . '">' . esc_html( $lnk ) . '</a> o</p>'
		. '<a class="default-btn" href="' . esc_url( grenvios_url_base() . '/cotizar/' ) . '">' . esc_html( $btn ) . ' <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div>'
		. '</div></section>';
}

/* ═══════════════════════════════════════════════════════════════════
 * 3) Cuánto cuesta enviar: la intención de búsqueda que faltaba
 * ═════════════════════════════════════════════════════════════════ */
function grenvios_home_precio() {
	$home = grenvios_url_base();
	$t    = grenvios_home_seo_textos();

	$sub   = grenvios_field( 'home_precio_sub',   $t['home_precio_sub'][2] );
	$tit   = grenvios_field( 'home_precio_title', $t['home_precio_title'][2] );
	$body  = grenvios_field( 'home_precio_body',  $t['home_precio_body'][2] );
	$lista = grenvios_field( 'home_precio_list',  $t['home_precio_list'][2] );
	if ( trim( wp_strip_all_tags( (string) $body ) ) === '' ) return '';   // vaciada a propósito

	/* Diseño de la maqueta del cliente (2026-10-01, skill grenvios-landing):
	 * texto + foto con la fórmula, ejemplo real a lo ancho, cuatro consejos en
	 * fila con icono y número, y los botones al cierre. */
	$img = trim( (string) grenvios_field( 'home_precio_img', '' ) );
	if ( $img === '' && function_exists( 'grenvios_ej_img' ) ) $img = grenvios_ej_img( 'caja' );
	$badge = grenvios_field( 'home_precio_badge', 'alto × largo × ancho ÷ 5.000' );

	$iconos = array( 'fa-ruler', 'fa-shield-check', 'fa-truck', 'fa-boxes-stacked' );
	$pasos  = '';
	if ( trim( wp_strip_all_tags( (string) $lista ) ) !== '' && preg_match_all( '~<li>(.*?)</li>~s', $lista, $m ) ) {
		foreach ( $m[1] as $i => $li ) {
			$pasos .= '<li class="wow fade-in-bottom" data-wow-delay="' . ( 100 + $i * 110 ) . 'ms">'
				. '<span class="gr-hp-n">' . ( $i + 1 ) . '</span>'
				. '<span class="gr-hp-ic" aria-hidden="true"><i class="fa-regular ' . esc_attr( isset( $iconos[ $i ] ) ? $iconos[ $i ] : 'fa-circle-check' ) . '"></i></span>'
				. '<span class="gr-hp-tx">' . wp_kses_post( $li ) . '</span></li>';
		}
	}

	return '<section class="gr-home-precio gr-home-precio--v2 padding"><div class="container">'
		. '<div class="gr-hp-top">'
		. '<div class="gr-hp-texto"><div class="section-heading mb-20">'
		. ( trim( (string) $sub ) !== '' ? '<h3 class="sub-heading">' . esc_html( $sub ) . '</h3>' : '' )
		. '<h2>' . wp_kses_post( $tit ) . '</h2></div>'
		. wp_kses_post( $body )
		. '</div>'
		. ( $img !== ''
			? '<figure class="gr-hp-foto wow fade-in-right" data-wow-delay="150ms"><img src="' . esc_url( $img ) . '" alt="" loading="lazy" decoding="async">'
				. '<div class="gr-hp-badge"><span class="gr-hp-badge-ic" aria-hidden="true"><i class="fa-regular fa-cube"></i></span><p>' . esc_html( $badge ) . '</p></div></figure>'
			: '' )
		. '</div>'
		. ( function_exists( 'grenvios_hm_ejemplo_html' ) ? grenvios_hm_ejemplo_html() : '' )
		. ( $pasos !== '' ? '<ul class="gr-precio-list gr-hp-pasos">' . $pasos . '</ul>' : '' )
		. '<div class="btn-group gr-hp-btns">'
		. '<a href="' . esc_url( $home . '/cotizar/' ) . '" class="default-btn">Cotiza tu envío <i class="fa-solid fa-arrow-right"></i></a> '
		. '<a href="' . esc_url( $home . '/servicios/peso-volumetrico/' ) . '" class="gr-link-under">Cómo se calcula el peso volumétrico</a>'
		. '</div>'
		. '</div></section>';
}

/* Se imprimen desde front-page.php, antes de las guías y del enlazado. */
function grenvios_home_seo_render() {
	if ( ! grenvios_home_seo_activa() ) return;
	echo grenvios_apply_media_overrides( grenvios_home_precio() . grenvios_home_tabla_destinos() );
}

/* ═══════════════════════════════════════════════════════════════════
 * 4) Más preguntas en la portada principal (también van al FAQPage)
 * ═════════════════════════════════════════════════════════════════ */
add_filter( 'grenvios_page_faqs', function ( $faqs, $slug ) {
	/* Preguntas generales: solo en Perú. Las rutas tienen sus FAQ por país y
	 * estas se repetirían igual en las nueve portadas. */
	if ( $slug !== 'home' || ! grenvios_home_seo_activa() || ! grenvios_home_es_principal() ) return $faqs;

	$extra = array(
		array(
			'¿Cuánto cuesta enviar un paquete al extranjero desde {{origen_ciudad}}?',
			'Se cobra el mayor entre el peso real y el peso volumétrico (alto × largo × ancho en cm ÷ 5000). Sobre ese peso influyen la modalidad —aérea o terrestre— y el país de destino. Envíanos peso, medidas y destino y te damos precio cerrado en minutos.',
		),
		array(
			'¿Qué no se puede enviar al extranjero?',
			'Por vía aérea no viajan líquidos, alimentos, aerosoles ni artículos con batería interna; por vía terrestre se admiten algunos de ellos según el país. Cada aduana tiene además su propia lista, y la revisamos contigo antes de despachar.',
		),
		array(
			'¿Recogen el paquete en mi domicilio?',
			'Sí. Coordinamos el recojo en tu casa, oficina o donde esté tu proveedor dentro de {{origen_ciudad}}, lo pesamos, lo embalamos y lo despachamos. El recojo no altera el plazo de tránsito.',
		),
	);

	return array_merge( (array) $faqs, $extra );
}, 20, 2 );
