const grid = document.getElementById('chart-grid');

if (grid) {
    fetch('/api/v1/charts', {
        headers: { Accept: 'application/json' },
    })
        .then((response) => {
            if (!response.ok) {
                throw new Error(`Chart data request failed (${response.status}).`);
            }

            return response.json();
        })
        .then((payload) => {
            if (!Array.isArray(payload.charts) || payload.charts.length !== 15) {
                throw new Error('The server did not return all 15 charts.');
            }

            window.renderAnalyticsCharts(payload);
        })
        .catch((error) => {
            const status = document.getElementById('dashboard-status');
            status.textContent = `Unable to load analytics: ${error.message}`;
            status.hidden = false;
            status.classList.add('is-error');
        });
}
