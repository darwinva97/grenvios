---
name: grenvios-landing
description: Diseño de páginas que venden para Grenvíos (courier internacional desde Lima) — composición de cada tipo de página, fondos de sección, banco de fotos de ejemplo, animación con criterio y lista de control SEO + conversión. Úsala SIEMPRE que una página se vea «básica» (título + texto), al crear o rediseñar una página completa, al elegir fotos o fondos, al añadir animaciones y al revisar contenido como redactor SEO para captar tráfico y contactos. Complementa a grenvios-v2 (lenguaje visual de las maquetas), grenvios-ui (patrones base) y grenvios-diseno (contenido, panel, auditoría).
---

# Grenvíos — páginas que venden

Negocio: courier internacional desde Lima (documentos, paquetes, carga, apostilla) hacia 9 países.
El visitante llega con una duda concreta (**¿cuánto cuesta?, ¿cuánto demora?, ¿puedo enviar esto?**)
y la página tiene un solo objetivo: que **cotice o escriba por WhatsApp**. El diseño sirve a eso.

Referencias del sector (DHL, FedEx, webs de logística 2026): herramienta de cotizar/rastrear al
frente, un CTA principal por pantalla, señales de confianza visibles, fotos reales de operación
(almacén, embalaje, entrega), secciones amplias con un solo mensaje, móvil primero (en LATAM la
mayoría del tráfico es móvil).

## 1. Receta por tipo de página (orden de secciones)

| Tipo | Orden | Dónde vive |
|---|---|---|
| **Servicio** (`/servicios/…`) | hero + garantías → entradilla → intro con imagen → tarjetas/paneles → franja de garantías → CTA vino → **contenido SEO con foto** → guías con miniatura → «Del blog» → enlaces → CTA final → FAQ | motor pse, partials, `ui-global.php` |
| **Destino** (`/destinos/<país>/`) | hero → chips estáticos → soluciones (foto + 2×2) → datos + qué enviar → precio (4 factores) → plazos → listas en tarjetas → **cobertura con foto del país** → datos prácticos → comparativa → cotizador → FAQ | `functions.php`, `inc/destinos-*.php` |
| **Portada** | cotizador en el hero → quiénes somos (collage) → servicios con foto → destinos con foto de ciudad → cifras → proceso → precio → tabla de plazos → testimonios → guías | plantilla + `customizer.php` |
| **Guía del blog** | H1 + extracto → cuerpo con H2 que responden preguntas → FAQ → «Sigue leyendo» con miniatura → CTA | `inc/blog-guias*.php` |

Reglas de composición:
- **Nunca dos secciones seguidas con la misma forma** (dos rejillas de tarjetas, dos listas). Alterna: texto+foto → tarjetas → tabla → franja.
- **Un elemento visual por sección**: foto, ilustración, icono grande, número o bandera. Una sección de solo texto es un error.
- **Un CTA cada 2–3 pantallas**, siempre el mismo verbo («Cotizar mi envío») + WhatsApp como secundario.
- Texto + foto **alterna de lado** (`gr-pseo--inv` en las pares).

## 2. Fondos de sección

| Fondo | Cuándo | Cómo |
|---|---|---|
| Blanco | lectura larga, tablas | por defecto |
| Rosado suave `--v2-fondo` | secciones alternas (`bg-grey` bajo `body.gr-v2`) | automático |
| Rosado con esquinas de 40 px | la sección principal de la página | `.gr-dsol--v2` |
| Vino degradado | franjas de cierre y CTA; **máx. una por pantalla** | `.dest-precio-cta`, `.grenvios-cta-box` |
| Foto con velo vino (≥ 80 % de opacidad a la izquierda) | cabeceras sobre foto (portada «Destinos») | `.project-section .bg-half::after` |
| Ruta punteada con punto de destino | márgenes de secciones anchas, solo ≥ 1500 px, nunca detrás de texto | `::before/::after` en `gr-landing.css` §4 |

Nunca dos fondos de color seguidos sin un blanco entre medias. Texto sobre foto solo con velo.

## 3. Banco de fotos de ejemplo (`assets/img/ejemplo/*.webp`, 1200×800)

Fotos libres (Pexels, uso comercial sin atribución). Son **de ejemplo**: el cliente las cambia en
«Editar página» (campo de imagen de cada sección) o en Personalizar → Imágenes.

