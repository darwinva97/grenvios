<?php
/**
 * Entrada del blog v2 (inc/blog-detalle.php tiene las piezas y la explicación).
 *
 * Cabecera con foto y metadatos → respuesta rápida → índice → artículo →
 * autor → etiquetas y navegación → comentarios; a la derecha, cotizar, el
 * servicio que la guía apoya y más guías del país. Después, «Sigue leyendo»
 * y la llamada a la acción del tema.
 *
 * @package grenvios
 */
get_header();
while ( have_posts() ) : the_post();
	$gid     = get_the_ID();
	$pais    = function_exists( 'grenvios_bd_pais' ) ? grenvios_bd_pais() : '';
	$img     = function_exists( 'grenvios_bd_img' ) ? grenvios_bd_img( $gid ) : '';
	$cuerpo  = apply_filters( 'the_content', get_the_content() );
	$cuerpo  = str_replace( ']]>', ']]&gt;', $cuerpo );
	if ( function_exists( 'grenvios_bd_sin_faq_doble' ) ) $cuerpo = grenvios_bd_sin_faq_doble( $cuerpo );
	list( $cuerpo, $toc ) = function_exists( 'grenvios_bd_indice' ) ? grenvios_bd_indice( $cuerpo ) : array( $cuerpo, array() );
	$min     = function_exists( 'grenvios_bd_minutos' ) ? grenvios_bd_minutos( $cuerpo ) : 0;
	$pub     = get_post_time( 'U', true, $gid );
	$mod     = get_post_modified_time( 'U', true, $gid );
	$fecha   = function_exists( 'grenvios_bd_fecha' ) ? grenvios_bd_fecha( $pub ) : get_the_date();
	$cats    = get_the_category( $gid );
	$cat     = $cats ? $cats[0] : null;
	$resumen = trim( wp_strip_all_tags( get_the_excerpt() ) );
	/* La respuesta rápida no repite la entradilla de la cabecera: es la
	 * respuesta a la primera pregunta frecuente de la guía. */
	$rapida  = function_exists( 'grenvios_bd_rapida' ) ? grenvios_bd_rapida( $cuerpo ) : '';
	$pilar   = function_exists( 'grenvios_bd_pilar' ) ? grenvios_bd_pilar( $gid ) : 0;
	$mas     = function_exists( 'grenvios_bd_mas' ) ? grenvios_bd_mas( $gid, 5 ) : array();
	?>
	<header class="gr-bd-hero">
		<div class="container">
			<div class="gr-bd-hero-grid">
				<div class="gr-bd-hero-tx">
					<?php if ( function_exists( 'logisko_breadcrumbs' ) && function_exists( 'grenvios_bd_migas' ) ) logisko_breadcrumbs( grenvios_bd_migas( $gid ) ); ?>
					<?php if ( $cat ) : ?><a class="gr-bd-cat" href="<?php echo esc_url( get_category_link( $cat ) ); ?>"><?php echo esc_html( $cat->name ); ?></a><?php endif; ?>
					<h1 class="gr-bd-title"><?php the_title(); ?></h1>
					<?php if ( $resumen !== '' ) : ?><p class="gr-bd-lead"><?php echo esc_html( $resumen ); ?></p><?php endif; ?>
					<ul class="gr-bd-meta">
						<li><i class="fa-solid fa-circle-user" aria-hidden="true"></i> Equipo Grenvíos</li>
						<li><i class="fa-regular fa-calendar" aria-hidden="true"></i> <time datetime="<?php echo esc_attr( gmdate( 'c', $pub ) ); ?>"><?php echo esc_html( $fecha ); ?></time></li>
						<?php if ( $min ) : ?><li><i class="fa-regular fa-clock" aria-hidden="true"></i> <?php echo (int) $min; ?> min de lectura</li><?php endif; ?>
						<?php if ( $pais !== '' ) : ?><li><i class="fa-solid fa-location-dot" aria-hidden="true"></i> Envíos a <?php echo esc_html( $pais ); ?></li><?php endif; ?>
					</ul>
				</div>
				<?php if ( $img ) : ?>
				<figure class="gr-bd-hero-img"><?php /* sin animación: es la imagen principal (LCP) */ ?>
					<img src="<?php echo esc_url( $img ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" width="720" height="540" fetchpriority="high" decoding="async">
				</figure>
				<?php endif; ?>
			</div>
		</div>
	</header>

	<section class="gr-bd padding">
		<div class="container">
			<div class="gr-bd-grid">
				<article <?php post_class( 'gr-bd-art post-details' ); ?>>
					<?php if ( $rapida !== '' ) : ?>
					<div class="gr-bd-rapida">
						<p class="gr-bd-rapida-t"><i class="fa-solid fa-bolt" aria-hidden="true"></i> Respuesta rápida</p>
						<?php echo $rapida; // phpcs:ignore -- ya escapado en grenvios_bd_rapida ?>
					</div>
					<?php endif; ?>

					<?php if ( count( $toc ) >= 3 ) : ?>
					<nav class="gr-bd-toc" aria-label="En este artículo">
						<p class="gr-bd-toc-t">En este artículo</p>
						<ol>
							<?php foreach ( $toc as $t ) : ?>
								<li><a href="#<?php echo esc_attr( $t[0] ); ?>"><?php echo esc_html( $t[1] ); ?></a></li>
							<?php endforeach; ?>
						</ol>
					</nav>
					<?php endif; ?>

					<div class="logisko-entry-content gr-bd-content"><?php echo $cuerpo; // phpcs:ignore -- the_content ya filtrado ?></div>

					<aside class="gr-bd-autor">
						<span class="gr-bd-autor-ic" aria-hidden="true"><i class="fa-solid fa-truck-fast"></i></span>
						<div>
							<p class="gr-bd-autor-n">Escrito por el equipo de Grenvíos</p>
							<p><?php echo esc_html( function_exists( 'grenvios_sede_tokens_apply' ) ? grenvios_sede_tokens_apply( 'Despachamos envíos internacionales desde {{origen_ciudad}} todas las semanas.' ) : 'Despachamos envíos internacionales todas las semanas.' ); ?> Cada guía la revisa el equipo que cotiza y prepara los envíos<?php echo $pais !== '' ? ' a ' . esc_html( $pais ) : ''; ?>, con lo que vemos en el mostrador.</p>
							<p class="gr-bd-autor-f">Actualizado el <?php echo esc_html( grenvios_bd_fecha( $mod ) ); ?></p>
						</div>
					</aside>

					<?php if ( has_tag() ) : ?>
					<ul class="gr-bd-tags">
						<?php foreach ( get_the_tags() as $tag ) : ?>
							<li><a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>"><i class="fa-solid fa-hashtag" aria-hidden="true"></i><?php echo esc_html( $tag->name ); ?></a></li>
						<?php endforeach; ?>
					</ul>
					<?php endif; ?>

					<nav class="gr-bd-nav" aria-label="Más guías">
						<?php
						$prev = get_previous_post(); $next = get_next_post();
						if ( $prev ) echo '<a class="gr-bd-nav-a" href="' . esc_url( get_permalink( $prev ) ) . '"><span>Guía anterior</span>' . esc_html( get_the_title( $prev ) ) . '</a>';
						if ( $next ) echo '<a class="gr-bd-nav-a is-next" href="' . esc_url( get_permalink( $next ) ) . '"><span>Guía siguiente</span>' . esc_html( get_the_title( $next ) ) . '</a>';
						?>
					</nav>

					<?php if ( comments_open() || get_comments_number() ) : ?>
					<div class="gr-bd-coment"><?php comments_template(); ?></div>
					<?php endif; ?>
				</article>

				<aside class="gr-bd-side">
					<div class="gr-bd-card gr-bd-cta">
						<p class="gr-bd-card-sub">Cotiza en minutos</p>
						<p class="gr-bd-card-t"><?php echo $pais !== '' ? '¿Envías a ' . esc_html( $pais ) . '?' : '¿Vas a enviar algo?'; ?></p>
						<p>Dinos qué envías, cuánto pesa y a qué ciudad va: te damos precio y plazo en la misma respuesta.</p>
						<a class="default-btn btn-light" href="/cotizar/">Cotizar mi envío</a>
					</div>
					<?php if ( $pilar ) : ?>
					<div class="gr-bd-card">
						<p class="gr-bd-card-sub">El servicio de esta guía</p>
						<a class="gr-bd-pilar" href="<?php echo esc_url( get_permalink( $pilar ) ); ?>"><i class="fa-solid fa-box-open" aria-hidden="true"></i><span><?php echo esc_html( get_the_title( $pilar ) ); ?></span><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
					</div>
					<?php endif; ?>
					<?php if ( $mas ) : ?>
					<div class="gr-bd-card">
						<p class="gr-bd-card-sub"><?php echo $pais !== '' ? 'Más guías de ' . esc_html( $pais ) : 'Más guías del tema'; ?></p>
						<ul class="gr-bd-mas">
							<?php foreach ( $mas as $mp ) : ?>
								<li><a href="<?php echo esc_url( get_permalink( $mp ) ); ?>"><?php echo esc_html( get_the_title( $mp ) ); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</div>
					<?php endif; ?>
				</aside>
			</div>
		</div>
	</section>
<?php endwhile;
/* Guías relacionadas (por etiqueta y por categoría) + llamada a la acción:
 * mantiene al lector dentro del cluster y lo lleva a cotizar. */
if ( function_exists( 'grenvios_render_guias_relacionadas' ) ) grenvios_render_guias_relacionadas();
if ( function_exists( 'grenvios_render_cta' ) ) grenvios_render_cta( 'guia' );

get_footer();
