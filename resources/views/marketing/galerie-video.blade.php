@extends('marketing.layouts.template')

@section('title', 'Galerie Vidéo | ERAH Esport')

@section('meta_description', 'Découvrez la galerie vidéo d’ERAH Esport : replays de compétitions, aftermovies, highlights et moments forts de nos événements gaming.')

@section('meta_keywords', 'Galerie vidéo ERAH Esport, vidéos esport, replays tournois, highlights gaming, aftermovie esport, compétitions ERAH, vidéos gaming Lozère')

@section('meta_author', 'ERAH Esport')

@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('page_styles')
<style>
	.gallery-video-slider-wrap {
		position: relative;
	}

	.gallery-video-slide-media {
		position: absolute;
		inset: 0;
		overflow: hidden;
		background: #05070d;
	}

	.gallery-video-slide-media video,
	.gallery-video-slide-media img,
	.gallery-video-slide-media .gallery-video-slide-poster {
		width: 100%;
		height: 100%;
		object-fit: cover;
		display: block;
	}

	.gallery-video-slide-poster {
		background-position: center;
		background-repeat: no-repeat;
		background-size: cover;
	}

	.gallery-video-slide-overlay {
		position: absolute;
		inset: 0;
		background:
			linear-gradient(180deg, rgba(7, 10, 18, .14), rgba(7, 10, 18, .62) 62%, rgba(7, 10, 18, .88)),
			radial-gradient(circle at 18% 20%, rgba(216, 7, 7, .22), transparent 34%);
	}

	.gallery-video-slide-badge {
		display: inline-flex;
		align-items: center;
		gap: 8px;
		padding: 8px 14px;
		border-radius: 999px;
		border: 1px solid rgba(255, 255, 255, .18);
		background: rgba(0, 0, 0, .28);
		backdrop-filter: blur(12px);
		font-size: 12px;
		letter-spacing: .08em;
		text-transform: uppercase;
		color: rgba(255, 255, 255, .92);
		position: absolute;
		top: 32px;
		left: 32px;
		z-index: 2;
	}

	.gallery-video-summary {
		max-width: 620px;
		margin-top: 20px;
		font-size: 18px;
		line-height: 1.6;
		color: rgba(255, 255, 255, .78);
	}

	.gallery-video-slider-actions {
		display: flex;
		flex-wrap: wrap;
		gap: 14px;
		margin-top: 26px;
	}

	.gallery-video-empty {
		max-width: 880px;
		margin: 0 auto;
		padding: 48px 34px;
		text-align: center;
		border: 1px solid rgba(255, 255, 255, .12);
		border-radius: 26px;
		background:
			linear-gradient(180deg, rgba(255, 255, 255, .035), rgba(255, 255, 255, .015)),
			rgba(255, 255, 255, .03);
		box-shadow: 0 22px 46px rgba(0, 0, 0, .18);
	}

	.gallery-video-empty p {
		margin: 12px 0 0;
		color: rgba(255, 255, 255, .76);
		line-height: 1.7;
	}

	@media (max-width: 767px) {
		.gallery-video-slide-badge {
			top: 18px;
			left: 18px;
		}

		.gallery-video-summary {
			font-size: 16px;
		}

		.gallery-video-slider-actions {
			gap: 10px;
		}
	}
</style>
@endsection

@section('content')
@php
    use Illuminate\Support\Str;

    $videos = $videos ?? collect();
    $defaultPoster = '/template/assets/img/logo-fond.png';
@endphp

