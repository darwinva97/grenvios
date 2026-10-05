<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Textos de secciones generadas por PHP, editables desde el panel
 * ══════════════════════════════════════════════════════════════════════════
 *
 * VERIFICACIÓN (2026-09-28, scripts/cobertura-panel.php): solo el 52 % de los
 * textos visibles de la ruta principal se podía editar. El resto lo pintaban
 * módulos PHP sin campos: las secciones de las fichas de destino
 * (inc/destinos-secciones.php), los bloques de la home (precio, destinos uno
 * por uno, cómo elegir, más servicios), las listas de las páginas de
 * herramienta, la cobertura de recojo, la barra lateral de las preguntas…
 *
 * Reescribir cada módulo para pasar cada frase por grenvios_tf() era largo y
 * frágil. Aquí se resuelve en un punto, sobre el HTML final:
 *
 *   1. En los contenedores de la lista (grenvios_dt_contenedores), cada texto
 *      —título, antetítulo, párrafo, punto, paso, término, botón— recibe una
 *      clave estable: hash del texto que genera la plantilla.
 *   2. Si esa página tiene guardado un texto para esa clave, lo sustituye.
 *   3. El panel muestra un acordeón por sección con sus textos, en orden,
 *      SALTANDO los que ya tienen campo propio en la página (registro, hero,
 *      listas): nada sale dos veces.
 *
 * Reglas de seguridad:
 *   · Solo se guarda lo que se cambia; vaciar un campo vuelve al automático.
 *   · Si cambia el dato que alimenta una frase (un plazo en el gestor), cambia
 *     su clave y vuelve a salir el texto automático: nunca queda un dato viejo.
 *   · Nunca se tocan elementos interactivos (con id, formularios, botones de
 *     formulario): la calculadora y el cotizador siguen funcionando.
 *   · Se apartan tablas (datos del gestor), tarjetas del blog, la lista de
 *     ciudades (sale de «Datos del país»), iconos y números de los pasos, y las
 *     secciones marcadas `gr-editable-propio` (ya tienen sus campos).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Contenedores cuyos textos se vuelven editables. Filtro para añadir más. */
function grenvios_dt_contenedores() {
	return apply_filters( 'grenvios_dt_contenedores', array(
		'~<section\b([^>]*class="[^"]*\b(?:dest-seo-sec|gr-home-precio|gr-home-dest|gr-home-elegir|gr-mas|gr-dcomp|gr-ci|gr-cobertura|srv-section|about-section|gr-ent|dest-cotiza)\b[^"]*"[^>]*)>(.*?)</section>~s',
		'~<div\b([^>]*class="grenvios-tracking"[^>]*)>(.*?)</div>~s',
	) );
}

/* Unidades recogidas en esta petición: [ id de sección => [ título, [ clave => [ etiqueta, texto ] ] ] ]. */
function grenvios_dt_unidades( $añadir = null ) {
	static $u = array();
	if ( $añadir !== null ) $u = $u + $añadir;
	return $u;
}

function grenvios_dt_clave( $texto ) {
	return 'dt_' . substr( md5( trim( preg_replace( '/\s+/u', ' ', $texto ) ) ), 0, 12 );
}

function grenvios_dt_norm( $s ) {
	$s = function_exists( 'grenvios_sede_tokens_apply' ) ? grenvios_sede_tokens_apply( (string) $s ) : (string) $s;
	$s = html_entity_decode( wp_strip_all_tags( $s ), ENT_QUOTES, 'UTF-8' );
	return mb_strtolower( trim( preg_replace( '/\s+/u', ' ', $s ) ) );
}

