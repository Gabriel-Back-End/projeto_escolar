<?php
require_once '../conexao.php';

$id = $_GET['id'];
$status_atual = $_GET['status'];

// Se era 1 vira 0, se era 0 vira 1
$novo_status = ($status_atual == 1) ? 0 : 1;

$sql = "UPDATE escolas SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $novo_status, $id);

if ($stmt->execute()) {
    header("Location: ../../frontend/telas/gerenciar_escolas.php");
}