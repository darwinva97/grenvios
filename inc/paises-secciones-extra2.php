<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Lo que se escribió para Perú, ahora en las diez rutas
 * ══════════════════════════════════════════════════════════════════════════
 *
 * La ruta principal recibió tres cosas que las de país no tenían (ver
 * inc/peru-seo-secciones.php): un GLOSARIO de los términos que aparecen al
 * cotizar, una COMPARATIVA con las alternativas al courier y, en el blog, algo
 * más que un listado de tarjetas.
 *
 * Esas secciones viven en el módulo de la ruta principal y están apagadas en
 * las rutas de país a propósito: allí mandan los bloques por destino, y copiar
 * el mismo texto diez veces sería exactamente el problema que este sitio
 * intenta evitar. Así que aquí se escriben otra vez, pero DATA-DRIVEN: el
 * glosario de Chile habla de la entrega a domicilio y de su impuesto; el de
 * Cuba, del retiro en agencia y de sus restricciones. Misma utilidad, texto
 * distinto en cada ruta.
 *
 *   · glosario     → /cotizar/ y /preguntas-frecuentes/ de cada país
 *   · alternativas → /servicios/ y /envio-internacional-de-paquetes/
 *   · guiaspais    → la página del blog de cada ruta, que era la más delgada
 *                    de las diez (689-721 palabras) y no enlazaba a sus guías
 *                    por tema
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Las tres ciudades de referencia del destino. Es el dato que más distingue a
 * dos países con la misma ficha técnica: España y Estados Unidos comparten vía,
 * entrega e impuesto, así que sin las ciudades sus secciones se parecían un
 * 90 %. Con ellas, el texto vuelve a hablar de cada ruta. */
function grenvios_pse_ciudades3( $d ) {
	if ( trim( (string) $d['ciudades'] ) === '' ) return '';
	$c = grenvios_pais_lista( $d['ciudades'] );
	return $c ? esc_html( grenvios_pais_frase_lista( array_slice( $c, 0, 3 ) ) ) : '';
}

/* Nota editorial del destino (inc/blog-paises-contenido.php): qué se envía,
 * quién envía, cómo funciona su vía y el consejo propio de la ruta.
 *
 * Es el único dato que distingue de verdad a dos países con la misma ficha
 * técnica. España y Estados Unidos comparten vía aérea, entrega a domicilio y
 * ausencia de impuesto declarado, así que sus páginas se parecían un 90 %; con
 * la nota, una habla de trámites de nacionalidad y la otra del ZIP obligatorio.
 * El blog ya las usaba; las páginas, no. */
function grenvios_pse_nota( $d, $clave ) {
	if ( ! function_exists( 'grenvios_bp_notas' ) ) return '';
	$n = grenvios_bp_notas();
	$s = isset( $d['slug'] ) ? $d['slug'] : '';
	return isset( $n[ $s ][ $clave ] ) ? trim( (string) $n[ $s ][ $clave ] ) : '';
}

/* Ruta (código de idioma) cuyo destino propio es este país. */
function grenvios_pse_lang_de( $slug ) {
	static $mapa = null;
	if ( $mapa === null ) {
		$mapa = array();
		if ( function_exists( 'pll_languages_list' ) && function_exists( 'grenvios_sede_destino_propio' ) ) {
			foreach ( pll_languages_list() as $l ) {
				$d = (string) grenvios_sede_destino_propio( $l );
				if ( $d !== '' ) $mapa[ $d ] = $l;
			}
		}
	}
	return isset( $mapa[ $slug ] ) ? $mapa[ $slug ] : '';
}

