# Grenvíos — Plan de SEO multiidioma para captar clientes

Complemento estratégico de `MULTIIDIOMA-SEO.md` (que explica **cómo funciona** el
sistema). Esto explica **qué publicar en cada idioma y por qué**, para que las
traducciones traigan clientes y no solo páginas.

---

> **Antes de seguir: esto no va de países, va de idiomas.** Posicionar «envíos a
> Cuba» para el público peruano no se hace traduciendo nada: se hace con una ruta
> de país, y eso está en `PAGINAS-POR-PAIS.md`. Este plan es para cuando el que
> busca **no lee español**. Confundir las dos cosas lleva a traducir al inglés
> páginas que nadie va a buscar en inglés.

## 1. La pregunta que decide todo: ¿quién busca a Grenvíos en otro idioma?

Grenvíos envía **desde Lima hacia el mundo**. Eso significa que el cliente casi
siempre está **físicamente en Perú** o paga desde el extranjero un envío que sale
de Perú. Así que la pregunta no es "¿en qué idioma hablan los países destino?"
(casi todos hablan español), sino **"¿quién, teniendo que enviar desde Perú, no
busca en español?"**. Hay cuatro perfiles reales:

| Perfil | Dónde está | Qué busca (ejemplos) | Ticket |
|---|---|---|---|
| **A · Extranjero residente o de paso en Lima** | Lima | *ship luggage from Peru*, *send package from Lima to USA*, *shipping company Lima English* | Medio, recurrente |
| **B · Importador / empresa extranjera que compra en Perú** | EE.UU., Europa, Asia | *freight forwarder Peru*, *shipping from Peru to USA*, *export agent Lima*, *air cargo Peru* | **Alto (B2B)** |
| **C · Familia migrante de 2ª generación** | EE.UU., España, Italia | *send package to Peru*, *how to ship documents to Peru* | Bajo, alto volumen |
| **D · Brasileño / lusófono con negocio frontera o Amazonía** | Brasil, Perú | *envio de encomendas Peru Brasil*, *frete internacional Peru* | Medio |

**Conclusión práctica:** el idioma que más puede rendir es el **inglés**, y no por
"internacionalizar", sino porque concentra los perfiles A y B — y B es el de
mayor ticket (carga internacional para empresas).

> Ojo con el perfil C: buscan *enviar HACIA Perú*, no desde Perú. Si Grenvíos no
> ofrece ese sentido del envío, esas visitas **no convierten** y solo ensucian
> las métricas. Antes de crear contenido para ellos hay que decidir si se atiende
> ese flujo (aunque sea como intermediario), porque es un mercado enorme.

## 2. Orden de idiomas recomendado

| Prioridad | Idioma | Justificación | Alcance sugerido |
|---|---|---|---|
| **1** | **Inglés (`en`)** | Perfiles A + B. Es el único idioma que puede traer B2B de carga. | Sitio completo + 3–4 páginas propias que NO existen en español |
| **2** | **Portugués (`pt-BR`)** | Brasil es el único mercado grande no hispano de la región y hay comercio real Perú–Brasil. | Home + servicios + destino Brasil |
| 3 | Italiano (`it`) | Solo si "apostilla y traducción" para trámites de ciudadanía italiana pesa en el negocio. **Validar antes**: ese público suele buscar en español. | Solo la página de Apostilla y Traducción |
| 4 | Chino / Japonés | Aparecen en la tabla de destinos, pero el tráfico de búsqueda hacia una web peruana es marginal y el coste de mantenimiento alto. | No, por ahora |
| ❌ | Variantes de español (`es-EC`, `es-CO`…) | Sería contenido duplicado. Google elegiría una sola versión. | **No hacer** |

El tráfico por país hispanohablante **ya lo capturan** las páginas
`/destinos/<país>/`. Ese es el sitio correcto para la intención geográfica.

## 3. El error que hay que evitar: traducir en espejo

La traducción automática da un punto de partida excelente, pero deja las páginas
optimizadas para **keywords españolas traducidas**, que no son las que la gente
teclea en inglés. Ejemplo real de la diferencia:

| Página española | Traducción en espejo (débil) | Lo que se busca en inglés (fuerte) |
|---|---|---|
| Envío Internacional de Paquetes | *International Parcel Shipping* | **Ship a package from Peru** / *send parcel from Lima* |
| Carga Internacional | *International Cargo* | **Freight forwarder in Peru** / *air freight from Lima* |
| Envío de Documentos | *Document Shipping* | **Send documents from Peru** (courier) |
| Apostilla y Traducción | *Apostille and Translation* | **Apostille Peruvian documents** / *Peru apostille service* |

Regla: **la palabra "Peru" / "Lima" tiene que estar en el `title` y en el `slug`
de la versión en inglés.** Quien busca en inglés casi siempre incluye el origen,
porque está buscando un proveedor en un país que no es el suyo.

