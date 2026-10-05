<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Cuatro secciones más por país
 * ══════════════════════════════════════════════════════════════════════════
 *
 * POR QUÉ
 * El posicionamiento de este sitio es POR PAÍS: /cl/ compite por «enviar a
 * Chile», no por «enviar». Lo único que distingue una página de su gemela en
 * otra ruta es el bloque de país (inc/paises-contenido.php), y varias páginas
 * recibían solo dos o tres secciones: /cl/servicios-de-envio-a-chile/ se
 * quedaba en 718 palabras y /cl/contacto-envios-a-chile/ en 616, contra las
 * 870-1.150 de sus equivalentes de la ruta principal. La ruta que tiene que
 * posicionar era la más delgada.
 *
 * Aquí se añaden cuatro secciones nuevas, todas construidas con los datos
 * reales del destino —vía disponible, forma de entrega, plazo, impuesto,
 * restricciones— para que el texto de Chile no sea el de Cuba con el nombre
 * cambiado, y se reparten entre las páginas que iban cortas.
 *
 * Nada de esto inventa un dato: si el destino no tiene ese campo relleno en el
 * gestor, la sección correspondiente no se pinta.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ─────────────────────────────────────────────────────────────────────────
 * 1) Primera vez que envías a este país
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_pais_seccion_primeravez( $d, $ang ) {
	$p = esc_html( $d['title'] );
	if ( ! $d['aereo'] && ! $d['terr'] ) return '';

	$tit = array(
		'servicios' => 'Primera vez que envías a ' . $p,
		'contacto'  => 'Si es tu primer envío a ' . $p,
		'nosotros'  => 'Cómo trabajamos un envío a ' . $p,
		'blog'      => 'Antes de tu primer envío a ' . $p,
	);
	$h = isset( $tit[ $ang ] ) ? $tit[ $ang ] : 'Tu primer envío a ' . $p;

	$via = ( $d['aereo'] && $d['terr'] )
		? 'Hacia ' . $p . ' hay dos vías y conviene preguntar por las dos: la aérea llega antes y la terrestre sale mejor cuando el bulto abulta.'
		: ( $d['aereo']
			? 'Hacia ' . $p . ' operamos por vía aérea, así que el plazo depende sobre todo de la aduana, no del trayecto.'
			: 'Hacia ' . $p . ' operamos por vía terrestre: el plazo es mayor que el aéreo y el precio, bastante menor en bultos voluminosos.' );

	$ent = $d['casa']
		? 'En ' . $p . ' entregamos en el domicilio del destinatario, así que necesitas su dirección exacta con una referencia y un teléfono que conteste.'
		: 'En ' . $p . ' el envío se retira en la agencia local que le corresponde al destinatario, y lo retira con su documento: su nombre tiene que coincidir con el del documento, sin apodos ni abreviaturas.';

	$c = '<ol class="gr-pais-pasos">'
		. '<li><strong>Define qué envías.</strong> Es lo primero porque manda sobre todo lo demás: la aduana de ' . $p
		. ' no trata igual un documento, una encomienda familiar o mercancía nueva con factura.</li>'
		. '<li><strong>Pesa y mide la caja ya cerrada.</strong> Se cobra el mayor entre el peso real y el volumétrico, así que una caja holgada se paga como si el aire fuera contenido.</li>'
		. '<li><strong>Elige la vía.</strong> ' . $via . '</li>'
		. '<li><strong>Reúne los datos del destinatario.</strong> ' . $ent . '</li>'
		. '<li><strong>Despacha y guarda tu número de guía.</strong> Con él se sigue el envío en todos los tramos hasta '
		. $p . ( $d['tiempo'] !== '' ? ', durante los ' . esc_html( $d['tiempo'] ) . ' que dura el trayecto' : '' ) . '.</li>'
		. '</ol>'
		. ( function_exists( 'grenvios_pse_nota' ) && grenvios_pse_nota( $d, 'quien' ) !== ''
			? '<p>A ' . $p . ' envían sobre todo ' . wp_kses_post( grenvios_pse_nota( $d, 'quien' ) ) . '.</p>' : '' )
		. '<p>Si algo de esto no lo tienes claro, pregúntalo antes de embalar: rehacer una caja cuesta cinco minutos y corregir un envío ya despachado a '
		. $p . ' cuesta días.</p>';

	return grenvios_pais_sec( '', $h, $c );
}

