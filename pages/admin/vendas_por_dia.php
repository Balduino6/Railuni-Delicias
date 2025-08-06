<?php
// pages/admin/vendas_por_dia.php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/db.php';

$auth = new Auth();
$auth->authorize(['admin','atendente']);
$conn = (new Database())->connect();

$data = $_GET['data'] ?? null;
if (!$data) {
    header('Location: vendas.php');
    exit;
}

// Busca vendas da data selecionada
$stmt = $conn->prepare(
    "SELECT 
        v.id, 
        v.data_hora, 
        v.total, 
        u.nome AS atendente
     FROM Vendas v
     JOIN Usuarios u ON v.id_usuario = u.id
     WHERE DATE(v.data_hora) = :data
     ORDER BY v.data_hora DESC"
);
$stmt->execute([':data' => $data]);
$vendas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>
<div class="d-flex">
  <?php include __DIR__ . '/../../includes/sidebar.php'; ?>
  <main class="flex-grow-1 p-5">

    <div class="container-fluid mt-4">
      <h1 class="text-primary mb-4">
        <i class="bi bi-receipt-cutoff me-2"></i>
        Vendas em <?= htmlspecialchars($data) ?>
      </h1>
      <div class="card shadow-sm rounded-4">
        <div class="card-body p-3">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>ID</th>
                  <th>Hora</th>
                  <th>Atendente</th>
                  <th class="text-end">Total (Kz)</th>
                  <th class="text-center">Ações</th>
                </tr>
              </thead>
              <tbody>
                <?php if ($vendas): foreach ($vendas as $v): ?>
                <tr>
                  <td><?= $v['id'] ?></td>
                  <td><?= date('H:i', strtotime($v['data_hora'])) ?></td>
                  <td><?= htmlspecialchars($v['atendente']) ?></td>
                  <td class="text-end">
                    <?= number_format((float)$v['total'], 2, ',', '.') ?>
                  </td>
                  <td class="text-center">
                    <a href="fatura.php?id=<?= $v['id'] ?>" class="btn btn-sm btn-outline-primary">
                      <i class="bi bi-printer-fill"></i> Fatura
                    </a>
                  </td>
                </tr>
                <?php endforeach; else: ?>
                <tr>
                  <td colspan="5" class="text-center text-muted py-4">
                    Nenhuma venda encontrada nesta data.
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
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
