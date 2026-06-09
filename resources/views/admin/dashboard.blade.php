@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4 fw-bold">Dashboard Admin</h2>
    
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card bg-primary text-white shadow-sm border-0 h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h6 class="text-white-50 text-uppercase fw-bold">Total Tabungan Qurban</h6>
                    <h2 class="fw-bold mb-0">Rp {{ number_format($totalSavings, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card bg-success text-white shadow-sm border-0 h-100">
                <div class="card-body d-flex flex-column justify-content-center">
                    <h6 class="text-white-50 text-uppercase fw-bold">Jumlah Peserta Aktif</h6>
                    <h2 class="fw-bold mb-0">{{ $participants->count() }} Orang</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between mb-4">
        <div>
            <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddParticipant">
                + Tambah Jamaah Baru
            </button>
            <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddTransaction">
                + Catat Setoran / Penarikan
            </button>
        </div>
        <a href="{{ route('admin.report') }}" target="_blank" class="btn btn-outline-dark shadow-sm">
            Cetak Laporan Keuangan
        </a>
        <a href="{{ route('admin.participants.index') }}" class="btn btn-outline-info shadow-sm ms-2">
            👥 Manajemen Jamaah
        </a>
        <a href="{{ route('admin.groups.index') }}" class="btn btn-outline-primary shadow-sm ms-2">
            🐮 Plotting Hewan
        </a>
        <button class="btn btn-outline-secondary shadow-sm ms-2" data-bs-toggle="modal" data-bs-target="#modalSettings">
            ⚙️ Pengaturan
        </button>
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

    @if($pendingTransactions->count() > 0)
    <div class="card shadow-sm border-0 mb-4 border-warning">
        <div class="card-header bg-warning fw-bold py-3">Menunggu Verifikasi ({{ $pendingTransactions->count() }})</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Peserta</th>
                            <th>Nominal</th>
                            <th>Bukti</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingTransactions as $trx)
                        <tr>
                            <td>{{ $trx->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ optional($trx->participant->user)->name ?? 'Unknown' }}</td>
                            <td class="fw-bold text-success">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                            <td>
                                @if($trx->proof_path)
                                    <a href="{{ asset('storage/' . $trx->proof_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">Lihat Bukti</a>
                                @else
                                    <span class="text-muted">Tidak ada</span>
                                @endif
                            </td>
                            <td>
                                <form action="{{ route('admin.transactions.verify', $trx->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    <button class="btn btn-sm btn-success" onclick="return confirm('Setujui setoran ini?')">✔ Setujui</button>
                                </form>
                                <form action="{{ route('admin.transactions.verify', $trx->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Tolak setoran ini?')">✖ Tolak</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white fw-bold py-3">Transaksi Terbaru (Terverifikasi)</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Peserta</th>
                            <th>Tipe</th>
                            <th>Jumlah</th>
                            <th>Struk/Kwitansi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $trx)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($trx->date)->format('Y-m-d') }}</td>
                            <td>{{ optional($trx->participant->user)->name ?? 'Unknown' }}</td>
                            <td>
                                @if($trx->type == 'setoran')
                                    <span class="badge bg-success">Setoran</span>
                                @else
                                    <span class="badge bg-danger">Penarikan</span>
                                @endif
                            </td>
                            <td class="fw-bold">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
                            <td>
                                @if($trx->proof_path)
                                    @php $proofUrl = str_starts_with($trx->proof_path, 'data:image') ? $trx->proof_path : asset('storage/' . $trx->proof_path); @endphp
                                    <a href="{{ $proofUrl }}" target="_blank" download="Bukti_Trx_{{ $trx->id }}" class="btn btn-sm btn-outline-primary" title="Download Bukti">⬇️ Bukti</a>
                                @endif
                                <a href="{{ route('admin.receipt.pdf', $trx->id) }}" class="btn btn-sm btn-outline-danger">📄 PDF</a>
                                <a href="{{ route('admin.receipt.wa', $trx->id) }}" class="btn btn-sm btn-outline-success">💬 WA</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">Belum ada transaksi terverifikasi</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Participant -->
