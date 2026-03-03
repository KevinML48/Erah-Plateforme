@extends('marketing.layouts.template')


@section('title', 'Stages | ERAH Esport')

@section('meta_description', 'Découvrez les opportunités de stages chez ERAH Esport à Mende (Lozère). Rejoignez notre structure pour développer vos compétences dans l’esport, la communication, l’événementiel et le management.')

@section('meta_keywords', 'ERAH Esport, stage esport, stage communication esport, stage événementiel, stage marketing esport, stage management esport, esport Lozère, esport Mende, association esport, opportunités de stage')

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

    /* Animation */
    @keyframes fadeIn {
        to {
            opacity: 1;
        }
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
            gap: 8px;
            /* espace vertical entre les boutons */
            width: 100%;
        }

        #cookie-banner div button {
            width: 100%;
            /* boutons plein largeur sur mobile */
        }
    }

    .logos-center {
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        gap: 40px;
        /* espace entre les logos */
        list-style: none;
        margin: 0;
        padding: 0;
    }
</style>

@endverbatim
@endsection


@section('content')
@verbatim

<div id="page-header" class="ph-full ph-full-m ph-cap-xxxlg ph-image-parallax ph-caption-parallax">

                <div class="page-header-inner tt-wrap">
    <div class="ph-caption">
        <div class="ph-caption-inner">
            <h2 class="ph-caption-subtitle">ERAH Esport</h2>
            <h1 class="ph-caption-title">Des <span class="text-main">Stages</span><br> dans l’Esport</h1>
        </div>
    </div>
</div>

<div class="page-header-inner ph-mask">
    <div class="ph-mask-inner tt-wrap">
        <div class="ph-caption">
            <div class="ph-caption-inner">
                <h2 class="ph-caption-subtitle">ERAH Esport</h2>
                <h1 class="ph-caption-title">Accompagner<br> les talents</h1>
            </div>
        </div>
    </div>
</div>



                <div class="ph-social">
                    <ul>
                        <li>
                            <a href="https://www.twitch.tv/erah_association" class="tt-magnetic-item" target="_blank"
                                rel="noopener" aria-label="Twitch ERAH Esport">
                                <i class="fa-brands fa-twitch"></i>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.instagram.com/erahesport/" class="tt-magnetic-item" target="_blank"
                                rel="noopener" aria-label="Instagram ERAH Esport">
                                <i class="fa-brands fa-instagram"></i>
                            </a>
                        </li>
                        <li>
                            <a href="https://x.com/ErahEsport" class="tt-magnetic-item" target="_blank" rel="noopener"
                                aria-label="X ERAH Esport">
                                <i class="fa-brands fa-twitter"></i>
                            </a>
                        </li>
                        <li>
                            <a href="https://discord.gg/9G89kkSjRx" class="tt-magnetic-item" target="_blank"
                                rel="noopener" aria-label="Discord ERAH Esport">
                                <i class="fa-brands fa-discord"></i>
                            </a>
                        </li>
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
                                <textPath xlink:href="#textcircle">Formez-vous Découvrez Grandissez avec ERAH -
                                </textPath>
                            </text>
                        </svg>
                    </a>
                </div>

            </div>


            <div id="tt-page-content">


                <div class="tt-section no-padding-bottom padding-bottom-xlg-80">
    <div class="tt-section-inner tt-wrap">

        <div class="tt-row">
            <div class="tt-col-xl-8">
                <div class="tt-heading tt-heading-xxxlg no-margin">
                    <h3 class="tt-heading-subtitle tt-text-reveal">Opportunités</h3>
                    <h2 class="tt-heading-title tt-text-reveal">Stages</h2>
                </div>
            </div>

            <div class="tt-col-xl-4 tt-align-self-end margin-top-40">
                <div
                    class="tt-text-uppercase max-width-400 margin-bottom-20 text-pretty tt-text-reveal">
                    Nous proposons uniquement des <strong>stages liés à l’esport</strong>, en lien direct avec nos
                    projets : compétitions, communication, partenariats et événements.
                </div>
            </div>
        </div>

    </div>
