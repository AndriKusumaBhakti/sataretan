<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<div class="container-fluid px-4">

    <!-- ================= HEADER ================= -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 font-weight-bold text-dark mb-1">Dashboard</h1>
            <p class="text-muted small mb-0">Ringkasan performa dan statistik try out</p>
        </div>
    </div>

    <!-- ================= STAT CARDS ================= -->
    <div class="row">

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card stat-primary shadow-sm">
                <div class="card-body">
                    <div class="stat-title">Total Try Out</div>
                    <div class="stat-value"><?= $tryout['total_tryout'] ?></div>
                    <i class="fas fa-book stat-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card stat-success shadow-sm">
                <div class="card-body">
                    <div class="stat-title">Total Peserta</div>
                    <div class="stat-value"><?= $tryout['total_peserta'] ?></div>
                    <i class="fas fa-users stat-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card stat-warning shadow-sm">
                <div class="card-body">
                    <div class="stat-title">Total Attempt</div>
                    <div class="stat-value"><?= $tryout['total_attempt'] ?></div>
                    <i class="fas fa-redo stat-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card stat-info shadow-sm">
                <div class="card-body">
                    <div class="stat-title">Rata-rata Nilai</div>
                    <div class="stat-value"><?= number_format($tryout['rata_nilai'], 1) ?></div>
                    <i class="fas fa-chart-line stat-icon"></i>
                </div>
            </div>
        </div>

    </div>

    <!-- ================= CHART ================= -->
    <div class="card shadow-sm border-0 rounded-xl">
        <div class="card-header bg-white py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                Grafik Performa Nilai per Kategori
            </h6>
            <small class="text-muted">Akademik • Psikolog • Jasmani tiap bulan</small>
        </div>

        <div class="card-body">
            <div class="chart-wrapper">
                <canvas id="chartBulanan"></canvas>
            </div>
        </div>
    </div>

</div>

<!-- ================= STYLE ================= -->
<style>
    .stat-card {
        border-radius: 20px;
        color: #fff;
        position: relative;
        overflow: hidden;
        transition: all .25s ease;
    }

    .stat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 45px rgba(0, 0, 0, .15);
    }

    .stat-title {
        font-size: 12px;
        letter-spacing: .7px;
        text-transform: uppercase;
        opacity: .85;
    }

    .stat-value {
        font-size: 34px;
        font-weight: 800;
    }

    .stat-icon {
        position: absolute;
        right: 18px;
        bottom: 14px;
        font-size: 48px;
        opacity: .18;
    }

    .stat-primary {
        background: linear-gradient(135deg, #5b8cff, #1c54e8);
    }

    .stat-success {
        background: linear-gradient(135deg, #22c55e, #15803d);
    }

    .stat-warning {
        background: linear-gradient(135deg, #fbbf24, #d97706);
    }

    .stat-info {
        background: linear-gradient(135deg, #22d3ee, #0e7490);
    }

    .rounded-xl {
        border-radius: 20px !important;
    }

    .chart-wrapper {
        position: relative;
        width: 100%;
        height: 420px;
    }

    @media(max-width:768px) {
        .chart-wrapper {
            height: 460px;
        }
    }
</style>

<!-- ================= CHART JS ================= -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const raw = <?= json_encode($grafik_bulanan) ?>;

    /* ===== NORMALISASI ===== */
    const bulanSet = [...new Set(raw.map(x => x.bulan))].sort();

    function formatBulan(b) {
        const [y, m] = b.split('-');
        return new Date(y, m - 1).toLocaleDateString('id-ID', {
            month: 'short',
            year: 'numeric'
        });
    }
    const labels = bulanSet.map(formatBulan);

    function dataKategori(nama) {
        return bulanSet.map(b => {
            const f = raw.find(x => x.bulan === b && x.kategori === nama);
            return f ? Number(f.rata_nilai) : null;
        });
    }

    /* ===== GRADIENT ===== */
    const ctx = document.getElementById('chartBulanan').getContext('2d');

    function gradient(color) {
        const g = ctx.createLinearGradient(0, 0, 0, 420);
        g.addColorStop(0, color.replace('1)', '0.35)'));
        g.addColorStop(1, color.replace('1)', '0.02)'));
        return g;
    }

    /* ===== CHART ===== */
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                    label: 'Akademik',
                    data: dataKategori('akademik'),
                    borderColor: 'rgba(91,140,255,1)',
                    backgroundColor: gradient('rgba(91,140,255,1)'),
                    fill: true,
                    tension: .4,
                    pointRadius: 3,
                    pointHoverRadius: 6
                },
                {
                    label: 'Psikolog',
                    data: dataKategori('psikolog'),
                    borderColor: 'rgba(34,197,94,1)',
                    backgroundColor: gradient('rgba(34,197,94,1)'),
                    fill: true,
                    tension: .4,
                    pointRadius: 3,
                    pointHoverRadius: 6
                },
                {
                    label: 'Jasmani',
                    data: dataKategori('jasmani'),
                    borderColor: 'rgba(251,191,36,1)',
                    backgroundColor: gradient('rgba(251,191,36,1)'),
                    fill: true,
                    tension: .4,
                    pointRadius: 3,
                    pointHoverRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            animation: {
                duration: 1200,
                easing: 'easeOutQuart'
            },

            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 18
                    }
                },
                tooltip: {
                    backgroundColor: '#111827',
                    padding: 12,
                    cornerRadius: 10,
                    callbacks: {
                        label: (ctx) => `${ctx.dataset.label}: ${ctx.raw ?? '-'}`
                    }
                }
            },

            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        maxRotation: 0
                    }
                },
                y: {
                    min: 0,
                    max: 100,
                    ticks: {
                        callback: (v) => v + ''
                    },
                    grid: {
                        color: 'rgba(0,0,0,.06)'
                    }
                }
            }
        }
    });
</script>

<?= $this->endSection(); ?>