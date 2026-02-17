<?= $this->extend('default/layout-template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- ================= HEADER ================= -->
    <div class="cbt-header mb-4">
        <div>
            <h5 class="font-weight-bold mb-1 text-gray-800">
                <?= esc($tryout['judul']) ?>
            </h5>
            <small class="text-muted">Hasil Tryout</small>
        </div>

        <div class="score-box">
            <small>Nilai Akhir</small>
            <span><?= $nilai ?></span>
        </div>
    </div>

    <?php if (!empty($hasOnline) && $hasOnline): ?>

        <!-- ================= RINGKASAN ================= -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3 mb-md-0">
                <div class="card hasil-card text-center">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Total Soal</h6>
                        <h3 class="font-weight-bold mb-0"><?= $total ?></h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3 mb-md-0">
                <div class="card hasil-card text-center">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Jawaban Benar</h6>
                        <h3 class="font-weight-bold text-success mb-0"><?= $benar ?></h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card hasil-card text-center">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Jawaban Salah</h6>
                        <h3 class="font-weight-bold text-danger mb-0"><?= $salah ?></h3>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- ================= BUTTON ================= -->
    <div class="text-center mb-4">
        <a href="<?= site_url('tryout/' . $kategori) ?>" class="btn btn-binjas">
            Kembali
        </a>
    </div>

</div>

<!-- ================= STYLE ================= -->
<style>
    :root {
        --green: #28a745;
        --green-dark: #1e7e34;
        --soft-bg: #f8f9fa;
        --border-soft: #e9ecef;
    }

    body {
        background: var(--soft-bg);
    }

    .container-fluid {
        max-width: 1200px;
        padding: 12px;
    }

    /* HEADER */
    .cbt-header {
        background: #fff;
        padding: 22px 26px;
        border-radius: 22px;
        box-shadow: 0 14px 36px rgba(0, 0, 0, .08);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .score-box {
        background: linear-gradient(135deg, var(--green), var(--green-dark));
        color: #fff;
        padding: 14px 34px;
        border-radius: 50px;
        text-align: center;
        min-width: 140px;
    }

    .score-box span {
        font-size: 26px;
        font-weight: 800;
    }

    /* CARD */
    .hasil-card,
    .soal-card {
        border-radius: 22px;
        border: none;
        box-shadow: 0 12px 28px rgba(0, 0, 0, .07);
    }

    .hasil-card h3 {
        font-size: 34px;
    }

    /* REVIEW */
    .review-item {
        padding: 18px 20px;
        border-radius: 16px;
        border: 1px solid var(--border-soft);
        background: #fff;
        transition: .25s;
    }

    .review-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, .06);
    }

    .soal-number {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: var(--green);
        color: #fff;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* OPSI */
    .opsi-review {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 10px;
    }

    .opsi-item {
        border: 1px solid var(--border-soft);
        border-radius: 12px;
        padding: 10px 12px;
        background: #fff;
    }

    .opsi-label {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #e9ecef;
        color: var(--green);
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
    }

    .opsi-text {
        font-size: 13px;
        line-height: 1.4;
        color: #444;
    }

    .opsi-nilai {
        font-weight: 600;
        color: var(--green-dark);
    }

    .opsi-benar {
        border-color: var(--green);
        background: #f6fff9;
    }

    .opsi-user {
        box-shadow: 0 0 0 2px rgba(40, 167, 69, .35);
    }

    /* BUTTON */
    .btn-binjas {
        background: linear-gradient(135deg, var(--green), var(--green-dark));
        color: #fff;
        font-weight: 600;
        padding: 12px 40px;
        border-radius: 40px;
        box-shadow: 0 8px 18px rgba(40, 167, 69, .35);
    }

    .btn-binjas:hover {
        opacity: .95;
        color: #fff;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .cbt-header {
            padding: 18px;
        }

        .score-box {
            width: 100%;
            border-radius: 16px;
        }

        .opsi-review {
            grid-template-columns: 1fr;
        }

        .hasil-card h3 {
            font-size: 28px;
        }
    }
</style>

<?= $this->endSection(); ?>