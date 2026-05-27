<div class="container mt-4">
    <div class="row">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white py-3">
                    <h5 class="card-title mb-0"><i class="bi bi-person-plus-fill me-2"></i>Novo Funcionário</h5>
                </div>
                <div class="card-body">
                    <form action="controller/CadastroUsuarioController.php" method="post">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nome Completo</label>
                            <input type="text" name="nome" class="form-control" placeholder="Ex: João Silva" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold d-block">Nível de Acesso</label>
                            <div class="btn-group w-100" role="group" aria-label="Níveis de acesso">

                                <input type="radio" class="btn-check" name="nivel" id="admin" value="admin" required>
                                <label class="btn btn-outline-danger" for="admin">Admin</label>

                                <input type="radio" class="btn-check" name="nivel" id="pro" value="pro" required>
                                <label class="btn btn-outline-danger" for="pro">Pro</label>

                                <input type="radio" class="btn-check" name="nivel" id="master" value="Master">
                                <label class="btn btn-outline-warning" for="master">Master</label>

                                <input type="radio" class="btn-check" name="nivel" id="user" value="Usuario">
                                <label class="btn btn-outline-primary" for="user">Usuário</label>
                                
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">E-mail Corporativo</label>
                            <input type="email" name="email" class="form-control" placeholder="email@oficina.com" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Chave de Acesso (Senha)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-key"></i></span>
                                <input type="password" name="senha" class="form-control" required>
                            </div>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" name="cadastrar" class="btn btn-dark btn-lg">
                                Cadastrar Funcionário
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-dark">Funcionários Ativos</h5>
                    <span class="badge bg-dark"><?= count($_SESSION['usuarios'] ?? []) ?> usuários</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Funcionário</th>
                                    <th>Nível</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($_SESSION['usuarios'])): ?>
                                    <?php foreach ($_SESSION['usuarios'] as $usuario): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-bold"><?= $usuario['id'] ,"-", $usuario['nome']  ?></div>
                                                <small class="text-muted"><?= $usuario['email'] ?? 'Sem e-mail' ?></small>
                                            </td>
                                            <td>
                                                <?php 
                                                    $cor = 'bg-secondary';
                                                    if($usuario['nivel'] == 'Admin') $cor = 'bg-dark';
                                                    if($usuario['nivel'] == 'Pro Master') $cor = 'bg-danger';
                                                    if($usuario['nivel'] == 'Master') $cor = 'bg-warning text-dark';
                                                    if($usuario['nivel'] == 'Usuario') $cor = 'bg-primary';
                                                ?>
                                                <span class="badge rounded-pill <?= $cor ?>"><?= $usuario['nivel'] ?></span>
                                            </td>
                                            <td class="text-end">
                                                <button class="btn btn-sm btn-outline-secondary border-0"><i class="bi bi-pencil-square"></i></button>
                                                <button class="btn btn-sm btn-outline-danger border-0"><i class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-muted">
                                            Nenhum funcionário cadastrado no sistema.
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