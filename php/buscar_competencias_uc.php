<?php
include "conexao_db.php";

header('Content-Type: application/json; charset=utf-8');

// O parâmetro deve ser o mesmo usado no fetch() → "iduc"
$iduc = isset($_GET['iduc']) ? intval($_GET['iduc']) : 0;

$competencias = [];

if ($iduc > 0) {
    $sql = "
        SELECT c.idcomp, c.nomecomp
        FROM uc_comp ucc
        JOIN competencia c ON ucc.idcomp = c.idcomp
        WHERE ucc.iduc = ?
        ORDER BY c.nomecomp
    ";

    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $iduc);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) {
            $competencias[] = [
                'idcomp' => (int) $row['idcomp'],
                'nomecomp' => $row['nomecomp']
            ];
        }
        $stmt->close();
    }
}

echo json_encode($competencias);

?>
