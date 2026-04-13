<?php
require_once '../../backend/conexao.php';

$mes_atual = date('m');
$ano_atual = date('Y');

// 1. Pega o filtro de escola (se houver)
$escola_filtro = $_GET['id_escola'] ?? '';

// 2. Busca escolas para o filtro
$escolas = $conn->query("SELECT * FROM escolas WHERE status = 1 ORDER BY nome_escola ASC");

// 3. Query "Cerebral": Busca alunos ativos de escolas também ATIVAS
$sql = "SELECT a.*, e.nome_escola, p.id as id_pagamento
        FROM alunos a
        JOIN escolas e ON a.id_escola = e.id
        LEFT JOIN pagamentos p ON a.id = p.id_aluno 
             AND MONTH(p.data_confirmacao) = '$mes_atual' 
             AND YEAR(p.data_confirmacao) = '$ano_atual'
        WHERE a.status = 'Ativo' 
        AND e.status = 1"; // <-- Adicionamos essa linha aqui

if ($escola_filtro) {
    $sql .= " AND a.id_escola = " . intval($escola_filtro);
}

$sql .= " ORDER BY p.id ASC, a.nome_crianca ASC"; // Mostra quem NÃO pagou primeiro
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Financeiro - Sistema do Tio</title>
    <link rel="stylesheet" href="../../assets/css/estilo.css">
    <style>
        .filtro-escolas { display: flex; gap: 10px; overflow-x: auto; padding: 10px 0; margin-bottom: 20px; }
        .btn-filtro { padding: 8px 15px; border-radius: 20px; border: 1px solid #3498db; background: white; color: #3498db; cursor: pointer; text-decoration: none; white-space: nowrap; font-size: 14px; }
        .btn-filtro.active { background: #3498db; color: white; }
        .status-pago { color: #2ecc71; font-weight: bold; }
        .status-pendente { color: #e74c3c; font-weight: bold; }
        .card-financeiro { background: white; padding: 15px; border-radius: 8px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="container">

    <?php include '../includes/menu.php'; ?>

    <h2>💰 Controle de Pagamentos</h2>

    <div class="filtro-escolas">
        <a href="financeiro.php" class="btn-filtro <?= !$escola_filtro ? 'active' : '' ?>">Todas</a>
        <?php while($esc = $escolas->fetch_assoc()): ?>
            <a href="financeiro.php?id_escola=<?= $esc['id'] ?>" class="btn-filtro <?= $escola_filtro == $esc['id'] ? 'active' : '' ?>">
                <?= $esc['nome_escola'] ?>
            </a>
        <?php endwhile; ?>
    </div>

    <?php while($aluno = $result->fetch_assoc()): ?>
        <div class="card-financeiro" style="border-left: 5px solid <?= $aluno['id_pagamento'] ? '#2ecc71' : '#e74c3c' ?>;">
            <div>
                <strong><?= $aluno['nome_crianca'] ?></strong>
                <div style="font-size: 12px; color: #7f8c8d;">
                    <?= $aluno['nome_escola'] ?> | R$ <?= number_format($aluno['valor_mensalidade'], 2, ',', '.') ?>
                </div>
            </div>

            <div>
                <?php if($aluno['id_pagamento']): ?>
                    <span class="status-pago">✅ PAGO</span>
                <?php else: ?>
                    <button class="btn-salvar" style="margin:0; padding: 5px 10px; background: #e74c3c;" 
                            onclick="abrirModal(<?= $aluno['id'] ?>, '<?= $aluno['nome_crianca'] ?>', <?= $aluno['valor_mensalidade'] ?>)">
                        ❌ DAR BAIXA
                    </button>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<div id="modalPagamento" class="modal">
    <div class="modal-content">
        <h3>Confirmar Pagamento</h3>
        <p>Como o aluno <strong id="nomeAlunoModal"></strong> pagou?</p>
        
        <form action="../../backend/acoes/confirmar_pagamento.php" method="POST">
            <input type="hidden" name="id_aluno" id="idAlunoModal">
            
            <div class="opcoes-pagamento">
                <button type="submit" name="metodo" value="Pix" class="btn-metodo pix">Pix</button>
                <button type="submit" name="metodo" value="Dinheiro" class="btn-metodo dinheiro">Dinheiro</button>
                <button type="submit" name="metodo" value="Cartão" class="btn-metodo cartao">Cartão</button>
            </div>
            
            <button type="button" class="btn-cancelar" onclick="fecharModal()">Cancelar</button>
        </form>
    </div>
</div>

<script>
function abrirModal(id, nome) {
    document.getElementById('idAlunoModal').value = id;
    document.getElementById('nomeAlunoModal').innerText = nome;
    document.getElementById('modalPagamento').style.display = 'block';
}

function fecharModal() {
    document.getElementById('modalPagamento').style.display = 'none';
}

// Fecha o modal se clicar fora da caixa branca
window.onclick = function(event) {
    let modal = document.getElementById('modalPagamento');
    if (event.target == modal) {
        fecharModal();
    }
}
</script>

</body>
</html>