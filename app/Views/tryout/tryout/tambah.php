<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<?php
$oldPilihan = old('pilihan') ?? [];
$oldProgram = old('program');
$oldProgram = is_array($oldProgram) ? $oldProgram : ($oldProgram ? [$oldProgram] : []);
?>

<div class="container-fluid container-tryout">
    <div class="mb-4">
        <h1 class="h4 font-weight-bold text-gray-800 mb-1">
            Tambah Try Out <?= strtoupper($kategori) ?>
        </h1>
        <small class="text-muted">
            Lengkapi data try out untuk kategori <?= strtoupper($kategori) ?>
        </small>
    </div>

    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-7 col-xl-6">
            <div class="tryout-card">
                <div class="card-body p-3 p-md-4">

                    <form id="form-tryout"
                        action="<?= site_url('tryout/' . $kategori . '/simpan') ?>"
                        method="post">

                        <?= csrf_field() ?>

                        <!-- JUDUL -->
                        <div class="form-group">
                            <label class="font-weight-bold">Judul Try Out *</label>
                            <input type="text"
                                name="judul"
                                class="form-control rounded-pill px-4"
                                value="<?= old('judul') ?>"
                                required>
                        </div>

                        <!-- PROGRAM -->
                        <div class="form-group">
                            <label class="font-weight-semibold d-block mb-2">Program *</label>
                            <div class="pill-grid">
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
                        <div class="form-group">
                            <label class="font-weight-semibold d-block mb-2">Pilihan Ujian *</label>

                            <?php foreach ($filterProgram[$kategori] as $prog => $keys): ?>
                                <div class="ujian-group mb-3"
                                    data-program="<?= $prog ?>"
                                    style="display:none">

                                    <small class="text-muted font-weight-bold d-block mb-1">
                                        <?= strtoupper($prog) ?>
                                    </small>

                                    <div class="pill-grid">
                                        <?php foreach ($pilihan as $item): ?>
                                            <?php if (in_array($item['key'], $keys)): ?>
                                                <label class="program-pill">
                                                    <input type="radio"
                                                        name="pilihan[<?= $prog ?>]"
                                                        value="<?= $item['key'] ?>"
                                                        data-mode="<?= $item['mode'] ?>"
                                                        <?= isset($oldPilihan[$prog]) && $oldPilihan[$prog] === $item['key']
                                                            ? 'checked' : '' ?>>
                                                    <span>
                                                        <?= $item['value'] ?>
                                                        <?php if ($item['mode'] === 'online'): ?>
                                                            <small class="badge badge-success ml-1">ONLINE</small>
                                                        <?php else: ?>
                                                            <small class="badge badge-secondary ml-1">OFFLINE</small>
                                                        <?php endif ?>
                                                    </span>
                                                </label>
                                            <?php endif ?>
                                        <?php endforeach ?>
                                    </div>
                                </div>
                            <?php endforeach ?>

                            <small class="text-muted d-block mt-2">
                                Pilih satu ujian untuk setiap program yang dipilih
                            </small>
                        </div>

                        <!-- JUMLAH SOAL -->
                        <div class="form-group">
                            <label class="font-weight-bold">Jumlah Soal</label>
                            <input type="number"
                                id="jumlah_soal"
                                name="jumlah_soal"
                                class="form-control rounded-pill px-4"
                                value="<?= old('jumlah_soal') ?>">
                        </div>

                        <!-- DURASI -->
                        <div class="form-group">
                            <label class="font-weight-bold">Durasi (Menit)</label>
                            <input type="number"
                                id="durasi"
                                name="durasi"
                                class="form-control rounded-pill px-4"
                                value="<?= old('durasi') ?>">
                        </div>

                        <!-- MASA BERLAKU -->
                        <div class="form-group">
                            <label class="font-weight-bold">Masa Berlaku *</label>
                            <div class="row">
                                <div class="col-12 col-md-6 mb-2">
                                    <input type="date"
                                        id="tanggal_mulai"
                                        name="tanggal_mulai"
                                        class="form-control rounded-pill px-4"
                                        value="<?= old('tanggal_mulai') ?>"
                                        required>
                                </div>
                                <div class="col-12 col-md-6">
                                    <input type="date"
                                        id="tanggal_selesai"
                                        name="tanggal_selesai"
                                        class="form-control rounded-pill px-4"
                                        value="<?= old('tanggal_selesai') ?>"
                                        required>
                                </div>
                            </div>
                        </div>

                        <!-- ACTION -->
                        <div class="action-bar">
                            <a href="<?= site_url('tryout/' . $kategori) ?>"
                                class="btn btn-light rounded-pill px-4">
                                Batal
                            </a>
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
    .container-tryout {
        max-width: 100%;
    }

    /* Card */
    .tryout-card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
    }

    /* Input */
    .form-control {
        height: 46px;
    }

    /* Pill grid responsive */
    .pill-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
        gap: 10px;
    }

    /* Pills */
    .program-pill {
        cursor: pointer;
    }

    .program-pill input {
        display: none;
    }

    .program-pill span {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 14px;
        border-radius: 999px;
        border: 1px solid #d1d3e2;
        background: #f8f9fc;
        font-weight: 600;
        font-size: 14px;
        transition: .2s ease;
        text-align: center;
    }

    .program-pill input:checked+span {
        background: #1cc88a;
        color: #fff;
        border-color: #1cc88a;
    }

    /* Action bar */
    .action-bar {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 24px;
    }

    /* Mobile tweaks */
    @media (max-width: 576px) {
        .form-control {
            height: 42px;
        }

        .action-bar {
            flex-direction: column;
        }

        .action-bar .btn {
            width: 100%;
        }
    }
