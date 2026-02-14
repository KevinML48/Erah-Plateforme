@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Admin Rewards" />

    <div class="space-y-6">
        @if (session('status'))
            <div class="rounded-xl border border-success-500/20 bg-success-500/10 px-4 py-3 text-sm text-success-300">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-error-500/20 bg-error-500/10 px-4 py-3 text-sm text-error-300">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Creer une reward</h3>
            <form method="POST" action="{{ route('admin.rewards.store') }}" class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-2">
                @csrf
                <input name="name" placeholder="Nom" class="h-11 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90" required />
                <input name="slug" placeholder="slug-reward" class="h-11 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90" required />
                <input type="number" name="points_cost" placeholder="Points cost" class="h-11 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90" required />
                <input type="number" name="stock" placeholder="Stock (vide = illimite)" class="h-11 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90" />
                <input name="image_url" placeholder="Image URL" class="h-11 rounded-lg border border-gray-300 bg-white px-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 md:col-span-2" />
                <textarea name="description" rows="3" placeholder="Description" class="rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 md:col-span-2"></textarea>
                <button type="submit" class="inline-flex h-11 items-center justify-center rounded-lg bg-brand-500 px-5 text-sm font-medium text-white hover:bg-brand-600 md:col-span-2">
                    Creer
                </button>
            </form>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Catalogue</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-800">
                            <th class="px-5 py-3 text-left text-xs text-gray-500 dark:text-gray-400">Nom</th>
                            <th class="px-5 py-3 text-left text-xs text-gray-500 dark:text-gray-400">Slug</th>
                            <th class="px-5 py-3 text-left text-xs text-gray-500 dark:text-gray-400">Cost</th>
                            <th class="px-5 py-3 text-left text-xs text-gray-500 dark:text-gray-400">Stock</th>
                            <th class="px-5 py-3 text-left text-xs text-gray-500 dark:text-gray-400">Active</th>
                            <th class="px-5 py-3 text-right text-xs text-gray-500 dark:text-gray-400">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rewards as $reward)
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <td class="px-5 py-4 text-sm text-gray-800 dark:text-white/90">{{ $reward->name }}</td>
                                <td class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $reward->slug }}</td>
                                <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ number_format((int) $reward->points_cost) }}</td>
                                <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $reward->stock === null ? 'illimite' : (int) $reward->stock }}</td>
                                <td class="px-5 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $reward->is_active ? 'oui' : 'non' }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <form method="POST" action="{{ route('admin.rewards.update', $reward) }}">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="is_active" value="{{ $reward->is_active ? 0 : 1 }}">
                                            <button type="submit" class="rounded-lg border border-gray-300 px-3 py-2 text-xs text-gray-700 dark:border-gray-700 dark:text-gray-300">
                                                {{ $reward->is_active ? 'Desactiver' : 'Activer' }}
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.rewards.delete', $reward) }}" onsubmit="return confirm('Supprimer cette reward ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-lg border border-error-500/40 px-3 py-2 text-xs text-error-400">
                                                Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-5">
                {{ $rewards->links() }}
            </div>
        </div>
    </div>
@endsection

