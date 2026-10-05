<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  FAQPage en las entradas del blog
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Las guías cierran con un bloque de preguntas (`.gr-post-faq`). En la página
 * eso ya es útil; para el buscador, además, conviene declararlo como FAQPage,
 * que es lo que permite que esas preguntas aparezcan desplegadas bajo el
 * resultado.
 *
 * Se lee del contenido ya renderizado, no de una lista aparte: así, si la
 * clienta edita o borra una pregunta desde el editor de WordPress, el schema
 * cambia con ella y nunca declara algo que no está en la página —que es el
 * error que hace que Google descarte el bloque entero—.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Pares pregunta/respuesta del bloque de FAQ de una entrada. */
function grenvios_bfs_pares( $html ) {
	if ( strpos( $html, 'gr-post-faq' ) === false ) return array();
	$out = array();
	if ( ! preg_match_all( '~<div class="gr-post-faq-item">\s*<h3>(.*?)</h3>\s*<p>(.*?)</p>~s', $html, $m, PREG_SET_ORDER ) ) return array();
	foreach ( $m as $x ) {
		$q = trim( wp_strip_all_tags( $x[1] ) );
		$a = trim( wp_strip_all_tags( $x[2] ) );
		if ( $q !== '' && $a !== '' ) $out[] = array( $q, $a );
	}
	return $out;
}

add_action( 'wp_head', function () {
	if ( ! is_singular( 'post' ) ) return;

	$post = get_queried_object();
	if ( ! $post ) return;

	/* El contenido pasa por `the_content` para que los tokens de sede y los
	 * enlaces entre guías estén resueltos: el schema debe decir exactamente lo
	 * mismo que lee el visitante. */
	$html  = apply_filters( 'the_content', $post->post_content );
	$pares = grenvios_bfs_pares( $html );
	if ( ! $pares ) return;

	$items = array();
	foreach ( $pares as $p ) {
		$items[] = array(
			'@type'          => 'Question',
			'name'           => $p[0],
			'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $p[1] ),
		);
	}

	echo "\n" . '<script type="application/ld+json">' . wp_json_encode( array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'@id'        => get_permalink( $post ) . '#faq',
		'mainEntity' => $items,
	), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}, 21 );
