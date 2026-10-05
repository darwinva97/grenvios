<?php
/**
 * Grenvíos — Páginas servicio × país (long tail que vende).
 *
 * QUÉ SON
 * Páginas del tipo `/enviar-documentos-a-estados-unidos/`, que responden
 * exactamente a como se busca: verbo + qué + a dónde. Son las que más venden,
 * porque quien las busca ya sabe qué quiere enviar y a dónde.
 *
 * EL RIESGO, Y CÓMO SE EVITA
 * Generar en masa 4 servicios × 9 países = 36 páginas clonadas cambiando dos
 * palabras es exactamente lo que Google llama *doorway pages*: penaliza esas
 * páginas y arrastra al dominio entero. Aquí no puede pasar, por diseño:
 *
 *   1. Se crean de una en una, como BORRADOR, y solo las que se elijan.
 *   2. Cada una exige CONTENIDO PROPIO (precio de referencia, plazo real,
 *      restricciones de ese país para ese servicio).
 *   3. Mientras no lo tenga, la página se sirve con `noindex` y se queda fuera
 *      del sitemap. Existe para el usuario, pero no entra al índice hasta que
 *      aporta algo que no está en ninguna otra página del sitio.
 *
 * Es decir: la herramienta escala, pero no deja escalar basura.
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

const GRENVIOS_COMBO_META = '_grenvios_combo';

/* ══════════════════════════════════════
   1) SERVICIOS COMBINABLES
══════════════════════════════════════ */
/* Cada entrada genera UNA ruta por país. La clave `kw` es a la vez la palabra
 * clave objetivo y el slug (`enviar documentos a cuba` → /enviar-documentos-a-cuba/):
 * verbo + qué + destino, que es exactamente como se busca.
 *
 * `seo` y `desc` son el <title> y la meta description, pensados uno a uno para
 * la intención de cada tipo de página. No se repiten entre tipos: si nueve
 * páginas de un país comparten título, compiten entre ellas y Google se queda
 * con una.
 *
 * Filtro `grenvios_combo_servicios`: para añadir un tipo de página por país sin
 * tocar el tema. */
