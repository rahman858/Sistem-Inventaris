<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Sistem Inventaris - Admin</a>
            <div>
                <span class="text-white me-3">Halo, {{ Auth::user()->name }} (Admin)</span>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col-md-8">
                <input id="searchInput" type="search" class="form-control" placeholder="Cari kode atau nama item..." autocomplete="off">
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('admin.transactions.index') }}" class="btn btn-secondary btn-sm me-2">Lihat Transaksi</a>
                <button id="btnAddItem" class="btn btn-success btn-sm">+ Tambah Item Baru</button>
            </div>
        </div>

        <div id="alertContainer"></div>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Dashboard Admin - Manajemen Inventaris</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th>Jumlah Stok</th>
                                <th width="180">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="itemsTableBody">
                            @forelse($items as $index => $item)
                            <tr data-id="{{ $item->id }}">
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->kode }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->stok }}</td>
                                <td>
                                    <button type="button" class="btn btn-warning btn-sm btn-edit" data-id="{{ $item->id }}" data-kode="{{ $item->kode }}" data-nama="{{ $item->nama }}" data-stok="{{ $item->stok }}">Edit</button>
                                    <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="{{ $item->id }}">Hapus</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada data inventaris. Silakan tambah data.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="itemModal" tabindex="-1" aria-labelledby="itemModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="itemModalLabel">Tambah Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="itemForm">
                    <div class="modal-body">
                        <input type="hidden" id="itemId" name="itemId" value="">
                        <div class="mb-3">
                            <label for="kode" class="form-label">Kode Item</label>
                            <input type="text" id="kode" name="kode" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="nama" class="form-label">Nama Item</label>
                            <input type="text" id="nama" name="nama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="stok" class="form-label">Stok</label>
                            <input type="number" id="stok" name="stok" class="form-control" min="0" required>
                        </div>
                        <div id="formErrors" class="text-danger"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary" id="submitButton">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const indexUrl = "{{ route('admin.items.ajax.index') }}";
        const storeUrl = "{{ route('admin.items.ajax.store') }}";
        const updateBaseUrl = "{{ url('/admin/items/ajax') }}";

        const searchInput = document.getElementById('searchInput');
        const itemsTableBody = document.getElementById('itemsTableBody');
        const alertContainer = document.getElementById('alertContainer');
        const itemModalElement = document.getElementById('itemModal');
        const itemModal = new bootstrap.Modal(itemModalElement);
        const itemForm = document.getElementById('itemForm');
        const itemIdField = document.getElementById('itemId');
        const itemKodeField = document.getElementById('kode');
        const itemNamaField = document.getElementById('nama');
        const itemStokField = document.getElementById('stok');
        const itemModalLabel = document.getElementById('itemModalLabel');
        const submitButton = document.getElementById('submitButton');
        const formErrors = document.getElementById('formErrors');

        let debounceTimeout = null;
        let currentMode = 'create';

        function escapeHtml(text) {
            return String(text)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/\"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function showAlert(message, type = 'success') {
            alertContainer.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${escapeHtml(message)}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
        }

        function renderRows(items) {
            if (!items.length) {
                itemsTableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center">Belum ada data inventaris. Silakan tambah data.</td>
                    </tr>
                `;
                return;
            }

            itemsTableBody.innerHTML = items.map((item, index) => `
                <tr data-id="${item.id}">
                    <td>${index + 1}</td>
                    <td>${escapeHtml(item.kode)}</td>
                    <td>${escapeHtml(item.nama)}</td>
                    <td>${item.stok}</td>
                    <td>
                        <button type="button" class="btn btn-warning btn-sm btn-edit" data-id="${item.id}" data-kode="${escapeHtml(item.kode)}" data-nama="${escapeHtml(item.nama)}" data-stok="${item.stok}">Edit</button>
                        <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="${item.id}">Hapus</button>
                    </td>
                </tr>
            `).join('');
        }

        async function fetchItems(search = '') {
            try {
                const response = await fetch(`${indexUrl}?search=${encodeURIComponent(search)}`);
                const data = await response.json();
                renderRows(data.items || []);
            } catch (error) {
                showAlert('Gagal memuat data inventaris. Periksa koneksi Anda.', 'danger');
            }
        }

        function resetForm() {
            currentMode = 'create';
            itemIdField.value = '';
            itemKodeField.value = '';
            itemNamaField.value = '';
            itemStokField.value = '';
            itemModalLabel.textContent = 'Tambah Item';
            submitButton.textContent = 'Simpan';
            formErrors.textContent = '';
        }

        function openEditModal(item) {
            currentMode = 'edit';
            itemIdField.value = item.id;
            itemKodeField.value = item.kode;
            itemNamaField.value = item.nama;
            itemStokField.value = item.stok;
            itemModalLabel.textContent = 'Edit Item';
            submitButton.textContent = 'Update';
            formErrors.textContent = '';
            itemModal.show();
        }

        document.getElementById('btnAddItem').addEventListener('click', () => {
            resetForm();
            itemModal.show();
        });

        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => fetchItems(searchInput.value.trim()), 300);
        });

        itemsTableBody.addEventListener('click', async (event) => {
            const editButton = event.target.closest('.btn-edit');
            const deleteButton = event.target.closest('.btn-delete');

            if (editButton) {
                openEditModal({
                    id: editButton.dataset.id,
                    kode: editButton.dataset.kode,
                    nama: editButton.dataset.nama,
                    stok: editButton.dataset.stok,
                });
                return;
            }

            if (deleteButton) {
                const itemId = deleteButton.dataset.id;
                if (!confirm('Yakin ingin menghapus item ini?')) {
                    return;
                }

                try {
                    const response = await fetch(`${updateBaseUrl}/${itemId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                    });

                    if (!response.ok) {
                        throw new Error('Gagal menghapus item');
                    }

                    const data = await response.json();
                    showAlert(data.message || 'Item berhasil dihapus!');
                    fetchItems(searchInput.value.trim());
                } catch (error) {
                    showAlert('Gagal menghapus item. Silakan coba lagi.', 'danger');
                }
            }
        });

        itemForm.addEventListener('submit', async (event) => {
            event.preventDefault();

            const payload = {
                kode: itemKodeField.value.trim(),
                nama: itemNamaField.value.trim(),
                stok: parseInt(itemStokField.value, 10),
            };
            const itemId = itemIdField.value;
            const method = currentMode === 'edit' ? 'PUT' : 'POST';
            const url = currentMode === 'edit' ? `${updateBaseUrl}/${itemId}` : storeUrl;

            try {
                const response = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(payload),
                });

                const data = await response.json();

                if (!response.ok) {
                    const errors = data.errors || {};
                    formErrors.innerHTML = Object.values(errors).flat().map(error => `<div>${escapeHtml(error)}</div>`).join('');
                    return;
                }

                showAlert(data.message || 'Perubahan berhasil disimpan!');
                itemModal.hide();
                fetchItems(searchInput.value.trim());
            } catch (error) {
                showAlert('Terjadi kesalahan saat menyimpan item.', 'danger');
            }
        });
    </script>
</body>
</html>
