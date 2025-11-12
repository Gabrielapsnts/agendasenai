<?php
include "conexao_db.php"; // ✅ MySQLi padrão

$id_uc = isset($_GET['id_uc']) ? intval($_GET['id_uc']) : 0;
$id_prof = isset($_GET['id_prof']) ? intval($_GET['id_prof']) : 0;

if ($id_uc <= 0 || $id_prof <= 0) {
    echo json_encode([]);
    exit;
}

// 🔹 Busca turmas vinculadas à UC e compatíveis com o turno do professor
$sql = "
SELECT t.id_turma, t.nome_turma, t.turno
FROM turma t
JOIN uc u ON t.iduc = u.iduc
JOIN professor_uc pu ON pu.iduc = u.iduc
JOIN professor p ON p.id_prof = pu.id_prof
WHERE u.iduc = ? AND p.id_prof = ?
ORDER BY t.nome_turma
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_uc, $id_prof);
$stmt->execute();
$res = $stmt->get_result();

$turmas = [];
while ($row = $res->fetch_assoc()) {
    $turmas[] = [
        'id_turma' => intval($row['id_turma']),
        'nome_turma' => $row['nome_turma'],
        'turno' => $row['turno']
    ];
}

header('Content-Type: application/json');
echo json_encode($turmas);
?>
