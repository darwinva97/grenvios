<?php
/**
 * Grenvíos — Pantalla "Traducciones".
 *
 * Una sola pantalla para la clienta:
 *   · Estado de cada página en cada idioma (falta / al día / desactualizada).
 *   · Botón "Traducir todo" y botón por página/idioma.
 *   · Ajustes del traductor (proveedor y clave de API).
 *   · Diagnóstico de SEO internacional.
 *
 * El trabajo se hace por lotes vía AJAX (una unidad por petición) para que no
 * se agote el tiempo de ejecución de PHP aunque haya 30 páginas × 4 idiomas.
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ── Menú ── */
/* Solo aparece si hay multiidioma activo: en un sitio monolingüe no tiene
 * sentido ocupar un hueco del escritorio con una pantalla vacía. */
add_action( 'admin_menu', function () {
	if ( ! grenvios_i18n_active() ) return;
	add_menu_page(
		'Traducciones', 'Traducciones', 'edit_pages',
		'grenvios-i18n', 'grenvios_i18n_admin_page',
		'dashicons-translation', 28
	);
} );

/* ── Guardado de ajustes (patrón PRG) ── */
add_action( 'admin_post_grenvios_i18n_settings', function () {
	if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Sin permiso.' );
	check_admin_referer( 'grenvios_i18n_settings' );

	$in  = wp_unslash( $_POST );
	foreach ( array( 'provider', 'claude_key', 'claude_model', 'deepl_key', 'google_key', 'brand', 'tone' ) as $gk ) {
		if ( ! isset( $in[ $gk ] ) ) $in[ $gk ] = '';
	}
	$cur = grenvios_i18n_settings();
	$new = array(
		'provider'     => in_array( $in['provider'], array( 'claude', 'deepl', 'google', 'manual' ), true ) ? $in['provider'] : 'claude',
		'claude_key'   => trim( (string) $in['claude_key'] ),
		'claude_model' => sanitize_text_field( $in['claude_model'] ),
		'deepl_key'    => trim( (string) $in['deepl_key'] ),
		'google_key'   => trim( (string) $in['google_key'] ),
		'brand'        => sanitize_textarea_field( $in['brand'] ),
		'tone'         => sanitize_textarea_field( $in['tone'] ),
	);
	// Campos de clave vacíos = "no cambiar" (así no se borra al guardar el resto).
	foreach ( array( 'claude_key', 'deepl_key', 'google_key' ) as $k ) {
		if ( $new[ $k ] === '' && ! empty( $cur[ $k ] ) ) $new[ $k ] = $cur[ $k ];
	}
	update_option( 'grenvios_i18n_settings', $new, false );

	wp_safe_redirect( add_query_arg( 'gr_msg', 'saved', admin_url( 'admin.php?page=grenvios-i18n' ) ) );
	exit;
} );

/* ── Borrar el diccionario de un idioma (forzar retraducción total) ── */
add_action( 'admin_post_grenvios_i18n_reset', function () {
	if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Sin permiso.' );
	check_admin_referer( 'grenvios_i18n_reset' );
	$lang = isset( $_GET['lang'] ) ? sanitize_key( $_GET['lang'] ) : '';
	if ( ! $lang ) wp_die( 'Idioma no indicado.' );
	delete_option( grenvios_i18n_dict_key( $lang ) );
	delete_option( grenvios_i18n_missing_key( $lang ) );
	delete_option( grenvios_i18n_destinos_key( $lang ) );
	wp_safe_redirect( add_query_arg( 'gr_msg', 'reset', admin_url( 'admin.php?page=grenvios-i18n' ) ) );
	exit;
} );

