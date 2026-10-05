<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Guías POR PAÍS: una tanda de entradas propia para cada destino
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Las catorce guías generales (inc/blog-guias-contenido.php) se copian a todas
 * las rutas. Estas no: cada una trata de UN país y vive solo en su ruta y en
 * el blog principal, reforzando la ficha de ese destino.
 *
 * CÓMO SE EVITA QUE SEAN NUEVE VECES EL MISMO TEXTO CON OTRO NOMBRE
 *
 * Cada plantilla se construye con los datos reales del destino —vías
 * disponibles, forma de entrega, plazo, impuesto, ciudades— y RAMIFICA con
 * ellos: la guía de un país solo aéreo no explica la vía terrestre, la de un
 * país con retiro en agencia no habla de repartidores, la de uno con impuesto
 * publicado lo cuantifica. A eso se suma una nota de contexto por país
 * (`grenvios_bp_notas`), escrita a mano, con lo que de verdad distingue a esa
 * ruta. El resultado son textos que comparten estructura —como comparten
 * estructura las fichas de cualquier catálogo— pero no contenido.
 *
 * Nada se afirma que no esté en el gestor de destinos o en las notas. Si un
 * dato falta (impuesto, ciudades), la frase que lo usa no se escribe.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Contexto propio de cada ruta. Solo lo que se sabe cierto de ESE país. */
