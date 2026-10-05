<?php
/**
 * ══════════════════════════════════════════════════════════════════════════
 *  Contenido local de las páginas de cada ruta: lo que solo vale para ESE país
 * ══════════════════════════════════════════════════════════════════════════
 *
 * MEDICIÓN (2026-10-02, 5-gramas con el país enmascarado, solo contenido
 * principal): frente a Perú, las páginas de cada ruta ya eran distintas
 * (0,00–0,36); entre rutas, no: /ec/aduana-de-ecuador/ y /co/aduana-de-colombia/
 * compartían el 0,64–0,82 del texto. La plantilla es la misma y solo cambia el
 * nombre del país, así que Google ve nueve copias.
 *
 * Aquí va lo que un redactor local escribiría y que NO se puede copiar de una
 * ruta a otra: qué organismo sanitario controla qué, cómo trata la aduana un
 * envío personal, si el país acepta la apostilla y en qué idioma, la diferencia
 * horaria con Lima, sus feriados y temporadas, por dónde entra el envío, el
 * clima para el que hay que embalar y las unidades en que se mide.
 *
 * Cada página de la ruta recibe DOS de estos bloques, elegidos por su tema
 * (grenvios_ppl_matriz) y con una entrada propia de la página, para que dentro
 * de la misma ruta tampoco se repitan. También alimenta sus preguntas
 * frecuentes. Sin cifras de tarifa ni plazos: eso, «te lo confirmamos al cotizar».
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/* ─────────────────────────────────────────────────────────────────────────
 * 1) Datos locales por país (verificables, sin tarifas)
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_ppl_datos() {
	return apply_filters( 'grenvios_ppl_datos', array(
		'ecuador' => array(
			'sanitario' => 'En Ecuador hay dos filtros además de la aduana: la ARCSA (Agencia de Regulación, Control y Vigilancia Sanitaria) controla alimentos procesados, medicamentos y cosméticos, y Agrocalidad, todo lo de origen vegetal o animal: semillas, plantas, frutas frescas, carnes y lácteos.',
			'regimen'   => 'Ecuador tiene un régimen de courier propio para los envíos personales pequeños, con un cupo por persona y por año. Si el envío lo supera o el contenido parece mercadería —varias unidades iguales, productos nuevos en caja—, pasa a un trámite de importación más largo. Que la descripción deje claro que es para uso personal ahorra días.',
			'apostilla' => 'Ecuador forma parte del Convenio de la Apostilla: un documento peruano apostillado por la Cancillería del Perú vale allí sin más legalizaciones. Y como el trámite se hace en español, no hace falta traducirlo.',
			'horario'   => 'Ecuador continental tiene la misma hora que Lima durante todo el año; Galápagos va una hora por detrás. Puedes coordinar la entrega en tiempo real, sin cálculos.',
			'feriados'  => 'Los feriados que más frenan la aduana y el reparto son el Carnaval (febrero o marzo, cambia cada año), el 10 de agosto, el 9 de octubre en Guayaquil y el 3 de noviembre en Cuenca. Ecuador suele trasladar feriados para armar «puentes» de varios días.',
			'temporada' => 'Las semanas más cargadas son diciembre y la previa del Día de la Madre (segundo domingo de mayo): conviene despachar con una o dos semanas de margen.',
			'frontera'  => 'Por carretera, el envío sale de Lima hacia el norte y cruza la frontera por Tumbes: Aguas Verdes del lado peruano, Huaquillas del ecuatoriano. Es el paso con más movimiento comercial entre los dos países, y por eso la vía terrestre es tan competitiva hacia Ecuador.',
			'clima'     => 'Ecuador tiene dos climas para embalar: la Costa (Guayaquil, Manta, Machala) es cálida y húmeda, así que textiles, papeles y cajas de cartón deben ir dentro de bolsa sellada; la Sierra (Quito, Cuenca) está en altura y castiga los envases mal cerrados.',
			'unidades'  => 'Ecuador usa el sistema métrico, como Perú: mide la caja en centímetros y pésala en kilos. La moneda es el dólar estadounidense, así que el valor declarado no necesita conversión.',
		),
		'colombia' => array(
			'sanitario' => 'En Colombia, el INVIMA controla alimentos, medicamentos, cosméticos y productos de aseo, y el ICA (Instituto Colombiano Agropecuario), los productos agropecuarios: semillas, plantas, carnes y lácteos. Un cosmético o un suplemento para vender necesita registro del INVIMA; para uso personal, que se vea que es personal.',
			'regimen'   => 'Colombia tiene una modalidad de tráfico postal y envíos urgentes para paquetes pequeños, con trámite simplificado ante la DIAN. Los envíos de más valor o con apariencia comercial pasan a importación ordinaria, con declaración completa. La descripción y el valor real deciden en cuál de los dos entra el tuyo.',
			'apostilla' => 'Colombia forma parte del Convenio de la Apostilla: un documento apostillado por la Cancillería del Perú se presenta allí sin legalización consular. Al estar en español, no necesita traducción oficial.',
			'horario'   => 'Colombia tiene la misma hora que Lima todo el año, sin horario de verano. Lo que escribas por la mañana tu destinatario lo lee por la mañana.',
			'feriados'  => 'Colombia es de los países con más festivos del continente y, por la Ley Emiliani, muchos se trasladan al lunes: hay más de una decena de «puentes» al año en los que no se reparte ni se despacha aduana. Los fijos que más pesan son el 20 de julio y el 7 de agosto.',
			'temporada' => 'Diciembre (novenas y Navidad) y mayo, por el Día de la Madre (segundo domingo), concentran los envíos familiares. Despachar con margen es la diferencia entre llegar antes o después de la fecha.',
			'frontera'  => 'Colombia no tiene un paso de carretera directo y habitual con Perú, así que la ruta terrestre es larga; la aérea sale de Lima y llega en menos días. Por eso hacia Colombia la vía se decide sobre todo por el tamaño del bulto.',
			'clima'     => 'Colombia cambia de clima según la altura: Bogotá es fría y está a más de 2 600 m, mientras que Barranquilla y Cartagena son calurosas y húmedas. Para la Costa Caribe, sella textiles, papeles y cartón en bolsa plástica.',
			'unidades'  => 'Colombia usa el sistema métrico: centímetros y kilos, igual que aquí. El valor declarado se expresa en el equivalente en dólares del valor real de lo que envías.',
		),
		'chile' => array(
			'sanitario' => 'En Chile el filtro más estricto no es la aduana sino el SAG (Servicio Agrícola y Ganadero): controla todo lo de origen vegetal o animal —frutas, semillas, frutos secos, carnes, lácteos, miel, madera sin tratar— y no declarar un producto de ese tipo puede acabar en multa. Medicamentos y cosméticos los regula el ISP (Instituto de Salud Pública).',
			'regimen'   => 'Chile distingue los envíos de bajo valor, que pasan con trámite simplificado, de los de mayor valor, para los que Aduanas pide declaración formal y el pago de derechos e IVA. El valor declarado decide el camino, así que declarar lo real es lo que evita sorpresas.',
			'apostilla' => 'Chile se sumó al Convenio de la Apostilla en 2016: los documentos peruanos apostillados se presentan allí sin legalización consular. Están en español, así que no requieren traducción.',
			'horario'   => 'Chile continental va una hora por delante de Lima en invierno y dos en verano (de septiembre a abril, aproximadamente). Si coordinas una entrega en Santiago, súmale esa diferencia a tu reloj.',
			'feriados'  => 'Los feriados que más frenan los envíos son el 18 y 19 de septiembre (Fiestas Patrias), que en la práctica paralizan el país varios días, además del 21 de mayo, el 8 de diciembre y la Semana Santa.',
			'temporada' => 'Fiestas Patrias, diciembre y el Día de la Madre (segundo domingo de mayo) son las fechas con más encomiendas. En septiembre, despacha antes del día 10 si quieres que llegue antes del «dieciocho».',
			'frontera'  => 'Por carretera, el envío baja de Lima al sur y cruza la frontera entre Tacna y Arica, por el paso Santa Rosa–Chacalluta, el único paso terrestre entre los dos países. Desde Arica sigue a Santiago y al resto de regiones.',
			'clima'     => 'Santiago es seco y el norte, desértico; el sur (Temuco, Puerto Montt) es lluvioso casi todo el año. Para las regiones del sur, protege la caja de la humedad con bolsa y cinta en todas las juntas.',
			'unidades'  => 'Chile usa el sistema métrico: centímetros y kilos. Ten a mano la comuna del destinatario: en Chile pesa más que el código postal.',
		),
		'bolivia' => array(
			'sanitario' => 'En Bolivia, el SENASAG controla alimentos y productos agropecuarios, y la AGEMED (Agencia Estatal de Medicamentos), los medicamentos. Por eso medicinas y alimentos sellados viajan por carretera con su receta o su etiqueta de fábrica a la vista.',
			'regimen'   => 'La Aduana Nacional de Bolivia trata como comercial un envío con varias unidades iguales del mismo producto. Para una encomienda familiar, una lista de contenido clara —pieza por pieza y para uso personal— evita que se reclasifique y se demore.',
			'apostilla' => 'Bolivia forma parte del Convenio de la Apostilla desde 2018: los documentos peruanos apostillados se aceptan allí sin legalización consular, y al estar en español no necesitan traducción.',
			'horario'   => 'Bolivia va una hora por delante de Lima todo el año. Cuando en Lima son las 9:00, en La Paz y Santa Cruz son las 10:00.',
			'feriados'  => 'Los feriados que más pesan son el Carnaval (febrero o marzo), que frena la actividad sobre todo en Oruro y el occidente, el 22 de enero, el 1 de mayo y el 6 de agosto, día de la Independencia.',
			'temporada' => 'En Bolivia el Día de la Madre se celebra el 27 de mayo, no el segundo domingo como en Perú: si envías un regalo, cuenta los días hasta esa fecha. Diciembre y Carnaval son las otras dos temporadas altas.',
			'frontera'  => 'Por carretera, el envío sube al altiplano por Puno y cruza la frontera en Desaguadero, a orillas del Titicaca. Desde allí sigue a La Paz y El Alto, y de ahí a Cochabamba y Santa Cruz.',
			'clima'     => 'La Paz y El Alto están a más de 3 600 m de altura. Con el cambio de presión, frascos y envases con aire pueden abrirse o gotear: ciérralos con cinta, mételos en bolsa sellada y deja algo de espacio en la tapa.',
			'unidades'  => 'Bolivia usa el sistema métrico, como Perú. El valor declarado se toma sobre lo que realmente vale lo que envías, aunque allí se pague en bolivianos.',
		),
		'argentina' => array(
			'sanitario' => 'En Argentina, el SENASA controla alimentos y productos de origen vegetal y animal, y la ANMAT, los medicamentos, cosméticos y productos médicos. Un alimento sin etiqueta de fábrica o un medicamento sin receta es lo primero que se retiene.',
			'regimen'   => 'Para recibir un envío courier en Argentina, el destinatario necesita su CUIT o CUIL —el número fiscal que se obtiene con el DNI—: sin ese dato, el envío no se libera en aduana. Pídeselo antes de despachar y escríbelo junto a su nombre.',
			'apostilla' => 'Argentina forma parte del Convenio de la Apostilla: los documentos peruanos apostillados se presentan allí sin legalización consular. Al estar en español, no requieren traducción.',
			'horario'   => 'Argentina va dos horas por delante de Lima todo el año. A las 9:00 de Lima, en Buenos Aires ya son las 11:00.',
			'feriados'  => 'Los feriados que más pesan son el 25 de mayo, el 20 de junio, el 9 de julio y los feriados «puente» que el gobierno fija cada año, además del Carnaval y la Semana Santa.',
			'temporada' => 'En Argentina el Día de la Madre es el tercer domingo de octubre, no en mayo: es la fecha que más regalos mueve desde Perú, junto con diciembre.',
			'frontera'  => 'Es una ruta muy larga por carretera y el tramo final dentro de Argentina también lo es: Buenos Aires recibe antes que Córdoba, Rosario o Mendoza, y la Patagonia suma varios días más.',
			'clima'     => 'Argentina tiene estaciones marcadas y opuestas a las del hemisferio norte. Si envías ropa a la familia, piensa en la estación que habrá allí cuando llegue la caja, no en la de aquí.',
			'unidades'  => 'Argentina usa el sistema métrico. Declara el valor real de lo que envías: la aduana argentina revisa con atención el valor declarado y conviene guardar la boleta de todo lo nuevo.',
		),
		'estados-unidos' => array(
			'sanitario' => 'En Estados Unidos, la FDA exige un aviso previo (Prior Notice) para los alimentos que entran al país, y el USDA controla carnes, frutas, semillas y plantas. Las medicinas, solo con receta y en cantidad de uso personal.',
			'regimen'   => 'Estados Unidos suspendió en 2025 la exención para envíos de bajo valor (el llamado «de minimis»): hoy un paquete pequeño también puede pagar aranceles al entrar. Antes de despachar te confirmamos qué aplica a tu envío, para que tu destinatario no reciba un cobro inesperado.',
			'apostilla' => 'Estados Unidos forma parte del Convenio de la Apostilla, así que un documento peruano apostillado no necesita legalización consular. Lo que sí suele pedir es una traducción certificada al inglés: consulta a la institución que lo recibe si la quiere hecha allí o aquí.',
			'horario'   => 'La costa este (Nueva York, Nueva Jersey, Florida) tiene la hora de Lima en invierno y una hora más de marzo a noviembre; California va dos o tres horas por detrás. Ten en cuenta el estado de tu destinatario antes de llamarlo.',
			'feriados'  => 'Los días que más frenan el reparto son el 4 de julio, Thanksgiving (cuarto jueves de noviembre) y Navidad. De Black Friday a fin de año los transportistas trabajan a plena capacidad y todo va más lento.',
			'temporada' => 'Thanksgiving, Navidad y el Día de la Madre (segundo domingo de mayo) son las tres fechas con más envíos familiares hacia Estados Unidos. Para Navidad, despacha antes de que empiece diciembre.',
			'frontera'  => 'Es una ruta aérea: el envío vuela desde Lima, pasa la revisión de la CBP en el punto de entrada a Estados Unidos y desde ahí sigue por la red de reparto nacional hasta la puerta.',
			'clima'     => 'Del frío del noreste en invierno al calor húmedo de Florida, el envío pasa por bodegas a distintas temperaturas. Chocolates, cremas y velas pueden deformarse: evítalos en verano o protégelos con aislante.',
			'unidades'  => 'En Estados Unidos se mide en libras y pulgadas: 1 kg son 2,2 lb y 1 pulgada son 2,54 cm. Si tu destinatario te pasa medidas de allí, conviértelas antes de pedir la cotización.',
		),
		'espana' => array(
			'sanitario' => 'España aplica las normas de la Unión Europea: en un envío personal desde fuera de la UE no pueden entrar carne, embutidos, leche ni productos lácteos. Los alimentos de origen vegetal envasados se revisan caso por caso, y las medicinas solo en cantidad de tratamiento personal.',
			'regimen'   => 'Desde julio de 2021, todo envío que llega a España desde fuera de la Unión Europea paga IVA de importación, sea cual sea su valor; los regalos entre particulares tienen una franquicia limitada. Te confirmamos al cotizar qué aplica, para que nadie pague de más al recibir.',
			'apostilla' => 'España forma parte del Convenio de la Apostilla y es el destino al que más documentos apostillados enviamos: nacionalidad, homologación de títulos y trámites de extranjería. Como se presentan en español, no necesitan traducción.',
			'horario'   => 'España peninsular va seis horas por delante de Lima en invierno y siete en verano (de finales de marzo a finales de octubre); Canarias, una hora menos. Lo que despachas por la tarde aquí, allí ya es de noche.',
			'feriados'  => 'Los días que más frenan los envíos son el 12 de octubre, el puente del 6 y 8 de diciembre, la Semana Santa y agosto, cuando media España está de vacaciones y muchas oficinas trabajan a medio gas.',
			'temporada' => 'En España los regalos llegan el 6 de enero, con los Reyes Magos, no solo en Navidad; el Día de la Madre es el primer domingo de mayo. Son las dos fechas con más envíos desde Perú.',
			'frontera'  => 'Es una ruta aérea: el envío vuela desde Lima, entra a la Unión Europea por la aduana española y sigue a toda la península. Canarias, Ceuta y Melilla quedan fuera del IVA de la Unión Europea y tienen su propio régimen; Baleares sí está dentro, pero el tramo hasta las islas suma unos días.',
			'clima'     => 'El clima no es el problema hacia España; lo es el trayecto largo. Un embalaje que aguante varios transbordos —caja de doble pared, relleno y cinta en H— es lo que llega intacto.',
			'unidades'  => 'España usa el sistema métrico y el euro: declara el valor real de lo que envías, que se convertirá a euros para calcular el IVA de importación.',
		),
		'venezuela' => array(
			'sanitario' => 'En Venezuela, el INSAI controla los productos de origen vegetal y animal. Por esta ruta aérea no se aceptan alimentos ni medicinas, así que una encomienda de ayuda familiar funciona mejor con ropa, calzado y artículos de higiene.',
			'regimen'   => 'El SENIAT revisa con atención las encomiendas con muchas unidades iguales: diez camisetas idénticas se leen como mercadería, no como ayuda familiar. Variar el contenido y declararlo pieza por pieza es lo que evita la retención.',
			'apostilla' => 'Venezuela forma parte del Convenio de la Apostilla: un documento peruano apostillado se presenta allí sin legalización consular, y al estar en español no requiere traducción.',
			'horario'   => 'Venezuela va una hora por delante de Lima todo el año. A las 9:00 de Lima, en Caracas son las 10:00.',
			'feriados'  => 'Los feriados que más pesan son el Carnaval y la Semana Santa, el 19 de abril, el 24 de junio (Batalla de Carabobo) y el 5 de julio, día de la Independencia.',
			'temporada' => 'Diciembre es la temporada alta por excelencia —los estrenos de fin de año— y septiembre, por el regreso a clases. El Día de la Madre es el segundo domingo de mayo.',
			'frontera'  => 'Es una ruta aérea con entrega puerta a puerta: el envío vuela desde Lima, pasa la aduana venezolana y se reparte en la casa del destinatario, sin que tenga que ir a buscarlo.',
			'clima'     => 'Venezuela es tropical y húmeda en casi todo el país: mete la ropa y el calzado en bolsas selladas dentro de la caja y no envíes productos que se derriten.',
			'unidades'  => 'Venezuela usa el sistema métrico. Declara el valor real de cada pieza: es la base de la revisión y del seguro.',
		),
		'cuba' => array(
			'sanitario' => 'Hacia Cuba aceptamos medicinas con su receta médica, que es lo que más se envía en esta ruta; los alimentos no viajan por vía aérea. Cada medicamento, en su envase original y con el nombre del paciente en la receta.',
			'regimen'   => 'La Aduana General de la República de Cuba clasifica cada envío y limita las cantidades de un mismo artículo: varias unidades iguales pueden leerse como comercio y no como ayuda familiar. La lista exacta del contenido es obligatoria y es lo que más agiliza la revisión.',
			'apostilla' => 'Cuba no forma parte del Convenio de la Apostilla: sus trámites suelen pedir legalización, no apostilla. Antes de enviar un documento, confirma con la institución cubana qué legalización exige.',
			'horario'   => 'Cuba tiene la misma hora que Lima en invierno y una hora más de marzo a noviembre, con su horario de verano. La conexión del destinatario puede ser intermitente: comparte con él el número de guía en cuanto lo tengas.',
			'feriados'  => 'Los feriados que más frenan la entrega son el 1 de enero, el 1 de mayo, los días alrededor del 26 de julio, el 10 de octubre y el 25 de diciembre.',
			'temporada' => 'Fin de año y el Día de las Madres (segundo domingo de mayo) son las fechas con más envíos a la isla. Con medicinas, mejor no esperar a una fecha: despacha cuando tengas la receta.',
			'frontera'  => 'Es una ruta aérea especializada: el envío vuela desde Lima, pasa la Aduana cubana y se entrega al destinatario. No hay vía terrestre posible, así que el peso volumétrico pesa mucho en la tarifa.',
			'clima'     => 'Cuba es cálida y húmeda todo el año: sella las medicinas y la ropa en bolsas dentro de la caja y evita los cartones finos, que se ablandan.',
			'unidades'  => 'Cuba usa el sistema métrico. Declara el valor real de cada pieza: la Aduana cubana valora el envío pieza por pieza.',
		),
	) );
}

/* ─────────────────────────────────────────────────────────────────────────
 * 2) Qué bloques lleva cada página y con qué entrada
 *    [ bloque, antetítulo, título (%s = país), frase de entrada propia de la página ]
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_ppl_matriz() {
	return apply_filters( 'grenvios_ppl_matriz', array(
		'home' => array(
			array( 'temporada', 'Fechas clave', 'Cuándo enviar a %s para llegar a tiempo', 'Las fechas que mueven más envíos a este país no son siempre las de Perú.' ),
			array( 'horario', 'Hora local', 'Diferencia horaria entre Lima y %s', 'Para coordinar con quien recibe, lo primero es saber qué hora es allí.' ),
		),
		'aduanas-e-impuestos' => array(
			array( 'regimen', 'Régimen de aduana', 'Cómo trata la aduana de %s un envío personal', 'Antes de hablar de impuestos, conviene saber en qué régimen entra tu envío.' ),
			array( 'sanitario', 'Controles sanitarios', 'Los permisos que se suman a la aduana en %s', 'La aduana no es el único organismo que revisa lo que entra.' ),
		),
		'que-se-puede-enviar' => array(
			array( 'sanitario', 'Quién controla qué', 'Qué organismos revisan lo que entra a %s', 'Que un producto esté permitido depende también del organismo sanitario del país.' ),
			array( 'clima', 'Embalar para el destino', 'Cómo preparar lo que envías para el clima de %s', 'Un producto permitido también puede llegar dañado si no se embala para el destino.' ),
		),
		'envio-de-alimentos' => array(
			array( 'sanitario', 'Control sanitario', 'Quién revisa los alimentos que llegan a %s', 'Con alimentos, el que decide no es solo la aduana.' ),
			array( 'temporada', 'Temporadas', 'Cuándo se envían más productos peruanos a %s', 'Los alimentos peruanos se envían sobre todo en unas pocas fechas del año.' ),
		),
		'envio-de-compras' => array(
			array( 'regimen', 'Compras y aduana', 'Cómo entran tus compras a %s', 'Una compra nueva, con caja y boleta, se mira distinto que un regalo usado.' ),
			array( 'unidades', 'Medidas y moneda', 'Medidas y valor declarado para %s', 'Para cotizar bien una compra hacen falta medidas exactas y su valor real.' ),
		),
		'envio-de-equipaje' => array(
			array( 'clima', 'Clima en destino', 'Prepara tu equipaje para el clima de %s', 'Quien se muda manda ropa para la vida de allí, no para la de aquí.' ),
			array( 'frontera', 'Ruta del equipaje', 'Por dónde viaja tu equipaje hasta %s', 'Saber por dónde entra el equipaje ayuda a elegir vía y plazo.' ),
		),
		'envio-internacional-de-documentos' => array(
			array( 'apostilla', 'Validez del documento', 'Apostilla e idioma: qué piden en %s', 'Un documento que llega rápido pero sin la legalización correcta no sirve.' ),
			array( 'horario', 'Coordinación', 'Hora de %s para coordinar la entrega', 'Con un documento urgente, cada hora cuenta, también la del país de destino.' ),
		),
		'apostilla-y-traduccion' => array(
			array( 'apostilla', 'Convenio de la Apostilla', '¿%s acepta la apostilla peruana?', 'La primera pregunta antes de tramitar nada es qué reconoce el país de destino.' ),
			array( 'feriados', 'Calendario', 'Feriados de %s que retrasan un trámite', 'Un documento apostillado a tiempo puede quedarse quieto por un feriado allí.' ),
		),
		'envio-internacional-de-paquetes' => array(
			array( 'clima', 'Embalaje', 'Embalar un paquete pensando en %s', 'El embalaje correcto depende del clima y del trayecto hasta el destino.' ),
			array( 'regimen', 'Al llegar', 'Cómo pasa tu paquete la aduana de %s', 'Un paquete bien embalado todavía tiene que pasar la revisión de entrada.' ),
		),
		'carga-internacional' => array(
			array( 'frontera', 'Ruta de la carga', 'Por dónde entra tu carga a %s', 'En carga, la ruta define costo, plazo y documentación.' ),
			array( 'sanitario', 'Permisos', 'Permisos sanitarios para carga hacia %s', 'Si tu carga es alimento, cosmético o agro, el permiso pesa tanto como el flete.' ),
		),
		'envios-para-empresas' => array(
			array( 'regimen', 'Envíos comerciales', 'Cuándo un envío a %s deja de ser personal', 'Para una empresa, lo importante es no caer en el régimen equivocado.' ),
			array( 'frontera', 'Logística', 'La ruta que siguen tus envíos a %s', 'Conocer la ruta permite planificar reposiciones y fechas de entrega.' ),
		),
		'peso-volumetrico' => array(
			array( 'unidades', 'Medidas', 'Cómo medir una caja para enviar a %s', 'El peso volumétrico empieza por medir bien, en las unidades correctas.' ),
			array( 'frontera', 'Vía y volumen', 'Cómo influye la ruta a %s en el peso que se cobra', 'El volumen pesa distinto según la vía por la que viaja el envío.' ),
		),
		'tiempos-de-entrega' => array(
			array( 'feriados', 'Feriados', 'Feriados de %s que alargan el plazo', 'El plazo se cuenta en días hábiles, y los feriados de destino no lo son.' ),
			array( 'frontera', 'Tramo internacional', 'El recorrido de un envío de Lima a %s', 'Saber por dónde pasa el envío explica por qué tarda lo que tarda.' ),
		),
		'rastreo-de-envios' => array(
			array( 'feriados', 'Si el seguimiento no avanza', 'Feriados en %s que detienen el seguimiento', 'Un seguimiento quieto durante días casi siempre coincide con un feriado allí.' ),
			array( 'horario', 'Hora local', 'Qué hora es en %s cuando revisas tu envío', 'Las actualizaciones del seguimiento llevan la hora del lugar donde ocurren.' ),
		),
		'contacto' => array(
			array( 'horario', 'Horario', 'Cómo coordinar entre Lima y %s', 'Escríbenos a la hora que te venga bien; para tu destinatario, ten en cuenta la suya.' ),
			array( 'temporada', 'Planifica', 'Fechas en las que conviene escribirnos antes', 'Hay semanas del año en las que conviene escribirnos con más anticipación.' ),
		),
		'cotizar' => array(
			array( 'regimen', 'Lo que no ves en la tarifa', 'Lo que la aduana de %s puede sumar a tu envío', 'Una buena cotización también avisa de lo que puede cobrarse en destino.' ),
			array( 'temporada', 'Fechas', 'Cotiza con tiempo para estas fechas en %s', 'En temporada alta, cotizar y despachar antes es lo que asegura el plazo.' ),
		),
		'como-enviar-un-paquete-al-extranjero' => array(
			array( 'clima', 'Antes de cerrar la caja', 'Cómo embalar para %s', 'Antes de cerrar la caja, piensa en el lugar donde se va a abrir.' ),
			array( 'horario', 'Coordina la entrega', 'Coordina con tu destinatario en %s', 'Avisa a quien recibe; para eso, conviene saber qué hora es allí.' ),
		),
		'envios-desde-provincias' => array(
			array( 'frontera', 'Ruta completa', 'De tu provincia a %s: el recorrido', 'Desde provincias el envío pasa primero por Lima y luego sigue esta ruta.' ),
			array( 'temporada', 'Con margen', 'Fechas de %s para las que conviene salir antes', 'Desde provincias se suma el tramo hasta Lima: en temporada alta, sal antes.' ),
		),
		'recojo-a-domicilio-lima' => array(
			array( 'horario', 'Coordinación', 'Recojo en Lima, entrega en %s', 'Mientras recogemos aquí, conviene avisar a quien recibe allí.' ),
			array( 'clima', 'Caja lista', 'Ten la caja lista para el clima de %s', 'Cuando pasamos a recoger, la caja tiene que estar preparada para el destino.' ),
		),
		'seguro-de-envios' => array(
			array( 'clima', 'Riesgos del trayecto', 'Qué puede dañar un envío a %s', 'El seguro cubre lo que pasa en el camino; un buen embalaje evita la mayoría.' ),
			array( 'regimen', 'Valor declarado', 'El valor declarado ante la aduana de %s', 'El valor que declaras es la base del seguro y también de la aduana.' ),
		),
		'nosotros' => array(
			array( 'frontera', 'Nuestra ruta', 'Cómo llegamos a %s', 'Llevamos años operando esta ruta y la conocemos tramo a tramo.' ),
			array( 'horario', 'Cerca aunque lejos', 'Te atendemos desde Lima, a la hora de %s', 'Te atendemos desde Lima, pero pensando en la hora de tu destinatario.' ),
		),
		'servicios' => array(
			array( 'frontera', 'La ruta', 'Por dónde viajan nuestros envíos a %s', 'Todos los servicios de esta ruta comparten el mismo recorrido.' ),
			array( 'regimen', 'Al llegar', 'Cómo entra un envío a %s', 'Sea un documento o una carga, al llegar pasa por la misma aduana.' ),
		),
		'preguntas-frecuentes' => array(
			array( 'apostilla', 'Documentos', 'Apostilla e idioma de los documentos para %s', 'Es la pregunta que más se repite cuando el envío lleva papeles.' ),
			array( 'feriados', 'Plazos', 'Feriados de %s que conviene conocer', 'Muchas preguntas por retrasos se responden mirando el calendario de allí.' ),
		),
	) );
}

/* Iconos por bloque. */
function grenvios_ppl_icono( $b ) {
	$i = array( 'sanitario' => 'fa-shield-heart', 'regimen' => 'fa-building-columns', 'apostilla' => 'fa-stamp', 'horario' => 'fa-clock', 'feriados' => 'fa-calendar-days', 'temporada' => 'fa-gift', 'frontera' => 'fa-route', 'clima' => 'fa-cloud-sun-rain', 'unidades' => 'fa-ruler-combined' );
	return isset( $i[ $b ] ) ? $i[ $b ] : 'fa-circle-info';
}

