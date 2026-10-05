<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Clúster temático: páginas de apoyo a los servicios pilar y a la home
 *  (2026-09-28)
 * ══════════════════════════════════════════════════════════════════════════
 *
 *   Pilar                          Página de apoyo
 *   ─────────────────────────────  ─────────────────────────────────────────
 *   Carga internacional         ←  /servicios/carga-aerea-internacional/
 *                               ←  /servicios/carga-terrestre-internacional/
 *   Paquetes y documentos       ←  /servicios/envio-express-internacional/
 *   Apostilla y traducción      ←  /servicios/traduccion-oficial-de-documentos/
 *   Home y /destinos/           ←  /envios-a-sudamerica/
 *
 * Cada página responde una búsqueda que su pilar no puede cubrir entera sin
 * perder foco («carga aérea» y «carga terrestre» son intenciones distintas de
 * «carga internacional»), enlaza hacia arriba con ancla descriptiva y recibe
 * enlaces del pilar. Así el pilar gana autoridad temática en vez de competir
 * con sus hijas. La home recibe el ancla «envíos internacionales desde Lima».
 *
 * Criterio de la skill grenvios-diseno: plazos, vías, entrega e impuesto
 * terrestre salen del gestor de destinos (bloque «destinos»); el resto son
 * definiciones del sector o lo que el sitio ya afirma. Nada de precios.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function grenvios_cluster_slugs() {
	return array( 'carga-aerea-internacional', 'carga-terrestre-internacional', 'envio-express-internacional', 'traduccion-oficial-de-documentos', 'envios-a-sudamerica' );
}

add_filter( 'grenvios_pse_slugs', function ( $s ) {
	return array_merge( $s, grenvios_cluster_slugs() );
} );

