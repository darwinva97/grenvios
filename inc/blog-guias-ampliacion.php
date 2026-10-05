<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Ampliación de las guías generales del blog
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Auditoría de las 95 entradas maestras: mediana de 308 palabras y ninguna por
 * encima de 624. Para las consultas que persiguen —«cuánto cuesta un envío
 * internacional», «qué no se puede enviar»— eso es la mitad de lo que tiene
 * cualquier resultado de la primera página. El texto estaba bien escrito, pero
 * respondía la pregunta y se iba.
 *
 * Este archivo no reescribe nada: AÑADE secciones al final del cuerpo de cada
 * guía a través del filtro `grenvios_guias_contenido`, y una tanda de preguntas
 * frecuentes. El importador las recoge igual que el texto original, así que se
 * propagan solas a las nueve rutas con el bloque por país detrás.
 *
 * Criterio, el mismo de siempre: cada sección nueva resuelve una duda que la
 * guía dejaba abierta —un caso límite, una comparación, un error concreto—,
 * con enlaces a la página que tiene el dato. Nada de párrafos de relleno para
 * subir el contador.
 *
 * Las preguntas van en el cuerpo con marcado propio y, además, salen como
 * FAQPage en el schema de la entrada (inc/blog-faq-schema.php).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Bloque de preguntas al pie de una guía. */
function grenvios_ga_faq( $pares ) {
	$h = '<h2>Preguntas frecuentes</h2><div class="gr-post-faq">';
	foreach ( $pares as $p ) {
		$h .= '<div class="gr-post-faq-item"><h3>' . $p[0] . '</h3><p>' . $p[1] . '</p></div>';
	}
	return $h . '</div>';
}

/* Ampliaciones por guía: HTML que se añade al final del cuerpo. */
function grenvios_ga_ampliaciones() {
	return apply_filters( 'grenvios_ga_ampliaciones', array(

	'como-embalar-un-paquete' => '
<h2>Cómo embalar según lo que envías</h2>
<p>La regla general —caja ajustada, relleno por todas las caras, cinta en H— vale para casi todo. Estos son los casos en los que conviene afinar:</p>
<ul>
<li><strong>Ropa y textil.</strong> Comprimido en bolsa al vacío y dentro de una caja del tamaño resultante. Es el contenido donde más se nota el <a href="%H%/servicios/peso-volumetrico/">peso volumétrico</a>: una caja grande a medio llenar puede duplicar el precio.</li>
<li><strong>Electrónica.</strong> En su caja original si la tienes, y esa caja dentro de otra con relleno alrededor. Nunca pegar cinta directamente sobre la caja del fabricante: en destino cuenta para el valor del producto.</li>
<li><strong>Vidrio y cerámica.</strong> Cada pieza envuelta por separado, nunca tocándose entre sí, y doble caja. Si suena al agitar, no está listo.</li>
<li><strong>Documentos.</strong> Sobre rígido o carpeta de cartón, sin grapas sobre sellos ni apostillas.</li>
<li><strong>Libros.</strong> Pesan mucho para su tamaño: caja pequeña de doble pared, llena. Una caja grande de libros a medio llenar se hunde por el centro.</li>
</ul>

<h2>Los errores que más veces vemos en el mostrador</h2>
<ol>
<li><strong>La caja «por si acaso».</strong> Elegida dos tallas más grande para que quepa todo cómodo. El aire se paga.</li>
<li><strong>Ropa como relleno.</strong> Se comprime con el primer apilado y deja huecos donde el contenido se mueve.</li>
<li><strong>Cinta de oficina.</strong> Se despega con el cambio de temperatura de una bodega de avión.</li>
<li><strong>Etiquetas viejas sin tachar.</strong> Una lectora automática puede leer el código equivocado.</li>
<li><strong>Declarar «varios».</strong> No es un problema de embalaje, pero se decide en el mismo momento y es lo que más retenciones provoca en aduana.</li>
</ol>

<h2>Qué hacemos nosotros al recibir tu paquete</h2>
<p>En nuestra sede de {{origen_ciudad}} —o en el momento del <a href="%H%/recojo-a-domicilio-lima/">recojo a domicilio</a>— abrimos el bulto contigo, revisamos que el contenido coincida con lo declarado, comprobamos que nada de lo que va dentro esté <a href="%H%/que-se-puede-enviar/">restringido en el destino</a> y ajustamos la caja si vemos que estás pagando volumen de más. Tenemos cajas de varios tamaños y material de relleno en el mostrador.</p>
<p>Es el último momento en el que todo tiene arreglo: una vez despachado, el envío viaja como está.</p>
' . grenvios_ga_faq( array(
		array( '¿Puedo usar una caja de supermercado?', 'Si está en buen estado y es del tamaño adecuado, sí. Lo que no sirve es una caja con las esquinas vencidas o ya reutilizada varias veces: cede en el primer apilado. Revísala contigo antes de embalar.' ),
		array( '¿Cuánto relleno es suficiente?', 'Entre tres y cinco centímetros en todas las caras, y que el contenido no toque ninguna pared de la caja. La prueba rápida: cierras, agitas y no debe sonar ni moverse nada.' ),
		array( '¿Tienen cajas y material de embalaje?', 'Sí, en la oficina de {{origen_ciudad}}, en varios tamaños. Elegimos la que ajusta al contenido, que además es la que menos te hace pagar por volumen.' ),
	) ),

	'documentos-para-aduana' => '
<h2>Cómo se describe bien un contenido</h2>
<p>La descripción es el campo que más peso tiene y el que peor se rellena. La diferencia entre un envío que pasa y uno que se abre suele estar en una línea de texto:</p>
<ul>
<li><strong>Mal:</strong> «regalos», «varios», «cosas personales», «ropa».</li>
<li><strong>Bien:</strong> «3 camisetas de algodón para adulto, 1 par de zapatillas de cuero talla 40, 1 libro».</li>
</ul>
<p>Tres reglas: qué es, de qué está hecho o para qué sirve, y cuántas unidades. Si hay varios tipos de producto, se listan por separado con su cantidad. No hace falta escribir una novela: hace falta que quien lea la declaración sepa qué va a encontrar si abre la caja.</p>

<h2>El valor declarado, sin mitos</h2>
<p>Circula la idea de que declarar menos ahorra. En la práctica:</p>
<ul>
<li>La aduana compara con precios de mercado. Un teléfono nuevo declarado en diez dólares se revisa <em>siempre</em>.</li>
<li>El valor declarado es la base del <a href="%H%/seguro-de-envios/">seguro</a>. Si se pierde, te compensan por lo que declaraste, no por lo que vale.</li>
<li>Si se detecta una subvaluación, el envío puede quedar retenido y, en algunos países, generar multa para quien lo recibe.</li>
</ul>
<p>Declarar el valor real y consultar antes <a href="%H%/aduanas-e-impuestos/">el umbral del país de destino</a> es lo que evita el problema: por debajo de ese umbral, muchos envíos no tributan.</p>

<h2>Qué pasa si te falta un papel</h2>
<p>Depende de cuándo se detecte. Si lo vemos en el despacho, en {{origen_ciudad}}, lo resolvemos antes de que el envío salga y no pasa nada. Si aparece en la aduana de destino, el envío queda retenido hasta que se aporte el documento: nosotros gestionamos el trámite, pero el tiempo ya se perdió. De ahí que la revisión previa no sea burocracia nuestra, sino el paso que evita la espera.</p>
' . grenvios_ga_faq( array(
		array( '¿Necesito factura para enviar algo usado?', 'No. La factura se pide para mercancía nueva, porque es lo que sustenta el valor declarado. Para objetos personales usados basta con describirlos y declararles un valor razonable de segunda mano.' ),
		array( '¿Qué documento piden para un regalo?', 'El mismo que para cualquier envío: la declaración de contenido con la descripción y el valor. «Regalo» no es una categoría aduanera: el paquete tributa según su contenido y su valor, lo compre quien lo compre.' ),
		array( '¿Quién rellena la declaración?', 'La preparamos nosotros contigo en el despacho, con lo que nos digas del contenido. Tú aportas la información y la boleta si la mercancía es nueva.' ),
	) ),

	'cuanto-cuesta-un-envio' => '
