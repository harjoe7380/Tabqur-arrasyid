<?php

namespace App\Http\Controllers;

use App\Models\QurbanGroup;
use App\Models\Participant;
use App\Models\Transaction;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $groups = QurbanGroup::with('participants.user')->get();
        // Jamaah yang lunas tapi belum masuk kelompok
        $participants = Participant::with('user', 'transactions')->whereNull('group_id')->get()->filter(function($p) {
            $verified = $p->transactions->where('status', 'verified');
            $terkumpul = $verified->where('type', 'setoran')->sum('amount') - $verified->where('type', 'penarikan')->sum('amount');
            return $terkumpul >= $p->target_amount;
        });

        return view('admin.groups', compact('groups', 'participants'));
    }

    public function store(Request $request)
    {
        QurbanGroup::create($request->validate([
            'name' => 'required|string|max:255',
            'animal_type' => 'required|string',
            'purchase_price' => 'nullable|numeric'
        ]));
        return back()->with('success', 'Kelompok kurban berhasil dibuat.');
    }

    public function assignParticipant(Request $request, QurbanGroup $group)
    {
        $request->validate(['participant_id' => 'required|exists:participants,id']);
        $participant = Participant::find($request->participant_id);
        $participant->update(['group_id' => $group->id]);
        
        return back()->with('success', 'Jamaah berhasil dimasukkan ke kelompok ' . $group->name);
    }
    
    public function removeParticipant(Participant $participant)
    {
        $participant->update(['group_id' => null]);
        return back()->with('success', 'Jamaah dikeluarkan dari kelompok.');
    }

    public function updatePrice(Request $request, QurbanGroup $group)
    {
        $group->update(['purchase_price' => $request->purchase_price]);
        return back()->with('success', 'Harga beli berhasil diperbarui. Kembalian telah dikalkulasi ulang.');
    }
}
