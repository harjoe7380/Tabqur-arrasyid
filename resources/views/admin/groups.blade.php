@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Manajemen Kelompok Kurban</h2>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Kembali ke Dasbor</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <!-- Form Tambah Kelompok -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white fw-bold">Tambah Kelompok</div>
                <div class="card-body">
                    <form action="{{ route('admin.groups.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label>Nama Kelompok</label>
                            <input type="text" name="name" class="form-control" placeholder="Cth: Sapi 1 / Kambing 1" required>
                        </div>
                        <div class="mb-3">
                            <label>Jenis Hewan</label>
                            <select name="animal_type" class="form-select" required>
                                <option value="Sapi">Sapi (Maks 7 Orang)</option>
                                <option value="Kambing">Kambing (1 Orang)</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Buat Kelompok</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Daftar Kelompok -->
        <div class="col-md-8">
            @foreach($groups as $group)
            @php
                $totalTerkumpulGrup = 0;
                foreach($group->participants as $p) {
                    $verified = $p->transactions->where('status', 'verified');
                    $terkumpul = $verified->where('type', 'setoran')->sum('amount') - $verified->where('type', 'penarikan')->sum('amount');
                    $totalTerkumpulGrup += $terkumpul;
                }
                $kembalian = $group->purchase_price ? $totalTerkumpulGrup - $group->purchase_price : 0;
            @endphp
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 fw-bold">{{ $group->name }} ({{ $group->animal_type }})</h5>
                    <span class="badge bg-info text-dark">{{ $group->participants->count() }} Anggota</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.groups.updatePrice', $group->id) }}" method="POST" class="d-flex mb-3 align-items-center">
                        @csrf
                        <label class="me-2 fw-bold text-nowrap">Harga Beli Asli:</label>
                        <input type="number" name="purchase_price" class="form-control me-2" value="{{ $group->purchase_price }}" placeholder="Belum diinput">
                        <button type="submit" class="btn btn-sm btn-success">Simpan Harga</button>
                    </form>
                    
                    @if($group->purchase_price)
                        <div class="alert alert-{{ $kembalian >= 0 ? 'success' : 'danger' }} p-2">
                            Terkumpul: <strong>Rp {{ number_format($totalTerkumpulGrup, 0, ',', '.') }}</strong> | 
                            Kembalian/Sisa Uang: <strong>Rp {{ number_format($kembalian, 0, ',', '.') }}</strong>
                        </div>
                    @endif

                    <ul class="list-group mb-3">
                        @foreach($group->participants as $p)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $p->user->name }}
                            <form action="{{ route('admin.groups.removeParticipant', $p->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger py-0 px-2" title="Keluarkan">✖</button>
                            </form>
                        </li>
                        @endforeach
                        @if($group->participants->isEmpty())
                            <li class="list-group-item text-muted text-center">Belum ada anggota</li>
                        @endif
                    </ul>

                    @if($group->animal_type == 'Sapi' && $group->participants->count() < 7 || $group->animal_type == 'Kambing' && $group->participants->count() < 1)
                    <form action="{{ route('admin.groups.assign', $group->id) }}" method="POST" class="d-flex">
                        @csrf
                        <select name="participant_id" class="form-select me-2" required>
                            <option value="">-- Pilih Jamaah Lunas --</option>
                            @foreach($participants as $p)
                                <option value="{{ $p->id }}">{{ $p->user->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-primary text-nowrap">Masukan Grup</button>
                    </form>
                    @else
                    <div class="alert alert-warning p-2 text-center mb-0">Kelompok Sudah Penuh!</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
