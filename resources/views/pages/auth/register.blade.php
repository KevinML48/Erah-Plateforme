@extends('marketing.layouts.template')

@section('title', 'Inscription | ERAH Plateforme')
@section('meta_description', 'Creez votre compte ERAH et rejoignez la plateforme esports.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('content')
    <div id="page-header" class="ph-full ph-full-m ph-cap-xxxxlg ph-center ph-caption-parallax ph-image-parallax">
        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">ERAH Plateforme</h2>
                    <h1 class="ph-caption-title">Creer un compte</h1>
                    <div class="ph-caption-description max-width-700">
                        Ouvrez votre espace personnel pour suivre votre progression et vos activites.
                    </div>
                </div>
            </div>
        </div>

        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">ERAH Plateforme</h2>
                        <h1 class="ph-caption-title">Creer un compte</h1>
                        <div class="ph-caption-description max-width-700">
                            Inscription rapide avec vos informations essentielles.
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

    <div id="tt-page-content">
        <div class="tt-section padding-bottom-xlg-140">
            <div class="tt-section-inner tt-wrap max-width-1400">
                <div class="tt-row">
                    <div class="tt-col-xl-5 padding-right-xlg-40 margin-bottom-60 no-margin-xlg-bottom">
                        <div class="tt-heading tt-heading-lg margin-bottom-40">
                            <h3 class="tt-heading-subtitle tt-text-reveal">Nouveau membre</h3>
                            <h2 class="tt-heading-title tt-text-reveal">Rejoignez ERAH</h2>
                            <p class="max-width-500 tt-anim-fadeinup text-gray">
                                Creez votre compte pour acceder aux missions, matchs, clips et classements.
                            </p>
                        </div>

                        <div class="tt-form-group">
                            <a href="{{ url('/auth/google/redirect') }}" class="tt-btn tt-btn-secondary tt-magnetic-item">
                                <span data-hover="Inscription Google">Inscription Google</span>
                            </a>
                        </div>

                        <div class="tt-form-group">
                            <a href="{{ url('/auth/discord/redirect') }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                <span data-hover="Inscription Discord">Inscription Discord</span>
                            </a>
                        </div>
                    </div>

                    <div class="tt-col-xl-7 padding-left-xlg-40">
                        <form method="POST" action="{{ route('auth.register') }}" class="tt-form tt-form-creative tt-form-lg">
                            @csrf

                            <div class="tt-form-group">
                                <label for="name">Nom</label>
                                <input class="tt-form-control" id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Votre pseudo ou nom" required autocomplete="name">
                            </div>

                            <div class="tt-form-group">
                                <label for="email">Adresse email</label>
                                <input class="tt-form-control" id="email" name="email" type="email" value="{{ old('email') }}" placeholder="email@domaine.com" required autocomplete="email">
                            </div>

                            <div class="tt-form-group">
                                <label for="password">Mot de passe</label>
                                <input class="tt-form-control" id="password" name="password" type="password" placeholder="Minimum 8 caracteres" required autocomplete="new-password">
                            </div>

                            <div class="tt-form-group">
                                <label for="password_confirmation">Confirmation mot de passe</label>
                                <input class="tt-form-control" id="password_confirmation" name="password_confirmation" type="password" placeholder="Retapez le mot de passe" required autocomplete="new-password">
                            </div>

                            <div class="tt-form-group">
                                <div class="tt-form-check">
                                    <input type="checkbox" id="remember" name="remember" value="1" @checked(old('remember', true))>
                                    <label for="remember">Se souvenir de moi</label>
                                </div>
                            </div>

                            <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                <span data-hover="Creer mon compte">Creer mon compte</span>
                            </button>

                            <div class="tt-form-text margin-top-20">
                                Deja inscrit ? <a href="{{ route('login') }}" class="tt-link">Se connecter</a>
                            </div>
                        </form>
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
@endsection
