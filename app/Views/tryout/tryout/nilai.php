<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- ================= HEADER ================= -->
    <div class="page-header mb-4">
        <div>
            <h1 class="h4 font-weight-bold mb-1">Daftar Nilai Try Out</h1>
            <p class="text-muted small mb-0">Rekap hasil pengerjaan peserta</p>
        </div>

        <div class="header-actions">
            <?php if (isset($hasOnline) && !$hasOnline): ?>
                <a href="<?= site_url("tryout/" . $kategori . "/nilai/tambah/" . $tryoutId) ?>"
                    class="btn btn-success rounded-pill px-4 shadow-sm">
                    <i class="fas fa-plus mr-1"></i> Tambah Nilai
                </a>
            <?php endif; ?>
            <div class="btn-group">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                    data-toggle="dropdown">
                    <i class="fas fa-download mr-1"></i> Export
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow-sm">
                    <a class="dropdown-item text-success"
                        href="<?= site_url("tryout/" . $kategori . "/nilai/export-excel/" . $tryoutId) ?>">
                        <i class="fas fa-file-excel mr-2"></i> Excel
                    </a>
                    <a class="dropdown-item text-danger"
                        href="<?= site_url("tryout/" . $kategori . "/nilai/export-pdf/" . $tryoutId) ?>">
                        <i class="fas fa-file-pdf mr-2"></i> PDF
                    </a>
                </div>
            </div>

            <a href="<?= site_url('tryout/' . $kategori) ?>"
                class="btn btn-sm btn-outline-secondary"
                title="Kembali">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>

    <!-- ================= STAT CARD ================= -->
    <?php if (!empty($nilai)): ?>
        <?php
        $selesai = array_filter($nilai, fn($n) => $n['status'] === 'finished');
        $ongoing = count($nilai) - count($selesai);
        $rata2   = array_sum(array_column($nilai, 'skor_akhir')) / count($nilai);
        ?>
        <div class="row mb-4">
            <?php
            $stats = [
                ['Peserta', count($nilai), 'users', 'primary'],
                ['Selesai', count($selesai), 'check-circle', 'success'],
                ['Ongoing', $ongoing, 'clock', 'warning'],
                ['Rata-rata', number_format($rata2, 1), 'chart-line', 'info'],
            ];
            ?>
            <?php foreach ($stats as [$label, $value, $icon, $type]): ?>
                <div class="col-xl-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card stat-<?= $type ?>">
                        <div class="stat-body">
                            <div class="stat-title"><?= $label ?></div>
                            <div class="stat-value"><?= $value ?></div>
                            <i class="fas fa-<?= $icon ?> stat-icon"></i>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- ================= CONTENT ================= -->
    <?php if (empty($nilai)): ?>

        <div class="empty-state">
            <i class="fas fa-chart-bar empty-icon"></i>
            <h5>Belum Ada Nilai</h5>
            <p class="text-muted mb-0">Peserta belum menyelesaikan try out ini</p>
        </div>

    <?php else: ?>

        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 responsive-table" id="nilaiTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Mulai</th>
                                <th>Selesai</th>
                                <th class="text-center">Nilai</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($nilai as $i => $n): ?>
                                <?php
                                $badge = $n['skor_akhir'] < 60 ? 'danger' : ($n['skor_akhir'] < 75 ? 'warning' : 'success');
                                ?>
                                <tr>
                                    <td data-label="#"> <?= $i + 1 ?> </td>
                                    <td data-label="Nama" class="font-weight-bold"><?= esc($n['nama']) ?></td>
                                    <td data-label="Mulai"><?= date('d M Y H:i', strtotime($n['started_at'])) ?></td>
                                    <td data-label="Selesai">
                                        <?= $n['finished_at']
                                            ? date('d M Y H:i', strtotime($n['finished_at']))
                                            : '-' ?>
                                    </td>
                                    <td data-label="Nilai" class="text-center">
                                        <span class="badge badge-<?= $badge ?> badge-pill px-3 py-2">
                                            <?= number_format($n['skor_akhir'], 2) ?>
                                        </span>
                                    </td>
                                    <td data-label="Status" class="text-center">
                                        <span class="badge badge-soft-<?= $n['status'] === 'finished' ? 'success' : 'warning' ?>">
                                            <?= ucfirst($n['status']) ?>
                                        </span>
                                    </td>
                                    <td data-label="Aksi" class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light border dropdown-toggle"
                                                data-toggle="dropdown">
                                                Aksi
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right shadow-sm">
                                                <a class="dropdown-item"
                                                    href="<?= site_url("tryout/" . $kategori . "/nilai/detail/" . $n['id']) ?>">
                                                    <i class="fas fa-eye mr-2 text-primary"></i> Detail
                                                </a>
                                                <a class="dropdown-item text-danger"
                                                    href="<?= site_url("tryout/" . $kategori . "/nilai/reset/" . $n['id']) ?>"
                                                    onclick="return confirm('Reset jawaban peserta ini?')">
                                                    <i class="fas fa-trash mr-2"></i> Reset
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php endif; ?>
</div>

<!-- ================= STYLE ================= -->
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .header-actions {
        display: flex;
        gap: 8px;
    }

    .stat-card {
        border-radius: 16px;
        color: #fff;
        height: 100%;
        transition: .3s;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 32px rgba(0, 0, 0, .15);
    }

    .stat-body {
        padding: 20px;
        position: relative;
    }

    .stat-title {
        font-size: 12px;
        opacity: .85;
        text-transform: uppercase;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
    }

    .stat-icon {
        position: absolute;
        right: 20px;
        bottom: 20px;
        font-size: 40px;
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

    .badge-soft-success {
        background: rgba(40, 167, 69, .15);
        color: #28a745;
        font-weight: 600;
    }

    .badge-soft-warning {
        background: rgba(255, 193, 7, .18);
        color: #856404;
        font-weight: 600;
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

    /* ===== MOBILE TABLE ===== */
    @media (max-width: 768px) {
        .responsive-table thead {
            display: none;
        }

        .responsive-table tr {
            display: block;
            margin-bottom: 12px;
            border-bottom: 1px solid #eee;
        }

        .responsive-table td {
            display: flex;
            justify-content: space-between;
            padding: .6rem .8rem;
            border: none;
        }

        .responsive-table td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #6c757d;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.jQuery && $('#nilaiTable').length) {
            $('#nilaiTable').DataTable({
                pageLength: 10,
                responsive: true,
                order: [
                    [4, 'desc']
                ]
            });
        }
    });
</script>

<?= $this->endSection(); ?>