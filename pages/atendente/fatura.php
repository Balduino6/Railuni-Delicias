<?php
// pages/admin/fatura.php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/db.php';

$auth = new Auth();
$auth->authorize(['admin','atendente']);
$conn = (new Database())->connect();

// Configurações da Empresa
$empresa = [
    'nome'    => 'Taska da Pandora',
    'endereco'=> 'Av. Principal, 1234, Luanda',
    'nif'     => '123456789',
    'telefone'=> '+244 923 000 000',
    'email'   => 'contato@taskapandora.co.ao'
];

// Busca cabeçalho da venda
$idVenda = $_GET['id'] ?? null;
$stmt = $conn->prepare(
  'SELECT v.id, v.data_hora, v.total, u.nome AS atendente
   FROM Vendas v
   JOIN Usuarios u ON v.id_usuario = u.id
   WHERE v.id = :id'
);
$stmt->execute([':id' => $idVenda]);
$venda = $stmt->fetch(PDO::FETCH_ASSOC);

// Busca itens da venda
$stmt2 = $conn->prepare(
  'SELECT p.nome, iv.quantidade, iv.preco_unitario
   FROM Itens_Venda iv
   JOIN Produtos p ON iv.id_produto = p.id
   WHERE iv.id_venda = :id'
);
$stmt2->execute([':id' => $idVenda]);
$itens = $stmt2->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../../includes/header.php';
?>

<style>
  /* Tamanho A4 e margens para impressão */
  @page { size: A4 portrait; margin: 15mm; }
  /* Fundo uniforme */
  body { background-color: #f0f2f5; }
  /* Invoice card styling */
  .invoice-card {
    width: 210mm;
    max-width: 100%;
    margin: 0 auto;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    background: #fff;
  }
  /* Print only invoice card */
  @media print {
    body * { visibility: hidden; }
    .invoice-card, .invoice-card * { visibility: visible; }
    .invoice-card { position: absolute; top: 0; left: 0; width: 100%; }
  }
</style>

<main class="flex-grow-1 p-5">
  <div class="card invoice-card mx-auto rounded-4">
    <div class="card-body p-5">
      <div class="row mb-4">
        <div class="col-6">
          <h2 class="mb-1 fw-bold"><?= htmlspecialchars($empresa['nome']) ?></h2>
          <p class="mb-0">NIF: <?= htmlspecialchars($empresa['nif']) ?></p>
          <p class="mb-0"><?= htmlspecialchars($empresa['endereco']) ?></p>
          <p class="mb-0">Tel: <?= htmlspecialchars($empresa['telefone']) ?> | E-mail: <?= htmlspecialchars($empresa['email']) ?></p>
        </div>
        <div class="col-6 text-end">
          <h3 class="fw-bold">Fatura #<?= $venda['id'] ?></h3>
          <p class="mb-0">Data: <?= date('d/m/Y H:i', strtotime($venda['data_hora'])) ?></p>
          <p class="mb-0">Atendente: <?= htmlspecialchars($venda['atendente']) ?></p>
        </div>
      </div>

      <table class="table table-bordered mb-4">
        <thead class="table-secondary">
          <tr>
            <th>Produto</th>
            <th class="text-center">Qtd.</th>
            <th class="text-end">Preço Unit. (Kz)</th>
            <th class="text-end">Subtotal (Kz)</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($itens as $item):
            $subtotal = $item['quantidade'] * $item['preco_unitario'];
          ?>
          <tr>
            <td><?= htmlspecialchars($item['nome']) ?></td>
            <td class="text-center"><?= $item['quantidade'] ?></td>
            <td class="text-end"><?= number_format($item['preco_unitario'],2,',','.') ?></td>
            <td class="text-end"><?= number_format($subtotal,2,',','.') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <div class="d-flex justify-content-end mb-4">
        <h4 class="fw-bold">Total: Kz <?= number_format($venda['total'],2,',','.') ?></h4>
      </div>

      <div class="row mt-5 no-print">
        <div class="col-6">
          <p class="mb-0 small">Obrigado pela preferência!</p>
        </div>
        <div class="col-6 text-end">
          <button class="btn btn-outline-secondary me-2" onclick="window.print()">
            <i class="fa-solid fa-print me-1"></i> Imprimir
          </button>
          <a href="vendas.php" class="btn btn-primary">
            <i class="fa-solid fa-arrow-left me-1"></i>Voltar
          </a>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
