<?php
/**
 * Más preguntas frecuentes y más contenido en las páginas delgadas (2026-10-08).
 *
 * AUDITORÍA sobre el sitio en vivo (434 páginas, sin entradas ni copias -N):
 *   · 220 páginas con 3 preguntas frecuentes o menos (24 por ruta de país).
 *   · 20 páginas de la ruta principal con menos de 700 palabras de contenido
 *     y 5 tipos de página que en las rutas tampoco llegan.
 *
 * Mismo criterio de redacción que el resto del tema: cero cifras inventadas.
 * Las respuestas por país salen de los datos del gestor de destinos (plazo,
 * vías, forma de entrega e impuesto de las rutas terrestres); lo demás, solo lo
 * que el sitio ya afirma (peso volumétrico ÷ 5000, impuesto terrestre pagado en
 * {{origen_ciudad}}, seguimiento por guía, recojo a domicilio). Precios y plazos
 * que no están en el gestor: «te lo confirmamos al cotizar».
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ═════════════════════════════════════════════════════════════════════════
 * 1) Preguntas frecuentes: hasta 7 por página
 * ═════════════════════════════════════════════════════════════════════════ */

/* Datos del país de la ruta actual (o null en la ruta principal). */
function grenvios_fa_pais() {
	$s = function_exists( 'grenvios_perfil_pais_actual' ) ? grenvios_perfil_pais_actual() : '';
	if ( $s === '' || ! function_exists( 'grenvios_dsec_red' ) ) return null;
	$red = grenvios_dsec_red();
	return isset( $red[ $s ] ) ? $red[ $s ] : null;
}

/* Países con vía terrestre, de los datos (para la ruta principal). */
function grenvios_fa_terrestres() {
	if ( ! function_exists( 'grenvios_dsec_red' ) ) return '';
	$n = array();
	foreach ( grenvios_dsec_red() as $d ) if ( ! empty( $d['terr'] ) ) $n[] = $d['title'];
	if ( count( $n ) < 2 ) return implode( '', $n );
	$ult = array_pop( $n );
	return implode( ', ', $n ) . ' y ' . $ult;
}

/* Preguntas comunes: [ patrón que ya la cubre, pregunta, respuesta ]. */
function grenvios_fa_comunes( $p ) {
	$t    = $p ? $p['title'] : '';
	$dest = $p ? 'a ' . $t : 'al extranjero';
	$plazo = ( $p && trim( (string) $p['tiempo'] ) !== '' && stripos( $p['tiempo'], 'consultar' ) === false )
		? 'El plazo hacia ' . $t . ' es de ' . $p['tiempo'] . ( stripos( $p['tiempo'], 'hábil' ) === false ? ' hábiles' : '' ) . ', contados desde que el envío sale de {{origen_ciudad}}, no desde que lo dejas en el mostrador.'
		: 'Depende del destino y de la vía. Cada país tiene su plazo en su ficha de destino, y te lo confirmamos al cotizar. Se cuenta en días hábiles desde que el envío sale de {{origen_ciudad}}.';
	if ( $p ) {
		$via = ! empty( $p['terr'] )
			? 'A ' . $t . ' puedes enviar por vía aérea o terrestre. La aérea es la rápida; la terrestre admite más tipos de contenido y conviene para lo que abulta y no corre prisa.'
			: 'A ' . $t . ' se envía por vía aérea: no hay ruta por carretera desde {{origen_ciudad}}. Por avión no viajan líquidos, alimentos, aerosoles ni artículos con batería interna.';
		$entrega = ! empty( $p['casa'] )
			? 'En ' . $t . ' el envío se entrega a domicilio, en la dirección que indiques. Hace falta la dirección completa con referencias y un teléfono local que conteste.'
			: 'En ' . $t . ' el envío se retira en la agencia local que corresponde. El destinatario recibe el aviso y lo recoge con el documento de identidad a cuyo nombre va el envío.';
		$imp = ( ! empty( $p['terr'] ) && $p['impuesto'] !== null )
			? 'Por vía terrestre se paga un impuesto aproximado del ' . rtrim( rtrim( number_format( (float) $p['impuesto'], 1, ',', '' ), '0' ), ',' ) . ' % sobre el valor declarado, y se cancela en {{origen_ciudad}} al despachar: quien recibe en ' . $t . ' no adelanta dinero. Por vía aérea depende del contenido y del valor, y te lo indicamos al cotizar.'
			: 'Depende de lo que envías y de su valor declarado: un documento no tiene valor comercial, una compra nueva sí. Antes de despachar te decimos qué puede cobrar la aduana de ' . $t . '.';
	} else {
		$terr = grenvios_fa_terrestres();
		$via = 'Por vía aérea a todos nuestros destinos' . ( $terr !== '' ? ' y por vía terrestre a ' . $terr : '' ) . '. La aérea es la rápida; la terrestre admite más tipos de contenido y conviene para lo que abulta y no corre prisa.';
		$entrega = 'Según el país, a domicilio o en una agencia local donde el destinatario lo retira con su documento de identidad. La ficha de cada destino indica cuál corresponde.';
		$imp = 'En las rutas terrestres el impuesto se paga en {{origen_ciudad}} al despachar, así que quien recibe no adelanta dinero. En las aéreas depende del contenido y del valor declarado, y te lo confirmamos antes de enviar.';
	}
	return array(
		array( 'demora|tarda|plazo|cuánto tiempo', '¿Cuánto demora un envío ' . $dest . '?', $plazo ),
		array( 'vía|aére|terrestre|tierra|avión|carretera', $p ? '¿Por qué vía se envía a ' . $t . '?' : '¿Envían por avión o por carretera?', $via ),
		array( 'recibe|entrega|retira|recoge (?:la|el) (?:encomienda|envío) en', $p ? '¿Cómo se entrega el envío en ' . $t . '?' : '¿Cómo recibe el envío la persona en destino?', $entrega ),
		array( 'cuesta|precio|cobra|tarifa|pagar menos', '¿Cómo se calcula el precio de un envío ' . $dest . '?', 'Se cobra el mayor entre el peso real y el peso volumétrico (largo × ancho × alto en centímetros, dividido entre 5000), según la vía y el destino. Con peso, medidas y contenido te damos el precio exacto al cotizar.' ),
		array( 'rastre|seguimiento|número de guía', '¿Puedo seguir mi envío ' . $dest . '?', 'Sí. Al despachar recibes un número de guía y con él consultas el estado en cada etapa, incluido el paso por aduana, en nuestra página de rastreo.' ),
		array( 'impuesto|aduana|pagar algo', $p ? '¿Paga impuestos un envío a ' . $t . '?' : '¿Mi envío pagará impuestos en destino?', $imp ),
		array( 'recojo|recogen|domicilio en', '¿Recogen el envío en mi casa?', 'Sí, recogemos en casa, oficina o donde tu proveedor dentro de {{origen_ciudad}}. Lo pesamos, lo medimos y lo despachamos' . ( $p ? ' a ' . $t : '' ) . '; coordinamos el día al cotizar.' ),
		array( 'datos del destinatario|qué necesit', '¿Qué datos necesito de quien recibe' . ( $p ? ' en ' . $t : '' ) . '?', 'Su nombre completo tal como figura en su documento de identidad, la dirección completa con referencias y un teléfono que conteste. Con eso se evita la mayoría de las demoras en la entrega.' ),
		array( 'cotiz', '¿Cómo cotizo mi envío' . ( $p ? ' a ' . $t : '' ) . '?', 'Envíanos peso, medidas, contenido y destino por el formulario de cotización o por WhatsApp. Te respondemos con el precio y el plazo, sin compromiso.' ),
	);
}

