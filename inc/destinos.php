<?php
/**
 * Grenvíos — Destinos (gestión de países con página propia).
 *
 * Admin: pantalla "Destinos" para AGREGAR / EDITAR / ELIMINAR países. Al agregar uno:
 *   1) se guarda en la opción `grenvios_destinos_custom` (se fusiona con los 9 fijos),
 *   2) se crea automáticamente su página (hija de "Destinos") que usa la MISMA plantilla
 *      data-driven `grenvios_render_destino()` con TEXTOS GENÉRICOS por defecto,
 *   3) aparece solo en el menú del encabezado (submenú Destinos, agrupado por continente)
 *      y en el menú "Principal" de WordPress.
 *
 * El submenú Destinos del encabezado se genera desde `grenvios_destinos()` (token
 * DESTINOSMENU en template-parts/header.html) → cualquier país nuevo se muestra sin tocar HTML.
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* Continentes disponibles para agrupar en el menú (orden = orden de despliegue).
 *
 * Filtro `grenvios_destino_continentes`: lo usa inc/sedes-contenido.php para
 * poner primero el continente de la sede activa. El orden fijo empieza por
 * América porque la sede original es Perú; a un usuario en Madrid ese orden le
 * abre el submenú con ocho países americanos antes que con Europa. */
function grenvios_destino_continentes() {
	return (array) apply_filters( 'grenvios_destino_continentes', array( 'América', 'Europa', 'Asia · África' ) );
}

/* Servicio por defecto de un destino (una de las páginas de servicio). */
function grenvios_destino_servicios() {
	return array(
		'envio-internacional-de-paquetes'   => 'Envío de Paquetes',
		'envio-internacional-de-documentos' => 'Envío de Documentos',
		'carga-internacional'               => 'Carga Internacional',
	);
}

/* Genera un registro de destino COMPLETO con textos genéricos a partir del título.
 * Los valores no vacíos de $in sobrescriben los genéricos. */
function grenvios_destino_normalize( $title, $in = array() ) {
	$title = trim( wp_strip_all_tags( (string) $title ) );
	$low   = function_exists( 'mb_strtolower' ) ? mb_strtolower( $title, 'UTF-8' ) : strtolower( $title );

	$def = array(
		'title'      => $title,
		'continente' => 'América',
		// {{origen_pais}} y {{origen_ciudad}} los rellena la sede activa
		// (inc/sedes-contenido.php). Con una sola sede resuelven a Perú y Lima,
		// exactamente como estaba escrito antes a mano.
		'seo'        => "Envíos a {$title} desde {{origen_pais}} | Grenvíos",
		'desc'       => "Envía documentos, paquetes y carga a {$title} con Grenvíos. Cotiza tu envío internacional desde {{origen_ciudad}} y consulta los tiempos de entrega.",
		'kw'         => "envíos a {$low}",
		'tiempo'     => 'Consultar según destino',
		'modos'      => 'Aéreo',
		'entrega'    => 'En agencia local',
		'restr'      => "Consulta con nuestro equipo las restricciones de aduana y los requisitos vigentes para {$title} antes de realizar tu envío. Te asesoramos en cada paso.",
		'lead'       => "Enviamos documentos, paquetes y carga a {$title} de forma segura, rápida y confiable. Elige la modalidad que mejor se adapte a tu envío y cotiza en minutos.",
		'servicio'   => 'envio-internacional-de-paquetes',
		'_custom'    => true,
	);

	$out = $def;
	foreach ( $def as $k => $v ) {
		if ( isset( $in[ $k ] ) && trim( (string) $in[ $k ] ) !== '' ) $out[ $k ] = $in[ $k ];
	}
	// Validaciones de dominio cerrado
	$serv = grenvios_destino_servicios();
	if ( ! isset( $serv[ $out['servicio'] ] ) ) $out['servicio'] = 'envio-internacional-de-paquetes';
	if ( ! in_array( $out['continente'], grenvios_destino_continentes(), true ) ) $out['continente'] = 'América';
	$out['_custom'] = true;
	return $out;
}

/* Destinos añadidos por el admin (opción). slug => registro normalizado. */
function grenvios_destinos_custom() {
	$opt = get_option( 'grenvios_destinos_custom' );
	if ( ! is_array( $opt ) ) return array();
	$out = array();
	foreach ( $opt as $slug => $d ) {
		$slug = sanitize_title( $slug );
		if ( $slug === '' || ! is_array( $d ) ) continue;
		$out[ $slug ] = grenvios_destino_normalize( isset( $d['title'] ) ? $d['title'] : $slug, $d );
	}
	return $out;
}

