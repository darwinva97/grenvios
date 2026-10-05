<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Contenido propio para las páginas delgadas de la ruta principal
 * ══════════════════════════════════════════════════════════════════════════
 *
 * AUDITORÍA DE LAS 26 PÁGINAS DE LA RUTA PRINCIPAL (palabras de contenido,
 * sin cabecera ni pie). Nada duplicado en títulos ni descripciones, H1 único
 * en todas: la base técnica estaba bien. El problema era de volumen:
 *
 *     240  /servicios/envio-de-compras/        ← servicio, delgadísima
 *     264  /recojo-a-domicilio-lima/
 *     361  /como-enviar-un-paquete-al-extranjero/  ← guía pilar
 *     369  /seguro-de-envios/
 *     373  /destinos/                          ← hub de nueve fichas
 *     376  /envios-desde-provincias/
 *     387  /servicios/                         ← pilar de servicios
 *     408  /tiempos-de-entrega/
 *     420  /servicios/carga-internacional/
 *     441  /cotizar/
 *     506  /rastreo-de-envios/
 *     510  /servicios/envio-de-alimentos/
 *
 * En las rutas de país estas mismas páginas reciben el bloque por país y se
 * defienden; en la principal —la que compite por las consultas genéricas, las
 * más buscadas— se quedaban en la mitad de lo que tiene cualquier competidor.
 *
 * Aquí cada una recibe una sección propia y sus preguntas frecuentes, que
 * además alimentan el FAQPage del schema. Criterio de redacción:
 *
 *   · Responder a lo que se busca, no rellenar. Cada sección resuelve una duda
 *     concreta que la página dejaba sin contestar.
 *   · Cero datos inventados: peso volumétrico ÷ 5000, vías aérea y terrestre,
 *     recojo en {{origen_ciudad}}, seguimiento por guía y paso por aduana son
 *     los que el sitio ya afirma. Plazos y porcentajes concretos salen del
 *     gestor de destinos, no de aquí.
 *   · Enlaces con ancla descriptiva hacia la página que resuelve el siguiente
 *     paso, para que el contenido también reparta autoridad.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Secciones por página: sub, título y cuerpo. %H% = raíz del sitio.
 * Filtro `grenvios_pseo_secciones` para añadir o cambiar sin tocar el tema. */
