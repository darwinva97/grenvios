<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Ruta principal: secciones con ángulo de ORIGEN y más enlazado interno
 * ══════════════════════════════════════════════════════════════════════════
 *
 * La auditoría de la ruta Perú dejó dos huecos, además de la cobertura local
 * que resuelve inc/cobertura-origen.php:
 *
 *   1) Nueve páginas sin sección propia. Las de servicio explican QUÉ se
 *      envía, pero ninguna respondía a lo que se pregunta cuando el origen es
 *      {{origen_pais}}: dónde se despacha, qué papel pide la aduana de aquí,
 *      qué conviene decidir antes de ir a la oficina.
 *   2) Enlazado interno pobre justo en las páginas que menos autoridad tienen:
 *      seis o siete enlaces en compras, seguro, alimentos, rastreo, peso
 *      volumétrico y recojo, mientras la portada reparte veinticuatro.
 *
 * Todo lo de aquí se engancha a los módulos que ya existen —el registro de
 * secciones editables (inc/paginas-contenido-seo.php) y el mapa de enlazado
 * (inc/seo-enlazado.php)—, así que hereda su comportamiento: los textos son
 * editables desde «Editar página», se guardan por página, y NO se pintan en
 * las rutas de país, donde ese trabajo lo hace el bloque por país.
 *
 * Criterio de redacción: mismo que en el resto del sitio. Se explica el cómo y
 * el porqué, los datos concretos —plazos, impuestos, restricciones— se enlazan
 * a la página que los tiene, y no se afirma nada que el sitio no sostenga ya.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ═══════════════════════════════════════════════════════════════════
 * 1) Secciones nuevas (se suman a las de inc/paginas-contenido-seo.php)
 * ═════════════════════════════════════════════════════════════════ */
