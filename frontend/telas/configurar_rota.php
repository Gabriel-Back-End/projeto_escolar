<?php
require_once '../../backend/conexao.php';

// Pegamos a escola e o período da URL (filtros vindos da tela de rota)
$escola_id = $_GET['id_escola'] ?? '';
$periodo = $_GET['periodo'] ?? 'Manhã';

// Buscamos os alunos filtrando por escola e período (incluindo os Integrais)
$sql = "SELECT id, nome_crianca, endereco, ordem_rota 
        FROM alunos 
        WHERE id_escola = ? 
        AND (periodo = ? OR periodo = 'Integral') 
        AND status = 'Ativo' 
        ORDER BY ordem_rota ASC, nome_crianca ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $escola_id, $periodo);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Definir Ordem da Rota</title>
    <link rel="stylesheet" href="../../assets/css/estilo.css">
    <style>
        /* (Seu CSS que enviamos antes está perfeito, mantenha-o aqui) */
        .container-ajuste { max-width: 600px; margin: 20px auto; padding: 20px; background: #f4f7f6; border-radius: 15px; font-family: sans-serif; }
        h2 { color: #2c3e50; text-align: center; margin-bottom: 5px; }
        .instrucao { text-align: center; color: #7f8c8d; margin-bottom: 30px; font-size: 14px; }
        .item-aluno { background: white; display: flex; align-items: center; padding: 15px; margin-bottom: 12px; border-radius: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .input-ordem { width: 50px; height: 40px; text-align: center; font-size: 18px; font-weight: bold; border: 2px solid #ddd; border-radius: 8px; margin-right: 20px; }
        .info-aluno { flex: 1; }
        .info-aluno strong { display: block; font-size: 16px; color: #34495e; }
        .info-aluno small { color: #95a5a6; font-size: 12px; }
        .btn-salvar-rota { background: #27ae60; color: white; border: none; padding: 15px; font-size: 16px; font-weight: bold; border-radius: 30px; cursor: pointer; width: 100%; margin-top: 20px; }
    </style>
</head>
<body>

    <div class="container-ajuste">
        <a href="rota.php?id_escola=<?= $escola_id ?>&periodo=<?= $periodo ?>" style="text-decoration: none; color: #3498db;">⬅️ Voltar</a>
        
        <h2>Definir Sequência</h2>
        <p class="instrucao">Ajuste os números para definir a ordem de coleta.</p>

        <form action="../../backend/acoes/salvar_rota.php" method="POST">
            <input type="hidden" name="id_escola" value="<?= $escola_id ?>">
            <input type="hidden" name="periodo" value="<?= $periodo ?>">

            <?php 
            // O WHILE QUE ESTAVA FALTANDO:
            while($aluno = $result->fetch_assoc()): 
            ?>
                <div class="item-aluno">
                    <input type="number" 
                           name="ordem[<?= $aluno['id'] ?>]" 
                           value="<?= $aluno['ordem_rota'] ?>" 
                           class="input-ordem">
                    
                    <div class="info-aluno">
                        <strong><?= $aluno['nome_crianca'] ?></strong>
                        <small>🏠 <?= $aluno['endereco'] ?></small>
                    </div>
                </div>
            <?php endwhile; ?>

            <div class="btn-container">
                <button type="submit" class="btn-salvar-rota">💾 Salvar Nova Sequência</button>
            </div>
        </form>
    </div>
    
</body>
</html>