</div>




                <div class="tt-section" id="ressources">
    <div class="tt-section-inner">

        <div class="tt-accordion tt-ac-xxlg tt-ac-hover tt-ac-counter tt-ac-borders">

            <!-- Stage Développement Web -->
            <div class="tt-accordion-item tt-anim-fadeinup">
                <div class="tt-accordion-heading">
                    <div class="tt-ac-head cursor-alter">
                        <div class="tt-ac-head-inner">
                            <h4 class="tt-ac-head-title">Développement Web</h4>
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

                <div class="tt-accordion-content max-width-1400">
                    <p>Le stagiaire participera au <strong>développement</strong> et à
                        l’<strong>amélioration de nos outils numériques</strong> et de notre site web,
                        en lien direct avec nos <span style="color:red; font-weight:bold;">projets
                            esport</span>.
                        Les missions incluent la <strong>création de pages dynamiques</strong>,
                        l’<strong>optimisation de contenus</strong> et le <strong>développement de
                            fonctionnalités adaptées</strong> à l’association.</p>
                    <a href="/contact" class="tt-btn tt-btn-outline tt-magnetic-item">
                        <span data-hover="Postuler">Postuler</span>
                    </a>
                </div>
            </div>

            <!-- Stage Développement d’application -->
            <div class="tt-accordion-item tt-anim-fadeinup">
                <div class="tt-accordion-heading">
                    <div class="tt-ac-head cursor-alter">
                        <div class="tt-ac-head-inner">
                            <h4 class="tt-ac-head-title">Développement d’Applications</h4>
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

                <div class="tt-accordion-content max-width-1400">
                    <p>Ce stage offre l’opportunité de travailler sur des <span
                            style="color:red; font-weight:bold;">projets numériques concrets pour ERAH
                            Esport</span>.
                        Le stagiaire pourra <strong>concevoir et développer des outils</strong>,
                        <strong>améliorer les solutions internes déjà existantes</strong> et
                        <strong>trouver des réponses numériques aux problématiques rencontrées par le
                            club</strong> (gestion d’événements, suivi des joueurs, automatisation de
                        tâches, etc.).
                        <br><br>
                        <span style="color:red; font-weight:bold;">Une expérience idéale pour mettre en
                            pratique ses compétences tout en apportant une réelle valeur ajoutée à
                            l’association.</span>
                    </p>
                    <a href="/contact" class="tt-btn tt-btn-outline tt-magnetic-item">
                        <span data-hover="Postuler">Postuler</span>
                    </a>
                </div>
            </div>

            <!-- Stage Commerce -->
            <div class="tt-accordion-item tt-anim-fadeinup">
                <div class="tt-accordion-heading">
                    <div class="tt-ac-head cursor-alter">
                        <div class="tt-ac-head-inner">
                            <h4 class="tt-ac-head-title">Commerce & Partenariats</h4>
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

                <div class="tt-accordion-content max-width-1400">
                    <p>Le stagiaire participera au <strong>développement commercial de
                            l’association</strong> : recherche de sponsors, mise en place de
                        partenariats, suivi de la relation avec les acteurs du secteur.
                        <span style="color:red; font-weight:bold;">Toutes les missions seront
                            directement reliées aux besoins et projets d’ERAH Esport.</span>
                    </p>
                    <a href="/contact" class="tt-btn tt-btn-outline tt-magnetic-item">
                        <span data-hover="Postuler">Postuler</span>
                    </a>
                </div>
            </div>

            <!-- Stage Graphisme -->
            <div class="tt-accordion-item tt-anim-fadeinup">
                <div class="tt-accordion-heading">
                    <div class="tt-ac-head cursor-alter">
                        <div class="tt-ac-head-inner">
                            <h4 class="tt-ac-head-title">Graphisme & Design</h4>
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

                <div class="tt-accordion-content max-width-1400">
                    <p>Le stagiaire travaillera sur l’<strong>identité visuelle d’ERAH Esport</strong> :
                        création de visuels pour nos équipes, événements, réseaux sociaux et supports de
                        communication.
                        <span style="color:red; font-weight:bold;">Chaque projet sera pensé pour
                            renforcer l’image et l’impact de l’association.</span>
                    </p>
                    <a href="/contact" class="tt-btn tt-btn-outline tt-magnetic-item">
                        <span data-hover="Postuler">Postuler</span>
                    </a>
                </div>
            </div>

            <!-- Stage Vidéo -->
            <div class="tt-accordion-item tt-anim-fadeinup">
                <div class="tt-accordion-heading">
                    <div class="tt-ac-head cursor-alter">
                        <div class="tt-ac-head-inner">
                            <h4 class="tt-ac-head-title">Montage Vidéo</h4>
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

                <div class="tt-accordion-content max-width-1400">
                    <p>Le stagiaire participera à la <strong>création de contenus vidéos</strong> pour
                        l’association : trailers, aftermovies d’événements, interviews de joueurs et
                        contenus pour nos plateformes (YouTube, Twitch, réseaux sociaux).
                        <span style="color:red; font-weight:bold;">Chaque production sera réalisée
                            autour des activités d’ERAH Esport.</span>
                    </p>
                    <a href="/contact" class="tt-btn tt-btn-outline tt-magnetic-item">
                        <span data-hover="Postuler">Postuler</span>
                    </a>
                </div>
            </div>

        </div>

    </div>
