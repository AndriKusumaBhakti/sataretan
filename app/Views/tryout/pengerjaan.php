<?= $this->extend('default/layout-template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- ================= HEADER ================= -->
    <div class="cbt-header mb-4">
        <div>
            <h5 class="font-weight-bold mb-0 text-gray-800 text-wrap">
                <?= esc($tryout['judul']) ?>
            </h5>
            <small class="text-muted d-block">
                Soal <?= $current ?> dari <?= $totalSoal ?>
            </small>
        </div>

        <small class="text-muted d-block">
            Berlaku:
            <?= date('d M Y', strtotime($tryout['tanggal_mulai'])) ?>
            –
            <?= date('d M Y', strtotime($tryout['tanggal_selesai'])) ?>
        </small>

        <div class="timer-box">
            <i class="fas fa-clock mr-1"></i>
            <span id="timer"></span>
        </div>
    </div>

    <!-- ================= FORM ================= -->
    <form id="formTryout"
        action="<?= site_url('tryout/' . $kategori . '/submit/' . $tryout['id']) ?>">

        <?= csrf_field() ?>

        <div class="card soal-card mb-4">
            <div class="card-body">

                <!-- SOAL -->
                <div class="soal-header mb-4">
                    <span class="soal-number"><?= $current ?></span>

                    <div class="soal-text">
                        <?= esc($soal['pertanyaan']) ?>

                        <?php if (!empty($soal['gambar_soal'])): ?>
                            <div class="soal-image">
                                <img src="<?= base_url('file/soal/' . $soal['gambar_soal']) ?>"
                                    alt="Gambar Soal">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- OPSI -->
                <?php foreach (['A', 'B', 'C', 'D', 'E'] as $opsi): ?>
                    <label class="opsi-card">
                        <input type="radio"
                            name="jawaban"
                            value="<?= $opsi ?>"
                            data-soal="<?= $soal['id'] ?>"
                            <?= isset($jawabanUser[$soal['id']]) && $jawabanUser[$soal['id']] === $opsi ? 'checked' : '' ?>>

                        <span class="opsi-label"><?= $opsi ?></span>

                        <span class="opsi-text">
                            <?= esc($soal['opsi_' . $opsi]) ?>

                            <?php if (!empty($soal['gambar_opsi_' . $opsi])): ?>
                                <div class="opsi-image">
                                    <img src="<?= base_url('file/soal/' . $soal['gambar_opsi_' . $opsi]) ?>"
                                        alt="Gambar Opsi <?= $opsi ?>">
                                </div>
                            <?php endif; ?>
                        </span>
                    </label>
                <?php endforeach; ?>

            </div>
        </div>

        <!-- ================= PAGINATION ================= -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <?php if ($current > 1): ?>
                <a href="<?= site_url('tryout/' . $kategori . '/pengerjaan/' . $tryout['id'] . '/' . ($current - 1)) ?>"
                    class="btn btn-binjas-outline rounded-pill px-4">
                    <i class="fas fa-arrow-left mr-1"></i> Sebelumnya
                </a>
            <?php else: ?>
                <div></div>
            <?php endif; ?>

            <?php if ($current < $totalSoal): ?>
                <a href="<?= site_url('tryout/' . $kategori . '/pengerjaan/' . $tryout['id'] . '/' . ($current + 1)) ?>"
                    class="btn btn-binjas rounded-pill px-5">
                    Berikutnya <i class="fas fa-arrow-right ml-1"></i>
                </a>
            <?php else: ?>
                <button id="btnSubmit"
                    type="submit"
                    class="btn btn-binjas rounded-pill px-5">
                    <i class="fas fa-paper-plane mr-1"></i> Submit
                </button>
            <?php endif; ?>

        </div>

    </form>
</div>

<!-- ================= SCRIPT ================= -->
<script>
    const DURASI = <?= $sisa_waktu ?>;
    const form = document.getElementById('formTryout');
    const timerEl = document.getElementById('timer');
    const btnSubmit = document.getElementById('btnSubmit');

    let csrfName = "<?= csrf_token() ?>";
    let csrfHash = "<?= csrf_hash() ?>";
    let endTime = Math.floor(Date.now() / 1000) + DURASI;
    let submitted = false;

    const interval = setInterval(() => {
        const now = Math.floor(Date.now() / 1000);
        const sisa = endTime - now;

        if (sisa <= 0) {
            clearInterval(interval);
            timerEl.innerText = '0:00';
            if (!submitted) {
                submitted = true;
                form.submit();
            }
            return;
        }

        const m = Math.floor(sisa / 60);
        const s = sisa % 60;
        timerEl.innerText = m + ':' + (s < 10 ? '0' + s : s);
    }, 1000);

    form.addEventListener('submit', () => {
        submitted = true;
        if (btnSubmit) {
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = `
                <span class="spinner-border spinner-border-sm mr-2"></span>
                Menyimpan...
            `;
        }
    });

    document.querySelectorAll('input[type=radio]').forEach(el => {
        el.addEventListener('change', function() {
            fetch("<?= site_url('tryout/save-jawaban') ?>", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    body: new URLSearchParams({
                        tryout_id: <?= $tryout['id'] ?>,
                        soal_id: this.dataset.soal,
                        jawaban: this.value,
                        [csrfName]: csrfHash
                    })
                })
                .then(res => res.json())
                .then(res => {
                    if (res.csrfHash) csrfHash = res.csrfHash;
                });
        });
    });
