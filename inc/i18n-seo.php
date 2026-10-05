<?php
/**
 * Grenvíos — SEO internacional.
 *
 * Qué resuelve este archivo (y por qué importa para posicionar en varios países):
 *
 *  1. hreflang recíproco + x-default en cada página. Sin esto Google trata las
 *     versiones como contenido duplicado y elige una sola.
 *  2. canonical AUTOREFERENTE por idioma: /en/services/ se apunta a sí misma,
 *     nunca a la versión española (ese es el error clásico que borra del índice
 *     todas las traducciones).
 *  3. og:locale y og:locale:alternate para redes.
 *  4. Sitemap con todas las versiones y sus alternates (functions.php llama a
 *     `grenvios_i18n_sitemap_alternates()`).
 *  5. Schema.org y textos del banner/migas traducidos.
 *
 * Aviso deliberado sobre "SEO por país": hreflang segmenta por IDIOMA (y como
 * mucho por variante regional), no por país de destino del envío. El tráfico de
 * "envíos a Ecuador" NO se gana con una versión es-EC del sitio —eso sería
 * contenido duplicado—, se gana con las páginas /destinos/<pais>/ que el tema ya
 * tiene. Las traducciones sirven para captar a quien BUSCA EN OTRO IDIOMA
 * (inglés, portugués, italiano…), que es un público distinto, no el mismo.
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ══════════════════════════════════════
   1) HREFLANG + OG:LOCALE
══════════════════════════════════════ */
add_action( 'wp_head', function () {
	if ( ! grenvios_i18n_active() ) return;

	/* Filtro `grenvios_i18n_hreflang_alternates`: separado del selector de idioma
	 * a propósito. inc/sedes-seo.php quita de aquí las sedes que aún van con
	 * `noindex` —declarar como alternativa una página fuera del índice invalida
	 * el grupo entero de hreflang—, pero esas sedes siguen apareciendo en el
	 * selector para que el usuario pueda llegar a ellas. */
	$alts = (array) apply_filters( 'grenvios_i18n_hreflang_alternates', grenvios_i18n_alternates() );
	$cur  = grenvios_i18n_current();

	/* og:locale se emite SIEMPRE, aunque no haya alternativas que declarar. Una
	 * ruta de país no tiene hreflang a propósito, pero sigue necesitando decirle
	 * a las redes en qué idioma está escrita.
	 * Filtro `grenvios_og_locale`: inc/paises-rutas.php lo usa para que una ruta
	 * de país declare el idioma real (es_PE) y no el del país de destino. */
	printf( '<meta property="og:locale" content="%s" />' . "\n",
		esc_attr( apply_filters( 'grenvios_og_locale', grenvios_i18n_locale( $cur ) ) ) );

	if ( count( $alts ) < 2 ) return;   // una sola versión: no hay nada que declarar

	echo "\n<!-- Grenvíos · SEO internacional -->\n";
	foreach ( $alts as $slug => $a ) {
		printf( '<link rel="alternate" hreflang="%s" href="%s" />' . "\n",
			esc_attr( $a['hreflang'] ), esc_url( $a['url'] ) );
	}
	// x-default: a dónde mandar al usuario cuyo idioma no tenemos. Siempre el
	// idioma maestro (español), que es donde está el contenido más completo.
	foreach ( $alts as $a ) {
		if ( ! empty( $a['default'] ) ) {
			printf( '<link rel="alternate" hreflang="x-default" href="%s" />' . "\n", esc_url( $a['url'] ) );
			break;
		}
	}

	// OpenGraph: los demás idiomas en los que existe esta página
	foreach ( $alts as $slug => $a ) {
		if ( $slug === $cur ) continue;
		printf( '<meta property="og:locale:alternate" content="%s" />' . "\n", esc_attr( $a['locale'] ) );
	}
}, 4 );   // antes del bloque SEO del tema (prioridad 10)

/* El tema ya imprime <link rel="canonical"> con get_permalink(), que en una
 * página traducida devuelve SU propia URL. Solo hay que evitar que Polylang
 * añada un segundo canonical y quede duplicado. */
add_filter( 'pll_check_canonical_url', '__return_false' );

/* Polylang emite sus propios hreflang; como aquí los generamos con x-default y
 * saltando las traducciones no publicadas, se desactivan los suyos. */
add_filter( 'pll_rel_hreflang_attributes', '__return_empty_array' );

/* Y además se quita su acción del `wp_head`.
 *
 * Con el filtro de arriba su salida ya era vacía, pero el trabajo se hacía
 * igual: medido con ?gr_perf=1, PLL_Frontend_Filters_Links::wp_head costaba
 * 0,067 s por visita —el segundo gasto más caro de toda la cabecera— para no
 * imprimir nada. Los hreflang de este sitio los emite la función de arriba. */
