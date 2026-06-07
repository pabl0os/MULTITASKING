1. Reglas de negocio
●	Todos los usuarios tienen que poder crear, modificar y eliminar las tareas que le pertenezcan.
●	El orden en el que aparecen las tareas puede ser cambiado entre “Recomendado”, “Por prioridad” o “Por fecha”.
●	La prioridad debe ser un parámetro más importante al momento de mostrar las tareas con más urgencia, pero debe ir debilitándose con las diferencias de tiempo, de forma que una tarea con baja prioridad, pero que se entrega hoy aparezca primero que una tarea con alta prioridad, pero que se entrega en una semana.
●	Las tareas deben tener los estados “Pendiente”, “En proceso” y “Realizado” que pueda ser modificado por el usuario.
●	Si una tarea se programa con el límite en la misma fecha que se escribió la notificación deberá ser lanzada entre una o dos horas antes de la hora límite con opción a que el usuario lo personalice.
●	Si una tarea se programa con el límite en un día diferente el usuario debe recibir la notificación uno o dos días antes del límite con opción a que el usuario lo personalice.
●	Si la tarea se excede de la fecha y hora límites entonces pasa a estar en estado “Atrasado” y se manda una notificación al usuario para que la complete.
●	Si hay tareas que están serializadas las tareas posteriores no se deben que poder realizar si es que las tareas anteriores no se han hecho.
●	Si una tarea serializada se cancela o borra las que seguían en la fila instantáneamente después de ellas dejan de estar serializadas con la que se borró, pasan a poder realizarse y se envía una notificación a los miembros de las tareas posteriores.
●	La persona que creó el proyecto automáticamente se convierte en líder de ese proyecto.
●	El líder sólo puede ser degradado por el mismo líder, pero debe elegir a un líder o miembro para ser el nuevo líder en el proceso.
●	Si la cuenta del líder es eliminada el colíder con mayor antigüedad pasa a ser el líder, si no hay colíder entonces el miembro más antiguo pasa a ser el líder.
●	El líder debe poder asignar a un miembro como colíder el cual podrá hacer lo mismo que el líder excepto asignar a miembros como colíderes, quitar este mismo rol a otros colíderes o borrar el proyecto.
●	El líder de proyecto debe tener el poder de incluir y sacar a miembros de equipo, asignar tareas a los miembros y a sí mismo.
●	Los miembros de un equipo no pueden asignarse tareas dentro del mismo proyecto, sólo el líder puede hacerlo.
●	Un usuario no debe tener más de N tareas en estado “En proceso” al mismo tiempo, el número N por defecto es 3, pero puede ser modificado por el líder de proyecto.
●	Un usuario no debe tener más de M tareas en estado “En proceso” al mismo tiempo, el número M por defecto es 8, pero puede ser modificado por el usuario.
●	Cualquier cambio en los parámetros N y M sólo afectará a cambios posteriores, además se aplicará siempre el límite más restrictivo entre N y M. 
●	Tanto miembros como líderes de proyecto pueden hacer comentarios sobre las tareas del proyecto.
●	El proyecto puede o no tener una prioridad global, pero las tareas sí deben llevar una prioridad cada una.
●	El proyecto puede o no tener una fecha límite global, pero las tareas si deben llevar una fecha y hora cada una.
●	Los miembros de equipo solo pueden modificar las tareas que se les han asignado a ellos dentro de un proyecto, ya sea su contenido o el estado.
●	Si en un proyecto hay tareas serializadas que le pertenecen a distintos miembros y la tarea predecesora se marca como “Completada”, los miembros encargados de la o las tareas sucesoras deben recibir una notificación para indicar que ya pueden realizar su tarea.
●	Un proyecto no puede ser clasificado como “Terminado” si una tarea activa dentro de él aún está “En proceso”, “Pendiente” o “Retrasada”.
●	Los proyectos deben tener un historial en el que se guarden los cambios realizados en el estado, fechas o asignación de usuarios de las tareas en el proyecto, así como el usuario que los hizo y la fecha y hora de modificación.
●	Si un usuario elimina su cuenta sus tareas se eliminan, sus tareas dentro de un proyecto pasan a no tener miembro asignado y el líder y colíderes reciben una notificación para que sea reasignado, se elimina de los miembros de proyecto y si era el único en un proyecto (como líder) el proyecto también se elimina.
2. Requerimientos funcionales y no funcionales

