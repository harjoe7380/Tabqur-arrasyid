<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendQurbanReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qurban:remind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim pengingat otomatis ke jamaah untuk menyetor tabungan bulanan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $participants = \App\Models\Participant::with('user', 'transactions')->get();

        $currentMonth = date('m');
        $currentYear = date('Y');
        
        $count = 0;

        foreach ($participants as $p) {
            if (!$p->user->no_hp) continue;

            // Hitung total terkumpul
            $verified = $p->transactions->where('status', 'verified');
            $terkumpul = $verified->where('type', 'setoran')->sum('amount') - $verified->where('type', 'penarikan')->sum('amount');
            
            if ($terkumpul >= $p->target_amount) continue; // Sudah lunas

            // Cek apakah sudah setor bulan ini
            $hasPaidThisMonth = $verified->where('type', 'setoran')->filter(function($t) use ($currentMonth, $currentYear) {
                return date('m', strtotime($t->date)) == $currentMonth && date('Y', strtotime($t->date)) == $currentYear;
            })->count() > 0;

            if (!$hasPaidThisMonth) {
                $kekurangan = $p->target_amount - $terkumpul;
                $msg = "Assalamu'alaikum Bpk/Ibu *" . $p->user->name . "*,\n\n";
                $msg .= "*(Pesan Otomatis)*\n";
                $msg .= "Mengingatkan untuk setoran tabungan Kurban bulan ini.\n\n";
                $msg .= "Terkumpul saat ini: *Rp " . number_format($terkumpul, 0, ',', '.') . "*\n";
                $msg .= "Kekurangan: Rp " . number_format($kekurangan, 0, ',', '.') . "\n\n";
                $msg .= "Silakan login ke portal atau hubungi Bendahara DKM. Jazakumullah khairan.\n\n- " . \App\Models\Setting::get('dkm_name', config('app.name'));

                \App\Services\FonnteService::sendMessage($p->user->no_hp, $msg);
                $count++;
            }
        }

        $this->info("Berhasil mengirim reminder ke $count jamaah.");
    }
}
