<?php
/**
 * Grenvíos — SEDES · contenido que cambia por país de origen (Fase 2).
 *
 * Lo que de verdad separa una sede de otra. Sin esto, diez sedes son diez
 * clones con distinto teléfono, y Google colapsa las variantes e indexa una
 * sola (ver la doctrina anti *doorway pages* de inc/paginas-combinadas.php).
 *
 * Tres cosas:
 *
 *   1. TOKENS DE ORIGEN — «desde Lima» dejaba de ser correcto en cuanto había
 *      una sede en Madrid. Ahora se escribe {{origen_ciudad}} y cada sede pone
 *      la suya.
 *   2. DESTINOS POR SEDE — desde Madrid no se envía a los mismos países que
 *      desde Lima, ni con los mismos plazos. Además, una sede nunca puede
 *      enviarse a sí misma.
 *   3. CONTINENTES POR SEDE — el submenú de Destinos va agrupado por
 *      continente en un orden fijo que empieza por América. A un usuario en
 *      Madrid eso le abre con ocho países americanos antes que con Europa.
 *      El continente de la propia sede pasa a ir primero.
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ══════════════════════════════════════
   1) TOKENS DE ORIGEN
══════════════════════════════════════ */

/* ORIGEN vs DESTINO — la distinción de la que depende que las frases tengan
 * sentido.
 *
 * En una RUTA DE PAÍS (/cu/), el país de la ruta es el DESTINO: quien la lee
 * está en Lima y quiere enviar a Cuba. El origen sigue siendo Perú. Confundirlo
 * produce frases al revés: «cuánto demora un envío desde Cuba», que es
 * justamente el servicio que no se presta.
 *
 * Solo en una SEDE de verdad —una oficina propia en otro país, que hoy no
 * existe— el país de la ruta sería el origen. */

/* Ciudad DESDE la que salen los envíos. En una ruta de país, siempre Lima. */
function grenvios_sede_ciudad( $sede = null ) {
	$sede = $sede !== null ? $sede : grenvios_sede();
	if ( function_exists( 'grenvios_es_ruta_pais' ) && grenvios_es_ruta_pais( $sede ) ) {
		$sede = grenvios_sede_master();
	}
	$biz = grenvios_biz( $sede );
	return isset( $biz['city'] ) && $biz['city'] !== '' ? $biz['city'] : '';
}

/* País DESDE el que se envía. En una ruta de país, siempre Perú. */
function grenvios_sede_pais_nombre( $sede = null ) {
	$sede = $sede !== null ? $sede : grenvios_sede();
	if ( function_exists( 'grenvios_es_ruta_pais' ) && grenvios_es_ruta_pais( $sede ) ) {
		$sede = grenvios_sede_master();
	}
	$propio = grenvios_sede_destino_propio( $sede );
	if ( $propio !== '' ) {
		$p = grenvios_sede_propuesta( $propio );
		if ( ! empty( $p['nombre'] ) ) return $p['nombre'];
	}
	return grenvios_sede_country( $sede );
}

/* País AL que se envía en esta ruta: «Cuba» en /cu/. Vacío en el sitio
 * principal, que no tiene un destino concreto sino todos. */
function grenvios_sede_destino_nombre( $sede = null ) {
	$sede = $sede !== null ? $sede : grenvios_sede();
	if ( ! function_exists( 'grenvios_es_ruta_pais' ) || ! grenvios_es_ruta_pais( $sede ) ) return '';

	$propio = grenvios_sede_destino_propio( $sede );
	if ( $propio === '' ) return '';
	$p = grenvios_sede_propuesta( $propio );
	return ! empty( $p['nombre'] ) ? $p['nombre'] : '';
}

/* Sustituye los tokens de sede (origen y datos de contacto) en cualquier cadena.
 *
 * Los de contacto hacen falta porque el teléfono y la dirección estaban escritos
 * a mano dentro de los textos («llama al 900 612 836», «visítanos en Jr. Callao
 * 220»). En la sede de Madrid eso manda al cliente a una oficina de Lima. */
