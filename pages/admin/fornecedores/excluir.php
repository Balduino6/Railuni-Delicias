<?php
require_once __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../models/Fornecedor.php';

$auth = new Auth();
$auth->authorize(['admin']);
$pdo = (new Database())->connect();
$model = new Fornecedor($pdo);

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id) {
    $model->excluir($id);
}
header('Location: listar.php');
exit;