<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Editor en el front: cobertura de TODAS las páginas
 * ══════════════════════════════════════════════════════════════════════════
 *
 * El botón «Editar página» solo aparece si la página tiene registro de campos
 * (inc/customizer.php) o FAQ propia. Faltaban:
 *
 *   · Aduanas e impuestos, Envío de alimentos, Seguro de envíos y Envíos desde
 *     provincias (y sus copias en cada ruta): se pintan por PHP y leían sus
 *     textos con grenvios_tf(), pero nadie los había registrado.
 *   · Artículos por país (y sus espejos).
 *   · La portada de cada ruta de país ofrecía los campos de la home, aunque se
 *     sirve con la plantilla de su destino.
 *   · Los datos del país que alimentan las secciones de destino y el bloque de
 *     cada página de la ruta solo se editaban en el escritorio.
 *
 * Los valores por defecto de los registros de abajo son exactamente los textos
 * que el render usa cuando el campo está vacío.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ─────────────────────────────────────────────────────────────────────────
 * 1) Registro de campos de las páginas que no lo tenían
 * ───────────────────────────────────────────────────────────────────────── */
add_filter( 'grenvios_text_registry', function ( $reg ) {
	$extra = array(
		'aduanas-e-impuestos' => array( 'label' => 'Aduanas', 'priority' => 60, 'sections' => array(
			'hero' => array( 'label' => 'Aduanas · Banner', 'fields' => array(
				'tool_eyebrow' => array( 'Frase superior', 'text', 'Antes de enviar' ),
				'tool_title' => array( 'Título H1', 'text', 'Aduanas e impuestos en envíos internacionales' ),
			) ),
			's1' => array( 'label' => 'Aduanas · El impuesto no lo cobra el courier: lo cobra el país que recibe', 'fields' => array(
				'ad_h2' => array( 'Título', 'text', 'El impuesto no lo cobra el courier: lo cobra el país que recibe' ),
				'ad_intro' => array( 'Texto', 'textarea', 'Esta es la parte que más confunde y la que más reclamos genera cuando nadie la explica a tiempo. El precio que pagas por el envío cubre el transporte. Lo que la aduana del país de destino decida cobrar es aparte, lo fija ese país y lo paga normalmente quien recibe el paquete, no quien lo envía. Saberlo antes evita la llamada incómoda de tu familiar preguntando por qué le piden dinero para retirar tu regalo.' ),
			) ),
			's2' => array( 'label' => 'Aduanas · De qué depende lo que te cobren', 'fields' => array(
				'ad_como_title' => array( 'Título', 'text', 'De qué depende lo que te cobren' ),
				'ad_valor_title' => array( 'Título', 'text', 'El valor declarado' ),
				'ad_valor_text' => array( 'Texto', 'textarea', 'Es el precio real de lo que va dentro, y es la base sobre la que se calcula todo. Declararlo por debajo para «ahorrar» es la causa número uno de que un paquete quede retenido: si la aduana no se cree el valor, abre, tasa por su cuenta y suma multa. Declara lo que cuesta y adjunta la boleta.' ),
				'ad_minimis_title' => array( 'Título', 'text', 'El mínimo exento de cada país' ),
				'ad_minimis_text' => array( 'Texto', 'textarea', 'Casi todos los países dejan entrar envíos sin cobrar impuestos por debajo de cierto valor. Por encima de ese umbral se paga, y el umbral es distinto en cada país y cambia con el tiempo. Por eso aquí no publicamos cifras: te confirmamos la vigente para tu destino al cotizar.' ),
				'ad_tipo_title' => array( 'Título', 'text', 'Qué es lo que envías' ),
				'ad_tipo_text' => array( 'Texto', 'textarea', 'No paga igual un documento que ropa, electrónica o alimentos. Algunas categorías tienen permisos propios —sanitarios, por ejemplo— y otras están directamente prohibidas por vía aérea. Describir bien el contenido acelera el despacho.' ),
				'ad_uso_title' => array( 'Título', 'text', 'Regalo o uso comercial' ),
				'ad_uso_text' => array( 'Texto', 'textarea', 'Un envío entre familiares y uno con fines de venta no se tratan igual. Enviar diez unidades del mismo producto se lee como comercial aunque sea un regalo, y ahí entran requisitos de importador. Si es tu caso, dilo antes: hay una forma correcta de hacerlo.' ),
			) ),
			's3' => array( 'label' => 'Aduanas · Restricciones vigentes por país', 'fields' => array(
				'ad_paises_title' => array( 'Título', 'text', 'Restricciones vigentes por país' ),
				'ad_paises_text' => array( 'Texto', 'textarea', 'Lo que cada destino exige y lo que no deja pasar. Entra en el país que te interesa para ver el detalle completo y los plazos.' ),
			) ),
			's4' => array( 'label' => 'Aduanas · Por qué se retiene un paquete', 'fields' => array(
				'ad_retencion_title' => array( 'Título', 'text', 'Por qué se retiene un paquete' ),
				'ad_retencion_text' => array( 'Texto', 'textarea', 'Casi siempre por una de estas cinco razones, y las cinco se evitan antes de despachar:' ),
				'ad_lista1' => array( 'Lista (una línea por punto)', 'textarea', "El valor declarado no coincide con lo que la aduana estima que vale el contenido.\nLa descripción es genérica: «regalo», «varios» o «muestras» no le dicen nada al inspector.\nFalta la boleta o factura que respalde el valor declarado.\nEl contenido necesita un permiso que no se adjuntó (sanitario, farmacéutico, cultural).\nQuien recibe no responde a tiempo cuando la aduana le pide un dato o el pago del impuesto." ),
				'ad_nota' => array( 'Nota', 'textarea', 'Si un envío nuestro queda retenido, te avisamos y te acompañamos en el trámite: te decimos exactamente qué pide la aduana de ese país y quién debe presentarlo. No te dejamos resolverlo solo con un número de guía.' ),
			) ),
		) ),
		'envio-de-alimentos' => array( 'label' => 'Alimentos', 'priority' => 60, 'sections' => array(
			'hero' => array( 'label' => 'Alimentos · Banner', 'fields' => array(
				'tool_eyebrow' => array( 'Frase superior', 'text', 'Un pedazo de casa' ),
				'tool_title' => array( 'Título H1', 'text', 'Envío de alimentos al extranjero desde {{origen_pais}}' ),
			) ),
			's1' => array( 'label' => 'Alimentos · Sí se puede enviar comida, pero no toda ni por cualquier vía', 'fields' => array(
				'al_h2' => array( 'Título', 'text', 'Sí se puede enviar comida, pero no toda ni por cualquier vía' ),
				'al_intro' => array( 'Texto', 'textarea', 'Es de lo que más nos piden, sobre todo en campaña navideña: panetón, café de Chanchamayo, cacao, ají en pasta, galletas, snacks. La regla que ordena todo es simple: cuanto más procesado y sellado de fábrica esté el producto, más fácil viaja. Lo fresco, lo casero y lo que lleva líquido es lo que da problemas.' ),
			) ),
			's2' => array( 'label' => 'Alimentos · Lo que normalmente sí viaja', 'fields' => array(
				'al_si_title' => array( 'Título', 'text', 'Lo que normalmente sí viaja' ),
				'al_lista1' => array( 'Lista (una línea por punto)', 'textarea', "Panetón y productos de panadería sellados de fábrica.\nCafé y cacao en grano o molido, en su empaque original.\nSnacks, galletas, caramelos y chocolates industriales.\nAjí, especias y condimentos secos, envasados y etiquetados.\nConservas y productos enlatados con fecha de vencimiento visible." ),
				'al_no_title' => array( 'Título', 'text', 'Lo que casi nunca pasa la aduana' ),
				'al_lista2' => array( 'Lista (una línea por punto)', 'textarea', "Carne, embutidos, lácteos y pescado, frescos o curados.\nFrutas y verduras frescas, y semillas sin certificado.\nComida preparada en casa o sin etiqueta de fabricante.\nLíquidos y pastas en envase abierto o sin sellar.\nProductos sin fecha de vencimiento ni lista de ingredientes." ),
				'al_nota_pais' => array( 'Nota', 'textarea', 'Ojo: esto es la norma general. Hay países especialmente estrictos con el origen animal y vegetal, y lo que entra sin problema a uno puede ser decomisado en el de al lado. Antes de comprar el producto, consúltanos por tu destino concreto.' ),
			) ),
			's3' => array( 'label' => 'Alimentos · La vía cambia lo que puedes mandar', 'fields' => array(
				'al_via_title' => array( 'Título', 'text', 'La vía cambia lo que puedes mandar' ),
				'al_aereo_title' => array( 'Título', 'text', 'Aéreo' ),
				'al_aereo_text' => array( 'Texto', 'textarea', 'Llega rápido y es la única opción fuera de Sudamérica, pero es la vía más restrictiva con los alimentos: nada de líquidos ni pastas, y control estricto del origen animal o vegetal. Ideal para seco, sellado y liviano.' ),
				'al_terrestre_title' => array( 'Título', 'text', 'Terrestre' ),
				'al_terrestre_text' => array( 'Texto', 'textarea', 'Solo llega a países de la región y tarda más, pero admite bastante más: ají en pasta, conservas y envases con líquido sellados de fábrica. Para una caja grande de productos peruanos suele salir mucho más a cuenta.' ),
				'al_nota_via' => array( 'Nota', 'textarea', 'Si tu destino admite las dos vías, te decimos con cuál llega tu producto concreto antes de que pagues.' ),
			) ),
			's4' => array( 'label' => 'Alimentos · Cómo embalarlo para que llegue entero', 'fields' => array(
				'al_emb_title' => array( 'Título', 'text', 'Cómo embalarlo para que llegue entero' ),
				'al_lista3' => array( 'Lista (una línea por punto)', 'textarea', "Deja los productos en su envase original: la etiqueta del fabricante es lo que la aduana lee.\nComprueba que ninguna fecha de vencimiento caiga dentro del plazo de tránsito.\nRefuerza con film los envases que puedan abrirse en el traslado.\nSepara el panetón de productos pesados: llega aplastado con facilidad.\nAdjunta la boleta de compra y una lista del contenido con su valor." ),
				'al_nota_camp' => array( 'Nota', 'textarea', 'En campaña navideña conviene despachar con margen: el volumen se multiplica y la aduana revisa más. Un envío de panetón puesto en camino a finales de noviembre llega tranquilo; el mismo envío a mediados de diciembre, ya no siempre.' ),
			) ),
		) ),
		'seguro-de-envios' => array( 'label' => 'Seguro', 'priority' => 60, 'sections' => array(
			'hero' => array( 'label' => 'Seguro · Banner', 'fields' => array(
				'tool_eyebrow' => array( 'Frase superior', 'text', 'Tranquilidad' ),
				'tool_title' => array( 'Título H1', 'text', 'Seguro para envíos internacionales' ),
			) ),
			's1' => array( 'label' => 'Seguro · Todo envío viaja con una cobertura básica; el valor declarado decide el resto', 'fields' => array(
				'sg_h2' => array( 'Título', 'text', 'Todo envío viaja con una cobertura básica; el valor declarado decide el resto' ),
				'sg_intro' => array( 'Texto', 'textarea', 'La pregunta llega siempre y merece una respuesta clara, no letra pequeña. Cada envío que despachamos tiene una cobertura mínima por extravío. Esa cobertura se calcula sobre el valor que declaraste, así que un paquete declarado por menos de lo que vale está, de hecho, asegurado por menos de lo que vale. Si dentro va algo importante, conviene declarar el valor real y contratar cobertura ampliada.' ),
			) ),
			's2' => array( 'label' => 'Seguro · Qué cubre', 'fields' => array(
				'sg_cubre_title' => array( 'Título', 'text', 'Qué cubre' ),
				'sg_lista1' => array( 'Lista (una línea por punto)', 'textarea', "Extravío total del envío durante el transporte.\nDaño atribuible a la manipulación, con el embalaje en regla.\nSustracción de contenido acreditada en destino." ),
				'sg_nocubre_title' => array( 'Título', 'text', 'Qué no cubre' ),
				'sg_lista2' => array( 'Lista (una línea por punto)', 'textarea', "Retención, decomiso o destrucción decididos por la aduana.\nDaño por embalaje insuficiente o por enviar algo no permitido.\nDeterioro propio del producto, como un alimento que vence en tránsito.\nLucro cesante o daños indirectos: se cubre el bien, no lo que dejaste de ganar." ),
			) ),
			's3' => array( 'label' => 'Seguro · Si algo salió mal, esto es lo que hay que hacer', 'fields' => array(
				'sg_pasos_title' => array( 'Título', 'text', 'Si algo salió mal, esto es lo que hay que hacer' ),
				'sg_pasos_text' => array( 'Texto', 'textarea', 'Los plazos de reclamo son cortos y empiezan a correr desde la entrega o desde la fecha estimada. Avísanos cuanto antes, aunque todavía no tengas todos los papeles.' ),
				'sg_lista3' => array( 'Lista (una línea por punto)', 'textarea', "Escríbenos con tu número de guía y una descripción de lo ocurrido.\nSi el paquete llegó dañado, fotografía la caja ANTES de abrirla y también el contenido.\nNo deseches el embalaje: es la prueba principal de cómo viajó.\nReúne la boleta de compra y el detalle del valor declarado.\nAbrimos el reclamo con el operador y te acompañamos hasta la resolución." ),
				'sg_nota' => array( 'Nota', 'textarea', 'Un consejo que ahorra disgustos: fotografía el contenido y la caja cerrada antes de entregarnos el paquete. Son treinta segundos y resuelven la mitad de los reclamos.' ),
			) ),
		) ),
		'envios-desde-provincias' => array( 'label' => 'Provincias', 'priority' => 60, 'sections' => array(
			'hero' => array( 'label' => 'Provincias · Banner', 'fields' => array(
				'tool_eyebrow' => array( 'Frase superior', 'text', 'Todo {{origen_pais}}' ),
				'tool_title' => array( 'Título H1', 'text', 'Envíos internacionales desde provincias' ),
			) ),
			's1' => array( 'label' => 'Provincias · No hace falta que viajes a {{origen_ciudad}} para enviar al extranjero', 'fields' => array(
				'pv_h2' => array( 'Título', 'text', 'No hace falta que viajes a {{origen_ciudad}} para enviar al extranjero' ),
				'pv_intro' => array( 'Texto', 'textarea', 'Todo envío internacional desde {{origen_pais}} sale por Lima: ahí están la aduana de salida y los vuelos de carga. Eso no significa que tengas que venir. Nos haces llegar el paquete por transporte nacional o agencia de bus, y desde nuestro almacén en {{origen_ciudad}} sigue el mismo circuito y las mismas tarifas que cualquier envío limeño.' ),
			) ),
			/* (Las ciudades de origen las edita la sección «Cobertura», inc/cobertura-origen.php.) */
			's3' => array( 'label' => 'Provincias · Cómo funciona', 'fields' => array(
				'pv_pasos_title' => array( 'Título', 'text', 'Cómo funciona' ),
				'pv_lista2' => array( 'Lista (una línea por punto)', 'textarea', "Cotizas por WhatsApp indicando desde qué ciudad envías y a qué país va.\nTe damos la dirección de nuestro almacén en {{origen_ciudad}} y los datos de remitente.\nDespachas el paquete por la agencia de transporte o de bus que prefieras.\nAl recibirlo lo revisamos, lo pesamos y te confirmamos el costo final.\nLo despachamos al extranjero y sigues el envío con tu número de guía." ),
				'pv_nota_plazo' => array( 'Nota', 'textarea', 'Suma al plazo internacional el tiempo del tramo nacional hasta {{origen_ciudad}}: normalmente entre uno y tres días según la ciudad. Si tienes fecha límite, dínoslo al cotizar y calculamos hacia atrás desde ella.' ),
			) ),
			's4' => array( 'label' => 'Provincias · Para que el tramo a {{origen_ciudad}} no te dé problemas', 'fields' => array(
				'pv_tips_title' => array( 'Título', 'text', 'Para que el tramo a {{origen_ciudad}} no te dé problemas' ),
				'pv_lista3' => array( 'Lista (una línea por punto)', 'textarea', "Embala pensando en dos viajes, no en uno: el paquete hará el trayecto nacional y después el internacional.\nRotula la caja con tu nombre y tu número de WhatsApp, no solo con el nuestro.\nEnvíanos la guía del transporte nacional en cuanto la tengas, para estar pendientes.\nMete la boleta de compra dentro de un sobre pegado a la caja: la necesitaremos para la declaración.\nSi envías alimentos, revisa antes que la vía de tu destino los admita." ),
			) ),
		) ),
		'articulos-por-pais' => array( 'label' => 'Artículos por país', 'priority' => 61, 'sections' => array(
			'hero' => array( 'label' => 'Artículos por país · Banner', 'fields' => array(
				'hub_eyebrow' => array( 'Frase superior', 'text', 'Guías de envío' ),
				'hub_title'   => array( 'Título H1', 'html', 'Guías de envío por <span>país de destino</span>' ),
			) ),
			'listado' => array( 'label' => 'Artículos por país · Listado', 'fields' => array(
				'hub_intro' => array( 'Texto de introducción', 'textarea', 'Cada destino tiene sus propias reglas de aduana, sus plazos y su lista de lo que no se puede enviar. Elige el país al que envías y lee solo lo que te afecta.' ),
				'hub_todos' => array( 'Botón del filtro · todos', 'text', 'Todos los países' ),
				'hub_vacio' => array( 'Mensaje si un país no tiene guías', 'text', 'Todavía no hay guías para ese país.' ),
			) ),
		) ),
	);
	foreach ( $extra as $slug => $def ) {
		if ( ! isset( $reg[ $slug ] ) ) { $reg[ $slug ] = $def; continue; }
		/* Otro módulo (inc/paginas-contenido-seo.php) ya registró la página con
		 * sus secciones SEO. Saltarla dejaba sin editar el banner y el cuerpo de
		 * la versión de Perú: se suman, estas primero (es el orden de la página). */
		$reg[ $slug ]['sections'] = array_merge( $def['sections'], (array) $reg[ $slug ]['sections'] );
	}
	return $reg;
} );

