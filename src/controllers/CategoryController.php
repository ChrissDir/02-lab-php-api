<?php
namespace App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Models\Category;
use PDO;
use Exception;

class CategoryController {
    private $db;
    private $category;

    public function __construct($db) {
        $this->db = $db;
        $this->category = new Category($db);
    }

    private function getData(Request $request) {
        $data = $request->getParsedBody();
        return $data;
    }

    public function read(Request $request, Response $response, array $args): Response {
        try {
            $categories_arr = $this->category->read();
            if(!empty($categories_arr)) {
                $response->getBody()->write(json_encode($categories_arr));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $data = array('error' => 'Aucune catégorie trouvée');
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
        if(empty($data['name']) || !is_string($data['name'])) {
            $response->getBody()->write(json_encode(array("message" => "Les données fournies sont incomplètes ou invalides.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $this->category->name = htmlspecialchars($data['name']);

        try {
            if($this->category->create()) {
                $response->getBody()->write(json_encode(array("message" => "Catégorie créée.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
            } else {
                $response->getBody()->write(json_encode(array("message" => "Échec de la création de la catégorie.")));
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
        if(empty($data['name']) || !is_string($data['name'])) {
            $response->getBody()->write(json_encode(array("message" => "Les données fournies sont incomplètes ou invalides.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $this->category->id = $id;
        $this->category->name = htmlspecialchars($data['name']);

        try {
            if($this->category->update()) {
                $response->getBody()->write(json_encode(array("message" => "Catégorie mise à jour.")));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode(array("message" => "Échec de la mise à jour de la catégorie.")));
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

        $this->category->id = $id;

        try {
            if($this->category->delete()) {
                $response->getBody()->write(json_encode(array("message" => "Catégorie supprimée.")));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode(array("message" => "Échec de la suppression de la catégorie.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(array("error" => "Une erreur est survenue.")));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
?>