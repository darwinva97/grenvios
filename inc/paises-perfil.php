<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Perfil editorial de cada país: lo que de verdad cambia entre rutas
 * ══════════════════════════════════════════════════════════════════════════
 *
 * AUDITORÍA (2026-10-02, quitando el nombre del país del texto):
 *   · /ec/cuanto-cuesta-enviar-a-ecuador/ y /co/cuanto-cuesta-enviar-a-colombia/
 *     coincidían en un 85 %; /ec/ y /cu/ en un 59 %; las fichas entre sí, 82 %.
 *   · Las páginas de servicio de cada ruta publicaban en su FAQPage las mismas
 *     preguntas genéricas que las otras nueve rutas.
 *
 * Ecuador y Colombia tienen la misma ficha técnica (aéreo + terrestre, retiro
 * en agencia, impuesto parecido), así que con los datos del gestor no se podía
 * escribir nada distinto. Lo que sí es distinto, y le sirve al que envía:
 *
 *   · el organismo de aduana que revisa el envío al llegar;
 *   · el documento con el que se identifica el destinatario;
 *   · cómo se escribe una dirección en ese país (cada uno tiene su costumbre);
 *   · la moneda y quién envía por esa ruta;
 *   · qué admite la ruta en medicinas, alimentos y aparatos con batería
 *     (reglas del propio sitio: FAQ de «Qué se puede enviar»).
 *
 * Son datos estables y verificables; ninguna cifra de precio ni de plazo (esas
 * salen del gestor). Se pintan al vuelo —no se escriben en el contenido—, así
 * que no pisan nada que la clienta haya retocado en el bloque de país, y sus
 * textos son editables desde el panel por el mecanismo automático (srv-section).
 *
 * Dónde salen:
 *   · secciones «Datos de <país>», «Cómo escribir la dirección» y «Qué admite
 *     la ruta» repartidas por tipo de página (grenvios_perfil_matriz);
 *   · preguntas frecuentes propias en cada página de la ruta, que sustituyen a
 *     las genéricas repetidas en las diez rutas.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function grenvios_perfil_paises() {
	return apply_filters( 'grenvios_perfil_paises', array(
		'ecuador' => array(
			'aduana'    => 'el SENAE (Servicio Nacional de Aduana del Ecuador)',
			'documento' => 'cédula de ciudadanía',
			'moneda'    => 'dólar estadounidense: Ecuador está dolarizado, así que el valor declarado se entiende sin conversiones',
			'direccion' => array(
				'formato' => 'Calle principal, número y calle transversal (la intersección), referencia, ciudad y provincia',
				'ejemplo' => 'Av. Amazonas N24-03 y Wilson, junto a la farmacia · Quito, Pichincha',
				'consejo' => 'En Ecuador la dirección se ubica por la intersección de dos calles: sin la calle transversal, el repartidor de la agencia no encuentra la casa ni avisa bien al destinatario.',
			),
			'comunidad' => 'Ecuador es vecino y la ruta tiene dos vías: la terrestre, por la frontera de Huaquillas, mueve paquetes y carga de comerciantes de ambos lados; la aérea, documentos y encargos familiares que no pueden esperar.',
		),
		'colombia' => array(
			'aduana'    => 'la DIAN (Dirección de Impuestos y Aduanas Nacionales)',
			'documento' => 'cédula de ciudadanía',
			'moneda'    => 'peso colombiano',
			'direccion' => array(
				'formato' => 'Tipo de vía (Calle, Carrera, Avenida, Diagonal o Transversal), número de la vía, «#», número de la vía que cruza y placa, barrio, ciudad y departamento',
				'ejemplo' => 'Carrera 43A # 14-27, barrio El Poblado · Medellín, Antioquia',
				'consejo' => 'La nomenclatura colombiana ya es una coordenada: «Calle 45 # 12-30» es la casa 30 de la Calle 45 cerca de la Carrera 12. Copiada tal cual, sin reordenar, el envío no se pierde.',
			),
			'comunidad' => 'En Lima vive una comunidad colombiana cada vez más grande que manda a casa documentos, regalos y encargos; y hay comercio que viaja en las dos direcciones por la vía terrestre.',
		),
		'chile' => array(
			'aduana'    => 'el Servicio Nacional de Aduanas de Chile',
			'documento' => 'cédula de identidad (RUN)',
			'moneda'    => 'peso chileno',
			'direccion' => array(
				'formato' => 'Calle y número, número de departamento o block si lo hay, comuna, ciudad y región',
				'ejemplo' => 'Av. Providencia 1208, depto. 504 · Providencia, Santiago, Región Metropolitana',
				'consejo' => 'En Chile manda la comuna, no solo la ciudad: Santiago tiene decenas y la misma calle puede repetirse en varias. Como a Chile entregamos a domicilio, una dirección sin comuna es la causa más común de reintento.',
			),
			'comunidad' => 'Santiago concentra una de las comunidades peruanas más grandes fuera del país. De ahí sale buena parte de lo que se envía a Chile: encargos familiares, productos peruanos y mudanzas pequeñas de quienes se instalan.',
		),
		'bolivia' => array(
			'aduana'    => 'la Aduana Nacional de Bolivia',
			'documento' => 'cédula de identidad (CI)',
			'moneda'    => 'boliviano',
			'direccion' => array(
				'formato' => 'Zona o barrio, calle o avenida, número de casa y referencias, ciudad y departamento',
				'ejemplo' => 'Zona Sopocachi, calle Belisario Salinas n.º 512, a media cuadra de la plaza · La Paz',
				'consejo' => 'En Bolivia la zona dice más que el número: muchas calles cambian de nombre o se repiten. Como el envío se retira en agencia, lo esencial es que el nombre coincida con la CI del destinatario.',
			),
			'comunidad' => 'Bolivia es vecina y es de las pocas rutas por donde viajan medicinas y alimentos sellados por tierra. Por eso la usan familias que mandan tratamientos y productos peruanos, y comerciantes que mueven mercadería.',
		),
		'argentina' => array(
			'aduana'    => 'la Aduana argentina, que hoy depende de ARCA (la antigua AFIP)',
			'documento' => 'DNI',
			'moneda'    => 'peso argentino',
			'direccion' => array(
				'formato' => 'Calle y altura (número), piso y departamento, localidad, provincia y código postal',
				'ejemplo' => 'Av. Corrientes 3247, piso 4, depto. B · CABA (C1193), Buenos Aires',
				'consejo' => 'En Argentina el número de la calle es la «altura» y conviene añadir piso y departamento por separado. El código postal identifica la localidad y evita confusiones entre calles homónimas de provincias distintas.',
			),
			'comunidad' => 'Buenos Aires tiene una comunidad peruana grande y asentada desde hace décadas: estudiantes, familias y emprendedores que encargan productos peruanos, documentos para trámites y regalos.',
		),
		'estados-unidos' => array(
			'aduana'    => 'la CBP (U.S. Customs and Border Protection)',
			'documento' => 'documento de identidad con foto (pasaporte, licencia de conducir o ID estatal; en EE. UU. no hay un documento nacional único)',
			'moneda'    => 'dólar estadounidense',
			'direccion' => array(
				'formato' => 'Número y calle, número de apartamento («Apt») o suite, ciudad, estado (abreviatura de dos letras) y ZIP code, en ese orden',
				'ejemplo' => '1450 NW 7th St, Apt 12 · Miami, FL 33125',
				'consejo' => 'El formato estadounidense va al revés que el nuestro: primero el número y luego la calle. Sin el ZIP code y el número de apartamento la entrega puerta a puerta se retrasa, sobre todo en edificios.',
			),
			'comunidad' => 'La comunidad peruana en Estados Unidos se concentra en el sur de Florida, Nueva Jersey y Nueva York. Desde Lima se le envían documentos para trámites, productos peruanos y encargos que no se consiguen allí.',
		),
		'espana' => array(
			'aduana'    => 'el Departamento de Aduanas de la Agencia Tributaria',
			'documento' => 'DNI, o NIE si el destinatario es extranjero residente',
			'moneda'    => 'euro',
			'direccion' => array(
				'formato' => 'Calle, número, piso y puerta, código postal de cinco cifras, municipio y provincia',
				'ejemplo' => 'Calle de Bravo Murillo 112, 3.º B · 28020 Madrid, Madrid',
				'consejo' => 'En España el piso y la puerta («3.º B») son parte de la dirección, no un detalle: en un edificio sin ellos el envío vuelve. El código postal tiene que corresponder al municipio.',
			),
			'comunidad' => 'Madrid y Barcelona reúnen a la mayor parte de los peruanos en España. Mucho de lo que se envía son documentos —partidas, títulos, poderes— para trámites de residencia y homologación, a menudo con apostilla.',
		),
		'venezuela' => array(
			'aduana'    => 'el SENIAT (Servicio Nacional Integrado de Administración Aduanera y Tributaria)',
			'documento' => 'cédula de identidad',
			'moneda'    => 'bolívar',
			'direccion' => array(
				'formato' => 'Urbanización o sector, avenida o calle, nombre o número del edificio o casa, punto de referencia, municipio y estado',
				'ejemplo' => 'Urb. La Candelaria, av. Este 2, edif. Luna, apto. 3-B, frente a la plaza · Caracas, Distrito Capital',
				'consejo' => 'En Venezuela el punto de referencia es parte de la dirección: muchas viviendas se ubican por el edificio o el comercio de al lado. Añade también un teléfono con WhatsApp del destinatario.',
			),
			'comunidad' => 'En Perú vive una de las comunidades venezolanas más grandes de la región, y es la que sostiene esta ruta: ropa, calzado, artículos de higiene y regalos para la familia, consolidados en un solo bulto.',
		),
		'cuba' => array(
			'aduana'    => 'la Aduana General de la República de Cuba',
			'documento' => 'carné de identidad',
			'moneda'    => 'peso cubano',
			'direccion' => array(
				'formato' => 'Calle y número, entre qué calles está («e/»), reparto, municipio y provincia',
				'ejemplo' => 'Calle 23 n.º 1155, e/ 10 y 12, Vedado · Plaza de la Revolución, La Habana',
				'consejo' => 'En Cuba la dirección se completa con las dos calles entre las que está la vivienda y el reparto. Como el envío se retira en agencia, el nombre tiene que coincidir con el carné de identidad.',
			),
			'comunidad' => 'La ruta a Cuba la usan sobre todo familias cubanas que viven en Perú y mandan medicinas con receta, ropa y artículos de primera necesidad.',
		),
	) );
}

