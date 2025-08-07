<?php
// pages/admin/permissoes.php

require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/db.php';

$auth = new Auth();
$auth->authorize(['admin']);

$conn = (new Database())->connect();
$msg = '';

// Atualizar permissões
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['permissoes'])) {
    foreach ($_POST['permissoes'] as $id => $perfil) {
        $stmt = $conn->prepare("UPDATE usuarios SET perfil = :perfil WHERE id = :id");
        $stmt->execute([
            ':perfil' => $perfil,
            ':id' => $id
        ]);
    }
    $msg = 'Permissões atualizadas com sucesso!';
}

// Buscar todos os usuários
$stmt = $conn->query("SELECT id, nome, email, perfil FROM usuarios ORDER BY nome");
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Permissões</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <style>
        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 25px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        select {
            padding: 6px;
            border-radius: 4px;
        }

        .btn {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #0066cc;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #004e99;
        }

        .success-msg {
            background: #d4edda;
            color: #155724;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        h2 {
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <?php include '../../includes/header.php'; ?>

    <div class="container">
        <h2>Gerenciar Permissões de Usuários</h2>

        <?php if (!empty($msg)): ?>
            <div class="success-msg"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <form method="post">
            <table>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Perfil</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= htmlspecialchars($usuario['nome']) ?></td>
                            <td><?= htmlspecialchars($usuario['email']) ?></td>
                            <td>
                                <select name="permissoes[<?= $usuario['id'] ?>]">
                                    <option value="admin" <?= $usuario['perfil'] === 'admin' ? 'selected' : '' ?>>Administrador</option>
                                    <option value="atendente" <?= $usuario['perfil'] === 'atendente' ? 'selected' : '' ?>>Atendente</option>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <button type="submit" class="btn">Salvar Permissões</button>
        </form>
    </div>

    <?php include '../../includes/footer.php'; ?>
</body>
</html>
