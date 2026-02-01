<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<div class="container-fluid px-3 px-md-4 pb-4">

    <!-- ================= HEADER ================= -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
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
        <div class="text-muted mt-2 small">Memuat data try out...</div>
    </div>

    <!-- ================= CONTENT ================= -->
    <div class="row gx-3 gy-3 d-none" id="tryout-content">

        <?php if (empty($tryout)): ?>

            <div class="col-12">
                <div class="empty-state">
                    <i class="fas fa-folder-open fa-2x mb-3 text-success"></i>
                    <h5 class="mb-1">Belum Ada Try Out</h5>
                    <p class="text-muted mb-0">Silakan kembali lagi nanti.</p>
                </div>
            </div>

        <?php else: ?>

            <?php foreach ($tryout as $t): ?>
                <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">

                    <div class="tryout-card h-100 position-relative">

                        <!-- ADMIN ACTION -->
                        <?php if ($isGuruOrAdmin): ?>
                            <div class="tryout-actions">
                                <?php if ($t['status'] === 'draft'): ?>
                                    <form action="<?= site_url('tryout/' . $kategori . '/publish/' . $t['id']) ?>" method="post">
                                        <?= csrf_field() ?>
                                        <button title="Aktifkan"><i class="fas fa-check"></i></button>
                                    </form>
                                <?php endif; ?>

                                <?php if ($t['status'] === 'aktif'): ?>
                                    <form action="<?= site_url('tryout/' . $kategori . '/unpublish/' . $t['id']) ?>" method="post">
                                        <?= csrf_field() ?>
                                        <button title="Nonaktifkan"><i class="fas fa-ban"></i></button>
                                    </form>
                                <?php endif; ?>

                                <a href="<?= site_url('tryout/' . $kategori . '/edit/' . $t['id']) ?>" title="Edit">
                                    <i class="fas fa-pen"></i>
                                </a>

                                <form action="<?= site_url('tryout/' . $kategori . '/delete/' . $t['id']) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <button title="Hapus"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        <?php endif; ?>

                        <!-- ICON -->
                        <div class="tryout-icon">
                            <i class="fas fa-book"></i>
                        </div>

                        <span class="badge badge-soft-success mb-2">
                            <?= esc($t['kategori']) ?>
                        </span>

                        <h6 class="tryout-title">
                            <?= esc($t['judul']) ?>
                        </h6>

                        <div class="tryout-meta">
                            <?php if (!empty($t['jumlah_soal'])): ?>
                                <div><i class="fas fa-list-ul"></i><?= $t['jumlah_soal'] ?> Soal</div>
                            <?php endif; ?>
                            <?php if (!empty($t['durasi'])): ?>
                                <div><i class="fas fa-clock"></i><?= $t['durasi'] ?> Menit</div>
                            <?php endif; ?>
                            <?php if (!empty($t['tanggal_mulai'])): ?>
                                <div>
                                    <i class="fas fa-calendar-alt"></i>
                                    <?= date('d M Y', strtotime($t['tanggal_mulai'])) ?> –
                                    <?= date('d M Y', strtotime($t['tanggal_selesai'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="tryout-stat">
                            <div><i class="fas fa-users"></i><span><?= $t['peserta'] ?? 0 ?></span></div>
                            <div><i class="fas fa-chart-line"></i><span><?= number_format($t['rata_nilai'] ?? 0, 1) ?></span></div>
                            <div><i class="fas fa-redo"></i><span><?= $t['attempt'] ?? 0 ?></span></div>
                        </div>

                        <div class="tryout-buttons mt-auto">
                            <a href="<?= site_url('tryout/' . $kategori . '/start/' . $t['id']) ?>"
                                class="btn btn-outline-success btn-sm rounded-pill w-100">
                                <i class="fas fa-play mr-1"></i> Mulai
                            </a>

                            <?php if ($isGuruOrAdmin): ?>
                                <a href="<?= site_url('tryout/' . $kategori . '/nilai/' . $t['id']) ?>"
                                    class="btn btn-outline-secondary btn-sm rounded-pill w-100">
                                    <i class="fas fa-list-alt mr-1"></i> Nilai
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
        background: #fff;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 9999
    }

    .loading-spinner {
        width: 42px;
        height: 42px;
        border: 5px solid #e9ecef;
        border-top-color: #28a745;
        border-radius: 50%;
        animation: spin .8s linear infinite
    }

    @keyframes spin {
        to {
            transform: rotate(360deg)
        }
    }

    /* ===== CARD ===== */
    .tryout-card {
        background: #fff;
        border-radius: 18px;
        padding: 18px;
        display: flex;
        flex-direction: column;
        box-shadow: 0 10px 26px rgba(0, 0, 0, .08);
        transition: .25s ease
    }

    .tryout-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 40px rgba(0, 0, 0, .14)
    }

    /* ===== ICON ===== */
    .tryout-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: linear-gradient(135deg, #28a745, #6fe0a1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 20px;
        margin-bottom: 12px
    }

    /* ===== TITLE ===== */
    .tryout-title {
        font-size: .95rem;
        font-weight: 700;
        line-height: 1.35;
        margin-bottom: 10px;
        min-height: 40px
    }

    /* ===== META ===== */
    .tryout-meta {
        font-size: 13px;
        color: #6c757d;
        margin-bottom: 12px
    }

    .tryout-meta div {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 4px
    }

    .tryout-meta i {
        color: #28a745
    }

    /* ===== STAT ===== */
    .tryout-stat {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 6px;
        margin-bottom: 14px
    }

    .tryout-stat div {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 6px 4px;
        font-size: 12px;
        font-weight: 600;
        text-align: center
    }

    .tryout-stat i {
        display: block;
        margin-bottom: 2px;
        color: #28a745
    }

    /* ===== ACTIONS ===== */
    .tryout-actions {
        position: absolute;
        top: 12px;
        right: 12px;
        display: flex;
        gap: 6px;
        opacity: 0;
        transition: .2s
    }

    .tryout-card:hover .tryout-actions {
        opacity: 1
    }

    .tryout-actions button,
    .tryout-actions a {
        width: 30px;
        height: 30px;
        border: none;
        border-radius: 50%;
        background: #fff;
        box-shadow: 0 6px 14px rgba(0, 0, 0, .15);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px
    }

    /* ===== BUTTONS ===== */
    .tryout-buttons {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px
    }

    /* ===== EMPTY ===== */
    .empty-state {
        background: #fff;
        border-radius: 24px;
        padding: 60px 20px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .08)
    }

    /* ===== RESPONSIVE ===== */
    @media(max-width:575px) {
        .tryout-buttons {
            grid-template-columns: 1fr
        }

        .tryout-actions {
            opacity: 1
        }
    }

    @media(min-width:768px) {
        #tryout-content {
            row-gap: 16px
        }
    }
</style>

<script>
    window.addEventListener('load', () => {
        document.getElementById('page-loading').style.display = 'none';
        document.getElementById('tryout-content').classList.remove('d-none');
    });
</script>

<?= $this->endSection(); ?>