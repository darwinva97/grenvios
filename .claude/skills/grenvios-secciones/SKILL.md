---
name: grenvios-secciones
description: Normativa de secciones de Grenvíos (qué tiene que tener cada sección de cada página en las diez rutas), auditoría que detecta secciones «solo texto», páginas basura y diferencias entre países, y el convertidor automático al diseño v3. Úsala SIEMPRE que una página o una ruta se vea «básica», al comparar el diseño de un país con otro, al añadir un tipo de sección nuevo y antes de dar por terminado un cambio de diseño que afecte a varias páginas.
---

# Grenvíos — normativa de secciones

Complementa a:
- `grenvios-landing`: orden de secciones por tipo de página, fondos y fotos;
- `grenvios-v2`: lenguaje visual (tokens, tarjetas, iconos);
- `grenvios-rutas`: bloques del país v3, formulario y blog por país;
- `grenvios-diseno`: contenido, panel y auditoría SEO.

Las páginas de referencia son las de Perú con diseño propio: portada, `/nosotros/`, `/servicios/` y las fichas (`/ec/envios-a-ecuador/`). Todo lo demás tiene que estar a su altura.

## 1. La norma: qué tiene que tener cada sección

Toda sección de contenido tiene **tres partes**:

1. **Cabecera:** antetítulo (versalitas 12–13 px, color de acento) y H2. El H2 es una pregunta o una promesa concreta.
2. **Cuerpo:** listas, tarjetas, tabla o párrafos cortos. Nunca un muro de párrafos.
3. **Un elemento visual, como mínimo**, que no sea un visto de lista:
   - foto (`grenvios_ej_por_tema()`, nunca un relleno),
   - icono grande en cuadrado o círculo,
   - número (contador CSS),
   - bandera (`grenvios_bandera_img()`),
   - tabla,
   - o tarjetas con forma propia.

**Una sección con solo título, párrafo y lista de vistos incumple la norma.** Un visto (`fa-check`), una ✕ o una flecha no cuentan como elemento visual.

**Exentas:**
- preguntas frecuentes (acordeón, siempre al final);
- entradilla `gr-ent`;
- notas de una línea al pie de un bloque (`gr-cobertura--nota`);
- franjas CTA y formulario de solicitud, que tienen su propio diseño.

### Tipos de sección y cómo deben verse

| Tipo (clase) | Quién lo pinta | Cómo se ve |
|---|---|---|
| Bloque del país (`gr-pais-sec`) | `grenvios_rd_bloques()` · `inc/rutas-diseno.php` | v3: cabecera con icono a la izquierda, cuerpo en tarjeta |
| Lista del motor (`gr-bq--lista`) | motor de páginas | v3 (`gr-sv`): la lista sin tarjeta de fondo |
| Glosario (`gr-bq--definiciones`, `dest-seo-glosario`) | motor de páginas / ficha | v3: términos en tarjetas con borde de acento |
| Checklist de la ficha (`dest-seo-sec` con vistos) | ficha | v3 |
| Dirección del país (`gr-pf--dir`) | `inc/paises-perfil.php` | v3 con icono de mapa |
| Intro de servicio (`srv-intro`) | plantillas de servicio | v3 |
| `srv-section` con solo cabecera, texto y lista | varios módulos | v3 |
| Tarjetas, pasos numerados, tablas, foto + texto | patrones de `grenvios-v2` / `grenvios-landing` | ya cumplen |

## 2. El convertidor automático (`inc/secciones-visuales.php`)

Filtro `grenvios_html_final` (prioridad 60, después de los rediseños de servicio 44–49) y `grenvios_pais_bloque_html` (20, después del v3 del país).

- **Qué convierte:**
  - los tipos de la tabla anterior,
  - **solo si la sección no tiene ya un elemento visual** (`grenvios_sv_tiene_visual()`).
- **Qué deja igual:**
  - FAQ, secciones con botones (`btn-group`) o formularios;
  - lo que ya es `gr-pv3`;
  - las páginas con «🎨 Diseño de esta página = Clásico» (`grenvios_estilo_pagina()`).
- **Qué hace:** envuelve la sección en la rejilla v3, que es la misma de los bloques del país (`gr-pv3-grid`, `gr-pv3-head`, `gr-pv3-body`, CSS en `gr-rutas.css`).
  - **No cambia ningún texto ni las clases originales.** Así el panel «Editar página» y los textos `dt` los siguen reconociendo.
  - Los párrafos de la cabecera pasan a abrir la tarjeta del cuerpo.
- **Icono y antetítulo:**
  - salen del título con `grenvios_sv_intencion()`:
    - prohibido → `fa-ban` «Restricciones»,
    - requisitos → `fa-clipboard-list` «Antes de despachar»,
    - glosario → `fa-book-open`,
    - proceso o tramos → `fa-route`,
    - correspondencia → `fa-envelope-open-text`,
    - dirección → `fa-map-location-dot`;
  - si el título no encaja, se usan el mapa general `grenvios_ui_icono()` y `grenvios_rd_etiqueta()`;
  - si la sección ya traía antetítulo (`sub-heading`), se conserva.
