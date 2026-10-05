<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Secciones SEO de la página de destino
 * ══════════════════════════════════════════════════════════════════════════
 *
 * La plantilla de destino —la que sirve /destinos/ecuador/ y también la portada
 * de cada ruta, /ec/envios-a-ecuador/— tenía seis secciones y ninguna respondía
 * a lo que de verdad se busca: cuánto cuesta, cuánto tarda, qué pide la aduana,
 * qué vía conviene.
 *
 * QUÉ SE AÑADE
 *
 *   Precio           «cuánto cuesta enviar a X» es la consulta más repetida.
 *   Plazos           tabla por modalidad y qué puede alargarla.
 *   Qué se envía     lo que más pasa por esa ruta (campo editable).
 *   Prohibidos       «qué no se puede enviar a X» (campo editable).
 *   Documentación    «qué documentos necesito» (campo editable).
 *   Embalaje         cómo preparar el bulto según la vía.
 *   Errores          los cinco fallos que retienen un envío. Cola larga pura.
 *   Cobertura        cada ciudad es una búsqueda: «envíos a Guayaquil».
 *   Comparativa      «aéreo o terrestre a X», solo donde existen las dos.
 *   Enlaces          la que más pesa: la portada del país no enlazaba a NINGUNA
 *                    de sus 24 páginas, así que la ruta era un montón de
 *                    páginas sueltas sin eje. Este bloque las une.
 *   Guías            las entradas del blog de esa ruta.
 *   FAQ              con marcado FAQPage.
 *
 * DISEÑO: SE USA EL VOCABULARIO DEL TEMA, NO UNO NUEVO
 *
 * Una primera versión pintaba todo con titular centrado, tarjetas planas y una
 * tabla. Encajaba de color, pero al lado de las secciones originales —que llevan
 * tarjetas con icono, pasos numerados y paneles a dos columnas— se notaba que
 * eran de otra mano. Aquí se reutilizan los componentes que el tema ya tiene y
 * ya estila: `srv-card` con `srv-card-ic`, `srv-steps`, `srv-times`,
 * `srv-two-grid` con `srv-panel` y `srv-features-grid`.
 *
 * LA REGLA QUE NO SE SALTA
 *
 * Las secciones que dependen de un dato que solo conoce quien despacha —qué no
 * admite esa aduana, qué documentación pide, a qué ciudades se llega— se pintan
 * únicamente si ese campo está relleno en *Destinos → Contenido por país*. Vacío,
 * no hay sección. Inventar una norma aduanera porque suena verosímil es darle a
 * un cliente algo que le pueden rechazar en el mostrador.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ─────────────────────────────────────────────────────────────────────────
 * Utilidades
 * ───────────────────────────────────────────────────────────────────────── */

function grenvios_dsec_datos( $slug, $d ) {
	if ( function_exists( 'grenvios_pais_datos' ) ) {
		$x = grenvios_pais_datos( $slug );
		if ( $x ) return $x;
	}
	$d['slug']     = $slug;
	$d['aereo']    = stripos( $d['modos'], 'aére' ) !== false || stripos( $d['modos'], 'aere' ) !== false;
	$d['terr']     = stripos( $d['modos'], 'terrestre' ) !== false;
	$d['casa']     = ( stripos( $d['entrega'], 'domicilio' ) !== false || stripos( $d['entrega'], 'puerta' ) !== false );   // «Puerta a puerta» también es a domicilio
	$d['impuesto'] = preg_match( '/(\d+(?:[.,]\d+)?)\s*%/u', (string) $d['restr'], $m ) ? str_replace( ',', '.', $m[1] ) : '';
	foreach ( array( 'ciudades', 'top', 'prohibidos', 'documentos', 'embalaje', 'comunidad' ) as $k ) {
		if ( ! isset( $d[ $k ] ) ) $d[ $k ] = '';
	}
	return $d;
}

/* Cabecera de sección con el mismo patrón que `srv-head` del tema. */
function grenvios_dsec_open( $clase, $sub, $titulo, $intro = '' ) {
	echo '<section class="srv-section dest-seo-sec ' . esc_attr( $clase ) . ' padding"><div class="container">'
		. '<div class="srv-head text-center">'
		. ( $sub !== '' ? '<h3 class="sub-heading">' . esc_html( $sub ) . '</h3>' : '' )
		. '<h2>' . wp_kses_post( $titulo ) . '</h2>'
		. ( $intro !== '' ? '<p class="srv-intro">' . wp_kses_post( $intro ) . '</p>' : '' )
		. '</div>';
}

function grenvios_dsec_close() {
	echo '</div></section>';
}

/* Tarjeta con icono, sobre el componente del tema.
 *
 * `srv-card` viene centrado y `srv-cards-2` lo mete en una caja de 720 px: bien
 * para tres tarjetas de una línea, mal para cuatro con un párrafo y un enlace
 * dentro —quedaban dos por fila y el texto centrado, que se lee fatal—. Aquí se
 * usa la rejilla de Bootstrap del tema, la misma del bloque de enlaces, y el
 * texto se alinea a la izquierda. */
function grenvios_dsec_card( $icono, $titulo, $texto, $col = 'col-lg-3 col-md-6' ) {
	static $n = 0;   // escalonado dentro de cada fila de cuatro
	$delay = ( 100 + ( $n++ % 4 ) * 110 ) . 'ms';
	return '<div class="' . esc_attr( $col ) . '"><div class="srv-card dest-seo-card wow fade-in-bottom" data-wow-delay="' . $delay . '">'
		. '<span class="srv-card-ic"><i class="' . esc_attr( $icono ) . '"></i></span>'
		. '<h3 class="srv-card-title">' . esc_html( $titulo ) . '</h3>'
		. '<p>' . wp_kses_post( $texto ) . '</p></div></div>';
}

function grenvios_dsec_lista( $txt ) {
	return function_exists( 'grenvios_pais_lista' ) ? grenvios_pais_lista( $txt )
		: array_values( array_filter( array_map( 'trim', explode( ',', (string) $txt ) ) ) );
}

/* La página de una ruta que corresponde a un slug maestro. */
function grenvios_dsec_url( $slug_maestro, $lang = '' ) {
	$p = get_page_by_path( $slug_maestro );
	if ( ! $p ) $p = get_page_by_path( 'servicios/' . $slug_maestro );
	if ( ! $p ) return '';
	if ( $lang !== '' && function_exists( 'pll_get_post' ) ) {
		$t = (int) pll_get_post( $p->ID, $lang );
		if ( $t && get_post_status( $t ) === 'publish' ) return get_permalink( $t );
	}
	return get_permalink( $p->ID );
}

function grenvios_dsec_lang( $slug ) {
	if ( ! function_exists( 'grenvios_sedes' ) || ! function_exists( 'grenvios_sede_destino_propio' ) ) return '';
	foreach ( array_keys( grenvios_sedes() ) as $l ) {
		if ( grenvios_sede_destino_propio( $l ) === $slug ) return $l;
	}
	return '';
}

/* ─────────────────────────────────────────────────────────────────────────
 * Posición de un país dentro de la red
 *
 * Todo lo que se afirma aquí sale de comparar los datos que la clienta ya tiene
 * cargados en cada destino: plazos, modalidades, entrega e impuesto. Nada se
 * inventa y nada se codifica a mano, así que si mañana cambia el plazo de un
 * país las frases se recalculan solas y ninguna queda mintiendo.
 * ───────────────────────────────────────────────────────────────────────── */

/* Días mínimos de un plazo escrito en texto: «8 a 10 días hábiles» → 8. */
function grenvios_dsec_dias( $tiempo ) {
	return preg_match( '/(\d+)/', (string) $tiempo, $m ) ? (int) $m[1] : 0;
}

/* Tabla de todos los destinos con sus datos comparables. */
function grenvios_dsec_red() {
	static $red = null;
	if ( $red !== null ) return $red;
	$red = array();
	foreach ( grenvios_destinos() as $sl => $x ) {
		$imp = preg_match( '/(\d+(?:[.,]\d+)?)\s*%/u', (string) $x['restr'], $m ) ? (float) str_replace( ',', '.', $m[1] ) : null;
		$red[ $sl ] = array(
			'title'    => $x['title'],
			'tiempo'   => $x['tiempo'],
			'modos'    => $x['modos'],
			'entrega'  => $x['entrega'],
			'dias'     => grenvios_dsec_dias( $x['tiempo'] ),
			'impuesto' => $imp,
			'terr'     => stripos( $x['modos'], 'terrestre' ) !== false,
			'casa'     => stripos( $x['entrega'], 'domicilio' ) !== false || stripos( $x['entrega'], 'puerta' ) !== false,
		);
	}
	return $red;
}

