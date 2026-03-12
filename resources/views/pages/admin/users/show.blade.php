@extends('marketing.layouts.template')

@section('title', 'Detail utilisateur admin | ERAH')
@section('meta_description', 'Detail operationnel d un utilisateur pour moderation et suivi fulfilment.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
@endsection

@section('content')
    @php
        $userProfile = $userProfile ?? null;
        $redemptions = $redemptions ?? collect();
        $redemptionStatusCounts = $redemptionStatusCounts ?? collect();
        $redemptionStatusLabels = $redemptionStatusLabels ?? \App\Models\GiftRedemption::statusLabels();
        $bets = $bets ?? collect();
        $duels = $duels ?? collect();
        $activityEvents = $activityEvents ?? collect();
    @endphp

    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'Administration ERAH',
        'heroTitle' => 'Detail utilisateur admin',
        'heroDescription' => ($userProfile?->name ?? 'Utilisateur').' - investigation complete du compte.',
        'heroMaskDescription' => 'Detail compte, commandes, paris et activite.',
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">Compte et progression</h2>
                        </div>
                        <div class="adm-row-actions margin-bottom-10">
                            <span class="adm-pill">ID #{{ $userProfile->id }}</span>
                            <span class="adm-pill">{{ $userProfile->role }}</span>
                            <span class="adm-pill">Ligue {{ $userProfile->progress?->league?->name ?? 'N/A' }}</span>
                            <span class="adm-pill">{{ $userProfile->email }}</span>
                        </div>
                        <div class="adm-kpi-grid">
                            <article class="adm-kpi-card">
                                <strong>{{ (int) ($userProfile->progress?->total_xp ?? 0) }}</strong>
                                <span>XP total</span>
                            </article>
                            <article class="adm-kpi-card">
                                <strong>{{ (int) ($userProfile->progress?->total_rank_points ?? 0) }}</strong>
                                <span>Points classement</span>
                            </article>
                            <article class="adm-kpi-card">
                                <strong>{{ (int) ($userProfile->wallet?->balance ?? 0) }}</strong>
                                <span>Solde paris</span>
                            </article>
                            <article class="adm-kpi-card">
                                <strong>{{ (int) ($userProfile->rewardWallet?->balance ?? 0) }}</strong>
                                <span>Points plateforme</span>
                            </article>
                            <article class="adm-kpi-card">
                                <strong>{{ (int) ($userProfile->loginStreak?->current_streak ?? 0) }}</strong>
                                <span>Streak actuel</span>
                            </article>
                        </div>
                        <div class="adm-row-actions margin-top-20">
                            <a href="{{ route('users.index', ['user_id' => $userProfile->id]) }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                <span data-hover="Retour liste">Retour liste</span>
                            </a>
                            <a href="{{ route('users.public', $userProfile->id) }}" class="tt-btn tt-btn-secondary tt-magnetic-item" target="_blank" rel="noopener">
                                <span data-hover="Profil public">Profil public</span>
                            </a>
                            <a href="{{ route('admin.gifts.index', ['user_id' => $userProfile->id]) }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                <span data-hover="Commandes cadeaux">Commandes cadeaux</span>
                            </a>
                        </div>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">Commandes cadeaux</h2>
                            <p class="adm-meta">Suivi des demandes et statut de fulfilment pour cet utilisateur.</p>
                        </div>
                        <div class="adm-row-actions margin-bottom-20">
                            @foreach($redemptionStatusLabels as $statusKey => $statusLabel)
                                <span class="adm-pill">{{ $statusLabel }}: {{ (int) ($redemptionStatusCounts[$statusKey] ?? 0) }}</span>
                            @endforeach
                        </div>
                        @if($redemptions->count())
                            <div class="adm-table-wrap">
                                <table class="adm-table">
                                    <thead>
                                        <tr>
                                            <th>Commande</th>
                                            <th>Cadeau</th>
                                            <th>Statut</th>
                                            <th>Date demande</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($redemptions as $redemption)
                                            <tr>
                                                <td><strong>CMD-{{ str_pad((string) $redemption->id, 6, '0', STR_PAD_LEFT) }}</strong><br><small>#{{ $redemption->id }}</small></td>
                                                <td>{{ $redemption->gift->title ?? 'Cadeau supprime' }}</td>
                                                <td><span class="adm-pill">{{ $redemptionStatusLabels[$redemption->status] ?? ucfirst((string) $redemption->status) }}</span></td>
                                                <td>{{ optional($redemption->requested_at)->format('d/m/Y H:i') ?: '-' }}</td>
                                                <td>
                                                    <a href="{{ route('admin.redemptions.show', $redemption->id) }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                                        <span data-hover="Detail">Detail</span>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="adm-empty">Aucune commande cadeau pour cet utilisateur.</div>
                        @endif
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">Paris recents</h2>
                        </div>
                        @if($bets->count())
                            <div class="adm-table-wrap">
                                <table class="adm-table">
                                    <thead>
                                        <tr>
                                            <th>Match</th>
                                            <th>Prediction</th>
                                            <th>Stake</th>
                                            <th>Statut</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($bets as $bet)
                                            <tr>
                                                <td>{{ $bet->match?->home_team ?? '?' }} vs {{ $bet->match?->away_team ?? '?' }}</td>
                                                <td>{{ $bet->prediction }}</td>
                                                <td>{{ (int) ($bet->stake_points ?? $bet->stake ?? 0) }}</td>
                                                <td><span class="adm-pill">{{ $bet->status }}</span></td>
                                                <td>{{ optional($bet->placed_at ?? $bet->created_at)->format('d/m/Y H:i') ?: '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="adm-empty">Aucun pari recent.</div>
                        @endif
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">Duels recents</h2>
                        </div>
                        @if($duels->count())
                            <div class="adm-table-wrap">
                                <table class="adm-table">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Adversaire</th>
                                            <th>Statut</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($duels as $duel)
                                            @php
                                                $opponent = $duel->challenger_id === $userProfile->id ? $duel->challenged : $duel->challenger;
                                            @endphp
                                            <tr>
                                                <td>#{{ $duel->id }}</td>
                                                <td>{{ $opponent?->name ?? 'N/A' }}</td>
                                                <td><span class="adm-pill">{{ $duel->status }}</span></td>
                                                <td>{{ optional($duel->requested_at ?? $duel->created_at)->format('d/m/Y H:i') ?: '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="adm-empty">Aucun duel recent.</div>
                        @endif
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">Activite recente</h2>
                        </div>
                        @if($activityEvents->count())
                            <div class="adm-table-wrap">
                                <table class="adm-table">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Module</th>
                                            <th>Reference</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($activityEvents as $event)
                                            <tr>
                                                <td>{{ optional($event->occurred_at)->format('d/m/Y H:i') ?: '-' }}</td>
                                                <td>{{ $event->event_type }}</td>
                                                <td>{{ $event->ref_type }}</td>
                                                <td>{{ $event->ref_id }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="adm-empty">Aucune activite recente pour cet utilisateur.</div>
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
