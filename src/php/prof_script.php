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

$id_uc = isset($_POST['id_uc']) ? (int) $_POST['id_uc'] : 0;

if ($id_uc <= 0) {
    die("Curso (id_uc) não foi selecionado corretamente.");
}

$sql = "INSERT INTO professor (nomeprof, turnos) VALUES ('$nomeprof', '$turnos')";

if (mysqli_query($conn, $sql)) {
    $id_prof = mysqli_insert_id($conn);

 $sql_rel = "INSERT INTO professor_uc (id_prof, iduc) VALUES ($id_prof, $id_uc)";

    if (!mysqli_query($conn, $sql_rel)) {
        echo "Erro ao inserir relacionamento professor_uc: " . $conn->error;
        exit;
    }

    header("Location: tabela_prof.php");
    exit;
} else {
    echo "Erro: " . $conn->error;
}


?>
 </body>
 </html>