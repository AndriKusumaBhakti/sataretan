<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- ================= HEADER ================= -->
    <div class="d-flex justify-content-between align-items-end mb-5">
        <div>
            <h1 class="h4 font-weight-bold text-gray-800 mb-1">
                Try Out <?= $kategori ? strtoupper($kategori) : '' ?>
            </h1>
            <p class="text-muted small mb-0">
                Pilih paket try out untuk memulai latihan
            </p>
        </div>

        <?php if ($isGuruOrAdmin): ?>
            <a href="<?= site_url('tryout/' . $kategori . '/tambah') ?>"
                class="btn btn-success rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Try Out
            </a>
        <?php endif; ?>
    </div>

    <!-- ================= LOADING ================= -->
    <div id="page-loading">
        <div class="loading-spinner"></div>
        <div class="loading-text">Memuat data try out...</div>
    </div>

    <!-- ================= CONTENT ================= -->
    <div class="row d-none" id="tryout-content">

        <?php if (empty($tryout)): ?>

            <!-- EMPTY STATE -->
            <div class="col-12">
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-folder-open"></i>
                    </div>
                    <h5>Belum Ada Try Out</h5>
                    <p>
                        Saat ini belum tersedia paket try out.<br>
                        Silakan kembali lagi nanti.
                    </p>
                </div>
            </div>

        <?php else: ?>

            <?php foreach ($tryout as $t): ?>
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">

                    <div class="tryout-card">

                        <!-- ADMIN ACTION -->
                        <?php if ($isGuruOrAdmin): ?>
                            <div class="tryout-actions">

                                <?php if ($t['status'] === 'draft'): ?>
                                    <form action="<?= site_url('tryout/' . $kategori . '/publish/' . $t['id']) ?>"
                                        method="post"
                                        onsubmit="return confirm('Aktifkan try out ini?')">
                                        <?= csrf_field() ?>
                                        <button title="Aktifkan">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($t['status'] === 'aktif'): ?>
                                    <form action="<?= site_url('tryout/' . $kategori . '/unpublish/' . $t['id']) ?>"
                                        method="post"
                                        onsubmit="return confirm('Nonaktifkan try out ini?')">
                                        <?= csrf_field() ?>
                                        <button title="Nonaktifkan">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <a href="<?= site_url('tryout/' . $kategori . '/edit/' . $t['id']) ?>" title="Edit">
                                    <i class="fas fa-pen"></i>
                                </a>

                                <form action="<?= site_url('tryout/' . $kategori . '/delete/' . $t['id']) ?>"
                                    method="post"
                                    onsubmit="return confirm('Hapus try out ini?')">
                                    <?= csrf_field() ?>
                                    <button title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                            </div>
                        <?php endif; ?>

                        <!-- ICON -->
                        <div class="tryout-icon">
                            <i class="fas fa-book"></i>
                        </div>

                        <!-- BADGE -->
                        <span class="badge badge-soft-success text-uppercase mb-2">
                            <?= esc($t['kategori']) ?>
                        </span>

                        <!-- TITLE -->
                        <h6 class="tryout-title">
                            <?= esc($t['judul']) ?>
                        </h6>

                        <!-- META -->
                        <div class="tryout-meta">
                            <div>
                                <i class="fas fa-list-ul"></i>
                                <?= $t['jumlah_soal'] ?> Soal
                            </div>
                            <div>
                                <i class="fas fa-clock"></i>
                                <?= $t['durasi'] ?> Menit
                            </div>
                        </div>

                        <!-- STAT -->
                        <div class="tryout-stat">
                            <div>
                                <i class="fas fa-users"></i>
                                <?= $t['peserta'] ?? 0 ?>
                            </div>
                            <div>
                                <i class="fas fa-chart-line"></i>
                                <?= isset($t['rata_nilai']) ? number_format($t['rata_nilai'], 1) : 0 ?>
                            </div>
                            <div>
                                <i class="fas fa-redo"></i>
                                <?= $t['attempt'] ?? 0 ?>
                            </div>
                        </div>

                        <div class="tryout-buttons mt-auto">
                            <a href="<?= site_url('tryout/' . $kategori . '/start/' . $t['id']) ?>"
                                class="btn btn-outline-success btn-sm rounded-pill">
                                <i class="fas fa-play mr-1"></i> Mulai Try Out
                            </a>
                            <?php if ($isGuruOrAdmin): ?>
                                <a href="<?= site_url('tryout/' . $kategori . '/nilai/' . $t['id']) ?>"
                                    class="btn btn-outline-secondary btn-sm rounded-pill">
                                    <i class="fas fa-list-alt mr-1"></i> Daftar Nilai
                                </a>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- ================= STYLE ================= -->
