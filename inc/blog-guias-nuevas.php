<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Siete guías nuevas del blog (2026-09-27)
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Búsquedas del mapa de keywords que no tenían ni página ni guía. Mismo
 * formato y mismo criterio que inc/blog-guias-contenido.php:
 *
 *   · una pregunta por guía, respondida entera;
 *   · cero cifras inventadas (plazos y precios se enlazan, no se prometen);
 *   · cada una refuerza a su página de dinero (`pilar`);
 *   · títulos sin «al extranjero»: al copiarse a una ruta de país reciben la
 *     coletilla del destino («…a Chile»).
 *
 * %H% = raíz del sitio · %P:slug% = enlace a otra guía · {{origen_*}} = sede.
 *
 * IMPORTAR SOLO ESTAS (el importador general reescribe TODAS las guías y
 * pisaría los retoques hechos a mano en las demás):
 *
 *     grenvios_guias_nuevas_importar();
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function grenvios_guias_nuevas() {
	$faq = function ( $pares ) { return function_exists( 'grenvios_ga_faq' ) ? grenvios_ga_faq( $pares ) : ''; };

	return array(

	/* ─────────────────────────────────────────────────────────────────── */
	'como-funciona-un-envio-internacional' => array(
		'titulo'    => 'Cómo funciona un envío internacional, de la cotización a la entrega',
		'categoria' => 'guias-de-envio',
		'pilar'     => 'como-enviar-un-paquete-al-extranjero',
		'extracto'  => 'Qué pasa con tu paquete desde que pides precio hasta que tu destinatario lo recibe: los siete momentos de un envío internacional y qué puedes hacer tú en cada uno.',
		'html'      => '
<p>Desde fuera, un envío internacional parece una caja que desaparece en {{origen_ciudad}} y reaparece días después en otro país. Por dentro son siete momentos distintos, y en casi todos hay algo que tú puedes hacer para que el siguiente vaya más rápido. Conocerlos también sirve para leer el <a href="%H%/rastreo-de-envios/">rastreo</a> sin preocuparte de más.</p>

<h2>1. La cotización</h2>
<p>El precio sale de cuatro datos: el peso, las medidas de la caja, el destino y la vía. Se cobra el mayor entre el peso real y el <a href="%H%/servicios/peso-volumetrico/">peso volumétrico</a> (largo × ancho × alto ÷ 5000), así que una caja grande y liviana puede costar más que una pequeña y pesada. Con los cuatro datos juntos, la primera respuesta ya trae la cifra cerrada.</p>

<h2>2. La entrega del paquete</h2>
<p>Lo traes a la oficina o lo <a href="%H%/recojo-a-domicilio-lima/">recogemos en tu domicilio</a>. El recojo no suma días: el envío entra en la misma programación.</p>

<h2>3. La revisión y el embalaje</h2>
<p>Antes de cerrar la caja revisamos el contenido contigo. Es el paso que más retenciones evita: aquí se detecta lo que el destino no admite o lo que necesita un documento aparte. Si hace falta, embalamos con una caja ajustada al contenido.</p>

<h2>4. La declaración y el despacho</h2>
<p>Se declara qué va dentro, cuántas unidades y cuánto vale. Una descripción precisa —«3 camisas de algodón, 1 par de zapatillas»— pasa; una genérica —«ropa», «varios»— invita a abrir el paquete. Al despachar recibes tu número de guía.</p>

<h2>5. El tránsito</h2>
<p>Es el tramo más largo. Por vía aérea, el paquete vuela hacia el país de destino; por vía terrestre, viaja por carretera hacia los países vecinos. En la vía terrestre el rastreo se actualiza con menos frecuencia, y eso no significa que el envío esté detenido.</p>

<h2>6. La aduana de destino</h2>
<p>Es el único tramo cuyo tiempo no controla nadie más que el país que recibe. La aduana puede revisar el paquete, pedir un dato o cobrar un impuesto. Si pide algo, te avisamos y te decimos exactamente qué y quién debe presentarlo. En <a href="%H%/aduanas-e-impuestos/">aduanas e impuestos</a> explicamos cómo se calcula lo que se paga.</p>

<h2>7. La entrega</h2>
<p>Según el destino, el paquete llega a domicilio o a una agencia local donde se retira con el documento a cuyo nombre va. El teléfono del destinatario es clave en este paso: es lo que usa el repartidor cuando la dirección no cuadra.</p>

<h2>Lo que depende de ti</h2>
<ul>
<li><strong>Medidas y peso reales</strong> al cotizar: evitan que el precio cambie en el mostrador.</li>
<li><strong>Contenido bien descrito</strong>: acorta la aduana.</li>
<li><strong>Datos completos del destinatario</strong>: nombre como en su documento, dirección con referencias y un teléfono que conteste.</li>
<li><strong>El número de guía a mano</strong>: con él cualquier consulta se resuelve en minutos.</li>
</ul>
<p>Si es tu primer envío, la guía práctica de <a href="%H%/como-enviar-un-paquete-al-extranjero/">cómo enviar un paquete al extranjero</a> resume todo en una página.</p>
' . $faq( array(
			array( '¿Cuánto tarda en total un envío internacional?', 'Depende del destino y de la vía. Los plazos se cuentan en días hábiles desde el despacho y no incluyen el tiempo que la aduana del destino pueda retener el paquete. Los de cada país están en la página de tiempos de entrega.' ),
			array( '¿En qué momento puedo seguir mi envío?', 'Desde el despacho, con el número de guía que te entregamos en ese momento.' ),
			array( '¿Qué hago si el rastreo no cambia en varios días?', 'Mira en qué estado está: en tránsito terrestre y en aduana es normal que pase tiempo sin cambios. Si ya pasó el plazo del destino, escríbenos con tu número de guía.' ),
		) ),
	),

	/* ─────────────────────────────────────────────────────────────────── */
	'como-enviar-ropa-sin-pagar-de-mas' => array(
		'titulo'    => 'Cómo enviar ropa sin pagar de más',
		'categoria' => 'guias-de-envio',
		'pilar'     => 'servicios/encomiendas-internacionales',
		'extracto'  => 'La ropa pesa poco y ocupa mucho: por eso se paga por volumen. Cómo doblarla, comprimirla y declararla para que el envío cueste lo justo y pase la aduana sin problemas.',
		'html'      => '
<p>La ropa es lo que más se envía a la familia y, a la vez, lo que más se paga de más. La razón es simple: pesa poco y abulta mucho, y en un envío internacional se cobra el mayor entre el peso real y el <a href="%H%/servicios/peso-volumetrico/">peso volumétrico</a>. Una caja de ropa casi siempre se cobra por su tamaño.</p>

<h2>Reduce el volumen antes de elegir la caja</h2>
<ul>
<li><strong>Dobla, no enrolles con relleno.</strong> La ropa doblada y apilada ocupa menos que enrollada con papel alrededor.</li>
<li><strong>Usa bolsas al vacío</strong> para abrigos, casacas, frazadas y todo lo mullido. Es donde más se nota.</li>
<li><strong>Quita el empaque de tienda</strong>: cajas de zapatos vacías, perchas y cartones de camisas suman volumen sin proteger nada. Los zapatos van rellenos con calcetines.</li>
<li><strong>Elige la caja después</strong>, cuando ya sabes el tamaño real de lo que envías. La caja ajustada es la que cierra sin forzar.</li>
</ul>

<h2>Una caja llena, mejor que dos a medias</h2>
<p>Cada envío tiene su propio despacho. Si vas a mandar ropa a la misma persona en dos momentos cercanos, junta todo en un solo bulto. Y si mezclas ropa con otras cosas —alimentos envasados, regalos—, puede ir en la misma <a href="%H%/servicios/encomiendas-internacionales/">encomienda</a>, cada cosa en su bolsa.</p>

<h2>Cómo declararla</h2>
<p>La aduana del país que recibe mira dos cosas en un envío de ropa: si es para uso personal y cuánto vale.</p>
<ul>
<li><strong>Describe con detalle</strong>: «4 polos de algodón, 2 pantalones jean, 1 casaca». «Ropa» a secas invita a abrir la caja.</li>
<li><strong>Nueva o usada</strong>: la ropa nueva se declara con su valor de compra; la usada, con un valor coherente con su estado.</li>
<li><strong>Cuidado con las cantidades</strong>: diez prendas iguales de la misma talla se leen como mercancía para vender, aunque sean regalos. Varía o consúltanos antes.</li>
</ul>

<h2>Vía aérea o terrestre</h2>
<p>La ropa viaja bien por las dos vías. Hacia países vecinos con ruta terrestre, un bulto grande de ropa que no corre prisa suele salir bastante más a cuenta por carretera. Si hay fecha —un cumpleaños, una mudanza—, la aérea. Consulta los plazos en <a href="%H%/tiempos-de-entrega/">tiempos de entrega</a>.</p>

<h2>Antes de cerrar la caja</h2>
<ol>
<li>Revisa que no haya nada en los bolsillos: encendedores, pilas o monedas pueden retener el envío.</li>
<li>Fotografía el contenido: es la prueba si hay que reclamar.</li>
<li>Mete una copia de la lista de contenido dentro de la caja.</li>
<li>Mide la caja cerrada y <a href="%H%/cotizar/">pide el precio con esas medidas</a>.</li>
</ol>
' . $faq( array(
			array( '¿Se cobra por kilo la ropa?', 'Se cobra el mayor entre el peso real y el volumétrico. Como la ropa abulta mucho para lo que pesa, casi siempre manda el volumétrico: por eso compensa comprimirla.' ),
			array( '¿Puedo enviar ropa usada?', 'Sí, en cantidades de uso personal y declarada como usada. Algunos países tienen reglas propias para la ropa usada: te lo confirmamos al cotizar.' ),
			array( '¿Las bolsas al vacío están permitidas?', 'Sí. Reducen mucho el volumen y, con él, el peso que se cobra.' ),
		) ),
	),

	/* ─────────────────────────────────────────────────────────────────── */
	'como-enviar-perfumes-y-cosmeticos' => array(
		'titulo'    => 'Cómo enviar perfumes y cosméticos',
		'categoria' => 'guias-de-envio',
		'pilar'     => 'que-se-puede-enviar',
		'extracto'  => 'Perfumes, cremas, maquillaje y esmaltes: qué se puede enviar, por qué la vía aérea es la más estricta con ellos y cómo embalarlos para que no se derramen.',
		'html'      => '
<p>Los perfumes y los cosméticos son de los regalos más frecuentes y de los envíos que más dudas generan. La respuesta corta: muchos se pueden enviar, pero no todos ni por cualquier vía. Lo que decide es su composición —alcohol, aerosol, líquido— y el país de destino.</p>

<h2>Por qué la vía aérea es la más estricta</h2>
<p>El perfume lleva alcohol, y el alcohol es inflamable. Por eso, en un avión, los perfumes, los aerosoles y algunos esmaltes se tratan como mercancía peligrosa y tienen restricciones fuertes. Lo mismo pasa con los productos presurizados: espumas, lacas y desodorantes en spray.</p>
<p>La vía terrestre, hacia los países vecinos, es mucho más flexible con los líquidos sellados de fábrica. Si tu destino tiene ruta terrestre, suele ser la forma de enviar este tipo de productos. <a href="%H%/que-se-puede-enviar/">Aquí está lo que admite cada destino</a>.</p>

<h2>Qué suele viajar sin problema</h2>
<ul>
<li><strong>Cosméticos sólidos o en crema</strong>: bases, polvos, labiales, cremas faciales y corporales, en su envase original.</li>
<li><strong>Productos de cuidado personal sin alcohol</strong>: jabones, champús y acondicionadores sellados, sobre todo por vía terrestre.</li>
<li><strong>Maquillaje en paletas</strong> y productos en polvo compacto.</li>
</ul>

<h2>Lo que conviene consultar antes</h2>
<ul>
<li>Perfumes y colonias, por el alcohol.</li>
<li>Aerosoles y productos presurizados de cualquier tipo.</li>
<li>Esmaltes de uñas y quitaesmaltes.</li>
<li>Cantidades grandes del mismo producto: se leen como mercancía para vender y pueden necesitar permiso sanitario en destino.</li>
</ul>

<h2>Cómo embalarlos</h2>
<ol>
<li><strong>Siempre en su envase original y sellado.</strong> Un producto abierto o trasvasado no se puede identificar y es el que se derrama.</li>
<li><strong>Tapa asegurada con cinta</strong> y cada producto en una bolsa cerrada.</li>
<li><strong>Separados del resto</strong>: nunca junto a la ropa o los papeles, por si algo se abre.</li>
<li><strong>Vidrio envuelto por separado</strong>, con relleno alrededor y lejos de las paredes de la caja. Más detalles en <a href="%H%/embalaje-para-envios-internacionales/">embalaje para envíos internacionales</a>.</li>
</ol>

<h2>Cómo declararlos</h2>
<p>Una línea por producto: marca, tipo, cantidad y valor según la boleta. «Cosméticos» a secas no le dice nada al inspector. Si envías varias unidades como regalo, dilo: ayuda a leer el envío como personal y no comercial.</p>
<p>¿Tienes dudas con un producto concreto? <a href="%H%/cotizar/">Escríbenos con el nombre del producto y el destino</a> antes de comprarlo.</p>
' . $faq( array(
			array( '¿Se puede enviar perfume por avión?', 'El perfume lleva alcohol y por avión se trata como mercancía peligrosa, con restricciones fuertes. Consúltanos antes: según el destino, puede ir por vía terrestre.' ),
			array( '¿Puedo enviar cremas y maquillaje?', 'Sí, en su envase original sellado y en cantidades de uso personal. Son de los cosméticos que menos problemas dan.' ),
			array( '¿Necesito permiso para enviar cosméticos?', 'Para uso personal, normalmente no. Si son muchas unidades o son para vender, algunos países piden un permiso sanitario.' ),
		) ),
	),

	/* ─────────────────────────────────────────────────────────────────── */
	'como-enviar-un-titulo-universitario' => array(
		'titulo'    => 'Cómo enviar un título universitario',
		'categoria' => 'documentos',
		'pilar'     => 'servicios/apostilla-y-traduccion',
		'extracto'  => 'Original o copia, apostilla antes o después, traducción y cómo protegerlo en el viaje: lo que conviene decidir antes de enviar un título universitario fuera del país.',
		'html'      => '
<p>Un título universitario casi nunca se envía por enviar: se manda para convalidar estudios, postular a un trabajo o colegiarse en otro país. Eso cambia el orden de las cosas. Lo primero no es el sobre, sino saber qué va a pedir la institución que lo recibe.</p>

<h2>Primero: ¿original o copia?</h2>
<p>Muchas instituciones del extranjero aceptan una copia legalizada y apostillada, y solo unas pocas exigen el original. Pregúntalo antes: un original perdido o retenido es mucho más difícil de reponer que una copia.</p>

<h2>Segundo: la apostilla, antes de enviarlo</h2>
<p>Para que un título peruano tenga validez en otro país, normalmente necesita <a href="%H%/servicios/apostilla-y-traduccion/">apostilla</a>, que certifica que la firma del documento es auténtica. La apostilla se tramita en el país que emitió el documento, así que conviene hacerla antes de que el título salga: después, desde fuera, es más complicado.</p>
<ul>
<li>Antes de apostillar, confirma con tu universidad que las firmas del título estén registradas: solo se apostillan firmas que la autoridad tiene registradas.</li>
<li>Si el país de destino no forma parte del Convenio de la Apostilla, el trámite es la legalización. Lo explicamos en <a href="%P:legalizar-documentos-cuando-el-pais-no-acepta-apostilla%">esta guía</a>.</li>
</ul>

<h2>Tercero: la traducción, si el destino no habla español</h2>
<p>Para Estados Unidos y cualquier país de otro idioma, lo habitual es que pidan una traducción oficial. Pregunta a la institución si la quieren hecha en origen o en destino: algunas solo aceptan traductores de su propio país.</p>

<h2>Cómo protegerlo en el viaje</h2>
<ul>
<li><strong>Sobre rígido o carpeta de cartón</strong>, del tamaño del título. Nunca doblado.</li>
<li><strong>Dentro de una funda plástica</strong>, por la humedad.</li>
<li><strong>Sin grapas ni clips</strong> sobre sellos, firmas o la apostilla.</li>
<li><strong>Una copia escaneada guardada</strong> antes de entregarlo.</li>
</ul>

<h2>Cómo enviarlo</h2>
<p>Un título viaja como documento: por vía aérea, con número de guía y hasta la dirección exacta de la institución o de la persona que lo va a presentar. Si es para una institución, pon el nombre del área o de la persona que lo recibe: los envíos a «la universidad», sin más, se pierden en la mensajería interna. Todo sobre plazos y cómo se entrega, en <a href="%H%/servicios/envio-internacional-de-documentos/">envío internacional de documentos</a>.</p>
' . $faq( array(
			array( '¿Tengo que apostillar el título antes de enviarlo?', 'Si va a usarse para un trámite oficial en otro país, normalmente sí, y conviene hacerlo antes de que salga de Perú: la apostilla se tramita en el país que emitió el documento.' ),
			array( '¿Puedo enviar una copia en lugar del original?', 'Muchas instituciones aceptan copia legalizada y apostillada. Pregúntalo antes de enviar el original.' ),
			array( '¿Cómo evito que se doble o se moje?', 'Sobre rígido o carpeta de cartón, dentro de una funda plástica y sin grapas sobre sellos ni firmas.' ),
		) ),
	),

	/* ─────────────────────────────────────────────────────────────────── */
	'legalizar-documentos-cuando-el-pais-no-acepta-apostilla' => array(
		'titulo'    => 'Legalizar documentos cuando el país no acepta apostilla',
		'categoria' => 'documentos',
		'pilar'     => 'servicios/apostilla-y-traduccion',
		'extracto'  => 'La apostilla solo sirve entre países del Convenio de La Haya. Para el resto, el documento se legaliza en cadena. Qué significa, en qué orden va y qué conviene preguntar antes.',
		'html'      => '
<p>La apostilla es un sello que hace válido un documento en otro país sin más trámites, pero solo funciona entre los países que forman parte del Convenio de La Haya sobre la Apostilla. Si el país de destino no está en el convenio, el documento necesita otro camino: la legalización.</p>

<h2>Apostilla o legalización: cómo saber cuál necesitas</h2>
<p>Depende solo del país donde vas a presentar el documento. La mayoría de los destinos a los que enviamos forman parte del convenio. Cuba es la excepción más habitual en nuestras rutas: no forma parte del convenio, así que sus trámites suelen pedir legalización. Aun así, las listas cambian: confírmalo siempre con la institución que va a recibir el documento o con el consulado.</p>

<h2>Cómo funciona la legalización</h2>
<p>La apostilla es un solo paso. La legalización es una cadena: cada autoridad certifica la firma de la anterior, hasta llegar a una que el país de destino reconoce.</p>
<ol>
<li><strong>La firma del documento</strong> tiene que estar reconocida por la autoridad peruana correspondiente (la entidad que lo emitió o su superior).</li>
<li><strong>La Cancillería peruana</strong> legaliza esa firma.</li>
<li><strong>El consulado del país de destino</strong> en Perú legaliza la firma de la Cancillería.</li>
</ol>
<p>El orden importa: si falta un eslabón, el siguiente no puede firmar. Por eso conviene empezar por preguntar al consulado qué pide exactamente.</p>

<h2>Qué preguntar antes de empezar</h2>
<ul>
<li>¿El documento tiene que ser original o sirve una copia certificada?</li>
<li>¿Hace falta traducción, y quién puede hacerla?</li>
<li>¿Tiene una antigüedad máxima? Algunas partidas y certificados solo valen si son recientes.</li>
<li>¿La legalización consular se hace en Perú o puede hacerse en destino?</li>
</ul>

<h2>Y después, el envío</h2>
<p>Un documento legalizado ha pasado por varias oficinas y cada firma cuesta tiempo. Protégelo como lo que es: sobre rígido, sin doblar, sin grapas sobre los sellos y con una copia escaneada guardada. Lo enviamos por vía aérea, con número de guía, hasta la dirección de quien lo va a presentar. Más en <a href="%H%/servicios/envio-internacional-de-documentos/">envío internacional de documentos</a> y, si el destino sí acepta apostilla, en <a href="%P:apostilla-para-enviar-documentos%">la guía de la apostilla</a>.</p>
' . $faq( array(
			array( '¿Qué diferencia hay entre apostilla y legalización?', 'La apostilla es un solo sello válido entre los países del Convenio de La Haya. La legalización es una cadena de firmas —autoridad emisora, Cancillería y consulado del destino— para los países que no forman parte del convenio.' ),
			array( '¿Cuba acepta la apostilla?', 'Cuba no forma parte del Convenio de la Apostilla, así que sus trámites suelen pedir legalización. Confírmalo siempre con la institución o el consulado, porque los requisitos pueden cambiar.' ),
			array( '¿Pueden legalizar el documento por mí?', 'Nos encargamos del envío y te orientamos sobre el orden del trámite. Escríbenos con el documento y el país de destino.' ),
		) ),
	),

	/* ─────────────────────────────────────────────────────────────────── */
	'como-empezar-a-exportar-con-envios-pequenos' => array(
		'titulo'    => 'Cómo empezar a exportar con envíos pequeños',
		'categoria' => 'empresas',
		'pilar'     => 'envios-para-empresas',
		'extracto'  => 'Para un emprendedor, exportar no empieza con un contenedor: empieza con una muestra y un primer pedido pequeño. Cómo dar esos pasos sin errores de documentación ni sorpresas de precio.',
		'html'      => '
<p>Mucha gente cree que exportar es cosa de contenedores y agentes de aduana. Para un emprendedor, en cambio, suele empezar con algo mucho más pequeño: una muestra para un cliente de otro país y, si le gusta, un primer pedido que cabe en unas cajas. Hacer bien esos primeros envíos es lo que permite que haya un segundo.</p>

<h2>Paso 1: la muestra</h2>
<p>Es tu carta de presentación. Pocas unidades, identificadas como muestra, con una factura proforma que declare su valor real aunque la regales. Si es un alimento o un cosmético, pregunta antes si el destino pide permiso sanitario también para muestras. Todo el detalle está en <a href="%H%/servicios/envio-de-muestras-comerciales/">envío de muestras comerciales</a>.</p>

<h2>Paso 2: el precio de tu producto puesto en destino</h2>
<p>Antes de dar precio a tu cliente, calcula lo que cuesta que tu producto llegue. Eso depende del peso cobrado por caja —el mayor entre real y <a href="%H%/servicios/peso-volumetrico/">volumétrico</a>—, de la vía y de los impuestos del país que recibe. Un embalaje bien pensado desde el principio puede cambiar tu margen: dos cajas ajustadas pagan menos que una grande a medio llenar.</p>

<h2>Paso 3: los documentos del primer pedido</h2>
<ul>
<li><strong>Factura comercial</strong> con el valor real, la descripción precisa y la cantidad de cada producto.</li>
<li><strong>Lista de bultos</strong> que coincida con la factura: qué va en cada caja, con su peso y medidas.</li>
<li><strong>Permisos del producto</strong>, si el destino los pide para esa categoría.</li>
</ul>
<p>La mayoría de las retenciones en aduana nacen de una factura que no coincide con lo que va dentro. <a href="%P:documentos-de-exportacion-para-carga%">Aquí está lo que revisa la aduana</a>.</p>

<h2>Paso 4: una plantilla para repetir</h2>
<p>Cuando el primer pedido llega bien, conviértelo en rutina: la misma descripción de producto en cada factura, las mismas cajas, el mismo rótulo. El envío número diez tiene que pasar igual de limpio que el primero.</p>

<h2>Errores típicos del primer envío</h2>
<ol>
<li><strong>Dar precio sin calcular el flete</strong> y descubrir después que el envío se come el margen.</li>
<li><strong>Declarar de menos para ahorrar impuestos</strong>: es la causa número uno de retenciones.</li>
<li><strong>Cambiar la descripción del producto</strong> de un envío a otro.</li>
<li><strong>Olvidar que el cliente paga impuestos al recibir</strong> en muchos destinos: díselo antes de que compre.</li>
</ol>
<p>Cuando los envíos se vuelven frecuentes, <a href="%H%/envios-para-empresas/">envíos para empresas</a> te ahorra coordinar cada uno desde cero. Y cuando el volumen crece, pasa a <a href="%H%/servicios/carga-internacional/">carga internacional</a>.</p>
' . $faq( array(
			array( '¿Necesito ser exportador registrado para enviar un pedido pequeño?', 'Depende del producto, del valor y del destino. Para envíos pequeños y muestras, muchas veces basta con la factura y la declaración correcta. Te orientamos según tu caso.' ),
			array( '¿Quién paga los impuestos en destino?', 'Normalmente quien recibe, salvo en las rutas terrestres, donde el impuesto se paga en {{origen_ciudad}} al despachar. Acuérdalo con tu cliente antes de venderle.' ),
			array( '¿Cómo calculo el costo de envío para ponerlo en mi precio?', 'Con el peso y las medidas de la caja ya embalada y el destino. Mándanoslos y te damos el precio cerrado.' ),
		) ),
	),

	/* ─────────────────────────────────────────────────────────────────── */
	'como-elegir-una-agencia-de-envios-internacionales' => array(
		'titulo'    => 'Cómo elegir una agencia de envíos internacionales',
		'categoria' => 'guias-de-envio',
		'pilar'     => 'nosotros',
		'extracto'  => 'Precio cerrado, seguimiento real, peso volumétrico explicado y alguien que responda cuando la aduana pide algo: las seis preguntas que conviene hacer antes de entregar tu paquete.',
		'html'      => '
<p>Elegir agencia de envíos por el precio más bajo del primer mensaje es la forma más común de pagar más al final. Lo que distingue a una agencia seria no es la tarifa de partida, sino lo que pasa cuando algo no sale como estaba previsto. Estas son las preguntas que conviene hacer antes de entregar tu paquete en {{origen_ciudad}}.</p>

<h2>1. ¿El precio que me das es cerrado?</h2>
<p>Pide el precio con el peso, las medidas, el destino y la vía. Si la respuesta es una tarifa «desde», pregunta qué la hace subir. Un precio serio te dice qué incluye y en qué caso cambiaría.</p>

<h2>2. ¿Cómo calculan el peso?</h2>
<p>Todas las agencias cobran el mayor entre el peso real y el <a href="%H%/servicios/peso-volumetrico/">peso volumétrico</a>. La diferencia está en si te lo explican antes o te enteras en el mostrador. Desconfía de quien cotiza solo con el peso.</p>

<h2>3. ¿Puedo seguir el envío?</h2>
<p>Pide un número de guía y una forma de consultarlo por tu cuenta. Un seguimiento que solo existe «si preguntas por WhatsApp» no es seguimiento.</p>

<h2>4. ¿Qué pasa si la aduana retiene el paquete?</h2>
<p>Es la pregunta más importante y la que menos se hace. Una retención se resuelve aportando un dato o un documento; lo que necesitas es que alguien te diga cuál y te acompañe en el trámite. Pregunta quién lo hace y cómo te avisan.</p>

<h2>5. ¿Está asegurado?</h2>
<p>Pregunta qué cubre y sobre qué valor. La cobertura se calcula sobre el valor declarado, así que declarar de menos es asegurar de menos. Más en <a href="%H%/seguro-de-envios/">seguro de envíos</a>.</p>

<h2>6. ¿Tienen oficina y alguien que responda?</h2>
<p>Una dirección física, un teléfono y un horario. Cuando hay un problema, poder ir a una oficina o hablar con una persona vale más que cualquier descuento.</p>

<h2>Señales de alerta</h2>
<ul>
<li>Precios muy por debajo del resto sin explicación.</li>
<li>Te sugieren declarar un valor menor «para no pagar impuestos».</li>
<li>No revisan el contenido antes de cerrar la caja.</li>
<li>No te dan número de guía hasta días después.</li>
</ul>
<p>Si quieres saber cómo trabajamos nosotros con cada una de estas preguntas, está en <a href="%H%/nosotros/">quiénes somos</a>. Y para comparar con datos, <a href="%H%/cotizar/">pídenos precio</a> con tu envío concreto.</p>
' . $faq( array(
			array( '¿Por qué dos agencias me dan precios tan distintos?', 'A menudo porque una cotiza solo con el peso y otra con el peso volumétrico, o porque una incluye impuestos y la otra no. Pide siempre el precio cerrado con las medidas de la caja.' ),
			array( '¿Qué debe incluir una buena cotización?', 'El flete hasta el destino, la vía, el plazo estimado y qué conceptos podrían sumarse (recojo, caja, impuestos del destino).' ),
			array( '¿Es mejor una agencia grande o una local?', 'Lo que importa es que tenga seguimiento, precio cerrado y alguien que resuelva cuando la aduana pide algo. Pregunta por esas tres cosas.' ),
		) ),
	),
	);
}

