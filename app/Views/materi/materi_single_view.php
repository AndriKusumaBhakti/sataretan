<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<?php
$isFile = ($materi['sumber'] === 'file');
$sourceUrl = $isFile
    ? base_url('file/materi/' . $materi['file'])
    : $materi['link'];
?>

<div class="container-fluid">

    <!-- ===== HEADER ===== -->
    <div class="materi-header-binjas mb-4">
        <div>
            <h2 class="materi-title-main">
                <?= esc($materi['judul']) ?>
            </h2>
            <span class="badge badge-soft-success text-uppercase">
                <?= esc($materi['tipe']) ?>
            </span>
        </div>
    </div>

    <!-- ===== VIEWER ===== -->
    <div class="card materi-viewer-binjas border-0 shadow-sm mb-4">
        <div class="card-body p-0 materi-protect">

            <?php if ($materi['tipe'] === 'video'): ?>

                <?php if (!$isFile && str_contains($sourceUrl, 'youtube')): ?>
                    <iframe
                        src="<?= esc($sourceUrl) ?>"
                        class="materi-iframe"
                        allowfullscreen
                        sandbox="allow-scripts allow-same-origin allow-presentation">
                    </iframe>
                <?php else: ?>
                    <video
                        src="<?= esc($sourceUrl) ?>"
                        class="materi-video"
                        controls
                        controlsList="nodownload noplaybackrate"
                        disablePictureInPicture
                        oncontextmenu="return false;">
                    </video>
                <?php endif; ?>

            <?php elseif ($materi['tipe'] === 'pdf'): ?>

                <!-- PDF: tampil saja -->
                <iframe
                    src="<?= esc($sourceUrl) ?>#toolbar=0&navpanes=0&scrollbar=0"
                    class="materi-iframe"
                    oncontextmenu="return false;">
                </iframe>

            <?php elseif (
                $materi['tipe'] === 'doc' ||
                $materi['tipe'] === 'docx' ||
                $materi['tipe'] === 'word'
            ): ?>

                <!-- WORD / DOC / DOCX (VIEW ONLY, NO DOWNLOAD) -->
                <iframe
                    src="https://view.officeapps.live.com/op/embed.aspx?src=<?= urlencode($sourceUrl) ?>"
                    class="materi-iframe"
                    frameborder="0"
                    oncontextmenu="return false;">
                </iframe>

            <?php else: ?>
                <div class="p-5 text-center text-muted">
                    <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                    <div>Format materi tidak didukung</div>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- ===== ACTION ===== -->
    <div class="d-flex justify-content-start mb-4">
        <a href="<?= site_url('materi/' . $kategori) ?>"
            class="btn btn-outline-success rounded-pill px-4">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Materi
        </a>
    </div>

</div>

<!-- ================= STYLE ================= -->
<style>
    .materi-header-binjas {
        padding-left: 18px;
        border-left: 5px solid #28a745;
    }

    .materi-title-main {
        font-weight: 800;
        color: #2e2e2e;
        margin-bottom: 6px;
    }

    .badge-soft-success {
        background: rgba(40, 167, 69, .12);
        color: #28a745;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 12px;
    }

    .materi-viewer-binjas {
        border-radius: 20px;
        overflow: hidden;
    }

    .materi-protect {
        user-select: none;
    }

    .materi-video {
        width: 100%;
        max-height: 75vh;
        background: #000;
    }

    .materi-iframe {
        width: 100%;
        height: 75vh;
        border: none;
        background: #fff;
    }

    @media (max-width: 768px) {

        .materi-video,
        .materi-iframe {
            height: 60vh;
        }
    }
</style>

<!-- ================= SCRIPT ================= -->
<script>
    document.addEventListener('contextmenu', e => e.preventDefault());

    document.addEventListener('keydown', function(e) {
        if (
            (e.ctrlKey && ['s', 'p', 'u'].includes(e.key.toLowerCase()))
        ) {
            e.preventDefault();
        }
    });
</script>

<?= $this->endSection(); ?>