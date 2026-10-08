<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Cabecera de cada página de ruta: el país arriba del todo
 * ══════════════════════════════════════════════════════════════════════════
 *
 * El `<title>` de las copias ya decía «Contacto para Envíos a Estados Unidos»,
 * pero lo que se ve —antesala, H1 y migas— seguía siendo el de Perú: «Contacto
 * Grenvíos — Hablemos de tu envío». Dos problemas a la vez:
 *
 *   SEO   El H1 no coincidía con el título, y es la segunda señal más fuerte de
 *         la página. Google veía una página sobre Estados Unidos con un
 *         encabezado que no lo mencionaba.
 *   UX    Quien llegaba desde el buscador no leía nada sobre su país por encima
 *         del pliegue, en 24 páginas por ruta.
 *
 * Al duplicar se copian los campos de la página de Perú tal cual, así que la
 * cabecera se reescribe al pintarla. Cubre los dos caminos del tema: las páginas
 * de plantilla (campos `*_hero_title`, `*_hero_eyebrow`…) y las que pintan desde
 * PHP con `logisko_page_banner()`.
 *
 * SI LA CLIENTA LO EDITÓ, MANDA LO SUYO
 *
 * En las páginas de plantilla se compara el valor guardado en la copia con el de
 * su original: si difieren, alguien lo escribió a mano para ese país y no se
 * toca. Solo se reemplaza lo que sigue siendo la copia literal de Perú.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Ruta de país activa (no la principal ni un idioma de verdad), o ''. */
function grenvios_cab_lang() {
	static $l = null;
	if ( $l !== null ) return $l;
	$l = '';
	if ( is_admin() || ! function_exists( 'grenvios_i18n_current' ) ) return $l;
	$c = grenvios_i18n_current();
	if ( ! $c || grenvios_i18n_default() === $c ) return $l;
	if ( ! function_exists( 'grenvios_es_ruta_pais' ) || ! grenvios_es_ruta_pais( $c ) ) return $l;
	return $l = $c;
}

function grenvios_cab_pais() {
	if ( function_exists( 'grenvios_espejo_es' ) && is_singular() && grenvios_espejo_es( (int) get_queried_object_id() ) ) return '';
	$l = grenvios_cab_lang();
	return ( $l !== '' && function_exists( 'grenvios_sede_destino_nombre' ) ) ? grenvios_sede_destino_nombre( $l ) : '';
}

/* H1 de cada página, con %s por el país. En minúscula de frase, como el resto de
 * cabeceras del tema, y con el país dentro de <span> para que tome el color de
 * acento igual que el «Grenvíos» de «Conectamos al mundo con Grenvíos».
 *
 * Filtro `grenvios_cabecera_titulos`: para reescribir cualquiera. */
function grenvios_cab_titulos() {
	return apply_filters( 'grenvios_cabecera_titulos', array(
		'contacto'                             => 'Hablemos de tu envío a <span>%s</span>',
		'nosotros'                             => 'Tu equipo para enviar a <span>%s</span>',
		'servicios'                            => 'Servicios de envío a <span>%s</span>',
		'envio-internacional-de-paquetes'      => 'Envío de paquetes a <span>%s</span>',
		'envio-internacional-de-documentos'    => 'Envío de documentos a <span>%s</span>',
		'carga-internacional'                  => 'Carga internacional a <span>%s</span>',
		'apostilla-y-traduccion'               => 'Apostilla y traducción para <span>%s</span>',
		'cotizar'                              => 'Cotiza tu envío a <span>%s</span>',
		'rastreo-de-envios'                    => 'Rastrea tu envío a <span>%s</span>',
		'envios-para-empresas'                 => 'Envíos de empresa a <span>%s</span>',
		'envio-de-compras'                     => 'Envía tus compras a <span>%s</span>',
		'envio-de-equipaje'                    => 'Envío de equipaje a <span>%s</span>',
		'envio-de-alimentos'                   => 'Envío de alimentos a <span>%s</span>',
		'peso-volumetrico'                     => 'Cómo se cobra el peso en tu envío a <span>%s</span>',
		'seguro-de-envios'                     => 'Seguro para tu envío a <span>%s</span>',
		'tiempos-de-entrega'                   => '¿Cuánto demora un envío a <span>%s</span>?',
		'que-se-puede-enviar'                  => 'Qué se puede enviar a <span>%s</span>',
		'aduanas-e-impuestos'                  => 'Aduana e impuestos de <span>%s</span>',
		'preguntas-frecuentes'                 => 'Preguntas frecuentes sobre envíos a <span>%s</span>',
		'envios-desde-provincias'              => 'Envía a <span>%s</span> desde cualquier ciudad del Perú',
		'recojo-a-domicilio-lima'              => 'Recogemos en casa tu envío a <span>%s</span>',
		'como-enviar-un-paquete-al-extranjero' => 'Cómo enviar un paquete a <span>%s</span>',
		'blog'                                 => 'Guías para enviar a <span>%s</span>',
	) );
}

