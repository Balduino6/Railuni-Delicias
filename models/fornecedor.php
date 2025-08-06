<?php
class Fornecedor {
    private $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }
    public function criar(array $dados) {
        $sql = "INSERT INTO fornecedores (nome, contato, telefone, email, endereco)
                VALUES (:nome, :contato, :telefone, :email, :endereco)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($dados);
    }
    public function listar(): array {
        return $this->pdo->query("SELECT * FROM fornecedores ORDER BY data_cadastro DESC")->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obter(int $id) {
        $stmt = $this->pdo->prepare("SELECT * FROM fornecedores WHERE id_fornecedor = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function atualizar(int $id, array $dados) {
        $dados['id'] = $id;
        $sql = "UPDATE fornecedores SET
                    nome = :nome,
                    contato = :contato,
                    telefone = :telefone,
                    email = :email,
                    endereco = :endereco
                WHERE id_fornecedor = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($dados);
    }
    public function excluir(int $id) {
        return $this->pdo->prepare("DELETE FROM fornecedores WHERE id_fornecedor = :id")->execute(['id' => $id]);
    }
}