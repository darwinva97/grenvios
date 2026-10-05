<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Guías locales por país: tres entradas que solo existen para ESE país
 * ══════════════════════════════════════════════════════════════════════════
 *
 * AUDITORÍA (2026-10-02): el blog de cada ruta tenía 30 entradas, pero 21 eran
 * la misma guía general con el país en el título: 92 % de texto en común entre
 * la versión de Ecuador y la de Colombia. Google se queda con una y deja las
 * otras fuera, así que la ruta no posicionaba nada local con ellas.
 *
 * Estas tres plantillas se construyen con los datos que cambian de verdad de
 * un país a otro (inc/paises-perfil.php y las notas de inc/blog-paises-contenido.php):
 *
 *   · cómo se escribe una dirección allí (formato, ejemplo, consejo, documento);
 *   · medicinas, alimentos y aparatos con batería (respuesta propia por país);
 *   · productos peruanos que más se envían y quién los envía.
 *
 * Se enganchan a `grenvios_bp_plantillas`, así que el importador de guías por
 * país las crea como las otras nueve: maestra en la ruta principal y copia en
 * la ruta del país. grenvios_guias_pais_importar( grenvios_bpl_claves() ).
 * Nada de cifras inventadas: plazos y precios, «te lo confirmamos al cotizar».
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Prefijo telefónico internacional de cada destino (dato público, no de tarifa). */
function grenvios_bpl_prefijo( $slug ) {
	$p = array( 'ecuador' => '+593', 'colombia' => '+57', 'chile' => '+56', 'bolivia' => '+591', 'argentina' => '+54', 'estados-unidos' => '+1', 'espana' => '+34', 'venezuela' => '+58', 'cuba' => '+53' );
	return isset( $p[ $slug ] ) ? $p[ $slug ] : '';
}

/* Claves de las plantillas de este módulo para un país (o para todos). */
function grenvios_bpl_claves( $slug = '' ) {
	$slugs = $slug !== '' ? array( $slug ) : array_keys( function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array() );
	$out = array();
	foreach ( $slugs as $s ) {
		$out[] = 'como-escribir-una-direccion-en-' . $s;
		$out[] = 'medicinas-alimentos-y-baterias-a-' . $s;
		$out[] = 'productos-peruanos-a-' . $s;
	}
	return $out;
}

