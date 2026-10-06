---
name: grenvios-rutas
description: Diseño de las páginas de servicio, destino y blog en las diez rutas de Grenvíos (/, /ec/, /co/…): anatomía de cada tipo de página, bloques del país v3, formulario de solicitud al pie, cabecera del blog por país y cómo verificar que todo cambia por país. Úsala SIEMPRE que una subpágina de una ruta se vea sin estilo, al añadir una sección por país, al tocar el formulario de solicitud o la cabecera del blog, y antes de dar por terminado un cambio que afecte a todas las rutas.
---

# Grenvíos — páginas de ruta, servicio, destino y blog

Las rutas (`/ec/`, `/co/`, `/cl/`, `/bo/`, `/ar/`, `/us/`, `/es/`, `/ve/`, `/cu/`) son **envíos DESDE Lima HACIA ese país**. La principal (sin prefijo) es Perú. Cada página de una ruta es una copia de la de Perú más un **bloque del país** y contenido local.

**Regla:** todo lo que se diseñe para una página debe verse bien en las diez rutas y cambiar solo con los datos del país. No escribas nunca un texto para un país concreto dentro de una plantilla.

Complementa a:
- `grenvios-diseno`: contenido, panel, URL, SEO y auditoría;
- `grenvios-v2` / `grenvios-ui`: patrones visuales base;
- `grenvios-landing`: composición y fotos.

## 1. Anatomía por tipo de página

| Tipo | Ejemplo | De arriba abajo |
|---|---|---|
| Servicio / subpágina de ruta | `/co/envio-de-documentos-a-colombia/` | Hero (camión o foto) → entradilla → secciones del servicio (plantilla) → **bloques del país v3** → «Lo que cambia cuando envías a X» (`gr-ppl`) → perfil (dirección, reglas) → guías del cluster → «Del blog» (guías del país) → «Continúa tu envío» → preguntas frecuentes → **formulario de solicitud** |
| Ficha de destino | `/co/envios-a-colombia/` | Hero → «En esta página» (chips estáticos, nunca sticky) → secciones `dest-*` → … → formulario |
| Listado del blog | `/co/guias-para-enviar-a-colombia/` | **Cabecera del blog por país** (`gr-bh`) → entradilla → filtros por tema → tarjetas → bloque del país |
| Entrada | `/co/enviar-ropa-y-calzado-a-colombia/` | Ver `grenvios-diseno` § «Detalle de entrada del blog» (`single.php`, `gr-blog.css`) |
| Portada, cotizar, contacto, rastreo | — | Tienen su propio formulario: **sin** formulario de solicitud |
| Portada de un país (`/bo/`) | `/bo/` | **Igual que la de Perú** (las 5 secciones de la maqueta: otros envíos, precio, plazos, destinos uno por uno, lo que cambia) y **después** los bloques del país. Las FAQ generales de la portada de Perú no se añaden en los países (`grenvios_home_es_principal()`): se repetirían en las nueve. |

## 2. Piezas (todas en `inc/rutas-diseno.php` + `assets/css/gr-rutas.css`)

### 2.1 Bloques del país v3 (`gr-pv3`)
- El bloque se guarda en el contenido (`<!-- grenvios:pais -->`) y lo pinta `grenvios_pais_render()`.
- Antes de pintarse pasa por el filtro `grenvios_pais_bloque_html` y `grenvios_rd_bloques()` lo transforma:
  - **Sección normal:**
    - cabecera a la izquierda: icono (`grenvios_ui_icono`), antetítulo por tema («Plazos · Colombia», «Aduana · Colombia»…, con `grenvios_rd_etiqueta`), H2 y barra de color;
    - cuerpo en tarjeta a la derecha: listas `srv-list` con vistos y tablas con cabecera vino y filas alternas.
  - **Un solo párrafo** → `gr-pv3--nota` (tarjeta con borde de acento).
  - **«¿Envías a X?»** (cuerpo con `btn-group` y poco texto) → banner vino `gr-pv3-cta` con botón blanco.
  - **Bloque con `gr-faq-item`** → se deja igual; la FAQ duplicada se quita en otro sitio.
