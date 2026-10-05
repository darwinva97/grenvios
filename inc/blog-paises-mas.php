<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Más guías por país (2026-10-02): seis búsquedas que la ruta no cubría
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Cada una apoya a una página de la ruta y responde a una búsqueda distinta
 * de esa página y de las otras 17 guías del país:
 *
 *   rastreo-de-envios          ← «Seguimiento de un envío a X: qué significa cada etapa»
 *   apostilla-y-traduccion     ← «Apostillar documentos peruanos para X» (Cuba: legalizar)
 *   peso-volumetrico           ← «Qué caja usar para enviar a X»
 *   envio-de-equipaje          ← «Estudiar en X: qué enviar desde Perú»
 *   contacto                   ← «Cómo avisar a tu destinatario en X»
 *   ficha envios-a-X           ← «Vives en X: cómo pedir productos de Perú»
 *
 * Contenido hecho con datos del país (paises-perfil.php, paises-paginas-locales.php,
 * notas de blog-paises-contenido.php y el calendario académico de abajo).
 * Sin tarifas ni plazos inventados. Importar por tandas de tres países:
 * grenvios_guias_pais_importar( grenvios_bpm_claves( 'ecuador' ) ).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function grenvios_bpm_claves( $slug = '' ) {
	$slugs = $slug !== '' ? array( $slug ) : array_keys( function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array() );
	$out = array();
	foreach ( $slugs as $s ) {
		foreach ( array( 'seguimiento-de-un-envio-a-', 'apostillar-documentos-para-', 'que-caja-usar-para-enviar-a-', 'estudiar-en-', 'avisar-a-tu-destinatario-en-', 'pedir-productos-de-peru-desde-' ) as $k ) $out[] = $k . $s;
	}
	return $out;
}

/* Calendario académico de cada destino (dato público). */
function grenvios_bpm_estudios( $s ) {
	$e = array(
		'ecuador'        => 'En Ecuador el año escolar empieza en septiembre en la Sierra y la Amazonía, y en abril o mayo en la Costa y Galápagos; las universidades trabajan por semestres.',
		'colombia'       => 'En Colombia la mayoría de colegios sigue el calendario A, que empieza en enero o febrero, y las universidades trabajan por semestres que arrancan en febrero y en agosto.',
		'chile'          => 'En Chile el año escolar y universitario empieza en marzo, y el segundo semestre arranca después de las vacaciones de invierno, a fines de julio.',
		'bolivia'        => 'En Bolivia el año escolar empieza en febrero, con un descanso de invierno a mitad de año; las universidades trabajan por semestres.',
		'argentina'      => 'En Argentina el ciclo lectivo empieza en marzo y las universidades trabajan por cuatrimestres: el segundo arranca en agosto.',
		'estados-unidos' => 'En Estados Unidos el año académico empieza a fines de agosto o en septiembre (fall semester), con un segundo ingreso en enero (spring semester).',
		'espana'         => 'En España el curso escolar y universitario empieza en septiembre; los trámites de homologación de títulos conviene iniciarlos meses antes.',
		'venezuela'      => 'En Venezuela el año escolar empieza en septiembre, y es una de las fechas con más envíos de útiles, uniformes y ropa.',
		'cuba'           => 'En Cuba el curso escolar empieza en septiembre, y la vuelta a clases es una de las fechas con más envíos de útiles y ropa a la isla.',
	);
	return isset( $e[ $s ] ) ? $e[ $s ] : '';
}

