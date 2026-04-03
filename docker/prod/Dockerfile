# ====================== STAGE 1: Composer ======================
FROM composer:2 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist \
    --no-scripts

# ====================== STAGE 2: App ======================
FROM php:8.4-fpm-alpine

ENV TZ=UTC

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        libpq-dev \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        icu-dev \
        libzip-dev \
        libxml2-dev \
        imagemagick-dev \
        oniguruma-dev \
    && apk add --no-cache \
        tzdata \
        nginx \
        supervisor \
        libpq \
        libpng \
        libjpeg-turbo \
        freetype \
        icu-libs \
        libzip \
        curl \
        libxml2 \
        imagemagick \
        oniguruma \
    && cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini" \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_pgsql \
        pgsql \
        gd \
        mbstring \
        xml \
        zip \
        bcmath \
        soap \
        intl \
        opcache \
    && pecl install igbinary msgpack redis imagick \
    && docker-php-ext-enable igbinary msgpack redis imagick \
    && apk del .build-deps \
    && rm -rf /tmp/pear /tmp/* /var/cache/apk/*

WORKDIR /var/www/html

#Копируем Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Копируем vendor
COPY --from=vendor /app/vendor ./vendor

# Копируем конфиги
COPY docker/prod/nginx.conf        /etc/nginx/http.d/default.conf
COPY docker/prod/php-fpm.conf      /usr/local/etc/php-fpm.d/www.conf
COPY docker/prod/php.ini           $PHP_INI_DIR/conf.d/10-php.ini
COPY docker/prod/php-opcache.ini   $PHP_INI_DIR/conf.d/20-opcache.ini
COPY docker/prod/supervisord.conf  /etc/supervisor/conf.d/supervisord.conf
COPY docker/prod/start-container   /usr/local/bin/start-container

RUN chmod +x /usr/local/bin/start-container \
    && mkdir -p /run/php /run/nginx

# Копируем исходники
COPY . .

RUN mkdir -p storage/logs storage/framework/{sessions,views,cache} bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Запускаем скрипты Laravel
RUN composer dump-autoload --optimize --no-interaction

EXPOSE 80

ENTRYPOINT ["start-container"]