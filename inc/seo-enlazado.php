<?php
/**
 * Grenvíos — Enlazado interno (internal linking).
 *
 * El enlazado interno es lo que convierte 20 páginas sueltas en una estructura que
 * Google entiende: reparte autoridad, define de qué trata cada página (por el texto
 * del enlace) y guía al usuario hacia la cotización. Aquí se resuelve en tres capas:
 *
 *   1. ENLAZADO AUTOMÁTICO POR KEYWORD
 *      Si en el texto de una página aparece la keyword objetivo de OTRA página, la
 *      primera aparición se convierte en enlace. Con límites estrictos: máximo N
 *      por página, una vez por destino, nunca dentro de títulos ni de otros
 *      enlaces, y nunca hacia la propia página.
 *
 *   2. BLOQUE "ENLACES RELACIONADOS"
 *      Al final del contenido, enlaces cruzados según el modelo pillar-cluster:
 *      servicio ↔ destinos, destino ↔ servicios, todo ↔ cotizar.
 *
 *   3. INFORME
 *      Cuántas páginas enlazan a cada página (entrantes) y cuántas salen. Una
 *      página sin enlaces entrantes es huérfana: Google la rastrea poco y la
 *      posiciona peor. Se ve en «SEO por página».
 *
 * Todo es consciente del idioma: los enlaces de la versión en inglés apuntan a
 * páginas en inglés (ver inc/i18n-links.php).
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* Máximo de enlaces automáticos insertados por página. Pocos y buenos: un texto
 * plagado de enlaces se lee peor y diluye la señal de cada uno. */
function grenvios_autolink_max() {
	return (int) apply_filters( 'grenvios_autolink_max', 5 );
}

/* ══════════════════════════════════════
   1) MAPA keyword → página (por idioma)
══════════════════════════════════════ */
function grenvios_link_map( $lang = null ) {
	$lang = $lang ? $lang : ( function_exists( 'grenvios_i18n_current' ) ? grenvios_i18n_current() : '' );

	$cache = get_transient( 'grenvios_link_map' );
	if ( is_array( $cache ) && isset( $cache[ $lang ] ) ) return $cache[ $lang ];
	if ( ! is_array( $cache ) ) $cache = array();

	$args = array( 'post_type' => 'page', 'post_status' => 'publish', 'numberposts' => -1 );
	if ( function_exists( 'grenvios_i18n_active' ) && grenvios_i18n_active() && $lang ) $args['lang'] = $lang;

	$map = array();
	foreach ( get_posts( $args ) as $p ) {
		if ( (int) get_option( 'page_on_front' ) === (int) $p->ID ) continue;   // la home ya se enlaza desde el logo
		// Los espejos (inc/paises-espejos.php) no reciben enlaces: su canónica es otra página.
		if ( function_exists( 'grenvios_espejo_es' ) && grenvios_espejo_es( $p->ID ) ) continue;
		$kw = function_exists( 'grenvios_seo_kw' ) ? grenvios_seo_kw( $p->ID ) : '';
		if ( trim( $kw ) === '' ) continue;
		$map[] = array(
			'id'    => (int) $p->ID,
			'kw'    => $kw,
			'url'   => get_permalink( $p ),
			'title' => $p->post_title,
		);
	}
	// Keywords más largas primero: "envío de paquetes a ecuador" debe ganarle a
	// "envío de paquetes" cuando ambas encajan en el mismo texto.
	usort( $map, function ( $a, $b ) { return mb_strlen( $b['kw'] ) - mb_strlen( $a['kw'] ); } );

	$cache[ $lang ] = $map;
	set_transient( 'grenvios_link_map', $cache, HOUR_IN_SECONDS * 6 );
	return $map;
}

/* El mapa se invalida cuando cambia cualquier página. */
add_action( 'save_post_page', function () { delete_transient( 'grenvios_link_map' ); } );
add_action( 'deleted_post',   function () { delete_transient( 'grenvios_link_map' ); } );

/* ══════════════════════════════════════
   2) ENLAZADO AUTOMÁTICO EN EL CONTENIDO
══════════════════════════════════════ */
add_filter( 'grenvios_content_html', 'grenvios_autolink_html', 20, 1 );

/* Las ENTRADAS del blog (las guías) se renderizan con the_content() desde
 * single.php, que no pasa por `grenvios_content_html`. Sin este enganche el
 * contenido de apoyo —cuyo propósito es justamente derivar autoridad hacia las
 * páginas de servicio— era el único que no recibía enlazado automático.
 * Prioridad 25: después de wpautop/shortcodes (10-11) y antes de que
 * inc/i18n-links.php reescriba los href al idioma activo (30). */
add_filter( 'the_content', function ( $html ) {
	if ( is_admin() || ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) return $html;
	return grenvios_autolink_html( $html );
}, 25 );

