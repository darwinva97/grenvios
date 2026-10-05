# Grenvíos — Mapa de keywords, contenido y enlazado interno

Alcance de idiomas: **español (base) + inglés**. Una keyword por página, en cada
idioma. Este documento es el que se abre antes de escribir o editar cualquier
página.

> **Sobre los volúmenes:** las keywords de abajo son hipótesis fundadas en cómo se
> busca este servicio, no cifras medidas. Antes de reescribir una página,
> valídalas en Keyword Planner (Perú / español y Perú+EE.UU. / inglés) y en
> Search Console → Rendimiento → Consultas. Si una tiene volumen cero, sustitúyela
> por la variante que sí lo tenga: la estructura del plan no cambia.

---

## 1. La regla: una página = una keyword

Dos páginas peleando por la misma keyword se canibalizan y Google elige una (casi
siempre la que no querías). Por eso:

- Cada página declara su keyword en **SEO Grenvíos → Palabra clave objetivo**.
- La pantalla **SEO por página** marca en rojo las que se pisan.
- Esa misma keyword es el **texto ancla** con el que el resto del sitio la enlaza:
  así el enlace interno le dice a Google exactamente de qué trata la página.

## 2. Mapa en español

### Páginas de servicio (intención comercial: quien ya quiere enviar)

| Página | Keyword objetivo | Secundarias (van en H2 y en el cuerpo) | Contenido que hay que tener sí o sí |
|---|---|---|---|
| `/servicios/envio-internacional-de-paquetes/` | **envío de paquetes internacional** | enviar paquete al extranjero desde Perú · courier internacional Lima · cuánto cuesta enviar un paquete al extranjero | Tabla aéreo vs. terrestre, fórmula de peso volumétrico con ejemplo numérico, qué NO se puede enviar, plazos por zona |
| `/servicios/envio-internacional-de-documentos/` | **envío de documentos al extranjero** | enviar documentos legales al extranjero · courier de documentos Perú · enviar título universitario al extranjero | Lista de documentos aceptados, sobre incluido, restricciones (dinero, tarjetas), plazos 4-7 días |
| `/servicios/carga-internacional/` | **carga internacional desde Perú** | transporte de carga aéreo Perú · exportar mercancía desde Perú · flete internacional Lima | Desde cuántos kg, aéreo vs. marítimo/terrestre, documentación de exportación, cotización por volumen |
| `/servicios/apostilla-y-traduccion/` | **apostilla de documentos Perú** | apostillar título profesional · traducción oficial de documentos Lima · legalizar documentos para el extranjero | Qué es la apostilla, qué documentos aplican, plazos, países del Convenio de La Haya, precio referencial |

### Páginas de destino (intención geográfica: quien busca un país)

Patrón repetible para los 9 países. Sustituye `<país>`:

| Elemento | Fórmula |
|---|---|
| Keyword objetivo | **envíos a `<país>` desde Perú** |
| Secundarias | enviar paquete a `<país>` · cuánto demora un envío a `<país>` · qué puedo enviar a `<país>` · courier Perú `<país>` |
| Title | `Envíos a <país> desde Perú \| Grenvíos` |
| Contenido obligatorio | Plazos reales por modalidad · restricciones aduaneras propias del país · impuestos/límites de valor · ciudades con entrega · 3-5 FAQ específicas del país |

**Lo que hace que estas páginas ganen o pierdan:** el contenido específico del
país. Si `/destinos/chile/` y `/destinos/bolivia/` dicen lo mismo cambiando el
nombre, Google las trata como plantillas vacías. La diferencia está en las
restricciones aduaneras, los plazos y los impuestos — que además es lo que el
cliente realmente pregunta por WhatsApp.

### Páginas transaccionales e informativas

| Página | Keyword objetivo | Nota |
|---|---|---|
| `/` (Inicio) | **envíos internacionales Perú** | La keyword más genérica y competida: es la de la home, no la de una interior |
| `/cotizar/` | **cotizar envío internacional** | Añade cuánto cuesta enviar un paquete al extranjero desde Perú como H2 |
| `/rastreo-de-envios/` | **rastrear envío internacional** | Alto volumen, baja intención de compra: sirve para captar y recordar la marca |
| `/preguntas-frecuentes/` | **qué se puede enviar al extranjero** | Es la página con más potencial de posiciones largas si cada pregunta es un H3 |
| `/nosotros/` | **empresa de envíos internacionales Lima** | Página de confianza; poco tráfico, mucha conversión |
| `/contacto/` | **agencia de envíos internacionales Lima** | Refuerza el SEO local (dirección, horario, mapa) |

## 3. Mapa en inglés

**No es la traducción del mapa español.** Quien busca en inglés incluye casi
siempre el origen (*from Peru*), porque busca un proveedor en un país ajeno.

