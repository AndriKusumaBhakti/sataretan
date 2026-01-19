<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<?php
$isFile = ($video['sumber'] === 'file');
$sourceUrl = $isFile
    ? base_url('file/video/' . $video['file'])
    : $video['link'];
?>

<div class="container-fluid">

    <!-- ===== HEADER ===== -->
    <div class="video-header-binjas mb-4">
        <div>
            <h2 class="video-title-main">
                <?= esc($video['judul']) ?>
            </h2>
            <span class="badge badge-soft-success text-uppercase">
                <?= esc($video['tipe']) ?>
            </span>
        </div>
    </div>

    <!-- ===== VIEWER ===== -->
    <div class="card video-viewer-binjas border-0 shadow-sm mb-4">
        <div class="card-body p-0">

            <?php if ($video['tipe'] === 'video'): ?>

                <?php if (!$isFile && str_contains($sourceUrl, 'youtube')): ?>
                    <!-- YouTube -->
                    <iframe
                        src="<?= esc($sourceUrl) ?>"
                        class="video-iframe"
                        allowfullscreen>
                    </iframe>

                <?php else: ?>
                    <!-- Video lokal / link -->
                    <video
                        src="<?= esc($sourceUrl) ?>"
                        controls
                        class="video-video">
                    </video>
                <?php endif; ?>

            <?php elseif ($video['tipe'] === 'pdf'): ?>

                <iframe
                    src="<?= esc($sourceUrl) ?>"
                    class="video-iframe"
                    frameborder="0">
                </iframe>

            <?php elseif ($video['tipe'] === 'doc' || $video['tipe'] === 'word'): ?>

                <iframe
                    src="https://view.officeapps.live.com/op/embed.aspx?src=<?= urlencode($sourceUrl) ?>"
                    class="video-iframe"
                    frameborder="0">
                </iframe>

            <?php else: ?>
                <div class="p-5 text-center text-muted">
                    <i class="fas fa-exclamation-circle fa-2x mb-3"></i>
                    <div>Format video tidak didukung</div>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- ===== ACTION ===== -->
    <div class="d-flex justify-content-start mb-4">
        <a href="<?= site_url('video/' . $kategori) ?>"
            class="btn btn-outline-success rounded-pill px-4">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Video
        </a>
    </div>

</div>

<!-- ================= STYLE ================= -->
<style>
    /* ===== HEADER ===== */
    .video-header-binjas {
        padding-left: 18px;
        border-left: 5px solid #28a745;
    }

    .video-title-main {
        font-weight: 800;
        color: #2e2e2e;
        margin-bottom: 6px;
    }

    /* ===== BADGE ===== */
    .badge-soft-success {
        background: rgba(40, 167, 69, .12);
        color: #28a745;
        font-weight: 600;
        padding: 6px 12px;
    }

    /* ===== VIEWER CARD ===== */
    .video-viewer-binjas {
        border-radius: 18px;
        overflow: hidden;
    }

    /* ===== VIDEO ===== */
    .video-video {
        width: 100%;
        max-height: 75vh;
        background: #000;
        outline: none;
    }

    /* ===== IFRAME ===== */
    .video-iframe {
        width: 100%;
        height: 75vh;
        border: none;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {

        .video-video,
        .video-iframe {
            height: 60vh;
        }
    }
</style>

<?= $this->endSection(); ?>