/* Reglas del sitio por ruta (FAQ «Qué se puede enviar» de la ruta principal). */
function grenvios_perfil_reglas( $slug, $d ) {
	$med = array( 'cuba' => 'Sí, por vía aérea y presentando la receta médica.', 'ecuador' => 'Sí, por vía terrestre.', 'bolivia' => 'Sí, por vía terrestre.' );
	$ali = array( 'ecuador' => 'Sí, sellados y no perecibles, por vía terrestre.', 'bolivia' => 'Sí, sellados y no perecibles, por vía terrestre.' );
	if ( $slug === 'chile' )        $bat = 'No: por vía aérea no se admiten aparatos con batería interna, y la ruta terrestre a Chile tampoco los acepta.';
	elseif ( ! empty( $d['terr'] ) ) $bat = 'Sí, por vía terrestre. Por vía aérea no se admiten aparatos con batería interna.';
	else                             $bat = 'No: la ruta es solo aérea y por avión no se admiten aparatos con batería interna.';
	return array(
		'medicinas' => isset( $med[ $slug ] ) ? $med[ $slug ] : 'No por esta ruta. Pregúntanos por alternativas antes de comprar el tratamiento.',
		'alimentos' => isset( $ali[ $slug ] ) ? $ali[ $slug ] : 'No por esta ruta: por vía aérea no se admiten alimentos.',
		'baterias'  => $bat,
	);
}

