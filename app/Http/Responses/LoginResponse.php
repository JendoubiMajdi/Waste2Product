<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        // Redirect admin users to admin dashboard
        if (Auth::user()->role === 'admin') {
            return $request->wantsJson()
                ? new JsonResponse('', 204)
                : redirect()->intended(route('admin.dashboard'));
        }

        // Redirect other users to regular home
        return $request->wantsJson()
            ? new JsonResponse('', 204)
            : redirect()->intended(config('fortify.home'));
    }
}