function grenvios_destinos_custom_save( $arr ) {
	update_option( 'grenvios_destinos_custom', $arr );
}

/* ── Menú del encabezado: submenú Destinos agrupado por continente (token DESTINOSMENU) ── */
function grenvios_destinos_menu_html() {
	/* Idéntico en todas las páginas de una ruta y costaba 0,08 s por visita:
	 * cada país resolvía su permalink con sus consultas. Se arma una vez por
	 * petición y se guarda 12 h por ruta (inc/cache-html.php lo invalida al
	 * guardar cualquier cosa). */
	static $memo = array();
	$ruta = function_exists( 'grenvios_i18n_current' ) ? grenvios_i18n_current() : '';
	if ( isset( $memo[ $ruta ] ) ) return $memo[ $ruta ];

	if ( ! is_admin() && ! is_user_logged_in() && function_exists( 'grenvios_cache_ver' ) ) {
		$tk  = 'gr_menu_' . md5( $ruta . '|' . grenvios_cache_ver() );
		$hit = get_transient( $tk );
		if ( is_string( $hit ) && $hit !== '' ) return $memo[ $ruta ] = $hit;
	}

	$dest   = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();
	$groups = array();
	foreach ( $dest as $slug => $d ) {
		$cont = ( ! empty( $d['continente'] ) ) ? $d['continente'] : 'América';
		$groups[ $cont ][ $slug ] = isset( $d['title'] ) ? $d['title'] : $slug;
	}
	$order = grenvios_destino_continentes();
	foreach ( array_keys( $groups ) as $c ) if ( ! in_array( $c, $order, true ) ) $order[] = $c;

	$out = '';
	foreach ( $order as $cont ) {
		if ( empty( $groups[ $cont ] ) ) continue;
		$out .= '<li class="menu-group-label">' . esc_html( $cont ) . '</li>';
		foreach ( $groups[ $cont ] as $slug => $title ) {
			$url  = apply_filters( 'grenvios_destino_permalink', home_url( '/destinos/' . $slug . '/' ), $slug );
			$out .= '<li><a href="' . esc_url( $url ) . '">' . esc_html( $title ) . '</a></li>';
		}
	}
	$hub  = apply_filters( 'grenvios_destino_permalink', home_url( '/destinos/' ), 'destinos' );
	$out .= '<li><a href="' . esc_url( $hub ) . '">' . esc_html( grenvios_t( 'Otros países (ver todos)' ) ) . '</a></li>';

	if ( ! empty( $tk ) ) set_transient( $tk, $out, 12 * HOUR_IN_SECONDS );
	return $memo[ $ruta ] = $out;
}

/* ── Datos de las tarjetas del hub /destinos/ ─────────────────────────────
 * Código ISO por país, para la bandera (assets/img/flags, copiadas de
 * Polylang) y la marca de agua de la tarjeta. Se busca por slug del destino o
 * por el nombre tal como se escribe en «Otros destinos». */
function grenvios_dest_iso( $ref ) {
	static $mapa = array(
		'ecuador' => 'ec', 'colombia' => 'co', 'chile' => 'cl', 'bolivia' => 'bo', 'argentina' => 'ar',
		'estados-unidos' => 'us', 'espana' => 'es', 'venezuela' => 've', 'cuba' => 'cu', 'brasil' => 'br',
		'panama' => 'pa', 'costa-rica' => 'cr', 'puerto-rico' => 'pr', 'uruguay' => 'uy', 'paraguay' => 'py',
		'canada' => 'ca', 'mexico' => 'mx', 'italia' => 'it', 'francia' => 'fr', 'alemania' => 'de',
		'china' => 'cn', 'japon' => 'jp', 'australia' => 'au', 'peru' => 'pe',
	);
	$k = sanitize_title( remove_accents( (string) $ref ) );
	return isset( $mapa[ $k ] ) ? $mapa[ $k ] : '';
}

/* Región para los filtros del hub: el gestor solo distingue continente, y
 * «América» mezcla rutas terrestres vecinas con vuelos al norte y al Caribe. */