/* Datos del país actual (ruta), o de un slug dado. */
function grenvios_perfil_de( $slug ) {
	$p = grenvios_perfil_paises();
	return isset( $p[ $slug ] ) ? $p[ $slug ] : array();
}

/* El contexto «quién envía» rellena el campo del gestor si está vacío. */
add_filter( 'grenvios_pais_datos', function ( $d ) {
	if ( ! is_array( $d ) || empty( $d['slug'] ) ) return $d;
	$pf = grenvios_perfil_de( $d['slug'] );
	if ( $pf && isset( $d['comunidad'] ) && trim( (string) $d['comunidad'] ) === '' ) $d['comunidad'] = $pf['comunidad'];
	return $d;
}, 30 );

/* ─────────────────────────────────────────────────────────────────────────
 * Secciones
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_perfil_sec_datos( $slug, $d ) {
	$pf = grenvios_perfil_de( $slug );
	if ( ! $pf ) return '';
	$p   = esc_html( $d['title'] );
	$ciu = function_exists( 'grenvios_pais_lista' ) ? array_slice( grenvios_pais_lista( $d['ciudades'] ), 0, 4 ) : array();
	$fichas = array(
		array( 'fa-building-columns', 'Aduana que revisa tu envío', ucfirst( $pf['aduana'] ) ),
		array( 'fa-id-card', 'Documento del destinatario', ucfirst( $pf['documento'] ) ),
		array( 'fa-coins', 'Moneda', ucfirst( $pf['moneda'] ) ),
	);
	if ( $ciu ) $fichas[] = array( 'fa-location-dot', 'Ciudades a las que más se envía', implode( ', ', $ciu ) );
	$h = '<ul class="gr-pf-fichas">';
	foreach ( $fichas as $f ) {
		$h .= '<li><span class="gr-pf-ic" aria-hidden="true"><i class="fa-solid ' . $f[0] . '"></i></span><span class="gr-pf-tx"><strong>' . esc_html( $f[1] ) . '</strong><span>' . esc_html( $f[2] ) . '</span></span></li>';
	}
	$h .= '</ul><p class="gr-pf-com">' . esc_html( $pf['comunidad'] ) . '</p>';
	return '<section class="srv-section gr-pais-sec gr-pf gr-pf--datos padding"><div class="container"><div class="srv-head text-center"><p class="sub-heading">Ficha de la ruta</p><h2>Lo que conviene saber de ' . $p . ' antes de enviar</h2></div>' . $h . '</div></section>';
}

function grenvios_perfil_sec_direccion( $slug, $d ) {
	$pf = grenvios_perfil_de( $slug );
	if ( ! $pf ) return '';
	$p  = esc_html( $d['title'] );
	$dr = $pf['direccion'];
	$h  = '<div class="gr-pf-dir"><div class="gr-pf-dir-tx"><p><strong>Formato:</strong> ' . esc_html( $dr['formato'] ) . '.</p>'
		. '<p class="gr-pf-ej"><span>Ejemplo</span>' . esc_html( $dr['ejemplo'] ) . '</p>'
		. '<p>' . esc_html( $dr['consejo'] ) . '</p>'
		. '<p>Además del domicilio, pide al destinatario su <strong>' . esc_html( $pf['documento'] ) . '</strong> y un teléfono que conteste: '
		. ( ! empty( $d['casa'] ) ? 'en ' . $p . ' entregamos a domicilio y el repartidor llama antes de llegar.' : 'en ' . $p . ' el envío se retira en agencia y le avisamos por ese número cuando esté listo.' ) . '</p></div></div>';
	return '<section class="srv-section gr-pais-sec gr-pf gr-pf--dir bg-grey padding"><div class="container"><div class="srv-head text-center"><p class="sub-heading">Datos del destinatario</p><h2>Cómo escribir una dirección en ' . $p . ' para que el envío llegue</h2></div>' . $h . '</div></section>';
}

function grenvios_perfil_sec_reglas( $slug, $d ) {
	$pf = grenvios_perfil_de( $slug );
	if ( ! $pf ) return '';
	$p = esc_html( $d['title'] );
	$r = grenvios_perfil_reglas( $slug, $d );
	$filas = array(
		array( 'fa-pills', 'Medicinas', $r['medicinas'] ),
		array( 'fa-jar', 'Alimentos', $r['alimentos'] ),
		array( 'fa-battery-half', 'Celulares, laptops y aparatos con batería', $r['baterias'] ),
	);
	$h = '<ul class="gr-pf-reglas">';
	foreach ( $filas as $f ) {
		$si = strpos( $f[2], 'Sí' ) === 0;
		$h .= '<li class="' . ( $si ? 'is-si' : 'is-no' ) . '"><span class="gr-pf-ic" aria-hidden="true"><i class="fa-solid ' . $f[0] . '"></i></span><span class="gr-pf-tx"><strong>' . esc_html( $f[1] ) . '</strong><span>' . esc_html( $f[2] ) . '</span></span></li>';
	}
	$h .= '</ul><p class="gr-pf-com">Al llegar, el envío lo revisa ' . esc_html( $pf['aduana'] ) . '. Describe el contenido con detalle y declara su valor real: es lo que más agiliza esa revisión.</p>';
	return '<section class="srv-section gr-pais-sec gr-pf gr-pf--reglas padding"><div class="container"><div class="srv-head text-center"><p class="sub-heading">Lo que admite la ruta</p><h2>Medicinas, alimentos y baterías: qué se puede enviar a ' . $p . '</h2></div>' . $h . '</div></section>';
}

/* Qué secciones lleva cada tipo de página de la ruta. */
function grenvios_perfil_matriz() {
	return apply_filters( 'grenvios_perfil_matriz', array(
		'home'                                 => array( 'datos' ),
		/* Las fichas ya llevan «Datos prácticos» (aduana, documento, dirección y código
		 * postal: inc/destinos-datos-practicos.php); aquí solo lo que no tenían. */
		'_ficha'                               => array( 'reglas' ),
		'servicios'                            => array( 'datos' ),
		'envio-internacional-de-paquetes'      => array( 'direccion', 'reglas' ),
		'envio-internacional-de-documentos'    => array( 'direccion' ),
		'carga-internacional'                  => array( 'datos' ),
		'envio-de-equipaje'                    => array( 'reglas' ),
		'envio-de-compras'                     => array( 'reglas' ),
		'envio-de-alimentos'                   => array( 'reglas' ),
		'apostilla-y-traduccion'               => array( 'datos' ),
		'peso-volumetrico'                     => array( 'datos' ),
		'tiempos-de-entrega'                   => array( 'direccion' ),
		'que-se-puede-enviar'                  => array( 'reglas' ),
		'aduanas-e-impuestos'                  => array( 'datos', 'reglas' ),
		'seguro-de-envios'                     => array( 'datos' ),
		'cotizar'                              => array( 'direccion' ),
		'rastreo-de-envios'                    => array( 'direccion' ),
		'como-enviar-un-paquete-al-extranjero' => array( 'direccion', 'reglas' ),
		'recojo-a-domicilio-lima'              => array( 'direccion' ),
		'envios-desde-provincias'              => array( 'datos' ),
		'envios-para-empresas'                 => array( 'datos' ),
		'nosotros'                             => array( 'datos' ),
		'contacto'                             => array( 'direccion' ),
		'preguntas-frecuentes'                 => array( 'datos', 'reglas' ),
		'blog'                                 => array( 'datos' ),
	) );
}

