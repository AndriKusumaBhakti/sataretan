<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="h4 font-weight-bold text-gray-800 mb-0">
                Profile Saya
            </h1>
            <small class="text-muted">
                Informasi akun dan pengaturan pengguna
            </small>
        </div>
    </div>

    <div class="row">

        <!-- PROFILE CARD -->
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

        <!-- INFO CARD -->
        <div class="col-lg-8 mb-4">
            <div class="profile-card h-100">

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

                <div class="row mb-3">
                    <div class="col-md-4 text-muted">Email</div>
                    <div class="col-md-8 font-weight-bold">
                        <?= esc($user['email']) ?>
                    </div>
                </div>

                <!-- ACTION -->
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?= base_url('profile/edit') ?>"
                        class="btn btn-outline-success rounded-pill px-4">
                        <i class="fas fa-user-edit mr-1"></i>
                        Edit Profile
                    </a>

                    <a href="<?= base_url('help') ?>"
                        class="btn btn-light rounded-pill px-4 ml-2">
                        <i class="fas fa-cog mr-1"></i>
                        Help
                    </a>
                </div>

            </div>
        </div>

    </div>

</div>

<!-- ================= STYLE ================= -->
<style>
    /* CARD BINJAS */
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

    /* AVATAR */
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #e9f7ef;
        background: #f8f9fa;
    }

    /* BUTTON */
    .btn-outline-success {
        font-weight: 600;
        border-width: 2px;
    }

    .btn-light {
        background: #f1f3f5;
        font-weight: 600;
    }
</style>

<?= $this->endSection(); ?>