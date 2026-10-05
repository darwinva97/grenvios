<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  CONTENIDO POR PAÍS — lo que hace única a cada página de una ruta
 * ══════════════════════════════════════════════════════════════════════════
 *
 * El duplicador copia las 24 páginas del sitio a la ruta de cada país. Si esas
 * copias se publican tal cual son nueve versiones del mismo texto cambiando el
 * prefijo: «doorway pages» de manual, que penalizan al dominio entero. Por eso
 * la compuerta las deja en `noindex` mientras sean idénticas a su original.
 *
 * Este módulo es lo que abre esa compuerta con contenido de verdad: monta, para
 * cada página y cada país, un bloque de secciones construido con los datos
 * reales de ese destino —plazos, modalidades, impuesto, forma de entrega— y lo
 * escribe en el contenido de la copia, donde la clienta puede reescribirlo.
 *
 * DOS REGLAS QUE NO SE SALTAN
 *
 * 1) No se inventan hechos. Las secciones se construyen con datos que ya están
 *    en el tema —los porcentajes de impuesto, los plazos y las modalidades los
 *    escribió la clienta— o con campos que ella rellena. Un campo vacío NO
 *    produce una sección con texto genérico: no produce sección. Publicar «la
 *    aduana de Cuba permite 10 kg» porque suena verosímil sería inventarle a un
 *    cliente una norma que le pueden rechazar en el mostrador.
 *
 * 2) Dos páginas del mismo país no llevan el mismo bloque. Cada página maestra
 *    tiene asignado su propio conjunto de secciones (ver `grenvios_pais_matriz`)
 *    y, cuando una sección se repite en dos páginas, entra con otro titular y
 *    otra entradilla según el ángulo de esa página. Si todas las páginas de Cuba
 *    llevaran la misma tabla de plazos, se habría cambiado la duplicación entre
 *    países por duplicación dentro del país.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ─────────────────────────────────────────────────────────────────────────
 * 1) DATOS DEL PAÍS
 * ───────────────────────────────────────────────────────────────────────── */

/* Campos que la clienta rellena por país (WordPress → Destinos). Los que no
 * rellene simplemente no pintan su sección. */
function grenvios_pais_campos() {
	return array(
		'ciudades'   => array( 'Ciudades con cobertura', 'Separadas por comas. Pinta la sección de cobertura.' ),
		'top'        => array( 'Lo que más se envía',    'Separado por comas: «medicinas, ropa, repuestos».' ),
		'prohibidos' => array( 'No se puede enviar',     'Separado por comas. Solo lo que sepas cierto de ESTE país.' ),
		'documentos' => array( 'Documentación exigida',  'Separado por comas: «copia del documento del destinatario».' ),
		'embalaje'   => array( 'Embalaje recomendado',   'Un párrafo con lo específico de la ruta a este país.' ),
		'comunidad'  => array( 'Contexto de la ruta',    'Un párrafo: quién envía a este país y por qué.' ),
	);
}

function grenvios_pais_extra_all() {
	$o = get_option( 'grenvios_pais_extra', array() );
	return is_array( $o ) ? $o : array();
}

function grenvios_pais_extra_save( $arr ) {
	update_option( 'grenvios_pais_extra', (array) $arr );
}

/* Datos completos de un país destino: los del tema + los campos editables.
 * $ref puede ser el slug del destino («cuba») o el código de ruta («cu»). */
function grenvios_pais_datos( $ref ) {
	$slug = $ref;
	if ( function_exists( 'grenvios_sede_destino_propio' ) ) {
		$d = grenvios_sede_destino_propio( $ref );
		if ( $d !== '' ) $slug = $d;
	}
	$dest = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();
	if ( ! isset( $dest[ $slug ] ) ) return array();

	$d = $dest[ $slug ];
	foreach ( array( 'title', 'tiempo', 'modos', 'entrega', 'restr', 'lead' ) as $k ) {
		if ( ! isset( $d[ $k ] ) ) $d[ $k ] = '';
	}
	$x = grenvios_pais_extra_all();
	$e = isset( $x[ $slug ] ) ? (array) $x[ $slug ] : array();
	foreach ( grenvios_pais_campos() as $k => $_ ) $d[ $k ] = isset( $e[ $k ] ) ? trim( (string) $e[ $k ] ) : '';

	$d['slug']  = $slug;
	$d['aereo'] = ( stripos( $d['modos'], 'aére' ) !== false || stripos( $d['modos'], 'aere' ) !== false );
	$d['terr']  = ( stripos( $d['modos'], 'terrestre' ) !== false );
	$d['casa']  = ( stripos( $d['entrega'], 'domicilio' ) !== false || stripos( $d['entrega'], 'puerta' ) !== false );
	// El porcentaje real que la clienta escribió en las notas del destino.
	$d['impuesto'] = preg_match( '/(\d+(?:[.,]\d+)?)\s*%/u', (string) $d['restr'], $m ) ? str_replace( ',', '.', $m[1] ) : '';
	/* Filtro `grenvios_pais_datos`: permite completar lo que el gestor aún no
	 * tiene relleno. Lo usa inc/destinos-ciudades.php con las ciudades de cada
	 * país; lo que la clienta escriba siempre manda sobre el valor por defecto. */
	return apply_filters( 'grenvios_pais_datos', $d );
}

function grenvios_pais_lista( $txt ) {
	return array_values( array_filter( array_map( 'trim', explode( ',', (string) $txt ) ) ) );
}

function grenvios_pais_frase_lista( $arr ) {
	$n = count( $arr );
	if ( ! $n ) return '';
	if ( $n === 1 ) return $arr[0];
	return implode( ', ', array_slice( $arr, 0, $n - 1 ) ) . ' y ' . $arr[ $n - 1 ];
}