RF-1	Registro de usuarios
Descripción	Un usuario debe ser capaz de registrarse utilizando un nombre de usuario y una contraseña.
Dependencias	Ninguna

RF-2	Inicio de sesiòn
Descripción	El usuario debe poder iniciar la sesión registrada utilizando su nombre de usuario y contraseña
Dependencias	RF-1

RF-3	Eliminaciòn de cuenta
Descripción	El usuario debe poder eliminar su propia cuenta.
Dependencias	RF-2

RF-4	Registro de tareas
Descripción	El usuario debe poder registrar tareas con nombre, descripción, fecha de entrega y prioridad.
Dependencias	RF-2

RF-5	Modificaciòn de tareas
Descripción	El usuario debe poder modificar los atributos de tareas registradas anteriormente.
Dependencias	RF-4

RF-6	Eliminaciòn de tareas
Descripción	El usuario debe poder eliminar las tareas que el mismo haya registrado anteriormente.
Dependencias	RF-4

RF-7	Visibilización de tareas
Descripción	El sistema debe mostrar las tareas al usuario según el orden que escoja, ya sea “recomendado”, “Por fecha” o “Por prioridad”
Dependencias	RF-4

RF-8	Notificación de tareas
Descripción	El sistema debe notificar al usuario sobre la entrega de tareas tiempo antes de su realización, en caso de que la tarea se registre el mismo día de entrega se debe notificar una o dos horas antes, en caso de que se registre para un día diferente debe notificarse uno o dos días antes.
Dependencias	RF-4

RF-9	Cálculo de prioridad
Descripción	En la vista de “Recomendado” la prioridad debe ser el parámetro más importante al momento de mostrar las tareas con más urgencia, sin embargo, debe debilitarse con la fecha de entrega de modo en que las tareas de alta prioridad se muestran después de las tareas que se entregan el mismo día.
Dependencias	RF-4

RF-10	Estado de tareas
Descripción	Las tareas deben tener el estado “Pendiente”, “En proceso” y “Realizado” que pueda ser modificado por el usuario
Dependencias	RF-4

RF-11	Personalización de notificación
Descripción	El usuario puede modificar la notificación establecida de forma predeterminada y establecer el tiempo de notificación el mismo.
Dependencias	RF-8

RF-12	Estado atrasado
Descripción	El sistema debe marcar una tarea como “Atrasada” si es que pasa la fecha de entrega sin estar realizada y notificar al usuario para que la realice.
Dependencias	RF-10

RF-13	Serialización de tareas
Descripción	El usuario debe poder serializar varias tareas en orden de realización haciendo que las tareas posteriores no puedan realizarse sin realizar las tareas previas.
Dependencias	RF-4

RF-14	Re-serialización de tareas
Descripción	El sistema debe re-serializar las tareas cuando el usuario elimine una tarea serializada, al re-serializar las tareas las tareas posteriores dejan de estar vinculadas a la tarea eliminada para que las tareas posteriores puedan realizarse sin realizar la tarea eliminada.
Dependencias	RF-13

RF-15	Creación de proyectos
Descripción	El usuario debe poder crear proyectos que serán realizados por varios participantes.
Dependencias	RF-4

RF-16	Lider de proyecto
Descripción	El sistema debe establecer como líder del proyecto a la persona que lo creó, este tendrá los privilegios de eliminar participantes del proyecto, agregar participantes y establecer tareas para los participantes y a sí mismo.
Dependencias	RF-15

RF-17	Modificación de proyectos
Descripción	El líder de un proyecto debe poder modificar los detalles y atributos de un proyecto
Dependencias	RF-16

RF-18	Eliminación de proyectos
Descripción	El líder de un proyecto debe ser capaz de eliminar el proyecto mismo.
Dependencias	RF-16

RF-19	Establecimiento de colideres
Descripción	El Líder de un proyecto debe ser capaz de establecer uno o varios colíderes para un proyecto de entre los participantes, estos tendrán los mismos privilegios que el líder excepto la modificación de colíderes y la eliminación del proyecto.
Dependencias	RF-16

RF-20	Degradación del lider
Descripción	El líder de un proyecto solo podrá ser degradado por el mismo líder, sin embargo, este deberá escoger a otro participante que será establecido como líder del proyecto.
Dependencias	RF-19, RF-16

RF-21	Establecimiento automático de lider
Descripción	El sistema debe establecer un líder cuando la cuenta del líder de proyecto es eliminada, el nuevo líder será el colider registrado con más antigüedad, en caso de no haber colíderes se establecerá como líder al participante más antiguo.
Dependencias	RF-16, RF-20

