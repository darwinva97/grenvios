<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Tercera tanda de contenido para la ruta principal
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Cada sección cubre algo que su página no tenía (revisado contra el esquema
 * de H2 de las 35 páginas antes de escribirla), no una variación de lo que ya
 * decía. Las que llevan cifras se calculan con el gestor de destinos:
 *
 *   · /tiempos-de-entrega/    días hábiles → días de calendario, por destino
 *   · /aduanas-e-impuestos/   impuesto terrestre sobre S/ 1 000 declarados
 *   · /cotizar/               mensaje modelo para pedir la cotización
 *   · /que-se-puede-enviar/   qué viaja por avión y qué por tierra
 *   · /como-enviar-un-paquete-al-extranjero/  documentos, paquete o mudanza
 *   · /servicios/envio-de-equipaje/           cómo embalar maletas y cajas
 *   · /servicios/apostilla-y-traduccion/      documentos que más se apostillan
 *   · /envios-desde-provincias/               cómo rotular el bulto
 *
 * Mismo mecanismo que inc/peru-seo-secciones.php: son secciones editables
 * desde el front, se guardan por página y no se pintan en las rutas de país.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Destinos del gestor con plazo numérico. */
function grenvios_ps3_destinos() {
	$out = array();
	if ( ! function_exists( 'grenvios_destinos' ) || ! function_exists( 'grenvios_pais_datos' ) ) return $out;
	foreach ( grenvios_destinos() as $slug => $x ) {
		$d = grenvios_pais_datos( $slug );
		if ( ! $d || ! preg_match_all( '/\d+/', (string) $d['tiempo'], $m ) ) continue;
		$d['min'] = (int) $m[0][0];
		$d['max'] = (int) end( $m[0] );
		$out[ $slug ] = $d;
	}
	return $out;
}

/* Días de calendario que pasan hasta completar N días hábiles, empezando a
 * contar el día siguiente a un despacho en lunes (sin contar feriados). */
function grenvios_ps3_calendario( $habiles ) {
	$dia = 1; $cuenta = 0; $cal = 0;   // 1 = lunes
	while ( $cuenta < $habiles ) {
		$dia = $dia % 7 + 1; $cal++;
		if ( $dia <= 5 ) $cuenta++;
	}
	return $cal;
}

function grenvios_ps3_tabla_plazos() {
	$filas = '';
	foreach ( grenvios_ps3_destinos() as $slug => $d ) {
		$hab = $d['min'] === $d['max'] ? $d['min'] : $d['min'] . ' a ' . $d['max'];
		$c1  = grenvios_ps3_calendario( $d['min'] );
		$c2  = grenvios_ps3_calendario( $d['max'] );
		$cal = $c1 === $c2 ? $c1 : $c1 . ' a ' . $c2;
		$filas .= '<tr><th scope="row"><a href="%H%/destinos/' . esc_attr( $slug ) . '/">' . esc_html( $d['title'] ) . '</a></th>'
			. '<td>' . esc_html( $d['modos'] ) . '</td><td>' . $hab . '</td><td>' . $cal . '</td></tr>';
	}
	if ( $filas === '' ) return '';
	return '<p>Los plazos se publican en días hábiles, pero quien espera un envío cuenta días de calendario. Esta es la equivalencia para un envío despachado un <strong>lunes</strong>, sin feriados de por medio:</p>'
		. '<div class="gr-table-wrap"><table class="gr-table"><thead><tr><th scope="col">Destino</th><th scope="col">Vía</th><th scope="col">Días hábiles</th><th scope="col">Días de calendario</th></tr></thead><tbody>'
		. $filas . '</tbody></table></div>'
		. '<p>Si despachas un jueves o un viernes, el fin de semana cae antes y suma dos días de calendario al principio del trayecto. Por eso, si tienes una fecha límite, conviene despachar a comienzos de semana. Un feriado en {{origen_pais}} o en el país de destino tampoco cuenta como día hábil.</p>';
}

