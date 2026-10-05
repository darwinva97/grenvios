<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Destinos en la portada: fichas y cómo elegir
 * ══════════════════════════════════════════════════════════════════════════
 *
 * QUÉ HABÍA
 * La portada ya tenía una tabla de rutas (inc/home-seo.php): destino,
 * modalidad, plazo y entrega, con enlace a cada ficha. Está bien como
 * referencia rápida, pero una tabla no vende ni posiciona: no dice nada de
 * cada país, no nombra ni una ciudad y el único texto que rodea a los nueve
 * enlaces son cuatro encabezados de columna.
 *
 * QUÉ SE AÑADE AQUÍ
 *
 *   1) UNA FICHA POR DESTINO, con su propia redacción a partir de sus datos:
 *      la vía que opera, el plazo real, cómo recibe el destinatario, las
 *      ciudades a las que más se envía y qué se manda a ese país. Nueve
 *      bloques de texto distintos entre sí, cada uno enlazando a su ficha con
 *      la keyword de esa página como texto ancla.
 *
 *      Es también donde entran las consultas de ciudad —«envíos a Santiago»,
 *      «enviar a Miami»—, que hoy no aparecían en la portada ni una vez.
 *
 *   2) CÓMO ELEGIR DESTINO, agrupando los países por lo único que de verdad
 *      cambia la experiencia de quien envía: si hay vía terrestre, y si la
 *      entrega es a domicilio o con retiro en agencia. Los grupos se arman
 *      solos con los datos del gestor, así que si un destino cambia de
 *      modalidad, cambia de grupo sin tocar el texto.
 *
 * DÓNDE NO SE PINTA: en las rutas de país. Allí la portada es sobre UN destino
 * y ese trabajo lo hace el bloque por país; repetir aquí los nueve sería el
 * mismo texto en diez portadas, que es justo lo que este sitio evita.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Solo la ruta principal, igual que el resto de secciones de la portada. */
function grenvios_hd_activa() {
	if ( is_admin() ) return false;
	return ! function_exists( 'grenvios_hq_pais' ) || grenvios_hq_pais() === '';
}

function grenvios_hd_textos() {
	return array(
		'home_hd_sub'      => array( 'Destinos · Antetítulo', 'text', 'A dónde enviamos' ),
		'home_hd_title'    => array( 'Destinos · Título', 'html', 'Nuestros destinos, uno por uno <span class="hl">y con sus condiciones</span>' ),
		'home_dest_intro'  => array( 'Destinos · Texto introductorio', 'textarea', 'Enviar a Chile no se parece a enviar a España, y no por la distancia: cambian la vía disponible, el plazo, la forma de entrega y lo que admite cada aduana. Estas son las nueve rutas que operamos de forma regular desde {{origen_ciudad}}, con lo que conviene saber de cada una antes de cotizar.' ),
		'home_elegir_sub'   => array( 'Elegir destino · Antetítulo', 'text', 'Cómo leer estas rutas' ),
		'home_elegir_title' => array( 'Elegir destino · Título', 'html', 'Lo que cambia de un destino <span class="hl">a otro</span>' ),
		'home_elegir_pie'   => array( 'Elegir destino · Cierre', 'textarea', 'Si tu país no está entre estos nueve, no significa que no lleguemos: trabajamos más de treinta destinos y varios se coordinan bajo pedido. Escríbenos con el país y te confirmamos vía, plazo y condiciones reales antes de que compres la caja.' ),
	);
}