- **Restricciones:** la lista de lo que NO se envía cambia los vistos por ✕ (`gr-sv--no`). Un visto dice «sí».
- **Sin tarjeta de fondo:** si el cuerpo es solo una lista o un glosario, va sin tarjeta blanca (`gr-sv-body--libre`), para no meter tarjetas dentro de otra tarjeta.
- **Coste:** 1–3 ms por página, medido sobre el HTML real.

**Tipo de sección nuevo:**
- Si ya nace con un elemento visual, no hay que hacer nada.
- Si es de solo texto y no encaja en `grenvios_sv_es_candidata()`, añade allí su clase.
- Si tiene un tema que no está en el mapa, añade la palabra a `grenvios_sv_intencion()` (para el icono) o a `grenvios_rd_etiqueta()` (para el antetítulo).
- `grenvios_rd_etiqueta()` compara **desde el inicio de palabra**. Antes «via» saltaba dentro de «enviar», «envías» y «Bolivia»: 349 de 1029 títulos salían como «La ruta», y en `/bo/` todos los bloques. «aére-» y «terrestre» van al final del mapa para que «impuestos de un envío terrestre» siga siendo «Aduana».

## 3. Auditar

```bash
cd .claude/skills/grenvios-secciones/scripts
python3 auditar-secciones.py https://grenvios.com --sitemap --ruta ar --max 60   # una ruta
python3 auditar-secciones.py https://grenvios.com /nosotros/ /ar/aduana-de-argentina/
python3 auditar-secciones.py --dir carpeta_html                                  # HTML ya descargado
```

Lista:
- secciones de solo texto, por tipo y por página;
- H2 repetidos en una misma página (bloque del país pintado dos veces);
- copias `-2/-3/-4` y «Sample Page»;
- páginas sin HTTP 200.

Pide 4 páginas a la vez: no lo lances contra producción en bucle.

**Comparar países:**
- Usa `--json` y compara la lista de clases de sección de `/xx/envios-a-<país>/` entre rutas: deben coincidir.
- El 2026-10-06 coincidían las 8 fichas (24–25 secciones). Solo España era distinta, porque no tiene ruta (ver §5).

**Probar un cambio sin WordPress** (en este servidor no hay PHP ni base de datos):
1. Baja el HTML real con `curl`.
2. Pásalo por `grenvios_sv_filtrar()` con `~/.local/bin/php83` y stubs mínimos de WP (`add_filter`, `esc_*`, `wp_strip_all_tags`, `remove_accents` con `strtr`; `iconv` translitera mal aquí).
3. Sírvelo en local con el tema enlazado en `/wp-content/themes/grenvios/`, para que las fuentes de iconos carguen sin CORS.
4. Captúralo con `grenvios-diseno/scripts/capturas.mjs`, que ya encuentra `/usr/bin/chromium`.

## 4. Medición de referencia (2026-10-06, sitio en vivo, 202 páginas reales de pe + ec + portadas y fichas de cada ruta)

| | Antes | Con el convertidor |
|---|---|---|
| Secciones de solo texto | 156 en 120 páginas | 4 en 4 páginas |

Las 4 que quedan:
- «Tu ruta a X, y 30 destinos más» (`gr-operamos-section`, en Sobre nosotros de cada ruta): lista de países sin bandera. Siguiente paso: banderas con `grenvios_ui_bandera()`.
- Una `gr-cobertura`.

## 5. Problemas de datos detectados (no son de diseño; se arreglan en el panel de producción)

- **Copias `-2`, `-3`, `-4`** de las páginas de servicio y de `destinos/<país>`:
  - El 2026-10-06 eran 229 de las 431 URL descargadas.
  - Son indexables, tienen canonical a sí mismas y un title por defecto («X – grenvios»), y van casi vacías o con el bloque del país pintado dos veces.
  - **Causa:** `grenvios_ensure_page()` buscaba las hijas de `/servicios/` y `/destinos/` por el slug suelto, no las encontraba y creaba otra en cada subida de `GRENVIOS_PAGES_V` (2, 3 y 4) y en cada guardado del gestor de destinos. Corregido: ahora busca bajo el padre.
  - **Las copias que ya existen hay que borrarlas** (o redirigirlas con 301 a la original) desde el panel.
- **«Sample Page»** de WordPress publicada y copiada en cada ruta (`/ar/sample-page-a-argentina/`): borrarla.
- **España sin ruta:**
  - Polylang tiene 9 idiomas-ruta (pe, ec, co, cl, bo, ar, us, ve, cu); falta `es-ES`.
  - `/es/envios-a-espana/` redirige a `/envios-a-espana/`, que da 404.
  - La portada enlaza a `/destinos/espana/`, la ficha antigua. Esa es la «página de país distinta».
- **Sitemap de `/co/` incompleto:** 50 URL frente a 106 en las demás rutas, aunque las páginas responden 200.
- **Medicinas en una ruta:** «Destinos donde las aceptamos» dice en `/ar/` que Argentina no está entre ellos. La lista de países no se reduce al país de la ruta.
