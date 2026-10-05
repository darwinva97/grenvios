<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Portada de cada ruta de país: el hero y el cotizador hablan de SU país
 * ══════════════════════════════════════════════════════════════════════════
 *
 * La portada de /ar/ o /cu/ es la misma home que la de Perú (front-page.php)
 * más su bloque de contenido por país. Lo que se veía por encima del pliegue
 * —carrusel y cotizador— seguía siendo el de Perú: «Llevamos tus envíos a más
 * de 30 países». Quien entra desde el buscador por «enviar a Argentina» leía un
 * titular que no menciona Argentina, y tenía que escribir el destino a mano en
 * un formulario que ya sabe a qué país va.
 *
 * Aquí se reescriben, solo en las rutas de país:
 *   · las diapositivas del carrusel (titular, frase superior y descripción),
 *   · el título de la tarjeta del cotizador,
 *   · el campo «Destino», que llega relleno con el país.
 *
 * SI LA CLIENTA LO EDITÓ, MANDA LO SUYO
 * Igual que en inc/paises-cabecera.php: solo se reemplaza lo que sigue siendo
 * la copia literal del texto que trae el tema. En cuanto alguien escribe algo
 * distinto para esa ruta, no se toca.
 *
 * El país de destino no se deduce de la URL: sale de la sede/ruta activa
 * (grenvios_sede_destino_nombre), la misma fuente que usan las cabeceras.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* País de la ruta activa, o '' si estamos en la principal o en un idioma real. */
function grenvios_hh_pais() {
	static $memo = null;
	if ( $memo !== null ) return $memo;
	if ( is_admin() ) return $memo = '';
	if ( function_exists( 'grenvios_cab_pais' ) )         return $memo = (string) grenvios_cab_pais();
	if ( function_exists( 'grenvios_sede_destino_nombre' ) ) return $memo = (string) grenvios_sede_destino_nombre();
	return $memo = '';
}

/* País del que trata la PÁGINA que se está viendo, venga de donde venga:
 *
 *   1) la ruta de país activa   → /ar/, /cu/ y todas sus páginas;
 *   2) la ficha de destino      → /destinos/argentina/ y sus espejos, que en la
 *                                 ruta principal son las páginas de cada país.
 *
 * Es lo que hace que el cotizador sirva en las decenas de páginas por país sin
 * una copia del formulario por cada una: el destino sale del contexto, no de
 * una lista escrita a mano.
 *
 * Filtro `grenvios_hq_pais`: para fijar el país en una página concreta. */
function grenvios_hq_pais() {
	/* Se resuelve UNA vez por petición: `grenvios_campo_valor` se dispara por
	 * cada campo de texto de la página —cientos— y sin esta caché la portada
	 * repetía la consulta de destinos otras tantas veces. */
	static $cache = null;
	if ( $cache !== null ) return $cache;

	$pais = grenvios_hh_pais();

	if ( $pais === '' && ! is_admin() && function_exists( 'grenvios_current_slug' ) && function_exists( 'grenvios_destinos' ) ) {
		$slug = grenvios_current_slug();
		$dest = grenvios_destinos();
		if ( $slug !== '' && isset( $dest[ $slug ] ) && ! empty( $dest[ $slug ]['title'] ) ) {
			$pais = (string) $dest[ $slug ]['title'];
		}
	}
	return $cache = (string) apply_filters( 'grenvios_hq_pais', $pais );
}

/* Textos del carrusel por país. Una entrada por diapositiva del tema, en el
 * mismo orden. %s es el país.
 *
 * Filtro `grenvios_home_hero_pais`: para reescribirlos sin tocar el tema. */
function grenvios_hh_slides( $pais ) {
	$s = array(
		array(
			'tagline' => 'Envíos desde {{origen_ciudad}} a %s',
			'title'   => 'Envíos internacionales de <br>paquetes y carga a <span>%s!</span>',
			'text'    => 'Llevamos tus documentos, paquetes y carga desde {{origen_ciudad}} <br>hasta cualquier ciudad de %s.',
		),
		array(
			'tagline' => 'Envíos desde {{origen_ciudad}} a %s',
			'title'   => 'Documentos, paquetes y <br>carga a <span>%s!</span>',
			'text'    => 'Envíos puerta a puerta desde {{origen_ciudad}}, {{origen_pais}}, <br>con seguimiento hasta la entrega en %s.',
		),
		array(
			'tagline' => 'Envíos desde {{origen_ciudad}} a %s',
			'title'   => 'Apostillamos y traducimos <br>para <span>%s!</span>',
			'text'    => 'No solo movemos tu documento: lo legalizamos y lo traducimos <br>para que tenga validez en %s.',
		),
	);
	foreach ( $s as $i => $slide ) {
		foreach ( $slide as $k => $v ) $s[ $i ][ $k ] = str_replace( '%s', $pais, $v );
	}
	return apply_filters( 'grenvios_home_hero_pais', $s, $pais );
}

/* Diapositivas del carrusel de la portada. */
add_filter( 'grenvios_repeater_items', function ( $items, $key ) {
	if ( $key !== 'home_slides' || ! is_array( $items ) || ! $items ) return $items;
	if ( function_exists( 'grenvios_current_slug' ) && grenvios_current_slug() !== 'home' ) return $items;

	$pais = grenvios_hh_pais();
	if ( $pais === '' ) return $items;

	// Lo que trae el tema para esta portada: sirve de referencia para saber si
	// la diapositiva sigue siendo la copia literal de Perú.
	$defs = function_exists( 'grenvios_repeater_defaults' ) ? grenvios_repeater_defaults( 'home' ) : array();
	$base = isset( $defs['home_slides'] ) ? $defs['home_slides'] : array();
	$nuevo = grenvios_hh_slides( $pais );

	foreach ( $items as $i => $it ) {
		if ( ! isset( $base[ $i ] ) || ! isset( $nuevo[ $i ] ) ) continue;   // diapositiva añadida a mano
		foreach ( array( 'tagline', 'title', 'text' ) as $campo ) {
			$actual = isset( $it[ $campo ] ) ? (string) $it[ $campo ] : '';
			$origen = isset( $base[ $i ][ $campo ] ) ? (string) $base[ $i ][ $campo ] : '';
			if ( $actual === $origen ) $items[ $i ][ $campo ] = $nuevo[ $i ][ $campo ];
		}
	}
	return $items;
}, 10, 2 );

/* Tarjeta del cotizador: título con el país y «Hasta» ya elegido. No se limita
 * a la portada: vale para cualquier página que se pinte con contexto de país. */
add_filter( 'grenvios_text_tokens', function ( $map ) {
	$pais = grenvios_hq_pais();
	if ( $pais === '' ) return $map;

	$campos = function_exists( 'grenvios_text_fields' ) ? grenvios_text_fields() : array();
	$sigue_igual = function ( $key ) use ( $map, $campos ) {
		if ( ! isset( $campos[ $key ] ) ) return false;
		return isset( $map[ '{{' . $key . '}}' ] ) && $map[ '{{' . $key . '}}' ] === $campos[ $key ]['default'];
	};

	if ( $sigue_igual( 'home_hq_title' ) ) {
		$map['{{home_hq_title}}'] = 'Cotiza tu <span>envío a ' . $pais . '</span>';
	}
	// El destino no se pide: ya se sabe. Queda editable por si el visitante
	// quiere cotizar otra ruta desde la misma página.
	if ( $sigue_igual( 'home_hq_destino_valor' ) ) {
		$map['{{home_hq_destino_valor}}'] = $pais;
	}
	return $map;
} );
