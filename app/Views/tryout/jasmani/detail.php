<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- ================= HEADER ================= -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 font-weight-bold text-gray-800 mb-1">
                Detail Hasil Jasmani
            </h1>
            <p class="text-muted small mb-0">
                Informasi lengkap hasil tes jasmani
            </p>
        </div>

        <a href="<?= site_url('tryout/jasmani') ?>" class="btn btn-light border shadow-sm">
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <!-- ================= USER INFO ================= -->
    <div class="card shadow-sm border-0 rounded-lg mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="info-title">Nama</div>
                    <div class="info-value"><?= esc($jasmani['name'] ?? '-') ?></div>
                </div>
                <div class="col-md-4">
                    <div class="info-title">Email</div>
                    <div class="info-value"><?= esc($jasmani['email'] ?? '-') ?></div>
                </div>
                <div class="col-md-4">
                    <div class="info-title">Kategori</div>
                    <span class="badge badge-soft-<?= $jasmani['kategori'] === 'tni' ? 'success' : 'info' ?>">
                        <?= strtoupper($jasmani['kategori']) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= DATA DASAR ================= -->
    <div class="card shadow-sm border-0 rounded-lg mb-4">
        <div class="card-header bg-white font-weight-bold">Data Dasar</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="info-title">Jenis Kelamin</div>
                    <div class="info-value"><?= esc($jasmani['jenis_kelamin']) ?></div>
                </div>
                <div class="col-md-3">
                    <div class="info-title">Usia</div>
                    <div class="info-value"><?= $jasmani['usia'] ?? '-' ?></div>
                </div>
                <div class="col-md-3">
                    <div class="info-title">Tinggi</div>
                    <div class="info-value"><?= $jasmani['tinggi'] ? $jasmani['tinggi'] . ' cm' : '-' ?></div>
                </div>
                <div class="col-md-3">
                    <div class="info-title">Berat</div>
                    <div class="info-value"><?= $jasmani['berat'] ? $jasmani['berat'] . ' kg' : '-' ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= GARJAS A ================= -->
    <div class="card shadow-sm border-0 rounded-lg mb-4">
        <div class="card-header bg-white font-weight-bold">Garjas A</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="info-title">Lari 12 Menit</div>
                    <div class="info-value"><?= esc($jasmani['lari_12']) ?> meter</div>
                </div>
                <div class="col-md-4">
                    <div class="info-title">Nilai Garjas A</div>
                    <div class="info-value"><?= $jasmani['nilai_garjas_a'] ?? '-' ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= GARJAS B ================= -->
    <div class="card shadow-sm border-0 rounded-lg mb-4">
        <div class="card-header bg-white font-weight-bold">
            Garjas B (<?= strtoupper($jasmani['kategori']) ?>)
        </div>
        <div class="card-body">
            <div class="row">

                <?php if ($jasmani['kategori'] === 'tni'): ?>
                    <?php
                    $items = [
                        'pull_up'     => 'Pull Up',
                        'sit_up'      => 'Sit Up',
                        'push_up'     => 'Push Up',
                        'shuttle_run' => 'Shuttle Run',
                        'renang'      => 'Renang',
                    ];
                    ?>
                <?php else: ?>
                    <?php
                    $items = [
                        'pull_up'     => 'Pull Up',
                        'sit_up'      => 'Sit Up',
                        'push_up'     => 'Push Up',
                        'lunges'      => 'Lunges',
                        'shuttle_run' => 'Shuttle Run',
                    ];
                    ?>
                <?php endif; ?>

                <?php foreach ($items as $key => $label): ?>
                    <div class="col-md-4 mb-3">
                        <div class="info-title"><?= $label ?></div>
                        <div class="info-value"><?= $jasmani[$key] ?? '-' ?></div>
                    </div>
                <?php endforeach; ?>

                <div class="col-md-4">
                    <div class="info-title">Nilai Garjas B</div>
                    <div class="info-value font-weight-bold">
                        <?= $jasmani['nilai_garjas_b'] ?? '-' ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- ================= TOTAL ================= -->
    <div class="card shadow-sm border-0 rounded-lg">
        <div class="card-body text-center">
            <div class="total-title">Nilai Akhir</div>
            <div class="total-value"><?= $jasmani['nilai_total'] ?? '-' ?></div>
            <div class="text-muted small mt-1">
                Tanggal Tes: <?= date('d M Y', strtotime($jasmani['created_at'])) ?>
            </div>
        </div>
    </div>

</div>

<style>
    .info-title {
        font-size: 12px;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
    }

    .info-value {
        font-size: 15px;
        font-weight: 700;
        color: #343a40;
    }

    .total-title {
        font-size: 13px;
        text-transform: uppercase;
        color: #6c757d;
    }

    .total-value {
        font-size: 38px;
        font-weight: 800;
        color: #1cc88a;
    }

    .badge-soft-success {
        background: rgba(28, 200, 138, .15);
        color: #1cc88a;
        font-weight: 700;
    }

    .badge-soft-info {
        background: rgba(54, 185, 204, .15);
        color: #36b9cc;
        font-weight: 700;
    }
</style>

<?= $this->endSection(); ?>