</div>

<div class="tt-section no-padding-bottom">
                    <div class="tt-section-inner max-width-2200">

                        <div id="portfolio-grid" class="pgi-hover">

                            <div class="tt-grid ttgr-layout-creative-2 ttgr-gap-4">

                                <div class="tt-grid-top display-flex tt-justify-content-end no-padding-bottom">

                                    <a href="#ressources" class="tt-btn tt-btn-link tt-magnetic-item">
                                        <span class="tt-btn-icon hide-from-sm"><i class="tt-btn-line"></i></span>
                                        <span data-hover="Nos Ressources">Nos Ressources</span>
                                    </a>

                                </div>

                                <div class="tt-grid-items-wrap isotope-items-wrap">

                                    <div class="tt-grid-item isotope-item lifestyle">
                                        <div class="ttgr-item-inner">

                                            <div class="portfolio-grid-item">
                                                <a href="/evenement" class="pgi-image-wrap"
                                                    data-cursor="Nos Actions">

                                                    <div class="pgi-image-holder">
                                                        <div class="pgi-image-inner tt-anim-zoomin">
                                                            <figure class="pgi-image ttgr-height">
                                                                <img src="/template/assets/img/VCL_LINE_UP.png"
                                                                    loading="lazy" alt="Showmatch Affiche">
                                                            </figure>
                                                        </div>
                                                    </div>
                                                </a>

                                                <div class="pgi-caption">
                                                    <div class="pgi-caption-inner">
                                                        <h2 class="pgi-title">
                                                            <a href="/single-project-1">Événements & Tournois</a>
                                                        </h2>
                                                        <div class="pgi-categories-wrap">
                                                            <div class="pgi-category">Organisation compétitive</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tt-grid-item isotope-item lifestyle">
                                        <div class="ttgr-item-inner">

                                            <div class="portfolio-grid-item">
                                                <a href="/evenement" class="pgi-image-wrap"
                                                    data-cursor="Nos Actions">

                                                    <div class="pgi-image-holder">
                                                        <div class="pgi-image-inner tt-anim-zoomin">
                                                            <figure class="pgi-image ttgr-height">
                                                                <img src="/template/assets/img/galerie/bracket-up-down.jpg"
                                                                    loading="lazy" alt="Road To Champions">
                                                            </figure>
                                                        </div>
                                                    </div>
                                                </a>

                                                <div class="pgi-caption">
                                                    <div class="pgi-caption-inner">
                                                        <h2 class="pgi-title">
                                                            <a href="/single-project-2">Projets Esport</a>
                                                        </h2>
                                                        <div class="pgi-categories-wrap">
                                                            <div class="pgi-category">Ligues, compétitions</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tt-grid-item isotope-item artistic">
                                        <div class="ttgr-item-inner">

                                            <div class="portfolio-grid-item">
                                                <a href="/galerie-video" class="pgi-image-wrap"
                                                    data-cursor="Nos Médias">

                                                    <div class="pgi-image-holder">
                                                        <div class="pgi-image-inner tt-anim-zoomin">
                                                            <figure class="pgi-video-wrap ttgr-height">
                                                                <video class="pgi-video" loop muted autoplay playsinline
    poster="/template/assets/img/galerie/interview-GA-2025.webp">
    <source src="/template/assets/vids/Trailer site.mp4" type="video/mp4">
    <source src="/template/assets/vids/Trailer site.webm" type="video/webm">
