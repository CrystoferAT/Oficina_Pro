<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../model/funcoes.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'login') {
        $email           = trim($_POST['email'] ?? '');
        $senha           = $_POST['senha'] ?? '';
        $captcha_usuario = $_POST['captcha'] ?? '';

        // Valida captcha
        if ((int)$captcha_usuario !== (int)$_SESSION['captcha_soma']) {
            header("Location: ../index.php?p=login&erro=captcha");
            exit;
        }

        unset($_SESSION['captcha_soma']); // limpa ANTES do redirect

        if (validarLogin($email, $senha)) {
            header("Location: ../index.php?p=home");
        } else {
            header("Location: ../index.php?p=login&erro=dados");
        }
        exit;
    }
}
