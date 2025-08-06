<?php

class Produto {
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function listarTodos() {
        return $this->pdo->query("SELECT * FROM produtos")->fetchAll();
    }

    public function buscar($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM produtos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function salvar($dados) {
        if (isset($dados['id']) && $dados['id']) {
            // UPDATE
            $stmt = $this->pdo->prepare(
                "UPDATE produtos SET nome = :nome, descricao = :descricao, categoria = :categoria, preco_custo = :preco_custo, preco_venda = :preco_venda WHERE id = :id"
            );
            $stmt->bindParam(':id', $dados['id'], PDO::PARAM_INT);
        } else {
            // INSERT
            $stmt = $this->pdo->prepare(
                "INSERT INTO produtos (nome, descricao, categoria, preco_custo, preco_venda) VALUES (:nome, :descricao, :categoria, :preco_custo, :preco_venda)"
            );
        }
        // Bind common parameters
        $stmt->bindParam(':nome', $dados['nome']);
        $stmt->bindParam(':descricao', $dados['descricao']);
        $stmt->bindParam(':categoria', $dados['categoria']);
        $stmt->bindParam(':preco_custo', $dados['preco_custo']);
        $stmt->bindParam(':preco_venda', $dados['preco_venda']);
        return $stmt->execute();
    }
}