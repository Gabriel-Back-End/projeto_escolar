<?php
require_once '../conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ordens = $_POST['ordem']; // Isso é um array: [id_aluno => valor_ordem]
    $periodo = $_POST['periodo'];

    // Preparamos a query uma única vez para performance
    $stmt = $conn->prepare("UPDATE alunos SET ordem_rota = ? WHERE id = ?");

    foreach ($ordens as $id => $posicao) {
        $stmt->bind_param("ii", $posicao, $id);
        $stmt->execute();
    }

    header("Location: ../../frontend/telas/rota.php?periodo=" . $periodo);
}