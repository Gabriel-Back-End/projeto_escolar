<?php
require_once '../conexao.php'; // Caminho para sua conexão mysqli

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Recebe os dados do formulário
    $nome_crianca = $_POST['nome_crianca'];
    $nome_responsavel = $_POST['nome_responsavel'];
    $telefone = $_POST['telefone_responsavel'];
    $id_escola = $_POST['id_escola'];
    $periodo = $_POST['periodo'];
    $serie = $_POST['serie'];
    $endereco = $_POST['endereco'];
    $valor = $_POST['valor_mensalidade'];
    $vencimento = $_POST['dia_vencimento'];

    // 2. Prepara o INSERT do Aluno (Usando Prepared Statements para segurança)
    $sql = "INSERT INTO alunos (nome_crianca, nome_responsavel, telefone_responsavel, id_escola, periodo, serie, endereco, valor_mensalidade, dia_vencimento) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssisssdi", $nome_crianca, $nome_responsavel, $telefone, $id_escola, $periodo, $serie, $endereco, $valor, $vencimento);

    if ($stmt->execute()) {
        $id_aluno = $conn->insert_id; // Pega o ID do aluno que acabou de ser criado
        $mes_atual = date('Y-m'); // Ex: 2026-04

        // 3. Já cria o primeiro registro de pagamento como 'Pendente'
        $sql_pagamento = "INSERT INTO pagamentos (id_aluno, mes_referencia, status_pg) VALUES (?, ?, 'Pendente')";
        $stmt_pg = $conn->prepare($sql_pagamento);
        $stmt_pg->bind_param("is", $id_aluno, $mes_atual);
        $stmt_pg->execute();

        // Redireciona de volta com sucesso (ou exibe mensagem)
        header("Location: ../../frontend/telas/cadastro_alunos.php?sucesso=1");
    } else {
        echo "Erro ao cadastrar: " . $conn->error;
    }
}