<?php
/**
 * Grenvíos — RUTAS POR PAÍS sobre Polylang.
 *
 * Polylang se usa aquí como duplicador de contenido, no como traductor: cada
 * país de destino es una «lengua» de Polylang y por tanto tiene su prefijo de
 * ruta, su copia de cada página, su copia de cada entrada del blog y su propio
 * menú. Lo que se gana es que al crear un país se duplica el sitio entero y
 * cada versión se puede reescribir para ese destino («somos mejores en Brasil»).
 *
 * PERO NO SON TRADUCCIONES, Y ESO CAMBIA EL SEO
 * `/` y `/br/` no son la misma página en dos idiomas: son páginas distintas
 * sobre destinos distintos, las dos en español y las dos para el mismo público
 * peruano. Si se declaran como alternativas con `hreflang`, Google entiende que
 * son la misma cosa, elige una y descarta las demás — justo las páginas que se
 * crearon para posicionar por separado. Por eso aquí:
 *
 *   · las rutas de país NO se declaran entre sí con hreflang
 *   · su <html lang> y su og:locale son los del idioma real (es-PE), no es-BR
 *   · cada página es canónica de sí misma
 *
 * Si algún día se añade un idioma de verdad (inglés), ese sí entra en el
 * hreflang. La distinción se hace sola comparando el código de idioma del
 * locale: es_PE y es_BR comparten «es» → son rutas de país; es_PE y en_US no →
 * son idiomas.
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ══════════════════════════════════════
   1) ¿RUTA DE PAÍS O IDIOMA DE VERDAD?
══════════════════════════════════════ */

/* Código ISO de idioma de un locale: es_PE -> es. */
function grenvios_ruta_iso( $lang ) {
	$loc = function_exists( 'grenvios_i18n_locale' ) ? grenvios_i18n_locale( $lang ) : (string) $lang;
	return strtolower( substr( str_replace( '-', '_', (string) $loc ), 0, 2 ) );
}

/* 'idioma' si habla otra lengua; 'pais' si es el mismo idioma que la maestra.
 *
 * Filtro `grenvios_ruta_tipo`: para forzar el tipo de una ruta concreta, por
 * ejemplo si Brasil se hiciera de verdad en portugués. */
function grenvios_ruta_tipo( $lang ) {
	$master = function_exists( 'grenvios_i18n_default' ) ? grenvios_i18n_default() : '';
	$tipo   = ( $lang === $master || grenvios_ruta_iso( $lang ) === grenvios_ruta_iso( $master ) )
		? 'pais'
		: 'idioma';
	return apply_filters( 'grenvios_ruta_tipo', $tipo, $lang );
}

function grenvios_es_ruta_pais( $lang ) {
	$master = function_exists( 'grenvios_i18n_default' ) ? grenvios_i18n_default() : '';
	return $lang !== $master && grenvios_ruta_tipo( $lang ) === 'pais';
}

/* ══════════════════════════════════════
   2) FUERA DEL HREFLANG
   Solo se declaran entre sí los idiomas de verdad. Con únicamente rutas de
   país no queda nada que declarar y el bloque entero no se imprime, que es lo
   correcto: no hay versiones alternativas que ofrecer.
══════════════════════════════════════ */
add_filter( 'grenvios_i18n_hreflang_alternates', function ( $alts ) {
	// Estando EN una ruta de país no se declara ninguna alternativa. Si se
	// dejara el bloque, /br/ anunciaría que la versión española es «/» sin
	// incluirse a sí misma: un grupo de hreflang no recíproco, que Google
	// descarta entero y de paso siembra la duda de que /br/ sea un duplicado.
	if ( grenvios_es_ruta_pais( grenvios_i18n_current() ) ) return array();

	// Y las rutas de país nunca se ofrecen como alternativa de nadie.
	foreach ( $alts as $slug => $a ) {
		if ( grenvios_es_ruta_pais( $slug ) ) unset( $alts[ $slug ] );
	}
	return $alts;
}, 5 );   // antes de la compuerta de sedes (10)

/* ══════════════════════════════════════
   3) EL IDIOMA DECLARADO ES EL REAL
   Una ruta de país sigue siendo español de Perú: quien la lee está en Perú y
   quiere enviar a Brasil. Declarar es-BR le diría a Google y al navegador que
   la página está escrita para brasileños.
══════════════════════════════════════ */
add_filter( 'language_attributes', function ( $out ) {
	if ( ! function_exists( 'grenvios_i18n_active' ) || ! grenvios_i18n_active() ) return $out;
	$cur = grenvios_i18n_current();
	if ( ! grenvios_es_ruta_pais( $cur ) ) return $out;

	$real = grenvios_i18n_hreflang( grenvios_i18n_default() );
	return preg_replace( '/lang="[^"]*"/', 'lang="' . esc_attr( $real ) . '"', $out );
}, 30 );   // después del filtro de inc/i18n.php (20)

add_filter( 'grenvios_og_locale', function ( $locale ) {
	$cur = function_exists( 'grenvios_i18n_current' ) ? grenvios_i18n_current() : '';
	if ( $cur === '' || ! grenvios_es_ruta_pais( $cur ) ) return $locale;
	return grenvios_i18n_locale( grenvios_i18n_default() );
} );

