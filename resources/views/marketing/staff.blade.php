@extends('marketing.layouts.template')


@section('title', 'Staff ERAH | ERAH Esport')

@section('meta_description', 'Découvrez le staff ERAH Esport : une équipe passionnée dédiée à l’esport, l’organisation d’événements et le développement de la scène gaming en Lozère et au-delà.')

@section('meta_keywords', 'Staff ERAH, équipe ERAH Esport, dirigeants ERAH, bénévoles esport, organisation esport, association gaming Lozère, esport Occitanie, esport Mende')

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

<div id="page-header"
				class="ph-full ph-full-m ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">

				<div class="page-header-inner tt-wrap">

					<div class="ph-caption">
						<div class="ph-caption-inner">
							<h2 class="ph-caption-subtitle">Notre équipe</h2>
							<h1 class="ph-caption-title">ERAH Staff</h1>
							<div class="ph-caption-description max-width-700">
								Découvrez l’équipe ERAH Esport, un collectif passionné et engagé pour soutenir nos
								joueurs, nos projets et notre communauté.
							</div>
						</div>
					</div>

				</div>

				<div class="page-header-inner ph-mask">
					<div class="ph-mask-inner tt-wrap">

						<div class="ph-caption">
							<div class="ph-caption-inner">
								<h2 class="ph-caption-subtitle">Esprit d’équipe</h2>
								<h1 class="ph-caption-title">Nos valeurs</h1>
								<div class="ph-caption-description max-width-700">
									Collaboration, passion et engagement : chaque membre de l’équipe ERAH contribue à
									faire grandir notre structure et notre communauté.
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
								<textPath xlink:href="#textcircle">Explorez Défiez Brillez Soutenez ERAH -</textPath>
							</text>
						</svg>
					</a>
				</div>


			</div>

			<div id="tt-page-content">

				<div class="tt-section">
					<div class="tt-section-inner">

						<div class="tt-portfolio-preview-list tt-ppli-portrait tt-ppli-hover">
							<div class="tt-ppl-items-list">

								<a href="https://x.com/YusohFR" class="tt-ppl-item" target="_blank" rel="noopener">
									<div class="tt-ppli-preview">
										<div class="tt-ppli-preview-image">

											<img src="/template/assets/img/galerie/chirac-conference-3.jpg" alt="Image">
										</div>
									</div>

									<div class="tt-ppl-item-inner">
										<div class="tt-ppl-item-holder">
											<div class="tt-ppli-col tt-ppli-col-count">
												<div class="tt-ppli-count"></div>
											</div>
											<div class="tt-ppli-col tt-ppli-col-caption">
												<div class="tt-ppli-caption">
													<h2 class="tt-ppli-title">Yusoh</h2>
													<div class="tt-ppli-categories">
														<div class="tt-ppli-category">Président</div>

													</div>
												</div>
											</div>
											<div class="tt-ppli-col tt-ppli-col-info tt-justify-content-md-end">
												<div class="tt-ppli-info">
													Fondateur
												</div>
											</div>
										</div>
									</div>
								</a>

								<a href="https://x.com/_SunC_e_" class="tt-ppl-item" target="_blank" rel="noopener">
									<div class="tt-ppli-preview">
										<div class="tt-ppli-preview-image">

											<img src="/template/assets/img/staffs-erah/sunce_8_11zon.jpg" alt="Image">
										</div>
									</div>

									<div class="tt-ppl-item-inner">
										<div class="tt-ppl-item-holder">
											<div class="tt-ppli-col tt-ppli-col-count">
												<div class="tt-ppli-count"></div>
											</div>
											<div class="tt-ppli-col tt-ppli-col-caption">
												<div class="tt-ppli-caption">
													<h2 class="tt-ppli-title">SunCe</h2>
													<div class="tt-ppli-categories">
														<div class="tt-ppli-category">CO - Président</div>

													</div>
												</div>
											</div>
											<div class="tt-ppli-col tt-ppli-col-info tt-justify-content-md-end">
												<div class="tt-ppli-info">
													CO - Fondateur
												</div>
											</div>
										</div>
									</div>
								</a>

								<a href="https://x.com/ERAH_Oxwig" class="tt-ppl-item" target="_blank" rel="noopener">
									<div class="tt-ppli-preview">
										<div class="tt-ppli-preview-image">

											<img src="/template/assets/img/staffs-erah/oxwig_3_11zon.jpg" alt="Image">
										</div>
									</div>

									<div class="tt-ppl-item-inner">
										<div class="tt-ppl-item-holder">
											<div class="tt-ppli-col tt-ppli-col-count">
												<div class="tt-ppli-count"></div>
											</div>
											<div class="tt-ppli-col tt-ppli-col-caption">
												<div class="tt-ppli-caption">
													<h2 class="tt-ppli-title">Oxwig</h2>
													<div class="tt-ppli-categories">
														<div class="tt-ppli-category">CO - CEO</div>

													</div>
												</div>
											</div>
											<div class="tt-ppli-col tt-ppli-col-info tt-justify-content-md-end">
												<div class="tt-ppli-info">
													CO - CEO
												</div>
											</div>
										</div>
									</div>
								</a>

								<a href="https://x.com/LuT1_" class="tt-ppl-item" target="_blank" rel="noopener">
									<div class="tt-ppli-preview">
										<div class="tt-ppli-preview-image">
											<img src="/template/assets/img/staffs-erah/lut1.jpg" alt="Image">
										</div>
									</div>

									<div class="tt-ppl-item-inner">
										<div class="tt-ppl-item-holder">
											<div class="tt-ppli-col tt-ppli-col-count">
												<div class="tt-ppli-count"></div>
											</div>
											<div class="tt-ppli-col tt-ppli-col-caption">
												<div class="tt-ppli-caption">
													<h2 class="tt-ppli-title">Lut1</h2>
													<div class="tt-ppli-categories">
														<div class="tt-ppli-category">Directeur Esport</div>
													</div>
												</div>
											</div>
											<div class="tt-ppli-col tt-ppli-col-info tt-justify-content-md-end">
												<div class="tt-ppli-info">Head of Esport</div>
											</div>
										</div>
									</div>
								</a>

								<a href="https://x.com/R1p3rl3" class="tt-ppl-item" target="_blank" rel="noopener">
									<div class="tt-ppli-preview">
										<div class="tt-ppli-preview-image">
											<img src="/template/assets/img/staffs-erah/reperle_6_11zon.jpg" alt="Image">
										</div>
									</div>

									<div class="tt-ppl-item-inner">
										<div class="tt-ppl-item-holder">
											<div class="tt-ppli-col tt-ppli-col-count">
												<div class="tt-ppli-count"></div>
											</div>
											<div class="tt-ppli-col tt-ppli-col-caption">
												<div class="tt-ppli-caption">
													<h2 class="tt-ppli-title">Riperle</h2>
													<div class="tt-ppli-categories">
														<div class="tt-ppli-category">Commercial</div>
													</div>
												</div>
											</div>
											<div class="tt-ppli-col tt-ppli-col-info tt-justify-content-md-end">
												<div class="tt-ppli-info">Chargé commercial</div>
											</div>
										</div>
									</div>
								</a>

								<a href="https://x.com/brandonn_yu" class="tt-ppl-item" target="_blank" rel="noopener">
									<div class="tt-ppli-preview">
										<div class="tt-ppli-preview-image">
											<img src="/template/assets/img/staffs-erah/brandon_1_11zon.jpg" alt="Image">
										</div>
									</div>

									<div class="tt-ppl-item-inner">
										<div class="tt-ppl-item-holder">
											<div class="tt-ppli-col tt-ppli-col-count">
												<div class="tt-ppli-count"></div>
											</div>
											<div class="tt-ppli-col tt-ppli-col-caption">
												<div class="tt-ppli-caption">
													<h2 class="tt-ppli-title">Brandon</h2>
													<div class="tt-ppli-categories">
														<div class="tt-ppli-category">Graphiste</div>
													</div>
												</div>
											</div>
											<div class="tt-ppli-col tt-ppli-col-info tt-justify-content-md-end">
												<div class="tt-ppli-info">Créatif</div>
											</div>
										</div>
									</div>
								</a>

								<a href="https://x.com/Shawnzyfrenchy" class="tt-ppl-item" target="_blank"
									rel="noopener">
									<div class="tt-ppli-preview">
										<div class="tt-ppli-preview-image">
											<img src="/template/assets/img/staffs-erah/shawnweak_7_11zon.jpg" alt="Image">
										</div>
									</div>

									<div class="tt-ppl-item-inner">
										<div class="tt-ppl-item-holder">
											<div class="tt-ppli-col tt-ppli-col-count">
												<div class="tt-ppli-count"></div>
											</div>
											<div class="tt-ppli-col tt-ppli-col-caption">
												<div class="tt-ppli-caption">
													<h2 class="tt-ppli-title">Shawnweak</h2>
													<div class="tt-ppli-categories">
														<div class="tt-ppli-category">Monteur vidéo</div>
													</div>
												</div>
											</div>
											<div class="tt-ppli-col tt-ppli-col-info tt-justify-content-md-end">
												<div class="tt-ppli-info">Vidéo Editor</div>
											</div>
										</div>
									</div>
								</a>




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
									<h2 class="tt-heading-title tt-text-reveal">Rejoins<br> l'Aventure</h2>
								</div>

							</div>

							<div class="tt-col-xl-4 tt-align-self-end tt-xl-column-reverse margin-top-40">

								<div class="max-width-600 margin-bottom-10 tt-text-uppercase tt-text-reveal">
									Tu veux faire partie de notre équipe ?<br>
									Envoie ta candidature et montre-nous ta motivation !
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
