<?php

namespace App\Actions\Auth;

use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Laravel\Fortify\Contracts\ResetsUserPasswords as ResetsUserPasswordsContract;

class ResetsUserPasswords implements ResetsUserPasswordsContract
{
    public function reset($user, array $input)
    {
        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();

        // Optionally, you can fire the password reset event here
        // event(new PasswordReset($user));
    }
}