</video>

                                                            </figure>
                                                        </div>
                                                    </div>
                                                </a>

                                                <div class="pgi-caption">
                                                    <div class="pgi-caption-inner">
                                                        <h2 class="pgi-title">
                                                            <a href="/single-project-3">Production Vidéo</a>
                                                        </h2>
                                                        <div class="pgi-categories-wrap">
                                                            <div class="pgi-category">Trailer, Aftermovies</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tt-grid-item isotope-item artistic">
                                        <div class="ttgr-item-inner">

                                            <div class="portfolio-grid-item">
                                                <a href="/galerie-photos" class="pgi-image-wrap"
                                                    data-cursor="Nos Actions">

                                                    <div class="pgi-image-holder">
                                                        <div class="pgi-image-inner tt-anim-zoomin">
                                                            <figure class="pgi-image ttgr-height">
                                                                <img src="/template/assets/img/galerie/intervention-2.webp"
                                                                    loading="lazy" alt="Intervention Pédagogique">
                                                            </figure>
                                                        </div>
                                                    </div>
                                                </a>

                                                <div class="pgi-caption">
                                                    <div class="pgi-caption-inner">
                                                        <h2 class="pgi-title">
                                                            <a href="/single-project-4">Ateliers & Formations</a>
                                                        </h2>
                                                        <div class="pgi-categories-wrap">
                                                            <div class="pgi-category">Sensibilisation & pédagogie</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tt-grid-item isotope-item wellness">
                                        <div class="ttgr-item-inner">

                                            <div class="portfolio-grid-item">
                                                <a href="/galerie-photos" class="pgi-image-wrap"
                                                    data-cursor="Nos Partenariats">

                                                    <div class="pgi-image-holder">
                                                        <div class="pgi-image-inner tt-anim-zoomin">
                                                            <figure class="pgi-image ttgr-height">
                                                                <img src="/template/assets/img/galerie/acteur-sport-2025-1.jpg"
                                                                    loading="lazy" alt="Mission Locale">
                                                            </figure>
                                                        </div>
                                                    </div>
                                                </a>

                                                <div class="pgi-caption">
                                                    <div class="pgi-caption-inner">
                                                        <h2 class="pgi-title">
                                                            <a href="/single-project-5">Partenariats & Impact</a>
                                                        </h2>
                                                        <div class="pgi-categories-wrap">
                                                            <div class="pgi-category">Institutions & Territoires</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>
                </div>


