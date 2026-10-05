<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Clúster de destinos: páginas por región (2026-09-28)
 * ══════════════════════════════════════════════════════════════════════════
 *
 *   /destinos/  (hub)
 *     ├─ /envios-a-sudamerica/                  (inc/paginas-cluster.php)
 *     ├─ /envios-a-norteamerica/                ← EE. UU. · Canadá · México
 *     ├─ /envios-a-centroamerica-y-el-caribe/   ← Cuba · Panamá · Costa Rica · Puerto Rico
 *     └─ /envios-a-europa/                      ← España · Italia · Francia · Alemania
 *
 * Por qué: entre el hub y las nueve fichas no había un nivel intermedio. Las
 * búsquedas por región («envíos a Europa desde Perú») no tenían página, y los
 * catorce países sin ficha solo aparecían como una lista en el hub. Cada
 * región enlaza a sus fichas y cada ficha enlaza a su región: la autoridad
 * sube y baja por la jerarquía en vez de quedarse en el hub.
 *
 * Datos: fichas y países «bajo pedido» salen del gestor (grenvios_destinos()
 * y grenvios_destinos_extra()). El resto son hechos generales de cada región
 * (quién revisa en aduana, idioma, apostilla); nada de precios ni umbrales.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Región => fichas propias que agrupa (para el enlace ficha → región). */
function grenvios_regiones() {
	return array(
		'envios-a-sudamerica'                 => array( 'ecuador', 'colombia', 'chile', 'bolivia', 'argentina', 'venezuela' ),
		'envios-a-norteamerica'               => array( 'estados-unidos' ),
		'envios-a-centroamerica-y-el-caribe'  => array( 'cuba' ),
		'envios-a-europa'                     => array( 'espana' ),
	);
}

add_filter( 'grenvios_pse_slugs', function ( $s ) {
	return array_merge( $s, array( 'envios-a-norteamerica', 'envios-a-centroamerica-y-el-caribe', 'envios-a-europa' ) );
} );