/* Procesa un contenedor: aparta, numera, aplica lo guardado y recoge unidades. */
function grenvios_dt_procesar( $s ) {
	$attrs = $s[1];
	$body  = $s[2];
	if ( strpos( $attrs, 'gr-editable-propio' ) !== false ) return $s[0];

	$apartado = array();
	$tapar = function ( $re ) use ( &$body, &$apartado ) {
		$body = preg_replace_callback( $re, function ( $m ) use ( &$apartado ) {
			$apartado[] = $m[0];
			return '%%DT' . ( count( $apartado ) - 1 ) . '%%';
		}, $body );
	};
	$tapar( '~<(script|style|svg|form|select|textarea|button)\b.*?</\1>~s' );
	$tapar( '~<table\b.*?</table>~s' );
	$tapar( '~<article\b[^>]*blog-card.*?</article>~s' );
	$tapar( '~<ul\b[^>]*dest-ciudades[^>]*>.*?</ul>~s' );
	$tapar( '~<span class="(?:srv-step-n|srv-step-ic|srv-card-ic|srv-time-ic|dest-guia-ic|gr-bq-ic|gr-bq-check-ic)[^"]*"[^>]*>.*?</span>~s' );
	$tapar( '~<i\b[^>]*></i>~' );
	$tapar( '~<(?:input|img)\b[^>]*>~' );

	$titulo   = preg_match( '~<h2[^>]*>(.*?)</h2>~s', $body, $mt ) ? trim( wp_strip_all_tags( preg_replace( '/%%DT\d+%%/', '', $mt[1] ) ) ) : '';
	if ( $titulo === '' && preg_match( '~<h3[^>]*>(.*?)</h3>~s', $body, $mt ) ) $titulo = trim( wp_strip_all_tags( preg_replace( '/%%DT\d+%%/', '', $mt[1] ) ) );
	$unidades = array();
	$cuenta   = array();

	$unidad = function ( $m ) use ( &$unidades, &$cuenta ) {
		$tag = $m[1]; $at = isset( $m[2] ) ? $m[2] : ''; $in = $m[3];
		if ( $tag === 'li' && strpos( $in, 'srv-step-tx' ) !== false ) return $m[0];
		if ( $tag === 'a' && strpos( $at, 'btn' ) === false ) return $m[0];
		if ( preg_match( '/\s(?:id|data-(?!wow-|effect|delay|duration|split|ease)[a-z-]+)=/', $at . $in ) ) return $m[0];   // interactivo (la animación no cuenta)
		$lead = '';
		if ( preg_match( '/^((?:\s|%%DT\d+%%)+)(.*)$/s', $in, $pm ) ) { $lead = $pm[1]; $in = $pm[2]; }
		if ( strpos( $in, '%%DT' ) !== false ) return $m[0];                          // lleva algo apartado dentro
		$txt = trim( wp_strip_all_tags( $in ) );
		if ( mb_strlen( $txt ) < 4 ) return $m[0];

		$clave = grenvios_dt_clave( $in );
		$tipo  = $tag === 'h2' ? 'Título'
			: ( strpos( $at, 'sub-heading' ) !== false ? 'Antetítulo'
			: ( strpos( $at, 'srv-intro' ) !== false ? 'Introducción'
			: ( in_array( $tag, array( 'h3', 'h4' ), true ) ? 'Subtítulo'
			: ( $tag === 'li' ? 'Punto'
			: ( $tag === 'strong' ? 'Tarjeta'
			: ( $tag === 'span' ? ( strpos( $at, 'gr-dt-tx' ) !== false ? 'Texto' : 'Paso' )
			: ( $tag === 'dt' ? 'Término'
			: ( $tag === 'dd' ? 'Definición'
			: ( $tag === 'a' ? 'Botón' : 'Párrafo' ) ) ) ) ) ) ) ) );
		$cuenta[ $tipo ] = isset( $cuenta[ $tipo ] ) ? $cuenta[ $tipo ] + 1 : 1;
		$etq = $tipo . ( in_array( $tipo, array( 'Título', 'Antetítulo', 'Introducción' ), true ) ? '' : ' ' . $cuenta[ $tipo ] );
		$unidades[ $clave ] = array( $etq, $in );

		$propio = trim( (string) grenvios_field( $clave, '' ) );
		if ( $propio === '' ) return $m[0];
		return '<' . $tag . $at . '>' . $lead . wp_kses_post( $propio ) . '</' . $tag . '>';
	};
	/* Primero el texto de los pasos (dentro de un <li> con número e icono);
	 * después el resto de elementos sin bloques dentro. */
	/* Patrones «desenrollados» ([^<]* … ): la versión con «(?:(?!…).)*?» avanzaba
	 * carácter a carácter y costaba 1,5–2,8 s por página. */
	$body = preg_replace_callback( '~<(span)(\s[^>]*srv-step-tx[^>]*)>([^<]*(?:<(?!/?span\b)[^<]*)*?)</span>~', $unidad, $body );
	/* Tarjetas «título + texto» dentro de un enlace con iconos (Más servicios
	 * de la home, gr-sm): <span class="…-tx"><strong>T</strong><span>X</span></span>.
	 * Cada parte es su propia unidad. */
	$body = preg_replace_callback( '~(<span class="[a-z-]*-tx"[^>]*>\s*)<strong>((?:(?!</?(?:strong|span)\b).)*?)</strong>(\s*)<span>((?:(?!</?span\b).)*?)</span>~s', function ( $m ) use ( $unidad ) {
		$t = $unidad( array( '<strong>' . $m[2] . '</strong>', 'strong', '', $m[2] ) );
		$x = $unidad( array( '<span class="gr-dt-tx">' . $m[4] . '</span>', 'span', ' class="gr-dt-tx"', $m[4] ) );
		$x = str_replace( ' class="gr-dt-tx"', '', $x );
		return $m[1] . $t . $m[3] . $x;
	}, $body );
	$body = preg_replace_callback( '~<(h2|h3|h4|p|li|dt|dd|a)(\s[^>]*)?>([^<]*(?:<(?!(?:h2|h3|h4|p|li|ul|ol|div|section|table|article|dl)\b)[^<]*)*?)</\1>~', $unidad, $body );

	for ( $i = 0; $i < 3 && strpos( $body, '%%DT' ) !== false; $i++ ) {   // también lo anidado
		$body = preg_replace_callback( '/%%DT(\d+)%%/', function ( $m ) use ( $apartado ) {
			return isset( $apartado[ (int) $m[1] ] ) ? $apartado[ (int) $m[1] ] : '';
		}, $body );
	}

	if ( $unidades ) {
		$id = md5( $titulo . '|' . implode( ',', array_keys( $unidades ) ) );
		grenvios_dt_unidades( array( $id => array( $titulo !== '' ? $titulo : 'Bloque', $unidades ) ) );
	}
	$tag = substr( $s[0], 1, strcspn( $s[0], " >", 1 ) );
	return '<' . $tag . $attrs . '>' . $body . '</' . $tag . '>';
}

