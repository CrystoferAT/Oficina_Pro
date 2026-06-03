<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <h1 class="display-5 mb-4">
                <i class="bi bi-wrench-adjustable me-2 text-primary"></i>Cadastro de Serviços Base
            </h1>
            <p class="text-muted">Cadastre aqui a mão de obra padrão da oficina. Peças e valores variáveis serão adicionados diretamente na abertura do pedido/orçamento.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0 fw-bold text-uppercase small">
                        <i class="bi bi-plus-circle me-2"></i>Novo Serviço
                    </h5>
                </div>
                <div class="card-body">
                    <form action="controller/servicosController.php" method="POST">
                        <input type="hidden" name="pagina_origem" value="cadastro_servicos">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-secondary">Serviço Técnico / Descrição</label>
                            <input type="text" name="servico" class="form-control" placeholder="Ex: Troca de Óleo, Alinhamento" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-secondary">Tempo Estimado (min)</label>
                                <div class="input-group">
                                    <input type="number" name="tempo" class="form-control" placeholder="Ex: 40">
                                    <span class="input-group-text"><i class="bi bi-clock"></i></span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold small text-secondary">Preço da Mão de Obra</label>
                                <div class="input-group">
                                    <span class="input-group-text">R$</span>
                                    <input type="number" step="0.01" name="precoServico" class="form-control" placeholder="0.00" required>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" name="salvar" class="btn btn-success fw-bold py-2 shadow-sm">
                                <i class="bi bi-check-lg me-1"></i> Salvar Serviço Base
                            </button>
                            <a href="index.php?p=servicos" class="btn btn-outline-secondary btn-sm text-uppercase fw-bold mt-1">
                                Voltar para o Gerenciador
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-dark text-white py-3">
                    <h5 class="card-title mb-0 fw-bold text-uppercase small">
                        <i class="bi bi-list-check me-2 text-primary"></i>Catálogo Atual de Mão de Obra
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Serviço</th>
                                    <th>Tempo</th>
                                    <th class="text-end pe-3">Mão de Obra</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $servicos = listarServicos();
                                if(empty($servicos)): 
                                ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted small">
                                        Nenhum serviço base cadastrado ainda.
                                    </td>
                                </tr>
                                <?php else: foreach($servicos as $s): ?>
                                <tr>
                                    <td class="ps-3">
                                        <strong class="text-dark small"><?= htmlspecialchars($s['servico']) ?></strong>
                                        <div class="text-muted" style="font-size: 0.75rem;">Cód: #<?= str_pad($s['id'], 4, '0', STR_PAD_LEFT) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border fw-normal">
                                            <?= $s['tempo'] > 0 ? $s['tempo'] . " min" : "Não inf." ?>
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold text-success pe-3">
                                        <?= formatarMoeda($s['precoServico']) ?>
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