<?php
class Categoria {
    private $db;
    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function listar() {
        $stmt = $this->db->query("SELECT * FROM categorias");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function salvar($nome) {
        $stmt = $this->db->prepare("INSERT INTO categorias (nome) VALUES (?)");
        return $stmt->execute([$nome]);
    }
}