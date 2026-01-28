<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="h4 font-weight-bold text-gray-800 mb-0">
                Pengaturan Akun
            </h1>
            <small class="text-muted">
                Kelola keamanan dan preferensi akun Anda
            </small>
        </div>
    </div>

    <div class="row">

        <!-- AKUN CARD -->
        <div class="col-lg-4 mb-4">
            <div class="profile-card h-100 text-center">

                <?php
                $photoUrl = !empty($user) && !empty($user['photo'])
                    ? base_url('file/profile/' . $user['photo'])
                    : base_url('assets/ui/img/undraw_profile.svg');
                ?>

                <img src="<?= $photoUrl ?>"
                    class="profile-avatar mb-3"
                    alt="Profile Photo">

                <h5 class="font-weight-bold mb-1">
                    <?= esc($user['name']) ?>
                </h5>

                <p class="text-muted mb-0">
                    <?= esc($user['email']) ?>
                </p>

            </div>
        </div>

        <!-- SETTINGS CARD -->
        <div class="col-lg-8 mb-4">
            <div class="profile-card h-100">

                <!-- INFORMASI AKUN -->
                <h6 class="font-weight-bold text-success mb-4">
                    <i class="fas fa-user mr-1"></i>
                    Informasi Akun
                </h6>

                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Nama Lengkap</div>
                    <div class="col-md-8 font-weight-bold">
                        <?= esc($user['name']) ?>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4 text-muted">Email</div>
                    <div class="col-md-8 font-weight-bold">
                        <?= esc($user['email']) ?>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4 text-muted">No HP</div>
                    <div class="col-md-8 font-weight-bold">
                        <?= esc($user['phone']) ?>
                    </div>
                </div>

                <hr class="my-4">

                <!-- KEAMANAN -->
                <h6 class="font-weight-bold text-success mb-3">
                    <i class="fas fa-lock mr-1"></i>
                    Keamanan Akun
                </h6>

                <form action="<?= base_url('change-password') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="text-muted">Password Lama</label>
                            <input type="password" name="old_password"
                                class="form-control" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="text-muted">Password Baru</label>
                            <input type="password" name="new_password"
                                class="form-control" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="text-muted">Konfirmasi Password</label>
                            <input type="password" name="confirm_password"
                                class="form-control" required>
                        </div>
                    </div>

                    <button class="btn btn-outline-success rounded-pill px-4">
                        <i class="fas fa-save mr-1"></i>
                        Simpan Password
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>

<!-- ================= STYLE ================= -->
<style>
    .profile-card {
        height: 100%;
        padding: 24px;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
        transition: .3s ease;
    }

    .profile-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 36px rgba(0, 0, 0, .15);
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #e9f7ef;
        background: #f8f9fa;
    }

    .btn-outline-success {
        font-weight: 600;
        border-width: 2px;
    }
</style>
<?= $this->endSection(); ?>