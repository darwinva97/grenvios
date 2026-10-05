<?php
/**
 * Grenvíos — Pantalla «Sedes» del administrador.
 *
 * Lista los países que hay en el encabezado (los DESTINOS) y permite convertir
 * cualquiera en SEDE con su propia ruta. Crear la sede es crear su idioma en
 * Polylang: de ahí salen solos el prefijo (/cl/), el hreflang, el selector y la
 * reescritura de enlaces internos. Lo único que añade el tema es que cada sede
 * tenga sus datos de contacto (ver inc/sedes.php).
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ── Menú ── */
add_action( 'admin_menu', function () {
	add_menu_page(
		'Sedes', 'Sedes', 'manage_options',
		'grenvios-sedes', 'grenvios_sedes_admin_page',
		'dashicons-admin-multisite', 28
	);
} );

/* ══════════════════════════════════════
   ACCIONES (patrón PRG, como la pantalla de Destinos)
══════════════════════════════════════ */
add_action( 'admin_post_grenvios_sede_save', function () {
	if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Sin permiso.' );
	check_admin_referer( 'grenvios_sede_admin' );

	$action = isset( $_POST['gv_action'] ) ? sanitize_key( $_POST['gv_action'] ) : '';
	$back   = admin_url( 'admin.php?page=grenvios-sedes' );
	$plan   = grenvios_sede_plan();

	/* Liberar el prefijo de la sede principal (es -> pe). */
	if ( $action === 'rename_master' ) {
		$to  = isset( $_POST['nuevo_slug'] ) ? sanitize_key( wp_unslash( $_POST['nuevo_slug'] ) ) : '';
		$res = grenvios_sede_rename_master( $to );
		$msg = is_wp_error( $res ) ? 'err:' . $res->get_error_message() : 'ok:Prefijo de la sede principal cambiado a «' . $to . '».';
		wp_safe_redirect( add_query_arg( 'gv_msg', rawurlencode( $msg ), $back ) );
		exit;
	}

	/* Duplicar todas las páginas del sitio a la ruta de un país. */
	if ( $action === 'duplicar' ) {
		$lang = isset( $_POST['sede'] ) ? sanitize_key( wp_unslash( $_POST['sede'] ) ) : '';
		if ( $lang === '' || ! function_exists( 'grenvios_ruta_duplicar' ) ) {
			wp_safe_redirect( add_query_arg( 'gv_msg', rawurlencode( 'err:Ruta no indicada.' ), $back ) );
			exit;
		}
		$r   = grenvios_ruta_duplicar( $lang );
		$msg = 'ok:' . grenvios_sede_name( $lang ) . ': ' . $r['creadas'] . ' páginas creadas'
			. ( $r['saltadas'] ? ', ' . $r['saltadas'] . ' ya existían' : '' ) . '.';
		if ( $r['errores'] ) $msg .= ' err:' . implode( ' · ', array_slice( $r['errores'], 0, 3 ) );
		wp_safe_redirect( add_query_arg( 'gv_msg', rawurlencode( $msg ), $back ) );
		exit;
	}

	/* Guardar los destinos activos de una sede. */
	if ( $action === 'destinos' ) {
		$sede = isset( $_POST['sede'] ) ? sanitize_key( wp_unslash( $_POST['sede'] ) ) : '';
		$sel  = isset( $_POST['dest'] ) ? (array) wp_unslash( $_POST['dest'] ) : array();
		if ( $sede === '' ) {
			wp_safe_redirect( add_query_arg( 'gv_msg', rawurlencode( 'err:Sede no indicada.' ), $back ) );
			exit;
		}
		grenvios_sede_destinos_save( $sel, $sede );
		$n   = count( grenvios_sede_destinos_sel( $sede ) );
		$msg = $n
			? 'ok:Destinos de ' . grenvios_sede_name( $sede ) . ' guardados (' . $n . ').'
			: 'ok:' . grenvios_sede_name( $sede ) . ' vuelve a heredar todos los destinos menos el suyo.';
		wp_safe_redirect( add_query_arg( 'gv_msg', rawurlencode( $msg ), $back ) );
		exit;
	}

	/* Crear una sede, o todas las pendientes. */
	if ( $action === 'create' || $action === 'create_all' ) {
		$pedidos = array();
		if ( $action === 'create' ) {
			$one = isset( $_POST['destino'] ) ? sanitize_key( wp_unslash( $_POST['destino'] ) ) : '';
			if ( isset( $plan[ $one ] ) ) $pedidos[ $one ] = $plan[ $one ];
		} else {
			foreach ( $plan as $k => $row ) {
				if ( $row['estado'] === 'pendiente' ) $pedidos[ $k ] = $row;
			}
		}

		$hechas = array();
		$fallos = array();
		foreach ( $pedidos as $row ) {
			if ( $row['estado'] !== 'pendiente' ) continue;
			$res = grenvios_sede_create( $row['slug'], $row['locale'], $row['titulo'], $row['slug'] );
			if ( is_wp_error( $res ) ) {
				$fallos[] = $row['titulo'] . ': ' . $res->get_error_message();
			} else {
				$hechas[] = $row['titulo'] . ' (/' . $row['slug'] . '/)';
			}
		}

		$msg = '';
		if ( $hechas ) $msg .= 'ok:Sedes creadas: ' . implode( ', ', $hechas ) . '.';
		if ( $fallos ) $msg .= ( $msg ? ' ' : '' ) . 'err:No se pudieron crear: ' . implode( ' · ', $fallos );
		if ( ! $msg )  $msg  = 'err:No había nada pendiente que crear.';

		wp_safe_redirect( add_query_arg( 'gv_msg', rawurlencode( $msg ), $back ) );
		exit;
	}

	wp_safe_redirect( $back );
	exit;
} );

