<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Ficha de destino · índice fijo «En esta página»
 * ══════════════════════════════════════════════════════════════════════════
 *
 * PROBLEMA DE USO (revisión UX, 2026-09-28): la ficha de un país tiene 28
 * secciones; en móvil mide más de 20 000 px. Quien llega buscando «cuánto
 * cuesta enviar a Chile» tiene que bajar diez pantallas para encontrarlo, y
 * nada le dice que la respuesta está en la página.
 *
 * SOLUCIÓN: una barra de accesos a las secciones que responden a las
 * preguntas de compra (qué enviar, precio, plazo, embalaje, ciudades, datos
 * prácticos, preguntas, cotizar). Va debajo del hero y se queda fija al
 * bajar, bajo la cabecera; en móvil es una fila de pastillas que se desliza.
 * Resalta la sección en la que estás.
 *
 * Solo enlaza secciones que existen en esa ficha (la de Estados Unidos no
 * tiene «Aéreo o terrestre»). Los textos se editan en el panel.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Qué secciones entran: clave => [ patrón del H2, rótulo por defecto ]. */
function grenvios_toc_items() {
	return array(
		'que-enviar' => array( '/^¿Qué puedes enviar/u',        'Qué enviar' ),
		'precio'     => array( '/^¿Cuánto cuesta/u',            'Precio' ),
		'plazo'      => array( '/^¿Cuánto demora/u',            'Plazo' ),
		'embalaje'   => array( '/^Cómo embalar/u',              'Embalaje' ),
		'via'        => array( '/^Aéreo o terrestre/u',         'Aéreo o terrestre' ),
		'ciudades'   => array( '/^Ciudades de/u',               'Ciudades' ),
		'datos'      => array( '/^Datos prácticos/u',           'Datos prácticos' ),
		'cotizar'    => array( '/^Cotiza tu envío/u',           'Cotizar' ),
		'preguntas'  => array( '/^Preguntas frecuentes sobre/u', 'Preguntas' ),
	);
}

/* Inserta el índice y las anclas. Corre sobre el bloque de la ficha (y otra
 * vez sobre la página entera): la marca `gr-toc` lo hace idempotente. */
add_filter( 'grenvios_html_final', function ( $html ) {
	if ( is_admin() || ! is_string( $html ) || strpos( $html, 'class="dest-section' ) === false ) return $html;
	if ( strpos( $html, 'class="gr-toc' ) !== false ) return $html;
	$antes = strpos( $html, '<section class="gr-dsol' );
	if ( $antes === false ) $antes = strpos( $html, '<section class="dest-section' );
	if ( $antes === false ) return $html;

	$enlaces = '';
	foreach ( grenvios_toc_items() as $k => $it ) {
		/* Las preguntas de la ficha ya no tienen sección propia: van en el
		 * acordeón final (#preguntas-frecuentes, grenvios_render_page_faqs). */
		if ( $k === 'preguntas' && ! empty( $GLOBALS['grenvios_dsec_faq_visibles'] ) ) {
			$enlaces .= '<li><a href="#preguntas-frecuentes">' . esc_html( grenvios_field( 'toc_preguntas', $it[1] ) ) . '</a></li>';
			continue;
		}
		/* El H2 que abre la sección; se le da el ancla a su <section>. */
		if ( ! preg_match_all( '~<h2[^>]*>(.*?)</h2>~su', $html, $m, PREG_OFFSET_CAPTURE ) ) break;
		foreach ( $m[1] as $h ) {
			$txt = trim( html_entity_decode( wp_strip_all_tags( $h[0] ), ENT_QUOTES, 'UTF-8' ) );
			if ( ! preg_match( $it[0], $txt ) ) continue;
			$sec = strrpos( substr( $html, 0, $h[1] ), '<section' );
			if ( $sec === false ) break;
			$fin = strpos( $html, '>', $sec );
			$tag = substr( $html, $sec, $fin - $sec );
			if ( strpos( $tag, ' id="' ) === false ) {
				$html = substr( $html, 0, $sec ) . '<section id="dp-' . $k . '"' . substr( $html, $sec + 8 );
				if ( $sec < $antes ) $antes += strlen( ' id="dp-' . $k . '"' );
				$id = 'dp-' . $k;
			} else {
				preg_match( '/ id="([^"]+)"/', $tag, $im );
				$id = $im[1];
			}
			$enlaces .= '<li><a href="#' . esc_attr( $id ) . '">' . esc_html( grenvios_field( 'toc_' . str_replace( '-', '_', $k ), $it[1] ) ) . '</a></li>';
			break;
		}
	}
	if ( substr_count( $enlaces, '<li>' ) < 3 ) return $html;   // con menos, no aporta

	$nav = '<nav class="gr-toc" aria-label="' . esc_attr( grenvios_field( 'toc_titulo', 'En esta página' ) ) . '"><div class="container"><div class="gr-toc-in">'
		. '<span class="gr-toc-k"><i class="fa-solid fa-list-ul" aria-hidden="true"></i> ' . esc_html( grenvios_field( 'toc_titulo', 'En esta página' ) ) . '</span>'
		. '<ul class="gr-toc-list">' . $enlaces . '</ul></div></div></nav>';
	return substr( $html, 0, $antes ) . $nav . substr( $html, $antes );
}, 30 );

