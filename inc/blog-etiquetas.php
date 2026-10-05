<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Etiquetas del blog: el segundo eje del enlazado interno
 * ══════════════════════════════════════════════════════════════════════════
 *
 * EL HUECO QUE TAPA ESTE MÓDULO
 * Había 302 entradas, 5 categorías por ruta… y CERO etiquetas. Las categorías
 * agrupan por familia («Destinos», «Aduanas»), que es un corte muy grueso: la
 * categoría «Destinos» tiene 63 entradas en la ruta principal y no le sirve de
 * nada a la página de «Tiempos de entrega», que necesita exactamente las diez
 * guías de plazos y ninguna más.
 *
 * Las etiquetas hacen ese corte fino, y con él:
 *
 *   · Cada etiqueta apunta a UNA página de dinero, igual que las guías
 *     (inc/seo-clusters.php). Su archivo deja de ser una lista suelta y pasa a
 *     ser la puerta de entrada al servicio: diez guías de precios enlazando a
 *     «Cotizar» valen más que diez guías sueltas.
 *   · En la ruta principal se crean además **etiquetas por país** («Envíos a
 *     Chile»), que agrupan las nueve guías de ese destino y empujan a su ficha.
 *     Es el enlazado que más falta le hacía a las fichas de destino.
 *   · Las entradas ganan un bloque de navegación transversal: quien lee cómo
 *     embalar encuentra las demás guías de embalaje, no las de Cuba.
 *
 * INDEXACIÓN, CON CRITERIO
 * No se indexan todas: un archivo con dos entradas es contenido delgado y
 * compite contra las propias guías. Solo entra al índice la etiqueta que tiene
 * descripción propia y al menos cuatro entradas en su ruta — en la práctica,
 * las de la ruta principal y las de país con volumen. El resto navega pero no
 * se indexa, con la regla que ya existía en inc/seo-clusters.php.
 *
 * TODO ES IDEMPOTENTE: `grenvios_etq_sync()` se puede ejecutar mil veces; crea
 * lo que falta, corrige descripciones vacías y no duplica nada.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ─────────────────────────────────────────────────────────────────────────
 * 1) Definición de las etiquetas temáticas
 *
 *    patrones → se buscan como subcadena en la clave de la guía
 *    (meta `grenvios_guia_key`) y en su slug, así que valen igual para la
 *    entrada maestra («como-embalar-un-paquete») y para su copia por país
 *    («como-embalar-un-paquete-a-chile»).
 *
 *    pilar → slug canónico de la página de dinero que refuerza. Se traduce
 *    sola a la página equivalente de cada ruta.
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_etq_def() {
	return apply_filters( 'grenvios_etq_def', array(

		'peso-volumetrico' => array(
			'nombre'   => 'Peso volumétrico',
			'pilar'    => 'servicios/peso-volumetrico',
			'patrones' => array( 'peso-volumetrico', 'cuanto-cuesta-un-envio' ),
			'desc'     => 'El peso volumétrico es la razón por la que dos paquetes que pesan lo mismo no cuestan lo mismo: se cobra el mayor entre el peso real y el que corresponde al espacio que ocupa la caja (alto × largo × ancho ÷ 5000). Aquí están las guías que explican cómo medirlo, cómo elegir una caja que no te haga pagar por aire y en qué casos conviene rehacer el embalaje antes de despachar.',
		),

		'embalaje' => array(
			'nombre'   => 'Embalaje',
			'pilar'    => 'como-enviar-un-paquete-al-extranjero',
			'patrones' => array( 'como-embalar' ),
			'desc'     => 'Un embalaje bien hecho cumple dos funciones a la vez: protege el contenido en cuatro tramos de transporte y evita que pagues volumen de más. Estas guías cubren qué caja elegir, cómo rellenar los huecos, cómo proteger lo frágil y qué errores de embalaje son los que más reclamos generan.',
		),

		'envio-de-paquetes' => array(
			'nombre'   => 'Envío de paquetes',
			'pilar'    => 'servicios/envio-internacional-de-paquetes',
			'patrones' => array( 'como-enviar-un-paquete' ),
			'desc'     => 'El paso a paso completo de un paquete internacional: qué datos necesita el destinatario, cómo se declara el contenido, qué vía conviene según lo que envías y qué ocurre en cada tramo hasta la entrega. Son las guías de referencia para quien envía por primera vez.',
		),

		'precios-de-envio' => array(
			'nombre'   => 'Precios de envío',
			'pilar'    => 'cotizar',
			'patrones' => array( 'cuanto-cuesta' ),
			'desc'     => 'Cuánto cuesta enviar y, sobre todo, por qué cuesta eso. El precio de un envío internacional depende del peso que se cobra, de la vía —aérea o terrestre—, del destino y del valor declarado. Estas guías desglosan cada factor país por país para que puedas estimar antes de cotizar y comparar presupuestos con la misma vara.',
		),

		'plazos-de-entrega' => array(
			'nombre'   => 'Plazos de entrega',
			'pilar'    => 'tiempos-de-entrega',
			'patrones' => array( 'cuanto-demora' ),
			'desc'     => 'Cuánto demora un envío a cada destino y de qué depende. El plazo son cuatro tramos encadenados —despacho, trayecto internacional, aduana y distribución local—, se cuenta en días hábiles desde el despacho y solo uno de ellos, el de aduana, es imprevisible. Aquí está el detalle por país y lo que puedes hacer para que no se alargue.',
		),

		'equipaje-y-mudanzas' => array(
			'nombre'   => 'Equipaje y mudanzas',
			'pilar'    => 'servicios/envio-de-equipaje',
			'patrones' => array( 'equipaje', 'mudarse-a' ),
			'desc'     => 'Enviar el equipaje por delante en vez de pagar exceso en el aeropuerto, o mover una casa entera a otro país. Estas guías explican cuándo sale a cuenta, cómo se embala lo que ya está usado, qué pide la aduana en una mudanza y qué conviene llevar contigo en vez de enviar.',
		),

		'compras-desde-el-extranjero' => array(
			'nombre'   => 'Compras desde el extranjero',
			'pilar'    => 'servicios/envio-de-compras',
			'patrones' => array( 'compras-hechas' ),
			'desc'     => 'Comprar en tiendas que no despachan fuera del país y recibirlo igual: se compra con nuestra dirección como destino, se consolidan los pedidos en un solo bulto y se paga un flete en vez de cinco. Aquí está cómo funciona, qué se puede consolidar y qué conviene revisar antes de darle a comprar.',
		),

		'rastreo-de-envios' => array(
			'nombre'   => 'Rastreo de envíos',
			'pilar'    => 'rastreo-de-envios',
			'patrones' => array( 'rastrear' ),
			'desc'     => 'Dónde está tu envío y qué significa cada estado del seguimiento. Un envío que lleva días «en tránsito» y uno detenido en aduana no son lo mismo y no se resuelven igual. Estas guías explican cómo leer el recorrido por número de guía y cuándo conviene avisarnos.',
		),

		'productos-prohibidos' => array(
			'nombre'   => 'Qué se puede enviar',
			'pilar'    => 'que-se-puede-enviar',
			'patrones' => array( 'que-se-puede-enviar', 'que-no-se-puede-enviar' ),
			'desc'     => 'Lo que cada aduana admite y lo que retiene. Las listas no son iguales de un país a otro: lo que entra sin problema a uno puede estar restringido en el vecino, y hay categorías —líquidos, cosméticos, suplementos, electrónica con batería, alimentos— que tienen reglas propias en casi todas partes. Consúltalo antes de comprar la caja, no después.',
		),

		'aduanas' => array(
			'nombre'   => 'Trámites de aduana',
			'pilar'    => 'aduanas-e-impuestos',
			'patrones' => array( 'documentos-para-aduana', 'documentos-de-exportacion' ),
			'desc'     => 'La documentación es la mitad del trabajo de un envío internacional: la mayoría de los envíos que se detienen no fallan en el transporte, sino en una factura mal emitida o una descripción de contenido que no coincide con lo que va dentro. Aquí está qué papeles hacen falta, quién los firma y cómo se declara un contenido para que no invite a la revisión.',
		),

		'apostilla-y-documentos' => array(
			'nombre'   => 'Documentos y apostilla',
			'pilar'    => 'servicios/apostilla-y-traduccion',
			'patrones' => array( 'apostilla', 'documento-urgente', 'enviar-documentos' ),
			'desc'     => 'Títulos, partidas, poderes y contratos que tienen que surtir efecto legal en otro país. Antes de enviarlos suele hacer falta apostillarlos y, según el destino, traducirlos oficialmente: enviar rápido un documento que luego no aceptan es perder el plazo dos veces. Estas guías cubren el trámite y el envío.',
		),

		'encomiendas-familiares' => array(
			'nombre'   => 'Encomiendas a familiares',
			'pilar'    => 'servicios/envio-internacional-de-paquetes',
			'patrones' => array( 'encomiendas' ),
			'desc'     => 'El envío más frecuente de todos: el paquete que se manda a la familia que vive fuera. Ropa, medicinas, alimentos envasados, cosas que allá no se consiguen o cuestan el triple. Estas guías reúnen lo que conviene saber de cada destino para que llegue completo, sin sorpresas en aduana y sin pagar por aire.',
		),

		'envios-para-empresas' => array(
			'nombre'   => 'Envíos de empresa',
			'pilar'    => 'envios-para-empresas',
			'patrones' => array( 'envios-recurrentes' ),
			'desc'     => 'Cuando enviar deja de ser algo puntual: envíos recurrentes, documentación de exportación, consolidación de proveedores y control de costos. Estas guías están escritas para quien tiene que justificar cada flete y necesita que el proceso sea el mismo todas las semanas.',
		),

		'aerea-o-terrestre' => array(
			'nombre'   => 'Vía aérea o terrestre',
			'pilar'    => 'servicios/envio-internacional-de-paquetes',
			'patrones' => array( 'aereo-o-terrestre' ),
			'desc'     => 'La decisión que más cambia la factura en bultos voluminosos. La vía aérea se paga por rapidez; la terrestre, por volumen, y hacia países vecinos la diferencia es grande. No todos los destinos tienen las dos: aquí está cuándo compensa cada una.',
		),

		'fechas-clave' => array(
			'nombre'   => 'Fechas y temporadas',
			'pilar'    => 'tiempos-de-entrega',
			'patrones' => array( 'fechas-clave' ),
			'desc'     => 'Navidad, Día de la Madre, inicio de clases, fiestas patrias de cada país: las fechas en las que todo el mundo envía a la vez son también en las que las aduanas y las distribuidoras locales van más lentas. Estas guías dicen con cuánta antelación conviene despachar a cada destino para que llegue a tiempo.',
		),

		'ciudades-de-destino' => array(
			'nombre'   => 'Ciudades de destino',
			'pilar'    => 'destinos',
			'patrones' => array( 'ciudades-de' ),
			'desc'     => 'A qué ciudades llega cada destino y cómo se entrega en cada una: a domicilio en las principales, con retiro en agencia local en varias del interior. Saberlo antes de despachar evita el error más común de todos, que es poner una dirección a la que el envío no llega.',
		),
	) );
}

