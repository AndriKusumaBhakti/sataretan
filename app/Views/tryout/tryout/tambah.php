<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- HEADER -->
    <div class="mb-5">
        <h1 class="h4 font-weight-bold text-gray-800 mb-1">
            Tambah Try Out <?= strtoupper($kategori) ?>
        </h1>
        <small class="text-muted">
            Lengkapi data try out untuk kategori <?= strtoupper($kategori) ?>
        </small>
    </div>

    <!-- FORM CARD -->
    <div class="row justify-content-center">
        <div class="col-lg-6">

            <div class="tryout-card">
                <div class="card-body">

                    <!-- FORM -->
                    <form id="form-tryout"
                        action="<?= site_url('tryout/' . $kategori . '/simpan') ?>"
                        method="post">

                        <?= csrf_field() ?>

                        <div class="form-group">
                            <label class="font-weight-bold">Judul Try Out</label>
                            <input type="text"
                                name="judul"
                                class="form-control rounded-pill px-4"
                                placeholder="Contoh: Try Out UTBK Paket 1"
                                required>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Jumlah Soal</label>
                            <input type="number"
                                name="jumlah_soal"
                                class="form-control rounded-pill px-4"
                                placeholder="Contoh: 90"
                                required>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Durasi (Menit)</label>
                            <input type="number"
                                name="durasi"
                                class="form-control rounded-pill px-4"
                                placeholder="Contoh: 120"
                                required>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="<?= site_url('tryout/' . $kategori) ?>"
                                class="btn btn-light rounded-pill px-4 mr-2">
                                Batal
                            </a>

                            <button type="submit"
                                id="btn-submit"
                                class="btn btn-success rounded-pill px-4">
                                Simpan Try Out
                            </button>
                        </div>

                    </form>
                    <!-- END FORM -->

                </div>
            </div>

        </div>
    </div>

</div>

<!-- ================= STYLE ================= -->
<style>
    .tryout-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
        transition: .3s ease;
    }

    .tryout-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 36px rgba(0, 0, 0, .15);
    }

    .form-control {
        height: 46px;
    }
</style>

<!-- ================= SCRIPT (ANTI DOUBLE SUBMIT) ================= -->
<script>
    document.getElementById('form-tryout').addEventListener('submit', function() {
        const btn = document.getElementById('btn-submit');

        // Disable tombol submit saja
        btn.disabled = true;

        // Loading state
        btn.innerHTML = `
        <span class="spinner-border spinner-border-sm mr-2"></span>
        Menyimpan...
    `;
    });
</script>


<?= $this->endSection(); ?>