<?php
/**
 * Grenvíos — Páginas de contenido de alto tráfico.
 *
 * Cuatro páginas más, cada una con una intención de búsqueda propia que hoy no
 * cubre ninguna otra página del sitio (nada de contenido repetido con otro
 * título, que es lo que Google descarta):
 *
 *   /tiempos-de-entrega/                    comparativa de plazos entre países
 *   /como-enviar-un-paquete-al-extranjero/  el proceso paso a paso
 *   /recojo-a-domicilio-lima/               SEO local: recojo en {{origen_ciudad}}
 *   /servicios/envio-de-compras/            compras hechas en {{origen_ciudad}}, enviadas a tu país
 *
 * Las dos primeras se alimentan de `grenvios_destinos()`, la misma fuente que
 * las páginas de país: son vistas distintas de los mismos datos, no textos
 * duplicados. Si la clienta agrega un país, aparece solo en las tres.
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ══════════════════════════════════════
   1) REGISTRO
══════════════════════════════════════ */
add_filter( 'grenvios_pages', function ( $pages ) {
	$nuevas = array(
		'tiempos-de-entrega' => array(
			'title' => 'Tiempos de Entrega',
			'seo'   => 'Cuánto Demora un Envío Internacional desde {{origen_pais}} | Grenvíos',
			'desc'  => 'Tiempos de entrega de envíos internacionales desde {{origen_pais}} país por país, por vía aérea y terrestre. Compara plazos antes de elegir tu modalidad.',
			'parent'=> '',
		),
		'como-enviar-un-paquete-al-extranjero' => array(
			'title' => 'Cómo Enviar un Paquete',
			'seo'   => 'Cómo Enviar un Paquete al Extranjero desde {{origen_pais}} | Grenvíos',
			'desc'  => 'Guía paso a paso para enviar un paquete al extranjero desde {{origen_pais}}: qué presentar, cómo embalar, cómo se cobra y cuánto demora. Sin sorpresas en aduana.',
			'parent'=> '',
		),
		'recojo-a-domicilio-lima' => array(
			'title' => 'Recojo a Domicilio en {{origen_ciudad}}',
			'seo'   => 'Recojo a Domicilio en {{origen_ciudad}} para tu Envío | Grenvíos',
			'desc'  => 'Recogemos tu paquete en casa, oficina o proveedor dentro de {{origen_ciudad}} y lo despachamos al extranjero. Coordina tu recojo por WhatsApp.',
			'parent'=> '',
		),
		'envio-de-compras' => array(
			'title' => 'Envío de Compras',
			'seo'   => 'Envío de Compras desde {{origen_ciudad}} a tu País | Grenvíos',
			'desc'  => 'Compra en tiendas peruanas, recibe en nuestra dirección de {{origen_ciudad}} y nosotros lo enviamos a tu país. Consolidamos varios paquetes en un solo envío.',
			'parent'=> 'servicios',
		),
	);
	$out = array();
	foreach ( $pages as $slug => $def ) {
		if ( $slug === 'cotizar' ) foreach ( $nuevas as $k => $v ) $out[ $k ] = $v;
		$out[ $slug ] = $def;
	}
	foreach ( $nuevas as $k => $v ) if ( ! isset( $out[ $k ] ) ) $out[ $k ] = $v;
	return $out;
} );

function grenvios_content_slugs() {
	return array( 'tiempos-de-entrega', 'como-enviar-un-paquete-al-extranjero', 'recojo-a-domicilio-lima', 'envio-de-compras' );
}

add_filter( 'grenvios_render_page_early', function ( $hecho, $slug ) {
	if ( $hecho || ! in_array( $slug, grenvios_content_slugs(), true ) ) return $hecho;
	switch ( $slug ) {
		case 'tiempos-de-entrega':                   grenvios_render_tiempos();  return true;
		case 'como-enviar-un-paquete-al-extranjero': grenvios_render_como();     return true;
		case 'recojo-a-domicilio-lima':              grenvios_render_recojo();   return true;
		case 'envio-de-compras':                     grenvios_render_compras();  return true;
	}
	return $hecho;
}, 12, 2 );

