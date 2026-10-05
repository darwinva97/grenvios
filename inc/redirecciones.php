<?php
/**
 * Grenvíos — Redirecciones 301.
 *
 * POR QUÉ EXISTE ESTE ARCHIVO
 * Reestructurar un sitio significa mover URLs, y una URL que cambia sin
 * redirección pierde TODO lo que había ganado: las posiciones, los enlaces que
 * apuntaban a ella y las visitas que llegaban por ahí. Google tarda meses en
 * recuperarlo, si lo recupera.
 *
 * Este módulo hace tres cosas:
 *
 *   1. CAPTURA AUTOMÁTICA. Si se cambia el slug de una página publicada, se crea
 *      sola la redirección de la URL vieja a la nueva. Es el caso que más se
 *      olvida y el que más daño hace.
 *   2. GESTIÓN MANUAL. Pantalla para añadir, editar y borrar redirecciones
 *      (por ejemplo al fusionar dos páginas que se canibalizaban).
 *   3. REGISTRO DE 404. Anota las URLs que no existen y cuántas veces se
 *      piden, para redirigir las que de verdad reciben tráfico.
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ══════════════════════════════════════
   ALMACÉN
══════════════════════════════════════ */
function grenvios_redirects() {
	$r = get_option( 'grenvios_redirects', array() );
	return is_array( $r ) ? $r : array();
}

function grenvios_redirects_save( $map ) {
	update_option( 'grenvios_redirects', $map, false );
}

/* Normaliza una ruta: siempre /asi/ (sin dominio, con barras a los lados). */
function grenvios_redirect_norm( $path ) {
	$path = trim( (string) $path );
	if ( $path === '' ) return '';
	if ( preg_match( '#^https?://#i', $path ) ) {
		$home = untrailingslashit( home_url() );
		if ( strpos( $path, $home ) === 0 ) $path = substr( $path, strlen( $home ) );
		else return esc_url_raw( $path );   // destino externo: se deja tal cual
	}
	$path = strtok( $path, '?' );           // sin query
	$path = '/' . trim( (string) $path, '/' );
	return ( $path === '/' ) ? '/' : $path . '/';
}

/* ══════════════════════════════════════
   1) CAPTURA AUTOMÁTICA AL CAMBIAR UN SLUG
   `post_updated` da el post ANTES y DESPUÉS: es el único momento en el que se
   conoce la URL vieja.
══════════════════════════════════════ */
add_action( 'post_updated', function ( $post_id, $post_after, $post_before ) {
	if ( $post_after->post_type !== 'page' && $post_after->post_type !== 'post' ) return;
	if ( wp_is_post_revision( $post_id ) ) return;
	if ( $post_before->post_name === $post_after->post_name ) return;
	if ( $post_before->post_status !== 'publish' ) return;   // nunca estuvo indexada
	if ( $post_before->post_name === '' ) return;            // borrador sin slug

	// URL vieja: se reconstruye con el slug anterior sobre la misma jerarquía.
	$nueva = grenvios_redirect_norm( get_permalink( $post_id ) );
	$vieja = grenvios_redirect_norm( str_replace(
		'/' . $post_after->post_name . '/',
		'/' . $post_before->post_name . '/',
		$nueva
	) );
	if ( $vieja === '' || $vieja === $nueva ) return;

	$map = grenvios_redirects();
	// Si algo redirigía a la URL vieja, ahora debe apuntar a la nueva: se evita
	// encadenar redirecciones (301 → 301), que diluyen la señal.
	foreach ( $map as $de => $data ) {
		if ( $data['a'] === $vieja ) $map[ $de ]['a'] = $nueva;
	}
	$map[ $vieja ] = array( 'a' => $nueva, 'tipo' => 'auto', 'fecha' => current_time( 'mysql' ), 'hits' => 0 );
	grenvios_redirects_save( $map );
}, 10, 3 );

