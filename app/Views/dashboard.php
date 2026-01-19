<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<div class="container-fluid px-4">

    <!-- ================= HEADER ================= -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 font-weight-bold text-dark mb-1">
                Dashboard
            </h1>
            <p class="text-muted small mb-0">
                Ringkasan performa dan statistik try out
            </p>
        </div>
    </div>

    <!-- ================= STATISTIK CARDS ================= -->
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

    <!-- ================= GRAFIK ================= -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-xl">
                <div class="card-header bg-white py-3 d-flex align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Statistik Try Out per Kategori
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="grafikTryoutKategori" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- ================= STYLE (VISUAL ONLY) ================= -->
<style>
    .stat-card {
        border-radius: 16px;
        color: #fff;
        position: relative;
        overflow: hidden;
        transition: all .25s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 32px rgba(0, 0, 0, .15);
    }

    .stat-title {
        font-size: 11px;
        letter-spacing: .8px;
        text-transform: uppercase;
        opacity: .85;
        margin-bottom: 6px;
    }

    .stat-value {
        font-size: 30px;
        font-weight: 800;
        line-height: 1;
    }

    .stat-icon {
        position: absolute;
        right: 18px;
        bottom: 16px;
        font-size: 44px;
        opacity: .25;
    }

    .stat-primary {
        background: linear-gradient(135deg, #4e73df, #224abe);
    }

    .stat-success {
        background: linear-gradient(135deg, #1cc88a, #13855c);
    }

    .stat-warning {
        background: linear-gradient(135deg, #f6c23e, #dda20a);
    }

    .stat-info {
        background: linear-gradient(135deg, #36b9cc, #258391);
    }
</style>

<!-- ================= CHART JS ================= -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const grafikData = <?= json_encode($tryout_grafik) ?>;

    const labels = grafikData.map(item => item.kategori);
    const peserta = grafikData.map(item => item.peserta);
    const attempt = grafikData.map(item => item.attempt);
    const rataNilai = grafikData.map(item => item.rata_nilai);

    const ctx = document.getElementById('grafikTryoutKategori').getContext('2d');

    new Chart(ctx, {
        data: {
            labels: labels,
            datasets: [{
                    type: 'bar',
                    label: 'Peserta',
                    data: peserta,
                    backgroundColor: 'rgba(28, 200, 138, 0.75)',
                    borderRadius: 6
                },
                {
                    type: 'bar',
                    label: 'Attempt',
                    data: attempt,
                    backgroundColor: 'rgba(246, 194, 62, 0.75)',
                    borderRadius: 6
                },
                {
                    type: 'line',
                    label: 'Rata-rata Nilai',
                    data: rataNilai,
                    borderColor: '#4e73df',
                    backgroundColor: '#4e73df',
                    borderWidth: 3,
                    fill: false,
                    tension: .35,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Jumlah'
                    }
                },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false
                    },
                    title: {
                        display: true,
                        text: 'Nilai'
                    }
                }
            }
        }
    });
</script>

<?= $this->endSection(); ?>