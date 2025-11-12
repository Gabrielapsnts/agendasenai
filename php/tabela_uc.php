<?php
include "conexao_db.php";  

// --- Consulta para exibir UCs e suas competências ---
$sql = "
SELECT 
    u.iduc,
    u.nomeuc,
    GROUP_CONCAT(CONCAT(c.nomecomp, ' (', c.cargah, 'h)') SEPARATOR '||') AS competencias
FROM uc u
LEFT JOIN uc_comp ucx ON u.iduc = ucx.iduc
LEFT JOIN competencia c ON ucx.idcomp = c.idcomp
GROUP BY u.iduc, u.nomeuc
ORDER BY u.nomeuc ASC;
";
$result = $conn->query($sql);

// --- Cadastro de nova UC e competência ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nomeuc = trim($_POST['nomeuc']);
    $comp = trim($_POST['comp']);
    $cargah = intval($_POST['cargah']);

    if ($nomeuc === "" || $comp === "" || $cargah <= 0) {
        die("Preencha todos os campos corretamente.");
    }

    // Verifica se UC já existe
    $sql_uc = "SELECT iduc FROM uc WHERE nomeuc = ?";
    $stmt_uc = $conn->prepare($sql_uc);
    $stmt_uc->bind_param("s", $nomeuc);
    $stmt_uc->execute();
    $result_uc = $stmt_uc->get_result();

    if ($result_uc->num_rows > 0) {
        $iduc = $result_uc->fetch_assoc()['iduc'];
    } else {
        $sql_insert_uc = "INSERT INTO uc (nomeuc) VALUES (?)";
        $stmt_insert_uc = $conn->prepare($sql_insert_uc);
        $stmt_insert_uc->bind_param("s", $nomeuc);
        $stmt_insert_uc->execute();
        $iduc = $stmt_insert_uc->insert_id;
    }

    // Insere SEM verificar nome igual — cada competência tem sua carga horária
    $sql_insert_comp = "INSERT INTO competencia (nomecomp, cargah) VALUES (?, ?)";
    $stmt_insert_comp = $conn->prepare($sql_insert_comp);
    $stmt_insert_comp->bind_param("si", $comp, $cargah);
    $stmt_insert_comp->execute();
    $idcomp = $stmt_insert_comp->insert_id;

    // Cria vínculo
    $sql_link = "INSERT INTO uc_comp (iduc, idcomp) VALUES (?, ?)";
    $stmt_link = $conn->prepare($sql_link);
    $stmt_link->bind_param("ii", $iduc, $idcomp);
    $stmt_link->execute();

    header("Location: tabela_uc.php");
    exit;
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

<nav class="navbar navbar-expand-lg" style="background-color: #0a0d8d; margin: 0; padding: 0.5rem 1rem;">
  <div class="container-fluid d-flex justify-content-between align-items-center">
    <a class="navbar-brand" style="color:white" href="dashboard.php">AGENDA SENAI</a>
    <ul class="navbar-nav">
      <li class="nav-item"><a class="nav-link" style="color:white" href="tabela_prof.php">Professores</a></li>
      <li class="nav-item"><a class="nav-link" style="color:white" href="tabela_agenda.php">Agenda</a></li>
       <li class="nav-item"><a class="nav-link active" style="color:white" href="tabela_turma.php">Turma</a></li>
      <li class="nav-item"><a class="nav-link" style="color:white" href="logout.php">Logout</a></li>
    </ul>
  </div>
</nav>

<div style="position: absolute; right: 50px; top: 65px;">
  <form action="tabela_uc.php" method="POST">
    <h2>Cadastro de UC</h2> 
    <div class="form-floating mb-3">
      <input type="text" class="form-control" id="nomeuc" name="nomeuc" required>
      <label for="nomeuc">Nome da UC</label>
    </div>
    <div class="form-floating mb-3">
      <input type="text" class="form-control" id="comp" name="comp" required>
      <label for="comp">Competência</label>
    </div>
    <div class="form-floating mb-3">
      <input type="number" class="form-control" id="cargah" name="cargah" min="1" required>
      <label for="cargah">Carga Horária (h)</label>
    </div>
    <button class="btn btn-primary" type="submit">Cadastrar</button>
  </form>
</div>

<div style="position: absolute; left: 15px; top: 70px; width: 1000px;">
  <table class="table table-bordered" style="background: #fff;">
    <thead>
      <tr>
        <th>UC</th>
        <th>Competências</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if ($result && $result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo "<tr>";
              echo "<td>" . htmlspecialchars($row['nomeuc']) . "</td>";

              // Transformando a string concatenada em lista <ul>
              if (!empty($row['competencias'])) {
                  $comps = explode('||', $row['competencias']);
                  echo "<td><ul>";
                  foreach ($comps as $c) {
                      echo "<li>" . htmlspecialchars($c) . "</li>";
                  }
                  echo "</ul></td>";
              } else {
                  echo "<td>Nenhuma</td>";
              }

              echo "<td>
                      <form method='POST' action='excluir_uc.php'>
                        <input type='hidden' name='iduc' value='{$row['iduc']}'>
                        <button class='btn btn-primary btn-sm'>Excluir</button>
                      </form>
                    </td>";
              echo "</tr>";
          }
      } else {
          echo "<tr><td colspan='3'>Nenhuma UC cadastrada</td></tr>";
      }
      ?>
    </tbody>
  </table>
</div>

</body>
</html>
