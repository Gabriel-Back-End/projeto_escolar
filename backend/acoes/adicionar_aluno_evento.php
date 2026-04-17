<?php
require_once '../conexao.php';

$id_evento = $_POST['id_evento'];
$id_aluno = $_POST['id_aluno'];

$sql = "INSERT INTO evento_participantes (id_evento, id_aluno, pago) VALUES (?, ?, 'Não')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_evento, $id_aluno);

if ($stmt->execute()) {
    header("Location: ../../frontend/telas/detalhes_evento.php?id=$id_evento");
}