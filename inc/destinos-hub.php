<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  /destinos/ (ruta principal): comparar rutas, no solo listarlas
 * ══════════════════════════════════════════════════════════════════════════
 *
 * QUÉ FALTABA
 * El hub listaba nueve tarjetas y catorce países sueltos, y luego dos bloques
 * de texto que explicaban «qué mirar en cada ficha» sin enseñar ni un dato.
 * Quien llega buscando «envíos internacionales desde Perú» quiere comparar:
 * cuánto tarda, por dónde va, si llega a la puerta y cuánto se paga de
 * impuesto. Todo eso ya estaba en el gestor de destinos y no salía aquí.
 *
 * QUÉ AÑADE (solo en la ruta principal; en las de país manda su bloque)
 *   1) Filtros sobre las tarjetas: región, vía terrestre, entrega a domicilio.
 *   2) Comparativa: una tabla con vía, plazo, entrega e impuesto terrestre.
 *   3) Panel de cifras junto a «Cada país tiene sus propias reglas».
 *   4) «Lo que necesitas para enviar»: tercera sección de contenido, editable.
 *   5) Tres preguntas frecuentes respondidas con los datos del gestor.
 *
 * Todo se calcula a partir de grenvios_pais_datos(): si la clienta cambia un
 * plazo o una modalidad en el gestor, la tabla, las cifras y las respuestas
 * cambian solas.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function grenvios_dh_activa() {
	if ( is_admin() ) return false;
	if ( ! function_exists( 'grenvios_current_slug' ) || grenvios_current_slug() !== 'destinos' ) return false;
	return ! function_exists( 'grenvios_hq_pais' ) || grenvios_hq_pais() === '';
}

/* Los destinos con ficha, con lo que hace falta para comparar. */
function grenvios_dh_filas() {
	static $filas = null;
	if ( $filas !== null ) return $filas;
	$filas = array();
	if ( ! function_exists( 'grenvios_destinos' ) || ! function_exists( 'grenvios_pais_datos' ) ) return $filas;

	foreach ( grenvios_destinos() as $slug => $x ) {
		$d = grenvios_pais_datos( $slug );
		if ( ! $d ) continue;
		preg_match_all( '/\d+/', (string) $d['tiempo'], $n );
		$filas[ $slug ] = array(
			'slug'     => $slug,
			'nombre'   => $d['title'],
			'iso'      => grenvios_dest_iso( $slug ),
			'region'   => grenvios_dest_region( $slug, isset( $x['continente'] ) ? $x['continente'] : '' ),
			'modos'    => $d['modos'],
			'terr'     => ! empty( $d['terr'] ),
			'casa'     => ! empty( $d['casa'] ),
			'tiempo'   => $d['tiempo'],
			'min'      => isset( $n[0][0] ) ? (int) $n[0][0] : 99,
			'max'      => $n[0] ? (int) end( $n[0] ) : 99,
			'impuesto' => (string) $d['impuesto'],
			'url'      => function_exists( 'grenvios_hd_url' ) ? grenvios_hd_url( $slug ) : home_url( '/destinos/' . $slug . '/' ),
		);
	}
	return $filas;
}

function grenvios_dh_cmp_plazo( $a, $b ) {
	return $a['min'] !== $b['min'] ? $a['min'] - $b['min'] : $a['max'] - $b['max'];
}

/* «29.5» → «29,5 %». */
function grenvios_dh_pct( $v ) {
	return str_replace( '.', ',', (string) $v ) . ' %';
}

/* «Chile, Estados Unidos y España». */
function grenvios_dh_enumerar( $nombres ) {
	$nombres = array_values( $nombres );
	if ( count( $nombres ) < 2 ) return implode( '', $nombres );
	$ult = array_pop( $nombres );
	return implode( ', ', $nombres ) . ' y ' . $ult;
}

/* ─────────────────────────────────────────────────────────────────────────
 * 1) y 2) Filtros sobre las tarjetas y comparativa debajo
 * ───────────────────────────────────────────────────────────────────────── */
add_filter( 'grenvios_content_html', function ( $html ) {
	if ( ! grenvios_dh_activa() || ! is_string( $html ) ) return $html;
	$filas = grenvios_dh_filas();
	if ( ! $filas ) return $html;

	/* Cabecera en dos columnas: texto a la izquierda, imagen a la derecha
	 * (maqueta del cliente 2026-10-02, skill grenvios-landing). */
	if ( strpos( $html, 'col-lg-8 mx-auto text-center sm-padding' ) !== false && strpos( $html, 'projects-section' ) !== false ) {
		$img = trim( (string) grenvios_field( 'dh_img', '' ) );
		if ( $img === '' ) $img = get_template_directory_uri() . '/assets/img/hero-home.jpg';
		$html = preg_replace_callback(
			'~<div class="row">\s*<div class="col-lg-8 mx-auto text-center sm-padding">\s*<div class="section-heading">([^<]*(?:<(?!/div>)[^<]*)*)</div>\s*</div>\s*</div>~',
			function ( $m ) use ( $img ) {
				return '<div class="gr-dh-hero"><div class="gr-dh-texto section-heading">' . $m[1] . '</div>'
					. '<figure class="gr-dh-foto wow fade-in-right" data-wow-delay="150ms"><img src="' . esc_url( $img ) . '" alt="" loading="lazy" decoding="async"></figure></div>';
			},
			$html, 1
		);
	}

	/* «Otros destinos»: agrupados por región con buscador (maqueta 2026-10-02). */
	if ( strpos( $html, '<ul class="gr-otros"' ) !== false && strpos( $html, 'gr-og' ) === false ) {
		$html = grenvios_dh_otros_agrupados( $html );
	}

	$marca = '<div class="row gy-4 gr-dcards">';
	if ( strpos( $html, $marca ) !== false ) {
		$html = str_replace( $marca, grenvios_dh_filtros_html( $filas ) . $marca, $html );
	}

	$fin = '<!--/.destinos-section-->';
	if ( strpos( $html, $fin ) !== false ) {
		$comp = grenvios_dh_comparativa_html( $filas );
		if ( function_exists( 'grenvios_apply_text_tokens' ) ) $comp = grenvios_apply_text_tokens( $comp );
		$html = str_replace( $fin, $fin . $comp, $html );
	}
	return $html;
}, 8 );

