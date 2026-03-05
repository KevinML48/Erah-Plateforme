@php
    $heroSubtitle = $heroSubtitle ?? 'ERAH Control Center';
    $heroTitle = $heroTitle ?? 'Admin Console';
    $heroDescription = $heroDescription ?? 'Pilotage global de la plateforme.';
    $heroMaskDescription = $heroMaskDescription ?? $heroDescription;
    $heroVideoPoster = $heroVideoPoster ?? '/template/assets/vids/1920/video-1-1920.jpg';
@endphp

<div id="page-header" class="ph-full ph-full-m ph-cap-xxxxlg ph-center ph-caption-parallax ph-image-parallax">
    <div class="ph-video ph-video-cover-6">
        <div class="ph-video-inner">
            <video loop muted autoplay playsinline preload="metadata" poster="{{ $heroVideoPoster }}">
                <source src="/template/assets/vids/placeholder.mp4" data-src="/template/assets/vids/1920/video-1-1920.mp4" type="video/mp4">
                <source src="/template/assets/vids/placeholder.webm" data-src="/template/assets/vids/1920/video-1-1920.webm" type="video/webm">
            </video>
        </div>
    </div>

    <div class="page-header-inner tt-wrap">
        <div class="ph-caption">
            <div class="ph-caption-inner">
                <h2 class="ph-caption-subtitle">{{ $heroSubtitle }}</h2>
                <h1 class="ph-caption-title">{{ $heroTitle }}</h1>
                <div class="ph-caption-description max-width-900">
                    {{ $heroDescription }}
                </div>
            </div>
        </div>
    </div>

    <div class="page-header-inner ph-mask">
        <div class="ph-mask-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">{{ $heroSubtitle }}</h2>
                    <h1 class="ph-caption-title">{{ $heroTitle }}</h1>
                    <div class="ph-caption-description max-width-900">
                        {{ $heroMaskDescription }}
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
                    <textPath xlink:href="#textcircle">Scroll To Explore - Scroll To Explore -</textPath>
                </text>
            </svg>
        </a>
    </div>
</div>
