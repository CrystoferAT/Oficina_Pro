<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}
require_once __DIR__ . "/conexao.php";

global $banco;
if (!isset($banco) || $banco === null) {
    getConexao(); 
}

function validarLogin($email, $pass) {
    global $banco;

    $stmt = $banco->prepare("SELECT id, nome, nivel, senha FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        $senhaValida = password_verify($pass, $usuario['senha']) || $pass === $usuario['senha'];
        if ($senhaValida) {
            $_SESSION['usuario_id']    = $usuario['id'];
            $_SESSION['usuario_nome']  = $usuario['nome'];
            $_SESSION['usuario_nivel'] = $usuario['nivel'];
            $_SESSION['autenticado']   = true;
            return true;
        }
    }
    return false;
}

function gerarId($array) {
    if (empty($array)) return 1;
    $ultimoElemento = end($array);
    return isset($ultimoElemento['id']) ? $ultimoElemento['id'] + 1 : count($array) + 1;
}

function formatarMoeda($valor) {
    return "R$ " . number_format($valor, 2, ',', '.');
}

// ==========================================
//  FUNÇÕES DE SERVIÇOS ADAPTADAS PARA O BANCO
// ==========================================

function salvarServico($dados){
    global $banco;
    
    $nome = $dados['servico'];
    $tempo = !empty($dados['tempo']) ? (int)$dados['tempo'] : 0;
    $maoDeObra = (float)$dados['precoServico'];

    $stmt = $banco->prepare("INSERT INTO servicos (nome, tempo_estimado_minutos, mao_de_obra, ativo) VALUES (?, ?, ?, 1)");
    $stmt->bind_param("sid", $nome, $tempo, $maoDeObra);
    return $stmt->execute();
}

function listarServicos(){
    global $banco;
    $resultado = $banco->query("SELECT id, nome, tempo_estimado_minutos, mao_de_obra, ativo FROM servicos ORDER BY id DESC");
    return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
}

function editarServicos($id, $novosDados){
    global $banco;
    
    $nome = $novosDados['servico'];
    $tempo = !empty($novosDados['tempo']) ? (int)$novosDados['tempo'] : 0;
    $maoDeObra = (float)$novosDados['precoServico'];

    $stmt = $banco->prepare("UPDATE servicos SET nome = ?, tempo_estimado_minutos = ?, mao_de_obra = ? WHERE id = ?");
    $stmt->bind_param("sidi", $nome, $tempo, $maoDeObra, $id);
    return $stmt->execute();
}

function excluirServicos($id){
    global $banco;
    $stmt = $banco->prepare("DELETE FROM servicos WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// ==========================================
//  RESTANTE DAS FUNÇÕES DO SISTEMA
// ==========================================

function cadastrarUsuario($dadosUsuario) {
    global $banco;

    $stmt = $banco->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $dadosUsuario['email']);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) return false;

    $senhaHash = password_hash($dadosUsuario['senha'], PASSWORD_DEFAULT);

    $stmt = $banco->prepare("INSERT INTO usuarios (nome, nivel, email, senha) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss",
        $dadosUsuario['nome'],
        $dadosUsuario['nivel'],
        $dadosUsuario['email'],
        $senhaHash
    );
    return $stmt->execute();
}

function listarUsuarios() {
    global $banco;
    $resultado = $banco->query("SELECT id, nome, email, nivel FROM usuarios ORDER BY id ASC");
    return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
}

function editarUsuario($id, $dadosUsuario) {
    global $banco;

    if (!empty($dadosUsuario['senha'])) {
        $senhaHash = password_hash($dadosUsuario['senha'], PASSWORD_DEFAULT);
        $stmt = $banco->prepare("UPDATE usuarios SET nome=?, nivel=?, email=?, senha=? WHERE id=?");
        $stmt->bind_param("ssssi",
            $dadosUsuario['nome'],
            $dadosUsuario['nivel'],
            $dadosUsuario['email'],
            $senhaHash,
            $id
        );
    } else {
        $stmt = $banco->prepare("UPDATE usuarios SET nome=?, nivel=?, email=? WHERE id=?");
        $stmt->bind_param("sssi",
            $dadosUsuario['nome'],
            $dadosUsuario['nivel'],
            $dadosUsuario['email'],
            $id
        );
    }
    return $stmt->execute();
}

