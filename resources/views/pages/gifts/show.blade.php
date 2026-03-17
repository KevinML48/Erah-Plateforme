@extends('marketing.layouts.template')

@section('title', $gift->metaTitle())
@section('meta_description', $gift->metaDescription())
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('page_styles')
<style>
    .gift-show-page {
        --gift-border: rgba(255, 255, 255, 0.1);
        --gift-muted: rgba(255, 255, 255, 0.68);
        --gift-surface: linear-gradient(180deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.02));
    }

    .gift-show-page .gift-breadcrumbs {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 22px;
        color: var(--gift-muted);
        font-size: 13px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .gift-show-page .gift-breadcrumbs a {
        color: inherit;
        text-decoration: none;
    }

    .gift-show-page .gift-hero-grid,
    .gift-show-page .gift-content-grid {
        display: grid;
        gap: 26px;
    }

    .gift-show-page .gift-hero-grid {
        grid-template-columns: minmax(0, 1.2fr) minmax(320px, 0.8fr);
        align-items: start;
    }

    .gift-show-page .gift-content-grid {
        grid-template-columns: minmax(0, 1fr) minmax(300px, 0.8fr);
        margin-top: 26px;
    }

    .gift-show-page .gift-surface {
        border: 1px solid var(--gift-border);
        border-radius: 28px;
        background: var(--gift-surface);
        box-shadow: 0 30px 70px rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(18px);
    }

    .gift-show-page .gift-gallery-card,
    .gift-show-page .gift-summary-card,
    .gift-show-page .gift-block,
    .gift-show-page .gift-sidebar-card,
    .gift-show-page .gift-similar-card,
    .gift-show-page .gift-history-card {
        padding: 26px;
    }

    .gift-show-page .gift-gallery-main {
        position: relative;
        overflow: hidden;
        border-radius: 22px;
        min-height: 520px;
        background: radial-gradient(circle at top left, rgba(219, 8, 18, 0.18), transparent 32%), linear-gradient(180deg, rgba(22, 22, 22, 0.88), rgba(8, 8, 8, 0.96));
    }

    .gift-show-page .gift-gallery-main img {
        width: 100%;
        height: 100%;
        min-height: 520px;
        object-fit: cover;
        display: block;
    }

    .gift-show-page .gift-gallery-badges,
    .gift-show-page .gift-summary-pills,
    .gift-show-page .gift-thumb-list,
    .gift-show-page .gift-cta-stack,
    .gift-show-page .gift-history-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .gift-show-page .gift-gallery-badges {
        position: absolute;
        top: 18px;
        left: 18px;
        right: 18px;
        justify-content: space-between;
    }

    .gift-show-page .gift-badge,
    .gift-show-page .gift-pill,
    .gift-show-page .gift-meta-chip,
    .gift-show-page .gift-status-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 36px;
        padding: 8px 14px;
        border-radius: 999px;
        border: 1px solid var(--gift-border);
        background: rgba(0, 0, 0, 0.36);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .gift-show-page .gift-status-chip.is-available {
        background: rgba(76, 191, 121, 0.18);
        border-color: rgba(76, 191, 121, 0.34);
    }

    .gift-show-page .gift-status-chip.is-low,
    .gift-show-page .gift-status-chip.is-insufficient_points,
    .gift-show-page .gift-status-chip.is-supporter_required {
        background: rgba(243, 185, 79, 0.18);
        border-color: rgba(243, 185, 79, 0.32);
    }

    .gift-show-page .gift-status-chip.is-unavailable,
    .gift-show-page .gift-status-chip.is-out,
    .gift-show-page .gift-status-chip.is-auth_required,
    .gift-show-page .gift-status-chip.is-already_owned,
    .gift-show-page .gift-status-chip.is-already_ordered {
        background: rgba(219, 8, 18, 0.18);
        border-color: rgba(219, 8, 18, 0.38);
    }

    .gift-show-page .gift-thumb-list {
        margin-top: 14px;
    }

    .gift-show-page .gift-thumb-list img {
        width: 92px;
        height: 92px;
        border-radius: 18px;
        object-fit: cover;
        border: 1px solid var(--gift-border);
    }

    .gift-show-page .gift-summary-card h1,
    .gift-show-page .gift-block h2,
    .gift-show-page .gift-sidebar-card h3,
    .gift-show-page .gift-history-card h3 {
        color: #fff;
    }

    .gift-show-page .gift-summary-card h1 {
        margin: 0 0 14px;
        font-size: clamp(36px, 4vw, 64px);
        line-height: 0.95;
    }

    .gift-show-page .gift-summary-card p,
    .gift-show-page .gift-block p,
    .gift-show-page .gift-sidebar-card p,
    .gift-show-page .gift-history-card p,
    .gift-show-page .gift-similar-card p,
    .gift-show-page .gift-empty {
        color: var(--gift-muted);
        line-height: 1.75;
    }

    .gift-show-page .gift-meta-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-top: 22px;
    }

    .gift-show-page .gift-meta-card,
    .gift-show-page .gift-state-card,
    .gift-show-page .gift-list-card {
        padding: 18px 20px;
        border: 1px solid var(--gift-border);
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.03);
    }

    .gift-show-page .gift-meta-card span,
    .gift-show-page .gift-state-card span {
        display: block;
        margin-bottom: 8px;
        color: rgba(255, 255, 255, 0.52);
        font-size: 11px;
        letter-spacing: 0.16em;
        text-transform: uppercase;
    }

    .gift-show-page .gift-meta-card strong,
    .gift-show-page .gift-state-card strong {
        color: #fff;
        font-size: clamp(24px, 2vw, 34px);
        line-height: 1.1;
    }

    .gift-show-page .gift-state-card {
        margin-top: 18px;
    }

    .gift-show-page .gift-cta-stack {
        margin-top: 24px;
    }

    .gift-show-page .gift-cta-stack form {
        margin: 0;
    }

    .gift-show-page .gift-cta-stack .tt-btn[disabled] {
        opacity: 0.45;
        pointer-events: none;
    }

    .gift-show-page .gift-block + .gift-block,
    .gift-show-page .gift-sidebar-card + .gift-sidebar-card,
    .gift-show-page .gift-history-card + .gift-block {
        margin-top: 22px;
    }

    .gift-show-page .gift-block ul,
    .gift-show-page .gift-sidebar-card ul {
        margin: 0;
        padding-left: 18px;
        color: var(--gift-muted);
        line-height: 1.8;
    }

    .gift-show-page .gift-sidebar-sticky {
        position: sticky;
        top: 24px;
    }

    .gift-show-page .gift-history-list,
    .gift-show-page .gift-similar-grid {
        display: grid;
        gap: 14px;
    }

    .gift-show-page .gift-history-item,
    .gift-show-page .gift-similar-card {
        padding: 18px 20px;
        border: 1px solid var(--gift-border);
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.03);
    }

    .gift-show-page .gift-history-top,
    .gift-show-page .gift-similar-top {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: center;
        margin-bottom: 10px;
    }

    .gift-show-page .gift-similar-card img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        border-radius: 16px;
        margin-bottom: 14px;
    }

    .gift-show-page .gift-similar-card h3 {
        margin: 0 0 8px;
        color: #fff;
        font-size: 22px;
    }

    body.tt-lightmode-on .gift-show-page {
        --gift-border: rgba(148, 163, 184, 0.24);
        --gift-muted: rgba(51, 65, 85, 0.84);
        --gift-surface: linear-gradient(180deg, rgba(255, 255, 255, 0.95), rgba(248, 250, 252, 0.9));
    }

    body.tt-lightmode-on .gift-show-page .gift-badge,
    body.tt-lightmode-on .gift-show-page .gift-pill,
    body.tt-lightmode-on .gift-show-page .gift-meta-chip,
    body.tt-lightmode-on .gift-show-page .gift-status-chip {
        background: rgba(255, 255, 255, 0.94);
        color: #0f172a;
    }

    body.tt-lightmode-on .gift-show-page .gift-summary-card h1,
    body.tt-lightmode-on .gift-show-page .gift-block h2,
    body.tt-lightmode-on .gift-show-page .gift-sidebar-card h3,
    body.tt-lightmode-on .gift-show-page .gift-history-card h3,
    body.tt-lightmode-on .gift-show-page .gift-meta-card strong,
    body.tt-lightmode-on .gift-show-page .gift-state-card strong,
    body.tt-lightmode-on .gift-show-page .gift-similar-card h3 {
        color: #0f172a;
    }

    @media (max-width: 1199.98px) {
        .gift-show-page .gift-hero-grid,
        .gift-show-page .gift-content-grid {
            grid-template-columns: 1fr;
        }

        .gift-show-page .gift-sidebar-sticky {
            position: static;
        }
    }

    @media (max-width: 767.98px) {
        .gift-show-page .gift-gallery-main,
        .gift-show-page .gift-gallery-main img {
            min-height: 340px;
        }

        .gift-show-page .gift-meta-grid {
            grid-template-columns: 1fr;
        }

        .gift-show-page .gift-gallery-card,
        .gift-show-page .gift-summary-card,
        .gift-show-page .gift-block,
        .gift-show-page .gift-sidebar-card,
        .gift-show-page .gift-similar-card,
        .gift-show-page .gift-history-card {
            padding: 20px;
        }
    }