add_filter( 'grenvios_pseo_secciones', function ( $m ) {

	$m['envio-internacional-de-documentos'] = array(
		'sub'    => 'Desde {{origen_ciudad}}',
		'titulo' => 'Qué mira la aduana en un <span class="hl">documento</span>',
		'html'   => '<p>Un documento personal o legal no tiene valor comercial: no paga impuestos y no entra en el cálculo de aduana como una mercancía. Esa es la buena noticia y también el origen del error más común, porque quien lo declara con un valor «por si acaso» convierte un envío exento en uno que se revisa.</p>
<ul class="gr-pseo-list">
<li><strong>Se declara por lo que es.</strong> «Documentos personales», «título universitario apostillado», «contrato firmado». Sin valor comercial.</li>
<li><strong>Si tiene que surtir efecto legal fuera,</strong> necesita <a href="%H%/servicios/apostilla-y-traduccion/">apostilla y a veces traducción oficial</a>, y ese trámite se hace aquí, en {{origen_pais}}, antes de enviar. Enviar rápido un documento que luego no aceptan es perder el plazo dos veces.</li>
<li><strong>Los datos del destinatario mandan.</strong> En un sobre, la etiqueta es lo único que lo identifica: nombre completo, dirección exacta y teléfono.</li>
</ul>
<p>Los documentos viajan por vía aérea, que es la más rápida, y por su peso son de los envíos más económicos del catálogo. Si tienes una fecha límite, dínosla al despachar: <a href="%H%/tiempos-de-entrega/">el plazo se cuenta en días hábiles desde el despacho</a>, no desde que nos escribes.</p>',
	);

	$m['envio-internacional-de-paquetes'] = array(
		'sub'    => 'Antes de traer el paquete',
		'titulo' => 'Tres decisiones que cambian <span class="hl">lo que pagas</span>',
		'html'   => '<p>Un paquete idéntico puede costar bastante distinto según tres decisiones que se toman antes de llegar a nuestra oficina de {{origen_ciudad}}. Ninguna tiene que ver con el contenido.</p>
<ol class="gr-pseo-steps">
<li><strong>El tamaño de la caja.</strong> Se cobra el mayor entre el peso real y el <a href="%H%/servicios/peso-volumetrico/">volumétrico</a>, y el volumétrico se calcula con las medidas exteriores. Una caja holgada se paga como si el aire fuera mercancía.</li>
<li><strong>La vía.</strong> La aérea se paga por rapidez y la terrestre por volumen. Para un bulto que abulta y no corre prisa hacia un país vecino, la diferencia es grande; <a href="%H%/destinos/">no todos los destinos tienen las dos</a>.</li>
<li><strong>Juntar o separar.</strong> Tres paquetes pequeños pagan tres fletes. Consolidados en un bulto ajustado, uno.</li>
</ol>
<p>Lo demás lo resolvemos aquí: embalamos, pesamos, medimos y revisamos la declaración antes de despachar. Si no quieres acercarte, <a href="%H%/recojo-a-domicilio-lima/">recogemos en tu distrito</a> y hacemos lo mismo en el momento del recojo.</p>',
	);

	$m['apostilla-y-traduccion'] = array(
		'sub'    => 'El orden correcto',
		'titulo' => 'Primero se legaliza, <span class="hl">después se envía</span>',
		'html'   => '<p>El error que más plazos rompe no es de transporte: es hacer los pasos en el orden equivocado. Un documento traducido antes de apostillar obliga a repetir la traducción, y uno enviado sin apostillar llega pero no sirve para el trámite.</p>
<ol class="gr-pseo-steps">
<li><strong>Documento original o copia certificada válida.</strong> Sin esto no empieza nada.</li>
<li><strong>Legalización y apostilla</strong> en {{origen_pais}}. Certifica que la firma y el cargo de quien lo emitió son auténticos.</li>
<li><strong>Traducción oficial,</strong> si el país de destino no tiene el español como idioma oficial. A veces la traducción también se apostilla.</li>
<li><strong>Envío</strong> por vía aérea con seguimiento, que es lo que corresponde a un documento.</li>
</ol>
<p>Son <strong>dos relojes que se suman</strong>: el del trámite, que corre aquí, y el del envío, que empieza cuando el documento ya está listo. Por eso conviene empezar en cuanto conoces la fecha del destino. Si el documento además viaja con otras cosas, mira <a href="%H%/servicios/envio-internacional-de-documentos/">cómo se declara un envío de documentos</a>.</p>',
	);

	$m['peso-volumetrico'] = array(
		'sub'    => 'En la práctica',
		'titulo' => 'Cómo bajar el peso que <span class="hl">te cobran</span>',
		'html'   => '<p>El peso volumétrico no es una penalización: es la forma en que todas las empresas de envío cobran el espacio, porque en el avión y en el camión lo que escasea es el sitio, no la capacidad de carga. Entenderlo sirve para una cosa muy concreta: pagar menos sin enviar menos.</p>
<ul class="gr-pseo-list">
<li><strong>Caja a medida del contenido.</strong> Es la palanca principal. En nuestra sede de {{origen_ciudad}} tenemos varios tamaños y elegimos el que ajusta.</li>
<li><strong>Comprimir lo blando.</strong> Ropa, edredones y peluches en bolsas al vacío pueden reducir el volumen a un tercio.</li>
<li><strong>Desmontar lo que se pueda</strong> y quitar los embalajes de tienda: cajas dentro de cajas y relleno de aire son centímetros que se pagan.</li>
<li><strong>Preguntar por la vía terrestre</strong> cuando el destino la tiene: castiga menos el volumen.</li>
</ul>
<p>Trae el bulto o dinos sus medidas y te decimos por cuál de los dos pesos se cobra antes de que lo cierres. Es el momento en que todavía se puede cambiar algo: <a href="%H%/cotizar/">cotiza con tus medidas</a>.</p>',
	);

	$m['envio-de-equipaje'] = array(
		'sub'    => 'Frente al exceso de equipaje',
		'titulo' => 'Cuándo sale a cuenta <span class="hl">enviarlo</span>',
		'html'   => '<p>La aerolínea cobra el exceso por pieza y sin negociar; nosotros cobramos por peso o volumen. La comparación no siempre gana el mismo, y conviene hacerla con números antes de facturar en el aeropuerto.</p>
<ul class="gr-pseo-list">
<li><strong>Segunda maleta en adelante:</strong> el envío suele salir mejor, sobre todo si no la necesitas los primeros días.</li>
<li><strong>Mudanza o estudios:</strong> casi siempre conviene enviar. Lo que va por delante viaja mientras tú resuelves lo demás.</li>
<li><strong>Un bulto pequeño y urgente:</strong> ahí la maleta de cabina gana.</li>
</ul>
<p>Lo que nunca se envía en bodega —ni de avión ni nuestro— son documentos, medicinas y <a href="%H%/que-se-puede-enviar/">electrónica con batería interna</a>: eso viaja contigo. Y el equipaje personal usado tiene un trato aduanero distinto al de la mercancía nueva, así que conviene declarar cada bulto por separado y no mezclarlos. Si te mudas, <a href="%H%/recojo-a-domicilio-lima/">recogemos en tu domicilio</a> el día que te venga bien.</p>',
	);

	$m['que-se-puede-enviar'] = array(
		'sub'    => 'La pregunta correcta',
		'titulo' => 'No es qué se puede enviar, <span class="hl">es a dónde y por qué vía</span>',
		'html'   => '<p>Casi todas las consultas llegan formuladas así: «¿se puede enviar esto?». Y la respuesta honesta casi siempre empieza igual: depende de dos cosas que no están en el producto.</p>
<ul class="gr-pseo-list">
<li><strong>La vía.</strong> La bodega de un avión no admite lo mismo que un camión: líquidos, aerosoles, alimentos y artículos con batería interna son el ejemplo habitual. Un producto puede viajar por carretera a un país vecino y estar prohibido por aire al mismo destino.</li>
<li><strong>La aduana del país de destino.</strong> Fija su propia lista, sus límites de cantidad y su umbral de valor. La de un país no vale para su vecino: <a href="%H%/destinos/">cada ficha de destino</a> resume lo que admite la suya.</li>
</ul>
<p>Por eso, cuando preguntes, dinos el <strong>nombre exacto del producto, el país y la vía</strong>. Con esos tres datos la respuesta es un sí o un no, no un «depende». Y si la aduana retiene algo, el flete no se devuelve: cinco minutos de consulta antes de comprar la caja evitan perder el envío entero. <a href="%H%/aduanas-e-impuestos/">Cómo funciona la aduana</a> · <a href="%H%/cotizar/">Consultar antes de enviar</a>.</p>',
	);

	$m['aduanas-e-impuestos'] = array(
		'sub'    => 'Quién paga y cuándo',
		'titulo' => 'El impuesto no aparece <span class="hl">al final</span>',
		'html'   => '<p>La queja clásica con los envíos internacionales es descubrir un cobro al recibir. Trabajamos al revés: si la ruta tiene impuesto, se calcula sobre el valor declarado y <strong>se paga aquí, en {{origen_ciudad}}, antes de despachar</strong>. Quien recibe no adelanta dinero.</p>
<ul class="gr-pseo-list">
<li><strong>La base es el valor declarado,</strong> no el flete. Por eso declarar de menos no abarata el trámite —la aduana conoce los precios— y además deja el envío sin <a href="%H%/seguro-de-envios/">cobertura real</a>.</li>
<li><strong>Cada país fija su umbral.</strong> Por debajo de cierto valor muchos envíos no tributan; por encima, sí. Te decimos el de tu destino antes de enviar.</li>
<li><strong>Las retenciones casi nunca son por impuestos.</strong> Son por documentación: una descripción vaga del contenido o una factura que no coincide con lo que va dentro.</li>
</ul>
<p>Nosotros gestionamos el trámite si ocurre, pero el trabajo de verdad está antes: revisamos la declaración contigo en el despacho. <a href="%G:documentos-para-aduana%">Qué papeles revisa la aduana</a> · <a href="%H%/que-se-puede-enviar/">Qué admite cada destino</a>.</p>',
	);

	$m['envios-para-empresas'] = array(
		'sub'    => 'Exportar desde {{origen_pais}}',
		'titulo' => 'Lo que frena a una empresa <span class="hl">no es el transporte</span>',
		'html'   => '<p>Cuando una empresa empieza a enviar al extranjero con frecuencia, el cuello de botella deja de ser el flete y pasa a ser el proceso: cada despacho se prepara desde cero, cada factura se redacta distinta, y basta una descripción de producto que cambia de un envío a otro para que la aduana de destino empiece a revisarlos todos.</p>
<ul class="gr-pseo-list">
<li><strong>Documentación estandarizada.</strong> Una plantilla de factura comercial y descripciones fijas por producto: el envío cincuenta pasa igual de limpio que el primero.</li>
<li><strong>Recojo programado</strong> en tu local o en el de tu proveedor dentro de {{origen_ciudad}}, con días fijos, sin pedirlo cada vez.</li>
<li><strong>Tarifa por volumen</strong> y facturación consolidada al mes: una factura, no cincuenta.</li>
<li><strong>Un asesor que conoce tus productos</strong> y tus destinos, para no explicar la empresa desde cero en cada llamada.</li>
</ul>
<p>Se empieza sin contratos largos ni volúmenes mínimos: medimos tus envíos reales durante unas semanas y ajustamos flujo y tarifa con datos. <a href="%H%/servicios/carga-internacional/">Carga internacional</a> · <a href="%H%/contacto/">Hablemos de tu operación</a>.</p>',
	);

	$m['nosotros'] = array(
		'sub'    => 'Por qué un operador de aquí',
		'titulo' => 'La diferencia entre un courier grande <span class="hl">y uno que te contesta</span>',
		'html'   => '<p>Los grandes couriers internacionales tienen una red enorme y un mostrador donde nadie sabe qué hay en tu caja. Nosotros operamos desde {{origen_ciudad}} y el equipo que te cotiza es el mismo que revisa tu envío antes de que salga. Eso cambia tres cosas concretas.</p>
<ul class="gr-pseo-list">
<li><strong>La vía terrestre existe.</strong> Para los países vecinos es una alternativa real y bastante más económica en bultos voluminosos; un operador que solo vuela no puede ofrecerla.</li>
<li><strong>El contenido se revisa antes, no después.</strong> La mayoría de las retenciones en aduana nacen en origen y se evitan en origen. Preferimos decirte «esto no entra» aquí que descubrirlo tu destinatario allá.</li>
<li><strong>Hay alguien al otro lado.</strong> Un número de WhatsApp con una persona que sabe en qué tramo está tu envío, no un número de guía en una web.</li>
</ul>
<p>Lo que sí compartimos con los grandes es la parte que no admite atajos: seguimiento de principio a fin, embalaje serio y <a href="%H%/seguro-de-envios/">cobertura sobre el valor declarado</a>. <a href="%H%/destinos/">Las rutas que operamos</a> · <a href="%H%/contacto/">Dónde estamos</a>.</p>',
	);

	return $m;
} );

