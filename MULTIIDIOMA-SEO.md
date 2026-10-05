# Grenvíos — Multiidioma y SEO internacional

Cómo funciona el sistema de idiomas del tema, cómo se agrega un idioma nuevo y qué
hace (y qué **no** hace) por el posicionamiento en otros países.

---

## 1. Qué se construyó

- **Una página real por idioma**, no traducción al vuelo. Cada versión es una
  página de WordPress con su propio `slug` traducido, su `title`, su
  `meta description` y su contenido guardado en base de datos.
  → Googlebot recibe HTML ya traducido, sin llamadas a ninguna API.
- **URLs traducidas**, que es lo que hace que Google las trate como páginas
  distintas y no como la misma página repetida:

  ```
  grenvios.com/servicios/envio-internacional-de-paquetes/
  grenvios.com/en/services/international-parcel-shipping/
  grenvios.com/pt/servicos/envio-internacional-de-encomendas/
  ```

- **Traducción automática** (Claude, DeepL o Google) que traduce título, slug,
  SEO, todos los textos del editor de página, los bloques repetibles, las FAQ y
  los textos fijos del diseño.
- **hreflang recíproco + `x-default`**, `canonical` autorreferente por idioma,
  `og:locale`, y un sitemap índice con un hijo por idioma.
- **Sin idiomas escritos en el código.** Todo se lee de Polylang: agregar un
  idioma es agregarlo en Polylang y pulsar un botón.

## 1 bis. Ojo: Polylang aquí hace DOS trabajos distintos

Este documento trata el **idioma de verdad**: la misma página traducida a otra
lengua. Pero el tema usa además Polylang como **duplicador por país de destino**
—`/cu/`, `/ar/`, `/es/`— para páginas que están todas en español y se dirigen al
mismo público peruano. Son dos cosas distintas y **se comportan al revés**:

| | Idioma de verdad (`/en/`) | Ruta de país (`/cu/`) |
|---|---|---|
| `hreflang` | Sí, recíproco + `x-default` | **Ninguno** |
| Se ofrece como alternativa | Sí | **No**, es canónica de sí misma |
| Traducción automática | Sí | No: se reescribe para ese destino |
| En el sitemap | Su hijo, con alternates | Su hijo, sin alternates |

El tema los distingue solo comparando el código de idioma del locale: `es_PE` y
`es_CU` comparten «es» → ruta de país; `es_PE` y `en_US` no → idioma de verdad.
Todo lo de este documento aplica a la segunda columna solo donde se diga.

**Las rutas de país están documentadas aparte, en `PAGINAS-POR-PAIS.md`.** Si lo
que quieres es añadir un destino y no una lengua, ese es el documento.

## 2. Polylang es obligatorio

El tema depende de Polylang (versión gratuita es suficiente). Sin él el sitio
sigue funcionando **en español**, pero no hay traducciones, ni hreflang, ni
sitemap multiidioma. Un aviso lo recuerda en el escritorio.

### Configuración de Polylang (una sola vez)

1. **Idiomas → Idiomas**: agrega el español (idioma por defecto) y los que
   quieras. El español debe quedar marcado como **idioma por defecto**: es el
   "idioma maestro" del que se traduce todo.
2. **Idiomas → Ajustes → Modificaciones de URL**:
   - *El idioma se define por el directorio del nombre en la URL de la página* ✔
   - *Ocultar la información del idioma por defecto en la URL* ✔
     (así el español queda en `grenvios.com/...` y los demás en `/en/...`)
3. **Ajustes → Enlaces permanentes**: debe estar en **Nombre de la entrada**.
   Con enlaces "Simple" (`?p=123`) no existen slugs traducidos que indexar.
4. **Idiomas → Ajustes → Sincronización**: deja **desactivada** la
   sincronización de campos personalizados. Cada idioma tiene su propio texto;
   si se sincroniza, la traducción se sobrescribe con el español.

## 3. Uso diario: pantalla «Traducciones»

En el menú del escritorio, **Traducciones**:

| Acción | Qué hace |
|---|---|
| **Traducir todo lo que falta** | Recorre todas las páginas × todos los idiomas y traduce solo lo pendiente. |
| **Traducir / Retraducir** (por fila) | Una página, un idioma. |
| **Textos fijos del diseño** | Menú, botones, formularios, pie, 404. |
| **Fichas de países (Destinos)** | Nombres, plazos y restricciones de cada país. |
| **Probar** | Comprueba que la clave de API responde. |
| **Borrar diccionario** | Fuerza a traducir todo de cero (vuelve a consumir API). |

