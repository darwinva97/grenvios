<?php
/**
 * Grenvíos — Palabra clave objetivo por página + auditoría on-page.
 *
 * Cada página (en CADA idioma, porque la traducción es una página propia) declara
 * UNA palabra clave objetivo. A partir de ahí:
 *
 *   · Se comprueba que esa keyword esté donde Google la lee: slug, title, meta
 *     description, H1/título y primer párrafo.
 *   · Se detecta CANIBALIZACIÓN: dos páginas del mismo idioma peleando por la
 *     misma keyword (el error más caro y más invisible de un sitio pillar-cluster).
 *   · Alimenta el enlazado interno automático (inc/seo-enlazado.php): la keyword
 *     de una página es el texto ancla con el que el resto del sitio la enlaza.
 *
 * Dónde se edita: caja "SEO Grenvíos" en la pantalla de edición de la página, y
 * vista general en Traducciones → pestaña SEO (menú "SEO por página").
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

const GRENVIOS_KW_META = 'grenvios_seo_kw';

/* ══════════════════════════════════════
   LECTURA
══════════════════════════════════════ */

/* Keyword objetivo de una página. Si no se definió a mano, se propone una a
 * partir del slug (mejor una aproximación que un campo vacío que nadie rellena). */
function grenvios_seo_kw( $post_id = 0, $fallback = true ) {
	$post_id = $post_id ? (int) $post_id : (int) get_queried_object_id();
	if ( ! $post_id ) return '';
	$kw = trim( (string) get_post_meta( $post_id, GRENVIOS_KW_META, true ) );
	if ( $kw !== '' || ! $fallback ) return $kw;

	/* Respaldo del slug: sin tildes y sin preposiciones («envio de equipaje»).
	 * Como esta keyword es el texto ancla del enlazado interno, antes de caer
	 * ahí se pregunta por una escrita como se busca
	 * (filtro `grenvios_seo_kw_default`, en inc/seo-keywords-defecto.php). */
	$slugkw = trim( str_replace( '-', ' ', (string) get_post_field( 'post_name', $post_id ) ) );
	return (string) apply_filters( 'grenvios_seo_kw_default', $slugkw, $post_id );
}

/* ¿La keyword se definió a mano? (para distinguir "propuesta" de "decidida") */
function grenvios_seo_kw_is_set( $post_id ) {
	return trim( (string) get_post_meta( (int) $post_id, GRENVIOS_KW_META, true ) ) !== '';
}

/* Texto visible aproximado de una página: título + textos del editor de página +
 * contenido libre. Sirve para medir presencia y densidad de la keyword sin tener
 * que renderizar la página entera. */
function grenvios_seo_page_text( $post_id ) {
	$post_id = (int) $post_id;
	$parts   = array( get_post_field( 'post_title', $post_id ) );

	// Campos del editor de página guardados en esta página (ya traducidos si es
	// una traducción) y, si no se guardaron, los textos por defecto del tema.
	$slug = function_exists( 'grenvios_canonical_slug' ) ? grenvios_canonical_slug( $post_id ) : get_post_field( 'post_name', $post_id );
	if ( (int) get_option( 'page_on_front' ) === $post_id ) $slug = 'home';

	if ( function_exists( 'grenvios_text_registry' ) ) {
		$reg = grenvios_text_registry();
		if ( isset( $reg[ $slug ] ) ) {
			foreach ( $reg[ $slug ]['sections'] as $sec ) {
				foreach ( $sec['fields'] as $key => $f ) {
					if ( ( isset( $f[1] ) ? $f[1] : 'text' ) === 'image' ) continue;
					$v = metadata_exists( 'post', $post_id, 'grenvios_' . $key )
						? get_post_meta( $post_id, 'grenvios_' . $key, true )
						: ( isset( $f[2] ) ? $f[2] : '' );
					if ( is_string( $v ) && $v !== '' ) $parts[] = $v;
				}
			}
		}
	}
	// Bloques repetibles guardados en la página.
	foreach ( get_post_meta( $post_id ) as $k => $v ) {
		if ( strpos( $k, 'grenvios_rep_' ) !== 0 ) continue;
		$val = maybe_unserialize( $v[0] );
		if ( ! is_array( $val ) ) continue;
		array_walk_recursive( $val, function ( $x ) use ( &$parts ) {
			if ( is_string( $x ) && $x !== '' ) $parts[] = $x;
		} );
	}
	$parts[] = (string) get_post_field( 'post_content', $post_id );

	/* Filtro `grenvios_seo_page_text`: buena parte del sitio no vive en
	 * post_content —las fichas de destino, las secciones por país y las
	 * propias de la ruta principal se pintan en PHP—, así que sin esto la
	 * auditoría contaba «1 palabra» en páginas de 3.700 y pedía contenido que
	 * ya existe. Lo completa inc/seo-titulos.php. */
	$parts = (array) apply_filters( 'grenvios_seo_page_text', $parts, $post_id, $slug );

	return wp_strip_all_tags( implode( "\n", $parts ) );
}

