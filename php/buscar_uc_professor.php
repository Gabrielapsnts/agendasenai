<?php
include "conexao_db.php";

$id_prof = isset($_GET['id_prof']) ? intval($_GET['id_prof']) : 0;

$sql = "
SELECT u.iduc, u.nomeuc
FROM professor_uc pu
JOIN uc u ON pu.iduc = u.iduc
WHERE pu.id_prof = ?
ORDER BY u.nomeuc
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_prof);
$stmt->execute();
$res = $stmt->get_result();

$ucs = [];
while ($row = $res->fetch_assoc()) {
  $ucs[] = $row;
}
header('Content-Type: application/json');
echo json_encode($ucs);
?>
