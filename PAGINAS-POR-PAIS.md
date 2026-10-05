# Grenvíos — Páginas y rutas por país

**Una sola operación: Perú.** Los datos de contacto son siempre los mismos. Lo que
cambia por país de destino son las **rutas, los títulos, los slugs, el SEO y el
contenido**.

Hay **dos mecanismos** y conviene no confundirlos:

| | Qué es | Ejemplo |
|---|---|---|
| **Rutas de país** (Polylang) | El sitio entero duplicado bajo un prefijo. Cada país tiene su copia de cada página y de cada entrada, y se reescriben para ese destino | `/cu/sobre-nosotros-envios-a-cuba/` |
| **Páginas por combinación** | Páginas sueltas de alta intención, con la palabra clave en la URL | `/enviar-paquetes-a-cuba/` |

Se complementan: las rutas dan la cobertura completa; las combinaciones cazan la
búsqueda concreta.

---

## 1. Rutas de país sobre Polylang

Polylang se usa como **duplicador de contenido**, no como traductor. Cada país es
una «lengua» de Polylang, así que sale gratis: el prefijo de ruta, la copia de
cada página, la copia de cada entrada del blog, el menú propio y el botón de
«crear versión» en el editor. **Al subir un post se elige a qué ruta pertenece.**

### El riesgo que esto trae, y cómo está resuelto

`/` y `/cu/` **no son la misma página en dos idiomas**: son páginas distintas
sobre destinos distintos, las dos en español y las dos para el mismo público
peruano. Si Polylang las declara como alternativas con `hreflang`, Google entiende
que son la misma cosa, elige una y descarta las demás — justo las páginas que se
crearon para posicionar por separado.

Por eso el tema distingue **ruta de país** de **idioma de verdad**, comparando el
código de idioma del locale:

- `es_PE` y `es_CU` comparten «es» → **ruta de país**
- `es_PE` y `en_US` no → **idioma de verdad**

| | `/` | `/cu/` ruta de país | `/en/` idioma |
|---|---|---|---|
| `<html lang>` | `es-PE` | `es-PE` | `en` |
| `og:locale` | `es_PE` | `es_PE` | `en_US` |
| `hreflang` | es-PE, en, x-default | **ninguno** | es-PE, en, x-default |

Una ruta de país no declara alternativas **ni se ofrece como alternativa de
nadie**: es canónica de sí misma y compite sola. Si mañana se añade inglés de
verdad, ese sí entra en el grupo sin tocar nada. Para forzar el tipo de una ruta
está el filtro `grenvios_ruta_tipo`.

### Duplicar el sitio a una ruta: 24 páginas por país

En *WordPress → Sedes*, cada ruta tiene un botón que crea de golpe su copia de
todas las páginas, ya enlazadas como traducciones y con el contenido copiado para
reescribirlo encima. A mano desde Polylang serían más de 200 pasos para nueve
países.

**Los slugs llevan el país, no un «-2».** WordPress no admite dos páginas con el
mismo slug en el mismo nivel, y Polylang 3.8 no cambia esa regla: si se fuerza,
`/cu/nosotros/` acaba sirviendo la página española y la copia queda inalcanzable.
Ya que el slug tiene que cambiar, se aprovecha para meter la palabra clave:

```
/cu/envios-a-cuba/                 (portada del país)
/cu/tiempos-de-entrega-a-cuba/
/cu/que-se-puede-enviar-a-cuba/
/cu/envio-de-paquetes-a-cuba/
/cu/aduana-de-cuba/
/cu/guias-para-enviar-a-cuba/      (blog del país)
```

**Cada slug está escrito para su búsqueda, no generado.** Pegar «-a-cuba» detrás
del slug original daba `como-enviar-un-paquete-al-extranjero-a-cuba`: larguísima y,
peor, dice «al extranjero **a Cuba**». El mapa está en `grenvios_ruta_slug()`, una
entrada por página, y se ajusta con el filtro `grenvios_ruta_slugs`.

