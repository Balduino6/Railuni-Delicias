<?php
require_once __DIR__ . '/../../../config/auth.php';
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../models/Cliente.php';

$auth = new Auth();
$auth->authorize(['admin','atendente']);
$pdo = (new Database())->connect();
$clientes = (new Cliente($pdo))->listar();
?>
<?php include_once __DIR__ . '/../../../includes/header.php'; ?>

<main class="card-container">
  <div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
      <h2>Clientes Cadastrados</h2>
      <a href="cadastrar.php" class="btn btn-primary">+ Novo Cliente</a>
    </div>
    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>E-mail</th>
          <th>Telefone</th>
          <th>Documento</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($clientes)): ?>
          <tr>
            <td colspan="6" style="text-align:center; padding:1rem;">Nenhum cliente cadastrado.</td>
          </tr>
        <?php else: ?>
          <?php foreach ($clientes as $c): ?>
          <tr>
            <td><?= $c['id_cliente']; ?></td>
            <td><?= htmlspecialchars($c['nome']); ?></td>
            <td><?= htmlspecialchars($c['email']); ?></td>
            <td><?= htmlspecialchars($c['telefone']); ?></td>
            <td><?= htmlspecialchars($c['tipo_documento'] . ' - ' . $c['numero_documento']); ?></td>
            <td>
              <a href="editar.php?id=<?= $c['id_cliente']; ?>" class="btn btn-sm btn-warning">Editar</a>
              <a href="excluir.php?id=<?= $c['id_cliente']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deseja excluir este cliente?');">Excluir</a>
            </td>
          </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
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