<?php
    $totalCarrinho = calcularTotalOrcamento();
    $pagina = isset($_GET['p']) ? $_GET['p'] : 'pagamento';
?>

<div class="container py-4">
    <h2 class="fw-bold"><i class="bi bi-wallet2 me-2 text-primary"></i>Finalizar Pagamento</h2>

    <?php if ($totalCarrinho == 0): ?>
        <div class="alert alert-warning shadow-sm border-0">
            <i class="bi bi-exclamation-circle-fill me-2"></i> Seu carrinho está vazio! Vá para os orçamentos para adicionar serviços.
        </div>
    <?php else: ?>
        <div class="card shadow-sm border-0 mt-4" style="max-width: 500px;">
            <div class="card-body p-4">
                <h4 class="card-title text-center mb-4">
                    Total a Pagar: <br>
                    <span class="text-success display-6 fw-bold"><?= formatarMoeda($totalCarrinho) ?></span>
                </h4>

                <form action="controller/PagamentoController.php" method="POST">
                    <div class="mb-4">
                        <label for="metodo_pagamento" class="form-label fw-bold">Escolha a forma de pagamento:</label>
                        <select class="form-select form-select-lg" name="metodo_pagamento" id="metodo_pagamento" required>
                            <option value="">Selecione...</option>
                            <option value="PIX">PIX (5% de desconto)</option>
                            <option value="Cartao de Credito">Cartão de Crédito</option>
                            <option value="Dinheiro">Dinheiro na Oficina</option>
                        </select>
                    </div>

                    <div class="d-grid gap-3">
                        <button type="submit" name="pagar" class="btn btn-success btn-lg shadow-sm">
                            <i class="bi bi-check-circle me-1"></i> Simular Pagamento
                        </button>
                        <a href="index.php?p=Carrinho" class="btn btn-outline-secondary">Voltar ao Orçamento</a>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>