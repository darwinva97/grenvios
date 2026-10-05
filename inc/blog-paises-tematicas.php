<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Guías temáticas por país (2026-10-02): ciudades principales y productos
 * ══════════════════════════════════════════════════════════════════════════
 *
 * PEDIDO: más artículos por país. Se descartó generar cientos por plantilla
 * (contenido masivo = riesgo de penalización de todo el sitio). Solo entran
 * búsquedas reales que la ruta aún no cubría y para las que hay datos propios
 * del país:
 *
 *   · 3 ciudades principales por país (datos de ciudad: geografía, clima,
 *     cómo se escriben allí las direcciones). Pilar: la ficha del país.
 *   · 9 productos con regla propia del país: celulares (IMEI), perfumes y
 *     cosméticos, ropa (estación de allí), artesanía, repuestos,
 *     superalimentos peruanos, joyas y plata, útiles escolares (calendario de
 *     allí) y regalos de cumpleaños.
 *
 * Mismo importador que las demás guías de país (maestra en la ruta principal
 * con canónica a la copia del país). Importar por tandas de tres países:
 * grenvios_guias_pais_importar( grenvios_bpt_claves( 'ecuador' ) ).
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* Tres ciudades por país con lo que de verdad cambia al enviar allí. */
function grenvios_bpt_ciudades( $s ) {
	$c = array(
		'ecuador' => array(
			'Quito' => array( 'Capital del país, en la Sierra y a unos 2 850 m de altura: fresca todo el año y con lluvias frecuentes por la tarde.', 'Quito usa una nomenclatura con letra y número —«N24-03» indica el norte de la ciudad— más la calle transversal. Copia la dirección tal cual: es lo que ubica la casa.', 'La altura afecta a frascos y envases con aire: ciérralos con cinta y en bolsa sellada.' ),
			'Guayaquil' => array( 'Puerto principal y ciudad más poblada de Ecuador, en la Costa: calor y humedad casi todo el año.', 'Indica la ciudadela o el barrio, la manzana y el solar si la dirección lo usa, más una referencia visible: en muchas zonas es lo que guía al repartidor de la agencia.', 'Con la humedad de la Costa, textiles, papeles y cartón deben ir dentro de bolsa sellada.' ),
			'Cuenca' => array( 'Tercera ciudad del país, en la Sierra sur, a unos 2 500 m, con un centro histórico declarado Patrimonio de la Humanidad.', 'En el centro histórico las calles son estrechas y de nombre propio: añade la calle transversal y una referencia, como en el resto de Ecuador.', 'Clima templado y seco por la altura: un embalaje estándar bien cerrado es suficiente.' ),
		),
		'colombia' => array(
			'Bogotá' => array( 'Capital del país, a unos 2 600 m de altura: fresca y lluviosa buena parte del año.', 'La nomenclatura es una coordenada: «Calle 45 # 12-30» es la casa 30 de la Calle 45 cerca de la Carrera 12. Añade el barrio y, si es un conjunto, torre y apartamento.', 'Con la lluvia bogotana, protege la caja con bolsa o film en las juntas.' ),
			'Medellín' => array( 'Segunda ciudad del país, en el valle de Aburrá, de clima templado todo el año; su área metropolitana incluye municipios como Envigado, Itagüí o Bello.', 'Indica el municipio exacto del área metropolitana, no solo «Medellín»: Envigado o Bello tienen sus propias direcciones.', 'Clima templado: un embalaje estándar bien cerrado basta.' ),
			'Cali' => array( 'Tercera ciudad del país, en el Valle del Cauca: cálida durante todo el año.', 'Misma nomenclatura de Calle y Carrera que el resto de Colombia; añade el barrio y la comuna si la conoces.', 'Con el calor, evita chocolates, velas o cremas que puedan deformarse.' ),
		),
		'chile' => array(
			'Santiago' => array( 'Capital y Región Metropolitana, donde vive más de un tercio de la población y una de las comunidades peruanas más grandes fuera del país.', 'Santiago tiene decenas de comunas y la misma calle puede repetirse en varias: la comuna es obligatoria. En edificios, número de departamento o block.', 'Clima seco, con inviernos fríos: un embalaje estándar bien cerrado basta.' ),
			'Valparaíso' => array( 'Puerto histórico de la costa central, junto a Viña del Mar, construido sobre cerros con escaleras y ascensores.', 'En los cerros, muchas casas solo se alcanzan a pie: añade el nombre del cerro, el pasaje y una referencia, y un teléfono que conteste para guiar al repartidor.', 'Por la humedad costera, protege papeles y textiles en bolsa.' ),
			'Antofagasta' => array( 'Principal ciudad del norte minero, en pleno desierto costero y con una comunidad migrante importante.', 'Indica la población o el sector además de la calle: la ciudad se extiende en franja a lo largo de la costa.', 'Clima seco y soleado: un embalaje estándar bien cerrado basta.' ),
		),
		'bolivia' => array(
			'La Paz' => array( 'Sede de gobierno, a más de 3 600 m de altura, junto a El Alto, que está aún más arriba.', 'La zona dice más que el número: muchas calles cambian de nombre o se repiten. Indica zona, calle, número y una referencia.', 'Con la altura, frascos y envases con aire pueden abrirse o gotear: ciérralos con cinta y en bolsa.' ),
			'Santa Cruz de la Sierra' => array( 'Ciudad más poblada de Bolivia, en tierras bajas, cálida y húmeda, y principal centro comercial del país.', 'Santa Cruz se organiza en anillos concéntricos y radiales: indica entre qué anillos está la dirección, el barrio y una referencia.', 'Con el calor y la humedad, sella textiles y evita productos que se derritan.' ),
			'Cochabamba' => array( 'Ciudad del valle central, de clima templado, punto de paso entre el occidente y el oriente del país.', 'Indica zona o barrio, calle, número y una referencia, como en el resto de Bolivia.', 'Clima templado: un embalaje estándar bien cerrado basta.' ),
		),
		'argentina' => array(
			'Buenos Aires' => array( 'La Ciudad Autónoma (CABA) y el Gran Buenos Aires que la rodea concentran buena parte de la población del país.', 'Distingue CABA de los partidos del conurbano —no es lo mismo «Buenos Aires» ciudad que provincia— e incluye el código postal y el piso y departamento.', 'Inviernos frescos y veranos húmedos: protege textiles en bolsa.' ),
			'Córdoba' => array( 'Segunda ciudad del país, en el centro, con una gran población universitaria.', 'Indica barrio, calle, número y código postal; en edificios, piso y departamento.', 'Clima templado con veranos calurosos: evita lo que se derrita en esa temporada.' ),
			'Mendoza' => array( 'Capital de la región vitivinícola, al pie de los Andes y en la frontera con Chile.', 'Indica el departamento del Gran Mendoza (Godoy Cruz, Guaymallén, Las Heras…) además de la calle: la ciudad se extiende sobre varios.', 'Clima seco, con inviernos fríos: un embalaje estándar bien cerrado basta.' ),
		),
		'estados-unidos' => array(
			'Miami' => array( 'Gran puerta de entrada de Sudamérica a Estados Unidos, en el sur de Florida, con calor y humedad todo el año.', 'Dirección en inglés: número, calle, número de apartamento o unidad, ciudad, estado (FL) y ZIP code. Sin el número de unidad, el envío no sale a reparto.', 'La temporada de huracanes va de junio a noviembre y puede frenar el reparto unos días; con la humedad, sella textiles y papeles.' ),
			'Nueva York' => array( 'La ciudad más poblada del país, con cinco condados (Manhattan, Queens, Brooklyn, el Bronx y Staten Island) y una comunidad peruana numerosa, sobre todo en Queens.', 'Indica el condado, el número de apartamento y el ZIP code. En edificios con portero, un teléfono que conteste evita que el envío vuelva.', 'Inviernos fríos y veranos calurosos: protege lo que se deforma con el calor.' ),
			'Newark y Nueva Jersey' => array( 'El norte de Nueva Jersey —Newark, Paterson y alrededores— reúne una de las comunidades peruanas más grandes de Estados Unidos.', 'Escribe la ciudad exacta (Paterson, Newark, Elizabeth…), el estado (NJ) y el ZIP code: hay calles con el mismo nombre en municipios vecinos.', 'Inviernos fríos: un embalaje estándar bien cerrado basta.' ),
		),
		'espana' => array(
			'Madrid' => array( 'Capital del país y ciudad con la mayor comunidad peruana de España.', 'Calle, número, piso y puerta («3.º B»), código postal —en la capital empiezan por 28— y ciudad. Sin piso y puerta, el repartidor no sabe a qué timbre llamar.', 'Veranos muy calurosos: en julio y agosto evita chocolates, cremas y velas.' ),
			'Barcelona' => array( 'Segunda ciudad del país, capital de Cataluña y con otra de las comunidades peruanas grandes de España.', 'Mismo formato que en el resto de España: calle, número, piso, puerta y código postal (en la ciudad empiezan por 08). La dirección puede estar en catalán: cópiala tal como figura.', 'Clima mediterráneo, húmedo en verano: sella textiles en bolsa.' ),
			'Valencia' => array( 'Tercera ciudad del país, en la costa mediterránea.', 'Calle, número, piso, puerta y código postal (en la ciudad empiezan por 46). Como en Barcelona, el nombre de la calle puede figurar en valenciano: cópialo tal cual.', 'Clima templado y húmedo en la costa: protege papeles y textiles.' ),
		),
		'venezuela' => array(
			'Caracas' => array( 'Capital del país, en un valle a unos 900 m, de clima templado.', 'Las direcciones se dan por urbanización, avenida o calle, edificio o quinta y una referencia; añade el municipio (Libertador, Chacao, Baruta…).', 'Clima templado: un embalaje estándar bien cerrado basta.' ),
			'Maracaibo' => array( 'Capital del Zulia y segunda ciudad del país, a orillas del lago, con uno de los climas más calurosos de Venezuela.', 'Indica sector o urbanización, calle, número de casa y una referencia visible.', 'Con el calor extremo, evita todo lo que se derrita y sella la ropa en bolsas.' ),
			'Valencia' => array( 'Capital del estado Carabobo, en el centro del país y uno de sus polos industriales.', 'Indica urbanización, calle o avenida, número y una referencia, y aclara que es Valencia (Carabobo).', 'Clima cálido: protege lo que se deforma con el calor.' ),
		),
		'cuba' => array(
			'La Habana' => array( 'Capital del país, dividida en municipios como Plaza de la Revolución, Centro Habana, La Habana Vieja o Playa.', 'Las direcciones cubanas se escriben con «entre»: calle y número, entre qué dos calles está, reparto o barrio y municipio. Sin las calles de «entre», el envío no se ubica.', 'Calor y humedad todo el año: sella medicinas y ropa en bolsas.' ),
			'Santiago de Cuba' => array( 'Segunda ciudad del país, en el oriente de la isla, la región más calurosa de Cuba.', 'Mismo formato con «entre»: calle, número, entre qué calles, reparto y municipio.', 'Con el calor, evita lo que se derrita y protege las medicinas.' ),
			'Camagüey' => array( 'Ciudad del centro-este de la isla, famosa por su trazado irregular de callejones y plazas.', 'Por su trazado, las calles de «entre» y una referencia son todavía más importantes que en otras ciudades.', 'Clima cálido y húmedo: sella el contenido en bolsas.' ),
		),
	);
	$base = isset( $c[ $s ] ) ? $c[ $s ] : array();
	return array_merge( $base, grenvios_bpt_ciudades_mas( $s ), grenvios_bpt_ciudades_resto( $s ) );
}

