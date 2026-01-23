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

                        <!-- ================= PROGRAM ================= -->
                        <?php
                        // Ambil program dari DB (json / array / string)
                        $dbProgram = $tryout['program'] ?? [];
                        if (is_string($dbProgram)) {
                            $decoded = json_decode($dbProgram, true);
                            $dbProgram = is_array($decoded) ? $decoded : [$dbProgram];
                        }

                        // Prioritas: old() > DB
                        $oldProgram = old('program');
                        $programSelected = is_array($oldProgram)
                            ? $oldProgram
                            : ($oldProgram ? [$oldProgram] : $dbProgram);
                        ?>

                        <?php if (session()->getFlashdata('errors')['program'] ?? false): ?>
                            <small class="text-danger d-block mb-2">
                                <?= session()->getFlashdata('errors')['program'] ?>
                            </small>
                        <?php endif; ?>

                        <!-- JUDUL -->
                        <div class="form-group">
                            <label class="font-weight-bold">
                                Judul Try Out <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                name="judul"
                                value="<?= old('judul') ?? esc($tryout['judul']) ?>"
                                class="form-control rounded-pill px-4"
                                required>
                        </div>

                        <!-- PROGRAM -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold d-block mb-2">
                                Program <span class="text-danger">*</span>
                            </label>

                            <div class="d-flex flex-wrap gap-2">

                                <label class="program-pill">
                                    <input type="checkbox"
                                        name="program[]"
                                        value="tni"
                                        <?= in_array('tni', $programSelected) ? 'checked' : '' ?>>
                                    <span>TNI</span>
                                </label>

                                <label class="program-pill">
                                    <input type="checkbox"
                                        name="program[]"
                                        value="polri"
                                        <?= in_array('polri', $programSelected) ? 'checked' : '' ?>>
                                    <span>POLRI</span>
                                </label>

                                <label class="program-pill">
                                    <input type="checkbox"
                                        name="program[]"
                                        value="kedinasan"
                                        <?= in_array('kedinasan', $programSelected) ? 'checked' : '' ?>>
                                    <span>KEDINASAN</span>
                                </label>

                            </div>
                        </div>
                        <!-- ================= END PROGRAM ================= -->

                        <!-- JUMLAH SOAL -->
                        <div class="form-group">
                            <label class="font-weight-bold">
                                Jumlah Soal <span class="text-danger">*</span>
                            </label>
                            <input type="number"
                                name="jumlah_soal"
                                value="<?= old('jumlah_soal') ?? $tryout['jumlah_soal'] ?>"
                                class="form-control rounded-pill px-4"
                                required>
                        </div>

                        <!-- DURASI -->
                        <div class="form-group">
                            <label class="font-weight-bold">
                                Durasi (Menit) <span class="text-danger">*</span>
                            </label>
                            <input type="number"
                                name="durasi"
                                value="<?= old('durasi') ?? $tryout['durasi'] ?>"
                                class="form-control rounded-pill px-4"
                                required>
                        </div>

                        <!-- ACTION -->
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
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
    }

    .form-control {
        height: 46px;
    }

    /* PROGRAM PILL */
    .program-pill {
        position: relative;
        cursor: pointer;
    }

    .program-pill input {
        display: none;
    }

    .program-pill span {
        display: inline-block;
        padding: 8px 18px;
        border-radius: 999px;
        border: 1px solid #d1d3e2;
        background: #f8f9fc;
        font-weight: 600;
        transition: .2s ease;
        user-select: none;
    }

    .program-pill input:checked+span {
        background: #1cc88a;
        color: #fff;
        border-color: #1cc88a;
    }
</style>

<!-- ================= SCRIPT ================= -->
<script>
    const formEdit = document.getElementById('form-edit-tryout');

    formEdit.addEventListener('submit', function(e) {

        const checkedProgram = document.querySelectorAll(
            'input[name="program[]"]:checked'
        );

        if (checkedProgram.length === 0) {
            e.preventDefault();
            alert('Pilih minimal satu program (TNI / POLRI / KEDINASAN)');
            return false;
        }

        const btn = document.getElementById('btn-update');
        btn.disabled = true;
        btn.innerHTML = `
            <span class="spinner-border spinner-border-sm mr-2"></span>
            Mengupdate...
        `;
    });
</script>

<?= $this->endSection(); ?>