function grenvios_bp_notas() {
	return apply_filters( 'grenvios_bp_notas', array(
		'ecuador' => array(
			'productos' => 'ropa y calzado, repuestos, artesanía y productos peruanos que allí no se consiguen; por carretera también cargas medianas para pequeños comercios',
			'quien'   => 'familias con parientes a los dos lados de la frontera, pequeños comercios que se abastecen en {{origen_ciudad}} y estudiantes',
			'via'     => 'La vía terrestre entra por la frontera de Huaquillas y es la más usada para paquetes y cargas medianas: la cercanía la hace competitiva en precio frente a la aérea.',
			'consejo' => 'Si el envío es voluminoso y no corre prisa, la carretera es la opción natural hacia Ecuador; la aérea se reserva para documentos y urgencias.',
		),
		'colombia' => array(
			'productos' => 'regalos y encomiendas familiares, ropa, productos de belleza envasados, muestras comerciales y documentos para trámites',
			'quien'   => 'la comunidad peruana de Bogotá, Medellín y Cali, y negocios que venden productos peruanos allí',
			'via'     => 'Es una ruta larga por carretera, así que la diferencia de plazo entre la vía terrestre y la aérea es mayor que hacia los países fronterizos; la terrestre compensa sobre todo en bultos que abultan.',
			'consejo' => 'Para Colombia conviene decidir la vía por el tamaño del bulto: documentos y paquetes pequeños por aire; cajas grandes por carretera.',
		),
		'chile' => array(
			'productos' => 'encomiendas familiares con ropa y alimentos peruanos envasados, mudanzas pequeñas de quienes se instalan en Santiago y repuestos',
			'quien'   => 'una de las comunidades peruanas más grandes del extranjero, concentrada en Santiago, y muchas familias que envían encomiendas con regularidad',
			'via'     => 'Es una de nuestras rutas con entrega directa en el domicilio del destinatario, lo que la hace especialmente cómoda para regalos y encomiendas familiares.',
			'consejo' => 'Hacia Chile la vía terrestre es la favorita para paquetes y mudanzas pequeñas; la aérea acelera documentos y envíos urgentes.',
		),
		'bolivia' => array(
			'productos' => 'medicinas con receta y alimentos sellados por vía terrestre, ropa, calzado y mercadería para comerciantes',
			'quien'   => 'familias y comerciantes de La Paz, El Alto, Santa Cruz y Cochabamba, con mucho intercambio de productos entre los dos países',
			'via'     => 'Por vía terrestre admitimos medicinas y alimentos sellados de fábrica, algo que la vía aérea no permite: es una de las razones por las que la carretera domina esta ruta.',
			'consejo' => 'Si mandas medicinas o alimentos envasados a Bolivia, la vía terrestre es la única opción, y conviene llevar la receta o la etiqueta de fábrica a la vista.',
		),
		'argentina' => array(
			'productos' => 'ropa y regalos para familiares, libros y documentos de estudiantes, y productos peruanos para negocios gastronómicos',
			'quien'   => 'familias en Buenos Aires y en el interior, estudiantes y profesionales que se mudan, y negocios que exportan productos peruanos',
			'via'     => 'Es la ruta terrestre más larga que operamos hacia el sur, así que el plazo por carretera es amplio; la vía aérea es la elección cuando la fecha manda.',
			'consejo' => 'Hacia Argentina el valor declarado se revisa con atención en aduana: declara lo real y guarda la boleta de todo lo nuevo.',
		),
		'estados-unidos' => array(
			'productos' => 'documentos apostillados, artesanía y textiles peruanos, regalos y compras hechas en Lima para quienes viven allí',
			'quien'   => 'la comunidad peruana de Nueva Jersey, Florida, Nueva York y California, empresas que exportan y estudiantes',
			'via'     => 'Servicio aéreo expreso con entrega puerta a puerta en las principales ciudades: es la ruta más rápida del catálogo.',
			'consejo' => 'En Estados Unidos el código postal (ZIP) y el número de unidad o apartamento son obligatorios en la dirección: sin ellos el envío no sale a reparto.',
		),
		'espana' => array(
			'productos' => 'documentos para trámites de nacionalidad y estudios, regalos y productos peruanos difíciles de encontrar en la península',
			'quien'   => 'peruanos residentes en Madrid, Barcelona y Valencia, personas en trámite de nacionalidad o estudios, y familias que envían documentos y regalos',
			'via'     => 'Servicio aéreo a toda la península, con un plazo corto que lo hace ideal para documentos legales, paquetes personales y compras.',
			'consejo' => 'Buena parte de lo que se envía a España son documentos para trámites: comprueba si necesitan apostilla antes de despachar, porque el trámite se hace en {{origen_pais}}, no allí.',
		),
		'venezuela' => array(
			'productos' => 'encomiendas de ayuda familiar: ropa, calzado, artículos de higiene y regalos, consolidados en un solo bulto',
			'quien'   => 'familias que envían encomiendas y ayuda a sus parientes, y emprendedores que abastecen pequeños negocios',
			'via'     => 'Servicio aéreo con entrega puerta a puerta, pensado para que las encomiendas familiares lleguen a la casa del destinatario sin que tenga que desplazarse.',
			'consejo' => 'Hacia Venezuela conviene mandar bultos bien consolidados y con el contenido declarado pieza por pieza: es lo que evita demoras en la entrada.',
		),
		'cuba' => array(
			'productos' => 'medicinas con receta, ropa, calzado y artículos de primera necesidad para la familia',
			'quien'   => 'familias que envían medicinas, ropa y artículos de primera necesidad a sus parientes en la isla',
			'via'     => 'Servicio aéreo especializado. Aceptamos medicinas acompañadas de su receta médica, que es lo que más se envía en esta ruta.',
			'consejo' => 'Para Cuba, cada medicina va con su receta y cada bulto con la lista exacta de lo que contiene. Es la ruta en la que más importa declarar bien.',
		),
	) );
}

/* Frases derivadas de los datos del destino. */
function grenvios_bp_frases( $d ) {
	$p = $d['title'];
	$f = array();
	$f['via'] = $d['aereo'] && $d['terr'] ? 'por vía aérea y terrestre' : ( $d['aereo'] ? 'por vía aérea' : 'por vía terrestre' );
	$f['plazo'] = $d['tiempo'] !== '' ? $d['tiempo'] : 'el plazo de la ruta';
	$f['entrega_corta'] = $d['casa'] ? 'entrega en el domicilio del destinatario' : 'retiro en la agencia local del destinatario';
	$f['entrega_larga'] = $d['casa']
		? 'El envío llega hasta la puerta de tu destinatario. Por eso el teléfono y la dirección exacta —con referencias si hace falta— son los dos datos que más importan.'
		: 'El destinatario retira el envío en la agencia local de su ciudad, con su documento de identidad. Recibe aviso cuando está disponible; conviene que sepa que tiene que ir a buscarlo.';
	$f['impuesto'] = $d['impuesto'] !== ''
		? 'En envíos terrestres se aplica un impuesto aproximado del ' . $d['impuesto'] . ' % sobre el valor declarado, que se paga en {{origen_ciudad}} antes de despachar: quien recibe no adelanta dinero.'
		: 'Si la aduana de ' . $p . ' aplica algún impuesto al valor declarado, te lo decimos antes de despachar, nunca después.';
	$f['dos_vias'] = $d['aereo'] && $d['terr'];
	$ciu = isset( $d['ciudades'] ) ? array_values( array_filter( array_map( 'trim', explode( ',', (string) $d['ciudades'] ) ) ) ) : array();
	$f['ciudades'] = $ciu;
	return $f;
}

