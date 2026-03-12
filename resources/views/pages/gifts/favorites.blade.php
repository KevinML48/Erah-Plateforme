@extends('marketing.layouts.template')

@section('title', 'Mes favoris cadeaux | ERAH')
@section('meta_description', 'Retrouvez vos cadeaux favoris et ajoutez-les rapidement au panier.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('page_styles')
<style>
    .gift-favorites-page {
        --gift-favorites-border: rgba(255, 255, 255, 0.12);
        --gift-favorites-muted: rgba(255, 255, 255, 0.72);
    }

    .gift-favorites-header {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 14px;
    }

    .gift-favorites-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
    }

    .gift-favorites-card {
        padding: 18px;
        border: 1px solid var(--gift-favorites-border);
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.03);
        display: grid;
        gap: 12px;
    }

    .gift-favorites-card img {
        width: 100%;
        aspect-ratio: 4 / 3;
        border-radius: 12px;
        object-fit: cover;
        border: 1px solid rgba(255, 255, 255, 0.14);
    }

    .gift-favorites-card h3 {
        margin: 0;
        color: #fff;
        font-size: 24px;
        line-height: 1;
    }

    .gift-favorites-card p {
        margin: 0;
        color: var(--gift-favorites-muted);
        line-height: 1.65;
    }

    .gift-favorites-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .gift-favorites-pill {
        display: inline-flex;
        align-items: center;
        min-height: 30px;
        padding: 6px 10px;
        border-radius: 999px;
        border: 1px solid var(--gift-favorites-border);
        background: rgba(255, 255, 255, 0.04);
        color: #fff;
        font-size: 11px;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .gift-favorites-pill.is-out {
        border-color: rgba(214, 78, 77, 0.35);
        background: rgba(214, 78, 77, 0.16);
    }

    .gift-favorites-pill.is-ok {
        border-color: rgba(90, 179, 61, 0.4);
        background: rgba(90, 179, 61, 0.16);
    }

    .gift-favorites-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .gift-favorites-summary {
        margin-top: 14px;
        color: var(--gift-favorites-muted);
    }

    .gift-favorites-empty {
        padding: 24px;
        border: 1px dashed var(--gift-favorites-border);
        border-radius: 16px;
        color: var(--gift-favorites-muted);
    }

    @media (max-width: 1199.98px) {
        .gift-favorites-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767.98px) {
        .gift-favorites-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
    @php
        $cartSummary = $cartSummary ?? [];
        $cartItems = (int) ($cartSummary['total_items'] ?? 0);
        $cartTotal = (int) ($cartSummary['total_points'] ?? 0);
    @endphp

    <div class="gift-favorites-page">
        <div id="page-header" class="ph-full ph-center ph-cap-xxlg ph-caption-parallax">
            <div class="page-header-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">Cadeaux</h2>
                        <h1 class="ph-caption-title">Mes favoris</h1>
                        <div class="ph-caption-description max-width-700">
                            Retrouvez vos cadeaux preferes, meme s ils deviennent temporairement indisponibles.
                        </div>
                        <div class="gift-favorites-header">
                            <a href="{{ route('gifts.index') }}" class="tt-btn tt-btn-outline">
                                <span data-hover="Catalogue">Retour catalogue</span>
                            </a>
                            <a href="{{ route('gifts.cart') }}" class="tt-btn tt-btn-secondary">
                                <span data-hover="Panier">Panier cadeaux ({{ $cartItems }})</span>
                            </a>
                        </div>
                        <div class="gift-favorites-summary">Panier actuel: {{ $cartTotal }} pts</div>
                    </div>
                </div>
            </div>
        </div>

        <div id="tt-page-content">
            <div class="tt-section padding-top-80 padding-bottom-100 border-top">
                <div class="tt-section-inner tt-wrap max-width-1800">
                    @if(($favorites ?? collect())->count())
                        <div class="gift-favorites-grid">
                            @foreach($favorites as $favorite)
                                @php
                                    $gift = $favorite->gift;
                                    $isAvailable = $gift && $gift->is_active && (int) $gift->stock > 0;
                                @endphp
                                <article class="gift-favorites-card">
                                    <img src="{{ $gift?->image_url ?: '/template/assets/img/logo.png' }}" alt="{{ $gift?->title ?: 'Cadeau indisponible' }}">
                                    <h3>{{ $gift?->title ?: 'Cadeau indisponible' }}</h3>
                                    <p>{{ \Illuminate\Support\Str::limit((string) ($gift?->description ?: 'Ce cadeau n est plus actif dans le catalogue.'), 120) }}</p>

                                    <div class="gift-favorites-meta">
                                        <span class="gift-favorites-pill">{{ (int) ($gift?->cost_points ?? 0) }} pts</span>
                                        <span class="gift-favorites-pill">{{ $gift ? 'Stock '.(int) $gift->stock : 'N/A' }}</span>
                                        <span class="gift-favorites-pill {{ $isAvailable ? 'is-ok' : 'is-out' }}">
                                            {{ $isAvailable ? 'Demandable' : 'Indisponible' }}
                                        </span>
                                    </div>

                                    <div class="gift-favorites-actions">
                                        @if($gift)
                                            <a href="{{ route('gifts.show', $gift->id) }}" class="tt-btn tt-btn-outline">
                                                <span data-hover="Fiche">Voir la fiche</span>
                                            </a>
                                            <form method="POST" action="{{ route('gifts.cart.add', $gift->id) }}">
                                                @csrf
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="tt-btn tt-btn-secondary" {{ $isAvailable ? '' : 'disabled' }}>
                                                    <span data-hover="Panier">Ajouter au panier</span>
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('gifts.favorites.toggle', $favorite->gift_id) }}">
                                            @csrf
                                            <button type="submit" class="tt-btn tt-btn-outline">
                                                <span data-hover="Retirer">Retirer</span>
                                            </button>
                                        </form>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="gift-favorites-empty">
                            Vous n avez pas encore ajoute de cadeau en favori.
                        </div>
                    @endif
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

