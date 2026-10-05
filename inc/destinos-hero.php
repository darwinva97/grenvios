<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Hero de las páginas de destino (pantalla completa) + «Nuestras soluciones»
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Maqueta de la clienta: foto a sangre con el camión a la derecha y velo granate
 * a la izquierda; antesala, H1 en dos líneas (la segunda en acento), entradilla,
 * tres garantías con icono y dos botones. Ocupa el alto de la pantalla menos la
 * cabecera. Debajo, cuatro tarjetas de soluciones.
 *
 * Lo usan la portada de cada ruta (/ar/envios-a-argentina/), las fichas de
 * /destinos/<país>/ y sus espejos: todas pasan por grenvios_render_destino().
 *
 * Nada de lo que se afirma depende de suposiciones: la tercera garantía y la
 * primera tarjeta cambian según la forma de entrega y las vías reales del
 * destino (a EE. UU. no se ofrece «Envíos terrestres»). Todo es editable desde
 * el panel «Editar página».
 */

if ( ! defined( 'ABSPATH' ) ) exit;

function grenvios_dhero_vias( $d ) {
	$m = isset( $d['modos'] ) ? (string) $d['modos'] : '';
	return array(
		'aereo' => ( stripos( $m, 'aére' ) !== false || stripos( $m, 'aere' ) !== false ),
		'terr'  => ( stripos( $m, 'terrestre' ) !== false ),
		'casa'  => ( stripos( (string) $d['entrega'], 'domicilio' ) !== false || stripos( (string) $d['entrega'], 'puerta' ) !== false ),
	);
}

/* Valores por defecto: los comparten el render y el panel del editor. */
function grenvios_dhero_defaults( $d ) {
	$p = $d['title'];
	$v = grenvios_dhero_vias( $d );
	return array(
		'dst_intro_sub'   => 'Envíos a ' . $p,
		'dst_intro_title' => 'Envíos a ' . $p . ' desde {{origen_ciudad}}, <span class="hl">seguros y a tiempo</span>',
		'dst_lead'        => $d['lead'],
		'dst_hf1_t'       => 'Seguro',
		'dst_hf1_d'       => 'Tu carga en buenas manos.',
		'dst_hf2_t'       => 'A tiempo',
		'dst_hf2_d'       => 'Entrega en ' . $d['tiempo'] . '.',
		'dst_hf3_t'       => $v['casa'] ? 'A todo ' . $p : 'Retiro en agencia',
		'dst_hf3_d'       => $v['casa'] ? 'Hasta la puerta de tu destinatario.' : 'Tu destinatario lo recoge en ' . $p . '.',
		'dst_hero_btn1'   => 'Cotizar envío',
		'dst_hero_btn2'   => 'Rastrea tu pedido',

		'dst_sol_eyebrow' => 'Envíos a ' . $p,
		'dst_sol_title'   => 'Nuestras soluciones de envío',
		'dst_sol_text'    => 'Servicios confiables y flexibles para que tus paquetes y cargas lleguen a ' . $p . ' sin complicaciones.',
		'dst_sol1_t'      => $v['terr'] ? 'Envíos terrestres' : 'Envío de documentos',
		'dst_sol1_d'      => $v['terr'] ? 'La opción más utilizada y con mejor relación costo-beneficio.' : 'Títulos, trámites y papeles legales, con seguimiento.',
		'dst_sol2_t'      => 'Envíos aéreos',
		'dst_sol2_d'      => $v['terr'] ? 'Ideal para documentos y envíos urgentes.' : 'La vía más rápida para llegar a ' . $p . '.',
		'dst_sol3_t'      => 'Paquetería y carga',
		'dst_sol3_d'      => 'Desde pequeños paquetes hasta grandes cargas.',
		'dst_sol4_t'      => 'Seguimiento en tiempo real',
		'dst_sol4_d'      => 'Rastrea tu pedido en todo momento.',
		// Etiquetas de las tarjetas y enlace (diseño 2026-09-28).
		'dst_sol1_b'      => $v['terr'] ? 'Más elegido' : 'Con seguimiento',
		'dst_sol2_b'      => 'Más rápido',
		'dst_sol3_b'      => 'Flexible',
		'dst_sol4_b'      => '24/7',
		'dst_sol_ver'     => 'Ver detalles',
	);
}

