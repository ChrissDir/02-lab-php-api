<?php
namespace App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Models\Technology;
use PDO;
use PDOException;

class TechnologyController {
    private $db;
    private $technology;

    public function __construct($db) {
        $this->db = $db;
        $this->technology = new Technology($db);
    }

    public function read(Request $request, Response $response, array $args): Response {
        $page = $args['page'] ?? 1;
        $items_per_page = $args['items_per_page'] ?? 10;
        $offset = ($page - 1) * $items_per_page;

        try {
            $result = $this->technology->read($offset, $items_per_page);
            $num = $result->rowCount();

            if($num > 0) {
                $technologies_arr = array();
                while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    array_push($technologies_arr, $row);
                }
                $response->getBody()->write(json_encode($technologies_arr));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode(array("message" => "Aucune technologie trouvée.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function create(Request $request, Response $response, array $args): Response {
        $data = $request->getParsedBody();

        if(empty($data['name']) || empty($data['category_id'])) {
            $response->getBody()->write(json_encode(array("message" => "Les données fournies sont incomplètes.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $this->technology->name = $data['name'];
        $this->technology->logo = $data['logo'];  // Assuming logo can be empty
        $this->technology->category_id = $data['category_id'];

        try {
            if($this->technology->create()) {
                $response->getBody()->write(json_encode(array("message" => "Technologie créée.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
            } else {
                $response->getBody()->write(json_encode(array("message" => "Échec de la création de la technologie.")));
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

        if(empty($data['name']) && empty($data['logo']) && empty($data['category_id'])) {
            $response->getBody()->write(json_encode(array("message" => "Aucune donnée fournie pour la mise à jour.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $this->technology->id = $id;
        if(!empty($data['name'])) $this->technology->name = $data['name'];
        if(!empty($data['logo'])) $this->technology->logo = $data['logo'];
        if(!empty($data['category_id'])) $this->technology->category_id = $data['category_id'];

        try {
            if($this->technology->update()) {
                $response->getBody()->write(json_encode(array("message" => "Technologie mise à jour.")));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode(array("message" => "Échec de la mise à jour de la technologie.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function delete(Request $request, Response $response, array $args): Response {
        $id = $args['id'];

        $this->technology->id = $id;

        try {
            if($this->technology->delete()) {
                $response->getBody()->write(json_encode(array("message" => "Technologie supprimée.")));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode(array("message" => "Échec de la suppression de la technologie.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
?>
