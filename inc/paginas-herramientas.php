<?php
/**
 * Grenvíos — Páginas de herramienta y de contenido operativo.
 *
 * Son las páginas que responden a lo que la gente pregunta ANTES de cotizar, y
 * que ningún courier de la región publica bien:
 *
 *   /servicios/peso-volumetrico/   Cómo se cobra un envío + calculadora real
 *   /que-se-puede-enviar/          Qué está permitido y qué no, por vía y por país
 *   /servicios/envio-de-equipaje/  Equipaje, mudanza personal y compras
 *
 * Se renderizan por DATOS (como las páginas de destino), no por plantilla HTML:
 * así las tablas salen de la misma fuente que el resto del sitio (los destinos,
 * sus plazos y sus modalidades) y nunca se contradicen entre páginas ni quedan
 * desactualizadas cuando la clienta agrega un país.
 *
 * Todos los textos pasan por grenvios_field(), así que siguen siendo editables
 * desde el editor de página, y por grenvios_t() para el multiidioma.
 *
 * @package grenvios
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ══════════════════════════════════════
   1) REGISTRO DE LAS PÁGINAS
══════════════════════════════════════ */
add_filter( 'grenvios_pages', function ( $pages ) {
	$nuevas = array(
		'peso-volumetrico' => array(
			'title' => 'Peso Volumétrico',
			'seo'   => 'Peso Volumétrico: Cómo se Calcula tu Envío | Grenvíos',
			'desc'  => 'Calcula el peso volumétrico de tu envío con la fórmula alto × largo × ancho ÷ 5000 y descubre si pagas por peso real o por volumen. Calculadora gratuita.',
			'parent'=> 'servicios',
		),
		'que-se-puede-enviar' => array(
			'title' => 'Qué se Puede Enviar',
			'seo'   => 'Qué se Puede Enviar al Extranjero desde {{origen_pais}} | Grenvíos',
			'desc'  => 'Lista completa de lo que se puede y no se puede enviar al extranjero por vía aérea y terrestre desde {{origen_pais}}, con las restricciones de aduana de cada país.',
			'parent'=> '',
		),
		'envio-de-equipaje' => array(
			'title' => 'Envío de Equipaje',
			'seo'   => 'Enviar Equipaje al Extranjero desde {{origen_pais}} | Grenvíos',
			'desc'  => 'Envía tu equipaje, mudanza personal o compras hechas en {{origen_ciudad}} a tu país sin pagar exceso de equipaje en el avión. Cotiza por peso y volumen.',
			'parent'=> 'servicios',
		),
	);
	// Se insertan antes de "cotizar" para respetar el orden del menú.
	$out = array();
	foreach ( $pages as $slug => $def ) {
		if ( $slug === 'cotizar' ) foreach ( $nuevas as $k => $v ) $out[ $k ] = $v;
		$out[ $slug ] = $def;
	}
	foreach ( $nuevas as $k => $v ) if ( ! isset( $out[ $k ] ) ) $out[ $k ] = $v;
	return $out;
} );

/* Slugs que renderiza este módulo. */
function grenvios_tool_slugs() {
	return array( 'peso-volumetrico', 'que-se-puede-enviar', 'envio-de-equipaje' );
}

/* ══════════════════════════════════════
   2) ENRUTADOR (lo llama page.php)
══════════════════════════════════════ */
function grenvios_render_tool_page( $slug ) {
	if ( ! in_array( $slug, grenvios_tool_slugs(), true ) ) return false;

	switch ( $slug ) {
		case 'peso-volumetrico':    grenvios_render_peso_volumetrico(); return true;
		case 'que-se-puede-enviar': grenvios_render_que_enviar();       return true;
		case 'envio-de-equipaje':   grenvios_render_equipaje();         return true;
	}
	return false;
}

/* Otras páginas data-driven registradas por módulos (inc/paginas-contenido.php).
 * page.php pregunta por este filtro, no por una función concreta. */
add_filter( 'grenvios_render_page_early', function ( $hecho, $slug ) {
	return $hecho ? $hecho : grenvios_render_tool_page( $slug );
}, 10, 2 );