function excluirUsuario($id) {
    global $banco;
    $stmt = $banco->prepare("DELETE FROM usuarios WHERE id=?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

function gerarCaptcha() {
    $n1 = rand(1, 9);
    $n2 = rand(1, 9);
    $_SESSION['captcha_soma'] = $n1 + $n2;
    return "Quanto é $n1 + $n2?";
}

function cadastrarCliente($nome, $email, $senha) {
    if (empty($nome) || empty($email) || empty($senha)) return false;

    if (!isset($_SESSION['clientes'])) $_SESSION['clientes'] = [];

    foreach ($_SESSION['clientes'] as $c) {
        if ($c['email'] === $email) return false;
    }

    $id = gerarId($_SESSION['clientes']);
    $_SESSION['clientes'][] = [
        'id'    => $id,
        'nome'  => $nome,
        'email' => $email,
        'senha' => $senha,
        'nivel' => 'cliente'
    ];
    return true;
}

function listarClientes(){
    return $_SESSION['clientes'] ?? [];
}

function adicionarAoOrcamento($id) {
    global $banco;
    $stmt = $banco->prepare("SELECT id, nome, tempo_estimado_minutos, mao_de_obra FROM servicos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows === 1) {
        if (!isset($_SESSION['orcamento_atual'])) $_SESSION['orcamento_atual'] = [];
        $_SESSION['orcamento_atual'][] = $resultado->fetch_assoc();
        return true;
    }
    return false;
}

function listarItensOrcamento() {
    return $_SESSION['orcamento_atual'] ?? [];
}

function calcularTotalOrcamento() {
    $total = 0;
    foreach (listarItensOrcamento() as $item) {
        $total += $item['mao_de_obra'] ?? 0;
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
            $total += $item['valorTotal'] ?? 0;
        }
    }
    return $total;
}

function finalizarPagamento($metodoPagamento) {
    if (!isset($_SESSION['pedidos_realizados'])) $_SESSION['pedidos_realizados'] = [];
    $pedido = [
        'id'        => count($_SESSION['pedidos_realizados']) + 1,
        'cliente'   => $_SESSION['usuario_nome'] ?? 'Cliente Geral',
        'criado_em' => date('Y-m-d H:i:s'),
        'status'    => 'aprovado',
        'itens'     => listarItensOrcamento(),
        'total'     => calcularTotalOrcamento(),
        'metodo'    => $metodoPagamento
    ];
    $_SESSION['pedidos_realizados'][] = $pedido;
    limparOrcamento();
    return $pedido['id'];
}

function obterMetricasDashboard() {
    global $banco;
    
    $resClientes = $banco->query("SELECT COUNT(id) as total FROM usuarios WHERE nivel = 'cliente'");
    $totalClientes = $resClientes ? $resClientes->fetch_assoc()['total'] : 0;
    
    $totalPedidos = isset($_SESSION['pedidos_realizados']) ? count($_SESSION['pedidos_realizados']) : 0;
    
    $faturamento = 0;
    if (isset($_SESSION['pedidos_realizados'])) {
        foreach ($_SESSION['pedidos_realizados'] as $p) {
            $faturamento += $p['total'];
        }
    }
    
    return [
        'total_clientes' => $totalClientes,
        'total_pedidos'  => $totalPedidos,
        'faturamento'    => $faturamento
    ];
}

function listarUltimosPedidos($limite = 5) {
    $pedidos = $_SESSION['pedidos_realizados'] ?? [];
    return array_slice(array_reverse($pedidos), 0, $limite);
}
?>