/* ─────────────────────────────────────────────────────────────────────────
 * 2) Etiquetas por país — solo en la ruta principal
 *
 *    En una ruta de país no tienen sentido: allí TODAS las entradas son de ese
 *    país, así que la etiqueta agruparía las 23 y no distinguiría nada. En la
 *    principal, en cambio, agrupan las nueve guías de cada destino y empujan a
 *    su ficha, que es la página que vende.
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_etq_paises() {
	$dest = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();
	$out  = array();
	foreach ( $dest as $slug => $d ) {
		$nombre = isset( $d['title'] ) && $d['title'] !== '' ? $d['title'] : ucfirst( $slug );
		$nombre = trim( preg_replace( '/^env[ií]os?\s+a\s+/iu', '', $nombre ) );
		$out[ 'envios-a-' . $slug ] = array(
			'nombre'   => 'Envíos a ' . $nombre,
			'pilar'    => 'destinos/' . $slug,
			'patrones' => array( '-a-' . $slug, '-en-' . $slug, 'ciudades-de-' . $slug ),
			'desc'     => 'Todas las guías sobre envíos a ' . $nombre . ' reunidas en un solo sitio: cuánto cuesta, cuánto demora, qué admite su aduana, a qué ciudades se entrega a domicilio, cómo se envían documentos y qué conviene tener listo antes de despachar. Si ya sabes lo que necesitas, la ficha del destino tiene las condiciones y el plazo actualizados.',
		);
	}
	return apply_filters( 'grenvios_etq_paises', $out );
}

/* Todas las etiquetas que corresponden a una ruta. */
function grenvios_etq_para_ruta( $lang ) {
	$defs = grenvios_etq_def();
	$def  = function_exists( 'grenvios_i18n_default' ) ? grenvios_i18n_default() : '';
	if ( $lang === $def ) $defs = array_merge( $defs, grenvios_etq_paises() );
	return $defs;
}