/* ══════════════════════════════════════
   2) TIEMPOS DE ENTREGA
   Vista comparativa: lo que una página de país no puede dar, porque solo habla
   de su país. Aquí el valor está en comparar.
══════════════════════════════════════ */
function grenvios_render_tiempos() {
	grenvios_tool_banner( 'Plazos reales', 'Tiempos de entrega de envíos internacionales, destino por destino' );
	$dest = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();
	?>
	<section class="srv-intro padding-top"><div class="container">
		<div class="srv-lead text-center">
			<h2><?php echo esc_html( grenvios_tf( 'te_h2', 'Los plazos dependen del destino y de la vía, no solo de la distancia' ) ); ?></h2>
			<p><?php echo esc_html( grenvios_tf( 'te_intro', 'Un envío aéreo a un país vecino puede llegar antes que uno terrestre a la ciudad de al lado, y la aduana puede sumar días que no dependen del transporte. Estos son los tiempos con los que trabajamos, contados en días hábiles desde el despacho en {{origen_ciudad}}.' ) ); ?></p>
		</div>
	</div></section>

	<section class="srv-section padding"><div class="container">
		<?php grenvios_render_tabla_destinos(); ?>
		<p class="gr-nota"><?php echo esc_html( grenvios_tf( 'te_nota', 'Los plazos son estimados en días hábiles y no incluyen el tiempo que la aduana del país de destino pueda retener un envío para revisión. Si tu envío es urgente, dínoslo al cotizar: hay rutas más rápidas según el destino.' ) ); ?></p>
	</div></section>

	<section class="srv-section bg-grey padding"><div class="container">
		<div class="srv-two-grid">
			<div class="srv-panel">
				<h3 class="srv-panel-title"><?php echo esc_html( grenvios_tf( 'te_aereo_title', 'Vía aérea' ) ); ?></h3>
				<p><?php echo esc_html( grenvios_tf( 'te_aereo_text', 'Es la opción rápida y la única disponible para destinos fuera de Sudamérica. Tiene más restricciones de contenido: nada de líquidos, alimentos ni objetos con batería.' ) ); ?></p>
			</div>
			<div class="srv-panel">
				<h3 class="srv-panel-title"><?php echo esc_html( grenvios_tf( 'te_terr_title', 'Vía terrestre' ) ); ?></h3>
				<p><?php echo esc_html( grenvios_tf( 'te_terr_text', 'Más económica y con menos restricciones —admite líquidos, alimentos sellados y objetos con batería—, pero solo llega a países de la región y tarda más.' ) ); ?></p>
			</div>
		</div>
	</div></section>
	<?php
}

/* ══════════════════════════════════════
   3) CÓMO ENVIAR UN PAQUETE (proceso)
══════════════════════════════════════ */
function grenvios_pasos_envio() {
	return array(
		array( 'Reúne lo que vas a enviar y su comprobante', 'Necesitas la boleta o factura de la mercancía. Sin ese documento la aduana no puede valorar el envío y puede retenerlo.' ),
		array( 'Comprueba que se pueda enviar', 'Cada vía y cada país admiten cosas distintas. Revisa la lista de permitidos y prohibidos antes de cerrar la caja.' ),
		array( 'Embala y mide', 'Usa una caja del tamaño justo: el espacio sobrante se paga. Mide alto, largo y ancho en centímetros con la caja ya cerrada, y pésala.' ),
		array( 'Cotiza', 'Con el peso, las medidas y el destino te damos el precio. Se cobra el mayor entre el peso real y el volumétrico.' ),
		array( 'Entrégalo o pide recojo', 'Puedes traerlo a nuestra oficina en el Cercado de Lima o coordinar que lo recojamos en tu domicilio.' ),
		array( 'Paga y despachamos', 'Todos los envíos se cancelan en {{origen_ciudad}} antes de salir. En destino el receptor solo retira, sin pagar el flete.' ),
		array( 'Sigue tu envío', 'Te confirmamos el estado hasta la entrega: en tránsito, en aduana o entregado.' ),
	);
}

