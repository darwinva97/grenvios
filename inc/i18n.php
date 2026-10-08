<?php
/**
 * Grenvíos — Núcleo multiidioma (Polylang).
 *
 * REGLA DE ORO DE ESTE TEMA: todo (plantillas, registro de textos, fondos por
 * página, destinos, FAQ) se resuelve por el SLUG EN ESPAÑOL. Las traducciones
 * son páginas independientes de WordPress con su PROPIO slug traducido
 * (/en/services/international-parcel-shipping/) para que Google las indexe como
 * páginas distintas. Para que ambas cosas convivan existe el "slug maestro":
 *
 *     página traducida  --(Polylang)-->  página en español  -->  slug maestro
 *
 * `grenvios_canonical_slug()` devuelve SIEMPRE el slug español, sin importar el
 * idioma que se esté viendo. El resto del tema sigue funcionando igual.
 *
 * NADA de idiomas fijos en el código: todo se lee de Polylang. Si mañana se
 * agrega alemán en Polylang, aparece solo en el selector, en el hreflang, en el
 * sitemap y en la pantalla de Traducciones.
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* Meta donde cada traducción guarda su slug maestro (respaldo si Polylang se
 * desactiva o si se rompe el vínculo de traducción). */
if ( ! defined( 'GRENVIOS_MASTER_META' ) ) {
	define( 'GRENVIOS_MASTER_META', '_grenvios_master_slug' );
}

/* ══════════════════════════════════════
   1) ESTADO DE POLYLANG
══════════════════════════════════════ */

/* ¿Polylang está activo y configurado? */
function grenvios_i18n_active() {
	static $on = null;
	if ( $on !== null ) return $on;
	$on = function_exists( 'pll_languages_list' ) && count( (array) pll_languages_list() ) > 0;
	$on = (bool) apply_filters( 'grenvios_i18n_enabled', $on );
	return $on;
}

/* Idiomas configurados en Polylang: slug => [slug, locale, name, flag, default, url].
 * Si Polylang no está, devuelve un único idioma (el del sitio) para que todo el
 * tema siga funcionando exactamente igual que antes. */
function grenvios_i18n_langs() {
	static $cache = null;
	if ( $cache !== null ) return $cache;

	$cache = array();
	if ( ! grenvios_i18n_active() ) {
		$loc = get_locale();
		$sl  = substr( $loc, 0, 2 );
		$cache[ $sl ] = array(
			'slug' => $sl, 'locale' => $loc, 'name' => strtoupper( $sl ),
			'flag' => '', 'default' => true, 'url' => home_url( '/' ),
		);
		return $cache;
	}

	$default = pll_default_language();
	foreach ( pll_languages_list( array( 'fields' => '' ) ) as $l ) {
		$slug = is_object( $l ) ? $l->slug : (string) $l;
		$cache[ $slug ] = array(
			'slug'    => $slug,
			'locale'  => ( is_object( $l ) && ! empty( $l->locale ) ) ? $l->locale : $slug,
			'name'    => ( is_object( $l ) && ! empty( $l->name ) ) ? $l->name : strtoupper( $slug ),
			/* Bandera SVG del tema (nítida); la de Polylang (PNG 16×11) solo de respaldo. */
			'flag'    => ( function_exists( 'grenvios_bandera_url' ) && is_object( $l ) && grenvios_bandera_url( ! empty( $l->flag_code ) ? $l->flag_code : $slug ) !== '' )
				? grenvios_bandera_url( ! empty( $l->flag_code ) ? $l->flag_code : $slug )
				: ( ( is_object( $l ) && ! empty( $l->flag_url ) ) ? $l->flag_url : '' ),
			'default' => ( $slug === $default ),
			'url'     => function_exists( 'pll_home_url' ) ? pll_home_url( $slug ) : home_url( '/' ),
		);
	}
	return $cache;
}

/* Slug del idioma por defecto (el idioma "maestro": español). */
function grenvios_i18n_default() {
	if ( grenvios_i18n_active() ) return pll_default_language();
	return substr( get_locale(), 0, 2 );
}

/* Slug del idioma que se está viendo/editando ahora. */
function grenvios_i18n_current() {
	if ( grenvios_i18n_active() ) {
		$l = pll_current_language();
		if ( $l ) return $l;
	}
	return grenvios_i18n_default();
}

