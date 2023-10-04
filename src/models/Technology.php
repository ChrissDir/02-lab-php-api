<?php
namespace Models;

use PDOException;
use PDO;
use Exception;

class Technology {
    private PDO $conn;
    private const TABLE_NAME = 'technologie';
    private const MAX_NAME_LENGTH = 255;
    private const MAX_LOGO_URL_LENGTH = 2048;
    public int $id;
    public string $name;
    public string $logo;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    public function read(int $offset = 0, int $items_per_page = 10): array {
        $query = "SELECT * FROM " . self::TABLE_NAME . " LIMIT :offset, :items_per_page";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':items_per_page', $items_per_page, PDO::PARAM_INT);

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Avoid exposing detailed error messages
            throw new Exception("Erreur de lecture des technologies.");
        }
    }

    public function create(): bool {
        if (empty($this->name) || strlen($this->name) > self::MAX_NAME_LENGTH || 
            empty($this->logo) || strlen($this->logo) > self::MAX_LOGO_URL_LENGTH) {
            throw new Exception("Les données fournies sont incomplètes ou invalides.");
        }

        $query = "INSERT INTO " . self::TABLE_NAME . " SET nom=:name, logo=:logo";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->logo = htmlspecialchars(strip_tags($this->logo));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':logo', $this->logo);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Avoid exposing detailed error messages
            throw new Exception("Erreur de création de technologie.");
        }
    }

    public function update(): bool {
        if (empty($this->id)) {
            throw new Exception("ID de technologie invalide.");
        }
        
        $updateFields = [];
        if (!empty($this->name)) {
            if (strlen($this->name) > self::MAX_NAME_LENGTH) {
                throw new Exception("Nom de technologie trop long.");
            }
            $this->name = htmlspecialchars(strip_tags($this->name));
            $updateFields[] = "nom=:name";
        }
        if (!empty($this->logo)) {
            if (strlen($this->logo) > self::MAX_LOGO_URL_LENGTH) {
                throw new Exception("URL du logo trop longue.");
            }
            $this->logo = htmlspecialchars(strip_tags($this->logo));
            $updateFields[] = "logo=:logo";
        }
    
        if (empty($updateFields)) {
            throw new Exception("Aucune donnée fournie pour la mise à jour.");
        }
    
        $query = "SELECT logo FROM " . self::TABLE_NAME . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $oldLogoPath = __DIR__ . "/../" . $row['logo'];
    
        $query = "UPDATE " . self::TABLE_NAME . " SET " . implode(", ", $updateFields) . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);
    
        if (!empty($this->name)) {
            $stmt->bindParam(':name', $this->name);
        }
        if (!empty($this->logo)) {
            $stmt->bindParam(':logo', $this->logo);
        }
    
        $this->id = (int) htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
    
        try {
            if ($stmt->execute()) {
                // Supprimer l'ancien logo si un nouveau logo est téléchargé
                if (!empty($this->logo) && file_exists($oldLogoPath) && $oldLogoPath !== __DIR__ . "/../" . $this->logo) {
                    unlink($oldLogoPath);
                }
                return true;
            }
        } catch (PDOException $e) {
            // Avoid exposing detailed error messages
            throw new Exception("Erreur de mise à jour de technologie.");
        }
        return false;
    }

    public function delete(): bool {
        if (empty($this->id)) {
            throw new Exception("ID de technologie invalide.");
        }
    
        // Récupérer le chemin du fichier logo avant de supprimer la technologie
        $query = "SELECT logo FROM " . self::TABLE_NAME . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $logoPath = __DIR__ . "/../" . $row['logo'];

        // Supprimer la technologie
        $query = "DELETE FROM " . self::TABLE_NAME . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);
        $this->id = (int) htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);

        try {
            if ($stmt->execute()) {
                // Supprimer le fichier logo
                if (file_exists($logoPath)) {
                    unlink($logoPath);
                }
                return true;
            }
        } catch (PDOException $e) {
            // Avoid exposing detailed error messages
            throw new Exception("Erreur de suppression de technologie.");
        }
        return false;
    }

    public function getCategories(int $technologyId): array {
        if (empty($technologyId)) {
            throw new Exception("ID de technologie invalide.");
        }
    
        $query = "SELECT c.id, c.nom 
                FROM categorie c 
                JOIN technologie_categorie tc ON c.id = tc.categorie_id 
                WHERE tc.technologie_id = :technologyId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':technologyId', $technologyId, PDO::PARAM_INT);

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Avoid exposing detailed error messages
            throw new Exception("Erreur de lecture des catégories.");
        }
    }

    public function addCategory(int $categoryId): bool {
        if (empty($this->id) || empty($categoryId)) {
            throw new Exception("ID de technologie ou de catégorie invalide.");
        }

        $query = "INSERT INTO technologie_categorie (technologie_id, categorie_id) VALUES (:technologyId, :categoryId)";
        $stmt = $this->conn->prepare($query);

        $this->id = (int) htmlspecialchars(strip_tags($this->id));
        $categoryId = (int) htmlspecialchars(strip_tags($categoryId));

        $stmt->bindParam(':technologyId', $this->id, \PDO::PARAM_INT);
        $stmt->bindParam(':categoryId', $categoryId, \PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Avoid exposing detailed error messages
            throw new Exception("Erreur d'association de catégorie.");
        }
    }

    public function removeCategory(int $categoryId): bool {
        if (empty($this->id) || empty($categoryId)) {
            throw new Exception("ID de technologie ou de catégorie invalide.");
        }

        $query = "DELETE FROM technologie_categorie WHERE technologie_id = :technologyId AND categorie_id = :categoryId";
        $stmt = $this->conn->prepare($query);

        $this->id = (int) htmlspecialchars(strip_tags($this->id));
        $categoryId = (int) htmlspecialchars(strip_tags($categoryId));

        $stmt->bindParam(':technologyId', $this->id, \PDO::PARAM_INT);
        $stmt->bindParam(':categoryId', $categoryId, \PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Avoid exposing detailed error messages
            throw new Exception("Erreur de dissociation de catégorie.");
        }
    }

    public function getResources(int $technologyId): array {
        if (empty($technologyId)) {
            throw new Exception("ID de technologie invalide.");
        }
    
        $query = "SELECT r.* FROM ressource r
                JOIN technologie_ressource tr ON r.id = tr.ressource_id
                WHERE tr.technologie_id = :technologyId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':technologyId', $technologyId, PDO::PARAM_INT);
    
        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Avoid exposing detailed error messages
            throw new Exception("Erreur lors de la récupération des ressources.");
        }
    }

    public function addResource(int $resourceId): bool {
        if (empty($this->id) || empty($resourceId)) {
            throw new Exception("ID de technologie ou de ressource invalide.");
        }

        $query = "INSERT INTO technologie_ressource (technologie_id, ressource_id) VALUES (:technologyId, :resourceId)";
        $stmt = $this->conn->prepare($query);

        $this->id = (int) htmlspecialchars(strip_tags($this->id));
        $resourceId = (int) htmlspecialchars(strip_tags($resourceId));

        $stmt->bindParam(':technologyId', $this->id, \PDO::PARAM_INT);
        $stmt->bindParam(':resourceId', $resourceId, \PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Avoid exposing detailed error messages
            throw new Exception("Erreur d'association de ressource.");
        }
    }

    public function removeResource(int $resourceId): bool {
        if (empty($this->id) || empty($resourceId)) {
            throw new Exception("ID de technologie ou de ressource invalide.");
        }

        $query = "DELETE FROM technologie_ressource WHERE technologie_id = :technologyId AND ressource_id = :resourceId";
        $stmt = $this->conn->prepare($query);

        $this->id = (int) htmlspecialchars(strip_tags($this->id));
        $resourceId = (int) htmlspecialchars(strip_tags($resourceId));

        $stmt->bindParam(':technologyId', $this->id, \PDO::PARAM_INT);
        $stmt->bindParam(':resourceId', $resourceId, \PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Avoid exposing detailed error messages
            throw new Exception("Erreur de dissociation de ressource.");
        }
    }

    public function uploadLogo($uploadedFile): string {
        // Vérifier le type de fichier
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($uploadedFile->getStream());
        if (strpos($mime, 'image') === false) {
            throw new Exception("Le fichier téléchargé n'est pas une image.");
        }
    
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8));  // Générer un nom de fichier unique
        $filename = sprintf("/Logos/%s.%s", $basename, $extension);  // chemin relatif
        $destination = __DIR__ . "/../" . $filename;
        $uploadedFile->moveTo($destination);
    
        return $filename;
    }

    public function uploadLogoFromBase64($base64Data): string {
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Data));
        if ($imageData === false) {
            throw new Exception("Erreur lors du décodage de la chaîne base64.");
        }
    
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($imageData);
        if (strpos($mime, 'image') === false) {
            throw new Exception("Le fichier fourni n'est pas une image.");
        }
    
        $extension = explode('/', $mime)[1];
        $basename = bin2hex(random_bytes(8));  // Générer un nom de fichier unique
        $filename = sprintf("/Logos/%s.%s", $basename, $extension);  // chemin relatif
        $destination = __DIR__ . "/../" . $filename;
        file_put_contents($destination, $imageData);

        return $filename;
    }
}
?>