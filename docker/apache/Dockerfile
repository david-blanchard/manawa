FROM php:7.4-apache
RUN apt update -y; apt upgrade -y; apt install apt-utils -y
RUN pecl install redis \
    && pecl install xdebug \
    && docker-php-ext-enable redis xdebug
RUN a2enmod rewrite
COPY apache/manawa.conf /etc/apache2/sites-available/manawa.conf
COPY php/php.ini /usr/local/etc/php/conf.d/customizations.ini
RUN a2ensite manawa
ENV PORT 8080
ENTRYPOINT []
CMD sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf && docker-php-entrypoint apache2-foreground

