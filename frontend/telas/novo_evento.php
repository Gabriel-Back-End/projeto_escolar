<?php require_once '../../backend/conexao.php'; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Criar Novo Passeio</title>
    <link rel="stylesheet" href="../../assets/css/estilo.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>
<div class="container" style="max-width: 500px;">
    <?php include '../includes/menu.php'; ?>
    
    <a href="eventos.php" style="text-decoration: none; color: #666;"><i class="ph ph-caret-left"></i> Voltar</a>
    <h2 style="margin-top: 10px;">🎒 Novo Passeio</h2>

    <form action="../../backend/acoes/salvar_evento.php" method="POST" enctype="multipart/form-data" class="seletor-escola">
        <div class="form-group">
            <label>Título do Evento</label>
            <input type="text" name="titulo" placeholder="Ex: Ida ao Zoológico" required>
        </div>

        <div class="form-group">
            <label>Valor por Aluno (R$)</label>
            <input type="number" step="0.01" name="valor_passeio" placeholder="0,00" required>
        </div>

        <div class="form-group">
            <label>Data do Evento</label>
            <input type="date" name="data_evento" required>
        </div>

        <div class="form-group">
            <label>Imagem de Capa</label>
            <input type="file" name="imagem" accept="image/*">
        </div>

        <button type="submit" class="btn-salvar" style="width: 100%; margin-top: 10px;">
            <i class="ph ph-check"></i> Criar Passeio
        </button>
    </form>
</div>
</body>
</html>