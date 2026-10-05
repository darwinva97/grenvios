<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Palabra clave objetivo por defecto de cada página
 * ══════════════════════════════════════════════════════════════════════════
 *
 * LO QUE ESTABA PASANDO
 * Las 350 páginas del sitio (35 × 10 rutas) tenían la keyword SIN definir. El
 * tema, para no dejar el campo vacío, la deducía del slug… y el slug no lleva
 * tildes ni preposiciones. Como esa keyword es el TEXTO ANCLA con el que el
 * enlazado interno y el cierre de cada guía enlazan a la página, el sitio
 * entero se estaba enlazando a sí mismo con anclas como:
 *
 *     «envio de equipaje»   «peso volumetrico»   «apostilla y traduccion»
 *     «que se puede enviar» «recojo a domicilio lima»
 *
 * Mal escritas para el lector y flojas para Google, que lee el ancla como la
 * descripción de la página que recibe el enlace. Son cientos de enlaces
 * internos: es el arreglo con más retorno por línea de código de todo el SEO
 * on-page que queda por hacer.
 *
 * LO QUE HACE ESTE MÓDULO
 * Da a cada página una keyword escrita como se busca, en español de Perú, con
 * intención comercial donde toca:
 *
 *     /servicios/envio-de-equipaje/   → «envío de equipaje al extranjero»
 *     /servicios/peso-volumetrico/    → «peso volumétrico»
 *     /cotizar/                       → «cotizar envío internacional»
 *
 * Y en las rutas de país la compone con el destino, que es como se busca de
 * verdad («envío de equipaje a Chile», «cuánto cuesta enviar a Chile»).
 *
 * NO PISA NADA: es solo el valor por defecto. En cuanto alguien escriba una
 * keyword en la caja «SEO Grenvíos» de esa página, manda la suya.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ─────────────────────────────────────────────────────────────────────────
 * 1) Keyword de la ruta principal, por slug canónico
 *    · 'kw'   → cómo se busca desde Perú
 *    · 'pais' → plantilla para las rutas de país (%s = nombre del destino)
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_kw_mapa() {
	return apply_filters( 'grenvios_kw_mapa', array(
		/* Ojo con la portada de una ruta de país: «envíos a Chile» ya es la
		 * keyword de la ficha /cl/envios-a-chile/, y dos páginas de la misma
		 * ruta peleando por la misma consulta es canibalización —las dos se
		 * frenan—. La portada se queda con la consulta de origen-destino, que
		 * es la que la diferencia. */
		'home' => array(
			'kw'   => 'envíos internacionales desde Perú',
			/* «envíos a X desde Perú» y «envíos a X» (la ficha) son la misma búsqueda
			 * para Google: la portada se queda con la consulta «courier» (2026-10-02). */
			'pais' => 'courier de Perú a %s',
		),
		'servicios' => array(
			'kw'   => 'servicios de envío internacional',
			'pais' => 'servicios de envío a %s',
		),
		'envio-internacional-de-paquetes' => array(
			'kw'   => 'envío internacional de paquetes',
			'pais' => 'envío de paquetes a %s',
		),
		'envio-internacional-de-documentos' => array(
			'kw'   => 'envío internacional de documentos',
			'pais' => 'envío de documentos a %s',
		),
		'carga-internacional' => array(
			'kw'   => 'carga internacional',
			'pais' => 'carga internacional a %s',
		),
		'envio-de-equipaje' => array(
			'kw'   => 'envío de equipaje al extranjero',
			'pais' => 'envío de equipaje a %s',
		),
		'envio-de-compras' => array(
			'kw'   => 'envío de compras al extranjero',
			'pais' => 'envío de compras a %s',
		),
		'envio-de-alimentos' => array(
			'kw'   => 'envío de alimentos al extranjero',
			'pais' => 'envío de alimentos a %s',
		),
		'apostilla-y-traduccion' => array(
			'kw'   => 'apostilla y traducción de documentos',
			'pais' => 'apostilla para documentos a %s',
		),
		'peso-volumetrico' => array(
			'kw'   => 'peso volumétrico',
			'pais' => 'peso volumétrico en envíos a %s',
		),
		'tiempos-de-entrega' => array(
			'kw'   => 'tiempos de entrega de envíos internacionales',
			'pais' => 'cuánto demora un envío a %s',
		),
		'que-se-puede-enviar' => array(
			'kw'   => 'qué se puede enviar al extranjero',
			'pais' => 'qué se puede enviar a %s',
		),
		'aduanas-e-impuestos' => array(
			'kw'   => 'aduanas e impuestos en envíos internacionales',
			'pais' => 'aduana de %s',
		),
		'seguro-de-envios' => array(
			'kw'   => 'seguro para envíos internacionales',
			'pais' => 'seguro para envíos a %s',
		),
		'cotizar' => array(
			'kw'   => 'cotizar envío internacional',
			'pais' => 'cuánto cuesta enviar a %s',
		),
		'rastreo-de-envios' => array(
			'kw'   => 'rastreo de envíos internacionales',
			'pais' => 'rastrear un envío a %s',
		),
		'como-enviar-un-paquete-al-extranjero' => array(
			'kw'   => 'cómo enviar un paquete al extranjero',
			'pais' => 'cómo enviar un paquete a %s',
		),
		'recojo-a-domicilio-lima' => array(
			'kw'   => 'recojo de envíos a domicilio en Lima',
			'pais' => 'recojo a domicilio para enviar a %s',
		),
		'envios-desde-provincias' => array(
			'kw'   => 'envíos internacionales desde provincias',
			'pais' => 'enviar a %s desde provincias',
		),
		'envios-para-empresas' => array(
			'kw'   => 'envíos internacionales para empresas',
			'pais' => 'envíos empresariales a %s',
		),
		'destinos' => array(
			'kw'   => 'destinos de envíos internacionales',
			'pais' => 'destinos de envíos internacionales',
		),
		'nosotros' => array(
			/* «courier internacional en Lima» es de /courier-internacional-en-lima/. */
			'kw'   => 'empresa de envíos desde Perú',
			'pais' => 'courier de envíos a %s',
		),
		'contacto' => array(
			'kw'   => 'agencia de envíos internacionales en Lima',
			'pais' => 'contacto para envíos a %s',
		),
		'preguntas-frecuentes' => array(
			'kw'   => 'preguntas frecuentes sobre envíos internacionales',
			'pais' => 'preguntas frecuentes de envíos a %s',
		),
		'blog' => array(
			'kw'   => 'guías de envíos internacionales',
			'pais' => 'guías para enviar a %s',
		),
		/* Misma historia: /que-se-puede-enviar-a-chile/ ya cubre la consulta
		 * general; esta página lista artículo por artículo. */
		'articulos-por-pais' => array(
			'kw'   => 'guías de envío por país',
			'pais' => 'guías de envío por país',
		),
	) );
}

