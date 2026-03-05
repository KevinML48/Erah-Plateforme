@extends('marketing.layouts.template')

@section('title', 'Mes favoris | ERAH Plateforme')
@section('meta_description', 'Vos clips favoris avec pagination et statistiques likes.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    <link rel="stylesheet" href="/template/assets/css/blog.css">
@endsection

@section('content')
    @php
        $isPublicApp = request()->routeIs('app.*');
        $indexRouteName = $isPublicApp ? 'app.clips.index' : 'clips.index';
        $showRouteName = $isPublicApp ? 'app.clips.show' : 'clips.show';
        $currentPage = (int) $clips->currentPage();
        $lastPage = (int) $clips->lastPage();
        $startPage = max(1, $currentPage - 2);
        $endPage = min($lastPage, $currentPage + 2);
        $pageUrls = $clips->getUrlRange($startPage, $endPage);
    @endphp

    <div id="page-header" class="ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">ERAH Plateforme</h2>
                    <h1 class="ph-caption-title">Mes favoris</h1>
                    <div class="ph-caption-description max-width-700">
                        Retrouvez tous les clips que vous avez ajoutes en favoris.
                    </div>
                </div>
            </div>
        </div>

        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">ERAH Plateforme</h2>
                        <h1 class="ph-caption-title">Mes favoris</h1>
                        <div class="ph-caption-description max-width-700">
                            Retrouvez tous les clips que vous avez ajoutes en favoris.
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
                        <textPath xlink:href="#textcircle">Mes Favoris - Mes Favoris -</textPath>
                    </text>
                </svg>
            </a>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 padding-bottom-40">
            <div class="tt-section-inner tt-wrap">
                <div class="tt-row">
                    <div class="tt-col-lg-9">
                        <div class="tt-btn-group">
                            <a href="{{ route($indexRouteName) }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                <span data-hover="Retour aux clips">Retour aux clips</span>
                            </a>
                        </div>
                    </div>
                    <div class="tt-col-lg-3 tt-align-self-center">
                        <p class="text-right text-sm-start text-muted no-margin">
                            {{ $clips->total() }} favori(s)
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="tt-section padding-bottom-xlg-120">
            <div class="tt-section-inner tt-wrap">
                @if(($clips ?? null) && $clips->count())
                    <div id="blog-list" class="bli-compact bli-image-cropped">
                        @foreach($clips as $clip)
                            @php
                                $thumbnail = $clip->thumbnail_url ?: '/template/assets/img/logo.png';
                                $authorName = $clip->createdBy?->name ?? 'ERAH';
                            @endphp

                            <article class="blog-list-item">
                                <a href="{{ route($showRouteName, $clip->slug) }}" class="bli-image-wrap" data-cursor="Voir<br>Clip">
                                    <figure class="bli-image tt-anim-zoomin">
                                        <img src="{{ $thumbnail }}" loading="lazy" alt="{{ $clip->title }}">
                                    </figure>
                                </a>

                                <div class="bli-info">
                                    <div class="bli-categories">
                                        <a href="{{ route($indexRouteName) }}">Favori</a>
                                    </div>

                                    <h2 class="bli-title">
                                        <a href="{{ route($showRouteName, $clip->slug) }}">{{ $clip->title }}</a>
                                    </h2>

                                    <div class="bli-meta">
                                        <span class="published">{{ optional($clip->published_at)->format('d/m/Y H:i') ?: 'Date inconnue' }}</span>
                                        <span class="posted-by">- par {{ $authorName }}</span>
                                    </div>

                                    <div class="bli-desc">
                                        {{ \Illuminate\Support\Str::limit((string) $clip->description, 180) }}
                                    </div>

                                    <div class="tt-row margin-top-20">
                                        <div class="tt-col-xl-7">
                                            <div class="text-sm">
                                                <strong>{{ (int) $clip->likes_count }}</strong> likes -
                                                <strong>{{ (int) $clip->favorites_count }}</strong> favoris -
                                                <strong>{{ (int) $clip->comments_count }}</strong> commentaires
                                            </div>
                                        </div>
                                        <div class="tt-col-xl-5 tt-align-self-center">
                                            <div class="tt-btn-group tt-justify-content-xl-end">
                                                <a href="{{ route($showRouteName, $clip->slug) }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                                    <span data-hover="Voir">Voir</span>
                                                </a>

                                                @auth
                                                    <form method="POST" action="{{ route('clips.unfavorite', $clip->id) }}">
                                                        @csrf
                                                        <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item">Retirer</button>
                                                    </form>
                                                @else
                                                    <a href="{{ route('login') }}" class="tt-btn tt-btn-outline tt-magnetic-item">Connexion</a>
                                                @endauth
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    @if($clips->hasPages())
                        <div class="tt-pagination">
                            <div class="tt-pagin-prev">
                                @if($clips->onFirstPage())
                                    <span class="tt-pagin-item"><i class="fas fa-arrow-left"></i></span>
                                @else
                                    <a href="{{ $clips->previousPageUrl() }}" class="tt-pagin-item tt-magnetic-item"><i class="fas fa-arrow-left"></i></a>
                                @endif
                            </div>
                            <div class="tt-pagin-numbers">
                                @foreach($pageUrls as $page => $url)
                                    <a href="{{ $url }}"
                                        class="tt-pagin-item tt-magnetic-item {{ $page === $currentPage ? 'active' : '' }}">
                                        {{ $page }}
                                    </a>
                                @endforeach
                            </div>
                            <div class="tt-pagin-next">
                                @if($clips->hasMorePages())
                                    <a href="{{ $clips->nextPageUrl() }}" class="tt-pagin-item tt-pagin-next tt-magnetic-item"><i class="fas fa-arrow-right"></i></a>
                                @else
                                    <span class="tt-pagin-item tt-pagin-next"><i class="fas fa-arrow-right"></i></span>
                                @endif
                            </div>
                        </div>
                    @endif
                @else
                    <div class="tt-heading tt-heading-lg tt-heading-center">
                        <h2 class="tt-heading-title">Aucun favori</h2>
                        <p class="max-width-500">Ajoutez des clips en favoris depuis la page clips.</p>
                        <p class="margin-top-30">
                            <a href="{{ route($indexRouteName) }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                <span data-hover="Parcourir les clips">Parcourir les clips</span>
                            </a>
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    <script src="/template/assets/vendor/jquery/jquery.min.js" defer></script>
    <script src="/template/assets/vendor/gsap/gsap.min.js" defer></script>
    <script src="/template/assets/vendor/gsap/ScrollToPlugin.min.js" defer></script>
    <script src="/template/assets/vendor/gsap/ScrollTrigger.min.js" defer></script>
    <script src="/template/assets/vendor/lenis.min.js" defer></script>
    <script src="/template/assets/vendor/isotope/imagesloaded.pkgd.min.js" defer></script>
    <script src="/template/assets/vendor/isotope/isotope.pkgd.min.js" defer></script>
    <script src="/template/assets/vendor/isotope/packery-mode.pkgd.min.js" defer></script>
    <script src="/template/assets/vendor/fancybox/js/fancybox.umd.js" defer></script>
    <script src="/template/assets/vendor/swiper/js/swiper-bundle.min.js" defer></script>
    <script src="/template/assets/js/theme.js" defer></script>
    <script src="/template/assets/js/cookies.js" defer></script>
@endsection