/* Nombre legible de una ruta: el país al que envía, no la «lengua» de Polylang. */
function grenvios_col_pais_nombre( $lang ) {
	if ( $lang === '' ) return '';
	$maestra = function_exists( 'grenvios_i18n_default' ) && grenvios_i18n_default() === $lang;

	/* El nombre de la sede es el que Polylang le puso a la «lengua», y para la
	 * ruta principal eso es «Español», no «Perú». Para el origen se usa el país
	 * real; para una ruta de destino, el nombre del país al que envía. */
	if ( $maestra && function_exists( 'grenvios_sede_pais_nombre' ) ) {
		$o = grenvios_sede_pais_nombre( $lang );
		if ( $o !== '' ) return $o;
	}
	if ( function_exists( 'grenvios_sede_destino_nombre' ) ) {
		$d = grenvios_sede_destino_nombre( $lang );
		if ( $d !== '' ) return $d;
	}
	if ( function_exists( 'grenvios_sedes' ) ) {
		$s = grenvios_sedes();
		if ( isset( $s[ $lang ]['name'] ) ) return $s[ $lang ]['name'];
	}
	return $lang;
}

/* Etiqueta completa, con el matiz de qué es esa ruta. */
function grenvios_col_pais_etiqueta( $lang ) {
	$n = grenvios_col_pais_nombre( $lang );
	if ( function_exists( 'grenvios_i18n_default' ) && grenvios_i18n_default() === $lang ) return $n . ' · sitio principal';
	if ( function_exists( 'grenvios_es_ruta_pais' ) && ! grenvios_es_ruta_pais( $lang ) ) return $n . ' · idioma';
	return $n;
}

/* ─────────────────────────────────────────────────────────────────────────
 * 2) SECCIONES
 *
 * Cada función recibe los datos del país y el «ángulo» —el slug maestro de la
 * página donde va— y devuelve '' si le faltan datos. El ángulo cambia titular y
 * entradilla: la misma tabla de plazos se presenta distinto en la página de
 * tiempos que en la de cotizar.
 * ───────────────────────────────────────────────────────────────────────── */

function grenvios_pais_sec( $clase, $titulo, $cuerpo ) {
	if ( trim( (string) $cuerpo ) === '' ) return '';
	return '<section class="srv-section gr-pais-sec ' . esc_attr( $clase ) . ' padding"><div class="container">'
		. '<div class="gr-pais-head text-center"><h2>' . $titulo . '</h2></div>'
		. '<div class="gr-pais-body">' . $cuerpo . '</div>'
		. '</div></section>';
}

/* Cómo llega el envío: modalidades reales de ese país. */
function grenvios_pais_seccion_ruta( $d, $ang ) {
	if ( ! $d['aereo'] && ! $d['terr'] ) return '';
	$p   = esc_html( $d['title'] );
	$tit = array(
		'home'                                 => 'Cómo llega tu envío a ' . $p,
		'nosotros'                             => 'Nuestra ruta a ' . $p,
		'recojo-a-domicilio-lima'              => 'Del recojo en {{origen_ciudad}} a la entrega en ' . $p,
		'envios-desde-provincias'              => 'Desde provincias hasta ' . $p,
		'como-enviar-un-paquete-al-extranjero' => 'Las vías disponibles hacia ' . $p,
		'servicios'                            => 'Modalidades disponibles hacia ' . $p,
		'tiempos-de-entrega'                   => 'Por qué el plazo a ' . $p . ' es el que es',
	);
	$h = isset( $tit[ $ang ] ) ? $tit[ $ang ] : 'La ruta a ' . $p;

	$vias = '';
	if ( $d['aereo'] ) $vias .= '<li><strong>Vía aérea.</strong> Es la opción rápida hacia ' . $p
		. '. Conviene cuando el envío urge, pesa poco o su valor justifica el flete: documentos, medicinas, muestras y repuestos pequeños.</li>';
	if ( $d['terr'] ) $vias .= '<li><strong>Vía terrestre.</strong> Es la opción económica hacia ' . $p
		. '. Rinde cuando el paquete abulta o pesa y la fecha no aprieta: ropa, menaje, encomiendas familiares y carga.</li>';

	$c = ( $d['lead'] !== '' ? '<p>' . esc_html( $d['lead'] ) . '</p>' : '' )
		. '<ul class="srv-list">' . $vias . '</ul>'
		. '<p>Sea cual sea la vía, el envío se recoge o se entrega en {{origen_ciudad}} y el destinatario lo recibe '
		. ( $d['casa'] ? 'en su propio domicilio' : 'en la agencia local que le corresponde' ) . ' en ' . $p
		. ( $d['tiempo'] !== '' ? ', en un plazo de ' . esc_html( $d['tiempo'] ) : '' ) . '.</p>';
	return grenvios_pais_sec( '', $h, $c );
}

