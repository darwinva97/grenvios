<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Diseño v3 de las páginas de ruta: bloques del país, formulario de
 *  solicitud al pie y cabecera del blog por país (2026-10-03)
 * ══════════════════════════════════════════════════════════════════════════
 *
 * PEDIDO: «/co/envio-de-documentos-a-colombia/ … sub páginas que no tienen
 * estilo de página y abajo un formulario en cada página que llegará al mail
 * … el hero del blog tiene que cambiar para cada país».
 *
 *   1) Bloques del país (grenvios_pais_render): eran título centrado + párrafo
 *      sin diseño. Ahora cabecera a la izquierda con icono y antetítulo por
 *      tema, cuerpo en tarjeta, listas con vistos, tablas con estilo y la
 *      llamada «¿Envías a X?» como banner vino. Se transforma el HTML al
 *      pintarlo (el bloque está guardado en el contenido): mismos textos, así
 *      que el panel los sigue reconociendo.
 *   2) Formulario de solicitud al pie de cada página (salvo portada, cotizar y
 *      contacto, que ya lo tienen). Sustituye a la llamada final para no
 *      repetirla. Envía con wp_mail al correo de la sede y guarda cada
 *      solicitud en el panel (Solicitudes), por si el correo falla.
 *   3) Blog de cada ruta: cabecera propia con la foto del país, buscador,
 *      temas, cifras y la guía más reciente del país.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* País de la ruta actual: [slug, nombre] o ['', ''] en la principal. */
function grenvios_rd_pais() {
	$s = function_exists( 'grenvios_perfil_pais_actual' ) ? grenvios_perfil_pais_actual() : '';
	if ( $s === '' ) return array( '', '' );
	$d = function_exists( 'grenvios_pais_datos' ) ? grenvios_pais_datos( $s ) : array();
	return array( $s, ! empty( $d['title'] ) ? $d['title'] : ucfirst( $s ) );
}

/* ═════════════════════════════════════════════════════════════════════════
 * 1) Bloques del país v3
 * ═════════════════════════════════════════════════════════════════════════ */
function grenvios_rd_etiqueta( $titulo ) {
	$t = remove_accents( mb_strtolower( wp_strip_all_tags( $titulo ) ) );
	$m = array(
		'plazo' => 'Plazos', 'tarda' => 'Plazos', 'demora' => 'Plazos', 'ruta' => 'La ruta', 'via' => 'La ruta',
		'aduana' => 'Aduana', 'impuesto' => 'Aduana', 'paga' => 'Aduana', 'document' => 'Documentos',
		'entrega' => 'Entrega', 'recibe' => 'Entrega', 'ciudad' => 'Cobertura', 'prohib' => 'Restricciones',
		'no se puede' => 'Restricciones', 'embal' => 'Embalaje', 'precio' => 'Precio', 'cuesta' => 'Precio',
		'compar' => 'Comparativa', 'quien' => 'Quién envía', 'comunidad' => 'Quién envía', 'temporada' => 'Fechas clave',
		'error' => 'Errores frecuentes', 'checklist' => 'Antes de despachar', 'lista' => 'Antes de despachar',
		// Al final: un título sobre impuestos o plazos «terrestres» es de aduana o de plazos.
		'aere' => 'La ruta', 'terrestre' => 'La ruta',
	);
	/* Desde el inicio de palabra: «via» no debe saltar dentro de «enviarlas». */
	foreach ( $m as $k => $v ) if ( preg_match( '~\\b' . preg_quote( $k, '~' ) . '~u', $t ) ) return $v;
	return '';
}

