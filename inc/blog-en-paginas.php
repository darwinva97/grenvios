<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  El blog, dentro de las páginas que venden
 * ══════════════════════════════════════════════════════════════════════════
 *
 * EL PROBLEMA MEDIDO
 * Con 302 entradas publicadas, la portada tenía CERO enlaces al blog. Y no solo
 * la portada: /servicios/, /destinos/, /contacto/, /cotizar/ y una docena más
 * tampoco. El bloque «Guías que te pueden ayudar» (inc/seo-clusters.php) solo
 * aparece si alguna entrada declara esa página como destino de su cluster, y
 * hay páginas que ninguna guía apunta. Resultado: un blog entero que no
 * repartía autoridad hacia arriba, y páginas comerciales que no daban al lector
 * ningún sitio al que seguir leyendo.
 *
 * QUÉ HACE ESTE MÓDULO
 * Da a CADA página un bloque de entradas escogidas por tema, usando las
 * etiquetas de inc/blog-etiquetas.php:
 *
 *   · Se eligen por etiqueta, no por fecha: en /tiempos-de-entrega/ salen las
 *     guías de plazos, no la última que se publicó.
 *   · Se excluyen las que ya pintó el bloque de cluster, para no repetir.
 *   · Se respeta la ruta: en /cl/ salen las entradas de Chile, que es de lo que
 *     va esa ruta entera. Es contenido distinto en cada una de las diez.
 *   · Si a una página no le corresponde ningún tema (nosotros, contacto), sale
 *     lo más reciente de SU ruta, que sigue siendo mejor que nada.
 *
 * Título e introducción son editables por página, como todo lo demás.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ─────────────────────────────────────────────────────────────────────────
 * 1) Qué temas le tocan a cada página
 *    Las claves son las de grenvios_etq_def(); 'recientes' = lo último de la
 *    ruta, para las páginas que no tratan de un tema concreto.
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_bep_temas() {
	return apply_filters( 'grenvios_bep_temas', array(
		'home'                                 => array( 'recientes' ),
		'servicios'                            => array( 'envio-de-paquetes', 'apostilla-y-documentos', 'equipaje-y-mudanzas' ),
		'envio-internacional-de-paquetes'      => array( 'envio-de-paquetes', 'embalaje', 'aerea-o-terrestre' ),
		'envio-internacional-de-documentos'    => array( 'enviar-documentos-a-', 'apostillar-documentos-para-', 'como-escribir-una-direccion-en-' ),
		'carga-internacional'                  => array( 'enviar-mercaderia-a-', 'enviar-repuestos-a-', 'vender-productos-peruanos-en-' ),
		'envio-de-equipaje'                    => array( 'mudarse-a-', 'estudiar-en-', 'que-caja-usar-para-enviar-a-' ),
		'envio-de-compras'                     => array( 'compras-en-peru-para-', 'enviar-celulares-y-laptops-a-', 'enviar-ropa-y-calzado-a-' ),
		'envio-de-alimentos'                   => array( 'enviar-superalimentos-a-', 'medicinas-alimentos-y-baterias-a-', 'productos-peruanos-a-' ),
		'apostilla-y-traduccion'               => array( 'apostillar-documentos-para-', 'enviar-documentos-a-', 'estudiar-en-' ),
		'peso-volumetrico'                     => array( 'que-caja-usar-para-enviar-a-', 'cuanto-cuesta-enviar-a-', 'via-de-envio-a-' ),
		'tiempos-de-entrega'                   => array( 'plazos-de-entrega', 'fechas-clave' ),
		'que-se-puede-enviar'                  => array( 'medicinas-alimentos-y-baterias-a-', 'enviar-perfumes-y-cosmeticos-a-', 'que-se-puede-enviar-a-' ),
		'aduanas-e-impuestos'                  => array( 'aduanas', 'productos-prohibidos' ),
		'seguro-de-envios'                     => array( 'valor-declarado-envio-a-', 'enviar-joyas-y-plata-a-', 'cuanto-cuesta-enviar-a-' ),
		'cotizar'                              => array( 'precios-de-envio', 'peso-volumetrico' ),
		'rastreo-de-envios'                    => array( 'seguimiento-de-un-envio-a-', 'cuanto-demora-un-envio-a-', 'avisar-a-tu-destinatario-en-' ),
		'como-enviar-un-paquete-al-extranjero' => array( 'envio-de-paquetes', 'embalaje' ),
		'recojo-a-domicilio-lima'              => array( 'envio-de-paquetes', 'embalaje' ),
		'envios-desde-provincias'              => array( 'envio-de-paquetes', 'plazos-de-entrega' ),
		'envios-para-empresas'                 => array( 'envios-para-empresas', 'aduanas' ),
		'destinos'                             => array( 'ciudades-de-destino', 'plazos-de-entrega' ),
		'nosotros'                             => array( 'recientes' ),
		'contacto'                             => array( 'avisar-a-tu-destinatario-en-', 'como-escribir-una-direccion-en-', 'pedir-productos-de-peru-desde-' ),
		'preguntas-frecuentes'                 => array( 'recientes' ),
	) );
}

