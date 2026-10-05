<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Cuatro páginas nuevas para búsquedas que el sitio no cubría
 * ══════════════════════════════════════════════════════════════════════════
 *
 *   /cuanto-cuesta-enviar-un-paquete-al-extranjero/  «cuánto cuesta enviar un
 *        paquete al extranjero»: la consulta con más intención de compra. Solo
 *        la respondía una pregunta frecuente de la portada.
 *   /servicios/encomiendas-internacionales/          «encomiendas internacionales»:
 *        así lo busca la mitad de la gente en Perú, Venezuela y Cuba. El sitio
 *        solo decía «paquete».
 *   /servicios/envio-de-medicinas-al-extranjero/                   «envío de medicinas al
 *        extranjero»: el gestor ya recoge qué rutas las admiten y no había
 *        página que lo contara.
 *   /embalaje-para-envios-internacionales/           «cómo embalar un paquete
 *        para envío internacional»: informativa, atrae y enlaza al cotizador.
 *
 * Mismo patrón que inc/paginas-seo-extra.php: fuera del menú, viven del
 * enlazado interno (grenvios_related_map), con textos editables desde el panel
 * y sin cifras que caduquen. Lo que depende de cada país (plazos, qué rutas
 * aceptan medicinas) sale del gestor de destinos.
 *
 * Solo en la ruta principal.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function grenvios_pn_slugs() {
	return array( 'cuanto-cuesta-enviar-un-paquete-al-extranjero', 'encomiendas-internacionales', 'envio-de-medicinas-al-extranjero', 'embalaje-para-envios-internacionales' );
}

/* ══════════════════════════════════════
   1) REGISTRO DE PÁGINAS
══════════════════════════════════════ */
add_filter( 'grenvios_pages', function ( $pages ) {
	$pages['cuanto-cuesta-enviar-un-paquete-al-extranjero'] = array(
		'title'  => 'Cuánto cuesta enviar un paquete al extranjero',
		'seo'    => 'Cuánto Cuesta Enviar un Paquete al Extranjero | Grenvíos',
		'desc'   => 'Cuánto cuesta enviar un paquete al extranjero desde Lima: qué fija el precio, ejemplos con peso real y volumétrico, y cómo pagar menos. Cotiza en minutos.',
		'parent' => '',
	);
	$pages['encomiendas-internacionales'] = array(
		'title'  => 'Encomiendas internacionales',
		'seo'    => 'Encomiendas Internacionales desde Lima | Grenvíos',
		'desc'   => 'Envía encomiendas internacionales desde Lima a tu familia: ropa, regalos y productos peruanos por vía aérea o terrestre, con seguimiento hasta la entrega.',
		'parent' => 'servicios',
	);
	$pages['envio-de-medicinas-al-extranjero'] = array(
		'title'  => 'Envío de medicinas al extranjero',
		'seo'    => 'Envío de Medicinas al Extranjero desde Perú | Grenvíos',
		'desc'   => 'Envío de medicinas al extranjero desde Lima: receta médica, envase original, cantidades de uso personal y qué destinos las admiten. Consulta antes de comprar.',
		'parent' => 'servicios',
	);
	$pages['embalaje-para-envios-internacionales'] = array(
		'title'  => 'Embalaje para envíos internacionales',
		'seo'    => 'Embalaje para Envíos Internacionales: Guía | Grenvíos',
		'desc'   => 'Embalaje para envíos internacionales: qué caja usar, cómo rellenar, sellar en H y proteger lo frágil. Evita daños y paga menos por peso volumétrico.',
		'parent' => '',
	);
	return $pages;
}, 20 );

/* Keyword objetivo de cada página (la que usa el enlazado y la auditoría). */
function grenvios_pn_kw() {
	return array(
		'cuanto-cuesta-enviar-un-paquete-al-extranjero' => 'cuánto cuesta enviar un paquete al extranjero',
		'encomiendas-internacionales'                   => 'encomiendas internacionales',
		'envio-de-medicinas-al-extranjero'                            => 'envío de medicinas al extranjero',
		'embalaje-para-envios-internacionales'          => 'embalaje para envíos internacionales',
	);
}
add_filter( 'grenvios_seo_kw_default', function ( $kw, $post_id ) {
	$slug = (string) get_post_field( 'post_name', $post_id );
	$m    = grenvios_pn_kw();
	return isset( $m[ $slug ] ) ? $m[ $slug ] : $kw;
}, 20, 2 );

/* Crea las páginas una sola vez, en el idioma principal, con su keyword. */
function grenvios_pn_crear() {
	if ( ! function_exists( 'grenvios_ensure_page' ) ) return;
	$serv = get_page_by_path( 'servicios' );
	$pages = grenvios_pages();
	foreach ( grenvios_pn_slugs() as $slug ) {
		if ( ! isset( $pages[ $slug ] ) ) continue;
		$parent = $pages[ $slug ]['parent'] === 'servicios' && $serv ? (int) $serv->ID : 0;
		$path   = $parent ? 'servicios/' . $slug : $slug;
		$ex     = get_page_by_path( $path );
		$id     = $ex ? (int) $ex->ID : (int) grenvios_ensure_page( $slug, $pages[ $slug ]['title'], $parent );
		if ( ! $id ) continue;
		if ( function_exists( 'pll_set_post_language' ) && function_exists( 'pll_default_language' ) && ! pll_get_post_language( $id ) ) {
			pll_set_post_language( $id, pll_default_language() );
		}
		$kw = grenvios_pn_kw();
		if ( defined( 'GRENVIOS_KW_META' ) && get_post_meta( $id, GRENVIOS_KW_META, true ) === '' ) {
			update_post_meta( $id, GRENVIOS_KW_META, $kw[ $slug ] );
		}
	}
	update_option( 'grenvios_pn_v', 1 );
	delete_transient( 'grenvios_links_graph' );
	flush_rewrite_rules();
}
add_action( 'admin_init', function () {
	if ( (int) get_option( 'grenvios_pn_v' ) < 1 ) grenvios_pn_crear();
} );

/* ══════════════════════════════════════
   2) RENDER
══════════════════════════════════════ */
add_filter( 'grenvios_render_page_early', function ( $hecho, $slug ) {
	if ( $hecho || ! in_array( $slug, grenvios_pn_slugs(), true ) ) return $hecho;
	switch ( $slug ) {
		case 'cuanto-cuesta-enviar-un-paquete-al-extranjero': grenvios_pn_render_precio();     return true;
		case 'encomiendas-internacionales':                   grenvios_pn_render_encomiendas(); return true;
		case 'envio-de-medicinas-al-extranjero':                            grenvios_pn_render_medicinas();   return true;
		case 'embalaje-para-envios-internacionales':          grenvios_pn_render_embalaje();    return true;
	}
	return $hecho;
}, 12, 2 );

