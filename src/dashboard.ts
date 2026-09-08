type DashboardSaleItem = {
    id_sale: number;
    sale_date?: string;
    client?: string;
    drink?: string;
    category?: string;
    quantity: number | string;
    unit_price: number | string;
    subtotal?: number | string;
};

type DashboardApiPayload = DashboardSaleItem[] | {
    data?: DashboardSaleItem[];
    error?: string;
};

type MetricasGlobais = {
    faturamentoTotal: number;
    itensVendidos: number;
    vendasUnicas: number[];
};

type ProdutoRanking = {
    nome: string;
    quantidade: number;
    faturamento: number;
};

type CategoriaRanking = {
    nome: string;
    faturamento: number;
    itensVendidos: number;
};

const formatarMoeda = (valor: number): string => {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(Number.isFinite(valor) ? valor : 0);
};

const converterNumero = (valor: unknown): number => {
    const numero = Number(valor);
    return Number.isFinite(numero) ? numero : 0;
};

const normalizarRegistro = (item: DashboardSaleItem): DashboardSaleItem => {
    const quantidade = converterNumero(item.quantity);
    const valorUnitario = converterNumero(item.unit_price);
    const subtotal = converterNumero(item.subtotal ?? (quantidade * valorUnitario));

    return {
        ...item,
        quantity: quantidade,
        unit_price: valorUnitario,
        subtotal,
        drink: item.drink?.trim() || 'Produto não informado',
        category: item.category?.trim() || 'Sem categoria'
    };
};

const construirMetricasGlobais = (dados: DashboardSaleItem[]): MetricasGlobais => {
    return dados.reduce((acumulador: MetricasGlobais, itemAtual: DashboardSaleItem) => {
        const quantidade = converterNumero(itemAtual.quantity);
        const valorUnitario = converterNumero(itemAtual.unit_price);
        const idVenda = Number(itemAtual.id_sale);

        acumulador.faturamentoTotal += quantidade * valorUnitario;
        acumulador.itensVendidos += quantidade;

        if (Number.isFinite(idVenda) && !acumulador.vendasUnicas.includes(idVenda)) {
            acumulador.vendasUnicas.push(idVenda);
        }

        return acumulador;
    }, {
        faturamentoTotal: 0,
        itensVendidos: 0,
        vendasUnicas: []
    });
};

const construirRankingProdutos = (dados: DashboardSaleItem[]): ProdutoRanking[] => {
    const mapaProdutos = dados.reduce<Record<string, ProdutoRanking>>((acumulador, item) => {
        const nome = item.drink ?? 'Produto não informado';
        const quantidade = converterNumero(item.quantity);
        const faturamento = converterNumero(item.unit_price) * quantidade;

        if (!acumulador[nome]) {
            acumulador[nome] = { nome, quantidade: 0, faturamento: 0 };
        }

        acumulador[nome].quantidade += quantidade;
        acumulador[nome].faturamento += faturamento;

        return acumulador;
    }, {});

    return Object.values(mapaProdutos)
        .sort((a, b) => b.quantidade - a.quantidade || b.faturamento - a.faturamento);
};

const construirRankingCategorias = (dados: DashboardSaleItem[]): CategoriaRanking[] => {
    const mapaCategorias = dados.reduce<Record<string, CategoriaRanking>>((acumulador, item) => {
        const nome = item.category ?? 'Sem categoria';
        const quantidade = converterNumero(item.quantity);
        const faturamento = converterNumero(item.unit_price) * quantidade;

        if (!acumulador[nome]) {
            acumulador[nome] = { nome, faturamento: 0, itensVendidos: 0 };
        }

        acumulador[nome].faturamento += faturamento;
        acumulador[nome].itensVendidos += quantidade;

        return acumulador;
    }, {});

    return Object.values(mapaCategorias)
        .map((categoria) => ({
            ...categoria,
            faturamento: Number(categoria.faturamento.toFixed(2))
        }))
        .sort((a, b) => b.faturamento - a.faturamento);
};

