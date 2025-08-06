<?php
class Venda {
    private $db;
    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function registrar($dados) {
        $stmt = $this->db->prepare("INSERT INTO vendas (usuario_id, total, data_venda) VALUES (?, ?, NOW())");
        $stmt->execute([$dados['usuario_id'], $dados['total']]);
        return $this->db->lastInsertId();
    }

    public function listar() {
        $stmt = $this->db->query("SELECT * FROM vendas");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}