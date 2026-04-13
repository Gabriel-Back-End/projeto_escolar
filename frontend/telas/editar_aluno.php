<?php
require_once '../../backend/conexao.php';

$id = $_GET['id'];

// 1. Busca os dados atuais do aluno
$sql_aluno = "SELECT * FROM alunos WHERE id = ?";
$stmt = $conn->prepare($sql_aluno);
$stmt->bind_param("i", $id);
$stmt->execute();
$aluno = $stmt->get_result()->fetch_assoc();

// 2. Busca as escolas para o select
$escolas = $conn->query("SELECT * FROM escolas WHERE status = 1 ORDER BY nome_escola ASC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Aluno - Sistema do Tio</title>
    <link rel="stylesheet" href="../../assets/css/estilo.css">
</head>
<body>

<div class="container" style="max-width: 600px;">

    <?php include '../includes/menu.php'; ?>

    <h2>✏️ Editar Aluno</h2>
    
    <form action="../../backend/acoes/atualizar_aluno.php" method="POST">
        <input type="hidden" name="id" value="<?= $aluno['id'] ?>">

        <div class="form-group">
            <label>Nome da Criança:</label>
            <input type="text" name="nome_crianca" value="<?= $aluno['nome_crianca'] ?>" required>
        </div>

        <div class="form-group">
            <label>Endereço de Busca:</label>
            <input type="text" name="endereco" value="<?= $aluno['endereco'] ?>" required>
        </div>

        <div class="form-group">
            <label>Escola:</label>
            <select name="id_escola" required>
                <?php while($esc = $escolas->fetch_assoc()): ?>
                    <option value="<?= $esc['id'] ?>" <?= $aluno['id_escola'] == $esc['id'] ? 'selected' : '' ?>>
                        <?= $esc['nome_escola'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div style="display: flex; gap: 10px;">
            <div class="form-group" style="flex: 1;">
                <label>Período:</label>
                <select name="periodo">
                    <option value="Manhã" <?= $aluno['periodo'] == 'Manhã' ? 'selected' : '' ?>>Manhã</option>
                    <option value="Tarde" <?= $aluno['periodo'] == 'Tarde' ? 'selected' : '' ?>>Tarde</option>
                    <option value="Integral" <?= $aluno['periodo'] == 'Integral' ? 'selected' : '' ?>>Integral</option>
                </select>
            </div>
            <div class="form-group" style="flex: 1;">
                <label>Mensalidade (R$):</label>
                <input type="number" step="0.01" name="valor_mensalidade" value="<?= $aluno['valor_mensalidade'] ?>" required>
            </div>
        </div>

        <button type="submit" class="btn-salvar">Salvar Alterações</button>
        <a href="gerenciar_alunos.php" style="display: block; text-align: center; margin-top: 15px; color: #666;">Cancelar</a>
    </form>
</div>

</body>
</html>