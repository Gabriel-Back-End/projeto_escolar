<?php
require_once '../../backend/conexao.php';

// Lógica para buscar as escolas cadastradas
$sql = "SELECT * FROM escolas ORDER BY nome_escola ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Escolas - Sistema do Tio</title>
    <link rel="stylesheet" href="../../assets/css/estilo.css">
    <style>
        .form-escola {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            display: flex;
            gap: 10px;
            align-items: flex-end;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .form-escola .form-group { flex: 1; margin-bottom: 0; }
        .btn-add { width: auto; margin-top: 0; padding: 10px 20px; }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        .ativo { background: #d4edda; color: #155724; }
        .inativo { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

    
<div class="container" style="max-width: 800px;">
    <?php include '../includes/menu.php'; ?>
    
    <h2>Gerenciar Escolas</h2>

    <form action="../../backend/acoes/salvar_escola.php" method="POST" class="form-escola" style="flex-wrap: wrap;">
        <div class="form-group" style="flex: 2; min-width: 250px;">
            <label for="nome_escola">Nome da Escola:</label>
            <input type="text" name="nome_escola" id="nome_escola" required placeholder="Ex: Colégio Objetivo">
        </div>
        
        <div class="form-group" style="flex: 1; min-width: 150px;">
            <label for="tipo_periodo">Funcionamento:</label>
            <select name="tipo_periodo" id="tipo_periodo">
                <option value="Normal">Manhã/Tarde</option>
                <option value="Apenas Integral">Apenas Integral</option>
            </select>
        </div>
        
        <button type="submit" class="btn-salvar btn-add">Adicionar</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Nome da Escola</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php while($escola = $result->fetch_assoc()): ?>
            <tr>
                <td>
                    <strong><?php echo $escola['nome_escola']; ?></strong>
                    <br>
                    <small style="color: #888; font-style: italic;">
                        (Tipo: <?php echo $escola['tipo']; ?>)
                    </small>
                </td>
                <td>
                    <span class="status-badge <?php echo $escola['status'] ? 'ativo' : 'inativo'; ?>">
                        <?php echo $escola['status'] ? 'Ativa' : 'Inativa'; ?>
                    </span>
                </td>
                <td>
                    <a href="../../backend/acoes/status_escola.php?id=<?php echo $escola['id']; ?>&status=<?php echo $escola['status']; ?>" 
                    style="font-size: 12px; color: #3498db; text-decoration: none;">
                        <?php echo $escola['status'] ? '🚫 Desativar' : '✅ Ativar'; ?>
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>