import Chart from 'chart.js/auto';
import './dashboard.js';

const palette = [
    '#be185d', '#db2777', '#ec4899', '#f472b6', '#fb7185',
    '#e11d48', '#f9a8d4', '#9d174d', '#c026d3', '#fda4af',
];

const currency = new Intl.NumberFormat(undefined, {
    style: 'currency',
    currency: 'USD',
    maximumFractionDigits: 2,
});

window.renderAnalyticsCharts = (payload) => {
    const grid = document.getElementById('chart-grid');
    const summary = document.getElementById('summary');
    const status = document.getElementById('dashboard-status');

    if (!grid || !summary || !status) return;

    const summaryLabels = {
        products: 'Products',
        categories: 'Categories',
        orders: 'Orders',
        reviews: 'Reviews',
    };

    summary.innerHTML = Object.entries(summaryLabels).map(([key, label]) => `
        <div class="summary-card">
            <span>${label}</span>
            <strong>${Number(payload.summary[key] ?? 0).toLocaleString()}</strong>
        </div>
    `).join('');

    grid.innerHTML = payload.charts.map((chart, index) => `
        <article class="chart-card">
            <div class="chart-heading">
                <h2>${chart.title}</h2>
                <span>${String(index + 1).padStart(2, '0')}</span>
            </div>
            <div class="chart-canvas-wrap">
                <canvas id="chart-${index}" role="img" aria-label="${chart.title}"></canvas>
            </div>
        </article>
    `).join('');

    payload.charts.forEach((chart, index) => {
        const isCircular = chart.type === 'doughnut' || chart.type === 'pie';
        const isLine = chart.type === 'line';
        const colors = chart.labels.map((_, colorIndex) => palette[colorIndex % palette.length]);

        new Chart(document.getElementById(`chart-${index}`), {
            type: chart.type,
            data: {
                labels: chart.labels,
                datasets: [{
                    label: chart.datasetLabel,
                    data: chart.values,
                    backgroundColor: isCircular ? colors : chart.color,
                    borderColor: isLine ? chart.color : (isCircular ? '#fff' : chart.color),
                    borderWidth: isCircular ? 2 : 1,
                    borderRadius: chart.type === 'bar' ? 6 : 0,
                    pointRadius: isLine ? 3 : 0,
                    pointHoverRadius: isLine ? 5 : 0,
                    fill: isLine,
                    tension: 0.35,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: chart.indexAxis ?? 'x',
                plugins: {
                    legend: { display: isCircular, position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label(context) {
                                const value = Number(context.raw ?? 0);
                                const formatted = chart.format === 'currency'
                                    ? currency.format(value)
                                    : value.toLocaleString(undefined, { maximumFractionDigits: 2 });
                                return `${context.dataset.label}: ${formatted}`;
                            },
                        },
                    },
                },
                scales: isCircular ? {} : {
                    x: {
                        beginAtZero: true,
                        grid: { display: chart.indexAxis === 'y' },
                        ticks: chart.format === 'currency' && chart.indexAxis === 'y'
                            ? { callback: (value) => currency.format(Number(value)) }
                            : {},
                    },
                    y: {
                        beginAtZero: true,
                        grid: { display: chart.indexAxis !== 'y' },
                        ticks: chart.format === 'currency' && chart.indexAxis !== 'y'
                            ? { callback: (value) => currency.format(Number(value)) }
                            : { precision: 0 },
                    },
                },
            },
        });
    });

    status.hidden = true;
};