function grenvios_dest_region( $slug, $continente = '' ) {
	$norte = array( 'estados-unidos', 'canada', 'mexico', 'cuba', 'puerto-rico', 'panama', 'costa-rica' );
	if ( in_array( $slug, $norte, true ) ) return 'norte';
	$c = sanitize_title( remove_accents( (string) $continente ) );
	if ( $c === 'europa' ) return 'europa';
	if ( $c === 'america' || $c === '' ) return 'sud';
	return $c;
}

/* URL de la bandera de un país por su código ISO.
 *
 * SVG vectoriales (assets/img/flags-svg, de flag-icons, licencia MIT: ver
 * LICENSE.txt en esa carpeta): nítidas a cualquier tamaño. Los PNG de
 * Polylang miden 16×11 y se veían borrosos al ampliarlos a 30–46 px en las
 * tarjetas (2026-10-03). Bolivia y España usan su bandera civil, sin escudo:
 * es oficial y pesa 1 KB en vez de 100. Respaldo: el PNG si falta el SVG. */
function grenvios_bandera_url( $iso ) {
	$iso = strtolower( (string) $iso );
	if ( ! preg_match( '/^[a-z]{2}$/', $iso ) ) return '';
	$dir = get_template_directory(); $uri = get_template_directory_uri();
	if ( file_exists( $dir . '/assets/img/flags-svg/' . $iso . '.svg' ) ) return $uri . '/assets/img/flags-svg/' . $iso . '.svg';
	if ( file_exists( $dir . '/assets/img/flags/' . $iso . '.png' ) ) return $uri . '/assets/img/flags/' . $iso . '.png';
	return '';
}

/* <img> de bandera. Decorativa (alt vacío): el nombre del país va al lado. */
function grenvios_bandera_img( $iso, $clase = 'gr-flag', $ancho = 24 ) {
	$u = grenvios_bandera_url( $iso );
	if ( $u === '' ) return '';
	return '<img class="' . esc_attr( $clase ) . '" src="' . esc_url( $u ) . '" alt="" width="' . (int) $ancho . '" height="' . (int) round( $ancho * 3 / 4 ) . '" loading="lazy" decoding="async">';
}

function grenvios_dest_flag_html( $iso ) {
	return grenvios_bandera_img( $iso, 'gr-flag', 24 );
}

