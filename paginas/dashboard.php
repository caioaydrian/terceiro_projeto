<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['usuario_autenticado'])) {
    header('Location: ?paginas=login');
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
            <a class="dashboard-link dashboard-link-primary" href="?paginas=dashboard">Dashboard</a>
            <a class="dashboard-link dashboard-link-light" href="?paginas=produtos">Products</a>
            <a class="dashboard-link dashboard-link-light" href="?paginas=clientes">Clients</a>
            <a class="dashboard-link dashboard-link-light" href="?paginas=comandas">Orders</a>
            <a class="dashboard-link" href="?paginas=logout">Log Out</a>
        </nav>
    </section>

    <p id="dashboard-status" class="dashboard-status" aria-live="polite">Loading Metrics...</p>

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

    <section class="dashboard-metrics dashboard-insights" aria-label="Indicadores de destaque">
        <div class="metric-card">
            <h3>Best Seller</h3>
            <p id="metrica-produto">Loading...</p>
        </div>
        <div class="metric-card">
            <h3>Top Category</h3>
            <p id="metrica-categoria">Loading...</p>
        </div>
    </section>
</main>

<script src="dist/dashboard.js"></script>