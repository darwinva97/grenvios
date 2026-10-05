<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Title y meta description alineados con la keyword de cada página
 * ══════════════════════════════════════════════════════════════════════════
 *
 * LA AUDITORÍA (menú «SEO por página», ahora que cada página tiene keyword de
 * verdad — ver inc/seo-keywords-defecto.php) daba 15 errores del mismo tipo en
 * la ruta principal: **el title no contiene la keyword**, que es la señal
 * on-page que más pesa. Ejemplos reales de lo que había:
 *
 *     keyword «courier internacional en Lima»
 *       title  «Nosotros | Grenvíos — Transporte Internacional Confiable»
 *
 *     keyword «agencia de envíos internacionales en Lima»
 *       title  «Contacto | Grenvíos — Lima, Perú»
 *
 *     keyword «rastreo de envíos internacionales»
 *       title  «Rastrea tu Envío | Seguimiento Grenvíos»
 *
 * Los títulos no eran malos; simplemente hablaban de la sección («Nosotros»,
 * «Contacto») en vez de la consulta. Un title empieza por lo que la persona
 * escribió en Google y deja la marca al final, donde no estorba.
 *
 * CRITERIO DE REDACCIÓN
 *   · La keyword, entera, lo más a la izquierda posible.
 *   · Un segundo elemento que dé un motivo para hacer clic —el precio, el
 *     plazo, la ciudad— y no solo repita la keyword.
 *   · Marca al final. Entre 50 y 60 caracteres: lo que se ve en el resultado.
 *   · La description repite la keyword una vez, en lenguaje natural, y dice
 *     qué se encuentra en la página. No es un eslogan.
 *
 * ALCANCE
 * Solo la ruta principal y solo cuando esa página NO tiene su propio title
 * escrito a mano: las rutas de país ya tienen los suyos con su destino, y lo
 * que escriba la clienta manda siempre.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function grenvios_seo_titulos() {
	return apply_filters( 'grenvios_seo_titulos', array(

		'home' => array(
			'Envíos Internacionales desde Perú a +30 Países | Grenvíos',
			'Envíos internacionales desde Perú a más de 30 destinos: paquetes, documentos, equipaje y carga. Cotiza en minutos y despacha el mismo día desde Lima.',
		),
		'nosotros' => array(
			'Quiénes Somos: Empresa de Envíos desde Perú | Grenvíos',
			'Grenvíos es una empresa de envíos desde Perú con oficina en Lima: quiénes somos, cómo revisamos cada envío antes de despacharlo y a qué países llegamos.',
		),
		'contacto' => array(
			'Agencia de Envíos Internacionales en Lima | Grenvíos',
			'Nuestra agencia de envíos internacionales en Lima: dirección, horario y qué traer para despachar. También recogemos tu envío en tu distrito.',
		),
		'cotizar' => array(
			'Cotizar Envío Internacional: Precio en Minutos | Grenvíos',
			'Cotizar un envío internacional con Grenvíos toma minutos: con el peso, las medidas y el destino te damos el precio cerrado, puesto en destino.',
		),
		'destinos' => array(
			'Destinos de Envíos Internacionales desde Perú | Grenvíos',
			'Todos los destinos de envíos internacionales desde Perú: vía disponible, plazo, forma de entrega y qué admite la aduana de cada país.',
		),
		'servicios' => array(
			'Servicios de Envío Internacional desde Perú | Grenvíos',
			'Nuestros servicios de envío internacional: paquetes, documentos, equipaje, compras, alimentos y carga, con recojo en Lima y entrega en destino.',
		),
		'envio-internacional-de-paquetes' => array(
			'Envío Internacional de Paquetes desde Perú | Grenvíos',
			'Envío internacional de paquetes desde Perú por vía aérea o terrestre: cómo se cobra el peso, qué declarar y cuánto demora según el destino.',
		),
		'envio-internacional-de-documentos' => array(
			'Envío Internacional de Documentos | Grenvíos',
			'Envío internacional de documentos por vía aérea: títulos, partidas, poderes y contratos, con apostilla previa cuando el destino la exige.',
		),
		'carga-internacional' => array(
			'Carga Internacional desde Perú: Aérea y Terrestre | Grenvíos',
			'Carga internacional desde Perú: consolidamos varios bultos, preparamos la documentación de exportación y elegimos la vía que más conviene.',
		),
		'envio-de-equipaje' => array(
			'Envío de Equipaje al Extranjero desde Perú | Grenvíos',
			'Envío de equipaje al extranjero: manda tus maletas por delante en vez de pagar exceso en el aeropuerto. Recojo en Lima y entrega en destino.',
		),
		'envio-de-compras' => array(
			'Envío de Compras al Extranjero desde Perú | Grenvíos',
			'Envío de compras al extranjero: compra en tiendas peruanas con nuestra dirección, consolidamos tus pedidos y pagas un solo flete.',
		),
		'envio-de-alimentos' => array(
			'Envío de Alimentos al Extranjero desde Perú | Grenvíos',
			'Envío de alimentos al extranjero: qué admite cada aduana, cómo se embalan los productos sellados y por qué vía pueden viajar.',
		),
		'apostilla-y-traduccion' => array(
			'Apostilla y Traducción de Documentos | Grenvíos',
			'Apostilla y traducción de documentos antes de enviarlos: qué trámite necesita cada país y cuánto suma al plazo total del envío.',
		),
		'peso-volumetrico' => array(
			'Peso Volumétrico: Calculadora y Fórmula | Grenvíos',
			'Calcula el peso volumétrico de tu caja (alto × largo × ancho ÷ 5000) y descubre por cuál de los dos pesos se cobra tu envío internacional.',
		),
		'tiempos-de-entrega' => array(
			'Tiempos de Entrega de Envíos Internacionales | Grenvíos',
			'Tiempos de entrega de envíos internacionales país por país: cómo se cuentan los días hábiles y qué tramo puede alargar el plazo.',
		),
		'que-se-puede-enviar' => array(
			'Qué se Puede Enviar al Extranjero desde Perú | Grenvíos',
			'Qué se puede enviar al extranjero y qué retiene la aduana: alimentos, medicinas, electrónica con batería, líquidos y productos de marca.',
		),
		'aduanas-e-impuestos' => array(
			'Aduanas e Impuestos en Envíos Internacionales | Grenvíos',
			'Aduanas e impuestos en envíos internacionales: sobre qué se calculan, quién los paga y qué documentación evita que el envío se detenga.',
		),
		'seguro-de-envios' => array(
			'Seguro para Envíos Internacionales | Grenvíos',
			'Seguro para envíos internacionales: qué cubre, qué no, y por qué declarar de menos el valor real deja tu envío cubierto por esa cifra.',
		),
		'rastreo-de-envios' => array(
			'Rastreo de Envíos Internacionales | Grenvíos',
			'Rastreo de envíos internacionales con tu número de guía: qué significa cada estado del seguimiento y cuándo conviene avisarnos.',
		),
		'como-enviar-un-paquete-al-extranjero' => array(
			'Cómo Enviar un Paquete al Extranjero desde Perú | Grenvíos',
			'Cómo enviar un paquete al extranjero en cinco pasos: medir, cotizar, elegir vía, declarar el contenido y seguir el envío hasta la entrega.',
		),
		'recojo-a-domicilio-lima' => array(
			'Recojo de Envíos a Domicilio en Lima | Grenvíos',
			'Recojo de envíos a domicilio en Lima: pasamos por tu paquete en tu casa, oficina o proveedor, lo pesamos, lo embalamos y lo despachamos.',
		),
		'envios-desde-provincias' => array(
			'Envíos Internacionales desde Provincias | Grenvíos',
			'Envíos internacionales desde provincias: cómo llega tu paquete a Lima, cuánto margen dejar y qué plazo se cuenta desde el despacho.',
		),
		'envios-para-empresas' => array(
			'Envíos Internacionales para Empresas | Grenvíos',
			'Envíos internacionales para empresas: consolidación, documentación de exportación, recojos programados y control de costos por envío.',
		),
		'preguntas-frecuentes' => array(
			'Preguntas Frecuentes sobre Envíos Internacionales | Grenvíos',
			'Preguntas frecuentes sobre envíos internacionales: precios, plazos, aduana, embalaje, seguro y entrega, respondidas una por una.',
		),
		'blog' => array(
			'Guías de Envíos Internacionales | Blog de Grenvíos',
			'Guías de envíos internacionales escritas desde la operación: precios, plazos, aduanas y embalaje, ordenadas por tema y por país.',
		),
		'articulos-por-pais' => array(
			'Guías de Envío por País: Aduana, Plazos y Precios | Grenvíos',
			'Qué se puede enviar por país, artículo por artículo: alimentos, medicinas, electrónica, cosméticos y ropa, con la regla de cada aduana.',
		),
	) );
}

