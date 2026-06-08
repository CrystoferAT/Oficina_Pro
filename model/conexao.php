<?php
function getConexao() {
    global $banco;


    if ($banco === null) {
        $banco = new mysqli("localhost", "root", "", "oficina_pro", 3306);

        if ($banco->connect_error) {
            die("Erro na conexão: " . $banco->connect_error);
        }

        $banco->set_charset("utf8mb4");
    }

    return $banco;
}
