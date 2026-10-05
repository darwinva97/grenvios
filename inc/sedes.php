<?php
/**
 * Grenvíos — SEDES (operaciones por país de origen).
 *
 * Una SEDE es un país DESDE el que Grenvíos opera: tiene dirección propia,
 * teléfono local, WhatsApp y redes propias. No confundir con un DESTINO
 * (inc/destinos.php), que es un país AL que se envía. Un mismo país puede ser
 * las dos cosas, o solo una: se puede enviar a Cuba sin tener oficina en Cuba.
 *
 * CADA SEDE ES UN IDIOMA DE POLYLANG con locale regional (es_PE, es_ES, en_US).
 * De ahí salen gratis, sin una línea de código, la ruta con prefijo (/es/), el
 * hreflang diferenciado, el selector de la cabecera y la reescritura de los
 * enlaces internos (ver inc/i18n.php e inc/i18n-links.php). Este archivo añade
 * lo único que Polylang no puede saber: QUÉ DATOS cambian de una sede a otra.
 *
 * ALMACENAMIENTO
 * La sede maestra (Perú) guarda donde guardó siempre, SIN sufijo. Por eso un
 * sitio de una sola sede no nota absolutamente nada y no hay migración que
 * hacer. Las demás sedes cuelgan de un sufijo con su slug de idioma:
 *
 *     maestra    grenvios_biz_phone         theme_mod
 *     sede «es»  grenvios_biz_phone__es     theme_mod
 *     maestra    grenvios_globals           option
 *     sede «es»  grenvios_globals__es       option
 *
 * HERENCIA
 * Un campo vacío en una sede hereda el de la maestra. Así solo se rellena lo
 * que de verdad cambia (teléfono, dirección) y lo compartido (colores, logo,
 * copyright) se mantiene solo en todas las sedes.
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ══════════════════════════════════════
   1) IDENTIDAD DE LA SEDE ACTIVA
══════════════════════════════════════ */

/* Todas las sedes: slug => datos del idioma (nombre, locale, bandera, url…).
 *
 * Filtro `grenvios_sedes`: NO todo idioma es una sede. El inglés puede ser solo
 * la traducción del sitio de Perú y no una operación en EE. UU.; un idioma que
 * se quite de esta lista sigue teniendo su ruta y su hreflang, pero hereda
 * TODOS los datos de contacto de la maestra en vez de tener los suyos. La sede
 * maestra nunca se puede quitar. */
function grenvios_sedes() {
	$langs = function_exists( 'grenvios_i18n_langs' ) ? grenvios_i18n_langs() : array();
	$sedes = (array) apply_filters( 'grenvios_sedes', $langs );

	$master = function_exists( 'grenvios_i18n_default' ) ? grenvios_i18n_default() : '';
	if ( $master !== '' && ! isset( $sedes[ $master ] ) && isset( $langs[ $master ] ) ) {
		$sedes = array( $master => $langs[ $master ] ) + $sedes;
	}
	return $sedes;
}

/* ¿Hay más de una sede? (Sin Polylang o con un solo idioma: no.) */
function grenvios_sedes_active() {
	if ( ! function_exists( 'grenvios_i18n_active' ) || ! grenvios_i18n_active() ) return false;
	return count( grenvios_sedes() ) > 1;
}

/* Slug de la sede que se está viendo o editando. */
function grenvios_sede() {
	return function_exists( 'grenvios_i18n_current' ) ? grenvios_i18n_current() : '';
}

/* Slug de la sede maestra (Perú): la que guarda sin sufijo. */
function grenvios_sede_master() {
	return function_exists( 'grenvios_i18n_default' ) ? grenvios_i18n_default() : '';
}

/* ¿La sede indicada (o la activa) es la maestra? */
function grenvios_sede_is_master( $sede = null ) {
	$sede = $sede !== null ? $sede : grenvios_sede();
	return $sede === '' || $sede === grenvios_sede_master();
}

