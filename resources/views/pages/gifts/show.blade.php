@extends('marketing.layouts.template')

@section('title', ($gift->title ?? 'Cadeau').' | Cadeaux ERAH')
@section('meta_description', 'Detail cadeau ERAH, demande et suivi depuis le solde points.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('page_styles')
<style>
    .gift-détail-page {
        --gift-accent: #db0812;
        --gift-accent-soft: rgba(219, 8, 18, 0.16);
        --gift-border: rgba(255, 255, 255, 0.1);
        --gift-text-muted: rgba(255, 255, 255, 0.64);
        --gift-card-bg: linear-gradient(180deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.02));
    }

    .gift-détail-page #page-header .ph-caption-description {
        max-width: 720px;
    }

    .gift-détail-header-pills,
    .gift-détail-inline-pills {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .gift-détail-pill {
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

    .gift-détail-surface {
        border: 1px solid var(--gift-border);
        border-radius: 32px;
        background: var(--gift-card-bg);
        box-shadow: 0 28px 70px rgba(0, 0, 0, 0.26);
        backdrop-filter: blur(18px);
    }

    .gift-détail-hero-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.05fr) minmax(360px, 0.95fr);
        gap: 30px;
    }

    .gift-détail-visual {
        padding: 26px;
    }

    .gift-détail-visual-stage {
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

    .gift-détail-visual-stage::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0, 0, 0, 0.08), rgba(0, 0, 0, 0.54));
        pointer-events: none;
    }

    .gift-détail-visual-image {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        opacity: 0.94;
    }

    .gift-détail-visual-badge,
    .gift-détail-visual-stamp,
    .gift-détail-visual-copy {
        position: relative;
        z-index: 1;
    }

    .gift-détail-visual-badge {
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

    .gift-détail-visual-stamp {
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

    .gift-détail-visual-copy {
        display: grid;
        gap: 12px;
        padding: 32px;
    }

    .gift-détail-visual-copy strong {
        font-family: "Big Shoulders Display", sans-serif;
        font-size: clamp(52px, 6vw, 108px);
        line-height: 0.9;
        text-transform: uppercase;
    }

    .gift-détail-visual-copy p {
        max-width: 32ch;
        margin: 0;
        color: rgba(255, 255, 255, 0.74);
        font-size: 17px;
        line-height: 1.7;
    }

    .gift-détail-usp-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-top: 16px;
    }

    .gift-détail-usp-card {
        padding: 18px;
        border: 1px solid var(--gift-border);
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.03);
    }

    .gift-détail-usp-card strong {
        display: block;
        margin-bottom: 6px;
        color: #fff;
        font-size: 14px;
        font-weight: 600;
    }

    .gift-détail-usp-card p {
        margin: 0;
        color: var(--gift-text-muted);
        font-size: 14px;
        line-height: 1.6;
    }

    .gift-détail-panel {
        padding: 34px;
    }

    .gift-détail-eyebrow {
        display: inline-block;
        margin-bottom: 14px;
        color: rgba(255, 255, 255, 0.56);
        font-size: 12px;
        letter-spacing: 0.24em;
        text-transform: uppercase;
    }

    .gift-détail-panel h2 {
        margin: 0 0 12px;
        color: #fff;
        font-size: clamp(34px, 3.4vw, 60px);
        line-height: 0.94;
    }

    .gift-détail-panel > p {
        margin: 0 0 22px;
        color: var(--gift-text-muted);
        font-size: 16px;
        line-height: 1.75;
    }

    .gift-détail-metrics {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
        margin-top: 24px;
    }

    .gift-détail-metric {
        padding: 18px 20px;
        border: 1px solid var(--gift-border);
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.03);
    }

    .gift-détail-metric span {
        display: block;
        margin-bottom: 8px;
        color: rgba(255, 255, 255, 0.48);
        font-size: 11px;
        letter-spacing: 0.18em;
        text-transform: uppercase;
    }

    .gift-détail-metric strong {
        display: block;
        color: #fff;
        font-size: clamp(28px, 2vw, 42px);
        line-height: 1;
    }

    .gift-détail-state {
        margin-top: 20px;
        padding: 20px 22px;
        border-radius: 24px;
        border: 1px solid var(--gift-border);
        background: rgba(255, 255, 255, 0.03);
    }

    .gift-détail-state.is-limited {
        border-color: rgba(240, 173, 78, 0.35);
        background: rgba(240, 173, 78, 0.08);
    }

    .gift-détail-state.is-unavailable {
        border-color: rgba(219, 8, 18, 0.28);
        background: rgba(219, 8, 18, 0.08);
    }

    body.tt-lightmode-on .gift-détail-page {
        --gift-border: rgba(148, 163, 184, 0.24);
        --gift-text-muted: rgba(51, 65, 85, 0.82);
        --gift-card-bg: linear-gradient(180deg, rgba(255, 255, 255, 0.94), rgba(248, 250, 252, 0.9));
    }

    body.tt-lightmode-on .gift-détail-surface,
    body.tt-lightmode-on .gift-détail-usp-card,
    body.tt-lightmode-on .gift-détail-metric,
    body.tt-lightmode-on .gift-détail-state,
    body.tt-lightmode-on .gift-détail-latest,
    body.tt-lightmode-on .gift-détail-history-item,
    body.tt-lightmode-on .gift-détail-side-card,
    body.tt-lightmode-on .gift-détail-wallet-highlight {
        box-shadow: 0 24px 52px rgba(148, 163, 184, 0.16);
    }

    body.tt-lightmode-on .gift-détail-pill,
    body.tt-lightmode-on .gift-détail-visual-badge {
        background: rgba(255, 255, 255, 0.9);
        color: #0f172a;
    }

    body.tt-lightmode-on .gift-détail-panel h2,
    body.tt-lightmode-on .gift-détail-usp-card strong,
    body.tt-lightmode-on .gift-détail-metric strong,
    body.tt-lightmode-on .gift-détail-state strong,
    body.tt-lightmode-on .gift-détail-section-heading h3,
    body.tt-lightmode-on .gift-détail-history-top strong,
    body.tt-lightmode-on .gift-détail-wallet-highlight strong {
        color: #0f172a;
    }

    body.tt-lightmode-on .gift-détail-visual-stage {
        border-color: rgba(148, 163, 184, 0.22);
        background:
            radial-gradient(circle at 18% 20%, rgba(219, 8, 18, 0.18), transparent 38%),
            radial-gradient(circle at 82% 78%, rgba(15, 23, 42, 0.05), transparent 32%),
            linear-gradient(180deg, rgba(255, 255, 255, 0.36), rgba(226, 232, 240, 0.56));
    }

    body.tt-lightmode-on .gift-détail-visual-copy p,
    body.tt-lightmode-on .gift-détail-history-meta,
    body.tt-lightmode-on .gift-détail-empty,
    body.tt-lightmode-on .gift-détail-empty p {
        color: rgba(51, 65, 85, 0.82);
    }

    .gift-détail-state strong {
        display: block;
        margin-bottom: 8px;
        color: #fff;
        font-size: 18px;
    }

    .gift-détail-state p {
        margin: 0;
        color: var(--gift-text-muted);
        font-size: 15px;
        line-height: 1.7;
    }

    .gift-détail-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 26px;
    }

    .gift-détail-actions .tt-btn[disabled] {
        opacity: 0.45;
        pointer-events: none;
    }

    .gift-détail-latest {
        margin-top: 24px;
        padding: 20px 22px;
        border-radius: 24px;
        border: 1px solid var(--gift-border);
        background: rgba(255, 255, 255, 0.03);
    }

    .gift-détail-latest-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 10px;
    }

    .gift-détail-latest-head strong {
        color: #fff;
        font-size: 18px;
    }

    .gift-détail-status {
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

    .gift-détail-status.is-pending { background: rgba(255, 255, 255, 0.06); }
    .gift-détail-status.is-approved { background: rgba(56, 142, 60, 0.16); border-color: rgba(56, 142, 60, 0.28); }
    .gift-détail-status.is-rejected { background: rgba(219, 8, 18, 0.16); border-color: rgba(219, 8, 18, 0.28); }
    .gift-détail-status.is-shipped { background: rgba(14, 131, 205, 0.16); border-color: rgba(14, 131, 205, 0.28); }
    .gift-détail-status.is-delivered { background: rgba(139, 195, 74, 0.18); border-color: rgba(139, 195, 74, 0.3); }
    .gift-détail-status.is-cancelled { background: rgba(117, 117, 117, 0.18); border-color: rgba(117, 117, 117, 0.3); }

    .gift-détail-latest p,
    .gift-détail-latest ul,
    .gift-détail-empty p,
    .gift-détail-side-card p,
    .gift-détail-history-item p {
        margin: 0;
        color: var(--gift-text-muted);
        line-height: 1.7;
    }

    .gift-détail-content-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(320px, 0.46fr);
        gap: 24px;
    }

    .gift-détail-history,
    .gift-détail-side-card {
        padding: 30px;
    }

    .gift-détail-section-heading {
        margin-bottom: 20px;
    }

    .gift-détail-section-heading h3 {
        margin: 0 0 6px;
        color: #fff;
        font-size: clamp(28px, 2.3vw, 44px);
        line-height: 0.98;
    }

    .gift-détail-section-heading p {
        margin: 0;
        color: var(--gift-text-muted);
        line-height: 1.7;
    }

    .gift-détail-history-list {
        display: grid;
        gap: 14px;
    }

    .gift-détail-history-item {
        padding: 20px 22px;
        border: 1px solid var(--gift-border);
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.03);
    }

    .gift-détail-history-top {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 8px;
    }

    .gift-détail-history-top strong {
        color: #fff;
        font-size: 17px;
    }

    .gift-détail-history-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 14px;
    }

    .gift-détail-history-meta span {
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

    .gift-détail-side-stack {
        display: grid;
        gap: 24px;
    }

    .gift-détail-side-card ul {
        display: grid;
        gap: 10px;
        padding-left: 18px;
        margin: 16px 0 0;
        color: var(--gift-text-muted);
    }

    .gift-détail-wallet-highlight {
        display: grid;
        gap: 14px;
        margin-top: 18px;
    }

    .gift-détail-wallet-highlight strong {
        color: #fff;
        font-size: 34px;
        line-height: 1;
    }

    .gift-détail-empty {
        padding: 26px;
        border: 1px dashed rgba(255, 255, 255, 0.14);
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.025);
    }

    @media (max-width: 1399.98px) {
        .gift-détail-hero-grid {
            grid-template-columns: 1fr;
        }

        .gift-détail-visual-stage {
            min-height: 520px;
        }
    }

    @media (max-width: 1199.98px) {
        .gift-détail-content-grid,
        .gift-détail-usp-grid {
            grid-template-columns: 1fr;
        }

        .gift-détail-visual-copy strong {
            font-size: clamp(44px, 14vw, 80px);
        }
    }

    @media (max-width: 767.98px) {
        .gift-détail-visual,
        .gift-détail-panel,
        .gift-détail-history,
        .gift-détail-side-card {
            padding: 22px;
        }

        .gift-détail-visual-stage {
            min-height: 380px;
        }

        .gift-détail-visual-copy {
            padding: 24px;
        }

        .gift-détail-metrics {
            grid-template-columns: 1fr;
        }

        .gift-détail-actions .tt-btn,
        .gift-détail-actions a.tt-btn {
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
        : ($giftStock < 1 ? 'Rupture de stock' : ($giftStock <= 5 ? 'Stock limité' : 'Demande ouverte'));
    $availabilityCopy = ! $gift->is_active
        ? 'Ce cadeau est désactivé pour le moment. Il redevi
endra accessible dès sa républication.'
        : ($giftStock < 1
            ? 'Le stock actuel est épuisé. Revenez plus tard pour vérifier un réapprovisionnement.'
            : ($giftStock <= 5
                ? 'Il reste peu d\'exemplaires. Une fois votre demande envoyée, le stock est réservé immédiatement.'
                : 'La demande est disponible. Les points sont débités à la demande et le stock est bloqué.');
    $latestRedemption = ($myRecentRedemptions ?? collect())->first();
@endphp

<div class="gift-détail-page">
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
                        {{ $gift->description ?: 'Une récompense membre à débloquer avec vos points sur la plateforme.' }}
                    </div>
                    <div class="gift-détail-header-pills margin-top-30">
                        <span class="gift-détail-pill">{{ $giftCategoryLabel }}</span>
                        <span class="gift-détail-pill">{{ $giftCost }} pts</span>
                        <span class="gift-détail-pill">Stock {{ $giftStock }}</span>
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
                            Consultez les détails, verifiez votre solde et lancez votre demande depuis la fiche cadeau.
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
                <div class="gift-détail-hero-grid">
                    <div class="gift-détail-surface gift-détail-visual tt-anim-fadeinup">
                        <div class="gift-détail-visual-stage">
                            <span class="gift-détail-visual-badge">{{ $giftCategoryLabel }}</span>
                            <span class="gift-détail-visual-stamp">Cadeau</span>
                            <img class="gift-détail-visual-image" src="{{ $giftCover }}" alt="{{ $gift->title }}">

                            <div class="gift-détail-visual-copy">
                                <strong>{{ $gift->title }}</strong>
                                <p>
                                    {{ $gift->description ?: 'Une récompense membre pensée pour valoriser votre activité et vos points cumulés sur ERAH.' }}
                                </p>
                            </div>
                        </div>

                        <div class="gift-détail-usp-grid">
                            <div class="gift-détail-usp-card">
                                <strong>Débit immédiat</strong>
                                <p>Les points sont prélevés au moment de la demande.</p>
                            </div>
                            <div class="gift-détail-usp-card">
                                <strong>Stock réservé</strong>
                                <p>Le stock diminue dès que votre demande est envoyée.</p>
                            </div>
                            <div class="gift-détail-usp-card">
                                <strong>Suivi membre</strong>
                                <p>Vous retrouvez l\'\u00e9tat de vos demandes dans votre historique.</p>
                            </div>
                        </div>
                    </div>

                    <div class="gift-détail-surface gift-détail-panel tt-anim-fadeinup">
                        <span class="gift-détail-eyebrow">Fiche cadeau</span>
                        <h2>{{ $gift->title }}</h2>
                        <p>{{ $gift->description ?: 'Ce cadeau fait partie du catalogue accessible depuis votre espace membre.' }}</p>

                        <div class="gift-détail-inline-pills">
                            <span class="gift-détail-pill">{{ $giftCategoryLabel }}</span>
                            <span class="gift-détail-pill">{{ $giftCost }} points</span>
                            <span class="gift-détail-pill">{{ $giftStock > 0 ? 'Stock '.$giftStock : 'Rupture' }}</span>
                        </div>

                        <div class="gift-détail-metrics">
                            <div class="gift-détail-metric">
                                <span>{{ $isAuthenticated ? 'Votre solde' : 'Solde membre' }}</span>
                                <strong>{{ $walletBalance }}</strong>
                            </div>
                            <div class="gift-détail-metric">
                                <span>Cout cadeau</span>
                                <strong>{{ $giftCost }}</strong>
                            </div>
                            <div class="gift-détail-metric">
                                <span>Stock restant</span>
                                <strong>{{ $giftStock }}</strong>
                            </div>
                            <div class="gift-détail-metric">
                                <span>{{ $isAuthenticated ? ($canAffordGift ? '\u00c9tat achat' : 'Points manquants') : 'Connexion' }}</span>
                                <strong>{{ $isAuthenticated ? ($canAffordGift ? 'Pret' : $pointsMissing) : 'Requise' }}</strong>
                            </div>
                        </div>

                        <div class="gift-détail-state {{ $availabilityState !== 'available' ? 'is-'.$availabilityState : '' }}">
                            <strong>{{ $availabilityTitle }}</strong>
                            <p>
                                {{ $availabilityCopy }}
                                @if ($isAuthenticated && ! $canAffordGift)
                                    Il vous manque actuellement <strong>{{ $pointsMissing }} points</strong> pour lancer la demande.
                                @endif
                            </p>
                        </div>

                        <div class="gift-détail-actions">
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
                            <div class="gift-détail-latest">
                                <div class="gift-détail-latest-head">
                                    <strong>Derniere commande: {{ 'CMD-'.str_pad((string) $latestRedemption->id, 6, '0', STR_PAD_LEFT) }}</strong>
                                    <span class="gift-détail-status is-{{ $latestRedemption->status }}">
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
                                    <span data-hover="Détail">Voir le détail de la commande</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="tt-section padding-bottom-xlg-120 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="gift-détail-content-grid">
                    <div class="gift-détail-surface gift-détail-history tt-anim-fadeinup">
                        <div class="gift-détail-section-heading">
                            <h3>Mes demandes récentes</h3>
                            <p>Retrouvez le suivi de vos demandes pour ce cadeau, avec les étapes de validation, expédition ou refus.</p>
                        </div>

                        @if ($isAuthenticated && ($myRecentRedemptions ?? null) && $myRecentRedemptions->count())
                            <div class="gift-détail-history-list">
                                @foreach ($myRecentRedemptions as $redemption)
                                    <article class="gift-détail-history-item">
                                        <div class="gift-détail-history-top">
                                            <strong>
                                                {{ 'CMD-'.str_pad((string) $redemption->id, 6, '0', STR_PAD_LEFT) }}
                                                - {{ optional($redemption->requested_at)->format('d/m/Y') ?: '-' }}
                                            </strong>
                                            <span class="gift-détail-status is-{{ $redemption->status }}">
                                                {{ $statusLabels[$redemption->status] ?? \Illuminate\Support\Str::headline((string) $redemption->status) }}
                                            </span>
                                        </div>

                                        <p>
                                            Creee le {{ optional($redemption->requested_at)->format('d/m/Y \\a H:i') ?: '-' }}.
                                            @if ($redemption->reason)
                                                Motif communique: {{ $redemption->reason }}.
                                            @endif
                                        </p>

                                        <div class="gift-détail-history-meta">
                                            <span>{{ (int) $redemption->cost_points_snapshot }} pts débites</span>
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
                                                <span>Expédiée {{ $redemption->shipped_at->format('d/m/Y') }}</span>
                                            @endif
                                            @if ($redemption->delivered_at)
                                                <span>Livrée {{ $redemption->delivered_at->format('d/m/Y') }}</span>
                                            @endif
                                        </div>

                                        <a href="{{ route('gifts.redemptions.show', $redemption->id) }}" class="tt-btn tt-btn-outline margin-top-15">
                                            <span data-hover="Detail">Voir le détail complet</span>
                                        </a>
                                    </article>
                                @endforeach
                            </div>
                        @elseif($isAuthenticated)
                            <div class="gift-détail-empty">
                                <p>Aucune demande pour ce cadeau pour le moment. Quand vous lancerez votre première demande, elle apparaitra ici avec son suivi.</p>
                            </div>
                        @else
                            <div class="gift-détail-empty">
                                <p>Connectez-vous pour voir votre historique de demandes cadeaux et leur suivi détaillé.</p>
                                <div class="gift-détail-actions margin-top-20">
                                    <a href="{{ route('login') }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                        <span data-hover="Connexion">Se connecter</span>
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="gift-détail-side-stack">
                        <aside class="gift-détail-surface gift-détail-side-card tt-anim-fadeinup">
                            <div class="gift-détail-section-heading">
                                <h3>Comment ça marche</h3>
                                <p>Le processus reste simple et visible depuis votre espace membre.</p>
                            </div>

                            <ul>
                                <li>Vous lancez la demande depuis cette fiche cadeau.</li>
                                <li>Les points sont débites immediatement si le solde est suffisant.</li>
                                <li>Le stock est reserve des l envoi de la demande.</li>
                                <li>Un admin peut ensuite valider, refuser, expedier ou marquer la livraison.</li>
                            </ul>
                        </aside>

                        <aside class="gift-détail-surface gift-détail-side-card tt-anim-fadeinup">
                            <div class="gift-détail-section-heading">
                                <h3>Portefeuille de points</h3>
                                <p>Consultez votre reserve de points avant de confirmer une nouvelle demande.</p>
                            </div>

                            @if($isAuthenticated)
                                <div class="gift-détail-wallet-highlight">
                                    <strong>{{ $walletBalance }} pts</strong>
                                    <p>
                                        Votre solde actuel permet
                                        {{ $canAffordGift ? 'de demander ce cadeau maintenant.' : 'de préparer une prochaine demande.' }}
                                    </p>
                                </div>

                                <div class="gift-détail-actions margin-top-30">
                                    <a href="{{ route('gifts.wallet') }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                        <span data-hover="Points">Voir mes points</span>
                                    </a>
                                </div>
                            @else
                                <div class="gift-détail-wallet-highlight">
                                    <strong>Connexion requise</strong>
                                    <p>Connectez-vous pour consulter votre solde points, ajouter au panier et enregistrer ce cadeau en favori.</p>
                                </div>
                                <div class="gift-détail-actions margin-top-30">
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
