@extends('marketing.layouts.template')

@section('title', 'Panier cadeaux | ERAH')
@section('meta_description', 'Panier cadeaux ERAH: quantites, total points et validation de commande.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('page_styles')
<style>
    .gift-cart-page {
        --gift-cart-border: rgba(255, 255, 255, 0.12);
        --gift-cart-muted: rgba(255, 255, 255, 0.72);
    }

    .gift-cart-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(320px, 0.45fr);
        gap: 20px;
    }

    .gift-cart-card {
        padding: 22px;
        border: 1px solid var(--gift-cart-border);
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.03);
    }

    .gift-cart-table {
        width: 100%;
        border-collapse: collapse;
    }

    .gift-cart-table th,
    .gift-cart-table td {
        padding: 12px 10px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        text-align: left;
        vertical-align: middle;
        color: #fff;
    }

    .gift-cart-table th {
        color: rgba(255, 255, 255, 0.64);
        font-size: 11px;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .gift-cart-table tr:last-child td {
        border-bottom: none;
    }

    .gift-cart-gift {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .gift-cart-gift img {
        width: 56px;
        height: 56px;
        border-radius: 10px;
        object-fit: cover;
        border: 1px solid rgba(255, 255, 255, 0.14);
    }

    .gift-cart-gift strong {
        display: block;
        color: #fff;
    }

    .gift-cart-gift small {
        color: var(--gift-cart-muted);
        font-size: 12px;
    }

    .gift-cart-status {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        padding: 6px 10px;
        border-radius: 999px;
        border: 1px solid var(--gift-cart-border);
        background: rgba(255, 255, 255, 0.04);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #fff;
    }

    .gift-cart-status.is-ok {
        border-color: rgba(90, 179, 61, 0.4);
        background: rgba(90, 179, 61, 0.16);
    }

    .gift-cart-status.is-ko {
        border-color: rgba(214, 78, 77, 0.35);
        background: rgba(214, 78, 77, 0.16);
    }

    .gift-cart-inline {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }

    .gift-cart-input {
        width: 92px;
        min-height: 40px;
        padding: 8px 10px;
        border-radius: 10px;
        border: 1px solid var(--gift-cart-border);
        background: rgba(0, 0, 0, 0.2);
        color: #fff;
    }

    .gift-cart-summary-list {
        display: grid;
        gap: 10px;
        margin-top: 14px;
    }

    .gift-cart-summary-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        color: #fff;
    }

    .gift-cart-summary-row span {
        color: var(--gift-cart-muted);
        font-size: 13px;
    }

    .gift-cart-summary-row strong {
        font-size: 18px;
    }

    .gift-cart-warning {
        margin-top: 14px;
        padding: 12px 14px;
        border-radius: 12px;
        border: 1px solid rgba(214, 78, 77, 0.35);
        background: rgba(214, 78, 77, 0.14);
        color: #fff;
        font-size: 14px;
        line-height: 1.6;
    }

    .gift-cart-empty {
        padding: 22px;
        border: 1px dashed var(--gift-cart-border);
        border-radius: 14px;
        color: var(--gift-cart-muted);
    }

    body.tt-lightmode-on .gift-cart-page {
        --gift-cart-border: rgba(148, 163, 184, 0.24);
        --gift-cart-muted: rgba(51, 65, 85, 0.78);
    }

    body.tt-lightmode-on .gift-cart-card {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.94), rgba(248, 250, 252, 0.88));
        box-shadow: 0 22px 48px rgba(148, 163, 184, 0.16);
    }

    body.tt-lightmode-on .gift-cart-table th {
        color: rgba(71, 85, 105, 0.82);
    }

    body.tt-lightmode-on .gift-cart-table td,
    body.tt-lightmode-on .gift-cart-gift strong,
    body.tt-lightmode-on .gift-cart-summary-row {
        color: #0f172a;
    }

    body.tt-lightmode-on .gift-cart-input {
        background: rgba(255, 255, 255, 0.96);
        color: #0f172a;
    }

    @media (max-width: 1199.98px) {
        .gift-cart-layout {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 991.98px) {
        .gift-cart-table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }
    }
</style>
@endsection

