<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Senai </title>
  <link rel="stylesheet" href="../bootstrap/bootstrap.css">
  
  <style>
    
    .login-box {
      background: #ffffff;
      border-radius: 16px;
      box-shadow: 0 4px 24px rgba(0,0,0,0.12);
      padding: 32px 32px 24px 32px;
      max-width: 400px;
      width: 100%;
      margin: 40px auto;
    }
    .login-box label,
    .login-box h3,
    .login-box span {
      color: #1a237e;
    }
    .login-bg {
      min-height: 100vh;
      display: flex;
      align-items: center;
    }
    .login-align-right {
      flex: 0 0 420px;
      margin-right: 5vw;
      margin-left: auto;
      width: 100%;
      max-width: 420px;
    }
  </style>
  <style> 
  body { 
    background-image: url('../assets/imagens/senai.png'); 
    background-size: 65% 120%; 
    background-position: left top; 
    background-repeat: no-repeat;
    background-color: rgb(6, 18, 73);
   }
  </style>
</head>

<body>


  <div class="login-bg">
  <div class="login-box login-align-right">
      <img src="https://portaldecompras.sistemafiep.org.br/media/Sistema-Fiep-Azul.png" style="max-width: 200px; max-height: 200px; display: block; margin: 0 auto 16px auto; border-color: rgb(255, 255, 255);" class="img-thumbnail" alt="...">
      <h3 style="font-style:oblique; text-align:center; margin-bottom: 24px;">LOGIN</h3>
      <form action="dashboard.php" method="post">
        <div class="mb-3">
          <label for="exampleInputEmail1" class="form-label">Usuário</label>
          <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Digite um email válido">
        </div>
        <div class="mb-3 position-relative">
          <label for="exampleInputPassword1" class="form-label">Senha</label>
          <input type="password" class="form-control" id="exampleInputPassword1" placeholder="Digite sua senha">



          <span 
            onclick="toggleSenha()" 
            style="position: absolute; top: 38px; right: 10px; cursor: pointer; user-select: none;"
            title="Mostrar/Ocultar senha"
          >
            👁️‍🗨️
          </span>
        </div>
        <button type="submit" class="btn btn-primary w-100 mb-2">Entrar</button>
        <button type="button" class="btn btn-secondary w-100">Esqueci a senha</button>
      </form>
    </div>
  </div>

  <script>
    function toggleSenha() {
      const inputSenha = document.getElementById('exampleInputPassword1');
      if (inputSenha.type === 'password') {
        inputSenha.type = 'text';
      } else {
        inputSenha.type = 'password';
      }
    }
  </script>

</body>      