function grenvios_pseo_secciones() {
	static $m = null;
	if ( $m !== null ) return $m;

	return $m = apply_filters( 'grenvios_pseo_secciones', array(

		'servicios' => array(
			'sub'   => 'Cómo elegir',
			'titulo' => 'Qué servicio te conviene según <span class="hl">lo que envías</span>',
			'html'  => '<p>La diferencia entre un servicio y otro no está en la ruta —el avión y el camión son los mismos—, sino en el <strong>embalaje, la documentación y la forma de cobrar</strong>. Por eso conviene elegir bien antes de cotizar.</p>
<ul class="gr-pseo-list">
<li><strong>Documentos.</strong> Contratos, títulos, partidas, poderes. Pesan poco, viajan por vía aérea y son el envío más económico del catálogo. Si además tienen que surtir efecto legal fuera del país, necesitan <a href="%H%/servicios/apostilla-y-traduccion/">apostilla y traducción</a>.</li>
<li><strong>Paquetes.</strong> Ropa, regalos, repuestos, compras. Aquí manda el <a href="%H%/servicios/peso-volumetrico/">peso volumétrico</a>: una caja grande y liviana puede costar más que una pequeña y pesada.</li>
<li><strong>Carga.</strong> A partir de volúmenes comerciales cambia el criterio: se cotiza el conjunto, se consolida y se despacha con documentación de exportación. Es el terreno de <a href="%H%/envios-para-empresas/">las empresas que envían de forma recurrente</a>.</li>
<li><strong>Casos especiales.</strong> <a href="%H%/servicios/envio-de-equipaje/">Equipaje y mudanzas</a>, <a href="%H%/servicios/envio-de-compras/">compras hechas en {{origen_pais}}</a> y <a href="%H%/servicios/envio-de-alimentos/">alimentos</a> tienen reglas propias de aduana según el destino.</li>
</ul>
<p>Si dudas entre dos, dinos qué envías y a dónde: te decimos cuál sale mejor de precio y cuál llega antes. No siempre es el mismo.</p>',
		),

		'carga-internacional' => array(
			'sub'   => 'Antes de mover carga',
			'titulo' => 'Lo que define el precio de una <span class="hl">carga internacional</span>',
			'html'  => '<p>En carga el precio deja de mirar el bulto suelto y empieza a mirar el conjunto: <strong>peso total, volumen ocupado y tipo de mercancía</strong>. Se sigue aplicando el criterio de peso real frente a volumétrico, pero sobre el consolidado, y ahí la vía terrestre suele ganar por goleada cuando la mercancía abulta y la fecha no aprieta.</p>
<p>La documentación es la otra mitad del trabajo. La mayoría de los envíos que se quedan retenidos no fallan por el transporte, sino por una factura mal emitida o una descripción de contenido que no coincide con lo que va dentro. Revisamos ambas antes de que la carga salga de {{origen_ciudad}}: <a href="%H%/aduanas-e-impuestos/">cada aduana tiene sus propios límites de valor y sus restricciones</a>, y no son los mismos de un país a otro.</p>
<ul class="gr-pseo-list">
<li><strong>Consolidamos.</strong> Varios bultos o varios proveedores en un solo despacho: un flete en vez de cinco.</li>
<li><strong>Recogemos en origen.</strong> En tu almacén o en el de tu proveedor dentro de {{origen_ciudad}}, con <a href="%H%/recojo-a-domicilio-lima/">recojo programado</a> si los envíos son recurrentes.</li>
<li><strong>Seguimos el despacho.</strong> Te avisamos en cada tramo, incluido el paso por aduana, que es el único que no controlamos.</li>
</ul>',
		),

		/* Antes repetía la explicación de la página («Compra en Perú aunque no
		 * estés aquí»). Ahora es un ejemplo con números de la consolidación. */
		'envio-de-compras' => array(
			'sub'   => 'Un ejemplo con números',
			'titulo' => 'Cinco pedidos, <span class="hl">un solo envío</span>',
			'html'  => '<p>Así se ve la consolidación con cinco compras de ropa y calzado hechas en tiendas de {{origen_ciudad}}. Cada tienda despacha su pedido en su propia caja, con relleno de aire y el calzado dentro de su caja de cartón.</p>
<ul class="gr-pseo-list">
<li><strong>Enviadas por separado:</strong> cinco cajas de 30 × 25 × 15 cm con 1,5 kg cada una. Cada caja se cobra por su <a href="%H%/servicios/peso-volumetrico/">peso volumétrico</a>, 2,25 kg, así que se pagan 11,25 kg repartidos en cinco despachos.</li>
<li><strong>Consolidadas:</strong> retiramos el embalaje de cada tienda y armamos una sola caja de 45 × 35 × 30 cm con los 7,5 kg de contenido. Su volumétrico es 9,45 kg, así que se cobran 9,45 kg en un solo despacho.</li>
</ul>
<p>La diferencia de peso parece pequeña. La grande está en pagar un despacho en vez de cinco. Por eso conviene avisarnos de todas las compras antes de que lleguen: esperamos al último pedido y salen juntas. <a href="%H%/cotizar/">Cotiza con la lista de lo que vas a comprar</a>.</p>',
		),

		/* Antes repetía «lo recogemos donde estés». Ahora explica cómo se pide
		 * y qué hay que tener listo, que es lo que la página no decía. */
		'recojo-a-domicilio-lima' => array(
			'sub'   => 'Antes de pedirlo',
			'titulo' => 'Cómo pedir el recojo y <span class="hl">qué tener listo</span>',
			'html'  => '<ol class="gr-pseo-steps">
<li><strong>Escríbenos por WhatsApp con tu distrito.</strong> Y la dirección exacta con una referencia, para coordinar el día y la franja horaria.</li>
<li><strong>Dinos qué envías y a dónde.</strong> Con eso te damos el precio antes de salir a recoger, no después.</li>
<li><strong>Ten el contenido a mano y la caja abierta.</strong> Lo revisamos contigo antes de cerrarla: es lo que evita una retención en la aduana de destino.</li>
<li><strong>Prepara tu documento y los datos del destinatario.</strong> Nombre completo como figura en su documento, dirección con código postal y un teléfono que conteste.</li>
</ol>
<p>Si son varios bultos o una mudanza, avísanos del número de cajas y sus medidas aproximadas para ir con el vehículo adecuado. El <a href="%H%/servicios/envio-de-equipaje/">envío de equipaje</a> se coordina igual.</p>',
		),

		/* Antes era «De tu casa a otro país, en cinco pasos» y la página ya
		 * explicaba siete: se contradecía. Ahora cubre lo que faltaba, los
		 * errores que retrasan un primer envío. */
		'como-enviar-un-paquete-al-extranjero' => array(
			'sub'   => 'Antes del primer envío',
			'titulo' => 'Los errores que más retrasan <span class="hl">un primer envío</span>',
			'html'  => '<ul class="gr-pseo-list">
<li><strong>Medir la caja abierta.</strong> Se mide cerrada y por su parte más ancha. Unos centímetros de más cambian el <a href="%H%/servicios/peso-volumetrico/">peso volumétrico</a> y, con él, el precio.</li>
<li><strong>Describir el contenido con una palabra.</strong> «Regalos» o «varios» invitan a la revisión en aduana. Cada artículo va con su cantidad y su valor.</li>
<li><strong>Declarar menos de lo que vale.</strong> Baja la cobertura del <a href="%H%/seguro-de-envios/">seguro</a> y, si la aduana nota la diferencia, el envío se detiene.</li>
<li><strong>Dar una dirección incompleta.</strong> Sin código postal, sin número de departamento o sin un teléfono local que conteste, la entrega se detiene en el último tramo.</li>
<li><strong>Usar un nombre distinto al del documento.</strong> Donde el envío se retira en agencia, el nombre de la guía tiene que coincidir con el documento del destinatario.</li>
<li><strong>Meter baterías sueltas o líquidos en un envío aéreo.</strong> Tienen reglas propias de transporte: <a href="%H%/que-se-puede-enviar/">revisa qué se puede enviar</a> antes de cerrar la caja.</li>
</ul>
<p>Los seis los detectamos en la revisión que hacemos antes de despachar, pero el envío sale antes si llegan resueltos desde casa.</p>',
		),

		/* Antes repetía «qué cubre y qué no», que la página ya explicaba. Ahora
		 * es un ejemplo con cifras de cómo el valor declarado fija la cobertura. */
		'seguro-de-envios' => array(
			'sub'   => 'Un ejemplo con números',
			'titulo' => 'Cuánto recuperas según <span class="hl">lo que declaraste</span>',
			'html'  => '<p>Dos envíos iguales con el mismo contenido: un teléfono y ropa que en total valen S/ 2 000.</p>
<ul class="gr-pseo-list">
<li><strong>Declarado por su valor real, S/ 2 000.</strong> Si el envío se pierde, la cobertura se calcula sobre S/ 2 000.</li>
<li><strong>Declarado por S/ 500 para pagar menos impuesto.</strong> Si el envío se pierde, la cobertura se calcula sobre S/ 500, aunque lo que había dentro valiera cuatro veces más.</li>
</ul>
<p>El impuesto que se ahorra en el segundo caso rara vez compensa ese riesgo, y además expone el envío a una revisión si la aduana nota que el valor no cuadra con el contenido.</p>
<p><strong>Cuándo conviene ampliar la cobertura:</strong> electrónica, mercancía que vas a vender, regalos de valor y piezas difíciles de reemplazar. <strong>Cuándo basta la básica:</strong> ropa usada y objetos de poco valor. En los documentos, la cobertura se calcula sobre lo que vale el papel, no sobre el trámite: si es un original difícil de conseguir, guarda una copia antes de enviarlo.</p>',
		),

		'tiempos-de-entrega' => array(
			'sub'   => 'Cómo se cuenta',
			'titulo' => 'Por qué un envío tarda lo que <span class="hl">tarda</span>',
			'html'  => '<p>El plazo de un envío internacional no es un número, son <strong>cuatro tramos encadenados</strong>: el despacho en {{origen_ciudad}}, el trayecto internacional, el paso por la aduana del país de destino y la distribución interna hasta el domicilio o la agencia. Los tres primeros los controlamos; el de aduana, no, y es el único que puede estirarse sin previo aviso.</p>
<p>Los plazos que publicamos se cuentan en <strong>días hábiles desde el despacho</strong>, no desde que nos escribes. Un envío entregado el viernes por la tarde empieza a contar el lunes, y un feriado en origen o en destino no cuenta como día hábil. Lo decimos así de claro porque la mayoría de las llamadas de «ya debería haber llegado» nacen de contar los días de otra manera.</p>
<ul class="gr-pseo-list">
<li><strong>La vía aérea acorta el tramo internacional</strong>, no el de aduana: si el envío se detiene, se detiene igual.</li>
<li><strong>Una descripción de contenido precisa</strong> es lo que más acelera el paso por aduana. Las vagas —«varios», «regalos»— invitan a la revisión.</li>
<li><strong>El destino final importa.</strong> Una capital no tarda lo mismo que una ciudad del interior: <a href="%H%/destinos/">mira el plazo de tu país</a>.</li>
</ul>',
		),

		'destinos' => array(
			'sub'   => 'Cómo leer esta página',
			'titulo' => 'Cada país tiene <span class="hl">sus propias reglas</span>',
			'html'  => '<p>Enviar a Chile no se parece a enviar a Estados Unidos, y no por la distancia: <strong>cambian la vía disponible, la forma de entrega y lo que la aduana admite</strong>. En Sudamérica la vía terrestre compite de verdad en precio para bultos voluminosos; hacia Norteamérica y Europa manda la aérea. En unos países entregamos en el domicilio del destinatario y en otros el envío se retira en una agencia local, y eso conviene saberlo antes de cotizar, no después.</p>
<p>En la ficha de cada destino encontrarás lo mismo y en el mismo orden: <strong>modalidades disponibles, plazo estimado en días hábiles, forma de entrega, qué admite su aduana y las ciudades a las que más se envía</strong>. Son los cinco datos que deciden un envío. Y si quieres leer más sobre un país concreto, están las <a href="%H%/articulos-por-pais/">guías de envío por país</a>.</p>
<p>Si tu país no aparece en la lista, escríbenos igual: trabajamos más de treinta destinos y muchos se coordinan bajo pedido. <a href="%H%/cotizar/">Pide tu cotización</a> y te confirmamos vía y plazo reales antes de que compres nada.</p>',
		),

		'cotizar' => array(
			'sub'   => 'Qué hay detrás del precio',
			'titulo' => 'Cómo se arma la cifra que <span class="hl">te damos</span>',
			'html'  => '<p>Una cotización de envío internacional se construye con cuatro variables y ninguna es negociable a ojo: <strong>el peso que se cobra, la vía, el destino y el valor declarado</strong>.</p>
<ul class="gr-pseo-list">
<li><strong>El peso que se cobra</strong> es el mayor entre el real y el <a href="%H%/servicios/peso-volumetrico/">volumétrico</a> (alto × largo × ancho ÷ 5000). Ajustar la caja al contenido es la forma más rápida de bajar el precio.</li>
<li><strong>La vía</strong> marca la diferencia grande: la aérea se paga por rapidez y la terrestre por volumen. Para un bulto que abulta y no corre prisa, no hay color.</li>
<li><strong>El destino</strong> fija el plazo y los impuestos de su aduana sobre el valor declarado. Te los decimos antes de despachar, nunca después.</li>
<li><strong>El valor declarado</strong> es la base del impuesto y del <a href="%H%/seguro-de-envios/">seguro</a>. Declarar de menos no abarata: deja el envío sin cobertura real.</li>
</ul>
<p>Por eso pedimos peso, medidas y ciudad de destino: con esos tres datos la cifra es cerrada. Sin ellos, cualquier precio que te den es una estimación que cambiará en el mostrador.</p>',
		),

		'rastreo-de-envios' => array(
			'sub'   => 'Qué significa cada estado',
			'titulo' => 'Dónde está tu envío y <span class="hl">qué está pasando</span>',
			'html'  => '<p>Un envío internacional no avanza a ritmo constante: pasa por tramos y en cada uno se detiene lo que tiene que detenerse. Saber qué significa cada estado evita la mitad de las preocupaciones.</p>
<ul class="gr-pseo-list">
<li><strong>Recibido en {{origen_ciudad}}.</strong> El bulto está en nuestra sede, pesado y medido. Todavía no ha salido del país.</li>
<li><strong>Despachado.</strong> Se emitió la documentación de exportación y el envío entró en la programación de su vía. Es el punto que separa «lo entregué» de «ya salió».</li>
<li><strong>En tránsito internacional.</strong> Viaja. En vía terrestre el estado cambia con menos frecuencia que en aérea: es normal, no es que esté detenido.</li>
<li><strong>En aduana de destino.</strong> El único tramo cuyo tiempo no controlamos. Si se alarga, casi siempre es por la descripción del contenido, y lo gestionamos nosotros.</li>
<li><strong>En reparto o disponible para retiro.</strong> Según el país, el envío sale a domicilio o queda en la agencia local del destinatario.</li>
</ul>
<p>Si el estado no cambia durante varios días hábiles, escríbenos con tu número de guía: en la mayoría de los casos es el paso por aduana y podemos decirte exactamente qué falta.</p>',
		),

		'envios-desde-provincias' => array(
			'sub'   => 'Si no estás en {{origen_ciudad}}',
			'titulo' => 'Enviar al extranjero desde <span class="hl">cualquier ciudad</span>',
			'html'  => '<p>No hace falta vivir en {{origen_ciudad}} para enviar al extranjero con nosotros. El envío hace <strong>dos tramos</strong>: primero llega a nuestra sede por una agencia de transporte local, y desde ahí sale al país de destino como cualquier otro despacho.</p>
<p>Lo único que cambia respecto a un envío hecho aquí es el <strong>tiempo del tramo interno</strong>, que hay que sumarle al plazo internacional, y que conviene avisarnos antes de mandarlo para reservar el despacho y revisar el contenido a la llegada. El precio del flete internacional es el mismo: no se cobra más por venir de provincia.</p>
<ul class="gr-pseo-list">
<li><strong>Manda el bulto a nombre de Grenvíos</strong> a nuestra dirección de {{origen_ciudad}}, con tu nombre y teléfono como remitente.</li>
<li><strong>Avísanos con la guía de la agencia local.</strong> Así lo esperamos y lo procesamos el mismo día que llega.</li>
<li><strong>Embálalo pensando en dos viajes.</strong> El tramo interno también maltrata la caja: <a href="%H%/como-enviar-un-paquete-al-extranjero/">mira cómo embalar</a>.</li>
</ul>',
		),

		'blog' => array(
			'sub'   => 'Qué vas a encontrar aquí',
			'titulo' => 'Guías para enviar al extranjero <span class="hl">sin sustos</span>',
			'html'  => '<p>Escribimos sobre lo que preguntan nuestros clientes antes de despachar: <strong>cuánto cuesta un envío y por qué, qué pide la aduana de cada país, qué se puede mandar y qué no, y cómo embalar para que llegue entero</strong>. Nada de teoría: cada guía nace de un caso real que pasó por nuestro mostrador.</p>
<p>Si es tu primera vez, empieza por <a href="%H%/como-enviar-un-paquete-al-extranjero/">cómo enviar un paquete al extranjero</a> y por <a href="%H%/servicios/peso-volumetrico/">cómo se calcula el peso que se cobra</a>: entre los dos resuelven las dudas del 80 % de los envíos. Si ya sabes a dónde envías, ve directo a <a href="%H%/destinos/">la ficha de tu país</a>, donde están sus plazos, sus vías y lo que admite su aduana.</p>',
		),

		'contacto' => array(
			'sub'   => 'Cómo llegar y cuándo',
			'titulo' => 'Atención en {{origen_ciudad}} y por <span class="hl">WhatsApp</span>',
			'html'  => '<p>Puedes resolver un envío entero sin moverte: cotizamos, coordinamos el <a href="%H%/recojo-a-domicilio-lima/">recojo a domicilio en {{origen_ciudad}}</a> y te damos seguimiento por WhatsApp hasta la entrega. Y si prefieres acercarte, estamos en {{contacto_direccion}}.</p>
<p>Para que la primera respuesta ya traiga el precio, cuéntanos tres cosas: <strong>qué envías, cuánto pesa y mide el bulto, y a qué ciudad va</strong>. Con eso calculamos el peso que se cobra y te damos una cifra cerrada en la misma conversación, en vez de pedirte datos por goteo.</p>
<ul class="gr-pseo-list">
<li><strong>Si ya despachaste</strong> y quieres saber dónde está tu envío, ten a mano el número de guía: <a href="%H%/rastreo-de-envios/">así funciona el seguimiento</a>.</li>
<li><strong>Si envías de forma recurrente</strong>, pregunta por la tarifa por volumen y el recojo programado: <a href="%H%/envios-para-empresas/">envíos para empresas</a>.</li>
<li><strong>Si estás fuera de {{origen_ciudad}}</strong>, también podemos: <a href="%H%/envios-desde-provincias/">mira cómo enviar desde provincias</a>.</li>
</ul>',
		),

		/* Antes repetía qué alimentos viajan y cuáles no, que la página ya
		 * explicaba. Ahora cubre cómo declararlos, que es donde se retienen. */
		'envio-de-alimentos' => array(
			'sub'   => 'Cómo declararlos',
			'titulo' => 'Cómo declarar alimentos para que <span class="hl">no los retengan</span>',
			'html'  => '<p>Con los alimentos, la aduana y el servicio sanitario del país de destino leen primero la declaración y después abren la caja. Una lista clara hace que el envío pase sin preguntas.</p>
<ul class="gr-pseo-list">
<li><strong>Un renglón por producto.</strong> Nombre comercial, marca, número de unidades y peso o volumen de cada una. «Comida peruana» no es una descripción.</li>
<li><strong>El valor de cada producto.</strong> Con la boleta de compra si la tienes: es la base del impuesto en destino.</li>
<li><strong>Etiqueta original visible y fecha de vencimiento legible.</strong> Sin etiquetas tapadas con cinta ni productos pasados a otro envase.</li>
<li><strong>Los alimentos, aparte del resto.</strong> En bolsa sellada dentro de la caja: si algo se derrama no estropea la ropa, y la inspección es más rápida.</li>
<li><strong>Nada sin declarar.</strong> Un producto que aparece en la caja y no en la lista puede detener todo el envío, no solo ese producto.</li>
</ul>
<p>Cada país tiene su propia lista de lo que admite. <a href="%H%/cotizar/">Mándanos la tuya antes de comprar</a> y te decimos qué entra en tu destino y por qué vía.</p>',
		),
	) );
}