/* Frases verdaderas sobre la posición de este país. Devuelve las que apliquen. */
function grenvios_dsec_posicion( $slug ) {
	$red = grenvios_dsec_red();
	if ( ! isset( $red[ $slug ] ) ) return array();
	$yo  = $red[ $slug ];
	$out = array();

	$conDias = array_filter( $red, function ( $x ) { return $x['dias'] > 0; } );
	if ( $yo['dias'] > 0 && $conDias ) {
		$dias = wp_list_pluck( $conDias, 'dias' );
		if ( $yo['dias'] === min( $dias ) && count( array_keys( $dias, min( $dias ) ) ) === 1 ) {
			$out[] = 'es el destino al que llegamos más rápido de toda nuestra red';
		} elseif ( $yo['terr'] ) {
			$terr = wp_list_pluck( array_filter( $conDias, function ( $x ) { return $x['terr']; } ), 'dias' );
			if ( $terr && $yo['dias'] === min( $terr ) && count( array_keys( $terr, min( $terr ) ) ) === 1 ) {
				$out[] = 'es el destino terrestre más rápido que operamos';
			}
		}
	}

	if ( $yo['impuesto'] !== null ) {
		$imps = array_filter( wp_list_pluck( $red, 'impuesto' ), function ( $v ) { return $v !== null; } );
		if ( $imps && $yo['impuesto'] === min( $imps ) && count( array_keys( $imps, min( $imps ) ) ) === 1 ) {
			$out[] = 'tiene el impuesto más bajo de los destinos que lo aplican, un ' . rtrim( rtrim( number_format( $yo['impuesto'], 1, ',', '' ), '0' ), ',' ) . ' %';
		} elseif ( $imps && $yo['impuesto'] === max( $imps ) && count( array_keys( $imps, max( $imps ) ) ) === 1 ) {
			$out[] = 'es el destino con el impuesto más alto, un ' . rtrim( rtrim( number_format( $yo['impuesto'], 1, ',', '' ), '0' ), ',' ) . ' %, así que ajustar el valor declarado pesa más aquí que en ningún otro';
		}
	}

	if ( $yo['terr'] && $yo['casa'] ) {
		$otros = array_filter( $red, function ( $x, $k ) use ( $slug ) { return $k !== $slug && $x['terr'] && $x['casa']; }, ARRAY_FILTER_USE_BOTH );
		if ( ! $otros ) $out[] = 'es el único destino con ruta terrestre al que entregamos en el domicilio, sin que nadie tenga que ir a recoger';
	}

	return $out;
}

/* ─────────────────────────────────────────────────────────────────────────
 * «Por qué elegirnos»
 *
 * Maqueta pedida por la clienta: antesala, titular grande, texto, barras de
 * progreso a la izquierda; imagen enmarcada a la derecha, y cuatro tarjetas
 * numeradas que se montan sobre la parte baja de la imagen.
 *
 * Las barras reutilizan el componente `skill-item` que el tema ya estila. Las
 * tarjetas con número de contorno son nuevas (el tema no las traía).
 *
 * Todo sale de campos editables de la página (inc/page-editor.php), con valores
 * por defecto que usan los datos reales del destino. Las barras no tienen valor
 * por defecto: un porcentaje de rendimiento solo lo puede poner quien lo mide, y
 * sin él el bloque de barras no aparece.
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_dsec_why( $slug, $d0 ) {
	$d = grenvios_dsec_datos( $slug, $d0 );
	$p = $d['title'];

	$eyebrow = grenvios_field( 'dst_why_eyebrow', 'Por qué elegirnos' );
	$titulo  = grenvios_field( 'dst_why_title', 'Tu operador de confianza para enviar a <span>' . esc_html( $p ) . '</span>' );
	$texto   = grenvios_field( 'dst_why_text',
		'Somos un operador peruano y la ruta a ' . $p . ' la gestionamos de principio a fin: recogemos en {{origen_ciudad}}, despachamos, seguimos el envío y te avisamos cuando tu destinatario lo recibe. Sin intermediarios que se pasen la responsabilidad.' );
	$img     = grenvios_field( 'dst_why_img', '' );
	if ( $img === '' ) $img = get_template_directory_uri() . '/assets/img/destino-hero.jpg';   // forklift.png era una silueta de relleno

	$barras = array();
	foreach ( array( 1, 2 ) as $n ) {
		$lab = trim( (string) grenvios_field( 'dst_why_bar' . $n . '_label', '' ) );
		$val = (int) preg_replace( '/\D/', '', (string) grenvios_field( 'dst_why_bar' . $n . '_val', '' ) );
		if ( $lab !== '' && $val > 0 ) $barras[] = array( $lab, min( 100, $val ) );
	}

	$tarjetas = array(
		array( 'fa-light fa-map-location-dot',     grenvios_field( 'dst_why1', 'Seguimiento en tiempo real' ) ),
		array( 'fa-light fa-file-invoice-dollar',  grenvios_field( 'dst_why2', 'Precio cerrado antes de despachar' ) ),
		array( 'fa-light fa-shield-check',         grenvios_field( 'dst_why3', 'Envío con seguro' ) ),
		array( 'fa-light fa-clock',                grenvios_field( 'dst_why4', 'Entrega en ' . $d['tiempo'] ) ),
	);

	echo '<section class="dest-why-section dest-why2 padding"><div class="container">'
		. '<div class="row align-items-start dest-why2-top">'
		. '<div class="col-lg-6">'
		. '<span class="dest-why2-eyebrow">' . esc_html( $eyebrow )
		. '<svg viewBox="0 0 34 12" aria-hidden="true"><path d="M2 1l5 5-5 5M11 1l5 5-5 5M20 1l5 5-5 5"/></svg></span>'
		. '<h2 class="dest-why2-title">' . wp_kses_post( $titulo ) . '</h2>'
		. '<p class="dest-why2-text">' . wp_kses_post( $texto ) . '</p>';

	if ( $barras ) {
		echo '<ul class="skill-wrap dest-why2-barras">';
		foreach ( $barras as $b ) {
			echo '<li class="skill-item"><h4>' . esc_html( $b[0] ) . '</h4>'
				. '<div class="progress"><div class="progress-bar" role="progressbar" style="--progress-bar-count:' . (int) $b[1] . '%"'
				. ' aria-valuenow="' . (int) $b[1] . '" aria-valuemin="0" aria-valuemax="100"><span>' . (int) $b[1] . '%</span></div></div></li>';
		}
		echo '</ul>';
	}

	echo '</div>'
		. '<div class="col-lg-6"><div class="dest-why2-media">'
		. '<img src="' . esc_url( $img ) . '" alt="' . esc_attr( 'Envíos a ' . $p . ' con Grenvíos' ) . '" loading="lazy">'
		. '</div></div>'
		. '</div>';

	echo '<div class="dest-why2-cards">';
	$i = 0;
	foreach ( $tarjetas as $t ) {
		$i++;
		if ( trim( (string) $t[1] ) === '' ) continue;
		echo '<div class="dest-why2-card">'
			. '<i class="' . esc_attr( $t[0] ) . '"></i>'
			. '<h3>' . esc_html( $t[1] ) . '</h3>'
			. '<span class="dest-why2-num" aria-hidden="true">' . sprintf( '%02d', $i ) . '</span>'
			. '</div>';
	}
	echo '</div></div></section>';
}

/* ─────────────────────────────────────────────────────────────────────────
 * 1) Tras la información
 * ───────────────────────────────────────────────────────────────────────── */