/* Primer texto visible de la página (aproxima el primer párrafo). */
function grenvios_seo_first_text( $post_id ) {
	$t = grenvios_seo_page_text( $post_id );
	return mb_substr( trim( preg_replace( '/\s+/u', ' ', $t ) ), 0, 400 );
}

/* Normaliza para comparar: minúsculas, sin tildes, sin puntuación. */
function grenvios_seo_norm( $s ) {
	$s = wp_strip_all_tags( (string) $s );
	$s = remove_accents( $s );
	$s = function_exists( 'mb_strtolower' ) ? mb_strtolower( $s, 'UTF-8' ) : strtolower( $s );
	$s = preg_replace( '/[^a-z0-9 ]+/', ' ', $s );
	return trim( preg_replace( '/\s+/', ' ', $s ) );
}

/* ¿Aparece la keyword en el texto? (tolerante a plurales y a palabras intermedias
 * cortas: "envio de paquetes" cuenta para "envio paquetes"). */
function grenvios_seo_has_kw( $haystack, $kw ) {
	$h = grenvios_seo_norm( $haystack );
	$k = grenvios_seo_norm( $kw );
	if ( $h === '' || $k === '' ) return false;
	if ( strpos( $h, $k ) !== false ) return true;
	// Todas las palabras significativas presentes y en orden.
	$words = array_filter( explode( ' ', $k ), function ( $w ) { return mb_strlen( $w ) > 2; } );
	if ( ! $words ) return false;
	$pat = implode( '\W+(?:\w+\W+){0,2}', array_map( function ( $w ) { return preg_quote( $w, '/' ); }, $words ) );
	return (bool) preg_match( '/' . $pat . '/', $h );
}

/* Veces que aparece la keyword y densidad aproximada (%). */
function grenvios_seo_kw_density( $post_id, $kw ) {
	$text  = grenvios_seo_norm( grenvios_seo_page_text( $post_id ) );
	$k     = grenvios_seo_norm( $kw );
	if ( $text === '' || $k === '' ) return array( 0, 0.0 );
	$n     = substr_count( $text, $k );
	$words = max( 1, str_word_count( $text ) );
	return array( $n, round( $n * count( explode( ' ', $k ) ) / $words * 100, 2 ) );
}

