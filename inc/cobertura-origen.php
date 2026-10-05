<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Cobertura en ORIGEN: distritos de {{origen_ciudad}} y ciudades del país
 * ══════════════════════════════════════════════════════════════════════════
 *
 * AUDITORÍA DE LA RUTA PERÚ (26 páginas, 21.967 palabras):
 *
 *   · CERO menciones de un distrito de Lima en TODO el sitio. Para un courier
 *     cuyo servicio diferencial es el recojo a domicilio, ese es el long tail
 *     local más obvio que existe: «recojo a domicilio Miraflores», «courier
 *     San Isidro», «enviar paquete desde Los Olivos». La página del recojo
 *     tenía 555 palabras y no nombraba ni un distrito.
 *   · Solo dos páginas mencionaban una ciudad del Perú. «Enviar al extranjero
 *     desde Arequipa» o «desde Trujillo» no lo cubría nadie.
 *
 * Aquí se añade esa cobertura, que además es la parte del negocio que NO
 * cambia con el país de destino: el origen siempre es la sede. Por eso vive
 * ligada a la SEDE y no al destino, y se pinta igual en /recojo-a-domicilio-lima/
 * que en la misma página de cualquier ruta de país.
 *
 * QUÉ SE AFIRMA Y QUÉ NO
 * El sitio ya dice que se recoge «dentro de {{origen_ciudad}}» y que «el costo
 * depende del distrito». Listar los distritos describe esa cobertura, no la
 * amplía: el texto mantiene que el costo varía por zona y remite a cotizar.
 * Para provincias, el propio tema ya explica que el bulto llega a la sede por
 * una agencia local; aquí solo se nombran las ciudades desde las que llega.
 *
 * Todo es editable: las listas y los textos se registran como campos del tema,
 * así que la clienta puede quitar un distrito o añadir una ciudad sin tocar
 * código, y cada ruta guarda su propia versión.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ── Datos por sede ─────────────────────────────────────────────────────── */

/* Distritos agrupados por zona. Filtro `grenvios_origen_zonas` para otra sede. */
function grenvios_origen_zonas() {
	$zonas = array(
		'pe' => array(
			'Lima Centro'  => 'Cercado de Lima, Breña, La Victoria, Lince, Jesús María, Pueblo Libre, Magdalena del Mar, San Miguel, Rímac',
			'Lima Moderna' => 'Miraflores, San Isidro, Santiago de Surco, San Borja, La Molina, Barranco, Surquillo, Chorrillos',
			'Lima Norte'   => 'Los Olivos, San Martín de Porres, Independencia, Comas, Carabayllo, Puente Piedra',
			'Lima Este'    => 'San Juan de Lurigancho, Ate, Santa Anita, El Agustino, Lurigancho-Chosica, Chaclacayo',
			'Lima Sur'     => 'Villa El Salvador, Villa María del Triunfo, San Juan de Miraflores, Lurín, Pachacámac',
			'Callao'       => 'Callao, Bellavista, La Perla, Carmen de la Legua, Ventanilla, La Punta',
		),
	);
	$sede = function_exists( 'grenvios_sede' ) ? grenvios_sede() : 'pe';
	$out  = isset( $zonas[ $sede ] ) ? $zonas[ $sede ] : ( isset( $zonas['pe'] ) ? $zonas['pe'] : array() );
	return apply_filters( 'grenvios_origen_zonas', $out, $sede );
}

/* Ciudades del país de origen desde las que llegan envíos a la sede. */
function grenvios_origen_ciudades() {
	$ciudades = array(
		'pe' => 'Arequipa, Trujillo, Chiclayo, Piura, Cusco, Huancayo, Iquitos, Tacna, Chimbote, Ica, Juliaca, Puno, Cajamarca, Ayacucho, Pucallpa, Tarapoto, Huaraz, Tumbes, Sullana, Moquegua',
	);
	$sede = function_exists( 'grenvios_sede' ) ? grenvios_sede() : 'pe';
	$out  = isset( $ciudades[ $sede ] ) ? $ciudades[ $sede ] : ( isset( $ciudades['pe'] ) ? $ciudades['pe'] : '' );
	return apply_filters( 'grenvios_origen_ciudades', $out, $sede );
}

/* ── Campos editables ───────────────────────────────────────────────────── */

