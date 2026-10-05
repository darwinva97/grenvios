<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Tres páginas más con el motor de inc/paginas-servicios-extra.php (2026-09-28)
 * ══════════════════════════════════════════════════════════════════════════
 *
 *   /glosario-de-envios-internacionales/          «glosario de envíos internacionales»
 *        Recurso de referencia: responde las búsquedas de definición («qué es
 *        el peso volumétrico», «qué es DDP») y es la página del sitio que más
 *        fácil se enlaza desde fuera. Lleva schema DefinedTermSet.
 *   /servicios/envio-de-regalos-al-extranjero/    «enviar regalos al extranjero»
 *        Búsqueda comercial y estacional (cumpleaños, Día de la Madre, Navidad).
 *   /servicios/envio-de-artesanias-al-extranjero/ «enviar artesanías peruanas al extranjero»
 *        Muy propia de Perú: la buscan quien regala y quien vende fuera.
 *
 * Criterio (skill grenvios-diseno): definiciones generales del sector y datos
 * que el sitio ya afirma; nada de cifras de impuestos, límites ni plazos.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_filter( 'grenvios_pse_slugs', function ( $s ) {
	return array_merge( $s, array( 'glosario-de-envios-internacionales', 'envio-de-regalos-al-extranjero', 'envio-de-artesanias-al-extranjero' ) );
} );

