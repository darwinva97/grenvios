<?php
/**
 * Grenvíos — Motor de traducción automática.
 *
 * Traduce UNA VEZ y guarda el resultado en la base de datos (post-meta de la
 * página traducida + diccionario de textos fijos). En el front NO se llama a
 * ninguna API: el visitante y Googlebot reciben HTML estático ya traducido.
 * Eso es lo que hace que las páginas traducidas sean indexables de verdad.
 *
 * Proveedores soportados (se elige en Traducciones → Ajustes):
 *   - claude : API de Anthropic. Respeta tokens {{campo}} y HTML, y adapta el
 *              copy comercial en vez de traducir literal.
 *   - deepl  : API de DeepL (plan free o pro).
 *   - google : Google Cloud Translation v2.
 *   - manual : no llama a ninguna API (solo crea la estructura de páginas).
 *
 * Todo lo traducido pasa por el diccionario por idioma, así que un mismo texto
 * ("Cotiza tu envío") se paga UNA sola vez aunque aparezca en 20 páginas.
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ══════════════════════════════════════
   AJUSTES
══════════════════════════════════════ */
function grenvios_i18n_settings() {
	$d = array(
		'provider'    => 'claude',
		'claude_key'  => '',
		'claude_model'=> 'claude-sonnet-5',
		'deepl_key'   => '',
		'google_key'  => '',
		'brand'       => "Grenvíos\nGrenvios",           // términos que NO se traducen
		'tone'        => 'Comercial, cercano y claro. Empresa peruana de envíos internacionales.',
	);
	$s = get_option( 'grenvios_i18n_settings', array() );
	return array_merge( $d, is_array( $s ) ? $s : array() );
}
function grenvios_i18n_setting( $key, $default = '' ) {
	$s = grenvios_i18n_settings();
	return isset( $s[ $key ] ) && $s[ $key ] !== '' ? $s[ $key ] : $default;
}
function grenvios_i18n_ready() {
	$p = grenvios_i18n_setting( 'provider', 'claude' );
	if ( $p === 'manual' ) return true;
	if ( $p === 'claude' ) return (bool) grenvios_i18n_setting( 'claude_key' );
	if ( $p === 'deepl' )  return (bool) grenvios_i18n_setting( 'deepl_key' );
	if ( $p === 'google' ) return (bool) grenvios_i18n_setting( 'google_key' );
	return false;
}

/* ══════════════════════════════════════
   ENTRADA ÚNICA: traducir una lista de textos
   Devuelve [ texto original => traducción ]. Usa el diccionario como caché,
   así que solo se envían a la API los textos realmente nuevos.
══════════════════════════════════════ */
function grenvios_i18n_translate_texts( $texts, $lang, $ctx = 'contenido de una web de envíos internacionales', $ns = '' ) {
	$out  = array();
	$todo = array();

	$dict = grenvios_i18n_dict( $lang );
	foreach ( (array) $texts as $t ) {
		$t = (string) $t;
		if ( trim( $t ) === '' ) continue;
		// La clave DEBE incluir el espacio de nombres: si no, un slug reutilizaría
		// la traducción del cuerpo de texto y nunca acertaría en su propia caché.
		$k = grenvios_i18n_dict_hash( $t, $ns );
		if ( isset( $dict[ $k ] ) && $dict[ $k ] !== '' ) { $out[ $t ] = $dict[ $k ]; continue; }
		$todo[ $k ] = $t;
	}
	if ( empty( $todo ) ) return $out;

	if ( grenvios_i18n_setting( 'provider', 'claude' ) === 'manual' ) {
		foreach ( $todo as $t ) $out[ $t ] = $t;   // estructura sí, traducción a mano
		return $out;
	}

	// Lotes: por número de textos y por tamaño, para no exceder el límite de la API.
	$batch = array(); $chars = 0; $done = array();
	foreach ( $todo as $t ) {
		$batch[] = $t;
		$chars  += mb_strlen( $t );
		if ( count( $batch ) >= 30 || $chars >= 6000 ) {
			$done += grenvios_i18n_api_call( $batch, $lang, $ctx );
			$batch = array(); $chars = 0;
		}
	}
	if ( $batch ) $done += grenvios_i18n_api_call( $batch, $lang, $ctx );

	grenvios_i18n_dict_save( $lang, $done, $ns );
	return $out + $done;
}

/* Traducción de un solo texto (atajo). */
function grenvios_i18n_translate_one( $text, $lang, $ctx = '', $ns = '' ) {
	if ( trim( (string) $text ) === '' ) return (string) $text;
	$r = grenvios_i18n_translate_texts( array( $text ), $lang, $ctx ? $ctx : 'contenido de una web de envíos internacionales', $ns );
	return isset( $r[ $text ] ) ? $r[ $text ] : (string) $text;
}

/* Registro de errores para mostrarlos en el admin (no se pierden en silencio). */
function grenvios_i18n_log_error( $msg ) {
	$log = get_option( 'grenvios_i18n_errors', array() );
	if ( ! is_array( $log ) ) $log = array();
	array_unshift( $log, array( 'when' => current_time( 'mysql' ), 'msg' => (string) $msg ) );
	update_option( 'grenvios_i18n_errors', array_slice( $log, 0, 20 ), false );
}
function grenvios_i18n_errors() {
	$l = get_option( 'grenvios_i18n_errors', array() );
	return is_array( $l ) ? $l : array();
}

/* ══════════════════════════════════════
   LLAMADA AL PROVEEDOR
══════════════════════════════════════ */
function grenvios_i18n_api_call( $texts, $lang, $ctx ) {
	$provider = grenvios_i18n_setting( 'provider', 'claude' );
	switch ( $provider ) {
		case 'deepl':  return grenvios_i18n_api_deepl( $texts, $lang );
		case 'google': return grenvios_i18n_api_google( $texts, $lang );
		default:       return grenvios_i18n_api_claude( $texts, $lang, $ctx );
	}
}

