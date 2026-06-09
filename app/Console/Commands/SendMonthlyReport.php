<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Participant;
use App\Models\Setting;
use App\Services\FonnteService;

class SendMonthlyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tabqur:monthly-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim laporan bulanan otomatis ke Admin dan Jamaah';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $participants = Participant::with(['user', 'transactions' => function($q) {
            $q->where('status', 'verified');
        }])->get();

        $totalTerkumpulSemua = 0;
        $totalTargetSemua = 0;
        $totalJamaah = $participants->count();

        // 1. Rekap untuk setiap jamaah
        foreach ($participants as $p) {
            $setoran = $p->transactions->where('type', 'setoran')->sum('amount');
            $penarikan = $p->transactions->where('type', 'penarikan')->sum('amount');
            $terkumpul = $setoran - $penarikan;
            
            $totalTerkumpulSemua += $terkumpul;
            $totalTargetSemua += $p->target_amount;

            if ($p->user->no_hp) {
                $msgJamaah = "Assalamu'alaikum Bpk/Ibu *" . $p->user->name . "*,\n\n";
                $msgJamaah .= "Terima kasih banyak telah berpartisipasi dalam program " . Setting::get('dkm_name', config('app.name')) . ".\n\n";
                $msgJamaah .= "Berikut adalah rekap tabungan qurban Anda di awal bulan ini:\n";
                $msgJamaah .= "🎯 Target Qurban: *Rp " . number_format($p->target_amount, 0, ',', '.') . "*\n";
                $msgJamaah .= "💰 Total Terkumpul: *Rp " . number_format($terkumpul, 0, ',', '.') . "*\n";
                
                $kekurangan = max(0, $p->target_amount - $terkumpul);
                if ($kekurangan > 0) {
                    $msgJamaah .= "🔻 Sisa Target: *Rp " . number_format($kekurangan, 0, ',', '.') . "*\n\n";
                    $msgJamaah .= "Ayo, tetap semangat menabung! Sedikit demi sedikit, insya Allah niat qurban Bpk/Ibu akan terwujud. 🚀\n";
                } else {
                    $msgJamaah .= "✨ Alhamdulillah, tabungan Anda sudah memenuhi target qurban!\n\n";
                }
                
                $msgJamaah .= "\nSemoga Allah memberkahi rezeki Bpk/Ibu. Aamiin.\n";

                FonnteService::sendMessage($p->user->no_hp, $msgJamaah);
            }
        }

        // 2. Rekap untuk Admin
        $adminPhone = Setting::get('admin_phone', env('ADMIN_PHONE'));
        if ($adminPhone) {
            $msgAdmin = "📊 *LAPORAN BULANAN TABQUR* 📊\n\n";
            $msgAdmin .= "Assalamu'alaikum Admin, berikut adalah rekapitulasi total tabungan seluruh jamaah saat ini untuk dilaporkan ke pengurus DKM:\n\n";
            $msgAdmin .= "👥 Total Jamaah: *" . $totalJamaah . " Orang*\n";
            $msgAdmin .= "🎯 Total Target Keseluruhan: *Rp " . number_format($totalTargetSemua, 0, ',', '.') . "*\n";
            $msgAdmin .= "💵 *Total Dana Terkumpul: Rp " . number_format($totalTerkumpulSemua, 0, ',', '.') . "*\n\n";
            $msgAdmin .= "Laporan individu juga telah berhasil dikirimkan ke masing-masing jamaah.\n\n";
            $msgAdmin .= "Terima kasih.";

            FonnteService::sendMessage($adminPhone, $msgAdmin);
        }

        $this->info('Laporan bulanan berhasil dikirim.');
    }
}