add_filter( 'grenvios_pse_paginas', function ( $d, $u ) {
	$home  = '<a href="' . $u( '/' ) . '">envíos internacionales desde Lima</a>';
	$dest  = '<a href="' . $u( '/destinos/' ) . '">todos nuestros destinos</a>';
	$trad  = '<a href="' . $u( '/servicios/traduccion-oficial-de-documentos/' ) . '">traducción oficial</a>';
	$apos  = '<a href="' . $u( '/servicios/apostilla-y-traduccion/' ) . '">apostilla</a>';
	$bajo  = 'Los países marcados «bajo pedido» no tienen ficha propia, pero enviamos a ellos de forma regular: la forma de entrega te la confirmamos al cotizar.';

	/* Sudamérica suma sus tres países bajo pedido. */
	if ( isset( $d['envios-a-sudamerica']['bloques'] ) ) {
		foreach ( $d['envios-a-sudamerica']['bloques'] as $i => $b ) {
			if ( $b['tipo'] === 'destinos' ) {
				$d['envios-a-sudamerica']['bloques'][ $i ]['extra'] = array( 'Brasil', 'Uruguay', 'Paraguay' );
				$d['envios-a-sudamerica']['bloques'][ $i ]['nota']  = $bajo;
			}
		}
	}

	/* ── Norteamérica ────────────────────────────────────────────────── */
	$d['envios-a-norteamerica'] = array(
		'parent' => '',
		'label'  => 'Norteamérica',
		'kw'     => 'envíos a Norteamérica desde Perú',
		'meta'   => array(
			'Envíos a Norteamérica desde Perú',
			'Envíos a Norteamérica desde Perú | Grenvíos',
			'Envíos de Perú a Estados Unidos, Canadá y México por vía aérea: plazos, forma de entrega, aduana y documentos que conviene preparar.',
		),
		'ent'    => 'Hacemos <strong>envíos a Norteamérica desde Perú</strong> por vía aérea: Estados Unidos con ruta propia y entrega puerta a puerta, y Canadá y México bajo pedido. Aquí comparas plazos y preparas el envío. <a href="%H%/cotizar/">Cotiza tu envío</a>.',
		'hero'   => array( 'Estados Unidos, Canadá y México', 'Envíos a Norteamérica desde {{origen_ciudad}}' ),
		'bloques' => array(
			array( 'tipo' => 'intro', 'k' => 'na_intro',
				'titulo' => 'La ruta más rápida de nuestro catálogo',
				'texto'  => 'Estados Unidos es el destino con el plazo más corto de nuestros ' . $home . ', y el que más envíos recibe de familias y empresas peruanas. A toda la región se llega por avión: no hay ruta terrestre, así que las reglas de la vía aérea se aplican a todo lo que envíes.' ),
			array( 'tipo' => 'destinos', 'k' => 'na_dest', 'paises' => array( 'estados-unidos' ), 'extra' => array( 'Canadá', 'México' ), 'orden' => 'plazo', 'fondo' => true,
				'titulo' => 'Destinos y plazos en Norteamérica',
				'texto'  => 'Plazos por vía aérea desde el despacho. La aduana de cada país puede sumar días si revisa el envío.',
				'nota'   => $bajo ),
			array( 'tipo' => 'panels', 'k' => 'na_claves',
				'titulo' => 'Lo que cambia al enviar a Norteamérica',
				'panels' => array(
					array( 'Aduana', 'En Estados Unidos revisa U.S. Customs and Border Protection (CBP); en Canadá y México, sus propias aduanas. Todas piden el contenido descrito con precisión y su valor real.' ),
					array( 'Dirección', 'En Estados Unidos, el código postal (ZIP) de cinco dígitos y el estado en dos letras son imprescindibles. Escríbela en el orden local: número, calle, apartamento, ciudad.' ),
					array( 'Documentos para trámites', 'Para Estados Unidos y Canadá, un documento peruano suele necesitar ' . $trad . ' al inglés. Estados Unidos y México aceptan la ' . $apos . '.' ),
					array( 'Solo por avión', 'Líquidos, alimentos y baterías sueltas tienen límites en la vía aérea. Revisa <a href="' . $u( '/que-se-puede-enviar/' ) . '">qué se puede enviar</a> antes de preparar la caja.' ),
				) ),
			array( 'tipo' => 'lista', 'k' => 'na_envian', 'fondo' => true,
				'titulo' => 'Lo que más se envía a la región',
				'items'  => array(
					'Documentos para trámites migratorios, académicos y notariales.',
					'Encomiendas familiares: ropa, regalos y productos peruanos envasados.',
					'Compras hechas en Perú, consolidadas en un solo envío.',
					'Muestras y pedidos pequeños de emprendedores que venden fuera.',
				),
				'nota' => 'La ficha de <a href="' . $u( '/destinos/estados-unidos/' ) . '">envíos a Estados Unidos</a> tiene las ciudades, la aduana y los datos prácticos completos.' ),
		),
		'faqs' => array(
			array( '¿Cuánto tarda un envío de Perú a Estados Unidos?', 'Es nuestro destino más rápido: el plazo por vía aérea está en la tabla de esta página, contado en días hábiles desde el despacho.' ),
			array( '¿Envían a Canadá y México?', 'Sí, bajo pedido y por vía aérea. No tienen ficha propia, pero enviamos a ellos de forma regular: te confirmamos la entrega al cotizar.' ),
			array( '¿Se puede enviar por tierra a Norteamérica?', 'No. A toda la región se envía por vía aérea.' ),
		),
		'relacionados' => array(
			'Destinos'       => array( 'destinos/estados-unidos', 'destinos', 'envios-a-centroamerica-y-el-caribe' ),
			'Siguiente paso' => array( 'servicios/envio-express-internacional', 'cotizar' ),
		),
	);

	/* ── Centroamérica y el Caribe ───────────────────────────────────── */
	$d['envios-a-centroamerica-y-el-caribe'] = array(
		'parent' => '',
		'label'  => 'Centroamérica y el Caribe',
		'kw'     => 'envíos a Centroamérica y el Caribe',
		'meta'   => array(
			'Envíos a Centroamérica y el Caribe',
			'Envíos a Centroamérica y el Caribe desde Perú | Grenvíos',
			'Envíos de Perú a Cuba, Panamá, Costa Rica y Puerto Rico por vía aérea: plazos, forma de entrega y lo que conviene saber de cada aduana.',
		),
		'ent'    => 'Hacemos <strong>envíos a Centroamérica y el Caribe</strong> desde Lima por vía aérea: Cuba con ruta propia, y Panamá, Costa Rica y Puerto Rico bajo pedido. Aquí comparas plazos y lo que pide cada aduana. <a href="%H%/cotizar/">Cotiza tu envío</a>.',
		'hero'   => array( 'Cuba, Panamá, Costa Rica y Puerto Rico', 'Envíos a Centroamérica y el Caribe desde {{origen_ciudad}}' ),
		'bloques' => array(
			array( 'tipo' => 'intro', 'k' => 'cc_intro',
				'titulo' => 'Una región, reglas muy distintas',
				'texto'  => 'Son destinos cercanos entre sí, pero cada aduana funciona a su manera: Cuba es especialmente estricta con lo que no está bien declarado, y Puerto Rico, al ser territorio de Estados Unidos, sigue las reglas de la aduana estadounidense. Todo se envía por avión, como parte de nuestros ' . $home . '.' ),
			array( 'tipo' => 'destinos', 'k' => 'cc_dest', 'paises' => array( 'cuba' ), 'extra' => array( 'Panamá', 'Costa Rica', 'Puerto Rico' ), 'orden' => 'plazo', 'fondo' => true,
				'titulo' => 'Destinos y plazos en la región',
				'texto'  => 'Plazos por vía aérea desde el despacho, sin contar la revisión de la aduana de destino.',
				'nota'   => $bajo ),
			array( 'tipo' => 'panels', 'k' => 'cc_claves',
				'titulo' => 'Lo que conviene saber de cada destino',
				'panels' => array(
					array( 'Cuba', 'La entrega es en agencia local, con el carné de identidad de quien recibe. Aceptamos medicinas con su receta. Para trámites, Cuba no forma parte del convenio de la apostilla: sus documentos suelen pedir legalización.' ),
					array( 'Puerto Rico', 'Territorio de Estados Unidos: revisa la aduana estadounidense (CBP) y la dirección lleva código postal (ZIP) de cinco dígitos.' ),
					array( 'Panamá y Costa Rica', 'Envíos bajo pedido por vía aérea. Te confirmamos al cotizar la forma de entrega y lo que admite cada aduana.' ),
					array( 'Solo por avión', 'Líquidos, alimentos y baterías sueltas tienen límites en la vía aérea. Consulta antes de embalar.' ),
				) ),
			array( 'tipo' => 'pasos', 'k' => 'cc_pasos', 'fondo' => true,
				'titulo' => 'Cómo preparar un envío a la región',
				'pasos'  => array(
					array( 'Confirma que el contenido entra.', 'Sobre todo en Cuba: pregunta antes de comprar lo que vas a enviar.' ),
					array( 'Declara cada artículo.', 'Nombre, cantidad y valor real. Una descripción vaga es la primera causa de retención.' ),
					array( 'Escribe bien los datos de quien recibe.', 'Nombre como en su documento, dirección con referencias y un teléfono que conteste.' ),
					array( 'Guarda el número de guía.', 'Para seguir el envío y para cualquier gestión con la aduana.' ),
				),
				'nota' => 'La ficha de <a href="' . $u( '/destinos/cuba/' ) . '">envíos a Cuba</a> tiene las ciudades, la aduana y los datos prácticos completos.' ),
		),
		'faqs' => array(
			array( '¿Envían a Panamá, Costa Rica y Puerto Rico?', 'Sí, bajo pedido y por vía aérea. Te confirmamos la forma de entrega y lo que admite cada aduana al cotizar.' ),
			array( '¿Se pueden enviar medicinas a Cuba?', 'Sí, acompañadas de su receta médica y en su envase original.' ),
			array( '¿Un envío a Puerto Rico pasa por la aduana de Estados Unidos?', 'Sí. Puerto Rico es territorio de Estados Unidos, así que se aplican las reglas de su aduana (CBP).' ),
		),
		'relacionados' => array(
			'Destinos'       => array( 'destinos/cuba', 'destinos', 'envios-a-norteamerica' ),
			'Siguiente paso' => array( 'servicios/envio-de-medicinas-al-extranjero', 'cotizar' ),
		),
	);

	/* ── Europa ──────────────────────────────────────────────────────── */
	$d['envios-a-europa'] = array(
		'parent' => '',
		'label'  => 'Europa',
		'kw'     => 'envíos a Europa desde Perú',
		'meta'   => array(
			'Envíos a Europa desde Perú',
			'Envíos a Europa desde Perú | Grenvíos',
			'Envíos de Perú a España, Italia, Francia y Alemania por vía aérea: plazos, IVA de importación, traducción de documentos y cómo preparar el envío.',
		),
		'ent'    => 'Hacemos <strong>envíos a Europa desde Perú</strong> por vía aérea: España con ruta propia y entrega puerta a puerta, e Italia, Francia y Alemania bajo pedido. Aquí están los plazos y lo que conviene saber de la aduana europea. <a href="%H%/cotizar/">Cotiza tu envío</a>.',
		'hero'   => array( 'España, Italia, Francia y Alemania', 'Envíos a Europa desde {{origen_ciudad}}' ),
		'bloques' => array(
			array( 'tipo' => 'intro', 'k' => 'eu_intro',
				'titulo' => 'De Lima a Europa, con una sola aduana que entender',
				'texto'  => 'Italia, Francia, Alemania y España forman parte de la Unión Europea, y un envío que llega desde Perú entra por la aduana del país que lo recibe. Las reglas de fondo son comunes, así que lo que aprendes para un país te sirve para los demás. Todo viaja por avión, como parte de nuestros ' . $home . '.' ),
			array( 'tipo' => 'destinos', 'k' => 'eu_dest', 'paises' => array( 'espana' ), 'extra' => array( 'Italia', 'Francia', 'Alemania' ), 'orden' => 'plazo', 'fondo' => true,
				'titulo' => 'Destinos y plazos en Europa',
				'texto'  => 'Plazos por vía aérea desde el despacho, sin contar la revisión de la aduana de destino.',
				'nota'   => $bajo ),
			array( 'tipo' => 'panels', 'k' => 'eu_claves',
				'titulo' => 'Lo que cambia al enviar a Europa',
				'panels' => array(
					array( 'IVA de importación', 'Al llegar de fuera de la Unión Europea, el envío pasa por aduana y puede pagar IVA de importación. Lo fija el país que recibe según el contenido y su valor.' ),
					array( 'Documentos', 'Los cuatro países aceptan la ' . $apos . '. Para Italia, Francia y Alemania, un documento peruano suele necesitar además ' . $trad . '.' ),
					array( 'Dirección', 'Calle, número, piso y puerta, código postal y ciudad. En los edificios, sin piso y puerta el envío no se entrega.' ),
					array( 'Solo por avión', 'Líquidos, alimentos y baterías sueltas tienen límites en la vía aérea. Revisa <a href="' . $u( '/que-se-puede-enviar/' ) . '">qué se puede enviar</a>.' ),
				) ),
			array( 'tipo' => 'lista', 'k' => 'eu_envian', 'fondo' => true,
				'titulo' => 'Lo que más se envía a Europa',
				'items'  => array(
					'Documentos para residencia, estudios y homologación de títulos.',
					'Encomiendas familiares con ropa, regalos y productos peruanos envasados.',
					'Equipaje enviado por delante antes de una mudanza.',
					'Artesanía y muestras de emprendedores que venden en Europa.',
				),
				'nota' => 'La ficha de <a href="' . $u( '/destinos/espana/' ) . '">envíos a España</a> tiene las ciudades, la aduana y los datos prácticos completos.' ),
		),
		'faqs' => array(
			array( '¿Un envío a Europa paga impuestos?', 'Puede pagar IVA de importación, porque llega de fuera de la Unión Europea. Lo decide la aduana del país que recibe según el contenido y su valor; te lo confirmamos al cotizar.' ),
			array( '¿Envían a Italia, Francia y Alemania?', 'Sí, bajo pedido y por vía aérea. No tienen ficha propia, pero enviamos a ellos de forma regular.' ),
			array( '¿Mis documentos peruanos valen en Europa?', 'Con apostilla, sí: los cuatro países forman parte del convenio. Fuera de España, suelen pedir además traducción oficial.' ),
		),
		'relacionados' => array(
			'Destinos'       => array( 'destinos/espana', 'destinos', 'envios-a-norteamerica' ),
			'Siguiente paso' => array( 'servicios/traduccion-oficial-de-documentos', 'cotizar' ),
		),
	);

	return $d;
}, 12, 2 );