function grenvios_ps3_tabla_impuestos() {
	$filas = ''; $aereos = array();
	foreach ( grenvios_ps3_destinos() as $slug => $d ) {
		if ( ! empty( $d['terr'] ) && $d['impuesto'] !== '' ) {
			$pct = (float) $d['impuesto'];
			$filas .= '<tr><th scope="row"><a href="%H%/destinos/' . esc_attr( $slug ) . '/">' . esc_html( $d['title'] ) . '</a></th>'
				. '<td>≈ ' . esc_html( str_replace( '.', ',', (string) $d['impuesto'] ) ) . ' %</td>'
				. '<td>S/ ' . esc_html( number_format( 1000 * $pct / 100, 0, ',', '.' ) ) . '</td></tr>';
		} else {
			$aereos[] = $d['title'];
		}
	}
	if ( $filas === '' ) return '';
	$ult = $aereos ? array_pop( $aereos ) : '';
	$lista_aereos = $aereos ? implode( ', ', $aereos ) . ' y ' . $ult : $ult;
	return '<p>En las rutas con vía terrestre el impuesto se calcula sobre el valor declarado en la boleta o factura y se paga en {{origen_ciudad}} al despachar, así que quien recibe no lo paga al recoger. Para un contenido declarado en <strong>S/ 1 000</strong>, queda así:</p>'
		. '<div class="gr-table-wrap"><table class="gr-table"><thead><tr><th scope="col">Destino (vía terrestre)</th><th scope="col">Impuesto aproximado</th><th scope="col">Sobre S/ 1 000</th></tr></thead><tbody>'
		. $filas . '</tbody></table></div>'
		. ( $lista_aereos !== '' ? '<p>En las rutas solo aéreas (' . esc_html( $lista_aereos ) . ') el impuesto depende de qué envías y de cuánto vale, y cada país fija su propio umbral. Te lo confirmamos al <a href="%H%/cotizar/">cotizar</a>, antes de despachar.</p>' : '' );
}

