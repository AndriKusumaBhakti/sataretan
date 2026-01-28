<nav class="navbar navbar-expand navbar-light topbar-binjas shadow-sm">

    <button id="sidebarToggleTop" class="btn btn-light d-md-none rounded-circle mr-3">
        <i class="fas fa-bars"></i>
    </button>

    <form class="d-none d-md-flex form-inline mr-auto ml-3">
        <div class="input-group search-binjas">
            <input type="text" class="form-control" placeholder="Cari materi...">
            <div class="input-group-append">
                <button class="btn btn-search" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </form>

    <ul class="navbar-nav ml-auto align-items-center">

        <?php if (session('success')): ?>
            <div class="alert alert-success alert-dismissible fade show auto-close">
                <?= esc(session('success')) ?>
            </div>
        <?php endif ?>


        <!-- ALERT -->
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger auto-close show">
                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                    <div><?= esc($err) ?></div>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-toggle="dropdown">
                <span class="mr-2 d-none d-lg-inline user-name">
                    <?= esc(session('name')) ?>
                </span>
                <img class="img-profile rounded-circle"
                    src="<?= base_url('assets/ui/img/undraw_profile.svg') ?>">
            </a>

            <div class="dropdown-menu dropdown-menu-right shadow-sm">
                <a class="dropdown-item" href="<?= base_url('profile') ?>">
                    <i class="fas fa-user mr-2"></i> Profile
                </a>
                <a class="dropdown-item" href="<?= base_url('account-settings') ?>">
                    <i class="fas fa-cog mr-2"></i> Pengaturan Akun
                </a>
                <a class="dropdown-item" href="<?= base_url('help') ?>">
                    <i class="fas fa-question-circle mr-2"></i> Bantuan
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="<?= base_url('logout') ?>">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            </div>
        </li>

    </ul>
</nav>

<style>
    /* CONTENT OFFSET AGAR TIDAK NIMPA SIDEBAR */
    #content-wrapper {
        margin-left: 240px;
        min-height: 100vh;
        transition: margin-left .3s ease;
    }

    /* MOBILE */
    @media (max-width: 768px) {
        #content-wrapper {
            margin-left: 0;
        }
    }

    /* MAIN CONTENT SCROLL */
    #main-content {
        padding: 24px;
        min-height: calc(100vh - 70px);
    }
</style>

<script>
    setTimeout(() => {
        document.querySelectorAll('.auto-close').forEach(el => {
            el.style.opacity = 0;
            setTimeout(() => el.remove(), 500);
        });
    }, 4000);
</script>