<div class="modal fade" id="modalAddParticipant" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Tambah Jamaah Baru</h5>
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
                <label>Email (Untuk Login)</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>No. WhatsApp (Awali dengan 08 / 62)</label>
                <input type="text" name="no_hp" class="form-control" placeholder="08123456789">
            </div>
            <div class="mb-3">
                <label>Password Akun</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Target Harga Hewan Qurban (Rp)</label>
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

<!-- Modal Add Transaction -->
<div class="modal fade" id="modalAddTransaction" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title">Catat Setoran / Penarikan</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.transactions.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
            <div class="mb-3">
                <label>Pilih Jamaah</label>
                <select name="participant_id" class="form-select" required>
                    <option value="">-- Pilih Jamaah --</option>
                    @foreach($participants as $p)
                        <option value="{{ $p->id }}">{{ $p->user->name }} (Target: Rp {{ number_format($p->target_amount, 0, ',', '.') }})</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label>Jenis Transaksi</label>
                <select name="type" class="form-select" required>
                    <option value="setoran">Uang Masuk (Setoran)</option>
                    <option value="penarikan">Uang Keluar (Penarikan / Pembelian Hewan)</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Nominal (Rp)</label>
                <input type="number" name="amount" class="form-control" required min="1">
            </div>
            <div class="mb-3">
                <label>Tanggal</label>
                <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div class="mb-3">
                <label>Foto Bukti Transfer (Opsional)</label>
                <input type="file" name="proof" class="form-control" accept="image/jpeg,image/png,image/jpg">
                <small class="text-muted">Upload jika jamaah mengirimkan bukti via WhatsApp ke Admin.</small>
            </div>
            <div class="mb-3">
                <label>Keterangan Tambahan</label>
                <input type="text" name="description" class="form-control" placeholder="Cth: Cicilan Bulan 1">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-success">Simpan & Kirim WA</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Settings -->
<div class="modal fade" id="modalSettings" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-secondary text-white">
        <h5 class="modal-title">Pengaturan Profil</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
            <div class="mb-3">
                <label>Logo Tabqur (Opsional)</label>
                <input type="file" name="logo_tabqur" class="form-control" accept="image/*">
                @if(\App\Models\Setting::get('logo_tabqur'))
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . \App\Models\Setting::get('logo_tabqur')) }}" alt="Logo Tabqur" class="img-thumbnail" style="max-height: 60px">
                    </div>
                @endif
            </div>
            <div class="mb-3">
                <label>Logo DKM (Opsional)</label>
                <input type="file" name="logo_dkm" class="form-control" accept="image/*">
                @if(\App\Models\Setting::get('logo_dkm'))
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . \App\Models\Setting::get('logo_dkm')) }}" alt="Logo DKM" class="img-thumbnail" style="max-height: 60px">
                    </div>
                @endif
            </div>
            <div class="mb-3">
                <label>Nama Masjid / DKM</label>
                <input type="text" name="dkm_name" class="form-control" value="{{ \App\Models\Setting::get('dkm_name', config('app.name')) }}" required>
            </div>
            <div class="mb-3">
                <label>Alamat Masjid</label>
                <textarea name="dkm_address" class="form-control">{{ \App\Models\Setting::get('dkm_address') }}</textarea>
            </div>
            <div class="mb-3">
                <label>Nomor Rekening Penampungan (Bank & No Rek)</label>
                <input type="text" name="bank_account" class="form-control" value="{{ \App\Models\Setting::get('bank_account', 'BSI 1234567890 a.n DKM Masjid') }}" required>
            </div>
            <div class="mb-3">
                <label>Instruksi Tambahan Setoran / Kode Unik</label>
                <textarea name="unique_code_instruction" class="form-control" placeholder="Cth: Tambahkan dengan kode unik 077">{{ \App\Models\Setting::get('unique_code_instruction', 'Tambahkan dengan kode unik 077') }}</textarea>
                <small class="text-muted">Keterangan ini akan muncul saat jamaah melakukan setor tabungan.</small>
            </div>
            <div class="mb-3">
                <label>Nomor WA Admin Utama</label>
                <input type="text" name="admin_phone" class="form-control" value="{{ \App\Models\Setting::get('admin_phone', env('ADMIN_PHONE')) }}">
                <small class="text-muted">Untuk menerima notifikasi transfer dari jamaah.</small>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-success">Simpan Pengaturan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
