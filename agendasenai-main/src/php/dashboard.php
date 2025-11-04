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
          position: relative;
          background: linear-gradient(135deg, #f0f6ff 0%, #e8f1ff 100%);
          margin: 0;
          min-height: 100vh;
          font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
      }
      
      body::before {
          content: "";
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 120vh;
          background-image: url('../assets/imagens/imgdashboard.png');
          background-size: 100% 120%;
          background-position: left top;
          background-repeat: no-repeat;
          
          opacity: 0.50;
          z-index: -1;
          pointer-events: none;
      }

      .navbar-white {
          background-color: #0a0d8d;
          border-radius: 0;
          box-shadow: 0 2px 8px rgba(10, 13, 141, 0.15);
      }

      .nav-link {
          color: #ffffff !important;
          transition: all 0.3s ease;
      }

      .nav-link:hover {
          opacity: 0.9;
      }

      .nav-link.active {
          color: #ffffff !important;
          font-weight: 600;
      }

      /* Melhorado o container com melhor layout em grid */
      .container {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
          gap: 30px;
          padding: 60px 40px;
          max-width: 1200px;
          margin: 0 auto;
      }

      /* Cards completamente redesenhados com cores suaves em azul */
      .card {
          background-color: #ffffff;
          border: none;
          border-radius: 12px;
          padding: 0;
          display: flex;
          flex-direction: column;
          box-shadow: 0 4px 15px rgba(58, 91, 219, 0.08);
          transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
          overflow: hidden;
          height: 100%;
      }

      .card:hover {
          transform: translateY(-8px);
          box-shadow: 0 12px 28px rgba(58, 91, 219, 0.15);
      }

      .card-img-top {
          width: 100%;
          height: 300px;
          object-fit: contain;
          display: block;
          background: linear-gradient(135deg, #e8f1ff 0%, #d4e4ff 100%);
          padding: 12px;
      }

      .card-body {
          padding: 40px;
          display: flex;
          flex-direction: column;
          flex-grow: 1;
          background-color: #ffffff;
      }

      .card-title {
          font-size: 20px;
          font-weight: 700;
          color: #0a0d8d;
          margin-bottom: 10px;
          letter-spacing: -0.5px;
      }

      .card-text {
          font-size: 14px;
          color: #586b8a;
          line-height: 1.6;
          margin-bottom: 20px;
          flex-grow: 1;
      }

      .btn-primary {
          background-color: #3b5bdb;
          border-color: #3b5bdb;
          color: #ffffff;
          font-weight: 600;
          font-size: 14px;
          padding: 10px 20px;
          border-radius: 8px;
          transition: all 0.3s ease;
          cursor: pointer;
          text-decoration: none;
          display: inline-block;
          text-align: center;
          width: 100%;
      }

      .btn-primary:hover {
          background-color: #2d42a8;
          border-color: #2d42a8;
          transform: translateY(-2px);
          box-shadow: 0 6px 16px rgba(58, 91, 219, 0.3);
          color: #ffffff;
      }

      .btn-primary:active {
          transform: translateY(0);
      }

      /* Responsive design melhorado */
      @media (max-width: 768px) {
          .container {
              grid-template-columns: 1fr;
              padding: 40px 20px;
              gap: 20px;
          }

          .card-body {
              padding: 20px;
          }

          .card-title {
              font-size: 18px;
          }
      }
    </style>
</head>
<body>

<!-- Navbar mantida sem alterações conforme solicitado -->
<nav class="navbar navbar-expand-lg" style="background-color: #0a0d8d; margin: 0; padding: 0.5rem 1rem;">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <a class="navbar-brand" style="color:white" href="dashboard.php">AGENDA SENAI</a>
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link active" style="color:white" aria-current="page" href="logout.php">Logout</a>
      </li>
    </ul>
  </div>
</nav>

<!-- Container redesenhado com grid layout -->
<div class="container">
    <div class="card">
      <img src="../assets/imagens/profs.png" class="card-img-top" alt="professores">
      <div class="card-body">
        <h5 class="card-title">Professores</h5>
        <p class="card-text">Visualizar a tabela de professores</p>
        <a href="tabela_prof.php" class="btn btn-primary">Ver tabela</a>
      </div>
    </div>
                  
    <div class="card">
      <img src="../assets/imagens/ucc.png" class="card-img-top" alt="cursos">
      <div class="card-body">
        <h5 class="card-title">Cursos</h5>
        <p class="card-text">Visualizar a tabela de unidades curriculares</p>
        <a href="tabela_uc.php" class="btn btn-primary">Ver tabela</a>
      </div>
    </div>

    <div class="card">
      <img src="../assets/imagens/agenda.png" class="card-img-top" alt="agenda">
      <div class="card-body">
        <h5 class="card-title">Agenda</h5>
        <p class="card-text">Vizualizar a agenda dos professores</p>
        <a href="tabela_agenda.php" class="btn btn-primary">Ver tabela</a>
      </div>
    </div>
</div>

</body>
</html>
