<?php
// pages/admin/vendas.php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/db.php';

$auth = new Auth();
$auth->authorize(['admin','atendente']);
$conn = (new Database())->connect();

// Ao finalizar (button name="vender"), grava e redireciona para fatura
if (isset($_POST['vender'])) {
    $conn->beginTransaction();

    // Calcula total no servidor também, por segurança
    $total = 0;
    foreach ($_POST['produtos'] as $item) {
        $total += $item['qt'] * $item['preco'];
    }

    $stmt = $conn->prepare(
        'INSERT INTO Vendas (id_usuario, total) VALUES (:user, :total)'
    );
    $stmt->execute([
        ':user'  => $auth->user()['id'],
        ':total' => $total
    ]);
    $idVenda = $conn->lastInsertId();

    $stmtItem = $conn->prepare(
        'INSERT INTO Itens_Venda 
            (id_venda, id_produto, quantidade, preco_unitario)
         VALUES 
            (:venda, :prod, :qt, :preco)'
    );
    $stmtEst = $conn->prepare(
        'INSERT INTO Estoque 
            (id_produto, tipo_movimento, quantidade, id_usuario)
         VALUES 
            (:prod, "saida", :qt, :user)'
    );

    foreach ($_POST['produtos'] as $item) {
        $stmtItem->execute([
            ':venda'  => $idVenda,
            ':prod'   => $item['id'],
            ':qt'     => $item['qt'],
            ':preco'  => $item['preco']
        ]);
        $stmtEst->execute([
            ':prod'  => $item['id'],
            ':qt'    => $item['qt'],
            ':user'  => $auth->user()['id']
        ]);
    }

    $conn->commit();
    header('Location: fatura.php?id=' . $idVenda);
    exit;
}

// Busca produtos e histórico
$produtos = $conn
  ->query('SELECT id, nome, preco_venda FROM Produtos ORDER BY nome')
  ->fetchAll(PDO::FETCH_ASSOC);

$vendas = $conn
  ->query(
    'SELECT v.id, v.data_hora, u.nome AS atendente, v.total
     FROM Vendas v
     JOIN Usuarios u ON v.id_usuario = u.id
     ORDER BY v.data_hora DESC'
  )
  ->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>

<style>
  body {
    background-color: #f0f2f5;
  }
  #vendas {
    border-bottom: 1px solid #ddd;
  }

</style>

<div class="d-flex">
  <main class="flex-grow-1 p-5">
    <h1 id="vendas" class="mb-4 display-4 fw-bold text-secondary">Vendas</h1>

    <!-- Formulário e Preview -->
    <div class="card shadow-sm rounded-4 mb-5">
      <div class="card-body">
        <h2 class="h5 mb-3">Registrar Venda</h2>

        <form id="formVenda" method="post" class="mb-4">
          <div id="itensVenda" class="row g-3"></div>

          <!-- preview da fatura -->
          <div id="preview" class="mt-4 d-none">
            <h4>Preview da Venda</h4>
            <ul id="previewItens" class="list-group mb-3"></ul>
            <div class="d-flex justify-content-end">
              <strong>Total: Kz <span id="previewTotal">0.00</span></strong>
            </div>
          </div>

          <div class="d-flex mt-3">
            <button type="button" id="addItem"   class="btn btn-outline-primary me-2">
              <i class="fa-solid fa-plus me-1"></i> Adicionar Item
            </button>
            <button type="button" id="calcPreview" class="btn btn-secondary me-2">
              <i class="fa-solid fa-eye me-1"></i> Mostrar Preview
            </button>
            <button type="submit" name="vender" class="btn btn-success">
              <i class="fa-solid fa-check me-1"></i> Finalizar Venda
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Histórico de Vendas -->
    <div class="table-responsive shadow-sm rounded-4 bg-white p-3">
      <table class="table table-hover align-middle mb-0" style="min-width: 600px;">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Data</th>
            <th>Atendente</th>
            <th class="text-end">Total (Kz)</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($vendas): foreach ($vendas as $v): ?>
            <tr>
              <td><?= $v['id'] ?></td>
              <td><?= $v['data_hora'] ?></td>
              <td><?= htmlspecialchars($v['atendente']) ?></td>
              <td class="text-end">
                <?= number_format((float)$v['total'], 2, ',', '.') ?>
              </td>
            </tr>
          <?php endforeach; else: ?>
            <tr>
              <td colspan="4" class="text-center text-muted py-4">
                Ainda não há vendas registradas.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<script>
const produtos      = <?= json_encode($produtos, JSON_HEX_TAG) ?>;
const itensVenda    = document.getElementById('itensVenda');
const preview       = document.getElementById('preview');
const previewItens  = document.getElementById('previewItens');
const previewTotal  = document.getElementById('previewTotal');
let count = 0;

// Adiciona linha de item
document.getElementById('addItem').addEventListener('click', () => {
  const idx = count++;
  const div = document.createElement('div');
  div.className = 'col-12 d-flex gap-2 align-items-center';
  div.innerHTML = `
    <select name="produtos[${idx}][id]" class="form-select" required>
      <option value="" disabled selected>Produto...</option>
      ${produtos.map(p => `<option value="${p.id}" data-preco="${p.preco_venda}">${p.nome}</option>`).join('')}
    </select>
    <input type="number" name="produtos[${idx}][qt]" class="form-control" min="1" value="1" required>
    <input type="hidden" name="produtos[${idx}][preco]" value="">
    <button type="button" class="btn btn-outline-danger" onclick="this.parentNode.remove()">
      <i class="fa-solid fa-trash"></i>
    </button>
  `;
  const select = div.querySelector('select');
  const hidden = div.querySelector('input[type=hidden]');
  select.addEventListener('change', () => {
    hidden.value = select.selectedOptions[0].dataset.preco;
  });
  itensVenda.appendChild(div);
});

// Calcula e exibe preview
document.getElementById('calcPreview').addEventListener('click', () => {
  previewItens.innerHTML = '';
  let total = 0;
  document.querySelectorAll('#itensVenda > div').forEach(row => {
    const sel   = row.querySelector('select');
    const qt    = parseFloat(row.querySelector('input[type=number]').value) || 0;
    const preco = parseFloat(row.querySelector('input[type=hidden]').value) || 0;
    if (sel.value && qt > 0 && preco > 0) {
      const nome     = sel.selectedOptions[0].text;
      const subtotal = qt * preco;
      total += subtotal;
      const li = document.createElement('li');
      li.className = 'list-group-item d-flex justify-content-between';
      li.textContent = `${nome} × ${qt}`;
      const span = document.createElement('span');
      span.textContent = `Kz ${subtotal.toFixed(2)}`;
      li.appendChild(span);
      previewItens.appendChild(li);
    }
  });
  previewTotal.textContent = total.toFixed(2);
  preview.classList.remove('d-none');
});
</script>
