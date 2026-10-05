<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Ficha de destino · «Datos prácticos para enviar a {país}»
 * ══════════════════════════════════════════════════════════════════════════
 *
 * AUDITORÍA (2026-09-28): las nueve fichas tienen 3 200–3 600 palabras y
 * 27–28 H2, pero casi todas sus secciones son la misma plantilla con el
 * nombre del país cambiado. Añadir más plantilla diluye la página. Lo que no
 * tenía ninguna es lo que DE VERDAD cambia de un país a otro y que el cliente
 * necesita para rellenar la guía sin errores:
 *
 *   · qué aduana revisa el envío,
 *   · qué documento de identidad usa quien recibe (clave donde se retira en agencia),
 *   · cómo se escribe una dirección en ese país,
 *   · cómo es su código postal y si se usa.
 *
 * Son datos generales y verificables del país, no promesas del negocio: nada
 * de umbrales de impuestos ni límites de valor, que cambian y se confirman al
 * cotizar (regla de la skill grenvios-diseno).
 *
 * Editable: los seis textos se guardan en la página de la ficha y aparecen en
 * el panel «Editar página» como sección propia.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function grenvios_dp_datos() {
	return array(
		'ecuador' => array(
			'aduana' => 'El Servicio Nacional de Aduana del Ecuador (SENAE) revisa todo lo que entra al país, también los envíos personales y los regalos.',
			'doc'    => 'Cédula de ciudadanía ecuatoriana. Como la entrega es en agencia local, quien retira presenta la cédula a cuyo nombre va el envío: el nombre de la guía tiene que coincidir con el de la cédula.',
			'dir'    => 'Provincia y ciudad, calle principal y calle transversal («Av. 10 de Agosto y Colón»), número de casa y una referencia. Es la forma en que se ubican las direcciones en Ecuador.',
			'cp'     => 'Ecuador tiene código postal de seis dígitos, pero casi no se usa en el día a día. Lo que más ayuda es la ciudad, la provincia y un teléfono que conteste.',
		),
		'colombia' => array(
			'aduana' => 'La Dirección de Impuestos y Aduanas Nacionales (DIAN) controla lo que entra a Colombia, también los envíos entre particulares.',
			'doc'    => 'Cédula de ciudadanía (o cédula de extranjería si quien recibe es extranjero residente). Con entrega en agencia local, se retira presentando ese documento.',
			'dir'    => 'Colombia usa nomenclatura de calles y carreras: «Calle 45 # 12-34» significa calle 45, a la altura de la carrera 12, a 34 metros de la esquina. Añade barrio, torre y apartamento si los hay, ciudad y departamento.',
			'cp'     => 'El código postal colombiano tiene seis dígitos. Ayuda a ubicar la dirección, aunque lo imprescindible es la nomenclatura completa y el barrio.',
		),
		'chile' => array(
			'aduana' => 'El Servicio Nacional de Aduanas de Chile revisa los envíos que entran al país, incluidos los personales.',
			'doc'    => 'RUT, el número de la cédula de identidad chilena (formato 12.345.678-9). Aunque la entrega sea a domicilio, conviene tenerlo en la guía: es el dato con el que se identifica a quien recibe.',
			'dir'    => 'Calle y número, departamento o casa, y sobre todo la comuna: en Chile la comuna ubica una dirección mejor que la ciudad. Termina con la región.',
			'cp'     => 'El código postal chileno tiene siete dígitos. Es opcional para la entrega, pero evita confusiones entre calles con el mismo nombre en comunas distintas.',
		),
		'bolivia' => array(
			'aduana' => 'La Aduana Nacional de Bolivia controla lo que entra al país, también los envíos personales.',
			'doc'    => 'Cédula de identidad (CI), que en Bolivia lleva la sigla del departamento donde se emitió (por ejemplo, LP o SC). Con entrega en agencia local, quien retira presenta su CI.',
			'dir'    => 'Ciudad, zona o barrio, calle y número, y una referencia clara. En Bolivia la zona y la referencia ubican la dirección mejor que la numeración.',
			'cp'     => 'En Bolivia el código postal prácticamente no se usa. Lo que importa es la ciudad, la zona y un teléfono del destinatario.',
		),
		'argentina' => array(
			'aduana' => 'La Dirección General de Aduanas, que depende del organismo de recaudación nacional (ARCA, antes AFIP), revisa lo que entra a Argentina.',
			'doc'    => 'DNI. Para envíos del régimen de courier, la aduana suele pedir además el CUIL o CUIT de quien recibe: pídeselo a tu destinatario antes de despachar. Con entrega en agencia local, se retira con el DNI.',
			'dir'    => 'Calle y altura (el número de la puerta), piso y departamento, localidad y provincia. En Buenos Aires, indica también el barrio.',
			'cp'     => 'Argentina usa el código postal argentino (CPA), de ocho caracteres con letras y números (por ejemplo, C1425ABC). El código antiguo de cuatro números también se reconoce.',
		),
		'estados-unidos' => array(
			'aduana' => 'U.S. Customs and Border Protection (CBP) revisa todo lo que entra a Estados Unidos, incluidos los envíos de regalo.',
			'doc'    => 'Estados Unidos no tiene un documento nacional único. Lo importante es el nombre completo tal como figura en la identificación de quien recibe y un teléfono de contacto.',
			'dir'    => 'En orden estadounidense: número y calle («1250 Brickell Ave»), apartamento o unidad («Apt 4B»), ciudad, estado en dos letras (FL, NY, NJ…) y código postal.',
			'cp'     => 'El código postal (ZIP code) de cinco dígitos es imprescindible: sin él la dirección no se procesa bien. El formato extendido ZIP+4 es opcional.',
		),
		'espana' => array(
			'aduana' => 'Aduanas, dentro de la Agencia Tributaria española. Como el envío viene de fuera de la Unión Europea, pasa por aduana y puede pagar IVA de importación al entrar.',
			'doc'    => 'DNI, o NIE si quien recibe es extranjero residente en España. Es el dato que se usa para identificar al destinatario en el despacho de aduana.',
			'dir'    => 'Tipo de vía, nombre y número, piso y puerta («C/ Mayor 12, 3.º B»), localidad y provincia. El piso y la puerta son imprescindibles en edificios.',
			'cp'     => 'El código postal español tiene cinco dígitos y es obligatorio. Los dos primeros identifican la provincia (28 es Madrid, 08 Barcelona).',
		),
		'venezuela' => array(
			'aduana' => 'El Servicio Nacional Integrado de Administración Aduanera y Tributaria (SENIAT) controla lo que entra a Venezuela.',
			'doc'    => 'Cédula de identidad venezolana, con la letra V para venezolanos o E para extranjeros residentes. Es el dato que identifica a quien recibe.',
			'dir'    => 'Urbanización o sector, calle o avenida, nombre del edificio o de la quinta, piso y apartamento, municipio y estado. Un punto de referencia ayuda mucho al repartidor.',
			'cp'     => 'Venezuela tiene código postal de cuatro dígitos, pero se usa poco. Lo que ubica la dirección es la urbanización, el municipio y la referencia.',
		),
		'cuba' => array(
			'aduana' => 'La Aduana General de la República de Cuba revisa los envíos que entran al país y es especialmente estricta con lo que no está bien declarado.',
			'doc'    => 'Carné de identidad cubano (once dígitos). Con entrega en agencia local, quien retira presenta el carné a cuyo nombre va el envío.',
			'dir'    => 'Calle y número, las dos calles entre las que está («Calle 23 n.º 456 e/ J y K»), reparto, municipio y provincia. En Cuba el «entre calles» es parte esencial de la dirección.',
			'cp'     => 'Cuba tiene código postal de cinco dígitos, pero apenas se usa. Lo importante es el reparto, el municipio y las calles de referencia.',
		),
	);
}