<style>
    /* ===== LOADING ===== */
    #page-loading {
        position: fixed;
        inset: 0;
        background: rgba(255, 255, 255, .96);
        z-index: 9999;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .loading-spinner {
        width: 46px;
        height: 46px;
        border: 5px solid #e9ecef;
        border-top: 5px solid #28a745;
        border-radius: 50%;
        animation: spin .9s linear infinite;
    }

    .loading-text {
        margin-top: 14px;
        font-size: 14px;
        font-weight: 600;
        color: #6c757d;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg)
        }
    }

    /* ===== EMPTY STATE ===== */
    .empty-state {
        background: #fff;
        border-radius: 24px;
        padding: 70px 20px;
        text-align: center;
        box-shadow: 0 14px 38px rgba(0, 0, 0, .08);
    }

    .empty-icon {
        width: 80px;
        height: 80px;
        margin: 0 auto 20px;
        border-radius: 50%;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        color: #adb5bd;
    }

    .empty-state h5 {
        font-weight: 700;
        margin-bottom: 6px;
    }

    .empty-state p {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 22px;
    }

    /* ===== CARD ===== */
    .tryout-card {
        height: 100%;
        background: #fff;
        border-radius: 20px;
        padding: 22px;
        display: flex;
        flex-direction: column;
        position: relative;
        box-shadow: 0 12px 30px rgba(0, 0, 0, .08);
        transition: .3s ease;
    }

    .tryout-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 22px 46px rgba(0, 0, 0, .14);
    }

    .tryout-icon {
        width: 58px;
        height: 58px;
        border-radius: 16px;
        background: linear-gradient(135deg, #28a745, #5dd28e);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-bottom: 14px;
    }

    .badge-soft-success {
        background: rgba(40, 167, 69, .12);
        color: #28a745;
        font-weight: 600;
    }

    .tryout-title {
        font-weight: 700;
        font-size: .95rem;
        line-height: 1.45em;
        margin: 12px 0 14px;
        min-height: 2.8em;
    }

    .tryout-meta {
        font-size: 13px;
        color: #6c757d;
        margin-bottom: 14px;
    }

    .tryout-meta div {
        margin-bottom: 4px;
    }

    .tryout-meta i {
        color: #28a745;
        margin-right: 6px;
    }

    /* ===== STAT ===== */
    .tryout-stat {
        display: flex;
        gap: 6px;
        margin-bottom: 16px;
    }

    .tryout-stat div {
        flex: 1;
        background: #f8f9fa;
        border-radius: 12px;
        padding: 6px;
        font-size: 12px;
        font-weight: 600;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    .tryout-stat i {
        color: #28a745;
    }

    /* ===== ACTION ===== */
    .tryout-actions {
        position: absolute;
        top: 14px;
        right: 14px;
        display: flex;
        gap: 8px;
        opacity: 0;
        transform: translateY(-6px);
        transition: .25s ease;
    }

    .tryout-card:hover .tryout-actions {
        opacity: 1;
        transform: none;
    }

    .tryout-actions a,
    .tryout-actions button {
        width: 34px;
        height: 34px;
        border: none;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 6px 16px rgba(0, 0, 0, .15);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #555;
    }

    .tryout-actions a:hover,
    .tryout-actions button:hover {
        background: #28a745;
        color: #fff;
    }

    .tryout-buttons {
        display: flex;
        gap: 8px;
    }

    .tryout-buttons .btn {
        flex: 1;
        white-space: nowrap;
    }

    @media (max-width: 575px) {
        .tryout-buttons {
            flex-direction: column;
        }
    }

    @media (hover: none) {
        .tryout-actions {
            opacity: 1;
            transform: none;
        }
    }
</style>

<!-- ================= SCRIPT ================= -->
<script>
    window.addEventListener('load', function() {
        document.getElementById('page-loading').style.display = 'none';
        document.getElementById('tryout-content').classList.remove('d-none');
    });
</script>

<?= $this->endSection(); ?>