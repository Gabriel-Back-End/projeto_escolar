<?php
require_once '../../backend/conexao.php';

// 1. Pega os filtros
$escola_id = $_GET['id_escola'] ?? '';
$periodo_filtro = $_GET['periodo'] ?? '';

// 2. Busca todas as escolas para o Tio escolher
$sql_escolas = "SELECT * FROM escolas WHERE status = 1 ORDER BY nome_escola ASC";
$res_escolas = $conn->query($sql_escolas);

$alunos = [];

// 3. LÓGICA DE OURO: 
if ($escola_id && $periodo_filtro) {
    $sql_rota = "SELECT a.*, e.nome_escola 
                 FROM alunos a
                 JOIN escolas e ON a.id_escola = e.id
                 WHERE a.id_escola = ? 
                 AND (a.periodo = ? OR a.periodo = 'Integral') AND a.status = 'Ativo' 
                 ORDER BY a.ordem_rota ASC";
    
    $stmt = $conn->prepare($sql_rota);
    $stmt->bind_param("is", $escola_id, $periodo_filtro);
    $stmt->execute();
    $alunos = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Minha Rota - Sistema do Tio</title>
    <link rel="stylesheet" href="../../assets/css/estilo.css">
    <style>
        .seletor-escola {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            margin-bottom: 20px;
        }
        .alerta-integral {
            background: #fff3cd;
            color: #856404;
            padding: 10px;
            border-radius: 5px;
            font-size: 13px;
            margin-bottom: 15px;
            border-left: 4px solid #ffeeba;
        }
        /* Estilo rápido para o botão de impressão não quebrar o layout */
        .btn-print {
            background: #34495e;
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
    </style>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>

<div class="container" style="max-width: 700px;">

    <?php include '../includes/menu.php'; ?>

    <h2><i class="ph ph-magnifying-glass"></i> Planejamento de Rota</h2>

    <div class="seletor-escola">
        <form action="" method="GET" style="display: grid; gap: 15px;">
            <div class="form-group">
                <label>Para qual escola vamos agora?</label>
                <select name="id_escola" onchange="this.form.submit()" required>
                    <option value="">Selecione a Escola...</option>
                    <?php while($esc = $res_escolas->fetch_assoc()): ?>
                        <option value="<?= $esc['id'] ?>" <?= $escola_id == $esc['id'] ? 'selected' : '' ?>>
                            <?= $esc['nome_escola'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <?php if($escola_id): ?>
            <div class="form-group">
                <label>Qual turno você vai fazer?</label>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" name="periodo" value="Manhã" class="btn-salvar <?= $periodo_filtro == 'Manhã' ? '' : 'btn-outline' ?>" style="flex:1; background: <?= $periodo_filtro == 'Manhã' ? '#2ecc71' : '#ccc' ?>"><i class="ph ph-sun"></i> Manhã</button>
                    <button type="submit" name="periodo" value="Tarde" class="btn-salvar <?= $periodo_filtro == 'Tarde' ? '' : 'btn-outline' ?>" style="flex:1; background: <?= $periodo_filtro == 'Tarde' ? '#e67e22' : '#ccc' ?>"><i class="ph ph-cloud-sun"></i> Tarde</button>
                </div>
            </div>
            <?php endif; ?>
        </form>
    </div>

    <?php if ($escola_id && $periodo_filtro): ?>
        
        <div style="margin-bottom: 15px; display: flex; justify-content: flex-end;">
            <a href="imprimir_rota.php?id_escola=<?= $escola_id ?>&periodo=<?= $periodo_filtro ?>" target="_blank" class="btn-print">
                <i class="ph ph-printer"></i> Imprimir Lista
            </a>
        </div>

        <div class="alerta-integral">
            <i class="ph ph-lightbulb"></i> Mostrando alunos de período <strong><?= $periodo_filtro ?></strong> e também os de <strong>Tempo Integral</strong> desta escola.
        </div>

        <?php if ($alunos->num_rows > 0): ?>
            <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
                <strong>Ordem de Coleta:</strong>
                <a href="configurar_rota.php?id_escola=<?= $escola_id ?>&periodo=<?= $periodo_filtro ?>">⚙️ Ajustar Sequência</a>
            </div>
            
            <?php while($aluno = $alunos->fetch_assoc()): ?>
                <div class="card-aluno" style="display:flex; background:white; padding:15px; margin-bottom:10px; border-radius:8px; align-items:center; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                    <div style="background:#2c3e50; color:white; width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center; margin-right:15px; font-weight:bold;">
                        <?= $aluno['ordem_rota'] ?>
                    </div>
                    <div style="flex:1">
                        <strong style="display:block"><?= $aluno['nome_crianca'] ?></strong>
                        <small style="color:#666"><i class="ph ph-house"></i> <?= $aluno['endereco'] ?></small>
                        <?php if($aluno['periodo'] == 'Integral'): ?>
                            <span style="font-size:10px; background:#eee; padding:2px 5px; border-radius:3px; margin-left:5px;">INTEGRAL</span>
                        <?php endif; ?>
                    </div>
                    <a href="https://www.google.com/maps/search/?api=1&query=<?= urlencode($aluno['endereco']) ?>" target="_blank" style="text-decoration:none; font-size:20px;"><i class="ph ph-map-pin-area"></i></a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center; padding:20px; color:#999;">Nenhum aluno para este turno nesta escola.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>
</html>