add_filter( 'grenvios_pseo_secciones', function ( $m ) {

	$mas = function ( $slug, $nueva ) use ( &$m ) {
		if ( ! isset( $m[ $slug ] ) ) { $m[ $slug ] = array( $nueva ); return; }
		$actual   = isset( $m[ $slug ]['html'] ) ? array( $m[ $slug ] ) : array_values( $m[ $slug ] );
		$actual[] = $nueva;
		$m[ $slug ] = $actual;
	};

	$plazos = grenvios_ps3_tabla_plazos();
	if ( $plazos !== '' ) {
		$mas( 'tiempos-de-entrega', array(
			'sub'    => 'En días de calendario',
			'titulo' => 'Cuándo llega tu envío si lo despachas <span class="hl">un lunes</span>',
			'html'   => $plazos,
		) );
	}

	$imp = grenvios_ps3_tabla_impuestos();
	if ( $imp !== '' ) {
		$mas( 'aduanas-e-impuestos', array(
			'sub'    => 'Un ejemplo con números',
			'titulo' => 'Cuánto se paga de impuesto <span class="hl">por vía terrestre</span>',
			'html'   => $imp,
		) );
	}

	$mas( 'cotizar', array(
		'sub'    => 'Cotizar envío internacional por WhatsApp',
		'titulo' => 'El mensaje que conviene <span class="hl">mandarnos</span>',
		'html'   => '<p>La mayoría de las cotizaciones tardan porque llegan por partes: primero el destino, luego el peso, al día siguiente las medidas. Si nos escribes con estos datos juntos, la primera respuesta ya trae la cifra cerrada.</p>
<blockquote class="gr-msg"><p>Hola, quiero cotizar un envío.<br>
<strong>Destino:</strong> ciudad y país (por ejemplo, Santiago, Chile).<br>
<strong>Qué envío:</strong> ropa y dos pares de zapatillas, sin baterías ni líquidos.<br>
<strong>Peso:</strong> 9 kg.<br>
<strong>Medidas de la caja cerrada:</strong> 50 × 40 × 30 cm.<br>
<strong>Valor del contenido:</strong> S/ 800.<br>
<strong>Vía:</strong> la más económica / la más rápida.<br>
<strong>Recojo:</strong> sí, en Miraflores / lo llevo a la oficina.</p></blockquote>
<p>Si no sabes las medidas exactas, una foto de la caja junto a una cinta métrica sirve. Y si aún no tienes caja, dinos qué va dentro: te proponemos una medida ajustada, que es lo que abarata el <a href="%H%/servicios/peso-volumetrico/">peso volumétrico</a>.</p>',
	) );

	$mas( 'que-se-puede-enviar', array(
		'sub'    => 'Por avión o por tierra',
		'titulo' => 'Qué viaja por cada vía, <span class="hl">de un vistazo</span>',
		'html'   => '<p>La vía cambia la respuesta más que el propio producto. Esta es la regla general con la que trabajamos; la de tu destino puede ser más estricta, y te la confirmamos antes de despachar.</p>
<div class="gr-table-wrap"><table class="gr-table"><thead><tr><th scope="col">Qué envías</th><th scope="col">Vía aérea</th><th scope="col">Vía terrestre</th></tr></thead><tbody>
<tr><th scope="row">Ropa, calzado y textiles</th><td>Sí</td><td>Sí</td></tr>
<tr><th scope="row">Documentos</th><td>Sí</td><td>Sí</td></tr>
<tr><th scope="row">Libros, artesanía y regalos</th><td>Sí</td><td>Sí</td></tr>
<tr><th scope="row">Alimentos envasados de fábrica</th><td>Según el destino</td><td>Según el destino</td></tr>
<tr><th scope="row">Medicinas</th><td>Con receta, según el destino</td><td>Con receta, según el destino</td></tr>
<tr><th scope="row">Productos con batería</th><td>No</td><td>Consultar</td></tr>
<tr><th scope="row">Líquidos, perfumes y aerosoles</th><td>No</td><td>Consultar</td></tr>
<tr><th scope="row">Dinero, armas y sustancias inflamables</th><td>No</td><td>No</td></tr>
</tbody></table></div>
<p>«Según el destino» quiere decir que cada aduana tiene su lista: el mismo alimento que entra en Bolivia por tierra puede no entrar en España. Revisa <a href="%H%/destinos/">la ficha de tu país</a> o escríbenos con el producto antes de comprarlo.</p>',
	) );

	$mas( 'como-enviar-un-paquete-al-extranjero', array(
		'sub'    => 'Según lo que envías',
		'titulo' => 'Documentos, un paquete o una mudanza: <span class="hl">qué cambia</span>',
		'html'   => '<p>Los pasos son los mismos, pero cada tipo de envío tiene su punto delicado.</p>
<ul class="gr-pseo-list">
<li><strong>Documentos.</strong> Pesan poco y viajan por vía aérea. Lo que decide si llegan a tiempo es el trámite previo: si tienen que surtir efecto legal fuera, hay que <a href="%H%/servicios/apostilla-y-traduccion/">apostillarlos y, a veces, traducirlos</a> antes de enviarlos. <a href="%H%/servicios/envio-internacional-de-documentos/">Envío de documentos</a>.</li>
<li><strong>Un paquete.</strong> Aquí manda el tamaño de la caja: se cobra el mayor entre el peso real y el volumétrico. Una caja ajustada al contenido es la forma más rápida de pagar menos. <a href="%H%/servicios/envio-internacional-de-paquetes/">Envío de paquetes</a>.</li>
<li><strong>Una mudanza o varias maletas.</strong> Son varios bultos grandes y casi nunca corren prisa, así que hacia países vecinos la vía terrestre suele salir bastante más barata. Numera cada caja y guarda una lista de lo que lleva cada una. <a href="%H%/servicios/envio-de-equipaje/">Envío de equipaje</a>.</li>
</ul>',
	) );

	$mas( 'envio-de-equipaje', array(
		'sub'    => 'Antes de cerrar',
		'titulo' => 'Cómo embalar maletas y <span class="hl">cajas de mudanza</span>',
		'html'   => '<ul class="gr-pseo-list">
<li><strong>Maletas rígidas, cerradas y con candado.</strong> Las blandas se deforman con el peso de otros bultos. Si solo tienes una blanda, la metemos en una caja.</li>
<li><strong>Una etiqueta por fuera y otra por dentro.</strong> Con el nombre y el teléfono del destinatario. Si la exterior se pierde, la interior identifica el bulto.</li>
<li><strong>Lo pesado abajo y en cajas pequeñas.</strong> Libros y vajilla en cajas que una persona pueda levantar sola; la ropa y lo liviano, en las grandes.</li>
<li><strong>Lo frágil, envuelto pieza por pieza.</strong> Papel o ropa entre cada pieza y ningún hueco dentro de la caja: lo que se rompe es lo que se mueve.</li>
<li><strong>Una lista de contenido por bulto.</strong> Numera las cajas (1 de 6, 2 de 6…) y anota qué lleva cada una. La aduana la pide y te sirve para comprobar que llegó todo.</li>
</ul>
<p>Si no tienes cajas, las ponemos nosotros ajustadas a lo que envías, y en el <a href="%H%/recojo-a-domicilio-lima/">recojo a domicilio</a> embalamos contigo.</p>',
	) );

	$mas( 'apostilla-y-traduccion', array(
		'sub'    => 'Los casos más frecuentes',
		'titulo' => 'Documentos que más se apostillan <span class="hl">antes de enviarlos</span>',
		'html'   => '<ul class="gr-pseo-list">
<li><strong>Títulos y certificados de estudios.</strong> Para convalidar una carrera, postular a un posgrado o colegiarse en otro país.</li>
<li><strong>Partidas de nacimiento, matrimonio y defunción.</strong> Para trámites de nacionalidad, residencia, herencias o matrimonio en el extranjero.</li>
<li><strong>Certificado de antecedentes.</strong> Suele pedirse para visas de trabajo y residencia.</li>
<li><strong>Poderes notariales.</strong> Para que alguien en el otro país firme o gestione en tu nombre: compraventas, trámites bancarios o sucesiones.</li>
</ul>
<p>Cada país receptor decide si además exige traducción oficial y a qué idioma. Antes de empezar, pregunta en la institución que va a recibir el documento qué pide exactamente: es la consulta que evita apostillar dos veces. Con eso resuelto, <a href="%H%/servicios/envio-internacional-de-documentos/">lo enviamos por vía aérea</a> con número de guía.</p>',
	) );

	$mas( 'envios-desde-provincias', array(
		'sub'    => 'Para que llegue sin problemas',
		'titulo' => 'Cómo rotular el bulto que <span class="hl">viene de provincia</span>',
		'html'   => '<p>El bulto hace dos viajes y pasa por dos empresas, así que el rótulo tiene que servir para las dos.</p>
<ul class="gr-pseo-list">
<li><strong>Destinatario del tramo interno:</strong> Grenvíos, con nuestra dirección de {{origen_ciudad}} y nuestro teléfono. Te los damos al cotizar.</li>
<li><strong>Remitente:</strong> tu nombre completo, tu ciudad y un teléfono con WhatsApp. Es como identificamos el bulto al recibirlo.</li>
<li><strong>Destino final, aparte:</strong> escribe el país y el nombre del destinatario en el exterior en un papel dentro de la caja, no en el rótulo de fuera, para que la agencia local no lo confunda.</li>
<li><strong>Frágil, si corresponde.</strong> En dos caras de la caja como mínimo: el bulto se apila de distintas formas en cada tramo.</li>
</ul>
<p>Cuando la agencia te dé su número de guía, mándanoslo: así sabemos que viene y lo procesamos el mismo día que llega.</p>',
	) );

	$mas( 'seguro-de-envios', array(
		'sub'    => 'Para quien recibe',
		'titulo' => 'Qué hacer al recibir el paquete <span class="hl">si algo no está bien</span>',
		'html'   => '<p>Casi todo lo que decide un reclamo pasa en los primeros minutos después de la entrega, y lo hace el destinatario, no quien envió. Conviene mandarle estas indicaciones junto con el número de guía.</p>
<ul class="gr-pseo-list">
<li><strong>Revisar la caja antes de firmar.</strong> Si está abierta, mojada, aplastada o con la cinta cambiada, anotarlo en el comprobante de entrega o decírselo a la agencia al retirar.</li>
<li><strong>Fotografiar antes de abrir.</strong> Las seis caras de la caja y la etiqueta con la guía. Después, el contenido tal como aparece.</li>
<li><strong>No tirar nada.</strong> Ni la caja ni el relleno: son la prueba de cómo viajó el envío.</li>
<li><strong>Avisar enseguida.</strong> El destinatario te avisa a ti y tú nos escribes con la guía y las fotos. Los plazos de reclamo son cortos y empiezan a contar desde la entrega.</li>
</ul>
<p>Con eso abrimos el reclamo ante el operador y te acompañamos hasta la resolución. La cobertura se calcula sobre el valor declarado: si aún no has enviado, <a href="%H%/cotizar/">declara el valor real al cotizar</a>.</p>',
	) );

	$mas( 'envio-de-alimentos', array(
		'sub'    => 'Producto por producto',
		'titulo' => 'Los productos peruanos que más se envían <span class="hl">y cómo prepararlos</span>',
		'html'   => '<p>Son los que más vemos en el mostrador. Que entren depende de la aduana de cada país, así que confírmalo antes de comprar; lo que sigue es cómo prepararlos para que no sean el motivo de una retención.</p>
<ul class="gr-pseo-list">
<li><strong>Café y cacao.</strong> En su bolsa o lata de fábrica, cerrada y con la etiqueta a la vista. En grano o molido, nunca en bolsas sin marca.</li>
<li><strong>Chocolates, galletas y dulces típicos.</strong> En su empaque original. Los que llevan relleno de manjar o crema se deforman con el calor: mejor en la parte central de la caja.</li>
<li><strong>Quinua, maca y harinas.</strong> En bolsa sellada de fábrica y, dentro de la caja, en una segunda bolsa: un envase roto llena todo de polvo.</li>
<li><strong>Ajíes y salsas en frasco.</strong> Pueden contar como líquidos en los controles de la vía aérea, así que pregúntanos antes. Si viajan, el frasco va envuelto y en bolsa sellada aparte.</li>
<li><strong>Especias y condimentos secos.</strong> En sobres de marca con su etiqueta. Mezclas caseras en bolsas sin identificar suelen ser las primeras en detenerse.</li>
</ul>
<p>Si mandas varios productos, junta la lista con cantidades y valores antes de cerrar la caja: es lo que te pediremos al <a href="%H%/cotizar/">cotizar</a>.</p>',
	) );

	$mas( 'articulos-por-pais', array(
		'sub'    => 'Por dónde empezar',
		'titulo' => 'Cómo aprovechar <span class="hl">estas guías</span>',
		'html'   => '<p>Cada bloque de esta página responde lo que cambia de un destino a otro: el plazo real, lo que admite su aduana, cuánto se paga de impuesto y a qué ciudades llegamos. Lo que vale para cualquier destino está en las guías generales y en estas páginas:</p>
<ul class="gr-pseo-list">
<li><strong>Si es tu primer envío:</strong> <a href="%H%/como-enviar-un-paquete-al-extranjero/">cómo enviar un paquete al extranjero</a>, paso a paso.</li>
<li><strong>Si te preocupa el precio:</strong> <a href="%H%/servicios/peso-volumetrico/">cómo se calcula el peso que se cobra</a>.</li>
<li><strong>Si tienes una fecha:</strong> <a href="%H%/tiempos-de-entrega/">los plazos de cada destino</a>, en días hábiles y de calendario.</li>
<li><strong>Si dudas de lo que va dentro:</strong> <a href="%H%/que-se-puede-enviar/">qué se puede enviar al extranjero</a> y <a href="%H%/aduanas-e-impuestos/">cómo funcionan las aduanas e impuestos</a>.</li>
<li><strong>Si quieres ver todos los servicios:</strong> <a href="%H%/servicios/">servicios de envío internacional</a> o, si prefieres conocernos antes, <a href="%H%/nosotros/">quiénes somos</a>.</li>
</ul>
<p>Elige tu país con los botones de arriba y lee solo lo que te afecta. Si tu destino no aparece, <a href="%H%/destinos/">mira todos los destinos</a> o escríbenos: trabajamos más de treinta.</p>',
	) );

	return $m;
}, 50 );