function grenvios_dh_filtros_html( $filas ) {
	$cuenta = function ( $fn ) use ( $filas ) { return count( array_filter( $filas, $fn ) ); };
	$botones = array(
		array( 'all',           'Todos',                  count( $filas ) ),
		array( 'region:sud',    'Sudamérica',             $cuenta( function ( $f ) { return $f['region'] === 'sud'; } ) ),
		array( 'region:norte',  'Norteamérica y Caribe',  $cuenta( function ( $f ) { return $f['region'] === 'norte'; } ) ),
		array( 'region:europa', 'Europa',                 $cuenta( function ( $f ) { return $f['region'] === 'europa'; } ) ),
		array( 'terr',          'Con vía terrestre',      $cuenta( function ( $f ) { return $f['terr']; } ) ),
		array( 'casa',          'Entrega a domicilio',    $cuenta( function ( $f ) { return $f['casa']; } ) ),
	);
	$ph = function_exists( 'grenvios_field' ) ? grenvios_field( 'dh_buscar', 'Buscar país o ciudad' ) : 'Buscar país o ciudad';
	$h  = '<div class="gr-dbar"><label class="gr-dsearch"><i class="fa-regular fa-magnifying-glass" aria-hidden="true"></i>'
		. '<input type="search" class="gr-dsearch-in" placeholder="' . esc_attr( $ph ) . '" aria-label="' . esc_attr( $ph ) . '" autocomplete="off"></label>'
		. '<div class="gr-dfilter" role="group" aria-label="Filtrar destinos">';
	foreach ( $botones as $b ) {
		if ( $b[2] < 1 ) continue;
		$on = $b[0] === 'all';
		$h .= '<button type="button" class="gr-dfilter-btn' . ( $on ? ' is-on' : '' ) . '" data-f="' . esc_attr( $b[0] ) . '" aria-pressed="' . ( $on ? 'true' : 'false' ) . '">'
			. esc_html( $b[1] ) . ' <span class="gr-dfilter-n">' . (int) $b[2] . '</span></button>';
	}
	$vacio = function_exists( 'grenvios_field' ) ? grenvios_field( 'dh_vacio', 'No encontramos ese destino en la lista. Escríbenos y te confirmamos si llegamos.' ) : 'No encontramos ese destino en la lista.';
	$h .= '</div></div><p class="gr-dvacio" hidden>' . esc_html( $vacio ) . '</p>';
	/* Sin JavaScript los botones no hacen nada y se ven todas las tarjetas. */
	$h .= '<script>(function(){var bar=document.querySelector(".gr-dbar"),grid=document.querySelector(".gr-dcards"),vac=document.querySelector(".gr-dvacio");if(!bar||!grid)return;'
		. 'var f="all",q="",inp=bar.querySelector(".gr-dsearch-in");'
		. 'function norm(s){return s.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g,"");}'
		. 'function apply(){var n=0,p=f.split(":");grid.querySelectorAll(".gr-dcard").forEach(function(c){'
		. 'var ok=p[0]==="all"||(p[0]==="region"?c.getAttribute("data-region")===p[1]:c.getAttribute("data-"+p[0])==="1");'
		. 'if(ok&&q)ok=norm(c.textContent).indexOf(q)>-1;c.parentElement.hidden=!ok;if(ok)n++;});if(vac)vac.hidden=n>0;}'
		. 'bar.addEventListener("click",function(e){var b=e.target.closest&&e.target.closest(".gr-dfilter-btn");if(!b)return;'
		. 'bar.querySelectorAll(".gr-dfilter-btn").forEach(function(x){var on=x===b;x.classList.toggle("is-on",on);x.setAttribute("aria-pressed",on?"true":"false");});'
		. 'f=b.getAttribute("data-f");apply();});'
		. 'if(inp)inp.addEventListener("input",function(){q=norm(inp.value.trim());apply();});})();</script>';
	return $h;
}

