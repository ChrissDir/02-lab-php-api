<?php
namespace App\controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once 'models/Resource.php';

class ResourceController {
    private $db;
    private $resource;

    public function __construct($db) {
        $this->db = $db;
        $this->resource = new Resource($db);
    }

    public function read(Request $request, Response $response, array $args): Response {
        $result = $this->resource->read();
        $num = $result->rowCount();

        if($num > 0) {
            $resources_arr = array();
            while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                array_push($resources_arr, $row);
            }
            $response->getBody()->write(json_encode($resources_arr));
        } else {
            $response->getBody()->write(json_encode(array("message" => "Aucune ressource trouvée.")));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response, array $args): Response {
        $data = $request->getParsedBody();

        if(empty($data['name']) || empty($data['url']) || empty($data['technology_id'])) {
            $response->getBody()->write(json_encode(array("message" => "Les données fournies sont incomplètes.")));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $this->resource->name = $data['name'];
        $this->resource->url = $data['url'];
        $this->resource->technology_id = $data['technology_id'];

        if($this->resource->create()) {
            $response->getBody()->write(json_encode(array("message" => "Ressource créée.")));
        } else {
            $response->getBody()->write(json_encode(array("message" => "Échec de la création de la ressource.")));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, array $args): Response {
        $id = $args['id'];
        $data = $request->getParsedBody();

        if(empty($data['name']) || empty($data['url']) || empty($data['technology_id'])) {
            $response->getBody()->write(json_encode(array("message" => "Les données fournies sont incomplètes.")));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $this->resource->id = $id;
        $this->resource->name = $data['name'];
        $this->resource->url = $data['url'];
        $this->resource->technology_id = $data['technology_id'];

        if($this->resource->update()) {
            $response->getBody()->write(json_encode(array("message" => "Ressource mise à jour.")));
        } else {
            $response->getBody()->write(json_encode(array("message" => "Échec de la mise à jour de la ressource.")));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response, array $args): Response {
        $id = $args['id'];

        $this->resource->id = $id;

        if($this->resource->delete()) {
            $response->getBody()->write(json_encode(array("message" => "Ressource supprimée.")));
        } else {
            $response->getBody()->write(json_encode(array("message" => "Échec de la suppression de la ressource.")));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}
?>