/* ══════════════════════════════════════
   4) COMPUERTA: UNA COPIA SIN TOCAR NO ENTRA AL ÍNDICE
   Al crear la ruta de un país se copia el sitio entero. Si esas copias se
   publican tal cual, son nueve versiones idénticas del mismo texto cambiando
   el prefijo de la URL: *doorway pages* de manual, y penaliza al dominio
   completo (misma doctrina que inc/paginas-combinadas.php).
══════════════════════════════════════ */

/* Huella del contenido editable de una página: contenido + textos + SEO. */
function grenvios_pagina_huella( $post_id ) {
	$post_id = (int) $post_id;
	if ( ! $post_id ) return '';
	/* El título y la descripción SEO quedan FUERA de la huella a propósito.
	 *
	 * Se siembran automáticamente al duplicar, así que si contaran, toda copia
	 * parecería tener contenido propio desde el primer segundo y la compuerta no
	 * serviría de nada. Y aunque se escribieran a mano: nueve páginas con el
	 * mismo cuerpo y distinto título siguen siendo contenido duplicado. Lo que
	 * hace única a una página de país es su TEXTO. */
	$ignorar = array( 'grenvios_seo_title', 'grenvios_seo_desc' );

	$partes = array( (string) get_post_field( 'post_content', $post_id ) );
	foreach ( get_post_meta( $post_id ) as $k => $v ) {
		if ( strpos( $k, 'grenvios_' ) !== 0 ) continue;
		if ( in_array( $k, $ignorar, true ) ) continue;
		$partes[] = $k . '=' . maybe_serialize( $v );
	}
	sort( $partes );
	return md5( implode( '|', $partes ) );
}

/* ¿La página de una ruta de país tiene contenido propio? */
function grenvios_pagina_pais_propia( $post_id ) {
	$post_id = (int) $post_id;
	if ( ! $post_id ) return true;
	if ( ! function_exists( 'grenvios_i18n_active' ) || ! grenvios_i18n_active() ) return true;

	$lang = function_exists( 'pll_get_post_language' ) ? pll_get_post_language( $post_id ) : '';
	if ( ! $lang || ! grenvios_es_ruta_pais( $lang ) ) return true;   // maestra o idioma real

	$master = grenvios_i18n_master_id( $post_id );
	if ( ! $master || $master === $post_id ) return true;

	// Espejo: su canónica apunta a otra URL, y noindex + canónica ajena son dos
	// señales contradictorias. Se deja solo la canónica.
	if ( function_exists( 'grenvios_espejo_es' ) && grenvios_espejo_es( $post_id ) ) return true;

	/* La portada de la ruta se sirve con la plantilla de ese destino, no con la
	 * portada genérica (ver front-page.php), así que su contenido visible es
	 * íntegramente propio aunque su `post_content` siga siendo el de la home de
	 * Perú. Comparar el texto guardado la dejaría fuera del índice por un
	 * parecido que en pantalla no existe. */
	if ( (int) get_option( 'page_on_front' ) && function_exists( 'pll_get_post' ) ) {
		$portada = (int) pll_get_post( (int) get_option( 'page_on_front' ), $lang );
		if ( $portada && $portada === $post_id ) return true;
	}

	// Título distinto ya es señal de que se ha trabajado; si además cambia el
	// contenido o algún texto, con más razón.
	if ( get_post_field( 'post_title', $post_id ) !== get_post_field( 'post_title', $master ) ) return true;

	/* El bloque de secciones por país (inc/paises-contenido.php) se escribe solo
	 * al duplicar, así que NO puede valer por sí mismo como «alguien trabajó esta
	 * página»: si valiera, las 216 copias entrarían al índice de golpe el día que
	 * se crean, que es justo lo que la compuerta existe para impedir.
	 *
	 * Se mide en dos pasos:
	 *
	 *   1) ¿Ha tocado la clienta algo? Se compara la huella SIN el bloque
	 *      automático. Cualquier edición suya abre el índice, como siempre.
	 *   2) Si no ha tocado nada, el bloque automático abre por sí solo cuando
	 *      alcanza volumen suficiente para que la página aporte algo de verdad
	 *      sobre ese país. Un bloque de tres líneas no lo hace.
	 *
	 * El umbral es de caracteres de texto visible y se ajusta con el filtro
	 * `grenvios_pais_umbral`. */
	$sin_bloque = function ( $id ) {
		$c = (string) get_post_field( 'post_content', $id );
		// El trim importa: al quitar el bloque quedan los saltos que lo rodeaban y,
		// sin normalizarlos, la copia parecía editada por los saltos sobrantes.
		return md5( trim( preg_replace( '~<!-- grenvios:pais -->.*?<!-- /grenvios:pais -->~s', '', $c ) ) );
	};
	if ( $sin_bloque( $post_id ) !== $sin_bloque( $master ) ) return true;
	if ( grenvios_pagina_huella( $post_id ) !== grenvios_pagina_huella( $master )
		&& get_post_field( 'post_content', $post_id ) === get_post_field( 'post_content', $master ) ) {
		return true;   // cambió un repeater o un campo, no el cuerpo
	}

	$umbral = (int) apply_filters( 'grenvios_pais_umbral', 600, $post_id );
	$cont   = (string) get_post_field( 'post_content', $post_id );
	if ( preg_match( '~<!-- grenvios:pais -->(.*?)<!-- /grenvios:pais -->~s', $cont, $m ) ) {
		$texto = trim( wp_strip_all_tags( $m[1] ) );
		if ( mb_strlen( $texto ) >= $umbral ) return true;
	}
	return false;
}

