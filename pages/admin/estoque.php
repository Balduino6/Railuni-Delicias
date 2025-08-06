<?php
// pages/admin/estoque.php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/db.php';

$auth = new Auth();
$auth->authorize(['admin']);
$conn = (new Database())->connect();

// Registrar movimento de estoque
if (isset($_POST['movimento'])) {
    $stmt = $conn->prepare('
        INSERT INTO Estoque (id_produto, tipo_movimento, quantidade, id_usuario)
        VALUES (:prod, :tipo, :qt, :user)
    ');
    $stmt->execute([
        ':prod' => $_POST['id_produto'],
        ':tipo' => $_POST['tipo'],
        ':qt'   => $_POST['quantidade'],
        ':user' => $auth->user()['id']
    ]);
    header('Location: estoque.php');
    exit;
}

// Carregar dados de estoque por produto
$stmt = $conn->prepare('
    SELECT
      p.id,
      p.nome,
      COALESCE(SUM(CASE WHEN e.tipo_movimento = "entrada" THEN e.quantidade ELSE 0 END),0) AS total_entradas,
      COALESCE(SUM(CASE WHEN e.tipo_movimento = "saida"   THEN e.quantidade ELSE 0 END),0) AS total_saidas
    FROM Produtos p
    LEFT JOIN Estoque e ON p.id = e.id_produto
    GROUP BY p.id, p.nome
    ORDER BY p.nome
');
$stmt->execute();
$estoques = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>

<style>
  body {
    background-color: #f0f2f5;
  } 
  #controle-estoque{
    border-bottom: 1px solid #ddd;
  }
</style>

<div class="d-flex">

  <main class="flex-grow-1 p-5">
    <h1 id="controle-estoque" class="mb-4 display-4 fw-bold text-secondary">Controle de Estoque</h1>
    <p class="text-muted mb-4">Gerencie os produtos e seus movimentos de entrada e saída.</p>

    <div class="row g-4 mb-5">

    <div class="card shadow-sm rounded-4 mb-5 mx-auto" style="max-width: 800px;">
      <div class="card-body">
        <h2 class="h5 mb-3">Registrar Movimento</h2>
        <form method="post" class="row g-3 align-items-end">
          <div class="col-md-5">
            <label for="id_produto" class="form-label">Produto</label>
            <select id="id_produto" name="id_produto" class="form-select" required>
              <option value="" disabled selected>Selecione...</option>
              <?php foreach ($estoques as $e): ?>
                <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nome']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label for="tipo" class="form-label">Tipo</label>
            <select id="tipo" name="tipo" class="form-select" required>
              <option value="entrada">Entrada</option>
              <option value="saida">Saída</option>
            </select>
          </div>
          <div class="col-md-2 form-floating">
            <input
              type="number"
              id="quantidade"
              name="quantidade"
              class="form-control"
              placeholder="Qtd"
              min="1"
              required
            >
            <label for="quantidade">Qtd.</label>
          </div>
          <div class="col-md-2 text-end">
            <button type="submit" name="movimento" class="btn btn-primary w-100">
              <i class="fa-solid fa-flask-potion me-1"></i> Registrar
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="table-responsive shadow-sm rounded-4 bg-white p-3">
      <table class="table table-hover align-middle mb-0" style="min-width: 700px;">
        <thead class="table-light">
          <tr>
            <th style="width: 10%;">ID</th>
            <th style="width: 50%;">Produto</th>
            <th class="text-end" style="width: 10%;">Entradas</th>
            <th class="text-end" style="width: 10%;">Saídas</th>
            <th class="text-end" style="width: 10%;">Saldo</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($estoques as $e): 
            $saldo = $e['total_entradas'] - $e['total_saidas'];
          ?>
            <tr>
              <td><?= $e['id'] ?></td>
              <td><?= htmlspecialchars($e['nome']) ?></td>
              <td class="text-end"><?= $e['total_entradas'] ?></td>
              <td class="text-end"><?= $e['total_saidas'] ?></td>
              <td class="text-end"><?= $saldo ?></td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($estoques)): ?>
            <tr>
              <td colspan="5" class="text-center text-muted py-4">
                Nenhum registro de estoque encontrado.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
