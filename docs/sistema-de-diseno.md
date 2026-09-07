# Sistema de diseño

Documento aprobado a partir del análisis del portal FESC como referencia (no como clon). Define la identidad visual de la plataforma informativa + NFC.

## Principios

- Identidad FESC como referencia de marca; fuente oficial: [Identidad Corporativa](https://www.fesc.edu.co/portal/informacion-institucional/identidad-corporativa). Ver [contexto institucional y físico](contexto-institucional-y-fisico.md).
- Experiencia **propia y moderna** (no clon del portal institucional).
- Mobile-first (prioridad en experiencias NFC).
- Componentes Blade reutilizables sin sobre-componentizar.
- Capa complementaria al portal oficial; enlazar a FESC cuando el detalle sea institucional.

## Colores

Tokens UI **provisionales**, inspirados en la identidad institucional conocida (gris + rojo). Deben validarse contra el Manual de Identidad oficial antes de considerarlos definitivos.

| Token | Uso | Valor base (provisional) |
|-------|-----|------------|
| `primary` | CTA, énfasis de marca | `#c8102e` |
| `primary-dark` | Hover/active de primary | `#9e0c24` |
| `primary-soft` | Fondos suaves de énfasis | `#f8e8eb` |
| `secondary` | Texto fuerte, chrome, headers | `#434345` |
| `secondary-light` | Texto secundario | `#6b6b6e` |
| `muted` | Bordes y fondos sutiles | `#e8e8e9` |
| `background` | Fondo de página | `#f7f7f8` |
| `surface` | Cards, paneles | `#ffffff` |
| `text` | Cuerpo | `#1a1a1b` |
| `success` | Publicado / activo | `#047857` |
| `warning` | Borrador / aviso | `#b45309` |
| `danger` | Error / inactivo crítico | `#b91c1c` |
| `info` | Información neutra | `#1e3a5f` |

## Tipografía

| Nivel | Uso | Tratamiento |
|-------|-----|-------------|
| H1 | Hero, títulos de página | Serif, bold, tracking tight |
| H2 | Secciones | Serif, semibold |
| H3 | Cards / subtítulos | Serif o sans semibold |
| Body | Párrafos | Sans 400/500 |
| Small | Metadatos | Sans 14px |
| Caption | Labels / badges | Sans 12px, uppercase selectivo |

Familias: Source Sans 3 (UI), Source Serif 4 (titulares).

## Espaciado

Escala base 4/8: `2, 4, 6, 8, 12, 16, 24, 32, 48`.

## Radios y sombras

- Radios modestos (`rounded`, `rounded-md`).
- Sombras mínimas; preferir borde + superficie.

## Estados interactivos

Default, hover, focus visible, active, disabled, loading, error.

## Componentes por dominio

- `ui/` — primitives
- `navigation/` — navbar pública, sidebar admin
- `content/` — cards y meta de publicaciones
- `nfc/` — experiencia de punto
- `admin/` — stats y bloques del panel

## Relación con el portal oficial

Conservar: par cromático, tono sobrio, patrón título + resumen + fecha, enlace a fuente oficial.

No replicar: densidad de home, menú institucional completo, patrones CMS antiguos, embeds pesados en NFC.

## Contexto físico (NFC)

Ubicaciones y ejemplos NFC: [contexto-institucional-y-fisico.md](contexto-institucional-y-fisico.md). Jerarquía flexible (bloque→piso→zona o ubicaciones independientes como biblioteca, auditorio y entradas).
