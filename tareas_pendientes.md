# Tareas Pendientes del Proyecto "Multitasking"

Basado en el `prd.md`, los `requerimientos.md` y el estado actual del proyecto, a continuación se detalla lo que falta por implementar para alcanzar la versión MVP (v1.0).

## 1. Backend & Controladores (Lógica de Negocio)
- [x] **Controladores:** Crear controladores para la gestión de Usuarios, Proyectos, Tareas y Notificaciones.
- [x] **Autenticación (RF-1, RF-2, RF-3):** Implementar el registro, inicio de sesión y eliminación de cuentas.
- [x] **Gestión de Tareas (RF-4 a RF-12):** 
  - CRUD de tareas (Crear, leer, actualizar, eliminar).
  - Lógica para cambiar estados ("Pendiente", "En proceso", "Realizado").
  - Lógica para marcar tareas como "Atrasadas".
- [x] **Gestión de Proyectos (RF-15 a RF-21):**
  - CRUD de proyectos.
  - Para miembros de equipo, líderes y colíderes.
  - Lógica para delegar o degradar roles.
- [x] **Límites de Trabajo en Proceso (WIP) (RF-22, RF-23, RF-25):** Implementar la validación de los límites personal (M) y del proyecto (N) para tareas "En proceso".
- [x] **Algoritmo "Recomendado" (RF-9):** Desarrollar la lógica de ordenamiento de tareas que equilibre la prioridad y la fecha de entrega.

## 2. Base de Datos (Supabase)
- [x] **Migraciones:** Crear y ejecutar las migraciones de Laravel para las tablas del sistema.
- [x] **Modelos Eloquent:** Crear los modelos correspondientes con sus relaciones (Uno a Muchos, Muchos a Muchos).
- [x] **Solucionar Error de Conexión:** Asegurar que la conexión a la base de datos Supabase esté funcional y estable.

## 3. Frontend & Integración
- [x] **Vistas Dinámicas:** Reemplazar datos estáticos en Blade (`dashboard`, `tasks`, `projects`, `notifications`) con datos provenientes de la base de datos.
- [x] **Formularios:** Implementar los formularios funcionales para crear/editar tareas y proyectos con validaciones (CSRF, validación de campos).
- [x] **Navegación y UX:** Asegurar que los flujos de redirección tras cada acción (como tras crear una tarea) sean los correctos.

## 4. Sistema de Notificaciones (RF-8, RF-11)
- [x] **Cron Jobs / Task Scheduling:** Configurar los comandos (o *Jobs*) en Laravel para ejecutarse periódicamente y detectar qué tareas están por vencer.
- [x] **Envío de Correos/Alertas:** Implementar el envío de notificaciones automáticas 1 o 2 días antes, o unas horas antes, según corresponda.
- [x] **Personalización:** Permitir al usuario modificar cuándo desea recibir estas notificaciones.

## 5. Funcionalidades Avanzadas (Depende de priorización para v1.0 / v2.0)
- [x] **Serialización de Tareas (RF-13, RF-14, RF-30):** Impedir el inicio de tareas bloqueadas y re-serializar en caso de eliminación.
- [x] **Historial y Comentarios (RF-26, RF-32):** Implementar el registro de cambios en proyectos y el sistema de comentarios.
- [ ] **Diagramas de Gantt (RF-33):** Integración de alguna librería frontend para la visualización de tiempos en los proyectos. *(Reservado para v2.0)*.
