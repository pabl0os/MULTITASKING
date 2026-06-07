# Dockerfile
FROM php:8.2-fpm-alpine

# Instalar dependencias de sistema y extensiones de PHP requeridas por Laravel y PgSQL
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpq-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    nodejs \
    npm

RUN docker-php-ext-install pdo pdo_pgsql bcmath xml

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Instalar dependencias de PHP (Composer)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Instalar dependencias de JS y compilar assets (Vite)
RUN npm install && npm run build

# Configurar permisos para Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Copiar configuraciones de Nginx y Supervisor
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf

# Exponer el puerto
EXPOSE 80

# Comando para iniciar Nginx & PHP-FPM a través de Supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