add_action( 'wp', function () {
	global $wp_filter;
	if ( empty( $wp_filter['wp_head'] ) ) return;
	foreach ( $wp_filter['wp_head']->callbacks as $prio => $cbs ) {
		foreach ( $cbs as $cb ) {
			$f = $cb['function'];
			if ( ! is_array( $f ) || ! is_object( $f[0] ) ) continue;
			if ( $f[1] !== 'wp_head' || ! ( $f[0] instanceof PLL_Frontend_Filters_Links ) ) continue;
			remove_action( 'wp_head', $f, $prio );
		}
	}
}, 9 );

/* ══════════════════════════════════════
   2) SITEMAP: alternates por URL
   Recibe la lista de URLs que arma functions.php y le añade, a cada una, las
   demás versiones de idioma.
══════════════════════════════════════ */
function grenvios_i18n_sitemap_alternates( $urls ) {
	if ( ! grenvios_i18n_active() ) return $urls;

	$langs = grenvios_i18n_langs();
	if ( count( $langs ) < 2 ) return $urls;

	// Índice: URL -> ID de página, para saber a qué grupo de traducciones
	// pertenece cada entrada del sitemap.
	$by_url = array();
	foreach ( get_posts( array( 'post_type' => array( 'page', 'post' ), 'post_status' => 'publish', 'numberposts' => -1, 'lang' => '' ) ) as $p ) {
		$by_url[ untrailingslashit( get_permalink( $p ) ) ] = (int) $p->ID;
	}
	foreach ( $langs as $l ) {
		$by_url[ untrailingslashit( $l['url'] ) ] = 'home:' . $l['slug'];
	}

	foreach ( $urls as $i => $u ) {
		$key = untrailingslashit( $u['loc'] );
		if ( ! isset( $by_url[ $key ] ) ) continue;
		$ref = $by_url[ $key ];
		$alt = array();

		if ( is_string( $ref ) && strpos( $ref, 'home:' ) === 0 ) {
			$suya = substr( $ref, strlen( 'home:' ) );
			if ( function_exists( 'grenvios_es_ruta_pais' ) && grenvios_es_ruta_pais( $suya ) ) continue;
			foreach ( $langs as $l ) {
				if ( function_exists( 'grenvios_es_ruta_pais' ) && grenvios_es_ruta_pais( $l['slug'] ) ) continue;
				$alt[ grenvios_i18n_hreflang( $l['slug'] ) ] = $l['url'];
			}
		} else {
			$tr = function_exists( 'pll_get_post_translations' ) ? pll_get_post_translations( $ref ) : array();

			/* Las RUTAS DE PAÍS no son versiones alternativas: son páginas
			 * distintas sobre destinos distintos, escritas para el mismo
			 * lector. inc/paises-rutas.php ya las excluye del hreflang del
			 * HTML; el sitemap las seguía declarando, así que el sitio enviaba
			 * dos señales contrarias y, además, grupos no recíprocos —que
			 * Google descarta enteros—. Aquí se aplica la misma regla. */
			$propia = function_exists( 'pll_get_post_language' ) ? (string) pll_get_post_language( $ref ) : '';
			if ( function_exists( 'grenvios_es_ruta_pais' ) && $propia !== '' && grenvios_es_ruta_pais( $propia ) ) continue;

			foreach ( $tr as $lg => $pid ) {
				if ( get_post_status( $pid ) !== 'publish' ) continue;
				if ( function_exists( 'grenvios_es_ruta_pais' ) && grenvios_es_ruta_pais( $lg ) ) continue;
				$alt[ grenvios_i18n_hreflang( $lg ) ] = get_permalink( $pid );
			}
		}
		if ( count( $alt ) < 2 ) continue;

		// x-default → idioma maestro
		$def = grenvios_i18n_hreflang( grenvios_i18n_default() );
		if ( isset( $alt[ $def ] ) ) $alt['x-default'] = $alt[ $def ];

		$urls[ $i ]['alt'] = $alt;
	}
	return $urls;
}

/* ══════════════════════════════════════
   3) TEXTOS SEO GENERADOS POR EL TEMA
══════════════════════════════════════ */

/* NOTA: aquí había dos add_filter() sobre `grenvios_breadcrumb_label` y
 * `grenvios_schema_description` que NUNCA se ejecutaban, porque nadie llamaba al
 * apply_filters() correspondiente. Las migas parecían traducidas y no lo estaban.
 *
 * Ahora cada texto se traduce en su origen, que es más directo y no depende de
 * un enganche invisible:
 *   · migas de pan  → grenvios_t() en logisko_breadcrumbs() (functions.php)
 *   · BlogPosting   → usa el título y el extracto de la entrada YA traducida
 *   · Organization  → la marca no se traduce; la descripción sale del registro
 *                     de páginas, que el traductor rellena por idioma.
 *
 * Los títulos de página NO pasan por el diccionario a propósito: Polylang ya
 * devuelve el título de la traducción, y volver a traducirlo daría un texto
 * distinto al H1 de la propia página. */