function grenvios_combo_servicios() {
	return apply_filters( 'grenvios_combo_servicios', array(

		/* ── Servicios: quien ya sabe qué quiere enviar ── */
		'documentos' => array(
			'verbo'  => 'Enviar documentos',
			'pagina' => 'servicios/envio-internacional-de-documentos',
			'kw'     => 'enviar documentos a %s',
			'seo'    => 'Enviar Documentos a %s desde {{origen_pais}} | Grenvíos',
			'desc'   => 'Envía documentos y trámites legales a %1$s desde {{origen_ciudad}}: plazos, apostilla y requisitos de aduana. Cotiza en minutos.',
		),
		'paquetes' => array(
			'verbo'  => 'Enviar paquetes',
			'pagina' => 'servicios/envio-internacional-de-paquetes',
			'kw'     => 'enviar paquetes a %s',
			'seo'    => 'Enviar Paquetes a %s: Precio y Tiempo | Grenvíos',
			'desc'   => 'Envía paquetes a %1$s desde {{origen_ciudad}} por vía aérea o terrestre. Consulta plazos, restricciones y cotiza tu envío hoy.',
		),
		'carga' => array(
			'verbo'  => 'Enviar carga',
			'pagina' => 'servicios/carga-internacional',
			'kw'     => 'enviar carga a %s',
			'seo'    => 'Carga y Mercancía a %s | Grenvíos',
			'desc'   => 'Carga comercial y grandes volúmenes a %1$s desde {{origen_ciudad}}. Asesoría en aduana, documentación y tarifas por volumen.',
		),
		'equipaje' => array(
			'verbo'  => 'Enviar equipaje',
			'pagina' => 'servicios/envio-de-equipaje',
			'kw'     => 'enviar equipaje a %s',
			'seo'    => 'Enviar Equipaje y Mudanza a %s | Grenvíos',
			'desc'   => 'Manda maletas, mudanza personal y efectos a %1$s desde {{origen_ciudad}}. Qué se puede llevar, plazos y cuánto cuesta.',
		),

		/* ── Decisión: quien compara antes de contratar ── */
		'precio' => array(
			'verbo'  => 'Cuánto cuesta enviar',
			'pagina' => 'cotizar',
			'kw'     => 'cuanto cuesta enviar a %s',
			'seo'    => '¿Cuánto Cuesta Enviar a %s? | Grenvíos',
			'desc'   => 'Cuánto cuesta un envío a %1$s desde {{origen_ciudad}}: precio por kilo, peso volumétrico y qué incluye la tarifa. Cotiza gratis.',
		),
		'tiempos' => array(
			'verbo'  => 'Cuánto demora un envío',
			'pagina' => 'tiempos-de-entrega',
			'kw'     => 'cuanto demora un envio a %s',
			'seo'    => '¿Cuánto Demora un Envío a %s? | Grenvíos',
			'desc'   => 'Tiempo de entrega a %1$s desde {{origen_ciudad}} en días hábiles, por vía aérea y terrestre, y qué puede retrasarlo en aduana.',
		),

		/* ── Dudas que frenan la compra ── */
		'restricciones' => array(
			'verbo'  => 'Qué se puede enviar',
			'pagina' => 'que-se-puede-enviar',
			'kw'     => 'que se puede enviar a %s',
			'seo'    => 'Qué se Puede Enviar a %s y Qué No | Grenvíos',
			'desc'   => 'Qué admite y qué prohíbe la aduana de %1$s: alimentos, medicinas, electrónica y límites de valor. Revísalo antes de empacar.',
		),
		'aduana' => array(
			'verbo'  => 'Aduana e impuestos',
			'pagina' => 'aduanas-e-impuestos',
			'kw'     => 'aduana de %s para envios',
			'seo'    => 'Aduana de %s: Impuestos y Trámites | Grenvíos',
			'desc'   => 'Cómo funciona la aduana de %1$s para un envío desde {{origen_pais}}: impuestos, umbral libre, papeles y por qué se retiene un paquete.',
		),
		'compras' => array(
			'verbo'  => 'Enviar compras',
			'pagina' => 'envio-de-compras',
			'kw'     => 'enviar compras a %s',
			'seo'    => 'Enviar Compras Online a %s | Grenvíos',
			'desc'   => 'Compra en {{origen_pais}} y recíbelo en %1$s. Consolidamos varias tiendas en un solo envío y lo despachamos a tu dirección.',
		),
	) );
}

/* Título SEO y meta description de una página por país.
 * Sin esto, estas páginas salían con el <title> genérico de WordPress: una
 * página creada para posicionar por un país y sin título propio no compite. */
add_filter( 'grenvios_seo_defaults', function ( $out, $slug ) {
	if ( $out[0] !== '' || $out[1] !== '' ) return $out;

	$page = get_page_by_path( $slug );
	if ( ! $page ) return $out;
	$c = grenvios_combo_of( $page->ID );
	if ( ! $c ) return $out;

	$pais = $c['pais_data']['title'];
	$serv = $c['serv_data'];

	$seo  = ! empty( $serv['seo'] )  ? sprintf( $serv['seo'], $pais )  : $serv['verbo'] . ' a ' . $pais . ' | Grenvíos';
	$desc = ! empty( $serv['desc'] ) ? sprintf( $serv['desc'], $pais ) : '';

	return array( $seo, $desc );
}, 10, 2 );

/* Slug de la combinación: /enviar-documentos-a-estados-unidos/ */
function grenvios_combo_slug( $serv_key, $pais_slug ) {
	$s = grenvios_combo_servicios();
	if ( ! isset( $s[ $serv_key ] ) ) return '';
	return sanitize_title( remove_accents( str_replace( ' ', '-', sprintf( $s[ $serv_key ]['kw'], $pais_slug ) ) ) );
}