| Nombre | Qué muestra | Úsala para |
|---|---|---|
| `embalaje` | manos guardando un pedido en una caja | qué enviar, paquetes, regalos, compras |
| `cinta` | manos sellando una caja con cinta | embalaje, frágil |
| `caja` | caja cerrada con cinta | peso volumétrico, precio, equipaje |
| `almacen`, `almacen-pasillo`, `bodega` | operarios y estanterías | empresas, carga, plazos, seguimiento |
| `documentos` | firma de documentos en un escritorio | documentos, apostilla, aduana |
| `entrega`, `recojo` | entrega en puerta | entrega a domicilio, recojo, familia |
| `sobres` | sobres en mano | correspondencia |
| `ecuador` `colombia` `chile` `argentina` `bolivia` `estados-unidos` `espana` `venezuela` `cuba` | ciudad del país | cobertura de la ficha, carrusel de destinos |

Funciones (`inc/imagenes-ejemplo.php`):
- `grenvios_ej_img( 'nombre' )` → URL.
- `grenvios_ej_por_tema( $texto, $respaldo, $tema_primero )` → elige por palabras del título; reparte entre fotos afines para no repetir.
- `grenvios_ej_miniatura( $post )` → miniatura de guía (imagen destacada o ejemplo).
- Filtro `grenvios_campo_valor`: si un campo de imagen vale un **relleno de la plantilla** (`post-*`, `content-bg-*`, `team-*`, `delivery-man`…), se sirve la foto de ejemplo. Un relleno gris nunca debe llegar a la página.

Para añadir fotos: descarga de `images.pexels.com/photos/<id>/pexels-photo-<id>.jpeg?w=1400`
(los ID se buscan con WebSearch en pexels.com), **mira la foto** antes de usarla (sin marcas de
otras empresas, sin texto), recorta a 3:2, WebP calidad 76, añade el nombre a `grenvios_ej_banco()`.
No uses fotos de personas como si fueran clientes o testimonios.

## 4. Patrones de esta skill (CSS en `assets/css/gr-landing.css`)

