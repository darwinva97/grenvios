# Grenvíos — Guía de imágenes a reemplazar

La plantilla usa imágenes de banco (stock) como **provisionales**. Para que el sitio
refleje la marca, reemplaza estos archivos en `assets/img/` por fotos reales de Grenvíos,
**manteniendo el mismo nombre de archivo** (así no hay que tocar el código).

Paleta de marca: **blanco · vino (#7a1f2b) · negro**. Idea del cliente: persona sonriente
con polo vino y logo Grenvíos, cajas/paquetes, ambiente confiable y moderno.

## Logo (prioridad alta)
| Archivo | Uso | Tamaño sugerido |
|---|---|---|
| `logo.png` | Logo a color (footer, fondos claros) | ~280×80 px, PNG transparente |
| `logo-light.png` | Logo en blanco (cabecera sobre fondo) | ~280×80 px, PNG transparente |

## Inicio
| Archivo | Dónde aparece | Recomendado |
|---|---|---|
| `slider-bg.jpg` | Fondo del héroe (3 slides) | 1920×900 px. Persona con polo vino + paquete, o operación de envío |
| `content-bg-1.jpg` | Sección "Nosotros" del inicio y página Nosotros | 700×800 px |
| `content-bg-5.jpg`–`content-bg-8.jpg` | Bloque de características | 600×400 px |
| `team-1.jpg`, `team-2.jpg`, `team-3.jpg` | Fotos de los testimonios de clientes | 120×120 px, rostro |
| `sponsor-01.png`–`sponsor-06.png` | Logos de respaldo/alianzas (el cliente quiere logos como protagonistas) | PNG transparente, ~200×80 px |

## Servicios
| Archivo | Dónde |
|---|---|
| `content-bg-2.jpg` | Envío de paquetes (imagen destacada) |
| `content-bg-3.jpg`, `content-bg-4.jpg` | Galería de paquetes/embalaje |
| `hanging-container.png` | Carga internacional |
| `forklift.png`, `delivery-man.png` | Apoyos gráficos |

## Nosotros
- `content-bg-1.jpg`, `post-2.jpg`, `delivery-man.png` — bloque "quiénes somos".
- **Respaldo institucional:** los nombres (Colegio de Traductores, MRE, Cámara de Comercio,
  Min. de Educación, Min. de Transportes) están como texto. Si consigues los logotipos
  oficiales, se pueden mostrar como imágenes (el cliente quiere "logos como protagonistas").

## Recomendaciones técnicas
- Formato **WebP** o JPG optimizado (peso < 250 KB por imagen para que cargue rápido).
- Respeta la proporción original de cada archivo para no deformar el diseño.
- Pon nombres de archivo idénticos a los de la tabla; si usas otros, hay que actualizar
  las rutas en `template-parts/*.html`.