/* Términos de marca que nunca deben traducirse. */
function grenvios_i18n_brand_terms() {
	$raw = (string) grenvios_i18n_setting( 'brand', 'Grenvíos' );
	$out = array();
	foreach ( preg_split( '/[\r\n,]+/', $raw ) as $t ) {
		$t = trim( $t );
		if ( $t !== '' ) $out[] = $t;
	}
	return $out;
}

/* ══════════════════════════════════════
   MODO LOCALIZACIÓN (sedes que comparten idioma con Perú)
══════════════════════════════════════ */

/* ¿El idioma destino es el mismo que el de la sede maestra? (es_PE → es_ES). */
function grenvios_i18n_es_misma_lengua( $lang ) {
	$iso = function ( $l ) {
		return strtolower( substr( str_replace( '-', '_', (string) grenvios_i18n_locale( $l ) ), 0, 2 ) );
	};
	return $iso( $lang ) === $iso( grenvios_i18n_default() );
}

/* Instrucciones para adaptar —no traducir— entre dos países del mismo idioma. */
function grenvios_i18n_system_localizar( $lang, $target ) {
	$ciudad = function_exists( 'grenvios_sede_ciudad' ) ? grenvios_sede_ciudad( $lang ) : '';
	$pais   = function_exists( 'grenvios_sede_pais_nombre' ) ? grenvios_sede_pais_nombre( $lang ) : $target;
	$origen = function_exists( 'grenvios_biz' ) ? grenvios_biz( grenvios_i18n_default() ) : array();
	$c_orig = isset( $origen['city'] ) ? $origen['city'] : 'Lima';
	$brand  = implode( ', ', grenvios_i18n_brand_terms() );

	$destino = $ciudad !== '' ? "{$ciudad} ({$pais})" : $pais;

	return "Eres especialista en localización de contenido comercial y SEO. El texto ya está en el idioma correcto: NO lo traduzcas.\n"
		. "Tu trabajo es ADAPTARLO de la operación de {$c_orig} a la operación de {$destino}, para que un lector de {$pais} lo sienta escrito para él.\n"
		. "Tono: " . grenvios_i18n_setting( 'tone' ) . "\n"
		. "REGLAS ESTRICTAS:\n"
		. "1. Devuelve EXCLUSIVAMENTE un array JSON de strings, mismo número y mismo orden que la entrada. Sin explicaciones ni bloques de código.\n"
		. "2. Conserva intactas las etiquetas HTML, sus atributos y su anidamiento.\n"
		. "3. Conserva EXACTAMENTE los tokens {{algo}} y {{REP:algo}} y los marcadores en MAYÚSCULAS (HOMEURL, DESTINOSMENU, ARKDINURI). Los tokens {{origen_ciudad}}, {{origen_pais}} y {{origen_desde}} ya se rellenan solos: NO los sustituyas por texto.\n"
		. "4. Sustituye la ciudad y el país de origen: lo que salía de {$c_orig} ahora sale de {$destino}.\n"
		. "5. Cambia la moneda, los organismos y los trámites por los del país destino (aduana, agencia tributaria, documentos de identidad). Si no conoces el equivalente exacto, usa un término genérico correcto en vez de inventar un nombre propio.\n"
		. "6. Adapta el vocabulario al español de {$pais} donde suene forzado, pero sin caer en localismos que dificulten la lectura.\n"
		. "7. NO cambies plazos de entrega, precios, tarifas ni números concretos: se configuran aparte y aquí serían datos inventados. Déjalos como están.\n"
		. "8. No traduzcas ni alteres los nombres de marca: {$brand}.\n"
		. "9. Respeta la longitud aproximada: es una web, el texto no puede desbordar botones ni titulares.\n"
		. "10. Si un elemento no necesita ningún cambio, devuélvelo tal cual.";
}