/* ─────────────────────────────────────────────────────────────────────────
 * 3) Sincronización: crear términos, traducirlos y asignarlos
 * ───────────────────────────────────────────────────────────────────────── */

/* Sufijo de slug de una ruta, el mismo criterio que usan las páginas y las
 * copias de las entradas: dos términos no pueden compartir slug aunque estén
 * en rutas distintas, y el país es además la palabra que se busca. */
function grenvios_etq_sufijo( $lang ) {
	$s = function_exists( 'grenvios_sede_destino_nombre' ) ? grenvios_sede_destino_nombre( $lang ) : '';
	$s = sanitize_title( remove_accents( $s ) );
	return $s !== '' ? $s : $lang;
}

/* Página de dinero equivalente en una ruta. */
function grenvios_etq_pagina( $ruta_slug, $lang ) {
	$page = get_page_by_path( $ruta_slug );
	if ( ! $page ) {
		$hoja = basename( $ruta_slug );
		foreach ( array( '', 'servicios/', 'destinos/' ) as $pref ) {
			$page = get_page_by_path( $pref . $hoja );
			if ( $page ) break;
		}
	}
	if ( ! $page ) return 0;
	if ( function_exists( 'pll_get_post' ) ) {
		$tid = (int) pll_get_post( $page->ID, $lang );
		if ( $tid ) return $tid;
	}
	return (int) $page->ID;
}