add_filter( 'grenvios_bp_plantillas', function ( $out, $d, $n ) {
	if ( ! function_exists( 'grenvios_perfil_de' ) || ! function_exists( 'grenvios_ppl_datos' ) ) return $out;
	$s  = $d['slug'];
	$pf = grenvios_perfil_de( $s );
	$xx = grenvios_ppl_datos();
	if ( ! $pf || empty( $xx[ $s ] ) ) return $out;

	$x    = $xx[ $s ];
	$p    = $d['title'];
	$f    = grenvios_bp_frases( $d );
	$dos  = $f['dos_vias'];
	$casa = ! empty( $d['casa'] );
	$c    = $f['ciudades'];
	$pref = function_exists( 'grenvios_bpl_prefijo' ) ? grenvios_bpl_prefijo( $s ) : '';
	$orig = function_exists( 'grenvios_bp_origen_sufijo' ) ? grenvios_bp_origen_sufijo() : 'desde-peru';
	$guia = function ( $k ) use ( $orig ) { return '%P:' . $k . '-' . $orig . '%'; };
	$faq  = function ( $pares ) { return function_exists( 'grenvios_ga_faq' ) ? grenvios_ga_faq( $pares ) : ''; };
	$ucf  = function ( $t ) { return mb_strtoupper( mb_substr( $t, 0, 1 ) ) . mb_substr( $t, 1 ); };
	$r    = function_exists( 'grenvios_perfil_reglas' ) ? grenvios_perfil_reglas( $s, $d ) : array( 'medicinas' => '', 'alimentos' => '', 'baterias' => '' );
	$ficha = '<a href="%H%/destinos/' . $s . '/">envíos a ' . $p . '</a>';
	$ultimo = $casa
		? 'Reparto a domicilio: el envío sale a la dirección de tu destinatario. El repartidor necesita un teléfono que conteste y la dirección completa.'
		: 'Disponible en agencia: tu destinatario recibe el aviso y lo retira con su ' . $pf['documento'] . '. El nombre del envío tiene que coincidir con el del documento.';

	/* ── 1. Seguimiento → rastreo ───────────────────────────────────── */
	$out[ 'seguimiento-de-un-envio-a-' . $s ] = array(
		'titulo'    => 'Seguimiento de un envío a ' . $p . ': qué significa cada etapa',
		'categoria' => 'guias-de-envio', 'pilar' => 'rastreo-de-envios',
		'extracto'  => 'Las etapas por las que pasa un envío de {{origen_ciudad}} a ' . $p . ', qué significa cada una, cuál puede tardar más y cuándo conviene escribirnos.',
		'html'      => '
<p>Con el número de guía ves dónde está tu envío, pero no siempre qué significa cada movimiento. Hacia ' . $p . ' el recorrido tiene cinco etapas, y solo una se escapa de nuestro control.</p>
<h2>Las etapas de un envío a ' . $p . '</h2>
<ol class="gr-pseo-steps">
<li><strong>Recibido en {{origen_ciudad}}.</strong> Pesamos, revisamos el contenido y la declaración. Aquí se generan el número de guía y, si corresponde, el pago del impuesto.</li>
<li><strong>En tránsito internacional.</strong> ' . $x['frontera'] . '</li>
<li><strong>En aduana de ' . $p . '.</strong> Lo revisa ' . $pf['aduana'] . '. Es la única etapa cuyo tiempo no controlamos; un envío bien declarado pasa sin detenerse.</li>
<li><strong>' . ( $casa ? 'En reparto.' : 'En agencia.' ) . '</strong> ' . $ultimo . '</li>
<li><strong>Entregado.</strong> El envío está en manos de tu destinatario.</li>
</ol>
<h2>Si el seguimiento no se mueve</h2>
<p>' . $x['feriados'] . ' Un seguimiento quieto durante uno o dos días hábiles es normal entre etapas; si coincide con un feriado de allí, cuenta esos días aparte.</p>
<h2>La hora de las actualizaciones</h2>
<p>' . $x['horario'] . '</p>
<p>Para consultar el estado, entra a <a href="%H%/rastreo-de-envios/">rastrear tu envío a ' . $p . '</a> con tu número de guía. Si una etapa se alarga, lee <a href="' . $guia( 'cuanto-demora-un-envio-a-' . $s ) . '">por qué se retrasa un envío a ' . $p . '</a>.</p>
' . $faq( array(
	array( '¿Qué significa «en aduana» en un envío a ' . $p . '?', 'Que lo está revisando ' . $pf['aduana'] . '. Es normal y, si la declaración es clara, el envío sigue sin intervención.' ),
	array( '¿Cuándo debo preocuparme si mi envío a ' . $p . ' no avanza?', 'Si pasan varios días hábiles sin movimiento y no hay feriado allí, escríbenos con tu número de guía y lo revisamos.' ),
) ),
	);

	/* ── 2. Apostilla / legalización → apostilla y traducción ───────── */
	$sin_apostilla = $s === 'cuba';
	$out[ 'apostillar-documentos-para-' . $s ] = array(
		'slug'      => ( $sin_apostilla ? 'legalizar-documentos-peruanos-para-' : 'apostillar-documentos-peruanos-para-' ) . $s,
		'titulo'    => $sin_apostilla
			? 'Legalizar documentos peruanos para Cuba: por qué no basta la apostilla'
			: 'Apostillar documentos peruanos para ' . $p . ': qué pedir y en qué orden',
		'categoria' => 'documentos', 'pilar' => 'apostilla-y-traduccion',
		'extracto'  => $sin_apostilla
			? 'Cuba no forma parte del Convenio de la Apostilla: qué significa para tus documentos peruanos y cómo prepararlos antes de enviarlos.'
			: 'Cómo dejar listo un documento peruano para usarlo en ' . $p . ': apostilla, idioma, orden de los trámites y envío.',
		'html'      => '
<p>' . $x['apostilla'] . '</p>
<h2>' . ( $sin_apostilla ? 'Qué cambia sin apostilla' : 'El orden que evita repetir trámites' ) . '</h2>
' . ( $sin_apostilla
	? '<p>Sin convenio, el documento no se apostilla: se legaliza. Pregunta primero a la institución cubana que lo va a recibir qué legalización exige y si pide algún paso consular; con esa respuesta, preparas el documento una sola vez.</p>
<ol class="gr-pseo-steps">
<li><strong>Confirma el requisito en Cuba.</strong> Original, copia certificada, legalización consular: que te lo digan por escrito.</li>
<li><strong>Consigue el documento en su forma final.</strong> Si cambia después de legalizarlo, hay que repetir el trámite.</li>
<li><strong>Legaliza en {{origen_pais}}</strong> siguiendo el camino que te indicaron.</li>
<li><strong>Envíalo</strong> en sobre rígido, sin doblar ni grapar, con número de guía, y comparte ese número con quien lo recibe: en Cuba la comunicación puede ser intermitente.</li>
</ol>
<p>Más detalle en <a href="%H%/legalizar-documentos-cuando-el-pais-no-acepta-apostilla/">cómo legalizar documentos cuando el país no acepta apostilla</a>.</p>'
	: '<ol class="gr-pseo-steps">
<li><strong>Pregunta qué piden allí.</strong> La institución de ' . $p . ' te dirá si quiere original, copia certificada o ambos.</li>
<li><strong>Consigue el documento en su forma final.</strong> La apostilla se pone sobre ese documento: si luego cambia, hay que repetirla.</li>
<li><strong>Apostilla en {{origen_pais}}.</strong> La emite la Cancillería; el trámite se hace aquí, no en ' . $p . '.</li>
<li><strong>Traducción, si hace falta.</strong> ' . ( $s === 'estados-unidos' ? 'Para Estados Unidos, normalmente sí: traducción certificada al inglés.' : 'Para ' . $p . ' no: el documento está en español.' ) . '</li>
<li><strong>Envío.</strong> En sobre rígido, sin doblar ni grapar, con número de guía.</li>
</ol>' ) . '
<h2>Para qué se piden más documentos en ' . $p . '</h2>
<p>En esta ruta los documentos viajan sobre todo para ' . ( $s === 'espana' ? 'nacionalidad, homologación de títulos y extranjería' : 'estudios, trabajo, residencia y trámites familiares' ) . '. ' . $n['consejo'] . '</p>
<p>Te ayudamos con el trámite en nuestro servicio de <a href="%H%/apostilla-y-traduccion/">apostilla y traducción para ' . $p . '</a>, y con el envío en <a href="' . $guia( 'enviar-documentos-a-' . $s ) . '">cómo enviar documentos a ' . $p . '</a>.</p>
' . $faq( array(
	array( $sin_apostilla ? '¿Cuba acepta la apostilla peruana?' : '¿' . $p . ' acepta la apostilla peruana?', $x['apostilla'] ),
	array( '¿Dónde se apostilla un documento peruano para ' . $p . '?', $sin_apostilla ? 'Para Cuba no se apostilla: se legaliza. Confirma con la institución cubana qué legalización exige.' : 'En {{origen_pais}}, ante la Cancillería, antes de enviarlo.' ),
) ),
	);

	/* ── 3. Caja → peso volumétrico ─────────────────────────────────── */
	$ej = array( array( 30, 20, 15 ), array( 40, 30, 30 ), array( 60, 40, 40 ) );
	$filas = '';
	foreach ( $ej as $e ) {
		$vol = round( $e[0] * $e[1] * $e[2] / 5000, 1 );
		$filas .= '<tr><td>' . $e[0] . ' × ' . $e[1] . ' × ' . $e[2] . ' cm' . ( $s === 'estados-unidos' ? ' (' . round( $e[0] / 2.54 ) . ' × ' . round( $e[1] / 2.54 ) . ' × ' . round( $e[2] / 2.54 ) . ' in)' : '' ) . '</td><td>' . str_replace( '.', ',', (string) $vol ) . ' kg</td></tr>';
	}
	$out[ 'que-caja-usar-para-enviar-a-' . $s ] = array(
		'titulo'    => 'Qué caja usar para enviar a ' . $p . ': tamaños y peso que se cobra',
		'categoria' => 'guias-de-envio', 'pilar' => 'peso-volumetrico',
		'extracto'  => 'Cómo elegir la caja para un envío a ' . $p . ': tamaños de ejemplo, el peso volumétrico de cada uno y cómo embalar para el trayecto.',
		'html'      => '
<p>La caja decide buena parte del precio de un envío a ' . $p . '. Se cobra el mayor entre el peso real y el volumétrico —largo × ancho × alto en centímetros, dividido entre 5000—, así que una caja grande y medio vacía paga aire.</p>
<h2>Tres tamaños de ejemplo</h2>
<div class="gr-table-wrap"><table class="gr-table"><thead><tr><th scope="col">Caja</th><th scope="col">Peso volumétrico</th></tr></thead><tbody>' . $filas . '</tbody></table></div>
<p>Si tu caja de 40 × 30 × 30 cm pesa 5 kg, se cobra por los 7,2 kg volumétricos; si pesa 10 kg, por los 10 reales.</p>
<h2>Qué caja conviene hacia ' . $p . '</h2>
<p>' . ( $dos
	? 'Hacia ' . $p . ' hay vía aérea y terrestre. En avión, el volumen pesa mucho: ajusta la caja al contenido. Por carretera, una caja más grande y pesada suele salir mejor; ' . lcfirst( $n['consejo'] )
	: 'Hacia ' . $p . ' la ruta es aérea, y en avión el volumen pesa mucho en la tarifa: ajusta la caja al contenido y comprime lo blando.' ) . '</p>
