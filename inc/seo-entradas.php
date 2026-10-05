<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Entradas del blog: <title>, H1 y description propios en cada ruta
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Auditoría de las 290 URLs de los sitemaps: las tres guías del blog, copiadas
 * a las nueve rutas, salían con el <title> por defecto de WordPress —«Cómo
 * embalar un paquete – greenvios»— idéntico en las DIEZ versiones, sin nombrar
 * el país, y con el extracto entero (300 caracteres) como meta description.
 * Eran las únicas 30 URLs del sitio con title duplicado.
 *
 * El cuerpo de cada copia ya es distinto (lleva el bloque de su país), pero
 * Google decide con el title y el H1 antes que con el cuerpo. Aquí:
 *
 *   · <title>: «Cómo embalar un paquete a Chile | Grenvíos».
 *   · H1 de la entrada (solo el de la propia entrada, no menús ni listados):
 *     el mismo texto, para que title y H1 coincidan.
 *   · description: el extracto recortado a tamaño de resultado y cerrado con
 *     lo que la copia aporta de verdad —los plazos y la aduana de ese país—.
 *
 * Si la entrada tiene su propio title/description escritos a mano
 * (grenvios_seo_title / grenvios_seo_desc), mandan esos y aquí no se toca nada.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* País de la ruta si estamos en una entrada de una ruta de país, o ''. */
function grenvios_se_pais() {
	if ( ! is_singular( 'post' ) || ! function_exists( 'grenvios_hh_pais' ) ) return '';
	return (string) grenvios_hh_pais();
}

/* Sufijo de origen para las guías que YA nombran a su país.
 *
 * Las 81 guías de destino («Mudarse a Cuba…», «Qué se puede enviar a Chile…»)
 * viven en dos sitios: en la ruta principal y, copiadas, en la ruta de ese
 * mismo país. Como su título ya nombra al país, la regla de arriba no le
 * añadía nada y las dos versiones salían con el MISMO <title>: 81 pares
 * duplicados, justo los de las guías que más tráfico de cola larga traen.
 *
 * Se diferencian por donde de verdad se diferencian: la de la ruta principal
 * es la que se lee desde Perú —y «enviar a Cuba desde Perú» es además la
 * consulta con intención—, así que lleva el origen; la de la ruta del país se
 * queda con el título limpio. Es el mismo criterio que ya siguen sus slugs
 * (…-desde-peru en la maestra, limpio en la copia). */
function grenvios_se_origen_sufijo( $post_id ) {
	if ( grenvios_se_pais() !== '' ) return '';                       // ruta de país: limpio
	if ( (string) get_post_meta( $post_id, 'grenvios_solo_pais', true ) === '' ) return '';
	$pais = function_exists( 'grenvios_sede_pais_nombre' ) ? trim( (string) grenvios_sede_pais_nombre() ) : '';
	return $pais !== '' ? ' desde ' . $pais : '';
}

/* Coloca «desde Perú» justo detrás del país del que habla la guía, no al final.
 * Pegado al final salían títulos como «…y vuelta a clases desde Perú»; detrás
 * del país se lee como se busca: «Mudarse a Cuba desde Perú: cómo enviar…». */
function grenvios_se_con_origen( $titulo, $post_id ) {
	$suf = grenvios_se_origen_sufijo( $post_id );
	if ( $suf === '' || mb_stripos( $titulo, trim( $suf ) ) !== false ) return $titulo;

	$destino = (string) get_post_meta( $post_id, 'grenvios_solo_pais', true );
	$dest    = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();
	$nombre  = isset( $dest[ $destino ]['title'] ) ? $dest[ $destino ]['title'] : '';
	$nombre  = trim( preg_replace( '/^env[ií]os?\s+a\s+/iu', '', $nombre ) );

	if ( $nombre !== '' ) {
		$pos = mb_stripos( $titulo, $nombre );
		if ( $pos !== false ) {
			$corte = $pos + mb_strlen( $nombre );
			return mb_substr( $titulo, 0, $corte ) . $suf . mb_substr( $titulo, $corte );
		}
	}
	return $titulo . $suf;
}

function grenvios_se_titulo_base( $post_id ) {
	$t    = trim( wp_strip_all_tags( get_post_field( 'post_title', $post_id ) ) );
	$pais = grenvios_se_pais();
	// «Cómo embalar un paquete» + « a Chile», salvo que el título ya lo nombre.
	if ( $pais !== '' && mb_stripos( $t, $pais ) === false ) $t .= ' a ' . $pais;

	return grenvios_se_con_origen( $t, $post_id );
}

/* ── <title> ────────────────────────────────────────────────────────────── */
add_filter( 'pre_get_document_title', function ( $title ) {
	if ( ! is_singular( 'post' ) ) return $title;
	$id = (int) get_queried_object_id();
	if ( get_post_meta( $id, 'grenvios_seo_title', true ) !== '' ) return $title;   // escrito a mano
	$t = grenvios_se_titulo_base( $id );
	/* Más de 60 caracteres: Google lo corta. Las guías llevan la keyword antes
	 * de los dos puntos («Valor declarado de un envío a Ecuador: cómo…»), así
	 * que el <title> se queda con esa parte; el H1 conserva el título entero. */
	if ( mb_strlen( $t . ' | Grenvíos' ) > 60 && ( $c = mb_strpos( $t, ':' ) ) !== false && $c >= 12 ) $t = mb_substr( $t, 0, $c );
	return $t . ' | Grenvíos';
}, 25 );

/* ── H1 de la entrada ───────────────────────────────────────────────────── */
add_filter( 'the_title', function ( $title, $id = 0 ) {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || is_admin() ) return $title;
	if ( (int) $id !== (int) get_queried_object_id() ) return $title;
	$pais = grenvios_se_pais();
	if ( $pais !== '' ) {
		return mb_stripos( $title, $pais ) !== false ? $title : $title . ' a ' . $pais;
	}
	// Ruta principal: el origen, para que H1 y <title> sigan coincidiendo.
	return grenvios_se_con_origen( $title, (int) get_queried_object_id() );
}, 10, 2 );

/* ── meta description ───────────────────────────────────────────────────── */
add_filter( 'grenvios_meta_description', function ( $desc, $slug ) {
	if ( ! is_singular( 'post' ) ) return $desc;
	$id = (int) get_queried_object_id();
	if ( get_post_meta( $id, 'grenvios_seo_desc', true ) !== '' ) return $desc;      // escrita a mano

	$base = trim( wp_strip_all_tags( (string) $desc ) );
	$pais = grenvios_se_pais();
	$cola = $pais !== '' ? ' Con los plazos y la aduana de ' . $pais . '.' : '';
	$max  = 158 - mb_strlen( $cola );

	if ( mb_strlen( $base ) > $max ) {
		// Corte en palabra completa, nunca a media palabra.
		$base = mb_substr( $base, 0, $max );
		$base = preg_replace( '/\s+\S*$/u', '', $base );
		$base = rtrim( $base, ' ,;:' ) . '…';
	}
	return $base . $cola;
}, 10, 2 );
