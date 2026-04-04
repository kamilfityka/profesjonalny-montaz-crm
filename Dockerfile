FROM php:8.1-fpm

# Argumenty
ARG user=www
ARG uid=1000

# Instalacja zależności systemowych
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalacja Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Utworzenie użytkownika
RUN useradd -G www-data,root -u $uid -d /home/$user $user \
    && mkdir -p /home/$user/.composer \
    && chown -R $user:$user /home/$user

WORKDIR /var/www

# Kopiowanie kodu aplikacji
COPY . .

# Uprawnienia do storage i cache
RUN mkdir -p storage/framework/{sessions,views,cache} bootstrap/cache \
    && chown -R $user:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Kopiowanie entrypoint
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

USER $user

EXPOSE 9000
ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
