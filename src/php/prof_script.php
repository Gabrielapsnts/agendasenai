<!DOCTYPE html>
 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professores</title>
    <link rel="stylesheet" href="../bootstrap/bootstrap.css">
    <script src="../bootstrap/bootstrap.js"></script>
 </head>
 <body>
 
 <?php
    include "conexao_db.php";

    $nomeprof = $_POST['nomeprof'];
    $turnos = $_POST['turnos'];
    $UCs = $_POST['UCs'] ;
    $competencias = $_POST['competencias'] ? implode(', ', $_POST['competencias']) :'';
   

    $sql = "INSERT INTO `professor`(`nomeprof`, `turnos`, `UCs`, `competencias`) VALUES ('$nomeprof','$turnos','$UCs','$competencias')";
    
    if(mysqli_query($conn, $sql)){
        // Tudo certo, só redireciona
        header("Location: tabela_prof.php");
        exit;
    } else {
        // Se der erro, mostra
        echo "Erro: " . $conn->error;
    }
?>
 </body>
 </html>