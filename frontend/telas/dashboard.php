<?php
require_once '../../backend/conexao.php';

$mes_atual = date('m');
$ano_atual = date('Y');

// 1. Total de Alunos Ativos
$res_alunos = $conn->query("SELECT COUNT(*) as total FROM alunos WHERE status = 'Ativo'");
$total_alunos = $res_alunos->fetch_assoc()['total'];

// 2. Quantos JÁ PAGARAM este mês? (Baseado no status da mensalidade)
$sql_pagos = "SELECT COUNT(*) as total FROM mensalidades 
              WHERE mes = '$mes_atual' AND ano = '$ano_atual' AND status = 'Pago'";
$res_pagos = $conn->query($sql_pagos);
$quantos_pagaram = $res_pagos->fetch_assoc()['total'] ?? 0;

// 3. Quantos FALTAM PAGAR? (Pendentes do mês atual)
$sql_pendentes = "SELECT COUNT(*) as total FROM mensalidades 
                 WHERE mes = '$mes_atual' AND ano = '$ano_atual' AND status = 'Pendente'";
$res_pendentes = $conn->query($sql_pendentes);
$quantos_faltam = $res_pendentes->fetch_assoc()['total'] ?? 0;

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Tio - Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/estilo.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .grid-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
        }
        .card h3 { margin: 0; color: #7f8c8d; font-size: 14px; text-transform: uppercase; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .card .valor { font-size: 28px; font-weight: bold; margin: 10px 0; display: block; }
        
        .c-azul { border-top: 5px solid #3498db; }
        .c-verde { border-top: 5px solid #2ecc71; color: #27ae60; }
        .c-laranja { border-top: 5px solid #e67e22; color: #d35400; }
        .c-vermelho { border-top: 5px solid #e74c3c; color: #c0392b; }
        .c-roxo { border-top: 5px solid #9b59b6; }

        .btn-acao-rapida {
            background: #f1f2f6;
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
            border: 1px dashed #ccc;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <?php include '../includes/menu.php'; ?>

    <h2>Bem-vindo, Tio Fabio! 👋</h2>
    <p>Resumo de <strong><?= date('d/m/Y') ?></strong>:</p>

    <div class="grid-cards">
        <div class="card c-roxo">
            <h3><i class="ph ph-student"></i> Alunos Ativos</h3>
            <span class="valor"><?= $total_alunos ?></span>
        </div>

        <div class="card c-verde">
            <h3><i class="ph ph-check-circle"></i> Pagos (Mês)</h3>
            <span class="valor"><?= $quantos_pagaram ?></span>
        </div>

        <div class="card c-laranja">
            <h3><i class="ph ph-warning-circle"></i> Pendentes (Mês)</h3>
            <span class="valor"><?= $quantos_faltam ?></span>
        </div>
    </div>

    <div class="btn-acao-rapida">
        <h4 style="margin-bottom: 10px;">Administração do Mês</h4>
        <p style="font-size: 13px; color: #666; margin-bottom: 15px;">
            Iniciou um novo mês? Clique no botão abaixo para gerar as cobranças de todos os alunos ativos.
        </p>
        <a href="../../backend/acoes/gerar_mensalidades.php" 
           onclick="return confirm('Deseja gerar as mensalidades do mês atual para todos os alunos?')"
           class="btn-salvar" style="background: #27ae60; text-decoration: none;">
           🚀 Gerar Mensalidades de <?= date('F') ?>
        </a>
    </div>

    <div style="margin-top: 30px; display: flex; gap: 20px;">
        <a href="rota.php" class="btn-salvar" style="text-decoration: none; text-align: center; background: #34495e; flex: 1;">Ver Rota</a>
        <a href="financeiro.php" class="btn-salvar" style="text-decoration: none; text-align: center; flex: 1;">Ir para Cobranças</a>
    </div>
</div>

</body>
</html>