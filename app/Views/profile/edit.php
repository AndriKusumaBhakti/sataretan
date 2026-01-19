<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- HEADER -->
    <div class="mb-5">
        <h1 class="h4 font-weight-bold text-gray-800 mb-1">
            Edit Profile
        </h1>
        <small class="text-muted">
            Perbarui informasi akun dan foto profil
        </small>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6">

            <div class="profile-card">

                <form id="form-profile" action="<?= base_url('profile/update') ?>"
                    method="post"
                    enctype="multipart/form-data">

                    <?= csrf_field() ?>

                    <!-- PHOTO -->
                    <div class="text-center mb-4">
                        <?php
                        $photoUrl = !empty($user['photo'])
                            ? base_url('file/profile/' . $user['photo'])
                            : base_url('assets/ui/img/undraw_profile.svg');
                        ?>

                        <img src="<?= $photoUrl ?>"
                            class="profile-avatar mb-3">

                        <div class="custom-file">
                            <input type="file"
                                name="photo"
                                class="custom-file-input"
                                id="photo">
                            <label class="custom-file-label" for="photo">
                                Pilih foto baru
                            </label>
                        </div>

                        <small class="text-muted d-block mt-2">
                            JPG / PNG • Maks. 2MB
                        </small>
                    </div>

                    <!-- NAME -->
                    <div class="form-group mb-3">
                        <label class="font-weight-bold">Nama Lengkap</label>
                        <input type="text"
                            name="name"
                            class="form-control rounded-pill px-4"
                            value="<?= esc($user['name']) ?>"
                            required>
                    </div>

                    <!-- EMAIL -->
                    <div class="form-group mb-4">
                        <label class="font-weight-bold">Email</label>
                        <input type="email"
                            name="email"
                            class="form-control rounded-pill px-4"
                            value="<?= esc($user['email']) ?>"
                            required>
                    </div>

                    <!-- BUTTON -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="<?= base_url('profile') ?>"
                            class="btn btn-outline-success rounded-pill px-4">
                            <i class="fas fa-arrow-left mr-1"></i>
                            Kembali
                        </a>

                        <button id="btn-submit" class="btn btn-success rounded-pill px-5">
                            <i class="fas fa-save mr-1"></i>
                            Simpan Perubahan
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>

</div>

<!-- ================= STYLE ================= -->
<style>
    /* CARD – sama dengan Materi */
    .profile-card {
        background: #ffffff;
        padding: 28px;
        border-radius: 16px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
        transition: .3s ease;
    }

    .profile-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 36px rgba(0, 0, 0, .15);
    }

    /* AVATAR */
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #e9f7ef;
        background: #f8f9fa;
    }

    /* FORM */
    .form-control:focus {
        box-shadow: none;
        border-color: #28a745;
    }

    /* BUTTON */
    .btn-success {
        font-weight: 600;
    }

    .btn-outline-success {
        font-weight: 600;
    }
</style>

<!-- ================= SCRIPT ================= -->
<script>
    document.querySelector('.custom-file-input')?.addEventListener('change', function() {
        this.nextElementSibling.innerText =
            this.files[0]?.name || 'Pilih foto baru';
    });
    document.getElementById('form-profile').addEventListener('submit', function() {
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