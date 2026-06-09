<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participant;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class ParticipantDashboardController extends Controller
{
    public function index()
    {
        $participant = Participant::where('user_id', Auth::id())->first();
        if (!$participant) {
            return view('peserta.dashboard', ['error' => 'Anda belum terdaftar sebagai peserta qurban.']);
        }

        $transactions = $participant->transactions()->orderBy('date', 'desc')->get();
        $totalSavings = $transactions->where('type', 'setoran')->sum('amount') - $transactions->where('type', 'penarikan')->sum('amount');
        
        $progress = $participant->target_amount > 0 ? ($totalSavings / $participant->target_amount) * 100 : 0;
        $progress = min(100, max(0, $progress));

        return view('peserta.dashboard', compact('participant', 'transactions', 'totalSavings', 'progress'));
    }
}
