<?php
namespace App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Models\Category;
use PDO;
use PDOException;

class CategoryController {
    private $db;
    private $category;

    public function __construct($db) {
        $this->db = $db;
        $this->category = new Category($db);
    }

    public function read(Request $request, Response $response, array $args): Response {
        try {
            $result = $this->category->read();
            $num = $result->rowCount();

            if($num > 0) {
                $categories_arr = array();
                while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                    array_push($categories_arr, $row);
                }
                $response->getBody()->write(json_encode($categories_arr));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $data = array('error' => 'Aucune catégorie trouvée');  // Modifié le message d'erreur
                return $response -> withJson($data, 404);  // Ajouté le code d'état 404
            }
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function create(Request $request, Response $response, array $args): Response {
        $data = $request->getParsedBody();

        if(empty($data['name'])) {
            $response->getBody()->write(json_encode(array("message" => "Les données fournies sont incomplètes.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $this->category->name = $data['name'];

        try {
            if($this->category->create()) {
                $response->getBody()->write(json_encode(array("message" => "Catégorie créée.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
            } else {
                $response->getBody()->write(json_encode(array("message" => "Échec de la création de la catégorie.")));
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

        if(empty($data['name'])) {
            $response->getBody()->write(json_encode(array("message" => "Les données fournies sont incomplètes.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $this->category->id = $id;
        $this->category->name = $data['name'];

        try {
            if($this->category->update()) {
                $response->getBody()->write(json_encode(array("message" => "Catégorie mise à jour.")));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode(array("message" => "Échec de la mise à jour de la catégorie.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    public function delete(Request $request, Response $response, array $args): Response {
        $id = $args['id'];

        $this->category->id = $id;

        try {
            if($this->category->delete()) {
                $response->getBody()->write(json_encode(array("message" => "Catégorie supprimée.")));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode(array("message" => "Échec de la suppression de la catégorie.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (PDOException $e) {
            $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
?>
