<?php
/**
 * Grenvíos — Diccionario de textos fijos del tema.
 *
 * El contenido editable de cada página vive en post-meta y se traduce página por
 * página (una página por idioma, con su propio slug). Pero en el diseño hay texto
 * que NO es editable: encabezado, pie, etiquetas de formularios, botones,
 * migas de pan, textos generados en PHP… Ese texto se traduce con un DICCIONARIO
 * por idioma:
 *
 *     opción `grenvios_i18n_dict_<idioma>`  =  [ md5(texto español) => traducción ]
 *
 * El diccionario lo rellena automáticamente el motor de traducción
 * (inc/i18n-translate.php) y se puede corregir a mano desde la pantalla
 * "Traducciones". Se aplica en DOS puntos:
 *
 *   1) grenvios_t( 'texto' )        → textos generados desde PHP.
 *   2) filtro `grenvios_partial_html` → recorre el HTML del partial y traduce
 *      solo los nodos de texto y los atributos visibles (alt, title, placeholder,
 *      aria-label, value de botones), SIN tocar el marcado, las clases, los
 *      scripts ni los tokens {{campo}} del editor de página.
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* Atributos cuyo contenido ve el usuario (y por tanto se traduce). */
function grenvios_i18n_attrs() {
	return array( 'alt', 'title', 'placeholder', 'aria-label', 'data-placeholder' );
}

/* ══════════════════════════════════════
   DICCIONARIO
══════════════════════════════════════ */

function grenvios_i18n_dict_key( $lang ) {
	return 'grenvios_i18n_dict_' . sanitize_key( $lang );
}

/* Clave de un texto dentro del diccionario.
 * El "espacio de nombres" ($ns) permite que un MISMO texto tenga traducciones
 * distintas segun su uso: el cuerpo de la pagina y el slug de la URL no se
 * traducen igual ("Envio de Paquetes" vs "ship-package-from-peru"). */
function grenvios_i18n_dict_hash( $text, $ns = '' ) {
	return md5( ( $ns === '' ? '' : $ns . '|' ) . (string) $text );
}

/* Diccionario completo de un idioma (cacheado en memoria por petición). */
function grenvios_i18n_dict( $lang ) {
	static $cache = array();
	if ( isset( $cache[ $lang ] ) ) return $cache[ $lang ];
	$d = get_option( grenvios_i18n_dict_key( $lang ), array() );
	return $cache[ $lang ] = is_array( $d ) ? $d : array();
}

/* Guarda pares [origen => traducción] en el diccionario de un idioma. */
function grenvios_i18n_dict_save( $lang, $pairs, $ns = '' ) {
	if ( empty( $pairs ) ) return;
	$dict = get_option( grenvios_i18n_dict_key( $lang ), array() );
	if ( ! is_array( $dict ) ) $dict = array();
	foreach ( $pairs as $src => $dst ) {
		if ( $dst === '' || $dst === null ) continue;
		$dict[ grenvios_i18n_dict_hash( $src, $ns ) ] = (string) $dst;
	}
	update_option( grenvios_i18n_dict_key( $lang ), $dict, false );
	wp_cache_delete( grenvios_i18n_dict_key( $lang ), 'options' );
}

/* Textos que aún no tienen traducción en un idioma (los recoge el motor).
 * Se acumulan al renderizar, así el traductor sabe qué falta sin tener que
 * adivinar qué strings genera el PHP en tiempo de ejecución. */
function grenvios_i18n_missing_key( $lang ) {
	return 'grenvios_i18n_missing_' . sanitize_key( $lang );
}
function grenvios_i18n_missing( $lang ) {
	$m = get_option( grenvios_i18n_missing_key( $lang ), array() );
	return is_array( $m ) ? $m : array();
}
function grenvios_i18n_note_missing( $lang, $text ) {
	// Solo se registra en el front y sin bloquear la petición: buffer estático
	// que se vuelca una sola vez al final (shutdown).
	static $buf = array();
	if ( $text === '' ) return;
	if ( ! isset( $buf[ $lang ] ) ) {
		$buf[ $lang ] = true;
		add_action( 'shutdown', function () use ( $lang ) {
			$pend = grenvios_i18n_pending_buffer( $lang );
			if ( empty( $pend ) ) return;
			$cur = grenvios_i18n_missing( $lang );
			$new = array_slice( array_unique( array_merge( $cur, $pend ) ), 0, 2000 );
			if ( $new !== $cur ) update_option( grenvios_i18n_missing_key( $lang ), $new, false );
		}, 99 );
	}
	grenvios_i18n_pending_buffer( $lang, $text );
}
/* Buffer en memoria de los textos sin traducir de esta petición. */
function grenvios_i18n_pending_buffer( $lang, $add = null ) {
	static $buf = array();
	if ( $add !== null ) {
		$buf[ $lang ][ md5( $add ) ] = $add;
		return null;
	}
	return isset( $buf[ $lang ] ) ? array_values( $buf[ $lang ] ) : array();
}