add_action( 'grenvios_destino_tras_info', function ( $slug, $d0 ) {
	$d    = grenvios_dsec_datos( $slug, $d0 );
	$p    = esc_html( $d['title'] );
	$lang = grenvios_dsec_lang( $slug );
	$via  = $d['aereo'] && $d['terr'] ? 'aérea y terrestre' : ( $d['aereo'] ? 'aérea' : 'terrestre' );

	/* ── Precio ──
	 * No se publican tarifas: cambian, dependen del envío y una cifra vieja
	 * genera reclamos. Lo que sí se explica es de qué depende el precio, que es
	 * lo que la persona quiere entender antes de escribir. */
	/* Diseño (maqueta 2026-09-28): cabecera + imagen con «4 factores» a la
	 * izquierda, las cuatro variables en 2×2 a la derecha y franja vino de cierre.
	 * Los textos los hace editables inc/destinos-textos-editables.php; la imagen,
	 * el campo `dst_precio_img` del panel. */
	$factores = array(
		array( 'fa-solid fa-weight-scale', 'Peso frente a volumen',
			'Se cobra el mayor de los dos, no el que a ti te conviene. Una caja grande y ligera —almohadas, peluches, ropa de invierno— paga por el espacio que ocupa en la bodega, así que puede costar más que un bulto pequeño y pesado. Ajustar la caja al contenido es el ahorro más fácil de conseguir.' ),
		array( 'fa-solid fa-box-open', 'Qué envías',
			'Documentos, ropa, alimentos, medicinas o electrónica no se despachan igual. Algunas categorías necesitan permisos o documentación adicional, y ese trámite se refleja en el precio y en el plazo. Describirlo bien desde el principio evita recotizar a mitad de camino.' ),
		array( 'fa-solid fa-file-invoice-dollar', 'Valor declarado',
			'Es la base sobre la que se calculan el impuesto y el seguro. Declarar por debajo para «ahorrar» es la causa número uno de que un paquete quede retenido: si la aduana no se cree el valor, lo abre, lo tasa por su cuenta y suma multa. Declara lo que cuesta y adjunta la boleta.' ),
		array( 'fa-solid fa-location-dot', 'Desde dónde despachas',
			'En nuestra sede de {{origen_ciudad}}, con recojo a domicilio o desde provincias. Cada opción tiene su coste y su tiempo: el recojo ahorra el viaje, el tramo desde provincias suma días al plazo total.' ),
	);
	$img = trim( (string) grenvios_field( 'dst_precio_img', '' ) );
	if ( $img === '' ) $img = grenvios_ui_img( 'precio' );

	echo '<section class="srv-section dest-seo-sec dest-precio bg-grey padding"><div class="container">'
		. '<div class="dest-precio-grid"><div class="dest-precio-lado">'
		. '<div class="srv-head"><h3 class="sub-heading">Precio</h3>'
		. '<h2>¿Cuánto cuesta enviar a ' . $p . '?</h2>'
		. '<p class="srv-intro">No hay una tarifa única, y desconfía de quien te la dé por teléfono sin preguntarte nada. El precio de un envío a ' . $p
		. ' sale de cuatro variables, y conocerlas te deja negociar con criterio en vez de aceptar la primera cifra.</p></div>'
		. '<figure class="dest-precio-foto wow fade-in-bottom" data-wow-delay="150ms"><img src="' . esc_url( $img ) . '" alt="" loading="lazy" decoding="async">'
		. '<div class="dest-precio-badge"><p><strong>4</strong> <span>factores definen el precio</span></p></div></figure>'
		. '</div><div class="dest-precio-cards">';
	foreach ( $factores as $i => $f ) {
		echo '<div class="dest-precio-card wow fade-in-bottom" data-wow-delay="' . ( 100 + ( $i % 2 ) * 110 ) . 'ms">'
			. '<span class="srv-card-ic"><i class="' . esc_attr( $f[0] ) . '"></i></span>'
			. '<h3 class="srv-card-title">' . esc_html( $f[1] ) . '</h3>'
			. '<p>' . wp_kses_post( $f[2] ) . '</p></div>';
	}
	echo '</div></div>';

	$cotizar = grenvios_dsec_url( 'cotizar', $lang );
	if ( $cotizar ) {
		echo '<div class="dest-seo-cta-linea dest-precio-cta wow fade-in-bottom"><a class="default-btn" href="' . esc_url( $cotizar ) . '">'
			. esc_html( 'Cotizar mi envío a ' . $d['title'] ) . '</a>'
			. '<span class="dest-precio-cta-ic"><i class="fa-solid fa-truck-fast"></i></span>'
			. '<p>Con tres datos —qué envías, cuánto pesa y mide, y a qué ciudad va— te damos precio cerrado, sin sorpresas al despachar.</p></div>';
	}
	grenvios_dsec_close();

	/* ── Plazos ── */
	if ( trim( (string) $d['tiempo'] ) !== '' && ( $d['aereo'] || $d['terr'] ) ) {
		grenvios_dsec_open(
			'', 'Plazos', '¿Cuánto demora un envío a ' . $p . '?',
			'El plazo hacia ' . $p . ' es de <strong>' . esc_html( $d['tiempo'] ) . '</strong>, y conviene entender desde cuándo se cuenta: '
			. 'desde que el envío <em>sale</em> de {{origen_ciudad}}, no desde que lo dejas en el mostrador. Entre una cosa y otra hay un despacho de por medio.'
		);
		echo '<div class="srv-two-grid"><div class="srv-panel">'
			. '<h3 class="srv-panel-title">Plazo por modalidad</h3><div class="table-responsive">'
			. '<table class="dest-seo-tabla"><thead><tr><th>Modalidad</th><th>Plazo</th><th>Entrega</th><th>Para qué conviene</th></tr></thead><tbody>';
		if ( $d['aereo'] ) {
			echo '<tr><td><strong>Aéreo</strong></td><td>' . esc_html( $d['tiempo'] ) . '</td><td>'
				. esc_html( $d['entrega'] ) . '</td><td>Documentos y urgencias</td></tr>';
		}
		if ( $d['terr'] ) {
			echo '<tr><td><strong>Terrestre</strong></td><td>' . esc_html( $d['tiempo'] ) . '</td><td>'
				. esc_html( $d['entrega'] ) . '</td><td>Paquetes, mudanzas y carga</td></tr>';
		}
		echo '</tbody></table></div>'
			. '<p class="dest-seo-nota">Los días son hábiles: no cuentan sábados, domingos ni feriados, ni los de aquí ni los de ' . $p . '.</p>'
			. '</div>'
			. '<div class="srv-panel"><h3 class="srv-panel-title">Qué puede alargarlo</h3><ul class="srv-times">'
			. '<li><span class="srv-time-ic"><i class="fa-solid fa-magnifying-glass"></i></span><span class="srv-time-v">Una revisión en la aduana de ' . $p . '</span></li>'
			. '<li><span class="srv-time-ic"><i class="fa-solid fa-calendar-xmark"></i></span><span class="srv-time-v">Feriados locales que no coinciden con los peruanos</span></li>'
			. '<li><span class="srv-time-ic"><i class="fa-solid fa-address-card"></i></span><span class="srv-time-v">Datos del destinatario incompletos o mal escritos</span></li>'
			. '<li><span class="srv-time-ic"><i class="fa-solid fa-cloud-bolt"></i></span><span class="srv-time-v">Retrasos de vuelo o de frontera fuera de nuestro control</span></li>'
			. '</ul><p class="dest-seo-nota">Cuando ocurre, se refleja en el número de seguimiento: no hace falta que llames para enterarte.</p></div></div>';
		grenvios_dsec_close();
	}

	/* ── Lo que más se envía (campo editable) ── */
	$top = grenvios_dsec_lista( $d['top'] );
	if ( $top ) {
		grenvios_dsec_open( 'bg-grey', 'Lo habitual', 'Qué se envía más a ' . $p,
			'Después de años despachando esta ruta, el contenido de los paquetes a ' . $p . ' se repite bastante. Si lo tuyo está en esta lista, es terreno conocido.' );
		echo '<div class="srv-features-grid">';
		foreach ( $top as $t ) {
			echo '<div class="srv-feature"><i class="fa-solid fa-circle-check"></i><span>' . esc_html( ucfirst( $t ) ) . '</span></div>';
		}
		echo '</div>';
		grenvios_dsec_close();
	}

	/* ── Lo que no se puede enviar (campo editable) ── */
	$proh = grenvios_dsec_lista( $d['prohibidos'] );
	if ( $proh ) {
		grenvios_dsec_open(
			'', 'Restricciones', 'Lo que no se puede enviar a ' . $p,
			'Cada aduana tiene su propia lista y la de ' . $p . ' no coincide con la de sus vecinos. Un envío que la incumple no se devuelve: se retiene, '
			. 'y recuperarlo cuesta más tiempo y más dinero que haberlo consultado antes.'
		);
		echo '<div class="row gy-3">';
		foreach ( $proh as $x ) {
			echo '<div class="col-lg-4 col-md-6"><div class="dest-seo-no"><i class="fa-solid fa-ban"></i><span>' . esc_html( ucfirst( $x ) ) . '</span></div></div>';
		}
		echo '</div><p class="dest-seo-pie">¿Tu caso no está claro? Pregúntanos antes de cerrar la caja. Sale más barato consultar que recuperar un envío retenido.</p>';
		grenvios_dsec_close();
	}

	/* ── Documentación (campo editable) ── */
	$docs = grenvios_dsec_lista( $d['documentos'] );
	if ( $docs ) {
		grenvios_dsec_open( 'bg-grey', 'Documentación', 'Qué papeles pide ' . $p,
			'Casi todos los envíos que se atascan lo hacen por un papel que faltaba, no por lo que llevaban dentro. Ten esto listo antes de despachar:' );
		echo '<ul class="srv-steps dest-seo-docs">';
		$i = 0;
		foreach ( $docs as $x ) {
			$i++;
			echo '<li><span class="srv-step-n">' . $i . '</span><span class="srv-step-tx">' . esc_html( ucfirst( $x ) ) . '</span></li>';
		}
		echo '</ul>';
		grenvios_dsec_close();
	}

	/* ── Errores que retrasan un envío ──
	 * Contenido general de despacho, no normas de ningún país: se puede escribir
	 * sin inventar nada y responde a una búsqueda real. */
	grenvios_dsec_open(
		'', 'Evitarlos', 'Cinco errores que retrasan un envío a ' . $p,
		'Cuando un paquete se queda parado, casi siempre es por una de estas cinco cosas. Las cinco se evitan antes de despachar.'
	);
	echo '<ul class="srv-steps">'
		. '<li><span class="srv-step-n">1</span><span class="srv-step-ic"><i class="fa-solid fa-tag"></i></span><span class="srv-step-tx"><strong>Declarar de menos.</strong> El valor declarado no cuadra con lo que la aduana estima que vale el contenido, y el envío pasa a revisión.</span></li>'
		. '<li><span class="srv-step-n">2</span><span class="srv-step-ic"><i class="fa-solid fa-file-lines"></i></span><span class="srv-step-tx"><strong>Describir en genérico.</strong> «Regalo», «varios» o «muestras» no le dicen nada al inspector. Un detalle concreto acelera el despacho.</span></li>'
		. '<li><span class="srv-step-n">3</span><span class="srv-step-ic"><i class="fa-solid fa-receipt"></i></span><span class="srv-step-tx"><strong>No adjuntar la boleta.</strong> Sin factura que respalde el valor, la aduana tasa por su cuenta y casi nunca a tu favor.</span></li>'
		. '<li><span class="srv-step-n">4</span><span class="srv-step-ic"><i class="fa-solid fa-shield-halved"></i></span><span class="srv-step-tx"><strong>Olvidar un permiso.</strong> Sanitarios, farmacéuticos o culturales: hay contenidos que necesitan autorización y sin ella no salen.</span></li>'
		. '<li><span class="srv-step-n">5</span><span class="srv-step-ic"><i class="fa-solid fa-phone-slash"></i></span><span class="srv-step-tx"><strong>Dar un contacto que no responde.</strong> Si la aduana de ' . $p . ' pide un dato y nadie contesta, el envío espera.</span></li>'
		. '</ul>';
	grenvios_dsec_close();

	/* ── Embalaje ── */
	$emb = trim( (string) $d['embalaje'] );
	grenvios_dsec_open( 'bg-grey', 'Embalaje', 'Cómo embalar lo que va a ' . $p,
		'Un envío por vía ' . $via . ' se manipula varias veces antes de llegar. El embalaje no es un detalle: es lo que decide si llega entero.' );
	echo '<div class="srv-two-grid"><div class="srv-panel">'
		. '<h3 class="srv-panel-title">Lo que siempre aplica</h3><ul class="srv-times">'
		. '<li><span class="srv-time-ic"><i class="fa-solid fa-box"></i></span><span class="srv-time-v">Caja de cartón doble, ajustada al contenido</span></li>'
		. '<li><span class="srv-time-ic"><i class="fa-solid fa-shirt"></i></span><span class="srv-time-v">Relleno en los huecos: nada debe bailar dentro</span></li>'
		. '<li><span class="srv-time-ic"><i class="fa-solid fa-tape"></i></span><span class="srv-time-v">Cinta de embalaje en todas las juntas, en forma de H</span></li>'
		. '<li><span class="srv-time-ic"><i class="fa-solid fa-droplet"></i></span><span class="srv-time-v">Líquidos y cremas en bolsa sellada aparte</span></li>'
		. '</ul></div><div class="srv-panel">'
		. '<h3 class="srv-panel-title">Propio de la ruta a ' . $p . '</h3>'
		. ( $emb !== ''
			? '<p>' . wp_kses_post( $emb ) . '</p>'
			: '<p>Si tu envío lleva algo delicado o de valor, dínoslo al cotizar: te decimos cómo prepararlo y si conviene asegurarlo. En la ruta a ' . $p
			  . ' la entrega es ' . esc_html( strtolower( $d['entrega'] ) ) . ', y eso también influye en cómo conviene rotular el bulto.</p>' )
		. '</div></div>';
	grenvios_dsec_close();

	/* La cobertura (ciudades) ya la pinta inc/destinos-ciudades.php con la
	 * misma lista del gestor: aquí salía una segunda vez, con el mismo H2
	 * «Ciudades de X a las que llegamos» en la misma página. */
}, 10, 2 );

