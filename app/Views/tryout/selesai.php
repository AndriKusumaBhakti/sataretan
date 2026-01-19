<?= $this->extend('default/layout-template'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid text-center">
    <div class="card shadow-sm p-4">
        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
        <h4>Try Out Selesai</h4>
        <p>Jawaban kamu berhasil disimpan</p>

        <a href="<?= site_url('tryout/hasil/'.$id) ?>" class="btn btn-primary">
            Lihat Hasil
        </a>
    </div>
</div>

<?= $this->endSection(); ?>
