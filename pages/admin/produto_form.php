<?php
// pages/admin/produto_form.php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/db.php';

$auth = new Auth();
$auth->authorize(['admin']);
$user = $auth->user();
$conn = (new Database())->connect();

// Inicializa variáveis
$id           = $_POST['id']           ?? ($_GET['id']         ?? '');
$nome         = '';
$descricao    = '';
$categoria    = '';
$preco_custo  = '';
$preco_venda  = '';

if ($id && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Carrega para edição
    $stmt = $conn->prepare('SELECT * FROM Produtos WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $produto = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($produto) {
        $nome        = $produto['nome'];
        $descricao   = $produto['descricao'];
        $categoria   = $produto['categoria'];
        $preco_custo = $produto['preco_custo'];
        $preco_venda = $produto['preco_venda'];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Captura valores do POST
    $nome         = $_POST['nome'];
    $descricao    = $_POST['descricao'];
    $categoria    = $_POST['categoria'];
    $preco_custo  = floatval($_POST['preco_custo']);
    $preco_venda  = floatval($_POST['preco_venda']);

    if ($id) {
        $sql = "UPDATE Produtos SET nome=:nome, descricao=:descricao, categoria=:categoria, preco_custo=:preco_custo, preco_venda=:preco_venda WHERE id=:id";
        $params = [':nome'=>$nome,':descricao'=>$descricao,':categoria'=>$categoria,':preco_custo'=>$preco_custo,':preco_venda'=>$preco_venda,':id'=>$id];
    } else {
        $sql = "INSERT INTO Produtos (nome,descricao,categoria,preco_custo,preco_venda) VALUES (:nome,:descricao,:categoria,:preco_custo,:preco_venda)";
        $params = [':nome'=>$nome,':descricao'=>$descricao,':categoria'=>$categoria,':preco_custo'=>$preco_custo,':preco_venda'=>$preco_venda];
    }
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    header('Location: produtos.php');
    exit;
} 

$cats = $conn->query("SELECT id,nome FROM categorias ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

?>

<!doctype html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Snackbar Taska da Pandora</title>
  <!-- Google Font Righteous -->
  <link href="https://fonts.googleapis.com/css2?family=Righteous&display=swap" rel="stylesheet">
  <!-- Bootstrap 5 -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <!-- FontAwesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <!-- Custom Styles -->
  <link rel="stylesheet" href="/assets/css/style.css">
  
  <style>
    /* Ajusta largura do menu offcanvas no mobile */
    .offcanvas {
      --bs-offcanvas-width: 250px;
    }
    .brand-title { font-family: 'Righteous', cursive; }

    /* Dark mode ajusta degradê */
    body.dark-mode .topbar {
      background: linear-gradient(135deg, #C75C8C, #D7C0AC) !important;
    }
    @media (max-width: 576px) {
    .offcanvas { --bs-offcanvas-width: 200px; }
    }
    @media (min-width: 577px) and (max-width: 768px) {
      .offcanvas { --bs-offcanvas-width: 220px; }
    }

    footer{
      background: linear-gradient(135deg, #E56EA1, #EFE1D6);
      color: #6B4226;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .footer-brand img {
      max-height: 32px;
    }

    .footer-brand span {
      font-family: 'Righteous', cursive;
      font-size: 1.2rem;
    }
    .footer-nav a {
      color: #6B4226;
      text-decoration: none;
    }
    .footer-nav a:hover {
      text-decoration: underline;
    }
    .footer {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      margin-top: 20px;
      padding: 20px;
      background-color: #f8f9fa;
      box-shadow: 0 -2px 4px rgba(0,0,0,0.1);
    }
    .footer-social a {
      color: #6B4226;
      text-decoration: none;
    }
  </style>
</head>
<body>
  <!-- NAVBAR / HEADER -->
  <header class="topbar d-flex justify-content-between align-items-center px-4 py-2"
          style="background: linear-gradient(135deg, #E56EA1, #EFE1D6); color: #6B4226; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
    <div class="d-flex align-items-center">
      <!-- botão mobile: abre offcanvas -->
      <button class="btn btn-outline-light d-lg-none me-3" type="button"
              data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu">
        <i class="fas fa-bars"></i>
      </button>
      <h1 class="h5 m-0 brand-title">Railuni Delicías</h1>
    </div>
    <div class="d-flex align-items-center">
      <span class="me-3">Olá, <?= htmlspecialchars($user['nome']); ?> (<?= $user['perfil']; ?>)</span>
      <button id="toggleDark" class="btn btn-outline-secondary btn-sm me-2">🌓</button>
      <a href="../../logout.php" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-sign-out-alt"></i> Sair
      </a>
    </div>
  </header>


<div class="d-flex">

  <main class="flex-grow-1 p-5">
    <div class="card shadow-sm rounded-4 mx-auto" style="max-width: 700px;">
      <div class="card-body">
        <h2 class="card-title mb-4"><?= $id ? 'Editar Produto' : 'Novo Produto' ?></h2>
        <form method="post" novalidate>
          <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

          <div class="mb-3 form-floating">
            <input 
              type="text" 
              class="form-control" 
              id="nome" 
              name="nome" 
              placeholder="Nome do produto" 
              value="<?= htmlspecialchars($nome) ?>" 
              required
            >
            <label for="nome">Nome do Produto</label>
          </div>

          <div class="mb-3 form-floating">
            <textarea 
              class="form-control" 
              placeholder="Descrição" 
              id="descricao" 
              name="descricao" 
              style="height: 100px;"
            ><?= htmlspecialchars($descricao) ?></textarea>
            <label for="descricao">Descrição</label>
          </div>

          <div class="mb-3">
            <label for="categoria" class="form-label">Categoria</label>
            <div class="d-flex">
              <select 
                class="form-select me-2" 
                id="categoria" 
                name="categoria" 
                required
              >
                <option value="" disabled <?= $categoria==''?'selected':'' ?>>Selecione...</option>
                <?php foreach ($cats as $c): ?>
                  <option value="<?= $c['id'] ?>" <?= $c['id']==$categoria?'selected':'' ?>>
                    <?= htmlspecialchars($c['nome']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <a 
                href="../admin/categoria_form.php" 
                target="_blank" 
                class="btn btn-outline-primary"
                title="Adicionar nova categoria"
              >
                <i class="fa-solid fa-plus"></i>
              </a>
            </div>
          </div>

          <div class="row g-3 mb-4">
            <div class="col-md-6 form-floating">
              <input 
                type="number" 
                step="0.01" 
                class="form-control" 
                id="preco_custo" 
                name="preco_custo" 
                placeholder="Preço de Custo" 
                value="<?= htmlspecialchars($preco_custo) ?>" 
                required
              >
              <label for="preco_custo">Preço de Custo (Kz)</label>
            </div>
            <div class="col-md-6 form-floating">
              <input 
                type="number" 
                step="0.01" 
                class="form-control" 
                id="preco_venda" 
                name="preco_venda" 
                placeholder="Preço de Venda" 
                value="<?= htmlspecialchars($preco_venda) ?>" 
                required
              >
              <label for="preco_venda">Preço de Venda (Kz)</label>
            </div>
          </div>

          <div class="d-flex justify-content-end">
            <a href="produtos.php" class="btn btn-outline-secondary me-2">
              <i class="fa-solid fa-arrow-left me-1"></i> Voltar
            </a>
            <button type="submit" class="btn btn-success">
              <i class="fa-solid fa-save me-1"></i> Salvar
            </button>
          </div>
        </form>
      </div>
    </div>
  </main>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