</style>
<!-- ================= SCRIPT ================= -->
<script>
    const programCheckboxes = document.querySelectorAll('input[name="program[]"]');
    const ujianGroups = document.querySelectorAll('.ujian-group');
    const form = document.getElementById('form-tryout');

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
                group.querySelectorAll('input[type="radio"]').forEach(r => r.checked = false);
            }
        });

        updateModeUjian();
    }

    function updateModeUjian() {
        const radios = document.querySelectorAll('input[type="radio"]:checked');
        let hasOnline = false;

        radios.forEach(r => {
            if (r.dataset.mode === 'online') hasOnline = true;
        });

        const jumlahSoal = document.getElementById('jumlah_soal');
        const durasi = document.getElementById('durasi');
        const tanggalMulai = document.getElementById('tanggal_mulai');
        const tanggalSelesai = document.getElementById('tanggal_selesai');

        if (hasOnline) {
            jumlahSoal.disabled = false;
            durasi.disabled = false;
            tanggalMulai.disabled = false;
            tanggalSelesai.disabled = false;
            jumlahSoal.required = true;
            durasi.required = true;
            tanggalMulai.required = true;
            tanggalSelesai.required = true;
        } else {
            jumlahSoal.disabled = true;
            durasi.disabled = true;
            tanggalMulai.disabled = true;
            tanggalSelesai.disabled = true;
            jumlahSoal.required = false;
            durasi.required = false;
            tanggalMulai.required = false;
            tanggalSelesai.required = false;
            jumlahSoal.value = '';
            durasi.value = '';
            tanggalMulai.value = '';
            tanggalSelesai.value = '';
        }
    }

    programCheckboxes.forEach(cb => cb.addEventListener('change', updatePilihan));
    document.querySelectorAll('input[type="radio"]').forEach(r => r.addEventListener('change', updateModeUjian));

    updatePilihan();

    form.addEventListener('submit', function(e) {
        const checkedProgram = document.querySelectorAll('input[name="program[]"]:checked');
        if (!checkedProgram.length) {
            e.preventDefault();
            alert('Pilih minimal satu program');
            return;
        }

        for (const cb of checkedProgram) {
            const prog = cb.value;
            if (!document.querySelector(`input[name="pilihan[${prog}]"]:checked`)) {
                e.preventDefault();
                alert(`Pilih ujian untuk program ${prog.toUpperCase()}`);
                return;
            }
        }
    });
</script>

<?= $this->endSection(); ?>