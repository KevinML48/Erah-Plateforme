@extends('marketing.layouts.template')

@section('title', 'Galerie Photo | ERAH Esport')
@section('meta_description', 'Parcourez la galerie photo ERAH Esport : competitions, evenements gaming et moments forts du club.')
@section('meta_keywords', 'galerie photo erah esport, photos esport, evenements gaming, competitions erah')
@section('meta_author', 'ERAH Esport')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('page_styles')
<style>
    #cookie-banner {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        max-width: 480px;
        background: rgba(0, 0, 0, 0.9);
        color: #fff;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.4);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        z-index: 10000;
        font-family: Arial, sans-serif;
        font-size: 14px;
        text-align: center;
        opacity: 0;
        animation: fadeIn 0.6s forwards;
    }

    @keyframes fadeIn {
        to { opacity: 1; }
    }

    #cookie-banner button {
        border: none;
        padding: 10px 18px;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
        transition: transform .2s, background .2s;
    }

    #cookie-banner button#accept-cookies {
        background: #4caf50;
        color: #fff;
    }

    #cookie-banner button#reject-cookies {
        background: #f44336;
        color: #fff;
    }

    #cookie-banner button:hover {
        transform: scale(1.05);
    }

    @media (max-width: 500px) {
        #cookie-banner div {
            display: flex;
            flex-direction: column;
            gap: 8px;
            width: 100%;
        }

        #cookie-banner div button {
            width: 100%;
        }
    }

    .tt-grid-item.is-hidden-by-default {
        display: none;
    }

    .gallery-empty {
        width: 100%;
        padding: 42px 32px;
        border: 1px solid rgba(255, 255, 255, .12);
        border-radius: 22px;
        text-align: center;
        color: rgba(255, 255, 255, .78);
        background: rgba(255, 255, 255, .02);
    }

    .gallery-empty h3 {
        margin-bottom: 12px;
        color: rgba(255, 255, 255, .92);
    }

    .gallery-item-link-disabled {
        cursor: default;
        pointer-events: none;
    }

    .pgi-video-wrap video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    body.tt-lightmode-on .gallery-empty {
        border-color: rgba(18, 23, 35, .16);
        color: rgba(18, 23, 35, .72);
        background: rgba(255, 255, 255, .82);
    }

    body.tt-lightmode-on .gallery-empty h3 {
        color: rgba(18, 23, 35, .94);
    }
</style>
@endsection

@section('content')
@php
    $photos = $photos ?? collect();
    $filters = $filters ?? collect();
    $initialVisibleCount = $initialVisibleCount ?? 12;
@endphp

<div id="page-header" class="ph-full ph-full-m ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
    <div class="page-header-inner tt-wrap">
        <div class="ph-caption">
            <div class="ph-caption-inner">
                <h2 class="ph-caption-subtitle">Galerie</h2>
                <h1 class="ph-caption-title">Photos</h1>
                <div class="ph-caption-description max-width-700">
                    Parcourez nos plus beaux cliches et revivez chaque moment fort en images.
                </div>
            </div>
        </div>
    </div>

    <div class="page-header-inner ph-mask">
        <div class="ph-mask-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">Galerie</h2>
                    <h1 class="ph-caption-title">Souvenirs</h1>
                    <div class="ph-caption-description max-width-700">
                        Competitions, activations, evenements et images club importes depuis la galerie historique.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="ph-social">
        <ul>
            <li><a href="https://www.twitch.tv/erah_association" class="tt-magnetic-item" target="_blank" rel="noopener"><i class="fa-brands fa-twitch"></i></a></li>
            <li><a href="https://www.instagram.com/erahesport/" class="tt-magnetic-item" target="_blank" rel="noopener"><i class="fa-brands fa-instagram"></i></a></li>
            <li><a href="https://x.com/ErahEsport" class="tt-magnetic-item" target="_blank" rel="noopener"><i class="fa-brands fa-twitter"></i></a></li>
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
                    <textPath xlink:href="#textcircle">Explorez Defiez Brillez Soutenez ERAH -</textPath>
                </text>
            </svg>
        </a>
    </div>
</div>

