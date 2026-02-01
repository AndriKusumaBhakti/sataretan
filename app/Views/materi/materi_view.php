<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<div class="container-fluid px-3 px-md-4 pb-4">

    <!-- ================= HEADER ================= -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h4 font-weight-bold text-gray-800 mb-1">
                Materi <?= $kategori ? strtoupper($kategori) : '' ?>
            </h1>
            <p class="text-muted small mb-0">
                Kumpulan materi pembelajaran
            </p>
        </div>

        <?php if ($isGuruOrAdmin): ?>
            <a href="<?= base_url('materi/' . $kategori . '/create') ?>"
                class="btn btn-success rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus mr-1"></i> Tambah Materi
            </a>
        <?php endif; ?>
    </div>

    <!-- ================= LOADING ================= -->
    <div id="page-loading">
        <div class="loading-spinner"></div>
        <div class="text-muted mt-2 small">Memuat data...</div>
    </div>

    <!-- ================= CONTENT ================= -->
    <div class="row gx-3 gy-3 d-none" id="materi-content">

        <?php if (empty($materi)): ?>

            <div class="col-12">
                <div class="empty-state">
                    <i class="fas fa-folder-open fa-2x mb-3 text-success"></i>
                    <h5 class="mb-1">Materi Tidak Ditemukan</h5>
                    <p class="text-muted mb-0">Belum ada materi untuk kategori ini.</p>
                </div>
            </div>

        <?php else: ?>

            <?php foreach ($materi as $m): ?>
                <?php
                $icon  = 'fa-file-alt';
                $color = 'success';

                if ($m['tipe'] === 'video') {
                    $icon = 'fa-play-circle';
                } elseif ($m['tipe'] === 'pdf') {
                    $icon = 'fa-file-pdf';
                    $color = 'danger';
                } elseif ($m['tipe'] === 'doc') {
                    $icon = 'fa-file-word';
                    $color = 'primary';
                }
                ?>

                <div class="col-xl-3 col-lg-4 col-md-6">

                    <div class="materi-card h-100 position-relative">

                        <?php if ($isGuruOrAdmin): ?>
                            <div class="materi-actions">
                                <a href="<?= base_url('materi/' . $m['kategori'] . '/edit/' . $m['id']) ?>" title="Edit">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <a href="<?= base_url('materi/delete/' . $m['id']) ?>"
                                    onclick="return confirm('Hapus materi ini?')" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="materi-icon bg-<?= $color ?>">
                            <i class="fas <?= $icon ?>"></i>
                        </div>

                        <span class="badge badge-soft-<?= $color ?> mb-2 align-self-start">
                            <?= strtoupper($m['tipe']) ?>
                        </span>

                        <h6 class="materi-title">
                            <?= esc($m['judul']) ?>
                        </h6>

                        <div class="materi-buttons mt-auto">
                            <a href="<?= site_url('materi/' . $m['kategori'] . '/' . $m['tipe'] . '/view/' . $m['id']) ?>"
                                class="btn btn-outline-success btn-sm rounded-pill w-100">
                                <i class="fas fa-eye mr-1"></i> Lihat Materi
                            </a>
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

    /* ===== EMPTY STATE ===== */
    .empty-state {
        background: #fff;
        border-radius: 24px;
        padding: 60px 20px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .08)
    }

    /* ===== CARD ===== */
    .materi-card {
        background: #fff;
        border-radius: 18px;
        padding: 18px;
        display: flex;
        flex-direction: column;
        box-shadow: 0 10px 26px rgba(0, 0, 0, .08);
        transition: .25s ease
    }

    .materi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 18px 40px rgba(0, 0, 0, .14)
    }

    /* ===== ICON ===== */
    .materi-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 20px;
        margin-bottom: 12px
    }

    /* ===== TITLE ===== */
    .materi-title {
        font-weight: 700;
        font-size: .95rem;
        line-height: 1.35;
        margin-bottom: 14px;
        min-height: 40px;
        color: #343a40
    }

    /* ===== BADGE ===== */
    .badge-soft-success,
    .badge-soft-danger,
    .badge-soft-primary {
        font-weight: 600;
        border-radius: 50px;
        padding: 6px 12px;
        font-size: 11px
    }

    .badge-soft-success {
        background: rgba(40, 167, 69, .12);
        color: #28a745
    }

    .badge-soft-danger {
        background: rgba(231, 74, 59, .12);
        color: #e74a3b
    }

    .badge-soft-primary {
        background: rgba(78, 115, 223, .12);
        color: #4e73df
    }

    /* ===== ACTIONS ===== */
    .materi-actions {
        position: absolute;
        top: 12px;
        right: 12px;
        display: flex;
        gap: 6px;
        opacity: 0;
        transition: .2s
    }

    .materi-card:hover .materi-actions {
        opacity: 1
    }

    .materi-actions a {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 14px rgba(0, 0, 0, .15);
        color: #555;
        font-size: 13px
    }

    .materi-actions a:hover {
        background: #28a745;
        color: #fff
    }

    /* ===== BUTTON ===== */
    .materi-buttons {
        display: grid;
        grid-template-columns: 1fr
    }

    /* ===== RESPONSIVE ===== */
    @media(max-width:575px) {
        .materi-actions {
            opacity: 1
        }
    }

    @media(min-width:768px) {
        #materi-content {
            row-gap: 16px
        }
    }
</style>

<!-- ================= SCRIPT ================= -->
<script>
    window.addEventListener('load', () => {
        document.getElementById('page-loading').style.display = 'none';
        document.getElementById('materi-content').classList.remove('d-none');
    });
</script>

<?= $this->endSection(); ?>