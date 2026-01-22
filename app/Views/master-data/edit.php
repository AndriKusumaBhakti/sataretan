<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">

            <div class="card user-form-modern border-0 shadow-sm">
                <div class="card-body p-4">

                    <!-- HEADER -->
                    <div class="mb-4">
                        <h4 class="font-weight-bold mb-1">
                            Edit User <?= strtoupper($kategori) ?>
                        </h4>
                        <small class="text-muted">
                            Perbarui informasi akun pengguna
                        </small>
                    </div>

                    <form action="<?= base_url('master-data/' . $kategori . '/update/' . $user['id']) ?>"
                        method="post">

                        <?= csrf_field() ?>

                        <!-- NAMA -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">Nama Lengkap</label>
                            <input type="text"
                                name="name"
                                class="form-control rounded-pill px-4"
                                value="<?= esc($user['name']) ?>"
                                required>
                        </div>

                        <!-- EMAIL -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">Email</label>
                            <input type="email"
                                name="email"
                                class="form-control rounded-pill px-4"
                                value="<?= esc($user['email']) ?>"
                                required>
                        </div>
                        <!-- NO HP -->
                        <div class="form-group mb-3">
                            <label class="font-weight-semibold">No HP</label>
                            <input type="text"
                                name="phone"
                                class="form-control rounded-pill px-4"
                                value="<?= esc($user['phone'] ?? '') ?>">
                        </div>

                        <?php if ($kategori === 'siswa'): ?>
                            <!-- PROGRAM -->
                            <div class="form-group mb-3">
                                <label>Program Tujuan</label>
                                <select name="program" id="program" class="form-control select-paket" required>
                                    <option value="">Pilih Program</option>
                                    <option value="tni" <?= $userPaket['program'] === 'tni' ? 'selected' : '' ?>>TNI</option>
                                    <option value="polri" <?= $userPaket['program'] === 'polri' ? 'selected' : '' ?>>POLRI</option>
                                    <option value="kedinasan" <?= $userPaket['program'] === 'kedinasan' ? 'selected' : '' ?>>Kedinasan</option>
                                </select>
                            </div>

                            <!-- PAKET -->
                            <div class="form-group mb-3" id="paket-wrapper">
                                <label class="font-weight-semibold">Paket</label>
                                <select name="paket_id" id="paket"
                                    class="form-control rounded-pill px-4"
                                    required>
                                    <option value="">-- Pilih Paket --</option>
                                    <?php foreach ($paket as $p): ?>
                                        <option value="<?= $p['id'] ?>"
                                            <?= isset($userPaket) && $userPaket['paket_id'] == $p['id'] ? 'selected' : '' ?>>
                                            <?= esc($p['nama']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <!-- BUTTON -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="<?= base_url('master-data/' . $kategori) ?>"
                                class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fas fa-arrow-left mr-1"></i> Kembali
                            </a>

                            <button type="submit"
                                id="btn-submit"
                                class="btn btn-success rounded-pill px-5">
                                <i class="fas fa-save mr-1"></i> Simpan Perubahan
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
    .user-form-modern {
        border-radius: 20px;
        box-shadow: 0 10px 28px rgba(0, 0, 0, .08);
    }

    .font-weight-semibold {
        font-weight: 600;
    }

    .form-control {
        height: 46px;
    }
</style>

<!-- ================= SCRIPT ================= -->
<script>
    document.querySelector('form').addEventListener('submit', function() {
        const btn = document.getElementById('btn-submit');
        btn.disabled = true;
        btn.innerHTML = `
            <span class="spinner-border spinner-border-sm mr-2"></span>
            Menyimpan...
        `;
    });

    // Program -> Paket logic
    const programSelect = document.getElementById('program');
    const paketWrapper = document.getElementById('paket-wrapper');
    const paketSelect = document.getElementById('paket');

    programSelect.addEventListener('change', function() {
        const program = this.value;
        console.log(program);
        if (!program) {
            paketWrapper.style.display = 'none';
            paketSelect.value = '';
            return;
        }

        paketWrapper.style.display = 'block';
    });
</script>

<?= $this->endSection(); ?>