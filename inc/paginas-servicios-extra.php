<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Tres servicios nuevos (2026-09-27)
 * ══════════════════════════════════════════════════════════════════════════
 *
 *   /servicios/envio-de-correspondencia-internacional/   «envío de correspondencia internacional»
 *   /servicios/envio-de-muestras-comerciales/            «envío de muestras comerciales»
 *   /servicios/envio-de-celulares-y-laptops/             «enviar celular / laptop al extranjero»
 *
 * Búsquedas del mapa de keywords sin página propia (ver MAPA-KEYWORDS.md).
 *
 * A diferencia de inc/paginas-nuevas-seo.php, aquí el contenido es DATOS: una
 * sola definición por página (grenvios_pse_def) que usan a la vez el render y
 * el registro del panel «Editar página». Así el texto por defecto de un campo
 * y el que se pinta no pueden desincronizarse.
 *
 * Tipos de bloque:
 *   intro  → h2 + párrafo centrado
 *   panels → h2 (+ texto) + rejilla de tarjetas título/texto (+ nota)
 *   lista  → h2 (+ texto) + lista con vistos, una línea por punto (+ nota)
 *   pasos  → h2 (+ texto) + pasos numerados título/texto (+ nota)
 *
 * Criterio de redacción, el del resto del tema: cero cifras inventadas. Se
 * afirma solo lo que el sitio ya afirma (vía aérea/terrestre, peso
 * volumétrico ÷ 5000, seguimiento por guía, impuesto terrestre pagado en
 * {{origen_ciudad}}); plazos y precios, al cotizar.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function grenvios_pse_slugs() {
	/* Filtro `grenvios_pse_slugs`: otros módulos suman páginas con el mismo motor
	 * (ver inc/paginas-servicios-extra2.php), definiéndolas en `grenvios_pse_paginas`. */
	return apply_filters( 'grenvios_pse_slugs', array( 'envio-de-correspondencia-internacional', 'envio-de-muestras-comerciales', 'envio-de-celulares-y-laptops' ) );
}

/* Página padre: «servicios» salvo que la definición diga otra cosa ('' = raíz). */
function grenvios_pse_parent( $slug ) {
	$def = grenvios_pse_def( $slug );
	return ( $def && array_key_exists( 'parent', $def ) ) ? (string) $def['parent'] : 'servicios';
}
function grenvios_pse_path( $slug ) {
	$p = grenvios_pse_parent( $slug );
	return $p !== '' ? $p . '/' . $slug : $slug;
}

