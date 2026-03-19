@extends('marketing.layouts.template')

@php
        $profiles = [
                [
                        'name' => '17Saizen',
                        'category' => 'Rocket League',
                        'role' => 'Joueur',
                        'href' => 'https://x.com/17saizen',
                        'image' => '/template/assets/img/rocket-league/saizen.jpg',
                ],
                [
                        'name' => 'AnoriQK',
                        'category' => 'Rocket League',
                        'role' => 'Joueur',
                        'href' => 'https://x.com/AnoriQK',
                        'image' => '/template/assets/img/rocket-league/anoriq.jpg',
                ],
                [
                        'name' => 'MayKooRL',
                        'category' => 'Rocket League',
                        'role' => 'Joueur',
                        'href' => 'https://x.com/MayKooRL',
                        'image' => '/template/assets/img/rocket-league/mayko.jpg',
                ],
                [
                        'name' => 'BeastBound',
                        'category' => 'Coach',
                        'role' => 'Staff',
                        'href' => 'https://x.com/BeastBoundLive',
                        'image' => '/template/assets/img/rocket-league/BeastBound.jpg',
                ],
                [
                        'name' => 'Zhin',
                        'category' => 'Manager',
                        'role' => 'Staff',
                        'href' => 'https://x.com/Zhin_rl',
                        'image' => '/template/assets/img/rocket-league/zhin.jpg',
                ],
        ];
@endphp

