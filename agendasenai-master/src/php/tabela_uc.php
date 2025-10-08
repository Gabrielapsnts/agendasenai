<?php
include "conexao_db.php";  

$sql = "SELECT iduc, nomeuc, cargah, comp FROM uc";
$result = $conn->query($sql);

if (!$result) {
    die("Erro na consulta: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Cadastro de UCs</title>
   <link rel="stylesheet" href="../bootstrap/bootstrap.css">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div style= "background-color: #0a0d8d;" class="container-fluid d-flex justify-content-between align-items-center">
    <!-- Logo / Título -->
    <a class="navbar-brand" style="color:white" href="dashboard.php">AGENDA SENAI</a>

    <!-- Formulário de busca -->
    <form class="d-flex" role="search" method="POST">
  <input class="form-control me-2" type="search" placeholder="Pesquisar" aria-label="Pesquisar" name="search" />
  <button type="submit" class="btn btn-outline-light">Buscar</button>
</form>


    <!-- Link de logout -->
    <ul class="navbar-nav">
<li class="nav-item">
       <a class="nav-link active"style="color:white" aria-current="page" href="tabela_prof.php">Professores</a>
 </li>
       <li class="nav-item">
        <a class="nav-link active"style="color:white" aria-current="page" href="tabela_agenda.php">Agenda</a>
 </li>
      <li class="nav-item">
        <a class="nav-link active"style="color:white" aria-current="page" href="logout.php">Logout</a>
      </li>
    </ul>
  </div>
</nav>

<!-- Formulário de cadastro -->
<div style="position: absolute; right: 50px; top: 65px;">
  <form action="ucs_script.php" method="POST">
    <div><h2>Cadastro de UC</h2></div> 
    
    <div class="form-floating mb-3">
      <input type="text" class="form-control" id="nomeuc" placeholder="Nome da UC" name="nomeuc" required>
      <label for="nomeuc">Curso</label>
    </div>

    <div class="form-floating mb-3">
      <input type="text" class="form-control" id="comp" placeholder="Nome da Competência" name="comp" required>
      <label for="comp">Competência</label>
    </div>

    <div class="form-floating mb-3">
      <input type="number" class="form-control" id="cargah" placeholder="Carga Horária" name="cargah" min="1" required>
      <label for="cargah">Carga Horária (horas)</label>
    </div>
    
    <button class="btn btn-primary" type="submit" id="btn-cadastrar">Cadastrar UCs</button>
  </form>
</div>

<!-- Tabela de UCs -->
<div id="uc-table-wrapper" style="position: absolute; left: 15px; top: 70px; width: 1000px;">
  <table class="table table-bordered" id="uc-table" style="background: #fff;">
    <thead>
      <tr>
        <th>Nome da UC</th>
        <th>Competências</th>
        <th>Carga horária</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . htmlspecialchars($row['nomeuc']) . "</td>";
              echo "<td>" . htmlspecialchars($row['comp']) . "</td>";
              echo "<td>" . htmlspecialchars($row['cargah']) . "h</td>";
              echo "<td>
                      <form method='POST' action='excluir_uc.php' onsubmit=\"return confirm('Tem certeza que deseja excluir esta UC?');\">
                        <input type='hidden' name='iduc' value='" . $row['iduc'] . "' />
                        <button type='submit' class='btn btn-primary btn-sm'>Excluir</button>
                      </form>
                    </td>";
              echo "</tr>";
          }
      } else {
          echo "<tr><td colspan='4'>Nenhuma UC cadastrada</td></tr>";
      }
      ?>
    </tbody>
  </table>
</div>

<!-- Rodapé fixo (vazio por enquanto) -->
<table class="table" style="position: fixed; bottom: -5px; right: 60px; width: 200px; font-size: 14px;">
  <!-- Conteúdo futuro -->
</table>

</body>
</html>