/* ══════════════════════════════════════
   1) CONTENIDO (una sola fuente)
══════════════════════════════════════ */
function grenvios_pse_def( $slug ) {
	$u = function ( $p ) { return function_exists( 'grenvios_pn_url' ) ? grenvios_pn_url( $p ) : esc_url( home_url( $p ) ); };
	$d = array(

	/* ── Correspondencia ─────────────────────────────────────────────── */
	'envio-de-correspondencia-internacional' => array(
		'label' => 'Correspondencia',
		'hero'  => array( 'Cartas, sobres y tarjetas', 'Envío de correspondencia internacional desde {{origen_ciudad}}' ),
		'bloques' => array(
			array( 'tipo' => 'intro', 'k' => 'co_intro',
				'titulo' => 'Una carta que llega con nombre, fecha y firma',
				'texto'  => 'El envío de correspondencia internacional es para todo lo que cabe en un sobre y tiene que llegar a una persona concreta: cartas, tarjetas, invitaciones, fotografías, papeles personales. Lo mandamos por vía aérea, se cobra como un envío liviano y lo sigues con número de guía hasta que tu destinatario lo recibe.' ),
			array( 'tipo' => 'panels', 'k' => 'co_dif',
				'titulo' => 'Correo postal o courier: qué cambia',
				'panels' => array(
					array( 'Correo postal', 'Sirve para lo que no corre prisa y no importa demasiado si se retrasa. El seguimiento suele ser parcial y la entrega depende del servicio postal de cada país.' ),
					array( 'Courier', 'Cada sobre tiene su número de guía, sigues cada tramo y sabes cuándo se entregó. Es lo que conviene cuando el contenido importa: una invitación con fecha, una carta que tiene que llegar antes de un día concreto o papeles personales.' ),
				),
				'nota' => 'Si lo que envías son documentos para un trámite —contratos, poderes, partidas—, mira <a href="' . $u( '/servicios/envio-internacional-de-documentos/' ) . '">envío internacional de documentos</a> y, si deben tener validez legal fuera, <a href="' . $u( '/servicios/apostilla-y-traduccion/' ) . '">apostilla y traducción</a>.' ),
			array( 'tipo' => 'lista', 'k' => 'co_si', 'fondo' => true,
				'titulo' => 'Qué se suele enviar como correspondencia',
				'items'  => array(
					'Cartas y tarjetas: cumpleaños, fiestas, condolencias.',
					'Invitaciones de boda, bautizo o graduación, con su sobre.',
					'Fotografías impresas y recuerdos de papel.',
					'Copias de documentos personales y estados de cuenta.',
					'Dibujos, postales y papeles que no se pueden doblar, en sobre rígido.',
				) ),
			array( 'tipo' => 'lista', 'k' => 'co_no',
				'titulo' => 'Lo que no va dentro de un sobre',
				'items'  => array(
					'Dinero en efectivo, cheques o tarjetas bancarias.',
					'Objetos: llaves, memorias USB o joyas convierten el sobre en paquete y se cobran como tal.',
					'Originales que tienen que surtir efecto legal sin haberse apostillado antes.',
				),
				'nota' => 'Si dudas con algo, pregúntanos antes de cerrar el sobre: <a href="' . $u( '/cotizar/' ) . '">escríbenos</a>.' ),
			array( 'tipo' => 'pasos', 'k' => 'co_pasos', 'fondo' => true,
				'titulo' => 'Cómo enviarla',
				'pasos'  => array(
					array( 'Prepara el sobre.', 'Tamaño carta u oficio, bien cerrado. Si lleva fotos o papeles que no deben doblarse, usa un sobre rígido o añade un cartón dentro.' ),
					array( 'Escribe bien los datos.', 'Nombre completo, dirección con referencias, código postal y un teléfono del destinatario. El teléfono es lo que usa el repartidor si la dirección no cuadra.' ),
					array( 'Entréganoslo.', 'En nuestra oficina de {{origen_ciudad}} o en un recojo a domicilio. Lo revisamos contigo y lo despachamos.' ),
					array( 'Sigue el envío.', 'Te damos el número de guía para consultarlo en rastreo de envíos hasta la entrega.' ),
				) ),
		),
		'faqs' => array(
			array( '¿Cuánto tarda una carta en llegar al extranjero?', 'Depende del destino: la correspondencia viaja por vía aérea con los plazos de cada país, contados en días hábiles desde el despacho. Los tienes en la página de tiempos de entrega.' ),
			array( '¿Puedo enviar varias cartas en un solo envío?', 'Sí, si van a la misma dirección. Si van a personas distintas, cada una necesita su propio envío con sus datos.' ),
			array( '¿Qué pasa si el destinatario no está en casa?', 'El repartidor usa el teléfono que indicaste para coordinar la entrega. Por eso es el dato más importante del sobre después de la dirección.' ),
		),
		'relacionados' => array(
			'Te puede interesar' => array( 'servicios/envio-internacional-de-documentos', 'servicios/apostilla-y-traduccion', 'tiempos-de-entrega' ),
			'Siguiente paso'     => array( 'cotizar', 'rastreo-de-envios' ),
		),
	),

	/* ── Muestras comerciales ────────────────────────────────────────── */
	'envio-de-muestras-comerciales' => array(
		'label' => 'Muestras',
		'hero'  => array( 'Para exportadores y emprendedores', 'Envío de muestras comerciales al extranjero' ),
		'bloques' => array(
			array( 'tipo' => 'intro', 'k' => 'mu_intro',
				'titulo' => 'La muestra es lo primero que tu cliente ve de ti',
				'texto'  => 'Antes de un pedido casi siempre hay una muestra: unas prendas para un comprador, un kilo de café para un tostador, un catálogo de tejidos para una tienda. El envío de muestras comerciales tiene sus propias reglas en aduana y una exigencia extra: tiene que llegar a tiempo y presentable, porque de ella depende la venta.' ),
			array( 'tipo' => 'panels', 'k' => 'mu_claves', 'fondo' => true,
				'titulo' => 'Lo que la aduana mira en una muestra',
				'panels' => array(
					array( 'Que sea una muestra', 'Pocas unidades, identificadas como muestra y sin intención de venta en destino. Diez unidades iguales de lo mismo ya se leen como mercancía.' ),
					array( 'Una factura con valor', 'Aunque la muestra se regale, se declara su valor real en una factura proforma, con la descripción del producto y la indicación de que es para evaluación.' ),
					array( 'Permisos del producto', 'Alimentos, cosméticos y productos naturales pueden necesitar un permiso sanitario en destino incluso como muestra. Te decimos cuál antes de despachar.' ),
					array( 'El país que recibe', 'Cada aduana trata las muestras a su manera y algunas cobran impuestos aunque no haya venta. Te confirmamos el caso de tu destino al cotizar.' ),
				) ),
			array( 'tipo' => 'pasos', 'k' => 'mu_pasos',
				'titulo' => 'Cómo preparar el envío de una muestra',
				'pasos'  => array(
					array( 'Elige la cantidad mínima que convence.', 'La suficiente para evaluar el producto, no un lote. Menos unidades es menos peso, menos preguntas en aduana y menos impuesto.' ),
					array( 'Rotula cada unidad como muestra.', 'Una etiqueta de «muestra sin valor comercial» en el producto o en su envase ayuda a leer el envío correctamente.' ),
					array( 'Haz la factura proforma.', 'Datos de tu empresa y de quien recibe, descripción precisa, cantidad, valor unitario y el motivo del envío.' ),
					array( 'Cuida la presentación.', 'La muestra llega al escritorio de tu cliente: embalaje limpio, producto protegido y, si quieres, tu ficha técnica dentro.' ),
					array( 'Avisa a tu cliente.', 'Pásale el número de guía y dile qué va dentro, por si su aduana le pide un dato.' ),
				) ),
			array( 'tipo' => 'lista', 'k' => 'mu_err', 'fondo' => true,
				'titulo' => 'Errores que retrasan una muestra',
				'items'  => array(
					'Declarar valor cero: la aduana no se lo cree y abre el paquete.',
					'Enviar cantidades de pedido con la etiqueta de muestra.',
					'Olvidar el permiso sanitario en alimentos o cosméticos.',
					'Mandarla por la vía más lenta cuando el cliente tiene fecha de decisión.',
				),
				'nota' => 'Si envías muestras de forma habitual, mira <a href="' . $u( '/envios-para-empresas/' ) . '">envíos para empresas</a>: fijamos un circuito para no empezar de cero cada vez. Y cuando llegue el pedido, <a href="' . $u( '/servicios/carga-internacional/' ) . '">carga internacional</a>.' ),
		),
		'faqs' => array(
			array( '¿Las muestras pagan impuestos?', 'Depende del país de destino. Algunos dejan pasar las muestras sin valor comercial en cantidades pequeñas y otros cobran igual. Te lo confirmamos antes de despachar.' ),
			array( '¿Necesito ser empresa para enviar muestras?', 'No. Un emprendedor puede enviar muestras como persona. Lo que no cambia es que el contenido se declare con su valor real y su descripción.' ),
			array( '¿Puedo enviar muestras de alimentos?', 'Sí, envasadas, selladas y con etiqueta, y siempre que el destino las admita. Algunos países piden permiso sanitario también para muestras.' ),
		),
		'relacionados' => array(
			'Te puede interesar' => array( 'envios-para-empresas', 'servicios/carga-internacional', 'aduanas-e-impuestos' ),
			'Siguiente paso'     => array( 'cotizar' ),
		),
	),

	/* ── Celulares y laptops ─────────────────────────────────────────── */
	'envio-de-celulares-y-laptops' => array(
		'label' => 'Celulares y laptops',
		'hero'  => array( 'Electrónica con batería', 'Envío de celulares y laptops al extranjero' ),
		'bloques' => array(
			array( 'tipo' => 'intro', 'k' => 'el_intro',
				'titulo' => 'Se pueden enviar, y la batería decide cómo',
				'texto'  => 'Un celular o una laptop se pueden enviar al extranjero. Lo que los hace distintos a cualquier otro paquete es su batería de litio: por vía aérea está regulada como mercancía peligrosa y tiene condiciones propias. Por eso, antes de embalar, conviene saber qué vía admite tu equipo y cómo tiene que ir preparado.' ),
			array( 'tipo' => 'panels', 'k' => 'el_bat', 'fondo' => true,
				'titulo' => 'Qué cambia según la batería',
				'panels' => array(
					array( 'Batería dentro del equipo', 'Es el caso normal: el celular o la laptop con su batería instalada. Viaja apagado del todo y bien protegido, y te confirmamos al cotizar las condiciones de tu destino.' ),
					array( 'Baterías sueltas y power banks', 'Son las más restringidas por avión. Si no hacen falta, no las incluyas; si hacen falta, consúltanos antes.' ),
					array( 'Baterías dañadas o hinchadas', 'No viajan por ninguna vía. Un equipo con la batería inflada tiene que repararse antes de enviarse.' ),
					array( 'Vía terrestre', 'Hacia los países vecinos con ruta terrestre, los objetos con batería tienen menos restricciones. Si no corre prisa, es la opción más sencilla.' ),
				) ),
			array( 'tipo' => 'panels', 'k' => 'el_adu',
				'titulo' => 'Nuevo o usado: lo que mira la aduana',
				'panels' => array(
					array( 'Equipo nuevo', 'Se declara con su boleta y su valor real. La electrónica nueva de valor alto es de lo que más impuestos paga en destino: te decimos cuánto antes de despachar.' ),
					array( 'Equipo usado', 'Se declara como usado y de uso personal, con un valor coherente con su estado. Si va con su caja, mejor.' ),
				),
				'nota' => 'Algunos países piden registrar el celular (su número IMEI) para usarlo en sus redes. Que tu destinatario lo consulte con su operador antes de recibirlo. Más sobre impuestos en <a href="' . $u( '/aduanas-e-impuestos/' ) . '">aduanas e impuestos</a>.' ),
			array( 'tipo' => 'pasos', 'k' => 'el_pasos', 'fondo' => true,
				'titulo' => 'Cómo prepararlo antes de entregarlo',
				'pasos'  => array(
					array( 'Haz una copia de tus datos.', 'Fotos, contactos y archivos: el envío va asegurado, tus datos no.' ),
					array( 'Si es para otra persona, desvincula tu cuenta.', 'Cierra sesión en tu cuenta de Apple o Google y restablece el equipo; si no, quien lo recibe no podrá usarlo.' ),
					array( 'Apágalo del todo.', 'Apagado, no en reposo, y con los botones protegidos para que no se encienda dentro de la caja.' ),
					array( 'Caja original o una ajustada.', 'Con relleno por todos los lados. El cargador y los cables van aparte, en una bolsa.' ),
					array( 'Declara modelo y valor.', 'Marca, modelo, si es nuevo o usado, y el valor según la boleta.' ),
				),
				'nota' => 'Más detalles sobre cómo proteger lo frágil en <a href="' . $u( '/embalaje-para-envios-internacionales/' ) . '">embalaje para envíos internacionales</a>.' ),
		),
		'faqs' => array(
			array( '¿Se puede enviar un celular por avión?', 'Sí, con la batería instalada, el equipo apagado y bien protegido. Las baterías sueltas y los power banks tienen restricciones mayores, y las baterías dañadas no se envían.' ),
			array( '¿Paga impuestos una laptop enviada al extranjero?', 'Depende del país de destino y del valor declarado. La electrónica nueva de valor alto suele pagar; te decimos cuánto antes de despachar.' ),
			array( '¿Puedo enviar el celular con su cargador?', 'Sí. El cargador y los cables van en la misma caja, en una bolsa aparte para que no rayen el equipo.' ),
		),
		'relacionados' => array(
			'Te puede interesar' => array( 'que-se-puede-enviar', 'embalaje-para-envios-internacionales', 'seguro-de-envios' ),
			'Siguiente paso'     => array( 'cotizar' ),
		),
	),
	);
	$d = apply_filters( 'grenvios_pse_paginas', $d, $u );
	return isset( $d[ $slug ] ) ? $d[ $slug ] : null;
}