@section('content')
    @php
        $lineItems = $summary['line_items'] ?? collect();
        $totalPoints = (int) ($summary['total_points'] ?? 0);
        $walletBalance = (int) ($summary['wallet_balance'] ?? 0);
        $missingPoints = (int) ($summary['missing_points'] ?? 0);
        $canCheckout = (bool) ($summary['can_checkout'] ?? false);
        $idempotencyKey = 'gift-cart-'.auth()->id().'-'.now()->timestamp.'-'.\Illuminate\Support\Str::random(8);
    @endphp

    <div class="gift-cart-page">
        <div id="page-header" class="ph-full ph-center ph-cap-xxlg ph-caption-parallax">
            <div class="page-header-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">Cadeaux</h2>
                        <h1 class="ph-caption-title">Panier cadeaux</h1>
                        <div class="ph-caption-description max-width-700">
                            Verifiez vos articles, ajustez les quantites et validez votre commande en une seule fois.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="tt-page-content">
            <div class="tt-section padding-top-80 padding-bottom-100 border-top">
                <div class="tt-section-inner tt-wrap max-width-1800">
                    <div class="gift-cart-layout">
                        <section class="gift-cart-card">
                            <div class="tt-heading tt-heading-sm no-margin">
                                <h3 class="tt-heading-title">Articles du panier</h3>
                            </div>

                            @if($lineItems->count())
                                <table class="gift-cart-table margin-top-15">
                                    <thead>
                                        <tr>
                                            <th>Cadeau</th>
                                            <th>Cout unitaire</th>
                                            <th>Quantite</th>
                                            <th>Total ligne</th>
                                            <th>Disponibilite</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lineItems as $lineItem)
                                            @php
                                                $gift = $lineItem['gift'];
                                                $cartItem = $lineItem['cart_item'];
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="gift-cart-gift">
                                                        <img src="{{ $gift?->image_url ?: '/template/assets/img/logo.png' }}" alt="{{ $gift?->title ?: 'Cadeau indisponible' }}">
                                                        <div>
                                                            <strong>{{ $gift?->title ?: 'Cadeau introuvable' }}</strong>
                                                            <small>ID #{{ $gift?->id ?: $cartItem->gift_id }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ (int) ($gift?->cost_points ?? 0) }} pts</td>
                                                <td>
                                                    <form method="POST" action="{{ route('gifts.cart.update', $cartItem->id) }}" class="gift-cart-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input class="gift-cart-input" type="number" min="1" max="50" name="quantity" value="{{ (int) $cartItem->quantity }}">
                                                        <button type="submit" class="tt-btn tt-btn-outline">
                                                            <span data-hover="Maj">Maj</span>
                                                        </button>
                                                    </form>
                                                </td>
                                                <td>{{ (int) $lineItem['line_total'] }} pts</td>
                                                <td>
                                                    <span class="gift-cart-status {{ $lineItem['is_available'] ? 'is-ok' : 'is-ko' }}">
                                                        {{ $lineItem['status_copy'] }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="gift-cart-inline">
                                                        @if($gift)
                                                            <a href="{{ route('gifts.show', $gift->id) }}" class="tt-btn tt-btn-outline">
                                                                <span data-hover="Fiche">Fiche</span>
                                                            </a>
                                                        @endif
                                                        <form method="POST" action="{{ route('gifts.cart.remove', $cartItem->id) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="tt-btn tt-btn-secondary">
                                                                <span data-hover="Retirer">Retirer</span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="gift-cart-empty margin-top-20">
                                    Votre panier est vide pour le moment.
                                </div>
                            @endif
                        </section>

                        <aside class="gift-cart-card">
                            <div class="tt-heading tt-heading-sm no-margin">
                                <h3 class="tt-heading-title">Resume commande</h3>
                            </div>

                            <div class="gift-cart-summary-list">
                                <div class="gift-cart-summary-row">
                                    <span>Sous-total</span>
                                    <strong>{{ $totalPoints }} pts</strong>
                                </div>
                                <div class="gift-cart-summary-row">
                                    <span>Total panier</span>
                                    <strong>{{ $totalPoints }} pts</strong>
                                </div>
                                <div class="gift-cart-summary-row">
                                    <span>Solde disponible</span>
                                    <strong>{{ $walletBalance }} pts</strong>
                                </div>
                                <div class="gift-cart-summary-row">
                                    <span>Points manquants</span>
                                    <strong>{{ $missingPoints }} pts</strong>
                                </div>
                            </div>

                            @if($missingPoints > 0)
                                <div class="gift-cart-warning">
                                    Solde insuffisant: il vous manque {{ $missingPoints }} points pour valider ce panier.
                                </div>
                            @endif

                            @if(!$canCheckout && $lineItems->count() > 0 && $missingPoints === 0)
                                <div class="gift-cart-warning">
                                    Un ou plusieurs cadeaux ne sont plus disponibles. Ajustez le panier puis reessayez.
                                </div>
                            @endif

                            <form method="POST" action="{{ route('gifts.cart.checkout') }}" class="margin-top-20">
                                @csrf
                                <input type="hidden" name="idempotency_key" value="{{ $idempotencyKey }}">
                                <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item" {{ $canCheckout ? '' : 'disabled' }}>
                                    <span data-hover="Valider le panier">Valider le panier</span>
                                </button>
                            </form>

                            <div class="gift-cart-inline margin-top-15">
                                <a href="{{ route('gifts.index') }}" class="tt-btn tt-btn-outline">
                                    <span data-hover="Catalogue">Retour catalogue</span>
                                </a>
                                <a href="{{ route('gifts.favorites') }}" class="tt-btn tt-btn-outline">
                                    <span data-hover="Favoris">Mes favoris</span>
                                </a>
                            </div>
                        </aside>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script src="/template/assets/vendor/jquery/jquery.min.js"></script>
    <script src="/template/assets/vendor/gsap/gsap.min.js"></script>
    <script src="/template/assets/vendor/gsap/ScrollToPlugin.min.js"></script>
    <script src="/template/assets/vendor/gsap/ScrollTrigger.min.js"></script>
    <script src="/template/assets/vendor/lenis.min.js"></script>
    <script src="/template/assets/vendor/isotope/imagesloaded.pkgd.min.js"></script>
    <script src="/template/assets/vendor/isotope/isotope.pkgd.min.js"></script>
    <script src="/template/assets/vendor/isotope/packery-mode.pkgd.min.js"></script>
    <script src="/template/assets/vendor/fancybox/js/fancybox.umd.js"></script>
    <script src="/template/assets/vendor/swiper/js/swiper-bundle.min.js"></script>
    <script src="/template/assets/js/theme.js"></script>
@endsection
