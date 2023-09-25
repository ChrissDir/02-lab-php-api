<?php

class Category {
    private $conn;
    private const TABLE_NAME = 'categorie';

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

    public function create() {
        $query = "INSERT INTO " . self::TABLE_NAME . " SET nom=:name";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));

        $stmt->bindParam(':name', $this->name);

        try {
            if($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Erreur de création de catégorie : " . $e->getMessage());
        }
    }

    public function update() {
        $query = "UPDATE " . self::TABLE_NAME . " SET nom=:name WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':id', $this->id);

        try {
            if($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Erreur de mise à jour de catégorie : " . $e->getMessage());
        }
    }

    public function delete() {
        $query = "DELETE FROM " . self::TABLE_NAME . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':id', $this->id);

        try {
            if($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Erreur de suppression de catégorie : " . $e->getMessage());
        }
    }
}
?>
