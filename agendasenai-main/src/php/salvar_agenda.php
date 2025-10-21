<?php
include "conexao_db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_prof = $_POST['id_prof'] ?? null;
    $id_uc = $_POST['id_uc'] ?? null;
    $id_comp = $_POST['id_comp'] ?? null;
    $data_inicio = $_POST['data_inicio'] ?? null;
    $data_fim = $_POST['data_fim'] ?? null;

    if ($id_prof && $id_uc && $id_comp && $data_inicio && $data_fim) {
        $stmt = $conn->prepare("INSERT INTO agenda (id_prof, id_uc, id_comp, data_inicio, data_fim) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiss", $id_prof, $id_uc, $id_comp, $data_inicio, $data_fim);

        if ($stmt->execute()) {
            // Você pode fazer um redirect para a página do calendário com mensagem
            header("Location: tabela_prof.php?msg=sucesso");
            exit();
        } else {
            echo "Erro ao salvar agenda: " . $stmt->error;
        }
    } else {
        echo "Todos os campos são obrigatórios.";
    }
} else {
    echo "Método inválido.";
}
