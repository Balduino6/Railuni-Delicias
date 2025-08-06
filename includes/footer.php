<?php
require_once __DIR__ . '/../config/auth.php';
$auth = new Auth();
$auth->authorize(['admin','atendente']);
$user = $auth->user();

// Definição de menus conforme perfil
$baseUrl = '/tascapandora';

?> 

<style>
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
   
   </main>
  </div>

  <footer class="footer bg-dark text-light py-4 mt-auto">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">
      <div class="footer-brand d-flex align-items-center">
        <img src="<?php echo $baseUrl ?>/assets/img/railuni.jpg" alt="Logo Footer" height="32" class="me-2"/>
        <span>© <?= date('Y'); ?> Railuni Delicías</span>
      </div>
      <nav class="footer-nav d-flex gap-3">
        <a href="/politica_privacidade.php" class="text-light text-decoration-none">
          Política de Privacidade
        </a>
        <a href="/termos_uso.php" class="text-light text-decoration-none">
          Termos de Uso
        </a>
        <a href="/contato.php" class="text-light text-decoration-none">
          Contato
        </a>
      </nav>
      <div class="footer-social d-flex gap-2">
        <a href="#"><i class="fab fa-facebook-f"></i></a>
        <a href="#"><i class="fab fa-instagram"></i></a>
        <a href="#"><i class="fab fa-whatsapp"></i></a>
      </div>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
  <!-- Darkmode Script -->
  <script src="/assets/js/darkmode.js"></script>
</body>
</html>