/* Slug maestro de la página que se está pintando. */
function grenvios_cab_slug() {
	$id = is_home() ? (int) get_option( 'page_for_posts' ) : (int) get_queried_object_id();
	if ( ! $id ) return '';
	if ( is_home() && function_exists( 'pll_get_post' ) ) {
		$t = (int) pll_get_post( $id, grenvios_cab_lang() );
		if ( $t ) $id = $t;
	}
	return function_exists( 'grenvios_canonical_slug' ) ? grenvios_canonical_slug( $id ) : get_post_field( 'post_name', $id );
}

/* ¿Sigue la copia con el texto literal de Perú en este campo? */
function grenvios_cab_sin_editar( $key ) {
	$id = (int) get_queried_object_id();
	if ( ! $id || ! function_exists( 'grenvios_i18n_master_id' ) ) return true;
	$m = (int) grenvios_i18n_master_id( $id );
	if ( ! $m || $m === $id ) return true;
	$mk = 'grenvios_' . $key;
	if ( ! metadata_exists( 'post', $id, $mk ) ) return true;
	return (string) get_post_meta( $id, $mk, true ) === (string) get_post_meta( $m, $mk, true );
}

/* ── Páginas de plantilla: los campos *_hero_* ── */
add_filter( 'grenvios_campo_valor', function ( $valor, $key ) {
	/* La mayoría de plantillas nombran sus campos `prefijo_hero_title`. La de
	 * preguntas frecuentes no: usa `pf_title` y `pf_sub`, y por eso quedaba como
	 * la única página de cada ruta con «Resolvemos tus dudas» de cabecera. */
	if ( preg_match( '/_hero_(title|eyebrow|breadcrumb_home)$/', $key, $mm ) ) {
		$campo = $mm[1];
	} elseif ( $key === 'pf_title' ) {
		$campo = 'title';
	} elseif ( $key === 'pf_sub' ) {
		$campo = 'eyebrow';
	} elseif ( $key === 'tool_title' ) {     // páginas pintadas desde PHP
		$campo = 'title';
	} elseif ( $key === 'tool_eyebrow' ) {
		$campo = 'eyebrow';
	} else {
		return $valor;
	}
	$pais = grenvios_cab_pais();
	if ( $pais === '' ) return $valor;
	if ( ! grenvios_cab_sin_editar( $key ) ) return $valor;   // la clienta lo escribió

	if ( $campo === 'eyebrow' || $campo === 'breadcrumb_home' ) {
		return 'Envíos a ' . $pais;
	}
	$mapa = grenvios_cab_titulos();
	$slug = grenvios_cab_slug();
	return isset( $mapa[ $slug ] ) ? sprintf( $mapa[ $slug ], esc_html( $pais ) ) : $valor;
}, 10, 2 );