/* ── Enlaces: hub → regiones, home → regiones ─────────────────────────── */
add_filter( 'grenvios_related_map', function ( $mapa, $slug ) {
	$regiones = array( 'envios-a-sudamerica', 'envios-a-norteamerica', 'envios-a-centroamerica-y-el-caribe', 'envios-a-europa' );
	$entrantes = array(
		'destinos'           => array( 'Por región' => $regiones ),
		'home'               => array( 'Rutas y servicios' => array( 'envios-a-norteamerica', 'envios-a-europa' ) ),
		'tiempos-de-entrega' => array( 'Por región' => $regiones ),
	);
	foreach ( $entrantes as $origen => $bloques ) {
		if ( ! isset( $mapa[ $origen ] ) ) $mapa[ $origen ] = array();
		foreach ( $bloques as $titulo => $destinos ) {
			$actual = isset( $mapa[ $origen ][ $titulo ] ) ? $mapa[ $origen ][ $titulo ] : array();
			$mapa[ $origen ][ $titulo ] = array_values( array_unique( array_merge( $actual, $destinos ) ) );
		}
	}
	/* Cada ficha de país enlaza a su región (ver seo-enlazado: las fichas
	 * también pasan por este filtro). */
	foreach ( grenvios_regiones() as $region => $paises ) {
		foreach ( $paises as $p ) {
			if ( $slug !== $p ) continue;
			$mapa[ $p ] = isset( $mapa[ $p ] ) ? $mapa[ $p ] : array();
			$mapa[ $p ]['Tu región'] = array( $region );
		}
	}
	return $mapa;
}, 24, 2 );

add_filter( 'grenvios_bep_temas', function ( $t ) {
	return array_merge( $t, array(
		'envios-a-norteamerica'              => array( 'envios-a-estados-unidos', 'apostilla-y-documentos' ),
		'envios-a-centroamerica-y-el-caribe' => array( 'envios-a-cuba', 'productos-prohibidos' ),
		'envios-a-europa'                    => array( 'envios-a-espana', 'apostilla-y-documentos' ),
	) );
}, 12 );

add_action( 'wp_head', function () {
	if ( is_admin() ) return;
	echo '<style id="grenvios-regiones-css">.gr-tabla-nota{display:inline-block;margin-left:6px;padding:2px 8px;border-radius:999px;background:var(--bg-grey,#f8f5f1);font-size:12px;font-weight:600;color:var(--body-color,#666);white-space:nowrap}</style>';
}, 106 );