/* ── Claude (Anthropic) ─────────────────────────────────────── */
function grenvios_i18n_api_claude( $texts, $lang, $ctx ) {
	$key = grenvios_i18n_setting( 'claude_key' );
	if ( ! $key ) return array();

	$target = grenvios_i18n_lang_name( $lang ) . ' (' . grenvios_i18n_locale( $lang ) . ')';
	$brand  = implode( ', ', grenvios_i18n_brand_terms() );

	/* Entre sedes que comparten idioma (Perú → España, Perú → México) no hay
	 * nada que traducir: hay que LOCALIZAR. Traducir «soles» a «soles» deja una
	 * página de Madrid hablando de SUNAT y de recojo en Lima, que es justo el
	 * clon que hace que Google colapse las variantes. */
	$system = grenvios_i18n_es_misma_lengua( $lang )
		? grenvios_i18n_system_localizar( $lang, $target )
		: "Eres traductor profesional de marketing y SEO. Traduces del español a {$target}.\n"
		. "Contexto: {$ctx}. Tono: " . grenvios_i18n_setting( 'tone' ) . "\n"
		. "REGLAS ESTRICTAS:\n"
		. "1. Devuelve EXCLUSIVAMENTE un array JSON de strings, mismo número y mismo orden que la entrada. Sin explicaciones ni bloques de código.\n"
		. "2. Conserva intactas las etiquetas HTML, sus atributos y su anidamiento.\n"
		. "3. Conserva EXACTAMENTE los tokens de plantilla {{algo}} y {{REP:algo}}, y los marcadores en MAYÚSCULAS como ARKDINURI, HOMEURL, DESTINOSMENU. No los traduzcas ni los reordenes salvo que la gramática lo exija.\n"
		. "4. No traduzcas los nombres de marca: {$brand}. Tampoco números, teléfonos, direcciones de Perú ni códigos.\n"
		. "5. Adapta el texto para que suene natural y comercial en el mercado destino y mantenga la intención de búsqueda (SEO), en vez de traducir palabra por palabra.\n"
		. "6. Respeta la longitud aproximada: es texto de una web, no puede desbordar los botones ni los titulares.\n"
		. "7. Si un elemento no necesita traducción, devuélvelo tal cual.";

	$res = wp_remote_post( 'https://api.anthropic.com/v1/messages', array(
		'timeout' => 120,
		'headers' => array(
			'content-type'      => 'application/json',
			'x-api-key'         => $key,
			'anthropic-version' => '2023-06-01',
		),
		'body' => wp_json_encode( array(
			'model'      => grenvios_i18n_setting( 'claude_model', 'claude-sonnet-5' ),
			'max_tokens' => 8000,
			'system'     => $system,
			'messages'   => array( array(
				'role'    => 'user',
				'content' => wp_json_encode( array_values( $texts ), JSON_UNESCAPED_UNICODE ),
			) ),
		), JSON_UNESCAPED_UNICODE ),
	) );

	if ( is_wp_error( $res ) ) {
		grenvios_i18n_log_error( 'Claude: ' . $res->get_error_message() );
		return array();
	}
	$code = wp_remote_retrieve_response_code( $res );
	$body = json_decode( wp_remote_retrieve_body( $res ), true );
	if ( $code !== 200 ) {
		$m = isset( $body['error']['message'] ) ? $body['error']['message'] : wp_remote_retrieve_body( $res );
		grenvios_i18n_log_error( 'Claude HTTP ' . $code . ': ' . substr( (string) $m, 0, 300 ) );
		return array();
	}
	$txt = isset( $body['content'][0]['text'] ) ? $body['content'][0]['text'] : '';
	$txt = trim( preg_replace( '/^```(?:json)?|```$/m', '', trim( $txt ) ) );
	$arr = json_decode( $txt, true );
	if ( ! is_array( $arr ) || count( $arr ) !== count( $texts ) ) {
		grenvios_i18n_log_error( 'Claude: respuesta no válida (se esperaban ' . count( $texts ) . ' textos).' );
		return array();
	}

	$out  = array();
	$vals = array_values( $texts );
	foreach ( $arr as $i => $t ) {
		if ( ! is_string( $t ) || trim( $t ) === '' ) continue;
		$out[ $vals[ $i ] ] = grenvios_i18n_repair_tokens( $vals[ $i ], $t );
	}
	return $out;
}

/* ── DeepL ──────────────────────────────────────────────────── */
function grenvios_i18n_api_deepl( $texts, $lang ) {
	$key = grenvios_i18n_setting( 'deepl_key' );
	if ( ! $key ) return array();
	$host = ( substr( $key, -3 ) === ':fx' ) ? 'https://api-free.deepl.com' : 'https://api.deepl.com';

	// Protege los tokens {{...}} y los marcadores del tema con <x> ignorado.
	$prot = array();
	foreach ( $texts as $t ) $prot[] = grenvios_i18n_protect( $t, '<x>%s</x>' );

	$body = array(
		'target_lang'  => grenvios_i18n_deepl_code( $lang ),
		'source_lang'  => 'ES',
		'tag_handling' => 'xml',
		'ignore_tags'  => 'x',
	);
	$q = http_build_query( $body );
	foreach ( $prot as $t ) $q .= '&text=' . rawurlencode( $t );

	$res = wp_remote_post( $host . '/v2/translate', array(
		'timeout' => 120,
		'headers' => array(
			'Authorization' => 'DeepL-Auth-Key ' . $key,
			'Content-Type'  => 'application/x-www-form-urlencoded',
		),
		'body' => $q,
	) );
	if ( is_wp_error( $res ) ) { grenvios_i18n_log_error( 'DeepL: ' . $res->get_error_message() ); return array(); }
	$code = wp_remote_retrieve_response_code( $res );
	$data = json_decode( wp_remote_retrieve_body( $res ), true );
	if ( $code !== 200 || empty( $data['translations'] ) ) {
		grenvios_i18n_log_error( 'DeepL HTTP ' . $code . ': ' . substr( wp_remote_retrieve_body( $res ), 0, 300 ) );
		return array();
	}
	$out  = array();
	$vals = array_values( $texts );
	foreach ( $data['translations'] as $i => $tr ) {
		if ( ! isset( $vals[ $i ] ) ) break;
		$t = grenvios_i18n_unprotect( $tr['text'], '<x>%s</x>' );
		$out[ $vals[ $i ] ] = grenvios_i18n_repair_tokens( $vals[ $i ], $t );
	}
	return $out;
}

/* Códigos de destino de DeepL (necesita variante para EN y PT). */
function grenvios_i18n_deepl_code( $lang ) {
	$hl  = strtoupper( grenvios_i18n_hreflang( $lang ) );
	$map = array( 'EN' => 'EN-US', 'PT' => 'PT-BR', 'ZH' => 'ZH', 'NB' => 'NB' );
	if ( isset( $map[ $hl ] ) ) return $map[ $hl ];
	return str_replace( '_', '-', $hl );
}

/* ── Google Cloud Translation v2 ────────────────────────────── */
function grenvios_i18n_api_google( $texts, $lang ) {
	$key = grenvios_i18n_setting( 'google_key' );
	if ( ! $key ) return array();

	$prot = array();
	foreach ( $texts as $t ) $prot[] = grenvios_i18n_protect( $t, '<span translate="no">%s</span>' );

	$res = wp_remote_post( 'https://translation.googleapis.com/language/translate/v2?key=' . rawurlencode( $key ), array(
		'timeout' => 120,
		'headers' => array( 'Content-Type' => 'application/json' ),
		'body'    => wp_json_encode( array(
			'q'      => array_values( $prot ),
			'source' => grenvios_i18n_default(),
			'target' => grenvios_i18n_hreflang( $lang ),
			'format' => 'html',
		), JSON_UNESCAPED_UNICODE ),
	) );
	if ( is_wp_error( $res ) ) { grenvios_i18n_log_error( 'Google: ' . $res->get_error_message() ); return array(); }
	$data = json_decode( wp_remote_retrieve_body( $res ), true );
	if ( empty( $data['data']['translations'] ) ) {
		grenvios_i18n_log_error( 'Google: ' . substr( wp_remote_retrieve_body( $res ), 0, 300 ) );
		return array();
	}
	$out  = array();
	$vals = array_values( $texts );
	foreach ( $data['data']['translations'] as $i => $tr ) {
		if ( ! isset( $vals[ $i ] ) ) break;
		$t = grenvios_i18n_unprotect( html_entity_decode( $tr['translatedText'], ENT_QUOTES, 'UTF-8' ), '<span translate="no">%s</span>' );
		$out[ $vals[ $i ] ] = grenvios_i18n_repair_tokens( $vals[ $i ], $t );
	}
	return $out;
}

