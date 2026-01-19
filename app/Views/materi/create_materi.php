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
                        <small class="text-muted">
                            Lengkapi data materi pembelajaran
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

                    <form id="form-materi" action="<?= base_url('materi/store') ?>"
                        method="post"
                        enctype="multipart/form-data">

                        <input type="hidden" name="kategori" value="<?= esc($kategori) ?>">

                        <!-- JUDUL -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">Judul Materi</label>
                            <input type="text"
                                name="judul"
                                class="form-control rounded-pill px-4"
                                placeholder="Masukkan judul materi"
                                value="<?= old('judul') ?>">
                        </div>

                        <!-- TIPE -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">Tipe Materi</label>
                            <select name="tipe"
                                class="form-control rounded-pill px-4">
                                <option value="">-- Pilih Tipe --</option>
                                <option value="pdf" <?= old('tipe') === 'pdf' ? 'selected' : '' ?>>PDF</option>
                            </select>
                        </div>

                        <!-- SUMBER -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">Sumber Materi</label>
                            <select name="sumber"
                                id="sumber"
                                class="form-control rounded-pill px-4">
                                <option value="">-- Pilih Sumber --</option>
                                <option value="file" <?= old('sumber') === 'file' ? 'selected' : '' ?>>Upload File</option>
                                <option value="link" <?= old('sumber') === 'link' ? 'selected' : '' ?>>Link</option>
                            </select>
                        </div>

                        <!-- FILE -->
                        <div class="form-group mb-3 d-none" id="fileInput">
                            <label class="font-weight-semibold">Upload File</label>
                            <input type="file" name="file" class="form-control-file">
                            <small class="text-muted">
                                PDF / Word
                            </small>
                        </div>

                        <!-- LINK -->
                        <div class="form-group mb-3 d-none" id="linkInput">
                            <label class="font-weight-semibold">Link Materi</label>
                            <input type="url"
                                name="link"
                                class="form-control rounded-pill px-4"
                                placeholder="https://">
                        </div>

                        <!-- BUTTON -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="<?= base_url('materi/' . $kategori) ?>"
                                class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>

                            <button id="btn-submit" type="submit"
                                class="btn btn-success rounded-pill px-5">
                                <i class="fas fa-save mr-1"></i> Simpan Materi
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
    /* ===== FORM CARD BINJAS ===== */
    .materi-form-binjas {
        border-radius: 20px;
        box-shadow: 0 10px 28px rgba(0, 0, 0, .08);
    }

    /* LABEL */
    .font-weight-semibold {
        font-weight: 600;
    }

    /* INPUT */
    .form-control {
        height: 46px;
    }

    /* FILE INPUT */
    .form-control-file {
        margin-top: 6px;
    }

    /* RESPONSIVE */
    @media (max-width: 576px) {
        .materi-form-binjas {
            padding: 0;
        }
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

        // Disable tombol submit saja
        btn.disabled = true;

        // Loading state
        btn.innerHTML = `
        <span class="spinner-border spinner-border-sm mr-2"></span>
        Menyimpan...
    `;
    });
</script>

<?= $this->endSection(); ?>