function grenvios_sede_tokens_apply( $text ) {
	$text = (string) $text;
	if ( strpos( $text, '{{origen' ) === false
		&& strpos( $text, '{{contacto' ) === false
		&& strpos( $text, '{{destino' ) === false ) return $text;

	$ciudad  = grenvios_sede_ciudad();
	$pais    = grenvios_sede_pais_nombre();
	$destino = grenvios_sede_destino_nombre();
	$biz     = grenvios_biz();

	return strtr( $text, array(
		'{{origen_ciudad}}'      => $ciudad,
		'{{origen_pais}}'        => $pais,
		// El país de ESTA ruta. En el sitio principal no hay uno solo, así que
		// «{{destino_pais}}» queda vacío y «{{destino_a}}» dice «al extranjero».
		'{{destino_pais}}'       => $destino,
		'{{destino_a}}'          => $destino !== '' ? 'a ' . $destino : 'al extranjero',
		// «desde Lima» / «desde España»: se resuelve entero para no dejar la
		// preposición colgando si la ciudad estuviera vacía.
		'{{origen_desde}}'       => $ciudad !== '' ? 'desde ' . $ciudad : ( $pais !== '' ? 'desde ' . $pais : '' ),
		'{{contacto_telefono}}'  => isset( $biz['phone'] ) ? $biz['phone'] : '',
		'{{contacto_tel}}'       => isset( $biz['phone_tel'] ) ? $biz['phone_tel'] : '',
		'{{contacto_email}}'     => isset( $biz['email'] ) ? $biz['email'] : '',
		'{{contacto_direccion}}' => isset( $biz['address'] ) ? $biz['address'] : '',
		'{{contacto_horario}}'   => isset( $biz['hours'] ) ? $biz['hours'] : '',
		// Solo dígitos: es lo que espera la URL de wa.me.
		'{{contacto_wa}}'        => isset( $biz['wa_number'] ) ? preg_replace( '/\D/', '', (string) $biz['wa_number'] ) : '',
	) );
}

/* Última pasada sobre el HTML ya montado. Se engancha aquí y no en el mapa de
 * `grenvios_text_tokens` porque strtr() sustituye en una sola pasada: un
 * {{origen_ciudad}} que viva DENTRO del texto de un campo o de un repeater no
 * lo alcanzaría y saldría en crudo a la página. */
add_filter( 'grenvios_text_html', 'grenvios_sede_tokens_apply' );

/* Y también en el contenido de las entradas y páginas editadas a mano. */
add_filter( 'the_content', 'grenvios_sede_tokens_apply', 9 );

/* ══════════════════════════════════════
   2) DESTINOS POR SEDE
   Por defecto una sede hereda todos los destinos MENOS ella misma. Si se
   guarda una selección propia, manda esa.
══════════════════════════════════════ */

/* Opción donde una sede guarda su selección de destinos. */
function grenvios_sede_destinos_option( $sede = null ) {
	return 'grenvios_destinos_sede' . grenvios_sede_suffix( $sede );
}

/* Slugs de destino activos en una sede. Array vacío = «todos menos yo». */
function grenvios_sede_destinos_sel( $sede = null ) {
	$sel = get_option( grenvios_sede_destinos_option( $sede ), array() );
	return is_array( $sel ) ? array_values( array_filter( array_map( 'sanitize_title', $sel ) ) ) : array();
}

function grenvios_sede_destinos_save( $slugs, $sede = null ) {
	$clean = array();
	foreach ( (array) $slugs as $s ) {
		$s = sanitize_title( $s );
		if ( $s !== '' ) $clean[] = $s;
	}
	update_option( grenvios_sede_destinos_option( $sede ), array_values( array_unique( $clean ) ) );
}

/* Slug de destino que representa al país de la propia sede (para excluirlo). */
function grenvios_sede_destino_propio( $sede = null ) {
	$sede = $sede !== null ? $sede : grenvios_sede();
	$iso  = grenvios_sede_country( $sede );
	if ( $iso === '' ) return '';
	foreach ( grenvios_sede_paises() as $dslug => $row ) {
		if ( strtoupper( $row[0] ) === $iso ) return $dslug;
	}
	return '';
}

