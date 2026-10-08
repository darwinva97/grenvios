---
name: grenvios-diseno
description: Sistema de diseño y reglas de contenido del tema Grenvíos (WordPress, envíos internacionales desde Lima). Úsala SIEMPRE antes de añadir o cambiar una página, una sección, un artículo del blog o estilos del tema, y al verificar que el sitio está bien implementado. Cubre componentes CSS existentes, tokens de color y tipografía, cómo crear páginas y guías editables desde el panel «Editar página», reglas de redacción sin cifras inventadas y los scripts de auditoría.
---

# Grenvíos — diseño y contenido

> **Páginas que venden:** composición por tipo de página, fondos, fotos de ejemplo, animación y control SEO → skill **grenvios-landing**.
>
> **Diseño nuevo:** el cliente rediseña con maquetas. Antes de tocar el aspecto de una sección, carga la skill **grenvios-v2** (lenguaje visual, patrones hechos, capa global `body.gr-v2`, interruptor por página).

Tema WordPress en `wp-content/themes/grenvios/`, sitio local `http://greenvios.localhost/`
(Laragon, PHP en `C:/laragon/bin/php/php-8.2/php`). Ruta principal = Perú (sin prefijo);
9 rutas de país con prefijo (`/ec/ /co/ /cl/ /bo/ /ar/ /us/ /es/ /ve/ /cu/`). `/ec/` significa
**envíos A Ecuador desde Lima**, no desde Ecuador.

> **Diseño visual de secciones, imágenes y animaciones → skill `grenvios-ui`.** Cárgala también
> cuando crees o cambies una sección: una sección nunca es solo «título + párrafo».

> **Norma de secciones** (qué debe tener cada una, convertidor de secciones de solo texto, auditoría y comparación entre países) → skill **grenvios-secciones**.

> Para el diseño de páginas de ruta, servicio, destino y blog por país (bloques del país v3, formulario de solicitud, cabecera del blog), ver la skill **grenvios-rutas**.

## 1. Regla de oro: reutilizar, no inventar

No escribas CSS nuevo si un componente ya lo resuelve. Antes de crear una clase, busca en
`style.css` (las secciones están separadas por banners `════`). Todo lo nuevo debe verse
igual que lo existente en móvil (575 / 767 / 991 px) y escritorio.

### Tokens
| Token | Valor | Uso |
|---|---|---|
| `--primary-color` | `#5e2129` (editable en el panel global «Colores») | marca, enlaces, números de pasos |
| `--gr-acc` | `#d8465c` | acento: viñetas, 2.ª línea de títulos hero |
| `--heading-color` | `#0c0c0c` | titulares, `<strong>` en listas |
| `--body-color` | `#666` | párrafos |
| `--bg-grey` | `#f6f7f9` / `#f8f5f1` | fondo de secciones alternas (`.bg-grey`) |
| `--gr-r-lg` / `--gr-r-md` | 16px / 12px | radios de tarjetas y tablas |
| Tipografía | Poppins (`--primary-font`, `--body-font`) | titulares con `letter-spacing:-.025em`; texto sin tracking |

Nunca pongas colores en duro: usa `var(--primary-color, #5e2129)` con respaldo.

### Componentes (clase → para qué)
| Componente | Marcado | Dónde se usa |
|---|---|---|
| Banner de página | `grenvios_tool_banner( $antetitulo, $titulo )` | todas las páginas pintadas por PHP; `hero-paginas.php` añade entradilla, garantías y botones |
| Intro centrada | `<section class="srv-intro padding-top"><div class="container"><div class="srv-lead text-center"><h2>…<p>…` | primer bloque tras el banner |
| Sección | `<section class="srv-section [bg-grey] padding"><div class="container"><div class="srv-head text-center"><h2>…` | cada bloque; alterna `bg-grey` para dar ritmo |
| Tarjetas 2 columnas | `.srv-two-grid > .srv-panel > h3.srv-panel-title + p` | comparativas, 2–4 tarjetas |
| Tarjetas 3 columnas | `.srv-trio-grid` | tres opciones paralelas |
| Lista con vistos | `<ul class="check-list"><li><i class="fa-solid fa-check"></i> …` | listas cortas dentro de secciones `srv-*` |
| Lista destacada | `ul.gr-pseo-list` (tarjeta blanca + punto de acento) | secciones de contenido SEO; `<strong>` al inicio de cada punto |
| Pasos numerados | `ol.gr-pseo-steps` (contador circular de marca) | procesos; `<li><strong>Título.</strong> texto` |
| Tabla | `<div class="gr-table-wrap"><table class="gr-table">` con `<th scope="col|row">` | datos comparativos; la tabla mide mín. 620px y el wrap se desplaza en horizontal (`overflow-x:auto`); nunca `overflow:hidden` en el wrap: recorta columnas en móvil |
| Botón | `<a class="default-btn">` (variante `btn-light` sobre fondo oscuro) | **`theme-btn` NO existe** en el CSS: salía como texto plano en 7 sitios |
| Nota | `<p class="gr-nota">` | aclaración o enlace al siguiente paso al final de un bloque |
| Mensaje ejemplo | `<blockquote class="gr-msg">` | plantillas de WhatsApp |
| Acento en título | `<span class="hl">palabra</span>` | una o dos palabras por título, no más |
| Sección SEO | `grenvios_pseo_render_una()` → `.gr-pseo` con `section-heading` + `h3.sub-heading` + `h2` | secciones añadidas por el filtro `grenvios_pseo_secciones` |

