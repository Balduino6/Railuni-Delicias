<?php

class ItemPedido {
    private $conn;
    public function __construct($db) {
        $this->conn = $db;
    }

    public function adicionar($pedido_id, $produto_id, $quantidade, $preco_unitario) {
        $stmt = $this->conn->prepare("INSERT INTO itens_pedido (id_pedido, id_produto, quantidade, preco_unitario) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$pedido_id, $produto_id, $quantidade, $preco_unitario]);
    }
}