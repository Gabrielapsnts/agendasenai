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

if (isset($_POST['usuario']) && isset($_POST['senha'])) {
    $usuario = $_POST['usuario'];
    $senha   = $_POST['senha'];

    // Prepara a consulta (usando prepared statements pra evitar SQL Injection)
    $sql = "SELECT `usuario`, `senha` FROM `login` WHERE usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Se estiver usando senha criptografada no cadastro
        if ($senha === $row['senha']) {
         header("Location: dashboard.php");
            exit;
      }  else {
            echo '<div class="alert alert-danger">Senha incorreta.</div>';
      }
       
      } else{
         echo '<div class="alert alert-warning text-center mt-3" role="alert">Usuário não encontrado.</div>';
            }

         $stmt->close();
         } else{
            echo "Por favor, envie usuário e senha.";
               }
          
?>



 </body>
 </html>