| Patrón | Marcado | Dónde |
|---|---|---|
| **Contenido SEO + foto** | `section.gr-pseo--foto` → `.gr-pseo-grid` (texto 1.25fr / `figure.gr-pseo-foto`) | `grenvios_pseo_render_una()`; campo `<clave>_img` |
| **Tarjeta de guía con miniatura** | `a.grenvios-guide-img` al inicio de `article.grenvios-guide-card` | seo-clusters, blog-guias, blog-en-paginas |
| **Cobertura con foto** | `section.dest-cob` → foto + distintivo «N ciudades principales» / cabecera + chips + nota | `inc/destinos-ciudades.php`; campo `dst_ciudades_img` |
| **Listas → tarjetas numeradas** | `ul.srv-steps` hijo directo de `.dest-seo-sec > .container` | solo CSS |
| **Glosario en tarjetas** | `dl.dest-seo-glosario` en 2 columnas con borde vino | solo CSS |
| **Portada · rutas** (maqueta 2026-10-01) | `section.gr-home-rutas--v2`: cabecera + imagen → `div.gr-rt-grid` con dos paneles (cabecera vino: rutas con vía terrestre / solo aéreas) de filas `a.gr-rt-fila` (destino · iconos de vía · plazo en grande · píldora de entrega; la terrestre más rápida `is-destacada`) → pie rosado con enlace y botón | `grenvios_home_tabla_destinos()` en `inc/home-seo.php`; campos `home_rutas_img/g1/g2/dest/pie/link/btn` |
| **Portada · destinos uno por uno** (maqueta 2026-10-02) | `div.gr-hd-grid--v2`: primera ruta en tarjeta grande (2 filas, foto arriba) + tarjetas con bandera y título, foto del país, chip vino de plazo, píldoras de vía y entrega, ciudades y productos con icono, enlace; las dos últimas apaisadas | `grenvios_hd_tarjetas()` en `inc/home-destinos.php`; campos `home_hd_img_<pais>` |
| **Portada · lo que cambia de un destino a otro** (maqueta 2026-10-02) | `div.gr-he-grid`: tres tarjetas `gr-he-card` con foto, número en cuadrado rosa + título, texto, países en píldoras y dos etiquetas de vía/entrega (`ul.gr-he-tags`); pie rosado `gr-he-pie` con icono, texto, dos enlaces y botón | `grenvios_hd_elegir()` en `inc/home-destinos.php`; campos `home_elegir_intro`, `home_elegir_img1/2/3` |
| **Del blog / guías** (maqueta 2026-10-02) | `section.gr-bep--v2`: cabecera a la izquierda con antetítulo y raya; con ≥ 5 guías, `gr-bep--destacada`: primera en tarjeta grande (foto 4:2.65, distintivo «Guía destacada») + 2 medianas + 3 pequeñas; cada tarjeta: foto, etiqueta rosa, título, extracto, «Leer guía →»; pie rosado `gr-bep-pie` con «Por temas:» (separados por barras) y botón | `grenvios_bep_render()` en `inc/blog-en-paginas.php`; campos `bep_sub`, `bep_badge`, `bep_leer` |
| **«Continúa tu envío»** (maqueta 2026-10-02) | `section.grenvios-related--v2`: título vino grande + subtítulo; `div.gr-rel-acciones` con tres accesos (Cotizar en vino, Rastrear y WhatsApp con borde vino y teléfono); grupos en tarjetas rosadas con icono en círculo (`gr-rel-ic`, por nombre del grupo en `grenvios_related_icono()`) y enlaces con chevrón a ambos lados | `grenvios_render_related()` en `inc/seo-enlazado.php`; ajustes globales `rel_title/sub/btn_*` (panel «🧭 Continúa tu envío») |
| **/destinos/ · rutas país por país** (maqueta 2026-10-02) | `div.gr-dh-hero` (texto + `figure.gr-dh-foto`) → `div.gr-dbar` (buscador `gr-dsearch` + filtros `gr-dfilter`, JS que combina ambos, aviso `gr-dvacio`) → tarjetas `gr-dcard`: bandera, nombre, píldora de vía con iconos, chip («Ruta destacada» = terrestre más rápida; «Entrega rápida» = solo aérea con máx. ≤ 7 días), plazo, entrega, ciudades, «Ver condiciones →»; la primera con borde vino; la píldora baja a su fila si la tarjeta mide < 380 px (container query) | `inc/destinos-hub.php`, `inc/destinos.php` (`grenvios_repeater_extras`), tpl en `inc/repeaters.php`; campos `dh_img`, `dh_buscar` |
| **/destinos/ · comparativa** (maqueta 2026-10-02) | `section.gr-dcomp--v2`: tres cifras (`gr-dc-stats`: rutas terrestres, solo aéreas, plazo de la más rápida) → dos paneles `gr-dc-panel` con cabecera rosada (icono, título, texto) y tabla destino · plazo (número en negrita + «días hábiles» debajo) · entrega · impuesto (píldora vino «≈ 18 %» + «se paga en Lima» / píldora gris «Solo vía aérea»); filas `is-destacada` (rosa, terrestre más rápida) e `is-rapida` (verde, la más rápida en general) → nota rosada con botón «Cotizar» | `grenvios_dh_comparativa_html()` en `inc/destinos-hub.php` (todo del gestor); textos editables por dt (`gr-dcomp` en la lista de contenedores) |
| **/destinos/ · otros destinos + «¿No encuentras tu país?»** (maqueta 2026-10-02) | `div.gr-og-wrap`: buscador ancho + 3 paneles por región (América / Europa / Asia-Pacífico, por ISO de la bandera en `grenvios_dh_region_otro()`) con cabecera rosada (icono, nombre, «N destinos» que se actualiza al buscar) y filas bandera · país · plazo · «Cotizar →»; franja `.cta-section .cta-wrapper` vino con mapa y repartidor a la derecha, textos a la izquierda | `grenvios_dh_otros_agrupados()` en `inc/destinos-hub.php` (lee la lista `ul.gr-otros` ya pintada); campo `dh_otros_buscar` |
| **/destinos/ · cómo leer esta página** (maqueta 2026-10-02) | en `gr-pseo--destinos gr-pseo--n1`: subtítulo `gr-pseo-lead` + tarjeta `gr-d5` «Los 5 datos que deciden tu envío» (3 + 2, número, icono, título, descripción) antes del cuerpo; panel lateral `gr-dstats--v2` vino: antetítulo con icono, título, cifras con línea y píldoras, caja «¿Tu país no aparece?», botón blanco y enlace de WhatsApp | filtro `grenvios_html_final` (prio. 41) y `grenvios_pseo_aside` en `inc/destinos-hub.php`; campos `dh_lead`, `dh_d5_*`, `dh_ds_*` |
| **/destinos/ · qué mirar en la ficha** (maqueta 2026-10-02) | `section.gr-n2v2`, rehecha en el filtro `grenvios_html_final` (prio. 42) a partir del texto de la sección: título y subtítulo centrados → foto con tarjeta translúcida «Compara antes de cotizar» (`gr-n2-foto`) + cinco tarjetas numeradas 3 + 2 (`gr-n2-card`, la 01 destacada, icono en círculo) + caja final `gr-n2-caja`; el texto sale de la lista de la sección, así que editarla en el panel cambia las tarjetas | `inc/destinos-hub.php`; campos `dh_n2_comp_t`, `dh_n2_comp`, `dh_n2_caja_t` |
| **/destinos/ · lo que necesitas** (maqueta 2026-10-02) | `section.gr-n3v2`, rehecha en `grenvios_html_final` (prio. 43) desde el texto de la sección: cabecera a la izquierda → foto con marco rosado y tarjeta «Prepara todo antes de cotizar» + «Checklist para tu envío» con pastilla «6 requisitos» y seis tarjetas (`gr-n3-card`: icono, número, título, texto y visto; 03 y 05 destacadas) → franja vino `gr-n3-cta` con dos botones (los enlaces salen de la frase final de la sección) | `inc/destinos-hub.php`; campos `dh_n3_*` |
| **Pie de página compacto** (maqueta 2026-10-02) | «Del blog» con < 5 guías (`gr-bep--compacta`): botón «Ver todas las guías» a la derecha de la cabecera, tarjetas con insignia de lectura («N min», calculada con las palabras reales de la guía), etiqueta, título, extracto y «Leer guía →»; «Continúa tu envío» con ≤ 4 grupos (`grenvios-related--compact`): panel beige, columnas con icono y enlaces con flecha y tarjeta vino «¿Listo para enviar?» (sustituye a la franja `grenvios-cta`, que se oculta a continuación). Con más guías/grupos (portada) se mantienen las versiones anteriores | `inc/blog-en-paginas.php`, `inc/seo-enlazado.php`; ajustes `bep_todas`, `rel_card_t`, `rel_card` |
| **Servicio · intro comercial** (maqueta 2026-10-03, `/servicios/carga-internacional/`) | `section.gr-ci`, rehecha en `grenvios_html_final` (prio. 44/45) desde `gr-bq-intro` + `gr-ent`: antetítulo con raya, título grande, párrafo de la página, tres iconos con etiqueta, párrafo de apoyo, botones «Cotizar» (a /cotizar/) y «Hablar con un asesor» (WhatsApp) + foto con marco rosado y tarjeta «Soluciones a medida»; la entradilla pasa a la franja de cita con su última frase en píldora vino. Para otra página: añadirla a `grenvios_si_paginas()` | `inc/servicio-intro-v2.php`; campos `si_*` |
| **Servicio · modalidades + documentación** (maqueta 2026-10-03, carga internacional) | `section.gr-cm`, rehecha en `grenvios_html_final` (prio. 46) desde la sección `srv-two-grid` y sus repetidores: cabecera centrada → panel 1 (modalidades: cada elemento «Carga… desde/de N kg» abre tarjeta con píldora de peso y los siguientes se suman a su descripción; barra de resumen) + panel 2 (documentos con descripción, caja «¿Tienes dudas?» con correo y teléfono sacados del texto, botón a WhatsApp). Otra página: `grenvios_cm_paginas()` | `inc/servicio-intro-v2.php`; campos `cm_*` |
| **Servicio · cierre** (maqueta 2026-10-03, carga internacional) | en `grenvios_html_final` (prio. 47): `srv-trio` → «Solución logística integral» (antetítulo con rayas, última palabra del título en vino, párrafo con su enlace, accesos como tarjetas con flecha `gr-cc-link`); la tira `srv-features` toma el estilo de la franja v2 (CSS); el CTA del servicio → franja vino con repartidor y botones «Cotizar mi carga →» / «WhatsApp» (contorno). Otra página: `grenvios_cc_paginas()` | `inc/servicio-intro-v2.php`; campos `cc_*` |
| **Servicio · contenido con factores** (maqueta 2026-10-03, carga internacional) | `section.gr-pv`, rehecha en `grenvios_html_final` (prio. 48) desde la primera sección de contenido SEO (`gr-pseo--<slug> gr-pseo--n1`): antetítulo con raya, título con acento en vino, primer párrafo, tarjeta «Tres factores…» (campo `pv_factores`: Título \| descripción \| icono), «Así acompañamos tu carga» con los puntos de la lista de la sección, aviso con la frase del enlace del 2.º párrafo, **el 2.º párrafo completo plegado en `<details>`** (no se pierde texto), botón y foto con tarjeta. Otra página: `grenvios_pv_paginas()` | `inc/servicio-intro-v2.php`; campos `pv_*` |
| **Servicio · pasos con checklist** (maqueta 2026-10-03, carga internacional) | `section.gr-pd`, rehecha en `grenvios_html_final` (prio. 49) desde la segunda sección de contenido (`gr-pseo--<slug> gr-pseo--n2`): foto con tarjeta a la izquierda; a la derecha antetítulo, título con acento, introducción, cabecera «Checklist de despacho» + «N pasos» (N = pasos reales), pasos unidos por una línea (número, icono, título, descripción), caja con la frase final y su enlace, botón. Otra página: `grenvios_pd_paginas()` | `inc/servicio-intro-v2.php`; campos `pd_*` |
| **Portada · otros envíos** (maqueta 2026-10-01) | `ul.gr-mas-grid--v2`: tarjeta grande con foto y distintivo «Más consultado» (01, columna izquierda, 3 filas) · 3 tarjetas con icono en círculo, número y flecha · franja vino con foto (05, empresas) y botón de línea · 3 tarjetas más | `grenvios_hm_servicios_html()` en `inc/home-mas.php`; campos `home_mas_img1/img5/badge/btn1/btn5` |
| **Portada · precio** (maqueta 2026-10-01) | `section.gr-home-precio--v2`: texto + `figure.gr-hp-foto` con fórmula en distintivo vino → `figure.gr-ej--v2` (cabecera vino, dos cajas isométricas SVG con cotas, «Ahorras N kg» en el centro, pie) → `ul.gr-hp-pasos` (número + icono + texto, 4 columnas) → botones | `grenvios_home_precio()` en `inc/home-seo.php`, `grenvios_hm_ejemplo_html()` en `inc/home-mas.php`; campos `home_precio_img`, `home_precio_badge` |

