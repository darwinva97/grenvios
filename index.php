<?php
/**
 * Blog / listado de entradas + resultados de búsqueda + archivos.
 * Hero editable (Personalizar → Grenvíos → Blog · Encabezado).
 * Las tarjetas se generan automáticamente desde las entradas (loop),
 * con filtro de categorías y paginación.
 *
 * @package grenvios
 */
get_header();

/* ── Encabezado (hero) ── */
if ( is_search() ) {
	$eyebrow = grenvios_t( 'Búsqueda' );
	/* translators: %s: términos buscados */
	$title   = sprintf( grenvios_t( 'Resultados para “%s”' ), '<span>' . esc_html( get_search_query() ) . '</span>' );
} elseif ( is_category() || is_tag() || is_archive() ) {
	$eyebrow = get_theme_mod( 'grenvios_blog_eyebrow', 'Blog' );
	$title   = get_the_archive_title();
} else {
	// Página del blog (listado de entradas): hero editable desde el botón "Editar página"
	// (post-meta) con respaldo en el Personalizador y, por último, el texto por defecto.
	$eyebrow = grenvios_field( 'blog_eyebrow', get_theme_mod( 'grenvios_blog_eyebrow', 'Blog' ) );
	$title   = grenvios_field( 'blog_title', get_theme_mod( 'grenvios_blog_title', 'Guías de envíos <span>internacionales</span>' ) );
}
$blog_bg = ( is_category() || is_tag() || is_archive() || is_search() )
	? get_theme_mod( 'grenvios_blog_bg', '' )
	: grenvios_field( 'blog_img', get_theme_mod( 'grenvios_blog_bg', '' ) );
/* Listado del blog (principal y de cada ruta): cabecera propia del país
 * (inc/rutas-diseno.php). Archivos y búsqueda conservan el banner. */
if ( is_home() && ! is_paged() && function_exists( 'grenvios_rd_blog_hero' ) ) {
	echo grenvios_rd_blog_hero(); // phpcs:ignore -- escapado en la función
} else {
	logisko_page_banner( $eyebrow, $title, array(), $blog_bg );
}

/* Texto propio del listado: sin él, la página del blog eran 138 palabras
 * —el hero y los títulos de las tarjetas— y no posicionaba por nada
 * (inc/paginas-contenido-seo.php). Solo en el listado, no en archivos ni
 * en búsqueda, donde no aporta. */
if ( is_home() && function_exists( 'grenvios_ent_render' ) ) grenvios_ent_render( 'blog' );
if ( is_home() && function_exists( 'grenvios_pseo_render' ) ) grenvios_pseo_render( 'blog' );

/* Cluster: si la categoría refuerza una página, se anuncia y se enlaza. */
if ( function_exists( 'grenvios_render_category_pillar' ) ) grenvios_render_category_pillar();

/* Archivo de etiqueta: descripción propia, enlace al servicio que refuerza y
 * los demás temas del blog (inc/blog-etiquetas.php). Sin esto el archivo era
 * un listado de tarjetas sin una sola línea de texto. */
if ( function_exists( 'grenvios_etq_render_cabecera' ) ) grenvios_etq_render_cabecera();

/* URL base del blog (para el filtro "Todas") */
$blog_url = get_option( 'page_for_posts' ) ? get_permalink( get_option( 'page_for_posts' ) ) : home_url( '/' );
$current_cat = ( is_category() ) ? (int) get_queried_object_id() : 0;
$cats = get_categories( array( 'hide_empty' => true ) );
?>
<section class="blog-section padding">
	<div class="container">

		<?php
		/* Aviso cuando el blog de una ruta de país todavía muestra las guías
		 * generales en vez de las suyas (ver inc/paises-blog.php). */
		do_action( 'grenvios_blog_antes_listado' );
		?>

		<?php if ( ! is_search() && ! empty( $cats ) ) : ?>
			<div class="blog-filter">
				<a href="<?php echo esc_url( $blog_url ); ?>" class="blog-filter-btn<?php echo ( ! $current_cat && ! is_tag() ) ? ' is-active' : ''; ?>"><?php echo esc_html( grenvios_t( 'Todas' ) ); ?></a>
				<?php foreach ( $cats as $c ) : ?>
					<a href="<?php echo esc_url( get_category_link( $c->term_id ) ); ?>" class="blog-filter-btn<?php echo ( $current_cat === (int) $c->term_id ) ? ' is-active' : ''; ?>"><?php echo esc_html( $c->name ); ?></a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<h2 class="screen-reader-text"><?php echo esc_html( grenvios_t( 'Artículos' ) ); ?></h2>
			<div class="row gy-4 blog-grid">
				<?php while ( have_posts() ) : the_post(); ?>
					<div class="col-lg-4 col-md-6">
						<article <?php post_class( 'blog-card' ); ?>>
							<div class="blog-card-thumb">
								<a href="<?php the_permalink(); ?>">
									<?php if ( has_post_thumbnail() ) : ?>
										<?php the_post_thumbnail( 'medium_large', array( 'alt' => esc_attr( get_the_title() ) ) ); ?>
									<?php else : ?>
										<img src="<?php echo esc_url( function_exists( 'grenvios_rd_card_img' ) ? grenvios_rd_card_img( get_post() ) : get_template_directory_uri() . '/assets/img/post-1.jpg' ); ?>" loading="lazy" decoding="async" alt="">
									<?php endif; ?>
								</a>
								<?php
								$pcats = get_the_category();
								if ( ! empty( $pcats ) ) :
									?>
									<a class="blog-card-cat" href="<?php echo esc_url( get_category_link( $pcats[0]->term_id ) ); ?>"><?php echo esc_html( $pcats[0]->name ); ?></a>
								<?php endif; ?>
							</div>
							<div class="blog-card-body">
								<div class="blog-card-meta">
									<span><i class="fa-regular fa-calendar" aria-hidden="true"></i> <?php echo esc_html( function_exists( 'grenvios_bd_fecha' ) ? grenvios_bd_fecha( get_post_time( 'U', true ) ) : get_the_date() ); ?></span>
									<span><i class="fa-regular fa-user" aria-hidden="true"></i> Equipo Grenvíos</span>
								</div>
								<h3 class="blog-card-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
								<a href="<?php the_permalink(); ?>" class="read-more"><?php echo esc_html( grenvios_t( 'Leer más' ) ); ?> <i class="fa-solid fa-arrow-right"></i></a>
							</div>
						</article>
					</div>
				<?php endwhile; ?>
			</div>

			<div class="logisko-pagination text-center mt-50">
				<?php echo paginate_links( array(
					'mid_size'  => 1,
					'prev_text' => '<i class="fa-solid fa-angle-left"></i>',
					'next_text' => '<i class="fa-solid fa-angle-right"></i>',
				) ); ?>
			</div>

		<?php else : ?>
			<div class="text-center">
				<p><?php echo esc_html( grenvios_t( 'No hay entradas para mostrar por ahora.' ) ); ?></p>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="default-btn mt-20"><?php echo esc_html( grenvios_t( 'Volver al inicio' ) ); ?></a>
			</div>
		<?php endif; ?>
	</div>
</section>
<?php
/* Secciones del país al pie del listado: la portada del blog de una ruta es una
 * página más de esa ruta y sin esto no decía nada de su destino. Solo en el
 * listado del blog; en categorías, etiquetas y búsqueda no pinta nada. */
if ( is_home() && function_exists( 'grenvios_pais_render' ) ) {
	echo apply_filters( 'grenvios_html_final', ( function () {
		ob_start();
		grenvios_pais_render();
		return ob_get_clean();
	} )() );
}

get_footer();
