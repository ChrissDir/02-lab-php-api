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
                return $response->withJson($technologies_arr, 200);
            } else {
                return $response->withJson(["message" => "Aucune technologie trouvée."], 404);
            }
        } catch (PDOException $e) {
            return $response->withJson(["error" => $e->getMessage()], 500);
        }
    }

    public function create(Request $request, Response $response, array $args): Response {
        $data = $request->getParsedBody();

        if(empty($data['name']) || empty($data['category_id'])) {
            return $response->withJson(["message" => "Les données fournies sont incomplètes."], 400);
        }

        $this->technology->name = $data['name'];
        $this->technology->logo = $data['logo'];
        $this->technology->category_id = $data['category_id'];

        try {
            if($this->technology->create()) {
                return $response->withJson(["message" => "Technologie créée."], 201);
            } else {
                return $response->withJson(["message" => "Échec de la création de la technologie."], 500);
            }
        } catch (PDOException $e) {
            return $response->withJson(["error" => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Response $response, array $args): Response {
        $id = $args['id'];
        $data = $request->getParsedBody();

        if(empty($data['name']) && empty($data['logo']) && empty($data['category_id'])) {
            return $response->withJson(["message" => "Aucune donnée fournie pour la mise à jour."], 400);
        }

        $this->technology->id = $id;
        if(!empty($data['name'])) $this->technology->name = $data['name'];
        if(!empty($data['logo'])) $this->technology->logo = $data['logo'];
        if(!empty($data['category_id'])) $this->technology->category_id = $data['category_id'];

        try {
            if($this->technology->update()) {
                return $response->withJson(["message" => "Technologie mise à jour."], 200);
            } else {
                return $response->withJson(["message" => "Échec de la mise à jour de la technologie."], 500);
            }
        } catch (PDOException $e) {
            return $response->withJson(["error" => $e->getMessage()], 500);
        }
    }

    public function delete(Request $request, Response $response, array $args): Response {
        $id = $args['id'];

        $this->technology->id = $id;

        try {
            if($this->technology->delete()) {
                return $response->withJson(["message" => "Technologie supprimée."], 200);
            } else {
                return $response->withJson(["message" => "Échec de la suppression de la technologie."], 500);
            }
        } catch (PDOException $e) {
            return $response->withJson(["error" => $e->getMessage()], 500);
        }
    }
}
?>