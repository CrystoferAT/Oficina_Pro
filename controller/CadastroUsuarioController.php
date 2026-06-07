<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "../model/funcoes.php";

// CADASTRAR
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cadastrar'])) {
    $dadosUsuario = [
        'nome'  => trim($_POST['nome']),
        'nivel' => $_POST['nivel'],
        'email' => trim($_POST['email']),
        'senha' => $_POST['senha']
    ];

    $resultado = cadastrarUsuario($dadosUsuario);

    if ($resultado) {
        header("Location: ../index.php?p=cadastro_usuarios&sucesso=cadastro");
    } else {
        header("Location: ../index.php?p=cadastro_usuarios&erro=email_duplicado");
    }
    exit;
}

// EDITAR
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar'])) {
    $id = (int)$_POST['id'];
    $dadosUsuario = [
        'nome'  => trim($_POST['nome']),
        'nivel' => $_POST['nivel'],
        'email' => trim($_POST['email']),
        'senha' => $_POST['senha'] // pode vir vazio — editarUsuario só atualiza se preenchido
    ];

    $resultado = editarUsuario($id, $dadosUsuario);

    if ($resultado) {
        header("Location: ../index.php?p=cadastro_usuarios&sucesso=edicao");
    } else {
        header("Location: ../index.php?p=cadastro_usuarios&erro=edicao");
    }
    exit;
}

// EXCLUIR
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['excluir'])) {
    $id = (int)$_POST['id'];

    $resultado = excluirUsuario($id);

    if ($resultado) {
        header("Location: ../index.php?p=cadastro_usuarios&sucesso=exclusao");
    } else {
        header("Location: ../index.php?p=cadastro_usuarios&erro=exclusao");
    }
    exit;
}
?>
