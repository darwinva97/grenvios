---
name: grenvios-ui
description: Patrones visuales, imágenes y animaciones de scroll para las secciones del tema Grenvíos. Úsala SIEMPRE que una página o sección nueva (o existente) se vea como «título + párrafo» sin diseño, al crear bloques pintados por PHP, al elegir imágenes o iconos para una sección y al añadir animaciones de entrada. Complementa a grenvios-diseno (contenido, panel, auditoría).
---

# Grenvíos — UI de secciones

> **Páginas que venden:** composición por tipo de página, fondos, fotos de ejemplo, animación y control SEO → skill **grenvios-landing**.
>
> **Diseño nuevo:** el cliente rediseña con maquetas. Antes de tocar el aspecto de una sección, carga la skill **grenvios-v2** (lenguaje visual, patrones hechos, capa global `body.gr-v2`, interruptor por página).

Una sección del tema **nunca** es solo «título centrado + párrafo + tarjetas de texto». Cada bloque
tiene jerarquía (antetítulo → título → apoyo), un elemento visual (imagen, icono, número o bandera)
y aparece al hacer scroll. Los patrones viven en `inc/ui-bloques.php` y `assets/css/gr-bloques.css`;
el motor de páginas (`inc/paginas-servicios-extra.php`) ya los usa.

## 1. Patrones (elige por el tipo de contenido)

| Contenido | Patrón | Función | Aspecto |
|---|---|---|---|
| Apertura de página: qué es y para quién | **Intro dividida** | `grenvios_ui_intro( $sub, $titulo, $texto, $img, $badge )` | Texto a la izquierda, imagen enmarcada a la derecha con marco degradado detrás y distintivo blanco |
| 2–4 ideas paralelas | **Tarjetas con icono** | `grenvios_ui_tarjetas( [[título, texto], …] )` | Icono en caja que gira y se colorea al pasar el ratón, barra de marca que se despliega arriba, elevación |
| Lista de comprobación | **Rejilla de vistos** | `grenvios_ui_checks( [texto, …] )` | Dos columnas de píldoras blancas con visto en círculo de acento |
| Proceso ordenado | **Línea de tiempo** | `grenvios_ui_pasos( [[título, texto], …] )` | Tarjetas con número grande «01», línea punteada que las une en escritorio |
| Definiciones | **Glosario** | `grenvios_ui_glosario( ['Término: definición', …] )` | Tarjetas con borde de acento; cada término tiene ancla (`#termino`) |
| Datos por país | **Tabla en tarjeta** | `grenvios_ui_tabla_con_banderas( $html )` | Sombra, cabecera gris, fila resaltada al pasar, bandera antes del país |
| Cabecera de cualquier bloque | **Section heading** | `grenvios_ui_cabecera( $sub, $titulo, $texto )` | `h3.sub-heading` + `h2.text-anim` (GSAP la anima) + subtítulo |

En el motor, cada tipo de bloque (`intro`, `panels`, `lista`, `pasos`, `definiciones`, `destinos`)
ya se pinta con su patrón, y todos los textos (antetítulo `_sub`, título `_t`, imagen `_img` de la
intro) son editables en el panel. Para páginas nuevas, usa el motor: el diseño sale solo.

**Aplicación global** (`inc/ui-global.php`, filtro `grenvios_html_final`): las páginas que NO usan el
motor (herramientas, contenido, SEO extra, partials, rutas de país) se transforman solas sobre su HTML:
`section.srv-intro` centrada → intro dividida con imagen (`ui_intro_img`, editable; mapa
`grenvios_ui_img_pagina_mapa`), `div.srv-head` → título animado, `div.srv-panel` → tarjeta con icono
(salvo si lleva formulario o calculadora), `ul.check-list` → rejilla de vistos, pasos y «Del blog» /
«Continúa tu envío» con aparición. Reglas del filtro: patrones EXACTOS (idempotente, corre en bloques y
en la página), expresiones «desenrolladas» y nunca patrones enormes (`.{0,1500}` no compila y devuelve
null → la página se queda sin contenido); la función devuelve el HTML original si algo falla.
Si una página nueva no se ve con el diseño, añade su slug al mapa de imágenes y usa esos patrones.

**Bloques globales** (en todas las páginas, CSS en gr-bloques.css): llamada final «¿Listo para enviar?»
= franja con degradado de marca y foto (`cta_img`, ajuste global editable; por defecto el repartidor);
entradilla `.gr-ent-p` = destacado con comillas y borde de acento; `p.gr-nota` = aviso con icono de
información; tablas antiguas (`gr-table`, `grenvios-rate-table`, `dest-seo-tabla`) en tarjeta y con
banderas (regla 7 de ui-global). No vuelvas a pintar estos bloques como texto suelto.

**Soluciones de la ficha de destino** (`grenvios_dhero_soluciones()` en `inc/destinos-hero.php`,
CSS `.gr-dsol--v2`): fondo rosa suave con esquinas superiores de 40 px, cabecera centrada, foto grande a la
izquierda (`dst_sol_img`, por defecto el repartidor) con distintivo vino del plazo (camión o avión +
«8–10 días hábiles», sacado del gestor; nunca a mano) y rejilla 2×2 de tarjetas: icono en círculo, etiqueta
en píldora (`dst_solN_b`), título, texto y «Ver detalles →» (`dst_sol_ver`); la primera con borde vino.
En móvil: foto arriba y tarjetas en una columna.