add_filter( 'grenvios_text_registry', function ( $reg ) {
	if ( ! isset( $reg['home'] ) ) return $reg;
	if ( ! is_admin() && function_exists( 'grenvios_hq_pais' ) && grenvios_hq_pais() !== '' ) return $reg;

	$t = grenvios_hd_textos();

	$reg['home']['sections']['homedest_fichas'] = array(
		'label'           => 'Inicio · Destinos uno por uno',
		'_no_token_check' => true,
		'sel'             => '.gr-home-dest',
		'fields'          => array(
			'home_hd_sub'     => $t['home_hd_sub'],
			/* Fotos de las tarjetas (vacías = foto de ejemplo del país). */
			'home_hd_img_ecuador' => array( 'Foto · Ecuador', 'image', '' ), 'home_hd_img_colombia' => array( 'Foto · Colombia', 'image', '' ),
			'home_hd_img_chile' => array( 'Foto · Chile', 'image', '' ), 'home_hd_img_bolivia' => array( 'Foto · Bolivia', 'image', '' ),
			'home_hd_img_argentina' => array( 'Foto · Argentina', 'image', '' ), 'home_hd_img_estados_unidos' => array( 'Foto · Estados Unidos', 'image', '' ),
			'home_hd_img_espana' => array( 'Foto · España', 'image', '' ), 'home_hd_img_venezuela' => array( 'Foto · Venezuela', 'image', '' ), 'home_hd_img_cuba' => array( 'Foto · Cuba', 'image', '' ),
			'home_hd_title'   => $t['home_hd_title'],
			'home_dest_intro' => $t['home_dest_intro'],
		),
	);
	$reg['home']['sections']['homedest_elegir'] = array(
		'label'           => 'Inicio · Cómo elegir destino',
		'_no_token_check' => true,
		'sel'             => '.gr-home-elegir',
		'fields'          => array(
			'home_elegir_sub'   => $t['home_elegir_sub'],
			'home_elegir_intro' => array( 'Cómo elegir · Subtítulo', 'text', 'La vía, la entrega y la aduana definen cada ruta.' ),
			'home_elegir_img1'  => array( 'Cómo elegir · Foto 1 (vacía = camión de la marca)', 'image', '' ),
			'home_elegir_img2'  => array( 'Cómo elegir · Foto 2 (vacía = entrega en puerta)', 'image', '' ),
			'home_elegir_img3'  => array( 'Cómo elegir · Foto 3 (vacía = entrega en agencia)', 'image', '' ),
			'home_elegir_title' => $t['home_elegir_title'],
			'home_elegir_pie'   => $t['home_elegir_pie'],
		),
	);
	return $reg;
} );

/* ─────────────────────────────────────────────────────────────────────────
 * Utilidades de datos
 * ───────────────────────────────────────────────────────────────────────── */

/* URL de la ficha del destino en la ruta activa. */
function grenvios_hd_url( $slug ) {
	if ( function_exists( 'grenvios_ficha_destino_url' ) ) return grenvios_ficha_destino_url( $slug );
	return grenvios_url_base() . '/destinos/' . $slug . '/';
}

/* Texto ancla: la keyword objetivo de la ficha, que es la consulta real
 * («envíos a Chile»). Si no se puede resolver, el título del destino. */
function grenvios_hd_ancla( $slug, $titulo ) {
	if ( function_exists( 'grenvios_kw_destino' ) ) {
		$kw = grenvios_kw_destino( $slug );
		if ( $kw !== '' ) return $kw;
	}
	return 'Envíos a ' . $titulo;
}

/* La keyword viene en minúscula porque así se busca; en un titular se abre con
 * mayúscula, respetando las tildes (ucfirst no lo hace). */
function grenvios_hd_titular( $s ) {
	$s = (string) $s;
	if ( $s === '' ) return $s;
	return mb_strtoupper( mb_substr( $s, 0, 1 ) ) . mb_substr( $s, 1 );
}

function grenvios_hd_ciudades( $slug, $n = 3 ) {
	if ( ! function_exists( 'grenvios_pais_datos' ) ) return array();
	$d = grenvios_pais_datos( $slug );
	if ( empty( $d['ciudades'] ) ) return array();
	return array_slice( grenvios_pais_lista( $d['ciudades'] ), 0, $n );
}

