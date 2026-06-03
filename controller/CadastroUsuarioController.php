<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../model/funcoes.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cadastrar'])) {
    $dadosUsuario = [
        'nome'  => trim($_POST['nome']),
        'nivel' => $_POST['nivel'],
        'email' => trim($_POST['email']),
        'senha' => $_POST['senha']
    ];

    $resultado = cadastrarUsuario($dadosUsuario);

    if ($resultado) {
        header("Location: ../index.php?p=cadastro_usuarios&sucesso=1");
    } else {
        header("Location: ../index.php?p=cadastro_usuarios&erro=email_duplicado");
    }
    exit;
}
?>