/* Textos del bloque. Uno por página sería inmantenible y repetitivo; se escribe
 * por tipo de página y se puede reescribir desde el editor. */
function grenvios_bep_textos( $slug ) {
	$dest = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();

	if ( isset( $dest[ $slug ] ) ) {
		$pais = trim( preg_replace( '/^env[ií]os?\s+a\s+/iu', '', (string) $dest[ $slug ]['title'] ) );
		return array(
			'Guías para enviar a ' . $pais,
			'Lo que conviene leer antes de despachar a este destino: precios, plazos, qué admite su aduana y a qué ciudades se entrega a domicilio.',
		);
	}

	if ( $slug === 'home' ) {
		return array(
			'Aprende a enviar antes de enviar',
			'Publicamos guías con lo que de verdad se pregunta antes de un envío internacional: cuánto cuesta, cuánto demora, qué admite cada aduana y cómo embalar para no pagar por aire. Todas están escritas desde nuestra propia operación, no copiadas de un manual.',
		);
	}

	return array(
		'Aprende antes de enviar',
		'Guías claras para decidir con más información.',
	);
}

/* ─────────────────────────────────────────────────────────────────────────
 * 2) Campos editables
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_bep_campos( $slug ) {
	list( $tit, $intro ) = grenvios_bep_textos( $slug );
	return array(
		'bep_sub'   => array( 'Blog · Antetítulo', 'text', $slug === 'home' ? 'Guías prácticas' : 'Guías para enviar mejor' ),
		'bep_title' => array( 'Blog · Título', 'text', $tit ),
		'bep_intro' => array( 'Blog · Introducción', 'textarea', $intro ),
		'bep_badge' => array( 'Blog · Distintivo de la guía destacada', 'text', 'Guía destacada' ),
		'bep_leer'  => array( 'Blog · Texto del enlace de cada guía', 'text', 'Leer guía' ),
		'bep_todas' => array( 'Blog · Texto del botón superior (3 guías)', 'text', 'Ver todas las guías' ),
	);
}

add_filter( 'grenvios_text_registry', function ( $reg ) {
	$temas = grenvios_bep_temas();
	$dest  = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();
	foreach ( array_keys( $reg ) as $slug ) {
		if ( ! isset( $temas[ $slug ] ) && ! isset( $dest[ $slug ] ) ) continue;
		$reg[ $slug ]['sections']['blog'] = array(
			'label'           => 'Bloque del blog',
			'_no_token_check' => true,
			'sel'             => '.gr-bep',
			'fields'          => grenvios_bep_campos( $slug ),
		);
	}
	return $reg;
} );

/* ─────────────────────────────────────────────────────────────────────────
 * 3) Selección de entradas
 * ───────────────────────────────────────────────────────────────────────── */

