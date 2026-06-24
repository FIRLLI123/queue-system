@extends('layouts.app')

@section('title', 'Laporan Order')

@push('styles')
<style>
/* ─── Page-level variables ──────────────────────────────────── */
:root {
    --report-crm:   #3b82f6; /* Blue */
    --report-cms:   #10b981; /* Green */
    --report-other: #eab308; /* Yellow */
    --report-total: #6b7280; /* Gray */
    --card-radius:  14px;
}

/* ─── Header gradient card ───────────────────────────────────── */
.report-hero {
    background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 50%, #2563eb 100%);
    border-radius: var(--card-radius);
    color: #fff;
    padding: 28px 32px;
    box-shadow: 0 8px 32px rgba(37,99,235,.35);
    position: relative;
    overflow: hidden;
}
.report-hero::before {
    content: '';
    position: absolute;
    top: -40px; right: -40px;
    width: 220px; height: 220px;
    border-radius: 50%;
    background: rgba(255,255,255,.06);
}
.report-hero::after {
    content: '';
    position: absolute;
    bottom: -60px; right: 80px;
    width: 160px; height: 160px;
    border-radius: 50%;
    background: rgba(255,255,255,.04);
}

/* ─── KPI Cards ──────────────────────────────────────────────── */
.kpi-card {
    border-radius: var(--card-radius);
    padding: 20px 22px;
    display: flex;
    align-items: center;
    gap: 18px;
    box-shadow: 0 4px 18px rgba(0,0,0,.07);
    border: none;
    transition: transform .2s, box-shadow .2s;
}
.kpi-card:hover { transform: translateY(-3px); box-shadow: 0 8px 28px rgba(0,0,0,.12); }
.kpi-icon {
    width: 54px; height: 54px;
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 22px; flex-shrink: 0;
}
.kpi-val { font-size: 28px; font-weight: 800; line-height: 1; }
.kpi-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; opacity: .65; margin-top: 3px; }

