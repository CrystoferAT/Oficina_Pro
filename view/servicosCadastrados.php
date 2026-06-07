<?php
    $pagina = isset($_GET['p']) ? $_GET['p'] : 'servicos';
?>

<div class="card shadow-sm border-0 mt-3">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 text-uppercase fw-bold">
            <i class="bi bi-list-check me-2 text-primary"></i>Gerenciamento de Serviços
        </h5>
        <a href="index.php?p=cadastro_servicos" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Novo Serviço
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Serviço / Detalhes</th>
                        <th>Tempo Estimado</th>
                        <th class="text-end text-primary ps-5">Mão de Obra (Valor Base)</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $servicos = listarServicos();
                    if(empty($servicos)): 
                    ?>
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Nenhum serviço cadastrado no sistema.
                        </td>
                    </tr>
                    <?php else: foreach($servicos as $s): ?>
                    <tr>
                        <td class="ps-4">
                            <span class="fw-bold d-block text-dark"><?= htmlspecialchars($s['nome']) ?></span>
                            <small class="text-muted">Cód: #<?= str_pad($s['id'] ?? 0, 4, '0', STR_PAD_LEFT) ?></small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border fw-normal">
                                <i class="bi bi-clock me-1 text-secondary"></i><?= (isset($s['tempo_estimado_minutos']) && $s['tempo_estimado_minutos'] > 0) ? $s['tempo_estimado_minutos'] . ' min' : 'Não informado' ?>
                            </span>
                        </td>
                        <td class="text-end fw-bold text-success"><?= formatarMoeda($s['mao_de_obra'] ?? 0) ?></td>
                        <td class="text-center">
                            <div class="btn-group shadow-sm">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $s['id'] ?>">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="controller/servicosController.php?excluir=<?= $s['id'] ?>" 
                                   class="btn btn-sm btn-outline-danger" 
                                   onclick="return confirm('Deseja realmente excluir este serviço?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>

                            <div class="modal fade text-start" id="editModal<?= $s['id'] ?>" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow">
                                        <div class="modal-header bg-primary text-white">
                                            <h5 class="modal-title">Editar Serviço #<?= $s['id'] ?></h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="controller/servicosController.php" method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="id_edicao" value="<?= $s['id'] ?>">
                                                
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small">Nome do Serviço:</label>
                                                    <input type="text" name="servico" class="form-control" value="<?= htmlspecialchars($s['nome']) ?>" required>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold small">Tempo Estimado (min):</label>
                                                        <input type="number" name="tempo" class="form-control" value="<?= $s['tempo_estimado_minutos'] ?>">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold small">Mão de Obra (R$):</label>
                                                        <input type="number" step="0.01" name="precoServico" class="form-control" value="<?= $s['mao_de_obra'] ?>" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer bg-light">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                                                <button type="submit" name="salvar" class="btn btn-primary btn-sm px-4">Salvar Alterações</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="container mt-4 px-0">
    <div class="row">
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0 fw-bold text-uppercase small">
                        <i class="bi bi-plus-circle me-2"></i>Novo Serviço Base
                    </h5>
                </div>
                <div class="card-body">
                    <form action="controller/servicosController.php" method="POST">
                        <input type="hidden" name="pagina_origem" value="<?= htmlspecialchars($pagina) ?>">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Serviço Técnico</label>
                            <input type="text" name="servico" class="form-control" placeholder="Ex: Troca de Óleo" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Tempo (min)</label>
                                <input type="number" name="tempo" class="form-control" placeholder="Ex: 40">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small">Mão de Obra (R$)</label>
                                <input type="number" step="0.01" name="precoServico" class="form-control" placeholder="0.00" required>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-2">
                            <button type="submit" name="salvar" class="btn btn-success">
                                <i class="bi bi-check-lg"></i> Salvar Serviço
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white py-3">
                    <h5 class="card-title mb-0 fw-bold text-uppercase small">
                        <i class="bi bi-clock-history me-2"></i>Cadastrados Recentemente
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Serviço</th>
                                    <th class="text-end">Mão de Obra</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($servicos)): ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted small">Nenhum serviço.</td>
                                </tr>
                                <?php else: foreach(array_slice($servicos, 0, 5) as $s): ?>
                                <tr>
                                    <td class="ps-3">
                                        <strong class="text-dark small d-block"><?= htmlspecialchars($s['nome']) ?></strong>
                                        <small class="text-muted text-xs">Tempo: <?= $s['tempo_estimado_minutos'] ?> min</small>
                                    </td>
                                    <td class="text-end fw-bold text-dark small"><?= formatarMoeda($s['mao_de_obra'] ?? 0) ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary py-0 px-2" data-bs-toggle="modal" data-bs-target="#editModal<?= $s['id'] ?>">
                                            <i class="bi bi-pencil small"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>