/* Datos de la combinación de una página (o false). */
function grenvios_combo_of( $post_id ) {
	$c = get_post_meta( (int) $post_id, GRENVIOS_COMBO_META, true );
	if ( ! is_array( $c ) || empty( $c['servicio'] ) || empty( $c['pais'] ) ) return false;

	$servs = grenvios_combo_servicios();
	$dest  = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();
	if ( ! isset( $servs[ $c['servicio'] ] ) || ! isset( $dest[ $c['pais'] ] ) ) return false;

	$c['serv_data'] = $servs[ $c['servicio'] ];
	$c['pais_data'] = $dest[ $c['pais'] ];
	return $c;
}

/* ══════════════════════════════════════
   2) CONTENIDO PROPIO: qué se exige y cómo se mide
   Sin esto la página sería un clon. Con esto, es la única página del sitio que
   cruza «qué envías» con «a dónde», que es justo lo que busca el cliente.
══════════════════════════════════════ */
function grenvios_combo_campos() {
	return array(
		'combo_intro'  => array( 'Introducción propia', 'textarea', 'Dos o tres frases sobre este servicio HACIA ESTE PAÍS en concreto: qué suele enviarse, quién lo envía, qué particularidad tiene la ruta.' ),
		'combo_precio' => array( 'Precio de referencia', 'textarea', 'Desde cuánto sale este envío a este país y qué incluye. Es el dato que más se busca y el que más convierte.' ),
		'combo_tiempo' => array( 'Plazo real', 'textarea', 'Tiempo de entrega para esta combinación, en días hábiles, y de qué depende.' ),
		'combo_restr'  => array( 'Restricciones de este país', 'textarea', 'Lo que la aduana de este país NO admite para este tipo de envío, y los límites de valor.' ),
		'combo_caso'   => array( 'Caso o consejo', 'textarea', 'Un ejemplo real o un consejo práctico de esta ruta. Opcional, pero es lo que distingue la página.' ),
	);
}

/* ¿Tiene contenido propio suficiente para entrar al índice? */
function grenvios_combo_es_indexable( $post_id ) {
	$campos = grenvios_combo_campos();
	$texto  = '';
	$llenos = 0;

	foreach ( $campos as $k => $def ) {
		$v = trim( (string) get_post_meta( $post_id, 'grenvios_' . $k, true ) );
		if ( $v === '' ) continue;
		$llenos++;
		$texto .= ' ' . $v;
	}
	$palabras = str_word_count( wp_strip_all_tags( $texto ) );

	// Criterio: introducción propia + al menos dos datos duros + 180 palabras.
	$tiene_intro = trim( (string) get_post_meta( $post_id, 'grenvios_combo_intro', true ) ) !== '';
	return ( $tiene_intro && $llenos >= 3 && $palabras >= 180 );
}

/* Qué le falta a una combinación para poder indexarse. */
function grenvios_combo_pendientes( $post_id ) {
	$out = array();
	if ( trim( (string) get_post_meta( $post_id, 'grenvios_combo_intro', true ) ) === '' ) {
		$out[] = 'la introducción propia';
	}
	$llenos = 0;
	foreach ( grenvios_combo_campos() as $k => $def ) {
		if ( trim( (string) get_post_meta( $post_id, 'grenvios_' . $k, true ) ) !== '' ) $llenos++;
	}
	if ( $llenos < 3 ) $out[] = 'al menos 3 de los 5 campos rellenos (van ' . $llenos . ')';

	$texto = '';
	foreach ( grenvios_combo_campos() as $k => $def ) $texto .= ' ' . get_post_meta( $post_id, 'grenvios_' . $k, true );
	$p = str_word_count( wp_strip_all_tags( $texto ) );
	if ( $p < 180 ) $out[] = '180 palabras de contenido propio (van ' . $p . ')';

	return $out;
}

/* ══════════════════════════════════════
   3) NOINDEX Y SITEMAP MIENTRAS ESTÉ INCOMPLETA
══════════════════════════════════════ */
add_action( 'wp_head', function () {
	if ( ! is_page() ) return;
	$id = (int) get_queried_object_id();
	if ( ! grenvios_combo_of( $id ) || grenvios_combo_es_indexable( $id ) ) return;

	echo '<meta name="robots" content="noindex, follow">' . "\n";
	echo '<!-- Grenvíos: combinación sin contenido propio suficiente; fuera del índice hasta completarla -->' . "\n";
}, 3 );