function grenvios_render_como() {
	grenvios_tool_banner( 'Paso a paso', 'Cómo enviar un paquete al extranjero' );
	?>
	<section class="srv-intro padding-top"><div class="container">
		<div class="srv-lead text-center">
			<h2><?php echo esc_html( grenvios_tf( 'ce_h2', 'Siete pasos, de la caja a la entrega' ) ); ?></h2>
			<p><?php echo esc_html( grenvios_tf( 'ce_intro', 'Enviar al extranjero desde {{origen_pais}} no tiene misterio, pero sí un orden. Si lo sigues, tu envío no se queda en aduana ni te cuesta más de lo previsto.' ) ); ?></p>
		</div>
	</div></section>

	<section class="srv-section padding"><div class="container">
		<ol class="gr-pasos">
		<?php foreach ( grenvios_pasos_envio() as $i => $p ) : ?>
			<li>
				<span class="gr-paso-num"><?php echo (int) ( $i + 1 ); ?></span>
				<div>
					<h3><?php echo esc_html( grenvios_t( $p[0] ) ); ?></h3>
					<p><?php echo esc_html( grenvios_t( $p[1] ) ); ?></p>
				</div>
			</li>
		<?php endforeach; ?>
		</ol>
	</div></section>

	<section class="srv-section bg-grey padding"><div class="container">
		<div class="srv-head text-center">
			<h2><?php echo esc_html( grenvios_tf( 'ce_links_title', 'Lo que necesitas saber antes de empezar' ) ); ?></h2>
		</div>
		<div class="row">
			<?php
			foreach ( grenvios_como_cards() as $c ) :
				$url = grenvios_tool_url( $c[0] ); ?>
				<div class="col-lg-4 col-md-6"><div class="promo-item">
					<div class="promo-content">
						<h3><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( grenvios_t( $c[1] ) ); ?></a></h3>
						<p><?php echo esc_html( grenvios_t( $c[2] ) ); ?></p>
					</div>
				</div></div>
			<?php endforeach; ?>
		</div>
	</div></section>
	<?php
}

/* Listas de texto en funciones: así el traductor puede recogerlas de una vez,
 * sin depender de que alguien visite cada página en cada idioma. */
function grenvios_recojo_pasos() {
	return array(
		'Escríbenos por WhatsApp con tu dirección y el distrito',
		'Dinos qué envías, el peso aproximado y las medidas de la caja',
		'Te confirmamos el costo del recojo según el distrito',
		'Acordamos día y franja horaria',
		'Recogemos, pesamos y te enviamos la cotización final',
	);
}

function grenvios_compras_pasos() {
	return array(
		'Nos avisas qué vas a comprar y en qué tienda',
		'Pones nuestra dirección de {{origen_ciudad}} como destino de tu compra',
		'Recibimos el paquete y te confirmamos que llegó',
		'Consolidamos: si son varias compras, van en un solo envío',
		'Cotizamos por peso y volumen, pagas y despachamos',
	);
}

function grenvios_compras_avisos() {
	return array(
		'Guarda siempre la boleta o factura: la aduana la exige',
		'Consolidar varias compras casi siempre sale más barato',
		'Los productos con batería o líquidos no viajan por vía aérea',
		'Cada país tiene un límite de valor libre de impuestos',
		'El envío se paga en {{origen_ciudad}}; en destino solo se retira',
	);
}

function grenvios_como_cards() {
	return array(
		array( 'peso-volumetrico',    'Cómo se calcula el precio', 'Peso real o volumétrico: el mayor de los dos manda. Calcúlalo antes de cotizar.' ),
		array( 'que-se-puede-enviar', 'Qué se puede enviar',       'Permitidos y prohibidos por vía aérea y terrestre, y por país de destino.' ),
		array( 'tiempos-de-entrega',  'Cuánto va a demorar',       'Plazos país por país, según la modalidad que elijas.' ),
	);
}