/* Preguntas propias de cada tipo de página (además de las comunes). */
function grenvios_fa_propias( $slug, $p ) {
	$dest = $p ? 'a ' . $p['title'] : 'al extranjero';
	$solo_aire = $p && empty( $p['terr'] );   // ruta sin carretera (Estados Unidos, España…)
	$b = array(
		'carga-aerea-internacional' => array(
			array( 'no viaja|no se puede|prohib', '¿Qué no puede ir como carga aérea?', 'Por avión no viajan líquidos, alimentos, aerosoles ni artículos con batería interna. Si tu carga incluye algo de eso, revisamos contigo si hay vía terrestre al destino o cómo separarlo.' ),
		),
		'carga-terrestre-internacional' => array(
			array( 'ventaja|conviene', '¿Cuándo conviene la carga terrestre?', 'Cuando la carga abulta y la fecha no aprieta: admite más tipos de contenido que el avión —líquidos sellados, artículos con batería— y el impuesto se paga en {{origen_ciudad}} al despachar.' ),
		),
		'mudanzas-internacionales' => array(
			array( 'prepar|cajas', '¿Cómo preparo las cajas de una mudanza?', 'Numera cada caja y haz una lista de lo que lleva con su valor aproximado: es tu declaración de contenido. Ciérralas con cinta en forma de H y separa lo frágil y lo que tenga batería o líquido.' ),
		),
		'traduccion-oficial-de-documentos' => array(
			array( 'juntos|mismo sobre', '¿Puedo enviar el original y la traducción juntos?', 'Sí. Apostillamos, traducimos y enviamos el documento listo para presentar, en un sobre protegido y con número de guía para seguirlo.' ),
		),
		'envio-de-repuestos-al-extranjero' => array(
			array( 'batería', '¿Se pueden enviar repuestos con batería' . ( $p ? ' a ' . $p['title'] : '' ) . '?', $solo_aire
				? 'A ' . $p['title'] . ' solo se envía por avión, y por avión no se admiten piezas con batería. Envía la pieza sin ella o consúltanos alternativas antes de comprar.'
				: 'Por vía aérea no se admiten piezas con batería. Por vía terrestre sí se admiten, salvo hacia Chile. Si tu repuesto la lleva, revisamos contigo la vía antes de despachar.' ),
		),
		'envio-de-correspondencia-internacional' => array(
			array( 'proteg|sobre', '¿Cómo va protegida una carta?', 'En un sobre de envío sellado. Si lleva fotografías, dibujos o papeles que no se pueden doblar, usamos un sobre rígido para que lleguen planos.' ),
		),
		'envio-de-ropa-al-extranjero' => array(
			array( 'volumen|ocupa|compact', '¿Cómo hago que una caja de ropa cueste menos?', 'La ropa pesa poco y ocupa mucho, así que suele cobrarse por volumen. Dóblala o enróllala, usa una caja ajustada al contenido y no dejes huecos: cada centímetro cuenta en el peso volumétrico.' ),
		),
		'envio-express-internacional' => array(
			array( 'cómo funciona|express', '¿Cómo funciona un envío express ' . $dest . '?', 'Va por vía aérea, que es la más rápida. El plazo exacto te lo confirmamos al cotizar, según el destino y el contenido.' ),
		),
		'envio-de-artesanias-al-extranjero' => array(
			array( 'declar', '¿Cómo se declaran las artesanías?', 'Una línea por tipo de pieza, con su material, cantidad y valor real. Una descripción vaga como «adornos» es lo que más revisiones genera en aduana.' ),
		),
		'envio-de-muestras-comerciales' => array(
			array( 'documento|proforma', '¿Qué documento acompaña una muestra?', 'Una factura proforma: una factura sin valor fiscal que se usa para declarar muestras cuando no hay venta, con la descripción y el valor de cada artículo.' ),
		),
		'envio-de-regalos-al-extranjero' => array(
			array( 'tarjeta|carta', '¿Puedo incluir una tarjeta o una carta en la caja?', 'Sí. Una tarjeta o una carta personal puede ir dentro del regalo. Lo que no conviene es esconder nada: todo el contenido se declara.' ),
		),
		'envio-de-celulares-y-laptops' => array(
			array( 'declar', '¿Cómo se declara un celular o una laptop?', 'Con su marca, modelo y valor real, y si es nuevo o usado. Si es nuevo, conviene llevar la boleta de compra para declarar el valor.' ),
		),
		'envio-de-libros-al-extranjero' => array(
			array( 'terrestre|carretera', '¿Los libros pueden ir por vía terrestre' . ( $p ? ' a ' . $p['title'] : '' ) . '?', $solo_aire
				? 'No: a ' . $p['title'] . ' no hay ruta por carretera desde {{origen_ciudad}}, así que van por avión. Como los libros pesan, cuenta con que suelen cobrarse por su peso real.'
				: ( $p ? 'Sí, ' . $p['title'] . ' tiene ruta por carretera desde {{origen_ciudad}}.' : 'Sí, a los países con ruta por carretera.' ) . ' Como los libros pesan, compara las dos vías al cotizar: a veces la terrestre sale más a cuenta.' ),
		),
		'aduanas-e-impuestos' => array(
			array( 'retien|retenid', '¿Por qué se retiene un envío en aduana?', 'Casi siempre por un valor declarado que no cuadra, una descripción vaga del contenido, un producto con reglas propias sin su permiso o datos del destinatario que no coinciden con su documento. Las cuatro cosas se revisan antes de despachar.' ),
			array( 'valor declarado', '¿Qué es el valor declarado?', 'Lo que vale el contenido del envío. Es la base sobre la que la aduana calcula los impuestos y también la de la cobertura del seguro, así que conviene declararlo real.' ),
		),
		'envio-de-alimentos' => array(
			array( 'qué alimentos|cuáles', '¿Qué alimentos son los más fáciles de enviar?', 'Los envasados, sellados de fábrica y con etiqueta. Por vía terrestre se admite más que por avión, y cada aduana tiene su propia lista: revísala con nosotros antes de comprar.' ),
		),
		'envio-de-medicinas-al-extranjero' => array(
			array( 'empac|embal', '¿Cómo deben ir empacadas las medicinas?', 'En su envase original, con el prospecto, en una bolsa sellada aparte del resto y con una copia de la receta visible dentro de la caja.' ),
		),
		'seguro-de-envios' => array(
			array( 'valor', '¿Qué valor declaro para asegurar mi envío?', 'El valor real del contenido. Es la base de la cobertura del seguro y de los impuestos en destino: declarar de menos deja el envío sin cubrir y puede retenerlo.' ),
		),
		'glosario-de-envios-internacionales' => array(
			array( 'número de guía', '¿Qué es el número de guía?', 'El código único de tu envío. Con él se consulta su estado en el rastreo y se hace cualquier gestión sobre él.' ),
		),
		'contacto' => array(
			array( 'whatsapp', '¿Puedo cotizar por WhatsApp?', 'Sí. Escríbenos con peso, medidas, contenido y destino, y te respondemos con el precio y el plazo.' ),
			array( 'empresa', '¿Atienden también a empresas?', 'Sí, a personas y a empresas. Para envíos recurrentes tenemos una página de envíos para empresas con cuenta corporativa y tarifas por volumen.' ),
		),
	);
	return isset( $b[ $slug ] ) ? $b[ $slug ] : array();
}

