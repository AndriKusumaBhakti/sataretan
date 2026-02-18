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

            <!-- ================= NAVIGASI NOMOR ================= -->
            <div class="soal-nav mb-4">
                <?php foreach ($soalList as $i => $s): ?>
                    <button class="nav-soal-btn <?= $i == 0 ? 'active' : '' ?>"
                        data-target="soal<?= $s['id'] ?>">
                        <?= $i + 1 ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- ================= LIST SOAL ================= -->
            <?php foreach ($soalList as $index => $soal): ?>
                <div class="soal-card mb-3 soal-item <?= $index == 0 ? 'active' : '' ?>"
                    id="soal<?= $soal['id'] ?>">

                    <div class="soal-head">
                        <div class="d-flex align-items-center">
                            <span class="soal-number"><?= $index + 1 ?></span>
                            <div class="soal-preview">
                                <?= character_limiter(strip_tags($soal['pertanyaan']), 120) ?>
                            </div>
                        </div>
                    </div>

                    <div class="card-body pt-3">

                        <div class="soal-text mb-3">
                            <?= esc($soal['pertanyaan']) ?>
                        </div>

                        <?php if (!empty($soal['gambar_soal'])): ?>
                            <div class="soal-image-wrapper">
                                <img class="img-fluid soal-image"
                                    src="<?= base_url('file/soal/' . $soal['gambar_soal']) ?>">
                            </div>
                        <?php endif; ?>

                        <div class="opsi-grid mt-4">
                            <?php foreach (['A', 'B', 'C', 'D', 'E'] as $opsi): ?>
                                <div class="opsi-item <?= $soal['jawaban_benar'] === $opsi ? 'opsi-benar' : '' ?>">
                                    <span class="opsi-label"><?= $opsi ?></span>

                                    <div class="flex-fill">
                                        <div class="opsi-text">
                                            <?= esc($soal['opsi_' . $opsi]) ?>
                                        </div>

                                        <?php if (!empty($soal['gambar_opsi_' . $opsi])): ?>
                                            <div class="opsi-image-wrapper mt-2">
                                                <img class="img-fluid opsi-image"
                                                    src="<?= base_url('file/soal/' . $soal['gambar_opsi_' . $opsi]) ?>">
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

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
            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</div>

<!-- ================= STYLE ================= -->
<style>
    /* ===== RESPONSIVE IMAGE FIX ===== */

    .soal-image-wrapper {
        width: 100%;
        text-align: center;
        margin-top: 10px;
    }

    .soal-image {
        max-width: 100%;
        height: auto;
        border-radius: 12px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, .08);
    }

    .opsi-image-wrapper {
        width: 100%;
    }

    .opsi-image {
        max-width: 100%;
        height: auto;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, .06);
    }

    /* ===== MOBILE IMPROVEMENT ===== */
    @media (max-width: 768px) {
        .opsi-grid {
            grid-template-columns: 1fr;
        }

        .soal-card {
            padding: 15px;
        }
    }

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

    /* ===== NAVIGASI ===== */
    .soal-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        justify-content: center;
    }

    .nav-soal-btn {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        border: none;
        background: #e9ecef;
        font-weight: 700;
        cursor: pointer;
        transition: .2s;
    }

    .nav-soal-btn.active {
        background: #198754;
        color: #fff;
        transform: scale(1.08);
    }

    /* ===== MODE SATU SOAL ===== */
    .soal-item {
        display: none;
    }

    .soal-item.active {
        display: block;
    }

    /* ===== CARD ===== */
    .soal-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 10px 26px rgba(0, 0, 0, .07);
        padding: 20px;
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

    document.addEventListener("DOMContentLoaded", function() {

        const buttons = document.querySelectorAll(".nav-soal-btn");
        const soalItems = document.querySelectorAll(".soal-item");

        function tampilSoal(id) {
            soalItems.forEach(el => el.classList.remove("active"));
            buttons.forEach(btn => btn.classList.remove("active"));

            document.getElementById(id).classList.add("active");
            document.querySelector(`[data-target="${id}"]`).classList.add("active");

            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });
        }

        buttons.forEach(btn => {
            btn.addEventListener("click", function() {
                tampilSoal(this.dataset.target);
            });
        });

        if (buttons.length > 0) {
            tampilSoal(buttons[0].dataset.target);
        }

    });
</script>

<?= $this->endSection(); ?>