<?php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/db.php';

$auth = new Auth();
$auth->authorize(['admin']);
$conn = (new Database())->connect();

// Filtros de data
$dataDe  = $_GET['de']  ?? date('Y-m-01');
$dataAte = $_GET['ate'] ?? date('Y-m-d');

// Estatísticas globais (sem filtro) para bater com o Painel de Controle
$stats = $conn->prepare(
    "SELECT
        (SELECT COUNT(*) FROM Produtos) AS total_produtos,
        (SELECT COUNT(*) FROM Vendas) AS total_vendas,
        (SELECT SUM(total)   FROM Vendas) AS faturamento,
        (SELECT
            SUM(CASE WHEN tipo_movimento='entrada' THEN quantidade ELSE 0 END) -
            SUM(CASE WHEN tipo_movimento='saida' THEN quantidade ELSE 0 END)
         FROM Estoque) AS estoque_atual
    "
);
$stats->execute();
$stats = $stats->fetch(PDO::FETCH_ASSOC);

// Vendas no período selecionado
$vpd = $conn->prepare(
    "SELECT
        DATE(data_hora) AS dia,
        COUNT(*)        AS total_vendas,
        SUM(total)      AS total_valor
     FROM Vendas
     WHERE DATE(data_hora) BETWEEN :de AND :ate
     GROUP BY DATE(data_hora)
     ORDER BY dia DESC"
);
$vpd->execute(['de' => $dataDe, 'ate' => $dataAte]);
$vendasPorDia = $vpd->fetchAll(PDO::FETCH_ASSOC);

// Mais vendidos (sem filtro para manter top geral)
$mv = $conn->prepare(
    "SELECT
        p.nome,
        SUM(iv.quantidade) AS total_vendido
     FROM Itens_Venda iv
     JOIN Produtos p ON iv.id_produto = p.id
     GROUP BY iv.id_produto
     ORDER BY total_vendido DESC
     LIMIT 10"
);
$mv->execute();
$maisVendidos = $mv->fetchAll(PDO::FETCH_ASSOC);

// Estoque crítico (sem filtro)
$ec = $conn->query(
    "SELECT
        p.nome,
        SUM(CASE WHEN e.tipo_movimento='entrada' THEN e.quantidade ELSE 0 END) -
        SUM(CASE WHEN e.tipo_movimento='saida' THEN e.quantidade ELSE 0 END) AS saldo
     FROM Produtos p
     LEFT JOIN Estoque e ON p.id = e.id_produto
     GROUP BY p.id
     HAVING saldo <= 5
     ORDER BY saldo ASC"
);
$estoqueCritico = $ec->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include __DIR__ . '/../../includes/header.php'; ?>
<style>
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
  h1{
    border-bottom: 1px solid #ddd;
  }
</style>
<div class="d-flex">
  <main class="flex-grow-1 px-5 py-5">

    <h1 class="mb-4 display-4 fw-bold text-secondary">
      <i class="bi bi-bar-chart-line-fill me-2"></i>Relatórios
    </h1>
    <p class="text-muted mb-4">Visualize estatísticas e relatórios do sistema.</p>

    <form class="row g-3 mb-5" method="get">
      <div class="col-auto">
        <label class="form-label">De</label>
        <input type="date" name="de"  value="<?= htmlspecialchars($dataDe) ?>" class="form-control">
      </div>
      <div class="col-auto">
        <label class="form-label">Até</label>
        <input type="date" name="ate" value="<?= htmlspecialchars($dataAte) ?>" class="form-control">
      </div>

      <div class="col-auto align-self-end">
        <button type="submit" class="btn btn-primary">Filtrar</button>
      </div>
    </form>

    <div class="row g-4 mb-5">
      <div class="col-md-3">
        <div class="card text-white bg-info h-100">
          <div class="card-body">
            <h6 class="card-title">Produtos Cadastrados</h6>
            <h2><?= (int) $stats['total_produtos'] ?></h2>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white bg-primary h-100">
          <div class="card-body">
            <h6 class="card-title">Total de Vendas</h6>
            <h2><?= (int) $stats['total_vendas'] ?></h2>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white bg-success h-100">
          <div class="card-body">
            <h6 class="card-title">Faturamento (Kz)</h6>
            <h2><?= number_format((float)$stats['faturamento'] ?: 0, 2, ',', '.') ?></h2>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-white bg-warning h-100">
          <div class="card-body">
            <h6 class="card-title">Estoque Atual</h6>
            <h2><?= (int) $stats['estoque_atual'] ?></h2>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <!-- Vendas por Dia -->
      <div class="col-12 col-lg-8">
        <div class="card card-custom bg-white border-0 h-100">
          <div class="card-header bg-dark text-white rounded-top-2">
            <h5 class="mb-0">
              <i class="bi bi-calendar-week-fill me-1"></i>Vendas por Dia
            </h5>
          </div>
          <div class="card-body">
            <table class="table table-hover align-middle mb-0" style="font-size:1rem;">
              <thead class="table-light">
                <tr><th>Data</th><th>Qtde</th><th class="text-end">Valor</th></tr>
              </thead>
              <tbody>
                <?php foreach ($vendasPorDia as $vd): ?>
                <tr style="cursor:pointer;" onclick="location.href='vendas_por_dia.php?data=<?= $vd['dia'] ?>'">
                  <td><?= $vd['dia'] ?></td>
                  <td><?= $vd['total_vendas'] ?></td>
                  <td class="text-end">Kz <?= number_format((float)$vd['total_valor'] ?: 0, 2, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Painel lateral: Mais Vendidos e Estoque Crítico -->
      <div class="col-12 col-lg-4">
        <div class="row g-4 h-100">
          <!-- Mais Vendidos -->
          <div class="col-lg-6">
            <div class="card card-custom bg-white border-0 h-100">
              <div class="card-header bg-secondary text-white rounded-top-2">
                <h5 class="mb-0">
                  <i class="fa-solid fa-star me-1"></i>Mais Vendidos
                </h5>
              </div>
              <div class="card-body">
                <table class="table table-hover align-middle mb-0" style="font-size:1rem;">
                  <thead class="table-light"><tr><th>Produto</th><th class="text-end">Qtde</th></tr></thead>
                  <tbody>
                    <?php foreach ($maisVendidos as $mv): ?>
                    <tr>
                      <td><?= htmlspecialchars($mv['nome']) ?></td>
                      <td class="text-end"><?= $mv['total_vendido'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <!-- Estoque Crítico -->
          <div class="col-lg-6">
            <div class="card card-custom bg-white border-0 h-100">
              <div class="card-header bg-danger text-white rounded-top-2">
                <h5 class="mb-0">
                  <i class="bi bi-exclamation-triangle-fill me-1"></i>Estoque Crítico
                </h5>
              </div>
              <div class="card-body">
                <table class="table table-hover align-middle mb-0" style="font-size:1rem;">
                  <thead class="table-light"><tr><th>Produto</th><th class="text-end">Saldo</th></tr></thead>
                  <tbody>
                    <?php foreach ($estoqueCritico as $ec): ?>
                    <tr>
                      <td><?= htmlspecialchars($ec['nome']) ?></td>
                      <td class="text-end"><?= $ec['saldo'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </main>
</div>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
