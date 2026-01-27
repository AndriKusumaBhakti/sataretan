<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        h2 {
            margin-bottom: 4px;
        }
        .subtitle {
            font-size: 11px;
            color: #666;
            margin-bottom: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px 6px;
            border: 1px solid #ddd;
        }
        th {
            background: #f4f6f8;
            text-align: center;
        }
        td {
            vertical-align: middle;
        }
        .center { text-align: center; }
        .right { text-align: right; }
    </style>
</head>
<body>

<h2><?= esc($judul) ?></h2>
<div class="subtitle">Dicetak: <?= date('d M Y H:i') ?></div>

<table>
    <thead>
        <tr>
            <th width="40">#</th>
            <th>Nama</th>
            <th width="140">Mulai</th>
            <th width="140">Selesai</th>
            <th width="80">Nilai</th>
            <th width="90">Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($nilai as $i => $n): ?>
            <tr>
                <td class="center"><?= $i + 1 ?></td>
                <td><?= esc($n['nama']) ?></td>
                <td class="center"><?= date('d M Y H:i', strtotime($n['started_at'])) ?></td>
                <td class="center">
                    <?= $n['finished_at']
                        ? date('d M Y H:i', strtotime($n['finished_at']))
                        : '-' ?>
                </td>
                <td class="right"><?= number_format($n['skor_akhir'], 2) ?></td>
                <td class="center"><?= ucfirst($n['status']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