/* ── Páginas pintadas desde PHP ── */
add_filter( 'grenvios_cabecera', function ( $par ) {
	$pais = grenvios_cab_pais();
	if ( $pais === '' ) return $par;
	$mapa = grenvios_cab_titulos();
	$slug = grenvios_cab_slug();
	if ( ! isset( $mapa[ $slug ] ) ) return $par;
	// Si la clienta cambió la cabecera de esta copia desde el editor, manda la suya.
	if ( ! grenvios_cab_sin_editar( 'tool_title' ) || ! grenvios_cab_sin_editar( 'tool_eyebrow' ) ) return $par;
	return array( 'Envíos a ' . $pais, sprintf( $mapa[ $slug ], esc_html( $pais ) ) );
} );

/* ── Migas: «Inicio» de una ruta es la portada de ese país, y así se llama ── */
add_filter( 'grenvios_miga_inicio', function ( $txt ) {
	$pais = grenvios_cab_pais();
	return $pais !== '' ? 'Envíos a ' . $pais : $txt;
} );

/* ══════════════════════════════════════════════════════════════════════════
 *  Las páginas que faltaban (2026-10-06)
 *
 *  23 páginas por ruta salían con el H1 y el <title> de Perú («Envío de libros
 *  al extranjero»), indexables y con canónica propia: no hablaban de su país y
 *  eran la misma página en las nueve rutas. Cada patrón está escrito para la
 *  intención de su página. Lo que la clienta edite en la copia manda.
 * ══════════════════════════════════════════════════════════════════════════ */

/* ¿La ruta actual tiene vía terrestre? (solo países vecinos). */
function grenvios_cab_terrestre() {
	$s = function_exists( 'grenvios_perfil_pais_actual' ) ? grenvios_perfil_pais_actual() : '';
	$d = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();
	return $s !== '' && ! empty( $d[ $s ]['terr'] );
}

/* Páginas de región: no tratan del país de la ruta. */
function grenvios_cab_regiones() {
	return array( 'envios-a-centroamerica-y-el-caribe', 'envios-a-europa', 'envios-a-norteamerica', 'envios-a-sudamerica' );
}

