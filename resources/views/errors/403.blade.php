@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-danger text-white text-center">
                    <h4>403 - Akses Ditolak</h4>
                </div>
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="display-1">🚫</i>
                    </div>
                    <h2 class="mb-4">Forbidden</h2>
                    <p class="lead mb-4">Anda tidak memiliki izin untuk mengakses halaman ini.</p>
                    <p class="text-muted">Halaman ini hanya dapat diakses oleh Administrator.</p>
                    <a href="{{ url()->previous() }}" class="btn btn-primary">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 