/* Solo la ruta principal y solo si la página no tiene title propio escrito. */
function grenvios_seo_titulo_propio() {
	if ( is_admin() ) return array();

	if ( function_exists( 'grenvios_i18n_is_default' ) && ! grenvios_i18n_is_default() ) return array();

	$id = (int) get_queried_object_id();
	if ( is_home() && ! is_front_page() ) {
		$pid = (int) get_option( 'page_for_posts' );
		if ( $pid ) $id = $pid;
	}
	if ( $id && trim( (string) get_post_meta( $id, 'grenvios_seo_title', true ) ) !== '' ) return array();

	$slug = function_exists( 'grenvios_current_slug' ) ? grenvios_current_slug() : '';
	$mapa = grenvios_seo_titulos();
	return isset( $mapa[ $slug ] ) ? $mapa[ $slug ] : array();
}

/* Se engancha donde el tema decide sus textos SEO por defecto, no en la salida:
 * así el <title> que se sirve y el que audita «SEO por página» son el mismo, y
 * lo que la clienta escriba a mano sigue mandando, porque las metas se aplican
 * después (ver grenvios_seo_for_slug). */
add_filter( 'grenvios_seo_defaults', function ( $base, $slug ) {
	if ( function_exists( 'grenvios_i18n_is_default' ) && ! grenvios_i18n_is_default() ) return $base;
	$mapa = grenvios_seo_titulos();
	if ( ! isset( $mapa[ $slug ] ) ) return $base;
	return array( $mapa[ $slug ][0], $mapa[ $slug ][1] );
}, 10, 2 );

