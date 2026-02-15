<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\EventTrackingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        $user = $this->resolveUser();
        $user->loadMissing('rank');

        return view('pages.profile', [
            'title' => 'Profile',
            'user' => $user,
        ]);
    }

    public function update(Request $request, EventTrackingService $eventTrackingService): RedirectResponse
    {
        $user = $this->resolveUser();

        $validated = $request->validate([
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:50'],
            'bio' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:100'],
            'city_state' => ['nullable', 'string', 'max:150'],
            'postal_code' => ['nullable', 'string', 'max:50'],
            'tax_id' => ['nullable', 'string', 'max:100'],
            'facebook' => ['nullable', 'url', 'max:255'],
            'x_url' => ['nullable', 'url', 'max:255'],
            'linkedin' => ['nullable', 'url', 'max:255'],
            'instagram' => ['nullable', 'url', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ]);

        $payload = [];

        foreach ([
            'email',
            'phone',
            'bio',
            'country',
            'city_state',
            'postal_code',
            'tax_id',
            'facebook',
            'x_url',
            'linkedin',
            'instagram',
        ] as $field) {
            if ($request->exists($field)) {
                $payload[$field] = $validated[$field] ?? null;
            }
        }

        if ($request->exists('first_name') || $request->exists('last_name')) {
            $firstName = trim((string) ($validated['first_name'] ?? ''));
            $lastName = trim((string) ($validated['last_name'] ?? ''));
            $fullName = trim($firstName.' '.$lastName);

            if ($fullName !== '') {
                $payload['name'] = $fullName;
            }
        }

        if ($request->hasFile('avatar')) {
            $previousAvatarPath = $this->extractPublicStoragePath($user->avatar_url);
            if ($previousAvatarPath) {
                Storage::disk('public')->delete($previousAvatarPath);
            }

            $storedPath = $request->file('avatar')->store('profile-avatars', 'public');
            $payload['avatar_url'] = Storage::url($storedPath);
        }

        if (!empty($payload)) {
            $user->update($payload);
            $eventTrackingService->trackAction($user, 'onboarding_step_completed', [
                'step' => 'profile_updated',
            ]);
        }

        return back()->with('status', 'profile-updated');
    }

    private function resolveUser(): User
    {
        if ($authUser = auth()->user()) {
            return $authUser;
        }

        $existingUser = User::query()->first();
        if ($existingUser) {
            return $existingUser;
        }

        return User::query()->create([
            'name' => 'Default User',
            'email' => 'default.user@example.com',
            'password' => Str::password(16),
        ]);
    }

    private function extractPublicStoragePath(?string $avatarUrl): ?string
    {
        if (!$avatarUrl) {
            return null;
        }

        $path = parse_url($avatarUrl, PHP_URL_PATH) ?: $avatarUrl;

        if (Str::startsWith($path, '/storage/')) {
            return ltrim(Str::after($path, '/storage/'), '/');
        }

        return null;
    }
}