/* Se procesa el HTML FINAL de la página, en una sola pasada y después de
 * cualquier caché. Antes iba en `grenvios_html_final`, que también se aplica a
 * bloques que el tema guarda en caché (la home): el bloque se guardaba ya
 * procesado, las visitas siguientes no lo veían en el panel y una edición no
 * se aplicaba hasta vaciar la caché. */
add_action( 'template_redirect', function () {
	if ( is_admin() || is_feed() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) return;
	/* Visitantes: solo hace falta si esta página tiene algún texto guardado desde
	 * el panel. Sin eso, coste cero (antes se procesaba en todas las visitas). */
	if ( ! current_user_can( 'edit_posts' ) ) {
		$pid = function_exists( 'grenvios_editor_post_id' ) ? (int) grenvios_editor_post_id() : (int) get_queried_object_id();
		if ( ! $pid ) return;
		$hay = false;
		foreach ( array_keys( (array) get_post_meta( $pid ) ) as $k ) {
			if ( strpos( $k, 'grenvios_dt_' ) === 0 ) { $hay = true; break; }
		}
		if ( ! $hay ) return;
	}
	ob_start( 'grenvios_dt_ob' );
}, 99 );

function grenvios_dt_ob( $html ) {
	if ( ! is_string( $html ) || strpos( $html, '<body' ) === false ) return $html;
	foreach ( grenvios_dt_contenedores() as $re ) {
		$html = preg_replace_callback( $re, 'grenvios_dt_procesar', $html );
	}
	/* El panel se pintó antes (wp_footer) y dejó un marcador: ahora que se
	 * conocen las secciones, se rellena. */
	if ( strpos( $html, '<!--GR_DT_PANEL-->' ) !== false ) {
		$html = str_replace( '<!--GR_DT_PANEL-->', grenvios_dt_panel_html(), $html );
	}
	return $html;
}

