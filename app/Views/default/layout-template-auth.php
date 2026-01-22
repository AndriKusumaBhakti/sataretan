<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SATARETAN Akademi</title>
    <link rel="icon" href="<?= base_url('file/logo/logo1.png') ?>">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        html,
        body {
            height: 100%;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", sans-serif;
            background: radial-gradient(circle at top, #7f1d1d, #000);
            background-attachment: fixed;
        }

        /* MAIN WRAPPER */
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* CONTENT AUTO-DETECT */
        .auth-content {
            margin: auto 0;
            /* ⭐ kunci auto detect */
            padding: 60px 15px;
            display: flex;
            justify-content: center;
        }

        /* FOOTER */
        .auth-footer {
            margin-top: auto;
            /* ⭐ dorong footer ke bawah */
            background: rgba(0, 0, 0, .85);
            border-top: 1px solid #222;
            color: #777;
            font-size: 13px;
            text-align: center;
            padding: 15px 10px;
        }
    </style>
</head>

<body>

    <div class="auth-wrapper">

        <div class="auth-content">
            <?= $this->renderSection('content'); ?>
        </div>

        <footer class="auth-footer">
            © <?= date('Y') ?> SATARETAN Akademi · All Rights Reserved
        </footer>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>