Estados de cada celda:

- `— falta` → todavía no existe esa versión.
- `al día` → traducida y coincide con el español actual.
- `desactualizada` → se editó el texto español después de traducir. Pulsa
  **Retraducir** en esa fila.

### Coste

Un mismo texto se traduce **una sola vez** aunque aparezca en 20 páginas: todo
pasa por un diccionario por idioma. Traducir el sitio entero a un idioma nuevo
es del orden de 40.000–60.000 caracteres; a partir de ahí solo se paga lo que
cambie.

## 4. Agregar un idioma nuevo (sin tocar código)

> **Cualquier idioma sirve**, no solo los latinos. Chino, japonés, coreano y ruso
> funcionan (el slug se genera romanizado para que la URL sea legible). Árabe y
> hebreo cargan `assets/css/rtl.css` automáticamente, pero el diseño necesita
> una revisión visual antes de publicarse. Detalle en
> `PLAN-SEO-MULTIIDIOMA.md`, sección 10.


1. **Idiomas → Idiomas** → agregar (por ejemplo Italiano).
2. **Traducciones** → aparece una columna nueva → **Traducir todo lo que falta**.
3. Revisar el menú de ese idioma en **Apariencia → Menús** (Polylang crea una
   ubicación por idioma) y la portada estática si se quiere una distinta.

Automáticamente aparece en el selector de idioma del encabezado, en los
`hreflang`, en el sitemap y en la pantalla de Traducciones.

## 5. Qué se traduce exactamente (cobertura)

Auditado en agosto de 2026, tras añadir las páginas de herramienta, las de
contenido, las rutas y las guías.

| Elemento | Cómo se traduce |
|---|---|
| **Páginas** (título, slug, `title`, `meta description`) | Una página real por idioma, con su URL traducida |
| **Textos del editor de página** | Campo a campo, desde el registro de textos |
| **Bloques repetibles y FAQ** | Se materializan y se traducen ítem por ítem |
| **Páginas de país** | Sus fichas (plazos, modalidades, restricciones) se traducen a una opción por idioma |
| **Rutas servicio × país** | Sus cinco campos propios, más la meta de la combinación |
| **Guías (entradas)** | Título, slug, extracto, contenido HTML, categoría, imagen destacada y la página de dinero que refuerzan |
| **Textos fijos del diseño** | Diccionario por idioma, recogido de los `.html` del tema |
| **Textos generados en PHP** | Declarados por cada módulo vía `grenvios_i18n_extra_strings` |
| **Cualquier otro campo guardado** | Barrido final: si hay un texto en la página maestra que no encajó en ninguna categoría, se traduce igualmente |
| **Enlaces internos** | Se reescriben al idioma activo, tanto en las plantillas como dentro del contenido |

Lo que **no** se traduce, a propósito: la marca, los teléfonos, la dirección de
Lima, los números de guía y las URLs de imagen.

### Por qué importaba el barrido final

Los tres huecos que tenía el sistema antes de esta revisión:

1. **Las guías no aparecían** en la pantalla de Traducciones: solo se recorrían
   páginas. Una entrada traducida es una página indexable más, así que ahora
   tienen su propia tabla.
2. **Las rutas** guardaban su contenido en campos que no están en el registro,
   así que la traducción quedaba con los textos en español.
3. **Los textos generados en PHP** (tablas, listas, pasos) solo se traducían
   *después* de que alguien visitara esa página en ese idioma, porque hasta
   entonces no se detectaban como faltantes. Ahora cada módulo los declara y
   «Traducir todo» los deja listos de una vez.

El barrido final es la red de seguridad: cualquier campo nuevo que se añada al
tema en el futuro se traduce aunque nadie se acuerde de registrarlo.


## 6. Cómo encaja con el diseño del tema (para quien mantenga el código)

El tema resuelve **todo** por el slug español: plantillas `content-<slug>.html`,
registro de textos, fondos por página, destinos y FAQ. Las traducciones tienen
otro slug, así que existe el **slug maestro**:

```
página traducida ──(Polylang)──► página en español ──► slug maestro
```

`grenvios_canonical_slug()` devuelve siempre el slug español. Lo consumen
`grenvios_current_slug()` y `page.php`, por lo que el resto del tema no se enteró
del cambio.

