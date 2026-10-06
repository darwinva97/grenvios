<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Fichas de destino: ciudades de cobertura y cotizador al cierre
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Comparando nuestras fichas con las de la competencia que mejor posiciona
 * («envíos a Estados Unidos»), lo nuestro ya cubre más temas y más a fondo
 * —precio, plazos, embalaje, errores, aduana, comparativa entre rutas—. Solo
 * faltaban dos cosas, y las dos son de las que mejor convierten:
 *
 *   1) LA LISTA DE CIUDADES DE DESTINO. Ellos la tienen («los 50 estados»,
 *      «Miami, Nueva York, Los Ángeles…») y nosotros no: el campo «Ciudades
 *      con cobertura» del gestor de destinos estaba vacío en los nueve países,
 *      así que la sección existía en el código y no se pintaba nunca. Es lo que
 *      captura la búsqueda larga —«enviar a Miami desde Lima»— y lo primero
 *      que mira quien va a enviar: si su ciudad aparece.
 *
 *   2) EL FORMULARIO DENTRO DE LA PÁGINA. Su ficha cierra con un cotizador;
 *      la nuestra cerraba con un botón que se lleva al visitante a otra URL.
 *      Aquí se pone el mismo cotizador de la portada, que ya sabe a qué país
 *      va (inc/paises-home-hero.php), justo antes del cierre.
 *
 * SOBRE LAS CIUDADES: las de aquí son solo el VALOR POR DEFECTO. Si la clienta
 * escribe su propia lista en el gestor de destinos, manda la suya. Y el texto
 * no promete cobertura exclusiva en ellas: dice que son las más solicitadas y
 * remite al resto del país, que es como funciona la operación.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Ciudades por defecto de cada destino: las más solicitadas de cada país.
 *
 * Filtro `grenvios_ciudades_destino`: para cambiarlas sin tocar el tema. */
function grenvios_ciudades_destino() {
	return apply_filters( 'grenvios_ciudades_destino', array(
		'ecuador'        => 'Quito, Guayaquil, Cuenca, Santo Domingo, Ambato, Manta, Machala, Portoviejo, Loja, Ibarra',
		'colombia'       => 'Bogotá, Medellín, Cali, Barranquilla, Cartagena, Bucaramanga, Pereira, Cúcuta, Santa Marta, Manizales',
		'chile'          => 'Santiago, Valparaíso, Viña del Mar, Concepción, Antofagasta, La Serena, Temuco, Iquique, Arica, Puerto Montt',
		'bolivia'        => 'La Paz, El Alto, Santa Cruz de la Sierra, Cochabamba, Sucre, Oruro, Tarija, Potosí',
		'argentina'      => 'Buenos Aires, Córdoba, Rosario, Mendoza, La Plata, Mar del Plata, Tucumán, Salta, Santa Fe, Neuquén',
		'estados-unidos' => 'Miami, Nueva York, Los Ángeles, Houston, Chicago, Washington D. C., Atlanta, Dallas, Orlando, Newark, Boston, San Francisco',
		'espana'         => 'Madrid, Barcelona, Valencia, Sevilla, Zaragoza, Málaga, Bilbao, Murcia, Alicante, Palma de Mallorca',
		'venezuela'      => 'Caracas, Maracaibo, Valencia, Barquisimeto, Maracay, Ciudad Guayana, Maturín, Puerto La Cruz, Mérida',
		'cuba'           => 'La Habana, Santiago de Cuba, Camagüey, Holguín, Santa Clara, Bayamo, Cienfuegos, Matanzas',
	) );
}

/* Rellena «ciudades» cuando el gestor de destinos lo tiene vacío. Así la
 * sección de cobertura del bloque por país —que ya existía— empieza a pintarse
 * en todas las páginas, no solo en la ficha. */
add_filter( 'grenvios_pais_datos', function ( $d ) {
	if ( ! is_array( $d ) || empty( $d['slug'] ) ) return $d;
	if ( ! empty( $d['ciudades'] ) ) return $d;          // la clienta escribió la suya
	$map = grenvios_ciudades_destino();
	if ( isset( $map[ $d['slug'] ] ) ) $d['ciudades'] = $map[ $d['slug'] ];
	return $d;
} );