async function fetchDashboard(): Promise<void> {
    const statusElement = document.getElementById('dashboard-status');
    const metricaFaturamento = document.getElementById('metrica-faturamento');
    const metricaItens = document.getElementById('metrica-itens');
    const metricaTicket = document.getElementById('metrica-ticket');
    const metricaProduto = document.getElementById('metrica-produto');
    const metricaCategoria = document.getElementById('metrica-categoria');

    const atualizarStatus = (mensagem: string, erro = false): void => {
        if (statusElement) {
            statusElement.textContent = mensagem;
            statusElement.classList.toggle('error', erro);
        }
    };

    const atualizarMetrica = (element: HTMLElement | null, valor: string): void => {
        if (element) {
            element.textContent = valor;
        }
    };

    try {
        atualizarStatus('Loading Metrics...');

        const response = await fetch('api/dashboard.php', {
            headers: {
                Accept: 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Failed to load dashboard data.');
        }

        const payload = await response.json() as DashboardApiPayload;

        if (payload && typeof payload === 'object' && 'error' in payload && payload.error) {
            throw new Error(payload.error);
        }

        const rawSalesData = Array.isArray(payload)
            ? payload
            : Array.isArray(payload?.data)
                ? payload.data
                : [];

        if (rawSalesData.length === 0) {
            atualizarStatus('No data available.', true);
            atualizarMetrica(metricaFaturamento, 'R$ 0,00');
            atualizarMetrica(metricaItens, '0 unid.');
            atualizarMetrica(metricaTicket, 'R$ 0,00');
            atualizarMetrica(metricaProduto, 'Nenhum dado registrado');
            atualizarMetrica(metricaCategoria, 'No data available');
            return;
        }

        const dadosNormalizados = rawSalesData
            .filter((item): item is DashboardSaleItem => Boolean(item) && typeof item === 'object')
            .map(normalizarRegistro);

        const metricasGlobais = construirMetricasGlobais(dadosNormalizados);
        const rankingProdutos = construirRankingProdutos(dadosNormalizados);
        const rankingCategorias = construirRankingCategorias(dadosNormalizados);
        const produtoMaisVendido = rankingProdutos[0] ?? null;
        const categoriaMaisVendida = rankingCategorias[0] ?? null;

        const totalPedidos = metricasGlobais.vendasUnicas.length;
        const ticketMedio = totalPedidos > 0 ? metricasGlobais.faturamentoTotal / totalPedidos : 0;

        atualizarMetrica(metricaFaturamento, formatarMoeda(metricasGlobais.faturamentoTotal));
        atualizarMetrica(metricaItens, `${metricasGlobais.itensVendidos} unid.`);
        atualizarMetrica(metricaTicket, formatarMoeda(ticketMedio));
        atualizarMetrica(
            metricaProduto,
            produtoMaisVendido ? `${produtoMaisVendido.nome} (${produtoMaisVendido.quantidade} unid.)` : 'Nenhum dado registrado'
        );
        atualizarMetrica(
            metricaCategoria,
            categoriaMaisVendida ? `${categoriaMaisVendida.nome} (${formatarMoeda(categoriaMaisVendida.faturamento)})` : 'Nenhum dado registrado'
        );
        atualizarStatus('Metrics updated successfully.');

        console.log('Metrics processed by reduce():', metricasGlobais);
        console.log('Top products by ranking:', rankingProdutos);
        console.log('Top categories by ranking:', rankingCategorias);
    } catch (erro) {
        const mensagem = erro instanceof Error ? erro.message : 'Unexpected error occurred while loading the dashboard.';
        console.error('Error loading dashboard:', erro);

        atualizarStatus(mensagem, true);
        atualizarMetrica(metricaFaturamento, '—');
        atualizarMetrica(metricaItens, '—');
        atualizarMetrica(metricaTicket, '—');
        atualizarMetrica(metricaProduto, '—');
        atualizarMetrica(metricaCategoria, '—');
    }
}

fetchDashboard();