**Las páginas van planas dentro de la ruta.** Conservar la jerarquía daba
`/cu/servicios-a-cuba/envio-de-paquetes-a-cuba/`: el país tres veces y 45
caracteres que no aportan nada, porque el prefijo `/cu/` ya da todo el contexto.
La página madre sigue existiendo como índice. Filtro `grenvios_ruta_aplanar`.

**Los slugs de servicio son «envio-de-…», no «enviar-…», a propósito**: las páginas
por combinación (§2) ya ocupan `enviar-paquetes-a-cuba` en la raíz del sitio, y
WordPress no admite dos páginas con el mismo slug en el mismo nivel aunque estén
en rutas distintas. Sin esta separación habría añadido `-2` en silencio.

**El árbol de Destinos y «Artículos por país» se crean como espejos.** Dentro de
`/cu/`, la ficha «Chile» es una página de envíos a Chile, igual que
`/cl/envios-a-chile/` y `/destinos/chile/`. Como páginas normales serían 99
duplicados (11 × 9 rutas) compitiendo con las buenas. Durante un tiempo se dejaron
fuera, pero en el listado de WordPress salían con «+» en todas las columnas de país.

Ahora `inc/paises-espejos.php` las crea en cada ruta, enlazadas como traducción
—desaparecen los «+»—, pero sin que compitan:

| | Espejo `/cu/destinos-cuba/chile/` |
|---|---|
| Canónica | `/cl/envios-a-chile/` (la portada de la ruta de ese país; si no la hay, la ficha del sitio principal) |
| Sitemap | fuera |
| `noindex` | no: con canónica ajena sería una señal contradictoria |
| Menús, enlaces y selector de país | no las usan: `grenvios_i18n_translation_id()` las ignora |
| Cabecera y bloque de país | no: la ficha de Chile en `/cu/` habla de Chile |

Medido: 99 espejos en 200, 99 canónicas correctas, 0 en el sitemap, 0 enlaces
hacia ellos desde los menús.

Quedan **24 páginas por ruta más 11 espejos**; con nueve países, 216 + 99. Para ajustar un slug está el filtro `grenvios_ruta_slug`;
para excluir o recuperar páginas, `grenvios_ruta_paginas`.

### El blog también es de cada país

El blog de una ruta salía **vacío**: Polylang filtra las entradas por «idioma» y
todas pertenecían a la principal. Una página pensada para atraer tráfico de cola
larga, en blanco en los nueve países.

Ahora `inc/paises-blog.php` duplica también las entradas, con su bloque de país al
pie y bajo la misma compuerta. Arrastra además **las categorías**: sin eso el
filtro del blog enviaba a `/category/uncategorized/` —el archivo del sitio
principal— y sacaba al visitante de su ruta; y ponerle el prefijo a mano daba 404,
porque Polylang solo crea el archivo de una categoría en los idiomas donde esa
categoría existe.

Dos detalles que costaron encontrar:

- **Las categorías se asignan después de fijar el idioma del post.** Al revés,
  Polylang las descarta —filtra los términos por el idioma del post, y mientras el
  post no tiene idioma se queda con la del sitio principal—. El síntoma era que
  todas las categorías de `/cu/` marcaban 0 entradas.
- **`uncategorized-cu` lo crea Polylang solo** al añadir el idioma; las demás las
  crea el tema con el nombre del país (`aduanas-cuba`). La diferencia de nombre es
  cosmética, no un fallo.

Si una ruta todavía no tiene entradas propias, su blog muestra las generales del
sitio principal con un aviso —«las específicas de Cuba están en camino»— en vez de
una página en blanco.

### «Artículos por país»: el índice que cruza los nueve blogs

Cada ruta tiene su blog y eso está bien para quien ya está en `/cu/`. Pero deja un
hueco: quien llega buscando «guía para enviar a Bolivia» no está en ninguna ruta, y
desde el sitio principal no había forma de ver qué se ha escrito de cada destino
—los artículos quedaban repartidos en nueve blogs que no se cruzan—.

