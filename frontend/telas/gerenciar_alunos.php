<?php
require_once '../../backend/conexao.php';

$busca = $_GET['busca'] ?? '';
$sql = "SELECT a.*, e.nome_escola 
        FROM alunos a 
        JOIN escolas e ON a.id_escola = e.id 
        WHERE a.nome_crianca LIKE ? 
        ORDER BY a.status ASC, a.nome_crianca ASC";

$stmt = $conn->prepare($sql);
$termo = "%$busca%";
$stmt->bind_param("s", $termo);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Alunos - Sistema do Tio</title>
    <link rel="stylesheet" href="../../assets/css/estilo.css">
    <style>
        .topo-acoes {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .btn-novo {
            background-color: #2ecc71;
            color: white;
            text-decoration: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 6px rgba(46, 204, 113, 0.2);
        }
        .btn-novo:hover { background-color: #27ae60; }

        .busca-container {
            display: flex;
            flex: 1;
            min-width: 250px;
        }
        .busca-input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px 0 0 5px;
        }
        .btn-busca {
            background: #3498db;
            color: white;
            border: none;
            padding: 0 15px;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
        }

        /* Tabela Estilizada */
        .tabela-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8f9fa; color: #7f8c8d; text-align: left; padding: 15px; font-size: 13px; text-transform: uppercase; }
        td { padding: 15px; border-top: 1px solid #eee; vertical-align: middle; }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .ativo { background: #ecfaf2; color: #2ecc71; }
        .inativo { background: #fdf2f2; color: #e74c3c; }

        .acoes-links { display: flex; gap: 15px; font-size: 18px; }
        .acoes-links a { text-decoration: none; }
    </style>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>

<div class="container">
    
    <?php include '../includes/menu.php'; ?>

    <div class="topo-acoes">
        <div>
            <h2 style="margin:0"><i class="ph-light ph-student"></i> Alunos</h2>
            <p style="color:#7f8c8d; margin:0; font-size:14px;">Total de alunos cadastrados</p>
        </div>
        
        <a href="cadastro_alunos.php" class="btn-novo">
            <span><i class="ph-light ph-plus"></i></span> Novo Aluno
        </a>
    </div>

    <form action="" method="GET" class="busca-container" style="margin-bottom: 25px;">
        <input type="text" name="busca" class="busca-input" placeholder="Buscar por nome da criança..." value="<?= htmlspecialchars($busca) ?>">
        <button type="submit" class="btn-busca"><i class="ph-bold ph-magnifying-glass"></i> Buscar</button>
    </form>

    <div class="tabela-container">
        <table>
            <thead>
                <tr>
                    <th>Aluno / Escola</th>
                    <th>Período</th>
                    <th>Status</th>
                    <th style="text-align: right;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($aluno = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <strong style="color:#2c3e50"><?= $aluno['nome_crianca'] ?></strong><br>
                            <small style="color:#7f8c8d"><i class="ph ph-backpack" style="margin-right: 4px;"></i> <?= $aluno['nome_escola'] ?></small>
                        </td>
                        <td>
                            <span style="font-size: 13px;"><?= $aluno['periodo'] ?></span>
                        </td>
                        <td>
                            <span class="status-badge <?= strtolower($aluno['status']) ?>">
                                <?= $aluno['status'] ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div class="acoes-links" style="justify-content: flex-end;">
                                <a href="editar_aluno.php?id=<?= $aluno['id'] ?>" title="Editar">✏️</a>
                                
                                <?php if($aluno['status'] == 'Ativo'): ?>
                                    <a href="../../backend/acoes/status_aluno.php?id=<?= $aluno['id'] ?>&status=Inativo" 
                                       onclick="return confirm('Desativar <?= $aluno['nome_crianca'] ?>?')" title="Desativar">🚫</a>
                                <?php else: ?>
                                    <a href="../../backend/acoes/status_aluno.php?id=<?= $aluno['id'] ?>&status=Ativo" title="Ativar">✅</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align:center; padding:30px; color:#999;">Nenhum aluno encontrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>