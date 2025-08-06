<?php
class Pedido {
    private $db;
    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function registrarItem($venda_id, $produto_id, $quantidade, $preco_unitario) {
        $stmt = $this->db->prepare("INSERT INTO pedidos (venda_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$venda_id, $produto_id, $quantidade, $preco_unitario]);
    }
}