<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Sataretan Akademi</title>
    <link rel="icon" type="image/png" href="<?= base_url('file/logo/logo1.png') ?>">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <!-- Bootstrap 4 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- AUTH GLOBAL STYLE -->
    <style>
        html, body {
            height: 100%;
        }

        body {
            margin: 0;
            background: radial-gradient(circle at top, #7f1d1d, #000);
            font-family: "Segoe UI", sans-serif;
            overflow: hidden;
        }

        /* WRAPPER */
        .auth-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* CONTENT */
        .auth-content {
            flex: 1;
            overflow-y: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }

        /* FOOTER */
        .auth-footer {
            height: 50px;
            background: #0b0b0b;
            border-top: 1px solid #222;
            color: #777;
            font-size: 13px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* CARD */
        .login-card {
            width: 100%;
            max-width: 440px;
            background: #0f0f0f;
            padding: 32px;
            border-radius: 18px;
            border: 1px solid #7f1d1d;
            box-shadow: 0 25px 60px rgba(0, 0, 0, .7);
            color: #fff;
        }

        /* HEADER */
        .login-header h3 {
            font-weight: 800;
            letter-spacing: 1px;
        }

        .login-header p {
            font-size: 14px;
            color: #aaa;
        }

        /* LOGO */
        .logo-circle {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: radial-gradient(circle, #facc15, #b45309);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: auto;
        }

        .logo-circle img {
            width: 48px;
        }

        /* FORM */
        label {
            font-size: 13px;
            color: #ccc;
        }

        .form-control,
        .select-paket {
            background: #111;
            border: 1px solid #333;
            border-radius: 12px;
            color: #fff;
            padding: 12px;
            height: 48px;
        }

        .form-control:focus,
        .select-paket:focus {
            border-color: #facc15;
            box-shadow: none;
            background: #111;
            color: #fff;
        }

        /* SELECT */
        .select-paket {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3E%3Cpath fill='%23facc15' d='M5.5 7l4.5 5 4.5-5z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 14px;
            cursor: pointer;
        }

        /* BUTTON */
        .btn-primary {
            background: linear-gradient(135deg, #facc15, #ca8a04);
            border: none;
            border-radius: 12px;
            font-weight: 700;
            color: #000;
            padding: 12px;
        }

        /* LINKS */
        .login-links a {
            color: #facc15;
            text-decoration: none;
            font-size: 14px;
        }

        .login-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

<div class="auth-wrapper">

    <div class="auth-content">
        <?= $this->renderSection('content'); ?>
    </div>

    <div class="auth-footer">
        © <?= date('Y') ?> SATARETAN Akademi · All Rights Reserved
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    setTimeout(() => {
        document.querySelectorAll('.auto-close').forEach(el => {
            el.style.opacity = 0;
            setTimeout(() => el.remove(), 500);
        });
    }, 4000);
</script>

</body>
</html>
