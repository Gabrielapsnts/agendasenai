<?php
session_start(); // inicia a sessão
session_unset(); // limpa todas as variáveis de sessão
session_destroy(); // destrói a sessão
header("Location: index.php"); 
exit();
?>