function grenvios_co_campos() {
	$zonas = grenvios_origen_zonas();
	$campos = array(
		'co_distritos_sub'   => array( 'Distritos · Antetítulo', 'text', 'Recojo en {{origen_ciudad}} y Callao' ),
		'co_distritos_title' => array( 'Distritos · Título', 'html', 'Distritos donde <span class="hl">recogemos tu envío</span>' ),
		'co_distritos_intro' => array( 'Distritos · Texto introductorio', 'textarea', 'Coordinamos el recojo en toda {{origen_ciudad}} y el Callao. El costo depende de la zona y se te dice al cotizar, junto con el flete, para que veas el total antes de decidir.' ),
		'co_distritos_pie'   => array( 'Distritos · Nota final', 'textarea', '¿Tu distrito no está en la lista o queda en una zona alejada? Escríbenos igual: casi siempre se puede coordinar, y si no, te decimos cuál es la agencia más cercana a la que acercar el bulto.' ),
	);
	foreach ( $zonas as $zona => $lista ) {
		$campos[ 'co_zona_' . sanitize_key( remove_accents( $zona ) ) ] = array( 'Distritos · ' . $zona, 'textarea', $lista );
	}
	$campos['co_ciudades_sub']   = array( 'Provincias · Antetítulo', 'text', 'Desde cualquier ciudad del {{origen_pais}}' );
	$campos['co_ciudades_title'] = array( 'Provincias · Título', 'html', 'Ciudades desde las que <span class="hl">nos llegan envíos</span>' );
	$campos['co_ciudades_intro'] = array( 'Provincias · Texto introductorio', 'textarea', 'No hace falta estar en {{origen_ciudad}} para enviar al extranjero. Estas son las ciudades desde las que más envíos recibimos: el bulto llega a nuestra sede por la agencia de transporte que prefieras y desde aquí sale a su destino internacional.' );
	$campos['co_ciudades_lista'] = array( 'Provincias · Lista de ciudades (separadas por comas)', 'textarea', grenvios_origen_ciudades() );
	$campos['co_ciudades_pie']   = array( 'Provincias · Nota final', 'textarea', 'Da igual desde dónde envíes: el precio del flete internacional es el mismo. Lo que pagas aparte es el tramo interno hasta {{origen_ciudad}}, que contratas con la agencia local que te quede mejor.' );
	return $campos;
}

/* Páginas donde se pinta cada bloque. */
function grenvios_co_paginas() {
	return apply_filters( 'grenvios_co_paginas', array(
		'distritos' => 'recojo-a-domicilio-lima',
		'ciudades'  => 'envios-desde-provincias',
	) );
}

add_filter( 'grenvios_text_registry', function ( $reg ) {
	if ( ! is_admin() && function_exists( 'grenvios_hq_pais' ) && grenvios_hq_pais() !== '' ) return $reg;
	$pg = grenvios_co_paginas();
	$c  = grenvios_co_campos();
	$zonas = array();
	foreach ( grenvios_origen_zonas() as $zona => $_ ) $zonas[ 'co_zona_' . sanitize_key( remove_accents( $zona ) ) ] = $c[ 'co_zona_' . sanitize_key( remove_accents( $zona ) ) ];

	if ( isset( $reg[ $pg['distritos'] ] ) ) {
		$reg[ $pg['distritos'] ]['sections']['cobertura'] = array(
			'label' => 'Cobertura · Distritos de {{origen_ciudad}}',
			'_no_token_check' => true,
			'sel'   => '.gr-cobertura',
			'fields' => array_merge( array(
				'co_distritos_sub'   => $c['co_distritos_sub'],
				'co_distritos_title' => $c['co_distritos_title'],
				'co_distritos_intro' => $c['co_distritos_intro'],
			), $zonas, array( 'co_distritos_pie' => $c['co_distritos_pie'] ) ),
		);
	}
	if ( isset( $reg[ $pg['ciudades'] ] ) ) {
		$reg[ $pg['ciudades'] ]['sections']['cobertura'] = array(
			'label' => 'Cobertura · Ciudades del {{origen_pais}}',
			'_no_token_check' => true,
			'sel'   => '.gr-cobertura',
			'fields' => array(
				'co_ciudades_sub'   => $c['co_ciudades_sub'],
				'co_ciudades_title' => $c['co_ciudades_title'],
				'co_ciudades_intro' => $c['co_ciudades_intro'],
				'co_ciudades_lista' => $c['co_ciudades_lista'],
				'co_ciudades_pie'   => $c['co_ciudades_pie'],
			),
		);
	}
	return $reg;
} );