/* ══════════════════════════════════════
   2) REGISTRO DE PÁGINAS Y CREACIÓN
══════════════════════════════════════ */
add_filter( 'grenvios_pages', function ( $pages ) {
	$meta = array(
		'envio-de-correspondencia-internacional' => array(
			'Envío de correspondencia internacional',
			'Envío de Correspondencia Internacional desde Lima | Grenvíos',
			'Envío de correspondencia internacional desde Lima: cartas, tarjetas, invitaciones y fotos por vía aérea, con número de guía hasta la entrega.',
		),
		'envio-de-muestras-comerciales' => array(
			'Envío de muestras comerciales',
			'Envío de Muestras Comerciales al Extranjero | Grenvíos',
			'Envío de muestras comerciales desde Perú: factura proforma, permisos, embalaje y qué mira la aduana. Para exportadores y emprendedores.',
		),
		'envio-de-celulares-y-laptops' => array(
			'Envío de celulares y laptops',
			'Enviar Celulares y Laptops al Extranjero | Grenvíos',
			'Cómo enviar un celular o una laptop al extranjero desde Lima: baterías de litio, vía aérea o terrestre, impuestos y cómo prepararlo.',
		),
	);
	foreach ( grenvios_pse_slugs() as $slug ) {
		if ( isset( $meta[ $slug ] ) ) continue;
		$def = grenvios_pse_def( $slug );
		if ( $def && isset( $def['meta'] ) ) $meta[ $slug ] = $def['meta'];
	}
	foreach ( $meta as $slug => $m ) {
		$pages[ $slug ] = array( 'title' => $m[0], 'seo' => $m[1], 'desc' => $m[2], 'parent' => grenvios_pse_parent( $slug ) );
	}
	return $pages;
}, 20 );