/* ─────────────────────────────────────────────────────────────────────────
 * 2) Lo que retiene un envío en este país
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_pais_seccion_errores( $d, $ang ) {
	$p = esc_html( $d['title'] );

	$tit = array(
		'rastreo-de-envios'    => 'Por qué un envío a ' . $p . ' se queda parado',
		'cotizar'              => 'Lo que encarece o detiene un envío a ' . $p,
		'seguro-de-envios'     => 'Los cinco descuidos que acaban en reclamo',
		'que-se-puede-enviar'  => 'Por qué la aduana de ' . $p . ' retiene un envío',
		'envio-de-compras'     => 'Errores al enviar compras a ' . $p,
		'preguntas-frecuentes' => 'Problemas frecuentes en envíos a ' . $p,
	);
	$h = isset( $tit[ $ang ] ) ? $tit[ $ang ] : 'Los errores que más cuestan al enviar a ' . $p;

	$items  = '<li><strong>Describir el contenido en vago.</strong> «Varios», «regalos», «cosas personales». Una descripción imprecisa es la invitación más directa a que la aduana de '
		. $p . ' abra el bulto, y abrirlo son días.</li>';
	$items .= '<li><strong>Declarar por debajo del valor real.</strong> No abarata: '
		. ( $d['impuesto'] !== ''
			? 'el impuesto de ' . $p . ' se calcula sobre el valor declarado, y si la aduana no se lo cree, lo revalúa ella.'
			: 'deja el envío cubierto por esa cifra menor y expone al destinatario a una revisión en ' . $p . '.' )
		. '</li>';

	if ( trim( (string) $d['prohibidos'] ) !== '' ) {
		$lista = grenvios_pais_frase_lista( grenvios_pais_lista( $d['prohibidos'] ) );
		$items .= '<li><strong>Meter algo que ' . $p . ' no admite.</strong> En esta ruta, lo que más se retiene es '
			. esc_html( $lista ) . '. Consúltalo antes de comprar, no después de embalar.</li>';
	} else {
		$items .= '<li><strong>Dar por hecho que lo que entra a un país entra a todos.</strong> Las listas de la aduana no son iguales: líquidos, cosméticos, suplementos, alimentos y electrónica con batería tienen reglas propias en casi cada destino.</li>';
	}

	$items .= $d['casa']
		? '<li><strong>Una dirección incompleta.</strong> En ' . $p . ' se entrega en el domicilio: sin referencia ni teléfono que conteste, el envío vuelve a la distribuidora y cada reintento suma días.</li>'
		: '<li><strong>El nombre del destinatario mal escrito.</strong> En ' . $p . ' el envío se retira con documento de identidad: si el nombre de la guía no coincide exactamente con el del documento, no se lo entregan.</li>';

	$items .= '<li><strong>Embalar con lo que había en casa.</strong> Una caja reutilizada y blanda no aguanta cuatro tramos de transporte, y el seguro no cubre el daño que causa un embalaje insuficiente.</li>';

	$consejo = function_exists( 'grenvios_pse_nota' ) ? grenvios_pse_nota( $d, 'consejo' ) : '';
	if ( $consejo !== '' ) {
		$items .= '<li><strong>Pasar por alto lo propio de esta ruta.</strong> ' . wp_kses_post( $consejo ) . '</li>';
	}

	$c = '<p>Casi ningún envío a ' . $p . ' se retrasa por el transporte. Se retrasa por cosas que se deciden antes de despachar, y estas son las que vemos una y otra vez:</p>'
		. '<ul class="srv-list">' . $items . '</ul>'
		. '<p>Todas se revisan con nosotros en el mostrador antes de cerrar la caja. Es gratis y es lo que más plazos salva.</p>';

	return grenvios_pais_sec( 'bg-grey', $h, $c );
}

/* ─────────────────────────────────────────────────────────────────────────
 * 3) Qué datos necesitas del destinatario
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_pais_seccion_checklist( $d, $ang ) {
	if ( trim( (string) $d['entrega'] ) === '' ) return '';
	$p = esc_html( $d['title'] );

	$tit = array(
		'contacto'                        => 'Qué traer para despachar a ' . $p,
		'recojo-a-domicilio-lima'         => 'Ten esto listo cuando pasemos a recoger',
		'servicios'                       => 'Datos que necesitas para enviar a ' . $p,
		'envio-internacional-de-paquetes' => 'Datos del destinatario en ' . $p,
		'rastreo-de-envios'               => 'Datos que hacen falta para seguir tu envío',
	);
	$h = isset( $tit[ $ang ] ) ? $tit[ $ang ] : 'Lo que hace falta para enviar a ' . $p;

	$items  = '<li><strong>Nombre completo del destinatario</strong>, tal como figura en su documento de identidad'
		. ( $d['casa'] ? '.' : ': en ' . $p . ' es con lo que retira el envío en la agencia.' ) . '</li>';
	$items .= $d['casa']
		? '<li><strong>Dirección exacta en ' . $p . '</strong> con una referencia que ayude a encontrarla: calle, número, piso o departamento, barrio y ciudad.</li>'
		: '<li><strong>Ciudad de ' . $p . '</strong> donde va a retirar. Con ella asignamos la agencia local que le corresponde y le avisamos cuando llegue.</li>';
	$items .= '<li><strong>Un teléfono local que conteste.</strong> Es el dato que más entregas salva: sin él, un aviso fallido se convierte en un reintento y en dos días más.</li>';
	$items .= '<li><strong>Su documento de identidad</strong> —número y tipo—, que varios trámites de ' . $p . ' piden para liberar el envío.</li>';
	$items .= '<li><strong>Descripción y valor del contenido.</strong> Lo que va dentro, en palabras concretas, y cuánto vale. De ahí salen la declaración y la cobertura.</li>';

	$c = '<p>Con estos cinco datos un envío a ' . $p . ' se despacha en una sola visita. Sin alguno de ellos, se despacha igual… y se queda esperando en algún tramo.</p>'
		. '<ul class="srv-list">' . $items . '</ul>';

	/* La ciudad del destinatario y lo que más se envía en esta ruta: es lo que
	 * diferencia el texto de dos países con la misma ficha técnica. */
	if ( function_exists( 'grenvios_pse_ciudades3' ) ) {
		$c3 = grenvios_pse_ciudades3( $d );
		if ( $c3 !== '' ) {
			$c .= '<p>Si tu destinatario está en ' . $c3 . ' o en otra ciudad de ' . $p . ', dínoslo al despachar: '
				. ( $d['casa'] ? 'define el tramo final de reparto hasta su domicilio.' : 'con ella asignamos la agencia donde retirará.' ) . '</p>';
		}
	}
	if ( function_exists( 'grenvios_pse_nota' ) ) {
		$prod = grenvios_pse_nota( $d, 'productos' );
		if ( $prod !== '' ) {
			$c .= '<p>Y describe el contenido en los mismos términos en que lo declaramos. En la ruta a ' . $p
				. ' lo más habitual es ' . wp_kses_post( $prod ) . ': cada uno tiene su forma de declararse y de embalarse.</p>';
		}
	}

	$c .= '<p>Anótalos antes de venir o de que pasemos a recoger: pedírselos al destinatario en el momento es lo que más alarga el trámite.</p>';

	return grenvios_pais_sec( '', $h, $c );
}