function grenvios_dh_comparativa_html( $filas ) {
	/* Diseño de la maqueta del cliente (2026-10-02, skill grenvios-landing):
	 * tres cifras, dos paneles (aéreo + terrestre / solo aéreo) con su tabla de
	 * destino · plazo · entrega · impuesto, y nota con botón. Todo sale del
	 * gestor de destinos. */
	$orden = array_values( $filas );
	usort( $orden, 'grenvios_dh_cmp_plazo' );
	$top = $orden[0];

	$terr = array(); $aire = array();
	foreach ( $filas as $f ) { if ( $f['terr'] ) $terr[] = $f; else $aire[] = $f; }

	/* Ruta destacada: la terrestre más rápida (la misma que en las tarjetas). */
	$destacada = '';
	$t_orden = $terr;
	usort( $t_orden, 'grenvios_dh_cmp_plazo' );
	if ( $t_orden ) $destacada = $t_orden[0]['slug'];

	$fila_html = function ( $f ) use ( $top, $destacada ) {
		$flag = function_exists( 'grenvios_dest_flag_html' ) ? grenvios_dest_flag_html( $f['iso'] ) : '';
		if ( $f['terr'] && $f['impuesto'] !== '' ) {
			$imp = '<strong class="gr-dc-imp-v">≈ ' . esc_html( grenvios_dh_pct( $f['impuesto'] ) ) . '</strong><small>se paga en {{origen_ciudad}}</small>';
		} else {
			$imp = '<span class="gr-dcomp-na">Solo vía aérea</span>';
		}
		$tiempo = trim( (string) $f['tiempo'] );
		$plazo  = preg_match( '/^(\d+(?:\s*(?:a|-|–)\s*\d+)?)\s*(.*)$/u', $tiempo, $mp )
			? '<strong>' . esc_html( $mp[1] ) . '</strong>' . ( $mp[2] !== '' ? '<span>' . esc_html( $mp[2] ) . '</span>' : '' )
			: esc_html( $tiempo );
		$etq = '';
		if ( $f['slug'] === $destacada )      $etq = '<span class="gr-dc-etq"><i class="fa-solid fa-star" aria-hidden="true"></i> Ruta destacada</span>';
		elseif ( $f['slug'] === $top['slug'] ) $etq = '<span class="gr-dc-etq gr-dc-etq--verde"><i class="fa-solid fa-bolt" aria-hidden="true"></i> Más rápido</span>';
		$clase = $f['slug'] === $destacada ? ' class="is-destacada"' : ( $f['slug'] === $top['slug'] ? ' class="is-rapida"' : '' );
		return '<tr' . $clase . '>'
			. '<th scope="row"><a href="' . esc_url( $f['url'] ) . '">' . $flag . '<span class="gr-dc-pais"><strong>' . esc_html( $f['nombre'] ) . '</strong>' . $etq . '</span></a></th>'
			. '<td data-label="Plazo" class="gr-dc-plazo">' . $plazo . '</td>'
			. '<td data-label="Entrega">' . ( $f['casa'] ? 'A domicilio' : 'Retiro en agencia local' ) . '</td>'
			. '<td data-label="Impuesto terrestre" class="gr-dcomp-imp">' . $imp . '</td>'
			. '</tr>';
	};
	$panel = function ( $filas_g, $titulo, $texto, $icono, $retardo ) use ( $fila_html ) {
		if ( ! $filas_g ) return '';
		$rows = '';
		foreach ( $filas_g as $f ) $rows .= $fila_html( $f );
		return '<div class="gr-dc-panel wow fade-in-bottom" data-wow-delay="' . $retardo . 'ms">'
			. '<div class="gr-dc-cab"><span class="gr-dc-cab-ic" aria-hidden="true"><i class="' . esc_attr( $icono ) . '"></i></span>'
			. '<div><h3>' . esc_html( $titulo ) . '</h3><p>' . esc_html( $texto ) . '</p></div></div>'
			. '<div class="gr-dcomp-wrap"><table class="gr-dcomp-table">'
			. '<caption class="grenvios-sr-only">' . esc_html( $titulo ) . ': plazo en días hábiles, forma de entrega e impuesto aproximado.</caption>'
			. '<thead><tr><th scope="col">Destino</th><th scope="col">Plazo</th><th scope="col">Entrega</th><th scope="col">Impuesto terrestre</th></tr></thead>'
			. '<tbody>' . $rows . '</tbody></table></div></div>';
	};

	$stat = function ( $icono, $num, $texto ) {
		return '<div class="gr-dc-stat"><span class="gr-dc-stat-ic" aria-hidden="true"><i class="' . esc_attr( $icono ) . '"></i></span>'
			. '<p><strong>' . esc_html( $num ) . '</strong> <span>' . esc_html( $texto ) . '</span></p></div>';
	};
	/* Rango de la ruta más rápida: «4 a 6 días» (sin repetir «hábiles»: va en la nota). */
	$rapida = trim( preg_replace( '/\s*d[ií]as?\s*h[aá]biles?\s*$/iu', '', (string) $top['tiempo'] ) );

	return '<section class="gr-dcomp gr-dcomp--v2 padding-bottom"><div class="container">'
		. '<div class="section-heading text-center mb-40">'
		. '<p class="sub-heading">Comparativa de rutas</p>'
		. '<h2>Plazo, vía e impuesto <span class="hl">de cada destino</span></h2>'
		. '<p>Los mismos cinco datos, uno al lado del otro. Pulsa en un país para ver su ficha completa: ciudades, qué admite su aduana y cómo se entrega.</p>'
		. '</div>'
		. '<div class="gr-dc-stats wow fade-in-bottom" data-wow-delay="100ms">'
		. $stat( 'fa-solid fa-truck', (string) count( $terr ), 'rutas con vía terrestre' )
		. $stat( 'fa-solid fa-plane', (string) count( $aire ), 'rutas solo aéreas' )
		. $stat( 'fa-regular fa-clock', $rapida . ' días', 'ruta más rápida' )
		. '</div>'
		. '<div class="gr-dc-grid">'
		. $panel( $terr, 'Aéreo + terrestre', 'Tu envío combina transporte aéreo y terrestre.', 'fa-solid fa-truck', 100 )
		. $panel( $aire, 'Solo vía aérea', 'Tu envío se transporta únicamente por vía aérea.', 'fa-solid fa-plane', 200 )
		. '</div>'
		. '<div class="gr-dcomp-nota gr-dc-nota wow fade-in-bottom" data-wow-delay="150ms"><span class="gr-dc-nota-ic" aria-hidden="true"><i class="fa-regular fa-circle-info"></i></span>'
		. '<p>Plazos en días hábiles desde el despacho. '
		. 'El impuesto de la vía terrestre es aproximado, se calcula sobre el valor declarado en la boleta o factura y se paga en {{origen_ciudad}} al despachar: '
		. 'quien recibe no lo paga. En las rutas aéreas depende del contenido y te lo confirmamos al cotizar.</p>'
		. '<a class="default-btn" href="' . esc_url( grenvios_url_base() . '/cotizar/' ) . '">Cotizar <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></div>'
		. '</div></section>';
}

/* ─────────────────────────────────────────────────────────────────────────
 * 3) Cifras junto a «Cada país tiene sus propias reglas»
 * ───────────────────────────────────────────────────────────────────────── */
add_filter( 'grenvios_pseo_aside', function ( $aside, $slug, $i ) {
	if ( $slug !== 'destinos' || $i !== 0 || ! grenvios_dh_activa() ) return $aside;
	$filas = grenvios_dh_filas();
	if ( ! $filas ) return $aside;

	$terr   = array_filter( $filas, function ( $f ) { return $f['terr']; } );
	$casa   = array_filter( $filas, function ( $f ) { return $f['casa']; } );
	$orden  = $filas;
	usort( $orden, 'grenvios_dh_cmp_plazo' );
	$rapida = $orden[0];
	$todas_sud = ! array_filter( $terr, function ( $f ) { return $f['region'] !== 'sud'; } );

	/* Diseño de la maqueta del cliente (2026-10-02, skill grenvios-landing):
	 * panel vino con título, cifras grandes separadas por una línea, notas en
	 * píldora y una caja «¿Tu país no aparece?» con botón y WhatsApp. */
	$cifra = function ( $num, $txt, $pildora = '' ) {
		return '<li><strong>' . esc_html( $num ) . '</strong><span>' . esc_html( $txt )
			. ( $pildora !== '' ? ' <em class="gr-ds-pill">' . esc_html( $pildora ) . '</em>' : '' ) . '</span></li>';
	};
	$f = function ( $k, $d ) { return function_exists( 'grenvios_field' ) ? grenvios_field( $k, $d ) : $d; };
	$plazo_rapido = $rapida['min'] === $rapida['max'] ? (string) $rapida['min'] : $rapida['min'] . '–' . $rapida['max'];
	$biz = function_exists( 'grenvios_biz' ) ? grenvios_biz() : array();
	$wa  = isset( $biz['wa_number'] ) ? preg_replace( '/\D/', '', (string) $biz['wa_number'] ) : '';

	return '<div class="gr-dstats gr-dstats--v2">'
		. '<p class="gr-dstats-t"><i class="fa-solid fa-route" aria-hidden="true"></i> ' . esc_html( $f( 'dh_ds_eye', 'Las rutas en cifras' ) ) . '</p>'
		. '<p class="gr-ds-titulo">' . esc_html( $f( 'dh_ds_t', 'Tu destino, en datos claros' ) ) . '</p>'
		. '<ul>'
		. $cifra( (string) count( $filas ), 'rutas con ficha propia y condiciones publicadas' )
		. $cifra( (string) count( $terr ), 'con vía terrestre', $todas_sud ? 'todas en Sudamérica' : '' )
		. $cifra( (string) count( $casa ), 'con entrega en el domicilio del destinatario' )
		. $cifra( $plazo_rapido, 'días hábiles a ' . $rapida['nombre'], 'la ruta más rápida' )
		. $cifra( '+30', 'destinos en total, varios bajo pedido' )
		. '</ul>'
		. '<div class="gr-ds-caja"><span class="gr-ds-caja-ic" aria-hidden="true"><i class="fa-regular fa-comment-dots"></i></span>'
		. '<p><strong>' . esc_html( $f( 'dh_ds_nota_t', '¿Tu país no aparece?' ) ) . '</strong><br>' . esc_html( $f( 'dh_ds_nota', 'Lo coordinamos bajo pedido. Te confirmamos vía y plazo reales.' ) ) . '</p></div>'
		. '<a class="default-btn" href="' . esc_url( grenvios_url_base() . '/cotizar/' ) . '">Cotizar mi envío <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>'
		. ( $wa !== '' ? '<a class="gr-ds-wa" href="https://wa.me/' . esc_attr( $wa ) . '" rel="nofollow noopener" target="_blank"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i> ' . esc_html( $f( 'dh_ds_wa', 'Consultar por WhatsApp' ) ) . '</a>' : '' )
		. '</div>';
}, 10, 3 );

