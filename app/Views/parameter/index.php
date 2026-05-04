<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- ================= HEADER ================= -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h4 font-weight-bold text-gray-800 mb-1">
                Manajemen Parameter
            </h1>
            <p class="text-muted small mb-0">
                Kelola konfigurasi parameter sistem
            </p>
        </div>

        <a href="<?= base_url('maintenance/parameter/create') ?>"
            class="btn btn-success shadow-sm px-4 rounded-pill">
            <i class="fas fa-plus mr-1"></i> Tambah Parameter
        </a>
    </div>

    <!-- ================= TABLE ================= -->
    <?php if (empty($params)): ?>

        <div class="empty-state">
            <i class="fas fa-database empty-icon"></i>
            <h5>Belum Ada Parameter</h5>
            <p class="text-muted">
                Gunakan tombol <b>Tambah Parameter</b> untuk menambahkan data
            </p>
        </div>

    <?php else: ?>

        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-body p-6">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0" id="paramTable">

                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Code</th>
                                <th>Value</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($params as $i => $p): ?>

                                <tr>

                                    <td><?= $i + 1 ?></td>

                                    <td class="font-weight-bold">
                                        <?= esc($p['code']) ?>
                                    </td>

                                    <td style="max-width:400px">
                                        <code style="font-size:12px">
                                            <?= esc(substr($p['value'], 0, 120)) ?>...
                                        </code>
                                    </td>

                                    <td class="text-center text-nowrap">

                                        <a href="<?= base_url('maintenance/parameter/edit/' . $p['id']) ?>"
                                            class="btn btn-sm btn-light border rounded-circle mr-1">
                                            <i class="fas fa-edit text-warning"></i>
                                        </a>

                                        <form action="<?= base_url('maintenance/parameter/delete/' . $p['id']) ?>"
                                            method="post"
                                            class="d-inline">

                                            <?= csrf_field() ?>

                                            <button class="btn btn-sm btn-light border rounded-circle"
                                                onclick="return confirm('Yakin ingin menghapus parameter ini?')">
                                                <i class="fas fa-trash text-danger"></i>
                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>
        </div>

    <?php endif; ?>

</div>

<!-- ================= STYLE ================= -->
<style>
    .empty-state {
        background: #fff;
        border-radius: 20px;
        padding: 80px 20px;
        text-align: center;
        box-shadow: 0 14px 38px rgba(0, 0, 0, .08)
    }

    .empty-icon {
        font-size: 60px;
        color: #d1d3e2;
        margin-bottom: 14px
    }

    code {
        background: #f8f9fc;
        padding: 6px 10px;
        border-radius: 6px;
        display: block;
        white-space: pre-wrap;
        word-break: break-word
    }
</style>

<!-- ================= SCRIPT ================= -->
<script>
    document.addEventListener('DOMContentLoaded', function() {

        if (window.jQuery && $('#paramTable').length) {
            $('#paramTable').DataTable({
                pageLength: 10,
                responsive: true
            });
        }

    });
</script>

<?= $this->endSection(); ?>