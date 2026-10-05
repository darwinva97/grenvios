<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Más contenido propio por país: precio, cobertura y preguntas de la portada
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Análisis de la portada de una ruta antes de este archivo (/ar/):
 *
 *   · El H1 —el oculto para buscadores— decía «Envíos internacionales … desde
 *     Lima, Perú» en las trece rutas. El encabezado más importante de la página
 *     no nombraba el país por el que esa página existe.
 *   · Las secciones de destinos, cotizador rápido, ventajas, proceso, opiniones
 *     y CTA seguían hablando «al mundo» y «a más de 30 países».
 *   · Las cuatro preguntas frecuentes eran las mismas en todas las rutas, y son
 *     las que se publican como FAQPage en el schema: trece páginas compitiendo
 *     con el mismo bloque de preguntas.
 *   · El bloque de país de la portada traía resumen, ruta, plazos, entrega y
 *     prohibidos. Faltaba lo que más se busca junto al nombre del país:
 *     CUÁNTO CUESTA.
 *
 * Aquí se corrige lo último de esa lista con contenido nuevo —no con más
 * repetición—: una sección de precio construida con los datos reales de cada
 * destino, y las preguntas de la portada reescritas por país. Los textos de
 * plantilla (H1 incluido) se reescriben en inc/paises-textos.php.
 *
 * Todo sale de datos que ya existen —vía, plazo, forma de entrega, impuesto—,
 * así que ningún país repite el texto de otro y nada afirma lo que no se sabe.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ═════════════════════════════════════════════════════════════════════════
 * 1) SECCIÓN NUEVA: «cuánto cuesta enviar a X»
 *
 * Se llama `grenvios_pais_seccion_precio` porque el motor de secciones resuelve
 * el nombre de la función a partir de la matriz (ver inc/paises-contenido.php).
 * El ángulo cambia por página, como en el resto de secciones.
 * ═══════════════════════════════════════════════════════════════════════ */