/* ══════════════════════════════════════
   PROTECCIÓN DE TOKENS
   Los tokens {{campo}} y los marcadores del tema se envuelven con una etiqueta
   que el traductor tiene prohibido tocar, y se desenvuelven al volver.
══════════════════════════════════════ */
function grenvios_i18n_token_regex() {
	return '/(\{\{[^}]*\}\}|ARKDINURI|HOMEURL|DESTINOSMENU|SOCIALHEADER|SOCIALFOOTER|TOPBAREXTRA|FOOTERLOGO|FOOTERABOUT|FOOTERCOPY|LANGSWITCHER)/';
}
function grenvios_i18n_protect( $text, $wrap ) {
	return preg_replace_callback( grenvios_i18n_token_regex(), function ( $m ) use ( $wrap ) {
		return sprintf( $wrap, $m[1] );
	}, (string) $text );
}
function grenvios_i18n_unprotect( $text, $wrap ) {
	$open  = substr( $wrap, 0, strpos( $wrap, '%s' ) );
	$close = substr( $wrap, strpos( $wrap, '%s' ) + 2 );
	return str_replace( array( $open, $close ), '', (string) $text );
}

/* Red de seguridad: si el traductor perdió o alteró algún token, se restaura el
 * texto original. Un token perdido rompe el contenido de la página, así que es
 * preferible dejar ese fragmento sin traducir que publicarlo roto. */
function grenvios_i18n_repair_tokens( $source, $translated ) {
	preg_match_all( grenvios_i18n_token_regex(), (string) $source, $a );
	preg_match_all( grenvios_i18n_token_regex(), (string) $translated, $b );
	sort( $a[0] ); sort( $b[0] );
	if ( $a[0] !== $b[0] ) return (string) $source;
	return (string) $translated;
}

/* ══════════════════════════════════════
   SLUGS TRADUCIDOS
   El slug se traduce como frase (no palabra por palabra) porque es una señal
   SEO: /en/services/international-parcel-shipping/ posiciona; /en/servicios/
   envio-internacional-de-paquetes/ no.
══════════════════════════════════════ */
function grenvios_i18n_translate_slug( $slug_es, $lang, $fallback_title = '' ) {
	$phrase = str_replace( '-', ' ', (string) $slug_es );

	// Espacio de nombres 'slug': la misma frase no se traduce igual en el cuerpo
	// de la pagina que en una URL.
	$t   = grenvios_i18n_translate_one(
		$phrase, $lang,
		'slug de URL para SEO: devuelve solo la frase traducida, en minusculas, sin tildes, 2 a 5 palabras, sin signos de puntuacion',
		'slug'
	);
	$new = sanitize_title( remove_accents( $t ) );

	// Idiomas sin alfabeto latino (chino, japones, coreano, ruso, arabe, griego...):
	// el slug quedaria en caracteres que el navegador codifica
	// (/zh/%E5%9B%BD%E9%99%85%E5%8C%85%E8%A3%B9/): valido para Google, pero
	// ilegible al compartirlo por WhatsApp y dificil de auditar. Se pide una
	// version romanizada o su equivalente en ingles.
	if ( ! preg_match( '/[a-z0-9]/', $new ) ) {
		$r  = grenvios_i18n_translate_one(
			$phrase, $lang,
			'slug de URL en alfabeto latino: romanizacion (pinyin, romaji) o el equivalente en ingles, minusculas, 2 a 5 palabras, sin signos',
			'slug_latin'
		);
		$rr = sanitize_title( remove_accents( $r ) );
		if ( preg_match( '/[a-z0-9]/', $rr ) ) $new = $rr;
	}

	if ( $new === '' && $fallback_title !== '' ) $new = sanitize_title( remove_accents( $fallback_title ) );
	if ( $new === '' ) $new = (string) $slug_es;
	return $new;
}

