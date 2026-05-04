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
                    <div class="stat-value">
                        <?= count($users) ?>
                    </div>
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


    <!-- ================= TABLE ================= -->
    <?php if (empty($users)): ?>
        <div class="empty-state">
            <i class="fas fa-users empty-icon"></i>
            <h5>Belum Ada User</h5>
            <p class="text-muted">
                Data user belum tersedia
            </p>
        </div>
    <?php else: ?>

        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-body p-0">
                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0" id="userTable">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>No HP</th>
                                <th>Program</th>
                                <th>Paket</th>
                                <th>Status</th>
                                <th>Expired</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($users as $i => $u): ?>
                                <tr>

                                    <td><?= $i + 1 ?></td>

                                    <td class="font-weight-bold">
                                        <?= esc($u['name']) ?>
                                    </td>

                                    <td>
                                        <?= esc($u['email']) ?>
                                    </td>

                                    <td>
                                        <?= esc($u['phone'] ?? '-') ?>
                                    </td>

                                    <td>
                                        <?= esc(strtoupper($u['user_program'] ?? '-')) ?>
                                    </td>

                                    <td>
                                        <?= esc($u['name_paket'] ?? '-') ?>
                                    </td>

                                    <td>
                                        <?php if (($u['paket_status'] ?? '') === 'A'): ?>
                                            <span class="badge badge-soft-success">Aktif</span>
                                        <?php elseif (($u['paket_status'] ?? '') === 'P'): ?>
                                            <span class="badge badge-soft-warning">Pending</span>
                                        <?php else: ?>
                                            <span class="badge badge-soft-danger">Tidak Aktif</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?= !empty($u['paket_exp'])
                                            ? date('d M Y', strtotime($u['paket_exp']))
                                            : '-' ?>
                                    </td>

                                    <td class="text-center text-nowrap">

                                        <!-- DETAIL HISTORY -->
                                         <!-- UPDATE STATUS INACTIVE -->
                                        <?php if (($u['paket_status'] ?? '') !== 'P'): ?>
                                            <a href="<?= base_url('maintenance/detail/' . $u['id']) ?>"
                                                class="btn btn-sm btn-light border rounded-circle mr-1"
                                                title="Detail History">
                                                <i class="fas fa-eye text-primary"></i>
                                            </a>
                                        <?php endif; ?>
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
    .select-paket {
        height: 48px;
        appearance: none;
        background-repeat: no-repeat;
        background-position: right 14px center;
        background-size: 14px;
    }

    .stat-card {
        border-radius: 18px;
        color: #fff;
        position: relative;
        overflow: hidden;
        transition: .3s;
    }

    .stat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, .15);
    }

    .stat-title {
        font-size: 12px;
        text-transform: uppercase;
        opacity: .85;
    }

    .stat-value {
        font-size: 30px;
        font-weight: 700;
    }

    .stat-icon {
        position: absolute;
        right: 18px;
        bottom: 18px;
        font-size: 44px;
        opacity: .25;
    }

    .stat-primary {
        background: linear-gradient(135deg, #4e73df, #224abe);
    }

    .stat-success {
        background: linear-gradient(135deg, #1cc88a, #13855c);
    }

    .stat-warning {
        background: linear-gradient(135deg, #f6c23e, #dda20a);
    }

    .stat-danger {
        background: linear-gradient(135deg, #e74a3b, #be2617);
    }

    .empty-state {
        background: #fff;
        border-radius: 20px;
        padding: 80px 20px;
        text-align: center;
        box-shadow: 0 14px 38px rgba(0, 0, 0, .08);
    }

    .empty-icon {
        font-size: 60px;
        color: #d1d3e2;
        margin-bottom: 14px;
    }

    .table td,
    .table th {
        vertical-align: middle;
        white-space: nowrap;
    }
</style>


<!-- ================= SCRIPT ================= -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.jQuery && $('#userTable').length) {
            $('#userTable').DataTable({
                pageLength: 10,
                responsive: true
            });
        }
    });
</script>

<?= $this->endSection(); ?>