@section('title', 'Rocket League | ERAH Esport')
@section('meta_description', 'Découvrez notre équipe Rocket League avec ERAH Esport : joueurs, staff et ambitions compétitives.')
@section('meta_keywords', 'Rocket League, ERAH Esport, equipe Rocket League, roster Rocket League, joueurs Rocket League, staff Rocket League, esport Lozere')
@section('meta_author', 'ERAH Esport')
@section('canonical', route('marketing.rocket-league'))
@section('meta_image', '/template/assets/img/rocket-league-team.png')
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
        font-family: 'Arial', sans-serif;
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
        transition: transform 0.2s, background 0.2s;
    }

    #cookie-banner button#accept-cookies {
        background: #4CAF50;
        color: #fff;
    }

    #cookie-banner button#accept-cookies:hover {
        transform: scale(1.05);
        background: #45a049;
    }

    #cookie-banner button#reject-cookies {
        background: #f44336;
        color: #fff;
    }

    #cookie-banner button#reject-cookies:hover {
        transform: scale(1.05);
        background: #d7372a;
    }

    .rocket-intro {
        display: grid;
        grid-template-columns: minmax(0, 1.2fr) minmax(0, 0.8fr);
        gap: 28px;
        align-items: center;
    }

    .rocket-intro-card {
        padding: 36px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 24px;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
    }

    .rocket-intro-card p,
    .rocket-intro-card li {
        font-size: 17px;
        line-height: 1.8;
        color: rgba(255, 255, 255, 0.82);
    }

    .rocket-pill {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px;
        border-radius: 999px;
        background: rgba(216, 7, 7, 0.18);
        border: 1px solid rgba(216, 7, 7, 0.35);
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .rocket-highlight {
        font-size: clamp(42px, 8vw, 88px);
        line-height: 0.95;
        font-weight: 700;
        margin: 14px 0 18px;
    }

    .rocket-meta {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-top: 30px;
    }

    .rocket-meta-item {
        padding: 18px 20px;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .rocket-meta-label {
        display: block;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: rgba(255, 255, 255, 0.55);
        margin-bottom: 8px;
    }

    .rocket-meta-value {
        display: block;
        font-size: 18px;
        font-weight: 600;
        color: #fff;
    }

    .rocket-preview-media {
        border-radius: 24px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .rocket-preview-media video,
    .rocket-preview-media img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    @media (max-width: 991px) {
        .rocket-intro,
        .rocket-meta {
            grid-template-columns: 1fr;
        }

        .rocket-intro-card {
            padding: 28px;
        }
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
</style>
@endsection

@section('content')
<div id="page-header" class="ph-full ph-full-m ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
    <div class="page-header-inner tt-wrap">
        <div class="ph-caption">
            <div class="ph-caption-inner">
                <h2 class="ph-caption-subtitle">Présentation officielle</h2>
                <h1 class="ph-caption-title">Notre &eacute;quipe Rocket League</h1>
                <div class="ph-caption-description max-width-700">
                    Nous sommes fiers de vous présenter notre équipe Rocket League.<br>
                    Une formation engagée qui portera les couleurs d <strong>ERAH Esport</strong> en compétition.
                </div>
            </div>
        </div>
    </div>

    <div class="page-header-inner ph-mask">
        <div class="ph-mask-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">Fiers de notre roster</h2>
                    <h1 class="ph-caption-title">Joueurs et staff</h1>
                    <div class="ph-caption-description max-width-700">
                        Découvrez les joueurs et le staff qui accompagnent le projet Rocket League d <strong>ERAH Esport</strong> sur la saison en cours.
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
                    <textPath xlink:href="#textcircle">Fiers de notre équipe Rocket League - ERAH Esport</textPath>
                </text>
            </svg>
        </a>
    </div>
</div>

<div id="tt-page-content">
    <div class="tt-section padding-top-xlg-100 padding-bottom-xlg-100">
        <div class="tt-section-inner tt-wrap">
            <div class="rocket-intro">
                <div class="rocket-intro-card">
                    <span class="rocket-pill">Rocket League</span>
                    <div class="rocket-highlight">Notre equipe<br>RLCS</div>
                    <p>
                        Voici notre equipe Rocket League engagee sur le circuit RLCS. Nous visons clairement la performance,
                        l exigence collective et l ambition de creer l histoire sous les couleurs d ERAH Esport.
                    </p>
                    <div class="rocket-meta">
                        <div class="rocket-meta-item">
                            <span class="rocket-meta-label">Format</span>
                            <span class="rocket-meta-value">3 joueurs</span>
                        </div>
                        <div class="rocket-meta-item">
                            <span class="rocket-meta-label">Encadrement</span>
                            <span class="rocket-meta-value">Coach + manager</span>
                        </div>
                        <div class="rocket-meta-item">
                            <span class="rocket-meta-label">Suivi</span>
                            <span class="rocket-meta-value">Twitch ERAH</span>
                        </div>
                    </div>
                </div>

                <div class="rocket-preview-media">
                    <video autoplay loop muted playsinline preload="metadata" poster="{{ asset('template/assets/img/rocket-league-team.png') }}" aria-label="Presentation Rocket League ERAH">
                        <source src="{{ asset('template/assets/vids/rocket-league/rendu-final.mp4') }}" type="video/mp4">
                    </video>
                </div>
            </div>
        </div>
    </div>

    <div class="tt-section">
        <div class="tt-section-inner">
            <div class="tt-portfolio-preview-list tt-ppli-portrait tt-ppli-hover">
                <div class="tt-ppl-items-list">
                    @foreach ($profiles as $profile)
                        <a href="{{ $profile['href'] }}" class="tt-ppl-item" target="_blank" rel="noopener">
                            <div class="tt-ppli-preview">
                                <div class="tt-ppli-preview-image">
                                    <img src="{{ $profile['image'] }}" alt="{{ $profile['name'] }}">
                                </div>
                            </div>

                            <div class="tt-ppl-item-inner">
                                <div class="tt-ppl-item-holder">
                                    <div class="tt-ppli-col tt-ppli-col-count">
                                        <div class="tt-ppli-count"></div>
                                    </div>
                                    <div class="tt-ppli-col tt-ppli-col-caption">
                                        <div class="tt-ppli-caption">
                                            <h2 class="tt-ppli-title">{{ $profile['name'] }}</h2>
                                            <div class="tt-ppli-categories">
                                                <div class="tt-ppli-category">{{ $profile['category'] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tt-ppli-col tt-ppli-col-info tt-justify-content-md-end">
                                        <div class="tt-ppli-info">{{ $profile['role'] }}</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="tt-section padding-top-xlg-120 padding-bottom-xlg-120">
        <div class="tt-section-inner tt-wrap">
            <div class="tt-row margin-bottom-40">
                <div class="tt-col-xl-8">
                    <div class="tt-heading tt-heading-xxxlg no-margin">
                        <h3 class="tt-heading-subtitle tt-text-reveal">Recrutement</h3>
                        <h2 class="tt-heading-title tt-text-reveal">Rejoins<br> l Aventure</h2>
                    </div>
                </div>

                <div class="tt-col-xl-4 tt-align-self-end tt-xl-column-reverse margin-top-40">
                    <div class="max-width-600 margin-bottom-10 tt-text-uppercase tt-text-reveal">
                        Tu veux faire partie de notre équipe ?<br>
                        Envoie ta candidature et montre-nous ta motivation.
                    </div>

                    <div class="tt-big-round-ptn margin-top-30 margin-bottom-xlg-80 tt-anim-fadeinup">
                        <a href="{{ route('marketing.contact') }}" class="tt-big-round-ptn-holder tt-magnetic-item">
                            <div class="tt-big-round-ptn-inner">Postuler<br> Maintenant</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
    @include('marketing.partials.theme-scripts')
@endsection