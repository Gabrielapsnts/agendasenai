<!DOCTYPE html>
<html lang="pt-br">
<head>


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Senai</title>
     <link rel="stylesheet" href="../bootstrap/bootstrap.css">
     <link rel="stylesheet" href="../css/dashboard.css">
     <style>
      body {
         background-color: #033b83b9;
}  </style>
</head>
<body>

<div class="navbar-header">
  <a style="color:black" href="dashboard.php">AGENDA SENAI</a>
</div>

<ul class="nav justify-content-end navbar-white">

  <li class="nav-item">
    <a class="nav-link" href="index.php">Logout</a>
  </li>
</ul>


<!--aqui inicia os containers para separar os profs-->

<div class="container">

    <!--<div class="mod-card-header">-->
    <div class="card" style="width: 30rem;  margin-top: 200px; margin-left:70px margin-right: 70 px max-width: 70px">
      <img src="../assets/imagens/profs.png" class="card-img-top" alt="professores">
      <div class="card-body">
        <h5 class="card-title">Professores</h5>
        <p class="card-text">Visualizar a tabela de professores </p>
        <a href="tabela_prof.php" class="btn btn-primary">Ver tabela</a>
      </div>
    </div>
    <!--<div class="mod-card-header">-->
                  
    <div class="card" style="width: 30rem; margin-top: 200px; margin-left:70px">
      <img src="../assets/imagens/ucc.png" class="card-img-top" alt="professores">
      <div class="card-body">
        <h5 class="card-title">UC</h5>
        <p class="card-text">Visualizar a tabela de unidades curriculares no geral </p>
        <a href="tabela_uc.php" class="btn btn-primary">Ver tabela</a>
      </div>
    </div>
    <!--<div class="mod-card-header">-->
    <div class="card" style="width: 30rem; margin-top: 200px; margin-left:70px">
      <img src="../assets/imagens/agenda.png" class="card-img-top" alt="professores">
      <div class="card-body">
        <h5 class="card-title">Agenda</h5>
        <p class="card-text">Vizualizar a carga horária dos professores e suas agendas </p>
        <a href="tabela_agenda.php" class="btn btn-primary">Ver tabela</a>
      </div>
    </div>
    
    </div>
</div>

</body>
</html>