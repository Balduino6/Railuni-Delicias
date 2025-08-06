<?php
require_once __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../models/Fornecedor.php';

$auth = new Auth();
$auth->authorize(['admin']);
$pdo = (new Database())->connect();
$model = new Fornecedor($pdo);

$error = '';
$success = '';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$fornecedor = $model->obter($id);

if (!$fornecedor) {
    header('Location: listar.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'nome'      => $_POST['nome'],
        'contato'   => $_POST['contato'],
        'telefone'  => $_POST['telefone'],
        'email'     => $_POST['email'],
        'endereco'  => $_POST['endereco']
    ];
    if ($model->atualizar($id, $dados)) {
        $success = 'Fornecedor atualizado!';
        $fornecedor = $model->obter($id);
    } else {
        $error = 'Erro ao atualizar fornecedor.';
    }
}
?>
<?php include_once __DIR__ . '/../../../includes/header.php'; ?>
<main class="card-container">
  <div class="card">
    <h2>Editar Fornecedor</h2>
    <?php if ($error): ?><div class="alert alert-error"><?= $error; ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= $success; ?></div><?php endif; ?>
    <form method="post">
      <?php foreach (['nome','contato','telefone','email','endereco'] as $field): ?>
      <div class="form-floating">
        <input type="text" class="form-control" id="<?= $field ?>" name="<?= $field ?>" placeholder="<?= ucfirst($field) ?>" value="<?= htmlspecialchars($fornecedor[$field]) ?>" required>
        <label for="<?= $field ?>"><?= ucfirst($field) ?></label>
      </div>
      <?php endforeach; ?>
      <button type="submit" class="btn btn-primary">Atualizar</button>
    </form>
  </div>
</main>
<?php include_once __DIR__ . '/../../../includes/footer.php'; ?>