/* Fuera del sitemap: no se pide a Google que rastree lo que no queremos indexar. */
add_filter( 'grenvios_sitemap_urls', function ( $urls ) {
	foreach ( $urls as $i => $u ) {
		if ( empty( $u['post_id'] ) ) continue;
		if ( grenvios_combo_of( $u['post_id'] ) && ! grenvios_combo_es_indexable( $u['post_id'] ) ) {
			unset( $urls[ $i ] );
		}
	}
	return array_values( $urls );
} );

/* ══════════════════════════════════════
   4) RENDER
══════════════════════════════════════ */
add_filter( 'grenvios_render_page_early', function ( $hecho, $slug ) {
	if ( $hecho ) return $hecho;
	$combo = grenvios_combo_of( get_the_ID() );
	if ( ! $combo ) return $hecho;
	grenvios_render_combo( $combo );
	return true;
}, 8, 2 );

function grenvios_render_combo( $c ) {
	$pais    = $c['pais_data'];
	$serv    = $c['serv_data'];
	$titulo  = $serv['verbo'] . ' a ' . $pais['title'];
	$campo   = function ( $k ) { return trim( (string) grenvios_field( $k, '' ) ); };

	if ( function_exists( 'logisko_page_banner' ) ) {
		logisko_page_banner( grenvios_t( 'Desde {{origen_ciudad}}, {{origen_pais}}' ), esc_html( $titulo ) );
	}
	?>
	<section class="srv-intro padding-top"><div class="container">
		<div class="srv-lead text-center">
			<h2><?php echo esc_html( sprintf( grenvios_t( '%1$s a %2$s desde {{origen_pais}}' ), grenvios_t( $serv['verbo'] ), $pais['title'] ) ); ?></h2>
			<?php $intro = $campo( 'combo_intro' ); ?>
			<p><?php echo esc_html( $intro !== '' ? $intro : $pais['lead'] ); ?></p>
		</div>
	</div></section>

	<section class="srv-section padding"><div class="container">
		<div class="gr-combo-datos">
			<div class="gr-combo-dato">
				<span><?php echo esc_html( grenvios_t( 'Tiempo estimado' ) ); ?></span>
				<strong><?php echo esc_html( $campo( 'combo_tiempo' ) !== '' ? $campo( 'combo_tiempo' ) : $pais['tiempo'] ); ?></strong>
			</div>
			<div class="gr-combo-dato">
				<span><?php echo esc_html( grenvios_t( 'Modalidades' ) ); ?></span>
				<strong><?php echo esc_html( $pais['modos'] ); ?></strong>
			</div>
			<div class="gr-combo-dato">
				<span><?php echo esc_html( grenvios_t( 'Entrega' ) ); ?></span>
				<strong><?php echo esc_html( $pais['entrega'] ); ?></strong>
			</div>
		</div>

		<?php if ( $campo( 'combo_precio' ) !== '' ) : ?>
			<div class="gr-combo-bloque">
				<h3><?php echo esc_html( sprintf( grenvios_t( 'Cuánto cuesta enviar a %s' ), $pais['title'] ) ); ?></h3>
				<p><?php echo esc_html( $campo( 'combo_precio' ) ); ?></p>
			</div>
		<?php endif; ?>

		<div class="gr-combo-bloque">
			<h3><?php echo esc_html( sprintf( grenvios_t( 'Restricciones para %s' ), $pais['title'] ) ); ?></h3>
			<p><?php echo esc_html( $campo( 'combo_restr' ) !== '' ? $campo( 'combo_restr' ) : $pais['restr'] ); ?></p>
		</div>

		<?php if ( $campo( 'combo_caso' ) !== '' ) : ?>
			<div class="gr-combo-bloque">
				<h3><?php echo esc_html( grenvios_t( 'De nuestra experiencia en esta ruta' ) ); ?></h3>
				<p><?php echo esc_html( $campo( 'combo_caso' ) ); ?></p>
			</div>
		<?php endif; ?>

		<div class="gr-combo-acciones">
			<a class="default-btn" href="<?php echo esc_url( grenvios_tool_url( 'cotizar' ) ); ?>"><?php echo esc_html( grenvios_t( 'Cotizar este envío' ) ); ?></a>
			<a href="<?php echo esc_url( grenvios_combo_url( $serv['pagina'] ) ); ?>"><?php echo esc_html( sprintf( grenvios_t( 'Ver todo sobre %s' ), mb_strtolower( grenvios_t( $serv['verbo'] ) ) ) ); ?></a>
			<a href="<?php echo esc_url( grenvios_combo_url( 'destinos/' . $c['pais'] ) ); ?>"><?php echo esc_html( sprintf( grenvios_t( 'Ver todo sobre envíos a %s' ), $pais['title'] ) ); ?></a>
		</div>
	</div></section>
	<?php
}

