<?php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/db.php';
$auth = new Auth();
$auth->authorize(['admin','atendente']);
$conn = (new Database())->connect();

// Listar mesas e seus pedidos abertos
'tbl' = 'Mesas e Pedidos';
$mesas = $conn->query(
    "SELECT m.id, m.nome AS mesa, COUNT(p.id) AS pedidos_abertos
     FROM Mesas m
     LEFT JOIN Pedidos p ON p.id_mesa = m.id AND p.status = 'aberto'
     GROUP BY m.id, m.nome"
)->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<h1>Mesas</h1>
<a href="mesa_form.php" class="btn">+ Nova Mesa</a>
<table class="table">
  <thead><tr><th>ID</th><th>Mesa</th><th>Pedidos Abertos</th><th>Ações</th></tr></thead>
  <tbody>
    <?php foreach ($mesas as $m): ?>
    <tr>
      <td><?= $m['id'] ?></td>
      <td><?= htmlspecialchars($m['mesa']) ?></td>
      <td><?= $m['pedidos_abertos'] ?></td>
      <td>
        <a href="pedido_form.php?mesa=<?= $m['id'] ?>">Abrir Pedido</a>
      </td>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php include __DIR__ . '/../../includes/footer.php'; ?>