/* ══════════════════════════════════════
   AJAX: una unidad de trabajo por petición
══════════════════════════════════════ */
add_action( 'wp_ajax_grenvios_i18n_run', function () {
	if ( ! current_user_can( 'edit_pages' ) ) wp_send_json_error( array( 'msg' => 'Sin permiso.' ) );
	check_ajax_referer( 'grenvios_i18n_run', 'nonce' );

	$type  = isset( $_POST['type'] ) ? sanitize_key( $_POST['type'] ) : '';
	$lang  = isset( $_POST['lang'] ) ? sanitize_key( $_POST['lang'] ) : '';
	if ( ! isset( grenvios_i18n_langs()[ $lang ] ) ) wp_send_json_error( array( 'msg' => 'Idioma desconocido.' ) );
	$id    = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;
	$force = ! empty( $_POST['force'] );

	@set_time_limit( 180 );

	switch ( $type ) {
		case 'page':
			$r = grenvios_i18n_translate_page( $id, $lang, $force );
			if ( is_wp_error( $r ) ) wp_send_json_error( array( 'msg' => $r->get_error_message() ) );
			wp_send_json_success( array(
				'msg' => get_the_title( $id ) . ' → ' . grenvios_i18n_lang_name( $lang ),
				'url' => get_permalink( $r ),
			) );

		case 'post':
			$r = grenvios_i18n_translate_post( $id, $lang, $force );
			if ( is_wp_error( $r ) ) wp_send_json_error( array( 'msg' => $r->get_error_message() ) );
			wp_send_json_success( array(
				'msg' => get_the_title( $id ) . ' → ' . grenvios_i18n_lang_name( $lang ),
				'url' => get_permalink( $r ),
			) );

		case 'strings':
			$n = grenvios_i18n_translate_strings( $lang );
			wp_send_json_success( array( 'msg' => 'Textos del diseño (' . grenvios_i18n_lang_name( $lang ) . '): ' . $n ) );

		case 'destinos':
			$n = grenvios_i18n_translate_destinos( $lang );
			wp_send_json_success( array( 'msg' => 'Fichas de países (' . grenvios_i18n_lang_name( $lang ) . '): ' . $n ) );

		case 'test':
			$t = grenvios_i18n_translate_one( 'Envíos internacionales de paquetes y documentos desde Lima.', $lang, 'prueba' );
			wp_send_json_success( array( 'msg' => 'Prueba → ' . $t ) );
	}
	wp_send_json_error( array( 'msg' => 'Trabajo desconocido.' ) );
} );

