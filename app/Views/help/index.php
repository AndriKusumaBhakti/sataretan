<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- HEADER -->
    <div class="mb-4">
        <h1 class="h4 font-weight-bold text-gray-800 mb-1">
            Help & Support
        </h1>
        <small class="text-muted">
            Pusat bantuan penggunaan aplikasi
        </small>
    </div>

    <div class="row">

        <!-- HELP CONTENT -->
        <div class="col-lg-7 col-md-12 mb-4">
            <div class="help-card">

                <h6 class="font-weight-bold text-primary mb-3">
                    <i class="fas fa-question-circle mr-1"></i>
                    Panduan Penggunaan
                </h6>

                <p class="text-muted mb-4">
                    Jika Anda mengalami kendala dalam penggunaan aplikasi,
                    silakan baca panduan berikut sebelum menghubungi admin.
                </p>

                <div class="help-item">
                    <i class="fas fa-clipboard-check"></i>
                    Cara menggunakan fitur Try Out
                </div>

                <div class="help-item">
                    <i class="fas fa-user-cog"></i>
                    Cara mengubah profil pengguna
                </div>

                <div class="help-item">
                    <i class="fas fa-sign-in-alt"></i>
                    Mengatasi masalah login
                </div>

                <div class="help-item">
                    <i class="fas fa-download"></i>
                    Mengakses dan mengunduh materi
                </div>

            </div>
        </div>

        <!-- CONTACT -->
        <div class="col-lg-5 col-md-12 mb-4">
            <div class="help-card">

                <h6 class="font-weight-bold text-success mb-3">
                    <i class="fas fa-headset mr-1"></i>
                    Hubungi Admin
                </h6>

                <p class="text-muted mb-4">
                    Masalah belum terselesaikan?
                    Hubungi admin melalui WhatsApp berikut:
                </p>

                <a href="https://wa.me/6285755088597"
                    target="_blank"
                    class="btn btn-success btn-block rounded-pill mb-3">
                    <i class="fab fa-whatsapp mr-2"></i>
                    +62 857-5508-8597
                </a>

                <a href="https://wa.me/6285706770538"
                    target="_blank"
                    class="btn btn-outline-success btn-block rounded-pill">
                    <i class="fab fa-whatsapp mr-2"></i>
                    +62 857-0677-0538
                </a>

            </div>
        </div>

    </div>

</div>

<!-- ================= STYLE ================= -->
<style>
    .help-card {
        background: #fff;
        border-radius: 18px;
        padding: 24px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
        transition: .3s ease;
        height: 100%;
    }

    /* Hover desktop only */
    @media (min-width: 768px) {
        .help-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 36px rgba(0, 0, 0, .15);
        }
    }

    .help-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px 16px;
        border-radius: 12px;
        background: #f8f9fa;
        margin-bottom: 12px;
        font-weight: 500;
        color: #555;
    }

    .help-item i {
        font-size: 18px;
        color: #1cc88a;
        min-width: 24px;
        text-align: center;
    }

    .btn-success,
    .btn-outline-success {
        padding: 12px;
    }

    @media (max-width: 576px) {
        .help-item {
            font-size: .9rem;
        }
    }
</style>

<?= $this->endSection(); ?>