function grenvios_autolink_html( $html ) {
	if ( is_admin() || ! is_singular() ) return $html;
	$here = (int) get_queried_object_id();
	$map  = grenvios_link_map();
	if ( empty( $map ) ) return $html;

	$max   = grenvios_autolink_max();
	$done  = array();   // un enlace por página destino, como mucho
	$count = 0;

	$parts = preg_split( '/(<[^>]+>)/s', $html, -1, PREG_SPLIT_DELIM_CAPTURE );
	if ( ! is_array( $parts ) ) return $html;

	$skip = 0;   // dentro de <a>, títulos, script/style, botones: no se enlaza
	foreach ( $parts as $i => $part ) {
		if ( $part === '' ) continue;

		if ( $part[0] === '<' ) {
			if ( preg_match( '/^<\s*(a|h1|h2|h3|script|style|button|option|select|textarea)\b/i', $part ) && substr( $part, -2 ) !== '/>' ) $skip++;
			if ( preg_match( '/^<\s*\/\s*(a|h1|h2|h3|script|style|button|option|select|textarea)\b/i', $part ) && $skip > 0 ) $skip--;
			continue;
		}
		if ( $skip > 0 || $count >= $max ) continue;
		if ( mb_strlen( trim( $part ) ) < 25 ) continue;   // fragmentos sueltos: no

		foreach ( $map as $t ) {
			if ( $count >= $max ) break;
			if ( $t['id'] === $here || isset( $done[ $t['id'] ] ) ) continue;

			$kw = trim( $t['kw'] );
			if ( mb_strlen( $kw ) < 8 ) continue;   // keywords muy cortas: demasiado ruido

			// Coincidencia exacta de la frase, respetando límites de palabra y
			// sin distinguir mayúsculas ni tildes.
			$pat = '/(?<![\w>])(' . preg_quote( $kw, '/' ) . ')(?![\w<])/iu';
			if ( ! preg_match( $pat, $part, $m, PREG_OFFSET_CAPTURE ) ) continue;

			$anchor = '<a href="' . esc_url( $t['url'] ) . '" title="' . esc_attr( $t['title'] ) . '">' . $m[1][0] . '</a>';
			$part   = substr( $part, 0, $m[1][1] ) . $anchor . substr( $part, $m[1][1] + strlen( $m[1][0] ) );

			$done[ $t['id'] ] = true;
			$count++;
		}
		$parts[ $i ] = $part;
	}
	return implode( '', $parts );
}

/* ══════════════════════════════════════
   3) BLOQUE "ENLACES RELACIONADOS"
   Modelo pillar-cluster: cada página empuja a las que le convienen y recibe
   enlaces de vuelta. Se imprime desde page.php, antes de las FAQ.
══════════════════════════════════════ */