/* Preguntas por página. Se suman a las del tema y van al FAQPage del schema. */
function grenvios_pseo_faqs() {
	static $f = null;
	if ( $f !== null ) return $f;

	return $f = apply_filters( 'grenvios_pseo_faqs', array(
		'servicios' => array(
			array( '¿Cuál es la diferencia entre paquete y carga?', 'El paquete es un envío personal o de bajo volumen y se cobra por el mayor entre su peso real y el volumétrico. La carga empieza cuando el volumen es comercial: se cotiza el conjunto, se consolida y se despacha con documentación de exportación. Si dudas, dinos qué envías y lo clasificamos contigo.' ),
			array( '¿Puedo enviar documentos y paquetes en el mismo envío?', 'Sí, siempre que el contenido esté declarado con precisión. Ten en cuenta que un documento suelto viaja más barato por su cuenta: al meterlo en una caja pasa a cobrarse por el volumen de la caja.' ),
		),
		'carga-internacional' => array(
			array( '¿Desde qué volumen se considera carga?', 'No hay una cifra mágica: cuando el envío deja de ser un bulto y pasa a ser varios, o cuando el volumen obliga a consolidar y a emitir documentación comercial, se cotiza como carga. Con tus medidas y tu frecuencia te decimos cuál de los dos sale mejor.' ),
			array( '¿Qué documentación necesito para exportar mi mercancía?', 'Factura comercial con el valor real, descripción precisa del contenido y, según el producto y el destino, ficha técnica o permisos sectoriales. Te confirmamos la lista exacta antes de que la carga salga de {{origen_ciudad}}.' ),
			array( '¿Recogen la carga en el almacén de mi proveedor?', 'Sí. Coordinamos el recojo en tu local, tu almacén o el de tu proveedor dentro de {{origen_ciudad}}, y para envíos recurrentes fijamos días de recojo para que no tengas que pedirlo cada vez.' ),
		),
		'envio-de-compras' => array(
			array( '¿Puedo comprar en varias tiendas y enviarlo todo junto?', 'Sí, y es lo que más ahorra. Recibimos los paquetes en nuestra sede de {{origen_ciudad}}, los consolidamos en un solo bulto y pagas un único flete en vez de uno por tienda.' ),
			array( '¿Cuánto tiempo guardan mis compras antes de enviarlas?', 'Las mantenemos en nuestra sede mientras llegan el resto de tus pedidos. Avísanos cuántos esperas y desde qué tiendas para reservarlas juntas y despacharlas en un solo envío.' ),
			array( '¿Las compras pagan impuestos al llegar?', 'Depende del país de destino y del valor declarado: cada aduana fija su propio umbral. Te decimos el de tu país antes de despachar, para que no haya sorpresas al recibir.' ),
		),
		'recojo-a-domicilio-lima' => array(
			array( '¿El recojo tiene costo?', 'Sí, y depende del distrito dentro de {{origen_ciudad}}. Te lo decimos al cotizar, junto con el flete, para que veas el total antes de decidir.' ),
			array( '¿El recojo retrasa mi envío?', 'No. El envío entra en la misma programación que si lo llevaras tú a nuestra sede. Lo que cuenta es el día del recojo, así que conviene coordinarlo con un poco de margen.' ),
			array( '¿Tengo que entregar el paquete ya embalado?', 'No hace falta. Preferimos revisar el contenido antes de cerrarlo: es lo que evita una retención en aduana. Si no tienes caja, ponemos una ajustada al contenido.' ),
		),
		'como-enviar-un-paquete-al-extranjero' => array(
			array( '¿Qué necesito para enviar un paquete al extranjero?', 'Tu documento de identidad, los datos completos del destinatario —nombre, dirección exacta, teléfono— y el contenido declarado con precisión. Si es mercancía nueva, la boleta o factura.' ),
			array( '¿Cuánto tarda todo el proceso?', 'Cotizar y despachar se resuelve el mismo día. A partir de ahí manda el plazo de la ruta, contado en días hábiles desde el despacho, más lo que sume la aduana del país de destino.' ),
			array( '¿Cómo embalo para que llegue bien?', 'Caja ajustada al contenido, relleno que impida el movimiento dentro y cinta en todas las uniones. Ajustada, no grande: el espacio vacío se paga como peso volumétrico y además permite que el contenido se golpee.' ),
		),
		'seguro-de-envios' => array(
			array( '¿Todos los envíos van asegurados?', 'La cobertura se calcula sobre el valor declarado del envío. Dinos qué valor tiene lo que mandas y te indicamos la cobertura que corresponde antes de despachar.' ),
			array( '¿Qué pasa si mi envío se daña?', 'Se abre el reclamo con la documentación del despacho y las fotos del contenido. Por eso recomendamos fotografiar lo que va dentro antes de cerrar la caja: es la prueba más simple y la que más rápido resuelve.' ),
			array( '¿El seguro cubre una retención en aduana?', 'No. Una retención no es un siniestro, es un tema documental, y se resuelve aportando lo que la aduana pide. Nos ocupamos nosotros del trámite.' ),
		),
		'tiempos-de-entrega' => array(
			array( '¿Desde cuándo se cuentan los días?', 'Desde el despacho, no desde que nos escribes, y en días hábiles: fines de semana y feriados —de origen o de destino— no cuentan.' ),
			array( '¿Por qué mi envío lleva días sin cambiar de estado?', 'Casi siempre es el paso por aduana, el único tramo cuyo tiempo no controlamos. En vía terrestre, además, el estado se actualiza con menos frecuencia que en aérea sin que eso signifique que esté detenido.' ),
			array( '¿La vía aérea garantiza que llegue antes?', 'Acorta el tramo internacional, que es el más largo, pero no el de aduana. Si el envío se revisa en destino, se revisa igual por aire que por carretera.' ),
		),
		'destinos' => array(
			array( '¿A cuántos países envían?', 'Trabajamos más de treinta destinos. Nueve de ellos tienen ruta propia con plazos, modalidades y condiciones de aduana publicadas; el resto se coordina bajo pedido con las mismas garantías.' ),
			array( '¿Cómo sé si llegan a la ciudad de mi destinatario?', 'En la ficha de cada país publicamos las ciudades a las que más se envía, pero llegamos también al resto del territorio. Dinos la dirección exacta al cotizar y te confirmamos plazo y forma de entrega.' ),
		),
		'cotizar' => array(
			array( '¿La cotización tiene algún costo o compromiso?', 'Ninguno. Te damos el precio con el peso, las medidas y el destino, y decides tú. No se cobra nada hasta que el envío se despacha.' ),
			array( '¿Por qué me piden las medidas si ya di el peso?', 'Porque se cobra el mayor entre el peso real y el volumétrico (alto × largo × ancho ÷ 5000). Sin medidas, cualquier precio es una estimación que cambiará en el mostrador.' ),
		),
		'rastreo-de-envios' => array(
			array( '¿Dónde consigo mi número de guía?', 'Se te entrega al despachar el envío, junto con el comprobante. Si lo perdiste, escríbenos con tu nombre y la fecha del despacho y lo recuperamos.' ),
			array( '¿El seguimiento funciona también en vía terrestre?', 'Sí. Lo que cambia es la frecuencia con la que se actualiza: en carretera los estados cambian menos a menudo que en un envío aéreo, y eso no significa que esté detenido.' ),
		),
		'envios-desde-provincias' => array(
			array( '¿Cuánto suma el tramo desde provincia?', 'Depende de la agencia de transporte local y de la ciudad de origen. Ese tiempo se suma al plazo internacional, que empieza a contar cuando el bulto llega a nuestra sede de {{origen_ciudad}} y se despacha.' ),
			array( '¿Cobran más por enviar desde provincia?', 'El flete internacional es el mismo. Lo que pagas aparte es el transporte interno hasta {{origen_ciudad}}, que contratas con la agencia local que prefieras.' ),
		),
		'aduanas-e-impuestos' => array(
			array( '¿Quién paga los impuestos, yo o quien recibe?', 'Depende del país de destino y de cómo se despache. Te lo decimos antes de enviar, junto con el umbral de valor a partir del cual el envío tributa, para que nadie se lleve una sorpresa al recibir.' ),
			array( '¿Sobre qué se calculan los impuestos?', 'Sobre el valor declarado de la mercancía, no sobre lo que pagaste de flete. Por eso declarar un valor irreal ni abarata el trámite ni te conviene: también es la base de la cobertura del envío.' ),
			array( '¿Qué hace que un envío se quede retenido en aduana?', 'Casi siempre una descripción de contenido imprecisa o una factura que no coincide con lo que va dentro. Se resuelve aportando el documento que falta, y lo gestionamos nosotros.' ),
		),
		'articulos-por-pais' => array(
			array( '¿Por qué un producto entra en un país y en otro no?', 'Porque la lista de restricciones la fija la aduana de cada país, no la empresa de transporte. Lo que Ecuador admite por carretera puede estar prohibido por vía aérea hacia otro destino.' ),
			array( '¿Qué pasa si envío algo no permitido?', 'La aduana puede retenerlo o destruirlo, y el flete no se devuelve. Por eso revisamos el contenido contigo antes de despachar: cinco minutos de revisión evitan perder el envío entero.' ),
		),
		'envio-de-alimentos' => array(
			array( '¿Puedo enviar comida casera?', 'No. Solo viajan alimentos envasados industrialmente, sellados de fábrica, no perecibles y con su etiqueta original. Lo preparado en casa no pasa el control sanitario de ningún destino.' ),
			array( '¿Por qué por avión se admite menos que por carretera?', 'Porque la normativa aérea es más restrictiva con líquidos, aerosoles y productos perecibles. El mismo producto puede viajar en camión hacia un país vecino y no en avión.' ),
		),
	) );
}

