<?php
// pages/admin/dashboard.php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/db.php';

$auth = new Auth();
$auth->authorize(['admin', 'atendente']);
$conn = (new Database())->connect();

// Dados estatísticos
$totalProdutos    = $conn->query("SELECT COUNT(*) FROM Produtos")->fetchColumn();
$totalVendas      = $conn->query("SELECT COUNT(*) FROM Vendas")->fetchColumn();
$totalEstoque     = $conn->query(
    "SELECT SUM(CASE WHEN tipo_movimento='entrada' THEN quantidade ELSE 0 END) -
            SUM(CASE WHEN tipo_movimento='saida' THEN quantidade ELSE 0 END)
     FROM Estoque"
)->fetchColumn();
$totalFaturamento = $conn->query("SELECT SUM(total) FROM Vendas")->fetchColumn();

// Vendas recentes
$recentVendas = $conn->query(
    "SELECT v.id, v.data_hora, u.nome AS atendente, v.total
     FROM Vendas v
     JOIN Usuarios u ON v.id_usuario = u.id
     ORDER BY v.data_hora DESC LIMIT 5"
)->fetchAll(PDO::FETCH_ASSOC);

// Top 5 produtos vendidos
$topProdutos = $conn->query(
    "SELECT p.nome, SUM(iv.quantidade) AS qt_vendida
     FROM Itens_Venda iv
     JOIN Produtos p ON iv.id_produto=p.id
     GROUP BY iv.id_produto
     ORDER BY qt_vendida DESC LIMIT 5"
)->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include_once __DIR__ . '/../../includes/header.php'; ?>
<style>
  /* Cor única para o corpo */
  body {
    background-color: #f0f2f5;
  }
  .card-custom {
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    transition: transform 0.2s;
  }
  .card-custom:hover {
    transform: translateY(-4px);
  }
  .icon-circle {
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
  }
    #dashboard{
    border-bottom: 1px solid #ddd;
  }
</style>
<div class="d-flex">
  <?php include_once __DIR__ . '/../../includes/sidebar.php'; ?>
  <main class="flex-grow-1 p-5">
    <h1 id="dashboard" class="mb-4 display-4 fw-bold text-secondary">Painel de Controle</h1>
    <p class="text-muted mb-4">Visão geral do sistema e estatísticas.</p>

    <!-- Cards estatísticos -->
    <div class="row g-4 mb-5">
      <div class="col-12 col-md-6 col-xl-3">
        <div class="card card-custom bg-white border-0 h-100">
          <div class="card-body d-flex align-items-center">
            <div class="icon-circle bg-primary text-white me-3">
              <i class="fa-solid fa-box fa-lg"></i>
            </div>
            <div>
              <h6 class="text-uppercase text-muted mb-1">Produtos</h6>
              <h2 class="fw-bold mb-0"><?= $totalProdutos ?></h2>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-6 col-xl-3">
        <div class="card card-custom bg-white border-0 h-100">
          <div class="card-body d-flex align-items-center">
            <div class="icon-circle bg-success text-white me-3">
              <i class="fa-solid fa-shopping-cart fa-lg"></i>
            </div>
            <div>
              <h6 class="text-uppercase text-muted mb-1">Vendas</h6>
              <h2 class="fw-bold mb-0"><?= $totalVendas ?></h2>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-6 col-xl-3">
        <div class="card card-custom bg-white border-0 h-100">
          <div class="card-body d-flex align-items-center">
            <div class="icon-circle bg-warning text-white me-3">
              <i class="fa-solid fa-layer-group fa-lg"></i>
            </div>
            <div>
              <h6 class="text-uppercase text-muted mb-1">Estoque</h6>
              <h2 class="fw-bold mb-0"><?= $totalEstoque ?></h2>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12 col-md-6 col-xl-3">
        <div class="card card-custom bg-white border-0 h-100">
          <div class="card-body d-flex align-items-center">
            <div class="icon-circle bg-info text-white me-3">
              <i class="fa-solid fa-dollar-sign fa-lg"></i>
            </div>
            <div>
              <h6 class="text-uppercase text-muted mb-1">Faturamento</h6>
              <h2 class="fw-bold mb-0">Kz <?= number_format((float)$totalFaturamento,2,',','.') ?></h2>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <!-- Vendas Recentes -->
      <div class="col-lg-6">
        <div class="card card-custom bg-white border-0 h-100">
          <div class="card-header bg-dark text-white rounded-top-2">
            <h5 class="mb-0"><i class="fa-solid fa-clock-rotate-left me-1"></i>Vendas Recentes</h5>
          </div>
          <div class="card-body p-4">
            <div class="table-responsive">
              <table class="table mb-0">
                <thead class="table-light">
                  <tr><th>ID</th><th>Data/Hora</th><th>Atendente</th><th class="text-end">Total</th></tr>
                </thead>
                <tbody>
                  <?php foreach ($recentVendas as $v): ?>
                  <tr class="align-middle">
                    <td><?= $v['id'] ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($v['data_hora'])) ?></td>
                    <td><?= htmlspecialchars($v['atendente']) ?></td>
                    <td class="text-end text-success fw-semibold">Kz <?= number_format($v['total'],2,',','.') ?></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Produtos -->
      <div class="col-lg-6">
        <div class="card card-custom bg-white border-0 h-100">
          <div class="card-header bg-secondary text-white rounded-top-2">
            <h5 class="mb-0"><i class="fa-solid fa-star me-1"></i>Top 5 Produtos</h5>
          </div>
          <div class="card-body p-4">
            <ul class="list-group list-group-flush">
              <?php foreach ($topProdutos as $p): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <?= htmlspecialchars($p['nome']) ?>
                <span class="badge bg-primary rounded-pill"><?= $p['qt_vendida'] ?></span>
              </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>
    </div>

  </main>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