| Página (EN) | Keyword objetivo | Secundarias |
|---|---|---|
| `/en/` | **international shipping from Peru** | courier from Lima · shipping company Peru |
| `/en/services/…parcels/` | **ship a package from Peru** | send parcel from Lima to USA · Peru courier service |
| `/en/services/…documents/` | **send documents from Peru** | courier documents Peru · ship passport documents from Lima |
| `/en/services/…cargo/` | **freight forwarder in Peru** | air freight from Lima · export cargo from Peru |
| `/en/services/…apostille/` | **apostille Peruvian documents** | Peru apostille service · certified translation Lima |
| `/en/destinations/usa/` | **shipping from Peru to USA** | send package Peru to USA · customs Peru USA |
| `/en/quote/` | **shipping cost from Peru** | how much to ship a box from Peru |

Regla de oro: **"Peru" o "Lima" en el `title` y en el `slug` de cada página en
inglés.**

## 4. Contenido de valor: qué añadir a cada página

El sitio es informativo y **no tiene blog** (decisión del cliente). Eso no impide
posicionar: el contenido va **dentro de las páginas del pilar**, que es donde
además convierte. Prioridad por impacto:

1. **Datos operativos concretos** (plazos, kg, medidas, precios referenciales,
   restricciones). Es lo que nadie más publica y lo que la gente busca. Una tabla
   de "qué se puede enviar a cada país" vale más que tres párrafos de marca.
2. **FAQ por página, no solo en la página de FAQ.** El tema ya lo soporta
   (repeater `page_faq` + schema `FAQPage`) y son las que captan las búsquedas
   en forma de pregunta.
3. **Ejemplos numéricos.** "Una caja de 40×30×30 cm que pesa 5 kg paga por 7,2 kg
   volumétricos" enseña, genera confianza y posiciona por *peso volumétrico*.
4. **Prueba y cercanía.** Fotos reales de la oficina y del proceso, nombre del
   equipo, horarios. Afecta a conversión y a las señales de negocio local.
5. **Páginas-guía nuevas bajo un pilar existente**, si hace falta más superficie:
   `/servicios/peso-volumetrico/`, `/servicios/que-no-se-puede-enviar/`.
   Viven bajo `/servicios/`, refuerzan el pilar y no exigen abrir un blog.

**Qué NO añadir:** texto de relleno sobre "la importancia de la logística
global". No posiciona, no convierte y diluye la keyword.

## 5. Enlazado interno (ya automatizado en el tema)

Tres capas, todas conscientes del idioma:

| Capa | Qué hace | Dónde se ajusta |
|---|---|---|
| **Enlaces automáticos por keyword** | Si en una página aparece la keyword objetivo de otra, la primera aparición se convierte en enlace. Máx. 5 por página, uno por destino, nunca en títulos ni dentro de otro enlace | `inc/seo-enlazado.php` (`grenvios_autolink_max`) |
| **Bloque «Continúa tu envío»** | Al final de cada página: servicio ↔ destinos, destino ↔ servicios y países vecinos, todo → cotizar | `grenvios_related_for()` |
| **Informe de enlaces** | Enlaces entrantes/salientes por página; detecta páginas huérfanas | «SEO por página» |

Reglas de enlazado manual, cuando escribas texto:

- **El ancla es la keyword de destino**, no "haz clic aquí" ni "ver más".
- **De lo general a lo específico y de vuelta:** el pilar `/servicios/` enlaza a
  sus 4 hijas, y cada hija enlaza de vuelta al pilar. Igual con `/destinos/`.
- **Cruza servicio × país** siempre que sea natural: es lo que genera las
  búsquedas de cola larga ("envío de paquetes a Ecuador vía terrestre").
- **Toda página comercial termina en `/cotizar/`**: el enlazado interno no es
  solo SEO, es el embudo.
- **Ninguna página sin enlaces entrantes.** Si «SEO por página» marca 0 entrantes,
  esa página es invisible por dentro.

## 6. Orden de trabajo recomendado

| Paso | Qué | Por qué primero |
|---|---|---|
| 1 | Definir la keyword objetivo de cada página (ES) en «SEO por página» y resolver las canibalizaciones que marque | Sin esto, el enlazado automático usa anclas equivocadas |
| 2 | Reescribir `title` y `meta description` de las 10 páginas principales | Es el cambio con mejor relación esfuerzo/resultado |
| 3 | Rellenar el contenido operativo que falte (plazos, restricciones, ejemplos) en servicios y destinos | Es lo que sostiene la posición una vez que entras |
| 4 | Revisar el informe de enlaces y arreglar páginas huérfanas | Reparte autoridad hacia las páginas que venden |
| 5 | Traducir todo al inglés y **reescribir a mano** keyword, title y slug de las 8 páginas estratégicas | Ver `PLAN-SEO-MULTIIDIOMA.md` |
| 6 | Medir a 4-8 semanas en Search Console: impresiones por página y consultas nuevas | Confirma qué keywords eran reales y cuáles cambiar |