/* Normaliza el mapa: una página puede declarar UNA sección (array con sub,
 * titulo y html) o VARIAS (lista de esos arrays). Internamente siempre lista,
 * para que el registro de campos y el render no tengan que distinguir. */
function grenvios_pseo_lista( $slug ) {
	$m = grenvios_pseo_secciones();
	if ( ! isset( $m[ $slug ] ) ) return array();
	$s = $m[ $slug ];
	return isset( $s['html'] ) ? array( $s ) : array_values( $s );
}

/* Clave de campo a partir del slug: «envio-de-compras» → «pseo_envio_de_compras». */
function grenvios_pseo_key( $slug ) {
	return 'pseo_' . str_replace( '-', '_', $slug );
}

/* ═══════════════════════════════════════════════════════════════════
 * ESTAS SECCIONES SON EDITABLES, COMO EL RESTO DEL SITIO
 *
 * El texto de aquí es solo el valor POR DEFECTO. Se registra en el mismo
 * sitio que los demás campos del tema, así que aparece en el panel «Editar
 * página» —con su antetítulo, su titular y su cuerpo— y se guarda en la
 * página que se está viendo. Es decir: cada ruta de país guarda su propia
 * versión y la de Perú no pisa la de Argentina.
 * ═════════════════════════════════════════════════════════════════ */