function grenvios_rd_bloques( $html ) {
	if ( ! is_string( $html ) || strpos( $html, 'gr-pais-sec' ) === false ) return $html;
	list( , $pais ) = grenvios_rd_pais();
	$r = preg_replace_callback(
		'~<section class="srv-section gr-pais-sec ([^"]*)">\s*<div class="container">\s*<div class="gr-pais-head text-center">\s*<h2>(.*?)</h2>\s*</div>\s*<div class="gr-pais-body">(.*?)</div>\s*</div>\s*</section>~s',
		function ( $m ) use ( $pais ) {
			$clases = trim( $m[1] ); $tit = $m[2]; $body = $m[3];
			/* El bloque de preguntas va en su propia sección de preguntas frecuentes. */
			if ( strpos( $body, 'gr-faq-item' ) !== false ) return $m[0];
			$grey  = strpos( $clases, 'bg-grey' ) !== false ? ' bg-grey' : '';
			$texto = trim( wp_strip_all_tags( $body ) );

			/* Llamada «¿Envías a X?»: banner. */
			if ( strpos( $body, 'btn-group' ) !== false && mb_strlen( $texto ) < 320 ) {
				$btn = preg_match( '~<div class="btn-group">.*?</div>~s', $body, $bb ) ? $bb[0] : '';
				$txt = trim( str_replace( $btn, '', $body ) );
				$btn = str_replace( 'class="default-btn"', 'class="default-btn btn-light"', $btn );
				return '<section class="srv-section gr-pais-sec gr-pv3 gr-pv3--cta padding"><div class="container"><div class="gr-pv3-cta wow fade-in-bottom" data-wow-delay="100ms">'
					. '<span class="gr-pv3-cta-ic" aria-hidden="true"><i class="fa-solid fa-paper-plane"></i></span>'
					. '<div class="gr-pv3-cta-tx"><h2>' . $tit . '</h2>' . $txt . '</div>'
					. '<div class="gr-pv3-cta-btn">' . $btn . '</div></div></div></section>';
			}

			$ic  = function_exists( 'grenvios_ui_icono' ) ? grenvios_ui_icono( wp_strip_all_tags( $tit ), 'fa-circle-info' ) : 'fa-circle-info';
			$eti = grenvios_rd_etiqueta( $tit );
			$sub = $eti !== '' ? $eti . ( $pais !== '' ? ' · ' . $pais : '' ) : ( $pais !== '' ? 'Envíos a ' . $pais : '' );
			/* Solo un párrafo: nota destacada a todo el ancho. */
			$corto = substr_count( $body, '<p' ) <= 1 && strpos( $body, '<ul' ) === false && strpos( $body, '<table' ) === false;
			return '<section class="srv-section gr-pais-sec gr-pv3' . ( $corto ? ' gr-pv3--nota' : '' ) . $grey . ' padding"><div class="container"><div class="gr-pv3-grid">'
				. '<header class="gr-pv3-head wow fade-in-bottom" data-wow-delay="100ms"><span class="gr-pv3-ic" aria-hidden="true"><i class="fa-solid ' . esc_attr( $ic ) . '"></i></span>'
				. ( $sub !== '' ? '<p class="gr-pv3-sub">' . esc_html( $sub ) . '</p>' : '' )
				. '<h2>' . $tit . '</h2></header>'
				. '<div class="gr-pais-body gr-pv3-body wow fade-in-bottom" data-wow-delay="200ms">' . $body . '</div>'
				. '</div></div></section>';
		},
		$html
	);
	return is_string( $r ) ? $r : $html;
}
add_filter( 'grenvios_pais_bloque_html', 'grenvios_rd_bloques', 10 );

/* ═════════════════════════════════════════════════════════════════════════
 * 2) Formulario de solicitud al pie de cada página
 * ═════════════════════════════════════════════════════════════════════════ */
function grenvios_rd_form_activo( $slug ) {
	if ( is_front_page() || ! is_page() ) return false;
	return ! in_array( (string) $slug, apply_filters( 'grenvios_form_excluir', array( 'cotizar', 'contacto', 'home', 'rastreo-de-envios' ) ), true );
}

/* La llamada final («¿Listo para enviar?») se sustituye por el formulario. */
add_filter( 'grenvios_cta_enabled', function ( $on, $slug ) {
	return grenvios_rd_form_activo( $slug ) ? false : $on;
}, 20, 2 );

/* Tipo de envío sugerido por el tema de la página. */
function grenvios_rd_tipo( $slug ) {
	$s = (string) $slug;
	if ( preg_match( '~document|apostill|traducc|correspond~', $s ) ) return 'Documentos';
	if ( preg_match( '~carga|empresa|muestra|repuesto|mudanza~', $s ) ) return 'Carga';
	if ( preg_match( '~equipaje~', $s ) ) return 'Equipaje';
	return 'Paquete';
}

/* Correo que recibe las solicitudes: el de la sede, con respaldo. */
function grenvios_rd_destinatario() {
	$e = function_exists( 'grenvios_sede_tokens_apply' ) ? trim( grenvios_sede_tokens_apply( '{{contacto_email}}' ) ) : '';
	if ( ! is_email( $e ) ) $e = (string) apply_filters( 'grenvios_form_email', 'info@grenvios.com' );
	return $e;
}

