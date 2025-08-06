<?php
// pages/admin/categorias.php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/db.php';

$auth = new Auth();
$auth->authorize(['admin']);
$conn = (new Database())->connect();

// Deleção
if (isset($_GET['delete_id'])) {
    $stmt = $conn->prepare('DELETE FROM categorias WHERE id = :id');
    $stmt->bindParam(':id', $_GET['delete_id'], PDO::PARAM_INT);
    $stmt->execute();
    header('Location: categorias.php');
    exit;
}

// Carrega categorias para listagem
$stmt = $conn->query('SELECT * FROM categorias ORDER BY nome');
$cats = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../../includes/header.php';
?>

<style>
   body {
    background-color: #f0f2f5;
  }

  #categorias{
    border-bottom: 1px solid #ddd;
  }  
  
</style>

<div class="d-flex">

  <main class="flex-grow-1 p-5">
    <h1 id="categorias" class="mb-4 display-4 fw-bold text-secondary">Categorias</h1>
    <p class="text-muted mb-4">Gerencie as categorias de produtos disponíveis no sistema.</p>
    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success">
        Categoria adicionada com sucesso!
      </div>
    <?php endif; ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <a href="categoria_form.php" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> Nova Categoria
      </a>
    </div>

    <div class="table-responsive shadow-sm rounded-4 bg-white p-3">
      <table class="table table-hover mb-0 align-middle" style="min-width: 600px;">
        <thead class="table-light">
          <tr>
            <th scope="col" style="width: 10%;">ID</th>
            <th scope="col" style="width: 70%;">Nome</th>
            <th scope="col" class="text-center" style="width: 20%;">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($cats)): ?>
            <?php foreach ($cats as $c): ?>
              <tr>
                <td><?= $c['id'] ?></td>
                <td><?= htmlspecialchars($c['nome']) ?></td>
                <td class="text-center">
                  <a 
                    href="categoria_form.php?id=<?= $c['id'] ?>" 
                    class="btn btn-sm btn-outline-secondary me-1" 
                    title="Editar"
                  >
                    <i class="fa-solid fa-pen-to-square"></i>
                  </a>
                  <a 
                    href="categorias.php?delete_id=<?= $c['id'] ?>" 
                    class="btn btn-sm btn-outline-danger" 
                    onclick="return confirm('Confirma exclusão desta categoria?');" 
                    title="Excluir"
                  >
                    <i class="fa-solid fa-trash"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="3" class="text-center text-muted py-4">
                Nenhuma categoria cadastrada.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
