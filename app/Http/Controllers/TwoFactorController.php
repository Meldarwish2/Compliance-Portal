<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class TwoFactorController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
        ]);

        $user = auth()->user();

        // Verify the code
        if (Hash::check($request->otp, $user->two_factor_secret)) {
            // Clear the 2FA secret
            $user->two_factor_secret = null;
            $user->save();

            // Redirect to the intended page
            return redirect()->intended('/dashboard')->with('success', '2FA verified successfully.');
        }

        return redirect()->back()->with('error', 'Invalid 2FA code.');
    }

    public function resend(): \Illuminate\Http\RedirectResponse
    {
        try {

        // Generate a random 6-digit code
        $code = random_int(100000, 999999);
        $user = auth()->user();
        $user->two_factor_secret = bcrypt($code);
        $user->save();
        Mail::to($user->email)->send(new \App\Mail\TwoFactorCodeMail($code));
        return redirect()->back()->with('success', '2FA code Resend successfully.');
        }
        catch (\Exception $e) {
            return redirect()->back()->with('error', 'Unable to resend 2FA code.');
        }
    }
}
