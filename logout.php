<?php
session_start();


unset($_SESSION['usuario_id']);
unset($_SESSION['usuario_nome']);
unset($_SESSION['usuario_nivel']);
unset($_SESSION['autenticado']);

//session_destroy();

header("Location: index.php?p=login");
exit;
?>