add_filter( 'grenvios_pse_paginas', function ( $d, $u ) {

	/* ── Glosario ────────────────────────────────────────────────────── */
	$d['glosario-de-envios-internacionales'] = array(
		'parent' => '',
		'label'  => 'Glosario',
		'kw'     => 'glosario de envíos internacionales',
		'meta'   => array(
			'Glosario de envíos internacionales',
			'Glosario de Envíos Internacionales | Grenvíos',
			'Qué significan peso volumétrico, valor declarado, desaduanaje, DAP, DDP y otros términos de los envíos internacionales, explicados en claro.',
		),
		'ent'    => 'Este <strong>glosario de envíos internacionales</strong> explica, sin tecnicismos, las palabras que vas a encontrar al cotizar, despachar y rastrear un envío: del peso volumétrico al desaduanaje. Si tienes una duda concreta, <a href="%H%/cotizar/">escríbenos</a>.',
		'hero'   => array( 'Términos claros', 'Glosario de envíos internacionales' ),
		'bloques' => array(
			array( 'tipo' => 'intro', 'k' => 'gl_intro',
				'titulo' => 'Las palabras que vas a oír al enviar un paquete',
				'texto'  => 'Cotizar, despachar y recibir un envío internacional trae su propio vocabulario, y buena parte de las dudas —y de los malentendidos sobre el precio— nacen de no saber qué significa una palabra. Aquí están las más habituales, agrupadas según el momento del envío en que aparecen.' ),
			array( 'tipo' => 'definiciones', 'k' => 'gl_antes', 'fondo' => true,
				'titulo'   => 'Antes de enviar: precio y preparación',
				'terminos' => array(
					array( 'Peso real', 'Lo que marca la balanza con el paquete ya embalado.' ),
					array( 'Peso volumétrico', 'El peso que se atribuye a un paquete por el espacio que ocupa: largo × ancho × alto en centímetros, dividido entre 5000.' ),
					array( 'Peso cobrable', 'El mayor entre el peso real y el volumétrico. Es sobre el que se calcula el precio del envío.' ),
					array( 'Valor declarado', 'Lo que vale el contenido del envío. Es la base de los impuestos en destino y de la cobertura del seguro.' ),
					array( 'Declaración de contenido', 'La lista de lo que va dentro del paquete, con cantidades y valores. Es lo primero que revisa la aduana.' ),
					array( 'Factura comercial', 'El documento que acompaña a una venta internacional: vendedor, comprador, productos, cantidades y valor real.' ),
					array( 'Factura proforma', 'Una factura previa, sin valor fiscal, que se usa para declarar muestras o regalos cuando no hay venta.' ),
					array( 'Lista de bultos (packing list)', 'El detalle de qué va en cada caja de un envío con varios bultos, con su peso y sus medidas.' ),
				) ),
			array( 'tipo' => 'definiciones', 'k' => 'gl_trans',
				'titulo'   => 'En camino: transporte y seguimiento',
				'terminos' => array(
					array( 'Courier', 'Empresa de mensajería que recoge, transporta y entrega el envío puerta a puerta o en agencia, con seguimiento propio.' ),
					array( 'Número de guía', 'El código único de tu envío. Con él se consulta el estado en el rastreo y se hace cualquier gestión.' ),
					array( 'Guía aérea (AWB)', 'El documento de transporte de un envío que viaja por avión. Su número identifica la carga ante la aerolínea.' ),
					array( 'Despacho', 'El momento en que el envío sale formalmente del país de origen. Desde ahí se cuentan los plazos.' ),
					array( 'Consolidación', 'Juntar varios paquetes en un solo envío para pagar un único flete.' ),
					array( 'Tránsito', 'El tramo entre el despacho y la llegada al país de destino.' ),
					array( 'Última milla', 'El tramo final, desde la agencia o el centro de distribución del destino hasta quien recibe.' ),
					array( 'Días hábiles', 'Días laborables: no cuentan los fines de semana ni los feriados del país de origen o de destino.' ),
				) ),
			array( 'tipo' => 'definiciones', 'k' => 'gl_aduana', 'fondo' => true,
				'titulo'   => 'En la aduana',
				'terminos' => array(
					array( 'Aduana', 'La autoridad de cada país que controla lo que entra y sale, y cobra los impuestos de importación.' ),
					array( 'Arancel', 'El impuesto que un país cobra por importar un producto. Depende del tipo de producto y de su valor.' ),
					array( 'Desaduanaje', 'El trámite por el que la aduana revisa y libera el envío para que pueda entregarse. También se llama nacionalización.' ),
					array( 'Retención', 'Cuando la aduana detiene un envío porque necesita un dato, un documento o el pago de un impuesto.' ),
					array( 'Mercancía restringida', 'La que solo puede enviarse cumpliendo condiciones: un permiso, una receta, una vía concreta.' ),
					array( 'Mercancía prohibida', 'La que un país no deja entrar en ningún caso.' ),
					array( 'Mercancía peligrosa', 'Productos que por su composición tienen reglas especiales en el transporte aéreo: baterías de litio, aerosoles, inflamables.' ),
					array( 'Muestra sin valor comercial', 'Pocas unidades de un producto enviadas para evaluarlo, no para venderlas en destino.' ),
				) ),
			array( 'tipo' => 'definiciones', 'k' => 'gl_docs',
				'titulo'   => 'Documentos y condiciones de entrega',
				'terminos' => array(
					array( 'Apostilla', 'El sello que da validez a un documento público en los países del Convenio de La Haya.' ),
					array( 'Legalización', 'La cadena de firmas que da validez a un documento en un país que no acepta la apostilla.' ),
					array( 'Incoterms', 'Reglas internacionales que fijan qué paga y de qué responde cada parte en una compraventa internacional.' ),
					array( 'DAP (entregado en lugar)', 'El envío llega a destino con el transporte pagado, pero los impuestos de importación los paga quien recibe.' ),
					array( 'DDP (entregado con derechos pagados)', 'El envío llega a destino con el transporte y los impuestos ya pagados por quien envía.' ),
				),
				'nota' => 'Para ver cómo encajan todos estos términos en un envío real, lee <a href="' . $u( '/como-enviar-un-paquete-al-extranjero/' ) . '">cómo enviar un paquete al extranjero</a>, o calcula tu <a href="' . $u( '/servicios/peso-volumetrico/' ) . '">peso volumétrico</a>.' ),
		),
		'faqs' => array(
			array( '¿Qué es el peso volumétrico?', 'El peso que se atribuye a un paquete por su tamaño: largo × ancho × alto en centímetros, dividido entre 5000. Se cobra el mayor entre este y el peso real.' ),
			array( '¿Qué diferencia hay entre DAP y DDP?', 'En DAP, quien recibe paga los impuestos de importación al llegar el envío. En DDP, los paga quien envía y el destinatario recibe sin pagar nada más.' ),
			array( '¿Qué significa que un envío esté en desaduanaje?', 'Que la aduana del país de destino lo está revisando para liberarlo. Es un paso normal; si necesita algo, te avisamos.' ),
		),
		'relacionados' => array(
			'Te puede interesar' => array( 'como-enviar-un-paquete-al-extranjero', 'servicios/peso-volumetrico', 'aduanas-e-impuestos' ),
			'Siguiente paso'     => array( 'cotizar' ),
		),
	);

	/* ── Regalos ─────────────────────────────────────────────────────── */
	$d['envio-de-regalos-al-extranjero'] = array(
		'label' => 'Regalos',
		'kw'    => 'enviar regalos al extranjero',
		'meta'  => array(
			'Envío de regalos al extranjero',
			'Enviar Regalos al Extranjero desde Perú | Grenvíos',
			'Cómo enviar regalos al extranjero desde Lima: qué viaja bien, qué evitar, cómo declararlo y con cuánto margen enviarlo para que llegue a la fecha.',
		),
		'ent'   => 'Para <strong>enviar regalos al extranjero</strong> desde Lima hay tres cosas que deciden si llegan a tiempo y enteros: qué eliges, cómo lo embalas y con cuánto margen lo despachas. Aquí te lo explicamos, y al <a href="%H%/cotizar/">cotizar</a> te decimos el plazo de tu destino.',
		'hero'  => array( 'Cumpleaños, fiestas y fechas especiales', 'Envío de regalos al extranjero desde {{origen_ciudad}}' ),
		'bloques' => array(
			array( 'tipo' => 'intro', 'k' => 'rg_intro',
				'titulo' => 'Un regalo que llega a tiempo vale el doble',
				'texto'  => 'Un regalo que llega después de la fecha pierde la mitad de su sentido. La mayoría de los retrasos no vienen del transporte sino de dos decisiones tomadas en casa: elegir algo que la aduana revisa más de la cuenta y despachar demasiado tarde. Las dos tienen solución.' ),
			array( 'tipo' => 'panels', 'k' => 'rg_ideas', 'fondo' => true,
				'titulo' => 'Regalos que viajan bien',
				'panels' => array(
					array( 'Productos peruanos envasados', 'Café, chocolate, panetón y dulces sellados de fábrica y con etiqueta. Son de lo más pedido y, bien declarados, pasan sin problemas en la mayoría de destinos.' ),
					array( 'Textiles', 'Chompas, chalinas y mantas de alpaca o algodón. Pesan poco y, comprimidas, ocupan poco: el regalo más fácil de enviar.' ),
					array( 'Artesanía', 'Cerámica, retablos, tallas o joyería de plata. Necesitan un embalaje cuidadoso; te contamos cómo en <a href="' . $u( '/servicios/envio-de-artesanias-al-extranjero/' ) . '">envío de artesanías</a>.' ),
					array( 'Libros, fotos y cartas', 'Livianos y sin restricciones. Una carta o unas fotos impresas dentro del paquete convierten el envío en otra cosa.' ),
				) ),
			array( 'tipo' => 'lista', 'k' => 'rg_evitar',
				'titulo' => 'Lo que conviene evitar o consultar antes',
				'items'  => array(
					'Comida casera, fruta fresca, lácteos y embutidos: casi ninguna aduana los deja pasar.',
					'Perfumes y aerosoles si el envío va por avión.',
					'Aparatos con batería suelta o power banks.',
					'Dinero en efectivo o tarjetas de regalo sin su envoltorio original.',
					'Muchas unidades iguales: diez regalos idénticos se leen como mercancía para vender.',
				),
				'nota' => 'La lista completa por destino está en <a href="' . $u( '/que-se-puede-enviar/' ) . '">qué se puede enviar</a>.' ),
			array( 'tipo' => 'pasos', 'k' => 'rg_pasos', 'fondo' => true,
				'titulo' => 'Cómo enviarlo para que llegue a la fecha',
				'pasos'  => array(
					array( 'Cuenta hacia atrás.', 'Toma la fecha del regalo, resta el plazo de tu destino en días hábiles y añade unos días de margen para la aduana. Los plazos por país están en tiempos de entrega.' ),
					array( 'Adelántate en campañas.', 'En Navidad y en el Día de la Madre el volumen se multiplica y la aduana revisa más. Despacha antes de lo habitual.' ),
					array( 'Declara que es un regalo, con su valor real.', 'Decir «regalo» ayuda a leer el envío como personal, pero no exime de impuestos: cada país decide a partir de qué valor cobra.' ),
					array( 'Quita el precio, no la boleta.', 'Retira la etiqueta de precio del producto, pero guarda la boleta: la necesitamos para declarar el valor.' ),
					array( 'Añade una nota.', 'Una tarjeta dentro del paquete, con el nombre de quien envía, es lo primero que se ve al abrirlo.' ),
				) ),
		),
		'faqs' => array(
			array( '¿Los regalos pagan impuestos en el extranjero?', 'Depende del país y del valor declarado. Declarar un envío como regalo ayuda a que se trate como personal, pero no lo exime: cada aduana fija su propio umbral. Te lo confirmamos al cotizar.' ),
			array( '¿Con cuánta anticipación debo enviar un regalo?', 'Suma el plazo de tu destino en días hábiles y añade margen para la aduana; en campañas como Navidad, algo más. Los plazos por país están en la página de tiempos de entrega.' ),
			array( '¿Puedo enviar varios regalos a distintas personas en una caja?', 'Si viven en la misma dirección, sí. Si no, cada destinatario necesita su propio envío.' ),
		),
		'relacionados' => array(
			'Te puede interesar' => array( 'servicios/encomiendas-internacionales', 'servicios/envio-de-artesanias-al-extranjero', 'tiempos-de-entrega' ),
			'Siguiente paso'     => array( 'cotizar', 'recojo-a-domicilio-lima' ),
		),
	);

	/* ── Artesanías ──────────────────────────────────────────────────── */
	$d['envio-de-artesanias-al-extranjero'] = array(
		'label' => 'Artesanías',
		'kw'    => 'enviar artesanías peruanas al extranjero',
		'meta'  => array(
			'Envío de artesanías al extranjero',
			'Enviar Artesanías Peruanas al Extranjero | Grenvíos',
			'Cómo enviar artesanías peruanas al extranjero: embalaje por material, réplicas y piezas antiguas, permisos y cómo declararlas si las vendes fuera.',
		),
		'ent'   => 'Para <strong>enviar artesanías peruanas al extranjero</strong> —como regalo o para venderlas fuera— lo que decide es el material: cada uno se embala distinto y algunos piden un papel más. Aquí está todo, y al <a href="%H%/cotizar/">cotizar</a> te confirmamos lo que pide tu destino.',
		'hero'  => array( 'Hecho en Perú', 'Envío de artesanías peruanas al extranjero' ),
		'bloques' => array(
			array( 'tipo' => 'intro', 'k' => 'ar_intro',
				'titulo' => 'Piezas únicas que tienen que llegar enteras',
				'texto'  => 'Un retablo, una cerámica de Chulucanas o una chompa tejida a mano no se reponen con otra igual si llegan rotos. Por eso las artesanías piden más cuidado que cualquier otro paquete: el embalaje se decide por el material, y algunas piezas necesitan además un documento antes de salir del país.' ),
			array( 'tipo' => 'panels', 'k' => 'ar_mat', 'fondo' => true,
				'titulo' => 'Cómo embalar según el material',
				'panels' => array(
					array( 'Textiles y alpaca', 'Doblados y en bolsa sellada, que los protege de la humedad. Comprimidos ocupan mucho menos y bajan el peso volumétrico.' ),
					array( 'Cerámica y piedra', 'Cada pieza envuelta por separado y en doble caja, con relleno entre las dos. Nada debe tocar las paredes ni otra pieza.' ),
					array( 'Madera y retablos', 'Protege las esquinas y las partes salientes. Algunos países revisan la madera natural sin tratar por riesgo de plagas: si no está barnizada ni pintada, consúltanos antes.' ),
					array( 'Plata y joyería', 'En estuche pequeño, dentro de una caja con relleno. Declara el valor real: es la base del seguro y lo que más revisa la aduana en piezas de metal.' ),
				),
				'nota' => 'Más detalles en <a href="' . $u( '/embalaje-para-envios-internacionales/' ) . '">embalaje para envíos internacionales</a>.' ),
			array( 'tipo' => 'lista', 'k' => 'ar_ojo',
				'titulo' => 'Piezas que necesitan un paso más',
				'items'  => array(
					'Réplicas de piezas arqueológicas o coloniales: pueden requerir una constancia del Ministerio de Cultura de que no son patrimonio. Pídela en la tienda o consúltanos.',
					'Piezas antiguas originales: el patrimonio cultural peruano no puede salir del país.',
					'Artesanía con plumas, cuero de animales silvestres, semillas o conchas: puede necesitar permisos de flora y fauna.',
					'Madera natural sin tratar: algunos destinos exigen que esté tratada.',
				),
				'nota' => 'Si dudas con una pieza, mándanos una foto antes de comprarla o embalarla.' ),
			array( 'tipo' => 'pasos', 'k' => 'ar_venta', 'fondo' => true,
				'titulo' => 'Si vendes artesanía fuera del país',
				'pasos'  => array(
					array( 'Empieza por una muestra.', 'Pocas piezas, con factura proforma y valor real, para que tu cliente las vea antes del pedido.' ),
					array( 'Describe cada pieza con precisión.', 'Material, técnica y cantidad: «4 platos de cerámica pintada a mano», no «artesanía».' ),
					array( 'Haz factura comercial en el pedido.', 'Con el valor real y la misma descripción que usarás siempre, para que cada envío pase igual que el anterior.' ),
					array( 'Calcula el flete en tu precio.', 'Con el peso y las medidas de la caja ya embalada: la artesanía frágil abulta, y el volumen se paga.' ),
				),
				'nota' => 'Si envías con frecuencia, mira <a href="' . $u( '/envios-para-empresas/' ) . '">envíos para empresas</a> y <a href="' . $u( '/servicios/envio-de-muestras-comerciales/' ) . '">envío de muestras comerciales</a>.' ),
		),
		'faqs' => array(
			array( '¿Se pueden enviar artesanías peruanas al extranjero?', 'Sí. Lo que cambia según el material es el embalaje y, en algunos casos —réplicas arqueológicas, piezas con plumas o cuero de fauna silvestre, madera sin tratar—, un documento o permiso adicional.' ),
			array( '¿Necesito un permiso para enviar réplicas de cerámica precolombina?', 'Puede hacer falta una constancia del Ministerio de Cultura de que la pieza no es patrimonio. Pídela en la tienda donde la compras o consúltanos antes de enviarla.' ),
			array( '¿Cómo evito que la cerámica llegue rota?', 'Cada pieza envuelta por separado, en doble caja y con relleno entre las dos, sin que nada toque las paredes.' ),
		),
		'relacionados' => array(
			'Te puede interesar' => array( 'servicios/envio-de-regalos-al-extranjero', 'embalaje-para-envios-internacionales', 'servicios/envio-de-muestras-comerciales' ),
			'Siguiente paso'     => array( 'cotizar' ),
		),
	);

	return $d;
}, 10, 2 );

