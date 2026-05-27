<?php
if(session_status() === PHP_SESSION_NONE){
        session_start();
    }
require_once __DIR__ . "/../conexao.php";

function gerarId($lista){
    if(empty($lista))return 1;
    $ultimo = end($lista);
    return $ultimo["id"] + 1;
}

function validarLogin($email, $pass) {
    if (isset($_SESSION['usuarios'])) {
        foreach ($_SESSION['usuarios'] as $u) {
            if ($u['email'] === $email && $u['senha'] === $pass) {
                $_SESSION['usuario_id'] = $u['id'];
                $_SESSION['usuario_nome'] = $u['nome'];
                $_SESSION['usuario_nivel'] = $u['nivel']; 
                $_SESSION['autenticado'] = true;
                return true;
            }
        }
    }

    
    if (isset($_SESSION['clientes'])) {
        foreach ($_SESSION['clientes'] as $c) {
            if ($c['email'] === $email && $c['senha'] === $pass) {
                $_SESSION['usuario_nome'] = $c['nome'];
                $_SESSION['usuario_nivel'] = 'cliente'; 
                $_SESSION['autenticado'] = true;
                return true;
            }
        }
    }
    
    return false;
}

function formatarMoeda($valor) {
    return "R$ " . number_format($valor, 2, ',', '.');
}

    
    if(!isset($_SESSION['servicos_cadastrados'])){
        $_SESSION['servicos_cadastrados']=[];
    }

    function salvarServico($dados){
        $total = (float)$dados['precoServico']+(float)$dados['precoPeca'];

        $id = gerarId($_SESSION['servico']);

        $novoServico = [
            'id' => $id,
            'servico' => $dados['servico'],
            'tempo' => $dados['tempo'],
            'precoServico' => (float)$dados['precoServico'],
            'pecas' => $dados['pecas'],
            'precoPeca' => (float)$dados['precoPeca'],
            'valorTotal' => $total
        ];

        $_SESSION['servicos_cadastrados'][] = $novoServico;
    }

    function listarServicos(){
        return $_SESSION['servicos_cadastrados']?? [];
        }

    function editarServicos($indice, $novosDados){
        if(isset($_SESSION['servicos_cadastrados'][$indice])){
        $total = (float)$novosDados['precoServico']+(float)$novosDados['precoPeca'];
            $_SESSION['servicos_cadastrados'][$indice] = [
                'servico'       => $novosDados['servico'],
                'precoServico'  => (float)$novosDados['precoServico'],
                'pecas'         => $novosDados['pecas'],
                'precoPeca'     => (float)$novosDados['precoPeca'],
                'valorTotal'    => $total
            ];
            return true;        
        }
        return false;
    }

    function excluirServicos($indice){
        if(isset($_SESSION['servicos_cadastrados'][$indice])){
            unset($_SESSION['servicos_cadastrados'][$indice]);

            $_SESSION['servicos_cadastrados'] = array_values($_SESSION['servicos_cadastrados']);
            return true;
        }
        return false;
    }
    function cadastrarUsuario($dadosUsuario){
        if(!isset($_SESSION['usuarios'])){
            $_SESSION['usuarios'] = [];
        }
        $id = gerarId($_SESSION['usuarios']); 
        $usuario = [
            'id'    =>$id,
            'nome'  => $dadosUsuario['nome'],
            'nivel' => $dadosUsuario['nivel'],
            'email' => $dadosUsuario['email'],
            'senha' => $dadosUsuario['senha']
        ];

        $_SESSION['usuarios'][] = $usuario;
    }


    function gerarCaptcha() {
        $n1 = rand(1, 9);
        $n2 = rand(1, 9);
        $_SESSION['captcha_soma'] = $n1 + $n2;
        return "Quanto é $n1 + $n2?";
    }


function cadastrarCliente($nome, $email, $senha) {
    if (empty($nome) || empty($email) || empty($senha)) {
        return false; 
    }

    if (!isset($_SESSION['clientes'])) {
        $_SESSION['clientes'] = [];
    }

    foreach ($_SESSION['clientes'] as $c) {
        if ($c['email'] === $email) {
            return false; 
        }
    }

    $id = gerarId($_SESSION['clientes']);

    $_SESSION['clientes'][] = [
        'id' => $id ,
        'nome' => $nome,
        'email' => $email,
        'senha' => $senha, 
        'nivel' => 'cliente'
    ];

    return true; 
}

function listarClientes(){
        return $_SESSION['clientes']?? [];
        }

function listarUsuarios(){
        return $_SESSION['usuarios']?? [];
        }


function adicionarAoOrcamento($indice) {
    $servicos = listarServicos();
    
    if (isset($servicos[$indice])) {
        if (!isset($_SESSION['orcamento_atual'])) {
            $_SESSION['orcamento_atual'] = [];
        }
        
        $_SESSION['orcamento_atual'][] = $servicos[$indice];
        return true;
    }
    return false;
}

function listarItensOrcamento() {
    return $_SESSION['orcamento_atual'] ?? [];
}

function calcularTotalOrcamento() {
    $itens = listarItensOrcamento();
    $total = 0;
    foreach ($itens as $item) {
        $total += $item['valorTotal'];
    }
    return $total;
}

function limparOrcamento() {
    unset($_SESSION['orcamento_atual']);
}

function removerDoOrcamento($indice) {
    if (isset($_SESSION['orcamento_atual'][$indice])) {
        unset($_SESSION['orcamento_atual'][$indice]);
        
        $_SESSION['orcamento_atual'] = array_values($_SESSION['orcamento_atual']);
        return true;
    }
    return false;
}

if (!isset($_SESSION['usuarios']) || empty($_SESSION['usuarios'])) {
    $_SESSION['usuarios'] = [
        [
            'id'    => 1,
            'nome'  => 'Administrador',
            'email' => 'admin@email.com',
            'senha' => '123', 
            'nivel' => 'admin'
        ]
    ];
}

function verificarAcesso($nivelRequerido = null) {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: index.php?p=login&erro=restrito");
        exit;
    }

    if ($nivelRequerido && $_SESSION['usuario_nivel'] !== $nivelRequerido) {
        header("Location: index.php?p=home&erro=permissao");
        exit;
    }
}

function calcularTotalCarrinho() {
    $total = 0;
    if (isset($_SESSION['carrinho'])) {
        foreach ($_SESSION['carrinho'] as $item) {
            $total += $item['valorTotal'];
        }
    }
    return $total;
}

function finalizarPagamento($metodoPagamento) {
    if (!isset($_SESSION['pedidos_realizados'])) {
        $_SESSION['pedidos_realizados'] = [];
    }

    $pedido = [
        'id_pedido' => uniqid(), 
        'itens'     => listarItensOrcamento(),
        'total'     => calcularTotalOrcamento(),
        'metodo'    => $metodoPagamento,
        'data'      => date('d/m/Y H:i:s')
    ];

    $_SESSION['pedidos_realizados'][] = $pedido;

    limparOrcamento();

    return $pedido['id_pedido'];
}
?>
