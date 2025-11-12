<?php
include "conexao_db.php";  

// --- BUSCA ---
$searchTerm = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['search']) && !empty(trim($_POST['search']))) {
    $searchTerm = $conn->real_escape_string($_POST['search']);
    $sql = "
        SELECT 
            t.id_turma,
            t.nome_turma,
            t.data_inicio,
            t.data_fim,
            t.turno,
            u.nomeuc
        FROM turma t
        JOIN uc u ON t.iduc = u.iduc
        WHERE t.nome_turma LIKE '%$searchTerm%' OR u.nomeuc LIKE '%$searchTerm%'
        ORDER BY t.nome_turma ASC;
    ";
} else {
    $sql = "
        SELECT 
            t.id_turma,
            t.nome_turma,
            t.data_inicio,
            t.data_fim,
            t.turno,
            u.nomeuc
        FROM turma t
        JOIN uc u ON t.iduc = u.iduc
        ORDER BY t.nome_turma ASC;
    ";
}

$result = $conn->query($sql);
if (!$result) die("Erro na consulta: " . $conn->error);

// --- CURSOS para o cadastro ---
$sqlCursos = "SELECT iduc, nomeuc FROM uc ORDER BY nomeuc";
$resultCursos = $conn->query($sqlCursos);

$cursos = [];
if ($resultCursos) {
    while($row = $resultCursos->fetch_assoc()) {
        $cursos[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cadastro de Turmas</title>
<link rel="stylesheet" href="../bootstrap/bootstrap.css">
<link rel="stylesheet" href="../css/tabela_prof.css"> <!-- Reutiliza estilo -->
</head>
<body>

<nav class="navbar navbar-expand-lg" style="background-color: #0a0d8d; margin: 0; padding: 0.5rem 1rem;">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <a class="navbar-brand" style="color:white" href="dashboard.php">AGENDA SENAI</a>
    <form class="d-flex" role="search" method="POST">
      <input class="form-control me-2" type="search" placeholder="Pesquisar turma ou curso" aria-label="Pesquisar" name="search" />
      <button type="submit" class="btn btn-outline-light">Buscar</button>
    </form>
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link active" style="color:white" href="tabela_prof.php">Professores</a></li>
      <li class="nav-item"><a class="nav-link active" style="color:white" href="tabela_uc.php">Cursos</a></li>
      <li class="nav-item"><a class="nav-link active" style="color:white" href="tabela_agenda.php">Agenda</a></li>
      <li class="nav-item"><a class="nav-link active" style="color:white" href="logout.php">Logout</a></li>
    </ul>
  </div>
</nav>

<!-- 📋 FORMULÁRIO DE CADASTRO -->
<div style="position: absolute; right: 50px; top: 65px; max-width: 500px;">
<form action="turma_script.php" method="POST">
  <h2>Cadastro de Turma</h2>

  <div class="form-floating mb-3">
    <input type="text" class="form-control" id="nome_turma" name="nome_turma" required>
    <label for="nome_turma">Nome da Turma</label>
  </div>

  <!-- Curso (UC) -->
  <label for="curso">Curso (UC):</label>
  <select id="curso" class="form-select" name="iduc" required>
    <option value="" selected disabled hidden>Selecione o curso</option>
    <?php foreach ($cursos as $curso): ?>
      <option value="<?= $curso['iduc'] ?>"><?= htmlspecialchars($curso['nomeuc']) ?></option>
    <?php endforeach; ?>
  </select>

  <br>
  <div class="form-floating mb-3">
    <input type="date" class="form-control" id="data_inicio" name="data_inicio">
    <label for="data_inicio">Data de Início</label>
  </div>

  <div class="form-floating mb-3">
    <input type="date" class="form-control" id="data_fim" name="data_fim">
    <label for="data_fim">Data de Término</label>
  </div>

  <div class="form-floating mb-3">
    <select class="form-select" id="turno" name="turno">
      <option value="">Selecione o turno</option>
      <option value="1">Manhã</option>
      <option value="2">Tarde</option>
      <option value="3">Noite</option>
    </select>
    <label for="turno">Turno</label>
  </div>

  <button class="btn btn-primary" type="submit">Cadastrar</button>
</form>
</div>

<!-- 📄 TABELA DE TURMAS -->
<div id="turma-table-wrapper" style="position: absolute; left: 15px; top: 70px; width: 1000px;">
  <table class="table table-bordered" style="background: #fff;">
    <thead>
      <tr>
        <th>Nome da Turma</th>
        <th>Curso (UC)</th>
        <th>Data Início</th>
        <th>Data Fim</th>
        <th>Turno</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
    <?php
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['nome_turma']) . "</td>";
        echo "<td>" . htmlspecialchars($row['nomeuc']) . "</td>";
        echo "<td>" . ($row['data_inicio'] ? htmlspecialchars($row['data_inicio']) : '-') . "</td>";
        echo "<td>" . ($row['data_fim'] ? htmlspecialchars($row['data_fim']) : '-') . "</td>";
        echo "<td>" . htmlspecialchars($row['turno'] ?: '-') . "</td>";
        echo "<td>
                <form method='POST' action='excluir_turma.php' onsubmit=\"return confirm('Tem certeza que deseja excluir esta turma?');\">
                  <input type='hidden' name='id_turma' value='" . $row['id_turma'] . "' />
                  <button type='submit' class='btn btn-primary btn-sm'>Excluir</button>
                </form>
              </td>";
        echo "</tr>";
    }
    ?>
    </tbody>
  </table>
</div>

</body>
</html>
