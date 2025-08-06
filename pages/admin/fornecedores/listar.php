<?php
require_once __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../models/Fornecedor.php';

$auth = new Auth();
$auth->authorize(['admin','atendente']);
$pdo = (new Database())->connect();
$fornecedores = (new Fornecedor($pdo))->listar();
?>
<?php include_once __DIR__ . '/../../../includes/header.php'; ?>
<main class="card-container">
  <div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
      <h2>Fornecedores</h2>
      <a href="cadastrar.php" class="btn btn-primary">+ Novo Fornecedor</a>
    </div>
    <?php if (empty($fornecedores)): ?>
      <p class="text-center">Nenhum fornecedor cadastrado.</p>
    <?php else: ?>
    <table class="table table-striped table-hover">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Email</th>
          <th>Telefone</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($fornecedores as $f): ?>
        <tr>
          <td><?= htmlspecialchars($f['id_fornecedor']) ?></td>
          <td><?= htmlspecialchars($f['nome']) ?></td>
           <td><?= htmlspecialchars($f['email']) ?></td>
          <td><?= htmlspecialchars($f['telefone']) ?></td>
            <td><?= htmlspecialchars($f['endereco']) ?></td>
          <td>
            <a href="editar.php?id=<?= $f['id_fornecedor'] ?>" class="btn btn-sm btn-warning">Editar</a>
            <a href="excluir.php?id=<?= $f['id_fornecedor'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza?')">Excluir</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
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