/* Atajo de lectura: texto editable con respaldo. */
function grenvios_tf( $key, $default ) {
	$v = function_exists( 'grenvios_field' ) ? grenvios_field( $key, '' ) : '';
	$v = ( $v !== '' ) ? $v : $default;
	return function_exists( 'grenvios_t' ) ? grenvios_t( $v ) : $v;
}

/* Banner superior común a las tres páginas. */
function grenvios_tool_banner( $eyebrow, $title ) {
	if ( function_exists( 'logisko_page_banner' ) ) {
		logisko_page_banner( grenvios_tf( 'tool_eyebrow', $eyebrow ), grenvios_tf( 'tool_title', $title ) );
	}
}

/* ══════════════════════════════════════
   3) PESO VOLUMÉTRICO (+ calculadora)
   El divisor 5000 es el estándar aéreo del sector; se deja editable porque
   cambia según la aerolínea y la modalidad.
══════════════════════════════════════ */
function grenvios_peso_divisor() {
	$d = (int) grenvios_tf( 'pv_divisor', '5000' );
	return $d > 0 ? $d : 5000;
}

function grenvios_render_peso_volumetrico() {
	$div = grenvios_peso_divisor();
	grenvios_tool_banner( 'Cómo se cobra tu envío', 'Peso volumétrico' );
	?>
	<section class="srv-intro padding-top"><div class="container">
		<div class="srv-lead text-center">
			<h2><?php echo esc_html( grenvios_tf( 'pv_h2', 'Se cobra el mayor entre el peso real y el peso volumétrico' ) ); ?></h2>
			<p><?php echo esc_html( grenvios_tf( 'pv_intro', 'Un paquete grande y liviano ocupa el mismo espacio en el avión que uno pequeño y pesado. Por eso, en todos los envíos internacionales el precio se calcula sobre el mayor de los dos pesos: el que marca la balanza y el que ocupa el paquete. Aquí puedes calcularlo tú mismo antes de cotizar.' ) ); ?></p>
		</div>
	</div></section>

	<section class="srv-section padding"><div class="container">
		<div class="srv-two-grid">
			<div class="srv-panel">
				<h3 class="srv-panel-title"><?php echo esc_html( grenvios_tf( 'pv_calc_title', 'Calcula tu envío' ) ); ?></h3>
				<p><?php echo esc_html( grenvios_tf( 'pv_calc_intro', 'Mide la caja ya cerrada, por su parte más ancha, y en centímetros.' ) ); ?></p>

				<div class="gr-pv-calc" data-div="<?php echo (int) $div; ?>">
					<div class="gr-pv-row">
						<label><?php echo esc_html( grenvios_tf( 'pv_l_alto', 'Alto (cm)' ) ); ?><input type="number" min="0" step="0.1" id="gr-pv-a"></label>
						<label><?php echo esc_html( grenvios_tf( 'pv_l_largo', 'Largo (cm)' ) ); ?><input type="number" min="0" step="0.1" id="gr-pv-l"></label>
						<label><?php echo esc_html( grenvios_tf( 'pv_l_ancho', 'Ancho (cm)' ) ); ?><input type="number" min="0" step="0.1" id="gr-pv-n"></label>
						<label><?php echo esc_html( grenvios_tf( 'pv_l_peso', 'Peso real (kg)' ) ); ?><input type="number" min="0" step="0.1" id="gr-pv-p"></label>
					</div>
					<div class="gr-pv-out" id="gr-pv-out" hidden>
						<p class="gr-pv-line"><span><?php echo esc_html( grenvios_tf( 'pv_r_vol', 'Peso volumétrico' ) ); ?></span><strong id="gr-pv-vol">—</strong></p>
						<p class="gr-pv-line"><span><?php echo esc_html( grenvios_tf( 'pv_r_real', 'Peso real' ) ); ?></span><strong id="gr-pv-real">—</strong></p>
						<p class="gr-pv-final"><span><?php echo esc_html( grenvios_tf( 'pv_r_cobra', 'Se cobra por' ) ); ?></span><strong id="gr-pv-final">—</strong></p>
						<p class="gr-pv-note" id="gr-pv-note"></p>
						<a class="default-btn" id="gr-pv-cta" href="<?php echo esc_url( grenvios_tool_url( 'cotizar' ) ); ?>"><?php echo esc_html( grenvios_tf( 'pv_r_btn', 'Cotizar este envío' ) ); ?></a>
					</div>
				</div>
			</div>

			<div class="srv-panel">
				<h3 class="srv-panel-title"><?php echo esc_html( grenvios_tf( 'pv_form_title', 'La fórmula' ) ); ?></h3>
				<p class="gr-pv-formula"><code>alto &times; largo &times; ancho &divide; <?php echo (int) $div; ?></code></p>
				<p><?php echo esc_html( grenvios_tf( 'pv_form_text', 'Las medidas van en centímetros y el resultado sale en kilos. El divisor depende de la modalidad: en vía aérea se usa 5000 y en vía terrestre puede variar; te lo confirmamos al cotizar.' ) ); ?></p>

				<h4><?php echo esc_html( grenvios_tf( 'pv_ej_title', 'Un ejemplo real' ) ); ?></h4>
				<p><?php echo esc_html( grenvios_tf( 'pv_ej_text', 'Una caja de 40 × 30 × 30 cm con ropa pesa 5 kg en la balanza. Su peso volumétrico es 40 × 30 × 30 ÷ 5000 = 7,2 kg. Como 7,2 es mayor que 5, el envío se cobra por 7,2 kg.' ) ); ?></p>
				<p><?php echo esc_html( grenvios_tf( 'pv_ej_tip', 'Consejo: si la caja te queda holgada, reducirla unos centímetros baja el peso volumétrico y por tanto el precio. Es el ajuste más rentable antes de enviar.' ) ); ?></p>
			</div>
		</div>
	</div></section>

	<section class="srv-section bg-grey padding"><div class="container">
		<div class="srv-head text-center">
			<h2><?php echo esc_html( grenvios_tf( 'pv_tabla_title', 'Ejemplos de cajas frecuentes' ) ); ?></h2>
			<p><?php echo esc_html( grenvios_tf( 'pv_tabla_intro', 'Medidas habituales y el peso por el que terminarías pagando.' ) ); ?></p>
		</div>
		<?php grenvios_render_tabla_pv( $div ); ?>
	</div></section>
	<?php
	grenvios_pv_assets();
}