/* Qué enlazar desde cada tipo de página (slug maestro en español). */
function grenvios_related_for( $slug ) {
	$serv = array(
		'envio-internacional-de-documentos',
		'envio-internacional-de-paquetes',
		'carga-internacional',
		'apostilla-y-traduccion',
	);
	$dest = function_exists( 'grenvios_destinos' ) ? array_keys( grenvios_destinos() ) : array();

	// Página de destino (país): sus servicios + países vecinos del mismo continente.
	if ( function_exists( 'grenvios_destinos' ) ) {
		$all = grenvios_destinos();
		if ( isset( $all[ $slug ] ) ) {
			$cont  = isset( $all[ $slug ]['continente'] ) ? $all[ $slug ]['continente'] : '';
			$otros = array();
			foreach ( $all as $s => $d ) {
				if ( $s === $slug ) continue;
				if ( $cont && isset( $d['continente'] ) && $d['continente'] !== $cont ) continue;
				$otros[] = 'destinos/' . $s;
				if ( count( $otros ) >= 4 ) break;
			}
			$mio = isset( $all[ $slug ]['servicio'] ) ? $all[ $slug ]['servicio'] : 'envio-internacional-de-paquetes';
			/* El hub cierra la lista de destinos: es la página padre de todas
			 * las fichas y no recibía enlace desde ninguna de ellas. */
			$otros[] = 'destinos';
			$grupo = array(
				'Servicios para este destino' => array( 'servicios/' . $mio, 'servicios/carga-internacional', 'servicios/apostilla-y-traduccion' ),
				'Otros destinos'              => $otros,
				'Siguiente paso'              => array( 'cotizar', 'rastreo-de-envios' ),
			);
			/* También por el filtro: así cada ficha enlaza a su región
			 * (inc/paginas-regiones.php). */
			$m = (array) apply_filters( 'grenvios_related_map', array( $slug => $grupo ), $slug );
			return isset( $m[ $slug ] ) ? $m[ $slug ] : $grupo;
		}
	}

	// Páginas de servicio: destinos más buscados + los otros servicios.
	if ( in_array( $slug, $serv, true ) ) {
		$otros = array_values( array_diff( $serv, array( $slug ) ) );
		$top   = array_slice( $dest, 0, 5 );
		$grupo = array(
			'Destinos más solicitados' => array_map( function ( $s ) { return 'destinos/' . $s; }, $top ),
			'Otros servicios'          => array_map( function ( $s ) { return 'servicios/' . $s; }, $otros ),
			'Herramientas'             => array( 'servicios/peso-volumetrico', 'que-se-puede-enviar' ),
			'Siguiente paso'           => array( 'cotizar', 'envios-para-empresas' ),
		);
		/* También pasan por el filtro: sin él, ningún módulo podía dar a estas
		 * cuatro páginas —las de más autoridad— enlaces hacia sus páginas nuevas. */
		$m = (array) apply_filters( 'grenvios_related_map', array( $slug => $grupo ), $slug );
		return isset( $m[ $slug ] ) ? $m[ $slug ] : $grupo;
	}

	// Pilares y páginas transaccionales.
	$mapa = array(
		'servicios'            => array( 'Nuestros servicios' => array_map( function ( $s ) { return 'servicios/' . $s; }, $serv ), 'Siguiente paso' => array( 'cotizar', 'destinos' ) ),
		'destinos'             => array( 'Servicios' => array_map( function ( $s ) { return 'servicios/' . $s; }, $serv ), 'Siguiente paso' => array( 'cotizar' ) ),
		'cotizar'              => array( 'Antes de cotizar' => array_map( function ( $s ) { return 'servicios/' . $s; }, array_slice( $serv, 0, 3 ) ), 'También te sirve' => array( 'preguntas-frecuentes', 'rastreo-de-envios' ) ),
		'rastreo-de-envios'    => array( 'Servicios' => array_map( function ( $s ) { return 'servicios/' . $s; }, array_slice( $serv, 0, 2 ) ), 'Siguiente paso' => array( 'cotizar', 'contacto' ) ),
		'preguntas-frecuentes' => array( 'Servicios' => array_map( function ( $s ) { return 'servicios/' . $s; }, $serv ), 'Siguiente paso' => array( 'cotizar', 'contacto' ) ),
		'envios-para-empresas' => array( 'Servicios para tu empresa' => array_map( function ( $s ) { return 'servicios/' . $s; }, $serv ), 'Siguiente paso' => array( 'cotizar', 'contacto' ) ),
		'peso-volumetrico'     => array( 'Servicios donde aplica' => array( 'servicios/envio-internacional-de-paquetes', 'servicios/carga-internacional', 'servicios/envio-de-equipaje' ), 'Siguiente paso' => array( 'cotizar', 'que-se-puede-enviar' ) ),
		'que-se-puede-enviar'  => array( 'Servicios' => array( 'servicios/envio-internacional-de-paquetes', 'servicios/envio-internacional-de-documentos', 'servicios/carga-internacional' ), 'Siguiente paso' => array( 'cotizar', 'preguntas-frecuentes' ) ),
		'envio-de-equipaje'    => array( 'Te puede interesar' => array( 'servicios/peso-volumetrico', 'que-se-puede-enviar', 'servicios/envio-internacional-de-paquetes' ), 'Siguiente paso' => array( 'cotizar' ) ),
		'tiempos-de-entrega'   => array( 'Destinos' => array( 'destinos' ), 'Antes de enviar' => array( 'que-se-puede-enviar', 'servicios/peso-volumetrico' ), 'Siguiente paso' => array( 'cotizar' ) ),
		'como-enviar-un-paquete-al-extranjero' => array( 'Lo que necesitas' => array( 'servicios/peso-volumetrico', 'que-se-puede-enviar', 'tiempos-de-entrega' ), 'Siguiente paso' => array( 'cotizar', 'recojo-a-domicilio-lima' ) ),
		'recojo-a-domicilio-lima' => array( 'Servicios' => array( 'servicios/envio-internacional-de-paquetes', 'servicios/envio-de-equipaje' ), 'Siguiente paso' => array( 'cotizar', 'contacto' ) ),
		'envio-de-compras'     => array( 'Te puede interesar' => array( 'servicios/envio-internacional-de-paquetes', 'que-se-puede-enviar', 'servicios/peso-volumetrico' ), 'Siguiente paso' => array( 'cotizar' ) ),
		'nosotros'             => array( 'Lo que hacemos' => array_map( function ( $s ) { return 'servicios/' . $s; }, array_slice( $serv, 0, 3 ) ), 'Siguiente paso' => array( 'destinos', 'cotizar' ) ),
		'contacto'             => array( 'Mientras tanto' => array( 'cotizar', 'preguntas-frecuentes', 'rastreo-de-envios' ) ),
	);
	/* Punto de extensión: los módulos que registran páginas propias añaden aquí
	 * sus bloques, y también los enlaces ENTRANTES desde las páginas que ya
	 * posicionan. Una página fuera del menú depende por completo de esto para no
	 * quedar huérfana (ver inc/paginas-seo-extra.php). */
	$mapa = (array) apply_filters( 'grenvios_related_map', $mapa, $slug );

	return isset( $mapa[ $slug ] ) ? $mapa[ $slug ] : array();
}