/* ══════════════════════════════════════
   PANTALLA
══════════════════════════════════════ */
function grenvios_sedes_admin_page() {
	$plan   = grenvios_sede_plan();
	$master = grenvios_sede_master();

	// ¿Algún país necesita un prefijo que ya ocupa la sede principal?
	$choque = '';
	foreach ( $plan as $row ) {
		if ( $row['estado'] === 'ocupado' && $row['slug'] === $master ) $choque = $row['titulo'];
	}

	$pendientes = 0;
	foreach ( $plan as $row ) if ( $row['estado'] === 'pendiente' ) $pendientes++;

	$estados = array(
		'maestra'     => array( 'Sede principal', '#2271b1' ),
		'sede'        => array( 'Activa',         '#00733f' ),
		'pendiente'   => array( 'Sin crear',      '#8a6d1a' ),
		'ocupado'     => array( 'Prefijo en uso', '#b32d2e' ),
		'desconocido' => array( 'Sin datos',      '#787c82' ),
	);
	?>
	<div class="wrap">
		<h1>Sedes</h1>
		<p style="max-width:44em">
			Una <strong>sede</strong> es un país <em>desde</em> el que Grenvíos opera, con su ruta, su
			teléfono y su dirección. Es distinto de un <strong>destino</strong>, que es un país <em>al</em>
			que se envía. Aquí aparecen los países del encabezado; convierte en sede solo aquellos en
			los que haya operación real.
		</p>

		<?php
		if ( isset( $_GET['gv_msg'] ) ) {
			foreach ( preg_split( '~\s(?=(?:ok|err):)~', wp_unslash( $_GET['gv_msg'] ) ) as $chunk ) {
				$chunk = trim( (string) $chunk );
				if ( $chunk === '' ) continue;
				$is_err = strpos( $chunk, 'err:' ) === 0;
				$text   = preg_replace( '~^(ok|err):~', '', $chunk );
				printf(
					'<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
					$is_err ? 'error' : 'success',
					esc_html( $text )
				);
			}
		}

		if ( ! function_exists( 'PLL' ) ) {
			echo '<div class="notice notice-error"><p><strong>Polylang no está activo.</strong> Las sedes son idiomas de Polylang: sin el plugin no se pueden crear rutas por país.</p></div>';
			return;
		}
		?>

		<?php if ( $choque ) : ?>
			<div class="notice notice-warning">
				<p>
					<strong>El prefijo «<?php echo esc_html( $master ); ?>» le hace falta a <?php echo esc_html( $choque ); ?>.</strong>
					La sede principal nació con el código genérico del idioma, no con el del país. Cámbialo a
					<code>pe</code> y el prefijo queda libre.
				</p>
				<p>
					Las URLs de Perú <strong>no cambian</strong>: Polylang oculta el prefijo del idioma
					principal, así que la portada sigue siendo <code><?php echo esc_html( home_url( '/' ) ); ?></code>.
					Hazlo <strong>antes</strong> de traducir contenido.
				</p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-bottom:8px">
					<?php wp_nonce_field( 'grenvios_sede_admin' ); ?>
					<input type="hidden" name="action" value="grenvios_sede_save">
					<input type="hidden" name="gv_action" value="rename_master">
					<input type="hidden" name="nuevo_slug" value="pe">
					<?php submit_button( 'Cambiar el prefijo principal a «pe»', 'secondary', 'submit', false ); ?>
				</form>
			</div>
		<?php endif; ?>

		<table class="widefat striped" style="max-width:1000px;margin-top:16px">
			<thead>
				<tr>
					<th>País</th>
					<th>Ruta</th>
					<th>Locale</th>
					<th>Ciudad de origen</th>
					<th>Estado</th>
					<th>Índice de Google</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ( $plan as $row ) :
				$e     = $row['estado'];
				$badge = isset( $estados[ $e ] ) ? $estados[ $e ] : array( $e, '#787c82' );
				$viva  = ( $e === 'sede' || $e === 'maestra' );
				$falta = $viva ? grenvios_sede_pendientes( $row['slug'] ) : array();
				// La ciudad real sale de los datos de la sede; si aún no la tiene,
				// se muestra la propuesta del registro en gris.
				$ciudad_real = $viva ? grenvios_sede_ciudad( $row['slug'] ) : '';
				?>
				<tr>
					<td><strong><?php echo esc_html( $row['titulo'] ); ?></strong></td>
					<td><code><?php echo esc_html( $row['ruta'] ? $row['ruta'] : '—' ); ?></code></td>
					<td><code><?php echo esc_html( $row['locale'] ? $row['locale'] : '—' ); ?></code></td>
					<td>
						<?php if ( $ciudad_real !== '' ) : ?>
							<?php echo esc_html( $ciudad_real ); ?>
						<?php elseif ( $row['ciudad'] ) : ?>
							<span style="color:#787c82" title="Propuesta; aún sin configurar"><?php echo esc_html( $row['ciudad'] ); ?></span>
						<?php else : ?>
							—
						<?php endif; ?>
					</td>
					<td>
						<span style="display:inline-block;padding:2px 9px;border-radius:10px;font-size:11px;font-weight:600;color:#fff;background:<?php echo esc_attr( $badge[1] ); ?>">
							<?php echo esc_html( $badge[0] ); ?>
						</span>
					</td>
					<td>
						<?php if ( ! $viva ) : ?>
							<span style="color:#787c82">—</span>
						<?php elseif ( ! $falta ) : ?>
							<span style="color:#00733f;font-weight:600">✓ Indexable</span>
						<?php else : ?>
							<span style="color:#8a6d1a;font-weight:600">noindex</span><br>
							<span style="font-size:11px;color:#646970">falta <?php echo esc_html( implode( ', ', $falta ) ); ?></span>
						<?php endif; ?>
					</td>
					<td style="text-align:right">
						<?php if ( $e === 'pendiente' ) : ?>
							<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
								<?php wp_nonce_field( 'grenvios_sede_admin' ); ?>
								<input type="hidden" name="action" value="grenvios_sede_save">
								<input type="hidden" name="gv_action" value="create">
								<input type="hidden" name="destino" value="<?php echo esc_attr( $row['destino'] ); ?>">
								<button class="button button-small">Crear sede</button>
							</form>
						<?php elseif ( $e === 'sede' || $e === 'maestra' ) : ?>
							<a class="button button-small" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>">Editar datos</a>
						<?php elseif ( $e === 'ocupado' ) : ?>
							<span style="color:#b32d2e">Libera «<?php echo esc_html( $row['slug'] ); ?>» primero</span>
						<?php else : ?>
							<span style="color:#787c82">Añádelo a <code>grenvios_sede_paises()</code></span>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>

		<?php if ( $pendientes > 1 ) : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:16px">
				<?php wp_nonce_field( 'grenvios_sede_admin' ); ?>
				<input type="hidden" name="action" value="grenvios_sede_save">
				<input type="hidden" name="gv_action" value="create_all">
				<?php submit_button( 'Crear las ' . $pendientes . ' sedes pendientes', 'primary', 'submit', false ); ?>
			</form>
		<?php endif; ?>

		<?php
		/* ── Cobertura de páginas por ruta de país ── */
		$rutas = array();
		foreach ( $plan as $row ) {
			if ( $row['estado'] === 'sede' && function_exists( 'grenvios_es_ruta_pais' ) && grenvios_es_ruta_pais( $row['slug'] ) ) {
				$rutas[ $row['slug'] ] = $row['titulo'];
			}
		}
		if ( $rutas ) : ?>
			<h2 style="margin-top:34px">Páginas de cada ruta</h2>
			<p style="max-width:46em">
				Cada ruta necesita su propia copia de las páginas del sitio para poder reescribirlas
				pensando en ese país. Duplicarlas una a una desde Polylang son decenas de pasos;
				este botón las crea todas de golpe, ya enlazadas.
				<strong>Se crean como copia del original</strong> y salen con <code>noindex</code>
				hasta que se les escribe contenido propio: publicar copias idénticas cambiando solo
				el prefijo de la URL hace perder posiciones a todo el dominio.
			</p>
			<table class="widefat striped" style="max-width:1000px">
				<thead><tr><th>Ruta</th><th>Páginas creadas</th><th></th></tr></thead>
				<tbody>
				<?php foreach ( $rutas as $slug => $titulo ) :
					$cob = grenvios_ruta_cobertura( $slug );
					$pct = $cob['total'] ? round( $cob['hechas'] / $cob['total'] * 100 ) : 0;
					?>
					<tr>
						<td><strong><?php echo esc_html( $titulo ); ?></strong> <span style="color:#646970">/<?php echo esc_html( $slug ); ?>/</span></td>
						<td>
							<?php echo (int) $cob['hechas']; ?> de <?php echo (int) $cob['total']; ?>
							<span style="display:inline-block;width:120px;height:6px;background:#e0e0e0;border-radius:3px;vertical-align:middle;margin-left:8px">
								<span style="display:block;width:<?php echo (int) $pct; ?>%;height:6px;background:<?php echo $pct >= 100 ? '#00733f' : '#2271b1'; ?>;border-radius:3px"></span>
							</span>
						</td>
						<td style="text-align:right">
							<?php if ( $cob['hechas'] < $cob['total'] ) : ?>
								<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
									<?php wp_nonce_field( 'grenvios_sede_admin' ); ?>
									<input type="hidden" name="action" value="grenvios_sede_save">
									<input type="hidden" name="gv_action" value="duplicar">
									<input type="hidden" name="sede" value="<?php echo esc_attr( $slug ); ?>">
									<button class="button button-primary">Crear las <?php echo (int) ( $cob['total'] - $cob['hechas'] ); ?> que faltan</button>
								</form>
							<?php else : ?>
								<span style="color:#00733f">✓ Completa</span>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

		<?php
		/* ── Destinos por sede ── */
		$vivas = array();
		foreach ( $plan as $row ) {
			if ( $row['estado'] === 'sede' ) $vivas[ $row['slug'] ] = $row['titulo'];
		}
		if ( $vivas ) :
			$todos = function_exists( 'grenvios_destinos_fixed' ) ? grenvios_destinos_fixed() : array();
			if ( function_exists( 'grenvios_destinos_custom' ) ) $todos = array_merge( grenvios_destinos_custom(), $todos );
			?>
			<h2 style="margin-top:34px">Destinos de cada sede</h2>
			<p style="max-width:44em">
				Desde Madrid no se envía a los mismos países que desde Lima. Marca los destinos de cada
				sede; <strong>si no marcas ninguno hereda todos menos el suyo propio</strong>, que se
				excluye siempre (una sede no puede enviarse a sí misma).
			</p>
			<?php foreach ( $vivas as $sede_slug => $sede_titulo ) :
				$sel    = grenvios_sede_destinos_sel( $sede_slug );
				$propio = grenvios_sede_destino_propio( $sede_slug );
				?>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
					  style="background:#fff;border:1px solid #c3c4c7;padding:14px 18px;margin-bottom:12px;max-width:1000px">
					<?php wp_nonce_field( 'grenvios_sede_admin' ); ?>
					<input type="hidden" name="action" value="grenvios_sede_save">
					<input type="hidden" name="gv_action" value="destinos">
					<input type="hidden" name="sede" value="<?php echo esc_attr( $sede_slug ); ?>">
					<strong><?php echo esc_html( $sede_titulo ); ?></strong>
					<span style="color:#646970">· /<?php echo esc_html( $sede_slug ); ?>/</span>
					<div style="display:flex;flex-wrap:wrap;gap:6px 22px;margin:10px 0">
						<?php foreach ( $todos as $dslug => $d ) :
							if ( $dslug === $propio ) continue;   // no se envía a sí misma
							?>
							<label style="min-width:150px">
								<input type="checkbox" name="dest[]" value="<?php echo esc_attr( $dslug ); ?>"
									<?php checked( in_array( $dslug, $sel, true ) ); ?>>
								<?php echo esc_html( $d['title'] ); ?>
							</label>
						<?php endforeach; ?>
					</div>
					<?php if ( $propio ) : ?>
						<p style="margin:0 0 10px;color:#646970;font-size:12px">
							<?php echo esc_html( isset( $todos[ $propio ]['title'] ) ? $todos[ $propio ]['title'] : $propio ); ?>
							no aparece: es el país de esta sede.
						</p>
					<?php endif; ?>
					<?php submit_button( 'Guardar destinos', 'secondary', 'submit', false ); ?>
					<?php if ( ! $sel ) : ?>
						<span style="margin-left:10px;color:#646970;font-size:12px">Ahora mismo: hereda todos.</span>
					<?php endif; ?>
				</form>
			<?php endforeach; ?>
		<?php endif; ?>

		<div class="notice notice-info inline" style="max-width:1000px;margin-top:22px">
			<p><strong>Qué pasa al crear una sede.</strong> Se da de alta su idioma en Polylang y su ruta
			empieza a responder. Las páginas de esa sede <em>todavía no existen</em>: hay que traducirlas
			(o adaptarlas, si comparten idioma con Perú) desde la pantalla de Traducciones.</p>
			<p>Mientras una sede no tenga teléfono, dirección y destinos propios, su contenido es un clon
			del de Perú. Publicar clones en buscadores hace perder posiciones a todo el dominio, así que
			<strong>conviene rellenar sus datos antes de darla a conocer</strong>.</p>
		</div>
	</div>
	<?php
}
