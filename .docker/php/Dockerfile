# 1. Començar des de la imatge FPM 8.3
FROM php:8.3-fpm

# 2. Instal·lar dependències del sistema
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev # <--- AFEGIDA llibreria per a 'zip'

# 3. Instal·lar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 4. Configurar i instal·lar extensions de PHP
# GD per a PhpSpreadsheet
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j$(nproc) gd
# pdo_mysql per a la base de dades
RUN docker-php-ext-install pdo_mysql
# zip per llegir fitxers .xlsx
RUN docker-php-ext-install zip # <--- AFEGIDA l'extensió 'zip'

# 5. Establir el directori de treball
WORKDIR /var/www/html