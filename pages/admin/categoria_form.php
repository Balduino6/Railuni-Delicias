<?php
// pages/admin/categoria_form.php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/db.php';

$auth = new Auth();
$auth->authorize(['admin']);
$user = $auth->user();
$conn = (new Database())->connect();

// Inicializa variáveis
$id   = $_POST['id']   ?? ($_GET['id'] ?? '');
$nome = '';

// Se for GET com ID, carrega para edição
if ($id && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmt = $conn->prepare('SELECT * FROM categorias WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $cat = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($cat) {
        $nome = $cat['nome'];
    }
}

// Processa POST (inserir ou atualizar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    if ($id) {
        $stmt = $conn->prepare('UPDATE categorias SET nome = :nome WHERE id = :id');
        $stmt->execute([':nome' => $nome, ':id' => $id]);
    } else {
        $stmt = $conn->prepare('INSERT INTO categorias (nome) VALUES (:nome)');
        $stmt->execute([':nome' => $nome]);
    }
    header('Location: categorias.php');
    exit;
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

  <!-- Bootstrap 5 -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
  <!-- FontAwesome 6 (All Styles) -->
  <link 
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" 
    rel="stylesheet" 
    integrity="sha512-dOxR5pby7HpdH5T+Ue6g4oSry4uG+o0gHlHurr3ZdP6kQlY9T0QHVXGZq8Ty2NVzBjv7YvTjvYh3UfwwX7lB6A==" 
    crossorigin="anonymous" 
    referrerpolicy="no-referrer" 
  />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

<div class="d-flex">
  

  <main class="flex-grow-1 p-5">
    <div class="card shadow-sm rounded-4 mx-auto" style="max-width: 500px;">
      <div class="card-body">
        <h2 class="card-title mb-4"><?= $id ? 'Editar Categoria' : 'Nova Categoria' ?></h2>
        <form method="post" novalidate>
          <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

          <div class="mb-4 form-floating">
            <input
              type="text"
              class="form-control"
              id="nome"
              name="nome"
              placeholder="Nome da categoria"
              value="<?= htmlspecialchars($nome) ?>"
              required
            >
            <label for="nome">Nome da Categoria</label>
          </div>

          <div class="d-flex justify-content-end">
            <a href="categorias.php" class="btn btn-outline-secondary me-2">
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