add_filter( 'grenvios_repeater_extras', function ( $items, $key ) {
	if ( $key === 'dest_otros' ) {
		foreach ( $items as $i => $it ) {
			$items[ $i ]['_flag'] = grenvios_dest_flag_html( grenvios_dest_iso( isset( $it['pais'] ) ? $it['pais'] : '' ) );
		}
		return $items;
	}
	if ( $key !== 'dest_cards' ) return $items;

	foreach ( $items as $i => $it ) {
		$link = isset( $it['link'] ) ? (string) $it['link'] : '';
		$slug = preg_match( '~/destinos/([a-z0-9-]+)/?~', $link, $m ) ? $m[1] : '';
		$d    = ( $slug !== '' && function_exists( 'grenvios_pais_datos' ) ) ? grenvios_pais_datos( $slug ) : array();
		$iso  = grenvios_dest_iso( $slug !== '' ? $slug : ( isset( $it['name'] ) ? $it['name'] : '' ) );

		/* El plazo y la entrega salen del gestor, igual que en la ficha: así la
		 * tarjeta nunca dice un plazo distinto del de la página a la que lleva. */
		$plazo   = ! empty( $d['tiempo'] ) ? $d['tiempo'] : ( isset( $it['time'] ) ? preg_replace( '/^\s*Entrega estimada:\s*/iu', '', (string) $it['time'] ) : '' );
		$entrega = '';
		if ( ! empty( $d['entrega'] ) ) {
			$entrega = ! empty( $d['casa'] ) ? 'Entrega a domicilio' : 'Retiro en agencia local';
		}
		$facts = '';
		if ( $plazo !== '' )   $facts .= '<li><i class="fa-regular fa-clock"></i><span>' . esc_html( $plazo ) . '</span></li>';
		if ( $entrega !== '' ) $facts .= '<li><i class="fa-solid ' . ( ! empty( $d['casa'] ) ? 'fa-house' : 'fa-store' ) . '"></i><span>' . esc_html( $entrega ) . '</span></li>';

		$ciudades = '';
		if ( ! empty( $d['ciudades'] ) && function_exists( 'grenvios_pais_lista' ) ) {
			$lista = array_slice( grenvios_pais_lista( $d['ciudades'] ), 0, 3 );
			if ( $lista ) {
				$ciudades = '<p class="gr-dcard-ciudades"><i class="fa-solid fa-location-dot"></i>'
					. esc_html( implode( ' · ', $lista ) ) . '</p>';
			}
		}

		if ( ! empty( $d['modos'] ) ) {
			$items[ $i ]['category'] = $d['modos'];
			$items[ $i ]['icon']     = ! empty( $d['terr'] ) ? 'logis logis-truck-2' : 'logis logis-airplane-flying';
		}
		$all = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();
		$items[ $i ]['_attrs']    = $slug !== ''
			? ' data-region="' . esc_attr( grenvios_dest_region( $slug, isset( $all[ $slug ]['continente'] ) ? $all[ $slug ]['continente'] : '' ) ) . '"'
				. ' data-terr="' . ( ! empty( $d['terr'] ) ? '1' : '0' ) . '" data-casa="' . ( ! empty( $d['casa'] ) ? '1' : '0' ) . '"'
			: '';
		/* Iconos de la vía (avión y/o camión) y chip de la ruta: «Ruta destacada»
		 * = la terrestre más rápida; «Entrega rápida» = las solo aéreas con plazo
		 * máximo de 7 días hábiles. Ambos salen de los datos del gestor. */
		$aire = ! empty( $d['aereo'] ) || stripos( (string) ( isset( $d['modos'] ) ? $d['modos'] : '' ), 'aére' ) !== false;
		$items[ $i ]['_modo_ic']  = ( $aire ? '<i class="fa-solid fa-plane" aria-hidden="true"></i>' : '' )
			. ( ! empty( $d['terr'] ) ? '<i class="fa-solid fa-truck" aria-hidden="true"></i>' : '' );
		$chip = '';
		if ( $slug !== '' && function_exists( 'grenvios_dh_filas' ) ) {
			static $destacada = null;
			if ( $destacada === null ) {
				$destacada = ''; $mejor = array( 999, 999 );
				foreach ( grenvios_dh_filas() as $fs => $fr ) {
					if ( $fr['terr'] && array( $fr['min'], $fr['max'] ) < $mejor ) { $mejor = array( $fr['min'], $fr['max'] ); $destacada = $fs; }
				}
			}
			$fila = grenvios_dh_filas();
			if ( $slug === $destacada ) {
				$chip = '<span class="gr-dcard-chip"><i class="fa-solid fa-star" aria-hidden="true"></i> Ruta destacada</span>';
			} elseif ( isset( $fila[ $slug ] ) && ! $fila[ $slug ]['terr'] && $fila[ $slug ]['max'] <= 7 ) {
				$chip = '<span class="gr-dcard-chip"><i class="fa-solid fa-bolt" aria-hidden="true"></i> Entrega rápida</span>';
			}
			$items[ $i ]['_destacada'] = $slug === $destacada;
		}
		$items[ $i ]['_chip']     = $chip;
		$items[ $i ]['_iso']      = esc_html( strtoupper( $iso ) );
		$items[ $i ]['_flag']     = grenvios_dest_flag_html( $iso );
		$items[ $i ]['_facts']    = $facts;
		$items[ $i ]['_ciudades'] = $ciudades;
	}
	return $items;
}, 10, 2 );

/* Fusiona TODOS los destinos (fijos + agregados) como TARJETAS en la vitrina del hub
 * /destinos/ (repeater dest_cards). Se añaden al final, sin duplicar los que ya estén
 * por enlace → así siempre se muestran todos los países que tenemos. */
function grenvios_dest_merge_auto_cards( $items ) {
	if ( ! is_array( $items ) ) $items = array();
	$all = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();
	if ( ! $all ) return $items;

	$have = array();
	foreach ( $items as $it ) {
		if ( isset( $it['link'] ) ) $have[ trim( strtolower( (string) $it['link'] ), '/' ) ] = true;
	}
	$n = 0;
	foreach ( $all as $slug => $d ) {
		$key = 'destinos/' . $slug;
		if ( isset( $have[ $key ] ) ) continue;
		$terr = ( stripos( $d['modos'], 'terrestre' ) !== false );
		$img  = function_exists( 'grenvios_img_url' ) ? grenvios_img_url( 'content-bg-' . ( ( $n % 8 ) + 1 ) . '.jpg' ) : '';
		$items[] = array(
			'img'      => $img,
			'icon'     => $terr ? 'logis logis-truck-2' : 'logis logis-airplane-flying',
			'category' => $d['modos'],
			'name'     => $d['title'],
			'time'     => $d['tiempo'],
			'link'     => '/destinos/' . $slug . '/',
			'btn'      => 'Ver condiciones',
		);
		$n++;
	}
	return $items;
}

