<?php
namespace App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Models\Resource;
use Exception;

class ResourceController {
    private $resource;

    public function __construct($db) {
        $this->resource = new Resource($db);
    }

    private function getData(Request $request) {
        $data = $request->getParsedBody();
        return $data;
    }

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
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(array("error" => "Une erreur est survenue.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function create(Request $request, Response $response, array $args): Response {
        $data = $this->getData($request);

        $this->resource->name = htmlspecialchars($data['name'] ?? "");
        $this->resource->url = htmlspecialchars($data['url'] ?? "");

        if (!isset($data['technology_id']) || !is_numeric($data['technology_id'])) {
            $response->getBody()->write(json_encode(array("error" => "technology_id is required and must be a number.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            if($this->resource->create()) {
                $this->resource->addTechnologyResourceRelationship((int) $data['technology_id']);
                $response->getBody()->write(json_encode(array("message" => "Ressource créée.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
            } else {
                $response->getBody()->write(json_encode(array("message" => "Échec de la création de la ressource.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(array("error" => "Une erreur est survenue.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function update(Request $request, Response $response, array $args): Response {
        $id = (int) $args['id'];
        $data = $this->getData($request);

        $this->resource->id = $id;
        $this->resource->name = htmlspecialchars($data['name'] ?? "");
        $this->resource->url = htmlspecialchars($data['url'] ?? "");
        $this->resource->technology_id = is_numeric($data['technology_id']) ? (int) $data['technology_id'] : null;

        try {
            if($this->resource->update()) {
                $response->getBody()->write(json_encode(array("message" => "Ressource mise à jour.")));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode(array("message" => "Échec de la mise à jour de la ressource.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(array("error" => "Une erreur est survenue.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function delete(Request $request, Response $response, array $args): Response {
        $id = (int) ($args['id'] ?? null);
        if (!$id) {
            $response->getBody()->write(json_encode(array("message" => "ID invalide.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $this->resource->id = $id;

        try {
            if($this->resource->delete()) {
                $response->getBody()->write(json_encode(array("message" => "Ressource supprimée.")));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode(array("message" => "Échec de la suppression de la ressource.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(array("error" => "Une erreur est survenue.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }   
}
?>