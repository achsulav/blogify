<?php
namespace App\Models;
use App\Foundation\Database;
use PDO;

class Category {
    protected PDO $db;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT * FROM categories ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