/* ─────────────────────────────────────────────────────────────────────────
 * 2) Tras el proceso: aéreo o terrestre
 * ───────────────────────────────────────────────────────────────────────── */
add_action( 'grenvios_destino_tras_proceso', function ( $slug, $d0 ) {
	$d = grenvios_dsec_datos( $slug, $d0 );
	if ( ! ( $d['aereo'] && $d['terr'] ) ) return;   // solo donde existen las dos
	$p = esc_html( $d['title'] );

	grenvios_dsec_open(
		'bg-grey', 'Elegir bien', 'Aéreo o terrestre a ' . $p,
		'Hacia ' . $p . ' operamos las dos vías, y la elección casi nunca es cuestión de gusto: la decide el peso frente al volumen y lo que corra la fecha.'
	);
	echo '<div class="srv-two-grid dest-seo-vias">'
		. '<div class="srv-panel dest-seo-via">'
		. '<div class="dest-seo-via-top"><span class="srv-card-ic"><i class="fa-solid fa-plane-up"></i></span>'
		. '<h3 class="srv-panel-title">Vía aérea</h3><span class="dest-seo-pill">Rápida</span></div>'
		. '<p>Conviene cuando el envío urge, pesa poco o su valor justifica el flete. Es la vía de los documentos y de todo lo que no puede esperar.</p><dl>'
		. '<div><dt>Plazo</dt><dd>' . esc_html( $d['tiempo'] ) . '</dd></div>'
		. '<div><dt>Coste</dt><dd>Mayor</dd></div>'
		. '<div><dt>Entrega</dt><dd>' . esc_html( $d['entrega'] ) . '</dd></div>'
		. '<div><dt>Típico</dt><dd>Documentos, medicinas, muestras</dd></div>'
		. '</dl></div>'
		. '<div class="srv-panel dest-seo-via is-econ">'
		. '<div class="dest-seo-via-top"><span class="srv-card-ic"><i class="fa-solid fa-truck-fast"></i></span>'
		. '<h3 class="srv-panel-title">Vía terrestre</h3><span class="dest-seo-pill on">Económica</span></div>'
		. '<p>Rinde cuando el paquete abulta o pesa y la fecha no aprieta. Es la vía de las encomiendas familiares y de la carga.</p><dl>'
		. '<div><dt>Plazo</dt><dd>' . esc_html( $d['tiempo'] ) . '</dd></div>'
		. '<div><dt>Coste</dt><dd>Menor</dd></div>'
		. '<div><dt>Entrega</dt><dd>' . esc_html( $d['entrega'] ) . '</dd></div>'
		. '<div><dt>Típico</dt><dd>Ropa, menaje, encomiendas, carga</dd></div>'
		. '</dl></div></div>';

	$imp = $d['impuesto'] !== ''
		? ' En envíos terrestres a ' . $p . ' se aplica un impuesto aproximado del ' . esc_html( $d['impuesto'] ) . ' % sobre el valor declarado, que se cancela en {{origen_ciudad}} al despachar.'
		: '';
	echo '<div class="dest-seo-regla"><span class="srv-card-ic"><i class="fa-solid fa-lightbulb"></i></span>'
		. '<p><strong>La regla que resuelve la duda:</strong> un bulto ligero pero voluminoso paga por el espacio que ocupa, no por lo que pesa. '
		. 'Si tu envío a ' . $p . ' abulta, la terrestre sale bastante mejor; si cabe en un sobre y corre prisa, la aérea.' . $imp . '</p></div>';
	grenvios_dsec_close();
}, 10, 2 );

