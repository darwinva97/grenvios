<?php
/**
 * Grenvíos — Páginas SEO de apoyo (fuera del menú).
 *
 * CUATRO intenciones de búsqueda que hoy no cubre ninguna otra página del sitio.
 * Se comprobó una por una contra el registro existente (21 páginas + destinos +
 * combinadas + 8 guías) para no repetir contenido, que es lo único que Google
 * castiga de verdad:
 *
 *   /aduanas-e-impuestos/      "¿cuánto voy a pagar de aduana?" — la duda que
 *                              frena la compra. Ninguna página la respondía.
 *   /servicios/envio-de-alimentos/  panetón, café, ají, chocolate a la familia.
 *                              Altísimo volumen en {{origen_pais}} y muy estacional.
 *   /seguro-de-envios/         "¿y si se pierde mi paquete?" — página de
 *                              confianza; convierte más de lo que atrae.
 *   /envios-desde-provincias/  SEO local fuera de {{origen_ciudad}} (Arequipa, Trujillo,
 *                              Cusco…), que hoy solo cubre recojo-a-domicilio-lima.
 *
 * NO ENTRAN AL MENÚ, A PROPÓSITO
 * El header ya tiene 20 enlaces; añadir cuatro más lo vuelve inservible en móvil
 * y reparte la autoridad entre demasiadas páginas. Estas viven del enlazado
 * interno: se registran en `grenvios_related_map` (abajo) para recibir enlaces
 * desde las páginas que ya posicionan, y salen en el sitemap como el resto.
 * Para Google es exactamente igual de válido: lo que cuenta es que reciban
 * enlaces internos, no que estén en la navegación principal.
 *
 * SOBRE LAS CIFRAS
 * Aquí NO se publican importes de franquicia ni porcentajes de arancel por país:
 * cambian sin aviso y publicar un número obsoleto es peor que no publicarlo. Se
 * explica el mecanismo (que sí es estable) y el dato concreto de cada país sale
 * del campo `restr` de `grenvios_destinos()`, que la clienta mantiene desde
 * «Destinos». Misma fuente que el resto del sitio: nunca se contradicen.
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ══════════════════════════════════════
   1) REGISTRO
══════════════════════════════════════ */
add_filter( 'grenvios_pages', function ( $pages ) {
	$nuevas = array(
		'aduanas-e-impuestos' => array(
			'title' => 'Aduanas e Impuestos',
			'seo'   => 'Impuestos y Aduana al Enviar desde {{origen_pais}} | Grenvíos',
			'desc'  => 'Quién paga la aduana en un envío internacional, cómo se calcula sobre el valor declarado y por qué se retiene un paquete. Explicado país por país.',
			'parent'=> '',
		),
		'envio-de-alimentos' => array(
			'title' => 'Envío de Alimentos',
			'seo'   => 'Enviar Alimentos Peruanos al Extranjero | Grenvíos',
			'desc'  => 'Envía panetón, café, cacao, ají o snacks peruanos a tu familia en el extranjero. Qué alimentos se aceptan, por qué vía y cómo embalarlos para que lleguen.',
			'parent'=> 'servicios',
		),
		'seguro-de-envios' => array(
			'title' => 'Seguro de Envíos',
			'seo'   => 'Seguro para Envíos Internacionales desde {{origen_pais}} | Grenvíos',
			'desc'  => 'Cómo se asegura un envío internacional, qué cubre el valor declarado y qué hacer si tu paquete se pierde o llega dañado. Pasos para reclamar.',
			'parent'=> '',
		),
		'envios-desde-provincias' => array(
			'title' => 'Envíos desde Provincias',
			'seo'   => 'Envíos Internacionales desde Provincias de {{origen_pais}} | Grenvíos',
			'desc'  => 'Envía al extranjero desde Arequipa, Trujillo, Cusco, Piura o Chiclayo. Cómo hacer llegar tu paquete a {{origen_ciudad}} y cuántos días suma al plazo total.',
			'parent'=> '',
		),
	);
	// Se insertan antes de «cotizar» para que queden dentro del bloque de
	// contenido y no al final del registro, igual que hacen los otros módulos.
	$out = array();
	foreach ( $pages as $slug => $def ) {
		if ( $slug === 'cotizar' ) foreach ( $nuevas as $k => $v ) $out[ $k ] = $v;
		$out[ $slug ] = $def;
	}
	foreach ( $nuevas as $k => $v ) if ( ! isset( $out[ $k ] ) ) $out[ $k ] = $v;
	return $out;
} );

