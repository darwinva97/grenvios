<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Imágenes de ejemplo: fotos reales donde la plantilla dejaba rellenos grises
 * ══════════════════════════════════════════════════════════════════════════
 *
 * La plantilla trae «fotos» que son rectángulos grises con su medida escrita
 * («1920X857», «1000X650»): `post-*`, `content-bg-*`, `testimonial-bg`,
 * `delivery-man.png`, `team-*`. Salían en la portada (nosotros, destinos,
 * contadores, testimonios), en Nosotros y en las tarjetas del blog.
 *
 * Aquí se sustituyen por fotos de EJEMPLO (assets/img/ejemplo/, banco libre
 * Pexels, uso comercial sin atribución) elegidas por tema: embalaje, almacén,
 * documentos, entrega y una ciudad por país de destino.
 *
 * Son un punto de partida: en cuanto el cliente sube su foto en el panel
 * («Editar página») o en Personalizar → Imágenes, se usa la suya. Solo se
 * sustituye cuando el valor es un relleno de la plantilla, venga del valor por
 * defecto o de un meta guardado con el relleno.
 *
 * Skill: .claude/skills/grenvios-landing (banco de imágenes y cuándo usar cada una).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Banco de ejemplo: nombre → archivo. */
function grenvios_ej_banco() {
	return array(
		'embalaje', 'cinta', 'caja', 'almacen', 'almacen-pasillo', 'bodega', 'documentos', 'entrega', 'recojo', 'sobres', 'equipaje',
		'ecuador', 'colombia', 'chile', 'argentina', 'bolivia', 'estados-unidos', 'espana', 'venezuela', 'cuba',
	);
}

function grenvios_ej_img( $nombre ) {
	$nombre = sanitize_title( $nombre );
	if ( ! in_array( $nombre, grenvios_ej_banco(), true ) ) return '';
	return get_template_directory_uri() . '/assets/img/ejemplo/' . $nombre . '.webp';
}

/* ¿La URL es un relleno de la plantilla? */
function grenvios_ej_es_relleno( $url ) {
	if ( ! is_string( $url ) || $url === '' || strpos( $url, '/assets/img/' ) === false ) return false;
	return (bool) preg_match( '~/assets/img/(?:post-\d+|content-bg-\d+|page-banner|slider-bg|hero-background|testimonial-bg|banner-add|delivery-man|team-\d+)\.(?:jpe?g|png|webp)$~i', $url );
}

