<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participant;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Setting;
use App\Services\FonnteService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $participants = Participant::with('user')->get();
        // Hanya ambil transaksi yang sudah diverifikasi untuk perhitungan saldo
        $verifiedTransactions = Transaction::where('status', 'verified')->get();
        $totalSavings = $verifiedTransactions->where('type', 'setoran')->sum('amount') - $verifiedTransactions->where('type', 'penarikan')->sum('amount');
        
        $transactions = Transaction::with('participant.user')->where('status', 'verified')->orderBy('date', 'desc')->limit(10)->get();
        
        // Transaksi pending untuk diverifikasi
        $pendingTransactions = Transaction::with('participant.user')->where('status', 'pending')->orderBy('created_at', 'asc')->get();

        return view('admin.dashboard', compact('participants', 'transactions', 'totalSavings', 'pendingTransactions'));
    }

    public function storeParticipant(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'no_hp' => 'nullable|string|max:20',
            'target_amount' => 'required|numeric',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password123'),
            'role' => 'peserta',
            'no_hp' => $request->no_hp,
        ]);

        Participant::create([
            'user_id' => $user->id,
            'target_amount' => $request->target_amount,
        ]);

        if ($user->no_hp) {
            $msg = "Assalamu'alaikum Bpk/Ibu *" . $user->name . "*,\n\n";
            $msg .= "Pendaftaran Anda sebagai peserta tabungan kurban telah berhasil.\n\n";
            $msg .= "Berikut adalah detail akun Anda untuk mengakses sistem:\n";
            $msg .= "🔗 *Link Akses*: " . url('/') . "\n";
            $msg .= "👤 *Email*: " . $user->email . "\n";
            $msg .= "🔑 *Password*: password123\n\n";
            $msg .= "Silakan login dan disarankan untuk segera mengubah password default Anda demi keamanan.\n\n";
            $msg .= "Terima kasih,\nPengurus " . config('app.name');

            FonnteService::sendMessage($user->no_hp, $msg);
        }

        return back()->with('success', 'Jamaah berhasil didaftarkan dan notifikasi telah dikirim.');
    }

    public function participants()
    {
        $participants = Participant::with('user', 'transactions')->get();
        return view('admin.participants', compact('participants'));
    }

    public function updateParticipant(Request $request, $id)
    {
        $participant = Participant::findOrFail($id);
        $user = $participant->user;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'no_hp' => 'nullable|string|max:20',
            'target_amount' => 'required|numeric',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
        ]);

        $participant->update([
            'target_amount' => $request->target_amount,
        ]);

        return back()->with('success', 'Data Jamaah berhasil diperbarui.');
    }

    public function destroyParticipant($id)
    {
        $participant = Participant::findOrFail($id);
        if ($participant->user) {
            $participant->user->delete(); // Ini otomatis menghapus participant & transactions karena cascadeOnDelete
        } else {
            $participant->delete();
        }
        return back()->with('success', 'Jamaah beserta histori tabungannya telah dihapus.');
    }

    public function updateSettings(Request $request)
    {
        Setting::set('dkm_name', $request->dkm_name);
        Setting::set('dkm_address', $request->dkm_address);
        Setting::set('bank_account', $request->bank_account);
        Setting::set('admin_phone', $request->admin_phone);

        return back()->with('success', 'Pengaturan DKM berhasil disimpan.');
    }

    public function storeTransaction(Request $request)
    {
        $request->validate([
            'participant_id' => 'required|exists:participants,id',
            'type' => 'required|in:setoran,penarikan',
            'amount' => 'required|numeric|min:1',
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
        ]);

        $trx = Transaction::create($request->all() + ['status' => 'verified']);

        if ($trx->type == 'setoran') {
            $participant = Participant::with('user')->find($trx->participant_id);
            if ($participant && $participant->user->no_hp) {
                // Kalkulasi total tabungan (verified)
                $transactions = Transaction::where('participant_id', $participant->id)->where('status', 'verified')->get();
                $totalSavings = $transactions->where('type', 'setoran')->sum('amount') - $transactions->where('type', 'penarikan')->sum('amount');
                
                $msg = "Assalamu'alaikum Bpk/Ibu *" . $participant->user->name . "*,\n\n";
                $msg .= "Setoran tabungan kurban Anda sebesar *Rp " . number_format($trx->amount, 0, ',', '.') . "* pada tanggal *" . \Carbon\Carbon::parse($trx->date)->format('d/m/Y') . "* telah kami terima.\n";
                if ($trx->description) {
                    $msg .= "Keterangan: " . $trx->description . "\n";
                }
                $msg .= "\n*Total Tabungan Saat Ini: Rp " . number_format($totalSavings, 0, ',', '.') . "*\n";
                $msg .= "Target Kurban: Rp " . number_format($participant->target_amount, 0, ',', '.') . "\n\n";
                $msg .= "Terima kasih,\nPengurus " . config('app.name');

                FonnteService::sendMessage($participant->user->no_hp, $msg);
            }
        }

        return back()->with('success', 'Transaksi berhasil disimpan dan notifikasi telah diproses.');
    }

    public function verifyTransaction(Request $request, Transaction $transaction)
    {
        $request->validate([
            'action' => 'required|in:approve,reject'
        ]);

        if ($request->action === 'approve') {
            $transaction->update(['status' => 'verified']);
            
            // Send WA Receipt
            $participant = Participant::with('user')->find($transaction->participant_id);
            if ($participant && $participant->user->no_hp) {
                $transactions = Transaction::where('participant_id', $participant->id)->where('status', 'verified')->get();
                $totalSavings = $transactions->where('type', 'setoran')->sum('amount') - $transactions->where('type', 'penarikan')->sum('amount');
                
                $msg = "Assalamu'alaikum Bpk/Ibu *" . $participant->user->name . "*,\n\n";
                $msg .= "✅ Laporan Setoran tabungan kurban Anda sebesar *Rp " . number_format($transaction->amount, 0, ',', '.') . "* pada tanggal *" . \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') . "* *TELAH DIVERIFIKASI* oleh Bendahara.\n";
                if ($transaction->description) {
                    $msg .= "Keterangan: " . $transaction->description . "\n";
                }
                $msg .= "\n*Total Tabungan Saat Ini: Rp " . number_format($totalSavings, 0, ',', '.') . "*\n";
                $msg .= "Target Kurban: Rp " . number_format($participant->target_amount, 0, ',', '.') . "\n\n";
                $msg .= "Terima kasih,\nPengurus " . config('app.name');

                FonnteService::sendMessage($participant->user->no_hp, $msg);
            }
            return back()->with('success', 'Setoran berhasil disetujui.');
        } else {
            $transaction->update(['status' => 'rejected']);
            return back()->with('success', 'Setoran telah ditolak.');
        }
    }

    public function report(Request $request)
    {
        $participants = Participant::with('user', 'transactions')->get()->map(function($p) {
            // Hanya kalkulasi transaksi verified
            $verifiedTransactions = $p->transactions->where('status', 'verified');
            $setoran = $verifiedTransactions->where('type', 'setoran')->sum('amount');
            $penarikan = $verifiedTransactions->where('type', 'penarikan')->sum('amount');
            $p->terkumpul = $setoran - $penarikan;
            $p->kekurangan = max(0, $p->target_amount - $p->terkumpul);
            return $p;
        });

        $totalTerkumpul = $participants->sum('terkumpul');
        $totalTarget = $participants->sum('target_amount');
        
        if ($request->export === 'pdf') {
            $pdf = Pdf::loadView('admin.report', compact('participants', 'totalTerkumpul', 'totalTarget'));
            return $pdf->download('Laporan_Keuangan_Kurban_' . date('Ymd') . '.pdf');
        }
        
        if ($request->export === 'csv') {
            $fileName = 'Laporan_Keuangan_Kurban_' . date('Ymd') . '.csv';
            $headers = [
                "Content-type"        => "text/csv",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];
            $columns = ['Nama Jamaah', 'Target Kurban (Rp)', 'Terkumpul (Rp)', 'Kekurangan (Rp)'];

            $callback = function() use($participants, $columns, $totalTerkumpul, $totalTarget) {
                $file = fopen('php://output', 'w');
                fputcsv($file, $columns);

                foreach ($participants as $p) {
                    fputcsv($file, [
                        $p->user->name,
                        $p->target_amount,
                        $p->terkumpul,
                        $p->kekurangan
                    ]);
                }
                
                fputcsv($file, ['TOTAL KESELURUHAN', $totalTarget, $totalTerkumpul, max(0, $totalTarget - $totalTerkumpul)]);
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return view('admin.report', compact('participants', 'totalTerkumpul', 'totalTarget'));
    }
}
