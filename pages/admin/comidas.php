<?php
// pages/admin/comidas.php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/db.php';

$auth = new Auth();
$auth->authorize(['admin']);
$conn = (new Database())->connect();

// Deleção
if (isset($_GET['delete_id'])) {
    $stmt = $conn->prepare('DELETE FROM comidas WHERE id = :id');
    $stmt->bindParam(':id', $_GET['delete_id'], PDO::PARAM_INT);
    $stmt->execute();
    header('Location: comidas.php');
    exit;
}

// Carrega comidas e suas categorias
$comidas = $conn->query(
    "SELECT c.id, c.nome, c.preco, cat.nome AS categoria
     FROM comidas c
     JOIN categorias cat ON c.id_categoria = cat.id
     ORDER BY c.nome"
)->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../../includes/header.php';
?>

<div class="d-flex">

  <main class="flex-grow-1 p-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 mb-0">Comidas</h1>
      <a href="comida_form.php" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> Nova Comida
      </a>
    </div>

    <div class="table-responsive shadow-sm rounded-4 bg-white p-3">
      <table class="table table-hover align-middle mb-0" style="min-width: 700px;">
        <thead class="table-light">
          <tr>
            <th style="width: 10%;">ID</th>
            <th style="width: 35%;">Nome</th>
            <th style="width: 20%;">Preço (Kz)</th>
            <th style="width: 25%;">Categoria</th>
            <th class="text-center" style="width: 10%;">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($comidas)): foreach ($comidas as $c): ?>
            <tr>
              <td><?= $c['id'] ?></td>
              <td><?= htmlspecialchars($c['nome']) ?></td>
              <td><?= number_format($c['preco'],2,',','.') ?></td>
              <td><?= htmlspecialchars($c['categoria']) ?></td>
              <td class="text-center">
                <a 
                  href="comida_form.php?id=<?= $c['id'] ?>" 
                  class="btn btn-sm btn-outline-secondary me-1" 
                  title="Editar"
                >
                  <i class="fa-solid fa-pen-to-square"></i>
                </a>
                <a 
                  href="comidas.php?delete_id=<?= $c['id'] ?>" 
                  class="btn btn-sm btn-outline-danger" 
                  onclick="return confirm('Tem certeza que deseja excluir esta comida?');" 
                  title="Excluir"
                >
                  <i class="fa-solid fa-trash"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; else: ?>
            <tr>
              <td colspan="5" class="text-center text-muted py-4">
                Nenhuma comida cadastrada.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
