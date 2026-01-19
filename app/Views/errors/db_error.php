<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Database Error</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin-top: 50px; }
        .error-box { border: 1px solid #e74c3c; padding: 30px; display: inline-block; }
        h1 { color: #e74c3c; }
    </style>
</head>
<body>
    <div class="error-box">
        <h1>⚠️ Database Tidak Tersedia</h1>
        <p><?= esc($message) ?></p>
    </div>
</body>
</html>
