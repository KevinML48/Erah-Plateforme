@extends('marketing.layouts.template')

@section('title', 'Catalogue cadeaux | ERAH')
@section('meta_description', 'Catalogue cadeaux ERAH, panier multi-cadeaux, favoris et suivi des commandes.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('page_styles')
<style>
    .gift-catalog-toolbar { margin-top: 18px; padding: 18px; border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 20px; background: rgba(255, 255, 255, 0.03); }
    .gift-toolbar-grid { display: grid; grid-template-columns: minmax(0, 1.4fr) repeat(2, minmax(180px, 0.55fr)) auto auto; gap: 12px; align-items: center; }
    .gift-toolbar-grid input, .gift-toolbar-grid select { width: 100%; min-height: 42px; padding: 10px 12px; border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 12px; background: rgba(0, 0, 0, 0.2); color: #fff; }
    .gift-toolbar-grid select option { color: #111; }
    .gift-balance-chip { display: inline-flex; align-items: center; gap: 10px; margin-top: 14px; padding: 9px 14px; border-radius: 999px; border: 1px solid rgba(255, 255, 255, 0.12); background: rgba(255, 255, 255, 0.05); color: #fff; font-size: 13px; letter-spacing: 0.08em; text-transform: uppercase; }
    .gift-quick-links { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 14px; }
    .gift-card-copy { margin: 10px 0 0; color: rgba(255, 255, 255, 0.74); font-size: 14px; line-height: 1.65; }
    .gift-card-foot { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 14px; }
    .gift-card-foot span { display: inline-flex; align-items: center; min-height: 28px; padding: 6px 10px; border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 999px; background: rgba(255, 255, 255, 0.03); color: rgba(255, 255, 255, 0.88); font-size: 11px; letter-spacing: 0.1em; text-transform: uppercase; }
    .gift-card-foot .is-available { border-color: rgba(90, 179, 61, 0.38); background: rgba(90, 179, 61, 0.16); }
    .gift-card-foot .is-low { border-color: rgba(242, 172, 51, 0.35); background: rgba(242, 172, 51, 0.15); }
    .gift-card-foot .is-out { border-color: rgba(214, 78, 77, 0.35); background: rgba(214, 78, 77, 0.16); }
    .gift-card-foot .is-unavailable { border-color: rgba(143, 143, 143, 0.35); background: rgba(143, 143, 143, 0.16); }
    .gift-card-warning { margin-top: 10px; color: rgba(255, 214, 124, 0.9); font-size: 13px; line-height: 1.5; }
    .gift-card-actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 14px; }
    .gift-card-actions form { margin: 0; }
    .gift-orders-grid, .gift-favorites-grid { display: grid; gap: 14px; }
    .gift-order-card, .gift-favorite-card { padding: 18px 20px; border: 1px solid rgba(255, 255, 255, 0.12); border-radius: 16px; background: rgba(255, 255, 255, 0.03); }
    .gift-order-head { display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between; margin-bottom: 8px; }
    .gift-order-head strong { color: #fff; letter-spacing: 0.08em; text-transform: uppercase; font-size: 12px; }
    .gift-order-status { display: inline-flex; align-items: center; min-height: 30px; padding: 6px 12px; border-radius: 999px; border: 1px solid rgba(255, 255, 255, 0.14); background: rgba(255, 255, 255, 0.03); color: #fff; font-size: 11px; letter-spacing: 0.1em; text-transform: uppercase; }
    .gift-order-card p, .gift-favorite-card p { margin: 0; color: rgba(255, 255, 255, 0.74); line-height: 1.7; }
    @media (max-width: 1199.98px) { .gift-toolbar-grid { grid-template-columns: 1fr 1fr; } .gift-toolbar-grid .tt-btn { width: 100%; } }
    @media (max-width: 767.98px) { .gift-toolbar-grid { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
    @php
        $giftIndexRouteName = $giftIndexRouteName ?? 'gifts.index';
        $giftShowRouteName = $giftShowRouteName ?? 'gifts.show';
        $isAuthenticated = (bool) ($isAuthenticated ?? false);
        $baseFilters = request()->except(['page', 'category']);
    @endphp

    <div id="page-header" class="ph-full ph-full-m ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">Recompenses membres</h2>
                    <h1 class="ph-caption-title">Catalogue cadeaux</h1>
                    <div class="ph-caption-description max-width-700">Choisissez une recompense, preparez votre panier cadeaux et suivez vos commandes.</div>
                    @if($isAuthenticated)
                        <div class="gift-balance-chip">Solde disponible: {{ (int) ($walletBalance ?? 0) }} pts</div>
                        <div class="gift-quick-links">
                            <a class="tt-btn tt-btn-secondary" href="{{ route('gifts.cart') }}"><span data-hover="Panier">Panier cadeaux ({{ (int) ($cartItemsCount ?? 0) }})</span></a>
                            <a class="tt-btn tt-btn-outline" href="{{ route('gifts.favorites') }}"><span data-hover="Favoris">Mes favoris</span></a>
                            <a class="tt-btn tt-btn-outline" href="{{ route('gifts.redemptions') }}"><span data-hover="Commandes">Mes commandes</span></a>
                        </div>
                    @else
                        <div class="gift-balance-chip">Connectez-vous pour utiliser panier et favoris cadeaux</div>
                        <div class="gift-quick-links">
                            <a class="tt-btn tt-btn-secondary" href="{{ route('login') }}"><span data-hover="Connexion">Se connecter pour commander</span></a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section">
            <div class="tt-section-inner max-width-2200">
                <div id="portfolio-grid" class="pgi-hover">
                    <div class="tt-grid ttgr-layout-3 ttgr-gap-3">
                        <div class="tt-grid-top">
                            <div class="tt-grid-categories-classic">
                                <div class="ttgr-cat-classic-nav ttgr-cat-classic-center">
                                    <ul class="ttgr-cat-classic-list">
                                        <li class="ttgr-cat-classic-item"><a href="{{ route($giftIndexRouteName, array_merge($baseFilters, ['category' => 'all'])) }}" data-offset="80" class="{{ $selectedCategory === 'all' ? 'active' : '' }}">Tous</a></li>
                                        @foreach($categories as $category)
                                            <li class="ttgr-cat-classic-item">
                                                <a href="{{ route($giftIndexRouteName, array_merge($baseFilters, ['category' => $category['key']])) }}" data-offset="80" class="{{ $selectedCategory === $category['key'] ? 'active' : '' }}">{{ $category['label'] }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            <form class="gift-catalog-toolbar" method="GET" action="{{ route($giftIndexRouteName) }}">
                                <input type="hidden" name="category" value="{{ $selectedCategory }}">
                                <div class="gift-toolbar-grid">
                                    <input type="text" name="search" value="{{ $searchTerm }}" placeholder="Rechercher un cadeau, un mot-cle..." aria-label="Rechercher un cadeau">
                                    <select name="availability" aria-label="Filtrer par disponibilite">
                                        @foreach($availabilityOptions as $key => $label)
                                            <option value="{{ $key }}" @selected($selectedAvailability === $key)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <select name="sort" aria-label="Trier le catalogue">
                                        @foreach($sortOptions as $key => $label)
                                            <option value="{{ $key }}" @selected($selectedSort === $key)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item"><span data-hover="Filtrer">Filtrer</span></button>
                                    <a href="{{ route($giftIndexRouteName) }}" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Reset">Reset</span></a>
                                </div>
                            </form>
                        </div>

                        <div class="tt-grid-items-wrap isotope-items-wrap">
                            @forelse($giftCards as $item)
                                @php($gift = $item['gift'])
                                <div class="tt-grid-item isotope-item {{ $item['category_key'] }}">
                                    <div class="ttgr-item-inner">
                                        <div class="portfolio-grid-item">
                                            <a href="{{ route($giftShowRouteName, $gift->id) }}" class="pgi-image-wrap" data-cursor="Voir<br>fiche">
                                                <div class="pgi-image-holder">
                                                    <div class="pgi-image-inner tt-anim-zoomin">
                                                        <figure class="pgi-image ttgr-height">
                                                            <img src="{{ $gift->image_url ?: '/template/assets/img/logo.png' }}" loading="lazy" alt="{{ $gift->title }}">
                                                        </figure>
                                                    </div>
                                                </div>
                                            </a>
                                            <div class="pgi-caption">
                                                <div class="pgi-caption-inner">
                                                    <h2 class="pgi-title"><a href="{{ route($giftShowRouteName, $gift->id) }}">{{ $gift->title }}</a></h2>
                                                    <div class="pgi-categories-wrap">
                                                        <div class="pgi-category">{{ $item['category_label'] }}</div>
                                                        <div class="pgi-category">{{ (int) $gift->cost_points }} pts</div>
                                                        <div class="pgi-category">{{ (int) $gift->stock }} en stock</div>
                                                    </div>
                                                    <p class="gift-card-copy">{{ \Illuminate\Support\Str::limit((string) ($gift->description ?: 'Recompense membre accessible avec vos points.'), 120) }}</p>
                                                    <div class="gift-card-foot">
                                                        <span class="is-{{ $item['availability_key'] }}">{{ $item['availability_label'] }}</span>
                                                        <span>{{ $item['availability_copy'] }}</span>
                                                        <span>{{ $item['lead_time'] }}</span>
                                                    </div>
                                                    @if(($item['points_missing'] ?? 0) > 0 && $isAuthenticated)
                                                        <p class="gift-card-warning">Il vous manque {{ (int) $item['points_missing'] }} points pour demander ce cadeau.</p>
                                                    @endif
                                                    <div class="gift-card-actions">
                                                        @if($isAuthenticated)
                                                            <form method="POST" action="{{ route('gifts.cart.add', $gift->id) }}">
                                                                @csrf
                                                                <input type="hidden" name="quantity" value="1">
                                                                <button type="submit" class="tt-btn tt-btn-secondary"><span data-hover="Panier">Ajouter au panier</span></button>
                                                            </form>
                                                            <form method="POST" action="{{ route('gifts.favorites.toggle', $gift->id) }}">
                                                                @csrf
                                                                <button type="submit" class="tt-btn tt-btn-outline">
                                                                    <span data-hover="{{ !empty($item['is_favorited']) ? 'Retirer favoris' : 'Ajouter favoris' }}">{{ !empty($item['is_favorited']) ? 'Retirer favoris' : 'Ajouter favoris' }}</span>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <a href="{{ route('login') }}" class="tt-btn tt-btn-secondary"><span data-hover="Connexion">Connexion pour panier/favoris</span></a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="tt-grid-item isotope-item">
                                    <div class="ttgr-item-inner"><div class="portfolio-grid-item"><div class="pgi-caption"><div class="pgi-caption-inner"><h2 class="pgi-title">Aucun cadeau pour ce filtre</h2></div></div></div></div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($isAuthenticated)
            <div class="tt-section padding-top-80 padding-bottom-80 border-top">
                <div class="tt-section-inner tt-wrap max-width-1400">
                    <div class="tt-row">
                        <div class="tt-col-lg-4">
                            <div class="tt-heading tt-heading-lg">
                                <h3 class="tt-heading-subtitle tt-text-reveal">Mes commandes</h3>
                                <h2 class="tt-heading-title tt-text-reveal">Suivi recent</h2>
                            </div>
                            <a href="{{ route('gifts.redemptions') }}" class="tt-btn tt-btn-secondary margin-top-20"><span data-hover="Voir toutes mes commandes">Voir toutes mes commandes</span></a>
                        </div>
                        <div class="tt-col-lg-8">
                            <div class="gift-orders-grid">
                                @forelse(($recentRedemptions ?? collect()) as $redemption)
                                    @php($status = (string) $redemption->status)
                                    <article class="gift-order-card tt-anim-fadeinup">
                                        <div class="gift-order-head">
                                            <strong>{{ 'CMD-'.str_pad((string) $redemption->id, 6, '0', STR_PAD_LEFT) }}</strong>
                                            <span class="gift-order-status is-{{ $status }}">{{ $statusLabels[$status] ?? \Illuminate\Support\Str::headline($status) }}</span>
                                        </div>
                                        <p>{{ $redemption->gift->title ?? 'Cadeau' }} - {{ (int) $redemption->cost_points_snapshot }} pts.</p>
                                        <a class="tt-btn tt-btn-outline margin-top-15" href="{{ route('gifts.redemptions.show', $redemption->id) }}"><span data-hover="Voir le detail">Voir le detail</span></a>
                                    </article>
                                @empty
                                    <div class="gift-order-card"><p>Pas encore de commande cadeau.</p></div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tt-section padding-top-80 padding-bottom-80 border-top">
                <div class="tt-section-inner tt-wrap max-width-1400">
                    <div class="tt-row">
                        <div class="tt-col-lg-4">
                            <div class="tt-heading tt-heading-lg">
                                <h3 class="tt-heading-subtitle tt-text-reveal">Mes favoris</h3>
                                <h2 class="tt-heading-title tt-text-reveal">Selection rapide</h2>
                            </div>
                            <a href="{{ route('gifts.favorites') }}" class="tt-btn tt-btn-secondary margin-top-20"><span data-hover="Voir tous mes favoris">Voir tous mes favoris</span></a>
                        </div>
                        <div class="tt-col-lg-8">
                            <div class="gift-favorites-grid">
                                @forelse(($favoriteGifts ?? collect()) as $favorite)
                                    @if($favorite->gift)
                                        <article class="gift-favorite-card tt-anim-fadeinup">
                                            <div class="gift-order-head"><strong>{{ $favorite->gift->title }}</strong></div>
                                            <p>{{ (int) $favorite->gift->cost_points }} pts - stock {{ (int) $favorite->gift->stock }}</p>
                                            <div class="gift-card-actions">
                                                <a href="{{ route('gifts.show', $favorite->gift->id) }}" class="tt-btn tt-btn-outline"><span data-hover="Ouvrir">Ouvrir la fiche</span></a>
                                                <form method="POST" action="{{ route('gifts.cart.add', $favorite->gift->id) }}">
                                                    @csrf
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" class="tt-btn tt-btn-secondary"><span data-hover="Panier">Ajouter au panier</span></button>
                                                </form>
                                            </div>
                                        </article>
                                    @endif
                                @empty
                                    <div class="gift-favorite-card"><p>Aucun favori pour le moment.</p></div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
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
