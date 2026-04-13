<?php
require_once '../conexao.php';

$id = $_GET['id'];
// Vamos receber o novo status via URL para ser um arquivo só para ativar/desativar
$novo_status = $_GET['status']; 

if ($id && $novo_status) {
    $sql = "UPDATE alunos SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $novo_status, $id);

    if ($stmt->execute()) {
        header("Location: ../../frontend/telas/gerenciar_alunos.php?atualizado=1");
        exit();
    }
}