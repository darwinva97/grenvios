---
name: grenvios-v2
description: Lenguaje visual v2 de Grenvíos, sacado de las maquetas que envía el cliente (ficha de destino, 2026-09-28). Úsala SIEMPRE que el cliente mande una maqueta o imagen de diseño («ahora este», «será así el diseño»), al rediseñar cualquier sección o página, y al extender el diseño nuevo al resto del sitio. Dice qué patrones existen, cómo convertir una sección al estilo de la maqueta, cómo se cambia el diseño por página y cómo verificar. Complementa a grenvios-ui (patrones base) y grenvios-diseno (contenido, panel, auditoría).
---

# Grenvíos — estilo v2 (maquetas del cliente)

El cliente está rediseñando la web **sección por sección con maquetas**. Cada maqueta se implementa
como un **patrón reutilizable** y se anota aquí, para que después se aplique a todas las páginas.
Empieza siempre por la ficha de destino (`/destinos/ecuador/`), que es la página de referencia.

## 1. El lenguaje visual (lo que tienen en común las maquetas)

| Elemento | Cómo es | Clase / token |
|---|---|---|
| Antetítulo | MAYÚSCULAS, 13 px, 700, `letter-spacing` 2,5 px, vino, **con raya**: a la izquierda si el bloque va alineado a la izquierda; a los dos lados («— Envíos a Ecuador —») si va centrado | `.sub-heading` dentro de `.srv-head`, `.gr-bq-head`, `.gr-pseo .section-heading` |
| Título | Grande y apretado: `clamp(28px, 3.1vw, 42–46px)`, 700, `letter-spacing -.03em`, `line-height 1.1–1.14` | h2 |
| Tarjeta | Blanca, radio 18, borde `#efe7e8`, sombra vino muy suave; al pasar: sube 5 px, borde `#e3c9cd` | `--v2-borde`, `--v2-sombra` |
| Icono | En **cuadrado rosa** (64–70 px, radio 16) o **círculo rosa** (56–92 px) `#f7eaec`, glifo vino; al pasar la tarjeta se rellena de vino con glifo blanco | `--v2-rosa` |
| Número | «01–04» por **contador CSS** (nunca texto: no debe ser un campo), 24 px, 700, rosa apagado `#d9a9b1`/`#c98a95` | `counter(…, decimal-leading-zero)` |
| Etiqueta | Píldora rosa con texto vino («Más elegido», «24/7») | `.gr-dsol-tag` |
| Destacada | La primera tarjeta con borde vino; o tarjetas 2 y 3 con fondo rosado `#fbf3f4` (ritmo de tablero) | `.is-destacada`, `nth-child(4n+2/3)` |
| Enlace de tarjeta | «Ver detalles →» subrayado vino | `.gr-dsol-ver` |
| Fondo de sección | Rosado suave `linear-gradient(135deg, #faf6f4, #f7eeee)`; la sección principal, con esquinas superiores de 40 px | `--v2-fondo` |
| Imagen | Grande, radio 22, con **distintivo vino** encima (plazo, «4 factores…») | `.gr-dsol-plazo`, `.dest-precio-badge` |
| Aviso | Rosado, borde izquierdo vino 4–5 px, icono ⓘ, separador fino entre párrafos, enlaces vino en negrita | `.dest-note`, `p.gr-nota` |
| Franja de cierre | Vino degradado, radio 18: botón blanco con flecha · separador vertical · icono · frase blanca | `.dest-precio-cta`, `.dest-seo-cta-linea` |
| Franja de datos | Una sola tarjeta blanca con 3–4 datos separados por líneas verticales: icono en círculo, ETIQUETA, valor en negrita | `.dest-facts`, `.srv-features-grid` |
| Chips de índice | Fila estática (nunca sticky), el primero relleno vino con ⌄ | `.gr-toc` |

Nunca colores en duro fuera de estos tokens; el vino es siempre `var(--primary-color, #5e2129)`.

## 2. Patrones ya hechos (reutilízalos antes de crear otro)

| Patrón (maqueta) | Dónde | Estructura |
|---|---|---|
| **Soluciones**: foto + distintivo de plazo a la izquierda, 2×2 tarjetas con icono, etiqueta y «Ver detalles» | `grenvios_dhero_soluciones()` · `inc/destinos-hero.php` · CSS `.gr-dsol--v2` | cabecera centrada con rayas → `div.gr-dsol-cuerpo` (foto 1fr / rejilla 1.05fr) |
| **Datos + qué enviar**: franja de 3 datos; 2×2 tarjetas numeradas a la izquierda; foto + aviso a la derecha | `functions.php` (ficha) · CSS `.dest-send-wrap` | `div.dest-facts` → `div.dest-send-wrap` (1.4fr / 1fr) |
| **Precio / factores**: cabecera + imagen con «4 factores» a la izquierda; 2×2 tarjetas numeradas a la derecha; franja vino | `inc/destinos-secciones.php` · CSS `.dest-precio` | `div.dest-precio-grid` (1fr / 1.6fr) + `div.dest-precio-cta` |
| **Nosotros · Nuestra historia + Misión y Visión** (maqueta 2026-10-08) | `template-parts/content-nosotros.html` · CSS `.gr-nh` en `gr-landing.css` | `div.gr-nh-top`: antetítulo con raya + título grande + mapa (`map-pattern.png` como máscara teñida de rosa, con rutas y puntos vino en SVG) / texto a la derecha → `div.gr-nh-cards`: dos tarjetas con icono vino en círculo y ruta punteada decorativa en la esquina. Campos `nos_historia_*` (nuevo: `nos_historia_sub`). Vale para las 9 rutas (misma plantilla). |
| **Capa global v2** (todas las páginas) | `inc/estilo-v2.php` + `assets/css/gr-v2.css` | solo CSS sobre `body.gr-v2`: cabeceras, fondos, tarjetas numeradas, avisos, franjas, tira de garantías, tablas |