/* ── Render ─────────────────────────────────────────────────────────────── */

function grenvios_co_lista( $txt ) {
	return array_values( array_filter( array_map( 'trim', explode( ',', (string) $txt ) ) ) );
}

function grenvios_co_distritos_html() {
	$c    = grenvios_co_campos();
	$home = function_exists( 'grenvios_url_base' ) ? grenvios_url_base() : home_url();
	$sub  = grenvios_field( 'co_distritos_sub',   $c['co_distritos_sub'][2] );
	$tit  = grenvios_field( 'co_distritos_title', $c['co_distritos_title'][2] );
	$intro= grenvios_field( 'co_distritos_intro', $c['co_distritos_intro'][2] );
	$pie  = grenvios_field( 'co_distritos_pie',   $c['co_distritos_pie'][2] );

	$grupos = '';
	$total  = 0;
	foreach ( grenvios_origen_zonas() as $zona => $def ) {
		$key  = 'co_zona_' . sanitize_key( remove_accents( $zona ) );
		$dist = grenvios_co_lista( grenvios_field( $key, $def ) );
		if ( ! $dist ) continue;
		$total += count( $dist );
		$chips = '';
		foreach ( $dist as $dd ) $chips .= '<li>' . esc_html( $dd ) . '</li>';
		$grupos .= '<div class="gr-cob-zona"><h3>' . esc_html( $zona ) . '</h3><ul class="gr-cob-chips">' . $chips . '</ul></div>';
	}
	if ( $grupos === '' ) return '';

	return '<section class="gr-cobertura padding-bottom"><div class="container">'
		. '<div class="section-heading text-center mb-40">'
		. ( trim( (string) $sub ) !== '' ? '<h3 class="sub-heading">' . esc_html( $sub ) . '</h3>' : '' )
		. '<h2>' . wp_kses_post( $tit ) . '</h2>'
		. ( trim( (string) $intro ) !== '' ? '<p>' . wp_kses_post( $intro ) . '</p>' : '' )
		. '</div>'
		. '<div class="gr-cob-grid">' . $grupos . '</div>'
		. ( trim( (string) $pie ) !== '' ? '<p class="gr-cob-pie">' . wp_kses_post( $pie ) . ' <a href="' . esc_url( $home . '/cotizar/' ) . '">Cotiza tu envío</a> o <a href="' . esc_url( $home . '/contacto/' ) . '">escríbenos</a>.</p>' : '' )
		. '</div></section>';
}

function grenvios_co_ciudades_html() {
	$c     = grenvios_co_campos();
	$home  = function_exists( 'grenvios_url_base' ) ? grenvios_url_base() : home_url();
	$sub   = grenvios_field( 'co_ciudades_sub',   $c['co_ciudades_sub'][2] );
	$tit   = grenvios_field( 'co_ciudades_title', $c['co_ciudades_title'][2] );
	$intro = grenvios_field( 'co_ciudades_intro', $c['co_ciudades_intro'][2] );
	$pie   = grenvios_field( 'co_ciudades_pie',   $c['co_ciudades_pie'][2] );
	$ciud  = grenvios_co_lista( grenvios_field( 'co_ciudades_lista', $c['co_ciudades_lista'][2] ) );
	if ( ! $ciud ) return '';

	$chips = '';
	foreach ( $ciud as $x ) $chips .= '<li><i class="fa-solid fa-location-dot"></i>' . esc_html( $x ) . '</li>';

	return '<section class="gr-cobertura padding-bottom"><div class="container">'
		. '<div class="section-heading text-center mb-40">'
		. ( trim( (string) $sub ) !== '' ? '<h3 class="sub-heading">' . esc_html( $sub ) . '</h3>' : '' )
		. '<h2>' . wp_kses_post( $tit ) . '</h2>'
		. ( trim( (string) $intro ) !== '' ? '<p>' . wp_kses_post( $intro ) . '</p>' : '' )
		. '</div>'
		. '<ul class="gr-cob-chips gr-cob-chips--ciudades">' . $chips . '</ul>'
		. ( trim( (string) $pie ) !== '' ? '<p class="gr-cob-pie">' . wp_kses_post( $pie ) . ' <a href="' . esc_url( $home . '/recojo-a-domicilio-lima/' ) . '">Si estás en {{origen_ciudad}}, lo recogemos nosotros</a>.</p>' : '' )
		. '</div></section>';
}

