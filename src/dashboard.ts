type SaleItem = {
    id_sale: number;
    sale_date: string;
    client: string;
    drink: string;
    quantity: string;
    unit_price: string;
    subtotal: string;
}

type MetricasGlobais = {
    faturamentoTotal: number;
    itensVendidos: number;
    vendasUnicas: number[];
}

async function fetchDashboard() {
    const response = await fetch("api/dashboard.php");
    if (!response.ok) throw new Error("Couldn't load the dashboard.");

    const rawSalesData: SaleItem[] = await response.json();

    const metricasGlobais = rawSalesData.reduce((acumulador: MetricasGlobais, itemAtual: SaleItem) => {

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

    const formatarMoeda = (valor: number) => {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(valor);
    };

    const metricaFaturamento = document.getElementById('metrica-faturamento');
    if (metricaFaturamento) metricaFaturamento.innerText = formatarMoeda(metricasGlobais.faturamentoTotal);

    const metricaItens = document.getElementById('metrica-itens');
    if (metricaItens) metricaItens.innerText = metricasGlobais.itensVendidos + " unid.";

    const metricaTicket = document.getElementById('metrica-ticket');
    if (metricaTicket) metricaTicket.innerText = formatarMoeda(ticketMedio);

    console.log("Métricas processadas via reduce():", metricasGlobais);
}

fetchDashboard()