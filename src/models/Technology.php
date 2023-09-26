<?php
namespace Models;
use PDOException;
use PDO;
use Exception;

class Technology {
    private $conn;
    private const TABLE_NAME = 'technologie';
    private const MAX_NAME_LENGTH = 255;  // Assurez-vous que cela correspond à la configuration de votre base de données
    private const MAX_LOGO_URL_LENGTH = 2048;  // Assurez-vous que cela correspond à la configuration de votre base de données

    public $id;
    public $name;
    public $logo;
    public $category_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read(int $offset = 0, int $items_per_page = 10) {
        $query = "SELECT * FROM " . self::TABLE_NAME . " LIMIT :offset, :items_per_page";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->bindParam(':items_per_page', $items_per_page, PDO::PARAM_INT);

        try {
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            throw new Exception("Erreur de lecture des technologies : " . $e->getMessage());
        }
    }

    public function create(): bool {
        if (empty($this->name) || strlen($this->name) > self::MAX_NAME_LENGTH || 
            empty($this->logo) || strlen($this->logo) > self::MAX_LOGO_URL_LENGTH || 
            empty($this->category_id)) {
            throw new Exception("Les données fournies sont incomplètes ou invalides.");
        }

        $query = "INSERT INTO " . self::TABLE_NAME . " SET nom=:name, logo=:logo, categorie_id=:category_id";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->logo = htmlspecialchars(strip_tags($this->logo));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':logo', $this->logo);
        $stmt->bindParam(':category_id', $this->category_id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur de création de technologie : " . $e->getMessage());
        }
    }

    public function update(): bool {
        if (empty($this->id) || 
            (empty($this->name) && empty($this->logo) && empty($this->category_id))) {
            throw new Exception("Aucune donnée fournie pour la mise à jour.");
        }

        $query = "UPDATE " . self::TABLE_NAME . " SET nom=:name, logo=:logo, categorie_id=:category_id WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->logo = htmlspecialchars(strip_tags($this->logo));
        $this->category_id = htmlspecialchars(strip_tags($this->category_id));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':logo', $this->logo);
        $stmt->bindParam(':category_id', $this->category_id);
        $stmt->bindParam(':id', $this->id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur de mise à jour de technologie : " . $e->getMessage());
        }
    }

    public function delete(): bool {
        if (empty($this->id)) {
            throw new Exception("ID de technologie invalide.");
        }

        $query = "DELETE FROM " . self::TABLE_NAME . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':id', $this->id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur de suppression de technologie : " . $e->getMessage());
        }
    }
}
?>
