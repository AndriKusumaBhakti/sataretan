<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<?php
$hasSub = !empty($subMateri);
$isFile = ($materi['sumber'] === 'file');

$sourceUrl = $isFile
    ? base_url('file/materi/' . $materi['file'])
    : $materi['link'];
?>

<div class="container-fluid px-2 px-md-4">

    <!-- ================= HEADER ================= -->
    <div class="materi-header-binjas mb-4">
        <div class="header-inner">
            <div>
                <h2 class="materi-title-main"><?= esc($materi['judul']) ?></h2>
                <span class="badge badge-soft-success"><?= esc($materi['tipe']) ?></span>
            </div>
        </div>
    </div>

    <div class="row g-3">

        <!-- ================= SIDEBAR SUB MATERI ================= -->
        <?php if ($hasSub): ?>
            <div class="col-lg-3">
                <div class="card shadow-sm sub-sidebar">
                    <div class="card-header">
                        <strong>📚 Sub Materi</strong>
                    </div>
                    <div class="list-group list-group-flush">
                        <?php foreach ($subMateri as $i => $sub):
                            $url = $materi['sumber'] === 'file'
                                ? base_url('file-sub/materi/' . $sub['file'])
                                : $sub['link'];
                        ?>
                            <div class="list-group-item sub-item"
                                data-index="<?= $i ?>"
                                data-url="<?= esc($url) ?>">
                                <span class="sub-number"><?= $i + 1 ?></span>
                                <span class="sub-text"><?= esc($sub['sub_judul']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- ================= VIEWER ================= -->
        <div class="<?= $hasSub ? 'col-lg-9' : 'col-12' ?>">
            <div class="card shadow-sm materi-viewer-binjas">

                <?php if ($hasSub): ?>
                    <div class="viewer-nav">
                        <button id="btnPrev" disabled>← Sebelumnya</button>
                        <span id="subTitle">Pilih Materi</span>
                        <button id="btnNext">Berikutnya →</button>
                    </div>
                <?php endif; ?>

                <div class="card-body p-0 materi-protect">

                    <?php if ($materi['tipe'] === 'video'): ?>

                        <video src="<?= esc($sourceUrl) ?>"
                            controls
                            controlsList="nodownload noplaybackrate"
                            disablePictureInPicture
                            class="materi-video"></video>

                    <?php else: ?>

                        <div id="pdfViewer"
                            data-source="<?= !$hasSub ? esc($sourceUrl) : '' ?>">
                        </div>

                    <?php endif; ?>

                </div>
            </div>
        </div>

    </div>

    <!-- BACK -->
    <div class="mt-4">
        <a href="<?= site_url('materi/' . $kategori) ?>" class="btn btn-outline-success rounded-pill px-4">
            ← Kembali ke Materi
        </a>
    </div>

</div>

<!-- ================= STYLE ================= -->
<style>
    /* ================= HEADER ================= */
    .materi-header-binjas {
        background: linear-gradient(135deg, #28a745, #1cc88a);
        color: #fff;
        border-radius: 18px;
        padding: 22px 24px;
    }

    .materi-title-main {
        font-weight: 800;
        font-size: 1.7rem;
        margin-bottom: 6px;
    }

    .badge-soft-success {
        background: rgba(255, 255, 255, .2);
        color: #fff;
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 13px;
    }

    /* ================= VIEWER CARD ================= */
    .materi-viewer-binjas {
        border-radius: 18px;
        overflow: hidden;
        background: #fff;
    }

    /* ================= NAV ================= */
    .viewer-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 18px;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        font-size: 14px;
    }

    .viewer-nav button {
        border: none;
        background: #e9ecef;
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 13px;
    }

    .viewer-nav button:disabled {
        opacity: .4;
    }

    /* ================= PDF VIEWER ================= */
    #pdfViewer {
        width: 100%;
        height: 75vh;
        overflow-y: auto;
        background: linear-gradient(#f1f3f5, #e9ecef);
        padding: 28px 0;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* PDF PAGE LOOK */
    canvas {
        width: 100% !important;
        max-width: 720px;
        height: auto !important;
        margin-bottom: 28px;
        background: #fff;
        border-radius: 10px;
        box-shadow:
            0 10px 30px rgba(0, 0, 0, .12),
            0 2px 8px rgba(0, 0, 0, .08);
    }

    /* ================= VIDEO ================= */
    .materi-video {
        width: 100%;
        height: 75vh;
        border-radius: 14px;
    }

    /* ================= MOBILE ================= */
    @media (max-width: 768px) {
        .materi-title-main {
            font-size: 1.3rem;
        }

        #pdfViewer,
        .materi-video {
            height: 55vh;
            padding: 18px 0;
        }

        canvas {
            max-width: 94%;
            margin-bottom: 20px;
        }
    }
</style>
<script>
    const subItems = document.querySelectorAll('.sub-item');
    const pdfViewer = document.getElementById('pdfViewer');
    const btnPrev = document.getElementById('btnPrev');
    const btnNext = document.getElementById('btnNext');
    const subTitle = document.getElementById('subTitle');

    let currentIndex = 0;

    function renderPDF(url) {
        if (!url) return;
        pdfViewer.innerHTML = '';

        pdfjsLib.getDocument(url).promise.then(pdf => {
            const containerWidth = pdfViewer.clientWidth;

            for (let i = 1; i <= pdf.numPages; i++) {
                pdf.getPage(i).then(page => {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');

                    const viewport = page.getViewport({
                        scale: 1
                    });
                    const scale = Math.min(
                        containerWidth / viewport.width,
                        window.innerWidth < 768 ? 1.15 : 1.4
                    );

                    const scaledViewport = page.getViewport({
                        scale
                    });

                    canvas.width = scaledViewport.width;
                    canvas.height = scaledViewport.height;

                    page.render({
                        canvasContext: ctx,
                        viewport: scaledViewport
                    });

                    pdfViewer.appendChild(canvas);
                });
            }
        });
    }

    function loadSub(index) {
        const item = subItems[index];
        if (!item) return;

        subItems.forEach(el => el.classList.remove('active'));
        item.classList.add('active');

        renderPDF(item.dataset.url);
        subTitle.innerText = item.querySelector('.sub-text').innerText;

        btnPrev.disabled = index === 0;
        btnNext.disabled = index === subItems.length - 1;
        currentIndex = index;
    }

    subItems.forEach(item =>
        item.addEventListener('click', () => loadSub(+item.dataset.index))
    );

    btnPrev?.addEventListener('click', () => loadSub(currentIndex - 1));
    btnNext?.addEventListener('click', () => loadSub(currentIndex + 1));

    if (subItems.length > 0) {
        loadSub(0);
    } else if (pdfViewer?.dataset.source) {
        renderPDF(pdfViewer.dataset.source);
    }

    /* PROTEKSI */
    document.addEventListener('contextmenu', e => e.preventDefault());
    document.addEventListener('keydown', e => {
        if (
            (e.ctrlKey && ['p', 's', 'u'].includes(e.key.toLowerCase())) ||
            e.key === 'PrintScreen'
        ) {
            e.preventDefault();
            alert('Aksi ini tidak diizinkan');
        }
    });
</script>

<?= $this->endSection(); ?>