function grenvios_seo_extra_slugs() {
	return array( 'aduanas-e-impuestos', 'envio-de-alimentos', 'seguro-de-envios', 'envios-desde-provincias' );
}

add_filter( 'grenvios_render_page_early', function ( $hecho, $slug ) {
	if ( $hecho || ! in_array( $slug, grenvios_seo_extra_slugs(), true ) ) return $hecho;
	switch ( $slug ) {
		case 'aduanas-e-impuestos':      grenvios_render_aduanas();     return true;
		case 'envio-de-alimentos':       grenvios_render_alimentos();   return true;
		case 'seguro-de-envios':         grenvios_render_seguro();      return true;
		case 'envios-desde-provincias':  grenvios_render_provincias();  return true;
	}
	return $hecho;
}, 12, 2 );

/* Lista con vistos, el patrón que ya usan las demás páginas. */
function grenvios_seo_extra_lista( $items, $key = '' ) {
	// Editable desde el panel: una línea por punto. Vacío = la lista del tema.
	$v = ( $key !== '' && function_exists( 'grenvios_field' ) ) ? trim( (string) grenvios_field( $key, '' ) ) : '';
	if ( $v !== '' ) $items = array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $v ) ) ) );
	echo '<ul class="check-list">';
	foreach ( $items as $t ) {
		echo '<li><i class="fa-solid fa-check"></i> ' . esc_html( grenvios_t( $t ) ) . '</li>';
	}
	echo '</ul>';
}

