@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Admin Points Adjustment" />

    <div class="space-y-6">
        <x-common.component-card title="Ajustement manuel des points" desc="Operation auditee, raison obligatoire">
            @if (session('status'))
                <x-ui.alert variant="success" title="Succes" :message="session('status')" />
            @endif

            @if ($errors->any())
                <x-ui.alert variant="error" title="Erreur de validation" :message="$errors->first()" />
            @endif

            <form method="POST" action="{{ route('admin.points.store') }}" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm text-gray-700 dark:text-gray-300">Email utilisateur</label>
                        <input
                            type="email"
                            name="email"
                            required
                            value="{{ old('email') }}"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90"
                            placeholder="user@example.com"
                        />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm text-gray-700 dark:text-gray-300">Montant (+/-)</label>
                        <input
                            type="number"
                            name="amount"
                            required
                            value="{{ old('amount') }}"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90"
                            placeholder="Ex: 500 ou -300"
                        />
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm text-gray-700 dark:text-gray-300">Raison</label>
                    <textarea
                        name="reason"
                        required
                        rows="3"
                        class="w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none dark:border-gray-700 dark:text-white/90"
                        placeholder="Explique pourquoi cet ajustement est applique."
                    >{{ old('reason') }}</textarea>
                </div>

                <div class="pt-2">
                    <x-ui.button type="submit">Appliquer l'ajustement</x-ui.button>
                </div>
            </form>
        </x-common.component-card>
    </div>
@endsection

