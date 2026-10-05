<?php
/**
 * Grenvíos — Contenido inicial de las Guías.
 *
 * Ocho guías escritas y listas, cada una con su categoría, su keyword y la
 * página de dinero que refuerza. Se crean como BORRADOR desde
 * «SEO por página → Guías», nunca solas: el contenido debe revisarlo alguien de
 * la empresa antes de publicarse.
 *
 * SOBRE LOS DATOS: el texto solo afirma lo que ya sostiene el propio sitio
 * (plazos por zona, fórmula del peso volumétrico, restricciones por vía, pago en
 * Lima, recojo por distrito). NO inventa precios, tasas de aduana ni límites de
 * valor: donde hace falta una cifra, remite a cotización. Un dato inventado en
 * una web de envíos es una reclamación al día siguiente.
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ══════════════════════════════════════
   LAS GUÍAS
   cluster: slug de la página de dinero que refuerza cada una.
══════════════════════════════════════ */
function grenvios_guias_iniciales() {
	return array(

		array(
			'titulo'   => 'Cómo calcular el peso volumétrico de tu envío (con ejemplos)',
			'slug'     => 'como-calcular-peso-volumetrico',
			'cat'      => 'guias-de-envio',
			'cluster'  => 'servicios/envio-internacional-de-paquetes',
			'kw'       => 'cómo calcular el peso volumétrico',
			'tags'     => array( 'peso volumétrico', 'tarifas', 'embalaje' ),
			'extracto' => 'La caja también pesa: aprende a calcular el peso volumétrico antes de cotizar y evita sorpresas en el precio de tu envío internacional.',
			'html'     => '
<p>Es la primera sorpresa de casi todo el que envía por primera vez: llevas una caja de 5 kilos y te cobran por 7. No es un error. En los envíos internacionales el precio no depende solo de lo que marca la balanza, sino también del <strong>espacio que ocupa el paquete</strong>.</p>

<h2>Por qué existe el peso volumétrico</h2>
<p>Un avión y un camión tienen un límite de espacio antes que un límite de peso. Una caja llena de almohadas puede pesar 3 kilos y ocupar el sitio de una caja de libros de 25. Si se cobrara solo por peso, ese espacio se regalaría. Por eso todas las empresas de transporte internacional —sin excepción— aplican la misma regla: <strong>se cobra el mayor entre el peso real y el peso volumétrico</strong>.</p>

<h2>La fórmula</h2>
<p>Se multiplican las tres medidas de la caja en centímetros y se divide entre un número fijo:</p>
<p><strong>alto × largo × ancho ÷ 5000 = peso volumétrico en kilos</strong></p>
<p>El divisor 5000 es el estándar en vía aérea. En vía terrestre puede variar según la ruta, así que confírmalo al cotizar.</p>

<h3>Un ejemplo con números reales</h3>
<p>Una caja de ropa de 40 × 30 × 30 cm que pesa 5 kilos en la balanza:</p>
<ul>
<li>40 × 30 × 30 = 36.000</li>
<li>36.000 ÷ 5000 = <strong>7,2 kg de peso volumétrico</strong></li>
<li>Como 7,2 es mayor que 5, el envío se cobra por 7,2 kg</li>
</ul>
<p>Ahora la misma ropa en una caja de 35 × 25 × 25 cm: 21.875 ÷ 5000 = 4,4 kg. El peso real (5 kg) pasa a ser el mayor, y pagas por 5 en vez de por 7,2. Mismo contenido, menos dinero.</p>

<h2>Tres cosas que bajan el precio</h2>
<ul>
<li><strong>Usa la caja justa.</strong> El aire que sobra dentro se paga. Es el ajuste más rentable y el que casi nadie hace.</li>
<li><strong>Comprime lo que se pueda.</strong> La ropa y la ropa de cama admiten bolsas de compresión; ahí el volumen baja mucho más que el peso.</li>
<li><strong>Reparte con criterio.</strong> Dos cajas pequeñas y llenas suelen salir mejor que una grande a medias, pero no siempre: pregunta antes de dividir el envío.</li>
</ul>

<h2>Cómo medir bien</h2>
<p>Mide la caja <strong>ya cerrada y con todo dentro</strong>, por su parte más ancha. Si el paquete abulta por algún lado, esa es la medida que cuenta, no la de la caja vacía. Si no es rectangular, toma el máximo de cada eje.</p>

<h2>Calcúlalo tú mismo</h2>
<p>No hace falta que hagas la cuenta a mano: en nuestra página de <a href="/servicios/peso-volumetrico/">peso volumétrico</a> tienes una calculadora donde pones las medidas y el peso, y te dice al instante por cuál de los dos se cobra tu envío.</p>
',
		),

		array(
			'titulo'   => 'Qué no se puede enviar por vía aérea (y sí por terrestre)',
			'slug'     => 'que-no-se-puede-enviar-via-aerea',
			'cat'      => 'aduanas',
			'cluster'  => 'que-se-puede-enviar',
			'kw'       => 'qué no se puede enviar por avión',
			'tags'     => array( 'restricciones', 'aduanas', 'vía aérea' ),
			'extracto' => 'Líquidos, baterías, alimentos: la lista de lo que la vía aérea no admite y que la terrestre sí, para que tu paquete no se quede en el camino.',
			'html'     => '
<p>La mayoría de los envíos que se quedan retenidos no fallan por el transporte, sino por llevar algo que esa vía no admite. Y lo que llama la atención es que <strong>casi todo lo prohibido por avión sí viaja por tierra</strong>. Saber esto antes de armar la caja te ahorra el disgusto.</p>

<h2>Lo que no viaja por vía aérea</h2>
<ul>
<li><strong>Líquidos, cremas y aerosoles.</strong> Sin excepciones, aunque vayan sellados.</li>
<li><strong>Alimentos y productos de consumo.</strong> Incluye conservas y envasados.</li>
<li><strong>Objetos con batería interna.</strong> Celulares, laptops, parlantes, relojes inteligentes. La restricción es por la batería de litio, no por el aparato.</li>
<li><strong>Televisores y pantallas.</strong></li>
<li><strong>Dinero, tarjetas y cheques.</strong> Esto no viaja por ninguna vía.</li>
<li><strong>Medicinas</strong>, salvo el caso de Cuba por vía aérea presentando la receta médica.</li>
</ul>

<h2>Lo que sí admite la vía terrestre</h2>
<p>La vía terrestre es más lenta y solo llega a países de la región, pero es mucho más permisiva:</p>
<ul>
<li>Líquidos y cremas sellados</li>
<li>Alimentos sellados no perecibles</li>
<li>Productos de consumo sellados</li>
<li>Objetos con batería interna</li>
</ul>
<p>Hay una excepción importante: <strong>Chile es más restrictivo</strong> que el resto de destinos terrestres. Si envías allí, confirma tu contenido antes de cerrar la caja.</p>

<h2>Prohibido por cualquier vía</h2>
<p>Dinero, tarjetas y cheques; productos perecibles o refrigerados; material inflamable o peligroso; y especies protegidas o productos restringidos por la aduana del país de destino.</p>

<h2>El caso de la ropa de marca</h2>
<p>Es la duda más frecuente y tiene una regla concreta: si son prendas de marca nacional, puedes enviar la cantidad que quieras. Si son originales o réplicas de marcas reconocidas —Adidas, Nike, Puma y similares—, <strong>solo se admiten dos prendas por marca</strong>. Es un límite aduanero, no nuestro.</p>

<h2>Qué pasa si envías algo no permitido</h2>
<p>La aduana puede retener el envío o destruirlo, y el costo del flete no se devuelve. Por eso revisamos el contenido contigo antes de despachar: son cinco minutos que evitan perder el paquete entero.</p>

<p>Puedes consultar la lista completa por vía y por país en <a href="/que-se-puede-enviar/">qué se puede enviar al extranjero</a>. Y si tienes dudas con un artículo concreto, escríbenos antes de comprarlo o empacarlo.</p>
',
		),

		array(
			'titulo'   => 'Aéreo o terrestre: cuál te conviene según lo que envías',
			'slug'     => 'aereo-o-terrestre-cual-conviene',
			'cat'      => 'guias-de-envio',
			'cluster'  => 'servicios/envio-internacional-de-paquetes',
			'kw'       => 'envío aéreo o terrestre',
			'tags'     => array( 'vía aérea', 'vía terrestre', 'plazos' ),
			'extracto' => 'La vía rápida no siempre es la mejor. Compara plazos, restricciones y costo para elegir bien la modalidad de tu envío internacional.',
			'html'     => '
<p>Elegir entre aéreo y terrestre no es solo elegir entre rápido y barato. Cada modalidad admite cosas distintas, y esa suele ser la diferencia que decide.</p>

<h2>Vía aérea</h2>
<p><strong>A favor:</strong> es la opción rápida y la única disponible para destinos fuera de Sudamérica. Para América y Europa el plazo habitual está entre 4 y 6 días hábiles; hacia Asia, entre 6 y 7.</p>
<p><strong>En contra:</strong> es la más restrictiva. Nada de líquidos, alimentos, productos de consumo ni objetos con batería. Y el precio sube más rápido con el volumen.</p>
<p><strong>Elígela si:</strong> envías documentos, ropa, artesanía, repuestos sin batería o cualquier cosa urgente.</p>

<h2>Vía terrestre</h2>
<p><strong>A favor:</strong> más económica y mucho más permisiva. Admite líquidos, cremas, alimentos sellados no perecibles y objetos con batería.</p>
<p><strong>En contra:</strong> tarda bastante más y solo llega a países de la región. Chile tiene restricciones adicionales.</p>
<p><strong>Elígela si:</strong> no tienes prisa, envías volumen, o tu contenido no puede viajar por avión.</p>

<h2>La pregunta que decide</h2>
<p>Antes de mirar el precio, mira el contenido. Si lo que envías incluye un solo artículo con batería o un solo líquido, la vía aérea queda descartada por mucho que te corra prisa. Y al revés: si tu destino está fuera de Sudamérica, la terrestre no es una opción.</p>

<h2>Un punto que se pasa por alto: la aduana</h2>
<p>Los plazos que damos son de transporte, en días hábiles desde el despacho en Lima. La aduana del país de destino puede sumar días si decide revisar el envío, y eso no depende de la modalidad ni de nosotros. Si tu envío tiene fecha límite, cuéntalo al cotizar y buscamos el margen.</p>

<p>Puedes comparar los plazos país por país en <a href="/tiempos-de-entrega/">tiempos de entrega</a>.</p>
',
		),

		array(
			'titulo'   => 'Cómo embalar un paquete para que llegue entero',
			'slug'     => 'como-embalar-paquete-envio-internacional',
			'cat'      => 'guias-de-envio',
			'cluster'  => 'servicios/envio-internacional-de-paquetes',
			'kw'       => 'cómo embalar un paquete',
			'tags'     => array( 'embalaje', 'consejos' ),
			'extracto' => 'Un envío internacional se manipula muchas veces antes de llegar. Así se prepara una caja para que aguante el viaje y no pague de más.',
			'html'     => '
<p>Un paquete que sale de Lima se carga, descarga, apila y revisa varias veces antes de llegar a destino. El embalaje no es un trámite: es lo que decide si tu envío llega entero.</p>

<h2>La caja</h2>
<p>Usa cartón corrugado en buen estado, y del <strong>tamaño justo</strong>. Una caja demasiado grande no solo te cuesta más —el aire se paga, por el peso volumétrico— sino que deja que el contenido se mueva y se golpee. Si no tienes una adecuada, en nuestra oficina puedes adquirirla.</p>
<p>No reutilices cajas que ya vengan blandas o con las esquinas vencidas: son las que ceden al apilarlas.</p>

<h2>El relleno</h2>
<p>Nada debe moverse dentro. Rellena los huecos con papel, plástico de burbujas o espuma hasta que puedas agitar la caja sin oír nada. Presta atención a las esquinas y al fondo, que es donde llegan los golpes.</p>

<h2>Lo frágil</h2>
<p>Envuelve cada pieza por separado, no en bloque, y déjala separada de las paredes de la caja. Para objetos muy delicados, lo seguro es la doble caja: la pieza embalada dentro de una caja pequeña, y esa caja dentro de otra mayor con relleno alrededor.</p>

<h2>El cierre</h2>
<p>Cinta de embalaje ancha, en las tres uniones de arriba y de abajo, en forma de H. Nada de cinta adhesiva fina ni de cuerdas: se enganchan en las cintas transportadoras.</p>

<h2>Antes de cerrar, comprueba</h2>
<ul>
<li>Que el contenido esté permitido en la vía que vas a usar</li>
<li>Que la boleta o factura de la mercancía esté a mano (la aduana la exige)</li>
<li>Que no queden etiquetas de envíos anteriores en la caja</li>
</ul>

<h2>Y después de cerrar</h2>
<p>Pesa y mide la caja <strong>ya cerrada</strong>, por su parte más ancha: esas son las medidas que valen para cotizar. Puedes calcular cuánto pesará a efectos de tarifa en nuestra <a href="/servicios/peso-volumetrico/">página de peso volumétrico</a>.</p>
',
		),

		array(
			'titulo'   => 'Qué documentos puedes enviar al extranjero y cuáles necesitan apostilla',
			'slug'     => 'documentos-al-extranjero-apostilla',
			'cat'      => 'documentos',
			'cluster'  => 'servicios/envio-internacional-de-documentos',
			'kw'       => 'enviar documentos al extranjero',
			'tags'     => array( 'documentos', 'apostilla', 'trámites' ),
			'extracto' => 'Títulos, partidas, poderes: qué documentos se pueden enviar fuera de Perú y en qué casos hay que apostillarlos antes de mandarlos.',
			'html'     => '
<p>Enviar un documento al extranjero tiene dos partes que se confunden a menudo: <strong>el transporte</strong> y <strong>la validez legal</strong>. Que el papel llegue no significa que sirva al llegar.</p>

<h2>Qué se puede enviar</h2>
<p>Documentos personales y académicos en general: títulos y certificados de estudios, partidas y actas, poderes, contratos, antecedentes, copias de DNI y pasaporte. Van en sobre A4 sellado y viajan por vía aérea, que es la modalidad rápida.</p>

<h2>Qué no se puede enviar como documento</h2>
<p>Dinero en efectivo, tarjetas y cheques están prohibidos por cualquier vía. No es una política nuestra: es una restricción del transporte internacional.</p>

<h2>Cuándo hace falta apostillar</h2>
<p>La apostilla es el sello que hace que un documento peruano tenga validez legal en otro país firmante del Convenio de La Haya. Sin ella, muchas instituciones extranjeras no aceptarán el documento por mucho que sea original.</p>
<p>Suele exigirse para:</p>
<ul>
<li>Títulos y certificados de estudios que vas a homologar</li>
<li>Partidas de nacimiento, matrimonio o defunción para trámites de residencia o nacionalidad</li>
<li>Poderes y documentos notariales que van a usarse ante autoridades de otro país</li>
<li>Antecedentes penales y policiales para trámites migratorios</li>
</ul>

<h2>El orden correcto</h2>
<p>Este es el error que más veces obliga a repetir todo: <strong>primero se apostilla, después se envía</strong>. Si el documento sale de Perú sin apostillar, no hay forma de apostillarlo desde el extranjero: hay que devolverlo o pedir un duplicado. Si además necesita traducción oficial, el orden habitual es apostillar, traducir y luego enviar.</p>

<h2>Plazos</h2>
<p>El transporte de documentos a América y Europa suele estar entre 4 y 6 días hábiles, y hacia Asia entre 6 y 7. El trámite de apostilla es aparte y tiene sus propios tiempos, así que cuenta ambos si tienes una fecha límite.</p>

<p>Si no estás seguro de si tu documento necesita apostilla, consúltanos antes de enviarlo: es una pregunta de un minuto que evita semanas de retraso. Puedes ver el detalle en <a href="/servicios/apostilla-y-traduccion/">apostilla y traducción</a>.</p>
',
		),

		array(
			'titulo'   => 'Cómo apostillar un documento peruano, paso a paso',
			'slug'     => 'como-apostillar-documento-peruano',
			'cat'      => 'documentos',
			'cluster'  => 'servicios/apostilla-y-traduccion',
			'kw'       => 'cómo apostillar un documento en Perú',
			'tags'     => array( 'apostilla', 'trámites', 'documentos' ),
			'extracto' => 'Qué es la apostilla, qué documentos la necesitan y en qué orden hacer el trámite para que tu papel sirva en el extranjero.',
			'html'     => '
<p>La apostilla es un sello que certifica que la firma y el cargo de quien emitió un documento son auténticos. Con ella, un documento peruano es válido en cualquier país firmante del Convenio de La Haya sin más trámites consulares.</p>

<h2>El orden importa</h2>
<ol>
<li><strong>Comprueba que el documento sea apostillable.</strong> Debe ser un original o una copia certificada emitida por la entidad competente. Una fotocopia simple no se apostilla.</li>
<li><strong>Consigue la certificación previa, si aplica.</strong> Algunos documentos necesitan pasar antes por la institución que los emitió o por un notario.</li>
<li><strong>Apostilla.</strong> El sello se coloca sobre el documento ya certificado.</li>
<li><strong>Traduce, si el destino lo exige.</strong> La traducción oficial va después de la apostilla, no antes: si traduces primero, la traducción no cubre el sello.</li>
<li><strong>Envía.</strong> Y solo entonces. Un documento que sale del país sin apostillar no puede apostillarse desde fuera.</li>
</ol>

<h2>Los documentos que más se apostillan</h2>
<ul>
<li>Títulos universitarios y certificados de estudios, para homologar o postular fuera</li>
<li>Partidas de nacimiento y matrimonio, para residencia o nacionalidad</li>
<li>Antecedentes penales y policiales, para trámites migratorios</li>
<li>Poderes notariales, para representar a alguien ante autoridades de otro país</li>
</ul>

<h2>Errores que obligan a empezar de nuevo</h2>
<ul>
<li><strong>Apostillar una fotocopia simple.</strong> Necesitas original o copia certificada.</li>
<li><strong>Traducir antes de apostillar.</strong> La traducción tiene que incluir el sello.</li>
<li><strong>Enviar sin apostillar</strong> «porque el original ya es válido». En destino no lo será.</li>
<li><strong>Documentos vencidos.</strong> Antecedentes y algunos certificados tienen fecha de caducidad; si el trámite en destino se alarga, pueden llegar caducados.</li>
</ul>

<h2>Cuánto demora</h2>
<p>Depende del tipo de documento y de la entidad que lo emite. Consúltanos tu caso concreto y te decimos el plazo estimado del trámite y del envío por separado, para que puedas planificar con la fecha límite real de tu destino.</p>

<p>Nosotros gestionamos apostilla, traducción oficial y envío en un solo trámite: <a href="/servicios/apostilla-y-traduccion/">ver el servicio</a>.</p>
',
		),

		array(
			'titulo'   => 'Qué documentación necesita tu empresa para exportar desde Perú',
			'slug'     => 'documentacion-para-exportar-desde-peru',
			'cat'      => 'empresas',
			'cluster'  => 'envios-para-empresas',
			'kw'       => 'documentos para exportar desde Perú',
			'tags'     => array( 'empresas', 'exportación', 'aduanas' ),
			'extracto' => 'La mayoría de los envíos comerciales que se retienen no fallan en el transporte, sino en un documento mal emitido. Esto es lo que se revisa antes de despachar.',
			'html'     => '
<p>Cuando una empresa envía al extranjero, el transporte es la parte fácil. Lo que decide si la mercancía llega o se queda parada en aduana es la documentación, y suele fallar siempre por los mismos motivos.</p>

<h2>La factura comercial</h2>
<p>Es el documento central. Debe reflejar el <strong>valor real</strong> de la mercancía, con el detalle de los productos y las cantidades. Declarar de menos «para pagar menos impuestos» es la causa número uno de envíos retenidos, multas y demoras que cuestan mucho más que lo que se pretendía ahorrar.</p>

<h2>La ficha técnica del producto</h2>
<p>Muchas aduanas la piden para clasificar la mercancía, sobre todo en productos industriales, químicos, cosméticos o alimentarios. Tenerla lista de antemano evita que el envío quede en espera mientras se solicita.</p>

<h2>La partida arancelaria</h2>
<p>Es el código que determina qué impuestos aplican y qué requisitos tiene el producto en destino. Un código equivocado puede significar un impuesto distinto del previsto o, directamente, un permiso que no se tramitó.</p>

<h2>Productos restringidos y permisos</h2>
<p>Cada país tiene su propia lista de productos restringidos y sus propios límites de valor libres de impuestos. Lo que sale sin problema hacia un destino puede necesitar certificado o permiso en otro. Esto se revisa <strong>antes</strong> de que la mercancía salga de Lima, no después.</p>

<h2>Lo que conviene tener resuelto de antemano</h2>
<ul>
<li>Factura comercial con valor real y descripción detallada</li>
<li>Ficha técnica del producto, si la aduana de destino la exige</li>
<li>Partida arancelaria correcta</li>
<li>Datos completos del destinatario, incluido su identificador fiscal si es empresa</li>
<li>Comprobación de restricciones del país de destino</li>
</ul>

<h2>Si envías con frecuencia</h2>
<p>Cuando el volumen es estable, tiene poco sentido cotizar cada despacho desde cero. Con cuenta corporativa se trabaja con tarifa acordada según volumen y frecuencia, factura electrónica a nombre de la empresa, recojo programado en tu almacén y un asesor que ya conoce tus rutas y tus productos.</p>

<p>Cuéntanos cuántos envíos haces al mes y a qué destinos en <a href="/envios-para-empresas/">envíos para empresas</a> y te armamos una propuesta.</p>
',
		),

		array(
			'titulo'   => 'Por qué un envío se queda en aduana (y cómo evitarlo)',
			'slug'     => 'por-que-un-envio-se-queda-en-aduana',
			'cat'      => 'aduanas',
			'cluster'  => 'destinos',
			'kw'       => 'envío retenido en aduana',
			'tags'     => array( 'aduanas', 'plazos', 'restricciones' ),
			'extracto' => 'Los cinco motivos reales por los que un paquete se queda retenido en la aduana del país de destino, y qué se puede hacer antes de enviar.',
			'html'     => '
<p>«Lleva una semana en aduana» es una de las frases que más angustia a quien envía. La buena noticia es que los motivos son casi siempre los mismos cinco, y cuatro de ellos se evitan antes de despachar.</p>

<h2>1. El valor declarado no cuadra</h2>
<p>Si la aduana sospecha que el valor declarado no corresponde con el contenido, abre el paquete y lo revisa. Declarar de menos para pagar menos impuestos alarga el trámite, puede acabar en multa y, en el mejor de los casos, cuesta más días de los que se pretendía ahorrar en dinero.</p>

<h2>2. Falta la boleta o factura</h2>
<p>Sin el comprobante de la mercancía, la aduana no tiene con qué valorar el envío. Es el documento que más veces falta y el que más fácil es tener a mano.</p>

<h2>3. El contenido está restringido en ese país</h2>
<p>Cada aduana tiene su propia lista. Lo que pasa sin problema hacia un destino puede necesitar permiso en otro, o estar directamente prohibido. Las medicinas, los productos de consumo y los artículos con batería son los que más se topan con esto.</p>

<h2>4. Se superó el límite de valor libre de impuestos</h2>
<p>Cada país fija un umbral por debajo del cual el envío no paga impuestos. Al superarlo, el destinatario debe pagar antes de retirar, y hasta que no lo haga el paquete no se mueve. Muchas «retenciones» son en realidad esto: un pago pendiente que nadie sabía que existía.</p>

<h2>5. Revisión aleatoria</h2>
<p>Este es el único que no se puede evitar. Las aduanas revisan una parte de los envíos al azar y eso suma días que no dependen del transporte ni del remitente.</p>

<h2>Qué puedes hacer antes de enviar</h2>
<ul>
<li>Declarar el valor real y guardar la boleta o factura</li>
<li>Comprobar las restricciones del país de destino para tu contenido</li>
<li>Preguntar por el límite de valor libre de impuestos de ese destino</li>
<li>Avisar al destinatario de que puede tener que pagar impuestos al retirar</li>
<li>Si tienes fecha límite, dar margen: los plazos de transporte no incluyen aduana</li>
</ul>

<p>Consulta las restricciones y los plazos de cada destino en nuestras <a href="/destinos/">páginas por país</a>, y la lista general en <a href="/que-se-puede-enviar/">qué se puede enviar</a>.</p>
',
		),
	);
}

