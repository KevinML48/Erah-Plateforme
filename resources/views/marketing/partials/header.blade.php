<header id="tt-header" class="tt-header-alter tt-header-scroll tt-header-filled">
	<div class="tt-header-inner tt-noise">

		<div class="tt-header-col tt-header-col-left">

			<div class="tt-logo">
				<a href="/" class="tt-magnetic-item">
					<img src="/template/assets/img/logo.png" class="tt-logo-light" alt="Logo">
					<img src="/template/assets/img/logo.png" class="tt-logo-dark" alt="Logo">
				</a>
			</div>


		</div>

		<div class="tt-header-col tt-header-col-center">

			<nav class="tt-main-menu tt-m-menu-center">
				<div class="tt-main-menu-holder">
					<div class="tt-main-menu-inner">
						<div class="tt-main-menu-content">


							<ul class="tt-main-menu-list">

								<li><a href="/">Accueil</a></li>

								<li><a href="/about">A propos</a></li>

								@auth
									<li><a href="{{ route('app.profile') }}">Profil</a></li>
								@else
									<li><a href="{{ route('login') }}">Profil</a></li>
								@endauth

								<li class="tt-submenu-wrap tt-submenu-master">
									<div class="tt-submenu-trigger">
										<a href="#">Equipes</a>
									</div>
									<div class="tt-submenu">
										<ul class="tt-submenu-list">

											<li><a href="/valorant-vcl">Valorant VCL</a></li>
											<li><a href="/staff">Staff ERAH</a></li>
											<li><a href="/medical">Medical</a></li>

										</ul>
									</div>
								</li>

								<li><a href="/app">Plateforme</a></li>

								<li class="tt-submenu-wrap tt-submenu-master">
									<div class="tt-submenu-trigger">
										<a href="#">Contenu</a>
									</div>
									<div class="tt-submenu">
										<ul class="tt-submenu-list">

											<li><a href="/galerie-photos">Galerie Photo</a></li>
											<li><a href="/galerie-video">Galerie Video</a></li>
											<li><a href="/evenement">Nos evenements</a></li>
											<li><a href="/nos-stages">Nos stages</a></li>

										</ul>
									</div>
								</li>


								<li><a href="/mende">Mende</a></li>

								<li><a href="/contact">Postuler</a></li>

							</ul>


						</div>
					</div>
				</div>

			</nav>


		</div>

		<div class="tt-header-col tt-header-col-right">


			<div id="tt-m-menu-toggle-btn-wrap">
				<div class="tt-m-menu-toggle-btn-text">
					<span class="tt-m-menu-text-menu">Menu</span>
					<span class="tt-m-menu-text-close">Close</span>
				</div>
				<div class="tt-m-menu-toggle-btn-holder">
					<a href="#" class="tt-m-menu-toggle-btn" aria-label="Ouvrir le menu">
						<span></span>
					</a>

				</div>
			</div>



			@auth
				<a href="{{ route('app.profile') }}"
					class="tt-btn tt-btn-secondary hide-from-xlg tt-magnetic-item">
					<span data-hover="Mon profil">Mon profil</span>
				</a>
			@else
				<a href="{{ route('login') }}"
					class="tt-btn tt-btn-secondary hide-from-xlg tt-magnetic-item">
					<span data-hover="Connexion">Connexion</span>
				</a>
			@endauth

			<div class="tt-style-switch">
				<div class="tt-style-switch-inner tt-magnetic-item">
					<div class="tt-stsw-light"><i class="fas fa-sun"></i></div>
					<div class="tt-stsw-dark"><i class="fas fa-moon"></i></div>
				</div>
			</div>
		</div>

	</div>

</header>

