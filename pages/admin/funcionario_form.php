<?php
// funcionario_form.php (Formulário completo)
require_once __DIR__ . '/../../config/auth.php';
require_once __DIR__ . '/../../config/db.php';

$auth = new Auth();
$auth->authorize(['admin']);
$conn = (new Database())->connect();

// ID para edição
$edit_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Busca usuários para dropdown (não vinculados ou vinculados ao editar)
$stmtUsers = $conn->prepare(
    "SELECT u.id, u.nome FROM usuarios u
     LEFT JOIN funcionarios f ON f.usuario_id = u.id
     WHERE f.usuario_id IS NULL OR f.usuario_id = :edit_id
     ORDER BY u.nome"
);
$stmtUsers->execute([':edit_id' => $edit_id]);
$usuarios = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

// Variáveis iniciais
$id = $nome_completo = $tipo_documento = $numero_documento = '';
$endereco = $nacionalidade = $telefone = $data_nascimento = $sexo = $data_admissao = '';
$usuario_id = '';

// Carrega dados existentes para edição
if ($edit_id && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    $stmt = $conn->prepare('SELECT * FROM funcionarios WHERE id = :id');
    $stmt->execute([':id' => $edit_id]);
    if ($func = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $func['id'];
        $nome_completo = $func['nome_completo'];
        $tipo_documento = $func['tipo_documento'];
        $numero_documento = $func['numero_documento'];
        $endereco = $func['endereco'];
        $nacionalidade = $func['nacionalidade'];
        $telefone = $func['telefone'];
        $data_nascimento = $func['data_nascimento'];
        $sexo = $func['sexo'];
        $usuario_id = $func['usuario_id'];
        $data_admissao = $func['data_admissao'];
    }
}

// Processa submissão do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recebe dados
    $id = (int)($_POST['id'] ?? 0);
    $nome_completo = trim($_POST['nome_completo']);
    $tipo_documento = $_POST['tipo_documento'];
    $numero_documento = trim($_POST['numero_documento']);
    $endereco = trim($_POST['endereco']);
    $nacionalidade = trim($_POST['nacionalidade']);
    $telefone = trim($_POST['telefone']);
    $data_nascimento = $_POST['data_nascimento'];
    $sexo = $_POST['sexo'];
    $usuario_id = (int)$_POST['usuario_id'];
    $data_admissao = $_POST['data_admissao'];

    // Valida documento único
    $sqlCheck = 'SELECT COUNT(*) FROM funcionarios WHERE numero_documento = :num_doc' .
                ($id ? ' AND id <> :id' : '');
    $stmtCheck = $conn->prepare($sqlCheck);
    $params = [':num_doc' => $numero_documento];
    if ($id) $params[':id'] = $id;
    $stmtCheck->execute($params);
    if ($stmtCheck->fetchColumn() > 0) {
        header('Location: funcionario_form.php?id=' . $id . '&msg=dup_doc');
        exit;
    }

    if ($id) {
        // Atualiza registro existente
        $stmt = $conn->prepare(
            'UPDATE funcionarios SET 
                nome_completo = :nome,
                tipo_documento = :tipo,
                numero_documento = :num,
                endereco = :endereco,
                nacionalidade = :nacionalidade,
                telefone = :telefone,
                data_nascimento = :nascimento,
                sexo = :sexo,
                usuario_id = :usuario,
                data_admissao = :admissao
             WHERE id = :id'
        );
        $params[':nome'] = $nome_completo;
        $params[':tipo'] = $tipo_documento;
        $params[':endereco'] = $endereco;
        $params[':nacionalidade'] = $nacionalidade;
        $params[':telefone'] = $telefone;
        $params[':nascimento'] = $data_nascimento;
        $params[':sexo'] = $sexo;
        $params[':usuario'] = $usuario_id;
        $params[':admissao'] = $data_admissao;
        $stmt->execute($params);
    } else {
        // Insere novo registro
        $stmt = $conn->prepare(
            'INSERT INTO funcionarios (
                nome_completo, tipo_documento, numero_documento,
                endereco, nacionalidade, telefone,
                data_nascimento, sexo, usuario_id, data_admissao
            ) VALUES (
                :nome, :tipo, :num,
                :endereco, :nacionalidade, :telefone,
                :nascimento, :sexo, :usuario, :admissao
            )'
        );
        $stmt->execute([
            ':nome' => $nome_completo,
            ':tipo' => $tipo_documento,
            ':num' => $numero_documento,
            ':endereco' => $endereco,
            ':nacionalidade' => $nacionalidade,
            ':telefone' => $telefone,
            ':nascimento' => $data_nascimento,
            ':sexo' => $sexo,
            ':usuario' => $usuario_id,
            ':admissao' => $data_admissao,
        ]);
    }

    // Redireciona para listagem com mensagem de sucesso
    header('Location: funcionarios.php?msg=success');
    exit;
}

