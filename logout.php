<?php
session_start();

// Destrói todas as variáveis da sessão
session_unset();

// Destrói a sessão
session_destroy();

// Redireciona para a página inicial
header("Location: index.php");
exit;
?>
