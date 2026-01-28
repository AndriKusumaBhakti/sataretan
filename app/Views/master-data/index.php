<?= $this->extend('default/layout-template', get_defined_vars()); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- ================= HEADER ================= -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 font-weight-bold text-gray-800 mb-1">
                Manajemen User
            </h1>
            <p class="text-muted small mb-0">
                Kelola dan pantau pengguna sistem
            </p>
        </div>
        <?php if ($kategori === 'siswa'): ?>
            <div class="btn-group">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                    data-toggle="dropdown">
                    <i class="fas fa-download mr-1"></i> Export
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow-sm">
                    <a class="dropdown-item text-success"
                        href="<?= site_url('master-data/' . $kategori . '/export-excel') ?>">
                        <i class="fas fa-file-excel mr-2"></i> Excel
                    </a>
                    <a class="dropdown-item text-danger"
                        href="<?= site_url('master-data/' . $kategori . '/export-pdf') ?>">
                        <i class="fas fa-file-pdf mr-2"></i> PDF
                    </a>
                </div>
            </div>
        <?php endif; ?>
        <a href="<?= base_url('master-data/' . $kategori . '/create') ?>"
            class="btn btn-success shadow-sm px-4">
            <i class="fas fa-plus mr-1"></i> Tambah Data
        </a>
    </div>

    <!-- ================= STATISTIK CARD ================= -->
    <?php if ($kategori === 'siswa'): ?>
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
                            <?= count(array_filter($users, fn($u) => $u['paket_status'] === 'A')) ?>
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
                            <?= count(array_filter($users, fn($u) => $u['paket_status'] === 'P')) ?>
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
                            <?= count(array_filter($users, fn($u) => $u['paket_status'] === 'I')) ?>
                        </div>
                        <i class="fas fa-user-times stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- ================= CONTENT ================= -->
    <?php if (empty($users)): ?>
        <div class="empty-state">
            <i class="fas fa-users empty-icon"></i>
            <h5>Belum Ada User</h5>
            <p class="text-muted">
                Gunakan tombol <b>Tambah Data</b> untuk menambahkan user baru
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
                                <?php if ($kategori === 'siswa'): ?>
                                    <th>Paket</th>
                                    <th>Status</th>
                                    <th>Expired</th>
                                <?php else: ?>
                                    <th>Role</th>
                                <?php endif; ?>

                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $i => $u): ?>
                                <tr>
                                    <td><?= $i + 1 ?></td>
                                    <td class="font-weight-bold"><?= esc($u['name']) ?></td>
                                    <td><?= esc($u['email']) ?></td>
                                    <td><?= esc($u['phone']) ?></td>
                                    <td><?= esc(strtoupper($u['user_program'] ?? '-')) ?></td>
                                    <?php if ($kategori === 'siswa'): ?>
                                        <td><?= esc($u['name_paket']) ?></td>
                                        <td>
                                            <?php if ($u['paket_status'] === 'A'): ?>
                                                <span class="badge badge-soft-success">Aktif</span>
                                            <?php elseif ($u['paket_status'] === 'P'): ?>
                                                <span class="badge badge-soft-warning">Pending</span>
                                                <!-- APPROVE (KHUSUS SISWA PENDING) -->
                                                <?php if ($kategori === 'siswa' && $u['paket_status'] === 'P'): ?>
                                                    <form action="<?= base_url('master-data/siswa/approve/' . $u['id']) ?>"
                                                        method="post"
                                                        class="d-inline">
                                                        <?= csrf_field() ?>
                                                        <button type="submit"
                                                            class="btn btn-sm btn-success"
                                                            onclick="return confirm('Approve siswa ini?')"
                                                            title="Approve">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge badge-soft-danger">Tidak Aktif</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= $u['paket_exp'] ? date('d M Y', strtotime($u['paket_exp'])) : '-' ?>
                                        </td>
                                    <?php else: ?>
                                        <td>
                                            <?= esc($u['role']) ?>
                                        </td>
                                    <?php endif; ?>

                                    <td class="text-center">
                                        <a href="<?= base_url('master-data/' . $kategori . '/edit/' . $u['id']) ?>"
                                            class="btn btn-sm btn-light border mr-1"
                                            title="Edit">
                                            <i class="fas fa-edit text-warning"></i>
                                        </a>

                                        <form action="<?= base_url('master-data/' . $kategori . '/delete/' . $u['id']) ?>"
                                            method="post"
                                            class="d-inline">
                                            <?= csrf_field() ?>
                                            <button class="btn btn-sm btn-light border"
                                                onclick="return confirm('Yakin ingin menghapus data ini?')"
                                                title="Hapus">
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
    .stat-card {
        border-radius: 16px;
        color: #fff;
        position: relative;
        overflow: hidden;
        transition: .3s ease;
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
        font-size: 28px;
        font-weight: 700;
    }

    .stat-icon {
        position: absolute;
        right: 20px;
        bottom: 20px;
        font-size: 42px;
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
        border-radius: 18px;
        padding: 80px 20px;
        text-align: center;
        box-shadow: 0 14px 38px rgba(0, 0, 0, .08);
    }

    .empty-icon {
        font-size: 56px;
        color: #d1d3e2;
        margin-bottom: 12px;
    }
</style>

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