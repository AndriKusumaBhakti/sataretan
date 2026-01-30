<?= $this->extend('default/layout-template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- ================= HEADER ================= -->
    <div class="text-center mb-5">
        <span class="badge badge-warning badge-pill px-4 py-2 mb-2">
            EDIT SOAL
        </span>
        <h4 class="font-weight-bold text-gray-800 mb-1">
            <?= esc($tryout['judul']) ?>
        </h4>
        <small class="text-muted">
            Perbarui soal try out
        </small>
    </div>

    <!-- ================= FORM ================= -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-9">

            <div class="soal-card">
                <div class="card-body p-4 p-lg-5">

                    <form id="form-soal"
                        action="<?= site_url('tryout/' . $kategori . '/' . $tryout['id'] . '/soal/update/' . $soal['id']) ?>"
                        method="post"
                        enctype="multipart/form-data">

                        <?= csrf_field() ?>

                        <!-- ================= PERTANYAAN ================= -->
                        <div class="section-title">
                            <span>1</span> Pertanyaan
                        </div>
                        <div class="form-group">
                            <textarea name="pertanyaan" class="form-control form-control-lg" rows="4"
                                required><?= esc($soal['pertanyaan']) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label class="small font-weight-bold text-muted">Gambar Soal (Opsional)</label>
                            <?php if ($soal['gambar_soal']): ?>
                                <div class="mb-2">
                                    <img src="<?= base_url('file/soal/' . $soal['gambar_soal']) ?>"
                                        class="img-fluid rounded" style="max-height:200px">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="gambar_soal" class="form-control-file" accept="image/*">
                        </div>

                        <!-- ================= OPSI ================= -->
                        <div class="section-title mt-5">
                            <span>2</span> Opsi Jawaban
                        </div>

                        <?php foreach (['A', 'B', 'C', 'D', 'E'] as $opsi): ?>
                            <div class="opsi-card row align-items-center">
                                <div class="col-auto">
                                    <div class="opsi-label"><?= $opsi ?></div>
                                </div>

                                <div class="col-md-7 col-12 mb-2 mb-md-0">
                                    <textarea name="opsi_<?= $opsi ?>" class="form-control" rows="2"
                                        required><?= esc($soal['opsi_' . $opsi]) ?></textarea>
                                </div>

                                <div class="col-md-3 col-12">
                                    <?php if ($soal['gambar_opsi_' . $opsi]): ?>
                                        <div class="mb-2">
                                            <img src="<?= base_url('file/soal/' . $soal['gambar_opsi_' . $opsi]) ?>"
                                                class="img-fluid rounded" style="max-height:160px">
                                        </div>
                                    <?php endif; ?>
                                    <input type="file" name="gambar_opsi_<?= $opsi ?>" class="form-control-file mb-2"
                                        accept="image/*">
                                    <input type="number" name="nilai_<?= $opsi ?>" class="form-control"
                                        placeholder="Nilai <?= $opsi ?>"
                                        value="<?= isset($soal['nilai_' . $opsi]) ? esc($soal['nilai_' . $opsi]) : 0 ?>"
                                        min="0" required>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!-- ================= JAWABAN ================= -->
                        <div class="section-title mt-5">
                            <span>3</span> Jawaban Benar
                        </div>
                        <div class="jawaban-wrapper">
                            <div class="row">
                                <div class="col-md-4 col-12">
                                    <select name="jawaban_benar" class="form-control rounded-pill select-jawaban" required>
                                        <?php foreach (['A', 'B', 'C', 'D', 'E'] as $opsi): ?>
                                            <option value="<?= $opsi ?>" <?= $soal['jawaban_benar'] === $opsi ? 'selected' : '' ?>>
                                                <?= $opsi ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- ================= BUTTON ================= -->
                        <div class="d-flex flex-column flex-md-row justify-content-end mt-5 gap-2">
                            <a href="<?= site_url('tryout/' . $kategori . '/' . $tryout['id'] . '/soal') ?>"
                                class="btn btn-light rounded-pill px-4">Batal</a>
                            <button type="submit" id="btn-submit" class="btn btn-success rounded-pill px-5">
                                <i class="fas fa-save mr-2"></i> Update Soal
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

<!-- ================= STYLE ================= -->
<style>
    .soal-card {
        background: linear-gradient(180deg, #ffffff, #f8fbff);
        border-radius: 22px;
        box-shadow: 0 18px 40px rgba(0, 0, 0, .08);
        overflow: visible;
    }

    .soal-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 26px 56px rgba(0, 0, 0, .14);
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 700;
        margin-bottom: 16px;
        color: #198754;
    }

    .section-title span {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: #198754;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .opsi-card {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        padding: 16px;
        border-radius: 16px;
        border: 1px solid #e9ecef;
        margin-bottom: 14px;
        background: #fff;
        align-items: flex-start;
    }

    .opsi-label {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #e9ecef;
        color: #198754;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .jawaban-wrapper {
        position: relative;
        z-index: 20;
    }

    .form-control {
        border-radius: 14px;
    }

    .form-control-lg {
        font-size: 15px;
    }

    textarea.form-control {
        resize: vertical;
    }

    .select-jawaban {
        padding-left: 14px;
        padding-right: 36px;
        height: 44px;
        line-height: 44px;
    }

    @media (max-width: 767px) {

        .opsi-card .col-md-7,
        .opsi-card .col-md-3 {
            flex: 0 0 100%;
            max-width: 100%;
        }
    }
</style>

<!-- ================= SCRIPT ================= -->
<script>
    document.getElementById('form-soal').addEventListener('submit', function() {
        const btn = document.getElementById('btn-submit');
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm mr-2"></span> Menyimpan...`;
    });
</script>

<?= $this->endSection(); ?>