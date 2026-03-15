@extends('marketing.layouts.template')


@section('title', 'Galerie Photo | ERAH Esport')

@section('meta_description', 'Parcourez la galerie photo d’ERAH Esport : photos des compétitions, événements gaming et moments forts de notre club en Lozère et au-delà.')

@section('meta_keywords', 'Galerie photo ERAH Esport, photos esport, événements gaming, compétitions esport, moments forts ERAH, club gaming Lozère, activités esport')

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

	  .tt-grid-item.hidden { display: none; }
  #loadMore {
    background: #111;
    color: #fff;
    padding: 12px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 16px;
  }
  #loadMore:hover { background: #444; }

  .custom-btn {
  background-color: #111;
  color: #fff;
  padding: 12px 20px;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-size: 16px;
}

.custom-btn:hover {
  background-color: #444;
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
							<h2 class="ph-caption-subtitle">Galerie</h2>
							<h1 class="ph-caption-title">Photos</h1>
							<div class="ph-caption-description max-width-700">
								Parcourez nos plus beaux clichés et revivez chaque moment fort en images.
							</div>
						</div>
					</div>

				</div>

				<div class="page-header-inner ph-mask">
					<div class="ph-mask-inner tt-wrap">

						<div class="ph-caption">
							<div class="ph-caption-inner">
								<h2 class="ph-caption-subtitle">Galerie</h2>
								<h1 class="ph-caption-title">Souvenirs</h1>
								<div class="ph-caption-description max-width-700">
									Parcourez nos plus beaux clichés et revivez chaque moment fort en images.
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
					<div class="tt-section-inner max-width-2200">

						<div id="portfolio-grid" class="pgi-hover pgi-cap-inside">


							<div class="tt-grid ttgr-layout-3 ttgr-gap-1 ttgr-not-cropped">

								<div class="tt-grid-top">

									<div class="tt-grid-categories">

										<div class="ttgr-cat-trigger-wrap">
											<div class="ttgr-cat-trigger-holder">
												<a href="#categories" class="ttgr-cat-trigger" data-offset="150">
													<div class="ttgr-cat-text hide-cursor">
														<span data-hover="Open">Filter</span>
													</div>
												</a>
											</div>
										</div>


										<div class="ttgr-cat-nav">
											<div class="ttgr-cat-close-btn">Close <i class="fas fa-times"></i></div>
											<div class="ttgr-cat-list-holder cursor-close" data-lenis-prevent>
												<div class="ttgr-cat-list-inner">
													<div class="ttgr-cat-list-content">
														<ul class="ttgr-cat-list hide-cursor">
															<li class="ttgr-cat-item"><a href="#" class="active">All</a>
															</li>
															<li class="ttgr-cat-item"><a href="#"
																	data-filter=".valorant">Valorant</a></li>
															<li class="ttgr-cat-item"><a href="#"
																	data-filter=".evenements">Événements</a></li>
															<li class="ttgr-cat-item"><a href="#"
																	data-filter=".compétitions">Compétitions</a></li>
														</ul>
													</div>
												</div>
											</div>
										</div>


									</div>


								</div>

								<div class="tt-grid-items-wrap isotope-items-wrap">

									<div class="tt-grid-item isotope-item evenements">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="HopLan 2025">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/acteur-sport-2025-3.jpg"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">ActeurSport 2025</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Intervention</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item evenements">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="HopLan 2025">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/acteur-sport-2025-1.jpg"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">ActeurSport 2025</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Intervention</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item evenements">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="HopLan 2025">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/acteursport-2025-2.jpg"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">ActeurSport 2025</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Intervention</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item evenements">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="HopLan 2025">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/lozere_nouvelle_4.jpg"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Lozere Nouvelle</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Journal</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item valorant">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="HopLan 2025">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/challengers_valorant.jpg"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Challengers VCL 2026</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Esport</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item valorant">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="HopLan 2025">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/bracket-up-down.jpg"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Challengers VCL 2026</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Esport</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item valorant">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="HopLan 2025">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/lyon_esport.jpg"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Lyon Esport 2025</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Esport</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item valorant">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="HopLan 2025">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/lyon-esport-1.jpg"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Lyon Esport 2025</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Esport</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item evenements">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="HopLan 2025">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/chirac-conference-1.jpg"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Conférence Chirac</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Esport</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item evenements">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="HopLan 2025">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/chirac-conference-2.jpg"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Conférence Chirac</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Esport</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item evenements">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="HopLan 2025">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/chirac-conference-3.jpg"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Conférence Chirac</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Esport</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item valorant">
										<div class="ttgr-item-inner">


											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="HopLan 2025">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/Hoplan-2025-2.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="/single-project-1">HopLAN 2025</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Valorant</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item valorant">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="HopLan 2025">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/Hoplan-2025-3.png"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">HopLAN 2025</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Valorant</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item valorant">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="Interview">

													<div class="pgi-image-holder">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-video-wrap ttgr-height">
																<video class="pgi-video" loop muted preload="metadata">
																	<source
																		src="/template/assets/vids/interview-HopLan-2025.webm"
																		data-src="/template/assets/vids/interview-HopLan-2025.webm"
																		type="video/mp4">
																	<source
																		src="/template/assets/vids/interview-HopLan-2025.webm"
																		data-src="/template/assets/vids/interview-HopLan-2025.webm"
																		type="video/webm">
																</video>

															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Interview</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Valorant</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item valorant">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="GA 2025">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie//gamers-assembly-2025-1.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Gamers Assembly 2025</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Valorant</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item valorant">
										<div class="ttgr-item-inner">


											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="GA 2025">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/gamers-assembly-2025-2.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Gamers Assembly 2025</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Valorant</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item valorant">
										<div class="ttgr-item-inner">


											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="GA 2025">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/gamers-assembly-2025-3.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Gamers Assembly 2025</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Valorant</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item valorant">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="Interview">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/interview-HopLan-2025.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Interview HopLan 2025</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Valorant</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item valorant">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="Interview">

													<div class="pgi-image-holder">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-video-wrap ttgr-height">
																<video class="pgi-video" loop muted preload="metadata">
																	<source
																		src="/template/assets/vids/interview-GA-2025.mp4"
																		data-src="/template/assets/vids/interview-GA-2025.mp4"
																		type="video/mp4">
																	<source
																		src="/template/assets/vids/interview-GA-2025.webm"
																		data-src="/template/assets/vids/interview-GA-2025.webm"
																		type="video/webm">
																</video>

															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Interview</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Valorant</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item valorant">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="GA 2024">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/gamers-assembly-2024-1.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Gamers Assembly 2024</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Valorant</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item valorant">
										<div class="ttgr-item-inner">


											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="GA 2024">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/gamers-assembly-2024-2.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Gamers Assembly 2024</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Valorant</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item valorant">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="GA 2024">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/gamers-assembly-2024-3.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Gamers Assembly 2024</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Valorant</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item valorant">
										<div class="ttgr-item-inner">


											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="HopLan 2024">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/HopLan-2024-1.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">HopLan 2024</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Valorant</div>

														</div>
													</div>
												</div>
											</div>

										</div>
									</div>

									<div class="tt-grid-item isotope-item valorant">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="HopLan 2024">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/HopLan-2024-2.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">HopLan 2024</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Valorant</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item valorant">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="HopLan 2024">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/HopLan-2024-3.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">HopLan 2024</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Valorant</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item valorant">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="HopLan 2024">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/HopLan-2024-4.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">HopLan 2024</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Valorant</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item valorant">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="HopLAN 2024">

													<div class="pgi-image-holder">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-video-wrap ttgr-height">
																<video class="pgi-video" loop muted preload="metadata">
																	<source
																		src="/template/assets/vids/HOPELAN - Trime.mp4"
																		data-src="/template/assets/vids/HOPELAN - Trime.mp4"
																		type="video/mp4">
																	<source
																		src="/template/assets/vids/HOPELAN - Trime.webm"
																		data-src="/template/assets/vids/HOPELAN - Trime.webm"
																		type="video/webm">
																</video>

															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">HopLan 2024</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Valorant</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item valorant">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="Interview">

													<div class="pgi-image-holder">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-video-wrap ttgr-height">
																<video class="pgi-video" loop muted preload="metadata">
																	<source
																		src="/template/assets/vids/interview-HopLan-2024.mp4"
																		data-src="/template/assets/vids/interview-HopLan-2024.mp4"
																		type="video/mp4">
																	<source
																		src="/template/assets/vids/interview-HopLan-2024.webm"
																		data-src="/template/assets/vids/interview-HopLan-2024.webm"
																		type="video/webm">
																</video>

															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Interview</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Valorant</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item compétitions">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="Alsace Arena">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/alsace-arena-COD-1.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Alsace Arena 2025</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">COD</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item compétitions">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="Cod REACT">

													<div class="pgi-image-holder">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-video-wrap ttgr-height">
																<video class="pgi-video" loop muted preload="metadata">
																	<source
																		src="/template/assets/img/galerie/cod_react.mp4"
																		data-src="/template/assets/img/galerie/cod_react.mp4"
																		type="video/mp4">
																	<source
																		src="/template/assets/img/galerie/cod_react.webm"
																		data-src="/template/assets/img/galerie/cod_react.webm"
																		type="video/webm">
																</video>

															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">React</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">COD</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item compétitions">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="Alsace Arena">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/alsace-arena-COD-2.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Alsace Arena 2025</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">COD</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item compétitions">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="Espot 2025">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/Espot-2025-1.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Espot 2025</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">COD</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item compétitions">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="Cod REACT">

													<div class="pgi-image-holder">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-video-wrap ttgr-height">
																<video class="pgi-video" loop muted preload="metadata">
																	<source
																		src="/template/assets/img/galerie/Espot-4-COD.mp4"
																		data-src="/template/assets/img/galerie/Espot-4-COD.mp4"
																		type="video/mp4">
																	<source
																		src="/template/assets/img/galerie/Espot-4-COD.webm"
																		data-src="/template/assets/img/galerie/Espot-4-COD.webm"
																		type="video/webm"> 
																</video>

															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Espot 2025</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">COD</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item compétitions">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="Espot 2025">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/Espot-2025-2.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Espot 2025</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">COD</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item compétitions">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="Espot 2025">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/Espot-2025-3.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Espot 2025</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">COD</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item compétitions">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="Espot 2024">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/Espot-paris-2024-1.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Espot 2024</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">COD</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item valorant">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="Bootcamp GC">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/bootcamp-GC.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Bootcamp GC 2024</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Valorant</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item valorant">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="Interview">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/gamers-assembly-2025-1.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">GA 2025</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Valorant</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item valorant">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="Moment GA 2025">

													<div class="pgi-image-holder">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-video-wrap ttgr-height">
																<video class="pgi-video" loop muted preload="metadata">
																	<source
																		src="/template/assets/img/galerie/moment-ga-2025.mp4"
																		data-src="/template/assets/img/galerie/moment-ga-2025.mp4"
																		type="video/mp4">
																	<source
																		src="/template/assets/img/galerie/moment-ga-2025.webm"
																		data-src="/template/assets/img/galerie/moment-ga-2025.webm"
																		type="video/webm">
																</video>

															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Moment GA 2025</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Valorant</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item evenements">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="Interventions">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/intervention-2.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Conférence 2025</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Esport</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item evenements">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="Interventions">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/Intervention-1.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Conférence 2025</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Esport</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item evenements">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="Missions Locale">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/mission-locale-1.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Interventions</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Mission Locale</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item evenements">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="Missions Locale">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/mission-locale-2.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Interventions</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Mission Locale</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item evenements">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="Missions Locale">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/mission-locale-3.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Interventions</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Mission Locale</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item evenements">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="LozereNouvelle">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/journal-1.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">LozereNouvelle</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Journal</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item evenements">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="LozereNouvelle">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/journal-2.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">LozereNouvelle</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Journal</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item evenements">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="LozereNouvelle">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/partenariat-1.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">LozereNouvelle</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Journal</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item evenements">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="Radio">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/lozere-nouvelle-1.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">LozereNouvelle</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Radio</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item evenements">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="Radio">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/lozere-nouvelle-2.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">LozereNouvelle</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">Radio</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item valorant">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="Moment GA 2025">

													<div class="pgi-image-holder">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-video-wrap ttgr-height">
																<video class="pgi-video" loop muted preload="metadata">
																	<source
																		src="/template/assets/vids/Présentation ERAH.mp4"
																		data-src="/template/assets/vids/Présentation ERAH.webm"
																		type="video/mp4">
																	<source
																		src="/template/assets/vids/Présentation ERAH.webm"
																		data-src="/template/assets/vids/Présentation ERAH.webm"
																		type="video/webm">
																</video>

															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Lancement</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">ERAH</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item compétitions">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="TGF 2024">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/tgf-1.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">TGF 2024</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">RL</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item compétitions">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="TGF 2024">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/tgf-2.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">TGF 2024</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">RL</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item compétitions">
										<div class="ttgr-item-inner">


											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="TGF 2024">

													<div class="pgi-image-holder cover-opacity-2">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-image ttgr-height">
																<img src="/template/assets/img/galerie/tgf-3.webp"
																	loading="lazy" alt="image">
															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">TGF 2024</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">RL</div>

														</div>
													</div>
												</div>
											</div>


										</div>
									</div>

									<div class="tt-grid-item isotope-item valorant">
										<div class="ttgr-item-inner">

											<div class="portfolio-grid-item">
												<a href="#" class="pgi-image-wrap" data-cursor="Moment GA 2025">

													<div class="pgi-image-holder">
														<div class="pgi-image-inner tt-anim-zoomin">
															<figure class="pgi-video-wrap ttgr-height">
																<video class="pgi-video" loop muted preload="metadata">
																	<source
																		src="/template/assets/vids/Interview_Equipe_Rocket-league - Trim.mp4"
																		data-src="/template/assets/vids/Interview_Equipe_Rocket-league - Trim.mp4"
																		type="video/mp4">
																	<source
																		src="/template/assets/vids/Interview_Equipe_Rocket-league - Trim.webm"
																		data-src="/template/assets/vids/Interview_Equipe_Rocket-league - Trim.webm"
																		type="video/webm">
																</video>

															</figure>
														</div>
													</div>
												</a>

												<div class="pgi-caption">
													<div class="pgi-caption-inner">
														<h2 class="pgi-title">
															<a href="#">Interview</a>
														</h2>
														<div class="pgi-categories-wrap">
															<div class="pgi-category">RL</div>

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

<div class="text-center" style="margin: 30px 0;">
  <a href="#" id="loadMoreBtn" class="tt-btn tt-btn-outline tt-magnetic-item" style="color: #fff;">
    <span data-hover="Afficher plus" style="color: #fff;">Afficher plus</span>
  </a>
</div>






			</div>

			<div class="tt-section padding-bottom-xlg-120">
				<div class="tt-section-inner tt-wrap">

					<div class="tt-row margin-bottom-40">
						<div class="tt-col-xl-8">


							<div class="tt-heading tt-heading-xxxlg no-margin">
								<h3 class="tt-heading-subtitle tt-text-reveal">Rejoins-nous</h3>
								<h2 class="tt-heading-title tt-text-reveal">Notre histoire</h2>
							</div>


						</div>

						<div class="tt-col-xl-4 tt-align-self-end tt-xl-column-reverse margin-top-40">

							<div class="max-width-600 margin-bottom-10 tt-text-uppercase tt-text-reveal">
								Plonge dans notre univers, vis chaque moment fort avec nous<br>
								et fais partie de l’histoire dès aujourd’hui&nbsp;!
							</div>


							<div class="tt-big-round-ptn margin-top-30 margin-bottom-xlg-80 tt-anim-fadeinup">
								<a href="/contact" class="tt-big-round-ptn-holder tt-magnetic-item">
									<div class="tt-big-round-ptn-inner">Je<br> Rejoins !</div>
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