`/articulos-por-pais/` los junta **agrupados por país**, con un filtro para quedarse
con uno solo. Se crea sola la primera vez que se entra al admin habiendo más de una
ruta, y **no se duplica** a las rutas: una versión en `/cu/` de «artículos de todos
los países» competiría con el original sin aportar nada.

Tres decisiones que conviene no deshacer:

- **El filtro es del lado del cliente.** Con recarga harían falta nueve URLs
  (`?pais=cu`) mostrando subconjuntos del mismo contenido: justo el tipo de página
  que Google marca como duplicada y que además diluye la que sí queremos
  posicionar. Filtrando en el navegador hay una sola URL y Google ve el listado
  entero.
- **La ruta principal no aparece como destino.** «Artículos sobre envíos a Perú»
  describiría un servicio que no se presta: Perú es el origen. Su grupo se titula
  «Guías generales, válidas para cualquier destino».
- **No se toca el menú del diseño.** Se enlaza desde cualquier listado del blog,
  que es donde el enlace tiene sentido.

### La cabecera de cada página nombra su país

El `<title>` de las copias ya decía «Contacto para Envíos a Estados Unidos», pero
lo visible —antesala, H1 y migas— seguía siendo el de Perú. Google veía una página
sobre Estados Unidos con un encabezado que no lo mencionaba.

`inc/paises-cabecera.php` lo reescribe al pintar, por los dos caminos del tema:
los campos `*_hero_title` / `*_hero_eyebrow` de las plantillas (más `pf_title` y
`pf_sub` de la FAQ, que no siguen el patrón) y `logisko_page_banner()` para las
que pintan desde PHP. Resultado medido: **216 de 216 H1 nombran su país**.

| | Perú | Ruta de Estados Unidos |
|---|---|---|
| Antesala | Contacto | Envíos a Estados Unidos |
| H1 | Contacto Grenvíos — Hablemos de tu envío | Hablemos de tu envío a Estados Unidos |
| Migas | Inicio / Contacto | Envíos a Estados Unidos / Contacto |

Si la clienta edita a mano la cabecera de una copia, se respeta: solo se reemplaza
lo que sigue siendo el texto literal de Perú. Los títulos se ajustan con el filtro
`grenvios_cabecera_titulos`.

### Origen y destino: la distinción de la que depende todo

En una ruta de país, **el país de la ruta es el DESTINO**, no el origen. Quien lee
`/cu/` está en Lima y quiere enviar a Cuba. Confundirlo produce frases al revés
—«cuánto demora un envío **desde** Cuba»— que describen justo el servicio que no
se presta.

| Token | En `/` | En `/cu/` |
|---|---|---|
| `{{origen_ciudad}}` · `{{origen_pais}}` | Lima · Perú | Lima · Perú |
| `{{destino_pais}}` | *(vacío)* | Cuba |
| `{{destino_a}}` | al extranjero | a Cuba |

Los datos de contacto son siempre los de Lima: hay una sola operación.

### SEO sembrado al duplicar: 18 de 18

Cada copia nace con su propio `<title>` y su meta description orientados a su
país. Sin esto heredarían los de Perú y no darían ninguna señal del destino.

```
/cu/envios-a-cuba/               Envíos a Cuba desde Perú: Precios y Tiempos
/es/tiempos-de-entrega-a-espana/ ¿Cuánto Demora un Envío a España? Plazos Reales
/ar/sobre-nosotros-…-argentina/  Quiénes Somos: Envíos a Argentina desde Perú
/cu/guias-para-enviar-a-cuba/    Guías para Enviar a Cuba
```

Los patrones están escritos uno a uno para la intención de cada tipo de página,
no generados pegando «a Cuba» detrás de un título cualquiera. Se ajustan con
`grenvios_ruta_seo_mapa` y `grenvios_ruta_seo_otros`.

### El selector del encabezado cambia de país, no de idioma

El desplegable del header lista las diez rutas y lleva a **la página equivalente**
en cada país: desde `/cu/aduana-de-cuba/` ofrece `/ec/aduana-de-ecuador/`, no la
portada. Es el mecanismo por el que se cambia de inquilino a propósito.