/* ══════════════════════════════════════
   4) RECOJO A DOMICILIO (SEO local)
   Esta página busca a quien está en {{origen_ciudad}} y no quiere ir a la oficina. Por eso
   lleva los datos del negocio: dirección, horario y teléfono reales.
══════════════════════════════════════ */
function grenvios_render_recojo() {
	$b = function_exists( 'grenvios_biz' ) ? grenvios_biz() : array();
	grenvios_tool_banner( 'Lima y alrededores', 'Recojo de envíos a domicilio en {{origen_ciudad}}' );
	?>
	<section class="srv-intro padding-top"><div class="container">
		<div class="srv-lead text-center">
			<h2><?php echo esc_html( grenvios_tf( 'rd_h2', 'No hace falta que vengas: lo recogemos donde estés' ) ); ?></h2>
			<p><?php echo esc_html( grenvios_tf( 'rd_intro', 'Recogemos tu paquete en casa, en tu oficina o directamente donde tu proveedor, dentro de {{origen_ciudad}}. Lo pesamos, lo medimos y lo despachamos al extranjero. Es la forma más cómoda de enviar si no puedes acercarte al Cercado.' ) ); ?></p>
		</div>
	</div></section>

	<section class="srv-section padding"><div class="container"><div class="srv-two-grid">
		<div class="srv-panel">
			<h3 class="srv-panel-title"><?php echo esc_html( grenvios_tf( 'rd_como_title', 'Cómo se coordina' ) ); ?></h3>
			<ul class="check-list">
				<?php foreach ( grenvios_recojo_pasos() as $i ) : ?>
					<li><i class="fa-solid fa-check"></i><?php echo esc_html( grenvios_t( $i ) ); ?></li>
				<?php endforeach; ?>
			</ul>
			<p><?php echo esc_html( grenvios_tf( 'rd_costo', 'El recojo tiene un costo que depende del distrito y se suma al del envío. Para clientes con envíos frecuentes fijamos días de recojo y una tarifa cerrada.' ) ); ?></p>
		</div>
		<div class="srv-panel">
			<h3 class="srv-panel-title"><?php echo esc_html( grenvios_tf( 'rd_of_title', 'O acércate a la oficina' ) ); ?></h3>
			<?php if ( ! empty( $b['address'] ) ) : ?>
				<p><strong><?php echo esc_html( grenvios_t( 'Dirección' ) ); ?>:</strong> <?php echo esc_html( $b['address'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $b['hours'] ) ) : ?>
				<p><strong><?php echo esc_html( grenvios_t( 'Horario' ) ); ?>:</strong> <?php echo esc_html( $b['hours'] ); ?></p>
			<?php endif; ?>
			<?php if ( ! empty( $b['phone'] ) ) : ?>
				<p><strong><?php echo esc_html( grenvios_t( 'Teléfono' ) ); ?>:</strong> <a href="tel:<?php echo esc_attr( isset( $b['phone_tel'] ) ? $b['phone_tel'] : $b['phone'] ); ?>"><?php echo esc_html( $b['phone'] ); ?></a></p>
			<?php endif; ?>
			<p><?php echo esc_html( grenvios_tf( 'rd_prov', 'Si envías desde provincia, mándalo por una agencia de transporte local a nuestra sede en {{origen_ciudad}} y desde aquí lo despachamos a su destino internacional.' ) ); ?></p>
			<a class="default-btn" href="<?php echo esc_url( grenvios_tool_url( 'cotizar' ) ); ?>"><?php echo esc_html( grenvios_t( 'Cotizar mi envío' ) ); ?></a>
		</div>
	</div></div></section>
	<?php
}