<div class="tt-section">
    <div class="tt-section-inner tt-wrap">

        <div class="tt-heading tt-heading-lg tt-heading-center margin-bottom-140">
            <h2 class="tt-heading-title tt-text-reveal">Notre Équipe de Stages</h2>
            <p class="max-width-600 tt-anim-fadeinup text-muted">
                Découvrez les personnes qui encadrent et accompagnent nos stagiaires au sein d'ERAH Esport.
            </p>
        </div>

        <!-- Yusoh -->
        <div class="tt-sticker">
            <div class="tt-row">
                <div class="tt-col-lg-6 margin-bottom-40">
                    <div class="tt-sticker-sticky tt-sticky-element">
                        <div class="tt-heading">
                            <h2 class="tt-heading-title tt-text-reveal">Yusoh</h2>
                            <p class="max-width-500 tt-text-reveal">
                                <a href="#ressources" class="tt-link"><strong>Maître de stage</strong></a> et 
                                <a href="#ressources" class="tt-link"><strong>CEO de l'association ERAH Esport</strong></a>.  
                                Il encadre directement les stagiaires et supervise les projets liés au 
                                <a href="#ressources" class="tt-link"><strong>développement web</strong></a>.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="tt-col-lg-6">
                    <div class="tt-sticker-scroller">
                        <div class="tt-image tti-border-radius tti-portrait margin-bottom-40">
                            <figure>
                                <a href="/template/assets/img/staffs-erah/yusoh_11_11zon.jpg"
                                    class="tt-image-link" data-cursor="View" data-fancybox data-caption="Yusoh">
                                    <img src="/template/assets/img/staffs-erah/yusoh_11_11zon.jpg"
                                        class="tt-anim-zoomin" loading="lazy" alt="Yusoh">
                                </a>
                                <figcaption>Yusoh</figcaption>
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Riperle -->
        <div class="tt-sticker">
            <div class="tt-row tt-lg-row-reverse">
                <div class="tt-col-lg-6 margin-bottom-40 padding-left-lg-5-p">
                    <div class="tt-sticker-sticky tt-sticky-element">
                        <div class="tt-heading">
                            <h2 class="tt-heading-title tt-text-reveal">Riperle</h2>
                            <p class="max-width-500 tt-text-reveal">
                                <a href="#ressources" class="tt-link"><strong>Commercial</strong></a> au sein de l'association.  
                                Ancien stagiaire chez ERAH Esport, il a rejoint officiellement l’équipe après son stage.  
                                Il accompagne aujourd’hui les stagiaires dans la découverte du 
                                <a href="#ressources" class="tt-link"><strong>commerce et des partenariats</strong></a>.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="tt-col-lg-6">
                    <div class="tt-sticker-scroller">
                        <div class="tt-image tti-border-radius tti-portrait margin-bottom-40">
                            <figure>
                                <a href="/template/assets/img/staffs-erah/reperle_6_11zon.jpg"
                                    class="tt-image-link" data-cursor="View" data-fancybox data-caption="Riperle">
                                    <img src="/template/assets/img/staffs-erah/reperle_6_11zon.jpg"
                                        class="tt-anim-zoomin" loading="lazy" alt="Riperle">
                                </a>
                                <figcaption>Riperle</figcaption>
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Oxwig -->
        <div class="tt-sticker">
            <div class="tt-row">
                <div class="tt-col-lg-6 margin-bottom-40">
                    <div class="tt-sticker-sticky tt-sticky-element">
                        <div class="tt-heading">
                            <h2 class="tt-heading-title tt-text-reveal">Oxwig</h2>
                            <p class="max-width-500 tt-text-reveal">
                                <a href="#ressources" class="tt-link"><strong>Head Valorant</strong></a> depuis 2 ans.  
                                Il propose des <a href="#ressources" class="tt-link"><strong>journées immersives</strong></a> où les stagiaires découvrent la gestion d’une équipe esport, la préparation de matchs et les coulisses du coaching compétitif.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="tt-col-lg-6">
                    <div class="tt-sticker-scroller">
                        <div class="tt-image tti-border-radius tti-portrait margin-bottom-40">
                            <figure>
                                <a href="/template/assets/img/staffs-erah/oxwig_3_11zon.jpg"
                                    class="tt-image-link" data-cursor="View" data-fancybox data-caption="Oxwig">
                                    <img src="/template/assets/img/staffs-erah/oxwig_3_11zon.jpg"
                                        class="tt-anim-zoomin" loading="lazy" alt="Oxwig">
                                </a>
                                <figcaption>Oxwig</figcaption>
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ShawnWeak -->
        <div class="tt-sticker">
            <div class="tt-row tt-lg-row-reverse">
                <div class="tt-col-lg-6 margin-bottom-40 padding-left-lg-5-p">
                    <div class="tt-sticker-sticky tt-sticky-element">
                        <div class="tt-heading">
                            <h2 class="tt-heading-title tt-text-reveal">ShawnWeak</h2>
                            <p class="max-width-500 tt-text-reveal">
                                <a href="#ressources" class="tt-link"><strong>Monteur vidéo</strong></a>.  
                                Il intervient lors de <a href="#ressources" class="tt-link"><strong>journées immersives</strong></a> pour présenter son poste, son quotidien et les missions liées à la production de contenus.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="tt-col-lg-6">
                    <div class="tt-sticker-scroller">
                        <div class="tt-image tti-border-radius tti-portrait margin-bottom-40">
                            <figure>
                                <a href="/template/assets/img/staffs-erah/shawnweak_7_11zon.jpg"
                                    class="tt-image-link" data-cursor="View" data-fancybox data-caption="ShawnWeak">
                                    <img src="/template/assets/img/staffs-erah/shawnweak_7_11zon.jpg"
                                        class="tt-anim-zoomin" loading="lazy" alt="ShawnWeak">
                                </a>
                                <figcaption>ShawnWeak</figcaption>
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Brandon -->
        <div class="tt-sticker">
            <div class="tt-row">
                <div class="tt-col-lg-6 margin-bottom-40">
                    <div class="tt-sticker-sticky tt-sticky-element">
                        <div class="tt-heading">
                            <h2 class="tt-heading-title tt-text-reveal">Brandon</h2>
                            <p class="max-width-500 tt-text-reveal">
                                <a href="#ressources" class="tt-link"><strong>Graphiste</strong></a>.  
                                Il conçoit les affiches et visuels de l’association et accompagne les stagiaires dans la création graphique et le design esportif.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="tt-col-lg-6">
                    <div class="tt-sticker-scroller">
                        <div class="tt-image tti-border-radius tti-portrait margin-bottom-40">
                            <figure>
                                <a href="/template/assets/img/staffs-erah/brandon_1_11zon.jpg"
                                    class="tt-image-link" data-cursor="View" data-fancybox data-caption="Brandon">
                                    <img src="/template/assets/img/staffs-erah/brandon_1_11zon.jpg"
                                        class="tt-anim-zoomin" loading="lazy" alt="Brandon">
                                </a>
                                <figcaption>Brandon</figcaption>
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <br><br><br><br><br><br>

        <div class="tt-heading tt-heading-lg tt-heading-center margin-bottom-140" id="ressources">
            <h2 class="tt-heading-title tt-text-reveal">Plus de profils à découvrir</h2>
            <p class="max-width-600 tt-anim-fadeinup text-muted">
                D’autres membres de l’association interviennent également, permettant aux stagiaires 
                de découvrir la diversité des métiers de l’esport : 
                <a href="#ressources" class="tt-link"><strong>communication</strong></a>, 
                <a href="#ressources" class="tt-link"><strong>management</strong></a>, 
                <a href="#ressources" class="tt-link"><strong>organisation d’événements</strong></a> et bien plus encore.
            </p>
        </div>

    </div>
