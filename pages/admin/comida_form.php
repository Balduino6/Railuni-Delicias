<?php
// pages/admin/comida_form.php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/db.php';

$auth = new Auth();
$auth->authorize(['admin']);
$conn = (new Database())->connect();

$id = $nome = $preco = $id_categoria = '';
$categorias = $conn->query("SELECT id, nome FROM categorias ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id           = $_POST['id'] ?? null;
    $nome         = $_POST['nome'];
    $preco        = $_POST['preco'];
    $id_categoria = $_POST['id_categoria'];

    if ($id) {
        $sql = 'UPDATE comidas SET nome=:nome, preco=:preco, id_categoria=:cat WHERE id=:id';
        $params = [':nome'=>$nome,':preco'=>$preco,':cat'=>$id_categoria,':id'=>$id];
    } else {
        $sql = 'INSERT INTO comidas (nome, preco, id_categoria) VALUES (:nome, :preco, :cat)';
        $params = [':nome'=>$nome,':preco'=>$preco,':cat'=>$id_categoria];
    }
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    header('Location: comidas.php');
    exit;
}

if (isset($_GET['id'])) {
    $stmt = $conn->prepare('SELECT * FROM comidas WHERE id=:id');
    $stmt->execute([':id'=>$_GET['id']]);
    $comida = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($comida) extract($comida);
}

include __DIR__ . '/../../includes/header.php';
?>

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="flex-grow-1 p-5">
      <div class="card shadow-lg rounded-4 mx-auto" style="max-width: 1000px;" >
        <!-- Cabeçalho claro -->
        <div class="card-header bg-light">
          <h4 class="text mb-0"><?= $id ? 'Editar Comida' : 'Nova Comida' ?></h4>
        </div>
        <div class="card-body">
          <form method="post" novalidate>
            <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

            <div class="mb-3 form-floating">
              <input 
                type="text" 
                class="form-control form-control-lg" 
                id="nome" 
                name="nome" 
                placeholder="Nome da Comida" 
                value="<?= htmlspecialchars($nome) ?>" 
                required
              >
              <label for="nome">Nome da Comida</label>
            </div>

            <div class="mb-3 form-floating">
              <input 
                type="number" 
                step="0.01" 
                class="form-control form-control-lg" 
                id="preco" 
                name="preco" 
                placeholder="Preço (Kz)" 
                value="<?= htmlspecialchars($preco) ?>" 
                required
              >
              <label for="preco">Preço (Kz)</label>
            </div>

            <div class="mb-3">
              <label for="categoria" class="form-label fw-semibold">Categoria</label>
              <select class="form-select me-2"  id="categoria" name="id_categoria" required>
                <option value="" disabled <?= !$id_categoria ? 'selected' : '' ?>>-- Selecione --</option>
                <?php foreach ($categorias as $cat): ?>
                  <option value="<?= $cat['id'] ?>" <?= $cat['id']==$id_categoria ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['nome']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="d-flex justify-content-between">
              <a href="comidas.php" class="btn btn-outline-secondary btn-lg">
                <i class="fa-solid fa-arrow-left me-1"></i>Cancelar
              </a>
              <button type="submit" class="btn btn-success btn-lg">
                <i class="fa-solid fa-check me-1"></i>Salvar
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