/* Busca la etiqueta por slug SIN pasar por Polylang.
 *
 * `get_term_by()` filtra por la lengua activa, que en CLI es siempre la
 * principal: las etiquetas de las rutas de país resultaban invisibles y cada
 * pasada creaba un duplicado con el código de ruta pegado al slug. La tabla no
 * miente y el slug ya es único en toda la taxonomía. */
function grenvios_etq_term( $slug, $lang = '' ) {
	global $wpdb;
	if ( function_exists( 'grenvios_upn_slug_nuevo' ) ) $slug = grenvios_upn_slug_nuevo( $slug, 'post_tag' );
	$id = $wpdb->get_var( $wpdb->prepare(
		"SELECT t.term_id FROM {$wpdb->terms} t
		 INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_id = t.term_id
		 WHERE tt.taxonomy = 'post_tag' AND t.slug = %s LIMIT 1",
		$slug
	) );
	return (int) $id;
}

/**
 * Crea/actualiza las etiquetas de una ruta y se las asigna a sus entradas.
 * Devuelve un resumen. Idempotente.
 */
function grenvios_etq_sync( $lang = '' ) {
	$res = array( 'creadas' => 0, 'actualizadas' => 0, 'asignaciones' => 0, 'entradas' => 0 );
	if ( ! function_exists( 'pll_languages_list' ) ) return $res;

	$langs = $lang !== '' ? array( $lang ) : pll_languages_list();
	$def   = function_exists( 'grenvios_i18n_default' ) ? grenvios_i18n_default() : '';
	$mapa  = function_exists( 'grenvios_category_pillar_option' ) ? grenvios_category_pillar_option() : array();

	foreach ( $langs as $l ) {
		$sufijo = grenvios_etq_sufijo( $l );
		$ids    = array();   // clave de etiqueta → term_id en esta ruta

		foreach ( grenvios_etq_para_ruta( $l ) as $clave => $e ) {
			$slug = ( $l === $def ) ? $clave : $clave . '-' . $sufijo;
			/* Slug renombrado al pasar los términos a primer nivel (inc/urls-primer-nivel.php). */
			if ( function_exists( 'grenvios_upn_slug_nuevo' ) ) $slug = grenvios_upn_slug_nuevo( $slug, 'post_tag' );
			$tid  = grenvios_etq_term( $slug, $l );

			if ( ! $tid ) {
				$nuevo = wp_insert_term( $e['nombre'], 'post_tag', array( 'slug' => $slug, 'description' => $e['desc'] ) );
				if ( is_wp_error( $nuevo ) ) continue;
				$tid = (int) $nuevo['term_id'];
				$res['creadas']++;
			} else {
				$term = get_term( $tid, 'post_tag' );
				if ( $term && trim( (string) $term->description ) !== $e['desc'] ) {
					wp_update_term( $tid, 'post_tag', array( 'description' => $e['desc'], 'name' => $e['nombre'] ) );
					$res['actualizadas']++;
				}
			}

			/* Siempre explícito: al insertar un término, Polylang le pone la
			 * lengua activa —en CLI, la principal—, así que preguntar «¿ya
			 * tiene idioma?» devolvía «pe» para las diez rutas y las etiquetas
			 * de país nacían en la ruta equivocada, sin poder asignarse a sus
			 * entradas. */
			if ( function_exists( 'pll_set_term_language' ) && pll_get_term_language( $tid ) !== $l ) {
				pll_set_term_language( $tid, $l );
			}
			$ids[ $clave ] = $tid;

			/* La etiqueta refuerza una página, igual que las categorías. Se
			 * guarda en el mismo mapa: los ids de término no se pisan entre
			 * taxonomías. */
			$pid = grenvios_etq_pagina( $e['pilar'], $l );
			if ( $pid ) $mapa[ $tid ] = $pid;
		}

		/* Traducciones entre rutas: la etiqueta de Chile es la misma que la de
		 * la principal, para que el selector de ruta no pierda la página. */
		if ( $l !== $def && function_exists( 'pll_save_term_translations' ) ) {
			foreach ( $ids as $clave => $tid ) {
				$base = grenvios_etq_term( $clave, $def );
				if ( ! $base ) continue;
				$tr = pll_get_term_translations( $base );
				if ( isset( $tr[ $l ] ) && (int) $tr[ $l ] === $tid ) continue;
				$tr[ $def ] = $base;
				$tr[ $l ]   = $tid;
				pll_save_term_translations( $tr );
			}
		}

		/* Asignación a las entradas de la ruta. */
		$posts = get_posts( array( 'post_type' => 'post', 'post_status' => 'any', 'numberposts' => -1, 'lang' => $l ) );
		foreach ( $posts as $p ) {
			$res['entradas']++;
			$clave_guia = (string) get_post_meta( $p->ID, 'grenvios_guia_key', true );
			$aguja      = $clave_guia . ' ' . $p->post_name;
			$asignar    = array();

			foreach ( grenvios_etq_para_ruta( $l ) as $clave => $e ) {
				if ( ! isset( $ids[ $clave ] ) ) continue;
				foreach ( $e['patrones'] as $pat ) {
					if ( strpos( $aguja, $pat ) !== false ) { $asignar[] = $ids[ $clave ]; break; }
				}
			}
			if ( ! $asignar ) continue;

			$ya = wp_get_post_terms( $p->ID, 'post_tag', array( 'fields' => 'ids' ) );
			$ya = is_wp_error( $ya ) ? array() : array_map( 'intval', $ya );
			sort( $ya );
			$nuevo = array_values( array_unique( array_map( 'intval', $asignar ) ) );
			sort( $nuevo );
			if ( $ya === $nuevo ) continue;

			wp_set_post_terms( $p->ID, $nuevo, 'post_tag', false );
			$res['asignaciones'] += count( $nuevo );
		}
	}

	/* Las CATEGORÍAS de la ruta principal apuntaban a ids de página que ya no
	 * existen —quedaron de una importación anterior—, así que sus archivos no
	 * enlazaban a nada y el bloque de pilar no se pintaba. Se reconstruye desde
	 * el slug declarado en grenvios_categorias_base(). */
	if ( function_exists( 'grenvios_categorias_base' ) ) {
		foreach ( grenvios_categorias_base() as $cslug => $c ) {
			$term = get_term_by( 'slug', $cslug, 'category' );
			if ( ! $term || is_wp_error( $term ) ) continue;
			$actual = isset( $mapa[ $term->term_id ] ) ? (int) $mapa[ $term->term_id ] : 0;
			if ( $actual && get_post_status( $actual ) === 'publish' ) continue;
			$pid = grenvios_etq_pagina( $c['pilar'], $def );
			if ( $pid ) $mapa[ (int) $term->term_id ] = $pid;
		}
	}

	/* De paso, las CATEGORÍAS de las rutas de país: el mapa de pilares se creó
	 * solo para las cinco categorías de la ruta principal, así que el archivo
	 * de «Aduanas y restricciones» de Chile no enlazaba a ninguna página. La
	 * traducción de la categoría apunta a la traducción de su pilar. */
	if ( function_exists( 'pll_get_term_translations' ) ) {
		foreach ( get_terms( array( 'taxonomy' => 'category', 'hide_empty' => false, 'lang' => $def ) ) as $cat ) {
			if ( is_wp_error( $cat ) || empty( $mapa[ $cat->term_id ] ) ) continue;
			$pagina = (int) $mapa[ $cat->term_id ];
			foreach ( pll_get_term_translations( $cat->term_id ) as $l => $tid ) {
				if ( $l === $def || ! empty( $mapa[ $tid ] ) ) continue;
				$tr = (int) pll_get_post( $pagina, $l );
				if ( $tr ) $mapa[ (int) $tid ] = $tr;
			}
		}
	}

	if ( function_exists( 'grenvios_category_pillar_option' ) ) {
		update_option( 'grenvios_cat_pillar', $mapa, false );
	}
	delete_transient( 'grenvios_links_graph' );
	return $res;
}