Formularios: campos con `form-control` (el de comentarios se añade por filtro en `inc/blog-guias.php`).
Entradilla SEO (`.gr-ent`): va **siempre justo debajo del hero**. `page.php` la coloca con
`grenvios_ent_tras_hero()` también en las páginas pintadas por PHP; no la imprimas a mano.
**Desde 2026-10-08 la entradilla suelta no se muestra** (el cliente no la quiere, ni como caja ni como sección con foto): se quita en `grenvios_html_final` (prioridad 62, `inc/seo-entradillas.php`). Solo queda en las páginas de `grenvios_si_paginas()`, que la integran en su franja de cita, y solo allí aparece el campo «Entradilla SEO» del panel. No la vuelvas a añadir sin preguntar.

**Imágenes de relleno.** Casi todo `assets/img/` son rellenos de la plantilla («1000X650»,
siluetas grises): `post-*`, `content-bg-*`, `page-banner`, `slider-bg`, `hero-background`,
`testimonial-bg`, `banner-add`, `delivery-men-2`, `forklift`. Fotos reales: `hero-home.jpg`
(avión en pista), `destino-hero.jpg` (camión con la marca), `cta-repartidor-4.webp` (repartidor
con caja Grenvíos) y `uploads/2026/09/ab-left-img.jpg`. **Nunca pongas un relleno como imagen
por defecto.** `inc/diseno-tarjetas.php` ya quita la foto de relleno de las tarjetas de servicio
(quedan con icono) y la silueta de las llamadas a la acción; si el cliente sube una real, sale sola.

**Sticky.** `html, body` usan `overflow-x: clip` (con `hidden` de respaldo): con `hidden`, ningún
`position: sticky` funcionaba. No lo vuelvas a `hidden`. La cabecera fija mide 80 px en escritorio
(`.sticky-header.sticky-fixed-top`) y no existe al bajar en móvil; el sitio usa Lenis
(`window.lenis.scrollTo`) para los saltos con ancla.

**Índice «En esta página»: estático, nunca sticky.** Las fichas llevan una fila de chips
(`inc/destinos-indice.php`) justo encima de «Nuestras soluciones de envío», con el primer chip
relleno. El cliente rechazó la versión pegada (sticky) al bajar: la fila se queda en su sitio y solo
hace scroll suave con Lenis. No añadas barras sticky nuevas sin preguntar. Si añades una sección clave
a la ficha, añádela a `grenvios_toc_items()`.

**Banderas:**
- Usa siempre `grenvios_bandera_img( $iso, $clase, $ancho )` o `grenvios_bandera_url( $iso )` (inc/destinos.php). Son las SVG 4:3 de `assets/img/flags-svg` (flag-icons, MIT).
- Nunca los PNG de Polylang: miden 16×11 y se ven borrosos al ampliarlos.
- Bolivia y España usan su bandera civil, sin escudo (oficial y de 1 KB).
- El CSS solo fija el ancho; la proporción la da `aspect-ratio: 4/3`.

Iconos: Font Awesome 6 (`fa-solid fa-…`). Imágenes decorativas y banderas junto a su nombre llevan `alt=""` (correcto); las de contenido, `alt` descriptivo.

## 2. Todo texto visible debe ser editable desde el panel

