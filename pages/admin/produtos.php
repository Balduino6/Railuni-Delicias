<?php
// pages/admin/produtos.php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/db.php';

$auth = new Auth();
$auth->authorize(['admin']);
$conn = (new Database())->connect();

// Deleção
if (isset($_GET['delete_id'])) {
    $stmt = $conn->prepare('DELETE FROM Produtos WHERE id = :id');
    $stmt->bindParam(':id', $_GET['delete_id'], PDO::PARAM_INT);
    $stmt->execute();
    header('Location: produtos.php');
    exit;
}

// Listagem
$stmt = $conn->query('SELECT * FROM Produtos ORDER BY nome');
$produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>
<style>
  body{
    background-color: #f8f9fa;
  }
  #produtos{
    border-bottom: 1px solid #ddd;
  }
</style>

<div class="d-flex">
  
  <main class="flex-grow-1 p-5">
    <h1 id="produtos" class="mb-4 display-4 fw-bold text-secondary">Produtos</h1>
    <p class="text-muted mb-4">Gerencie os produtos disponíveis no sistema.</p>
    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success">
        Produto adicionado com sucesso!
      </div>
    <?php endif; ?>
    <div class="d-flex justify-content-between align-items-center mb-4">
      <a href="produto_form.php" class="btn btn-primary">
        <i class="fa-solid fa-plus me-1"></i> Novo Produto
      </a>
    </div>

    <div class="table-responsive shadow-sm rounded-4 bg-white p-3">
      <table 
        class="table table-hover mb-0 align-middle" 
        style="min-width: 800px;"
      >
        <thead class="table-light">
          <tr>
            <th scope="col" style="width: 5%;">ID</th>
            <th scope="col" style="width: 35%;">Nome</th>
            <th scope="col" style="width: 25%;">Categoria</th>
            <th scope="col" style="width: 20%;">Preço Venda</th>
            <th scope="col" class="text-center" style="width: 15%;">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($produtos as $p): ?>
            <tr>
              <td><?= $p['id'] ?></td>
              <td><?= htmlspecialchars($p['nome']) ?></td>
              <td><?= htmlspecialchars($p['categoria']) ?></td>
              <td>Kz <?= number_format($p['preco_venda'], 2, ',', '.') ?></td>
              <td class="text-center">
                <a 
                  href="produto_form.php?id=<?= $p['id'] ?>" 
                  class="btn btn-sm btn-outline-secondary me-1"
                  title="Editar"
                >
                  <i class="fa-solid fa-pen-to-square"></i>
                </a>
                <a 
                  href="produtos.php?delete_id=<?= $p['id'] ?>" 
                  class="btn btn-sm btn-outline-danger"
                  onclick="return confirm('Tem certeza que deseja excluir este produto?');"
                  title="Excluir"
                >
                  <i class="fa-solid fa-trash"></i>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($produtos)): ?>
            <tr>
              <td colspan="5" class="text-center text-muted py-4">
                Nenhum produto cadastrado.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
