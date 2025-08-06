<?php
// config/auth.php

session_start();

require_once __DIR__ . '/db.php';

class Auth {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    public function login($email, $senha) {
        $sql = 'SELECT id, nome, email, senha_hash, perfil FROM Usuarios WHERE email = :email';
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($senha, $user['senha_hash'])) {
            // Armazena dados na sessão
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nome'];
            $_SESSION['user_profile'] = $user['perfil'];
            return true;
        }
        return false;
    }

    public function check() {
        return isset($_SESSION['user_id']);
    }

    public function user() {
        if ($this->check()) {
            return [
                'id' => $_SESSION['user_id'],
                'nome' => $_SESSION['user_name'],
                'perfil' => $_SESSION['user_profile']
            ];
        }
        return null;
    }

    public function logout() {
        session_unset();
        session_destroy();
    }

    /**
     * Autoriza perfis para acessar páginas.
     * Redireciona para login em '/tascapandora/login.php'.
     * Ajuste conforme o nome da pasta do seu projeto.
     *
     * @param array $perfisPermitidos
     */
    public function authorize(array $perfisPermitidos = []): void {
        if (!$this->check() || !in_array($_SESSION['user_profile'], $perfisPermitidos, true)) {
            $baseUrl = '/tascapandora'; // Nome da pasta do projeto
            header("Location: {$baseUrl}/login.php?erro=permissao");
            exit;
        }
    }
}