El panel «Editar página» (botón flotante para administradores, `inc/page-editor.php`) se
construye desde el **registro de textos** (`grenvios_text_registry`). Un texto pintado con
`grenvios_tf( 'clave', 'defecto' )` que no esté registrado **se ve pero no se puede editar**.

Reglas:
- Lee siempre con `grenvios_tf()` / `grenvios_field()`; nunca texto fijo en el HTML.
- Registra cada clave con `[ etiqueta, tipo, defecto ]`; tipos: `text`, `textarea`, `html` (admite enlaces), `image`.
- Agrupa por **sección en el orden de la página**; la sección del banner se llama `hero` (el panel la pone primera y le añade los campos del hero).
- Secciones pintadas por PHP: `'_no_token_check' => true`.
- Listas: un campo `textarea` «una línea por punto».
- **Una sola fuente**: define el contenido como datos y genera render y registro del mismo array (patrón de `inc/paginas-servicios-extra.php`). Duplicar el texto por defecto en dos sitios acaba desincronizado.
- Si registras un slug que ya existe, **fusiona** secciones (`array_merge`), no lo saltes con `if ( ! isset )`: así se perdieron las secciones de Aduanas, Alimentos, Seguro y Provincias.
- El guardado (`grenvios_rest_save_page`) filtra el HTML con una lista blanca: tablas, listas con clase, `blockquote` y `div` con clase están permitidos. Si un componente nuevo necesita otra etiqueta, añádela ahí.

**Secciones generadas por PHP** (`inc/destinos-textos-editables.php`): en los contenedores de
`grenvios_dt_contenedores()` (secciones de las fichas, bloques de la home, `srv-section`, cobertura,
barra lateral de FAQ…) cada texto se vuelve editable solo, con clave `dt_` + hash del texto que
genera la plantilla. Se procesa el **HTML final** (ob en `template_redirect`), después de cualquier
caché; el panel deja `<!--GR_DT_PANEL-->` y se rellena al final. Si creas una sección PHP sin campos,
añade su clase a esa lista en vez de dejarla fija. **Dentro del manejador de búfer no se puede
llamar a `ob_start()`** (la página sale en blanco para los administradores): pinta el HTML a mano.
Marca con `gr-editable-propio` una sección que ya tenga sus campos, para no duplicarlos.
**Rendimiento:** a los visitantes solo se les procesa la página si tiene algún `grenvios_dt_*`
guardado. Las expresiones regulares sobre el HTML completo van siempre «desenrolladas»
(`[^<]*(?:<(?!…)[^<]*)*?`), nunca con `(?:(?!…).)*?`: esa forma añadía 1,5–2,8 s por página.
Mide cualquier filtro sobre el HTML final con una prueba A/B (módulo activo / desactivado).

## 3. Crear contenido

### Página nueva (ruta principal)
Copia el patrón de `inc/paginas-servicios-extra.php`:
1. Contenido como datos en una función `…_def( $slug )` (bloques `intro`, `panels`, `lista`, `pasos`).
2. `grenvios_pages` (title, seo ≤ 60 car., desc 70–170 car., parent) y keyword (`grenvios_seo_kw_default`).
3. Creación con **opción propia versionada** (`update_option( 'mi_modulo_v', 1 )` en `admin_init`). Las opciones de otros módulos ya están en 1: añadir slugs allí no crea nada.
4. Render en `grenvios_render_page_early` (prioridad 12).
5. Registro del panel generado desde los mismos datos.
6. Entradilla (`grenvios_ent_textos`), FAQ (`grenvios_page_faqs`, van al schema), enlazado (`grenvios_related_map`, **también enlaces entrantes** desde páginas con autoridad) y texto para la auditoría SEO (`grenvios_seo_page_text`).
7. `require_once` en `functions.php` y `grenvios_cache_bump()` (el bloque de enlaces está en caché).

**Atajo:** para una página de contenido/servicio no hace falta un módulo nuevo. Añádela con los
filtros `grenvios_pse_slugs` + `grenvios_pse_paginas` (ejemplo: `inc/paginas-servicios-extra2.php`),
con `parent` ('' = raíz), `label`, `kw`, `meta` [title, seo, desc], `ent`, `hero`, `bloques`, `faqs`
y `relacionados`, y **sube `GRENVIOS_PSE_V`** para que se cree. Bloques: `intro`, `panels`, `lista`,
`pasos` y `definiciones` (glosarios: «Término: definición», con ancla por término).
Bloque `destinos`: tabla alimentada por el gestor de destinos (`'via' => 'aereo'|'terrestre'`,
`'paises' => [slugs]`, `'extra' => ['Canadá',…]` para países sin ficha de `grenvios_destinos_extra()`,
`'orden' => 'plazo'`); nunca escribas plazos ni impuestos a mano.