add_filter( 'grenvios_bp_plantillas', function ( $out, $d, $n ) {
	if ( ! function_exists( 'grenvios_perfil_de' ) ) return $out;
	$s  = $d['slug'];
	$pf = grenvios_perfil_de( $s );
	if ( ! $pf ) return $out;

	$p     = $d['title'];
	$f     = grenvios_bp_frases( $d );
	$casa  = ! empty( $d['casa'] );
	$dos   = $f['dos_vias'];
	$c     = $f['ciudades'];
	$pilar = 'destinos/' . $s;
	$dir   = isset( $pf['direccion'] ) ? $pf['direccion'] : array();
	$reg   = function_exists( 'grenvios_perfil_reglas' ) ? grenvios_perfil_reglas( $s, $d ) : array();
	$pref  = grenvios_bpl_prefijo( $s );
	$orig  = function_exists( 'grenvios_bp_origen_sufijo' ) ? grenvios_bp_origen_sufijo() : 'desde-peru';
	$faq   = function ( $pares ) { return function_exists( 'grenvios_ga_faq' ) ? grenvios_ga_faq( $pares ) : ''; };
	$ucf   = function ( $t ) { return function_exists( 'mb_strtoupper' ) ? mb_strtoupper( mb_substr( $t, 0, 1 ) ) . mb_substr( $t, 1 ) : ucfirst( $t ); };

	/* ── 1. Dirección ────────────────────────────────────────────────── */
	if ( ! empty( $dir['formato'] ) ) {
		$ciud = $c ? '<p>Llegamos a ' . esc_html( implode( ', ', array_slice( $c, 0, 6 ) ) ) . ( count( $c ) > 6 ? ' y otras ciudades' : '' ) . '. Si la tuya no está en la lista, pregúntanos antes de despachar; tienes el detalle en <a href="%P:ciudades-de-' . $s . '-' . $orig . '%">envíos a las ciudades de ' . $p . '</a>.</p>' : '';
		$out[ 'como-escribir-una-direccion-en-' . $s ] = array(
			'titulo'    => 'Cómo escribir una dirección en ' . $p . ' para que tu envío llegue a la primera',
			'categoria' => 'destinos', 'pilar' => $pilar,
			'extracto'  => 'El formato de dirección que se usa en ' . $p . ', un ejemplo bien escrito y los datos del destinatario que evitan reintentos y retenciones.',
			'html'      => '
<p>La mayoría de los envíos que se demoran en el último tramo no tienen un problema de transporte: tienen una dirección escrita como se escribiría en {{origen_pais}}. Cada país ordena la dirección a su manera, y en ' . $p . ' hay un detalle que decide si el envío llega a la primera.</p>
<h2>El formato que se usa en ' . $p . '</h2>
<p>' . $dir['formato'] . '.</p>
<h2>Un ejemplo bien escrito</h2>
<blockquote class="gr-msg">' . $dir['ejemplo'] . '</blockquote>
<p>' . $dir['consejo'] . '</p>
<h2>' . ( $casa ? 'Entrega a domicilio: lo que necesita el repartidor' : 'Retiro en agencia: lo que necesita tu destinatario' ) . '</h2>
' . ( $casa
	? '<p>A ' . $p . ' entregamos en el domicilio, así que la dirección es la que guía al repartidor. El nombre del destinatario debe coincidir con el de su ' . $pf['documento'] . ' y el teléfono tiene que contestar: es lo que permite resolver una referencia confusa sin devolver el envío.</p>'
	: '<p>En ' . $p . ' el envío se retira en la agencia local. La dirección sirve para asignar la agencia más cercana y para avisar al destinatario; lo que decide la entrega es que el nombre del envío coincida exactamente con el de su ' . $pf['documento'] . ', que tendrá que presentar al retirarlo.</p>' ) . '
' . $ciud . '
<h2>Lista de comprobación antes de despachar</h2>
<ul class="gr-pseo-list">
<li><strong>Nombre completo</strong> tal como figura en su ' . $pf['documento'] . '.</li>
<li><strong>Teléfono con prefijo internacional</strong>' . ( $pref ? ' (' . $pref . ')' : '' ) . ' y, si puede, con WhatsApp.</li>
<li><strong>Dirección en el orden de ' . $p . '</strong>, no en el de {{origen_pais}}.</li>
<li><strong>Una referencia</strong> visible: un comercio, un parque, el color de la fachada.</li>
<li><strong>Ciudad y ' . ( in_array( $s, array( 'chile' ), true ) ? 'comuna y región' : ( in_array( $s, array( 'colombia', 'bolivia' ), true ) ? 'departamento' : ( $s === 'estados-unidos' ? 'estado y código postal (ZIP)' : 'provincia o estado' ) ) ) . '</strong>.</li>
</ul>
<p>Con estos datos listos, <a href="%H%/cotizar/">cotiza tu envío a ' . $p . '</a>: te confirmamos precio y plazo en la misma respuesta.</p>
' . $faq( array(
	array( '¿Qué pasa si la dirección en ' . $p . ' está incompleta?', 'El envío puede quedar a la espera hasta que se complete el dato, y eso suma días. Por eso revisamos la dirección y el teléfono contigo al despachar, antes de que salga de {{origen_ciudad}}.' ),
	array( '¿Puedo enviar a nombre de otra persona en ' . $p . '?', 'Sí, siempre que el nombre del envío coincida con el de su ' . $pf['documento'] . ( $casa ? ' y alguien pueda recibirlo en la dirección.' : ', porque se lo pedirán al retirar en la agencia.' ) ),
	array( '¿Hace falta escribir la dirección en algún idioma o formato especial?', 'Escríbela como se escribe en ' . $p . ', con el orden de arriba y sin abreviaturas inventadas. Si tienes una foto del recibo de luz o de una carta que llegó a esa dirección, cópiala tal cual.' ),
) ),
		);
	}

	/* ── 2. Medicinas, alimentos y aparatos con batería ──────────────── */
	if ( $reg ) {
		$si = function ( $t ) { return stripos( $t, 'sí' ) === 0; };
		$out[ 'medicinas-alimentos-y-baterias-a-' . $s ] = array(
			'slug'      => 'enviar-medicinas-alimentos-o-baterias-a-' . $s,
			'titulo'    => 'Medicinas, alimentos y aparatos con batería: qué puedes enviar a ' . $p,
			'categoria' => 'aduanas', 'pilar' => 'que-se-puede-enviar',
			'extracto'  => 'Respuesta directa para ' . $p . ': si se pueden enviar medicinas, alimentos y aparatos con batería, por qué vía y qué llevar para que no se retengan.',
			'html'      => '
<p>Son las tres consultas que más recibimos para ' . $p . ', y las tres tienen una respuesta distinta según la vía. ' . ( $dos ? 'Hacia ' . $p . ' operamos por aire y por carretera, y la carretera admite cosas que el avión no.' : 'Hacia ' . $p . ' operamos por vía aérea, y la bodega de un avión es la que pone el límite.' ) . ' Esto es lo que aplica hoy en la ruta.</p>
<div class="gr-table-wrap"><table class="gr-table"><thead><tr><th scope="col">Producto</th><th scope="col">¿Se puede enviar a ' . $p . '?</th></tr></thead><tbody>
<tr><th scope="row">Medicinas</th><td>' . $reg['medicinas'] . '</td></tr>
<tr><th scope="row">Alimentos</th><td>' . $reg['alimentos'] . '</td></tr>
<tr><th scope="row">Aparatos con batería</th><td>' . $reg['baterias'] . '</td></tr>
</tbody></table></div>
<h2>Medicinas</h2>
<p>' . ( $si( $reg['medicinas'] )
	? 'Se pueden enviar, y es algo que se manda a menudo a ' . $p . '. Cada medicamento va en su envase original, con la receta médica a nombre de quien lo va a tomar y en cantidad de tratamiento personal: una caja de más se lee como mercadería.'
	: 'Por esta ruta no las aceptamos. Si se trata de un tratamiento que no se consigue allí, escríbenos antes de comprarlo: te decimos si hay una alternativa y no pagas por algo que no va a viajar.' ) . '</p>
<h2>Alimentos</h2>
<p>' . ( $si( $reg['alimentos'] )
	? 'Solo sellados de fábrica y no perecibles, con su etiqueta y fecha de vencimiento a la vista: café, ají en frasco, galletas, chocolates. Nada casero ni abierto, que es lo que la aduana de ' . $p . ' retiene.'
	: 'Por avión no viajan alimentos, ni sellados. Es la consulta que más se repite antes de fiestas: mejor saberlo antes de armar la caja que en la revisión del despacho.' ) . '</p>
