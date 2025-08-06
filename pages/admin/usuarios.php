<?php
// pages/admin/usuarios.php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/db.php';

$auth = new Auth();
$auth->authorize(['admin']);
$conn = (new Database())->connect();

// Busca usuários
$usuarios = $conn->query("SELECT id, nome, email, perfil FROM usuarios ORDER BY nome")
                 ->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>

<div class="container-fluid my-5">
  <div class="d-flex justify-content-between align-items-center mb-4 px-3">
    <h2 class="fw-semibold text-primary"><i class="bi bi-people-fill me-2"></i>Usuários</h2>
    <a href="usuario_form.php" class="btn btn-success btn-lg shadow-sm">
      <i class="bi bi-person-plus-fill me-1"></i> Novo Usuário
    </a>
  </div>

  <div class="card shadow-sm rounded-4 mx-3">
    <div class="card-body px-4 py-3">
      <div class="table-responsive">
        <table class="table table-striped align-middle mb-0" style="font-size: 1rem;">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Nome</th>
              <th>Email</th>
              <th>Perfil</th>
              <th class="text-center">Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($usuarios): foreach ($usuarios as $u): ?>
              <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['nome']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td>
                  <?php if ($u['perfil'] === 'admin'): ?>
                    <span class="badge bg-primary">Admin</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">Atendente</span>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <a href="usuario_form.php?id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <a href="usuarios.php?delete_id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-danger" title="Excluir"
                     onclick="return confirm('Excluir este usuário?');">
                    <i class="bi bi-trash"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; else: ?>
              <tr>
                <td colspan="5" class="text-center py-4 text-muted">
                  Nenhum usuário cadastrado.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>


<?php include __DIR__ . '/../../includes/footer.php'; ?>