/* ─────────────────────────────────────────────────────────────────────────
 *  Que la auditoría mida lo que la página muestra de verdad
 *
 *  La ficha de un destino son 3.700 palabras en el navegador y la auditoría
 *  veía «1 palabra»: su contenido no está en post_content, lo arma el tema con
 *  los datos del destino. Igual pasa con las secciones por país y con las
 *  secciones propias de la ruta principal. Medir mal es peor que no medir: la
 *  pantalla de SEO pedía «600+ palabras» en las páginas más largas del sitio.
 * ───────────────────────────────────────────────────────────────────────── */
add_filter( 'grenvios_seo_page_text', function ( $parts, $post_id, $slug ) {
	/* 1) Ficha de destino: sus datos son su contenido.
	 *
	 *    Se pinta entera en un búfer con el post de la ficha como contexto, que
	 *    es lo único que mide de verdad una página data-driven: así la
	 *    auditoría ve las mismas 3.700 palabras y las mismas apariciones de la
	 *    keyword que ve Google. Si algo fallara, se cae a los campos sueltos de
	 *    abajo y la auditoría sigue funcionando. */
	if ( function_exists( 'grenvios_destinos' ) ) {
		$dest = grenvios_destinos();
		if ( isset( $dest[ $slug ] ) && function_exists( 'grenvios_render_destino' ) ) {
			global $post;
			$previo = $post;
			$post   = get_post( $post_id );
			if ( $post ) setup_postdata( $post );
			try {
				ob_start();
				grenvios_render_destino( $slug, array( 'hero' => true ) );
				$render = ob_get_clean();
				if ( is_string( $render ) && $render !== '' ) $parts[] = $render;
			} catch ( Throwable $e ) {
				if ( ob_get_level() ) ob_end_clean();
			}
			wp_reset_postdata();
			$post = $previo;
		}
		if ( isset( $dest[ $slug ] ) ) {
			foreach ( array( 'title', 'lead', 'tiempo', 'modos', 'entrega', 'restr', 'desc' ) as $k ) {
				if ( ! empty( $dest[ $slug ][ $k ] ) ) $parts[] = (string) $dest[ $slug ][ $k ];
			}
			if ( function_exists( 'grenvios_pais_datos' ) ) {
				$d = grenvios_pais_datos( $slug );
				foreach ( array( 'ciudades', 'top', 'prohibidos', 'documentos', 'embalaje', 'comunidad' ) as $k ) {
					if ( ! empty( $d[ $k ] ) ) $parts[] = (string) $d[ $k ];
				}
			}
		}
	}

	/* 2) Secciones propias de la ruta principal (inc/paginas-contenido-seo.php). */
	if ( function_exists( 'grenvios_pseo_lista' ) ) {
		foreach ( grenvios_pseo_lista( $slug ) as $sec ) {
			foreach ( array( 'sub', 'titulo', 'html' ) as $k ) {
				if ( ! empty( $sec[ $k ] ) ) $parts[] = (string) $sec[ $k ];
			}
		}
	}

	/* 2 bis) El hub de guías por país también se pinta en PHP. */
	if ( $slug === 'articulos-por-pais' && function_exists( 'grenvios_hub_blog_datos' ) ) {
		foreach ( (array) grenvios_hub_blog_datos() as $fila ) {
			array_walk_recursive( $fila, function ( $x ) use ( &$parts ) {
				if ( is_string( $x ) && $x !== '' ) $parts[] = $x;
			} );
		}
	}

	/* 3) La entradilla con la keyword (inc/seo-entradillas.php): en las páginas
	 *    data-driven no está en el registro de campos, así que sin esto la
	 *    auditoría no la veía y seguía diciendo que la keyword no aparecía. */
	if ( function_exists( 'grenvios_ent_campos' ) ) {
		$lang = function_exists( 'pll_get_post_language' ) ? (string) pll_get_post_language( $post_id ) : '';
		$def  = function_exists( 'grenvios_i18n_default' ) ? grenvios_i18n_default() : '';

		if ( metadata_exists( 'post', $post_id, 'grenvios_ent_intro' ) ) {
			$v = (string) get_post_meta( $post_id, 'grenvios_ent_intro', true );
		} elseif ( $lang !== '' && $lang !== $def && function_exists( 'grenvios_ent_pais_texto' ) ) {
			/* Página de ruta de país: su entradilla se genera con el destino. */
			$v = grenvios_ent_pais_texto( $slug, $lang, $post_id );
		} else {
			$e = grenvios_ent_campos( $slug );
			$v = (string) $e['ent_intro'][2];
		}
		if ( $v !== '' ) $parts[] = $v;
	}

	/* 4) Preguntas frecuentes de la página, que también son texto indexable. */
	if ( function_exists( 'grenvios_page_faqs' ) ) {
		foreach ( (array) grenvios_page_faqs( $slug ) as $f ) {
			if ( is_array( $f ) ) $parts[] = implode( ' ', array_filter( $f, 'is_string' ) );
		}
	}

	return $parts;
}, 10, 3 );
