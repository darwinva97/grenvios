<?php
/**
 * Grenvíos — SEDES · indexación y datos estructurados (Fases 3 y 4).
 *
 * LA COMPUERTA (Fase 3)
 * Al crear una sede su ruta responde de inmediato, pero sus páginas todavía no
 * existen: `/cl/` devuelve 200 con hreflang y contenido heredado de Perú. Eso
 * es una sede vacía indexable, y diez de ellas son un caso de manual de
 * *doorway pages*: Google penaliza esas páginas y arrastra al dominio entero.
 *
 * La defensa es la que este tema ya usa para las páginas combinadas
 * (inc/paginas-combinadas.php): la sede existe para el usuario desde el primer
 * día, pero NO entra al índice hasta que aporta algo que no está en ninguna
 * otra versión del sitio. Mientras esté incompleta:
 *
 *   · sus páginas salen con `noindex, follow`
 *   · sus URLs quedan fuera del sitemap
 *   · no se declara en los hreflang de las demás sedes
 *
 * Los tres a la vez. Un hreflang que apunta a una página `noindex` es una señal
 * contradictoria y Google descarta el grupo entero.
 *
 * SCHEMA POR SEDE (Fase 4)
 * Cada sede emite su propio `LocalBusiness` con su dirección, su teléfono y sus
 * redes. Sin esto, las diez sedes declararían ser el mismo negocio de Lima.
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ══════════════════════════════════════
   1) ¿ESTÁ LISTA LA SEDE?
══════════════════════════════════════ */

/* Qué le falta a una sede para poder indexarse. Array vacío = está lista.
 * La sede maestra siempre está lista: es el sitio original. */
function grenvios_sede_pendientes( $sede = null ) {
	$sede = $sede !== null ? $sede : grenvios_sede();
	if ( ! function_exists( 'grenvios_sedes_active' ) || ! grenvios_sedes_active() ) return array();
	if ( grenvios_sede_is_master( $sede ) ) return array();

	/* Una RUTA DE PAÍS no es una oficina: comparte el teléfono y la dirección de
	 * Perú a propósito, así que exigirle datos de contacto propios la dejaría en
	 * `noindex` para siempre. Su compuerta es otra —tener contenido propio— y
	 * vive en inc/paises-rutas.php, página a página. */
	if ( function_exists( 'grenvios_es_ruta_pais' ) && grenvios_es_ruta_pais( $sede ) ) return array();

	$falta  = array();
	$suffix = grenvios_sede_suffix( $sede );

	/* Una traducción tampoco es una oficina. Esta compuerta solo entra cuando
	 * ALGUIEN YA EMPEZÓ a darle datos de contacto propios: entonces sí hay que
	 * exigirle que los termine, porque una sede a medias enseña el teléfono de
	 * Lima junto a una dirección de Madrid. Si no tiene ninguno, comparte los de
	 * Perú a propósito y no hay nada que reprocharle. */
	$empezada = false;
	foreach ( array( 'phone', 'phone_tel', 'wa_number', 'address', 'city' ) as $k ) {
		if ( trim( (string) get_theme_mod( 'grenvios_biz_' . $k . $suffix, '' ) ) !== '' ) { $empezada = true; break; }
	}
	if ( ! $empezada ) return array();

	// Datos que NO pueden heredarse de Perú sin mentir: si una sede muestra el
	// teléfono y la dirección de Lima, no es una sede, es un clon.
	$propios = array(
		'phone'     => 'teléfono propio',
		'phone_tel' => 'teléfono para llamar',
		'wa_number' => 'WhatsApp propio',
		'address'   => 'dirección propia',
		'city'      => 'ciudad de origen',
	);
	foreach ( $propios as $key => $label ) {
		if ( trim( (string) get_theme_mod( 'grenvios_biz_' . $key . $suffix, '' ) ) === '' ) {
			$falta[] = $label;
		}
	}

	// Al menos un destino: una sede sin destinos no tiene nada que ofrecer.
	if ( function_exists( 'grenvios_sede_destinos_sel' ) ) {
		$sel = grenvios_sede_destinos_sel( $sede );
		if ( ! $sel ) {
			// Sin selección propia hereda «todos menos yo», lo cual es válido,
			// pero solo si de verdad hay destinos que ofrecer.
			$todos = function_exists( 'grenvios_destinos_fixed' ) ? count( grenvios_destinos_fixed() ) : 0;
			if ( $todos < 2 ) $falta[] = 'al menos un destino';
		}
	}

	/* Filtro `grenvios_sede_pendientes`: para exigir más (o menos) antes de
	 * dejar que una sede entre al índice. */
	return (array) apply_filters( 'grenvios_sede_pendientes', $falta, $sede );
}