/* ══════════════════════════════════════
   API PÚBLICA: grenvios_t()
   Uso en PHP:  echo grenvios_t( 'Solicitar cotización' );
══════════════════════════════════════ */
function grenvios_t( $text, $lang = null ) {
	$text = (string) $text;
	if ( $text === '' ) return $text;
	$lang = $lang ? $lang : grenvios_i18n_current();

	if ( grenvios_i18n_is_default( $lang ) ) {
		$out = $text;
	} else {
		$dict = grenvios_i18n_dict( $lang );
		$k    = md5( $text );
		if ( isset( $dict[ $k ] ) && $dict[ $k ] !== '' ) {
			$out = $dict[ $k ];
		} else {
			grenvios_i18n_note_missing( $lang, $text );
			$out = $text;   // sin traducción todavía: el original, nunca vacío
		}
	}

	/* Filtro `grenvios_t`: lo usa inc/sedes-contenido.php para resolver los
	 * tokens de sede que viven dentro de textos fijos del diseño, como el
	 * banner «Desde {{origen_ciudad}}, {{origen_pais}}». */
	return apply_filters( 'grenvios_t', $out, $text, $lang );
}

/* Igual que grenvios_t() pero para textos con marcado sencillo. */
function grenvios_te( $text, $lang = null ) {
	echo grenvios_t( $text, $lang );
}

/* ══════════════════════════════════════
   RECORRIDO DE HTML
   Modo 'translate' → devuelve el HTML traducido.
   Modo 'collect'   → devuelve la lista de textos traducibles encontrados.
══════════════════════════════════════ */
function grenvios_i18n_walk_html( $html, $lang, $mode = 'translate' ) {
	if ( ! is_string( $html ) || $html === '' ) return $mode === 'collect' ? array() : $html;

	$found = array();
	$dict  = ( $mode === 'translate' ) ? grenvios_i18n_dict( $lang ) : array();
	$attrs = grenvios_i18n_attrs();

	$parts = preg_split( '/(<[^>]+>)/s', $html, -1, PREG_SPLIT_DELIM_CAPTURE );
	if ( ! is_array( $parts ) ) return $mode === 'collect' ? array() : $html;

	$skip = 0;   // profundidad dentro de <script>/<style>/<svg>

	foreach ( $parts as $i => $part ) {
		if ( $part === '' ) continue;

		// ── Etiquetas ──
		if ( $part[0] === '<' ) {
			if ( preg_match( '/^<\s*(script|style|svg)\b/i', $part ) && substr( $part, -2 ) !== '/>' ) $skip++;
			if ( preg_match( '/^<\s*\/\s*(script|style|svg)\b/i', $part ) && $skip > 0 ) $skip--;
			if ( $skip > 0 ) continue;

			// Atributos visibles al usuario
			$tag = $part;
			foreach ( $attrs as $attr ) {
				$tag = preg_replace_callback(
					'/\b' . preg_quote( $attr, '/' ) . '\s*=\s*"([^"]*)"/i',
					function ( $m ) use ( &$found, $dict, $mode, $lang ) {
						$val = $m[1];
						if ( ! grenvios_i18n_is_translatable( $val ) ) return $m[0];
						if ( $mode === 'collect' ) { $found[ md5( $val ) ] = $val; return $m[0]; }
						$t = isset( $dict[ md5( $val ) ] ) ? $dict[ md5( $val ) ] : '';
						if ( $t === '' ) { grenvios_i18n_note_missing( $lang, $val ); return $m[0]; }
						return str_replace( '"' . $val . '"', '"' . esc_attr( $t ) . '"', $m[0] );
					},
					$tag
				);
			}
			if ( $mode === 'translate' ) $parts[ $i ] = $tag;
			continue;
		}

		// ── Nodos de texto ──
		if ( $skip > 0 ) continue;

		// Conserva los espacios/saltos de línea de alrededor (el diseño depende de ellos)
		if ( ! preg_match( '/^(\s*)(.*?)(\s*)$/s', $part, $m ) ) continue;
		$core = $m[2];
		if ( ! grenvios_i18n_is_translatable( $core ) ) continue;

		if ( $mode === 'collect' ) {
			$found[ md5( $core ) ] = $core;
			continue;
		}
		$t = isset( $dict[ md5( $core ) ] ) ? $dict[ md5( $core ) ] : '';
		if ( $t === '' ) { grenvios_i18n_note_missing( $lang, $core ); continue; }
		$parts[ $i ] = $m[1] . $t . $m[3];
	}

	return $mode === 'collect' ? array_values( $found ) : implode( '', $parts );
}