function grenvios_dhero_f( $key, $defs ) {
	return grenvios_field( $key, isset( $defs[ $key ] ) ? $defs[ $key ] : '' );
}

function grenvios_dhero_render( $slug, $d ) {
	$home = grenvios_url_base();
	$defs = grenvios_dhero_defaults( $d );
	$img  = grenvios_field( 'dst_hero_img', '' );
	if ( $img === '' ) $img = get_template_directory_uri() . '/assets/img/destino-hero.jpg';

	$feats = array(
		array( 'fa-solid fa-shield-halved', 'dst_hf1' ),
		array( 'fa-regular fa-clock',       'dst_hf2' ),
		array( 'fa-solid fa-paper-plane',   'dst_hf3' ),
	);

	/* Migas: se conserva su schema BreadcrumbList; la maqueta no las muestra. */
	ob_start();
	logisko_breadcrumbs( array(
		apply_filters( 'grenvios_miga_inicio', grenvios_t( 'Inicio' ) ) => $home . '/',
		grenvios_t( 'Destinos' ) => $home . '/destinos/',
		$d['title'] => '',
	) );
	$migas = preg_replace( '~<nav class="breadcrumbs".*?</nav>~s', '', ob_get_clean() );
	?>
	<section class="gr-dhero">
		<div class="gr-dhero-media" aria-hidden="true">
			<img src="<?php echo esc_url( $img ); ?>" alt="" fetchpriority="high" decoding="async">
		</div>
		<div class="container">
			<div class="gr-dhero-content">
				<p class="gr-dhero-eyebrow"><?php echo esc_html( grenvios_dhero_f( 'dst_intro_sub', $defs ) ); ?></p>
				<h1 class="gr-dhero-title"><?php echo wp_kses_post( grenvios_dhero_f( 'dst_intro_title', $defs ) ); ?></h1>
				<p class="gr-dhero-lead"><?php echo esc_html( grenvios_dhero_f( 'dst_lead', $defs ) ); ?></p>

				<ul class="gr-dhero-feats">
					<?php foreach ( $feats as $f ) :
						$t = trim( (string) grenvios_dhero_f( $f[1] . '_t', $defs ) );
						if ( $t === '' ) continue; ?>
					<li>
						<span class="gr-dhero-ic"><i class="<?php echo esc_attr( $f[0] ); ?>"></i></span>
						<span class="gr-dhero-ft"><strong><?php echo esc_html( $t ); ?></strong><?php echo esc_html( grenvios_dhero_f( $f[1] . '_d', $defs ) ); ?></span>
					</li>
					<?php endforeach; ?>
				</ul>

				<div class="gr-dhero-btns">
					<a href="<?php echo esc_url( $home . '/cotizar/' ); ?>" class="gr-dhero-btn"><i class="fa-solid fa-box"></i> <?php echo esc_html( grenvios_dhero_f( 'dst_hero_btn1', $defs ) ); ?> <i class="fa-solid fa-arrow-right gr-dhero-arrow"></i></a>
					<a href="<?php echo esc_url( $home . '/rastreo-de-envios/' ); ?>" class="gr-dhero-btn gr-dhero-btn--line"><i class="fa-solid fa-location-dot"></i> <?php echo esc_html( grenvios_dhero_f( 'dst_hero_btn2', $defs ) ); ?></a>
				</div>
			</div>
		</div>
		<?php echo $migas; // solo el JSON-LD ?>
	</section>
	<script>
	/* Alto de pantalla menos la cabecera (y la barra de administración, si está). */
	(function(){
		function fit(){
			var h=document.querySelector('.main-header'), a=document.getElementById('wpadminbar');
			var n=(h?h.offsetHeight:0)+(a?a.offsetHeight:0);
			document.documentElement.style.setProperty('--gr-dhero-top', n+'px');
		}
		fit(); window.addEventListener('resize', fit); window.addEventListener('load', fit);
	})();
	</script>
	<?php
}

