<?php foreach (['lari_12', 'chinning', 'sit_up', 'push_up', 'shuttle_run', 'renang'] as $v): ?>
    <div class="form-row">
        <div class="col-8">
            <label><?= strtoupper($v) ?></label>
            <input type="number" name="<?= $v ?>" data-type="<?= $v ?>" class="form-control">
        </div>
        <div class="col-4">
            <label>Nilai</label>
            <input type="text" data-nilai="<?= $v ?>" class="form-control bg-light" readonly>
        </div>
    </div>
<?php endforeach ?>