function grenvios_perfil_render( $base, $slug_pais ) {
	$d = function_exists( 'grenvios_pais_datos' ) ? grenvios_pais_datos( $slug_pais ) : array();
	if ( ! $d || ! grenvios_perfil_de( $d['slug'] ) ) return '';
	$m = grenvios_perfil_matriz();
	$secs = isset( $m[ $base ] ) ? $m[ $base ] : array( 'datos' );
	$h = '';
	foreach ( $secs as $s ) {
		$fn = 'grenvios_perfil_sec_' . $s;
		if ( function_exists( $fn ) ) $h .= $fn( $d['slug'], $d );
	}
	return $h;
}

/* País de la ruta actual ('' en la ruta principal). */
function grenvios_perfil_pais_actual() {
	if ( ! function_exists( 'grenvios_i18n_current' ) || ! function_exists( 'grenvios_es_ruta_pais' ) ) return '';
	$lang = grenvios_i18n_current();
	if ( ! grenvios_es_ruta_pais( $lang ) || ! function_exists( 'grenvios_sede_destino_propio' ) ) return '';
	return (string) grenvios_sede_destino_propio( $lang );
}

/* Páginas de la ruta (no fichas): tras el bloque de país. */
add_filter( 'grenvios_pais_render_extra', function ( $html, $post_id ) {
	$pais = grenvios_perfil_pais_actual();
	if ( $pais === '' ) return $html;
	if ( function_exists( 'grenvios_espejo_es' ) && grenvios_espejo_es( $post_id ) ) return $html;
	$base = function_exists( 'grenvios_canonical_slug' ) ? grenvios_canonical_slug( $post_id ) : '';
	if ( is_front_page() ) $base = 'home';
	if ( $base === '' || ( function_exists( 'grenvios_destinos' ) && array_key_exists( $base, grenvios_destinos() ) ) ) return $html;
	return $html . grenvios_perfil_render( $base, $pais );
}, 10, 2 );

