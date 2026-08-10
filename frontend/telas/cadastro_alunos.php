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
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
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

        <div class="form-row">
            <div class="form-group">
                <label for="endereco">Endereço:</label>
                <textarea id="endereco" name="endereco" rows="1" placeholder="Rua, número"></textarea>
            </div>
            <div class="form-group">
                <label for="bairro">Bairro:</label>
                <input type="text" id="bairro" name="bairro" placeholder="Ex: Bela Vista">
            </div>
        </div>

        <div class="section-title">Financeiro</div>
            
            <!-- Select de Opções -->
            <label for="tipoMensalidade">Tipo de Mensalidade:</label>
            <select id="tipoMensalidade" name="tipo_mensalidade" required>
                <option value="">-- Selecione uma opção --</option>
                <option value="unica">Mensalidade Única</option>
                <option value="compartilhada">Mensalidade Compartilhada</option>
            </select>

            <!-- Campos visíveis apenas para Mensalidade Única -->
            <div class="form-row" id="camposUnica" style="display: none; margin-top: 15px;">
                <div class="form-group">
                    <label for="valor_mensalidade">Valor Mensal (R$):</label>
                    <input type="number" id="valor_mensalidade" name="valor_mensalidade" step="0.01" placeholder="0.00">
                </div>
                <div class="form-group">
                    <label for="dia_vencimento">Dia de Vencimento:</label>
                    <input type="number" id="dia_vencimento" name="dia_vencimento" min="1" max="31" placeholder="1 a 31">
                </div>
            </div>

            <!-- Campos visíveis apenas para Mensalidade Compartilhada -->
            <div class="form-row" id="camposCompartilhada" style="display: none; margin-top: 15px;">
                <label for="quemCompartilhar">Quem irá compartilhar:</label>
                <input type="number" name="quem_compartilhar" min="2" placeholder="Ex: 2">
                <br><br>
                <label for="novo_valor">Qual será o Valor da Nova Mensalidade:</label>
                <input type="number" name="novo_valor" step="0.01" placeholder="De 125,00 para ...">
            </div>
        <button type="submit" class="btn-salvar">Cadastrar Aluno na Perua</button>
    </form>
</div>
<script>
  const selectTipo = document.getElementById('tipoMensalidade');
    const camposUnica = document.getElementById('camposUnica');
    const camposCompartilhada = document.getElementById('camposCompartilhada');

    // Seleciona os campos que mudam de exigência
    const valorMensalidade = document.getElementById('valor_mensalidade');
    const diaVencimento = document.getElementById('dia_vencimento');

    selectTipo.addEventListener('change', function() {
    // Esconde ambos os blocos
    camposUnica.style.display = 'none';
    camposCompartilhada.style.display = 'none';

    // Remove a obrigatoriedade dos campos ocultos
    valorMensalidade.removeAttribute('required');
    diaVencimento.removeAttribute('required');

    // Aplica a exibição e o 'required' correto para cada opção
    if (this.value === 'unica') {
        camposUnica.style.display = 'flex'; // Mantém o layout de linha (form-row)
        valorMensalidade.setAttribute('required', 'true');
        diaVencimento.setAttribute('required', 'true');
    } else if (this.value === 'compartilhada') {
        camposCompartilhada.style.display = 'block';
    }
    });
</script>
</body>
</html>