function grenvios_rd_form_html( $slug ) {
	list( $ps, $pn ) = grenvios_rd_pais();
	$tipo  = grenvios_rd_tipo( $slug );
	$dest  = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();
	$ops   = '';
	foreach ( $dest as $ds => $dd ) {
		$pd  = function_exists( 'grenvios_pais_datos' ) ? grenvios_pais_datos( $ds ) : array();
		$nom = ! empty( $pd['title'] ) ? $pd['title'] : ucfirst( $ds );
		$ops .= '<option' . selected( $ds, $ps, false ) . '>' . esc_html( $nom ) . '</option>';
	}
	$ops .= '<option>Otro país</option>';
	$tipos = '';
	foreach ( array( 'Documentos', 'Paquete', 'Equipaje', 'Carga' ) as $t ) $tipos .= '<option' . selected( $t, $tipo, false ) . '>' . esc_html( $t ) . '</option>';
	$wa  = function_exists( 'grenvios_sede_tokens_apply' ) ? trim( grenvios_sede_tokens_apply( '{{contacto_wa}}' ) ) : '';
	$tel = function_exists( 'grenvios_sede_tokens_apply' ) ? trim( grenvios_sede_tokens_apply( '{{contacto_telefono}}' ) ) : '';
	if ( strpos( $wa, '{{' ) !== false ) $wa = '';
	if ( strpos( $tel, '{{' ) !== false ) $tel = '';
	$wa_num = preg_replace( '/\D/', '', $wa !== '' ? $wa : $tel );
	$titulo = $pn !== '' ? 'Cotiza tu envío a <span class="hl">' . esc_html( $pn ) . '</span>' : 'Cotiza tu envío <span class="hl">internacional</span>';
	$ok     = isset( $_GET['solicitud'] ) && $_GET['solicitud'] === 'ok';

	ob_start(); ?>
	<section class="gr-lf padding" id="solicitud" aria-labelledby="gr-lf-t">
		<div class="container">
			<div class="gr-lf-grid">
				<div class="gr-lf-tx wow fade-in-bottom" data-wow-delay="100ms">
					<p class="gr-lf-sub">Solicitud de cotización</p>
					<h2 id="gr-lf-t"><?php echo $titulo; // phpcs:ignore -- escapado arriba ?></h2>
					<p>Cuéntanos qué envías y a qué ciudad: te respondemos con el precio y el plazo, sin compromiso.</p>
					<ul class="gr-lf-ventajas">
						<li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Respuesta en menos de 24 horas hábiles</li>
						<li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Revisamos contigo qué admite <?php echo $pn !== '' ? esc_html( $pn ) : 'el destino'; ?> antes de despachar</li>
						<li><i class="fa-solid fa-circle-check" aria-hidden="true"></i> Recojo a domicilio en Lima</li>
					</ul>
					<?php if ( $wa_num !== '' ) : ?>
					<a class="gr-lf-wa" href="https://wa.me/<?php echo esc_attr( $wa_num ); ?>" target="_blank" rel="noopener"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i><span>¿Prefieres WhatsApp?<strong><?php echo esc_html( $tel !== '' ? $tel : $wa ); ?></strong></span></a>
					<?php endif; ?>
				</div>
				<form class="gr-lf-form wow fade-in-bottom" data-wow-delay="200ms" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate>
					<input type="hidden" name="action" value="grenvios_solicitud">
					<input type="hidden" name="origen" value="<?php echo esc_attr( get_permalink() ); ?>">
					<input type="hidden" name="ts" value="<?php echo esc_attr( time() ); ?>">
					<div class="gr-lf-hp" aria-hidden="true"><label for="gr-lf-web">Web</label><input id="gr-lf-web" type="text" name="web" tabindex="-1" autocomplete="off"></div>
					<div class="gr-lf-row">
						<p><label for="gr-lf-nombre">Nombre y apellido <span class="gr-lf-req" aria-hidden="true">*</span></label><input id="gr-lf-nombre" name="nombre" type="text" class="form-control" autocomplete="name" required></p>
						<p><label for="gr-lf-tel">WhatsApp o teléfono <span class="gr-lf-req" aria-hidden="true">*</span></label><input id="gr-lf-tel" name="telefono" type="tel" class="form-control" autocomplete="tel" required></p>
					</div>
					<div class="gr-lf-row">
						<p><label for="gr-lf-email">Correo <span class="gr-lf-req" aria-hidden="true">*</span></label><input id="gr-lf-email" name="email" type="email" class="form-control" autocomplete="email" required></p>
						<p><label for="gr-lf-pais">País de destino</label><select id="gr-lf-pais" name="pais" class="form-control gr-select-nativo"><?php echo $ops; // phpcs:ignore ?></select></p>
					</div>
					<div class="gr-lf-row">
						<p><label for="gr-lf-tipo">Qué envías</label><select id="gr-lf-tipo" name="tipo" class="form-control gr-select-nativo"><?php echo $tipos; // phpcs:ignore ?></select></p>
						<p><label for="gr-lf-peso">Peso aproximado (kg)</label><input id="gr-lf-peso" name="peso" type="text" inputmode="decimal" class="form-control"></p>
					</div>
					<p><label for="gr-lf-msg">Detalle del envío</label><textarea id="gr-lf-msg" name="mensaje" rows="3" class="form-control" placeholder="Contenido, medidas, ciudad de destino…"></textarea></p>
					<p class="gr-lf-consent"><input id="gr-lf-ok" name="consent" type="checkbox" value="1" required> <label for="gr-lf-ok">Acepto que Grenvíos me contacte para responder a mi solicitud.</label></p>
					<button type="submit" class="default-btn gr-lf-btn">Enviar solicitud <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></button>
					<p class="gr-lf-msgbox" role="status" aria-live="polite"<?php echo $ok ? '' : ' hidden'; ?>><?php echo $ok ? '¡Gracias! Recibimos tu solicitud y te responderemos pronto.' : ''; ?></p>
				</form>
			</div>
		</div>
	</section>
	<script>
	(function(){var f=document.querySelector('.gr-lf-form');if(!f||!window.fetch)return;f.addEventListener('submit',function(e){
		if(!f.checkValidity()){f.reportValidity();e.preventDefault();return;}
		e.preventDefault();var b=f.querySelector('.gr-lf-btn'),m=f.querySelector('.gr-lf-msgbox');b.disabled=true;
		var d=new FormData(f);d.append('ajax','1');
		fetch(f.action,{method:'POST',body:d,credentials:'same-origin'}).then(function(r){return r.json();}).then(function(j){
			m.hidden=false;m.textContent=j.mensaje||'';m.className='gr-lf-msgbox '+(j.ok?'is-ok':'is-error');if(j.ok)f.reset();b.disabled=false;
		}).catch(function(){m.hidden=false;m.className='gr-lf-msgbox is-error';m.textContent='No pudimos enviar tu solicitud. Escríbenos por WhatsApp.';b.disabled=false;});
	});})();
	</script>
	<?php
	return ob_get_clean();
}