/* Tabla de plazos por modalidad. */
function grenvios_pais_seccion_plazos( $d, $ang ) {
	if ( trim( (string) $d['tiempo'] ) === '' ) return '';
	$p   = esc_html( $d['title'] );
	$tit = array(
		'tiempos-de-entrega'                => 'Cuánto demora un envío a ' . $p,
		'cotizar'                           => 'Qué plazo entra en tu cotización a ' . $p,
		'rastreo-de-envios'                 => 'Qué plazo esperar mientras rastreas',
		'preguntas-frecuentes'              => 'Plazos de entrega a ' . $p,
		'envios-desde-provincias'           => 'Plazos desde provincias a ' . $p,
		'apostilla-y-traduccion'            => 'Plazos del envío una vez apostillado',
		'envio-internacional-de-documentos' => 'Cuánto tarda un documento en llegar a ' . $p,
		'envio-internacional-de-paquetes'   => 'Cuánto tarda un paquete en llegar a ' . $p,
	);
	$h = isset( $tit[ $ang ] ) ? $tit[ $ang ] : 'Plazos hacia ' . $p;

	$filas = '';
	if ( $d['aereo'] ) $filas .= '<tr><td>Aéreo</td><td>' . esc_html( $d['tiempo'] ) . '</td><td>'
		. esc_html( $d['entrega'] ) . '</td><td>Documentos y urgencias</td></tr>';
	if ( $d['terr'] ) $filas .= '<tr><td>Terrestre</td><td>' . esc_html( $d['tiempo'] ) . '</td><td>'
		. esc_html( $d['entrega'] ) . '</td><td>Paquetes, mudanzas y carga</td></tr>';
	if ( $filas === '' ) return '';

	$c = '<p>El plazo hacia ' . $p . ' es de <strong>' . esc_html( $d['tiempo'] )
		. '</strong>, y se cuenta desde que el envío sale de {{origen_ciudad}}, no desde que lo entregas en el mostrador.</p>'
		. '<div class="table-responsive"><table class="gr-tabla"><thead><tr><th>Modalidad</th><th>Plazo</th><th>Entrega</th><th>Para qué conviene</th></tr></thead><tbody>'
		. $filas . '</tbody></table></div>'
		. '<p>Los trámites de aduana en ' . $p . ' y los feriados locales pueden alargarlo; cuando ocurre, se refleja en el número de seguimiento.</p>';
	return grenvios_pais_sec( 'bg-grey', $h, $c );
}

/* Impuesto real del país, tomado de las notas del destino. */
function grenvios_pais_seccion_impuestos( $d, $ang ) {
	if ( trim( (string) $d['restr'] ) === '' ) return '';
	$p   = esc_html( $d['title'] );
	$tit = array(
		'aduanas-e-impuestos'  => 'La aduana de ' . $p . ' y lo que se paga',
		'envio-de-compras'     => 'Impuestos al enviar tus compras a ' . $p,
		'carga-internacional'  => 'Impuestos de la carga a ' . $p,
		'envio-de-equipaje'    => 'Impuestos al enviar equipaje a ' . $p,
		'seguro-de-envios'     => 'Valor declarado, seguro e impuesto en ' . $p,
		'envios-para-empresas' => 'Costes aduaneros de tus envíos a ' . $p,
		'preguntas-frecuentes' => '¿Qué se paga por enviar a ' . $p . '?',
	);
	$h = isset( $tit[ $ang ] ) ? $tit[ $ang ] : 'Impuestos en el envío a ' . $p;

	$c = '<p>' . wp_kses_post( $d['restr'] ) . '</p>';
	if ( $d['impuesto'] !== '' ) {
		$c .= '<p>El impuesto se calcula sobre el <strong>valor declarado</strong>, no sobre lo que pesa el bulto. '
			. 'Por eso declararlo bien —ni de más ni de menos— es lo que evita sorpresas: declarar de menos expone el envío '
			. 'a una revisión y a que sea el destinatario quien responda en ' . $p . '.</p>';
	}
	$c .= '<p>Se cancela en {{origen_ciudad}} al despachar, de modo que quien recibe en ' . $p
		. ' no tiene que pagar nada para retirar su envío.</p>';
	return grenvios_pais_sec( '', $h, $c );
}

/* Cómo lo recibe el destinatario. */
function grenvios_pais_seccion_entrega( $d, $ang ) {
	if ( trim( (string) $d['entrega'] ) === '' ) return '';
	$p = esc_html( $d['title'] );
	$h = ( $ang === 'contacto' ) ? 'Dónde recibe tu destinatario en ' . $p : 'Cómo se entrega en ' . $p;
	$c = $d['casa']
		? '<p>A ' . $p . ' entregamos <strong>a domicilio</strong>: el destinatario no se traslada a ninguna agencia. '
		  . 'Para que la entrega salga a la primera hacen falta la dirección completa con una referencia y un teléfono local que conteste.</p>'
		: '<p>En ' . $p . ' la entrega es <strong>en agencia local</strong>: cuando el envío llega, el destinatario recibe el aviso y '
		  . 'lo retira con su documento de identidad. Por eso su nombre debe coincidir exactamente con el del documento.</p>';
	$c .= '<p>El número de seguimiento se entrega al despachar y muestra el estado del envío durante todo el trayecto hasta ' . $p . '.</p>';
	return grenvios_pais_sec( 'bg-grey', $h, $c );
}

