<?php
class ItemVenda {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function adicionar($vendaId, $produtoId, $quantidade, $precoUnitario) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO itens_venda (id_venda, id_produto, quantidade, preco_unitario) VALUES (?, ?, ?, ?)"
        );
        return $stmt->execute([$vendaId, $produtoId, $quantidade, $precoUnitario]);
    }

    public function listarPorVenda($vendaId) {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM itens_venda WHERE id_venda = ?"
        );
        $stmt->execute([$vendaId]);
        return $stmt->fetchAll();
    }
}