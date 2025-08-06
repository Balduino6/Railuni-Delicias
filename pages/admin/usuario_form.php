<?php
// pages/admin/usuario_form.php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/db.php';

$auth = new Auth();
$auth->authorize(['admin']);
$user = $auth->user();
$conn = (new Database())->connect();

$id = $nome = $email = $perfil = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = $_POST['id'] ?? null;
    $nome   = $_POST['nome'];
    $email  = $_POST['email'];
    $senha  = $_POST['senha_hash'] ?? null;
    $perfil = $_POST['perfil'];

    if ($id) {
        $sql = 'UPDATE usuarios SET nome=:nome, email=:email, perfil=:perfil'
             . ($senha ? ', senha_hash=:senha' : '')
             . ' WHERE id=:id';
        $params = [':nome'=>$nome,':email'=>$email,':perfil'=>$perfil,':id'=>$id];
        if ($senha) $params[':senha'] = password_hash($senha, PASSWORD_DEFAULT);
    } else {
        $sql = 'INSERT INTO usuarios (nome,email,senha_hash,perfil) VALUES (:nome,:email,:senha,:perfil)';
        $params = [':nome'=>$nome,':email'=>$email,':senha'=>password_hash($senha, PASSWORD_DEFAULT),':perfil'=>$perfil];
    }
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    header('Location: usuarios.php');
    exit;
}

if (isset($_GET['id'])) {
    $stmt = $conn->prepare('SELECT * FROM usuarios WHERE id=:id');
    $stmt->execute([':id'=>$_GET['id']]);
    if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
        extract($user);
    }
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
    <div class="col-12 col-md-10 col-lg-8">
      <div class="card shadow rounded-4">
        <div class="card-header bg-light text-dark">
          <h4 class="mb-0"><?= $id ? 'Editar Usuário' : 'Novo Usuário' ?></h4>
        </div>
        <div class="card-body p-4">
          <form method="post" class="row g-3">
            <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

            <div class="col-12 form-floating">
              <input
                type="text"
                class="form-control form-control-lg"
                id="nome"
                name="nome"
                placeholder="Nome completo"
                value="<?= htmlspecialchars($nome) ?>"
                required
              >
              <label for="nome">Nome completo</label>
            </div>

            <div class="col-12 form-floating">
              <input
                type="email"
                class="form-control form-control-lg"
                id="email"
                name="email"
                placeholder="email@exemplo.com"
                value="<?= htmlspecialchars($email) ?>"
                required
              >
              <label for="email">Email</label>
            </div>

            <div class="col-12 col-md-6 form-floating">
              <input
                type="password"
                class="form-control form-control-lg"
                id="senha"
                name="senha_hash"
                placeholder="<?= $id ? 'Deixe em branco para manter' : 'Senha' ?>"
                <?= $id ? '' : 'required' ?>
              >
              <label for="senha"><?= $id ? 'Nova senha (opcional)' : 'Senha' ?></label>
            </div>

            <div class="col-12 col-md-6 form-floating">
              <select
                class="form-select form-select-lg"
                id="perfil"
                name="perfil"
                required
              >
                <option value="admin" <?= $perfil==='admin'?'selected':'' ?>>Administrador</option>
                <option value="atendente" <?= $perfil==='atendente'?'selected':'' ?>>Atendente</option>
              </select>
              <label for="perfil">Perfil</label>
            </div>

            <div class="col-12 d-flex justify-content-end">
              <a href="usuarios.php" class="btn btn-outline-secondary btn-lg me-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Cancelar
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
