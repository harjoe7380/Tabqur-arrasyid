@extends('layouts.app')

@section('content')
<div class="container text-center mt-5 pt-5">
    <div class="display-1 text-muted mb-4">📶</div>
    <h2 class="fw-bold text-secondary">Anda Sedang Offline</h2>
    <p class="text-muted">Sepertinya koneksi internet Anda terputus. Sistem Tabungan Qurban membutuhkan koneksi internet untuk melihat data terbaru dan melakukan setoran.</p>
    <button onclick="window.location.reload()" class="btn btn-primary mt-3 px-4 rounded-pill">Coba Muat Ulang</button>
</div>
@endsection
