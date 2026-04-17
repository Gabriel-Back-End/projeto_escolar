<?php
require_once '../conexao.php';

// Pegamos o ID da participação e o ID do evento para poder voltar para a página certa
$id_p = $_GET['id_participacao'] ?? '';
$id_e = $_GET['id_evento'] ?? '';

if ($id_p && $id_e) {
    // Só por segurança: Deletar apenas se existir o ID
    $sql = "DELETE FROM evento_participantes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_p);
    
    if ($stmt->execute()) {
        header("Location: ../../frontend/telas/detalhes_evento.php?id=$id_e&removido=1");
    } else {
        echo "Erro ao remover: " . $conn->error;
    }
} else {
    header("Location: ../../frontend/telas/eventos.php");
}