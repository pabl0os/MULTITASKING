# MultiTasking - Gestor de Tareas y Proyectos

MultiTasking es una aplicación moderna y de alta calidad estética para la gestión de tareas, proyectos y notificaciones en tiempo real, diseñada con un enfoque minimalista y de alta productividad.

---

## 🚀 Características Principales

- **Panel de Control (Cerebro)**: Vista centralizada con KPIs y tareas priorizadas.
- **Gestión de Tareas**: Vista organizada de tareas con filtros interactivos por estado.
- **Proyectos**: Visualización del avance de proyectos con barras de progreso dinámicas.
- **Historial de Actividad (Notificaciones)**: Línea de tiempo que rastrea los eventos clave.
- **Inicio de Sesión**: Interfaz limpia y pulida para autenticación de usuarios.

---

## 🛠️ Tecnologías Utilizadas

- **Laravel 11**: Framework backend robusto y estructurado.
- **Tailwind CSS v4**: Para el estilizado rápido, moderno y modular.
- **Componentes Blade**: Templating de Laravel para máxima reutilización de componentes UI.
- **Outfit (Google Fonts)**: Tipografía moderna y elegante orientada a aplicaciones SaaS.

---

## 📦 Instalación y Configuración

Sigue estos pasos para ejecutar el proyecto en tu entorno local:

1. **Clonar o descargar el repositorio** en tu máquina local.
2. **Instalar dependencias de PHP**:
   ```bash
   composer install
   ```

4. **Configurar archivo de entorno**:
   Copia el archivo `.env.example` como `.env` y configura tus variables de entorno si es necesario.
   ```bash
   cp .env.example .env
   ```
5. **Generar la clave de la aplicación**:
   ```bash
   php artisan key:generate
   ```
6. **Compilar recursos de Frontend (Vite)**:
   ```bash
   npm run build
   # O para desarrollo continuo:
   npm run dev
   ```

---

## 🖥️ Ejecución local

Inicia el servidor de desarrollo de Laravel:

```bash
php artisan serve
```

Por defecto, la aplicación estará disponible en [http://127.0.0.1:8000](http://127.0.0.1:8000).

### Rutas Disponibles:
- `/` o `/login`: Pantalla de Login.
- `/dashboard`: Panel central (Cerebro).
- `/tasks`: Lista de Tareas.
- `/projects`: Módulo de Proyectos.
- `/notifications`: Centro de Notificaciones.