function grenvios_pse_kw() {
	$extra = array();
	foreach ( grenvios_pse_slugs() as $s ) {
		$def = grenvios_pse_def( $s );
		if ( $def && isset( $def['kw'] ) ) $extra[ $s ] = $def['kw'];
	}
	return $extra + array(
		'envio-de-correspondencia-internacional' => 'envío de correspondencia internacional',
		'envio-de-muestras-comerciales'          => 'envío de muestras comerciales',
		'envio-de-celulares-y-laptops'           => 'enviar celular al extranjero',
	);
}
add_filter( 'grenvios_seo_kw_default', function ( $kw, $post_id ) {
	$m = grenvios_pse_kw();
	$s = (string) get_post_field( 'post_name', $post_id );
	return isset( $m[ $s ] ) ? $m[ $s ] : $kw;
}, 20, 2 );

/* Se crean una sola vez (opción con versión propia), como hijas de Servicios. */
function grenvios_pse_crear() {
	if ( ! function_exists( 'grenvios_ensure_page' ) ) return;
	$pages = grenvios_pages();
	$kw    = grenvios_pse_kw();
	foreach ( grenvios_pse_slugs() as $slug ) {
		if ( ! isset( $pages[ $slug ] ) ) continue;
		$padre = grenvios_pse_parent( $slug );
		$pp    = $padre !== '' ? get_page_by_path( $padre ) : null;
		if ( $padre !== '' && ! $pp ) continue;
		$ex = get_page_by_path( grenvios_pse_path( $slug ) );
		$id = $ex ? (int) $ex->ID : (int) grenvios_ensure_page( $slug, $pages[ $slug ]['title'], $pp ? (int) $pp->ID : 0 );
		if ( ! $id ) continue;
		if ( function_exists( 'pll_set_post_language' ) && function_exists( 'pll_default_language' ) && ! pll_get_post_language( $id ) ) {
			pll_set_post_language( $id, pll_default_language() );
		}
		if ( defined( 'GRENVIOS_KW_META' ) && get_post_meta( $id, GRENVIOS_KW_META, true ) === '' ) {
			update_post_meta( $id, GRENVIOS_KW_META, $kw[ $slug ] );
		}
	}
	update_option( 'grenvios_pse_v', GRENVIOS_PSE_V );
	delete_transient( 'grenvios_links_graph' );
	flush_rewrite_rules();
}
/* Sube este número al añadir páginas: así se crean las que falten (las que ya
 * existen no se tocan). */