</script>

<!-- ================= STYLE ================= -->
<style>
    .cbt-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff;
        padding: 16px 20px;
        border-radius: 18px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .08);
        flex-wrap: wrap;
    }

    .timer-box {
        background: #28a745;
        color: #fff;
        padding: 8px 18px;
        border-radius: 30px;
        font-weight: 700;
    }

    .soal-card {
        border-radius: 18px;
        border: 0;
        box-shadow: 0 10px 25px rgba(0, 0, 0, .06);
    }

    .soal-header {
        display: flex;
        gap: 12px;
    }

    .soal-number {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #28a745;
        color: #fff;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .soal-text img,
    .opsi-text img {
        max-width: 100%;
        height: auto;
        display: block;
        margin: 10px auto;
    }

    .opsi-card {
        display: flex;
        gap: 14px;
        padding: 14px 16px;
        border-radius: 14px;
        border: 1px solid #e9ecef;
        cursor: pointer;
        margin-bottom: 12px;
        -webkit-tap-highlight-color: transparent;
    }

    .opsi-card:hover {
        background: #f8fdf9;
    }

    .opsi-card:active {
        transform: scale(0.98);
    }

    .opsi-card input {
        display: none;
    }

    .opsi-label {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #e9ecef;
        color: #28a745;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .opsi-card input:checked+.opsi-label {
        background: #28a745;
        color: #fff;
    }

    .btn-binjas {
        background: linear-gradient(135deg, #28a745, #1e7e34);
        color: #fff;
        font-weight: 600;
    }

    .btn-binjas-outline {
        background: #f1f3f5;
        font-weight: 600;
    }

    .soal-image img,
    .opsi-image img {
        max-width: 100%;
        max-height: 260px;
        object-fit: contain;
        border-radius: 10px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, .12);
    }

    /* ========== MOBILE ========== */
    @media (max-width: 576px) {

        .container-fluid {
            padding-left: 12px;
            padding-right: 12px;
        }

        .cbt-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            position: sticky;
            top: 10px;
            z-index: 100;
        }

        .timer-box {
            align-self: flex-end;
            font-size: 14px;
            padding: 6px 14px;
        }

        .soal-header {
            flex-direction: column;
            gap: 8px;
        }

        .soal-number,
        .opsi-label {
            width: 30px;
            height: 30px;
            font-size: 14px;
        }

        .opsi-text,
        .soal-text {
            font-size: 14px;
        }

        .d-flex.justify-content-between {
            flex-direction: column;
            gap: 12px;
        }

        .d-flex.justify-content-between .btn {
            width: 100%;
            padding: 12px;
        }
    }
</style>

<?= $this->endSection(); ?>