/* ══════════════════════════════════════
   CREACIÓN (bajo demanda, siempre como borrador)
══════════════════════════════════════ */
add_action( 'admin_post_grenvios_guias_seed', function () {
	if ( ! current_user_can( 'publish_posts' ) ) wp_die( 'Sin permiso.' );
	check_admin_referer( 'grenvios_guias_seed' );

	$creadas = 0;
	$saltadas = 0;

	foreach ( grenvios_guias_iniciales() as $g ) {
		if ( get_page_by_path( $g['slug'], OBJECT, 'post' ) ) { $saltadas++; continue; }

		$id = wp_insert_post( array(
			'post_type'    => 'post',
			'post_status'  => 'draft',          // el contenido lo revisa una persona
			'post_title'   => $g['titulo'],
			'post_name'    => $g['slug'],
			'post_excerpt' => $g['extracto'],
			'post_content' => trim( $g['html'] ),
		), true );
		if ( is_wp_error( $id ) ) continue;

		// Categoría del cluster
		$cat = get_term_by( 'slug', $g['cat'], 'category' );
		if ( $cat ) wp_set_post_categories( $id, array( (int) $cat->term_id ) );

		// Etiquetas
		if ( ! empty( $g['tags'] ) ) wp_set_post_tags( $id, $g['tags'] );

		// Página de dinero que refuerza
		$pagina = get_page_by_path( $g['cluster'] );
		if ( $pagina ) update_post_meta( $id, GRENVIOS_CLUSTER_META, (int) $pagina->ID );

		// Keyword objetivo
		update_post_meta( $id, GRENVIOS_KW_META, $g['kw'] );

		if ( function_exists( 'grenvios_i18n_active' ) && grenvios_i18n_active() && function_exists( 'pll_set_post_language' ) ) {
			pll_set_post_language( $id, grenvios_i18n_default() );
		}
		$creadas++;
	}

	delete_transient( 'grenvios_links_graph' );
	wp_safe_redirect( admin_url( 'edit.php?post_status=draft&gr_creadas=' . $creadas . '&gr_saltadas=' . $saltadas ) );
	exit;
} );

