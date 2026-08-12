<?php
require_once '../../backend/conexao.php';

$mes_atual = date('m');
$ano_atual = date('Y');

$escola_filtro = $_GET['id_escola'] ?? '';
$escolas = $conn->query("SELECT * FROM escolas WHERE status = 1 ORDER BY nome_escola ASC");

// Mantemos a query detalhada para coletar todas as parcelas individuais
$sql = "SELECT 
    m.*, 
    a.nome_crianca, 
    a.id AS id_aluno, 
    a.valor_mensalidade, 
    e.nome_escola,
    titular.nome_crianca AS nome_aluno_titular
FROM mensalidades m
JOIN alunos a ON m.id_aluno = a.id
JOIN escolas e ON a.id_escola = e.id
LEFT JOIN alunos titular ON a.aluno_titular_id = titular.id
WHERE (a.paga_mensalidade = 1 OR titular.paga_mensalidade = 1)
  AND (m.status = 'Pendente' OR (m.mes = '$mes_atual' AND m.ano = '$ano_atual'))";

if ($escola_filtro) {
    $sql .= " AND a.id_escola = " . intval($escola_filtro);
}

$sql .= " ORDER BY a.nome_crianca ASC, m.ano ASC, m.mes ASC";
$result = $conn->query($sql);

if (!$result) {
    die("Erro na query: " . $conn->error);
}

// LÓGICA DE AGRUPAMENTO: Transformamos as linhas repetidas em um array único por Aluno
$alunos_financeiro = [];
while ($row = $result->fetch_assoc()) {
    $id_aluno = $row['id_aluno'];
    
    // Se o aluno ainda não foi adicionado ao array, criamos a estrutura dele
    if (!isset($alunos_financeiro[$id_aluno])) {
        $alunos_financeiro[$id_aluno] = [
            'nome_crianca' => $row['nome_crianca'] . "|" .$row['nome_aluno_titular'],
            'nome_escola'  => $row['nome_escola'],
            'status_geral' => 'Pago', // Padrão inicial assumido
            'total_pendente' => 0,
            'meses_pendentes' => []
        ];
    }
    
    // Se houver QUALQUER mensalidade pendente, o status do card muda para Pendente
    if ($row['status'] == 'Pendente') {
        $alunos_financeiro[$id_aluno]['status_geral'] = 'Pendente';
        $alunos_financeiro[$id_aluno]['total_pendente'] += $row['valor_devido'];
        
        // Guardamos os dados de cada mês atrasado para usar no modal depois
        $alunos_financeiro[$id_aluno]['meses_pendentes'][] = [
            'id_mensalidade' => $row['id'],
            'mes' => str_pad($row['mes'], 2, '0', STR_PAD_LEFT),
            'ano' => $row['ano'],
            'valor' => $row['valor_devido']
        ];
    }
}

