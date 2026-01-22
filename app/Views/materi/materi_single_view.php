<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<?php
$hasSub = !empty($subMateri);
$isFile = ($materi['sumber'] === 'file');

$sourceUrl = null;
if (!$hasSub) {
    $sourceUrl = $isFile
        ? base_url('file/materi/' . $materi['file'])
        : $materi['link'];
}
?>

<div class="container-fluid px-2 px-md-4">

    <!-- ===== HEADER ===== -->
    <div class="materi-header-binjas mb-3">
        <div>
            <h2 class="materi-title-main"><?= esc($materi['judul']) ?></h2>
            <span class="badge badge-soft-success text-uppercase">
                <?= esc($materi['tipe']) ?>
            </span>
        </div>
    </div>

    <!-- ===== SUB MATERI ===== -->
    <?php if ($hasSub): ?>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">

                <h6 class="font-weight-bold mb-3">
                    <i class="fas fa-layer-group mr-2"></i>Sub Materi
                </h6>

                <div class="list-group list-group-flush" id="subList">
                    <?php foreach ($subMateri as $i => $sub): ?>
                        <?php
                        $subUrl = null;
                        if ($materi['sumber'] === 'file' && $sub['file']) {
                            $subUrl = base_url('file-sub/materi/' . $sub['file']);
                        }
                        if ($materi['sumber'] === 'link' && $sub['link']) {
                            $subUrl = $sub['link'];
                        }
                        ?>
                        <div class="list-group-item sub-item"
                            data-index="<?= $i ?>"
                            data-url="<?= esc($subUrl) ?>">
                            <strong><?= ($i + 1) ?>.</strong>
                            <?= esc($sub['sub_judul']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>

            </div>
        </div>
    <?php endif; ?>

    <!-- ===== VIEWER ===== -->
    <div class="card materi-viewer-binjas border-0 shadow-sm mb-3">

        <?php if ($hasSub): ?>
            <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <button id="btnPrev" class="btn btn-outline-secondary btn-sm rounded-pill px-3" disabled>
                    ← Sebelumnya
                </button>

                <span id="subTitle" class="sub-title-mobile"></span>

                <button id="btnNext" class="btn btn-outline-success btn-sm rounded-pill px-3">
                    Berikutnya →
                </button>
            </div>
        <?php endif; ?>

        <div class="card-body p-0 materi-protect">

            <?php if (!$hasSub && $sourceUrl): ?>

                <?php if ($materi['tipe'] === 'video'): ?>
                    <video
                        src="<?= esc($sourceUrl) ?>"
                        class="materi-video"
                        controls
                        controlsList="nodownload noplaybackrate"
                        disablePictureInPicture>
                    </video>
                <?php else: ?>
                    <iframe
                        src="<?= esc($sourceUrl) ?>#toolbar=0&navpanes=0"
                        class="materi-iframe">
                    </iframe>
                <?php endif; ?>

            <?php else: ?>
                <iframe
                    id="materiViewer"
                    class="materi-iframe"
                    src="">
                </iframe>
            <?php endif; ?>

        </div>
    </div>

    <!-- ===== ACTION ===== -->
    <div class="mb-4">
        <a href="<?= site_url('materi/' . $kategori) ?>"
            class="btn btn-outline-success rounded-pill px-4">
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Materi
        </a>
    </div>

</div>

<!-- ================= STYLE ================= -->
<style>
    .materi-header-binjas {
        padding-left: 14px;
        border-left: 5px solid #28a745;
    }

    .materi-title-main {
        font-weight: 800;
        color: #2e2e2e;
        font-size: 1.6rem;
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
        border-radius: 16px;
        overflow: hidden;
    }

    .materi-video,
    .materi-iframe {
        width: 100%;
        height: 75vh;
        border: none;
    }

    .sub-item {
        cursor: pointer;
        padding: 12px 14px;
        font-size: 14px;
    }

    .sub-item.active {
        background: #1cc88a;
        color: #fff;
        font-weight: 600;
    }

    .sub-title-mobile {
        font-weight: 600;
        font-size: 14px;
        color: #6c757d;
        max-width: 180px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-align: center;
    }

    /* ===== MOBILE ===== */
    @media (max-width: 768px) {
        .materi-title-main {
            font-size: 1.2rem;
        }

        .materi-video,
        .materi-iframe {
            height: 55vh;
        }

        .sub-title-mobile {
            max-width: 120px;
            font-size: 13px;
        }
    }

    /* ===== EXTRA SMALL ===== */
    @media (max-width: 480px) {

        .materi-video,
        .materi-iframe {
            height: 48vh;
        }
    }
</style>

<!-- ================= SCRIPT ================= -->
<script>
    const subItems = document.querySelectorAll('.sub-item');
    const viewer = document.getElementById('materiViewer');
    const btnPrev = document.getElementById('btnPrev');
    const btnNext = document.getElementById('btnNext');
    const subTitle = document.getElementById('subTitle');

    let currentIndex = 0;

    function loadSub(index) {
        const item = subItems[index];
        if (!item) return;

        subItems.forEach(el => el.classList.remove('active'));
        item.classList.add('active');

        viewer.src = item.dataset.url + '#toolbar=0&navpanes=0';
        subTitle.innerText = item.innerText;

        btnPrev.disabled = index === 0;
        btnNext.disabled = index === subItems.length - 1;

        currentIndex = index;
    }

    subItems.forEach(item => {
        item.addEventListener('click', () => {
            loadSub(parseInt(item.dataset.index));
        });
    });

    btnPrev?.addEventListener('click', () => {
        if (currentIndex > 0) loadSub(currentIndex - 1);
    });

    btnNext?.addEventListener('click', () => {
        if (currentIndex < subItems.length - 1) loadSub(currentIndex + 1);
    });

    if (subItems.length > 0) loadSub(0);

    document.addEventListener('contextmenu', e => e.preventDefault());
</script>

<?= $this->endSection(); ?>