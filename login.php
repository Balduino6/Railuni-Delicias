<?php
require_once __DIR__ . '/config/auth.php';
$auth = new Auth();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha_hash'] ?? '';

    if (!$email) {
        $error = 'E-mail inválido.';
    } elseif (!$auth->login($email, $senha)) {
        $error = 'E-mail ou senha incorretos.';
    } else {
        $perfil = $_SESSION['user_profile'];
        header('Location: ' . ($perfil === 'admin'
            ? 'pages/admin/dashboard.php'
            : 'pages/atendente/vendas.php'
        ));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - Railuni Delícias</title>
  <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    * {
      margin: 0; padding: 0; box-sizing: border-box;
    }
    body {
      font-family: 'Quicksand', sans-serif;
      background: linear-gradient(135deg, #E56EA1, #EFE1D6);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .login-container {
      background: rgba(255,255,255,0.85);
      padding: 2rem;
      border-radius: 16px;
      box-shadow: 0 12px 32px rgba(0,0,0,0.15);
      width: 360px;
      position: relative;
      text-align: center;
    }
    .logo {
      background: url('assets/img/railuni.jpg') center/contain no-repeat;
      width: 80px;
      height: 80px;
      margin: 0 auto 1rem;
    }
    h2 {
      color: #6B4226;
      font-weight: 600;
      margin-bottom: 1rem;
    }
    .input-group {
      position: relative;
      margin-bottom: 1.5rem;
    }
    .input-group input {
      width: 100%;
      padding: 1rem 1rem 0.5rem;
      border: 1px solid rgba(107,66,38,0.2);
      border-radius: 8px;
      background: #fff;
      color: #6B4226;
      font-size: 1rem;
    }
    .input-group label {
      position: absolute;
      top: 1rem;
      left: 1rem;
      color: #6B4226a1;
      pointer-events: none;
      transition: 0.2s ease;
    }
    .input-group input:focus + label,
    .input-group input:not(:placeholder-shown) + label {
      top: 0.4rem;
      font-size: 0.75rem;
      color: #6B4226;
    }
    .input-group input:focus {
      outline: none;
      border-color: #E56EA1;
      background-color: #fff8f9;
    }
    .btn-login {
      background: #E56EA1;
      color: white;
      border: none;
      padding: 0.75rem;
      width: 100%;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    .btn-login:hover {
      background: #cc5a90;
      transform: translateY(-2px);
    }
    .error-box {
      background: rgba(255, 0, 0, 0.1);
      color: #b30000;
      padding: 0.75rem;
      border-radius: 6px;
      margin-bottom: 1rem;
      font-size: 0.9rem;
    }
    .extras {
      margin-top: 1rem;
    }
    .extras a {
      text-decoration: none;
      font-size: 0.85rem;
      color: #6B4226;
      transition: color 0.2s;
    }
    .extras a:hover {
      color: #E56EA1;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="logo"></div>
    <h2>Railuni Delícias</h2>
    <?php if ($error): ?>
      <div class="error-box"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" novalidate>
      <div class="input-group">
        <input type="email" name="email" id="email" placeholder=" " required value="<?= isset($email)?htmlspecialchars($email):'' ?>">
        <label for="email">E-mail</label>
      </div>
      <div class="input-group">
        <input type="password" name="senha_hash" id="senha" placeholder=" " required>
        <label for="senha">Senha</label>
      </div>
      <button type="submit" class="btn-login">Entrar</button>
    </form>
    <div class="extras">
      <a href="recuperar_senha.php">Esqueceu a senha?</a>
    </div>
  </div>
</body>
</html>
