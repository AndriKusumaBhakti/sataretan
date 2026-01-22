<?= $this->extend('default/layout-template-auth'); ?>
<?= $this->section('content'); ?>

<div class="login-card">

    <!-- HEADER -->
    <div class="login-header text-center mb-4">
        <div class="logo-circle mb-3">
            <img src="<?= base_url('file/logo/logo1.png') ?>" alt="Logo">
        </div>
        <h3>DAFTAR AKUN</h3>
        <p>Registrasi Peserta & Alumni</p>
    </div>

    <!-- ALERT ERROR -->
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger auto-close show">
            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                <div><?= esc($err) ?></div>
            <?php endforeach ?>
        </div>
    <?php endif ?>

    <!-- ALERT SUCCESS -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success auto-close show">
            <?= esc(session()->getFlashdata('success')) ?>
        </div>
    <?php endif ?>

    <!-- FORM -->
    <form method="post" action="<?= base_url('register') ?>">

        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text"
                name="name"
                class="form-control"
                placeholder="Nama lengkap"
                value="<?= old('name') ?>"
                required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email"
                name="email"
                class="form-control"
                placeholder="contoh@email.com"
                value="<?= old('email') ?>"
                required>
        </div>

        <div class="form-group">
            <label>No WhatsApp</label>
            <input type="text"
                name="phone"
                class="form-control"
                placeholder="628xxxxxxxxxx"
                value="<?= old('phone') ?>"
                required>
        </div>

        <div class="form-group">
            <label>Kategori</label>
            <select id="kategori" name="kategori" class="form-control select-paket">
                <option value="tni">TNI</option>
                <option value="polri">POLRI</option>
                <option value="kedinasan">Kedinasan</option>
            </select>
        </div>

        <div class="form-group">
            <label>Paket Belajar</label>
            <select name="paket_id" class="form-control select-paket" required>
                <option value="">Pilih Paket</option>
                <?php foreach ($paket as $pkg): ?>
                    <option value="<?= $pkg['id'] ?>"
                        <?= old('paket_id') == $pkg['id'] ? 'selected' : '' ?>>
                        <?= esc($pkg['nama']) ?> (<?= $pkg['range_month'] ?> Bulan)
                    </option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password"
                name="password"
                class="form-control"
                placeholder="••••••••"
                required>
        </div>

        <div class="form-group mb-4">
            <label>Konfirmasi Password</label>
            <input type="password"
                name="password_confirm"
                class="form-control"
                placeholder="••••••••"
                required>
        </div>

        <button type="submit" class="btn btn-primary btn-block mb-3">
            Daftar
        </button>

    </form>

    <!-- LINKS -->
    <div class="login-links text-center">
        <span>Sudah punya akun?</span>
        <a href="<?= base_url('login') ?>">Masuk</a>
    </div>

</div>

<!-- STYLE (KONSISTEN DENGAN LOGIN) -->
<style>
    .login-card {
        width: 100%;
        max-width: 460px;
        background: linear-gradient(180deg, #0f0f0f, #080808);
        padding: 34px;
        border-radius: 20px;
        border: 1px solid rgba(127, 29, 29, 0.8);
        box-shadow: 0 30px 80px rgba(0, 0, 0, .85);
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
        border-radius: 40px;
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
        width: 84px;
        height: 84px;
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

    /* SELECT PAKET */
    .select-paket {
        height: 48px;
        padding: 6px 42px 6px 14px;
        appearance: none;
        background-color: #111;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='%23dc2626' d='M5.5 7l4.5 5 4.5-5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        background-size: 14px;
        cursor: pointer;
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

    .login-links {
        font-size: 14px;
        color: #aaa;
    }

    .login-links a {
        color: #fca5a5;
        margin-left: 5px;
        text-decoration: none;
        font-weight: 600;
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