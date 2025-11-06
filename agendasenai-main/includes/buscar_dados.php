<?php
include "conexao_db.php";

// 🔹 Buscar professores
$sqlAllProf = "SELECT id_prof, nomeprof FROM professor ORDER BY nomeprof ASC";
$resAllProf = $conn->query($sqlAllProf);

$idsByName = [];
$profNameById = [];

if ($resAllProf) {
    while ($r = $resAllProf->fetch_assoc()) {
        $id = $r['id_prof'];
        $name = trim($r['nomeprof']);
        $profNameById[$id] = $name;
        if (!isset($idsByName[$name])) $idsByName[$name] = [];
        $idsByName[$name][] = $id;
    }
}

// 🔹 Criar lista de professores únicos
$professores = [];
foreach ($idsByName as $name => $ids) {
    $professores[] = [
        'id_prof' => intval($ids[0]),
        'nomeprof' => $name,
        'all_ids' => $ids
    ];
}

// 🔹 Buscar cursos, competências e turno
$sqlCursos = "
    SELECT 
        p.id_prof, 
        p.turnos, 
        uc.iduc, 
        uc.nomeuc, 
        c.idcomp, 
        c.nomecomp
    FROM professor p
    JOIN professor_uc puc ON p.id_prof = puc.id_prof
    JOIN uc ON puc.iduc = uc.iduc
    JOIN uc_comp ucc ON uc.iduc = ucc.iduc
    JOIN competencia c ON ucc.idcomp = c.idcomp
    ORDER BY p.id_prof, uc.nomeuc, c.nomecomp
";
$resCursos = $conn->query($sqlCursos);

$cursosPorProfessorById = [];

if ($resCursos) {
    while ($row = $resCursos->fetch_assoc()) {
        $idProf = $row['id_prof'];
        $idUc = $row['iduc'];

        if (!isset($cursosPorProfessorById[$idProf])) $cursosPorProfessorById[$idProf] = [];
        if (!isset($cursosPorProfessorById[$idProf][$idUc])) {
            $cursosPorProfessorById[$idProf][$idUc] = [
                'nomeuc' => $row['nomeuc'],
                'turno' => $row['turnos'], // ✅ Adicionado o turno
                'competencias' => []
            ];
        }

        $exists = false;
        foreach ($cursosPorProfessorById[$idProf][$idUc]['competencias'] as $c) {
            if ($c['idcomp'] == $row['idcomp']) {
                $exists = true;
                break;
            }
        }

        if (!$exists) {
            $cursosPorProfessorById[$idProf][$idUc]['competencias'][] = [
                'idcomp' => $row['idcomp'],
                'nomecomp' => $row['nomecomp']
            ];
        }
    }
}

// 🔹 Agregar cursos por professor único
$cursosPorProfessor = [];
foreach ($professores as $prof) {
    $primary = $prof['id_prof'];
    $allIds = $prof['all_ids'];
    $merged = [];

    foreach ($allIds as $pid) {
        if (!isset($cursosPorProfessorById[$pid])) continue;

        foreach ($cursosPorProfessorById[$pid] as $idUc => $ucData) {
            if (!isset($merged[$idUc])) {
                $merged[$idUc] = [
                    'nomeuc' => $ucData['nomeuc'],
                    'turno' => $ucData['turno'], // ✅ Mantém o turno por UC
                    'competencias' => []
                ];
            }

            foreach ($ucData['competencias'] as $comp) {
                $found = false;
                foreach ($merged[$idUc]['competencias'] as $existing) {
                    if ($existing['idcomp'] == $comp['idcomp']) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) $merged[$idUc]['competencias'][] = $comp;
            }
        }
    }
    $cursosPorProfessor[$primary] = $merged;
}

// 🔹 Verificar professor selecionado
$selectedProf = isset($_GET['id_prof']) ? intval($_GET['id_prof']) : null;

// 🔹 Buscar eventos (inclui turno)
$eventos = [];
if ($selectedProf) {
    $sqlEv = "
        SELECT 
            a.id, a.id_prof, a.id_uc, a.id_comp, a.turno,
            a.data_inicio, a.data_fim, 
            uc.nomeuc, c.nomecomp
        FROM agenda a
        JOIN uc ON a.id_uc = uc.iduc
        JOIN competencia c ON a.id_comp = c.idcomp
        WHERE a.id_prof = ?
    ";

    $stmt = $conn->prepare($sqlEv);
    if ($stmt) {
        $stmt->bind_param("i", $selectedProf);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($r = $res->fetch_assoc()) {
            $eventos[] = [
                'id' => $r['id'],
                'id_prof' => $r['id_prof'],
                'id_uc' => $r['id_uc'],
                'id_comp' => $r['id_comp'],
                'turno' => $r['turno'], // ✅ Inclui turno no evento
                'data_inicio' => $r['data_inicio'],
                'data_fim' => $r['data_fim'],
                'nomeuc' => $r['nomeuc'],
                'nomecomp' => $r['nomecomp']
            ];
        }
        $stmt->close();
    }
}
?>