/* Imprime el bloque. Se llama desde page.php. */
function grenvios_render_related_links( $slug ) {
	if ( ! apply_filters( 'grenvios_related_links_enabled', true ) ) return;
	$groups = grenvios_related_for( $slug );
	if ( empty( $groups ) ) return;

	/* Caché del bloque ya montado.
	 *
	 * Armarlo cuesta una búsqueda de página, un permalink y la keyword objetivo
	 * por cada enlace. En la portada, que desde que reparte autoridad enlaza a
	 * veinticinco páginas, eso eran más de un segundo en cada visita. El
	 * contenido solo cambia cuando se edita una página, así que se guarda y se
	 * tira la caché al guardar (ver más abajo). */
	/* La clave lleva la versión de caché del tema: sin ella, este bloque se
	 * quedaba 12 h con el HTML anterior aunque cambiaran los textos ancla, y
	 * no había forma de refrescarlo salvo esperar (inc/cache-html.php). */
	$ver = function_exists( 'grenvios_cache_ver' ) ? grenvios_cache_ver() : ( defined( 'LOGISKO_VER' ) ? LOGISKO_VER : '' );
	$ck = 'gr_rel_' . md5( $slug . '|' . ( function_exists( 'grenvios_i18n_current' ) ? grenvios_i18n_current() : '' ) . '|' . (int) get_queried_object_id() . '|' . $ver );
	$hit = get_transient( $ck );
	if ( is_string( $hit ) && $hit !== '' ) { echo $hit; return; }

	$t = function ( $s ) { return function_exists( 'grenvios_t' ) ? grenvios_t( $s ) : $s; };

	$html = ''; $grupos_html = array();
	foreach ( $groups as $label => $paths ) {
		$items = '';
		foreach ( (array) $paths as $path ) {
			$page = get_page_by_path( $path );
			if ( ! $page ) continue;
			// Versión del idioma activo.
			if ( function_exists( 'grenvios_i18n_page_translation_by_path' ) && function_exists( 'grenvios_i18n_is_default' ) && ! grenvios_i18n_is_default() ) {
				$tid  = grenvios_i18n_page_translation_by_path( $path, grenvios_i18n_current() );
				if ( $tid ) $page = get_post( $tid );
			}
			if ( ! $page || $page->post_status !== 'publish' ) continue;
			if ( (int) $page->ID === (int) get_queried_object_id() ) continue;

			/* Texto ancla = keyword objetivo de la página destino, que es la
			 * señal que Google lee para saber de qué va la página enlazada.
			 *
			 * Antes solo se usaba si estaba escrita A MANO, y no lo estaba en
			 * ninguna de las 350 páginas: el bloque enlazaba con el título del
			 * menú —«Cotizar», «Destinos», «Envío de Documentos»— igual en las
			 * diez rutas. Ahora vale también la keyword por defecto
			 * (inc/seo-keywords-defecto.php), que en una ruta de país lleva el
			 * destino: «Envío de documentos a Chile». El título queda de
			 * respaldo por si una página se queda sin keyword. */
			$anchor = '';
			if ( function_exists( 'grenvios_seo_kw' ) ) $anchor = trim( (string) grenvios_seo_kw( $page->ID ) );
			if ( $anchor === '' ) $anchor = $page->post_title;

			$items .= '<li><a href="' . esc_url( get_permalink( $page ) ) . '">' . esc_html( ucfirst( $anchor ) ) . '</a></li>';
		}
		if ( $items === '' ) continue;
		/* Icono del grupo por su nombre (maqueta 2026-10-02, skill grenvios-landing). */
		$ic = grenvios_related_icono( $label );
		$grp = '<div class="grenvios-related-group">'
			. '<h3><span class="gr-rel-ic" aria-hidden="true"><i class="fa-regular ' . esc_attr( $ic ) . '"></i></span>' . esc_html( $t( $label ) ) . '</h3><ul>' . $items . '</ul></div>';
		$grupos_html[] = $grp;
		$html .= '<div class="col-lg-4 col-md-6">' . $grp . '</div>';
	}
	if ( $html === '' ) return;

	/* Cabecera con subtítulo y tres accesos directos: cotizar, rastrear, WhatsApp. */
	$g       = function ( $k, $d ) { return function_exists( 'grenvios_g' ) ? grenvios_g( $k, $d ) : $d; };
	$cotizar = home_url( '/cotizar/' );
	$rastreo = home_url( '/rastreo-de-envios/' );
	if ( function_exists( 'grenvios_i18n_localize_url' ) ) { $cotizar = grenvios_i18n_localize_url( $cotizar ); $rastreo = grenvios_i18n_localize_url( $rastreo ); }
	$biz = function_exists( 'grenvios_biz' ) ? grenvios_biz() : array();
	$wa  = isset( $biz['wa_number'] ) ? preg_replace( '/\D/', '', (string) $biz['wa_number'] ) : '';
	$tel = isset( $biz['phone'] ) ? $biz['phone'] : '';
	$acciones = '<div class="gr-rel-acciones">'
		. '<a class="gr-rel-accion gr-rel-accion--vino" href="' . esc_url( $cotizar ) . '"><i class="fa-regular fa-calculator" aria-hidden="true"></i><span>' . esc_html( $t( $g( 'rel_btn_cotizar', 'Cotizar mi envío' ) ) ) . '</span><i class="fa-regular fa-arrow-right gr-rel-go" aria-hidden="true"></i></a>'
		. ( get_page_by_path( 'rastreo-de-envios' ) ? '<a class="gr-rel-accion" href="' . esc_url( $rastreo ) . '"><i class="fa-regular fa-magnifying-glass" aria-hidden="true"></i><span>' . esc_html( $t( $g( 'rel_btn_rastrear', 'Rastrear envío' ) ) ) . '</span><i class="fa-regular fa-arrow-right gr-rel-go" aria-hidden="true"></i></a>' : '' )
		. ( $wa ? '<a class="gr-rel-accion" href="https://wa.me/' . esc_attr( $wa ) . '" rel="nofollow noopener" target="_blank"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i><span>' . esc_html( $t( $g( 'rel_btn_wa', 'Escribir por WhatsApp' ) ) ) . ( $tel ? '<small>' . esc_html( $tel ) . '</small>' : '' ) . '</span><i class="fa-regular fa-arrow-right gr-rel-go" aria-hidden="true"></i></a>' : '' )
		. '</div>';

	/* Pasa por `grenvios_html_final` como cualquier otro bloque del tema. El
	 * árbol /destinos/ no se duplica por ruta, así que estos enlaces nacen
	 * apuntando al sitio general: era el último punto por el que el usuario
	 * salía de su ruta de país. */
	/* Con cuatro grupos o menos (la mayoría de páginas) sale la versión compacta de
	 * la maqueta: columnas con icono y enlaces + tarjeta vino «¿Listo para enviar?».
	 * Con más (portada), los tres accesos y las tarjetas rosadas. */
	$n = count( $grupos_html );
	if ( $n >= 1 && $n <= 4 ) {
		$tarjeta = '<div class="gr-rel-tarjeta"><span class="gr-rel-tarjeta-ic" aria-hidden="true"><i class="fa-regular fa-paper-plane"></i></span>'
			. '<strong>' . esc_html( $t( $g( 'rel_card_t', '¿Listo para enviar?' ) ) ) . '</strong>'
			. '<p>' . esc_html( $t( $g( 'rel_card', 'Cotiza con peso, medidas y destino.' ) ) ) . '</p>'
			. '<a class="gr-rel-tarjeta-bt" href="' . esc_url( $cotizar ) . '">' . esc_html( $t( $g( 'rel_btn_cotizar', 'Cotizar mi envío' ) ) ) . ' <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div>';
		$out = apply_filters( 'grenvios_html_final',
			'<section class="grenvios-related grenvios-related--compact padding-bottom"><div class="container"><div class="gr-rel-panel">'
			. '<div class="gr-rel-cab"><h2 class="grenvios-related-title">' . esc_html( $t( $g( 'rel_title', 'Continúa tu envío' ) ) ) . '</h2>'
			. '<p class="gr-rel-sub">' . esc_html( $t( $g( 'rel_sub_c', 'Encuentra el siguiente paso sin volver a buscar.' ) ) ) . '</p></div>'
			. '<div class="gr-rel-cgrid" style="--n:' . $n . '">' . implode( '', $grupos_html ) . $tarjeta . '</div></div></div></section>' );
	} else {
		$out = apply_filters( 'grenvios_html_final',
			'<section class="grenvios-related grenvios-related--v2 padding-bottom"><div class="container">'
			. '<div class="gr-rel-cab"><h2 class="grenvios-related-title">' . esc_html( $t( $g( 'rel_title', 'Continúa tu envío' ) ) ) . '</h2>'
			. '<p class="gr-rel-sub">' . esc_html( $t( $g( 'rel_sub', 'Encuentra el servicio, destino o guía que necesitas.' ) ) ) . '</p></div>'
			. $acciones
			. '<div class="row">' . $html . '</div></div></section>' );
	}

	set_transient( $ck, $out, 12 * HOUR_IN_SECONDS );
	echo $out;
}