add_filter( 'grenvios_text_registry', function ( $reg ) {
	/* Estas secciones solo se pintan en la ruta principal (en las de país ese
	 * trabajo lo hace el bloque por país), así que tampoco se ofrecen para
	 * editar allí: un campo que no se ve en la página confunde más que ayuda.
	 * En el admin y en las peticiones REST sí se registran, para que el guardado
	 * conozca el tipo de cada campo. */
	if ( ! is_admin() && function_exists( 'grenvios_hq_pais' ) && grenvios_hq_pais() !== '' ) return $reg;

	foreach ( grenvios_pseo_secciones() as $slug => $_ ) {
		if ( ! isset( $reg[ $slug ] ) ) {
			$reg[ $slug ] = array( 'label' => ucfirst( str_replace( '-', ' ', $slug ) ), 'priority' => 80, 'sections' => array() );
		}
		$nombre = ucfirst( str_replace( '-', ' ', $slug ) );
		foreach ( grenvios_pseo_lista( $slug ) as $i => $s ) {
			$k   = grenvios_pseo_key( $slug ) . ( $i ? '_' . ( $i + 1 ) : '' );
			$sec = 'pseo' . ( $i ? '_' . ( $i + 1 ) : '' );
			$reg[ $slug ]['sections'][ $sec ] = array(
				'label'  => $nombre . ' · Sección de contenido' . ( $i ? ' ' . ( $i + 1 ) : '' ),
				/* El panel oculta por defecto los campos que no aparecen como token
				 * {{campo}} en el partial de la página. Estos se pintan desde PHP,
				 * así que se le dice que no haga esa comprobación; `sel` es lo que
				 * usa el botón de «llevar a la sección». */
				'_no_token_check' => true,
				'sel'    => '.gr-pseo',
				'fields' => array(
					$k . '_sub'   => array( 'Antetítulo', 'text', $s['sub'] ),
					$k . '_title' => array( 'Título (usa <span class="hl">…</span> para el acento)', 'html', $s['titulo'] ),
					$k . '_body'  => array( 'Cuerpo (admite párrafos, listas y enlaces)', 'html', $s['html'] ),
					$k . '_img'   => array( 'Foto que acompaña al texto (vacía = foto de ejemplo según el tema)', 'image', '' ),
				),
			);
		}
	}
	return $reg;
} );