<div id="tt-page-content">
    <div class="tt-section">
        <div class="tt-section-inner max-width-2200">
            <div id="portfolio-grid" class="pgi-hover pgi-cap-inside">
                <div class="tt-grid ttgr-layout-3 ttgr-gap-1 ttgr-not-cropped">
                    <div class="tt-grid-top">
                        <div class="tt-grid-categories">
                            <div class="ttgr-cat-trigger-wrap">
                                <div class="ttgr-cat-trigger-holder">
                                    <a href="#categories" class="ttgr-cat-trigger" data-offset="150">
                                        <div class="ttgr-cat-text hide-cursor">
                                            <span data-hover="Open">Filter</span>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <div class="ttgr-cat-nav">
                                <div class="ttgr-cat-close-btn">Close <i class="fas fa-times"></i></div>
                                <div class="ttgr-cat-list-holder cursor-close" data-lenis-prevent>
                                    <div class="ttgr-cat-list-inner">
                                        <div class="ttgr-cat-list-content">
                                            <ul class="ttgr-cat-list hide-cursor">
                                                <li class="ttgr-cat-item"><a href="#" class="active">All</a></li>
                                                @foreach($filters as $filter)
                                                    <li class="ttgr-cat-item">
                                                        <a href="#" data-filter=".{{ $filter['key'] }}">{{ $filter['label'] }}</a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tt-grid-items-wrap isotope-items-wrap">
                        @forelse($photos as $photo)
                            @php
                                $mediaUrl = $photo->primary_media_url;
                                $cursorLabel = $photo->cursor_label ?: ($photo->title ?: 'Voir');
                                $itemClasses = trim(implode(' ', array_filter([
                                    'tt-grid-item',
                                    'isotope-item',
                                    $photo->filter_key,
                                    $loop->index >= $initialVisibleCount ? 'is-hidden-by-default' : null,
                                ])));
                            @endphp
                            <div class="{{ $itemClasses }}">
                                <div class="ttgr-item-inner">
                                    <div class="portfolio-grid-item">
                                        <a href="{{ $mediaUrl ?: '#' }}" class="pgi-image-wrap {{ $mediaUrl ? '' : 'gallery-item-link-disabled' }}" data-cursor="{{ $cursorLabel }}">
                                            <div class="pgi-image-holder {{ $photo->is_video ? '' : 'cover-opacity-2' }}">
                                                <div class="pgi-image-inner tt-anim-zoomin">
                                                    @if($photo->is_video)
                                                        <figure class="pgi-video-wrap ttgr-height">
                                                            <video class="pgi-video" loop muted preload="metadata" playsinline>
                                                                <source src="{{ $photo->video_url }}" data-src="{{ $photo->video_url }}" type="{{ $photo->media_mime_type ?: 'video/mp4' }}">
                                                            </video>
                                                        </figure>
                                                    @else
                                                        <figure class="pgi-image ttgr-height">
                                                            <img src="{{ $photo->image_url }}" loading="lazy" alt="{{ $photo->display_alt_text }}">
                                                        </figure>
                                                    @endif
                                                </div>
                                            </div>
                                        </a>

                                        <div class="pgi-caption">
                                            <div class="pgi-caption-inner">
                                                <h2 class="pgi-title">
                                                    <a href="{{ $mediaUrl ?: '#' }}">{{ $photo->title ?: 'Galerie ERAH' }}</a>
                                                </h2>
                                                <div class="pgi-categories-wrap">
                                                    <div class="pgi-category">{{ $photo->category_label ?: ($photo->filter_label ?: 'Galerie') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="gallery-empty">
                                <h3>Galerie en preparation</h3>
                                <p>Aucune photo active n'est disponible pour le moment. Vous pourrez alimenter cette page depuis l'admin galerie.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($photos->count() > $initialVisibleCount)
        <div class="text-center" style="margin: 30px 0;">
            <a href="#" id="loadMoreBtn" class="tt-btn tt-btn-outline tt-magnetic-item" style="color: #fff;">
                <span data-hover="Afficher plus" style="color: #fff;">Afficher plus</span>
            </a>
        </div>
    @endif

    <div class="tt-section padding-bottom-xlg-120">
        <div class="tt-section-inner tt-wrap">
            <div class="tt-row margin-bottom-40">
                <div class="tt-col-xl-8">
                    <div class="tt-heading tt-heading-xxxlg no-margin">
                        <h3 class="tt-heading-subtitle tt-text-reveal">Rejoins-nous</h3>
                        <h2 class="tt-heading-title tt-text-reveal">Notre histoire</h2>
                    </div>
                </div>

                <div class="tt-col-xl-4 tt-align-self-end tt-xl-column-reverse margin-top-40">
                    <div class="max-width-600 margin-bottom-10 tt-text-uppercase tt-text-reveal">
                        Plonge dans notre univers, vis chaque moment fort avec nous<br>
                        et fais partie de l'histoire des maintenant !
                    </div>

                    <div class="tt-big-round-ptn margin-top-30 margin-bottom-xlg-80 tt-anim-fadeinup">
                        <a href="{{ route('marketing.contact') }}" class="tt-big-round-ptn-holder tt-magnetic-item">
                            <div class="tt-big-round-ptn-inner">Je<br> Rejoins !</div>
                        </a>
                    </div>
                </div>
            </div>
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
        var loadMoreButton = document.getElementById('loadMoreBtn');
        if (!loadMoreButton) {
            return;
        }

        var batchSize = 9;
        var hiddenSelector = '.tt-grid-item.is-hidden-by-default';

        function updateButton() {
            if (!document.querySelector(hiddenSelector)) {
                loadMoreButton.style.display = 'none';
            }
        }

        loadMoreButton.addEventListener('click', function (event) {
            event.preventDefault();

            Array.prototype.slice.call(document.querySelectorAll(hiddenSelector), 0, batchSize).forEach(function (item) {
                item.classList.remove('is-hidden-by-default');
            });

            if (window.jQuery) {
                var $grid = window.jQuery('.isotope-items-wrap');
                if ($grid.length && typeof $grid.isotope === 'function') {
                    $grid.isotope('layout');
                }
            }

            updateButton();
        });

        updateButton();
    });
</script>
@endsection