add_action( 'wp_head', function () {
	if ( is_admin() ) return;

	$id = 0;
	if ( is_singular() ) {
		$id = (int) get_queried_object_id();
	} elseif ( is_home() ) {
		// La página del blog no es `is_singular()` —es la lista de entradas— así
		// que sin esto se quedaba fuera de la compuerta y era la única copia sin
		// tocar que sí entraba al índice.
		$id = (int) get_option( 'page_for_posts' );
		if ( $id && function_exists( 'pll_get_post' ) ) {
			$cur = grenvios_i18n_current();
			$tid = (int) pll_get_post( $id, $cur );
			if ( $tid ) $id = $tid;
		}
	}
	if ( ! $id || grenvios_pagina_pais_propia( $id ) ) return;

	echo '<meta name="robots" content="noindex, follow">' . "\n";
	echo '<!-- Grenvíos: copia sin editar de la versión principal; fuera del índice hasta escribir el contenido de este país -->' . "\n";
}, 3 );

/* Y fuera del sitemap, por lo mismo. */
add_filter( 'grenvios_sitemap_urls', function ( $urls ) {
	foreach ( $urls as $i => $u ) {
		if ( empty( $u['post_id'] ) ) continue;
		if ( ! grenvios_pagina_pais_propia( $u['post_id'] ) ) unset( $urls[ $i ] );
	}
	return array_values( $urls );
}, 25 );

/* ══════════════════════════════════════
   5) DUPLICAR EL SITIO A LA RUTA DE UN PAÍS
   Crear a mano la traducción de cada página desde Polylang son decenas de
   pasos por país. Esto las crea todas de una vez, ya enlazadas como
   traducciones, con el contenido copiado para que se reescriba encima.
══════════════════════════════════════ */

/* Páginas del idioma maestro que se pueden duplicar a una ruta de país. */
function grenvios_ruta_paginas_maestras() {
	$args = array(
		'post_type'   => 'page',
		'post_status' => array( 'publish', 'draft' ),
		'numberposts' => -1,
		'orderby'     => 'menu_order',
		'order'       => 'ASC',
	);
	if ( function_exists( 'grenvios_i18n_active' ) && grenvios_i18n_active() ) {
		$args['lang'] = grenvios_i18n_default();
	}
	$out = array();
	foreach ( get_posts( $args ) as $p ) {
		$out[ $p->ID ] = $p;
	}
	return $out;
}

/* Duplica las páginas indicadas (o todas) a la ruta de un país.
 * Devuelve array( 'creadas' => n, 'saltadas' => n, 'errores' => array ). */
