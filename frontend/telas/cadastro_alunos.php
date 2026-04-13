<?php
// Inclui a conexão que você criou
require_once '../../backend/conexao.php'; 

// Busca as escolas ativas para o Select
$sql_escolas = "SELECT id, nome_escola FROM escolas WHERE status = 1 ORDER BY nome_escola ASC";
$result_escolas = $conn->query($sql_escolas);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Aluno - Sistema do Tio</title>
    <link rel="stylesheet" href="../../assets/css/estilo.css">
</head>
<body>

<div class="container">
    <?php include '../includes/menu.php'; ?>
    <h2>Novo Cadastro de Aluno</h2>
    
    <?php if (isset($_GET['sucesso'])): ?>
        <div class="alerta-sucesso">
            ✅ Aluno cadastrado e mensalidade de <?php echo date('m/Y'); ?> gerada!
        </div>
    <?php endif; ?>

    <form action="../../backend/acoes/salvar_aluno.php" method="POST">
        
        <div class="section-title">Dados do Aluno e Responsável</div>
        
        <div class="form-group">
            <label for="nome_crianca">Nome da Criança:</label>
            <input type="text" id="nome_crianca" name="nome_crianca" required placeholder="Nome completo da criança">
        </div>

        <div class="form-group">
            <label for="nome_responsavel">Pai / Mãe / Responsável:</label>
            <input type="text" id="nome_responsavel" name="nome_responsavel" required placeholder="Nome de quem o tio deve cobrar">
        </div>

        <div class="form-group">
            <label for="telefone_responsavel">Telefone do Responsável (WhatsApp):</label>
            <input type="text" id="telefone_responsavel" name="telefone_responsavel" placeholder="(00) 00000-0000">
        </div>

        <div class="section-title">Logística (Rota)</div>

        <div class="form-group">
            <label for="id_escola">Escola:</label>
            <select name="id_escola" id="id_escola" required>
                <option value="">Selecione uma escola ativa...</option>
                <?php while ($escola = $result_escolas->fetch_assoc()): ?>
                    <option value="<?php echo $escola['id']; ?>">
                        <?php echo htmlspecialchars($escola['nome_escola']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="periodo">Período:</label>
                <select name="periodo" id="periodo" required>
                    <option value="Manhã">Manhã</option>
                    <option value="Tarde">Tarde</option>
                    <option value="Integral">Integral</option>
                </select>
            </div>
            <div class="form-group">
                <label for="serie">Série:</label>
                <input type="text" id="serie" name="serie" placeholder="Ex: 3º ano B">
            </div>
        </div>

        <div class="form-group">
            <label for="endereco">Endereço:</label>
            <textarea id="endereco" name="endereco" rows="2" placeholder="Rua, número, bairro..."></textarea>
        </div>

        <div class="section-title">Financeiro</div>

        <div class="form-row">
            <div class="form-group">
                <label for="valor_mensalidade">Valor Mensal (R$):</label>
                <input type="number" id="valor_mensalidade" name="valor_mensalidade" step="0.01" required placeholder="0.00">
            </div>
            <div class="form-group">
                <label for="dia_vencimento">Dia de Vencimento:</label>
                <input type="number" id="dia_vencimento" name="dia_vencimento" min="1" max="31" required placeholder="1 a 31">
            </div>
        </div>

        <button type="submit" class="btn-salvar">Cadastrar Aluno na Perua</button>
    </form>
</div>

</body>
</html>