ARG PHP_VERSION=8.0
FROM php:$PHP_VERSION-cli-alpine

RUN apk add git zip unzip autoconf make g++

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

WORKDIR /package

COPY composer.json ./

RUN composer install

COPY src ./src
COPY tests ./tests
COPY ecs.php phpstan.neon phpunit.xml ./

RUN vendor/bin/parallel-lint --no-colors --no-progress src tests
RUN vendor/bin/ecs -n --no-progress-bar
RUN vendor/bin/phpunit --colors="never"
RUN vendor/bin/phpstan analyse --no-ansi --no-progress --memory-limit 2G
