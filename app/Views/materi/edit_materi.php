<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<?php
// Decode program JSON → array
$materiProgram = !empty($materi['program'])
    ? json_decode($materi['program'], true)
    : [];
?>

<div class="container-fluid">

    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">

            <div class="card materi-form-binjas border-0 shadow-sm">
                <div class="card-body p-4">

                    <!-- HEADER -->
                    <div class="mb-4">
                        <h4 class="font-weight-bold mb-1">
                            Edit Materi <?= strtoupper($kategori) ?>
                        </h4>
                        <small class="text-muted">
                            Perbarui informasi materi pembelajaran
                        </small>
                    </div>

                    <form id="form-materi"
                        action="<?= base_url('materi/update/' . $materi['id']) ?>"
                        method="post"
                        enctype="multipart/form-data">

                        <?= csrf_field() ?>

                        <input type="hidden" name="kategori" value="<?= esc($kategori) ?>">

                        <!-- JUDUL -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">Judul Materi</label>
                            <input type="text"
                                name="judul"
                                class="form-control rounded-pill px-4"
                                value="<?= esc($materi['judul']) ?>"
                                required>
                        </div>

                        <!-- PROGRAM -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold d-block mb-2">
                                Program
                            </label>

                            <div class="d-flex flex-wrap gap-2">

                                <label class="program-pill">
                                    <input type="checkbox" name="program[]" value="tni"
                                        <?= in_array('tni', $materiProgram) ? 'checked' : '' ?>>
                                    <span>TNI</span>
                                </label>

                                <label class="program-pill">
                                    <input type="checkbox" name="program[]" value="polri"
                                        <?= in_array('polri', $materiProgram) ? 'checked' : '' ?>>
                                    <span>POLRI</span>
                                </label>

                                <label class="program-pill">
                                    <input type="checkbox" name="program[]" value="kedinasan"
                                        <?= in_array('kedinasan', $materiProgram) ? 'checked' : '' ?>>
                                    <span>KEDINASAN</span>
                                </label>

                            </div>
                        </div>

                        <!-- TIPE -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">Tipe Materi</label>
                            <select name="tipe" class="form-control rounded-pill px-4" required>
                                <option value="">-- Pilih Tipe --</option>
                                <option value="pdf" <?= $materi['tipe'] === 'pdf' ? 'selected' : '' ?>>
                                    PDF
                                </option>
                            </select>
                        </div>

                        <!-- SUMBER -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">Sumber Materi</label>
                            <select name="sumber"
                                id="sumber"
                                class="form-control rounded-pill px-4"
                                required>
                                <option value="">-- Pilih Sumber --</option>
                                <option value="file" <?= $materi['sumber'] === 'file' ? 'selected' : '' ?>>
                                    Upload File
                                </option>
                                <option value="link" <?= $materi['sumber'] === 'link' ? 'selected' : '' ?>>
                                    Link
                                </option>
                            </select>
                        </div>

                        <!-- FILE -->
                        <div class="form-group mb-3 <?= $materi['sumber'] === 'file' ? '' : 'd-none' ?>"
                            id="fileInput">
                            <label class="font-weight-semibold">
                                Upload File (Opsional)
                            </label>

                            <input type="file"
                                name="file"
                                class="form-control-file">

                            <?php if (!empty($materi['file'])): ?>
                                <small class="text-muted d-block mt-1">
                                    File saat ini:
                                    <strong><?= esc($materi['file']) ?></strong>
                                </small>
                            <?php endif; ?>
                        </div>

                        <!-- LINK -->
                        <div class="form-group mb-3 <?= $materi['sumber'] === 'link' ? '' : 'd-none' ?>"
                            id="linkInput">
                            <label class="font-weight-semibold">Link Materi</label>
                            <input type="url"
                                name="link"
                                class="form-control rounded-pill px-4"
                                value="<?= esc($materi['link']) ?>">
                        </div>

                        <!-- BUTTON -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="<?= base_url('materi/' . $kategori) ?>"
                                class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>

                            <button id="btn-submit"
                                type="submit"
                                class="btn btn-success rounded-pill px-5">
                                <i class="fas fa-save mr-1"></i> Update Materi
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
    .materi-form-binjas {
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
        display: inline-flex;
        align-items: center;
        border: 1px solid #ddd;
        border-radius: 50px;
        padding: 6px 14px;
        cursor: pointer;
        background: #f8f9fa;
    }

    .program-pill input {
        display: none;
    }

    .program-pill span {
        font-size: 14px;
    }

    .program-pill input:checked+span {
        background: #28a745;
        color: #fff;
        padding: 6px 14px;
        border-radius: 50px;
    }
</style>

<!-- ================= SCRIPT ================= -->
<script>
    const sumberSelect = document.getElementById('sumber');
    const fileInput = document.getElementById('fileInput');
    const linkInput = document.getElementById('linkInput');

    function toggleSumber() {
        fileInput.classList.add('d-none');
        linkInput.classList.add('d-none');

        if (sumberSelect.value === 'file') {
            fileInput.classList.remove('d-none');
        } else if (sumberSelect.value === 'link') {
            linkInput.classList.remove('d-none');
        }
    }

    sumberSelect.addEventListener('change', toggleSumber);
    document.addEventListener('DOMContentLoaded', toggleSumber);

    document.getElementById('form-materi').addEventListener('submit', function() {
        const btn = document.getElementById('btn-submit');
        btn.disabled = true;
        btn.innerHTML = `
        <span class="spinner-border spinner-border-sm mr-2"></span>
        Menyimpan...
    `;
    });
</script>

<?= $this->endSection(); ?>