/* ══════════════════════════════════════
   2) APLICACIÓN
══════════════════════════════════════ */
add_action( 'template_redirect', function () {
	if ( is_admin() || ! is_404() ) return;

	$req = grenvios_redirect_norm( parse_url( add_query_arg( null, null ), PHP_URL_PATH ) );
	if ( $req === '' ) return;

	$map = grenvios_redirects();
	if ( ! isset( $map[ $req ] ) ) {
		grenvios_log_404( $req );
		return;
	}
	$destino = $map[ $req ]['a'];
	$url     = preg_match( '#^https?://#i', $destino ) ? $destino : home_url( $destino );

	// Contador de uso: sirve para saber qué redirecciones siguen vivas.
	$map[ $req ]['hits'] = ( isset( $map[ $req ]['hits'] ) ? (int) $map[ $req ]['hits'] : 0 ) + 1;
	grenvios_redirects_save( $map );

	wp_redirect( $url, 301 );
	exit;
}, 4 );   // antes de la redirección de idioma (prioridad 5)

/* ══════════════════════════════════════
   3) REGISTRO DE 404
══════════════════════════════════════ */
function grenvios_log_404( $path ) {
	$log = get_option( 'grenvios_404_log', array() );
	if ( ! is_array( $log ) ) $log = array();

	if ( isset( $log[ $path ] ) ) {
		$log[ $path ]['hits']++;
		$log[ $path ]['ultima'] = current_time( 'mysql' );
	} else {
		if ( count( $log ) > 300 ) {   // no dejar crecer la opción sin límite
			uasort( $log, function ( $a, $b ) { return $b['hits'] - $a['hits']; } );
			$log = array_slice( $log, 0, 200, true );
		}
		$log[ $path ] = array( 'hits' => 1, 'ultima' => current_time( 'mysql' ) );
	}
	update_option( 'grenvios_404_log', $log, false );
}

/* ══════════════════════════════════════
   4) PANTALLA
══════════════════════════════════════ */
add_action( 'admin_menu', function () {
	add_submenu_page(
		'grenvios-seo', 'Redirecciones', 'Redirecciones', 'manage_options',
		'grenvios-redirects', 'grenvios_redirects_page'
	);
}, 30 );

add_action( 'admin_post_grenvios_redirect_add', function () {
	if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Sin permiso.' );
	check_admin_referer( 'grenvios_redirects' );

	$de = grenvios_redirect_norm( wp_unslash( isset( $_POST['de'] ) ? $_POST['de'] : '' ) );
	$a  = grenvios_redirect_norm( wp_unslash( isset( $_POST['a'] ) ? $_POST['a'] : '' ) );

	if ( $de === '' || $a === '' || $de === $a ) {
		wp_safe_redirect( admin_url( 'admin.php?page=grenvios-redirects&gr_msg=invalida' ) );
		exit;
	}
	$map = grenvios_redirects();
	// Evita cadenas: si el destino ya redirige a otro sitio, se apunta al final.
	$visto = array();
	while ( isset( $map[ $a ] ) && ! isset( $visto[ $a ] ) ) { $visto[ $a ] = true; $a = $map[ $a ]['a']; }

	$map[ $de ] = array( 'a' => $a, 'tipo' => 'manual', 'fecha' => current_time( 'mysql' ), 'hits' => 0 );
	grenvios_redirects_save( $map );

	// Si venía del registro de 404, se limpia de ahí.
	$log = get_option( 'grenvios_404_log', array() );
	if ( is_array( $log ) && isset( $log[ $de ] ) ) { unset( $log[ $de ] ); update_option( 'grenvios_404_log', $log, false ); }

	wp_safe_redirect( admin_url( 'admin.php?page=grenvios-redirects&gr_msg=ok' ) );
	exit;
} );

add_action( 'admin_post_grenvios_redirect_del', function () {
	if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Sin permiso.' );
	check_admin_referer( 'grenvios_redirects_del' );
	$de  = grenvios_redirect_norm( wp_unslash( isset( $_GET['de'] ) ? $_GET['de'] : '' ) );
	$map = grenvios_redirects();
	unset( $map[ $de ] );
	grenvios_redirects_save( $map );
	wp_safe_redirect( admin_url( 'admin.php?page=grenvios-redirects&gr_msg=borrada' ) );
	exit;
} );

