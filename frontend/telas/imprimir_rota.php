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

$sql .= " ORDER BY a.nome_crianca ASC";
$result = $conn->query($sql);

// Busca nome da escola para o cabeçalho
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
    <title>Lista de Chamada - <?= $nome_unidade ?></title>
    <style>
        body { font-family: sans-serif; padding: 20px; color: #333; }
        .header-print { text-align: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; font-size: 12px; }
        th { background: #eee; }
        
        /* Quadrinhos para o Tio marcar OK */
        .check-box { width: 20px; height: 20px; border: 1px solid #000; display: inline-block; }
        
        .btn-imprimir { 
            background: #2c3e50; color: white; padding: 10px 20px; 
            border: none; border-radius: 5px; cursor: pointer; margin-bottom: 20px;
        }

        @media print {
            .btn-imprimir, .no-print { display: none; }
            body { padding: 0; }
            th { background: #eee !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>

    <button class="btn-imprimir" onclick="window.print()">🖨️ Imprimir Lista</button>
    <a href="rota.php" class="no-print" style="margin-left:10px; color:#666;">⬅️ Voltar</a>

    <div class="header-print">
        <h1 style="margin:0;">Lista de Transporte Escolar</h1>
        <p style="margin:5px 0;">
            <strong>Escola:</strong> <?= $nome_unidade ?> | 
            <strong>Período:</strong> <?= $periodo ?: 'Todos' ?>
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="30%">Aluno</th>
                <th width="15%">Escola</th>
                <th width="10%">Ida</th>
                <th width="10%">Volta</th>
            </tr>
        </thead>
        <tbody>
            <?php while($aluno = $result->fetch_assoc()): ?>
            <tr>
                <td><strong><?= $aluno['nome_crianca'] ?></strong></td>
                <td><?= $aluno['nome_escola'] ?></td>
                <td style="text-align:center;"><div class="check-box"></div></td>
                <td style="text-align:center;"><div class="check-box"></div></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div style="margin-top: 30px; font-size: 10px; text-align: center; color: #777;">
        Gerado pelo Sistema do Tio em <?= date('d/m/Y H:i') ?>
    </div>

</body>
</html>