<h2>Un ejemplo con números</h2>
<p>Dos envíos al mismo país, el mismo día:</p>
<ul>
<li><strong>Caja A:</strong> 30 × 20 × 15 cm con 4 kg de libros. Volumétrico: 9000 ÷ 5000 = 1,8 kg. Peso real: 4 kg. <strong>Se cobra por 4 kg.</strong></li>
<li><strong>Caja B:</strong> 60 × 45 × 40 cm con 4 kg de ropa. Volumétrico: 108 000 ÷ 5000 = 21,6 kg. Peso real: 4 kg. <strong>Se cobra por 21,6 kg.</strong></li>
</ul>
<p>Mismo peso en la balanza, más de cinco veces de diferencia en la factura. La caja B con la ropa comprimida al vacío entra en una de 40 × 30 × 25 cm: volumétrico 6 kg, y se cobraría por 6. El ajuste tarda diez minutos y no cambia lo que envías.</p>

<h2>Lo que no entra en el precio del flete</h2>
<p>Para que la comparación sea honesta, conviene saber qué se suma aparte:</p>
<ul>
<li><strong>El impuesto de la aduana de destino,</strong> cuando la ruta lo tiene. Se calcula sobre el valor declarado y en nuestras rutas se paga aquí, antes de despachar: <a href="%H%/aduanas-e-impuestos/">quien recibe no adelanta dinero</a>.</li>
<li><strong>El <a href="%H%/recojo-a-domicilio-lima/">recojo a domicilio</a>,</strong> si no traes el bulto a la oficina. Depende del distrito.</li>
<li><strong>El tramo interno</strong> si envías <a href="%H%/envios-desde-provincias/">desde provincia</a>, que contratas con la agencia local.</li>
</ul>
<p>Todo eso se te dice antes de decidir, junto con el flete, para que compares el total y no una parte.</p>