/* Slug de la ruta con el plazo más corto (mínimo y, si empatan, máximo). */
function grenvios_hd_mas_rapida() {
	static $r = null;
	if ( $r !== null ) return $r;
	$r = ''; $best = array( 999, 999 );
	foreach ( ( function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array() ) as $slug => $x ) {
		$d = function_exists( 'grenvios_pais_datos' ) ? grenvios_pais_datos( $slug ) : $x;
		if ( ! preg_match_all( '/\d+/', (string) ( isset( $d['tiempo'] ) ? $d['tiempo'] : '' ), $m ) ) continue;
		$k = array( (int) $m[0][0], (int) end( $m[0] ) );
		if ( $k < $best ) { $best = $k; $r = $slug; }
	}
	return $r;
}

/* ─────────────────────────────────────────────────────────────────────────
 * 1) Una ficha por destino
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_hd_tarjetas() {
	if ( ! function_exists( 'grenvios_destinos' ) ) return '';
	$dest = grenvios_destinos();
	if ( ! $dest ) return '';

	$cards = ''; $n = 0;
	foreach ( $dest as $slug => $base ) {
		$d = function_exists( 'grenvios_pais_datos' ) ? grenvios_pais_datos( $slug ) : $base;
		if ( ! $d ) continue;

		$p   = esc_html( $d['title'] );
		$url = grenvios_hd_url( $slug );

		/* Frase 1: la vía, que es lo que decide el precio. */
		/* Antes todas las rutas aéreas decían «la más rápida del catálogo»,
		 * también Cuba (14 días) y Venezuela (15). Ahora solo lo dice la que lo
		 * es según el gestor; las demás dan su plazo. */
		$via = ( ! empty( $d['aereo'] ) && ! empty( $d['terr'] ) )
			? 'Vía aérea y terrestre: la terrestre es la que rinde cuando el bulto abulta y no corre prisa.'
			: ( ! empty( $d['terr'] )
				? 'Vía terrestre, la opción que mejor rinde en bultos voluminosos.'
				: ( $slug === grenvios_hd_mas_rapida()
					? 'Solo vía aérea, y es la ruta más rápida que operamos.'
					: 'Solo vía aérea, con un plazo de ' . esc_html( $d['tiempo'] ) . ' desde el despacho.' ) );

		/* Frase 2: cómo lo recibe el destinatario, que es lo que hay que
		 * preguntarle antes de despachar. */
		$ent = ! empty( $d['casa'] )
			? 'Entregamos en el domicilio del destinatario, así que necesitas su dirección exacta y un teléfono que conteste.'
			: 'El envío se retira en la agencia local que le corresponde, con su documento de identidad.';

		/* Frase 3: ciudades, que es por donde entran las consultas de ciudad. */
		$ciudades = grenvios_hd_ciudades( $slug, 3 );
		/* Sin `grenvios_pais_frase_lista()` a propósito: esa función ya cierra con
		 * «y», y aquí la frase sigue con «y al resto del país» —salía «Santiago,
		 * Valparaíso y Viña del Mar y al resto del país»—. */
		$cfrase = $ciudades
			? 'Llegamos a ' . esc_html( implode( ', ', $ciudades ) ) . ' y al resto del país.'
			: '';

		/* Frase 4: qué se manda de verdad a ese país (nota editorial del
		 * destino, la misma que usa el blog). */
		$prod = function_exists( 'grenvios_pse_nota' ) ? grenvios_pse_nota( $d, 'productos' ) : '';
		$pfrase = $prod !== '' ? 'Lo que más se envía: ' . wp_kses_post( $prod ) . '.' : '';

		/* Diseño de la maqueta del cliente (2026-10-02, skill grenvios-landing):
		 * foto del país, chip vino con el plazo, píldoras de vía y entrega,
		 * ciudades y productos con icono, enlace al pie. La primera ruta sale
		 * en una tarjeta grande. Fotos: campo `home_hd_img_<slug>` o la de
		 * ejemplo del país. */
		$n++;
		$img = trim( (string) grenvios_field( 'home_hd_img_' . str_replace( '-', '_', $slug ), '' ) );
		if ( $img === '' && function_exists( 'grenvios_ej_img' ) ) $img = grenvios_ej_img( $slug );
		$aire = ! empty( $d['aereo'] ); $terr = ! empty( $d['terr'] );
		$via_ic = ( $aire ? '<i class="fa-solid fa-plane" aria-hidden="true"></i>' : '' ) . ( $terr ? '<i class="fa-solid fa-truck" aria-hidden="true"></i>' : '' );
		$ent_ic = ! empty( $d['casa'] ) ? 'fa-house' : 'fa-building';
		$pills  = ( ! empty( $d['modos'] ) ? '<span class="gr-hd-pill"><span class="gr-hd-pill-ic">' . $via_ic . '</span>' . esc_html( $d['modos'] ) . '</span>' : '' )
			. ( ! empty( $d['entrega'] ) ? '<span class="gr-hd-pill"><i class="fa-regular ' . $ent_ic . '" aria-hidden="true"></i>' . esc_html( $d['entrega'] ) . '</span>' : '' );

		$flag = function_exists( 'grenvios_dest_flag_html' ) && function_exists( 'grenvios_dest_iso' ) ? grenvios_dest_flag_html( grenvios_dest_iso( $slug ) ) : '';
		$grande = $n === 1;
		$cards .= '<article class="gr-hd-card' . ( $grande ? ' gr-hd-card--grande' : '' ) . ' wow fade-in-bottom" data-wow-delay="' . ( 100 + ( ( $n - 1 ) % 4 ) * 90 ) . 'ms">'
			. '<h3>' . $flag . '<a href="' . esc_url( $url ) . '">' . esc_html( grenvios_hd_titular( grenvios_hd_ancla( $slug, $d['title'] ) ) ) . '</a></h3>'
			. ( $img !== '' ? '<figure class="gr-hd-foto"><img src="' . esc_url( $img ) . '" alt="" loading="lazy" decoding="async"></figure>' : '' )
			. '<div class="gr-hd-cuerpo">'
			. ( ! empty( $d['tiempo'] ) ? '<p class="gr-hd-plazo"><i class="fa-regular fa-clock" aria-hidden="true"></i><span>' . esc_html( $d['tiempo'] ) . '</span></p>' : '' )
			. ( $pills !== '' ? '<p class="gr-hd-pills">' . $pills . '</p>' : '' )
			. '<p class="gr-hd-via">' . $via . ' ' . $ent . '</p>'
			. ( $cfrase !== '' ? '<p class="gr-hd-ciudades"><i class="fa-solid fa-location-dot" aria-hidden="true"></i><span>' . ( $grande && $ciudades ? '<strong>Principales ciudades</strong>' . esc_html( implode( ', ', $ciudades ) ) . ' y el resto del país.' : $cfrase ) . '</span></p>' : '' )
			. ( $pfrase !== '' ? '<p class="gr-hd-prod"><i class="fa-regular fa-box" aria-hidden="true"></i><span>' . ( $grande ? '<strong>Lo que más se envía</strong>' . wp_kses_post( ucfirst( $prod ) ) . '.' : $pfrase ) . '</span></p>' : '' )
			. '<p class="gr-hd-link"><a href="' . esc_url( $url ) . '">Ver condiciones y plazos de ' . $p . ' <i class="fa-regular fa-arrow-right" aria-hidden="true"></i></a></p>'
			. '</div></article>';
	}
	if ( $cards === '' ) return '';

	$t     = grenvios_hd_textos();
	$sub   = grenvios_field( 'home_hd_sub',     $t['home_hd_sub'][2] );
	$tit   = grenvios_field( 'home_hd_title',   $t['home_hd_title'][2] );
	$intro = grenvios_field( 'home_dest_intro', $t['home_dest_intro'][2] );

	return '<section class="gr-home-dest gr-home-dest--v2 padding-bottom"><div class="container">'
		. '<div class="section-heading text-center mb-40">'
		. ( trim( (string) $sub ) !== '' ? '<h3 class="sub-heading">' . esc_html( $sub ) . '</h3>' : '' )
		. '<h2>' . wp_kses_post( $tit ) . '</h2>'
		. ( trim( (string) $intro ) !== '' ? '<p>' . wp_kses_post( $intro ) . '</p>' : '' )
		. '</div>'
		. '<div class="gr-hd-grid gr-hd-grid--v2">' . $cards . '</div>'
		. '</div></section>';
}

