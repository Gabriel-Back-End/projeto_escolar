<?php
require_once '../../backend/conexao.php';

$mes_atual = date('m');
$ano_atual = date('Y');

// 1. Total de Alunos Ativos
$res_alunos = $conn->query("SELECT COUNT(*) as total FROM alunos WHERE status = 'Ativo'");
$total_alunos = $res_alunos->fetch_assoc()['total'];

// 2. Quantos JÁ PAGARAM este mês?
// Contamos alunos únicos que têm registro na tabela de pagamentos neste mês
$sql_pagos = "SELECT COUNT(DISTINCT id_aluno) as total FROM pagamentos 
              WHERE MONTH(data_confirmacao) = '$mes_atual' 
              AND YEAR(data_confirmacao) = '$ano_atual'";
$res_pagos = $conn->query($sql_pagos);
$quantos_pagaram = $res_pagos->fetch_assoc()['total'] ?? 0;

// 3. Quantos FALTAM PAGAR?
// É a conta simples: Total de Ativos - Quem já pagou
$quantos_faltam = $total_alunos - $quantos_pagaram;


?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Tio - Dashboard</title>
    <link rel="stylesheet" href="../../assets/css/estilo.css">
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
        .card h3 { margin: 0; color: #7f8c8d; font-size: 14px; text-transform: uppercase; }
        .card .valor { font-size: 24px; font-weight: bold; margin: 10px 0; display: block; }
        
        .c-azul { border-top: 5px solid #3498db; }
        .c-verde { border-top: 5px solid #2ecc71; color: #27ae60; }
        .c-laranja { border-top: 5px solid #e67e22; color: #d35400; }
        .c-roxo { border-top: 5px solid #9b59b6; }
    </style>
</head>
<body>

    
<div class="container">

    <?php include '../includes/menu.php'; ?>

    <h2>Bem-vindo, Tio! 👋</h2>

    <p>Aqui está o resumo do seu transporte para <strong><?= date('M / Y') ?></strong>:</p>

    <div class="grid-cards">
        <div class="card c-roxo">
            <h3>👥 Alunos Ativos</h3>
            <span class="valor"><?= $total_alunos ?></span>
        </div>

        <div class="card c-verde">
            <h3>✅ Já Pagaram</h3>
            <span class="valor"><?= $quantos_pagaram ?> <small style="font-size: 12px; color: #7f8c8d;">alunos</small></span>
        </div>

        <div class="card c-laranja">
            <h3>⏳ Faltam Pagar</h3>
            <span class="valor"><?= $quantos_faltam ?> <small style="font-size: 12px; color: #7f8c8d;">alunos</small></span>
        </div>

    </div>

    <div style="margin-top: 40px; display: flex; gap: 20px;">
        <a href="rota.php" class="btn-salvar" style="text-decoration: none; text-align: center; background: #34495e;">Ver Rota de Hoje</a>
        <a href="financeiro.php" class="btn-salvar" style="text-decoration: none; text-align: center;">Ir para Cobranças</a>
    </div>
</div>

</body>
</html>