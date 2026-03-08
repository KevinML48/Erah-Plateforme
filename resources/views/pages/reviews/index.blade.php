@extends('marketing.layouts.template')

@section('title', 'Avis membres | ERAH')
@section('meta_description', 'Retrouvez tous les avis publies par les membres et soutiens du club ERAH.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.reviews.partials.styles')
@endsection

@section('content')
    <div id="page-header" class="ph-full ph-cap-xxxxlg ph-center ph-image-parallax">
        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">Communaute ERAH</h2>
                    <h1 class="ph-caption-title">Tous les avis</h1>
                    <div class="ph-caption-description max-width-900">
                        Les retours publies mettent en avant les membres, leurs parcours et les personnes qui suivent le projet de pres.
                    </div>
                    <div class="reviews-page-actions margin-top-30">
                        <span class="reviews-page-hero-meta">{{ (int) $publishedCount }} avis publies</span>
                        <a href="{{ auth()->check() ? route('profile.show') : route('login') }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                            <span data-hover="Deposer mon avis">Deposer mon avis</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="tt-row tt-lg-row-reverse">
                    <div class="tt-col-lg-4 margin-bottom-40">
                        <aside class="reviews-page-summary">
                            <strong>{{ (int) $publishedCount }}</strong>
                            <span>Avis visibles publiquement</span>
                            <p>Les cartes valorisent le membre, son profil public et ses informations de progression quand elles existent deja sur la plateforme.</p>
                            <div class="reviews-page-actions">
                                <a href="{{ route('marketing.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                    <span data-hover="Retour accueil">Retour accueil</span>
                                </a>
                            </div>
                        </aside>
                    </div>

                    <div class="tt-col-lg-8">
                        @if($reviews->count())
                            <div class="reviews-page-grid">
                                @foreach($reviews as $review)
                                    @include('pages.reviews.partials.card', ['review' => $review])
                                @endforeach
                            </div>

                            <div class="adm-pagin margin-top-40">{{ $reviews->links() }}</div>
                        @else
                            <div class="tt-heading">
                                <h2 class="tt-heading-title">Aucun avis public</h2>
                                <p class="max-width-700">La page se remplira au fur et a mesure des publications et des retours membres.</p>
                            </div>
                        @endif
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