/* Aéreo frente a terrestre, con los datos de ese país. */
function grenvios_pais_seccion_comparativa( $d, $ang ) {
	if ( ! ( $d['aereo'] && $d['terr'] ) ) return '';
	$p   = esc_html( $d['title'] );
	$tit = array(
		'peso-volumetrico'     => 'Peso volumétrico: cuándo cambia la vía a ' . $p,
		'carga-internacional'  => 'Aéreo o terrestre para tu carga a ' . $p,
		'cotizar'              => 'Qué vía te conviene cotizar hacia ' . $p,
		'envios-para-empresas' => 'Qué vía usan las empresas que envían a ' . $p,
		'servicios'            => 'Aéreo o terrestre hacia ' . $p,
	);
	$h = isset( $tit[ $ang ] ) ? $tit[ $ang ] : 'Aéreo o terrestre a ' . $p;
	$c = '<p>Hacia ' . $p . ' operamos las dos vías, y la elección casi nunca es de gusto: la decide el peso frente al volumen.</p>'
		. '<div class="table-responsive"><table class="gr-tabla"><thead><tr><th></th><th>Aéreo</th><th>Terrestre</th></tr></thead><tbody>'
		. '<tr><td>Coste</td><td>Mayor</td><td>Menor</td></tr>'
		. '<tr><td>Conviene para</td><td>Poco peso, mucha urgencia</td><td>Mucho volumen, sin prisa</td></tr>'
		. '<tr><td>Típico</td><td>Documentos, medicinas, muestras</td><td>Ropa, menaje, encomiendas, carga</td></tr>'
		. '</tbody></table></div>'
		. '<p>Un bulto ligero pero voluminoso paga por el espacio que ocupa, no por lo que pesa. Si tu envío a ' . $p
		. ' abulta, la vía terrestre suele salir bastante mejor; si cabe en un sobre y corre prisa, la aérea.</p>';
	return grenvios_pais_sec( '', $h, $c );
}

/* ── Secciones que dependen de campos editables: sin dato, no hay sección ── */

function grenvios_pais_seccion_top( $d, $ang ) {
	$l = grenvios_pais_lista( $d['top'] );
	if ( ! $l ) return '';
	$p   = esc_html( $d['title'] );
	$tit = array(
		'blog'                            => 'Guías según lo que envías a ' . $p,
		'que-se-puede-enviar'             => 'Lo que sí se envía a ' . $p,
		'envio-internacional-de-paquetes' => 'Qué va dentro de los paquetes a ' . $p,
	);
	$h = isset( $tit[ $ang ] ) ? $tit[ $ang ] : 'Lo que más se envía a ' . $p;
	$c = '<p>Por nuestra ruta a ' . $p . ' pasa sobre todo ' . esc_html( grenvios_pais_frase_lista( $l ) ) . '.</p><ul class="srv-list">';
	foreach ( $l as $i ) $c .= '<li>' . esc_html( ucfirst( $i ) ) . '</li>';
	return grenvios_pais_sec( 'bg-grey', $h, $c . '</ul>' );
}

function grenvios_pais_seccion_prohibidos( $d, $ang ) {
	$l = grenvios_pais_lista( $d['prohibidos'] );
	if ( ! $l ) return '';
	$p = esc_html( $d['title'] );
	$h = ( $ang === 'envio-de-alimentos' ) ? 'Alimentos que no entran en ' . $p : 'Lo que no se puede enviar a ' . $p;
	$c = '<p>Estas restricciones son propias de ' . $p . ' y no coinciden con las de otros destinos. Un envío que las incumple se retiene en aduana:</p><ul class="srv-list">';
	foreach ( $l as $i ) $c .= '<li>' . esc_html( ucfirst( $i ) ) . '</li>';
	return grenvios_pais_sec( '', $h, $c . '</ul><p>Ante la duda, pregúntanos antes de cerrar la caja: sale más barato consultar que recuperar un envío retenido.</p>' );
}

function grenvios_pais_seccion_documentos( $d, $ang ) {
	$l = grenvios_pais_lista( $d['documentos'] );
	if ( ! $l ) return '';
	$p   = esc_html( $d['title'] );
	$tit = array(
		'apostilla-y-traduccion'            => 'Documentos que ' . $p . ' exige legalizados',
		'envio-internacional-de-documentos' => 'Qué acompaña a tus documentos hacia ' . $p,
		'envio-de-compras'                  => 'Qué papeles pide ' . $p . ' para una compra',
	);
	$h = isset( $tit[ $ang ] ) ? $tit[ $ang ] : 'Documentación para enviar a ' . $p;
	$c = '<p>Para que el envío a ' . $p . ' despache sin demoras hacen falta:</p><ul class="srv-list">';
	foreach ( $l as $i ) $c .= '<li>' . esc_html( ucfirst( $i ) ) . '</li>';
	return grenvios_pais_sec( 'bg-grey', $h, $c . '</ul>' );
}

function grenvios_pais_seccion_embalaje( $d, $ang ) {
	if ( trim( (string) $d['embalaje'] ) === '' ) return '';
	$p = esc_html( $d['title'] );
	$h = ( $ang === 'seguro-de-envios' ) ? 'Embalaje y seguro en la ruta a ' . $p : 'Cómo embalar lo que va a ' . $p;
	return grenvios_pais_sec( '', $h, '<p>' . wp_kses_post( $d['embalaje'] ) . '</p>' );
}

function grenvios_pais_seccion_ciudades( $d, $ang ) {
	$l = grenvios_pais_lista( $d['ciudades'] );
	if ( ! $l ) return '';
	$p = esc_html( $d['title'] );
	$c = '<p>Llegamos a ' . esc_html( grenvios_pais_frase_lista( $l ) )
		. '. Si tu destinatario vive fuera de estas ciudades, escríbenos: casi siempre hay forma de llegar.</p>';
	return grenvios_pais_sec( 'bg-grey', 'Ciudades de ' . $p . ' con cobertura', $c );
}

function grenvios_pais_seccion_comunidad( $d, $ang ) {
	if ( trim( (string) $d['comunidad'] ) === '' ) return '';
	return grenvios_pais_sec( '', 'Por qué se envía a ' . esc_html( $d['title'] ), '<p>' . wp_kses_post( $d['comunidad'] ) . '</p>' );
}

