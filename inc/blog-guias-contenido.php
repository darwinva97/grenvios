<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Guías del blog: el contenido que da autoridad temática al dominio
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Antes de este archivo el blog tenía tres entradas de 20 palabras cada una
 * —esqueletos— copiadas a las nueve rutas. Un blog así no aporta nada: ni
 * responde búsquedas, ni refuerza las páginas de servicio, ni da motivos para
 * enlazarlo.
 *
 * Aquí viven catorce guías, una por intención de búsqueda que rodea a cada
 * servicio y a cada destino. Criterio de redacción:
 *
 *   · Cada guía responde a UNA pregunta que la gente hace antes de enviar, y
 *     la responde entera: pasos, errores, qué mirar. Nada de introducciones
 *     de relleno.
 *   · Cero datos inventados. Plazos, precios y porcentajes salen del gestor de
 *     destinos y de las páginas de servicio, que se enlazan; la guía explica
 *     el cómo y el porqué, no promete cifras.
 *   · Cada una refuerza a su página de dinero (el «clúster» del tema): se
 *     enlaza a ella con ancla descriptiva y se le asigna como destino, para
 *     que la página de servicio la muestre en «Guías que te pueden ayudar».
 *   · Los títulos están escritos para que, al copiarse a una ruta, admitan la
 *     coletilla del país sin sonar raro: «Cómo embalar un paquete para
 *     enviarlo» → «…para enviarlo a Chile» (ver inc/seo-entradas.php).
 *
 * %H% es la raíz del sitio; los tokens {{origen_ciudad}} y {{origen_pais}} se
 * resuelven al pintar, así que una sede distinta cambia el texto sola.
 *
 * Importación: grenvios_guias_importar() (inc/blog-guias-importar.php) crea o
 * actualiza la entrada maestra de cada guía y sincroniza sus copias por país.
 * Es idempotente: se puede volver a ejecutar tras retocar un texto aquí.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function grenvios_guias_contenido() {
	return apply_filters( 'grenvios_guias_contenido', array(

	/* ─────────────────────────────────────────────────────────────────── */
	'como-embalar-un-paquete' => array(
		'titulo'    => 'Cómo embalar un paquete para enviarlo',
		'categoria' => 'guias-de-envio',
		'pilar'     => 'servicios/envio-internacional-de-paquetes',
		'extracto'  => 'Caja, relleno, cinta y etiqueta: cómo embalar un paquete para que llegue entero y no pague de más por el espacio vacío.',
		'html'      => '
<p>Un paquete internacional no viaja en un solo vehículo: pasa por camiones, cintas, bodegas y manos distintas antes de llegar. Cada tramo lo golpea un poco. Embalar bien no es un detalle estético: es la diferencia entre un envío que llega entero y un reclamo, y también la diferencia entre pagar lo justo y pagar por aire.</p>

<h2>La caja: ajustada, no grande</h2>
<p>El error más caro es elegir una caja «por si acaso». En un envío internacional se cobra el mayor entre el peso real y el <a href="%H%/servicios/peso-volumetrico/">peso volumétrico</a>, y el volumétrico se calcula con las medidas exteriores de la caja. Cada centímetro de aire alrededor del contenido se paga como si fuera mercancía.</p>
<ul>
<li><strong>Caja de doble pared</strong> para todo lo que pese más de cinco kilos o tenga aristas duras.</li>
<li><strong>Caja nueva o en buen estado.</strong> Una caja reutilizada con las esquinas vencidas cede en el primer apilado.</li>
<li><strong>Sin espacio para que el contenido se mueva.</strong> Si agitas la caja cerrada y oyes movimiento, falta relleno o sobra caja.</li>
</ul>

<h2>El relleno: que nada toque las paredes</h2>
<p>El contenido no debe apoyarse en ningún lado de la caja. Envuelve cada pieza por separado y deja entre tres y cinco centímetros de relleno en todas las caras. Sirven el papel arrugado, el cartón corrugado, el plástico de burbujas o la espuma. Lo que no sirve es la ropa suelta como relleno improvisado: se comprime y deja huecos.</p>
<p>Para lo frágil —cerámica, vidrio, electrónica— la regla es doble caja: la pieza envuelta dentro de una caja pequeña, y esa caja dentro de otra mayor con relleno alrededor.</p>

<h2>Líquidos y objetos que pueden manchar</h2>
<p>Antes de preguntarte cómo embalarlos, comprueba si tu destino los admite: <a href="%H%/que-se-puede-enviar/">no todos los países ni todas las vías aceptan líquidos</a>. Si se admiten, van con la tapa sellada con cinta, dentro de una bolsa cerrada y en posición vertical, separados de todo lo que pueda estropearse si se derraman.</p>

<h2>El cierre: cinta en todas las uniones</h2>
<p>Cinta de embalaje de al menos cinco centímetros de ancho, no cinta adhesiva de oficina. Se sella la unión central de arriba y de abajo y después las dos uniones laterales de cada cara, en forma de H. Una caja que se abre en tránsito es una caja que llega incompleta o no llega.</p>

<h2>La etiqueta: legible y por duplicado</h2>
<ul>
<li>Nombre completo, dirección exacta y <strong>teléfono del destinatario</strong>, escritos con claridad. El teléfono es lo que usa el repartidor cuando la dirección no cuadra.</li>
<li>Tus datos de remitente.</li>
<li>Una copia de los datos <strong>dentro</strong> de la caja: si la etiqueta exterior se daña, el envío sigue teniendo dueño.</li>
</ul>
<p>Retira o tacha etiquetas antiguas, códigos de barras de otros envíos y direcciones de tiendas: una lectora automática puede enviar el paquete al sitio equivocado.</p>

<h2>Lo que conviene hacer antes de cerrar</h2>
<ol>
<li><strong>Fotografía el contenido</strong> con la caja abierta. Es la prueba más simple si hay que reclamar.</li>
<li><strong>Anota qué va dentro</strong> con precisión —«2 camisas, 1 par de zapatillas», no «ropa»—. Esa descripción es la que revisa la aduana, y las vagas invitan a abrir el paquete.</li>
<li><strong>Pesa y mide la caja ya cerrada</strong>, por su parte más ancha. Con esos cuatro números <a href="%H%/cotizar/">te damos el precio cerrado</a>.</li>
</ol>

<p>Si prefieres no hacerlo tú, lo hacemos nosotros: en nuestra sede de {{origen_ciudad}} tenemos cajas de varios tamaños y embalamos el contenido en el momento. Y con el <a href="%H%/recojo-a-domicilio-lima/">recojo a domicilio</a> revisamos el embalaje al recoger, antes de que el paquete empiece a viajar.</p>
',
	),

	/* ─────────────────────────────────────────────────────────────────── */
	'documentos-para-aduana' => array(
		'titulo'    => 'Documentos que pide la aduana para tu envío',
		'categoria' => 'aduanas',
		'pilar'     => 'que-se-puede-enviar',
		'extracto'  => 'Qué papeles revisa la aduana en un envío internacional, cuáles son obligatorios y cuál es el error que más paquetes retiene.',
		'html'      => '
<p>La mayoría de los envíos que se quedan retenidos en aduana no fallan por el transporte. Fallan por un papel: una descripción de contenido que no coincide con lo que va dentro, una factura sin valor, un dato del destinatario incompleto. Todo eso se arregla en cinco minutos antes de despachar y cuesta días resolverlo en destino.</p>

<h2>Lo que revisa cualquier aduana</h2>
<h3>La declaración de contenido</h3>
<p>Es el documento central. Describe qué va en el envío, en qué cantidad y con qué valor. Tiene que ser <strong>precisa</strong>: «3 camisetas de algodón, 1 libro» y no «regalos» o «varios». Una descripción vaga es una invitación a abrir el paquete, y abrirlo significa días.</p>
<h3>El valor declarado</h3>
<p>Es la base sobre la que la aduana calcula el impuesto y sobre la que se calcula el <a href="%H%/seguro-de-envios/">seguro del envío</a>. Tiene que ser el valor real: declarar de menos no abarata el trámite —la aduana conoce los precios— y deja el envío sin cobertura si se pierde.</p>
<h3>Los datos del destinatario</h3>
<p>Nombre completo, dirección exacta, teléfono y, en varios países, un número de identificación (documento de identidad, número fiscal). Sin ese número el envío no puede despacharse en destino aunque todo lo demás esté en orden.</p>

<h2>Lo que se pide según el contenido</h2>
<ul>
<li><strong>Mercancía nueva:</strong> boleta o factura de compra. Es lo que demuestra el valor declarado.</li>
<li><strong>Documentos con efecto legal</strong> (títulos, poderes, partidas): no pagan aduana, pero para tener validez en destino necesitan <a href="%H%/servicios/apostilla-y-traduccion/">apostilla y, a veces, traducción oficial</a>.</li>
<li><strong>Medicinas:</strong> receta médica, y solo en los destinos y vías que las admiten.</li>
<li><strong>Alimentos:</strong> etiqueta original de fábrica visible; sin ella, el control sanitario los rechaza.</li>
<li><strong>Carga comercial:</strong> factura comercial, descripción por partida y, según el producto, ficha técnica o permisos sectoriales. Lo explicamos aparte en <a href="%H%/servicios/carga-internacional/">carga internacional</a>.</li>
</ul>

<h2>Lo que cambia de un país a otro</h2>
<p>Cada aduana fija su propio umbral de valor a partir del cual el envío tributa, su propia lista de productos restringidos y su propia forma de cobrar el impuesto. Por eso un envío idéntico puede entrar sin trámite en un país y pagar en el vecino. Antes de comprar nada, <a href="%H%/destinos/">mira lo que admite tu destino</a>.</p>

<h2>Los tres errores que más paquetes retienen</h2>
<ol>
<li><strong>Descripción que no coincide con el contenido.</strong> Declaraste «ropa» y dentro había un perfume. La aduana lo encuentra, y ya no es un envío: es un problema.</li>
<li><strong>Valor declarado irreal.</strong> Un teléfono nuevo declarado a diez dólares se revisa siempre.</li>
<li><strong>Destinatario sin teléfono o sin número de identificación.</strong> El envío llega y no puede entregarse.</li>
</ol>

<p>Nosotros revisamos la declaración contigo antes de que el envío salga de {{origen_ciudad}}. No es burocracia: es lo que hace que el paquete pase por aduana sin detenerse.</p>
',
	),

	/* ─────────────────────────────────────────────────────────────────── */
	'cuanto-cuesta-un-envio' => array(
		'titulo'    => 'Cuánto cuesta un envío internacional',
		'categoria' => 'guias-de-envio',
		'pilar'     => 'cotizar',
		'extracto'  => 'De qué depende el precio de un envío internacional, por qué se cobra por volumen y las cuatro decisiones que más lo abaratan.',
		'html'      => '
<p>No hay una tarifa única porque no hay un envío único. El precio de mandar algo al extranjero sale de cuatro variables, y entenderlas es la forma más rápida de pagar menos sin que el envío llegue peor.</p>

<h2>1. El peso que se cobra no es el de la balanza</h2>
<p>Se cobra el <strong>mayor</strong> entre el peso real y el peso volumétrico. El volumétrico se calcula multiplicando alto × largo × ancho de la caja en centímetros y dividiendo entre 5000. Una caja de 50 × 40 × 30 cm «pesa» 12 kilos aunque dentro lleve dos kilos de ropa: se cobra por 12. Por eso la primera palanca de precio es la caja, no el contenido. <a href="%H%/servicios/peso-volumetrico/">Aquí está el cálculo con ejemplos</a>.</p>

<h2>2. La vía: aérea o terrestre</h2>
<p>La vía aérea se paga por rapidez; la terrestre, por volumen. Para un documento o un paquete pequeño la diferencia es poca y el avión gana. Para un bulto voluminoso que no corre prisa hacia un país vecino, la carretera puede costar bastante menos. <a href="%H%/servicios/envio-internacional-de-paquetes/">No todos los destinos tienen las dos vías</a>: depende de la ruta.</p>

<h2>3. El destino</h2>
<p>Dos cosas cambian con el país: la tarifa de la ruta y los impuestos de su aduana sobre el valor declarado. En algunos destinos el impuesto se paga aquí, antes de despachar; en otros lo paga quien recibe. Te lo decimos antes de enviar, nunca después. <a href="%H%/destinos/">Cada ficha de destino</a> indica su modalidad y su forma de entrega.</p>

<h2>4. El valor declarado</h2>
<p>No cambia el flete, pero sí el impuesto y el seguro. Declarar por debajo del valor real no abarata nada que valga la pena: deja el envío sin cobertura y, si la aduana lo detecta, lo retiene.</p>

<h2>Lo que abarata un envío de verdad</h2>
<ol>
<li><strong>Ajustar la caja al contenido.</strong> Unos centímetros menos por lado bajan el peso volumétrico y, con él, la factura.</li>
<li><strong>Juntar varios envíos en uno.</strong> Cinco paquetes pequeños pagan cinco fletes; un bulto consolidado paga uno.</li>
<li><strong>Elegir la vía terrestre cuando el plazo lo permite</strong> y el destino la tiene.</li>
<li><strong>Preguntar antes de comprar.</strong> Un producto que la aduana de destino no admite es dinero perdido dos veces: la compra y el flete.</li>
</ol>

<h2>Lo que necesitamos para darte un precio cerrado</h2>
<p>Tres datos: qué envías, cuánto pesa y mide el bulto cerrado, y a qué ciudad va. Con eso calculamos los dos pesos, cotizamos por el mayor y te decimos el precio y el plazo en la misma respuesta. Sin esos datos, cualquier cifra es una estimación que cambiará en el mostrador. <a href="%H%/cotizar/">Pide tu cotización</a>.</p>
',
	),

	/* ─────────────────────────────────────────────────────────────────── */
	'como-calcular-el-peso-volumetrico' => array(
		'titulo'    => 'Cómo calcular el peso volumétrico de tu envío',
		'categoria' => 'guias-de-envio',
		'pilar'     => 'servicios/peso-volumetrico',
		'extracto'  => 'La fórmula del peso volumétrico explicada con tres ejemplos reales, y cómo usarla para que tu envío cueste menos.',
		'html'      => '
<p>Si alguna vez te sorprendió que un paquete ligero costara más que uno pesado, la explicación tiene nombre: peso volumétrico. Es el concepto que más dudas genera al cotizar y el que más dinero ahorra cuando se entiende.</p>

<h2>La fórmula</h2>
<p><strong>Alto × largo × ancho (en centímetros) ÷ 5000 = peso volumétrico en kilos.</strong></p>
<p>Se compara con el peso real de la balanza y se cobra el mayor de los dos. El divisor 5000 es el estándar en envíos internacionales aéreos y es el que aplicamos.</p>

<h2>Tres ejemplos</h2>
<h3>Una caja de zapatos</h3>
<p>35 × 25 × 15 cm → 13 125 ÷ 5000 = <strong>2,6 kg</strong>. Si las zapatillas pesan 1,2 kg, se cobra por 2,6. Aquí el volumétrico manda, como en casi toda la ropa y el calzado.</p>
<h3>Una caja con libros</h3>
<p>40 × 30 × 20 cm → 24 000 ÷ 5000 = <strong>4,8 kg</strong>. Los libros pesan 9 kg reales. Se cobra por 9: manda el peso real.</p>
<h3>Una caja grande con un edredón</h3>
<p>60 × 50 × 40 cm → 120 000 ÷ 5000 = <strong>24 kg</strong>. El edredón pesa 2 kg. Se cobra por 24. Es el caso que más duele y el que más fácil se evita: comprimido en una bolsa al vacío entra en una caja de la mitad y el volumétrico baja a un tercio.</p>

<h2>Por qué se cobra así</h2>
<p>En el avión y en el camión lo que escasea es el espacio, no la capacidad de carga. Una caja grande y ligera ocupa el sitio de una pequeña y pesada. Todas las empresas de envío internacional cobran por el mayor de los dos pesos; lo que cambia entre unas y otras es el divisor y la tarifa por kilo.</p>

<h2>Cómo medir bien</h2>
<ul>
<li>Con la caja <strong>ya armada y cerrada</strong>, no con las medidas nominales del fabricante.</li>
<li>Por la parte <strong>más ancha</strong> de cada lado, incluyendo cualquier abultamiento.</li>
<li>Si el bulto no es rectangular —una maleta, un bulto irregular—, se toma el máximo de cada dimensión.</li>
</ul>

<h2>Cómo bajarlo</h2>
<ol>
<li><strong>Caja a medida del contenido.</strong> Es la palanca principal. En nuestra sede de {{origen_ciudad}} tenemos varios tamaños y elegimos el justo.</li>
<li><strong>Comprimir lo blando.</strong> Ropa, edredones y peluches en bolsas al vacío.</li>
<li><strong>Desmontar lo que se pueda.</strong> Un objeto desarmado ocupa una fracción.</li>
<li><strong>Quitar embalajes de tienda.</strong> Cajas dentro de cajas y relleno de aire suman centímetros que se pagan.</li>
</ol>

<p>Con las medidas de la caja cerrada y su peso real te decimos por cuál de los dos se cobra y cuánto. <a href="%H%/cotizar/">Cotiza con tus medidas</a> o, si no las tienes claras, <a href="%H%/servicios/peso-volumetrico/">revisa la explicación completa</a>.</p>
',
	),

	/* ─────────────────────────────────────────────────────────────────── */
	'aereo-o-terrestre' => array(
		'titulo'    => 'Aéreo o terrestre: qué vía elegir para tu envío',
		'categoria' => 'guias-de-envio',
		'pilar'     => 'servicios/envio-internacional-de-paquetes',
		'extracto'  => 'Cuándo conviene la vía aérea y cuándo la terrestre: precio, plazo, qué admite cada una y en qué destinos existe la opción.',
		'html'      => '
<p>Para muchos destinos no hay elección: solo existe la vía aérea. Pero hacia los países vecinos hay dos caminos, y elegir mal significa pagar más por algo que no corría prisa o esperar semanas por algo que urgía.</p>

<h2>Lo que cambia entre una vía y otra</h2>
<table>
<thead><tr><th></th><th>Aérea</th><th>Terrestre</th></tr></thead>
<tbody>
<tr><td><strong>Plazo</strong></td><td>El más corto de la ruta</td><td>Más largo; depende de la distancia y los pasos de frontera</td></tr>
<tr><td><strong>Precio</strong></td><td>Se paga la rapidez; el volumen pesa mucho en la tarifa</td><td>Más económica para bultos voluminosos</td></tr>
<tr><td><strong>Restricciones</strong></td><td>Las más estrictas: sin líquidos, aerosoles ni baterías internas</td><td>Admite más productos según el país</td></tr>
<tr><td><strong>Disponibilidad</strong></td><td>Todos los destinos</td><td>Solo países con ruta por carretera desde {{origen_pais}}</td></tr>
</tbody>
</table>

<h2>Elige aérea si…</h2>
<ul>
<li>Envías <strong>documentos</strong> o un paquete pequeño: el sobreprecio es mínimo y llega antes.</li>
<li>Hay una <strong>fecha</strong> que no se puede mover.</li>
<li>El destino está fuera de Sudamérica: no hay alternativa.</li>
</ul>

<h2>Elige terrestre si…</h2>
<ul>
<li>El bulto <strong>abulta más de lo que pesa</strong>: ropa, edredones, artesanía, repuestos grandes. El <a href="%H%/servicios/peso-volumetrico/">peso volumétrico</a> castiga menos en camión.</li>
<li>El destino tiene ruta por carretera y la fecha admite el plazo.</li>
<li>El contenido es de los que <a href="%H%/que-se-puede-enviar/">la vía aérea no admite</a> y la terrestre sí en ese país.</li>
</ul>

<h2>Lo que NO cambia</h2>
<p>El paso por aduana es el mismo por aire que por carretera. Si el envío se revisa en destino, se revisa igual; la vía acorta el tramo internacional, no el trámite. Y el seguimiento existe en las dos, aunque por carretera el estado se actualiza con menos frecuencia sin que eso signifique que el envío esté detenido.</p>

<h2>Cómo decidir en un minuto</h2>
<ol>
<li>Mira si tu destino tiene las dos vías en <a href="%H%/destinos/">su ficha</a>.</li>
<li>Pesa y mide la caja: si el volumétrico duplica al real, la terrestre te conviene.</li>
<li>Pon una fecha límite realista. Si hay margen, ahorra; si no lo hay, vuela.</li>
</ol>
<p>Si dudas, <a href="%H%/cotizar/">pide la cotización con las dos vías</a>: te damos precio y plazo de cada una y decides con números.</p>
',
	),

	/* ─────────────────────────────────────────────────────────────────── */
	'que-no-se-puede-enviar' => array(
		'titulo'    => 'Qué no se puede enviar',
		'categoria' => 'aduanas',
		'pilar'     => 'que-se-puede-enviar',
		'extracto'  => 'Lo que ninguna vía admite, lo que depende del país y lo que pasa si envías algo prohibido. Revísalo antes de comprar la caja.',
		'html'      => '
<p>Hay dos listas de prohibiciones, y conviene no confundirlas: la de la <strong>vía</strong> —lo que un avión o un camión no pueden llevar— y la de la <strong>aduana del país de destino</strong>, que es distinta en cada uno. Un producto puede pasar la primera y no la segunda.</p>

<h2>Lo que no viaja por ninguna vía</h2>
<ul>
<li>Dinero en efectivo, cheques y tarjetas de banco.</li>
<li>Armas, munición y sus piezas.</li>
<li>Drogas y sustancias controladas, incluidos productos con cannabis aunque sean legales en origen o en destino.</li>
<li>Animales vivos y muestras biológicas.</li>
<li>Material inflamable, explosivo o corrosivo.</li>
<li>Réplicas de marcas registradas en cantidad comercial.</li>
</ul>

<h2>Lo que la vía aérea no admite y la terrestre a veces sí</h2>
<ul>
<li><strong>Líquidos, cremas y aerosoles:</strong> perfumes, cosméticos, bebidas. Por carretera algunos países los admiten sellados de fábrica.</li>
<li><strong>Artículos con batería interna:</strong> celulares, laptops, relojes inteligentes. Prohibidos en bodega de avión; por carretera se admiten salvo en algunos destinos.</li>
<li><strong>Alimentos:</strong> por avión no; por carretera, solo sellados, no perecibles y hacia los países que los aceptan. <a href="%H%/servicios/envio-de-alimentos/">Lo detallamos aquí</a>.</li>
<li><strong>Medicinas:</strong> según el país y siempre con receta.</li>
</ul>

<h2>Lo que depende de cada aduana</h2>
<p>Suplementos, semillas y plantas, productos de origen animal, tabaco, prendas de marca en cantidad, equipos usados, repuestos de vehículo: cada país tiene su criterio, su límite de cantidad y su umbral de valor. La lista de Ecuador no vale para Chile ni la de Chile para Estados Unidos. Por eso la consulta correcta no es «¿se puede enviar esto?» sino «¿se puede enviar esto a este país por esta vía?». <a href="%H%/destinos/">La ficha de cada destino</a> resume lo que su aduana admite.</p>

<h2>Qué pasa si envías algo prohibido</h2>
<p>La aduana puede retener el envío, devolverlo o destruirlo, y el flete no se devuelve en ninguno de los tres casos. En los peores, hay multa para quien envía. No es un riesgo que compense: por eso revisamos el contenido contigo antes de despachar y preferimos decirte «esto no» en {{origen_ciudad}} a que lo descubras en destino.</p>

<h2>Antes de comprar la caja</h2>
<ol>
<li>Haz la lista de lo que quieres mandar, producto por producto.</li>
<li>Mira <a href="%H%/que-se-puede-enviar/">qué se puede enviar</a> y la ficha de tu destino.</li>
<li>Lo que tenga dudas, pregúntanoslo con el nombre exacto del producto. Cinco minutos de consulta evitan perder el envío entero.</li>
</ol>
',
	),

	/* ─────────────────────────────────────────────────────────────────── */
	'apostilla-para-enviar-documentos' => array(
		'titulo'    => 'Apostilla y traducción para enviar documentos',
		'categoria' => 'documentos',
		'pilar'     => 'servicios/apostilla-y-traduccion',
		'extracto'  => 'Qué es la apostilla, cuándo la necesita un documento, cuándo hace falta además traducción oficial y en qué orden se hace todo.',
		'html'      => '
<p>Un título universitario, una partida de nacimiento o un poder notarial son válidos en {{origen_pais}}. Para que lo sean en otro país necesitan una certificación que diga «este documento es auténtico»: eso es la apostilla. Sin ella, el documento llega, pero no sirve.</p>

<h2>Qué es exactamente la apostilla</h2>
<p>Es un sello o anexo que certifica la autenticidad de la firma y del cargo de quien emitió o legalizó el documento. Lo regula el Convenio de La Haya, y lo aceptan todos los países firmantes sin más trámite. No certifica el contenido del documento —no dice que un título sea bueno—, sino que es real y que lo firmó quien dice firmarlo.</p>

<h2>Cuándo la necesitas</h2>
<ul>
<li><strong>Estudios en el extranjero:</strong> títulos, certificados de notas, constancias.</li>
<li><strong>Trámites de residencia o nacionalidad:</strong> partidas de nacimiento y matrimonio, antecedentes penales.</li>
<li><strong>Trabajo:</strong> certificados laborales, colegiaturas profesionales.</li>
<li><strong>Negocios y poderes:</strong> poderes notariales, escrituras, documentos de empresa.</li>
</ul>
<p>Si el documento va a presentarse ante una institución pública o un notario del país de destino, casi con seguridad la necesita. Si es un documento privado para uso privado, normalmente no.</p>

<h2>Cuándo hace falta también traducción</h2>
<p>Cuando el país de destino no tiene el español como idioma oficial, la institución que recibe el documento pedirá una traducción oficial —hecha por un traductor certificado, con su firma y sello—, y en algunos casos que esa traducción vaya también apostillada. Es habitual para Estados Unidos, Italia y otros países no hispanohablantes.</p>

<h2>El orden importa</h2>
<ol>
<li>Se obtiene el documento original o una copia certificada válida.</li>
<li>Se legaliza y se apostilla en {{origen_pais}}.</li>
<li>Se traduce, si hace falta. Traducir antes de apostillar obliga a repetir.</li>
<li>Se envía por vía aérea, que es la que corresponde a documentos, con seguimiento.</li>
</ol>

<h2>Plazos: dos relojes distintos</h2>
<p>El trámite de apostilla y traducción tiene su propio plazo, que corre en {{origen_pais}}, y el envío tiene el suyo, que empieza cuando el documento ya está listo. Son dos relojes que se suman: conviene empezar por el trámite en cuanto se conoce la fecha del destino.</p>

<h2>Lo que hacemos nosotros</h2>
<p>Gestionamos la legalización, la apostilla y la traducción oficial, y despachamos el documento en el mismo flujo, sin que tengas que ir de una oficina a otra. <a href="%H%/servicios/apostilla-y-traduccion/">Así funciona el servicio</a>, y si tienes fecha límite, <a href="%H%/contacto/">dínoslo desde el principio</a>: es lo que permite ordenar los dos relojes.</p>
',
	),

	/* ─────────────────────────────────────────────────────────────────── */
	'como-enviar-un-documento-urgente' => array(
		'titulo'    => 'Cómo enviar un documento urgente',
		'categoria' => 'documentos',
		'pilar'     => 'servicios/envio-internacional-de-documentos',
		'extracto'  => 'Qué hacer cuando un documento tiene que estar en otro país en pocos días: vía, preparación, seguimiento y los errores que cuestan una semana.',
		'html'      => '
<p>Un contrato que vence, un título que pide una universidad, un poder que necesita un familiar. Los documentos son el envío más urgente que existe y, por suerte, el más fácil de mover rápido: pesan poco, viajan por vía aérea y no pagan aduana. Lo que los retrasa casi nunca es el transporte; es la preparación.</p>

<h2>Primero: ¿el documento ya sirve en destino?</h2>
<p>Antes de enviar nada, comprueba si el documento necesita <a href="%P:apostilla-para-enviar-documentos%">apostilla o traducción oficial</a> para tener validez donde va. Enviar rápido un documento que luego no aceptan es perder el plazo dos veces. Si la necesita, ese trámite va primero.</p>

<h2>Cómo prepararlo</h2>
<ul>
<li><strong>Sobre rígido o carpeta de cartón</strong> que impida que se doble, dentro de la bolsa de envío.</li>
<li><strong>Nunca grapas ni clips</strong> sobre apostillas o sellos: dañan el documento y pueden invalidarlo.</li>
<li><strong>Copia digital</strong> antes de enviar: escaneada o fotografiada. Es un respaldo y, a veces, adelanta el trámite en destino.</li>
<li><strong>Datos completos del destinatario</strong>, con teléfono. En un sobre, la etiqueta es lo único que lo identifica.</li>
</ul>

<h2>Qué se declara</h2>
<p>Los documentos personales y legales no tienen valor comercial y no tributan. Se declaran como lo que son —«documentos personales», «título universitario apostillado»—, sin inventarles un valor. Declararlos como mercancía es el error que los mete en aduana sin motivo.</p>

<h2>La vía y el plazo</h2>
<p>Documentos, siempre por vía aérea: es la más rápida y su precio es de los más bajos del catálogo porque el peso es mínimo. El plazo real depende del destino y se cuenta en días hábiles desde el despacho; <a href="%H%/tiempos-de-entrega/">aquí explicamos cómo se cuentan</a>. Si despachas un viernes por la tarde, el primer día hábil es el lunes.</p>

<h2>Seguimiento</h2>
<p>Desde que se despacha en {{origen_ciudad}} el envío tiene número de guía y puedes consultar en qué tramo está. Para un documento urgente conviene compartir ese número con el destinatario: si el repartidor no lo encuentra, es él quien resuelve.</p>

<h2>Los tres errores que cuestan una semana</h2>
<ol>
<li>Enviar sin apostilla un documento que la necesitaba.</li>
<li>Una dirección sin teléfono o con un dato incompleto: el sobre llega y espera.</li>
<li>Declararlo como mercancía con valor: entra en revisión de aduana sin motivo.</li>
</ol>

<p>Con el documento listo, el despacho es el mismo día: en nuestra sede o con <a href="%H%/recojo-a-domicilio-lima/">recojo a domicilio</a>. <a href="%H%/servicios/envio-internacional-de-documentos/">Así funciona el envío de documentos</a>.</p>
',
	),

	/* ─────────────────────────────────────────────────────────────────── */
	'documentos-de-exportacion-para-carga' => array(
		'titulo'    => 'Documentos de exportación para enviar carga',
		'categoria' => 'empresas',
		'pilar'     => 'servicios/carga-internacional',
		'extracto'  => 'Factura comercial, descripción por partida, permisos: los papeles que una carga necesita para salir sin retenciones y llegar sin sorpresas.',
		'html'      => '
<p>En carga, el transporte es la parte fácil. Lo que separa un despacho limpio de una retención de dos semanas es la documentación, y casi todos los problemas se ven en origen antes de que la mercancía suba al camión o al avión.</p>

<h2>La factura comercial</h2>
<p>Es el documento que la aduana de destino usa para calcular impuestos y para decidir si revisa la carga. Tiene que llevar:</p>
<ul>
<li>Datos completos de quien exporta y de quien importa, con número fiscal.</li>
<li><strong>Descripción precisa de cada producto</strong>: qué es, de qué está hecho, para qué sirve. «Repuestos» no es una descripción; «filtros de aceite para motor diésel, 24 unidades» sí.</li>
<li>Cantidad, valor unitario y valor total, en la moneda que corresponda.</li>
<li>País de origen de la mercancía.</li>
</ul>
<p>El valor tiene que ser el real. Una factura con valores irreales se detecta comparando con precios de mercado, y a partir de ahí la aduana revisa todo lo que venga de ese remitente.</p>

<h2>La lista de empaque</h2>
<p>Qué va en cada bulto, cuánto pesa y cuánto mide cada uno. Es lo que permite a la aduana verificar sin abrir todo, y lo que permite a quien recibe comprobar que llegó completo.</p>

<h2>Lo que depende del producto</h2>
<ul>
<li><strong>Ficha técnica</strong> para maquinaria, electrónica y productos con especificaciones.</li>
<li><strong>Permisos sectoriales</strong> para alimentos, cosméticos, productos de origen animal o vegetal, medicamentos y equipos médicos. Cada país tiene los suyos y algunos se piden antes de embarcar.</li>
<li><strong>Certificado de origen</strong> cuando el destino tiene acuerdos comerciales con {{origen_pais}}: puede reducir o eliminar aranceles.</li>
</ul>

<h2>Lo que depende del destino</h2>
<p>Cada aduana tiene su umbral de valor, su forma de cobrar y sus productos restringidos. Antes de cerrar una venta al extranjero conviene saber qué va a pedir la aduana de ese país y quién va a pagar el impuesto. <a href="%H%/aduanas-e-impuestos/">Lo explicamos aquí</a>, y en <a href="%H%/destinos/">la ficha de cada destino</a> está lo específico de su aduana.</p>

<h2>El error de fondo</h2>
<p>Tratar la documentación como un trámite posterior al envío. Es al revés: se prepara antes, se revisa antes, y solo entonces la carga sale. Nosotros la revisamos contigo en {{origen_ciudad}}, y para empresas que exportan de forma recurrente <a href="%H%/envios-para-empresas/">fijamos un flujo</a> para que cada despacho no empiece de cero. <a href="%H%/servicios/carga-internacional/">Así trabajamos la carga internacional</a>.</p>
',
	),

	/* ─────────────────────────────────────────────────────────────────── */
	'como-enviar-equipaje-por-delante' => array(
		'titulo'    => 'Cómo enviar equipaje por delante',
		'categoria' => 'guias-de-envio',
		'pilar'     => 'servicios/envio-de-equipaje',
		'extracto'  => 'Mudanza, estudios o viaje largo: cómo enviar maletas y cajas antes de volar, qué conviene mandar y cómo se compara con el exceso de equipaje.',
		'html'      => '
<p>Cuando el viaje es largo —una mudanza, un año de estudios, un regreso a casa— el equipaje deja de caber en el avión, y el exceso de equipaje de las aerolíneas se cobra por pieza y sin negociar. Enviar por delante lo que no necesitas el primer día suele salir más barato y, sobre todo, te deja viajar ligero.</p>

<h2>Qué conviene enviar y qué llevar contigo</h2>
<ul>
<li><strong>Enviar:</strong> ropa de temporada, libros, ropa de cama, utensilios, objetos personales que no usarás las primeras semanas.</li>
<li><strong>Llevar en el avión:</strong> documentos, medicinas, electrónica con batería (que además <a href="%H%/que-se-puede-enviar/">no viaja en bodega</a>), objetos de valor y lo que necesites nada más llegar.</li>
</ul>

<h2>Maleta o caja</h2>
<p>Las dos sirven. La maleta protege mejor y se envía como bulto; la caja ajusta mejor el volumen y suele costar menos porque el <a href="%H%/servicios/peso-volumetrico/">peso volumétrico</a> es menor. Si envías una maleta, envuélvela o protégela: en tránsito se trata como cualquier bulto.</p>

<h2>Cómo se cobra</h2>
<p>Por el mayor entre el peso real y el volumétrico de cada bulto. El equipaje tiende a ser voluminoso y ligero —ropa—, así que comprimir en bolsas al vacío y llenar bien cada caja baja el precio de verdad. Un bulto grande mal aprovechado se paga como si estuviera lleno.</p>

<h2>La aduana y el equipaje usado</h2>
<p>El equipaje personal usado suele tener un tratamiento aduanero distinto al de la mercancía nueva, pero cada país fija sus propios límites y pide su propia declaración. Conviene declarar cada bulto por separado, con una descripción honesta —«ropa usada, libros, artículos personales»— y sin mezclar mercancía nueva dentro del equipaje: es lo que convierte un envío personal en una importación.</p>

<h2>Cuándo enviarlo</h2>
<p>Con margen: el equipaje no corre prisa y eso permite elegir la vía terrestre si el destino la tiene, que para bultos voluminosos es la más económica. Cuenta el plazo desde el despacho en días hábiles y súmale el paso por aduana, que no controlamos. <a href="%H%/tiempos-de-entrega/">Así se cuentan los plazos</a>.</p>

<h2>Antes de despachar</h2>
<ol>
<li>Haz inventario de cada bulto y fotografía el contenido.</li>
<li>Pesa y mide cada bulto cerrado.</li>
<li>Pide la cotización comparando con lo que cobra tu aerolínea por el exceso. A partir de la segunda maleta, casi siempre gana el envío.</li>
</ol>
<p><a href="%H%/servicios/envio-de-equipaje/">Así funciona el envío de equipaje</a>, con <a href="%H%/recojo-a-domicilio-lima/">recojo en tu domicilio</a> dentro de {{origen_ciudad}}.</p>
',
	),

	/* ─────────────────────────────────────────────────────────────────── */
	'como-rastrear-tu-envio' => array(
		'titulo'    => 'Cómo rastrear tu envío',
		'categoria' => 'guias-de-envio',
		'pilar'     => 'tiempos-de-entrega',
		'extracto'  => 'Qué significa cada estado del seguimiento, por qué un envío parece detenido y cuándo conviene preguntar.',
		'html'      => '
<p>El seguimiento de un envío internacional genera más ansiedad de la necesaria por una razón simple: los estados no avanzan a ritmo constante. Hay tramos en los que el envío recorre miles de kilómetros sin que cambie nada en pantalla, y otros en los que cambia tres veces en un día. Saber qué significa cada estado evita la mitad de las llamadas.</p>

<h2>El número de guía</h2>
<p>Se genera al despachar el envío y aparece en tu comprobante. Es el identificador de tu envío en todos los tramos; guárdalo y compártelo con el destinatario. Si lo pierdes, con tu nombre y la fecha del despacho lo recuperamos.</p>

<h2>Qué significa cada estado</h2>
<ul>
<li><strong>Recibido en {{origen_ciudad}}.</strong> El bulto está en nuestra sede, pesado y medido. Todavía no ha salido del país.</li>
<li><strong>Despachado.</strong> Se emitió la documentación de exportación y el envío entró en la programación de su vía. Es el punto que separa «lo entregué» de «ya salió».</li>
<li><strong>En tránsito internacional.</strong> Viaja hacia el país de destino. Por vía terrestre el estado se actualiza con menos frecuencia que por aérea: es normal, no significa que esté detenido.</li>
<li><strong>En aduana de destino.</strong> El único tramo cuyo tiempo no controlamos. Si se alarga, casi siempre es por la descripción del contenido o un dato del destinatario, y lo gestionamos nosotros.</li>
<li><strong>En reparto</strong> o <strong>disponible para retiro</strong>. Según el país, el envío sale al domicilio o queda en la agencia local del destinatario.</li>
<li><strong>Entregado.</strong> Con confirmación de entrega.</li>
</ul>

<h2>«Lleva días sin cambiar de estado»</h2>
<p>Las causas, por orden de frecuencia: está en tránsito por carretera (normal), está en aduana (normal hasta cierto punto), o hay un dato que falta (y ahí sí conviene actuar). La regla práctica: si el plazo estimado de la ruta todavía no se ha cumplido, es tránsito; si se cumplió y no hay cambio, escríbenos con el número de guía y te decimos exactamente qué pasa.</p>

<h2>Cómo se cuentan los días</h2>
<p>El plazo de cada destino se cuenta en <strong>días hábiles desde el despacho</strong>, no desde que nos escribes ni desde que recogimos el paquete. Fines de semana y feriados —en origen y en destino— no cuentan. <a href="%H%/tiempos-de-entrega/">Aquí está el detalle</a> y en <a href="%H%/destinos/">cada ficha de destino</a> el plazo de esa ruta.</p>

<h2>Lo que acelera un envío</h2>
<p>No hay truco en el transporte; el margen está en la aduana. Una descripción de contenido precisa y un destinatario con teléfono y número de identificación son lo que hace que el envío pase sin detenerse. Todo eso se decide antes de despachar. <a href="%H%/rastreo-de-envios/">Consulta el estado de tu envío</a>.</p>
',
	),

	/* ─────────────────────────────────────────────────────────────────── */
	'cuanto-demora-un-envio-internacional' => array(
		'titulo'    => 'Cuánto demora un envío internacional',
		'categoria' => 'guias-de-envio',
		'pilar'     => 'tiempos-de-entrega',
		'extracto'  => 'Los cuatro tramos que suman el plazo de un envío, cuál de ellos no controla nadie y qué puedes hacer para que llegue antes.',
		'html'      => '
<p>«¿Cuánto demora?» es la pregunta que más recibimos, y la respuesta honesta empieza por explicar que un envío internacional no es un trayecto: son cuatro tramos encadenados, y el plazo total es la suma.</p>

<h2>Los cuatro tramos</h2>
<ol>
<li><strong>Despacho en {{origen_ciudad}}.</strong> Recepción, pesaje, revisión del contenido, documentación de exportación. Suele resolverse el mismo día si el envío llega listo.</li>
<li><strong>Tramo internacional.</strong> El vuelo o el recorrido por carretera hasta el país de destino. Es el más largo y el que cambia según la vía.</li>
<li><strong>Aduana de destino.</strong> El único tramo cuyo tiempo no controla nadie que no sea la aduana. Un envío bien declarado pasa sin detenerse; uno con dudas se revisa.</li>
<li><strong>Distribución interna.</strong> Del punto de entrada al domicilio o a la agencia del destinatario. Una capital tarda menos que una ciudad del interior.</li>
</ol>

<h2>Cómo se cuentan los días</h2>
<p>Los plazos que publicamos son en <strong>días hábiles desde el despacho</strong>. Tres consecuencias prácticas:</p>
<ul>
<li>Un envío despachado el viernes por la tarde empieza a contar el lunes.</li>
<li>Los feriados de origen y de destino no cuentan, y no siempre coinciden.</li>
<li>El día que nos escribes no es el día del despacho; el despacho es cuando el bulto está aquí, revisado y documentado.</li>
</ul>

<h2>Lo que alarga un envío</h2>
<ul>
<li><strong>Una descripción de contenido vaga.</strong> «Regalos» o «varios» invitan a la revisión en aduana.</li>
<li><strong>Un destinatario incompleto.</strong> Sin teléfono o sin número de identificación, el envío llega y espera.</li>
<li><strong>Un producto que el destino restringe.</strong> Se retiene mientras se aclara, y a veces no se libera.</li>
<li><strong>Temporada alta.</strong> Diciembre y las fechas señaladas saturan las aduanas de todos los países.</li>
</ul>

<h2>Lo que lo acorta</h2>
<p>La vía aérea acorta el tramo internacional, pero no el paso por aduana: si el envío se revisa, se revisa igual. Lo que de verdad acorta el plazo total es lo que se hace antes de despachar: declarar con precisión, dar todos los datos del destinatario y consultar las restricciones del país. <a href="%P:documentos-para-aduana%">Aquí está lo que revisa la aduana</a>.</p>

<h2>El plazo de tu ruta</h2>
<p>Cada destino tiene su plazo estimado por modalidad y su forma de entrega, y todo está en <a href="%H%/destinos/">su ficha</a>. Si tienes una fecha límite, dínosla al cotizar: elegimos la vía que llega y te decimos si el margen es suficiente. <a href="%H%/tiempos-de-entrega/">Tiempos de entrega por destino</a>.</p>
',
	),

	/* ─────────────────────────────────────────────────────────────────── */
	'como-enviar-tus-compras-hechas-en-peru' => array(
		'titulo'    => 'Cómo enviar tus compras hechas en {{origen_pais}}',
		'categoria' => 'guias-de-envio',
		'pilar'     => 'servicios/envio-de-compras',
		'extracto'  => 'Compra en tiendas peruanas desde el extranjero: cómo usar nuestra dirección, consolidar varios pedidos en un envío y qué declarar.',
		'html'      => '
<p>Muchas tiendas de {{origen_pais}} no envían al extranjero, y las que lo hacen cobran un flete por cada pedido. La forma de resolverlo es simple: compras normalmente, usas nuestra dirección de {{origen_ciudad}} como destino de entrega, y cuando llega lo despachamos a tu país. Si compras en varias tiendas, lo juntamos todo en un solo envío.</p>

<h2>Cómo funciona, paso a paso</h2>
<ol>
<li><strong>Compras</strong> en la tienda que quieras y pones como dirección de entrega la de nuestra sede, con tu nombre.</li>
<li><strong>Nos avisas</strong> de qué tiendas esperas paquetes y cuántos, para reservarlos juntos.</li>
<li><strong>Recibimos y revisamos</strong> cada paquete al llegar: que esté completo y sin daños.</li>
<li><strong>Consolidamos</strong>: retiramos el embalaje de tienda —cajas dentro de cajas, relleno de aire— y armamos un solo bulto ajustado al contenido.</li>
<li><strong>Cotizamos y despachamos</strong> con el peso y las medidas reales del bulto final.</li>
</ol>

<h2>Por qué consolidar ahorra</h2>
<p>Dos razones. La primera, obvia: un flete en vez de cinco. La segunda, menos obvia: al retirar los embalajes de tienda el bulto final ocupa menos, y el <a href="%H%/servicios/peso-volumetrico/">peso volumétrico</a> —que es por lo que suele cobrarse la ropa y los productos ligeros— baja con él.</p>

<h2>Qué declarar</h2>
<ul>
<li><strong>El valor real de lo comprado.</strong> Es la base del impuesto en destino y del seguro. Declarar de menos no abarata nada y deja el envío sin cobertura.</li>
<li><strong>Guarda las boletas.</strong> La aduana las pide cuando el contenido es mercancía nueva.</li>
<li><strong>Cada producto con su nombre</strong>, no «compras». La descripción precisa es lo que evita la revisión.</li>
</ul>

<h2>Antes de comprar: ¿tu país lo admite?</h2>
<p>Perfumes, cosméticos, suplementos, alimentos y electrónica con batería tienen reglas propias según el destino y la vía. Comprar algo que la aduana de tu país no deja entrar es perder la compra y el flete. <a href="%H%/que-se-puede-enviar/">Mira qué se puede enviar</a> y <a href="%H%/destinos/">lo específico de tu destino</a> antes de pagar.</p>

<h2>Cuánto tiempo guardamos tus compras</h2>
<p>Las mantenemos en nuestra sede mientras llegan el resto de tus pedidos. Dinos cuántos esperas: cuando estén todos, consolidamos y despachamos en un solo envío. <a href="%H%/servicios/envio-de-compras/">Así funciona el envío de compras</a>.</p>
',
	),

	/* ─────────────────────────────────────────────────────────────────── */
	'envios-recurrentes-de-tu-empresa' => array(
		'titulo'    => 'Envíos recurrentes de tu empresa',
		'categoria' => 'empresas',
		'pilar'     => 'envios-para-empresas',
		'extracto'  => 'Recojo programado, documentación estandarizada, tarifa por volumen y control de costos: cómo dejar de tratar cada envío como el primero.',
		'html'      => '
<p>Una empresa que envía muestras, repuestos, documentos o mercancía al extranjero cada semana no tiene un problema de transporte: tiene un problema de proceso. Cada envío tratado como si fuera el primero cuesta tiempo, errores y dinero. Esto es lo que cambia cuando se organiza.</p>

<h2>1. Un flujo, no un trámite</h2>
<p>Se define una sola vez: quién prepara el envío, qué documentación lleva, cómo se declara, cuándo se recoge. A partir de ahí cada despacho sigue el mismo camino y ya no depende de que la persona que lo hizo la última vez se acuerde.</p>

<h2>2. Recojo programado</h2>
<p>Para envíos frecuentes fijamos días de <a href="%H%/recojo-a-domicilio-lima/">recojo en tu local o almacén</a> dentro de {{origen_ciudad}}, y no hay que pedirlo cada vez. El envío entra en la programación sin llamadas.</p>

<h2>3. Documentación estandarizada</h2>
<p>La mayoría de las retenciones en aduana nacen de una factura mal emitida o de una descripción de producto que cambia de un envío a otro. Con una plantilla de factura comercial y descripciones fijas por producto, el envío número cincuenta pasa igual de limpio que el primero. <a href="%P:documentos-de-exportacion-para-carga%">Aquí está lo que pide la aduana</a>.</p>

<h2>4. Tarifa por volumen</h2>
<p>El precio de un envío puntual no es el precio de cincuenta al mes. Con frecuencia estable trabajamos con tarifa acordada, distinta de la del mostrador, y con facturación consolidada mensual: una factura, no cincuenta.</p>

<h2>5. Elegir la vía por tipo de envío, no por costumbre</h2>
<p>Documentos y muestras pequeñas, por vía aérea. Repuestos voluminosos y reposición de stock hacia países vecinos, por carretera si el plazo lo permite. Una empresa que manda todo por avión «porque siempre se hizo así» paga por rapidez que no necesita. <a href="%P:aereo-o-terrestre%">Cómo elegir la vía</a>.</p>

<h2>6. Un interlocutor</h2>
<p>Un asesor que conoce tus productos, tus destinos y tus plazos, y que resuelve sin que tengas que explicar la empresa desde cero en cada llamada. Es lo que separa un proveedor de envíos de una logística.</p>

<h2>Por dónde empezar</h2>
<p>Sin contratos largos ni volúmenes mínimos: empezamos con tus envíos reales, medimos cuánto se envía y a dónde, y ajustamos el flujo y la tarifa con datos. <a href="%H%/envios-para-empresas/">Envíos para empresas</a> explica cómo se abre la cuenta y qué incluye.</p>
',
	),

	) );
}
