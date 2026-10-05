<?php
/**
 * Grenvíos — Edición desde el sitio (frontend) para guías y rutas.
 *
 * El tema ya tenía editor en línea para las PÁGINAS (inc/page-editor.php), que
 * trabaja sobre el registro de textos. Faltaban dos cosas que no encajan en ese
 * registro:
 *
 *   · las GUÍAS (entradas): título, extracto, contenido y a qué página refuerzan;
 *   · las RUTAS servicio × país: sus campos de contenido propio.
 *
 * Este módulo añade un panel propio para esos dos casos. Se abre con un botón
 * flotante que solo ven los administradores, guarda por REST y recarga.
 *
 * Decisión deliberada: el contenido de la guía se edita como HTML en un área de
 * texto, no con un editor visual. Meter TinyMCE en el frontend obliga a cargar
 * medio panel de WordPress en cada visita de un administrador y suele romper los
 * estilos del tema. Para corregir un párrafo, un dato o un enlace —que es el 90 %
 * de lo que se hace desde el sitio— el área de texto es más rápida y segura; para
 * reescribir una guía entera, el editor de WordPress sigue estando a un clic.
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ¿Hay algo editable con este panel en la vista actual? */
function grenvios_fe_contexto() {
	if ( ! is_singular() || ! current_user_can( 'edit_post', get_the_ID() ) ) return '';
	if ( is_singular( 'post' ) ) return 'guia';
	if ( is_page() && function_exists( 'grenvios_combo_of' ) && grenvios_combo_of( get_the_ID() ) ) return 'ruta';
	return '';
}

/* ══════════════════════════════════════
   REST
══════════════════════════════════════ */
add_action( 'rest_api_init', function () {
	register_rest_route( 'grenvios/v1', '/save-frontend', array(
		'methods'             => 'POST',
		'callback'            => 'grenvios_fe_save',
		'permission_callback' => function () { return current_user_can( 'edit_posts' ); },
	) );
} );

function grenvios_fe_save( WP_REST_Request $req ) {
	$post_id = (int) $req->get_param( 'post_id' );
	if ( ! $post_id || ! current_user_can( 'edit_post', $post_id ) ) {
		return new WP_REST_Response( array( 'success' => false, 'message' => 'Sin permiso.' ), 403 );
	}
	$tipo = sanitize_key( (string) $req->get_param( 'tipo' ) );
	$post = array( 'ID' => $post_id );

	/* ── Campos comunes del post ── */
	$titulo = $req->get_param( 'titulo' );
	if ( $titulo !== null ) $post['post_title'] = sanitize_text_field( (string) $titulo );

	$extracto = $req->get_param( 'extracto' );
	if ( $extracto !== null ) $post['post_excerpt'] = sanitize_textarea_field( (string) $extracto );

	$contenido = $req->get_param( 'contenido' );
	if ( $contenido !== null ) {
		// wp_kses_post: permite el HTML habitual de un artículo y bloquea scripts.
		$post['post_content'] = wp_kses_post( (string) $contenido );
	}

	$estado = $req->get_param( 'estado' );
	if ( $estado !== null && in_array( $estado, array( 'publish', 'draft' ), true ) ) {
		if ( $estado === 'publish' && ! current_user_can( 'publish_posts' ) ) {
			return new WP_REST_Response( array( 'success' => false, 'message' => 'No puedes publicar.' ), 403 );
		}
		$post['post_status'] = $estado;
	}

	if ( count( $post ) > 1 ) wp_update_post( $post );

	/* ── Guía: página de dinero que refuerza ── */
	if ( $tipo === 'guia' ) {
		$target = $req->get_param( 'cluster' );
		if ( $target !== null && defined( 'GRENVIOS_CLUSTER_META' ) ) {
			$target = (int) $target;
			if ( $target ) update_post_meta( $post_id, GRENVIOS_CLUSTER_META, $target );
			else delete_post_meta( $post_id, GRENVIOS_CLUSTER_META );
		}
		delete_transient( 'grenvios_links_graph' );
	}

	/* ── Ruta servicio × país: campos de contenido propio ── */
	if ( $tipo === 'ruta' && function_exists( 'grenvios_combo_campos' ) ) {
		$campos = $req->get_param( 'campos' );
		if ( is_array( $campos ) ) {
			foreach ( grenvios_combo_campos() as $key => $def ) {
				if ( ! isset( $campos[ $key ] ) ) continue;
				update_post_meta( $post_id, 'grenvios_' . $key, sanitize_textarea_field( (string) $campos[ $key ] ) );
			}
		}
	}

	/* ── Keyword objetivo (común a los dos) ── */
	$kw = $req->get_param( 'keyword' );
	if ( $kw !== null && defined( 'GRENVIOS_KW_META' ) ) {
		$kw = sanitize_text_field( (string) $kw );
		if ( $kw !== '' ) update_post_meta( $post_id, GRENVIOS_KW_META, $kw );
		else delete_post_meta( $post_id, GRENVIOS_KW_META );
	}
	delete_transient( 'grenvios_link_map' );

	$aviso = '';
	if ( $tipo === 'ruta' && function_exists( 'grenvios_combo_pendientes' ) ) {
		$p = grenvios_combo_pendientes( $post_id );
		if ( $p ) $aviso = 'Guardado. Sigue fuera del índice de Google: falta ' . implode( '; ', $p ) . '.';
	}
	return new WP_REST_Response( array( 'success' => true, 'message' => $aviso ), 200 );
}

