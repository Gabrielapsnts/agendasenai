<?php
include "conexao_db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['nome_turma'];
    $iduc = $_POST['iduc'];
    $inicio = $_POST['data_inicio'] ?? null;
    $fim = $_POST['data_fim'] ?? null;
    $turno = $_POST['turno'] ?? null;

    $stmt = $conn->prepare("INSERT INTO turma (nome_turma, iduc, data_inicio, data_fim, turno) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sisss", $nome, $iduc, $inicio, $fim, $turno);
    $stmt->execute();

    header("Location: tabela_turma.php");
    exit;
}
?>
