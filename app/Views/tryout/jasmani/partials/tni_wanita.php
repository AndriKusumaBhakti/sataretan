<!-- ================= USIA ================= -->
<div class="form-group">
    <label>Usia</label>
    <input type="number" name="usia" class="form-control usia" min="16" max="40" required>
</div>

<!-- ================= BMI ================= -->
<div class="divider">BMI</div>
<div class="form-row">
    <div class="col-4">
        <label>Tinggi (cm)</label>
        <input type="number" name="tinggi" class="form-control tinggi">
    </div>
    <div class="col-4">
        <label>Berat (kg)</label>
        <input type="number" name="berat" class="form-control berat">
    </div>
    <div class="col-2">
        <label>Index BMI</label>
        <input type="text" name="bmi" class="form-control bg-light nilai_bmi" readonly>
    </div>
    <div class="col-2">
        <label>Kategori</label>
        <input type="text" name="kategori_bmi" class="form-control bg-light kategori_bmi" readonly>
    </div>
</div>

<div class="divider">Garjas A</div>
<div class="form-row">
    <div class="col-8">
        <label>Lari 12 Menit (meter)</label>
        <input type="number" name="lari_12" data-type="lari_12" class="form-control">
    </div>
    <div class="col-4">
        <label>Nilai</label>
        <input type="text" data-nilai="lari_12" class="form-control bg-light" readonly>
    </div>
</div>

<div class="divider">Garjas B</div>

<?php
$items = [
    'chinning'    => 'Chinning',
    'sit_up'      => 'Sit Up (1 menit)',
    'push_up'     => 'Push Up (1 menit)',
    'shuttle_run' => 'Shuttle Run',
    'renang'      => 'Renang',
];
foreach ($items as $key => $label):
?>
    <div class="form-row">
        <div class="col-8">
            <label><?= $label ?></label>
            <input type="number" name="<?= $key ?>" data-type="<?= $key ?>" class="form-control">
        </div>
        <div class="col-4">
            <label>Nilai</label>
            <input type="text" name="nilai_<?= $key ?>" data-nilai="<?= $key ?>" class="form-control bg-light" readonly>
        </div>
    </div>
<?php endforeach ?>

<div class="form-group mt-2">
    <label>Nilai Garjas B</label>
    <input type="text" data-nilai="garjas_b" class="form-control bg-light" readonly>
</div>