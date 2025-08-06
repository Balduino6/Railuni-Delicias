<?php
// pages/admin/usuario_form.php
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/db.php';

$auth = new Auth();
$auth->authorize(['admin']);
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

include __DIR__ . '/../../includes/header.php';
?>

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-12 col-md-10 col-lg-8">
      <div class="card shadow rounded-4">
        <div class="card-header bg-primary text-white">
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
                <i class="bi bi-arrow-left-circle me-1"></i> Cancelar
              </a>
              <button type="submit" class="btn btn-success btn-lg">
                <i class="bi bi-check-circle me-1"></i> Salvar
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>