/* Tabla de ejemplos: se calcula, no se escribe a mano (así nunca se contradice
 * con la calculadora ni con la fórmula). */
function grenvios_render_tabla_pv( $div ) {
	$cajas = array(
		array( 'Sobre / documentos',          32, 24,  2, 0.5 ),
		array( 'Caja pequeña (zapatos)',      35, 25, 15, 2.0 ),
		array( 'Caja mediana (ropa)',         40, 30, 30, 5.0 ),
		array( 'Caja grande (varios)',        60, 40, 40, 12.0 ),
		array( 'Maleta de viaje',             70, 45, 30, 20.0 ),
	);
	echo '<div class="gr-table-wrap"><table class="gr-table"><thead><tr>';
	foreach ( array( 'Tipo de bulto', 'Medidas (cm)', 'Peso real', 'Peso volumétrico', 'Se cobra por' ) as $th ) {
		echo '<th>' . esc_html( grenvios_t( $th ) ) . '</th>';
	}
	echo '</tr></thead><tbody>';
	foreach ( $cajas as $c ) {
		list( $nombre, $a, $l, $n, $peso ) = $c;
		$vol   = round( $a * $l * $n / $div, 1 );
		$cobra = max( $vol, $peso );
		$es_vol = $vol > $peso;
		printf(
			'<tr><td>%s</td><td>%d × %d × %d</td><td>%s kg</td><td>%s kg</td><td><strong>%s kg</strong> <span class="gr-tag">%s</span></td></tr>',
			esc_html( grenvios_t( $nombre ) ), $a, $l, $n,
			esc_html( number_format_i18n( $peso, 1 ) ),
			esc_html( number_format_i18n( $vol, 1 ) ),
			esc_html( number_format_i18n( $cobra, 1 ) ),
			esc_html( $es_vol ? grenvios_t( 'volumen' ) : grenvios_t( 'peso real' ) )
		);
	}
	echo '</tbody></table></div>';
}

