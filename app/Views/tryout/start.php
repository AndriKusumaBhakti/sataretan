<?= $this->extend('default/layout-template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <div class="row justify-content-center">
        <div class="col-xl-5 col-lg-6 col-md-8">

            <!-- ================= HEADER ================= -->
            <div class="text-center mb-5">
                <span class="badge badge-success badge-pill px-4 py-2 mb-3 shadow-sm">
                    TRY OUT
                </span>

                <h3 class="font-weight-bold text-gray-800 mb-1">
                    <?= esc($tryout['judul']) ?>
                </h3>

                <p class="text-muted small mb-0">
                    Periksa detail try out sebelum memulai pengerjaan
                </p>
            </div>

            <!-- ================= CARD ================= -->
            <div class="tryout-detail-card">
                <div class="card-body p-4">

                    <!-- INFO -->
                    <div class="row text-center mb-4">

                        <div class="col-4">
                            <div class="stat-card">
                                <div class="stat-icon bg-gradient-success">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <div class="stat-label">Kategori</div>
                                <div class="stat-value text-uppercase">
                                    <?= esc($tryout['kategori']) ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="stat-card">
                                <div class="stat-icon bg-gradient-info">
                                    <i class="fas fa-list-ul"></i>
                                </div>
                                <div class="stat-label">Soal</div>
                                <div class="stat-value">
                                    <?= $jumlahSoalTersedia ?> / <?= $tryout['jumlah_soal'] ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="stat-card">
                                <div class="stat-icon bg-gradient-warning">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="stat-label">Durasi</div>
                                <div class="stat-value">
                                    <?= $tryout['durasi'] ?> Menit
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- ================= STATUS ================= -->
                    <?php if (!$soalSiap): ?>
                        <div class="status-box status-warning mb-4">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Soal belum lengkap.<br>
                            Minimal <b><?= $tryout['jumlah_soal'] ?></b> soal diperlukan
                        </div>
                    <?php else: ?>
                        <div class="status-box status-success mb-4">
                            <i class="fas fa-check-circle mr-2"></i>
                            Try out siap dikerjakan
                        </div>
                    <?php endif; ?>

                    <!-- ================= ADMIN ================= -->
                    <?php if ($isGuruOrAdmin): ?>
                        <a href="<?= site_url('tryout/' . $kategori . '/' . $tryout['id'] . '/soal') ?>"
                            class="btn btn-outline-success btn-block rounded-pill mb-3 btn-admin">
                            <i class="fas fa-cogs mr-2"></i>
                            Kelola Soal
                        </a>
                    <?php endif; ?>

                    <!-- ================= START ================= -->
                    <?php if (!$soalSiap): ?>
                        <button class="btn btn-secondary btn-block btn-lg rounded-pill" disabled>
                            <i class="fas fa-lock mr-2"></i>
                            Try Out Belum Tersedia
                        </button>
                    <?php else: ?>
                        <a href="<?= site_url('tryout/' . $kategori . '/pengerjaan/' . $tryout['id'] . '/1') ?>"
                            class="btn btn-success btn-block btn-lg rounded-pill btn-start">
                            <i class="fas fa-play mr-2"></i>
                            Mulai Try Out
                        </a>
                    <?php endif; ?>

                    <p class="text-center text-muted small mt-3 mb-0">
                        Waktu akan langsung berjalan setelah try out dimulai
                    </p>

                </div>
            </div>

        </div>
    </div>

</div>

<!-- ================= STYLE ================= -->
<style>
    /* CARD */
    .tryout-detail-card {
        background: linear-gradient(180deg, #ffffff, #f9fbfd);
        border-radius: 24px;
        box-shadow: 0 14px 38px rgba(0, 0, 0, .08);
        transition: .35s ease;
    }

    .tryout-detail-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 24px 55px rgba(0, 0, 0, .15);
    }

    /* STAT */
    .stat-card {
        padding: 14px 6px;
    }

    .stat-icon {
        width: 54px;
        height: 54px;
        border-radius: 50%;
        margin: 0 auto 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 20px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, .18);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745, #5dd28e);
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #17a2b8, #6fd3e6);
    }

    .bg-gradient-warning {
        background: linear-gradient(135deg, #ffc107, #ffda6a);
    }

    .stat-label {
        font-size: 12px;
        color: #6c757d;
    }

    .stat-value {
        font-weight: 700;
        font-size: 16px;
        color: #343a40;
    }

    /* STATUS */
    .status-box {
        border-radius: 16px;
        padding: 14px 18px;
        font-size: 14px;
        text-align: center;
    }

    .status-warning {
        background: #fff3cd;
        color: #856404;
    }

    .status-success {
        background: #e6f4ea;
        color: #1e7e34;
    }

    /* BUTTON */
    .btn-start {
        font-weight: 600;
        letter-spacing: .4px;
        transition: .25s ease;
    }

    .btn-start:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 30px rgba(40, 167, 69, .35);
    }

    .btn-admin {
        font-weight: 500;
    }
</style>

<?= $this->endSection(); ?>