add_filter( 'grenvios_destinos', function ( $destinos ) {
	// El recorte por sede solo tiene sentido con más de una sede…
	if ( function_exists( 'grenvios_sedes_active' ) && grenvios_sedes_active() ) {
		$sede = grenvios_sede();

		/* Una SEDE no se envía a sí misma: si algún día hay oficina en España,
		 * /es/destinos/espana/ no tiene sentido porque ahí España es el origen.
		 *
		 * Pero una RUTA DE PAÍS es lo contrario: el país de la ruta es el DESTINO.
		 * Quitarlo dejaba fuera precisamente el país del que trata esa ruta, y el
		 * submenú Destinos de /cu/ listaba ocho países sin Cuba —mientras el
		 * selector de la bandera, al lado, sí la mostraba—: dos listas distintas
		 * en el mismo encabezado. */
		$es_ruta_pais = function_exists( 'grenvios_es_ruta_pais' ) && grenvios_es_ruta_pais( $sede );
		if ( ! $es_ruta_pais ) {
			$propio = grenvios_sede_destino_propio( $sede );
			if ( $propio !== '' && isset( $destinos[ $propio ] ) ) unset( $destinos[ $propio ] );
		}

		// Selección propia, si la hay.
		$sel = grenvios_sede_destinos_sel( $sede );
		if ( $sel ) {
			foreach ( array_keys( $destinos ) as $slug ) {
				if ( ! in_array( $slug, $sel, true ) ) unset( $destinos[ $slug ] );
			}
		}
	}

	// …pero los tokens se resuelven SIEMPRE. Con una sola sede dan «Perú» y
	// «Lima», igual que cuando estaban escritos a mano; si no se resolvieran
	// aquí, un sitio de una sede mostraría {{origen_pais}} en crudo.
	foreach ( $destinos as $slug => $d ) {
		foreach ( array( 'seo', 'desc', 'lead', 'restr', 'kw' ) as $campo ) {
			if ( isset( $d[ $campo ] ) ) $destinos[ $slug ][ $campo ] = grenvios_sede_tokens_apply( $d[ $campo ] );
		}
	}
	return $destinos;
}, 20 );

/* ══════════════════════════════════════
   3) CONTINENTES POR SEDE
   Mismo conjunto de continentes, distinto orden: primero el de la sede.
══════════════════════════════════════ */

/* Continente al que pertenece el país de una sede ('' si no se sabe).
 *
 * Sale del registro de sedes, no de la lista de destinos: Perú es la sede
 * principal y por eso mismo no figura entre los destinos, así que leerlo de
 * ahí dejaba sin continente justo a la sede más importante. */
function grenvios_sede_continente( $sede = null ) {
	$propio = grenvios_sede_destino_propio( $sede );
	if ( $propio === '' ) return '';

	$p = grenvios_sede_propuesta( $propio );
	if ( ! empty( $p['continente'] ) ) return $p['continente'];

	// Último recurso: un país agregado a mano en Destinos que no esté en el
	// registro de sedes pero sí tenga continente en su ficha.
	$custom = function_exists( 'grenvios_destinos_custom' ) ? grenvios_destinos_custom() : array();
	if ( isset( $custom[ $propio ]['continente'] ) ) return $custom[ $propio ]['continente'];

	return '';
}

add_filter( 'grenvios_destino_continentes', function ( $lista ) {
	if ( ! function_exists( 'grenvios_sedes_active' ) || ! grenvios_sedes_active() ) return $lista;

	$mio = grenvios_sede_continente();
	if ( $mio === '' ) return $lista;

	$idx = array_search( $mio, $lista, true );
	if ( $idx === false || $idx === 0 ) return $lista;

	unset( $lista[ $idx ] );
	array_unshift( $lista, $mio );
	return array_values( $lista );
} );

/* ══════════════════════════════════════
   4) TOKENS EN EL RESTO DE SUPERFICIES
   El <title>, los textos sueltos traducibles y el contenido de las entradas no
   pasan por grenvios_apply_text_tokens(), así que se resuelven aquí. Cada
   llamada sale enseguida si la cadena no lleva ningún token.
══════════════════════════════════════ */

/* El tema arma el <title> en `pre_get_document_title` (functions.php:583), que
 * corta antes de que WordPress llegue a `document_title_parts`. Hay que
 * engancharse a los dos: al primero con prioridad tardía para pillar el título
 * del tema, y al segundo por si algún día se deja pasar a WordPress. */
add_filter( 'pre_get_document_title', 'grenvios_sede_tokens_apply', 99 );

add_filter( 'document_title_parts', function ( $parts ) {
	foreach ( $parts as $k => $v ) {
		if ( is_string( $v ) ) $parts[ $k ] = grenvios_sede_tokens_apply( $v );
	}
	return $parts;
}, 20 );

add_filter( 'grenvios_t', 'grenvios_sede_tokens_apply', 20 );
add_filter( 'the_title', 'grenvios_sede_tokens_apply', 20 );

/* Títulos y descripciones del registro de páginas. Alimentan el <title>, el
 * meta description, el OpenGraph y los nodos Service del JSON-LD, así que
 * resolverlos aquí evita tener que perseguirlos en cada consumidor. */
add_filter( 'grenvios_pages', function ( $pages ) {
	foreach ( $pages as $slug => $p ) {
		foreach ( array( 'title', 'seo', 'desc', 'kw' ) as $campo ) {
			if ( isset( $p[ $campo ] ) && is_string( $p[ $campo ] ) ) {
				$pages[ $slug ][ $campo ] = grenvios_sede_tokens_apply( $p[ $campo ] );
			}
		}
	}
	return $pages;
}, 20 );
