# Modelo de negocio

Documento conceptual del producto. No define implementación técnica.

## Propósito

Plataforma web para la difusión y consulta de información de interés para la comunidad de la Fundación de Estudios Superiores Comfanorte (FESC).

La propuesta de valor conecta el espacio físico del campus con la plataforma digital mediante puntos NFC:

espacio físico → NFC → plataforma web → experiencia de información → estadísticas e interacciones

Principio central: convertir puntos físicos de la institución en puertas de acceso digitales a información relevante, administrables y medibles.

## Qué no es

- No es un blog simple al que se le agregaron chips NFC.
- Un NFC no pertenece de forma permanente a una noticia concreta.
- La plataforma no depende exclusivamente de los chips para funcionar.
- La consulta pública no exige inicio de sesión.

## Canales de acceso

1. **Web tradicional**: entrada directa al sitio, exploración pública.
2. **NFC**: escaneo de un chip en un punto físico del campus.

Ambos canales deben coexistir.

## Funcionamiento del NFC

- El chip se programa con una URL permanente asociada a un **punto NFC** (por ejemplo `/nfc/biblioteca`).
- El chip identifica el punto, no un contenido fijo.
- El contenido mostrado se configura desde la administración y puede cambiar sin reprogramar el chip.
- Un punto puede estar **activo** o **inactivo**. Si está inactivo, la plataforma responde de forma adecuada sin exigir reprogramación física.
- Cada acceso por NFC debe poder registrarse para estadísticas (escaneos, punto, fecha/hora, contenido mostrado o consultado, frecuencia, etc.).

## Experiencia del visitante (NFC)

Al escanear, el usuario no debe quedar limitado a “abrir una sola noticia”. Debe encontrar una experiencia de descubrimiento según el punto: información del lugar, noticias, eventos, comunicados, información institucional y/o referencias a contenido oficial externo.

La plataforma debe saber **desde qué punto NFC** llegó el usuario.

## Tipos de contenido

| Tipo | Descripción |
|------|-------------|
| Interno | Creado en la plataforma (noticias, comunicados, eventos, académico, institucional, etc.). |
| Externo | Resumen contextual en la plataforma + enlace a la fuente oficial (p. ej. sitios FESC). |
| Combinado | Contenido propio que además referencia o enlaza fuentes externas. |

## Actores y perfiles

| Actor | Descripción |
|-------|-------------|
| Visitante público | Consulta información sin autenticación obligatoria. |
| Editor | Creación, edición y gestión de contenido. |
| Administrador | Gestión general de la plataforma, puntos NFC, permisos y seguimiento de uso. |

Los permisos y responsabilidades deben diferenciarse entre perfiles administrativos.

## Entidades conceptuales

- Usuario administrativo y rol
- Punto NFC (identificador, nombre, código estable, ubicación, descripción, estado, contenido asociado, fechas)
- Contenido (interno / externo / combinado)
- Tipo o categoría de contenido (pendiente de detalle)
- Asociación punto NFC ↔ contenidos (configurable en el tiempo)
- Acceso / evento de uso NFC (trazabilidad)
- Interacción o consulta de contenido (si se define el nivel de detalle estadístico)

## Relaciones clave

- Un punto NFC tiene muchos accesos registrados.
- Un punto NFC se asocia a uno o varios contenidos según la configuración vigente.
- Un contenido puede referenciar fuentes o enlaces externos.
- Un acceso NFC ocurre en un punto y puede relacionarse con el contenido mostrado o consultado.

## Reglas de negocio fundamentales

1. El NFC identifica un punto físico; el código/URL del punto es estable.
2. El contenido asociado se administra en software, sin reprogramar el chip.
3. Activo / inactivo se controla en la plataforma.
4. Consulta pública sin login obligatorio.
5. La plataforma funciona con y sin NFC.
6. Los accesos por NFC deben poder medirse.
7. Admin y editor tienen responsabilidades distintas.
8. No implementar capacidades no definidas; evitar sobreingeniería.

## Módulos conceptuales del sistema

1. Acceso público y descubrimiento de información
2. Experiencia por punto NFC
3. Gestión de contenido
4. Gestión de puntos NFC
5. Administración y permisos
6. Estadísticas y trazabilidad
7. Referencias a información oficial externa

## Decisiones aún abiertas

Quedan por definir, entre otras: forma exacta de la experiencia NFC (landing / bloques), cardinalidad y orden del contenido asociado, taxonomía de tipos, ciclo de vida editorial, profundidad de las estadísticas, caducidad de contenidos, alcance multi-sede y política de privacidad de los registros de acceso.

Estos puntos deben resolverse antes de fijar la arquitectura técnica detallada o implementar funcionalidades dependientes.