function grenvios_ruta_duplicar( $lang, $ids = array() ) {
	$res = array( 'creadas' => 0, 'saltadas' => 0, 'errores' => array() );
	if ( ! function_exists( 'pll_set_post_language' ) ) {
		$res['errores'][] = 'Polylang no está activo.';
		return $res;
	}
	$master = grenvios_i18n_default();
	if ( $lang === $master ) {
		$res['errores'][] = 'No se puede duplicar la ruta principal sobre sí misma.';
		return $res;
	}

	$paginas = grenvios_ruta_paginas_maestras();
	if ( $ids ) {
		foreach ( array_keys( $paginas ) as $id ) {
			if ( ! in_array( (int) $id, array_map( 'intval', $ids ), true ) ) unset( $paginas[ $id ] );
		}
	}

	/* El árbol de Destinos NO se duplica.
	 *
	 * En la ruta de Cuba, la única destinación relevante es Cuba: la propia ruta
	 * ya es la página de ese destino. Duplicar el árbol entero produce
	 * /cu/destinos/chile/ — «Chile a Cuba», un servicio que no existe — y nueve
	 * páginas huérfanas por país que nadie va a escribir y que solo pesan en el
	 * rastreo. El hub /destinos/ del sitio principal sigue siendo el índice de
	 * todos los países. */
	$hub = get_page_by_path( 'destinos' );
	$id_hub = $hub ? (int) $hub->ID : 0;
	foreach ( $paginas as $id => $p ) {
		if ( $id_hub && ( (int) $id === $id_hub || (int) $p->post_parent === $id_hub ) ) {
			unset( $paginas[ $id ] );
		}
	}

	/* Todas las páginas se duplican a cada país.
	 *
	 * Hubo una versión que dejaba fuera seis —rastreo, seguro, apostilla, peso
	 * volumétrico, recojo y provincias— por considerarlas «del origen». El
	 * argumento era malo: «seguro de envíos a Cuba» y «seguro de envíos a Chile»
	 * son búsquedas distintas y no compiten entre sí, y con el bloque de
	 * contenido por país cada una dice cosas distintas. Lo que sí las haría
	 * competir es publicarlas idénticas, y de eso ya se encarga la compuerta.
	 *
	 * Filtro `grenvios_ruta_solo_origen`: si alguna vez se quiere dejar una fuera,
	 * basta con nombrarla aquí. Por defecto está vacío. */
	$solo_origen = (array) apply_filters( 'grenvios_ruta_solo_origen', array(), $lang );
	if ( $solo_origen ) {
		foreach ( $paginas as $id => $p ) {
			if ( in_array( $p->post_name, $solo_origen, true ) ) unset( $paginas[ $id ] );
		}
	}

	/* Filtro `grenvios_ruta_paginas`: para excluir (o recuperar) páginas
	 * concretas antes de duplicar. */
	$paginas = (array) apply_filters( 'grenvios_ruta_paginas', $paginas, $lang );

	// Primero las páginas sin madre: una hija necesita que su madre ya exista
	// traducida para colgar de ella y no de la versión en español.
	uasort( $paginas, function ( $a, $b ) {
		return ( $a->post_parent ? 1 : 0 ) <=> ( $b->post_parent ? 1 : 0 );
	} );

	foreach ( $paginas as $id => $p ) {
		if ( (int) pll_get_post( $id, $lang ) ) { $res['saltadas']++; continue; }

		/* Dentro de una ruta las páginas van PLANAS.
		 *
		 * Conservar la jerarquía daba
		 * /cu/servicios-a-cuba/envio-de-paquetes-a-cuba/ : el país tres veces y
		 * 45 caracteres que no añaden nada, porque el prefijo /cu/ ya da todo el
		 * contexto. Plana queda /cu/envio-de-paquetes-a-cuba/, que es la URL que
		 * se quiere posicionar. La página madre sigue existiendo como índice.
		 *
		 * Filtro `grenvios_ruta_aplanar`: devolver false para conservar el árbol. */
		$parent = 0;
		if ( $p->post_parent && ! apply_filters( 'grenvios_ruta_aplanar', true, $lang, $p ) ) {
			$parent = (int) pll_get_post( $p->post_parent, $lang );
			if ( ! $parent ) $parent = (int) $p->post_parent;
		}

		$nuevo = wp_insert_post( array(
			'post_type'    => 'page',
			'post_status'  => $p->post_status,
			'post_title'   => $p->post_title,
			'post_name'    => grenvios_ruta_slug( $p->post_name, $lang, $p ),
			'post_content' => $p->post_content,
			'post_excerpt' => $p->post_excerpt,
			'post_parent'  => $parent,
			'menu_order'   => $p->menu_order,
		), true );

		if ( is_wp_error( $nuevo ) ) {
			$res['errores'][] = $p->post_title . ': ' . $nuevo->get_error_message();
			continue;
		}

		// El contenido del editor de página, los repeaters y el SEO: se copian
		// para que haya algo sobre lo que reescribir, no para dejarlo así.
		foreach ( get_post_meta( $id ) as $k => $v ) {
			if ( strpos( $k, 'grenvios_' ) !== 0 ) continue;
			update_post_meta( $nuevo, $k, maybe_unserialize( $v[0] ) );
		}
		if ( function_exists( 'grenvios_i18n_stamp_master' ) ) {
			grenvios_i18n_stamp_master( $nuevo, $p->post_name );
		}

		/* Contenido propio de este país: las secciones que hacen que la copia NO
		 * sea el texto de Perú con otro prefijo. Se escriben en el contenido, no
		 * se inyectan al pintar, para que la clienta pueda reescribirlas y para
		 * que la compuerta las cuente como texto propio. Ver inc/paises-contenido.php. */
		if ( function_exists( 'grenvios_pais_aplicar' ) ) {
			grenvios_pais_aplicar( $nuevo, $p->post_name, $lang );
		}

		// Título y descripción SEO ya orientados a este país. Sin esto la copia
		// hereda el <title> de Perú y no da ninguna señal del destino: son las
		// dos líneas que más rinden y las más fáciles de olvidar al reescribir.
		grenvios_ruta_sembrar_seo( $nuevo, $p, $lang );

		pll_set_post_language( $nuevo, $lang );
		$tr = function_exists( 'pll_get_post_translations' ) ? pll_get_post_translations( $id ) : array();
		$tr[ $master ] = $id;
		$tr[ $lang ]   = $nuevo;
		pll_save_post_translations( $tr );

		$res['creadas']++;
	}

	/* Y las entradas del blog: sin esto el blog de cada ruta quedaba vacío, que
	 * es la peor versión de una página pensada para atraer tráfico. */
	if ( function_exists( 'grenvios_ruta_duplicar_posts' ) ) {
		$rp = grenvios_ruta_duplicar_posts( $lang );
		$res['posts'] = $rp['creadas'];
	}

	/* Acción `grenvios_ruta_duplicada`: la usa inc/paises-espejos.php para crear
	 * las copias de Destinos, sus fichas y el índice de artículos. */
	do_action( 'grenvios_ruta_duplicada', $lang, $res );

	if ( $res['creadas'] ) flush_rewrite_rules( false );
	return $res;
}

/* Slug de la copia de una página en la ruta de un país.
 *
 * WordPress resuelve las páginas por su ruta de slugs, y Polylang 3.8 no altera
 * esa resolución: dos páginas no pueden compartir slug en el mismo nivel aunque
 * estén en rutas distintas. Si se fuerza, `/cu/nosotros/` acaba sirviendo la
 * página española y la copia queda inalcanzable.
 *
 * Así que el slug tiene que cambiar, y ya que cambia se aprovecha: en vez del
 * «-2» que pondría WordPress, se le añade el país. «tiempos-de-entrega-a-cuba»
 * lleva la palabra clave que de verdad se busca, y es mejor URL que repetir
 * «tiempos-de-entrega» detrás del prefijo.
 *
 * Filtro `grenvios_ruta_slug`: para escribir a mano el slug de una página
 * concreta en un país concreto. */
