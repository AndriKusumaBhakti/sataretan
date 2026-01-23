<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">
            <div class="card materi-form-binjas border-0 shadow-sm">
                <div class="card-body p-4">

                    <!-- HEADER -->
                    <div class="mb-4">
                        <h4 class="font-weight-bold mb-1">
                            Tambah Materi <?= strtoupper($kategori) ?>
                        </h4>
                        <small class="text-muted">Lengkapi data materi pembelajaran</small>
                    </div>

                    <!-- ERROR -->
                    <?php if (session('errors')): ?>
                        <div class="alert alert-danger">
                            <?php foreach (session('errors') as $err): ?>
                                <div><?= esc($err) ?></div>
                            <?php endforeach ?>
                        </div>
                    <?php endif ?>

                    <form id="form-materi"
                        action="<?= base_url('materi/store') ?>"
                        method="post"
                        enctype="multipart/form-data">

                        <?= csrf_field() ?>

                        <input type="hidden" name="kategori" value="<?= esc($kategori) ?>">

                        <!-- JUDUL -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">
                                Judul Materi <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                name="judul"
                                class="form-control rounded-pill px-4"
                                value="<?= old('judul') ?>">
                        </div>

                        <!-- PROGRAM -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold d-block mb-2">
                                Program <span class="text-danger">*</span>
                            </label>
                            <div class="d-flex flex-wrap gap-2">
                                <?php
                                $oldProgram = old('program');
                                $oldProgram = is_array($oldProgram)
                                    ? $oldProgram
                                    : ($oldProgram ? [$oldProgram] : []);
                                ?>
                                <?php foreach (['tni' => 'TNI', 'polri' => 'POLRI', 'kedinasan' => 'KEDINASAN'] as $k => $v): ?>
                                    <label class="program-pill">
                                        <input type="checkbox"
                                            name="program[]"
                                            value="<?= $k ?>"
                                            <?= in_array($k, $oldProgram) ? 'checked' : '' ?>>
                                        <span><?= $v ?></span>
                                    </label>
                                <?php endforeach ?>
                            </div>
                        </div>

                        <!-- TIPE -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">
                                Tipe Materi <span class="text-danger">*</span>
                            </label>
                            <select name="tipe"
                                class="form-control rounded-pill px-4">
                                <option value="">-- Pilih --</option>
                                <option value="pdf" <?= old('tipe') === 'pdf' ? 'selected' : '' ?>>PDF</option>
                            </select>
                        </div>

                        <!-- SUMBER -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">
                                Sumber Materi <span class="text-danger">*</span>
                            </label>
                            <select name="sumber"
                                id="sumber"
                                class="form-control rounded-pill px-4">
                                <option value="">-- Pilih --</option>
                                <option value="file" <?= old('sumber') === 'file' ? 'selected' : '' ?>>Upload File</option>
                                <option value="link" <?= old('sumber') === 'link' ? 'selected' : '' ?>>Link</option>
                            </select>
                        </div>

                        <!-- FILE UTAMA -->
                        <div class="form-group mb-3 d-none" id="fileInput">
                            <label class="font-weight-semibold">
                                Upload File Utama
                            </label>
                            <input type="file" name="file" class="form-control-file">
                        </div>

                        <!-- LINK UTAMA -->
                        <div class="form-group mb-3 d-none" id="linkInput">
                            <label class="font-weight-semibold">
                                Link Materi
                            </label>
                            <input type="url"
                                name="link"
                                class="form-control rounded-pill px-4"
                                placeholder="https://">
                        </div>

                        <!-- SUB JUDUL -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold d-flex justify-content-between">
                                <span>Sub Judul Materi</span>
                                <button type="button"
                                    class="btn btn-sm btn-outline-success rounded-pill"
                                    id="btnAddSub">
                                    + Tambah Sub Judul
                                </button>
                            </label>

                            <div id="subJudulWrapper"></div>
                        </div>

                        <!-- BUTTON -->
                        <div class="d-flex justify-content-between mt-4">
                            <a href="<?= base_url('materi/' . $kategori) ?>"
                                class="btn btn-outline-secondary rounded-pill px-4">
                                Kembali
                            </a>
                            <button type="submit"
                                id="btn-submit"
                                class="btn btn-success rounded-pill px-5">
                                Simpan Materi
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

    /* PROGRAM PILL */
    .program-pill {
        position: relative;
        cursor: pointer;
    }

    .program-pill input {
        display: none;
    }

    .program-pill span {
        display: inline-block;
        padding: 10px 22px;
        border-radius: 999px;
        border: 2px solid #ced4da;
        font-weight: 600;
        transition: .2s;
        user-select: none;
    }

    .program-pill input:checked+span {
        background: #198754;
        border-color: #198754;
        color: #fff;
    }

    @media (max-width: 576px) {
        .materi-form-binjas {
            padding: 0;
        }
    }