/* slug maestro => [ H1, title, description ]; %1$s = país. */
function grenvios_cab_mas() {
	$terr = grenvios_cab_terrestre();
	return apply_filters( 'grenvios_cabecera_mas', array(
		'carga-aerea-internacional'              => array( 'Carga aérea a <span>%1$s</span> desde Lima', 'Carga Aérea a %1$s desde Lima | Grenvíos', 'Envía carga por avión a %1$s desde Lima: qué admite la vía aérea, cómo se cotiza por peso o volumen y qué documentos necesitas.' ),
		'carga-terrestre-internacional'          => $terr
			? array( 'Carga terrestre a <span>%1$s</span> desde Lima', 'Carga Terrestre a %1$s desde Lima | Grenvíos', 'Carga por carretera de Lima a %1$s: qué admite la vía terrestre, plazos, impuesto pagado en Lima y cómo preparar el despacho.' )
			: array( 'Carga a <span>%1$s</span>: va por avión, no por carretera', 'Carga a %1$s: Solo por Vía Aérea | Grenvíos', 'A %1$s no hay ruta terrestre desde Lima: la carga viaja por avión. Qué cambia en plazos, contenido admitido y documentos.' ),
		'courier-internacional-en-lima'          => array( 'Courier en Lima para enviar a <span>%1$s</span>', 'Courier en Lima para Envíos a %1$s | Grenvíos', 'Courier en Lima para enviar documentos, paquetes y carga a %1$s: recojo, despacho, seguimiento y entrega en destino.' ),
		'cuanto-cuesta-enviar-un-paquete-al-extranjero' => array( 'Cómo se calcula el precio de un paquete a <span>%1$s</span>', 'Cómo se Calcula el Envío a %1$s | Grenvíos', 'De qué depende el precio de un paquete a %1$s: peso real o volumétrico, vía de envío y valor declarado. Te lo confirmamos al cotizar.' ),
		'embalaje-para-envios-internacionales'   => array( 'Cómo embalar un envío a <span>%1$s</span>', 'Embalaje para Envíos a %1$s: Guía | Grenvíos', 'Cómo embalar tu envío a %1$s para que llegue entero y no pague volumen de más: caja, relleno, frágiles y cierre.' ),
		'encomiendas-internacionales'            => array( 'Encomiendas a <span>%1$s</span> desde Lima', 'Encomiendas a %1$s desde Lima | Grenvíos', 'Envía encomiendas a %1$s desde Lima: qué puedes mandar, cómo se declara el contenido y cómo la recibe tu familia.' ),
		'envio-de-artesanias-al-extranjero'      => array( 'Envío de artesanías peruanas a <span>%1$s</span>', 'Enviar Artesanías Peruanas a %1$s | Grenvíos', 'Cómo enviar artesanías peruanas a %1$s: embalaje de piezas frágiles, declaración y qué revisa la aduana al llegar.' ),
		'envio-de-celulares-y-laptops'           => array( 'Envío de celulares y laptops a <span>%1$s</span>', 'Enviar Celulares y Laptops a %1$s | Grenvíos', 'Cómo enviar celulares y laptops a %1$s: baterías, vía de envío, valor declarado y lo que pide la aduana del destino.' ),
		'envio-de-correspondencia-internacional' => array( 'Envío de correspondencia a <span>%1$s</span> desde Lima', 'Correspondencia a %1$s desde Lima | Grenvíos', 'Envía cartas, tarjetas e invitaciones a %1$s desde Lima con seguimiento: qué va en un sobre y cómo llega.' ),
		'envio-de-libros-al-extranjero'          => array( 'Envío de libros a <span>%1$s</span>', 'Enviar Libros a %1$s desde Lima | Grenvíos', 'Cómo enviar libros a %1$s desde Lima: embalaje para que no se doblen, peso volumétrico y cómo se declaran.' ),
		'envio-de-medicinas-al-extranjero'       => array( 'Envío de medicinas a <span>%1$s</span> desde Perú', 'Enviar Medicinas a %1$s desde Perú | Grenvíos', 'Qué medicinas se pueden enviar a %1$s desde Perú: receta, envase original, cantidades de uso personal y declaración.' ),
		'envio-de-muestras-comerciales'          => array( 'Envío de muestras comerciales a <span>%1$s</span>', 'Muestras Comerciales a %1$s | Grenvíos', 'Envía muestras comerciales a %1$s: factura proforma, valor declarado y cómo evitar que la aduana las trate como venta.' ),
		'envio-de-regalos-al-extranjero'         => array( 'Envío de regalos a <span>%1$s</span> desde Lima', 'Enviar Regalos a %1$s desde Perú | Grenvíos', 'Envía regalos a %1$s desde Lima: qué se puede mandar, cómo embalarlo y con cuánta antelación para que llegue a tiempo.' ),
		'envio-de-repuestos-al-extranjero'       => array( 'Envío de repuestos a <span>%1$s</span>', 'Enviar Repuestos a %1$s desde Lima | Grenvíos', 'Cómo enviar repuestos a %1$s desde Lima: piezas con o sin batería, embalaje, factura y vía de envío según el tamaño.' ),
		'envio-de-ropa-al-extranjero'            => array( 'Envío de ropa a <span>%1$s</span>', 'Enviar Ropa a %1$s desde Lima | Grenvíos', 'Cómo enviar ropa y calzado a %1$s desde Lima: cómo declararla, cuánto ocupa la caja y qué revisa la aduana.' ),
		'envio-express-internacional'            => array( 'Envío express a <span>%1$s</span> desde Lima', 'Envío Express a %1$s desde Lima | Grenvíos', 'Envío express a %1$s desde Lima para lo que no puede esperar: por vía aérea, con seguimiento y plazo confirmado al cotizar.' ),
		'glosario-de-envios-internacionales'     => array( 'Glosario para enviar a <span>%1$s</span>', 'Glosario de Envíos a %1$s | Grenvíos', 'Las palabras que verás al enviar a %1$s: peso volumétrico, valor declarado, guía, aduana y entrega, explicadas en claro.' ),
		'mudanzas-internacionales'               => array( 'Mudanzas pequeñas a <span>%1$s</span>', 'Mudanza a %1$s desde Lima | Grenvíos', 'Mudanzas pequeñas a %1$s desde Lima: qué se puede mandar, cómo embalar las cajas y qué vía conviene según el volumen.' ),
		'traduccion-oficial-de-documentos'       => array( 'Traducción oficial de documentos para <span>%1$s</span>', 'Traducción Oficial para %1$s | Grenvíos', 'Traducción oficial de tus documentos para que tengan validez en %1$s, y envío del original apostillado desde Lima.' ),
	) );
}

