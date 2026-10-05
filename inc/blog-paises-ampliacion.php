<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Ampliación de las guías por país
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Las nueve plantillas por destino generaban entradas de unas 300 palabras más
 * el bloque del país. Aquí se les añaden dos secciones y sus preguntas, y como
 * las plantillas son data-driven, la ampliación también ramifica: la guía de un
 * país solo aéreo no repite lo que dice la de uno con carretera, y la de uno
 * con impuesto publicado lo cuantifica.
 *
 * Se engancha al filtro `grenvios_bp_plantillas`, así que el importador la
 * recoge sin cambios y se propaga a las nueve rutas.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Preguntas al pie, con el mismo marcado que las guías generales. */
function grenvios_bpa_faq( $pares ) {
	return function_exists( 'grenvios_ga_faq' ) ? grenvios_ga_faq( $pares ) : '';
}

add_filter( 'grenvios_bp_plantillas', function ( $out, $d, $n ) {
	$p    = $d['title'];
	$s    = $d['slug'];
	$f    = grenvios_bp_frases( $d );
	$dos  = $f['dos_vias'];
	$casa = ! empty( $d['casa'] );
	$c    = $f['ciudades'];
	$c1   = $c ? $c[0] : '';
	$imp  = $d['impuesto'] !== '';

	/* Frases reutilizables que ya dependen del país. */
	$via_larga = $dos
		? 'Hacia ' . $p . ' puedes elegir entre la vía aérea y la terrestre, y la diferencia de precio en bultos voluminosos es grande.'
		: 'Hacia ' . $p . ' operamos por vía aérea, así que el volumen del bulto pesa mucho en la tarifa: comprimir y ajustar la caja es lo que más ahorra.';
	$entrega_detalle = $casa
		? 'Como la entrega es en el domicilio del destinatario, el teléfono y la dirección exacta son los dos datos que deciden si el reparto sale bien a la primera. Si la dirección es difícil de encontrar, añade una referencia.'
		: 'Como el retiro es en la agencia local, tu destinatario necesita su documento de identidad y estar atento al aviso de llegada. Conviene avisarle de que tendrá que ir a recogerlo.';
	$imp_detalle = $imp
		? 'En esta ruta se aplica un impuesto aproximado del ' . $d['impuesto'] . ' % sobre el valor declarado en los envíos terrestres. Se paga aquí, en {{origen_ciudad}}, antes de despachar, así que quien recibe no adelanta nada.'
		: 'Si la aduana de ' . $p . ' aplica algún cargo sobre el valor declarado, te lo decimos antes de despachar. Lo que nunca hacemos es dejar que aparezca en la entrega.';

	$añadir = function ( $key, $html ) use ( &$out ) {
		if ( ! isset( $out[ $key ] ) ) return;
		$out[ $key ]['html'] = rtrim( $out[ $key ]['html'] ) . "\n" . $html;
	};

	/* 1 ── paso a paso */
	$añadir( 'como-enviar-un-paquete-a-' . $s, '
<h2>Errores que retrasan un envío a ' . $p . '</h2>
<ol>
<li><strong>Declarar «regalos» o «varios».</strong> Es la causa número uno de revisión en la aduana de destino. Describe pieza por pieza.</li>
<li><strong>Un destinatario incompleto.</strong> ' . ( $casa ? 'Sin teléfono, el repartidor no puede resolver una dirección dudosa.' : 'Sin teléfono, tu destinatario no recibe el aviso de que ya puede retirar.' ) . '</li>
<li><strong>Una caja demasiado grande.</strong> No retrasa, pero encarece: el aire se cobra como <a href="%H%/servicios/peso-volumetrico/">peso volumétrico</a>.</li>
<li><strong>Meter algo restringido</strong> sin consultarlo. ' . $n['consejo'] . '</li>
</ol>
' . grenvios_bpa_faq( array(
		array( '¿Cuánto tarda en salir mi envío a ' . $p . '?', 'El despacho se resuelve el mismo día si traes el bulto listo. A partir de ahí corre el plazo de la ruta, ' . $f['plazo'] . ', contado en días hábiles.' ),
		array( '¿Puedo enviar a ' . $p . ' sin acercarme a la oficina?', 'Sí. Coordinamos el recojo en tu domicilio dentro de {{origen_ciudad}} y hacemos ahí mismo la revisión y el embalaje.' ),
	) ) );

	/* 2 ── precio */
	$añadir( 'cuanto-cuesta-enviar-a-' . $s, '
<h2>Cómo bajar el precio de tu envío a ' . $p . '</h2>
<ul>
<li><strong>Ajusta la caja.</strong> ' . $via_larga . '</li>
<li><strong>Junta los envíos.</strong> Tres paquetes pequeños pagan tres fletes; consolidados, uno.</li>
<li><strong>Declara el valor real.</strong> ' . $imp_detalle . '</li>
</ul>
<p>Si dudas entre dos formas de mandarlo, cotiza las dos: te damos el precio de cada una y decides con números. <a href="%H%/cotizar/">Pedir cotización</a>.</p>
' . grenvios_bpa_faq( array(
		array( '¿El precio a ' . $p . ' incluye la entrega?', $casa ? 'Sí, la tarifa cubre el trayecto hasta el domicilio del destinatario.' : 'Sí, la tarifa cubre el trayecto hasta la agencia local donde tu destinatario retira el envío.' ),
		array( '¿Qué se suma aparte del flete?', ( $imp ? 'El impuesto sobre el valor declarado en los envíos terrestres, que se paga aquí antes de despachar, y ' : '' ) . 'el recojo a domicilio si no traes el bulto a la oficina. Todo se te dice antes de decidir.' ),
	) ) );

	/* 3 ── plazos */
	$añadir( 'cuanto-demora-un-envio-a-' . $s, '
<h2>Cuándo despachar para llegar a tiempo</h2>
<p>Con un plazo habitual de ' . $f['plazo'] . ', la regla para una fecha concreta es sencilla: <strong>ese plazo más una semana de colchón</strong>, y más aún en diciembre, que es cuando todas las aduanas se saturan a la vez. Cuenta hacia atrás desde la fecha en que tu destinatario necesita el envío, no desde la fecha en que puedes despacharlo.</p>
' . grenvios_bpa_faq( array(
		array( '¿Por qué mi envío a ' . $p . ' lleva días sin actualizarse?', ( $dos ? 'Si va por carretera, el estado cambia con menos frecuencia que por avión y es normal. ' : '' ) . 'Si ya se cumplió el plazo estimado, escríbenos con el número de guía: casi siempre es el paso por aduana y podemos decirte exactamente qué falta.' ),
		array( '¿Los días incluyen fines de semana?', 'No. Son días hábiles desde el despacho, y los feriados de {{origen_pais}} y de ' . $p . ' tampoco cuentan.' ),
	) ) );

	/* 4 ── qué se puede enviar */
	$añadir( 'que-se-puede-enviar-a-' . $s, '
<h2>Cómo consultar un producto concreto</h2>
<p>Dinos el <strong>nombre exacto</strong> —marca y modelo si es electrónica, principio activo si es medicina— y te decimos si entra a ' . $p . ', por qué vía y con qué condiciones. Es una consulta de dos minutos que evita comprar algo que después no va a poder salir, porque si la aduana lo retiene el flete no se devuelve.</p>
' . grenvios_bpa_faq( array(
		array( '¿Puedo enviar electrónica a ' . $p . '?', ( $dos ? 'Por vía aérea no, si lleva batería interna; por carretera se admite en esta ruta. Dinos el aparato y te confirmamos.' : 'Los artículos con batería interna no viajan en bodega de avión, y esta ruta es aérea. Consúltanos el caso concreto antes de comprar.' ) ),
		array( '¿Qué pasa si envío algo no permitido?', 'La aduana puede retenerlo, devolverlo o destruirlo, y el flete no se devuelve. Por eso revisamos el contenido contigo antes de despachar.' ),
	) ) );

	/* 5 ── encomiendas */
	$añadir( 'enviar-encomiendas-a-familiares-en-' . $s, '
<h2>Cómo lo recibe tu familia en ' . $p . '</h2>
<p>' . $entrega_detalle . ' Comparte con ellos el número de guía desde el primer día: si hay cualquier incidencia en destino, la resuelven mucho más rápido que tú desde aquí.</p>
' . grenvios_bpa_faq( array(
		array( '¿Puedo enviar ropa usada a mi familia en ' . $p . '?', 'Sí, y es de lo que más se envía. Declárala como «ropa usada de uso personal» con un valor razonable de segunda mano: es lo que corresponde y evita que se trate como mercancía nueva.' ),
		array( '¿Puedo mandar varias cajas a la vez?', 'Sí. Se cotiza el conjunto y suele salir mejor que enviarlas por separado en semanas distintas.' ),
	) ) );

	/* 6 ── documentos */
	$añadir( 'enviar-documentos-a-' . $s, '
<h2>Si el documento es para un trámite</h2>
<p>La mayoría de los documentos que salen hacia ' . $p . ' van a presentarse ante una institución: una universidad, un registro, un consulado. En ese caso lo que decide no es la rapidez del envío, sino que el documento llegue con la validez que piden allí. Comprueba antes si necesita <a href="%H%/servicios/apostilla-y-traduccion/">apostilla o traducción</a>, porque ese trámite se hace aquí y tiene su propio plazo.</p>
' . grenvios_bpa_faq( array(
		array( '¿Los documentos pagan impuesto al entrar a ' . $p . '?', 'No. Los documentos personales y legales no tienen valor comercial y no tributan, siempre que se declaren como lo que son y no se les invente un valor.' ),
		array( '¿Cuánto tarda un documento a ' . $p . '?', 'Viajan por vía aérea, que es la modalidad más rápida de la ruta. El plazo se cuenta en días hábiles desde el despacho y puedes seguirlo con tu número de guía.' ),
	) ) );

	/* 7 ── ciudades */
	if ( isset( $out[ 'ciudades-de-' . $s ] ) && $c1 !== '' ) {
		$añadir( 'ciudades-de-' . $s, '
<h2>Qué poner en la dirección</h2>
<p>' . $entrega_detalle . ' Una dirección completa en ' . $p . ' lleva: nombre y apellidos del destinatario, calle y número, ' . ( $s === 'estados-unidos' ? 'número de apartamento o unidad, ciudad, estado y código postal (ZIP)' : 'ciudad, provincia o estado y código postal si lo tiene' ) . ', y un teléfono móvil que conteste.</p>
' . grenvios_bpa_faq( array(
			array( '¿Llegan a ciudades pequeñas de ' . $p . '?', 'Sí. El tramo internacional es el mismo para todo el país; lo que cambia es la distribución interna, que puede sumar días. Dinos la dirección exacta al cotizar y te confirmamos plazo y forma de entrega.' ),
			array( '¿Cambia el precio según la ciudad?', 'El tramo internacional no cambia dentro del mismo país. Si el destino final está lejos del punto de entrada, te lo indicamos al cotizar.' ),
		) ) );
	}

	/* 8 ── fechas clave */
	$añadir( 'enviar-a-' . $s . '-en-fechas-clave', '
<h2>Por qué diciembre no es como el resto del año</h2>
<p>No es que el transporte vaya más lento: es que llega de golpe a las aduanas de todos los países a la vez, y las revisiones se acumulan. Un envío que en marzo pasa sin detenerse, en diciembre puede esperar unos días en el mismo trámite. Por eso la recomendación no es pagar más rápido, sino <strong>despachar antes</strong>: es lo único que de verdad compensa la saturación.</p>
' . grenvios_bpa_faq( array(
		array( '¿Cuándo es la fecha límite para que llegue por Navidad a ' . $p . '?', 'Cuenta el plazo de la ruta, ' . $f['plazo'] . ', más una semana de margen por la saturación de diciembre. Escríbenos con tu fecha y te decimos el último día recomendado para despachar.' ),
		array( '¿Puedo programar un envío para que llegue en una fecha?', 'Podemos elegir la vía que mejor se ajuste a tu fecha límite y decirte si el margen alcanza. Lo que no controla ninguna empresa de envíos es el tiempo exacto de aduana.' ),
	) ) );

	/* 9 ── mudanza */
	$añadir( 'mudarse-a-' . $s . '-enviar-equipaje', '
<h2>El inventario, caja por caja</h2>
<p>Antes de cerrar cada bulto, anota qué va dentro y fotografíalo abierto. Sirve para declarar con precisión —que es lo que evita que abran las cajas en ' . $p . '—, para saber dónde está cada cosa al llegar y como prueba si hay que reclamar. Numera las cajas y guarda la lista en el teléfono.</p>
' . grenvios_bpa_faq( array(
		array( '¿El equipaje usado paga impuestos en ' . $p . '?', 'El equipaje personal usado suele tener un trato distinto al de la mercancía nueva, pero cada país fija sus límites. Lo importante: no mezcles productos nuevos con etiqueta dentro de las cajas de equipaje.' ),
		array( '¿Cuándo conviene enviarlo?', 'Antes de viajar, con margen suficiente para que el envío vaya en camino mientras tú resuelves el traslado. Si llegas antes que las cajas, deja a alguien que pueda recibirlas o retirarlas.' ),
	) ) );

	/* ── Refuerzo de las cuatro plantillas que quedaban más cortas ──────
	 * Son las que más ramifican con los datos del país, así que al quitarles
	 * las frases que no aplican se quedaban en la mitad que las demás. */

	$añadir( 'enviar-documentos-a-' . $s, '
<h2>Qué documentos se envían más a ' . $p . '</h2>
<ul>
<li><strong>Académicos:</strong> títulos, certificados de notas y constancias, para convalidaciones y postulaciones.</li>
<li><strong>Civiles:</strong> partidas de nacimiento y matrimonio, antecedentes penales, para trámites de residencia.</li>
<li><strong>Notariales:</strong> poderes y declaraciones juradas, para que alguien gestione algo en tu nombre.</li>
<li><strong>Laborales y comerciales:</strong> certificados de trabajo, contratos firmados.</li>
</ul>
<p>Todos tienen algo en común: se presentan ante una institución, y ahí lo que decide no es la rapidez sino la validez. Por eso el trámite de apostilla va antes que el envío, nunca después.</p>

<h2>Cómo preparar el sobre</h2>
<p>Sobre rígido o carpeta de cartón que impida que el documento se doble, sin grapas ni clips sobre los sellos, y una copia digital guardada antes de despachar. Si el documento viaja junto a otras cosas, el envío deja de ser «solo documentos» y se declara por todo lo que lleva: para algo urgente, mejor que vaya solo.</p>
' );

	$añadir( 'cuanto-demora-un-envio-a-' . $s, '
<h2>Qué puedes hacer para que no se alargue</h2>
<ul>
<li><strong>Describe el contenido pieza por pieza.</strong> Es lo que más acelera el paso por la aduana de ' . $p . ': una descripción vaga invita a la revisión.</li>
<li><strong>Da un teléfono que conteste en destino.</strong> ' . ( $casa ? 'Es lo que usa el repartidor cuando la dirección no cuadra.' : 'Es por donde llega el aviso de que el envío está disponible para retirar.' ) . '</li>
<li><strong>Despacha temprano en la semana.</strong> Un envío entregado el viernes por la tarde empieza a contar el lunes.</li>
<li><strong>Consulta antes lo que envías.</strong> Un producto restringido no se retrasa: se queda.</li>
</ul>

<h2>Cómo saber si va bien</h2>
<p>Compara siempre con el plazo estimado de la ruta. Mientras no se haya cumplido, que el estado no cambie durante unos días es normal' . ( $dos ? ', sobre todo si el envío va por carretera' : '' ) . '. Si se cumplió y sigue sin moverse, escríbenos con el número de guía: consultamos directamente el tramo donde está. <a href="%H%/rastreo-de-envios/">Seguimiento de envíos</a>.</p>
' );

	$añadir( 'que-se-puede-enviar-a-' . $s, '
<h2>Lo que sí entra sin problema</h2>
<p>La mayoría de lo que la gente envía a ' . $p . ' no tiene ninguna restricción: ropa y calzado, libros, juguetes, artículos de bebé, herramientas de mano, repuestos, artesanía, regalos y documentos. Lo que hay que cuidar en todos ellos es la descripción y el valor declarado, no el permiso.</p>

<h2>Lo que hay que consultar antes de comprar</h2>
<ul>
<li><strong>Cosméticos y perfumes:</strong> son líquidos y llevan alcohol.</li>
<li><strong>Suplementos y vitaminas:</strong> dependen del control sanitario de ' . $p . '.</li>
<li><strong>Electrónica con batería interna:</strong> la restricción es de la vía, no del país.</li>
<li><strong>Alimentos:</strong> solo envasados de fábrica y no perecibles, donde se admiten.</li>
<li><strong>Prendas de marca en cantidad:</strong> pasan a tratarse como mercancía comercial.</li>
</ul>
<p>La consulta útil incluye el nombre exacto del producto, no la categoría: «perfume de 100 ml» tiene respuesta; «cosas de belleza», no.</p>
' );

	$añadir( 'cuanto-cuesta-enviar-a-' . $s, '
<h2>Por qué te pedimos las medidas</h2>
<p>Porque sin ellas cualquier precio es una estimación. Se cobra el mayor entre el peso real y el volumétrico, y el volumétrico solo se puede calcular con el alto, el largo y el ancho de la caja cerrada. Dos envíos de cuatro kilos pueden costar muy distinto si uno va en una caja ajustada y el otro en una que le sobra la mitad.</p>
<p>Con esos tres números y la ciudad de destino en ' . $p . ' te damos precio cerrado en minutos, y si vemos que estás pagando volumen de más, te lo decimos antes de despachar.</p>
' );

	return $out;
}, 10, 3 );