/* ─────────────────────────────────────────────────────────────────────────
 * 4) «Lo que necesitas para enviar»: tercera sección de contenido
 *    Texto fijo y editable como las otras dos. Lo único que sale de datos es
 *    la línea de medicinas, que depende de lo que admite cada ruta.
 * ───────────────────────────────────────────────────────────────────────── */
add_filter( 'grenvios_pseo_secciones', function ( $m ) {
	$nueva = array(
		'sub'    => 'Antes de cotizar',
		'titulo' => 'Lo que necesitas para enviar <span class="hl">a cualquier destino</span>',
		'html'   => '<p>Da igual el país: con estos datos a mano la cotización sale a la primera y el envío no se detiene por un dato que falta.</p>
<ul class="gr-need">
<li><strong>Datos completos del destinatario.</strong> Nombre tal como figura en su documento, dirección con código postal y teléfono. Donde el envío se retira en agencia, el nombre tiene que coincidir con el documento con el que lo recoge.</li>
<li><strong>Tu documento de identidad.</strong> DNI, carné de extranjería o pasaporte: el envío sale a nombre del remitente.</li>
<li><strong>La lista del contenido y su valor.</strong> Es la base del impuesto en destino y de la cobertura del <a href="%H%/seguro-de-envios/">seguro</a>: declarar de menos no abarata, deja el envío cubierto por esa cifra menor.</li>
<li><strong>Boleta o factura.</strong> Obligatoria si lo que envías es mercancía nueva: la aduana la pide y sin ella el envío puede quedarse retenido.</li>
<li><strong>Peso y medidas de la caja cerrada.</strong> Con ellas se calcula el <a href="%H%/servicios/peso-volumetrico/">peso volumétrico</a>, que es lo que se cobra cuando la caja abulta más de lo que pesa.</li>
<li><strong>Receta médica.</strong> Imprescindible para enviar medicinas a los destinos que las admiten. Antes de comprar, mira <a href="%H%/que-se-puede-enviar/">qué acepta cada país</a>.</li>
</ul>
<p>¿Lo tienes? <a href="%H%/cotizar/">Pide tu cotización</a> con el destino y las medidas, o <a href="%H%/recojo-a-domicilio-lima/">pide que pasemos a recoger</a> y lo revisamos contigo en ese momento.</p>',
	);
	if ( ! isset( $m['destinos'] ) ) { $m['destinos'] = array( $nueva ); return $m; }
	$actual   = isset( $m['destinos']['html'] ) ? array( $m['destinos'] ) : array_values( $m['destinos'] );
	$actual[] = $nueva;
	$m['destinos'] = $actual;
	return $m;
}, 40 );

/* ─────────────────────────────────────────────────────────────────────────
 * 5) Preguntas que la página ya podía responder con sus datos
 * ───────────────────────────────────────────────────────────────────────── */
add_filter( 'grenvios_page_faqs', function ( $faqs, $slug ) {
	if ( $slug !== 'destinos' ) return $faqs;
	if ( function_exists( 'grenvios_hq_pais' ) && grenvios_hq_pais() !== '' ) return $faqs;
	$filas = grenvios_dh_filas();
	if ( ! $filas ) return $faqs;

	$orden = $filas;
	usort( $orden, 'grenvios_dh_cmp_plazo' );
	$r1 = $orden[0];
	$r2 = isset( $orden[1] ) ? $orden[1] : null;
	$lento = end( $orden );

	$casa = wp_list_pluck( array_filter( $filas, function ( $f ) { return $f['casa']; } ), 'nombre' );
	$agen = wp_list_pluck( array_filter( $filas, function ( $f ) { return ! $f['casa']; } ), 'nombre' );

	$imp = array();
	foreach ( $filas as $f ) {
		if ( $f['terr'] && $f['impuesto'] !== '' ) $imp[] = grenvios_dh_pct( $f['impuesto'] ) . ' en ' . $f['nombre'];
	}

	$nuevas = array();
	$nuevas[] = array(
		'¿Cuál es la ruta más rápida desde {{origen_ciudad}}?',
		$r1['nombre'] . ', con ' . $r1['tiempo'] . ' por vía aérea'
			. ( $r2 ? ', seguida de ' . $r2['nombre'] . ' (' . $r2['tiempo'] . ')' : '' ) . '. '
			. 'La más larga de las rutas con ficha es ' . $lento['nombre'] . ', con ' . $lento['tiempo'] . '. '
			. 'Los plazos se cuentan desde el despacho, no desde el día en que cotizas.',
	);
	if ( $casa ) {
		$nuevas[] = array(
			'¿En qué países entregan a domicilio?',
			'En ' . grenvios_dh_enumerar( $casa ) . ' el envío llega a la dirección del destinatario.'
				. ( $agen ? ' En ' . grenvios_dh_enumerar( $agen ) . ' se retira en una agencia local de su ciudad, presentando el documento a cuyo nombre va el envío.' : '' ),
		);
	}
	if ( $imp ) {
		$nuevas[] = array(
			'¿Quién paga el impuesto en los envíos por tierra?',
			'El remitente, en {{origen_ciudad}}, al despachar. Es aproximado y se calcula sobre el valor declarado: '
				. grenvios_dh_enumerar( $imp ) . '. Quien recibe no paga ese impuesto al recoger el envío.',
		);
	}

	$vistas = array();
	foreach ( (array) $faqs as $q ) $vistas[] = mb_strtolower( trim( wp_strip_all_tags( (string) $q[0] ) ) );
	foreach ( $nuevas as $q ) {
		if ( ! in_array( mb_strtolower( $q[0] ), $vistas, true ) ) $faqs[] = $q;
	}
	return $faqs;
}, 30, 2 );


