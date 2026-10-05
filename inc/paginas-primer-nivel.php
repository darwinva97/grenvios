<?php
/**
 * ════════════════════════════════════════════════════════════════════════
 *  Cinco páginas nuevas en PRIMER NIVEL (dominio/slug) — 2026-10-02
 * ════════════════════════════════════════════════════════════════════════
 *
 * Regla del cliente: las páginas nuevas cuelgan de la raíz, no de /servicios/.
 * Cada una responde una búsqueda que ninguna otra página del sitio atacaba
 * (comprobado contra el mapa de keywords: 269 keywords, ninguna repetida):
 *
 *   /courier-internacional-en-lima/       «courier internacional en Lima»
 *        La palabra con la que más se busca el servicio en Perú. La portada va
 *        por «envíos internacionales desde Perú» y las portadas de ruta por
 *        «courier de Perú a <país>»: esta es la genérica de la ciudad.
 *   /envio-de-ropa-al-extranjero/         «enviar ropa al extranjero»
 *        Lo que más se manda a Venezuela, Chile y Argentina. Intención
 *        comercial; la guía del blog («cómo enviar ropa sin pagar de más») es
 *        la informativa y se enlazan entre sí.
 *   /envio-de-repuestos-al-extranjero/    «enviar repuestos al extranjero»
 *        Repuestos de auto y de maquinaria: entre lo más enviado a Ecuador.
 *   /envio-de-libros-al-extranjero/       «enviar libros al extranjero»
 *   /mudanzas-internacionales/            «mudanza internacional desde Lima»
 *        Mudanzas pequeñas (cajas, enseres): más que equipaje, menos que un
 *        contenedor. Se apoya en la vía terrestre y en la carga consolidada.
 *
 * Criterio (skills grenvios-diseno y grenvios-landing): solo datos que el sitio
 * ya afirma —peso volumétrico ÷ 5000, vías por país, reglas de baterías y de
 * prendas de marca, recojo en Lima, impuesto terrestre pagado en Lima—. Nada
 * de precios, plazos ni límites inventados.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_filter( 'grenvios_pse_slugs', function ( $s ) {
	return array_merge( $s, array( 'courier-internacional-en-lima', 'envio-de-ropa-al-extranjero', 'envio-de-repuestos-al-extranjero', 'envio-de-libros-al-extranjero', 'mudanzas-internacionales' ) );
} );

add_filter( 'grenvios_ui_img_pagina_mapa', function ( $m ) {
	return $m + array(
		'courier-internacional-en-lima'    => 'repartidor',
		'envio-de-ropa-al-extranjero'      => 'ejemplo/embalaje.webp',
		'envio-de-repuestos-al-extranjero' => 'ejemplo/almacen.webp',
		'envio-de-libros-al-extranjero'    => 'ejemplo/caja.webp',
		'mudanzas-internacionales'         => 'ejemplo/equipaje.webp',
	);
} );

add_filter( 'grenvios_pse_paginas', function ( $d, $u ) {

	/* ── Courier internacional en Lima ──────────────────────────────── */
	$d['courier-internacional-en-lima'] = array(
		'parent' => '',
		'label'  => 'Courier internacional',
		'kw'     => 'courier internacional en Lima',
		'meta'   => array(
			'Courier internacional en Lima',
			'Courier Internacional en Lima: Envíos al Exterior | Grenvíos',
			'Courier internacional en Lima para documentos, paquetes y carga: recojo a domicilio, vía aérea o terrestre y precio cerrado antes de despachar.',
		),
		'ent'   => 'Un <strong>courier internacional en Lima</strong> no es solo quien lleva la caja: es quien te dice cuánto vas a pagar antes de despachar, qué admite la aduana del país al que envías y dónde está tu envío en cada tramo. Así trabajamos desde nuestra sede en Lima.',
		'hero'  => array( 'Envíos al exterior', 'Courier internacional en Lima' ),
		'bloques' => array(
			array( 'tipo' => 'intro', 'k' => 'cl_intro',
				'titulo' => 'Qué hace un courier internacional (y qué debería hacer por ti)',
				'texto'  => 'Recoger, embalar, pesar, documentar, despachar, seguir y entregar. Parece una lista, pero la diferencia entre un courier y otro está en lo que hace antes de que el paquete salga: revisar que el contenido se pueda enviar a ese país, que la declaración coincida con la boleta y que el embalaje no te haga pagar por aire. Eso es lo que evita retenciones, reclamos y sorpresas en el precio.' ),
			array( 'tipo' => 'panels', 'k' => 'cl_como', 'fondo' => true,
				'titulo' => 'Cómo trabajamos un envío desde Lima',
				'panels' => array(
					array( 'Precio cerrado antes de despachar', 'Se cobra el mayor entre el peso real y el volumétrico (largo × ancho × alto ÷ 5000). Lo calculamos con la caja ya embalada y te damos el precio final antes de que el envío salga.' ),
					array( 'Aéreo o terrestre, según convenga', 'La vía aérea para lo urgente o de poco peso; la terrestre, hacia los países vecinos, para bultos que abultan y pueden esperar unos días más.' ),
					array( 'Recojo en tu distrito', 'Pasamos por tu casa, oficina o la tienda de tu proveedor dentro de Lima. El recojo no cambia el plazo del envío.' ),
					array( 'Seguimiento con número de guía', 'Desde que el envío se despacha tiene número de guía, y un asesor te confirma por WhatsApp en qué tramo está.' ),
				),
				'nota' => 'Mira los plazos de cada país en <a href="' . $u( '/tiempos-de-entrega/' ) . '">tiempos de entrega</a>.' ),
			array( 'tipo' => 'lista', 'k' => 'cl_elegir',
				'titulo' => 'Cinco preguntas para elegir courier en Lima',
				'items'  => array(
					'¿Me da el precio final antes de despachar, o puede cambiar al pesar?',
					'¿Me dice qué admite la aduana del país al que envío, antes de que compre o embale?',
					'¿El envío lleva número de guía desde el primer día?',
					'¿Recoge en mi distrito o tengo que llevarlo a una oficina?',
					'¿Quién responde si la aduana pide un documento o retiene el envío?',
				),
				'nota' => 'Hay una guía completa en el blog: cómo elegir una agencia de envíos internacionales.' ),
			array( 'tipo' => 'destinos', 'k' => 'cl_dest', 'fondo' => true,
				'titulo' => 'Nuestras rutas desde Lima', 'orden' => 'plazo' ),
		),
		'faqs' => array(
			array( '¿Qué es un courier internacional?', 'Una empresa de mensajería que recoge un envío, lo transporta a otro país y lo entrega a domicilio o en agencia, con un número de guía para seguirlo. El courier también prepara la documentación que pide la aduana.' ),
			array( '¿Recogen el envío en mi casa en Lima?', 'Sí. Coordinamos el recojo en tu casa, oficina o la tienda de tu proveedor dentro de {{origen_ciudad}}, y lo pesamos y embalamos en el momento si hace falta.' ),
			array( '¿Cómo sé cuánto voy a pagar?', 'Con el peso y las medidas de la caja ya embalada y el país de destino. Se cobra el mayor entre el peso real y el volumétrico, y te damos el precio cerrado antes de despachar.' ),
			array( '¿Envían por tierra o solo por avión?', 'Las dos. La vía terrestre está disponible hacia los países vecinos de la ruta —Ecuador, Colombia, Chile, Bolivia y Argentina—; al resto se envía por avión.' ),
		),
		'relacionados' => array(
			'Antes de enviar' => array( 'cuanto-cuesta-enviar-un-paquete-al-extranjero', 'que-se-puede-enviar', 'recojo-a-domicilio-lima' ),
			'Siguiente paso'  => array( 'cotizar' ),
		),
	);

	/* ── Ropa ────────────────────────────────────────────────────────── */
	$d['envio-de-ropa-al-extranjero'] = array(
		'parent' => '',
		'label'  => 'Envío de ropa',
		'kw'     => 'enviar ropa al extranjero',
		'meta'   => array(
			'Envío de ropa al extranjero',
			'Enviar Ropa al Extranjero desde Lima | Grenvíos',
			'Cómo enviar ropa al extranjero desde Lima sin pagar de más: embalaje que baja el peso volumétrico, prendas de marca, ropa nueva y usada, y qué pide la aduana.',
		),
		'ent'   => 'Para <strong>enviar ropa al extranjero</strong> lo que más pesa en el precio no es la ropa: es el aire. La ropa pesa poco y ocupa mucho, así que se cobra por volumen. Doblarla, comprimirla y elegir bien la caja es lo que más baja el costo.',
		'hero'  => array( 'Ropa, calzado y textiles', 'Envío de ropa al extranjero' ),
		'bloques' => array(
			array( 'tipo' => 'intro', 'k' => 'ro_intro',
				'titulo' => 'La ropa se paga por el espacio que ocupa',
				'texto'  => 'Una caja de ropa doblada sin cuidado puede costar casi el doble que la misma ropa enrollada y comprimida. La razón es el peso volumétrico —largo × ancho × alto ÷ 5000—: cuando la caja abulta más de lo que pesa, se cobra por el volumen. Por eso la ropa es el envío donde más se nota un buen embalaje.' ),
			array( 'tipo' => 'pasos', 'k' => 'ro_emb', 'fondo' => true,
				'titulo' => 'Cómo embalar ropa para que cueste menos',
				'pasos'  => array(
					array( 'Enrolla en vez de doblar.', 'Ocupa menos y se arruga menos. Las prendas gruesas, al fondo.' ),
					array( 'Comprime al vacío las prendas voluminosas.', 'Casacas, chompas y edredones se reducen mucho en bolsas de vacío.' ),
					array( 'Elige la caja al final.', 'Primero junta la ropa, luego busca la caja que la contenga justa: una caja grande «por si acaso» se paga entera.' ),
					array( 'Protege de la humedad.', 'Bolsa plástica dentro de la caja, sobre todo en la vía terrestre.' ),
				) ),
			array( 'tipo' => 'lista', 'k' => 'ro_reglas',
				'titulo' => 'Lo que conviene saber antes de enviar ropa',
				'items'  => array(
					'Prendas de marca nacional: la cantidad que quieras.',
					'Prendas originales o réplicas de marcas reconocidas: solo dos prendas por marca.',
					'Ropa nueva para vender: va con boleta o factura y paga como mercancía.',
					'Ropa usada para la familia: descríbela como tal; no la mezcles con prendas nuevas con etiqueta.',
					'Calzado: dentro de su caja o con el interior relleno, para que no se deforme.',
				),
				'nota' => 'Más ideas para ahorrar en <a href="' . $u( '/servicios/peso-volumetrico/' ) . '">peso volumétrico</a>.' ),
			array( 'tipo' => 'destinos', 'k' => 'ro_dest', 'fondo' => true,
				'titulo' => 'A dónde enviamos ropa y cómo llega', 'orden' => 'plazo' ),
		),
		'faqs' => array(
			array( '¿Cuánto cuesta enviar una caja de ropa al extranjero?', 'Depende del país y del mayor entre el peso real y el volumétrico de la caja. Como la ropa abulta, el volumen suele mandar: comprimirla y usar una caja justa es lo que más baja el precio. Te damos el precio cerrado al cotizar.' ),
			array( '¿Puedo enviar ropa de marca?', 'Sí. Prendas de marca nacional, la cantidad que quieras; originales o réplicas de marcas reconocidas, hasta dos prendas por marca.' ),
			array( '¿Es mejor enviar ropa por avión o por tierra?', 'Si el destino es un país vecino de la ruta terrestre y la fecha no aprieta, la terrestre suele salir más a cuenta para ropa, porque se paga por volumen. Para el resto de países, o si corre prisa, aérea.' ),
			array( '¿Tengo que declarar la ropa usada?', 'Sí: todo el contenido se declara. Descríbela como ropa usada, con cantidades, y un valor acorde. Es lo que más agiliza la revisión en aduana.' ),
		),
		'relacionados' => array(
			'Te puede interesar' => array( 'servicios/encomiendas-internacionales', 'servicios/envio-de-regalos-al-extranjero', 'embalaje-para-envios-internacionales' ),
			'Siguiente paso'     => array( 'cotizar' ),
		),
	);

	/* ── Repuestos ───────────────────────────────────────────────────── */
	$d['envio-de-repuestos-al-extranjero'] = array(
		'parent' => '',
		'label'  => 'Envío de repuestos',
		'kw'     => 'enviar repuestos al extranjero',
		'meta'   => array(
			'Envío de repuestos al extranjero',
			'Enviar Repuestos al Extranjero desde Lima | Grenvíos',
			'Envío de repuestos de auto y de maquinaria al extranjero desde Lima: embalaje de piezas pesadas, piezas con aceite o batería, factura y la vía que más conviene.',
		),
		'ent'   => 'Para <strong>enviar repuestos al extranjero</strong> hay que mirar tres cosas antes de la caja: si la pieza lleva aceite, combustible o batería; cuánto pesa frente a lo que abulta; y si es para uso propio o para vender. De eso depende la vía y el papeleo.',
		'hero'  => array( 'Piezas de auto y maquinaria', 'Envío de repuestos al extranjero' ),
		'bloques' => array(
			array( 'tipo' => 'intro', 'k' => 're_intro',
				'titulo' => 'Piezas pesadas, cajas pequeñas: el caso contrario a la ropa',
				'texto'  => 'Un repuesto suele pesar mucho para lo poco que ocupa, así que se cobra por peso real y no por volumen. Lo que más importa entonces es que la pieza llegue entera —el metal golpea y rompe la caja— y que esté limpia de cualquier fluido, porque el transporte, sobre todo el aéreo, no admite restos de aceite ni combustible.' ),
			array( 'tipo' => 'panels', 'k' => 're_tipos', 'fondo' => true,
				'titulo' => 'Según el repuesto',
				'panels' => array(
					array( 'Piezas mecánicas limpias', 'Filtros, pastillas, rodamientos, piezas de suspensión: viajan sin problema por las dos vías si van limpias y bien sujetas.' ),
					array( 'Piezas con fluidos', 'Bombas, inyectores, amortiguadores o cajas usadas tienen que ir drenadas y limpias. Si no se pueden vaciar, consúltanos antes.' ),
					array( 'Piezas con batería o electrónica', 'Por vía aérea no se admiten aparatos con batería interna. Por vía terrestre sí, salvo hacia Chile.' ),
					array( 'Repuestos para vender', 'Van con factura comercial y una descripción precisa (marca, modelo, número de pieza). Si envías seguido, mira envíos para empresas.' ),
				) ),
			array( 'tipo' => 'pasos', 'k' => 're_emb',
				'titulo' => 'Cómo embalar un repuesto',
				'pasos'  => array(
					array( 'Limpia y drena.', 'Ni una gota de aceite, grasa o combustible: mancha la caja y el transporte la rechaza.' ),
					array( 'Protege las partes salientes.', 'Ejes, roscas y conectores con cartón o espuma.' ),
					array( 'Inmoviliza la pieza.', 'Que no se mueva dentro de la caja: el peso concentrado rompe el fondo si golpea.' ),
					array( 'Caja doble o reforzada.', 'Para piezas pesadas, cartón de doble pared y cinta en H.' ),
				) ),
			array( 'tipo' => 'destinos', 'k' => 're_dest', 'fondo' => true,
				'titulo' => 'Rutas y vías disponibles', 'via' => 'terrestre', 'orden' => 'plazo' ),
		),
		'faqs' => array(
			array( '¿Puedo enviar repuestos de auto al extranjero?', 'Sí, limpios y drenados de cualquier fluido. Las piezas con batería o electrónica solo por vía terrestre (salvo a Chile), porque por avión no se admiten.' ),
			array( '¿Se paga por peso o por volumen?', 'Por el mayor de los dos. En repuestos casi siempre manda el peso real, porque son piezas densas.' ),
			array( '¿Necesito factura para enviar repuestos?', 'Si son para vender, sí: factura comercial con marca, modelo y número de pieza. Si son para uso propio, boleta o la descripción con su valor real.' ),
		),
		'relacionados' => array(
			'Te puede interesar' => array( 'servicios/carga-internacional', 'envios-para-empresas', 'embalaje-para-envios-internacionales' ),
			'Siguiente paso'     => array( 'cotizar' ),
		),
	);

	/* ── Libros ──────────────────────────────────────────────────────── */
	$d['envio-de-libros-al-extranjero'] = array(
		'parent' => '',
		'label'  => 'Envío de libros',
		'kw'     => 'enviar libros al extranjero',
		'meta'   => array(
			'Envío de libros al extranjero',
			'Enviar Libros al Extranjero desde Lima | Grenvíos',
			'Cómo enviar libros al extranjero desde Lima: embalaje para que no se doblen ni se mojen, libros usados y nuevos, y cómo se calcula el precio por peso.',
		),
		'ent'   => 'Para <strong>enviar libros al extranjero</strong> hay que pensar al revés que con la ropa: pesan mucho para lo que ocupan, así que se paga por peso real. Lo que más cuida el bolsillo es no mandar libros que se pueden conseguir allá; lo que más cuida los libros, un embalaje que no los deje moverse ni mojarse.',
		'hero'  => array( 'Libros, apuntes y colecciones', 'Envío de libros al extranjero' ),
		'bloques' => array(
			array( 'tipo' => 'intro', 'k' => 'li_intro',
				'titulo' => 'Libros: pesados, frágiles en las esquinas y sensibles a la humedad',
				'texto'  => 'Una caja de libros llena es de las más pesadas que se envían, y los libros sufren de tres maneras: esquinas dobladas, lomos rotos por el peso de otros encima y páginas onduladas por la humedad. Un buen embalaje evita las tres y no añade casi peso.' ),
			array( 'tipo' => 'pasos', 'k' => 'li_emb', 'fondo' => true,
				'titulo' => 'Cómo embalar libros',
				'pasos'  => array(
					array( 'Cajas pequeñas, no una grande.', 'Una caja grande llena de libros pesa demasiado y se rompe; dos medianas viajan mejor.' ),
					array( 'Libros en plano y alternando lomos.', 'Así se reparte el peso y no se tuercen.' ),
					array( 'Bolsa plástica alrededor.', 'Contra la humedad, sobre todo en tránsito terrestre.' ),
					array( 'Rellena los huecos.', 'Que nada se mueva: el movimiento es lo que dobla las esquinas.' ),
				) ),
			array( 'tipo' => 'lista', 'k' => 'li_tips',
				'titulo' => 'Antes de enviar libros',
				'items'  => array(
					'Libros usados de tu biblioteca: descríbelos como usados, con la cantidad y un valor acorde.',
					'Libros nuevos para vender o regalar: con su boleta o factura.',
					'Tesis, apuntes y documentos encuadernados: si tienen valor legal, mira apostilla y traducción.',
					'Libros antiguos o de colección: declara su valor real; es la base del seguro.',
				),
				'nota' => 'Si envías documentos con valor legal, mira <a href="' . $u( '/servicios/apostilla-y-traduccion/' ) . '">apostilla y traducción</a>.' ),
			array( 'tipo' => 'panels', 'k' => 'li_casos', 'fondo' => true,
				'titulo' => 'Quién envía libros al extranjero',
				'panels' => array(
					array( 'Estudiantes que se van a estudiar fuera', 'Libros de la carrera y apuntes que no se consiguen en el país de destino. Conviene enviarlos antes de viajar, por la vía que llegue a tiempo para el inicio de clases.' ),
					array( 'Familias que se mudan', 'La biblioteca de casa suele ser lo más pesado de una mudanza. Si son muchas cajas, mira la opción de mudanza pequeña como carga consolidada.' ),
					array( 'Autores y editoriales', 'Ejemplares para presentaciones, ferias o lectores fuera del Perú. Con factura si se venden, y descritos con título y cantidad.' ),
					array( 'Regalos y libros de colección', 'Un libro antiguo o una edición firmada: declara su valor real, que es la base del seguro si algo pasa en el camino.' ),
				),
				'nota' => 'Si te mudas con muchos libros, mira <a href="' . $u( '/mudanzas-internacionales/' ) . '">mudanzas internacionales</a>.' ),
			array( 'tipo' => 'destinos', 'k' => 'li_dest',
				'titulo' => 'A dónde enviamos libros y cómo llegan', 'orden' => 'plazo' ),
		),
		'faqs' => array(
			array( '¿Cómo se cobra un envío de libros?', 'Por el mayor entre el peso real y el volumétrico; en libros casi siempre manda el peso real, porque son densos.' ),
			array( '¿Puedo enviar libros usados?', 'Sí. Declara que son usados, con la cantidad y un valor acorde.' ),
			array( '¿Cómo evito que los libros lleguen dañados?', 'En cajas pequeñas, en plano, con bolsa contra la humedad y sin huecos que dejen moverse a los libros.' ),
		),
		'relacionados' => array(
			'Te puede interesar' => array( 'servicios/envio-internacional-de-paquetes', 'embalaje-para-envios-internacionales', 'servicios/envio-de-correspondencia-internacional' ),
			'Siguiente paso'     => array( 'cotizar' ),
		),
	);

	/* ── Mudanzas ────────────────────────────────────────────────────── */
	$d['mudanzas-internacionales'] = array(
		'parent' => '',
		'label'  => 'Mudanzas internacionales',
		'kw'     => 'mudanza internacional desde Lima',
		'meta'   => array(
			'Mudanzas internacionales pequeñas',
			'Mudanza Internacional desde Lima | Grenvíos',
			'Mudanza internacional pequeña desde Lima: cajas, enseres y equipaje por vía terrestre o aérea, con inventario, embalaje y recojo a domicilio.',
		),
		'ent'   => 'Una <strong>mudanza internacional desde Lima</strong> no siempre necesita un contenedor. Si te vas con cajas, ropa, libros y algunos enseres, lo práctico es enviarlo como carga consolidada: por tierra hacia los países vecinos, por avión al resto.',
		'hero'  => array( 'Te mudas fuera del Perú', 'Mudanzas internacionales pequeñas' ),
		'bloques' => array(
			array( 'tipo' => 'intro', 'k' => 'mu2_intro',
				'titulo' => 'Entre el equipaje y el contenedor',
				'texto'  => 'Lo que cabe en dos maletas va como equipaje; lo que llena una casa va en contenedor. En medio está la mudanza pequeña: varias cajas, un televisor, libros, ropa de temporada. Para eso sirve la carga consolidada, que junta tus bultos en un solo despacho.' ),
			array( 'tipo' => 'panels', 'k' => 'mu2_via', 'fondo' => true,
				'titulo' => 'Por tierra o por avión',
				'panels' => array(
					array( 'Vía terrestre', 'Hacia Ecuador, Colombia, Chile, Bolivia y Argentina. Es la que mejor rinde en bultos que abultan y en la que caben cosas que el avión no lleva, como aparatos con batería (salvo hacia Chile).' ),
					array( 'Vía aérea', 'Hacia el resto de destinos, y cuando la fecha aprieta. Se cobra por el mayor entre peso real y volumétrico, así que conviene reducir el volumen.' ),
				) ),
			array( 'tipo' => 'pasos', 'k' => 'mu2_pasos',
				'titulo' => 'Cómo preparar una mudanza pequeña',
				'pasos'  => array(
					array( 'Haz el inventario.', 'Caja por caja, con lo que lleva cada una. Es la base de la declaración y del seguro.' ),
					array( 'Numera y rotula las cajas.', '«1 de 6, 2 de 6…», con el nombre del destinatario en cada una.' ),
					array( 'Separa lo que no viaja.', 'Líquidos, inflamables y, por avión, aparatos con batería.' ),
					array( 'Pide el recojo.', 'Pasamos por tu casa en Lima, pesamos, medimos y te damos el precio cerrado.' ),
				) ),
			array( 'tipo' => 'destinos', 'k' => 'mu2_dest', 'fondo' => true,
				'titulo' => 'Destinos y forma de entrega', 'orden' => 'plazo' ),
		),
		'faqs' => array(
			array( '¿Hacen mudanzas internacionales desde Lima?', 'Mudanzas pequeñas, sí: cajas, enseres y equipaje enviados como carga consolidada, por vía terrestre a los países vecinos y aérea al resto. Para una casa entera en contenedor, consúltanos.' ),
			array( '¿Qué es mejor para una mudanza, avión o terrestre?', 'Terrestre si el destino es un país vecino y la fecha no aprieta: rinde más en bultos voluminosos. Aérea al resto de países o cuando hay prisa.' ),
			array( '¿Necesito un inventario?', 'Sí: una lista de lo que va en cada caja, con su valor. Es lo que pide la aduana y la base del seguro.' ),
		),
		'relacionados' => array(
			'Te puede interesar' => array( 'servicios/envio-de-equipaje', 'servicios/carga-terrestre-internacional', 'seguro-de-envios' ),
			'Siguiente paso'     => array( 'cotizar', 'recojo-a-domicilio-lima' ),
		),
	);

	return $d;
}, 10, 2 );

