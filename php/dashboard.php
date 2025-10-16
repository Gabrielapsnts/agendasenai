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
          position: relative; /* importante para o ::before */
          background-color: #033b83b9; /* sua cor de fundo */
          margin: 0; /* remove margens padrões */
          min-height: 100vh; /* garante altura mínima */
      }
      
      body::before {
          content: "";
          position: fixed; /* fixa a imagem de fundo */
          top: 0;
          left: 0;
          width: 100%;
          height: 120vh; /* 120% da altura da viewport pra acompanhar seu background-size */
          background-image: url('../assets/imagens/imgdashboard.png');
          background-size: 100% 120%;
          background-position: left top;
          background-repeat: no-repeat;
          opacity: 0.3; /* controla a transparência aqui */
          z-index: -1; /* fica atrás do conteúdo */
          pointer-events: none; /* para a imagem não interferir em cliques */
      }
    </style>
</head>
<body>

<!-- aqui começa a navbar-->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div style= "background-color: #0a0d8d;" class="container-fluid d-flex justify-content-between align-items-center">
    <!-- Logo / Título -->
    <a class="navbar-brand" style="color:white" href="dashboard.php">AGENDA SENAI</a>

    


    <!-- Link de logout -->
    <ul class="navbar-nav">

 
      <li class="nav-item">
        <a class="nav-link active"style="color:white" aria-current="page" href="logout.php">Logout</a>
      </li>
    </ul>
  </div>
</nav>


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
        <h5 class="card-title">Cursos</h5>
        <p class="card-text">Visualizar a tabela de unidades curriculares</p>
        <a href="tabela_uc.php" class="btn btn-primary">Ver tabela</a>
      </div>
    </div>
    <!--<div class="mod-card-header">-->
    <div class="card" style="width: 30rem; margin-top: 200px; margin-left:70px">
      <img src="../assets/imagens/agenda.png" class="card-img-top" alt="professores">
      <div class="card-body">
        <h5 class="card-title">Agenda</h5>
        <p class="card-text">Vizualizar a agenda dos professores </p>
        <a href="tabela_agenda.php" class="btn btn-primary">Ver tabela</a>
      </div>
    </div>
    
    </div>
</div>

</body>
</html>