/* Se imprime desde page.php, junto al resto de secciones propias.
 *
 * SOLO EN LA RUTA PRINCIPAL. El origen es el mismo para todas las rutas, así
 * que este bloque saldría idéntico en las diez: doscientas cincuenta palabras
 * repetidas en diez URLs del mismo dominio, que es exactamente la señal de
 * duplicado que el resto del sitio se esfuerza en evitar. En las rutas de país
 * se sustituye por una línea que enlaza aquí, donde la cobertura vive. */
function grenvios_co_render( $slug ) {
	if ( is_admin() ) return;
	$pg = grenvios_co_paginas();
	if ( $slug !== $pg['distritos'] && $slug !== $pg['ciudades'] ) return;

	$en_ruta_pais = function_exists( 'grenvios_hq_pais' ) && grenvios_hq_pais() !== '';
	if ( $en_ruta_pais ) { grenvios_co_enlace( $slug ); return; }

	$html = ( $slug === $pg['distritos'] ) ? grenvios_co_distritos_html() : grenvios_co_ciudades_html();
	if ( $html === '' ) return;
	echo function_exists( 'grenvios_apply_media_overrides' ) ? grenvios_apply_media_overrides( $html ) : $html;
}

/* En una ruta de país: una línea con el enlace a la cobertura real. */
function grenvios_co_enlace( $slug ) {
	$pg   = grenvios_co_paginas();
	$base = function_exists( 'grenvios_i18n_site_root' ) ? grenvios_i18n_site_root() : untrailingslashit( home_url() );
	$pais = function_exists( 'grenvios_hq_pais' ) ? grenvios_hq_pais() : '';

	$txt = ( $slug === $pg['distritos'] )
		? 'Recogemos en toda {{origen_ciudad}} y el Callao antes de despachar a ' . esc_html( $pais ) . '. <a href="' . esc_url( $base . '/recojo-a-domicilio-lima/' ) . '">Consulta los distritos con recojo</a>.'
		: 'Si envías desde fuera de {{origen_ciudad}}, tu bulto llega a nuestra sede y de ahí sale a ' . esc_html( $pais ) . '. <a href="' . esc_url( $base . '/envios-desde-provincias/' ) . '">Ciudades desde las que nos llegan envíos</a>.';

	$html = '<section class="gr-cobertura gr-cobertura--nota padding-bottom"><div class="container"><p class="gr-cob-pie">' . $txt . '</p></div></section>';
	echo function_exists( 'grenvios_apply_media_overrides' ) ? grenvios_apply_media_overrides( $html ) : $html;
}

/* ── Preguntas propias de estas dos páginas ─────────────────────────────── */
add_filter( 'grenvios_page_faqs', function ( $faqs, $slug ) {
	$pg = grenvios_co_paginas();
	// Mismo motivo que el render: en las rutas de país mandan las suyas.
	if ( function_exists( 'grenvios_hq_pais' ) && grenvios_hq_pais() !== '' ) return $faqs;

	if ( $slug === $pg['distritos'] ) {
		$faqs[] = array(
			'¿Recogen en mi distrito?',
			'Coordinamos recojo en toda {{origen_ciudad}} y el Callao. El costo varía según la zona y te lo decimos al cotizar, junto con el flete. Si tu dirección queda en una zona alejada, te confirmamos si se puede coordinar o cuál es la agencia más cercana.',
		);
		$faqs[] = array(
			'¿Cuánto cuesta el recojo a domicilio?',
			'Depende del distrito. Se suma al precio del envío y se te informa antes de que decidas: nunca aparece después. En nuestra sede de {{contacto_direccion}} no hay costo de recojo.',
		);
	}

	if ( $slug === $pg['ciudades'] ) {
		$faqs[] = array(
			'¿Desde qué ciudades del {{origen_pais}} puedo enviar?',
			'Desde cualquiera. Recibimos envíos de Arequipa, Trujillo, Chiclayo, Piura, Cusco, Huancayo y del resto del país: el bulto llega a nuestra sede de {{origen_ciudad}} por la agencia de transporte que prefieras y desde aquí lo despachamos al extranjero.',
		);
		$faqs[] = array(
			'¿Cuánto suma enviar desde provincia?',
			'El flete internacional es el mismo. Lo que pagas aparte es el transporte interno hasta {{origen_ciudad}}, que contratas con la agencia local. En tiempo, hay que sumar ese tramo al plazo internacional, que empieza a contar cuando el bulto llega y se despacha.',
		);
	}

	return $faqs;
}, 26, 2 );
