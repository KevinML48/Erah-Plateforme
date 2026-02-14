@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Admin Matches" />

    <div class="space-y-6">
        @if (session('status'))
            <x-ui.alert variant="success" title="Succes" :message="session('status')" />
        @endif

        @if ($errors->any())
            <x-ui.alert variant="error" title="Erreur" :message="$errors->first()" />
        @endif

        <x-common.component-card title="Creer un match" desc="Creation rapide d'un match pronosticable">
            <form method="POST" action="{{ route('admin.matches.store') }}" class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                @csrf

                <div>
                    <label class="mb-2 block text-sm text-gray-700 dark:text-gray-300">Jeu</label>
                    <input name="game" type="text" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 placeholder:text-gray-400 dark:border-gray-700 dark:text-white/90 dark:placeholder:text-gray-500" placeholder="VALORANT" />
                </div>

                <div>
                    <label class="mb-2 block text-sm text-gray-700 dark:text-gray-300">Titre</label>
                    <input name="title" type="text" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 placeholder:text-gray-400 dark:border-gray-700 dark:text-white/90 dark:placeholder:text-gray-500" placeholder="ERAH vs Team X" />
                </div>

                <div>
                    <label class="mb-2 block text-sm text-gray-700 dark:text-gray-300">Debut</label>
                    <input name="starts_at" type="datetime-local" required class="admin-datetime-input h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 dark:border-gray-700 dark:text-white/90" />
                </div>

                <div>
                    <label class="mb-2 block text-sm text-gray-700 dark:text-gray-300">Status</label>
                    <select name="status" class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                        @foreach ($statuses as $status)
                            <option value="{{ $status->value }}" @selected($status->value === 'DRAFT') style="color:#101828;background-color:#ffffff;">{{ $status->value }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm text-gray-700 dark:text-gray-300">Reward points</label>
                    <input name="points_reward" type="number" min="0" value="100" required class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 placeholder:text-gray-400 dark:border-gray-700 dark:text-white/90 dark:placeholder:text-gray-500" />
                </div>

                <div class="flex items-end">
                    <button type="submit" class="inline-flex h-11 w-full items-center justify-center rounded-lg bg-brand-500 px-4 text-sm font-medium text-white lg:w-auto">
                        Creer le match
                    </button>
                </div>
            </form>
        </x-common.component-card>

        <x-common.component-card title="Matchs" desc="Gestion des pronostics et validation des resultats">
            <div class="space-y-3 md:hidden">
                @foreach ($matches as $match)
                    <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.03]">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $match->title }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $match->game }} - {{ $match->starts_at?->format('d/m/Y H:i') }}</p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Status: {{ $match->status?->value }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Resultat: {{ $match->result?->value ?? 'N/A' }}</p>
                            </div>
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">{{ $match->predictions_count }} pronostics</span>
                        </div>

                        <div class="mt-3 grid grid-cols-2 gap-2">
                            <form method="POST" action="{{ route('admin.matches.open', $match) }}">@csrf<button class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-700 dark:border-gray-700 dark:text-white/90">Open</button></form>
                            <form method="POST" action="{{ route('admin.matches.lock', $match) }}">@csrf<button class="w-full rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-700 dark:border-gray-700 dark:text-white/90">Lock</button></form>
                            <form method="POST" action="{{ route('admin.matches.complete', $match) }}" class="col-span-2 flex gap-2">
                                @csrf
                                <select name="result" class="h-9 w-full rounded-lg border border-gray-300 bg-transparent px-2 text-xs text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                    @foreach ($results as $result)
                                        <option value="{{ $result->value }}" style="color:#101828;background-color:#ffffff;">{{ $result->value }}</option>
                                    @endforeach
                                </select>
                                <button class="rounded-lg bg-brand-500 px-3 py-2 text-xs font-medium text-white">Complete</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="hidden md:block overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="w-full min-w-[980px]">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-3 text-left text-theme-xs text-gray-500 dark:text-gray-400">Match</th>
                                <th class="px-5 py-3 text-left text-theme-xs text-gray-500 dark:text-gray-400">Starts at</th>
                                <th class="px-5 py-3 text-left text-theme-xs text-gray-500 dark:text-gray-400">Status</th>
                                <th class="px-5 py-3 text-left text-theme-xs text-gray-500 dark:text-gray-400">Result</th>
                                <th class="px-5 py-3 text-left text-theme-xs text-gray-500 dark:text-gray-400">Predictions</th>
                                <th class="px-5 py-3 text-right text-theme-xs text-gray-500 dark:text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($matches as $match)
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="px-5 py-4">
                                        <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $match->title }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $match->game }}</p>
                                    </td>
                                    <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $match->starts_at?->format('d/m/Y H:i') }}</td>
                                    <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $match->status?->value }}</td>
                                    <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $match->result?->value ?? 'N/A' }}</td>
                                    <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $match->predictions_count }}</td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <form method="POST" action="{{ route('admin.matches.open', $match) }}">@csrf<button class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs text-gray-700 dark:border-gray-700 dark:text-white/90">Open</button></form>
                                            <form method="POST" action="{{ route('admin.matches.lock', $match) }}">@csrf<button class="rounded-lg border border-gray-300 px-3 py-1.5 text-xs text-gray-700 dark:border-gray-700 dark:text-white/90">Lock</button></form>
                                            <form method="POST" action="{{ route('admin.matches.complete', $match) }}" class="flex items-center gap-2">
                                                @csrf
                                                <select name="result" class="h-8 rounded-lg border border-gray-300 bg-transparent px-2 text-xs text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                                    @foreach ($results as $result)
                                                        <option value="{{ $result->value }}" style="color:#101828;background-color:#ffffff;">{{ $result->value }}</option>
                                                    @endforeach
                                                </select>
                                                <button class="rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-medium text-white">Complete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pt-4">
                {{ $matches->withQueryString()->links() }}
            </div>
        </x-common.component-card>
    </div>
@endsection
