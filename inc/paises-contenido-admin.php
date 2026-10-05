<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Destinos → Contenido por país
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Pantalla para rellenar los campos de los que dependen las secciones que no se
 * pueden deducir de los datos que ya tiene el tema: qué se envía más a ese país,
 * qué no admite su aduana, qué documentación exige, cómo conviene embalar.
 *
 * Son justo los datos que no se pueden inventar. El tema sabe el plazo, la
 * modalidad, la forma de entrega y el impuesto porque están escritos en la ficha
 * del destino; lo demás lo sabe quien despacha los envíos, y hasta que lo escriba
 * la sección no se pinta.
 *
 * La tabla de cobertura de arriba dice, país por país, cuántas de sus 24 páginas
 * llegan al umbral para entrar al índice y cuáles se quedan cortas. Es la lista
 * de tareas: rellenar un campo abre varias páginas de golpe.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'admin_menu', function () {
	add_submenu_page(
		'grenvios-destinos',
		'Contenido por país', 'Contenido por país', 'edit_pages',
		'grenvios-pais-contenido', 'grenvios_pais_contenido_admin'
	);
}, 20 );

/* Guarda los campos y, si se pide, rehace el bloque de las páginas ya creadas. */
add_action( 'admin_post_grenvios_pais_save', function () {
	if ( ! current_user_can( 'edit_pages' ) ) wp_die( 'Sin permiso.' );
	check_admin_referer( 'grenvios_pais_admin' );

	$slug = isset( $_POST['pais'] ) ? sanitize_title( wp_unslash( $_POST['pais'] ) ) : '';
	if ( $slug === '' ) {
		wp_safe_redirect( admin_url( 'admin.php?page=grenvios-pais-contenido' ) );
		exit;
	}

	$all = grenvios_pais_extra_all();
	$row = isset( $all[ $slug ] ) ? (array) $all[ $slug ] : array();
	foreach ( grenvios_pais_campos() as $k => $_ ) {
		if ( isset( $_POST[ $k ] ) ) $row[ $k ] = sanitize_textarea_field( wp_unslash( $_POST[ $k ] ) );
	}
	$all[ $slug ] = $row;
	grenvios_pais_extra_save( $all );

	/* Rehacer el bloque no es automático a propósito: reescribe el contenido de
	 * hasta 24 páginas y, si la clienta ya retocó alguna a mano, machacaría el
	 * bloque —no su texto, pero sí el bloque— sin avisar. Lo pide ella. */
	$rehechas = 0;
	if ( ! empty( $_POST['regenerar'] ) && function_exists( 'grenvios_pais_regenerar' ) ) {
		$lang = '';
		if ( function_exists( 'grenvios_sedes' ) ) {
			foreach ( grenvios_sedes() as $l => $_x ) {
				if ( function_exists( 'grenvios_sede_destino_propio' ) && grenvios_sede_destino_propio( $l ) === $slug ) { $lang = $l; break; }
			}
		}
		if ( $lang !== '' ) $rehechas = (int) grenvios_pais_regenerar( $lang );
	}

	wp_safe_redirect( add_query_arg(
		array( 'page' => 'grenvios-pais-contenido', 'pais' => $slug, 'gv_ok' => 1, 'gv_n' => $rehechas ),
		admin_url( 'admin.php' )
	) );
	exit;
} );

/* Cuántas páginas de una ruta llegan al umbral y entran al índice. */
function grenvios_pais_cobertura( $lang ) {
	$out = array( 'total' => 0, 'abiertas' => 0, 'cerradas' => array() );
	$ids = get_posts( array(
		'post_type' => 'page', 'numberposts' => -1, 'post_status' => 'publish',
		'fields' => 'ids', 'lang' => $lang,
	) );
	foreach ( $ids as $id ) {
		$out['total']++;
		if ( function_exists( 'grenvios_pagina_pais_propia' ) && grenvios_pagina_pais_propia( $id ) ) {
			$out['abiertas']++;
		} else {
			$out['cerradas'][] = get_the_title( $id );
		}
	}
	return $out;
}