</style>

<!-- ================= SCRIPT ================= -->
<script>
    const form = document.getElementById('form-materi');
    const sumberSelect = document.getElementById('sumber');
    const fileInput = document.getElementById('fileInput');
    const linkInput = document.getElementById('linkInput');
    const wrapper = document.getElementById('subJudulWrapper');
    const btnAddSub = document.getElementById('btnAddSub');

    function hideUtama() {
        fileInput.classList.add('d-none');
        linkInput.classList.add('d-none');
    }

    function toggleUtama() {
        if (wrapper.children.length > 0) {
            hideUtama();
            return;
        }

        hideUtama();

        if (sumberSelect.value === 'file') {
            fileInput.classList.remove('d-none');
        }
        if (sumberSelect.value === 'link') {
            linkInput.classList.remove('d-none');
        }
    }

    sumberSelect.addEventListener('change', toggleUtama);

    btnAddSub.onclick = () => {
        if (!sumberSelect.value) {
            alert('Pilih sumber materi terlebih dahulu');
            return;
        }

        const inputTambahan =
            sumberSelect.value === 'file' ?
            `<input type="file" name="sub_file[]" class="form-control-file">` :
            `<input type="url" name="sub_link[]" class="form-control rounded-pill px-4" placeholder="https://">`;

        const div = document.createElement('div');
        div.className = 'sub-item border rounded p-3 mb-2';
        div.innerHTML = `
            <input type="text"
                name="sub_judul[]"
                class="form-control rounded-pill px-4 mb-2"
                placeholder="Sub Judul">
            ${inputTambahan}
            <button type="button"
                class="btn btn-sm btn-danger mt-2 btnRemove">
                Hapus
            </button>
        `;

        wrapper.appendChild(div);
        toggleUtama();

        div.querySelector('.btnRemove').onclick = () => {
            div.remove();
            toggleUtama();
        };
    };

    // ================= VALIDASI SUBMIT =================
    form.addEventListener('submit', function(e) {

        if (document.querySelectorAll('input[name="program[]"]:checked').length === 0) {
            e.preventDefault();
            alert('Pilih minimal satu program (TNI / POLRI / KEDINASAN)');
            return;
        }

        if (!form.judul.value.trim()) {
            e.preventDefault();
            alert('Judul materi wajib diisi');
            return;
        }

        if (!form.tipe.value) {
            e.preventDefault();
            alert('Tipe materi wajib dipilih');
            return;
        }

        if (!form.sumber.value) {
            e.preventDefault();
            alert('Sumber materi wajib dipilih');
            return;
        }

        if (wrapper.children.length === 0) {
            if (form.sumber.value === 'file' && !form.file.value) {
                e.preventDefault();
                alert('Upload file utama');
                return;
            }

            if (form.sumber.value === 'link' && !form.link.value) {
                e.preventDefault();
                alert('Isi link materi');
                return;
            }
        }

        const btn = document.getElementById('btn-submit');
        btn.disabled = true;
        btn.innerHTML = `
            <span class="spinner-border spinner-border-sm mr-2"></span>
            Menyimpan...
        `;
    });
</script>

<?= $this->endSection(); ?>