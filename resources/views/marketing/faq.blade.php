@extends('marketing.layouts.template')

@section('title', 'FAQ Plateforme | ERAH Esport')
@section('meta_description', 'FAQ complete ERAH: compte, points, classement, paris, missions, cadeaux, duels et notifications.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('content')
    <div id="page-header" class="ph-full ph-full-m ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">Need Help?</h2>
                    <h1 class="ph-caption-title">FAQ Platform</h1>
                    <div class="ph-caption-description max-width-700">
                        Tutos complets pour comprendre le fonctionnement de la plateforme ERAH.
                    </div>
                </div>
            </div>
        </div>

        <div class="page-header-inner ph-mask">
            <div class="ph-mask-inner tt-wrap">
                <div class="ph-caption">
                    <div class="ph-caption-inner">
                        <h2 class="ph-caption-subtitle">ERAH Guide</h2>
                        <h1 class="ph-caption-title">FAQ Complete</h1>
                        <div class="ph-caption-description max-width-700">
                            Compte, points, missions, cadeaux, paris, duels et notifications.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="ph-social">
            <ul>
                <li><a href="https://www.twitch.tv/erah_association" class="tt-magnetic-item" target="_blank" rel="noopener"><i class="fa-brands fa-twitch"></i></a></li>
                <li><a href="https://www.instagram.com/erahesport/" class="tt-magnetic-item" target="_blank" rel="noopener"><i class="fa-brands fa-instagram"></i></a></li>
                <li><a href="https://x.com/ErahEsport" class="tt-magnetic-item" target="_blank" rel="noopener"><i class="fa-brands fa-x-twitter"></i></a></li>
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
                        <textPath xlink:href="#textcircle">Scroll to Explore - Scroll to Explore -</textPath>
                    </text>
                </svg>
            </a>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-lg-80 padding-bottom-lg-120 padding-bottom-80 border-top">
            <div class="tt-section-inner tt-wrap">
                <div class="tt-heading tt-heading-lg tt-heading-center margin-bottom-120">
                    <h3 class="tt-heading-subtitle tt-text-uppercase">Quick Start</h3>
                    <h2 class="tt-heading-title tt-text-reveal">3 etapes pour commencer</h2>
                    <p class="max-width-700 tt-anim-fadeinup text-muted">
                        Demarrez vite avec ces 3 actions essentielles pour activer votre progression ERAH.
                    </p>
                </div>

                <div class="tt-sticky-testimonials tt-stte-reversed-colors tt-stte-center">
                    <div class="tt-stte-item">
                        <div class="tt-stte-card cursor-alter">
                            <div class="tt-stte-card-counter"></div>
                            <div class="tt-stte-card-caption">
                                <div class="tt-stte-text">
                                    Creer votre compte. Connectez-vous avec email/mot de passe ou Google/Discord,
                                    puis ouvrez la plateforme sur /app.
                                </div>
                                <div class="tt-stte-subtext">- Etape 1</div>
                            </div>
                        </div>
                    </div>

                    <div class="tt-stte-item">
                        <div class="tt-stte-card cursor-alter">
                            <div class="tt-stte-card-counter"></div>
                            <div class="tt-stte-card-caption">
                                <div class="tt-stte-text">
                                    Lancer vos activites. Faites des missions, interagissez sur les clips
                                    et participez aux duels/matchs pour progresser.
                                </div>
                                <div class="tt-stte-subtext">- Etape 2</div>
                            </div>
                        </div>
                    </div>

                    <div class="tt-stte-item">
                        <div class="tt-stte-card cursor-alter">
                            <div class="tt-stte-card-counter"></div>
                            <div class="tt-stte-card-caption">
                                <div class="tt-stte-text">
                                    Recuperer vos rewards. Cumulez vos points, montez en ligue
                                    et echangez vos reward points dans le store cadeaux.
                                </div>
                                <div class="tt-stte-subtext">- Etape 3</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tt-section padding-top-lg-60 padding-top-80 padding-bottom-lg-40 border-top">
            <div class="tt-section-inner tt-wrap max-width-1600">
                <div class="tt-row">
                    <div class="tt-col-lg-5">
                        <div class="tt-heading tt-heading-xlg">
                            <h2 class="tt-heading-title tt-text-reveal">Compte et<br>Securite</h2>
                        </div>
                        <div class="text-muted tt-anim-fadeinup">
                            <p>Connexion stable et securisee.</p>
                        </div>
                    </div>
                    <div class="tt-col-lg-1 padding-top-30"></div>
                    <div class="tt-col-lg-6 tt-align-self-center tt-anim-fadeinup">
                        <p class="text-lg">
                            La plateforme prend en charge la connexion locale et le social login.
                            Une fois connecte, vous retrouvez vos donnees et vos raccourcis menu.
                        </p>
                        <p>
                            Pensez a verifier votre profil, vos preferences de notification et vos canaux actifs.
                            Si une action est protegee, une redirection vers la connexion est appliquee automatiquement.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="tt-section padding-top-lg-60 padding-top-80 padding-bottom-lg-40 border-top">
            <div class="tt-section-inner tt-wrap max-width-1600">
                <div class="tt-row">
                    <div class="tt-col-lg-5">
                        <div class="tt-heading tt-heading-xlg">
                            <h2 class="tt-heading-title tt-text-reveal">Points et<br>Classement</h2>
                        </div>
                        <div class="text-muted tt-anim-fadeinup">
                            <p>XP, rank points et progression ligue.</p>
                        </div>
                    </div>
                    <div class="tt-col-lg-1 padding-top-30"></div>
                    <div class="tt-col-lg-6 tt-align-self-center tt-anim-fadeinup">
                        <p class="text-lg">
                            Les actions valides credites vos points via des transactions controlees et auditables.
                            Votre ligue evolue automatiquement selon les seuils.
                        </p>
                        <p>
                            Vous pouvez consulter le classement global, votre position et votre historique d'activites
                            directement sur les pages classement, profil et dashboard.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="tt-section padding-top-lg-60 padding-top-80 padding-bottom-lg-40 border-top">
            <div class="tt-section-inner tt-wrap max-width-1600">
                <div class="tt-row">
                    <div class="tt-col-lg-5">
                        <div class="tt-heading tt-heading-xlg">
                            <h2 class="tt-heading-title tt-text-reveal">Paris et<br>Wallets</h2>
                        </div>
                        <div class="text-muted tt-anim-fadeinup">
                            <p>Mises, verrouillage et settlement.</p>
                        </div>
                    </div>
                    <div class="tt-col-lg-1 padding-top-30"></div>
                    <div class="tt-col-lg-6 tt-align-self-center tt-anim-fadeinup">
                        <p class="text-lg">
                            Les paris sont autorises uniquement avant le lock du match.
                            Le systeme regle ensuite les bets de maniere idempotente.
                        </p>
                        <p>
                            Votre wallet bet points est debite/credite automatiquement selon le resultat.
                            Les transactions restent visibles pour verifier chaque mouvement.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="tt-section padding-top-lg-60 padding-top-80 padding-bottom-lg-40 border-top">
            <div class="tt-section-inner tt-wrap max-width-1600">
                <div class="tt-row">
                    <div class="tt-col-lg-5">
                        <div class="tt-heading tt-heading-xlg">
                            <h2 class="tt-heading-title tt-text-reveal">Missions et<br>Cadeaux</h2>
                        </div>
                        <div class="text-muted tt-anim-fadeinup">
                            <p>Reward points et redemptions.</p>
                        </div>
                    </div>
                    <div class="tt-col-lg-1 padding-top-30"></div>
                    <div class="tt-col-lg-6 tt-align-self-center tt-anim-fadeinup">
                        <p class="text-lg">
                            Les missions daily/weekly alimentent votre progression et votre wallet reward points.
                            Ces points servent exclusivement au store cadeaux.
                        </p>
                        <p>
                            Quand vous lancez une redemption, le stock est reserve et les points sont debites.
                            En cas de rejet admin, le remboursement est applique automatiquement.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="tt-section padding-top-lg-60 padding-top-80 padding-bottom-lg-40 border-top">
            <div class="tt-section-inner tt-wrap max-width-1600">
                <div class="tt-row">
                    <div class="tt-col-lg-5">
                        <div class="tt-heading tt-heading-xlg">
                            <h2 class="tt-heading-title tt-text-reveal">Duels et<br>Notifications</h2>
                        </div>
                        <div class="text-muted tt-anim-fadeinup">
                            <p>Flux social et alertes en temps reel.</p>
                        </div>
                    </div>
                    <div class="tt-col-lg-1 padding-top-30"></div>
                    <div class="tt-col-lg-6 tt-align-self-center tt-anim-fadeinup">
                        <p class="text-lg">
                            Vous pouvez creer, accepter ou refuser des duels selon votre statut et les delais de reponse.
                            Les notifications in-app sont toujours persistees.
                        </p>
                        <p>
                            Selon vos preferences, des canaux supplementaires peuvent etre actives.
                            Gardez vos reglages a jour pour ne manquer aucune action importante.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="tt-section padding-top-lg-80 padding-bottom-lg-120 padding-bottom-80 border-top">
            <div class="tt-section-inner tt-wrap max-width-1600">
                <div class="tt-row">
                    <div class="tt-col-lg-3">
                        <div class="tt-heading tt-heading-lg">
                            <h2 class="tt-heading-title tt-text-reveal">FAQ<br>Detaillee</h2>
                        </div>
                        <p class="text-muted">
                            Questions frequentes sur les mecaniques de la plateforme ERAH.
                        </p>
                    </div>

                    <div class="tt-col-lg-1 padding-top-30"></div>

                    <div class="tt-col-lg-8 tt-align-self-center">
                        <div class="tt-accordion tt-ac-sm tt-ac-borders tt-ac-counter">
                            <div class="tt-accordion-item tt-anim-fadeinup">
                                <div class="tt-accordion-heading">
                                    <div class="tt-ac-head cursor-alter"><div class="tt-ac-head-inner"><h4 class="tt-ac-head-title">Comment acceder rapidement aux pages principales ?</h4></div></div>
                                    <div class="tt-accordion-caret"><div class="tt-accordion-caret-inner tt-magnetic-item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path></svg></div></div>
                                </div>
                                <div class="tt-accordion-content max-width-900"><p>Utilisez le menu principal et le menu deroulant Plateforme pour ouvrir rapidement classement, clips, missions, duels, cadeaux et profil.</p></div>
                            </div>

                            <div class="tt-accordion-item tt-anim-fadeinup">
                                <div class="tt-accordion-heading">
                                    <div class="tt-ac-head cursor-alter"><div class="tt-ac-head-inner"><h4 class="tt-ac-head-title">Pourquoi certaines actions demandent une connexion ?</h4></div></div>
                                    <div class="tt-accordion-caret"><div class="tt-accordion-caret-inner tt-magnetic-item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path></svg></div></div>
                                </div>
                                <div class="tt-accordion-content max-width-900"><p>Les pages en lecture peuvent etre publiques, mais les actions sensibles (parier, commenter, redemption, duels) exigent un compte pour securiser les donnees.</p></div>
                            </div>

                            <div class="tt-accordion-item tt-anim-fadeinup">
                                <div class="tt-accordion-heading">
                                    <div class="tt-ac-head cursor-alter"><div class="tt-ac-head-inner"><h4 class="tt-ac-head-title">Comment monter de ligue plus vite ?</h4></div></div>
                                    <div class="tt-accordion-caret"><div class="tt-accordion-caret-inner tt-magnetic-item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path></svg></div></div>
                                </div>
                                <div class="tt-accordion-content max-width-900"><p>Concentrez-vous sur les missions avec meilleur rendement, maintenez une activite reguliere sur clips et duels, et suivez vos objectifs hebdo.</p></div>
                            </div>

                            <div class="tt-accordion-item tt-anim-fadeinup">
                                <div class="tt-accordion-heading">
                                    <div class="tt-ac-head cursor-alter"><div class="tt-ac-head-inner"><h4 class="tt-ac-head-title">Quand un pari ne peut plus etre annule ?</h4></div></div>
                                    <div class="tt-accordion-caret"><div class="tt-accordion-caret-inner tt-magnetic-item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path></svg></div></div>
                                </div>
                                <div class="tt-accordion-content max-width-900"><p>Apres la fenetre autorisee et surtout une fois le match verrouille, l'annulation n'est plus possible. Verifiez toujours le lock time avant validation.</p></div>
                            </div>

                            <div class="tt-accordion-item tt-anim-fadeinup">
                                <div class="tt-accordion-heading">
                                    <div class="tt-ac-head cursor-alter"><div class="tt-ac-head-inner"><h4 class="tt-ac-head-title">Comment sont calcules les gains des bets ?</h4></div></div>
                                    <div class="tt-accordion-caret"><div class="tt-accordion-caret-inner tt-magnetic-item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path></svg></div></div>
                                </div>
                                <div class="tt-accordion-content max-width-900"><p>Le payout est base sur la mise et le snapshot d'odds du pari. Le settlement applique ensuite credit/debit wallet de facon transactionnelle et sans double execution.</p></div>
                            </div>

                            <div class="tt-accordion-item tt-anim-fadeinup">
                                <div class="tt-accordion-heading">
                                    <div class="tt-ac-head cursor-alter"><div class="tt-ac-head-inner"><h4 class="tt-ac-head-title">A quoi servent les reward points exactement ?</h4></div></div>
                                    <div class="tt-accordion-caret"><div class="tt-accordion-caret-inner tt-magnetic-item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path></svg></div></div>
                                </div>
                                <div class="tt-accordion-content max-width-900"><p>Les reward points servent au store cadeaux uniquement. Ils ne remplacent ni les bet points, ni les rank points du classement.</p></div>
                            </div>

                            <div class="tt-accordion-item tt-anim-fadeinup">
                                <div class="tt-accordion-heading">
                                    <div class="tt-ac-head cursor-alter"><div class="tt-ac-head-inner"><h4 class="tt-ac-head-title">Comment suivre une demande de cadeau ?</h4></div></div>
                                    <div class="tt-accordion-caret"><div class="tt-accordion-caret-inner tt-magnetic-item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path></svg></div></div>
                                </div>
                                <div class="tt-accordion-content max-width-900"><p>Chaque redemption affiche son statut: pending, approved, shipped ou delivered. En cas de rejet, le remboursement est visible dans votre wallet reward.</p></div>
                            </div>

                            <div class="tt-accordion-item tt-anim-fadeinup">
                                <div class="tt-accordion-heading">
                                    <div class="tt-ac-head cursor-alter"><div class="tt-ac-head-inner"><h4 class="tt-ac-head-title">Comment activer des notifications utiles sans spam ?</h4></div></div>
                                    <div class="tt-accordion-caret"><div class="tt-accordion-caret-inner tt-magnetic-item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path></svg></div></div>
                                </div>
                                <div class="tt-accordion-content max-width-900"><p>Gardez les notifications in-app, puis activez email/push uniquement pour les categories critiques: duels, paris, missions et systeme.</p></div>
                            </div>

                            <div class="tt-accordion-item tt-anim-fadeinup">
                                <div class="tt-accordion-heading">
                                    <div class="tt-ac-head cursor-alter"><div class="tt-ac-head-inner"><h4 class="tt-ac-head-title">Pourquoi un duel expire parfois sans reponse ?</h4></div></div>
                                    <div class="tt-accordion-caret"><div class="tt-accordion-caret-inner tt-magnetic-item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path></svg></div></div>
                                </div>
                                <div class="tt-accordion-content max-width-900"><p>Chaque duel pending a une fenetre de validite. Sans acceptation dans le delai, il passe automatiquement en expire pour garder un flux propre.</p></div>
                            </div>

                            <div class="tt-accordion-item tt-anim-fadeinup">
                                <div class="tt-accordion-heading">
                                    <div class="tt-ac-head cursor-alter"><div class="tt-ac-head-inner"><h4 class="tt-ac-head-title">Ou trouver une aide humaine si je bloque ?</h4></div></div>
                                    <div class="tt-accordion-caret"><div class="tt-accordion-caret-inner tt-magnetic-item"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path></svg></div></div>
                                </div>
                                <div class="tt-accordion-content max-width-900"><p>Utilisez la page contact et decrivez votre probleme (compte, wallet, reward, mission, duel ou clips). L'equipe ERAH vous repondra rapidement.</p></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tt-section padding-top-xlg-120 padding-bottom-xlg-120 border-top">
            <div class="tt-section-inner tt-wrap">
                <div class="tt-row margin-bottom-40">
                    <div class="tt-col-xl-8">
                        <div class="tt-heading tt-heading-xxxlg no-margin">
                            <h3 class="tt-heading-subtitle tt-text-reveal">Matchs & Paris</h3>
                            <h2 class="tt-heading-title tt-text-reveal">Pret a lancer<br>votre premier pari ?</h2>
                        </div>
                    </div>

                    <div class="tt-col-xl-4 tt-align-self-end tt-xl-column-reverse margin-top-40">
                        <div class="tt-big-round-ptn margin-top-30 margin-bottom-xlg-80 tt-anim-fadeinup">
                            <a href="{{ route('app.matches.index') }}" class="tt-big-round-ptn-holder tt-magnetic-item">
                                <div class="tt-big-round-ptn-inner">Parier<br>Maintenant</div>
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
@endsection