/* ══════════════════════════════════════
   TRADUCCIÓN DE UNA PÁGINA COMPLETA
   Crea (o actualiza) la página del idioma, la vincula en Polylang y traduce:
   título, slug, SEO title, meta description, TODOS los campos del editor de
   página, los repeaters y las FAQ.
══════════════════════════════════════ */
function grenvios_i18n_translate_page( $master_id, $lang, $force = false ) {
	$master_id = (int) $master_id;
	if ( ! $master_id || ! grenvios_i18n_active() ) {
		return new WP_Error( 'grenvios_i18n', 'Polylang no está activo.' );
	}
	if ( grenvios_i18n_is_default( $lang ) ) {
		return new WP_Error( 'grenvios_i18n', 'El idioma de origen no se traduce a sí mismo.' );
	}
	$master = get_post( $master_id );
	if ( ! $master ) return new WP_Error( 'grenvios_i18n', 'Página de origen inexistente.' );

	$mslug = $master->post_name;
	$ctx   = 'página "' . $master->post_title . '" de una web peruana de envíos internacionales';

	/* 1) Padre: garantiza que la jerarquía exista en el idioma destino, para que
	 *    la URL quede /en/services/international-parcel-shipping/. */
	$parent_t = 0;
	if ( $master->post_parent ) {
		$parent_t = grenvios_i18n_translation_id( $master->post_parent, $lang );
		if ( ! $parent_t ) {
			$r = grenvios_i18n_translate_page( $master->post_parent, $lang, false );
			if ( is_wp_error( $r ) ) return $r;
			$parent_t = (int) $r;
		}
	}

	/* 2) Página traducida: crear o reutilizar. */
	$tid = grenvios_i18n_translation_id( $master_id, $lang );
	$title = grenvios_i18n_translate_one( $master->post_title, $lang, $ctx . ' — título de página, breve' );

	if ( ! $tid ) {
		$slug = grenvios_i18n_translate_slug( $mslug, $lang, $title );
		$tid  = wp_insert_post( array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_parent'  => $parent_t,
			'post_content' => '',
			'menu_order'   => $master->menu_order,
		), true );
		if ( is_wp_error( $tid ) ) return $tid;

		pll_set_post_language( $tid, $lang );
		$tr = function_exists( 'pll_get_post_translations' ) ? pll_get_post_translations( $master_id ) : array();
		$tr[ grenvios_i18n_default() ] = $master_id;
		$tr[ $lang ] = $tid;
		pll_save_post_translations( $tr );
	} elseif ( $force ) {
		wp_update_post( array( 'ID' => $tid, 'post_title' => $title, 'post_parent' => $parent_t ) );
	}

	grenvios_i18n_stamp_master( $tid, $mslug );

	/* 3) Plantilla de página (si el maestro usa una). */
	$tpl = get_post_meta( $master_id, '_wp_page_template', true );
	if ( $tpl ) update_post_meta( $tid, '_wp_page_template', $tpl );

	/* 4) SEO: title tag y meta description propios del idioma. */
	// La portada se registra con la clave 'home' en el registro de textos.
	$rslug = ( (int) get_option( 'page_on_front' ) === $master_id ) ? 'home' : $mslug;
	$seo   = grenvios_seo_for_slug( $rslug, $master_id );
	$seo_title = isset( $seo[0] ) ? $seo[0] : '';
	$seo_desc  = isset( $seo[1] ) ? $seo[1] : '';
	if ( $seo_title ) {
		update_post_meta( $tid, 'grenvios_seo_title', grenvios_i18n_translate_one( $seo_title, $lang, $ctx . ' — etiqueta title para Google, máximo 60 caracteres' ) );
	}
	if ( $seo_desc ) {
		update_post_meta( $tid, 'grenvios_seo_desc', grenvios_i18n_translate_one( $seo_desc, $lang, $ctx . ' — meta description para Google, 150-160 caracteres, con llamada a la acción' ) );
	}

	/* 5) Campos del editor de página (tokens {{campo}}). */
	grenvios_i18n_translate_fields( $master_id, $tid, $rslug, $lang, $ctx );

	/* 6) Repeaters (tarjetas, listas, pasos, FAQ de la página…). */
	grenvios_i18n_translate_repeaters( $master_id, $tid, $rslug, $lang, $ctx );

	/* 6.b) Rutas servicio × país: sus campos de contenido propio no viven en el
	 *      registro de textos, así que se traducen aparte. También se copia la
	 *      meta de la combinación para que la traducción se renderice igual. */
	if ( function_exists( 'grenvios_combo_of' ) && grenvios_combo_of( $master_id ) ) {
		$combo = get_post_meta( $master_id, GRENVIOS_COMBO_META, true );
		if ( $combo ) update_post_meta( $tid, GRENVIOS_COMBO_META, $combo );

		$src = array();
		foreach ( grenvios_combo_campos() as $key => $def ) {
			$v = trim( (string) get_post_meta( $master_id, 'grenvios_' . $key, true ) );
			if ( $v !== '' ) $src[ $key ] = $v;
		}
		if ( $src ) {
			$map = grenvios_i18n_translate_texts( array_values( $src ), $lang, $ctx . ' — ficha de una ruta de envío concreta (precio de referencia, plazos y restricciones aduaneras)' );
			foreach ( $src as $key => $text ) {
				update_post_meta( $tid, 'grenvios_' . $key, isset( $map[ $text ] ) ? $map[ $text ] : $text );
			}
		}
	}

	/* 6.c) BARRIDO FINAL: cualquier otro texto guardado en la página maestra.
	 *      Cubre los campos que no viven en el registro (los `dst_*` de las
	 *      páginas de país, campos sintéticos y cualquiera que se añada en el
	 *      futuro). Sin esto, un texto personalizado a mano se quedaría en
	 *      español en todas las traducciones y nadie se enteraría. */
	$resto = array();
	foreach ( get_post_meta( $master_id ) as $mk => $mv ) {
		if ( strpos( $mk, 'grenvios_' ) !== 0 ) continue;
		if ( strpos( $mk, 'grenvios_rep_' ) === 0 ) continue;            // ya tratados
		if ( in_array( $mk, array( 'grenvios_seo_title', 'grenvios_seo_desc' ), true ) ) continue;
		if ( metadata_exists( 'post', $tid, $mk ) ) continue;             // ya traducido arriba
		$val = maybe_unserialize( $mv[0] );
		if ( ! is_string( $val ) || trim( $val ) === '' ) continue;
		// Imágenes, URLs y números se copian tal cual.
		if ( preg_match( '#^(https?://|/wp-content/)#i', trim( $val ) ) || substr( $mk, -4 ) === '_img'
			|| strpos( $mk, '_bg_' ) !== false || ! grenvios_i18n_is_translatable( $val ) ) {
			update_post_meta( $tid, $mk, $val );
			continue;
		}
		$resto[ $mk ] = $val;
	}
	if ( $resto ) {
		$map = grenvios_i18n_translate_texts( array_values( $resto ), $lang, $ctx );
		foreach ( $resto as $mk => $text ) {
			update_post_meta( $tid, $mk, isset( $map[ $text ] ) ? $map[ $text ] : $text );
		}
	}

	/* 7) Contenido libre del editor de WordPress, si la página lo usa. */
	if ( trim( (string) $master->post_content ) !== '' ) {
		$html = grenvios_i18n_translate_html_content( $master->post_content, $lang, $ctx );
		wp_update_post( array( 'ID' => $tid, 'post_content' => $html ) );
	}

	/* 8) Marca de estado: hash del origen para detectar "desactualizada". */
	update_post_meta( $tid, '_grenvios_i18n_hash', grenvios_i18n_source_hash( $master_id, $mslug ) );
	update_post_meta( $tid, '_grenvios_i18n_date', current_time( 'mysql' ) );

	return (int) $tid;
}

