<?php
include "conexao_db.php";

if (
  empty($_POST['id']) || empty($_POST['id_prof']) || empty($_POST['id_uc']) ||
  empty($_POST['id_comp']) || empty($_POST['data_inicio']) || empty($_POST['data_fim'])
) {
  die("Erro: dados insuficientes.");
}

$id = intval($_POST['id']);
$id_prof = intval($_POST['id_prof']);
$id_uc = intval($_POST['id_uc']);
$id_comp = intval($_POST['id_comp']);
$data_inicio = $_POST['data_inicio'];
$data_fim = $_POST['data_fim'];
$turno = $_POST['turno'] ?? null;

$sql = "UPDATE agenda SET id_prof=?, id_uc=?, id_comp=?, data_inicio=?, data_fim=?, turno=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iiisssi", $id_prof, $id_uc, $id_comp, $data_inicio, $data_fim, $turno, $id);
if ($stmt->execute()) {
  echo "<script>alert('Evento atualizado com sucesso!'); window.location='tabela_agenda.php';</script>";
} else {
  echo "<script>alert('Erro ao atualizar o evento.'); history.back();</script>";
}
$stmt->close();
$conn->close();
?>
