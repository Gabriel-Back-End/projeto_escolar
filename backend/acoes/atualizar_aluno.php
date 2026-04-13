<?php
require_once '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nome = $_POST['nome_crianca'];
    $endereco = $_POST['endereco'];
    $id_escola = $_POST['id_escola'];
    $periodo = $_POST['periodo'];
    $valor = $_POST['valor_mensalidade'];

    $sql = "UPDATE alunos SET 
            nome_crianca = ?, 
            endereco = ?, 
            id_escola = ?, 
            periodo = ?, 
            valor_mensalidade = ? 
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisdi", $nome, $endereco, $id_escola, $periodo, $valor, $id);

    if ($stmt->execute()) {
        header("Location: ../../frontend/telas/gerenciar_alunos.php?sucesso=1");
    } else {
        echo "Erro ao atualizar: " . $conn->error;
    }
}