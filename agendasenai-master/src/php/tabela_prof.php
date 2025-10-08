<!-- A tabela precisa ter os seguintes itens:
 Nome Professor;
 Turno;
 Competencias a serem atendidas na UC;
 UC que atende.

 Outras funções a serem adicionada futuramente -->
 
<?php
include "conexao_db.php";  

$searchTerm = '';
$sql = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search']) && !empty(trim($_POST['search']))) {
    $searchTerm = $conn->real_escape_string(trim($_POST['search']));
    
    // Alterado: busca por nome do professor OU pelo nome da UC (curso)
    $sql = "SELECT id_prof, nomeprof, turnos, UCs, competencias 
            FROM professor 
            WHERE nomeprof LIKE '%$searchTerm%' 
               OR UCs LIKE '%$searchTerm%'
               OR competencias LIKE '%$searchTerm%'";
} else {
    $sql = "SELECT id_prof, nomeprof, turnos, UCs, competencias FROM professor";
}

$result = $conn->query($sql);

// Se a consulta falhar, mostrar o erro
if (!$result) {
    die("Erro na consulta: " . $conn->error);
}
?>
 
 <!DOCTYPE html>
 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="stylesheet" href="../bootstrap/bootstrap.css" >
    <link rel="stylesheet" href="../css/tabela_prof.css" >
 </head>
 <body>
<!-- aqui começa a navbar-->
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
       <a class="nav-link active"style="color:white" aria-current="page" href="tabela_uc.php">Cursos</a>
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
    <!-- Seleção de Curso -->
<label for="curso">Curso:</label>
<select id="curso" class="form-select" name="UCs" required>
  <option value="" selected disabled hidden>Selecione</option>
  <option value="Mecânica">Mecânica</option>
  <option value="Mecatrônica">Mecatrônica</option>
  <option value="Desenvolvimento de Sistemas">Desenvolvimento de Sistemas</option>
  <option value="Eletromecânica">Eletromecânica</option>
  <option value="Automação">Automação</option>
  <option value="Administração">Administração</option>
</select>

<br><br>

<!-- Checkboxes de Competências -->
<div id="competencias-container">
  <strong>Selecione um curso para ver as competências</strong>
</div>

<script>
  const competenciasPorCurso = {
    "Mecânica": ["Leitura de Projetos", "Usinagem", "Metrologia"],
    "Mecatrônica": ["Eletrônica Básica", "Sistemas Embarcados", "Automação"],
    "Desenvolvimento de Sistemas": ["Internet das Coisas", "Programa de Aplicativos", "Banco de Dados", "Desenvolvimento de Sistemas", "Modelagem de Sistemas", "Teste de Sistemas"],
    "Eletromecânica": ["Circuitos Elétricos", "Comandos Elétricos"],
    "Automação": ["Sensores", "Atuadores"],
    "Administração": ["Gestão Orgânica", "Marketing", "Contabilidade"]
  };

  const cursoSelect = document.getElementById('curso');
  const competenciasContainer = document.getElementById('competencias-container');

  cursoSelect.addEventListener('change', function () {
    const cursoSelecionado = this.value;
    const competencias = competenciasPorCurso[cursoSelecionado] || [];

    // Limpa competências anteriores
    competenciasContainer.innerHTML = '';

    if (competencias.length === 0) {
      competenciasContainer.innerHTML = '<strong>Nenhuma competência disponível.</strong>';
      return;
    }

    // Cria checkboxes
    competencias.forEach((comp, index) => {
      const checkboxId = `comp${index}`;

      const label = document.createElement('label');
      label.setAttribute('for', checkboxId);
      label.style.display = 'block'; // Coloca cada checkbox em uma linha

      const checkbox = document.createElement('input');
      checkbox.type = 'checkbox';
      checkbox.name = 'competencias[]';
      checkbox.value = comp;
      checkbox.id = checkboxId;

      label.appendChild(checkbox);
      label.appendChild(document.createTextNode(' ' + comp));

      competenciasContainer.appendChild(label);
    });
  });
</script>



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
    <th>Cursos</th>
    <th>Competências</th>
    <th>Ações</th> <!-- Nova coluna -->
  
  </tr>
</thead>

    <tbody>

      <!-- pega todos os professores do banco e cria as linhas da tabela na página -->
      <?php
// Garante que $sql foi definido antes deste ponto
$result = $conn->query($sql);

// Verifica se a consulta foi bem-sucedida
if ($result === false) {
    die("Erro na consulta: " . $conn->error);
}

// Verifica se retornou resultados
while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row["nomeprof"]) . "</td>";
    echo "<td>" . htmlspecialchars($row["turnos"]) . "</td>";
    echo "<td>" . htmlspecialchars($row["UCs"]) . "</td>";

    $competencias = explode(',', $row["competencias"]);
    echo "<td>
            <ul style='max-height: 120px; overflow-y: auto; padding-left: 20px; margin: 0;'>";
    foreach ($competencias as $comp) {
        echo "<li>" . htmlspecialchars(trim($comp)) . "</li>";
    }
    echo "</ul></td>";

    // Coluna de Ações com botão de excluir
    echo "<td>
            <form method='POST' action='excluir_professor.php' onsubmit=\"return confirm('Tem certeza que deseja excluir este professor?');\">
              <input type='hidden' name='id_prof' value='" . $row['id_prof'] . "' />
              <button type='submit' class='btn btn-primary btn-sm'>Excluir</button>
            </form>
          </td>";

    echo "</tr>";
}

?>

      <!-- Linhas serão inseridas aqui -->
    </tbody>
  </table>
</div>
</div>
<table class="table"
   style="position: fixed; bottom: -5px; right: 60px; width: 200px; font-size: 14px; ">
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
