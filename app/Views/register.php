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
        <div class="alert alert-danger auto-close">
            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                <div><?= esc($err) ?></div>
            <?php endforeach ?>
        </div>
    <?php endif ?>

    <!-- FORM -->
    <form method="post" action="<?= base_url('register') ?>">

        <div class="form-group mb-3">
            <label>Nama Lengkap</label>
            <input type="text" name="name" class="form-control"
                value="<?= old('name') ?>" placeholder="Nama lengkap" required>
        </div>

        <div class="form-group mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control"
                value="<?= old('email') ?>" placeholder="contoh@email.com" required>
        </div>

        <div class="form-group mb-3">
            <label>No WhatsApp</label>
            <input type="text" name="phone" class="form-control"
                value="<?= old('phone') ?>" placeholder="628xxxxxxxxxx" required>
        </div>

        <!-- PROGRAM -->
        <div class="form-group mb-3">
            <label>Program Tujuan</label>
            <select name="program" id="program" class="form-control select-paket" required>
                <option value="">Pilih Program</option>
                <option value="tni">TNI</option>
                <option value="polri">POLRI</option>
                <option value="kedinasan">Kedinasan</option>
            </select>
        </div>

        <!-- PAKET (HIDDEN AWAL) -->
        <div class="form-group mb-3" id="paket-wrapper" style="display:none;">
            <label>Paket Belajar</label>
            <select name="paket_id" id="paket" class="form-control select-paket" required>
                <option value="">Pilih Paket</option>
                <?php foreach ($paket as $pkg): ?>
                    <option value="<?= $pkg['id'] ?>">
                        <?= esc($pkg['nama']) ?> (<?= $pkg['range_month'] ?> Bulan)
                    </option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="form-group mb-3">
            <label>Password</label>
            <input type="password" name="password"
                class="form-control" placeholder="••••••••" required>
        </div>

        <div class="form-group mb-4">
            <label>Konfirmasi Password</label>
            <input type="password" name="password_confirm"
                class="form-control" placeholder="••••••••" required>
        </div>

        <button class="btn btn-primary btn-block mb-3">
            Daftar
        </button>
    </form>

    <div class="login-links text-center">
        <span>Sudah punya akun?</span>
        <a href="<?= base_url('login') ?>">Masuk</a>
    </div>

</div>

<!-- STYLE -->
<style>
    .login-card {
        width: 100%;
        max-width: 460px;
        background: linear-gradient(180deg, #0f0f0f, #080808);
        padding: 34px;
        border-radius: 20px;
        border: 1px solid rgba(127, 29, 29, .8);
        box-shadow: 0 30px 80px rgba(0, 0, 0, .85);
        color: #fff;
        position: relative;
    }

    .login-card::before {
        content: "";
        position: absolute;
        inset: -45px;
        background: radial-gradient(circle, rgba(127, 29, 29, .25), transparent 70%);
        z-index: -1;
        border-radius: 40px;
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
        border-color: #dc2626;
        box-shadow: none;
    }

    .select-paket {
        height: 48px;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='%23dc2626' d='M5.5 7l4.5 5 4.5-5z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        background-size: 14px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #dc2626, #7f1d1d);
        border: none;
        border-radius: 12px;
        font-weight: 700;
    }

    .login-links {
        font-size: 14px;
        color: #aaa;
    }

    .login-links a {
        color: #fca5a5;
        margin-left: 6px;
        font-weight: 600;
        text-decoration: none;
    }
</style>

<!-- SCRIPT -->
<script>
    // Auto close alert
    setTimeout(() => {
        document.querySelectorAll('.auto-close').forEach(el => {
            el.style.opacity = 0;
            setTimeout(() => el.remove(), 500);
        });
    }, 4000);

    // Program -> Paket logic
    const programSelect = document.getElementById('program');
    const paketWrapper = document.getElementById('paket-wrapper');
    const paketSelect = document.getElementById('paket');

    programSelect.addEventListener('change', function() {
        const program = this.value;

        if (!program) {
            paketWrapper.style.display = 'none';
            paketSelect.value = '';
            return;
        }

        paketWrapper.style.display = 'block';
    });
</script>

<?= $this->endSection(); ?>