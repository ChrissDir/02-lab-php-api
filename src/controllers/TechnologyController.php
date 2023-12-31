<?php
namespace App;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Models\Technology;
use PDO;
use Exception;

// ------------------------------
// Contrôleur de Technologie
// ------------------------------
class TechnologyController {
    private $db;  // Base de données
    private $technology;  // Modèle de technologie

    // ------------------------------
    // Constructeur
    // ------------------------------
    public function __construct($db) {
        $this->db = $db;  // Initialisation de la base de données
        $this->technology = new Technology($db);  // Initialisation du modèle de technologie
    }

    // ------------------------------
    // Fonction pour extraire les données de la requête
    // ------------------------------
    private function getData(Request $request) {
        $data = $request->getParsedBody();
        if (empty($data)) {
            $data = json_decode($request->getBody(), true);
        }
        return $data;
    }

    // ------------------------------
    // Lire les technologies
    // ------------------------------
    public function read(Request $request, Response $response, array $args): Response {
        $page = $args['page'] ?? 1;  // Page courante
        $items_per_page = $args['items_per_page'] ?? 10;  // Éléments par page
        $offset = ($page - 1) * $items_per_page;  // Offset pour la requête SQL

        try {
            $technologies_arr = $this->technology->read($offset, $items_per_page);  // Lire les technologies
            if(!empty($technologies_arr)) {
                $response->getBody()->write(json_encode($technologies_arr));  // Écrire les technologies dans la réponse
                return $response->withHeader('Content-Type', 'application/json');  // Définir le type de contenu
            } else {
                $response->getBody()->write(json_encode(["message" => "Aucune technologie trouvée."]));  // Message d'erreur
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);  // Statut 404
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(["error" => $e->getMessage()]));  // Message d'erreur
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);  // Statut 500
        }
    }

    // ------------------------------
    // Créer une technologie
    // ------------------------------
    public function create(Request $request, Response $response, array $args): Response {
        $data = $this->getData($request);  // Données envoyées dans la requête
        $uploadedFiles = $request->getUploadedFiles();  // Fichiers téléchargés
        $uploadedFile = $uploadedFiles['logo'];  // Fichier logo

        // ------------------------------
        // Vérifier si le fichier a été téléchargé sans erreur
        // ------------------------------
        if ($uploadedFile && $uploadedFile->getError() === UPLOAD_ERR_OK) {
            $this->technology->name = $data['name'] ?? '';  // Nom de la technologie
            $this->technology->logo = $this->technology->uploadLogo($uploadedFile);  // Télécharger le logo

            try {
                if($this->technology->create()) {  // Créer la technologie
                    $response->getBody()->write(json_encode(["message" => "Technologie créée.", "logo" => $this->technology->logo]));  // Message de succès
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(201);  // Statut 201
                } else {
                    $response->getBody()->write(json_encode(["message" => "Échec de la création de la technologie."]));  // Message d'erreur
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(500);  // Statut 500
                }
            } catch (Exception $e) {
                $response->getBody()->write(json_encode(["error" => $e->getMessage()]));  // Message d'erreur
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);  // Statut 500
            }
        } else {
            $response->getBody()->write(json_encode(["error" => "Erreur de téléchargement du logo."]));  // Message d'erreur
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);  // Statut 400
        }
    }

    // ------------------------------
    // Mettre à jour une technologie
    // ------------------------------

    public function update(Request $request, Response $response, array $args): Response {
        $data = $this->getData($request);  // Données envoyées dans la requête
        $id = $args['id'] ?? $data['id'];  // Permet de mettre à jour par ID dans l'URL ou dans le corps de la requête

        $this->technology->id = $id;  // Définir l'ID de la technologie
        $this->technology->name = $data['name'] ?? '';  // Nom de la technologie
        $uploadedFiles = $request->getUploadedFiles();  // Fichiers téléchargés
        $uploadedFile = $uploadedFiles['logo'] ?? null;  // Fichier logo

        // Vérifier si le fichier a été téléchargé sans erreur
        if ($uploadedFile && $uploadedFile->getError() === UPLOAD_ERR_OK) {
            $this->technology->logo = $this->technology->uploadLogo($uploadedFile);  // Télécharger le logo
        } elseif (isset($data['logo'])) {  // Si un logo en base64 est fourni
            $this->technology->logo = $this->technology->uploadLogoFromBase64($data['logo']);  // Télécharger le logo depuis base64
        }

        try {
            if($this->technology->update()) {  // Mettre à jour la technologie
                $response->getBody()->write(json_encode(["message" => "Technologie mise à jour.", "logo" => $this->technology->logo]));  // Message de succès
                return $response->withHeader('Content-Type', 'application/json');  // Définir le type de contenu
            } else {
                $response->getBody()->write(json_encode(["message" => "Échec de la mise à jour de la technologie."]));  // Message d'erreur
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);  // Statut 500
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(["error" => $e->getMessage()]));  // Message d'erreur
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);  // Statut 500
        }
    }

    
    // ------------------------------
    // Supprimer une technologie
    // ------------------------------

    public function delete(Request $request, Response $response, array $args): Response {
        $data = $this->getData($request);
        $id = $args['id'] ?? $data['id'];  // Permet de supprimer par ID dans l'URL ou dans le corps de la requête

        $this->technology->id = $id;  // Définir l'ID de la technologie

        try {
            if($this->technology->delete()) {  // Supprimer la technologie
                $response->getBody()->write(json_encode(["message" => "Technologie supprimée."]));  // Message de succès
                return $response->withHeader('Content-Type', 'application/json');  // Définir le type de contenu
            } else {
                $response->getBody()->write(json_encode(["message" => "Échec de la suppression de la technologie."]));  // Message d'erreur
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);  // Statut 500
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(["error" => $e->getMessage()]));  // Message d'erreur
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);  // Statut 500
        }
    }
    
    // ------------------------------
    // Récupérer les catégories associées à une technologie
    // ------------------------------

    public function getCategories(Request $request, Response $response, array $args): Response {
        $technologyId = $args['id'];  // ID de la technologie

        try {
            $categories = $this->technology->getCategories($technologyId);  // Récupérer les catégories
            if(!empty($categories)) {
                $response->getBody()->write(json_encode($categories));  // Écrire les catégories dans la réponse
                return $response->withHeader('Content-Type', 'application/json');  // Définir le type de contenu
            } else {
                $response->getBody()->write(json_encode(["message" => "Aucune catégorie trouvée pour cette technologie."]));  // Message d'erreur
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);  // Statut 404
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(["error" => $e->getMessage()]));  // Message d'erreur
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);  // Statut 500
        }
    }

    // ------------------------------
    // Associer une catégorie à une technologie
    // ------------------------------

    public function addCategory(Request $request, Response $response, array $args): Response {
        $data = $this->getData($request);
        $technologyId = $args['id'];
        $categoryId = $data['category_id'];

        $this->technology->id = $technologyId;

        try {
            if ($this->technology->addCategory($categoryId)) {
                $response->getBody()->write(json_encode(["message" => "Catégorie associée à la technologie."]));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode(["message" => "Échec de l'association de la catégorie à la technologie."]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(["error" => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    // ------------------------------
    // Dissocier une catégorie d'une technologie
    // ------------------------------

    public function removeCategory(Request $request, Response $response, array $args): Response {
        $data = $this->getData($request);
        $technologyId = $args['id'];
        $categoryId = $args['categoryId'];

        $this->technology->id = $technologyId;

        try {
            if ($this->technology->removeCategory($categoryId)) {
                $response->getBody()->write(json_encode(["message" => "Catégorie dissociée de la technologie."]));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode(["message" => "Échec de la dissociation de la catégorie de la technologie."]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(["error" => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    // ------------------------------
    // Récupérer les ressources associées à une technologie
    // ------------------------------

    public function getResources(Request $request, Response $response, array $args): Response {
        $technologyId = $args['id'];  // ID de la technologie

        try {
            $resources = $this->technology->getResources($technologyId);  // Récupérer les ressources
            if(!empty($resources)) {
                $response->getBody()->write(json_encode($resources));  // Écrire les ressources dans la réponse
                return $response->withHeader('Content-Type', 'application/json');  // Définir le type de contenu
            } else {
                $response->getBody()->write(json_encode(["message" => "Aucune ressource trouvée pour cette technologie."]));  // Message d'erreur
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);  // Statut 404
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(["error" => $e->getMessage()]));  // Message d'erreur
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);  // Statut 500
        }
    }

    // ------------------------------
    // Associer une ressource à une technologie
    // ------------------------------

    public function addResource(Request $request, Response $response, array $args): Response {
        $data = $this->getData($request);
        $technologyId = $args['id'];
        $resourceId = $data['resource_id'];

        $this->technology->id = $technologyId;

        try {
            if ($this->technology->addResource($resourceId)) {
                $response->getBody()->write(json_encode(["message" => "Ressource associée à la technologie."]));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode(["message" => "Échec de l'association de la ressource à la technologie."]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(["error" => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    // ------------------------------
    // Dissocier une ressource d'une technologie
    // ------------------------------
    
    public function removeResource(Request $request, Response $response, array $args): Response {
        $data = $this->getData($request);
        $technologyId = $args['id'];
        $resourceId = $args['resourceId'];

        $this->technology->id = $technologyId;

        try {
            if ($this->technology->removeResource($resourceId)) {
                $response->getBody()->write(json_encode(["message" => "Ressource dissociée de la technologie."]));
                return $response->withHeader('Content-Type', 'application/json');
            } else {
                $response->getBody()->write(json_encode(["message" => "Échec de la dissociation de la ressource de la technologie."]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
        } catch (Exception $e) {
            $response->getBody()->write(json_encode(["error" => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
?>