function grenvios_pais_seccion_precio( $d, $ang ) {
	$p   = esc_html( $d['title'] );
	$via = $d['aereo'] && $d['terr'] ? 'aérea y terrestre' : ( $d['aereo'] ? 'aérea' : 'terrestre' );
	$dos = $d['aereo'] && $d['terr'];
	$imp = $d['impuesto'] !== ''
		? ' A eso se suma el impuesto de aproximadamente ' . esc_html( $d['impuesto'] ) . ' % sobre el valor declarado, que se paga aquí, en {{origen_ciudad}}, y no en destino.'
		: ' Si la aduana de ' . $p . ' aplica algún impuesto al valor declarado, te lo decimos antes de despachar, no después.';
	$eleccion = $dos
		? 'Entre la vía aérea y la terrestre la diferencia de precio es grande: la aérea se paga por rapidez y la terrestre por volumen. Para una caja voluminosa que no corre prisa, la terrestre suele costar bastante menos.'
		: 'La ruta a ' . $p . ' se opera por vía ' . $via . ', así que el precio depende sobre todo del peso que se cobre y de la ciudad de entrega.';

	$volumetrico = 'El precio de un envío a ' . $p . ' no sale solo de la balanza: se cobra el <strong>mayor</strong> entre el peso real y el peso volumétrico, que se calcula multiplicando alto × largo × ancho en centímetros y dividiendo entre 5000. Por eso una caja grande y liviana puede costar más que una pequeña y pesada, y por eso ajustar el embalaje es la forma más rápida de bajar el precio.';

	$m = array(
		'home' => array(
			'Cuánto cuesta enviar a ' . $p,
			'<p>' . $volumetrico . '</p><p>' . $eleccion . $imp . ' Con el peso y las medidas reales te damos un precio cerrado en minutos, sin sorpresas al recoger.</p>',
		),
		'cotizar' => array(
			'Qué necesitamos para darte el precio a ' . $p,
			'<p>Con tres datos basta: qué envías, cuánto pesa y mide el bulto, y a qué ciudad de ' . $p . ' va. Con eso calculamos el peso real y el volumétrico —alto × largo × ancho ÷ 5000— y cotizamos por el mayor de los dos.</p><p>' . $eleccion . $imp . '</p>',
		),
		'envio-internacional-de-paquetes' => array(
			'Cuánto cuesta un paquete a ' . $p,
			'<p>' . $volumetrico . '</p><p>Antes de cerrar la caja, mídela ya armada: unos centímetros de más en cada lado cambian el peso volumétrico y, con él, lo que pagas por tu paquete a ' . $p . '.' . $imp . '</p>',
		),
		'carga-internacional' => array(
			'Cómo se cotiza la carga a ' . $p,
			'<p>En carga el precio deja de mirar el bulto suelto y empieza a mirar el conjunto: peso total, volumen ocupado y tipo de mercancía. Se aplica el mismo criterio de peso real frente a volumétrico, pero sobre el consolidado.</p><p>' . $eleccion . $imp . ' Para volúmenes estables hacia ' . $p . ' trabajamos con tarifa acordada, no con el precio del envío puntual.</p>',
		),
		'peso-volumetrico' => array(
			'El peso volumétrico en la ruta a ' . $p,
			'<p>' . $volumetrico . '</p><p>En la ruta a ' . $p . ' el criterio es el mismo que en el resto de destinos ' . ( $dos ? 'aéreos y terrestres' : 'por vía ' . $via ) . '. Cambia el precio por kilo, no la forma de calcular el peso que se cobra.</p>',
		),
		'envio-de-compras' => array(
			'Cuánto cuesta traer tus compras a ' . $p,
			'<p>El flete se calcula por el mayor entre el peso real y el volumétrico del paquete ya consolidado. Si compras en varias tiendas y lo juntamos todo en un solo envío a ' . $p . ', pagas un solo flete en vez de uno por tienda.</p><p>' . $imp . ' Declarar por debajo del valor real no abarata nada: deja el envío sin cobertura si algo pasa.</p>',
		),
		'envios-para-empresas' => array(
			'Tarifas para envíos recurrentes a ' . $p,
			'<p>El precio de un envío puntual a ' . $p . ' no es el precio de cincuenta envíos al mes. Con frecuencia estable acordamos una tarifa por volumen y un recojo programado, y la facturación se consolida al mes.</p><p>' . $eleccion . $imp . '</p>',
		),
		'envio-de-equipaje' => array(
			'Cuánto cuesta enviar tu equipaje a ' . $p,
			'<p>Una maleta se cobra como un bulto más: por el mayor entre su peso real y su volumen. Frente al exceso de equipaje de la aerolínea, suele salir a cuenta a partir de la segunda maleta, sobre todo hacia ' . $p . '.</p><p>' . $eleccion . $imp . '</p>',
		),
	);

	if ( ! isset( $m[ $ang ] ) ) return '';
	return grenvios_pais_sec( '', $m[ $ang ][0], $m[ $ang ][1] );
}

/* ═════════════════════════════════════════════════════════════════════════
 * 2) MÁS SECCIONES POR PÁGINA
 *
 * La portada de la ruta es la página que más tráfico recibe y la que menos
 * contenido propio llevaba: cinco secciones. Se le añade el precio (lo más
 * buscado junto al nombre del país), lo que más se envía y la cobertura de
 * ciudades. Las secciones sin datos devuelven '' solas, así que un país a
 * medio rellenar no pinta un hueco.
 * ═══════════════════════════════════════════════════════════════════════ */
add_filter( 'grenvios_pais_matriz', function ( $m ) {
	$m['home'] = array( 'resumen', 'ruta', 'precio', 'plazos', 'entrega', 'top', 'prohibidos', 'ciudades' );

	// El precio también entra donde la intención de búsqueda es económica.
	$extra = array(
		'cotizar'                         => 'precio',
		'envio-internacional-de-paquetes' => 'precio',
		'carga-internacional'             => 'precio',
		'peso-volumetrico'                => 'precio',
		'envio-de-compras'                => 'precio',
		'envios-para-empresas'            => 'precio',
		'envio-de-equipaje'               => 'precio',
	);
	foreach ( $extra as $slug => $sec ) {
		if ( ! isset( $m[ $slug ] ) || in_array( $sec, $m[ $slug ], true ) ) continue;
		// Segunda posición: después del resumen, antes del resto.
		array_splice( $m[ $slug ], 1, 0, array( $sec ) );
	}
	return $m;
} );