/* ─────────────────────────────────────────────────────────────────────────
 * 4) Secciones propias del país: la ruta, su posición en la red y las fechas
 *
 * Van enganchadas tras el proceso, después de la comparativa de modalidades.
 * Son las que hacen que la página hable de ESE país y no de un país cualquiera
 * con el nombre cambiado.
 * ───────────────────────────────────────────────────────────────────────── */
add_action( 'grenvios_destino_tras_proceso', function ( $slug, $d0 ) {
	$d    = grenvios_dsec_datos( $slug, $d0 );
	$p    = esc_html( $d['title'] );
	$lang = grenvios_dsec_lang( $slug );

	/* ── Cómo es la ruta hasta este país ──
	 *
	 * El texto sale de las notas del destino, que es donde la clienta escribió lo
	 * concreto de cada corredor: que a Ecuador se entra por Huaquillas, que a
	 * Cuba se aceptan medicinas con receta. Esa frase es el dato más propio que
	 * tiene cada país y estaba enterrada en un recuadro de «información
	 * importante» que nadie lee. */
	$pasos = array();
	$pasos[] = array( 'fa-solid fa-box-open', 'Recepción en {{origen_ciudad}}',
		'Recibimos el bulto en nuestra sede o lo recogemos donde estés. Se pesa, se mide y se comprueba que el contenido coincida con lo declarado.' );
	$pasos[] = array( 'fa-solid fa-file-signature', 'Despacho de exportación',
		'Se emite la documentación y se cancelan aquí los derechos que correspondan. Es el paso que separa «lo entregué» de «ya salió».' );
	/* Un solo paso para el tramo internacional. Antes se listaban «Tramo
	 * terrestre» y «Tramo aéreo» como pasos consecutivos, y en una lista numerada
	 * eso se lee como si el envío hiciera los dos seguidos. Son alternativas: se
	 * elige una al cotizar. */
	if ( $d['terr'] && $d['aereo'] ) {
		$pasos[] = array( 'fa-solid fa-route', 'Tramo internacional, por la vía que elijas',
			'Por carretera hasta el paso de frontera y de ahí a territorio de ' . $d['title'] . ', o en bodega de avión si contrataste la vía aérea. Es el tramo que marca la diferencia de precio entre las dos.' );
	} elseif ( $d['terr'] ) {
		$pasos[] = array( 'fa-solid fa-road', 'Tramo terrestre',
			'El envío viaja por carretera hasta el paso de frontera y continúa en territorio de ' . $d['title'] . '.' );
	} else {
		$pasos[] = array( 'fa-solid fa-plane-departure', 'Tramo aéreo',
			'El envío sale en bodega hacia ' . $d['title'] . ' y queda a disposición de la aduana al aterrizar.' );
	}
	$pasos[] = array( 'fa-solid fa-building-shield', 'Aduana de ' . $d['title'],
		'Es el único tramo cuyo tiempo no controlamos. Un envío bien declarado y bien descrito pasa sin detenerse.' );
	$pasos[] = array( $d['casa'] ? 'fa-solid fa-house-circle-check' : 'fa-solid fa-store',
		$d['casa'] ? 'Entrega en el domicilio' : 'Retiro en la agencia local',
		$d['casa']
			? 'Se entrega en la dirección indicada. Hacen falta una referencia y un teléfono local que conteste.'
			: 'El destinatario recibe el aviso y retira con su documento de identidad, que debe coincidir con el nombre del envío.' );

	grenvios_dsec_open( '', 'La ruta', 'Cómo llega tu envío a ' . $p . ', tramo a tramo',
		'Un envío a ' . $p . ' no es un trayecto, son varios encadenados. Saber dónde está el tuyo evita la mitad de las llamadas de «ya debería haber llegado».' );
	echo '<ul class="srv-steps">';
	$i = 0;
	foreach ( $pasos as $x ) {
		$i++;
		echo '<li><span class="srv-step-n">' . $i . '</span>'
			. '<span class="srv-step-ic"><i class="' . esc_attr( $x[0] ) . '"></i></span>'
			. '<span class="srv-step-tx"><strong>' . wp_kses_post( $x[1] ) . '.</strong> ' . wp_kses_post( $x[2] ) . '</span></li>';
	}
	echo '</ul>';
	if ( trim( (string) $d['restr'] ) !== '' ) {
		echo '<div class="dest-seo-regla"><span class="srv-card-ic"><i class="fa-solid fa-circle-info"></i></span>'
			. '<p><strong>Lo propio de la ruta a ' . $p . ':</strong> ' . wp_kses_post( $d['restr'] ) . '</p></div>';
	}
	grenvios_dsec_close();

	/* ── Este país frente al resto de la red ──
	 *
	 * Cada afirmación se calcula comparando los datos reales de los nueve
	 * destinos, así que es cierta y además es distinta en cada página: en Ecuador
	 * dice que es el terrestre más rápido, en Chile que es el de impuesto más
	 * alto. De paso reparte enlaces hacia los demás países. */
	$red = grenvios_dsec_red();
	if ( count( $red ) > 2 && isset( $red[ $slug ] ) ) {
		$pos = grenvios_dsec_posicion( $slug );
		$fra = $pos
			? 'Entre nuestros destinos, ' . $d['title'] . ' ' . implode( ', y ', $pos ) . '.'
			: 'Así queda ' . $d['title'] . ' frente al resto de destinos que operamos.';

		grenvios_dsec_open( 'bg-grey', 'En contexto', $p . ' frente a nuestros otros destinos', $fra );
		echo '<div class="table-responsive"><table class="dest-seo-tabla dest-seo-comp">'
			. '<thead><tr><th>Destino</th><th>Plazo</th><th>Modalidades</th><th>Entrega</th><th>Impuesto</th></tr></thead><tbody>';
		foreach ( $red as $sl => $x ) {
			$es  = ( $sl === $slug );
			$url = '';
			if ( ! $es ) {
				$l2  = grenvios_dsec_lang( $sl );
				$url = $l2 !== '' && function_exists( 'grenvios_sedes' )
					? ( grenvios_sedes()[ $l2 ]['url'] ?? '' )
					: get_permalink( get_page_by_path( 'destinos/' . $sl ) ?: 0 );
			}
			$nombre = $es
				? '<strong>' . esc_html( $x['title'] ) . '</strong>'
				: ( $url ? '<a href="' . esc_url( $url ) . '">' . esc_html( $x['title'] ) . '</a>' : esc_html( $x['title'] ) );
			echo '<tr' . ( $es ? ' class="is-actual"' : '' ) . '>'
				. '<td>' . $nombre . '</td>'
				. '<td>' . esc_html( $x['tiempo'] ) . '</td>'
				. '<td>' . esc_html( $x['modos'] ) . '</td>'
				. '<td>' . esc_html( $x['entrega'] ) . '</td>'
				. '<td>' . ( $x['impuesto'] !== null ? esc_html( rtrim( rtrim( number_format( $x['impuesto'], 1, ',', '' ), '0' ), ',' ) ) . ' %' : '—' ) . '</td>'
				. '</tr>';
		}
		echo '</tbody></table></div>'
			. '<p class="dest-seo-nota">El impuesto se aplica a los envíos terrestres y se cancela en {{origen_ciudad}}: en ningún destino lo paga quien recibe.</p>';
		grenvios_dsec_close();
	}

	/* ── Cuándo enviar ──
	 * Estacionalidad real del negocio, no normas de ningún país. */
	grenvios_dsec_open( '', 'Planificar', '¿Cuándo conviene enviar a ' . $p . '?',
		'Hay semanas en las que todo el mundo envía a la vez, y son justo aquellas en las que el envío tiene que llegar sí o sí. Con el plazo de ' . esc_html( $d['tiempo'] ) . ' en la mano, estas son las fechas a las que hay que anticiparse.' );
	echo '<div class="row gy-4">'
		. grenvios_dsec_card( 'fa-solid fa-gift', 'Navidad y Reyes',
			'Es el pico del año y el momento en que las aduanas van más cargadas. Para que llegue antes del 24, despacha con varias semanas de margen sobre el plazo normal.' )
		. grenvios_dsec_card( 'fa-solid fa-heart', 'Día de la Madre',
			'La segunda fecha con más volumen. Se concentra en pocos días, así que el margen aquí importa tanto como en diciembre.' )
		. grenvios_dsec_card( 'fa-solid fa-graduation-cap', 'Inicio de clases',
			'Útiles, uniformes y material se mueven en bloque. Si el envío tiene fecha de uso, cuenta hacia atrás desde ese día.' )
		. grenvios_dsec_card( 'fa-solid fa-briefcase-medical', 'Envíos que no esperan',
			'Medicinas y documentos con plazo legal no deberían viajar en temporada alta si hay alternativa. Consúltanos y vemos la vía más segura.' )
		. '</div>';
	grenvios_dsec_close();
}, 20, 2 );

