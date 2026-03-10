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
                            Edit Parameter
                        </h4>
                        <small class="text-muted">
                            Perbarui konfigurasi parameter sistem
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

                    <form id="form-param"
                        action="<?= base_url('maintenance/parameter/update/' . $param['id']) ?>"
                        method="post">

                        <?= csrf_field() ?>

                        <!-- CODE -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">Code Parameter</label>

                            <input type="text"
                                name="code"
                                class="form-control rounded-pill px-4"
                                value="<?= old('code', $param['code']) ?>">
                        </div>

                        <!-- VALUE -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">Value (JSON)</label>

                            <textarea name="value"
                                class="form-control"
                                rows="8"><?= old('value', $param['value']) ?></textarea>

                            <small class="text-muted">
                                Gunakan format JSON valid
                            </small>

                        </div>

                        <!-- BUTTON -->
                        <div class="d-flex justify-content-between align-items-center mt-4">

                            <a href="<?= base_url('maintenance/parameter') ?>"
                                class="btn btn-outline-secondary rounded-pill px-4">
                                Kembali
                            </a>

                            <button id="btn-submit"
                                type="submit"
                                class="btn btn-success rounded-pill px-5">
                                Update Parameter
                            </button>

                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .video-form-binjas {
        border-radius: 20px;
        box-shadow: 0 10px 28px rgba(0, 0, 0, .08);
    }

    .font-weight-semibold {
        font-weight: 600;
    }

    .form-control {
        min-height: 46px;
    }

    textarea.form-control {
        border-radius: 14px;
    }
</style>

<script>
    document.getElementById('form-param').addEventListener('submit', function() {

        const btn = document.getElementById('btn-submit');

        btn.disabled = true;
        btn.innerHTML = 'Menyimpan...';

    });
</script>

<?= $this->endSection(); ?>