- Los textos no cambian, solo se envuelven: el panel («Editar página») los sigue reconociendo.
- Si añades una sección de país nueva (`grenvios_pais_seccion_*`), usa `grenvios_pais_sec()`: el diseño v3 le llega solo.
- Si su título trata un tema nuevo, añade la palabra a `grenvios_rd_etiqueta()`.
- **Trampa de CSS:** `style.css` da `margin:0 auto` a `.gr-pais-body`. Dentro de una rejilla eso lo ajusta al contenido y una tabla ensancha la página en móvil. Por eso `.gr-pv3 .gr-pv3-body{margin:0;width:100%}` y las columnas de la rejilla llevan `minmax(0,1fr)`.

### 2.2 Formulario de solicitud (`gr-lf`, ancla `#solicitud`)
- **Dónde:** al pie de cada página (`do_action( 'grenvios_pagina_cierre', $slug )` en `page.php`, tras las FAQ).
  - Sustituye a la llamada final «¿Listo para enviar?» (`grenvios_cta_enabled` → false) para no repetir.
  - Excluidas: portada, `cotizar`, `contacto` y `rastreo-de-envios` (filtro `grenvios_form_excluir`).
  - **Tampoco sale en las fichas de destino**: ya tienen su cotizador (`dest-cotiza`, en `inc/destinos-ciudades.php`). Ese cotizador llama a `grenvios_rd_cotizador_propio( true )`, y entonces no se pintan ni el formulario del pie ni la franja CTA. Antes las fichas tenían dos formularios. **Regla: un solo formulario de cotización por página.** Si una sección nueva trae su propio formulario, que también llame a `grenvios_rd_cotizador_propio( true )`.
  - La portada tampoco tiene ya la sección «Pide tu cotización al instante» (`quick-quote`): repetía el cotizador del hero.
  - Los desplegables del cotizador (`.gr-hq`) tienen su propio desplegable en `hero-quote.js` y se quedan fuera de nice-select. Con los dos encima, el icono se descolocaba en las pantallas táctiles.
- **Se adapta solo a la página:**
  - título «Cotiza tu envío a <país>»;
  - país preseleccionado;
  - tipo de envío según el slug (`grenvios_rd_tipo`: documentos, carga, equipaje, paquete);
  - WhatsApp y teléfono de la sede (`{{contacto_wa}}` para el enlace, `{{contacto_telefono}}` para mostrar).
- **Envío:** `admin-post.php?action=grenvios_solicitud` (con `fetch` y respuesta JSON; sin JavaScript, redirige con `?solicitud=ok#solicitud`).
  - Va a `wp_mail` → `{{contacto_email}}` de la sede (respaldo: filtro `grenvios_form_email`), con Reply-To del visitante.
  - **Cada solicitud se guarda** en el tipo privado `gr_solicitud` (menú «Solicitudes» del panel), con la marca `_gr_correo_enviado`: si el correo falla, no se pierde nada.
- **Antispam:** campo trampa `web` y un mínimo de 3 s entre cargar y enviar. Al bot se le responde «gracias» sin guardar.
- **Accesibilidad:** cada campo con `<label for>`, mensaje en `role="status"` y `aria-live`.
- **Desplegables nativos (`gr-select-nativo`):**
  - `main.js` aplica nice-select a todos los `<select>` salvo a estos.
  - nice-select medía 50 px con `line-height:40px`, así que el texto caía y no cuadraba con los campos de 48 px. Además no se actualizaba tras `form.reset()`.
  - El nativo lleva `appearance:none` y una flecha vino en SVG.
  - Todos los campos tienen el mismo borde al pasar el ratón y el mismo foco.
  - El barrido del botón es vino oscuro (`--primary-dark`), no el negro de `.default-btn`.
- No inventes promesas: «Respuesta en menos de 24 horas hábiles» ya figura en el formulario de la portada.