### Cómo se aplica con el sistema ya montado

1. **Traducir todo** desde la pantalla *Traducciones* (base sólida en minutos).
2. Sobre esa base, **editar a mano solo lo estratégico** en las 6–8 páginas que
   importan: `title`, `meta description`, H1 y **slug**. El slug se edita en la
   propia página de WordPress; el sistema conserva el vínculo con la página
   española (queda grabado el "slug maestro"), así que cambiarlo no rompe nada.
3. Volver a *Traducciones* solo cuando cambie el español.

## 4. Páginas que deben existir SOLO en inglés

Aquí está la diferencia entre "sitio traducido" y "sitio que capta". Son páginas
sin equivalente español, porque responden a una búsqueda que en español no
existe:

| Página propuesta (EN) | Intención que captura | Perfil |
|---|---|---|
| `/en/shipping-from-peru-to-usa/` | La ruta más buscada, con aduana, plazos y prohibiciones | A + B |
| `/en/freight-forwarder-peru/` | B2B: exportadores que buscan operador logístico en Perú | **B** |
| `/en/ship-luggage-from-peru/` | Turista/expat que se va y no puede llevar todo en el avión | A |
| `/en/apostille-peruvian-documents/` | Extranjero que necesita legalizar documentos peruanos | A + B |
| `/en/expats-guide-shipping-from-lima/` | Contenido de captación: guía práctica → deriva a cotizar | A |

**Sí se pueden crear sin tocar código:** en WordPress, crear la página con idioma
inglés (Polylang), sin traducción vinculada, y escribir su contenido en el editor.
El tema la renderiza por la vía de contenido editable. Conviene enlazarla desde
la home en inglés y desde el servicio equivalente.

## 5. Ajustes por idioma que sí mueven la conversión

Traducir el texto no basta si la página sigue "hablando" a un peruano:

- **Menú del idioma**: en inglés no tiene sentido listar 9 destinos hispanos con
  el mismo peso. Prioriza *USA, Spain, Italy, Brazil* y una entrada "All
  destinations". Se hace en Apariencia → Menús (Polylang crea un menú por idioma).
- **Moneda y unidades**: menciona USD junto a soles y `kg (lb)` en las tablas de
  peso volumétrico. Un americano no dimensiona "S/ 120".
- **Prueba social específica**: una reseña o caso de un cliente extranjero pesa
  más que diez en español para ese público.
- **Horario y contacto**: indica la zona horaria (GMT-5) y que se atiende por
  WhatsApp — para el perfil B, agrega un formulario de cotización de carga con
  campo de Incoterm y partida arancelaria.
- **Formulario en el idioma**: los campos y los mensajes de error del formulario
  se traducen desde *Traducciones → Textos fijos del diseño*.
- **Documentos y requisitos**: el extranjero desconoce la normativa peruana. Una
  sección "What you need before shipping" convierte mejor que cualquier eslogan.

## 6. Fuera del sitio (esto pesa tanto como el on-page)

1. **Google Business Profile**: un solo perfil (Lima), pero con descripción y
   publicaciones también en inglés; responder reseñas en el idioma en que llegan.
2. **Directorios donde busca el perfil A**: grupos y foros de expats en Lima,
   InterNations, comunidades de nómadas. Un enlace desde ahí vale más que diez
   directorios genéricos.
3. **Directorios B2B para el perfil B**: cámaras de comercio binacionales
   (Peruvian-American, British Peruvian), asociaciones de exportadores. Ese es el
   backlink que Google lee como "operador logístico real en Perú".
4. **Enlaces en el idioma correcto**: un backlink desde una web en inglés refuerza
   la versión `/en/`, no la española.

## 7. Cómo medir si funciona (y cuándo cortar)

En Google Search Console, con una sola propiedad de dominio:

- Filtra por **página → contiene `/en/`**. Mira *impresiones* antes que clics: si
  a los 2–3 meses hay impresiones creciendo, la indexación va bien aunque aún no
  haya clics.
- Revisa **Segmentación internacional**: "etiquetas hreflang sin retorno"
  significa que falta traducir alguna página; se arregla completando la
  traducción (el sistema solo declara las publicadas).
- Métrica de negocio real: **cotizaciones y clics a WhatsApp desde `/en/`**, no
  sesiones. Etiqueta el botón de WhatsApp por idioma para poder separarlo.
- **Criterio de corte honesto:** si a los 6 meses un idioma no genera ni
  impresiones relevantes ni una sola cotización, no insistas con más traducción:
  o el público no busca en ese idioma, o busca otra cosa. Mejor invertir ese
  esfuerzo en más páginas `/destinos/` o en el inglés.

## 8. Plan de ejecución sugerido