/* ¿Estamos en el idioma maestro? (Entonces todo se comporta como el tema original.) */
function grenvios_i18n_is_default( $lang = null ) {
	$lang = $lang ? $lang : grenvios_i18n_current();
	return $lang === grenvios_i18n_default();
}

/* Nombre legible del idioma (para el prompt de traducción y para el admin). */
function grenvios_i18n_lang_name( $lang ) {
	$langs = grenvios_i18n_langs();
	return isset( $langs[ $lang ] ) ? $langs[ $lang ]['name'] : strtoupper( $lang );
}

/* Locale completo (es_ES, en_US…) para og:locale. */
function grenvios_i18n_locale( $lang ) {
	$langs = grenvios_i18n_langs();
	return isset( $langs[ $lang ] ) ? $langs[ $lang ]['locale'] : $lang;
}

/* Código hreflang (es, en, pt-BR…) derivado del locale de Polylang.
 * Si hay UN solo idioma con ese ISO se usa el código corto (es), que apunta a
 * todos los países que hablan ese idioma. Si hay varias variantes del mismo
 * idioma (es_ES y es_MX) se usa el código regional para que Google las
 * distinga (es-ES / es-MX). */
function grenvios_i18n_hreflang( $lang ) {
	$loc   = str_replace( '_', '-', grenvios_i18n_locale( $lang ) );
	$parts = explode( '-', $loc );
	if ( count( $parts ) < 2 ) return strtolower( $loc );
	$iso = strtolower( $parts[0] );
	$reg = strtoupper( $parts[1] );
	$same = 0;
	foreach ( grenvios_i18n_langs() as $l ) {
		if ( strtolower( substr( str_replace( '_', '-', $l['locale'] ), 0, 2 ) ) === $iso ) $same++;
	}
	return $same > 1 ? $iso . '-' . $reg : $iso;
}

/* ══════════════════════════════════════
   2) SLUG MAESTRO (la pieza clave)
══════════════════════════════════════ */

/* ID de la versión en español (maestra) de cualquier página. */
function grenvios_i18n_master_id( $post_id ) {
	$post_id = (int) $post_id;
	if ( ! $post_id ) return 0;
	if ( ! grenvios_i18n_active() ) return $post_id;
	$def = grenvios_i18n_default();
	if ( function_exists( 'pll_get_post_language' ) && pll_get_post_language( $post_id ) === $def ) return $post_id;
	$m = function_exists( 'pll_get_post' ) ? (int) pll_get_post( $post_id, $def ) : 0;
	return $m ? $m : $post_id;
}

/* ¿Es la copia en una ruta del hub de destinos («Envíos internacionales»)? */
function grenvios_es_hub_espejo( $post_id ) {
	static $hub = null;
	if ( $hub === null ) { $h = get_page_by_path( 'destinos' ); $hub = $h ? (int) $h->ID : 0; }
	return $hub && function_exists( 'grenvios_espejo_es' ) && grenvios_espejo_es( $post_id )
		&& (int) get_post_meta( (int) $post_id, '_grenvios_espejo', true ) === $hub;
}

/* Traducción de una página a un idioma (0 si aún no existe). */
function grenvios_i18n_translation_id( $post_id, $lang ) {
	if ( ! grenvios_i18n_active() || ! $post_id ) return 0;
	$t = (int) pll_get_post( (int) $post_id, $lang );
	/* Las páginas espejo (inc/paises-espejos.php) están enlazadas como traducción
	 * para que la ruta quede completa, pero no deben recibir enlaces: el menú,
	 * los enlaces del contenido y el selector de país siguen llevando a la
	 * página real del destino. Para el tema, un espejo no es una traducción. */
	/* Excepción: el hub «Envíos internacionales» de cada ruta SÍ recibe enlaces,
	 * para que el menú no saque al visitante de su país (canónica a la de Perú y
	 * fuera del sitemap, como todo espejo). */
	if ( $t && function_exists( 'grenvios_espejo_es' ) && grenvios_espejo_es( $t ) && ! grenvios_es_hub_espejo( $t ) ) return 0;
	return $t;
}

/* SLUG MAESTRO (en español) de una página, sea cual sea su idioma.
 * Es lo que consumen plantillas, registro de textos, destinos, FAQ y fondos. */
