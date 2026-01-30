<?= $this->extend('default/layout-template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- ================= HEADER ================= -->
    <div class="text-center mb-5">
        <span class="badge badge-success badge-pill px-4 py-2 mb-2">
            TAMBAH SOAL
        </span>
        <h4 class="font-weight-bold text-gray-800 mb-1">
            <?= esc($tryout['judul']) ?>
        </h4>
        <small class="text-muted">
            Buat soal baru untuk try out ini
        </small>
    </div>

    <!-- ================= UPLOAD EXCEL ================= -->
    <div class="mb-4 text-center">
        <button type="button"
            class="btn btn-outline-success rounded-pill px-4"
            data-toggle="collapse"
            data-target="#uploadExcelSoal">
            <i class="fas fa-file-excel mr-2"></i>
            Upload Soal via Excel
        </button>
    </div>

    <div id="uploadExcelSoal" class="collapse mb-5">
        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <form action="<?= site_url('tryout/' . $kategori . '/' . $tryout['id'] . '/soal/upload-excel') ?>"
                    method="post"
                    enctype="multipart/form-data">

                    <?= csrf_field() ?>

                    <div class="form-group">
                        <label class="font-weight-bold">
                            File ZIP (Excel + Gambar)
                        </label>

                        <input type="file"
                            name="file_zip"
                            class="form-control-file"
                            accept=".zip"
                            required>

                        <small class="text-muted d-block mt-2">
                            Format ZIP berisi:
                            <br>• 1 file Excel (.xls / .xlsx)
                            <br>• Folder <strong>images/</strong> (opsional, untuk gambar)
                            <br><br>
                            Kolom wajib: pertanyaan, opsi_a, opsi_b, opsi_c, opsi_d, opsi_e, jawaban
                            <br>
                            Opsional: gambar_soal, gambar_opsi_a s/d gambar_opsi_e
                        </small>
                    </div>

                    <div class="text-right">
                        <button type="submit"
                            class="btn btn-success rounded-pill px-4">
                            <i class="fas fa-upload mr-2"></i>
                            Upload ZIP
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>


    <!-- ================= FORM MANUAL ================= -->
    <div class="row justify-content-center">
        <div class="col-lg-9">

            <div class="soal-card">
                <div class="card-body p-4 p-lg-5">

                    <form id="form-soal"
                        action="<?= site_url('tryout/' . $kategori . '/' . $tryout['id'] . '/soal/simpan') ?>"
                        method="post"
                        enctype="multipart/form-data">

                        <?= csrf_field() ?>

                        <!-- ================= PERTANYAAN ================= -->
                        <div class="section-title">
                            <span>1</span> Pertanyaan
                        </div>

                        <div class="form-group">
                            <textarea name="pertanyaan"
                                class="form-control form-control-lg"
                                rows="4"
                                placeholder="Tulis pertanyaan di sini..."
                                required></textarea>
                        </div>

                        <div class="form-group">
                            <label class="small font-weight-bold text-muted">
                                Gambar Soal (Opsional)
                            </label>
                            <input type="file"
                                name="gambar_soal"
                                class="form-control-file"
                                accept="image/*">
                        </div>

                        <!-- ================= OPSI ================= -->
                        <div class="section-title mt-5">
                            <span>2</span> Opsi Jawaban
                        </div>

                        <?php foreach (['A', 'B', 'C', 'D', 'E'] as $opsi): ?>
                            <div class="opsi-card">
                                <div class="opsi-label"><?= $opsi ?></div>

                                <div class="flex-fill">
                                    <textarea name="opsi_<?= $opsi ?>"
                                        class="form-control"
                                        rows="2"
                                        placeholder="Jawaban <?= $opsi ?>"
                                        required></textarea>

                                    <input type="file"
                                        name="gambar_opsi_<?= $opsi ?>"
                                        class="form-control-file mt-2"
                                        accept="image/*">
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!-- ================= JAWABAN ================= -->
                        <div class="section-title mt-5">
                            <span>3</span> Jawaban Benar
                        </div>

                        <div class="jawaban-wrapper">
                            <div class="row">
                                <div class="col-md-4">
                                    <select name="jawaban_benar"
                                        class="form-control rounded-pill select-jawaban"
                                        required>
                                        <option value="">Pilih Jawaban</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="C">C</option>
                                        <option value="D">D</option>
                                        <option value="E">E</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- ================= BUTTON ================= -->
                        <div class="d-flex justify-content-end mt-5">
                            <a href="<?= site_url('admin/tryout/' . $tryout['id']) ?>"
                                class="btn btn-light rounded-pill px-4 mr-2">
                                Batal
                            </a>

                            <button type="submit"
                                id="btn-submit"
                                class="btn btn-success rounded-pill px-5">
                                <i class="fas fa-save mr-2"></i>
                                Simpan Soal
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
        transition: .3s ease;
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
        gap: 16px;
        padding: 16px;
        border-radius: 16px;
        border: 1px solid #e9ecef;
        margin-bottom: 14px;
        background: #fff;
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
</style>

<!-- ================= SCRIPT ================= -->
<script>
    document.getElementById('form-soal').addEventListener('submit', function() {
        const btn = document.getElementById('btn-submit');
        btn.disabled = true;
        btn.innerHTML = `
            <span class="spinner-border spinner-border-sm mr-2"></span>
            Menyimpan...
        `;
    });
</script>

<?= $this->endSection(); ?>