/* ─────────────────────────────────────────────────────────────────────────
 * 1) Glosario, con lo que cada término significa EN ESTA RUTA
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_pais_seccion_glosario( $d, $ang ) {
	$p = esc_html( $d['title'] );

	$tit = array(
		'cotizar'              => 'Seis palabras que verás al cotizar tu envío a ' . $p,
		'preguntas-frecuentes' => 'El vocabulario de un envío a ' . $p,
		'servicios'            => 'Los términos que usamos al hablar de ' . $p,
	);
	$h = isset( $tit[ $ang ] ) ? $tit[ $ang ] : 'En claro: los términos de tu envío a ' . $p;

	$via = ( $d['aereo'] && $d['terr'] )
		? 'Hacia ' . $p . ' se elige entre las dos y esa elección es la que más mueve el precio.'
		: ( $d['aereo']
			? 'Hacia ' . $p . ' operamos por vía aérea.'
			: 'Hacia ' . $p . ' operamos por vía terrestre.' );

	$imp = $d['impuesto'] !== ''
		? 'En ' . $p . ' es además la base del impuesto, así que declarar de menos no abarata: expone el envío a una revalorización en aduana.'
		: 'Es la base de la cobertura del envío, así que declarar de menos no abarata: deja el envío asegurado por esa cifra menor.';

	$pp = $d['casa']
		? 'A ' . $p . ' sí aplica: el envío llega al domicilio del destinatario, y por eso hace falta su dirección exacta con referencia y un teléfono que conteste.'
		: 'A ' . $p . ' no aplica: el envío se retira en la agencia local que le corresponde al destinatario, con su documento de identidad.';

	/* La entradilla propia del destino. Solo en las páginas cuya matriz no trae
	 * la sección «ruta», que es donde ya se usa: así no se repite dentro de la
	 * misma página y sí diferencia a dos países con la misma ficha técnica. */
	$lead = in_array( $ang, array( 'cotizar', 'preguntas-frecuentes' ), true ) && trim( (string) $d['lead'] ) !== ''
		? '<p>' . esc_html( $d['lead'] ) . '</p>'
		: '';

	$c = $lead
		. '<ul class="srv-list">'
		. '<li><strong>Peso volumétrico.</strong> El peso que corresponde al espacio que ocupa la caja: alto × largo × ancho en centímetros, dividido entre 5000. Se compara con el peso real y se cobra el mayor de los dos.</li>'
		. '<li><strong>Vía.</strong> El medio por el que viaja el envío. ' . $via . '</li>'
		. '<li><strong>Valor declarado.</strong> Lo que vale el contenido. ' . $imp . '</li>'
		. '<li><strong>Despacho.</strong> El momento en que el envío sale de {{origen_ciudad}} con su documentación. Es cuando empieza a contar el plazo hacia ' . $p
		. ( $d['tiempo'] !== '' ? ' —los ' . esc_html( $d['tiempo'] ) . ' que publicamos—' : '' ) . ', no cuando nos escribes.</li>'
		. '<li><strong>Guía.</strong> El número que identifica tu envío en todos los tramos. Con él se consulta el estado y se resuelve cualquier incidencia hasta la entrega en ' . $p . '.</li>'
		. '<li><strong>Puerta a puerta.</strong> ' . $pp . '</li>'
		. '</ul>'
		. '<p>Si algo de la cotización a ' . $p . ' no se entiende, pregúntalo antes de despachar: no cuesta nada y evita la sorpresa que ninguna empresa quiere dar.</p>';

	$consejo = grenvios_pse_nota( $d, 'consejo' );
	if ( $consejo !== '' ) $c .= '<p><strong>Propio de esta ruta:</strong> ' . wp_kses_post( $consejo ) . '</p>';

	return grenvios_pais_sec( 'bg-grey', $h, $c );
}