/* Calculadora: sin librerías, sin peticiones. */
function grenvios_pv_assets() {
	$biz = function_exists( 'grenvios_biz' ) ? grenvios_biz() : array();
	$wa  = isset( $biz['wa_number'] ) ? preg_replace( '/\D/', '', (string) $biz['wa_number'] ) : '';
	?>
	<script>
	(function(){
		var box = document.querySelector('.gr-pv-calc');
		if(!box) return;
		var div = parseFloat(box.dataset.div) || 5000;
		var ids = ['gr-pv-a','gr-pv-l','gr-pv-n','gr-pv-p'].map(function(i){return document.getElementById(i);});
		var out = document.getElementById('gr-pv-out');
		var wa  = <?php echo wp_json_encode( $wa ); ?>;

		function fmt(n){ return (Math.round(n*10)/10).toLocaleString('es-PE',{minimumFractionDigits:1,maximumFractionDigits:1}); }

		function calc(){
			var a=parseFloat(ids[0].value)||0, l=parseFloat(ids[1].value)||0,
			    n=parseFloat(ids[2].value)||0, p=parseFloat(ids[3].value)||0;
			if(!(a&&l&&n)){ out.hidden = true; return; }
			var vol = a*l*n/div, cobra = Math.max(vol,p), esVol = vol > p;
			out.hidden = false;
			document.getElementById('gr-pv-vol').textContent  = fmt(vol)+' kg';
			document.getElementById('gr-pv-real').textContent = p ? fmt(p)+' kg' : '—';
			document.getElementById('gr-pv-final').textContent= fmt(cobra)+' kg';
			document.getElementById('gr-pv-note').textContent = !p
				? <?php echo wp_json_encode( grenvios_t( 'Ingresa el peso real para saber cuál de los dos manda.' ) ); ?>
				: (esVol
					? <?php echo wp_json_encode( grenvios_t( 'Tu paquete ocupa más de lo que pesa: se cobra por volumen. Reducir la caja bajaría el precio.' ) ); ?>
					: <?php echo wp_json_encode( grenvios_t( 'Tu paquete pesa más de lo que ocupa: se cobra por peso real.' ) ); ?>);

			var cta = document.getElementById('gr-pv-cta');
			if(wa){
				var msg = <?php echo wp_json_encode( grenvios_t( 'Hola, quiero cotizar un envío de' ) ); ?>
					+' '+fmt(cobra)+' kg ('+a+'x'+l+'x'+n+' cm, '+(p?fmt(p):'?')+' kg reales).';
				cta.href = 'https://wa.me/'+wa+'?text='+encodeURIComponent(msg);
				cta.rel = 'nofollow noopener'; cta.target = '_blank';
			}
		}
		ids.forEach(function(el){ el.addEventListener('input', calc); });
	})();
	</script>
	<?php
}

/* ══════════════════════════════════════
   4) QUÉ SE PUEDE ENVIAR
   Las restricciones por país salen de los datos de destinos: si la clienta
   agrega un país, aparece aquí solo.
══════════════════════════════════════ */
function grenvios_restricciones_via() {
	return array(
		'aereo' => array(
			'label'   => 'Vía aérea',
			'si'      => array( 'Documentos, títulos y pasaportes', 'Ropa y calzado', 'Artesanía y regalos', 'Repuestos y piezas sin batería', 'Libros y material impreso', 'Muestras comerciales' ),
			'no'      => array( 'Líquidos, cremas y aerosoles', 'Alimentos y productos de consumo', 'Objetos con batería interna (celulares, laptops, parlantes)', 'Televisores y pantallas', 'Dinero, tarjetas y cheques', 'Medicinas (salvo Cuba, con receta médica)' ),
		),
		'terrestre' => array(
			'label'   => 'Vía terrestre',
			'si'      => array( 'Todo lo permitido en vía aérea', 'Líquidos y cremas selladas', 'Alimentos sellados no perecibles', 'Productos de consumo sellados', 'Objetos con batería interna' ),
			'no'      => array( 'Dinero, tarjetas y cheques', 'Productos perecibles o refrigerados', 'Material inflamable o peligroso', 'Especies protegidas y productos restringidos por aduana' ),
		),
	);
}

