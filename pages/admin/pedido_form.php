<?php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/db.php';
$auth = new Auth();
$auth->authorize(['admin','atendente']);
$conn = (new Database())->connect();

$idMesa = $_GET['mesa'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Inserir pedido
    $conn->beginTransaction();
    $stmt = $conn->prepare(
        'INSERT INTO Pedidos (id_mesa, id_usuario, data_hora, status) VALUES (:mesa, :user, NOW(), "aberto")'
    );
    $stmt->execute([':mesa'=>$idMesa, ':user'=>$auth->user()['id']]);
    $idPedido = $conn->lastInsertId();

    // Itens do pedido
    $stmtItem = $conn->prepare(
        'INSERT INTO Itens_Pedido (id_pedido, id_produto, quantidade, preco_unitario)
         VALUES (:pedido, :prod, :qt, :preco)'
    );
    foreach ($_POST['itens'] as $item) {
        $stmtItem->execute([
            ':pedido'=>$idPedido,
            ':prod'=>$item['id'],
            ':qt'=>$item['qt'],
            ':preco'=>$item['preco']
        ]);
        // atualiza estoque
        $stmtEst = $conn->prepare(
            'INSERT INTO Estoque (id_produto, tipo_movimento, quantidade, id_usuario)
             VALUES (:prod, "saida", :qt, :user)'
        );
        $stmtEst->execute([':prod'=>$item['id'], ':qt'=>$item['qt'], ':user'=>$auth->user()['id']]);
    }
    $conn->commit();
    header('Location: mesas.php');
    exit;
}

// Carregar produtos para seleção
$produtos = $conn->query('SELECT id, nome, preco_venda FROM Produtos ORDER BY nome')->fetchAll(PDO::FETCH_ASSOC);
?>
<?php include __DIR__ . '/../../includes/header.php'; ?>
<h1>Abrir Pedido - Mesa <?= htmlspecialchars($idMesa) ?></h1>
<form method="post" id="formPedido">
  <input type="hidden" name="mesa" value="<?= $idMesa ?>">
  <div id="itensPedido"></div>
  <button type="button" id="addItemPedido">+ Adicionar Item</button>
  <button type="submit">Salvar Pedido</button>
</form>
<script>
const produtosPedido = <?= json_encode($produtos) ?>;
const itensPedido = document.getElementById('itensPedido');
const addItemPed = document.getElementById('addItemPedido');
let idxPed = 0;
addItemPed.onclick = () => {
  const i = idxPed++;
  const div = document.createElement('div');
  div.innerHTML = `
    <select name="itens[${i}][id]">
      ${produtosPedido.map(p => `<option value="${p.id}" data-preco="${p.preco_venda}">${p.nome}</option>`).join('')}
    </select>
    <input type="number" name="itens[${i}][qt]" value="1" min="1">
    <input type="hidden" name="itens[${i}][preco]" value="${produtosPedido[0].preco_venda}">
    <button type="button" onclick="this.parentNode.remove()">×</button>
  `;
  itensPedido.appendChild(div);
};
</script>
<?php include __DIR__ . '/../../includes/footer.php'; ?>