add_filter( 'grenvios_pse_paginas', function ( $d, $u ) {
	$home = '<a href="' . $u( '/' ) . '">envíos internacionales desde Lima</a>';

	/* ── Carga aérea ─────────────────────────────────────────────────── */
	$d['carga-aerea-internacional'] = array(
		'label' => 'Carga aérea',
		'kw'    => 'carga aérea internacional',
		'meta'  => array(
			'Carga aérea internacional',
			'Carga Aérea Internacional desde Lima | Grenvíos',
			'Carga aérea internacional desde Lima: cuándo conviene el avión, destinos y plazos, qué no viaja por aire y cómo preparar la documentación.',
		),
		'ent'   => 'La <strong>carga aérea internacional</strong> es la vía rápida y la única que llega a destinos fuera de Sudamérica. Aquí explicamos cuándo compensa pagarla, qué no admite y cómo preparar la carga para que salga sin esperas. <a href="%H%/cotizar/">Cotiza tu carga</a>.',
		'hero'  => array( 'Cuando el plazo manda', 'Carga aérea internacional desde {{origen_ciudad}}' ),
		'bloques' => array(
			array( 'tipo' => 'intro', 'k' => 'ca_intro',
				'titulo' => 'La vía rápida para tu mercancía',
				'texto'  => 'La carga aérea se paga por rapidez: acorta el tramo más largo del envío y es la única opción para Estados Unidos, España y los destinos que no tienen ruta por carretera. A cambio, cobra por el espacio que ocupa la carga y tiene más restricciones de contenido que la vía terrestre. Forma parte de nuestra <a href="' . $u( '/servicios/carga-internacional/' ) . '">carga internacional</a>.' ),
			array( 'tipo' => 'panels', 'k' => 'ca_cuando', 'fondo' => true,
				'titulo' => 'Cuándo conviene el avión',
				'panels' => array(
					array( 'Hay fecha de entrega', 'Una feria, un pedido con plazo, un cliente esperando. Si el tiempo cuesta dinero, el avión suele compensar su precio.' ),
					array( 'El destino está lejos', 'Fuera de Sudamérica no hay ruta terrestre: a Estados Unidos, España o Cuba, la vía aérea es la única.' ),
					array( 'La mercancía vale mucho por kilo', 'Electrónica, repuestos o muestras: cuanto más valioso y compacto es el envío, menos pesa el flete en el precio final.' ),
					array( 'Cuándo no conviene', 'Carga pesada y voluminosa hacia países vecinos sin prisa: ahí la <a href="' . $u( '/servicios/carga-terrestre-internacional/' ) . '">carga terrestre</a> suele salir bastante más a cuenta.' ),
				) ),
			array( 'tipo' => 'destinos', 'k' => 'ca_dest', 'via' => 'aereo', 'orden' => 'plazo',
				'titulo' => 'Destinos y plazos por vía aérea',
				'texto'  => 'Plazos en días hábiles desde el despacho, sin contar el tiempo que la aduana del destino pueda retener la carga para revisarla.' ),
			array( 'tipo' => 'lista', 'k' => 'ca_no', 'fondo' => true,
				'titulo' => 'Lo que la vía aérea no admite o limita',
				'items'  => array(
					'Líquidos y productos que puedan derramarse.',
					'Alimentos, salvo los que el destino admita y en las condiciones que fije.',
					'Objetos con batería de litio suelta y power banks.',
					'Aerosoles, inflamables y productos presurizados.',
				),
				'nota' => 'Si tu mercancía está en esta lista y el destino es un país vecino, la vía terrestre suele admitirla. Consulta <a href="' . $u( '/que-se-puede-enviar/' ) . '">qué se puede enviar</a>.' ),
			array( 'tipo' => 'pasos', 'k' => 'ca_pasos',
				'titulo' => 'Cómo preparar una carga aérea',
				'pasos'  => array(
					array( 'Mide cada bulto ya embalado.', 'Por avión se cobra el mayor entre el peso real y el volumétrico (largo × ancho × alto ÷ 5000): las medidas exactas evitan que el precio cambie al recibir la carga.' ),
					array( 'Prepara la factura comercial.', 'Con el valor real, la descripción precisa y la cantidad de cada producto.' ),
					array( 'Haz la lista de bultos.', 'Qué va en cada caja, con su peso y medidas, y que coincida con la factura.' ),
					array( 'Embala para varios tramos.', 'Cajas resistentes, bultos numerados y rotulados igual. En aeropuertos la carga se manipula y se apila.' ),
					array( 'Confirma los permisos.', 'Si el producto los necesita en destino, te decimos cuáles antes de que la carga salga de {{origen_ciudad}}.' ),
				),
				'nota' => 'Si envías de forma recurrente, mira <a href="' . $u( '/envios-para-empresas/' ) . '">envíos para empresas</a>.' ),
		),
		'faqs' => array(
			array( '¿Cómo se cobra la carga aérea?', 'Por el mayor entre el peso real y el peso volumétrico (largo × ancho × alto en centímetros ÷ 5000). Por eso las medidas del bulto embalado son tan importantes como su peso.' ),
			array( '¿A qué destinos llega la carga aérea?', 'A todos nuestros destinos. Para Estados Unidos, España, Venezuela y Cuba es la única vía; para los países vecinos también hay ruta terrestre.' ),
			array( '¿Qué documentos necesita una carga aérea?', 'Factura comercial con el valor real, lista de bultos y, según el producto y el destino, permisos sectoriales. Te confirmamos la lista exacta al cotizar.' ),
		),
		'relacionados' => array(
			'Te puede interesar' => array( 'servicios/carga-internacional', 'servicios/carga-terrestre-internacional', 'servicios/peso-volumetrico' ),
			'Siguiente paso'     => array( 'cotizar', 'envios-para-empresas' ),
		),
	);

	/* ── Carga terrestre ─────────────────────────────────────────────── */
	$d['carga-terrestre-internacional'] = array(
		'label' => 'Carga terrestre',
		'kw'    => 'carga terrestre internacional',
		'meta'  => array(
			'Carga terrestre internacional',
			'Carga Terrestre Internacional desde Lima | Grenvíos',
			'Carga terrestre internacional desde Lima a Ecuador, Colombia, Chile, Bolivia y Argentina: plazos, impuesto pagado en origen y qué admite la carretera.',
		),
		'ent'   => 'La <strong>carga terrestre internacional</strong> es la forma más económica de mover volumen hacia los países vecinos, y admite mercancía que el avión no lleva. Aquí están los destinos, los plazos y cómo se paga el impuesto. <a href="%H%/cotizar/">Cotiza tu carga</a>.',
		'hero'  => array( 'Por carretera a los países vecinos', 'Carga terrestre internacional desde {{origen_ciudad}}' ),
		'bloques' => array(
			array( 'tipo' => 'intro', 'k' => 'ct_intro',
				'titulo' => 'Más volumen por menos, y con menos restricciones',
				'texto'  => 'Por carretera llegamos a los países vecinos de la región. Tarda más que el avión, pero para carga pesada o voluminosa suele salir mucho más a cuenta y admite productos que no pueden volar. Es una de las dos vías de nuestra <a href="' . $u( '/servicios/carga-internacional/' ) . '">carga internacional</a>.' ),
			array( 'tipo' => 'destinos', 'k' => 'ct_dest', 'via' => 'terrestre', 'fondo' => true,
				'titulo' => 'Destinos por vía terrestre',
				'texto'  => 'En estas rutas el impuesto se calcula sobre el valor declarado en la boleta o factura y se paga en {{origen_ciudad}} al despachar: quien recibe no lo paga al recoger.' ),
			array( 'tipo' => 'panels', 'k' => 'ct_pros',
				'titulo' => 'Por qué elegir la carretera',
				'panels' => array(
					array( 'Precio por volumen', 'Para bultos grandes o pesados, el flete terrestre suele ser mucho menor que el aéreo.' ),
					array( 'Admite más productos', 'Líquidos sellados de fábrica, alimentos envasados y objetos con batería, que por avión tienen límites.' ),
					array( 'Impuesto cerrado en origen', 'Se paga al despachar, así que tu destinatario no se lleva sorpresas al recibir.' ),
					array( 'Cuándo no conviene', 'Si hay fecha límite o el destino está fuera de la región: entonces, <a href="' . $u( '/servicios/carga-aerea-internacional/' ) . '">carga aérea</a>.' ),
				) ),
			array( 'tipo' => 'pasos', 'k' => 'ct_pasos', 'fondo' => true,
				'titulo' => 'Cómo preparar una carga terrestre',
				'pasos'  => array(
					array( 'Embala para un viaje largo.', 'La carretera suma días y trasbordos: cajas resistentes, contenido inmóvil y bultos que aguanten peso encima.' ),
					array( 'Declara el valor real.', 'Es la base del impuesto que se paga en origen. Declarar de menos solo retrasa el despacho.' ),
					array( 'Numera y rotula cada bulto.', '1 de 5, 2 de 5… con los mismos datos, para que ninguno se separe del resto.' ),
					array( 'Adjunta factura y lista de bultos.', 'Que coincidan entre sí y con lo que va dentro.' ),
				),
				'nota' => 'Los plazos de cada destino, contados en días hábiles, están en <a href="' . $u( '/tiempos-de-entrega/' ) . '">tiempos de entrega</a>.' ),
		),
		'faqs' => array(
			array( '¿A qué países llega la carga terrestre?', 'A los países vecinos con ruta por carretera: Ecuador, Colombia, Chile, Bolivia y Argentina.' ),
			array( '¿Quién paga el impuesto en la vía terrestre?', 'Se paga en {{origen_ciudad}} al despachar, calculado sobre el valor declarado. Quien recibe no paga nada al recoger.' ),
			array( '¿La carga terrestre admite alimentos y líquidos?', 'Admite más que la aérea: alimentos envasados y líquidos sellados de fábrica, siempre que el país de destino los permita.' ),
		),
		'relacionados' => array(
			'Te puede interesar' => array( 'servicios/carga-internacional', 'servicios/carga-aerea-internacional', 'aduanas-e-impuestos' ),
			'Siguiente paso'     => array( 'cotizar', 'envios-para-empresas' ),
		),
	);

	/* ── Envío express ───────────────────────────────────────────────── */
	$d['envio-express-internacional'] = array(
		'label' => 'Envío express',
		'kw'    => 'envío express internacional',
		'meta'  => array(
			'Envío express internacional',
			'Envío Express Internacional desde Lima | Grenvíos',
			'Envío express internacional desde Lima: destinos ordenados por plazo, qué acelera un envío urgente y qué no, y cómo prepararlo para que salga el mismo día.',
		),
		'ent'   => 'Un <strong>envío express internacional</strong> no depende solo de la vía: depende de lo que se hace antes de despachar. Aquí están nuestros destinos ordenados por plazo y lo que de verdad acorta un envío urgente. <a href="%H%/cotizar/">Cuéntanos tu fecha límite</a>.',
		'hero'  => array( 'Envíos urgentes', 'Envío express internacional desde {{origen_ciudad}}' ),
		'bloques' => array(
			array( 'tipo' => 'intro', 'k' => 'ex_intro',
				'titulo' => 'Lo urgente se decide antes de despachar',
				'texto'  => 'Un documento que tiene que llegar para una firma, un repuesto que detiene una máquina, un regalo con fecha. En un envío urgente, la vía aérea acorta el tramo internacional, pero lo que más tiempo ahorra es que nada se detenga por el camino: datos completos, contenido bien declarado y la documentación lista. Vale para <a href="' . $u( '/servicios/envio-internacional-de-paquetes/' ) . '">paquetes</a> y para <a href="' . $u( '/servicios/envio-internacional-de-documentos/' ) . '">documentos</a>.' ),
			array( 'tipo' => 'destinos', 'k' => 'ex_dest', 'via' => 'aereo', 'orden' => 'plazo', 'fondo' => true,
				'titulo' => 'Nuestros destinos, del más rápido al más lento',
				'texto'  => 'Plazos por vía aérea en días hábiles desde el despacho. Si tu envío es urgente, dínoslo al cotizar: según el destino hay rutas más rápidas.' ),
			array( 'tipo' => 'panels', 'k' => 'ex_que',
				'titulo' => 'Qué acelera un envío y qué no',
				'panels' => array(
					array( 'Sí lo acelera', 'Despachar el mismo día que cotizas, la vía aérea y que el contenido esté bien descrito: es lo que evita preguntas en aduana.' ),
					array( 'No lo acelera', 'Pagar más no acorta la revisión de la aduana del destino: ese tiempo lo fija el país que recibe.' ),
					array( 'Lo retrasa', 'Una dirección incompleta, un teléfono que no contesta o una factura que no coincide con lo que va dentro.' ),
					array( 'Días hábiles', 'Los plazos no cuentan fines de semana ni feriados de origen o destino. Un envío urgente despachado un viernes pierde el fin de semana.' ),
				) ),
			array( 'tipo' => 'pasos', 'k' => 'ex_pasos', 'fondo' => true,
				'titulo' => 'Cómo preparar un envío urgente',
				'pasos'  => array(
					array( 'Escríbenos con la fecha límite.', 'Destino, contenido, peso y medidas en un solo mensaje: la respuesta llega con precio y plazo.' ),
					array( 'Ten el contenido listo para revisar.', 'Si hay que embalar, lo hacemos al recibirlo; si pides recojo, coordínalo con margen.' ),
					array( 'Confirma los datos del destinatario.', 'Nombre como en su documento, dirección con referencias y un teléfono que conteste.' ),
					array( 'Sigue el envío.', 'Con el número de guía en rastreo de envíos, desde el despacho hasta la entrega.' ),
				) ),
		),
		'faqs' => array(
			array( '¿Cuál es el destino más rápido?', 'Estados Unidos, con un plazo de pocos días hábiles por vía aérea. La tabla de esta página ordena todos los destinos por plazo.' ),
			array( '¿Pagar más hace que la aduana revise antes?', 'No. El tiempo de revisión lo fija la aduana del país de destino. Lo que sí evita demoras es declarar el contenido con precisión.' ),
			array( '¿Puedo enviar algo urgente si no estoy en Lima?', 'Sí, aunque debes sumar el tramo nacional hasta {{origen_ciudad}}. Te ayudamos a calcular hacia atrás desde tu fecha límite.' ),
		),
		'relacionados' => array(
			'Te puede interesar' => array( 'servicios/envio-internacional-de-paquetes', 'servicios/envio-internacional-de-documentos', 'tiempos-de-entrega' ),
			'Siguiente paso'     => array( 'cotizar', 'recojo-a-domicilio-lima' ),
		),
	);

	/* ── Traducción oficial ──────────────────────────────────────────── */
	$d['traduccion-oficial-de-documentos'] = array(
		'label' => 'Traducción oficial',
		'kw'    => 'traducción oficial de documentos',
		'meta'  => array(
			'Traducción oficial de documentos',
			'Traducción Oficial de Documentos en Lima | Grenvíos',
			'Traducción oficial y certificada de documentos en Lima: cuál necesitas, en qué orden va con la apostilla y cómo enviar el documento traducido al extranjero.',
		),
		'ent'   => 'Si un documento peruano va a usarse en un país de otro idioma, casi siempre necesita <strong>traducción oficial</strong>. Aquí explicamos qué tipo de traducción te piden, en qué orden va con la apostilla y cómo enviarlo después. <a href="%H%/cotizar/">Consúltanos tu caso</a>.',
		'hero'  => array( 'Documentos para el extranjero', 'Traducción oficial de documentos' ),
		'bloques' => array(
			array( 'tipo' => 'intro', 'k' => 'tr_intro',
				'titulo' => 'La traducción correcta ahorra rehacer el trámite',
				'texto'  => 'Una partida, un título o un certificado que va a Estados Unidos o a cualquier país de otro idioma tiene que llegar traducido, y no vale cualquier traducción: la institución que lo recibe decide qué tipo acepta. Pedir la equivocada obliga a empezar de nuevo. Por eso lo resolvemos junto con la <a href="' . $u( '/servicios/apostilla-y-traduccion/' ) . '">apostilla y traducción</a> y el envío.' ),
			array( 'tipo' => 'panels', 'k' => 'tr_tipos', 'fondo' => true,
				'titulo' => 'Qué tipo de traducción te piden',
				'panels' => array(
					array( 'Traducción oficial', 'La que en Perú emite la oficina de traducciones oficiales del Estado. Es la que suelen exigir los trámites ante organismos públicos.' ),
					array( 'Traducción certificada', 'La que firma un traductor colegiado, que certifica que es fiel al original. Muchas instituciones del extranjero la aceptan.' ),
					array( 'Traducción simple', 'Sin firma que la respalde. Sirve para entender un documento, no para presentarlo en un trámite.' ),
					array( 'La que decide', 'Siempre la institución de destino. Pregunta qué tipo aceptan y si la quieren hecha en origen o en su país.' ),
				) ),
			array( 'tipo' => 'pasos', 'k' => 'tr_orden',
				'titulo' => 'El orden habitual: apostilla, traducción y envío',
				'pasos'  => array(
					array( 'Apostilla el original.', 'La apostilla certifica la firma del documento peruano y se tramita en Perú. Si el destino no la acepta, el camino es la legalización.' ),
					array( 'Traduce el documento completo.', 'Incluida la apostilla: la traducción tiene que cubrir todo lo que la institución va a leer.' ),
					array( 'Revisa nombres y fechas.', 'Que coincidan exactamente con el pasaporte o documento de identidad de la persona.' ),
					array( 'Envíalo protegido.', 'Sobre rígido, sin doblar ni grapar sobre sellos, con número de guía hasta la entrega.' ),
				),
				'nota' => 'El orden puede cambiar según el país y la institución: confírmalo con quien va a recibir el documento. Lo explicamos también en <a href="' . $u( '/servicios/envio-internacional-de-documentos/' ) . '">envío internacional de documentos</a>.' ),
			array( 'tipo' => 'lista', 'k' => 'tr_docs', 'fondo' => true,
				'titulo' => 'Documentos que más se traducen',
				'items'  => array(
					'Partidas de nacimiento, matrimonio y defunción.',
					'Títulos universitarios y certificados de estudios.',
					'Antecedentes penales y policiales.',
					'Poderes, contratos y documentos notariales.',
					'Certificados laborales y constancias de trabajo.',
				) ),
		),
		'faqs' => array(
			array( '¿Qué diferencia hay entre traducción oficial y certificada?', 'La oficial la emite en Perú la oficina de traducciones del Estado; la certificada la firma un traductor colegiado. Qué tipo sirve lo decide la institución que recibe el documento.' ),
			array( '¿Se traduce antes o después de apostillar?', 'Lo habitual es apostillar primero el original y traducir después el documento completo, apostilla incluida. Confírmalo con la institución de destino.' ),
			array( '¿Pueden enviar el documento traducido al extranjero?', 'Sí. Lo enviamos por vía aérea, protegido y con número de guía hasta la dirección de la persona o institución que lo va a presentar.' ),
		),
		'relacionados' => array(
			'Te puede interesar' => array( 'servicios/apostilla-y-traduccion', 'servicios/envio-internacional-de-documentos', 'glosario-de-envios-internacionales' ),
			'Siguiente paso'     => array( 'cotizar' ),
		),
	);

	/* ── Sudamérica (apoya a la home y a /destinos/) ─────────────────── */
	$d['envios-a-sudamerica'] = array(
		'parent' => '',
		'label'  => 'Sudamérica',
		'kw'     => 'envíos a Sudamérica desde Perú',
		'meta'   => array(
			'Envíos a Sudamérica desde Perú',
			'Envíos a Sudamérica desde Perú | Grenvíos',
			'Envíos de Perú a Ecuador, Colombia, Chile, Bolivia, Argentina y Venezuela: vía aérea o terrestre, plazos, entrega y cómo elegir la ruta.',
		),
		'ent'    => 'Hacemos <strong>envíos a Sudamérica desde Perú</strong> por aire y por carretera: seis países con ruta propia, plazos publicados y seguimiento hasta la entrega. Aquí comparas las rutas y eliges la que te conviene. <a href="%H%/cotizar/">Cotiza tu envío</a>.',
		'hero'   => array( 'Rutas por aire y por tierra', 'Envíos a Sudamérica desde {{origen_ciudad}}' ),
		'bloques' => array(
			array( 'tipo' => 'intro', 'k' => 'sa_intro',
				'titulo' => 'La región a la que más enviamos',
				'texto'  => 'Sudamérica es donde nuestros ' . $home . ' tienen más opciones: a los países vecinos se puede enviar por avión o por carretera, y esa elección cambia el precio, el plazo y lo que se puede mandar. Esta página compara las seis rutas de la región; la ficha de cada país tiene el detalle completo.' ),
			array( 'tipo' => 'destinos', 'k' => 'sa_dest', 'paises' => array( 'ecuador', 'colombia', 'chile', 'bolivia', 'argentina', 'venezuela' ), 'fondo' => true,
				'titulo' => 'Las seis rutas, una al lado de la otra',
				'texto'  => 'Pulsa en un país para ver su ficha: ciudades, qué admite su aduana y cómo se entrega.' ),
			array( 'tipo' => 'panels', 'k' => 'sa_via',
				'titulo' => 'Aire o carretera: cómo elegir en la región',
				'panels' => array(
					array( 'Por avión', 'Más rápido y la única vía a Venezuela. Tiene más restricciones de contenido: líquidos, alimentos y baterías tienen límites.' ),
					array( 'Por carretera', 'A Ecuador, Colombia, Chile, Bolivia y Argentina. Más económico para volumen, admite más productos y el impuesto se paga en {{origen_ciudad}} al despachar.' ),
					array( 'Si hay prisa', 'Avión. Si además el envío es urgente, mira <a href="' . $u( '/servicios/envio-express-internacional/' ) . '">envío express internacional</a>.' ),
					array( 'Si hay volumen', 'Carretera. Para cajas grandes o mercancía, <a href="' . $u( '/servicios/carga-terrestre-internacional/' ) . '">carga terrestre internacional</a>.' ),
				) ),
			array( 'tipo' => 'lista', 'k' => 'sa_saber', 'fondo' => true,
				'titulo' => 'Lo que conviene saber antes de enviar a la región',
				'items'  => array(
					'En varios destinos la entrega es en agencia local: quien recibe retira con su documento de identidad.',
					'Cada país tiene su propia lista de productos restringidos, aunque sean vecinos.',
					'Los plazos se cuentan en días hábiles desde el despacho y no incluyen la revisión de aduana.',
					'La dirección se escribe distinto en cada país: la ficha de cada destino explica cómo.',
				),
				'nota' => 'Todas las rutas, también fuera de Sudamérica, están en <a href="' . $u( '/destinos/' ) . '">destinos de envíos internacionales</a>.' ),
		),
		'faqs' => array(
			array( '¿A qué países de Sudamérica envían?', 'Tenemos ruta propia a Ecuador, Colombia, Chile, Bolivia, Argentina y Venezuela. Al resto de la región también enviamos, coordinándolo bajo pedido.' ),
			array( '¿Qué es más barato, aéreo o terrestre?', 'Para envíos pesados o voluminosos, la vía terrestre suele ser bastante más económica. Para un sobre o una caja pequeña con prisa, la aérea.' ),
			array( '¿A qué países se puede enviar por tierra?', 'A Ecuador, Colombia, Chile, Bolivia y Argentina. A Venezuela, solo por vía aérea.' ),
		),
		'relacionados' => array(
			'Destinos'       => array( 'destinos/ecuador', 'destinos/colombia', 'destinos/chile', 'destinos/bolivia', 'destinos/argentina', 'destinos/venezuela' ),
			'Siguiente paso' => array( 'destinos', 'cotizar' ),
		),
	);

	return $d;
}, 11, 2 );

