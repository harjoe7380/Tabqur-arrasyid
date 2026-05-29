@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Portal Peserta Kurban</h2>
        @if(!isset($error))
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalLaporSetoran">
            + Lapor Setoran
        </button>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm border-0">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger shadow-sm border-0">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @isset($error)
        <div class="alert alert-danger shadow-sm border-0">{{ $error }}</div>
    @else
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-body text-center py-5">
                <h5 class="text-uppercase text-muted fw-bold mb-3">Total Tabungan Saat Ini</h5>
                <h1 class="display-4 text-success fw-bold mb-3">Rp {{ number_format($totalSavings, 0, ',', '.') }}</h1>
                <p class="text-muted mb-4">Target Kurban: <strong>Rp {{ number_format($participant->target_amount, 0, ',', '.') }}</strong></p>
                
                <div class="progress mb-2" style="height: 25px; border-radius: 15px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success fw-bold" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                        {{ number_format($progress, 1) }}%
                    </div>
                </div>
                
                @if($participant->target_amount - $totalSavings > 0)
                    <small class="text-danger fw-bold">Kurang Rp {{ number_format($participant->target_amount - $totalSavings, 0, ',', '.') }} lagi untuk mencapai target.</small>
                @else
                    <small class="text-success fw-bold">🎉 Alhamdulillah! Tabungan Anda sudah memenuhi target kurban.</small>
                @endif
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white fw-bold py-3">Riwayat Transaksi</div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($transactions as $t)
                    <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                        <div>
                            <strong class="d-block">{{ ucfirst($t->type) }}</strong>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($t->date)->translatedFormat('d F Y') }}</small>
                            @if($t->description)
                                <br><small class="text-secondary">{{ $t->description }}</small>
                            @endif
                            <br>
                            @if($t->status == 'pending')
                                <span class="badge bg-warning text-dark mt-1">Menunggu Verifikasi</span>
                            @elseif($t->status == 'rejected')
                                <span class="badge bg-danger mt-1">Ditolak</span>
                            @else
                                <span class="badge bg-success mt-1">Terverifikasi</span>
                            @endif
                        </div>
                        <span class="fs-5 {{ $t->type == 'setoran' ? 'text-success' : 'text-danger' }} fw-bold">
                            {{ $t->type == 'setoran' ? '+' : '-' }} Rp {{ number_format($t->amount, 0, ',', '.') }}
                        </span>
                    </li>
                    @empty
                    <li class="list-group-item text-center text-muted py-4">Belum ada riwayat transaksi tabungan.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    @endisset
</div>

<!-- Modal Lapor Setoran -->
@if(!isset($error))
<div class="modal fade" id="modalLaporSetoran" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Lapor Setoran Tabungan</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('peserta.transactions.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
            <div class="alert alert-info">
                Silakan transfer setoran Anda ke Rekening DKM: <strong>BSI 1234567890 a.n DKM Masjid</strong>, lalu unggah buktinya di sini.
            </div>
            <div class="mb-3">
                <label>Nominal Setoran (Rp)</label>
                <input type="number" name="amount" class="form-control" required min="1000">
            </div>
            <div class="mb-3">
                <label>Tanggal Transfer</label>
                <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="mb-3">
                <label>Foto Bukti Transfer</label>
                <input type="file" name="proof" class="form-control" accept="image/jpeg,image/png,image/jpg" required>
            </div>
            <div class="mb-3">
                <label>Keterangan Tambahan (Opsional)</label>
                <input type="text" name="description" class="form-control" placeholder="Cth: Titip lewat Pak RT">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Kirim Laporan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endif
@endsection
