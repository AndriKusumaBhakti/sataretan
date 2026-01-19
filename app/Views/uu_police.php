<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>


<div class="container-fluid">

    <!-- ===== HEADER ===== -->
    <div class="materi-header-binjas mb-4">
        <div>
            <h2 class="materi-title-main">
                UU Kepolisian
            </h2>
        </div>
    </div>

    <!-- ===== VIEWER ===== -->
    <div class="card materi-viewer-binjas border-0 shadow-sm mb-4">
        <div class="card-body p-0">
            <iframe
                src="<?= esc(base_url('file/uu/2026_16_01_17_32_uu_kepolsian.pdf')) ?>#toolbar=0&navpanes=0&scrollbar=0"
                class="materi-iframe"
                oncontextmenu="return false;">
            </iframe>
        </div>
    </div>
</div>

<!-- ================= STYLE ================= -->
<style>
    /* ===== HEADER ===== */
    .materi-header-binjas {
        padding-left: 18px;
        border-left: 5px solid #28a745;
    }

    .materi-title-main {
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
    .materi-viewer-binjas {
        border-radius: 18px;
        overflow: hidden;
    }

    /* ===== VIDEO ===== */
    .materi-video {
        width: 100%;
        max-height: 75vh;
        background: #000;
        outline: none;
    }

    /* ===== IFRAME ===== */
    .materi-iframe {
        width: 100%;
        height: 75vh;
        border: none;
    }

    /* ===== RESPONSIVE ===== */
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