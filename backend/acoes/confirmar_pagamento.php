<?php
require_once '../conexao.php';

// Agora recebemos o ID da MENSALIDADE, não apenas do aluno
if (isset($_POST['id_mensalidade']) && isset($_POST['metodo'])) {
    
    $id_mensalidade = intval($_POST['id_mensalidade']);
    $metodo = $_POST['metodo'];
    $data_hoje = date('Y-m-d H:i:s');

    // ATUALIZAMOS a mensalidade que estava pendente
    $sql = "UPDATE mensalidades 
                SET status = 'Pago', 
                    metodo_pagamento = ?, 
                    data_pagamento = ? 
                WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $metodo, $data_hoje, $id_mensalidade);

    if ($stmt->execute()) {
        // Sucesso! Volta para o financeiro
        header("Location: ../../frontend/telas/financeiro.php?sucesso=pago");
    } else {
        echo "Erro ao confirmar: " . $conn->error;
    }
} else {
    echo "Dados insuficientes para processar o pagamento.";
}