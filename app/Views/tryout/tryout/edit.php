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

                        <?php
                        // Ambil program dari DB
                        $dbProgram = $tryout['program'] ?? [];
                        if (is_string($dbProgram)) {
                            $decoded = json_decode($dbProgram, true);
                            $dbProgram = is_array($decoded) ? $decoded : [];
                        }

                        // Ambil pilihan ujian dari DB
                        $dbPilihan = $tryout['ujian'] ?? [];
                        if (is_string($dbPilihan)) {
                            $decoded = json_decode($dbPilihan, true);
                            $dbPilihan = is_array($decoded) ? $decoded : [];
                        }

                        // Prioritas old() > DB
                        $programSelected = old('program') ?? $dbProgram;
                        $pilihanSelected = old('pilihan') ?? $dbPilihan;
                        ?>

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
                                <?php foreach ($program as $p): ?>
                                    <label class="program-pill">
                                        <input type="checkbox"
                                            name="program[]"
                                            value="<?= $p['key'] ?>"
                                            <?= in_array($p['key'], $programSelected) ? 'checked' : '' ?>>
                                        <span><?= strtoupper($p['value']) ?></span>
                                    </label>
                                <?php endforeach ?>
                            </div>
                        </div>

                        <!-- PILIHAN UJIAN -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold d-block mb-2">
                                Pilihan Ujian <span class="text-danger">*</span>
                            </label>

                            <?php foreach ($filterProgram[$kategori] as $prog => $keys): ?>
                                <div class="ujian-group mb-3"
                                    data-program="<?= $prog ?>"
                                    style="display:none">

                                    <small class="text-muted font-weight-bold">
                                        <?= strtoupper($prog) ?>
                                    </small>

                                    <div class="d-flex flex-wrap gap-2 mt-1">
                                        <?php foreach ($pilihan as $item): ?>
                                            <?php if (in_array($item['key'], $keys)): ?>
                                                <label class="program-pill">
                                                    <input type="radio"
                                                        name="pilihan[<?= $prog ?>]"
                                                        value="<?= $item['key'] ?>"
                                                        <?= isset($pilihanSelected[$prog]) && $pilihanSelected[$prog] === $item['key'] ? 'checked' : '' ?>>
                                                    <span><?= $item['value'] ?></span>
                                                </label>
                                            <?php endif ?>
                                        <?php endforeach ?>
                                    </div>
                                </div>
                            <?php endforeach ?>

                            <small class="text-muted">
                                Pilih satu ujian untuk setiap program yang dipilih
                            </small>
                        </div>

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

                        <!-- MASA BERLAKU -->
                        <div class="form-group">
                            <label class="font-weight-bold">
                                Masa Berlaku Try Out <span class="text-danger">*</span>
                            </label>

                            <div class="row">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <input type="date"
                                        name="tanggal_mulai"
                                        class="form-control rounded-pill px-4"
                                        value="<?= old('tanggal_mulai') ?? $tryout['tanggal_mulai'] ?>"
                                        required>
                                </div>
                                <div class="col-md-6">
                                    <input type="date"
                                        name="tanggal_selesai"
                                        class="form-control rounded-pill px-4"
                                        value="<?= old('tanggal_selesai') ?? $tryout['tanggal_selesai'] ?>"
                                        required>
                                </div>
                            </div>
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
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
    }

    .form-control {
        height: 46px;
    }

    .program-pill {
        cursor: pointer;
    }

    .program-pill input {
        display: none;
    }

    .program-pill span {
        padding: 8px 18px;
        border-radius: 999px;
        border: 1px solid #d1d3e2;
        background: #f8f9fc;
        font-weight: 600;
        transition: .2s ease;
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
    const programCheckboxes = document.querySelectorAll('input[name="program[]"]');
    const ujianGroups = document.querySelectorAll('.ujian-group');

    // SHOW/HIDE UJIAN PER PROGRAM
    function updatePilihan() {
        const selected = Array.from(programCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
        ujianGroups.forEach(group => {
            const prog = group.dataset.program;
            if (selected.includes(prog)) {
                group.style.display = 'block';
            } else {
                group.style.display = 'none';
                group.querySelectorAll('input[type="radio"]').forEach(i => i.checked = false);
            }
        });
    }
    programCheckboxes.forEach(cb => cb.addEventListener('change', updatePilihan));
    updatePilihan();

    // RADIO TOGGLE: klik ulang bisa uncheck
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('click', function() {
            if (this.checkedAlready) this.checked = false;
            document.querySelectorAll(`input[name="${this.name}"]`).forEach(r => r.checkedAlready = r.checked);
        });
    });

    // SUBMIT VALIDASI
    formEdit.addEventListener('submit', function(e) {
        const checkedProgram = document.querySelectorAll('input[name="program[]"]:checked');
        if (checkedProgram.length === 0) {
            e.preventDefault();
            alert('Pilih minimal satu program (TNI / POLRI / KEDINASAN)');
            return false;
        }

        // tiap program wajib pilih ujian
        const programs = Array.from(checkedProgram).map(cb => cb.value);
        for (const prog of programs) {
            const ujianChecked = document.querySelector(`input[name="pilihan[${prog}]"]:checked`);
            if (!ujianChecked) {
                e.preventDefault();
                alert(`Pilih satu ujian untuk program ${prog.toUpperCase()}`);
                return false;
            }
        }

        const btn = document.getElementById('btn-update');
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm mr-2"></span>Mengupdate...`;
    });
</script>

<?= $this->endSection(); ?>