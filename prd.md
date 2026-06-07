**PRD: Multitasking**
**1. Problema**
La falta de un sistema automatizado centralizado para la gestión de tareas genera desorden, ineficiencia y fomenta la procrastinación. Al no existir un seguimiento continuo que exija registrar avances reales ni un mecanismo que envíe alertas activas sobre la proximidad de los plazos, los usuarios pierden el control de sus compromisos académicos y profesionales, afectando la calidad de su trabajo y propiciando retrasos inevitables.
**2. Usuarios**
**Usuario Principal**
**Usuario Registrado:** Persona que maneja múltiples proyectos y necesita organizar sus actividades por prioridad para evitar la procrastinación.

- **Situación actual:** Utiliza un ecosistema fragmentado de herramientas (Notion, Trello, Craft Docs), sin recibir recordatorios activos, lo que lo obliga a revisar manualmente el estado de las tareas. En ocasiones recurre a herramientas empresariales (como Jira) que resultan excesivamente complejas para un flujo de trabajo ligero y directo.

**Usuarios Secundarios**

- **Líder de proyecto:** Creador de un proyecto grupal con permisos administrativos para designar roles, asignar tareas y establecer límites.
- **Colíder:** Miembro promovido para asistir en la administración del proyecto, con privilegios compartidos con el líder (con ciertas restricciones).
- **Miembro del proyecto:** Participante regular con interacción limitada a sus tareas asignadas.
- **Sistema (Automático):** Entidad lógica encargada de disparar correos electrónicos y alertas de fechas límite de forma autónoma.
- **Invitado:** Usuario no autenticado que solo interactúa con los módulos de acceso (registro/inicio de sesión).
- **Situación actual grupal:** Se comunican por plataformas de mensajería externas (WhatsApp) y llevan un control mixto y manual, lo que provoca que los equipos pierdan visibilidad de los cuellos de botella y estanquen el progreso hasta el último minuto.

**3. Flujo del Usuario Principal (Gestión Individual)**
1. **Autenticación:** El usuario accede a la plataforma e inicia sesión.
2. **Revisión del Panel:** Visualiza un resumen general del estado de sus tareas (pendientes, en proceso, realizadas, atrasadas) y alertas de vencimiento.
3. **Registro y Organización:** Crea una nueva tarea (nombre, fecha límite, prioridad) y visualiza su lista bajo el ordenamiento inteligente "Recomendado".
4. **Ejecución:** Selecciona una tarea y cambia su estado a "En proceso", validando automáticamente que no exceda su límite personal de tareas simultáneas.
5. **Finalización:** Concluye la actividad, cambia el estado a "Realizado" y el sistema registra el avance en la bitácora.
**Flujo del Usuario Secundario (Líder de Proyecto)**
1. **Creación del entorno:** Registra un nuevo proyecto con nombre, fecha límite y prioridad global.
2. **Conformación del equipo:** Invita a miembros registrados y asigna roles de colíder si es necesario.
3. **Delegación:** Crea tareas y las asigna a los miembros del equipo.
4. **Ajuste de control:** Modifica el límite de tareas "En proceso" específico para su proyecto.
**Flujo del Usuario Secundario (Miembro del Proyecto)**
1. **Revisión:** Ingresa al proyecto y consulta sus tareas asignadas.
2. **Inicio:** Cambia el estado de su tarea a "En proceso", respetando la restricción del límite de tareas simultáneas.
3. **Ejecución:** Realiza el trabajo asignado (utilizando canales externos en la v1.0).
4. **Conclusión:** Marca la tarea como "Realizado", actualizando el historial general del equipo.
**4. Modelo de Datos**
La arquitectura cumple con los requerimientos de persistencia relacional al estructurarse en 7 tablas fundamentales, superando el estándar necesario para la evaluación técnica.  
**Usuario**