.kpi-total  { background: linear-gradient(135deg,#f8fafc,#f1f5f9); }
.kpi-crm    { background: linear-gradient(135deg,#eff6ff,#dbeafe); }
.kpi-cms    { background: linear-gradient(135deg,#f0fdf4,#ecfdf5); }
.kpi-other  { background: linear-gradient(135deg,#fffbeb,#fef3c7); }

.kpi-total  .kpi-val { color: #475569; }
.kpi-crm    .kpi-val { color: #2563eb; }
.kpi-cms    .kpi-val { color: #059669; }
.kpi-other  .kpi-val { color: #d97706; }

.kpi-total  .kpi-icon { background: rgba(71,85,105,.12); color: #475569; }
.kpi-crm    .kpi-icon { background: rgba(59,130,246,.12); color: #2563eb; }
.kpi-cms    .kpi-icon { background: rgba(16,185,129,.12); color: #059669; }
.kpi-other  .kpi-icon { background: rgba(245,158,11,.12); color: #d97706; }

/* ─── Filter bar ─────────────────────────────────────────────── */
.filter-bar {
    background: #fff;
    border-radius: var(--card-radius);
    padding: 18px 22px;
    box-shadow: 0 2px 12px rgba(0,0,0,.06);
    border: 1px solid #e2e8f0;
}
.filter-bar .form-control,
.filter-bar .custom-select {
    border-radius: 8px;
    border-color: #cbd5e1;
    font-size: 13px;
    height: 38px;
}
.filter-bar label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #64748b; margin-bottom: 4px; }

/* ─── Chart cards ────────────────────────────────────────────── */
.chart-card {
    background: #fff;
    border-radius: var(--card-radius);
    box-shadow: 0 2px 16px rgba(0,0,0,.07);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}
.chart-card-header {
    padding: 16px 22px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.chart-card-title { font-weight: 700; font-size: 14px; color: #1e293b; }
.chart-card-sub   { font-size: 11px; color: #94a3b8; margin-top: 2px; }
.chart-card-body  { padding: 18px 22px 22px; }

/* ─── Data table ─────────────────────────────────────────────── */
.report-table-card {
    background: #fff;
    border-radius: var(--card-radius);
    box-shadow: 0 2px 16px rgba(0,0,0,.07);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}
.report-table-card table thead th {
    background: #f8fafc;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #64748b;
    border-bottom: 1px solid #e2e8f0;
    padding: 12px 14px;
    white-space: nowrap;
}
.report-table-card table tbody td {
    font-size: 13px;
    padding: 10px 14px;
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
}
.report-table-card table tbody tr:last-child td { border-bottom: none; }
.report-table-card table tbody tr:hover td { background: #f8fafc; }
.badge-crm   { background: rgba(59,130,246,.12); color: #2563eb; }
.badge-cms   { background: rgba(16,185,129,.12); color: #059669; }
.badge-other { background: rgba(245,158,11,.12); color: #d97706; }
.count-pill {
    display: inline-block;
    min-width: 36px;
    text-align: center;
    padding: 3px 9px;
    border-radius: 20px;
    font-weight: 700;
    font-size: 12px;
}

/* ─── Export button ──────────────────────────────────────────── */
.btn-export {
    background: linear-gradient(135deg,#16a34a,#15803d);
    color: #fff;
    border: none;
    border-radius: 8px;
    padding: 8px 18px;
    font-weight: 700;
    font-size: 13px;
    transition: all .2s;
    box-shadow: 0 2px 8px rgba(22,163,74,.3);
}
.btn-export:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(22,163,74,.4); color: #fff; }
.btn-filter {
    background: linear-gradient(135deg,#2563eb,#1d4ed8);
    color: #fff; border: none; border-radius: 8px;
    padding: 8px 20px; font-weight: 700; font-size: 13px;
    transition: all .2s;
    box-shadow: 0 2px 8px rgba(37,99,235,.3);
}
.btn-filter:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(37,99,235,.4); color: #fff; }

/* ─── Skeleton ───────────────────────────────────────────────── */
.skeleton { background: linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; border-radius: 6px; }
@keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* ─── Empty state ────────────────────────────────────────────── */
.empty-state { text-align: center; padding: 60px 20px; color: #94a3b8; }
.empty-state i { font-size: 48px; margin-bottom: 16px; }
</style>
@endpush

@section('content')

{{-- ── PAGE HERO ──────────────────────────────────────────────────────── --}}
<div class="report-hero mb-4">
    <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap:16px; position:relative; z-index:1;">
        <div>
            <h1 class="h4 font-weight-bold mb-1" style="color:#fff;">
                <i class="fas fa-chart-bar mr-2" style="opacity:.85;"></i>Laporan Order CC
            </h1>
            <p class="mb-0" style="opacity:.75; font-size:13px;">
                Analitik performa penerimaan order Customer Care — filter per tanggal, user, atau tipe.
            </p>
        </div>
        <div class="d-flex align-items-center" style="gap:10px;">
            <span class="badge badge-light px-3 py-2 font-weight-bold" style="font-size:12px; border-radius:20px;" id="range-badge">
                <i class="fas fa-calendar-alt mr-1"></i> Memuat...
            </span>
            <a href="#" id="btn-export-top" class="btn-export d-flex align-items-center" style="gap:8px; text-decoration:none;">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
        </div>
    </div>
</div>

{{-- ── FILTER BAR ─────────────────────────────────────────────────────── --}}
<div class="filter-bar mb-4">
    <form id="filter-form" class="row align-items-end" style="gap:0;">
        <div class="col-auto mb-2">
            <label for="f-date-from">Dari Tanggal</label>
            <input type="date" id="f-date-from" name="date_from" class="form-control">
        </div>
        <div class="col-auto mb-2">
            <label for="f-date-to">Sampai Tanggal</label>
            <input type="date" id="f-date-to" name="date_to" class="form-control">
        </div>
        <div class="col-auto mb-2" style="min-width:180px;">
            <label for="f-user">User CC</label>
            <select id="f-user" name="user_id" class="custom-select">
                <option value="">Semua User</option>
            </select>
        </div>
        <div class="col-auto mb-2" style="min-width:150px;">
            <label for="f-type">Tipe Order</label>
            <select id="f-type" name="order_type" class="custom-select">
                <option value="">Semua Tipe</option>
                <option value="CRM">CRM</option>
                <option value="CMS">CMS</option>
                <option value="OTHER">OTHER</option>
            </select>
        </div>
        <div class="col-auto mb-2">
            <label class="d-block" style="visibility:hidden;">Go</label>
            <button type="submit" class="btn-filter d-flex align-items-center" style="gap:6px;">
                <i class="fas fa-search"></i> Tampilkan
            </button>
        </div>
        <div class="col-auto mb-2 ml-auto">
            <label class="d-block" style="visibility:hidden;">Reset</label>
            <button type="button" id="btn-reset-filter" class="btn btn-outline-secondary" style="border-radius:8px; height:38px; font-size:13px; font-weight:600;">
                <i class="fas fa-undo mr-1"></i> Reset
            </button>
        </div>
    </form>
</div>

{{-- ── KPI CARDS ───────────────────────────────────────────────────────── --}}
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="kpi-card kpi-total">
            <div class="kpi-icon"><i class="fas fa-layer-group"></i></div>
            <div>
                <div class="kpi-val" id="kpi-total">–</div>
                <div class="kpi-label">Total Order</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="kpi-card kpi-crm">
            <div class="kpi-icon"><i class="fas fa-phone"></i></div>
            <div>
                <div class="kpi-val" id="kpi-crm">–</div>
                <div class="kpi-label">CRM Orders</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="kpi-card kpi-cms">
            <div class="kpi-icon"><i class="fab fa-whatsapp"></i></div>
            <div>
                <div class="kpi-val" id="kpi-cms">–</div>
                <div class="kpi-label">CMS Orders</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="kpi-card kpi-other">
            <div class="kpi-icon"><i class="fas fa-cog"></i></div>
            <div>
                <div class="kpi-val" id="kpi-other">–</div>
                <div class="kpi-label">Other Orders</div>
            </div>
        </div>
    </div>
</div>

{{-- ── TWO CHARTS ──────────────────────────────────────────────────────── --}}
<div class="row mb-4">

    {{-- LEFT: Line chart — daily order trend --}}
    <div class="col-xl-7 mb-4">
        <div class="chart-card h-100">
            <div class="chart-card-header">
                <div>
                    <div class="chart-card-title"><i class="fas fa-chart-line mr-2 text-primary"></i>Tren Order Harian</div>
                    <div class="chart-card-sub">Jumlah order per tipe per hari dalam rentang yang dipilih</div>
                </div>
                <span class="badge badge-pill badge-primary px-3 py-2 font-weight-bold" style="font-size:11px;" id="chart-line-range-badge">–</span>
            </div>
            <div class="chart-card-body">
                <canvas id="chart-line" style="max-height:300px;"></canvas>
                <div class="empty-state d-none" id="line-empty">
                    <i class="fas fa-chart-line text-gray-300"></i>
                    <p class="mb-0 font-weight-bold" style="font-size:14px;">Belum ada data</p>
                    <p style="font-size:12px;">Coba ubah filter tanggal atau user.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT: Doughnut — distribution per user --}}
    <div class="col-xl-5 mb-4">
        <div class="chart-card h-100">
            <div class="chart-card-header">
                <div>
                    <div class="chart-card-title"><i class="fas fa-chart-pie mr-2 text-warning"></i>Distribusi per User</div>
                    <div class="chart-card-sub">Proporsi total order masing-masing staf CC</div>
                </div>
            </div>
            <div class="chart-card-body d-flex flex-column align-items-center">
                <div style="max-width:240px; width:100%;">
                    <canvas id="chart-donut"></canvas>
                </div>
                <div id="donut-legend" class="mt-3 w-100" style="max-height:120px; overflow-y:auto;"></div>
                <div class="empty-state d-none" id="donut-empty">
                    <i class="fas fa-chart-pie text-gray-300"></i>
                    <p class="mb-0 font-weight-bold" style="font-size:14px;">Belum ada data</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── DETAILED TABLE ──────────────────────────────────────────────────── --}}
<div class="report-table-card mb-4">
    <div class="d-flex align-items-center justify-content-between px-4 py-3 border-bottom" style="border-color:#f1f5f9!important;">
        <div>
            <h6 class="font-weight-bold text-dark mb-0">
                <i class="fas fa-table mr-2 text-secondary"></i>Detail Order Per User Per Tanggal
            </h6>
            <small class="text-muted" id="table-row-count">Memuat data...</small>
        </div>
        <a href="#" id="btn-export-table" class="btn-export d-flex align-items-center" style="gap:6px; text-decoration:none; font-size:12px; padding:7px 14px;">
            <i class="fas fa-file-excel"></i> Download CSV
        </a>
    </div>
    <div style="overflow-x:auto; max-height:480px; overflow-y:auto;">
        <table class="table mb-0" id="report-table">
            <thead style="position:sticky; top:0; z-index:1;">
                <tr>
                    <th>Tanggal</th>
                    <th>Nama CEC</th>
                    <th style="text-align:center; color:var(--report-crm);">CRM</th>
                    <th style="text-align:center; color:var(--report-cms);">CMS</th>
                    <th style="text-align:center; color:var(--report-other);">OTHER</th>
                    <th style="text-align:center;">Total</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <tr><td colspan="6" class="text-center py-5 text-muted">
                    <div class="skeleton" style="height:14px; width:60%; margin:0 auto 10px;"></div>
                    <div class="skeleton" style="height:14px; width:40%; margin:0 auto;"></div>
                </td></tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
{{-- Chart.js CDN --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script>
$(document).ready(function () {

    /* ── Helpers ───────────────────────────────────────────── */
    const fmt = n => Number(n).toLocaleString('id-ID');

    function todayStr() {
        return new Date().toISOString().split('T')[0];
    }
    function firstOfMonth() {
        const d = new Date();
        d.setDate(1);
        return d.toISOString().split('T')[0];
    }
    function formatDate(str) {
        if (!str) return '–';
        const [y,m,d] = str.split('-');
        return `${d}/${m}/${y}`;
    }

    /* ── Defaults ──────────────────────────────────────────── */
    document.getElementById('f-date-from').value = firstOfMonth();
    document.getElementById('f-date-to').value   = todayStr();

    /* ── Chart instances ────────────────────────────────────── */
    let lineChart   = null;
    let donutChart  = null;

    const PALETTE = [
        '#3b82f6','#10b981','#f59e0b','#8b5cf6',
        '#ef4444','#06b6d4','#ec4899','#84cc16',
        '#f97316','#6366f1','#14b8a6','#a78bfa',
    ];

    /* ── Build Line Chart ───────────────────────────────────── */
    function buildLineChart(dateTotals) {
        const ctx = document.getElementById('chart-line').getContext('2d');

        if (lineChart) lineChart.destroy();

        const lineEmpty = document.getElementById('line-empty');
        if (!dateTotals || dateTotals.length === 0) {
            document.getElementById('chart-line').style.display = 'none';
            lineEmpty.classList.remove('d-none');
            return;
        }
        document.getElementById('chart-line').style.display = '';
        lineEmpty.classList.add('d-none');

        const labels = dateTotals.map(d => formatDate(d.date));
        const crmData   = dateTotals.map(d => d.CRM   || 0);
        const cmsData   = dateTotals.map(d => d.CMS   || 0);
        const otherData = dateTotals.map(d => d.OTHER || 0);
        const totalData = dateTotals.map(d => d.TOTAL || 0);

        lineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Total',
                        data: totalData,
                        borderColor: '#6b7280',
                        backgroundColor: 'rgba(107,114,128,.08)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 7,
                        borderWidth: 2.5,
                    },
                    {
                        label: 'CRM',
                        data: crmData,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59,130,246,.0)',
                        fill: false,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        borderWidth: 2,
                    },
                    {
                        label: 'CMS',
                        data: cmsData,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16,185,129,.0)',
                        fill: false,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        borderWidth: 2,
                    },
                    {
                        label: 'OTHER',
                        data: otherData,
                        borderColor: '#eab308',
                        backgroundColor: 'rgba(234,179,8,.0)',
                        fill: false,
                        tension: 0.4,
                        pointRadius: 3,
                        pointHoverRadius: 6,
                        borderWidth: 2,
                        borderDash: [4,4],
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'top', labels: { font: { size: 12, weight: '600' }, padding: 16, boxWidth: 14 } },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.dataset.label}: ${fmt(ctx.parsed.y)} order`,
                        },
                    },
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 }, maxRotation: 45 } },
                    y: { beginAtZero: true, ticks: { font: { size: 11 }, precision: 0 }, grid: { color: 'rgba(0,0,0,.04)' } },
                },
            },
        });
    }

    /* ── Build Donut Chart ──────────────────────────────────── */
    function buildDonutChart(userTotals) {
        const ctx = document.getElementById('chart-donut').getContext('2d');

        if (donutChart) donutChart.destroy();

        const donutEmpty = document.getElementById('donut-empty');
        const legendEl   = document.getElementById('donut-legend');

        if (!userTotals || userTotals.length === 0) {
            document.getElementById('chart-donut').style.display = 'none';
            donutEmpty.classList.remove('d-none');
            legendEl.innerHTML = '';
            return;
        }
        document.getElementById('chart-donut').style.display = '';
        donutEmpty.classList.add('d-none');

        const labels = userTotals.map(u => u.name);
        const values = userTotals.map(u => u.TOTAL);
        const colors = userTotals.map((_, i) => PALETTE[i % PALETTE.length]);

        donutChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    borderWidth: 2,
                    borderColor: '#fff',
                    hoverBorderColor: '#fff',
                    hoverOffset: 8,
                }],
            },
            options: {
                responsive: true,
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => ` ${ctx.label}: ${fmt(ctx.parsed)} order`,
                        },
                    },
                },
            },
        });

        // Custom legend
        const total = values.reduce((a, b) => a + b, 0);
        let legHtml = '';
        userTotals.forEach((u, i) => {
            const pct = total > 0 ? Math.round(u.TOTAL / total * 100) : 0;
            legHtml += `
                <div class="d-flex align-items-center justify-content-between mb-1" style="font-size:12px;">
                    <div class="d-flex align-items-center" style="gap:7px;">
                        <span style="display:inline-block;width:10px;height:10px;border-radius:3px;background:${colors[i]};flex-shrink:0;"></span>
                        <span class="font-weight-semibold text-dark">${u.name}</span>
                    </div>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <span class="text-muted">${fmt(u.TOTAL)}</span>
                        <span class="badge badge-light font-weight-bold" style="font-size:10px;">${pct}%</span>
                    </div>
                </div>
            `;
        });
        legendEl.innerHTML = legHtml;
    }

    /* ── Build Table ────────────────────────────────────────── */
    function buildTable(tableRows) {
        const tbody = document.getElementById('table-body');
        const countEl = document.getElementById('table-row-count');

        if (!tableRows || tableRows.length === 0) {
            tbody.innerHTML = `
                <tr><td colspan="6" class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p class="font-weight-bold mb-1">Tidak ada data dalam rentang ini</p>
                    <p style="font-size:12px;">Coba ubah filter periode atau user.</p>
                </td></tr>
            `;
            countEl.textContent = '0 baris';
            return;
        }

        countEl.textContent = `${tableRows.length} baris data`;

        let html = '';
        tableRows.forEach(row => {
            const crmPill   = row.CRM   > 0 ? `<span class="count-pill badge-crm">${row.CRM}</span>`   : `<span class="text-muted">–</span>`;
            const cmsPill   = row.CMS   > 0 ? `<span class="count-pill badge-cms">${row.CMS}</span>`   : `<span class="text-muted">–</span>`;
            const otherPill = row.OTHER > 0 ? `<span class="count-pill badge-other">${row.OTHER}</span>` : `<span class="text-muted">–</span>`;

            html += `
                <tr>
                    <td class="font-monospace font-weight-bold text-dark" style="font-size:12px;">${formatDate(row.order_date)}</td>
                    <td>
                        <div class="font-weight-bold text-dark" style="font-size:13px;">${row.name}</div>
                        <div class="text-muted" style="font-size:10px;">@${row.username}</div>
                    </td>
                    <td style="text-align:center;">${crmPill}</td>
                    <td style="text-align:center;">${cmsPill}</td>
                    <td style="text-align:center;">${otherPill}</td>
                    <td style="text-align:center;">
                        <span class="font-weight-bold text-dark">${row.TOTAL}</span>
                    </td>
                </tr>
            `;
        });
        tbody.innerHTML = html;
    }

    /* ── Update KPIs ────────────────────────────────────────── */
    function updateKPIs(userTotals) {
        let total = 0, crm = 0, cms = 0, other = 0;
        (userTotals || []).forEach(u => {
            total += u.TOTAL; crm += u.CRM; cms += u.CMS; other += u.OTHER;
        });
        document.getElementById('kpi-total').textContent = fmt(total);
        document.getElementById('kpi-crm').textContent   = fmt(crm);
        document.getElementById('kpi-cms').textContent   = fmt(cms);
        document.getElementById('kpi-other').textContent = fmt(other);
    }

    /* ── Populate user dropdown ─────────────────────────────── */
    function populateUserDropdown(users) {
        const sel = document.getElementById('f-user');
        const current = sel.value;
        // Keep only the first 'Semua User' option
        while (sel.options.length > 1) sel.remove(1);
        (users || []).forEach(u => {
            const opt = document.createElement('option');
            opt.value = u.id;
            opt.textContent = `${u.name} (@${u.username})`;
            sel.appendChild(opt);
        });
        if (current) sel.value = current;
    }

    /* ── Build export URL ───────────────────────────────────── */
    function buildExportUrl() {
        const from = document.getElementById('f-date-from').value;
        const to   = document.getElementById('f-date-to').value;
        const uid  = document.getElementById('f-user').value;
        const type = document.getElementById('f-type').value;
        const p = new URLSearchParams({ date_from: from, date_to: to });
        if (uid)  p.set('user_id', uid);
        if (type) p.set('order_type', type);
        return `/admin/report/export?${p.toString()}`;
    }

    function updateExportLinks() {
        const url = buildExportUrl();
        document.getElementById('btn-export-top').href   = url;
        document.getElementById('btn-export-table').href = url;
    }

    /* ── Fetch & Render ─────────────────────────────────────── */
    let isFetching = false;

    function fetchReport() {
        if (isFetching) return;
        isFetching = true;

        const from = document.getElementById('f-date-from').value;
        const to   = document.getElementById('f-date-to').value;
        const uid  = document.getElementById('f-user').value;
        const type = document.getElementById('f-type').value;

        // Range badge
        document.getElementById('range-badge').innerHTML =
            `<i class="fas fa-calendar-alt mr-1"></i> ${formatDate(from)} – ${formatDate(to)}`;
        document.getElementById('chart-line-range-badge').textContent =
            `${formatDate(from)} – ${formatDate(to)}`;

        updateExportLinks();

        const params = new URLSearchParams({ date_from: from, date_to: to });
        if (uid)  params.set('user_id', uid);
        if (type) params.set('order_type', type);

        fetch(`/admin/report/data?${params.toString()}`, {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            populateUserDropdown(data.users);
            updateKPIs(data.user_totals);
            buildLineChart(data.date_totals);
            buildDonutChart(data.user_totals);
            buildTable(data.table_rows);
        })
        .catch(err => {
            console.error('Report fetch error:', err);
            if (window.showToast) window.showToast('Gagal memuat data laporan.', 'error');
        })
        .finally(() => { isFetching = false; });
    }

    /* ── Events ─────────────────────────────────────────────── */
    document.getElementById('filter-form').addEventListener('submit', function (e) {
        e.preventDefault();
        fetchReport();
    });

    document.getElementById('btn-reset-filter').addEventListener('click', function () {
        document.getElementById('f-date-from').value = firstOfMonth();
        document.getElementById('f-date-to').value   = todayStr();
        document.getElementById('f-user').value       = '';
        document.getElementById('f-type').value       = '';
        fetchReport();
    });

    // Stop polling.js from interfering on this page
    if (window.stopRealtimePolling) window.stopRealtimePolling();

    /* ── Initial load ────────────────────────────────────────── */
    fetchReport();
});
</script>
@endpush
