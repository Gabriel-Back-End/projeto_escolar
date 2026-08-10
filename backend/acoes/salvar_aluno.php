<?php
require_once '../conexao.php'; // Caminho para sua conexão mysqli

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Recebe os dados do formulário
    $nome_crianca = $_POST['nome_crianca'] ?? '';
    $nome_responsavel = $_POST['nome_responsavel'] ?? '';
    $telefone = $_POST['telefone_responsavel'] ?? '';
    $id_escola  = $_POST['id_escola'] ?? 0;
    $periodo = $_POST['periodo'] ?? '';
    $serie = $_POST['serie'] ?? '';
    $endereco = $_POST['endereco'] ?? '';
    $valor = !empty($_POST['valor_mensalidade']) ? $_POST['valor_mensalidade'] : 0;
    $vencimento = !empty($_POST['dia_vencimento']) ? $_POST['dia_vencimento'] : 0;
    $bairro = $_POST['bairro'] ?? '';
    $id_compartilhar = !empty($_POST['quem_compartilhar']) ? $_POST['quem_compartilhar'] : 0;
    $novo_valor = !empty($_POST['novo_valor']) ? $_POST['novo_valor'] : 0;

    $valor_mensalidade = !empty($valor) ? $valor : $novo_valor;

    // 2. Prepara o INSERT do Aluno
    $sql = "INSERT INTO alunos (nome_crianca, nome_responsavel, telefone_responsavel, id_escola, periodo, serie, endereco, valor_mensalidade, dia_vencimento, bairro) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssisssdis", $nome_crianca, $nome_responsavel, $telefone, $id_escola, $periodo, $serie, $endereco, $valor_mensalidade, $vencimento, $bairro);

    if ($stmt->execute()) {
        $id_aluno = $conn->insert_id; // Pega o ID do aluno que acabou de ser criado
        $mes_atual = date('Y-m');

        $data = explode('-', $mes_atual);
        $ano = (int)$data[0];
        $mes = (int)$data[1];

        // 3. Verifica se é mensalidade compartilhada ou única
        if ($id_compartilhar != 0 && $novo_valor != 0) {
            //caso a mensalidade seja compartilhada, vamos colocar o id do novo aluno na coluna men_compartilhada da tabela alunos, e vamos fazer um update para o valor da mensalidade
                $insert_comp = "UPDATE alunos SET men_compartilhada = ? WHERE id = ?";
                $stmt_comp = $conn->prepare($insert_comp);
                $stmt_comp->bind_param("ii", $id_aluno, $id_compartilhar);
                $stmt_comp->execute();

                $update_valor = "UPDATE alunos SET valor_mensalidade = ? WHERE id = ?";
                $stmt_update = $conn->prepare($update_valor);
                $stmt_update->bind_param("di", $valor_mensalidade, $id_compartilhar);
                $stmt_update->execute();
        } else {
            // Mensalidade única
            $sql_pagamento = "INSERT INTO mensalidades (id_aluno, mes, ano, status, valor_devido) VALUES (?, ?, ?, 'Pendente', ?)";
            $stmt_pg = $conn->prepare($sql_pagamento);

            $stmt_pg->bind_param("iiid", $id_aluno, $mes, $ano, $valor_mensalidade);
            $stmt_pg->execute();

            echo "Aluno e mensalidade cadastrados com sucesso! ID do aluno: $id_aluno";
        }

    } else {
        echo "Erro ao cadastrar aluno: " . $stmt->error;
    }
}