/* ══════════════════════════════════════
   2) ADUANAS E IMPUESTOS
   El valor está en explicar el MECANISMO, que no cambia, y en enlazar el dato
   de cada país. Es la pregunta que más frena una cotización.
══════════════════════════════════════ */
function grenvios_render_aduanas() {
	grenvios_tool_banner( 'Antes de enviar', 'Aduanas e impuestos en envíos internacionales' );
	?>
	<section class="srv-intro padding-top"><div class="container">
		<div class="srv-lead text-center">
			<h2><?php echo esc_html( grenvios_tf( 'ad_h2', 'El impuesto no lo cobra el courier: lo cobra el país que recibe' ) ); ?></h2>
			<p><?php echo esc_html( grenvios_tf( 'ad_intro', 'Esta es la parte que más confunde y la que más reclamos genera cuando nadie la explica a tiempo. El precio que pagas por el envío cubre el transporte. Lo que la aduana del país de destino decida cobrar es aparte, lo fija ese país y lo paga normalmente quien recibe el paquete, no quien lo envía. Saberlo antes evita la llamada incómoda de tu familiar preguntando por qué le piden dinero para retirar tu regalo.' ) ); ?></p>
		</div>
	</div></section>

	<section class="srv-section padding"><div class="container">
		<div class="srv-head text-center">
			<h2><?php echo esc_html( grenvios_tf( 'ad_como_title', 'De qué depende lo que te cobren' ) ); ?></h2>
		</div>
		<div class="srv-two-grid">
			<div class="srv-panel">
				<h3 class="srv-panel-title"><?php echo esc_html( grenvios_tf( 'ad_valor_title', 'El valor declarado' ) ); ?></h3>
				<p><?php echo esc_html( grenvios_tf( 'ad_valor_text', 'Es el precio real de lo que va dentro, y es la base sobre la que se calcula todo. Declararlo por debajo para «ahorrar» es la causa número uno de que un paquete quede retenido: si la aduana no se cree el valor, abre, tasa por su cuenta y suma multa. Declara lo que cuesta y adjunta la boleta.' ) ); ?></p>
			</div>
			<div class="srv-panel">
				<h3 class="srv-panel-title"><?php echo esc_html( grenvios_tf( 'ad_minimis_title', 'El mínimo exento de cada país' ) ); ?></h3>
				<p><?php echo esc_html( grenvios_tf( 'ad_minimis_text', 'Casi todos los países dejan entrar envíos sin cobrar impuestos por debajo de cierto valor. Por encima de ese umbral se paga, y el umbral es distinto en cada país y cambia con el tiempo. Por eso aquí no publicamos cifras: te confirmamos la vigente para tu destino al cotizar.' ) ); ?></p>
			</div>
			<div class="srv-panel">
				<h3 class="srv-panel-title"><?php echo esc_html( grenvios_tf( 'ad_tipo_title', 'Qué es lo que envías' ) ); ?></h3>
				<p><?php echo esc_html( grenvios_tf( 'ad_tipo_text', 'No paga igual un documento que ropa, electrónica o alimentos. Algunas categorías tienen permisos propios —sanitarios, por ejemplo— y otras están directamente prohibidas por vía aérea. Describir bien el contenido acelera el despacho.' ) ); ?></p>
			</div>
			<div class="srv-panel">
				<h3 class="srv-panel-title"><?php echo esc_html( grenvios_tf( 'ad_uso_title', 'Regalo o uso comercial' ) ); ?></h3>
				<p><?php echo esc_html( grenvios_tf( 'ad_uso_text', 'Un envío entre familiares y uno con fines de venta no se tratan igual. Enviar diez unidades del mismo producto se lee como comercial aunque sea un regalo, y ahí entran requisitos de importador. Si es tu caso, dilo antes: hay una forma correcta de hacerlo.' ) ); ?></p>
			</div>
		</div>
	</div></section>

	<section class="srv-section bg-grey padding"><div class="container">
		<div class="srv-head text-center">
			<h2><?php echo esc_html( grenvios_tf( 'ad_paises_title', 'Restricciones vigentes por país' ) ); ?></h2>
			<p><?php echo esc_html( grenvios_tf( 'ad_paises_text', 'Lo que cada destino exige y lo que no deja pasar. Entra en el país que te interesa para ver el detalle completo y los plazos.' ) ); ?></p>
		</div>
		<?php grenvios_render_tabla_destinos(); ?>
	</div></section>

	<section class="srv-section padding"><div class="container">
		<div class="srv-head text-center">
			<h2><?php echo esc_html( grenvios_tf( 'ad_retencion_title', 'Por qué se retiene un paquete' ) ); ?></h2>
			<p><?php echo esc_html( grenvios_tf( 'ad_retencion_text', 'Casi siempre por una de estas cinco razones, y las cinco se evitan antes de despachar:' ) ); ?></p>
		</div>
		<?php
		grenvios_seo_extra_lista( array(
			'El valor declarado no coincide con lo que la aduana estima que vale el contenido.',
			'La descripción es genérica: «regalo», «varios» o «muestras» no le dicen nada al inspector.',
			'Falta la boleta o factura que respalde el valor declarado.',
			'El contenido necesita un permiso que no se adjuntó (sanitario, farmacéutico, cultural).',
			'Quien recibe no responde a tiempo cuando la aduana le pide un dato o el pago del impuesto.',
		), 'ad_lista1' );
		?>
		<p class="gr-nota"><?php echo esc_html( grenvios_tf( 'ad_nota', 'Si un envío nuestro queda retenido, te avisamos y te acompañamos en el trámite: te decimos exactamente qué pide la aduana de ese país y quién debe presentarlo. No te dejamos resolverlo solo con un número de guía.' ) ); ?></p>
	</div></section>
	<?php
}

