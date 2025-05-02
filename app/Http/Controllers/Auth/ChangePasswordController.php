<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ChangePasswordController extends Controller
{
    public function show()
    {
        return view('auth.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'username' => 'required|string|exists:users,username',
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Find the user by username
        $user = User::where('username', $request->username)->first();

        // Check if old password matches
        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors([
                'old_password' => 'Password lama tidak sesuai',
            ]);
        }

        // Update the password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('login')
            ->with('success', 'Password berhasil diubah. Silakan login dengan password baru Anda.');
    }
}