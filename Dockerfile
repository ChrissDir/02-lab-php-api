# Utiliser l'image officielle de PHP avec Apache
FROM php:apache-bullseye

# Installer les extensions PHP nécessaires et Composer
RUN docker-php-ext-install pdo pdo_mysql

# Copier le code source dans le conteneur
COPY src/ /var/www/html/

# Activer la réécriture d'URL pour Apache
RUN a2enmod rewrite

# Mettre à jour la liste des paquets, installer Git, et installer Composer
RUN apt-get update && \
    apt-get install -y git && \
    rm -rf /var/lib/apt/lists/* && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Changer le propriétaire et les permissions du répertoire
RUN mkdir -p /var/www/html/src/Logos && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    chown -R www-data:www-data /var/www/html/src/Logos && \
    chmod -R 755 /var/www/html/src/Logos

# Installer les dépendances via Composer
WORKDIR /var/www/html
RUN composer install
