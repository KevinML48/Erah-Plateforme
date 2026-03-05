@extends('marketing.layouts.template')

@section('title', 'Manage Match | Admin ERAH')
@section('meta_description', 'Console detaillee de pilotage d un match: statut, resultat et settlement.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
@endsection

@section('content')
    @php
        $teamA = $match->team_a_name ?? $match->home_team ?? 'Team A';
        $teamB = $match->team_b_name ?? $match->away_team ?? 'Team B';
        $currentStatus = (string) ($match->status ?? '-');
        $currentResult = (string) ($match->result ?? '');
    @endphp

    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'ERAH Control Center',
        'heroTitle' => 'Manage Match #'.$match->id,
        'heroDescription' => $teamA.' vs '.$teamB,
        'heroMaskDescription' => 'Statut, resultat et settlement idempotent.',
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">{{ $teamA }} vs {{ $teamB }}</h2>
                            <p class="max-width-700 tt-anim-fadeinup text-gray">Page de commande match avec actions critiques securisees.</p>
                        </div>

                        <div class="adm-row-actions margin-bottom-20">
                            <a href="{{ route('admin.matches.edit', $match->id) }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                <span data-hover="Edit">Edit</span>
                            </a>
                            <a href="{{ route('matches.show', $match->id) }}" class="tt-btn tt-btn-secondary tt-magnetic-item" target="_blank" rel="noopener">
                                <span data-hover="Voir cote user">Voir cote user</span>
                            </a>
                            <a href="{{ route('admin.matches.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                <span data-hover="Retour">Retour</span>
                            </a>
                        </div>

                        <div class="adm-compact-kpis">
                            <article class="adm-compact-kpi tt-anim-fadeinup">
                                <strong>{{ $currentStatus }}</strong>
                                <span>Status</span>
                            </article>
                            <article class="adm-compact-kpi tt-anim-fadeinup">
                                <strong>{{ $currentResult !== '' ? $currentResult : '-' }}</strong>
                                <span>Resultat</span>
                            </article>
                            <article class="adm-compact-kpi tt-anim-fadeinup">
                                <strong>{{ (int) ($match->bets_count ?? $match->bets->count()) }}</strong>
                                <span>Bets</span>
                            </article>
                            <article class="adm-compact-kpi tt-anim-fadeinup">
                                <strong>{{ optional($match->starts_at)->format('d/m/Y H:i') ?: '-' }}</strong>
                                <span>Debut</span>
                            </article>
                            <article class="adm-compact-kpi tt-anim-fadeinup">
                                <strong>{{ optional($match->locked_at)->format('d/m/Y H:i') ?: '-' }}</strong>
                                <span>Lock</span>
                            </article>
                            <article class="adm-compact-kpi tt-anim-fadeinup">
                                <strong>{{ optional($match->settlement?->processed_at)->format('d/m/Y H:i') ?: '-' }}</strong>
                                <span>Settlement</span>
                            </article>
                        </div>
                    </section>

                    <div class="adm-sub-grid">
                        <section class="adm-surface">
                            <h3 class="adm-surface-title">Changer statut</h3>
                            <form method="POST" action="{{ route('admin.matches.status', $match->id) }}" class="tt-form tt-form-creative adm-form">
                                @csrf
                                <div class="tt-form-group">
                                    <label for="status">Status</label>
                                    <select class="tt-form-control" id="status" name="status" required data-lenis-prevent>
                                        @foreach($statuses as $statusValue)
                                            <option value="{{ $statusValue }}" {{ $currentStatus === $statusValue ? 'selected' : '' }}>{{ $statusValue }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                    <span data-hover="Mettre a jour statut">Mettre a jour statut</span>
                                </button>
                            </form>
                        </section>

                        <section class="adm-surface">
                            <h3 class="adm-surface-title">Definir resultat</h3>
                            <form method="POST" action="{{ route('admin.matches.result', $match->id) }}" class="tt-form tt-form-creative adm-form">
                                @csrf
                                <div class="tt-form-group">
                                    <label for="result">Resultat</label>
                                    <select class="tt-form-control" id="result" name="result" required data-lenis-prevent>
                                        @foreach($resultOptions as $resultValue => $resultLabel)
                                            <option value="{{ $resultValue }}" {{ $currentResult === $resultValue ? 'selected' : '' }}>{{ $resultLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item">
                                    <span data-hover="Appliquer resultat">Appliquer resultat</span>
                                </button>
                            </form>
                        </section>
                    </div>

                    <section class="adm-surface">
                        <h3 class="adm-surface-title">Settlement idempotent</h3>
                        <p class="adm-meta margin-bottom-20">Utilisez une cle unique pour chaque execution afin d eviter les doubles traitements.</p>

                        <form method="POST" action="{{ route('admin.matches.settle', $match->id) }}" class="tt-form tt-form-creative adm-form">
                            @csrf

                            <div class="adm-form-grid">
                                <div class="tt-form-group">
                                    <label for="settle_result">Resultat settlement</label>
                                    <select class="tt-form-control" id="settle_result" name="result" required data-lenis-prevent>
                                        @foreach($resultOptions as $resultValue => $resultLabel)
                                            <option value="{{ $resultValue }}">{{ $resultLabel }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="tt-form-group">
                                    <label for="idempotency_key">Idempotency key</label>
                                    <input class="tt-form-control" id="idempotency_key" name="idempotency_key" value="settle-{{ $match->id }}-{{ now()->timestamp }}" required>
                                </div>
                            </div>

                            <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                <span data-hover="Executer settlement">Executer settlement</span>
                            </button>
                        </form>
                    </section>

                    <section class="adm-surface">
                        <h3 class="adm-surface-title">Derniers bets (30)</h3>

                        @if($match->bets->count())
                            <div class="adm-table-wrap">
                                <table class="adm-table">
                                    <thead>
                                        <tr>
                                            <th>Bet ID</th>
                                            <th>User</th>
                                            <th>Prediction</th>
                                            <th>Stake</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($match->bets as $bet)
                                            <tr>
                                                <td>#{{ $bet->id }}</td>
                                                <td>{{ $bet->user->name ?? 'N/A' }}</td>
                                                <td>{{ $bet->prediction }}</td>
                                                <td>{{ (int) $bet->stake_points }}</td>
                                                <td><span class="adm-pill">{{ $bet->status }}</span></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="adm-empty">Aucun bet sur ce match.</div>
                        @endif
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    @include('pages.admin.partials.theme-scripts')
@endsection