/* Textos que la página YA edita con campos propios: no se repiten en el panel. */
function grenvios_dt_ya_editables( $slug ) {
	$vals = array();
	$reg  = function_exists( 'grenvios_text_registry' ) ? grenvios_text_registry() : array();
	if ( isset( $reg[ $slug ]['sections'] ) ) {
		foreach ( $reg[ $slug ]['sections'] as $sec ) {
			foreach ( (array) $sec['fields'] as $k => $f ) {
				if ( ( isset( $f[1] ) ? $f[1] : '' ) === 'image' ) continue;
				$v = grenvios_dt_norm( grenvios_field( $k, isset( $f[2] ) ? $f[2] : '' ) );
				if ( $v !== '' ) foreach ( preg_split( '/\r\n|\r|\n/', $v ) as $l ) if ( trim( $l ) !== '' ) $vals[] = trim( $l );
			}
		}
	}
	if ( function_exists( 'grenvios_hero_pagina_defaults' ) ) {
		foreach ( grenvios_hero_pagina_defaults() as $k => $d ) $vals[] = grenvios_dt_norm( grenvios_field( $k, $d ) );
	}
	if ( function_exists( 'grenvios_repeater_schema' ) && function_exists( 'grenvios_repeater' ) ) {
		foreach ( grenvios_repeater_schema( $slug ) as $rep ) {
			foreach ( (array) grenvios_repeater( $rep['key'] ) as $item ) foreach ( (array) $item as $v ) $vals[] = grenvios_dt_norm( $v );
		}
	}
	return array_values( array_filter( array_unique( $vals ) ) );
}

/* Panel: el acordeón se construye al final (grenvios_dt_ob), cuando ya se han
 * recogido las secciones; aquí solo se deja el marcador y el contexto. */
function grenvios_dt_contexto( $ctx = null ) {
	static $c = null;
	if ( $ctx !== null ) $c = $ctx;
	return $c;
}
add_action( 'grenvios_editor_secciones', function ( $slug, $post_id = 0, $render_field = null ) {
	if ( ! is_callable( $render_field ) ) return;
	grenvios_dt_contexto( array( 'slug' => $slug, 'render' => $render_field ) );
	echo '<!--GR_DT_PANEL-->';
}, 9, 3 );

function grenvios_dt_panel_html() {
	$ctx    = grenvios_dt_contexto();
	$grupos = grenvios_dt_unidades();
	if ( ! $ctx || ! $grupos ) return '';
	$slug   = $ctx['slug'];
	$render = $ctx['render'];
	$ya     = grenvios_dt_ya_editables( $slug );
	$esta   = function ( $txt ) use ( $ya ) {
		$t = grenvios_dt_norm( $txt );
		foreach ( $ya as $v ) if ( $t === $v || ( mb_strlen( $t ) > 10 && strpos( $v, $t ) !== false ) ) return true;
		return false;
	};
	$dest    = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();
	$prefijo = ( isset( $dest[ $slug ] ) || ( function_exists( 'grenvios_editor_pais_slug' ) && grenvios_editor_pais_slug( $slug ) !== '' ) ) ? 'Destino · ' : 'Sección · ';
	$html = '';
	foreach ( $grupos as $g ) {
		list( $titulo, $unidades ) = $g;
		$campos = '';
		foreach ( $unidades as $k => $u ) {
			$actual = trim( (string) grenvios_field( $k, '' ) );
			if ( $actual === '' && $esta( $u[1] ) ) continue;       // ya tiene su campo
			/* Mismo marcado que el $render_field del panel, pero sin ob_start():
			 * aquí estamos DENTRO de un manejador de búfer y PHP no permite
			 * abrir otro (la página salía en blanco para los administradores). */
			$fid     = 'nep-f-' . esc_attr( $k );
			$campos .= '<div class="nep-field"><label for="' . $fid . '">' . esc_html( $u[0] ) . '</label>'
				. '<span class="nep-hint">Puedes usar &lt;br&gt; y &lt;span class="hl"&gt;…&lt;/span&gt;.</span>'
				. '<textarea id="' . $fid . '" data-field-key="' . esc_attr( $k ) . '" rows="3">' . esc_textarea( $actual !== '' ? $actual : $u[1] ) . '</textarea></div>';
		}
		if ( $campos === '' ) continue;
		$html .= '<div class="nep-accordion" data-sel=""><button class="nep-acc-header" type="button"><span>'
			. esc_html( $prefijo . $titulo ) . '</span><i class="fa-solid fa-chevron-down"></i></button>'
			. '<div class="nep-acc-body"><div class="nep-grid">' . $campos . '</div></div></div>';
	}
	if ( $html === '' ) return '';
	return '<div class="nep-global-note"><i class="fa-solid fa-file-lines"></i> Textos de secciones automáticas: deja un campo vacío para volver al texto automático.</div>' . $html;
}
