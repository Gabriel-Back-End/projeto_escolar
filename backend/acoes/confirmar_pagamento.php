<?php
// backend/acoes/confirmar_pagamento.php
require_once '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recebe o array de IDs selecionados no modal e o método de pagamento
    $ids_mensalidades = $_POST['ids_mensalidades'] ?? [];
    $metodo = $_POST['metodo'] ?? '';

    if (!empty($ids_mensalidades) && !empty($metodo)) {
        // Executa a baixa para cada um dos meses marcados pelo Tio
        foreach ($ids_mensalidades as $id) {
            $id_limpo = intval($id);
            $sql = "UPDATE mensalidades 
                    SET status = 'Pago', metodo_pagamento = '$metodo', data_pagamento = NOW() 
                    WHERE id = $id_limpo";
            $conn->query($sql);
        }
        
        // Retorna para a página financeira atualizada
        header("Location: ../../frontend/telas/financeiro.php?sucesso=1");
        exit;
    }
}

header("Location: ../../frontend/telas/financeiro.php?erro=1");
exit;