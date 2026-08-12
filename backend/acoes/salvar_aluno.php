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

    echo "nome: " . $nome_crianca . "<br>";
    echo "nome responsavel: " . $nome_responsavel . "<br>";
    echo "telefone: " . $telefone . "<br>";
    echo "id escola: " . $id_escola . "<br>";
    echo "periodo: " . $periodo . "<br>";
    echo "serie: " . $serie . "<br>";
    echo "endereco: " . $endereco . "<br>";
    echo "vencimento: " . $vencimento . "<br>";
    echo "bairro: " . $bairro . "<br>";
    echo "id compartilhar: " . $id_compartilhar . "<br>";
    echo "Valor da Mensalidade: " . $valor_mensalidade . "<br>";

    //Descobrir se o aluno vai compartilhar a mensalidade ou será mensalidade única

        //Se o valor do id_compartilhar for igual a 0, o aluno cadastrado ele não compartilhará a mensalidade
        if($id_compartilhar == 0){
            //Como o aluno que será cadastrado ele não compartilhara a mensalidade temos que atribuir o valor 1 na coluna paga_mensalidade e deixaremos o aluno_titular_id vazio
                $sql = "INSERT INTO alunos (nome_crianca, nome_responsavel, telefone_responsavel, id_escola, periodo, serie, endereco, valor_mensalidade, dia_vencimento, paga_mensalidade) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssisssdi", $nome_crianca, $nome_responsavel, $telefone, $id_escola, $periodo, $serie, $endereco, $valor_mensalidade, $vencimento);

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

        }else {
            //Como o aluno que será cadastrado ele compartilhara a mensalidade temos que atribuir o valor 0 na coluna paga_mensalidade e deixaremos o aluno_titular_id com o id do aluno que ele compartilhará a mensalidade
                $sql = "INSERT INTO alunos (nome_crianca, nome_responsavel, telefone_responsavel, id_escola, periodo, serie, endereco, valor_mensalidade, dia_vencimento, paga_mensalidade, aluno_titular_id) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssisssdii", $nome_crianca, $nome_responsavel, $telefone, $id_escola, $periodo, $serie, $endereco, $valor_mensalidade, $vencimento, $id_compartilhar);
                $stmt->execute();

                
                header("Location: ../../frontend/telas/cadastro_alunos.php?sucesso=1");
        }
}