/* ══════════════════════════════════════
   4) REDIRECCIONES SEGURAS
   Si alguien entra a /en/<slug-en-español>/ (por ejemplo un enlace viejo o un
   copiar-pegar), se redirige 301 a la URL traducida correcta en vez de servir
   un 404. Evita perder enlaces y rastreo.
══════════════════════════════════════ */
add_action( 'template_redirect', function () {
	if ( ! is_404() || ! grenvios_i18n_active() ) return;

	$path = trim( (string) parse_url( add_query_arg( null, null ), PHP_URL_PATH ), '/' );
	if ( $path === '' ) return;
	$parts = explode( '/', $path );

	$langs = grenvios_i18n_langs();
	if ( ! isset( $langs[ $parts[0] ] ) ) return;      // no empieza por un idioma
	$lang = array_shift( $parts );
	$slug = end( $parts );
	if ( ! $slug ) return;

	// ¿Existe una página española con ese slug? Entonces buscamos su traducción.
	$q = get_posts( array(
		'post_type' => 'page', 'name' => $slug, 'post_status' => 'publish',
		'numberposts' => 1, 'lang' => grenvios_i18n_default(),
	) );
	if ( empty( $q ) ) return;

	$tid = grenvios_i18n_translation_id( $q[0]->ID, $lang );
	if ( ! $tid || get_post_status( $tid ) !== 'publish' ) return;

	wp_safe_redirect( get_permalink( $tid ), 301 );
	exit;
}, 5 );

/* ══════════════════════════════════════
   6) COMPROBACIÓN DE SALUD (se muestra en la pantalla Traducciones)
   Detecta los fallos que de verdad hunden un SEO internacional.
══════════════════════════════════════ */
function grenvios_i18n_healthcheck() {
	$issues = array();

	if ( ! grenvios_i18n_active() ) {
		$issues[] = array( 'error', 'Polylang no está activo: no hay traducciones, ni hreflang, ni sitemap multiidioma.' );
		return $issues;
	}
	$langs = grenvios_i18n_langs();
	if ( count( $langs ) < 2 ) {
		$issues[] = array( 'warn', 'Solo hay un idioma configurado en Polylang. Agrega los idiomas en Idiomas → Idiomas.' );
	}

	// Permalinks amigables: sin ellos no hay slugs traducidos que indexar.
	if ( ! get_option( 'permalink_structure' ) ) {
		$issues[] = array( 'error', 'Los enlaces permanentes están en "Simple" (?p=123). Cámbialos a "Nombre de la entrada" en Ajustes → Enlaces permanentes: sin eso no existen slugs traducidos.' );
	}

	// Variantes del mismo idioma: duplicado casi seguro.
	$iso = array();
	foreach ( $langs as $l ) {
		$k = strtolower( substr( str_replace( '_', '-', $l['locale'] ), 0, 2 ) );
		$iso[ $k ][] = $l['name'];
	}
	foreach ( $iso as $k => $names ) {
		if ( count( $names ) > 1 ) {
			$issues[] = array( 'warn', 'Hay varias variantes del mismo idioma (' . esc_html( implode( ', ', $names ) ) . '). Google solo las distingue si el contenido es realmente distinto (precios, plazos, moneda); si es el mismo texto, lo tratará como duplicado.' );
		}
	}

	// Traducciones a medias: peor que no tenerlas.
	foreach ( $langs as $slug => $l ) {
		if ( $l['default'] ) continue;
		$total = $done = 0;
		$todo = grenvios_i18n_master_pages();
		if ( function_exists( 'grenvios_i18n_master_posts' ) ) $todo += grenvios_i18n_master_posts();
		foreach ( $todo as $pid => $title ) {
			$total++;
			$st = grenvios_i18n_page_status( $pid, $slug );
			if ( $st['state'] === 'ok' ) $done++;
		}
		if ( $total && $done === 0 ) {
			$issues[] = array( 'warn', 'El idioma ' . esc_html( $l['name'] ) . ' está activo pero no tiene ninguna página traducida: el selector lo ocultará hasta que traduzcas.' );
		} elseif ( $total && $done < $total ) {
			$issues[] = array( 'info', esc_html( $l['name'] ) . ': ' . $done . ' de ' . $total . ' páginas traducidas.' );
		}
	}

	if ( ! grenvios_i18n_ready() ) {
		$issues[] = array( 'warn', 'Falta la clave de API del traductor: sin ella solo se crea la estructura de páginas, sin texto traducido.' );
	}
	return $issues;
}

/* Páginas del idioma maestro que pueden traducirse: ID => título. */
function grenvios_i18n_master_pages() {
	$out = array();
	$args = array( 'post_type' => 'page', 'post_status' => array( 'publish', 'draft' ), 'numberposts' => -1, 'orderby' => 'menu_order', 'order' => 'ASC' );
	if ( grenvios_i18n_active() ) $args['lang'] = grenvios_i18n_default();
	foreach ( get_posts( $args ) as $p ) $out[ (int) $p->ID ] = $p->post_title;
	return $out;
}
