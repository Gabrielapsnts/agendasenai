<?php
include "conexao_db.php";

// Verifica se todos os campos obrigatórios foram enviados
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
$data_inicio = $_POST['data_inicio'];
$data_fim = $_POST['data_fim'];

// Validação: data final não pode ser anterior à inicial
if ($data_fim < $data_inicio) {
    header("Location: tabela_agenda.php?id_prof=$id_prof");
    exit;
}

// Inserir o novo agendamento
$sqlInsert = "INSERT INTO agenda (id_prof, id_uc, id_comp, data_inicio, data_fim) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sqlInsert);
$stmt->bind_param("iiiss", $id_prof, $id_uc, $id_comp, $data_inicio, $data_fim);
$stmt->execute();

$stmt->close();
$conn->close();

// Recarrega a página do professor automaticamente para atualizar o calendário
header("Location: tabela_agenda.php?id_prof=$id_prof");
exit;
?>