function grenvios_dhero_soluciones( $slug, $d ) {
	$home  = grenvios_url_base();
	$defs  = grenvios_dhero_defaults( $d );
	$v     = grenvios_dhero_vias( $d );
	$cards = array(
		array( $v['terr'] ? 'fa-solid fa-truck' : 'fa-solid fa-file-lines', 'dst_sol1', $v['terr'] ? '/servicios/envio-internacional-de-paquetes/' : '/servicios/envio-internacional-de-documentos/' ),
		array( 'fa-solid fa-plane', 'dst_sol2', '/servicios/envio-internacional-de-documentos/' ),
		array( 'fa-solid fa-box-open', 'dst_sol3', '/servicios/carga-internacional/' ),
		array( 'fa-solid fa-location-dot', 'dst_sol4', '/rastreo-de-envios/' ),
	);
	if ( ! $v['terr'] ) $cards[1][2] = '/servicios/envio-internacional-de-paquetes/';
	/* Foto (editable; por defecto la del repartidor del tema) con el plazo
	 * real del destino: «8 a 10 días hábiles» → «8–10» + «días hábiles». */
	$img = trim( (string) grenvios_field( 'dst_sol_img', '' ) );
	if ( $img === '' ) $img = get_template_directory_uri() . '/assets/img/cta-repartidor-4.webp';
	$plazo = trim( (string) $d['tiempo'] );
	$num   = preg_match( '/^(\d+)\s*(?:a|-|–)\s*(\d+)/u', $plazo, $pm ) ? $pm[1] . '–' . $pm[2] : ( preg_match( '/^(\d+)/', $plazo, $pm ) ? $pm[1] : '' );
	$resto = trim( preg_replace( '/^\d+\s*(?:(?:a|-|–)\s*\d+)?/u', '', $plazo ) );
	$ver   = grenvios_dhero_f( 'dst_sol_ver', $defs );
	?>
	<section class="gr-dsol gr-dsol--v2">
		<div class="container">
			<div class="gr-dsol-head">
				<p class="gr-dsol-eyebrow"><?php echo esc_html( grenvios_dhero_f( 'dst_sol_eyebrow', $defs ) ); ?></p>
				<h2><?php echo esc_html( grenvios_dhero_f( 'dst_sol_title', $defs ) ); ?></h2>
				<p><?php echo esc_html( grenvios_dhero_f( 'dst_sol_text', $defs ) ); ?></p>
			</div>
			<div class="gr-dsol-cuerpo">
				<figure class="gr-dsol-foto wow fade-in-bottom" data-wow-delay="100ms">
					<img src="<?php echo esc_url( $img ); ?>" alt="" loading="lazy" decoding="async">
					<?php if ( $num !== '' ) : ?>
					<figcaption class="gr-dsol-plazo">
						<i class="<?php echo $v['terr'] ? 'fa-solid fa-truck-fast' : 'fa-solid fa-plane-departure'; ?>" aria-hidden="true"></i>
						<span><strong><?php echo esc_html( $num ); ?></strong><?php echo esc_html( $resto !== '' ? $resto : 'días hábiles' ); ?></span>
					</figcaption>
					<?php endif; ?>
				</figure>
				<div class="gr-dsol-grid">
					<?php $i = 0; foreach ( $cards as $c ) :
						$t = trim( (string) grenvios_dhero_f( $c[1] . '_t', $defs ) );
						if ( $t === '' ) continue;
						$b = trim( (string) grenvios_dhero_f( $c[1] . '_b', $defs ) ); ?>
					<a class="gr-dsol-card wow fade-in-bottom<?php echo $i === 0 ? ' is-destacada' : ''; ?>" data-wow-delay="<?php echo 150 + $i * 100; ?>ms" href="<?php echo esc_url( $home . $c[2] ); ?>">
						<span class="gr-dsol-top">
							<span class="gr-dsol-ic"><i class="<?php echo esc_attr( $c[0] ); ?>" aria-hidden="true"></i></span>
							<?php if ( $b !== '' ) : ?><span class="gr-dsol-tag"><?php echo esc_html( $b ); ?></span><?php endif; ?>
						</span>
						<h3><?php echo esc_html( $t ); ?></h3>
						<p><?php echo esc_html( grenvios_dhero_f( $c[1] . '_d', $defs ) ); ?></p>
						<?php if ( trim( (string) $ver ) !== '' ) : ?><span class="gr-dsol-ver"><?php echo esc_html( $ver ); ?> <i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span><?php endif; ?>
					</a>
					<?php $i++; endforeach; ?>
				</div>
			</div>
		</div>
	</section>
	<?php
}
