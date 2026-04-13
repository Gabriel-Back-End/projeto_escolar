<?php
require_once '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome_escola'];
    $tipo = $_POST['tipo_periodo']; // Captura o novo campo

    // Adicionamos o 's' extra no bind_param para a string do tipo
    $sql = "INSERT INTO escolas (nome_escola, status, tipo_periodo) VALUES (?, 1, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nome, $tipo);

    if ($stmt->execute()) {
        header("Location: ../../frontend/telas/gerenciar_escolas.php?sucesso=1");
    } else {
        echo "Erro ao salvar escola: " . $conn->error;
    }
}