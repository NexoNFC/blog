# Contextualizador institucional y físico — FESC

Documento de análisis (sin implementación de marca ni dominio de ubicaciones). Define las fuentes oficiales y el contexto de sede que deben orientar negocio, UX y arquitectura.

## 1. Identidad institucional (fuente oficial)

Fuente principal de identidad visual:

[Identidad Corporativa FESC](https://www.fesc.edu.co/portal/informacion-institucional/identidad-corporativa)

En esa página la institución publica, entre otros:

- Logo FESC (variantes, p. ej. 500×500)
- Logo institucional Fundación de Estudios Superiores Comfanorte
- Logo FESC fondo blanco / fondo negro
- Logo / sello de certificado ICONTEC
- Fondos para pantallas
- **Manual de Identidad Institucional**
- Material editable de presentaciones institucionales

### Reglas de marca

1. Antes de implementar logos, colores institucionales definitivos o elementos de marca, **consultar esa página** (y el manual descargable cuando el equipo lo incorpore al proyecto).
2. **No inventar** logotipos, colores de marca, tipografías institucionales ni sellos cuando puedan obtenerse de la fuente oficial.
3. Los tokens actuales del [sistema de diseño](sistema-de-diseno.md) son una **adaptación UI moderna inspirada** en la identidad conocida (gris + rojo). Deben reconciliarse con el manual oficial en cuanto se disponga de los assets y especificaciones exactas.
4. La plataforma debe **inspirarse** en FESC, no **copiar** el portal institucional actual: experiencia propia, tecnológica y limpia, coherente con la marca.

### Relación con el portal oficial

- El portal [fesc.edu.co/portal](https://www.fesc.edu.co/portal/) es la sede web institucional.
- Este producto es una **capa complementaria** de información + NFC para la comunidad FESC.
- Cuando el detalle sea institucional oficial, enlazar a FESC; no duplicar innecesariamente.

## 2. Contexto físico — sede Cúcuta (inventario inicial)

Solo lo definido. No inventar bloques, pisos, oficinas, laboratorios, salones ni zonas no proporcionados o no verificados en fuente oficial.

### Bloques

| Bloque | Pisos |
|--------|-------|
| Bloque A | 3 |
| Bloque B | 2 |
| Bloque C | 4 |

### Entradas / accesos

- Entrada por Avenida 4
- Entrada por Avenida 5

### Espacios con nombre propio

- Auditorio Avenida 5
- Biblioteca Moisés San Juan López

## 3. Jerarquía de ubicación (flexible)

Referencia habitual:

```text
Bloque → Piso → Zona/Espacio → Punto NFC
```

**No asumir** que todos los espacios pertenecen a esa cadena.

Lugares especiales pueden modelarse como **ubicaciones independientes** cuando tenga sentido:

- Biblioteca Moisés San Juan López
- Auditorio Avenida 5
- Entrada Avenida 4
- Entrada Avenida 5

Las zonas internas por piso **no están definidas**: si se necesitan, quedan como decisión pendiente (no inventarlas).

La arquitectura de información debe permitir agregar después: bloques, pisos, espacios, accesos, puntos NFC y tipos de ubicación, sin rehacer el dominio.

## 4. Relación con NFC

```text
UBICACIÓN FÍSICA
→ PUNTO NFC
→ CONTENIDO DIGITAL
→ ESCANEOS
→ ESTADÍSTICAS
```

### Ejemplos iniciales de contextualización (no catálogo cerrado de producción)

| Punto (ejemplo) | Ubicación conceptual |
|-----------------|----------------------|
| NFC — Entrada Avenida 4 | Acceso independiente |
| NFC — Entrada Avenida 5 | Acceso independiente |
| NFC — Bloque A, Piso 1 | Bloque → piso |
| NFC — Bloque A, Piso 3 | Bloque → piso |
| NFC — Bloque B, Piso 2 | Bloque → piso |
| NFC — Bloque C, Piso 4 | Bloque → piso |
| NFC — Biblioteca Moisés San Juan López | Espacio nombrado independiente |
| NFC — Auditorio Avenida 5 | Espacio nombrado independiente |

Sirven para razonar dominio y UX. No añadir otros edificios o espacios sin fuente oficial o definición del equipo.

## 5. Implicaciones

### Dominio

- Punto NFC = código/URL estable anclado a una ubicación (jerárquica o independiente).
- Contenido del punto = configurable en software, sin reprogramar el chip.
- Estadísticas agregables por punto y, a futuro, por ubicación.

### UX

- En móvil (prioridad NFC), mostrar el lugar de origen de forma legible y veraz.
- No rellenar niveles de ubicación con datos ficticios.
- Sensación de producto tecnológico asociado a la comunidad FESC, no de clon del sitio institucional.

### Administración (futuro)

- Gestionar catálogo de ubicaciones y asociación NFC ↔ ubicación ↔ contenido.
- Activar/desactivar puntos sin alterar el mapa físico.

## 6. Decisiones pendientes

1. Incorporar al repositorio (con licencia/uso institucional) logos y extracto aplicable del Manual de Identidad.
2. Confirmar códigos hex / pantone oficiales desde el manual (hoy: tokens UI provisionales).
3. Catálogo de zonas por piso, si aplica.
4. Modelo técnico unificado para ubicaciones jerárquicas vs independientes.
5. Profundidad mínima al crear un punto NFC.
6. Quién administra el catálogo físico vs quién solo edita contenido.

## 7. Qué no hacer

- Inventar marca o espacios físicos.
- Copiar el portal FESC.
- Tratar la ubicación solo como texto libre definitivo.
- Acoplar un NFC de forma permanente a una noticia.
- Implementar identidad visual “definitiva” sin consultar la página/manual oficiales.

## Relación con otros documentos

- [Sistema de diseño](sistema-de-diseno.md)
- [Referencias de diseño](referencias-diseno.md)
