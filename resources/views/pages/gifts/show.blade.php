@extends('marketing.layouts.template')

@section('title', ($gift->title ?? 'Cadeau').' | Cadeaux ERAH')
@section('meta_description', 'Detail cadeau ERAH, demande et suivi depuis le solde points.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('page_styles')
<style>
    .gift-detail-page {
        --gift-accent: #db0812;
        --gift-accent-soft: rgba(219, 8, 18, 0.16);
        --gift-border: rgba(255, 255, 255, 0.1);
        --gift-text-muted: rgba(255, 255, 255, 0.64);
        --gift-card-bg: linear-gradient(180deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.02));
    }

    .gift-detail-page #page-header .ph-caption-description {
        max-width: 720px;
    }

    .gift-detail-header-pills,
    .gift-detail-inline-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .gift-detail-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        padding: 10px 16px;
        border: 1px solid var(--gift-border);
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.04);
        color: rgba(255, 255, 255, 0.88);
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .gift-detail-surface {
        border: 1px solid var(--gift-border);
        border-radius: 32px;
        background: var(--gift-card-bg);
        box-shadow: 0 28px 70px rgba(0, 0, 0, 0.26);
        backdrop-filter: blur(18px);
    }

    .gift-detail-hero-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.05fr) minmax(360px, 0.95fr);
        gap: 30px;
    }

    .gift-detail-visual {
        padding: 26px;
    }

    .gift-detail-visual-stage {
        position: relative;
        display: grid;
        align-items: end;
        min-height: 620px;
        overflow: hidden;
        border-radius: 28px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background:
            radial-gradient(circle at 18% 20%, rgba(219, 8, 18, 0.22), transparent 38%),
            radial-gradient(circle at 82% 78%, rgba(255, 255, 255, 0.08), transparent 32%),
            linear-gradient(180deg, rgba(18, 18, 18, 0.78), rgba(8, 8, 8, 0.94));
    }

    .gift-detail-visual-stage::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0.08), rgba(0, 0, 0, 0.54));
        pointer-events: none;
    }

    .gift-detail-visual-image {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        opacity: 0.94;
    }

    .gift-detail-visual-badge,
    .gift-detail-visual-stamp,
    .gift-detail-visual-copy {
        position: relative;
        z-index: 1;
    }

    .gift-detail-visual-badge {
        position: absolute;
        top: 22px;
        left: 22px;
        display: inline-flex;
        padding: 9px 14px;
        border-radius: 999px;
        background: rgba(0, 0, 0, 0.42);
        border: 1px solid rgba(255, 255, 255, 0.1);
        color: #fff;
        font-size: 12px;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .gift-detail-visual-stamp {
        position: absolute;
        top: 22px;
        right: 22px;
        display: inline-flex;
        padding: 9px 14px;
        border-radius: 999px;
        background: rgba(219, 8, 18, 0.88);
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .gift-detail-visual-copy {
        display: grid;
        gap: 12px;
        padding: 32px;
    }

    .gift-detail-visual-copy strong {
        font-family: "Big Shoulders Display", sans-serif;
        font-size: clamp(52px, 6vw, 108px);
        line-height: 0.9;
        text-transform: uppercase;
    }

    .gift-detail-visual-copy p {
        max-width: 32ch;
        margin: 0;
        color: rgba(255, 255, 255, 0.74);
        font-size: 17px;
        line-height: 1.7;
    }

    .gift-detail-usp-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-top: 16px;
    }

    .gift-detail-usp-card {
        padding: 18px;
        border: 1px solid var(--gift-border);
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.03);
    }

    .gift-detail-usp-card strong {
        display: block;
        margin-bottom: 6px;
        color: #fff;
        font-size: 14px;
        font-weight: 600;
    }

    .gift-detail-usp-card p {
        margin: 0;
        color: var(--gift-text-muted);
        font-size: 14px;
        line-height: 1.6;
    }

    .gift-detail-panel {
        padding: 34px;
    }

    .gift-detail-eyebrow {
        display: inline-block;
        margin-bottom: 14px;
        color: rgba(255, 255, 255, 0.56);
        font-size: 12px;
        letter-spacing: 0.24em;
        text-transform: uppercase;
    }

    .gift-detail-panel h2 {
        margin: 0 0 12px;
        color: #fff;
        font-size: clamp(34px, 3.4vw, 60px);
        line-height: 0.94;
    }

    .gift-detail-panel > p {
        margin: 0 0 22px;
        color: var(--gift-text-muted);
        font-size: 16px;
        line-height: 1.75;
    }

    .gift-detail-metrics {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        margin-top: 24px;
    }

    .gift-detail-metric {
        padding: 18px 20px;
        border: 1px solid var(--gift-border);
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.03);
    }

    .gift-detail-metric span {
        display: block;
        margin-bottom: 8px;
        color: rgba(255, 255, 255, 0.48);
        font-size: 11px;
        letter-spacing: 0.18em;
        text-transform: uppercase;
    }

    .gift-detail-metric strong {
        display: block;
        color: #fff;
        font-size: clamp(28px, 2vw, 42px);
        line-height: 1;
    }

    .gift-detail-state {
        margin-top: 20px;
        padding: 20px 22px;
        border-radius: 24px;
        border: 1px solid var(--gift-border);
        background: rgba(255, 255, 255, 0.03);
    }

    .gift-detail-state.is-limited {
        border-color: rgba(240, 173, 78, 0.35);
        background: rgba(240, 173, 78, 0.08);
    }

    .gift-detail-state.is-unavailable {
        border-color: rgba(219, 8, 18, 0.28);
        background: rgba(219, 8, 18, 0.08);
    }

    .gift-detail-state strong {
        display: block;
        margin-bottom: 8px;
        color: #fff;
        font-size: 18px;
    }

    .gift-detail-state p {
        margin: 0;
        color: var(--gift-text-muted);
        font-size: 15px;
        line-height: 1.7;
    }

    .gift-detail-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 26px;
    }

    .gift-detail-actions .tt-btn[disabled] {
        opacity: 0.45;
        pointer-events: none;
    }

    .gift-detail-latest {
        margin-top: 24px;
        padding: 20px 22px;
        border-radius: 24px;
        border: 1px solid var(--gift-border);
        background: rgba(255, 255, 255, 0.03);
    }

    .gift-detail-latest-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 10px;
    }

    .gift-detail-latest-head strong {
        color: #fff;
        font-size: 18px;
    }

    .gift-detail-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 34px;
        padding: 8px 13px;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.04);
        color: #fff;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }

    .gift-detail-status.is-pending { background: rgba(255, 255, 255, 0.06); }
    .gift-detail-status.is-approved { background: rgba(56, 142, 60, 0.16); border-color: rgba(56, 142, 60, 0.28); }
    .gift-detail-status.is-rejected { background: rgba(219, 8, 18, 0.16); border-color: rgba(219, 8, 18, 0.28); }
    .gift-detail-status.is-shipped { background: rgba(14, 131, 205, 0.16); border-color: rgba(14, 131, 205, 0.28); }
    .gift-detail-status.is-delivered { background: rgba(139, 195, 74, 0.18); border-color: rgba(139, 195, 74, 0.3); }
    .gift-detail-status.is-cancelled { background: rgba(117, 117, 117, 0.18); border-color: rgba(117, 117, 117, 0.3); }

    .gift-detail-latest p,
    .gift-detail-latest ul,
    .gift-detail-empty p,
    .gift-detail-side-card p,
    .gift-detail-history-item p {
        margin: 0;
        color: var(--gift-text-muted);
        line-height: 1.7;
    }

    .gift-detail-content-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(320px, 0.46fr);
        gap: 24px;
    }

    .gift-detail-history,
    .gift-detail-side-card {
        padding: 30px;
    }

    .gift-detail-section-heading {
        margin-bottom: 20px;
    }

    .gift-detail-section-heading h3 {
        margin: 0 0 6px;
        color: #fff;
        font-size: clamp(28px, 2.3vw, 44px);
        line-height: 0.98;
    }

    .gift-detail-section-heading p {
        margin: 0;
        color: var(--gift-text-muted);
        line-height: 1.7;
    }

    .gift-detail-history-list {
        display: grid;
        gap: 14px;
    }

    .gift-detail-history-item {
        padding: 20px 22px;
        border: 1px solid var(--gift-border);
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.03);
    }

    .gift-detail-history-top {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 8px;
    }

    .gift-detail-history-top strong {
        color: #fff;
        font-size: 17px;
    }

    .gift-detail-history-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 14px;
    }

    .gift-detail-history-meta span {
        display: inline-flex;
        align-items: center;
        min-height: 34px;
        padding: 8px 12px;
        border: 1px solid var(--gift-border);
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.04);
        color: rgba(255, 255, 255, 0.8);
        font-size: 11px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .gift-detail-side-stack {
        display: grid;
        gap: 24px;
    }

    .gift-detail-side-card ul {
        display: grid;
        gap: 10px;
        padding-left: 18px;
        margin: 16px 0 0;
        color: var(--gift-text-muted);
    }

    .gift-detail-wallet-highlight {
        display: grid;
        gap: 14px;
        margin-top: 18px;
    }

    .gift-detail-wallet-highlight strong {
        color: #fff;
        font-size: 34px;
        line-height: 1;
    }

    .gift-detail-empty {
        padding: 26px;
        border: 1px dashed rgba(255, 255, 255, 0.14);
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.025);
    }

    @media (max-width: 1399.98px) {
        .gift-detail-hero-grid {
            grid-template-columns: 1fr;
        }

        .gift-detail-visual-stage {
            min-height: 520px;
        }
    }

    @media (max-width: 1199.98px) {
        .gift-detail-content-grid,
        .gift-detail-usp-grid {
            grid-template-columns: 1fr;
        }

        .gift-detail-visual-copy strong {
            font-size: clamp(44px, 14vw, 80px);
        }
    }

    @media (max-width: 767.98px) {
        .gift-detail-visual,
        .gift-detail-panel,
        .gift-detail-history,
        .gift-detail-side-card {
            padding: 22px;
        }

        .gift-detail-visual-stage {
            min-height: 380px;
        }

        .gift-detail-visual-copy {
            padding: 24px;
        }

        .gift-detail-metrics {
            grid-template-columns: 1fr;
        }

        .gift-detail-actions .tt-btn,
        .gift-detail-actions a.tt-btn {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
@php
    $statusLabels = $statusLabels ?? \App\Models\GiftRedemption::statusLabels();
    $isAuthenticated = (bool) ($isAuthenticated ?? false);
    $giftIndexRouteName = $giftIndexRouteName ?? 'gifts.index';

    $availabilityState = ! $gift->is_active || $giftStock < 1 ? 'unavailable' : ($giftStock <= 5 ? 'limited' : 'available');
    $availabilityTitle = ! $gift->is_active
        ? 'Cadeau temporairement indisponible'
        : ($giftStock < 1 ? 'Rupture de stock' : ($giftStock <= 5 ? 'Stock limite' : 'Demande ouverte'));
    $availabilityCopy = ! $gift->is_active
        ? 'Ce cadeau est desactive pour le moment. Il redeviendra accessible des sa republication.'
        : ($giftStock < 1
            ? 'Le stock actuel est epuise. Revenez plus tard pour verifier un reapprovisionnement.'
            : ($giftStock <= 5
                ? 'Il reste peu d exemplaires. Une fois votre demande envoyee, le stock est reserve immediatement.'
                : 'La demande est disponible. Les points sont debites a la demande et le stock est bloque.'));
    $latestRedemption = ($myRecentRedemptions ?? collect())->first();
@endphp

<div class="gift-detail-page">
    <div id="page-header" class="ph-full ph-full-m ph-cap-xxlg ph-center ph-image-parallax ph-caption-parallax">
        <div class="ph-image ph-image-cover-6">
            <div class="ph-image-inner">
                <img src="{{ $giftCover }}" alt="{{ $gift->title }}">
            </div>
        </div>

        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">Catalogue cadeaux</h2>
                    <h1 class="ph-caption-title">{{ $gift->title }}</h1>
                    <div class="ph-caption-description">
                        {{ $gift->description ?: 'Une recompense membre a debloquer avec vos points sur la plateforme.' }}
                    </div>
                    <div class="gift-detail-header-pills margin-top-30">
                        <span class="gift-detail-pill">{{ $giftCategoryLabel }}</span>
                        <span class="gift-detail-pill">{{ $giftCost }} pts</span>
                        <span class="gift-detail-pill">Stock {{ $giftStock }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">Catalogue</h2>
                        <h1 class="ph-caption-title">Cadeau</h1>
                        <div class="ph-caption-description max-width-700">
                            Consultez les details, verifiez votre solde et lancez votre demande depuis la fiche cadeau.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tt-scroll-down">
            <a href="#tt-page-content" class="tt-scroll-down-inner tt-magnetic-item" data-offset="0">
                <div class="tt-scrd-icon"></div>
                <svg viewBox="0 0 500 500">
                    <defs>
                        <path d="M50,250c0-110.5,89.5-200,200-200s200,89.5,200,200s-89.5,200-200,200S50,360.5,50,250" id="textcircle"></path>
                    </defs>
                    <text dy="30">
                        <textPath xlink:href="#textcircle">Voir la fiche cadeau - Voir la fiche cadeau -</textPath>
                    </text>
                </svg>
            </a>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-xlg-100 padding-bottom-xlg-80">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="gift-detail-hero-grid">
                    <div class="gift-detail-surface gift-detail-visual tt-anim-fadeinup">
                        <div class="gift-detail-visual-stage">
                            <span class="gift-detail-visual-badge">{{ $giftCategoryLabel }}</span>
                            <span class="gift-detail-visual-stamp">Cadeau</span>
                            <img class="gift-detail-visual-image" src="{{ $giftCover }}" alt="{{ $gift->title }}">

                            <div class="gift-detail-visual-copy">
                                <strong>{{ $gift->title }}</strong>
                                <p>
                                    {{ $gift->description ?: 'Une recompense membre pensee pour valoriser votre activite et vos points cumules sur ERAH.' }}
                                </p>
                            </div>
                        </div>

                        <div class="gift-detail-usp-grid">
                            <div class="gift-detail-usp-card">
                                <strong>Debit immediat</strong>
                                <p>Les points sont preleves au moment de la demande.</p>
                            </div>
                            <div class="gift-detail-usp-card">
                                <strong>Stock reserve</strong>
                                <p>Le stock diminue des que votre demande est envoyee.</p>
                            </div>
                            <div class="gift-detail-usp-card">
                                <strong>Suivi membre</strong>
                                <p>Vous retrouvez l etat de vos demandes dans votre historique.</p>
                            </div>
                        </div>
                    </div>

                    <div class="gift-detail-surface gift-detail-panel tt-anim-fadeinup">
                        <span class="gift-detail-eyebrow">Fiche cadeau</span>
                        <h2>{{ $gift->title }}</h2>
                        <p>{{ $gift->description ?: 'Ce cadeau fait partie du catalogue accessible depuis votre espace membre.' }}</p>

                        <div class="gift-detail-inline-pills">
                            <span class="gift-detail-pill">{{ $giftCategoryLabel }}</span>
                            <span class="gift-detail-pill">{{ $giftCost }} points</span>
                            <span class="gift-detail-pill">{{ $giftStock > 0 ? 'Stock '.$giftStock : 'Rupture' }}</span>
                        </div>

                        <div class="gift-detail-metrics">
                            <div class="gift-detail-metric">
                                <span>{{ $isAuthenticated ? 'Votre solde' : 'Solde membre' }}</span>
                                <strong>{{ $walletBalance }}</strong>
                            </div>
                            <div class="gift-detail-metric">
                                <span>Cout cadeau</span>
                                <strong>{{ $giftCost }}</strong>
                            </div>
                            <div class="gift-detail-metric">
                                <span>Stock restant</span>
                                <strong>{{ $giftStock }}</strong>
                            </div>
                            <div class="gift-detail-metric">
                                <span>{{ $isAuthenticated ? ($canAffordGift ? 'Etat achat' : 'Points manquants') : 'Connexion' }}</span>
                                <strong>{{ $isAuthenticated ? ($canAffordGift ? 'Pret' : $pointsMissing) : 'Requise' }}</strong>
                            </div>
                        </div>

                        <div class="gift-detail-state {{ $availabilityState !== 'available' ? 'is-'.$availabilityState : '' }}">
                            <strong>{{ $availabilityTitle }}</strong>
                            <p>
                                {{ $availabilityCopy }}
                                @if ($isAuthenticated && ! $canAffordGift)
                                    Il vous manque actuellement <strong>{{ $pointsMissing }} points</strong> pour lancer la demande.
                                @endif
                            </p>
                        </div>

                        <div class="gift-detail-actions">
                            @if($isAuthenticated)
                                <form method="POST" action="{{ route('gifts.redeem', $gift->id) }}">
                                    @csrf
                                    <input type="hidden" name="idempotency_key" value="redeem-{{ auth()->id() }}-{{ $gift->id }}-{{ now()->timestamp }}">
                                    <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item" {{ (! $isRedeemable || ! $canAffordGift) ? 'disabled' : '' }}>
                                        <span data-hover="Demander">Demander ce cadeau</span>
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('gifts.cart.add', $gift->id) }}">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item" {{ (! $isRedeemable) ? 'disabled' : '' }}>
                                        <span data-hover="Panier">Ajouter au panier {{ (int) ($cartItemQuantity ?? 0) > 0 ? '('.(int) $cartItemQuantity.')' : '' }}</span>
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('gifts.favorites.toggle', $gift->id) }}">
                                    @csrf
                                    <button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item">
                                        <span data-hover="{{ $isFavorited ? 'Retirer favoris' : 'Ajouter favoris' }}">
                                            {{ $isFavorited ? 'Retirer favoris' : 'Ajouter favoris' }}
                                        </span>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                    <span data-hover="Connexion">Se connecter pour commander</span>
                                </a>
                            @endif

                            <a href="{{ route($giftIndexRouteName) }}" class="tt-btn tt-btn-secondary tt-magnetic-item">
                                <span data-hover="Catalogue">Retour catalogue</span>
                            </a>

                            @if($isAuthenticated)
                                <a href="{{ route('gifts.redemptions') }}" class="tt-btn tt-btn-secondary tt-magnetic-item">
                                    <span data-hover="Demandes">Mes demandes</span>
                                </a>
                                <a href="{{ route('gifts.cart') }}" class="tt-btn tt-btn-secondary tt-magnetic-item">
                                    <span data-hover="Panier">Panier cadeaux</span>
                                </a>
                            @endif
                        </div>

                        @if ($isAuthenticated && $latestRedemption)
                            <div class="gift-detail-latest">
                                <div class="gift-detail-latest-head">
                                    <strong>Derniere commande: {{ 'CMD-'.str_pad((string) $latestRedemption->id, 6, '0', STR_PAD_LEFT) }}</strong>
                                    <span class="gift-detail-status is-{{ $latestRedemption->status }}">
                                        {{ $statusLabels[$latestRedemption->status] ?? \Illuminate\Support\Str::headline((string) $latestRedemption->status) }}
                                    </span>
                                </div>
                                <p>
                                    Demandee le {{ optional($latestRedemption->requested_at)->format('d/m/Y \\a H:i') ?: '-' }}.
                                    @if ($latestRedemption->tracking_code)
                                        Suivi: {{ $latestRedemption->tracking_code }}
                                        @if ($latestRedemption->tracking_carrier)
                                            ({{ $latestRedemption->tracking_carrier }})
                                        @endif.
                                    @endif
                                    @if ($latestRedemption->reason)
                                        Motif: {{ $latestRedemption->reason }}.
                                    @endif
                                </p>
                                <a href="{{ route('gifts.redemptions.show', $latestRedemption->id) }}" class="tt-btn tt-btn-outline margin-top-15">
                                    <span data-hover="Voir le detail">Voir le detail de la commande</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="tt-section padding-bottom-xlg-120 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="gift-detail-content-grid">
                    <div class="gift-detail-surface gift-detail-history tt-anim-fadeinup">
                        <div class="gift-detail-section-heading">
                            <h3>Mes demandes recentes</h3>
                            <p>Retrouvez le suivi de vos demandes pour ce cadeau, avec les etapes de validation, expedition ou refus.</p>
                        </div>

                        @if ($isAuthenticated && ($myRecentRedemptions ?? null) && $myRecentRedemptions->count())
                            <div class="gift-detail-history-list">
                                @foreach ($myRecentRedemptions as $redemption)
                                    <article class="gift-detail-history-item">
                                        <div class="gift-detail-history-top">
                                            <strong>
                                                {{ 'CMD-'.str_pad((string) $redemption->id, 6, '0', STR_PAD_LEFT) }}
                                                - {{ optional($redemption->requested_at)->format('d/m/Y') ?: '-' }}
                                            </strong>
                                            <span class="gift-detail-status is-{{ $redemption->status }}">
                                                {{ $statusLabels[$redemption->status] ?? \Illuminate\Support\Str::headline((string) $redemption->status) }}
                                            </span>
                                        </div>

                                        <p>
                                            Creee le {{ optional($redemption->requested_at)->format('d/m/Y \\a H:i') ?: '-' }}.
                                            @if ($redemption->reason)
                                                Motif communique: {{ $redemption->reason }}.
                                            @endif
                                        </p>

                                        <div class="gift-detail-history-meta">
                                            <span>{{ (int) $redemption->cost_points_snapshot }} pts debites</span>
                                            @if ($redemption->tracking_code)
                                                <span>
                                                    Suivi {{ $redemption->tracking_code }}
                                                    @if ($redemption->tracking_carrier)
                                                        {{ $redemption->tracking_carrier }}
                                                    @endif
                                                </span>
                                            @endif
                                            @if ($redemption->approved_at)
                                                <span>Validee {{ $redemption->approved_at->format('d/m/Y') }}</span>
                                            @endif
                                            @if ($redemption->shipped_at)
                                                <span>Expediee {{ $redemption->shipped_at->format('d/m/Y') }}</span>
                                            @endif
                                            @if ($redemption->delivered_at)
                                                <span>Livree {{ $redemption->delivered_at->format('d/m/Y') }}</span>
                                            @endif
                                        </div>

                                        <a href="{{ route('gifts.redemptions.show', $redemption->id) }}" class="tt-btn tt-btn-outline margin-top-15">
                                            <span data-hover="Detail">Voir le detail complet</span>
                                        </a>
                                    </article>
                                @endforeach
                            </div>
                        @elseif($isAuthenticated)
                            <div class="gift-detail-empty">
                                <p>Aucune demande pour ce cadeau pour le moment. Quand vous lancerez votre premiere demande, elle apparaitra ici avec son suivi.</p>
                            </div>
                        @else
                            <div class="gift-detail-empty">
                                <p>Connectez-vous pour voir votre historique de demandes cadeaux et leur suivi detaille.</p>
                                <div class="gift-detail-actions margin-top-20">
                                    <a href="{{ route('login') }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                        <span data-hover="Connexion">Se connecter</span>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="gift-detail-side-stack">
                        <aside class="gift-detail-surface gift-detail-side-card tt-anim-fadeinup">
                            <div class="gift-detail-section-heading">
                                <h3>Comment ca marche</h3>
                                <p>Le process reste simple et visible depuis votre espace membre.</p>
                            </div>

                            <ul>
                                <li>Vous lancez la demande depuis cette fiche cadeau.</li>
                                <li>Les points sont debites immediatement si le solde est suffisant.</li>
                                <li>Le stock est reserve des l envoi de la demande.</li>
                                <li>Un admin peut ensuite valider, refuser, expedier ou marquer la livraison.</li>
                            </ul>
                        </aside>

                        <aside class="gift-detail-surface gift-detail-side-card tt-anim-fadeinup">
                            <div class="gift-detail-section-heading">
                                <h3>Portefeuille points</h3>
                                <p>Consultez votre reserve de points avant de confirmer une nouvelle demande.</p>
                            </div>

                            @if($isAuthenticated)
                                <div class="gift-detail-wallet-highlight">
                                    <strong>{{ $walletBalance }} pts</strong>
                                    <p>
                                        Votre solde actuel permet
                                        {{ $canAffordGift ? 'de demander ce cadeau maintenant.' : 'de preparer une prochaine demande.' }}
                                    </p>
                                </div>

                                <div class="gift-detail-actions margin-top-30">
                                    <a href="{{ route('gifts.wallet') }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                        <span data-hover="Points">Voir mes points</span>
                                    </a>
                                </div>
                            @else
                                <div class="gift-detail-wallet-highlight">
                                    <strong>Connexion requise</strong>
                                    <p>Connectez-vous pour consulter votre solde points, ajouter au panier et enregistrer ce cadeau en favori.</p>
                                </div>
                                <div class="gift-detail-actions margin-top-30">
                                    <a href="{{ route('login') }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                        <span data-hover="Connexion">Se connecter</span>
                                    </a>
                                </div>
                            @endif
                        </aside>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