## 7. Cómo se mide que esto vende

No mires solo posiciones. En orden de importancia:

1. **Clics a WhatsApp y envíos del formulario de cotización, por página de
   origen.** Es la única métrica que es dinero.
2. **Consultas de Search Console con intención de compra** (las que incluyen
   *cuánto cuesta*, *precio*, *cotizar*, *desde Perú*), frente a las meramente
   informativas.
3. **Impresiones por página**: dice si Google ya entiende la página, incluso
   antes de que lleguen los clics.
4. **Páginas de entrada**: si el 90 % del tráfico entra por la home, el enlazado
   y las páginas interiores aún no están haciendo su trabajo.

---

## 8. Qué se aprendió mirando a DHL Perú (y qué NO copiar)

Análisis de `dhl.com/pe-es` (agosto 2026). **Aviso metodológico:** dhl.com bloquea
la descarga directa (timeout en `robots.txt`, en el sitemap y en el HTML), así que
las rutas de abajo están verificadas vía buscador, pero **no** se pudieron leer sus
`title`, sus `hreflang` ni su sitemap. No hay conclusiones sobre lo que no se pudo
ver.

Rutas confirmadas:

```
dhl.com/pe-es/home.html
dhl.com/pe-es/home/express.html
dhl.com/pe-es/home/rastreo.html
dhl.com/pe-es/home/quienes-somos.html
dhl.com/pe-es/home/obtenga-una-cotizacion.html
dhl.com/pe-es/home/dhl-para-empresas.html
dhl.com/pe-es/home/express/small-business-partnership-program.html
mydhl.express.dhl/pe/es/home.html                                        (app transaccional)
bootcamp.latam.express.dhl.com/envios-internacionales/envios-desde-peru   (microsite de captación)
```

### Por qué NO se copia su estructura de URL

`/pe-es/home/express.html` arrastra tres lastres: el prefijo `pe-es` (existe porque
operan en 220 países; Grenvíos opera desde uno), el segmento `/home/` que no aporta
nada y la extensión `.html` heredada de su gestor de contenidos. Y `express.html`
**no contiene ninguna keyword**.

DHL puede permitírselo porque posiciona por marca y por autoridad de dominio: la
gente busca "DHL Perú". Grenvíos no tiene ese colchón, así que necesita la keyword
en la URL. La prueba está en su propio ecosistema: **donde DHL sí depende del SEO
—su microsite de captación— usa URLs como las de este tema**:
`/envios-internacionales/envios-desde-peru`. Keyword en la ruta, sin `/home/`, sin
`.html`.

### Lo que sí se aplicó

| Patrón de DHL | Implementado |
|---|---|
| Página propia para empresas (`dhl-para-empresas`, `small-business-partnership-program`) | ✅ **`/envios-para-empresas/`** — página completa del tema: plantilla, textos editables, 5 bloques repetibles, 5 FAQ, SEO y enlazado |
| Cotizador accesible desde cualquier página | ✅ Bloque de llamada a la acción al cierre de todas las páginas, con enlace a `/cotizar/` y WhatsApp que incluye el nombre de la página de origen |
| Separación sitio informativo / app transaccional | Ya resuelto en el tema |

### Pendiente (no implementado)

**Guía de tarifas y servicios en PDF**, como su `service_and_rate_guide_pe_es_2025.pdf`.
Capta búsquedas de "tarifas", genera confianza y funciona como imán de enlaces y
como captura de correo. Requiere que el cliente decida qué precios publica.

### Keyword de la página nueva

| Página | Keyword objetivo | Secundarias |
|---|---|---|
| `/envios-para-empresas/` | **envíos internacionales para empresas** | courier corporativo Perú · cuenta corporativa de envíos · tarifas por volumen envíos · exportar muestras desde Perú |
| `/en/shipping-for-business/` | **business shipping from Peru** | corporate courier account Peru · bulk shipping rates Peru |

---

## 9. Clusters de contenido: guías por categoría que empujan a las páginas que venden

### La jerarquía (esto decide hacia dónde va cada enlace)

| Nivel | Páginas | Su trabajo | Enlaces |
|---|---|---|---|
| **Dinero** | Servicios, destinos, `/envios-para-empresas/`, `/cotizar/` | Convertir | **Reciben** de todo lo demás |
| **Información** | FAQ, Nosotros, Rastreo | Confianza y captación | **Dan** hacia dinero |
| **Apoyo (tráfico)** | Guías, agrupadas por categoría | Captar búsquedas informativas | **Dan todo** su enlace a UNA página de dinero |