/* ─────────────────────────────────────────────────────────────────────────
 * 2) Cómo elegir: los destinos agrupados por lo que cambia de verdad
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_hd_grupos() {
	$dest = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();
	$g    = array( 'terrestre' => array(), 'aereo_casa' => array(), 'aereo_agencia' => array() );

	foreach ( $dest as $slug => $base ) {
		$d = function_exists( 'grenvios_pais_datos' ) ? grenvios_pais_datos( $slug ) : $base;
		if ( ! $d ) continue;
		if ( ! empty( $d['terr'] ) )        $g['terrestre'][ $slug ]     = $d;
		elseif ( ! empty( $d['casa'] ) )    $g['aereo_casa'][ $slug ]    = $d;
		else                                $g['aereo_agencia'][ $slug ] = $d;
	}
	return $g;
}

function grenvios_hd_lista_enlaces( $grupo ) {
	$out = array();
	foreach ( $grupo as $slug => $d ) {
		$out[] = '<a href="' . esc_url( grenvios_hd_url( $slug ) ) . '">' . esc_html( $d['title'] ) . '</a>';
	}
	return implode( ' · ', $out );
}

function grenvios_hd_elegir() {
	$g = grenvios_hd_grupos();
	$home = function_exists( 'grenvios_url_base' ) ? grenvios_url_base() : home_url();

	/* Diseño de la maqueta del cliente (2026-10-02, skill grenvios-landing):
	 * tres tarjetas con foto, número, título, texto, países en píldoras y dos
	 * etiquetas al pie (vía y entrega); cierre en franja rosada con enlaces y
	 * botón. Las fotos tienen campo en el panel; por defecto, el camión de la
	 * marca y dos fotos de ejemplo. */
	$ej  = function ( $n ) { return function_exists( 'grenvios_ej_img' ) ? grenvios_ej_img( $n ) : ''; };
	$foto = function ( $k, $def ) {
		$v = trim( (string) grenvios_field( $k, '' ) );
		return $v !== '' ? $v : $def;
	};
	$defs = array(
		'terrestre'     => array( 'home_elegir_img1', get_template_directory_uri() . '/assets/img/destino-hero.jpg', 'Destinos con vía terrestre',
			'Son los países a los que se puede llegar por carretera desde {{origen_ciudad}}, y ahí está la decisión que más dinero mueve: la vía aérea se paga por rapidez y la terrestre por volumen, así que una caja grande que no corre prisa cuesta bastante menos por tierra. También es la vía que admite contenidos que el avión no acepta, como los alimentos sellados hacia algunos destinos.',
			array( array( 'fa-truck', 'Aéreo y terrestre' ), array( 'fa-boxes-stacked', 'Mejor para volumen' ) ) ),
		'aereo_casa'    => array( 'home_elegir_img2', $ej( 'entrega' ), 'Solo vía aérea, con entrega a domicilio',
			'Rutas largas donde la carretera no es una opción: el envío vuela y se entrega en la puerta del destinatario. Son nuestros plazos más cortos, y por eso el peso manda: cada kilo cuenta, así que el embalaje ajustado es lo que mantiene el precio a raya. Necesitas la dirección completa —con número de unidad o apartamento y código postal donde aplique— y un teléfono local que conteste.',
			array( array( 'fa-plane', 'Aéreo' ), array( 'fa-house', 'Puerta a puerta' ) ) ),
		'aereo_agencia' => array( 'home_elegir_img3', $ej( 'sobres' ), 'Vía aérea con retiro en agencia',
			'Destinos donde la distribución interna se hace desde una agencia local: cuando el envío llega, el destinatario recibe el aviso y lo retira con su documento. Por eso aquí el nombre de la guía tiene que coincidir <strong>exactamente</strong> con el del documento, sin apodos ni abreviaturas, y conviene avisar al destinatario de que lo espere.',
			array( array( 'fa-plane', 'Aéreo' ), array( 'fa-building', 'En agencia local' ) ) ),
	);

	$bloques = ''; $n = 0;
	foreach ( $defs as $grupo => $def ) {
		if ( empty( $g[ $grupo ] ) ) continue;
		$n++;
		$img  = $foto( $def[0], $def[1] );
		$tags = '';
		foreach ( $def[4] as $tg ) {
			$tags .= '<li><i class="fa-solid ' . esc_attr( $tg[0] ) . '" aria-hidden="true"></i>' . esc_html( $tg[1] ) . '</li>';
		}
		$bloques .= '<div class="gr-hd-grupo gr-he-card wow fade-in-bottom" data-wow-delay="' . ( 100 + ( $n - 1 ) * 110 ) . 'ms">'
			. ( $img !== '' ? '<figure class="gr-he-foto"><img src="' . esc_url( $img ) . '" alt="" loading="lazy" decoding="async"></figure>' : '' )
			. '<div class="gr-he-cab"><span class="gr-he-n" aria-hidden="true">' . sprintf( '%02d', $n ) . '</span><h3>' . esc_html( $def[2] ) . '</h3></div>'
			. '<p>' . wp_kses_post( $def[3] ) . '</p>'
			. '<p class="gr-hd-paises">' . str_replace( ' · ', '', grenvios_hd_lista_enlaces( $g[ $grupo ] ) ) . '</p>'
			. '<ul class="gr-he-tags">' . $tags . '</ul>'
			. '</div>';
	}
	if ( $bloques === '' ) return '';

	$t   = grenvios_hd_textos();
	$sub = grenvios_field( 'home_elegir_sub',   $t['home_elegir_sub'][2] );
	$tit = grenvios_field( 'home_elegir_title', $t['home_elegir_title'][2] );
	$pie = grenvios_field( 'home_elegir_pie',   $t['home_elegir_pie'][2] );
	$int = grenvios_field( 'home_elegir_intro', 'La vía, la entrega y la aduana definen cada ruta.' );

	$cierre = '<div class="gr-hd-pie gr-he-pie wow fade-in-bottom" data-wow-delay="150ms">'
		. '<span class="gr-he-pie-ic" aria-hidden="true"><i class="fa-regular fa-comments"></i></span>'
		. ( trim( (string) $pie ) !== '' ? '<p>' . wp_kses_post( $pie ) . '</p>' : '' )
		. '<ul class="gr-he-pie-links">'
		. '<li><a href="' . esc_url( $home . '/destinos/' ) . '">Mira todos los destinos <i class="fa-regular fa-arrow-right" aria-hidden="true"></i></a></li>'
		. '<li><a href="' . esc_url( $home . '/tiempos-de-entrega/' ) . '">Cómo se cuentan los plazos <i class="fa-regular fa-arrow-right" aria-hidden="true"></i></a></li>'
		. '</ul>'
		. '<a class="default-btn" href="' . esc_url( $home . '/cotizar/' ) . '">Pide tu cotización <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>'
		. '</div>';

	return '<section class="gr-home-elegir gr-home-elegir--v2 padding-bottom"><div class="container">'
		. '<div class="section-heading text-center mb-40">'
		. ( trim( (string) $sub ) !== '' ? '<h3 class="sub-heading">' . esc_html( $sub ) . '</h3>' : '' )
		. '<h2>' . wp_kses_post( $tit ) . '</h2>'
		. ( trim( (string) $int ) !== '' ? '<p>' . esc_html( $int ) . '</p>' : '' )
		. '</div>'
		. '<div class="gr-hd-grupos gr-he-grid">' . $bloques . '</div>'
		. $cierre
		. '</div></section>';
}

