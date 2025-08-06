<?php
class Comida {
    private $db;
    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function listarDisponiveis() {
        $stmt = $this->db->query("SELECT * FROM comidas WHERE estoque > 0");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}