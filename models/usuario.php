<?php
class Usuario {
    private $db;
    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function buscarPorEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function criar($dados) {
        $stmt = $this->db->prepare("INSERT INTO usuarios (nome, email, senha_hash, perfil) VALUES (?, ?, ?, ?)");
        return $stmt->execute([
            $dados['nome'],
            $dados['email'],
            password_hash($dados['senha_hash'], PASSWORD_DEFAULT),
            $dados['perfil']
        ]);
    }
}

class Usuario {
    public $id, $nome, $email, $senha, $perfil;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function criar($nome, $email, $senha_hash, $perfil = 'atendente') {
        $stmt = $this->pdo->prepare("INSERT INTO usuarios (nome, email, senha_hash, perfil) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$nome, $email, $senha_hash, $perfil]);
    }
}