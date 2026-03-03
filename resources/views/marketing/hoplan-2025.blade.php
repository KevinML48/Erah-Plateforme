@extends('marketing.layouts.template')


@section('title', 'HopLan 2025 | ERAH Esport')

@section('meta_description', 'HopLan 2025 ERAH Esport : tournois, compétitions et animations gaming réunissant joueurs et passionnés en Lozère.')

@section('meta_keywords', 'HopLan 2025, ERAH Esport HopLan, tournoi HopLan, LAN gaming 2025, compétition esport Lozère, événement gaming, LAN party, esport France')

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

<div id="page-header" class="ph-full ph-full-m ph-cap-xlg ph-center ph-image-parallax ph-caption-parallax">


				<div class="ph-video ph-video-grayscale ph-video-cover-1">
					<div class="ph-video-inner">
						<video loop muted autoplay playsinline preload="metadata" 
       						poster="/template/assets/img/galerie/Hoplan-2025-2.webp">
  							<source src="/template/assets/vids/HOPELAN - Trime.mp4" type="video/mp4">
						</video>
					</div>
				</div>




				<div class="page-header-inner tt-wrap">

					<div class="ph-caption">
						<div class="ph-caption-inner">
							<h1 class="ph-caption-title">HOPLAN<br> 2025</h1>
							<div class="ph-caption-categories">
								<div class="ph-caption-category">Valorant</div>

							</div>
						</div>
					</div>

				</div>

				<div class="ph-share">
					<div class="ph-share-inner">
						<div class="ph-share-trigger">
							<div class="ph-share-text">Share</div>
							<div class="ph-share-icon"><i class="fas fa-share"></i></div>
						</div>
						<div class="ph-share-buttons">
							<ul>
								<li><a href="https://www.twitch.tv/erah_association" class="tt-magnetic-item"
										target="_blank" rel="noopener"><i class="fa-brands fa-twitch"></i></a></li>
								<li><a href="https://www.instagram.com/erahesport/" class="tt-magnetic-item"
										target="_blank" rel="noopener"><i class="fa-brands fa-instagram"></i></a></li>
								<li><a href="https://x.com/ErahEsport" class="tt-magnetic-item" target="_blank"
										rel="noopener"><i class="fa-brands fa-twitter"></i></a></li>
								<li><a href="https://discord.gg/9G89kkSjRx" class="tt-magnetic-item" target="_blank"
										rel="noopener"><i class="fa-brands fa-discord"></i></a></li>
							</ul>
						</div>
					</div>
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

								<textPath xlink:href="#textcircle">Scroll to Explore - Scroll to Explore -</textPath>
							</text>
						</svg>
					</a>
				</div>


			</div>



			<div id="tt-page-content">



				<div class="tt-section padding-top-xlg-130 padding-bottom-xlg-100">
					<div class="tt-section-inner tt-wrap">

						<div class="tt-row">
							<div class="tt-col-lg-9">

								<div class="tt-anim-fadeinup">
									<p>Nous sommes heureux d’annoncer notre participation à la <strong>HopLAN Valorant
											2025</strong> qui s’est tenue à Strasbourg. Cet événement majeur a été
										l’occasion pour notre équipe de démontrer tout son talent et sa détermination
										face à une forte concurrence.</p>

									<p>Grâce à leur engagement et leur cohésion, nos joueurs ont réussi à se hisser dans
										le Top 3 de la compétition, une performance dont nous sommes très fiers et qui
										illustre parfaitement la progression du club au fil des saisons.</p>
								</div>

							</div>

							<div class="tt-col-lg-3">
							</div>
						</div>

					</div>
				</div>



				<div class="tt-section no-padding">
					<div class="tt-section-inner tt-wrap">

						<div class="tt-sticker">

							<div class="tt-row tt-lg-row-reverse">
								<div class="tt-col-lg-3 margin-bottom-60">

									<div class="tt-sticker-sticky tt-sticky-element">


										<div class="tt-heading tt-heading-lg margin-bottom-40">

											<h2 class="tt-heading-title tt-text-reveal">Info</h2>
										</div>



										<div class="tt-project-info-list tt-anim-fadeinup">
											<ul>
												<li>
													<div class="pi-list-heading">Événements</div>
													<div class="pi-list-cont">HopLan</div>
												</li>
												<li>
													<div class="pi-list-heading">Année</div>
													<div class="pi-list-cont">2025</div>
												</li>
												<li>
													<div class="pi-list-heading">Équipe</div>
													<div class="pi-list-cont">Valorant</div>
												</li>
												<li>
													<div class="pi-list-heading">Plus d’informations</div>
													<div class="pi-list-cont"><a
															href="https://x.com/ErahEsport/status/1946986590976282733"
															target="_blank" rel="noopener">Twitter<span
																class="pi-list-icon"><i
																	class="fas fa-arrow-right"></i></span></a></div>
												</li>
											</ul>
										</div>


									</div>


								</div>

								<div class="tt-col-lg-9 padding-right-lg-3-p">

									<div class="tt-sticker-scroller">


										<div class="tt-image tti-border-radius tti-landscape margin-bottom-40">
											<figure>
												<a href="/template/assets/img/galerie/Hoplan-2025-3.png" class="tt-image-link"
													data-cursor="View" data-fancybox="gallery-746091"
													data-caption="Équipe Valorant">
													<img src="/template/assets/img/galerie/Hoplan-2025-3.png" class="tt-anim-zoomin"
														loading="lazy" alt="Image">
												</a>
												<figcaption>
													Équipe Valorant
												</figcaption>
											</figure>
										</div>


										<div class="tt-image tti-border-radius tti-landscape margin-bottom-40">
											<figure>
												<a href="/template/assets/img/galerie/interview-HopLan-2025.webp" class="tt-image-link"
													data-cursor="View" data-fancybox="gallery-746091"
													data-caption="Interview Valorant">
													<img src="/template/assets/img/galerie/interview-HopLan-2025.webp"
														class="tt-anim-zoomin" loading="lazy" alt="Image">
												</a>
												<figcaption>
													Interview Valorant
												</figcaption>
											</figure>
										</div>


										<div class="tt-image tti-border-radius tti-landscape margin-bottom-40">
											<figure>
												<a href="/template/assets/vids/day-2-hoplan-2025.mp4" class="tt-image-link"
													data-cursor="View" data-fancybox="gallery-746091"
													data-caption="Qualification Day 2">
													<video src="/template/assets/vids/day-2-hoplan-2025.mp4" class="tt-anim-zoomin" autoplay loop muted playsinline>
