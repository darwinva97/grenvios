<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Calculadora de peso volumétrico
 * ══════════════════════════════════════════════════════════════════════════
 *
 * Todo el sitio explica el peso volumétrico —la portada, la página del
 * servicio, cuatro guías del blog— y en ningún sitio se podía calcular. La
 * consulta «cómo calcular el peso volumétrico» es de las que más tráfico mueve
 * en este sector y es, además, la que más enlaces gana: una calculadora se
 * enlaza y se comparte; un párrafo explicando una división, no.
 *
 * Es un cálculo aritmético, no una cotización: la fórmula es pública y la misma
 * en todo el sector (alto × largo × ancho ÷ 5000). Por eso el resultado se
 * muestra con lo que significa —cuál de los dos pesos se cobra y cuánto volumen
 * sobra— y termina llevando a cotizar, donde sí entran la vía, el destino y la
 * tarifa.
 *
 * Sin dependencias: el JS son treinta líneas en línea. Funciona sin él —los
 * campos son un formulario normal— y con teclado.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Página donde se pinta. Filtro por si se quiere mover o repetir. */
function grenvios_hv_paginas() {
	return apply_filters( 'grenvios_hv_paginas', array( 'peso-volumetrico' ) );
}

/* La página de herramienta (inc/paginas-herramientas.php) ya trae su propia
 * calculadora, con el divisor editable: pintar esta además dejaba DOS
 * calculadoras seguidas en /servicios/peso-volumetrico/ (y, en las rutas de
 * país, una nota mandando a «usar la calculadora» justo debajo de ella).
 * Aquí se queda solo la pregunta frecuente con la fórmula. */
function grenvios_hv_redundante( $slug ) {
	return function_exists( 'grenvios_tool_slugs' ) && in_array( $slug, grenvios_tool_slugs(), true );
}

function grenvios_hv_campos() {
	return array(
		'hv_sub'     => array( 'Calculadora · Antetítulo', 'text', 'Calcúlalo tú mismo' ),
		'hv_title'   => array( 'Calculadora · Título', 'html', 'Calculadora de <span class="hl">peso volumétrico</span>' ),
		'hv_intro'   => array( 'Calculadora · Texto introductorio', 'textarea', 'Mide la caja ya armada y cerrada, por su parte más ancha, y anota su peso real en la balanza. La calculadora te dice por cuál de los dos pesos se cobra tu envío.' ),
		'hv_nota'    => array( 'Calculadora · Nota final', 'textarea', 'El resultado es el peso que se cobra, no el precio: sobre él influyen además la vía —aérea o terrestre— y el país de destino. Con estos mismos datos te damos el precio cerrado en minutos.' ),
	);
}

add_filter( 'grenvios_text_registry', function ( $reg ) {
	if ( ! is_admin() && function_exists( 'grenvios_hq_pais' ) && grenvios_hq_pais() !== '' ) return $reg;
	foreach ( grenvios_hv_paginas() as $slug ) {
		if ( ! isset( $reg[ $slug ] ) || grenvios_hv_redundante( $slug ) ) continue;
		$reg[ $slug ]['sections']['calculadora'] = array(
			'label' => 'Calculadora de peso volumétrico',
			'_no_token_check' => true,
			'sel'   => '.gr-calc',
			'fields' => grenvios_hv_campos(),
		);
	}
	return $reg;
} );