| Fase | Qué se hace | Resultado |
|---|---|---|
| **1 · Base** | Configurar Polylang, agregar inglés, *Traducir todo* | Sitio completo en inglés, indexable, con hreflang y sitemap |
| **2 · Afinado** | Reescribir a mano `title`, `H1`, `meta` y **slug** de las 6–8 páginas estratégicas con keywords reales del mercado inglés | Deja de competir consigo mismo; empieza a rankear por intención propia |
| **3 · Captación** | Crear las 3–5 páginas que solo existen en inglés (sección 4) | Cubre búsquedas que en español no existen (B2B incluido) |
| **4 · Conversión** | Menú, moneda, formulario y prueba social del idioma (sección 5) | El tráfico traducido empieza a convertir |
| **5 · Autoridad** | Backlinks y directorios en inglés (sección 6) | Consolida el ranking |
| **6 · Portugués** | Repetir 1–4 para `pt-BR`, con alcance reducido | Segundo mercado, con el aprendizaje ya hecho |

Las fases 1 y 2 son las que dan el salto grande. La 3 es la que trae clientes que
hoy **no existen** para Grenvíos en ningún idioma.

---

## 9. Cómo saber en qué idioma buscan (antes de gastar en traducir)

No hay que adivinar. Con el sitio en español ya publicado hay cuatro fuentes que
dicen en qué idioma llega la demanda real:

1. **Search Console → Rendimiento → Países.** Si aparecen impresiones desde
   Brasil, Estados Unidos o China sobre consultas propias, hay demanda latente.
   Filtra por consulta que no esté en español: son las que ya te encuentran a
   pesar de no tener contenido en ese idioma.
2. **Analytics → Idioma del navegador.** Visitas con `pt-BR`, `en-US` o `zh-CN`
   que entran a `/servicios/` y se van rápido = gente que no entiende la página.
   Ese rebote es dinero perdido y es la señal más directa.
3. **La bandeja real:** WhatsApp, correo y llamadas. ¿En qué idioma escriben los
   que ya preguntan? Si el equipo ya responde consultas en inglés o portugués a
   mano, ese idioma está validado sin necesidad de datos.
4. **Keyword Planner con el idioma cambiado.** Consulta el volumen de
   *"freight forwarder Peru"*, *"frete internacional Peru"*, *"秘鲁 货运"* con el
   idioma y país correspondiente. Si un idioma no da volumen ahí, no lo dará
   tampoco después de traducir.

**Orden de decisión:** primero se traduce el idioma donde ya hay evidencia (1–3),
no el que parece estratégico sobre el mapa. Traducir es barato con este sistema;
mantener un idioma vivo (revisar textos, responder en ese idioma, atender un
cliente que escribe en chino) no lo es.

### Caso concreto del chino

Aparece en la tabla de destinos y hay comercio real Perú–China, pero antes de
publicarlo conviene mirar dos cosas:

- **¿Quién buscaría?** Importadores chinos que compran en Perú, o empresas
  peruanas que traen carga de China. El segundo grupo **busca en español**, así
  que no necesita un sitio en chino.
- **¿Se puede atender?** Si llega una consulta en chino y nadie en la oficina
  puede responderla, la traducción genera una mala experiencia en vez de un
  cliente. Este es el filtro que decide, más que el SEO.

La misma pregunta vale para cualquier idioma: **traducir la web es el paso 1;
poder atender en ese idioma es el paso 0.**

## 10. Qué idiomas soporta el sistema (técnicamente)

Cualquiera que exista en Polylang. No hay idiomas escritos en el código: se
agrega el idioma y aparece solo en el selector, los `hreflang`, el sitemap y la
pantalla de Traducciones.

| Aspecto | Estado |
|---|---|
| Idiomas latinos (pt, en, it, fr, de…) | Completo, sin ajustes |
| Chino, japonés, coreano, ruso, griego | Funciona. El **slug** se genera romanizado (pinyin/romaji o su equivalente en inglés) para que la URL siga siendo legible al compartirla; se puede editar a mano en cada página |
| Árabe, hebreo (escritura derecha-izquierda) | Base funcional: se carga `assets/css/rtl.css` automáticamente. **Requiere revisión visual** sección por sección antes de publicar: el diseño original es de izquierda a derecha |
| Tipografía CJK | El navegador usa su fuente del sistema. Si se publica chino o japonés en serio, conviene añadir una fuente con esos glifos |

**Cobertura por proveedor de traducción** (se elige en Traducciones → Ajustes):

- **Claude** — cualquier idioma, y es el único que adapta el copy comercial en
  vez de traducir literal. Recomendado para idiomas donde el texto vende.
- **DeepL** — excelente calidad en portugués, inglés, italiano, alemán, francés,
  chino y japonés; su lista de idiomas es limitada.
- **Google** — la lista más amplia (útil para idiomas que DeepL no cubre), con
  calidad algo inferior en textos comerciales.

Se puede cambiar de proveedor por idioma: traduce un idioma con uno, cambia el
ajuste y traduce el siguiente con otro. Lo ya traducido se conserva en el
diccionario de cada idioma.
