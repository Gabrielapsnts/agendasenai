<!-- A tabela precisa ter as seguintes coisas:
 Nome UC
 Carga horaria
 Turno
 Profs
 Competencias

 Coisas a serem adicionada futuramente -->

 <!DOCTYPE html>
 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../bootstrap/bootstrap.css">
 </head>
 <body>
 
 <style>
  body {
    position: relative;
    margin: 0;
    font-family: Arial, sans-serif;
  }

  .navbar-header {
    position: absolute;
    top: 00px; /* Distância do topo da página */
    left: 20px; /* Alterado para a esquerda */
    font-size: 24px;
    font-weight: bold;
    color: #000000;
  }
  
  .navbar-white {
    background-color: #ffffff;
    border-radius: 5px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }

  .nav-link {
    color: #000000;
  }

  .nav-link.active {
    color: #0d6efd;
  }

  .nav-link.disabled {
    color: #6c757d;
  }
</style>

<div class="navbar-header">
  <a style="color:black" href="dashboard.php">AGENDA SENAI</a>
</div>

<ul class="nav justify-content-end navbar-white">
  <li class="nav-item">
    <a class="nav-link active" aria-current="page" href="#">Active</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#">Link</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="#">Link</a>
  </li>
  <li class="nav-item">
    <a class="nav-link disabled" aria-disabled="true">Disabled</a>
  </li>
</ul>


 </body>
 </html>
 <style>
