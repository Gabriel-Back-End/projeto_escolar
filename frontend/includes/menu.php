<style>
    :root {
        --nav-bg: #2c3e50;
        --nav-hover: #1abc9c;
        --nav-text: #ecf0f1;
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
        max-width: 1100px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 20px;
        height: 70px;
    }

    .navbar-logo {
        color: var(--nav-hover);
        font-size: 20px;
        font-weight: 800;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 10px;
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
        font-size: 13px;
        font-weight: 600;
        padding: 0 12px;
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
        height: 100%;
        text-align: center;
    }

    .navbar-links a:hover {
        background-color: rgba(255,255,255,0.1);
        color: var(--nav-hover);
    }

    .navbar-links a.active {
        background-color: var(--nav-hover);
        color: white;
    }

    /* Ajuste para telas pequenas (Celular do Tio) */
    @media (max-width: 768px) {
        .navbar-container { flex-direction: column; height: auto; padding: 10px; }
        .navbar-links { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            width: 100%; 
            margin-top: 10px;
        }
        .navbar-links a { 
            padding: 12px; 
            justify-content: center;
            border: 1px solid rgba(255,255,255,0.05);
        }
    }
</style>

<?php
$pagina_atual = basename($_SERVER['PHP_SELF']);
?>

<nav class="navbar">
    <div class="navbar-container">
        
        <a href="financeiro.php" class="navbar-logo">
            <span>🚐</span> SISTEMA DO TIO
        </a>
        
        <div class="navbar-links">

            <a href="dashboard.php" class="<?= ($pagina_atual == 'dashboard.php') ? 'active' : '' ?>">
                🏠 Início
            </a>

            <a href="financeiro.php" class="<?= ($pagina_atual == 'financeiro.php') ? 'active' : '' ?>">
                💰 Financeiro
            </a>
            
            <a href="gerenciar_alunos.php" class="<?= ($pagina_atual == 'gerenciar_alunos.php' || $pagina_atual == 'cadastro_alunos.php' || $pagina_atual == 'editar_aluno.php') ? 'active' : '' ?>">
                👥 Alunos
            </a>
            
            <a href="rota.php" class="<?= ($pagina_atual == 'rota.php' || $pagina_atual == 'configurar_rota.php') ? 'active' : '' ?>">
                📍 Rota
            </a>
            
            <a href="gerenciar_escolas.php" class="<?= ($pagina_atual == 'gerenciar_escolas.php') ? 'active' : '' ?>">
                🏫 Escolas
            </a>
        </div>
    </div>
</nav>