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
    </div>

    @if (request()->query('edit') === 'info')
        <script>
            window.addEventListener('load', function () {
                window.dispatchEvent(new CustomEvent('open-profile-info-modal'));
            });
        </script>
    @endif
@endsection