/* ── Menú "Principal" de WordPress: añade / quita el item de un destino bajo "Destinos" ── */
function grenvios_menu_destino_parent_item( $menu_id ) {
	$dest_page = get_page_by_path( 'destinos' );
	if ( ! $dest_page ) return 0;
	foreach ( (array) wp_get_nav_menu_items( $menu_id ) as $it ) {
		if ( (int) $it->object_id === (int) $dest_page->ID && $it->type === 'post_type' ) return (int) $it->ID;
	}
	return 0;
}

function grenvios_menu_add_destino( $page_id ) {
	if ( ! $page_id ) return;
	$menu = wp_get_nav_menu_object( 'Principal' );
	if ( ! $menu ) return;
	$menu_id = $menu->term_id;
	// Evita duplicados
	foreach ( (array) wp_get_nav_menu_items( $menu_id ) as $it ) {
		if ( (int) $it->object_id === (int) $page_id && $it->type === 'post_type' ) return;
	}
	$parent = grenvios_menu_destino_parent_item( $menu_id );
	wp_update_nav_menu_item( $menu_id, 0, array(
		'menu-item-title'     => get_the_title( $page_id ),
		'menu-item-object'    => 'page',
		'menu-item-object-id' => $page_id,
		'menu-item-type'      => 'post_type',
		'menu-item-status'    => 'publish',
		'menu-item-parent-id' => $parent,
	) );
}

function grenvios_menu_remove_destino( $page_id ) {
	$menu = wp_get_nav_menu_object( 'Principal' );
	if ( ! $menu ) return;
	foreach ( (array) wp_get_nav_menu_items( $menu->term_id ) as $it ) {
		if ( (int) $it->object_id === (int) $page_id && $it->type === 'post_type' ) wp_delete_post( $it->ID, true );
	}
}

/* ── Menú admin ── */
add_action( 'admin_menu', function () {
	add_menu_page(
		'Destinos', 'Destinos', 'edit_pages',
		'grenvios-destinos', 'grenvios_destinos_admin_page',
		'dashicons-admin-site-alt3', 27
	);
} );

/* Procesa Agregar / Guardar / Eliminar (patrón PRG). */
add_action( 'admin_post_grenvios_dest_save', function () {
	if ( ! current_user_can( 'edit_pages' ) ) wp_die( 'Sin permiso.' );
	check_admin_referer( 'grenvios_dest_admin' );

	$action = isset( $_POST['gv_action'] ) ? sanitize_key( $_POST['gv_action'] ) : '';
	$title  = isset( $_POST['title'] ) ? sanitize_text_field( wp_unslash( $_POST['title'] ) ) : '';
	$slug   = isset( $_POST['slug'] ) && $_POST['slug'] !== '' ? sanitize_title( wp_unslash( $_POST['slug'] ) ) : sanitize_title( $title );
	$msg    = 'err';

	// Se pide país + continente + (opcional) SEO de la página; el resto usa textos genéricos.
	$fields = array();
	if ( isset( $_POST['continente'] ) ) {
		$fields['continente'] = sanitize_text_field( wp_unslash( $_POST['continente'] ) );
	}
	if ( isset( $_POST['seo'] ) )  $fields['seo']  = sanitize_text_field( wp_unslash( $_POST['seo'] ) );
	if ( isset( $_POST['desc'] ) ) $fields['desc'] = sanitize_textarea_field( wp_unslash( $_POST['desc'] ) );
	if ( isset( $_POST['kw'] ) )   $fields['kw']   = sanitize_text_field( wp_unslash( $_POST['kw'] ) );

	$custom = get_option( 'grenvios_destinos_custom' );
	if ( ! is_array( $custom ) ) $custom = array();

	if ( ( $action === 'add' || $action === 'update' ) && $title !== '' && $slug !== '' ) {
		$fixed = function_exists( 'grenvios_destinos_fixed' ) ? grenvios_destinos_fixed() : array();

		// No permitir chocar con un slug fijo o con otra página existente (salvo la propia en update)
		$existing = get_page_by_path( $slug );
		$clash    = isset( $fixed[ $slug ] );
		if ( $action === 'add' && ( $clash || ( $existing && ! isset( $custom[ $slug ] ) ) ) ) {
			wp_safe_redirect( add_query_arg( 'gv_msg', 'dup', admin_url( 'admin.php?page=grenvios-destinos' ) ) );
			exit;
		}

		$fields['title'] = $title;
		$custom[ $slug ] = grenvios_destino_normalize( $title, $fields );
		grenvios_destinos_custom_save( $custom );

		// Crea / actualiza la página (hija de Destinos)
		$dest_page = get_page_by_path( 'destinos' );
		$parent    = $dest_page ? $dest_page->ID : 0;
		if ( function_exists( 'grenvios_ensure_page' ) ) {
			$pid = grenvios_ensure_page( $slug, $title, $parent );
			if ( $pid ) {
				// mantiene el título sincronizado en ediciones
				if ( get_the_title( $pid ) !== $title ) wp_update_post( array( 'ID' => $pid, 'post_title' => $title ) );
				grenvios_menu_add_destino( $pid );
			}
		}
		flush_rewrite_rules( false );
		$msg = ( $action === 'add' ) ? 'agregado' : 'guardado';

	} elseif ( $action === 'delete' && $slug !== '' && isset( $custom[ $slug ] ) ) {
		unset( $custom[ $slug ] );
		grenvios_destinos_custom_save( $custom );
		$page = get_page_by_path( $slug );
		if ( $page ) {
			grenvios_menu_remove_destino( $page->ID );
			wp_delete_post( $page->ID, true ); // borra la página del destino
		}
		flush_rewrite_rules( false );
		$msg = 'eliminado';
	}

	wp_safe_redirect( add_query_arg( 'gv_msg', $msg, admin_url( 'admin.php?page=grenvios-destinos' ) ) );
	exit;
} );

