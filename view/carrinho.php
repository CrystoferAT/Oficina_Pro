<?php
$pagina = isset($_GET['p']) ? $_GET['p'] : 'Carrinho'; 

if (isset($_GET['acao'])) {
    $acao = $_GET['acao'];
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
    
    if ($acao === 'add' && $id !== null) {
        adicionarAoOrcamento($id);
    } 
    elseif ($acao === 'remover' && $id !== null) {
        removerDoOrcamento($id);
    } 
    elseif ($acao === 'limpar') {
        limparOrcamento();
    }
    
    header("Location: index.php?p=Carrinho");
    exit;
}

$itensNoOrcamento = listarItensOrcamento();
$totalGeral = calcularTotalOrcamento();
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold text-dark">
            <i class="bi bi-cart3 text-primary me-2"></i>Seu Orçamento
        </h2>
        <div>
            <a href="index.php?p=Orcamento" class="btn btn-outline-primary btn-sm me-2">
                <i class="bi bi-arrow-left"></i> Adicionar mais serviços
            </a>
            <?php if (!empty($itensNoOrcamento)): ?>
                <a href="index.php?p=Carrinho&acao=limpar" class="btn btn-danger btn-sm shadow-sm" 
                   onclick="return confirm('Deseja realmente limpar todo o orçamento?')">
                    <i class="bi bi-trash"></i> Limpar Tudo
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-4">Serviço</th>
                            <th class="text-end pe-5">Valor da Mão de Obra</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($itensNoOrcamento)): ?>
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-cart-x fs-1"></i>
                                        <p class="mt-2">Seu orçamento está vazio no momento.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($itensNoOrcamento as $idServico => $item): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($item['nome']) ?></div>
                                    </td>
                                    <td class="text-end pe-5 fw-bold text-primary">
                                        <?= formatarMoeda($item['mao_de_obra']) ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="index.php?p=Carrinho&acao=remover&id=<?= $idServico ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           title="Remover este item">
                                            <i class="bi bi-x-circle me-1"></i> Retirar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($itensNoOrcamento)): ?>
                        <tfoot class="table-group-divider">
                            <tr class="table-light">
                                <td class="text-end fw-bold py-3 fs-5">Valor Total do Orçamento:</td>
                                <td class="text-end pe-5 fw-bold text-success py-3 fs-4">
                                    <?= formatarMoeda($totalGeral) ?>
                                </td>
                                <td></td>
                            </tr>
                        </footer>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <?php if (!empty($itensNoOrcamento)): ?>
        <div class="mt-4 d-flex justify-content-end gap-2">
            <button class="btn btn-outline-secondary btn-lg px-4 shadow-sm fw-bold" onclick="window.print()">
                <i class="bi bi-printer me-2"></i>Imprimir
            </button>

            <a href="index.php?p=pagamento" class="btn btn-success btn-lg px-5 shadow fw-bold">
                <i class="bi bi-credit-card me-2"></i>Finalizar Pagamento
            </a>
        </div>
    <?php endif; ?>
</div>