/* Preguntas frecuentes construidas con los datos reales del país. */
function grenvios_pais_seccion_faq( $d, $ang ) {
	$p = esc_html( $d['title'] );
	$q = array();
	if ( $d['tiempo'] !== '' ) $q[] = array( '¿Cuánto demora un envío a ' . $p . '?',
		'El plazo es de ' . esc_html( $d['tiempo'] ) . ' desde que el envío sale de {{origen_ciudad}}.' );
	if ( $d['entrega'] !== '' ) $q[] = array( '¿Cómo lo recibe mi destinatario en ' . $p . '?',
		$d['casa'] ? 'A domicilio, en la dirección que indiques.' : 'En la agencia local, presentando su documento de identidad.' );
	if ( $d['impuesto'] !== '' ) $q[] = array( '¿Tiene que pagar algo al recibir en ' . $p . '?',
		'No. El impuesto de aproximadamente ' . esc_html( $d['impuesto'] ) . ' % sobre el valor declarado se cancela en {{origen_ciudad}} al despachar.' );
	if ( $d['aereo'] && $d['terr'] ) $q[] = array( '¿Aéreo o terrestre a ' . $p . '?',
		'Aéreo si corre prisa o pesa poco; terrestre si abulta y la fecha no aprieta.' );
	if ( ! $q ) return '';

	$c = '';
	/* Clase propia, no `srv-panel-title`: esa está definida con `text-align:center`
	 * porque es el titular de un panel, y aplicada a una pregunta dejaba el
	 * enunciado centrado sobre una respuesta alineada a la izquierda. */
	foreach ( $q as $x ) $c .= '<div class="gr-faq-item"><h3 class="gr-faq-q">' . $x[0] . '</h3><p class="gr-faq-a">' . $x[1] . '</p></div>';
	$h = ( $ang === 'preguntas-frecuentes' ) ? 'Dudas propias de la ruta a ' . $p : 'Preguntas frecuentes sobre ' . $p;
	return grenvios_pais_sec( 'bg-grey', $h, $c );
}

/* Cierre. El enlace sale relativo a propósito: el localizador lo convierte en
 * la página de cotización DE ESA RUTA (ver inc/i18n-links.php). */
function grenvios_pais_seccion_cta( $d, $ang ) {
	$p = esc_html( $d['title'] );
	return grenvios_pais_sec( '', '¿Envías a ' . $p . '?',
		'<p>Dinos qué envías y a qué ciudad de ' . $p . ', y te damos el precio y la fecha de entrega.</p>'
		. '<div class="btn-group"><a class="default-btn" href="/cotizar/">Cotizar mi envío a ' . $p . '</a></div>' );
}

/* Entradilla propia de CADA página, construida solo con datos reales del país.
 *
 * Es la sección que garantiza que ninguna copia se quede sin contenido propio:
 * las demás dependen de campos que la clienta puede no haber rellenado todavía,
 * y sin esto páginas como «qué se puede enviar» o «blog» se quedaban con el CTA
 * y volvían a ser idénticas entre sí dentro del mismo país.
 *
 * El texto cambia por página porque cambia el ángulo, y por país porque cambian
 * el plazo, la modalidad, la forma de entrega y el impuesto. */
