<?php
namespace Models;

use PDO;
use PDOException;
use Exception;

class Category {
    private PDO $conn;
    private const TABLE_NAME = 'categorie';
    private const MAX_NAME_LENGTH = 255;

    public int $id;
    public string $name;

    public function __construct(PDO $db) {
        $this->conn = $db;
    }

    public function read(): array {
        $query = "SELECT * FROM " . self::TABLE_NAME;
        $stmt = $this->conn->prepare($query);

        try {
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Avoid exposing detailed error messages
            throw new Exception("Erreur de lecture des catégories.");
        }
    }

    public function create(): bool {
        if (empty($this->name) || strlen($this->name) > self::MAX_NAME_LENGTH) {
            throw new Exception("Nom de catégorie invalide.");
        }

        $query = "INSERT INTO " . self::TABLE_NAME . " SET nom=:name";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));

        $stmt->bindParam(':name', $this->name);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Avoid exposing detailed error messages
            throw new Exception("Erreur de création de catégorie.");
        }
    }

    public function update(): bool {
        if (empty($this->id) || !is_numeric($this->id)) {
            throw new Exception("ID de catégorie invalide.");
        }

        $updateFields = [];
        if (!empty($this->name)) {
            if (strlen($this->name) > self::MAX_NAME_LENGTH) {
                throw new Exception("Nom de catégorie trop long.");
            }
            $this->name = htmlspecialchars(strip_tags($this->name));
            $updateFields[] = "nom=:name";
        }

        if (empty($updateFields)) {
            throw new Exception("Aucune donnée fournie pour la mise à jour.");
        }

        $query = "UPDATE " . self::TABLE_NAME . " SET " . implode(", ", $updateFields) . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        if (!empty($this->name)) {
            $stmt->bindParam(':name', $this->name);
        }
        
        $this->id = (int) htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Avoid exposing detailed error messages
            throw new Exception("Erreur de mise à jour de catégorie.");
        }
    }

    public function delete(): bool {
        if (empty($this->id) || !is_numeric($this->id)) {
            throw new Exception("ID de catégorie invalide.");
        }

        $query = "DELETE FROM " . self::TABLE_NAME . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->id = (int) htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            // Avoid exposing detailed error messages
            throw new Exception("Erreur de suppression de catégorie.");
        }
    }
}
?>