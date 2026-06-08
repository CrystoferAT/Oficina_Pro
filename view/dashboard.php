<?php
$listaServicos = listarServicos();
$listaUsuarios = listarUsuarios(); 
$listaClientes = listarClientes(); 

$metricas = obterMetricasDashboard();
$ultimosPedidos = listarUltimosPedidos(5);
$pagina = isset($_GET['p']) ? $_GET['p'] : 'dash';
?>

<div class="row mb-4">
    <div class="col-12 text-center text-lg-start d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1"><i class="bi bi-speedometer2 me-2 text-primary"></i>Painel de Controle</h2>
            <p class="text-muted mb-0">Resumo geral e monitoring das atividades da oficina.</p>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm bg-primary text-white h-100">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <h6 class="text-uppercase fw-bold small text-white-50">Clientes</h6>
                    <h2 class="mb-0 fw-bold"><?= $metricas['total_clientes'] ?></h2>
                </div>
                <i class="bi bi-people fs-1 opacity-50 d-none d-sm-block"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm bg-success text-white h-100">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <h6 class="text-uppercase fw-bold small text-white-50">Faturamento</h6>
                    <h2 class="mb-0 fw-bold" style="font-size: 1.6rem;"><?= formatarMoeda($metricas['faturamento']) ?></h2>
                </div>
                <i class="bi bi-cash-coin fs-1 opacity-50 d-none d-sm-block"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm bg-warning text-dark h-100">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <h6 class="text-uppercase fw-bold small text-dark-50">Orçamentos / OS</h6>
                    <h2 class="mb-0 fw-bold"><?= $metricas['total_pedidos'] ?></h2>
                </div>
                <i class="bi bi-file-earmark-text fs-1 opacity-50 d-none d-sm-block"></i>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3">
        <div class="card border-0 shadow-sm bg-dark text-white h-100">
            <div class="card-body d-flex align-items-center justify-content-between p-4">
                <div>
                    <h6 class="text-uppercase fw-bold small text-white-50">Serviços Base</h6>
                    <h2 class="mb-0 fw-bold"><?= count($listaServicos) ?></h2>
                </div>
                <i class="bi bi-wrench fs-1 opacity-50 d-none d-sm-block"></i>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title mb-0 fw-bold text-dark text-uppercase small">
                    <i class="bi bi-clock-history me-2 text-primary"></i>Últimos Orçamentos e Serviços Feitos
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th class="ps-3">Nº Orçamento</th>
                                <th>Cliente</th>
                                <th>Data de Entrada</th>
                                <th>Status</th>
                                <th class="text-end pe-3">Valor Total</th>
                                <th class="text-end pe-3">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($ultimosPedidos)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted small">Nenhum orçamento emitido até o momento.</td>
                            </tr>
                            <?php else: foreach($ultimosPedidos as $p): ?>
                            <tr>
                                <td class="ps-3 fw-bold text-secondary">#<?= str_pad($p['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                <td><strong class="text-dark"><?= htmlspecialchars($p['cliente']) ?></strong></td>
                                <td class="small text-muted"><?= date('d/m/Y H:i', strtotime($p['criado_em'])) ?></td>
                                <td>
                                    <?php if($p['status'] === 'aprovado'): ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Aprovado</span>
                                    <?php elseif($p['status'] === 'cancelado'): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Cancelado</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Pendente</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end fw-bold text-dark pe-3"><?= formatarMoeda($p['total']) ?></td>
                                <td class="text-end pe-3">
                                    <a href="index.php?p=pedido&id=<?= $p['id'] ?>" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-eye me-1"></i> Detalhes
                                    </a>
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

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-primary fw-bold small text-uppercase"><i class="bi bi-person-badge me-2"></i>Equipe / Funcionários</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th class="ps-3">Nome</th>
                                <th>Nível</th>
                                <th>E-mail</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($listaUsuarios)): ?>
                                <tr><td colspan="3" class="text-center py-4 text-muted small">Nenhum funcionário cadastrado.</td></tr>
                            <?php else: foreach($listaUsuarios as $user):
                                if ($user['nivel'] === 'cliente') { continue; }
                                ?>
                                <tr>
                                    <td class="ps-3 fw-semibold text-dark small"><?= htmlspecialchars($user['nome']) ?></td>
                                    <td><span class="badge rounded-pill bg-info-subtle text-info border border-info-subtle"><?= ucfirst($user['nivel']) ?></span></td>
                                    <td class="text-muted small"><?= htmlspecialchars($user['email']) ?></td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 text-success fw-bold small text-uppercase"><i class="bi bi-people me-2"></i>Clientes</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th class="ps-3">Nome do Cliente</th>
                                <th>E-mail de Contato</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($listaUsuarios)): ?>
                                <tr><td colspan="3" class="text-center py-4 text-muted small">Nenhum Cliente cadastrado.</td></tr>
                            <?php else: foreach($listaUsuarios as $user):
                                if ($user['nivel'] !== 'cliente') { continue; }
                                ?>
                                <tr>
                                    <td class="ps-3 fw-semibold text-dark small"><?= htmlspecialchars($user['nome']) ?></td>
                                    <td><span class="badge rounded-pill bg-info-subtle text-info border border-info-subtle"><?= ucfirst($user['nivel']) ?></span></td>
                                    <td class="text-muted small"><?= htmlspecialchars($user['email']) ?></td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1055;">
    <div id="orcamentoToast" class="toast border-0 shadow" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="7000">
        <div class="toast-header bg-success text-white">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong class="me-auto">Oficina Pro Alerta</strong>
            <small>Agora mesmo</small>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body bg-white text-dark">
            <span id="toastMensagem">Orçamento processado com sucesso!</span>
        </div>
    </div>
</div>