Mostraba **«Español»** para la ruta principal, que es el nombre que Polylang le da
a la lengua. En un desplegable donde el resto son Cuba, Chile o Argentina, nadie
entiende que «Español» significa «Perú», que es a donde vuelve si lo pulsa. Ahora
usa el nombre del país, y la etiqueta visible pasó de `PE` a `Perú`.

Es el mismo fallo que apareció en la columna del listado de páginas y en el hub de
artículos: **cada sitio que muestre una ruta tiene que pedirle el nombre a
`grenvios_col_pais_nombre()`**, nunca leer el nombre de la sede directamente.

### Cada ruta es un inquilino: la navegación no se sale

Quien entra en `/cu/` tiene que seguir en `/cu/` al pulsar «Nosotros», «Rastreo»
o «Cotizar». Un enlace que devuelva al sitio general rompe el inquilino.

Medido sobre las **216 páginas y 36 entradas** de las nueve rutas:

| | Resultado |
|---|---|
| Fugas al sitio general | **0** |
| Enlaces entre rutas (cambio de inquilino) | 3.588 |



Los saltos «a otro país» **son intencionados**: el submenú Destinos pasó a ser el
cambio de inquilino, y de camino teje enlaces internos entre rutas. El único
enlace al sitio general es `/destinos/` —«ver todos los países»—, la salida
deliberada al índice global.

#### Siete fugas que hubo que cerrar

1. **`home_url()` no es la raíz del sitio.** Con Polylang y prefijo de directorio,
   en el frontend devuelve la portada del idioma activo —incluido su slug:
   `/cu/envios-a-cuba`—. `grenvios_i18n_localize_url()` comparaba contra eso, veía
   `/cotizar/` como enlace externo y lo dejaba sin traducir: **un enlace en cada
   página sacaba al usuario de su ruta**. Ahora se lee de la opción `home`, que
   Polylang no filtra (`grenvios_i18n_site_root()`).
2. **El submenú Destinos** enlazaba a las fichas del sitio general.
3. **La tabla de plazos y las tarjetas** construían la URL a mano, sin filtro.
4. **El bloque «A dónde llegamos»** de Nosotros, igual.
5. **El bloque de enlazado interno** «Continúa tu envío» hacía `echo` directo sin
   pasar por ningún filtro.
6. **`HOMEURL`, el token del menú**, se sustituía por `home_url()`. Todo el menú de
   cada ruta salía como `/cu/envios-a-cuba/contacto/`: URLs no canónicas que
   además ignoraban los slugs de país recién creados. Ahora usa
   `grenvios_url_base()`, que sí devuelve la raíz.
7. **El localizador arrastraba los enlaces de otras rutas a la ruta actual.** Al
   ver un prefijo lo quitaba y volvía a resolver contra el idioma activo, así que
   en `/cu/` las cinco filas de la tabla de plazos —Argentina, Chile, España…—
   apuntaban todas a `/cu/envios-a-cuba/`. Un enlace que ya nombra su ruta es
   deliberado y no se toca.

**Dónde se arma un enlace y dónde no.** `grenvios_url_base()` se usa allí donde se
construye un enlace interno; para el «Inicio» de las migas y los `@id` de schema sí
se quiere la portada de la ruta, y ahí sigue `home_url()`. Y todo el cuerpo de
`page.php` sale por `grenvios_html_final`, que es el único punto por el que pasan
los renders que imprimen directamente.

### Sitemap: un XML por país para Search Console

`/sitemap.xml` deja de ser una lista plana y pasa a ser un **índice**:

```
/sitemap.xml          índice
  /sitemap-pe.xml     Perú
  /sitemap-cu.xml     Cuba
  /sitemap-es.xml     España      …un hijo por país
```

Es la única forma de leer en Search Console si Google indexa la ruta de Cuba o
solo la de Perú: con un sitemap único de 200 URLs mezcladas ese dato no se separa.