function grenvios_ruta_slug( $base, $lang, $post = null ) {
	$pais = function_exists( 'grenvios_sede_destino_nombre' ) ? grenvios_sede_destino_nombre( $lang ) : '';
	$pais = sanitize_title( remove_accents( $pais ) );

	// Sin nombre de país no se inventa uno con el código de la ruta: «envios-a-ar»
	// no es una palabra clave y quedaría fijado en la URL para siempre.
	if ( $pais === '' ) return apply_filters( 'grenvios_ruta_slug', $base, $base, $lang, $post );

	/* Slug final de cada página, con %s por el país.
	 *
	 * No se genera pegando «-a-cuba» detrás del slug original: eso producía
	 * «como-enviar-un-paquete-al-extranjero-a-cuba», que además de larguísima
	 * dice «al extranjero a Cuba». Cada una está escrita para la búsqueda real
	 * de esa página en ese país.
	 *
	 * Los slugs de servicio son «envio-de-…» y no «enviar-…» a propósito: las
	 * páginas por combinación (inc/paginas-combinadas.php) ya ocupan
	 * «enviar-paquetes-a-cuba» en la raíz del sitio, y dos páginas no pueden
	 * compartir slug en el mismo nivel aunque estén en rutas distintas.
	 *
	 * Filtro `grenvios_ruta_slugs`: para reescribir cualquiera. */
	$mapa = apply_filters( 'grenvios_ruta_slugs', array(
		'home'                                 => 'envios-a-%s',
		'servicios'                            => 'servicios-de-envio-a-%s',
		'envio-internacional-de-paquetes'      => 'envio-de-paquetes-a-%s',
		'envio-internacional-de-documentos'    => 'envio-de-documentos-a-%s',
		'carga-internacional'                  => 'carga-internacional-a-%s',
		'envio-de-equipaje'                    => 'envio-de-equipaje-a-%s',
		'envio-de-compras'                     => 'envio-de-compras-a-%s',
		'envio-de-alimentos'                   => 'envio-de-alimentos-a-%s',
		'apostilla-y-traduccion'               => 'apostilla-y-traduccion-para-%s',
		'peso-volumetrico'                     => 'peso-volumetrico-envios-a-%s',
		'tiempos-de-entrega'                   => 'tiempos-de-entrega-a-%s',
		'que-se-puede-enviar'                  => 'que-se-puede-enviar-a-%s',
		'aduanas-e-impuestos'                  => 'aduana-de-%s',
		'seguro-de-envios'                     => 'seguro-para-envios-a-%s',
		'cotizar'                              => 'cuanto-cuesta-enviar-a-%s',
		'rastreo-de-envios'                    => 'rastrear-envio-a-%s',
		'como-enviar-un-paquete-al-extranjero' => 'como-enviar-un-paquete-a-%s',
		'recojo-a-domicilio-lima'              => 'recojo-a-domicilio-envios-a-%s',
		'envios-desde-provincias'              => 'enviar-a-%s-desde-provincias',
		'envios-para-empresas'                 => 'envios-empresariales-a-%s',
		'nosotros'                             => 'sobre-nosotros-envios-a-%s',
		'contacto'                             => 'contacto-envios-a-%s',
		'preguntas-frecuentes'                 => 'preguntas-frecuentes-envios-a-%s',
		'blog'                                 => 'guias-para-enviar-a-%s',
	), $lang, $pais );

	if ( isset( $mapa[ $base ] ) ) {
		$slug = sprintf( $mapa[ $base ], $pais );
	} elseif ( $base === $pais || substr( $base, -strlen( '-' . $pais ) ) === '-' . $pais ) {
		$slug = $base;                       // ya nombra al país
	} else {
		$slug = $base . '-a-' . $pais;       // páginas nuevas que no estén en el mapa
	}

	return apply_filters( 'grenvios_ruta_slug', $slug, $base, $lang, $post );
}



/* Cuántas páginas le faltan a una ruta para tener el sitio completo. */
function grenvios_ruta_cobertura( $lang ) {
	// Se cuenta lo mismo que se duplica, para que la barra no prometa páginas
	// que nunca se van a crear (el árbol de Destinos queda fuera).
	$paginas = grenvios_ruta_paginas_maestras();
	$hub     = get_page_by_path( 'destinos' );
	$id_hub  = $hub ? (int) $hub->ID : 0;
	foreach ( $paginas as $id => $p ) {
		if ( $id_hub && ( (int) $id === $id_hub || (int) $p->post_parent === $id_hub ) ) unset( $paginas[ $id ] );
	}
	$paginas = (array) apply_filters( 'grenvios_ruta_paginas', $paginas, $lang );

	$hechas = 0;
	if ( function_exists( 'pll_get_post' ) ) {
		foreach ( array_keys( $paginas ) as $id ) {
			if ( (int) pll_get_post( $id, $lang ) ) $hechas++;
		}
	}
	return array( 'hechas' => $hechas, 'total' => count( $paginas ) );
}

/* Siembra el SEO de una página recién duplicada, orientándolo a su país.
 *
 * Reutiliza los patrones de inc/paginas-combinadas.php, que están escritos uno a
 * uno para la intención de cada tipo de página («¿Cuánto Demora un Envío a
 * Cuba?»). Solo se aplica a las páginas cuyo equivalente existe ahí; el resto se
 * queda sin sembrar antes que con una frase mal construida. */