### 2.3 Cabecera del blog por país (`gr-bh`)
- `index.php` la usa en el listado (`is_home()`, página 1) de cada ruta. Los archivos y la búsqueda mantienen el banner normal.
- **Cambia sola con la ruta:**
  - foto de fondo del país (`grenvios_ej_img( <slug> )`, la de Perú en la principal);
  - antetítulo «Blog · Envíos a X»;
  - H1 editable (`blog_title`; por defecto «Guías para enviar a <span>X</span>»);
  - entradilla, buscador que busca en esa ruta y temas (categorías de la ruta);
  - cifras reales: guías propias del país (`grenvios_solo_pais`) y número de temas;
  - **guía más reciente** del país como tarjeta destacada.
- **Tarjetas del listado:**
  - fecha en español y «Equipo Grenvíos»;
  - imagen variada con `grenvios_rd_card_img()`: las guías de ciudad alternan la foto del país y fotos afines para no repetir la misma imagen seguida.

## 3. Reglas de diseño (resumen)

- **Paleta:** vino `--primary-color` #5e2129, acento `--gr-acc` #d8465c, fondos #f8f5f1 / #efe6e0. Nunca un color fijo sin `var()`.
- **Bloques:**
  - tarjeta blanca, radio 16–22 px, borde #efe6e0 y sombra vino suave;
  - antetítulo en versalitas de 12 px con el color de acento;
  - H2 con letter-spacing −0,025em.
- **Ritmo:** alterna blanco y `bg-grey`; no pongas dos banners vino seguidos.
- **Animación:** `wow fade-in-bottom` en el contenedor (nunca en el texto editable), escalonada 100/200 ms y con `prefers-reduced-motion`.
- **Imágenes:**
  - `:where(img){height:auto}` + width/height (los añade `inc/rendimiento-a11y.php`);
  - nunca un relleno de la plantilla;
  - la imagen principal (LCP) sin lazy ni animación.
- **Banderas:** `grenvios_bandera_img()` (SVG); nunca los PNG de Polylang.
- **Sticky:** nada. «En esta página» y la columna lateral son estáticos.

## 4. Verificar (obligatorio)

```bash
cd .claude/skills/grenvios-diseno/scripts
# 1) Capturas de escritorio y móvil, de una ruta con 2 vías y otra aérea (y MIRARLAS):
node capturas.mjs <salida> http://greenvios.localhost/co/envio-de-documentos-a-colombia/ http://greenvios.localhost/us/envio-de-paquetes-a-estados-unidos/
# 2) SEO de <head>, accesibilidad y rendimiento:
PYTHONIOENCODING=utf-8 MSYS_NO_PATHCONV=1 python a11y-perf.py co/envio-de-documentos-a-colombia/ co/guias-para-enviar-a-colombia/
```

3. **Recorrido de todas las rutas:**
   - Lista las páginas de primer nivel de pe, ec, co y us (sin espejos).
   - Pide cada una y comprueba HTTP 200, que no haya «Fatal error/Warning» y que aparezca `class="gr-lf "`, salvo en las excluidas.
   - Resultado de referencia (2026-10-03): 105 URL, 0 errores; solo faltaba en portada, cotizar, contacto, rastreo y blog, que es lo correcto.
4. **Formulario:**
   - Un POST válido debe redirigir a `?solicitud=ok` y crear un `gr_solicitud`.
   - Un POST con el campo `web` relleno no debe crear nada.
   - **Borra la solicitud de prueba.**
5. **Si «se sale del ancho» en móvil:** mide la cadena de anchos con CDP, del elemento hacia arriba. Casi siempre es un `margin:auto` o un `1fr` sin `minmax(0,…)` en una rejilla.

## 5. Cabecera y menú (2026-10-06)

- **Menú:**
  - Inicio · Servicios · **Envíos Internacionales** (→ `/destinos/`, con el desplegable de países) · Contacto · Rastrea tu Pedido.
  - «Nosotros» va en el pie, al inicio de «Servicios y enlaces».
  - Plantillas: `template-parts/header.html` y `footer.html`. En cada ruta, los enlaces se adaptan solos (`/us/sobre-nosotros-envios-a-estados-unidos/`).