/* Bloques comunes: cabecera de sección y panel. */
function grenvios_pn_intro( $k_tit, $tit, $k_txt, $txt ) {
	echo '<section class="srv-intro padding-top"><div class="container"><div class="srv-lead text-center">'
		. '<h2>' . esc_html( grenvios_tf( $k_tit, $tit ) ) . '</h2>'
		. '<p>' . wp_kses_post( grenvios_tf( $k_txt, $txt ) ) . '</p>'
		. '</div></div></section>';
}
function grenvios_pn_sec_open( $clase, $k_tit, $tit, $k_txt = '', $txt = '' ) {
	echo '<section class="srv-section ' . esc_attr( $clase ) . ' padding"><div class="container"><div class="srv-head text-center">'
		. '<h2>' . esc_html( grenvios_tf( $k_tit, $tit ) ) . '</h2>'
		. ( $k_txt !== '' ? '<p>' . wp_kses_post( grenvios_tf( $k_txt, $txt ) ) . '</p>' : '' )
		. '</div>';
}
function grenvios_pn_sec_close() { echo '</div></section>'; }
function grenvios_pn_panel( $k_tit, $tit, $k_txt, $txt ) {
	echo '<div class="srv-panel"><h3 class="srv-panel-title">' . esc_html( grenvios_tf( $k_tit, $tit ) ) . '</h3>'
		. '<p>' . wp_kses_post( grenvios_tf( $k_txt, $txt ) ) . '</p></div>';
}
function grenvios_pn_nota( $k, $t ) {
	echo '<p class="gr-nota">' . wp_kses_post( grenvios_tf( $k, $t ) ) . '</p>';
}
function grenvios_pn_url( $path ) {
	return esc_url( ( function_exists( 'grenvios_url_base' ) ? grenvios_url_base() : home_url() ) . $path );
}

/* ── 2a) Cuánto cuesta enviar un paquete al extranjero ─────────────────── */
function grenvios_pn_render_precio() {
	grenvios_tool_banner( 'Precios de envío', 'Cuánto cuesta enviar un paquete al extranjero' );
	grenvios_pn_intro( 'pc_h2', 'El precio lo fijan cuatro datos, y los cuatro se saben antes de despachar',
		'pc_intro', 'Saber cuánto cuesta enviar un paquete al extranjero no depende de una tarifa por país, sino de cuatro datos de tu envío: el peso que se cobra, la vía, el destino y el valor de lo que va dentro. Con ellos te damos una cifra cerrada en minutos, y cambiar cualquiera de los cuatro cambia el precio.' );

	grenvios_pn_sec_open( '', 'pc_datos_title', 'Qué fija el precio de tu envío' );
	echo '<div class="srv-two-grid">';
	grenvios_pn_panel( 'pc_d1_t', 'El peso que se cobra', 'pc_d1_x', 'Es el mayor entre el peso real y el peso volumétrico (largo × ancho × alto en cm ÷ 5000). Una caja grande y liviana se cobra por su tamaño, no por lo que marca la balanza.' );
	grenvios_pn_panel( 'pc_d2_t', 'La vía', 'pc_d2_x', 'La vía aérea se paga por rapidez y la terrestre por volumen. Hacia países vecinos, un bulto grande que no corre prisa cuesta bastante menos por tierra.' );
	grenvios_pn_panel( 'pc_d3_t', 'El destino', 'pc_d3_x', 'Cambia el plazo, la forma de entrega y los impuestos de su aduana. No cuesta lo mismo una entrega a domicilio en Santiago que un retiro en agencia en Quito.' );
	grenvios_pn_panel( 'pc_d4_t', 'El valor declarado', 'pc_d4_x', 'Es la base del impuesto en destino y de la cobertura del seguro. Declarar de menos no abarata el envío: lo deja asegurado por esa cifra menor.' );
	echo '</div>';
	grenvios_pn_sec_close();

	/* Ejemplos calculados con el mismo divisor que la calculadora del sitio. */
	$div = function_exists( 'grenvios_peso_divisor' ) ? (int) grenvios_peso_divisor() : 5000;
	if ( $div <= 0 ) $div = 5000;
	$cajas = array(
		array( 'Sobre con documentos', 30, 22, 2, 0.4 ),
		array( 'Caja de zapatos',      35, 25, 15, 1.5 ),
		array( 'Caja mediana de ropa', 40, 30, 30, 6 ),
		array( 'Caja grande de mudanza', 60, 40, 40, 10 ),
	);
	$fmt  = function ( $n ) { return rtrim( rtrim( number_format( $n, 1, ',', '.' ), '0' ), ',' ); };
	$rows = '';
	foreach ( $cajas as $c ) {
		$vol = $c[1] * $c[2] * $c[3] / $div;
		$cob = max( $vol, $c[4] );
		$rows .= '<tr><th scope="row">' . esc_html( $c[0] ) . '</th><td>' . $c[1] . ' × ' . $c[2] . ' × ' . $c[3] . ' cm</td>'
			. '<td>' . $fmt( $c[4] ) . ' kg</td><td>' . $fmt( $vol ) . ' kg</td><td><strong>' . $fmt( $cob ) . ' kg</strong></td></tr>';
	}
	grenvios_pn_sec_open( 'bg-grey', 'pc_ej_title', 'Cuánto se cobra por cuatro envíos frecuentes',
		'pc_ej_text', 'El precio final se calcula sobre la última columna. Fíjate en la caja de mudanza: pesa 10 kg, pero por su tamaño se cobra como si pesara el doble.' );
	echo '<div class="gr-table-wrap"><table class="gr-table"><thead><tr><th scope="col">Envío</th><th scope="col">Medidas</th><th scope="col">Peso real</th><th scope="col">Peso volumétrico</th><th scope="col">Peso que se cobra</th></tr></thead><tbody>'
		. $rows . '</tbody></table></div>';
	grenvios_pn_nota( 'pc_ej_nota', 'Calcula el de tu caja con la <a href="' . grenvios_pn_url( '/servicios/peso-volumetrico/' ) . '">calculadora de peso volumétrico</a>.' );
	grenvios_pn_sec_close();

	grenvios_pn_sec_open( '', 'pc_menos_title', 'Cómo pagar menos por el mismo envío' );
	grenvios_seo_extra_lista( array(
		'Usa una caja ajustada al contenido: cada centímetro de aire se cobra.',
		'Junta varios envíos en un solo bulto: pagas un despacho en vez de varios.',
		'Si el destino es un país vecino y no hay prisa, pregunta por la vía terrestre.',
		'Quita el embalaje de tienda y dobla la ropa en lugar de enrollarla con relleno.',
		'Declara el valor real: un envío retenido por valor dudoso cuesta más que el impuesto.',
	), 'pc_lista1' );
	grenvios_pn_nota( 'pc_nota_tarifa', 'No publicamos una tarifa fija por país porque el mismo destino cuesta distinto según el peso cobrado y la vía. Escríbenos con el destino, el peso y las medidas y te damos el precio cerrado, puesto en destino: <a href="' . grenvios_pn_url( '/cotizar/' ) . '">cotiza tu envío</a>.' );
	grenvios_pn_sec_close();
}