/* ══════════════════════════════════════
   AUDITORÍA ON-PAGE
   Devuelve una lista de comprobaciones: [estado, etiqueta, detalle]
   estado: ok | warn | error
══════════════════════════════════════ */
function grenvios_seo_audit( $post_id ) {
	$post_id = (int) $post_id;
	$out     = array();
	$kw      = grenvios_seo_kw( $post_id );
	$slug    = (string) get_post_field( 'post_name', $post_id );
	$title   = (string) get_post_field( 'post_title', $post_id );

	$cslug = function_exists( 'grenvios_canonical_slug' ) ? grenvios_canonical_slug( $post_id ) : $slug;
	if ( (int) get_option( 'page_on_front' ) === $post_id ) $cslug = 'home';
	$seo = function_exists( 'grenvios_seo_for_slug' ) ? grenvios_seo_for_slug( $cslug, $post_id ) : array( '', '' );
	$seo_title = isset( $seo[0] ) ? $seo[0] : '';
	$seo_desc  = isset( $seo[1] ) ? $seo[1] : '';

	// 0) Keyword definida
	$out[] = grenvios_seo_kw_is_set( $post_id )
		? array( 'ok', 'Keyword objetivo', $kw )
		: array( 'warn', 'Keyword objetivo', 'Sin definir — se está usando el slug («' . $kw . '»). Defínela para que el enlazado interno use el ancla correcta.' );

	// 1) Slug
	$out[] = grenvios_seo_has_kw( str_replace( '-', ' ', $slug ), $kw )
		? array( 'ok', 'Keyword en el slug', '/' . $slug . '/' )
		: array( 'warn', 'Keyword en el slug', 'El slug «' . $slug . '» no contiene la keyword. Cambiarlo en una página ya indexada cuesta posiciones: hazlo solo si la página es nueva o si el slug actual es claramente malo.' );

	// 2) Title tag
	$len = mb_strlen( $seo_title );
	if ( $seo_title === '' ) {
		$out[] = array( 'error', 'Title tag', 'Vacío: Google inventará uno.' );
	} elseif ( ! grenvios_seo_has_kw( $seo_title, $kw ) ) {
		$out[] = array( 'error', 'Title tag', 'No contiene la keyword. Es la señal on-page que más pesa.' );
	} elseif ( $len > 60 ) {
		$out[] = array( 'warn', 'Title tag', $len . ' caracteres: se cortará en el buscador (máx. ~60).' );
	} elseif ( $len < 30 ) {
		$out[] = array( 'warn', 'Title tag', $len . ' caracteres: estás desaprovechando espacio.' );
	} else {
		$out[] = array( 'ok', 'Title tag', $len . ' caracteres, con la keyword.' );
	}

	// 3) Meta description
	$len = mb_strlen( $seo_desc );
	if ( $seo_desc === '' ) {
		$out[] = array( 'error', 'Meta description', 'Vacía: pierdes el control del texto que invita a hacer clic.' );
	} elseif ( $len < 110 || $len > 165 ) {
		$out[] = array( 'warn', 'Meta description', $len . ' caracteres (recomendado 120-160).' );
	} elseif ( ! grenvios_seo_has_kw( $seo_desc, $kw ) ) {
		$out[] = array( 'warn', 'Meta description', 'Sin la keyword: no se resaltará en negrita en el resultado.' );
	} else {
		$out[] = array( 'ok', 'Meta description', $len . ' caracteres, con la keyword.' );
	}

	// 4) Título visible (H1). Se mide el H1 que sale publicado: el título
	//    interno de la página («Aduanas e Impuestos») casi nunca es el H1 que
	//    pinta la plantilla, y medir ese daba avisos falsos en 31 páginas.
	$h1 = grenvios_seo_h1_real( $post_id );
	$out[] = grenvios_seo_has_kw( $h1, $kw )
		? array( 'ok', 'Keyword en el H1', $h1 )
		: array( 'warn', 'Keyword en el H1', 'El H1 no la contiene («' . $h1 . '»).' );

	// 5) Primer párrafo
	$out[] = grenvios_seo_has_kw( grenvios_seo_first_text( $post_id ), $kw )
		? array( 'ok', 'Keyword en el primer bloque de texto', 'Aparece pronto en la página.' )
		: array( 'warn', 'Keyword en el primer bloque de texto', 'No aparece en las primeras líneas: es donde Google y el usuario confirman de qué va la página.' );

	// 6) Densidad
	list( $n, $dens ) = grenvios_seo_kw_density( $post_id, $kw );
	if ( $n === 0 ) {
		$out[] = array( 'error', 'Repeticiones', 'La keyword no aparece en el cuerpo de la página.' );
	} elseif ( $dens > 3.5 ) {
		$out[] = array( 'warn', 'Repeticiones', $n . ' veces (' . $dens . '%): demasiado. Suena forzado y Google lo penaliza.' );
	} elseif ( $dens < 0.4 ) {
		$out[] = array( 'warn', 'Repeticiones', $n . ' veces (' . $dens . '%): poca presencia. Usa también variantes naturales.' );
	} else {
		$out[] = array( 'ok', 'Repeticiones', $n . ' veces (' . $dens . '%).' );
	}

	// 7) Longitud del contenido
	$words = str_word_count( grenvios_seo_norm( grenvios_seo_page_text( $post_id ) ) );
	if ( $words < 300 ) {
		$out[] = array( 'error', 'Extensión', $words . ' palabras: insuficiente para competir. Apunta a 600+ en páginas de servicio y destino.' );
	} elseif ( $words < 600 ) {
		$out[] = array( 'warn', 'Extensión', $words . ' palabras: correcto, pero mejorable con FAQ y detalles operativos.' );
	} else {
		$out[] = array( 'ok', 'Extensión', $words . ' palabras.' );
	}

	// 8) Canibalización: otra página del mismo idioma con la misma keyword.
	$dupes = grenvios_seo_kw_conflicts( $post_id );
	$out[] = $dupes
		? array( 'error', 'Canibalización', 'Misma keyword que: ' . implode( ', ', $dupes ) . '. Compiten entre sí y Google elegirá una. Diferencia la keyword o fusiona las páginas.' )
		: array( 'ok', 'Canibalización', 'Keyword única en su idioma.' );

	// 9) Enlaces internos (los calcula el módulo de enlazado)
	if ( function_exists( 'grenvios_links_report' ) ) {
		$rep = grenvios_links_report( $post_id );
		$out[] = $rep['in'] >= 2
			? array( 'ok', 'Enlaces internos que apuntan aquí', $rep['in'] . ' páginas enlazan a esta.' )
			: array( 'warn', 'Enlaces internos que apuntan aquí', $rep['in'] . ': una página sin enlaces entrantes es una página huérfana para Google.' );
	}

	return $out;
}