</video>

												</a>
												<figcaption>
													Qualification Day 2
												</figcaption>
											</figure>
										</div>





										<div class="tt-image tti-border-radius tti-landscape margin-bottom-40">
											<figure>
												<a href="/template/assets/vids/moment-hoplan-2025.mp4" class="tt-image-link"
													data-cursor="View" data-fancybox="gallery-746091"
													data-caption="HopLan 2025 Moments">
													<video src="/template/assets/vids/moment-hoplan-2025.mp4" class="tt-anim-zoomin" autoplay loop muted playsinline>
</video>

												</a>
												<figcaption>
													HopLan 2025 Moments
												</figcaption>
											</figure>
										</div>


									</div>


								</div>
							</div>

						</div>


					</div>
				</div>



				<div class="tt-section padding-bottom-xlg-160">
					<div class="tt-section-inner tt-wrap">

						<div class="text-xlg max-width-1000 tt-text-reveal">
							Nous sommes très heureux de voir l’évolution du club et de l’équipe, dont les efforts et
							l’engagement se traduisent aujourd’hui par cette belle performance.
						</div>

						<a href="https://x.com/ErahEsport"
							class="tt-btn tt-btn-outline margin-top-40 tt-anim-fadeinup tt-magnetic-item"
							target="_blank" rel="noopener">
							<span data-hover="Nous soutenir">Nous soutenir</span>
						</a>

					</div>
				</div>



				<div class="tt-section padding-top-xlg-120 padding-bottom-xlg-140 border-top">
					<div class="tt-section-inner tt-wrap">

						<div class="tt-next-project">
							<div class="tt-row">
								<div class="tt-col-md-7">

									<div class="tt-next-project-caption">
										<div class="tt-np-top tt-anim-fadeinup">
											<a href="/#erah" class="tt-btn tt-btn-link">
												<span data-hover="Notre équipe">Notre équipe</span>
												<span class="tt-btn-icon"><i class="fas fa-arrow-right"></i></span>
											</a>
										</div>
										<h3 class="tt-np-title tt-text-reveal">Next</h3>
									</div>

								</div>

								<div class="tt-col-md-5 tt-align-self-center">

									<div class="tt-next-project-item">
										<a href="/challengers-vcl-2025" class="tt-npi-image" data-cursor="GA 2024">
											<figure class="tt-npi-image-inner">

												<img src="/template/assets/img/galerie/challengers_valorant.jpg" class="tt-anim-zoomin"
													loading="lazy" alt="Image">
											</figure>
										</a>
										<div class="tt-npi-caption">
											<div class="tt-npi-title">
												<a href="/gamers-assembly-2024">Challengers VCL 2026</a>
											</div>
											<div class="tt-npi-categories-wrap">
												<div class="tt-npi-category">Valorant</div>

											</div>
										</div>
									</div>

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