/* ══════════════════════════════════════
   PANTALLA
══════════════════════════════════════ */
function grenvios_i18n_admin_page() {
	if ( ! current_user_can( 'edit_pages' ) ) wp_die( 'Sin permiso.' );

	$s      = grenvios_i18n_settings();
	$langs  = grenvios_i18n_langs();
	$def    = grenvios_i18n_default();
	$pages  = grenvios_i18n_master_pages();
	$others = array_filter( $langs, function ( $l ) { return empty( $l['default'] ); } );
	$msg    = isset( $_GET['gr_msg'] ) ? sanitize_key( $_GET['gr_msg'] ) : '';
	?>
	<div class="wrap grenvios-i18n">
		<h1>Traducciones</h1>

		<?php if ( $msg === 'saved' ) : ?><div class="notice notice-success is-dismissible"><p>Ajustes guardados.</p></div><?php endif; ?>
		<?php if ( $msg === 'reset' ) : ?><div class="notice notice-success is-dismissible"><p>Diccionario borrado: la próxima traducción se hará de cero.</p></div><?php endif; ?>

		<?php /* ── Diagnóstico ── */ ?>
		<h2>Estado</h2>
		<?php foreach ( grenvios_i18n_healthcheck() as $i ) :
			$cls = $i[0] === 'error' ? 'notice-error' : ( $i[0] === 'warn' ? 'notice-warning' : 'notice-info' ); ?>
			<div class="notice <?php echo esc_attr( $cls ); ?> inline" style="margin:6px 0"><p><?php echo wp_kses_post( $i[1] ); ?></p></div>
		<?php endforeach; ?>

		<?php if ( grenvios_i18n_errors() ) : ?>
			<details style="margin:10px 0"><summary>Últimos errores del traductor</summary>
			<ul style="margin-left:18px;list-style:disc">
			<?php foreach ( grenvios_i18n_errors() as $e ) : ?>
				<li><code><?php echo esc_html( $e['when'] ); ?></code> — <?php echo esc_html( $e['msg'] ); ?></li>
			<?php endforeach; ?>
			</ul></details>
		<?php endif; ?>

		<?php if ( count( $others ) ) : ?>
		<?php /* ── Tabla de páginas × idiomas ── */ ?>
		<h2>Páginas</h2>
		<p>
			Cada traducción es una página propia con <strong>su URL traducida</strong>
			(<code><?php echo esc_html( home_url( '/servicios/envio-internacional-de-paquetes/' ) ); ?></code> →
			<code><?php echo esc_html( home_url( '/' . key( $others ) . '/services/international-parcel-shipping/' ) ); ?></code>),
			para que Google la indexe como página distinta.
		</p>

		<p>
			<button class="button button-primary" id="gr-run-all">Traducir todo lo que falta</button>
			<button class="button" id="gr-run-all-force">Retraducir todo</button>
			<span id="gr-progress" style="margin-left:12px;font-weight:600"></span>
		</p>
		<div id="gr-log" style="max-height:220px;overflow:auto;background:#fff;border:1px solid #dcdcde;padding:8px;display:none;margin-bottom:16px"></div>

		<table class="widefat striped">
			<thead><tr>
				<th style="width:32%">Página (<?php echo esc_html( grenvios_i18n_lang_name( $def ) ); ?>)</th>
				<?php foreach ( $others as $slug => $l ) : ?>
					<th><?php echo esc_html( $l['name'] ); ?></th>
				<?php endforeach; ?>
			</tr></thead>
			<tbody>
			<?php foreach ( $pages as $pid => $title ) : ?>
				<tr>
					<td>
						<strong><?php echo esc_html( $title ); ?></strong><br>
						<code><?php echo esc_html( get_post_field( 'post_name', $pid ) ); ?></code>
					</td>
					<?php foreach ( $others as $slug => $l ) :
						$st  = grenvios_i18n_page_status( $pid, $slug );
						$lbl = array( 'missing' => '— falta', 'stale' => 'desactualizada', 'ok' => 'al día' ); ?>
						<td data-lang="<?php echo esc_attr( $slug ); ?>" data-page="<?php echo (int) $pid; ?>">
							<span class="gr-state gr-<?php echo esc_attr( $st['state'] ); ?>"><?php echo esc_html( $lbl[ $st['state'] ] ); ?></span><br>
							<?php if ( ! empty( $st['id'] ) ) : ?>
								<a href="<?php echo esc_url( $st['url'] ); ?>" target="_blank" rel="noopener"><code><?php echo esc_html( get_post_field( 'post_name', $st['id'] ) ); ?></code></a>
								<a href="<?php echo esc_url( get_edit_post_link( $st['id'] ) ); ?>" class="button-link">editar</a><br>
							<?php endif; ?>
							<button class="button button-small gr-run" data-type="page" data-id="<?php echo (int) $pid; ?>" data-lang="<?php echo esc_attr( $slug ); ?>" data-force="1">
								<?php echo $st['state'] === 'missing' ? 'Traducir' : 'Retraducir'; ?>
							</button>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>

		<?php $guias = grenvios_i18n_master_posts(); ?>
		<?php if ( $guias ) : ?>
		<h2>Guías</h2>
		<p>Las entradas del blog también son páginas indexables: se traducen igual, con su propio slug.</p>
		<table class="widefat striped">
			<thead><tr>
				<th style="width:32%">Guía (<?php echo esc_html( grenvios_i18n_lang_name( $def ) ); ?>)</th>
				<?php foreach ( $others as $slug => $l ) : ?><th><?php echo esc_html( $l['name'] ); ?></th><?php endforeach; ?>
			</tr></thead>
			<tbody>
			<?php foreach ( $guias as $gid => $gtitle ) : ?>
				<tr>
					<td><strong><?php echo esc_html( $gtitle ); ?></strong><br><code><?php echo esc_html( get_post_field( 'post_name', $gid ) ); ?></code></td>
					<?php foreach ( $others as $slug => $l ) :
						$st  = grenvios_i18n_page_status( $gid, $slug );
						$lbl = array( 'missing' => '— falta', 'stale' => 'desactualizada', 'ok' => 'al día' ); ?>
						<td data-lang="<?php echo esc_attr( $slug ); ?>" data-post="<?php echo (int) $gid; ?>">
							<span class="gr-state gr-<?php echo esc_attr( $st['state'] ); ?>"><?php echo esc_html( $lbl[ $st['state'] ] ); ?></span><br>
							<?php if ( ! empty( $st['id'] ) ) : ?>
								<a href="<?php echo esc_url( $st['url'] ); ?>" target="_blank" rel="noopener"><code><?php echo esc_html( get_post_field( 'post_name', $st['id'] ) ); ?></code></a><br>
							<?php endif; ?>
							<button class="button button-small gr-run" data-type="post" data-id="<?php echo (int) $gid; ?>" data-lang="<?php echo esc_attr( $slug ); ?>" data-force="1">
								<?php echo $st['state'] === 'missing' ? 'Traducir' : 'Retraducir'; ?>
							</button>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php endif; ?>

		<h2>Contenido compartido</h2>
		<table class="widefat striped" style="max-width:900px">
			<thead><tr><th>Elemento</th><?php foreach ( $others as $slug => $l ) : ?><th><?php echo esc_html( $l['name'] ); ?></th><?php endforeach; ?></tr></thead>
			<tbody>
				<tr>
					<td><strong>Textos fijos del diseño</strong><br><span class="description">Menú, botones, formularios, pie de página, 404.</span></td>
					<?php foreach ( $others as $slug => $l ) : ?>
						<td><button class="button button-small gr-run" data-type="strings" data-lang="<?php echo esc_attr( $slug ); ?>">Traducir</button></td>
					<?php endforeach; ?>
				</tr>
				<tr>
					<td><strong>Fichas de países (Destinos)</strong><br><span class="description">Nombre, plazos, restricciones. Al agregar un país nuevo, vuelve a pulsar aquí.</span></td>
					<?php foreach ( $others as $slug => $l ) : ?>
						<td><button class="button button-small gr-run" data-type="destinos" data-lang="<?php echo esc_attr( $slug ); ?>">Traducir</button></td>
					<?php endforeach; ?>
				</tr>
				<tr>
					<td><strong>Prueba del traductor</strong><br><span class="description">Comprueba que la clave de API funciona.</span></td>
					<?php foreach ( $others as $slug => $l ) : ?>
						<td><button class="button button-small gr-run" data-type="test" data-lang="<?php echo esc_attr( $slug ); ?>">Probar</button></td>
					<?php endforeach; ?>
				</tr>
				<tr>
					<td><strong>Borrar diccionario</strong><br><span class="description">Obliga a traducir todo de nuevo (vuelve a consumir API).</span></td>
					<?php foreach ( $others as $slug => $l ) : ?>
						<td><a class="button button-small" onclick="return confirm('¿Borrar el diccionario de este idioma?')"
							href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=grenvios_i18n_reset&lang=' . $slug ), 'grenvios_i18n_reset' ) ); ?>">Borrar</a></td>
					<?php endforeach; ?>
				</tr>
			</tbody>
		</table>
		<?php else : ?>
			<div class="notice notice-info inline"><p>Agrega al menos un segundo idioma en <strong>Idiomas → Idiomas</strong> (Polylang) y vuelve aquí: aparecerá una columna por idioma, sin tocar código.</p></div>
		<?php endif; ?>

		<?php /* ── Ajustes ── */ ?>
		<h2>Ajustes del traductor</h2>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="grenvios_i18n_settings">
			<?php wp_nonce_field( 'grenvios_i18n_settings' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="gr-provider">Proveedor</label></th>
					<td>
						<select name="provider" id="gr-provider">
							<option value="claude" <?php selected( $s['provider'], 'claude' ); ?>>Claude (Anthropic) — adapta el copy y respeta los tokens</option>
							<option value="deepl"  <?php selected( $s['provider'], 'deepl' ); ?>>DeepL</option>
							<option value="google" <?php selected( $s['provider'], 'google' ); ?>>Google Cloud Translation</option>
							<option value="manual" <?php selected( $s['provider'], 'manual' ); ?>>Sin API (solo crear las páginas, texto a mano)</option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="gr-ck">Clave de Anthropic</label></th>
					<td>
						<input type="password" id="gr-ck" name="claude_key" class="regular-text" autocomplete="off"
							placeholder="<?php echo $s['claude_key'] ? '•••••• (guardada, deja vacío para conservarla)' : 'sk-ant-...'; ?>">
						<p class="description">Modelo: <input type="text" name="claude_model" value="<?php echo esc_attr( $s['claude_model'] ); ?>" class="regular-text" style="max-width:220px"></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="gr-dk">Clave de DeepL</label></th>
					<td><input type="password" id="gr-dk" name="deepl_key" class="regular-text" autocomplete="off"
						placeholder="<?php echo $s['deepl_key'] ? '•••••• (guardada)' : '...:fx para el plan free'; ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="gr-gk">Clave de Google</label></th>
					<td><input type="password" id="gr-gk" name="google_key" class="regular-text" autocomplete="off"
						placeholder="<?php echo $s['google_key'] ? '•••••• (guardada)' : 'API key de Cloud Translation'; ?>"></td>
				</tr>
				<tr>
					<th scope="row"><label for="gr-brand">Palabras que no se traducen</label></th>
					<td><textarea name="brand" id="gr-brand" rows="2" class="large-text"><?php echo esc_textarea( $s['brand'] ); ?></textarea>
						<p class="description">Una por línea: marca, nombres propios, nombres de servicio que quieras conservar en español.</p></td>
				</tr>
				<tr>
					<th scope="row"><label for="gr-tone">Tono</label></th>
					<td><textarea name="tone" id="gr-tone" rows="2" class="large-text"><?php echo esc_textarea( $s['tone'] ); ?></textarea></td>
				</tr>
			</table>
			<?php submit_button( 'Guardar ajustes' ); ?>
		</form>

		<h2>Cómo funciona</h2>
		<ol style="max-width:900px;list-style:decimal;margin-left:20px">
			<li>Cada idioma que agregues en Polylang aparece solo en esta pantalla, en el selector del sitio, en los <code>hreflang</code> y en el sitemap. <strong>No hay que tocar código para agregar un idioma.</strong></li>
			<li>Al traducir, se crea una página real de WordPress por idioma, con <strong>slug traducido</strong> y su propio <code>title</code> y <code>meta description</code>. Google la indexa como página independiente.</li>
			<li>La traducción se guarda en la base de datos: el visitante y Googlebot reciben HTML ya traducido, sin llamadas a ninguna API.</li>
			<li>Si editas el texto en español, la traducción se marca <em>desactualizada</em> y basta con pulsar «Retraducir» en esa fila.</li>
			<li>Un mismo texto se traduce (y se paga) una sola vez aunque aparezca en varias páginas.</li>
		</ol>
	</div>

	<style>
		.gr-state{display:inline-block;padding:1px 7px;border-radius:9px;font-size:11px;font-weight:600}
		.gr-ok{background:#d1e7dd;color:#0a3622}.gr-stale{background:#fff3cd;color:#664d03}.gr-missing{background:#f1f1f1;color:#666}
		#gr-log p{margin:2px 0;font-size:12px}
	</style>
	<script>
	(function(){
		var nonce = <?php echo wp_json_encode( wp_create_nonce( 'grenvios_i18n_run' ) ); ?>;
		var ajax  = <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>;
		var log = document.getElementById('gr-log'), prog = document.getElementById('gr-progress');

		function say(t, ok){ if(!log) return; log.style.display='block';
			var p=document.createElement('p'); p.textContent=(ok?'✓ ':'✗ ')+t; if(!ok)p.style.color='#b32d2e';
			log.appendChild(p); log.scrollTop=log.scrollHeight; }

		function run(job){
			var b=new FormData(); b.append('action','grenvios_i18n_run'); b.append('nonce',nonce);
			b.append('type',job.type); b.append('lang',job.lang);
			if(job.id) b.append('id',job.id); if(job.force) b.append('force','1');
			return fetch(ajax,{method:'POST',body:b,credentials:'same-origin'})
				.then(function(r){return r.json();})
				.then(function(j){ say((j.data&&j.data.msg)||'sin respuesta', !!j.success); return j; })
				.catch(function(e){ say('Error de red: '+e, false); });
		}

		// Botón individual
		document.querySelectorAll('.gr-run').forEach(function(btn){
			btn.addEventListener('click', function(){
				var t=btn.textContent; btn.disabled=true; btn.textContent='Traduciendo…';
				run({type:btn.dataset.type, id:btn.dataset.id, lang:btn.dataset.lang, force:btn.dataset.force})
					.then(function(){ btn.disabled=false; btn.textContent=t;
						if(btn.dataset.type==='page'||btn.dataset.type==='post') location.reload(); });
			});
		});

		// Cola completa
		function queue(force){
			var jobs=[];
			<?php foreach ( $others as $slug => $l ) : ?>
				jobs.push({type:'strings', lang:'<?php echo esc_js( $slug ); ?>'});
				jobs.push({type:'destinos', lang:'<?php echo esc_js( $slug ); ?>'});
			<?php endforeach; ?>
			document.querySelectorAll('td[data-page]').forEach(function(td){
				var st=td.querySelector('.gr-state');
				var pending = st && !st.classList.contains('gr-ok');
				if(force || pending) jobs.push({type:'page', id:td.dataset.page, lang:td.dataset.lang, force:force?1:0});
			});
			document.querySelectorAll('td[data-post]').forEach(function(td){
				var st=td.querySelector('.gr-state');
				var pending = st && !st.classList.contains('gr-ok');
				if(force || pending) jobs.push({type:'post', id:td.dataset.post, lang:td.dataset.lang, force:force?1:0});
			});
			if(!jobs.length){ prog.textContent='Todo está al día.'; return; }
			var i=0;
			(function next(){
				if(i>=jobs.length){ prog.textContent='Listo ('+jobs.length+' trabajos). Recargando…';
					setTimeout(function(){location.reload();},1200); return; }
				prog.textContent='Traduciendo '+(i+1)+' de '+jobs.length+'…';
				run(jobs[i++]).then(next);
			})();
		}
		var a=document.getElementById('gr-run-all'), f=document.getElementById('gr-run-all-force');
		if(a) a.addEventListener('click', function(){ queue(false); });
		if(f) f.addEventListener('click', function(){ if(confirm('Se retraducirá TODO (vuelve a consumir API). ¿Continuar?')) queue(true); });
	})();
	</script>
	<?php
}