/* Tercera tanda: el resto de ciudades de la cobertura del gestor. */
function grenvios_bpt_ciudades_resto( $s ) {
	$sierra = 'Calle, número, calle transversal, barrio y una referencia: como en todo Ecuador, la intersección es lo que ubica la casa.';
	$co     = 'Nomenclatura de Calle y Carrera con «#» y placa, más el barrio.';
	$cl     = 'Calle, número, departamento o block si lo hay, y la comuna, que es obligatoria.';
	$ar     = 'Calle, número, piso y departamento, barrio y código postal.';
	$us     = 'Número, calle, número de apartamento o unidad, ciudad, estado y ZIP code. Sin número de unidad, el envío no sale a reparto.';
	$ve     = 'Urbanización o sector, calle o avenida, número de casa y una referencia visible.';
	$cu     = 'Calle y número, entre qué dos calles está, reparto y municipio: el formato con «entre» de toda Cuba.';
	$c = array(
		'ecuador' => array(
			'Santo Domingo' => array( 'Capital de Santo Domingo de los Tsáchilas, el gran nudo de carreteras entre la Sierra y la Costa.', $sierra . ' En muchos sectores la cooperativa o urbanización ayuda tanto como la calle.', 'Clima cálido y muy lluvioso: protege la caja con bolsa y cinta en todas las juntas.' ),
			'Ambato' => array( 'Capital de Tungurahua, en la Sierra central, conocida como la ciudad de las flores y las frutas.', $sierra, 'Por la altura, cierra bien frascos y envases con aire.' ),
			'Portoviejo' => array( 'Capital de Manabí, en la costa central, a pocos kilómetros de Manta.', $sierra, 'Calor costero: sella textiles y papeles en bolsa.' ),
			'Ibarra' => array( 'Capital de Imbabura, en la Sierra norte, conocida como la «ciudad blanca».', $sierra, 'Clima templado: un embalaje estándar bien cerrado basta.' ),
		),
		'colombia' => array(
			'Pereira' => array( 'Capital de Risaralda, en el Eje Cafetero.', $co . ' Si es Dosquebradas, indícalo: es otro municipio.', 'Clima templado y lluvioso: protege la caja de la humedad.' ),
			'Cúcuta' => array( 'Capital de Norte de Santander, en la frontera con Venezuela.', $co, 'Calor intenso: evita lo que se derrita.' ),
			'Santa Marta' => array( 'Ciudad del Caribe, la más antigua de Colombia, a orillas de la Sierra Nevada.', $co, 'Calor y humedad caribeños: todo dentro de bolsas selladas.' ),
			'Manizales' => array( 'Capital de Caldas, en el Eje Cafetero, construida sobre laderas de montaña.', $co . ' Por las pendientes, una referencia visible ayuda mucho al repartidor de la agencia.', 'Clima fresco y lluvioso: protege la caja de la humedad.' ),
		),
		'chile' => array(
			'Viña del Mar' => array( 'La «ciudad jardín», junto a Valparaíso en la costa central.', $cl . ' Viña del Mar y Valparaíso son comunas distintas aunque estén pegadas.', 'Humedad costera: protege papeles y textiles en bolsa.' ),
			'La Serena' => array( 'Capital de la región de Coquimbo, en el norte chico, pegada a la ciudad de Coquimbo.', $cl . ' La Serena y Coquimbo son comunas distintas.', 'Clima templado costero, con neblina matinal: protege papeles en bolsa.' ),
			'Temuco' => array( 'Capital de La Araucanía, en el sur del país.', $cl, 'Clima lluvioso: protege la caja con bolsa y cinta en las juntas.' ),
			'Puerto Montt' => array( 'Capital de la región de Los Lagos, puerta de entrada a la Patagonia chilena y a Chiloé.', $cl . ' Para Chiloé y la Patagonia, el tramo final suma unos días.', 'De las ciudades más lluviosas del país: todo dentro de bolsas selladas.' ),
		),
		'bolivia' => array(
			'Oruro' => array( 'Ciudad del altiplano, a unos 3 700 m, sede del Carnaval de Oruro, Patrimonio Oral e Inmaterial de la Humanidad.', 'Calle, número, zona y una referencia. En Carnaval, la ciudad se paraliza: cuéntalo en el plazo.', 'Con la altura, frascos y envases con aire pueden abrirse: ciérralos con cinta y en bolsa.' ),
			'Potosí' => array( 'Una de las ciudades más altas del mundo, a más de 4 000 m, al pie del Cerro Rico.', 'Calle, número, zona y una referencia.', 'A esa altura, frascos y envases con aire pueden abrirse o gotear: ciérralos con cinta y en bolsa sellada.' ),
		),
		'argentina' => array(
			'Mar del Plata' => array( 'Principal ciudad balnearia de la costa atlántica, en la provincia de Buenos Aires.', $ar, 'Humedad costera: protege textiles en bolsa.' ),
			'Tucumán' => array( 'San Miguel de Tucumán, capital de la provincia más pequeña del país, en el noroeste.', $ar, 'Veranos cálidos y húmedos: evita lo que se derrita.' ),
			'Santa Fe' => array( 'Capital de la provincia de Santa Fe, junto al río Paraná y frente a la ciudad de Paraná, en Entre Ríos.', $ar . ' Aclara que es Santa Fe capital y no Rosario, que está en la misma provincia.', 'Clima húmedo: protege textiles y papeles.' ),
			'Neuquén' => array( 'Capital de la provincia de Neuquén, puerta de la Patagonia norte.', $ar, 'Clima seco y ventoso: caja bien cerrada y con cinta en H.' ),
		),
		'estados-unidos' => array(
			'Chicago' => array( 'Tercera ciudad del país, a orillas del lago Míchigan, en Illinois.', $us . ' El estado es IL.', 'Inviernos muy fríos y nevados: protege la caja de la humedad.' ),
			'Atlanta' => array( 'Capital de Georgia, en el sureste del país.', $us . ' El estado es GA.', 'Veranos calurosos y húmedos: evita lo que se derrita.' ),
			'Dallas' => array( 'Ciudad del norte de Texas, en el área metropolitana de Dallas–Fort Worth.', $us . ' Dentro del área hay muchas ciudades (Arlington, Irving, Plano…): escribe la exacta. El estado es TX.', 'Veranos muy calurosos: evita chocolates, cremas y velas.' ),
			'Orlando' => array( 'Ciudad del centro de Florida.', $us . ' El estado es FL.', 'Calor húmedo y temporada de huracanes de junio a noviembre: sella el contenido en bolsas.' ),
			'Boston' => array( 'Capital de Massachusetts, en Nueva Inglaterra.', $us . ' El estado es MA.', 'Inviernos fríos y nevados: protege la caja de la humedad.' ),
			'San Francisco' => array( 'Ciudad de la bahía de California, en la costa oeste; va dos o tres horas por detrás de Lima.', $us . ' El estado es CA.', 'Clima templado y con neblina: un embalaje estándar bien cerrado basta.' ),
		),
		'espana' => array(
			'Málaga' => array( 'Capital de la Costa del Sol, en Andalucía.', 'Calle, número, piso, puerta y código postal (en la ciudad empiezan por 29).', 'Veranos calurosos: evita chocolates, cremas y velas en julio y agosto.' ),
			'Murcia' => array( 'Capital de la Región de Murcia, en el sureste de la península.', 'Calle, número, piso, puerta y código postal (en la ciudad empiezan por 30).', 'Veranos muy calurosos: evita lo que se derrita.' ),
			'Alicante' => array( 'Ciudad de la costa mediterránea, en la Comunidad Valenciana.', 'Calle, número, piso, puerta y código postal (en la provincia empiezan por 03). El nombre de la calle puede figurar en valenciano.', 'Clima mediterráneo: protege textiles de la humedad.' ),
			'Palma de Mallorca' => array( 'Capital de las Islas Baleares. Las islas están dentro del IVA de la Unión Europea, pero el tramo hasta ellas suma unos días.', 'Calle, número, piso, puerta y código postal (en las Baleares empiezan por 07).', 'Clima mediterráneo: protege textiles de la humedad.' ),
		),
		'venezuela' => array(
			'Ciudad Guayana' => array( 'Ciudad del estado Bolívar formada por Puerto Ordaz y San Félix, en la confluencia de los ríos Orinoco y Caroní.', $ve . ' Indica si es Puerto Ordaz o San Félix.', 'Calor y humedad: sella la ropa en bolsas.' ),
			'Maturín' => array( 'Capital del estado Monagas, en el oriente del país.', $ve, 'Clima cálido: protege lo que se deforma con el calor.' ),
			'Puerto La Cruz' => array( 'Ciudad costera del estado Anzoátegui, en el oriente, junto a Barcelona y Lechería.', $ve . ' Aclara el municipio: Puerto La Cruz, Barcelona y Lechería son distintos.', 'Brisa marina y humedad: protege lo metálico y lo electrónico en bolsa.' ),
		),
		'cuba' => array(
			'Bayamo' => array( 'Capital de la provincia Granma, en el oriente de la isla.', $cu, 'Calor y humedad: sella medicinas y ropa en bolsas.' ),
			'Matanzas' => array( 'Ciudad de la costa norte, al este de La Habana, conocida como la «Atenas de Cuba».', $cu, 'Brisa marina y humedad: protege lo metálico y lo electrónico en bolsa.' ),
		),
	);
	return isset( $c[ $s ] ) ? $c[ $s ] : array();
}