**Coherencia con la compuerta.** Un país solo aparece en el índice cuando tiene al
menos una página con contenido propio, y dentro de su sitemap solo entran esas.
Anunciar una URL que se sirve con `noindex` es contradictorio.

**Con un solo idioma no cambia nada**: sigue siendo la lista plana de siempre.

**Imágenes.** Cada URL lleva su `<image:image>`, con la misma cadena que Open
Graph: imagen destacada → el hero puesto en el editor → la del tema.

### Qué se puede editar de cada página

Todo, desde el editor de la página (metabox *SEO Grenvíos*):

| Campo | Dónde | Viene relleno con |
|---|---|---|
| **Title tag** | metabox SEO | el título del país sembrado al duplicar |
| **Meta description** | metabox SEO | la descripción del país sembrada |
| **Ruta (slug)** | enlace permanente | `tiempos-de-entrega-a-cuba` |
| **Imagen** | Imagen destacada | vacía → hereda la de la página |
| **Palabra clave** | metabox SEO | vacío |

Vienen rellenos para no partir de cero, pero **mandan siempre los valores escritos
a mano**.

### La compuerta: una copia sin tocar no entra al índice

Publicar las copias tal cual son nueve versiones idénticas del mismo texto
cambiando el prefijo: *doorway pages* de manual, y penaliza al dominio completo.

Mientras una página duplicada sea **idéntica a su original**, sale con
`noindex, follow` y queda fuera del sitemap. En cuanto se le escribe algo propio,
entra al índice sola. Es la doctrina que `inc/paginas-combinadas.php` ya aplicaba.

El título y la descripción SEO **no cuentan** para esa comparación: se siembran
solos, así que si contaran, toda copia parecería única desde el primer segundo.
Lo que hace única a una página de país es su **texto**.

---

## 3. Contenido propio de cada país

Duplicar las 24 páginas y cambiarles el slug no basta: el cuerpo seguía siendo el
mismo texto en los nueve países. `inc/paises-contenido.php` monta, para cada
página y cada país, un bloque de secciones construido con los datos reales de ese
destino, y lo escribe en el contenido de la copia.

### De dónde sale el texto, y de dónde no

| Se construye solo | Lo escribe la clienta |
|---|---|
| Plazos, modalidades, forma de entrega | Ciudades con cobertura |
| El impuesto (sale del `restr` de la ficha) | Lo que más se envía |
| Comparativa aéreo/terrestre | Lo que la aduana no admite |
| Preguntas frecuentes con esos datos | Documentación exigida |
| Entradilla propia de cada página | Embalaje y contexto de la ruta |

**No se inventan hechos.** Un campo vacío no produce una sección con relleno
genérico: no produce sección. Publicar «la aduana de Cuba permite 10 kg» porque
suena verosímil sería inventarle a un cliente una norma que le pueden rechazar en
el mostrador. Los porcentajes de impuesto, los plazos y las modalidades ya
estaban escritos en la ficha de cada destino; lo demás se rellena en
*Destinos → Contenido por país*, que además lista qué falta en cada uno.

### La matriz: ninguna página lleva el bloque de otra

Cada una de las páginas maestras tiene asignado su propio conjunto de
secciones, y cuando una sección se repite en dos páginas entra con otro titular y
otra entradilla según el ángulo de esa página. Sin esto se habría cambiado la
duplicación *entre países* por duplicación *dentro del país*.

Cuando el conjunto asignado se queda corto —en un país de una sola modalidad la
comparativa aéreo/terrestre no existe— se completa con secciones reales que esa
página no estaba usando, nunca con texto inventado. Los dos umbrales son
ajustables: `grenvios_pais_objetivo` (cuánto contenido propio se busca, 1100
caracteres) y `grenvios_pais_umbral` (cuánto exige la compuerta, 600).

### Qué se consiguió, medido

Similitud real entre páginas renderizadas (solapamiento de secuencias de 8
palabras, quitando cabecera, pie y enlazado interno), con tres países creados:

