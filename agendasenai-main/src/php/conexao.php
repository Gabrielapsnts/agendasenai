<?php

$server = "localhost";
$user = "root";
$pass = "";
$bd = "agendasenai";

if ($conn = mysqli_connect($server, $user, $pass, $bd) ) {
    echo "Conexao deu boa!";
    
}else 
    echo "Se fodeu n deu certo!";



?>