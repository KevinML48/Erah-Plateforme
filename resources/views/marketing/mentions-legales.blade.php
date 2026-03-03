@extends('marketing.layouts.template')


@section('title', 'Mentions Légales | ERAH Esport')

@section('meta_description', 'Consultez les mentions légales du site ERAH Esport : informations sur l’éditeur, l’association, l’hébergement et les conditions d’utilisation.')

@section('meta_keywords', 'Mentions légales ERAH Esport, informations légales, éditeur du site, association ERAH, hébergeur site web, conditions d’utilisation')

@section('meta_author', 'ERAH Esport')

@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')


@section('page_styles')
@verbatim

<style>
	/* Bandeau cookies stylé */
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
  box-shadow: 0 4px 20px rgba(0,0,0,0.4);
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

/* Animation */
@keyframes fadeIn {
  to { opacity: 1; }
}

/* Boutons stylés */
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

@media (max-width: 500px) {
  #cookie-banner div {
    display: flex;
    flex-direction: column;
    gap: 8px; /* espace vertical entre les boutons */
    width: 100%;
  }
  #cookie-banner div button {
    width: 100%; /* boutons plein largeur sur mobile */
  }
}
</style>

@endverbatim
@endsection


@section('content')
@verbatim

<div id="tt-page-content">
<div id="page-header"
     class="ph-full ph-full-m ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">

    <div class="page-header-inner tt-wrap">

        <div class="ph-caption">
            <div class="ph-caption-inner">
                <h2 class="ph-caption-subtitle">Informations légales</h2>
                <h1 class="ph-caption-title">Mentions Légales</h1>
                <div class="ph-caption-description max-width-700">
                    Retrouvez toutes les informations légales concernant l’association ERAH, son fonctionnement et la protection de vos données.
                </div>
            </div>
        </div>

    </div>

    <div class="page-header-inner ph-mask">
        <div class="ph-mask-inner tt-wrap">

            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">Mentions</h2>
                    <h1 class="ph-caption-title">Légales</h1>
                    <div class="ph-caption-description max-width-700">
                        Retrouvez toutes les informations légales concernant l’association ERAH, son fonctionnement et la protection de vos données.
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="ph-social">
        <ul>
            <li><a href="https://www.twitch.tv/erah_association" class="tt-magnetic-item" target="_blank"
                   rel="noopener"><i class="fa-brands fa-twitch"></i></a></li>
            <li><a href="https://www.instagram.com/erahesport/" class="tt-magnetic-item" target="_blank"
                   rel="noopener"><i class="fa-brands fa-instagram"></i></a></li>
            <li><a href="https://x.com/ErahEsport" class="tt-magnetic-item" target="_blank"
                   rel="noopener"><i class="fa-brands fa-twitter"></i></a></li>
            <li><a href="https://discord.gg/9G89kkSjRx" class="tt-magnetic-item" target="_blank"
                   rel="noopener"><i class="fa-brands fa-discord"></i></a></li>
        </ul>
    </div>

    <div class="tt-scroll-down">
        <a href="#tt-page-content" class="tt-scroll-down-inner tt-magnetic-item" data-offset="0">
            <div class="tt-scrd-icon"></div>
            <svg viewBox="0 0 500 500">
                <defs>
                    <path
                        d="M50,250c0-110.5,89.5-200,200-200s200,89.5,200,200s-89.5,200-200,200S50,360.5,50,250"
                        id="textcircle"></path>
                </defs>
                <text dy="30">
                    <textPath xlink:href="#textcircle">Consultez les Mentions Légales ERAH -</textPath>
                </text>
            </svg>
        </a>
    </div>

</div>


