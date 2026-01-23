<?php
$items = [
    'lari_12'     => 'Lari 12 Menit (meter)',
    'pull_up'      => 'Pull Up (1 menit)',
    'sit_up'     => 'Sit Up (1 menit)',
    'push_up' => 'Push Up (1 menit)',
    'shuttle_run'      => 'Shuttle Run',
    'renang'      => 'Renang',
];
foreach ($items as $key => $label): ?>
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