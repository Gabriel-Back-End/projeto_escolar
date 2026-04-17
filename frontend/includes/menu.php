<style>
    :root {
        --nav-bg: #2c3e50;
        --nav-hover: #1abc9c;
        --nav-text: #ecf0f1;
        --nav-accent: #9b59b6; /* Cor roxinha para Passeios */
    }

    .navbar {
        background-color: var(--nav-bg);
        padding: 0;
        margin-bottom: 30px;
        width: 100%;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        font-family: 'Segoe UI', Roboto, sans-serif;
    }

    .navbar-container {
        max-width: 1200px; /* Aumentei um pouco para caber o novo item */
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 15px;
        height: 70px;
    }

    .navbar-logo {
        color: var(--nav-hover);
        font-size: 18px;
        font-weight: 800;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    .navbar-links {
        display: flex;
        gap: 2px;
        height: 100%;
    }

    .navbar-links a {
        color: var(--nav-text);
        text-decoration: none;
        font-size: 12px; /* Diminuí levemente para não quebrar a linha */
        font-weight: 600;
        padding: 0 10px;
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
        height: 100%;
        text-align: center;
        gap: 5px;
    }

    .navbar-links a:hover {
        background-color: rgba(255,255,255,0.1);
        color: var(--nav-hover);
    }

    .navbar-links a.active {
        background-color: var(--nav-hover);
        color: white;
    }

    /* Destaque especial para Passeios */
    .link-eventos.active {
        background-color: var(--nav-accent) !important;
    }

    /* Ajuste para telas pequenas (Celular do Tio) */
    @media (max-width: 850px) {
        .navbar-container { flex-direction: column; height: auto; padding: 10px; }
        .navbar-logo { margin-bottom: 10px; }
        .navbar-links { 
            display: grid; 
            grid-template-columns: 1fr 1fr 1fr; /* 3 colunas no celular para caber os 6 itens */
            width: 100%; 
        }
        .navbar-links a { 
            padding: 10px 5px; 
            flex-direction: column; /* Ícone em cima do texto no celular */
            font-size: 11px;
            justify-content: center;
            border: 1px solid rgba(255,255,255,0.05);
            height: 60px;
        }
        .navbar-links a i { margin-right: 0 !important; font-size: 18px; }
    }
</style>

<?php
$pagina_atual = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar">
    <div class="navbar-container">
        
        <a href="dashboard.php" class="navbar-logo">
            <i class="ph ph-van"></i> SISTEMA DO TIO
        </a>
        
        <div class="navbar-links">

            <a href="dashboard.php" class="<?= ($pagina_atual == 'dashboard.php') ? 'active' : '' ?>">
                <i class="ph ph-house-line"></i> Início
            </a>

            <a href="financeiro.php" class="<?= ($pagina_atual == 'financeiro.php') ? 'active' : '' ?>">
                <i class="ph ph-money"></i> Financeiro
            </a>
            
            <a href="gerenciar_alunos.php" class="<?= ($pagina_atual == 'gerenciar_alunos.php' || $pagina_atual == 'cadastro_alunos.php' || $pagina_atual == 'editar_aluno.php') ? 'active' : '' ?>">
                <i class="ph ph-users"></i> Alunos
            </a>
            
            <a href="eventos.php" class="link-eventos <?= ($pagina_atual == 'eventos.php' || $pagina_atual == 'detalhes_evento.php' || $pagina_atual == 'novo_evento.php') ? 'active' : '' ?>">
                <i class="ph ph-ticket"></i> Passeios
            </a>

            <a href="rota.php" class="<?= ($pagina_atual == 'rota.php' || $pagina_atual == 'configurar_rota.php') ? 'active' : '' ?>">
                <i class="ph ph-map-trifold"></i> Rota
            </a>
            
            <a href="gerenciar_escolas.php" class="<?= ($pagina_atual == 'gerenciar_escolas.php') ? 'active' : '' ?>">
                <i class="ph ph-backpack"></i> Escolas
            </a>
        </div>
    </div>
</nav>