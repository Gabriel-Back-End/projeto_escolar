<?php
// 1. Inclui a tua conexão (ajusta o caminho se necessário)
require_once '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 2. Captura os dados do formulário/modal
    $id_aluno = $_POST['id_aluno'];
    $metodo = $_POST['metodo'];
    $mes_atual = date('Y-m'); // Formato: 2026-04
    $data_hora_agora = date('Y-m-d H:i:s');

    // 3. Prepara o UPDATE
    // Queremos atualizar o status para 'Pago', definir o método e a data da confirmação
    $sql = "UPDATE pagamentos 
            SET status_pg = 'Pago', 
                metodo_pg = ?, 
                data_confirmacao = ? 
            WHERE id_aluno = ? AND mes_referencia = ?";

    $stmt = $conn->prepare($sql);
    
    // s = string, i = integer
    // ordem: metodo(s), data_confirmacao(s), id_aluno(i), mes_referencia(s)
    $stmt->bind_param("ssis", $metodo, $data_hora_agora, $id_aluno, $mes_atual);

    if ($stmt->execute()) {
        // 4. Se deu certo, volta para a tela do financeiro com um aviso de sucesso
        header("Location: ../../frontend/telas/financeiro.php?pago=1");
        exit();
    } else {
        // Se houver erro no banco
        echo "Erro ao processar pagamento: " . $conn->error;
    }
} else {
    // Se tentarem aceder ao ficheiro diretamente sem ser via POST
    header("Location: ../../frontend/telas/financeiro.php");
    exit();
}
?>