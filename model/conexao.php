<?php
    $banco = new mysqli ("localhost", "root","", "oficina_pro");

    if($banco->connect_error){
        die("Erro na conexão". $banco->connect_error);
    }
    $banco->set_charset("utf8mb4");
?>