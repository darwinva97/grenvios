<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Guías de apoyo por país: una por página de servicio de la ruta
 * ══════════════════════════════════════════════════════════════════════════
 *
 * PEDIDO (2026-10-02): blog propio por país que dé autoridad, con enlaces
 * internos, a las páginas que deben posicionar en cada ruta: servicios y ficha
 * del destino.
 *
 * Cada guía responde a una búsqueda DISTINTA de la página que apoya (nunca su
 * keyword), la enlaza usando su keyword como texto del enlace y la declara su
 * pilar (grenvios_cluster_target). Así la página recibe también el enlace de
 * vuelta en «Guías relacionadas» y en «Del blog». En la ruta principal, la
 * maestra «…-desde-peru» cede la canónica a la copia del país
 * (inc/seo-canibalizacion.php).
 *
 *   carga-internacional   ← «Cómo enviar mercadería a X»
 *   envios-para-empresas  ← «Vender productos peruanos en X»
 *   envio-de-compras      ← «Compras en Perú para alguien en X»
 *   seguro-de-envios      ← «Valor declarado de un envío a X»
 *   ficha envios-a-X      ← «Aéreo o terrestre a X» / «Envío aéreo a X»
 *
 * Todo sale de datos del país (paises-perfil.php, paises-paginas-locales.php y
 * las notas de blog-paises-contenido.php). Sin tarifas ni plazos inventados.
 * Importar solo estas: grenvios_guias_pais_importar( grenvios_bpa2_claves() ).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function grenvios_bpa2_claves( $slug = '' ) {
	$slugs = $slug !== '' ? array( $slug ) : array_keys( function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array() );
	$out = array();
	foreach ( $slugs as $s ) {
		foreach ( array( 'enviar-mercaderia-a-', 'vender-productos-peruanos-en-', 'compras-en-peru-para-', 'valor-declarado-envio-a-', 'via-de-envio-a-' ) as $k ) $out[] = $k . $s;
	}
	return $out;
}

/* «Medicinas: …» · «Alimentos: …» · «Baterías: …» en una línea. */
function grenvios_bpa2_reglas_txt( $s, $d ) {
	if ( ! function_exists( 'grenvios_perfil_reglas' ) ) return '';
	$r = grenvios_perfil_reglas( $s, $d );
	$e = array( 'medicinas' => 'Medicinas', 'alimentos' => 'Alimentos', 'baterias' => 'Aparatos con batería' );
	$h = array();
	foreach ( $r as $k => $v ) $h[] = '<strong>' . ( isset( $e[ $k ] ) ? $e[ $k ] : ucfirst( $k ) ) . ':</strong> ' . $v;
	return implode( ' ', $h );
}