/* ─────────────────────────────────────────────────────────────────────────
 * 5) Cobertura de intención: perfiles, origen, checklist y glosario
 *
 * Las secciones anteriores responden a «cuánto cuesta» y «cuánto tarda». Estas
 * cubren los otros tres tipos de búsqueda que llegan a una página de destino:
 *
 *   Por PERFIL      «enviar un paquete a mi familia en X», «enviar mercadería
 *                   a X para vender», «mudarme a X». La misma ruta, pero la
 *                   persona no se reconoce en un texto genérico.
 *   Por ORIGEN      «enviar a X desde Arequipa». Cola larga con intención
 *                   altísima y ninguna página la mencionaba.
 *   Por DEFINICIÓN  «qué es el peso volumétrico», «qué es el valor declarado».
 *                   Búsquedas informativas que traen a quien todavía no sabe
 *                   que necesita cotizar.
 *
 * Nada de esto depende de datos que haya que inventar: son la operación de
 * siempre explicada desde el ángulo de quien busca.
 * ───────────────────────────────────────────────────────────────────────── */
add_action( 'grenvios_destino_antes_cta', function ( $slug, $d0 ) {
	$d    = grenvios_dsec_datos( $slug, $d0 );
	$p    = esc_html( $d['title'] );
	$lang = grenvios_dsec_lang( $slug );

	$u_paq  = grenvios_dsec_url( 'envio-internacional-de-paquetes', $lang );
	$u_emp  = grenvios_dsec_url( 'envios-para-empresas', $lang );
	$u_equ  = grenvios_dsec_url( 'envio-de-equipaje', $lang );
	$u_apo  = grenvios_dsec_url( 'apostilla-y-traduccion', $lang );
	$u_prov = grenvios_dsec_url( 'envios-desde-provincias', $lang );
	$u_peso = grenvios_dsec_url( 'peso-volumetrico', $lang );
	$u_adu  = grenvios_dsec_url( 'aduanas-e-impuestos', $lang );

	$enl = function ( $url, $txt ) {
		return $url ? '<a href="' . esc_url( $url ) . '">' . esc_html( $txt ) . '</a>' : esc_html( $txt );
	};

	/* ── Perfiles ── */
	grenvios_dsec_open( '', 'Según tu caso', 'Qué necesitas saber según por qué envías a ' . $p,
		'No es lo mismo mandarle un paquete a tu madre que despachar mercadería para vender. La ruta a ' . $p
		. ' es la misma, pero lo que tienes que preparar cambia bastante.' );
	echo '<div class="row gy-4">'
		. grenvios_dsec_card( 'fa-solid fa-people-roof', 'Envías a tu familia',
			'Es el caso más común. Ropa, aseo, medicinas y regalos. Lo que más problemas da no es el contenido, sino declarar «regalo» sin detallar y sin boleta: la aduana no puede valorar lo que no se describe. '
			. $enl( $u_paq, 'Cómo enviar un paquete' ) . '.' )
		. grenvios_dsec_card( 'fa-solid fa-store', 'Vendes o compras para revender',
			'Aunque sea poca cantidad, varias unidades del mismo producto se leen como envío comercial y entran otros requisitos. Dilo desde el principio: hay una forma correcta de hacerlo y sale más barata que corregirlo después. '
			. $enl( $u_emp, 'Envíos de empresa' ) . '.' )
		. grenvios_dsec_card( 'fa-solid fa-suitcase-rolling', 'Te mudas a ' . $d['title'],
			'Menaje, ropa y efectos personales viajan mejor repartidos en varios bultos que en uno enorme, y conviene declarar cada uno por separado. Aquí el volumen manda sobre el peso. '
			. $enl( $u_equ, 'Envío de equipaje' ) . '.' )
		. grenvios_dsec_card( 'fa-solid fa-stamp', 'Mandas documentos con validez legal',
			'Títulos, poderes y certificados suelen necesitar apostilla antes de salir, y ese trámite corre por separado del envío. Cuéntanoslo al cotizar para encadenar los dos plazos. '
			. $enl( $u_apo, 'Apostilla y traducción' ) . '.' )
		. '</div>';
	grenvios_dsec_close();

	/* ── Desde dónde envías ──
	 * Las ciudades son de origen, no de destino: es dónde puede estar el cliente,
	 * y eso lo sabemos porque el servicio de provincias existe. */
	grenvios_dsec_open( 'bg-grey', 'Desde Perú', '¿Puedo enviar a ' . $p . ' si no estoy en {{origen_ciudad}}?',
		'Sí. Toda la operación se despacha desde {{origen_ciudad}}, pero no hace falta que vivas aquí: tu envío llega primero a nuestra sede y desde ahí sale hacia ' . $p . '.' );
	echo '<div class="srv-two-grid"><div class="srv-panel">'
		. '<h3 class="srv-panel-title">Cómo funciona desde provincias</h3><ul class="srv-times">'
		. '<li><span class="srv-time-ic"><i class="fa-solid fa-1"></i></span><span class="srv-time-v">Nos escribes con el detalle del envío y lo cotizamos igual que si estuvieras aquí</span></li>'
		. '<li><span class="srv-time-ic"><i class="fa-solid fa-2"></i></span><span class="srv-time-v">Lo despachas por transporte interno hasta nuestra sede de {{origen_ciudad}}</span></li>'
		. '<li><span class="srv-time-ic"><i class="fa-solid fa-3"></i></span><span class="srv-time-v">Al llegar lo revisamos, lo pesamos y confirmamos el precio final</span></li>'
		. '<li><span class="srv-time-ic"><i class="fa-solid fa-4"></i></span><span class="srv-time-v">Sale hacia ' . $p . ' y sigues el envío con tu número de guía</span></li>'
		. '</ul></div><div class="srv-panel">'
		. '<h3 class="srv-panel-title">Lo que conviene tener en cuenta</h3>'
		. '<p>El tramo interno hasta {{origen_ciudad}} <strong>suma días al plazo total</strong>: a los ' . esc_html( $d['tiempo'] )
		. ' de la ruta a ' . $p . ' hay que añadirle lo que tarde tu transporte en llegar aquí. Si tienes fecha límite, cuenta hacia atrás desde ella incluyendo ese tramo.</p>'
		. '<p style="margin-top:14px">Embálalo pensando en los dos viajes, no solo en el internacional: la caja tiene que aguantar el trayecto entero. '
		. $enl( $u_prov, 'Ver cómo enviar desde provincias' ) . '.</p>'
		. '</div></div>';
	grenvios_dsec_close();

	/* ── Checklist ── */
	grenvios_dsec_open( '', 'Antes de ir', 'Checklist antes de despachar tu envío a ' . $p,
		'Repasa esto antes de salir de casa. Son cinco minutos que ahorran una vuelta y, a veces, un envío retenido.' );
	echo '<ul class="srv-steps dest-seo-check-list">'
		. '<li><span class="srv-step-n"><i class="fa-solid fa-check"></i></span><span class="srv-step-tx"><strong>El contenido está descrito con detalle.</strong> No «ropa», sino «5 polos de algodón y 2 pantalones». El inspector no adivina.</span></li>'
		. '<li><span class="srv-step-n"><i class="fa-solid fa-check"></i></span><span class="srv-step-tx"><strong>Tienes la boleta o factura a mano.</strong> Es lo que respalda el valor declarado. Sin ella, lo tasa la aduana.</span></li>'
		. '<li><span class="srv-step-n"><i class="fa-solid fa-check"></i></span><span class="srv-step-tx"><strong>El nombre del destinatario coincide con su documento.</strong> Un apodo o un nombre incompleto es motivo suficiente para que no se lo entreguen.</span></li>'
		. '<li><span class="srv-step-n"><i class="fa-solid fa-check"></i></span><span class="srv-step-tx"><strong>Hay un teléfono en ' . $p . ' que contesta.</strong> Si la aduana o la agencia necesitan un dato, es el único camino.</span></li>'
		. '<li><span class="srv-step-n"><i class="fa-solid fa-check"></i></span><span class="srv-step-tx"><strong>La caja está ajustada al contenido.</strong> Nada debe bailar dentro, y el aire de más lo pagas tú en volumen.</span></li>'
		. '<li><span class="srv-step-n"><i class="fa-solid fa-check"></i></span><span class="srv-step-tx"><strong>Sabes qué vía contrataste.</strong> Aérea o terrestre cambia el precio y el plazo; confírmalo antes de pagar.</span></li>'
		. '</ul>';
	grenvios_dsec_close();

	/* ── Glosario ──
	 * Cada término es una búsqueda informativa por sí mismo y trae a quien
	 * todavía no sabe que necesita cotizar. */
	grenvios_dsec_open( 'bg-grey', 'En claro', 'Cinco palabras que verás al enviar a ' . $p,
		'El vocabulario del despacho aduanero confunde más de lo que ayuda. Estas cinco son las que de verdad cambian lo que pagas.' );
	echo '<dl class="dest-seo-glosario">'
		. '<div><dt>Peso volumétrico</dt><dd>El peso «teórico» que se calcula a partir de las medidas de la caja. Si es mayor que el peso real, es el que se cobra: por eso una caja grande y ligera puede costar más que una pequeña y pesada. '
		. $enl( $u_peso, 'Cómo se calcula' ) . '.</dd></div>'
		. '<div><dt>Valor declarado</dt><dd>Lo que dices que vale el contenido. Es la base del impuesto y del seguro, y lo que la aduana contrasta con la boleta. Ni de más ni de menos: exacto.</dd></div>'
		. '<div><dt>Guía o número de seguimiento</dt><dd>El código que se genera al despachar. Con él sigues el envío en cada etapa, incluido el paso por la aduana de ' . $p . '.</dd></div>'
		. '<div><dt>Despacho</dt><dd>El trámite de salida. Entre que dejas el bulto y que el envío «sale» hay un despacho de por medio, y el plazo se cuenta desde ahí, no desde que lo entregaste.</dd></div>'
		. '<div><dt>Arancel o impuesto de importación</dt><dd>Lo que cobra el país de destino por dejar entrar la mercancía. En nuestra operación se cancela en {{origen_ciudad}} al despachar, así que quien recibe no paga nada. '
		. $enl( $u_adu, 'Aduanas e impuestos' ) . '.</dd></div>'
		. '</dl>';
	grenvios_dsec_close();
}, 15, 2 );

