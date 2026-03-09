@extends('marketing.layouts.template')

@section('title', 'Clips | ERAH Plateforme')
@section('meta_description', 'Liste des clips ERAH avec likes, favoris et pagination.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    <link rel="stylesheet" href="/template/assets/css/blog.css">
    <style>
        .js-clip-preview .bli-image {
            position: relative;
            overflow: hidden;
        }

        .js-clip-preview .js-clip-poster {
            transition: opacity .2s ease;
        }

        .js-clip-preview .clip-preview-media {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
            object-fit: cover;
            opacity: 0;
            visibility: hidden;
            transition: opacity .2s ease, visibility .2s ease;
            background: #000;
        }

        .js-clip-preview.is-previewing .js-clip-poster {
            opacity: 0;
        }

        .js-clip-preview.is-previewing .clip-preview-media {
            opacity: 1;
            visibility: visible;
        }

        .tt-shortcuts-inline {
            margin-top: 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        @media (max-width: 767.98px) {
            .clip-index-toolbar .tt-btn-group,
            .clip-card-actions {
                display: grid;
                width: 100%;
                gap: 10px;
            }

            .clip-index-toolbar .tt-btn,
            .clip-card-actions .tt-btn,
            .clip-card-actions form {
                width: 100%;
            }

            .tt-shortcuts-inline {
                align-items: flex-start;
                flex-direction: column;
            }

            .tt-shortcuts-inline .tt-btn-group {
                width: 100%;
                overflow-x: auto;
                flex-wrap: nowrap;
                padding-bottom: 4px;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $isPublicApp = request()->routeIs('app.*');
        $isGuest = auth()->guest();
        $participationLoginUrl = route('login', ['required' => 'participation']);
        $indexRouteName = $isPublicApp ? 'app.clips.index' : 'clips.index';
        $showRouteName = $isPublicApp ? 'app.clips.show' : 'clips.show';
        $favoritesUrl = $isPublicApp
            ? (auth()->check() ? route('app.clips.favorites') : $participationLoginUrl)
            : ($isGuest ? $participationLoginUrl : route('clips.favorites'));
        $currentPage = (int) $clips->currentPage();
        $lastPage = (int) $clips->lastPage();
        $startPage = max(1, $currentPage - 2);
        $endPage = min($lastPage, $currentPage + 2);
        $pageUrls = $clips->getUrlRange($startPage, $endPage);
        $platformShortcuts = app(\App\Services\ShortcutService::class)->getForUser(auth()->user());
    @endphp

    <div id="page-header" class="ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">ERAH Plateforme</h2>
                    <h1 class="ph-caption-title">Clips</h1>
                    <div class="ph-caption-description max-width-700">
                        Filtrez, consultez et interagissez avec les derniers clips.
                    </div>
                </div>
            </div>
        </div>

        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">ERAH Plateforme</h2>
                        <h1 class="ph-caption-title">Clips</h1>
                        <div class="ph-caption-description max-width-700">
                            Filtrez, consultez et interagissez avec les derniers clips.
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
                        <textPath xlink:href="#textcircle">Explore Clips - Explore Clips -</textPath>
                    </text>
                </svg>
            </a>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 padding-bottom-40">
            <div class="tt-section-inner tt-wrap">
                <div class="tt-row clip-index-toolbar">
                    <div class="tt-col-lg-9">
                        <div class="tt-btn-group">
                            <a href="{{ route($indexRouteName, ['sort' => 'recent']) }}"
                                class="tt-btn {{ $sort === 'recent' ? 'tt-btn-primary' : 'tt-btn-outline' }} tt-magnetic-item">
                                <span data-hover="Recents">Recents</span>
                            </a>
                            <a href="{{ route($indexRouteName, ['sort' => 'popular']) }}"
                                class="tt-btn {{ $sort === 'popular' ? 'tt-btn-primary' : 'tt-btn-outline' }} tt-magnetic-item">
                                <span data-hover="Populaires">Populaires</span>
                            </a>
                            <a href="{{ $favoritesUrl }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                <span data-hover="Mes favoris">Mes favoris</span>
                            </a>
                        </div>

                        @if(count($platformShortcuts))
                            <div class="tt-shortcuts-inline">
                                <span class="tt-form-text">Raccourcis plateforme:</span>
                                <div class="tt-btn-group">
                                    @foreach($platformShortcuts as $shortcut)
                                        @php
                                            $shortcutUrl = ($shortcut['requires_auth'] ?? false) && !auth()->check()
                                                ? route('login')
                                                : $shortcut['url'];
                                        @endphp
                                        <a href="{{ $shortcutUrl }}" class="tt-btn tt-btn-link tt-magnetic-item">
                                            <span data-hover="{{ $shortcut['label'] }}">{{ $shortcut['label'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    <div class="tt-col-lg-3 tt-align-self-center">
                        <p class="text-right text-sm-start text-muted no-margin">
                            {{ $clips->total() }} clip(s)
                        </p>
                    </div>
                </div>

                @if($isGuest)
                    <div class="tt-alert tt-alert-danger margin-top-20">
                        Creez un compte pour participer, gagner des points et progresser sur la plateforme.
                    </div>
                @endif
            </div>
        </div>

        <div class="tt-section padding-bottom-xlg-120">
            <div class="tt-section-inner tt-wrap">
                @if(($clips ?? null) && $clips->count())
                    <div id="blog-list" class="bli-compact bli-image-cropped">
                        @foreach($clips as $clip)
                            @php
                                $isLiked = in_array($clip->id, $likedIds ?? [], true);
                                $isFavorited = in_array($clip->id, $favoriteIds ?? [], true);
                                $thumbnail = $clip->thumbnail_url ?: '/template/assets/img/logo.png';
                                $authorName = $clip->createdBy?->name ?? 'ERAH';
                                $videoUrl = trim((string) ($clip->video_url ?? ''));
                                $previewType = 'none';
                                $previewEmbedUrl = '';

                                if ($videoUrl !== '') {
                                    if (preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $videoUrl) === 1) {
                                        $previewType = 'file';
                                    } elseif (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/)([A-Za-z0-9_-]{6,})~i', $videoUrl, $matches) === 1) {
                                        $youtubeId = $matches[1];
                                        $previewType = 'embed';
                                        $previewEmbedUrl = 'https://www.youtube.com/embed/'.$youtubeId.'?autoplay=1&mute=1&controls=0&rel=0&modestbranding=1&playsinline=1&loop=1&playlist='.$youtubeId;
                                    } elseif (preg_match('~vimeo\.com/(?:video/)?([0-9]{6,})~i', $videoUrl, $matches) === 1) {
                                        $vimeoId = $matches[1];
                                        $previewType = 'embed';
                                        $previewEmbedUrl = 'https://player.vimeo.com/video/'.$vimeoId.'?autoplay=1&muted=1&loop=1&title=0&byline=0&portrait=0';
                                    }
                                }
                            @endphp

                            <article class="blog-list-item">
                                <a href="{{ route($showRouteName, $clip->slug) }}"
                                    class="bli-image-wrap js-clip-preview"
                                    data-cursor="Voir<br>Clip"
                                    data-preview-type="{{ $previewType }}">
                                    <figure class="bli-image tt-anim-zoomin">
                                        <img src="{{ $thumbnail }}" loading="lazy" alt="{{ $clip->title }}" class="js-clip-poster">
                                        @if($previewType === 'file')
                                            <video class="clip-preview-media js-clip-video" muted loop playsinline preload="metadata">
                                                <source src="{{ $videoUrl }}">
                                            </video>
                                        @elseif($previewType === 'embed')
                                            <iframe class="clip-preview-media js-clip-embed"
                                                src="about:blank"
                                                data-src="{{ $previewEmbedUrl }}"
                                                allow="autoplay; encrypted-media; picture-in-picture"
                                                loading="lazy"
                                                title="Preview {{ $clip->title }}">
                                            </iframe>
                                        @endif
                                    </figure>
                                </a>

                                <div class="bli-info">
                                    <div class="bli-categories">
                                        <a href="{{ route($indexRouteName, ['sort' => $sort]) }}">{{ $sort === 'popular' ? 'Populaire' : 'Recent' }}</a>
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
                                            <div class="tt-btn-group tt-justify-content-xl-end clip-card-actions">
                                                <a href="{{ route($showRouteName, $clip->slug) }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                                    <span data-hover="Voir">Voir</span>
                                                </a>

                                                @if(auth()->check())
                                                    @if($isLiked)
                                                        <form method="POST" action="{{ route('clips.unlike', $clip->id) }}">
                                                            @csrf
                                                            <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item">Unlike</button>
                                                        </form>
                                                    @else
                                                        <form method="POST" action="{{ route('clips.like', $clip->id) }}">
                                                            @csrf
                                                            <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item">Like</button>
                                                        </form>
                                                    @endif

                                                    @if($isFavorited)
                                                        <form method="POST" action="{{ route('clips.unfavorite', $clip->id) }}">
                                                            @csrf
                                                            <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item">Unfavorite</button>
                                                        </form>
                                                    @else
                                                        <form method="POST" action="{{ route('clips.favorite', $clip->id) }}">
                                                            @csrf
                                                            <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item">Favorite</button>
                                                        </form>
                                                    @endif
                                                @else
                                                    <a href="{{ $participationLoginUrl }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                                        <span data-hover="Se connecter">Se connecter</span>
                                                    </a>

                                                    <a href="{{ $participationLoginUrl }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                                        <span data-hover="Creer un compte">Creer un compte</span>
                                                    </a>
                                                @endif
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
                        <h2 class="tt-heading-title">Aucun clip disponible</h2>
                        <p class="max-width-500">Publiez des clips ou revenez plus tard.</p>
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var canHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
            if (!canHover) {
                return;
            }

            var cards = document.querySelectorAll('.js-clip-preview');
            cards.forEach(function (card) {
                var type = card.getAttribute('data-preview-type');
                if (type !== 'file' && type !== 'embed') {
                    return;
                }

                var video = card.querySelector('.js-clip-video');
                var iframe = card.querySelector('.js-clip-embed');

                var startPreview = function () {
                    card.classList.add('is-previewing');

                    if (type === 'file' && video) {
                        try {
                            video.currentTime = 0;
                        } catch (e) {
                            // ignore seek errors on some streams
                        }
                        video.play().catch(function () {});
                    }

                    if (type === 'embed' && iframe && iframe.dataset.src && iframe.src === 'about:blank') {
                        iframe.src = iframe.dataset.src;
                    }
                };

                var stopPreview = function () {
                    card.classList.remove('is-previewing');

                    if (type === 'file' && video) {
                        video.pause();
                        try {
                            video.currentTime = 0;
                        } catch (e) {
                            // ignore seek errors on some streams
                        }
                    }

                    if (type === 'embed' && iframe) {
                        iframe.src = 'about:blank';
                    }
                };

                card.addEventListener('mouseenter', startPreview);
                card.addEventListener('mouseleave', stopPreview);
                card.addEventListener('focusin', startPreview);
                card.addEventListener('focusout', stopPreview);
            });
        });
    </script>
@endsection