/* Claves y rótulos de los campos editables. */
function grenvios_dp_campos( $slug, $pais ) {
	$d = grenvios_dp_datos()[ $slug ];
	return array(
		'dp_eyebrow'=> array( 'Antetítulo', 'text', 'Antes de rellenar la guía' ),
		'dp_title'  => array( 'Título', 'text', 'Datos prácticos para enviar a ' . $pais ),
		'dp_intro'  => array( 'Introducción', 'textarea', 'Lo que cambia de un país a otro no es solo el plazo: también quién revisa el paquete, qué documento usa tu destinatario y cómo se escribe su dirección. Con estos datos bien puestos en la guía, el envío no se detiene por un dato mal escrito.' ),
		'dp_k1'     => array( 'Tarjeta 1 · título', 'text', 'Quién revisa tu envío' ),
		'dp_aduana' => array( 'Tarjeta 1 · texto', 'textarea', $d['aduana'] ),
		'dp_k2'     => array( 'Tarjeta 2 · título', 'text', 'Documento de quien recibe' ),
		'dp_doc'    => array( 'Tarjeta 2 · texto', 'textarea', $d['doc'] ),
		'dp_k3'     => array( 'Tarjeta 3 · título', 'text', 'Cómo escribir la dirección' ),
		'dp_dir'    => array( 'Tarjeta 3 · texto', 'textarea', $d['dir'] ),
		'dp_k4'     => array( 'Tarjeta 4 · título', 'text', 'Código postal' ),
		'dp_cp'     => array( 'Tarjeta 4 · texto', 'textarea', $d['cp'] ),
		'dp_nota'   => array( 'Nota final', 'textarea', 'Los requisitos de aduana cambian con el tiempo: te confirmamos los vigentes para tu envío al cotizar.' ),
	);
}