function grenvios_combo_url( $path ) {
	$p   = get_page_by_path( $path );
	$url = $p ? get_permalink( $p ) : home_url( '/' . $path . '/' );
	if ( function_exists( 'grenvios_i18n_localize_url' ) ) $url = grenvios_i18n_localize_url( $url );
	return $url;
}

/* ══════════════════════════════════════
   5) CAJA DE EDICIÓN
══════════════════════════════════════ */
add_action( 'add_meta_boxes', function () {
	global $post;
	if ( ! $post || ! grenvios_combo_of( $post->ID ) ) return;
	add_meta_box( 'grenvios-combo', 'Contenido propio de esta ruta (obligatorio para indexar)', 'grenvios_combo_metabox', 'page', 'normal', 'high' );
}, 5 );

function grenvios_combo_metabox( $post ) {
	wp_nonce_field( 'grenvios_combo', 'grenvios_combo_nonce' );
	$pend = grenvios_combo_pendientes( $post->ID );
	?>
	<?php if ( $pend ) : ?>
		<div class="notice notice-warning inline" style="margin:0 0 14px;padding:10px 14px">
			<p style="margin:0"><strong>Esta página NO se está indexando en Google.</strong>
			Falta: <?php echo esc_html( implode( '; ', $pend ) ); ?>.</p>
			<p style="margin:6px 0 0">Es a propósito: una página que solo repite lo que ya dicen la del servicio y la del país
			no aporta nada y perjudica al resto del sitio. En cuanto tenga contenido propio, entra al índice sola.</p>
		</div>
	<?php else : ?>
		<div class="notice notice-success inline" style="margin:0 0 14px;padding:10px 14px">
			<p style="margin:0"><strong>Lista para indexar.</strong> Tiene contenido propio suficiente.</p>
		</div>
	<?php endif; ?>

	<?php foreach ( grenvios_combo_campos() as $key => $def ) :
		$val = get_post_meta( $post->ID, 'grenvios_' . $key, true ); ?>
		<p>
			<label><strong><?php echo esc_html( $def[0] ); ?></strong><br>
			<span class="description"><?php echo esc_html( $def[2] ); ?></span><br>
			<textarea name="grenvios_<?php echo esc_attr( $key ); ?>" rows="3" class="widefat"><?php echo esc_textarea( $val ); ?></textarea></label>
		</p>
	<?php endforeach; ?>
	<?php
}

add_action( 'save_post_page', function ( $post_id ) {
	if ( ! isset( $_POST['grenvios_combo_nonce'] ) || ! wp_verify_nonce( $_POST['grenvios_combo_nonce'], 'grenvios_combo' ) ) return;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_page', $post_id ) ) return;

	foreach ( array_keys( grenvios_combo_campos() ) as $key ) {
		if ( ! isset( $_POST[ 'grenvios_' . $key ] ) ) continue;
		update_post_meta( $post_id, 'grenvios_' . $key, sanitize_textarea_field( wp_unslash( $_POST[ 'grenvios_' . $key ] ) ) );
	}
} );

