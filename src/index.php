<?php
// ------------------------------
// Importation des dépendances et configuration
// ------------------------------
require 'vendor/autoload.php';
require 'config.php';

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// ------------------------------
// Création de l'application Slim
// ------------------------------
$app = AppFactory::create();

// ------------------------------
// Configuration de la connexion à la base de données
// ------------------------------
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Ajout du middleware pour analyser le corps de la requête
$app->addBodyParsingMiddleware();

// ------------------------------
// Middleware pour gérer les headers CORS
// ------------------------------
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

// ------------------------------
// Définition des routes pour TechnologyController
// ------------------------------
$app->group('/technologies', function ($group) use ($db) {
    $controller = new \App\TechnologyController($db);
    $group->get('', [$controller, 'read']);
    $group->post('', [$controller, 'create']);
    $group->post('/{id}', [$controller, 'update']);
    $group->delete('/{id}', [$controller, 'delete']);
});

// ------------------------------
// Définition des routes pour CategoryController
// ------------------------------
$app->group('/categories', function ($group) use ($db) {
    $controller = new \App\CategoryController($db);
    $group->get('', [$controller, 'read']);
    $group->post('', [$controller, 'create']);
    $group->post('/{id}', [$controller, 'update']);
    $group->delete('/{id}', [$controller, 'delete']);
});

// ------------------------------
// Définition des routes pour ResourceController
// ------------------------------
$app->group('/ressources', function ($group) use ($db) {
    $controller = new \App\ResourceController($db);
    $group->get('', [$controller, 'read']);
    $group->post('', [$controller, 'create']);
    $group->post('/{id}', [$controller, 'update']);
    $group->delete('/{id}', [$controller, 'delete']);
});
// ------------------------------
// Gestionnaire d'erreurs personnalisé
// ------------------------------
$customErrorHandler = function (
    Request $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails
) use ($app) {
    $payload = ['error' => $exception->getMessage()];
    $response = $app->getResponseFactory()->createResponse();
    $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_UNICODE));
    return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
};
// ------------------------------
// Ajout du middleware d'erreur
// ------------------------------
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler($customErrorHandler);

// ------------------------------
// Démarrage de l'application Slim
// ------------------------------
$app->run();
?>