/* Enlaces entrantes desde páginas con autoridad. */
add_filter( 'grenvios_related_map', function ( $mapa, $slug ) {
	$entrantes = array(
		'servicios'                            => array( 'Más servicios'      => array( 'servicios/envio-de-regalos-al-extranjero', 'servicios/envio-de-artesanias-al-extranjero' ) ),
		'encomiendas-internacionales'          => array( 'Te puede interesar' => array( 'servicios/envio-de-regalos-al-extranjero' ) ),
		'envio-de-alimentos'                   => array( 'Te puede interesar' => array( 'servicios/envio-de-regalos-al-extranjero' ) ),
		'envio-de-muestras-comerciales'        => array( 'Te puede interesar' => array( 'servicios/envio-de-artesanias-al-extranjero' ) ),
		'como-enviar-un-paquete-al-extranjero' => array( 'Lo que necesitas'   => array( 'glosario-de-envios-internacionales' ) ),
		'preguntas-frecuentes'                 => array( 'Te puede interesar' => array( 'glosario-de-envios-internacionales' ) ),
		'aduanas-e-impuestos'                  => array( 'Te puede interesar' => array( 'glosario-de-envios-internacionales' ) ),
	);
	foreach ( $entrantes as $origen => $bloques ) {
		if ( ! isset( $mapa[ $origen ] ) ) continue;
		foreach ( $bloques as $titulo => $destinos ) {
			$actual = isset( $mapa[ $origen ][ $titulo ] ) ? $mapa[ $origen ][ $titulo ] : array();
			$mapa[ $origen ][ $titulo ] = array_values( array_unique( array_merge( $actual, $destinos ) ) );
		}
	}
	return $mapa;
}, 22, 2 );

