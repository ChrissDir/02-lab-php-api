<?php
namespace Models;

use PDO;
use PDOException;
use Exception;

class Resource {
    private PDO $conn;
    private const TABLE_NAME = 'ressource';
    private const MAX_NAME_LENGTH = 255;
    private const MAX_URL_LENGTH = 2048;

    public int $id;
    public string $name;
    public string $url;
    public int $technology_id;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    /**
     * @throws Exception
     */
    public function read(): array {
        $query = "SELECT * FROM " . self::TABLE_NAME;
        $stmt = $this->conn->prepare($query);

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception("Erreur de lecture des ressources : " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function create(): bool {
        if (empty($this->name) || strlen($this->name) > self::MAX_NAME_LENGTH || 
            empty($this->url) || strlen($this->url) > self::MAX_URL_LENGTH || 
            empty($this->technology_id)) {
            throw new Exception("Les données fournies sont incomplètes ou invalides.");
        }

        $query = "INSERT INTO " . self::TABLE_NAME . " SET nom=:name, url=:url, technologie_id=:technology_id";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->url = htmlspecialchars(strip_tags($this->url));
        $this->technology_id = (int) htmlspecialchars(strip_tags($this->technology_id));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':url', $this->url);
        $stmt->bindParam(':technology_id', $this->technology_id, \PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur de création de ressource : " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function update(): bool {
        if (empty($this->id)) {
            throw new Exception("ID de ressource invalide.");
        }

        $updateFields = [];
        if (!empty($this->name)) {
            if (strlen($this->name) > self::MAX_NAME_LENGTH) {
                throw new Exception("Nom de ressource trop long.");
            }
            $this->name = htmlspecialchars(strip_tags($this->name));
            $updateFields[] = "nom=:name";
        }
        if (!empty($this->url)) {
            if (strlen($this->url) > self::MAX_URL_LENGTH) {
                throw new Exception("URL de ressource trop long.");
            }
            $this->url = htmlspecialchars(strip_tags($this->url));
            $updateFields[] = "url=:url";
        }
        if (!empty($this->technology_id)) {
            $this->technology_id = (int) htmlspecialchars(strip_tags($this->technology_id));
            $updateFields[] = "technologie_id=:technology_id";
        }

        if (empty($updateFields)) {
            throw new Exception("Aucune donnée fournie pour la mise à jour.");
        }

        $query = "UPDATE " . self::TABLE_NAME . " SET " . implode(", ", $updateFields) . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        if (!empty($this->name)) {
            $stmt->bindParam(':name', $this->name);
        }
        if (!empty($this->url)) {
            $stmt->bindParam(':url', $this->url);
        }
        if (!empty($this->technology_id)) {
            $stmt->bindParam(':technology_id', $this->technology_id, \PDO::PARAM_INT);
        }
        
        $this->id = (int) htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur de mise à jour de ressource : " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function delete(): bool {
        if (empty($this->id)) {
            throw new Exception("ID de ressource invalide.");
        }

        $query = "DELETE FROM " . self::TABLE_NAME . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->id = (int) htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur de suppression de ressource : " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function addTechnology(int $technologyId): bool {
        if (empty($this->id) || empty($technologyId)) {
            throw new Exception("ID de ressource ou de technologie invalide.");
        }

        $query = "INSERT INTO technologie_ressource (ressource_id, technologie_id) VALUES (:resourceId, :technologyId)";
        $stmt = $this->conn->prepare($query);

        $this->id = (int) htmlspecialchars(strip_tags($this->id));
        $technologyId = (int) htmlspecialchars(strip_tags($technologyId));

        $stmt->bindParam(':resourceId', $this->id, \PDO::PARAM_INT);
        $stmt->bindParam(':technologyId', $technologyId, \PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur d'association de technologie : " . $e->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function removeTechnology(int $technologyId): bool {
        if (empty($this->id) || empty($technologyId)) {
            throw new Exception("ID de ressource ou de technologie invalide.");
        }

        $query = "DELETE FROM technologie_ressource WHERE ressource_id = :resourceId AND technologie_id = :technologyId";
        $stmt = $this->conn->prepare($query);

        $this->id = (int) htmlspecialchars(strip_tags($this->id));
        $technologyId = (int) htmlspecialchars(strip_tags($technologyId));

        $stmt->bindParam(':resourceId', $this->id, \PDO::PARAM_INT);
        $stmt->bindParam(':technologyId', $technologyId, \PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur de dissociation de technologie : " . $e->getMessage());
        }
    }
}
?>