include __DIR__ . '/../../includes/header.php';
?>
<main class="content">
  <div class="container my-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-10 col-lg-8">
        <div class="card shadow rounded-4">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><?= $id ? 'Editar Funcionário' : 'Novo Funcionário' ?></h4>
          </div>
          <div class="card-body p-4">
            <?php if (isset($_GET['msg']) && $_GET['msg'] === 'dup_doc'): ?>
              <div class="alert alert-danger">Número de documento já cadastrado.</div>
            <?php endif; ?>
            <form method="post" class="row g-3">
              <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

              <div class="col-12 form-floating">
                <input type="text" class="form-control form-control-lg" id="nome_completo" name="nome_completo"
                       placeholder="Nome completo" value="<?= htmlspecialchars($nome_completo) ?>" required>
                <label for="nome_completo">Nome completo</label>
              </div>

              <div class="col-6 form-floating">
                <select class="form-select form-select-lg" id="tipo_documento" name="tipo_documento" required>
                  <option value="bilhete" <?= $tipo_documento==='bilhete'?'selected':'' ?>>Bilhete de Identidade</option>
                  <option value="passaporte" <?= $tipo_documento==='passaporte'?'selected':'' ?>>Passaporte</option>
                </select>
                <label for="tipo_documento">Tipo de documento</label>
              </div>

              <div class="col-6 form-floating">
                <input type="text" class="form-control form-control-lg" id="numero_documento" name="numero_documento"
                       placeholder="Número do documento" value="<?= htmlspecialchars($numero_documento) ?>" required>
                <label for="numero_documento">Número do documento</label>
              </div>

              <div class="col-12 form-floating">
                <input type="text" class="form-control form-control-lg" id="endereco" name="endereco"
                       placeholder="Endereço" value="<?= htmlspecialchars($endereco) ?>">
                <label for="endereco">Endereço</label>
              </div>

              <div class="col-6 form-floating">
                <input type="text" class="form-control form-control-lg" id="nacionalidade" name="nacionalidade"
                       placeholder="Nacionalidade" value="<?= htmlspecialchars($nacionalidade) ?>">
                <label for="nacionalidade">Nacionalidade</label>
              </div>

              <div class="col-6 form-floating">
                <input type="text" class="form-control form-control-lg" id="telefone" name="telefone"
                       placeholder="Telefone" value="<?= htmlspecialchars($telefone) ?>">
                <label for="telefone">Telefone</label>
              </div>

              <div class="col-6 form-floating">
                <input type="date" class="form-control form-control-lg" id="data_nascimento" name="data_nascimento"
                       placeholder="Data de Nascimento" value="<?= htmlspecialchars($data_nascimento) ?>" required>
                <label for="data_nascimento">Data de Nascimento</label>
              </div>

              <div class="col-6 form-floating">
                <select class="form-select form-select-lg" id="sexo" name="sexo" required>
                  <option value="Masculino" <?= $sexo==='Masculino'?'selected':'' ?>>Masculino</option>
                  <option value="Feminino" <?= $sexo==='Feminino'?'selected':'' ?>>Feminino</option>
                  <option value="Outro" <?= $sexo==='Outro'?'selected':'' ?>>Outro</option>
                </select>
                <label for="sexo">Sexo</label>
              </div>

              <div class="col-12 form-floating">
                <select class="form-select form-select-lg" id="usuario_id" name="usuario_id" required>
                  <option value="" disabled <?= !$usuario_id?'selected':'' ?>>Selecione usuário</option>
                  <?php foreach ($usuarios as $u): ?>
                    <option value="<?= $u['id'] ?>" <?= $u['id']==$usuario_id?'selected':'' ?>>
                      <?= htmlspecialchars($u['nome']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <label for="usuario_id">Usuário do sistema</label>
              </div>

              <div class="col-6 form-floating">
                <input type="date" class="form-control form-control-lg" id="data_admissao" name="data_admissao"
                       placeholder="Data de Admissão" value="<?= htmlspecialchars($data_admissao) ?>" required>
                <label for="data_admissao">Data de Admissão</label>
              </div>

              <div class="col-12 d-flex justify-content-end mt-3">
                <a href="funcionarios.php" class="btn btn-outline-secondary btn-lg me-3">
                  <i class="bi bi-arrow-left-circle me-1"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-success btn-lg">
                  <i class="bi bi-check-circle me-1"></i>Salvar
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<?php include __DIR__ . '/../../includes/footer.php'; ?>
