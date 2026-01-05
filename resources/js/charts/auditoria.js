document.addEventListener("DOMContentLoaded", () => {

    if (!window.Highcharts) {
        console.error("Highcharts não carregou pelo Vite.");
        return;
    }

    // ======== Mensal ========
    Highcharts.chart('chartMensal', {
        chart: { type: 'column', backgroundColor: 'transparent' },
        title: { text: null },
        xAxis: { categories: window.chartData.meses },
        series: [{ name: 'Gastos', data: window.chartData.mensalValoresArray }]
    });

    // ======== Distribuição ========
    Highcharts.chart('chartDistribuicao', {
        chart: { type: 'pie', backgroundColor: 'transparent' },
        title: { text: null },
        series: [{
            data: window.chartData.mensalPercentual.map((v,i)=>({
                name: window.chartData.meses[i],
                y: v
            }))
        }]
    });

    // ======== Radar ========
    Highcharts.chart('chartRadar', {
        chart: { polar: true, type: 'line', backgroundColor: 'transparent' },
        title: { text: null },
        xAxis: {
            categories: window.chartData.topCategorias.map(c => c.Categoria)
        },
        series: [{
            name: 'Total',
            data: window.chartData.topCategorias.map(c => c.total)
        }]
    });

    // ======== CNPJs ========
    Highcharts.chart('chartCnpjs', {
        chart: { type: 'bar', backgroundColor: 'transparent' },
        xAxis: { categories: window.chartData.topCnpjs.map(c => c.cnpj) },
        series: [{
            name: 'Total',
            data: window.chartData.topCnpjs.map(c => c.total)
        }]
    });

    // ======== Heatmap ========
    Highcharts.chart('chartHeatmap', {
        chart: { type: 'heatmap', backgroundColor: 'transparent' },
        title: { text: null },
        xAxis: { categories: window.chartData.heatmapMeses },
        colorAxis: { min: 0, minColor: '#ffffff', maxColor: '#F9821A' },
        series: [{
            borderWidth: 1,
            data: window.chartData.heatmapValores.map((v,i)=>[i,0,v]),
        }]
    });

});
