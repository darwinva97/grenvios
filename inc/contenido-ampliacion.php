<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Ampliación de contenido: las páginas más delgadas de la ruta principal
 * ══════════════════════════════════════════════════════════════════════════
 *
 * AUDITORÍA (2026-09-27, palabras de contenido sin cabecera, pie, enlazado
 * ni FAQ). Técnicamente las 656 URL estaban bien —H1 único, title, meta,
 * canonical y schema en todas, ningún enlace interno roto—; lo que faltaba
 * era volumen en estas:
 *
 *     607  /servicios/encomiendas-internacionales/
 *     659  /embalaje-para-envios-internacionales/
 *     665  /cuanto-cuesta-enviar-un-paquete-al-extranjero/
 *     716  /servicios/carga-internacional/
 *     718  /servicios/envio-de-medicinas-al-extranjero/
 *     730  /recojo-a-domicilio-lima/
 *     781  /rastreo-de-envios/
 *
 * Usa el mismo mecanismo que inc/paginas-contenido-seo.php, así que cada
 * sección aparece sola en el panel «Editar página» (antetítulo, título y
 * cuerpo) y solo se pinta en la ruta principal.
 *
 * Mismo criterio de redacción: cero cifras inventadas. Solo se afirma lo que
 * el sitio ya afirma (peso volumétrico ÷ 5000, vías aérea y terrestre,
 * impuesto de las rutas terrestres pagado en {{origen_ciudad}}, entrega a
 * domicilio o en agencia, seguimiento por guía). Plazos y precios, al cotizar.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_filter( 'grenvios_pseo_secciones', function ( $m ) {

	$mas = function ( $slug, $nueva ) use ( &$m ) {
		if ( ! isset( $m[ $slug ] ) ) { $m[ $slug ] = array( $nueva ); return; }
		$actual   = isset( $m[ $slug ]['html'] ) ? array( $m[ $slug ] ) : array_values( $m[ $slug ] );
		$actual[] = $nueva;
		$m[ $slug ] = $actual;
	};

	/* ── Encomiendas internacionales ─────────────────────────────────── */
	$mas( 'encomiendas-internacionales', array(
		'sub'    => 'Paso a paso',
		'titulo' => 'Cómo preparar tu encomienda para que <span class="hl">llegue sin tropiezos</span>',
		'html'   => '<p>La mayoría de las encomiendas que se demoran no tienen un problema de transporte, sino de preparación: una dirección incompleta, un contenido mal descrito o una caja que no aguantó. Estos cinco pasos lo evitan.</p>
<ol class="gr-pseo-steps">
<li><strong>Haz la lista antes de cerrar.</strong> Anota cada artículo con su cantidad y su valor aproximado. Esa lista es la declaración de contenido y te ahorra rehacerla en el mostrador.</li>
<li><strong>Separa lo que tiene reglas propias.</strong> Medicinas, alimentos y todo lo que lleve batería o líquido va en bolsas aparte, para que una revisión no obligue a desarmar toda la caja.</li>
<li><strong>Elige una caja ajustada.</strong> El precio se calcula con el mayor entre el peso real y el volumétrico (largo × ancho × alto ÷ 5000). Si no tienes caja, la ponemos nosotros; mira <a href="%H%/embalaje-para-envios-internacionales/">cómo embalar un envío internacional</a>.</li>
<li><strong>Confirma los datos de quien recibe.</strong> Nombre completo tal como figura en su documento, dirección con referencias y un teléfono que conteste.</li>
<li><strong>Guarda tu número de guía.</strong> Con él sigues la encomienda en <a href="%H%/rastreo-de-envios/">rastreo de envíos</a> hasta la entrega.</li>
</ol>',
	) );
	$mas( 'encomiendas-internacionales', array(
		'sub'    => 'Del otro lado',
		'titulo' => 'Lo que tiene que saber <span class="hl">quien la recibe</span>',
		'html'   => '<p>Una encomienda no termina cuando sale de {{origen_ciudad}}: termina cuando tu familiar la tiene en la mano. Avísale de estos puntos antes de despachar y te ahorrarás llamadas de última hora.</p>
<ul class="gr-pseo-list">
<li><strong>Cómo se entrega en su país.</strong> En unos destinos llega a domicilio y en otros se retira en una agencia local. La tabla de destinos de esta página indica cuál corresponde.</li>
<li><strong>Qué documento necesita.</strong> Para retirar en agencia se presenta el documento de identidad a cuyo nombre va el envío. Si el nombre no coincide, no se entrega.</li>
<li><strong>Si puede haber impuestos.</strong> En las rutas terrestres el impuesto se paga en {{origen_ciudad}} al despachar. En las aéreas depende del contenido y del valor, y te lo confirmamos antes; revisa <a href="%H%/aduanas-e-impuestos/">aduanas e impuestos</a> para entender cómo funciona.</li>
<li><strong>Qué hacer si la caja llega dañada.</strong> Fotografiarla antes de abrirla y avisarnos el mismo día. Es la base de cualquier reclamo al <a href="%H%/seguro-de-envios/">seguro del envío</a>.</li>
</ul>',
	) );

	/* ── Embalaje ────────────────────────────────────────────────────── */
	$mas( 'embalaje-para-envios-internacionales', array(
		'sub'    => 'Referencia rápida',
		'titulo' => 'Qué embalaje usar <span class="hl">según lo que envías</span>',
		'html'   => '<div class="gr-table-wrap"><table class="gr-table"><thead><tr><th scope="col">Contenido</th><th scope="col">Caja</th><th scope="col">Relleno y protección</th></tr></thead><tbody>
<tr><th scope="row">Documentos</th><td>Sobre rígido o carpeta de cartón</td><td>Dentro de una funda plástica, sin doblar los originales</td></tr>
<tr><th scope="row">Ropa y calzado</th><td>Caja ajustada o bolsa al vacío dentro de caja</td><td>La propia ropa sirve de relleno; zapatos en su caja o envueltos</td></tr>
<tr><th scope="row">Frascos y vidrio</th><td>Caja dentro de otra caja</td><td>Cada pieza envuelta por separado, relleno entre las dos cajas</td></tr>
<tr><th scope="row">Alimentos envasados</th><td>Caja de doble pared</td><td>En su envase de fábrica, en bolsa sellada y lejos de la ropa</td></tr>
<tr><th scope="row">Electrónica</th><td>Caja original o una ajustada</td><td>Relleno en todos los lados; consulta antes si lleva batería</td></tr>
</tbody></table></div>
<p>Si dudas entre dos opciones, elige siempre la caja más pequeña que cierre sin forzar: protege mejor y baja el <a href="%H%/servicios/peso-volumetrico/">peso volumétrico</a>.</p>',
	) );
	$mas( 'embalaje-para-envios-internacionales', array(
		'sub'    => 'Pensando en la aduana',
		'titulo' => 'Un paquete fácil de revisar <span class="hl">se revisa antes</span>',
		'html'   => '<p>Cualquier envío internacional puede ser abierto en la aduana de destino. No se puede evitar, pero sí se puede hacer que la revisión sea rápida y que el paquete se vuelva a cerrar bien.</p>
<ul class="gr-pseo-list">
<li><strong>Agrupa por tipo.</strong> Ropa con ropa, alimentos con alimentos, medicinas aparte. El inspector encuentra lo que busca sin vaciar la caja.</li>
<li><strong>Deja las etiquetas de fábrica a la vista.</strong> Un producto con su envase original se identifica de un vistazo; uno trasvasado a otro recipiente, no.</li>
<li><strong>Mete la lista de contenido dentro.</strong> Una copia de la declaración en un sobre, justo debajo de la tapa.</li>
<li><strong>No envuelvas cada cosa con metros de cinta.</strong> Protege con relleno, no con cinta: lo que hay que cortar para revisar, llega peor cerrado.</li>
</ul>
<p>Todo esto se aplica también a las <a href="%H%/servicios/encomiendas-internacionales/">encomiendas familiares</a>, que son las que más se revisan por lo variado de su contenido.</p>',
	) );

	/* ── Cuánto cuesta ───────────────────────────────────────────────── */
	$mas( 'cuanto-cuesta-enviar-un-paquete-al-extranjero', array(
		'sub'    => 'Antes de pedir precio',
		'titulo' => 'Cómo medir y pesar tu caja <span class="hl">sin errores</span>',
		'html'   => '<p>La diferencia entre la cotización y lo que se cobra en el mostrador casi siempre viene de una medida mal tomada. Medirla bien toma un minuto:</p>
<ol class="gr-pseo-steps">
<li><strong>Mide la caja ya cerrada</strong>, con todo dentro. Una caja llena se abomba y crece un poco respecto a la vacía.</li>
<li><strong>Toma los tres lados en centímetros</strong>: largo, ancho y alto, por el exterior y en el punto más ancho.</li>
<li><strong>Multiplica y divide entre 5000.</strong> Ese es el peso volumétrico. Compáralo con lo que marca la balanza: se cobra el mayor de los dos.</li>
<li><strong>Pésala con una balanza de equipaje</strong> o de baño (pesándote con y sin la caja). Si no puedes, dinos qué va dentro y lo estimamos contigo.</li>
</ol>
<p>Si prefieres no hacer cuentas, la <a href="%H%/servicios/peso-volumetrico/">calculadora de peso volumétrico</a> lo hace por ti.</p>',
	) );
	$mas( 'cuanto-cuesta-enviar-un-paquete-al-extranjero', array(
		'sub'    => 'Sin letra pequeña',
		'titulo' => 'Qué incluye el precio que te damos <span class="hl">y qué puede cambiarlo</span>',
		'html'   => '<p>La cotización que recibes es el precio puesto en destino para ese peso, esas medidas y esa vía. Conviene saber qué va dentro de esa cifra y qué la haría variar:</p>
<ul class="gr-pseo-list">
<li><strong>Incluye</strong> el flete internacional hasta el destino y el seguimiento con número de guía. En las rutas terrestres, también el impuesto que se paga en {{origen_ciudad}} al despachar.</li>
<li><strong>Se suma si lo pides</strong>: el <a href="%H%/recojo-a-domicilio-lima/">recojo a domicilio</a>, cuyo costo depende del distrito, y la caja si no traes la tuya.</li>
<li><strong>Puede cambiar</strong> si al recibir el paquete el peso o las medidas no coinciden con los que nos diste. Por eso lo revisamos contigo antes de despachar, no después.</li>
<li><strong>No depende de nosotros</strong> el impuesto de las rutas aéreas: lo fija la aduana del país de destino según el contenido y su valor. Te lo explicamos en <a href="%H%/aduanas-e-impuestos/">aduanas e impuestos</a>.</li>
</ul>',
	) );

	/* ── Medicinas ───────────────────────────────────────────────────── */
	$mas( 'envio-de-medicinas-al-extranjero', array(
		'sub'    => 'En la aduana',
		'titulo' => 'Qué revisan en destino <span class="hl">cuando llega una medicina</span>',
		'html'   => '<p>La autoridad sanitaria y la aduana del país que recibe miran tres cosas, y las tres se preparan desde {{origen_ciudad}}:</p>
<ul class="gr-pseo-list">
<li><strong>Que el medicamento esté permitido.</strong> Cada país tiene su lista. Lo que se vende libremente en una farmacia peruana puede requerir permiso en otro lugar; por eso pedimos el nombre del medicamento antes de que lo compres.</li>
<li><strong>Que la cantidad sea de uso personal.</strong> La receta y la cantidad de envases tienen que ser coherentes entre sí: un tratamiento, no un stock.</li>
<li><strong>Que sea identificable.</strong> Envase original, etiqueta legible y prospecto dentro. Un blíster suelto no se puede comprobar y suele quedarse retenido.</li>
</ul>
<p>Si la aduana pide un documento adicional, te avisamos y lo gestionamos contigo. Lo que no se puede hacer es completar después un envío que salió sin receta.</p>',
	) );
	$mas( 'envio-de-medicinas-al-extranjero', array(
		'sub'    => 'Tratamientos continuos',
		'titulo' => 'Si tu familiar necesita la medicina <span class="hl">todos los meses</span>',
		'html'   => '<p>Es un caso muy habitual: un tratamiento largo que en el país de destino es caro o no se consigue. Para que cada envío salga igual de fácil que el primero:</p>
<ul class="gr-pseo-list">
<li><strong>Mantén la receta vigente</strong> y renuévala antes de que venza. Cada envío necesita una receta válida en la fecha del despacho.</li>
<li><strong>Envía la cantidad de un periodo razonable</strong>, no la de un año entero de una vez: lo coherente con la receta es lo que pasa sin problemas.</li>
<li><strong>Calcula con el plazo del destino.</strong> Despacha con margen suficiente para que el siguiente envío llegue antes de que se acabe el anterior. Los plazos por país están en <a href="%H%/tiempos-de-entrega/">tiempos de entrega</a>.</li>
<li><strong>Puedes sumar otras cosas.</strong> Ropa o alimentos pueden ir en la misma <a href="%H%/servicios/encomiendas-internacionales/">encomienda</a>, siempre con las medicinas en su bolsa aparte.</li>
</ul>',
	) );

	/* ── Carga internacional ─────────────────────────────────────────── */
	$mas( 'carga-internacional', array(
		'sub'    => 'Antes del despacho',
		'titulo' => 'Cómo dejar tu carga <span class="hl">lista para salir</span>',
		'html'   => '<p>Una carga bien preparada se despacha el día previsto; una con papeles o bultos que no cuadran, espera. Esto es lo que revisamos en cada envío comercial:</p>
<ol class="gr-pseo-steps">
<li><strong>Factura comercial con el valor real</strong> y una descripción precisa de la mercancía. Es la base del trámite en destino.</li>
<li><strong>Lista de bultos</strong> con el contenido, el peso y las medidas de cada uno. Tiene que coincidir con la factura.</li>
<li><strong>Bultos numerados</strong> (1 de 5, 2 de 5…) y rotulados igual. Si uno se separa en el camino, se identifica al instante.</li>
<li><strong>Embalaje de exportación.</strong> Cajas resistentes y, en volúmenes grandes, bultos consolidados y asegurados para el apilamiento.</li>
<li><strong>Permisos del producto</strong> si el destino los exige. Te confirmamos cuáles antes de que la carga salga de {{origen_ciudad}}.</li>
</ol>
<p>Si envías con frecuencia, lee también <a href="%H%/envios-para-empresas/">envíos para empresas</a>: fijamos días de recojo y un circuito para no empezar de cero cada vez.</p>',
	) );

	/* ── Recojo a domicilio ──────────────────────────────────────────── */
	$mas( 'recojo-a-domicilio-lima', array(
		'sub'    => 'El día del recojo',
		'titulo' => 'Qué pasa cuando llegamos <span class="hl">a tu puerta</span>',
		'html'   => '<ol class="gr-pseo-steps">
<li><strong>Te confirmamos la franja de visita</strong> por WhatsApp, para que haya alguien que entregue el paquete.</li>
<li><strong>Revisamos el contenido contigo.</strong> Es el momento de separar lo que tenga reglas propias y evitar una retención en aduana.</li>
<li><strong>Embalamos si hace falta</strong>, con una caja ajustada a lo que envías.</li>
<li><strong>Pesamos y medimos</strong> delante de ti. Así el precio final coincide con lo que se cobra.</li>
<li><strong>Te dejamos el comprobante</strong> y, al despachar, el número de guía para seguirlo en <a href="%H%/rastreo-de-envios/">rastreo de envíos</a>.</li>
</ol>
<p>El envío entra en la misma programación que si lo llevaras tú a la oficina: el recojo no suma días. Si aún no tienes el precio, <a href="%H%/cotizar/">cotiza primero</a> y pide el recojo en el mismo mensaje.</p>',
	) );

	/* ── Rastreo ─────────────────────────────────────────────────────── */
	$mas( 'rastreo-de-envios', array(
		'sub'    => 'Cuando algo no avanza',
		'titulo' => 'Qué hacer si el estado <span class="hl">no cambia</span>',
		'html'   => '<p>Un envío puede pasar varios días en el mismo estado sin que haya un problema. Antes de preocuparte, mira en cuál está:</p>
<ul class="gr-pseo-list">
<li><strong>En tránsito.</strong> Es el tramo más largo. En la vía terrestre el estado se actualiza menos a menudo que en la aérea, y eso no significa que esté detenido.</li>
<li><strong>Proceso de aduana.</strong> Es el único tramo cuyo tiempo no controlamos: depende de la revisión del país de destino. Una descripción de contenido precisa lo acorta.</li>
<li><strong>Retenido.</strong> La aduana necesita algo: un documento, un dato o el pago de un impuesto. Te avisamos y te decimos exactamente qué pide y quién debe presentarlo.</li>
<li><strong>Listo para la entrega.</strong> Si la entrega es en agencia, tu destinatario ya puede retirarlo con su documento.</li>
</ul>
<p>Si el plazo del destino ya pasó y el estado sigue igual, escríbenos con tu número de guía y lo revisamos. Los plazos de cada país están en <a href="%H%/tiempos-de-entrega/">tiempos de entrega</a>.</p>',
	) );

	return $m;
}, 20 );

/* Preguntas nuevas: se suman a las de cada página y van al FAQPage. */
add_filter( 'grenvios_pseo_faqs', function ( $f ) {
	$sumar = array(
		'rastreo-de-envios' => array(
			array( '¿Qué significa que mi envío esté «Retenido»?', 'Que la aduana de destino necesita algo para liberarlo: un documento, un dato del contenido o el pago de un impuesto. Te avisamos con lo que pide y te acompañamos en el trámite.' ),
		),
		'recojo-a-domicilio-lima' => array(
			array( '¿Pesan el paquete en el momento del recojo?', 'Sí. Lo pesamos y medimos delante de ti, para que el precio final coincida con el de la cotización.' ),
		),
		'carga-internacional' => array(
			array( '¿Cómo tengo que rotular una carga de varios bultos?', 'Numera cada bulto (1 de 5, 2 de 5…) con los mismos datos de remitente y destinatario, y acompaña la carga con una lista de bultos que coincida con la factura comercial.' ),
		),
	);
	foreach ( $sumar as $slug => $lista ) {
		$f[ $slug ] = array_merge( isset( $f[ $slug ] ) ? (array) $f[ $slug ] : array(), $lista );
	}
	return $f;
}, 20 );