add_filter( 'grenvios_bp_plantillas', function ( $out, $d, $n ) {
	if ( ! function_exists( 'grenvios_perfil_de' ) || ! function_exists( 'grenvios_ppl_datos' ) ) return $out;
	$s  = $d['slug'];
	$pf = grenvios_perfil_de( $s );
	$xx = grenvios_ppl_datos();
	if ( ! $pf || empty( $xx[ $s ] ) ) return $out;

	$x     = $xx[ $s ];
	$p     = $d['title'];
	$f     = grenvios_bp_frases( $d );
	$dos   = $f['dos_vias'];
	$orig  = function_exists( 'grenvios_bp_origen_sufijo' ) ? grenvios_bp_origen_sufijo() : 'desde-peru';
	$guia  = function ( $k ) use ( $orig ) { return '%P:' . $k . '-' . $orig . '%'; };
	$faq   = function ( $pares ) { return function_exists( 'grenvios_ga_faq' ) ? grenvios_ga_faq( $pares ) : ''; };
	$ucf   = function ( $t ) { return mb_strtoupper( mb_substr( $t, 0, 1 ) ) . mb_substr( $t, 1 ); };
	$lcf   = function ( $t ) { return mb_strtolower( mb_substr( $t, 0, 1 ) ) . mb_substr( $t, 1 ); };
	$reglas = grenvios_bpa2_reglas_txt( $s, $d );
	$ficha = '<a href="%H%/destinos/' . $s . '/">envíos a ' . $p . '</a>';

	/* ── 1. Mercadería → carga internacional ────────────────────────── */
	$out[ 'enviar-mercaderia-a-' . $s ] = array(
		'titulo'    => 'Cómo enviar mercadería a ' . $p . ' desde {{origen_pais}}: permisos, aduana y ruta',
		'categoria' => 'empresas', 'pilar' => 'carga-internacional',
		'extracto'  => 'Lo que cambia cuando lo que envías a ' . $p . ' es mercadería y no un regalo: qué organismo la revisa, en qué régimen entra y por dónde viaja.',
		'html'      => '
<p>Un paquete personal y un envío de mercadería pueden pesar lo mismo y viajar por la misma ruta, pero ' . $p . ' no los trata igual. En cuanto hay varias unidades iguales, productos nuevos en caja o una factura de venta, el envío pasa a otro régimen y pide otros papeles. Saberlo antes separa una entrega a tiempo de una mercadería retenida.</p>
<h2>Quién revisa la mercadería en ' . $p . '</h2>
<p>' . $x['sanitario'] . '</p>
<p>Todo lo demás lo revisa ' . $pf['aduana'] . '. Si vendes alimentos, cosméticos o suplementos, el permiso sanitario pesa tanto como el flete: confirma con tu comprador que lo tiene antes de despachar.</p>
<h2>En qué régimen entra</h2>
<p>' . $x['regimen'] . '</p>
<h2>Por dónde viaja</h2>
<p>' . $x['frontera'] . ' ' . $n['via'] . '</p>
<h2>Lo que necesitas tener listo</h2>
<ul class="gr-pseo-list">
<li><strong>Factura o boleta</strong> con el valor real de cada producto.</li>
<li><strong>Lista de empaque</strong>: qué va en cada caja, cuántas unidades y su peso.</li>
<li><strong>Datos del comprador en ' . $p . '</strong>: nombre como en su ' . $pf['documento'] . ', dirección y teléfono.</li>
<li><strong>Permiso sanitario</strong>, si el producto lo requiere.</li>
</ul>
<p>Para volúmenes mayores tienes nuestro servicio de <a href="%H%/servicios/carga-internacional/">carga internacional a ' . $p . '</a>: consolidamos, documentamos y te confirmamos plazo y costo antes de despachar. Si vas a enviar con regularidad, lee también <a href="' . $guia( 'vender-productos-peruanos-en-' . $s ) . '">cómo vender productos peruanos en ' . $p . '</a>.</p>
' . $faq( array(
	array( '¿Puedo enviar mercadería a ' . $p . ' como paquete personal?', 'No conviene: si el contenido parece comercial, la aduana lo reclasifica y el envío se demora. ' . $x['regimen'] ),
	array( '¿Qué organismo revisa alimentos y cosméticos que llegan a ' . $p . '?', $x['sanitario'] ),
) ),
	);

	/* ── 2. Vender allí → envíos para empresas ──────────────────────── */
	$out[ 'vender-productos-peruanos-en-' . $s ] = array(
		'titulo'    => 'Vender productos peruanos en ' . $p . ': cómo abastecer a tus clientes con envíos regulares',
		'categoria' => 'empresas', 'pilar' => 'envios-para-empresas',
		'extracto'  => 'Para negocios que venden en ' . $p . ': quién compra productos peruanos allí, qué se vende más, en qué fechas y cómo organizar envíos regulares.',
		'html'      => '
<p>' . $pf['comunidad'] . ' Para un negocio peruano eso es un mercado: clientes que ya conocen el producto y lo buscan.</p>
<h2>Quién compra</h2>
<p>En esta ruta envían y compran sobre todo ' . $n['quien'] . '. Lo que más se mueve: ' . $n['productos'] . '.</p>
<h2>Cuándo se vende más</h2>
<p>' . $x['temporada'] . '</p>
<p>' . $x['feriados'] . ' Planifica la reposición contando esos días: un feriado allí detiene la aduana y el reparto aunque aquí sea día hábil.</p>
<h2>Envíos regulares sin sorpresas</h2>
<ol class="gr-pseo-steps">
<li><strong>Un mismo formato de caja.</strong> Con medidas fijas, el <a href="%H%/servicios/peso-volumetrico/">peso volumétrico</a> y el costo son previsibles.</li>
<li><strong>Documentación al día.</strong> Factura en cada envío y, si el producto lo pide, el permiso sanitario de ' . $p . '.</li>
<li><strong>Un calendario.</strong> Despachar en días fijos te permite prometer fechas a tus clientes.</li>
<li><strong>El horario de allí.</strong> ' . $x['horario'] . '</li>
</ol>
<p>Con <a href="%H%/envios-para-empresas/">envíos para empresas a ' . $p . '</a> fijamos contigo frecuencia, tarifa y documentación. Si el volumen crece, el siguiente paso es <a href="' . $guia( 'enviar-mercaderia-a-' . $s ) . '">enviar mercadería a ' . $p . ' como carga</a>.</p>
' . $faq( array(
	array( '¿Qué productos peruanos se venden más en ' . $p . '?', $ucf( $n['productos'] ) . '.' ),
	array( '¿Qué fechas conviene planificar para vender en ' . $p . '?', $x['temporada'] ),
) ),
	);

	/* ── 3. Compras → envío de compras ──────────────────────────────── */
	$out[ 'compras-en-peru-para-' . $s ] = array(
		'slug'      => 'enviar-compras-de-peru-a-' . $s,
		'titulo'    => 'Compras en {{origen_pais}} para alguien en ' . $p . ': cómo enviarlas sin pagar de más',
		'categoria' => 'guias-de-envio', 'pilar' => 'envio-de-compras',
		'extracto'  => 'Si compraste en {{origen_pais}} para alguien que vive en ' . $p . ': cómo declarar la compra, qué revisa su aduana y cómo armar la caja para pagar menos flete.',
		'html'      => '
<p>Comprar aquí y enviarlo es habitual: ropa, calzado, artesanía o algo que allí cuesta más o no se encuentra. La compra ya está hecha; lo que falta es que llegue sin pagar de más en el flete ni en la aduana de ' . $p . '.</p>
<h2>Cómo trata ' . $p . ' una compra nueva</h2>
<p>' . $x['regimen'] . '</p>
<p>Una compra nueva, con su caja y su etiqueta, se mira distinto que un regalo usado: guarda la boleta y declara el valor que figura en ella.</p>
<h2>Moneda y medidas</h2>
<p>' . $x['unidades'] . '</p>
<h2>Cómo pagar menos flete</h2>
<ul class="gr-pseo-list">
<li><strong>Quita las cajas originales voluminosas</strong> cuando el producto viaje protegido sin ellas: se cobra el mayor entre el peso real y el volumétrico.</li>
<li><strong>Junta todas las compras</strong> en un solo bulto: un envío, un flete.</li>
<li><strong>Deja la etiqueta</strong> en lo nuevo: ayuda a que la aduana lo valore bien.</li>
</ul>
<h2>Lo que no conviene comprar para enviar</h2>
<p>' . $reglas . '</p>
<p>Te ayudamos a consolidar y declarar tus compras con el servicio de <a href="%H%/servicios/envio-de-compras/">envío de compras a ' . $p . '</a>. Antes de comprar, revisa <a href="' . $guia( 'medicinas-alimentos-y-baterias-a-' . $s ) . '">qué puedes enviar a ' . $p . '</a>.</p>
' . $faq( array(
	array( '¿Paga impuestos en ' . $p . ' una compra hecha en {{origen_pais}}?', $x['regimen'] ),
	array( '¿Puedo quitar la caja original para pagar menos?', 'Sí, si el producto viaja protegido sin ella. Conserva la etiqueta y la boleta para la aduana.' ),
) ),
	);

	/* ── 4. Valor declarado → seguro de envíos ──────────────────────── */
	$out[ 'valor-declarado-envio-a-' . $s ] = array(
		'titulo'    => 'Valor declarado de un envío a ' . $p . ': cómo calcularlo para la aduana y el seguro',
		'categoria' => 'aduanas', 'pilar' => 'seguro-de-envios',
		'extracto'  => 'Qué valor declarar en un envío a ' . $p . ', en qué moneda, por qué de él dependen la aduana y el seguro, y qué pasa si se declara de menos.',
		'html'      => '
<p>El valor declarado es una cifra que escribes una vez y que decide dos cosas: lo que revisa ' . $pf['aduana'] . ' y lo que cubre el seguro si algo pasa en el camino. Declarar de menos para «ahorrar» suele salir caro dos veces.</p>
<h2>Qué valor poner</h2>
<p>El precio real de cada pieza: lo que pagaste si es nuevo, o un valor razonable de mercado si es usado. ' . $x['unidades'] . '</p>
<h2>Lo que hace la aduana de ' . $p . ' con ese valor</h2>
<p>' . $x['regimen'] . '</p>
' . ( $d['impuesto'] !== '' ? '<p>' . $f['impuesto'] . '</p>' : '' ) . '
<h2>Lo que cubre el seguro</h2>
<p>El seguro responde hasta el valor declarado: si declaraste la mitad de lo que vale, en caso de pérdida recuperas la mitad. Y el mejor seguro es el embalaje. ' . $x['clima'] . '</p>
<h2>Errores frecuentes</h2>
<ol>
<li>Poner «0» o «sin valor» en un regalo: para la aduana todo tiene un valor.</li>
<li>Un valor redondo para todo el bulto en vez de pieza por pieza.</li>
<li>No guardar la boleta de lo nuevo.</li>
</ol>
<p>Revisa cómo funciona el <a href="%H%/seguro-de-envios/">seguro para envíos a ' . $p . '</a> y, si dudas de un producto, <a href="' . $guia( 'que-se-puede-enviar-a-' . $s ) . '">qué retiene la aduana de ' . $p . '</a>.</p>
' . $faq( array(
	array( '¿En qué moneda declaro un envío a ' . $p . '?', $x['unidades'] ),
	array( '¿Qué pasa si declaro de menos un envío a ' . $p . '?', 'La aduana puede revisar y tasar el envío por su cuenta, con demora y posible multa, y el seguro solo cubre hasta el valor declarado.' ),
) ),
	);

	/* ── 5. Vía → ficha del destino ─────────────────────────────────── */
	if ( $dos ) {
		$out[ 'via-de-envio-a-' . $s ] = array(
			'slug'      => 'aereo-o-terrestre-a-' . $s . '-cual-conviene',
			'titulo'    => 'Aéreo o terrestre a ' . $p . ': cuál conviene según lo que envías',
			'categoria' => 'destinos', 'pilar' => 'destinos/' . $s,
			'extracto'  => 'Hacia ' . $p . ' hay dos vías: cuándo conviene la aérea, cuándo la terrestre y qué admite cada una.',
			'html'      => '
<p>' . $n['via'] . ' Elegir bien la vía es la decisión que más cambia el precio y el plazo de un envío a ' . $p . '.</p>
<h2>Por dónde va cada una</h2>
<p>' . $x['frontera'] . ' La vía aérea sale de {{origen_ciudad}} y es la que se usa para documentos y urgencias.</p>
<h2>Qué admite cada vía</h2>
<p>' . $reglas . '</p>
<h2>Cuál elegir</h2>
<ul class="gr-pseo-list">
<li><strong>Aérea</strong> si el envío es pequeño, son documentos o la fecha aprieta.</li>
<li><strong>Terrestre</strong> si abulta, pesa o lleva algo que el avión no admite.</li>
<li><strong>En temporada alta</strong>, sal antes con cualquiera de las dos: ' . $lcf( $x['temporada'] ) . '</li>
</ul>
<p>' . $n['consejo'] . ' Plazos y condiciones de cada vía, en la ficha de ' . $ficha . '.</p>
' . $faq( array(
	array( '¿Qué es más barato para enviar a ' . $p . ', aéreo o terrestre?', 'Para bultos voluminosos y pesados, normalmente la terrestre; en paquetes pequeños la diferencia es menor. Te damos las dos cifras al cotizar.' ),
	array( '¿Por dónde pasa el envío terrestre a ' . $p . '?', $x['frontera'] ),
) ),
		);
	} else {
		$r = function_exists( 'grenvios_perfil_reglas' ) ? grenvios_perfil_reglas( $s, $d ) : array( 'alimentos' => '', 'medicinas' => '' );
		$out[ 'via-de-envio-a-' . $s ] = array(
			'slug'      => 'envio-aereo-a-' . $s . '-como-preparar-la-caja',
			'titulo'    => 'Envío aéreo a ' . $p . ': cómo preparar la caja para la bodega del avión',
			'categoria' => 'destinos', 'pilar' => 'destinos/' . $s,
			'extracto'  => 'Hacia ' . $p . ' operamos por vía aérea: qué no admite la bodega de un avión, cómo reducir el volumen y cómo embalar para el trayecto.',
			'html'      => '
<p>' . $n['via'] . ' Al ser una ruta solo aérea mandan dos cosas: lo que admite la bodega del avión y el volumen de la caja, que en avión pesa mucho en la tarifa.</p>
<h2>El recorrido</h2>
<p>' . $x['frontera'] . '</p>
<h2>Lo que no sube al avión</h2>
<p>Líquidos, aerosoles, inflamables y aparatos con batería interna. <strong>Alimentos:</strong> ' . $r['alimentos'] . ' <strong>Medicinas:</strong> ' . $r['medicinas'] . '</p>
<h2>Cómo bajar el volumen</h2>
<ul class="gr-pseo-list">
<li><strong>Caja a medida</strong>: unos centímetros por lado cambian el <a href="%H%/servicios/peso-volumetrico/">peso volumétrico</a>.</li>
<li><strong>Ropa al vacío</strong>: es lo que más ahorra en una ruta aérea.</li>
<li><strong>Un solo bulto</strong> en lugar de varios.</li>
</ul>
<h2>Embalar para el trayecto</h2>
<p>' . $x['clima'] . '</p>
<p>' . $n['consejo'] . ' Plazos y condiciones, en la ficha de ' . $ficha . '.</p>
' . $faq( array(
	array( '¿Hay envío terrestre a ' . $p . '?', 'No: hacia ' . $p . ' operamos solo por vía aérea.' ),
	array( '¿Cómo pago menos en un envío aéreo a ' . $p . '?', 'Reduciendo el volumen: caja a medida, ropa al vacío y un solo bulto. En avión se cobra el mayor entre el peso real y el volumétrico.' ),
) ),
		);
	}

	return $out;
}, 25, 3 );