/* En una página de destino el marcado de preguntas lo emite este módulo, con
 * las dos tandas juntas. Se marca al pintar la sección para no adivinar aquí
 * si la página es o no de destino. */
add_filter( 'grenvios_faq_schema_propio', function ( $propio ) {
	return empty( $GLOBALS['grenvios_dsec_faq_emitido'] ) ? $propio : false;
} );

/* ─────────────────────────────────────────────────────────────────────────
 * 6) Datos estructurados del servicio
 *
 * FAQPage ya se emite con las preguntas. Esto añade el servicio en sí: qué se
 * ofrece, quién lo presta y a qué país llega. Es lo que permite que Google
 * entienda la página como «servicio de envío a ese país» y no como un texto
 * cualquiera que menciona un país.
 * ───────────────────────────────────────────────────────────────────────── */
add_action( 'grenvios_destino_antes_cta', function ( $slug, $d0 ) {
	$d = grenvios_dsec_datos( $slug, $d0 );
	$b = function_exists( 'grenvios_biz' ) ? grenvios_biz() : array();

	$nodo = array(
		'@context'    => 'https://schema.org',
		'@type'       => 'Service',
		'serviceType' => 'Envío internacional de paquetes, documentos y carga a ' . $d['title'],
		'name'        => 'Envíos a ' . $d['title'] . ' desde ' . ( function_exists( 'grenvios_sede_pais_nombre' ) ? grenvios_sede_pais_nombre() : 'Perú' ),
		'provider'    => array(
			'@type' => 'Organization',
			'name'  => 'Grenvíos',
			'url'   => function_exists( 'grenvios_url_base' ) ? grenvios_url_base() . '/' : home_url( '/' ),
		),
		'areaServed'  => array( '@type' => 'Country', 'name' => $d['title'] ),
		'url'         => get_permalink(),
	);
	if ( trim( (string) $d['lead'] ) !== '' ) {
		$nodo['description'] = wp_strip_all_tags( grenvios_sede_tokens_apply( $d['lead'] ) );
	}
	if ( ! empty( $b['phone_tel'] ) ) {
		$nodo['provider']['telephone'] = $b['phone_tel'];
	}
	echo '<script type="application/ld+json">' . wp_json_encode( $nodo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>';
}, 30, 2 );

/* ─────────────────────────────────────────────────────────────────────────
 * 3) Antes del cierre: enlaces internos, guías y preguntas frecuentes
 * ───────────────────────────────────────────────────────────────────────── */
add_action( 'grenvios_destino_antes_cta', function ( $slug, $d0 ) {
	$d    = grenvios_dsec_datos( $slug, $d0 );
	$p    = esc_html( $d['title'] );
	$lang = grenvios_dsec_lang( $slug );

	/* ── Enlaces internos ──
	 * La sección que más mueve la aguja. Sin ella la portada de un país no
	 * enlazaba a ninguna de sus otras páginas: 24 páginas sueltas sin eje. Con
	 * ella la portada pasa a ser el pilar del que cuelga el grupo temático. */
	$grupos = array(
		/* Cada enlace lleva su frase completa con %s por el país. Pegar «a Ecuador»
		 * detrás de cualquier etiqueta daba cosas como «Contacto a Ecuador», que
		 * no es español, y «Peso volumétrico a Ecuador», que además es falso: la
		 * regla del peso es la misma para todos los destinos. */
		'Qué envías' => array( 'fa-solid fa-boxes-stacked', array(
			'envio-internacional-de-paquetes'   => 'Envío de paquetes a %s',
			'envio-internacional-de-documentos' => 'Envío de documentos a %s',
			'carga-internacional'               => 'Carga internacional a %s',
			'envio-de-equipaje'                 => 'Envío de equipaje a %s',
			'envio-de-compras'                  => 'Envío de compras a %s',
			'envio-de-alimentos'                => 'Envío de alimentos a %s',
		) ),
		'Antes de enviar' => array( 'fa-solid fa-clipboard-check', array(
			'que-se-puede-enviar' => 'Qué se puede enviar a %s',
			'aduanas-e-impuestos' => 'La aduana de %s',
			'tiempos-de-entrega'  => 'Tiempos de entrega a %s',
			'peso-volumetrico'    => 'Cómo se cobra el peso',
			'seguro-de-envios'    => 'Seguro para envíos a %s',
		) ),
		'Cómo despachar' => array( 'fa-solid fa-truck-ramp-box', array(
			'como-enviar-un-paquete-al-extranjero' => 'Cómo enviar un paquete a %s',
			'recojo-a-domicilio-lima'              => 'Recojo a domicilio en {{origen_ciudad}}',
			'envios-desde-provincias'              => 'Enviar a %s desde provincias',
			'apostilla-y-traduccion'               => 'Apostilla y traducción para %s',
			'envios-para-empresas'                 => 'Envíos de empresa a %s',
		) ),
		'Resolver dudas' => array( 'fa-solid fa-circle-question', array(
			'cotizar'              => 'Cuánto cuesta enviar a %s',
			'preguntas-frecuentes' => 'Preguntas frecuentes de %s',
			'rastreo-de-envios'    => 'Rastrear un envío a %s',
			'contacto'             => 'Contactar por un envío a %s',
		) ),
	);

	$html = '';
	foreach ( $grupos as $titulo => $conf ) {
		list( $icono, $items ) = $conf;
		$li = '';
		foreach ( $items as $maestro => $patron ) {
			$url = grenvios_dsec_url( $maestro, $lang );
			if ( $url === '' ) continue;
			// El texto del enlace nombra el país donde tiene sentido: es la señal
			// que se quiere dar, pero forzarla siempre produce frases falsas.
			$texto = strpos( $patron, '%s' ) !== false ? sprintf( $patron, $d['title'] ) : $patron;
			$li   .= '<li><a href="' . esc_url( $url ) . '">' . esc_html( $texto ) . '</a></li>';
		}
		if ( $li !== '' ) {
			$html .= '<div class="col-lg-3 col-md-6"><div class="dest-seo-grupo">'
				. '<span class="srv-card-ic"><i class="' . esc_attr( $icono ) . '"></i></span>'
				. '<h3>' . esc_html( $titulo ) . '</h3><ul>' . $li . '</ul></div></div>';
		}
	}
	if ( $html !== '' ) {
		grenvios_dsec_open( 'bg-grey', 'Todo en un sitio', 'Todo sobre envíos a ' . $p,
			'Cada paso de tu envío a ' . $p . ' tiene su propia página, con el detalle que aquí no cabe.' );
		echo '<div class="row gy-4">' . $html . '</div>';
		grenvios_dsec_close();
	}

	/* ── Guías del blog de esa ruta ── */
	$args = array( 'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => 3 );
	if ( $lang !== '' ) $args['lang'] = $lang;
	$posts = get_posts( $args );
	if ( $posts ) {
		grenvios_dsec_open( '', 'Guías', 'Antes de enviar a ' . $p,
			'Lo que conviene leer antes de despachar, explicado sin prisa.' );
		echo '<div class="row gy-4">';
		foreach ( $posts as $po ) {
			/* Sin imagen destacada no se pinta la foto de relleno del tema (un recuadro
			 * gris con «Placeholder»): la tarjeta lleva un icono en su lugar. */
			$thumb = has_post_thumbnail( $po->ID )
				? '<div class="blog-card-thumb"><a href="' . esc_url( get_permalink( $po->ID ) ) . '">'
					. get_the_post_thumbnail( $po->ID, 'medium_large', array( 'alt' => esc_attr( get_the_title( $po->ID ) ) ) ) . '</a></div>'
				: '<span class="dest-guia-ic" aria-hidden="true"><i class="fa-solid fa-book-open"></i></span>';
			echo '<div class="col-lg-4 col-md-6"><article class="blog-card' . ( has_post_thumbnail( $po->ID ) ? '' : ' dest-guia-sin-img' ) . '">'
				. $thumb
				. '<div class="blog-card-body"><h3 class="blog-card-title"><a href="' . esc_url( get_permalink( $po->ID ) ) . '">'
				. esc_html( get_the_title( $po->ID ) ) . '</a></h3>'
				. '<p>' . esc_html( wp_trim_words( get_the_excerpt( $po->ID ), 18 ) ) . '</p></div></article></div>';
		}
		echo '</div>';
		grenvios_dsec_close();
	}

	/* ── Preguntas frecuentes, con marcado FAQPage ── */
	$faq = array();
	if ( trim( (string) $d['tiempo'] ) !== '' ) {
		$faq[] = array( '¿Cuánto demora un envío a ' . $d['title'] . '?',
			'El plazo es de ' . $d['tiempo'] . ' desde que el envío sale de {{origen_ciudad}}. Son días hábiles: no cuentan fines de semana ni feriados.' );
	}
	if ( trim( (string) $d['entrega'] ) !== '' ) {
		$faq[] = array( '¿Cómo lo recibe mi destinatario en ' . $d['title'] . '?',
			$d['casa']
				? 'A domicilio, en la dirección que indiques. Hacen falta la dirección completa con una referencia y un teléfono local que conteste.'
				: 'En la agencia local que le corresponde. Recibe el aviso cuando llega y lo retira con su documento de identidad, que debe coincidir con el nombre del envío.' );
	}
	if ( $d['impuesto'] !== '' ) {
		$faq[] = array( '¿Tiene que pagar algo al recibir en ' . $d['title'] . '?',
			'No. El impuesto de aproximadamente ' . $d['impuesto'] . ' % sobre el valor declarado se cancela en {{origen_ciudad}} al despachar, así que quien recibe no adelanta dinero.' );
	}
	if ( $d['aereo'] && $d['terr'] ) {
		$faq[] = array( '¿Aéreo o terrestre a ' . $d['title'] . '?',
			'Aéreo si corre prisa o pesa poco; terrestre si abulta y la fecha no aprieta. Un bulto voluminoso paga por el espacio que ocupa, no por su peso.' );
	}
	$faq[] = array( '¿Puedo saber dónde está mi envío a ' . $d['title'] . '?',
		'Sí. Al despachar recibes un número de seguimiento que muestra el estado del envío en cada etapa, incluido el paso por aduana.' );

	if ( $faq ) {
		grenvios_dsec_open( 'bg-grey', 'Dudas', 'Preguntas frecuentes sobre ' . $p );
		echo '<div class="dest-seo-faq">';
		foreach ( $faq as $q ) {
			echo '<div class="dest-seo-faq-item"><h3>' . esc_html( $q[0] ) . '</h3><p>' . wp_kses_post( $q[1] ) . '</p></div>';
		}
		echo '</div>';
		grenvios_dsec_close();

		/* Un solo FAQPage por página. La sección genérica del tema («Resolvemos tus
		 * dudas») también emitía el suyo, y dos bloques FAQPage en la misma URL
		 * es un error: Google puede descartar los dos. Aquí se recogen las dos
		 * tandas de preguntas en un único nodo, y más abajo se le dice al tema
		 * que no emita el suyo. */
		$todas = $faq;
		if ( function_exists( 'grenvios_repeater' ) ) {
			foreach ( (array) grenvios_repeater( 'page_faq' ) as $it ) {
				$q = isset( $it['q'] ) ? $it['q'] : ( isset( $it[0] ) ? $it[0] : '' );
				$a = isset( $it['a'] ) ? $it['a'] : ( isset( $it[1] ) ? $it[1] : '' );
				if ( trim( (string) $q ) === '' ) continue;
				$todas[] = array( $q, $a );
			}
		}

		$nodo = array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => array() );
		$vistas = array();
		foreach ( $todas as $q ) {
			$name = wp_strip_all_tags( grenvios_sede_tokens_apply( $q[0] ) );
			if ( $name === '' || isset( $vistas[ $name ] ) ) continue;   // sin repetir preguntas
			$vistas[ $name ] = true;
			$nodo['mainEntity'][] = array(
				'@type'          => 'Question',
				'name'           => $name,
				'acceptedAnswer' => array( '@type' => 'Answer', 'text' => wp_strip_all_tags( grenvios_sede_tokens_apply( $q[1] ) ) ),
			);
		}
		$GLOBALS['grenvios_dsec_faq_emitido'] = true;
		echo '<script type="application/ld+json">' . wp_json_encode( $nodo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>';
	}
}, 10, 2 );