/* ─────────────────────────────────────────────────────────────────────────
 * 3) Render: tras el bloque de país, antes del perfil (prioridad 5)
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_ppl_html( $base, $pais ) {
	$datos = grenvios_ppl_datos();
	$m     = grenvios_ppl_matriz();
	if ( empty( $datos[ $pais ] ) || empty( $m[ $base ] ) ) return '';
	$d  = function_exists( 'grenvios_pais_datos' ) ? grenvios_pais_datos( $pais ) : array();
	$p  = ! empty( $d['title'] ) ? $d['title'] : ucfirst( $pais );
	$x  = $datos[ $pais ];

	$cards = '';
	$i = 0;
	foreach ( $m[ $base ] as $b ) {
		/* La 4.ª posición (frase de entrada) ya no se pinta: era la misma en las
		 * nueve rutas y restaba lo que el bloque aporta de propio. */
		list( $clave, $sub, $tit ) = $b;
		if ( empty( $x[ $clave ] ) ) continue;
		$texto = $x[ $clave ];
		/* El bloque de fechas lleva también el de temporada, y al revés: juntos
		 * responden «cuándo enviar». */
		if ( $clave === 'feriados' && ! empty( $x['temporada'] ) && $base !== 'apostilla-y-traduccion' ) $texto .= ' ' . $x['temporada'];
		$cards .= '<article class="gr-ppl-card wow fade-in-bottom" data-wow-delay="' . ( 100 + 120 * $i ) . 'ms">'
			. '<span class="gr-ppl-ic" aria-hidden="true"><i class="fa-solid ' . grenvios_ppl_icono( $clave ) . '"></i></span>'
			. '<p class="gr-ppl-sub">' . esc_html( $sub ) . '</p>'
			. '<h3>' . esc_html( sprintf( $tit, $p ) ) . '</h3>'
			. '<p>' . esc_html( $texto ) . '</p>'
			. '</article>';
		$i++;
	}
	if ( $cards === '' ) return '';
	return '<section class="srv-section gr-pais-sec gr-ppl padding"><div class="container">'
		. '<div class="srv-head text-center"><p class="sub-heading">Solo para ' . esc_html( $p ) . '</p><h2>Lo que cambia cuando envías a <span class="hl">' . esc_html( $p ) . '</span></h2></div>'
		. '<div class="gr-ppl-grid">' . $cards . '</div></div></section>';
}