/* ══════════════════════════════════════
   PANEL
══════════════════════════════════════ */
add_action( 'wp_footer', function () {
	$ctx = grenvios_fe_contexto();
	if ( ! $ctx ) return;

	$post = get_post();
	$id   = (int) $post->ID;
	$kw   = function_exists( 'grenvios_seo_kw' ) ? grenvios_seo_kw( $id, false ) : '';
	?>
	<button type="button" class="gr-fe-abrir" id="gr-fe-abrir">
		<?php echo esc_html( $ctx === 'guia' ? 'Editar guía' : 'Editar ruta' ); ?>
	</button>

	<div class="gr-fe-panel" id="gr-fe-panel" hidden>
		<header>
			<strong><?php echo esc_html( $ctx === 'guia' ? 'Editar guía' : 'Editar esta ruta' ); ?></strong>
			<button type="button" id="gr-fe-cerrar" aria-label="Cerrar">&times;</button>
		</header>

		<div class="gr-fe-body">
			<?php if ( $ctx === 'ruta' && function_exists( 'grenvios_combo_pendientes' ) ) :
				$pend = grenvios_combo_pendientes( $id ); ?>
				<div class="gr-fe-aviso <?php echo $pend ? 'is-warn' : 'is-ok'; ?>">
					<?php if ( $pend ) : ?>
						<strong>Fuera del índice de Google.</strong> Falta: <?php echo esc_html( implode( '; ', $pend ) ); ?>.
					<?php else : ?>
						<strong>Lista para indexar.</strong> Tiene contenido propio suficiente.
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<label>Título
				<input type="text" id="gr-fe-titulo" value="<?php echo esc_attr( $post->post_title ); ?>">
			</label>

			<label>Palabra clave objetivo
				<input type="text" id="gr-fe-kw" value="<?php echo esc_attr( $kw ); ?>" placeholder="ej. enviar documentos a estados unidos">
			</label>

			<?php if ( $ctx === 'guia' ) : ?>
				<label>Extracto <span>(el resumen que se ve en los listados y en Google)</span>
					<textarea id="gr-fe-extracto" rows="2"><?php echo esc_textarea( $post->post_excerpt ); ?></textarea>
				</label>

				<label>¿A qué página refuerza?
					<select id="gr-fe-cluster">
						<option value="0">— Ninguna —</option>
						<?php
						$sel = function_exists( 'grenvios_cluster_target' ) ? grenvios_cluster_target( $id ) : 0;
						foreach ( grenvios_money_pages() as $pid => $titulo ) : ?>
							<option value="<?php echo (int) $pid; ?>" <?php selected( $sel, $pid ); ?>><?php echo esc_html( $titulo ); ?></option>
						<?php endforeach; ?>
					</select>
				</label>

				<label>Contenido <span>(HTML: &lt;p&gt;, &lt;h2&gt;, &lt;ul&gt;, &lt;a&gt;…)</span>
					<textarea id="gr-fe-contenido" rows="16"><?php echo esc_textarea( $post->post_content ); ?></textarea>
				</label>

			<?php else :
				foreach ( grenvios_combo_campos() as $key => $def ) : ?>
					<label><?php echo esc_html( $def[0] ); ?> <span><?php echo esc_html( $def[2] ); ?></span>
						<textarea data-campo="<?php echo esc_attr( $key ); ?>" rows="3"><?php echo esc_textarea( get_post_meta( $id, 'grenvios_' . $key, true ) ); ?></textarea>
					</label>
				<?php endforeach;
			endif; ?>
		</div>

		<footer>
			<span class="gr-fe-msg" id="gr-fe-msg"></span>
			<?php if ( $post->post_status !== 'publish' && current_user_can( 'publish_posts' ) ) : ?>
				<button type="button" class="gr-fe-btn-sec" id="gr-fe-publicar">Guardar y publicar</button>
			<?php endif; ?>
			<button type="button" class="gr-fe-btn" id="gr-fe-guardar">Guardar</button>
			<a class="gr-fe-link" href="<?php echo esc_url( get_edit_post_link( $id ) ); ?>">Editor completo</a>
		</footer>
	</div>

	<style>
		.gr-fe-abrir{position:fixed;left:20px;bottom:20px;z-index:99998;padding:10px 18px;border:0;border-radius:24px;
			background:#111;color:#fff;font-size:14px;font-weight:600;cursor:pointer;box-shadow:0 4px 14px rgba(0,0,0,.25)}
		.gr-fe-panel{position:fixed;top:0;right:0;width:min(520px,100%);height:100%;z-index:99999;background:#fff;
			box-shadow:-4px 0 24px rgba(0,0,0,.18);display:flex;flex-direction:column;font-size:14px;color:#1d2327}
		.gr-fe-panel header{display:flex;justify-content:space-between;align-items:center;padding:14px 18px;border-bottom:1px solid #e0e0e0}
		.gr-fe-panel header button{background:0;border:0;font-size:26px;line-height:1;cursor:pointer;color:#666}
		.gr-fe-body{flex:1;overflow:auto;padding:16px 18px}
		.gr-fe-body label{display:block;margin-bottom:16px;font-weight:600}
		.gr-fe-body label span{display:block;font-weight:400;font-size:12px;opacity:.7;margin:2px 0 4px}
		.gr-fe-body input,.gr-fe-body textarea,.gr-fe-body select{width:100%;padding:8px 10px;border:1px solid #c3c4c7;
			border-radius:5px;font:inherit;font-weight:400;margin-top:5px}
		.gr-fe-body textarea{font-family:Menlo,Consolas,monospace;font-size:13px;line-height:1.5}
		.gr-fe-aviso{padding:10px 12px;border-radius:6px;margin-bottom:16px;font-weight:400}
		.gr-fe-aviso.is-warn{background:#fcf3d7;border-left:3px solid #dba617}
		.gr-fe-aviso.is-ok{background:#e4f6e6;border-left:3px solid #00a32a}
		.gr-fe-panel footer{display:flex;align-items:center;gap:12px;padding:12px 18px;border-top:1px solid #e0e0e0}
		.gr-fe-btn{margin-left:auto;padding:9px 20px;border:0;border-radius:5px;background:#2271b1;color:#fff;font-weight:600;cursor:pointer}
		.gr-fe-btn-sec{padding:9px 16px;border:1px solid #2271b1;border-radius:5px;background:#fff;color:#2271b1;font-weight:600;cursor:pointer}
		.gr-fe-link{font-size:13px;color:#2271b1}
		.gr-fe-msg{font-size:13px;opacity:.85;flex:1}
		@media(max-width:600px){.gr-fe-panel{width:100%}}
	</style>

	<script>
	(function(){
		var abrir = document.getElementById('gr-fe-abrir'),
		    panel = document.getElementById('gr-fe-panel'),
		    msg   = document.getElementById('gr-fe-msg'),
		    tipo  = <?php echo wp_json_encode( $ctx ); ?>,
		    id    = <?php echo (int) $id; ?>,
		    nonce = <?php echo wp_json_encode( wp_create_nonce( 'wp_rest' ) ); ?>,
		    url   = <?php echo wp_json_encode( esc_url_raw( rest_url( 'grenvios/v1/save-frontend' ) ) ); ?>;

		abrir.addEventListener('click', function(){ panel.hidden = false; abrir.hidden = true; });
		document.getElementById('gr-fe-cerrar').addEventListener('click', function(){ panel.hidden = true; abrir.hidden = false; });

		function val(sel){ var el = document.getElementById(sel); return el ? el.value : null; }

		function guardar(estado){
			var datos = { post_id:id, tipo:tipo, titulo:val('gr-fe-titulo'), keyword:val('gr-fe-kw') };
			if(estado) datos.estado = estado;

			if(tipo === 'guia'){
				datos.extracto  = val('gr-fe-extracto');
				datos.contenido = val('gr-fe-contenido');
				datos.cluster   = val('gr-fe-cluster');
			} else {
				datos.campos = {};
				panel.querySelectorAll('[data-campo]').forEach(function(t){ datos.campos[t.dataset.campo] = t.value; });
			}

			msg.textContent = 'Guardando…';
			fetch(url, {
				method:'POST', credentials:'same-origin',
				headers:{'Content-Type':'application/json','X-WP-Nonce':nonce},
				body: JSON.stringify(datos)
			})
			.then(function(r){ return r.json(); })
			.then(function(j){
				if(j && j.success){
					msg.textContent = j.message || 'Guardado. Recargando…';
					setTimeout(function(){ location.reload(); }, j.message ? 2200 : 600);
				} else {
					msg.textContent = (j && j.message) ? j.message : 'No se pudo guardar.';
				}
			})
			.catch(function(){ msg.textContent = 'Error de conexión.'; });
		}

		document.getElementById('gr-fe-guardar').addEventListener('click', function(){ guardar(null); });
		var pub = document.getElementById('gr-fe-publicar');
		if(pub) pub.addEventListener('click', function(){ guardar('publish'); });
	})();
	</script>
	<?php
}, 99 );
