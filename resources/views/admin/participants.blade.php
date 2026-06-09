@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Manajemen Jamaah</h2>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary me-2">Kembali ke Dasbor</a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahJamaah">+ Tambah Jamaah Manual</button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white fw-bold py-3">Daftar Jamaah Qurban</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>No. WA</th>
                            <th>Target</th>
                            <th>Terkumpul</th>
                            <th>Kekurangan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($participants as $p)
                        @php
                            $verified = $p->transactions->where('status', 'verified');
                            $setoran = $verified->where('type', 'setoran')->sum('amount');
                            $penarikan = $verified->where('type', 'penarikan')->sum('amount');
                            $terkumpul = $setoran - $penarikan;
                            $kekurangan = max(0, $p->target_amount - $terkumpul);
                        @endphp
                        <tr>
                            <td>{{ $p->user->name }}</td>
                            <td>{{ $p->user->no_hp ?? '-' }}</td>
                            <td>Rp {{ number_format($p->target_amount, 0, ',', '.') }}</td>
                            <td class="text-success fw-bold">Rp {{ number_format($terkumpul, 0, ',', '.') }}</td>
                            <td class="{{ $kekurangan > 0 ? 'text-danger' : 'text-success' }}">
                                {{ $kekurangan > 0 ? 'Rp ' . number_format($kekurangan, 0, ',', '.') : 'Lunas' }}
                            </td>
                            <td>
                                <div class="d-flex">
                                    <button class="btn btn-sm btn-outline-warning me-1" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditJamaah" 
                                        data-id="{{ $p->id }}" 
                                        data-name="{{ $p->user->name }}" 
                                        data-no_hp="{{ $p->user->no_hp }}" 
                                        data-email="{{ $p->user->email }}" 
                                        data-target="{{ $p->target_amount }}" 
                                        onclick="populateEditModal(this)">✏️ Edit</button>

                                    <form action="{{ route('admin.participants.destroy', $p->id) }}" method="POST" onsubmit="return confirm('YAKIN HAPUS JAMAAH INI? Semua riwayat tabungannya akan ikut terhapus permanen!');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">🗑️ Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @if($participants->isEmpty())
                        <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada jamaah yang terdaftar.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Jamaah -->
<div class="modal fade" id="modalTambahJamaah" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Tambah Jamaah Manual</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.participants.store') }}" method="POST">
        @csrf
        <div class="modal-body">
            <div class="mb-3">
                <label>Nama Lengkap</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Nomor WhatsApp</label>
                <input type="text" name="no_hp" class="form-control" placeholder="Cth: 08123456789" required>
            </div>
            <div class="mb-3">
                <label>Email (Untuk Login)</label>
                <input type="email" name="email" class="form-control" required>
                <small class="text-muted">Password otomatis: <strong>password123</strong></small>
            </div>
            <div class="mb-3">
                <label>Target Tabungan Qurban (Rp)</label>
                <input type="number" name="target_amount" class="form-control" value="3500000" required>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan Jamaah</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Edit Jamaah -->
<div class="modal fade" id="modalEditJamaah" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title">Edit Jamaah</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="edit_form" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
            <div class="mb-3">
                <label>Nama Lengkap</label>
                <input type="text" name="name" id="edit_name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Nomor WhatsApp</label>
                <input type="text" name="no_hp" id="edit_no_hp" class="form-control" placeholder="Cth: 08123456789" required>
            </div>
            <div class="mb-3">
                <label>Email (Untuk Login)</label>
                <input type="email" name="email" id="edit_email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Target Tabungan Qurban (Rp)</label>
                <input type="number" name="target_amount" id="edit_target_amount" class="form-control" required>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-warning">Update Jamaah</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function populateEditModal(btn) {
    document.getElementById('edit_form').action = '/admin/participants/' + btn.dataset.id;
    document.getElementById('edit_name').value = btn.dataset.name;
    document.getElementById('edit_no_hp').value = btn.dataset.no_hp;
    document.getElementById('edit_email').value = btn.dataset.email;
    document.getElementById('edit_target_amount').value = btn.dataset.target;
}
</script>
@endsection
