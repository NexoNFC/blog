# Convenciones de desarrollo

Documento de referencia del equipo para la plataforma informativa FESC (PPA, Ingeniería de Software): difusión de información para la comunidad institucional, con acceso web y puntos NFC en el campus.

El dominio y las reglas de negocio están en [Modelo de negocio](modelo-de-negocio.md).

El proyecto se trata como software real: buenas prácticas, trazabilidad, separación de responsabilidades, documentación y desarrollo incremental.

---

## 1. Stack tecnológico

### Permitido

- Laravel
- PHP
- Blade
- HTML / CSS / JavaScript
- Tailwind CSS
- MySQL / MariaDB
- Git / GitHub

### No utilizar (salvo decisión técnica explícita)

- React
- Vue
- Angular
- Inertia
- Livewire

La interfaz se construye principalmente con Blade, HTML, Tailwind CSS y JavaScript.

---

## 2. Principios

- Prioridad: código correcto > mantenible > simple > sofisticado innecesario.
- No sobreingeniería (microservicios, CQRS, Event Sourcing, arquitectura hexagonal completa, capas artificiales).
- No implementar “porque funciona”: comprender el problema, el impacto, los archivos afectados y no romper lo existente.
- Nombres de código en inglés; documentación funcional y académica en español.
- Antes de cambios arquitectónicos importantes: explicar problema, propuesta, alternativas, impacto y archivos afectados.
- Dependencias solo con necesidad real; preferir soluciones nativas de Laravel.

### Identidad del repositorio

El repositorio debe representar el trabajo del equipo. No incluir en README, commits, ramas, issues, pull requests, documentación, comentarios, nombres de código, mensajes de error ni configuración referencias innecesarias a herramientas de apoyo al desarrollo.

---

## 3. Arquitectura

Separar correctamente:

- Rutas
- Controladores
- Modelos
- Migraciones
- Seeders
- Form Requests
- Services
- Policies
- Vistas y componentes Blade
- JavaScript y recursos estáticos

Reglas:

- Controladores delgados; lógica compleja en un Service.
- Sin lógica de negocio en Blade ni en rutas.
- Nombres claros alineados al dominio (`ContentController`, `NfcPointService`, `ContentRequest`). Evitar nombres genéricos (`DataController`, `MainController`, etc.).
- Antes de crear o modificar: revisar el código existente y no duplicar estructuras.
- El NFC identifica un punto físico administrable; no acoplar de forma permanente un chip a una sola publicación.

---

## 4. Base de datos

- Toda modificación estructural mediante migraciones.
- No modificar la estructura de la BD manualmente como flujo principal.
- Nombres descriptivos, claves foráneas, índices justificados e integridad referencial.
- Normalización razonable, sin sobrecomplicar el modelo.
- Antes de una tabla nueva: confirmar que la información requiere una entidad independiente.

---

## 5. Modelos Eloquent

- Representar correctamente las entidades del dominio.
- Definir relaciones (`hasMany`, `belongsTo`, `hasOne`, `belongsToMany`) cuando corresponda.
- Evitar N+1; usar eager loading.
- Lógica de negocio compleja fuera del modelo (en Service).

---

## 6. Validación y seguridad

- Validar toda entrada de usuario.
- Preferir Form Requests cuando la validación sea compleja o reutilizable.
- Usar autenticación de Laravel, Policies/Gates, protección CSRF, mass assignment protection y Eloquent/Query Builder.
- Secretos solo en `.env` (nunca en el repositorio). Mantener `.env.example` actualizado.
- Mensajes al usuario en español; no exponer detalle técnico sensible.
- Diferenciar errores de validación, autorización, recurso inexistente e internos.

---

## 7. Git y flujo de trabajo

- No desarrollar directamente en `main` (`main` = código estable).
- Trazabilidad: tarea → rama → commits → pull request → merge.
- Una rama = una tarea; no mezclar funcionalidades independientes.

### Tareas

Cada tarea importante debe indicar:

- Qué se necesita
- Por qué se necesita
- Comportamiento esperado
- Criterios de aceptación

### Ramas

```
feature/TASK-XXX-nombre-descriptivo
fix/TASK-XXX-...
docs/TASK-XXX-...
chore/TASK-XXX-...
```

Evitar nombres genéricos: `nueva`, `prueba`, `final`, `arreglo`, `cambios`.

### Commits

Formato Conventional Commits en español:

```
tipo: descripción
```

Tipos: `feat`, `fix`, `docs`, `refactor`, `test`, `chore`, `style`.

Commits pequeños y lógicos. Evitar mensajes genéricos (`cambios`, `update`, `final`).

### Pull Requests

Título: `[TASK-XXX] Descripción`

Incluir: objetivo, cambios realizados, módulos afectados, validación y consideraciones.

### Flujo por funcionalidad

1. Definir tarea y criterios de aceptación
2. Crear rama
3. Analizar código existente
4. Implementar solo lo de la tarea
5. Validar (manual y/ o pruebas)
6. Commits claros → PR → merge a `main`
7. Documentar si aplica

No marcar como terminado lo no implementado, no validado o incompleto. Diferenciar: implementado / validado / pendiente / requiere revisión.

---

## 8. Frontend

- Vistas Blade limpias: layouts, componentes, partials y slots.
- Sin lógica de negocio en Blade; evitar HTML duplicado.
- Componentes reutilizables cuando un elemento se repita (`button`, `card`, `input`, `alert`, `badge`).
- Tailwind organizado; evitar estilos repetitivos o innecesarios.
- JavaScript solo cuando aporte interacción real.
- Sin emojis como elementos principales de la interfaz; preferir iconos consistentes.
- Jerarquía visual, espaciado, tipografía y estados (`hover`, `focus`, `disabled`) coherentes.
- Responsive: escritorio, tablet y móvil.

---

## 9. Documentación

El README debe mantenerse alineado con el estado real del proyecto (descripción, objetivo, tecnologías, requisitos, instalación, configuración, ejecución, estructura, base de datos, usuarios de prueba si existen y comandos importantes).

No inventar funcionalidades que aún no existan.

---

## 10. Pruebas

Validar funcionalidades importantes antes de integrar a `main`.

Cuando sea razonable, cubrir con pruebas Laravel:

- Autenticación y autorización
- Validaciones
- Operaciones CRUD
- Relaciones importantes
- Reglas de negocio

No crear pruebas solo para aumentar artificialmente la cobertura.

---

## 11. Configuración y no destructividad

- No modificar configuración global (`composer.json`, `package.json`, config de Laravel, Docker, BD, auth) sin razón clara e impacto analizado.
- Antes de eliminar archivos, tablas, columnas, rutas, controladores, componentes o paquetes: comprobar dependencias.
- Si un cambio puede romper funcionalidades existentes, advertirlo antes de ejecutarlo.

---

## 12. Checklist antes de un cambio

- ¿Pertenece a la tarea actual?
- ¿Respeta la arquitectura y la separación de responsabilidades?
- ¿Es necesario y mantenible?
- ¿Puede romper algo existente?
- ¿Puede explicarse técnicamente?
- ¿Tiene trazabilidad (tarea → rama → commit)?

Si alguna respuesta es negativa: detenerse y analizar antes de continuar.