/* Fichas de destino (en su ruta y en /destinos/<país>/): antes del cotizador final. */
add_action( 'grenvios_destino_antes_cta', function ( $slug, $d0 ) {
	if ( ! grenvios_perfil_de( $slug ) ) return;
	$d = function_exists( 'grenvios_pais_datos' ) ? grenvios_pais_datos( $slug ) : array();
	if ( ! $d ) return;
	foreach ( grenvios_perfil_matriz()['_ficha'] as $s ) {
		$fn = 'grenvios_perfil_sec_' . $s;
		if ( function_exists( $fn ) ) echo $fn( $slug, $d );
	}
}, 5, 2 );

/* ─────────────────────────────────────────────────────────────────────────
 * Preguntas frecuentes propias de cada página de la ruta
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_perfil_faq_banco( $slug, $d ) {
	$pf = grenvios_perfil_de( $slug );
	if ( ! $pf ) return array();
	$p  = $d['title'];
	$r  = grenvios_perfil_reglas( $slug, $d );
	$ciu = function_exists( 'grenvios_pais_lista' ) ? grenvios_pais_lista( $d['ciudades'] ) : array();
	$b = array(
		'aduana'    => array( '¿Quién revisa mi envío cuando llega a ' . $p . '?', 'Lo revisa ' . $pf['aduana'] . '. Para que el trámite no se detenga, la descripción del contenido y el valor declarado tienen que coincidir con la boleta o factura; nosotros los revisamos contigo antes de despachar en {{origen_ciudad}}.' ),
		'documento' => array( '¿Qué documento necesita el destinatario en ' . $p . '?', 'Su ' . $pf['documento'] . '. ' . ( ! empty( $d['casa'] ) ? 'Con él se confirma la entrega en su domicilio.' : 'Lo presenta al retirar el envío en la agencia, y el nombre tiene que coincidir exactamente con el de la guía.' ) ),
		'direccion' => array( '¿Cómo escribo la dirección de un envío a ' . $p . '?', $pf['direccion']['formato'] . '. Por ejemplo: «' . $pf['direccion']['ejemplo'] . '». ' . $pf['direccion']['consejo'] ),
		'medicinas' => array( '¿Puedo enviar medicinas a ' . $p . '?', $r['medicinas'] ),
		'alimentos' => array( '¿Puedo enviar alimentos a ' . $p . '?', $r['alimentos'] ),
		'baterias'  => array( '¿Puedo enviar un celular o una laptop a ' . $p . '?', $r['baterias'] ),
	);
	if ( count( $ciu ) >= 3 ) {
		$b['ciudades'] = array( '¿Llegan a ' . $ciu[2] . ' y a otras ciudades de ' . $p . '?', 'Sí. Las ciudades a las que más se envía son ' . grenvios_pais_frase_lista( array_slice( $ciu, 0, 6 ) ) . ', y llegamos también al resto del país: dinos la dirección al cotizar y te confirmamos plazo y forma de entrega.' );
	}
	if ( ! empty( $d['impuesto'] ) ) {
		$b['impuesto'] = array( '¿Cuánto impuesto se paga en un envío terrestre a ' . $p . '?', 'Aproximadamente un ' . str_replace( '.', ',', $d['impuesto'] ) . ' % sobre el valor declarado en la boleta o factura, y se paga en {{origen_ciudad}} al despachar: el destinatario no paga nada al recibir. Por vía aérea depende del contenido y te lo confirmamos al cotizar.' );
	} else {
		$b['impuesto'] = array( '¿Mi envío a ' . $p . ' paga impuestos?', 'Depende del contenido y de su valor declarado: ' . $pf['aduana'] . ' aplica sus propios criterios. Te lo confirmamos al cotizar, antes de despachar, para que no haya sorpresas al llegar.' );
	}
	return $b;
}

function grenvios_perfil_faq_matriz() {
	return array(
		'home'                                 => array( 'direccion', 'aduana' ),
		'_ficha'                               => array( 'aduana', 'documento', 'direccion', 'medicinas', 'ciudades' ),
		'servicios'                            => array( 'aduana', 'documento', 'ciudades' ),
		'envio-internacional-de-paquetes'      => array( 'direccion', 'baterias', 'impuesto', 'aduana' ),
		'envio-internacional-de-documentos'    => array( 'documento', 'direccion', 'aduana' ),
		'carga-internacional'                  => array( 'impuesto', 'aduana', 'ciudades' ),
		'envio-de-equipaje'                    => array( 'baterias', 'impuesto', 'direccion' ),
		'envio-de-compras'                     => array( 'baterias', 'impuesto', 'aduana' ),
		'envio-de-alimentos'                   => array( 'alimentos', 'medicinas', 'aduana' ),
		'apostilla-y-traduccion'               => array( 'documento', 'aduana', 'direccion' ),
		'peso-volumetrico'                     => array( 'impuesto', 'direccion', 'ciudades' ),
		'tiempos-de-entrega'                   => array( 'aduana', 'direccion', 'ciudades' ),
		'que-se-puede-enviar'                  => array( 'medicinas', 'alimentos', 'baterias', 'aduana' ),
		'aduanas-e-impuestos'                  => array( 'aduana', 'impuesto', 'documento' ),
		'seguro-de-envios'                     => array( 'impuesto', 'aduana', 'documento' ),
		'cotizar'                              => array( 'direccion', 'impuesto', 'ciudades' ),
		'rastreo-de-envios'                    => array( 'documento', 'direccion', 'aduana' ),
		'como-enviar-un-paquete-al-extranjero' => array( 'direccion', 'documento', 'baterias', 'aduana' ),
		'recojo-a-domicilio-lima'              => array( 'direccion', 'documento', 'ciudades' ),
		'envios-desde-provincias'              => array( 'direccion', 'impuesto', 'ciudades' ),
		'envios-para-empresas'                 => array( 'aduana', 'impuesto', 'ciudades' ),
		'nosotros'                             => array( 'ciudades', 'aduana' ),
		'contacto'                             => array( 'direccion', 'documento', 'ciudades' ),
		'preguntas-frecuentes'                 => array( 'aduana', 'documento', 'direccion', 'medicinas', 'alimentos', 'baterias', 'impuesto', 'ciudades' ),
		'blog'                                 => array( 'aduana', 'direccion' ),
	);
}

/* En la ruta de un país: preguntas propias primero; de las heredadas solo se
 * quedan las que ya nombran al país (las genéricas son idénticas en las diez
 * rutas). Si con eso quedaran menos de cuatro, se completa con las genéricas. */