<h2>Por qué dos empresas dan precios distintos</h2>
<p>No es solo margen. Cambian tres cosas: el divisor que usan para el peso volumétrico (el estándar aéreo es 5000, y algunos usan otro), si ofrecen vía terrestre —que para bultos voluminosos cambia la cifra por completo— y qué incluye la tarifa: hay quien cotiza barato y suma el despacho, el seguro o la entrega en destino después. Pide siempre el <strong>precio total puesto en destino</strong> y compara eso.</p>
' . grenvios_ga_faq( array(
		array( '¿Hay un peso mínimo?', 'Las rutas suelen tener una tarifa mínima que cubre el despacho y la documentación, así que un envío de 200 gramos y uno de 1 kg pueden costar parecido. Por eso, si vas a mandar varias cosas, conviene juntarlas en un solo envío.' ),
		array( '¿El precio cambia según la ciudad de destino?', 'Dentro de un mismo país, el tramo internacional es el mismo; lo que puede variar es la distribución interna hasta una ciudad del interior. Te lo confirmamos al cotizar con la dirección exacta.' ),
		array( '¿Cuánto tardan en darme el precio?', 'Minutos, si nos das peso, medidas del bulto cerrado y ciudad de destino. Sin esos tres datos cualquier cifra es una estimación que cambiará en el mostrador.' ),
	) ),

	'como-calcular-el-peso-volumetrico' => '
<h2>Tabla rápida de cajas frecuentes</h2>
<p>Para que no tengas que hacer la cuenta cada vez, estos son los pesos volumétricos de los tamaños más usados:</p>
<ul>
<li>30 × 20 × 15 cm → <strong>1,8 kg</strong></li>
<li>40 × 30 × 20 cm → <strong>4,8 kg</strong></li>
<li>40 × 40 × 40 cm → <strong>12,8 kg</strong></li>
<li>50 × 40 × 30 cm → <strong>12 kg</strong></li>
<li>60 × 40 × 40 cm → <strong>19,2 kg</strong></li>
<li>60 × 50 × 40 cm → <strong>24 kg</strong></li>
</ul>
<p>Si el peso real de tu contenido está por debajo de esa cifra, estás pagando volumen: ahí es donde conviene bajar de tamaño de caja.</p>

<h2>Casos en los que no aplica el volumétrico</h2>
<p>Cuando el contenido es denso, manda siempre el peso real: libros, repuestos metálicos, herramientas, latas, líquidos permitidos por carretera. En esos envíos no ganas nada comprimiendo, y lo que conviene es lo contrario —una caja de doble pared, bien llena y sin espacio para que el contenido se desplace— porque el riesgo no es el precio, es que se rompa.</p>

<h2>Cómo medir un bulto que no es una caja</h2>
<p>Una maleta, un bulto envuelto, algo irregular: se toma la <strong>medida máxima de cada dimensión</strong>, incluyendo ruedas, asas y cualquier abultamiento. No se mide la parte más estrecha ni se «redondea hacia abajo»: en el mostrador se mide como se cobra, y es mejor saberlo antes.</p>
' . grenvios_ga_faq( array(
		array( '¿Por qué se divide entre 5000?', 'Es el factor estándar en envíos internacionales aéreos: equivale a decir que un metro cúbico se cobra como 200 kilos. Es el que aplicamos; lo que cambia entre empresas es la tarifa por kilo, no la fórmula.' ),
		array( '¿El peso volumétrico también aplica por carretera?', 'Sí, pero pesa menos en la cuenta: el camión tiene más tolerancia al volumen que la bodega de un avión. Por eso los bultos grandes y ligeros salen mejor por vía terrestre cuando el destino la tiene.' ),
		array( '¿Puedo llevar el contenido y que ustedes elijan la caja?', 'Es lo que recomendamos. Traes las cosas, elegimos la caja que ajusta y te decimos en el momento por cuál de los dos pesos se cobra.' ),
	) ),

	'aereo-o-terrestre' => '
<h2>Un caso práctico</h2>
<p>Una caja de 50 × 40 × 30 cm con 6 kg de ropa y utensilios hacia un país vecino. El volumétrico es 12 kg, así que se cobra por 12 en las dos vías. La diferencia está en la tarifa por kilo y en el plazo: la aérea llega en cuestión de días y se paga por ello; la terrestre tarda más y para ese volumen suele costar bastante menos. Si la caja llevara documentos o algo urgente, ni se plantea: avión.</p>
<p>La pregunta útil no es «¿cuál es mejor?», sino <strong>«¿cuántos días tengo?»</strong>. Con la fecha límite encima de la mesa, la elección se hace sola.</p>

<h2>Lo que cambia en el contenido admitido</h2>
<p>Es la parte que más sorprende: no es solo cuestión de precio y plazo. La bodega de un avión no admite líquidos, aerosoles, alimentos ni artículos con batería interna, y por carretera varios de esos productos sí viajan según el país. Si lo que envías está en esa lista, <a href="%H%/que-se-puede-enviar/">la vía puede decidirla el contenido</a> y no tú.</p>

