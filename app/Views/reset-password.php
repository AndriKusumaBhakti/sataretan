<?= $this->extend('default/layout-template-auth'); ?>
<?= $this->section('content'); ?>

<div class="login-card">

    <!-- HEADER -->
    <div class="login-header text-center mb-4">
        <div class="logo-circle mb-3">
            <img src="<?= base_url('file/logo/logo1.png') ?>" alt="Logo">
        </div>
        <h3>RESET PASSWORD</h3>
        <p>Masukkan password baru untuk akun Anda</p>
    </div>

    <!-- ALERT SUCCESS -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success auto-close show text-center">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <!-- ALERT ERROR -->
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger auto-close show">
            <?php if ($errors = session()->getFlashdata('errors')): ?>
                <?php
                if (is_array($errors) || is_object($errors)) {
                    foreach ($errors as $err) {
                        echo '<div>' . esc($err) . '</div>';
                    }
                } else {
                    echo '<div>' . esc($errors) . '</div>';
                }
                ?>
            <?php endif ?>
        </div>
    <?php endif ?>

    <!-- FORM -->
    <form method="post" action="<?= base_url('reset-password') ?>">

        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= esc($token) ?>">

        <div class="form-group mb-3">
            <label>Password Baru</label>
            <input type="password"
                name="password"
                class="form-control"
                placeholder="Minimal 6 karakter"
                required autofocus>
        </div>

        <div class="form-group mb-3">
            <label>Konfirmasi Password</label>
            <input type="password"
                name="password_confirm"
                class="form-control"
                placeholder="Ulangi password"
                required>
        </div>

        <button type="submit" class="btn btn-primary btn-block mb-3">
            Reset Password
        </button>

    </form>

    <!-- LINKS -->
    <div class="login-links text-center">
        <a href="<?= base_url('/login') ?>">← Kembali ke Login</a>
    </div>

</div>

<!-- STYLE (SAMA DENGAN LOGIN & FORGOT PASSWORD) -->
<style>
    .login-card {
        width: 100%;
        max-width: 420px;
        background: linear-gradient(180deg, #0f0f0f, #080808);
        padding: 32px;
        border-radius: 20px;
        border: 1px solid rgba(127, 29, 29, 0.8);
        box-shadow: 0 30px 80px rgba(0, 0, 0, .8);
        color: #fff;
        position: relative;
    }

    .login-card::before {
        content: "";
        position: absolute;
        inset: -45px;
        background: radial-gradient(circle, rgba(127, 29, 29, 0.25), transparent 70%);
        z-index: -1;
        border-radius: 35px;
    }

    .login-header h3 {
        font-weight: 800;
        letter-spacing: 1px;
    }

    .login-header p {
        font-size: 14px;
        color: #aaa;
    }

    .logo-circle {
        width: 82px;
        height: 82px;
        border-radius: 50%;
        background: radial-gradient(circle, #dc2626, #7f1d1d);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: auto;
        box-shadow: 0 0 30px rgba(220, 38, 38, 0.6);
    }

    .logo-circle img {
        width: 80px;
    }

    label {
        font-size: 13px;
        color: #ccc;
    }

    .form-control {
        background: #111;
        border: 1px solid #333;
        border-radius: 12px;
        color: #fff;
        padding: 12px;
    }

    .form-control:focus {
        background: #111;
        border-color: #dc2626;
        box-shadow: none;
        color: #fff;
    }

    .btn-primary {
        background: linear-gradient(135deg, #dc2626, #7f1d1d);
        border: none;
        border-radius: 12px;
        font-weight: 700;
        padding: 12px;
        color: #fff;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #ef4444, #991b1b);
    }

    .login-links a {
        color: #fca5a5;
        font-size: 14px;
        text-decoration: none;
    }

    .login-links a:hover {
        text-decoration: underline;
    }

    .alert {
        border-radius: 12px;
        font-size: 14px;
    }

    .alert.auto-close {
        transition: opacity .5s ease, transform .5s ease;
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

<?= $this->endSection(); ?>