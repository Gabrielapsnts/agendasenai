<?php
include "conexao_db.php";

$id_turma = $_GET['id_turma'] ?? 0;

// Buscar turma e seu curso
$sql = "
SELECT 
  t.id_turma,
  t.nome_turma,
  t.iduc,
  t.turno,
  u.nomeuc
FROM turma t
JOIN uc u ON t.iduc = u.iduc
WHERE t.id_turma = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_turma);
$stmt->execute();
$res = $stmt->get_result();
$turma = $res->fetch_assoc();

if (!$turma) {
    echo json_encode(['error' => 'Turma não encontrada']);
    exit;
}

// Buscar professores da UC
$sqlProf = "
SELECT DISTINCT p.id_prof, p.nomeprof
FROM professor p
JOIN professor_uc pu ON p.id_prof = pu.id_prof
WHERE pu.iduc = ?
ORDER BY p.nomeprof
";
$stmt = $conn->prepare($sqlProf);
$stmt->bind_param("i", $turma['iduc']);
$stmt->execute();
$res = $stmt->get_result();
$professores = $res->fetch_all(MYSQLI_ASSOC);

// Buscar competências
$sqlComp = "
SELECT c.idcomp, c.nomecomp
FROM uc_comp ucx
JOIN competencia c ON ucx.idcomp = c.idcomp
WHERE ucx.iduc = ?
ORDER BY c.nomecomp
";
$stmt = $conn->prepare($sqlComp);
$stmt->bind_param("i", $turma['iduc']);
$stmt->execute();
$res = $stmt->get_result();
$competencias = $res->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    'iduc' => $turma['iduc'],
    'turno' => $turma['turno'],
    'professores' => $professores,
    'competencias' => $competencias
]);
?>
