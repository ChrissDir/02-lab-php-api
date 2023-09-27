# Utilisez l'image officielle de PHP avec Apache
FROM php:apache-bullseye

# Installez les extensions PHP nécessaires et Composer
RUN docker-php-ext-install pdo pdo_mysql

# Copiez votre code source dans le conteneur
COPY src/ /var/www/html/

# Activez la réécriture d'URL pour Apache
RUN a2enmod rewrite

# Mettez à jour la liste des paquets, installez Git, et installez Composer
RUN apt-get update && \
    apt-get install -y git && \
    rm -rf /var/lib/apt/lists/* && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Changez le propriétaire et les permissions du répertoire
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Installez les dépendances via Composer
WORKDIR /var/www/html
RUN composer install
