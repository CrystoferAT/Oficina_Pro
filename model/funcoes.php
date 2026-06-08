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
    $usuarioId = $_SESSION['usuario_id'] ?? null;
    $itens = listarItensOrcamento();
    $total = calcularTotalOrcamento();

    if (empty($itens) || !$usuarioId) {
        return false;
    }

    $pedidoId = salvarPedido($usuarioId, $total, $metodoPagamento, $itens, 'aprovado');
    if (!$pedidoId) {
        return false;
    }

    $_SESSION['ultimo_pedido_id'] = $pedidoId;
    $_SESSION['ultimo_pedido_total'] = $total;
    $_SESSION['ultimo_pedido_pagamento'] = $metodoPagamento;

    setcookie('ultimo_pedido', $pedidoId, time() + 86400, '/');
    setcookie('ultimo_metodo_pagamento', $metodoPagamento, time() + 86400, '/');
    $_COOKIE['ultimo_pedido'] = $pedidoId;
    $_COOKIE['ultimo_metodo_pagamento'] = $metodoPagamento;

    limparOrcamento();
    return $pedidoId;
}

function salvarPedido($usuarioId, $total, $metodoPagamento, $itens) {
    global $banco;
    $status = 'aprovado';
    if (func_num_args() >= 5) {
        $args = func_get_args();
        $status = $args[4];
    }

    $banco->begin_transaction();
    try {
        $stmt = $banco->prepare("INSERT INTO pedidos (usuario_id, total, status, forma_pagamento) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("idss", $usuarioId, $total, $status, $metodoPagamento);
        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        $pedidoId = $banco->insert_id;

        $agregado = [];
        foreach ($itens as $item) {
            $sid = $item['id'];
            if (!isset($agregado[$sid])) {
                $agregado[$sid] = [
                    'servico_id' => $sid,
                    'quantidade' => 0,
                    'valor_mao_de_obra' => 0.0,
                    'valor_pecas' => 0.0,
                ];
            }
            $agregado[$sid]['quantidade'] += 1;
            $agregado[$sid]['valor_mao_de_obra'] += (float)($item['mao_de_obra'] ?? 0);
        }

        $stmtItem = $banco->prepare(
            "INSERT INTO pedido_itens (pedido_id, servico_id, quantidade, valor_mao_de_obra, valor_pecas, valor_total_item) VALUES (?, ?, ?, ?, ?, ?)"
        );

        foreach ($agregado as $a) {
            $servicoId = $a['servico_id'];
            $quantidade = (int)$a['quantidade'];
            $valorMaoDeObra = (float)$a['valor_mao_de_obra'];
            $valorPecas = 0.00;
            $valorTotalItem = $valorMaoDeObra + $valorPecas;
            $stmtItem->bind_param("iiiddd", $pedidoId, $servicoId, $quantidade, $valorMaoDeObra, $valorPecas, $valorTotalItem);
            if (!$stmtItem->execute()) {
                throw new Exception($stmtItem->error);
            }
        }

        $banco->commit();
        return $pedidoId;
    } catch (Exception $e) {
        $banco->rollback();
        return false;
    }
}
function criarPedidoPendente() {
    $usuarioId = $_SESSION['usuario_id'] ?? null;
    if (!$usuarioId) return false;

    $itens = listarItensOrcamento();
    $total = calcularTotalOrcamento();
    if (empty($itens)) return false;

    return salvarPedido($usuarioId, $total, 'N/A', $itens, 'pendente');
}

function obterMetricasDashboard() {
    global $banco;
    
    $resClientes = $banco->query("SELECT COUNT(id) as total FROM usuarios WHERE nivel = 'cliente'");
    $totalClientes = $resClientes ? $resClientes->fetch_assoc()['total'] : 0;

    $resPedidos = $banco->query("SELECT COUNT(id) as total, COALESCE(SUM(total), 0) as faturamento FROM pedidos");
    $dadosPedidos = $resPedidos ? $resPedidos->fetch_assoc() : ['total' => 0, 'faturamento' => 0.00];

    return [
        'total_clientes' => $totalClientes,
        'total_pedidos'  => (int)$dadosPedidos['total'],
        'faturamento'    => (float)$dadosPedidos['faturamento']
    ];
}

function listarUltimosPedidos($limite = 5) {
    global $banco;

    $stmt = $banco->prepare(
        "SELECT p.id, u.nome as cliente, p.criado_em, p.status, p.total
         FROM pedidos p
         LEFT JOIN usuarios u ON u.id = p.usuario_id
         ORDER BY p.id DESC
         LIMIT ?"
    );
    $stmt->bind_param("i", $limite);
    $stmt->execute();
    $resultado = $stmt->get_result();

    return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
}

function obterPedidoPorId($pedidoId) {
    global $banco;

    $stmt = $banco->prepare(
        "SELECT p.id, p.usuario_id, u.nome as cliente, p.criado_em, p.status, p.total, p.forma_pagamento
         FROM pedidos p
         LEFT JOIN usuarios u ON u.id = p.usuario_id
         WHERE p.id = ?"
    );
    $stmt->bind_param("i", $pedidoId);
    $stmt->execute();
    $resultado = $stmt->get_result();

    return $resultado ? $resultado->fetch_assoc() : null;
}

function listarItensPedido($pedidoId) {
    global $banco;

    $stmt = $banco->prepare(
        "SELECT pi.id, s.nome as servico, pi.quantidade, pi.valor_mao_de_obra, pi.valor_pecas, pi.valor_total_item
         FROM pedido_itens pi
         LEFT JOIN servicos s ON s.id = pi.servico_id
         WHERE pi.pedido_id = ?"
    );
    $stmt->bind_param("i", $pedidoId);
    $stmt->execute();
    $resultado = $stmt->get_result();

    return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
}?>b4ca6c11cb3f25815ed079d991ca23de5cff6a
