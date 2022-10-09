FROM php:8.1-fpm-alpine

ARG user
ARG uid
ARG PUID=1000
#ENV PUID ${PUID}
ARG PGID=1000
#ENV PGID ${PGID}

# persistent / runtime deps
RUN apk add --no-cache \
		acl \
		file \
		gettext \
		git \
		openssl \
		$PHPIZE_DEPS \
		zlib-dev \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev \
        libpq-dev \
    ;

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-install pdo pdo_pgsql #pgsql zip pcntl
#RUN docker-php-ext-install pdo_mysql && docker-php-ext-enable pdo_mysql
RUN docker-php-ext-install exif && docker-php-ext-enable exif
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd
#RUN pecl install redis && docker-php-ext-enable redis
RUN docker-php-ext-enable opcache

#RUN pecl install xdebug
#RUN docker-php-ext-enable xdebug

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY docker/laravel/php.ini /usr/local/etc/php/php.ini
#COPY docker/laravel/docker-php-ext-xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini


# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1

# copy init script
COPY docker/laravel/init.sh /usr/local/bin/init
RUN chmod +x /usr/local/bin/init
COPY docker/laravel/seed.sh /usr/local/bin/seed
RUN chmod +x /usr/local/bin/seed
#
RUN addgroup -S -g "$PGID" sebi0815 && adduser -S -u "$PUID" $user -G sebi0815

#
#USER user
#
#
#WORKDIR /srv/api
#
#CMD ["php-fpm"]
#
#EXPOSE 9000

# Install system dependencies
#RUN apt-get update && apt-get install -y \
#    git \
#    curl \
#    libpng-dev \
#    libonig-dev \
#    libxml2-dev \
#    zip \
#    unzip
#
## Clear cache
#RUN apt-get clean && rm -rf /var/lib/apt/lists/*
#
## Install PHP extensions
#RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd
#
## Get latest Composer
#COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
#
# Create system user to run Composer and Artisan Commands
#RUN adduser -G www-data,root -u $uid  $user
#RUN mkdir -p /home/$user/.composer && \
#    chown -R $user:$user /home/$user

# Set working directory
WORKDIR /var/www

USER $user
