<?php
include "conexao_db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_prof'])) {
    $id = intval($_POST['id_prof']);

    $stmt = $conn->prepare("DELETE FROM professor WHERE id_prof = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: tabela_prof.php"); // redireciona para a lista atualizada
        exit();
    } else {
        echo "Erro ao excluir: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Requisição inválida.";
}
?>
