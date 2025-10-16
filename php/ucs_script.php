<?php
include "conexao_db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: tabela_uc.php");
    exit();
}

$nomeuc = trim($_POST['nomeuc'] ?? '');
$comp = trim($_POST['comp'] ?? '');
$cargah = intval($_POST['cargah'] ?? 0);

if ($nomeuc === "" || $comp === "" || $cargah <= 0) {
    die("Preencha todos os campos corretamente.");
}

// 1) UC
$sql_uc = "SELECT iduc FROM uc WHERE nomeuc = ?";
$stmt_uc = $conn->prepare($sql_uc);
$stmt_uc->bind_param("s", $nomeuc);
$stmt_uc->execute();
$res_uc = $stmt_uc->get_result();

if ($res_uc && $res_uc->num_rows > 0) {
    $iduc = $res_uc->fetch_assoc()['iduc'];
} else {
    $sql_insert_uc = "INSERT INTO uc (nomeuc) VALUES (?)";
    $stmt_insert_uc = $conn->prepare($sql_insert_uc);
    $stmt_insert_uc->bind_param("s", $nomeuc);
    $stmt_insert_uc->execute();
    $iduc = $stmt_insert_uc->insert_id;
}

// 2) competencia
$sql_comp = "SELECT idcomp FROM competencia WHERE nomecomp = ?";
$stmt_comp = $conn->prepare($sql_comp);
$stmt_comp->bind_param("s", $comp);
$stmt_comp->execute();
$res_comp = $stmt_comp->get_result();

if ($res_comp && $res_comp->num_rows > 0) {
    $idcomp = $res_comp->fetch_assoc()['idcomp'];
    $sql_update_carga = "UPDATE competencia SET cargah = ? WHERE idcomp = ?";
    $stmt_up = $conn->prepare($sql_update_carga);
    $stmt_up->bind_param("ii", $cargah, $idcomp);
    $stmt_up->execute();
} else {
    $sql_insert_comp = "INSERT INTO competencia (nomecomp, cargah) VALUES (?, ?)";
    $stmt_insert_comp = $conn->prepare($sql_insert_comp);
    $stmt_insert_comp->bind_param("si", $comp, $cargah);
    $stmt_insert_comp->execute();
    $idcomp = $stmt_insert_comp->insert_id;
}

// 3) link
$sql_check = "SELECT 1 FROM uc_comp WHERE iduc = ? AND idcomp = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $iduc, $idcomp);
$stmt_check->execute();
$res_check = $stmt_check->get_result();

if (!$res_check || $res_check->num_rows == 0) {
    $sql_link = "INSERT INTO uc_comp (iduc, idcomp) VALUES (?, ?)";
    $stmt_link = $conn->prepare($sql_link);
    $stmt_link->bind_param("ii", $iduc, $idcomp);
    $stmt_link->execute();
}

header("Location: tabela_uc.php");
exit();