| | Antes | Ahora |
|---|---|---|
| Dos páginas del mismo país | — | **11 % de media, 33 % la peor** |
| La misma página en dos países | 99 % la peor | 21 % de media, **75 % la peor** |

Dentro de un país el problema está resuelto. **Entre países no del todo, y
conviene decirlo claro:** el bloque propio pesa alrededor de un tercio de la
página, y los otros dos tercios son el cuerpo de la plantilla, idéntico en los
nueve países. Por eso la peor pareja —las portadas de Chile y Ecuador, dos países
con las mismas modalidades y sin campos rellenos— sigue en el 70 %.

Bajarlo más no es cuestión de generar más texto automático: rellenar los campos
de cada país y reescribir el cuerpo de las páginas que de verdad importan es lo
que lo resuelve. La compuerta sigue ahí para que nada entre al índice antes de
tiempo.

### La compuerta, ahora en dos pasos

El bloque se escribe solo al duplicar, así que no puede valer por sí mismo como
«alguien trabajó esta página»: si valiera, las 216 copias entrarían al índice el
día que se crean. Se mide así:

1. **¿Ha tocado algo la clienta?** Se compara la huella de la página sin el bloque
   automático. Cualquier edición suya abre el índice, como siempre.
2. **Si no ha tocado nada**, el bloque automático abre por sí solo cuando alcanza
   los 600 caracteres de texto propio. Un bloque de tres líneas no lo hace.

### Dónde se pinta

El bloque se guarda en `post_content`, pero casi todas las páginas del tema son
*parametrizadas*: renderizan su plantilla con tokens y **no imprimen
`post_content`**. Sin un renderizador propio el contenido quedaba guardado y sin
salir nunca —las páginas seguían siendo idénticas aunque en la base de datos no lo
fueran—. Sale por `grenvios_pais_render()`, llamada desde `page.php` y desde
`front-page.php`, que es la que sirve la portada de cada ruta.

---

## 2. Páginas por combinación

Nueve tipos × nueve países = **81 rutas posibles**, cada una con título y meta
description propios. Los 81 títulos son distintos entre sí.

| Tipo | Ruta (ejemplo Cuba) | Intención |
|---|---|---|
| Documentos | `/enviar-documentos-a-cuba/` | ya sabe qué envía |
| Paquetes | `/enviar-paquetes-a-cuba/` | ya sabe qué envía |
| Carga | `/enviar-carga-a-cuba/` | comercial |
| Equipaje | `/enviar-equipaje-a-cuba/` | mudanza |
| Precio | `/cuanto-cuesta-enviar-a-cuba/` | **compara antes de contratar** |
| Plazos | `/cuanto-demora-un-envio-a-cuba/` | compara |
| Restricciones | `/que-se-puede-enviar-a-cuba/` | duda que frena la compra |
| Aduana | `/aduana-de-cuba-para-envios/` | duda que frena la compra |
| Compras | `/enviar-compras-a-cuba/` | e-commerce |

Añadir un tipo es una entrada en `grenvios_combo_servicios()`. Añadir un país es
darlo de alta en *Destinos*.

---

## 3. «Trabajamos también en…» en Nosotros

La página Nosotros lleva un bloque **A dónde llegamos** (`{{paises_operamos}}`)
con los países agrupados por continente, cada uno con su plazo y sus modalidades,
enlazando a su ruta. Responde a «¿a dónde llegan?» y reparte enlaces internos
hacia las páginas que deben posicionar. Se edita en *Personalizar → Nosotros ·
Dónde operamos*.

---

## Home y destino de cada país (2026-09-14)

| URL | Página | Plantilla |
|---|---|---|
| `/ar/` | «Inicio» (traducción de la home de Perú) | Home: carrusel + secciones + bloque del país |
| `/ar/envios-a-argentina/` | Traducción de `/destinos/argentina/` | Destino: hero del camión a pantalla completa |
| `/` | Home de Perú | Sin cambios |

