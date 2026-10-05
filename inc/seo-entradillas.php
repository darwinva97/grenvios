<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Entradilla con la keyword, al principio de cada página
 * ══════════════════════════════════════════════════════════════════════════
 *
 * ÚLTIMO ERROR QUE QUEDABA EN LA AUDITORÍA, en 18 de las 35 páginas de la ruta
 * principal: «la keyword no aparece en el cuerpo de la página». No es un
 * tecnicismo. La página de /nosotros/ hablaba de «transporte internacional
 * confiable» y nunca decía **courier internacional en Lima**, que es lo que la
 * gente escribe en Google; /rastreo-de-envios/ decía «sigue tu envío» y nunca
 * **rastreo de envíos internacionales**.
 *
 * Aquí cada página recibe un párrafo de entrada —dos o tres frases— que:
 *
 *   1. usa la keyword una vez, en lenguaje natural y en la primera línea, que
 *      es donde Google la lee con más peso;
 *   2. dice de qué va la página, para que quien llega desde el buscador sepa en
 *      dos segundos si está donde quería;
 *   3. enlaza al siguiente paso con ancla descriptiva, de modo que además
 *      reparte autoridad hacia las páginas que venden.
 *
 * No es relleno: sustituye a nada y se lee antes del contenido que ya había.
 * Es editable como cualquier otra sección y se puede vaciar desde el editor.
 *
 * ALCANCE: la ruta principal. En las rutas de país esta función la cumple el
 * bloque de país, que ya abre con el destino en la primera frase.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function grenvios_ent_textos() {
	return apply_filters( 'grenvios_ent_textos', array(

		'home' => 'Somos un courier peruano especializado en <strong>envíos internacionales desde Perú</strong>: paquetes, documentos, equipaje y carga a más de treinta destinos, con revisión del contenido antes de despachar y seguimiento hasta la entrega. <a href="%H%/cotizar/">Cotiza tu envío</a> con el peso y el destino, o mira <a href="%H%/destinos/">las condiciones de cada país</a>.',

		'nosotros' => 'Grenvíos es una <strong>empresa de envíos desde Perú</strong> con sede en Lima. No somos una plataforma: hay una oficina, un mostrador y alguien que revisa contigo lo que vas a enviar antes de que la caja se cierre. Eso es lo que evita la mayoría de las retenciones en aduana. <a href="%H%/contacto/">Aquí estamos</a>, <a href="%H%/servicios/">esto es lo que hacemos</a> y así trabajamos como <a href="%H%/courier-internacional-en-lima/">courier internacional en Lima</a>.',

		'contacto' => 'Somos una <strong>agencia de envíos internacionales en Lima</strong> con oficina abierta al público: puedes venir a despachar, escribirnos para cotizar o pedir que <a href="%H%/recojo-a-domicilio-lima/">pasemos a recoger tu envío</a> en tu distrito. Si escribes con el peso, las medidas y el destino, te contestamos con el precio cerrado.',

		'destinos' => 'Estos son nuestros <strong>destinos de envíos internacionales</strong> desde Perú. Cada ficha trae lo mismo y en el mismo orden —vía disponible, plazo, forma de entrega, qué admite su aduana y a qué ciudades llegamos—, para que puedas comparar dos países sin leerlos enteros. Si el tuyo no aparece, <a href="%H%/cotizar/">escríbenos</a>: trabajamos más de treinta y varios se coordinan bajo pedido.',

		'servicios' => 'Todos nuestros <strong>servicios de envío internacional</strong> parten del mismo sitio —nuestra oficina de Lima— y se diferencian en el embalaje, la documentación y la forma de cobrar. Elegir bien el servicio antes de cotizar es lo que más cambia el precio final.',

		'tiempos-de-entrega' => 'Los <strong>tiempos de entrega de envíos internacionales</strong> no son un número suelto: son cuatro tramos encadenados y se cuentan en días hábiles desde el despacho. Aquí está el plazo de cada destino y, sobre todo, qué puede alargarlo y qué no. <a href="%H%/destinos/">Consulta el de tu país</a> antes de prometer una fecha.',

		'rastreo-de-envios' => 'El <strong>rastreo de envíos internacionales</strong> se hace con el número de guía que se entrega al despachar. Con él se ve en qué tramo está el envío, incluido el paso por la aduana de destino, que es el único que no controlamos. Si algo se detiene, <a href="%H%/contacto/">escríbenos con tu número de guía</a> y lo revisamos.',

		'aduanas-e-impuestos' => 'Las <strong>aduanas e impuestos en envíos internacionales</strong> se calculan sobre el valor declarado del contenido, no sobre lo que pesa el bulto. Entenderlo evita los dos errores más caros: declarar de menos, que expone el envío a una revisión, y declarar sin factura lo que es mercancía nueva. <a href="%H%/que-se-puede-enviar/">Consulta también qué admite cada país</a>.',

		'seguro-de-envios' => 'El <strong>seguro para envíos internacionales</strong> se calcula sobre el valor declarado, el mismo importe que sirve de base al impuesto en destino. De ahí sale el error que más dinero cuesta: declarar por debajo del valor real para pagar menos deja el envío cubierto por esa cifra menor.',

		'envios-desde-provincias' => 'Los <strong>envíos internacionales desde provincias</strong> funcionan igual que desde Lima, con un tramo previo: tu paquete llega primero a nuestra oficina y desde ahí se despacha al extranjero. Lo que cambia es el margen que conviene dejar, no el precio del flete internacional.',

		'recojo-a-domicilio-lima' => 'El <strong>recojo de envíos a domicilio en Lima</strong> es el paso que más tiempo ahorra y el que menos gente conoce: pasamos por tu paquete en tu casa, tu oficina o donde tu proveedor, lo pesamos, lo medimos y lo despachamos. No altera el plazo de tránsito.',

		'envio-internacional-de-paquetes' => 'En un <strong>envío internacional de paquetes</strong> el precio lo deciden tres cosas que se resuelven antes de llegar al mostrador: el tamaño de la caja, la vía y lo que declaras. Ninguna tiene que ver con el contenido. <a href="%H%/servicios/peso-volumetrico/">Calcula primero el peso que se cobra</a>.',

		'envio-internacional-de-documentos' => 'Un <strong>envío internacional de documentos</strong> no tiene valor comercial: no paga impuestos y viaja por vía aérea, que es la más rápida. Lo que sí necesita, si tiene que surtir efecto legal fuera, es <a href="%H%/servicios/apostilla-y-traduccion/">apostilla y a veces traducción oficial</a>, y ese trámite se hace aquí antes de enviar.',

		'envio-de-equipaje' => 'El <strong>envío de equipaje al extranjero</strong> es la alternativa al exceso de equipaje del aeropuerto: mandas tus maletas por delante, viajas ligero y en la segunda pieza suele salir más barato. Sirve igual para una mudanza completa. <a href="%H%/cotizar/">Cotiza con el peso y las medidas</a>.',

		'envio-de-compras' => 'El <strong>envío de compras al extranjero</strong> resuelve el problema de las tiendas peruanas que no despachan fuera: compras con nuestra dirección de Lima como destino, consolidamos todos tus pedidos en un solo bulto y pagas un flete en vez de cinco.',

		'envio-de-alimentos' => 'El <strong>envío de alimentos al extranjero</strong> tiene reglas propias en cada aduana y no todas las vías lo admiten. Antes de comprar nada conviene mirar <a href="%H%/que-se-puede-enviar/">qué acepta el país de destino</a>: un alimento que no puede salir es dinero perdido dos veces.',

		'preguntas-frecuentes' => 'Las <strong>preguntas frecuentes sobre envíos internacionales</strong> que más nos hacen, contestadas una por una: precio, plazo, aduana, embalaje, seguro y entrega. Si la tuya no está aquí, <a href="%H%/contacto/">escríbenos</a>: la contestamos y la añadimos.',

		'blog' => 'Nuestras <strong>guías de envíos internacionales</strong> están escritas desde la operación diaria, no copiadas de un manual: lo que preguntan en el mostrador, lo que retiene una aduana y lo que encarece un envío sin que nadie lo avise.',

		'articulos-por-pais' => 'Estas son nuestras <strong>guías de envío por país</strong>, artículo por artículo: alimentos, medicinas, cosméticos, electrónica con batería y ropa de marca, con la regla que aplica cada aduana. Las listas no son iguales de un destino a otro, así que conviene mirar la del tuyo antes de comprar la caja.',

		'cotizar' => 'Para <strong>cotizar un envío internacional</strong> necesitamos cuatro datos: peso, medidas de la caja ya cerrada, destino y qué va dentro. Con eso te damos el precio total puesto en destino, que es la única cifra comparable entre presupuestos.',

		'que-se-puede-enviar' => 'Saber <strong>qué se puede enviar al extranjero</strong> antes de comprar la caja evita el gasto que no se recupera: un envío retenido no se devuelve. Estas son las categorías con reglas propias en casi todos los destinos y lo que aplica en cada uno.',

		'peso-volumetrico' => 'El <strong>peso volumétrico</strong> es la razón por la que dos paquetes que pesan lo mismo no cuestan lo mismo: se cobra el mayor entre el peso real y el que corresponde al espacio que ocupa la caja. Aquí lo puedes calcular con tus propias medidas.',

		'carga-internacional' => 'En <strong>carga internacional</strong> el precio deja de mirar el bulto suelto y empieza a mirar el conjunto: peso total, volumen ocupado y tipo de mercancía. La documentación es la otra mitad del trabajo.',

		'apostilla-y-traduccion' => 'La <strong>apostilla y traducción de documentos</strong> es lo que hace que un título, una partida o un poder emitido en Perú tenga validez legal en otro país. Sin ella el documento llega, pero no sirve, y el plazo se pierde dos veces.',

		'como-enviar-un-paquete-al-extranjero' => 'Esta es la guía completa de <strong>cómo enviar un paquete al extranjero</strong> desde Perú, en cinco pasos y en el orden en que se hacen. Los dos donde se pierde más dinero son medir la caja y declarar el contenido; los dos se arreglan en cinco minutos antes de despachar.',

		'envios-para-empresas' => 'Los <strong>envíos internacionales para empresas</strong> se parecen poco a un envío suelto: hay volumen, hay recurrencia y hay que justificar cada flete. Trabajamos con consolidación, recojos programados y la documentación de exportación lista antes de que la carga salga.',
	) );
}

