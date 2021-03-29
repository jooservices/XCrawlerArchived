ARG PHP_VERSION

FROM php:7.4-fpm

ARG PHP_VERSION

# Set Environment Variables
ENV DEBIAN_FRONTEND noninteractive

ENV COMPOSER_ALLOW_SUPERUSER 1

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && chmod +x /usr/local/bin/composer

# always run apt update when start and after add new source list, then clean up at end.
RUN set -xe; \
    apt-get update -yqq && \
    pecl channel-update pecl.php.net && \
    apt-get install -yqq \
      apt-utils \
      libzip-dev zip unzip && \
      docker-php-ext-configure zip && \
      # Install the zip extension
      docker-php-ext-install zip && \
      php -m | grep -q 'zip'


# Install the soap extension
RUN rm /etc/apt/preferences.d/no-debian-php && \
        apt-get -y install libxml2-dev php-soap && \
        docker-php-ext-install soap

# Install the xsl extension
RUN apt-get -y install libxslt-dev && docker-php-ext-install xsl

# Install the xdebug extension
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Copy xdebug configuration for remote debugging
COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

RUN sed -i "s/xdebug.remote_autostart=0/xdebug.remote_autostart=1/" /usr/local/etc/php/conf.d/xdebug.ini && \
    sed -i "s/xdebug.remote_enable=0/xdebug.remote_enable=1/" /usr/local/etc/php/conf.d/xdebug.ini && \
    sed -i "s/xdebug.cli_color=0/xdebug.cli_color=1/" /usr/local/etc/php/conf.d/xdebug.ini

# Install the bcmath extension
RUN docker-php-ext-install bcmath

# Install intl and requirements
RUN apt-get install -y zlib1g-dev libicu-dev g++ && \
        docker-php-ext-configure intl && \
        docker-php-ext-install intl

# Install dependencies
RUN apt-get update \
  && apt-get install -y \
    apt-utils \
    libfreetype6-dev \
    libicu-dev \
    libjpeg62-turbo-dev \
    libmcrypt-dev \
    libpng-dev \
    libjpeg-dev \
    libpq-dev \
    libxslt-dev \
    sudo \
    default-mysql-client \
    git \
    curl \
    libzip-dev \
    libonig-dev \
    libcurl4-nss-dev \
    libxml2-dev

# Install MySQL Client
RUN docker-php-ext-install iconv \
    && docker-php-ext-install curl \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install pdo \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install json \
    && docker-php-ext-install soap \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-configure gd \
    && docker-php-ext-install gd

RUN set -xe; php -v | head -n 1 | grep -q "PHP ${PHP_VERSION}."

# Install NodeJS
RUN apt-get install -y nodejs
# confirm that it was successful
RUN node -v

# Install NPM
RUN apt install -y npm
# npm installs automatically
RUN npm -v

# Install supervisor
RUN apt-get update && apt-get install -y supervisor

COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

###########################################################################
# Crontab
###########################################################################
RUN apt-get update && apt-get -y install -qq cron

USER root

COPY ./crontab /etc/cron.d

RUN chmod -R 644 /etc/cron.d

RUN crontab /etc/cron.d/veso

RUN touch /var/log/cron.log

USER root

# Clean up
RUN apt-get clean && \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* && \
    rm /var/log/lastlog /var/log/faillog

# Configure non-root user.

RUN groupmod -o -g 1000 www-data && \
    usermod -o -u 1000 -g www-data www-data

WORKDIR /var/www

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

EXPOSE 9000
