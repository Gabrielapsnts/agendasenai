<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Professores</title>
   <link rel="stylesheet" href="../bootstrap/bootstrap.css">
   <script src="../bootstrap/bootstrap.js"></script>
</head>
<body>

<?php
include "conexao_db.php";

// Recebe os dados do formulário, sanitize se quiser
$nomeprof = $_POST['nomeprof'] ?? '';
$turnos = $_POST['turnos'] ?? '';
$id_uc = isset($_POST['id_uc']) ? (int)$_POST['id_uc'] : 0;
$competencias = $_POST['competencias'] ?? []; // array com ids das competências selecionadas

if ($id_uc <= 0) {
    die("Curso (id_uc) não foi selecionado corretamente.");
}

if (empty($nomeprof)) {
    die("Nome do professor é obrigatório.");
}

// Inserir professor usando prepared statement para evitar SQL Injection
$stmt = $conn->prepare("INSERT INTO professor (nomeprof, turnos) VALUES (?, ?)");
$stmt->bind_param("ss", $nomeprof, $turnos);

if ($stmt->execute()) {
    $id_prof = $stmt->insert_id;
    $stmt->close();

    // Inserir relacionamento professor-curso
    $stmtRel = $conn->prepare("INSERT INTO professor_uc (id_prof, iduc) VALUES (?, ?)");
    $stmtRel->bind_param("ii", $id_prof, $id_uc);

    if (!$stmtRel->execute()) {
        die("Erro ao inserir relacionamento professor_uc: " . $conn->error);
    }
    $stmtRel->close();

    // Inserir competências selecionadas (se houver)
    if (!empty($competencias)) {
        $stmtComp = $conn->prepare("INSERT INTO professor_competencia (id_prof, idcomp) VALUES (?, ?)");
        foreach ($competencias as $idcomp) {
            $idcomp = (int)$idcomp; // garante que é inteiro
            $stmtComp->bind_param("ii", $id_prof, $idcomp);
            if (!$stmtComp->execute()) {
                die("Erro ao inserir competências: " . $conn->error);
            }
        }
        $stmtComp->close();
    }

    // Redireciona para a página de tabela após o sucesso
    header("Location: tabela_prof.php");
    exit;

} else {
    die("Erro ao inserir professor: " . $conn->error);
}

?>

</body>
</html>