/* Estilos del mensaje modelo de /cotizar/. */
add_action( 'wp_head', function () {
	if ( is_admin() ) return;
	echo '<style id="grenvios-ps3-css">'
		. '.gr-msg{margin:18px 0 20px;padding:18px 22px;border-left:4px solid var(--primary-color,#5e2129);border-radius:0 12px 12px 0;background:#faf5f3}'
		. '.gr-msg p{margin:0!important;font-size:16px!important;line-height:1.85!important;color:var(--heading-color,#0c0c0c)!important}'
		. '.gr-msg strong{color:var(--primary-color,#5e2129)}'
		. '</style>';
}, 110 );

/* ─────────────────────────────────────────────────────────────────────────
 * /articulos-por-pais/: una entradilla propia por país
 *
 * Los nueve bloques «Artículos sobre envíos a X» eran el mismo título con
 * tarjetas distintas debajo. Ahora cada uno abre con una frase que solo vale
 * para su país: vía, plazo, entrega y ciudades, sacados del gestor.
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_hub_blog_intro( $lang ) {
	if ( ! function_exists( 'grenvios_sede_destino_propio' ) || ! function_exists( 'grenvios_pais_datos' ) ) return '';
	$slug = grenvios_sede_destino_propio( $lang );
	if ( $slug === '' ) return '';
	$d = grenvios_pais_datos( $slug );
	if ( ! $d || empty( $d['title'] ) ) return '';

	$via = ( ! empty( $d['aereo'] ) && ! empty( $d['terr'] ) ) ? 'por vía aérea o terrestre' : ( ! empty( $d['terr'] ) ? 'por vía terrestre' : 'por vía aérea' );
	$ent = ! empty( $d['casa'] ) ? 'con entrega en el domicilio del destinatario' : 'con retiro en una agencia local';
	$ciu = function_exists( 'grenvios_pais_lista' ) ? array_slice( grenvios_pais_lista( isset( $d['ciudades'] ) ? $d['ciudades'] : '' ), 0, 3 ) : array();

	$txt = 'A ' . $d['title'] . ' enviamos ' . $via . ', ' . $ent
		. ( ! empty( $d['tiempo'] ) ? ', en ' . $d['tiempo'] : '' ) . '.'
		. ( $ciu ? ' Las ciudades a las que más se envía son ' . implode( ', ', array_slice( $ciu, 0, -1 ) ) . ( count( $ciu ) > 1 ? ' y ' : '' ) . end( $ciu ) . '.' : '' )
		. ' Estas guías responden lo que más se pregunta antes de despachar a ' . $d['title'] . '.';
	return '<p class="gr-hub-pais-intro">' . esc_html( $txt ) . '</p>';
}