/* Preguntas propias de esas páginas (se suman y se deduplican solas). */
add_filter( 'grenvios_pseo_faqs', function ( $f ) {
	$f['envio-internacional-de-documentos'] = array(
		array( '¿Un documento paga impuestos?', 'No. Los documentos personales y legales no tienen valor comercial y no tributan. Se declaran como lo que son; ponerles un valor «por si acaso» es lo que los mete en una revisión de aduana sin motivo.' ),
		array( '¿Cómo sé si mi documento necesita apostilla?', 'Si va a presentarse ante una institución pública, una universidad o un notario del país de destino, casi con seguridad la necesita. Dinos qué documento es y para qué trámite y te lo confirmamos antes de enviar.' ),
	);
	$f['envio-internacional-de-paquetes'] = array(
		array( '¿Puedo llevar el paquete sin cerrar?', 'Es lo que preferimos. Revisamos el contenido contigo antes de embalar: es el momento de detectar algo que la aduana del destino no admite, y de elegir una caja que no te haga pagar por aire.' ),
		array( '¿Tienen cajas en la oficina?', 'Sí, de varios tamaños, y elegimos la que ajusta al contenido. Una caja a medida es la forma más rápida de bajar el peso volumétrico y con él el precio.' ),
	);
	$f['peso-volumetrico'] = array(
		array( '¿Por qué mi caja liviana cuesta como si pesara 20 kilos?', 'Porque se cobra el mayor entre el peso real y el volumétrico (alto × largo × ancho ÷ 5000), y el volumétrico lo marca el tamaño de la caja. Ajustarla al contenido es lo que cambia la cifra.' ),
		array( '¿El divisor 5000 es igual en todas las empresas?', 'Es el estándar en envíos internacionales aéreos y es el que aplicamos. Lo que cambia entre unas empresas y otras es la tarifa por kilo, no la forma de calcular el peso que se cobra.' ),
	);
	$f['que-se-puede-enviar'] = array(
		array( '¿Puedo enviar celulares o laptops?', 'Por vía aérea no: los artículos con batería interna están prohibidos en bodega. Por vía terrestre sí se pueden enviar hacia varios destinos; dinos el país y te confirmamos si entra.' ),
		array( '¿Qué pasa si envío algo no permitido?', 'La aduana puede retenerlo, devolverlo o destruirlo, y el flete no se devuelve en ninguno de los tres casos. Por eso revisamos el contenido contigo antes de despachar.' ),
	);
	$f['aduanas-e-impuestos'] = array(
		array( '¿Mi destinatario tendrá que pagar algo al recibir?', 'En las rutas con impuesto, el cálculo se hace sobre el valor declarado y se paga aquí, en {{origen_ciudad}}, antes de despachar. Te decimos el importe antes de enviar, nunca después.' ),
	);
	$f['envios-para-empresas'] = array(
		array( '¿Hay un volumen mínimo para abrir una cuenta?', 'No. Se empieza con tus envíos reales, se mide el volumen durante unas semanas y con esos datos se ajusta la tarifa y el flujo. Sin contratos largos.' ),
	);
	$f['nosotros'] = array(
		array( '¿Dónde está la oficina y qué horario tienen?', 'Estamos en {{contacto_direccion}}. Atendemos {{contacto_horario}}, y coordinamos recojos a domicilio en {{origen_ciudad}} dentro de ese horario.' ),
	);
	return $f;
} );