**Regla que no se negocia:** una guía refuerza **una sola** página de dinero. Si
enlaza a cinco, reparte su autoridad en migajas y no mueve ninguna posición.

### Nota sobre el blog

El blog se eliminó por decisión del cliente (jun 2026). Para que este modelo
funcione hace falta esa capa de contenido, pero **montada como Guías, no como
blog de noticias**: categorías que calcan los pilares, cada entrada con un
objetivo comercial declarado. Si finalmente no se quiere ninguna capa de
contenido, la alternativa es meter ese material dentro de las páginas de
servicio (sección 4) — funciona, pero da menos superficie para captar búsquedas
informativas.

### Cómo se opera (ya implementado)

1. Al escribir una guía, en la caja lateral **«Cluster SEO — ¿a qué página
   refuerza?»** se elige la página de dinero. Solo aparecen las que venden.
2. La guía enlaza automáticamente a esa página, **usando la keyword de destino
   como texto ancla**.
3. Esa página muestra el bloque **«Guías que te pueden ayudar»** (solo si tiene
   guías: nunca una sección vacía).
4. En **Ajustes de la categoría** se elige el pilar que refuerza: su archivo deja
   de ser una lista suelta y anuncia el servicio al que pertenece.
5. En **SEO por página** aparece la tabla *Contenido de apoyo por página de
   dinero*: las que están a cero, en amarillo. Esa es la lista de trabajo.

### Plan de guías: qué escribir primero

Ordenado por lo que más cerca está de la venta. Cada una refuerza una sola página:

| Guía | Refuerza a | Por qué |
|---|---|---|
| Cómo calcular el peso volumétrico de tu envío | Envío de Paquetes | Es LA duda que frena la compra; se busca mucho y casi nadie lo explica con un ejemplo real |
| Qué no se puede enviar al extranjero desde Perú | Envío de Paquetes | Búsqueda informativa masiva, y evita cotizaciones que no se cierran |
| Cuánto demora un envío internacional según el destino | Destinos | Alimenta las 9 páginas de país sin duplicarlas |
| Qué documentos necesitas para exportar desde Perú | Envíos para Empresas | Intención B2B pura, muy poca competencia local |
| Impuestos y límites de valor por país | Destinos | Contenido que los grandes couriers no publican en detalle para la región |
| Cómo apostillar un documento peruano paso a paso | Apostilla y Traducción | Trámite con demanda estable y búsqueda muy concreta |
| Aéreo o terrestre: cuál te conviene según lo que envías | Envío de Paquetes | Ayuda a decidir; empuja directo a cotizar |
| Cómo embalar un paquete para que llegue entero | Envío de Paquetes | Captación pura, buena para enlaces y para compartir |

Con **6-8 guías bien enfocadas basta**. Cincuenta artículos mediocres posicionan
menos que ocho que resuelven una duda real y empujan a la misma página.

## 10. Higiene de indexación (qué NO debe entrar a Google)

Un sitio pequeño no puede permitirse decenas de páginas vacías compitiendo
consigo mismo. El tema ahora marca `noindex, follow` automáticamente en:

| Página | Motivo |
|---|---|
| Archivos de **etiqueta** | Listas de dos entradas que canibalizan a la categoría |
| Archivos de **autor** y de **fecha** | No aportan nada en una web de empresa |
| **Resultados de búsqueda** interna | Contenido generado, sin valor para Google |
| **Páginas de adjunto** | Páginas huérfanas con una imagen |
| **Listados paginados** (página 2, 3…) | El contenido real está en las entradas |
| **Categorías con menos de 3 entradas y sin descripción** | Página delgada; deja de ser noindex sola en cuanto se llena |

`follow` significa que Google **sí sigue** los enlaces de esas páginas: no se
pierde el enlazado interno, solo se evita que compitan en el índice.

Lo que **sí** debe indexarse: la home, servicios, destinos, empresas, cotizar,
rastreo, FAQ, nosotros, contacto, cada guía y las categorías con contenido real.
Es exactamente lo que lista `/sitemap.xml`.

---

## 11. Análisis del export de Keyword Planner (24 ago 2026)

Archivo: `Keyword Stats 2026-08-24 at 00_12_02.csv` — 450 keywords, 44.250 de
volumen declarado.

### Diagnóstico: el export no es de Perú

**Ninguna de las 450 keywords menciona Perú o Lima.** En cambio aparecen:

| Señal encontrada | Mercado real |
|---|---|
| `chilexpress`, `starken`, `correos de chile` | Chile |
| **`carta documento`** (decenas de keywords), `oca`, `andreani` | Argentina — *carta documento* es una figura legal argentina |
| `correos`, `carta certificada`, `burofax`, `472` | España |
| `servientrega`, `interrapidisimo`, `cédula` | Colombia |

