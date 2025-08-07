<?php
require_once __DIR__ . '/../config/auth.php';
$auth = new Auth();
$auth->authorize(['admin', 'atendente']);
$user = $auth->user();

$baseUrl = '/tascapandora';

$icons = [
    'Dashboard' => 'fas fa-home',
    'Produtos' => 'fas fa-boxes',
    'Categorias' => 'fas fa-tags',
    'Estoque' => 'fas fa-warehouse',
    'Vendas' => 'fas fa-shopping-cart',
    'Comidas' => 'fas fa-hamburger',
    'Usuários' => 'fas fa-users',
    'Relatórios' => 'fas fa-chart-line',
    'Fechamento' => 'fas fa-file-invoice-dollar',
    'Diário' => 'fas fa-calendar-day',
    'Semanal' => 'fas fa-calendar-week',
    'Mensal' => 'fas fa-calendar-alt',
    'Funcionários' => 'fas fa-user-tie',
    'Clientes' => 'fas fa-user-friends',
    'Fornecedores' => 'fas fa-truck',
    'Permissões' => 'fas fa-user-shield',
    'Pedidos' => 'fas fa-concierge-bell',
    'Mesas' => 'fas fa-chair',
];

$menus = [
    'admin' => [
        'Dashboard' => "$baseUrl/pages/admin/dashboard.php",
        'Produtos' => "$baseUrl/pages/admin/produtos.php",
        'Categorias' => "$baseUrl/pages/admin/categorias.php",
        'Estoque' => "$baseUrl/pages/admin/estoque.php",
        'Vendas' => "$baseUrl/pages/admin/vendas.php",
        'Comidas' => "$baseUrl/pages/admin/comidas.php",
        'Usuários' => "$baseUrl/pages/admin/usuarios.php",
        'Relatórios' => "$baseUrl/pages/admin/relatorios.php",
        'Fechamento' => [
            'Diário' => "$baseUrl/pages/admin/fechamento_diario.php",
            'Semanal' => "$baseUrl/pages/admin/fechamento_semanal.php",
            'Mensal' => "$baseUrl/pages/admin/fechamento_mensal.php",
        ],
        'Funcionários' => "$baseUrl/pages/admin/funcionarios.php",
        'Clientes' => "$baseUrl/pages/admin/clientes/listar.php",
        'Fornecedores' => "$baseUrl/pages/admin/fornecedores/listar.php",
        'Permissões' => "$baseUrl/pages/admin/permissoes.php",
    ],
    'atendente' => [
        'Pedidos' => "$baseUrl/pages/atendente/pedidos.php",
        'Mesas' => "$baseUrl/pages/atendente/mesas.php",
        'Estoque' => "$baseUrl/pages/atendente/estoque.php",
        'Vendas' => "$baseUrl/pages/atendente/vendas.php",
    ]
];

$perfil = $user['perfil'];
?>

<nav id="sidebarMenu" class="sidebar bg-white border-end shadow-sm vh-100">
  <div class="text-center py-4 border-bottom">
    <a href="<?= $baseUrl ?>/pages/<?= $perfil ?>/dashboard.php">
      <img src="<?= $baseUrl ?>/assets/img/railuni.jpg" alt="Logo" class="img-fluid" style="max-height: 60px;">
    </a>
  </div>

  <ul class="nav flex-column mt-3 px-3">
    <?php foreach ($menus[$perfil] as $label => $link): ?>
      <?php if (is_array($link)): ?>
        <?php $submenuId = 'submenu' . md5($label); ?>
        <?php
          $anyActive = false;
          foreach ($link as $sublink) {
            if (strpos($_SERVER['REQUEST_URI'], basename($sublink)) !== false) {
              $anyActive = true;
              break;
            }
          }
        ?>
        <li class="nav-item mb-2">
          <a class="nav-link d-flex justify-content-between align-items-center <?= $anyActive ? 'active bg-light text-primary border-start border-3 border-primary' : 'text-dark' ?> rounded"
             data-bs-toggle="collapse" href="#<?= $submenuId ?>" role="button"
             aria-expanded="<?= $anyActive ? 'true' : 'false' ?>" aria-controls="<?= $submenuId ?>">
            <span><i class="<?= $icons[$label] ?? 'fas fa-circle' ?> me-2"></i><?= htmlspecialchars($label) ?></span>
            <i class="fas fa-chevron-down small <?= $anyActive ? 'rotate-icon' : '' ?>"></i>
          </a>
          <div class="collapse <?= $anyActive ? 'show' : '' ?> ms-2" id="<?= $submenuId ?>">
            <ul class="nav flex-column">
              <?php foreach ($link as $sublabel => $sublink): ?>
                <?php $active = (strpos($_SERVER['REQUEST_URI'], basename($sublink)) !== false); ?>
                <li class="nav-item mb-1">
                  <a href="<?= $sublink ?>"
                     class="nav-link d-flex align-items-center <?= $active ? 'active bg-light text-primary border-start border-3 border-primary' : 'text-dark' ?> small rounded">
                    <i class="<?= $icons[$sublabel] ?? 'fas fa-circle' ?> me-2"></i>
                    <span><?= htmlspecialchars($sublabel) ?></span>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </li>
      <?php else: ?>
        <?php $isActive = (strpos($_SERVER['REQUEST_URI'], basename($link)) !== false); ?>
        <li class="nav-item mb-1">
          <a href="<?= $link ?>"
             class="nav-link d-flex align-items-center <?= $isActive ? 'active bg-light text-primary border-start border-3 border-primary' : 'text-dark' ?> rounded">
            <i class="<?= $icons[$label] ?? 'fas fa-dot-circle' ?> me-2"></i>
            <span><?= htmlspecialchars($label) ?></span>
          </a>
        </li>
      <?php endif; ?>
    <?php endforeach; ?>
  </ul>

  <div class="mt-auto text-center pb-3 border-top px-2">
    <small class="text-muted">Logado como</small><br>
    <strong><?= htmlspecialchars($user['nome']); ?></strong>
  </div>
</nav>

<!-- Inclua no <head> se ainda não estiver -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.sidebar .nav-link {
  padding: 0.75rem 1rem;
  transition: all 0.2s ease-in-out;
}
.sidebar .nav-link:hover {
  background-color: #f1f1f1;
  color: #0d6efd !important;
}
.rotate-icon {
  transform: rotate(180deg);
  transition: transform 0.3s ease;
}
.sidebar .nav-link i {
  min-width: 20px;
}
</style>
