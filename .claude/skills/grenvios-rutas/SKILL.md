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