/* ── 2b) Encomiendas internacionales ───────────────────────────────────── */
function grenvios_pn_render_encomiendas() {
	grenvios_tool_banner( 'Para la familia', 'Encomiendas internacionales desde {{origen_ciudad}}' );
	grenvios_pn_intro( 'en_h2', 'Una encomienda es un envío personal, y la aduana la trata como tal',
		'en_intro', 'Las encomiendas internacionales son los envíos que se mandan a un familiar o a un amigo: ropa, regalos, productos peruanos que allá no se consiguen, documentos personales. No son mercancía para vender, y eso es lo que las distingue ante la aduana del país de destino: pagan menos trámites que una carga comercial, siempre que el contenido lo parezca.' );

	grenvios_pn_sec_open( '', 'en_que_title', 'Qué suele ir en una encomienda' );
	echo '<div class="srv-two-grid">';
	echo '<div class="srv-panel"><h3 class="srv-panel-title">' . esc_html( grenvios_tf( 'en_si_title', 'Lo más habitual' ) ) . '</h3>';
	grenvios_seo_extra_lista( array(
		'Ropa y calzado, nuevos o usados.',
		'Regalos: juguetes, libros, artesanía, adornos.',
		'Productos peruanos envasados de fábrica: café, chocolates, galletas.',
		'Documentos personales y fotografías.',
		'Medicinas con receta, hacia los destinos que las admiten.',
	), 'en_lista1' );
	echo '</div><div class="srv-panel"><h3 class="srv-panel-title">' . esc_html( grenvios_tf( 'en_no_title', 'Lo que conviene consultar antes' ) ) . '</h3>';
	grenvios_seo_extra_lista( array(
		'Productos con batería, perfumes y líquidos, si el envío va por avión.',
		'Alimentos caseros o sin etiqueta de fabricante.',
		'Muchas unidades iguales del mismo artículo: se leen como comerciales.',
		'Electrónica nueva de valor alto, por los impuestos del destino.',
	), 'en_lista2' );
	echo '</div></div>';
	grenvios_pn_sec_close();

	/* Destinos con plazo y forma de entrega, desde el gestor. */
	$filas = '';
	if ( function_exists( 'grenvios_destinos' ) && function_exists( 'grenvios_pais_datos' ) ) {
		foreach ( grenvios_destinos() as $slug => $x ) {
			$d = grenvios_pais_datos( $slug );
			if ( ! $d ) continue;
			$filas .= '<tr><th scope="row"><a href="' . grenvios_pn_url( '/destinos/' . $slug . '/' ) . '">' . esc_html( $d['title'] ) . '</a></th>'
				. '<td>' . esc_html( $d['modos'] ) . '</td><td>' . esc_html( $d['tiempo'] ) . '</td>'
				. '<td>' . ( ! empty( $d['casa'] ) ? 'A domicilio' : 'Retiro en agencia local' ) . '</td></tr>';
		}
	}
	if ( $filas !== '' ) {
		grenvios_pn_sec_open( 'bg-grey', 'en_dest_title', 'A dónde enviamos encomiendas',
			'en_dest_text', 'Plazos en días hábiles desde el despacho. Donde la entrega es en agencia, quien recibe retira la encomienda con el documento a cuyo nombre va.' );
		echo '<div class="gr-table-wrap"><table class="gr-table"><thead><tr><th scope="col">Destino</th><th scope="col">Vía</th><th scope="col">Plazo</th><th scope="col">Entrega</th></tr></thead><tbody>' . $filas . '</tbody></table></div>';
		grenvios_pn_sec_close();
	}

	grenvios_pn_sec_open( '', 'en_dif_title', 'Encomienda o carga: dónde está la diferencia' );
	echo '<div class="srv-two-grid">';
	grenvios_pn_panel( 'en_d1_t', 'Encomienda', 'en_d1_x', 'Artículos variados, en cantidades de uso personal, para una persona concreta. Se declara el contenido y su valor, y en las rutas terrestres el impuesto se paga en {{origen_ciudad}} al despachar.' );
	grenvios_pn_panel( 'en_d2_t', 'Carga', 'en_d2_x', 'Muchas unidades del mismo producto, mercancía para vender o envíos de una empresa. Lleva factura comercial y, según el destino, trámites de importación. Si es tu caso, mira <a href="' . grenvios_pn_url( '/servicios/carga-internacional/' ) . '">carga internacional</a>.' );
	echo '</div>';
	grenvios_pn_nota( 'en_nota', 'Si no sabes en cuál de los dos casos está tu envío, dinos qué va dentro al <a href="' . grenvios_pn_url( '/cotizar/' ) . '">cotizar</a> y te lo decimos antes de preparar la caja.' );
	grenvios_pn_sec_close();
}

/* ── 2c) Envío de medicinas al extranjero ──────────────────────────────── */
function grenvios_pn_render_medicinas() {
	grenvios_tool_banner( 'Con receta y en su envase', 'Envío de medicinas al extranjero desde {{origen_pais}}' );
	grenvios_pn_intro( 'me_h2', 'Se puede enviar medicinas, con tres condiciones que no se negocian',
		'me_intro', 'El envío de medicinas al extranjero es de los más consultados y de los más delicados, porque no depende solo de nosotros: cada país decide qué medicamentos deja entrar y con qué papeles. Lo que sí es igual en todos es el punto de partida: receta médica, envase original y cantidades de uso personal.' );

	grenvios_pn_sec_open( '', 'me_req_title', 'Lo que necesitas para enviarlas' );
	grenvios_seo_extra_lista( array(
		'Receta médica vigente, a nombre de la persona que va a usar el medicamento.',
		'El medicamento en su envase original, con el prospecto y la etiqueta a la vista.',
		'Cantidades que correspondan a un tratamiento personal, no a reventa.',
		'La boleta de compra, para declarar el valor.',
		'Los datos completos del destinatario, igual que en su documento de identidad.',
	), 'me_lista1' );
	grenvios_pn_sec_close();

	/* Rutas que las admiten, según las notas del gestor. */
	$si = array(); $resto = array();
	if ( function_exists( 'grenvios_destinos' ) && function_exists( 'grenvios_pais_datos' ) ) {
		foreach ( grenvios_destinos() as $slug => $x ) {
			$d = grenvios_pais_datos( $slug );
			if ( ! $d ) continue;
			if ( stripos( remove_accents( (string) $d['restr'] ), 'medicin' ) !== false ) $si[ $slug ] = $d;
			else $resto[] = $d['title'];
		}
	}
	if ( $si ) {
		grenvios_pn_sec_open( 'bg-grey', 'me_dest_title', 'Destinos donde las aceptamos', 'me_dest_text', 'Estas son las rutas en las que hoy recibimos medicinas y sus condiciones propias:' );
		echo '<div class="srv-two-grid">';
		foreach ( $si as $slug => $d ) {
			echo '<div class="srv-panel"><h3 class="srv-panel-title"><a href="' . grenvios_pn_url( '/destinos/' . $slug . '/' ) . '">' . esc_html( $d['title'] ) . '</a></h3>'
				. '<p>' . esc_html( $d['restr'] ) . '</p><p><strong>Plazo:</strong> ' . esc_html( $d['tiempo'] ) . ' · <strong>Vía:</strong> ' . esc_html( $d['modos'] ) . '</p></div>';
		}
		echo '</div>';
		if ( $resto ) {
			$ult = array_pop( $resto );
			grenvios_pn_nota( 'me_dest_nota', 'Para ' . esc_html( ( $resto ? implode( ', ', $resto ) . ' y ' : '' ) . $ult ) . ', escríbenos con el nombre del medicamento antes de comprarlo: te decimos si entra y qué pide su aduana.' );
		}
		grenvios_pn_sec_close();
	}

	grenvios_pn_sec_open( '', 'me_emb_title', 'Cómo embalar y declarar las medicinas',
		'me_emb_text', 'Una caja con medicinas pasa más controles que una de ropa. Estas cinco pautas hacen que la inspección sea rápida y que el contenido llegue entero.' );
	echo '<ol class="gr-pseo-steps">';
	foreach ( array(
		array( 'me_e1', 'Todo en su caja original.', 'Blísters dentro de su estuche, frascos con su tapa de fábrica y el prospecto dentro. Un medicamento suelto no se puede identificar y suele retenerse.' ),
		array( 'me_e2', 'La receta, visible.', 'Una copia en un sobre pegado a la tapa de la caja, por dentro, y otra que nos entregas al despachar.' ),
		array( 'me_e3', 'En bolsa sellada aparte.', 'Separadas de alimentos, cosméticos y ropa, para que la inspección no obligue a abrir todo el paquete.' ),
		array( 'me_e4', 'Protección contra golpes y calor.', 'Relleno alrededor de los frascos de vidrio y la caja lejos de las paredes. Durante el transporte no hay control de temperatura.' ),
		array( 'me_e5', 'Una línea por medicamento.', 'En la declaración: nombre comercial, principio activo, cantidad de envases y valor según la boleta.' ),
	) as $e ) {
		echo '<li><strong>' . esc_html( grenvios_tf( $e[0] . '_t', $e[1] ) ) . '</strong> ' . esc_html( grenvios_tf( $e[0] . '_x', $e[2] ) ) . '</li>';
	}
	echo '</ol>';
	grenvios_pn_sec_close();

	grenvios_pn_sec_open( '', 'me_no_title', 'Lo que no enviamos' );
	grenvios_seo_extra_lista( array(
		'Medicamentos controlados: psicotrópicos, estupefacientes y los que exigen receta especial.',
		'Medicamentos que necesitan refrigeración: no viajan con cadena de frío.',
		'Productos sin registro sanitario, sueltos o fuera de su envase.',
		'Cantidades que excedan un tratamiento personal.',
	), 'me_lista2' );
	grenvios_pn_nota( 'me_nota', 'Las medicinas van en bolsa sellada dentro de la caja, separadas del resto, y se declaran una por una con su nombre, cantidad y valor. Si también envías ropa o alimentos, puedes mandarlo todo en la misma <a href="' . grenvios_pn_url( '/servicios/encomiendas-internacionales/' ) . '">encomienda</a>.' );
	grenvios_pn_sec_close();
}