**Jerarquía de destinos** (`inc/paginas-regiones.php`): hub `/destinos/` → región (`/envios-a-…/`) →
ficha de país. `grenvios_regiones()` dice qué fichas van en cada región; cada ficha enlaza a su región
(«Tu región») porque las fichas también pasan por `grenvios_related_map`. Un país nuevo con ficha
debe añadirse a su región.

**Clúster temático** (`inc/paginas-cluster.php`): cada página de apoyo responde una búsqueda que su
pilar no cubre sin perder foco, enlaza al pilar (y a la home con «envíos internacionales desde Lima»)
y recibe enlaces del pilar por `grenvios_related_map` (las cuatro páginas de servicio principales
también pasan por ese filtro). No crees una página que compita con la home o con un pilar por la
misma keyword.

### Sección nueva en las fichas de destino (/destinos/<país>/ y portada de cada ruta)
Las fichas ya tienen 27–28 secciones de plantilla: **no añadas más plantilla**. Añade solo lo que
cambia de verdad entre países (ejemplo: `inc/destinos-datos-practicos.php`). Enganches, en orden:
`grenvios_destino_tras_info`, `grenvios_destino_tras_proceso` y `grenvios_destino_antes_cta`
(prioridad 10 = orden de `require_once`). Usa `grenvios_dsec_open()`, `grenvios_dsec_card()` en
`<div class="row gy-4">` y `grenvios_dsec_close()`, y notas con `.dest-ciudades-pie`.
Panel: **no registres el slug del país** en `grenvios_text_registry` (el panel dejaría de construir
las secciones de la ficha); añade un acordeón con la acción `grenvios_editor_secciones` y usa
`grenvios_editor_pais_slug()` para cubrir también la portada de la ruta.

**Enlazar desde el hub.** Todo servicio nuevo bajo /servicios/ debe entrar en el bloque «Más
servicios» del hub (`grenvios_sm_items()` en `inc/servicios-mas.php`), y toda página nueva debe
tener tema de blog (`grenvios_bep_temas`) para que su «Del blog» sea relevante.

### Sección de contenido en una página existente
Filtro `grenvios_pseo_secciones` (ver `inc/contenido-ampliacion.php`): `sub`, `titulo`, `html`.
Aparece sola en el panel. Solo se pinta en la ruta principal.

### Guía del blog
Patrón de `inc/blog-guias-nuevas.php`: `titulo` (sin «al extranjero»: las copias añaden «…a Chile»),
`categoria` (`guias-de-envio|documentos|aduanas|destinos|empresas`), `pilar` (página que refuerza),
`extracto`, `html` con `<h2>`, listas y FAQ al final con `grenvios_ga_faq()`.
**Importa solo las nuevas** con una función que restrinja la lista (`grenvios_guias_nuevas_importar()`):
el importador general reescribe todas las guías y borraría retoques hechos a mano. Antes de importar,
comprueba cuántas copias crearía el duplicador (debe ser solo las tuyas × 9 rutas).

**Etiquetas:** se asignan por patrones del slug (`grenvios_etq_def`). Añade el patrón de cada guía
nueva y etiqueta **solo las nuevas** (`grenvios_guias_nuevas_etiquetar()`); `grenvios_etq_sync()`
reescribe las etiquetas de todas las entradas. Sin etiqueta, la guía no sale en ningún «Del blog».

Marcadores en el HTML: `%H%` raíz del sitio · `%P:slug%` otra guía (se resuelve al pintar, también
en su forma dañada `href="slug%"`) · `{{origen_ciudad}}`, `{{origen_pais}}` datos de la sede.

