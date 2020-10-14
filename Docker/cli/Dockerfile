FROM php:7.4-cli

ARG USER_ID
ARG GROUP_ID

RUN export DEBIAN_FRONTEND=noninteractive \
    && apt-get update && apt-get -y upgrade \
    && apt-get -y --no-install-recommends install apt-utils unzip git

RUN addgroup --gid $GROUP_ID app
RUN adduser --disabled-password --gecos '' --uid $USER_ID --gid $GROUP_ID app

ADD https://raw.githubusercontent.com/mlocati/docker-php-extension-installer/master/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/bin/composer \
    && mkdir /composer_home \
    && chown $USER_ID:$GROUP_ID /composer_home

ENV COMPOSER_HOME /composer_home
ENV COMPOSER_DISCARD_CHANGES true

USER root
RUN install-php-extensions pdo_pgsql
USER app