/* Foto de ejemplo para un tema libre (título de sección, categoría, slug). */
function grenvios_ej_por_tema( $texto, $respaldo = 'almacen', $tema_primero = false ) {
	$t = function_exists( 'remove_accents' ) ? strtolower( remove_accents( (string) $texto ) ) : strtolower( (string) $texto );
	$reglas = array(
		'ecuador' => 'ecuador', 'colombia' => 'colombia', 'chile' => 'chile', 'argentina' => 'argentina', 'bolivia' => 'bolivia',
		'estados unidos' => 'estados-unidos', 'estados-unidos' => 'estados-unidos', 'eeuu' => 'estados-unidos', 'miami' => 'estados-unidos',
		'espana' => 'espana', 'europa' => 'espana', 'venezuela' => 'venezuela', 'cuba' => 'cuba',
		'apostill' => 'documentos', 'traducc' => 'documentos', 'document' => 'documentos', 'titulo' => 'documentos', 'tramite' => 'documentos', 'legaliz' => 'documentos',
		'aduana' => 'documentos', 'impuesto' => 'documentos', 'declar' => 'documentos',
		'embal' => 'cinta', 'empac' => 'cinta', 'fragil' => 'cinta', 'caja' => 'caja',
		'volumetric' => 'caja', 'peso' => 'caja', 'medid' => 'caja', 'precio' => 'caja', 'cuesta' => 'caja', 'tarifa' => 'caja', 'cotiz' => 'caja',
		'recojo' => 'recojo', 'domicilio' => 'entrega', 'puerta' => 'entrega', 'entrega' => 'entrega',
		'regalo' => 'embalaje', 'encomienda' => 'embalaje', 'familia' => 'entrega', 'ropa' => 'embalaje', 'compras' => 'embalaje', 'alimento' => 'embalaje', 'medicin' => 'embalaje',
		'sobre' => 'sobres', 'correspondencia' => 'sobres', 'carta' => 'sobres',
		'empresa' => 'almacen', 'carga' => 'almacen', 'comercial' => 'almacen', 'export' => 'almacen', 'mayor' => 'almacen', 'negocio' => 'almacen',
		'seguimiento' => 'bodega', 'rastre' => 'bodega', 'plazo' => 'almacen-pasillo', 'tiempo' => 'almacen-pasillo', 'demora' => 'almacen-pasillo',
		'paquete' => 'embalaje', 'equipaje' => 'equipaje', 'maleta' => 'equipaje',
	);
	/* En las tarjetas de guías de un país todas nombran el país: ahí manda el
	 * tema (regalos, documentos, fechas…) y el país queda de respaldo. */
	if ( $tema_primero ) {
		$paises = array_slice( $reglas, 0, 13, true );
		$reglas = array_slice( $reglas, 13, null, true ) + $paises;
	}
	foreach ( $reglas as $k => $img ) {
		if ( strpos( $t, $k ) === false ) continue;
		/* Los temas genéricos (paquete, precio, embalaje…) alternan entre fotos
		 * afines para que tres tarjetas seguidas no salgan con la misma. */
		$afines = array(
			'embalaje' => array( 'embalaje', 'cinta', 'caja', 'entrega' ),
			'cinta'    => array( 'cinta', 'caja', 'embalaje' ),
			'caja'     => array( 'caja', 'cinta', 'bodega' ),
			'almacen'  => array( 'almacen', 'bodega', 'almacen-pasillo' ),
		);
		if ( isset( $afines[ $img ] ) ) {
			$img = $afines[ $img ][ abs( crc32( $t ) ) % count( $afines[ $img ] ) ];
		}
		return grenvios_ej_img( $img );
	}
	/* Sin tema reconocible: reparto estable entre las fotos generales. */
	$generales = array( 'embalaje', 'almacen', 'cinta', 'bodega', 'entrega', 'caja', 'recojo', 'almacen-pasillo', 'sobres' );
	$respaldo  = $generales[ abs( crc32( $t . $respaldo ) ) % count( $generales ) ];
	return grenvios_ej_img( $respaldo );
}

/* Miniatura de una guía: su imagen destacada o una foto de ejemplo por tema. */
function grenvios_ej_miniatura( $post ) {
	$post = get_post( $post );
	if ( ! $post ) return '';
	$img = has_post_thumbnail( $post )
		? get_the_post_thumbnail( $post, 'medium_large', array( 'loading' => 'lazy' ) )
		: '<img src="' . esc_url( grenvios_ej_por_tema( $post->post_title, 'embalaje', true ) ) . '" alt="" loading="lazy" decoding="async">';
	return '<a href="' . esc_url( get_permalink( $post ) ) . '" class="grenvios-guide-img" tabindex="-1" aria-hidden="true">' . $img . '</a>';
}