/* ── 2d) Embalaje para envíos internacionales ──────────────────────────── */
function grenvios_pn_render_embalaje() {
	grenvios_tool_banner( 'Guía práctica', 'Embalaje para envíos internacionales' );
	grenvios_pn_intro( 'em_h2', 'Un buen embalaje protege el contenido y además te hace pagar menos',
		'em_intro', 'El embalaje para envíos internacionales tiene que aguantar varios tramos, cambios de temperatura y bultos apilados encima. Y como el precio se calcula sobre el tamaño de la caja, embalar bien también es la forma más directa de bajar el peso que se cobra.' );

	grenvios_pn_sec_open( '', 'em_pasos_title', 'Cuatro pasos para embalar bien' );
	echo '<ol class="gr-pseo-steps">';
	$pasos = array(
		array( 'em_p1', 'La caja.', 'Nueva, de cartón corrugado de doble pared y lo más ajustada posible al contenido. Las cajas reutilizadas pierden resistencia y las etiquetas antiguas confunden al escanear.' ),
		array( 'em_p2', 'El relleno.', 'Nada debe moverse dentro. Papel arrugado, plástico de burbujas o la propia ropa entre los objetos. Deja unos 5 cm de relleno entre el contenido y las paredes.' ),
		array( 'em_p3', 'El cierre.', 'Cinta de embalaje ancha en forma de H: a lo largo de la unión central y en los dos bordes, arriba y abajo. La cinta adhesiva de oficina o la de papel no sirven.' ),
		array( 'em_p4', 'El rótulo.', 'Una sola etiqueta por fuera con los datos del destinatario, y una copia dentro de la caja. Tapa o retira cualquier etiqueta anterior.' ),
	);
	foreach ( $pasos as $p ) {
		echo '<li><strong>' . esc_html( grenvios_tf( $p[0] . '_t', $p[1] ) ) . '</strong> ' . esc_html( grenvios_tf( $p[0] . '_x', $p[2] ) ) . '</li>';
	}
	echo '</ol>';
	grenvios_pn_sec_close();

	grenvios_pn_sec_open( 'bg-grey', 'em_casos_title', 'Casos que necesitan un cuidado extra' );
	echo '<div class="srv-two-grid">';
	grenvios_pn_panel( 'em_c1_t', 'Objetos frágiles', 'em_c1_x', 'Cada pieza envuelta por separado y, si es muy delicada, en una caja dentro de otra caja con relleno entre las dos. Marca la caja como frágil en al menos dos caras.' );
	grenvios_pn_panel( 'em_c2_t', 'Líquidos y cremas', 'em_c2_x', 'Solo si la vía y el destino los admiten. Tapa asegurada con cinta, dentro de una bolsa sellada, y separados de la ropa y los papeles.' );
	grenvios_pn_panel( 'em_c3_t', 'Ropa', 'em_c3_x', 'Doblada, no enrollada con relleno. Una bolsa al vacío reduce mucho el volumen, y por tanto el peso volumétrico, en envíos grandes de ropa.' );
	grenvios_pn_panel( 'em_c4_t', 'Electrónica', 'em_c4_x', 'En su caja original si la tienes, con relleno alrededor. Los aparatos con batería tienen reglas propias por vía aérea: consulta antes de embalar.' );
	echo '</div>';
	grenvios_pn_sec_close();

	grenvios_pn_sec_open( '', 'em_err_title', 'Errores de embalaje que salen caros' );
	grenvios_seo_extra_lista( array(
		'Usar una caja mucho más grande de lo necesario: se cobra el espacio vacío.',
		'Dejar huecos: lo que se mueve dentro es lo que se rompe.',
		'Cerrar con cinta solo la unión central: las esquinas ceden con el peso.',
		'Cerrar la caja antes de revisar el contenido con nosotros.',
		'Poner los datos del destinatario solo por fuera: si la etiqueta se pierde, no hay forma de identificar el bulto.',
	), 'em_lista1' );
	grenvios_pn_nota( 'em_nota', 'Si no tienes caja o no sabes cuál usar, la ponemos nosotros ajustada a lo que envías, en la oficina o en el <a href="' . grenvios_pn_url( '/recojo-a-domicilio-lima/' ) . '">recojo a domicilio</a>. Antes de medirla, mira cómo influye en el precio en <a href="' . grenvios_pn_url( '/cuanto-cuesta-enviar-un-paquete-al-extranjero/' ) . '">cuánto cuesta enviar un paquete al extranjero</a>.' );
	grenvios_pn_sec_close();
}

