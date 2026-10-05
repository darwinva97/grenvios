<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Cabecera de cada página de ruta: el país arriba del todo
 * ══════════════════════════════════════════════════════════════════════════
 *
 * El `<title>` de las copias ya decía «Contacto para Envíos a Estados Unidos»,
 * pero lo que se ve —antesala, H1 y migas— seguía siendo el de Perú: «Contacto
 * Grenvíos — Hablemos de tu envío». Dos problemas a la vez:
 *
 *   SEO   El H1 no coincidía con el título, y es la segunda señal más fuerte de
 *         la página. Google veía una página sobre Estados Unidos con un
 *         encabezado que no lo mencionaba.
 *   UX    Quien llegaba desde el buscador no leía nada sobre su país por encima
 *         del pliegue, en 24 páginas por ruta.
 *
 * Al duplicar se copian los campos de la página de Perú tal cual, así que la
 * cabecera se reescribe al pintarla. Cubre los dos caminos del tema: las páginas
 * de plantilla (campos `*_hero_title`, `*_hero_eyebrow`…) y las que pintan desde
 * PHP con `logisko_page_banner()`.
 *
 * SI LA CLIENTA LO EDITÓ, MANDA LO SUYO
 *
 * En las páginas de plantilla se compara el valor guardado en la copia con el de
 * su original: si difieren, alguien lo escribió a mano para ese país y no se
 * toca. Solo se reemplaza lo que sigue siendo la copia literal de Perú.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Ruta de país activa (no la principal ni un idioma de verdad), o ''. */
function grenvios_cab_lang() {
	static $l = null;
	if ( $l !== null ) return $l;
	$l = '';
	if ( is_admin() || ! function_exists( 'grenvios_i18n_current' ) ) return $l;
	$c = grenvios_i18n_current();
	if ( ! $c || grenvios_i18n_default() === $c ) return $l;
	if ( ! function_exists( 'grenvios_es_ruta_pais' ) || ! grenvios_es_ruta_pais( $c ) ) return $l;
	return $l = $c;
}

function grenvios_cab_pais() {
	if ( function_exists( 'grenvios_espejo_es' ) && is_singular() && grenvios_espejo_es( (int) get_queried_object_id() ) ) return '';
	$l = grenvios_cab_lang();
	return ( $l !== '' && function_exists( 'grenvios_sede_destino_nombre' ) ) ? grenvios_sede_destino_nombre( $l ) : '';
}

/* H1 de cada página, con %s por el país. En minúscula de frase, como el resto de
 * cabeceras del tema, y con el país dentro de <span> para que tome el color de
 * acento igual que el «Grenvíos» de «Conectamos al mundo con Grenvíos».
 *
 * Filtro `grenvios_cabecera_titulos`: para reescribir cualquiera. */
function grenvios_cab_titulos() {
	return apply_filters( 'grenvios_cabecera_titulos', array(
		'contacto'                             => 'Hablemos de tu envío a <span>%s</span>',
		'nosotros'                             => 'Tu equipo para enviar a <span>%s</span>',
		'servicios'                            => 'Servicios de envío a <span>%s</span>',
		'envio-internacional-de-paquetes'      => 'Envío de paquetes a <span>%s</span>',
		'envio-internacional-de-documentos'    => 'Envío de documentos a <span>%s</span>',
		'carga-internacional'                  => 'Carga internacional a <span>%s</span>',
		'apostilla-y-traduccion'               => 'Apostilla y traducción para <span>%s</span>',
		'cotizar'                              => 'Cotiza tu envío a <span>%s</span>',
		'rastreo-de-envios'                    => 'Rastrea tu envío a <span>%s</span>',
		'envios-para-empresas'                 => 'Envíos de empresa a <span>%s</span>',
		'envio-de-compras'                     => 'Envía tus compras a <span>%s</span>',
		'envio-de-equipaje'                    => 'Envío de equipaje a <span>%s</span>',
		'envio-de-alimentos'                   => 'Envío de alimentos a <span>%s</span>',
		'peso-volumetrico'                     => 'Cómo se cobra el peso en tu envío a <span>%s</span>',
		'seguro-de-envios'                     => 'Seguro para tu envío a <span>%s</span>',
		'tiempos-de-entrega'                   => '¿Cuánto demora un envío a <span>%s</span>?',
		'que-se-puede-enviar'                  => 'Qué se puede enviar a <span>%s</span>',
		'aduanas-e-impuestos'                  => 'Aduana e impuestos de <span>%s</span>',
		'preguntas-frecuentes'                 => 'Preguntas frecuentes sobre envíos a <span>%s</span>',
		'envios-desde-provincias'              => 'Envía a <span>%s</span> desde cualquier ciudad del Perú',
		'recojo-a-domicilio-lima'              => 'Recogemos en casa tu envío a <span>%s</span>',
		'como-enviar-un-paquete-al-extranjero' => 'Cómo enviar un paquete a <span>%s</span>',
		'blog'                                 => 'Guías para enviar a <span>%s</span>',
	) );
}