/* ─────────────────────────────────────────────────────────────────────────
 * 4) Cuándo conviene despachar
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_pais_seccion_temporada( $d, $ang ) {
	if ( trim( (string) $d['tiempo'] ) === '' ) return '';
	$p = esc_html( $d['title'] );

	$tit = array(
		'tiempos-de-entrega'      => 'Cuándo despachar para que llegue a tiempo a ' . $p,
		'envios-desde-provincias' => 'Cuánto margen dejar desde provincias',
		'home'                    => 'Con cuánta antelación enviar a ' . $p,
	);
	$h = isset( $tit[ $ang ] ) ? $tit[ $ang ] : 'Con cuánta antelación enviar a ' . $p;

	$c = '<p>El plazo hacia ' . $p . ' es de <strong>' . esc_html( $d['tiempo'] )
		. '</strong> en condiciones normales, y las condiciones normales no existen en cuatro momentos del año: Navidad y fin de año, Día de la Madre, la vuelta a clases y las fiestas locales de '
		. $p . '. En esas semanas envía todo el mundo a la vez, y tanto las aduanas como las distribuidoras locales van más lentas.</p>'
		. '<ul class="srv-list">'
		. '<li><strong>Para una fecha concreta,</strong> dobla el plazo habitual al calcular tu margen. Llegar una semana antes no cuesta nada; llegar un día tarde arruina el motivo del envío.</li>'
		. '<li><strong>En temporada alta, prioriza la vía rápida</strong> aunque el bulto no lo pida: '
		. ( $d['aereo'] ? 'la aérea acorta el tramo internacional, que es el único que se puede acortar.' : 'despacha con más días de margen, porque el tramo terrestre es el que más se resiente de la congestión.' )
		. '</li>'
		. '<li><strong>Evita despachar en viernes por la tarde.</strong> El plazo se cuenta en días hábiles desde el despacho: lo que entra el viernes tarde empieza a contar el lunes.</li>'
		. '</ul>'
		. '<p>Si tienes una fecha límite, dínosla al cotizar. Con ella elegimos la vía que llega a tiempo, que no siempre es la más barata ni la más rápida.</p>';

	return grenvios_pais_sec( 'bg-grey', $h, $c );
}

/* ─────────────────────────────────────────────────────────────────────────
 * 5) Reparto: qué página recibe qué
 *    Se inserta ANTES de 'cta', que siempre cierra el bloque.
 * ───────────────────────────────────────────────────────────────────────── */
