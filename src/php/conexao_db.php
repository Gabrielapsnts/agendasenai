<?php

$server = "localhost";
$user = "root";
$pass = "";
$bd = "agendasenai";

// Tenta conectar
$conn = mysqli_connect($server, $user, $pass, $bd);

// Se der erro, mostra mensagem e para a execução
if (!$conn) {
    die("Erro na conexão: " . mysqli_connect_error());
}

// Se der certo, não imprime nada
?>
