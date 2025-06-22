# Etapa base: PHP + FPM
FROM php:7.4-fpm

# Establece el entorno
ARG APP_ENV=prod
ENV APP_ENV=${APP_ENV}

# Instala dependencias del sistema y Node.js + npm
RUN apt-get update && apt-get install -y \
    git unzip libonig-dev libxml2-dev libzip-dev zip curl gnupg && \
    curl -fsSL https://deb.nodesource.com/setup_18.x | bash - && \
    apt-get install -y nodejs

# Instala extensiones PHP necesarias
RUN docker-php-ext-install pdo pdo_mysql mbstring xml zip

# Instala Yarn globalmente
RUN npm install -g yarn

# Instala Composer desde la imagen oficial
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Directorio de trabajo
WORKDIR /var/www/html

# Copia archivos del proyecto
COPY . .

# Configura Git seguro y ejecuta composer
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV SYMFONY_SKIP_SCRIPT=1
# Copiar entorno de producción para que Symfony pueda cargarlo
COPY .env.production .env
# Git seguro + instalación de dependencias PHP
RUN git config --global --add safe.directory /var/www/html && \
    composer install --no-dev --optimize-autoloader

# Instala dependencias frontend y compila assets
RUN npm install --legacy-peer-deps && npm run build

# Asigna permisos adecuados (por si acaso)
RUN chown -R www-data:www-data var/cache var/log

# Expone el puerto 8000
EXPOSE 8000

# Usa el servidor PHP nativo sirviendo desde `public`
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]