/* ── Render: justo después de «Ciudades de X a las que llegamos» ───────── */
add_action( 'grenvios_destino_tras_proceso', function ( $slug, $d0 ) {
	$datos = grenvios_dp_datos();
	if ( ! isset( $datos[ $slug ] ) ) return;
	$pais = isset( $d0['title'] ) ? $d0['title'] : $slug;
	$c    = grenvios_dp_campos( $slug, $pais );
	$v    = function ( $k ) use ( $c ) { return grenvios_field( $k, $c[ $k ][2] ); };

	/* `gr-editable-propio`: ya se edita con sus campos dp_*; que el editor
	 * genérico de secciones (inc/destinos-textos-editables.php) no la duplique. */
	grenvios_dsec_open( 'gr-editable-propio', esc_html( $v( 'dp_eyebrow' ) ), esc_html( $v( 'dp_title' ) ), esc_html( $v( 'dp_intro' ) ) );
	echo '<div class="row gy-4">'
		. grenvios_dsec_card( 'fa-solid fa-building-shield', $v( 'dp_k1' ), esc_html( $v( 'dp_aduana' ) ) )
		. grenvios_dsec_card( 'fa-solid fa-id-card', $v( 'dp_k2' ), esc_html( $v( 'dp_doc' ) ) )
		. grenvios_dsec_card( 'fa-solid fa-map-location-dot', $v( 'dp_k3' ), esc_html( $v( 'dp_dir' ) ) )
		. grenvios_dsec_card( 'fa-solid fa-envelope-open-text', $v( 'dp_k4' ), esc_html( $v( 'dp_cp' ) ) )
		. '</div>';
	$nota = trim( (string) $v( 'dp_nota' ) );
	if ( $nota !== '' ) echo '<p class="dest-ciudades-pie">' . esc_html( $nota ) . '</p>';
	grenvios_dsec_close();
}, 10, 2 );

/* ── Panel «Editar página»: sección propia en la ficha (y en la portada de su ruta) ── */
add_action( 'grenvios_editor_secciones', function ( $slug, $post_id = 0, $render_field = null ) {
	if ( ! is_callable( $render_field ) ) return;
	$ps = function_exists( 'grenvios_editor_pais_slug' ) ? grenvios_editor_pais_slug( $slug ) : $slug;
	$datos = grenvios_dp_datos();
	if ( $ps === '' || ! isset( $datos[ $ps ] ) ) return;
	$dest = grenvios_destinos();
	$pais = isset( $dest[ $ps ]['title'] ) ? $dest[ $ps ]['title'] : $ps;
	echo '<div class="nep-accordion" data-sel=".dest-seo-sec"><button class="nep-acc-header" type="button"><span>'
		. esc_html( 'Destino · Datos prácticos para enviar a ' . $pais ) . '</span><i class="fa-solid fa-chevron-down"></i></button>'
		. '<div class="nep-acc-body"><div class="nep-grid">';
	foreach ( grenvios_dp_campos( $ps, $pais ) as $k => $f ) echo $render_field( $k, $f[0], $f[1], grenvios_field( $k, $f[2] ) );
	echo '</div></div></div>';
}, 8, 3 );

/* Tipos para el guardado REST (textarea conserva los saltos de línea). */
add_filter( 'grenvios_text_registry', function ( $reg ) {
	$reg['__destino_datos_practicos'] = array( 'label' => 'Datos prácticos (fichas de destino)', 'priority' => 999, 'sections' => array(
		'dp' => array( 'label' => 'Datos prácticos', 'fields' => grenvios_dp_campos( 'chile', 'Chile' ) ),
	) );
	return $reg;
} );
