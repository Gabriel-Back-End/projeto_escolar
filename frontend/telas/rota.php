<?php
require_once '../../backend/conexao.php';

// 1. Pega os filtros
$escola_id = $_GET['id_escola'] ?? '';
$periodo_filtro = $_GET['periodo'] ?? '';

// 2. Busca todas as escolas (Note que incluí o campo 'tipo' na query)
$sql_escolas = "SELECT id, nome_escola, tipo FROM escolas WHERE status = 1 ORDER BY nome_escola ASC";
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
        .seletor-escola { background: #f8f9fa; padding: 20px; border-radius: 10px; border: 1px solid #e0e0e0; margin-bottom: 20px; }
        .alerta-integral { background: #fff3cd; color: #856404; padding: 10px; border-radius: 5px; font-size: 13px; margin-bottom: 15px; border-left: 4px solid #ffeeba; }
        .btn-print { background: #34495e; color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; font-size: 14px; display: inline-flex; align-items: center; gap: 5px; }
        .btn-outline { border: 1px solid #ccc; background: transparent !important; color: #666; }
    </style>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
</head>
<body>

<div class="container" style="max-width: 700px;">

    <?php include '../includes/menu.php'; ?>

    <h2><i class="ph ph-magnifying-glass"></i> Planejamento de Rota</h2>

    <div class="seletor-escola">
        <form action="" method="GET" id="formRota" style="display: grid; gap: 15px;">
            <div class="form-group">
                <label>Para qual escola vamos agora?</label>
                <select name="id_escola" id="id_escola" onchange="atualizarInterface()" required>
                    <option value="">Selecione a Escola...</option>
                    <?php while($esc = $res_escolas->fetch_assoc()): ?>
                        <option value="<?= $esc['id'] ?>" 
                                data-tipo="<?= $esc['tipo'] ?>" 
                                <?= $escola_id == $esc['id'] ? 'selected' : '' ?>>
                            <?= $esc['nome_escola'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div id="secao-periodo" class="form-group" style="<?= $escola_id ? 'display:block' : 'display:none' ?>">
                <label id="label-periodo">Qual turno você vai fazer?</label>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" name="periodo" value="Manhã" id="btn-manha" class="btn-salvar <?= $periodo_filtro == 'Manhã' ? '' : 'btn-outline' ?>" style="flex:1; background: #2ecc71">
                        <i class="ph ph-sun"></i> Manhã
                    </button>
                    <button type="submit" name="periodo" value="Tarde" id="btn-tarde" class="btn-salvar <?= $periodo_filtro == 'Tarde' ? '' : 'btn-outline' ?>" style="flex:1; background: #e67e22">
                        <i class="ph ph-cloud-sun"></i> Tarde
                    </button>
                    <button type="submit" name="periodo" value="Integral" id="btn-integral" class="btn-salvar" style="flex:1; background: #9b59b6; display:none;">
                        <i class="ph ph-infinity"></i> Integral
                    </button>
                </div>
            </div>
        </form>
    </div>

    <?php if ($escola_id && $periodo_filtro): ?>
        <div style="margin-bottom: 15px; display: flex; justify-content: flex-end;">
            <a href="imprimir_rota.php?id_escola=<?= $escola_id ?>&periodo=<?= $periodo_filtro ?>" target="_blank" class="btn-print">
                <i class="ph ph-printer"></i> Imprimir Lista
            </a>
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
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center; padding:20px; color:#999;">Nenhum aluno para este turno nesta escola.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

<script>
function atualizarInterface() {
    const select = document.getElementById('id_escola');
    const opcao = select.options[select.selectedIndex];
    const tipo = opcao.getAttribute('data-tipo');
    
    const btnManha = document.getElementById('btn-manha');
    const btnTarde = document.getElementById('btn-tarde');
    const btnIntegral = document.getElementById('btn-integral');
    const secao = document.getElementById('secao-periodo');

    if (select.value === "") {
        secao.style.display = 'none';
        return;
    }

    secao.style.display = 'block';

    if (tipo === 'Integral') {
        // Esconde Manhã/Tarde e mostra apenas Integral
        btnManha.style.display = 'none';
        btnTarde.style.display = 'none';
        btnIntegral.style.display = 'block';
        document.getElementById('label-periodo').innerText = "Escola de Tempo Integral:";
    } else {
        // Mostra Manhã/Tarde e esconde Integral
        btnManha.style.display = 'block';
        btnTarde.style.display = 'block';
        btnIntegral.style.display = 'none';
        document.getElementById('label-periodo').innerText = "Qual turno você vai fazer?";
    }
}

// Executa ao carregar para manter o estado caso já tenha escola selecionada
window.onload = atualizarInterface;
</script>

</body>
</html>