/* ─────────────────────────────────────────────────────────────────────────
 * 2) La portada de una ruta edita los campos de su destino
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_editor_destino_de_ruta() {
	if ( ! function_exists( 'grenvios_es_ruta_pais' ) || ! function_exists( 'grenvios_sede_destino_propio' ) ) return '';
	$lang = function_exists( 'grenvios_i18n_current' ) ? grenvios_i18n_current() : '';
	if ( ! $lang || ! grenvios_es_ruta_pais( $lang ) || grenvios_i18n_default() === $lang ) return '';
	$d    = grenvios_sede_destino_propio( $lang );
	$dest = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();
	return ( $d !== '' && isset( $dest[ $d ] ) ) ? $d : '';
}

/* (La home de cada ruta es una home: el panel ofrece los campos de la home.) */

/* ─────────────────────────────────────────────────────────────────────────
 * 3) «Datos de este país» en el panel
 *
 * Son los seis campos de Destinos → Contenido por país. No se guardan en la
 * página sino en el país, así que el mismo dato sirve para su ficha, su portada
 * de ruta y las 24 páginas de la ruta.
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_editor_pais_slug( $slug ) {
	$dest = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();
	if ( isset( $dest[ $slug ] ) ) return $slug;
	return grenvios_editor_destino_de_ruta();
}

add_action( 'grenvios_editor_secciones', function ( $slug ) {
	if ( ! function_exists( 'grenvios_pais_campos' ) || ! current_user_can( 'edit_pages' ) ) return;
	$ps = grenvios_editor_pais_slug( $slug );
	if ( $ps === '' ) return;
	$dest   = grenvios_destinos();
	$nombre = isset( $dest[ $ps ]['title'] ) ? $dest[ $ps ]['title'] : $ps;
	$all    = grenvios_pais_extra_all();
	$row    = isset( $all[ $ps ] ) ? (array) $all[ $ps ] : array();
	?>
    <div class="nep-accordion" data-sel=".gr-pais-sec">
      <button class="nep-acc-header" type="button"><span><?php echo esc_html( 'Datos de ' . $nombre . ' (todas sus páginas)' ); ?></span><i class="fa-solid fa-chevron-down"></i></button>
      <div class="nep-acc-body"><div class="nep-grid">
        <div class="nep-field"><span class="nep-hint" style="line-height:1.5"><?php echo esc_html( 'Estos datos son del país, no de esta página: se usan en la ficha de ' . $nombre . ', en la portada de su ruta y en sus 24 páginas. Al guardar se actualizan todas. Lo que dejes vacío no se muestra.' ); ?></span></div>
        <?php foreach ( grenvios_pais_campos() as $k => $c ) :
          $fid = 'nep-pais-' . esc_attr( $k ); ?>
        <div class="nep-field">
          <label for="<?php echo $fid; ?>"><?php echo esc_html( $c[0] ); ?></label>
          <span class="nep-hint"><?php echo esc_html( $c[1] ); ?></span>
          <textarea id="<?php echo $fid; ?>" data-pais-key="<?php echo esc_attr( $ps . '|' . $k ); ?>" rows="3"><?php echo esc_textarea( isset( $row[ $k ] ) ? $row[ $k ] : '' ); ?></textarea>
        </div>
        <?php endforeach; ?>
      </div></div>
    </div>
	<?php
} );

add_action( 'grenvios_editor_guardar', function ( $request ) {
	$in = $request->get_param( 'pais' );
	if ( ! is_array( $in ) || ! $in || ! current_user_can( 'edit_pages' ) || ! function_exists( 'grenvios_pais_campos' ) ) return;

	$campos  = grenvios_pais_campos();
	$dest    = function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array();
	$all     = grenvios_pais_extra_all();
	$tocados = array();
	foreach ( $in as $clave => $valor ) {
		$partes = explode( '|', (string) $clave, 2 );
		if ( count( $partes ) !== 2 ) continue;
		$ps = sanitize_title( $partes[0] );
		$k  = sanitize_key( $partes[1] );
		if ( ! isset( $dest[ $ps ] ) || ! isset( $campos[ $k ] ) ) continue;
		$v   = sanitize_textarea_field( (string) $valor );
		$old = isset( $all[ $ps ][ $k ] ) ? (string) $all[ $ps ][ $k ] : '';
		if ( $v === $old ) continue;
		$all[ $ps ][ $k ] = $v;
		$tocados[ $ps ]   = true;
	}
	if ( ! $tocados ) return;
	grenvios_pais_extra_save( $all );

	/* El bloque de país va guardado dentro de cada página: sin rehacerlo, el
	 * cambio no se vería al recargar. Solo se rehace la ruta del país tocado. */
	if ( function_exists( 'grenvios_pais_regenerar' ) && function_exists( 'grenvios_sedes' ) ) {
		foreach ( grenvios_sedes() as $l => $_s ) {
			if ( isset( $tocados[ grenvios_sede_destino_propio( $l ) ] ) ) grenvios_pais_regenerar( $l );
		}
	}
} );
