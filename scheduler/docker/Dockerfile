FROM php:8-cli

ARG APP_USER="user"
RUN set -x && useradd -u 1000 -m -s /bin/bash ${APP_USER}
RUN usermod -aG sudo ${APP_USER}
ENV APP_USER=${APP_USER}

RUN apt-get update && apt-get install -y wget unzip curl cron sudo

RUN docker-php-ext-install -j$(nproc) opcache pdo_mysql

COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

RUN echo '%sudo ALL=(ALL) NOPASSWD:ALL' >> /etc/sudoers

ENV TERM=xterm-color

WORKDIR /var/www/

ENTRYPOINT ["bash", "-c", "sudo crontab /etc/cron.d/crontab && sudo -S cron -f"]