<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\LoginTrackingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(
        private readonly LoginTrackingService $loginTrackingService
    ) {
    }

    public function showSignIn(): View
    {
        return view('pages.auth.signin', ['title' => 'Sign In']);
    }

    public function showSignUp(): View
    {
        return view('pages.auth.signup', ['title' => 'Sign Up']);
    }

    public function signIn(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'Email ou mot de passe invalide.',
            ]);
        }

        $request->session()->regenerate();
        if ($user = $request->user()) {
            $this->loginTrackingService->onSuccessfulLogin($user);
        }

        return redirect()->route('dashboard');
    }

    public function signUp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'fname' => ['required', 'string', 'max:255'],
            'lname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $name = trim($validated['fname'].' '.$validated['lname']);

        $user = User::query()->create([
            'name' => $name,
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $this->loginTrackingService->onSuccessfulLogin($user);

        return redirect()->route('dashboard');
    }
}
