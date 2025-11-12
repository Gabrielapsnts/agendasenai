<?php
include "conexao_db.php";

// Verifica campos obrigatórios
if (
    !isset($_POST['id_prof']) || empty($_POST['id_prof']) ||
    !isset($_POST['id_uc']) || empty($_POST['id_uc']) ||
    !isset($_POST['id_comp']) || empty($_POST['id_comp']) ||
    !isset($_POST['data_inicio']) || empty($_POST['data_inicio']) ||
    !isset($_POST['data_fim']) || empty($_POST['data_fim'])
    
) {
    header("Location: tabela_agenda.php");
    exit;
}

$id_prof = intval($_POST['id_prof']);
$id_uc = intval($_POST['id_uc']);
$id_comp = intval($_POST['id_comp']);
$id_turma = isset($_POST['id_turma']) ? intval($_POST['id_turma']) : null;
$data_inicio = $_POST['data_inicio'];
$data_fim = $_POST['data_fim'];
$turno = $_POST['turno'] ?? null; // <--- NOVO
$id_evento = isset($_POST['id_evento']) ? intval($_POST['id_evento']) : null;
$dia_edicao = isset($_POST['dia_edicao']) ? $_POST['dia_edicao'] : null;


// Validação: data final >= inicial
if ($data_fim < $data_inicio) {
    header("Location: tabela_agenda.php?id_prof=$id_prof");
    exit;
}

if ($id_evento && $dia_edicao) {
    // --- EDIÇÃO DE UM DIA ESPECÍFICO DENTRO DE UM INTERVALO ---
    $sql = "SELECT * FROM agenda WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_evento);
    $stmt->execute();
    $res = $stmt->get_result();
    $evento = $res->fetch_assoc();
    $stmt->close();

    if ($evento) {
        $inicio = $evento['data_inicio'];
        $fim = $evento['data_fim'];

        if ($inicio == $fim) {
            // Evento de um dia → atualiza normalmente
            $sqlUp = "UPDATE agenda SET id_prof=?, id_uc=?, id_comp=?, turno=?, data_inicio=?, data_fim=? WHERE id=?";
            $stmt = $conn->prepare($sqlUp);
            $stmt->bind_param("iiisssi", $id_prof, $id_uc, $id_comp, $turno, $data_inicio, $data_fim, $id_evento);
            $stmt->execute();
        }
        else if ($dia_edicao == $inicio) {
            // Editando o primeiro dia → divide e substitui o primeiro
            $novoInicio = date('Y-m-d', strtotime($inicio . ' +1 day'));
            $sqlUp = "UPDATE agenda SET data_inicio=? WHERE id=?";
            $stmt = $conn->prepare($sqlUp);
            $stmt->bind_param("si", $novoInicio, $id_evento);
            $stmt->execute();

            $sqlNew = "INSERT INTO agenda (id_prof, id_uc, id_comp, id_turma, turno, data_inicio, data_fim)
           VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sqlNew);
$stmt->bind_param("iiiisss", $id_prof, $id_uc, $id_comp, $id_turma, $turno, $data_inicio, $data_fim);

            $stmt->execute();
        }
        else if ($dia_edicao == $fim) {
            // Editando o último dia → divide e substitui o último
            $novoFim = date('Y-m-d', strtotime($fim . ' -1 day'));
            $sqlUp = "UPDATE agenda SET data_fim=? WHERE id=?";
            $stmt = $conn->prepare($sqlUp);
            $stmt->bind_param("si", $novoFim, $id_evento);
            $stmt->execute();

            $sqlNew = "INSERT INTO agenda (id_prof, id_uc, id_comp, id_turma, turno, data_inicio, data_fim)
           VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sqlNew);
$stmt->bind_param("iiiisss", $id_prof, $id_uc, $id_comp, $id_turma, $turno, $dia_edicao, $dia_edicao);

            $stmt->execute();
        }
        else {
            // Editando um dia do meio → divide em 3 partes
            $antesFim = date('Y-m-d', strtotime($dia_edicao . ' -1 day'));
            $depoisInicio = date('Y-m-d', strtotime($dia_edicao . ' +1 day'));

            // Atualiza o evento original (parte antes)
            $sqlUp = "UPDATE agenda SET data_fim=? WHERE id=?";
            $stmt = $conn->prepare($sqlUp);
            $stmt->bind_param("si", $antesFim, $id_evento);
            $stmt->execute();

            // Cria novo evento para o trecho restante (parte depois)
            $sqlNewRest = "INSERT INTO agenda (id_prof, id_uc, id_comp, id_turma, turno, data_inicio, data_fim)
               VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sqlNewRest);
$stmt->bind_param("iiiisss", $evento['id_prof'], $evento['id_uc'], $evento['id_comp'], $id_turma, $evento['turno'], $depoisInicio, $fim);
            $stmt->execute();

            // Cria novo evento para o dia editado
            $sqlNew = "INSERT INTO agenda (id_prof, id_uc, id_comp, id_turma, turno, data_inicio, data_fim)
           VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sqlNew);
$stmt->bind_param("iiiisss", $id_prof, $id_uc, $id_comp, $id_turma, $turno, $dia_edicao, $dia_edicao);

            $stmt->execute();
        }
    }
}
else {
    // --- INSERÇÃO NORMAL ---
   $sql = "INSERT INTO agenda (id_prof, id_uc, id_comp, id_turma, turno, data_inicio, data_fim) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiisss", $id_prof, $id_uc, $id_comp, $id_turma, $turno, $data_inicio, $data_fim);

    $stmt->execute();
}

$stmt->close();
$conn->close();

// Redirecionar
header("Location: tabela_agenda.php?id_prof=$id_prof");
exit;
?>