/* ═════════════════════════════════════════════════════════════════════════
 * 3) PREGUNTAS FRECUENTES DE LA PORTADA, POR PAÍS
 *
 * Son las que salen como FAQPage en el schema. Las de la ficha de destino ya
 * cubren plazo y forma de entrega, así que estas atacan otro ángulo —precio,
 * recojo, seguimiento y qué admite la aduana— para no repetirlas.
 * ═══════════════════════════════════════════════════════════════════════ */
add_filter( 'grenvios_page_faqs', function ( $faqs, $slug ) {
	if ( $slug !== 'home' || is_admin() ) return $faqs;
	if ( ! function_exists( 'grenvios_hq_pais' ) ) return $faqs;

	$pais = grenvios_hq_pais();
	if ( $pais === '' ) return $faqs;

	$d = function_exists( 'grenvios_pais_datos' ) && function_exists( 'grenvios_i18n_current' )
		? grenvios_pais_datos( grenvios_i18n_current() )
		: array();

	$tiempo = ! empty( $d['tiempo'] ) ? $d['tiempo'] : '';
	$via    = ! empty( $d['aereo'] ) && ! empty( $d['terr'] )
		? 'por vía aérea y terrestre'
		: ( ! empty( $d['aereo'] ) ? 'por vía aérea' : ( ! empty( $d['terr'] ) ? 'por vía terrestre' : '' ) );
	$ent    = ! empty( $d['casa'] ) ? 'con entrega en el domicilio del destinatario' : 'con retiro en agencia local';
	$imp    = ! empty( $d['impuesto'] )
		? ' El impuesto de aproximadamente ' . $d['impuesto'] . ' % sobre el valor declarado se paga en {{origen_ciudad}}, no en destino.'
		: '';

	$out = array(
		array(
			'¿Cuánto cuesta enviar a ' . $pais . ' desde {{origen_ciudad}}?',
			'Se cobra el mayor entre el peso real y el peso volumétrico (alto × largo × ancho en cm ÷ 5000), y sobre ese peso influyen la modalidad y la ciudad de entrega en ' . $pais . '.' . $imp . ' Envíanos peso y medidas y te damos precio cerrado en minutos.',
		),
		array(
			'¿Cuánto demora un envío de {{origen_ciudad}} a ' . $pais . '?',
			$tiempo !== ''
				? 'El plazo habitual hacia ' . $pais . ' es de ' . $tiempo . ' ' . $via . ', contados en días hábiles desde el despacho en {{origen_ciudad}}. El paso por aduana puede sumar días que no dependen del transporte.'
				: 'El plazo depende de la modalidad ' . $via . ' y de la ciudad de destino en ' . $pais . '. Te confirmamos el tiempo estimado al cotizar, contado en días hábiles desde {{origen_ciudad}}.',
		),
		array(
			'¿Qué puedo enviar a ' . $pais . ' y qué no?',
			'A ' . $pais . ' se envía lo habitual: documentos, ropa, regalos, repuestos, compras y carga comercial. Lo que cambia de un país a otro son las restricciones de su aduana —líquidos, alimentos, medicinas o artículos con batería según la vía—, y las revisamos contigo antes de despachar.',
		),
		array(
			'¿Recogen mi paquete en {{origen_ciudad}} para enviarlo a ' . $pais . '?',
			'Sí. Coordinamos el recojo en tu casa, oficina o donde esté tu proveedor dentro de {{origen_ciudad}}, lo pesamos, lo embalamos y lo despachamos hacia ' . $pais . '. El recojo no altera el plazo de tránsito.',
		),
		array(
			'¿Cómo recibe mi envío el destinatario en ' . $pais . '?',
			'La entrega en ' . $pais . ' se hace ' . $ent . '. Desde que se despacha en {{origen_ciudad}} el envío lleva número de guía, y un asesor te confirma por WhatsApp en qué punto del trayecto está.',
		),
	);

	return apply_filters( 'grenvios_home_faqs_pais', $out, $pais, $d );
}, 10, 2 );