add_filter( 'grenvios_page_faqs', function ( $faqs, $slug ) {
	if ( is_admin() || ! is_string( $slug ) || $slug === '' ) return $faqs;
	if ( in_array( $slug, array( 'home', 'preguntas-frecuentes', 'blog', 'rastreo-de-envios', 'cotizar' ), true ) ) return $faqs;
	if ( function_exists( 'grenvios_destinos' ) && array_key_exists( $slug, grenvios_destinos() ) ) return $faqs;   // fichas: FAQ propia
	if ( function_exists( 'grenvios_cab_regiones' ) && in_array( $slug, grenvios_cab_regiones(), true ) && grenvios_fa_pais() ) return $faqs;   // región dentro de una ruta: copia
	$faqs = array_values( (array) $faqs );
	$objetivo = 7;
	if ( count( $faqs ) >= $objetivo ) return $faqs;

	$p     = grenvios_fa_pais();
	$texto = mb_strtolower( implode( ' | ', array_map( function ( $f ) { return is_array( $f ) && isset( $f[0] ) ? (string) $f[0] : ''; }, $faqs ) ) );
	foreach ( array_merge( grenvios_fa_propias( $slug, $p ), grenvios_fa_comunes( $p ) ) as $c ) {
		if ( count( $faqs ) >= $objetivo ) break;
		if ( preg_match( '~' . $c[0] . '~iu', $texto ) ) continue;   // ya hay una pregunta de ese tema
		$faqs[] = array( $c[1], $c[2] );
		$texto .= ' | ' . mb_strtolower( $c[1] );
	}
	return $faqs;
}, 99, 2 );

/* ═════════════════════════════════════════════════════════════════════════
 * 2) Contenido propio por país en las páginas que se quedaban cortas
 *    (bloques locales de inc/paises-paginas-locales.php)
 * ═════════════════════════════════════════════════════════════════════════ */
add_filter( 'grenvios_ppl_matriz', function ( $m ) {
	$m += array(
		'mudanzas-internacionales' => array(
			array( 'clima', 'Clima en destino', 'Qué llevar en tu mudanza según el clima de %s', 'Una mudanza se prepara para la vida de allí: el clima decide qué mandar y cómo embalarlo.' ),
			array( 'regimen', 'Al llegar', 'Cómo entra tu mudanza por la aduana de %s', 'Enseres usados y artículos nuevos no se miran igual en la aduana.' ),
		),
		'envio-de-repuestos-al-extranjero' => array(
			array( 'regimen', 'Aduana', 'Repuestos para uso propio o para vender en %s', 'La aduana distingue entre un repuesto para tu vehículo y uno para revender.' ),
			array( 'frontera', 'Ruta', 'Por dónde viajan tus repuestos hasta %s', 'La ruta decide si una pieza con batería puede viajar o no.' ),
		),
		'envio-de-ropa-al-extranjero' => array(
			array( 'clima', 'Clima en destino', 'Ropa para el clima de %s', 'Lo que se envía depende de la estación que encontrará allí quien la recibe.' ),
			array( 'temporada', 'Fechas clave', 'Cuándo se envía más ropa a %s', 'Las fiestas y el cambio de estación mueven la mayoría de las cajas de ropa.' ),
		),
		'carga-terrestre-internacional' => array(
			array( 'frontera', 'Ruta de la carga', 'Por dónde entra la carga terrestre a %s', 'En carga terrestre, el paso de frontera define plazo y documentación.' ),
			array( 'regimen', 'Aduana', 'Cómo se despacha la carga en la aduana de %s', 'Una carga comercial entra por un régimen distinto al de un envío personal.' ),
		),
	);
	return $m;
} );

/* ═════════════════════════════════════════════════════════════════════════
 * 3) Una sección más en las 20 páginas cortas de la ruta principal
 *    (mismo mecanismo que inc/contenido-ampliacion.php: editable en el panel)
 * ═════════════════════════════════════════════════════════════════════════ */