</style>
@endsection

@section('content')
    @php
        $giftIndexRouteName = $giftIndexRouteName ?? 'gifts.index';
        $giftShowRouteName = $giftShowRouteName ?? 'gifts.show';
    @endphp

    <div id="tt-page-content" class="gift-show-page">
        <div class="tt-section padding-top-xlg-140 padding-bottom-80">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <nav class="gift-breadcrumbs" aria-label="Fil d Ariane cadeau">
                    <a href="{{ route($giftIndexRouteName) }}">Catalogue cadeaux</a>
                    <span>/</span>
                    <span>{{ $giftCategoryLabel }}</span>
                    <span>/</span>
                    <span>{{ $gift->title }}</span>
                </nav>

                <div class="gift-hero-grid">
                    <section class="gift-surface gift-gallery-card tt-anim-fadeinup">
                        <div class="gift-gallery-main">
                            <div class="gift-gallery-badges">
                                <span class="gift-badge">{{ $giftCategoryLabel }}</span>
                                <span class="gift-badge">{{ $giftTypeLabel }}</span>
                            </div>
                            <img src="{{ $giftCover }}" alt="{{ $gift->title }}">
                        </div>

                        @if(count($galleryImages) > 1)
                            <div class="gift-thumb-list" aria-label="Galerie cadeau">
                                @foreach($galleryImages as $image)
                                    <a href="{{ $image }}" data-fancybox="gift-gallery">
                                        <img src="{{ $image }}" alt="{{ $gift->title }}">
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </section>

                    <section class="gift-surface gift-summary-card tt-anim-fadeinup">
                        <div class="gift-summary-pills">
                            <span class="gift-pill">{{ $giftCost }} points</span>
                            <span class="gift-pill">{{ $giftStock > 0 ? 'Stock '.$giftStock : 'Rupture' }}</span>
                            <span class="gift-pill">{{ $giftDeliveryLabel }}</span>
                            @if($isSupporterOnly)
                                <span class="gift-pill">Supporter requis</span>
                            @endif
                            @if(! $gift->isRepeatable())
                                <span class="gift-pill">Achat unique</span>
                            @endif
                        </div>

                        <h1>{{ $gift->title }}</h1>
                        <p>{{ $shortDescription }}</p>

                        <div class="gift-meta-grid">
                            <div class="gift-meta-card">
                                <span>{{ $isAuthenticated ? 'Votre solde' : 'Solde membre' }}</span>
                                <strong>{{ $walletBalance }}</strong>
                            </div>
                            <div class="gift-meta-card">
                                <span>Cout du cadeau</span>
                                <strong>{{ $giftCost }}</strong>
                            </div>
                            <div class="gift-meta-card">
                                <span>Disponibilite</span>
                                <strong>{{ $availabilityKey === 'low' ? 'Limitee' : ($giftStock > 0 ? 'Ouverte' : 'Fermee') }}</strong>
                            </div>
                            <div class="gift-meta-card">
                                <span>{{ $pointsMissing > 0 ? 'Points manquants' : 'Etat achat' }}</span>
                                <strong>{{ $pointsMissing > 0 ? $pointsMissing : ($isRedeemable ? 'Pret' : 'Bloque') }}</strong>
                            </div>
                        </div>

                        <div class="gift-state-card">
                            <span>Etat actuel</span>
                            <div class="gift-history-top">
                                <strong>{{ $availabilityTitle }}</strong>
                                <span class="gift-status-chip is-{{ $availabilityState }}">{{ str_replace('_', ' ', $availabilityState) }}</span>
                            </div>
                            <p>{{ $availabilityCopy }}</p>
                        </div>

                        <div class="gift-cta-stack">
                            @if($isAuthenticated)
                                <form method="POST" action="{{ route('gifts.redeem', $gift->id) }}">
                                    @csrf
                                    <input type="hidden" name="idempotency_key" value="redeem-{{ auth()->id() }}-{{ $gift->id }}-{{ now()->timestamp }}">
                                    <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item" {{ $isRedeemable ? '' : 'disabled' }}>
                                        <span data-hover="Acheter / echanger">Acheter / echanger ce cadeau</span>
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('gifts.cart.add', $gift->id) }}">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item" {{ $canAddToCart ? '' : 'disabled' }}>
                                        <span data-hover="Panier">Ajouter au panier{{ $cartItemQuantity > 0 ? ' ('.$cartItemQuantity.')' : '' }}</span>
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('gifts.favorites.toggle', $gift->id) }}">
                                    @csrf
                                    <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item">
                                        <span data-hover="{{ $isFavorited ? 'Retirer favoris' : 'Ajouter favoris' }}">{{ $isFavorited ? 'Retirer favoris' : 'Ajouter favoris' }}</span>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('login') }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                    <span data-hover="Connexion">Se connecter pour acheter</span>
                                </a>
                            @endif

                            <a href="{{ route($giftIndexRouteName) }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                <span data-hover="Retour">Retour vers le shop cadeaux</span>
                            </a>
                        </div>

                        @if($latestRedemption)
                            <div class="gift-list-card margin-top-30">
                                <div class="gift-history-top">
                                    <strong>{{ 'CMD-'.str_pad((string) $latestRedemption->id, 6, '0', STR_PAD_LEFT) }}</strong>
                                    <span class="gift-status-chip is-{{ $latestRedemption->status }}">{{ $statusLabels[$latestRedemption->status] ?? \Illuminate\Support\Str::headline((string) $latestRedemption->status) }}</span>
                                </div>
                                <p>Derniere commande liee a ce cadeau, demandee le {{ optional($latestRedemption->requested_at)->format('d/m/Y \a H:i') ?: '-' }}.</p>
                                <a href="{{ route('gifts.redemptions.show', $latestRedemption->id) }}" class="tt-btn tt-btn-outline margin-top-15">
                                    <span data-hover="Suivi">Voir le suivi de commande</span>
                                </a>
                            </div>
                        @endif
                    </section>
                </div>

                <div class="gift-content-grid">
                    <div>
                        <article class="gift-surface gift-block tt-anim-fadeinup">
                            <h2>Description detaillee</h2>
                            <p>{{ $longDescription }}</p>
                        </article>

                        <article class="gift-surface gift-block tt-anim-fadeinup">
                            <h2>Conditions et eligibilite</h2>
                            @if(!empty($giftConditions))
                                <ul>
                                    @foreach($giftConditions as $condition)
                                        <li>{{ $condition }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <ul>
                                    <li>Le cadeau doit etre actif et encore disponible en stock.</li>
                                    <li>Le debit points est effectue immediatement au moment de la demande.</li>
                                    <li>Les regles de possession ou d achat unique sont controlees automatiquement.</li>
                                    @if($isSupporterOnly)
                                        <li>Un statut supporter actif est requis pour ce cadeau.</li>
                                    @endif
                                </ul>
                            @endif

                            @if($giftEligibilityDetails)
                                <p class="margin-top-20">{{ $giftEligibilityDetails }}</p>
                            @endif
                        </article>

                        <article class="gift-surface gift-block tt-anim-fadeinup">
                            <h2>Remise et livraison</h2>
                            <p>{{ $giftDeliveryDetails ?: 'Le mode de remise depend du type du cadeau: attribution immediate sur le profil, traitement manuel par l equipe, ou expedition si le cadeau est physique.' }}</p>
                        </article>

                        <article class="gift-surface gift-history-card tt-anim-fadeinup">
                            <h3>Historique lie a ce cadeau</h3>
                            <p>Retrouvez ici vos commandes deja lancees pour cette fiche cadeau.</p>

                            @if($isAuthenticated && $myRecentRedemptions->count())
                                <div class="gift-history-list margin-top-20">
                                    @foreach($myRecentRedemptions as $redemption)
                                        <article class="gift-history-item">
                                            <div class="gift-history-top">
                                                <strong>{{ 'CMD-'.str_pad((string) $redemption->id, 6, '0', STR_PAD_LEFT) }}</strong>
                                                <span class="gift-status-chip is-{{ $redemption->status }}">{{ $statusLabels[$redemption->status] ?? \Illuminate\Support\Str::headline((string) $redemption->status) }}</span>
                                            </div>
                                            <p>Demandee le {{ optional($redemption->requested_at)->format('d/m/Y \a H:i') ?: '-' }}.</p>
                                            <div class="gift-history-meta">
                                                <span class="gift-meta-chip">{{ (int) $redemption->cost_points_snapshot }} pts</span>
                                                @if($redemption->tracking_code)
                                                    <span class="gift-meta-chip">Suivi {{ $redemption->tracking_code }}</span>
                                                @endif
                                                @if($redemption->tracking_carrier)
                                                    <span class="gift-meta-chip">{{ $redemption->tracking_carrier }}</span>
                                                @endif
                                            </div>
                                            <a href="{{ route('gifts.redemptions.show', $redemption->id) }}" class="tt-btn tt-btn-outline margin-top-15">
                                                <span data-hover="Detail">Ouvrir la commande</span>
                                            </a>
                                        </article>
                                    @endforeach
                                </div>
                            @elseif($isAuthenticated)
                                <div class="gift-empty margin-top-20">Aucune commande n a encore ete lancee pour ce cadeau.</div>
                            @else
                                <div class="gift-empty margin-top-20">Connectez-vous pour suivre vos commandes et voir votre historique sur cette fiche cadeau.</div>
                            @endif
                        </article>
                    </div>

                    <aside class="gift-sidebar-sticky">
                        <div class="gift-surface gift-sidebar-card tt-anim-fadeinup">
                            <h3>Resume achat</h3>
                            <ul>
                                <li>Validation metier et stock en base avant creation de la commande.</li>
                                <li>Debit points et decrement stock realises dans une transaction DB securisee.</li>
                                <li>Protection contre re-soumission via cle idempotente sur l achat direct.</li>
                                <li>Historique et suivi disponibles ensuite depuis vos commandes cadeaux.</li>
                            </ul>
                        </div>

                        <div class="gift-surface gift-sidebar-card tt-anim-fadeinup">
                            <h3>Cadeaux similaires</h3>
                            @if($similarGifts->count())
                                <div class="gift-similar-grid margin-top-20">
                                    @foreach($similarGifts as $similarGift)
                                        <article class="gift-similar-card">
                                            <img src="{{ $similarGift->primaryImageUrl() }}" alt="{{ $similarGift->title }}">
                                            <div class="gift-similar-top">
                                                <span class="gift-meta-chip">{{ $similarGift->launchCatalogCategoryLabel() ?: $giftCategoryLabel }}</span>
                                                <span class="gift-meta-chip">{{ (int) $similarGift->cost_points }} pts</span>
                                            </div>
                                            <h3>{{ $similarGift->title }}</h3>
                                            <p>{{ $similarGift->shortDescription() !== '' ? $similarGift->shortDescription() : \Illuminate\Support\Str::limit((string) $similarGift->description, 100) }}</p>
                                            <a href="{{ route($giftShowRouteName, $similarGift->routeIdentifier()) }}" class="tt-btn tt-btn-outline margin-top-15">
                                                <span data-hover="Fiche">Voir la fiche cadeau</span>
                                            </a>
                                        </article>
                                    @endforeach
                                </div>
                            @else
                                <p class="margin-top-20">Aucun cadeau similaire n est remonte pour le moment.</p>
                            @endif
                        </div>
                    </aside>
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