/* ══════════════════════════════════════
   3) ENVÍO DE ALIMENTOS
   Búsqueda muy peruana y muy estacional (panetón en noviembre-diciembre). El
   corte real es aéreo vs terrestre, y eso ya está en los datos de destinos.
══════════════════════════════════════ */
function grenvios_render_alimentos() {
	grenvios_tool_banner( 'Un pedazo de casa', 'Envío de alimentos al extranjero desde {{origen_pais}}' );
	?>
	<section class="srv-intro padding-top"><div class="container">
		<div class="srv-lead text-center">
			<h2><?php echo esc_html( grenvios_tf( 'al_h2', 'Sí se puede enviar comida, pero no toda ni por cualquier vía' ) ); ?></h2>
			<p><?php echo esc_html( grenvios_tf( 'al_intro', 'Es de lo que más nos piden, sobre todo en campaña navideña: panetón, café de Chanchamayo, cacao, ají en pasta, galletas, snacks. La regla que ordena todo es simple: cuanto más procesado y sellado de fábrica esté el producto, más fácil viaja. Lo fresco, lo casero y lo que lleva líquido es lo que da problemas.' ) ); ?></p>
		</div>
	</div></section>

	<section class="srv-section padding"><div class="container">
		<div class="srv-two-grid">
			<div class="srv-panel">
				<h3 class="srv-panel-title"><?php echo esc_html( grenvios_tf( 'al_si_title', 'Lo que normalmente sí viaja' ) ); ?></h3>
				<?php
				grenvios_seo_extra_lista( array(
					'Panetón y productos de panadería sellados de fábrica.',
					'Café y cacao en grano o molido, en su empaque original.',
					'Snacks, galletas, caramelos y chocolates industriales.',
					'Ají, especias y condimentos secos, envasados y etiquetados.',
					'Conservas y productos enlatados con fecha de vencimiento visible.',
				), 'al_lista1' );
				?>
			</div>
			<div class="srv-panel">
				<h3 class="srv-panel-title"><?php echo esc_html( grenvios_tf( 'al_no_title', 'Lo que casi nunca pasa la aduana' ) ); ?></h3>
				<?php
				grenvios_seo_extra_lista( array(
					'Carne, embutidos, lácteos y pescado, frescos o curados.',
					'Frutas y verduras frescas, y semillas sin certificado.',
					'Comida preparada en casa o sin etiqueta de fabricante.',
					'Líquidos y pastas en envase abierto o sin sellar.',
					'Productos sin fecha de vencimiento ni lista de ingredientes.',
				), 'al_lista2' );
				?>
			</div>
		</div>
		<p class="gr-nota"><?php echo esc_html( grenvios_tf( 'al_nota_pais', 'Ojo: esto es la norma general. Hay países especialmente estrictos con el origen animal y vegetal, y lo que entra sin problema a uno puede ser decomisado en el de al lado. Antes de comprar el producto, consúltanos por tu destino concreto.' ) ); ?></p>
	</div></section>

	<section class="srv-section bg-grey padding"><div class="container">
		<div class="srv-head text-center">
			<h2><?php echo esc_html( grenvios_tf( 'al_via_title', 'La vía cambia lo que puedes mandar' ) ); ?></h2>
		</div>
		<div class="srv-two-grid">
			<div class="srv-panel">
				<h3 class="srv-panel-title"><?php echo esc_html( grenvios_tf( 'al_aereo_title', 'Aéreo' ) ); ?></h3>
				<p><?php echo esc_html( grenvios_tf( 'al_aereo_text', 'Llega rápido y es la única opción fuera de Sudamérica, pero es la vía más restrictiva con los alimentos: nada de líquidos ni pastas, y control estricto del origen animal o vegetal. Ideal para seco, sellado y liviano.' ) ); ?></p>
			</div>
			<div class="srv-panel">
				<h3 class="srv-panel-title"><?php echo esc_html( grenvios_tf( 'al_terrestre_title', 'Terrestre' ) ); ?></h3>
				<p><?php echo esc_html( grenvios_tf( 'al_terrestre_text', 'Solo llega a países de la región y tarda más, pero admite bastante más: ají en pasta, conservas y envases con líquido sellados de fábrica. Para una caja grande de productos peruanos suele salir mucho más a cuenta.' ) ); ?></p>
			</div>
		</div>
		<p class="gr-nota"><?php echo esc_html( grenvios_tf( 'al_nota_via', 'Si tu destino admite las dos vías, te decimos con cuál llega tu producto concreto antes de que pagues.' ) ); ?></p>
	</div></section>

	<section class="srv-section padding"><div class="container">
		<div class="srv-head text-center">
			<h2><?php echo esc_html( grenvios_tf( 'al_emb_title', 'Cómo embalarlo para que llegue entero' ) ); ?></h2>
		</div>
		<?php
		grenvios_seo_extra_lista( array(
			'Deja los productos en su envase original: la etiqueta del fabricante es lo que la aduana lee.',
			'Comprueba que ninguna fecha de vencimiento caiga dentro del plazo de tránsito.',
			'Refuerza con film los envases que puedan abrirse en el traslado.',
			'Separa el panetón de productos pesados: llega aplastado con facilidad.',
			'Adjunta la boleta de compra y una lista del contenido con su valor.',
		), 'al_lista3' );
		?>
		<p class="gr-nota"><?php echo esc_html( grenvios_tf( 'al_nota_camp', 'En campaña navideña conviene despachar con margen: el volumen se multiplica y la aduana revisa más. Un envío de panetón puesto en camino a finales de noviembre llega tranquilo; el mismo envío a mediados de diciembre, ya no siempre.' ) ); ?></p>
	</div></section>
	<?php
}

