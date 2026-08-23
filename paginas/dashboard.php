<?php
require_once __DIR__ . '/../config/database.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

if (empty($_SESSION['usuario_autenticado'])) {
    header('Location: ../index.php?paginas=login');
    exit;
}

try {
    if (!$pdo) {
        throw new RuntimeException('Conexão com o banco indisponível.');
    }

    $stmt = $pdo->query("SELECT id_sale, quantity, unit_price FROM vw_sales_complete");
    $dadosBrutos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $dadosBrutos = [];
} catch (RuntimeException $e) {
    $dadosBrutos = [];
}

$jsonDados = json_encode($dadosBrutos, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
?>

<main class="dashboard-page">
    <section class="dashboard-heading">
        <div>
            <span class="login-eyebrow">Painel administrativo</span>
            <h1>Olá, administrador.</h1>
            <p>Resumo das métricas globais da Dark Cafeteria.</p>
        </div>
        <nav class="dashboard-actions" aria-label="Ações do painel">
            <a class="dashboard-link dashboard-link-light" href="?paginas=inicio">Ver site</a>
            <a class="dashboard-link" href="?paginas=logout">Sair</a>
        </nav>
    </section>

    <section class="dashboard-metrics" aria-label="Métricas de vendas">
        <div class="metric-card">
            <h3>Faturamento Total</h3>
            <p id="metrica-faturamento">Carregando...</p>
        </div>
        <div class="metric-card">
            <h3>Itens Vendidos</h3>
            <p id="metrica-itens">Carregando...</p>
        </div>
        <div class="metric-card">
            <h3>Ticket Médio</h3>
            <p id="metrica-ticket">Carregando...</p>
        </div>
    </section>
</main>

<script>
    const rawSalesData = <?= $jsonDados ?>;

    const metricasGlobais = rawSalesData.reduce((acumulador, itemAtual) => {

        const quantidade = parseFloat(itemAtual.quantity);
        const valorUnitario = parseFloat(itemAtual.unit_price);
        const idVenda = itemAtual.id_sale;

        acumulador.faturamentoTotal += (quantidade * valorUnitario);

        acumulador.itensVendidos += quantidade;

        if (!acumulador.vendasUnicas.includes(idVenda)) {
            acumulador.vendasUnicas.push(idVenda);
        }

        return acumulador;
    }, {
        faturamentoTotal: 0,
        itensVendidos: 0,
        vendasUnicas: []
    });

    const totalPedidos = metricasGlobais.vendasUnicas.length;
    const ticketMedio = totalPedidos > 0 ?
        (metricasGlobais.faturamentoTotal / totalPedidos) :
        0;

    const formatarMoeda = (valor) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(valor);
    };

    document.getElementById('metrica-faturamento').innerText = formatarMoeda(metricasGlobais.faturamentoTotal);
    document.getElementById('metrica-itens').innerText = metricasGlobais.itensVendidos + " unid.";
    document.getElementById('metrica-ticket').innerText = formatarMoeda(ticketMedio);

    console.log("Métricas processadas via reduce():", metricasGlobais);
</script>