/* H1 tal como sale publicado. Se lee de la página servida y se guarda 12 h;
 * si la petición falla (servidor local sin loopback, por ejemplo) se usa el
 * título interno, que es lo que medía antes esta comprobación. */
function grenvios_seo_h1_real( $post_id ) {
	$post_id = (int) $post_id;
	$key     = 'grenvios_h1_' . $post_id;
	$h1      = get_transient( $key );
	if ( is_string( $h1 ) && $h1 !== '' ) return $h1;
	$h1  = '';
	$res = wp_remote_get( get_permalink( $post_id ), array( 'timeout' => 20, 'sslverify' => false ) );
	if ( ! is_wp_error( $res ) && (int) wp_remote_retrieve_response_code( $res ) === 200 ) {
		if ( preg_match( '#<h1[^>]*>(.*?)</h1>#is', (string) wp_remote_retrieve_body( $res ), $m ) ) {
			$h1 = trim( preg_replace( '/\s+/u', ' ', html_entity_decode( wp_strip_all_tags( $m[1] ), ENT_QUOTES, 'UTF-8' ) ) );
		}
	}
	if ( $h1 === '' ) return (string) get_post_field( 'post_title', $post_id );
	set_transient( $key, $h1, 12 * HOUR_IN_SECONDS );
	return $h1;
}
add_action( 'save_post_page', function ( $post_id ) { delete_transient( 'grenvios_h1_' . (int) $post_id ); } );

/* Páginas del MISMO idioma que declaran la misma keyword. */
function grenvios_seo_kw_conflicts( $post_id ) {
	$post_id = (int) $post_id;
	$kw      = grenvios_seo_norm( grenvios_seo_kw( $post_id ) );
	if ( $kw === '' ) return array();

	$args = array( 'post_type' => 'page', 'post_status' => 'publish', 'numberposts' => -1 );
	if ( function_exists( 'grenvios_i18n_active' ) && grenvios_i18n_active() ) {
		$args['lang'] = pll_get_post_language( $post_id );
	}
	$out = array();
	foreach ( get_posts( $args ) as $p ) {
		if ( (int) $p->ID === $post_id ) continue;
		if ( grenvios_seo_norm( grenvios_seo_kw( $p->ID ) ) === $kw ) $out[] = $p->post_title;
	}
	return $out;
}