/* Icono de cada grupo del bloque «Continúa tu envío», por el nombre del grupo. */
function grenvios_related_icono( $label ) {
	$l = function_exists( 'remove_accents' ) ? strtolower( remove_accents( (string) $label ) ) : strtolower( (string) $label );
	$reglas = array(
		'ruta' => 'fa-plane', 'servicio'  => 'fa-file-lines', 'destino' => 'fa-location-dot', 'antes' => 'fa-box', 'herramienta' => 'fa-screwdriver-wrench',
		'guia' => 'fa-book-open', 'caso' => 'fa-book-open', 'siguiente' => 'fa-gear', 'paso' => 'fa-gear',
		'region' => 'fa-earth-americas', 'blog' => 'fa-newspaper', 'empresa' => 'fa-building', 'pais' => 'fa-flag',
	);
	foreach ( $reglas as $k => $ic ) if ( strpos( $l, $k ) !== false ) return $ic;
	return 'fa-arrow-right';
}

/* Al guardar cualquier página cambian títulos, keywords y enlaces: la caché del
 * bloque se tira entera (son unas pocas decenas de claves, no merece un índice). */
add_action( 'save_post', function ( $post_id ) {
	if ( wp_is_post_revision( $post_id ) ) return;
	global $wpdb;
	$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_gr_rel_%' OR option_name LIKE '_transient_timeout_gr_rel_%'" );
} );