/* Nombre legible de una sede, para el Customizer y los avisos del admin. */
function grenvios_sede_name( $sede ) {
	$all = grenvios_sedes();
	return isset( $all[ $sede ] ) ? $all[ $sede ]['name'] : strtoupper( (string) $sede );
}

/* Locale de una sede (es_MX). Se lee de la propia lista de sedes y no de
 * Polylang, para que una sede añadida por el filtro `grenvios_sedes` también
 * tenga locale y no acabe emitiendo el país de la maestra en el schema. */
function grenvios_sede_locale( $sede ) {
	$all = grenvios_sedes();
	if ( isset( $all[ $sede ]['locale'] ) && $all[ $sede ]['locale'] !== '' ) return $all[ $sede ]['locale'];

	/* La lista de idiomas se cachea por petición. Si la ruta se acaba de crear
	 * en esta misma petición, todavía no está en esa caché y el locale saldría
	 * vacío: de ahí salían slugs como «envios-a-ar» en vez de «envios-a-argentina».
	 * Se pregunta a Polylang directamente antes de rendirse. */
	if ( function_exists( 'PLL' ) ) {
		$pll = PLL();
		if ( $pll && ! empty( $pll->model->languages ) ) {
			$l = $pll->model->languages->get( $sede );
			if ( $l && ! empty( $l->locale ) ) return $l->locale;
		}
	}
	return function_exists( 'grenvios_i18n_locale' ) ? grenvios_i18n_locale( $sede ) : (string) $sede;
}

/* Código ISO de país de una sede (es_MX -> MX). Cadena vacía si no se deduce. */
function grenvios_sede_country( $sede ) {
	if ( preg_match( '~^[a-z]{2}[_-]([A-Za-z]{2})$~', (string) grenvios_sede_locale( $sede ), $m ) ) {
		return strtoupper( $m[1] );
	}
	return '';
}

/* Sufijo de almacenamiento: cadena vacía para la maestra, «__es» para el resto.
 * Un idioma que no sea sede también devuelve cadena vacía: no tiene datos
 * propios, lee los de la maestra. */
function grenvios_sede_suffix( $sede = null ) {
	$sede = $sede !== null ? $sede : grenvios_sede();
	if ( grenvios_sede_is_master( $sede ) ) return '';
	$sedes = grenvios_sedes();
	if ( ! isset( $sedes[ $sede ] ) ) return '';
	return '__' . sanitize_key( $sede );
}

/* ══════════════════════════════════════
   1 bis) DE PAÍS DEL MENÚ A SEDE
   Los países del encabezado (inc/destinos.php) son DESTINOS. Cualquiera de
   ellos puede además convertirse en SEDE, y para eso hace falta saber su
   código ISO, su locale y la ciudad desde la que saldrían los envíos. Este
   registro hace esa traducción; lo que no esté aquí se pide a mano.
══════════════════════════════════════ */

/* slug del país => array( prefijo, locale, ciudad de origen, continente, nombre ).
 *
 * El continente va aquí y NO se lee de la lista de destinos a propósito: Perú
 * es la sede principal y justamente por eso no figura entre los destinos, así
 * que cualquier dato que dependiera de esa lista se quedaría vacío para la sede
 * más importante del sitio. */