function grenvios_ruta_sembrar_seo( $nuevo, $master, $lang ) {
	if ( ! function_exists( 'grenvios_combo_servicios' ) ) return;

	$pais = function_exists( 'grenvios_sede_destino_nombre' ) ? grenvios_sede_destino_nombre( $lang ) : '';
	if ( $pais === '' ) return;

	/* slug de la página maestra => clave del patrón SEO por país. */
	$mapa = apply_filters( 'grenvios_ruta_seo_mapa', array(
		'envio-internacional-de-documentos' => 'documentos',
		'envio-internacional-de-paquetes'   => 'paquetes',
		'carga-internacional'               => 'carga',
		'envio-de-equipaje'                 => 'equipaje',
		'cotizar'                           => 'precio',
		'tiempos-de-entrega'                => 'tiempos',
		'que-se-puede-enviar'               => 'restricciones',
		'aduanas-e-impuestos'               => 'aduana',
		'envio-de-compras'                  => 'compras',
	) );
	$aplicar = function ( $patron ) use ( $pais ) {
		$txt = sprintf( $patron, $pais );
		return function_exists( 'grenvios_sede_tokens_apply' ) ? grenvios_sede_tokens_apply( $txt ) : $txt;
	};

	$seo = $desc = '';

	if ( isset( $mapa[ $master->post_name ] ) ) {
		$serv = grenvios_combo_servicios();
		$key  = $mapa[ $master->post_name ];
		if ( ! empty( $serv[ $key ]['seo'] ) )  $seo  = $aplicar( $serv[ $key ]['seo'] );
		if ( ! empty( $serv[ $key ]['desc'] ) ) $desc = $aplicar( $serv[ $key ]['desc'] );
	} else {
		/* El resto de páginas del sitio. Sin esto heredarían el <title> de Perú
		 * y las 15 que no son de servicio no darían ninguna señal de su país.
		 * Cada patrón está escrito para la intención de esa página, no generado
		 * pegando «a Cuba» detrás de un título cualquiera. */
		$otros = apply_filters( 'grenvios_ruta_seo_otros', array(
			/* La portada de la ruta y su página de destino compartían title y
			 * description palabra por palabra: dos URLs indexables idénticas para
			 * Google. La portada lleva ahora los suyos; «Precios y Tiempos» se
			 * queda en la página de destino, que es donde están. */
			'home' => array(
				'Courier de {{origen_pais}} a %1$s | Grenvíos',
				'Courier de {{origen_pais}} a %1$s: documentos, paquetes y carga desde {{origen_ciudad}}, con recojo, cotizador en línea y seguimiento hasta la entrega.',
			),
			'servicios' => array(
				'Servicios de Envío a %1$s | Grenvíos',
				'Todas las formas de enviar a %1$s desde {{origen_ciudad}}: documentos, paquetes, equipaje, compras y carga comercial.',
			),
			'nosotros' => array(
				'Quiénes Somos: Envíos a %1$s desde {{origen_pais}} | Grenvíos',
				'Operamos la ruta a %1$s de forma regular desde {{origen_ciudad}}. Quiénes somos, cómo trabajamos y por qué confían en nosotros.',
			),
			'contacto' => array(
				'Contacto para Envíos a %1$s | Grenvíos',
				'Habla con nosotros sobre tu envío a %1$s: teléfono, WhatsApp, correo y dirección de nuestra oficina en {{origen_ciudad}}.',
			),
			'rastreo-de-envios' => array(
				'Rastrear un Envío a %1$s | Grenvíos',
				'Consulta dónde está tu envío a %1$s con tu número de guía y resuelve cualquier duda sobre su estado.',
			),
			'preguntas-frecuentes' => array(
				'Preguntas Frecuentes: Envíos a %1$s | Grenvíos',
				'Las dudas que más nos llegan sobre enviar a %1$s: plazos, precios, aduana, qué se puede mandar y cómo se entrega.',
			),
            'envios-para-empresas' => array(
				'Envíos Empresariales a %1$s | Grenvíos',
				'Envíos recurrentes y carga comercial a %1$s para empresas: cuenta corporativa, tarifas por volumen y facturación.',
			),
			'apostilla-y-traduccion' => array(
				'Apostilla y Traducción para %1$s | Grenvíos',
				'Apostillamos y traducimos tus documentos antes de enviarlos a %1$s, para que tengan validez legal al llegar.',
			),
			'seguro-de-envios' => array(
				'Seguro para Envíos a %1$s | Grenvíos',
				'Protege el valor de tu envío a %1$s: qué cubre el seguro, cuánto cuesta y cómo se declara la mercancía.',
			),
			'envio-de-alimentos' => array(
				'Enviar Alimentos a %1$s: Qué se Permite | Grenvíos',
				'Qué alimentos admite la aduana de %1$s, por qué vía se pueden mandar y cómo empacarlos para que lleguen bien.',
			),
			'peso-volumetrico' => array(
				'Peso Volumétrico para Envíos a %1$s | Grenvíos',
				'Calcula el peso volumétrico de tu envío a %1$s y entiende por qué una caja grande y liviana puede costar más.',
			),
			'como-enviar-un-paquete-al-extranjero' => array(
				'Cómo Enviar un Paquete a %1$s Paso a Paso | Grenvíos',
				'Todo el proceso para enviar un paquete a %1$s desde {{origen_ciudad}}: qué necesitas, cómo empacarlo y cuánto tarda.',
			),
			'recojo-a-domicilio-lima' => array(
				'Recojo a Domicilio para Enviar a %1$s | Grenvíos',
				'Recogemos tu envío a %1$s en tu casa, oficina o proveedor dentro de {{origen_ciudad}}. Cómo se coordina y qué cuesta.',
			),
			'envios-desde-provincias' => array(
				'Enviar a %1$s desde Provincias | Grenvíos',
				'Cómo enviar a %1$s si no estás en {{origen_ciudad}}: haz llegar tu paquete a nuestra oficina y nosotros lo despachamos.',
			),
			'blog' => array(
				'Guías para Enviar a %1$s | Grenvíos',
				'Artículos prácticos sobre envíos a %1$s: aduana, plazos, restricciones y consejos para que tu paquete llegue sin trabas.',
			),
		), $lang );

		if ( isset( $otros[ $master->post_name ] ) ) {
			$seo  = $aplicar( $otros[ $master->post_name ][0] );
			$desc = $aplicar( $otros[ $master->post_name ][1] );
		}
	}

	if ( $seo !== '' )  update_post_meta( $nuevo, 'grenvios_seo_title', $seo );
	if ( $desc !== '' ) update_post_meta( $nuevo, 'grenvios_seo_desc',  $desc );
}

