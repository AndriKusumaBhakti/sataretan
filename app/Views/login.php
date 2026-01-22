<?= $this->extend('default/layout-template-auth'); ?>
<?= $this->section('content'); ?>

<div class="login-card">

    <!-- HEADER -->
    <div class="login-header text-center mb-4">
        <div class="logo-circle mb-3">
            <img src="<?= base_url('file/logo/logo1.png') ?>" alt="Logo">
        </div>
        <h3>SATARETAN AKADEMI</h3>
        <p>Portal Login Peserta & Alumni</p>
    </div>

    <!-- ALERT -->
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger auto-close show">
            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                <div><?= esc($err) ?></div>
            <?php endforeach ?>
        </div>
    <?php endif ?>

    <!-- FORM -->
    <form method="post" action="<?= base_url('login') ?>">

        <div class="form-group mb-3">
            <label>Email</label>
            <input type="email"
                   name="email"
                   class="form-control"
                   placeholder="contoh@email.com"
                   required>
        </div>

        <div class="form-group mb-3">
            <label>Password</label>
            <input type="password"
                   name="password"
                   class="form-control"
                   placeholder="••••••••"
                   required>
        </div>

        <button type="submit" class="btn btn-primary btn-block mb-3">
            Masuk
        </button>

        <div class="divider">atau</div>

        <a href="<?= base_url('auth/google') ?>" class="btn google-btn btn-block mb-3">
            <img src="https://www.svgrepo.com/show/475656/google-color.svg" width="18" class="mr-2">
            Masuk dengan Google
        </a>
    </form>

    <!-- LINKS -->
    <div class="login-links text-center mt-3">
        <a href="<?= base_url('forgot-password') ?>">Lupa Password?</a>
        <span class="mx-1">·</span>
        <a href="<?= base_url('register') ?>">Daftar Akun</a>
    </div>

</div>

<!-- STYLE KHUSUS LOGIN -->
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

/* AURA MERAH */
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
}

.btn-primary:hover {
    background: linear-gradient(135deg, #ef4444, #991b1b);
}

.google-btn {
    border-radius: 12px;
    padding: 12px;
    background: transparent;
    border: 1px solid #444;
    color: #fff;
}

.google-btn:hover {
    background: #1a1a1a;
}

.divider {
    text-align: center;
    margin: 18px 0;
    color: #777;
    position: relative;
    font-size: 13px;
}

.divider::before,
.divider::after {
    content: '';
    position: absolute;
    top: 50%;
    width: 40%;
    height: 1px;
    background: #333;
}

.divider::before { left: 0; }
.divider::after  { right: 0; }

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
