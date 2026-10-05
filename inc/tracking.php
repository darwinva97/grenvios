<?php
/**
 * Grenvíos — Seguimiento de envíos (rastreo manual, CRUD simple).
 *
 * Admin: una sola pantalla "Rastreo de envíos" con tabla — agregar, editar el código,
 * cambiar el estado y eliminar, todo manual. Sin ACF ni plugins.
 * Almacén: CPT interno `grenvios_envio` (sin UI propia) con meta numero_guia / estado_actual
 * / fecha_actualizacion.
 * Público: el cliente ingresa su número de guía y ve el estado en una barra animada de 7 pasos
 * (REST grenvios/v1/track + assets/js/tracking.js).
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* Estados por defecto (orden = avance). label => clase de ícono. */
function grenvios_track_states_default() {
	return array(
		'Etiqueta creada'                  => 'fa-light fa-tag',
		'Salió del centro de recolección'  => 'fa-light fa-warehouse',
		'En tránsito'                      => 'fa-light fa-truck-fast',
		'Proceso de aduana'                => 'fa-light fa-building-columns',
		'Retenido'                         => 'fa-light fa-circle-pause',
		'Listo para la entrega'            => 'fa-light fa-box-check',
		'Entregado'                        => 'fa-light fa-circle-check',
	);
}

/* Estados vigentes: los editados por el admin (opción) o los de defecto. */
function grenvios_track_states() {
	$opt = get_option( 'grenvios_track_states' );
	return ( is_array( $opt ) && $opt ) ? $opt : grenvios_track_states_default();
}

/* Guarda el conjunto de estados (array ordenado label => icono). */
function grenvios_track_states_save( $states ) {
	update_option( 'grenvios_track_states', $states );
}

/* Crea un envío de prueba por cada estado vigente (no duplica los que ya existan).
   GRV-001 = "En tránsito" si ese estado existe; el resto en orden. */
function grenvios_track_seed_demo() {
	$states = grenvios_track_states();
	$labels = array_keys( $states );
	$seq    = array();
	if ( in_array( 'En tránsito', $labels, true ) ) $seq[] = 'En tránsito';
	foreach ( $labels as $l ) if ( $l !== 'En tránsito' ) $seq[] = $l;

	$demo = array();
	$n = 1;
	foreach ( $seq as $l ) { $demo[ sprintf( 'GRV-%03d', $n++ ) ] = $l; }

	foreach ( $demo as $g => $e ) {
		$exists = get_posts( array(
			'post_type' => 'grenvios_envio', 'post_status' => 'publish', 'numberposts' => 1, 'fields' => 'ids',
			'meta_query' => array( array( 'key' => 'numero_guia', 'value' => $g, 'compare' => '=' ) ),
		) );
		if ( $exists ) continue;
		$new = wp_insert_post( array( 'post_type' => 'grenvios_envio', 'post_status' => 'publish', 'post_title' => $g ) );
		if ( $new ) {
			update_post_meta( $new, 'numero_guia', $g );
			update_post_meta( $new, 'estado_actual', isset( $states[ $e ] ) ? $e : $keys[0] );
			update_post_meta( $new, 'fecha_actualizacion', time() );
		}
	}
}

/* Al activar el tema: sembrar los envíos de prueba UNA sola vez (si borras los demos,
   reactivar el tema no los vuelve a crear). */
add_action( 'after_switch_theme', function () {
	if ( get_option( 'grenvios_track_demo_seeded' ) ) return;
	grenvios_track_seed_demo();
	update_option( 'grenvios_track_demo_seeded', 1 );
}, 20 );

/* ── CPT solo-almacén (sin interfaz propia: se gestiona desde la página tabla) ── */
add_action( 'init', function () {
	register_post_type( 'grenvios_envio', array(
		'labels'              => array( 'name' => 'Envíos', 'singular_name' => 'Envío' ),
		'public'              => false,
		'show_ui'             => false,
		'show_in_menu'        => false,
		'supports'            => array( 'title' ),
		'capability_type'     => 'post',
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'has_archive'         => false,
		'rewrite'             => false,
	) );
} );