/* ══════════════════════════════════════
   CAJA EN LA PANTALLA DE EDICIÓN DE LA PÁGINA
══════════════════════════════════════ */
add_action( 'add_meta_boxes', function () {
	add_meta_box( 'grenvios-seo', 'SEO Grenvíos', 'grenvios_seo_metabox', 'page', 'normal', 'high' );
} );

function grenvios_seo_metabox( $post ) {
	wp_nonce_field( 'grenvios_seo_meta', 'grenvios_seo_nonce' );
	$kw    = (string) get_post_meta( $post->ID, GRENVIOS_KW_META, true );
	$title = (string) get_post_meta( $post->ID, 'grenvios_seo_title', true );
	$desc  = (string) get_post_meta( $post->ID, 'grenvios_seo_desc', true );
	$lang  = ( function_exists( 'grenvios_i18n_active' ) && grenvios_i18n_active() ) ? pll_get_post_language( $post->ID ) : '';
	?>
	<p><label><strong>Palabra clave objetivo</strong><?php if ( $lang ) echo ' <em>(' . esc_html( strtoupper( $lang ) ) . ')</em>'; ?><br>
		<input type="text" name="grenvios_seo_kw" value="<?php echo esc_attr( $kw ); ?>" class="widefat"
			placeholder="ej. envío de paquetes internacional"></label>
		<span class="description">Una sola por página. Es también el texto con el que el resto del sitio la enlaza.</span></p>

	<p><label><strong>Title tag</strong> (lo que se ve en Google, ~60 caracteres)<br>
		<input type="text" name="grenvios_seo_title" value="<?php echo esc_attr( $title ); ?>" class="widefat"></label>
		<span class="description">Vacío = se usa el texto por defecto del tema.</span></p>

	<p><label><strong>Meta description</strong> (120-160 caracteres, con llamada a la acción)<br>
		<textarea name="grenvios_seo_desc" rows="3" class="widefat"><?php echo esc_textarea( $desc ); ?></textarea></label></p>

	<h4 style="margin:14px 0 6px">Auditoría de esta página</h4>
	<table class="widefat striped" style="max-width:820px"><tbody>
	<?php foreach ( grenvios_seo_audit( $post->ID ) as $c ) :
		$icon = $c[0] === 'ok' ? '✅' : ( $c[0] === 'warn' ? '⚠️' : '⛔' ); ?>
		<tr>
			<td style="width:26px"><?php echo $icon; ?></td>
			<td style="width:34%"><strong><?php echo esc_html( $c[1] ); ?></strong></td>
			<td><?php echo esc_html( $c[2] ); ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody></table>
	<p class="description">La auditoría se recalcula al guardar la página.</p>
	<?php
}

add_action( 'save_post_page', function ( $post_id ) {
	if ( ! isset( $_POST['grenvios_seo_nonce'] ) || ! wp_verify_nonce( $_POST['grenvios_seo_nonce'], 'grenvios_seo_meta' ) ) return;
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_page', $post_id ) ) return;

	foreach ( array( GRENVIOS_KW_META => 'grenvios_seo_kw', 'grenvios_seo_title' => 'grenvios_seo_title', 'grenvios_seo_desc' => 'grenvios_seo_desc' ) as $meta => $field ) {
		if ( ! isset( $_POST[ $field ] ) ) continue;
		$v = sanitize_text_field( wp_unslash( $_POST[ $field ] ) );
		if ( $v === '' ) delete_post_meta( $post_id, $meta );
		else update_post_meta( $post_id, $meta, $v );
	}
	delete_transient( 'grenvios_link_map' );   // la keyword alimenta el enlazado interno
}, 10, 1 );

/* ══════════════════════════════════════
   PANTALLA GENERAL: SEO por página
══════════════════════════════════════ */
add_action( 'admin_menu', function () {
	add_menu_page(
		'SEO por página', 'SEO por página', 'edit_pages',
		'grenvios-seo', 'grenvios_seo_admin_page', 'dashicons-search', 29
	);
} );

