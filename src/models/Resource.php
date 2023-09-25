<?php

class Resource {
    private $conn;
    private const TABLE_NAME = 'ressource';

    public $id;
    public $name;
    public $url;
    public $technology_id;

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
            throw new Exception("Erreur de lecture des ressources : " . $e->getMessage());
        }
    }

    public function create() {
        $query = "INSERT INTO " . self::TABLE_NAME . " SET nom=:name, url=:url, technologie_id=:technology_id";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->url = htmlspecialchars(strip_tags($this->url));
        $this->technology_id = htmlspecialchars(strip_tags($this->technology_id));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':url', $this->url);
        $stmt->bindParam(':technology_id', $this->technology_id);

        try {
            if($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Erreur de création de ressource : " . $e->getMessage());
        }
    }

    public function update() {
        $query = "UPDATE " . self::TABLE_NAME . " SET nom=:name, url=:url, technologie_id=:technology_id WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->url = htmlspecialchars(strip_tags($this->url));
        $this->technology_id = htmlspecialchars(strip_tags($this->technology_id));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':url', $this->url);
        $stmt->bindParam(':technology_id', $this->technology_id);
        $stmt->bindParam(':id', $this->id);

        try {
            if($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $e) {
            throw new Exception("Erreur de mise à jour de ressource : " . $e->getMessage());
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
            throw new Exception("Erreur de suppression de ressource : " . $e->getMessage());
        }
    }
}
?>