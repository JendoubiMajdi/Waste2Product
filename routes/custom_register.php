<?php

use Illuminate\Http\Request;
use App\Actions\Fortify\CreateNewUser;

Route::post('/register', function (Request $request) {
    $input = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'password' => ['required', 'string', 'confirmed', 'min:8'],
    ]);
    app(CreateNewUser::class)->create($input);
    return redirect()->route('login')->with('registration_success', true);
})->name('register');
