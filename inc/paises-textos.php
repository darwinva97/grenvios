<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  El CUERPO de cada página habla del país de su ruta
 * ══════════════════════════════════════════════════════════════════════════
 *
 * inc/paises-cabecera.php ya reescribe el H1 y la antesala de cada copia. Pero
 * de ahí para abajo, /ar/servicios-de-envio-a-argentina/ seguía leyéndose como
 * la página de Perú: «Enviamos tus documentos, paquetes y carga al mundo»,
 * «Enviamos a más de 30 países», «¿Listo para enviar?». Una página que promete
 * Argentina en el título y luego no la vuelve a nombrar es la definición de
 * contenido duplicado: trece rutas repitiendo el mismo cuerpo palabra por
 * palabra, compitiendo entre ellas por las mismas búsquedas.
 *
 * CRITERIO DE REDACCIÓN (el mismo en todas las páginas)
 *
 *   · El país aparece donde el lector lo busca —H2, entradilla y llamadas a la
 *     acción—, no repetido en cada frase: eso es relleno y se nota al leer.
 *   · Siempre en la dirección real del negocio: DESDE {{origen_ciudad}} HACIA el
 *     país. Los tokens de origen se resuelven después, así que una sede
 *     distinta cambia el texto sola.
 *   · Se usan las palabras con las que se busca de verdad («cuánto cuesta
 *     enviar a X», «envío de paquetes a X», «apostillar para X»), en frases
 *     que un cliente diría, no encadenadas a la fuerza.
 *   · Nada de promesas nuevas: plazos, vías y coberturas se quedan como están;
 *     esos datos salen del gestor de destinos, no de aquí.
 *
 * SI LA CLIENTA LO EDITÓ, MANDA LO SUYO: solo se reescribe el campo que sigue
 * siendo idéntico al texto que trae el tema.
 *
 * Filtro `grenvios_textos_pais`: para añadir o cambiar cualquier redacción sin
 * tocar este archivo.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Redacción por página. %s = país de destino.
 * Se omiten los campos `*_hero_*`: esos los lleva inc/paises-cabecera.php. */
