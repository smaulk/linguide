FROM ubuntu:22.04

WORKDIR /var/www/html
ENV TZ=UTC

RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

RUN apt-get update \
    && mkdir -p /etc/apt/keyrings \
    && apt-get install -y gnupg curl ca-certificates zip unzip nginx supervisor \
    && curl -sS 'https://keyserver.ubuntu.com/pks/lookup?op=get&search=0x14aa40ec0831756756d7f66c4f4ea0aae5267a6c' \
       | gpg --dearmor | tee /etc/apt/keyrings/ppa_ondrej_php.gpg > /dev/null \
    && echo "deb [signed-by=/etc/apt/keyrings/ppa_ondrej_php.gpg] https://ppa.launchpadcontent.net/ondrej/php/ubuntu jammy main" \
       > /etc/apt/sources.list.d/ppa_ondrej_php.list \
    && apt-get update \
    && apt-get install -y \
       php8.4-fpm \
       php8.4-pgsql php8.4-gd \
       php8.4-curl php8.4-mbstring \
       php8.4-xml php8.4-zip php8.4-bcmath php8.4-soap \
       php8.4-intl php8.4-readline \
       php8.4-msgpack php8.4-igbinary php8.4-redis \
       php8.4-imagick \
    && curl -sLS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer \
    && apt-get autoremove -y && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Копируем исходники
COPY --chown=www-data:www-data . .

# Устанавливаем зависимости без dev-пакетов
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Создаём нужные директории и выставляем права
RUN mkdir -p storage/logs storage/framework/{sessions,views,cache} bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Конфиги
COPY docker/prod/nginx.conf /etc/nginx/sites-available/default
COPY docker/prod/php-fpm.conf /etc/php/8.4/fpm/pool.d/www.conf
COPY docker/prod/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/prod/start-container /usr/local/bin/start-container
RUN chmod +x /usr/local/bin/start-container \
    && mkdir -p /run/php

EXPOSE 80

ENTRYPOINT ["start-container"]