/* ══════════════════════════════════════
   6) EL SUBMENÚ DESTINOS APUNTA A LAS OTRAS RUTAS
   Cada ruta de país es un inquilino independiente: quien navega dentro de /cu/
   tiene que seguir dentro de /cu/. El submenú Destinos enlazaba a las fichas
   del sitio principal (/destinos/chile/) y sacaba al usuario de su ruta en el
   primer clic.

   Cuando un país tiene ruta propia, el enlace pasa a su ruta. Así el submenú se
   convierte en el paso de un inquilino a otro, y de camino reparte enlaces
   internos entre las rutas, que es justo lo que necesitan para posicionar.
══════════════════════════════════════ */
/* Decisión de la clienta (2026-09-14): la portada de la ruta es la HOME del país
 * (carrusel) y «Destinos → Argentina» lleva a una página aparte, la FICHA de
 * destino dentro de esa ruta —/ar/destinos-argentina/argentina/—, con el hero a
 * pantalla completa. Esa ficha es el espejo creado por inc/paises-espejos.php;
 * su canónica sigue apuntando a la portada del país, así que Google no ve dos
 * páginas compitiendo por «envíos a Argentina». */
function grenvios_ficha_destino_url( $slug ) {
	if ( ! function_exists( 'grenvios_sedes' ) || ! function_exists( 'pll_get_post' ) ) return '';
	static $cache = array();
	if ( isset( $cache[ $slug ] ) ) return $cache[ $slug ];

	$maestra = get_page_by_path( 'destinos/' . $slug );
	if ( ! $maestra ) return $cache[ $slug ] = '';
	foreach ( grenvios_sedes() as $lang => $l ) {
		if ( ! grenvios_es_ruta_pais( $lang ) ) continue;
		if ( ! function_exists( 'grenvios_sede_destino_propio' ) || grenvios_sede_destino_propio( $lang ) !== $slug ) continue;
		$ficha = (int) pll_get_post( $maestra->ID, $lang );
		if ( $ficha && get_post_status( $ficha ) === 'publish' ) return $cache[ $slug ] = get_permalink( $ficha );
	}
	return $cache[ $slug ] = '';
}

add_filter( 'grenvios_destino_permalink', function ( $url, $slug ) {
	if ( $slug === 'destinos' ) return $url;
	$f = grenvios_ficha_destino_url( $slug );
	return $f !== '' ? $f : $url;
}, 20, 2 );

/* Los enlaces a /destinos/<pais>/ escritos dentro del contenido —las tablas de
 * plazos, las tarjetas, las guías— también apuntan a la ruta de ese país cuando
 * existe. Sin esto, un enlace en el cuerpo del texto saca al usuario de su ruta
 * aunque el menú esté bien. */
function grenvios_rutas_reescribir_destinos( $html ) {
	if ( ! is_string( $html ) || strpos( $html, '/destinos/' ) === false ) return $html;
	if ( ! function_exists( 'grenvios_sedes' ) ) return $html;
	if ( function_exists( 'grenvios_cache_html' ) ) {
		return grenvios_cache_html( 'rutas-destinos', $html, grenvios_i18n_current(), 'grenvios_rutas_reescribir_destinos_raw' );
	}
	return grenvios_rutas_reescribir_destinos_raw( $html );
}

/* Ficha de un destino DENTRO de la ruta que se está viendo (en Perú, la del hub
 * principal; en /bo/, su copia /bo/envios-internacionales/ecuador/). '' si es el
 * propio país de la ruta (va a su ficha de siempre) o si no hay copia. */