function grenvios_pais_seccion_resumen( $d, $ang ) {
	$p   = esc_html( $d['title'] );
	$pl  = $d['tiempo'] !== '' ? esc_html( $d['tiempo'] ) : '';
	$via = $d['aereo'] && $d['terr'] ? 'por vía aérea y terrestre' : ( $d['aereo'] ? 'por vía aérea' : 'por vía terrestre' );
	$ent = $d['casa'] ? 'con entrega a domicilio' : 'con retiro en agencia local';
	$imp = $d['impuesto'] !== '' ? ' El impuesto de aproximadamente ' . esc_html( $d['impuesto'] ) . ' % sobre el valor declarado se paga en {{origen_ciudad}}, no en destino.' : '';
	$plz = $pl !== '' ? ' El plazo habitual es de ' . $pl . '.' : '';

	$m = array(
		'home' => array( 'Envíos a ' . $p . ' desde {{origen_ciudad}}',
			'Operamos la ruta a ' . $p . ' ' . $via . ', ' . $ent . '.' . $plz . $imp ),
		'servicios' => array( 'Todos nuestros servicios hacia ' . $p,
			'Cada servicio de esta página está disponible hacia ' . $p . ' ' . $via . '.' . $plz . ' Lo que cambia de un servicio a otro es el embalaje y la documentación, no la ruta.' ),
		'envio-internacional-de-paquetes' => array( 'Paquetes a ' . $p,
			'Enviamos paquetes a ' . $p . ' ' . $via . ', ' . $ent . '.' . $plz . ' El precio depende del peso frente al volumen, así que una caja grande y ligera puede costar más que una pequeña y pesada.' ),
		'envio-internacional-de-documentos' => array( 'Documentos a ' . $p,
			'Los documentos hacia ' . $p . ' viajan ' . ( $d['aereo'] ? 'por vía aérea, la más rápida' : $via ) . ', ' . $ent . '.' . $plz . ' Al pesar poco, el flete es de los más económicos del catálogo.' ),
		'carga-internacional' => array( 'Carga y mercancía a ' . $p,
			'Movemos carga a ' . $p . ' ' . $via . '.' . $plz . $imp . ' Para volúmenes grandes la vía terrestre es casi siempre la que sale a cuenta.' ),
		'envio-de-equipaje' => array( 'Equipaje y mudanzas a ' . $p,
			'Si te mudas a ' . $p . ' o mandas equipaje por delante, la ruta es la misma ' . $via . ', ' . $ent . '.' . $plz . ' Conviene declarar cada bulto por separado.' ),
		'envio-de-compras' => array( 'Tus compras hacia ' . $p,
			'Recibimos tus compras y las enviamos a ' . $p . ' ' . $via . ', ' . $ent . '.' . $plz . $imp ),
		'envio-de-alimentos' => array( 'Alimentos a ' . $p,
			'Los alimentos hacia ' . $p . ' viajan ' . $via . ' y deben ir sellados de fábrica, nunca preparados en casa.' . $plz . ' Cada país tiene su propia lista de lo que admite, y la de ' . $p . ' no es la de sus vecinos.' ),
		'apostilla-y-traduccion' => array( 'Apostilla para documentos que van a ' . $p,
			'Legalizamos el documento y lo enviamos a ' . $p . ' ' . $via . '.' . $plz . ' El plazo del envío se suma al del trámite de apostilla, que corre por separado.' ),
		'peso-volumetrico' => array( 'Cómo se cobra el peso en la ruta a ' . $p,
			'En la ruta a ' . $p . ' se cobra el mayor entre el peso real y el volumétrico, igual que en el resto de destinos ' . $via . '.' . $plz . ' Por eso conviene ajustar la caja al contenido antes de cotizar.' ),
		'tiempos-de-entrega' => array( 'El plazo real hacia ' . $p,
			'Un envío a ' . $p . ' tarda ' . ( $pl !== '' ? $pl : 'lo que indique su modalidad' ) . ' ' . $via . ', ' . $ent . '. Se cuenta en días hábiles desde el despacho en {{origen_ciudad}}.' ),
		'que-se-puede-enviar' => array( 'Qué admite la ruta a ' . $p,
			'Hacia ' . $p . ' se admite lo habitual —ropa, documentos, regalos, repuestos— ' . $via . '.' . $plz . ' Las restricciones que de verdad importan son las de la aduana de ' . $p . ', que no coinciden con las de otros destinos.' ),
		'aduanas-e-impuestos' => array( 'La aduana de ' . $p,
			'Todo envío a ' . $p . ' pasa por aduana.' . $imp . $plz . ' Quien recibe en ' . $p . ' no adelanta dinero para retirar el envío.' ),
		'seguro-de-envios' => array( 'Asegurar un envío a ' . $p,
			'El seguro de un envío a ' . $p . ' se calcula sobre el valor declarado, el mismo importe que sirve de base al impuesto.' . $plz . ' Declarar por debajo abarata el seguro pero deja el envío sin cobertura real.' ),
		'cotizar' => array( 'Cotizar un envío a ' . $p,
			'Para cotizar hacia ' . $p . ' hacen falta tres datos: qué envías, cuánto pesa y mide, y a qué ciudad va.' . $plz . ' Con eso te damos precio cerrado ' . $via . '.' ),
		'rastreo-de-envios' => array( 'Seguir un envío a ' . $p,
			'Cada envío a ' . $p . ' lleva número de seguimiento desde que se despacha en {{origen_ciudad}}.' . $plz . ' Si el estado no cambia durante varios días hábiles, suele ser el paso por aduana.' ),
		'como-enviar-un-paquete-al-extranjero' => array( 'Enviar un paquete a ' . $p . ', paso a paso',
			'Enviar a ' . $p . ' son cuatro pasos: cotizar, embalar, despachar en {{origen_ciudad}} y seguir el envío hasta la entrega ' . ( $d['casa'] ? 'en el domicilio' : 'en la agencia' ) . '.' . $plz ),
		'recojo-a-domicilio-lima' => array( 'Recojo en {{origen_ciudad}} para enviar a ' . $p,
			'Recogemos tu envío en {{origen_ciudad}} y lo despachamos hacia ' . $p . ' ' . $via . ', ' . $ent . '.' . $plz . ' El recojo no altera el plazo de tránsito.' ),
		'envios-desde-provincias' => array( 'Enviar a ' . $p . ' desde provincias',
			'Si estás fuera de {{origen_ciudad}}, tu envío a ' . $p . ' llega primero a nuestra sede y desde ahí sale ' . $via . '.' . $plz . ' Al plazo hay que sumarle el tramo interno hasta {{origen_ciudad}}.' ),
		'envios-para-empresas' => array( 'Envíos de empresa hacia ' . $p,
			'Para empresas que despachan a ' . $p . ' de forma recurrente trabajamos con recojo programado y facturación mensual, sobre la misma ruta ' . $via . '.' . $plz . $imp ),
		'nosotros' => array( 'Quiénes somos para tu envío a ' . $p,
			'Somos un operador peruano y la ruta a ' . $p . ' es una de las que trabajamos ' . $via . ', ' . $ent . '.' . $plz . ' Toda la operación se coordina desde {{origen_ciudad}}.' ),
		'contacto' => array( 'Contactar por un envío a ' . $p,
			'Si tu consulta es sobre ' . $p . ', dinos la ciudad de destino y qué envías: con eso resolvemos precio y plazo en la misma conversación.' . $plz ),
		'preguntas-frecuentes' => array( 'Preguntas sobre la ruta a ' . $p,
			'Las dudas más repetidas sobre ' . $p . ' son el plazo, el impuesto y cómo lo recibe el destinatario.' . $plz . $imp ),
		'blog' => array( 'Guías para enviar a ' . $p,
			'Aquí reunimos lo que conviene saber antes de mandar algo a ' . $p . ': plazos, aduana, embalaje y qué admite cada modalidad ' . $via . '.' . $plz ),
	);
	if ( ! isset( $m[ $ang ] ) ) return '';
	return grenvios_pais_sec( '', $m[ $ang ][0], '<p>' . $m[ $ang ][1] . '</p>' );
}

