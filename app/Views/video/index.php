<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- ================= HEADER ================= -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h4 font-weight-bold text-gray-800 mb-1">
                Video Pembelajaran <?= $kategori ? strtoupper($kategori) : '' ?>
            </h1>
            <small class="text-muted">Kumpulan video pembelajaran</small>
        </div>

        <?php if ($isGuruOrAdmin): ?>
            <a href="<?= base_url('video/' . $kategori . '/create') ?>"
                class="btn btn-success rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus mr-1"></i> Add Video
            </a>
        <?php endif; ?>
    </div>

    <!-- ================= LOADING ================= -->
    <div id="page-loading">
        <div class="loading-spinner"></div>
        <div class="loading-text">Memuat data...</div>
    </div>

    <!-- ================= CONTENT ================= -->
    <div class="row gx-3 gy-3 d-none" id="video-content">

        <?php if (empty($video)): ?>

            <div class="col-12">
                <div class="data-not-found">
                    <i class="fas fa-folder-open"></i>
                    <h5>Video Tidak Ditemukan</h5>
                    <p>Belum ada video pembelajaran untuk kategori ini.</p>
                </div>
            </div>

        <?php else: ?>

            <?php foreach ($video as $m): ?>
                <?php
                $icon  = 'fa-play-circle';
                $color = 'success';
                ?>

                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="video-card-binjas h-100">

                        <?php if ($isGuruOrAdmin): ?>
                            <div class="video-actions">
                                <a href="<?= base_url('video/' . $m['kategori'] . '/edit/' . $m['id']) ?>">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <a href="<?= base_url('video/delete/' . $m['id']) ?>"
                                    onclick="return confirm('Hapus video ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="video-icon bg-<?= $color ?>">
                            <i class="fas <?= $icon ?>"></i>
                        </div>

                        <span class="badge badge-soft-<?= $color ?> text-uppercase mb-2">
                            VIDEO
                        </span>

                        <h6 class="video-title">
                            <?= esc($m['judul']) ?>
                        </h6>

                        <a href="<?= site_url('video/' . $m['kategori'] . '/video/view/' . $m['id']) ?>"
                            class="btn btn-outline-success btn-sm mt-auto rounded-pill">
                            <i class="fas fa-eye mr-1"></i> Lihat Video
                        </a>

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
        background: rgba(255, 255, 255, .95);
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .loading-spinner {
        width: 44px;
        height: 44px;
        border: 4px solid #e9ecef;
        border-top: 4px solid #28a745;
        border-radius: 50%;
        animation: spin .8s linear infinite;
    }

    .loading-text {
        margin-top: 12px;
        font-weight: 600;
        color: #6c757d;
        font-size: 14px;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg)
        }
    }

    /* ===== DATA NOT FOUND ===== */
    .data-not-found {
        background: #fff;
        border-radius: 20px;
        padding: 60px 20px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .08);
    }

    /* ===== CARD ===== */
    .video-card-binjas {
        background: #fff;
        border-radius: 18px;
        padding: 18px;
        box-shadow: 0 8px 22px rgba(0, 0, 0, .08);
        display: flex;
        flex-direction: column;
        position: relative;
        transition: all .25s ease;
    }

    .video-card-binjas:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 34px rgba(0, 0, 0, .14);
    }

    /* ===== ICON ===== */
    .video-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 22px;
        margin-bottom: 12px;
    }

    /* ===== TITLE ===== */
    .video-title {
        font-weight: 700;
        font-size: .92rem;
        line-height: 1.45em;
        margin: 10px 0 14px;
        min-height: 2.6em;
    }

    /* ===== ACTIONS ===== */
    .video-actions {
        position: absolute;
        top: 12px;
        right: 12px;
        display: flex;
        gap: 8px;
        opacity: 0;
        transform: translateY(-6px);
        transition: .25s ease;
    }

    .video-card-binjas:hover .video-actions {
        opacity: 1;
        transform: translateY(0);
    }

    .video-actions a {
        width: 32px;
        height: 32px;
        background: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #555;
        box-shadow: 0 6px 14px rgba(0, 0, 0, .15);
        font-size: 13px;
    }

    .video-actions a:hover {
        background: #28a745;
        color: #fff;
    }

    /* ===== BUTTON ===== */
    .btn-outline-success {
        font-weight: 600;
        border-width: 2px;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 576px) {
        .video-title {
            min-height: auto;
        }
    }
</style>

<!-- ================= SCRIPT ================= -->
<script>
    window.addEventListener('load', function() {
        document.getElementById('page-loading').style.display = 'none';
        document.getElementById('video-content').classList.remove('d-none');
    });
</script>

<?= $this->endSection(); ?>