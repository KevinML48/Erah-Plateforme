@extends('marketing.layouts.template')

@section('title', 'FAQ Plateforme | ERAH Esport')
@section('meta_description', 'FAQ et tutoriels ERAH pour comprendre le fonctionnement de la plateforme: compte, missions, duels, clips, classement, paris et cadeaux.')

@section('content')
    <div id="tt-page-content">
        <div class="tt-section padding-top-xlg-120 padding-bottom-xlg-80 border-bottom">
            <div class="tt-section-inner tt-wrap">
                <div class="tt-heading tt-heading-xxlg">
                    <h3 class="tt-heading-subtitle tt-text-uppercase">FAQ</h3>
                    <h2 class="tt-heading-title">Comment fonctionne la plateforme ERAH</h2>
                </div>
                <p class="max-width-900">
                    Cette page regroupe les informations essentielles pour demarrer rapidement sur la plateforme.
                    Chaque section suit un format tutoriel simple: <strong>objectif</strong>, <strong>comment faire</strong>,
                    et <strong>resultat attendu</strong>.
                </p>
            </div>
        </div>

        <div class="tt-section padding-top-80 padding-bottom-xlg-120">
            <div class="tt-section-inner tt-wrap">
                <div class="tt-row tt-lg-row-reverse">
                    <div class="tt-col-lg-8">
                        <div class="tt-accordion tt-accordion-style-2 tt-anim-fadeinup">
                            <div class="tt-accordion-item">
                                <div class="tt-accordion-heading">
                                    <div class="tt-ac-head-title">1) Creer un compte et se connecter</div>
                                    <div class="tt-accordion-caret-wrap"><div class="tt-accordion-caret"></div></div>
                                </div>
                                <div class="tt-accordion-content max-width-900">
                                    <p><strong>Objectif :</strong> acceder a votre espace joueur.</p>
                                    <p><strong>Etapes :</strong> cliquez sur Connexion, utilisez email/mot de passe ou Google/Discord, puis validez.</p>
                                    <p><strong>Resultat :</strong> vous arrivez sur /app avec votre profil et vos donnees.</p>
                                </div>
                            </div>

                            <div class="tt-accordion-item">
                                <div class="tt-accordion-heading">
                                    <div class="tt-ac-head-title">2) Gagner des points (XP et classement)</div>
                                    <div class="tt-accordion-caret-wrap"><div class="tt-accordion-caret"></div></div>
                                </div>
                                <div class="tt-accordion-content max-width-900">
                                    <p><strong>Objectif :</strong> progresser dans les ligues.</p>
                                    <p><strong>Etapes :</strong> participez aux missions, duels, interactions clips et paris valides.</p>
                                    <p><strong>Resultat :</strong> vos rank points augmentent et vous montez automatiquement de ligue selon les seuils.</p>
                                </div>
                            </div>

                            <div class="tt-accordion-item">
                                <div class="tt-accordion-heading">
                                    <div class="tt-ac-head-title">3) Utiliser les clips</div>
                                    <div class="tt-accordion-caret-wrap"><div class="tt-accordion-caret"></div></div>
                                </div>
                                <div class="tt-accordion-content max-width-900">
                                    <p><strong>Objectif :</strong> interagir avec la communaute.</p>
                                    <p><strong>Etapes :</strong> ouvrez un clip, likez, ajoutez en favoris, commentez et partagez.</p>
                                    <p><strong>Resultat :</strong> vos interactions sont prises en compte dans les activites de plateforme.</p>
                                </div>
                            </div>

                            <div class="tt-accordion-item">
                                <div class="tt-accordion-heading">
                                    <div class="tt-ac-head-title">4) Matchs et paris</div>
                                    <div class="tt-accordion-caret-wrap"><div class="tt-accordion-caret"></div></div>
                                </div>
                                <div class="tt-accordion-content max-width-900">
                                    <p><strong>Objectif :</strong> parier avant le verrouillage d&apos;un match.</p>
                                    <p><strong>Etapes :</strong> allez sur Matchs, ouvrez un match, choisissez une option et validez votre mise.</p>
                                    <p><strong>Resultat :</strong> votre pari apparait dans Mes paris; le reglement met a jour wallet + bonus eventuels.</p>
                                </div>
                            </div>

                            <div class="tt-accordion-item">
                                <div class="tt-accordion-heading">
                                    <div class="tt-ac-head-title">5) Missions et cadeaux</div>
                                    <div class="tt-accordion-caret-wrap"><div class="tt-accordion-caret"></div></div>
                                </div>
                                <div class="tt-accordion-content max-width-900">
                                    <p><strong>Objectif :</strong> debloquer des recompenses.</p>
                                    <p><strong>Etapes :</strong> suivez les missions du jour/semaine, completez les objectifs puis consultez le store cadeaux.</p>
                                    <p><strong>Resultat :</strong> vous recevez des reward points et pouvez demander des redemptions.</p>
                                </div>
                            </div>

                            <div class="tt-accordion-item">
                                <div class="tt-accordion-heading">
                                    <div class="tt-ac-head-title">6) Duels et notifications</div>
                                    <div class="tt-accordion-caret-wrap"><div class="tt-accordion-caret"></div></div>
                                </div>
                                <div class="tt-accordion-content max-width-900">
                                    <p><strong>Objectif :</strong> gerer vos invitations et rester informe.</p>
                                    <p><strong>Etapes :</strong> creez ou acceptez des duels et configurez vos preferences de notifications.</p>
                                    <p><strong>Resultat :</strong> vous suivez vos activites en temps reel depuis la page Notifications.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tt-col-lg-4">
                        <div class="tt-box tt-anim-fadeinup">
                            <h4 class="margin-bottom-15">Acces rapide</h4>
                            <ul class="tt-list">
                                <li><a class="tt-link" href="/app">Ouvrir la plateforme</a></li>
                                <li><a class="tt-link" href="/app/classement">Classement</a></li>
                                <li><a class="tt-link" href="/app/clips">Clips</a></li>
                                <li><a class="tt-link" href="/app/matchs">Matchs</a></li>
                                <li><a class="tt-link" href="/contact">Contact support</a></li>
                            </ul>
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
