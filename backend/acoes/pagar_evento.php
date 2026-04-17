<?php
require_once '../conexao.php';

$id_participacao = $_POST['id_participacao'];
$id_evento = $_POST['id_evento'];
$metodo = $_POST['metodo'];

$sql = "UPDATE evento_participantes SET pago = 'Sim', metodo_pagamento = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $metodo, $id_participacao);

if ($stmt->execute()) {
    header("Location: ../../frontend/telas/detalhes_evento.php?id=$id_evento");
}