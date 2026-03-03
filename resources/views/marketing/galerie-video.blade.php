@extends('marketing.layouts.template')


@section('title', 'Galerie Vidéo | ERAH Esport')

@section('meta_description', 'Découvrez la galerie vidéo d’ERAH Esport : replays de compétitions, aftermovies, highlights et moments forts de nos événements gaming.')

@section('meta_keywords', 'Galerie vidéo ERAH Esport, vidéos esport, replays tournois, highlights gaming, aftermovie esport, compétitions ERAH, vidéos gaming Lozère')

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
</style>

@endverbatim
@endsection


@section('content')
@verbatim



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