<h2>Y si el destino solo tiene una vía</h2>
<p>Hacia Norteamérica, Europa y el Caribe operamos por vía aérea: no hay ruta por carretera. En esos casos la pregunta cambia y pasa a ser cómo reducir el volumen, porque el volumen es lo que más pesa en la tarifa aérea. Comprimir lo blando, ajustar la caja y consolidar varios pedidos en un bulto es lo que abarata. <a href="%H%/destinos/">Cada ficha de destino</a> indica qué vías tiene.</p>
' . grenvios_ga_faq( array(
		array( '¿Puedo cambiar de vía después de cotizar?', 'Sí, mientras el envío no se haya despachado. Te damos el precio y el plazo de las dos opciones cuando el destino las tiene, y decides con los dos números delante.' ),
		array( '¿La vía terrestre es menos segura?', 'No. Cambian el plazo y la frecuencia con la que se actualiza el seguimiento, no el cuidado del envío. La cobertura sobre el valor declarado es la misma.' ),
		array( '¿Qué vía usan para documentos?', 'Aérea siempre. Pesan poco, así que el sobreprecio es mínimo y llegan bastante antes.' ),
	) ),

	'que-no-se-puede-enviar' => '
<h2>Los productos que más consultas generan</h2>
<ul>
<li><strong>Perfumes y cosméticos.</strong> Son líquidos y alcohol: por vía aérea, no. Por carretera, según el país y siempre sellados de fábrica.</li>
<li><strong>Celulares, laptops y relojes inteligentes.</strong> La batería de litio interna es lo que los frena en bodega de avión. Por carretera se admiten en varios destinos.</li>
<li><strong>Suplementos y vitaminas.</strong> Dependen del control sanitario del país de destino más que de la vía. Consúltalo con el nombre exacto del producto.</li>
<li><strong>Prendas de marca.</strong> Sin problema en cantidades personales; en cantidad comercial pasan a tratarse como mercancía y pueden requerir documentación.</li>
<li><strong>Herramientas y repuestos.</strong> Se admiten; lo que hay que cuidar es la descripción, porque una vaga («piezas») invita a revisión.</li>
</ul>

<h2>Cómo consultar para que la respuesta sirva</h2>
<p>«¿Puedo enviar esto?» casi nunca tiene una respuesta directa. Con estos tres datos, sí:</p>
<ol>
<li><strong>Nombre exacto del producto</strong> —marca y modelo si es electrónica, principio activo si es medicina—.</li>
<li><strong>País de destino.</strong></li>
<li><strong>Vía</strong>, si el destino tiene las dos.</li>
</ol>
<p>Con eso te decimos sí o no, y si es no, si hay alternativa. Es una consulta de dos minutos por WhatsApp que evita comprar algo que no va a poder salir.</p>

<h2>Qué ocurre si algo prohibido llega a la aduana</h2>
<p>Tres desenlaces posibles, ninguno bueno: retención hasta aportar un permiso que muchas veces no existe para particulares, devolución al remitente pagando el flete de vuelta, o destrucción. En los tres casos el flete de ida no se devuelve. Por eso revisamos el contenido en el despacho: preferimos una conversación incómoda en {{origen_ciudad}} a una pérdida en destino.</p>
' . grenvios_ga_faq( array(
		array( '¿Puedo enviar un celular a mi familia?', 'Por vía aérea no, porque lleva batería de litio interna. Por vía terrestre se admite hacia varios destinos; dinos el país y te confirmamos si entra y con qué condiciones.' ),
		array( '¿Y medicamentos con receta?', 'Depende del país de destino y de la vía. Donde se admiten, siempre van acompañados de la receta médica. Consúltanos con el nombre del medicamento antes de comprarlo.' ),
		array( '¿Qué pasa con las baterías sueltas?', 'Las baterías de litio sueltas no viajan por vía aérea en ningún caso. Dentro del aparato, tampoco en bodega. Es una restricción de seguridad aérea, no una política nuestra.' ),
	) ),

	'apostilla-para-enviar-documentos' => '
<h2>Documentos que más se apostillan</h2>
<ul>
<li><strong>Académicos:</strong> títulos, diplomas, certificados de notas, constancias de egreso y sílabos para convalidaciones.</li>
<li><strong>Civiles:</strong> partidas de nacimiento, matrimonio y defunción, certificados de soltería.</li>
<li><strong>Penales y policiales:</strong> antecedentes, muy pedidos en trámites de residencia y visado.</li>
<li><strong>Laborales:</strong> certificados de trabajo, colegiaturas profesionales.</li>
<li><strong>Notariales:</strong> poderes, escrituras, declaraciones juradas.</li>
</ul>
<p>Cada uno tiene su vía de obtención y su entidad emisora, y ese paso es previo a todo lo demás: sin el documento válido en la mano, no hay apostilla que valga.</p>

<h2>Errores que obligan a empezar de nuevo</h2>
<ol>
<li><strong>Traducir antes de apostillar.</strong> La traducción debe incluir la apostilla; hacerlo al revés obliga a traducir otra vez.</li>
<li><strong>Apostillar una fotocopia simple.</strong> Se apostilla el original o una copia certificada, no una fotocopia cualquiera.</li>
<li><strong>Laminar o plastificar el documento.</strong> Lo invalida para el trámite.</li>
<li><strong>Grapar sobre el sello.</strong> Parece menor y no lo es: puede anular la certificación.</li>
<li><strong>Dejarlo para el final.</strong> El trámite tiene su propio plazo y no se acelera pagando el envío más rápido.</li>
</ol>

