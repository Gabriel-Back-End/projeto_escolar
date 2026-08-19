<?php
require_once '../../backend/conexao.php';

$mes_atual = date('m');
$ano_atual = date('Y');

$escola_filtro = isset($_GET['id_escola']) ? intval($_GET['id_escola']) : 0;
$escolas = $conn->query("SELECT * FROM escolas WHERE status = 1 ORDER BY nome_escola ASC");

// 1. CONSULTA SQL: Puxa apenas quem efetivamente paga mensalidade (paga_mensalidade = 1)
$sql = "SELECT 
    m.id AS id_mensalidade, 
    m.mes, 
    m.ano, 
    m.valor_devido, 
    m.status,
    a.id AS id_aluno, 
    a.nome_crianca AS nome_titular, 
    e.nome_escola,
    (
        SELECT GROUP_CONCAT(dep.nome_crianca SEPARATOR ' | ') 
        FROM alunos dep 
        WHERE dep.aluno_titular_id = a.id
    ) AS dependentes
FROM mensalidades m
INNER JOIN alunos a ON m.id_aluno = a.id
INNER JOIN escolas e ON a.id_escola = e.id
WHERE a.paga_mensalidade = 1
  AND (m.status = 'Pendente' OR (m.mes = '$mes_atual' AND m.ano = '$ano_atual'))";

if ($escola_filtro > 0) {
    $sql .= " AND a.id_escola = " . $escola_filtro;
}

$sql .= " ORDER BY a.nome_crianca ASC, m.ano ASC, m.mes ASC";
$result = $conn->query($sql);

if (!$result) {
    die("Erro na query: " . $conn->error);
}

// 2. MONTAGEM DA LISTA
$alunos_financeiro = [];

while ($row = $result->fetch_assoc()) {
    $id_aluno = $row['id_aluno'];
    
    // Formata a exibição: "Nome Titular | Nome Dependente"
    $nome_exibicao = $row['nome_titular'];
    if (!empty($row['dependentes'])) {
        $nome_exibicao .= " | " . $row['dependentes'];
    }

    if (!isset($alunos_financeiro[$id_aluno])) {
        $alunos_financeiro[$id_aluno] = [
            'nome_crianca'    => $nome_exibicao,
            'nome_escola'     => $row['nome_escola'],
            'status_geral'    => 'Pago',
            'total_pendente'  => 0,
            'meses_pendentes' => []
        ];
    }

    if ($row['status'] === 'Pendente') {
        $alunos_financeiro[$id_aluno]['status_geral'] = 'Pendente';
        $alunos_financeiro[$id_aluno]['total_pendente'] += $row['valor_devido'];
        
        $alunos_financeiro[$id_aluno]['meses_pendentes'][] = [
            'id_mensalidade' => $row['id_mensalidade'],
            'mes'            => str_pad($row['mes'], 2, '0', STR_PAD_LEFT),
            'ano'            => $row['ano'],
            'valor'          => $row['valor_devido']
        ];
    }
}

// 3. ORDENAÇÃO: Pendentes primeiro, depois ordem alfabética
uasort($alunos_financeiro, function($a, $b) {
    if ($a['status_geral'] !== $b['status_geral']) {
        return ($a['status_geral'] === 'Pendente') ? -1 : 1;
    }
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
        <a href="financeiro.php" class="btn-filtro <?= $escola_filtro === 0 ? 'active' : '' ?>">Todas</a>
        <?php while($esc = $escolas->fetch_assoc()): ?>
            <a href="financeiro.php?id_escola=<?= $esc['id'] ?>" class="btn-filtro <?= $escola_filtro == $esc['id'] ? 'active' : '' ?>">
                <?= htmlspecialchars($esc['nome_escola']) ?>
            </a>
        <?php endwhile; ?>
    </div>

    <?php if (empty($alunos_financeiro)): ?>
        <p>Nenhum registro financeiro encontrado.</p>
    <?php else: ?>
        <?php foreach($alunos_financeiro as $id_titular => $dados): ?>
            <?php 
                $qtd_meses = count($dados['meses_pendentes']); 
                $cor_borda = $dados['status_geral'] == 'Pago' ? '#2ecc71' : '#e74c3c';
            ?>
            <div class="card-financeiro" style="border-left: 5px solid <?= $cor_borda ?>;">
                <div>
                    <strong><?= htmlspecialchars($dados['nome_crianca']) ?></strong>
                    <div style="font-size: 12px; color: #7f8c8d;">
                        <?= htmlspecialchars($dados['nome_escola']) ?> | 
                        <?php if($dados['status_geral'] == 'Pago'): ?>
                            <span style="color: #2ecc71;">Mês atual está em dia</span>
                        <?php else: ?>
                            <span style="color: #e74c3c; font-weight: bold;">
                                <?= $qtd_meses ?> <?= $qtd_meses > 1 ? 'meses atrasados' : 'mês atrasado' ?> 
                                (Total: R$ <?= number_format($dados['total_pendente'], 2, ',', '.') ?>)
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <?php if($dados['status_geral'] == 'Pago'): ?>
                        <span class="status-pago">✅ EM DIA</span>
                    <?php else: ?>
                        <button class="btn-salvar" style="margin:0; padding: 5px 10px; background: #e74c3c; color: #fff; border: none; border-radius: 4px; cursor: pointer;" 
                                data-nome="<?= htmlspecialchars($dados['nome_crianca'], ENT_QUOTES) ?>"
                                data-meses='<?= json_encode($dados['meses_pendentes']) ?>'
                                onclick="abrirModal(this)">
                            ❌ DAR BAIXA
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div id="modalPagamento" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5);">
    <div class="modal-content" style="background:#fff; margin:10% auto; padding:20px; width:90%; max-width:400px; border-radius:8px;">
        <h3>Confirmar Pagamento</h3>
        <p>Selecione quais mensalidades de <strong id="nomeAlunoModal"></strong> o Tio vai dar baixa:</p>
        
        <form action="../../backend/acoes/confirmar_pagamento.php" method="POST">
            
            <div class="checkbox-group" id="listaMesesModal"></div>
            
            <p style="font-size: 14px; margin-top: 15px; font-weight: bold;">Qual foi a forma de pagamento?</p>
            <div class="opcoes-pagamento" style="display:flex; gap:10px; justify-content:space-between;">
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