/* Slug maestro de la página que se está pintando. */
function grenvios_cab_slug() {
	$id = is_home() ? (int) get_option( 'page_for_posts' ) : (int) get_queried_object_id();
	if ( ! $id ) return '';
	if ( is_home() && function_exists( 'pll_get_post' ) ) {
		$t = (int) pll_get_post( $id, grenvios_cab_lang() );
		if ( $t ) $id = $t;
	}
	return function_exists( 'grenvios_canonical_slug' ) ? grenvios_canonical_slug( $id ) : get_post_field( 'post_name', $id );
}

/* ¿Sigue la copia con el texto literal de Perú en este campo? */
function grenvios_cab_sin_editar( $key ) {
	$id = (int) get_queried_object_id();
	if ( ! $id || ! function_exists( 'grenvios_i18n_master_id' ) ) return true;
	$m = (int) grenvios_i18n_master_id( $id );
	if ( ! $m || $m === $id ) return true;
	$mk = 'grenvios_' . $key;
	if ( ! metadata_exists( 'post', $id, $mk ) ) return true;
	return (string) get_post_meta( $id, $mk, true ) === (string) get_post_meta( $m, $mk, true );
}

/* ── Páginas de plantilla: los campos *_hero_* ── */
add_filter( 'grenvios_campo_valor', function ( $valor, $key ) {
	/* La mayoría de plantillas nombran sus campos `prefijo_hero_title`. La de
	 * preguntas frecuentes no: usa `pf_title` y `pf_sub`, y por eso quedaba como
	 * la única página de cada ruta con «Resolvemos tus dudas» de cabecera. */
	if ( preg_match( '/_hero_(title|eyebrow|breadcrumb_home)$/', $key, $mm ) ) {
		$campo = $mm[1];
	} elseif ( $key === 'pf_title' ) {
		$campo = 'title';
	} elseif ( $key === 'pf_sub' ) {
		$campo = 'eyebrow';
	} elseif ( $key === 'tool_title' ) {     // páginas pintadas desde PHP
		$campo = 'title';
	} elseif ( $key === 'tool_eyebrow' ) {
		$campo = 'eyebrow';
	} else {
		return $valor;
	}
	$pais = grenvios_cab_pais();
	if ( $pais === '' ) return $valor;
	if ( ! grenvios_cab_sin_editar( $key ) ) return $valor;   // la clienta lo escribió

	if ( $campo === 'eyebrow' || $campo === 'breadcrumb_home' ) {
		return 'Envíos a ' . $pais;
	}
	$mapa = grenvios_cab_titulos();
	$slug = grenvios_cab_slug();
	return isset( $mapa[ $slug ] ) ? sprintf( $mapa[ $slug ], esc_html( $pais ) ) : $valor;
}, 10, 2 );

/* ── Páginas pintadas desde PHP ── */
add_filter( 'grenvios_cabecera', function ( $par ) {
	$pais = grenvios_cab_pais();
	if ( $pais === '' ) return $par;
	$mapa = grenvios_cab_titulos();
	$slug = grenvios_cab_slug();
	if ( ! isset( $mapa[ $slug ] ) ) return $par;
	// Si la clienta cambió la cabecera de esta copia desde el editor, manda la suya.
	if ( ! grenvios_cab_sin_editar( 'tool_title' ) || ! grenvios_cab_sin_editar( 'tool_eyebrow' ) ) return $par;
	return array( 'Envíos a ' . $pais, sprintf( $mapa[ $slug ], esc_html( $pais ) ) );
} );

/* ── Migas: «Inicio» de una ruta es la portada de ese país, y así se llama ── */
add_filter( 'grenvios_miga_inicio', function ( $txt ) {
	$pais = grenvios_cab_pais();
	return $pais !== '' ? 'Envíos a ' . $pais : $txt;
} );
