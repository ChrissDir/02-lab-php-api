<?php
namespace App\controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once 'models/Category.php';

class CategoryController {
    private $db;
    private $category;

    public function __construct($db) {
        $this->db = $db;
        $this->category = new Category($db);
    }

    public function read(Request $request, Response $response, array $args): Response {
        $result = $this->category->read();
        $num = $result->rowCount();

        if($num > 0) {
            $categories_arr = array();
            while($row = $result->fetch(PDO::FETCH_ASSOC)) {
                array_push($categories_arr, $row);
            }
            $response->getBody()->write(json_encode($categories_arr));
        } else {
            $response->getBody()->write(json_encode(array("message" => "Aucune catégorie trouvée.")));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $request, Response $response, array $args): Response {
        $data = $request->getParsedBody();

        if(empty($data['name'])) {
            $response->getBody()->write(json_encode(array("message" => "Les données fournies sont incomplètes.")));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $this->category->name = $data['name'];

        if($this->category->create()) {
            $response->getBody()->write(json_encode(array("message" => "Catégorie créée.")));
        } else {
            $response->getBody()->write(json_encode(array("message" => "Échec de la création de la catégorie.")));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, array $args): Response {
        $id = $args['id'];
        $data = $request->getParsedBody();

        if(empty($data['name'])) {
            $response->getBody()->write(json_encode(array("message" => "Les données fournies sont incomplètes.")));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $this->category->id = $id;
        $this->category->name = $data['name'];

        if($this->category->update()) {
            $response->getBody()->write(json_encode(array("message" => "Catégorie mise à jour.")));
        } else {
            $response->getBody()->write(json_encode(array("message" => "Échec de la mise à jour de la catégorie.")));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $request, Response $response, array $args): Response {
        $id = $args['id'];

        $this->category->id = $id;

        if($this->category->delete()) {
            $response->getBody()->write(json_encode(array("message" => "Catégorie supprimée.")));
        } else {
            $response->getBody()->write(json_encode(array("message" => "Échec de la suppression de la catégorie.")));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}
?>