/* ¿Puede esta sede entrar al índice de Google? */
function grenvios_sede_indexable( $sede = null ) {
	return count( grenvios_sede_pendientes( $sede ) ) === 0;
}

/* ══════════════════════════════════════
   2) NOINDEX MIENTRAS ESTÉ INCOMPLETA
══════════════════════════════════════ */
add_action( 'wp_head', function () {
	if ( is_admin() || grenvios_sede_indexable() ) return;

	$falta = grenvios_sede_pendientes();
	echo '<meta name="robots" content="noindex, follow">' . "\n";
	echo '<!-- Grenvíos: sede incompleta (falta ' . esc_html( implode( ', ', $falta ) )
		. '); fuera del índice hasta completarla. Los enlaces sí se siguen. -->' . "\n";
}, 3 );

/* ══════════════════════════════════════
   3) FUERA DEL SITEMAP
   No se le pide a Google que rastree lo que no queremos que indexe.
══════════════════════════════════════ */
add_filter( 'grenvios_sitemap_urls', function ( $urls ) {
	if ( ! function_exists( 'grenvios_sedes_active' ) || ! grenvios_sedes_active() ) return $urls;

	// Prefijos de las sedes que aún no están listas.
	$bloqueadas = array();
	foreach ( grenvios_sedes() as $slug => $l ) {
		if ( ! grenvios_sede_indexable( $slug ) ) $bloqueadas[] = '/' . $slug . '/';
	}
	if ( ! $bloqueadas ) return $urls;

	$home = untrailingslashit( home_url() );
	foreach ( $urls as $i => $u ) {
		if ( empty( $u['loc'] ) ) continue;
		$path = str_replace( $home, '', (string) $u['loc'] );
		foreach ( $bloqueadas as $pref ) {
			if ( strpos( $path, $pref ) === 0 ) { unset( $urls[ $i ] ); break; }
		}
	}
	return array_values( $urls );
}, 20 );

/* ══════════════════════════════════════
   4) FUERA DE LOS HREFLANG
   Declarar como alternativa una página que va con noindex es contradictorio:
   Google descarta el grupo entero de hreflang.

   Solo se toca el hreflang. La sede sigue estando en el SELECTOR del
   encabezado: existe para el usuario desde el primer día, simplemente no se
   le ofrece a Google todavía.
══════════════════════════════════════ */
add_filter( 'grenvios_i18n_hreflang_alternates', function ( $alts ) {
	foreach ( $alts as $slug => $a ) {
		if ( ! grenvios_sede_indexable( $slug ) ) unset( $alts[ $slug ] );
	}
	return $alts;
} );

/* ══════════════════════════════════════
   5) SCHEMA `LocalBusiness` POR SEDE
   El bloque de functions.php ya lee grenvios_biz(), que es sensible a la sede,
   así que el teléfono y la dirección ya salen bien. Faltaba lo demás: el
   identificador, la URL, la ciudad y las redes sociales.
══════════════════════════════════════ */
add_filter( 'grenvios_schema_organization', function ( $org ) {
	if ( ! function_exists( 'grenvios_sedes_active' ) || ! grenvios_sedes_active() ) return $org;

	$sede  = grenvios_sede();
	$biz   = grenvios_biz( $sede );
	$langs = grenvios_sedes();
	$url   = isset( $langs[ $sede ]['url'] ) ? $langs[ $sede ]['url'] : home_url( '/' );

	// Un identificador por sede: si todas comparten @id, Google las trata como
	// un único negocio y solo se queda con una dirección.
	$org['@id']  = untrailingslashit( $url ) . '/#organization-' . sanitize_key( $sede );
	$org['url']  = $url;
	$org['name'] = $biz['name'];

	if ( ! empty( $biz['city'] ) )    $org['address']['addressLocality'] = $biz['city'];
	if ( ! empty( $biz['country'] ) ) $org['address']['addressCountry']  = $biz['country'];
	if ( ! empty( $biz['address'] ) ) $org['address']['streetAddress']   = $biz['address'];

	// Redes de la sede, no las de Perú escritas a mano.
	if ( function_exists( 'grenvios_social_fields' ) ) {
		$same = array();
		foreach ( array_keys( grenvios_social_fields() ) as $k ) {
			$u = grenvios_g( 'social_' . $k, '' );
			if ( $u && strpos( $u, 'wa.me' ) === false ) $same[] = $u;
		}
		if ( $same ) $org['sameAs'] = array_values( array_unique( $same ) );
	}

	// El idioma en el que atiende esta sede.
	$org['availableLanguage'] = grenvios_sede_locale( $sede );

	return $org;
} );