/* term_id de una etiqueta en la ruta activa, a partir de su clave. */
function grenvios_bep_tag_id( $clave, $lang ) {
	if ( ! function_exists( 'grenvios_etq_term' ) ) return 0;
	$def  = function_exists( 'grenvios_i18n_default' ) ? grenvios_i18n_default() : '';
	$slug = ( $lang === '' || $lang === $def ) ? $clave : $clave . '-' . grenvios_etq_sufijo( $lang );
	return grenvios_etq_term( $slug, $lang );
}

function grenvios_bep_posts( $slug, $limite = 3, $excluir = array() ) {
	$lang  = function_exists( 'grenvios_i18n_current' ) ? grenvios_i18n_current() : '';
	$temas = grenvios_bep_temas();
	$dest  = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();

	$claves = array();
	if ( isset( $temas[ $slug ] ) ) {
		$claves = $temas[ $slug ];
	} elseif ( isset( $dest[ $slug ] ) ) {
		/* Ficha de destino: su etiqueta de país en la ruta principal. En una
		 * ruta de país esa etiqueta no existe —allí TODAS las entradas son de
		 * ese país— y se cae al respaldo, que es exactamente lo que se quiere. */
		$claves = array( 'envios-a-' . $slug, 'recientes' );
	} else {
		$claves = array( 'recientes' );
	}

	$args = array(
		'post_type'        => 'post',
		'post_status'      => 'publish',
		'numberposts'      => (int) $limite,
		'post__not_in'     => array_map( 'intval', $excluir ),
		'ignore_sticky_posts' => true,
	);
	if ( $lang !== '' ) $args['lang'] = $lang;

	$ids = array();
	foreach ( $claves as $clave ) {
		if ( $clave === 'recientes' ) continue;
		$tid = grenvios_bep_tag_id( $clave, $lang );
		if ( $tid ) $ids[] = $tid;
	}

	$out = array();
	if ( $ids ) {
		$a = $args;
		$a['tax_query'] = array( array( 'taxonomy' => 'post_tag', 'field' => 'term_id', 'terms' => $ids ) );
		if ( isset( $dest[ $slug ] ) ) {
			$out = get_posts( $a );
		} else {
			/* Página general: primero las guías generales. Por fecha salían las
			 * últimas importadas —tres de Cuba seguidas en /servicios/—, que no
			 * responden a quien está mirando los servicios en general. Las de
			 * un país quedan de reserva, y se mantiene la variedad por tema. */
			$a['numberposts'] = (int) $limite * 8;
			$general = array(); $de_pais = array();
			foreach ( get_posts( $a ) as $p ) {
				$es_pais = false;
				$tg = get_the_tags( $p->ID );
				if ( $tg && ! is_wp_error( $tg ) ) foreach ( $tg as $t ) if ( strpos( $t->slug, 'envios-a-' ) !== false ) { $es_pais = true; break; }   // también «guias-envios-a-»
				if ( $es_pais ) $de_pais[] = $p; else $general[] = $p;
			}
			$out = grenvios_bep_variar( $general, $limite );
			if ( count( $out ) < $limite ) $out = array_merge( $out, grenvios_bep_variar( $de_pais, $limite - count( $out ) ) );
		}
	}

	/* En una ruta de país, primero las guías de ESE país: son las únicas del
	 * blog de la ruta que se indexan (las generales copiadas declaran su
	 * canónica en la ruta principal) y las que hacen la página distinta de la
	 * misma página en otra ruta. Se eligen por el tema de la página. */
	$def_l = function_exists( 'grenvios_i18n_default' ) ? grenvios_i18n_default() : '';
	if ( $lang !== '' && $lang !== $def_l && ! isset( $dest[ $slug ] ) && function_exists( 'grenvios_bep_locales' ) ) {
		$loc = grenvios_bep_locales( $slug, $lang, $limite, $excluir );
		if ( $loc ) {
			$ids_loc = wp_list_pluck( $loc, 'ID' );
			$resto   = array_filter( $out, function ( $p ) use ( $ids_loc ) { return ! in_array( $p->ID, $ids_loc, true ); } );
			$out     = array_slice( array_merge( $loc, array_values( $resto ) ), 0, (int) $limite );
		}
	}

	/* Respaldo: completar con lo más reciente de la ruta hasta el límite. Una
	 * sección a medias se ve peor que una sección con una guía general.
	 *
	 * Se pide de más y se filtra por variedad: las entradas se importaron país
	 * por país, así que «lo más reciente» eran seis guías del MISMO país —en la
	 * portada salían seis de Cuba seguidas—. Una por tema da una portada que se
	 * lee como un blog y no como un archivo. */
	if ( count( $out ) < $limite ) {
		$faltan = $limite - count( $out );
		$a = $args;
		$a['numberposts']  = $faltan * 6;
		$a['post__not_in'] = array_merge( $a['post__not_in'], wp_list_pluck( $out, 'ID' ) );
		$out = array_merge( $out, grenvios_bep_variar( get_posts( $a ), $faltan ) );
	}

	return $out;
}

