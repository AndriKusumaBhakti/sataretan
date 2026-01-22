<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<?php
$materiProgram = !empty($materi['program'])
    ? explode(',', $materi['program'])
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
                        <small class="text-muted">Perbarui informasi materi pembelajaran</small>
                    </div>

                    <form action="<?= base_url('materi/update/' . $materi['id']) ?>"
                        method="post"
                        enctype="multipart/form-data">

                        <?= csrf_field() ?>
                        <input type="hidden" name="kategori" value="<?= esc($kategori) ?>">

                        <!-- JUDUL -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">Judul Materi</label>
                            <input type="text" name="judul"
                                class="form-control rounded-pill px-4"
                                value="<?= esc($materi['judul']) ?>" required>
                        </div>

                        <!-- PROGRAM -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold d-block mb-2">Program</label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php foreach (['tni', 'polri', 'kedinasan'] as $p): ?>
                                    <label class="program-pill">
                                        <input type="checkbox" name="program[]" value="<?= $p ?>"
                                            <?= in_array($p, $materiProgram) ? 'checked' : '' ?>>
                                        <span><?= strtoupper($p) ?></span>
                                    </label>
                                <?php endforeach ?>
                            </div>
                        </div>

                        <!-- TIPE -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">Tipe Materi</label>
                            <select name="tipe" class="form-control rounded-pill px-4" required>
                                <option value="pdf" selected>PDF</option>
                            </select>
                        </div>

                        <!-- SUMBER -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">Sumber Materi</label>
                            <select name="sumber" id="sumber"
                                class="form-control rounded-pill px-4" required>
                                <option value="">-- Pilih --</option>
                                <option value="file" <?= $materi['sumber'] === 'file' ? 'selected' : '' ?>>File</option>
                                <option value="link" <?= $materi['sumber'] === 'link' ? 'selected' : '' ?>>Link</option>
                            </select>
                        </div>

                        <!-- FILE UTAMA -->
                        <div id="fileInput" class="form-group mb-3">
                            <label>Upload File (opsional)</label>
                            <input type="file" name="file" class="form-control-file">
                            <?php if ($materi['file']): ?>
                                <small class="text-muted">File saat ini: <?= esc($materi['file']) ?></small>
                            <?php endif ?>
                        </div>

                        <!-- LINK UTAMA -->
                        <div id="linkInput" class="form-group mb-3">
                            <label>Link Materi</label>
                            <input type="url" name="link"
                                class="form-control rounded-pill px-4"
                                value="<?= esc($materi['link']) ?>">
                        </div>

                        <hr>

                        <!-- SUB MATERI -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold d-flex justify-content-between">
                                <span>Sub Materi</span>
                                <button type="button"
                                    id="btnAddSub"
                                    class="btn btn-sm btn-outline-success rounded-pill">
                                    + Tambah Sub Judul
                                </button>
                            </label>

                            <div id="subWrapper">
                                <?php foreach ($subMateri as $sub): ?>
                                    <div class="sub-item border rounded p-3 mb-2">
                                        <input type="text" name="sub_judul[]"
                                            class="form-control rounded-pill px-4 mb-2"
                                            value="<?= esc($sub['sub_judul']) ?>" required>

                                        <?php if ($materi['sumber'] === 'file'): ?>
                                            <input type="file" name="sub_file[]" class="form-control-file">
                                            <?php if ($sub['file']): ?>
                                                <small class="text-muted">File: <?= esc($sub['file']) ?></small>
                                            <?php endif ?>
                                        <?php else: ?>
                                            <input type="url" name="sub_link[]"
                                                class="form-control rounded-pill px-4"
                                                value="<?= esc($sub['link']) ?>">
                                        <?php endif ?>

                                        <button type="button"
                                            class="btn btn-sm btn-danger mt-2 btnRemove">
                                            Hapus
                                        </button>
                                    </div>
                                <?php endforeach ?>
                            </div>
                        </div>

                        <!-- BUTTON -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?= base_url('materi/' . $kategori) ?>"
                                class="btn btn-outline-secondary rounded-pill px-4">
                                Kembali
                            </a>
                            <button class="btn btn-success rounded-pill px-5">
                                Update Materi
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
    const sumber = document.getElementById('sumber');
    const fileInput = document.getElementById('fileInput');
    const linkInput = document.getElementById('linkInput');
    const subWrapper = document.getElementById('subWrapper');
    const btnAddSub = document.getElementById('btnAddSub');

    function hideUtama() {
        fileInput.classList.add('d-none');
        linkInput.classList.add('d-none');
    }

    function toggleUtama() {
        const jumlahSub = subWrapper.querySelectorAll('.sub-item').length;

        // ❌ ADA SUB → FILE & LINK UTAMA HILANG
        if (jumlahSub >= 1) {
            hideUtama();
            return;
        }

        // ✅ TIDAK ADA SUB → TAMPIL SESUAI SUMBER
        hideUtama();
        if (sumber.value === 'file') fileInput.classList.remove('d-none');
        if (sumber.value === 'link') linkInput.classList.remove('d-none');
    }

    sumber.addEventListener('change', toggleUtama);

    btnAddSub.onclick = () => {
        if (!sumber.value) {
            alert('Pilih sumber dulu');
            return;
        }

        const inputSumber = sumber.value === 'file' ?
            `<input type="file" name="sub_file[]" class="form-control-file">` :
            `<input type="url" name="sub_link[]" class="form-control rounded-pill px-4">`;

        const div = document.createElement('div');
        div.className = 'sub-item border rounded p-3 mb-2';
        div.innerHTML = `
            <input type="text" name="sub_judul[]"
                   class="form-control rounded-pill px-4 mb-2" required>
            ${inputSumber}
            <button type="button"
                    class="btn btn-sm btn-danger mt-2 btnRemove">
                Hapus
            </button>
        `;
        subWrapper.appendChild(div);
        toggleUtama();
    };

    document.addEventListener('click', e => {
        if (e.target.classList.contains('btnRemove')) {
            e.target.closest('.sub-item').remove();
            toggleUtama();
        }
    });

    // 🔥 JALANKAN SAAT HALAMAN DIBUKA
    document.addEventListener('DOMContentLoaded', toggleUtama);
</script>

<?= $this->endSection(); ?>