/* ─────────────────────────────────────────────────────────────────────────
 * 3) LA MATRIZ: qué secciones lleva cada página
 *
 * Es el corazón del módulo. Ninguna página comparte conjunto con otra, y el
 * orden también cambia: aunque dos coincidan en una sección, no coinciden en la
 * que abre ni en la que cierra.
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_pais_matriz() {
	return apply_filters( 'grenvios_pais_matriz', array(
		/* La HOME del país: sus secciones propias. Sin «cta» ni «faq», que la home ya
		 * trae los suyos. */
		'home'                                 => array( 'resumen', 'ruta', 'plazos', 'entrega', 'prohibidos' ),
		'servicios'                            => array( 'resumen', 'comparativa', 'ruta', 'cta' ),
		'envio-internacional-de-paquetes'      => array( 'resumen', 'top', 'embalaje', 'plazos', 'cta' ),
		'envio-internacional-de-documentos'    => array( 'resumen', 'documentos', 'plazos', 'cta' ),
		'carga-internacional'                  => array( 'resumen', 'comparativa', 'impuestos', 'cta' ),
		'envio-de-equipaje'                    => array( 'resumen', 'embalaje', 'impuestos', 'cta' ),
		'envio-de-compras'                     => array( 'resumen', 'impuestos', 'documentos', 'cta' ),
		'envio-de-alimentos'                   => array( 'resumen', 'prohibidos', 'embalaje', 'cta' ),
		'apostilla-y-traduccion'               => array( 'resumen', 'documentos', 'plazos', 'cta' ),
		'peso-volumetrico'                     => array( 'resumen', 'comparativa', 'embalaje', 'cta' ),
		'tiempos-de-entrega'                   => array( 'resumen', 'plazos', 'ruta', 'faq', 'cta' ),
		'que-se-puede-enviar'                  => array( 'resumen', 'prohibidos', 'top', 'cta' ),
		'aduanas-e-impuestos'                  => array( 'resumen', 'impuestos', 'documentos', 'faq', 'cta' ),
		'seguro-de-envios'                     => array( 'resumen', 'impuestos', 'embalaje', 'cta' ),
		'cotizar'                              => array( 'resumen', 'plazos', 'comparativa', 'cta' ),
		'rastreo-de-envios'                    => array( 'resumen', 'entrega', 'plazos', 'cta' ),
		'como-enviar-un-paquete-al-extranjero' => array( 'resumen', 'ruta', 'embalaje', 'documentos', 'cta' ),
		'recojo-a-domicilio-lima'              => array( 'resumen', 'ruta', 'ciudades', 'cta' ),
		'envios-desde-provincias'              => array( 'resumen', 'ruta', 'plazos', 'cta' ),
		'envios-para-empresas'                 => array( 'resumen', 'comparativa', 'impuestos', 'cta' ),
		'nosotros'                             => array( 'resumen', 'comunidad', 'ciudades', 'ruta', 'cta' ),
		'contacto'                             => array( 'resumen', 'entrega', 'ciudades', 'cta' ),
		'preguntas-frecuentes'                 => array( 'resumen', 'faq', 'impuestos', 'plazos', 'cta' ),
		'blog'                                 => array( 'resumen', 'top', 'comunidad', 'cta' ),
		/* Las ENTRADAS del blog. No es una página maestra: es el conjunto que se
		 * añade a cada entrada duplicada, para que una guía copiada a nueve rutas
		 * no sea nueve veces el mismo texto. Va al final del artículo. */
		'_post'                                => array( 'ruta', 'plazos', 'faq', 'cta' ),
	) );
}

/* HTML de las secciones de país para una página maestra concreta. */
function grenvios_pais_contenido( $base_slug, $lang ) {
	$d = grenvios_pais_datos( $lang );
	if ( ! $d ) return '';

	// La página de destino del país ya es íntegramente sobre él: sin bloque.
	if ( function_exists( 'grenvios_destinos' ) && array_key_exists( $base_slug, grenvios_destinos() ) ) return '';

	$matriz = grenvios_pais_matriz();
	// Una página que no esté en la matriz recibe el bloque mínimo: mejor eso que
	// quedarse sin nada que la distinga de su original.
	// Una página listada con array vacío quiere decir «sin bloque», y eso se
	// respeta; el respaldo solo actúa para páginas que no están en la matriz.
	if ( isset( $matriz[ $base_slug ] ) && ! $matriz[ $base_slug ] ) return '';
	$secs = isset( $matriz[ $base_slug ] ) ? $matriz[ $base_slug ] : array( 'ruta', 'cta' );

	$render = function ( $lista ) use ( $d, $base_slug ) {
		$h = '';
		foreach ( $lista as $s ) {
			$fn = 'grenvios_pais_seccion_' . $s;
			if ( function_exists( $fn ) ) $h .= $fn( $d, $base_slug );
		}
		return $h;
	};

	$out = $render( $secs );

	/* Relleno cuando el conjunto asignado se queda corto.
	 *
	 * Pasa cuando una sección no tiene datos: en un país de una sola modalidad
	 * la comparativa aéreo/terrestre no existe, y páginas como «peso volumétrico»
	 * se quedaban en el CTA. En vez de inventar el dato que falta, se añaden
	 * secciones REALES que esa página no estaba usando —siempre en el mismo
	 * orden, para que dos páginas no acaben con el mismo relleno—. */
	$reserva = array_values( array_diff(
		array( 'ruta', 'plazos', 'entrega', 'faq', 'comparativa', 'ciudades', 'top', 'documentos', 'prohibidos', 'embalaje', 'comunidad', 'impuestos' ),
		$secs
	) );
	/* El objetivo de relleno es más alto que el umbral de la compuerta: la
	 * compuerta decide si la página vale para el índice, esto decide cuánto pesa
	 * el contenido propio frente al cuerpo de plantilla, que es idéntico en todos
	 * los países. Con 600 el bloque era el 16 % de la página y dos países sin
	 * datos propios seguían pareciéndose un 70 %. */
	$objetivo = (int) apply_filters( 'grenvios_pais_objetivo', 1100, $base_slug );
	while ( mb_strlen( trim( wp_strip_all_tags( $out ) ) ) < $objetivo && $reserva ) {
		$extra = array_shift( $reserva );
		$out   = $render( array( $extra ) ) . $out;
	}

	if ( $out === '' ) return '';

	return "\n<!-- grenvios:pais -->\n" . $out . "\n<!-- /grenvios:pais -->\n";
}

