# Utilisez l'image officielle de PHP avec Apache
FROM php:apache-bullseye

# Installez les extensions PHP nécessaires et Composer
RUN docker-php-ext-install pdo pdo_mysql

# Copiez votre code source dans le conteneur
COPY src/ /var/www/html/

# Activez la réécriture d'URL pour Apache
RUN a2enmod rewrite
RUN apt update &&\
    apt install git &&\
    composer install
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('sha384', 'composer-setup.php') === 'e21205b207c3ff031906575712edab6f13eb0b361f2085f1f1237b7126d785e826a450292b6cfd1d64d92e6563bbde02') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html