**Datos rápidos y «¿Qué puedes enviar?» de la ficha** (`functions.php`, CSS `.dest-send-wrap`): franja blanca
única con los 3 datos (icono en círculo, etiqueta y valor, separadores verticales); debajo, dos columnas:
antetítulo con raya (`dst_enviar_sub`), título, texto y tarjetas 2×2 numeradas por contador CSS (01–04, no
texto) a la izquierda; a la derecha, foto (`dst_enviar_img`, por defecto la ilustración de embalaje) pegada
al aviso «Información importante» (rosa, borde vino, separador entre párrafos).

**«¿Cuánto cuesta?» de la ficha** (`inc/destinos-secciones.php`, CSS `.dest-precio`): cabecera alineada a la
izquierda + imagen 4:3 (`dst_precio_img`; por defecto `ilustraciones/precio.svg`, caja en balanza) con distintivo
vino «4 factores definen el precio»; a la derecha, 4 tarjetas 2×2 con número por contador CSS e icono en
cuadrado rosa; cierre en franja vino con botón blanco, separador, camión y frase. Textos editables por el
mecanismo automático (dt), imagen por su campo.

Ritmo vertical: alterna `bg-grey` entre bloques (`'fondo' => true`), nunca dos grises seguidos.
No más de un bloque de tarjetas seguido de otro de tarjetas: intercala vistos, pasos o tabla.

## 2. Imágenes

**Nunca un relleno de la plantilla** (ver grenvios-diseno: `post-*`, `content-bg-*`, siluetas…).
`grenvios_ui_img( $nombre )` da las válidas:

| Nombre | Archivo | Úsala para |
|---|---|---|
| `avion` | `hero-home.jpg` (avión cargando en pista, camiones) | vía aérea, urgencia, Norteamérica, Europa, Caribe |
| `camion` | `destino-hero.jpg` (camión con la marca al atardecer) | vía terrestre, Sudamérica, carga |
| `repartidor` | `cta-repartidor-4.webp` (repartidor con polo vino y caja) | regalos, encomiendas, trato personal |
| `documentos` | `ilustraciones/documentos.svg` (sobre, documento sellado, ruta) | documentos, traducción, correspondencia, glosario |
| `embalaje` | `ilustraciones/embalaje.svg` (caja isométrica, cinta en H, etiqueta) | embalaje, muestras, electrónica, artesanía |
| `precio` | `ilustraciones/precio.svg` (caja en balanza, cinta métrica) | precio, peso volumétrico, tarifas |

La imagen de cada página del motor está en `grenvios_ui_img_pagina()`; el cliente puede cambiarla
en el panel (campo «Imagen» del bloque de introducción). Las ilustraciones nuevas: SVG en
`assets/img/ilustraciones/`, fondo `#f8f5f1`→`#efe6e0`, vino `#5e2129`/`#7a2a35`, acento `#d8465c`,
esquinas de 28 px, sombra suave vino, sin texto dentro (el texto va en el HTML).
Las imágenes decorativas llevan `alt=""`; las de contenido, `alt` descriptivo.

**Iconos**: `grenvios_ui_icono( $texto )` elige el icono de Font Awesome por el tema del título
(aduana, impuesto, batería, avión, carretera, documento, dirección, plazo, regalo, frágil…). Si
añades un tema nuevo, añade su regla ahí en vez de fijar el icono a mano.

## 3. Animaciones de scroll

Solo las del tema, nada de librerías nuevas:

- **Títulos**: `h2.text-anim` dentro de `.section-heading` → GSAP (main.js) lo anima al entrar.
  Atributos: `data-effect="fade-in-bottom"`, `data-delay`, `data-duration`.
- **Elementos**: `class="wow fade-in-bottom"` (o `fade-in-right` para imágenes a la derecha) con
  `data-wow-delay="Nms"` → WOW.js los revela al hacer scroll. Escalona con `grenvios_ui_delay( $i )`
  (100 ms + 110 ms por elemento; reinicia cada fila para que la última tarjeta no tarde un segundo).
- **Hover**: elevación de 4–6 px, sombra vino, icono que se colorea. Transiciones de 0,2–0,35 s.
- **Movimiento reducido**: `@media (prefers-reduced-motion: reduce)` quita animaciones y
  desplazamientos (ya está en gr-bloques.css; mantenlo si añades clases).
- **Nunca** `data-*` en un elemento con texto editable del mecanismo automático
  (inc/destinos-textos-editables.php): los salta por considerarlos interactivos. Pon el `wow` en el
  contenedor (tarjeta, `li`), no en el `h3`/`p`.

## 4. Verificar

1. `node ../grenvios-diseno/scripts/capturas.mjs <salida> <urls>` a 1366 y 390 px, y **mirar**
   las capturas: jerarquía clara, imagen presente, nada cortado, ritmo gris/blanco.
2. Las animaciones solo se ven en scroll real: las capturas recorren la página para dispararlas.
3. `php ../grenvios-diseno/scripts/cobertura-panel.php greenvios.localhost pe <url>`: los textos
   nuevos (antetítulos, imagen) deben seguir siendo editables.
4. Prueba A/B de tiempo si añades algo que procese el HTML de toda la página.
