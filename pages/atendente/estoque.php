<?php
// pages/atendente/estoque.php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/db.php';

$auth = new Auth();
$auth->authorize(['atendente']);
$user = $auth->user();
$conn = (new Database())->connect();

// Registrar apenas SAÍDA de estoque
if (isset($_POST['movimento'])) {
    $stmt = $conn->prepare('
        INSERT INTO Estoque (id_produto, tipo_movimento, quantidade, id_usuario)
        VALUES (:prod, "saida", :qt, :user)
    ');
    $stmt->execute([
        ':prod' => $_POST['id_produto'],
        ':qt'   => $_POST['quantidade'],
        ':user' => $user['id']
    ]);
    header('Location: estoque.php');
    exit;
}

// Carregar saldo atual por produto
$stmt = $conn->prepare('
    SELECT
      p.id,
      p.nome,
      COALESCE(SUM(CASE WHEN e.tipo_movimento = "entrada" THEN e.quantidade ELSE 0 END),0) AS entradas,
      COALESCE(SUM(CASE WHEN e.tipo_movimento = "saida"   THEN e.quantidade ELSE 0 END),0) AS saidas
    FROM Produtos p
    LEFT JOIN Estoque e ON p.id = e.id_produto
    GROUP BY p.id, p.nome
    ORDER BY p.nome
');
$stmt->execute();
$estoques = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>

<main class="flex-grow-1 p-5">
  <h1 class="mb-4 display-6 fw-bold text-secondary">Saída de Estoque</h1>

  <div class="card shadow-sm rounded-4 mb-5 mx-auto" style="max-width: 600px;">
    <div class="card-body">
      <h2 class="h6 mb-3">Registrar Saída</h2>
      <form method="post" class="row g-3 align-items-end">
        <div class="col-md-6">
          <label for="id_produto" class="form-label">Produto</label>
          <select id="id_produto" name="id_produto" class="form-select" required>
            <option value="" disabled selected>Selecione...</option>
            <?php foreach ($estoques as $e): ?>
              <?php $saldo = $e['entradas'] - $e['saidas']; ?>
              <option value="<?= $e['id'] ?>">
                <?= htmlspecialchars($e['nome']) ?> (Disp.: <?= $saldo ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3 form-floating">
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
        <div class="col-md-3 text-end">
          <button type="submit" name="movimento" class="btn btn-danger w-100">
            <i class="fa-solid fa-minus me-1"></i> Registrar
          </button>
        </div>
      </form>
    </div>
  </div>

  <div class="table-responsive shadow-sm rounded-4 bg-white p-3">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Produto</th>
          <th class="text-end">Entradas</th>
          <th class="text-end">Saídas</th>
          <th class="text-end">Saldo</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($estoques)): ?>
          <tr>
            <td colspan="5" class="text-center text-muted py-4">
              Nenhum registro de estoque.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($estoques as $e): 
            $saldo = $e['entradas'] - $e['saidas'];
          ?>
            <tr>
              <td><?= $e['id'] ?></td>
              <td><?= htmlspecialchars($e['nome']) ?></td>
              <td class="text-end"><?= $e['entradas'] ?></td>
              <td class="text-end"><?= $e['saidas'] ?></td>
              <td class="text-end"><?= $saldo ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
