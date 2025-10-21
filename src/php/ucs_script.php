<!DOCTYPE html>
<html lang="pt-BR">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Cadastro de UCs</title>
   <link rel="stylesheet" href="../bootstrap/bootstrap.css">
   <script src="../bootstrap/bootstrap.js"></script>
</head>
<body>

<?php
include "conexao_db.php";

$nomeuc = $_POST['nomeuc'];
$comp = $_POST['comp'];
$cargah = $_POST['cargah'];

// Atenção à ordem correta dos campos no banco: nomeuc, comp, cargah
$sql = "INSERT INTO uc (nomeuc, comp, cargah) VALUES ('$nomeuc', '$comp', '$cargah')";

if(mysqli_query($conn, $sql)){
    header("Location: tabela_uc.php");
    exit;
} else {
    echo "Erro: " . $conn->error;
}
?>


</body>
</html>
