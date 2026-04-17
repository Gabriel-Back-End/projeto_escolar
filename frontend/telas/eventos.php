<?php
require_once '../../backend/conexao.php';

// Busca eventos e conta quantos participantes cada um tem
$sql = "SELECT e.*, 
        (SELECT COUNT(*) FROM evento_participantes WHERE id_evento = e.id) as total_alunos
        FROM eventos e 
        ORDER BY e.data_evento DESC";
$eventos = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Eventos e Passeios - Sistema do Tio</title>
    <link rel="stylesheet" href="../../assets/css/estilo.css">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        .grid-eventos { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-top: 20px; }
        .card-evento { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1); border: 1px solid #eee; }
        .card-evento img { width: 100%; height: 150px; object-fit: cover; }
        .conteudo-evento { padding: 15px; }
        .btn-novo-evento { background: #9b59b6; color: white; padding: 12px 20px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 20px; }
        .badge-valor { background: #e74c3c; color: white; padding: 3px 8px; border-radius: 5px; font-size: 12px; }
        .badge-valor {
    background: #e74c3c;
    color: white;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: bold;
    font-size: 12px;
}

.card-evento {
    transition: transform 0.2s;
}

.card-evento:hover {
    transform: translateY(-5px); /* Dá um efeito de flutuar ao passar o mouse */
}
    </style>
</head>
<body>

<div class="container">
    <?php include '../includes/menu.php'; ?>

    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2><i class="ph ph-ticket"></i> Passeios e Eventos</h2>
        <a href="novo_evento.php" class="btn-novo-evento"><i class="ph ph-plus-circle"></i> Criar Novo Passeio</a>
    </div>

    <div class="grid-eventos">
        
        <?php while($ev = $eventos->fetch_assoc()): ?>
            
            <div class="card-evento">
                <?php if($ev['imagem']): ?>
                    <img src="../../assets/img/eventos/<?= $ev['imagem'] ?>" alt="Capa">
                <?php else: ?>
                    <div style="height: 150px; background: #eee; display: flex; align-items: center; justify-content: center;"><i class="ph ph-image-square"></i></div>
                <?php endif; ?>

                <div class="conteudo-evento">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span class="badge-valor">R$ <?= number_format($ev['valor_passeio'], 2, ',', '.') ?></span>
                        
                        <span style="font-size: 11px; color: #666; font-weight: bold; background: #f1f2f6; padding: 4px 8px; border-radius: 20px; display: flex; align-items: center; gap: 4px;">
                            <i class="ph ph-users"></i> <?= $ev['total_alunos'] ?> confirmados
                        </span>
                    </div>

                    <h3 style="margin: 15px 0 5px 0; font-size: 18px;"><?= $ev['titulo'] ?></h3>
                    <p style="font-size: 13px; color: #666; margin-bottom: 15px;">
                        <i class="ph ph-calendar"></i> <?= date('d/m/Y', strtotime($ev['data_evento'])) ?>
                    </p>
                    
                    <a href="detalhes_evento.php?id=<?= $ev['id'] ?>" class="btn-salvar" style="display: block; text-align: center; text-decoration: none; background: #2ecc71; font-size: 13px;">
                        Gerenciar Alunos
                    </a>
                </div>
            </div>

        <?php endwhile; // 3. Aqui o loop termina e volta para pegar o próximo evento ?>
        
    </div> 
</div>

</body>
</html>