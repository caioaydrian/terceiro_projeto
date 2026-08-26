<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['usuario_autenticado'])) {
    header('Location: ../index.php?paginas=login');
    exit;
}
?>

<main class="dashboard-page">
    <section class="dashboard-heading">
        <div>
            <span class="login-eyebrow">Admin Panel</span>
            <h1>Hello, Admin.</h1>
            <p>Resume of the sales of Dark Cafeteria.</p>
        </div>
        <nav class="dashboard-actions" aria-label="Ações do painel">
            <a class="dashboard-link dashboard-link-light" href="?paginas=inicio">Back to site</a>
            <a class="dashboard-link" href="?paginas=logout">Log Out</a>
        </nav>
    </section>

    <section class="dashboard-metrics" aria-label="Métricas de vendas">
        <div class="metric-card">
            <h3>Total Revenue</h3>
            <p id="metrica-faturamento">Loading...</p>
        </div>
        <div class="metric-card">
            <h3>Sold Itens</h3>
            <p id="metrica-itens">Loading...</p>
        </div>
        <div class="metric-card">
            <h3>Medium Ticket</h3>
            <p id="metrica-ticket">Loading...</p>
        </div>
    </section>
</main>

<script src="dist/dashboard.js"></script>