/* ═══════════════════════════════════════════════════════════════════
 * 1 bis) Más preguntas en la página de Preguntas Frecuentes
 *
 * Tenía diez preguntas y 469 palabras, y es la página a la que apuntan todas
 * las demás cuando alguien duda. Las diez del tema resuelven QUÉ se puede
 * enviar; faltaban las de precio, plazo, seguimiento y trámite, que son las
 * que la gente escribe en el buscador antes de decidirse. Se añaden a la lista
 * global, así que entran también en el FAQPage del schema.
 * ═════════════════════════════════════════════════════════════════ */
add_filter( 'grenvios_page_faqs', function ( $faqs, $slug ) {
	if ( $slug !== 'preguntas-frecuentes' ) return $faqs;
	if ( function_exists( 'grenvios_hq_pais' ) && grenvios_hq_pais() !== '' ) return $faqs;   // las rutas tienen las suyas

	$extra = array(
		array(
			'¿Cuánto cuesta enviar al extranjero desde {{origen_pais}}?',
			'Depende del peso que se cobre, de la vía y del destino. Se cobra el mayor entre el peso real y el volumétrico (alto × largo × ancho en cm ÷ 5000), así que el tamaño de la caja influye tanto como lo que pesa. Envíanos peso, medidas y ciudad de destino y te damos precio cerrado en minutos.',
		),
		array(
			'¿Cuánto demora un envío internacional?',
			'Según el destino y la modalidad, y siempre en días hábiles desde el despacho, no desde que nos escribes. El paso por la aduana del país de destino puede sumar días que no dependen del transporte. En la ficha de cada destino publicamos su plazo estimado.',
		),
		array(
			'¿Cómo sigo mi envío?',
			'Al despachar recibes un número de guía que identifica tu envío en todos los tramos. Con él te confirmamos por WhatsApp en qué punto está: despachado, en tránsito, en aduana, en reparto o entregado.',
		),
		array(
			'¿Qué pasa si mi envío se queda retenido en aduana?',
			'Nos ocupamos nosotros del trámite y te decimos qué falta, que casi siempre es un dato de la factura o del contenido declarado. Por eso revisamos la documentación antes de que el envío salga de {{origen_ciudad}}: la mayoría de las retenciones se evitan en origen.',
		),
		array(
			'¿Puedo enviar si no estoy en {{origen_ciudad}}?',
			'Sí. Mandas el bulto a nuestra sede por la agencia de transporte local que prefieras y desde aquí sale a su destino internacional. El flete internacional es el mismo; lo que pagas aparte es ese tramo interno.',
		),
		array(
			'¿Atienden a empresas?',
			'Sí. Para empresas que envían de forma recurrente trabajamos con recojo programado, documentación estandarizada, tarifa por volumen y facturación consolidada al mes, sin contratos largos ni volúmenes mínimos.',
		),
		array(
			'¿El seguro está incluido?',
			'La cobertura se calcula sobre el valor declarado del envío. Por eso conviene declarar el valor real: declarar de menos no abarata el trámite y deja el envío cubierto por esa cifra menor si se pierde o se daña.',
		),
		array(
			'¿Emiten comprobante de pago?',
			'Sí, emitimos boleta o factura a nombre de quien lo solicite. Para empresas, además, consolidamos los envíos del mes en una sola factura.',
		),
	);

	return array_merge( (array) $faqs, $extra );
}, 24, 2 );