/* ─────────────────────────────────────────────────────────────────────────
 * Entradilla de una página de RUTA DE PAÍS
 *
 * Aquí no vale el texto de arriba: en /cl/ la consulta es «envío de paquetes a
 * Chile», no «envío internacional de paquetes». Se arma con la keyword de la
 * página —que ya lleva el destino (inc/seo-keywords-defecto.php)— y con un dato
 * real de la ruta: la vía, el plazo o la forma de entrega. Así las 315 páginas
 * de país abren nombrando su consulta y sin repetir un texto entre rutas.
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_ent_pais_texto( $slug, $lang = '', $post_id = 0 ) {
	/* Con $lang y $post_id se puede calcular FUERA de la petición de esa página
	 * —lo necesita la auditoría de «SEO por página», que recorre las 350 desde
	 * el escritorio—. Sin ellos usa el contexto activo. */
	if ( $lang !== '' && function_exists( 'grenvios_sede_destino_nombre' ) ) {
		$pais = trim( (string) grenvios_sede_destino_nombre( $lang ) );
	} else {
		if ( ! function_exists( 'grenvios_hq_pais' ) ) return '';
		$pais = trim( (string) grenvios_hq_pais() );
		$lang = function_exists( 'grenvios_i18n_current' ) ? (string) grenvios_i18n_current() : '';
	}
	if ( $pais === '' ) return '';

	$kw = function_exists( 'grenvios_seo_kw' ) ? trim( (string) grenvios_seo_kw( $post_id ) ) : '';
	if ( $kw === '' ) return '';

	/* Cada página tiene su frase, escrita para que la keyword entre donde entra
	 * en castellano. Pegar «Todo sobre el » delante de cualquier keyword da
	 * engendros como «Todo sobre el cuánto cuesta enviar a Chile». %k es la
	 * keyword tal cual; %K, con mayúscula inicial para abrir frase. */
	$aperturas = array(
		'home'                                 => 'Los %k salen de nuestra oficina de Lima todas las semanas.',
		'servicios'                            => 'Estos son nuestros %k: paquetes, documentos, equipaje, compras, alimentos y carga.',
		'envio-internacional-de-paquetes'      => 'En el %k se cobra el mayor entre el peso real y el volumétrico, así que la caja importa tanto como el contenido.',
		'envio-internacional-de-documentos'    => 'El %k viaja por vía aérea y no paga impuestos: un documento no tiene valor comercial.',
		'carga-internacional'                  => 'En %k se cotiza el conjunto: peso total, volumen ocupado y tipo de mercancía.',
		'envio-de-equipaje'                    => 'El %k es la alternativa al exceso de equipaje del aeropuerto: las maletas van por delante y tú viajas ligero.',
		'envio-de-compras'                     => 'El %k resuelve el problema de las tiendas peruanas que no despachan al extranjero.',
		'envio-de-alimentos'                   => 'El %k depende de lo que admita su aduana y de la vía por la que viaje.',
		'apostilla-y-traduccion'               => 'La %k se tramita aquí, antes de despachar: un documento sin apostillar llega, pero no sirve.',
		'peso-volumetrico'                     => 'El %k decide el precio cuando la caja ocupa más espacio del que pesa.',
		'tiempos-de-entrega'                   => '¿%K? El plazo se cuenta en días hábiles desde el despacho, no desde tu consulta.',
		'que-se-puede-enviar'                  => '%K y qué retiene su aduana: conviene mirarlo antes de comprar la caja, no después.',
		'aduanas-e-impuestos'                  => 'La %k calcula lo que se paga sobre el valor declarado del contenido, no sobre lo que pesa el bulto.',
		'seguro-de-envios'                     => 'El %k se calcula sobre el valor declarado, que es también la base del impuesto.',
		'cotizar'                              => '¿%K? Con el peso, las medidas y lo que va dentro te damos el precio total puesto en destino.',
		'rastreo-de-envios'                    => 'Para %k necesitas el número de guía que se entrega al despachar: con él se ve cada tramo.',
		'como-enviar-un-paquete-al-extranjero' => '%K, paso a paso y en el orden en que se hace.',
		'recojo-a-domicilio-lima'              => 'Con %k no tienes que traer el paquete: pasamos por él, lo pesamos y lo despachamos.',
		'envios-desde-provincias'              => 'Para %k tu paquete llega primero a nuestra oficina de Lima y desde ahí sale al extranjero.',
		'envios-para-empresas'                 => 'Los %k se trabajan con consolidación, recojos programados y la documentación lista antes de despachar.',
		'destinos'                             => 'Estos son los %k que operamos, cada uno con su vía, su plazo y sus condiciones de aduana.',
		'nosotros'                             => 'Somos el %k: despachamos esta ruta todas las semanas desde nuestra oficina de Lima.',
		'contacto'                             => 'Este es el %k: ven a despachar, escríbenos para cotizar o pide que pasemos a recoger.',
		'preguntas-frecuentes'                 => 'Las %k, contestadas una por una.',
		'blog'                                 => 'Todas nuestras %k, ordenadas por el momento en que se necesitan.',
		'articulos-por-pais'                   => 'Nuestras %k, artículo por artículo y con la regla que aplica cada aduana.',
	);

	$plantilla = isset( $aperturas[ $slug ] )
		? $aperturas[ $slug ]
		: 'Todo sobre los %k: vía disponible, plazo, aduana y ciudades de entrega.';   // ficha de destino

	$frase = str_replace(
		array( '%k', '%K' ),
		array( '<strong>' . esc_html( $kw ) . '</strong>', '<strong>' . esc_html( grenvios_ent_mayus( $kw ) ) . '</strong>' ),
		$plantilla
	);

	/* Y un dato real de la ruta, para que la entradilla informe y no solo
	 * nombre la consulta. */
	if ( function_exists( 'grenvios_pais_datos' ) && $lang !== '' ) {
		$d = grenvios_pais_datos( $lang );
		if ( $d ) {
			$via = ( ! empty( $d['aereo'] ) && ! empty( $d['terr'] ) )
				? 'por vía aérea o terrestre'
				: ( ! empty( $d['terr'] ) ? 'por vía terrestre' : 'por vía aérea' );
			$ent = ! empty( $d['casa'] ) ? 'con entrega en el domicilio del destinatario' : 'con retiro en agencia local';
			$pla = ! empty( $d['tiempo'] ) ? ', en un plazo de ' . esc_html( $d['tiempo'] ) : '';
			$frase .= ' Operamos la ruta a ' . esc_html( $pais ) . ' ' . $via . ' ' . $ent . $pla . '.';
		}
	}

	return $frase;
}