function grenvios_sede_paises() {
	$map = array(
		'peru'           => array( 'pe', 'es_PE', 'Lima',             'América', 'Perú' ),
		'ecuador'        => array( 'ec', 'es_EC', 'Quito',            'América', 'Ecuador' ),
		'colombia'       => array( 'co', 'es_CO', 'Bogotá',           'América', 'Colombia' ),
		'chile'          => array( 'cl', 'es_CL', 'Santiago',         'América', 'Chile' ),
		'bolivia'        => array( 'bo', 'es_BO', 'La Paz',           'América', 'Bolivia' ),
		'argentina'      => array( 'ar', 'es_AR', 'Buenos Aires',     'América', 'Argentina' ),
		// es_US, no en_US: la ruta la lee un peruano que quiere enviar a EE. UU.,
		// así que el contenido va en español. Con en_US el sistema la trataría
		// como una traducción al inglés y le pondría hreflang, que es otra cosa.
		'estados-unidos' => array( 'us', 'es_US', 'Miami',            'América', 'Estados Unidos' ),
		'espana'         => array( 'es', 'es_ES', 'Madrid',           'Europa',  'España' ),
		'venezuela'      => array( 've', 'es_VE', 'Caracas',          'América', 'Venezuela' ),
		'cuba'           => array( 'cu', 'es_CU', 'La Habana',        'América', 'Cuba' ),
		// Países frecuentes que aún no están en el menú, por si se agregan.
		'mexico'         => array( 'mx', 'es_MX', 'Ciudad de México', 'América', 'México' ),
		'brasil'         => array( 'br', 'pt_BR', 'São Paulo',        'América', 'Brasil' ),
		'italia'         => array( 'it', 'it_IT', 'Milán',            'Europa',  'Italia' ),
		'canada'         => array( 'ca', 'en_CA', 'Toronto',          'América', 'Canadá' ),
		'uruguay'        => array( 'uy', 'es_UY', 'Montevideo',       'América', 'Uruguay' ),
		'paraguay'       => array( 'py', 'es_PY', 'Asunción',         'América', 'Paraguay' ),
		'costa-rica'     => array( 'cr', 'es_CR', 'San José',         'América', 'Costa Rica' ),
		'panama'         => array( 'pa', 'es_PA', 'Ciudad de Panamá', 'América', 'Panamá' ),
	);
	/* Filtro `grenvios_sede_paises`: para dar de alta un país que no esté en
	 * la lista sin tocar el tema. */
	return apply_filters( 'grenvios_sede_paises', $map );
}

/* Datos de sede propuestos para un país (array vacío si no se conoce). */
function grenvios_sede_propuesta( $destino_slug ) {
	$map = grenvios_sede_paises();
	if ( ! isset( $map[ $destino_slug ] ) ) return array();
	$row = array_pad( (array) $map[ $destino_slug ], 5, '' );
	return array(
		'slug'       => $row[0],
		'locale'     => $row[1],
		'ciudad'     => $row[2],
		'continente' => $row[3],
		'nombre'     => $row[4] !== '' ? $row[4] : ucwords( str_replace( '-', ' ', $destino_slug ) ),
	);
}

/* Estado de cada país del encabezado respecto a ser sede.
 * Devuelve una fila por país con: título, prefijo propuesto, locale, ciudad,
 * y el estado — 'maestra', 'sede', 'pendiente', 'ocupado' o 'desconocido'. */
function grenvios_sede_plan() {
	$destinos = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();
	$langs    = function_exists( 'grenvios_i18n_langs' ) ? grenvios_i18n_langs() : array();
	$master   = grenvios_sede_master();
	$rows     = array();

	// La sede principal (Perú) encabeza la lista: no es un destino de sí misma.
	if ( isset( $langs[ $master ] ) ) {
		$rows['peru'] = array(
			'destino' => 'peru',
			'titulo'  => 'Perú',
			'slug'    => $master,
			'locale'  => $langs[ $master ]['locale'],
			'ciudad'  => 'Lima',
			'ruta'    => '/',
			'estado'  => 'maestra',
		);
	}

	foreach ( $destinos as $dslug => $d ) {
		$p = grenvios_sede_propuesta( $dslug );
		if ( ! $p ) {
			$rows[ $dslug ] = array(
				'destino' => $dslug, 'titulo' => $d['title'], 'slug' => '', 'locale' => '',
				'ciudad'  => '', 'ruta' => '', 'estado' => 'desconocido',
			);
			continue;
		}

		$estado = 'pendiente';
		if ( isset( $langs[ $p['slug'] ] ) ) {
			// Ya existe un idioma con ese prefijo. ¿Es este país u otro?
			$estado = ( $langs[ $p['slug'] ]['locale'] === $p['locale'] ) ? 'sede' : 'ocupado';
		}

		$rows[ $dslug ] = array(
			'destino' => $dslug,
			'titulo'  => $d['title'],
			'slug'    => $p['slug'],
			'locale'  => $p['locale'],
			'ciudad'  => $p['ciudad'],
			'ruta'    => '/' . $p['slug'] . '/',
			'estado'  => $estado,
		);
	}
	return $rows;
}