/* Una entrada por tema (su primera etiqueta) mientras alcance; si faltan, se
 * completa con el resto en orden. */
function grenvios_bep_variar( $posts, $limite ) {
	/* Variedad en los DOS ejes: ni dos guías del mismo país ni dos del mismo
	 * tema. Con uno solo se colaba «Mudarse a Cuba / a Venezuela / a España…»,
	 * que es tan monótono como seis guías del mismo país. Lo que no pasa el
	 * filtro queda de reserva para completar el hueco si falta. */
	$paises = array();
	$temas  = array();
	$prim   = array();
	$resto  = array();

	foreach ( $posts as $p ) {
		$tags = get_the_tags( $p->ID );
		$tags = ( ! empty( $tags ) && ! is_wp_error( $tags ) ) ? $tags : array();

		$pais = 0;
		$tema = 0;
		foreach ( $tags as $tg ) {
			if ( strpos( $tg->slug, 'envios-a-' ) !== false ) {
				if ( ! $pais ) $pais = (int) $tg->term_id;
			} elseif ( ! $tema ) {
				$tema = (int) $tg->term_id;
			}
		}

		if ( ( $pais && isset( $paises[ $pais ] ) ) || ( $tema && isset( $temas[ $tema ] ) ) ) {
			$resto[] = $p;
			continue;
		}
		if ( $pais ) $paises[ $pais ] = true;
		if ( $tema ) $temas[ $tema ]  = true;
		$prim[] = $p;
	}

	$out = array_slice( $prim, 0, $limite );
	if ( count( $out ) < $limite ) {
		$out = array_merge( $out, array_slice( $resto, 0, $limite - count( $out ) ) );
	}
	return $out;
}