/* Panel: imagen de la cabecera y texto del buscador de /destinos/. */
add_filter( 'grenvios_text_registry', function ( $reg ) {
	if ( ! isset( $reg['destinos'] ) ) return $reg;
	$reg['destinos']['sections']['dh_extra'] = array(
		'label'           => 'Destinos · Imagen y buscador',
		'_no_token_check' => true,
		'sel'             => '.gr-dh-hero',
		'fields'          => array(
			'dh_img'    => array( 'Imagen de la cabecera (vacía = avión en pista)', 'image', '' ),
			'dh_buscar' => array( 'Texto del buscador', 'text', 'Buscar país o ciudad' ),
			'dh_vacio'  => array( 'Aviso cuando la búsqueda no encuentra nada', 'text', 'No encontramos ese destino en la lista. Escríbenos y te confirmamos si llegamos.' ),
		),
	);
	return $reg;
}, 45 );


/* Región de un destino sin ficha, por su código ISO (el de su bandera). */
function grenvios_dh_region_otro( $iso ) {
	$america = array( 'br','pa','cr','pr','uy','py','ca','mx','co','ec','cl','ar','bo','pe','ve','cu','us','do','gt','sv','hn','ni','ht','jm','tt','bs','bz','gy','sr' );
	$europa  = array( 'it','fr','de','es','pt','gb','uk','nl','be','ch','at','se','no','dk','fi','ie','pl','gr','cz','hu','ro','bg','hr','ru','ua','tr' );
	$iso = strtolower( (string) $iso );
	if ( in_array( $iso, $america, true ) ) return 'america';
	if ( in_array( $iso, $europa, true ) )  return 'europa';
	return 'asia';
}

/* Convierte la lista plana `ul.gr-otros` en paneles por región con buscador.
 * Lee los datos de la propia lista (país, plazo, enlace, bandera): no inventa
 * ninguno. Si el marcado no es el esperado, devuelve el HTML tal cual. */
function grenvios_dh_otros_agrupados( $html ) {
	$a = strpos( $html, '<ul class="gr-otros"' );
	$b = $a === false ? false : strpos( $html, '</ul>', $a );
	if ( $b === false ) return $html;
	$lista = substr( $html, $a, $b + 5 - $a );

	if ( ! preg_match_all( '~<li class="gr-otro"><a href="([^"]*)">(<img[^>]*>)?<span class="gr-otro-pais">([^<]*)</span><span class="gr-otro-tiempo"><i[^>]*></i>\s*([^<]*)</span><span class="gr-otro-btn">([^<]*)<i[^>]*></i></span></a></li>~u', $lista, $m, PREG_SET_ORDER ) ) {
		return $html;
	}
	$grupos = array(
		'america' => array( 'América',       'fa-earth-americas', array() ),
		'europa'  => array( 'Europa',        'fa-plane',          array() ),
		'asia'    => array( 'Asia-Pacífico', 'fa-globe',          array() ),
	);
	foreach ( $m as $x ) {
		$iso = preg_match( '~/flags(?:-svg)?/([a-z]{2})\.(?:png|svg)~', (string) $x[2], $mi ) ? $mi[1] : '';
		$r   = grenvios_dh_region_otro( $iso );
		$grupos[ $r ][2][] = '<li class="gr-og-item"><a href="' . esc_url( html_entity_decode( $x[1] ) ) . '">' . $x[2]
			. '<span class="gr-og-tx"><strong>' . esc_html( html_entity_decode( $x[3] ) ) . '</strong>'
			. '<span><i class="fa-regular fa-clock" aria-hidden="true"></i> ' . esc_html( html_entity_decode( trim( $x[4] ) ) ) . '</span></span>'
			. '<span class="gr-og-cot">' . esc_html( html_entity_decode( trim( $x[5] ) ) ) . ' <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span></a></li>';
	}

	$ph = function_exists( 'grenvios_field' ) ? grenvios_field( 'dh_otros_buscar', 'Buscar otro destino' ) : 'Buscar otro destino';
	$out = '<div class="gr-og-wrap"><label class="gr-dsearch gr-og-search"><i class="fa-regular fa-magnifying-glass" aria-hidden="true"></i>'
		. '<input type="search" class="gr-og-in" placeholder="' . esc_attr( $ph ) . '" aria-label="' . esc_attr( $ph ) . '" autocomplete="off"></label>'
		. '<div class="gr-og gr-og--n' . count( array_filter( $grupos, function ( $g ) { return $g[2]; } ) ) . '">';
	foreach ( $grupos as $k => $g ) {
		if ( ! $g[2] ) continue;
		$n = count( $g[2] );
		$out .= '<div class="gr-og-panel wow fade-in-bottom" data-wow-delay="' . ( $k === 'america' ? 100 : ( $k === 'europa' ? 200 : 300 ) ) . 'ms">'
			. '<div class="gr-og-cab"><span class="gr-og-cab-ic" aria-hidden="true"><i class="fa-solid ' . esc_attr( $g[1] ) . '"></i></span>'
			. '<div><h3>' . esc_html( $g[0] ) . '</h3><p><span class="gr-og-n">' . $n . '</span> ' . ( $n === 1 ? 'destino' : 'destinos' ) . '</p></div></div>'
			. '<ul class="gr-og-lista">' . implode( '', $g[2] ) . '</ul></div>';
	}
	$out .= '</div><p class="gr-og-vacio" hidden>No vemos ese destino en la lista, pero cubrimos más de treinta países: escríbenos y te lo confirmamos.</p></div>'
		. '<script>(function(){var w=document.querySelector(".gr-og-wrap");if(!w)return;var inp=w.querySelector(".gr-og-in"),vac=w.querySelector(".gr-og-vacio");'
		. 'function norm(s){return s.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g,"");}'
		. 'if(inp)inp.addEventListener("input",function(){var q=norm(inp.value.trim()),tot=0;'
		. 'w.querySelectorAll(".gr-og-panel").forEach(function(p){var n=0;p.querySelectorAll(".gr-og-item").forEach(function(li){var ok=!q||norm(li.textContent).indexOf(q)>-1;li.hidden=!ok;if(ok)n++;});'
		. 'var c=p.querySelector(".gr-og-n");if(c&&!q)c.textContent=p.querySelectorAll(".gr-og-item").length;else if(c)c.textContent=n;p.hidden=n===0;tot+=n;});'
		. 'if(vac)vac.hidden=tot>0;});})();</script>';

	return substr( $html, 0, $a ) . $out . substr( $html, $b + 5 );
}

add_filter( 'grenvios_text_registry', function ( $reg ) {
	if ( ! isset( $reg['destinos']['sections']['dh_extra'] ) ) return $reg;
	$reg['destinos']['sections']['dh_extra']['fields']['dh_otros_buscar'] = array( 'Buscador de «Otros destinos»', 'text', 'Buscar otro destino' );
	return $reg;
}, 46 );