/* ══════════════════════════════════════
   4) SEGURO DE ENVÍOS
   Página de confianza: atrae poco tráfico pero se lee justo antes de decidir.
══════════════════════════════════════ */
function grenvios_render_seguro() {
	grenvios_tool_banner( 'Si se pierde o se daña', 'Seguro para envíos internacionales' );
	?>
	<section class="srv-intro padding-top"><div class="container">
		<div class="srv-lead text-center">
			<h2><?php echo esc_html( grenvios_tf( 'sg_h2', 'Todo envío viaja con una cobertura básica; el valor declarado decide el resto' ) ); ?></h2>
			<p><?php echo esc_html( grenvios_tf( 'sg_intro', 'La pregunta llega siempre y merece una respuesta clara, no letra pequeña. Cada envío que despachamos tiene una cobertura mínima por extravío. Esa cobertura se calcula sobre el valor que declaraste, así que un paquete declarado por menos de lo que vale está, de hecho, asegurado por menos de lo que vale. Si dentro va algo importante, conviene declarar el valor real y contratar cobertura ampliada.' ) ); ?></p>
		</div>
	</div></section>

	<section class="srv-section padding"><div class="container">
		<div class="srv-two-grid">
			<div class="srv-panel">
				<h3 class="srv-panel-title"><?php echo esc_html( grenvios_tf( 'sg_cubre_title', 'Qué cubre' ) ); ?></h3>
				<?php
				grenvios_seo_extra_lista( array(
					'Extravío total del envío durante el transporte.',
					'Daño atribuible a la manipulación, con el embalaje en regla.',
					'Sustracción de contenido acreditada en destino.',
				), 'sg_lista1' );
				?>
			</div>
			<div class="srv-panel">
				<h3 class="srv-panel-title"><?php echo esc_html( grenvios_tf( 'sg_nocubre_title', 'Qué no cubre' ) ); ?></h3>
				<?php
				grenvios_seo_extra_lista( array(
					'Retención, decomiso o destrucción decididos por la aduana.',
					'Daño por embalaje insuficiente o por enviar algo no permitido.',
					'Deterioro propio del producto, como un alimento que vence en tránsito.',
					'Lucro cesante o daños indirectos: se cubre el bien, no lo que dejaste de ganar.',
				), 'sg_lista2' );
				?>
			</div>
		</div>
	</div></section>

	<section class="srv-section bg-grey padding"><div class="container">
		<div class="srv-head text-center">
			<h2><?php echo esc_html( grenvios_tf( 'sg_pasos_title', 'Si algo salió mal, esto es lo que hay que hacer' ) ); ?></h2>
			<p><?php echo esc_html( grenvios_tf( 'sg_pasos_text', 'Los plazos de reclamo son cortos y empiezan a correr desde la entrega o desde la fecha estimada. Avísanos cuanto antes, aunque todavía no tengas todos los papeles.' ) ); ?></p>
		</div>
		<?php
		grenvios_seo_extra_lista( array(
			'Escríbenos con tu número de guía y una descripción de lo ocurrido.',
			'Si el paquete llegó dañado, fotografía la caja ANTES de abrirla y también el contenido.',
			'No deseches el embalaje: es la prueba principal de cómo viajó.',
			'Reúne la boleta de compra y el detalle del valor declarado.',
			'Abrimos el reclamo con el operador y te acompañamos hasta la resolución.',
		), 'sg_lista3' );
		?>
		<p class="gr-nota"><?php echo esc_html( grenvios_tf( 'sg_nota', 'Un consejo que ahorra disgustos: fotografía el contenido y la caja cerrada antes de entregarnos el paquete. Son treinta segundos y resuelven la mitad de los reclamos.' ) ); ?></p>
	</div></section>
	<?php
}