/* Schema del glosario: DefinedTermSet con cada término, para que Google pueda
 * mostrar la definición directamente. Sale de los mismos datos editables. */
add_action( 'wp_head', function () {
	if ( ! is_page() || is_admin() ) return;
	$slug = function_exists( 'grenvios_current_slug' ) ? grenvios_current_slug() : '';
	if ( $slug !== 'glosario-de-envios-internacionales' ) return;
	$def = grenvios_pse_def( $slug );
	if ( ! $def ) return;
	$url  = get_permalink();
	$terms = array();
	foreach ( $def['bloques'] as $b ) {
		if ( $b['tipo'] !== 'definiciones' ) continue;
		$defecto = array_map( function ( $t ) { return $t[0] . ': ' . $t[1]; }, $b['terminos'] );
		foreach ( grenvios_pse_items( $b['k'] . '_defs', $defecto ) as $linea ) {
			$par = array_map( 'trim', explode( ':', $linea, 2 ) );
			if ( ! isset( $par[1] ) ) continue;
			$terms[] = array( '@type' => 'DefinedTerm', 'name' => $par[0], 'description' => $par[1], 'url' => $url . '#' . sanitize_title( $par[0] ) );
		}
	}
	if ( ! $terms ) return;
	echo '<script type="application/ld+json">' . wp_json_encode( array(
		'@context' => 'https://schema.org', '@type' => 'DefinedTermSet',
		'name' => 'Glosario de envíos internacionales', 'url' => $url, 'hasDefinedTerm' => $terms,
	), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}, 30 );

/* Tema del bloque «Del blog» de cada página nueva (inc/blog-en-paginas.php):
 * sin esto salían «las más recientes», que no tenían que ver con la página. */
add_filter( 'grenvios_bep_temas', function ( $t ) {
	return array_merge( $t, array(
		'encomiendas-internacionales'            => array( 'encomiendas-familiares', 'envio-de-paquetes' ),
		'envio-de-medicinas-al-extranjero'       => array( 'productos-prohibidos', 'aduanas' ),
		'cuanto-cuesta-enviar-un-paquete-al-extranjero' => array( 'precios-de-envio', 'peso-volumetrico' ),
		'embalaje-para-envios-internacionales'   => array( 'embalaje', 'peso-volumetrico' ),
		'envio-de-correspondencia-internacional' => array( 'apostilla-y-documentos' ),
		'envio-de-muestras-comerciales'          => array( 'envios-para-empresas', 'aduanas' ),
		'envio-de-celulares-y-laptops'           => array( 'productos-prohibidos', 'embalaje' ),
		'envio-de-regalos-al-extranjero'         => array( 'encomiendas-familiares', 'fechas-clave' ),
		'envio-de-artesanias-al-extranjero'      => array( 'embalaje', 'envios-para-empresas' ),
		'glosario-de-envios-internacionales'     => array( 'envio-de-paquetes', 'aduanas', 'peso-volumetrico' ),
	) );
} );
