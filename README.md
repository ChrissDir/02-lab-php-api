# 02-lab-php-api

Ce projet est une API REST développée en PHP en utilisant le framework Slim 4, permettant de gérer des technologies, des catégories et des ressources dans le domaine du développement web.

## Installation

1. Clonez ce dépôt Git.
2. Assurez-vous d'avoir Docker installé sur votre machine.
3. Exécutez `docker compose up` à la racine du projet pour démarrer les conteneurs Docker.
4. Une fois que Docker est en cours d'exécution, l'API est accessible et prête à être utilisée. Toute la logique du projet se trouve dans le répertoire src/.

## API Endpoints

### Technologies

- **Lister toutes les technologies** :
  - Méthode : `GET`
  - URL : `/technologies`

- **Créer une nouvelle technologie** :
  - Méthode : `POST`
  - URL : `/technologies`
  - Paramètres : `name`, `logo`, `category_id`

- **Mettre à jour une technologie** :
  - Méthode : `PUT`
  - URL : `/technologies/{id}`
  - Paramètres : `name`, `logo`(en base64), `category_id`

- **Supprimer une technologie** :
  - Méthode : `DELETE`
  - URL : `/technologies/{id}`


- **Lister toutes les ressources d'une technologie** :
  - Méthode : `GET`
  - URL : `/technologies/{id}/ressources`

- **Ajouter une ressource à une technologie** :
  - Méthode : `POST`
  - URL : `/technologies/{id}/ressources`
  - Paramètres : `resource_id`

- **Supprimer une ressource d'une technologie** :
  - Méthode : `DELETE`
  - URL : `/technologies/{id}/ressources/{resourceId}`


- **Lister toutes les catégories d'une technologie** :
  - Méthode : `GET`
  - URL : `/technologies/{id}/categories`

- **Ajouter une catégorie à une technologie** :
  - Méthode : `POST`
  - URL : `/technologies/{id}/categories`
  - Paramètres : `category_id`

- **Supprimer une catégorie d'une technologie** :
  - Méthode : `DELETE`
  - URL : `/technologies/{id}/categories/{categoryId}`

### Catégories

- **Lister toutes les catégories** :
  - Méthode : `GET`
  - URL : `/categories`

- **Créer une nouvelle catégorie** :
  - Méthode : `POST`
  - URL : `/categories`
  - Paramètres : `name`

- **Mettre à jour une catégorie** :
  - Méthode : `PUT`
  - URL : `/categories/{id}`
  - Paramètres : `name`

- **Supprimer une catégorie** :
  - Méthode : `DELETE`
  - URL : `/categories/{id}`

### Ressources

- **Lister toutes les ressources** :
  - Méthode : `GET`
  - URL : `/ressources`

- **Créer une nouvelle ressource** :
  - Méthode : `POST`
  - URL : `/ressources`
  - Paramètres : `name`, `url`, `technology_id`

- **Mettre à jour une ressource** :
  - Méthode : `PUT`
  - URL : `/ressources/{id}`
  - Paramètres : `name`, `url`, `technology_id`

- **Supprimer une ressource** :
  - Méthode : `DELETE`
  - URL : `/ressources/{id}`

## Tests

Utilisez Postman ou un autre outil de test d'API pour tester les endpoints de l'API.

## Déploiement

Le projet est déployé sur un serveur avec Docker, accessible via le domaine `php-dev-2.online` ou `localhost`.