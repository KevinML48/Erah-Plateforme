@extends('marketing.layouts.template')

@section('title', '404 Not Found | ERAH Esport')
@section('meta_description', 'Page introuvable sur le site ERAH Esport.')
@section('meta_keywords', 'ERAH Esport 404, page non trouvee')
@section('meta_author', 'ERAH Esport')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('content')
<div id="tt-page-content">
    <div class="tt-section no-padding">
        <div class="tt-section-inner tt-wrap">
            <div class="tt-404-error">
                <h2 class="tt-404-error-subtitle">404 Error</h2>
                <h1 class="tt-404-error-title">Oops!</h1>
                <div class="tt-404-error-description">Desole, la page demandee est introuvable ou a ete supprimee.</div>
                <a href="/" class="tt-btn tt-btn-secondary margin-top-40 tt-magnetic-item">Accueil</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('page_scripts')
@verbatim
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
@endverbatim
@endsection
