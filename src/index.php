<?php
require 'vendor/autoload.php';
require 'config.php';  // Assurez-vous que ce fichier est inclus en premier

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\TechnologyController;
use App\CategoryController;
use App\ResourceController;

$app = AppFactory::create();

$db = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME, DB_USER, DB_PASS);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$technologyController = new TechnologyController($db);
$categoryController = new CategoryController($db);
$resourceController = new ResourceController($db);

// Route pour obtenir toutes les technologies
$app->get('/technologies', function (Request $request, Response $response, $args) use ($technologyController) {
    return $technologyController->read($request, $response, $args);
});

// Route pour créer une nouvelle technologie
$app->post('/technologies', function (Request $request, Response $response, $args) use ($technologyController) {
    $data = $request->getParsedBody();
    return $technologyController->create($request, $response, $args);
});

// Route pour mettre à jour une technologie
$app->put('/technologies/{id}', function (Request $request, Response $response, $args) use ($technologyController) {
    $id = $args['id'];
    $data = $request->getParsedBody();
    return $technologyController->update($request, $response, $args);
});

// Route pour supprimer une technologie
$app->delete('/technologies/{id}', function (Request $request, Response $response, $args) use ($technologyController) {
    $id = $args['id'];
    return $technologyController->delete($request, $response, $args);
});

// Gestionnaire d'erreurs
$customErrorHandler = function (
    Request $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails
) use ($app) {
    $payload = [
        'error' => $exception->getMessage()
    ];
    $response = $app->getResponseFactory()->createResponse();
    $response->getBody()->write(
        json_encode($payload, JSON_UNESCAPED_UNICODE)
    );
    return $response->withStatus(500);
};

// Ajoutez le gestionnaire d'erreurs à l'application
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setDefaultErrorHandler($customErrorHandler);

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write(file_get_contents(__DIR__ . '/index.html'));
    return $response;
});

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