function grenvios_redirects_page() {
	if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Sin permiso.' );
	$map = grenvios_redirects();
	$log = get_option( 'grenvios_404_log', array() );
	if ( ! is_array( $log ) ) $log = array();
	uasort( $log, function ( $a, $b ) { return $b['hits'] - $a['hits']; } );
	$msg = isset( $_GET['gr_msg'] ) ? sanitize_key( $_GET['gr_msg'] ) : '';
	?>
	<div class="wrap">
		<h1>Redirecciones</h1>
		<?php if ( $msg === 'ok' ) : ?><div class="notice notice-success"><p>Redirección guardada.</p></div><?php endif; ?>
		<?php if ( $msg === 'borrada' ) : ?><div class="notice notice-success"><p>Redirección eliminada.</p></div><?php endif; ?>
		<?php if ( $msg === 'invalida' ) : ?><div class="notice notice-error"><p>Origen y destino deben ser distintos y no pueden estar vacíos.</p></div><?php endif; ?>

		<p style="max-width:900px">
			Cuando una URL cambia, la vieja debe redirigir a la nueva con un <strong>301</strong>:
			así Google traslada a la nueva las posiciones y los enlaces que tenía la anterior.
			Sin eso, la página empieza de cero.
			<strong>Al cambiar el slug de una página publicada, la redirección se crea sola.</strong>
		</p>

		<h2>Añadir una redirección</h2>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="grenvios_redirect_add">
			<?php wp_nonce_field( 'grenvios_redirects' ); ?>
			<table class="form-table" role="presentation"><tr>
				<th scope="row"><label for="gr-de">URL vieja</label></th>
				<td><input type="text" id="gr-de" name="de" class="regular-text" placeholder="/servicios/envio-viejo/" required>
					<p class="description">Solo la ruta, sin el dominio.</p></td>
			</tr><tr>
				<th scope="row"><label for="gr-a">URL nueva</label></th>
				<td><input type="text" id="gr-a" name="a" class="regular-text" placeholder="/servicios/envio-nuevo/" required></td>
			</tr></table>
			<?php submit_button( 'Guardar redirección' ); ?>
		</form>

		<h2>Activas <span class="description">(<?php echo count( $map ); ?>)</span></h2>
		<?php if ( ! $map ) : ?>
			<p>Todavía no hay ninguna.</p>
		<?php else : ?>
			<table class="widefat striped" style="max-width:1000px">
				<thead><tr><th style="width:34%">URL vieja</th><th style="width:34%">Redirige a</th><th style="width:80px">Usos</th><th style="width:90px">Origen</th><th style="width:70px"></th></tr></thead>
				<tbody>
				<?php foreach ( $map as $de => $d ) : ?>
					<tr>
						<td><code><?php echo esc_html( $de ); ?></code></td>
						<td><code><?php echo esc_html( $d['a'] ); ?></code></td>
						<td><?php echo (int) ( isset( $d['hits'] ) ? $d['hits'] : 0 ); ?></td>
						<td><?php echo $d['tipo'] === 'auto' ? 'automática' : 'manual'; ?></td>
						<td><a class="button button-small" onclick="return confirm('¿Eliminar esta redirección? La URL vieja volverá a dar 404.')"
							href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=grenvios_redirect_del&de=' . rawurlencode( $de ) ), 'grenvios_redirects_del' ) ); ?>">Borrar</a></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

		<h2>Páginas no encontradas (404)</h2>
		<p class="description">URLs que alguien pidió y no existen. Si una recibe visitas repetidas, merece una redirección.</p>
		<?php if ( ! $log ) : ?>
			<p>Ninguna registrada.</p>
		<?php else : ?>
			<table class="widefat striped" style="max-width:1000px">
				<thead><tr><th style="width:50%">URL pedida</th><th style="width:80px">Veces</th><th style="width:160px">Última vez</th><th></th></tr></thead>
				<tbody>
				<?php foreach ( array_slice( $log, 0, 40, true ) as $path => $d ) : ?>
					<tr>
						<td><code><?php echo esc_html( $path ); ?></code></td>
						<td><?php echo (int) $d['hits']; ?></td>
						<td><?php echo esc_html( $d['ultima'] ); ?></td>
						<td>
							<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="display:flex;gap:6px">
								<input type="hidden" name="action" value="grenvios_redirect_add">
								<?php wp_nonce_field( 'grenvios_redirects' ); ?>
								<input type="hidden" name="de" value="<?php echo esc_attr( $path ); ?>">
								<input type="text" name="a" placeholder="/destino/" class="small-text" style="width:180px" required>
								<button class="button button-small">Redirigir</button>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
	<?php
}
