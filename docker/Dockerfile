# ============================================================
# Stage 1 — Composer dependencies
# ============================================================
FROM composer:2 AS composer-deps

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader \
    --ignore-platform-reqs

# ============================================================
# Stage 2 — PHP-FPM runtime
# ============================================================
# No node-build stage: dialogues-cse's webpack-mix outputs (public/css, public/js,
# public/mix-manifest.json) are committed to the repo.
FROM php:8.4-fpm-alpine AS runtime

RUN apk add --no-cache \
        bash git curl zip unzip mysql-client tzdata \
        icu-dev libzip-dev oniguruma-dev libxml2-dev \
        libpng-dev libjpeg-turbo-dev freetype-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        bcmath exif gd intl mbstring opcache pcntl pdo_mysql zip \
    && rm -rf /var/cache/apk/*

# phpredis (PECL) — for CACHE_DRIVER=redis / SESSION_DRIVER=redis backed by the shared Redis on the `db` network
RUN apk add --no-cache --virtual .redis-build-deps autoconf gcc g++ make linux-headers \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .redis-build-deps \
    && rm -rf /tmp/pear

ENV TZ=Europe/Paris
RUN cp /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

WORKDIR /var/www/html

COPY --chown=www-data:www-data . /var/www/html

RUN rm -rf \
        node_modules \
        tests \
        .git \
        .github \
        .vscode \
        .idea \
        docker

COPY --from=composer-deps --chown=www-data:www-data /app/vendor /var/www/html/vendor
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
RUN composer dump-autoload --optimize --classmap-authoritative --no-dev

COPY docker/php/php.ini   /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/php/www.conf  /usr/local/etc/php-fpm.d/zz-app.conf

RUN mkdir -p storage/framework/{cache,sessions,views,testing} \
             storage/framework/cache/data \
             storage/logs \
             bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache

USER www-data
EXPOSE 9000
CMD ["php-fpm", "-F", "-R"]