/* ══════════════════════════════════════
   6) PANTALLA: matriz servicio × país
══════════════════════════════════════ */
add_action( 'admin_menu', function () {
	add_submenu_page(
		'grenvios-seo', 'Rutas (servicio × país)', 'Rutas', 'edit_pages',
		'grenvios-combos', 'grenvios_combos_page'
	);
}, 25 );

add_action( 'admin_post_grenvios_combo_add', function () {
	if ( ! current_user_can( 'edit_pages' ) ) wp_die( 'Sin permiso.' );
	check_admin_referer( 'grenvios_combos' );

	$serv = isset( $_POST['servicio'] ) ? sanitize_key( $_POST['servicio'] ) : '';
	$pais = isset( $_POST['pais'] ) ? sanitize_key( $_POST['pais'] ) : '';
	$servs = grenvios_combo_servicios();
	$dest  = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();

	if ( ! isset( $servs[ $serv ] ) || ! isset( $dest[ $pais ] ) ) {
		wp_safe_redirect( admin_url( 'admin.php?page=grenvios-combos&gr_msg=error' ) );
		exit;
	}
	$slug = grenvios_combo_slug( $serv, $pais );
	if ( get_page_by_path( $slug ) ) {
		wp_safe_redirect( admin_url( 'admin.php?page=grenvios-combos&gr_msg=existe' ) );
		exit;
	}
	$titulo = $servs[ $serv ]['verbo'] . ' a ' . $dest[ $pais ]['title'];
	$id = wp_insert_post( array(
		'post_type'   => 'page',
		'post_status' => 'draft',            // SIEMPRE borrador: se publica con contenido
		'post_title'  => $titulo,
		'post_name'   => $slug,
	), true );
	if ( is_wp_error( $id ) ) {
		wp_safe_redirect( admin_url( 'admin.php?page=grenvios-combos&gr_msg=error' ) );
		exit;
	}
	update_post_meta( $id, GRENVIOS_COMBO_META, array( 'servicio' => $serv, 'pais' => $pais ) );
	update_post_meta( $id, GRENVIOS_KW_META, sprintf( $servs[ $serv ]['kw'], mb_strtolower( $dest[ $pais ]['title'] ) ) );
	update_post_meta( $id, 'grenvios_seo_title', $titulo . ' desde {{origen_pais}} | Grenvíos' );

	if ( function_exists( 'grenvios_i18n_active' ) && grenvios_i18n_active() && function_exists( 'pll_set_post_language' ) ) {
		pll_set_post_language( $id, grenvios_i18n_default() );
	}
	delete_transient( 'grenvios_link_map' );

	wp_safe_redirect( get_edit_post_link( $id, '' ) );
	exit;
} );

