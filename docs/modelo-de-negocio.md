# Modelo de negocio

Documento conceptual del producto. No define implementación técnica.

**Estado:** el dominio de noticias, NFC, visitas y escaneos ya persiste en base de datos. Solo existe el rol administrador. Aún faltan extracción automática, procesamiento de lenguaje y la acción «Actualizar ahora».

## Propósito

Plataforma web para la difusión y consulta de información de interés para la comunidad de la Fundación de Estudios Superiores Comfanorte (FESC).

La propuesta de valor conecta el espacio físico del campus con la plataforma digital mediante puntos NFC:

espacio físico → NFC → plataforma web → experiencia de información → estadísticas

Principio central: convertir puntos físicos de la institución en puertas de acceso digitales a información relevante de FESC, administrables y medibles.

El contenido no se redacta en la plataforma. Se extrae de fuentes oficiales (sitio FESC e Instagram), se redacta con IA a partir de ese extracto y el administrador asocia esas piezas a puntos NFC.

## Qué no es

- No es un blog simple al que se le agregaron chips NFC.
- No es un CMS editorial donde un editor crea noticias desde cero.
- Un NFC no pertenece de forma permanente a una noticia concreta.
- La plataforma no depende exclusivamente de los chips para funcionar.
- La consulta pública no exige inicio de sesión.
- No se reproducen videos de Instagram embebidos (iframe); Instagram no lo permite.

## Canales de acceso

1. **Web tradicional**: entrada directa al sitio, exploración pública del catálogo ingerido.
2. **NFC**: escaneo de un chip en un punto físico del campus.

Ambos canales deben coexistir.

## Origen del contenido

Flujo objetivo:

sitio FESC / Instagram FESC → extracción periódica (cada 24 h) → redacción con IA → catálogo en la plataforma → asociación a puntos NFC (admin) → consulta pública y estadísticas

| Fuente | Qué se trae | Qué se muestra al visitante |
|--------|-------------|-----------------------------|
| Sitio FESC | Texto, imágenes y URL de origen | Versión redactada por IA + enlace a la fuente oficial |
| Instagram | Texto, imágenes y URL de la publicación | Versión redactada por IA + imágenes; si el post es video, miniatura (si existe) y enlace «Ver en Instagram» |

La extracción se actualiza al menos cada 24 horas con la información más reciente. Cada pieza del catálogo conserva el enlace a su origen.

## Funcionamiento del NFC

- El chip se programa con una URL permanente asociada a un **punto NFC** (por ejemplo `/nfc/biblioteca`).
- El chip identifica el punto, no un contenido fijo.
- El contenido mostrado se elige en administración (asociación al catálogo ingerido) y puede cambiar sin reprogramar el chip.
- Un punto puede estar **activo** o **inactivo**. Si está inactivo, la plataforma responde de forma adecuada sin exigir reprogramación física.
- Cada acceso por NFC debe poder registrarse para estadísticas (escaneos, punto, fecha/hora, contenido mostrado o consultado, frecuencia, etc.).

## Experiencia del visitante (NFC)

Al escanear, el usuario no debe quedar limitado a “abrir una sola noticia”. Debe encontrar una experiencia de descubrimiento según el punto: información del lugar y piezas del catálogo asociadas (noticias, eventos, comunicados institucionales provenientes de FESC o Instagram).

La plataforma debe saber **desde qué punto NFC** llegó el usuario.

## Tipos de contenido

El contenido del catálogo se distingue por **fuente**, no por redacción interna.

| Fuente | Descripción |
|--------|-------------|
| FESC | Extraído del sitio institucional; texto mostrado = redacción IA; siempre con enlace al origen. |
| Instagram | Extraído de la cuenta institucional; texto e imágenes; videos no se reproducen en la plataforma (miniatura + enlace a Instagram). |

## Actores y perfiles

| Actor | Descripción |
|-------|-------------|
| Visitante público | Consulta información sin autenticación obligatoria. |
| Administrador | Asocia puntos NFC con piezas del catálogo ingerido, gestiona el estado de los puntos y consulta estadísticas. |

No existe el rol **editor**. No se crean ni se editan noticias redactadas a mano en la plataforma.

## Entidades conceptuales

- Usuario administrativo (administrador)
- Punto NFC (identificador, nombre, código estable, ubicación, descripción, estado, contenidos asociados, fechas)
- Extracción / pieza de origen (fuente FESC o Instagram, URL, texto crudo, imágenes, tipo imagen o video, fecha de captura)
- Contenido de catálogo (versión redactada por IA a partir del extracto, enlace a origen, medios)
- Asociación punto NFC ↔ contenidos del catálogo (configurable en el tiempo)
- Acceso / evento de uso NFC (trazabilidad)
- Interacción o consulta de contenido (si se define el nivel de detalle estadístico)

## Relaciones clave

- Un punto NFC tiene muchos accesos registrados.
- Un punto NFC se asocia a uno o varios contenidos del catálogo según la configuración vigente.
- Un contenido del catálogo proviene de una extracción y apunta a su fuente oficial.
- Un acceso NFC ocurre en un punto y puede relacionarse con el contenido mostrado o consultado.

## Reglas de negocio fundamentales

1. El NFC identifica un punto físico; el código/URL del punto es estable.
2. El contenido asociado se cambia en software, sin reprogramar el chip.
3. Activo / inactivo se controla en la plataforma.
4. Consulta pública sin login obligatorio.
5. La plataforma funciona con y sin NFC.
6. Los accesos por NFC deben poder medirse.
7. El catálogo se alimenta de extracción (FESC e Instagram) y redacción con IA; el administrador no redacta noticias.
8. El administrador asocia NFC al catálogo y consulta estadísticas.
9. Los videos de Instagram no se incrustan; se ofrece miniatura (si hay) y enlace a la publicación.
10. No implementar capacidades no definidas; evitar sobreingeniería.

## Módulos conceptuales del sistema

1. Acceso público y descubrimiento de información
2. Experiencia por punto NFC
3. Ingesta periódica (FESC e Instagram) y redacción con IA
4. Catálogo de contenidos ingeridos
5. Asociación de puntos NFC al catálogo
6. Administración (puntos, estado, un único rol admin)
7. Estadísticas y trazabilidad

## Decisiones aún abiertas

Quedan por definir, entre otras: URLs concretas del sitio FESC a extraer, cuenta de Instagram de referencia, proveedor y contrato de la API de IA, tratamiento de publicaciones que son solo video, forma exacta de la experiencia NFC (landing / bloques), cardinalidad y orden del contenido asociado, profundidad de las estadísticas, caducidad o baja de piezas ingeridas, alcance multi-sede y política de privacidad de los registros de acceso.

Estos puntos deben resolverse antes de fijar la arquitectura técnica detallada o implementar las etapas de extractor, IA y panel de asociación.
