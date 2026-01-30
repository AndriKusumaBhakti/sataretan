<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<?php
$oldProgram = old('program') ?? [];
?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">
            <div class="card video-form-binjas border-0 shadow-sm">
                <div class="card-body p-4">

                    <!-- HEADER -->
                    <div class="mb-4">
                        <h4 class="font-weight-bold mb-1">
                            Tambah Video <?= strtoupper($kategori) ?>
                        </h4>
                        <small class="text-muted">
                            Lengkapi data video pembelajaran
                        </small>
                    </div>

                    <!-- ERROR SERVER -->
                    <?php if (session('errors')): ?>
                        <div class="alert alert-danger">
                            <?php foreach (session('errors') as $err): ?>
                                <div><?= esc($err) ?></div>
                            <?php endforeach ?>
                        </div>
                    <?php endif ?>

                    <form id="form-video"
                        action="<?= base_url('video/store') ?>"
                        method="post"
                        enctype="multipart/form-data">

                        <?= csrf_field() ?>
                        <input type="hidden" name="kategori" value="<?= esc($kategori) ?>">

                        <!-- JUDUL -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">Judul Video</label>
                            <input type="text"
                                name="judul"
                                class="form-control rounded-pill px-4"
                                value="<?= old('judul') ?>"
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
                                            <?= in_array($p['key'], $oldProgram) ? 'checked' : '' ?>>
                                        <span><?= strtoupper($p['value']) ?></span>
                                    </label>
                                <?php endforeach ?>
                            </div>
                        </div>

                        <!-- TIPE -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">Tipe Video</label>
                            <select name="tipe"
                                class="form-control rounded-pill px-4"
                                required>
                                <option value="">-- Pilih Tipe --</option>
                                <option value="video" <?= old('tipe') === 'video' ? 'selected' : '' ?>>
                                    Video
                                </option>
                            </select>
                        </div>

                        <!-- SUMBER -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">Sumber Video</label>
                            <select name="sumber"
                                id="sumber"
                                class="form-control rounded-pill px-4"
                                required>
                                <option value="">-- Pilih Sumber --</option>
                                <option value="file" <?= old('sumber') === 'file' ? 'selected' : '' ?>>
                                    Upload File
                                </option>
                                <option value="link" <?= old('sumber') === 'link' ? 'selected' : '' ?>>
                                    Link
                                </option>
                            </select>
                        </div>

                        <!-- FILE -->
                        <div class="form-group mb-3 d-none" id="fileInput">
                            <label class="font-weight-semibold">Upload File</label>
                            <input type="file" name="file" class="form-control-file">
                            <small class="text-muted">Format video (mp4, mkv, dll)</small>
                        </div>

                        <!-- LINK -->
                        <div class="form-group mb-3 d-none" id="linkInput">
                            <label class="font-weight-semibold">Link Video</label>
                            <input type="url"
                                name="link"
                                class="form-control rounded-pill px-4"
                                placeholder="https://">
                        </div>

                        <!-- BUTTON -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="<?= base_url('video/' . $kategori) ?>"
                                class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>

                            <button id="btn-submit"
                                type="submit"
                                class="btn btn-success rounded-pill px-5">
                                <i class="fas fa-save mr-1"></i> Simpan Video
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
    const sumberSelect = document.getElementById('sumber');
    const fileInput = document.getElementById('fileInput');
    const linkInput = document.getElementById('linkInput');
    const form = document.getElementById('form-video');
    const btnSubmit = document.getElementById('btn-submit');

    function toggleSumber() {
        fileInput.classList.add('d-none');
        linkInput.classList.add('d-none');

        if (sumberSelect.value === 'file') {
            fileInput.classList.remove('d-none');
        }
        if (sumberSelect.value === 'link') {
            linkInput.classList.remove('d-none');
        }
    }

    sumberSelect.addEventListener('change', toggleSumber);
    document.addEventListener('DOMContentLoaded', toggleSumber);

    // ✅ VALIDASI PROGRAM + ANTI DOUBLE SUBMIT
    form.addEventListener('submit', function(e) {
        const checked = document.querySelectorAll('input[name="program[]"]:checked');

        if (checked.length === 0) {
            e.preventDefault();
            alert('Pilih minimal satu program (TNI / POLRI / KEDINASAN)');
            return false;
        }

        btnSubmit.disabled = true;
        btnSubmit.innerHTML = `
        <span class="spinner-border spinner-border-sm mr-2"></span>
        Menyimpan...
    `;
    });
</script>

<?= $this->endSection(); ?>