if ( ! defined( 'GRENVIOS_PSE_V' ) ) define( 'GRENVIOS_PSE_V', 5 );
add_action( 'admin_init', function () {
	if ( (int) get_option( 'grenvios_pse_v' ) < GRENVIOS_PSE_V ) grenvios_pse_crear();
} );

/* ══════════════════════════════════════
   3) RENDER
══════════════════════════════════════ */
add_filter( 'grenvios_render_page_early', function ( $hecho, $slug ) {
	if ( $hecho || ! in_array( $slug, grenvios_pse_slugs(), true ) ) return $hecho;
	grenvios_pse_render( $slug );
	return true;
}, 12, 2 );

/* Lista con vistos editable: una línea por punto. */
function grenvios_pse_items( $key, $items ) {
	$v = trim( (string) grenvios_field( $key, '' ) );
	return $v !== '' ? array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $v ) ) ) ) : $items;
}

/* Antetítulo por defecto de cada tipo de bloque (editable en el panel). */
function grenvios_pse_sub_defecto( $b, $def ) {
	if ( isset( $b['sub'] ) ) return $b['sub'];
	$m = array( 'intro' => $def['label'], 'panels' => 'Lo esencial', 'lista' => 'Antes de enviar', 'pasos' => 'Paso a paso', 'destinos' => 'Plazos y rutas', 'definiciones' => 'Glosario' );
	return isset( $m[ $b['tipo'] ] ) ? $m[ $b['tipo'] ] : '';
}

/* Pinta la página con los patrones de la skill grenvios-ui (inc/ui-bloques.php):
 * intro dividida con imagen, tarjetas con icono, vistos en rejilla, pasos en
 * línea de tiempo, glosario en tarjetas y tabla con banderas, todo con
 * aparición al hacer scroll. Los textos siguen saliendo de los mismos campos. */