La exportación se hizo **sin restringir la ubicación**. Consecuencia directa: los
tres picos de 5.000 (`carta certificada`, `correos certificado`,
`correos certificados servientrega`) son demanda de España y Colombia. Construir
la estructura del sitio con esos volúmenes sería crear páginas para mercados que
Grenvíos no atiende.

| | Keywords | Volumen |
|---|---|---|
| Marcas de la competencia | 160 | — |
| Otros mercados | 101 | — |
| **Descarte** | **261 (58 %)** | **30.800 (70 %)** |
| **Usables** | **189** | **13.450** |

### Segundo problema: cubre un solo cluster

264 de las 319 keywords genéricas son de documentos/cartas. **Cero** de carga,
**cero** de apostilla, **cero** de rastreo, 16 de paquetes. Es la investigación
de **una página** (`/servicios/envio-internacional-de-documentos/`), no del sitio.

### Lo que sí sirve de este archivo

Para la página de documentos, ya limpio:

| Rol | Keyword | Competencia |
|---|---|---|
| **Objetivo** | envío de documentos al extranjero | Medio |
| Secundarias (H2 y cuerpo) | envío de documentos internacional · envío de documentación al exterior · envío de correspondencia internacional · servicio de envío de documentos · courier documentos · envío de documentos express · envío rápido de documentos | Medio/Alto |
| FAQ y cola larga | enviar documentos a otro país · enviar pasaporte por mensajería · enviar papeles · envío de documentos por avión · agencias de envío de documentos al extranjero | Bajo |
| Cluster de destino | enviar documentos a Estados Unidos · envío de cartas a Estados Unidos | Medio |

Dos hallazgos con valor propio:

- **`envíos para empresas pequeñas` — competencia ALTA.** Competencia alta en
  Google Ads significa que alguien paga por ese clic: hay dinero detrás. Valida
  la página `/envios-para-empresas/`.
- **`envío de correspondencia internacional` y `envío de documentación al
  exterior` — ALTA.** Son las de intención comercial real de este cluster, por
  encima de las de mayor volumen aparente.

### Cómo reexportar bien

1. Keyword Planner → **Ubicación: Perú** (no «todos los países»). Es el error que
   invalidó este archivo.
2. **Idioma: español.**
3. **Un export por cluster**, con su propia semilla: *envío de paquetes*, *carga
   internacional*, *apostilla*, *rastreo*, y uno por país destino prioritario.
   Una sola semilla solo devuelve keywords de su tema.
4. Sin campaña activa, los volúmenes son rangos (50 / 500 / 5.000): sirven para
   **ordenar**, no como cifras exactas.

### Herramienta (ya implementada)

**SEO por página → Importar keywords**: sube el CSV, lo limpia (marcas y otros
mercados, por palabra completa), lo agrupa por intención, propone la página de
cada keyword y, con un clic, escribe la keyword objetivo y las secundarias.
No pisa las keywords que ya se decidieron a mano. Avisa cuando más del 40 % del
volumen es descarte — que es justo lo que pasa con este archivo.

Las secundarias se ven al editar cada página, en la caja *Keywords secundarias*.

## 12. Sobre «apuntar todo al home»

Conviene matizarlo, porque hacerlo al pie de la letra resta:

**El home ya recibe un enlace desde todas las páginas del sitio** — el logo y el
menú. Es, con diferencia, la página más enlazada. Añadir enlaces desde las guías
no le aporta casi nada, y a la guía sí le quita: cada enlace que sale reparte
autoridad, y el que va al home es el que menos rinde.

**Dónde debe apuntar el contenido de apoyo:** a la página de dinero de su cluster.
Una guía sobre peso volumétrico enlazando a *Envío de Paquetes* mueve esa página;
enlazando al home, no mueve nada.

**El home posiciona por otra vía:** es la que recibe los enlaces externos (marca,
directorios, reseñas) y la que hereda la autoridad de todo el sitio. Su keyword
—«envíos internacionales Perú»— se sostiene con eso y con el enlazado descendente
hacia servicios y destinos, no con enlaces artificiales hacia arriba.

**La excepción legítima:** una guía cuyo tema *sea* el término genérico («cómo
funciona un envío internacional desde Perú») sí puede enlazar al home con ese
ancla. Una o dos, no todas.

En resumen: **hacia arriba manda el menú; hacia abajo y hacia el lado manda el
contenido.**

---

## 13. Arquitectura ampliada (ago 2026)

### Páginas nuevas