/* ─────────────────────────────────────────────────────────────────────────
 * 4) Render
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_bep_render( $slug, $limite = 3 ) {
	if ( is_admin() ) return;

	/* Lo que ya pintó el bloque de cluster no se repite. */
	$ya = array();
	if ( function_exists( 'grenvios_cluster_posts' ) && ( is_page() || is_front_page() ) ) {
		foreach ( grenvios_cluster_posts( (int) get_queried_object_id() ) as $p ) $ya[] = (int) $p->ID;
	}

	$posts = grenvios_bep_posts( $slug, $limite, $ya );
	if ( empty( $posts ) ) return;

	$c     = grenvios_bep_campos( $slug );
	$tit   = grenvios_field( 'bep_title', $c['bep_title'][2] );
	$intro = grenvios_field( 'bep_intro', $c['bep_intro'][2] );
	$t     = function ( $s ) { return function_exists( 'grenvios_t' ) ? grenvios_t( $s ) : $s; };

	$blog_id  = (int) get_option( 'page_for_posts' );
	if ( $blog_id && function_exists( 'pll_get_post' ) && function_exists( 'grenvios_i18n_current' ) ) {
		$tr = (int) pll_get_post( $blog_id, grenvios_i18n_current() );
		if ( $tr ) $blog_id = $tr;
	}

	/* Diseño de la maqueta del cliente (2026-10-02, skill grenvios-landing):
	 * cabecera a la izquierda con antetítulo; con cinco o más guías, la primera
	 * sale destacada (foto grande, distintivo) y el resto en 2 + 3; con menos,
	 * tres por fila. Cierre en franja rosada con los temas y el botón. */
	$sub   = grenvios_field( 'bep_sub',   $c['bep_sub'][2] );
	$badge = grenvios_field( 'bep_badge', $c['bep_badge'][2] );
	$leer  = grenvios_field( 'bep_leer',  $c['bep_leer'][2] );
	$destacar = count( $posts ) >= 5;

	echo '<section class="gr-bep gr-bep--v2' . ( $destacar ? ' gr-bep--destacada' : ' gr-bep--compacta' ) . ' padding-bottom"><div class="container">';
	echo '<div class="gr-bep-top"><div class="section-heading mb-30">'
		. ( trim( (string) $sub ) !== '' ? '<p class="sub-heading">' . esc_html( $sub ) . '</p>' : '' )
		. '<h2>' . esc_html( $tit ) . '</h2>';
	if ( trim( (string) $intro ) !== '' ) echo '<p>' . wp_kses_post( $intro ) . '</p>';
	echo '</div>';
	/* Versión compacta: el botón «Ver todas las guías» sube a la cabecera. */
	if ( ! $destacar && $blog_id && get_post_status( $blog_id ) === 'publish' ) {
		echo '<a class="gr-bep-todas-top" href="' . esc_url( get_permalink( $blog_id ) ) . '">' . esc_html( $t( $c['bep_todas'][2] ) ) . ' <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>';
	}
	echo '</div><div class="row gr-bep-grid">';

	$i = 0;
	foreach ( $posts as $p ) {
		$i++;
		$url  = get_permalink( $p );
		$es_d = $destacar && $i === 1;
		$col  = $destacar ? ( $es_d ? 'gr-bep-col gr-bep-col--grande' : ( $i <= 3 ? 'gr-bep-col gr-bep-col--media' : 'gr-bep-col gr-bep-col--tercio' ) ) : 'col-lg-4 col-md-6';
		echo '<div class="' . $col . '"><article class="grenvios-guide-card gr-bep-card' . ( $es_d ? ' gr-bep-card--grande' : '' ) . '">';
		echo function_exists( 'grenvios_ej_miniatura' ) ? grenvios_ej_miniatura( $p ) : '';
		if ( ! $destacar ) {
			/* Tiempo de lectura a partir de las palabras reales de la guía (200 por minuto). */
			$mins = max( 1, (int) ceil( str_word_count( wp_strip_all_tags( (string) $p->post_content ) ) / 200 ) );
			echo '<span class="gr-bep-min"><i class="fa-regular fa-book-open" aria-hidden="true"></i> ' . $mins . ' min</span>';
		}
		if ( $es_d && trim( (string) $badge ) !== '' ) {
			echo '<span class="gr-bep-badge"><i class="fa-solid fa-star" aria-hidden="true"></i> ' . esc_html( $badge ) . '</span>';
		}
		echo '<div class="gr-bep-cuerpo">';
		$tags = get_the_tags( $p->ID );
		if ( ! empty( $tags ) && ! is_wp_error( $tags ) ) {
			$tg = $tags[0];
			echo '<a class="gr-bep-tag" href="' . esc_url( get_term_link( $tg ) ) . '">' . esc_html( $tg->name ) . '</a>';
		}
		echo '<h3><a href="' . esc_url( $url ) . '">' . esc_html( get_the_title( $p ) ) . '</a></h3>';
		$ex = wp_strip_all_tags( get_the_excerpt( $p ) );
		if ( $ex !== '' ) echo '<p>' . esc_html( wp_trim_words( $ex, $es_d ? 26 : 20 ) ) . '</p>';
		echo '<p class="gr-bep-leer"><a href="' . esc_url( $url ) . '">' . esc_html( $leer ) . ' <i class="fa-regular fa-arrow-right" aria-hidden="true"></i></a></p>';
		echo '</div></article></div>';
	}

	echo '</div>';

	/* Cierre: temas del blog (solo en la portada) + botón a todas las guías. */
	$temas_html = '';
	if ( $slug === 'home' ) {
		$temas = get_terms( array(
			'taxonomy'   => 'post_tag',
			'hide_empty' => true,
			'orderby'    => 'count',
			'order'      => 'DESC',
			'number'     => 10,
			'lang'       => function_exists( 'grenvios_i18n_current' ) ? grenvios_i18n_current() : '',
		) );
		if ( ! empty( $temas ) && ! is_wp_error( $temas ) ) {
			$links = array();
			foreach ( $temas as $tg ) {
				$links[] = '<li><a href="' . esc_url( get_term_link( $tg ) ) . '">' . esc_html( $tg->name ) . '</a></li>';
			}
			$temas_html = '<p class="gr-bep-temas"><span>' . esc_html( $t( 'Por temas:' ) ) . '</span></p><ul class="gr-bep-temas-lista">' . implode( '', $links ) . '</ul>';
		}
	}
	$boton = ( $destacar && $blog_id && get_post_status( $blog_id ) === 'publish' )
		? '<p class="gr-bep-todas"><a class="default-btn" href="' . esc_url( get_permalink( $blog_id ) ) . '">' . esc_html( $t( 'Ver todas las guías del blog' ) ) . ' <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></p>'
		: '';
	if ( $temas_html !== '' || $boton !== '' ) {
		echo '<div class="gr-bep-pie">' . $temas_html . $boton . '</div>';
	}
	echo '</div></section>';
}

