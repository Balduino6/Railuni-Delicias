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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = [
        'nome'      => $_POST['nome'] ?? '',
        'telefone'  => $_POST['telefone'] ?? '',
        'email'     => $_POST['email'] ?? '',
        'endereco'  => $_POST['endereco'] ?? ''
    ];
    if ($model->criar($dados)) {
        $success = 'Fornecedor cadastrado com sucesso!';
    } else {
        $error = 'Erro ao cadastrar fornecedor. Verifique os dados.';
    }
}
?>
<?php include_once __DIR__ . '/../../../includes/header.php'; ?>
<main class="card-container">
  <div class="card">
    <h2>Cadastrar Fornecedor</h2>
    <?php if ($error): ?><div class="alert alert-error"><?= $error; ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= $success; ?></div><?php endif; ?>
    <form method="post">
      <div class="form-floating">
        <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome" required>
        <label for="nome">Nome</label>
      </div>
      <div class="form-floating">
        <input type="text" class="form-control" id="telefone" name="telefone" placeholder="Telefone" required>
        <label for="telefone">Telefone</label>
      </div>
      <div class="form-floating">
        <input type="email" class="form-control" id="email" name="email" placeholder="E-mail">
        <label for="email">E-mail</label>
      </div>
      <div class="form-floating">
        <input type="text" class="form-control" id="endereco" name="endereco" placeholder="Endereço">
        <label for="endereco">Endereço</label>
      </div>
      <button type="submit" class="btn btn-primary">Salvar</button>
    </form>
  </div>
</main>
<?php include_once __DIR__ . '/../../../includes/footer.php'; ?>

<style>
    /* Card layout */
.card-container { display: flex; justify-content: center; padding: 2rem; }
.card {
  width: 100%; max-width: 600px;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  padding: 2rem;
}
.card h2 { margin-bottom: 1.5rem; color: #333; }

/* Form Floating */
.form-floating { position: relative; margin-bottom: 1rem; }
.form-floating .form-control,
.form-floating .form-select {
  width: 100%; padding: 1rem .75rem .25rem .75rem;
  border: 1px solid #ccc; border-radius: 8px;
  background: #f9f9f9;
  transition: border-color .2s;
}
.form-floating label {
  position: absolute; top: .75rem; left: .75rem;
  font-size: .9rem; color: #777;
  pointer-events: none;
  transition: transform .2s, font-size .2s;
}
.form-floating .form-control:focus + label,
.form-floating .form-select:focus + label,
.form-floating .form-control:not(:placeholder-shown) + label {
  transform: translateY(-1rem);
  font-size: .75rem;
  color: #007bff;
}

/* Table */
.table { width: 100%; border-collapse: collapse; }
.table th,
.table td {
  padding: .75rem; text-align: left; border-bottom: 1px solid #e0e0e0;
}
.table th { background: #f1f1f1; }
.table tr:hover { background: #fafafa; }

/* Buttons */
.btn {
  display: inline-block;
  padding: .5rem 1rem;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 600;
  transition: background .2s, transform .1s;
}
.btn-primary { background: #007bff; color: #fff; border: none; }
.btn-primary:hover { background: #0056b3; }
.btn-warning { background: #ffc107; color: #212529; }
.btn-warning:hover { background: #e0a800; }
.btn-danger { background: #dc3545; color: #fff; }
.btn-danger:hover { background: #c82333; }
.btn-sm { padding: .25rem .5rem; font-size: .85rem; }

/* Alerts */
.alert { padding: 1rem; border-radius: 6px; margin-bottom: 1rem; }
.alert-error { background: #f8d7da; color: #721c24; border-left: 4px solid #f5c6cb; }
.alert-success { background: #d4edda; color: #155724; border-left: 4px solid #c3e6cb; }
</style>