/* Render de la pantalla admin. */
function grenvios_destinos_admin_page() {
	if ( ! current_user_can( 'edit_pages' ) ) return;
	$action   = admin_url( 'admin-post.php' );
	$fixed    = function_exists( 'grenvios_destinos_fixed' ) ? grenvios_destinos_fixed() : array();
	$custom   = grenvios_destinos_custom();
	$conts    = grenvios_destino_continentes();
	$home     = untrailingslashit( home_url() );
	$msg      = isset( $_GET['gv_msg'] ) ? sanitize_key( $_GET['gv_msg'] ) : '';
	$msgmap   = array(
		'agregado'  => 'Destino agregado: se creó su página con textos genéricos y aparece en el menú.',
		'guardado'  => 'Cambios guardados.',
		'eliminado' => 'Destino eliminado (también se borró su página).',
		'dup'       => 'Ese país (o su URL) ya existe. Usa otro nombre o edítalo abajo.',
		'err'       => 'No se pudo procesar. Revisa el nombre del país.',
	);

	// Edición: ?edit=slug
	$edit = isset( $_GET['edit'] ) ? sanitize_title( $_GET['edit'] ) : '';
	$ed   = ( $edit !== '' && isset( $custom[ $edit ] ) ) ? $custom[ $edit ] : null;

	$cont_select = function ( $cur ) use ( $conts ) {
		$o = '';
		foreach ( $conts as $c ) $o .= '<option value="' . esc_attr( $c ) . '" ' . selected( $cur, $c, false ) . '>' . esc_html( $c ) . '</option>';
		return $o;
	};
	$box = 'background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:16px;max-width:900px';
	?>
	<div class="wrap">
		<h1><span class="dashicons dashicons-admin-site-alt3" style="font-size:28px;height:28px;width:28px;vertical-align:-4px"></span> Destinos</h1>
		<p>Agrega países de destino. Al guardar se crea su página automáticamente con una <strong>plantilla de textos genéricos</strong> (que luego puedes personalizar en la propia página con el botón <em>Editar</em>), y aparece en el menú <em>Destinos</em> del encabezado.</p>

		<?php if ( isset( $msgmap[ $msg ] ) ) : ?>
			<div class="notice <?php echo $msg === 'dup' || $msg === 'err' ? 'notice-error' : 'notice-success'; ?> is-dismissible"><p><?php echo esc_html( $msgmap[ $msg ] ); ?></p></div>
		<?php endif; ?>

		<h2 style="margin-top:18px"><?php echo $ed ? 'Editar destino: ' . esc_html( $ed['title'] ) : 'Agregar destino'; ?></h2>
		<form method="post" action="<?php echo esc_url( $action ); ?>" style="<?php echo esc_attr( $box ); ?>">
			<?php wp_nonce_field( 'grenvios_dest_admin' ); ?>
			<input type="hidden" name="action" value="grenvios_dest_save">
			<input type="hidden" name="gv_action" value="<?php echo $ed ? 'update' : 'add'; ?>">
			<?php if ( $ed ) : ?><input type="hidden" name="slug" value="<?php echo esc_attr( $edit ); ?>"><?php endif; ?>

			<div style="display:flex;gap:14px;flex-wrap:wrap">
				<label style="flex:2;min-width:220px">País *<br>
					<input type="text" name="title" required class="regular-text" style="width:100%" placeholder="Ej. Brasil" value="<?php echo esc_attr( $ed ? $ed['title'] : '' ); ?>"></label>
				<label style="flex:1;min-width:180px">Continente<br>
					<select name="continente" style="width:100%"><?php echo $cont_select( $ed ? $ed['continente'] : 'América' ); ?></select></label>
			</div>

			<?php if ( ! $ed ) : ?>
				<p style="color:#777;margin:10px 0 0">La URL será <code><?php echo esc_html( $home ); ?>/destinos/<span id="gv-slug-prev">pais</span>/</code>. La página se crea con textos genéricos que puedes personalizar luego desde la propia página con el botón <em>Editar</em>.</p>
			<?php endif; ?>

			<details style="margin-top:14px" <?php echo ( $ed && ( $ed['seo'] !== '' || $ed['desc'] !== '' || $ed['kw'] !== '' ) ) ? 'open' : ''; ?>>
				<summary style="cursor:pointer;color:#5e2129;font-weight:600">SEO de la página (opcional)</summary>
				<p style="color:#777;margin:8px 0">Si lo dejas vacío se genera automáticamente a partir del nombre del país.</p>
				<label style="display:block">Título SEO (aparece en Google y en la pestaña)<br>
					<input type="text" name="seo" class="large-text" placeholder="Envíos a <?php echo esc_attr( $ed ? $ed['title'] : 'País' ); ?> desde Perú | Grenvíos" value="<?php echo esc_attr( $ed ? $ed['seo'] : '' ); ?>"></label>
				<label style="display:block;margin-top:10px">Meta descripción (resumen bajo el título en Google)<br>
					<textarea name="desc" rows="2" class="large-text" placeholder="Describe el envío a este país en 1-2 líneas (~155 caracteres)."><?php echo esc_textarea( $ed ? $ed['desc'] : '' ); ?></textarea></label>
				<label style="display:block;margin-top:10px">Palabra clave principal<br>
					<input type="text" name="kw" class="regular-text" placeholder="envíos a <?php echo esc_attr( strtolower( $ed ? $ed['title'] : 'país' ) ); ?>" value="<?php echo esc_attr( $ed ? $ed['kw'] : '' ); ?>"></label>
			</details>

			<p style="margin-top:16px">
				<button class="button button-primary button-large"><?php echo $ed ? 'Guardar cambios' : 'Agregar destino y crear página'; ?></button>
				<?php if ( $ed ) : ?>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=grenvios-destinos' ) ); ?>" class="button">Cancelar</a>
					<a href="<?php echo esc_url( $home . '/destinos/' . $edit . '/' ); ?>" target="_blank" class="button">Ver página ↗</a>
				<?php endif; ?>
			</p>
		</form>

		<h2 style="margin-top:30px">Destinos agregados (<?php echo count( $custom ); ?>)</h2>
		<?php if ( ! $custom ) : ?>
			<p>Aún no has agregado destinos propios. Usa el formulario de arriba.</p>
		<?php else : ?>
			<table class="wp-list-table widefat fixed striped" style="max-width:900px">
				<thead><tr><th>País</th><th>Continente</th><th>Modalidad</th><th>Página</th><th style="width:180px">Acciones</th></tr></thead>
				<tbody>
				<?php foreach ( $custom as $slug => $d ) : ?>
					<tr>
						<td><strong><?php echo esc_html( $d['title'] ); ?></strong><br><code><?php echo esc_html( $slug ); ?></code></td>
						<td><?php echo esc_html( $d['continente'] ); ?></td>
						<td><?php echo esc_html( $d['modos'] ); ?></td>
						<td><a href="<?php echo esc_url( $home . '/destinos/' . $slug . '/' ); ?>" target="_blank">Ver ↗</a></td>
						<td>
							<a class="button button-small" href="<?php echo esc_url( admin_url( 'admin.php?page=grenvios-destinos&edit=' . $slug ) ); ?>">Editar</a>
							<form method="post" action="<?php echo esc_url( $action ); ?>" style="display:inline">
								<?php wp_nonce_field( 'grenvios_dest_admin' ); ?>
								<input type="hidden" name="action" value="grenvios_dest_save">
								<input type="hidden" name="gv_action" value="delete">
								<input type="hidden" name="slug" value="<?php echo esc_attr( $slug ); ?>">
								<button class="button button-small button-link-delete" onclick="return confirm('¿Eliminar el destino &quot;<?php echo esc_js( $d['title'] ); ?>&quot;? También se borrará su página.');">Eliminar</button>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

		<h2 style="margin-top:30px">Destinos fijos del tema (<?php echo count( $fixed ); ?>)</h2>
		<p style="color:#777">Estos vienen con el tema y tienen textos propios optimizados; se editan desde su página (botón <em>Editar</em>) o en el código.</p>
		<p><?php echo esc_html( implode( ' · ', wp_list_pluck( $fixed, 'title' ) ) ); ?></p>
	</div>
	<script>
	(function(){
		var t=document.querySelector('input[name="title"]'), p=document.getElementById('gv-slug-prev');
		if(t&&p){ t.addEventListener('input',function(){
			var s=this.value.toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g,'').replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,'');
			p.textContent=s||'pais';
		}); }
	})();
	</script>
	<?php
}

