<?php
$isSiswa = ($kategori ?? '') === 'siswa';
$isGuru  = ($kategori ?? '') === 'guru';
?>

<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">

            <div class="card video-form-binjas border-0 shadow-sm">
                <div class="card-body p-4">

                    <!-- HEADER -->
                    <div class="mb-4">
                        <h4 class="font-weight-bold mb-1">
                            <?= $isSiswa ? 'Tambah Data Siswa' : 'Tambah Data Guru' ?>
                        </h4>
                        <small class="text-muted">
                            <?= $isSiswa
                                ? 'Lengkapi data akun siswa'
                                : 'Lengkapi data user guru' ?>
                        </small>
                    </div>

                    <!-- ERROR -->
                    <?php if (session('errors')): ?>
                        <div class="alert alert-danger">
                            <?php foreach (session('errors') as $err): ?>
                                <div><?= esc($err) ?></div>
                            <?php endforeach ?>
                        </div>
                    <?php endif ?>

                    <form id="form-user"
                        action="<?= base_url('master-data/' . $kategori . '/store') ?>"
                        method="post"
                        enctype="multipart/form-data">

                        <?= csrf_field() ?>

                        <!-- NAMA -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">
                                <?= $isSiswa ? 'Nama Lengkap' : 'Nama Guru' ?>
                            </label>
                            <input type="text"
                                name="name"
                                class="form-control rounded-pill px-4"
                                value="<?= old('name') ?>">
                        </div>

                        <!-- EMAIL -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">Email</label>
                            <input type="email"
                                name="email"
                                class="form-control rounded-pill px-4"
                                value="<?= old('email') ?>">
                        </div>

                        <!-- PHONE -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">No WhatsApp</label>
                            <input type="text"
                                name="phone"
                                class="form-control rounded-pill px-4"
                                value="<?= old('phone') ?>">
                        </div>


                        <!-- PAKET (KHUSUS SISWA) -->
                        <?php if ($isSiswa): ?>
                            <!-- PROGRAM -->
                            <div class="form-group mb-3">
                                <label>Program Tujuan</label>
                                <select name="program" id="program" class="form-control select-paket" required>
                                    <option value="">-- Pilih Program --</option>
                                    <?php foreach ($program as $u): ?>
                                        <option value="<?= $u['key'] ?>">
                                            <?= esc($u['value']) ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>

                            <!-- PAKET (HIDDEN AWAL) -->
                            <div class="form-group mb-3" id="paket-wrapper" style="display:none;">
                                <label>Paket Belajar</label>
                                <select name="paket_id" id="paket" class="form-control select-paket" required>
                                    <option value="">Pilih Paket</option>
                                    <?php foreach ($paket as $pkg): ?>
                                        <option value="<?= $pkg['id'] ?>">
                                            <?= esc($pkg['nama']) ?> (<?= $pkg['range_month'] ?> Bulan)
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        <?php endif ?>

                        <!-- PASSWORD -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">Password</label>
                            <input type="password"
                                name="password"
                                class="form-control rounded-pill px-4"
                                placeholder="Minimal 6 karakter">
                        </div>

                        <!-- FOTO -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">
                                <?= $isSiswa ? 'Foto Siswa' : 'Foto Guru' ?>
                            </label>
                            <input type="file"
                                name="photo"
                                class="form-control-file"
                                accept="image/*">
                            <small class="text-muted">
                                JPG / PNG, maksimal 2MB
                            </small>
                        </div>

                        <!-- BUTTON -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="<?= base_url(
                                            $isSiswa
                                                ? 'master-data/siswa'
                                                : 'master-data/guru'
                                        ) ?>"
                                class="btn btn-outline-secondary rounded-pill px-4">
                                Kembali
                            </a>

                            <button id="btn-submit"
                                type="submit"
                                class="btn <?= $isSiswa ? 'btn-success' : 'btn-success' ?> rounded-pill px-5">
                                Simpan <?= $isSiswa ? 'Siswa' : 'Guru' ?>
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<!-- STYLE -->
<style>
    .video-form-binjas {
        border-radius: 20px;
        box-shadow: 0 10px 28px rgba(0, 0, 0, .08);
    }

    .font-weight-semibold {
        font-weight: 600;
    }

    .form-control {
        height: 46px;
    }

    .form-control-file {
        margin-top: 6px;
    }
</style>

<!-- SCRIPT -->
<script>
    document.getElementById('form-user').addEventListener('submit', function() {
        const btn = document.getElementById('btn-submit');
        btn.disabled = true;
        btn.innerHTML = 'Menyimpan...';
    });
    // Program -> Paket logic
    const programSelect = document.getElementById('program');
    const paketWrapper = document.getElementById('paket-wrapper');
    const paketSelect = document.getElementById('paket');

    programSelect.addEventListener('change', function() {
        const program = this.value;
        console.log(program);
        if (!program) {
            paketWrapper.style.display = 'none';
            paketSelect.value = '';
            return;
        }

        paketWrapper.style.display = 'block';
    });
</script>

<?= $this->endSection(); ?>