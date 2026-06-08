<div class="container mt-4">

    <?php if (isset($_GET['sucesso'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php
                if ($_GET['sucesso'] === 'cadastro')  echo 'Funcionário cadastrado com sucesso!';
                if ($_GET['sucesso'] === 'edicao')    echo 'Funcionário atualizado com sucesso!';
                if ($_GET['sucesso'] === 'exclusao')  echo 'Funcionário excluído com sucesso!';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['erro'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php
                if ($_GET['erro'] === 'email_duplicado') echo 'Este e-mail já está cadastrado no sistema.';
                if ($_GET['erro'] === 'edicao')          echo 'Erro ao atualizar funcionário.';
                if ($_GET['erro'] === 'exclusao')        echo 'Erro ao excluir funcionário.';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white py-3">
                    <h5 class="card-title mb-0" id="form-titulo">
                        <i class="bi bi-person-plus-fill me-2"></i>Novo Usuário
                    </h5>
                </div>
                <div class="card-body">
                    <form action="controller/CadastroUsuarioController.php" method="post" id="form-usuario">
                        <input type="hidden" name="id" id="campo-id" value="">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nome Completo</label>
                            <input type="text" name="nome" id="campo-nome" class="form-control" placeholder="Ex: João Silva" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold d-block">Nível de Acesso</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="nivel" id="nivel-admin" value="admin" required>
                                <label class="btn btn-outline-danger" for="nivel-admin">Admin</label>

                                <input type="radio" class="btn-check" name="nivel" id="nivel-funcionario" value="funcionario">
                                <label class="btn btn-outline-warning" for="nivel-funcionario">Funcionário</label>

                                <input type="radio" class="btn-check" name="nivel" id="nivel-cliente" value="cliente">
                                <label class="btn btn-outline-primary" for="nivel-cliente">Cliente</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">E-mail</label>
                            <input type="email" name="email" id="campo-email" class="form-control" placeholder="email@oficina.com" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Senha <small id="senha-hint" class="text-muted fw-normal"></small></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-key"></i></span>
                                <input type="password" name="senha" id="campo-senha" class="form-control">
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" name="cadastrar" id="btn-cadastrar" class="btn btn-dark btn-lg">
                                Cadastrar Usuário
                            </button>
                            <button type="submit" name="editar" id="btn-editar" class="btn btn-warning btn-lg d-none">
                                Salvar Alterações
                            </button>
                            <button type="button" id="btn-cancelar" class="btn btn-outline-secondary d-none" onclick="cancelarEdicao()">
                                Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <?php $usuarios = listarUsuarios(); ?>
                <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-dark">Usuários Ativos</h5>
                    <span class="badge bg-dark"><?= count($usuarios) ?> usuários</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Usuário</th>
                                    <th>Nível</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($usuarios)): ?>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold"><?= $usuario['id'] . " - " . htmlspecialchars($usuario['nome']) ?></div>
                                                <small class="text-muted"><?= htmlspecialchars($usuario['email']) ?></small>
                                            </td>
                                            <td>
                                                <?php
                                                    $cor = 'bg-secondary';
                                                    if ($usuario['nivel'] === 'admin')       $cor = 'bg-dark';
                                                    if ($usuario['nivel'] === 'funcionario') $cor = 'bg-warning text-dark';
                                                    if ($usuario['nivel'] === 'cliente')     $cor = 'bg-primary';
                                                ?>
                                                <span class="badge rounded-pill <?= $cor ?>"><?= $usuario['nivel'] ?></span>
                                            </td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-outline-secondary border-0"
                                                    onclick="preencherEdicao(
                                                        '<?= $usuario['id'] ?>',
                                                        '<?= htmlspecialchars($usuario['nome'], ENT_QUOTES) ?>',
                                                        '<?= $usuario['nivel'] ?>',
                                                        '<?= htmlspecialchars($usuario['email'], ENT_QUOTES) ?>'
                                                    )">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>

                                                <form action="controller/CadastroUsuarioController.php" method="post" class="d-inline"
                                                    onsubmit="return confirm('Tem certeza que deseja excluir <?= htmlspecialchars($usuario['nome'], ENT_QUOTES) ?>?')">
                                                    <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
                                                    <button type="submit" name="excluir" class="btn btn-sm btn-outline-danger border-0">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            Nenhum Usuário cadastrado no sistema.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function preencherEdicao(id, nome, nivel, email) {
    document.getElementById('campo-id').value    = id;
    document.getElementById('campo-nome').value  = nome;
    document.getElementById('campo-email').value = email;
    document.getElementById('campo-senha').value = '';

    const radio = document.getElementById('nivel-' + nivel);
    if (radio) radio.checked = true;

    document.getElementById('form-titulo').innerHTML = '<i class="bi bi-pencil-square me-2"></i>Editar Funcionário';
    document.getElementById('btn-cadastrar').classList.add('d-none');
    document.getElementById('btn-editar').classList.remove('d-none');
    document.getElementById('btn-cancelar').classList.remove('d-none');
    document.getElementById('senha-hint').textContent = '(deixe em branco para manter a atual)';

    document.getElementById('form-usuario').scrollIntoView({ behavior: 'smooth' });
}

function cancelarEdicao() {
    document.getElementById('form-usuario').reset();
    document.getElementById('campo-id').value = '';
    document.getElementById('form-titulo').innerHTML = '<i class="bi bi-person-plus-fill me-2"></i>Novo Funcionário';
    document.getElementById('btn-cadastrar').classList.remove('d-none');
    document.getElementById('btn-editar').classList.add('d-none');
    document.getElementById('btn-cancelar').classList.add('d-none');
    document.getElementById('senha-hint').textContent = '';
}
</script>
