<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Segundas secciones: comparativa, glosario y páginas que quedaban cortas
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Con la primera tanda (inc/peru-seo.php) ninguna página de la ruta principal
 * bajaba ya de las 500 palabras, pero quedaban dos huecos de fondo que no se
 * arreglan alargando párrafos:
 *
 *   · No había NINGÚN contenido comparativo. «Courier o correo postal»,
 *     «enviar o pagar exceso de equipaje», «aérea o terrestre» son consultas
 *     con intención de decisión —las que convierten— y no se respondían.
 *   · No había glosario. Peso volumétrico, valor declarado, guía, despacho,
 *     puerta a puerta y consolidar se usan en todo el sitio dando por hecho
 *     que se entienden, y son justo lo que busca quien envía por primera vez.
 *
 * Son SEGUNDAS secciones de páginas que ya tenían una (el módulo admite varias
 * por página desde el refactor de inc/paginas-contenido-seo.php), así que
 * heredan todo: editables desde el front, guardadas por página y no se pintan
 * en las rutas de país, donde manda el contenido por destino.
 *
 * Prioridad 30 para correr después de inc/peru-seo.php (20) y respetar lo que
 * ya haya: la sección existente se conserva y la nueva se añade detrás.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_filter( 'grenvios_pseo_secciones', function ( $m ) {

	/* Convierte el valor en lista si aún era una sección suelta y añade la nueva. */
	$mas = function ( $slug, $nueva ) use ( &$m ) {
		if ( ! isset( $m[ $slug ] ) ) { $m[ $slug ] = array( $nueva ); return; }
		$actual   = isset( $m[ $slug ]['html'] ) ? array( $m[ $slug ] ) : array_values( $m[ $slug ] );
		$actual[] = $nueva;
		$m[ $slug ] = $actual;
	};

	/* ── Comparativa: la decisión que se toma antes de elegir empresa ── */
	$mas( 'servicios', array(
		'sub'    => 'Comparado con las alternativas',
		'titulo' => 'Courier, correo postal o <span class="hl">llevarlo en la maleta</span>',
		'html'   => '<p>Antes de elegir empresa, casi todo el mundo compara tres caminos. Cada uno gana en un escenario distinto y conviene decirlo sin adornos:</p>
<ul class="gr-pseo-list">
<li><strong>Correo postal.</strong> Suele ser lo más barato en envíos muy pequeños y ligeros, y también lo más lento. Nadie revisa contigo el contenido antes de despachar ni gestiona el envío si se detiene en aduana: el seguimiento existe, el acompañamiento no.</li>
<li><strong>Llevarlo en la maleta.</strong> Imbatible si ya viajas y cabe. Deja de serlo en la segunda maleta, cuando el exceso de equipaje se cobra por pieza: ahí <a href="%H%/servicios/envio-de-equipaje/">enviarlo por delante</a> suele salir mejor y además viajas ligero.</li>
<li><strong>Courier.</strong> Gana cuando importa el plazo, cuando el contenido necesita que alguien revise si entra en el país de destino, o cuando el bulto abulta y conviene poder elegir <a href="%H%/servicios/envio-internacional-de-paquetes/">entre vía aérea y terrestre</a>.</li>
</ul>
<p>Y entre couriers, lo que de verdad mueve el precio no es la marca: es si ofrecen <strong>vía terrestre</strong> —que hacia países vecinos y con bultos voluminosos cambia la factura— y si revisan el contenido en origen, que es lo que evita la retención que cuesta días. Pide siempre el <strong>precio total puesto en destino</strong>: es la única cifra comparable entre presupuestos.</p>',
	) );

	/* ── Glosario: los seis términos que aparecen en toda cotización ── */
	$mas( 'cotizar', array(
		'sub'    => 'En claro',
		'titulo' => 'Seis palabras que verás al <span class="hl">cotizar tu envío</span>',
		'html'   => '<ul class="gr-pseo-list">
<li><strong>Peso volumétrico.</strong> El peso que corresponde al espacio que ocupa la caja: alto × largo × ancho en centímetros, dividido entre 5000. Se compara con el peso real y se cobra el mayor de los dos. <a href="%H%/servicios/peso-volumetrico/">Calcúlalo aquí</a>.</li>
<li><strong>Valor declarado.</strong> Lo que vale el contenido. Es la base del impuesto en destino y de la cobertura del envío, así que declarar de menos no abarata: deja el envío asegurado por esa cifra menor.</li>
<li><strong>Despacho.</strong> El momento en que el envío sale con su documentación de exportación. Es cuando empieza a contar el plazo, no cuando nos escribes.</li>
<li><strong>Guía.</strong> El número que identifica tu envío en todos los tramos. Con él se consulta el estado y se resuelve cualquier incidencia.</li>
<li><strong>Puerta a puerta.</strong> Que el envío llega al domicilio del destinatario. Donde no aplica, se retira en una agencia local de su ciudad: <a href="%H%/destinos/">cada destino indica cuál es su caso</a>.</li>
<li><strong>Consolidar.</strong> Juntar varios paquetes en un solo bulto para pagar un flete en vez de varios. Es lo que más ahorra a quien <a href="%H%/servicios/envio-de-compras/">compra en varias tiendas</a>.</li>
</ul>
<p>Si algo de la cotización no se entiende, pregúntalo antes de despachar: no cuesta nada y evita la sorpresa que ninguna empresa quiere dar.</p>',
	) );

	/* ── Hub de destinos: cómo decidir a cuál entrar ── */
	$mas( 'destinos', array(
		'sub'    => 'Antes de elegir',
		'titulo' => 'Qué mirar en la ficha de <span class="hl">cada destino</span>',
		'html'   => '<p>Todas las fichas traen los mismos cinco datos y en el mismo orden, para que puedas comparar destinos sin leerlas enteras:</p>
<ol class="gr-pseo-steps">
<li><strong>Modalidad.</strong> Si hay vía aérea, terrestre o las dos. Es lo que más cambia el precio en bultos voluminosos.</li>
<li><strong>Plazo estimado.</strong> En días hábiles desde el despacho, no desde tu consulta: <a href="%H%/tiempos-de-entrega/">aquí explicamos cómo se cuentan</a>.</li>
<li><strong>Forma de entrega.</strong> A domicilio o con retiro en agencia local. Determina qué datos necesitas del destinatario.</li>
<li><strong>Qué admite su aduana</strong> y, cuando la ruta lo tiene, el umbral de valor a partir del cual el envío paga impuestos.</li>
<li><strong>Ciudades</strong> a las que más se envía, para comprobar si la de tu destinatario aparece.</li>
</ol>
<p>Si tu país no tiene ficha propia, no significa que no lleguemos: trabajamos más de treinta destinos y varios se coordinan bajo pedido. <a href="%H%/cotizar/">Escríbenos con el país</a> y te confirmamos vía, plazo y condiciones reales antes de que compres la caja.</p>',
	) );

	/* ── Contacto: intención local, con lo que hay que traer ── */
	$mas( 'contacto', array(
		'sub'    => 'Antes de venir',
		'titulo' => 'Qué traer y qué dejar <span class="hl">resuelto</span>',
		'html'   => '<p>Nos encuentras en {{contacto_direccion}}. Si vas a acercarte a despachar, con esto se resuelve todo en una sola visita:</p>
<ul class="gr-pseo-list">
<li><strong>Tu documento de identidad</strong> y los datos completos del destinatario: nombre, dirección exacta, teléfono y, según el país, su número de identificación.</li>
<li><strong>El contenido sin cerrar.</strong> Lo revisamos contigo antes de embalar: es el momento de detectar algo que la aduana del destino no admite y de elegir una caja que no te haga pagar por aire.</li>
<li><strong>La boleta o factura</strong> si lo que envías es mercancía nueva.</li>
<li><strong>Tu fecha límite,</strong> si la tienes. Con ella elegimos la vía que llega a tiempo en vez de la más barata.</li>
</ul>
<p>Y si no puedes venir, no hace falta: <a href="%H%/recojo-a-domicilio-lima/">recogemos en tu distrito</a> y hacemos la misma revisión en el momento del recojo. Desde fuera de {{origen_ciudad}}, <a href="%H%/envios-desde-provincias/">así funciona el envío desde provincias</a>.</p>',
	) );

	/* ── Compras: era la página de servicio más delgada del sitio ── */
	$mas( 'envio-de-compras', array(
		'sub'    => 'Casos frecuentes',
		'titulo' => 'Lo que más nos piden <span class="hl">consolidar</span>',
		'html'   => '<ul class="gr-pseo-list">
<li><strong>Varias tiendas, un solo envío.</strong> El caso clásico: tres o cuatro pedidos que llegan en semanas distintas y salen juntos. Se paga un flete en vez de cuatro.</li>
<li><strong>Compras de temporada.</strong> Ofertas y liquidaciones que conviene aprovechar aunque el envío salga más tarde.</li>
<li><strong>Productos peruanos difíciles de conseguir fuera:</strong> textiles, artesanía, café y cacao envasados, marcas locales de ropa y calzado.</li>
<li><strong>Repuestos y piezas</strong> que solo vende un proveedor local y que no despacha al extranjero.</li>
</ul>
<p>En todos ellos el trabajo es el mismo: <strong>retirar el embalaje sobrante de cada tienda</strong> —cajas dentro de cajas, relleno de aire— y armar un bulto ajustado. Es lo que baja el <a href="%H%/servicios/peso-volumetrico/">peso volumétrico</a>, que es por lo que se cobra casi todo lo que abulta y pesa poco.</p>
<p>Antes de comprar, comprueba <a href="%H%/que-se-puede-enviar/">qué admite tu país</a>: perfumes, cosméticos, suplementos y electrónica con batería tienen reglas propias, y una compra que no puede salir es dinero perdido dos veces.</p>',
	) );

	/* ── Blog: por dónde empezar. Era la página más delgada de la ruta (592
	 *    palabras) y enlazaba solo a las últimas entradas, nunca a los temas. ── */
	$mas( 'blog', array(
		'sub'    => 'Por dónde empezar',
		'titulo' => 'Las guías, ordenadas por <span class="hl">lo que necesitas saber</span>',
		'html'   => '<p>Publicamos desde la operación: lo que preguntan en el mostrador, lo que retiene una aduana y lo que encarece un envío sin que nadie lo avise. Están agrupadas por tema para que no tengas que leer el blog entero.</p>
<ul class="gr-pseo-list">
<li><strong>Si es tu primer envío:</strong> <a href="%T%envio-de-paquetes/">cómo se envía un paquete</a> y <a href="%T%embalaje/">cómo embalarlo</a> para que no pagues por aire.</li>
<li><strong>Si lo que te preocupa es el precio:</strong> <a href="%T%precios-de-envio/">qué define el precio en cada destino</a> y <a href="%T%peso-volumetrico/">cómo se calcula el peso que se cobra</a>.</li>
<li><strong>Si tienes una fecha:</strong> <a href="%T%plazos-de-entrega/">los plazos país por país</a> y <a href="%T%fechas-clave/">con cuánta antelación enviar en temporada alta</a>.</li>
<li><strong>Si dudas de lo que va dentro:</strong> <a href="%T%productos-prohibidos/">qué admite cada aduana</a> y <a href="%T%aduanas/">qué documentación se exige</a>.</li>
<li><strong>Si es un caso concreto:</strong> <a href="%T%apostilla-y-documentos/">documentos y apostilla</a>, <a href="%T%equipaje-y-mudanzas/">equipaje y mudanzas</a>, <a href="%T%compras-desde-el-extranjero/">compras hechas en {{origen_pais}}</a> o <a href="%T%encomiendas-familiares/">encomiendas a la familia</a>.</li>
<li><strong>Si ya sabes el destino:</strong> cada país tiene su propio grupo de guías —precio, plazo, aduana, ciudades—: están reunidas en las <a href="%H%/articulos-por-pais/">guías de envío por país</a> y enlazadas desde <a href="%H%/destinos/">su ficha de destino</a>.</li>
</ul>
<p>Y si después de leer sigue habiendo una duda concreta, <a href="%H%/cotizar/">escríbenos con el caso</a>: contestamos con tus medidas y tu destino delante, no con un texto general.</p>',
	) );

	return $m;
}, 30 );