### Detalle de entrada del blog (single.php v2, 2026-10-02)
- **Archivos:** `single.php`, `inc/blog-detalle.php` y `assets/css/gr-blog.css` (solo se carga en entradas).
- **Cabecera:**
  - degradado vino con la foto del tema (`grenvios_bd_img`: la destacada o, si no hay, una de ejemplo por tema; nunca un relleno);
  - la foto no lleva animación WOW, porque es la imagen principal (LCP);
  - migas Inicio › Blog › Categoría (`grenvios_bd_migas`) y H1 **sin** text-transform;
  - autor «Equipo Grenvíos», fecha en español (`grenvios_bd_fecha`; el paquete es_PE no está instalado) y minutos de lectura.
- **Artículo:**
  - «Respuesta rápida» con la 1.ª pregunta frecuente de la guía (pensada para el fragmento destacado);
  - índice de los H2 con anclas, sin la llamada «¿Envías a…?»;
  - caja de autor con la fecha de actualización (E-E-A-T);
  - comentarios en español.
- **Lateral, estático:** cotizar, el servicio pilar de la guía y más guías del mismo país (en la ruta principal, solo generales).
- **FAQ duplicada:** si la guía trae `gr-post-faq`, se quita la del bloque de país (`grenvios_bd_sin_faq_doble`).
- **Schema BlogPosting:** autor organización e imagen real, con el filtro `grenvios_blogposting_node`.
- **Capturas (`capturas.mjs`):** usan el puerto 0 y leen `DevToolsActivePort`. Edge 154 ya no abre un puerto fijo.

### SEO por país y canibalización (2026-10-02)
- **Todas las URL en primer nivel** (`inc/urls-primer-nivel.php`):
  - Páginas, entradas (`/%postname%/`, sin fecha), categorías y etiquetas sin `/category/` ni `/tag/`, y servicios sin `/servicios/`. Todo vale igual dentro de cada ruta (`/ec/slug/`).
  - Las URL antiguas responden 301.
  - Páginas nuevas: `'parent' => ''` (ejemplo: `inc/paginas-primer-nivel.php`).
  - Las hijas de `/servicios/` siguen siéndolo en la base (el código las busca por `servicios/slug`): solo cambia su URL pública.
  - `/destinos/<país>/` y los espejos `/ec/destinos-ecuador/<país>/` llevan con 301 a su ficha de ruta. Los administradores no se redirigen, para poder editarlas.
  - **`/destinos/` se llama `/envios-internacionales/`** (2026-10-08), como el menú. En la base sigue siendo la página `destinos` con sus hijas, porque el código las busca con `get_page_by_path( 'destinos/…' )`: no la renombres. `inc/urls-primer-nivel.php` §6 añade las reglas, el filtro `page_link` (con él la canónica, el sitemap y las migas), un 301 desde `/destinos/…` y la reescritura de los enlaces fijos (plantillas, guías, contenido guardado y JSON-LD) sobre la página entera. Hace falta que un administrador entre en el panel una vez para regenerar las reglas (`GRENVIOS_UPN_V` = 3). En enlaces nuevos puedes seguir escribiendo `/destinos/`: se convierten solos.
- **Slugs únicos por ruta entre páginas, entradas, categorías y etiquetas.** Al estar todo en el mismo nivel, gana la página y la entrada o el término quedan inaccesibles. Antes de crear algo, comprueba el slug.
  - Los términos que chocaban se renombraron (`grenvios_upn_renombres()`): categoría `destinos`→`guias-de-destinos`; etiquetas `aduanas`→`tramites-de-aduana`, `envios-para-empresas`→`guias-para-empresas`, `rastreo-de-envios`→`seguimiento-de-envios`, `peso-volumetrico`→`calculo-de-peso-volumetrico`, `envios-a-<país>`→`guias-envios-a-<país>`.
  - Las búsquedas por el slug viejo se traducen solas, porque `get_terms_args` hace la conversión.
- **Sitemap agrupado**:
  - `/sitemap.xml` es un índice de `/sitemap-<ruta>-{paginas,entradas,categorias,etiquetas}.xml`, con una hoja XSL para leerlo en el navegador.
  - Una URL entra con `tipo` en el filtro `grenvios_sitemap_urls`; si no lo lleva, se deduce de `post_id`.
- **Blog sin duplicados entre rutas** (`inc/seo-canibalizacion.php`):
  - La copia de una guía general en una ruta pone su canónica en la original de la ruta principal (eran 92 % iguales entre países).
  - La maestra `…-desde-peru` de una guía de país pone su canónica en la copia de la ruta del país.
  - Las dos quedan fuera del sitemap.