- Polylang tiene activada `redirect_lang`: la home del país se sirve en `/ar/`; `/ar/inicio-argentina/` redirige a `/ar/`.
- El menú Destinos, las tarjetas de `/destinos/` y los enlaces a `/destinos/<país>/` llevan a `/<ruta>/envios-a-<país>/` (`grenvios_ficha_destino_url()`).
- Las fichas de ese país en OTRAS rutas (`/cu/destinos-cuba/argentina/`) siguen siendo espejos con canónica a `/ar/envios-a-argentina/`.
- Al crear un país nuevo, `inc/paises-home-destino.php` hace la separación sola (acción `grenvios_ruta_duplicada`, prioridad 30).
- Todas las páginas interiores usan el hero a pantalla completa (`inc/hero-paginas.php`); solo las homes conservan el carrusel.

## 4. Países y rutas

| País | Ruta | Locale | Ciudad de origen |
|---|---|---|---|
| Perú | `/` | `es_PE` | Lima *(sede principal)* |
| Ecuador | `/ec/` | `es_EC` | Lima |
| Colombia | `/co/` | `es_CO` | Lima |
| Chile | `/cl/` | `es_CL` | Lima |
| Bolivia | `/bo/` | `es_BO` | Lima |
| Argentina | `/ar/` | `es_AR` | Lima |
| Estados Unidos | `/us/` | `es_US` | Lima |
| España | `/es/` | `es_ES` | Lima |
| Venezuela | `/ve/` | `es_VE` | Lima |
| Cuba | `/cu/` | `es_CU` | Lima |

El origen es siempre Lima: hay una sola operación. El locale solo sirve para
identificar el país de destino de la ruta.

### ⚠️ España: liberar el prefijo `es` antes de nada

La ruta principal nació con el código del **idioma** (`es`), y España necesita ese
mismo código. En *WordPress → Sedes* hay un botón para cambiarlo a `pe`. Las URLs
de Perú **no cambian** (Polylang oculta el prefijo del idioma principal). Hazlo
**antes** de crear contenido.

---

## 5. Lo que falta

Ya no queda arquitectura por construir. Lo pendiente es **contenido**:

1. Crear las rutas que interesen, en *WordPress → Sedes*.
2. **Escribir el cuerpo de cada página**: por qué somos mejores en esa ruta, las
   restricciones reales de esa aduana, los plazos de ese destino. Hasta entonces
   la página va con `noindex` y fuera del sitemap.
3. Priorizar por demanda: empezar por los tres países con más envíos y, dentro de
   cada uno, por precio y plazos, que son las que convierten.

Seis páginas bien escritas posicionan; doscientas copiadas hacen perder posiciones
a todo el dominio.

---

## 6. Notas de verificación

Probado creando países de verdad y borrándolos después. El
sitio quedó como estaba: 34 páginas, un idioma, 0 errores.

```
TOTAL=253  HTTP200=253  ERRORES_RED=0   (216 páginas + 36 entradas + el hub)
NOINDEX=0/253   TOKENS_SIN_RESOLVER=0   ERRORES_PHP=0
CANONICAL_CORRECTO=253/253   HREFLANG_EN_HEAD=0
ENLACES_A_OTRO_PAIS=3718   FUGAS_AL_SITIO_GENERAL=0
```

Y la compuerta comprobada en los dos sentidos: al escribir un párrafo propio en
`/cu/aduana-de-cuba/`, esa página pasó a indexable y apareció en `sitemap-cu.xml`
—que hasta entonces no existía—, mientras las otras 23 de Cuba siguieron en
`noindex` y fuera del índice.

### Fallos encontrados al verificar, ya corregidos

1. **Estados Unidos no se creaba**: estaba como `en_US`, así que el sistema lo
   tomaba por traducción al inglés. Corregido a `es_US`.
2. **Los textos decían lo contrario del servicio**: `{{origen_pais}}` resolvía al
   país de la ruta, pero la ruta es el destino.