/* El guardado acepta el HTML del cuerpo: párrafos, listas, enlaces y negritas
 * ya están en la lista blanca del editor (ver grenvios_rest_save_page). */

/* Solo en la ruta principal: en las de país el bloque por país ya cumple
 * esta función y duplicarlo restaría en vez de sumar. */
function grenvios_pseo_activa() {
	if ( is_admin() ) return false;
	return ! function_exists( 'grenvios_hq_pais' ) || grenvios_hq_pais() === '';
}

function grenvios_pseo_render( $slug ) {
	if ( ! grenvios_pseo_activa() ) return;
	foreach ( grenvios_pseo_lista( $slug ) as $i => $s ) {
		grenvios_pseo_render_una( $slug, $s, $i );
	}
}

function grenvios_pseo_render_una( $slug, $s, $i = 0 ) {
	$k    = grenvios_pseo_key( $slug ) . ( $i ? '_' . ( $i + 1 ) : '' );
	$home = function_exists( 'grenvios_url_base' ) ? grenvios_url_base() : home_url();

	/* Lo que haya guardado la clienta en ESTA página manda; si no ha tocado
	 * nada, sale el texto por defecto del registro. */
	$sub   = grenvios_field( $k . '_sub',   $s['sub'] );
	$tit   = grenvios_field( $k . '_title', $s['titulo'] );
	$cuerpo = grenvios_field( $k . '_body', $s['html'] );
	if ( trim( wp_strip_all_tags( (string) $cuerpo ) ) === '' ) return;   // vaciado a propósito

	$html = str_replace( '%H%', esc_url( $home ), $cuerpo );
	/* Aparición escalonada de los puntos de listas y pasos (skill grenvios-ui). */
	$ni = 0;
	$html = preg_replace_callback( '~<li>~', function () use ( &$ni ) {
		return '<li class="wow fade-in-bottom" data-wow-delay="' . ( 80 + ( $ni++ % 6 ) * 70 ) . 'ms">';
	}, $html );

	/* %T% → raíz de los archivos de etiqueta (inc/blog-etiquetas.php). Se
	 * escribe así para no repetir la base del sitio en cada enlace de una lista
	 * de temas, que es donde más enlaces hay por párrafo. */
	$html = str_replace( '%T%', esc_url( trailingslashit( $home ) . 'tag/' ), $html );

	/* %G:slug% → enlace a una guía del blog. Se resuelve aquí porque estas
	 * secciones se imprimen directamente, sin pasar por `the_content`, que es
	 * donde vive el resolutor de %P: (inc/blog-guias-importar.php). Escribir la
	 * URL con su fecha a mano se rompería en cuanto la entrada cambie de día. */
	if ( strpos( $html, '%G:' ) !== false && function_exists( 'grenvios_guia_maestra' ) ) {
		$html = preg_replace_callback( '/%G:([a-z0-9-]+)%/', function ( $m ) use ( $home ) {
			$post = grenvios_guia_maestra( $m[1] );
			return $post ? esc_url( get_permalink( $post->ID ) ) : esc_url( $home . '/blog/' );
		}, $html );
	}

	/* Filtro `grenvios_pseo_aside`: columna lateral opcional (datos, cifras)
	 * que acompaña al texto. Si llega vacía, la sección se pinta como siempre. */
	$aside = (string) apply_filters( 'grenvios_pseo_aside', '', $slug, $i );

	/* Sin columna propia: foto que acompaña al texto (skill grenvios-landing).
	 * La del campo del panel o, si está vacío, una de ejemplo elegida por el
	 * tema del título. Alterna de lado en cada sección. */
	$foto = false;
	if ( $aside === '' && function_exists( 'grenvios_ej_por_tema' ) ) {
		$img = trim( (string) grenvios_field( $k . '_img', '' ) );
		if ( $img === '' ) $img = grenvios_ej_por_tema( wp_strip_all_tags( $tit ) . ' ' . $slug, $i % 2 ? 'almacen' : 'embalaje' );
		if ( $img !== '' ) {
			$foto  = true;
			$aside = '<figure class="gr-pseo-foto wow ' . ( $i % 2 ? 'fade-in-left' : 'fade-in-right' ) . '" data-wow-delay="150ms">'
				. '<img src="' . esc_url( $img ) . '" alt="" loading="lazy" decoding="async"></figure>';
		}
	}

	$out = '<section class="gr-pseo gr-pseo--' . esc_attr( $slug ) . ' gr-pseo--n' . ( $i + 1 ) . ( $aside !== '' ? ' gr-pseo--aside' : '' ) . ( $foto ? ' gr-pseo--foto' . ( $i % 2 ? ' gr-pseo--inv' : '' ) : '' ) . ' padding-bottom"><div class="container">'
		. ( $aside !== '' ? '<div class="gr-pseo-grid"><div class="gr-pseo-main">' : '' )
		. '<div class="section-heading mb-30">'
		. ( trim( (string) $sub ) !== '' ? '<h3 class="sub-heading">' . esc_html( $sub ) . '</h3>' : '' )
		. '<h2>' . wp_kses_post( $tit ) . '</h2>'
		. '</div><div class="gr-pseo-body">' . $html . '</div>'
		. ( $aside !== '' ? '</div><aside class="gr-pseo-aside">' . $aside . '</aside></div>' : '' )
		. '</div></section>';

	echo function_exists( 'grenvios_apply_media_overrides' ) ? grenvios_apply_media_overrides( $out ) : $out;
}

/* Las preguntas se añaden a las que ya tenga la página. */
add_filter( 'grenvios_page_faqs', function ( $faqs, $slug ) {
	if ( ! grenvios_pseo_activa() ) return $faqs;
	$f = grenvios_pseo_faqs();
	if ( ! isset( $f[ $slug ] ) ) return $faqs;

	/* Sin esta comprobación, /servicios/ salía con «¿Cuál es la diferencia
	 * entre paquete y carga?» dos veces: la pregunta ya estaba en las del
	 * tema. Una pregunta repetida dentro del mismo FAQPage es de los errores
	 * que hacen que Google descarte el bloque entero. */
	$vistas = array();
	foreach ( (array) $faqs as $q ) {
		$vistas[] = mb_strtolower( trim( wp_strip_all_tags( (string) $q[0] ) ) );
	}
	foreach ( $f[ $slug ] as $q ) {
		if ( in_array( mb_strtolower( trim( $q[0] ) ), $vistas, true ) ) continue;
		$faqs[] = $q;
	}
	return $faqs;
}, 25, 2 );
