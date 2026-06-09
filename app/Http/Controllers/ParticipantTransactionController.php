<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Participant;
use App\Services\FonnteService;
use Illuminate\Support\Facades\Auth;

class ParticipantTransactionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1000',
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $participant = Participant::where('user_id', Auth::id())->first();
        if (!$participant) {
            return back()->with('error', 'Peserta tidak ditemukan.');
        }

        // Upload image
        $path = $request->file('proof')->store('proofs', 'public');

        $trx = Transaction::create([
            'participant_id' => $participant->id,
            'type' => 'setoran',
            'amount' => $request->amount,
            'date' => $request->date,
            'description' => $request->description,
            'status' => 'pending',
            'proof_path' => $path,
        ]);

        // Send WA to Admin
        $adminPhone = \App\Models\Setting::get('admin_phone', env('ADMIN_PHONE'));
        if ($adminPhone) {
            $msg = "⚠️ *Notifikasi Laporan Setoran Baru* ⚠️\n\n";
            $msg .= "Jamaah: *" . Auth::user()->name . "*\n";
            $msg .= "Nominal: *Rp " . number_format($request->amount, 0, ',', '.') . "*\n";
            $msg .= "Tgl: " . \Carbon\Carbon::parse($request->date)->format('d/m/Y') . "\n";
            $msg .= "Keterangan: " . ($request->description ?? '-') . "\n\n";
            $msg .= "Mohon segera cek dasbor Admin untuk memverifikasi bukti transfer dan menyetujui setoran ini.";
            
            FonnteService::sendMessage($adminPhone, $msg);
        }

        return back()->with('success', 'Laporan setoran berhasil dikirim. Menunggu verifikasi Bendahara.');
    }
}
