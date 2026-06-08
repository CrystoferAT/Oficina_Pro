<?php
    $pergunta = gerarCaptcha();

    $erro_tipo = $_GET['erro'] ?? '';
    $status    = $_GET['status'] ?? '';
    $erro_msg  = "";

    if ($erro_tipo === 'captcha') {
        $erro_msg = "Soma do CAPTCHA incorreta!";
    } elseif ($erro_tipo === 'dados') {
        $erro_msg = "E-mail ou senha inválidos!";
    }
?>

<div class="container">
    <div class="row justify-content-center align-items-center vh-100">
        <div class="col-md-4">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-dark text-white text-center py-4">
                    <h3 class="mb-0 text-uppercase fw-bold">Sistema Oficina</h3>
                    <small class="text-secondary">Acesso Restrito</small>
                </div>
                <div class="card-body p-4">
                    
                    <?php if ($status === 'sucesso'): ?>
                        <div class="alert alert-success py-2 small text-center fw-bold" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i> Cadastro realizado com sucesso!
                        </div>
                    <?php endif; ?>

                    <?php if ($erro_msg): ?>
                        <div class="alert alert-danger py-2 small text-center" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= $erro_msg ?>
                        </div>
                    <?php endif; ?>

                    <form action="controller/LoginController.php" method="POST">
                        <input type="hidden" name="acao" value="login">

                        <div class="mb-3">
                            <label class="form-label small fw-bold">E-mail</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                <input type="email" name="email" class="form-control" placeholder="seu@email.com" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Senha</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                                <input type="password" name="senha" class="form-control" placeholder="••••••••" required>
                            </div>
                        </div>

                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body p-3">
                                <label class="form-label small fw-bold text-primary text-uppercase">Validação de Segurança</label>
                                <p class="mb-2">Quanto é: <span class="badge bg-white text-dark border shadow-sm px-3 py-2"><?= $pergunta ?></span></p>
                                <input type="number" name="captcha" class="form-control form-control-lg text-center" placeholder="?" required>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm fw-bold">
                                Entrar no Sistema
                            </button>
                            <div class="text-center mt-3">
                                <a href="index.php?p=cadastro" class="text-decoration-none small">Não tem conta? Cadastre-se</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3 bg-white border-0 text-muted">
                    <small>Esqueceu a chave de acesso? Fale com o Master.</small>
                </div>
            </div>
        </div>
    </div>
</div>