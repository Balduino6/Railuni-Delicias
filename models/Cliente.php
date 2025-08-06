<?php
class Cliente {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function criar(array $dados) {
        $sql = "INSERT INTO clientes (nome, email, telefone, tipo_documento, numero_documento)
                VALUES (:nome, :email, :telefone, :tipo_documento, :numero_documento)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($dados);
    }

    public function listar(): array {
        $stmt = $this->pdo->query("SELECT * FROM clientes ORDER BY data_cadastro DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obter(int $id) {
        $stmt = $this->pdo->prepare("SELECT * FROM clientes WHERE id_cliente = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizar(int $id, array $dados) {
        $dados['id'] = $id;
        $sql = "UPDATE clientes SET
                    nome = :nome,
                    email = :email,
                    telefone = :telefone,
                    tipo_documento = :tipo_documento,
                    numero_documento = :numero_documento
                WHERE id_cliente = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($dados);
    }

    public function excluir(int $id) {
        $stmt = $this->pdo->prepare("DELETE FROM clientes WHERE id_cliente = :id");
        return $stmt->execute(['id' => $id]);
    }
}