/* ═══════════════════════════════════════════════════════════════════
 * 2) Más enlazado interno en las páginas que menos tenían
 * ═════════════════════════════════════════════════════════════════ */
add_filter( 'grenvios_related_map', function ( $mapa ) {
	$extra = array(
		'envio-de-compras' => array(
			'Antes de comprar'  => array( 'que-se-puede-enviar', 'aduanas-e-impuestos', 'servicios/peso-volumetrico' ),
			'Cómo llega'        => array( 'tiempos-de-entrega', 'rastreo-de-envios', 'destinos' ),
			'Siguiente paso'    => array( 'cotizar', 'contacto' ),
		),
		'seguro-de-envios' => array(
			'Lo que influye'    => array( 'aduanas-e-impuestos', 'que-se-puede-enviar', 'servicios/peso-volumetrico' ),
			'Servicios'         => array( 'servicios/envio-internacional-de-paquetes', 'servicios/carga-internacional' ),
			'Siguiente paso'    => array( 'cotizar', 'rastreo-de-envios' ),
		),
		'envio-de-alimentos' => array(
			'Antes de enviar'   => array( 'que-se-puede-enviar', 'aduanas-e-impuestos', 'destinos' ),
			'Te puede interesar'=> array( 'servicios/envio-internacional-de-paquetes', 'servicios/envio-de-compras' ),
			'Siguiente paso'    => array( 'cotizar', 'contacto' ),
		),
		'rastreo-de-envios' => array(
			'Sobre los plazos'  => array( 'tiempos-de-entrega', 'aduanas-e-impuestos', 'destinos' ),
			'Servicios'         => array( 'servicios/envio-internacional-de-paquetes', 'servicios/envio-internacional-de-documentos' ),
			'Siguiente paso'    => array( 'cotizar', 'contacto' ),
		),
		'peso-volumetrico' => array(
			'Servicios donde aplica' => array( 'servicios/envio-internacional-de-paquetes', 'servicios/carga-internacional', 'servicios/envio-de-equipaje' ),
			'Antes de enviar'   => array( 'que-se-puede-enviar', 'como-enviar-un-paquete-al-extranjero' ),
			'Siguiente paso'    => array( 'cotizar', 'recojo-a-domicilio-lima' ),
		),
		'recojo-a-domicilio-lima' => array(
			'Servicios'         => array( 'servicios/envio-internacional-de-paquetes', 'servicios/envio-de-equipaje', 'servicios/envio-de-compras' ),
			'Si no estás aquí'  => array( 'envios-desde-provincias', 'contacto' ),
			'Siguiente paso'    => array( 'cotizar', 'tiempos-de-entrega' ),
		),
		'envios-desde-provincias' => array(
			'Cómo funciona'     => array( 'como-enviar-un-paquete-al-extranjero', 'servicios/peso-volumetrico', 'que-se-puede-enviar' ),
			'Si estás aquí'     => array( 'recojo-a-domicilio-lima' ),
			'Siguiente paso'    => array( 'cotizar', 'tiempos-de-entrega', 'contacto' ),
		),
		'contacto' => array(
			'Mientras tanto'    => array( 'cotizar', 'preguntas-frecuentes', 'rastreo-de-envios' ),
			'Servicios'         => array( 'servicios/envio-internacional-de-paquetes', 'servicios/envio-internacional-de-documentos', 'servicios/carga-internacional' ),
			'Cobertura'         => array( 'recojo-a-domicilio-lima', 'envios-desde-provincias', 'destinos' ),
		),
		'preguntas-frecuentes' => array(
			'Servicios'         => array( 'servicios/envio-internacional-de-documentos', 'servicios/envio-internacional-de-paquetes', 'servicios/carga-internacional', 'servicios/apostilla-y-traduccion' ),
			'Antes de enviar'   => array( 'que-se-puede-enviar', 'servicios/peso-volumetrico', 'aduanas-e-impuestos', 'tiempos-de-entrega' ),
			'Siguiente paso'    => array( 'cotizar', 'rastreo-de-envios', 'contacto' ),
		),
		'articulos-por-pais' => array(
			'Antes de enviar'   => array( 'que-se-puede-enviar', 'aduanas-e-impuestos', 'servicios/envio-de-alimentos' ),
			'Destinos'          => array( 'destinos' ),
			'Siguiente paso'    => array( 'cotizar', 'contacto' ),
		),
		'nosotros' => array(
			'Lo que hacemos'    => array( 'servicios/envio-internacional-de-documentos', 'servicios/envio-internacional-de-paquetes', 'servicios/carga-internacional', 'servicios/apostilla-y-traduccion' ),
			'Cobertura'         => array( 'destinos', 'recojo-a-domicilio-lima', 'envios-desde-provincias' ),
			'Siguiente paso'    => array( 'cotizar', 'contacto', 'envios-para-empresas' ),
		),
	);

	foreach ( $extra as $slug => $grupos ) $mapa[ $slug ] = $grupos;
	return $mapa;
}, 20 );
