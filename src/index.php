<?php
require 'vendor/autoload.php';
require 'config.php';

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\TechnologyController;
use App\CategoryController;
use App\ResourceController;
use Slim\Middleware\ErrorMiddleware;

function logMessage($message) {
    $logFile = fopen('app.log', 'a');  // Ouvre le fichier app.log en mode append
    fwrite($logFile, date('Y-m-d H:i:s') . " - " . $message . "\n");  // Écrit le message dans le fichier de log
    fclose($logFile);  // Ferme le fichier de log
}

$app = AppFactory::create();

// Configuration de la base de données
$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Injection des dépendances dans les contrôleurs
$technologyController = new TechnologyController($db);
$categoryController = new CategoryController($db);
$resourceController = new ResourceController($db);

// Définition des routes
// Routes pour TechnologyController
$app->get('/technologies', [$technologyController, 'read']);
$app->post('/technologies', [$technologyController, 'create']);
$app->put('/technologies/{id}', [$technologyController, 'update']);
$app->delete('/technologies/{id}', [$technologyController, 'delete']);

// Routes pour CategoryController
$app->get('/categories', [$categoryController, 'read']);
$app->post('/categories', [$categoryController, 'create']);
$app->put('/categories/{id}', [$categoryController, 'update']);
$app->delete('/categories/{id}', [$categoryController, 'delete']);

// Routes pour ResourceController
$app->get('/resources', [$resourceController, 'read']);
$app->post('/resources', [$resourceController, 'create']);
$app->put('/resources/{id}', [$resourceController, 'update']);
$app->delete('/resources/{id}', [$resourceController, 'delete']);

// Gestionnaire d'erreurs personnalisé
$customErrorHandler = function (
    Request $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails
) use ($app) {
    logMessage($exception->getMessage());  // Log l'erreur
    $payload = ['error' => $exception->getMessage()];
    $response = $app->getResponseFactory()->createResponse();
    $response->getBody()->write(json_encode($payload, JSON_UNESCAPED_UNICODE));
    return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
};

// Ajout du middleware d'erreur
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler($customErrorHandler);

// Middleware pour gérer les headers CORS
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});

$app->run();
?>