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
        $normalizeText = static function ($value): string {
            $text = trim((string) ($value ?? ''));
            if ($text === '') {
                return '-';
            }

            $text = preg_replace('/\[[^\]]+\]\s*/', '', $text) ?: $text;
            $text = preg_replace('/\s+/', ' ', $text) ?: $text;
            $text = trim($text);

            return $text === '' ? '-' : $text;
        };

        $eventTypeLabels = [
            'redeem_requested' => 'Demande creee',
            'admin_approved' => 'Commande approuvee',
            'admin_rejected' => 'Commande rejetee',
            'admin_shipped' => 'Commande expediee',
            'admin_delivered' => 'Commande livree',
            'admin_refunded' => 'Commande remboursee',
            'admin_note_added' => 'Note interne enregistree',
        ];

        $walletTypeLabels = [
            'gift_purchase' => 'Debit commande cadeau',
            'redeem_cost' => 'Debit commande cadeau',
            'redeem_refund' => 'Remboursement commande cadeau',
            'grant' => 'Credit manuel',
            'admin_adjustment' => 'Ajustement admin',
        ];

        $shippingNoteInput = old('shipping_note');
        if ($shippingNoteInput === null) {
            $shippingNoteInput = $normalizeText($redemption->shipping_note);
            $shippingNoteInput = $shippingNoteInput === '-' ? '' : $shippingNoteInput;
        }

        $internalNoteInput = old('internal_note');
        if ($internalNoteInput === null) {
            $internalNoteInput = $normalizeText($redemption->internal_note);
            $internalNoteInput = $internalNoteInput === '-' ? '' : $internalNoteInput;
        }

        $formatEventSummary = static function ($event) use ($normalizeText, $redemption, $statusLabels): string {
            $data = is_array($event->data ?? null) ? $event->data : [];
            $eventType = (string) ($event->type ?? '');
            $reason = $normalizeText($data['reason'] ?? null);
            $trackingCode = $normalizeText($data['tracking_code'] ?? null);
            $trackingCarrier = $normalizeText($data['tracking_carrier'] ?? null);
            $shippingNote = $normalizeText($data['shipping_note'] ?? null);
            $internalNote = $normalizeText($data['internal_note'] ?? null);

            return match ($eventType) {
                'redeem_requested' => 'Demande creee pour le cadeau #'.((int) ($data['gift_id'] ?? $redemption->gift_id)).' ('.((int) ($data['cost_points'] ?? $redemption->cost_points_snapshot ?? 0)).' pts).',
                'admin_approved' => 'Commande validee pour preparation.',
                'admin_rejected' => $reason !== '-' ? 'Commande rejetee. Motif: '.$reason : 'Commande rejetee.',
                'admin_shipped' => collect([
                    $trackingCode !== '-' ? 'tracking '.$trackingCode : null,
                    $trackingCarrier !== '-' ? 'transporteur '.$trackingCarrier : null,
                    $shippingNote !== '-' ? 'note expedition: '.$shippingNote : null,
                ])->filter()->isNotEmpty()
                    ? 'Commande expediee ('.collect([
                        $trackingCode !== '-' ? 'tracking '.$trackingCode : null,
                        $trackingCarrier !== '-' ? 'transporteur '.$trackingCarrier : null,
                        $shippingNote !== '-' ? 'note expedition: '.$shippingNote : null,
                    ])->filter()->implode(', ').').'
                    : 'Commande marquee comme expediee.',
                'admin_delivered' => 'Commande marquee comme livree.',
                'admin_refunded' => $reason !== '-' ? 'Points rembourses. Motif: '.$reason : 'Points rembourses a l utilisateur.',
                'admin_note_added' => $internalNote !== '-' ? 'Note interne: '.$internalNote : 'Note interne mise a jour.',
                default => (function () use ($data, $normalizeText, $statusLabels): string {
                    if ($data === []) {
                        return 'Evenement enregistre.';
                    }

                    $labels = [
                        'reason' => 'Motif',
                        'tracking_code' => 'Tracking',
                        'tracking_carrier' => 'Transporteur',
                        'shipping_note' => 'Note expedition',
                        'internal_note' => 'Note interne',
                        'gift_id' => 'Cadeau',
                        'cost_points' => 'Points',
                        'status' => 'Statut',
                    ];

                    $parts = [];
                    foreach ($labels as $key => $label) {
                        if (! array_key_exists($key, $data)) {
                            continue;
                        }

                        $value = $key === 'status'
                            ? ($statusLabels[(string) $data[$key]] ?? $normalizeText($data[$key]))
                            : $normalizeText($data[$key]);

                        if ($value === '-') {
                            continue;
                        }

                        $parts[] = $label.': '.$value;
                    }

                    return $parts === [] ? 'Evenement enregistre.' : implode(' | ', $parts);
                })(),
            };
        };

        $formatWalletType = static function (?string $type) use ($walletTypeLabels): string {
            $typeValue = (string) ($type ?? '');

            return $walletTypeLabels[$typeValue]
                ?? \Illuminate\Support\Str::headline(str_replace('_', ' ', $typeValue));
        };

        $formatWalletReference = static function ($tx): string {
            $refType = (string) ($tx->ref_type ?? '');
            $refId = trim((string) ($tx->ref_id ?? ''));

            return match ($refType) {
                \App\Models\RewardWalletTransaction::REF_TYPE_GIFT => $refId !== '' ? 'Commande cadeau #'.$refId : 'Commande cadeau',
                \App\Models\RewardWalletTransaction::REF_TYPE_MISSION => $refId !== '' ? 'Mission #'.$refId : 'Mission',
                \App\Models\RewardWalletTransaction::REF_TYPE_ADMIN => $refId !== '' ? 'Action admin #'.$refId : 'Action admin',
                \App\Models\RewardWalletTransaction::REF_TYPE_SYSTEM => $refId !== '' ? 'Systeme #'.$refId : 'Systeme',
                default => $refId !== '' ? '#'.$refId : 'Operation interne',
            };
        };

        $formatWalletDetails = static function ($tx) use ($normalizeText): string {
            $metadata = is_array($tx->metadata ?? null) ? $tx->metadata : [];
            $parts = [];

            if (array_key_exists('reason', $metadata)) {
                $reason = $normalizeText($metadata['reason']);
                if ($reason !== '-') {
                    $parts[] = 'Motif: '.$reason;
                }
            }

            if (array_key_exists('source', $metadata)) {
                $source = $normalizeText($metadata['source']);
                if ($source !== '-') {
                    $parts[] = 'Source: '.$source;
                }
            }

            if (array_key_exists('actor_id', $metadata)) {
                $actorId = (int) $metadata['actor_id'];
                if ($actorId > 0) {
                    $parts[] = 'Admin #'.$actorId;
                }
            }

            return $parts === [] ? '-' : implode(' | ', $parts);
        };
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
                                    @if($redemption->user)
                                        <div class="adm-row-actions margin-top-10">
                                            <a href="{{ route('admin.users.show', $redemption->user_id) }}" class="tt-btn tt-btn-secondary tt-magnetic-item">
                                                <span data-hover="Fiche utilisateur">Fiche utilisateur</span>
                                            </a>
                                            <a href="{{ route('admin.gifts.index', ['user_id' => $redemption->user_id]) }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                                <span data-hover="Toutes ses commandes">Toutes ses commandes</span>
                                            </a>
                                        </div>
                                    @endif
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
                                    <small>Motif: {{ $normalizeText($redemption->reason) }}</small><br>
                                    <small>Note expedition: {{ $normalizeText($redemption->shipping_note) }}</small>
                                </div>
                                <div class="adm-user-item">
                                    <strong>Note interne</strong>
                                    <small>{{ $normalizeText($redemption->internal_note) }}</small>
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
                                        <div class="tt-form-group adm-col-span-2"><label>Note expedition</label><textarea class="tt-form-control" name="shipping_note" rows="2">{{ $shippingNoteInput }}</textarea></div>
                                    </div>
                                    <button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item"><span data-hover="Expedier">Expedier</span></button>
                                </form>

                                <form method="POST" action="{{ route('admin.redemptions.note', $redemption->id) }}" class="tt-form tt-form-creative adm-form">
                                    @csrf
                                    <div class="adm-form-grid">
                                        <div class="tt-form-group adm-col-span-2"><label>Note interne</label><textarea class="tt-form-control" name="internal_note" rows="2" required minlength="3">{{ $internalNoteInput }}</textarea></div>
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
                                                <td>{{ $eventTypeLabels[(string) $event->type] ?? \Illuminate\Support\Str::headline(str_replace('_', ' ', (string) $event->type)) }}</td>
                                                <td>{{ $event->actor?->name ?? 'Systeme' }}</td>
                                                <td><small>{{ $formatEventSummary($event) }}</small></td>
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
                                    <thead><tr><th>Date</th><th>Operation</th><th>Impact points</th><th>Solde apres</th><th>Reference</th><th>Details</th></tr></thead>
                                    <tbody>
                                        @foreach($walletTransactions as $tx)
                                            <tr>
                                                <td>{{ optional($tx->created_at)->format('d/m/Y H:i') ?: '-' }}</td>
                                                <td>{{ $formatWalletType($tx->type) }}</td>
                                                <td>{{ ((int) $tx->amount > 0 ? '+' : '').(int) $tx->amount }} pts</td>
                                                <td>{{ (int) $tx->balance_after }} pts</td>
                                                <td>{{ $formatWalletReference($tx) }}</td>
                                                <td>
                                                    <small>{{ $formatWalletDetails($tx) }}</small>
                                                    @if(! blank($tx->unique_key))
                                                        <details class="margin-top-5">
                                                            <summary>Trace technique</summary>
                                                            <small>{{ $tx->unique_key }}</small>
                                                        </details>
                                                    @endif
                                                </td>
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