/* Estilos: sobre los tokens del tema (skill grenvios-diseno). */
add_action( 'wp_head', function () {
	if ( is_admin() ) return;
	echo '<style id="grenvios-toc-css">'
		. '.gr-toc{position:sticky;top:var(--gr-toc-top,0px);z-index:40;background:rgba(255,255,255,.96);backdrop-filter:saturate(1.4) blur(8px);-webkit-backdrop-filter:saturate(1.4) blur(8px);border-bottom:1px solid #eee;transition:box-shadow .2s ease}'
		. '.gr-toc.is-fijo{box-shadow:0 10px 24px -18px rgba(0,0,0,.35)}'
		. '.gr-toc-in{display:flex;align-items:center;gap:18px;min-height:62px}'
		. '.gr-toc-k{flex:0 0 auto;font-size:13px;font-weight:700;letter-spacing:.02em;text-transform:uppercase;color:var(--heading-color,#0c0c0c);display:inline-flex;align-items:center;gap:8px}'
		. '.gr-toc-k i{color:var(--primary-color,#5e2129)}'
		. '.gr-toc-list{list-style:none;margin:0;padding:10px 0;display:flex;gap:8px;overflow-x:auto;scrollbar-width:none;-webkit-overflow-scrolling:touch;scroll-snap-type:x proximity}'
		. '.gr-toc-list::-webkit-scrollbar{display:none}'
		. '.gr-toc-list li{flex:0 0 auto;scroll-snap-align:start}'
		. '.gr-toc-list.mas-der{-webkit-mask-image:linear-gradient(90deg,#000 85%,transparent);mask-image:linear-gradient(90deg,#000 85%,transparent)}'
		. '.gr-toc-list.mas-izq{-webkit-mask-image:linear-gradient(90deg,transparent,#000 15%);mask-image:linear-gradient(90deg,transparent,#000 15%)}'
		. '.gr-toc-list.mas-izq.mas-der{-webkit-mask-image:linear-gradient(90deg,transparent,#000 12%,#000 88%,transparent);mask-image:linear-gradient(90deg,transparent,#000 12%,#000 88%,transparent)}'
		. '.gr-toc-list a{display:inline-block;padding:8px 16px;border-radius:999px;border:1px solid #e6e1dd;background:#fff;font-size:14px;font-weight:600;line-height:1.2;color:var(--heading-color,#0c0c0c);text-decoration:none;white-space:nowrap;transition:background .2s,color .2s,border-color .2s}'
		. '.gr-toc-list a:hover,.gr-toc-list a:focus-visible{border-color:var(--primary-color,#5e2129);color:var(--primary-color,#5e2129)}'
		. '.gr-toc-list a.is-activo{background:var(--primary-color,#5e2129);border-color:var(--primary-color,#5e2129);color:#fff}'
		. '[id^="dp-"]{scroll-margin-top:calc(var(--gr-toc-top,0px) + 80px)}'
		. '.gr-toc{margin-top:26px}'
		. '@media(min-width:992px){.gr-toc-in{gap:14px}.gr-toc-list{gap:6px}.gr-toc-list a{padding:7px 13px;font-size:13.5px}}'
		. '@media(max-width:767px){.gr-toc-k{display:none}.gr-toc-in{min-height:56px}.gr-toc-list a{font-size:13px;padding:7px 14px}}'
		. '@media(prefers-reduced-motion:reduce){.gr-toc,.gr-toc-list a{transition:none}}'
		. '</style>';
}, 103 );