/* ══════════════════════════════════════
   DÓNDE OPERAMOS · token {{paises_operamos}}
   Bloque para la página Nosotros: los países a los que se trabaja, agrupados
   por continente y enlazados a su página. Cumple dos funciones a la vez —
   responde a «¿a dónde llegan?», que es la primera pregunta de quien no conoce
   la empresa, y reparte enlaces internos hacia las páginas de país, que son
   las que tienen que posicionar.
══════════════════════════════════════ */
function grenvios_paises_operamos_html() {
	$destinos = grenvios_destinos();
	if ( ! $destinos ) return '';

	$por_continente = array();
	foreach ( $destinos as $slug => $d ) {
		$por_continente[ $d['continente'] ][ $slug ] = $d;
	}

	$h = '<div class="gr-operamos">';
	foreach ( grenvios_destino_continentes() as $cont ) {
		if ( empty( $por_continente[ $cont ] ) ) continue;   // continente sin países: no se pinta
		$h .= '<div class="gr-operamos-grupo"><h3>' . esc_html( grenvios_t( $cont ) ) . '</h3><ul>';
		foreach ( $por_continente[ $cont ] as $slug => $d ) {
			// Mismo filtro que el submenú del encabezado: si ese país tiene ruta
			// propia, el enlace va a su ruta y el usuario no sale de la suya.
			$url = apply_filters( 'grenvios_destino_permalink', home_url( '/destinos/' . $slug . '/' ), $slug );
			$h  .= '<li><a href="' . esc_url( $url ) . '">' . esc_html( $d['title'] ) . '</a>'
				. '<span>' . esc_html( $d['tiempo'] ) . ' · ' . esc_html( $d['modos'] ) . '</span></li>';
		}
		$h .= '</ul></div>';
	}
	return $h . '</div>';
}

/* Frase corta para la ficha de un país: «también trabajamos la ruta a Cuba». */
function grenvios_pais_operamos_frase( $pais_slug ) {
	$d = grenvios_destinos();
	if ( ! isset( $d[ $pais_slug ] ) ) return '';
	$t = $d[ $pais_slug ]['title'];
	return sprintf(
		grenvios_t( 'Esta es una de las rutas que operamos desde {{origen_ciudad}}: enviamos a %1$s de forma regular, con %2$s y entrega %3$s.' ),
		$t,
		mb_strtolower( $d[ $pais_slug ]['modos'] ),
		mb_strtolower( $d[ $pais_slug ]['entrega'] )
	);
}

/* El token se resuelve en cualquier plantilla o texto del editor. */
add_filter( 'grenvios_text_html', function ( $html ) {
	if ( strpos( $html, '{{paises_operamos}}' ) === false ) return $html;
	return str_replace( '{{paises_operamos}}', grenvios_paises_operamos_html(), $html );
}, 5 );
