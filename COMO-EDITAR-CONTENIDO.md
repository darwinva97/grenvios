# Cómo editar los textos de cada página (Grenvíos)

## Seguimiento de envíos (Rastreo) — cómo usarlo (v2.19.0)
En el menú de WordPress aparece **“Envíos (Rastreo)”**. Para cada paquete:
1. **Añadir envío** → escribe el **Número de guía** (el código que le darás al cliente; te conviene
   poner ese mismo código como **título** para ubicarlo fácil).
2. Elige el **Estado actual** entre los 7: *Etiqueta creada · Salió del centro de recolección ·
   En tránsito · Proceso de aduana · Retenido · Listo para la entrega · Entregado*.
3. **Publica**. A medida que el paquete avanza, entra al envío, cambia el estado y **Actualiza**
   (la fecha de actualización se guarda sola).

El cliente entra a **Rastrea tu Envío**, escribe su número de guía y ve el estado en una **barra
animada de 7 pasos**. Si el número no existe, le aparece un aviso para escribir por WhatsApp.
El buscador del Inicio lleva a la página de rastreo con el resultado.


A partir de la versión 2.5.0 del tema, **el contenido de cada página es editable desde
WordPress**, conservando el diseño y las animaciones.

## Cómo funciona
Al activar el tema, el contenido de cada página se vuelca a su **editor de WordPress**
dentro de un bloque **“HTML personalizado”**. Cuando entras a *Páginas → (la página) → Editar*,
verás ese bloque con el texto y la maqueta; editas el texto y guardas.

> El tema **no pisa** lo que edites: una vez que una página tiene contenido, las
> reactivaciones del tema no lo sobrescriben.

## Pasos
1. WordPress → **Páginas** → abre la página (Inicio, Nosotros, Servicios, etc.).
2. Verás un bloque **HTML personalizado** con el contenido. Haz clic dentro.
3. Cambia solo el **texto** entre las etiquetas. Ejemplo:
   `<h2>Envío de Paquetes Internacional</h2>` → cambia *Envío de Paquetes Internacional*.
4. **No borres** las etiquetas `<...>` ni las clases (`class="..."`): son las que dan el diseño.
5. **Actualizar**. Usa la **vista previa** antes de publicar.

## Consejos
- Para volver al diseño original de una página: **vacía** su contenido y **reactiva el tema**
  (Apariencia → Temas); se vuelve a volcar el HTML por defecto.

## Imágenes, fondos y datos de contacto → **Apariencia → Personalizar** (desde la v2.6.0)
Ya **no hace falta** editar URLs a mano dentro del HTML. Abre **Apariencia → Personalizar →
“Grenvíos — Imágenes y contenido”** y verás un **panel lateral** con vista previa en vivo:

- **Logos e identidad** — logo del sitio y favicon.
- **Portada / Hero** — fondo e ilustraciones de la sección principal.
- **Fondos de secciones** — imágenes de fondo de todo el sitio (se cambian de una vez en
  todas las páginas donde aparecen; incluso las que viven en el CSS).
- **Imágenes de contenido** — equipo, testimonios, respaldos, banderas, banners.
- **Patrones e ilustraciones (avanzado)** — elementos decorativos.
- **Datos de contacto** — teléfono, WhatsApp, correo, dirección y horario. Cambiarlos aquí
  actualiza el pie de página, el botón de WhatsApp y los datos para Google (Schema) a la vez.

Cómo cambiar una imagen: clic en el campo → **Cambiar imagen** → sube o elige de *Medios* →
**Publicar**. Si dejas un campo vacío, se usa la imagen por defecto del tema. Cada campo
muestra debajo el nombre del archivo original como referencia (ver también `ASSETS-IMAGENES.md`).

## Textos de páginas → **Editor de Página en línea** (v2.8.0)
Los textos se editan **sobre el propio sitio**, sin entrar al escritorio:

1. Inicia sesión como administrador y abre la página que quieras editar (ej. el Inicio).
2. Abajo a la derecha verás el botón **“Editar página”**. Haz clic: se abre un **panel lateral**.
3. El panel lista las **secciones** de esa página (Hero, Rastreo, Nosotros, Servicios…).
   Al abrir una sección, esa parte de la página se **resalta** para ubicarte.
4. Edita cada campo (título, párrafo, ítem, botón) y pulsa **Guardar**. La página se recarga
   con los cambios.

- Cada texto se guarda **en su propia página** (post-meta), no afecta a las demás.
- Los campos con la nota *“Puedes usar `<br>` y `<span>…</span>`”* admiten un salto de línea
  (`<br>`) o resaltar una palabra (`<span class="hl">palabra</span>`).