/* Segunda tanda (2026-10-02): tres ciudades más por país, elegidas porque
 * tienen algo propio que decir (frontera, formato de dirección, clima). */
function grenvios_bpt_ciudades_mas( $s ) {
	$c = array(
		'ecuador' => array(
			'Machala' => array( 'Capital de El Oro, en la costa sur, a poca distancia de la frontera de Huaquillas y conocida como la capital bananera del Ecuador.', 'Indica la ciudadela o el barrio, la calle, la calle transversal y una referencia visible: como en el resto de Ecuador, la intersección es lo que ubica la casa.', 'Calor y humedad de la Costa: sella textiles, papeles y cartón en bolsa.' ),
			'Manta' => array( 'Puerto de Manabí, en la costa central, uno de los principales puertos pesqueros del país.', 'Calle, número, calle transversal, barrio y una referencia. En las zonas nuevas, el nombre de la urbanización ayuda tanto como la calle.', 'Con la brisa marina, protege en bolsa lo metálico y lo electrónico.' ),
			'Loja' => array( 'Ciudad de la Sierra sur, cercana a la frontera con Perú por Macará, de clima templado.', 'Calle, número, calle transversal, barrio y una referencia, como en el resto de la Sierra.', 'Clima templado: un embalaje estándar bien cerrado basta.' ),
		),
		'colombia' => array(
			'Barranquilla' => array( 'Principal puerto del Caribe colombiano, en la desembocadura del río Magdalena, famosa por su Carnaval.', 'Nomenclatura de Calle y Carrera con «#» y placa, más el barrio. En el área metropolitana, indica si es Soledad o Puerto Colombia.', 'Calor y humedad caribeños: sella textiles y papeles y evita lo que se derrita.' ),
			'Cartagena' => array( 'Ciudad del Caribe con un centro histórico amurallado, Patrimonio de la Humanidad.', 'En el centro histórico muchas calles tienen nombre propio en lugar de número: añade el barrio (Centro, Getsemaní, Bocagrande…) y una referencia.', 'Humedad caribeña todo el año: todo dentro de bolsas selladas.' ),
			'Bucaramanga' => array( 'Capital de Santander, conocida como la «ciudad de los parques», de clima templado.', 'Calle y Carrera con «#» y placa, más el barrio; indica el municipio si es Floridablanca, Girón o Piedecuesta.', 'Clima templado: un embalaje estándar bien cerrado basta.' ),
		),
		'chile' => array(
			'Arica' => array( 'Primera ciudad chilena al cruzar la frontera desde Tacna, en el extremo norte: por aquí entra la vía terrestre.', 'Calle, número y población o sector; en edificios, departamento o block. La comuna es Arica.', 'Desierto costero, seco y templado: un embalaje estándar basta.' ),
			'Iquique' => array( 'Ciudad del norte grande, en la región de Tarapacá, conocida por su Zona Franca (ZOFRI).', 'Calle, número, sector y comuna (Iquique o Alto Hospicio, que son distintas).', 'Clima seco: un embalaje estándar bien cerrado basta.' ),
			'Concepción' => array( 'Capital de la región del Biobío, en el centro sur, ciudad universitaria y polo industrial.', 'La comuna es obligatoria: Concepción, Talcahuano, San Pedro de la Paz o Chiguayante son comunas distintas del mismo Gran Concepción.', 'Inviernos lluviosos: protege la caja con bolsa y cinta en todas las juntas.' ),
		),
		'bolivia' => array(
			'El Alto' => array( 'Ciudad vecina a La Paz, a unos 4 000 m de altura, la más alta del país y uno de sus grandes centros comerciales.', 'Indica zona y distrito, calle o avenida, número y una referencia: es una ciudad grande que crece rápido y la zona es lo que ubica la casa.', 'Con la altura, frascos y envases con aire pueden abrirse o gotear: ciérralos con cinta y en bolsa.' ),
			'Sucre' => array( 'Capital constitucional de Bolivia, a unos 2 800 m, con un centro histórico Patrimonio de la Humanidad.', 'Calle, número, zona y una referencia; en el centro histórico, la calle y la manzana bastan para ubicarse.', 'Clima templado y seco: un embalaje estándar bien cerrado basta.' ),
			'Tarija' => array( 'Ciudad del sur del país, de clima templado, conocida por sus viñedos y cercana a la frontera con Argentina.', 'Calle, número, barrio y una referencia.', 'Clima templado: un embalaje estándar bien cerrado basta.' ),
		),
		'argentina' => array(
			'Rosario' => array( 'Tercera ciudad del país, en la provincia de Santa Fe, a orillas del río Paraná.', 'Calle, número, piso y departamento, barrio y código postal. Aclara que es Rosario, Santa Fe.', 'Clima húmedo: protege textiles en bolsa.' ),
			'La Plata' => array( 'Capital de la provincia de Buenos Aires, una ciudad planificada con calles numeradas y avenidas diagonales.', 'Las calles son números, así que la dirección se escribe con la calle y entre qué calles está: «calle 7 n.º 1234 entre 59 y 60». Sin el «entre», no se ubica.', 'Clima templado y húmedo: protege textiles y papeles.' ),
			'Salta' => array( 'Capital del noroeste argentino, al pie de los Andes y cerca de la frontera con Bolivia.', 'Calle, número, barrio y código postal; en edificios, piso y departamento.', 'Clima seco con inviernos frescos: un embalaje estándar bien cerrado basta.' ),
		),
		'estados-unidos' => array(
			'Los Ángeles' => array( 'La mayor ciudad de California, en la costa oeste, con una comunidad peruana importante en el sur del estado. Va dos o tres horas por detrás de Lima.', 'Dentro del condado de Los Ángeles hay muchas ciudades (Glendale, Inglewood, Long Beach…): escribe la ciudad exacta, el estado (CA), el ZIP code y el número de apartamento.', 'Clima seco y soleado: un embalaje estándar bien cerrado basta; evita lo que se derrita en verano.' ),
			'Houston' => array( 'La ciudad más grande de Texas.', 'Número, calle, apartamento o suite, Houston, TX y ZIP code. Sin número de unidad, el envío no sale a reparto.', 'Calor húmedo y temporada de huracanes de junio a noviembre: sella el contenido en bolsas.' ),
			'Washington D. C.' => array( 'La capital del país; su área metropolitana incluye partes de Virginia y Maryland, donde también vive una comunidad peruana numerosa.', 'Las direcciones de la ciudad llevan cuadrante (NW, NE, SW o SE): «1600 K St NW». Sin el cuadrante, la misma dirección puede existir cuatro veces. Si es Virginia o Maryland, escribe la ciudad y el estado.', 'Inviernos fríos y veranos húmedos: protege lo que se deforma con el calor.' ),
		),
		'espana' => array(
			'Sevilla' => array( 'Capital de Andalucía, con los veranos más calurosos de la península.', 'Calle, número, piso, puerta y código postal (en la ciudad empiezan por 41).', 'En julio y agosto, evita chocolates, cremas y velas: el calor los deforma.' ),
			'Zaragoza' => array( 'Capital de Aragón, a medio camino entre Madrid y Barcelona.', 'Calle, número, piso, puerta y código postal (en la ciudad empiezan por 50).', 'Clima de contrastes y viento fuerte (el cierzo): una caja bien cerrada y con cinta en H.' ),
			'Bilbao' => array( 'Principal ciudad del País Vasco, de clima lluvioso.', 'Calle, número, piso, puerta y código postal (en la ciudad empiezan por 48). El nombre de la calle puede figurar en euskera: cópialo tal cual.', 'Llueve a menudo: protege la caja con bolsa o film.' ),
		),
		'venezuela' => array(
			'Mérida' => array( 'Ciudad andina, a unos 1 600 m de altura, universitaria y de clima fresco.', 'Urbanización o sector, calle o avenida, número de casa y una referencia.', 'Clima fresco: ropa de abrigo ligera, a diferencia del resto del país.' ),
			'Barquisimeto' => array( 'Capital del estado Lara, en el centro occidente del país.', 'Urbanización, calle o carrera, número de casa y una referencia.', 'Clima cálido y seco: protege lo que se deforma con el calor.' ),
			'Maracay' => array( 'Capital del estado Aragua, cerca de Caracas y de Valencia.', 'Urbanización, calle o avenida, número y una referencia, y aclara que es Maracay, Aragua.', 'Clima cálido: sella la ropa en bolsas.' ),
		),
		'cuba' => array(
			'Holguín' => array( 'Ciudad del oriente cubano, conocida como la «ciudad de los parques».', 'Calle y número, entre qué dos calles está, reparto y municipio: el formato con «entre» de toda Cuba.', 'Calor y humedad: sella medicinas y ropa en bolsas.' ),
			'Santa Clara' => array( 'Capital de la provincia de Villa Clara, en el centro de la isla.', 'Calle y número, entre qué calles, reparto y municipio.', 'Clima cálido y húmedo: todo dentro de bolsas selladas.' ),
			'Cienfuegos' => array( 'Ciudad del sur de la isla, en la bahía de Jagua, con un centro histórico Patrimonio de la Humanidad.', 'Calle y número, entre qué calles, reparto y municipio.', 'Brisa marina y humedad: protege lo metálico y lo electrónico en bolsa.' ),
		),
	);
	return isset( $c[ $s ] ) ? $c[ $s ] : array();
}