function grenvios_textos_pais_mapa() {
	static $mapa = null;
	if ( $mapa !== null ) return $mapa;
	return $mapa = apply_filters( 'grenvios_textos_pais', array(

		/* ── Portada de la ruta ───────────────────────────────────────── */
		'home' => array(
			/* El H1 de la portada (oculto, para buscadores): era idéntico en las
			 * trece rutas. Es el encabezado que más pesa de la página. */
			'home_sr_h1'        => 'Courier de {{origen_pais}} a %s: paquetes, documentos y carga desde {{origen_ciudad}}',
			'home_dest_title'   => '¡Enviamos a %s <br>y a los principales <span class="hl">destinos del mundo!</span>',
			'home_qq_title'     => 'Pide tu <span class="hl">cotización</span> a %s al instante',
			'home_feat_sub'     => 'Tu ruta a %s, de principio a fin',
			'home_feat_title'   => '¡Logística pensada <br>para tu envío a <span class="hl">%s!</span>',
			'home_feat_text'    => 'Combinamos transporte aéreo y terrestre, embalaje seguro y seguimiento <br>en cada etapa del trayecto a %s.',
			'home_feat_tab2_text' => 'Coordinamos la ruta a %s y otros 30 destinos con aliados confiables en origen y en destino.',
			'home_proc_sub'     => 'Así enviamos a %s',
			'home_proc_title'   => '¿Cómo llega tu <br>paquete a <span class="hl">%s?</span>',
			'home_proc_text'    => 'Un proceso simple y transparente en cuatro pasos, <br>desde la cotización hasta la entrega en %s.',
			'home_proc1_text'   => 'Solicita tu cotización con la ciudad de %s, el peso y el tipo de envío.',
			'home_proc4_text'   => 'Recibimos la confirmación de entrega en el destino de %s.',
			'home_testi_text'   => 'La confianza de quienes ya enviaron con Grenvíos <br>a %s y a otros destinos.',
			'home_track_text'   => 'Ingresa tu número de guía y consulta al instante en qué punto del trayecto a %s está tu envío.',
			'home_serv_sub'     => 'Nuestros servicios hacia %s',
			'home_serv_title'   => '¡Soluciones de envío <br>a %s a tu <span class="hl">medida!</span>',
			'home_serv_text'    => 'Documentos, paquetes y carga desde {{origen_ciudad}} <br>hasta cualquier ciudad de %s.',
			'home_about_sub'    => 'De {{origen_ciudad}} a %s',
			'home_about_title'  => '¡Tu envío a <span class="hl">%s</span> <br>en buenas manos!',
			'home_about_text'   => 'En Grenvíos gestionamos el envío de documentos, paquetes y carga desde {{origen_ciudad}}, {{origen_pais}}, hasta %s. Coordinamos la vía aérea y la terrestre, el recojo a domicilio y la entrega puerta a puerta, con seguimiento en cada etapa del trayecto.',
			'home_dest_sub'     => 'Además de %s',
			'home_dest_text'    => 'Si tu envío no va a %s, también lo llevamos: <br>estos son otros destinos que trabajamos a diario.',
			'home_qq_sub'       => 'Cotiza tu envío a %s en 1 minuto',
			'home_qq_text'      => 'Cuéntanos lo básico de tu envío a %s y te atendemos al instante por WhatsApp.',
			'home_cta_sub'      => 'Envíos seguros a %s',
			'home_cta_title'    => '¿Listo para enviar<br>a <span class="hl">%s?</span>',
		),

		/* ── Servicios ────────────────────────────────────────────────── */
		'servicios' => array(
			'serv_services_eyebrow'  => 'Servicios de envío a %s',
			'serv_services_title'    => '¡Enviamos tus documentos, paquetes <br>y carga a <span class="hl">%s!</span>',
			'serv_services_subtitle' => 'Desde {{origen_ciudad}} despachamos a %s por vía aérea y terrestre, <br>para personas y empresas, con recojo a domicilio y seguimiento.',
			'serv_destinos_eyebrow'  => 'Cobertura en %s',
			'serv_destinos_title'    => '¡Llegamos a todas las ciudades <br>de <span class="hl">%s!</span>',   /* sin «todo/toda»: el género del país varía */
			'serv_destinos_subtitle' => 'Entregamos en las principales ciudades de %s y, si tu envío va a otro país, también lo llevamos: trabajamos más de 30 destinos entre América, Europa y Asia.',
			'serv_cta_eyebrow'       => '¿Listo para enviar a %s?',
			'serv_cta_title'         => '¡Cotiza tu envío<br>a %s <span class="hl">hoy!</span>',
		),

		/* ── Cotizar ──────────────────────────────────────────────────── */
		'cotizar' => array(
			'cot_form_heading'   => 'Cotizar un envío a %s',
			'cot_form_intro'     => 'Calcula cuánto cuesta enviar a %s según el peso, el volumen y la ciudad de entrega. Te respondemos en minutos.',
			'cot_steps_subheading' => 'Cotizar tu envío a %s es muy fácil',
			'cot_steps_title'    => 'Cotiza tu envío a %s en <span class="hl">3 simples pasos</span>',
			'cot_step1_text'     => 'Dinos a qué ciudad de %s va tu envío, qué mandas (documentos, paquete o carga) y el peso o las medidas del bulto.',
			'cot_step2_text'     => 'Calculamos el costo por peso real o volumétrico y por la vía que mejor funcione hacia %s —aérea o terrestre—, y te respondemos en minutos.',
		),

		/* ── Contacto ─────────────────────────────────────────────────── */
		'contacto' => array(
			'cont_form_text'   => 'Resolvemos tus dudas sobre envíos de {{origen_ciudad}} a %s <br>y te ayudamos a cotizar el tuyo sin compromiso.',
			'cont_info_text'   => 'Estamos para coordinar tu envío a %s <br>de forma rápida y segura.',
			'cont_cards_title' => 'Te acompañamos en cada <span class="hl">envío a %s</span>',
			'cont_cta_title'   => '¡Tu paquete a %s<br>con total <span class="hl">confianza!</span>',
		),

		/* ── Nosotros ─────────────────────────────────────────────────── */
		'nosotros' => array(
			'nos_about_title'    => '¡Envíos seguros de <br><span class="hl">{{origen_ciudad}} a %s!</span>',
			'nos_about_text'     => 'Somos una empresa peruana de envíos internacionales con sede en {{origen_ciudad}}. La ruta a %s es una de las que trabajamos a diario: combinamos transporte aéreo y terrestre, recojo a domicilio y entrega puerta a puerta, con seguimiento en cada etapa.',
			'nos_operamos_title' => 'Tu ruta a %s, y 30 destinos más',
			'nos_operamos_text'  => 'Trabajamos estas rutas de forma regular desde {{origen_ciudad}}. Entra en %s y verás los plazos reales, las modalidades disponibles y qué admite su aduana.',
			'nos_cta_title'      => '¡Tu paquete a %s<br>con total <span class="hl">confianza!</span>',
		),

		/* ── Envío de paquetes ────────────────────────────────────────── */
		'envio-internacional-de-paquetes' => array(
			'paq_intro_title'       => 'Envío de paquetes de {{origen_ciudad}} a %s',
			'paq_modalidades_title' => 'Aéreo o terrestre a %s: ¿cuál te conviene?',
			'paq_equipaje_title'    => 'Equipaje y compras de {{origen_ciudad}} a %s',
			'paq_cta_title'         => '¿Listo para enviar tu paquete a %s?',
		),

		/* ── Envío de documentos ──────────────────────────────────────── */
		'envio-internacional-de-documentos' => array(
			'doc_intro_title'   => 'Envío de documentos de {{origen_ciudad}} a %s',
			'doc_tiempos_title' => 'Tiempos de entrega a %s',
			'doc_legal_title'   => '¿Tu documento necesita validez legal en %s?',
			'doc_cta_title'     => '¿Necesitas enviar un documento a %s hoy?',
		),

		/* ── Carga internacional ──────────────────────────────────────── */
		'carga-internacional' => array(
			'carga_main_intro'      => 'En Grenvíos movemos grandes volúmenes de mercancía desde {{origen_ciudad}}, {{origen_pais}}, hasta %s. Coordinamos la vía aérea y la terrestre, el despacho y la documentación, para que tu carga salga sin retenciones y llegue cuando la necesitas.',
			'carga_solucion_title'  => 'Tu carga a %s, de principio a fin',
			'carga_cta_title'       => '¿Listo para mover tu carga a %s?',
		),

		/* ── Apostilla y traducción ───────────────────────────────────── */
		'apostilla-y-traduccion' => array(
			'apos_intro_title' => 'Apostilla y traducción para trámites en %s',
			'apos_combo_title' => 'Apostilla, traducción y envío a %s en un solo lugar',
			'apos_cta_title'   => '¿Necesitas apostillar y enviar tu documento a %s?',
		),

		/* ── Envíos para empresas ─────────────────────────────────────── */
		'envios-para-empresas' => array(
			'emp_main_title'       => 'Tu operación de envíos a %s, resuelta desde {{origen_ciudad}}',
			'emp_soluciones_title' => 'Qué resolvemos para tu empresa cuando envía a %s',
			'emp_soluciones_intro' => 'Cuatro necesidades que se repiten en casi todas las empresas que envían a %s desde {{origen_pais}}.',
			'emp_docs_intro'       => 'La mayoría de los envíos que se quedan retenidos en la aduana de %s no fallan por el transporte, sino por un papel mal emitido. Estos son los que te vamos a pedir.',
		),

		/* ── Hub de destinos ──────────────────────────────────────────── */
		'destinos' => array(
			'dest_hero_title' => 'Envíos de {{origen_ciudad}} a <span>%s</span> y a todo el mundo',
			'dest_intro_sub'  => 'Envíos a %s y a otros destinos',
		),
	) );
}

/* Reescribe el campo si esta página tiene país y el texto sigue siendo el del
 * tema. El país lo pone el contexto: ver grenvios_hq_pais(). */
add_filter( 'grenvios_campo_valor', function ( $valor, $key, $default = '' ) {
	if ( is_admin() ) return $valor;
	if ( ! function_exists( 'grenvios_hq_pais' ) || ! function_exists( 'grenvios_current_slug' ) ) return $valor;

	// Los *_hero_* ya los lleva la cabecera; aquí solo el cuerpo (salvo el hub).
	if ( preg_match( '/_hero_(title|eyebrow|breadcrumb_home)$/', $key ) && $key !== 'dest_hero_title' ) return $valor;

	// Si el texto guardado no es el del tema, alguien lo escribió para esta página.
	if ( (string) $valor !== (string) $default ) return $valor;

	$pais = grenvios_hq_pais();
	if ( $pais === '' ) return $valor;

	static $slug = null;
	if ( $slug === null ) $slug = grenvios_current_slug();
	$mapa = grenvios_textos_pais_mapa();
	if ( ! isset( $mapa[ $slug ][ $key ] ) ) return $valor;

	return sprintf( $mapa[ $slug ][ $key ], $pais );
}, 20, 3 );
