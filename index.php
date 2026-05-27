<?php
    ob_start();
    session_start();
    require_once "model/funcoes.php";
    $pagina = isset($_GET['p']) ? $_GET['p'] : 'home';
    
?>
<!DOCTYPE html>
<html lang="pt-br" class="h-100">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Oficina Pro - Gestão</title>
        
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
        
        <style>
            html,body {
                min-height: 100vh;
                margin: 0;
                padding: 0;
            }
            body{
                display: flex;
                flex-direction: column;

            }
            main {
                flex: 1;
            }
        </style>
    </head>

    <body class="bg-light h-100">
    
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
            <div class="container">
                <a class="navbar-brand fw-bold text-uppercase" href="index.php?p=home">
                    <i class="bi bi-wrench-adjustable-circle text-primary me-2"></i>Oficina Pro
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="menuPrincipal">
                <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link <?= $pagina == 'home' ? 'active' : '' ?>" href="index.php?p=home">Home</a>
                        </li>

                        <?php if (isset($_SESSION['autenticado']) && $_SESSION['autenticado'] === true): ?>
                        <?php if ($_SESSION['usuario_nivel'] === 'admin' || $_SESSION['usuario_nivel'] !== 'cliente'): ?>
                            <li class="nav-item">
                                <a class="nav-link <?= $pagina == 'cadastro_usuarios' ? 'active' : '' ?>" href="index.php?p=cadastro_usuarios">Funcionários</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $pagina == 'cadastro_servicos' ? 'active' : '' ?>" href="index.php?p=cadastro_servicos">Novos Serviços</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $pagina == 'servicos' ? 'active' : '' ?>" href="index.php?p=servicos">Listagem</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $pagina == 'dash' ? 'active' : '' ?>" href="index.php?p=dash">DashBoard</a>
                            </li>
                            <li class="nav-item ms-lg-2">
                                    <li class="nav-item"><a class="nav-link text-danger"  href="logout.php">Sair</a></li>
                            </li>
                            
                    
                        <?php endif; ?>
                            
                        <?php if ($_SESSION['usuario_nivel'] === 'cliente'): ?>
                            
                            <li class="nav-item">
                                <a class="nav-link <?= $pagina == 'Orcamento' ? 'active' : '' ?>" href="index.php?p=Orcamento">Orçamento</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $pagina == 'Carrinho' ? 'active' : '' ?>" href="index.php?p=Carrinho">Carrinho</a>
                            </li>
                        
                            <li class="nav-item ms-lg-2">
                                <a class="btn btn-outline-light btn-sm mt-1" href="logout.php">
                                    Sair
                                </a>
                            </li>
                        
                        <?php endif; ?>

                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link <?= $pagina == 'login' ? 'active' : '' ?>" href="index.php?p=login">Entrar</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $pagina == 'cadastro' ? 'active' : '' ?>" href="index.php?p=cadastro">Criar Conta</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <?php require_once "view/includes/header.php"; ?>
        </nav>

        <main class="flex-grow-1">
            <div class="container py-4">
                <?php
                switch ($pagina) {
                    case 'home':

                        $botoes = "";
                        if (!isset($_SESSION['autenticado'])) {
                            $botoes = "
                            <div class='d-flex justify-content-center gap-2'>
                                <a href='?p=login' class='btn btn-dark btn-lg px-4'>Login</a>
                                <a href='?p=cadastro' class='btn btn-outline-secondary btn-lg px-4'>Cadastre-se</a>
                            </div>";
                        }
                        
                        echo "
                        <div class='p-5 mb-4 bg-white border rounded-3 shadow-sm'>
                            <div class='container-fluid py-5 text-center'>
                                <h1 class='display-4 fw-bold text-dark'>Bem vindo(a) à Oficina.</h1>
                                <hr class='my-4 mx-auto w-25'>
                                $botoes
                            </div>
                        </div>";
                        break;
                    case 'cadastro_usuarios':
                        verificarAcesso('admin');
                        include 'view/cadastroUsuarios.php';
                        break;
                    case 'cadastro':
                        include 'view/cadastro_usuario.php';
                        break;
                    case 'cadastro_servicos':
                        verificarAcesso('admin');
                        include 'view/cadastroServicos.php';
                        break;
                    case 'login':
                        include 'view/login.php';
                        break;
                    case 'servicos':
                        include 'view/servicosCadastrados.php';
                        break;
                    case 'Orcamento':
                        include 'view/orcamento.php';
                        break;
                    case 'Carrinho':
                        include 'view/carrinho.php';
                        break;
                    case 'dash':
                        verificarAcesso('admin');
                        include 'view/dashboard.php';
                        break;
                    case 'pagamento':
                        include 'view/pagamento.php';
                        break;
                    case 'sucesso':
                        $numPedido = isset($_GET['pedido']) ? htmlspecialchars($_GET['pedido']) : '';
                        echo "
                        <div class='p-5 mb-4 bg-white border rounded-3 shadow-sm'>
                            <div class='container-fluid py-5 text-center'>
                                <i class='bi bi-check-circle-fill text-success' style='font-size: 4rem;'></i>
                                <h1 class='display-5 fw-bold text-dark mt-3'>Pagamento Aprovado!</h1>
                                <p class='fs-4 text-muted'>Obrigado por fechar negócio com a Oficina Pro.</p>
                                <div class='bg-light p-3 rounded-3 d-inline-block mt-2 mb-4 border'>
                                    <span class='fs-5'>Número do seu pedido:</span><br>
                                    <strong class='fs-3 text-primary'>{$numPedido}</strong>
                                </div>
                                <br>
                                <a href='index.php?p=home' class='btn btn-dark btn-lg px-4'>
                                    <i class='bi bi-house-door me-2'></i>Voltar ao Início
                                </a>
                            </div>
                        </div>";
                        break;
                    default:
                        echo "
                        <div class='alert alert-danger shadow-sm border-0 d-flex align-items-center' role='alert'>
                            <i class='bi bi-exclamation-triangle-fill fs-2 me-3'></i>
                            <div>
                                <h4 class='alert-heading mb-0'>Erro 404: Página não encontrada!</h4>
                                <p class='mb-0'>O link que você seguiu pode estar quebrado ou a página foi removida.</p>
                            </div>
                        </div>";
                }
                ?>
            </div>
        </main>

        <footer class="bg-dark text-secondary py-3 mt-auto border-top border-secondary text-center">
            <div class="container">
                <?php require_once "view/includes/footer.php"; ?>
                <p class="mb-0 small text-uppercase fw-light" style="letter-spacing: 1px;">
                    &copy; <?= date('Y') ?> - Oficina Pro - Sistema de Gestão Interna
                </p>
            </div>
        </footer>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
<?php
ob_end_flush(); 
?>