/* ══════════════════════════════════════
   5) ENVÍOS DESDE PROVINCIAS
   SEO local fuera de {{origen_ciudad}}. Complementa recojo-a-domicilio-lima sin repetirla:
   allí el tema es el recojo en la ciudad; aquí, cómo llegar a {{origen_ciudad}} desde fuera.
══════════════════════════════════════ */
function grenvios_provincias_ciudades() {
	return array( 'Arequipa', 'Trujillo', 'Cusco', 'Piura', 'Chiclayo', 'Huancayo', 'Iquitos', 'Tacna' );
}

function grenvios_render_provincias() {
	grenvios_tool_banner( 'Todo {{origen_pais}}', 'Envíos internacionales desde provincias' );
	?>
	<section class="srv-intro padding-top"><div class="container">
		<div class="srv-lead text-center">
			<h2><?php echo esc_html( grenvios_tf( 'pv_h2', 'No hace falta que viajes a {{origen_ciudad}} para enviar al extranjero' ) ); ?></h2>
			<p><?php echo esc_html( grenvios_tf( 'pv_intro', 'Todo envío internacional desde {{origen_pais}} sale por Lima: ahí están la aduana de salida y los vuelos de carga. Eso no significa que tengas que venir. Nos haces llegar el paquete por transporte nacional o agencia de bus, y desde nuestro almacén en {{origen_ciudad}} sigue el mismo circuito y las mismas tarifas que cualquier envío limeño.' ) ); ?></p>
		</div>
	</div></section>

	<?php /* Las ciudades de origen las pinta inc/cobertura-origen.php más abajo, con
	 * más ciudades y el detalle del tramo interno: aquí salía una segunda lista. */ ?>

	<section class="srv-section bg-grey padding"><div class="container">
		<div class="srv-head text-center">
			<h2><?php echo esc_html( grenvios_tf( 'pv_pasos_title', 'Cómo funciona' ) ); ?></h2>
		</div>
		<?php
		grenvios_seo_extra_lista( array(
			'Cotizas por WhatsApp indicando desde qué ciudad envías y a qué país va.',
			'Te damos la dirección de nuestro almacén en {{origen_ciudad}} y los datos de remitente.',
			'Despachas el paquete por la agencia de transporte o de bus que prefieras.',
			'Al recibirlo lo revisamos, lo pesamos y te confirmamos el costo final.',
			'Lo despachamos al extranjero y sigues el envío con tu número de guía.',
		), 'pv_lista2' );
		?>
		<p class="gr-nota"><?php echo esc_html( grenvios_tf( 'pv_nota_plazo', 'Suma al plazo internacional el tiempo del tramo nacional hasta {{origen_ciudad}}: normalmente entre uno y tres días según la ciudad. Si tienes fecha límite, dínoslo al cotizar y calculamos hacia atrás desde ella.' ) ); ?></p>
	</div></section>

	<section class="srv-section padding"><div class="container">
		<div class="srv-head text-center">
			<h2><?php echo esc_html( grenvios_tf( 'pv_tips_title', 'Para que el tramo a {{origen_ciudad}} no te dé problemas' ) ); ?></h2>
		</div>
		<?php
		grenvios_seo_extra_lista( array(
			'Embala pensando en dos viajes, no en uno: el paquete hará el trayecto nacional y después el internacional.',
			'Rotula la caja con tu nombre y tu número de WhatsApp, no solo con el nuestro.',
			'Envíanos la guía del transporte nacional en cuanto la tengas, para estar pendientes.',
			'Mete la boleta de compra dentro de un sobre pegado a la caja: la necesitaremos para la declaración.',
			'Si envías alimentos, revisa antes que la vía de tu destino los admita.',
		), 'pv_lista3' );
		?>
	</div></section>
	<?php
}