/* Campos de imagen cuyo valor es un relleno → foto de ejemplo. */
add_filter( 'grenvios_campo_valor', function ( $valor, $key, $default ) {
	/* Llamada final de la portada (.gr-hcta): la silueta gris «delivery-men-2»
	 * salía a 115 % de alto en la portada de las rutas. Foto real del
	 * repartidor. Solo esta clave: en los demás CTA esa silueta se retira entera
	 * (inc/diseno-tarjetas.php). */
	if ( $key === 'home_cta_men' && is_string( $valor ) && strpos( $valor, 'delivery-men' ) !== false ) {
		return get_template_directory_uri() . '/assets/img/cta-repartidor-4.webp';
	}
	if ( ! grenvios_ej_es_relleno( $valor ) ) return $valor;

	/* Carrusel de destinos de la portada: la ciudad del país que nombra la tarjeta. */
	if ( preg_match( '/^home_dest(\d)_img$/', $key, $m ) ) {
		$orden  = array( 1 => 'Ecuador', 2 => 'Colombia', 3 => 'Chile', 4 => 'Estados Unidos', 5 => 'España' );
		$nombre = grenvios_field_crudo( 'home_dest' . $m[1] . '_name', isset( $orden[ (int) $m[1] ] ) ? $orden[ (int) $m[1] ] : '' );
		/* Primero, la imagen destacada de la ficha del país. */
		$fd = function_exists( 'grenvios_imagen_de_ruta' ) ? grenvios_imagen_de_ruta( 'destinos/' . sanitize_title( remove_accents( $nombre ) ) ) : '';
		if ( $fd !== '' ) return $fd;
		$u      = grenvios_ej_img( $nombre );
		return $u !== '' ? $u : grenvios_ej_por_tema( $nombre, 'almacen' );
	}

	$tpl  = get_template_directory_uri() . '/assets/img/';
	$mapa = array(
		'home_about_img1'  => grenvios_ej_img( 'almacen' ),
		'home_about_img2'  => grenvios_ej_img( 'cinta' ),
		'home_about_img3'  => '',                                   // silueta recortada: se retira (ver filtro de abajo)
		'home_dest_bg'     => grenvios_ej_img( 'almacen-pasillo' ),
		'home_feat_bg'     => $tpl . 'destino-hero.jpg',
		'home_feat_img1'   => grenvios_ej_img( 'embalaje' ),
		'home_feat_img2'   => grenvios_ej_img( 'bodega' ),
		'home_testi_cargo' => '',
		'nos_img_1'        => grenvios_ej_img( 'almacen' ),
		'nos_img_2'        => grenvios_ej_img( 'cinta' ),
		'nos_img_3'        => '',
		'serv_img_1'       => grenvios_ej_img( 'documentos' ),
		'serv_img_2'       => grenvios_ej_img( 'embalaje' ),
		'serv_img_3'       => grenvios_ej_img( 'almacen' ),
		'serv_img_4'       => grenvios_ej_img( 'sobres' ),
		'doc_img_1'        => grenvios_ej_img( 'documentos' ),
		'paq_img_1'        => grenvios_ej_img( 'embalaje' ),
		'paq_img_2'        => grenvios_ej_img( 'cinta' ),
		'paq_img_3'        => grenvios_ej_img( 'entrega' ),
		'apos_img_1'       => grenvios_ej_img( 'documentos' ),
	);
	return array_key_exists( $key, $mapa ) ? $mapa[ $key ] : $valor;
}, 20, 3 );

/* Rellenos que no son campos (o cuyo campo quedó vacío): se limpian en el HTML. */
add_filter( 'grenvios_html_final', function ( $html ) {
	if ( is_admin() || ! is_string( $html ) || $html === '' ) return $html;
	$original = $html;

	/* Imagen con src vacío (figura recortada retirada) y fondo vacío. */
	if ( strpos( $html, 'src=""' ) !== false ) {
		$html = preg_replace( '~<img\b[^>]*\ssrc=""[^>]*>~i', '', $html );
	}
	if ( strpos( $html, 'background-image:url()' ) !== false ) {
		$html = str_replace( array( ' style="background-image:url()"', 'style="background-image:url()"' ), '', $html );
	}

	/* Avatares grises de la prueba social del hero: icono sobre círculo de marca. */
	if ( strpos( $html, '/assets/img/team-' ) !== false ) {
		$ini  = array( 'ML', 'CR', 'JA', 'PV' );
		$n    = 0;
		$html = preg_replace_callback( '~<img\b[^>]*\ssrc="[^"]*/assets/img/team-\d+\.jpg"[^>]*>~i', function () use ( &$n, $ini ) {
			return '<span class="gr-ej-avatar" aria-hidden="true"><i class="fa-solid fa-user"></i></span>';
		}, $html );
	}

	/* Tarjetas del blog sin imagen destacada: foto de ejemplo según el título. */
	if ( strpos( $html, '/assets/img/post-' ) !== false ) {
		$html = preg_replace_callback( '~<img\b([^>]*)\ssrc="[^"]*/assets/img/post-\d+\.jpg"([^>]*)>~i', function ( $m ) {
			$alt = preg_match( '~\salt="([^"]*)"~', $m[0], $a ) ? html_entity_decode( $a[1], ENT_QUOTES, 'UTF-8' ) : '';
			return '<img' . $m[1] . ' src="' . esc_url( grenvios_ej_por_tema( $alt, 'embalaje' ) ) . '"' . $m[2] . '>';
		}, $html );
	}

	return is_string( $html ) && $html !== '' ? $html : $original;
}, 24 );
