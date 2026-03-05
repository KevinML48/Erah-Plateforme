<footer id="tt-footer" class="border-top">
    <div class="tt-footer-inner tt-wrap">
        <div class="tt-row">
            <div class="tt-col-xl-3 tt-col-sm-6">
                <div class="tt-footer-widget">
                    <h5 class="tt-footer-widget-heading">Support</h5>
                    <ul class="tt-footer-widget-list">
                        <li><a href="https://www.vlr.gg/team/18024/erah-esport" class="tt-link" target="_blank" rel="noopener">Page VLR</a></li>
                        <li><a href="https://tracker.gg/valorant/premier/teams/f698836f-cfac-4872-bf2f-9bfaaeeefc25/matches" class="tt-link" target="_blank" rel="noopener">Ligue Invite</a></li>
                        <li><a href="https://discord.gg/9G89kkSjRx" class="tt-link" target="_blank" rel="noopener">Discord</a></li>
                        <li><a href="https://www.linkedin.com/company/erah-association/" class="tt-link" target="_blank" rel="noopener">LinkedIn</a></li>
                        <li><a class="tt-link" href="/mentions-legales">Mentions légales</a></li>
                    </ul>
                </div>
            </div>

            <div class="tt-col-xl-3 tt-col-sm-6">
                <div class="tt-footer-widget">
                    <h5 class="tt-footer-widget-heading">Sitemap</h5>
                    <ul class="tt-footer-widget-list">
                        <li><a class="tt-link" href="/about">A propos</a></li>
                        <li><a class="tt-link" href="/nos-stages">Nos stages</a></li>
                        <li><a class="tt-link" href="/mende">Mende</a></li>
                        <li><a class="tt-link" href="/boutique">Boutique</a></li>
                        @auth
                            <li><a class="tt-link" href="{{ route('app.profile') }}">Mon profil</a></li>
                        @else
                            <li><a class="tt-link" href="{{ route('login') }}">Se connecter</a></li>
                        @endauth
                        <li><a class="tt-link" href="/contact">Contact</a></li>
                        <li><a href="#" id="manage-cookies" class="tt-link">Gérer mes cookies</a></li>
                    </ul>
                </div>
            </div>

            <div class="tt-col-xl-3 tt-col-sm-6">
                <div class="tt-footer-widget">
                    <h5 class="tt-footer-widget-heading">Contact</h5>
                    <ul class="tt-footer-widget-list">
                        <li><a href="https://maps.app.goo.gl/MTiizsoAEUrp7NpZ6" class="tt-link" target="_blank" rel="nofollow noopener">Mende, 48000</a></li>
                        <li><a href="mailto:erah.association@gmail.com" class="tt-link">erah.association@gmail.com</a></li>
                        <li><a href="tel:+33649425578" class="tt-link">+(33) 06 49 42 55 78</a></li>
                        <li>
                            <div class="tt-social-buttons">
                                <ul>
                                    <li><a href="https://www.twitch.tv/erah_association" class="tt-magnetic-item" target="_blank" rel="noopener" aria-label="Twitch ERAH Esport"><i class="fa-brands fa-twitch"></i></a></li>
                                    <li><a href="https://www.instagram.com/erahesport/" class="tt-magnetic-item" target="_blank" rel="noopener" aria-label="Instagram ERAH Esport"><i class="fa-brands fa-instagram"></i></a></li>
                                    <li><a href="https://x.com/ErahEsport" class="tt-magnetic-item" target="_blank" rel="noopener" aria-label="X ERAH Esport"><i class="fa-brands fa-twitter"></i></a></li>
                                    <li><a href="https://discord.gg/9G89kkSjRx" class="tt-magnetic-item" target="_blank" rel="noopener" aria-label="Discord ERAH Esport"><i class="fa-brands fa-discord"></i></a></li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="tt-col-xl-3 tt-col-sm-6 tt-justify-content-xl-end">
                <div class="tt-footer-widget">
                    <ul class="tt-footer-widget-list">
                        <li>
                            <div class="tt-footer-logo">
                                <a class="tt-magnetic-item" href="/">
                                    <img src="/template/assets/img/logo.png" class="tt-logo-light" loading="lazy" alt="Logo">
                                    <img src="/template/assets/img/logo.png" class="tt-logo-dark" loading="lazy" alt="Logo">
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>

<a href="#" class="tt-scroll-to-top">
    <div class="tt-stt-progress tt-magnetic-item">
        <svg class="tt-stt-progress-circle" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"></path>
        </svg>
    </div>
</a>