/* Estilos mínimos del bloque (heredan la tipografía y el color del tema). */
add_action( 'wp_head', function () {
	if ( is_admin() ) return;
	echo '<style id="grenvios-related-css">'
		. '.grenvios-related{padding-top:20px}'
		. '.grenvios-related-title{font-size:24px;margin-bottom:22px}'
		. '.grenvios-related-group{margin-bottom:24px}'
		. '.grenvios-related-group h3{font-size:16px;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;opacity:.75}'
		. '.grenvios-related-group ul{list-style:none;padding:0;margin:0}'
		. '.grenvios-related-group li{margin-bottom:7px;line-height:1.4}'
		. '.grenvios-related-group li a{text-decoration:none;border-bottom:1px solid transparent}'
		. '.grenvios-related-group li a:hover{border-bottom-color:currentColor}'
		. '</style>';
}, 101 );

/* ══════════════════════════════════════
   4) INFORME DE ENLACES INTERNOS
   Cuenta enlaces entrantes/salientes de cada página, incluidos los que genera
   automáticamente este módulo.
══════════════════════════════════════ */
function grenvios_links_report( $post_id ) {
	$post_id = (int) $post_id;
	$graph   = grenvios_links_graph();
	return array(
		'in'  => isset( $graph['in'][ $post_id ] )  ? count( array_unique( $graph['in'][ $post_id ] ) )  : 0,
		'out' => isset( $graph['out'][ $post_id ] ) ? count( array_unique( $graph['out'][ $post_id ] ) ) : 0,
	);
}

/* Textos por defecto (con sus href) de los campos de un tipo de página. Se
 * leen una vez por slug: todas las traducciones de una página comparten los
 * mismos valores por defecto, y pedir el texto completo página a página
 * llevaba el cálculo del grafo a más de dos minutos. */
function grenvios_links_default_blob( $slug ) {
	static $memo = array(), $reg = null;
	if ( isset( $memo[ $slug ] ) ) return $memo[ $slug ];
	if ( $reg === null ) $reg = function_exists( 'grenvios_text_registry' ) ? grenvios_text_registry() : array();
	$out = '';
	if ( isset( $reg[ $slug ]['sections'] ) ) {
		foreach ( $reg[ $slug ]['sections'] as $sec ) {
			foreach ( (array) $sec['fields'] as $f ) {
				if ( isset( $f[2] ) && is_string( $f[2] ) && strpos( $f[2], 'href' ) !== false ) $out .= ' ' . $f[2];
			}
		}
	}
	return $memo[ $slug ] = $out;
}

