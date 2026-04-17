<?php
require_once '../../backend/conexao.php';

$id_evento = $_GET['id'];

// 1. Busca os dados do passeio
$stmt = $conn->prepare("SELECT * FROM eventos WHERE id = ?");
$stmt->bind_param("i", $id_evento);
$stmt->execute();
$evento = $stmt->get_result()->fetch_assoc();

// 2. Busca apenas os nomes, ordenados pela sequência
$sql_participantes = "
    SELECT p.ordem_passeio, p.pago, a.nome_crianca 
    FROM evento_participantes p 
    JOIN alunos a ON p.id_aluno = a.id 
    WHERE p.id_evento = ? 
    ORDER BY p.ordem_passeio ASC, a.nome_crianca ASC";

$stmt_p = $conn->prepare($sql_participantes);
$stmt_p->bind_param("i", $id_evento);
$stmt_p->execute();
$participantes = $stmt_p->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista Simplificada - <?= $evento['titulo'] ?></title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 30px; background: #fff; }
        
        .topo { text-align: center; border-bottom: 2px solid #000; margin-bottom: 20px; padding-bottom: 10px; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #000; padding: 12px; text-align: left; }
        th { background: #eee; text-transform: uppercase; font-size: 14px; }
        
        .col-ordem { width: 40px; text-align: center; }
        .col-check { width: 60px; text-align: center; }
        
        .quadrado { width: 22px; height: 22px; border: 1px solid #000; margin: 0 auto; }
        
        .btn-imprimir { 
            display: inline-block; padding: 10px 20px; background: #2c3e50; 
            color: #fff; text-decoration: none; border-radius: 5px; margin-bottom: 20px;
        }

        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: center;">
        <a href="javascript:window.print()" class="btn-imprimir">🖨️ IMPRIMIR LISTA</a>
    </div>

    <div class="topo">
        <h1 style="margin: 0;"><?= mb_strtoupper($evento['titulo']) ?></h1>
        <p style="margin: 5px 0;">Data: <?= date('d/m/Y', strtotime($evento['data_evento'])) ?> | Total: <?= $participantes->num_rows ?> Alunos</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-ordem">Nº</th>
                <th>NOME DA CRIANÇA</th>
                <th class="col-check">OK?</th>
            </tr>
        </thead>
        <tbody>
            <?php while($p = $participantes->fetch_assoc()): ?>
            <tr>
                <td class="col-ordem"><strong><?= $p['ordem_passeio'] ?></strong></td>
                <td style="font-size: 16px;"><?= $p['nome_crianca'] ?></td>
                <td class="col-check"><div class="quadrado"></div></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div style="margin-top: 40px;">
        <p><strong>Observações:</strong> __________________________________________________________________</p>
    </div>

</body>
</html>