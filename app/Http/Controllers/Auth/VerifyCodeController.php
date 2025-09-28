<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class VerifyCodeController extends Controller
{
    public function showVerificationForm(Request $request)
    {
        $userId = $request->session()->get('verify_user_id');
        if (!$userId) {
            return redirect()->route('login');
        }
        return view('auth.verify-code');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);
        $userId = $request->session()->get('verify_user_id');
        if (!$userId) {
            return redirect()->route('login');
        }
        $user = User::find($userId);
        if (!$user || $user->email_verification_code !== $request->code) {
            return back()->with('error', 'Invalid verification code.');
        }
        $user->is_email_verified = true;
        $user->email_verification_code = null;
        $user->save();
        Auth::login($user);
        $request->session()->forget('verify_user_id');
        return redirect()->route('dashboard');
    }
}
