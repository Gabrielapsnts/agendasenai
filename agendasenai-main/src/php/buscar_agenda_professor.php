<?php
include "conexao_db.php";

if (!isset($_GET['id_prof'])) {
    echo json_encode(['error' => 'Professor não informado']);
    exit;
}

$id_prof = intval($_GET['id_prof']);

$sql = "SELECT uc.iduc, uc.nomeuc, c.idcomp, c.nomecomp, a.data_inicio, a.data_fim
        FROM agenda a
        JOIN uc ON a.iduc = uc.iduc
        JOIN competencia c ON a.idcomp = c.idcomp
        WHERE a.id_prof = $id_prof";

$result = $conn->query($sql);

$cursos = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
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
            'data_fim' => $row['data_fim'],
        ];
    }
}

echo json_encode(['cursos' => $cursos]);