add_action( 'grenvios_pagina_cierre', function ( $slug ) {
	if ( grenvios_rd_form_activo( $slug ) ) echo grenvios_rd_form_html( $slug ); // phpcs:ignore
} );

/* Registro de solicitudes en el panel. */
add_action( 'init', function () {
	register_post_type( 'gr_solicitud', array(
		'labels'          => array( 'name' => 'Solicitudes', 'singular_name' => 'Solicitud', 'menu_name' => 'Solicitudes' ),
		'public'          => false,
		'show_ui'         => true,
		'menu_icon'       => 'dashicons-email-alt',
		'menu_position'   => 26,
		'supports'        => array( 'title', 'editor' ),
		'capability_type' => 'post',
		'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
		'map_meta_cap'    => true,
	) );
} );

/* Recepción (con y sin JavaScript). */
function grenvios_rd_recibir() {
	$ajax = ! empty( $_POST['ajax'] );
	$fin  = function ( $ok, $msg ) use ( $ajax ) {
		if ( $ajax ) wp_send_json( array( 'ok' => $ok, 'mensaje' => $msg ) );
		$back = isset( $_POST['origen'] ) ? esc_url_raw( wp_unslash( $_POST['origen'] ) ) : home_url( '/' );
		wp_safe_redirect( add_query_arg( 'solicitud', $ok ? 'ok' : 'error', $back ) . '#solicitud' );
		exit;
	};
	$v = function ( $k, $max = 200 ) { return isset( $_POST[ $k ] ) ? mb_substr( sanitize_text_field( wp_unslash( $_POST[ $k ] ) ), 0, $max ) : ''; };

	/* Antispam: campo trampa vacío y al menos 3 s entre cargar y enviar. */
	if ( $v( 'web' ) !== '' || ( time() - (int) $v( 'ts' ) ) < 3 ) $fin( true, '¡Gracias! Recibimos tu solicitud.' );

	$nombre = $v( 'nombre', 120 ); $tel = $v( 'telefono', 40 ); $email = sanitize_email( $v( 'email', 120 ) );
	if ( $nombre === '' || $tel === '' || ! is_email( $email ) || empty( $_POST['consent'] ) ) {
		$fin( false, 'Revisa tu nombre, teléfono y correo, y acepta que te contactemos.' );
	}
	$pais = $v( 'pais', 60 ); $tipo = $v( 'tipo', 30 ); $peso = $v( 'peso', 20 );
	$msg  = isset( $_POST['mensaje'] ) ? mb_substr( sanitize_textarea_field( wp_unslash( $_POST['mensaje'] ) ), 0, 2000 ) : '';
	$orig = isset( $_POST['origen'] ) ? esc_url_raw( wp_unslash( $_POST['origen'] ) ) : '';

	$cuerpo = "Nombre: $nombre\nTeléfono: $tel\nCorreo: $email\nPaís de destino: $pais\nQué envía: $tipo\nPeso aprox. (kg): $peso\n\nDetalle:\n$msg\n\nPágina: $orig\n";
	$id = wp_insert_post( array(
		'post_type' => 'gr_solicitud', 'post_status' => 'private',
		'post_title' => $nombre . ' · ' . $pais . ' · ' . $tipo, 'post_content' => $cuerpo,
	) );
	$enviado = wp_mail(
		grenvios_rd_destinatario(),
		'Nueva solicitud: ' . $tipo . ' a ' . $pais . ' — ' . $nombre,
		$cuerpo,
		array( 'Reply-To: ' . $nombre . ' <' . $email . '>' )
	);
	if ( $id ) update_post_meta( $id, '_gr_correo_enviado', $enviado ? 1 : 0 );
	$fin( true, '¡Gracias, ' . $nombre . '! Recibimos tu solicitud y te responderemos pronto con el precio y el plazo.' );
}
add_action( 'admin_post_nopriv_grenvios_solicitud', 'grenvios_rd_recibir' );
add_action( 'admin_post_grenvios_solicitud', 'grenvios_rd_recibir' );