/* ── Sección de cobertura en la ficha de destino ───────────────────────── */
add_action( 'grenvios_destino_tras_proceso', function ( $slug, $d0 ) {
	$map = grenvios_ciudades_destino();
	$txt = '';
	// Si el gestor tiene lista propia, esa manda.
	if ( function_exists( 'grenvios_pais_datos' ) ) {
		$d   = grenvios_pais_datos( $slug );
		$txt = isset( $d['ciudades'] ) ? (string) $d['ciudades'] : '';
	}
	if ( trim( $txt ) === '' && isset( $map[ $slug ] ) ) $txt = $map[ $slug ];
	if ( trim( $txt ) === '' ) return;

	$ciudades = array_values( array_filter( array_map( 'trim', explode( ',', $txt ) ) ) );
	if ( ! $ciudades ) return;

	$p       = isset( $d0['title'] ) ? $d0['title'] : $slug;
	$entrega = isset( $d0['entrega'] ) ? mb_strtolower( $d0['entrega'] ) : '';
	$casa    = ( strpos( $entrega, 'domicilio' ) !== false || strpos( $entrega, 'puerta' ) !== false );

	$intro = $casa
		? 'Entregamos en el domicilio del destinatario en todo el país. Estas son las ciudades de ' . $p . ' a las que más se envía desde {{origen_ciudad}}.'
		: 'El destinatario retira en la agencia local de su ciudad. Estas son las ciudades de ' . $p . ' a las que más se envía desde {{origen_ciudad}}.';

	/* Diseño v2 (skill grenvios-landing): foto del país con distintivo a la
	 * izquierda; cabecera, ciudades y nota a la derecha. */
	$img = trim( (string) grenvios_field( 'dst_ciudades_img', '' ) );
	if ( $img === '' && function_exists( 'grenvios_ej_img' ) ) $img = grenvios_ej_img( $slug );

	echo '<section class="srv-section dest-seo-sec dest-cob bg-grey padding"><div class="container">'
		. '<div class="dest-cob-grid' . ( $img === '' ? ' dest-cob-grid--sin-foto' : '' ) . '">';
	if ( $img !== '' ) {
		echo '<figure class="dest-cob-foto wow fade-in-left" data-wow-delay="150ms">'
			. '<img src="' . esc_url( $img ) . '" alt="" loading="lazy" decoding="async">'
			. '<div class="dest-cob-badge"><span class="dest-cob-badge-ic"><i class="fa-solid fa-location-dot"></i></span>'
			. '<p><strong>' . count( $ciudades ) . '</strong> ciudades principales</p></div></figure>';
	}
	echo '<div class="dest-cob-tx"><div class="srv-head"><h3 class="sub-heading">Cobertura</h3>'
		. '<h2>Ciudades de ' . esc_html( $p ) . ' a las que llegamos</h2>'
		. '<p class="srv-intro">' . wp_kses_post( $intro ) . '</p></div>';
	echo '<ul class="dest-ciudades">';
	foreach ( $ciudades as $c ) {
		echo '<li><i class="fa-solid fa-location-dot"></i>' . esc_html( $c ) . '</li>';
	}
	echo '</ul>';
	echo '<p class="dest-ciudades-pie">¿Tu destinatario vive en otra ciudad de ' . esc_html( $p ) . '? '
		. 'También llegamos: dinos la dirección exacta al cotizar y te confirmamos plazo y forma de entrega.</p>';
	echo '</div></div>';
	grenvios_dsec_close();
}, 10, 2 );

/* ── Cotizador al cierre de la ficha ───────────────────────────────────── */
add_action( 'grenvios_destino_antes_cta', function ( $slug, $d0 ) {
	if ( ! function_exists( 'grenvios_hq_form_html' ) ) return;
	$p = isset( $d0['title'] ) ? $d0['title'] : '';
	if ( function_exists( 'grenvios_rd_cotizador_propio' ) ) grenvios_rd_cotizador_propio( true );

	echo '<section class="srv-section dest-cotiza padding"><div class="container">';
	echo '<div class="row align-items-center">';
	echo '<div class="col-lg-5"><div class="dest-cotiza-texto">';
	echo '<h3 class="sub-heading">Cotiza sin compromiso</h3>';
	echo '<h2>Dinos qué envías a ' . esc_html( $p ) . '<br>y te damos el precio</h2>';
	echo '<p>Con el peso, las medidas y la ciudad de destino calculamos el costo por peso real o '
		. 'volumétrico —el mayor de los dos— y te respondemos por WhatsApp o correo. Sin compromiso '
		. 'y sin cargos que aparezcan después.</p>';
	echo '<ul class="dest-cotiza-list">'
		. '<li><i class="fa-solid fa-check"></i>Precio cerrado antes de despachar</li>'
		. '<li><i class="fa-solid fa-check"></i>Recojo a domicilio en {{origen_ciudad}}</li>'
		. '<li><i class="fa-solid fa-check"></i>Seguimiento hasta la entrega en ' . esc_html( $p ) . '</li>'
		. '</ul>';
	echo '</div></div>';
	echo '<div class="col-lg-7"><div class="dest-cotiza-form">' . grenvios_hq_form_html() . '</div></div>';
	echo '</div></div></section>';
}, 20, 2 );
