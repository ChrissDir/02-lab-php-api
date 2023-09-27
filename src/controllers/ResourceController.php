<?php
namespace App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Models\Resource;
use PDO;
use PDOException;

class ResourceController {
    private $db;
    private $resource;

    public function __construct($db) {
        $this->db = $db;
        $this->resource = new Resource($db);
    }

    public function read(Request $request, Response $response, array $args): Response {
        try {
            $result = $this->resource->read();
            $num = $result->rowCount();

            if($num > 0) {
                $resources_arr = array();
                while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    array_push($resources_arr, $row);
                }
                $response->getBody()->write(json_encode($resources_arr));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode(array("message" => "Aucune ressource trouvée.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function create(Request $request, Response $response, array $args): Response {
        $data = $request->getParsedBody();

        if(empty($data['name']) || empty($data['url']) || empty($data['technology_id'])) {
            $response->getBody()->write(json_encode(array("message" => "Les données fournies sont incomplètes.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $this->resource->name = $data['name'];
        $this->resource->url = $data['url'];
        $this->resource->technology_id = $data['technology_id'];

        try {
            if($this->resource->create()) {
                $response->getBody()->write(json_encode(array("message" => "Ressource créée.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
            } else {
                $response->getBody()->write(json_encode(array("message" => "Échec de la création de la ressource.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function update(Request $request, Response $response, array $args): Response {
        $id = $args['id'];
        $data = $request->getParsedBody();

        if(empty($data['name']) && empty($data['url']) && empty($data['technology_id'])) {  // Modifié pour permettre des mises à jour partielles
            $response->getBody()->write(json_encode(array("message" => "Aucune donnée fournie pour la mise à jour.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $this->resource->id = $id;
        if(!empty($data['name'])) $this->resource->name = $data['name'];  // Permet des mises à jour partielles
        if(!empty($data['url'])) $this->resource->url = $data['url'];  // Permet des mises à jour partielles
        if(!empty($data['technology_id'])) $this->resource->technology_id = $data['technology_id'];  // Permet des mises à jour partielles

        try {
            if($this->resource->update()) {
                $response->getBody()->write(json_encode(array("message" => "Ressource mise à jour.")));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode(array("message" => "Échec de la mise à jour de la ressource.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function delete(Request $request, Response $response, array $args): Response {
        $id = $args['id'];

        $this->resource->id = $id;

        try {
            if($this->resource->delete()) {
                $response->getBody()->write(json_encode(array("message" => "Ressource supprimée.")));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode(array("message" => "Échec de la suppression de la ressource.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
?>