/* ── Enlaces entrantes: del pilar a sus páginas de apoyo, y a la home ── */
add_filter( 'grenvios_related_map', function ( $mapa, $slug ) {
	$entrantes = array(
		'carga-internacional'               => array( 'Por vía'            => array( 'servicios/carga-aerea-internacional', 'servicios/carga-terrestre-internacional' ) ),
		'envio-internacional-de-paquetes'   => array( 'Te puede interesar' => array( 'servicios/envio-express-internacional' ) ),
		'envio-internacional-de-documentos' => array( 'Te puede interesar' => array( 'servicios/envio-express-internacional', 'servicios/traduccion-oficial-de-documentos' ) ),
		'apostilla-y-traduccion'            => array( 'Te puede interesar' => array( 'servicios/traduccion-oficial-de-documentos' ) ),
		'destinos'                          => array( 'Por región'         => array( 'envios-a-sudamerica' ) ),
		'tiempos-de-entrega'                => array( 'Antes de enviar'    => array( 'servicios/envio-express-internacional' ) ),
		'home'                              => array( 'Rutas y servicios'  => array( 'envios-a-sudamerica', 'servicios/carga-aerea-internacional', 'servicios/carga-terrestre-internacional', 'servicios/envio-express-internacional' ) ),
	);
	foreach ( $entrantes as $origen => $bloques ) {
		if ( ! isset( $mapa[ $origen ] ) ) $mapa[ $origen ] = array();
		foreach ( $bloques as $titulo => $destinos ) {
			$actual = isset( $mapa[ $origen ][ $titulo ] ) ? $mapa[ $origen ][ $titulo ] : array();
			$mapa[ $origen ][ $titulo ] = array_values( array_unique( array_merge( $actual, $destinos ) ) );
		}
	}
	return $mapa;
}, 23, 2 );

/* Tema del «Del blog» de cada página. */
add_filter( 'grenvios_bep_temas', function ( $t ) {
	return array_merge( $t, array(
		'carga-aerea-internacional'        => array( 'envios-para-empresas', 'aerea-o-terrestre' ),
		'carga-terrestre-internacional'    => array( 'aerea-o-terrestre', 'aduanas' ),
		'envio-express-internacional'      => array( 'plazos-de-entrega', 'rastreo-de-envios' ),
		'traduccion-oficial-de-documentos' => array( 'apostilla-y-documentos' ),
		'envios-a-sudamerica'              => array( 'ciudades-de-destino', 'aerea-o-terrestre' ),
	) );
}, 11 );
