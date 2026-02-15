@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="User Profile" />
    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-error-300 bg-error-50 px-4 py-3 text-sm text-error-700">
            {{ $errors->first() }}
        </div>
    @endif
    @if (session('status') === 'profile-updated')
        <div class="mb-4 rounded-lg border border-success-300 bg-success-50 px-4 py-3 text-sm text-success-700">
            Profile updated successfully.
        </div>
    @endif
    <div class="rounded-2xl border border-gray-200 bg-white p-3 sm:p-5 dark:border-gray-800 dark:bg-white/[0.03] lg:p-6">
        <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-7">Profile</h3>
        <x-profile.profile-card :user="$user" />
        <div class="mb-6">
            <x-rank.available-ranks :user="$user" />
        </div>
        <x-profile.personal-info-card :user="$user" />
        <x-profile.address-card :user="$user" />

        <div class="mt-6 rounded-2xl border border-error-500/30 bg-error-500/10 p-4 sm:p-5">
            <h4 class="text-base font-semibold text-error-100">Zone dangereuse</h4>
            <p class="mt-1 text-sm text-error-200/90">
                Supprimer ton compte est definitif. Toutes tes donnees seront perdues.
            </p>
            @php
                $hasOAuthLinked = !empty($user?->google_id) || !empty($user?->discord_id);
            @endphp

            <form method="POST" action="{{ route('profile.destroy') }}" class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end">
                @csrf
                @method('DELETE')
                @if ($hasOAuthLinked)
                    <div class="w-full sm:max-w-xs">
                        <label class="mb-1.5 block text-xs font-medium text-error-200">Confirmation (OAuth)</label>
                        <input
                            type="text"
                            name="delete_confirmation"
                            required
                            class="h-11 w-full rounded-lg border border-error-400/40 bg-gray-900/60 px-3 text-sm text-white placeholder:text-gray-400 focus:border-error-400 focus:outline-none"
                            placeholder="Tape SUPPRIMER"
                        />
                        <p class="mt-1 text-xs text-error-200/80">Compte Google/Discord detecte: saisis <strong>SUPPRIMER</strong>.</p>
                    </div>
                @else
                    <div class="w-full sm:max-w-xs">
                        <label class="mb-1.5 block text-xs font-medium text-error-200">Mot de passe de confirmation</label>
                        <input
                            type="password"
                            name="password"
                            required
                            class="h-11 w-full rounded-lg border border-error-400/40 bg-gray-900/60 px-3 text-sm text-white placeholder:text-gray-400 focus:border-error-400 focus:outline-none"
                            placeholder="Ton mot de passe"
                        />
                    </div>
                @endif
                <button
                    type="submit"
                    onclick="return confirm('Confirmer la suppression definitive du compte ?')"
                    class="inline-flex h-11 items-center justify-center rounded-lg bg-error-600 px-4 text-sm font-semibold text-white hover:bg-error-500"
                >
                    Supprimer mon compte
                </button>
            </form>
        </div>
    </div>

    @if (request()->query('edit') === 'info')
        <script>
            window.addEventListener('load', function () {
                window.dispatchEvent(new CustomEvent('open-profile-info-modal'));
            });
        </script>
    @endif
@endsection