/* Las nueve plantillas. Cada una devuelve slug, título, categoría, pilar,
 * extracto y HTML para el país $d con las notas $n. %H% = raíz del sitio. */
function grenvios_bp_plantillas( $d, $n ) {
	$p  = $d['title'];
	$s  = $d['slug'];
	$f  = grenvios_bp_frases( $d );
	$c  = $f['ciudades'];
	$c3 = $c ? implode( ', ', array_slice( $c, 0, 3 ) ) : '';
	$pilar = 'destinos/' . $s;
	$out = array();

	/* 1 ── paso a paso */
	$out[ 'como-enviar-un-paquete-a-' . $s ] = array(
		/* Título y slug propios: «cómo enviar un paquete a <país>» es la
		 * keyword de la página de la ruta. La guía responde otra búsqueda:
		 * la de quien envía por primera vez. */
		'slug'   => 'primer-envio-a-' . $s,
		'titulo' => 'Tu primer envío a ' . $p . ': 5 pasos y los errores que lo retrasan',
		'categoria' => 'destinos', 'pilar' => $pilar,
		'extracto' => 'Si es la primera vez que envías a ' . $p . ': qué comprobar antes de comprar la caja, qué vía elegir, qué declarar y en qué fallan los primeros envíos.',
		'html' => '
<p>La primera vez que se envía a ' . $p . ' se cometen siempre los mismos errores, y casi todos se pagan después: en aduana, en un reintento de entrega o en un flete más caro de lo necesario. ' . $d['lead'] . '</p>
<p>Es una de nuestras rutas habituales: la usan ' . $n['quien'] . ', y lo que más viaja son ' . $n['productos'] . '. Operamos ' . $f['via'] . ', con ' . $f['entrega_corta'] . ' y un plazo habitual de ' . $f['plazo'] . '. Estos son los pasos, en el orden en que conviene darlos.</p>
<h2>1. Comprueba qué admite ' . $p . '</h2>
<p>Antes de comprar la caja, revisa <a href="%H%/que-se-puede-enviar/">qué se puede enviar</a> y lo específico de <a href="%H%/destinos/' . $s . '/">la ficha de ' . $p . '</a>. Un producto que la aduana no deja entrar es dinero perdido dos veces: la compra y el flete.</p>
<h2>2. Elige la vía</h2>
<p>' . $n['via'] . '</p>' . ( $f['dos_vias'] ? '<p>' . $n['consejo'] . '</p>' : '<p>Al ser una ruta aérea, aplican las restricciones de bodega de avión: sin líquidos, aerosoles ni artículos con batería interna.</p>' ) . '
<h2>3. Pesa, mide y declara</h2>
<p>Se cobra el mayor entre el peso real y el <a href="%H%/servicios/peso-volumetrico/">volumétrico</a> (alto × largo × ancho ÷ 5000). Mide la caja cerrada y describe el contenido pieza por pieza: «2 camisas, 1 par de zapatillas», no «ropa». ' . $f['impuesto'] . '</p>
<h2>4. Despacha</h2>
<p>En nuestra sede de {{origen_ciudad}} o con <a href="%H%/recojo-a-domicilio-lima/">recojo a domicilio</a>. Revisamos el embalaje y la declaración antes de que el envío salga: la mayoría de las retenciones se evitan aquí, no se resuelven en ' . $p . '.</p>
<h2>5. Sigue el envío hasta la entrega</h2>
<p>Desde el despacho tienes número de guía. ' . $f['entrega_larga'] . '</p>
<p><a href="%H%/cotizar/">Cotiza tu envío a ' . $p . '</a> con el peso, las medidas y la ciudad de destino: te damos precio cerrado y plazo en la misma respuesta.</p>',
	);

	/* 2 ── precio */
	$out[ 'cuanto-cuesta-enviar-a-' . $s ] = array(
		/* «Cuánto cuesta enviar a <país>» es la página de cotizar de la ruta;
		 * la guía va a la búsqueda informativa de cómo pagar menos. */
		'slug'   => 'como-pagar-menos-al-enviar-a-' . $s,
		'titulo' => 'Cómo pagar menos al enviar a ' . $p . ': lo que define el precio',
		'categoria' => 'destinos', 'pilar' => 'cotizar',
		'extracto' => 'Las decisiones que abaratan un envío a ' . $p . ': el peso que se cobra, la vía, la caja y lo que su aduana revisa.',
		'html' => '
<p>No hay una tarifa fija para ' . $p . ' porque no hay un envío fijo. El precio sale de tres variables, y entenderlas es la forma más corta de pagar menos.</p>
<h2>El peso que se cobra</h2>
<p>El mayor entre el real y el volumétrico —alto × largo × ancho en centímetros, dividido entre 5000—. Una caja grande y ligera paga por el espacio que ocupa. Ajustar la caja al contenido es la primera palanca; <a href="%H%/servicios/peso-volumetrico/">aquí está el cálculo con ejemplos</a>.</p>
<h2>La vía</h2>
' . ( $f['dos_vias']
	? '<p>Hacia ' . $p . ' hay dos caminos. ' . $n['via'] . ' La aérea se paga por rapidez; la terrestre, por volumen: para un bulto voluminoso que no corre prisa, la diferencia es grande.</p>'
	: '<p>Hacia ' . $p . ' operamos por vía aérea. ' . $n['via'] . ' El precio por kilo es el de una ruta aérea, así que el volumen del bulto pesa más en la cuenta que en una ruta por carretera.</p>' ) . '
<h2>La aduana de ' . $p . '</h2>
<p>' . $d['restr'] . '</p>
<p>' . $f['impuesto'] . ' El valor declarado es también la base del <a href="%H%/seguro-de-envios/">seguro</a>: declarar de menos no abarata y deja el envío sin cobertura.</p>
<h2>Lo que abarata un envío a ' . $p . '</h2>
<ol>
<li><strong>Caja a medida.</strong> Unos centímetros menos por lado bajan el volumétrico.</li>
<li><strong>Un solo bulto.</strong> Varios paquetes pequeños pagan varios fletes; consolidados, uno.</li>' . ( $f['dos_vias'] ? '
<li><strong>Vía terrestre</strong> cuando la fecha lo permite.</li>' : '
<li><strong>Comprimir lo blando</strong> (ropa, edredones) en bolsas al vacío: en una ruta aérea es lo que más nota.</li>' ) . '
<li><strong>Consultar antes de comprar</strong> lo que ' . $p . ' admite.</li>
</ol>
<p>Con peso, medidas y ciudad de destino te damos la cifra cerrada. <a href="%H%/cotizar/">Cotiza tu envío a ' . $p . '</a>.</p>',
	);

	/* 3 ── plazos */
	$out[ 'cuanto-demora-un-envio-a-' . $s ] = array(
		/* El plazo lo posiciona la página de tiempos de entrega de la ruta;
		 * la guía responde a quien ve que su envío tarda. */
		'slug'   => 'por-que-se-retrasa-un-envio-a-' . $s,
		'titulo' => 'Por qué se retrasa un envío a ' . $p . ' y cómo evitarlo',
		'categoria' => 'destinos', 'pilar' => 'tiempos-de-entrega',
		'extracto' => 'Qué tramo alarga un envío a ' . $p . ', cómo se cuentan los días hábiles y qué se resuelve antes de despachar para que no se retrase.',
		'html' => '
<p>Un envío a ' . $p . ' casi nunca se retrasa en el avión o en la carretera: se retrasa en un dato que faltaba al despachar o en la aduana. Para saber dónde mirar, primero hay que ver de qué está hecho el plazo.</p>
<p>El plazo habitual hacia ' . $p . ' es de <strong>' . $f['plazo'] . '</strong>, contados en días hábiles desde el despacho en {{origen_ciudad}}. Ese número es la suma de cuatro tramos, y conviene saber cuál es cuál.</p>
<h2>Los tramos</h2>
<ol>
<li><strong>Despacho en {{origen_ciudad}}.</strong> Recepción, pesaje, revisión y documentación. El mismo día si el envío llega listo.</li>
<li><strong>Tramo internacional.</strong> ' . $n['via'] . '</li>
<li><strong>Aduana de ' . $p . '.</strong> El único tramo cuyo tiempo no controlamos. Un envío bien declarado pasa sin detenerse.</li>
<li><strong>Entrega.</strong> ' . $f['entrega_larga'] . '</li>
</ol>
<h2>Cómo se cuentan los días</h2>
<ul>
<li>Desde el despacho, no desde que nos escribes ni desde el recojo.</li>
<li>Solo días hábiles: fines de semana y feriados —de {{origen_pais}} y de ' . $p . '— no cuentan.</li>
<li>' . ( $c3 ? 'Una ciudad grande como ' . $c[0] . ' suele recibir antes que una del interior.' : 'La capital suele recibir antes que las ciudades del interior.' ) . '</li>
</ul>
<h2>Lo que alarga un envío a ' . $p . '</h2>
<p>Una descripción vaga del contenido, un destinatario sin teléfono' . ( $d['casa'] ? ' o sin dirección exacta' : ' o sin documento para retirar' ) . ', un producto restringido y la temporada alta de diciembre. Todo menos lo último se resuelve antes de despachar. ' . $n['consejo'] . ' <a href="%H%/tiempos-de-entrega/">Cómo se cuentan los plazos</a> y <a href="%H%/destinos/' . $s . '/">la ficha de ' . $p . '</a>.</p>',
	);

	/* 4 ── qué se puede enviar */
	$out[ 'que-se-puede-enviar-a-' . $s ] = array(
		/* «Qué se puede enviar a <país>» es la página de la ruta; la guía va
		 * a lo que la aduana retiene y cómo evitarlo. */
		'slug'   => 'que-retiene-la-aduana-de-' . $s,
		'titulo' => 'Qué retiene la aduana de ' . $p . ' y cómo evitarlo',
		'categoria' => 'aduanas', 'pilar' => 'que-se-puede-enviar',
		'extracto' => 'Lo que la aduana de ' . $p . ' revisa, lo que la vía no admite y los errores de declaración que más envíos retienen.',
		'html' => '
<p>Hay dos filtros: lo que la <strong>vía</strong> permite llevar y lo que la <strong>aduana de ' . $p . '</strong> deja entrar. Un producto puede pasar el primero y no el segundo.</p>
<h2>Lo que admite la vía</h2>
' . ( $f['dos_vias']
	? '<p>Hacia ' . $p . ' hay vía aérea y terrestre, y no admiten lo mismo. Por avión no viajan líquidos, aerosoles, alimentos ni artículos con batería interna. Por carretera algunos de ellos sí, según el producto. ' . $n['via'] . '</p>'
	: '<p>Hacia ' . $p . ' operamos solo por vía aérea, y la bodega de un avión es estricta: sin líquidos, aerosoles, alimentos frescos ni artículos con batería interna. ' . $n['via'] . '</p>' ) . '