| Archivo | Responsabilidad |
|---|---|
| `inc/i18n.php` | Idiomas, slug maestro, selector de idioma, integración Polylang. |
| `inc/i18n-strings.php` | Diccionario de textos fijos + `grenvios_t()` + recorrido de HTML. |
| `inc/i18n-translate.php` | Motor: proveedores, lotes, traducción de páginas, repeaters, destinos. |
| `inc/i18n-links.php` | Reescribe los enlaces internos al idioma activo. |
| `inc/i18n-seo.php` | hreflang, `x-default`, og:locale, alternates del sitemap, diagnóstico. |
| `inc/i18n-admin.php` | Pantalla «Traducciones». |
| `inc/paises-rutas.php` | Rutas de país: duplicador, slugs, compuerta, sitemap por país. |
| `inc/paises-contenido.php` | Secciones de contenido propio de cada destino. |

Ganchos añadidos al tema (puntos únicos, sin duplicar lógica):

- `grenvios_partial_html` — todo el HTML del diseño pasa por aquí (traducción de
  textos fijos, reescritura de enlaces, selector de idioma).
- `grenvios_destinos` — fichas de país en el idioma activo.
- `grenvios_destino_permalink` y `grenvios_rep_link` — enlaces por idioma.
- `grenvios_seo_for_slug( $slug, $post_id )` — el `title`/`description` guardado
  en la página manda sobre el texto por defecto del tema.

Los tokens `{{campo}}` y los marcadores (`HOMEURL`, `DESTINOSMENU`…) se protegen
antes de enviar el texto al traductor y se verifican al volver: si el traductor
perdió alguno, se descarta esa traducción y se conserva el original. Un token
roto rompe la página; un fragmento sin traducir, no.

## 7. SEO internacional: qué esperar de verdad

**Lo que hreflang sí hace:** decirle a Google que `/en/services/` es la versión
en inglés de `/servicios/`, para que no las trate como duplicado y le muestre a
cada usuario la de su idioma.

**Lo que hreflang NO hace:** posicionar por país de destino del envío. Casi todos
los destinos de Grenvíos (Ecuador, Colombia, Chile, Bolivia, Argentina, España,
Venezuela, Cuba) hablan español: **crear una versión `es-EC`, `es-CO`, `es-CL`
del sitio sería contenido duplicado**, no una mejora. Google lo trataría como
la misma página repetida y elegiría una.

Ese tráfico se gana con lo que el tema ya tiene:

- Las páginas `/destinos/<país>/`, una por país, con sus plazos, restricciones y
  FAQ propias. Ahí está la intención geográfica.
- El cruce interno servicio × país (`/servicios/envio-internacional-de-paquetes/`
  ↔ `/destinos/ecuador/`), que multiplica las combinaciones de cola larga.

Las traducciones sirven para un público **distinto**: quien busca en inglés,
portugués o italiano. Ese es el motivo real para tenerlas, y por eso conviene
priorizar idiomas con demanda propia antes que multiplicar variantes de español.

Solo tiene sentido separar variantes del mismo idioma (`es-ES` vs `es-PE`) si el
contenido es **realmente distinto**: precios, moneda, plazos o normativa. El
diagnóstico de la pantalla Traducciones avisa cuando detecta variantes del mismo
idioma.

### Después de publicar

1. Google Search Console: **una propiedad para todo el dominio** (las versiones
   de idioma no se separan si están en subdirectorios).
2. Enviar `https://grenvios.com/sitemap.xml`. Con más de un idioma o país deja de
   ser una lista plana y pasa a ser un **índice** con un hijo por ruta
   (`sitemap-pe.xml`, `sitemap-cu.xml`…). Es lo que permite ver en Search Console
   si Google indexa la ruta de Cuba o solo la de Perú, dato que un sitemap único
   de cientos de URLs mezcladas no separa.
3. Revisar el informe de **Segmentación internacional** las primeras semanas: si
   aparecen "etiquetas hreflang sin retorno", es que falta traducir alguna página
   (el selector y el sitemap solo listan las publicadas, así que suele resolverse
   completando la traducción).

## 8. Lo que hay que revisar a mano después de traducir

La traducción automática es buena, pero estos puntos conviene mirarlos una vez
por idioma:

- **Títulos de botones y menús**: que no desborden el diseño.
- **`title` y `meta description`**: que respeten ~60 y ~155 caracteres.
- **Slugs**: se generan traduciendo la frase, no palabra por palabra. Si una
  página es estratégica, vale la pena elegir el slug a mano con la keyword real
  del mercado (se edita en la propia página de WordPress).
- **Datos que no se traducen a propósito**: teléfono, dirección de Lima, marca,
  números de guía.
- **Restricciones aduaneras por país**: son texto legal/operativo; que un humano
  confirme que la traducción no cambió el sentido.
