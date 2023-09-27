<?php
namespace Models;
use PDOException;
use Exception;

class Resource {
    private $conn;
    private const TABLE_NAME = 'ressource';
    private const MAX_NAME_LENGTH = 255;
    private const MAX_URL_LENGTH = 2048;

    public $id;
    public $name;
    public $url;
    public $technology_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Reads all resources from the database.
     *
     * @return PDOStatement The result statement.
     * @throws Exception If there is an error during the database operation.
     */
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

    /**
     * Creates a new resource in the database.
     *
     * @return bool True on success, false otherwise.
     * @throws Exception If there is an error during the database operation or invalid data.
     */
    public function create(): bool {
        if (empty($this->name) || strlen($this->name) > self::MAX_NAME_LENGTH || 
            empty($this->url) || strlen($this->url) > self::MAX_URL_LENGTH || 
            empty($this->technology_id) || !is_numeric($this->technology_id)) {
            throw new Exception("Les données fournies sont incomplètes ou invalides.");
        }

        $query = "INSERT INTO " . self::TABLE_NAME . " SET nom=:name, url=:url, technologie_id=:technology_id";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->url = htmlspecialchars(strip_tags($this->url));
        $this->technology_id = htmlspecialchars(strip_tags($this->technology_id));

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
     * Updates an existing resource in the database.
     *
     * @return bool True on success, false otherwise.
     * @throws Exception If there is an error during the database operation or invalid data.
     */
    public function update(): bool {
        if (empty($this->id) || !is_numeric($this->id) || 
            (empty($this->name) && empty($this->url) && empty($this->technology_id))) {
            throw new Exception("Aucune donnée fournie pour la mise à jour.");
        }

        $query = "UPDATE " . self::TABLE_NAME . " SET nom=:name, url=:url, technologie_id=:technology_id WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->url = htmlspecialchars(strip_tags($this->url));
        $this->technology_id = htmlspecialchars(strip_tags($this->technology_id));
        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':url', $this->url);
        $stmt->bindParam(':technology_id', $this->technology_id, \PDO::PARAM_INT);
        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur de mise à jour de ressource : " . $e->getMessage());
        }
    }

    /**
     * Deletes an existing resource from the database.
     *
     * @return bool True on success, false otherwise.
     * @throws Exception If there is an error during the database operation or invalid data.
     */
    public function delete(): bool {
        if (empty($this->id) || !is_numeric($this->id)) {
            throw new Exception("ID de ressource invalide.");
        }

        $query = "DELETE FROM " . self::TABLE_NAME . " WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(':id', $this->id, \PDO::PARAM_INT);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Erreur de suppression de ressource : " . $e->getMessage());
        }
    }
}
?>
