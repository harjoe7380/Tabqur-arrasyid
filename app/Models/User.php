<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'no_hp',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function participant()
    {
        return $this->hasOne(Participant::class);
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        // 1. Send via WhatsApp if phone number exists
        if (!empty($this->no_hp)) {
            $resetUrl = url(route('password.reset', [
                'token' => $token,
                'email' => $this->getEmailForPasswordReset(),
            ], false));
            
            $dkmName = \App\Models\Setting::get('dkm_name', config('app.name'));
            
            $message = "🔐 *PERMINTAAN RESET PASSWORD*\n\n";
            $message .= "Halo {$this->name},\n";
            $message .= "Kami menerima permintaan untuk mengatur ulang kata sandi akun Tabqur Anda di *{$dkmName}*.\n\n";
            $message .= "Silakan klik tautan di bawah ini untuk membuat password baru:\n";
            $message .= "{$resetUrl}\n\n";
            $message .= "_Jika Anda tidak merasa meminta reset password, abaikan pesan ini._";

            \App\Services\FonnteService::sendMessage($this->no_hp, $message);
        }

        // 2. Send via Email (Default Laravel behavior)
        $this->notify(new \Illuminate\Auth\Notifications\ResetPassword($token));
    }
}