- **Blog por país que da autoridad a las páginas de la ruta**:
  - Hay 35 guías propias por país. Las 12 últimas salen de `blog-paises-tematicas.php`: 3 ciudades principales con datos de ciudad (pilar: la ficha) y 9 productos con regla propia del país (celulares/IMEI, perfumes, ropa según la estación, artesanía, repuestos, maca/quinua/café/cacao, joyas, útiles según el calendario, cumpleaños).
  - **No generar guías por plantilla en masa.** El cliente pidió 200 por país; se acordó hacer solo las que tienen datos reales. Antes de añadir otra tanda, busca una intención nueva y un dato propio del país; si no hay dato, no hay guía.
  - Antes había 23 guías por país, todas indexables (la 6.ª fuente, `blog-paises-mas.php`, cubre seguimiento → rastreo, apostilla/legalización → apostilla, caja → peso volumétrico, estudiar allí → equipaje, avisar al destinatario → contacto y pedir de Perú desde allí → ficha). Las primeras 17:
    - 9 de país (`blog-paises-contenido.php`);
    - 3 locales (`blog-paises-locales.php`);
    - 5 de apoyo (`blog-paises-apoyo.php`): mercadería → carga, vender allí → empresas, compras → envío de compras, valor declarado → seguro, vía → ficha.
  - Cada guía de apoyo enlaza a su página con la keyword de esa página como texto del enlace y la declara pilar. La página le devuelve el enlace («Del blog» elige por tema en `grenvios_bep_locales()`).
  - Importa por tandas de 3 países: el importador completo se queda sin memoria.
  - El `<title>` de una entrada larga se corta en los dos puntos (`seo-entradas.php`): pon la keyword antes de los dos puntos.
  - Los enlaces `%P:` entre guías encuentran la guía también por su clave o por su slug antiguo, así que no se rompen si cambia el slug público.
- **Lo que posiciona cada ruta en el blog** son sus guías de país (`inc/blog-paises-contenido.php`) y las locales (`inc/blog-paises-locales.php`: dirección, medicinas/alimentos/baterías, productos peruanos), hechas con los datos de `paises-perfil.php`.
  - Las guías no repiten la keyword de una página de la ruta. Por ejemplo, la guía es «Tu primer envío a X» y la página es «Cómo enviar un paquete a X».
  - Para rehacer solo unas guías: `grenvios_guias_pais_importar( $claves )`. Una plantilla puede fijar su `'slug'` aparte de su clave.
- **Una keyword, una página.** Antes de crear una, comprueba el mapa (`grenvios_seo_kw`): hoy no hay ninguna repetida. Portada de ruta = «courier de Perú a <país>»; ficha de ruta (`/ec/envios-a-ecuador/`) = «envíos a <país>». No vuelvas a poner «envíos a <país>» en la portada de la ruta.
- **`/destinos/<país>/` cede la canónica** a la ficha de su ruta y sale del sitemap (`inc/seo-canibalizacion.php`). Los enlaces internos ya apuntan a la ruta.
- **Contenido propio por país** (`inc/paises-perfil.php`): aduana, documento del destinatario, moneda, formato de dirección, quién envía y reglas de medicinas/alimentos/baterías. Se pinta al vuelo según `grenvios_perfil_matriz()` y genera las FAQ de cada página de la ruta (sustituye las genéricas repetidas). Si añades un país, añade su perfil.
- **Contenido local en cada página de ruta** (`inc/paises-paginas-locales.php`):
  - Datos verificables por país (`grenvios_ppl_datos()`): organismos sanitarios, régimen de aduana, apostilla e idioma, diferencia horaria con Lima, feriados, temporadas, ruta o frontera, clima para embalar y unidades.
  - Cada tipo de página recibe dos bloques según su tema (`grenvios_ppl_matriz()`), y esos mismos datos generan preguntas frecuentes propias.
  - En las rutas, un filtro reduce las tablas «de los diez países» a la fila del país y quita la FAQ duplicada del bloque de país.
  - «Del blog» de una ruta muestra primero las guías de ese país (`grenvios_bep_locales()`).
  - Nunca pongas una frase fija igual en las nueve rutas dentro de estos bloques: es justo lo que se mide como duplicado.
- **Medir el parecido** entre copias de países: shingles de 5 palabras con el nombre del país enmascarado, solo sobre el contenido principal. Las fichas con la misma ficha técnica (Ecuador/Colombia) rondan el 75-80 %: el siguiente paso es reescribir por país los párrafos de plantilla de la ficha.