/* ¿Este texto merece traducirse? Filtra ruido: vacíos, números, símbolos,
 * tokens del editor ({{campo}}, {{REP:clave}}) y marcadores del tema
 * (ARKDINURI, HOMEURL, DESTINOSMENU, SOCIALHEADER…). */
function grenvios_i18n_is_translatable( $text ) {
	$t = trim( (string) $text );
	if ( $t === '' || mb_strlen( $t ) < 2 ) return false;

	// Marcadores/tokens del tema, solos o combinados
	$probe = preg_replace( '/\{\{[^}]*\}\}/', '', $t );
	$probe = str_replace(
		array( 'ARKDINURI', 'HOMEURL', 'DESTINOSMENU', 'SOCIALHEADER', 'SOCIALFOOTER',
		       'TOPBAREXTRA', 'FOOTERLOGO', 'FOOTERABOUT', 'FOOTERCOPY', 'LANGSWITCHER' ),
		'', $probe
	);
	$probe = trim( html_entity_decode( $probe, ENT_QUOTES, 'UTF-8' ) );
	if ( $probe === '' ) return false;

	// Debe contener al menos una letra (evita "01", "—", "+51 900 612 836")
	if ( ! preg_match( '/\p{L}{2,}/u', $probe ) ) return false;

	// URLs y rutas sueltas
	if ( preg_match( '#^(https?://|/|\#|mailto:|tel:)\S*$#i', $probe ) ) return false;

	return true;
}

/* Textos traducibles de un partial (los usa el motor para saber qué mandar). */
function grenvios_i18n_collect_partial( $name ) {
	$file = get_template_directory() . '/template-parts/' . $name . '.html';
	if ( ! file_exists( $file ) ) return array();
	return grenvios_i18n_walk_html( (string) file_get_contents( $file ), '', 'collect' );
}

/* Todos los partials del tema (encabezado, pie, 404 y contenidos). */
function grenvios_i18n_all_partials() {
	$out = array();
	foreach ( (array) glob( get_template_directory() . '/template-parts/*.html' ) as $f ) {
		$out[] = basename( $f, '.html' );
	}
	return $out;
}

/* ══════════════════════════════════════
   APLICACIÓN AUTOMÁTICA AL RENDER
   Se engancha ANTES de que se resuelvan los tokens {{campo}}: así el texto
   editable (que ya está traducido en la meta de la página traducida) nunca
   pasa por el diccionario, y el texto fijo del diseño sí.
══════════════════════════════════════ */
add_filter( 'grenvios_partial_html', function ( $html, $name ) {
	$lang = grenvios_i18n_current();
	if ( grenvios_i18n_is_default( $lang ) ) return $html;
	/* Recorrer el HTML entero traduciendo cadena a cadena costaba 0,17 s por
	 * partial y por visita, siempre con el mismo resultado: se memoriza
	 * (inc/cache-html.php). */
	if ( function_exists( 'grenvios_cache_html' ) ) {
		return grenvios_cache_html( 'i18n-strings', $html, $lang, function ( $h ) use ( $lang ) {
			return grenvios_i18n_walk_html( $h, $lang, 'translate' );
		} );
	}
	return grenvios_i18n_walk_html( $html, $lang, 'translate' );
}, 5, 2 );   // prioridad 5: antes del selector de idioma (10)

/* Título de página, migas de pan y demás texto que el tema imprime en PHP. */
add_filter( 'grenvios_text', 'grenvios_t', 10, 1 );

/* ══════════════════════════════════════
   TEXTOS DEL BLOG, MIGAS Y 404
   Se declaran aquí para que el traductor los resuelva de una vez, sin esperar a
   que alguien visite cada plantilla en cada idioma. Antes estos textos usaban
   __() con el dominio 'grenvios', pero el tema no carga ningún .mo: se quedaban
   en español para siempre. Ahora pasan por el diccionario como el resto.
══════════════════════════════════════ */
add_filter( 'grenvios_i18n_extra_strings', function ( $textos ) {
	foreach ( array(
		// Migas de pan
		'Inicio', 'Destinos', 'Preguntas Frecuentes',
		// Listado del blog, filtros y paginación
		'Todas', 'Leer más', 'No hay entradas para mostrar por ahora.',
		'Volver al inicio', 'Búsqueda', 'Resultados para “%s”',
		// Entrada y 404
		'Comentarios %s', '¡Vaya! No encontramos esta página.',
		// Selector de idioma
		'Cambiar idioma',
		// Descripción de respaldo de los archivos de categoría
		'Guías y artículos sobre %s de Grenvíos: consejos prácticos, requisitos y plazos para tus envíos internacionales desde Perú.',
	) as $t ) $textos[] = $t;
	return $textos;
} );
