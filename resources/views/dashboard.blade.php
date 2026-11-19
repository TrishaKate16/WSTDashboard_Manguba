<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Store Analytics | {{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="analytics-page">
    <main class="analytics-shell">
        <header class="analytics-header">
            <div>
                <p class="eyebrow">STORE INSIGHTS</p>
                <h1>Analytics Dashboard</h1>
                <p class="subtitle">Products, inventory, orders, and customer reviews at a glance.</p>
            </div>
            <a class="refresh-button" href="/dashboard">Refresh data</a>
        </header>

        <section class="summary-grid" id="summary" aria-label="Store totals">
            <div class="summary-card"><span>Products</span><strong>—</strong></div>
            <div class="summary-card"><span>Categories</span><strong>—</strong></div>
            <div class="summary-card"><span>Orders</span><strong>—</strong></div>
            <div class="summary-card"><span>Reviews</span><strong>—</strong></div>
        </section>

        <p class="dashboard-status" id="dashboard-status" role="status">Loading chart data…</p>
        <section class="chart-grid" id="chart-grid" aria-label="Store analytics charts"></section>
    </main>
</body>
</html>
