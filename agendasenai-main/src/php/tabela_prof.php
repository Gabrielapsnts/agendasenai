<?php
include "conexao_db.php";  

// Consulta professores (com busca opcional)
$searchTerm = '';
$sql = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search']) && !empty(trim($_POST['search']))) {
    $searchTerm = $conn->real_escape_string($_POST['search']);

    $sql = "
    SELECT 
        p.id_prof,
        p.nomeprof,
        p.turnos,
        GROUP_CONCAT(DISTINCT u.nomeuc SEPARATOR ', ') AS unidades,
        GROUP_CONCAT(DISTINCT c.nomecomp SEPARATOR ', ') AS competencias
    FROM professor p
    JOIN professor_uc pu ON p.id_prof = pu.id_prof
    JOIN uc u ON pu.iduc = u.iduc
    JOIN uc_comp ucc ON u.iduc = ucc.iduc
    JOIN competencia c ON ucc.idcomp = c.idcomp
    WHERE p.nomeprof LIKE '%$searchTerm%' OR u.nomeuc LIKE '%$searchTerm%' OR c.nomecomp LIKE '%$searchTerm%'
    GROUP BY p.id_prof, p.nomeprof, p.turnos
    ORDER BY p.nomeprof ASC;
    ";
} else {
   $sql = "
SELECT 
    p.id_prof,
    p.nomeprof,
    p.turnos,
    GROUP_CONCAT(DISTINCT u.nomeuc SEPARATOR ', ') AS unidades,
    GROUP_CONCAT(DISTINCT c.nomecomp SEPARATOR ', ') AS competencias
FROM professor p
JOIN professor_uc pu ON p.id_prof = pu.id_prof
JOIN uc u ON pu.iduc = u.iduc
JOIN professor_competencia pc ON p.id_prof = pc.id_prof
JOIN competencia c ON pc.idcomp = c.idcomp
GROUP BY p.id_prof, p.nomeprof, p.turnos
ORDER BY p.nomeprof ASC;
";}


$result = $conn->query($sql);
if (!$result) die("Erro na consulta: " . $conn->error);

// --- Cursos e competências para cadastro ---
$sqlCursos = "SELECT iduc, nomeuc FROM uc ORDER BY nomeuc";
$resultCursos = $conn->query($sqlCursos);

$cursos = [];
if ($resultCursos) {
    while($row = $resultCursos->fetch_assoc()) {
        $cursos[$row['iduc']] = [
            'nome' => $row['nomeuc'],
            'competencias' => []
        ];
    }
}

// Puxar competências de cada curso
$sqlCompetencias = "
SELECT uc.iduc, c.idcomp, c.nomecomp
FROM uc
JOIN uc_comp ucx ON uc.iduc = ucx.iduc
JOIN competencia c ON ucx.idcomp = c.idcomp
ORDER BY uc.nomeuc, c.nomecomp
";
$resultComp = $conn->query($sqlCompetencias);
if ($resultComp) {
    while($row = $resultComp->fetch_assoc()) {
        $cursos[$row['iduc']]['competencias'][] = [
            'idcomp' => $row['idcomp'],
            'nomecomp' => $row['nomecomp']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cadastro de Professores</title>
<link rel="stylesheet" href="../bootstrap/bootstrap.css">
<link rel="stylesheet" href="../css/tabela_prof.css">
</head>
<body>

<nav class="navbar navbar-expand-lg" style="background-color: #0a0d8d; margin: 0; padding: 0.5rem 1rem;">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <a class="navbar-brand" style="color:white" href="dashboard.php">AGENDA SENAI</a>
    <form class="d-flex" role="search" method="POST">
      <input class="form-control me-2" type="search" placeholder="Pesquisar" aria-label="Pesquisar" name="search" />
      <button type="submit" class="btn btn-outline-light">Buscar</button>
    </form>
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link active" style="color:white" href="tabela_uc.php">Cursos</a></li>
      <li class="nav-item"><a class="nav-link active" style="color:white" href="tabela_agenda.php">Agenda</a></li>
      <li class="nav-item"><a class="nav-link active" style="color:white" href="logout.php">Logout</a></li>
    </ul>
  </div>
</nav>

<div style="position: absolute; right: 50px; top: 65px; max-width: 500px;">
<form action="prof_script.php" method="POST">
  <h2>Cadastro</h2>
  <div class="form-floating mb-3">
    <input type="text" class="form-control" id="professor" name="nomeprof" required>
    <label for="professor">Professor</label>
  </div>
  <div class="form-floating mb-3">
    <input type="text" class="form-control" id="turno" name="turnos">
    <label for="turno">Turno</label>
  </div>
  <br>

  <!-- Seleção de Curso -->
  <label for="curso">Curso:</label>
  <select id="curso" class="form-select" name="id_uc" required onchange="mostrarCompetencias()">
    <option value="" selected disabled hidden>Selecione</option>
    <?php foreach ($cursos as $iduc => $curso): ?>
      <option value="<?= $iduc ?>"><?= htmlspecialchars($curso['nome']) ?></option>
    <?php endforeach; ?>
  </select>

  <br><br>
  <!-- Checkboxes de Competências -->
  <div id="competencias-container">
    <strong>Selecione um curso para ver as competências</strong>
  </div>

  <br>
  <button class="btn btn-primary" type="submit">Cadastro</button>
</form>

<!-- ✅ Tabela colocada logo após o botão -->
<br><br>
<table class="table">
  <thead>
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Turnos</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <th scope="row">1</th>
      <td>Manhã</td>
    </tr>
    <tr>
      <th scope="row">2</th>
      <td>Tarde</td>
    </tr>
    <tr>
      <th scope="row">3</th>
      <td>Noite</td>
    </tr>
  </tbody>
</table>
</div>


<!-- Tabela de professores cadastrados -->
<div id="prof-table-wrapper" style="position: absolute; left: 15px; top: 70px; width: 1000px;">
  <table class="table table-bordered" style="background: #fff;">
    <thead>
      <tr>
        <th>Professores</th>
        <th>Turno</th>
        <th>Cursos</th>
        <th>Competências</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
    <?php
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['nomeprof']) . "</td>";
        echo "<td>" . htmlspecialchars($row['turnos']) . "</td>";
        echo "<td>" . htmlspecialchars($row['unidades']) . "</td>";
        echo "<td><ul style='max-height:120px; overflow-y:auto; padding-left:20px; margin:0;'>";
        foreach (explode(',', $row["competencias"]) as $comp) {
            echo "<li>" . htmlspecialchars(trim($comp)) . "</li>";
        }
        echo "</ul></td>";
        echo "<td>
                <form method='POST' action='excluir_professor.php' onsubmit=\"return confirm('Tem certeza que deseja excluir este professor?');\">
                  <input type='hidden' name='id_prof' value='" . $row['id_prof'] . "' />
                  <button type='submit' class='btn btn-primary btn-sm'>Excluir</button>
                </form>
              </td>";
        echo "</tr>";
    }
    ?>
    </tbody>
  </table>
</div>

<script>

  
// Passando os cursos e competências do PHP para JS
const cursos = <?php echo json_encode($cursos); ?>;

function mostrarCompetencias() {
    const cursoId = document.getElementById('curso').value;
    const container = document.getElementById('competencias-container');
    container.innerHTML = '';

    if (!cursoId || !cursos[cursoId] || cursos[cursoId].competencias.length === 0) {
        container.innerHTML = '<strong>Nenhuma competência cadastrada para este curso.</strong>';
        return;
    }

    cursos[cursoId].competencias.forEach(comp => {
        const div = document.createElement('div');
        div.classList.add('form-check');

        const input = document.createElement('input');
        input.type = 'checkbox';
        input.classList.add('form-check-input');
        input.name = 'competencias[]';
        input.value = comp.idcomp;
        input.id = 'comp_' + comp.idcomp;

        const label = document.createElement('label');
        label.classList.add('form-check-label');
        label.htmlFor = 'comp_' + comp.idcomp;
        label.textContent = comp.nomecomp;

        div.appendChild(input);
        div.appendChild(label);
        container.appendChild(div);
    });
}
</script>

</body>
</html>
