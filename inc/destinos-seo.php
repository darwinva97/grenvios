<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Fichas de destino: quitar lo que se repite y enlazar lo que faltaba
 * ══════════════════════════════════════════════════════════════════════════
 *
 * AUDITORÍA DE /destinos/chile/ (2.848 palabras, 41 enlaces internos, schema
 * con Service, Country, FAQPage y BreadcrumbList: la base estaba bien). Lo que
 * fallaba era por acumulación, no por falta:
 *
 *   · DOS bloques de preguntas en la misma página. El propio del destino
 *     —«Preguntas frecuentes sobre Chile», cinco preguntas con los datos
 *     reales de la ruta— y, al cerrar, el genérico del tema «Resolvemos tus
 *     dudas». De sus cuatro preguntas, DOS eran la misma que ya estaba arriba:
 *     «¿Cuánto demora un envío a Chile?» aparecía dos veces palabra por
 *     palabra, y «¿Cómo lo recibe mi destinatario?» / «¿Cómo recibo mi envío?»
 *     son la misma pregunta escrita distinto. Ocho preguntas en el FAQPage, de
 *     las que dos eran ruido.
 *   · DOS llamadas a la acción de cierre, una detrás de otra: «¿Listo para
 *     enviar a Chile?» (del destino) y «¿Listo para enviar?» (genérica).
 *   · Las guías enlazadas incluían «Hello world!», la entrada de ejemplo de
 *     WordPress, duplicada a las nueve rutas. Diez URLs de relleno enlazadas
 *     desde las páginas más importantes del sitio.
 *
 * Aquí no se añade texto: se quita repetición y se sustituyen las preguntas
 * duplicadas por otras que el visitante sí se hace y que la página todavía no
 * respondía —recojo, aduana retenida, seguro, qué se puede enviar—.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Slug de la ficha de destino que se está viendo, o '' si no es una. */
function grenvios_dseo_slug() {
	if ( is_admin() || ! function_exists( 'grenvios_current_slug' ) || ! function_exists( 'grenvios_destinos' ) ) return '';
	$slug = grenvios_current_slug();
	$dest = grenvios_destinos();
	return ( $slug !== '' && isset( $dest[ $slug ] ) ) ? $slug : '';
}

/* ── 1) Una sola llamada a la acción de cierre ─────────────────────────── */
add_filter( 'grenvios_cta_enabled', function ( $on, $slug ) {
	if ( ! function_exists( 'grenvios_destinos' ) ) return $on;
	$dest = grenvios_destinos();
	// La ficha ya cierra con «¿Listo para enviar a X?», con su país y su enlace.
	return isset( $dest[ $slug ] ) ? false : $on;
}, 10, 2 );

/* ── 2) El bloque final de preguntas COMPLEMENTA, no repite ────────────── */
add_filter( 'grenvios_page_faqs', function ( $faqs, $slug ) {
	$d = grenvios_dseo_slug();
	if ( $d === '' || $d !== $slug ) return $faqs;

	$dest = grenvios_destinos();
	$p    = $dest[ $slug ]['title'];

	/* Las cinco de arriba ya cubren plazo, forma de entrega, pagos al recibir,
	 * aéreo frente a terrestre y seguimiento. Estas cuatro son las que quedaban
	 * sin responder en la página. */
	return array(
		array(
			'¿Qué puedo enviar a ' . $p . '?',
			'Documentos, ropa, regalos, repuestos, compras y carga comercial. Lo que cambia de un país a otro son las restricciones de su aduana —líquidos, alimentos, medicinas o artículos con batería según la vía—, y las revisamos contigo antes de despachar para que nada se quede retenido.',
		),
		array(
			'¿Recogen mi paquete en {{origen_ciudad}} para enviarlo a ' . $p . '?',
			'Sí. Coordinamos el recojo en tu casa, tu oficina o donde esté tu proveedor dentro de {{origen_ciudad}}. Lo pesamos, lo medimos, lo embalamos y lo despachamos hacia ' . $p . '. El recojo no altera el plazo de tránsito.',
		),
		array(
			'¿Qué pasa si la aduana de ' . $p . ' retiene mi envío?',
			'Nos ocupamos nosotros: te avisamos de qué falta —normalmente un dato de la factura o del contenido declarado— y gestionamos el trámite. Por eso revisamos la documentación antes de que el envío salga de {{origen_ciudad}}: la mayoría de las retenciones se evitan en origen, no se resuelven en destino.',
		),
		array(
			'¿Cómo cotizo mi envío a ' . $p . '?',
			'Necesitamos tres datos: qué envías, cuánto pesa y mide el bulto, y a qué ciudad de ' . $p . ' va. Con eso calculamos el peso real y el volumétrico, cotizamos por el mayor de los dos y te damos precio cerrado en minutos.',
		),
	);
}, 30, 2 );

/* ── 3) La entrada de ejemplo de WordPress ─────────────────────────────
 * «Hello world!» estaba publicada y duplicada a las nueve rutas, y las fichas
 * de destino la enlazaban como si fuera una guía. No se filtra al pintar: se
 * despublica, que es donde estaba el problema (ver el informe del cambio). */
