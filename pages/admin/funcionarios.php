<?php
// funcionarios.php (Listagem atualizada)
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/db.php';

$auth = new Auth();
$auth->authorize(['admin']);
$conn = (new Database())->connect();

// Deleção
if (isset($_GET['delete_id'])) {
    $stmt = $conn->prepare('DELETE FROM funcionarios WHERE id = :id');
    $stmt->execute([':id' => $_GET['delete_id']]);
    header('Location: funcionarios.php');
    exit;
}

// Mensagem de ação
$message = '';
if (isset($_GET['msg'])) {
    $message = $_GET['msg'] === 'success' ? 'Funcionário salvo com sucesso!' : 'Erro ao salvar funcionário.';
}

// Busca funcionários
$funcionarios = $conn->query(
    "SELECT f.id, f.nome_completo, f.numero_documento AS num_doc, f.telefone, u.nome AS usuario
     FROM funcionarios f
     JOIN usuarios u ON f.usuario_id = u.id
     ORDER BY f.nome_completo"
)->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../../includes/header.php';
?>
<main class="content">
  <div class="container-fluid my-5">
    <?php if ($message): ?>
      <div class="alert alert-info px-4 py-2">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>
    <div class="d-flex justify-content-between align-items-center mb-4 px-3">
      <h2 class="fw-semibold text-primary">
        <i class="bi bi-people-fill me-2"></i>Funcionários
      </h2>
      <a href="funcionario_form.php" class="btn btn-success btn-lg shadow-sm">
        <i class="bi bi-person-plus-fill me-1"></i> Novo Funcionário
      </a>
    </div>

    <div class="card shadow-sm rounded-4 mx-3">
      <div class="card-body px-4 py-3">
        <div class="table-responsive">
          <table class="table table-striped align-middle mb-0" style="font-size:1rem;">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Nome Completo</th>
                <th>Documento</th>
                <th>Telefone</th>
                <th>Usuário</th>
                <th class="text-center">Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($funcionarios): foreach ($funcionarios as $f): ?>
                <tr>
                  <td><?= $f['id'] ?></td>
                  <td><?= htmlspecialchars($f['nome_completo']) ?></td>
                  <td><?= htmlspecialchars($f['num_doc']) ?></td>
                  <td><?= htmlspecialchars($f['telefone']) ?></td>
                  <td><?= htmlspecialchars($f['usuario']) ?></td>
                  <td class="text-center">
                    <a href="funcionario_form.php?id=<?= $f['id'] ?>" class="btn btn-sm btn-outline-primary me-1" title="Editar">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <a href="funcionarios.php?delete_id=<?= $f['id'] ?>" class="btn btn-sm btn-outline-danger" title="Excluir" onclick="return confirm('Excluir este funcionário?');">
                      <i class="bi bi-trash"></i>
                    </a>
                  </td>
                </tr>
              <?php endforeach; else: ?>
                <tr>
                  <td colspan="6" class="text-center py-4 text-muted">
                    Nenhum funcionário cadastrado.
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</main>
<?php include __DIR__ . '/../../includes/footer.php'; ?>