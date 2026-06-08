<?php
verificarAcesso();

$pedidoId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$pedido = obterPedidoPorId($pedidoId);
$itensPedido = $pedido ? listarItensPedido($pedidoId) : [];
$cookiePedido = isset($_COOKIE['ultimo_pedido']) ? htmlspecialchars($_COOKIE['ultimo_pedido']) : '';
$pagina = isset($_GET['p']) ? $_GET['p'] : 'pedido';

if (!$pedido) {
    echo "<div class='alert alert-warning shadow-sm border-0'>Pedido não encontrado ou não existe.</div>";
    return;
}
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-start mb-4 gap-3 flex-column flex-md-row">
        <div>
            <h2 class="fw-bold"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Detalhes do Pedido</h2>
            <p class="text-muted mb-0">Visualize o conteúdo, valores e status do orçamento/pedido.</p>
        </div>
        <div class="text-end">
            <a href="index.php?p=dash" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Voltar ao Dashboard
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title fw-bold">Resumo do Pedido</h5>
                    <p class="mb-2"><strong>Número:</strong> #<?= str_pad($pedido['id'], 5, '0', STR_PAD_LEFT) ?></p>
                    <p class="mb-2"><strong>Cliente:</strong> <?= htmlspecialchars($pedido['cliente']) ?></p>
                    <p class="mb-2"><strong>Status:</strong>
                        <?php if ($pedido['status'] === 'aprovado'): ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle">Aprovado</span>
                        <?php elseif ($pedido['status'] === 'cancelado'): ?>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Cancelado</span>
                        <?php else: ?>
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Pendente</span>
                        <?php endif; ?>
                    </p>
                    <p class="mb-2"><strong>Forma de Pagamento:</strong> <?= htmlspecialchars($pedido['forma_pagamento']) ?></p>
                    <p class="mb-2"><strong>Valor Total:</strong> <?= formatarMoeda($pedido['total']) ?></p>
                    <p class="mb-0 text-muted"><strong>Criado em:</strong> <?= date('d/m/Y H:i', strtotime($pedido['criado_em'])) ?></p>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">Itens do Pedido</h5>
                    <?php if (empty($itensPedido)): ?>
                        <div class="alert alert-secondary mb-0">Nenhum item registrado para este pedido.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light small text-uppercase">
                                    <tr>
                                        <th>Serviço</th>
                                        <th class="text-center">Qtd.</th>
                                        <th class="text-end">Mão de Obra</th>
                                        <th class="text-end">Total Item</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($itensPedido as $item): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($item['servico'] ?? 'Serviço removido') ?></td>
                                            <td class="text-center"><?= (int)$item['quantidade'] ?></td>
                                            <td class="text-end text-primary fw-bold"><?= formatarMoeda($item['valor_mao_de_obra']) ?></td>
                                            <td class="text-end fw-bold"><?= formatarMoeda($item['valor_total_item']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if ($cookiePedido): ?>
        <div class="alert alert-info small shadow-sm border-0">
            Último pedido registrado em cookie: <strong>#<?= $cookiePedido ?></strong>
        </div>
    <?php endif; ?>
</div>