/* Se imprime desde front-page.php, justo después de las secciones de precio y
 * rutas (inc/home-seo.php), que son las que dan el contexto. */
function grenvios_hd_render() {
	if ( ! grenvios_hd_activa() ) return;
	$html = grenvios_hd_tarjetas() . grenvios_hd_elegir();
	if ( $html === '' ) return;
	echo function_exists( 'grenvios_apply_media_overrides' ) ? grenvios_apply_media_overrides( $html ) : $html;
}

add_action( 'wp_head', function () {
	if ( is_admin() ) return;
	echo '<style id="grenvios-home-dest-css">'
		/* Fichas: tarjetas blancas con bandera, datos en pastillas y enlace con flecha. */
		. '.gr-hd-grid{display:grid;gap:20px;grid-template-columns:repeat(auto-fill,minmax(280px,1fr))}'
		. '.gr-hd-card{position:relative;display:flex;flex-direction:column;padding:26px 24px 22px;border:1px solid #ece7e3;border-radius:16px;background:#fff;box-shadow:0 1px 2px rgba(12,12,12,.04);transition:transform .25s ease,box-shadow .25s ease,border-color .25s ease}'
		. '.gr-hd-card:hover{transform:translateY(-3px);border-color:transparent;box-shadow:0 18px 38px -20px rgba(94,33,41,.35)}'
		. '.gr-hd-card h3{display:flex;align-items:center;gap:10px;font-size:20px;font-weight:700;margin:0 0 12px;line-height:1.25;letter-spacing:-.3px}'
		. '.gr-hd-card h3 .gr-flag{width:22px;height:auto;flex:none}'
		. '.gr-hd-card h3 a{color:var(--heading-color,#0c0c0c)}'
		. '.gr-hd-card h3 a::after{content:"";position:absolute;inset:0;border-radius:16px}'
		. '.gr-hd-card p{margin:0 0 10px;font-size:15px;line-height:1.6;color:var(--body-color,#666)}'
		. '.gr-hd-meta{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:14px!important}'
		. '.gr-hd-chip{display:inline-block;font-size:12.5px;font-weight:600;padding:4px 10px;border-radius:999px;background:#f5efec;color:var(--primary-color,#5e2129)}'
		. '.gr-hd-chip:first-child{background:var(--primary-color,#5e2129);color:#fff}'
		. '.gr-hd-ciudades,.gr-hd-prod{font-size:14.5px!important}'
		. '.gr-hd-link{margin:auto 0 0!important;padding-top:14px;border-top:1px solid #f0ebe8;font-weight:700;font-size:14.5px}'
		. '.gr-hd-link a{display:inline-flex;align-items:center;gap:8px;color:var(--primary-color,#5e2129)}'
		. '.gr-hd-link a i{transition:transform .25s ease}.gr-hd-card:hover .gr-hd-link a i{transform:translateX(4px)}'
		/* Grupos: tres tarjetas con icono y los países como pastillas. */
		. '.gr-hd-grupos{display:grid;gap:20px;grid-template-columns:repeat(auto-fit,minmax(300px,1fr))}'
		. '.gr-hd-grupo{display:flex;flex-direction:column;padding:28px 26px;border-radius:16px;background:#faf6f4;border:1px solid #efe7e3}'
		. '.gr-hd-grupo-ic{width:52px;height:52px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;background:var(--primary-color,#5e2129);color:#fff;font-size:21px;margin-bottom:16px}'
		. '.gr-hd-grupo h3{font-size:19px;font-weight:700;margin:0 0 10px;letter-spacing:-.3px}'
		. '.gr-hd-grupo p{font-size:15px;line-height:1.65;margin:0 0 16px;color:var(--body-color,#666)}'
		. '.gr-hd-grupo .gr-hd-paises{margin:auto 0 0;display:flex;flex-wrap:wrap;gap:8px;font-size:0}'
		. '.gr-hd-paises a{display:inline-block;padding:6px 12px;border-radius:999px;background:#fff;border:1px solid #e6dcd7;font-size:14px;font-weight:600;color:var(--heading-color,#0c0c0c);transition:all .2s ease}'
		. '.gr-hd-paises a:hover{background:var(--primary-color,#5e2129);border-color:var(--primary-color,#5e2129);color:#fff}'
		. '.gr-hd-pie{max-width:78ch;margin:30px auto 0;text-align:center;font-size:15.5px;line-height:1.7}'
		. '.gr-hd-pie a{color:var(--primary-color,#5e2129);font-weight:600;text-decoration:underline;text-underline-offset:3px}'
		. '@media(max-width:575px){.gr-hd-card{padding:20px 18px}.gr-hd-grupo{padding:22px 20px}}'
		. '</style>';
}, 109 );

