<?php
// pages/admin/comida_form.php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/db.php';

$auth = new Auth();
$auth->authorize(['admin']);
$user = $auth->user();
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
    body {
      background-color: #f0f2f5;
    }
    #categorias {
      border-bottom: 1px solid #ddd;
    }

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
                <i class="fa-solid fa-save me-1"></i>Salvar
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
