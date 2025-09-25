<!-- A tabela precisa ter os seguintes itens:
 Nome Professor;
 Turno;
 Competencias a serem atendidas na UC;
 UC que atende.

 Outras funções a serem adicionada futuramente -->
 <?php
 
 ?>
 <!DOCTYPE html>
 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../bootstrap/bootstrap.css" >
 </head>
 <body>
<!-- aqui começa a navbar-->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <!-- Logo / Título -->
    <a class="navbar-brand" style="color:black" href="dashboard.php">AGENDA SENAI</a>

    <!-- Formulário de busca -->
    <form class="d-flex" role="search" method ="POST">
      <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search"/>
      <button class="btn btn-outline-primary" type="submit">Search</button>
    </form>

    <!-- Link de logout -->
    <ul class="navbar-nav">
  <li class="nav-item">
    <a class="nav-link active" aria-current="page" href="index.php">Logout</a>
  </li>
  </ul>
    </ul>
  </div>
</nav>
<div style="position: absolute; right: 50px; top: 65px;">
  <form action="prof_script.php" method = "POST">
    <div><h2>Cadastro</h2></div> 
    <div class="form-floating mb-3">
      <input type="text" class="form-control" id="professor" placeholder="Professor" name = "nomeprof" required>
      <label for="professor">Professor</label>
    </div>
    <div class="form-floating">
      <input type="text" class="form-control" id="turno" placeholder="Turno" name = "turnos" >
      <label for="turno">Turno</label>
    </div>
    <br>
    <select class="form-select" aria-label="Matérias" name="UCs" required>
  <option value="" selected disabled hidden>Matéria</option>
  <option value="Mecânica">Mecânica</option>
  <option value="Mecatrônica">Mecatrônica</option>
  <option value="Desenvolvimento de Sistemas">Desenvolvimento de Sistemas</option>
  <option value="Eletromecânica">Eletromecânica</option>
  <option value="Automação">Automação</option>
  <option value="Administração">Administração</option>
</select>
    <br>
    <select class="form-select" aria-label="Default select example"name="competencias">
  <option value="" selected disabled hidden>Competências</option>
  <option value="Lógica de Programação">Lógica de Programação</option>
  <option value="maozinha">maozinha</option>
  <option value="anao">anao</option>
</select>
    <br>
  <button class="btn btn-primary" type="submit" id="btn-cadastrar">Cadastro</button>
</div>
</form>

<!-- Tabela dinâmica de professores cadastrados -->
<div id="prof-table-wrapper" style="position: absolute; left: 15px; top: 70px; width: 1000px;">
  <table class="table table-bordered" id="prof-table" style="background: #fff;">
    <thead>
      <tr>
        <th>Professores</th>
        <th>Turno</th>
        <th>Matéria</th>
        <th>Competências</th>
      </tr>
    </thead>
    <tbody>
     <tbody>

  <!-- pega todos os professores do banco e cria as linhas da tabela na página -->
<?php
include "conexao_db.php";

$result = $conn->query("SELECT * FROM professor");
while($row = $result->fetch_assoc()){
    echo "<tr>
            <td>{$row['nomeprof']}</td>
            <td>{$row['turnos']}</td>
            <td>{$row['UCs']}</td>
            <td>{$row['competencias']}</td>
          </tr>";
}
?>
</tbody>
 
    </tbody>
  </table>

</div>
</div>
<table class="table"
   style="position: fixed; bottom: -5px; right: 60px; width: 200px; font-size: 14px;">
  <thead>
    <tr>
      <th scope="col">Turno</th>
      <th scope="col">ID</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">Manhã</th>
      <td>1</td>
    </tr>
    <tr>
      <th scope="row">Tarde</th>
      <td>2</td>
    </tr>
    <tr>
      <th scope="row">Noite</th>
      <td>3</td>
    </tr>
  </tbody>
</table>
 </body>
 </html>
 <style>