/* Enlaces entrantes desde páginas con autoridad. */
add_filter( 'grenvios_related_map', function ( $mapa, $slug ) {
	$entrantes = array(
		'home'                        => array( 'Rutas y servicios'  => array( 'courier-internacional-en-lima' ) ),
		'servicios'                   => array( 'Más servicios'      => array( 'envio-de-ropa-al-extranjero', 'envio-de-repuestos-al-extranjero', 'envio-de-libros-al-extranjero', 'mudanzas-internacionales' ) ),
		'envio-de-equipaje'           => array( 'Te puede interesar' => array( 'mudanzas-internacionales' ) ),
		'encomiendas-internacionales' => array( 'Te puede interesar' => array( 'envio-de-ropa-al-extranjero' ) ),
		'peso-volumetrico'            => array( 'Te puede interesar' => array( 'envio-de-ropa-al-extranjero' ) ),
		'envio-de-celulares-y-laptops'=> array( 'Te puede interesar' => array( 'envio-de-repuestos-al-extranjero' ) ),
		'carga-terrestre-internacional' => array( 'Te puede interesar' => array( 'mudanzas-internacionales', 'envio-de-repuestos-al-extranjero' ) ),
		'envio-internacional-de-paquetes' => array( 'Te puede interesar' => array( 'envio-de-ropa-al-extranjero', 'envio-de-libros-al-extranjero' ) ),
		'nosotros'                    => array( 'Te puede interesar' => array( 'courier-internacional-en-lima' ) ),
	);
	foreach ( $entrantes as $origen => $bloques ) {
		if ( $slug !== $origen && ! isset( $mapa[ $origen ] ) ) continue;
		foreach ( $bloques as $titulo => $destinos ) {
			$actual = isset( $mapa[ $origen ][ $titulo ] ) ? $mapa[ $origen ][ $titulo ] : array();
			$mapa[ $origen ][ $titulo ] = array_values( array_unique( array_merge( $actual, $destinos ) ) );
		}
	}
	return $mapa;
}, 23, 2 );

