<?php
require_once '../../backend/conexao.php';

$id_evento = $_GET['id'];

// Busca dados do evento
$evento = $conn->query("SELECT * FROM eventos WHERE id = $id_evento")->fetch_assoc();

// Cálculos Financeiros
$res_total = $conn->query("SELECT SUM(e.valor_passeio) as total 
                           FROM evento_participantes p 
                           JOIN eventos e ON p.id_evento = e.id 
                           WHERE p.id_evento = $id_evento AND p.pago = 'Sim'");
$arrecadado = $res_total->fetch_assoc()['total'] ?? 0;

// Alunos Disponíveis
$alunos_disponiveis = $conn->query("SELECT * FROM alunos WHERE status = 'Ativo' AND id NOT IN (SELECT id_aluno FROM evento_participantes WHERE id_evento = $id_evento) ORDER BY nome_crianca ASC");

// Participantes Ordenados
$participantes = $conn->query("SELECT p.*, a.nome_crianca FROM evento_participantes p JOIN alunos a ON p.id_aluno = a.id WHERE p.id_evento = $id_evento ORDER BY p.ordem_passeio ASC, a.nome_crianca ASC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciar Passeio - <?= $evento['titulo'] ?></title>
    <link rel="stylesheet" href="../../assets/css/estilo.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        /* Ajustes de Layout Principal */
        .container { max-width: 900px; margin: 0 auto; padding: 20px; }
        
        .header-evento { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
        
        .resumo-cards { display: flex; gap: 15px; margin-bottom: 25px; }
        .card-mini { 
            background: white; padding: 15px; border-radius: 10px; flex: 1; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.05); border-top: 4px solid #3498db;
            text-align: center; 
        }

        /* Formulário de Adição */
        .form-adicionar { background: #f8f9fa; padding: 20px; border-radius: 10px; border: 1px dashed #cbd5e0; margin-bottom: 30px; }
        .row-adicionar { display: flex; gap: 10px; align-items: center; }
        .row-adicionar select { flex: 1; padding: 12px; border-radius: 8px; border: 1px solid #ddd; height: 45px; }
        .btn-add { background: #2ecc71; color: white; border: none; padding: 0 20px; border-radius: 8px; font-weight: bold; height: 45px; cursor: pointer; white-space: nowrap; }

        /* Estilização da Lista (Onde estava quebrando) */
        .participante-card { 
            background: white; border: 1px solid #edf2f7; border-radius: 10px; 
            padding: 15px; margin-bottom: 12px; display: flex; align-items: center; 
            gap: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        
        .ordem-box { display: flex; align-items: center; gap: 5px; }
        .ordem-input {
            width: 50px; height: 35px; text-align: center; border-radius: 6px;
            border: 2px solid #e2e8f0; font-weight: bold; font-size: 14px;
        }

        .aluno-info { flex: 1; min-width: 150px; }
        .aluno-nome { font-weight: 700; color: #2d3748; display: block; margin-bottom: 4px; }
        
        .status-badge { font-size: 11px; font-weight: bold; padding: 3px 10px; border-radius: 5px; text-transform: uppercase; display: inline-flex; align-items: center; gap: 5px; }
        .status-pendente { background: #fff5f5; color: #e53e3e; }
        .status-pago { background: #f0fff4; color: #38a169; }

        .acoes-pagamento { display: flex; align-items: center; gap: 10px; }
        .form-baixa { display: flex; gap: 5px; }
        .form-baixa select { padding: 8px; border-radius: 6px; border: 1px solid #ddd; font-size: 13px; background: #fff; }
        .btn-baixa { background: #38a169; color: white; border: none; padding: 8px 15px; border-radius: 6px; font-size: 12px; font-weight: bold; cursor: pointer; }

        .btn-trash { color: #e74c3c; background: none; border: none; cursor: pointer; font-size: 20px; display: flex; align-items: center; }

        /* Responsividade para Celular */
        @media (max-width: 768px) {
            .participante-card { flex-direction: column; align-items: flex-start; }
            .acoes-pagamento { width: 100%; justify-content: space-between; border-top: 1px solid #eee; padding-top: 10px; }
            .ordem-box { margin-bottom: 10px; }
        }
    </style>
</head>
<body>

<div class="container">
    <div style="margin-bottom: 15px;">
        <a href="eventos.php" style="text-decoration: none; color: #666; font-size: 14px;"><i class="ph ph-arrow-left"></i> Voltar para Passeios</a>
    </div>

    <div class="header-evento">
        <h2 style="margin:0;">Passeio: <span style="color: #2c3e50;"><?= $evento['titulo'] ?></span></h2>
        <a href="imprimir_passeio.php?id=<?= $id_evento ?>" target="_blank" class="btn-add" style="background: #34495e; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
            <i class="ph ph-printer"></i> Imprimir Lista
        </a>
    </div>

    <div class="resumo-cards">
        <div class="card-mini" style="border-color: #9b59b6;">
            <small style="color: #888; font-weight: bold; text-transform: uppercase; font-size: 10px;">Confirmados</small>
            <div style="font-size: 22px; font-weight: 800;"><?= $participantes->num_rows ?></div>
        </div>
    </div>
        
    <div class="form-adicionar">
        <form action="../../backend/acoes/adicionar_aluno_evento.php" method="POST">
            <input type="hidden" name="id_evento" value="<?= $id_evento ?>">
            <label style="font-size: 14px; margin-bottom: 10px; display: block;">Adicionar criança ao passeio:</label>
            <div class="row-adicionar">
                <select name="id_aluno" required>
                    <option value="">Selecione um aluno da lista...</option>
                    <?php while($al = $alunos_disponiveis->fetch_assoc()): ?>
                        <option value="<?= $al['id'] ?>"><?= $al['nome_crianca'] ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="btn-add">Adicionar Aluno</button>
            </div>
        </form>
    </div>

    <div class="lista-confirmados">
        <h3 style="font-size: 16px; color: #2c3e50; margin-bottom: 15px;"><i class="ph ph-users-three"></i> Lista de Embarque e Pagamentos</h3>

        <?php while($p = $participantes->fetch_assoc()): ?>
            <div class="participante-card" style="border-left: 6px solid <?= $p['pago'] == 'Sim' ? '#2ecc71' : '#e74c3c' ?>;">
                
                <div class="ordem-box">
                    <form action="../../backend/acoes/salvar_ordem_passeio.php" method="POST">
                        <input type="hidden" name="id_participacao" value="<?= $p['id'] ?>">
                        <input type="hidden" name="id_evento" value="<?= $id_evento ?>">
                        <input type="number" name="ordem" value="<?= $p['ordem_passeio'] ?>" onchange="this.form.submit()" class="ordem-input">
                    </form>
                </div>

                <div class="aluno-info">
                    <span class="aluno-nome"><?= $p['nome_crianca'] ?></span>
                    <?php if($p['pago'] == 'Sim'): ?>
                        <span class="status-badge status-pago"><i class="ph ph-check-circle"></i> Pago (<?= $p['metodo_pagamento'] ?>)</span>
                    <?php else: ?>
                        <span class="status-badge status-pendente"><i class="ph ph-clock"></i> Pagamento Pendente</span>
                    <?php endif; ?>
                </div>

                <div class="acoes-pagamento">
                    <?php if($p['pago'] == 'Não'): ?>
                        <form action="../../backend/acoes/pagar_evento.php" method="POST" class="form-baixa">
                            <input type="hidden" name="id_participacao" value="<?= $p['id'] ?>">
                            <input type="hidden" name="id_evento" value="<?= $id_evento ?>">
                            <select name="metodo" required>
                                <option value="Pix">Pix</option>
                                <option value="Dinheiro">Dinheiro</option>
                            </select>
                            <button type="submit" class="btn-baixa">Dar Baixa</button>
                        </form>

                        <a href="../../backend/acoes/remover_aluno_evento.php?id_participacao=<?= $p['id'] ?>&id_evento=<?= $id_evento ?>" 
                           onclick="return confirm('Deseja realmente remover <?= $p['nome_crianca'] ?> do passeio?')" class="btn-trash">
                            <i class="ph ph-trash"></i>
                        </a>
                    <?php endif; ?>
                </div>

            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>