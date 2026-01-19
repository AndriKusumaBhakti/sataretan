<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <div class="mb-5">
        <h1 class="h4 font-weight-bold text-gray-800 mb-1">
            Edit Try Out <?= strtoupper($kategori) ?>
        </h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">

            <div class="tryout-card">
                <div class="card-body">

                    <!-- FORM -->
                    <form id="form-edit-tryout"
                        action="<?= site_url('tryout/' . $kategori . '/update/' . $tryout['id']) ?>"
                        method="post">

                        <?= csrf_field() ?>

                        <div class="form-group">
                            <label class="font-weight-bold">Judul Try Out</label>
                            <input type="text"
                                name="judul"
                                value="<?= esc($tryout['judul']) ?>"
                                class="form-control rounded-pill px-4"
                                required>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Jumlah Soal</label>
                            <input type="number"
                                name="jumlah_soal"
                                value="<?= $tryout['jumlah_soal'] ?>"
                                class="form-control rounded-pill px-4"
                                required>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Durasi (Menit)</label>
                            <input type="number"
                                name="durasi"
                                value="<?= $tryout['durasi'] ?>"
                                class="form-control rounded-pill px-4"
                                required>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="<?= site_url('tryout/' . $kategori) ?>"
                                class="btn btn-light rounded-pill px-4 mr-2">
                                Batal
                            </a>

                            <button type="submit"
                                id="btn-update"
                                class="btn btn-success rounded-pill px-4">
                                Update
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
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
    }
</style>

<!-- ================= SCRIPT (ANTI DOUBLE SUBMIT) ================= -->
<script>
    document.getElementById('form-edit-tryout').addEventListener('submit', function() {
        const btn = document.getElementById('btn-update');

        // Disable tombol submit saja (AMAN)
        btn.disabled = true;

        // Loading state
        btn.innerHTML = `
        <span class="spinner-border spinner-border-sm mr-2"></span>
        Mengupdate...
    `;
    });
</script>

<?= $this->endSection(); ?>