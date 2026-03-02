<?php

namespace App\Http\Controllers\Web;

use App\Application\Actions\Auth\RegisterUserAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthPageController extends Controller
{
    public function showLogin(): View
    {
        return view('pages.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ];

        if (! Auth::attempt($credentials, $request->boolean('remember', true))) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->with('error', 'Identifiants invalides.');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Connexion reussie.');
    }

    public function showRegister(): View
    {
        return view('pages.auth.register');
    }

    public function register(Request $request, RegisterUserAction $registerUserAction): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $user = $registerUserAction->execute(
            payload: $validated,
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );

        Auth::login($user, $request->boolean('remember', true));
        $request->session()->regenerate();

        return redirect()->route('onboarding')
            ->with('success', 'Compte cree avec succes.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', 'Deconnexion effectuee.');
    }
}
