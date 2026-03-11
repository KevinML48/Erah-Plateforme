@extends('marketing.layouts.template')

@section('title', 'Cadeaux ERAH')
@section('meta_description', 'Catalogue cadeaux ERAH et demandes depuis le solde points.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('content')
    <div id="page-header" class="ph-full ph-full-m ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">Catalogue cadeaux</h2>
                    <h1 class="ph-caption-title">Cadeaux ERAH</h1>
                    <div class="ph-caption-description max-width-700">
                        Utilisez vos points plateforme pour debloquer des cadeaux et suivre vos demandes.
                    </div>
                </div>
            </div>
        </div>

        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">Catalogue</h2>
                        <h1 class="ph-caption-title">Cadeaux</h1>
                        <div class="ph-caption-description max-width-700">
                            Choisissez une recompense, verifiez votre solde et lancez votre demande depuis votre espace membre.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ph-social">
            <ul>
                <li><a href="https://www.twitch.tv/erah_association" class="tt-magnetic-item" target="_blank" rel="noopener"><i class="fa-brands fa-twitch"></i></a></li>
                <li><a href="https://www.instagram.com/erahesport/" class="tt-magnetic-item" target="_blank" rel="noopener"><i class="fa-brands fa-instagram"></i></a></li>
                <li><a href="https://x.com/ErahEsport" class="tt-magnetic-item" target="_blank" rel="noopener"><i class="fa-brands fa-x-twitter"></i></a></li>
                <li><a href="https://discord.gg/9G89kkSjRx" class="tt-magnetic-item" target="_blank" rel="noopener"><i class="fa-brands fa-discord"></i></a></li>
            </ul>
        </div>

        <div class="tt-scroll-down">
            <a href="#tt-page-content" class="tt-scroll-down-inner tt-magnetic-item" data-offset="0">
                <div class="tt-scrd-icon"></div>
                <svg viewBox="0 0 500 500">
                    <defs>
                        <path d="M50,250c0-110.5,89.5-200,200-200s200,89.5,200,200s-89.5,200-200,200S50,360.5,50,250" id="textcircle"></path>
                    </defs>
                    <text dy="30">
                        <textPath xlink:href="#textcircle">Voir le catalogue - Voir le catalogue -</textPath>
                    </text>
                </svg>
            </a>
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
                                        <li class="ttgr-cat-classic-item">
                                            <a href="{{ route('gifts.index') }}" data-offset="80" class="{{ $selectedCategory === 'all' ? 'active' : '' }}">Tous</a>
                                        </li>
                                        @foreach($categories as $category)
                                            <li class="ttgr-cat-classic-item">
                                                <a href="{{ route('gifts.index', ['category' => $category['key']]) }}"
                                                   data-offset="80"
                                                   data-filter=".{{ $category['key'] }}"
                                                   class="{{ $selectedCategory === $category['key'] ? 'active' : '' }}">
                                                    {{ $category['label'] }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="tt-grid-items-wrap isotope-items-wrap">
                            @forelse($giftCards as $item)
                                @php
                                    $gift = $item['gift'];
                                    $cover = $gift->image_url ?: '/template/assets/img/logo.png';
                                    $stockLabel = ((int) $gift->stock > 0) ? ((int) $gift->stock.' en stock') : 'Rupture';
                                @endphp
                                <div class="tt-grid-item isotope-item {{ $item['category_key'] }}">
                                    <div class="ttgr-item-inner">
                                        <div class="portfolio-grid-item">
                                            <a href="{{ route('gifts.show', $gift->id) }}" class="pgi-image-wrap" data-cursor="View<br>Gift">
                                                <div class="pgi-image-holder">
                                                    <div class="pgi-image-inner tt-anim-zoomin">
                                                        <figure class="pgi-image ttgr-height">
                                                            <img src="{{ $cover }}" loading="lazy" alt="{{ $gift->title }}">
                                                        </figure>
                                                    </div>
                                                </div>
                                            </a>

                                            <div class="pgi-caption">
                                                <div class="pgi-caption-inner">
                                                    <h2 class="pgi-title">
                                                        <a href="{{ route('gifts.show', $gift->id) }}">{{ $gift->title }}</a>
                                                    </h2>
                                                    <div class="pgi-categories-wrap">
                                                        <div class="pgi-category">{{ $item['category_label'] }}</div>
                                                        <div class="pgi-category">{{ (int) $gift->cost_points }} pts</div>
                                                        <div class="pgi-category">{{ $stockLabel }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="tt-grid-item isotope-item">
                                    <div class="ttgr-item-inner">
                                        <div class="portfolio-grid-item">
                                            <div class="pgi-caption">
                                                <div class="pgi-caption-inner">
                                                    <h2 class="pgi-title">Aucun cadeau disponible</h2>
                                                    <div class="pgi-categories-wrap">
                                                        <div class="pgi-category">Essayez une autre categorie</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tt-section padding-top-lg-80 padding-bottom-lg-120 padding-bottom-80 border-top">
            <div class="tt-section-inner tt-wrap max-width-1600">
                <div class="tt-row">
                    <div class="tt-col-lg-3">
                        <div class="tt-heading tt-heading-lg">
                            <h3 class="tt-heading-subtitle tt-text-reveal">Besoin d aide ?</h3>
                            <h2 class="tt-heading-title tt-text-reveal">FAQ<br>Cadeaux</h2>
                        </div>

                        <p class="text-muted">
                            Comprendre comment gagner des points, debloquer des cadeaux
                            et suivre vos demandes.
                        </p>

                        <a href="{{ route('marketing.faq') }}" class="tt-btn tt-btn-secondary margin-top-30">
                            <span data-hover="FAQ complete">FAQ complete</span>
                        </a>
                    </div>

                    <div class="tt-col-lg-1 padding-top-30">
                    </div>

                    <div class="tt-col-lg-8 tt-align-self-center">
                        <div class="tt-accordion tt-ac-sm tt-ac-borders tt-ac-counter">
                            <div class="tt-accordion-item tt-anim-fadeinup">
                                <div class="tt-accordion-heading">
                                    <div class="tt-ac-head cursor-alter">
                                        <div class="tt-ac-head-inner">
                                            <h4 class="tt-ac-head-title">Comment gagner des points ?</h4>
                                        </div>
                                    </div>
                                    <div class="tt-accordion-caret">
                                        <div class="tt-accordion-caret-inner tt-magnetic-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                                <path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="tt-accordion-content max-width-900">
                                    <p>
                                        Les points viennent surtout des missions quotidiennes ou hebdomadaires,
                                        de certaines activites valides sur la plateforme et des ajustements admin. Plus vous jouez
                                        regulierement, plus vous cumulez vite.
                                    </p>
                                </div>
                            </div>

                            <div class="tt-accordion-item tt-anim-fadeinup">
                                <div class="tt-accordion-heading">
                                    <div class="tt-ac-head cursor-alter">
                                        <div class="tt-ac-head-inner">
                                            <h4 class="tt-ac-head-title">A quoi servent les points ?</h4>
                                        </div>
                                    </div>
                                    <div class="tt-accordion-caret">
                                        <div class="tt-accordion-caret-inner tt-magnetic-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                                <path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="tt-accordion-content max-width-900">
                                    <p>
                                        Les points servent a vos usages de plateforme, notamment les cadeaux et les autres modules relies au portefeuille. La progression, elle, se lit surtout avec l XP et votre rang.
                                    </p>
                                </div>
                            </div>

                            <div class="tt-accordion-item tt-anim-fadeinup">
                                <div class="tt-accordion-heading">
                                    <div class="tt-ac-head cursor-alter">
                                        <div class="tt-ac-head-inner">
                                            <h4 class="tt-ac-head-title">Quand les points sont-ils credites ?</h4>
                                        </div>
                                    </div>
                                    <div class="tt-accordion-caret">
                                        <div class="tt-accordion-caret-inner tt-magnetic-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                                <path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="tt-accordion-content max-width-900">
                                    <p>
                                        Les points sont credites automatiquement quand une mission est validee
                                        ou quand un evenement est regle (ex: settlement de pari). En cas de relance,
                                        le systeme reste idempotent pour eviter les doubles gains.
                                    </p>
                                </div>
                            </div>

                            <div class="tt-accordion-item tt-anim-fadeinup">
                                <div class="tt-accordion-heading">
                                    <div class="tt-ac-head cursor-alter">
                                        <div class="tt-ac-head-inner">
                                            <h4 class="tt-ac-head-title">Comment demander un cadeau ?</h4>
                                        </div>
                                    </div>
                                    <div class="tt-accordion-caret">
                                        <div class="tt-accordion-caret-inner tt-magnetic-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                                <path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="tt-accordion-content max-width-900">
                                    <p>
                                        Ouvrez un cadeau, cliquez sur Demander, puis confirmez. Le cout en points
                                        est debite a la demande et le stock est reserve immediatement.
                                    </p>
                                </div>
                            </div>

                            <div class="tt-accordion-item tt-anim-fadeinup">
                                <div class="tt-accordion-heading">
                                    <div class="tt-ac-head cursor-alter">
                                        <div class="tt-ac-head-inner">
                                            <h4 class="tt-ac-head-title">Que se passe-t-il si une demande est rejetee ?</h4>
                                        </div>
                                    </div>
                                    <div class="tt-accordion-caret">
                                        <div class="tt-accordion-caret-inner tt-magnetic-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                                <path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="tt-accordion-content max-width-900">
                                    <p>
                                        Si une demande est rejetee par un admin, vos points sont rembourses
                                        automatiquement et le stock est restaure.
                                    </p>
                                </div>
                            </div>

                            <div class="tt-accordion-item tt-anim-fadeinup">
                                <div class="tt-accordion-heading">
                                    <div class="tt-ac-head cursor-alter">
                                        <div class="tt-ac-head-inner">
                                            <h4 class="tt-ac-head-title">Ou suivre mes cadeaux et leur statut ?</h4>
                                        </div>
                                    </div>
                                    <div class="tt-accordion-caret">
                                        <div class="tt-accordion-caret-inner tt-magnetic-item">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                                <path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="tt-accordion-content max-width-900">
                                    <p>
                                        Depuis la page cadeaux et votre profil, vous pouvez suivre le statut:
                                        pending, approved, shipped ou delivered.
                                    </p>
                                </div>
                            </div>
                        </div>
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