add_filter( 'grenvios_pais_render_extra', function ( $html, $post_id ) {
	if ( ! function_exists( 'grenvios_perfil_pais_actual' ) ) return $html;
	$pais = grenvios_perfil_pais_actual();
	if ( $pais === '' ) return $html;
	if ( function_exists( 'grenvios_espejo_es' ) && grenvios_espejo_es( $post_id ) ) return $html;
	$base = function_exists( 'grenvios_canonical_slug' ) ? grenvios_canonical_slug( $post_id ) : '';
	if ( is_front_page() ) $base = 'home';
	if ( $base === '' ) return $html;
	return $html . grenvios_ppl_html( $base, $pais );
}, 5, 2 );

/* ─────────────────────────────────────────────────────────────────────────
 * 4) Preguntas frecuentes locales (van también al schema FAQPage)
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_ppl_faqs( $base, $pais ) {
	$datos = grenvios_ppl_datos();
	if ( empty( $datos[ $pais ] ) ) return array();
	$x = $datos[ $pais ];
	$d = function_exists( 'grenvios_pais_datos' ) ? grenvios_pais_datos( $pais ) : array();
	$p = ! empty( $d['title'] ) ? $d['title'] : ucfirst( $pais );
	$banco = array(
		'sanitario' => array( '¿Qué organismo revisa alimentos y medicinas que llegan a ' . $p . '?', $x['sanitario'] ),
		'regimen'   => array( '¿Cómo trata la aduana de ' . $p . ' un envío personal?', $x['regimen'] ),
		'apostilla' => array( '¿' . $p . ' acepta documentos peruanos apostillados?', $x['apostilla'] ),
		'horario'   => array( '¿Qué diferencia horaria hay entre Lima y ' . $p . '?', $x['horario'] ),
		'feriados'  => array( '¿Qué feriados de ' . $p . ' pueden retrasar mi envío?', $x['feriados'] ),
		'temporada' => array( '¿Cuándo conviene enviar a ' . $p . ' en temporada alta?', $x['temporada'] ),
		'frontera'  => array( '¿Por dónde viaja un envío de Lima a ' . $p . '?', $x['frontera'] ),
		'clima'     => array( '¿Cómo embalar un envío para el clima de ' . $p . '?', $x['clima'] ),
		'unidades'  => array( '¿En qué unidades mido y declaro un envío a ' . $p . '?', $x['unidades'] ),
	);
	$m = grenvios_ppl_matriz();
	if ( empty( $m[ $base ] ) ) return array();
	$out = array();
	foreach ( $m[ $base ] as $b ) if ( isset( $banco[ $b[0] ] ) ) $out[] = $banco[ $b[0] ];
	return $out;
}

/* Después del filtro del perfil (90): se añaden al principio, sin repetir. */
add_filter( 'grenvios_page_faqs', function ( $faqs, $slug ) {
	if ( is_admin() || ! function_exists( 'grenvios_perfil_pais_actual' ) ) return $faqs;
	$pais = grenvios_perfil_pais_actual();
	if ( $pais === '' ) return $faqs;
	if ( function_exists( 'grenvios_destinos' ) && array_key_exists( $slug, grenvios_destinos() ) ) return $faqs;
	$nuevas = grenvios_ppl_faqs( $slug, $pais );
	if ( ! $nuevas ) return $faqs;
	$vistas = array(); $out = array();
	foreach ( array_merge( $nuevas, (array) $faqs ) as $f ) {
		if ( ! is_array( $f ) || empty( $f[0] ) ) continue;
		$k = mb_strtolower( $f[0] );
		if ( isset( $vistas[ $k ] ) ) continue;
		$vistas[ $k ] = 1; $out[] = $f;
	}
	return $out;
}, 95, 2 );