/* ══════════════════════════════════════
   2) DATOS DE CONTACTO POR SEDE
   Enganchado al filtro de grenvios_biz(): la sede pisa a la maestra solo en
   los campos que tenga rellenos.
══════════════════════════════════════ */
add_filter( 'grenvios_biz', function ( $biz, $sede ) {
	if ( ! grenvios_sedes_active() || grenvios_sede_is_master( $sede ) ) return $biz;

	$suffix = grenvios_sede_suffix( $sede );
	foreach ( array_keys( grenvios_biz_fields() ) as $key ) {
		$val = get_theme_mod( 'grenvios_biz_' . $key . $suffix, '' );
		if ( trim( (string) $val ) !== '' ) $biz[ $key ] = $val;   // vacío = hereda
	}

	// El país del schema no es texto libre: se deduce del locale de la sede
	// (es_MX -> MX) salvo que se haya fijado a mano. Si no se pudiera deducir,
	// se deja el de la maestra, que es preferible a inventarlo.
	$manual = get_theme_mod( 'grenvios_biz_country' . $suffix, '' );
	if ( trim( (string) $manual ) !== '' ) {
		$biz['country'] = strtoupper( substr( trim( $manual ), 0, 2 ) );
	} else {
		$iso = grenvios_sede_country( $sede );
		if ( $iso !== '' ) $biz['country'] = $iso;
	}

	return $biz;
}, 10, 2 );

/* ══════════════════════════════════════
   3) AJUSTES GLOBALES POR SEDE
   Redes sociales, barra superior y pie. Misma regla: vacío = hereda.
══════════════════════════════════════ */

/* Nombre de la opción donde una sede guarda sus ajustes globales. */
function grenvios_globals_option( $sede = null ) {
	return 'grenvios_globals' . grenvios_sede_suffix( $sede );
}

add_filter( 'grenvios_globals', function ( $globals, $sede ) {
	if ( ! grenvios_sedes_active() || grenvios_sede_is_master( $sede ) ) return $globals;

	$own = get_option( grenvios_globals_option( $sede ), array() );
	if ( ! is_array( $own ) ) return $globals;

	foreach ( $own as $key => $val ) {
		if ( trim( (string) $val ) !== '' ) $globals[ $key ] = $val;   // vacío = hereda
	}
	return $globals;
}, 10, 2 );