/* «Cómo leer esta página»: subtítulo y tarjeta «Los 5 datos que deciden tu
 * envío» antes del texto (maqueta 2026-10-02). Se inserta en la pasada final
 * para no mezclar marcado con el cuerpo editable de la sección. */
add_filter( 'grenvios_html_final', function ( $html ) {
	if ( ! grenvios_dh_activa() || ! is_string( $html ) || strpos( $html, 'gr-pseo--destinos gr-pseo--n1' ) === false || strpos( $html, 'gr-d5' ) !== false ) return $html;
	$a = strpos( $html, 'gr-pseo--destinos gr-pseo--n1' );
	$marca = '</h2></div><div class="gr-pseo-body">';
	$b = strpos( $html, $marca, $a );
	if ( $b === false ) return $html;

	$f = function ( $k, $d ) { return function_exists( 'grenvios_field' ) ? grenvios_field( $k, $d ) : $d; };
	$lead  = $f( 'dh_lead', 'La vía, el plazo, la entrega y la aduana cambian según el destino. Compáralos antes de cotizar.' );
	$titulo = $f( 'dh_d5_t', 'Los 5 datos que deciden tu envío' );
	$lineas = $f( 'dh_d5_items', "Modalidad | Aéreo, terrestre o ambos\nPlazo | Días hábiles desde {{origen_ciudad}}\nEntrega | A domicilio o en agencia\nAduana | Qué admite cada país\nCiudades | Principales destinos" );
	$iconos = array( 'fa-truck', 'fa-calendar', 'fa-house', 'fa-file-lines', 'fa-location-dot' );

	$items = ''; $n = 0;
	foreach ( preg_split( '/\r\n|\r|\n/', (string) $lineas ) as $l ) {
		$l = trim( $l );
		if ( $l === '' ) continue;
		$partes = array_map( 'trim', explode( '|', $l, 2 ) );
		$items .= '<li class="gr-d5-item"><span class="gr-d5-n">' . sprintf( '%02d', ++$n ) . '</span>'
			. '<span class="gr-d5-ic" aria-hidden="true"><i class="fa-regular ' . esc_attr( isset( $iconos[ $n - 1 ] ) ? $iconos[ $n - 1 ] : 'fa-circle-check' ) . '"></i></span>'
			. '<span class="gr-d5-tx"><strong>' . esc_html( $partes[0] ) . '</strong>' . ( isset( $partes[1] ) ? '<span>' . esc_html( $partes[1] ) . '</span>' : '' ) . '</span></li>';
	}
	$card = '';
	if ( $items !== '' ) {
		$card = '<div class="gr-d5"><h3>' . esc_html( $titulo ) . '</h3><ul class="gr-d5-lista">' . $items . '</ul></div>';
		if ( function_exists( 'grenvios_apply_text_tokens' ) ) $card = grenvios_apply_text_tokens( $card );
	}
	$ins = '</h2>' . ( trim( (string) $lead ) !== '' ? '<p class="gr-pseo-lead">' . esc_html( $lead ) . '</p>' : '' ) . '</div><div class="gr-pseo-body">' . $card;
	return substr( $html, 0, $b ) . $ins . substr( $html, $b + strlen( $marca ) );
}, 41 );

add_filter( 'grenvios_text_registry', function ( $reg ) {
	if ( ! isset( $reg['destinos']['sections']['dh_extra'] ) ) return $reg;
	$reg['destinos']['sections']['dh_reglas'] = array(
		'label'           => 'Destinos · Cómo leer esta página (datos y panel)',
		'_no_token_check' => true,
		'sel'             => '.gr-d5',
		'fields'          => array(
			'dh_lead'      => array( 'Subtítulo bajo el título', 'textarea', 'La vía, el plazo, la entrega y la aduana cambian según el destino. Compáralos antes de cotizar.' ),
			'dh_d5_t'      => array( 'Tarjeta · Título', 'text', 'Los 5 datos que deciden tu envío' ),
			'dh_d5_items'  => array( 'Tarjeta · Datos (una línea por dato: Título | descripción)', 'textarea', "Modalidad | Aéreo, terrestre o ambos\nPlazo | Días hábiles desde {{origen_ciudad}}\nEntrega | A domicilio o en agencia\nAduana | Qué admite cada país\nCiudades | Principales destinos" ),
			'dh_ds_eye'    => array( 'Panel lateral · Antetítulo', 'text', 'Las rutas en cifras' ),
			'dh_ds_t'      => array( 'Panel lateral · Título', 'text', 'Tu destino, en datos claros' ),
			'dh_ds_nota_t' => array( 'Panel lateral · Caja · Título', 'text', '¿Tu país no aparece?' ),
			'dh_ds_nota'   => array( 'Panel lateral · Caja · Texto', 'text', 'Lo coordinamos bajo pedido. Te confirmamos vía y plazo reales.' ),
			'dh_ds_wa'     => array( 'Panel lateral · Enlace de WhatsApp', 'text', 'Consultar por WhatsApp' ),
		),
	);
	return $reg;
}, 47 );


/* «Antes de elegir · Qué mirar en la ficha de cada destino»: se rehace con el
 * diseño de la maqueta (2026-10-02, skill grenvios-landing) a partir del texto
 * de la propia sección: título y subtítulo centrados, foto con tarjeta
 * «Compara antes de cotizar» a la izquierda, cinco tarjetas numeradas (3 + 2)
 * y caja final. Si el marcado no es el esperado, se deja la sección como está. */