<div class="tt-section padding-top-lg-120 padding-top-80 padding-bottom-80">
    <div class="tt-section-inner tt-wrap max-width-1600">

        <div class="tt-row">
            <div class="tt-col-lg-3">

                <div class="tt-heading tt-heading-lg">
                    <h2 class="tt-heading-title tt-text-reveal">Mentions légales</h2>
                </div>

                <p class="text-muted">
                    Toutes les informations légales concernant l’association ERAH.
                </p>

            </div> <!-- /.tt-col -->

            <div class="tt-col-lg-1 padding-top-30"></div> <!-- /.tt-col -->

            <div class="tt-col-lg-8 tt-align-self-center">

                <div class="tt-accordion tt-ac-sm tt-ac-borders tt-ac-counter">

                    <div class="tt-accordion-item tt-anim-fadeinup">
                        <div class="tt-accordion-heading">
                            <div class="tt-ac-head cursor-alter">
                                <div class="tt-ac-head-inner">
                                    <h4 class="tt-ac-head-title">Éditeur du site</h4>
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
                        <div class="tt-accordion-content max-width-1000">
                            <p>Le présent site est édité par ERAH, association régie par la loi du 1er juillet 1901.</p>
                            <ul>
                                <li>Dénomination sociale : ERAH</li>
                                <li>Forme juridique : Association loi 1901</li>
                                <li>Siège social : 15 quai Petite Roubeyrolle, 48000, Mende, France</li>
                                <li>Numéro SIRET : 938 832 615</li>
                                <li>Code APE : 93.12Z</li>
                                <li>Numéro RNA : W482005898</li>
                                <li>Email : erah.association@gmail.com</li>
                                <li>Téléphone : +(33) 06 49 42 55 78</li>
                            </ul>
                        </div>
                    </div>

                    <div class="tt-accordion-item tt-anim-fadeinup">
                        <div class="tt-accordion-heading">
                            <div class="tt-ac-head cursor-alter">
                                <div class="tt-ac-head-inner">
                                    <h4 class="tt-ac-head-title">Direction de la publication</h4>
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
                        <div class="tt-accordion-content max-width-1000">
                            <ul>
                                <li>Directeur de la publication : Kevin Molines</li>
                                <li>Responsable éditorial : Service Communication de l’association</li>
                            </ul>
                        </div>
                    </div>

                    <div class="tt-accordion-item tt-anim-fadeinup">
    <div class="tt-accordion-heading">
        <div class="tt-ac-head cursor-alter">
            <div class="tt-ac-head-inner">
                <h4 class="tt-ac-head-title">Hébergement</h4>
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
    <div class="tt-accordion-content max-width-1000">
        <ul>
            <li>Nom de l’hébergeur : Netlify</li>
            <li>Adresse : 2325 3rd Street, Suite 296, San Francisco, CA 94107, États-Unis</li>
            <li>Site web : <a href="https://www.netlify.com" target="_blank" class="tt-link" rel="noopener noreferrer">https://www.netlify.com</a></li>
        </ul>
    </div>
</div>


                    <div class="tt-accordion-item tt-anim-fadeinup">
                        <div class="tt-accordion-heading">
                            <div class="tt-ac-head cursor-alter">
                                <div class="tt-ac-head-inner">
                                    <h4 class="tt-ac-head-title">Propriété intellectuelle</h4>
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
                        <div class="tt-accordion-content max-width-1000">
                            <p>Tous les contenus présents sur ce site sont la propriété exclusive de l’association ERAH, sauf mention contraire. Toute reproduction ou adaptation est interdite sans autorisation écrite préalable.</p>
                            <p>Tous droits réservés.</p>
                        </div>
                    </div>

                    <div class="tt-accordion-item tt-anim-fadeinup">
                        <div class="tt-accordion-heading">
                            <div class="tt-ac-head cursor-alter">
                                <div class="tt-ac-head-inner">
                                    <h4 class="tt-ac-head-title">Responsabilité</h4>
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
                        <div class="tt-accordion-content max-width-1000">
                            <p>ERAH s’efforce de fournir des informations précises mais ne peut être tenue responsable des omissions ou inexactitudes. L’association décline toute responsabilité concernant les interruptions du site ou les liens vers d’autres sites.</p>
                        </div>
                    </div>

                    <div class="tt-accordion-item tt-anim-fadeinup">
                        <div class="tt-accordion-heading">
                            <div class="tt-ac-head cursor-alter">
                                <div class="tt-ac-head-inner">
                                    <h4 class="tt-ac-head-title">Collecte des données et cookies</h4>
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
                        <div class="tt-accordion-content max-width-1000">
                            <p>Les données collectées via le site sont utilisées uniquement pour répondre aux demandes de contact. Elles ne sont pas partagées avec des tiers et sont temporairement hébergées sur les serveurs de Netlify avant envoi par e-mail.</p>
                            <p>Le site utilise Google Analytics. Un bandeau de consentement permet d’accepter ou de refuser ces cookies.</p>
                        </div>
                    </div>

                    <div class="tt-accordion-item tt-anim-fadeinup">
                        <div class="tt-accordion-heading">
                            <div class="tt-ac-head cursor-alter">
                                <div class="tt-ac-head-inner">
                                    <h4 class="tt-ac-head-title">Droits des utilisateurs</h4>
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
                        <div class="tt-accordion-content max-width-1000">
                            <p>Conformément au RGPD et à la loi Informatique et Libertés, vous disposez d’un droit d’accès, de rectification et de suppression de vos données. <br> <br> Pour exercer vos droits, contactez : <a href="mailto:erah.association@gmail.com">erah.association@gmail.com</a></p>
                        </div>
                    </div>

                </div> <!-- End accordion -->

            </div> <!-- /.tt-col -->
        </div><!-- /.tt-row -->

    </div> <!-- /.tt-section-inner -->
</div>


@endverbatim
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
