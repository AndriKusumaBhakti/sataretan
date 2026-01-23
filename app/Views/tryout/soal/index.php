<?= $this->extend('default/layout-template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- ================= HEADER ================= -->
    <div class="text-center mb-5">
        <span class="badge badge-success badge-pill px-4 py-2 mb-2 shadow-sm">
            KELOLA SOAL
        </span>
        <h4 class="font-weight-bold text-gray-800 mb-1">
            <?= esc($tryout['judul']) ?>
        </h4>
        <p class="text-muted small mb-0">
            Daftar soal pada try out ini
        </p>
    </div>

    <!-- ================= ACTION ================= -->
    <div class="d-flex justify-content-end mb-4">
        <a href="<?= site_url('tryout/' . $kategori . '/' . $tryout['id'] . '/soal/tambah') ?>"
            class="btn btn-success rounded-pill px-4 shadow-sm">
            <i class="fas fa-plus mr-1"></i> Tambah Soal
        </a>
    </div>

    <!-- ================= LOADING ================= -->
    <div id="page-loading">
        <div class="loading-spinner"></div>
        <div class="loading-text">Memuat soal...</div>
    </div>

    <!-- ================= CONTENT ================= -->
    <div id="soal-content" class="d-none">

        <?php if (empty($soalList)): ?>

            <!-- EMPTY STATE -->
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h5>Belum Ada Soal</h5>
                <p>
                    Soal untuk try out ini belum tersedia.<br>
                    Silakan tambahkan soal terlebih dahulu.
                </p>
            </div>

        <?php else: ?>

            <?php foreach ($soalList as $index => $soal): ?>
                <div class="soal-card mb-3">

                    <!-- HEADER SOAL -->
                    <div class="soal-head"
                        data-toggle="collapse"
                        data-target="#soal<?= $soal['id'] ?>">

                        <div class="d-flex align-items-center">
                            <span class="soal-number"><?= $index + 1 ?></span>

                            <div class="soal-preview">
                                <?= character_limiter(strip_tags($soal['pertanyaan']), 120) ?>
                            </div>
                        </div>

                        <span class="toggle-icon">
                            <i class="fas fa-chevron-down"></i>
                        </span>
                    </div>

                    <!-- DETAIL -->
                    <div class="collapse" id="soal<?= $soal['id'] ?>">
                        <div class="card-body pt-3">

                            <!-- PERTANYAAN -->
                            <div class="soal-text mb-3">
                                <?= esc($soal['pertanyaan']) ?>
                            </div>

                            <?php if (!empty($soal['gambar_soal'])): ?>
                                <div class="soal-image">
                                    <img src="<?= base_url('file/soal/' . $soal['gambar_soal']) ?>">
                                </div>
                            <?php endif; ?>

                            <!-- OPSI -->
                            <div class="opsi-grid mt-4">
                                <?php foreach (['A', 'B', 'C', 'D', 'E'] as $opsi): ?>
                                    <div class="opsi-item <?= $soal['jawaban_benar'] === $opsi ? 'opsi-benar' : '' ?>">
                                        <span class="opsi-label"><?= $opsi ?></span>

                                        <div class="flex-fill">
                                            <div class="opsi-text">
                                                <?= esc($soal['opsi_' . $opsi]) ?>
                                            </div>

                                            <?php if (!empty($soal['gambar_opsi_' . $opsi])): ?>
                                                <img src="<?= base_url('file/soal/' . $soal['gambar_opsi_' . $opsi]) ?>">
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- ACTION -->
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="<?= site_url('tryout/' . $kategori . '/' . $tryout['id'] . '/soal/edit/' . $soal['id']) ?>"
                                    class="btn btn-sm btn-outline-warning rounded-pill px-3 mr-2">
                                    <i class="fas fa-edit mr-1"></i> Edit
                                </a>

                                <form action="<?= site_url('tryout/' . $kategori . '/' . $tryout['id'] . '/soal/hapus/' . $soal['id']) ?>"
                                    method="get"
                                    onsubmit="return confirm('Hapus soal ini?')">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                        <i class="fas fa-trash mr-1"></i> Hapus
                                    </button>
                                </form>
                            </div>

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
        width: 46px;
        height: 46px;
        border: 5px solid #e9ecef;
        border-top: 5px solid #198754;
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
    }

    .empty-state p {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 22px;
    }

    /* ===== SOAL CARD ===== */
    .soal-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 10px 26px rgba(0, 0, 0, .07);
        overflow: hidden;
        transition: .25s ease;
    }

    .soal-card:hover {
        box-shadow: 0 18px 40px rgba(0, 0, 0, .12);
    }

    .soal-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 20px;
        cursor: pointer;
    }

    .soal-number {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #198754;
        color: #fff;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
    }

    .soal-preview {
        font-size: 14px;
        color: #555;
    }

    .toggle-icon {
        width: 34px;
        height: 34px;
        background: #f1f3f5;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .soal-text {
        line-height: 1.6;
    }

    .soal-image img {
        max-height: 180px;
        border-radius: 14px;
        display: block;
        margin: 12px auto;
    }

    /* ===== OPSI ===== */
    .opsi-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .opsi-item {
        border: 1px solid #e9ecef;
        border-radius: 14px;
        padding: 10px 12px;
        display: flex;
        gap: 10px;
        font-size: 14px;
    }

    .opsi-item img {
        max-height: 80px;
        border-radius: 8px;
        margin-top: 6px;
    }

    .opsi-label {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #e9ecef;
        color: #198754;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .opsi-benar {
        border-color: #198754;
        background: #f0fdf4;
    }
</style>

<!-- ================= SCRIPT ================= -->
<script>
    window.addEventListener('load', function() {
        document.getElementById('page-loading').style.display = 'none';
        document.getElementById('soal-content').classList.remove('d-none');
    });
</script>

<?= $this->endSection(); ?>