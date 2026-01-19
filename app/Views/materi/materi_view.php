<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- ================= HEADER ================= -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="h4 font-weight-bold text-gray-800 mb-1">
                Materi <?= $kategori ? strtoupper($kategori) : '' ?>
            </h1>
            <small class="text-muted">Kumpulan materi pembelajaran</small>
        </div>

        <?php if ($isGuruOrAdmin): ?>
            <a href="<?= base_url('materi/' . $kategori . '/create') ?>"
                class="btn btn-success rounded-pill px-4 shadow-sm d-flex align-items-center gap-2">
                <i class="fas fa-plus"></i> Add Materi
            </a>
        <?php endif; ?>
    </div>

    <!-- ================= LOADING ================= -->
    <div id="page-loading">
        <div class="loading-spinner"></div>
        <div class="loading-text">Memuat data...</div>
    </div>

    <!-- ================= CONTENT ================= -->
    <div class="row d-none" id="materi-content">

        <?php if (empty($materi)): ?>

            <!-- ===== DATA NOT FOUND ===== -->
            <div class="col-12">
                <div class="data-not-found">
                    <i class="fas fa-folder-open"></i>
                    <h5>Materi Tidak Ditemukan</h5>
                    <p>Belum ada materi untuk kategori ini.</p>
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

                <div class="col-xl-3 col-lg-4 col-md-6 mb-4 d-flex">
                    <div class="materi-card-binjas w-100">

                        <?php if ($isGuruOrAdmin): ?>
                            <div class="materi-actions">
                                <a href="<?= base_url('materi/' . $m['kategori'] . '/edit/' . $m['id']) ?>">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <a href="<?= base_url('materi/delete/' . $m['id']) ?>"
                                    onclick="return confirm('Hapus materi ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        <?php endif; ?>

                        <div class="materi-icon bg-<?= $color ?>">
                            <i class="fas <?= $icon ?>"></i>
                        </div>

                        <span class="badge badge-soft-<?= $color ?> text-uppercase mb-2 align-self-start">
                            <?= strtoupper($m['tipe']) ?>
                        </span>

                        <h6 class="materi-title">
                            <?= esc($m['judul']) ?>
                        </h6>

                        <div class="mt-auto pt-3">
                            <a href="<?= site_url('materi/' . $m['kategori'] . '/' . $m['tipe'] . '/view/' . $m['id']) ?>"
                                class="btn btn-outline-success btn-sm rounded-pill btn-block">
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
        background: rgba(255, 255, 255, .96);
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .loading-spinner {
        width: 48px;
        height: 48px;
        border: 5px solid #e9ecef;
        border-top: 5px solid #28a745;
        border-radius: 50%;
        animation: spin .9s linear infinite;
    }

    .loading-text {
        margin-top: 14px;
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
        border-radius: 24px;
        padding: 80px 20px;
        text-align: center;
        box-shadow: 0 14px 34px rgba(0, 0, 0, .08);
    }

    .data-not-found i {
        font-size: 60px;
        color: #dee2e6;
        margin-bottom: 18px;
    }

    .data-not-found h5 {
        font-weight: 700;
        color: #495057;
        margin-bottom: 6px;
    }

    .data-not-found p {
        color: #6c757d;
        font-size: 14px;
    }

    /* ===== CARD ===== */
    .materi-card-binjas {
        background: #fff;
        border-radius: 20px;
        padding: 22px;
        box-shadow: 0 12px 32px rgba(0, 0, 0, .08);
        display: flex;
        flex-direction: column;
        position: relative;
        transition: .3s ease;
        min-height: 100%;
    }

    .materi-card-binjas:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 46px rgba(0, 0, 0, .15);
    }

    /* ===== ICON ===== */
    .materi-icon {
        width: 58px;
        height: 58px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 22px;
        margin-bottom: 14px;
    }

    /* ===== TITLE ===== */
    .materi-title {
        font-weight: 700;
        color: #343a40;
        line-height: 1.4;
        margin-bottom: 12px;
        font-size: 15px;
    }

    /* ===== BADGE ===== */
    .badge-soft-success {
        background: rgba(40, 167, 69, .12);
        color: #28a745;
        font-weight: 600;
        border-radius: 50px;
        padding: 6px 12px;
        font-size: 11px;
    }

    .badge-soft-danger {
        background: rgba(231, 74, 59, .12);
        color: #e74a3b;
        font-weight: 600;
        border-radius: 50px;
        padding: 6px 12px;
        font-size: 11px;
    }

    .badge-soft-primary {
        background: rgba(78, 115, 223, .12);
        color: #4e73df;
        font-weight: 600;
        border-radius: 50px;
        padding: 6px 12px;
        font-size: 11px;
    }

    /* ===== ACTIONS ===== */
    .materi-actions {
        position: absolute;
        top: 16px;
        right: 16px;
        display: flex;
        gap: 8px;
        opacity: 0;
        transform: translateY(-6px);
        transition: .25s ease;
    }

    .materi-card-binjas:hover .materi-actions {
        opacity: 1;
        transform: translateY(0);
    }

    .materi-actions a {
        width: 34px;
        height: 34px;
        background: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #555;
        box-shadow: 0 6px 16px rgba(0, 0, 0, .15);
        font-size: 14px;
        transition: .2s ease;
    }

    .materi-actions a:hover {
        background: #28a745;
        color: #fff;
    }
</style>

<!-- ================= SCRIPT ================= -->
<script>
    window.addEventListener('load', function() {
        document.getElementById('page-loading').style.display = 'none';
        document.getElementById('materi-content').classList.remove('d-none');
    });
</script>

<?= $this->endSection(); ?>