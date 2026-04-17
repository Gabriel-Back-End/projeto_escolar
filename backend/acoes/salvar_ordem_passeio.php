<?php
require_once '../conexao.php';

// Recebe os dados do formulário
$id_p = $_POST['id_participacao'] ?? null;
$id_e = $_POST['id_evento'] ?? null;
$ordem = $_POST['ordem'] ?? 0;

if ($id_p && $id_e) {
    // Prepara a atualização para evitar erros de SQL
    $stmt = $conn->prepare("UPDATE evento_participantes SET ordem_passeio = ? WHERE id = ?");
    $stmt->bind_param("ii", $ordem, $id_p);

    if ($stmt->execute()) {
        // Sucesso: Volta para os detalhes do evento
        header("Location: ../../frontend/telas/detalhes_evento.php?id=$id_e&ordem_salva=1");
    } else {
        // Erro: Caso algo dê errado no banco
        echo "Erro ao atualizar ordem: " . $conn->error;
    }
} else {
    // Se não vierem os IDs, volta para a lista geral
    header("Location: ../../frontend/telas/eventos.php");
}
exit();