<h2>Cuando el documento va acompañado</h2>
<p>Es habitual que el documento apostillado viaje junto a otras cosas —una carpeta con copias, un par de fotos, algún objeto personal—. En cuanto hay algo más que papel, el envío deja de ser «solo documentos» y pasa a declararse como lo que lleva. Conviene decidirlo antes: si el documento es urgente, va solo y por vía aérea; lo demás puede ir después en otro envío, normalmente más barato. <a href="%H%/servicios/envio-internacional-de-documentos/">Cómo se declara un envío de documentos</a>.</p>
' . grenvios_ga_faq( array(
		array( '¿La apostilla caduca?', 'La apostilla en sí no tiene fecha de vencimiento, pero muchas instituciones exigen que el documento de base tenga una antigüedad máxima —tres o seis meses es lo habitual en antecedentes y partidas—. Pregunta el requisito en destino antes de iniciar el trámite.' ),
		array( '¿Puedo apostillar un documento de otra persona?', 'Sí, normalmente se puede gestionar con una autorización. Lo que no se puede es apostillar un documento que no sea original o copia certificada válida.' ),
		array( '¿Ustedes hacen el trámite completo?', 'Gestionamos la legalización, la apostilla y la traducción oficial, y despachamos el documento en el mismo flujo, sin que tengas que ir de una oficina a otra.' ),
	) ),

	'como-enviar-un-documento-urgente' => '
<h2>Cuánto margen necesitas de verdad</h2>
<p>Para una fecha límite conviene contar hacia atrás con tres bloques, no con uno:</p>
<ol>
<li><strong>Trámite previo</strong> (apostilla, traducción), si aplica. Corre aquí y es el más difícil de acelerar.</li>
<li><strong>Tránsito internacional</strong>, en días hábiles desde el despacho.</li>
<li><strong>Entrega y presentación</strong>: que el documento llegue no es que esté presentado. Deja un par de días para que quien lo recibe pueda hacer el trámite.</li>
</ol>
<p>El error clásico es calcular solo el segundo bloque. <a href="%H%/tiempos-de-entrega/">Aquí explicamos cómo se cuentan los días</a>, que también sorprende: un envío despachado el viernes por la tarde empieza a contar el lunes.</p>

<h2>Si el plazo ya está encima</h2>
<ul>
<li><strong>Manda una copia digital de inmediato.</strong> Muchas instituciones aceptan un escaneo para iniciar el trámite mientras llega el original.</li>
<li><strong>Comparte el número de guía</strong> con quien lo recibe: si el repartidor no encuentra la dirección, resuelve él, no tú, a miles de kilómetros.</li>
<li><strong>Confirma la dirección de entrega.</strong> Una oficina con horario limitado o una universidad en vacaciones puede costar más días que el vuelo.</li>
</ul>

<h2>Qué NO acelera un documento</h2>
<p>Conviene decirlo claro para no gastar dinero en balde: pagar más no acelera la aduana, y un documento bien declarado no necesita que se acelere. Lo que de verdad ahorra días es haber hecho el trámite antes, haber declarado con precisión y tener el teléfono correcto del destinatario. Todo eso es gratis y se decide antes de despachar.</p>
' . grenvios_ga_faq( array(
		array( '¿Puedo enviar el documento un sábado?', 'Atendemos sábados por la mañana y recibimos el envío, pero el despacho entra en la programación del siguiente día hábil. Para una fecha ajustada, el viernes por la mañana es mejor que el sábado.' ),
		array( '¿Qué pasa si el destinatario no está?', 'Depende del país: en rutas con entrega a domicilio se reintenta o se deja aviso; en rutas con retiro en agencia, el envío queda disponible allí. Por eso pedimos siempre un teléfono de contacto en destino.' ),
		array( '¿Conviene enviar copia y original por separado?', 'Para trámites críticos, sí: el escaneo por correo electrónico adelanta el proceso y el original va por su vía. Nunca dividas un expediente en dos envíos físicos distintos.' ),
	) ),

	'documentos-de-exportacion-para-carga' => '
<h2>Cómo se describe una partida</h2>
<p>La descripción por partida es lo que la aduana usa para clasificar la mercancía y decidir el arancel. Una descripción pobre no ahorra trabajo: lo multiplica.</p>
<ul>
<li><strong>Mal:</strong> «repuestos», «textiles», «productos varios».</li>
<li><strong>Bien:</strong> «filtros de aceite para motor diésel, referencia X, 24 unidades, material: papel y metal».</li>
</ul>
<p>Tres datos por partida: qué es, de qué material y cuántas unidades. Si repites esa misma descripción en todos los envíos del mismo producto, la aduana de destino te reconoce y deja de revisar.</p>