add_filter( 'grenvios_pais_matriz', function ( $m ) {
	$extra = array(
		'home'                                 => array( 'temporada' ),
		'servicios'                            => array( 'primeravez', 'checklist' ),
		'contacto'                             => array( 'checklist', 'primeravez' ),
		'rastreo-de-envios'                    => array( 'errores', 'checklist' ),
		'nosotros'                             => array( 'primeravez' ),
		'cotizar'                              => array( 'errores' ),
		'seguro-de-envios'                     => array( 'errores' ),
		'recojo-a-domicilio-lima'              => array( 'checklist' ),
		'envios-desde-provincias'              => array( 'temporada' ),
		'tiempos-de-entrega'                   => array( 'temporada' ),
		'blog'                                 => array( 'primeravez' ),
		'preguntas-frecuentes'                 => array( 'errores' ),
		'envio-internacional-de-paquetes'      => array( 'checklist' ),
		'envio-de-compras'                     => array( 'errores' ),
		'que-se-puede-enviar'                  => array( 'errores' ),
		'como-enviar-un-paquete-al-extranjero' => array( 'checklist' ),
	);

	foreach ( $extra as $slug => $secs ) {
		if ( ! isset( $m[ $slug ] ) || ! is_array( $m[ $slug ] ) ) continue;
		$lista = $m[ $slug ];
		$pos   = array_search( 'cta', $lista, true );
		$nuevas = array_values( array_diff( $secs, $lista ) );
		if ( ! $nuevas ) continue;
		if ( $pos === false ) {
			$lista = array_merge( $lista, $nuevas );
		} else {
			array_splice( $lista, $pos, 0, $nuevas );
		}
		$m[ $slug ] = $lista;
	}
	return $m;
}, 20 );

add_action( 'wp_head', function () {
	if ( is_admin() ) return;
	echo '<style id="grenvios-pais-extra-css">'
		. '.gr-pais-pasos{counter-reset:grp;list-style:none;padding:0;margin:0 0 18px}'
		. '.gr-pais-pasos li{counter-increment:grp;position:relative;padding:0 0 14px 42px}'
		. '.gr-pais-pasos li:before{content:counter(grp);position:absolute;left:0;top:0;width:28px;height:28px;'
		. 'border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;'
		. 'background:rgba(0,0,0,.06)}'
		. '</style>';
}, 106 );
