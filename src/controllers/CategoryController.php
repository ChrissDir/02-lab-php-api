<?php
// ------------------------------
// Namespace et Importations
// ------------------------------
namespace App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Models\Category;
use PDO;
use Exception;  // Importation de la classe Exception

// ------------------------------
// Contrôleur de Catégorie
// ------------------------------
class CategoryController {
    private $db;
    private $category;

    // ------------------------------
    // Constructeur
    // ------------------------------
    public function __construct($db) {
        $this->db = $db;
        $this->category = new Category($db);
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
    // Lire les catégories
    // ------------------------------
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
        } catch (Exception $e) {  // Modification ici
            $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    // ------------------------------
    // Créer une catégorie
    // ------------------------------
    public function create(Request $request, Response $response, array $args): Response {
        $data = $this->getData($request);
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
        } catch (Exception $e) {  // Modification ici
            $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    // ------------------------------
    // Mettre à jour une catégorie
    // ------------------------------
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
        } catch (Exception $e) {  // Modification ici
            $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    // ------------------------------
    // Supprimer une catégorie
    // ------------------------------
    public function delete(Request $request, Response $response, array $args): Response {
        $data = $this->getData($request);
        $id = $args['id'] ?? $data['id'];  // Permet de supprimer par ID dans l'URL ou dans le corps de la requête

        $this->category->id = $id;

        try {
            if($this->category->delete()) {
                $response->getBody()->write(json_encode(array("message" => "Catégorie supprimée.")));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode(array("message" => "Échec de la suppression de la catégorie.")));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (Exception $e) {  // Modification ici
            $response->getBody()->write(json_encode(array("error" => $e->getMessage())));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
?>