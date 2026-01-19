<?= $this->extend('default/layout-template') ?>
<?= $this->section('content') ?>

<div class="container-fluid">

    <div class="text-center mb-4">
        <h1 class="h5 font-weight-bold text-white">
            Hitung Nilai Jasmani T.A. 2026
        </h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-5 col-lg-6 col-md-8">

            <form method="post" action="<?= site_url('tryout/jasmani/store') ?>" class="jasmani-card">
                <?= csrf_field() ?>
                <!-- PILIH USER -->
                <?php if ($isGuruOrAdmin): ?>
                    <div class="form-group">
                        <label>Pilih User</label>

                        <select name="user_id" class="form-control select-users" <?= empty($users) ? 'disabled' : 'required' ?>>
                            <option value="">
                                <?= empty($users) ? '-- Tidak ada user aktif --' : '-- Pilih User --' ?>
                            </option>

                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $u): ?>
                                    <option value="<?= $u['id'] ?>">
                                        <?= esc($u['name']) ?> - (<?= esc($u['email']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>

                        <?php if (empty($users)): ?>
                            <small class="text-warning">
                                User aktif belum tersedia
                            </small>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- KATEGORI -->
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori" id="kategori" class="form-control select-users">
                        <option value="tni">TNI</option>
                        <option value="polri">POLRI</option>
                    </select>
                </div>

                <!-- JENIS KELAMIN -->
                <div class="form-group">
                    <label>Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-control select-users">
                        <option value="Pria">Pria</option>
                        <option value="Wanita">Wanita</option>
                    </select>
                </div>

                <!-- KHUSUS TNI -->
                <div id="field-tni">

                    <div class="form-row">
                        <div class="form-group col-6">
                            <label>Usia</label>
                            <input type="number" name="usia" class="form-control">
                        </div>
                        <div class="form-group col-6">
                            <label>Tinggi (cm)</label>
                            <input type="number" name="tinggi" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Berat (kg)</label>
                        <input type="number" name="berat" class="form-control">
                    </div>

                    <div class="form-row">
                        <div class="form-group col-6">
                            <label>Kategori BMI</label>
                            <input type="text" class="form-control bg-light" readonly>
                        </div>
                        <div class="form-group col-6">
                            <label>Index BMI</label>
                            <input type="text" class="form-control bg-light" readonly>
                        </div>
                    </div>

                </div>

                <!-- GARJAS A -->
                <div class="divider">GARJAS A</div>

                <div class="form-row align-items-center">
                    <div class="form-group col-8">
                        <label>Jarak Lari 12 Menit (meter)</label>
                        <input type="number" name="lari_12" class="form-control">
                    </div>
                    <div class="form-group col-4">
                        <label>Nilai</label>
                        <input type="text" class="form-control bg-light" readonly>
                    </div>
                </div>

                <!-- GARJAS B -->
                <div class="divider">GARJAS B</div>

                <?php
                $items = [
                    'pull_up' => 'Pull Up (1 menit)',
                    'sit_up' => 'Sit Up (1 menit)',
                    'lunges' => 'Lunges',
                    'push_up' => 'Push Up (1 menit)',
                    'shuttle_run' => 'Shuttle Run',
                    'renang' => 'Renang (Polri)'
                ];
                foreach ($items as $name => $label): ?>
                    <div class="form-row align-items-center">
                        <div class="form-group col-8">
                            <label><?= $label ?></label>
                            <input type="number" name="<?= $name ?>" class="form-control">
                        </div>
                        <div class="form-group col-4">
                            <label>Nilai</label>
                            <input type="text" class="form-control bg-light" readonly>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- NILAI GARJAS B -->
                <div class="form-row align-items-center">
                    <div class="form-group col-8">
                        <label>Nilai Garjas B</label>
                        <input type="text" class="form-control bg-light" readonly>
                    </div>
                    <div class="form-group col-4">
                        <button type="button" class="btn btn-warning btn-block mt-4">
                            Hitung Garjas B
                        </button>
                    </div>
                </div>

                <!-- SUBMIT -->
                <button class="btn btn-warning btn-block font-weight-bold mt-3">
                    Hitung
                </button>

            </form>

        </div>
    </div>
</div>

<style>
    body {
        background: url('/assets/img/bg-military.jpg') center/cover no-repeat fixed;
    }

    .jasmani-card {
        background: rgba(255, 255, 255, .95);
        border-radius: 18px;
        padding: 22px;
        box-shadow: 0 18px 45px rgba(0, 0, 0, .25);
    }

    label {
        font-size: 12.5px;
        font-weight: 600;
        color: #6c757d;
    }

    .form-control {
        border-radius: 10px;
        font-size: 14px;
    }

    .bg-light {
        background: #edf0f2 !important;
    }

    .divider {
        margin: 16px 0 12px;
        padding: 6px 12px;
        font-size: 13px;
        font-weight: 700;
        background: #f1f3f5;
        color: #6c757d;
        border-radius: 8px;
    }

    .btn-warning {
        background: #27ae60;
        border: none;
        color: #fff;
    }

    .select-users {
        height: 48px;
        padding: 6px 42px 6px 14px;
        /* vertical kecil, horizontal rapi */
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        cursor: pointer;

        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='%23facc15' d='M5.5 7l4.5 5 4.5-5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        background-size: 14px;
    }
</style>

<script>
    document.getElementById('kategori').addEventListener('change', function() {
        document.getElementById('field-tni').style.display =
            this.value === 'tni' ? 'block' : 'none';
    });
</script>

<?= $this->endSection() ?>