function grenvios_pse_render( $slug ) {
	$def = grenvios_pse_def( $slug );
	if ( ! $def ) return;
	grenvios_tool_banner( $def['hero'][0], $def['hero'][1] );

	foreach ( $def['bloques'] as $b ) {
		$k   = $b['k'];
		$sub = grenvios_tf( $k . '_sub', grenvios_pse_sub_defecto( $b, $def ) );
		$tit = grenvios_tf( $k . '_t', $b['titulo'] );

		if ( $b['tipo'] === 'intro' ) {
			$img = trim( (string) grenvios_field( $k . '_img', '' ) );
			if ( $img === '' ) $img = isset( $b['img'] ) ? grenvios_ui_img( $b['img'] ) : grenvios_ui_img_pagina( $slug );
			echo grenvios_ui_intro( $sub, esc_html( $tit ), grenvios_tf( $k . '_x', $b['texto'] ), $img, grenvios_field( 'tool_eyebrow', $def['hero'][0] ) );
			continue;
		}

		$txt = isset( $b['texto'] ) ? grenvios_tf( $k . '_x', $b['texto'] ) : '';
		/* `gr-bq--tipo` (doble guion): con `gr-bq-pasos` la sección heredaba la
		 * línea de tiempo de la lista y cruzaba el título. */
		echo '<section class="gr-bq gr-bq--' . esc_attr( $b['tipo'] ) . ( ! empty( $b['fondo'] ) ? ' bg-grey' : '' ) . ' padding"><div class="container">'
			. grenvios_ui_cabecera( $sub, esc_html( $tit ), $txt );

		if ( $b['tipo'] === 'panels' ) {
			$items = array();
			foreach ( $b['panels'] as $i => $p ) {
				$n = $i + 1;
				$items[] = array( grenvios_tf( $k . '_p' . $n . '_t', $p[0] ), grenvios_tf( $k . '_p' . $n . '_x', $p[1] ) );
			}
			echo grenvios_ui_tarjetas( $items );
		} elseif ( $b['tipo'] === 'lista' ) {
			echo grenvios_ui_checks( array_map( 'grenvios_t', grenvios_pse_items( $k . '_lista', $b['items'] ) ) );
		} elseif ( $b['tipo'] === 'destinos' ) {
			echo '<div class="wow fade-in-bottom" data-wow-delay="100ms">' . grenvios_ui_tabla_con_banderas( grenvios_pse_tabla_destinos( $b ) ) . '</div>';
		} elseif ( $b['tipo'] === 'definiciones' ) {
			echo grenvios_ui_glosario( grenvios_pse_items( $k . '_defs', array_map( function ( $t ) { return $t[0] . ': ' . $t[1]; }, $b['terminos'] ) ) );
		} elseif ( $b['tipo'] === 'pasos' ) {
			$items = array();
			foreach ( $b['pasos'] as $i => $p ) {
				$pk = $k . '_s' . ( $i + 1 );
				$items[] = array( grenvios_tf( $pk . '_t', $p[0] ), grenvios_tf( $pk . '_x', $p[1] ) );
			}
			echo grenvios_ui_pasos( $items );
		}
		if ( isset( $b['nota'] ) ) grenvios_pn_nota( $k . '_nota', $b['nota'] );
		echo '</div></section>';
	}
}