/* Registro de celulares: dónde es obligatorio registrar el IMEI de un equipo traído del extranjero. */
function grenvios_bpt_imei( $s ) {
	$i = array(
		'colombia' => 'En Colombia un celular traído del extranjero debe registrar su IMEI para funcionar en las redes del país: tu destinatario hace el registro con su operador al recibirlo.',
		'ecuador'  => 'En Ecuador los celulares que entran del extranjero deben estar registrados y homologados para usarse en las redes del país: tu destinatario lo gestiona al recibirlo.',
	);
	return isset( $i[ $s ] ) ? $i[ $s ] : '';
}

/* Estación de allí respecto a Perú. */
function grenvios_bpt_estacion( $s ) {
	$e = array(
		'ecuador'        => 'Ecuador está sobre la línea ecuatorial: no hay estaciones marcadas y el clima lo decide la región. Ropa abrigada para Quito o Cuenca; fresca para Guayaquil y la Costa.',
		'colombia'       => 'Colombia está junto al ecuador: no hay estaciones, el clima lo decide la altura. Abrigo para Bogotá, ropa ligera para la Costa Caribe y Cali.',
		'chile'          => 'Chile está en el mismo hemisferio que Perú y tiene las mismas estaciones, pero con inviernos más fríos y lluviosos en el centro y el sur: para junio a agosto, ropa de abrigo de verdad.',
		'bolivia'        => 'Bolivia comparte estaciones con Perú, pero el clima lo decide la altura: abrigo para La Paz y El Alto todo el año, ropa ligera para Santa Cruz.',
		'argentina'      => 'Argentina comparte estaciones con Perú, pero con inviernos bastante más fríos en Buenos Aires, el centro y el sur: lo que aquí es abrigo ligero allí se queda corto.',
		'estados-unidos' => 'Estados Unidos está en el hemisferio norte: sus estaciones son las opuestas a las de Perú. Cuando aquí es verano, en Nueva York o Nueva Jersey es invierno.',
		'espana'         => 'España está en el hemisferio norte: estaciones opuestas a las de Perú. La ropa de invierno que envías en mayo llega para el verano de allí.',
		'venezuela'      => 'Venezuela es tropical y no tiene estaciones marcadas: ropa ligera para casi todo el país y algo de abrigo solo para Mérida y las zonas altas.',
		'cuba'           => 'Cuba es tropical: calor todo el año, con un invierno suave. Ropa ligera y fresca es la que más se usa.',
	);
	return isset( $e[ $s ] ) ? $e[ $s ] : '';
}