RF-22	Límite de tareas en proceso por proyecto
Descripción	En un proyecto con múltiples participantes un usuario no puede tener más de N tareas en estado “En proceso”, este número N puede ser modificado por el líder, con un valor predeterminado de 3
Dependencias	RF-15

RF-23	Límite personal de tareas “En proceso”
Descripción	El usuario no podrá tener más de M tareas en estado “En proceso”, el número M es 8 de forma predeterminada pero puede ser modificado por el usuario.
Dependencias	RF-15, RF-10

RF-24	Bloqueo de asignación de tareas
Descripción	Los participantes no deben poder asignar tareas a otros participantes ni a sí mismos.
Dependencias	RF-5, RF-16

RF-25	Cambio de N y M
Descripción	Tras realizar el cualquier cambio en los parámetros N y M estos solo se aplicarán en cambios posteriores, y siempre se aplicará el más restrictivo entre N y M
Dependencias	RF-22, RF-23

RF-26	Comentarios en tareas
Descripción	Los usuarios, y el Líder de un proyecto deben poder realizar comentarios en el sistema sobre las tareas del mismo proyecto.
Dependencias	RF-4, RF-15

RF-27	Prioridades en proyecto
Descripción	El Líder debe poder asignar una prioridad global al proyecto y prioridades a las tareas, sin embargo, la prioridad global no debe ser obligatoria.
Dependencias	RF-4, RF-15

RF-28	Fechas en proyecto
Descripción	El Líder debe poder asignar una fecha de entrega global del proyecto y fechas de entrega a las tareas, sin embargo, la fecha de entrega global no debe ser obligatoria.
Dependencias	RF-4, RF-15

RF-29	Modificación en proyectos
Descripción	Los participantes de un proyecto deben solo poder modificar las tareas que se le han asignado, tanto los atributos como el estado.
Dependencias	RF-5, RF-15

RF-30	Serialización en proyectos
Descripción	El sistema debe notificar a los participantes cuando una tarea serializada pueda empezar a realizarse en el caso de que la tarea anterior sea completada y esta estuviera asignada a otro participante.
Dependencias	RF-13, RF-15

RF-31	Terminación de proyectos
Descripción	El Líder de un proyecto podrá marcar como terminado un proyecto, siempre y cuando todas las tareas dentro de este estén en estado de “Realizada”
Dependencias	RF-10, RF-15

RF-32	Registro de cambios en proyectos
Descripción	El sistema debe proveer y mostrar en un proyecto un registro de los cambios realizados en este, asimismo, deberá mostrar el usuario que hizo los cambios, la fecha en que se hicieron y la hora de modificación.
Dependencias	RF-15

RF-33	Estado de tareas tras eliminación de cuentas
Descripción	El sistema debe borrar las tareas de un usuario cuando este elimine su cuenta, asimismo las tareas asignadas a este dentro de un proyecto deben ser marcadas como sin asignar y en caso de que el usuario esté solo en un proyecto (Como líder) este también deberá ser borrado.
Dependencias	RF-3, RF-4

RF-33	Creación de diagramas
Descripción	El líder del proyecto debe poder ser capaz de realizar diagramas de Gant simples utilizando las tareas asignadas anteriormente.
Dependencias	RF-4, RF-15, RF-16


RNF-1	Seguridad
Descripción	El sistema debe asegurar que los datos de los usuarios no deban ser vistos o accedidos por personas no autorizadas

RNF-2	Disponibilidad
Descripción	El sistema debe estar siempre disponible para el usuario

RNF-3	Fiabilidad
Descripción	Los datos deben guardarse fielmente en el sistema y evitar que estos se pierdan

RNF-4	Velocidad
Descripción	Las acciones en el sistema deben realizarse en menos de 3 segundos para evitar retrasos en el uso de los usuarios.

RNF-5	Paleta de colores
Descripción	Las interfaces del sistema deberán utilizar una paleta de colores claros que permitan distinguir las letras y otros elementos del sistema claramente del fondo.

RNF-6	Sistemas de navegación
Descripción	El sistema deberá hacer uso de sistemas de navegación principales y secundarios como menús y submenús para mejorar la experiencia del usuario.

RNF-7	Navegación predecible
Descripción	La navegación por el sistema debe ser simple y predecible para que el usuario pueda navegar libremente por el sistema.