/* Ficha de destino: la keyword es la consulta comercial del país. */
function grenvios_kw_destino( $slug ) {
	$dest = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();
	if ( ! isset( $dest[ $slug ] ) ) return '';
	$p = trim( preg_replace( '/^env[ií]os?\s+a\s+/iu', '', (string) $dest[ $slug ]['title'] ) );
	return $p !== '' ? 'envíos a ' . $p : '';
}

/* ─────────────────────────────────────────────────────────────────────────
 * 2) Keyword por defecto de una página concreta
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_kw_por_defecto( $post_id ) {
	$post_id = (int) $post_id;
	if ( ! $post_id ) return '';

	/* Entradas del blog: el título ya es la consulta; no se toca. */
	if ( get_post_type( $post_id ) !== 'page' ) return '';

	$slug = function_exists( 'grenvios_canonical_slug' )
		? grenvios_canonical_slug( $post_id )
		: get_post_field( 'post_name', $post_id );

	if ( (int) get_option( 'page_on_front' ) === $post_id ) $slug = 'home';

	/* Ficha de destino (/destinos/chile/ y sus copias por ruta). */
	$kd = grenvios_kw_destino( $slug );
	if ( $kd !== '' ) return $kd;

	$mapa = grenvios_kw_mapa();
	if ( ! isset( $mapa[ $slug ] ) ) return '';

	$lang = function_exists( 'pll_get_post_language' ) ? (string) pll_get_post_language( $post_id ) : '';
	$def  = function_exists( 'grenvios_i18n_default' ) ? grenvios_i18n_default() : '';

	/* Ruta de país: la keyword lleva el destino, que es como se busca. */
	if ( $lang !== '' && $lang !== $def && ! empty( $mapa[ $slug ]['pais'] ) && function_exists( 'grenvios_sede_destino_nombre' ) ) {
		$pais = trim( (string) grenvios_sede_destino_nombre( $lang ) );
		if ( $pais !== '' ) {
			$kw = sprintf( $mapa[ $slug ]['pais'], $pais );
			/* «desde Perú» sale de la sede, no escrito a mano: si la empresa
			 * abre una sede en otro país, la keyword se ajusta sola. */
			if ( function_exists( 'grenvios_sede_pais_nombre' ) ) {
				$origen = trim( (string) grenvios_sede_pais_nombre() );
				if ( $origen !== '' && $origen !== 'Perú' ) $kw = str_replace( 'desde Perú', 'desde ' . $origen, $kw );
			}
			return $kw;
		}
	}

	return $mapa[ $slug ]['kw'];
}

/* Se engancha al respaldo de inc/seo-keywords.php: solo actúa cuando la página
 * no tiene keyword escrita a mano. */
add_filter( 'grenvios_seo_kw_default', function ( $kw, $post_id ) {
	$propia = grenvios_kw_por_defecto( $post_id );
	return $propia !== '' ? $propia : $kw;
}, 10, 2 );