/* ══════════════════════════════════════
   TRADUCCIÓN DE UNA GUÍA (entrada del blog)
   Las entradas no usan el registro de textos: su contenido es HTML libre. Se
   traducen el título, el slug, el extracto, el contenido (respetando el
   marcado) y su categoría; y se conserva a qué página de dinero refuerza,
   apuntando a la versión traducida de esa página.
══════════════════════════════════════ */
function grenvios_i18n_translate_post( $master_id, $lang, $force = false ) {
	$master_id = (int) $master_id;
	if ( ! $master_id || ! grenvios_i18n_active() ) {
		return new WP_Error( 'grenvios_i18n', 'Polylang no está activo.' );
	}
	if ( grenvios_i18n_is_default( $lang ) ) {
		return new WP_Error( 'grenvios_i18n', 'El idioma de origen no se traduce a sí mismo.' );
	}
	$master = get_post( $master_id );
	if ( ! $master || $master->post_type !== 'post' ) {
		return new WP_Error( 'grenvios_i18n', 'Entrada inexistente.' );
	}

	$ctx   = 'guía de una web peruana de envíos internacionales: texto divulgativo, claro y práctico';
	$title = grenvios_i18n_translate_one( $master->post_title, $lang, $ctx . ' — título del artículo' );

	$tid = grenvios_i18n_translation_id( $master_id, $lang );
	if ( ! $tid ) {
		$slug = grenvios_i18n_translate_slug( $master->post_name, $lang, $title );
		$tid  = wp_insert_post( array(
			'post_type'    => 'post',
			'post_status'  => $master->post_status,
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_content' => '',
		), true );
		if ( is_wp_error( $tid ) ) return $tid;

		pll_set_post_language( $tid, $lang );
		$tr = function_exists( 'pll_get_post_translations' ) ? pll_get_post_translations( $master_id ) : array();
		$tr[ grenvios_i18n_default() ] = $master_id;
		$tr[ $lang ] = $tid;
		pll_save_post_translations( $tr );
	} elseif ( $force ) {
		wp_update_post( array( 'ID' => $tid, 'post_title' => $title ) );
	}

	/* Extracto y contenido */
	$upd = array( 'ID' => $tid, 'post_title' => $title );
	if ( trim( (string) $master->post_excerpt ) !== '' ) {
		$upd['post_excerpt'] = grenvios_i18n_translate_one( $master->post_excerpt, $lang, $ctx . ' — resumen breve' );
	}
	if ( trim( (string) $master->post_content ) !== '' ) {
		$upd['post_content'] = grenvios_i18n_translate_html_content( $master->post_content, $lang, $ctx );
	}
	wp_update_post( $upd );

	/* Imagen destacada */
	$thumb = get_post_thumbnail_id( $master_id );
	if ( $thumb ) set_post_thumbnail( $tid, $thumb );

	/* Categoría: Polylang mantiene una categoría por idioma; si existe la
	 * traducción del término, se usa; si no, se deja la del original. */
	$cats = wp_get_post_terms( $master_id, 'category', array( 'fields' => 'ids' ) );
	if ( $cats && ! is_wp_error( $cats ) ) {
		$dest = array();
		foreach ( $cats as $cid ) {
			$t = function_exists( 'pll_get_term' ) ? (int) pll_get_term( $cid, $lang ) : 0;
			$dest[] = $t ? $t : $cid;
		}
		wp_set_post_categories( $tid, $dest );
	}

	/* Página de dinero que refuerza: se apunta a la versión traducida. */
	if ( defined( 'GRENVIOS_CLUSTER_META' ) ) {
		$target = (int) get_post_meta( $master_id, GRENVIOS_CLUSTER_META, true );
		if ( $target ) {
			$t_trad = grenvios_i18n_translation_id( $target, $lang );
			update_post_meta( $tid, GRENVIOS_CLUSTER_META, $t_trad ? $t_trad : $target );
		}
	}

	/* Keyword objetivo del idioma (no es la traducción literal de la española,
	 * pero sirve de punto de partida y se puede ajustar a mano). */
	if ( defined( 'GRENVIOS_KW_META' ) ) {
		$kw = trim( (string) get_post_meta( $master_id, GRENVIOS_KW_META, true ) );
		if ( $kw !== '' ) {
			update_post_meta( $tid, GRENVIOS_KW_META, grenvios_i18n_translate_one( $kw, $lang, 'palabra clave de búsqueda para SEO', 'kw' ) );
		}
	}

	update_post_meta( $tid, '_grenvios_i18n_hash', grenvios_i18n_source_hash( $master_id, $master->post_name ) );
	update_post_meta( $tid, '_grenvios_i18n_date', current_time( 'mysql' ) );

	delete_transient( 'grenvios_links_graph' );
	return (int) $tid;
}

/* Guías del idioma maestro: ID => título. */
function grenvios_i18n_master_posts() {
	$out  = array();
	$args = array( 'post_type' => 'post', 'post_status' => array( 'publish', 'draft' ), 'numberposts' => -1 );
	if ( grenvios_i18n_active() ) $args['lang'] = grenvios_i18n_default();
	foreach ( get_posts( $args ) as $p ) $out[ (int) $p->ID ] = $p->post_title;
	return $out;
}