add_filter( 'grenvios_page_faqs', function ( $faqs, $slug ) {
	if ( is_admin() ) return $faqs;
	$pais = grenvios_perfil_pais_actual();
	if ( $pais === '' ) return $faqs;
	$d = function_exists( 'grenvios_pais_datos' ) ? grenvios_pais_datos( $pais ) : array();
	if ( ! $d ) return $faqs;
	$banco = grenvios_perfil_faq_banco( $d['slug'], $d );
	if ( ! $banco ) return $faqs;

	$es_ficha = function_exists( 'grenvios_destinos' ) && array_key_exists( $slug, grenvios_destinos() );
	$m = grenvios_perfil_faq_matriz();
	$claves = $es_ficha ? $m['_ficha'] : ( isset( $m[ $slug ] ) ? $m[ $slug ] : array( 'aduana', 'direccion' ) );

	$propias = array();
	foreach ( $claves as $k ) if ( isset( $banco[ $k ] ) ) $propias[] = $banco[ $k ];

	$p = $d['title'];
	$con_pais = array(); $genericas = array();
	foreach ( (array) $faqs as $f ) {
		if ( ! is_array( $f ) || empty( $f[0] ) ) continue;
		if ( mb_stripos( $f[0] . ' ' . ( isset( $f[1] ) ? $f[1] : '' ), $p ) !== false ) $con_pais[] = $f; else $genericas[] = $f;
	}
	$out = array_merge( $propias, $con_pais );
	/* Sin preguntas repetidas (misma pregunta). */
	$vistas = array(); $final = array();
	foreach ( $out as $f ) { $k = mb_strtolower( $f[0] ); if ( isset( $vistas[ $k ] ) ) continue; $vistas[ $k ] = 1; $final[] = $f; }
	while ( count( $final ) < 4 && $genericas ) $final[] = array_shift( $genericas );
	return $final;
}, 90, 2 );