/* ══════════════════════════════════════
   4) PANEL «Editar página»: el registro sale de la misma definición
══════════════════════════════════════ */
add_filter( 'grenvios_text_registry', function ( $reg ) {
	foreach ( grenvios_pse_slugs() as $slug ) {
		$def = grenvios_pse_def( $slug );
		$sec = array(
			'hero' => array( 'label' => $def['label'] . ' · Banner', 'fields' => array(
				'tool_eyebrow' => array( 'Frase superior', 'text', $def['hero'][0] ),
				'tool_title'   => array( 'Título H1', 'text', $def['hero'][1] ),
			) ),
		);
		foreach ( $def['bloques'] as $b ) {
			$k = $b['k'];
			$f = array(
				$k . '_sub' => array( 'Antetítulo', 'text', grenvios_pse_sub_defecto( $b, $def ) ),
				$k . '_t'   => array( 'Título', 'text', $b['titulo'] ),
			);
			if ( $b['tipo'] === 'intro' ) {
				$f[ $k . '_img' ] = array( 'Imagen (vacía = la del diseño)', 'image', '' );
			}
			if ( isset( $b['texto'] ) ) $f[ $k . '_x' ] = array( 'Texto', 'html', $b['texto'] );
			if ( $b['tipo'] === 'panels' ) {
				foreach ( $b['panels'] as $i => $p ) {
					$n = $i + 1;
					$f[ $k . '_p' . $n . '_t' ] = array( 'Tarjeta ' . $n . ' · título', 'text', $p[0] );
					$f[ $k . '_p' . $n . '_x' ] = array( 'Tarjeta ' . $n . ' · texto', 'html', $p[1] );
				}
			} elseif ( $b['tipo'] === 'lista' ) {
				$f[ $k . '_lista' ] = array( 'Lista (una línea por punto)', 'textarea', implode( "\n", $b['items'] ) );
			} elseif ( $b['tipo'] === 'definiciones' ) {
				$f[ $k . '_defs' ] = array( 'Términos (uno por línea: «Término: definición»)', 'textarea', implode( "
", array_map( function ( $t ) { return $t[0] . ': ' . $t[1]; }, $b['terminos'] ) ) );
			} elseif ( $b['tipo'] === 'pasos' ) {
				foreach ( $b['pasos'] as $i => $p ) {
					$n = $i + 1;
					$f[ $k . '_s' . $n . '_t' ] = array( 'Paso ' . $n . ' · título', 'text', $p[0] );
					$f[ $k . '_s' . $n . '_x' ] = array( 'Paso ' . $n . ' · texto', 'textarea', $p[1] );
				}
			}
			if ( isset( $b['nota'] ) ) $f[ $k . '_nota' ] = array( 'Nota', 'html', $b['nota'] );
			$sec[ $k ] = array( 'label' => $def['label'] . ' · ' . $b['titulo'], '_no_token_check' => true, 'fields' => $f );
		}
		if ( ! isset( $reg[ $slug ] ) ) $reg[ $slug ] = array( 'label' => $def['label'], 'priority' => 63, 'sections' => array() );
		$reg[ $slug ]['sections'] = array_merge( $sec, (array) $reg[ $slug ]['sections'] );
	}
	return $reg;
}, 9 );

/* ══════════════════════════════════════
   5) ENTRADILLA, FAQ, ENLAZADO Y AUDITORÍA
══════════════════════════════════════ */
add_filter( 'grenvios_ent_textos', function ( $t ) {
	$t['envio-de-correspondencia-internacional'] = 'Hacemos el <strong>envío de correspondencia internacional</strong> desde Lima: cartas, tarjetas, invitaciones y fotografías por vía aérea, con número de guía hasta que llegan a su destinatario. <a href="%H%/cotizar/">Cotiza tu envío</a>.';
	$t['envio-de-muestras-comerciales']          = 'El <strong>envío de muestras comerciales</strong> abre la puerta a tu primer pedido fuera del país. Aquí explicamos qué mira la aduana, qué documentos llevar y cómo presentarla. <a href="%H%/cotizar/">Cotiza el envío de tu muestra</a>.';
	$t['envio-de-celulares-y-laptops']           = 'Para <strong>enviar un celular o una laptop al extranjero</strong> lo que manda es la batería de litio: decide la vía y cómo se prepara el equipo. Te lo explicamos paso a paso y te decimos qué admite tu destino al <a href="%H%/cotizar/">cotizar</a>.';
	foreach ( grenvios_pse_slugs() as $s ) {
		$def = grenvios_pse_def( $s );
		if ( $def && isset( $def['ent'] ) ) $t[ $s ] = $def['ent'];
	}
	return $t;
} );

add_filter( 'grenvios_page_faqs', function ( $faqs, $slug ) {
	if ( function_exists( 'grenvios_hq_pais' ) && grenvios_hq_pais() !== '' ) return $faqs;
	$def = in_array( $slug, grenvios_pse_slugs(), true ) ? grenvios_pse_def( $slug ) : null;
	return $def ? array_merge( (array) $faqs, $def['faqs'] ) : $faqs;
}, 30, 2 );

add_filter( 'grenvios_related_map', function ( $mapa, $slug ) {
	foreach ( grenvios_pse_slugs() as $s ) $mapa[ $s ] = grenvios_pse_def( $s )['relacionados'];

	/* Enlaces entrantes desde las páginas que ya tienen autoridad. */
	$entrantes = array(
		'servicios'                         => array( 'Más servicios'      => array( 'servicios/envio-de-correspondencia-internacional', 'servicios/envio-de-muestras-comerciales', 'servicios/envio-de-celulares-y-laptops' ) ),
		'envio-internacional-de-documentos' => array( 'Te puede interesar' => array( 'servicios/envio-de-correspondencia-internacional' ) ),
		'envios-para-empresas'              => array( 'Te puede interesar' => array( 'servicios/envio-de-muestras-comerciales' ) ),
		'que-se-puede-enviar'               => array( 'Te puede interesar' => array( 'servicios/envio-de-celulares-y-laptops' ) ),
		'encomiendas-internacionales'       => array( 'Te puede interesar' => array( 'servicios/envio-de-celulares-y-laptops' ) ),
	);
	foreach ( $entrantes as $origen => $bloques ) {
		if ( ! isset( $mapa[ $origen ] ) ) continue;
		foreach ( $bloques as $titulo => $destinos ) {
			$actual = isset( $mapa[ $origen ][ $titulo ] ) ? $mapa[ $origen ][ $titulo ] : array();
			$mapa[ $origen ][ $titulo ] = array_values( array_unique( array_merge( $actual, $destinos ) ) );
		}
	}
	return $mapa;
}, 21, 2 );

/* La auditoría «SEO por página» mide el texto que se pinta, no solo el título. */
add_filter( 'grenvios_seo_page_text', function ( $parts, $post_id, $slug ) {
	$slug = (string) get_post_field( 'post_name', $post_id );
	if ( ! in_array( $slug, grenvios_pse_slugs(), true ) ) return $parts;
	try {
		ob_start();
		grenvios_pse_render( $slug );
		$parts[] = ob_get_clean();
		foreach ( grenvios_pse_def( $slug )['faqs'] as $q ) $parts[] = $q[0] . ' ' . $q[1];
	} catch ( Throwable $e ) {
		if ( ob_get_level() ) ob_end_clean();
	}
	return $parts;
}, 20, 3 );


/* ══════════════════════════════════════
   Bloque «destinos»: tabla alimentada por el gestor de destinos
   Opciones: 'via' => 'aereo' | 'terrestre' (filtra rutas), 'paises' => [slugs],
   'orden' => 'plazo' (de más rápido a más lento). Nunca inventa datos: vía,
   plazo, entrega e impuesto terrestre son los de cada ficha.
══════════════════════════════════════ */
function grenvios_pse_tabla_destinos( $b ) {
	if ( ! function_exists( 'grenvios_destinos' ) || ! function_exists( 'grenvios_dsec_datos' ) ) return '';
	$via   = isset( $b['via'] ) ? $b['via'] : '';
	$solo  = isset( $b['paises'] ) ? (array) $b['paises'] : array();
	$base  = function_exists( 'grenvios_url_base' ) ? grenvios_url_base() : home_url();
	$filas = array();
	foreach ( grenvios_destinos() as $slug => $d0 ) {
		if ( $solo && ! in_array( $slug, $solo, true ) ) continue;
		$d = grenvios_dsec_datos( $slug, $d0 );
		if ( $via === 'terrestre' && empty( $d['terr'] ) ) continue;
		if ( $via === 'aereo' && empty( $d['aereo'] ) ) continue;
		preg_match( '/\d+/', (string) $d['tiempo'], $m );
		$filas[] = array( 'slug' => $slug, 'd' => $d, 'min' => $m ? (int) $m[0] : 999 );
	}
	/* 'extra' => [ 'Canadá', … ]: países sin ficha propia del hub /destinos/
	 * (grenvios_destinos_extra()). Solo se conoce su plazo: la entrega se
	 * confirma al cotizar y no llevan impuesto terrestre (van por avión). */
	$extra = array();
	if ( ! empty( $b['extra'] ) && function_exists( 'grenvios_destinos_extra' ) ) {
		$todos = grenvios_destinos_extra();
		foreach ( (array) $b['extra'] as $pais ) {
			if ( ! isset( $todos[ $pais ] ) ) continue;
			preg_match( '/\d+/', $todos[ $pais ], $m );
			$extra[] = array( 'slug' => '', 'd' => array( 'title' => $pais, 'modos' => 'Aéreo', 'tiempo' => $todos[ $pais ], 'casa' => null ), 'min' => $m ? (int) $m[0] : 999 );
		}
	}
	$filas = array_merge( $filas, $extra );
	if ( ! $filas ) return '';
	if ( isset( $b['orden'] ) && $b['orden'] === 'plazo' ) usort( $filas, function ( $x, $y ) { return $x['min'] <=> $y['min']; } );

	$imp  = ( $via === 'terrestre' );
	$html = '<div class="gr-table-wrap"><table class="gr-table"><thead><tr><th scope="col">Destino</th>'
		. ( $via === '' ? '<th scope="col">Vía</th>' : '' )
		. '<th scope="col">Plazo' . ( $via === 'terrestre' ? ' por tierra' : '' ) . '</th><th scope="col">Entrega</th>'
		. ( $imp ? '<th scope="col">Impuesto aproximado</th>' : '' ) . '</tr></thead><tbody>';
	foreach ( $filas as $f ) {
		$d = $f['d'];
		$pais = $f['slug'] !== ''
			? '<a href="' . esc_url( $base . '/destinos/' . $f['slug'] . '/' ) . '">' . esc_html( $d['title'] ) . '</a>'
			: esc_html( $d['title'] ) . ' <span class="gr-tabla-nota">bajo pedido</span>';
		$html .= '<tr><th scope="row">' . $pais . '</th>'
			. ( $via === '' ? '<td>' . esc_html( $d['modos'] ) . '</td>' : '' )
			. '<td>' . esc_html( $d['tiempo'] ) . '</td>'
			. '<td>' . esc_html( $d['casa'] === null ? 'Te la confirmamos al cotizar' : ( ! empty( $d['casa'] ) ? 'A domicilio' : 'Retiro en agencia local' ) ) . '</td>'
			. ( $imp ? '<td>' . ( ! empty( $d['impuesto'] ) ? '≈ ' . esc_html( str_replace( '.', ',', (string) $d['impuesto'] ) ) . ' % · se paga en {{origen_ciudad}}' : 'Te lo confirmamos al cotizar' ) . '</td>' : '' )
			. '</tr>';
	}
	$html .= '</tbody></table></div>';
	// La tabla no pasa por grenvios_tf(): sin esto «{{origen_ciudad}}» salía literal.
	return function_exists( 'grenvios_sede_tokens_apply' ) ? grenvios_sede_tokens_apply( $html ) : $html;
}