/* Campos de texto del registro: valor guardado en la página maestra o, si nunca
 * se editó, el texto por defecto del tema. Las imágenes se copian sin traducir. */
function grenvios_i18n_translate_fields( $master_id, $tid, $mslug, $lang, $ctx ) {
	if ( ! function_exists( 'grenvios_text_registry' ) ) return;
	$reg = grenvios_text_registry();
	if ( ! isset( $reg[ $mslug ] ) ) return;

	$src = array();   // clave => texto origen
	foreach ( $reg[ $mslug ]['sections'] as $sec ) {
		foreach ( $sec['fields'] as $key => $f ) {
			$type = isset( $f[1] ) ? $f[1] : 'text';
			$def  = isset( $f[2] ) ? $f[2] : '';
			$val  = metadata_exists( 'post', $master_id, 'grenvios_' . $key )
				? get_post_meta( $master_id, 'grenvios_' . $key, true )
				: $def;
			if ( $type === 'image' ) {           // imágenes: se comparten
				if ( $val !== '' ) update_post_meta( $tid, 'grenvios_' . $key, $val );
				continue;
			}
			if ( trim( (string) $val ) === '' ) { update_post_meta( $tid, 'grenvios_' . $key, '' ); continue; }
			$src[ $key ] = (string) $val;
		}
	}
	// Fondos por página (URLs de imagen): se copian tal cual.
	if ( function_exists( 'grenvios_page_bg_fields' ) ) {
		$bg = grenvios_page_bg_fields();
		if ( isset( $bg[ $mslug ] ) ) {
			foreach ( $bg[ $mslug ] as $key => $def ) {
				$v = get_post_meta( $master_id, 'grenvios_' . $key, true );
				if ( $v !== '' ) update_post_meta( $tid, 'grenvios_' . $key, $v );
			}
		}
	}
	if ( empty( $src ) ) return;

	$map = grenvios_i18n_translate_texts( array_values( $src ), $lang, $ctx );
	foreach ( $src as $key => $text ) {
		$t = isset( $map[ $text ] ) ? $map[ $text ] : $text;
		update_post_meta( $tid, 'grenvios_' . $key, $t );
	}
}

/* Repeaters: se materializan (defaults del tema incluidos) y se traducen solo
 * los valores que son texto visible; URLs, iconos y números se copian. */
function grenvios_i18n_translate_repeaters( $master_id, $tid, $mslug, $lang, $ctx ) {
	if ( ! function_exists( 'grenvios_repeater_defaults' ) ) return;

	$keys = array_keys( (array) grenvios_repeater_defaults( $mslug ) );
	foreach ( get_post_meta( $master_id ) as $mk => $mv ) {
		if ( strpos( $mk, 'grenvios_rep_' ) === 0 ) $keys[] = substr( $mk, strlen( 'grenvios_rep_' ) );
	}
	// FAQ de la página: por defecto vienen del tema.
	$keys[] = 'page_faq';
	$keys   = array_values( array_unique( $keys ) );

	foreach ( $keys as $key ) {
		$items = get_post_meta( $master_id, 'grenvios_rep_' . $key, true );
		if ( ! is_array( $items ) ) {
			if ( $key === 'page_faq' && function_exists( 'grenvios_page_faqs' ) ) {
				$items = array();
				foreach ( grenvios_page_faqs( $mslug ) as $f ) $items[] = array( 'q' => $f[0], 'a' => $f[1] );
			} else {
				$defs  = grenvios_repeater_defaults( $mslug );
				$items = isset( $defs[ $key ] ) ? $defs[ $key ] : null;
			}
		}
		if ( ! is_array( $items ) || empty( $items ) ) continue;

		// 1ª pasada: recolectar todos los textos del repeater en un solo lote.
		$texts = array();
		array_walk_recursive( $items, function ( $v ) use ( &$texts ) {
			if ( is_string( $v ) && grenvios_i18n_is_translatable( $v ) && ! preg_match( '#^(https?://|/|\#|mailto:|tel:)#i', trim( $v ) ) ) {
				$texts[ md5( $v ) ] = $v;
			}
		} );
		if ( empty( $texts ) ) { update_post_meta( $tid, 'grenvios_rep_' . $key, $items ); continue; }

		$map = grenvios_i18n_translate_texts( array_values( $texts ), $lang, $ctx . ' — bloque repetible "' . $key . '"' );

		// 2ª pasada: sustituir.
		array_walk_recursive( $items, function ( &$v ) use ( $map ) {
			if ( is_string( $v ) && isset( $map[ $v ] ) ) $v = $map[ $v ];
		} );
		update_post_meta( $tid, 'grenvios_rep_' . $key, $items );
	}
}

/* Contenido HTML libre: se trocean los nodos de texto para no romper el marcado. */
function grenvios_i18n_translate_html_content( $html, $lang, $ctx ) {
	$texts = grenvios_i18n_walk_html( $html, $lang, 'collect' );
	if ( empty( $texts ) ) return $html;
	grenvios_i18n_translate_texts( $texts, $lang, $ctx );   // deja todo en el diccionario
	return grenvios_i18n_walk_html( $html, $lang, 'translate' );
}

/* Huella del contenido de origen: si la clienta edita el español, la traducción
 * queda marcada como "desactualizada" en la pantalla de Traducciones. */
function grenvios_i18n_source_hash( $master_id, $mslug ) {
	$parts = array( get_post_field( 'post_title', $master_id ), get_post_field( 'post_content', $master_id ) );
	foreach ( get_post_meta( $master_id ) as $k => $v ) {
		if ( strpos( $k, 'grenvios_' ) !== 0 ) continue;
		$parts[] = $k . '=' . maybe_serialize( $v );
	}
	sort( $parts );
	return md5( $mslug . '|' . implode( '|', $parts ) );
}