add_filter( 'grenvios_html_final', function ( $html ) {
	if ( ! grenvios_dh_activa() || ! is_string( $html ) || strpos( $html, 'gr-pseo--destinos gr-pseo--n2' ) === false || strpos( $html, 'gr-n2v2' ) !== false ) return $html;
	$a = strpos( $html, 'gr-pseo--destinos gr-pseo--n2' );
	$a = strrpos( substr( $html, 0, $a ), '<section' );
	$b = $a === false ? false : strpos( $html, '</section>', $a );
	if ( $b === false ) return $html;
	$sec = substr( $html, $a, $b + 10 - $a );

	if ( ! preg_match( '~<p class="sub-heading">(.*?)</p><h2>(.*?)</h2>~s', $sec, $mh ) ) return $html;
	if ( ! preg_match( '~<div class="gr-pseo-body">(.*?)</div></div><aside~s', $sec, $mb ) ) return $html;
	$body = $mb[1];
	if ( ! preg_match( '~^\s*<p>(.*?)</p>~s', $body, $ml ) ) return $html;
	if ( ! preg_match_all( '~<li[^>]*>\s*<strong>(.*?)</strong>(.*?)</li>~s', $body, $mi, PREG_SET_ORDER ) || count( $mi ) < 3 ) return $html;
	$cierre = '';
	if ( preg_match( '~</ol>\s*<p>(.*?)</p>\s*$~s', $body, $mc ) ) {
		/* El título de la caja ya dice «¿Tu país no tiene ficha propia?»: se quita la frase repetida. */
		$cierre = preg_replace( '/^\s*Si tu país no tiene ficha propia, no significa que no lleguemos:\s*(\p{L})/u', '$1', $mc[1], 1, $quitado );
		if ( $quitado ) $cierre = mb_strtoupper( mb_substr( $cierre, 0, 1 ) ) . mb_substr( $cierre, 1 );
	}
	$img = preg_match( '~<img src="([^"]+)"~', $sec, $mimg ) ? $mimg[1] : '';

	$f = function ( $k, $d ) { return function_exists( 'grenvios_field' ) ? grenvios_field( $k, $d ) : $d; };
	$lead = preg_replace( '/:\s*$/u', '.', trim( wp_strip_all_tags( $ml[1] ) ) );
	$iconos = array( 'fa-plane', 'fa-calendar', 'fa-box', 'fa-file-lines', 'fa-location-dot' );

	$cards = '';
	foreach ( $mi as $i => $x ) {
		$titulo = trim( preg_replace( '/[.:]\s*$/u', '', wp_strip_all_tags( $x[1] ) ) );
		$desc   = trim( preg_replace( '/^\s*y,\s*/iu', '', $x[2] ) );
		$desc   = $desc !== '' ? mb_strtoupper( mb_substr( $desc, 0, 1 ) ) . mb_substr( $desc, 1 ) : '';
		$cards .= '<li class="gr-n2-card' . ( $i === 0 ? ' is-destacada' : '' ) . ' wow fade-in-bottom" data-wow-delay="' . ( 100 + $i * 90 ) . 'ms">'
			. '<span class="gr-n2-n">' . sprintf( '%02d', $i + 1 ) . '</span>'
			. '<span class="gr-n2-ic" aria-hidden="true"><i class="fa-regular ' . esc_attr( isset( $iconos[ $i ] ) ? $iconos[ $i ] : 'fa-circle-check' ) . '"></i></span>'
			. '<span class="gr-n2-tx"><strong>' . esc_html( $titulo ) . '</strong><span>' . wp_kses_post( $desc ) . '</span></span></li>';
	}

	$foto = $img !== ''
		? '<figure class="gr-n2-foto wow fade-in-left" data-wow-delay="150ms"><img src="' . esc_url( $img ) . '" alt="" loading="lazy" decoding="async">'
			. '<div class="gr-n2-comp"><span class="gr-n2-comp-ic" aria-hidden="true"><i class="fa-solid fa-arrow-right-arrow-left"></i></span>'
			. '<p><strong>' . esc_html( $f( 'dh_n2_comp_t', 'Compara antes de cotizar' ) ) . '</strong><span>' . esc_html( $f( 'dh_n2_comp', 'Los datos cambian según cada destino.' ) ) . '</span></p></div></figure>'
		: '';
	$caja = $cierre !== ''
		? '<div class="gr-n2-caja wow fade-in-bottom" data-wow-delay="200ms"><span class="gr-n2-caja-ic" aria-hidden="true"><i class="fa-regular fa-circle-info"></i></span>'
			. '<div><strong>' . esc_html( $f( 'dh_n2_caja_t', '¿Tu país no tiene ficha propia?' ) ) . '</strong><p>' . wp_kses_post( $cierre ) . '</p></div></div>'
		: '';

	$nueva = '<section class="gr-pseo gr-pseo--destinos gr-pseo--n2 gr-n2v2 padding-bottom"><div class="container">'
		. '<div class="section-heading text-center mb-40"><p class="sub-heading">' . $mh[1] . '</p><h2>' . $mh[2] . '</h2>'
		. '<p class="gr-n2-lead">' . esc_html( $lead ) . '</p></div>'
		. '<div class="gr-n2-grid">' . $foto . '<div class="gr-n2-der"><ul class="gr-n2-cards">' . $cards . '</ul>' . $caja . '</div></div>'
		. '</div></section>';
	return substr( $html, 0, $a ) . $nueva . substr( $html, $b + 10 );
}, 42 );

add_filter( 'grenvios_text_registry', function ( $reg ) {
	if ( ! isset( $reg['destinos']['sections']['dh_extra'] ) ) return $reg;
	$reg['destinos']['sections']['dh_n2'] = array(
		'label'           => 'Destinos · Qué mirar en la ficha (foto y caja)',
		'_no_token_check' => true,
		'sel'             => '.gr-n2v2',
		'fields'          => array(
			'dh_n2_comp_t' => array( 'Tarjeta sobre la foto · Título', 'text', 'Compara antes de cotizar' ),
			'dh_n2_comp'   => array( 'Tarjeta sobre la foto · Texto', 'text', 'Los datos cambian según cada destino.' ),
			'dh_n2_caja_t' => array( 'Caja final · Título', 'text', '¿Tu país no tiene ficha propia?' ),
		),
	);
	return $reg;
}, 48 );


/* «Antes de cotizar · Lo que necesitas para enviar a cualquier destino»: se
 * rehace con el diseño de la maqueta (2026-10-02, skill grenvios-landing) a
 * partir del texto de la propia sección: cabecera, foto con tarjeta «Prepara
 * todo antes de cotizar», «Checklist para tu envío» con seis tarjetas
 * numeradas y franja vino «¿Ya tienes todo listo?». Si el marcado no es el
 * esperado, se deja la sección como está. */