/* Tema del bloque «Del blog» de cada página nueva. */
add_filter( 'grenvios_bep_temas', function ( $t ) {
	return array_merge( $t, array(
		'courier-internacional-en-lima'    => array( 'envio-de-paquetes', 'precios-de-envio' ),
		'envio-de-ropa-al-extranjero'      => array( 'encomiendas-familiares', 'peso-volumetrico' ),
		'envio-de-repuestos-al-extranjero' => array( 'envios-para-empresas', 'productos-prohibidos' ),
		'envio-de-libros-al-extranjero'    => array( 'embalaje', 'envio-de-paquetes' ),
		'mudanzas-internacionales'         => array( 'equipaje-y-mudanzas', 'embalaje' ),
	) );
} );

/* Hub de servicios: las nuevas entran en «Más servicios». */
add_filter( 'grenvios_sm_items', function ( $items ) {
	return $items + array(
		'envio-de-ropa-al-extranjero'      => array( 'fa-solid fa-shirt', 'Envío de ropa', 'Embalaje que baja el volumen, prendas de marca y ropa usada.' ),
		'envio-de-repuestos-al-extranjero' => array( 'fa-solid fa-gears', 'Envío de repuestos', 'Piezas de auto y maquinaria: limpias, sujetas y con la vía correcta.' ),
		'envio-de-libros-al-extranjero'    => array( 'fa-solid fa-book', 'Envío de libros', 'Cajas pequeñas, en plano y protegidos de la humedad.' ),
		'mudanzas-internacionales'         => array( 'fa-solid fa-truck-moving', 'Mudanzas pequeñas', 'Cajas y enseres como carga consolidada, por tierra o por avión.' ),
	);
} );
