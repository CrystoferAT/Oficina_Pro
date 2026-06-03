<?php
$pagina = isset($_GET['p']) ? $_GET['p'] : 'cadastro';
$erro = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome  = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    $dadosUsuario = [
        'nome'  => $nome,
        'email' => $email,
        'senha' => $senha,
        'nivel' => 'cliente'
    ];

    if (cadastrarUsuario($dadosUsuario)) {
        // Redireciona para o login passando o status de sucesso na URL
        header("Location: index.php?p=login&status=sucesso");
        exit;
    } else {
        $erro = "Erro: Este e-mail já está cadastrado ou houve um problema no servidor!";
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 card shadow-sm p-4">
            <h2 class="mb-4 text-center">Cadastro de Novo Cliente</h2>
            
            <?php if ($erro): ?>
                <div class="alert alert-danger"><?= $erro ?></div>
            <?php endif; ?>

            <form action="index.php?p=cadastro" method="POST">
                <div class="mb-3">
                    <label class="form-label">Nome Completo</label>
                    <input type="text" name="nome" class="form-control" placeholder="Ex: João Silva" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" placeholder="email@exemplo.com" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Senha</label>
                    <input type="password" name="senha" class="form-control" placeholder="Crie uma senha" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Finalizar Cadastro</button>
            </form>
            
            <div class="mt-3 text-center">
                <a href="index.php?p=login" class="text-decoration-none">Já tem conta? Faça login</a>
            </div>
        </div>
    </div>
</div>