function grenvios_render_que_enviar() {
	grenvios_tool_banner( 'Antes de enviar', 'Qué se puede enviar al extranjero' );
	?>
	<section class="srv-intro padding-top"><div class="container">
		<div class="srv-lead text-center">
			<h2><?php echo esc_html( grenvios_tf( 'qe_h2', 'Lo que puedes enviar depende de la vía y del país de destino' ) ); ?></h2>
			<p><?php echo esc_html( grenvios_tf( 'qe_intro', 'La mayoría de los envíos que se quedan retenidos no fallan por el transporte, sino por llevar algo que esa vía o esa aduana no admite. Esta es la lista con la que trabajamos; si tienes dudas con un artículo concreto, consúltanos antes de armar el paquete.' ) ); ?></p>
		</div>
	</div></section>

	<section class="srv-section padding"><div class="container"><div class="srv-two-grid">
	<?php foreach ( grenvios_restricciones_via() as $key => $via ) : ?>
		<div class="srv-panel">
			<h3 class="srv-panel-title"><?php echo esc_html( grenvios_t( $via['label'] ) ); ?></h3>
			<h4 class="gr-ok-title"><?php echo esc_html( grenvios_t( 'Sí se puede enviar' ) ); ?></h4>
			<ul class="check-list">
				<?php foreach ( $via['si'] as $i ) : ?>
					<li><i class="fa-solid fa-check"></i><?php echo esc_html( grenvios_t( $i ) ); ?></li>
				<?php endforeach; ?>
			</ul>
			<h4 class="gr-no-title"><?php echo esc_html( grenvios_t( 'No se puede enviar' ) ); ?></h4>
			<ul class="check-list gr-no-list">
				<?php foreach ( $via['no'] as $i ) : ?>
					<li><i class="fa-solid fa-xmark"></i><?php echo esc_html( grenvios_t( $i ) ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endforeach; ?>
	</div></div></section>

	<section class="srv-section bg-grey padding"><div class="container">
		<div class="srv-head text-center">
			<h2><?php echo esc_html( grenvios_tf( 'qe_pais_title', 'Restricciones por país de destino' ) ); ?></h2>
			<p><?php echo esc_html( grenvios_tf( 'qe_pais_intro', 'Cada aduana tiene sus propias reglas y sus propios plazos. Estos son los países con página propia; entra en el tuyo para ver el detalle.' ) ); ?></p>
		</div>
		<?php grenvios_render_tabla_destinos(); ?>
	</div></section>
	<?php
}

/* ══════════════════════════════════════
   5) TABLA DE PAÍSES (reutilizable)
   Fuente única: grenvios_destinos(). Sirve en "qué se puede enviar" y en el
   hub /destinos/, sin duplicar contenido: son datos, no texto repetido.
══════════════════════════════════════ */
function grenvios_render_tabla_destinos() {
	$dest = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();
	if ( empty( $dest ) ) return;

	echo '<div class="gr-table-wrap"><table class="gr-table"><thead><tr>';
	foreach ( array( 'País', 'Tiempo estimado', 'Modalidades', 'Entrega', 'Restricciones' ) as $th ) {
		echo '<th>' . esc_html( grenvios_t( $th ) ) . '</th>';
	}
	echo '</tr></thead><tbody>';

	foreach ( $dest as $slug => $d ) {
		// Mismo filtro que el menú: si ese país tiene ruta propia, la fila de la
		// tabla lleva a su ruta y el usuario no sale de la que está viendo.
		$url = apply_filters( 'grenvios_destino_permalink', home_url( '/destinos/' . $slug . '/' ), $slug );
		if ( function_exists( 'grenvios_i18n_localize_url' ) ) $url = grenvios_i18n_localize_url( $url );
		printf(
			'<tr><td><a href="%s"><strong>%s</strong></a></td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
			esc_url( $url ),
			esc_html( isset( $d['title'] ) ? $d['title'] : $slug ),
			esc_html( isset( $d['tiempo'] ) ? $d['tiempo'] : '' ),
			esc_html( isset( $d['modos'] ) ? $d['modos'] : '' ),
			esc_html( isset( $d['entrega'] ) ? $d['entrega'] : '' ),
			esc_html( isset( $d['restr'] ) ? wp_trim_words( $d['restr'], 14 ) : '' )
		);
	}
	echo '</tbody></table></div>';
}

/* ══════════════════════════════════════
   6) ENVÍO DE EQUIPAJE
   Las listas viven en funciones (y no dentro del HTML) para que el motor de
   traducción pueda recogerlas sin esperar a que alguien visite la página en
   ese idioma. Ver `grenvios_i18n_extra_strings`.
══════════════════════════════════════ */
function grenvios_equipaje_casos() {
	return array(
		'Migrantes que vuelven a su país y no quieren pagar exceso de equipaje',
		'Estudiantes que terminan un intercambio en {{origen_ciudad}}',
		'Turistas que compraron más de lo que cabe en la maleta',
		'Mudanzas personales: ropa, libros, menaje y objetos personales',
		'Compras hechas en {{origen_ciudad}} que recibimos y despachamos por ti',
	);
}

function grenvios_equipaje_pasos() {
	return array(
		'Nos dices qué envías, cuánto pesa y a qué país va',
		'Recogemos en tu domicilio en {{origen_ciudad}} o lo traes a la oficina',
		'Pesamos y medimos: se cobra el mayor entre peso real y volumétrico',
		'Pagas en {{origen_ciudad}} antes del despacho; en destino solo se retira',
		'Te confirmamos el estado del envío hasta la entrega',
	);
}
function grenvios_render_equipaje() {
	grenvios_tool_banner( 'Equipaje y mudanza personal', 'Envío de equipaje al extranjero' );
	?>
	<section class="srv-intro padding-top"><div class="container">
		<div class="srv-lead text-center">
			<h2><?php echo esc_html( grenvios_tf( 'eq_h2', 'Manda tu equipaje por separado y viaja ligero' ) ); ?></h2>
			<p><?php echo esc_html( grenvios_tf( 'eq_intro', 'El exceso de equipaje en el avión suele costar más que enviar esa misma maleta por courier. Si te mudas, vuelves a tu país o compraste más de lo que cabe en tu vuelo, nosotros lo despachamos desde {{origen_ciudad}} y lo recibes en destino.' ) ); ?></p>
		</div>
	</div></section>

	<section class="srv-section padding"><div class="container"><div class="srv-two-grid">
		<div class="srv-panel">
			<h3 class="srv-panel-title"><?php echo esc_html( grenvios_tf( 'eq_casos_title', 'Para quién es' ) ); ?></h3>
			<ul class="check-list">
				<?php foreach ( grenvios_equipaje_casos() as $i ) : ?>
					<li><i class="fa-solid fa-check"></i><?php echo esc_html( grenvios_t( $i ) ); ?></li>
				<?php endforeach; ?>
			</ul>
		</div>
		<div class="srv-panel">
			<h3 class="srv-panel-title"><?php echo esc_html( grenvios_tf( 'eq_como_title', 'Cómo funciona' ) ); ?></h3>
			<ul class="check-list">
				<?php foreach ( grenvios_equipaje_pasos() as $i ) : ?>
					<li><i class="fa-solid fa-arrow-right"></i><?php echo esc_html( grenvios_t( $i ) ); ?></li>
				<?php endforeach; ?>
			</ul>
			<p><?php echo esc_html( grenvios_tf( 'eq_nota', 'El equipaje personal usado suele tener un trato aduanero distinto al de la mercancía nueva. Te indicamos qué declarar en cada caso.' ) ); ?></p>
			<a class="default-btn" href="<?php echo esc_url( grenvios_tool_url( 'peso-volumetrico' ) ); ?>"><?php echo esc_html( grenvios_t( 'Calcular el peso de mi equipaje' ) ); ?></a>
		</div>
	</div></div></section>

	<section class="srv-section bg-grey padding"><div class="container">
		<div class="srv-head text-center">
			<h2><?php echo esc_html( grenvios_tf( 'eq_paises_title', 'A dónde enviamos equipaje' ) ); ?></h2>
		</div>
		<?php grenvios_render_tabla_destinos(); ?>
	</div></section>
	<?php
}

/* URL interna resuelta al idioma activo. */
function grenvios_tool_url( $slug ) {
	$page = get_page_by_path( $slug );
	if ( ! $page ) $page = get_page_by_path( 'servicios/' . $slug );
	$url = $page ? get_permalink( $page ) : home_url( '/' . $slug . '/' );
	if ( function_exists( 'grenvios_i18n_localize_url' ) ) $url = grenvios_i18n_localize_url( $url );
	return $url;
}

/* ══════════════════════════════════════
   7) ESTILOS
══════════════════════════════════════ */
add_action( 'wp_head', function () {
	if ( is_admin() ) return;
	echo '<style id="grenvios-tools-css">'
		. '.gr-table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}'
		. '.gr-table{width:100%;border-collapse:collapse;min-width:620px;background:#fff}'
		. '.gr-table th,.gr-table td{padding:11px 14px;border-bottom:1px solid rgba(0,0,0,.09);text-align:left;font-size:15px;vertical-align:top}'
		. '.gr-table th{font-size:13px;text-transform:uppercase;letter-spacing:.4px;opacity:.7}'
		. '.gr-table tbody tr:hover{background:rgba(0,0,0,.02)}'
		. '.gr-tag{font-size:11px;padding:1px 7px;border-radius:9px;background:rgba(0,0,0,.07);white-space:nowrap}'
		. '.gr-pv-row{display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:16px}'
		. '.gr-pv-row label{display:flex;flex-direction:column;gap:5px;font-size:14px;font-weight:600}'
		. '.gr-pv-row input{padding:9px 11px;border:1px solid rgba(0,0,0,.18);border-radius:6px;font-size:16px}'
		. '.gr-pv-out{padding:16px 18px;border-radius:8px;background:rgba(0,0,0,.045)}'
		. '.gr-pv-line{display:flex;justify-content:space-between;margin:0 0 6px;font-size:15px}'
		. '.gr-pv-final{display:flex;justify-content:space-between;margin:10px 0 6px;padding-top:10px;border-top:1px solid rgba(0,0,0,.12);font-size:19px}'
		. '.gr-pv-note{font-size:14px;opacity:.8;margin:0 0 14px}'
		. '.gr-pv-formula code{font-size:17px;padding:8px 12px;display:inline-block;background:rgba(0,0,0,.06);border-radius:6px}'
		. '.gr-ok-title{margin-top:18px}.gr-no-title{margin-top:22px}'
		. '.gr-no-list li i{opacity:.55}'
		. '@media(max-width:575px){.gr-pv-row{grid-template-columns:1fr}}'
		. '</style>';
}, 104 );

/* ══════════════════════════════════════
   TEXTOS DE ESTE MÓDULO PARA EL TRADUCTOR
   Sin esto, los textos que genera el PHP solo se traducirían después de que
   alguien visitara la página en ese idioma (que es cuando se detectan como
   "faltantes"). Declarándolos, «Traducir todo» los deja listos de una vez.
══════════════════════════════════════ */
add_filter( 'grenvios_i18n_extra_strings', function ( $textos ) {
	foreach ( grenvios_restricciones_via() as $via ) {
		$textos[] = $via['label'];
		foreach ( $via['si'] as $t ) $textos[] = $t;
		foreach ( $via['no'] as $t ) $textos[] = $t;
	}
	foreach ( grenvios_equipaje_casos() as $t ) $textos[] = $t;
	foreach ( grenvios_equipaje_pasos() as $t ) $textos[] = $t;

	foreach ( array(
		'Tipo de bulto', 'Medidas (cm)', 'Peso real', 'Peso volumétrico', 'Se cobra por',
		'volumen', 'peso real', 'País', 'Tiempo estimado', 'Modalidades', 'Entrega', 'Restricciones',
		'Sobre / documentos', 'Caja pequeña (zapatos)', 'Caja mediana (ropa)', 'Caja grande (varios)', 'Maleta de viaje',
		'Sí se puede enviar', 'No se puede enviar', 'Calcular el peso de mi equipaje',
		'Ingresa el peso real para saber cuál de los dos manda.',
		'Tu paquete ocupa más de lo que pesa: se cobra por volumen. Reducir la caja bajaría el precio.',
		'Tu paquete pesa más de lo que ocupa: se cobra por peso real.',
		'Hola, quiero cotizar un envío de',
	) as $t ) $textos[] = $t;

	return $textos;
} );