- idUsuario (INT)
- nombre (VARCHAR)
- correo (VARCHAR)
- contraseña (VARCHAR)
- limitePersonal (INT)

**Proyecto**

- idProyecto (INT)
- nombre (VARCHAR)
- descripcion (TEXT)
- prioridadGlobal (VARCHAR)
- fechaLimite (DATETIME)
- estado (VARCHAR)
- idLider (INT)

**Tarea**

- idTarea (INT)
- titulo (VARCHAR)
- descripcion (TEXT)
- prioridad (VARCHAR)
- fechaLimite (DATETIME)
- estado (VARCHAR)
- idProyecto (INT)
- idUsuarioAsignado (INT)

**MiembroProyecto**

- idMiembro (INT)
- rol (VARCHAR)
- fechaIngreso (DATE)
- idUsuario (INT)
- idProyecto (INT)

**Notificacion**

- idNotificacion (INT)
- mensaje (VARCHAR)
- fechaEnvio (DATETIME)
- tipo (VARCHAR)
- idUsuario (INT)

**Comentario (Reservado para v2.0)**

- idComentario (INT)
- contenido (TEXT)
- fechaPublicacion (DATETIME)
- idUsuario (INT)
- idTarea (INT)

**RegistroHistorial (Reservado para v2.0)**

- idRegistro (INT)
- descripcionCambio (TEXT)
- fechaHoraCambio (DATETIME)
- idUsuario (INT)
- idProyecto (INT)

**5. Roles & Permisos**

- **Invitado:** Puede registrarse, iniciar sesión y recuperar contraseña.
- **Miembro del proyecto:** Puede ver las tareas que le fueron asignadas. Puede editar el contenido y cambiar el estado de sus propias tareas asignadas.
- **Colíder:** Puede administrar el proyecto, crear/asignar tareas y agregar nuevos miembros. No puede borrar el proyecto base, degradar al líder actual ni modificar los permisos de otros colíderes.
- **Líder de proyecto:** Puede gestionar absolutamente todo dentro de su proyecto, incluyendo su eliminación total, designación de colíderes, establecimiento de fechas límite globales y modificación de los límites de trabajo en proceso.

**6. Panel de Administración**
**No aplica para este proyecto.**
La aplicación operará estrictamente como un entorno de autogestión para el usuario final. No existirá un dashboard de "Super Admin" global. Las jerarquías y administraciones están limitadas de manera encapsulada a la gestión interna de cada Proyecto a través de la tabla `MiembroProyecto`.
**7. MVP (v1.0)**
**Incluye:**

- Autenticación, registro y autogestión de cuenta.
- Creación de proyectos, invitaciones de miembros y gestión de roles internos.
- Creación de tareas (individuales y asignadas en grupo).
- **Core Feature:** Algoritmo de ordenamiento "Recomendado" para priorización inteligente.
- **Core Feature:** Sistema automático de notificaciones y alertas por correo para plazos cercanos.

**No Incluye (v2.0):**

- Serialización de tareas (cadenas de dependencias y re-serialización).
- Bloqueo duro por límites estrictos de trabajo en proceso (WIP / parámetros N y M).
- Sistema interno de comentarios por tarea.
- Bitácora e historial detallado de auditoría de cambios (`RegistroHistorial`).

**8. Branding**

- **Nombre:** Multitasking
- **Tono:** Minimalista, limpio, profesional, orientado estrictamente a la productividad.
- **Estructura:** Sistema de navegación simple y predecible mediante el uso de tarjetas con bordes suaves para separar visualmente proyectos, tareas y alertas.
- **Colores:**
  - **Fondo principal:** Blanco o gris muy claro para máximo contraste y legibilidad.
  - **Barra de navegación:** Tono oscuro.
  - **Acentos (Botones/Progreso):** Azul.
  - **Etiquetas Semánticas:** Rojo/Rosa (Prioridad alta / Atrasado), Amarillo (Prioridad media), Verde (Terminado).