add_filter( 'grenvios_guias_contenido', function ( $guias ) {
	return array_merge( $guias, grenvios_guias_nuevas() );
}, 10 );

/* Importa SOLO las guías nuevas, sin tocar las demás. */
function grenvios_guias_nuevas_importar() {
	if ( ! function_exists( 'grenvios_guias_importar' ) ) return array( 'error' => 'sin importador' );
	$solo = function ( $guias ) { return array_intersect_key( $guias, grenvios_guias_nuevas() ); };
	add_filter( 'grenvios_guias_contenido', $solo, 999 );
	$res = grenvios_guias_importar();
	remove_filter( 'grenvios_guias_contenido', $solo, 999 );
	return $res;
}

/* ── Etiquetas de las guías nuevas ───────────────────────────────────────
 * Las etiquetas se asignan por patrones del slug (inc/blog-etiquetas.php) y
 * estos slugs no encajaban en ninguno: las siete guías no salían en los
 * bloques «Del blog» ni en los archivos de etiqueta. */
add_filter( 'grenvios_etq_def', function ( $def ) {
	$mas = array(
		'envio-de-paquetes'      => array( 'como-funciona-un-envio', 'elegir-una-agencia', 'como-enviar-ropa' ),
		'encomiendas-familiares' => array( 'como-enviar-ropa' ),
		'productos-prohibidos'   => array( 'perfumes-y-cosmeticos' ),
		'apostilla-y-documentos' => array( 'titulo-universitario', 'legalizar-documentos' ),
		'envios-para-empresas'   => array( 'empezar-a-exportar' ),
	);
	foreach ( $mas as $clave => $pats ) {
		if ( isset( $def[ $clave ]['patrones'] ) ) $def[ $clave ]['patrones'] = array_values( array_unique( array_merge( $def[ $clave ]['patrones'], $pats ) ) );
	}
	return $def;
} );