/* ucfirst que respeta las tildes: «¿cuánto…» → «¿Cuánto…». */
function grenvios_ent_mayus( $s ) {
	$s = (string) $s;
	if ( $s === '' ) return $s;
	return mb_strtoupper( mb_substr( $s, 0, 1 ) ) . mb_substr( $s, 1 );
}

/* ─────────────────────────────────────────────────────────────────────────
 * Campo editable: una entradilla por página.
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_ent_campos( $slug ) {
	$t = grenvios_ent_textos();
	return array(
		'ent_intro' => array( 'Entradilla SEO', 'html', isset( $t[ $slug ] ) ? $t[ $slug ] : '' ),
	);
}

add_filter( 'grenvios_text_registry', function ( $reg ) {
	if ( ! is_admin() && function_exists( 'grenvios_hq_pais' ) && grenvios_hq_pais() !== '' ) return $reg;
	foreach ( grenvios_ent_textos() as $slug => $txt ) {
		if ( ! isset( $reg[ $slug ] ) ) continue;
		// En la portada principal no se pinta (ver grenvios_ent_html): el campo no haría nada.
		if ( $slug === 'home' ) continue;
		$reg[ $slug ]['sections'] = array_merge(
			array( 'entradilla' => array(
				'label'           => 'Entradilla SEO',
				'_no_token_check' => true,
				'sel'             => '.gr-ent',
				'fields'          => grenvios_ent_campos( $slug ),
			) ),
			$reg[ $slug ]['sections']
		);
	}
	return $reg;
} );

/* ─────────────────────────────────────────────────────────────────────────
 * Render. Va justo debajo del encabezado de la página, antes del contenido.
 * ───────────────────────────────────────────────────────────────────────── */