| Página | Keyword objetivo | Qué aporta |
|---|---|---|
| `/servicios/peso-volumetrico/` | **peso volumétrico** | Responde LA duda que frena la compra, con **calculadora real** (mide, calcula y abre WhatsApp con los datos ya escritos). Contenido de utilidad = tiempo en página, enlaces y conversión |
| `/que-se-puede-enviar/` | **qué se puede enviar al extranjero** | Permitidos y prohibidos por vía aérea y terrestre + tabla de países. Evita cotizaciones que no cierran |
| `/servicios/envio-de-equipaje/` | **enviar equipaje al extranjero** | Segmento propio (migrantes, estudiantes, turistas) que antes se perdía dentro de «paquetes» |
| `/envios-para-empresas/` | **envíos internacionales para empresas** | B2B. Validado por el export: `envíos para empresas pequeñas` tiene competencia **Alta** |

**Corrección necesaria de canibalización:** `/preguntas-frecuentes/` tenía asignada
la keyword *qué se puede enviar al extranjero*, que ahora es de la página nueva.
Cámbiala a **preguntas frecuentes envíos internacionales**. La pantalla *SEO por
página* lo marcará en rojo hasta que se corrija.

### Secciones nuevas que suben la calidad

- **Calculadora de peso volumétrico**: sin librerías ni peticiones; el botón de
  cotizar abre WhatsApp con las medidas y el peso ya escritos.
- **Tabla de ejemplos de cajas**: se **calcula**, no se escribe a mano, así que
  nunca contradice a la fórmula ni a la calculadora.
- **Tabla de países reutilizable**: sale de `grenvios_destinos()`, la misma
  fuente que las páginas de país. Si la clienta agrega un país, aparece sola en
  todas las tablas. Son datos, no texto duplicado.
- **Bloque «Continúa tu envío»**, **guías del cluster** y **llamada a la acción**
  ahora se imprimen **fuera** del `if` de `page.php`: una página nueva no puede
  quedarse sin enlazado por olvido.

### Estructura resultante

```
/                                     envíos internacionales Perú
├── /nosotros/
├── /servicios/                       (pilar)
│   ├── /envio-internacional-de-documentos/
│   ├── /envio-internacional-de-paquetes/
│   ├── /carga-internacional/
│   ├── /apostilla-y-traduccion/
│   ├── /peso-volumetrico/            ← nueva (herramienta)
│   └── /envio-de-equipaje/           ← nueva (segmento)
├── /destinos/                        (pilar) → 9 países
├── /envios-para-empresas/            ← nueva (B2B)
├── /que-se-puede-enviar/             ← nueva (informativa de alto tráfico)
├── /cotizar/                         (conversión)
├── /rastreo-de-envios/
├── /preguntas-frecuentes/
└── /contacto/
```

## 14. Reestructurar sin perder posiciones

**Lo que NO se hizo, a propósito: cambiar los slugs existentes.** Una URL que
cambia sin redirección pierde sus posiciones, los enlaces que apuntaban a ella y
el tráfico que recibía; Google tarda meses en recolocarla, si lo hace. Mover una
URL solo se justifica cuando la actual es claramente mala, y siempre con 301.

Por eso se añadió **SEO por página → Redirecciones**:

| Función | Detalle |
|---|---|
| **Captura automática** | Al cambiar el slug de una página **publicada**, la redirección 301 de la URL vieja a la nueva se crea sola. Es el paso que más se olvida |
| **Sin cadenas** | Si algo redirigía a la URL vieja, se reapunta a la nueva. Las cadenas 301→301 diluyen la señal |
| **Gestión manual** | Para fusionar páginas que se canibalizaban, o rescatar URLs antiguas |
| **Registro de 404** | Lista las URLs que se piden y no existen, con cuántas veces. Las que repiten merecen redirección: es tráfico que hoy se pierde |

### Orden seguro para reestructurar

1. Publicar primero lo nuevo (ya hecho) y enlazarlo.
2. Medir 4-8 semanas en Search Console **antes** de tocar lo viejo.
3. Solo entonces, si una página no despega y su slug es el problema, cambiarlo
   — la redirección se crea sola — y actualizar los enlaces internos que apunten
   a la URL vieja.
4. Revisar el registro de 404 cada pocas semanas.

**Fusionar antes que mover:** si dos páginas compiten por la misma keyword, casi
siempre es mejor fusionarlas (una gana, la otra redirige a ella) que reescribir
las dos. Se suma la autoridad en vez de repartirla.

---

## 15. Segunda tanda de páginas + sección de Guías

### Páginas nuevas

