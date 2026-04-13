<?php
require_once '../../backend/conexao.php';

$id_escola = $_GET['id_escola'] ?? '';
$periodo = $_GET['periodo'] ?? '';

// Busca os alunos baseados no filtro da rota
$sql = "SELECT a.*, e.nome_escola 
        FROM alunos a 
        JOIN escolas e ON a.id_escola = e.id 
        WHERE a.status = 'Ativo'";

if ($id_escola) $sql .= " AND a.id_escola = " . intval($id_escola);
if ($periodo) $sql .= " AND a.periodo = '$periodo'";

$sql .= " ORDER BY a.ordem_rota ASC, a.nome_crianca ASC";
$result = $conn->query($sql);

$nome_unidade = "Todas as Escolas";
if($id_escola) {
    $esc = $conn->query("SELECT nome_escola FROM escolas WHERE id = $id_escola")->fetch_assoc();
    $nome_unidade = $esc['nome_escola'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista - <?= $nome_unidade ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 20px; color: #333; background-color: #fff; }
        .header-print { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        
        /* Ajuste da Tabela */
        table { width: 100%; border-collapse: collapse; table-layout: fixed; } /* table-layout fixed ajuda a controlar as larguras */
        th, td { border: 1px solid #999; padding: 6px 4px; text-align: left; font-size: 11px; overflow: hidden; }
        th { background: #f2f2f2; text-transform: uppercase; }
        
        /* Estilo dos Quadrinhos */
        .check-group { display: flex; justify-content: space-around; gap: 2px; }
        .check-box { width: 14px; height: 14px; border: 1px solid #333; display: inline-block; }
        .day-label { font-size: 8px; display: block; text-align: center; color: #666; margin-bottom: 2px; }

        .btn-imprimir { 
            background: #2c3e50; color: white; padding: 10px 20px; 
            border: none; border-radius: 5px; cursor: pointer; margin-bottom: 20px; font-weight: bold;
        }

        @media print {
            .btn-imprimir, .no-print { display: none; }
            body { padding: 0; }
            th { background: #eee !important; -webkit-print-color-adjust: exact; }
            table { border: 1px solid #000; }
        }
    </style>
</head>
<body>

    <button class="btn-imprimir" onclick="window.print()">🖨️ Imprimir Lista</button>
    <a href="rota.php" class="no-print" style="margin-left:10px; color:#666; text-decoration:none;">⬅️ Voltar</a>

    <div class="header-print">
        <h2 style="margin:0;"><?= $nome_unidade ?></h2>
        <p style="margin:5px 0; font-size: 14px;">
            <strong>Período:</strong> <?= $periodo ?: 'Geral' ?> 
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">Nº</th>
                <th width="35%">Nome do Aluno</th>
                <th width="20%">Escola</th>
                <th width="30%">Frequência Semanal (S T Q Q S)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $cont = 1;
            while($aluno = $result->fetch_assoc()): 
            ?>
            <tr>
                <td style="text-align:center;"><?= $cont++ ?></td>
                <td><strong><?= mb_strtoupper($aluno['nome_crianca']) ?></strong></td>
                <td><?= $aluno['nome_escola'] ?></td>
                <td>
                    <div class="check-group">
                        <div><span class="day-label">S</span><div class="check-box"></div></div>
                        <div><span class="day-label">T</span><div class="check-box"></div></div>
                        <div><span class="day-label">Q</span><div class="check-box"></div></div>
                        <div><span class="day-label">Q</span><div class="check-box"></div></div>
                        <div><span class="day-label">S</span><div class="check-box"></div></div>
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>