function grenvios_canonical_slug( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : (int) get_queried_object_id();
	if ( ! $post_id ) return '';

	static $cache = array();
	if ( isset( $cache[ $post_id ] ) ) return $cache[ $post_id ];

	// 1) Meta grabada al traducir: sobrevive a cualquier cambio de slug.
	$meta = (string) get_post_meta( $post_id, GRENVIOS_MASTER_META, true );
	if ( $meta !== '' ) return $cache[ $post_id ] = $meta;

	// 2) Vínculo de traducción de Polylang -> post_name de la página española.
	$master = grenvios_i18n_master_id( $post_id );
	$slug   = (string) get_post_field( 'post_name', $master );

	// 3) Sin traducción: su propio slug.
	if ( $slug === '' ) $slug = (string) get_post_field( 'post_name', $post_id );

	return $cache[ $post_id ] = $slug;
}

/* Deja grabado el slug maestro en una traducción (lo usa el traductor). */
function grenvios_i18n_stamp_master( $translation_id, $master_slug ) {
	update_post_meta( (int) $translation_id, GRENVIOS_MASTER_META, sanitize_title( $master_slug ) );
}

/* ══════════════════════════════════════
   3) INTEGRACIÓN CON POLYLANG
══════════════════════════════════════ */

/* Al crear una traducción desde el propio Polylang (botón "+"), copia el
 * contenido del editor de página (metas grenvios_*, repeaters y slug maestro)
 * para que la traducción arranque con el contenido real y no en blanco.
 * $sync = true significa "sincronización permanente": ahí NO copiamos nada,
 * porque cada idioma debe poder editar su propio texto sin pisar al español. */
add_filter( 'pll_copy_post_metas', function ( $keys, $sync, $from ) {
	if ( $sync ) return $keys;
	$from = (int) $from;
	if ( ! $from ) return $keys;
	foreach ( get_post_meta( $from ) as $k => $v ) {
		if ( strpos( $k, 'grenvios_' ) === 0 ) $keys[] = $k;
	}
	$keys[] = GRENVIOS_MASTER_META;
	return array_values( array_unique( $keys ) );
}, 10, 3 );

/* Al guardar la página maestra, el slug maestro de sus traducciones se
 * mantiene al día (si el cliente cambia el slug español). */
add_action( 'save_post_page', function ( $post_id, $post ) {
	if ( wp_is_post_revision( $post_id ) || ! grenvios_i18n_active() ) return;
	if ( pll_get_post_language( $post_id ) !== grenvios_i18n_default() ) return;
	foreach ( grenvios_i18n_langs() as $slug => $l ) {
		if ( $l['default'] ) continue;
		$tid = grenvios_i18n_translation_id( $post_id, $slug );
		if ( $tid ) grenvios_i18n_stamp_master( $tid, $post->post_name );
	}
}, 20, 2 );

/* ══════════════════════════════════════
   4) SELECTOR DE IDIOMA (token LANGSWITCHER en header.html)
══════════════════════════════════════ */

/* Enlaces a la MISMA página en los demás idiomas. Solo se listan los idiomas
 * con la traducción publicada: nunca enlazar a un 404 ni redirigir a la
 * portada, porque eso genera hreflang rotos y Google los descarta. */
function grenvios_i18n_alternates() {
	$out = array();
	if ( ! grenvios_i18n_active() ) return $out;

	$pid = (int) get_queried_object_id();

	// La portada se resuelve SIEMPRE como portada, aunque sea una página
	// estática (que también es `is_singular()`). Si no, en un sitio con página
	// de inicio fija y sin traducir, «/» no declaraba ningún alternate mientras
	// que «/cl/» sí los declaraba: un hreflang no recíproco que Google descarta.
	$es_portada = is_front_page() || is_home();

	foreach ( grenvios_i18n_langs() as $slug => $l ) {
		$url = '';
		if ( $es_portada ) {
			$url = $l['url'];
		} elseif ( is_singular() && $pid ) {
			$tid = grenvios_i18n_translation_id( $pid, $slug );
			if ( $tid && get_post_status( $tid ) === 'publish' ) $url = get_permalink( $tid );
		}
		if ( ! $url ) continue;
		$out[ $slug ] = array(
			'url'      => $url,
			'name'     => $l['name'],
			'locale'   => $l['locale'],
			'hreflang' => grenvios_i18n_hreflang( $slug ),
			'flag'     => $l['flag'],
			'default'  => $l['default'],
			'current'  => ( $slug === grenvios_i18n_current() ),
		);
	}
	return $out;
}