/* HTML de la entradilla de la página actual (vacío si no le toca). */
function grenvios_ent_html( $slug ) {
	if ( is_admin() ) return '';

	/* En la portada principal no hay bloque aparte: la keyword abre el texto
	 * de «Quiénes somos» (home_about_text), que es el primer párrafo real de
	 * la página. Suelto entre el rastreo y esa sección, el bloque quedaba
	 * huérfano, repetía lo mismo que el párrafo de debajo y la silueta del
	 * repartidor se le montaba encima. */
	if ( $slug === 'home' && ( ! function_exists( 'grenvios_hq_pais' ) || grenvios_hq_pais() === '' ) ) return '';

	$campos  = grenvios_ent_campos( $slug );
	$defecto = $campos['ent_intro'][2];

	/* En una ruta de país la entradilla se genera con su destino. */
	if ( function_exists( 'grenvios_hq_pais' ) && grenvios_hq_pais() !== '' ) {
		$defecto = grenvios_ent_pais_texto( $slug );
	}
	if ( $defecto === '' ) return '';

	$txt = grenvios_field( 'ent_intro', $defecto );
	if ( trim( wp_strip_all_tags( (string) $txt ) ) === '' ) return '';

	$home = function_exists( 'grenvios_url_base' ) ? grenvios_url_base() : home_url();
	$txt  = str_replace( '%H%', esc_url( $home ), $txt );

	return '<section class="gr-ent"><div class="container"><p class="gr-ent-p">' . wp_kses_post( $txt ) . '</p></div></section>';
}

