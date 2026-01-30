<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<?php
$oldPilihan = old('pilihan') ?? [];
$oldProgram = old('program');
$oldProgram = is_array($oldProgram) ? $oldProgram : ($oldProgram ? [$oldProgram] : []);
?>

<div class="container-fluid">
    <div class="mb-5">
        <h1 class="h4 font-weight-bold text-gray-800 mb-1">
            Tambah Try Out <?= strtoupper($kategori) ?>
        </h1>
        <small class="text-muted">
            Lengkapi data try out untuk kategori <?= strtoupper($kategori) ?>
        </small>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="tryout-card">
                <div class="card-body">

                    <form id="form-tryout"
                        action="<?= site_url('tryout/' . $kategori . '/simpan') ?>"
                        method="post">

                        <?= csrf_field() ?>

                        <!-- JUDUL -->
                        <div class="form-group">
                            <label class="font-weight-bold">Judul Try Out *</label>
                            <input type="text" name="judul"
                                class="form-control rounded-pill px-4"
                                value="<?= old('judul') ?>" required>
                        </div>

                        <!-- PROGRAM -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold d-block mb-2">
                                Program *
                            </label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach ($program as $p): ?>
                                    <label class="program-pill">
                                        <input type="checkbox"
                                            name="program[]"
                                            value="<?= $p['key'] ?>"
                                            <?= in_array($p['key'], $oldProgram) ? 'checked' : '' ?>>
                                        <span><?= strtoupper($p['value']) ?></span>
                                    </label>
                                <?php endforeach ?>
                            </div>
                        </div>

                        <!-- PILIHAN UJIAN -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold d-block mb-2">
                                Pilihan Ujian *
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
                                                        <?= isset($oldPilihan[$prog]) && $oldPilihan[$prog] === $item['key']
                                                            ? 'checked' : '' ?>>
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
                            <label class="font-weight-bold">Jumlah Soal *</label>
                            <input type="number" name="jumlah_soal"
                                class="form-control rounded-pill px-4"
                                value="<?= old('jumlah_soal') ?>" required>
                        </div>

                        <!-- DURASI -->
                        <div class="form-group">
                            <label class="font-weight-bold">Durasi (Menit) *</label>
                            <input type="number" name="durasi"
                                class="form-control rounded-pill px-4"
                                value="<?= old('durasi') ?>" required>
                        </div>

                        <!-- MASA BERLAKU -->
                        <div class="form-group">
                            <label class="font-weight-bold">Masa Berlaku *</label>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <input type="date" name="tanggal_mulai"
                                        class="form-control rounded-pill px-4"
                                        value="<?= old('tanggal_mulai') ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="date" name="tanggal_selesai"
                                        class="form-control rounded-pill px-4"
                                        value="<?= old('tanggal_selesai') ?>" required>
                                </div>
                            </div>
                        </div>

                        <!-- ACTION -->
                        <div class="d-flex justify-content-end mt-4">
                            <a href="<?= site_url('tryout/' . $kategori) ?>"
                                class="btn btn-light rounded-pill px-4 mr-2">Batal</a>
                            <button type="submit"
                                id="btn-submit"
                                class="btn btn-success rounded-pill px-4">
                                Simpan Try Out
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
    .tryout-card {
        background: #fff;
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
    const programCheckboxes = document.querySelectorAll('input[name="program[]"]');
    const ujianGroups = document.querySelectorAll('.ujian-group');
    const form = document.getElementById('form-tryout');

    form.addEventListener('submit', function(e) {
        const checkedProgram = document.querySelectorAll('input[name="program[]"]:checked');

        if (checkedProgram.length === 0) {
            e.preventDefault();
            alert('Pilih minimal satu program (TNI / POLRI / KEDINASAN)');
            return false;
        }

        // VALIDASI: setiap program wajib pilih 1 ujian
        const programs = Array.from(checkedProgram).map(cb => cb.value);
        for (const prog of programs) {
            const ujianChecked = document.querySelector(`input[name="pilihan[${prog}]"]:checked`);
            if (!ujianChecked) {
                e.preventDefault();
                alert(`Pilih salah satu ujian untuk program ${prog.toUpperCase()}`);
                return false;
            }
        }

        const mulai = document.querySelector('input[name="tanggal_mulai"]').value;
        const selesai = document.querySelector('input[name="tanggal_selesai"]').value;
        if (mulai && selesai && selesai < mulai) {
            e.preventDefault();
            alert('Tanggal selesai tidak boleh lebih awal dari tanggal mulai');
            return false;
        }

        const btn = document.getElementById('btn-submit');
        btn.disabled = true;
        btn.innerHTML = `
            <span class="spinner-border spinner-border-sm mr-2"></span>
            Menyimpan...
        `;
    });

    function updatePilihan() {
        const selected = Array.from(programCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

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

    // =========================
    // RADIO TOGGLE (klik ulang bisa uncheck)
    // =========================
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('click', function(e) {
            if (this.checkedAlready) {
                this.checked = false;
            }
            // simpan status checked untuk semua radio di group
            document.querySelectorAll(`input[name="${this.name}"]`).forEach(r => r.checkedAlready = r.checked);
        });
    });
</script>

<?= $this->endSection(); ?>