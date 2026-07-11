@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="card shadow">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Daftar Inventaris - User</h4>
            <form action="{{ route('logout') }}" method="POST" class="mb-0">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">Logout</button>
            </form>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <input id="searchQuery" type="search" class="form-control" placeholder="Cari inventaris berdasarkan kode atau nama..." autocomplete="off">
            </div>

            <div id="alertContainer"></div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Nama Barang</th>
                            <th>Kode</th>
                            <th>Stok</th>
                        </tr>
                    </thead>
                    <tbody id="itemsTableBody">
                        @forelse($items as $item)
                        <tr>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->kode }}</td>
                            <td>{{ $item->stok }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center">Belum ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const searchUrl = "{{ route('inventaris.search') }}";
    const searchInput = document.getElementById('searchQuery');
    const itemsTableBody = document.getElementById('itemsTableBody');
    const alertContainer = document.getElementById('alertContainer');

    function escapeHtml(text) {
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
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
                    <td colspan="3" class="text-center">Tidak ada data yang sesuai.</td>
                </tr>
            `;
            return;
        }

        itemsTableBody.innerHTML = items.map(item => `
            <tr>
                <td>${escapeHtml(item.nama)}</td>
                <td>${escapeHtml(item.kode)}</td>
                <td>${item.stok}</td>
            </tr>
        `).join('');
    }

    async function fetchItems(query = '') {
        try {
            const response = await fetch(`${searchUrl}?q=${encodeURIComponent(query)}`);
            const data = await response.json();
            renderRows(data.items || []);
        } catch (error) {
            showAlert('Gagal memuat data inventaris.', 'danger');
        }
    }

    let debounceTimeout = null;
    searchInput.addEventListener('input', () => {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => fetchItems(searchInput.value.trim()), 300);
    });

    document.addEventListener('DOMContentLoaded', () => {
        fetchItems('');
    });
</script>
@endpush
