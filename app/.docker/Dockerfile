FROM php:8.1-fpm

# Actualizar e instalar las dependencias necesarias
RUN apt-get update && apt-get install -y \
    zip unzip curl libonig-dev libxml2-dev \
    libzip-dev libicu-dev libssl-dev pkg-config \
    libpq-dev libmemcached-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl pdo pdo_mysql zip \
    && pecl install redis \
    && pecl install memcached \
    && docker-php-ext-enable redis memcached \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar la aplicación al contenedor
RUN find /code -type f -print0 | sort -z | xargs -0 sha256sum | sha256sum
COPY /code/ /var/www/html

COPY ./.docker/rootfs/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./.docker/rootfs/metrics_php.php /usr/share/php/
COPY ./.docker/rootfs/metrics_nginx.php /usr/share/php/
# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Instalar dependencias de Composer
RUN composer install --optimize-autoloader

# Establecer permisos
RUN chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} \; \
    && find /var/www/html -type f -exec chmod 644 {} \;

# Exponer el puerto
EXPOSE 9000

# Comando por defecto
CMD ["php-fpm"]