<div id="tt-page-content">
	@if($videos->isNotEmpty())
		<div class="gallery-video-slider-wrap">
			<div class="tt-portfolio-slider cursor-drag-mouse-down" data-direction="vertical" data-speed="1000"
				data-mousewheel="true" data-keyboard="true" data-simulate-touch="true" data-parallax="true"
				data-loop="true">
				<div class="swiper">
					<div class="swiper-wrapper">
						@foreach($videos as $video)
							<div class="swiper-slide">
								<article class="tt-portfolio-slider-item" data-swiper-parallax="50%">
									<div class="tt-posl-image-wrap cover-opacity-2">
										<div class="gallery-video-slide-media">
											@if($video->preview_video_url || $video->preview_video_webm_url)
												<video class="tt-posl-video" autoplay loop muted playsinline preload="metadata"
													poster="{{ $video->resolved_thumbnail_url ?: $defaultPoster }}">
													@if($video->resolved_preview_video_url)
														<source src="{{ $video->resolved_preview_video_url }}" type="video/mp4">
													@endif
													@if($video->preview_video_webm_url)
														<source src="{{ $video->preview_video_webm_url }}" type="video/webm">
													@endif
												</video>
											@elseif($video->resolved_thumbnail_url)
												<div class="gallery-video-slide-poster" style="background-image: url('{{ $video->resolved_thumbnail_url }}');"></div>
											@else
												<div class="gallery-video-slide-poster" style="background-image: url('{{ $defaultPoster }}');"></div>
											@endif

											<div class="gallery-video-slide-overlay"></div>

											@if($video->is_featured)
												<span class="gallery-video-slide-badge">A la une</span>
											@endif
										</div>
									</div>

									<div class="tt-posl-item-caption">
										<div class="tt-posl-item-caption-outer" data-swiper-parallax="100%">
											<div class="tt-posl-item-caption-inner">
												<div class="tt-posl-item-categories-wrap">
													<div class="tt-posl-item-category">{{ $video->display_category_label }}</div>
												</div>

												<h2 class="tt-posl-item-title">
													<a href="{{ $video->video_url }}" target="_blank" rel="noopener" data-cursor="Voir<br>la vidéo">
														{{ $video->title }}
													</a>
												</h2>

												<p class="gallery-video-summary">
													{{ $video->excerpt ?: Str::limit($video->description ?: 'Vidéo ajoutée depuis l’admin ERAH et publiée directement dans le slider.', 150) }}
												</p>

												<div class="gallery-video-slider-actions">
													<a href="{{ $video->video_url }}" class="tt-btn tt-btn-secondary hide-to-mobile" target="_blank" rel="noopener">
														<span data-hover="Voir la vidéo">Voir la vidéo</span>
													</a>

													<a href="{{ route('marketing.contact') }}" class="tt-btn tt-btn-outline hide-to-mobile tt-magnetic-item">
														<span data-hover="Proposer un contenu">Proposer un contenu</span>
													</a>
												</div>
											</div>
										</div>
									</div>
								</article>
							</div>
						@endforeach
					</div>
				</div>

				<div class="tt-portfolio-slider-navigation">
					<div class="tt-posl-nav-prev">
						<div class="tt-posl-nav-arrow tt-magnetic-item">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
								<path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path>
							</svg>
						</div>
					</div>
					<div class="tt-posl-nav-next">
						<div class="tt-posl-nav-arrow tt-magnetic-item">
							<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
								<path d="M13.025 1l-2.847 2.828 6.176 6.176h-16.354v3.992h16.354l-6.176 6.176 2.847 2.828 10.975-11z"></path>
							</svg>
						</div>
					</div>
				</div>

				<div class="tt-posl-pagination tt-hide-cursor"></div>

				<div class="tt-social-buttons">
					<ul>
						<li><a href="https://www.twitch.tv/erah_association" class="tt-magnetic-item" target="_blank" rel="noopener"><i class="fa-brands fa-twitch"></i></a></li>
						<li><a href="https://www.instagram.com/erahesport/" class="tt-magnetic-item" target="_blank" rel="noopener"><i class="fa-brands fa-instagram"></i></a></li>
						<li><a href="https://x.com/ErahEsport" class="tt-magnetic-item" target="_blank" rel="noopener"><i class="fa-brands fa-twitter"></i></a></li>
						<li><a href="https://discord.gg/9G89kkSjRx" class="tt-magnetic-item" target="_blank" rel="noopener"><i class="fa-brands fa-discord"></i></a></li>
					</ul>
				</div>
			</div>
		</div>
	@else
		<div class="tt-section padding-top-xlg-120 padding-bottom-xlg-120">
			<div class="tt-section-inner tt-wrap max-width-1200">
				<div class="gallery-video-empty">
					<h2>Galerie vidéo en préparation</h2>
					<p>Aucune vidéo publiée n'est disponible pour le moment. Ajoute simplement une vidéo dans l'admin et elle remontera automatiquement dans le slider public.</p>
					<div class="gallery-video-slider-actions" style="justify-content: center; margin-top: 26px;">
						<a href="{{ route('marketing.contact') }}" class="tt-btn tt-btn-secondary tt-magnetic-item">
							<span data-hover="Nous contacter">Nous contacter</span>
						</a>
					</div>
				</div>
			</div>
		</div>
	@endif
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
<script src="/template/assets/js/cookies.js" defer></script>
@endsection
