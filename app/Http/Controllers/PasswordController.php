<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('password_error', 'Password lama tidak cocok dengan catatan kami!');
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('password_success', 'Password berhasil diubah!');
    }
}
