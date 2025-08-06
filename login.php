<?php
// login.php
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
            : 'pages/funcionario/vendas.php'
        ));
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Entrar - Taska da Pandora</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Poppins', sans-serif;
      background: url('assets/img/snackbar-bg-blur.jpg') center/cover no-repeat;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-wrapper {
      background: rgba(34, 34, 34, 0.6);
      backdrop-filter: blur(12px);
      border-radius: 16px;
      padding: 2rem;
      width: 360px;
      box-shadow: 0 12px 32px rgba(0,0,0,0.4);
      position: relative;
    }
    .logo {
      display: block;
      margin: 0 auto 1.5rem;
      width: 80px;
      height: 80px;
      background: url('assets/img/logo.png') center/contain no-repeat;
    }
    h2 {
      text-align: center;
      color: #ffd700;
      margin-bottom: 1rem;
      font-weight: 500;
    }
    .input-group {
      position: relative;
      margin-bottom: 1.5rem;
    }
    .input-group input {
      width: 100%;
      padding: 1rem 1rem 0.5rem;
      background: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.3);
      border-radius: 8px;
      color: #ffffff;
      font-size: 1rem;
      caret-color: #ffd700;
    }
    .input-group label {
      position: absolute;
      top: 1rem;
      left: 1rem;
      color: rgba(255,215,0,0.7);
      transition: 0.2s;
      pointer-events: none;
    }
    .input-group input:focus + label,
    .input-group input:not(:placeholder-shown) + label {
      top: 0.3rem;
      font-size: 0.75rem;
      color: #ffd700;
    }
    .input-group input:focus {
      outline: none;
      background: rgba(255,255,255,0.2);
      border-color: #ffd700;
    }
    .btn-login {
      width: 100%;
      padding: 0.75rem;
      background: #ffd700;
      border: none;
      border-radius: 8px;
      color: #222222;
      font-size: 1rem;
      font-weight: 500;
      cursor: pointer;
      transition: background 0.3s, transform 0.2s;
    }
    .btn-login:hover {
      background: #e6c200;
      transform: translateY(-2px);
    }
    .error-box {
      background: rgba(255,0,0,0.2);
      color: #ffdddd;
      padding: 0.75rem;
      border-radius: 6px;
      margin-bottom: 1rem;
      font-size: 0.875rem;
      text-align: center;
    }
    .extras {
      text-align: center;
      margin-top: 1rem;
    }
    .extras a {
      color: #ffd700;
      text-decoration: none;
      font-size: 0.875rem;
      transition: color 0.2s;
    }
    .extras a:hover {
      color: #ffffff;
    }
  </style>
</head>
<body>
  <div class="login-wrapper">
    <div class="logo"></div>
    <h2>Entrar na Taska</h2>
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
      <a href="recuperar_senha.php">Esqueci minha senha</a>
    </div>
  </div>
</body>
</html>
