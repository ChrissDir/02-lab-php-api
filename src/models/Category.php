<?php
namespace Models;
use PDOException;
use Exception;

class Category {
    private $conn;
    private const TABLE_NAME = 'categorie';
    private const MAX_NAME_LENGTH = 255;  // Assurez-vous que cela correspond à la configuration de votre base de données

    public $id;
    public $name;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        $query = "SELECT * FROM " . self::TABLE_NAME;
        $stmt = $this->conn->prepare($query);

        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Erreur de lecture des catégories : " . $e->getMessage());
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
            throw new Exception("Erreur de création de catégorie : " . $e->getMessage());
        }
    }

    public function update(): bool {
        if (empty($this->name) || strlen($this->name) > self::MAX_NAME_LENGTH || empty($this->id)) {
            throw new Exception("Données invalides pour la mise à jour.");
        }

        $query = "UPDATE " . self::TABLE_NAME . " SET nom=:name WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':id', $this->id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur de mise à jour de catégorie : " . $e->getMessage());
        }
    }

    public function delete(): bool {
        if (empty($this->id)) {
            throw new Exception("ID de catégorie invalide.");
        }

        $query = "DELETE FROM " . self::TABLE_NAME . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':id', $this->id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur de suppression de catégorie : " . $e->getMessage());
        }
    }
}
?>