/* Etiqueta SOLO las guías nuevas (maestra y copias). grenvios_etq_sync()
 * reescribe las etiquetas de todas las entradas y borraría ajustes manuales. */
function grenvios_guias_nuevas_etiquetar() {
	if ( ! function_exists( 'grenvios_etq_para_ruta' ) || ! function_exists( 'pll_get_post_translations' ) ) return 0;
	$def = function_exists( 'grenvios_i18n_default' ) ? grenvios_i18n_default() : '';
	$n   = 0;
	foreach ( array_keys( grenvios_guias_nuevas() ) as $slug ) {
		$m = grenvios_guia_maestra( $slug );
		if ( ! $m ) continue;
		foreach ( pll_get_post_translations( $m->ID ) as $l => $pid ) {
			$aguja = (string) get_post_meta( $pid, 'grenvios_guia_key', true ) . ' ' . get_post_field( 'post_name', $pid );
			$tags  = array();
			foreach ( grenvios_etq_para_ruta( $l ) as $clave => $e ) {
				foreach ( $e['patrones'] as $pat ) {
					if ( strpos( $aguja, $pat ) === false ) continue;
					$tslug = ( $l === $def ) ? $clave : $clave . '-' . grenvios_etq_sufijo( $l );
					$tid   = grenvios_etq_term( $tslug, $l );
					if ( $tid ) $tags[] = (int) $tid;
					break;
				}
			}
			if ( $tags ) { wp_set_post_terms( $pid, array_values( array_unique( $tags ) ), 'post_tag', true ); $n++; }
		}
	}
	return $n;
}