/* ══════════════════════════════════════
   6) ENLAZADO INTERNO
   Aquí es donde estas páginas se ganan la vida. Como no están en el menú,
   dependen por completo de recibir enlaces desde las páginas que ya posicionan:
   sin esto serían huérfanas y Google apenas las rastrearía.
══════════════════════════════════════ */
add_filter( 'grenvios_related_map', function ( $mapa, $slug ) {
	// a) Bloque de relacionados DE las páginas nuevas (hacia dentro del sitio).
	$mapa['aduanas-e-impuestos'] = array(
		'Antes de enviar' => array( 'que-se-puede-enviar', 'servicios/peso-volumetrico', 'tiempos-de-entrega' ),
		'Destinos'        => array( 'destinos' ),
		'Siguiente paso'  => array( 'cotizar', 'preguntas-frecuentes' ),
	);
	$mapa['envio-de-alimentos'] = array(
		'Te puede interesar' => array( 'que-se-puede-enviar', 'aduanas-e-impuestos', 'servicios/envio-internacional-de-paquetes' ),
		'Destinos'           => array( 'destinos' ),
		'Siguiente paso'     => array( 'cotizar' ),
	);
	$mapa['seguro-de-envios'] = array(
		'Te puede interesar' => array( 'aduanas-e-impuestos', 'rastreo-de-envios', 'que-se-puede-enviar' ),
		'Siguiente paso'     => array( 'cotizar', 'contacto' ),
	);
	$mapa['envios-desde-provincias'] = array(
		'Servicios'      => array( 'servicios/envio-internacional-de-paquetes', 'servicios/envio-internacional-de-documentos', 'servicios/envio-de-equipaje' ),
		'Lo que necesitas' => array( 'servicios/peso-volumetrico', 'tiempos-de-entrega' ),
		'Siguiente paso' => array( 'cotizar', 'contacto' ),
	);

	// b) Enlaces HACIA las páginas nuevas desde las que ya tienen autoridad.
	//    Sin este bloque quedarían huérfanas: es la mitad que se suele olvidar.
	$entrantes = array(
		'que-se-puede-enviar'  => array( 'Te puede interesar' => array( 'aduanas-e-impuestos', 'servicios/envio-de-alimentos' ) ),
		'cotizar'              => array( 'También te sirve'   => array( 'aduanas-e-impuestos', 'seguro-de-envios' ) ),
		'tiempos-de-entrega'   => array( 'Antes de enviar'    => array( 'aduanas-e-impuestos' ) ),
		'rastreo-de-envios'    => array( 'Te puede interesar' => array( 'seguro-de-envios' ) ),
		'preguntas-frecuentes' => array( 'Te puede interesar' => array( 'aduanas-e-impuestos', 'seguro-de-envios' ) ),
		'recojo-a-domicilio-lima' => array( 'Te puede interesar' => array( 'envios-desde-provincias' ) ),
		'destinos'             => array( 'Antes de enviar'    => array( 'aduanas-e-impuestos' ) ),
	);
	foreach ( $entrantes as $origen => $bloques ) {
		if ( ! isset( $mapa[ $origen ] ) ) continue;   // respeta lo que ya haya
		foreach ( $bloques as $titulo => $destinos ) {
			$actual = isset( $mapa[ $origen ][ $titulo ] ) ? $mapa[ $origen ][ $titulo ] : array();
			$mapa[ $origen ][ $titulo ] = array_values( array_unique( array_merge( $actual, $destinos ) ) );
		}
	}
	return $mapa;
}, 10, 2 );

/* ══════════════════════════════════════
   7) TEXTOS PARA EL TRADUCTOR
   Los títulos de bloque y las listas se declaran aquí para que la pantalla
   «Traducciones» los resuelva de una vez, sin esperar a que alguien visite
   cada página en cada idioma.
══════════════════════════════════════ */
add_filter( 'grenvios_i18n_extra_strings', function ( $textos ) {
	foreach ( array(
		'Antes de enviar', 'Destinos', 'Siguiente paso', 'Te puede interesar',
		'Servicios', 'Lo que necesitas', 'También te sirve',
		'Aduanas e Impuestos', 'Envío de Alimentos', 'Seguro de Envíos', 'Envíos desde Provincias',
	) as $t ) $textos[] = $t;
	foreach ( grenvios_provincias_ciudades() as $c ) $textos[] = $c;
	return $textos;
} );