/* ¿Ya se imprimió en esta petición? Evita que salga dos veces cuando el
 * contenido pasa por el filtro y además la plantilla llama al render. */
function grenvios_ent_hecho( $marcar = false ) {
	static $hecho = false;
	if ( $marcar ) $hecho = true;
	return $hecho;
}

/* La entradilla va DEBAJO DEL HERO, que es donde se lee y donde Google espera
 * el primer párrafo. Como el hero vive dentro del propio contenido de la
 * página, se inyecta justo después de su sección: imprimirla desde la
 * plantilla la dejaba al final de la página, detrás incluso de la llamada a la
 * acción, que es el peor sitio posible para el primer párrafo. */
function grenvios_ent_tras_hero( $html ) {
	if ( grenvios_ent_hecho() || ! is_string( $html ) || $html === '' ) return $html;

	$slug = function_exists( 'grenvios_current_slug' ) ? grenvios_current_slug() : '';
	$ent  = grenvios_ent_html( $slug );
	if ( $ent === '' ) return $html;

	/* Justo después del hero, cerrando bien su sección: el hero lleva secciones
	 * anidadas dentro, así que buscar el primer `</section>` metía la entradilla
	 * en mitad del bloque siguiente. Aquí se cuenta la anidación. */
	/* La portada no abre con <section>: su hero es el carrusel
	 * `<div class="slider-section">`. Sin esa marca la entradilla caía ENCIMA
	 * del hero, pegada a la cabecera. */
	$pos = false;
	$tag = 'section';
	foreach ( array(
		'<section class="gr-dhero'    => 'section',
		'<section class="page-header' => 'section',
		'<section class="dest-hero'   => 'section',
		'<div class="slider-section'  => 'div',
	) as $marca => $t ) {
		$pos = strpos( $html, $marca );
		if ( $pos !== false ) { $tag = $t; break; }
	}

	if ( $pos !== false ) {
		$i         = $pos + 1;
		$abiertas  = 1;
		$largo     = strlen( $html );
		$ab_tag    = '<' . $tag;
		$ci_tag    = '</' . $tag . '>';
		while ( $i < $largo && $abiertas > 0 ) {
			$abre  = strpos( $html, $ab_tag, $i );
			/* `<div` no debe contar `<divider…`; sí `<div ` y `<div>`. */
			while ( $abre !== false && ! in_array( substr( $html, $abre + strlen( $ab_tag ), 1 ), array( ' ', '>', "\n", "\t" ), true ) ) {
				$abre = strpos( $html, $ab_tag, $abre + 1 );
			}
			$cierra = strpos( $html, $ci_tag, $i );
			if ( $cierra === false ) break;
			if ( $abre !== false && $abre < $cierra ) { $abiertas++; $i = $abre + strlen( $ab_tag ); continue; }
			$abiertas--;
			$i = $cierra + strlen( $ci_tag );
		}
		/* En la portada, tras el carrusel van la promo del mes (oculta) y la caja
		 * «Rastrea tu envío», que sube sobre el hero con margen negativo: si la
		 * entradilla queda entre ellos, la caja la tapa. Se saltan las dos (y los
		 * comentarios que las separan). */
		while ( $abiertas === 0 && $tag === 'div' ) {
			if ( preg_match( '/\G(?:\s+|<!--.*?-->)+/s', $html, $m, 0, $i ) ) $i += strlen( $m[0] );
			if ( ! preg_match( '/\G<section\b[^>]*>/', $html, $m, 0, $i ) ) break;
			if ( strpos( $m[0], 'grenvios-promo-section' ) === false && ! preg_match( '/margin-top:\s*-/', $m[0] ) ) break;
			$prof = 1;
			$j    = $i + 1;
			while ( $j < $largo && $prof > 0 ) {
				$abre   = strpos( $html, '<section', $j );
				$cierra = strpos( $html, '</section>', $j );
				if ( $cierra === false ) break;
				if ( $abre !== false && $abre < $cierra ) { $prof++; $j = $abre + 8; continue; }
				$prof--;
				$j = $cierra + strlen( '</section>' );
			}
			if ( $prof !== 0 ) break;
			$i = $j;
		}

		if ( $abiertas === 0 ) {
			grenvios_ent_hecho( true );
			return substr( $html, 0, $i ) . $ent . substr( $html, $i );
		}
	}

	grenvios_ent_hecho( true );
	return $ent . $html;
}
add_filter( 'grenvios_content_html', 'grenvios_ent_tras_hero', 5 );