/* Rutas para el SELECTOR visible, que no es lo mismo que los alternates de SEO.
 *
 * `grenvios_i18n_alternates()` descarta una ruta cuando la página actual no tiene
 * equivalente en ella, y para `hreflang` eso es obligatorio: no se puede declarar
 * como alternativa una URL que no existe.
 *
 * Pero el desplegable del encabezado es navegación, no SEO. Con la regla estricta,
 * una página que solo existe en el sitio principal —«Artículos por país», por
 * ejemplo— se quedaba SIN selector, y el visitante perdía de golpe la forma de
 * cambiar de país. Aquí se rellenan esos huecos con la portada de cada ruta: no es
 * la página equivalente, pero es a donde tiene sentido llevarlo. */
function grenvios_i18n_switcher_items() {
	$items = grenvios_i18n_alternates();

	foreach ( grenvios_i18n_langs() as $slug => $l ) {
		if ( isset( $items[ $slug ] ) ) continue;
		if ( empty( $l['url'] ) ) continue;
		$items[ $slug ] = array(
			'url'      => $l['url'],
			'name'     => $l['name'],
			'locale'   => $l['locale'],
			'hreflang' => grenvios_i18n_hreflang( $slug ),
			'flag'     => $l['flag'],
			'default'  => $l['default'],
			'current'  => ( $slug === grenvios_i18n_current() ),
			'portada'  => true,        // no hay equivalente: va a la portada del país
		);
	}

	// Se conserva el orden de los idiomas, no el de los alternates encontrados.
	$orden = array();
	foreach ( grenvios_i18n_langs() as $slug => $_l ) {
		if ( isset( $items[ $slug ] ) ) $orden[ $slug ] = $items[ $slug ];
	}
	return $orden;
}

/* HTML del selector para el encabezado (mismo patrón de menú del tema). */
function grenvios_i18n_switcher_html() {
	$alts = grenvios_i18n_switcher_items();
	if ( count( $alts ) < 2 ) return '';   // un solo idioma: no ensuciar el header

	$cur = grenvios_i18n_current();
	$me  = isset( $alts[ $cur ] ) ? $alts[ $cur ] : reset( $alts );

	/* El nombre que se muestra es el del PAÍS, no el de la «lengua» de Polylang.
	 *
	 * Sin esto la ruta principal aparecía como «Español» en un desplegable donde
	 * el resto son Cuba, Chile o Argentina: el visitante no entiende que
	 * «Español» significa «Perú», que es a lo que vuelve si lo pulsa. */
	$nombre = function ( $slug, $respaldo ) {
		return function_exists( 'grenvios_col_pais_nombre' )
			? grenvios_col_pais_nombre( $slug )
			: $respaldo;
	};

	$h  = '<li class="menu-item-has-children grenvios-lang-switcher">';
	$h .= '<a href="#" aria-label="' . esc_attr( grenvios_t( 'Cambiar país de destino' ) ) . '">';
	if ( ! empty( $me['flag'] ) ) $h .= '<img class="gr-lang-flag" src="' . esc_url( $me['flag'] ) . '" alt="" width="20" height="15"> ';
	// En su propio <span> para que el CSS pueda ocultarlo cuando el ancho aprieta
	// y quede solo la bandera (ver .gr-lang-nombre en assets/css/main.css).
	/* En la cabecera, el nombre corto: así «Estados Unidos» ocupa lo mismo que
	 * «Perú» o «Bolivia» y la cabecera es igual en todos los países. En el
	 * desplegable va el nombre completo. */
	$corto = array( 'Estados Unidos' => 'EE. UU.' );
	$visible = $nombre( $cur, strtoupper( $cur ) );
	if ( isset( $corto[ $visible ] ) ) $visible = $corto[ $visible ];
	$h .= '<span class="gr-lang-nombre">' . esc_html( $visible ) . '</span>'
		. '</a><ul class="sub-menu">';
	/* Un país, una fila: la ruta principal y la ruta «pe» de Polylang se llaman
	 * las dos «Perú» y salían repetidas. Se queda la primera, o la actual. */
	$vistos = array();
	foreach ( $alts as $slug => $a ) {
		$n = $nombre( $slug, $a['name'] );
		if ( isset( $vistos[ $n ] ) && ! $a['current'] ) unset( $alts[ $slug ] );
		elseif ( isset( $vistos[ $n ] ) ) unset( $alts[ $vistos[ $n ] ] );
		if ( isset( $alts[ $slug ] ) ) $vistos[ $n ] = $slug;
	}
	foreach ( $alts as $slug => $a ) {
		$cls = $a['current'] ? ' class="current-lang"' : '';
		$h  .= '<li' . $cls . '><a href="' . esc_url( $a['url'] ) . '" hreflang="' . esc_attr( $a['hreflang'] ) . '" lang="' . esc_attr( $a['hreflang'] ) . '">';
		if ( ! empty( $a['flag'] ) ) $h .= '<img class="gr-lang-flag" src="' . esc_url( $a['flag'] ) . '" alt="" width="20" height="15" loading="lazy"> ';
		$h  .= esc_html( $nombre( $slug, $a['name'] ) ) . '</a></li>';
	}
	$h .= '</ul></li>';
	return $h;
}