/* ─────────────────────────────────────────────────────────────────────────
 * 2) Courier, correo postal o llevarlo en la maleta — hacia ESTE país
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_pais_seccion_alternativas( $d, $ang ) {
	$p = esc_html( $d['title'] );

	$tit = array(
		'servicios'                       => 'Courier, correo postal o llevarlo a ' . $p . ' en la maleta',
		'envio-internacional-de-paquetes' => 'Otras formas de hacer llegar un paquete a ' . $p,
		'envio-de-equipaje'               => 'Enviar por delante o pagar exceso de equipaje',
		'nosotros'                        => 'Por qué un courier y no otra vía hacia ' . $p,
	);
	$h = isset( $tit[ $ang ] ) ? $tit[ $ang ] : 'Las alternativas para enviar a ' . $p;

	$courier = ( $d['aereo'] && $d['terr'] )
		? 'Hacia ' . $p . ' tenemos vía aérea y terrestre, así que se puede elegir entre llegar antes o pagar menos por el mismo bulto; esa elección no existe en las otras dos opciones.'
		: ( $d['terr']
			? 'Hacia ' . $p . ' trabajamos vía terrestre, que es la que rinde cuando el bulto abulta: en volumen es donde más se diferencia del correo postal.'
			: 'Hacia ' . $p . ' trabajamos vía aérea, con revisión del contenido en origen: es lo que evita que el envío se detenga en su aduana.' );

	$nota_via = grenvios_pse_nota( $d, 'via' );

	$c = '<p>Antes de elegir empresa, casi todo el mundo compara tres caminos para hacer llegar algo a ' . $p . '. Cada uno gana en un escenario distinto:</p>'
		. ( $nota_via !== '' ? '<p>' . wp_kses_post( $nota_via ) . '</p>' : '' )
		. '<ul class="srv-list">'
		. '<li><strong>Correo postal.</strong> Suele ser lo más barato en envíos muy pequeños y ligeros, y también lo más lento hacia ' . $p
		. '. Nadie revisa contigo el contenido antes de despachar ni gestiona el envío si su aduana lo detiene.</li>'
		. '<li><strong>Llevarlo en la maleta.</strong> Imbatible si ya viajas a ' . $p . ' y cabe. Deja de serlo en la segunda maleta, cuando el exceso de equipaje se cobra por pieza.</li>'
		. '<li><strong>Courier.</strong> ' . $courier . '</li>'
		. '</ul>'
		. '<p>Y entre couriers, lo que de verdad mueve el precio de un envío a ' . $p
		. ' no es la marca: es qué vías ofrece y si revisa el contenido en origen, que es lo que evita la retención que cuesta días. '
		. 'Pide siempre el <strong>precio total puesto en ' . $p . '</strong>: es la única cifra comparable entre presupuestos.</p>';

	if ( $d['tiempo'] !== '' ) {
		$c3  = grenvios_pse_ciudades3( $d );
		$c .= '<p>Como referencia, nuestro plazo a ' . $p . ' es de <strong>' . esc_html( $d['tiempo'] )
			. '</strong> desde el despacho'
			. ( $c3 !== '' ? ', con entrega en ' . $c3 . ' y en el resto de ciudades de la ruta' : '' )
			. '. Compara ese número con el que te den las otras opciones antes de decidir.</p>';
	}

	return grenvios_pais_sec( '', $h, $c );
}

/* ─────────────────────────────────────────────────────────────────────────
 * 3) El blog de la ruta: sus guías, por tema y con enlace
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_pais_seccion_guiaspais( $d, $ang ) {
	$p    = esc_html( $d['title'] );
	$s    = $d['slug'];
	$lang = grenvios_pse_lang_de( $s );
	if ( $lang === '' || ! function_exists( 'grenvios_guia_por_clave' ) ) return '';

	/* Las guías propias de este destino, en el orden en que se necesitan. */
	$bloques = array(
		'Antes de enviar' => array(
			'como-enviar-un-paquete-a-' . $s   => 'Cómo enviar un paquete a ' . $p . ', paso a paso',
			'que-se-puede-enviar-a-' . $s      => 'Qué se puede enviar a ' . $p . ' y qué retiene su aduana',
			'cuanto-cuesta-enviar-a-' . $s     => 'Cuánto cuesta enviar a ' . $p . ' y qué define el precio',
		),
		'Plazos y fechas' => array(
			'cuanto-demora-un-envio-a-' . $s   => 'Cuánto demora un envío a ' . $p,
			'enviar-a-' . $s . '-en-fechas-clave' => 'Con cuánta antelación enviar en fechas clave',
			'ciudades-de-' . $s                => 'A qué ciudades de ' . $p . ' llegamos',
		),
		'Casos concretos' => array(
			'enviar-documentos-a-' . $s        => 'Enviar documentos a ' . $p . ': apostilla y plazos',
			'enviar-encomiendas-a-familiares-en-' . $s => 'Encomiendas a familiares en ' . $p,
			'mudarse-a-' . $s . '-enviar-equipaje' => 'Mudarse a ' . $p . ': enviar el equipaje por delante',
		),
	);

	$html  = '';
	$total = 0;
	foreach ( $bloques as $titulo => $guias ) {
		$li = '';
		foreach ( $guias as $clave => $texto ) {
			$post = grenvios_guia_por_clave( $clave, $lang );
			if ( ! $post || $post->post_status !== 'publish' ) continue;
			$li .= '<li><a href="' . esc_url( get_permalink( $post ) ) . '">' . esc_html( $texto ) . '</a></li>';
			$total++;
		}
		if ( $li === '' ) continue;
		$html .= '<div class="gr-guias-grupo"><h3>' . esc_html( $titulo ) . '</h3><ul class="srv-list">' . $li . '</ul></div>';
	}
	if ( $total < 3 ) return '';

	$intro = '<p>Todo lo que publicamos sobre esta ruta sale de despachar a ' . $p
		. ' todas las semanas: qué pide su aduana, cuánto tarda de verdad, qué se retiene y qué conviene tener listo antes de embalar. '
		. 'Están ordenadas por el momento en que se necesitan.</p>';

	$cierre = '<p>Si después de leer sigue habiendo una duda concreta de tu envío a ' . $p
		. ', escríbenos: contestamos con el caso delante, no con un texto genérico.</p>';

	return grenvios_pais_sec( '', 'Guías para enviar a ' . $p, $intro . '<div class="gr-guias-cols">' . $html . '</div>' . $cierre );
}