- Si dejas un campo vacío, vuelve a su texto por defecto.
- Disponible en **todas las páginas** (las 350: Perú, las 24 de cada ruta de país, las
  fichas de Destinos y Artículos por país). Desde la v2.20 también en Aduanas e impuestos,
  Envío de alimentos, Seguro de envíos, Envíos desde provincias y Artículos por país.
- Solo se guardan los campos que **cambiaste**. Pulsar Guardar sin tocar nada no fija los
  textos por defecto en la página (así la cabecera de cada país sigue actualizándose sola).
- Las **listas con vistos** de esas páginas se editan como texto: **una línea por punto**.
- En la **portada de una ruta** (`/ec/envios-a-ecuador/`) el panel muestra los campos de la
  plantilla de destino, que es la que se ve, no los de la home de Perú.
- En las páginas de una ruta y en las fichas de destino hay una sección más:
  **«Datos de &lt;país&gt; (todas sus páginas)»**. Son los seis campos de
  *Destinos → Contenido por país*; al guardar se actualizan las 24 páginas de esa ruta.
- Además del texto, cada página tiene campos de **imagen** (fotos y banners de contenido):
  haz clic en **Seleccionar/Cambiar**, elige de *Medios* y **Guardar**.

### Secciones con lista: añadir / quitar / reordenar (repeaters) — v2.10.0
Algunas secciones son **listas de elementos** que puedes ampliar o recortar. En el panel
aparecen como secciones propias, por ejemplo en el **Inicio**:
- **🗂️ Tarjetas de Servicios**
- **⭐ Testimonios**

Dentro de cada una verás los elementos numerados (Servicio 1, Servicio 2…). En cada uno:
- Edita sus campos (título, texto, imagen, enlace…).
- **↑ / ↓** para cambiar el orden.
- **🗑 (papelera)** para quitar ese elemento.
- **“Agregar …”** (abajo) para añadir uno nuevo.

Pulsa **Guardar**: la página se recarga con la lista actualizada. Si quitas todos los
elementos, esa sección queda vacía. Cada lista se guarda **en su propia página**.

> Los **fondos de sección** (los que viven en el diseño/CSS) y el ícono decorativo del camión
> se cambian en **Apariencia → Personalizar → Grenvíos — Imágenes y contenido**.
> En **Destinos** y **Preguntas Frecuentes** el panel edita banner, textos principales,
> «Por qué elegirnos», CTA y preguntas; las secciones SEO calculadas (plazos, comparativas,
> glosario…) salen de los datos del destino y de «Datos de &lt;país&gt;».

## Páginas data-driven (se editan distinto)
Estas **no** usan el editor de bloques porque se generan desde los datos del tema:
- **Destinos por país** (Ecuador, Colombia, Chile, …): textos, tiempos e impuestos viven en la
  función `grenvios_destinos()` de `functions.php`.
- **Preguntas Frecuentes**: las 10 preguntas viven en `grenvios_faqs()` de `functions.php`.

Si quieres que estas también sean editables desde el editor de WordPress, se puede convertir
(pídelo y se hace).

---

## Contenido por país → **Destinos → Contenido por país**

Cuando un país tiene su propia ruta (`/cu/`, `/ar/`…), sus 24 páginas nacen con
secciones escritas para ese destino. La mayoría se construyen solas con la ficha
del destino —plazo, modalidades, forma de entrega y el impuesto—, pero hay seis
datos que el tema **no puede saber** y que se rellenan en esta pantalla:

| Campo | Qué poner |
|---|---|
| Ciudades con cobertura | Separadas por comas |
| Lo que más se envía | «medicinas, ropa, repuestos» |
| No se puede enviar | Solo lo que sepas cierto **de ese país** |
| Documentación exigida | «copia del documento del destinatario» |
| Embalaje recomendado | Un párrafo con lo propio de esa ruta |
| Contexto de la ruta | Quién envía a ese país y por qué |

**Un campo vacío no inventa texto: no pinta la sección.** Es deliberado. Publicar
«la aduana de Cuba permite 10 kg» porque suena verosímil sería darle a un cliente
una norma que le pueden rechazar en el mostrador.

La tabla de arriba de esa pantalla dice, país por país, qué campos faltan. Al
guardar puedes marcar **«Rehacer el bloque»**: reescribe solo las secciones
automáticas de ese país; el texto que hayas escrito tú por fuera no se toca.

### Y si quieres cambiar el texto de una página concreta

Se edita como cualquier otra, desde **Páginas**. El bloque de país está dentro del
contenido, así que puedes reescribirlo, ampliarlo o borrarlo. En cuanto tocas una
página de un país, esa edición manda sobre lo automático.

> **Cómo ver qué página es de qué país:** en **Páginas** hay una columna **País** y
> un desplegable para filtrar. Es más claro que el selector de banderas de
> Polylang, que dice «idioma» donde en realidad hay un destino.