Imágenes por defecto de estos patrones: `grenvios_ui_img()` → `repartidor`, `embalaje`, `precio`
(ilustración de caja en balanza), `avion`, `camion`, `documentos`. Cada imagen tiene su campo en el
panel (`dst_sol_img`, `dst_enviar_img`, `dst_precio_img`). **No hay fotos como las de las maquetas**
(manos embalando, caja en balanza): se usa una ilustración propia y se avisa al cliente de que puede
subir la foto desde el panel. Nunca un relleno de la plantilla.

## 3. Cómo convertir una sección a partir de una maqueta

1. **Localiza el marcado actual** (curl de la página + busca la clase; `grep` en `inc/` y `functions.php`).
2. **Conserva textos y campos**: los mismos `grenvios_field()` y las mismas claves; los textos de
   secciones automáticas (dt, `inc/destinos-textos-editables.php`) siguen siendo editables mientras sigan
   dentro de `h2/h3/h4/p/li/a` y sin atributos `data-*`. Si añades texto nuevo, que sea un campo o un `<p>`.
3. **Reestructura el marcado** a la rejilla de la maqueta; números con contador CSS; `wow fade-in-bottom`
   con `data-wow-delay` en los contenedores (tarjeta, figura), nunca en el `h3`/`p`.
4. **CSS al final de `assets/css/gr-bloques.css`** (patrones de sección) con banner `════` que cite la
   maqueta y la fecha; responsive a 1199/991/575 y `prefers-reduced-motion`. Reglas globales (que
   valen para muchas páginas): en `assets/css/gr-v2.css`, **siempre bajo `body.gr-v2`**.
5. **Panel**: la imagen nueva = campo `image` en el registro sintético del destino
   (`inc/page-editor.php`) o en `grenvios_text_registry` de la página. Antetítulos nuevos, igual.
6. **Si el ancla del índice depende del H2** (`grenvios_toc_items()`), comprueba que el `id="dp-…"` sigue saliendo.
7. `grenvios_cache_bump()` y **verifica** (sección 5).
8. **Anota el patrón en la tabla de la sección 2** de esta skill.

## 4. Cambiar el diseño por página

`body.gr-v2` se pone en todas las páginas salvo que la página tenga `grenvios_estilo_pagina = clasico`.
El cliente lo cambia en «Editar página» → acordeón **🎨 Diseño de esta página** (Nuevo / Clásico).
Por eso **toda regla global va bajo `body.gr-v2`**: así «Clásico» devuelve la página al diseño anterior.
Los patrones de sección reestructurados (soluciones, qué enviar, precio) no dependen del interruptor.
La portada (home) usa su propia plantilla y aún no tiene el acordeón.

## 5. Verificar (obligatorio)

```bash
cd .claude/skills/grenvios-diseno/scripts
node capturas.mjs <salida> http://greenvios.localhost/destinos/ecuador/   # 1366 y 390 px
php cobertura-panel.php greenvios.localhost pe destinos/ecuador            # ≥ 99 %; solo «Santo Domingo» (dato del gestor)
```
Recorta la captura en la zona de la sección y **compárala con la maqueta**: columnas, orden,
distintivos, números, franja vino, móvil (tarjetas 2×2 compactas o en una columna, botón a lo ancho).
Prueba al menos otra ficha (`estados-unidos`: solo aéreo) y una copia de ruta (`/ec/envios-a-ecuador/`).
Para la capa global, captura una página de servicio y una del motor (`/servicios/peso-volumetrico/`).
Con poca memoria, **máx. 2 URL por captura**, en primer plano.

> **Páginas completas:** la composición de cada tipo de página, los fondos, el banco de fotos de ejemplo y la animación están en la skill **grenvios-landing**.

## 6. Pendiente de la visión v2

- Resto de secciones de la ficha (plazos, embalaje, ciudades, errores, glosario, FAQ) siguen con la
  capa global: rediseñarlas cuando llegue su maqueta o aplicando los patrones de la sección 2.
- Portada: ya usa fotos de ejemplo (skill grenvios-landing); faltan las fotos reales del cliente y el
  acordeón «Diseño de esta página» en su panel.