/* H1 (páginas pintadas desde PHP y de plantilla) por el mapa de arriba. */
add_filter( 'grenvios_cabecera_titulos', function ( $mapa ) {
	foreach ( grenvios_cab_mas() as $slug => $t ) {
		if ( ! isset( $mapa[ $slug ] ) ) $mapa[ $slug ] = str_replace( '%1$s', '%s', $t[0] );
	}
	return $mapa;
} );

function grenvios_cab_seo_de( $slug ) {
	$pais = grenvios_cab_pais();
	$m    = grenvios_cab_mas();
	if ( $pais === '' || ! isset( $m[ $slug ] ) ) return null;
	$tok = function ( $t ) { return function_exists( 'grenvios_sede_tokens_apply' ) ? grenvios_sede_tokens_apply( $t ) : $t; };
	return array( $tok( sprintf( $m[ $slug ][1], $pais ) ), $tok( sprintf( $m[ $slug ][2], $pais ) ) );
}

/* <title> y description: por defecto… */
add_filter( 'grenvios_seo_defaults', function ( $base, $slug ) {
	$s = grenvios_cab_seo_de( $slug );
	return $s ? $s : $base;
}, 40, 2 );

/* …y también si la copia guardó el title de Perú tal cual (al duplicar se
 * copian los metadatos). Lo que la clienta haya cambiado en la copia manda. */
add_filter( 'grenvios_seo_guardado', function ( $par, $post_id, $slug ) {
	$s = grenvios_cab_seo_de( $slug );
	if ( ! $s || ! function_exists( 'grenvios_i18n_master_id' ) ) return $par;
	$m = (int) grenvios_i18n_master_id( $post_id );
	if ( ! $m || $m === (int) $post_id ) return $par;
	$igual = function ( $k ) use ( $post_id, $m ) {
		return (string) get_post_meta( $post_id, $k, true ) === (string) get_post_meta( $m, $k, true );
	};
	return array( $igual( 'grenvios_seo_title' ) ? $s[0] : $par[0], $igual( 'grenvios_seo_desc' ) ? $s[1] : $par[1] );
}, 10, 3 );

/* ── Páginas de región dentro de una ruta: copia de la de Perú ──
 * «Envíos a Europa» dentro de /ar/ no habla de Argentina: canónica a la página
 * de Perú y fuera del sitemap, como las demás copias (inc/seo-canibalizacion.php). */
function grenvios_cab_region_original( $post_id ) {
	if ( ! $post_id || ! function_exists( 'grenvios_i18n_master_id' ) || ! function_exists( 'grenvios_canonical_slug' ) ) return '';
	if ( ! in_array( grenvios_canonical_slug( $post_id ), grenvios_cab_regiones(), true ) ) return '';
	$m = (int) grenvios_i18n_master_id( $post_id );
	return ( $m && $m !== (int) $post_id ) ? (string) get_permalink( $m ) : '';
}
add_filter( 'grenvios_canonical', function ( $url ) {
	if ( ! is_singular( 'page' ) ) return $url;
	$o = grenvios_cab_region_original( (int) get_queried_object_id() );
	return $o !== '' ? $o : $url;
}, 31 );
add_filter( 'grenvios_sitemap_urls', function ( $urls ) {
	foreach ( $urls as $i => $u ) {
		if ( ! empty( $u['post_id'] ) && grenvios_cab_region_original( (int) $u['post_id'] ) !== '' ) unset( $urls[ $i ] );
	}
	return array_values( $urls );
}, 57 );