<h2>Incoterms, en una línea</h2>
<p>El término comercial define quién paga qué y hasta dónde llega tu responsabilidad. No hace falta dominarlos todos, pero sí tener claro con tu cliente <strong>quién asume el flete, el seguro y los impuestos de importación</strong> antes de cerrar el precio de venta. Muchas discusiones posteriores nacen de no haberlo dicho por escrito.</p>

<h2>Errores que cuestan una semana de almacenaje</h2>
<ol>
<li><strong>Factura y lista de empaque que no cuadran.</strong> Si la factura dice 24 unidades y la lista 20, se abre todo.</li>
<li><strong>Valor sin moneda</strong> o con moneda distinta a la del contrato.</li>
<li><strong>Falta del número fiscal del importador.</strong> En muchos países el despacho no arranca sin él.</li>
<li><strong>Producto que necesitaba permiso sectorial</strong> y nadie lo tramitó: es el que más días cuesta, porque se resuelve fuera de la aduana.</li>
</ol>
<p>Revisamos los cuatro puntos antes de que la carga salga de {{origen_ciudad}}. Para empresas que exportan de forma recurrente, <a href="%H%/envios-para-empresas/">fijamos plantillas</a> para que el envío número cincuenta se despache igual de limpio que el primero.</p>
' . grenvios_ga_faq( array(
		array( '¿Necesito ser exportador registrado?', 'Para envíos comerciales de bajo valor suele bastar con el RUC de la empresa y la factura. A partir de ciertos montos o productos, el trámite cambia: te decimos cuál aplica a tu caso antes de despachar.' ),
		array( '¿Quién paga los impuestos de importación?', 'Lo define lo que acuerdes con tu cliente. Conviene dejarlo por escrito en la cotización: es el motivo más común de discusión cuando la mercancía ya está en destino.' ),
		array( '¿Pueden consolidar mercancía de varios proveedores?', 'Sí. Recibimos en nuestra sede los bultos de cada proveedor, los consolidamos y despachamos un solo envío con una sola documentación.' ),
	) ),

	'como-enviar-equipaje-por-delante' => '
<h2>Qué llevar en cabina, qué facturar y qué enviar</h2>
<ul>
<li><strong>Cabina:</strong> documentos, medicinas con receta, electrónica, objetos de valor, lo imprescindible de dos días.</li>
<li><strong>Facturado en el avión:</strong> lo que vas a necesitar la primera semana.</li>
<li><strong>Enviado por delante:</strong> ropa de otra temporada, libros, ropa de cama, utensilios, todo lo que puede esperar.</li>
</ul>
<p>La regla práctica: si no lo vas a echar en falta en dos semanas, va por delante. Lo que sí conviene es <strong>enviar antes de viajar</strong>, no después: así el envío viaja mientras tú resuelves el traslado.</p>

<h2>Inventario: el paso que casi nadie hace</h2>
<p>Antes de cerrar cada bulto, escribe qué va dentro y fotografíalo abierto. Sirve para tres cosas: declarar con precisión —que es lo que evita la revisión en aduana—, saber en qué caja está cada cosa al llegar, y tener prueba si hay que reclamar. Numera las cajas y guarda la lista en el teléfono.</p>

<h2>Equipaje usado frente a mercancía nueva</h2>
<p>El equipaje personal usado suele tener un trato aduanero distinto al de la mercancía nueva, pero cada país fija sus propios límites. Dos consejos que aplican casi siempre: <strong>no mezcles</strong> productos nuevos con etiqueta dentro de las cajas de equipaje —convierte el envío en una importación comercial— y <strong>declara cada bulto por separado</strong>, no como un lote único. <a href="%H%/aduanas-e-impuestos/">Cómo funciona la aduana</a>.</p>
' . grenvios_ga_faq( array(
		array( '¿Cuántas cajas puedo enviar?', 'Las que necesites. Se cotiza por el conjunto y, a partir de varios bultos, suele salir mejor que el exceso de equipaje de la aerolínea. Dinos cuántas son y sus medidas.' ),
		array( '¿Y si viajo antes de que llegue?', 'No hay problema, pero deja a alguien que pueda recibir o retirar el envío en destino, con su teléfono como contacto. En rutas con retiro en agencia, esa persona necesitará su documento de identidad.' ),
		array( '¿Puedo enviar una maleta con ruedas tal cual?', 'Sí, se envía como bulto. Conviene envolverla o protegerla, porque en tránsito se maneja como cualquier caja. Y se mide con ruedas y asas incluidas.' ),
	) ),

	'como-rastrear-tu-envio' => '
<h2>Cuánto tiempo es normal en cada estado</h2>
<ul>
<li><strong>Recibido y despachado:</strong> el mismo día o el siguiente hábil, si el envío llegó listo.</li>
<li><strong>En tránsito internacional:</strong> lo que marque el plazo de la ruta. Por vía terrestre puede pasar varios días sin actualizarse y ser completamente normal.</li>
<li><strong>En aduana:</strong> lo habitual es que pase sin detenerse. Si se alarga más de unos días hábiles, suele haber algo documental que resolver, y te avisamos nosotros antes de que preguntes.</li>
<li><strong>En reparto o disponible:</strong> de horas a un par de días, según la ciudad.</li>
</ul>
<p>La regla para no preocuparse de más: compara con el <a href="%H%/tiempos-de-entrega/">plazo estimado de tu ruta</a>. Mientras no se haya cumplido, lo normal es que todo vaya bien aunque el estado no cambie.</p>

