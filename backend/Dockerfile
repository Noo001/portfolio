FROM php:8.3-apache

# Включаем mod_rewrite
RUN a2enmod rewrite

# Устанавливаем зависимости системы
RUN apt-get update && apt-get install -y \
    libzip-dev \
    unzip \
    libcurl4-openssl-dev \
    libssl-dev \
    libsqlite3-dev \
    && docker-php-ext-install zip

# Устанавливаем Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Настройка Apache
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

# Копируем зависимости
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Копируем код
COPY . .

# Создаём нужные директории и выставляем права
RUN mkdir -p logs data && chown -R www-data:www-data logs data

# Порт
EXPOSE 80