/* ── Menú admin: una sola página con la tabla CRUD ── */
add_action( 'admin_menu', function () {
	add_menu_page(
		'Rastreo de envíos', 'Rastreo de envíos', 'edit_posts',
		'grenvios-rastreo', 'grenvios_tracking_admin_page',
		'dashicons-location-alt', 26
	);
} );

/* Font Awesome en la página de rastreo del admin (para previsualizar los íconos de estado). */
add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( $hook !== 'toplevel_page_grenvios-rastreo' ) return;
	wp_enqueue_style( 'grenvios-fa-admin', get_template_directory_uri() . '/assets/lib/font-awesome-pro/css/fontawesome.min.css', array(), LOGISKO_VER );
} );

/* Procesa Agregar / Guardar / Eliminar (patrón PRG: procesa y redirige). */
add_action( 'admin_post_grenvios_track_save', function () {
	if ( ! current_user_can( 'edit_posts' ) ) wp_die( 'Sin permiso.' );
	check_admin_referer( 'grenvios_track_admin' );
	$states = grenvios_track_states();
	$action = isset( $_POST['gv_action'] ) ? sanitize_key( $_POST['gv_action'] ) : '';
	$guia   = isset( $_POST['numero_guia'] ) ? sanitize_text_field( wp_unslash( $_POST['numero_guia'] ) ) : '';
	$estado = isset( $_POST['estado_actual'] ) ? sanitize_text_field( wp_unslash( $_POST['estado_actual'] ) ) : '';
	$pid    = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0;
	$msg    = '';

	/* Campos extra del envío (columnas): servicio, tipo, cliente y detalle libre.
	   Servicio/tipo se normalizan contra listas cerradas; el resto texto plano. */
	$servicio = isset( $_POST['servicio'] ) ? sanitize_text_field( wp_unslash( $_POST['servicio'] ) ) : '';
	$tipo     = isset( $_POST['tipo_envio'] ) ? sanitize_text_field( wp_unslash( $_POST['tipo_envio'] ) ) : '';
	$cliente  = isset( $_POST['cliente_nombre'] ) ? sanitize_text_field( wp_unslash( $_POST['cliente_nombre'] ) ) : '';
	$detalle  = isset( $_POST['detalle'] ) ? sanitize_textarea_field( wp_unslash( $_POST['detalle'] ) ) : '';
	/* Servicio es de texto libre (Aéreo/Terrestre son solo sugerencias). Tipo sí es lista cerrada. */
	if ( ! in_array( $tipo, array( 'Paquete', 'Documento' ), true ) ) $tipo = '';

	if ( $action === 'add' && $guia !== '' && isset( $states[ $estado ] ) ) {
		$pid = wp_insert_post( array( 'post_type' => 'grenvios_envio', 'post_status' => 'publish', 'post_title' => $guia ) );
		if ( $pid ) {
			update_post_meta( $pid, 'numero_guia', $guia );
			update_post_meta( $pid, 'estado_actual', $estado );
			update_post_meta( $pid, 'servicio', $servicio );
			update_post_meta( $pid, 'tipo_envio', $tipo );
			update_post_meta( $pid, 'cliente_nombre', $cliente );
			update_post_meta( $pid, 'detalle', $detalle );
			update_post_meta( $pid, 'fecha_actualizacion', time() );
			$msg = 'agregado';
		}
	} elseif ( $action === 'update' && $pid && get_post_type( $pid ) === 'grenvios_envio' ) {
		if ( $guia !== '' ) {
			update_post_meta( $pid, 'numero_guia', $guia );
			wp_update_post( array( 'ID' => $pid, 'post_title' => $guia ) );
		}
		if ( isset( $states[ $estado ] ) ) update_post_meta( $pid, 'estado_actual', $estado );
		update_post_meta( $pid, 'servicio', $servicio );
		update_post_meta( $pid, 'tipo_envio', $tipo );
		update_post_meta( $pid, 'cliente_nombre', $cliente );
		update_post_meta( $pid, 'detalle', $detalle );
		update_post_meta( $pid, 'fecha_actualizacion', time() );
		$msg = 'guardado';
	} elseif ( $action === 'delete' && $pid && get_post_type( $pid ) === 'grenvios_envio' ) {
		wp_delete_post( $pid, true );
		$msg = 'eliminado';
	} elseif ( $action === 'demo' ) {
		grenvios_track_seed_demo();
		$msg = 'demo';

	/* ── Gestión de ESTADOS (agregar / editar / eliminar / reordenar) ── */
	} elseif ( $action === 'state_add' ) {
		$label = isset( $_POST['st_label'] ) ? sanitize_text_field( wp_unslash( $_POST['st_label'] ) ) : '';
		$icon  = isset( $_POST['st_icon'] )  ? sanitize_text_field( wp_unslash( $_POST['st_icon'] ) )  : '';
		if ( $label !== '' ) {
			$st = grenvios_track_states();
			if ( ! isset( $st[ $label ] ) ) {
				$st[ $label ] = $icon !== '' ? $icon : 'fa-solid fa-circle';
				grenvios_track_states_save( $st );
			}
			$msg = 'estado';
		}
	} elseif ( $action === 'state_update' ) {
		$old   = isset( $_POST['st_old'] )   ? sanitize_text_field( wp_unslash( $_POST['st_old'] ) )   : '';
		$label = isset( $_POST['st_label'] ) ? sanitize_text_field( wp_unslash( $_POST['st_label'] ) ) : '';
		$icon  = isset( $_POST['st_icon'] )  ? sanitize_text_field( wp_unslash( $_POST['st_icon'] ) )  : '';
		$st = grenvios_track_states();
		if ( $old !== '' && isset( $st[ $old ] ) && $label !== '' ) {
			$new = array();
			foreach ( $st as $k => $v ) {
				if ( $k === $old ) $new[ $label ] = ( $icon !== '' ? $icon : $v );
				else               $new[ $k ]     = $v;
			}
			grenvios_track_states_save( $new );
			if ( $label !== $old ) { // renombrado: migrar envíos con el estado antiguo
				$ships = get_posts( array( 'post_type' => 'grenvios_envio', 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids',
					'meta_query' => array( array( 'key' => 'estado_actual', 'value' => $old, 'compare' => '=' ) ) ) );
				foreach ( $ships as $sid ) update_post_meta( $sid, 'estado_actual', $label );
			}
			$msg = 'estado';
		}
	} elseif ( $action === 'state_delete' ) {
		$old = isset( $_POST['st_old'] ) ? sanitize_text_field( wp_unslash( $_POST['st_old'] ) ) : '';
		$st  = grenvios_track_states();
		if ( $old !== '' && isset( $st[ $old ] ) && count( $st ) > 1 ) {
			unset( $st[ $old ] );
			grenvios_track_states_save( $st );
			$msg = 'estado';
		}
	} elseif ( $action === 'state_up' || $action === 'state_down' ) {
		$old    = isset( $_POST['st_old'] ) ? sanitize_text_field( wp_unslash( $_POST['st_old'] ) ) : '';
		$st     = grenvios_track_states();
		$labels = array_keys( $st );
		$i      = array_search( $old, $labels, true );
		if ( $i !== false ) {
			$j = ( $action === 'state_up' ) ? $i - 1 : $i + 1;
			if ( $j >= 0 && $j < count( $labels ) ) {
				$tmp = $labels[ $i ]; $labels[ $i ] = $labels[ $j ]; $labels[ $j ] = $tmp;
				$new = array();
				foreach ( $labels as $k ) $new[ $k ] = $st[ $k ];
				grenvios_track_states_save( $new );
			}
			$msg = 'estado';
		}
	}

	wp_safe_redirect( add_query_arg( 'gv_msg', $msg, admin_url( 'admin.php?page=grenvios-rastreo' ) ) );
	exit;
} );

