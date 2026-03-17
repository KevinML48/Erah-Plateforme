@extends('marketing.layouts.template')


@section('title', 'Annonce Live 18/03 | ERAH Esport')

@section('meta_description', 'Rendez-vous le 18/03 en live avec ERAH Esport pour une annonce officielle et la présentation du roster.')

@section('meta_keywords', 'live 18/03, ERAH Esport, annonce officielle, roster, gaming Lozère, sport électronique')

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

.live-announcement-grid {
	display: grid;
	grid-template-columns: repeat(2, minmax(0, 1fr));
	gap: 28px;
}

.live-announcement-card {
	padding: 36px;
	border: 1px solid rgba(255,255,255,0.12);
	border-radius: 24px;
	background: linear-gradient(180deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);
}

.live-announcement-card h3 {
	margin-bottom: 18px;
}

.live-announcement-card p,
.live-announcement-card li {
	font-size: 17px;
	line-height: 1.8;
	color: rgba(255,255,255,0.82);
}

.live-announcement-list {
	margin: 0;
	padding-left: 18px;
}

.live-announcement-actions {
	display: flex;
	flex-wrap: wrap;
	gap: 16px;
	margin-top: 28px;
}

.live-pill {
	display: inline-flex;
	align-items: center;
	gap: 10px;
	padding: 10px 16px;
	border-radius: 999px;
	background: rgba(145, 70, 255, 0.18);
	border: 1px solid rgba(145, 70, 255, 0.35);
	color: #fff;
	font-size: 13px;
	font-weight: 700;
	letter-spacing: 0.08em;
	text-transform: uppercase;
}

.live-date {
	font-size: clamp(56px, 9vw, 108px);
	line-height: 0.95;
	font-weight: 700;
	margin: 14px 0 18px;
}

.live-meta {
	display: grid;
	grid-template-columns: repeat(3, minmax(0, 1fr));
	gap: 14px;
	margin-top: 30px;
}

.live-meta-item {
	padding: 18px 20px;
	border-radius: 18px;
	background: rgba(255,255,255,0.04);
	border: 1px solid rgba(255,255,255,0.08);
}

.live-meta-label {
	display: block;
	font-size: 12px;
	text-transform: uppercase;
	letter-spacing: 0.08em;
	color: rgba(255,255,255,0.55);
	margin-bottom: 8px;
}

.live-meta-value {
	display: block;
	font-size: 18px;
	font-weight: 600;
	color: #fff;
}

@media (max-width: 991px) {
	.live-announcement-grid,
	.live-meta {
		grid-template-columns: 1fr;
	}

	.live-announcement-card {
		padding: 28px;
	}
}
</style>

@endverbatim
@endsection


@section('content')
@verbatim

<div id="page-header"
	class="ph-full ph-full-m ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">

	<div class="page-header-inner tt-wrap">

		<div class="ph-caption">
			<div class="ph-caption-inner">
				<h2 class="ph-caption-subtitle">Annonce officielle</h2>
				<h1 class="ph-caption-title">Live roster le 18/03</h1>
				<div class="ph-caption-description max-width-700">
					Le <strong>18/03</strong>, ERAH Esport présente en <strong>live</strong> son annonce officielle.<br>
					Le roster sera révélé en direct sur notre chaîne Twitch officielle.
				</div>
			</div>
		</div>

	</div>

	<div class="page-header-inner ph-mask">
		<div class="ph-mask-inner tt-wrap">

			<div class="ph-caption">
				<div class="ph-caption-inner">
					<h2 class="ph-caption-subtitle">Reveal officiel</h2>
					<h1 class="ph-caption-title">Annonce de l’équipe</h1>
					<div class="ph-caption-description max-width-700">
						Découvrez en direct les joueurs et le staff qui représenteront <strong>ERAH Esport</strong>.<br>
						Cette annonce marque le lancement officiel de notre nouvelle communication autour du roster.
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
					<textPath xlink:href="#textcircle">Annonce live 18/03 - ERAH Esport</textPath>
				</text>
			</svg>
		</a>
	</div>

</div>



			<div id="tt-page-content">

				<div class="tt-section padding-top-xlg-120 padding-bottom-xlg-120">
					<div class="tt-section-inner tt-wrap">

						<div class="live-announcement-grid">
							<div class="live-announcement-card">
								<span class="live-pill"><i class="fa-brands fa-twitch"></i> Live officiel</span>
								<div class="live-date">18/03</div>
								<p>
									Cette page est dédiée à l’<strong>annonce</strong> d’ERAH Esport.
									Le <strong>18/03</strong>, le roster officiel sera révélé en direct avec une prise de parole autour du projet et de la saison.
								</p>

								<div class="live-meta">
									<div class="live-meta-item">
										<span class="live-meta-label">Date</span>
										<span class="live-meta-value">18 mars</span>
									</div>
									<div class="live-meta-item">
										<span class="live-meta-label">Format</span>
										<span class="live-meta-value">Reveal officiel</span>
									</div>
									<div class="live-meta-item">
										<span class="live-meta-label">Plateforme</span>
										<span class="live-meta-value">Twitch ERAH</span>
									</div>
								</div>

								<div class="live-announcement-actions">
									<a href="https://www.twitch.tv/erah_association" class="tt-btn tt-btn-secondary tt-magnetic-item" target="_blank" rel="noopener">
										Regarder le live sur Twitch
									</a>
									<a href="https://www.twitch.tv/erah_association" class="tt-btn tt-btn-outline tt-magnetic-item" target="_blank" rel="noopener">
										Suivre la chaine
									</a>
								</div>
							</div>

							<div class="live-announcement-card">
								<h3 class="tt-heading-title">Annonce</h3>
								<ul class="live-announcement-list">
									<li>Révélation officielle de l’équipe d’ERAH Esport.</li>
									<li>Présentation du projet compétitif et de l’identité du roster.</li>
									<li>Annonce en direct pensée autour de la saison à venir.</li>
								</ul>

								<h3 class="tt-heading-title margin-top-40">Diffusion Twitch</h3>
								<p>
									L’annonce sera diffusée sur la chaine Twitch officielle d’ERAH Esport :<br>
									<strong>twitch.tv/erah_association</strong>
								</p>
								<p>
									Revenez le <strong>18/03</strong> pour suivre en direct la révélation complète de cette annonce.
								</p>
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
