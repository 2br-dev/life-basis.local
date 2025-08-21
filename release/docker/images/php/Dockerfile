FROM php:8.1-apache

RUN set -xe; \
    apt update; \
    apt install --yes \
        locales \
        locales-all \
        cron \
        zip unzip \
        mc \
        libonig-dev libzip-dev libpng-dev libjpeg-dev libgif-dev libwebp-dev libfreetype6-dev; \
    docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp; \
    docker-php-ext-install mysqli gd zip mbstring opcache; \
    docker-php-ext-enable gd; \
    update-locale;

RUN a2enmod rewrite;
WORKDIR /var/www/html
COPY cron /var/spool/cron/crontabs/root
RUN chmod 0600 /var/spool/cron/crontabs/root; \
    chown root:crontab /var/spool/cron/crontabs/root; \
    crontab /var/spool/cron/crontabs/root; \
    chown -R www-data:www-data /var/www/html;

CMD service cron restart && apache2-foreground