/* Grafo de enlaces del sitio (cacheado 6 h). */
function grenvios_links_graph() {
	$g = get_transient( 'grenvios_links_graph' );
	if ( is_array( $g ) ) return $g;

	$g    = array( 'in' => array(), 'out' => array() );
	$args = array( 'post_type' => 'page', 'post_status' => 'publish', 'numberposts' => -1 );
	if ( function_exists( 'grenvios_i18n_active' ) && grenvios_i18n_active() ) $args['lang'] = '';
	$pages = get_posts( $args );

	// Índice URL -> ID para resolver los destinos de cada enlace.
	$by_url = array();
	foreach ( $pages as $p ) $by_url[ untrailingslashit( get_permalink( $p ) ) ] = (int) $p->ID;

	foreach ( $pages as $p ) {
		$targets = array();

		// a) Enlaces escritos en el contenido y en los bloques repetibles.
		$blob = (string) $p->post_content;
		foreach ( get_post_meta( $p->ID ) as $k => $v ) {
			if ( strpos( $k, 'grenvios_' ) !== 0 ) continue;
			$blob .= ' ' . maybe_serialize( $v );
		}
		/* También los enlaces que se publican sin estar guardados en la página:
		 * los textos por defecto de sus campos y la plantilla del tema. Antes
		 * solo se leía lo guardado, y páginas enlazadas desde decenas de sitios
		 * salían como huérfanas. */
		$cslug = function_exists( 'grenvios_canonical_slug' ) ? grenvios_canonical_slug( $p->ID ) : $p->post_name;
		if ( (int) get_option( 'page_on_front' ) === (int) $p->ID ) $cslug = 'home';
		$blob .= ' ' . grenvios_links_default_blob( $cslug );
		$tpl = get_template_directory() . '/template-parts/content-' . $cslug . '.html';
		if ( is_readable( $tpl ) ) $blob .= ' ' . file_get_contents( $tpl );
		$blob = str_replace( array( 'HOMEURL', '%H%' ), grenvios_url_base(), $blob );
		if ( preg_match_all( '#href="([^"]+)"|"link";s:\d+:"([^"]+)"#i', $blob, $m ) ) {
			foreach ( array_merge( $m[1], $m[2] ) as $u ) {
				if ( $u === '' ) continue;
				$abs = ( $u[0] === '/' ) ? grenvios_url_base() . untrailingslashit( $u ) : untrailingslashit( $u );
				if ( isset( $by_url[ $abs ] ) ) $targets[] = $by_url[ $abs ];
			}
		}

		// b) Enlaces que genera el bloque "relacionados" de esta página.
		$slug = function_exists( 'grenvios_canonical_slug' ) ? grenvios_canonical_slug( $p->ID ) : $p->post_name;
		foreach ( grenvios_related_for( $slug ) as $paths ) {
			foreach ( (array) $paths as $path ) {
				$t = get_page_by_path( $path );
				if ( $t ) $targets[] = (int) $t->ID;
			}
		}

		foreach ( array_unique( $targets ) as $t ) {
			if ( $t === (int) $p->ID ) continue;
			$g['out'][ (int) $p->ID ][] = $t;
			$g['in'][ $t ][]            = (int) $p->ID;
		}
	}

	/* El logo de la cabecera enlaza la portada desde todas las páginas. */
	$front = (int) get_option( 'page_on_front' );
	if ( $front ) {
		foreach ( $pages as $p ) {
			if ( (int) $p->ID === $front ) continue;
			$g['out'][ (int) $p->ID ][] = $front;
			$g['in'][ $front ][]        = (int) $p->ID;
		}
	}

	// Aristas aportadas por otros modulos (guias de cluster -> pagina de dinero).
	foreach ( (array) apply_filters( 'grenvios_links_extra', array() ) as $edge ) {
		list( $from, $to ) = $edge;
		if ( ! $from || ! $to || $from === $to ) continue;
		$g['out'][ $from ][] = $to;
		$g['in'][ $to ][]    = $from;
	}

	set_transient( 'grenvios_links_graph', $g, HOUR_IN_SECONDS * 6 );
	return $g;
}
add_action( 'save_post_page', function () { delete_transient( 'grenvios_links_graph' ); } );

