<?php
include "conexao_db.php";

if (!isset($_POST['id']) || empty($_POST['id']) || !isset($_POST['dia']) || empty($_POST['dia'])) {
  echo "Erro: dados insuficientes.";
  exit;
}

$id = intval($_POST['id']);
$dia = $_POST['dia']; // dia específico clicado

// Busca o evento completo
$sql = "SELECT * FROM agenda WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows == 0) {
  echo "Evento não encontrado.";
  exit;
}

$evento = $res->fetch_assoc();
$stmt->close();

$data_inicio = $evento['data_inicio'];
$data_fim = $evento['data_fim'];

if ($data_inicio == $data_fim) {
  // Evento de um dia → exclui tudo
  $sqlDel = "DELETE FROM agenda WHERE id = ?";
  $stmt = $conn->prepare($sqlDel);
  $stmt->bind_param("i", $id);
  $stmt->execute();
  echo "Evento excluído com sucesso.";
}
else if ($dia == $data_inicio) {
  // Excluiu o primeiro dia → move início +1
  $novoInicio = date('Y-m-d', strtotime($data_inicio . ' +1 day'));
  $sqlUp = "UPDATE agenda SET data_inicio=? WHERE id=?";
  $stmt = $conn->prepare($sqlUp);
  $stmt->bind_param("si", $novoInicio, $id);
  $stmt->execute();
  echo "Dia removido do início do evento.";
}
else if ($dia == $data_fim) {
  // Excluiu o último dia → move fim -1
  $novoFim = date('Y-m-d', strtotime($data_fim . ' -1 day'));
  $sqlUp = "UPDATE agenda SET data_fim=? WHERE id=?";
  $stmt = $conn->prepare($sqlUp);
  $stmt->bind_param("si", $novoFim, $id);
  $stmt->execute();
  echo "Dia removido do final do evento.";
}
else {
  // Excluiu um dia no meio → divide em 2 eventos
  $antesFim = date('Y-m-d', strtotime($dia . ' -1 day'));
  $depoisInicio = date('Y-m-d', strtotime($dia . ' +1 day'));

  // Atualiza evento original para encerrar antes
  $sqlUp = "UPDATE agenda SET data_fim=? WHERE id=?";
  $stmt = $conn->prepare($sqlUp);
  $stmt->bind_param("si", $antesFim, $id);
  $stmt->execute();

  // Cria novo evento para o trecho restante
  $sqlNew = "INSERT INTO agenda (id_prof, id_uc, id_comp, data_inicio, data_fim)
             VALUES (?, ?, ?, ?, ?)";
  $stmt = $conn->prepare($sqlNew);
  $stmt->bind_param("iiiss", $evento['id_prof'], $evento['id_uc'], $evento['id_comp'], $depoisInicio, $data_fim);
  $stmt->execute();

  echo "Evento dividido com sucesso (dia removido do meio).";
}

$stmt->close();
$conn->close();
?>