/* ─────────────────────────────────────────────────────────────────────────
 * 4) Indexación: solo las etiquetas con contenido real
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_etq_indexable( $term ) {
	if ( ! $term || is_wp_error( $term ) ) return false;
	if ( trim( (string) $term->description ) === '' ) return false;
	return (int) $term->count >= (int) apply_filters( 'grenvios_etq_minimo', 4 );
}

add_filter( 'grenvios_noindex_tag', function ( $noindex ) {
	if ( ! is_tag() ) return $noindex;
	return grenvios_etq_indexable( get_queried_object() ) ? false : $noindex;
}, 20 );

/* Las etiquetas indexables entran al sitemap de su ruta; las demás, no. */
add_filter( 'grenvios_sitemap_urls', function ( $urls, $lang ) {
	foreach ( get_terms( array( 'taxonomy' => 'post_tag', 'hide_empty' => true, 'lang' => $lang ) ) as $t ) {
		if ( ! grenvios_etq_indexable( $t ) ) continue;
		$urls[] = array( 'loc' => get_term_link( $t ), 'priority' => '0.4' );
	}
	return $urls;
}, 10, 2 );

/* ─────────────────────────────────────────────────────────────────────────
 * 5) Cabecera del archivo de etiqueta
 *    Se imprime desde index.php. Sin esto, el archivo es un listado de
 *    tarjetas: cero texto propio y ningún enlace a la página que vende.
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_etq_render_cabecera() {
	if ( ! is_tag() ) return;
	$term = get_queried_object();
	if ( ! $term || is_wp_error( $term ) ) return;

	$t   = function ( $s ) { return function_exists( 'grenvios_t' ) ? grenvios_t( $s ) : $s; };
	$map = function_exists( 'grenvios_category_pillar_option' ) ? grenvios_category_pillar_option() : array();
	$pid = isset( $map[ $term->term_id ] ) ? (int) $map[ $term->term_id ] : 0;

	echo '<section class="grenvios-cluster-pillar gr-tag-intro"><div class="container">';

	if ( trim( (string) $term->description ) !== '' ) {
		echo '<div class="grenvios-cluster-desc"><p>' . wp_kses_post( $term->description ) . '</p></div>';
	}

	if ( $pid && get_post_status( $pid ) === 'publish' ) {
		$kw = function_exists( 'grenvios_seo_kw' ) ? grenvios_seo_kw( $pid ) : get_the_title( $pid );
		echo '<p class="gr-tag-pilar">' . esc_html( $t( 'Si ya sabes lo que necesitas, ve directo al servicio:' ) )
			. ' <a href="' . esc_url( get_permalink( $pid ) ) . '"><strong>' . esc_html( $kw ) . '</strong></a>.</p>';
	}

	/* Etiquetas hermanas: el archivo deja de ser un callejón sin salida. */
	$otras = get_terms( array(
		'taxonomy'   => 'post_tag',
		'hide_empty' => true,
		'exclude'    => array( $term->term_id ),
		'orderby'    => 'count',
		'order'      => 'DESC',
		'number'     => 8,
		'lang'       => function_exists( 'grenvios_i18n_current' ) ? grenvios_i18n_current() : '',
	) );
	if ( ! empty( $otras ) && ! is_wp_error( $otras ) ) {
		echo '<p class="gr-tag-otras"><span>' . esc_html( $t( 'Otros temas del blog:' ) ) . '</span> ';
		$links = array();
		foreach ( $otras as $o ) {
			$links[] = '<a href="' . esc_url( get_term_link( $o ) ) . '">' . esc_html( $o->name ) . '</a>';
		}
		echo wp_kses_post( implode( ' · ', $links ) ) . '</p>';
	}

	echo '</div></section>';
}