function grenvios_bpt_productos() {
	return array( 'celulares-y-laptops', 'perfumes-y-cosmeticos', 'ropa-y-calzado', 'artesania', 'repuestos', 'superalimentos', 'joyas-y-plata', 'utiles-escolares', 'regalos-de-cumpleanos' );
}

function grenvios_bpt_claves( $slug = '' ) {
	$slugs = $slug !== '' ? array( $slug ) : array_keys( function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array() );
	$out = array();
	foreach ( $slugs as $s ) {
		foreach ( array_keys( grenvios_bpt_ciudades( $s ) ) as $ciu ) $out[] = 'envios-a-' . sanitize_title( remove_accents( $ciu ) ) . '-' . $s;
		foreach ( grenvios_bpt_productos() as $k ) $out[] = 'enviar-' . $k . '-a-' . $s;
	}
	return $out;
}

add_filter( 'grenvios_bp_plantillas', function ( $out, $d, $n ) {
	if ( ! function_exists( 'grenvios_perfil_de' ) || ! function_exists( 'grenvios_ppl_datos' ) ) return $out;
	$s  = $d['slug'];
	$pf = grenvios_perfil_de( $s );
	$xx = grenvios_ppl_datos();
	if ( ! $pf || empty( $xx[ $s ] ) ) return $out;

	$x    = $xx[ $s ];
	$p    = $d['title'];
	$f    = grenvios_bp_frases( $d );
	$dos  = $f['dos_vias'];
	$casa = ! empty( $d['casa'] );
	$orig = function_exists( 'grenvios_bp_origen_sufijo' ) ? grenvios_bp_origen_sufijo() : 'desde-peru';
	$guia = function ( $k ) use ( $orig ) { return '%P:' . $k . '-' . $orig . '%'; };
	$faq  = function ( $pares ) { return function_exists( 'grenvios_ga_faq' ) ? grenvios_ga_faq( $pares ) : ''; };
	$r    = function_exists( 'grenvios_perfil_reglas' ) ? grenvios_perfil_reglas( $s, $d ) : array( 'medicinas' => '', 'alimentos' => '', 'baterias' => '' );
	$ficha = '<a href="%H%/destinos/' . $s . '/">envíos a ' . $p . '</a>';
	$entrega = $casa
		? 'A ' . $p . ' entregamos a domicilio: el repartidor necesita la dirección completa y un teléfono que conteste.'
		: 'En ' . $p . ' el envío se retira en la agencia local con el ' . $pf['documento'] . ' del destinatario; el nombre del envío debe coincidir con el del documento.';
	$cotiza = '<a href="%H%/cotizar/">Cotiza tu envío a ' . $p . '</a> y te confirmamos precio y plazo.';

	/* ── Ciudades ───────────────────────────────────────────────────── */
	foreach ( grenvios_bpt_ciudades( $s ) as $ciu => $cd ) {
		$cs = sanitize_title( remove_accents( $ciu ) );
		$out[ 'envios-a-' . $cs . '-' . $s ] = array(
			'titulo'    => 'Envíos a ' . $ciu . ', ' . $p . ', desde Lima: dirección, clima y entrega',
			'categoria' => 'destinos', 'pilar' => 'destinos/' . $s,
			'extracto'  => 'Lo que conviene saber para enviar un paquete o documento a ' . $ciu . ' (' . $p . ') desde Lima: cómo escribir la dirección allí, cómo embalar para su clima y cómo se entrega.',
			'html'      => '
<p>' . $cd[0] . ' Es uno de los destinos a los que más enviamos dentro de ' . $p . ', y tiene sus propias reglas para que el envío llegue a la primera.</p>
<h2>Cómo escribir una dirección en ' . $ciu . '</h2>
<p>' . $cd[1] . '</p>
<p>Completa los datos con el nombre del destinatario tal como aparece en su ' . $pf['documento'] . ' y un teléfono con WhatsApp.</p>
<h2>Cómo llega el envío</h2>
<p>' . $x['frontera'] . ' ' . $entrega . '</p>
<h2>Embalar para ' . $ciu . '</h2>
<p>' . $cd[2] . '</p>
<h2>Fechas que mueven los envíos</h2>
<p>' . $x['temporada'] . '</p>
<p>Plazos y condiciones de la ruta, en la ficha de ' . $ficha . '; el detalle de otras ciudades, en <a href="' . $guia( 'ciudades-de-' . $s ) . '">envíos a las ciudades de ' . $p . '</a>. ' . $cotiza . '</p>
' . $faq( array(
	array( '¿Envían a ' . $ciu . ' desde Lima?', 'Sí. ' . $entrega ),
	array( '¿Cómo se escribe una dirección en ' . $ciu . '?', $cd[1] ),
) ),
		);
	}

	/* ── Productos ──────────────────────────────────────────────────── */
	$imei = grenvios_bpt_imei( $s );
	$out[ 'enviar-celulares-y-laptops-a-' . $s ] = array(
		'titulo'    => 'Enviar un celular o una laptop a ' . $p . ': batería, aduana y registro',
		'categoria' => 'aduanas', 'pilar' => 'envio-de-compras',
		'extracto'  => 'Si vas a enviar un celular, una laptop o una tablet a ' . $p . ': por qué vía viaja la batería, cómo lo revisa su aduana y qué tiene que hacer quien lo recibe.',
		'html'      => '
<p>Un celular o una laptop es de los envíos más pedidos y de los que más preguntas generan, porque juntan tres filtros: la batería, el valor y, en algunos países, el registro del equipo.</p>
<h2>La batería decide la vía</h2>
<p>' . $r['baterias'] . '</p>
<h2>La aduana de ' . $p . '</h2>
<p>' . $x['regimen'] . ' Declara la marca, el modelo y el valor real, y guarda la boleta: un equipo nuevo sin boleta es lo que más se retiene.</p>
<h2>Después de recibirlo</h2>
<p>' . ( $imei !== '' ? $imei : 'Si es un celular, confirma con el operador de ' . $p . ' que el modelo funciona en sus redes antes de enviarlo.' ) . '</p>
<h2>Cómo embalarlo</h2>
<ul class="gr-pseo-list">
<li><strong>En su caja original</strong>, dentro de otra caja con relleno.</li>
<li><strong>Apagado</strong> y con la batería en su sitio.</li>
<li><strong>Sin accesorios sueltos</strong> que golpeen la pantalla.</li>
</ul>
<p>Más detalle en <a href="%H%/servicios/envio-de-celulares-y-laptops/">envío de celulares y laptops</a> y en <a href="%H%/servicios/envio-de-compras/">envío de compras a ' . $p . '</a>.</p>
' . $faq( array(
	array( '¿Puedo enviar un celular a ' . $p . '?', $r['baterias'] ),
	array( '¿Hay que registrar un celular que llega a ' . $p . '?', $imei !== '' ? $imei : 'Consulta con el operador de allí si el modelo funciona en sus redes; te ayudamos con la declaración del envío.' ),
) ),
	);

	$out[ 'enviar-perfumes-y-cosmeticos-a-' . $s ] = array(
		'titulo'    => 'Enviar perfumes y cosméticos a ' . $p . ': qué se puede y cómo',
		'categoria' => 'aduanas', 'pilar' => 'que-se-puede-enviar',
		'extracto'  => 'Perfumes, cremas y maquillaje a ' . $p . ': qué no viaja por avión, quién controla los cosméticos allí y cómo declararlos.',
		'html'      => '
<p>Perfumes y cosméticos parecen un envío sencillo, pero mezclan dos problemas: muchos son líquidos o inflamables, y en ' . $p . ' los cosméticos tienen control sanitario propio.</p>
<h2>Lo que no viaja por avión</h2>
<p>El perfume lleva alcohol y es inflamable; los aerosoles y los esmaltes, también. Por vía aérea no se admiten. ' . ( $dos ? 'Hacia ' . $p . ' existe la vía terrestre, que admite algunos líquidos sellados: consúltanos con el producto concreto antes de prepararlo.' : 'Hacia ' . $p . ' operamos solo por vía aérea, así que perfumes y aerosoles no pueden enviarse.' ) . '</p>
<h2>Quién controla los cosméticos en ' . $p . '</h2>
<p>' . $x['sanitario'] . '</p>
<h2>Lo que sí se envía sin problema</h2>
<ul class="gr-pseo-list">
<li><strong>Cremas y maquillaje sólidos</strong>, cerrados y en su envase original.</li>
<li><strong>Jabones y productos en barra.</strong></li>
<li><strong>Cantidad de uso personal</strong>: varias unidades iguales se leen como mercadería.</li>
</ul>
<p>Revisa la lista completa en <a href="%H%/que-se-puede-enviar/">qué se puede enviar a ' . $p . '</a> o <a href="' . $guia( 'que-se-puede-enviar-a-' . $s ) . '">qué retiene la aduana de ' . $p . '</a>.</p>
' . $faq( array(
	array( '¿Puedo enviar perfume a ' . $p . '?', 'Por vía aérea no: es inflamable. ' . ( $dos ? 'Por vía terrestre, consúltanos según el producto.' : 'Hacia ' . $p . ' solo operamos por vía aérea.' ) ),
	array( '¿Quién controla los cosméticos que llegan a ' . $p . '?', $x['sanitario'] ),
) ),
	);

	$out[ 'enviar-ropa-y-calzado-a-' . $s ] = array(
		'titulo'    => 'Enviar ropa y calzado a ' . $p . ': qué mandar según el clima de allí',
		'categoria' => 'guias-de-envio', 'pilar' => 'envio-internacional-de-paquetes',
		'extracto'  => 'Qué ropa enviar a ' . $p . ' según su clima y estación, cómo comprimirla para pagar menos y cómo declararla.',
		'html'      => '
<p>La ropa es lo que más se envía a ' . $p . ', y el error más común no es de aduana: es mandar la ropa equivocada para el clima de allí.</p>
<h2>El clima de ' . $p . '</h2>
<p>' . grenvios_bpt_estacion( $s ) . '</p>
<h2>Cómo pagar menos</h2>
<ul class="gr-pseo-list">
<li><strong>Bolsas al vacío</strong> para lo blando: ' . ( $dos ? 'en la vía aérea es lo que más se nota.' : 'en una ruta aérea es lo que más ahorra.' ) . '</li>
<li><strong>Zapatos con algo dentro</strong>: medias o accesorios, para no enviar aire.</li>
<li><strong>Un solo bulto</strong> en lugar de varios.</li>
</ul>
<h2>Cómo declararla</h2>
<p>Pieza por pieza y con valor real. ' . $x['regimen'] . '</p>
<p>' . $entrega . ' Prepara tu caja con el servicio de <a href="%H%/servicios/envio-internacional-de-paquetes/">envío de paquetes a ' . $p . '</a>; para el tamaño, <a href="' . $guia( 'que-caja-usar-para-enviar-a-' . $s ) . '">qué caja usar para enviar a ' . $p . '</a>.</p>
' . $faq( array(
	array( '¿Qué ropa conviene enviar a ' . $p . '?', grenvios_bpt_estacion( $s ) ),
	array( '¿La ropa usada paga impuestos al llegar a ' . $p . '?', 'Se declara igual, pieza por pieza y con un valor razonable. ' . $x['regimen'] ),
) ),
	);

	$out[ 'enviar-artesania-a-' . $s ] = array(
		'titulo'    => 'Enviar artesanía peruana a ' . $p . ': madera, textiles y cerámica',
		'categoria' => 'guias-de-envio', 'pilar' => 'envio-internacional-de-paquetes',
		'extracto'  => 'Cómo enviar artesanía peruana a ' . $p . ': qué materiales revisa su control sanitario, cómo embalar cerámica y textiles y qué no puede salir del Perú.',
		'html'      => '
<p>La artesanía peruana es de los regalos más enviados a ' . $p . ', pero no todos los materiales pasan igual. La madera, las fibras vegetales, las semillas y las plumas los revisa el control agropecuario; la cerámica y el vidrio, solo el embalaje.</p>
<h2>Qué materiales se revisan en ' . $p . '</h2>
<p>' . $x['sanitario'] . ' Una pieza de madera sin tratar, con corteza o con semillas puede retenerse: si es para vender, pide al artesano que esté tratada y que lo diga la boleta.</p>
<h2>Lo que no puede salir del Perú</h2>
<p>Las piezas antiguas o que formen parte del patrimonio cultural no pueden exportarse. Si envías una réplica, conserva la boleta del artesano o de la tienda.</p>
<h2>Cómo embalarla</h2>
<ul class="gr-pseo-list">
<li><strong>Cerámica:</strong> cada pieza envuelta por separado, con relleno, en caja de doble pared.</li>
<li><strong>Textiles:</strong> en bolsa sellada. ' . $x['clima'] . '</li>
<li><strong>Retablos y madera:</strong> esquinas protegidas y sin espacio para moverse.</li>
</ul>
<p>Lo explicamos con más detalle en <a href="%H%/servicios/envio-de-artesanias-al-extranjero/">envío de artesanías al extranjero</a>, y preparamos tu caja con el servicio de <a href="%H%/servicios/envio-internacional-de-paquetes/">envío de paquetes a ' . $p . '</a>.</p>
' . $faq( array(
	array( '¿Se puede enviar artesanía de madera a ' . $p . '?', 'Sí, si está tratada y sin corteza ni semillas. ' . $x['sanitario'] ),
	array( '¿Cómo envío cerámica a ' . $p . ' sin que se rompa?', 'Cada pieza envuelta por separado, con relleno sin huecos y en caja de doble pared. La revisamos al recibirla.' ),
) ),
	);

	$out[ 'enviar-repuestos-a-' . $s ] = array(
		'titulo'    => 'Enviar repuestos a ' . $p . ': piezas de auto, maquinaria y electrónica',
		'categoria' => 'empresas', 'pilar' => 'carga-internacional',
		'extracto'  => 'Cómo enviar repuestos a ' . $p . ': qué piezas tienen restricciones, cómo declararlas para su aduana y cuándo conviene enviarlas como carga.',
		'html'      => '
<p>Un repuesto suele ser urgente, pesado y difícil de describir: tres razones para prepararlo bien. Hacia ' . $p . ' estas son las reglas.</p>
<h2>Piezas con restricciones</h2>
<ul class="gr-pseo-list">
<li><strong>Baterías:</strong> ' . $r['baterias'] . '</li>
<li><strong>Piezas con aceite o combustible</strong> (motores, bombas): deben ir limpias y drenadas.</li>
<li><strong>Aerosoles y líquidos</strong> (lubricantes, pinturas): no viajan por avión.</li>
</ul>
<h2>Cómo declararlas</h2>
<p>Con el nombre técnico y el código de la pieza, no «repuesto». ' . $x['regimen'] . '</p>
<h2>Por dónde viajan</h2>
<p>' . $x['frontera'] . ' ' . ( $dos ? 'Para piezas pesadas que no corren prisa, la vía terrestre suele salir mejor; para una pieza que tiene parada una máquina, la aérea.' : 'Al ser una ruta aérea, el peso y el volumen de la pieza pesan mucho en la tarifa: embala ajustado.' ) . '</p>
<p>Para volúmenes o piezas grandes, <a href="%H%/servicios/carga-internacional/">carga internacional a ' . $p . '</a>; lo explicamos también en <a href="%H%/envio-de-repuestos-al-extranjero/">envío de repuestos al extranjero</a>.</p>
' . $faq( array(
	array( '¿Puedo enviar una batería de auto a ' . $p . '?', $r['baterias'] ),
	array( '¿Cómo declaro un repuesto para ' . $p . '?', 'Con su nombre técnico, código de pieza y valor real, y con la boleta o factura.' ),
) ),
	);

	$out[ 'enviar-superalimentos-a-' . $s ] = array(
		'slug'      => 'enviar-maca-quinua-cafe-y-cacao-a-' . $s,
		'titulo'    => 'Enviar maca, quinua, café y cacao peruanos a ' . $p . ': qué se permite',
		'categoria' => 'aduanas', 'pilar' => 'envio-de-alimentos',
		'extracto'  => 'Si quieres enviar maca, quinua, café o cacao del Perú a ' . $p . ': si se permite por esta ruta, qué organismo lo controla y cómo prepararlo.',
		'html'      => '
<p>Maca, quinua, café y cacao son de los productos peruanos más buscados fuera del país. Enviarlos a ' . $p . ' depende de dos cosas: la vía y el control sanitario de allí.</p>
<h2>¿Se pueden enviar a ' . $p . '?</h2>
<p>' . $r['alimentos'] . '</p>
<h2>Quién los controla en ' . $p . '</h2>
<p>' . $x['sanitario'] . '</p>
<h2>Cómo prepararlos</h2>
<ul class="gr-pseo-list">
<li><strong>Sellados de fábrica</strong>, con etiqueta, registro y fecha de vencimiento.</li>
<li><strong>Nada a granel ni casero</strong>: es lo primero que se retiene.</li>
<li><strong>Cantidad de consumo personal</strong>; para vender, es otro régimen.</li>
</ul>
<p>Si vas a vender en ' . $p . ', lee <a href="' . $guia( 'vender-productos-peruanos-en-' . $s ) . '">cómo vender productos peruanos en ' . $p . '</a>. Las condiciones generales, en <a href="%H%/servicios/envio-de-alimentos/">envío de alimentos a ' . $p . '</a>.</p>
' . $faq( array(
	array( '¿Puedo enviar maca o quinua a ' . $p . '?', $r['alimentos'] ),
	array( '¿Quién controla los alimentos que llegan a ' . $p . '?', $x['sanitario'] ),
) ),
	);

	$out[ 'enviar-joyas-y-plata-a-' . $s ] = array(
		'titulo'    => 'Enviar joyas y plata peruana a ' . $p . ': valor declarado y seguro',
		'categoria' => 'guias-de-envio', 'pilar' => 'seguro-de-envios',
		'extracto'  => 'Cómo enviar joyas y piezas de plata peruana a ' . $p . ': cómo declarar su valor, qué cubre el seguro y cómo embalarlas.',
		'html'      => '
<p>La plata peruana es un regalo clásico y una pieza de valor: por eso se envía distinto que una caja de ropa. Lo que más importa es el valor declarado, porque de él dependen la aduana y el seguro.</p>
<h2>Cómo declarar el valor</h2>
<p>El precio real de cada pieza, con su boleta. ' . $x['unidades'] . '</p>
<h2>La aduana de ' . $p . '</h2>
<p>' . $x['regimen'] . '</p>
<h2>El seguro</h2>
<p>Cubre hasta el valor declarado. Una joya declarada por debajo de lo que vale queda sin cobertura real.</p>
<h2>Cómo embalarlas</h2>
<ul class="gr-pseo-list">
<li><strong>Estuche o bolsa individual</strong> para cada pieza.</li>
<li><strong>Caja pequeña rígida</strong> dentro de la caja del envío, con relleno.</li>
<li><strong>Sin señales externas</strong> de que lleva joyas.</li>
</ul>
<p>Revisa el <a href="%H%/seguro-de-envios/">seguro para envíos a ' . $p . '</a> y <a href="' . $guia( 'valor-declarado-envio-a-' . $s ) . '">cómo calcular el valor declarado de un envío a ' . $p . '</a>.</p>
' . $faq( array(
	array( '¿Puedo enviar joyas de plata a ' . $p . '?', 'Sí, declaradas pieza por pieza con su valor real y su boleta, y con seguro.' ),
	array( '¿En qué moneda declaro una joya enviada a ' . $p . '?', $x['unidades'] ),
) ),
	);

	$est = function_exists( 'grenvios_bpm_estudios' ) ? grenvios_bpm_estudios( $s ) : '';
	if ( $est !== '' ) {
		$out[ 'enviar-utiles-escolares-a-' . $s ] = array(
			'titulo'    => 'Enviar útiles y uniformes escolares a ' . $p . ': cuándo despachar',
			'categoria' => 'guias-de-envio', 'pilar' => 'envio-internacional-de-paquetes',
			'extracto'  => 'Cuándo empiezan las clases en ' . $p . ', con cuánta anticipación enviar útiles y uniformes desde {{origen_pais}} y cómo declararlos.',
			'html'      => '
<p>La vuelta a clases es una de las fechas con más envíos familiares a ' . $p . '. La clave no es qué enviar, sino cuándo: el calendario de allí no es el de Perú.</p>
<h2>Cuándo empiezan las clases en ' . $p . '</h2>
<p>' . $est . '</p>
<h2>Con cuánta anticipación despachar</h2>
<p>Cuenta el plazo de la ruta y suma una o dos semanas de margen: en las semanas previas al inicio de clases se juntan muchos envíos. ' . $x['feriados'] . '</p>
<h2>Cómo declararlos</h2>
<p>Pieza por pieza y como uso personal. Veinte cuadernos iguales pueden leerse como mercadería: si es para varios niños, dilo en la descripción. ' . $x['regimen'] . '</p>
<h2>Cómo embalarlos</h2>
<ul class="gr-pseo-list">
<li><strong>Libros y cuadernos</strong> al fondo, en bolsa contra la humedad.</li>
<li><strong>Uniformes</strong> doblados o al vacío.</li>
<li><strong>Sin pegamentos líquidos ni aerosoles</strong> si va por avión.</li>
</ul>
<p>Prepara la caja con el servicio de <a href="%H%/servicios/envio-internacional-de-paquetes/">envío de paquetes a ' . $p . '</a>; para fechas especiales, <a href="' . $guia( 'enviar-a-' . $s . '-en-fechas-clave' ) . '">enviar a ' . $p . ' en fechas clave</a>.</p>
' . $faq( array(
	array( '¿Cuándo empiezan las clases en ' . $p . '?', $est ),
	array( '¿Puedo enviar varios cuadernos iguales a ' . $p . '?', 'Sí, para uso personal o familiar; indícalo en la descripción para que no se lea como mercadería.' ),
) ),
		);
	}

	$out[ 'enviar-regalos-de-cumpleanos-a-' . $s ] = array(
		'titulo'    => 'Enviar un regalo de cumpleaños a ' . $p . ': que llegue a tiempo',
		'categoria' => 'guias-de-envio', 'pilar' => 'envio-internacional-de-paquetes',
		'extracto'  => 'Cómo enviar un regalo de cumpleaños a ' . $p . ' para que llegue antes de la fecha: cuándo despachar, qué evitar y cómo avisar a quien lo recibe.',
		'html'      => '
<p>Un regalo de cumpleaños tiene una fecha que no se mueve, así que se planifica hacia atrás: desde el día del cumpleaños hasta el día en que tiene que salir de {{origen_ciudad}}.</p>
<h2>Cuándo despachar</h2>
<p>Suma el plazo de la ruta, una semana de margen y los feriados de allí. ' . $x['feriados'] . '</p>
<h2>Qué evitar en el regalo</h2>
<ul class="gr-pseo-list">
<li><strong>Alimentos:</strong> ' . $r['alimentos'] . '</li>
<li><strong>Aparatos con batería:</strong> ' . $r['baterias'] . '</li>
<li><strong>Perfumes y aerosoles</strong> por vía aérea.</li>
</ul>
<h2>La sorpresa y la entrega</h2>
<p>' . $entrega . ' Si quieres que sea sorpresa, avisa a otra persona de la casa para que esté atenta. ' . $x['horario'] . '</p>
<p>Declara el regalo pieza por pieza y con valor real, aunque sea un regalo. Lo preparamos con el servicio de <a href="%H%/servicios/envio-internacional-de-paquetes/">envío de paquetes a ' . $p . '</a>; para encomiendas familiares, <a href="' . $guia( 'enviar-encomiendas-a-familiares-en-' . $s ) . '">enviar regalos y encomiendas a ' . $p . '</a>.</p>
' . $faq( array(
	array( '¿Con cuánta anticipación envío un regalo de cumpleaños a ' . $p . '?', 'El plazo de la ruta más una semana de margen, y más si hay feriados allí en esas fechas.' ),
	array( '¿Tengo que declarar el valor de un regalo enviado a ' . $p . '?', 'Sí: todo envío se declara con su valor real, también los regalos.' ),
) ),
	);

	/* ── Secciones de país por tema ─────────────────────────────────────
	 * Se insertan antes de las preguntas frecuentes. Cada tema recibe los
	 * datos del país que responden a sus dudas siguientes, no un bloque fijo. */
	$sec = array(
		'regimen'   => array( 'Al llegar a la aduana de ' . $p, $x['regimen'] ),
		'unidades'  => array( 'Moneda y valor declarado', $x['unidades'] ),
		'entrega'   => array( 'Cómo se entrega en ' . $p, $entrega . ' ' . $x['horario'] ),
		'feriados'  => array( 'Feriados de ' . $p . ' que alargan el plazo', $x['feriados'] ),
		'temporada' => array( 'Cuándo se envía más a ' . $p, $x['temporada'] ),
		'frontera'  => array( 'Por dónde viaja tu envío', $x['frontera'] ),
		'clima'     => array( 'Embalar para el clima de ' . $p, $x['clima'] ),
		'quien'     => array( 'Quién envía a ' . $p, 'En esta ruta envían sobre todo ' . $n['quien'] . '. Lo que más viaja: ' . $n['productos'] . '.' ),
		'via'       => array( 'La vía hacia ' . $p, $n['via'] . ' ' . $n['consejo'] ),
	);
	$extra = array(
		'enviar-celulares-y-laptops-a-'   => array( 'unidades', 'entrega', 'feriados' ),
		'enviar-perfumes-y-cosmeticos-a-' => array( 'regimen', 'unidades', 'entrega' ),
		'enviar-ropa-y-calzado-a-'        => array( 'temporada', 'via', 'feriados' ),
		'enviar-artesania-a-'             => array( 'regimen', 'entrega' ),
		'enviar-repuestos-a-'             => array( 'unidades', 'entrega', 'feriados' ),
		'enviar-superalimentos-a-'        => array( 'regimen', 'quien', 'temporada', 'entrega' ),
		'enviar-joyas-y-plata-a-'         => array( 'entrega', 'clima', 'feriados' ),
		'enviar-utiles-escolares-a-'      => array( 'via', 'entrega' ),
		'enviar-regalos-de-cumpleanos-a-' => array( 'temporada', 'via', 'clima' ),
		'envios-a-'                       => array( 'feriados', 'via', 'regimen' ),
	);
	foreach ( $out as $k => &$g ) {
		if ( substr( $k, -strlen( '-' . $s ) ) !== '-' . $s ) continue;
		foreach ( $extra as $pre => $lista ) {
			if ( strpos( $k, $pre ) !== 0 || ! in_array( $k, grenvios_bpt_claves( $s ), true ) ) continue;
			$h = '';
			foreach ( $lista as $b ) if ( ! empty( $sec[ $b ][1] ) ) $h .= "\n<h2>" . $sec[ $b ][0] . '</h2>' . "\n<p>" . $sec[ $b ][1] . '</p>';
			/* Antes del párrafo de cierre (enlaces y cotización): ese va justo antes de las preguntas. */
			$pos = strpos( $g['html'], '<h2>Preguntas frecuentes</h2>' );
			if ( $pos !== false ) {
				$cierre = strrpos( substr( $g['html'], 0, $pos ), '<p>' );
				if ( $cierre !== false ) $pos = $cierre;
			}
			$g['html'] = $pos !== false ? substr( $g['html'], 0, $pos ) . $h . "\n" . substr( $g['html'], $pos ) : $g['html'] . $h;
			break;
		}
	}
	unset( $g );

	return $out;
}, 35, 3 );
