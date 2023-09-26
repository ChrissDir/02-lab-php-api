# Utilisez l'image officielle de PHP avec Apache
FROM php:apache-bullseye

# Installez les extensions PHP nécessaires et Composer
RUN docker-php-ext-install pdo pdo_mysql

# Copiez votre code source dans le conteneur
COPY src/ /var/www/html/

# Activez la réécriture d'URL pour Apache
RUN a2enmod rewrite

# Mettez à jour la liste des paquets et installez Git
RUN apt-get update && \
    apt-get install -y git

# Installez Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php -r "if (hash_file('sha384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
    php composer-setup.php && \
    php -r "unlink('composer-setup.php');" && \
    mv composer.phar /usr/local/bin/composer

# Changez le propriétaire et les permissions du répertoire
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Installez les dépendances via Composer
WORKDIR /var/www/html
RUN composer install