/* ══════════════════════════════════════
   4) CUSTOMIZER: UNA SECCIÓN DE CONTACTO POR SEDE
   Solo aparecen si hay más de una sede. Los campos arrancan vacíos porque
   vacío significa «lo mismo que la sede principal».
══════════════════════════════════════ */
add_action( 'customize_register', function ( $wp_customize ) {
	if ( ! grenvios_sedes_active() ) return;

	$master     = grenvios_sede_master();
	$biz_master = array();
	$defaults   = grenvios_biz_defaults();
	foreach ( array_keys( grenvios_biz_fields() ) as $key ) {
		$biz_master[ $key ] = get_theme_mod( 'grenvios_biz_' . $key, $defaults[ $key ] );
	}

	$order = 0;
	foreach ( grenvios_sedes() as $sede => $data ) {
		if ( $sede === $master ) continue;   // la maestra ya tiene su sección
		$order++;
		$suffix  = grenvios_sede_suffix( $sede );
		$section = 'grenvios_sec_sede_' . sanitize_key( $sede );

		$wp_customize->add_section( $section, array(
			'title'       => 'Sede · ' . grenvios_sede_name( $sede ),
			'description' => 'Datos de la operación en ' . esc_html( grenvios_sede_name( $sede ) )
				. '. Deja un campo vacío para usar el mismo valor que la sede principal.',
			'panel'       => 'grenvios_panel',
			'priority'    => 200 + $order,
		) );

		foreach ( grenvios_biz_fields() as $key => $label ) {
			$sid  = 'grenvios_biz_' . $key . $suffix;
			$here = isset( $biz_master[ $key ] ) ? $biz_master[ $key ] : '';
			$wp_customize->add_setting( $sid, array(
				'default'           => '',
				'sanitize_callback' => 'sanitize_text_field',
				'transport'         => 'refresh',
			) );
			$wp_customize->add_control( $sid, array(
				'label'       => $label,
				'description' => $here !== '' ? 'Sede principal: ' . $here : '',
				'section'     => $section,
				'type'        => 'text',
			) );
		}

		// País ISO para el schema. Vacío = se deduce del locale de Polylang.
		$locale = grenvios_sede_locale( $sede );
		$wp_customize->add_setting( 'grenvios_biz_country' . $suffix, array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( 'grenvios_biz_country' . $suffix, array(
			'label'       => 'País (código ISO, ej. ES)',
			'description' => 'Vacío = se deduce del idioma de la sede (' . esc_html( $locale ) . ').',
			'section'     => $section,
			'type'        => 'text',
		) );
	}
}, 20 );

/* ══════════════════════════════════════
   5) ALTA DE SEDES EN POLYLANG
   Crear la sede = crear su idioma. De ahí salen la ruta, el hreflang y el
   selector sin que el tema haga nada más.
══════════════════════════════════════ */

/* ¿Se puede operar sobre los idiomas de Polylang ahora mismo? */
function grenvios_sede_pll_model() {
	if ( ! function_exists( 'PLL' ) ) return null;
	$pll = PLL();
	if ( ! $pll || empty( $pll->model ) || empty( $pll->model->languages ) ) return null;
	return $pll->model->languages;
}

/* Da de alta una sede. Devuelve true o un WP_Error con el motivo. */
function grenvios_sede_create( $slug, $locale, $name, $flag = '' ) {
	$langs = grenvios_sede_pll_model();
	if ( ! $langs ) return new WP_Error( 'no_pll', 'Polylang no está activo.' );

	$slug = sanitize_key( $slug );
	if ( $slug === '' || $locale === '' || $name === '' ) {
		return new WP_Error( 'incompleto', 'Faltan el prefijo, el locale o el nombre.' );
	}
	if ( $flag === '' ) $flag = $slug;   // las banderas de Polylang van por ISO

	$res = $langs->add( array(
		'name'   => $name,
		'slug'   => $slug,
		'locale' => $locale,
		'rtl'    => false,
		'flag'   => $flag,
	) );

	if ( is_wp_error( $res ) && $res->has_errors() ) return $res;
	flush_rewrite_rules( false );
	return true;
}

/* Renombra el prefijo de la sede principal (es -> pe).
 *
 * Hace falta porque la sede principal nació con el prefijo genérico del idioma
 * («es» de español) y ese es justo el prefijo que le toca a España como país.
 * Como Polylang oculta el prefijo del idioma por defecto, las URLs de Perú NO
 * cambian: sigue siendo la portada sin prefijo. Solo se libera el código. */
function grenvios_sede_rename_master( $new_slug ) {
	$langs = grenvios_sede_pll_model();
	if ( ! $langs ) return new WP_Error( 'no_pll', 'Polylang no está activo.' );

	$new_slug = sanitize_key( $new_slug );
	$master   = grenvios_sede_master();
	if ( $new_slug === '' || $new_slug === $master ) {
		return new WP_Error( 'sin_cambio', 'El prefijo nuevo es igual al actual.' );
	}

	$lang = $langs->get( $master );
	if ( ! $lang ) return new WP_Error( 'no_lang', 'No se encuentra la sede principal.' );

	$res = $langs->update( array(
		'lang_id' => $lang->term_id,
		'name'    => $lang->name,
		'slug'    => $new_slug,
		'locale'  => $lang->locale,
		'rtl'     => ! empty( $lang->is_rtl ),
		'flag'    => $new_slug,
	) );

	if ( is_wp_error( $res ) && $res->has_errors() ) return $res;
	flush_rewrite_rules( false );
	return true;
}