/* Estado de una traducción: falta / al día / desactualizada. */
function grenvios_i18n_page_status( $master_id, $lang ) {
	$tid = grenvios_i18n_translation_id( $master_id, $lang );
	if ( ! $tid ) return array( 'state' => 'missing', 'id' => 0 );
	$saved = (string) get_post_meta( $tid, '_grenvios_i18n_hash', true );
	$now   = grenvios_i18n_source_hash( $master_id, get_post_field( 'post_name', $master_id ) );
	return array(
		'state' => ( $saved && $saved !== $now ) ? 'stale' : 'ok',
		'id'    => $tid,
		'url'   => get_permalink( $tid ),
		'date'  => get_post_meta( $tid, '_grenvios_i18n_date', true ),
	);
}

/* ══════════════════════════════════════
   TEXTOS FIJOS DEL TEMA (encabezado, pie, botones, formularios…)
══════════════════════════════════════ */
function grenvios_i18n_translate_strings( $lang ) {
	$texts = array();
	foreach ( grenvios_i18n_all_partials() as $p ) {
		foreach ( grenvios_i18n_collect_partial( $p ) as $t ) $texts[ md5( $t ) ] = $t;
	}
	// Textos declarados por los módulos (páginas data-driven, bloques de
	// enlazado, rutas…). Sin esto solo se traducirían DESPUÉS de que alguien
	// visitara cada página en ese idioma.
	foreach ( (array) apply_filters( 'grenvios_i18n_extra_strings', array() ) as $t ) {
		$t = trim( (string) $t );
		if ( $t !== '' ) $texts[ md5( $t ) ] = $t;
	}

	// Textos que el PHP generó en el front y aún no tenían traducción.
	foreach ( grenvios_i18n_missing( $lang ) as $t ) $texts[ md5( $t ) ] = $t;

	if ( empty( $texts ) ) return 0;
	$map = grenvios_i18n_translate_texts( array_values( $texts ), $lang, 'interfaz de una web de envíos internacionales: menús, botones, etiquetas de formulario y pie de página. Traducciones cortas' );
	delete_option( grenvios_i18n_missing_key( $lang ) );
	return count( $map );
}

/* ══════════════════════════════════════
   DESTINOS (países)
   Los países se editan en una sola pantalla y son datos, no páginas de texto:
   se traducen a una opción por idioma y `grenvios_destinos()` sirve la versión
   del idioma activo. Si la clienta agrega un país nuevo, basta con volver a
   pulsar "Traducir" y aparece en todos los idiomas.
══════════════════════════════════════ */
function grenvios_i18n_destinos_key( $lang ) {
	return 'grenvios_destinos_i18n_' . sanitize_key( $lang );
}
function grenvios_i18n_translate_destinos( $lang ) {
	if ( ! function_exists( 'grenvios_destinos' ) ) return 0;
	$base = grenvios_i18n_destinos_source();
	if ( empty( $base ) ) return 0;

	$trad_keys = array( 'title', 'seo', 'desc', 'kw', 'tiempo', 'modos', 'entrega', 'restr', 'lead', 'continente' );
	$texts = array();
	foreach ( $base as $slug => $d ) {
		foreach ( $trad_keys as $k ) {
			if ( ! empty( $d[ $k ] ) && grenvios_i18n_is_translatable( $d[ $k ] ) ) $texts[ md5( $d[ $k ] ) ] = $d[ $k ];
		}
	}
	if ( empty( $texts ) ) return 0;

	$map = grenvios_i18n_translate_texts( array_values( $texts ), $lang, 'fichas de países destino de una empresa de envíos internacionales (nombre del país, tiempos de entrega, restricciones aduaneras)' );

	$out = array();
	foreach ( $base as $slug => $d ) {
		$row = $d;
		foreach ( $trad_keys as $k ) {
			if ( ! empty( $d[ $k ] ) && isset( $map[ $d[ $k ] ] ) ) $row[ $k ] = $map[ $d[ $k ] ];
		}
		$out[ $slug ] = $row;   // la CLAVE (slug español) no cambia: es el slug maestro
	}
	update_option( grenvios_i18n_destinos_key( $lang ), $out, false );
	return count( $out );
}

/* Datos de destinos en el idioma maestro, sin pasar por el filtro de idioma. */
function grenvios_i18n_destinos_source() {
	$GLOBALS['grenvios_i18n_raw_destinos'] = true;
	$base = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();
	unset( $GLOBALS['grenvios_i18n_raw_destinos'] );
	return $base;
}

/* Sirve los destinos en el idioma activo. */
add_filter( 'grenvios_destinos', function ( $destinos ) {
	if ( ! empty( $GLOBALS['grenvios_i18n_raw_destinos'] ) ) return $destinos;
	$lang = grenvios_i18n_current();
	if ( grenvios_i18n_is_default( $lang ) ) return $destinos;
	$tr = get_option( grenvios_i18n_destinos_key( $lang ), array() );
	if ( ! is_array( $tr ) || empty( $tr ) ) return $destinos;
	foreach ( $destinos as $slug => $d ) {
		if ( isset( $tr[ $slug ] ) ) $destinos[ $slug ] = array_merge( $d, $tr[ $slug ] );
	}
	return $destinos;
}, 10, 1 );

/* Los enlaces del submenú de destinos deben apuntar a la página del idioma. */
add_filter( 'grenvios_destino_permalink', function ( $url, $slug ) {
	if ( ! grenvios_i18n_active() || grenvios_i18n_is_default() ) return $url;
	// $slug === 'destinos' es el hub; el resto son paginas hijas.
	$path = ( $slug === 'destinos' ) ? 'destinos' : 'destinos/' . $slug;
	$tid  = grenvios_i18n_page_translation_by_path( $path, grenvios_i18n_current() );
	return $tid ? get_permalink( $tid ) : $url;
}, 10, 2 );