<h2>Celulares, laptops y otros aparatos con batería</h2>
<p>' . $reg['baterias'] . ' Si el aparato es nuevo, guarda la boleta: el valor declarado se revisa con ella. Más detalle en <a href="%H%/servicios/envio-de-celulares-y-laptops/">envío de celulares y laptops</a>.</p>
<h2>Cómo declararlos para que no se retengan</h2>
<ul class="gr-pseo-list">
<li><strong>Pieza por pieza</strong>: «2 cajas de paracetamol», no «medicinas».</li>
<li><strong>Valor real</strong>: en ' . $p . ' se declara en ' . $pf['moneda'] . '. Declarar de menos no abarata y deja el envío sin <a href="%H%/seguro-de-envios/">seguro</a>.</li>
<li><strong>La documentación a la vista</strong>: receta, boleta o etiqueta de fábrica, según el caso.</li>
</ul>
<p>' . $n['consejo'] . ' Ante la duda con un producto concreto, dinos su nombre exacto y te respondemos si entra y por qué vía. <a href="%H%/cotizar/">Consulta y cotiza tu envío a ' . $p . '</a>.</p>
' . $faq( array(
	array( '¿Puedo enviar medicinas a ' . $p . '?', $reg['medicinas'] ),
	array( '¿Puedo enviar alimentos peruanos a ' . $p . '?', $reg['alimentos'] ),
	array( '¿Puedo enviar un celular o una laptop a ' . $p . '?', $reg['baterias'] ),
) ),
		);
	}

	/* ── 3. Productos peruanos que más se envían ─────────────────────── */
	if ( ! empty( $n['productos'] ) && ! empty( $pf['comunidad'] ) ) {
		$out[ 'productos-peruanos-a-' . $s ] = array(
			'titulo'    => 'Productos peruanos para enviar a ' . $p . ': lo que más se manda y cómo prepararlo',
			'categoria' => 'destinos', 'pilar' => $pilar,
			'extracto'  => 'Qué productos de {{origen_pais}} se envían más a ' . $p . ', quién los manda, cuáles no pasan y cómo prepararlos para que lleguen bien.',
			'html'      => '
<p>' . $pf['comunidad'] . '</p>
<h2>Quién envía a ' . $p . '</h2>
<p>En esta ruta envían sobre todo ' . $n['quien'] . '. Cada perfil manda cosas distintas y con prisas distintas, y eso decide la vía.</p>
<h2>Lo que más se manda</h2>
<p>' . $ucf( $n['productos'] ) . '.</p>
<h2>Lo que no pasa (o solo por una vía)</h2>
<ul class="gr-pseo-list">
<li><strong>Alimentos:</strong> ' . ( isset( $reg['alimentos'] ) ? $reg['alimentos'] : 'consúltanos antes.' ) . '</li>
<li><strong>Medicinas:</strong> ' . ( isset( $reg['medicinas'] ) ? $reg['medicinas'] : 'consúltanos antes.' ) . '</li>
<li><strong>Aparatos con batería:</strong> ' . ( isset( $reg['baterias'] ) ? $reg['baterias'] : 'consúltanos antes.' ) . '</li>
</ul>
<h2>Artesanía, textiles y regalos</h2>
<p>Viajan sin problema si van bien protegidos: cerámica envuelta pieza por pieza, textiles en bolsa sellada contra la humedad. Si es para vender, decláralo como mercadería, no como regalo. Lo explicamos en <a href="%H%/servicios/envio-de-artesanias-al-extranjero/">envío de artesanías</a>.</p>
<h2>Cómo prepararlos</h2>
<ol class="gr-pseo-steps">
<li><strong>Agrupa en un solo bulto.</strong> Varios paquetes pequeños pagan varios fletes.</li>
<li><strong>Ajusta la caja al contenido.</strong> Se cobra el mayor entre el peso real y el <a href="%H%/servicios/peso-volumetrico/">volumétrico</a>.</li>
<li><strong>Haz la lista del contenido</strong> con el valor de cada cosa en ' . $pf['moneda'] . '.</li>
<li><strong>' . ( $casa ? 'Confirma la dirección' : 'Avisa a tu destinatario' ) . '</strong>: ' . ( $casa ? 'en ' . $p . ' entregamos a domicilio.' : 'en ' . $p . ' se retira en agencia con su ' . $pf['documento'] . '.' ) . '</li>
</ol>
<p>' . $n['via'] . ' <a href="%H%/cotizar/">Cotiza tu envío a ' . $p . '</a> y te confirmamos precio y plazo.</p>
' . $faq( array(
	array( '¿Qué productos peruanos se pueden enviar a ' . $p . '?', $ucf( $n['productos'] ) . '. Los alimentos y las medicinas dependen de la vía: revisa la lista de arriba.' ),
	array( '¿Tengo que declarar los regalos que envío a ' . $p . '?', 'Sí, todo se declara pieza por pieza con su valor. Un regalo mal declarado es la causa más común de retención en aduana.' ),
) ),
		);
	}

	return $out;
}, 20, 3 );