add_action( 'wp_head', function () {
	if ( is_admin() ) return;
	echo '<style id="grenvios-etq-css">'
		. '.gr-tag-intro{padding:18px 0 0}'
		. '.gr-tag-intro .grenvios-cluster-desc p{margin:0 0 10px}'
		. '.gr-tag-pilar{margin:0 0 8px}'
		. '.gr-tag-otras{font-size:14px;opacity:.85;margin:0}'
		. '.gr-tag-otras span{font-weight:600;opacity:.9}'
		. '.gr-post-tags{margin:28px 0 0;font-size:14px}'
		. '.gr-post-tags a{display:inline-block;margin:0 6px 6px 0;padding:5px 12px;border:1px solid rgba(0,0,0,.12);border-radius:999px}'
		. '</style>';
}, 104 );

/* ─────────────────────────────────────────────────────────────────────────
 * 6) Etiquetas al pie de cada entrada
 *    Es el enlace que convierte 302 entradas sueltas en 16 temas navegables.
 * ───────────────────────────────────────────────────────────────────────── */
add_filter( 'the_content', function ( $content ) {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) return $content;
	$tags = get_the_tags();
	if ( empty( $tags ) || is_wp_error( $tags ) ) return $content;

	$t    = function ( $s ) { return function_exists( 'grenvios_t' ) ? grenvios_t( $s ) : $s; };
	$out  = '<nav class="gr-post-tags" aria-label="' . esc_attr( $t( 'Temas de esta guía' ) ) . '">';
	$out .= '<strong>' . esc_html( $t( 'Temas de esta guía:' ) ) . '</strong> ';
	foreach ( $tags as $tg ) {
		$out .= '<a href="' . esc_url( get_term_link( $tg ) ) . '">' . esc_html( $tg->name ) . '</a>';
	}
	$out .= '</nav>';

	return $content . $out;
}, 18 );

add_filter( 'grenvios_i18n_extra_strings', function ( $textos ) {
	foreach ( array(
		'Si ya sabes lo que necesitas, ve directo al servicio:',
		'Otros temas del blog:', 'Temas de esta guía:', 'Temas de esta guía',
	) as $s ) $textos[] = $s;
	return $textos;
} );