function grenvios_combos_page() {
	if ( ! current_user_can( 'edit_pages' ) ) wp_die( 'Sin permiso.' );
	$servs = grenvios_combo_servicios();
	$dest  = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();
	$msg   = isset( $_GET['gr_msg'] ) ? sanitize_key( $_GET['gr_msg'] ) : '';
	?>
	<div class="wrap">
		<h1>Rutas: servicio × país</h1>

		<?php if ( $msg === 'existe' ) : ?><div class="notice notice-warning"><p>Esa ruta ya existe.</p></div><?php endif; ?>
		<?php if ( $msg === 'error' ) : ?><div class="notice notice-error"><p>No se pudo crear la página.</p></div><?php endif; ?>

		<div class="notice notice-info inline" style="margin:14px 0;padding:12px 16px;max-width:900px">
			<p style="margin:0 0 8px"><strong>Estas son las páginas que más venden</strong>, porque responden a como se busca de verdad:
			<em>enviar documentos a Estados Unidos</em>, no <em>servicios de courier</em>. Quien busca así ya sabe qué envía y a dónde.</p>
			<p style="margin:0"><strong>Pero no las crees todas.</strong> Generar 4 servicios × 9 países = 36 páginas clonadas es lo que
			Google llama <em>doorway pages</em>, y penaliza el sitio entero. Crea solo las rutas que de verdad mueves,
			y dales datos propios: precio de referencia, plazo real y restricciones de ese país.
			Mientras no los tengan, la página se queda <strong>fuera del índice</strong> automáticamente.</p>
		</div>

		<h2>Crear una ruta</h2>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="grenvios_combo_add">
			<?php wp_nonce_field( 'grenvios_combos' ); ?>
			<select name="servicio">
				<?php foreach ( $servs as $k => $s ) : ?>
					<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $s['verbo'] ); ?></option>
				<?php endforeach; ?>
			</select>
			<select name="pais">
				<?php foreach ( $dest as $k => $d ) : ?>
					<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $d['title'] ); ?></option>
				<?php endforeach; ?>
			</select>
			<?php submit_button( 'Crear como borrador', 'primary', 'submit', false ); ?>
		</form>

		<h2 style="margin-top:24px">Estado de las rutas</h2>
		<table class="widefat striped">
			<thead><tr>
				<th style="width:18%">País</th>
				<?php foreach ( $servs as $s ) : ?><th><?php echo esc_html( $s['verbo'] ); ?></th><?php endforeach; ?>
			</tr></thead>
			<tbody>
			<?php foreach ( $dest as $pk => $d ) : ?>
				<tr>
					<td><strong><?php echo esc_html( $d['title'] ); ?></strong></td>
					<?php foreach ( $servs as $sk => $s ) :
						$page = get_page_by_path( grenvios_combo_slug( $sk, $pk ) ); ?>
						<td>
							<?php if ( ! $page ) : ?>
								<span style="opacity:.5">—</span>
							<?php else :
								$ok = grenvios_combo_es_indexable( $page->ID );
								$publicada = ( $page->post_status === 'publish' ); ?>
								<a href="<?php echo esc_url( get_edit_post_link( $page->ID ) ); ?>">
									<?php echo $publicada ? ( $ok ? '✅ indexable' : '⚠️ sin contenido' ) : '📝 borrador'; ?>
								</a>
							<?php endif; ?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<p class="description">
			✅ publicada y con contenido propio · ⚠️ publicada pero fuera del índice hasta completarla · 📝 borrador · — no existe
		</p>
	</div>
	<?php
}

/* ══════════════════════════════════════
   7) ESTILOS
══════════════════════════════════════ */
add_action( 'wp_head', function () {
	if ( is_admin() ) return;
	echo '<style id="grenvios-combo-css">'
		. '.gr-combo-datos{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:30px}'
		. '.gr-combo-dato{padding:16px 18px;border-radius:8px;background:rgba(0,0,0,.045)}'
		. '.gr-combo-dato span{display:block;font-size:12px;text-transform:uppercase;letter-spacing:.4px;opacity:.65;margin-bottom:4px}'
		. '.gr-combo-dato strong{font-size:16px;line-height:1.35}'
		. '.gr-combo-bloque{margin-bottom:26px;max-width:80ch}'
		. '.gr-combo-bloque h3{font-size:20px;margin-bottom:8px}'
		. '.gr-combo-acciones{display:flex;flex-wrap:wrap;gap:16px;align-items:center;margin-top:10px}'
		. '@media(max-width:767px){.gr-combo-datos{grid-template-columns:1fr}}'
		. '</style>';
}, 107 );

/* Textos de las páginas de ruta. */
add_filter( 'grenvios_i18n_extra_strings', function ( $textos ) {
	foreach ( array(
		'Desde {{origen_ciudad}}, {{origen_pais}}', '%1$s a %2$s desde {{origen_pais}}', 'Tiempo estimado', 'Modalidades', 'Entrega',
		'Cuánto cuesta enviar a %s', 'Restricciones para %s', 'De nuestra experiencia en esta ruta',
		'Cotizar este envío', 'Ver todo sobre %s', 'Ver todo sobre envíos a %s',
	) as $t ) $textos[] = $t;
	foreach ( grenvios_combo_servicios() as $s ) $textos[] = $s['verbo'];
	return $textos;
} );
