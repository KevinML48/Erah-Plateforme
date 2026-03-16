<?php

namespace App\Http\Controllers\Auth;

use App\Application\Actions\Auth\RegisterUserAction;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(
        Request $request,
        RegisterUserAction $registerUserAction
    ): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $user = $registerUserAction->execute(
            payload: $request->only(['name', 'email', 'password']),
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );

        event(new Registered($user));

        Auth::login($user, $request->boolean('remember', true));

        return redirect()->to(url('/'))
            ->with('success', 'Compte cree avec succes.');
    }
}
