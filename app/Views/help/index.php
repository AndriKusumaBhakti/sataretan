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

        <!-- HELP LIST -->
        <div class="col-lg-7 mb-4">
            <div class="card help-card border-0 shadow-sm">
                <div class="card-body p-4">

                    <h6 class="font-weight-bold text-primary mb-3">
                        <i class="fas fa-question-circle mr-1"></i>
                        Butuh Bantuan?
                    </h6>

                    <p class="text-muted mb-4">
                        Jika Anda mengalami kendala dalam penggunaan aplikasi,
                        silakan baca panduan berikut atau hubungi admin.
                    </p>

                    <ul class="help-list">
                        <li>
                            <i class="fas fa-check-circle"></i>
                            Cara menggunakan fitur Try Out
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            Cara mengubah profil pengguna
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            Mengatasi masalah login
                        </li>
                        <li>
                            <i class="fas fa-check-circle"></i>
                            Mengakses dan mengunduh materi
                        </li>
                    </ul>

                </div>
            </div>
        </div>

        <!-- CONTACT -->
        <div class="col-lg-5 mb-4">
            <div class="card help-card border-0 shadow-sm">
                <div class="card-body p-4">

                    <h6 class="font-weight-bold text-success mb-3">
                        <i class="fas fa-headset mr-1"></i>
                        Hubungi Admin
                    </h6>

                    <p class="text-muted mb-3">
                        Jika masalah belum terselesaikan,
                        Anda dapat menghubungi admin melalui:
                    </p>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-phone text-success mr-2"></i>
                            <a href="https://wa.me/6285755088597"
                                target="_blank"
                                class="inline-block bg-yellow-400 text-black px-4 py-2 rounded-lg font-bold hover:bg-yellow-300 transition">
                                +62 857-5508-8597
                            </a>
                            <a href="https://wa.me/6285706770538"
                                target="_blank"
                                class="inline-block bg-yellow-400 text-black px-4 py-2 rounded-lg font-bold hover:bg-yellow-300 transition">
                                +62 857-0677-0538
                            </a>

                        </li>
                        <li>
                            <i class="fab fa-whatsapp text-success mr-2"></i>
                            WhatsApp Admin
                        </li>
                    </ul>

                </div>
            </div>
        </div>

    </div>

</div>

<!-- ================= STYLE ================= -->
<style>
    .help-card {
        border-radius: 18px;
    }

    .help-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .help-list li {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 0;
        font-weight: 500;
        color: #555;
    }

    .help-list i {
        color: #1cc88a;
        font-size: 16px;
    }
</style>

<?= $this->endSection(); ?>