<h2>Qué hacer si algo se sale de lo normal</h2>
<ol>
<li><strong>Ten a mano el número de guía</strong> y la fecha de despacho.</li>
<li><strong>Escríbenos por WhatsApp</strong> en vez de esperar. Consultamos directamente con el operador del tramo donde está.</li>
<li><strong>Si es aduana,</strong> normalmente falta un dato: una descripción más precisa, una factura o el número de identificación del destinatario. Se aporta y sigue.</li>
<li><strong>Si es entrega,</strong> casi siempre es un teléfono que no contesta o una dirección incompleta. Con el contacto correcto se resuelve en el siguiente intento.</li>
</ol>

<h2>Lo que el seguimiento no dice</h2>
<p>El estado cuenta dónde está el envío, no en qué condiciones. Por eso insistimos tanto en el embalaje y en las fotos del contenido antes de cerrar la caja: si hay que abrir un reclamo, esa es la documentación que lo sostiene, no el historial de seguimiento. <a href="%H%/seguro-de-envios/">Qué cubre el seguro</a>.</p>
' . grenvios_ga_faq( array(
		array( '¿El seguimiento es en tiempo real?', 'Se actualiza en cada hito del trayecto —despacho, salida, llegada, aduana, reparto—, no minuto a minuto. Entre hito e hito puede haber días sin cambios, sobre todo por vía terrestre.' ),
		array( '¿Mi destinatario puede consultar el estado?', 'Sí, comparte con él el número de guía. De hecho conviene: si hay un intento de entrega fallido, él puede resolverlo mucho más rápido que tú.' ),
		array( '¿Qué hago si el estado dice «entregado» y no lo recibió?', 'Escríbenos de inmediato con el número de guía. Se solicita la prueba de entrega al operador de destino, que incluye fecha, hora y quién firmó.' ),
	) ),

	'cuanto-demora-un-envio-internacional' => '
<h2>Las fechas en las que todo tarda más</h2>
<ul>
<li><strong>Diciembre y principios de enero.</strong> Es el pico del año en todas las aduanas: no es que tu envío vaya lento, es que van lentos todos.</li>
<li><strong>Semana Santa y fiestas patrias</strong>, en origen y en destino, y no siempre coinciden en el calendario.</li>
<li><strong>Cambios normativos en aduana.</strong> Puntuales, pero cuando ocurren afectan a todo lo que esté en tránsito.</li>
</ul>
<p>Para esas fechas, la regla es sencilla: plazo de la ruta más una semana de colchón, y despachar antes en lugar de pagar más rápido después.</p>

<h2>Cómo pedir una fecha de entrega realista</h2>
<p>Dinos la fecha límite al cotizar, no después. Con ella elegimos la vía que llega, te decimos si el margen alcanza y, si no alcanza, lo decimos claro en vez de prometerlo. Un plazo estimado es eso —una estimación basada en cómo se comporta la ruta—, y la aduana es el tramo que ninguna empresa de envíos controla, por mucho que algunas lo vendan como si sí.</p>

<h2>Por qué dos envíos al mismo país tardan distinto</h2>
<p>Pasa y tiene explicación: ciudad de destino distinta —una capital no es una ciudad del interior—, vía distinta, una descripción de contenido más precisa que la otra, o simplemente que uno cayó en una revisión aleatoria de aduana. De los cuatro factores, tres los controlas tú antes de despachar. <a href="%G:documentos-para-aduana%">Declarar bien</a> es lo que más consistencia da a los plazos.</p>
' . grenvios_ga_faq( array(
		array( '¿Me pueden garantizar una fecha exacta?', 'Damos un plazo estimado por ruta y modalidad, que es lo que se puede sostener con honestidad: el paso por aduana no lo controla ninguna empresa de envíos. Si tienes fecha límite, la tenemos en cuenta al elegir la vía.' ),
		array( '¿Los días que dan incluyen el fin de semana?', 'No. Son días hábiles contados desde el despacho, y los feriados de origen y de destino tampoco cuentan.' ),
		array( '¿Puedo acelerar un envío que ya salió?', 'Una vez despachado, el envío sigue la programación de su vía. Lo que sí se puede es resolver rápido cualquier incidencia documental para que no sume días en aduana.' ),
	) ),

	'como-enviar-tus-compras-hechas-en-peru' => '
<h2>Cómo indicar nuestra dirección en la tienda</h2>
<p>Al comprar, pon nuestra dirección de {{origen_ciudad}} como destino de entrega y <strong>tu nombre como destinatario</strong>, no el nuestro: así identificamos de quién es cada paquete cuando llega. Si la tienda pide un teléfono de contacto, puedes poner el nuestro, y si permite añadir una referencia, escribe tu nombre completo otra vez.</p>
<p>Avísanos de qué tiendas esperas paquetes y cuántos. Con eso los reservamos juntos en vez de despachar el primero que llegue.</p>

