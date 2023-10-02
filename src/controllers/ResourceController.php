<?php
// ------------------------------
// Namespace et Importations
// ------------------------------
namespace App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Models\Resource;
use Exception;  // Importation de la classe Exception

// ------------------------------
// Contrôleur de Ressource
// ------------------------------
class ResourceController {
    private $resource;

    // ------------------------------
    // Constructeur
    // ------------------------------
    public function __construct($db) {
        $this->resource = new Resource($db);
    }

    // ------------------------------
    // Fonction pour extraire les données de la requête
    // ------------------------------
    private function getData(Request $request) {
        $data = $request->getParsedBody();
        if (empty($data)) {
            $data = $request->getParsedBody();
        }
        return $data;
    }

    // ------------------------------
    // Lire les ressources
    // ------------------------------
    public function read(Request $request, Response $response, array $args): Response {
        try {
            $resources_arr = $this->resource->read();
            if(!empty($resources_arr)) {
                $response->getBody()->write(json_encode($resources_arr));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $data = array('error' => 'Aucune ressource trouvée'); 
                $response->getBody()->write(json_encode($data));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
        } catch (Exception $e) {  // Ajout de | Exception
            $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    // ------------------------------
    // Créer une ressource
    // ------------------------------
    public function create(Request $request, Response $response, array $args): Response {
        $data = $this->getData($request);

        $this->resource->name = $data['name'] ?? null;
        $this->resource->url = $data['url'] ?? null;
        $this->resource->technology_id = $data['technology_id'] ?? null;

        try {
            if($this->resource->create()) {
                $response->getBody()->write(json_encode(array("message" => "Ressource créée.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
            } else {
                $response->getBody()->write(json_encode(array("message" => "Échec de la création de la ressource.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (Exception $e) {  // Ajout de | Exception
            $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    // ------------------------------
    // Mettre à jour une ressource
    // ------------------------------
    public function update(Request $request, Response $response, array $args): Response {
        $id = $args['id'];
        $data = $request->getParsedBody();

        $this->resource->id = $id;
        $this->resource->name = $data['name'] ?? null;
        $this->resource->url = $data['url'] ?? null;
        $this->resource->technology_id = $data['technology_id'] ?? null;

        try {
            if($this->resource->update()) {
                $response->getBody()->write(json_encode(array("message" => "Ressource mise à jour.")));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode(array("message" => "Échec de la mise à jour de la ressource.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (Exception $e) {  // Ajout de | Exception
            $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    // ------------------------------
    // Supprimer une ressource
    // ------------------------------
    public function delete(Request $request, Response $response, array $args): Response {
        $data = $this->getData($request);
        $id = $args['id'] ?? $data['id'];  // Permet de supprimer par ID dans l'URL ou dans le corps de la requête

        $this->resource->id = $id;

        try {
            if($this->resource->delete()) {
                $response->getBody()->write(json_encode(array("message" => "Ressource supprimée.")));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode(array("message" => "Échec de la suppression de la ressource.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (Exception $e) {  // Ajout de | Exception
            $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
?>