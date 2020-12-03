FROM php:7.2-apache
RUN a2enmod rewrite
COPY apache/manawa.conf /etc/apache2/sites-available/manawa.conf
COPY php/php.ini /usr/local/etc/php/conf.d/customizations.ini
RUN a2ensite manawa
ENV PORT 8080
ENTRYPOINT []
CMD ["php", "-S", "0.0.0.0:8080", "-t", "/sites"]