</div>




                
                <div class="tt-section padding-top-xlg-20">
    <div class="tt-section-inner">

        <div class="tt-heading tt-heading-lg tt-heading-center margin-bottom-60">
            <h2 class="tt-heading-title tt-text-reveal">Stages</h2> 
            <p class="max-width-400 tt-anim-fadeinup text-muted">Découvrez nos stages immersifs au cœur de l’esport.</p>
        </div>
        
        <div class="tt-clipper">
            <a href="https://www.youtube.com/watch?v=6nGs9iGrpok" class="tt-clipper-inner" data-cursor="Play<br>Reel" data-fancybox data-caption="Découvrez les stages proposés par ERAH Esport">

                <div class="tt-clipper-bg">
                    <video loop muted autoplay playsinline preload="metadata" poster="https://youtu.be/MCh7wI7gMOU?si=JrfekOTsBj2aPjfj">
                        <source src="/template/assets/vids/Presentation ERAH.mp4" data-src="/template/assets/vids/Presentation ERAH.mp4" type="video/mp4">
                        <source src="/template/assets/vids/Presentation ERAH.webm" data-src="/template/assets/vids/Presentation ERAH.webm" type="video/webm">
                    </video>
                </div>

                <div class="tt-clipper-content">

                    <div class="tt-clipper-btn">
                        <i class="fa-solid fa-play"></i>
                    </div>

                </div>
            </a> 
        </div>

    </div> 