/* ─────────────────────────────────────────────────────────────────────────
 * 4) ESCRITURA EN LA COPIA
 *
 * El bloque se escribe en el contenido de la página, no se inyecta al pintar.
 * Dos razones: la clienta puede reescribirlo desde el editor como cualquier
 * otro texto, y la compuerta —que compara la copia con su original— lo ve como
 * contenido propio y abre el índice sola.
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_pais_aplicar( $post_id, $base_slug, $lang ) {
	$p = get_post( $post_id );
	if ( ! $p ) return false;

	$html = grenvios_pais_contenido( $base_slug, $lang );

	/* El bloque viejo se quita SIEMPRE, aunque el nuevo sea vacío.
	 *
	 * Antes se salía antes de tiempo cuando no había nada que escribir, así que
	 * una página que dejaba de llevar bloque —la portada de la ruta, desde que
	 * usa la plantilla de destino— conservaba el de la vez anterior y seguía
	 * pintando secciones repetidas por mucho que se regenerara. */
	$cont = preg_replace( '~\n?<!-- grenvios:pais -->.*?<!-- /grenvios:pais -->\n?~s', '', (string) $p->post_content );

	$nuevo = $cont . $html;
	if ( $nuevo === $p->post_content ) return $html !== '';
	wp_update_post( array( 'ID' => $post_id, 'post_content' => $nuevo ) );
	return true;
}

/* Rehace el bloque de todas las páginas de una ruta. Se usa desde el botón de
 * Sedes cuando se rellenan los campos de un país después de haberlo creado. */
function grenvios_pais_regenerar( $lang ) {
	$n = 0;
	$ids = get_posts( array(
		'post_type'   => 'page',
		'numberposts' => -1,
		'post_status' => 'any',
		'fields'      => 'ids',
		'lang'        => $lang,
	) );
	foreach ( $ids as $id ) {
		$base = function_exists( 'grenvios_canonical_slug' ) ? grenvios_canonical_slug( $id ) : '';
		if ( $base === '' ) continue;
		if ( function_exists( 'grenvios_espejo_es' ) && grenvios_espejo_es( $id ) ) continue;   // espejo: sin bloque de país
		if ( grenvios_pais_aplicar( $id, $base, $lang ) ) $n++;
	}
	return $n;
}

/* ─────────────────────────────────────────────────────────────────────────
 * 5) PINTADO
 *
 * El bloque se guarda en `post_content`, pero la mayoría de las páginas del tema
 * son «parametrizadas»: renderizan su plantilla con tokens y NO imprimen
 * post_content (ver `grenvios_render_editable`). Sin este renderizador el
 * contenido por país quedaba guardado y sin salir nunca: las páginas seguían
 * siendo idénticas entre países aunque en la base de datos no lo fueran.
 *
 * Sale por un único sitio —page.php, después del contenido y antes del enlazado
 * interno— y `grenvios_render_editable` lo quita de su volcado para que no se
 * imprima dos veces en las páginas que sí muestran post_content.
 * ───────────────────────────────────────────────────────────────────────── */

function grenvios_pais_bloque_de( $post_id ) {
	$c = (string) get_post_field( 'post_content', $post_id );
	return preg_match( '~<!-- grenvios:pais -->(.*?)<!-- /grenvios:pais -->~s', $c, $m ) ? $m[1] : '';
}

function grenvios_pais_sin_bloque( $html ) {
	return preg_replace( '~
?<!-- grenvios:pais -->.*?<!-- /grenvios:pais -->
?~s', '', (string) $html );
}

function grenvios_pais_render() {
	if ( ! is_singular( 'page' ) && ! is_home() ) return false;
	$id = is_home() ? (int) get_option( 'page_for_posts' ) : (int) get_queried_object_id();
	if ( is_home() && $id && function_exists( 'pll_get_post' ) ) {
		$tid = (int) pll_get_post( $id, grenvios_i18n_current() );
		if ( $tid ) $id = $tid;
	}
	if ( ! $id ) return false;

	$html = grenvios_pais_bloque_de( $id );
	/* Filtro `grenvios_pais_render_extra`: secciones por país que se pintan al
	 * vuelo, sin escribirse en el contenido (inc/paises-perfil.php). */
	$html .= (string) apply_filters( 'grenvios_pais_render_extra', '', $id );
	if ( trim( $html ) === '' ) return false;
	/* Filtro `grenvios_pais_bloque_html`: diseño v3 de los bloques (inc/rutas-diseno.php). */
	$html = (string) apply_filters( 'grenvios_pais_bloque_html', $html, $id );

	// Mismo tratamiento que cualquier contenido del tema: tokens, imágenes del
	// Customizer y, al salir por page.php, traducción de enlaces a la ruta.
	echo apply_filters( 'grenvios_content_html', grenvios_apply_media_overrides( $html ) );
	return true;
}