## 4. Redacción

- Español neutro, tuteo, frases cortas. Cada sección responde una duda concreta; nada de relleno.
- **Cero cifras inventadas.** Solo se afirma lo que el sitio ya afirma: peso volumétrico = largo × ancho × alto ÷ 5000 · vía aérea y terrestre (terrestre solo a países vecinos, admite más: líquidos sellados, baterías) · impuesto de rutas terrestres pagado en {{origen_ciudad}} al despachar · entrega a domicilio o en agencia · seguimiento por número de guía. Plazos y precios: «te lo confirmamos al cotizar» o enlace a `tiempos-de-entrega`.
- Datos del país que el tema no puede saber (ciudades, prohibiciones) se dejan vacíos: vacío = la sección no se pinta.
- Cada bloque termina llevando al siguiente paso con ancla descriptiva (no «haz clic aquí»).
- Title único ≤ 60 car. con «| Grenvíos»; un solo H1; meta description 70–170 car.

## 5. Verificar (obligatorio al terminar)

```bash
cd .claude/skills/grenvios-diseno/scripts
php campos-sin-registrar.php          # textos visibles no editables (salida vacía = bien)
php auditar-sitio.php greenvios.localhost 20   # prueba rápida
php auditar-sitio.php greenvios.localhost      # todo el sitio (≈10 min)
```
`php cobertura-panel.php greenvios.localhost pe [texto-de-url]` compara cada texto visible con los
campos del panel y dice qué no se puede editar (objetivo: >97 %; lo restante debe ser navegación o
datos del gestor).
`auditar-sitio.php` revisa como visitante y como administrador: HTTP, errores PHP, H1, title,
meta, canonical, schema, tokens visibles, FAQ repetidas, enlaces rotos o relativos sospechosos,
panel de edición, banner primero y secciones vacías. No lances dos auditorías a la vez: MySQL de
Laragon se queda sin memoria y aparecen falsos «HTTP 0».

### SEO de <head>, accesibilidad y rendimiento (rápido, sin navegador)
```bash
PYTHONIOENCODING=utf-8 MSYS_NO_PATHCONV=1 python a11y-perf.py ec/ ec/envios-a-ecuador/ aduanas/ cotizar/
```
Revisa:
- **SEO:** title 10–60, description 70–170, canonical, og:* y twitter:card (y que og:image responda y no sea un relleno), un H1.
- **Accesibilidad:** alt, enlaces y botones sin nombre, campos sin etiqueta, ids duplicados, saltos de título.
- **Rendimiento:**
  - CSS que bloquea y scripts en `<head>` sin defer;
  - imágenes sin medidas;
  - imagen principal en lazy;
  - peso del HTML y tiempo de respuesta.

Con poca RAM, úsalo en lugar de la auditoría completa, que se corta por falta de memoria.

`inc/rendimiento-a11y.php` corrige sobre la página ENTERA (búfer propio; `grenvios_html_final` solo ve trozos):
- **Medidas de imagen:** width/height de las imágenes locales, leídas una vez y guardadas en la opción `grenvios_img_dims`. La regla `:where(img){height:auto}` mantiene la proporción.
- **Nombres accesibles:** de los botones de búsqueda y «volver arriba», de los campos con placeholder y de la paginación.
- **CSS diferido:** animate, keyframe, odometer, venobox y nice-select se cargan con `media=print onload`.
- **og:image y sitemap de imágenes:** nunca un relleno (filtros `grenvios_og_image` / `grenvios_sitemap_image`).

### Revisión visual (obligatoria en cambios de diseño)
```bash
node capturas.mjs <carpeta-salida> http://greenvios.localhost/pagina/ [más URLs]
```
Captura por tramos de 8000 px (las fichas en móvil pasan de 20 000). Abre cada URL en Edge/Chrome headless (Node 22, CDP) a 1366 px y 390 px, recorre la página para
disparar animaciones, guarda la captura completa y avisa de: scroll horizontal, elementos fuera del
ancho, contenido **recortado** por `overflow:hidden`, tablas sin contenedor, imágenes rotas y
titulares desbordados. Después **mira las capturas** (córtalas en tramos, miden hasta 10.000 px):
el detector no ve un botón sin estilo ni una sección mal colocada.

Para menús y desplegables, hover/clic reales por CDP (no forzar CSS).
