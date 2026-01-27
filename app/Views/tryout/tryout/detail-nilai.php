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

    <!-- ================= RINGKASAN ================= -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card hasil-card text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Soal</h6>
                    <h3 class="font-weight-bold mb-0">
                        <?= $total ?>
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card hasil-card text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Jawaban Benar</h6>
                    <h3 class="font-weight-bold text-success mb-0">
                        <?= $benar ?>
                    </h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card hasil-card text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Jawaban Salah</h6>
                    <h3 class="font-weight-bold text-danger mb-0">
                        <?= $salah ?>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- ================= REVIEW SOAL ================= -->
    <div class="card soal-card mb-4">
        <div class="card-body">

            <h6 class="font-weight-bold mb-4 text-gray-800">
                Review Jawaban
            </h6>

            <?php foreach ($detail as $i => $d): ?>
                <div class="review-item mb-3 <?= $d['benar'] ? 'review-benar' : 'review-salah' ?>">
                    <div class="d-flex align-items-start gap-3">
                        <span class="soal-number">
                            <?= $i + 1 ?>
                        </span>

                        <div class="flex-grow-1">
                            <small class="d-block">
                                Jawaban Kamu:
                                <b><?= $d['jawaban_user'] ?: '-' ?></b>
                            </small>

                            <small class="d-block">
                                Kunci Jawaban:
                                <b><?= $d['jawaban_benar'] ?></b>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>

    <!-- ================= BUTTON ================= -->
    <div class="text-center mb-4">
        <a href="<?= site_url('tryout/' . $kategori . "/nilai/" .$tryout['id']) ?>"
            class="btn btn-binjas rounded-pill px-5">
            Kembali
        </a>
    </div>

</div>

<!-- ================= STYLE ================= -->
<style>
    /* GLOBAL */
    .container-fluid {
        max-width: 1200px;
    }

    h5,
    h6 {
        letter-spacing: .3px;
    }

    /* HEADER */
    .cbt-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #ffffff;
        padding: 20px 24px;
        border-radius: 20px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, .08);
        gap: 16px;
        flex-wrap: wrap;
    }

    .score-box {
        background: linear-gradient(135deg, #28a745, #1e7e34);
        color: #fff;
        padding: 12px 28px;
        border-radius: 40px;
        text-align: center;
        line-height: 1.2;
    }

    .score-box small {
        display: block;
        font-size: 11px;
        opacity: .9;
    }

    .score-box span {
        font-size: 22px;
        font-weight: 800;
    }

    /* CARD */
    .soal-card,
    .hasil-card {
        border-radius: 20px;
        border: none;
        box-shadow: 0 12px 28px rgba(0, 0, 0, .07);
    }

    .hasil-card h3 {
        font-size: 34px;
    }

    /* REVIEW */
    .review-item {
        padding: 16px 18px;
        border-radius: 16px;
        border: 1px solid #e9ecef;
        transition: all .2s ease;
    }

    .review-item:hover {
        transform: translateY(-2px);
    }

    .review-benar {
        background: #f6fff9;
        border-color: #28a745;
    }

    .review-salah {
        background: #fff5f5;
        border-color: #dc3545;
    }

    /* SOAL NUMBER */
    .soal-number {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #28a745;
        color: #fff;
        font-weight: 700;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* BUTTON */
    .btn-binjas {
        background: linear-gradient(135deg, #28a745, #1e7e34);
        color: #fff;
        font-weight: 600;
        padding: 10px 36px;
        box-shadow: 0 8px 18px rgba(40, 167, 69, .35);
    }

    .btn-binjas:hover {
        opacity: .95;
        color: #fff;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
        .cbt-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .score-box {
            align-self: stretch;
        }

        .hasil-card h3 {
            font-size: 28px;
        }
    }
</style>

<?= $this->endSection(); ?>