<h2>Qué revisamos al recibir cada paquete</h2>
<ul>
<li>Que llegue <strong>completo y sin daños</strong>, antes de que se pierda el plazo de reclamo con la tienda.</li>
<li>Que el contenido <strong>pueda salir hacia tu país</strong>: es el momento de detectar un perfume o un aparato con batería que la vía no admite.</li>
<li>Que el <strong>embalaje de tienda</strong> se pueda retirar para consolidar sin dañar el producto.</li>
</ul>

<h2>Productos peruanos que más se piden</h2>
<p>Lo que más consolidamos: ropa y calzado de marcas locales, textiles de alpaca, artesanía, café y cacao envasados, productos de belleza y repuestos difíciles de conseguir fuera. Ojo con los alimentos y los cosméticos: <a href="%H%/que-se-puede-enviar/">tienen reglas propias según el destino y la vía</a>, y conviene consultarlo antes de pagar, no después de que llegue a nuestra sede.</p>
' . grenvios_ga_faq( array(
		array( '¿Cuánto tiempo guardan mis compras?', 'Las mantenemos mientras llegan el resto de tus pedidos. Dinos cuántos esperas y desde qué tiendas; cuando estén todos, consolidamos y despachamos.' ),
		array( '¿Qué pasa si una tienda envía algo equivocado?', 'Lo detectamos al revisar el paquete y te avisamos de inmediato, que es cuando todavía estás a tiempo de reclamar a la tienda. Por eso abrimos y revisamos cada paquete al recibirlo.' ),
		array( '¿Puedo comprar y que ustedes paguen?', 'No gestionamos la compra ni el pago: tú compras y nosotros recibimos, consolidamos y enviamos. Así el comprobante queda a tu nombre, que es lo que sustenta el valor declarado.' ),
	) ),

	'envios-recurrentes-de-tu-empresa' => '
<h2>Qué medir antes de negociar una tarifa</h2>
<p>Para que una tarifa por volumen tenga sentido hay que saber qué se envía de verdad. Cuatro datos de los últimos tres meses:</p>
<ul>
<li><strong>Número de envíos al mes</strong> y su distribución (¿picos o constante?).</li>
<li><strong>Peso y volumen medios</strong> por envío: es lo que decide si te conviene aérea o terrestre.</li>
<li><strong>Destinos</strong> y su reparto.</li>
<li><strong>Incidencias:</strong> cuántos envíos se retuvieron y por qué.</li>
</ul>
<p>Con eso se arma una propuesta con números en vez de una lista de precios genérica. Si no los tienes, los medimos nosotros durante las primeras semanas.</p>

<h2>Quién hace qué dentro de tu empresa</h2>
<p>Un proceso de envíos falla casi siempre por lo mismo: nadie es el responsable. Conviene definir tres roles, aunque recaigan en la misma persona: <strong>quién prepara</strong> el bulto y la documentación, <strong>quién autoriza</strong> el gasto y <strong>quién resuelve</strong> cuando hay una incidencia en destino. Con eso, el envío deja de depender de quién esté ese día.</p>

<h2>Errores que encarecen sin que se note</h2>
<ol>
<li><strong>Enviar de a uno</strong> lo que podría ir consolidado semanalmente.</li>
<li><strong>Usar siempre la vía aérea</strong> por costumbre, también en reposiciones que no urgen.</li>
<li><strong>Cajas estándar</strong> para productos que no las llenan: volumen que se paga en cada envío.</li>
<li><strong>Documentación improvisada</strong> en cada despacho: es la causa número uno de retenciones, y una retención cuesta más que cualquier ahorro de tarifa.</li>
</ol>
<p><a href="%H%/servicios/carga-internacional/">Carga internacional</a> · <a href="%H%/envios-para-empresas/">Cómo trabajamos con empresas</a>.</p>
' . grenvios_ga_faq( array(
		array( '¿Emiten factura a nombre de la empresa?', 'Sí, factura electrónica a nombre de la empresa, y para envíos recurrentes consolidamos el mes en una sola factura con el detalle de cada despacho.' ),
		array( '¿Pueden trabajar contra orden de compra?', 'Sí. Coordinamos el flujo con tu área de compras y adaptamos la facturación a vuestro proceso interno.' ),
		array( '¿Qué pasa si un envío se retiene en aduana?', 'Lo gestionamos nosotros y te informamos de qué falta. Para empresas, además, revisamos después qué lo causó y ajustamos la plantilla de documentación para que no se repita.' ),
	) ),

	) );
}

/* Se añaden al final del cuerpo de cada guía, antes de importar. */
add_filter( 'grenvios_guias_contenido', function ( $guias ) {
	foreach ( grenvios_ga_ampliaciones() as $slug => $extra ) {
		if ( ! isset( $guias[ $slug ] ) ) continue;
		if ( strpos( $guias[ $slug ]['html'], 'gr-post-faq' ) !== false ) continue;   // ya ampliada
		$guias[ $slug ]['html'] = rtrim( $guias[ $slug ]['html'] ) . "\n" . $extra;
	}
	return $guias;
}, 20 );