/* ─────────────────────────────────────────────────────────────────────────
 * 5) Limpieza en las rutas: fuera lo que es de «todos los países»
 *
 *  · Tablas con los diez destinos («Restricciones vigentes por país»,
 *    «A dónde enviamos equipaje»…): en /ec/ solo interesa la fila de Ecuador.
 *    Se deja la cabecera y esa fila, y el título dice «en Ecuador».
 *  · El bloque «Preguntas frecuentes sobre <país>» del bloque de país, si la
 *    página ya tiene su sección de preguntas frecuentes (eran dos seguidas).
 * ───────────────────────────────────────────────────────────────────────── */
function grenvios_ppl_nombres_paises() {
	$out = array();
	foreach ( ( function_exists( 'grenvios_destinos' ) ? grenvios_destinos() : array() ) as $slug => $d ) {
		$pd = function_exists( 'grenvios_pais_datos' ) ? grenvios_pais_datos( $slug ) : array();
		$out[ $slug ] = ! empty( $pd['title'] ) ? $pd['title'] : ucfirst( $slug );
	}
	return $out;
}

function grenvios_ppl_limpiar_ruta( $html ) {
	if ( ! is_string( $html ) || $html === '' || ! is_singular( 'page' ) ) return $html;
	if ( ! function_exists( 'grenvios_perfil_pais_actual' ) ) return $html;
	$pais = grenvios_perfil_pais_actual();
	if ( $pais === '' ) return $html;
	$id = (int) get_queried_object_id();
	if ( function_exists( 'grenvios_espejo_es' ) && grenvios_espejo_es( $id ) ) return $html;
	if ( function_exists( 'grenvios_destinos' ) && function_exists( 'grenvios_canonical_slug' ) && array_key_exists( grenvios_canonical_slug( $id ), grenvios_destinos() ) ) return $html;
	$nombres = grenvios_ppl_nombres_paises();
	if ( empty( $nombres[ $pais ] ) ) return $html;
	$mio  = $nombres[ $pais ];
	$tiene_faq = strpos( $html, 'grenvios-faq-section' ) !== false;

	$r = preg_replace_callback( '~<section\b[^>]*>.*?</section>~s', function ( $m ) use ( $nombres, $mio, $tiene_faq ) {
		$sec = $m[0];
		/* FAQ duplicada del bloque de país. */
		if ( $tiene_faq && strpos( $sec, 'gr-pais-sec' ) !== false && strpos( $sec, 'gr-faq-item' ) !== false ) return '';
		if ( strpos( $sec, '<table' ) === false || strpos( $sec, 'gr-pais-sec' ) !== false ) return $sec;
		$otros = 0;
		foreach ( $nombres as $n ) if ( $n !== $mio && strpos( $sec, $n ) !== false ) $otros++;
		if ( $otros < 3 ) return $sec;
		/* Solo la fila del país; si no la hay, la sección sobra. */
		$quedan = 0;
		$t = preg_replace_callback( '~<tbody\b[^>]*>(.*?)</tbody>~s', function ( $tb ) use ( $mio, &$quedan ) {
			$filas = '';
			if ( preg_match_all( '~<tr\b.*?</tr>~s', $tb[1], $rr ) ) {
				foreach ( $rr[0] as $tr ) if ( strpos( $tr, $mio ) !== false ) { $filas .= $tr; $quedan++; }
			}
			return '<tbody>' . $filas . '</tbody>';
		}, $sec );
		if ( ! is_string( $t ) || ! $quedan ) return '';
		$t2 = preg_replace( '~(<h2\b[^>]*>[^<]*?)\s+(?:por|de cada)\s+pa[ií]s(?:\s+de\s+destino)?~su', '$1 en ' . $mio, $t, 1 );
		return is_string( $t2 ) ? $t2 : $t;
	}, $html );
	return is_string( $r ) ? $r : $html;
}
add_filter( 'grenvios_html_final', 'grenvios_ppl_limpiar_ruta', 61 );
