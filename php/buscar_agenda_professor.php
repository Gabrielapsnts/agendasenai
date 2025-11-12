<?php
include "conexao_db.php"; // ✅ unifica o tipo de conexão

if (!isset($_GET['id_prof'])) {
    echo json_encode(['error' => 'Professor não informado']);
    exit;
}

$id_prof = intval($_GET['id_prof']);

$sql = "SELECT 
            uc.iduc, uc.nomeuc, 
            c.idcomp, c.nomecomp, 
            a.data_inicio, a.data_fim
        FROM agenda a
        JOIN uc ON a.id_uc = uc.iduc
        JOIN competencia c ON a.id_comp = c.idcomp
        WHERE a.id_prof = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_prof);
$stmt->execute();
$res = $stmt->get_result();

$cursos = [];

while ($row = $res->fetch_assoc()) {
    $cursoId = $row['iduc'];
    if (!isset($cursos[$cursoId])) {
        $cursos[$cursoId] = [
            'nomeuc' => $row['nomeuc'],
            'competencias' => []
        ];
    }
    $cursos[$cursoId]['competencias'][] = [
        'idcomp' => $row['idcomp'],
        'nomecomp' => $row['nomecomp'],
        'data_inicio' => $row['data_inicio'],
        'data_fim' => $row['data_fim']
    ];
}

header('Content-Type: application/json');
echo json_encode(['cursos' => $cursos]);
?>