function grenvios_pais_contenido_admin() {
	if ( ! current_user_can( 'edit_pages' ) ) wp_die( 'Sin permiso.' );

	$dest  = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();
	$sedes = function_exists( 'grenvios_sedes' ) ? grenvios_sedes() : array();
	$sel   = isset( $_GET['pais'] ) ? sanitize_title( wp_unslash( $_GET['pais'] ) ) : '';
	if ( $sel === '' || ! isset( $dest[ $sel ] ) ) { $ks = array_keys( $dest ); $sel = $ks ? $ks[0] : ''; }

	echo '<div class="wrap"><h1>Contenido por país</h1>';

	if ( isset( $_GET['gv_ok'] ) ) {
		$n = isset( $_GET['gv_n'] ) ? (int) $_GET['gv_n'] : 0;
		echo '<div class="notice notice-success is-dismissible"><p>Guardado.'
			. ( $n ? ' Se rehizo el bloque de ' . $n . ' páginas.' : '' ) . '</p></div>';
	}

	echo '<p>Las secciones de plazos, aduana, modalidades y entrega se construyen solas con la ficha del destino. '
		. 'Aquí se rellena <strong>lo que el tema no puede saber</strong>: sin estos datos, esas secciones no se pintan '
		. '—en vez de pintarse con texto inventado—.</p>';

	/* ── Cobertura ── */
	if ( $sedes ) {
		echo '<h2>Qué falta por país</h2><table class="widefat striped" style="max-width:900px"><thead><tr>'
			. '<th>País</th><th>Páginas en el índice</th><th>Campos sin rellenar</th></tr></thead><tbody>';
		foreach ( $sedes as $lang => $s ) {
			if ( function_exists( 'grenvios_es_ruta_pais' ) && ! grenvios_es_ruta_pais( $lang ) ) continue;
			$c = grenvios_pais_cobertura( $lang );
			if ( ! $c['total'] ) continue;
			$nombre = isset( $s['name'] ) ? $s['name'] : $lang;
			/* La columna útil no es cuántas páginas indexan —con el contenido
			 * automático suelen indexar todas— sino qué datos faltan: cada campo
			 * que se rellena añade una sección propia a varias páginas de golpe. */
			$pslug  = function_exists( 'grenvios_sede_destino_propio' ) ? grenvios_sede_destino_propio( $lang ) : '';
			$dp     = $pslug !== '' ? grenvios_pais_datos( $pslug ) : array();
			$faltan = array();
			if ( $dp ) {
				foreach ( grenvios_pais_campos() as $k => $meta ) {
					if ( trim( (string) ( isset( $dp[ $k ] ) ? $dp[ $k ] : '' ) ) === '' ) $faltan[] = $meta[0];
				}
			}
			$edit = add_query_arg( array( 'page' => 'grenvios-pais-contenido', 'pais' => $pslug ), admin_url( 'admin.php' ) );
			echo '<tr><td><strong>' . esc_html( $nombre ) . '</strong> <code>/' . esc_html( $lang ) . '/</code></td>'
				. '<td>' . (int) $c['abiertas'] . ' de ' . (int) $c['total'] . '</td>'
				. '<td>' . ( $faltan
					? '<a href="' . esc_url( $edit ) . '">' . esc_html( implode( ', ', $faltan ) ) . '</a>'
					: '<span style="color:#2271b1">completo</span>' ) . '</td></tr>';
		}
		echo '</tbody></table>';
	}

	/* ── Selector de país ── */
	echo '<h2 style="margin-top:28px">Editar un país</h2><p>';
	foreach ( $dest as $slug => $d ) {
		$url = add_query_arg( array( 'page' => 'grenvios-pais-contenido', 'pais' => $slug ), admin_url( 'admin.php' ) );
		$on  = ( $slug === $sel );
		echo '<a href="' . esc_url( $url ) . '" class="button' . ( $on ? ' button-primary' : '' ) . '" style="margin:0 4px 4px 0">'
			. esc_html( $d['title'] ) . '</a>';
	}
	echo '</p>';

	if ( $sel === '' ) { echo '</div>'; return; }

	$datos = grenvios_pais_datos( $sel );
	$all   = grenvios_pais_extra_all();
	$row   = isset( $all[ $sel ] ) ? (array) $all[ $sel ] : array();

	echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '" style="max-width:900px">';
	wp_nonce_field( 'grenvios_pais_admin' );
	echo '<input type="hidden" name="action" value="grenvios_pais_save">';
	echo '<input type="hidden" name="pais" value="' . esc_attr( $sel ) . '">';

	// Lo que el tema ya sabe, para que se vea que no hay que repetirlo aquí.
	if ( $datos ) {
		echo '<p class="description" style="margin:10px 0 18px">De la ficha de ' . esc_html( $datos['title'] )
			. ' ya se usan: plazo <code>' . esc_html( $datos['tiempo'] ) . '</code>, modalidades <code>'
			. esc_html( $datos['modos'] ) . '</code>, entrega <code>' . esc_html( $datos['entrega'] ) . '</code>'
			. ( $datos['impuesto'] !== '' ? ', impuesto <code>' . esc_html( $datos['impuesto'] ) . ' %</code>' : '' )
			. '. Se editan en <a href="' . esc_url( admin_url( 'admin.php?page=grenvios-destinos' ) ) . '">Destinos</a>.</p>';
	}

	echo '<table class="form-table">';
	foreach ( grenvios_pais_campos() as $k => $meta ) {
		$v = isset( $row[ $k ] ) ? $row[ $k ] : '';
		echo '<tr><th scope="row"><label for="gv_' . esc_attr( $k ) . '">' . esc_html( $meta[0] ) . '</label></th><td>'
			. '<textarea id="gv_' . esc_attr( $k ) . '" name="' . esc_attr( $k ) . '" rows="3" class="large-text">'
			. esc_textarea( $v ) . '</textarea>'
			. '<p class="description">' . esc_html( $meta[1] ) . '</p></td></tr>';
	}
	echo '</table>';

	echo '<p><label><input type="checkbox" name="regenerar" value="1" checked> '
		. 'Rehacer el bloque de las páginas de este país al guardar</label><br>'
		. '<span class="description">Reescribe solo el bloque automático. El texto que hayas escrito tú fuera de él no se toca.</span></p>';

	submit_button( 'Guardar' );
	echo '</form></div>';
}
