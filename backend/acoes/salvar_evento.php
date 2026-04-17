<?php
require_once '../conexao.php';

$titulo = $_POST['titulo'];
$valor = $_POST['valor_passeio'];
$data = $_POST['data_evento'];
$imagem_nome = null;

// Lógica de Upload da Imagem
if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
    $extensao = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
    $imagem_nome = md5(uniqid()) . "." . $extensao;
    $destino = "../../assets/img/eventos/" . $imagem_nome;
    
    // Cria a pasta se não existir
    if (!is_dir("../../assets/img/eventos/")) {
        mkdir("../../assets/img/eventos/", 0777, true);
    }
    
    move_uploaded_file($_FILES['imagem']['tmp_name'], $destino);
}

$sql = "INSERT INTO eventos (titulo, valor_passeio, data_evento, imagem) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sdss", $titulo, $valor, $data, $imagem_nome);

if ($stmt->execute()) {
    header("Location: ../../frontend/telas/eventos.php?sucesso=1");
} else {
    echo "Erro ao salvar: " . $conn->error;
}