/* Render de la página tabla. */
function grenvios_tracking_admin_page() {
	if ( ! current_user_can( 'edit_posts' ) ) return;
	$states  = grenvios_track_states();
	$keys    = array_keys( $states );
	$action  = admin_url( 'admin-post.php' );
	$msgmap  = array( 'agregado' => 'Envío agregado.', 'guardado' => 'Cambios guardados.', 'eliminado' => 'Envío eliminado.', 'demo' => 'Envíos de prueba creados: GRV-001 (En tránsito) … Puedes editarlos o eliminarlos.', 'estado' => 'Estados actualizados.' );
	$msg     = isset( $_GET['gv_msg'] ) ? sanitize_key( $_GET['gv_msg'] ) : '';
	$per     = 20;
	$paged   = isset( $_GET['gv_paged'] ) ? max( 1, (int) $_GET['gv_paged'] ) : 1;
	$query   = new WP_Query( array(
		'post_type'      => 'grenvios_envio',
		'post_status'    => 'publish',
		'posts_per_page' => $per,
		'paged'          => $paged,
		'orderby'        => 'modified',
		'order'          => 'DESC',
	) );
	$envios  = $query->posts;
	$total   = (int) $query->found_posts;
	$pages   = (int) $query->max_num_pages;

	$select = function ( $current ) use ( $keys ) {
		$o = '';
		foreach ( $keys as $k ) $o .= '<option value="' . esc_attr( $k ) . '" ' . selected( $current, $k, false ) . '>' . esc_html( $k ) . '</option>';
		return $o;
	};
	/* Select de opciones cerradas (servicio, tipo) con opción vacía "—". */
	$opts = function ( $current, $list ) {
		$o = '<option value="">—</option>';
		foreach ( $list as $v ) $o .= '<option value="' . esc_attr( $v ) . '" ' . selected( $current, $v, false ) . '>' . esc_html( $v ) . '</option>';
		return $o;
	};
	?>
	<div class="wrap">
		<h1><span class="dashicons dashicons-location-alt" style="font-size:28px;height:28px;width:28px;vertical-align:-4px"></span> Rastreo de envíos</h1>
		<p>Gestiona los códigos de rastreo y su estado. El cliente consulta su número de guía en la página <em>Rastrea tu Envío</em>.</p>

		<?php if ( isset( $msgmap[ $msg ] ) ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php echo esc_html( $msgmap[ $msg ] ); ?></p></div>
		<?php endif; ?>

		<h2 style="margin-top:18px">Agregar envío</h2>
		<form method="post" action="<?php echo esc_url( $action ); ?>" style="background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:14px;display:flex;gap:10px;flex-wrap:wrap;align-items:flex-end;max-width:900px">
			<?php wp_nonce_field( 'grenvios_track_admin' ); ?>
			<input type="hidden" name="action" value="grenvios_track_save">
			<input type="hidden" name="gv_action" value="add">
			<label>Número de guía<br><input type="text" name="numero_guia" required class="regular-text" placeholder="Ej. GRV-000123"></label>
			<label>Estado<br><select name="estado_actual"><?php echo $select( $keys[0] ); ?></select></label>
			<label>Servicio<br>
				<input type="text" name="servicio" list="gv-servicios" class="regular-text" placeholder="Aéreo, Terrestre… o escribe otro">
			</label>
			<datalist id="gv-servicios">
				<option value="Aéreo"></option>
				<option value="Terrestre"></option>
			</datalist>
			<label>Tipo<br>
				<select name="tipo_envio">
					<option value="">—</option>
					<option value="Paquete">Paquete</option>
					<option value="Documento">Documento</option>
				</select>
			</label>
			<label>Nombre y Apellido<br><input type="text" name="cliente_nombre" class="regular-text" placeholder="Ej. Juan Pérez"></label>
			<label style="flex-basis:100%">Detalle<br><textarea name="detalle" rows="2" class="large-text" placeholder="Información extra del envío (contenido, referencia, observaciones…)"></textarea></label>
			<button class="button button-primary">Agregar</button>
		</form>

		<form method="post" action="<?php echo esc_url( $action ); ?>" style="margin-top:10px">
			<?php wp_nonce_field( 'grenvios_track_admin' ); ?>
			<input type="hidden" name="action" value="grenvios_track_save">
			<input type="hidden" name="gv_action" value="demo">
			<button class="button" onclick="return confirm('Se crearán 7 envíos de prueba (GRV-001 a GRV-007), uno por estado. ¿Continuar?');"><span class="dashicons dashicons-randomize" style="vertical-align:-4px"></span> Crear 7 envíos de prueba</button>
			<span style="color:#777"> — útil para probar; luego puedes eliminarlos.</span>
		</form>

		<h2 style="margin-top:26px">Envíos registrados (<?php echo (int) $total; ?>)</h2>
		<p><input type="search" id="gv-filter" placeholder="Filtrar esta página por número de guía…" class="regular-text"></p>
		<table class="wp-list-table widefat striped" id="gv-table">
			<thead><tr>
				<th style="width:12%">Número de guía</th>
				<th style="width:9%">Servicio</th>
				<th style="width:9%">Tipo</th>
				<th style="width:14%">Nombre y Apellido</th>
				<th style="width:15%">Estado</th>
				<th style="width:20%">Detalle</th>
				<th style="width:10%">Última actualización</th>
				<th style="width:11%">Acciones</th>
			</tr></thead>
			<tbody>
			<?php if ( ! $envios ) : ?>
				<tr><td colspan="8">Aún no hay envíos. Agrega el primero arriba.</td></tr>
			<?php else : foreach ( $envios as $e ) :
				$guia     = get_post_meta( $e->ID, 'numero_guia', true );
				$estado   = get_post_meta( $e->ID, 'estado_actual', true );
				$servicio = get_post_meta( $e->ID, 'servicio', true );
				$tipo     = get_post_meta( $e->ID, 'tipo_envio', true );
				$cliente  = get_post_meta( $e->ID, 'cliente_nombre', true );
				$detalle  = get_post_meta( $e->ID, 'detalle', true );
				$fecha    = (int) get_post_meta( $e->ID, 'fecha_actualizacion', true );
				$fid      = 'gvf-' . (int) $e->ID;
			?>
				<tr>
					<td data-guia="<?php echo esc_attr( strtolower( $guia ) ); ?>"><input type="text" name="numero_guia" form="<?php echo esc_attr( $fid ); ?>" value="<?php echo esc_attr( $guia ); ?>" style="width:100%"></td>
					<td><input type="text" name="servicio" form="<?php echo esc_attr( $fid ); ?>" list="gv-servicios" value="<?php echo esc_attr( $servicio ); ?>" style="width:100%"></td>
					<td><select name="tipo_envio" form="<?php echo esc_attr( $fid ); ?>" style="width:100%"><?php echo $opts( $tipo, array( 'Paquete', 'Documento' ) ); ?></select></td>
					<td><input type="text" name="cliente_nombre" form="<?php echo esc_attr( $fid ); ?>" value="<?php echo esc_attr( $cliente ); ?>" style="width:100%"></td>
					<td><select name="estado_actual" form="<?php echo esc_attr( $fid ); ?>" style="width:100%"><?php echo $select( $estado ); ?></select></td>
					<td><textarea name="detalle" form="<?php echo esc_attr( $fid ); ?>" rows="2" style="width:100%"><?php echo esc_textarea( $detalle ); ?></textarea></td>
					<td><?php echo $fecha ? esc_html( date_i18n( 'd/m/Y H:i', $fecha ) ) : '—'; ?></td>
					<td>
						<form id="<?php echo esc_attr( $fid ); ?>" method="post" action="<?php echo esc_url( $action ); ?>" style="display:inline">
							<?php wp_nonce_field( 'grenvios_track_admin' ); ?>
							<input type="hidden" name="action" value="grenvios_track_save">
							<input type="hidden" name="post_id" value="<?php echo (int) $e->ID; ?>">
							<button class="button button-primary" name="gv_action" value="update">Guardar</button>
							<button class="button button-link-delete" name="gv_action" value="delete" onclick="return confirm('¿Eliminar este envío? No se puede deshacer.');">Eliminar</button>
						</form>
					</td>
				</tr>
			<?php endforeach; endif; ?>
			</tbody>
		</table>
		<?php if ( $pages > 1 ) :
			$links = paginate_links( array(
				'base'      => add_query_arg( 'gv_paged', '%#%', admin_url( 'admin.php?page=grenvios-rastreo' ) ),
				'format'    => '',
				'current'   => $paged,
				'total'     => $pages,
				'prev_text' => '‹ Anterior',
				'next_text' => 'Siguiente ›',
			) );
			if ( $links ) echo '<div class="tablenav bottom"><div class="tablenav-pages">' .
				'<span class="displaying-num">' . (int) $total . ' envíos</span> ' . $links . '</div></div>';
		endif; ?>

		<hr style="margin:34px 0 18px">
		<h2>Estados del envío</h2>
		<p>Agrega, renombra, cambia el ícono, reordena o elimina los estados que verá el cliente.
		   El <strong>orden</strong> aquí es el avance en la barra de seguimiento. Íconos: clases de
		   <a href="https://fontawesome.com/search" target="_blank" rel="noopener">Font Awesome</a>
		   (ej. <code>fa-solid fa-box</code>, <code>fa-light fa-truck-fast</code>).</p>

		<div style="max-width:820px">
		<?php $allstates = grenvios_track_states(); $scount = count( $allstates ); $si = 0; foreach ( $allstates as $label => $icon ) : $si++; ?>
			<form method="post" action="<?php echo esc_url( $action ); ?>" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;background:#fff;border:1px solid #dcdcde;border-radius:8px;padding:8px 10px;margin-bottom:8px">
				<?php wp_nonce_field( 'grenvios_track_admin' ); ?>
				<input type="hidden" name="action" value="grenvios_track_save">
				<input type="hidden" name="st_old" value="<?php echo esc_attr( $label ); ?>">
				<span style="width:30px;text-align:center;font-size:16px;color:#5e2129"><i class="<?php echo esc_attr( $icon ); ?>"></i></span>
				<input type="text" name="st_label" value="<?php echo esc_attr( $label ); ?>" style="flex:2;min-width:170px" required>
				<input type="text" name="st_icon" value="<?php echo esc_attr( $icon ); ?>" placeholder="fa-solid fa-box" style="flex:2;min-width:170px">
				<button class="button" name="gv_action" value="state_up" title="Subir" <?php disabled( $si, 1 ); ?>>↑</button>
				<button class="button" name="gv_action" value="state_down" title="Bajar" <?php disabled( $si, $scount ); ?>>↓</button>
				<button class="button button-primary" name="gv_action" value="state_update">Guardar</button>
				<button class="button button-link-delete" name="gv_action" value="state_delete" onclick="return confirm('¿Eliminar el estado &quot;<?php echo esc_js( $label ); ?>&quot;? Los envíos que lo tengan quedarán sin estado válido.');" <?php disabled( $scount, 1 ); ?>>Eliminar</button>
			</form>
		<?php endforeach; ?>

			<form method="post" action="<?php echo esc_url( $action ); ?>" style="display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap;background:#f6f7f7;border:1px dashed #c3c4c7;border-radius:8px;padding:12px">
				<?php wp_nonce_field( 'grenvios_track_admin' ); ?>
				<input type="hidden" name="action" value="grenvios_track_save">
				<input type="hidden" name="gv_action" value="state_add">
				<label>Nuevo estado<br><input type="text" name="st_label" required class="regular-text" placeholder="Ej. En reparto"></label>
				<label>Ícono<br><input type="text" name="st_icon" class="regular-text" placeholder="fa-solid fa-person-biking"></label>
				<button class="button button-primary">Agregar estado</button>
			</form>
		</div>
	</div>
	<script>
	(function(){
		// Filtro simple por número de guía
		var fil=document.getElementById('gv-filter');
		if(fil){ fil.addEventListener('input',function(){
			var q=this.value.toLowerCase();
			document.querySelectorAll('#gv-table tbody tr').forEach(function(tr){
				var cell=tr.querySelector('[data-guia]');
				if(!cell){tr.style.display='';return;}
				tr.style.display = cell.getAttribute('data-guia').indexOf(q)>-1 ? '' : 'none';
			});
		}); }
	})();
	</script>
	<?php
}

/* ── Consulta pública por REST ── */
add_action( 'rest_api_init', function () {
	register_rest_route( 'grenvios/v1', '/track', array(
		'methods'             => 'GET',
		'permission_callback' => '__return_true',
		'callback'            => 'grenvios_track_query',
		'args'                => array( 'guia' => array( 'required' => true ) ),
	) );
} );

function grenvios_track_query( WP_REST_Request $req ) {
	$guia   = trim( (string) $req->get_param( 'guia' ) );
	$states = grenvios_track_states();
	$labels = array_keys( $states );
	if ( $guia === '' ) return new WP_REST_Response( array( 'found' => false ), 200 );

	$ids = get_posts( array(
		'post_type'   => 'grenvios_envio',
		'post_status' => 'publish',
		'numberposts' => 1,
		'fields'      => 'ids',
		'meta_query'  => array( array( 'key' => 'numero_guia', 'value' => $guia, 'compare' => '=' ) ),
	) );
	if ( empty( $ids ) ) return new WP_REST_Response( array( 'found' => false ), 200 );

	$id     = $ids[0];
	$estado = get_post_meta( $id, 'estado_actual', true );
	$idx    = array_search( $estado, $labels, true );
	if ( $idx === false ) $idx = 0;
	$fecha  = (int) get_post_meta( $id, 'fecha_actualizacion', true );

	return new WP_REST_Response( array(
		'found'    => true,
		'guia'     => $guia,
		'estado'   => $estado,
		'indice'   => $idx,
		'total'    => count( $labels ),
		'fecha'    => $fecha ? date_i18n( 'd/m/Y H:i', $fecha ) : '',
		'servicio' => get_post_meta( $id, 'servicio', true ),
		'tipo'     => get_post_meta( $id, 'tipo_envio', true ),
		'cliente'  => get_post_meta( $id, 'cliente_nombre', true ),
		'detalle'  => get_post_meta( $id, 'detalle', true ),
	), 200 );
}

/* ── Front: JS + datos ── */
add_action( 'wp_enqueue_scripts', function () {
	$uri = get_template_directory_uri();
	wp_enqueue_script( 'grenvios-tracking', $uri . '/assets/js/tracking.js', array(), LOGISKO_VER, true );
	wp_localize_script( 'grenvios-tracking', 'grenviosTrack', array(
		'rest'    => esc_url_raw( rest_url( 'grenvios/v1/track' ) ),
		'rastreo' => esc_url_raw( home_url( '/rastreo-de-envios/' ) ),
		'wa'      => 'https://wa.me/' . ( function_exists( 'grenvios_biz' ) ? grenvios_biz()['wa_number'] : '51900612836' ),
		'states'  => grenvios_track_states(),
	) );
}, 110 );
