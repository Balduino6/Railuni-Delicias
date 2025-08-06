<?php
require_once './config/auth.php';
$auth = new Auth();

if (!$auth->check()) {
    header('Location: login.php');
    exit;
}

$nome = $_SESSION['user_name'];
$perfil = $_SESSION['user_profile'];

$destino = $perfil === 'admin' ? 'pages/admin/dashboard.php' : 'pages/atendente/vendas.php';
header("Location: $destino");
exit;