<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TwoFactorController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate([
            'two_factor_code' => 'required|numeric',
        ]);

        $user = auth()->user();

        // Verify the code
        if (Hash::check($request->two_factor_code, $user->two_factor_secret)) {
            // Clear the 2FA secret
            $user->two_factor_secret = null;
            $user->save();

            // Redirect to the intended page
            return redirect()->intended('/dashboard')->with('success', '2FA verified successfully.');
        }

        return redirect()->back()->with('error', 'Invalid 2FA code.');
    }
}