| Página | Keyword objetivo | Intención que captura (y que no cubría nadie) |
|---|---|---|
| `/tiempos-de-entrega/` | **cuánto demora un envío internacional** | Comparar plazos **entre** países. Una página de país solo habla de su país; aquí el valor está en la comparación |
| `/como-enviar-un-paquete-al-extranjero/` | **cómo enviar un paquete al extranjero** | El proceso completo en 7 pasos. Es la búsqueda de quien nunca ha enviado |
| `/recojo-a-domicilio-lima/` | **recojo a domicilio Lima** | SEO **local**: quien está en Lima y no quiere ir a la oficina. Lleva dirección, horario y teléfono reales |
| `/servicios/envio-de-compras/` | **enviar compras desde Perú** | Compras en tiendas peruanas que no envían al extranjero. Servicio que ya se presta pero no tenía página |

Ninguna repite el contenido de otra: `/tiempos-de-entrega/` compara, `/destinos/<país>/`
detalla, `/que-se-puede-enviar/` restringe y `/como-enviar…/` explica el proceso.
Las tres primeras beben de `grenvios_destinos()`, así que un país nuevo aparece
solo en todas.

### Sección de Guías (blog)

- La página de entradas pasa a llamarse **Guías**, con su propio title y
  description. No es un blog de noticias: es contenido de apoyo con objetivo
  comercial.
- **5 categorías que calcan los pilares**, creadas automáticamente, con
  descripción propia y ya asociadas a la página de dinero que refuerzan:
  *Guías de envío*, *Documentos y trámites*, *Aduanas y restricciones*,
  *Destinos*, *Envíos para empresas*.
- **Guías relacionadas** al final de cada entrada: primero por etiqueta (más
  específica) y se completa por categoría.
- **Schema `Article`** en cada guía y llamada a la acción al cierre.

### Etiquetas: para navegar, no para posicionar

Están activas y funcionan, pero **no se indexan por defecto**, y conviene
entender por qué: en un sitio con 15-20 guías, un archivo de etiqueta tiene dos
o tres entradas y compite a la vez contra su categoría y contra las guías
mismas. Google lo lee como página delgada y, en el peor caso, la posiciona en
lugar de la guía que sí querías.

Se usan para agrupar transversalmente (por país, por tipo de envío) y para las
*guías relacionadas*. Si una etiqueta llega a tener varias entradas **y**
descripción propia, se puede indexar con la casilla **«Indexar en Google»** que
aparece al editarla.

Misma lógica para las categorías: entran al índice en cuanto tienen 3 entradas o
una descripción — por eso se crean ya con descripción.

### Estructura final

```
/                                              envíos internacionales Perú
├── /nosotros/
├── /servicios/                                (pilar)
│   ├── /envio-internacional-de-documentos/
│   ├── /envio-internacional-de-paquetes/
│   ├── /carga-internacional/
│   ├── /apostilla-y-traduccion/
│   ├── /peso-volumetrico/                     herramienta + calculadora
│   ├── /envio-de-equipaje/                    segmento propio
│   └── /envio-de-compras/                     servicio propio
├── /destinos/                                 (pilar) → 9 países
├── /envios-para-empresas/                     B2B
├── /que-se-puede-enviar/                      informativa de alto tráfico
├── /tiempos-de-entrega/                       comparativa de plazos
├── /como-enviar-un-paquete-al-extranjero/     proceso paso a paso
├── /recojo-a-domicilio-lima/                  SEO local
├── /cotizar/                                  conversión
├── /rastreo-de-envios/
├── /preguntas-frecuentes/
├── /guias/                                    (blog) → 5 categorías
└── /contacto/
```

De 13 páginas iniciales a **21 páginas + la sección de guías**, cada una con su
propia keyword, sin que ninguna se pise con otra.

---

## 16. Rutas: páginas servicio × país (la long tail que vende)

### Por qué estas páginas y no «más páginas»

La gente no busca *«servicios de courier»*. Busca **`enviar documentos a Estados
Unidos`** — verbo + qué + a dónde. Quien busca así ya decidió qué envía y a
dónde: está a un paso de cotizar. En tu propio export de Keyword Planner
aparecen `enviar documentos a estados unidos`, `envío de cartas a estados
unidos` y `enviar correspondencia a estados unidos` (competencia **Alta**).

Ninguna página del sitio cubre hoy ese cruce: la de servicio habla del servicio,
la de país habla del país. La combinación es territorio libre.

### El riesgo, y por qué la herramienta no te deja caer en él

4 servicios × 9 países = 36 páginas. Generarlas todas cambiando dos palabras es
exactamente lo que Google llama **doorway pages**: no solo no posicionan, sino
que arrastran al dominio entero. Por eso el generador impone tres reglas:

| Regla | Qué hace |
|---|---|
| **Se crean de una en una, como borrador** | Nunca hay generación masiva |
| **Exigen contenido propio** | Introducción propia + 3 de 5 campos (precio de referencia, plazo real, restricciones del país, caso) + 180 palabras |
| **`noindex` automático mientras no lo tengan** | La página existe para el usuario, pero **no entra al índice ni al sitemap** hasta que aporta algo que no está en ninguna otra página |