/* ═════════════════════════════════════════════════════════════════════════
 * 3) Cabecera del blog por país (/blog/ y /<ruta>/guias-para-enviar-a-<país>/)
 * ═════════════════════════════════════════════════════════════════════════ */
function grenvios_rd_blog_hero() {
	list( $ps, $pn ) = grenvios_rd_pais();
	$lang  = function_exists( 'pll_current_language' ) ? (string) pll_current_language() : '';
	$foto  = function_exists( 'grenvios_ej_img' ) ? grenvios_ej_img( $ps !== '' ? $ps : 'almacen' ) : '';
	if ( $foto === '' && function_exists( 'grenvios_ej_por_tema' ) ) $foto = grenvios_ej_por_tema( $pn, 'almacen' );
	$eyebrow = grenvios_field( 'blog_eyebrow', $pn !== '' ? 'Blog · Envíos a ' . $pn : 'Blog de Grenvíos' );
	$title   = grenvios_field( 'blog_title', $pn !== '' ? 'Guías para enviar a <span>' . esc_html( $pn ) . '</span>' : 'Guías de envíos <span>internacionales</span>' );
	$lead    = $pn !== ''
		? 'Todo lo que conviene saber antes de enviar a ' . $pn . ': aduana, plazos, ciudades, documentos y qué se puede mandar. Escrito por el equipo que despacha la ruta cada semana.'
		: 'Guías prácticas para enviar documentos, paquetes y carga desde Perú: aduana, plazos, embalaje y precios explicados por quienes despachan cada semana.';

	/* Cifras reales del blog de esta ruta. */
	$args = array( 'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids' );
	if ( $lang !== '' ) $args['lang'] = $lang;
	if ( $ps !== '' ) $args['meta_key'] = 'grenvios_solo_pais';
	$n_guias = count( get_posts( $args ) );
	$cats    = get_categories( array( 'hide_empty' => true ) );

	/* Guía destacada: la más reciente del país (o de la ruta principal). */
	$dq = array( 'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => 1 );
	if ( $lang !== '' ) $dq['lang'] = $lang;
	if ( $ps !== '' ) $dq['meta_key'] = 'grenvios_solo_pais';
	$dest = get_posts( $dq );
	$dest = $dest ? $dest[0] : null;
	$buscar = ( function_exists( 'pll_home_url' ) && $lang !== '' ) ? pll_home_url( $lang ) : home_url( '/' );

	ob_start(); ?>
	<header class="gr-bh"<?php if ( $foto ) : ?> style="--gr-bh-foto:url('<?php echo esc_url( $foto ); ?>')"<?php endif; ?>>
		<div class="container">
			<div class="gr-bh-grid">
				<div class="gr-bh-tx">
					<p class="gr-bh-eyebrow"><i class="fa-solid fa-book-open" aria-hidden="true"></i> <?php echo esc_html( wp_strip_all_tags( $eyebrow ) ); ?></p>
					<h1 class="gr-bh-title"><?php echo wp_kses( $title, array( 'span' => array() ) ); ?></h1>
					<p class="gr-bh-lead"><?php echo esc_html( $lead ); ?></p>
					<form class="gr-bh-buscar" role="search" method="get" action="<?php echo esc_url( $buscar ); ?>">
						<label class="screen-reader-text" for="gr-bh-s">Buscar en las guías</label>
						<i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
						<input id="gr-bh-s" type="search" name="s" placeholder="<?php echo esc_attr( $pn !== '' ? 'Busca en las guías de ' . $pn . '…' : 'Busca una guía…' ); ?>">
						<button type="submit" class="default-btn">Buscar</button>
					</form>
					<?php if ( $cats ) : ?>
					<ul class="gr-bh-temas" aria-label="Temas">
						<?php foreach ( array_slice( $cats, 0, 6 ) as $c ) : ?>
							<li><a href="<?php echo esc_url( get_category_link( $c ) ); ?>"><?php echo esc_html( $c->name ); ?></a></li>
						<?php endforeach; ?>
					</ul>
					<?php endif; ?>
					<ul class="gr-bh-cifras">
						<li><strong><?php echo (int) $n_guias; ?></strong><span><?php echo $pn !== '' ? 'guías sobre ' . esc_html( $pn ) : 'guías publicadas'; ?></span></li>
						<li><strong><?php echo (int) count( $cats ); ?></strong><span>temas</span></li>
						<li><strong><i class="fa-solid fa-user-check" aria-hidden="true"></i></strong><span>escritas por el equipo de Grenvíos</span></li>
					</ul>
				</div>
				<?php if ( $dest ) : ?>
				<a class="gr-bh-dest" href="<?php echo esc_url( get_permalink( $dest ) ); ?>">
					<span class="gr-bh-dest-img"><img src="<?php echo esc_url( function_exists( 'grenvios_bd_img' ) ? grenvios_bd_img( $dest->ID ) : '' ); ?>" alt="" width="640" height="400" fetchpriority="high" decoding="async"></span>
					<span class="gr-bh-dest-tx">
						<span class="gr-bh-dest-sub">Guía más reciente</span>
						<strong><?php echo esc_html( get_the_title( $dest ) ); ?></strong>
						<span class="gr-bh-dest-ex"><?php echo esc_html( wp_trim_words( get_the_excerpt( $dest ), 18 ) ); ?></span>
						<span class="gr-bh-dest-more">Leer la guía <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
					</span>
				</a>
				<?php endif; ?>
			</div>
		</div>
	</header>
	<?php
	return ob_get_clean();
}

/* Foto de una tarjeta del listado. Las guías de ciudad («Envíos a Cuenca,
 * Ecuador…») daban todas la misma foto por la palabra «entrega» del título:
 * alternan la foto del país y otras afines según el id, para que el listado
 * no repita la misma imagen seguida. */
function grenvios_rd_card_img( $p ) {
	$p = get_post( $p );
	if ( ! $p || ! function_exists( 'grenvios_ej_por_tema' ) ) return '';
	$pais = (string) get_post_meta( $p->ID, 'grenvios_solo_pais', true );
	$clave = (string) get_post_meta( $p->ID, 'grenvios_guia_key', true );
	if ( $pais !== '' && strpos( $clave, 'envios-a-' ) === 0 && function_exists( 'grenvios_ej_img' ) ) {
		$pool = array( $pais, 'entrega', 'recojo', 'bodega', 'almacen-pasillo', 'caja' );
		$u = grenvios_ej_img( $pool[ $p->ID % count( $pool ) ] );
		if ( $u !== '' ) return $u;
	}
	return grenvios_ej_por_tema( $p->post_title, 'embalaje', true );
}

/* ── Estilos ────────────────────────────────────────────────────────── */
add_action( 'wp_enqueue_scripts', function () {
	$f = get_template_directory() . '/assets/css/gr-rutas.css';
	if ( file_exists( $f ) ) wp_enqueue_style( 'gr-rutas', get_template_directory_uri() . '/assets/css/gr-rutas.css', array(), (string) filemtime( $f ) );
}, 31 );