/* ══════════════════════════════════════
   5) ENVÍO DE COMPRAS
══════════════════════════════════════ */
function grenvios_render_compras() {
	grenvios_tool_banner( 'Compras en {{origen_pais}}', 'Envío de compras al extranjero desde {{origen_ciudad}}' );
	?>
	<section class="srv-intro padding-top"><div class="container">
		<div class="srv-lead text-center">
			<h2><?php echo esc_html( grenvios_tf( 'ec_h2', 'Compra en {{origen_pais}} aunque no estés aquí' ) ); ?></h2>
			<p><?php echo esc_html( grenvios_tf( 'ec_intro', 'Muchas tiendas peruanas no envían al extranjero. Compras, pones nuestra dirección de {{origen_ciudad}} como destino, y cuando llega lo despachamos a tu país. Si compras en varias tiendas, juntamos todo en un solo envío y pagas un solo flete.' ) ); ?></p>
		</div>
	</div></section>

	<section class="srv-section padding"><div class="container"><div class="srv-two-grid">
		<div class="srv-panel">
			<h3 class="srv-panel-title"><?php echo esc_html( grenvios_tf( 'ec_como_title', 'Cómo funciona' ) ); ?></h3>
			<ul class="check-list">
				<?php foreach ( grenvios_compras_pasos() as $i ) : ?>
					<li><i class="fa-solid fa-check"></i><?php echo esc_html( grenvios_t( $i ) ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="srv-panel">
			<h3 class="srv-panel-title"><?php echo esc_html( grenvios_tf( 'ec_ojo_title', 'Lo que conviene saber' ) ); ?></h3>
			<ul class="check-list">
				<?php foreach ( grenvios_compras_avisos() as $i ) : ?>
					<li><i class="fa-solid fa-circle-info"></i><?php echo esc_html( grenvios_t( $i ) ); ?></li>
				<?php endforeach; ?>
			</ul>
			<a class="default-btn" href="<?php echo esc_url( grenvios_tool_url( 'que-se-puede-enviar' ) ); ?>"><?php echo esc_html( grenvios_t( 'Ver qué se puede enviar' ) ); ?></a>
		</div>
	</div></div></section>
	<?php
}

/* ══════════════════════════════════════
   6) ESTILOS
══════════════════════════════════════ */
add_action( 'wp_head', function () {
	if ( is_admin() ) return;
	echo '<style id="grenvios-contenido-css">'
		. '.gr-pasos{list-style:none;padding:0;margin:0;max-width:820px}'
		. '.gr-pasos li{display:flex;gap:18px;margin-bottom:26px;align-items:flex-start}'
		. '.gr-paso-num{flex:0 0 40px;width:40px;height:40px;border-radius:50%;display:flex;align-items:center;'
		. 'justify-content:center;font-weight:700;background:rgba(0,0,0,.07)}'
		. '.gr-pasos h3{font-size:18px;margin:6px 0 6px}'
		. '.gr-pasos p{margin:0;opacity:.85}'
		. '.gr-nota{margin-top:18px;font-size:14px;opacity:.75;max-width:80ch}'
		. '</style>';
}, 105 );

/* Textos de este módulo para el traductor (ver el mismo bloque en
 * inc/paginas-herramientas.php). */
add_filter( 'grenvios_i18n_extra_strings', function ( $textos ) {
	foreach ( grenvios_pasos_envio() as $p ) { $textos[] = $p[0]; $textos[] = $p[1]; }
	foreach ( grenvios_recojo_pasos() as $t )   $textos[] = $t;
	foreach ( grenvios_compras_pasos() as $t )  $textos[] = $t;
	foreach ( grenvios_compras_avisos() as $t ) $textos[] = $t;
	foreach ( grenvios_como_cards() as $c ) { $textos[] = $c[1]; $textos[] = $c[2]; }

	foreach ( array(
		'Dirección', 'Horario', 'Teléfono', 'Cotizar mi envío', 'Ver qué se puede enviar',
	) as $t ) $textos[] = $t;

	return $textos;
} );
