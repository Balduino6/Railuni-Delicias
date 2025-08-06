<?php
// fechamento_diario.php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/db.php';

$auth = new Auth();
$auth->authorize(['admin']);
$user = $auth->user();
$conn = (new Database())->connect();

// Data alvo
$data = $_GET['data'] ?? date('Y-m-d');

// 1) Calcula total de vendas do dia
$stmtV = $conn->prepare("
  SELECT SUM(total) AS total_vendas
  FROM vendas
  WHERE DATE(data_hora) = :data
");
$stmtV->execute([':data' => $data]);
$rowV = $stmtV->fetch(PDO::FETCH_ASSOC);
$totalVendas = $rowV['total_vendas'] ?? 0;

// 2) Calcula total de custos do dia (caso exista tabela)
$totalCustos = 0;
$tableExists = $conn->query("
  SELECT COUNT(*)
  FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'custos_diarios'
")->fetchColumn();
if ($tableExists) {
  $stmtC = $conn->prepare("
    SELECT SUM(valor) AS total_custos
    FROM custos_diarios
    WHERE data = :data
  ");
  $stmtC->execute([':data' => $data]);
  $rowC = $stmtC->fetch(PDO::FETCH_ASSOC);
  $totalCustos = $rowC['total_custos'] ?? 0;
}

$lucro = $totalVendas - $totalCustos;

// 3) Insere ou atualiza o fechamento diário
$stmtF = $conn->prepare("
  INSERT INTO fechamentos (id_usuario, data_hora, total)
  VALUES (:id_usuario, :data_hora, :total)
  ON DUPLICATE KEY UPDATE
    total = VALUES(total),
    data_hora = VALUES(data_hora)
");
// Para evitar duplicação, certifique-se de criar uma UNIQUE:
// ALTER TABLE fechamentos ADD UNIQUE unq_user_date (id_usuario, DATE(data_hora));
$stmtF->execute([
  ':id_usuario' => $user['id'],
  ':data_hora'  => $data . ' 23:59:59',
  ':total'      => $lucro
]);

include __DIR__ . '/../../includes/header.php';
?>

<h2>Fechamento Diário (<?= htmlspecialchars($data) ?>)</h2>
<form class="row g-3 mb-4">
  <div class="col-auto">
    <label for="data" class="form-label">Data</label>
    <input type="date" id="data" name="data" class="form-control"
           value="<?= htmlspecialchars($data) ?>">
  </div>
  <div class="col-auto align-self-end">
    <button class="btn btn-primary">Filtrar</button>
  </div>
</form>

<div class="row">
  <div class="col-md-4">
    <div class="card text-center mb-3">
      <div class="card-header">Total Vendas</div>
      <div class="card-body fs-4">Kz <?= number_format($totalVendas, 2, ',', '.') ?></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-center mb-3">
      <div class="card-header">Total Custos</div>
      <div class="card-body fs-4">Kz <?= number_format($totalCustos, 2, ',', '.') ?></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card text-center mb-3">
      <div class="card-header">Lucro</div>
      <div class="card-body fs-4 <?= $lucro >= 0 ? 'text-success' : 'text-danger' ?>">
        Kz <?= number_format($lucro, 2, ',', '.') ?>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