/* Aviso con el resultado. */
add_action( 'admin_notices', function () {
	if ( ! isset( $_GET['gr_creadas'] ) ) return;
	$c = (int) $_GET['gr_creadas'];
	$s = isset( $_GET['gr_saltadas'] ) ? (int) $_GET['gr_saltadas'] : 0;
	echo '<div class="notice notice-success"><p><strong>Guías creadas: ' . $c . '</strong>'
		. ( $s ? ' · ya existían: ' . $s : '' )
		. '. Están en <strong>borrador</strong>: revísalas, añade una imagen destacada y publícalas.</p></div>';
} );

/* ══════════════════════════════════════
   PANTALLA
══════════════════════════════════════ */
add_action( 'admin_menu', function () {
	add_submenu_page(
		'grenvios-seo', 'Guías', 'Guías', 'publish_posts',
		'grenvios-guias', 'grenvios_guias_admin_page'
	);
}, 28 );

function grenvios_guias_admin_page() {
	if ( ! current_user_can( 'publish_posts' ) ) wp_die( 'Sin permiso.' );
	$guias = grenvios_guias_iniciales();
	?>
	<div class="wrap">
		<h1>Guías de contenido</h1>
		<p style="max-width:900px">
			Ocho guías escritas y listas para publicar. Cada una está enfocada a una búsqueda concreta
			y <strong>refuerza una sola página de venta</strong>, que es lo que hace que el contenido
			trabaje para el negocio y no solo para el tráfico.
		</p>
		<div class="notice notice-info inline" style="margin:12px 0;padding:10px 14px;max-width:900px">
			<p style="margin:0">Se crean como <strong>borrador</strong>. Antes de publicar cada una:
			revisa que los datos coincidan con tu operación actual, añade una imagen destacada y ajusta
			lo que quieras. El texto no incluye precios ni cifras de aduana: esos datos cambian y deben
			salir de la empresa, no de una plantilla.</p>
		</div>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="grenvios_guias_seed">
			<?php wp_nonce_field( 'grenvios_guias_seed' ); ?>
			<?php submit_button( 'Crear las guías como borrador', 'primary', 'submit', false ); ?>
			<span class="description" style="margin-left:10px">Las que ya existan no se duplican.</span>
		</form>

		<table class="widefat striped" style="margin-top:20px">
			<thead><tr><th style="width:34%">Guía</th><th style="width:20%">Keyword</th><th style="width:16%">Categoría</th><th>Refuerza a</th><th style="width:90px">Estado</th></tr></thead>
			<tbody>
			<?php foreach ( $guias as $g ) :
				$post = get_page_by_path( $g['slug'], OBJECT, 'post' ); ?>
				<tr>
					<td><strong><?php echo esc_html( $g['titulo'] ); ?></strong><br><code><?php echo esc_html( $g['slug'] ); ?></code></td>
					<td><?php echo esc_html( $g['kw'] ); ?></td>
					<td><?php echo esc_html( $g['cat'] ); ?></td>
					<td><code><?php echo esc_html( $g['cluster'] ); ?></code></td>
					<td>
						<?php if ( ! $post ) : ?>
							<span style="opacity:.5">—</span>
						<?php else : ?>
							<a href="<?php echo esc_url( get_edit_post_link( $post->ID ) ); ?>">
								<?php echo $post->post_status === 'publish' ? '✅ publicada' : '📝 borrador'; ?>
							</a>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
	<?php
}