La caja de edición dice en todo momento qué falta y por qué no se está
indexando. En cuanto se completa, entra sola.

### Dónde se opera

**SEO por página → Rutas.** Matriz país × servicio con el estado de cada una:

- `—` no existe · `📝` borrador · `⚠️` publicada pero fuera del índice · `✅` indexable

### Cuáles crear primero

No las 36. Empieza por las rutas que **de verdad mueves** y donde tengas datos
propios que dar:

| Ruta | Por qué |
|---|---|
| `/enviar-documentos-a-estados-unidos/` | Confirmada en el export, competencia Alta |
| `/enviar-paquetes-a-espana/` | Comunidad peruana grande, envío personal recurrente |
| `/enviar-paquetes-a-chile/` | Ruta terrestre propia, con restricciones particulares |
| `/enviar-carga-a-estados-unidos/` | B2B, ticket alto |
| `/enviar-equipaje-a-espana/` | Migración de vuelta, ticket medio-alto |

**El dato que decide la venta es el precio de referencia.** Es lo que nadie
publica y lo primero que pregunta el cliente. Si en una ruta no puedes dar un
precio, da al menos el plazo real y las restricciones concretas: sin datos
propios, esa página no merece existir.

### Qué NO hacer

- No crear una ruta «por si acaso»: si no tienes datos propios, la página se
  queda en `noindex` y solo añade ruido.
- No copiar el texto de la página de país cambiando el servicio: el sistema lo
  permitiría, pero es justo el clon que Google descarta.
- No publicar las 36 «para tener más páginas». **Cinco rutas con datos reales
  valen más que treinta y seis vacías** — y treinta y seis vacías pueden costar
  el posicionamiento de las que ya funcionan.

---

## 17. Contenido de las guías y edición desde el sitio

### Ocho guías escritas, listas para publicar

En **SEO por página → Guías** hay un botón que las crea todas **como borrador**.
Cada una está enfocada a una búsqueda concreta y refuerza **una sola** página de
venta:

| Guía | Keyword | Refuerza a |
|---|---|---|
| Cómo calcular el peso volumétrico (con ejemplos) | cómo calcular el peso volumétrico | Envío de Paquetes |
| Qué no se puede enviar por vía aérea (y sí por terrestre) | qué no se puede enviar por avión | Qué se puede enviar |
| Aéreo o terrestre: cuál te conviene | envío aéreo o terrestre | Envío de Paquetes |
| Cómo embalar un paquete para que llegue entero | cómo embalar un paquete | Envío de Paquetes |
| Qué documentos puedes enviar y cuáles necesitan apostilla | enviar documentos al extranjero | Envío de Documentos |
| Cómo apostillar un documento peruano, paso a paso | cómo apostillar un documento en Perú | Apostilla y Traducción |
| Qué documentación necesita tu empresa para exportar | documentos para exportar desde Perú | Envíos para Empresas |
| Por qué un envío se queda en aduana (y cómo evitarlo) | envío retenido en aduana | Destinos |

**Los textos no inventan datos.** Solo afirman lo que el propio sitio ya sostiene
—plazos por zona, la fórmula del peso volumétrico, las restricciones por vía, el
pago en Lima, el límite de dos prendas por marca— y donde haría falta una cifra
(precios, tasas de aduana, límites de valor) remiten a cotización. Un dato
inventado en una web de envíos es una reclamación al día siguiente.

Por eso se crean en borrador: alguien de la empresa debe confirmar que todo
coincide con la operación actual antes de publicar.

### Edición desde el sitio

Al ver una guía o una ruta como administrador aparece abajo a la izquierda el
botón **«Editar guía»** / **«Editar ruta»**. Abre un panel lateral con:

- **Guías:** título, palabra clave objetivo, extracto, a qué página refuerza y el
  contenido. Con «Guardar y publicar» si está en borrador.
- **Rutas:** título, keyword y los cinco campos de contenido propio, con el aviso
  en vivo de qué falta para que la página entre al índice de Google.

**Por qué el contenido se edita como HTML y no con editor visual:** montar
TinyMCE en el frontend obliga a cargar medio panel de WordPress en cada visita de
un administrador y suele romper los estilos del tema. Para corregir un párrafo,
un dato o un enlace —el 90 % de lo que se hace desde el sitio— el área de texto
es más rápida y no rompe nada. Para reescribir una guía entera, el editor
completo de WordPress está a un clic desde el mismo panel.

### Enlaces internos en el contenido traducido

Los enlaces escritos dentro de una guía (`/servicios/peso-volumetrico/`) se
reescriben al idioma activo al mostrarse, no en la base de datos: la versión en
inglés enlaza a las páginas en inglés y el texto original en español queda
intacto.