<h2>Lo que revisa la aduana de ' . $p . '</h2>
<p>' . $d['restr'] . '</p>
<p>' . $f['impuesto'] . '</p>
<h2>Lo que no viaja por ninguna vía</h2>
<p>Dinero en efectivo, armas y sus piezas, sustancias controladas, animales vivos, material inflamable y réplicas de marca en cantidad comercial.</p>
<h2>Los errores que más retienen</h2>
<ol>
<li>Declarar «regalos» o «varios» en vez de describir cada pieza.</li>
<li>Un valor declarado que no coincide con el producto.</li>
<li>Un destinatario sin teléfono' . ( $d['casa'] ? '' : ' o sin documento para retirar en agencia' ) . '.</li>
</ol>
<p>' . $n['consejo'] . ' Ante la duda, dinos el nombre exacto del producto: te decimos si entra y por qué vía. <a href="%H%/que-se-puede-enviar/">Qué se puede enviar</a> · <a href="%H%/destinos/' . $s . '/">Ficha de ' . $p . '</a>.</p>',
	);

	/* 5 ── encomiendas familiares */
	$out[ 'enviar-encomiendas-a-familiares-en-' . $s ] = array(
		'titulo' => 'Enviar regalos y encomiendas a tus familiares en ' . $p,
		'categoria' => 'destinos', 'pilar' => 'servicios/envio-internacional-de-paquetes',
		'extracto' => 'Cómo enviar una encomienda a tu familia en ' . $p . ' sin pagar de más y sin que se quede en aduana: qué mandar, cómo embalar y cómo se entrega.',
		'html' => '
<p>La encomienda familiar es el envío más común hacia ' . $p . '. Lo que más se manda desde {{origen_ciudad}} son ' . $n['productos'] . '. También es el envío que más se hace con prisa y el que más se paga de más por una caja mal elegida.</p>
<p>' . $d['lead'] . '</p>
<h2>Qué conviene mandar</h2>
<p>Lo que pesa poco y vale mucho para quien lo recibe: ropa, calzado, artículos personales, regalos. Lo que abulta y pesa —y no urge— conviene juntarlo en un solo bulto y ' . ( $f['dos_vias'] ? 'enviarlo por vía terrestre.' : 'comprimirlo bien: la ruta es aérea y el volumen se paga.' ) . ' Antes de meter algo en la caja, mira <a href="%P:que-se-puede-enviar-a-' . $s . '%">qué admite ' . $p . '</a>.</p>
<h2>Cómo embalar</h2>
<ul>
<li>Caja ajustada, sin aire: cada centímetro vacío se cobra como <a href="%H%/servicios/peso-volumetrico/">peso volumétrico</a>.</li>
<li>Cada pieza envuelta por separado; lo frágil, en doble caja.</li>
<li>Una lista del contenido dentro de la caja y la misma en la declaración, pieza por pieza.</li>
</ul>
<h2>Cómo lo recibe tu familia</h2>
<p>' . $f['entrega_larga'] . ' Comparte con ellos el número de guía: así saben cuándo esperar' . ( $d['casa'] ? ' al repartidor.' : ' el aviso de la agencia.' ) . '</p>
<h2>Cuándo enviarlo</h2>
<p>Con margen: el plazo habitual es de ' . $f['plazo'] . ' y diciembre satura las aduanas de todos los países. Para Navidad, el Día de la Madre o un cumpleaños, cuenta hacia atrás desde la fecha y suma una semana de colchón. <a href="%P:enviar-a-' . $s . '-en-fechas-clave%">Fechas clave para enviar a ' . $p . '</a>.</p>
<p>' . $n['consejo'] . '</p>
<p><a href="%H%/servicios/envio-internacional-de-paquetes/">Envío de paquetes</a> · <a href="%H%/cotizar/">Cotiza tu encomienda a ' . $p . '</a>.</p>',
	);

	/* 6 ── documentos */
	$out[ 'enviar-documentos-a-' . $s ] = array(
		'titulo' => 'Enviar documentos a ' . $p . ': apostilla, plazos y errores que evitar',
		'categoria' => 'documentos', 'pilar' => 'servicios/envio-internacional-de-documentos',
		'extracto' => 'Cómo enviar títulos, partidas, poderes y contratos a ' . $p . ' para que lleguen a tiempo y tengan validez allí.',
		'html' => '
<p>Los documentos son el envío más rápido y más barato hacia ' . $p . ': pesan poco, viajan por vía aérea y no pagan aduana. Lo que los retrasa casi nunca es el transporte, sino llegar sin la validez que ' . $p . ' exige.</p>
<h2>¿El documento sirve en ' . $p . '?</h2>
<p>Un título, una partida de nacimiento o un poder notarial son válidos en {{origen_pais}}. Para que lo sean ante una institución de ' . $p . ' suelen necesitar <a href="%H%/servicios/apostilla-y-traduccion/">apostilla</a>, y ese trámite se hace aquí, antes de enviar. Enviar rápido un documento que luego no aceptan es perder el plazo dos veces.</p>
<h2>Cómo prepararlo</h2>
<ul>
<li>Sobre rígido o carpeta que impida doblarlo; nada de grapas sobre sellos o apostillas.</li>
<li>Copia digital antes de enviar.</li>
<li>Datos completos del destinatario, con teléfono' . ( $d['casa'] ? ' y dirección exacta: la entrega es en domicilio.' : ': retira en agencia con su documento de identidad.' ) . '</li>
</ul>
<h2>Qué se declara</h2>
<p>Como lo que son —«documentos personales», «título apostillado»—, sin inventarles valor. Declararlos como mercancía es lo que los mete en aduana sin motivo.</p>
<h2>Plazo</h2>
<p>El de la ruta aérea a ' . $p . ', en días hábiles desde el despacho. Si además necesita apostilla o traducción, ese trámite tiene su propio reloj y va primero. ' . $n['consejo'] . '</p>
<p><a href="%H%/servicios/envio-internacional-de-documentos/">Envío de documentos</a> · <a href="%P:apostilla-para-enviar-documentos%">Cuándo hace falta apostilla</a>.</p>',
	);

	/* 7 ── ciudades (solo si hay lista) */
	if ( $c ) {
		$lis = '';
		foreach ( array_slice( $c, 0, 10 ) as $ciudad ) $lis .= '<li><strong>' . esc_html( $ciudad ) . '</strong>: ' . $f['entrega_corta'] . ', plazo habitual de ' . $f['plazo'] . '.</li>';
		$out[ 'ciudades-de-' . $s ] = array(
			'titulo' => 'Envíos a ' . $c3 . ' y otras ciudades de ' . $p,
			'categoria' => 'destinos', 'pilar' => $pilar,
			'extracto' => 'A qué ciudades de ' . $p . ' llegamos desde {{origen_ciudad}}, cómo se entrega en cada una y qué cambia entre la capital y el interior.',
			'html' => '
<p>Llegamos a todo ' . $p . ', pero no todas las ciudades reciben igual. Estas son las que más se piden desde {{origen_ciudad}} y lo que conviene saber de cada una.</p>
<ul>' . $lis . '</ul>
<h2>Capital frente a interior</h2>
<p>El tramo internacional es el mismo para todo el país; lo que cambia es la distribución interna. Una ciudad grande recibe antes; una del interior puede sumar días' . ( $d['casa'] ? ' de reparto.' : ' hasta que el envío llega a su agencia.' ) . ' Si tu destinatario vive fuera de estas ciudades, dinos la dirección exacta al cotizar y te confirmamos plazo y forma de entrega.</p>
<h2>La dirección, completa</h2>
<p>' . $f['entrega_larga'] . ' ' . ( $s === 'estados-unidos' ? 'En Estados Unidos, el código postal y el número de apartamento no son opcionales.' : 'Añade referencias si la dirección es difícil de encontrar.' ) . '</p>
<p><a href="%H%/destinos/' . $s . '/">Ficha completa de ' . $p . '</a> · <a href="%H%/cotizar/">Cotizar</a>.</p>',
		);
	}

	/* 8 ── fechas clave */
	$out[ 'enviar-a-' . $s . '-en-fechas-clave' ] = array(
		'titulo' => 'Enviar a ' . $p . ' en fechas clave: Navidad, Día de la Madre y vuelta a clases',
		'categoria' => 'destinos', 'pilar' => $pilar,
		'extracto' => 'Cuándo despachar un envío a ' . $p . ' para que llegue a tiempo en las fechas señaladas, con el plazo real de la ruta y el margen que conviene.',
		'html' => '
<p>Tres momentos concentran buena parte de los envíos a ' . $p . ', y los tres tienen el mismo problema: llegan de golpe a las aduanas de todos los países. La regla es sencilla: <strong>plazo de la ruta más una semana de colchón</strong>.</p>
<h2>Navidad y Reyes</h2>
<p>La temporada más saturada del año. Con un plazo habitual de ' . $f['plazo'] . ', el envío debería salir de {{origen_ciudad}} con al menos tres semanas de anticipación' . ( $f['dos_vias'] ? ', y más si va por carretera.' : '.' ) . ' Regalos declarados pieza por pieza y con su valor real: en diciembre las aduanas revisan más, no menos.</p>
<h2>Día de la Madre</h2>
<p>Envíos pequeños y personales. Como pesan poco, la vía aérea apenas encarece y da margen. ' . ( $d['casa'] ? 'La entrega es en domicilio: comparte el número de guía para que sepan cuándo esperar al repartidor.' : 'El retiro es en agencia: avisa a quien recibe para que esté pendiente del aviso.' ) . '</p>
<h2>Vuelta a clases y mudanzas de estudiantes</h2>
<p>Ropa, libros, útiles. Aquí manda el volumen: caja ajustada, ropa comprimida y' . ( $f['dos_vias'] ? ' vía terrestre si la fecha lo permite.' : ' un solo bulto bien aprovechado.' ) . ' <a href="%P:como-enviar-equipaje-por-delante%">Cómo enviar equipaje por delante</a>.</p>
<h2>Lo que no cambia con la fecha</h2>
<p>' . $n['consejo'] . ' Y la aduana de ' . $p . ' aplica las mismas reglas en diciembre que en marzo: <a href="%P:que-se-puede-enviar-a-' . $s . '%">lo que no entra, no entra</a>.</p>
<p><a href="%H%/cotizar/">Cotiza con fecha límite</a>: elegimos la vía que llega y te decimos si el margen alcanza.</p>',
	);

	/* 9 ── equipaje / mudanza */
	$out[ 'mudarse-a-' . $s . '-enviar-equipaje' ] = array(
		'titulo' => 'Mudarse a ' . $p . ': cómo enviar tu equipaje por delante',
		'categoria' => 'destinos', 'pilar' => 'servicios/envio-de-equipaje',
		'extracto' => 'Qué enviar antes de viajar a ' . $p . ', qué llevar contigo, cómo se cobra el equipaje y cómo declararlo para que no se trate como mercancía.',
		'html' => '
<p>Quien se muda a ' . $p . ' —por trabajo, estudios o familia— se encuentra con que el equipaje no cabe en el avión y el exceso se cobra por pieza. Enviar por delante lo que no necesitas el primer día suele salir más barato y te deja viajar ligero. ' . $n['via'] . '</p>
<p>' . $d['restr'] . '</p>
<h2>Qué enviar y qué llevar</h2>
<ul>
<li><strong>Enviar:</strong> ropa de temporada, libros, ropa de cama, utensilios, lo que no usarás las primeras semanas.</li>
<li><strong>Llevar contigo:</strong> documentos, medicinas, electrónica con batería, objetos de valor.</li>
</ul>
<h2>Cómo se cobra</h2>
<p>Por el mayor entre el peso real y el volumétrico de cada bulto. El equipaje abulta y pesa poco, así que comprimir en bolsas al vacío y llenar bien cada caja cambia la factura. ' . ( $f['dos_vias'] ? 'Hacia ' . $p . ' la vía terrestre suele ser la más económica para este tipo de bulto si el plazo lo permite.' : 'La ruta a ' . $p . ' es aérea: el volumen pesa en el precio, y un bulto bien aprovechado es la diferencia.' ) . '</p>
<h2>Cómo declararlo</h2>
<p>Cada bulto por separado, con una descripción honesta —«ropa usada, libros, artículos personales»— y sin mezclar mercancía nueva dentro: es lo que convierte un envío personal en una importación. ' . $f['impuesto'] . '</p>
<h2>Cómo llega</h2>
<p>' . $f['entrega_larga'] . ' Plazo habitual: ' . $f['plazo'] . ' desde el despacho. Si viajas antes de que llegue, deja a alguien que pueda recibirlo o retirarlo.</p>
<p><a href="%H%/servicios/envio-de-equipaje/">Envío de equipaje</a> · <a href="%P:como-enviar-equipaje-por-delante%">Guía general de equipaje</a> · <a href="%H%/cotizar/">Cotizar</a>.</p>',
	);

	return apply_filters( 'grenvios_bp_plantillas', $out, $d, $n );
}
