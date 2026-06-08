<?php
require_once __DIR__ . "/../model/funcoes.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pagar'])) {
    $metodo = $_POST['metodo_pagamento'] ?? '';

    sleep(2);

    $idPedido = finalizarPagamento($metodo);
    if ($idPedido === false) {
        header("Location: ../index.php?p=Carrinho&erro=nao_processado");
        exit();
    }

    header("Location: ../index.php?p=sucesso&pedido=" . urlencode($idPedido));
    exit();
}

header("Location: ../index.php");
exit();
?>