/* ─────────────────────────────────────────────────────────────────────────
 *  Tres preguntas de destino en la portada
 *
 *  La portada respondía «¿a qué países puedo enviar?» y poco más. Estas tres
 *  son las que siguen: cuál llega antes, cuál sale más barato y qué pasa con
 *  los países que no tienen ficha. Van al FAQPage de la portada, así que
 *  pueden salir como respuesta directa en el buscador, y se arman con los
 *  datos del gestor: si mañana cambia un plazo, cambia la respuesta.
 * ───────────────────────────────────────────────────────────────────────── */
add_filter( 'grenvios_page_faqs', function ( $faqs, $slug ) {
	if ( $slug !== 'home' || ! grenvios_hd_activa() ) return $faqs;

	$dest = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();
	if ( ! $dest ) return $faqs;

	/* El destino con el plazo más corto, leído de los datos: se toma el primer
	 * número del rango («4 a 6 días hábiles» → 4). */
	$mejor = ''; $mejor_dias = 0; $mejor_plazo = '';
	foreach ( $dest as $d ) {
		if ( empty( $d['tiempo'] ) || ! preg_match( '/(\d+)/', $d['tiempo'], $m ) ) continue;
		$dias = (int) $m[1];
		if ( $mejor_dias === 0 || $dias < $mejor_dias ) {
			$mejor_dias  = $dias;
			$mejor       = $d['title'];
			$mejor_plazo = $d['tiempo'];
		}
	}

	$terrestres = array();
	foreach ( $dest as $slug_d => $base ) {
		$d = function_exists( 'grenvios_pais_datos' ) ? grenvios_pais_datos( $slug_d ) : $base;
		if ( ! empty( $d['terr'] ) ) $terrestres[] = $d['title'];
	}

	if ( $mejor !== '' ) {
		$faqs[] = array(
			'¿A qué destino llega antes un envío?',
			'Hoy el plazo más corto es el de ' . $mejor . ': ' . $mejor_plazo . ' desde el despacho en {{origen_ciudad}}. '
			. 'Los plazos se cuentan en días hábiles y no incluyen el tiempo que la aduana del país de destino decida tomarse, que es el único tramo que no controlamos.',
		);
	}

	if ( $terrestres ) {
		$faqs[] = array(
			'¿A qué países se puede enviar por vía terrestre?',
			'Por carretera llegamos a ' . grenvios_pais_frase_lista( $terrestres ) . '. '
			. 'Es la vía que conviene cuando el bulto abulta y la fecha no aprieta: se paga por volumen y no por rapidez, así que en cajas grandes la diferencia de precio frente a la aérea es notable.',
		);
	}

	$faqs[] = array(
		'¿Y si mi país no está en la lista de destinos?',
		'Trabajamos más de treinta destinos; los que tienen ficha propia son los que operamos de forma regular. '
		. 'Para el resto coordinamos el envío bajo pedido: escríbenos con el país, el peso y las medidas y te confirmamos vía, plazo y condiciones reales antes de que compres la caja.',
	);

	return $faqs;
}, 26, 2 );
