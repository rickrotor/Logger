# Используем php:7.4-apache как базовый образ
FROM php:8.4-apache

# Устанавливаем необходимые зависимости
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    default-mysql-client \
    netcat-openbsd \
    libcurl4-openssl-dev \
    zip \
    unzip \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# RUN apt-get update && apt-get install -y  netcat

# Включаем модуль Apache mod_rewrite (если необходимо для MODX)
RUN a2enmod rewrite

# Разрешаем .htaccess файлы
RUN echo "<Directory /var/www/html/>\\n\
    AllowOverride All\\n\
    </Directory>" >> /etc/apache2/apache2.conf