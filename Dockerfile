FROM php:8.2-apache

RUN apt-get update && apt-get install -y libpq-dev git unzip     && docker-php-ext-install pdo_pgsql

RUN a2enmod rewrite

COPY . /var/www/html/
WORKDIR /var/www/html/

RUN mkdir -p /var/www/html/public/uploads/questions && chown -R www-data:www-data /var/www/html/public/uploads && chmod -R 755 /var/www/html/public/uploads

# Set Apache document root to /public
RUN sed -i 's|/var/www/html|/var/www/html/public|' /etc/apache2/sites-available/000-default.conf

EXPOSE 80
CMD ["apache2-foreground"]
