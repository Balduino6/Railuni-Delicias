<?php
// pages/admin/fechamento_mensal.php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/db.php';

$auth = new Auth();
$auth->authorize(['admin']);
$user = $auth->user();
$conn = (new Database())->connect();

// Mês alvo (YYYY-MM)
$input = $_GET['mes'] ?? date('Y-m');
[$year, $month] = explode('-', $input);

// Calcula primeiro e último dia do mês
$start = "$year-$month-01";
$end   = date('Y-m-t', strtotime($start));

// 1) Total Vendas no mês
$stmtV = $conn->prepare("
    SELECT SUM(iv.quantidade * iv.preco_unitario) AS total_vendas
    FROM vendas v
    JOIN itens_venda iv ON iv.id_venda = v.id
    WHERE DATE(v.data_hora) BETWEEN :start AND :end
");
$stmtV->execute([':start'=>$start,':end'=>$end]);
$totalVendas = $stmtV->fetchColumn() ?: 0;

// 2) Despesas no mês
$custoDespesas = 0;
$tableExists = $conn->query("
    SELECT COUNT(*) FROM information_schema.TABLES
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'custos_diarios'
")->fetchColumn();
if ($tableExists) {
    $stmtC = $conn->prepare("
        SELECT SUM(valor) AS despesas
        FROM custos_diarios
        WHERE data BETWEEN :start AND :end
    ");
    $stmtC->execute([':start'=>$start,':end'=>$end]);
    $custoDespesas = $stmtC->fetchColumn() ?: 0;
}

// 3) Lucro Líquido
$lucro = $totalVendas - $custoDespesas;

// 4) Itens vendidos no mês
$stmtItens = $conn->prepare("
    SELECT p.nome AS produto,
           SUM(iv.quantidade) AS quantidade,
           SUM(iv.quantidade * iv.preco_unitario) AS subtotal
    FROM vendas v
    JOIN itens_venda iv ON iv.id_venda = v.id
    JOIN produtos p   ON p.id = iv.id_produto
    WHERE DATE(v.data_hora) BETWEEN :start AND :end
    GROUP BY p.nome
    ORDER BY p.nome
");
$stmtItens->execute([':start'=>$start,':end'=>$end]);
$itensVendidos = $stmtItens->fetchAll(PDO::FETCH_ASSOC);

// 5) Grava fechamento mensal
$stmtF = $conn->prepare("
    INSERT INTO fechamentos (id_usuario, data_hora, total)
    VALUES (:id_usuario, :data_hora, :total)
    ON DUPLICATE KEY UPDATE
      total = VALUES(total),
      data_hora = VALUES(data_hora)
");
$stmtF->execute([
    ':id_usuario'=>$user['id'],
    ':data_hora'=>"$year-$month-01 00:00:00",
    ':total'=>$lucro
]);

include __DIR__ . '/../../includes/header.php';
?>

<div class="container" id="print-area">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2>Fechamento Mensal (<?= htmlspecialchars("$year-$month") ?>)</h2>
    <button onclick="window.print()" class="btn btn-secondary">Imprimir</button>
  </div>
  <p><strong>Período:</strong> <?= $start ?> até <?= $end ?></p>
  <p><strong>Atendente:</strong> <?= htmlspecialchars($user['nome']) ?></p>

  <form class="row g-3 mb-4">
    <div class="col-auto">
      <label for="mes" class="form-label">Mês</label>
      <input type="month" id="mes" name="mes" class="form-control" value="<?= htmlspecialchars($input) ?>">
    </div>
    <div class="col-auto align-self-end">
      <button class="btn btn-primary">Filtrar</button>
    </div>
  </form>

  <div class="row mb-4">
    <div class="col-md-4">
      <div class="card text-center">
        <div class="card-header">Total Vendas</div>
        <div class="card-body fs-4">Kz <?= number_format($totalVendas,2,',','.') ?></div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-center">
        <div class="card-header">Total Despesas</div>
        <div class="card-body fs-4">Kz <?= number_format($custoDespesas,2,',','.') ?></div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card text-center">
        <div class="card-header">Lucro Líquido</div>
        <div class="card-body fs-4 <?= $lucro>=0?'text-success':'text-danger' ?>">
          Kz <?= number_format($lucro,2,',','.') ?>
        </div>
      </div>
    </div>
  </div>

  <h4>Itens Vendidos</h4>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Produto</th>
        <th class="text-end">Quantidade</th>
        <th class="text-end">Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($itensVendidos)): ?>
        <tr><td colspan="3" class="text-center">Nenhum item vendido.</td></tr>
      <?php else: foreach($itensVendidos as $item): ?>
        <tr>
          <td><?= htmlspecialchars($item['produto']) ?></td>
          <td class="text-end"><?= (int)$item['quantidade'] ?></td>
          <td class="text-end">Kz <?= number_format($item['subtotal'],2,',','.') ?></td>
        </tr>
      <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

<!-- Em footer.php (antes de </body>): -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<style>
@media print {
  body * { visibility: hidden; }
  #print-area, #print-area * { visibility: visible; }
  #print-area { position: absolute; top:0; left:0; width:100%; }
  #print-area button { display: none; }
}
</style>