uasort($alunos_financeiro, function($a, $b) {
    // 1. Se o status geral for diferente, quem é 'Pendente' vem primeiro (-1) e 'Pago' vai para o final (1)
    if ($a['status_geral'] !== $b['status_geral']) {
        return ($a['status_geral'] === 'Pendente') ? -1 : 1;
    }
    
    // 2. Se ambos tiverem o mesmo status, organiza por ordem alfabética do nome da criança
    return strcasecmp($a['nome_crianca'], $b['nome_crianca']);
});

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Financeiro - Sistema do Tio</title>
    <link rel="stylesheet" href="../../assets/css/estilo.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .filtro-escolas { display: flex; gap: 10px; overflow-x: auto; padding: 10px 0; margin-bottom: 20px; }
        .btn-filtro { padding: 8px 15px; border-radius: 20px; border: 1px solid #3498db; background: white; color: #3498db; cursor: pointer; text-decoration: none; white-space: nowrap; font-size: 14px; }
        .btn-filtro.active { background: #3498db; color: white; }
        .status-pago { color: #2ecc71; font-weight: bold; }
        .status-pendente { color: #e74c3c; font-weight: bold; }
        .card-financeiro { background: white; padding: 15px; border-radius: 8px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        
        /* Ajustes do Modal para os Checkboxes */
        .checkbox-group { margin: 15px 0; background: #f8f9fa; padding: 10px; border-radius: 6px; text-align: left; max-height: 150px; overflow-y: auto; }
        .checkbox-item { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; font-size: 14px; cursor: pointer; }
        .checkbox-item input { width: 18px; height: 18px; cursor: pointer; }
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

    <?php foreach($alunos_financeiro as $id_aluno => $dados): ?>
        <?php 
            $qtd_meses = count($dados['meses_pendentes']); 
            $cor_borda = $dados['status_geral'] == 'Pago' ? '#2ecc71' : '#e74c3c';
        ?>
        <div class="card-financeiro" style="border-left: 5px solid <?= $cor_borda ?>;">
            <div>
                <strong><?= $dados['nome_crianca'] ?></strong>
                <div style="font-size: 12px; color: #7f8c8d;">
                    <?= $dados['nome_escola'] ?> | 
                    <?php if($dados['status_geral'] == 'Pago'): ?>
                        <span style="color: #2ecc71;">Mês atual está em dia</span>
                    <?php else: ?>
                        <span style="color: #e74c3c; font-weight: bold;">
                            <?= $qtd_meses ?> <?= $qtd_meses > 1 ? 'meses atrasados' : 'mês atrasado' ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <?php if($dados['status_geral'] == 'Pago'): ?>
                    <span class="status-pago">✅ EM DIA</span>
                <?php else: ?>
                    <button class="btn-salvar" style="margin:0; padding: 5px 10px; background: #e74c3c;" 
                            data-nome="<?= htmlspecialchars($dados['nome_crianca']) ?>"
                            data-meses='<?= json_encode($dados['meses_pendentes']) ?>'
                            onclick="abrirModal(this)">
                        ❌ DAR BAIXA
                    </button>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div id="modalPagamento" class="modal">
    <div class="modal-content">
        <h3>Confirmar Pagamento</h3>
        <p>Selecione quais mensalidades de <strong id="nomeAlunoModal"></strong> o Tio vai dar baixa:</p>
        
        <form action="../../backend/acoes/confirmar_pagamento.php" method="POST">
            
            <div class="checkbox-group" id="listaMesesModal"></div>
            
            <p style="font-size: 14px; margin-top: 15px; font-weight: bold;">Qual foi a forma de pagamento?</p>
            <div class="opcoes-pagamento">
                <button type="submit" name="metodo" value="Pix" class="btn-metodo pix">Pix</button>
                <button type="submit" name="metodo" value="Dinheiro" class="btn-metodo dinheiro">Dinheiro</button>
                <button type="submit" name="metodo" value="Cartão" class="btn-metodo cartao">Cartão</button>
            </div>
        </form>
    </div>
</div>

<script>
function abrirModal(botao) {
    let nomeAluno = botao.getAttribute('data-nome');
    let meses = JSON.parse(botao.getAttribute('data-meses'));
    
    document.getElementById('nomeAlunoModal').innerText = nomeAluno;
    
    let containerLista = document.getElementById('listaMesesModal');
    containerLista.innerHTML = ''; 
    
    meses.forEach(function(item) {
        let valorFormatado = parseFloat(item.valor).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        
        let label = document.createElement('label');
        label.className = 'checkbox-item';
        
        // O input agora vem sem o "checked", exigindo que o usuário clique para selecionar
        label.innerHTML = `
            <input type="checkbox" name="ids_mensalidades[]" value="${item.id_mensalidade}">
            <span>Mês: ${item.mes}/${item.ano} - <strong>${valorFormatado}</strong></span>
        `;
        containerLista.appendChild(label);
    });
    
    document.getElementById('modalPagamento').style.display = 'block';
}

function fecharModal() {
    document.getElementById('modalPagamento').style.display = 'none';
}

window.onclick = function(event) {
    let modal = document.getElementById('modalPagamento');
    if (event.target == modal) {
        fecharModal();
    }
}
</script>

</body>
</html>