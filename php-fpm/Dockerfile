FROM php:7.0-fpm

RUN apt-get update && apt-get install -y \
        libmcrypt-dev \
	libcurl4-openssl-dev \
	libssl-dev \
	libicu-dev
RUN docker-php-ext-install intl mcrypt curl mysqli pdo_mysql mbstring
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

RUN rm -f /usr/local/etc/php-fpm.conf
RUN rm -rf /usr/local/etc/php-fpm.d

COPY start.sh /etc/init.d/start.sh
COPY conf/php-fpm.conf /usr/local/etc/
COPY conf/php-fpm.d /usr/local/etc/php-fpm.d

RUN echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_port=9009" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_autostart=off" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_connect_back=on" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_handler=dbgp" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
