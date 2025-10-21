<?php
include "conexao_db.php";

if (isset($_POST['iduc'])) {
    $iduc = intval($_POST['iduc']); // segurança básica

    $sql = "DELETE FROM uc WHERE iduc = $iduc";

    if ($conn->query($sql) === TRUE) {
        header("Location: tabela_uc.php"); // ou o nome da página com a tabela
        exit;
    } else {
        echo "Erro ao excluir UC: " . $conn->error;
    }
} else {
    echo "ID da UC não informado.";
}
?>