add_filter( 'grenvios_pseo_secciones', function ( $m ) {
	$mas = function ( $slug, $nueva ) use ( &$m ) {
		if ( ! isset( $m[ $slug ] ) ) { $m[ $slug ] = array( $nueva ); return; }
		$actual   = isset( $m[ $slug ]['html'] ) ? array( $m[ $slug ] ) : array_values( $m[ $slug ] );
		$actual[] = $nueva;
		$m[ $slug ] = $actual;
	};

	$mas( 'mudanzas-internacionales', array(
		'sub'    => 'Paso a paso',
		'titulo' => 'Cómo organizar una mudanza pequeña <span class="hl">sin sorpresas</span>',
		'html'   => '<p>Una mudanza pequeña no es un envío más grande: son varias cajas que tienen que llegar juntas, con todo declarado y sin nada que obligue a abrirlas. Estos pasos lo hacen sencillo.</p>
<ol class="gr-pseo-steps">
<li><strong>Decide qué viaja y qué no.</strong> Lo que se reemplaza fácil allí no compensa enviarlo. Prioriza lo personal, lo de valor y lo que es difícil de conseguir en destino.</li>
<li><strong>Numera las cajas y haz el inventario.</strong> Una lista por caja con cada artículo, su cantidad y su valor aproximado. Esa lista es tu declaración de contenido.</li>
<li><strong>Separa lo que tiene reglas propias.</strong> Medicinas, alimentos y lo que lleve batería o líquido van aparte: por avión no viajan y por carretera tienen condiciones.</li>
<li><strong>Elige la vía por volumen.</strong> Una mudanza abulta, así que se cobra por el mayor entre el peso real y el volumétrico (largo × ancho × alto ÷ 5000). Donde hay ruta terrestre suele salir más a cuenta.</li>
<li><strong>Cotiza con medidas reales.</strong> Con las medidas de cada caja te damos precio y plazo; si no puedes acercarte, <a href="%H%/recojo-a-domicilio-lima/">las recogemos en tu casa</a>.</li>
</ol>',
	) );

	$mas( 'traduccion-oficial-de-documentos', array(
		'sub'    => 'En orden',
		'titulo' => 'De documento peruano a documento <span class="hl">válido en el extranjero</span>',
		'html'   => '<p>Un documento que llega rápido pero sin el trámite correcto no sirve en destino. El orden importa, y estos son los pasos que seguimos contigo.</p>
<ol class="gr-pseo-steps">
<li><strong>Confirma qué pide la institución de destino.</strong> Universidad, consulado o empleador: cada uno dice si quiere el original, la apostilla, la traducción o las tres cosas.</li>
<li><strong>Legaliza o apostilla el original.</strong> La apostilla certifica la firma del documento peruano. Mira <a href="%H%/apostilla-y-traduccion/">apostilla y traducción</a> para saber qué documentos la llevan.</li>
<li><strong>Traduce el documento ya apostillado.</strong> Así la traducción incluye también el sello de la apostilla, que es lo que suelen exigir.</li>
<li><strong>Revisa nombres y fechas.</strong> Un nombre escrito distinto que en el pasaporte es el error que más trámites devuelve.</li>
<li><strong>Envía el original con seguimiento.</strong> Va en sobre protegido y con número de guía, por <a href="%H%/envio-internacional-de-documentos/">envío internacional de documentos</a>.</li>
</ol>',
	) );

	$mas( 'envio-de-repuestos-al-extranjero', array(
		'sub'    => 'Antes de despachar',
		'titulo' => 'Cómo preparar un repuesto para que <span class="hl">llegue y pase la aduana</span>',
		'html'   => '<p>Un repuesto suele ser pesado, de forma irregular y, a veces, con aceite o batería. Por eso se prepara distinto que un paquete corriente.</p>
<ul class="gr-pseo-list">
<li><strong>Limpio y seco.</strong> Una pieza usada viaja sin restos de aceite, combustible ni líquidos. Si gotea, la vía aérea la rechaza.</li>
<li><strong>Batería aparte, o por tierra.</strong> Por avión no viajan piezas con batería. Por carretera sí, salvo hacia Chile; si la tuya la lleva, revisamos la vía antes.</li>
<li><strong>Bien sujeto en la caja.</strong> Las piezas metálicas rompen la caja desde dentro. Cartón doble, esquinas protegidas y nada suelto.</li>
<li><strong>Declarado con su referencia.</strong> Marca, modelo, número de parte y valor real. Si es para vender, lleva factura: no entra como envío personal.</li>
<li><strong>Medido antes de cotizar.</strong> Se cobra el mayor entre el peso real y el volumétrico (largo × ancho × alto ÷ 5000). Con peso y medidas te damos el precio en <a href="%H%/cotizar/">cotizar</a>.</li>
</ul>',
	) );

	$mas( 'envio-de-correspondencia-internacional', array(
		'sub'    => 'Para que llegue entera',
		'titulo' => 'Cómo preparar cartas, tarjetas e <span class="hl">invitaciones</span>',
		'html'   => '<p>La correspondencia pesa poco, pero se estropea fácil: se dobla, se moja o llega con la dirección borrosa. Así se evita.</p>
<ul class="gr-pseo-list">
<li><strong>Un sobre por destinatario.</strong> Si mandas varias cartas a la misma ciudad, cada una lleva su nombre y su dirección completa.</li>
<li><strong>Sobre rígido para lo que no se dobla.</strong> Fotografías, dibujos, invitaciones con relieve o diplomas van planos.</li>
<li><strong>Dirección escrita como en destino.</strong> Calle, número, ciudad y código postal en el orden de ese país, y un teléfono de quien recibe.</li>
<li><strong>Nada de dinero ni objetos de valor.</strong> La correspondencia es papel. Lo que tenga valor va como <a href="%H%/envio-internacional-de-paquetes/">paquete</a>, declarado.</li>
<li><strong>Número de guía siempre.</strong> Con él sabes cuándo llegó, en <a href="%H%/rastreo-de-envios/">rastreo de envíos</a>.</li>
</ul>',
	) );

	$mas( 'envio-de-ropa-al-extranjero', array(
		'sub'    => 'Para pagar menos',
		'titulo' => 'Cómo armar una caja de ropa <span class="hl">que no pague aire</span>',
		'html'   => '<p>La ropa casi nunca se cobra por lo que pesa, sino por lo que ocupa. Una caja bien armada puede costar bastante menos que la misma ropa metida de cualquier forma.</p>
<ol class="gr-pseo-steps">
<li><strong>Elige la caja por el contenido.</strong> Una caja ajustada, sin huecos. Se cobra el mayor entre el peso real y el volumétrico (largo × ancho × alto ÷ 5000).</li>
<li><strong>Dobla o enrolla.</strong> Enrollada ocupa menos y se arruga menos. Los abrigos, al fondo y comprimidos.</li>
<li><strong>Protege de la humedad.</strong> Una bolsa plástica por dentro de la caja evita que una lluvia en el trayecto la estropee.</li>
<li><strong>Haz la lista.</strong> Tipo de prenda, cantidad, si es nueva o usada y su valor. «Ropa» a secas es lo que hace revisar una caja.</li>
<li><strong>Compara las dos vías.</strong> Donde hay ruta terrestre, una caja voluminosa suele salir más a cuenta por carretera; te damos los dos precios en <a href="%H%/cotizar/">cotizar</a>.</li>
</ol>',
	) );

	$mas( 'carga-terrestre-internacional', array(
		'sub'    => 'Antes de reservar',
		'titulo' => 'Lo que tiene que estar listo <span class="hl">antes de cargar el camión</span>',
		'html'   => '<p>La carga terrestre viaja por carretera hasta los países vecinos. Admite más que el avión, pero cruza una frontera, y ahí lo que cuenta es que todo cuadre.</p>
<ul class="gr-pseo-list">
<li><strong>Bultos cerrados y numerados.</strong> Cada uno con su peso y sus medidas, para cotizar el volumen real.</li>
<li><strong>Lista de contenido por bulto.</strong> Descripción, cantidad y valor. Es la base del despacho y del impuesto.</li>
<li><strong>Factura si es venta.</strong> Una carga comercial necesita factura comercial; una mudanza o un envío personal, la lista de contenido.</li>
<li><strong>Impuesto pagado en origen.</strong> En las rutas terrestres se cancela en {{origen_ciudad}} al despachar, así que quien recibe no adelanta dinero.</li>
<li><strong>Datos del destinatario completos.</strong> Nombre como en su documento, dirección y teléfono. Si se retira en agencia, el documento tiene que coincidir.</li>
</ul>
<p class="gr-nota">Si tu carga no tiene ruta por carretera, va por <a href="%H%/carga-aerea-internacional/">carga aérea</a>.</p>',
	) );

	$mas( 'envio-express-internacional', array(
		'sub'    => 'Cuando no puede esperar',
		'titulo' => 'Qué acelera de verdad un envío <span class="hl">urgente</span>',
		'html'   => '<p>Pagar la vía más rápida ayuda, pero lo que más retrasa un envío urgente no es el avión: es un dato que falta. Esto es lo que de verdad acorta el plazo.</p>
<ul class="gr-pseo-list">
<li><strong>Vía aérea.</strong> Es la rápida y la única hacia los destinos sin ruta por carretera.</li>
<li><strong>Contenido sin reglas especiales.</strong> Documentos y objetos sin batería, líquidos ni alimentos pasan la revisión sin pausas.</li>
<li><strong>Declaración clara.</strong> Descripción exacta y valor real: una aduana no detiene lo que entiende a la primera.</li>
<li><strong>Destinatario localizable.</strong> Dirección completa y un teléfono que conteste el día de la entrega.</li>
<li><strong>Entregar antes del despacho.</strong> El plazo se cuenta desde que el envío sale de {{origen_ciudad}}; si no puedes venir, <a href="%H%/recojo-a-domicilio-lima/">lo recogemos</a>.</li>
</ul>',
	) );

	$mas( 'envio-de-artesanias-al-extranjero', array(
		'sub'    => 'Piezas frágiles',
		'titulo' => 'Cómo embalar artesanías para que <span class="hl">lleguen enteras</span>',
		'html'   => '<p>Cerámica, piedra de Huamanga, retablos, tejidos o platería: cada material pide un embalaje distinto. Lo que tienen en común es que nada debe moverse dentro de la caja.</p>
<ol class="gr-pseo-steps">
<li><strong>Cada pieza envuelta por separado.</strong> Plástico de burbujas o papel, y las partes salientes —asas, figuras— protegidas aparte.</li>
<li><strong>Relleno en todos los huecos.</strong> Si al mover la caja algo suena, falta relleno.</li>
<li><strong>Caja doble para lo muy frágil.</strong> Una caja dentro de otra, con relleno entre las dos.</li>
<li><strong>Tejidos en bolsa.</strong> Los textiles van en bolsa cerrada para protegerlos de la humedad y el polvo.</li>
<li><strong>Declaración pieza por pieza.</strong> Material, cantidad y valor real. Las réplicas se declaran como réplicas. Mira <a href="%H%/embalaje-para-envios-internacionales/">embalaje para envíos internacionales</a>.</li>
</ol>',
	) );

	$mas( 'envio-de-muestras-comerciales', array(
		'sub'    => 'Para que no se trate como venta',
		'titulo' => 'Cómo enviar una muestra <span class="hl">sin problemas en aduana</span>',
		'html'   => '<p>Una muestra que la aduana interpreta como mercadería para vender se detiene o paga más. Lo que la distingue es la cantidad y cómo se declara.</p>
<ul class="gr-pseo-list">
<li><strong>Cantidad de muestra.</strong> Una o pocas unidades de cada referencia, no un lote.</li>
<li><strong>Factura proforma.</strong> Factura sin valor fiscal con la descripción y el valor de cada artículo. Es el documento habitual para muestras.</li>
<li><strong>Productos con reglas propias.</strong> Alimentos, cosméticos o suplementos pueden necesitar permiso sanitario en destino aunque sean muestras: <a href="%H%/que-se-puede-enviar/">revisa qué se puede enviar</a>.</li>
<li><strong>Datos del importador.</strong> Nombre de la empresa y contacto de quien recibe, para que la aduana sepa a quién avisar.</li>
</ul>',
	) );

	$mas( 'envio-de-regalos-al-extranjero', array(
		'sub'    => 'Para llegar a tiempo',
		'titulo' => 'Cómo enviar un regalo <span class="hl">sin que llegue tarde</span>',
		'html'   => '<p>Un regalo tiene fecha. Lo que hace que llegue a tiempo es enviarlo con margen y prepararlo para que no se detenga en el camino.</p>
<ol class="gr-pseo-steps">
<li><strong>Cuenta hacia atrás desde la fecha.</strong> Suma el plazo del destino, los días hábiles y los feriados de allí. Mira <a href="%H%/tiempos-de-entrega/">tiempos de entrega</a>.</li>
<li><strong>Evita lo que tiene reglas propias.</strong> Alimentos, perfumes y lo que lleve batería pueden necesitar vía terrestre o permisos; mejor confírmalo antes de comprar.</li>
<li><strong>Embalaje de viaje, no de tienda.</strong> El papel de regalo va dentro de una caja de envío con relleno.</li>
<li><strong>Declara el contenido real.</strong> «Regalo» no es una descripción: di qué es, cuántos y cuánto vale.</li>
<li><strong>Varios regalos, una caja.</strong> Si van a la misma dirección, juntarlos suele costar menos que enviarlos por separado.</li>
</ol>',
	) );

	$mas( 'envio-de-celulares-y-laptops', array(
		'sub'    => 'Antes de despachar',
		'titulo' => 'Cómo preparar un celular o una laptop <span class="hl">para el viaje</span>',
		'html'   => '<p>Un equipo electrónico lleva batería, y eso decide la vía. Además vale dinero, y eso decide cómo se declara y se protege.</p>
<ul class="gr-pseo-list">
<li><strong>La vía, primero.</strong> Por avión no viajan artículos con batería interna; por carretera sí se admiten, salvo hacia Chile.</li>
<li><strong>Apagado y protegido.</strong> En su caja original o con protección en pantalla y esquinas, sin espacio para moverse.</li>
<li><strong>Datos borrados o con copia.</strong> Haz una copia de seguridad y desvincula tus cuentas si el equipo es para otra persona.</li>
<li><strong>Declarado con marca y modelo.</strong> Valor real y si es nuevo o usado; si es nuevo, la boleta ayuda a sustentar el valor.</li>
<li><strong>Asegurado.</strong> Por su valor, conviene el <a href="%H%/seguro-de-envios/">seguro del envío</a>.</li>
</ul>',
	) );

	$mas( 'courier-internacional-en-lima', array(
		'sub'    => 'Cómo trabajamos',
		'titulo' => 'Tu envío, de Lima <span class="hl">a la puerta de destino</span>',
		'html'   => '<p>Un courier internacional se encarga de todo el trayecto: recoger, preparar, despachar y entregar. Así es el recorrido de un envío con nosotros.</p>
<ol class="gr-pseo-steps">
<li><strong>Cotizas.</strong> Con peso, medidas, contenido y destino te damos precio y plazo en <a href="%H%/cotizar/">cotizar</a>.</li>
<li><strong>Lo traes o lo recogemos.</strong> En nuestra oficina de {{origen_ciudad}} o con <a href="%H%/recojo-a-domicilio-lima/">recojo a domicilio</a>.</li>
<li><strong>Lo revisamos contigo.</strong> Pesamos, medimos y repasamos el contenido antes de cerrar la caja, que es lo que evita retenciones.</li>
<li><strong>Lo despachamos.</strong> Por vía aérea o terrestre según el destino, con número de guía.</li>
<li><strong>Lo sigues hasta la entrega.</strong> A domicilio o en agencia, según el país, con el estado en <a href="%H%/rastreo-de-envios/">rastreo de envíos</a>.</li>
</ol>',
	) );

	$mas( 'envio-de-libros-al-extranjero', array(
		'sub'    => 'Para que no se doblen',
		'titulo' => 'Cómo embalar libros <span class="hl">para un envío internacional</span>',
		'html'   => '<p>Los libros pesan y tienen esquinas que se golpean. Un buen embalaje los protege y evita pagar por espacio vacío.</p>
<ul class="gr-pseo-list">
<li><strong>Cajas pequeñas y llenas.</strong> Una caja grande de libros es demasiado pesada y se rompe; mejor varias medianas.</li>
<li><strong>De canto o planos, pero firmes.</strong> Sin huecos para que no se deslicen y doblen las tapas.</li>
<li><strong>Esquinas protegidas.</strong> Cartón en las esquinas de los libros de tapa dura o de valor.</li>
<li><strong>Bolsa contra la humedad.</strong> El papel absorbe agua: una bolsa plástica dentro de la caja lo protege.</li>
<li><strong>Peso real y volumétrico.</strong> Se cobra el mayor de los dos (largo × ancho × alto ÷ 5000); los libros casi siempre van por peso real. Compara vías en <a href="%H%/cotizar/">cotizar</a>.</li>
</ul>',
	) );

	$mas( 'contacto', array(
		'sub'    => 'Para responderte más rápido',
		'titulo' => 'Qué datos tener a mano <span class="hl">al escribirnos</span>',
		'html'   => '<p>Cuanto más completo el primer mensaje, más rápido te damos un precio cerrado. Con esto respondemos sin idas y vueltas:</p>
<ul class="gr-pseo-list">
<li><strong>Destino.</strong> País y ciudad de quien recibe.</li>
<li><strong>Qué envías.</strong> Documentos, paquete, equipaje o carga, y una descripción del contenido.</li>
<li><strong>Peso y medidas.</strong> Largo, ancho y alto de la caja: el precio se calcula con el mayor entre el peso real y el volumétrico.</li>
<li><strong>Si hay algo especial.</strong> Medicinas, alimentos, líquidos o artículos con batería tienen reglas propias según la vía.</li>
<li><strong>Si necesitas recojo.</strong> Dirección y horario en {{origen_ciudad}} para <a href="%H%/recojo-a-domicilio-lima/">recoger el envío</a>.</li>
</ul>',
	) );

	$mas( 'envio-de-compras', array(
		'sub'    => 'Antes de pagar',
		'titulo' => 'Lo que conviene revisar <span class="hl">antes de comprar</span>',
		'html'   => '<p>El mejor momento para evitar un problema con una compra es antes de pagarla. Repasa esto antes de comprar algo para enviarlo al extranjero.</p>
<ul class="gr-pseo-list">
<li><strong>¿Se puede enviar?</strong> Perfumes, cosméticos, alimentos y electrónica con batería tienen reglas propias según el destino y la vía: <a href="%H%/que-se-puede-enviar/">revísalo aquí</a>.</li>
<li><strong>¿Cuánto ocupa?</strong> La caja de la tienda puede ser mucho más grande que el producto. Se cobra por el mayor entre el peso real y el volumétrico.</li>
<li><strong>¿Guardas la boleta?</strong> Sirve para declarar el valor real, que es la base de impuestos y del seguro.</li>
<li><strong>¿Viene de varias tiendas?</strong> Podemos esperar todos los pedidos y despacharlos juntos en un solo envío.</li>
</ul>',
	) );

	$mas( 'carga-aerea-internacional', array(
		'sub'    => 'Antes de reservar',
		'titulo' => 'Cómo preparar una carga <span class="hl">para la vía aérea</span>',
		'html'   => '<p>La carga aérea es la opción rápida y la única hacia los destinos sin ruta por carretera. A cambio, es más estricta con lo que admite y se cobra con atención al volumen.</p>
<ul class="gr-pseo-list">
<li><strong>Revisa el contenido.</strong> Por avión no viajan líquidos, alimentos, aerosoles ni artículos con batería interna.</li>
<li><strong>Bultos compactos.</strong> Se cobra el mayor entre el peso real y el volumétrico (largo × ancho × alto ÷ 5000): el espacio vacío se paga.</li>
<li><strong>Documentación lista.</strong> Factura comercial si es venta y lista de contenido por bulto, con valores reales.</li>
<li><strong>Embalaje para manipulación.</strong> Cajas resistentes, esquinas protegidas y bultos etiquetados con su número.</li>
<li><strong>Datos del destinatario.</strong> Empresa o persona, dirección y teléfono, tal como en su documento.</li>
</ul>
<p class="gr-nota">Si tu destino tiene ruta por carretera y la fecha no aprieta, compara con la <a href="%H%/carga-terrestre-internacional/">carga terrestre</a>.</p>',
	) );

	/* Tabla de destinos con los datos del gestor, en las páginas que no la tenían. */
	$tabla = function () {
		if ( ! function_exists( 'grenvios_dsec_red' ) ) return '';
		$f = '';
		foreach ( grenvios_dsec_red() as $d ) {
			$f .= '<tr><th scope="row">' . esc_html( $d['title'] ) . '</th><td>' . esc_html( $d['tiempo'] ) . '</td><td>' . esc_html( $d['modos'] ) . '</td><td>' . esc_html( $d['entrega'] ) . '</td></tr>';
		}
		return $f === '' ? '' : '<div class="gr-table-wrap"><table class="gr-table"><thead><tr><th scope="col">Destino</th><th scope="col">Plazo</th><th scope="col">Vía</th><th scope="col">Entrega</th></tr></thead><tbody>' . $f . '</tbody></table></div>';
	};
	$t = $tabla();
	if ( $t !== '' ) {
		foreach ( array(
			'traduccion-oficial-de-documentos'        => 'Una vez traducido, así llega el documento a cada destino.',
			'envio-de-correspondencia-internacional' => 'Plazo, vía y forma de entrega de tu carta en cada destino.',
			'envio-de-artesanias-al-extranjero'      => 'A dónde enviamos tus artesanías, en cuánto tiempo y cómo se entregan.',
			'envio-de-muestras-comerciales'          => 'Plazo y forma de entrega de una muestra en cada destino, para planificar la reunión con tu cliente.',
			'envio-de-regalos-al-extranjero'         => 'Usa el plazo de cada destino para calcular cuándo enviar el regalo.',
			'envio-de-celulares-y-laptops'           => 'Fíjate en la vía: donde solo hay avión, los equipos con batería no pueden viajar.',
			'contacto'                               => 'Antes de escribirnos, aquí tienes el plazo, la vía y la entrega de cada destino.',
		) as $slug => $intro ) {
			$mas( $slug, array(
				'sub'    => 'Destinos',
				'titulo' => 'Plazo, vía y entrega <span class="hl">de cada destino</span>',
				'html'   => '<p>' . $intro . ' Los plazos se cuentan en días hábiles desde que el envío sale de {{origen_ciudad}}; el precio exacto te lo damos al cotizar.</p>' . $t,
			) );
		}
	}

	/* Una segunda sección corta en las que ya tenían tabla. */
	$mas( 'mudanzas-internacionales', array(
		'sub'    => 'Al llegar',
		'titulo' => 'Lo que conviene resolver <span class="hl">antes de que llegue la mudanza</span>',
		'html'   => '<ul class="gr-pseo-list">
<li><strong>Quién la recibe.</strong> Si aún no tienes dirección fija allí, envía a la de un familiar o amigo de confianza, con su nombre tal como figura en su documento.</li>
<li><strong>Dónde se entrega.</strong> En unos destinos llega a domicilio y en otros se retira en agencia: la tabla de esta página indica cuál corresponde.</li>
<li><strong>Qué documentos llevar.</strong> Guarda el inventario de cada caja y tu número de guía: son lo primero que piden si hay una revisión.</li>
<li><strong>Cómo revisar al recibir.</strong> Cuenta las cajas, mira su estado y fotografía cualquier daño antes de abrirlas.</li>
</ul>',
	) );
	$mas( 'envio-de-repuestos-al-extranjero', array(
		'sub'    => 'Para negocios',
		'titulo' => 'Si envías repuestos <span class="hl">de forma habitual</span>',
		'html'   => '<p>Un taller o una distribuidora que manda repuestos cada semana no necesita cotizar envío por envío. Con una <a href="%H%/envios-para-empresas/">cuenta de empresa</a> se fija la tarifa por volumen, se programan los recojos en tu almacén y se emite factura. Para la aduana, lo importante es lo mismo que en un envío suelto: cada pieza con su referencia, su cantidad y su valor real, y factura comercial cuando es venta. Si un cliente te pide algo con batería, revisa antes si su país tiene ruta por carretera.</p>',
	) );
	$mas( 'envio-de-ropa-al-extranjero', array(
		'sub'    => 'Nueva o usada',
		'titulo' => 'Ropa nueva y ropa usada <span class="hl">no se declaran igual</span>',
		'html'   => '<ul class="gr-pseo-list">
<li><strong>Ropa usada, para uso personal.</strong> Se declara como efectos personales, con cantidades razonables para una persona y su valor aproximado.</li>
<li><strong>Ropa nueva, con etiqueta.</strong> Es una compra: se declara con su valor de compra y conviene llevar la boleta.</li>
<li><strong>Muchas unidades iguales.</strong> Diez polos de la misma talla y modelo parecen mercadería para vender, aunque sean regalos. Si es para vender, va con factura como <a href="%H%/envios-para-empresas/">envío comercial</a>.</li>
<li><strong>Prendas de marca.</strong> Algunas aduanas revisan las marcas conocidas; declara la marca y el valor real.</li>
</ul>',
	) );
	$mas( 'carga-terrestre-internacional', array(
		'sub'    => 'Aérea o terrestre',
		'titulo' => 'Cuándo conviene la carretera <span class="hl">y cuándo el avión</span>',
		'html'   => '<p>La vía terrestre conviene cuando la carga abulta, pesa y la fecha no aprieta: admite más tipos de contenido que el avión —líquidos sellados o artículos con batería— y el impuesto queda pagado en {{origen_ciudad}}. El avión conviene cuando hay prisa, cuando el envío es pequeño o de valor, o cuando el destino no tiene ruta por carretera. Si tienes dudas, pide las dos cotizaciones: con el mismo peso y las mismas medidas, la diferencia de precio y de plazo te dice cuál elegir.</p>',
	) );
	$mas( 'envio-express-internacional', array(
		'sub'    => 'Documentos urgentes',
		'titulo' => 'Cuando lo urgente <span class="hl">es un documento</span>',
		'html'   => '<p>Un contrato, una partida o un pasaporte que tienen que llegar a tiempo son el caso más común de envío urgente. Lo que más tiempo ahorra no es el transporte, sino tener el documento listo antes: apostillado y traducido si el destino lo pide. Si aún no lo está, mira <a href="%H%/apostilla-y-traduccion/">apostilla y traducción</a> para hacerlo en un solo lugar, y después el envío va en sobre protegido con número de guía por <a href="%H%/envio-internacional-de-documentos/">envío de documentos</a>.</p>',
	) );
	$mas( 'courier-internacional-en-lima', array(
		'sub'    => 'Para elegir bien',
		'titulo' => 'Qué preguntarle a un courier <span class="hl">antes de enviar</span>',
		'html'   => '<ul class="gr-pseo-list">
<li><strong>¿Me revisan el contenido antes de cerrar?</strong> Es lo que evita la mayoría de las retenciones en aduana.</li>
<li><strong>¿Cómo calculan el precio?</strong> Si no te preguntan las medidas, la sorpresa llega con el peso volumétrico.</li>
<li><strong>¿Quién paga los impuestos y dónde?</strong> En nuestras rutas terrestres se pagan en {{origen_ciudad}} al despachar.</li>
<li><strong>¿Tengo número de guía?</strong> Sin él no hay forma de saber dónde está el envío.</li>
<li><strong>¿Cómo se entrega en destino?</strong> A domicilio o en agencia: cambia con cada país.</li>
</ul>',
	) );
	$mas( 'envio-de-libros-al-extranjero', array(
		'sub'    => 'Bibliotecas y mudanzas',
		'titulo' => 'Si envías muchos libros <span class="hl">a la vez</span>',
		'html'   => '<p>Una biblioteca personal o los libros de una mudanza se envían mejor en varias cajas medianas que en una grande: pesan menos cada una, se manipulan mejor y no se rompen. Haz una lista por caja con la cantidad de libros y su valor aproximado; los libros usados para uso personal se declaran como efectos personales. Si el destino tiene ruta por carretera, cotiza también la vía terrestre, que suele salir a cuenta con envíos pesados que no tienen prisa.</p>',
	) );

	/* Regiones: qué cambia al elegir un país de la región. */
	$region = function ( $slug, $nombre ) use ( $mas ) {
		$mas( $slug, array(
			'sub'    => 'Antes de enviar',
			'titulo' => 'Lo que cambia de un país a otro <span class="hl">en ' . $nombre . '</span>',
			'html'   => '<p>Dentro de una misma región, cada país tiene su propia aduana, sus plazos y su forma de entrega. Antes de cotizar, conviene tener claro esto del país al que envías.</p>
<ul class="gr-pseo-list">
<li><strong>La vía.</strong> Por carretera solo se llega a los países vecinos de Perú; al resto se envía por avión, y por avión no viajan líquidos, alimentos, aerosoles ni artículos con batería interna.</li>
<li><strong>El plazo.</strong> Se cuenta en días hábiles desde que el envío sale de {{origen_ciudad}} y cambia con cada país. Lo tienes en la ficha de cada destino.</li>
<li><strong>La entrega.</strong> En unos países llega a domicilio y en otros se retira en agencia con el documento de identidad.</li>
<li><strong>La aduana.</strong> Cada país fija qué admite y qué cobra. Revisa <a href="%H%/aduanas-e-impuestos/">aduanas e impuestos</a> y <a href="%H%/que-se-puede-enviar/">qué se puede enviar</a>.</li>
</ul>',
		) );
	};
	$prepara = function ( $slug, $nombre, $paises ) use ( $mas ) {
		$mas( $slug, array(
			'sub'    => 'Paso a paso',
			'titulo' => 'Cómo preparar tu envío <span class="hl">a ' . $nombre . '</span>',
			'html'   => '<p>Un envío a ' . $paises . ' se prepara igual que cualquier envío internacional, con un cuidado extra en lo que más revisan sus aduanas: que el contenido esté descrito con detalle y que el valor sea el real.</p>
<ol class="gr-pseo-steps">
<li><strong>Confirma que se puede enviar.</strong> Revisa la lista de <a href="%H%/que-se-puede-enviar/">qué se puede enviar</a> y la ficha del país antes de comprar o empacar.</li>
<li><strong>Haz la lista de contenido.</strong> Cada artículo con su cantidad y su valor. «Varios» o «regalos» no son descripciones.</li>
<li><strong>Embala para el trayecto.</strong> Caja resistente, sin huecos y con lo frágil protegido; mira <a href="%H%/embalaje-para-envios-internacionales/">cómo embalar</a>.</li>
<li><strong>Revisa los datos de quien recibe.</strong> Nombre como en su documento, dirección completa y un teléfono que conteste.</li>
<li><strong>Cotiza con peso y medidas.</strong> Te damos precio y plazo en <a href="%H%/cotizar/">cotizar</a>, y al despachar, tu número de guía.</li>
</ol>',
		) );
	};
	$prepara( 'envios-a-norteamerica', 'Norteamérica', 'Estados Unidos, Canadá o México' );
	$prepara( 'envios-a-europa', 'Europa', 'España, Italia, Francia o Alemania' );
	$prepara( 'envios-a-centroamerica-y-el-caribe', 'Centroamérica y el Caribe', 'Cuba, Panamá, Costa Rica o Puerto Rico' );
	$prepara( 'envios-a-sudamerica', 'Sudamérica', 'cualquier país de Sudamérica' );

	$region( 'envios-a-norteamerica', 'Norteamérica' );
	$region( 'envios-a-europa', 'Europa' );
	$region( 'envios-a-centroamerica-y-el-caribe', 'Centroamérica y el Caribe' );
	$region( 'envios-a-sudamerica', 'Sudamérica' );

	return $m;
}, 20 );
