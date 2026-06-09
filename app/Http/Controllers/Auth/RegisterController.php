<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected function redirectTo()
    {
        return '/peserta/dashboard';
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'no_hp' => ['required', 'string', 'max:20'],
            'target_amount' => ['required', 'numeric', 'min:100000'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @return User
     */
    protected function create(array $data)
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'peserta',
                'no_hp' => $data['no_hp'],
            ]);

            \App\Models\Participant::create([
                'user_id' => $user->id,
                'target_amount' => $data['target_amount'],
            ]);

            if ($user->no_hp) {
                $msg = "Assalamu'alaikum Bpk/Ibu *" . $user->name . "*,\n\n";
                $msg .= "Pendaftaran Anda sebagai peserta tabungan qurban telah berhasil.\n\n";
                $msg .= "Berikut adalah detail akun Anda untuk mengakses sistem:\n";
                $msg .= "🔗 *Link Akses*: " . url('/') . "\n";
                $msg .= "👤 *Email*: " . $user->email . "\n";
                $msg .= "🔑 *Password*: " . $data['password'] . "\n\n";
                $msg .= "Silakan simpan informasi ini untuk keperluan login Anda di masa mendatang.\n\n";
                $msg .= "Terima kasih,\nPengurus " . \App\Models\Setting::get('dkm_name', 'Tabungan Qurban');

                \App\Services\FonnteService::sendMessage($user->no_hp, $msg);
            }

            // Send WA Notification to Admin
            $adminPhone = \App\Models\Setting::get('admin_phone', env('ADMIN_PHONE'));
            if ($adminPhone) {
                $adminMsg = "⚠️ *Pendaftaran Jamaah Baru* ⚠️\n\n";
                $adminMsg .= "Ada pendaftar baru di sistem Tabqur:\n";
                $adminMsg .= "Nama: *" . $user->name . "*\n";
                $adminMsg .= "Target Qurban: *Rp " . number_format($data['target_amount'], 0, ',', '.') . "*\n";
                $adminMsg .= "No WA: " . $user->no_hp . "\n\n";
                $adminMsg .= "Mohon cek Dasbor Admin untuk info lebih lanjut.";

                \App\Services\FonnteService::sendMessage($adminPhone, $adminMsg);
            }

            return $user;
        });
    }
}
