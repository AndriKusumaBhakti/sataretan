<?= $this->extend('default/layout-template') ?>
<?= $this->section('content') ?>

<?php
$jasmaniData = $jasmani ?? [];

$totalData = count($jasmaniData);
$totalTni = count(array_filter($jasmaniData, function ($j) {
    return ($j['kategori'] ?? '') === 'tni';
}));
$totalPolri = count(array_filter($jasmaniData, function ($j) {
    return ($j['kategori'] ?? '') === 'polri';
}));
$totalBulanIni = count(array_filter($jasmaniData, function ($j) {
    return date('Y-m', strtotime($j['created_at'])) === date('Y-m');
}));
?>

<div class="container-fluid">

    <!-- ================= HEADER ================= -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 font-weight-bold text-gray-800 mb-1">
                Hasil Tes Jasmani
            </h1>
            <p class="text-muted small mb-0">
                Rekap dan monitoring hasil jasmani TNI & POLRI
            </p>
        </div>

        <a href="<?= site_url('tryout/jasmani/create') ?>"
            class="btn btn-success shadow-sm px-4">
            <i class="fas fa-plus mr-1"></i> Hitung Baru
        </a>
    </div>

    <!-- ================= STATISTIK ================= -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card stat-primary">
                <div class="card-body">
                    <div class="stat-title">Total Data</div>
                    <div class="stat-value"><?= $totalData ?></div>
                    <i class="fas fa-clipboard-list stat-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card stat-success">
                <div class="card-body">
                    <div class="stat-title">TNI</div>
                    <div class="stat-value"><?= $totalTni ?></div>
                    <i class="fas fa-shield-alt stat-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card stat-info">
                <div class="card-body">
                    <div class="stat-title">POLRI</div>
                    <div class="stat-value"><?= $totalPolri ?></div>
                    <i class="fas fa-user-shield stat-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card stat-warning">
                <div class="card-body">
                    <div class="stat-title">Bulan Ini</div>
                    <div class="stat-value"><?= $totalBulanIni ?></div>
                    <i class="fas fa-calendar-alt stat-icon"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= CONTENT ================= -->
    <?php if (empty($jasmaniData)): ?>

        <div class="empty-state">
            <i class="fas fa-dumbbell empty-icon"></i>
            <h5>Belum Ada Data Jasmani</h5>
            <p class="text-muted">
                Silakan lakukan <b>Hitung Baru</b> untuk mulai menambahkan data
            </p>
        </div>

    <?php else: ?>

        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="jasmaniTable">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Kategori</th>
                                <th>JK</th>
                                <th>Usia</th>
                                <th>Lari 12 Menit</th>
                                <th>Nilai Garjas B</th>
                                <th>Tanggal</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($jasmaniData as $i => $j): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>

                                    <td>
                                        <div class="font-weight-bold"><?= esc($j['name']) ?></div>
                                        <small class="text-muted"><?= esc($j['email']) ?></small>
                                    </td>

                                    <td>
                                        <span class="badge badge-soft-<?= $j['kategori'] === 'tni' ? 'success' : 'info' ?>">
                                            <?= strtoupper($j['kategori']) ?>
                                        </span>
                                    </td>

                                    <td><?= esc($j['jenis_kelamin']) ?></td>
                                    <td><?= $j['usia'] ?? '-' ?></td>
                                    <td><?= (int) $j['lari_12'] ?> m</td>
                                    <td><?= $j['nilai_garjas_b'] ?? '-' ?></td>

                                    <td><?= date('d M Y', strtotime($j['created_at'])) ?></td>

                                    <td class="text-center">
                                        <a href="<?= site_url('tryout/jasmani/detail/' . $j['id']) ?>"
                                            class="btn btn-sm btn-light border"
                                            title="Detail">
                                            <i class="fas fa-eye text-info"></i>
                                        </a>

                                        <form action="<?= site_url('tryout/jasmani/remove/' . $j['id']) ?>"
                                            method="get"
                                            class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button class="btn btn-sm btn-light border"
                                                onclick="return confirm('Yakin ingin menghapus data ini?')"
                                                title="Hapus">
                                                <i class="fas fa-trash text-danger"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php endif ?>

</div>

<!-- ================= STYLE ================= -->
<style>
    .stat-card {
        border-radius: 16px;
        color: #fff;
        position: relative;
        overflow: hidden;
        transition: .3s ease;
    }

    .stat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, .15);
    }

    .stat-title {
        font-size: 12px;
        text-transform: uppercase;
        opacity: .85;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
    }

    .stat-icon {
        position: absolute;
        right: 20px;
        bottom: 20px;
        font-size: 42px;
        opacity: .25;
    }

    .stat-primary {
        background: linear-gradient(135deg, #4e73df, #224abe);
    }

    .stat-success {
        background: linear-gradient(135deg, #1cc88a, #13855c);
    }

    .stat-info {
        background: linear-gradient(135deg, #36b9cc, #258391);
    }

    .stat-warning {
        background: linear-gradient(135deg, #f6c23e, #dda20a);
    }

    .empty-state {
        background: #fff;
        border-radius: 18px;
        padding: 80px 20px;
        text-align: center;
        box-shadow: 0 14px 38px rgba(0, 0, 0, .08);
    }

    .empty-icon {
        font-size: 56px;
        color: #d1d3e2;
        margin-bottom: 12px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.jQuery && $('#jasmaniTable').length) {
            $('#jasmaniTable').DataTable({
                pageLength: 10,
                responsive: true,
                order: [
                    [7, 'desc']
                ]
            });
        }
    });
</script>

<?= $this->endSection() ?>