<h2>Embalar para el destino</h2>
<p>' . $x['clima'] . '</p>
<ul class="gr-pseo-list">
<li><strong>Caja de doble pared</strong> para lo pesado o frágil.</li>
<li><strong>Relleno sin huecos</strong>: lo que se mueve dentro se rompe.</li>
<li><strong>Cinta en H</strong> arriba y abajo.</li>
</ul>
<p>' . $x['unidades'] . ' Calcula el tuyo en la página de <a href="%H%/servicios/peso-volumetrico/">peso volumétrico para envíos a ' . $p . '</a> y, para bajar el costo, lee <a href="' . $guia( 'cuanto-cuesta-enviar-a-' . $s ) . '">cómo pagar menos al enviar a ' . $p . '</a>.</p>
' . $faq( array(
	array( '¿Cuál es el peso volumétrico de una caja de 40 × 30 × 30 cm?', '40 × 30 × 30 ÷ 5000 = 7,2 kg. Si la caja pesa menos que eso, se cobra por 7,2 kg.' ),
	array( '¿Puedo usar mi propia caja para enviar a ' . $p . '?', 'Sí, si está en buen estado y es resistente. La revisamos al recibirla y, si hace falta, la reforzamos.' ),
) ),
	);

	/* ── 4. Estudiantes → equipaje ──────────────────────────────────── */
	$est = grenvios_bpm_estudios( $s );
	if ( $est !== '' ) {
		$out[ 'estudiar-en-' . $s ] = array(
			'slug'      => 'estudiar-en-' . $s . '-que-enviar-desde-peru',
			'titulo'    => 'Estudiar en ' . $p . ': qué enviar desde {{origen_pais}} y cuándo',
			'categoria' => 'guias-de-envio', 'pilar' => 'envio-de-equipaje',
			'extracto'  => 'Para estudiantes que se van a ' . $p . ': qué documentos y qué cosas conviene enviar, en qué fechas según el calendario de allí y cómo prepararlo.',
			'html'      => '
<p>Irse a estudiar a ' . $p . ' es viajar con más cosas de las que caben en una maleta: libros, ropa de toda la temporada, documentos originales. Lo que no va contigo puede ir por delante, y lo que olvidaste puede llegar después.</p>
<h2>El calendario de allí</h2>
<p>' . $est . ' Cuenta hacia atrás desde la fecha de inicio: el equipaje tiene que llegar antes que tú o en tu primera semana.</p>
<h2>Documentos</h2>
<p>' . $x['apostilla'] . ' Envía los originales con número de guía y quédate con copias escaneadas.</p>
<h2>Qué enviar y qué llevar</h2>
<ul class="gr-pseo-list">
<li><strong>Por delante:</strong> libros, ropa de la estación siguiente, objetos personales que no necesitas al llegar.</li>
<li><strong>Contigo:</strong> documentos de viaje, laptop y medicinas de uso diario.</li>
<li><strong>Medicinas:</strong> ' . $r['medicinas'] . '</li>
<li><strong>Aparatos con batería:</strong> ' . $r['baterias'] . '</li>
</ul>
<h2>El clima al que llegas</h2>
<p>' . $x['clima'] . '</p>
<p>Prepara el envío con nuestro servicio de <a href="%H%/servicios/envio-de-equipaje/">envío de equipaje a ' . $p . '</a>. Si además te mudas, lee <a href="' . $guia( 'mudarse-a-' . $s . '-enviar-equipaje' ) . '">mudarse a ' . $p . ' y enviar tu equipaje por delante</a>.</p>
' . $faq( array(
	array( '¿Cuándo empieza el año académico en ' . $p . '?', $est ),
	array( '¿Puedo enviar libros y ropa a ' . $p . ' antes de viajar?', 'Sí: es lo más habitual. Declara el contenido pieza por pieza como efectos personales usados.' ),
) ),
		);
	}

	/* ── 5. Avisar al destinatario → contacto ───────────────────────── */
	$out[ 'avisar-a-tu-destinatario-en-' . $s ] = array(
		'slug'      => 'como-avisar-a-tu-destinatario-en-' . $s,
		'titulo'    => 'Cómo avisar a tu destinatario en ' . $p . ': datos, horario y entrega',
		'categoria' => 'guias-de-envio', 'pilar' => 'contacto',
		'extracto'  => 'Qué datos pedirle a quien recibe en ' . $p . ', cuándo avisarle según la hora de allí y qué necesita para ' . ( $casa ? 'recibir a domicilio' : 'retirar en agencia' ) . '.',
		'html'      => '
<p>Un envío llega bien cuando quien lo recibe sabe que viene. La mitad de los retrasos en el último tramo son un destinatario que no contesta o que no sabía que tenía que ' . ( $casa ? 'estar en casa' : 'ir a la agencia' ) . '.</p>
<h2>Los datos que necesitas de él</h2>
<ul class="gr-pseo-list">
<li><strong>Nombre completo</strong> como en su ' . $pf['documento'] . '.</li>
<li><strong>Teléfono con prefijo</strong>' . ( $pref ? ' ' . $pref : '' ) . ', a ser posible con WhatsApp.</li>
<li><strong>Dirección</strong> en el formato de ' . $p . ': ' . lcfirst( $pf['direccion']['formato'] ) . '.</li>
</ul>
<h2>Cuándo avisarle</h2>
<p>' . $x['horario'] . ' Avísale tres veces: cuando despachas (con el número de guía), cuando el envío entra a ' . $p . ' y cuando ' . ( $casa ? 'sale a reparto' : 'está en la agencia' ) . '.</p>
<h2>Lo que tiene que hacer al recibir</h2>
<p>' . $ultimo . '</p>
' . ( $c ? '<p>Llegamos a ' . esc_html( implode( ', ', array_slice( $c, 0, 5 ) ) ) . ' y otras ciudades; si tu destinatario está fuera de ellas, consúltanos antes.</p>' : '' ) . '
<p>¿Dudas antes de despachar? <a href="%H%/contacto/">Contáctanos para tu envío a ' . $p . '</a>. Y para escribir bien la dirección, <a href="' . $guia( 'como-escribir-una-direccion-en-' . $s ) . '">cómo escribir una dirección en ' . $p . '</a>.</p>
' . $faq( array(
	array( '¿Qué datos necesito del destinatario en ' . $p . '?', 'Nombre completo como en su ' . $pf['documento'] . ', teléfono con prefijo' . ( $pref ? ' ' . $pref : '' ) . ' y la dirección en el formato de ' . $p . '.' ),
	array( '¿Qué hora es en ' . $p . ' respecto a Lima?', $x['horario'] ),
) ),
	);

	/* ── 6. Desde allí: pedir productos de Perú → ficha ─────────────── */
	$out[ 'pedir-productos-de-peru-desde-' . $s ] = array(
		'slug'      => 'vives-en-' . $s . '-como-pedir-productos-de-peru',
		'titulo'    => 'Pedir productos de Perú desde ' . $p . ': cómo recibirlos en casa',
		'categoria' => 'destinos', 'pilar' => 'destinos/' . $s,
		'extracto'  => 'Si vives en ' . $p . ' y quieres algo de Perú: quién lo entrega en {{origen_ciudad}}, qué datos das tú, qué no puede viajar y cómo lo recibes.',
		'html'      => '
<p>Este artículo es para quien está en ' . $p . ' y extraña algo de Perú, o necesita un documento, un repuesto o un producto que allí no encuentra. No hace falta que viajes: alguien en {{origen_pais}} lo entrega y tú lo recibes.</p>
<h2>Cómo funciona</h2>
<ol class="gr-pseo-steps">
<li><strong>Alguien en {{origen_pais}} prepara el envío.</strong> Un familiar, un amigo o el proveedor que te vende; lo deja en nuestra sede de {{origen_ciudad}} o pide <a href="%H%/recojo-a-domicilio-lima/">recojo a domicilio</a>.</li>
<li><strong>Tú das tus datos.</strong> Nombre como en tu ' . $pf['documento'] . ', teléfono y dirección en el formato de ' . $p . '.</li>
<li><strong>Viaja.</strong> ' . $x['frontera'] . '</li>
<li><strong>Lo recibes.</strong> ' . $ultimo . '</li>
</ol>
<h2>Lo que más se pide desde ' . $p . '</h2>
<p>' . $ucf( $n['productos'] ) . '.</p>
<h2>Lo que no puede viajar</h2>
<ul class="gr-pseo-list">
<li><strong>Alimentos:</strong> ' . $r['alimentos'] . '</li>
<li><strong>Medicinas:</strong> ' . $r['medicinas'] . '</li>
<li><strong>Aparatos con batería:</strong> ' . $r['baterias'] . '</li>
</ul>
<h2>Al llegar a ' . $p . '</h2>
<p>' . $x['regimen'] . '</p>
<p>Plazos, vías y condiciones, en la ficha de ' . $ficha . '. Si quien te envía no conoce el proceso, compártele <a href="' . $guia( 'como-enviar-un-paquete-a-' . $s ) . '">la guía de su primer envío a ' . $p . '</a>.</p>
' . $faq( array(
	array( '¿Puedo pedir algo de Perú si vivo en ' . $p . '?', 'Sí: alguien en {{origen_pais}} lo entrega en nuestra sede de {{origen_ciudad}} o pide recojo a domicilio, y tú lo recibes con tus datos.' ),
	array( '¿Qué productos de Perú no pueden enviarse a ' . $p . '?', 'Alimentos: ' . $r['alimentos'] . ' Medicinas: ' . $r['medicinas'] ),
) ),
	);

	return $out;
}, 30, 3 );