3. **Los slugs heredaron ese error** → `/cu/servicios-a-peru/`.
4. **Sembrar el SEO desactivaba la compuerta**: las copias salían indexables.
5. **Las 9 páginas de blog se escapaban** de la compuerta y del SEO por país, por
   no ser `is_singular()`.
6. **La portada de cada país se colaba en su sitemap**, y además duplicada.
7. **Caché estática de idiomas**: al crear y duplicar en la misma petición salían
   slugs como `envios-a-ar` en vez de `envios-a-argentina`.
8. **Siete fugas de navegación** (ver §1), casi todas por `home_url()`.
9. **`/sitemap.xml` respondía 301** a `/sitemap.xml/`: WordPress añade la barra
   final a lo que no reconoce como archivo. La URL que se envía a Search Console
   debe responder 200 directamente.

### Supuestos que resultaron falsos

- ~~`es_BO` y `es_CU` no existen en Polylang~~ → valida el locale por patrón, no
  contra una lista blanca.
- ~~Falta `x-default`~~ → ya estaba en `inc/i18n-seo.php`.

---

## 7. Archivos

| Archivo | Qué hace |
|---|---|
| `inc/paises-rutas.php` | Rutas de país: tipo, SEO, compuerta, duplicador, persistencia |
| `inc/paises-contenido.php` | Secciones de contenido propio de cada país y su matriz |
| `inc/paises-blog.php` | Entradas y categorías por ruta, y respaldo del blog vacío |
| `inc/paises-blog-hub.php` | `/articulos-por-pais/`: los nueve blogs agrupados y filtrables |
| `inc/paises-cabecera.php` | Antesala, H1 y migas de cada página de ruta con su país |
| `inc/paises-espejos.php` | Destinos, fichas y «Artículos por país» en cada ruta, con canónica a la página real |
| `inc/destinos-secciones.php` | Secciones SEO de la página de destino y de la portada de cada ruta |
| `inc/paises-contenido-admin.php` | *Destinos → Contenido por país*: campos y cobertura |
| `inc/paginas-combinadas.php` | Páginas servicio × país y su SEO |
| `inc/destinos.php` | Países destino y bloque «A dónde llegamos» |
| `inc/sedes.php` | Registro de países, alta en Polylang, herencia de datos |
| `inc/sedes-admin.php` | Pantalla *WordPress → Sedes* |
| `inc/sedes-contenido.php` | Tokens de origen y destino |
| `inc/sedes-seo.php` | Compuerta de sedes y schema |
| `inc/i18n-links.php` | Reescritura de enlaces a la ruta activa |
| `inc/i18n-seo.php` | hreflang, `x-default`, `og:locale` |
| `functions.php` | Sitemap por país, schema, SEO por página |

---

## 8. La capa de sedes que quedó construida

Antes de aclarar el alcance se construyó un montaje **multi-sede**: cada país como
operación con teléfono y dirección propios. No es lo que hacía falta, pero se dejó
porque está **inerte** (se apaga sola con un idioma) y serviría si algún día se
abre oficina fuera de Perú. De ella sí se aprovecha ahora el sistema de tokens,
que sustituyó a 190 valores escritos a mano por todo el tema.

---

## 9. Pendiente de decidir: las fichas `/destinos/<pais>/`

Cuando un país tiene ruta, **su ruta y su ficha antigua hablan de lo mismo**:
`/cl/envios-a-chile/` y `/destinos/chile/` compiten por «envíos a Chile». Todos
los enlaces internos ya apuntan a la ruta, así que la ficha queda huérfana pero
sigue publicada e indexada.

Hay que elegir, y es decisión de negocio:

- **Redirigir 301** `/destinos/chile/` → `/cl/envios-a-chile/`. Traspasa la
  autoridad y elimina la competencia. Es lo que haría un SEO, pero es irreversible
  en la práctica.
- **Mantener las dos** con roles distintos: la ficha como resumen dentro del
  índice `/destinos/`, la ruta como el desarrollo completo. Exige recortar la
  ficha para no repetir a la ruta.

Mientras no se decida, la ficha sigue como está: no se ha tocado nada.
