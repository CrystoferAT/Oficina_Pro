<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../model/funcoes.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cadastrar'])) {
    $dadosUsuario = [
        'nome'  => trim($_POST['nome']  ?? ''),
        'nivel' => $_POST['nivel']  ?? 'cliente',
        'email' => trim($_POST['email'] ?? ''),
        'senha' => $_POST['senha'] ?? ''
    ];

    if (empty($dadosUsuario['nome']) || empty($dadosUsuario['email']) || empty($dadosUsuario['senha'])) {
        header("Location: ../index.php?p=cadastro_usuarios&erro=campos_vazios");
        exit;
    }

    $resultado = cadastrarUsuario($dadosUsuario);

    if ($resultado) {
        header("Location: ../index.php?p=cadastro_usuarios&sucesso=1");
    } else {
        header("Location: ../index.php?p=cadastro_usuarios&erro=email_duplicado");
    }
    exit;
}
