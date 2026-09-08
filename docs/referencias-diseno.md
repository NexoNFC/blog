# Referencias de diseño

Síntesis de investigación para orientar la interfaz de la plataforma informativa FESC.

El contexto institucional (identidad corporativa oficial) y el físico del campus (bloques, NFC) están en [contexto-institucional-y-fisico.md](contexto-institucional-y-fisico.md).

## 1. Portales y blogs institucionales

Patrones observados en sitios de educación superior (portales de noticias, facultades y comunicación institucional):

- Flujo **listado → detalle**, con tipología visible (noticia, evento, comunicado).
- Portada con **un destacado** y un listado secundario; poca ornamentación.
- Jerarquía tipográfica clara; tono sobrio y mobile-first.
- Comunicación institucional controlada en origen (portal y redes oficiales), no redacción paralela en un CMS propio.

Aplicación al producto: la web pública debe sentirse institucional y legible; el NFC añade descubrimiento por lugar, no un feed tipo red social. El catálogo se alimenta de FESC e Instagram, no de un editor interno.

## 2. Identidad FESC

| Elemento | Referencia |
|----------|------------|
| Fuente oficial de marca | [Identidad Corporativa](https://www.fesc.edu.co/portal/informacion-institucional/identidad-corporativa) (logos, manual, variantes) |
| Colores | Según manual oficial; tokens UI actuales son provisionales (gris + rojo) |
| Portal | [fesc.edu.co](https://fesc.edu.co/portal/) |

No inventar logos ni colores de marca cuando existan en la fuente oficial. La UI debe sentirse asociada a FESC, no ser una copia del portal. Detalle: [contexto-institucional-y-fisico.md](contexto-institucional-y-fisico.md).

La maquetación inicial usa tokens derivados; el logo oficial se integrará cuando el equipo incorpore los assets del manual.

## 3. Redes sociales

Cuentas de referencia:

- Instagram / Facebook / Threads: `@fesc.edusuperior`
- X: `@fesc_superior`

Estilo de comunicación en redes: fotografía institucional, eventos, logros estudiantiles y llamados a acción cortos.

Uso en el producto: Instagram es **fuente de ingesta** (texto e imágenes; videos como miniatura + enlace a la publicación). El tono de las piezas puede inspirar la redacción IA, pero la web **no** copia el layout de un feed social.

## 4. Gestión de NFC (industria y campus)

Plataformas y casos (Ixkio, Tapped, Kitetags, señalética NFC en campus) coinciden en:

1. URL o código **estable** en el chip.
2. Destino o contenido **administrable** sin reprogramar el hardware.
3. Estado activo/inactivo desde software.
4. Métricas de escaneo (trazabilidad).

Panel NFC típico: código/slug, nombre, **ubicación estructurada**, estado, contenidos asociados, URL de prueba, indicadores de uso.

En FESC, la ubicación se ancla al contexto físico documentado. Ver [contexto-institucional-y-fisico.md](contexto-institucional-y-fisico.md).

Experiencia al escanear: **landing de descubrimiento** del punto (varios contenidos), no un redirect fijo a una sola noticia. El usuario debe reconocer el lugar físico de origen.

## 5. Panel de administración — asociación NFC y catálogo ingerido

El administrador no crea noticias. El panel debe permitir:

- Ver el **catálogo ingerido** (fuente FESC o Instagram, fecha de extracción, enlace al origen, texto IA, imágenes).
- Distinguir publicaciones de **video** de Instagram (miniatura + «Ver en Instagram», sin reproductor embebido).
- **Asociar** uno o varios contenidos del catálogo a un punto NFC, y cambiar esa asociación sin reprogramar el chip.
- Activar o desactivar puntos NFC.
- Consultar **estadísticas** de uso (escaneos por punto, frecuencia, contenido mostrado si se define).

Campos que ya no aplican como flujo principal: formulario de título, cuerpo, tipo y publicación redactados a mano.

Fuera de la implementación actual: extractor cada 24 h, API de IA, persistencia del catálogo y asociación real punto ↔ contenido.

## 6. Decisiones de maquetación inicial

- Marca visual: sistema de diseño en [sistema-de-diseno.md](sistema-de-diseno.md) (gris/rojo FESC adaptado).
- Vistas con datos de ejemplo y componentes Blade por dominio (`ui`, `navigation`, `content`, `nfc`, `admin`).
- Módulos visibles hoy: home pública, detalle de contenido, experiencia NFC, dashboard admin, listados de contenidos y puntos NFC (aún con maqueta editorial; el producto objetivo es catálogo ingerido + asociación).
- Enlace explícito al [sitio oficial FESC](https://www.fesc.edu.co/portal/) para no confundir esta capa con el portal institucional.
