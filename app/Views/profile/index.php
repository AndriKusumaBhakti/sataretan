<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
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
        <div class="col-lg-4 col-md-5 mb-4">
            <div class="profile-card text-center">

                <?php
                $photoUrl = !empty($user['photo'])
                    ? base_url('file/profile/' . $user['photo'])
                    : base_url('assets/ui/img/undraw_profile.svg');
                ?>

                <img src="<?= $photoUrl ?>"
                    class="profile-avatar mb-3"
                    alt="Profile Photo">

                <h5 class="font-weight-bold mb-1">
                    <?= esc($user['name']) ?>
                </h5>

                <p class="text-muted small mb-0">
                    <?= esc($user['email']) ?>
                </p>

            </div>
        </div>

        <!-- INFO CARD -->
        <div class="col-lg-8 col-md-7 mb-4">
            <div class="profile-card">

                <h6 class="font-weight-bold text-success mb-4">
                    <i class="fas fa-id-card mr-1"></i>
                    Informasi Akun
                </h6>

                <!-- ITEM -->
                <div class="info-item">
                    <span class="label">
                        <i class="fas fa-user mr-2"></i>Nama Lengkap
                    </span>
                    <span class="value">
                        <?= esc($user['name']) ?>
                    </span>
                </div>

                <div class="info-item">
                    <span class="label">
                        <i class="fas fa-envelope mr-2"></i>Email
                    </span>
                    <span class="value">
                        <?= esc($user['email']) ?>
                    </span>
                </div>

                <div class="info-item">
                    <span class="label">
                        <i class="fas fa-phone mr-2"></i>No HP
                    </span>
                    <span class="value">
                        <?= esc($user['phone'] ?? '-') ?>
                    </span>
                </div>

                <hr class="my-4">

                <!-- ACTION -->
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <a href="<?= base_url('profile/edit') ?>"
                            class="btn btn-outline-success btn-block rounded-pill">
                            <i class="fas fa-user-edit mr-1"></i>
                            Edit Profile
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>

<!-- ================= STYLE ================= -->
<style>
    .profile-card {
        padding: 24px;
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
        transition: .3s ease;
    }

    /* Disable hover effect on mobile */
    @media (min-width: 768px) {
        .profile-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 36px rgba(0, 0, 0, .15);
        }
    }

    .profile-avatar {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #e9f7ef;
        background: #f8f9fa;
    }

    /* Smaller avatar on mobile */
    @media (max-width: 576px) {
        .profile-avatar {
            width: 90px;
            height: 90px;
        }
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px dashed #eee;
        font-size: .95rem;
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-item .label {
        color: #6c757d;
    }

    .info-item .value {
        font-weight: 600;
        text-align: right;
    }

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