function grenvios_rutas_ficha_en_ruta( $slug ) {
	static $c = array();
	$lang = function_exists( 'grenvios_i18n_current' ) ? (string) grenvios_i18n_current() : '';
	$k = $lang . '|' . $slug;
	if ( isset( $c[ $k ] ) ) return $c[ $k ];
	$m = get_page_by_path( 'destinos/' . $slug );
	if ( ! $m || get_post_status( $m ) !== 'publish' ) return $c[ $k ] = '';
	$ruta = function_exists( 'grenvios_es_ruta_pais' ) && $lang !== '' && grenvios_es_ruta_pais( $lang );
	if ( $ruta && function_exists( 'grenvios_sede_destino_propio' ) && grenvios_sede_destino_propio( $lang ) === $slug ) return $c[ $k ] = '';
	$id = (int) $m->ID;
	if ( $ruta && function_exists( 'pll_get_post' ) ) {
		$t = (int) pll_get_post( $id, $lang );
		if ( ! $t || $t === $id || get_post_status( $t ) !== 'publish' ) return $c[ $k ] = '';
		$id = $t;
	}
	return $c[ $k ] = (string) get_permalink( $id );
}

/* El trabajo de verdad, sin caché. */
function grenvios_rutas_reescribir_destinos_raw( $html ) {

	// slug de país => URL de su ruta, solo para los que tienen ruta.
	static $mapa = null;
	if ( $mapa === null ) {
		$mapa = array();
		foreach ( grenvios_sedes() as $lang => $l ) {
			if ( ! grenvios_es_ruta_pais( $lang ) ) continue;
			$d = grenvios_sede_destino_propio( $lang );
			if ( $d === '' ) continue;
			$f = grenvios_ficha_destino_url( $d );
			$mapa[ $d ] = $f !== '' ? $f : $l['url'];
		}
	}
	if ( ! $mapa ) return $html;

	return preg_replace_callback(
		'~href="([^"]*?/destinos/([a-z0-9-]+)/?)"(?!\s+hreflang=)~i',   // el selector de país no se toca
		function ( $m ) use ( $mapa ) {
			$u = grenvios_rutas_ficha_en_ruta( strtolower( $m[2] ) );
			if ( $u !== '' ) return 'href="' . esc_url( $u ) . '"';
			return isset( $mapa[ $m[2] ] ) ? 'href="' . esc_url( $mapa[ $m[2] ] ) . '"' : $m[0];
		},
		$html
	);
}
add_filter( 'the_content', 'grenvios_rutas_reescribir_destinos', 40 );
add_filter( 'grenvios_partial_html', 'grenvios_rutas_reescribir_destinos', 12 );
add_filter( 'grenvios_html_final', 'grenvios_rutas_reescribir_destinos', 12 );
/* Y después del enlazado interno (prioridad 20 en inc/seo-enlazado.php): su
 * bloque «Destinos más solicitados» se inyecta cuando los filtros de arriba ya
 * han pasado, así que sin esto era el último enlace que sacaba de la ruta. */
add_filter( 'grenvios_content_html', 'grenvios_rutas_reescribir_destinos', 30 );

/* La misma reescritura para una URL suelta: los bloques repetibles (tarjetas de
 * destino, filas de la tabla de plazos) pasan su enlace por `grenvios_rep_link`,
 * no por el HTML ya montado. */
function grenvios_rutas_url_destino( $url ) {
	$url = (string) $url;
	if ( $url === '' || strpos( $url, '/destinos/' ) === false ) return $url;
	if ( ! function_exists( 'grenvios_sedes' ) ) return $url;

	if ( ! preg_match( '~/destinos/([a-z0-9-]+)/?$~i', $url, $m ) ) return $url;
	$pais = strtolower( $m[1] );
	$u = grenvios_rutas_ficha_en_ruta( $pais );
	if ( $u !== '' ) return $u;

	foreach ( grenvios_sedes() as $lang => $l ) {
		if ( ! grenvios_es_ruta_pais( $lang ) ) continue;
		if ( grenvios_sede_destino_propio( $lang ) !== $pais ) continue;
		$f = grenvios_ficha_destino_url( $pais );
		return $f !== '' ? $f : $l['url'];
	}
	return $url;
}
add_filter( 'grenvios_rep_link', 'grenvios_rutas_url_destino', 20 );

/* ══════════════════════════════════════
   7) /pe/ TAMBIÉN LLEVA A PERÚ
   Perú es la ruta principal y va sin prefijo (/, /nosotros/…), pero quien
   escribe /pe/ por analogía con /ar/ o /cu/ acababa en «Peso volumétrico»:
   WordPress, al no encontrar la URL, adivinaba la página que más se parecía.
   Se redirige a la misma dirección sin el prefijo.
══════════════════════════════════════ */
add_action( 'init', function () {
	if ( is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) return;
	if ( ! function_exists( 'grenvios_i18n_default' ) ) return;
	$pref = grenvios_i18n_default();
	if ( $pref === '' ) return;

	$uri  = isset( $_SERVER['REQUEST_URI'] ) ? (string) wp_unslash( $_SERVER['REQUEST_URI'] ) : '';
	$base = rtrim( (string) wp_parse_url( get_option( 'home' ), PHP_URL_PATH ), '/' );
	if ( ! preg_match( '#^' . preg_quote( $base, '#' ) . '/' . preg_quote( $pref, '#' ) . '(/.*)?$#i', strtok( $uri, '?' ), $m ) ) return;

	$resto = isset( $m[1] ) ? $m[1] : '/';
	$q     = strpos( $uri, '?' ) !== false ? substr( $uri, strpos( $uri, '?' ) ) : '';
	wp_safe_redirect( untrailingslashit( get_option( 'home' ) ) . ( $resto === '' ? '/' : $resto ) . $q, 301 );
	exit;
}, 1 );