function grenvios_hv_html() {
	$c    = grenvios_hv_campos();
	$home = function_exists( 'grenvios_url_base' ) ? grenvios_url_base() : home_url();
	$sub  = grenvios_field( 'hv_sub',   $c['hv_sub'][2] );
	$tit  = grenvios_field( 'hv_title', $c['hv_title'][2] );
	$int  = grenvios_field( 'hv_intro', $c['hv_intro'][2] );
	$nota = grenvios_field( 'hv_nota',  $c['hv_nota'][2] );

	ob_start(); ?>
<section class="gr-calc padding-bottom"><div class="container">
	<div class="section-heading text-center mb-40">
		<?php if ( trim( (string) $sub ) !== '' ) : ?><h3 class="sub-heading"><?php echo esc_html( $sub ); ?></h3><?php endif; ?>
		<h2><?php echo wp_kses_post( $tit ); ?></h2>
		<?php if ( trim( (string) $int ) !== '' ) : ?><p><?php echo wp_kses_post( $int ); ?></p><?php endif; ?>
	</div>

	<form class="gr-calc-box" id="gr-calc" novalidate>
		<div class="gr-calc-campos">
			<div class="gr-calc-campo">
				<label for="gc-alto">Alto (cm)</label>
				<input type="number" id="gc-alto" min="1" step="0.1" inputmode="decimal" placeholder="30">
			</div>
			<div class="gr-calc-campo">
				<label for="gc-largo">Largo (cm)</label>
				<input type="number" id="gc-largo" min="1" step="0.1" inputmode="decimal" placeholder="40">
			</div>
			<div class="gr-calc-campo">
				<label for="gc-ancho">Ancho (cm)</label>
				<input type="number" id="gc-ancho" min="1" step="0.1" inputmode="decimal" placeholder="20">
			</div>
			<div class="gr-calc-campo">
				<label for="gc-peso">Peso real (kg)</label>
				<input type="number" id="gc-peso" min="0" step="0.1" inputmode="decimal" placeholder="4">
			</div>
		</div>

		<output class="gr-calc-salida" id="gc-salida" aria-live="polite">
			<p class="gr-calc-vacio">Completa las medidas y el peso para ver el resultado.</p>
		</output>

		<p class="gr-calc-nota"><?php echo wp_kses_post( $nota ); ?>
			<a href="<?php echo esc_url( $home . '/cotizar/' ); ?>">Cotizar con estos datos</a>.</p>
	</form>
</div></section>

<script>
(function () {
	var f = document.getElementById('gr-calc');
	if (!f) return;
	var ids = ['gc-alto', 'gc-largo', 'gc-ancho', 'gc-peso'];
	var out = document.getElementById('gc-salida');
	var num = function (id) { var v = parseFloat(document.getElementById(id).value.replace(',', '.')); return isFinite(v) && v > 0 ? v : 0; };
	var fmt = function (n) { return n.toFixed(n < 10 ? 2 : 1).replace('.', ','); };

	function calcular() {
		var a = num('gc-alto'), l = num('gc-largo'), an = num('gc-ancho'), p = num('gc-peso');
		if (!a || !l || !an) {
			out.innerHTML = '<p class="gr-calc-vacio">Completa las medidas y el peso para ver el resultado.</p>';
			return;
		}
		var vol = (a * l * an) / 5000;
		var cobra = Math.max(vol, p);
		var manda = vol >= p ? 'volumétrico' : 'real';
		var html = '<div class="gr-calc-res">'
			+ '<div class="gr-calc-dato' + (manda === 'volumétrico' ? ' is-manda' : '') + '"><span>Peso volumétrico</span><strong>' + fmt(vol) + ' kg</strong></div>'
			+ '<div class="gr-calc-dato' + (manda === 'real' ? ' is-manda' : '') + '"><span>Peso real</span><strong>' + (p ? fmt(p) + ' kg' : '—') + '</strong></div>'
			+ '<div class="gr-calc-dato gr-calc-dato--total"><span>Se cobra por</span><strong>' + fmt(cobra) + ' kg</strong></div>'
			+ '</div>';
		if (p) {
			html += '<p class="gr-calc-explica">Manda el peso <strong>' + manda + '</strong>: '
				+ (manda === 'volumétrico'
					? 'la caja ocupa más espacio del que pesa, así que estás pagando volumen. Si ajustas la caja al contenido, el precio baja.'
					: 'el contenido pesa más de lo que ocupa, así que la caja está bien aprovechada.')
				+ '</p>';
		}
		out.innerHTML = html;
	}

	ids.forEach(function (id) {
		var el = document.getElementById(id);
		el.addEventListener('input', calcular);
	});
	f.addEventListener('submit', function (e) { e.preventDefault(); calcular(); });
})();
</script>
	<?php
	return ob_get_clean();
}

/* Se imprime desde page.php. Solo en la ruta principal: en las de país la misma
 * herramienta saldría idéntica en diez URLs, y el cálculo no cambia con el
 * destino. Allí se enlaza a esta. */
function grenvios_hv_render( $slug ) {
	if ( is_admin() || ! in_array( $slug, grenvios_hv_paginas(), true ) || grenvios_hv_redundante( $slug ) ) return;
	$home = function_exists( 'grenvios_i18n_site_root' ) ? grenvios_i18n_site_root() : untrailingslashit( home_url() );

	if ( function_exists( 'grenvios_hq_pais' ) && grenvios_hq_pais() !== '' ) {
		echo '<section class="gr-calc gr-calc--nota padding-bottom"><div class="container"><p class="gr-calc-nota">'
			. 'El peso volumétrico se calcula igual para cualquier destino: alto × largo × ancho ÷ 5000. '
			. '<a href="' . esc_url( $home . '/servicios/peso-volumetrico/' ) . '">Usa la calculadora</a> y vuelve con el resultado.'
			. '</p></div></section>';
		return;
	}

	echo function_exists( 'grenvios_apply_media_overrides' ) ? grenvios_apply_media_overrides( grenvios_hv_html() ) : grenvios_hv_html();
}

/* Una pregunta más en la página, con la fórmula escrita: es la que Google
 * puede mostrar como respuesta directa. */
add_filter( 'grenvios_page_faqs', function ( $faqs, $slug ) {
	if ( ! in_array( $slug, grenvios_hv_paginas(), true ) ) return $faqs;
	if ( function_exists( 'grenvios_hq_pais' ) && grenvios_hq_pais() !== '' ) return $faqs;
	// La página del servicio ya trae esta pregunta: repetida, el FAQPage sale duplicado.
	foreach ( (array) $faqs as $fq ) {
		if ( isset( $fq[0] ) && stripos( (string) $fq[0], 'cómo se calcula el peso volumétrico' ) !== false ) return $faqs;
	}
	$faqs[] = array(
		'¿Cómo se calcula el peso volumétrico?',
		'Se multiplican alto × largo × ancho de la caja en centímetros y se divide entre 5000. El resultado está en kilos y se compara con el peso real: se cobra el mayor de los dos. Por ejemplo, una caja de 40 × 30 × 20 cm da 24 000 ÷ 5000 = 4,8 kg de peso volumétrico.',
	);
	return $faqs;
}, 27, 2 );