/* ─────────────────────────────────────────────────────────────────────────
 * 3 bis) Consolidar varios paquetes en uno
 *        Las páginas de compras y carga se quedaban en 812-823 palabras en
 *        todas las rutas porque sus secciones dependen de campos del gestor
 *        (documentación exigida) que aún están vacíos. Esta no depende de
 *        ninguno: se arma con la vía y el impuesto del destino.
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_pais_seccion_consolidar( $d, $ang ) {
	$p = esc_html( $d['title'] );

	$tit = array(
		'envio-de-compras'     => 'Varias compras, un solo envío a ' . $p,
		'carga-internacional'  => 'Consolidar tu carga hacia ' . $p,
		'envios-para-empresas' => 'Consolidar los envíos de tu empresa a ' . $p,
	);
	$h = isset( $tit[ $ang ] ) ? $tit[ $ang ] : 'Juntar varios bultos en un envío a ' . $p;

	$via = $d['terr']
		? 'Y como hacia ' . $p . ' hay vía terrestre, un consolidado grande es justo donde más se nota: se paga por volumen y el volumen bien armado es lo que abarata.'
		: 'Hacia ' . $p . ' el consolidado viaja por vía aérea, donde cada kilo cuenta: por eso el trabajo está en que no sobre ni un centímetro de caja.';

	$imp = $d['impuesto'] !== ''
		? '<li><strong>El impuesto se calcula igual.</strong> Consolidar no lo evita: se aplica sobre el valor declarado del conjunto, así que conviene tener las boletas de todo lo que va dentro.</li>'
		: '<li><strong>Declara el conjunto, no cada caja.</strong> El valor declarado del bulto consolidado es el que cuenta en la aduana de ' . $p . ', y es también la base de la cobertura.</li>';

	$productos = grenvios_pse_nota( $d, 'productos' );

	$c = '<p>Consolidar es juntar varios paquetes en un solo bulto para pagar <strong>un flete en vez de varios</strong>. '
		. 'Es lo que más ahorra a quien compra en tiendas distintas o recibe de varios proveedores, porque el precio de un envío a ' . $p
		. ' no crece igual que el número de cajas.</p>'
		. '<ul class="srv-list">'
		. '<li><strong>Retiramos el embalaje sobrante.</strong> Cajas dentro de cajas y relleno de aire: eso es lo que dispara el peso volumétrico, que es por lo que se cobra casi todo lo que abulta y pesa poco.</li>'
		. '<li><strong>Esperamos a que llegue todo.</strong> Los pedidos que llegan en semanas distintas salen juntos; tú decides la fecha de corte.</li>'
		. $imp
		. '<li><strong>Una sola guía.</strong> Un número de seguimiento para todo el envío hasta ' . $p
		. ( $d['tiempo'] !== '' ? ', con el plazo habitual de ' . esc_html( $d['tiempo'] ) : '' ) . '.</li>'
		. '</ul>'
		. '<p>' . $via . '</p>';

	if ( $productos !== '' ) {
		$c .= '<p>En esta ruta lo que más se consolida es ' . wp_kses_post( $productos )
			. '. Si es tu caso, dínoslo al abrir el consolidado: cambia la forma de embalar y, con ella, el volumen final.</p>';
	}

	$c3 = grenvios_pse_ciudades3( $d );
	if ( $c3 !== '' ) {
		$c .= '<p>La ciudad de destino dentro de ' . $p . ' —' . $c3
			. ' o cualquier otra— no cambia el trámite del consolidado: solo el tramo local de reparto'
			. ( $d['casa'] ? ' hasta el domicilio.' : ' hasta la agencia donde se retira.' ) . '</p>';
	}

	return grenvios_pais_sec( 'bg-grey', $h, $c );
}

/* ─────────────────────────────────────────────────────────────────────────
 * 3 ter) El recojo, contado para esta ruta
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_pais_seccion_recojo( $d, $ang ) {
	$p = esc_html( $d['title'] );

	$tit = array(
		'recojo-a-domicilio-lima'              => 'Recogemos tu envío a ' . $p . ' donde estés',
		'envios-desde-provincias'              => 'Si envías a ' . $p . ' desde provincias',
		'como-enviar-un-paquete-al-extranjero' => 'Sin salir de casa: el recojo',
		'contacto'                             => 'Si prefieres que pasemos a recoger',
	);
	$h = isset( $tit[ $ang ] ) ? $tit[ $ang ] : 'Recojo a domicilio para tu envío a ' . $p;

	$c = '<p>No hace falta que traigas el paquete: pasamos por él en tu casa, tu oficina o donde tu proveedor, lo pesamos, lo medimos y lo despachamos hacia ' . $p
		. ' en la misma programación que si lo hubieras traído tú. <strong>El recojo no alarga el plazo</strong>; lo que sí manda es el día en que se recoge, porque es el día en que empieza a contar.</p>'
		. '<ul class="srv-list">'
		. '<li><strong>Ten el contenido listo, no cerrado.</strong> Lo revisamos en el momento: es cuando se detecta algo que la aduana de ' . $p . ' no admite, que es lo que de verdad retrasa un envío.</li>'
		. '<li><strong>Si no tienes caja, la ponemos nosotros,</strong> ajustada al contenido. Una caja holgada se paga como si el aire fuera mercancía.</li>'
		. '<li><strong>Ten a mano los datos del destinatario en ' . $p . ':</strong> '
		. ( $d['casa'] ? 'dirección exacta con referencia y un teléfono local que conteste.' : 'nombre tal como figura en su documento y la ciudad donde va a retirar.' )
		. '</li>'
		. '<li><strong>Coordínalo con margen.</strong> En temporada alta los recojos se llenan con días de antelación, y el plazo hacia ' . $p
		. ( $d['tiempo'] !== '' ? ' (' . esc_html( $d['tiempo'] ) . ')' : '' ) . ' corre desde el despacho, no desde la llamada.</li>'
		. '</ul>';

	$quien = grenvios_pse_nota( $d, 'quien' );
	if ( $quien !== '' ) {
		$c .= '<p>El recojo lo usa sobre todo quien envía con regularidad a ' . $p . ': ' . wp_kses_post( $quien )
			. '. Si es tu caso, se puede dejar programado y ahorrarte la llamada cada vez.</p>';
	}

	$c3 = grenvios_pse_ciudades3( $d );
	if ( $c3 !== '' ) {
		$c .= '<p>Desde el recojo en {{origen_ciudad}}, el envío sigue el mismo camino que cualquier otro hacia ' . $p
			. ': ' . $c3 . ' y el resto de ciudades de la ruta se atienden igual, con el mismo número de guía desde el primer día.</p>';
	}

	return grenvios_pais_sec( '', $h, $c );
}

/* ─────────────────────────────────────────────────────────────────────────
 * 4) Reparto
 * ───────────────────────────────────────────────────────────────────────── */
