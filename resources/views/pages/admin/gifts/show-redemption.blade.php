@extends('marketing.layouts.template')

@section('title', $orderNumber.' | Admin commande cadeau')
@section('meta_description', 'Detail admin d une commande cadeau ERAH.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
@endsection

@section('content')
    @php
        $statusLabels = $statusLabels ?? \App\Models\GiftRedemption::statusLabels();
    @endphp

    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'Administration ERAH',
        'heroTitle' => 'Detail commande cadeau',
        'heroDescription' => $orderNumber.' - suivi complet, events et actions admin.',
        'heroMaskDescription' => 'Detail commande cadeau.',
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20">
                            <h2 class="tt-heading-title tt-text-reveal">{{ $orderNumber }}</h2>
                            <p class="adm-meta">Statut actuel: {{ $statusLabels[$redemption->status] ?? ucfirst((string) $redemption->status) }}</p>
                        </div>

                        <div class="adm-sub-grid">
                            <div class="adm-sub-stack">
                                <div class="adm-user-item">
                                    <strong>Utilisateur</strong>
                                    <small>#{{ $redemption->user_id }} - {{ $redemption->user->name ?? 'Utilisateur supprime' }} - {{ $redemption->user->email ?? '-' }}</small>
                                </div>
                                <div class="adm-user-item">
                                    <strong>Cadeau</strong>
                                    <small>#{{ $redemption->gift_id }} - {{ $redemption->gift->title ?? 'Cadeau supprime' }} - {{ (int) $redemption->cost_points_snapshot }} pts</small>
                                </div>
                                <div class="adm-user-item">
                                    <strong>Dates et suivi</strong>
                                    <small>Demande: {{ optional($redemption->requested_at)->format('d/m/Y H:i') ?: '-' }}</small><br>
                                    <small>Approuvee: {{ optional($redemption->approved_at)->format('d/m/Y H:i') ?: '-' }}</small><br>
                                    <small>Expediee: {{ optional($redemption->shipped_at)->format('d/m/Y H:i') ?: '-' }}</small><br>
                                    <small>Livree: {{ optional($redemption->delivered_at)->format('d/m/Y H:i') ?: '-' }}</small><br>
                                    <small>Rejetee: {{ optional($redemption->rejected_at)->format('d/m/Y H:i') ?: '-' }}</small><br>
                                    <small>Tracking: {{ $redemption->tracking_code ?: '-' }} {{ $redemption->tracking_carrier ? '('.$redemption->tracking_carrier.')' : '' }}</small>
                                </div>
                                <div class="adm-user-item">
                                    <strong>Motif / note client</strong>
                                    <small>{{ $redemption->reason ?: '-' }}</small><br>
                                    <small>Note expedition: {{ $redemption->shipping_note ?: '-' }}</small>
                                </div>
                                <div class="adm-user-item">
                                    <strong>Note interne</strong>
                                    <small>{{ $redemption->internal_note ?: 'Aucune note interne' }}</small>
                                </div>
                            </div>

                            <div class="adm-sub-stack">
                                <h3 class="adm-surface-title">Actions fulfillment</h3>
                                <div class="adm-row-actions">
                                    <form method="POST" action="{{ route('admin.redemptions.approve', $redemption->id) }}">
                                        @csrf
                                        <button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item"><span data-hover="Approuver">Approuver</span></button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.redemptions.deliver', $redemption->id) }}">
                                        @csrf
                                        <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Livrer">Livrer</span></button>
                                    </form>
                                </div>

                                <form method="POST" action="{{ route('admin.redemptions.reject', $redemption->id) }}" class="tt-form tt-form-creative adm-form">
                                    @csrf
                                    <div class="adm-form-grid">
                                        <div class="tt-form-group adm-col-span-2">
                                            <label>Rejeter (motif obligatoire)</label>
                                            <input class="tt-form-control" name="reason" required minlength="3" placeholder="Motif du rejet">
                                        </div>
                                    </div>
                                    <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Rejeter">Rejeter</span></button>
                                </form>

                                <form method="POST" action="{{ route('admin.redemptions.ship', $redemption->id) }}" class="tt-form tt-form-creative adm-form">
                                    @csrf
                                    <div class="adm-form-grid">
                                        <div class="tt-form-group"><label>Tracking (obligatoire)</label><input class="tt-form-control" name="tracking_code" required value="{{ old('tracking_code', $redemption->tracking_code) }}"></div>
                                        <div class="tt-form-group"><label>Transporteur</label><input class="tt-form-control" name="tracking_carrier" value="{{ old('tracking_carrier', $redemption->tracking_carrier) }}"></div>
                                        <div class="tt-form-group adm-col-span-2"><label>Note expedition</label><textarea class="tt-form-control" name="shipping_note" rows="2">{{ old('shipping_note', $redemption->shipping_note) }}</textarea></div>
                                    </div>
                                    <button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item"><span data-hover="Expedier">Expedier</span></button>
                                </form>

                                <form method="POST" action="{{ route('admin.redemptions.note', $redemption->id) }}" class="tt-form tt-form-creative adm-form">
                                    @csrf
                                    <div class="adm-form-grid">
                                        <div class="tt-form-group adm-col-span-2"><label>Note interne</label><textarea class="tt-form-control" name="internal_note" rows="2" required minlength="3">{{ old('internal_note', $redemption->internal_note) }}</textarea></div>
                                    </div>
                                    <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Enregistrer note">Enregistrer note</span></button>
                                </form>

                                <form method="POST" action="{{ route('admin.redemptions.refund', $redemption->id) }}" class="tt-form tt-form-creative adm-form">
                                    @csrf
                                    <div class="adm-form-grid">
                                        <div class="tt-form-group adm-col-span-2"><label>Remboursement (motif obligatoire)</label><input class="tt-form-control" name="reason" required minlength="3" placeholder="Motif du remboursement"></div>
                                    </div>
                                    <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Rembourser">Rembourser</span></button>
                                </form>
                            </div>
                        </div>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20"><h2 class="tt-heading-title tt-text-reveal">Historique evenements</h2></div>
                        @if($redemption->events->count())
                            <div class="adm-table-wrap">
                                <table class="adm-table">
                                    <thead><tr><th>Date</th><th>Type</th><th>Acteur</th><th>Resume</th></tr></thead>
                                    <tbody>
                                        @foreach($redemption->events as $event)
                                            <tr>
                                                <td>{{ optional($event->created_at)->format('d/m/Y H:i') ?: '-' }}</td>
                                                <td>{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', (string) $event->type)) }}</td>
                                                <td>{{ $event->actor?->name ?? 'Systeme' }}</td>
                                                <td><small>{{ json_encode($event->data ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</small></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="adm-empty">Aucun evenement enregistre pour cette commande.</div>
                        @endif
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-20"><h2 class="tt-heading-title tt-text-reveal">Transactions wallet liees</h2></div>
                        @if(($walletTransactions ?? collect())->count())
                            <div class="adm-table-wrap">
                                <table class="adm-table">
                                    <thead><tr><th>Date</th><th>Type</th><th>Montant</th><th>Solde apres</th><th>Unique key</th></tr></thead>
                                    <tbody>
                                        @foreach($walletTransactions as $tx)
                                            <tr>
                                                <td>{{ optional($tx->created_at)->format('d/m/Y H:i') ?: '-' }}</td>
                                                <td>{{ $tx->type }}</td>
                                                <td>{{ (int) $tx->amount }}</td>
                                                <td>{{ (int) $tx->balance_after }}</td>
                                                <td><small>{{ $tx->unique_key }}</small></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="adm-empty">Aucune transaction wallet liee trouvee.</div>
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