/* ══════════════════════════════════════
   5) LLAMADA A LA ACCIÓN SISTEMÁTICA
   Patrón tomado de los grandes couriers: el cotizador está a un clic desde
   CUALQUIER página, no solo desde el menú. Aquí se imprime al cierre de cada
   página (salvo en las que ya son el destino: cotizar y contacto).
══════════════════════════════════════ */
function grenvios_render_cta( $slug ) {
	if ( ! apply_filters( 'grenvios_cta_enabled', true, $slug ) ) return;
	if ( in_array( $slug, array( 'cotizar', 'contacto' ), true ) ) return;

	$t = function ( $s ) { return function_exists( 'grenvios_t' ) ? grenvios_t( $s ) : $s; };

	// Enlaces en el idioma activo.
	$cotizar = home_url( '/cotizar/' );
	if ( function_exists( 'grenvios_i18n_localize_url' ) ) $cotizar = grenvios_i18n_localize_url( $cotizar );

	$biz = function_exists( 'grenvios_biz' ) ? grenvios_biz() : array();
	$wa  = isset( $biz['wa_number'] ) ? preg_replace( '/\D/', '', (string) $biz['wa_number'] ) : '';
	$tel = isset( $biz['phone'] ) ? $biz['phone'] : '';

	// Mensaje de WhatsApp con el nombre de la página: el asesor sabe de entrada
	// qué estaba mirando el cliente.
	$msg = rawurlencode( $t( 'Hola, quiero cotizar un envío. Vengo de la página' ) . ': ' . get_the_title() );

	echo '<section class="grenvios-cta"><div class="container"><div class="grenvios-cta-box wow fade-in-bottom" data-wow-delay="100ms">';
	echo '<div class="grenvios-cta-text">';
	/* Textos editables en el panel, sección global «Llamada final» (inc/globals.php). */
	$g = function ( $k, $d ) { return function_exists( 'grenvios_g' ) ? grenvios_g( $k, $d ) : $d; };
	echo '<h2>' . esc_html( $t( $g( 'cta_title', '¿Listo para enviar?' ) ) ) . '</h2>';
	echo '<p>' . esc_html( $t( $g( 'cta_text', 'Cotiza en minutos según el peso, el volumen y el destino de tu envío. Sin compromiso.' ) ) ) . '</p>';
	echo '</div><div class="grenvios-cta-actions">';
	echo '<a class="default-btn" href="' . esc_url( $cotizar ) . '">' . esc_html( $t( $g( 'cta_btn', 'Cotizar mi envío' ) ) ) . '</a>';
	if ( $wa ) {
		echo '<a class="grenvios-cta-wa" href="https://wa.me/' . esc_attr( $wa ) . '?text=' . $msg . '" rel="nofollow noopener" target="_blank">';
		echo '<i class="fa-brands fa-whatsapp"></i> ' . esc_html( $t( $g( 'cta_wa', 'Escribir por WhatsApp' ) ) );
		if ( $tel ) echo ' <span>' . esc_html( $tel ) . '</span>';
		echo '</a>';
	}
	echo '</div>';
	/* Foto real (skill grenvios-ui): la del repartidor del tema o la que se suba
	 * en el panel, sección global «Llamada final». */
	$img = function_exists( 'grenvios_g' ) ? trim( (string) grenvios_g( 'cta_img', '' ) ) : '';
	if ( $img === '' ) $img = get_template_directory_uri() . '/assets/img/cta-repartidor-4.webp';
	echo '<div class="grenvios-cta-media" aria-hidden="true"><img src="' . esc_url( $img ) . '" alt="" loading="lazy" decoding="async"></div>';
	echo '</div></div></section>';
}

/* Estilos del bloque de llamada a la acción. */
add_action( 'wp_head', function () {
	if ( is_admin() ) return;
	echo '<style id="grenvios-cta-css">'
		. '.grenvios-cta{padding:0 0 60px}'
		. '.grenvios-cta-box{display:flex;flex-wrap:wrap;gap:24px;align-items:center;justify-content:space-between;'
		. 'padding:32px 36px;border-radius:10px;background:rgba(0,0,0,.04)}'
		. '.grenvios-cta-text h2{font-size:26px;margin:0 0 6px}'
		. '.grenvios-cta-text p{margin:0;max-width:56ch;opacity:.85}'
		. '.grenvios-cta-actions{display:flex;flex-wrap:wrap;gap:14px;align-items:center}'
		. '.grenvios-cta-wa{display:inline-flex;align-items:center;gap:8px;font-weight:600;text-decoration:none}'
		. '.grenvios-cta-wa span{opacity:.7;font-weight:400;white-space:nowrap}'
		. '@media(max-width:767px){.grenvios-cta-box{padding:24px}.grenvios-cta-text h2{font-size:22px}}'
		. '</style>';
}, 102 );

/* Textos de los bloques de enlazado y llamada a la acción. */
add_filter( 'grenvios_i18n_extra_strings', function ( $textos ) {
	foreach ( array(
		'Continúa tu envío', '¿Listo para enviar?',
		'Cotiza en minutos según el peso, el volumen y el destino de tu envío. Sin compromiso.',
		'Cotizar mi envío', 'Escribir por WhatsApp',
		'Hola, quiero cotizar un envío. Vengo de la página',
		'Servicios para este destino', 'Otros destinos', 'Siguiente paso',
		'Destinos más solicitados', 'Otros servicios', 'Nuestros servicios',
		'Antes de cotizar', 'También te sirve', 'Servicios', 'Lo que hacemos',
		'Mientras tanto', 'Herramientas', 'Te puede interesar', 'Servicios donde aplica',
		'Servicios para tu empresa', 'Lo que necesitas', 'Destinos', 'Antes de enviar',
	) as $t ) $textos[] = $t;
	return $textos;
} );