</div>










                <div class="tt-section no-padding-bottom">
                    <div class="tt-section-inner">


                        <div class="tt-heading tt-heading-xxlg tt-heading-center">
                            <h3 class="tt-heading-subtitle tt-text-reveal">Collaborations</h3>
                            <h2 class="tt-heading-title tt-text-reveal">Écoles & Institutions qui nous font confiance
                            </h2>
                        </div>


                    </div>
                </div>

                <div class="tt-section">
                    <div class="tt-section-inner tt-wrap">

                        <ul class="tt-logo-wall tt-anim-fadeinup logos-center">
                            <li>
                                <a href="https://gamingcampus.fr/" class="tt-logo-wall-item cursor-alter"
                                    target="_blank" rel="noopener">
                                    <div class="tt-lv-item-inner">
                                        <img src="/template/assets/img/clients/client-2-light.png" class="tt-lv-img-light"
                                            loading="lazy" alt="Gaming Campus">
                                        <img src="/template/assets/img/clients/client-2-dark.png" class="tt-lv-img-dark"
                                            loading="lazy" alt="Gaming Campus">
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="https://www.eni-ecole.fr/" class="tt-logo-wall-item cursor-alter"
                                    target="_blank" rel="noopener">
                                    <div class="tt-lv-item-inner">
                                        <img src="/template/assets/img/clients/client-3-light.png" class="tt-lv-img-light"
                                            loading="lazy" alt="ENI École">
                                        <img src="/template/assets/img/clients/client-3-dark.png" class="tt-lv-img-dark"
                                            loading="lazy" alt="ENI École">
                                    </div>
                                </a>
                            </li>
                            <li>
                                <a href="https://www.formationsuniversitaires.fr/" class="tt-logo-wall-item cursor-alter">
                                    <div class="tt-lv-item-inner">
                                        <img src="/template/assets/img/clients/client-8-light.webp" class="tt-lv-img-light"
                                            loading="lazy" alt="Contact">
                                        <img src="/template/assets/img/clients/client-8-dark.webp" class="tt-lv-img-dark"
                                            loading="lazy" alt="Contact">
                                    </div>
                                </a>
                            </li>
                        </ul>

                    </div>
                </div>



                <div class="tt-section padding-top-xlg-180 padding-bottom-xlg-120">
    <div class="tt-section-inner tt-wrap">

        <div class="tt-row margin-bottom-40">
            <div class="tt-col-xl-8">

                <div class="tt-heading tt-heading-xxxlg no-margin">
                    <h3 class="tt-heading-subtitle tt-text-reveal">Stages</h3>
                    <h2 class="tt-heading-title tt-text-reveal">Rejoignez<br> ERAH Esport</h2>
                </div>

            </div>

            <div class="tt-col-xl-4 tt-align-self-end tt-xl-column-reverse margin-top-40">

                <div
    class="max-width-600 margin-bottom-10 tt-text-uppercase text-pretty tt-text-reveal">
    Rejoignez nos <strong>stages</strong> et vivez une expérience concrète dans l’esport !
</div>


                <div class="tt-big-round-ptn margin-top-30 margin-bottom-xlg-80 tt-anim-fadeinup">
                    <a href="/contact" class="tt-big-round-ptn-holder tt-magnetic-item">
                        <div class="tt-big-round-ptn-inner">Postuler<br> Maintenant</div>
                    </a>
                </div>

            </div>
        </div>

    </div>
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