add_action( 'wp_head', function () {
	if ( is_admin() ) return;
	echo '<style id="grenvios-bep-css">'
		. '.gr-bep{padding-top:10px}'
		. '.gr-bep .section-heading p{max-width:70ch}'
		. '.gr-bep-card{margin-bottom:26px}'
		. '.gr-bep-tag{display:inline-block;font-size:13px;opacity:.75;border-bottom:1px solid currentColor}'
		. '.gr-bep-temas{margin:4px 0 10px;font-size:14px;opacity:.9}'
		. '.gr-bep-temas span{font-weight:600}'
		. '.gr-bep-temas a{border-bottom:1px solid rgba(0,0,0,.18)}'
		. '.gr-bep-todas{margin:6px 0 0;font-weight:600}'
		. '</style>';
}, 105 );

add_filter( 'grenvios_i18n_extra_strings', function ( $textos ) {
	$textos[] = 'Ver todas las guías del blog';
	$textos[] = 'Por temas:';
	return $textos;
} );

/* Guías propias del país de la ruta (meta grenvios_solo_pais), ordenadas por
 * afinidad con el tema de la página: prefijos de su clave de guía. */
function grenvios_bep_locales( $slug, $lang, $limite, $excluir = array() ) {
	$temas = array(
		'aduanas-e-impuestos'  => array( 'que-se-puede-enviar-a-', 'valor-declarado-envio-a-', 'medicinas-alimentos-y-baterias-a-', 'cuanto-cuesta-enviar-a-' ),
		'que-se-puede-enviar'  => array( 'medicinas-alimentos-y-baterias-a-', 'que-se-puede-enviar-a-', 'productos-peruanos-a-' ),
		'envio-de-alimentos'   => array( 'medicinas-alimentos-y-baterias-a-', 'productos-peruanos-a-', 'que-se-puede-enviar-a-' ),
		'envio-de-compras'     => array( 'compras-en-peru-para-', 'productos-peruanos-a-', 'cuanto-cuesta-enviar-a-', 'que-se-puede-enviar-a-' ),
		'envio-de-equipaje'    => array( 'mudarse-a-', 'cuanto-cuesta-enviar-a-', 'como-escribir-una-direccion-en-' ),
		'envio-internacional-de-documentos' => array( 'enviar-documentos-a-', 'como-escribir-una-direccion-en-', 'cuanto-demora-un-envio-a-' ),
		'apostilla-y-traduccion' => array( 'enviar-documentos-a-', 'como-escribir-una-direccion-en-', 'que-se-puede-enviar-a-' ),
		'tiempos-de-entrega'   => array( 'cuanto-demora-un-envio-a-', 'enviar-a-', 'ciudades-de-' ),
		'rastreo-de-envios'    => array( 'cuanto-demora-un-envio-a-', 'como-escribir-una-direccion-en-', 'ciudades-de-' ),
		'cotizar'              => array( 'cuanto-cuesta-enviar-a-', 'como-enviar-un-paquete-a-', 'que-se-puede-enviar-a-' ),
		'peso-volumetrico'     => array( 'cuanto-cuesta-enviar-a-', 'como-enviar-un-paquete-a-', 'productos-peruanos-a-' ),
		'seguro-de-envios'     => array( 'valor-declarado-envio-a-', 'cuanto-cuesta-enviar-a-', 'que-se-puede-enviar-a-', 'como-enviar-un-paquete-a-' ),
		'contacto'             => array( 'como-escribir-una-direccion-en-', 'como-enviar-un-paquete-a-', 'ciudades-de-' ),
		'carga-internacional'  => array( 'enviar-mercaderia-a-', 'vender-productos-peruanos-en-', 'via-de-envio-a-' ),
		'envios-para-empresas' => array( 'vender-productos-peruanos-en-', 'enviar-mercaderia-a-', 'valor-declarado-envio-a-' ),
		'servicios'            => array( 'via-de-envio-a-', 'enviar-mercaderia-a-', 'compras-en-peru-para-' ),
		'nosotros'             => array( 'productos-peruanos-a-', 'enviar-encomiendas-a-familiares-en-', 'ciudades-de-' ),
	);
	$temas['envio-internacional-de-paquetes'] = array( 'enviar-ropa-y-calzado-a-', 'enviar-artesania-a-', 'enviar-regalos-de-cumpleanos-a-' );
	$temas['como-enviar-un-paquete-al-extranjero'] = array( 'como-enviar-un-paquete-a-', 'enviar-utiles-escolares-a-', 'que-caja-usar-para-enviar-a-' );
	$pref = isset( $temas[ $slug ] ) ? $temas[ $slug ] : array( 'como-enviar-un-paquete-a-', 'como-escribir-una-direccion-en-', 'productos-peruanos-a-' );
	$posts = get_posts( array(
		'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => 30, 'lang' => $lang,
		'meta_key' => 'grenvios_solo_pais', 'post__not_in' => array_map( 'intval', (array) $excluir ),
	) );
	if ( ! $posts ) return array();
	$rank = function ( $p ) use ( $pref ) {
		$k = (string) get_post_meta( $p->ID, 'grenvios_guia_key', true );
		foreach ( $pref as $i => $x ) if ( strpos( $k, $x ) === 0 ) return $i;
		return 99;
	};
	usort( $posts, function ( $a, $b ) use ( $rank ) { return $rank( $a ) - $rank( $b ); } );
	return array_slice( $posts, 0, (int) $limite );
}
