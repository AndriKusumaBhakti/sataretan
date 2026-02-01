<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- ================= HEADER ================= -->
    <div class="text-center mb-5">
        <span class="badge badge-success badge-pill px-4 py-2 mb-2">
            TAMBAH NILAI
        </span>
        <h4 class="font-weight-bold text-gray-800 mb-1">
            <?= esc($tryout['judul']) ?>
        </h4>
        <small class="text-muted">
            Input nilai manual untuk try out offline
        </small>
    </div>

    <!-- ================= FORM ================= -->
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">

            <div class="nilai-card">
                <div class="card-body p-4 p-lg-5">

                    <form id="form-nilai"
                        action="<?= site_url("tryout/$kategori/nilai/simpan/$tryoutId") ?>"
                        method="post">

                        <?= csrf_field() ?>

                        <!-- ================= PESERTA ================= -->
                        <div class="section-title">
                            <span>1</span> Peserta
                        </div>

                        <div class="form-group">
                            <label class="small font-weight-bold text-muted">
                                Pilih Peserta
                            </label>
                            <select name="user_id"
                                class="form-control form-control-lg"
                                required>
                                <option value="">-- Pilih Peserta --</option>
                                <?php foreach ($users as $u): ?>
                                    <option value="<?= $u['id'] ?>">
                                        <?= esc($u['name']) ?>
                                        <?php if (!empty($u['email'])): ?>
                                            (<?= esc($u['email']) ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <!-- ================= NILAI ================= -->
                        <div class="section-title mt-5">
                            <span>2</span> Nilai Akhir
                        </div>

                        <div class="form-group">
                            <label class="small font-weight-bold text-muted">
                                Skor (0 – 100)
                            </label>
                            <input type="number"
                                name="skor_akhir"
                                class="form-control form-control-lg"
                                min="0"
                                max="100"
                                step="0.01"
                                placeholder="Masukkan nilai peserta"
                                required>
                        </div>

                        <div class="alert alert-info small mt-4">
                            <i class="fas fa-info-circle mr-1"></i>
                            Nilai yang ditambahkan manual akan otomatis berstatus
                            <strong>selesai</strong>.
                        </div>

                        <!-- ================= BUTTON ================= -->
                        <div class="d-flex flex-column flex-md-row justify-content-end mt-5 gap-2">
                            <a href="<?= site_url("tryout/$kategori/nilai/$tryoutId") ?>"
                                class="btn btn-light rounded-pill px-4">
                                Batal
                            </a>
                            <button type="submit"
                                id="btn-submit"
                                class="btn btn-success rounded-pill px-5">
                                <i class="fas fa-save mr-2"></i>
                                Simpan Nilai
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
    .nilai-card {
        background: linear-gradient(180deg, #ffffff, #f8fbff);
        border-radius: 22px;
        box-shadow: 0 18px 40px rgba(0, 0, 0, .08);
        transition: .3s;
    }

    .nilai-card:hover {
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

    .form-control {
        border-radius: 14px;
    }

    .form-control-lg {
        font-size: 15px;
        height: 46px;
    }

    @media (max-width: 767px) {
        .form-control-lg {
            height: 44px;
        }
    }
</style>

<!-- ================= SCRIPT ================= -->
<script>
    document.getElementById('form-nilai').addEventListener('submit', function() {
        const btn = document.getElementById('btn-submit');
        btn.disabled = true;
        btn.innerHTML = `
            <span class="spinner-border spinner-border-sm mr-2"></span>
            Menyimpan...
        `;
    });
</script>

<?= $this->endSection(); ?>