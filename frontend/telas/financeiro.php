<?php
require_once '../../backend/conexao.php';

$mes_atual = date('m');
$ano_atual = date('Y');

$escola_filtro = $_GET['id_escola'] ?? '';
$escolas = $conn->query("SELECT * FROM escolas WHERE status = 1 ORDER BY nome_escola ASC");

$sql = "SELECT m.*, a.nome_crianca, a.valor_mensalidade, e.nome_escola 
        FROM mensalidades m
        JOIN alunos a ON m.id_aluno = a.id
        JOIN escolas e ON a.id_escola = e.id
        WHERE (m.status = 'Pendente' OR (m.mes = '$mes_atual' AND m.ano = '$ano_atual'))";

if ($escola_filtro) {
    $sql .= " AND a.id_escola = " . intval($escola_filtro);
}

// MUDANÇA AQUI: Ordenamos para que 'Pendente' apareça antes de 'Pago'
// E quem deve meses mais antigos (ex: Março) apareça antes de Abril.
$sql .= " ORDER BY m.status ASC, m.ano ASC, m.mes ASC, a.nome_crianca ASC";

$result = $conn->query($sql);

if (!$result) {
    die("Erro na query: " . $conn->error);
}
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

    <?php while($item = $result->fetch_assoc()): ?>
        <div class="card-financeiro" style="border-left: 5px solid <?= $item['status'] == 'Pago' ? '#2ecc71' : '#e74c3c' ?>;">
            <div>
                <strong><?= $item['nome_crianca'] ?></strong>
                <div style="font-size: 12px; color: #7f8c8d;">
                    Ref: <?= str_pad($item['mes'], 2, '0', STR_PAD_LEFT) ?>/<?= $item['ano'] ?> | 
                    <?= $item['nome_escola'] ?> | 
                    R$ <?= number_format($item['valor_devido'], 2, ',', '.') ?>
                </div>
            </div>

            <div>
                <?php if($item['status'] == 'Pago'): ?>
                    <span class="status-pago">✅ PAGO (<?= $item['metodo_pagamento'] ?>)</span>
                <?php else: ?>
                    <button class="btn-salvar" style="margin:0; padding: 5px 10px; background: #e74c3c;" 
                            onclick="abrirModal(<?= $item['id'] ?>, '<?= $item['nome_crianca'] ?>', '<?= $item['mes'] ?>/<?= $item['ano'] ?>')">
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
            <input type="hidden" name="id_mensalidade" id="idMensalidadeModal">
            
            <div class="opcoes-pagamento">
                <button type="submit" name="metodo" value="Pix" class="btn-metodo pix">Pix</button>
                <button type="submit" name="metodo" value="Dinheiro" class="btn-metodo dinheiro">Dinheiro</button>
                <button type="submit" name="metodo" value="Cartão" class="btn-metodo cartao">Cartão</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModal(idMensalidade, nomeAluno, referencia) {
    // 1. Coloca o ID da mensalidade no campo escondido do formulário
    document.getElementById('idMensalidadeModal').value = idMensalidade;
    
    // 2. Atualiza o texto do modal para o Tio saber o que está fazendo
    // Vamos mostrar: "João Silva (Ref: 04/2026)"
    document.getElementById('nomeAlunoModal').innerText = nomeAluno + " (Ref: " + referencia + ")";
    
    // 3. Exibe o modal
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