/* Sustituye el token LANGSWITCHER del header. Si la plantilla aún no lo tiene,
 * el selector se inyecta como último ítem del menú principal. */
add_filter( 'grenvios_partial_html', function ( $html, $name ) {
	if ( strpos( $html, 'LANGSWITCHER' ) !== false ) {
		$sw   = grenvios_i18n_switcher_html();
		$html = str_replace( 'LANGSWITCHER', $sw, $html );
		// Con un solo país el selector no se pinta: se quita también su envoltorio
		// para no dejar un <ul> vacío ocupando hueco en el encabezado.
		if ( $sw === '' ) {
			$html = str_replace( '<ul class="grenvios-lang-menu"></ul>', '', $html );
		}
		return $html;
	}
	if ( $name !== 'header' ) return $html;
	$sw = grenvios_i18n_switcher_html();
	if ( ! $sw ) return $html;

	/* Va en el bloque de la derecha, con la lupa y el botón Cotizar, NO dentro
	 * del menú.
	 *
	 * Como un ítem más del menú competía por el mismo espacio que los enlaces:
	 * con un nombre largo —«Estados Unidos»— la fila se partía en dos y el
	 * selector caía debajo, y al forzarla a una sola línea se montaba encima de
	 * la lupa. Y conceptualmente tampoco era un enlace de navegación: es un
	 * control, como la búsqueda. Ahí tiene su propio espacio y ningún nombre de
	 * país, por largo que sea, descuadra el encabezado. */
	if ( preg_match( '/<div[^>]*class="[^"]*menu-right-item[^"]*"[^>]*>/i', $html, $m, PREG_OFFSET_CAPTURE ) ) {
		$pos = $m[0][1] + strlen( $m[0][0] );
		return substr( $html, 0, $pos )
			. '<ul class="grenvios-lang-menu">' . $sw . '</ul>'
			. substr( $html, $pos );
	}

	// Respaldo: si la plantilla no tuviera ese bloque, al final del menú.
	if ( preg_match( '/<ul[^>]*class="[^"]*(?:main-menu|nav-menu|menu)[^"]*"[^>]*>/i', $html, $m, PREG_OFFSET_CAPTURE ) ) {
		$open  = $m[0][1] + strlen( $m[0][0] );
		$close = strpos( $html, '</ul>', $open );
		if ( $close !== false ) return substr( $html, 0, $close ) . $sw . substr( $html, $close );
	}
	return $html;
}, 10, 2 );

/* Atributo lang correcto en <html>. */
add_filter( 'language_attributes', function ( $out ) {
	if ( ! grenvios_i18n_active() ) return $out;
	$hl = grenvios_i18n_hreflang( grenvios_i18n_current() );
	return preg_replace( '/lang="[^"]*"/', 'lang="' . esc_attr( $hl ) . '"', $out );
}, 20 );

/* ══════════════════════════════════════
   5) EL MULTIIDIOMA ES OPCIONAL
   Sin Polylang instalado, TODO este subsistema queda inerte: el sitio funciona
   como una web monolingüe en español y no aparece ningún aviso ni pantalla
   extra. Si algún día se instala Polylang y se añade un idioma, el multiidioma
   se activa solo, sin tocar código.

   Para desactivarlo aunque Polylang esté instalado (por ejemplo si se usa el
   plugin para otra cosa), basta con:

       add_filter( 'grenvios_i18n_enabled', '__return_false' );
══════════════════════════════════════ */
