<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Columna «País» en el listado de páginas
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Polylang ya trae su selector de banderas, pero dice «idioma» donde aquí lo que
 * hay son destinos: en el listado se ve una bandera de Cuba que en realidad
 * significa «esta página habla de enviar A Cuba», no «esta página está en
 * cubano». Esta columna lo dice con palabras, y su filtro no depende del selector
 * de Polylang.
 *
 * Es solo presentación en el admin: no toca el enrutado, ni el contenido, ni el
 * SEO. Si algún día se quita Polylang, esta columna es lo único de la vista de
 * páginas que seguiría funcionando igual.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* `grenvios_col_pais_nombre()` y `grenvios_col_pais_etiqueta()` viven en
 * inc/paises-contenido.php: el frontend también las necesita (el hub de
 * artículos por país), y este archivo solo se carga en el admin. */

/* Etiqueta de la ruta a la que pertenece una página. */
function grenvios_col_pais_de( $post_id ) {
	if ( ! function_exists( 'pll_get_post_language' ) ) return array( '', '' );
	$lang = pll_get_post_language( $post_id );
	if ( ! $lang ) return array( '', '—' );
	return array( $lang, grenvios_col_pais_etiqueta( $lang ) );
}

add_filter( 'manage_pages_columns', function ( $cols ) {
	// Se coloca justo después del título, que es donde se mira.
	$out = array();
	foreach ( $cols as $k => $v ) {
		$out[ $k ] = $v;
		if ( $k === 'title' ) $out['grenvios_pais'] = 'País';
	}
	return isset( $out['grenvios_pais'] ) ? $out : $cols + array( 'grenvios_pais' => 'País' );
} );

add_action( 'manage_pages_custom_column', function ( $col, $post_id ) {
	if ( $col !== 'grenvios_pais' ) return;
	list( $lang, $etiqueta ) = grenvios_col_pais_de( $post_id );
	if ( $etiqueta === '' ) { echo '—'; return; }

	if ( $lang === '' ) { echo esc_html( $etiqueta ); return; }
	$url = add_query_arg(
		array( 'post_type' => 'page', 'grenvios_pais' => $lang ),
		admin_url( 'edit.php' )
	);
	echo '<a href="' . esc_url( $url ) . '">' . esc_html( $etiqueta ) . '</a>'
		. ' <code style="font-size:11px;opacity:.6">/' . esc_html( $lang ) . '/</code>';
}, 10, 2 );

/* Desplegable de filtro encima del listado. */
add_action( 'restrict_manage_posts', function ( $post_type ) {
	if ( $post_type !== 'page' || ! function_exists( 'grenvios_sedes' ) ) return;
	$sedes = grenvios_sedes();
	if ( count( $sedes ) < 2 ) return;   // un solo país: el filtro no aporta nada

	$sel = isset( $_GET['grenvios_pais'] ) ? sanitize_key( $_GET['grenvios_pais'] ) : '';
	echo '<select name="grenvios_pais"><option value="">Todos los países</option>';
	foreach ( array_keys( $sedes ) as $lang ) {
		echo '<option value="' . esc_attr( $lang ) . '"' . selected( $sel, $lang, false ) . '>'
			. esc_html( grenvios_col_pais_etiqueta( $lang ) ) . ' (/' . esc_html( $lang ) . '/)</option>';
	}
	echo '</select>';
} );

/* Aplica el filtro. Polylang filtra por su propio parámetro `lang`, así que aquí
 * solo se traduce el nuestro al suyo y se deja que él haga la consulta: duplicar
 * la lógica de filtrado daría resultados distintos a los de su selector. */
add_action( 'pre_get_posts', function ( $q ) {
	if ( ! is_admin() || ! $q->is_main_query() ) return;
	if ( $q->get( 'post_type' ) !== 'page' ) return;
	$p = isset( $_GET['grenvios_pais'] ) ? sanitize_key( $_GET['grenvios_pais'] ) : '';
	if ( $p !== '' ) $q->set( 'lang', $p );
} );
