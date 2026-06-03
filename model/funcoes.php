<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . "/conexao.php";

// ─── LOGIN & USUÁRIOS ─────────────────────────────────────────────────────────
function validarLogin($email, $pass) {
    $banco = getConexao();
    $stmt = $banco->prepare("SELECT id, nome, nivel, senha FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();
    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();
        $stmt->close();
        if (password_verify($pass, $usuario['senha'])) {
            $_SESSION['usuario_id']    = $usuario['id'];
            $_SESSION['usuario_nome']  = $usuario['nome'];
            $_SESSION['usuario_nivel'] = $usuario['nivel'];
            $_SESSION['autenticado']   = true;
            return true;
        }
    } else {
        $stmt->close();
    }
    return false;
}

function cadastrarUsuario($dadosUsuario) {
    $banco = getConexao();
    $stmt = $banco->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $dadosUsuario['email']);
    $stmt->execute();
    $stmt->store_result();
    $existe = $stmt->num_rows > 0;
    $stmt->close();
    if ($existe) return false;

    $senhaHash = password_hash($dadosUsuario['senha'], PASSWORD_DEFAULT);
    $stmt = $banco->prepare("INSERT INTO usuarios (nome, nivel, email, senha) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $dadosUsuario['nome'], $dadosUsuario['nivel'], $dadosUsuario['email'], $senhaHash);
    $resultado = $stmt->execute();
    $stmt->close();
    return $resultado;
}

function formatarMoeda($valor) {
    return "R$ " . number_format((float)$valor, 2, ',', '.');
}

// ─── CONTROLE DE ACESSO ───────────────────────────────────────────────────────
/**
 * Bloqueia o acesso de usuários não autorizados e redireciona para a Home ou Login
 * @param string $nivelExigido 'admin' ou 'funcionario'
 */
function verificarAcesso($nivelExigido = null) {
    // 1. Se não estiver autenticado, manda direto para a página de login
    if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true) {
        header("Location: index.php?p=login&erro=autenticar");
        exit;
    }

    // 2. Se a página exigir um nível específico e o usuário não atingir esse requisito
    if ($nivelExigido !== null) {
        // Se a página exige ADMIN, mas o usuário NÃO É admin, barra ele!
        if ($nivelExigido === 'admin' && $_SESSION['usuario_nivel'] !== 'admin') {
            header("Location: index.php?p=home&erro=permissao");
            exit;
        }
        
        // Se a página exige FUNCIONARIO, apenas admin e funcionario podem passar
        if ($nivelExigido === 'funcionario' && $_SESSION['usuario_nivel'] === 'cliente') {
            header("Location: index.php?p=home&erro=permissao");
            exit;
        }
    }
}

// ─── SERVIÇOS PERSISTIDOS NO BANCO DE DADOS ──────────────────────────────────

function salvarServico($dados) {
    $banco = getConexao();
    
    $nome  = $dados['servico'];
    $tempo = !empty($dados['tempo']) ? (int)$dados['tempo'] : 0;
    $precoMaoObra = (float)$dados['precoServico'];

    $stmt = $banco->prepare("INSERT INTO servicos (nome, tempo_estimado_minutos, mao_de_obra) VALUES (?, ?, ?)");
    $stmt->bind_param("sid", $nome, $tempo, $precoMaoObra);
    $resultado = $stmt->execute();
    $stmt->close();
    return $resultado;
}

function listarServicos() {
    $banco = getConexao();
    
    $sql = "SELECT id, nome AS servico, tempo_estimado_minutos AS tempo, mao_de_obra AS precoServico 
            FROM servicos WHERE ativo = 1 ORDER BY id DESC";
    
    $resultado = $banco->query($sql);
    return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
}

function editarServicos($id, $novosDados) {
    $banco = getConexao();
    
    $nome  = $novosDados['servico'];
    $tempo = !empty($novosDados['tempo']) ? (int)$novosDados['tempo'] : 0;
    $precoMaoObra = (float)$novosDados['precoServico'];

    $stmt = $banco->prepare("UPDATE servicos SET nome = ?, tempo_estimado_minutos = ?, mao_de_obra = ? WHERE id = ?");
    $stmt->bind_param("sidi", $nome, $tempo, $precoMaoObra, $id);
    $resultado = $stmt->execute();
    $stmt->close();
    return $resultado;
}

function excluirServicos($id) {
    $banco = getConexao();
    $stmt = $banco->prepare("UPDATE servicos SET ativo = 0 WHERE id = ?");
    $stmt->bind_param("i", $id);
    $resultado = $stmt->execute();
    $stmt->close();
    return $resultado;
}

function listarUsuarios() {
    $banco = getConexao();
    $sql = "SELECT id, nome, email, nivel, criado_em FROM usuarios ORDER BY id DESC";
    
    $resultado = $banco->query($sql);
    return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
}


// ─── METODOS ADICIONADOS PARA A DASHBOARD & ORÇAMENTOS ────────────────────────

