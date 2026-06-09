<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Setting;
use App\Services\FonnteService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function downloadPdf($id)
    {
        $transaction = Transaction::with('participant.user')->findOrFail($id);

        $data = [
            'transaction' => $transaction,
            'dkm_name' => Setting::get('dkm_name', config('app.name')),
            'dkm_address' => Setting::get('dkm_address', 'Alamat belum diatur'),
            'admin_phone' => Setting::get('admin_phone', env('ADMIN_PHONE')),
        ];

        $pdf = Pdf::loadView('admin.receipt_pdf', $data);
        return $pdf->download('Struk_Setoran_' . $transaction->participant->user->name . '_' . date('Ymd', strtotime($transaction->date)) . '.pdf');
    }

    public function resendWa($id)
    {
        $transaction = Transaction::with('participant.user')->findOrFail($id);
        $participant = $transaction->participant;

        if (!$participant->user->no_hp) {
            return back()->withErrors(['error' => 'Jamaah ini tidak memiliki Nomor WhatsApp.']);
        }

        // Kalkulasi saldo terverifikasi
        $transactions = Transaction::where('participant_id', $participant->id)->where('status', 'verified')->get();
        $totalSavings = $transactions->where('type', 'setoran')->sum('amount') - $transactions->where('type', 'penarikan')->sum('amount');
        
        $msg = "Assalamu'alaikum Bpk/Ibu *" . $participant->user->name . "*,\n\n";
        $msg .= "*(Kirim Ulang Struk)*\n";
        $msg .= "Setoran tabungan qurban Anda sebesar *Rp " . number_format($transaction->amount, 0, ',', '.') . "* pada tanggal *" . \Carbon\Carbon::parse($transaction->date)->format('d/m/Y') . "* *TELAH DIVERIFIKASI*.\n";
        if ($transaction->description) {
            $msg .= "Keterangan: " . $transaction->description . "\n";
        }
        $msg .= "\n*Total Tabungan Saat Ini: Rp " . number_format($totalSavings, 0, ',', '.') . "*\n";
        $msg .= "Target Qurban: Rp " . number_format($participant->target_amount, 0, ',', '.') . "\n\n";
        $msg .= "Terima kasih,\nPengurus " . Setting::get('dkm_name', config('app.name'));

        $sent = FonnteService::sendMessage($participant->user->no_hp, $msg);

        if ($sent) {
            return back()->with('success', 'Struk WhatsApp berhasil dikirim ulang ke ' . $participant->user->name);
        } else {
            return back()->withErrors(['error' => 'Gagal mengirim WhatsApp. Periksa koneksi internet atau token Fonnte Anda.']);
        }
    }
}