- **Cabecera idéntica en todos los países** (el cliente lo exige):
  - El selector muestra siempre bandera y nombre, como en Perú. «Estados Unidos» sale como «EE. UU.» en la cabecera (en el desplegable va completo).
  - Por debajo de 1250 px se oculta el nombre en todos los países a la vez.
  - Separación del menú: 22 px; 16 px por debajo de 1250 px; 12 px y letra de 14 px entre 993 y 1100 px.
  - Las reglas antiguas de 20 y 14 px nunca se aplicaban (las pisaba `.header-menu-wrap ul li { margin-right: 30px }`).
  - Medido: cabe de 993 a 1600 px con todos los nombres; el más justo es «Venezuela», a 19 px de la línea.
  - **No ocultes el nombre según el país:** se probó (`grAjustarCabecera`) y la cabecera dejaba de ser igual en todas las rutas.
- **Panel de países:**
  - Una columna justo debajo del selector, alineada con su borde izquierdo (`left: -14px`, 232 px). Por debajo de 1250 px (solo bandera, en todos los países) se abre hacia la izquierda para no salirse de la pantalla.
  - El cliente rechazó las dos columnas: se abrían sobre el menú.
  - Alto máximo `calc(100vh - 150px)`: si la ventana es baja, se desplaza por dentro en vez de recortarse.
  - Siempre opaco y con `z-index: 1000`. El `transition: all .25s … .1s` del tema animaba la opacidad y el z-index al cerrar, y se veía gris y translúcido.
  - Se abre también con el teclado (`:focus-within`). El país actual va marcado con un ✓.
  - El tema fuerza `.header-menu-wrap li ul { display: block !important }`; por eso el panel lleva `display: grid !important`.
  - `grenvios_i18n_switcher_html()` quita los países repetidos: la ruta principal y la ruta `pe` de Polylang salían las dos como «Perú».
- **`/destinos/` daba 404:**
  - Una categoría vacía `destinos` (y `destinos-<país>` en cada ruta) tenía regla de URL, y esas reglas van antes que las de páginas.
  - `grenvios_upn_reglas_terminos()` ya no crea reglas para slugs que son de una página.
  - Las reglas se regeneran al entrar un administrador en el panel tras subir `GRENVIOS_UPN_V`.
- **Versiones de CSS y JS:** se versionan por la fecha del fichero (`filemtime`), no con `LOGISKO_VER`. Con la constante fija, un `main.js` cambiado seguía saliendo de la caché del navegador.

## 6. Una sola FAQ por página (2026-10-06)

- Las fichas pintaban dos bloques de FAQ: «Preguntas frecuentes sobre X» (5 preguntas generadas con los datos del gestor) y el acordeón final (`grenvios_render_page_faqs`).
- Ahora las 5 preguntas de la ficha van al **principio del acordeón final**, sin repetir: `$GLOBALS['grenvios_dsec_faq_visibles']` en `inc/destinos-secciones.php`.
- El esquema FAQPage sigue siendo uno solo y coincide con lo visible.
- El chip «Preguntas» del índice apunta a `#preguntas-frecuentes`, el `id` del acordeón.
- **Una URL, una entrada:**
  - La ruta principal y la ruta `pe` de Polylang pueden tener dos entradas con el mismo slug.
  - La URL encontraba las dos y `single.php` pintaba la página dos veces: dos H1 y dos FAQ, y la primera era la del otro idioma.
  - Ahora el filtro `the_posts` (`inc/urls-primer-nivel.php` §5) se queda con la entrada cuyo enlace coincide con la dirección pedida.
  - Caso real: `como-enviar-tus-compras-hechas-en-peru` (2205 y 2234).
- **Verificar:** baja el sitio (páginas + entradas del sitemap) y pasa `grenvios-secciones/scripts/duplicados.py <carpeta> <salida.json>`. Cuenta formularios de cotización y contacto, bloques de FAQ, preguntas repetidas y esquemas FAQPage por página.
  - Medido el 2026-10-06 sobre 831 páginas y entradas de las 9 rutas: antes, 18 páginas con dos formularios y 10 con dos FAQ; con los cambios, 0 y 0. Ninguna página con dos FAQPage.