/**
 * Retorna apenas os usuários cadastrados com nível de 'cliente'
 */
function listarClientes() {
    $banco = getConexao();
    $sql = "SELECT id, nome, email, criado_em FROM usuarios WHERE nivel = 'cliente' ORDER BY nome ASC";
    $resultado = $banco->query($sql);
    return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Gera os dados numéricos de contagem para os cartões informativos do topo
 */
function obterMetricasDashboard() {
    $banco = getConexao();
    
    // Contagem de clientes ativos
    $clientes = $banco->query("SELECT COUNT(*) as total FROM usuarios WHERE nivel = 'cliente'")->fetch_assoc()['total'];
    // Contagem de serviços cadastrados no catálogo
    $servicos = $banco->query("SELECT COUNT(*) as total FROM servicos WHERE ativo = 1")->fetch_assoc()['total'];
    
    // Verifica se a tabela 'pedidos' ou 'orcamentos' existe para não quebrar o código
    $tabelaPedidos = $banco->query("SHOW TABLES LIKE 'pedidos'")->num_rows > 0;
    
    $pedidos = 0;
    $faturamento = 0.00;

    if ($tabelaPedidos) {
        $pedidos = $banco->query("SELECT COUNT(*) as total FROM pedidos")->fetch_assoc()['total'];
        $faturamento = $banco->query("SELECT SUM(total) as total FROM pedidos WHERE status = 'aprovado'")->fetch_assoc()['total'];
    }

    return [
        'total_clientes' => $clientes,
        'total_servicos' => $servicos,
        'total_pedidos'  => $pedidos,
        'faturamento'    => $faturamento ?? 0.00
    ];
}

/**
 * Puxa o histórico recente de orçamentos e serviços realizados
 */
function listarUltimosPedidos($limite = 5) {
    $banco = getConexao();
    
    // Verifica se a tabela 'pedidos' existe antes de fazer o SELECT
    if ($banco->query("SHOW TABLES LIKE 'pedidos'")->num_rows === 0) {
        return [];
    }

    $sql = "SELECT p.id, u.nome as cliente, p.total, p.status, p.criado_em 
            FROM pedidos p 
            INNER JOIN usuarios u ON p.usuario_id = u.id 
            ORDER BY p.id DESC LIMIT ?";
    
    $stmt = $banco->prepare($sql);
    $stmt->bind_param("i", $limite);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $stmt->close();
    
    return $resultado ? $resultado->fetch_all(MYSQLI_ASSOC) : [];
}

/**
 * Gera um CAPTCHA matemático simples e armazena a resposta correta na sessão
 * @return string Texto da pergunta gerada (ex: "5 + 8")
 */
function gerarCaptcha() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Gera dois números aleatórios entre 1 e 9
    $num1 = rand(1, 9);
    $num2 = rand(1, 9);

    // Bate exatamente com o $_SESSION['captcha_soma'] do seu LoginController.php
    $_SESSION['captcha_soma'] = $num1 + $num2;

    // Retorna o texto para exibir no HTML do login.php
    return "$num1 + $num2";
}

// ─── GERENCIAMENTO DO CARRINHO / ORÇAMENTO EM SESSÃO ─────────────────────────

/**
 * Adiciona um serviço do banco ao carrinho em sessão
 */
function adicionarAoOrcamento($idServico) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Inicializa o carrinho se não existir
    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }
    
    $banco = getConexao();
    $idServico = (int)$idServico;
    
    // Busca os dados atualizados do serviço no banco de dados
    $stmt = $banco->prepare("SELECT id, nome AS servico, mao_de_obra AS precoServico FROM servicos WHERE id = ? AND ativo = 1");
    $stmt->bind_param("i", $idServico);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows === 1) {
        $servico = $resultado->fetch_assoc();
        
        // Armazena o item usando o ID como chave para evitar duplicados no carrinho
        $_SESSION['carrinho'][$idServico] = [
            'id'           => $servico['id'],
            'servico'      => $servico['servico'],
            'precoServico' => (float)$servico['precoServico']
        ];
    }
    $stmt->close();
}

/**
 * Remove um item específico do carrinho usando seu ID
 */
function removerDoOrcamento($idServico) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['carrinho'][$idServico])) {
        unset($_SESSION['carrinho'][$idServico]);
    }
}

/**
 * Esvazia completamente o carrinho da sessão
 */
function limparOrcamento() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['carrinho'] = [];
}

/**
 * Retorna a lista de itens atualmente salvos na sessão do usuário
 */
function listarItensOrcamento() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return $_SESSION['carrinho'] ?? [];
}

/**
 * Calcula a soma total de todos os serviços presentes no carrinho
 */
function calcularTotalOrcamento() {
    $itens = listarItensOrcamento();
    $total = 0.00;
    foreach ($itens as $item) {
        $total += (float)$item['precoServico'];
    }
    return $total;
}
?>