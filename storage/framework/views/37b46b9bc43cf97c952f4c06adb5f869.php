<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Transaksi - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Sistem Inventaris - Transaksi</a>
            <div class="d-flex gap-2">
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-outline-light btn-sm">Kembali ke Dashboard</a>
                <form action="<?php echo e(route('logout')); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Daftar Transaksi</h4>
            <a href="<?php echo e(route('admin.transactions.create')); ?>" class="btn btn-primary">Tambah Transaksi</a>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Waktu</th>
                        <th>Item</th>
                        <th>Kode</th>
                        <th>Jumlah</th>
                        <th>Tipe</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($index + 1); ?></td>
                            <td><?php echo e($transaction->created_at->format('d M Y H:i')); ?></td>
                            <td><?php echo e($transaction->item->nama ?? 'Item tidak ditemukan'); ?></td>
                            <td><?php echo e($transaction->item->kode ?? '-'); ?></td>
                            <td><?php echo e($transaction->jumlah); ?></td>
                            <td><?php echo e(ucfirst($transaction->tipe)); ?></td>
                            <td><?php echo e($transaction->keterangan ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center">Belum ada transaksi.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php /**PATH C:\laragon\www\PraktikumWeb\resources\views/admin/transactions/index.blade.php ENDPATH**/ ?>