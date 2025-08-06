<?php
require_once __DIR__ . '/../config/auth.php';
$auth = new Auth();
$auth->authorize(['admin','atendente']);
$user = $auth->user();
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

  <!-- OFFCANVAS SIDEBAR PARA MOBILE -->
  <div class="offcanvas offcanvas-start" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="sidebarLabel">Menu</h5>
      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
    </div>
    <div class="offcanvas-body p-0">
      <?php include __DIR__ . '/sidebar.php'; ?>
    </div>
  </div>

  <div class="d-flex flex-grow-1">
    <!-- SIDEBAR PARA DESKTOP -->
    <aside class="d-none d-lg-block col-lg-2 pe-0">
      <?php include __DIR__ . '/sidebar.php'; ?>
    </aside>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="flex-fill p-4">