## 5. Animación con criterio

- **Entrada**: `wow fade-in-bottom` en tarjetas (escalonado 100–110 ms, reinicia por fila); fotos con `fade-in-left/right` según el lado. Títulos con GSAP `text-anim`.
- **Una sola animación continua por sección**: el distintivo sobre la foto flota (`gr-flotar`, 5 s, 6 px). Nada más se mueve solo.
- **Hover**: tarjeta sube 4–5 px; icono se rellena de vino; foto hace zoom 1.05 en 0,8 s.
- **Prohibido**: parpadeos, carruseles automáticos nuevos, parallax, barras sticky, animar texto de lectura, retrasos > 600 ms.
- Todo lo nuevo se desactiva en `@media (prefers-reduced-motion: reduce)`.
- `data-wow-*` solo en contenedores, nunca en `h3`/`p` editables.

## 6. Lista de control SEO + conversión (por página)

Como redactor SEO: cada página ataca **una intención** y una keyword principal.

1. `<title>` ≤ 60 caracteres con la keyword al inicio y «| Grenvíos»; description 120–160 con beneficio + acción.
2. Un solo H1 con la keyword; H2 que sean **preguntas reales** («¿Cuánto cuesta enviar a…?»).
3. Primer párrafo: responde la duda en 2 frases (sirve de fragmento destacado).
4. ≥ 700 palabras de contenido útil en servicios; ≥ 1 tabla o lista por página.
5. Sin cifras inventadas: plazos e impuestos del gestor; precios → «te lo confirmamos al cotizar».
6. Enlaces internos: al pilar, a 2–3 páginas hermanas y a la ficha de destino; ancla descriptiva.
7. CTA de cotizar en el hero, a mitad y al final; WhatsApp visible.
8. Imágenes con `loading="lazy"`, WebP, `alt=""` si son decorativas o `alt` descriptivo con la keyword si aportan contenido.
9. FAQ al final (van al schema).

Comprobación rápida (título, descripción, H1, H2, palabras, CTA, imágenes sin alt, canonical, schema):
script `scripts/seo-rapido.py` de esta skill → `python seo-rapido.py [url…]`.

## 7. Verificar

```bash
cd .claude/skills/grenvios-diseno/scripts
node capturas.mjs <salida> <url1> <url2>      # máx. 2 URL por vez (poca memoria)
php auditar-sitio.php greenvios.localhost 40  # prueba rápida
php cobertura-panel.php greenvios.localhost pe <ruta>
```
Mira las capturas a 1366 y 390 px: foto presente en cada sección clave, ningún relleno gris
(«1000X650»), texto legible sobre fotos, tarjetas de guías con fotos distintas, móvil en una columna.
Si una captura sale con diseño de móvil a 1366 px o fotos en gris, repítela sola: es un fallo de la
captura tras un timeout, no de la página.