/* Comportamiento: fija el índice bajo la cabecera, salta con el scroll suave
 * del tema (Lenis) y marca la sección visible. Sin dependencias. */
add_action( 'wp_footer', function () {
	if ( is_admin() ) return;
	?>
<script id="grenvios-toc-js">
/* Fila ESTÁTICA (el cliente no quiere pestañas pegadas al bajar): solo el
   salto suave a cada sección, respetando la cabecera fija y Lenis. */
(function () {
	var nav = document.querySelector('.gr-toc');
	if (!nav) return;
	function cabecera() {
		var h = 0;
		document.querySelectorAll('.sticky-header, .main-header, header').forEach(function (e) {
			var cs = getComputedStyle(e), r = e.getBoundingClientRect();
			if ((cs.position === 'fixed' || cs.position === 'sticky') && cs.display !== 'none' && +cs.opacity > 0.1 && r.top <= 1 && r.bottom > 0 && r.height < 200) h = Math.max(h, r.bottom);
		});
		return Math.round(h);
	}
	[].slice.call(nav.querySelectorAll('a[href^="#"]')).forEach(function (a) {
		a.addEventListener('click', function (e) {
			var t = document.getElementById(a.getAttribute('href').slice(1));
			if (!t) return;
			e.preventDefault();
			var off = -(Math.max(cabecera(), 80) + 16);
			if (window.lenis && typeof window.lenis.scrollTo === 'function') window.lenis.scrollTo(t, { offset: off });
			else window.scrollTo({ top: t.getBoundingClientRect().top + window.pageYOffset + off, behavior: 'smooth' });
			if (history.replaceState) history.replaceState(null, '', a.getAttribute('href'));
		});
	});
})();
</script>
	<?php
}, 60 );

/* Panel «Editar página»: los rótulos del índice, en la ficha y en la portada de su ruta. */
add_action( 'grenvios_editor_secciones', function ( $slug, $post_id = 0, $render_field = null ) {
	if ( ! is_callable( $render_field ) ) return;
	$ps = function_exists( 'grenvios_editor_pais_slug' ) ? grenvios_editor_pais_slug( $slug ) : $slug;
	$dest = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();
	if ( $ps === '' || ! isset( $dest[ $ps ] ) ) return;
	echo '<div class="nep-accordion" data-sel=".gr-toc"><button class="nep-acc-header" type="button"><span>Destino · Índice «En esta página»</span><i class="fa-solid fa-chevron-down"></i></button>'
		. '<div class="nep-acc-body"><div class="nep-grid">';
	echo $render_field( 'toc_titulo', 'Título del índice', 'text', grenvios_field( 'toc_titulo', 'En esta página' ) );
	foreach ( grenvios_toc_items() as $k => $it ) {
		$key = 'toc_' . str_replace( '-', '_', $k );
		echo $render_field( $key, 'Acceso · ' . $it[1], 'text', grenvios_field( $key, $it[1] ) );
	}
	echo '</div></div></div>';
}, 7, 3 );