add_filter( 'grenvios_pais_matriz', function ( $m ) {
	$extra = array(
		'cotizar'                         => array( 'glosario' ),
		'preguntas-frecuentes'            => array( 'glosario' ),
		'servicios'                       => array( 'alternativas' ),
		'envio-internacional-de-paquetes' => array( 'alternativas' ),
		'envio-de-equipaje'               => array( 'alternativas' ),
		'blog'                            => array( 'guiaspais' ),
		'envio-de-compras'                => array( 'consolidar' ),
		'carga-internacional'             => array( 'consolidar' ),
		'envios-para-empresas'            => array( 'consolidar' ),
		'recojo-a-domicilio-lima'         => array( 'recojo' ),
		'envios-desde-provincias'         => array( 'recojo' ),
		'como-enviar-un-paquete-al-extranjero' => array( 'recojo' ),
	);

	foreach ( $extra as $slug => $secs ) {
		if ( ! isset( $m[ $slug ] ) || ! is_array( $m[ $slug ] ) ) continue;
		$lista  = $m[ $slug ];
		$nuevas = array_values( array_diff( $secs, $lista ) );
		if ( ! $nuevas ) continue;
		$pos = array_search( 'cta', $lista, true );
		if ( $pos === false ) $lista = array_merge( $lista, $nuevas );
		else array_splice( $lista, $pos, 0, $nuevas );
		$m[ $slug ] = $lista;
	}
	return $m;
}, 22 );

add_action( 'wp_head', function () {
	if ( is_admin() ) return;
	echo '<style id="grenvios-pais-extra2-css">'
		. '.gr-guias-cols{display:grid;gap:22px;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));margin:0 0 18px}'
		. '.gr-guias-grupo h3{font-size:17px;margin:0 0 10px}'
		. '.gr-guias-grupo .srv-list{margin:0}'
		. '</style>';
}, 107 );
