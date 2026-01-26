<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Sataretan Akademi</title>
    <link rel="icon" type="image/png" href="<?= base_url('file/logo/logo1.png') ?>" />

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="<?= base_url('assets/ui/css/custom.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/ui/css/app.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/ui/css/sidebar-binjas.css') ?>" rel="stylesheet">
    <link href="<?= base_url('assets/ui/css/topbar-binjas.css') ?>" rel="stylesheet">

    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap4.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        pdfjsLib.GlobalWorkerOptions.workerSrc =
            "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js";
    </script>
</head>

<body id="page-top">

    <div id="wrapper">

        <!-- SIDEBAR (FIXED) -->
        <?= $this->include('default/layout-sidebar'); ?>

        <!-- CONTENT WRAPPER -->
        <div id="content-wrapper">

            <!-- TOPBAR -->
            <?= $this->include('default/layout-topbar'); ?>

            <!-- MAIN CONTENT -->
            <main id="main-content">
                <?= $this->renderSection('content'); ?>
            </main>

            <!-- FOOTER -->
            <?php if (is_file(APPPATH . 'Views/default/layout-footer.php')): ?>
                <?= $this->include('default/layout-footer'); ?>
            <?php endif; ?>

        </div>

    </div>

    <!-- Scroll to Top -->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap4.min.js"></script>

    <script src="<?= base_url('assets/ui/js/custom.js') ?>"></script>

</body>

</html>