<?php
require_once '../../backend/conexao.php';

$periodo = $_GET['periodo'] ?? 'Manhã';
$sql = "SELECT id, nome_crianca, endereco, ordem_rota FROM alunos WHERE periodo = ? ORDER BY ordem_rota ASC, nome_crianca ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $periodo);
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
        .item-rota { display: flex; align-items: center; gap: 15px; background: white; padding: 10px; margin-bottom: 5px; border-radius: 5px; }
        .input-ordem { width: 50px; padding: 5px; text-align: center; border: 2px solid var(--primary-color); border-radius: 4px; }
    </style>
</head>
<body>

<div class="container">
    <?php include '../includes/menu.php'; ?>
    
    <h2>Definir Sequência de Busca</h2>
    <p>Dê um número para cada aluno na ordem que você vai passar na casa deles.</p>

    <form action="../../backend/acoes/salvar_ordem.php" method="POST">
        <input type="hidden" name="periodo" value="<?php echo $periodo; ?>">
        
        <?php while($aluno = $result->fetch_assoc()): ?>
            <div class="item-rota">
                <input type="number" name="ordem[<?php echo $aluno['id']; ?>]" 
                       value="<?php echo $aluno['ordem_rota']; ?>" class="input-ordem">
                <div>
                    <strong><?php echo $aluno['nome_crianca']; ?></strong><br>
                    <small><?php echo $aluno['endereco']; ?></small>
                </div>
            </div>
        <?php endwhile; ?>

        <button type="submit" class="btn-salvar">Salvar Nova Ordem</button>
    </form>
</div>
</body>
</html>