/* Respaldo para las páginas data-driven, que no pasan por el filtro anterior.
 * Se llama desde page.php / front-page.php / index.php. */
function grenvios_ent_render( $slug ) {
	if ( grenvios_ent_hecho() ) return;
	$ent = grenvios_ent_html( $slug );
	if ( $ent === '' ) return;
	grenvios_ent_hecho( true );
	echo function_exists( 'grenvios_apply_media_overrides' ) ? grenvios_apply_media_overrides( $ent ) : $ent;
}

add_action( 'wp_head', function () {
	if ( is_admin() ) return;
	echo '<style id="grenvios-ent-css">'
		. '.gr-ent{padding:34px 0 0}'
		. '.gr-ent-p{max-width:78ch;margin:0;font-size:18px;line-height:1.65;opacity:.92}'
		/* Los enlaces de la entradilla salían en negro y sin subrayar: no se
		 * distinguían del texto. */
		. '.gr-ent-p a{color:var(--primary-color,#5e2129);font-weight:600;text-decoration:underline;text-underline-offset:3px}'
		. '.gr-ent-p a:hover{text-decoration-thickness:2px}'
		. '@media(max-width:767px){.gr-ent{padding-top:26px}.gr-ent-p{font-size:16px}}'
		. '</style>';
}, 108 );