add_filter( 'grenvios_html_final', function ( $html ) {
	if ( ! grenvios_dh_activa() || ! is_string( $html ) || strpos( $html, 'gr-pseo--destinos gr-pseo--n3' ) === false || strpos( $html, 'gr-n3v2' ) !== false ) return $html;
	$a = strpos( $html, 'gr-pseo--destinos gr-pseo--n3' );
	$a = strrpos( substr( $html, 0, $a ), '<section' );
	$b = $a === false ? false : strpos( $html, '</section>', $a );
	if ( $b === false ) return $html;
	$sec = substr( $html, $a, $b + 10 - $a );

	if ( ! preg_match( '~<p class="sub-heading">(.*?)</p><h2>(.*?)</h2>~s', $sec, $mh ) ) return $html;
	if ( ! preg_match( '~<div class="gr-pseo-body">(.*?)</div></div><aside~s', $sec, $mb ) ) return $html;
	$body = $mb[1];
	if ( ! preg_match( '~^\s*<p>(.*?)</p>~s', $body, $ml ) ) return $html;
	if ( ! preg_match_all( '~<li[^>]*>\s*<strong>(.*?)</strong>(.*?)</li>~s', $body, $mi, PREG_SET_ORDER ) || count( $mi ) < 3 ) return $html;
	$img = preg_match( '~<img src="([^"]+)"~', $sec, $mimg ) ? $mimg[1] : '';

	/* Enlaces de la frase final («Pide tu cotización… pide que pasemos a recoger…»). */
	$base = function_exists( 'grenvios_url_base' ) ? grenvios_url_base() : home_url();
	$u1 = $base . '/cotizar/'; $u2 = $base . '/recojo-a-domicilio-lima/';
	if ( preg_match( '~</ul>\s*<p>(.*?)</p>\s*$~s', $body, $mc ) && preg_match_all( '~<a href="([^"]+)"~', $mc[1], $ma ) ) {
		if ( isset( $ma[1][0] ) ) $u1 = html_entity_decode( $ma[1][0] );
		if ( isset( $ma[1][1] ) ) $u2 = html_entity_decode( $ma[1][1] );
	}

	$f = function ( $k, $d ) { return function_exists( 'grenvios_field' ) ? grenvios_field( $k, $d ) : $d; };
	$lead   = trim( wp_strip_all_tags( $ml[1] ) );
	$iconos = array( 'fa-user', 'fa-id-card', 'fa-box', 'fa-receipt', 'fa-weight-scale', 'fa-file-medical' );
	$total  = count( $mi );

	$cards = '';
	foreach ( $mi as $i => $x ) {
		$titulo = trim( preg_replace( '/[.:]\s*$/u', '', wp_strip_all_tags( $x[1] ) ) );
		$cards .= '<li class="gr-n3-card' . ( $i === 2 || $i === 4 ? ' is-destacada' : '' ) . ' wow fade-in-bottom" data-wow-delay="' . ( 100 + ( $i % 2 ) * 90 + intdiv( $i, 2 ) * 40 ) . 'ms">'
			. '<span class="gr-n3-ic" aria-hidden="true"><i class="fa-regular ' . esc_attr( isset( $iconos[ $i ] ) ? $iconos[ $i ] : 'fa-circle-check' ) . '"></i></span>'
			. '<span class="gr-n3-hd"><span class="gr-n3-n">' . sprintf( '%02d', $i + 1 ) . '</span><strong>' . esc_html( $titulo ) . '</strong></span>'
			. '<span class="gr-n3-ds">' . wp_kses_post( trim( $x[2] ) ) . '</span>'
			. '<span class="gr-n3-ok" aria-hidden="true"><i class="fa-solid fa-check"></i></span></li>';
	}

	$foto = $img !== ''
		? '<figure class="gr-n3-foto wow fade-in-left" data-wow-delay="150ms"><img src="' . esc_url( $img ) . '" alt="" loading="lazy" decoding="async">'
			. '<div class="gr-n3-prep"><span class="gr-n3-prep-ic" aria-hidden="true"><i class="fa-regular fa-box"></i></span>'
			. '<p><strong>' . esc_html( $f( 'dh_n3_prep_t', 'Prepara todo antes de cotizar' ) ) . '</strong><span>' . esc_html( $f( 'dh_n3_prep', 'Los datos que necesitas para despachar sin contratiempos.' ) ) . '</span></p></div></figure>'
		: '';

	$nueva = '<section class="gr-pseo gr-pseo--destinos gr-pseo--n3 gr-n3v2 padding-bottom"><div class="container">'
		. '<div class="section-heading mb-40"><p class="sub-heading">' . $mh[1] . '</p><h2>' . $mh[2] . '</h2><p class="gr-n3-lead">' . esc_html( $lead ) . '</p></div>'
		. '<div class="gr-n3-grid">' . $foto
		. '<div class="gr-n3-der"><div class="gr-n3-cab"><h3>' . esc_html( $f( 'dh_n3_check_t', 'Checklist para tu envío' ) ) . '</h3><span class="gr-n3-pill"><strong>' . $total . '</strong> requisitos</span></div>'
		. '<ul class="gr-n3-cards">' . $cards . '</ul></div></div>'
		. '<div class="gr-n3-cta wow fade-in-bottom" data-wow-delay="150ms"><div class="gr-n3-cta-tx"><strong>' . esc_html( $f( 'dh_n3_cta_t', '¿Ya tienes todo listo?' ) ) . '</strong><span>' . esc_html( $f( 'dh_n3_cta', 'Envíanos el destino, el peso y las medidas.' ) ) . '</span></div>'
		. '<div class="gr-n3-cta-bt"><a class="gr-n3-b1" href="' . esc_url( $u1 ) . '">' . esc_html( $f( 'dh_n3_b1', 'Pedir mi cotización' ) ) . ' <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>'
		. '<a class="gr-n3-b2" href="' . esc_url( $u2 ) . '"><i class="fa-solid fa-truck" aria-hidden="true"></i> ' . esc_html( $f( 'dh_n3_b2', 'Solicitar recojo' ) ) . '</a>'
		. '<small>' . esc_html( $f( 'dh_n3_nota', 'Revisamos contigo cada dato antes del despacho.' ) ) . '</small></div></div>'
		. '</div></section>';
	return substr( $html, 0, $a ) . $nueva . substr( $html, $b + 10 );
}, 43 );

add_filter( 'grenvios_text_registry', function ( $reg ) {
	if ( ! isset( $reg['destinos']['sections']['dh_extra'] ) ) return $reg;
	$reg['destinos']['sections']['dh_n3'] = array(
		'label'           => 'Destinos · Lo que necesitas (checklist y franja)',
		'_no_token_check' => true,
		'sel'             => '.gr-n3v2',
		'fields'          => array(
			'dh_n3_prep_t'  => array( 'Tarjeta sobre la foto · Título', 'text', 'Prepara todo antes de cotizar' ),
			'dh_n3_prep'    => array( 'Tarjeta sobre la foto · Texto', 'text', 'Los datos que necesitas para despachar sin contratiempos.' ),
			'dh_n3_check_t' => array( 'Título del checklist', 'text', 'Checklist para tu envío' ),
			'dh_n3_cta_t'   => array( 'Franja vino · Título', 'text', '¿Ya tienes todo listo?' ),
			'dh_n3_cta'     => array( 'Franja vino · Texto', 'text', 'Envíanos el destino, el peso y las medidas.' ),
			'dh_n3_b1'      => array( 'Franja vino · Botón 1', 'text', 'Pedir mi cotización' ),
			'dh_n3_b2'      => array( 'Franja vino · Botón 2', 'text', 'Solicitar recojo' ),
			'dh_n3_nota'    => array( 'Franja vino · Nota', 'text', 'Revisamos contigo cada dato antes del despacho.' ),
		),
	);
	return $reg;
}, 49 );
