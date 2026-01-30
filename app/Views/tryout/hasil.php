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

    <!-- ================= REVIEW SOAL ================= -->
    <div class="card soal-card mb-4">
        <div class="card-body">

            <h6 class="font-weight-bold mb-4 text-gray-800">Review Jawaban</h6>

            <?php foreach ($detail as $i => $d): ?>
                <div class="review-item mb-3">
                    <div class="d-flex align-items-start gap-3 mb-2">
                        <span class="soal-number"><?= $i + 1 ?></span>

                        <div class="flex-grow-1">
                            <small class="d-block">
                                Jawaban Kamu:
                                <b><?= $d['jawaban_user'] ?: '-' ?></b>
                            </small>

                            <small class="d-block mb-2">
                                Kunci Jawaban:
                                <b><?= $d['jawaban_benar'] ?></b>
                            </small>

                            <!-- ================= OPSI DENGAN NILAI ================= -->
                            <div class="opsi-review d-flex flex-wrap gap-2">
                                <?php foreach (['A','B','C','D','E'] as $opsi): 
                                    $nilai_opsi = isset($d['nilai_' . strtolower($opsi)]) ? $d['nilai_' . strtolower($opsi)] : 0;
                                    $is_user = $d['jawaban_user'] === $opsi;
                                    $is_benar = $d['jawaban_benar'] === $opsi;
                                ?>
                                    <div class="opsi-item <?= $is_benar ? 'opsi-benar' : '' ?> <?= $is_user ? 'opsi-user' : '' ?>">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="opsi-label"><?= $opsi ?></span>
                                            <span class="opsi-nilai"><?= $nilai_opsi > 0 ? $nilai_opsi : '' ?></span>
                                        </div>
                                        <div class="opsi-text"><?= esc($d['opsi_' . strtolower($opsi)]) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

        </div>
    </div>

    <!-- ================= BUTTON ================= -->
    <div class="text-center mb-4">
        <a href="<?= site_url('tryout/' . $kategori) ?>" class="btn btn-binjas rounded-pill px-5">
            Kembali
        </a>
    </div>

</div>

<!-- ================= STYLE ================= -->
<style>
    .container-fluid { max-width: 1200px; }

    h5,h6 { letter-spacing: .3px; }

    .cbt-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff;
        padding: 20px 24px;
        border-radius: 20px;
        box-shadow: 0 12px 30px rgba(0,0,0,.08);
        gap: 16px;
        flex-wrap: wrap;
    }

    .score-box {
        background: linear-gradient(135deg,#28a745,#1e7e34);
        color: #fff;
        padding: 12px 28px;
        border-radius: 40px;
        text-align: center;
        line-height: 1.2;
    }
    .score-box small { display:block; font-size:11px; opacity:.9; }
    .score-box span { font-size:22px; font-weight:800; }

    .soal-card,.hasil-card { border-radius:20px; border:none; box-shadow:0 12px 28px rgba(0,0,0,.07); }
    .hasil-card h3 { font-size:34px; }

    .review-item {
        padding:16px 18px;
        border-radius:16px;
        border:1px solid #e9ecef;
        transition: all .2s ease;
    }
    .review-item:hover { transform:translateY(-2px); }

    .opsi-review { display:flex; flex-wrap:wrap; gap:8px; }
    .opsi-item {
        border:1px solid #e9ecef;
        border-radius:12px;
        padding:8px 12px;
        min-width:140px;
        flex:1 1 140px;
        background:#fff;
    }
    .opsi-label {
        width:24px;
        height:24px;
        border-radius:50%;
        background:#e9ecef;
        color:#28a745;
        font-weight:700;
        display:flex;
        align-items:center;
        justify-content:center;
        margin-right:6px;
    }
    .opsi-nilai { font-weight:600; color:#198754; }
    .opsi-text { font-size:13px; margin-top:4px; }

    .opsi-benar { border-color:#28a745; background:#f6fff9; }
    .opsi-user { box-shadow:0 0 0 2px rgba(40,167,69,.3); }

    .soal-number {
        width:36px;
        height:36px;
        border-radius:50%;
        background:#28a745;
        color:#fff;
        font-weight:700;
        font-size:14px;
        display:flex;
        align-items:center;
        justify-content:center;
        flex-shrink:0;
    }

    .btn-binjas {
        background: linear-gradient(135deg,#28a745,#1e7e34);
        color:#fff;
        font-weight:600;
        padding:10px 36px;
        box-shadow:0 8px 18px rgba(40,167,69,.35);
    }
    .btn-binjas:hover { opacity:.95; color:#fff; }

    @media (max-width:768px){
        .cbt-header { flex-direction:column; align-items:flex-start; }
        .score-box { align-self:stretch; }
        .hasil-card h3 { font-size:28px; }
        .opsi-item { min-width:100%; flex:1 1 100%; }
    }
</style>

<?= $this->endSection(); ?>