function grenvios_seo_admin_page() {
	if ( ! current_user_can( 'edit_pages' ) ) wp_die( 'Sin permiso.' );

	$langs = ( function_exists( 'grenvios_i18n_langs' ) ) ? grenvios_i18n_langs() : array();
	$cur   = isset( $_GET['lang'] ) ? sanitize_key( $_GET['lang'] ) : ( function_exists( 'grenvios_i18n_default' ) ? grenvios_i18n_default() : '' );

	$args = array( 'post_type' => 'page', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'menu_order', 'order' => 'ASC' );
	if ( $langs && isset( $langs[ $cur ] ) ) $args['lang'] = $cur;
	$pages = get_posts( $args );
	?>
	<div class="wrap">
		<h1>SEO por página</h1>
		<p>Una keyword por página, sin que dos peleen por la misma. Las filas en rojo son las que hoy te están costando posiciones.</p>

		<?php if ( count( $langs ) > 1 ) : ?>
			<p><?php foreach ( $langs as $slug => $l ) :
				$url = add_query_arg( array( 'page' => 'grenvios-seo', 'lang' => $slug ), admin_url( 'admin.php' ) ); ?>
				<a class="button <?php echo $slug === $cur ? 'button-primary' : ''; ?>" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $l['name'] ); ?></a>
			<?php endforeach; ?></p>
		<?php endif; ?>

		<table class="widefat striped">
			<thead><tr>
				<th style="width:24%">Página</th>
				<th style="width:22%">Keyword objetivo</th>
				<th style="width:10%">Enlaces int.</th>
				<th>Problemas detectados</th>
				<th style="width:70px"></th>
			</tr></thead>
			<tbody>
			<?php foreach ( $pages as $p ) :
				$audit = grenvios_seo_audit( $p->ID );
				$bad   = array_filter( $audit, function ( $c ) { return $c[0] !== 'ok'; } );
				$err   = array_filter( $audit, function ( $c ) { return $c[0] === 'error'; } );
				$rep   = function_exists( 'grenvios_links_report' ) ? grenvios_links_report( $p->ID ) : array( 'in' => '-', 'out' => '-' ); ?>
				<tr<?php echo $err ? ' style="background:#fcf0f1"' : ''; ?>>
					<td><strong><?php echo esc_html( $p->post_title ); ?></strong><br><code><?php echo esc_html( $p->post_name ); ?></code></td>
					<td>
						<?php echo esc_html( grenvios_seo_kw( $p->ID ) ); ?>
						<?php if ( ! grenvios_seo_kw_is_set( $p->ID ) ) : ?><br><em style="color:#996800">propuesta desde el slug</em><?php endif; ?>
					</td>
					<td><?php echo esc_html( $rep['in'] ); ?> ← / → <?php echo esc_html( $rep['out'] ); ?></td>
					<td>
						<?php if ( ! $bad ) : ?><span style="color:#0a3622">Todo correcto.</span><?php else : ?>
							<ul style="margin:0;list-style:disc;padding-left:18px">
							<?php foreach ( $bad as $c ) : ?>
								<li><?php echo $c[0] === 'error' ? '⛔' : '⚠️'; ?> <strong><?php echo esc_html( $c[1] ); ?>:</strong> <?php echo esc_html( $c[2] ); ?></li>
							<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</td>
					<td><a class="button button-small" href="<?php echo esc_url( get_edit_post_link( $p->ID ) ); ?>">Editar</a></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>

		<?php if ( function_exists( 'grenvios_cluster_report' ) ) : ?>
			<h2>Contenido de apoyo por página de dinero</h2>
			<p>Cada guía refuerza UNA página. Las que están a cero dependen solo de sí mismas para posicionar.</p>
			<table class="widefat striped" style="max-width:640px">
				<thead><tr><th>Página</th><th style="width:120px">Guías</th></tr></thead><tbody>
				<?php foreach ( grenvios_cluster_report() as $row ) : ?>
					<tr<?php echo $row['guias'] === 0 ? ' style="background:#fcf9e8"' : ''; ?>>
						<td><?php echo esc_html( $row['title'] ); ?></td>
						<td><?php echo (int) $row['guias']; ?><?php echo $row['guias'] === 0 ? ' — sin apoyo' : ''; ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody></table>
		<?php endif; ?>
	</div>
	<?php
}
