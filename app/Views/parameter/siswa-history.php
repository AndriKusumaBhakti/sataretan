<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- ================= HEADER ================= -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="h4 font-weight-bold text-gray-800 mb-1">
                History Approval User
            </h1>
            <p class="text-muted small mb-0">
                Kelola dan pantau history approval paket user
            </p>
        </div>
    </div>

    <!-- ================= STATISTIK CARD ================= -->
    <div class="row">

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card stat-primary">
                <div class="card-body">
                    <div class="stat-title">Total User</div>
                    <div class="stat-value"><?= count($users) ?></div>
                    <i class="fas fa-users stat-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card stat-success">
                <div class="card-body">
                    <div class="stat-title">Aktif</div>
                    <div class="stat-value">
                        <?= count(array_filter($users, fn($u) => ($u['paket_status'] ?? '') === 'A')) ?>
                    </div>
                    <i class="fas fa-user-check stat-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card stat-warning">
                <div class="card-body">
                    <div class="stat-title">Pending</div>
                    <div class="stat-value">
                        <?= count(array_filter($users, fn($u) => ($u['paket_status'] ?? '') === 'P')) ?>
                    </div>
                    <i class="fas fa-user-clock stat-icon"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card stat-danger">
                <div class="card-body">
                    <div class="stat-title">Tidak Aktif</div>
                    <div class="stat-value">
                        <?= count(array_filter($users, fn($u) => ($u['paket_status'] ?? '') === 'I')) ?>
                    </div>
                    <i class="fas fa-user-times stat-icon"></i>
                </div>
            </div>
        </div>

    </div>

    <!-- ================= FILTER ================= -->
    <form method="get" class="mb-4 d-flex flex-wrap gap-2 align-items-end">

        <div>
            <label>Status</label>
            <select name="status" class="form-control">
                <option value="">Semua</option>
                <option value="A" <?= ($filter['status'] == 'A') ? 'selected' : '' ?>>Aktif</option>
                <option value="P" <?= ($filter['status'] == 'P') ? 'selected' : '' ?>>Pending</option>
                <option value="I" <?= ($filter['status'] == 'I') ? 'selected' : '' ?>>Tidak Aktif</option>
            </select>
        </div>

        <div>
            <label>Dari</label>
            <input type="date" name="date_from" class="form-control"
                value="<?= $filter['date_from'] ?? '' ?>">
        </div>

        <div>
            <label>Sampai</label>
            <input type="date" name="date_to" class="form-control"
                value="<?= $filter['date_to'] ?? '' ?>">
        </div>

        <div>
            <button class="btn btn-primary">Filter</button>
            <a href="<?= base_url('maintenance/history-siswa') ?>" class="btn btn-secondary">Reset</a>
        </div>

    </form>

    <!-- ================= TABLE ================= -->
    <div class="card">
        <div class="card-body p-6">

            <table class="table table-bordered mb-0" id="userTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Program</th>
                        <th>Paket</th>
                        <th>Status</th>
                        <th>Expired</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                Tidak ada data
                            </td>
                        </tr>
                    <?php else: ?>

                        <?php foreach ($users as $i => $u): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td><?= esc($u['name']) ?></td>
                                <td><?= esc($u['email']) ?></td>
                                <td><?= esc($u['user_program'] ?? '-') ?></td>
                                <td><?= esc($u['name_paket'] ?? '-') ?></td>

                                <td>
                                    <?php if ($u['paket_status'] == 'A'): ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php elseif ($u['paket_status'] == 'P'): ?>
                                        <span class="badge badge-warning">Pending</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?= $u['paket_exp'] ? date('d M Y', strtotime($u['paket_exp'])) : '-' ?>
                                </td>

                                <td>
                                    <button
                                        class="btn btn-sm btn-primary btn-detail"
                                        data-id="<?= $u['id'] ?>">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php endif; ?>
                </tbody>

            </table>

        </div>
    </div>

</div>

<!-- ================= MODAL ================= -->
<div class="modal fade" id="modalHistory">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5>History Approval</h5>
                <button class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="historyContent"></div>
        </div>
    </div>
</div>

<!-- ================= STYLE ================= -->
<style>
    .stat-card {
        border-radius: 18px;
        color: #fff;
        position: relative;
        overflow: hidden;
    }

    .stat-title {
        font-size: 12px;
        text-transform: uppercase;
    }

    .stat-value {
        font-size: 30px;
        font-weight: bold;
    }

    .stat-icon {
        position: absolute;
        right: 15px;
        bottom: 15px;
        font-size: 40px;
        opacity: .3;
    }

    .stat-primary {
        background: #4e73df;
    }

    .stat-success {
        background: #1cc88a;
    }

    .stat-warning {
        background: #f6c23e;
    }

    .stat-danger {
        background: #e74a3b;
    }

    .table td,
    .table th {
        vertical-align: middle;
        white-space: nowrap;
    }
</style>

<!-- ================= SCRIPT ================= -->
<script>
    $(document).ready(function() {

        let table = $('#userTable').DataTable({
            pageLength: 10,
            responsive: true,
            ordering: true,
            searching: true,
            lengthChange: true,
            columnDefs: [{
                orderable: false,
                targets: 7
            }],
            drawCallback: function(settings) {
                var api = this.api();
                api.column(0, {
                    search: 'applied',
                    order: 'applied'
                }).nodes().each(function(cell, i) {
                    cell.innerHTML = i + 1;
                });
            }
        });

        // DETAIL HISTORY (FIX UNTUK DATATABLE)
        $(document).on('click', '.btn-detail', function() {

            const id = $(this).data('id');

            fetch("<?= base_url('maintenance/history/') ?>" + id)
                .then(res => res.json())
                .then(data => {

                    let html = '';

                    if (data.length === 0) {
                        html = '<p>Tidak ada history</p>';
                    } else {

                        html += `<table class="table table-bordered">
                    <tr>
                        <th>Approved By</th>
                        <th>Tanggal</th>
                        <th>Expired</th>
                        <th>Note</th>
                    </tr>`;

                        data.forEach(d => {
                            html += `
                        <tr>
                            <td>${d.approved_name ?? '-'}</td>
                            <td>${d.created_at ?? '-'}</td>
                            <td>${d.expired_at ?? '-'}</td>
                            <td>${d.note ?? '-'}</td>
                        </tr>`;
                        });

                        html += '</table>';
                    }

                    $('#historyContent').html(html);
                    $('#modalHistory').modal('show');
                });

        });

    });
</script>

<?= $this->endSection(); ?>