/* ══════════════════════════════════════
   2b) EDITABLES DESDE EL PANEL «Editar página»
   Todos los textos que pintan las funciones de arriba con grenvios_tf() /
   grenvios_seo_extra_lista(), agrupados por sección en el orden de la
   página. Los valores por defecto son los mismos del render. Sin este
   registro el panel solo ofrecía el banner y las secciones SEO añadidas.
══════════════════════════════════════ */
add_filter( 'grenvios_text_registry', function ( $reg ) {
	$pn = array(
		'cuanto-cuesta-enviar-un-paquete-al-extranjero' => array(
			'hero' => array( 'label' => 'Cuánto cuesta · Banner', 'fields' => array(
				'tool_eyebrow' => array( 'Frase superior', 'text', 'Precios de envío' ),
				'tool_title' => array( 'Título H1', 'text', 'Cuánto cuesta enviar un paquete al extranjero' ),
			) ),
			's1' => array( 'label' => 'Cuánto cuesta · El precio lo fijan cuatro datos, y los cuatro se saben antes de despachar', 'fields' => array(
				'pc_h2' => array( 'Título', 'text', 'El precio lo fijan cuatro datos, y los cuatro se saben antes de despachar' ),
				'pc_intro' => array( 'Texto', 'html', 'Saber cuánto cuesta enviar un paquete al extranjero no depende de una tarifa por país, sino de cuatro datos de tu envío: el peso que se cobra, la vía, el destino y el valor de lo que va dentro. Con ellos te damos una cifra cerrada en minutos, y cambiar cualquiera de los cuatro cambia el precio.' ),
			) ),
			's2' => array( 'label' => 'Cuánto cuesta · Qué fija el precio de tu envío', 'fields' => array(
				'pc_datos_title' => array( 'Título', 'text', 'Qué fija el precio de tu envío' ),
				'pc_d1_t' => array( '«El peso que se cobra» · título', 'text', 'El peso que se cobra' ),
				'pc_d1_x' => array( '«El peso que se cobra» · texto', 'html', 'Es el mayor entre el peso real y el peso volumétrico (largo × ancho × alto en cm ÷ 5000). Una caja grande y liviana se cobra por su tamaño, no por lo que marca la balanza.' ),
				'pc_d2_t' => array( '«La vía» · título', 'text', 'La vía' ),
				'pc_d2_x' => array( '«La vía» · texto', 'html', 'La vía aérea se paga por rapidez y la terrestre por volumen. Hacia países vecinos, un bulto grande que no corre prisa cuesta bastante menos por tierra.' ),
				'pc_d3_t' => array( '«El destino» · título', 'text', 'El destino' ),
				'pc_d3_x' => array( '«El destino» · texto', 'html', 'Cambia el plazo, la forma de entrega y los impuestos de su aduana. No cuesta lo mismo una entrega a domicilio en Santiago que un retiro en agencia en Quito.' ),
				'pc_d4_t' => array( '«El valor declarado» · título', 'text', 'El valor declarado' ),
				'pc_d4_x' => array( '«El valor declarado» · texto', 'html', 'Es la base del impuesto en destino y de la cobertura del seguro. Declarar de menos no abarata el envío: lo deja asegurado por esa cifra menor.' ),
			) ),
			's3' => array( 'label' => 'Cuánto cuesta · Cuánto se cobra por cuatro envíos frecuentes', 'fields' => array(
				'pc_ej_title' => array( 'Título', 'text', 'Cuánto se cobra por cuatro envíos frecuentes' ),
				'pc_ej_text' => array( 'Texto', 'html', 'El precio final se calcula sobre la última columna. Fíjate en la caja de mudanza: pesa 10 kg, pero por su tamaño se cobra como si pesara el doble.' ),
				'pc_ej_nota' => array( 'Nota', 'html', 'Calcula el de tu caja con la <a href="' . grenvios_pn_url( '/servicios/peso-volumetrico/' ) . '">calculadora de peso volumétrico</a>.' ),
			) ),
			's4' => array( 'label' => 'Cuánto cuesta · Cómo pagar menos por el mismo envío', 'fields' => array(
				'pc_menos_title' => array( 'Título', 'text', 'Cómo pagar menos por el mismo envío' ),
				'pc_lista1' => array( 'Lista (una línea por punto)', 'textarea', 'Usa una caja ajustada al contenido: cada centímetro de aire se cobra.
Junta varios envíos en un solo bulto: pagas un despacho en vez de varios.
Si el destino es un país vecino y no hay prisa, pregunta por la vía terrestre.
Quita el embalaje de tienda y dobla la ropa en lugar de enrollarla con relleno.
Declara el valor real: un envío retenido por valor dudoso cuesta más que el impuesto.' ),
				'pc_nota_tarifa' => array( 'Nota', 'html', 'No publicamos una tarifa fija por país porque el mismo destino cuesta distinto según el peso cobrado y la vía. Escríbenos con el destino, el peso y las medidas y te damos el precio cerrado, puesto en destino: <a href="' . grenvios_pn_url( '/cotizar/' ) . '">cotiza tu envío</a>.' ),
			) ),
		),
		'encomiendas-internacionales' => array(
			'hero' => array( 'label' => 'Encomiendas · Banner', 'fields' => array(
				'tool_eyebrow' => array( 'Frase superior', 'text', 'Para la familia' ),
				'tool_title' => array( 'Título H1', 'text', 'Encomiendas internacionales desde {{origen_ciudad}}' ),
			) ),
			's1' => array( 'label' => 'Encomiendas · Una encomienda es un envío personal, y la aduana la trata como tal', 'fields' => array(
				'en_h2' => array( 'Título', 'text', 'Una encomienda es un envío personal, y la aduana la trata como tal' ),
				'en_intro' => array( 'Texto', 'html', 'Las encomiendas internacionales son los envíos que se mandan a un familiar o a un amigo: ropa, regalos, productos peruanos que allá no se consiguen, documentos personales. No son mercancía para vender, y eso es lo que las distingue ante la aduana del país de destino: pagan menos trámites que una carga comercial, siempre que el contenido lo parezca.' ),
			) ),
			's2' => array( 'label' => 'Encomiendas · Qué suele ir en una encomienda', 'fields' => array(
				'en_que_title' => array( 'Título', 'text', 'Qué suele ir en una encomienda' ),
				'en_si_title' => array( 'Subtítulo', 'text', 'Lo más habitual' ),
				'en_lista1' => array( 'Lista (una línea por punto)', 'textarea', 'Ropa y calzado, nuevos o usados.
Regalos: juguetes, libros, artesanía, adornos.
Productos peruanos envasados de fábrica: café, chocolates, galletas.
Documentos personales y fotografías.
Medicinas con receta, hacia los destinos que las admiten.' ),
				'en_no_title' => array( 'Subtítulo', 'text', 'Lo que conviene consultar antes' ),
				'en_lista2' => array( 'Lista (una línea por punto)', 'textarea', 'Productos con batería, perfumes y líquidos, si el envío va por avión.
Alimentos caseros o sin etiqueta de fabricante.
Muchas unidades iguales del mismo artículo: se leen como comerciales.
Electrónica nueva de valor alto, por los impuestos del destino.' ),
			) ),
			's3' => array( 'label' => 'Encomiendas · A dónde enviamos encomiendas', 'fields' => array(
				'en_dest_title' => array( 'Título', 'text', 'A dónde enviamos encomiendas' ),
				'en_dest_text' => array( 'Texto', 'html', 'Plazos en días hábiles desde el despacho. Donde la entrega es en agencia, quien recibe retira la encomienda con el documento a cuyo nombre va.' ),
			) ),
			's4' => array( 'label' => 'Encomiendas · Encomienda o carga: dónde está la diferencia', 'fields' => array(
				'en_dif_title' => array( 'Título', 'text', 'Encomienda o carga: dónde está la diferencia' ),
				'en_d1_t' => array( '«Encomienda» · título', 'text', 'Encomienda' ),
				'en_d1_x' => array( '«Encomienda» · texto', 'html', 'Artículos variados, en cantidades de uso personal, para una persona concreta. Se declara el contenido y su valor, y en las rutas terrestres el impuesto se paga en {{origen_ciudad}} al despachar.' ),
				'en_d2_t' => array( '«Carga» · título', 'text', 'Carga' ),
				'en_d2_x' => array( '«Carga» · texto', 'html', 'Muchas unidades del mismo producto, mercancía para vender o envíos de una empresa. Lleva factura comercial y, según el destino, trámites de importación. Si es tu caso, mira <a href="' . grenvios_pn_url( '/servicios/carga-internacional/' ) . '">carga internacional</a>.' ),
				'en_nota' => array( 'Nota', 'html', 'Si no sabes en cuál de los dos casos está tu envío, dinos qué va dentro al <a href="' . grenvios_pn_url( '/cotizar/' ) . '">cotizar</a> y te lo decimos antes de preparar la caja.' ),
			) ),
		),
		'envio-de-medicinas-al-extranjero' => array(
			'hero' => array( 'label' => 'Medicinas · Banner', 'fields' => array(
				'tool_eyebrow' => array( 'Frase superior', 'text', 'Con receta y en su envase' ),
				'tool_title' => array( 'Título H1', 'text', 'Envío de medicinas al extranjero desde {{origen_pais}}' ),
			) ),
			's1' => array( 'label' => 'Medicinas · Se puede enviar medicinas, con tres condiciones que no se negocian', 'fields' => array(
				'me_h2' => array( 'Título', 'text', 'Se puede enviar medicinas, con tres condiciones que no se negocian' ),
				'me_intro' => array( 'Texto', 'html', 'El envío de medicinas al extranjero es de los más consultados y de los más delicados, porque no depende solo de nosotros: cada país decide qué medicamentos deja entrar y con qué papeles. Lo que sí es igual en todos es el punto de partida: receta médica, envase original y cantidades de uso personal.' ),
			) ),
			's2' => array( 'label' => 'Medicinas · Lo que necesitas para enviarlas', 'fields' => array(
				'me_req_title' => array( 'Título', 'text', 'Lo que necesitas para enviarlas' ),
				'me_lista1' => array( 'Lista (una línea por punto)', 'textarea', 'Receta médica vigente, a nombre de la persona que va a usar el medicamento.
El medicamento en su envase original, con el prospecto y la etiqueta a la vista.
Cantidades que correspondan a un tratamiento personal, no a reventa.
La boleta de compra, para declarar el valor.
Los datos completos del destinatario, igual que en su documento de identidad.' ),
			) ),
			's3' => array( 'label' => 'Medicinas · Destinos donde las aceptamos', 'fields' => array(
				'me_dest_title' => array( 'Título', 'text', 'Destinos donde las aceptamos' ),
				'me_dest_text' => array( 'Texto', 'html', 'Estas son las rutas en las que hoy recibimos medicinas y sus condiciones propias:' ),
				'me_dest_nota' => array( 'Nota bajo los destinos (vacía = texto automático con los demás países)', 'html', '' ),
			) ),
			's4' => array( 'label' => 'Medicinas · Cómo embalar y declarar las medicinas', 'fields' => array(
				'me_emb_title' => array( 'Título', 'text', 'Cómo embalar y declarar las medicinas' ),
				'me_emb_text' => array( 'Texto', 'html', 'Una caja con medicinas pasa más controles que una de ropa. Estas cinco pautas hacen que la inspección sea rápida y que el contenido llegue entero.' ),
				'me_e1_t' => array( 'Paso 1 · título', 'text', 'Todo en su caja original.' ),
				'me_e1_x' => array( 'Paso 1 · texto', 'textarea', 'Blísters dentro de su estuche, frascos con su tapa de fábrica y el prospecto dentro. Un medicamento suelto no se puede identificar y suele retenerse.' ),
				'me_e2_t' => array( 'Paso 2 · título', 'text', 'La receta, visible.' ),
				'me_e2_x' => array( 'Paso 2 · texto', 'textarea', 'Una copia en un sobre pegado a la tapa de la caja, por dentro, y otra que nos entregas al despachar.' ),
				'me_e3_t' => array( 'Paso 3 · título', 'text', 'En bolsa sellada aparte.' ),
				'me_e3_x' => array( 'Paso 3 · texto', 'textarea', 'Separadas de alimentos, cosméticos y ropa, para que la inspección no obligue a abrir todo el paquete.' ),
				'me_e4_t' => array( 'Paso 4 · título', 'text', 'Protección contra golpes y calor.' ),
				'me_e4_x' => array( 'Paso 4 · texto', 'textarea', 'Relleno alrededor de los frascos de vidrio y la caja lejos de las paredes. Durante el transporte no hay control de temperatura.' ),
				'me_e5_t' => array( 'Paso 5 · título', 'text', 'Una línea por medicamento.' ),
				'me_e5_x' => array( 'Paso 5 · texto', 'textarea', 'En la declaración: nombre comercial, principio activo, cantidad de envases y valor según la boleta.' ),
			) ),
			's5' => array( 'label' => 'Medicinas · Lo que no enviamos', 'fields' => array(
				'me_no_title' => array( 'Título', 'text', 'Lo que no enviamos' ),
				'me_lista2' => array( 'Lista (una línea por punto)', 'textarea', 'Medicamentos controlados: psicotrópicos, estupefacientes y los que exigen receta especial.
Medicamentos que necesitan refrigeración: no viajan con cadena de frío.
Productos sin registro sanitario, sueltos o fuera de su envase.
Cantidades que excedan un tratamiento personal.' ),
				'me_nota' => array( 'Nota', 'html', 'Las medicinas van en bolsa sellada dentro de la caja, separadas del resto, y se declaran una por una con su nombre, cantidad y valor. Si también envías ropa o alimentos, puedes mandarlo todo en la misma <a href="' . grenvios_pn_url( '/servicios/encomiendas-internacionales/' ) . '">encomienda</a>.' ),
			) ),
		),
		'embalaje-para-envios-internacionales' => array(
			'hero' => array( 'label' => 'Embalaje · Banner', 'fields' => array(
				'tool_eyebrow' => array( 'Frase superior', 'text', 'Guía práctica' ),
				'tool_title' => array( 'Título H1', 'text', 'Embalaje para envíos internacionales' ),
			) ),
			's1' => array( 'label' => 'Embalaje · Un buen embalaje protege el contenido y además te hace pagar menos', 'fields' => array(
				'em_h2' => array( 'Título', 'text', 'Un buen embalaje protege el contenido y además te hace pagar menos' ),
				'em_intro' => array( 'Texto', 'html', 'El embalaje para envíos internacionales tiene que aguantar varios tramos, cambios de temperatura y bultos apilados encima. Y como el precio se calcula sobre el tamaño de la caja, embalar bien también es la forma más directa de bajar el peso que se cobra.' ),
			) ),
			's2' => array( 'label' => 'Embalaje · Cuatro pasos para embalar bien', 'fields' => array(
				'em_pasos_title' => array( 'Título', 'text', 'Cuatro pasos para embalar bien' ),
				'em_p1_t' => array( 'Paso 1 · título', 'text', 'La caja.' ),
				'em_p1_x' => array( 'Paso 1 · texto', 'textarea', 'Nueva, de cartón corrugado de doble pared y lo más ajustada posible al contenido. Las cajas reutilizadas pierden resistencia y las etiquetas antiguas confunden al escanear.' ),
				'em_p2_t' => array( 'Paso 2 · título', 'text', 'El relleno.' ),
				'em_p2_x' => array( 'Paso 2 · texto', 'textarea', 'Nada debe moverse dentro. Papel arrugado, plástico de burbujas o la propia ropa entre los objetos. Deja unos 5 cm de relleno entre el contenido y las paredes.' ),
				'em_p3_t' => array( 'Paso 3 · título', 'text', 'El cierre.' ),
				'em_p3_x' => array( 'Paso 3 · texto', 'textarea', 'Cinta de embalaje ancha en forma de H: a lo largo de la unión central y en los dos bordes, arriba y abajo. La cinta adhesiva de oficina o la de papel no sirven.' ),
				'em_p4_t' => array( 'Paso 4 · título', 'text', 'El rótulo.' ),
				'em_p4_x' => array( 'Paso 4 · texto', 'textarea', 'Una sola etiqueta por fuera con los datos del destinatario, y una copia dentro de la caja. Tapa o retira cualquier etiqueta anterior.' ),
			) ),
			's3' => array( 'label' => 'Embalaje · Casos que necesitan un cuidado extra', 'fields' => array(
				'em_casos_title' => array( 'Título', 'text', 'Casos que necesitan un cuidado extra' ),
				'em_c1_t' => array( '«Objetos frágiles» · título', 'text', 'Objetos frágiles' ),
				'em_c1_x' => array( '«Objetos frágiles» · texto', 'html', 'Cada pieza envuelta por separado y, si es muy delicada, en una caja dentro de otra caja con relleno entre las dos. Marca la caja como frágil en al menos dos caras.' ),
				'em_c2_t' => array( '«Líquidos y cremas» · título', 'text', 'Líquidos y cremas' ),
				'em_c2_x' => array( '«Líquidos y cremas» · texto', 'html', 'Solo si la vía y el destino los admiten. Tapa asegurada con cinta, dentro de una bolsa sellada, y separados de la ropa y los papeles.' ),
				'em_c3_t' => array( '«Ropa» · título', 'text', 'Ropa' ),
				'em_c3_x' => array( '«Ropa» · texto', 'html', 'Doblada, no enrollada con relleno. Una bolsa al vacío reduce mucho el volumen, y por tanto el peso volumétrico, en envíos grandes de ropa.' ),
				'em_c4_t' => array( '«Electrónica» · título', 'text', 'Electrónica' ),
				'em_c4_x' => array( '«Electrónica» · texto', 'html', 'En su caja original si la tienes, con relleno alrededor. Los aparatos con batería tienen reglas propias por vía aérea: consulta antes de embalar.' ),
			) ),
			's4' => array( 'label' => 'Embalaje · Errores de embalaje que salen caros', 'fields' => array(
				'em_err_title' => array( 'Título', 'text', 'Errores de embalaje que salen caros' ),
				'em_lista1' => array( 'Lista (una línea por punto)', 'textarea', 'Usar una caja mucho más grande de lo necesario: se cobra el espacio vacío.
Dejar huecos: lo que se mueve dentro es lo que se rompe.
Cerrar con cinta solo la unión central: las esquinas ceden con el peso.
Cerrar la caja antes de revisar el contenido con nosotros.
Poner los datos del destinatario solo por fuera: si la etiqueta se pierde, no hay forma de identificar el bulto.' ),
				'em_nota' => array( 'Nota', 'html', 'Si no tienes caja o no sabes cuál usar, la ponemos nosotros ajustada a lo que envías, en la oficina o en el <a href="' . grenvios_pn_url( '/recojo-a-domicilio-lima/' ) . '">recojo a domicilio</a>. Antes de medirla, mira cómo influye en el precio en <a href="' . grenvios_pn_url( '/cuanto-cuesta-enviar-un-paquete-al-extranjero/' ) . '">cuánto cuesta enviar un paquete al extranjero</a>.' ),
			) ),
		),
	);
	foreach ( $pn as $slug => $secciones ) {
		if ( ! isset( $reg[ $slug ] ) ) {
			$reg[ $slug ] = array( 'label' => ucfirst( str_replace( '-', ' ', $slug ) ), 'priority' => 62, 'sections' => array() );
		}
		foreach ( $secciones as $k => $sec ) $secciones[ $k ]['_no_token_check'] = true;
		$reg[ $slug ]['sections'] = array_merge( $secciones, (array) $reg[ $slug ]['sections'] );
	}
	return $reg;
}, 9 );


/* ══════════════════════════════════════
   3) ENTRADILLA CON LA KEYWORD
══════════════════════════════════════ */
add_filter( 'grenvios_ent_textos', function ( $t ) {
	$t['cuanto-cuesta-enviar-un-paquete-al-extranjero'] = 'Para saber <strong>cuánto cuesta enviar un paquete al extranjero</strong> desde Perú necesitas cuatro datos: peso, medidas de la caja, destino y valor del contenido. Aquí explicamos cómo se combinan y cómo pagar menos. Con esos datos, <a href="%H%/cotizar/">te damos el precio cerrado</a>.';
	$t['encomiendas-internacionales'] = 'Enviamos <strong>encomiendas internacionales</strong> desde Lima a familiares y amigos en más de treinta destinos: ropa, regalos y productos peruanos, por vía aérea o terrestre y con seguimiento hasta la entrega. <a href="%H%/cotizar/">Cotiza tu encomienda</a>.';
	$t['envio-de-medicinas-al-extranjero'] = 'El <strong>envío de medicinas al extranjero</strong> es posible con receta médica, en envase original y en cantidades de uso personal, hacia los destinos cuya aduana lo admite. Consúltanos con el nombre del medicamento <a href="%H%/cotizar/">antes de comprarlo</a>.';
	$t['embalaje-para-envios-internacionales'] = 'Un buen <strong>embalaje para envíos internacionales</strong> evita daños y baja el precio, porque se cobra el tamaño de la caja. Esta guía explica qué caja usar, cómo rellenarla y cómo cerrarla. Luego, <a href="%H%/servicios/peso-volumetrico/">calcula el peso que se cobra</a>.';
	return $t;
} );

/* ══════════════════════════════════════
   4) PREGUNTAS FRECUENTES (van al FAQPage)
══════════════════════════════════════ */
add_filter( 'grenvios_page_faqs', function ( $faqs, $slug ) {
	if ( function_exists( 'grenvios_hq_pais' ) && grenvios_hq_pais() !== '' ) return $faqs;
	$m = array(
		'cuanto-cuesta-enviar-un-paquete-al-extranjero' => array(
			array( '¿Cuánto cuesta enviar un paquete de 5 kg al extranjero?', 'Depende del tamaño de la caja, la vía y el destino. Si la caja es pequeña, se cobran los 5 kg reales; si abulta más de lo que pesa, se cobra su peso volumétrico. Escríbenos con las medidas y el destino y te damos el precio cerrado en minutos.' ),
			array( '¿Por qué no hay una tarifa fija por país?', 'Porque el mismo destino cuesta distinto según el peso que se cobra y la vía elegida. Publicar un precio único sería publicar un precio que casi nunca coincide con tu envío.' ),
			array( '¿El precio incluye los impuestos del destino?', 'En las rutas terrestres el impuesto se paga en {{origen_ciudad}} al despachar y lo incluimos en la cotización. En las aéreas depende del contenido y del valor; te lo confirmamos antes de despachar.' ),
			array( '¿Cómo puedo pagar menos por mi envío?', 'Ajustando la caja al contenido, juntando varios envíos en uno y, hacia países vecinos, eligiendo la vía terrestre si no hay prisa.' ),
		),
		'encomiendas-internacionales' => array(
			array( '¿Qué es una encomienda internacional?', 'Un envío personal a un familiar o amigo en otro país: ropa, regalos, productos peruanos o documentos, en cantidades de uso personal.' ),
			array( '¿Cuánto demora una encomienda?', 'Depende del destino y de la vía: desde 4 a 6 días hábiles a Estados Unidos hasta 15 a Venezuela. Los plazos de cada país están en la tabla de esta página.' ),
			array( '¿Puedo enviar comida en una encomienda?', 'Sí, si es envasada de fábrica, no perecible y con su etiqueta, y si el país de destino la admite. Consulta antes de comprar.' ),
			array( '¿Quién recoge la encomienda en destino?', 'Donde la entrega es a domicilio, la recibe el destinatario en su dirección. Donde es en agencia, la retira con el documento a cuyo nombre va el envío.' ),
		),
		'envio-de-medicinas-al-extranjero' => array(
			array( '¿Se pueden enviar medicinas al extranjero?', 'Sí, con receta médica, en su envase original y en cantidades de uso personal, hacia los destinos que las admiten. Los medicamentos controlados y los que necesitan frío no se envían.' ),
			array( '¿A qué países puedo enviar medicinas?', 'Hoy las aceptamos en las rutas indicadas en esta página, con sus condiciones. Para el resto, escríbenos con el nombre del medicamento antes de comprarlo.' ),
			array( '¿La receta tiene que estar a nombre del destinatario?', 'Conviene que sí: es lo que demuestra que el medicamento es para uso personal de quien lo recibe.' ),
		),
		'embalaje-para-envios-internacionales' => array(
			array( '¿Qué caja es mejor para un envío internacional?', 'Una caja nueva de cartón corrugado de doble pared, lo más ajustada posible al contenido.' ),
			array( '¿Cómo se cierra una caja para envío internacional?', 'Con cinta de embalaje ancha en forma de H: a lo largo de la unión central y en los bordes, arriba y abajo.' ),
			array( '¿El embalaje influye en el precio?', 'Sí. Se cobra el mayor entre el peso real y el volumétrico, que depende de las medidas de la caja. Una caja ajustada puede bajar bastante el peso que se cobra.' ),
			array( '¿Ustedes embalan el paquete?', 'Sí. Si no tienes caja, la ponemos ajustada a lo que envías, en la oficina o durante el recojo a domicilio.' ),
		),
	);
	if ( ! isset( $m[ $slug ] ) ) return $faqs;
	return array_merge( (array) $faqs, $m[ $slug ] );
}, 30, 2 );

/* ══════════════════════════════════════
   5) ENLAZADO INTERNO: hacia dentro y hacia las nuevas
══════════════════════════════════════ */
add_filter( 'grenvios_related_map', function ( $mapa, $slug ) {
	$mapa['cuanto-cuesta-enviar-un-paquete-al-extranjero'] = array(
		'Antes de cotizar' => array( 'servicios/peso-volumetrico', 'embalaje-para-envios-internacionales', 'aduanas-e-impuestos' ),
		'Destinos'         => array( 'destinos', 'tiempos-de-entrega' ),
		'Siguiente paso'   => array( 'cotizar' ),
	);
	$mapa['encomiendas-internacionales'] = array(
		'Te puede interesar' => array( 'servicios/envio-de-alimentos', 'servicios/envio-de-medicinas-al-extranjero', 'que-se-puede-enviar' ),
		'Destinos'           => array( 'destinos', 'tiempos-de-entrega' ),
		'Siguiente paso'     => array( 'cotizar', 'recojo-a-domicilio-lima' ),
	);
	$mapa['envio-de-medicinas-al-extranjero'] = array(
		'Te puede interesar' => array( 'servicios/encomiendas-internacionales', 'que-se-puede-enviar', 'aduanas-e-impuestos' ),
		'Destinos'           => array( 'destinos/cuba', 'destinos/bolivia' ),
		'Siguiente paso'     => array( 'cotizar' ),
	);
	$mapa['embalaje-para-envios-internacionales'] = array(
		'Te puede interesar' => array( 'servicios/peso-volumetrico', 'cuanto-cuesta-enviar-un-paquete-al-extranjero', 'como-enviar-un-paquete-al-extranjero' ),
		'Siguiente paso'     => array( 'cotizar', 'recojo-a-domicilio-lima' ),
	);

	$entrantes = array(
		''                                     => array(),
		'cotizar'                              => array( 'También te sirve'   => array( 'cuanto-cuesta-enviar-un-paquete-al-extranjero', 'embalaje-para-envios-internacionales' ) ),
		'peso-volumetrico'                     => array( 'Te puede interesar' => array( 'cuanto-cuesta-enviar-un-paquete-al-extranjero', 'embalaje-para-envios-internacionales' ) ),
		'como-enviar-un-paquete-al-extranjero' => array( 'Te puede interesar' => array( 'embalaje-para-envios-internacionales', 'cuanto-cuesta-enviar-un-paquete-al-extranjero' ) ),
		'envio-internacional-de-paquetes'      => array( 'Te puede interesar' => array( 'servicios/encomiendas-internacionales', 'cuanto-cuesta-enviar-un-paquete-al-extranjero' ) ),
		'envio-de-alimentos'                   => array( 'Te puede interesar' => array( 'servicios/encomiendas-internacionales', 'servicios/envio-de-medicinas-al-extranjero' ) ),
		'que-se-puede-enviar'                  => array( 'Te puede interesar' => array( 'servicios/envio-de-medicinas-al-extranjero', 'embalaje-para-envios-internacionales' ) ),
		'servicios'                            => array( 'Más servicios'      => array( 'servicios/encomiendas-internacionales', 'servicios/envio-de-medicinas-al-extranjero' ) ),
		'preguntas-frecuentes'                 => array( 'Te puede interesar' => array( 'cuanto-cuesta-enviar-un-paquete-al-extranjero', 'servicios/envio-de-medicinas-al-extranjero' ) ),
	);
	foreach ( $entrantes as $origen => $bloques ) {
		if ( $origen === '' || ! isset( $mapa[ $origen ] ) ) continue;
		foreach ( $bloques as $titulo => $destinos ) {
			$actual = isset( $mapa[ $origen ][ $titulo ] ) ? $mapa[ $origen ][ $titulo ] : array();
			$mapa[ $origen ][ $titulo ] = array_values( array_unique( array_merge( $actual, $destinos ) ) );
		}
	}
	return $mapa;
}, 20, 2 );

/* ══════════════════════════════════════
   6) TEXTO PARA LA AUDITORÍA «SEO POR PÁGINA»
   Estas páginas se pintan desde PHP, así que la auditoría solo veía el título
   y la entradilla (unas 150 palabras de 800). Se pinta la página en un búfer,
   igual que hace inc/seo-titulos.php con las fichas de destino, y la
   auditoría mide lo mismo que Google.
══════════════════════════════════════ */
add_filter( 'grenvios_seo_page_text', function ( $parts, $post_id, $slug ) {
	$slug = (string) get_post_field( 'post_name', $post_id );
	$fn = array(
		'cuanto-cuesta-enviar-un-paquete-al-extranjero' => 'grenvios_pn_render_precio',
		'encomiendas-internacionales'                   => 'grenvios_pn_render_encomiendas',
		'envio-de-medicinas-al-extranjero'                            => 'grenvios_pn_render_medicinas',
		'embalaje-para-envios-internacionales'          => 'grenvios_pn_render_embalaje',
	);
	if ( ! isset( $fn[ $slug ] ) || ! function_exists( $fn[ $slug ] ) ) return $parts;
	try {
		ob_start();
		call_user_func( $fn[ $slug ] );
		$html = ob_get_clean();
		if ( is_string( $html ) && $html !== '' ) {
			$parts[] = $html;
			foreach ( (array) apply_filters( 'grenvios_page_faqs', array(), $slug ) as $q ) {
				$parts[] = $q[0] . ' ' . $q[1];
			}
		}
	} catch ( Throwable $e ) {
		if ( ob_get_level() ) ob_end_clean();
	}
	return $parts;
}, 20, 3 );
