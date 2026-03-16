<header id="tt-header" class="tt-header-alter tt-header-scroll tt-header-filled tt-header-platform" data-mobile-nav-root>
	@php
		$user = auth()->user();
		$isAuthenticated = auth()->check();
		$isAdmin = $isAuthenticated && $user?->role === \App\Models\User::ROLE_ADMIN;
		$platformShortcuts = app(\App\Services\ShortcutService::class)->getForUser($user);

		$primaryNavigation = [
			['label' => 'Accueil', 'url' => url('/')],
			['label' => 'A propos', 'url' => url('/about')],
			[
				'label' => 'Equipes',
				'children' => [
					['label' => 'Valorant VCL', 'url' => url('/valorant-vcl')],
					['label' => 'Staff ERAH', 'url' => url('/staff')],
					['label' => 'Medical', 'url' => url('/medical')],
				],
			],
			[
				'label' => 'Contenu',
				'children' => [
					['label' => 'Galerie Photo', 'url' => route('marketing.gallery-photos')],
					['label' => 'Galerie Video', 'url' => url('/galerie-video')],
					['label' => 'Nos evenements', 'url' => url('/evenement')],
					['label' => 'Nos stages', 'url' => url('/nos-stages')],
				],
			],
			[
				'label' => 'Plateforme',
				'url' => route('marketing.platform'),
				'children' => collect([
					['label' => 'FAQ', 'url' => route('marketing.faq')],
				])
					->merge(collect($platformShortcuts)->map(fn ($shortcut) => [
						'label' => $shortcut['label'],
						'url' => $shortcut['url'],
					]))
					->when($isAdmin, fn ($items) => $items->push([
						'label' => 'Admin dashboard',
						'url' => route('admin.dashboard'),
					]))
					->values()
					->all(),
			],
		];

		$secondaryNavigation = [
			['label' => 'Supporter', 'url' => route('supporter.show')],
			['label' => 'Boutique', 'url' => route('marketing.boutique')],
			['label' => 'Postuler', 'url' => route('marketing.contact')],
			['label' => 'Mende', 'url' => url('/mende')],
		];

		$desktopPrimaryAccountAction = $isAuthenticated
			? ['label' => 'Mon profil', 'url' => route('app.profile')]
			: ['label' => 'Se connecter', 'url' => route('login')];

		$desktopAdminAction = $isAdmin
			? ['label' => 'Admin', 'url' => route('admin.dashboard')]
			: null;

		$showSettingsShortcut = $isAuthenticated && ! request()->routeIs('settings.*');

		$mobilePrimaryLinks = collect($primaryNavigation)
			->filter(fn ($item) => !empty($item['url']))
			->map(fn ($item) => [
				'label' => $item['label'],
				'url' => $item['url'],
			])
			->values()
			->all();

		$mobileExploreLinks = collect($primaryNavigation)
			->flatMap(fn ($item) => collect($item['children'] ?? [])->map(fn ($child) => [
				'label' => $child['label'],
				'url' => $child['url'],
			]))
			->values()
			->all();

		$mobileShortcutLinks = collect($platformShortcuts)
			->map(fn ($shortcut) => [
				'label' => $shortcut['label'],
				'url' => $shortcut['url'],
			])
			->values()
			->all();

		$mobileSessionLinks = $isAuthenticated
			? [
				['label' => 'Mon profil', 'url' => route('app.profile')],
				['label' => 'Parametres', 'url' => route('settings.index')],
				['label' => 'Notifications', 'url' => route('notifications.preferences')],
				['label' => 'Portefeuille', 'url' => route('wallet.index')],
				['label' => 'Centre d aide', 'url' => route('console.help')],
			]
			: [
				['label' => 'Se connecter', 'url' => route('login')],
				['label' => 'Inscription', 'url' => route('register')],
				['label' => 'Explorer la plateforme', 'url' => route('marketing.platform')],
			];

		if ($desktopAdminAction) {
			array_unshift($mobileSessionLinks, ['label' => 'Admin dashboard', 'url' => $desktopAdminAction['url']]);
		}

		$mobileSections = collect([
			['label' => 'Navigation principale', 'links' => $mobilePrimaryLinks],
			['label' => 'Explorer', 'links' => $mobileExploreLinks],
			['label' => 'Raccourcis ERAH', 'links' => $mobileShortcutLinks],
			['label' => 'Plus', 'links' => $secondaryNavigation],
			['label' => $isAuthenticated ? 'Session' : 'Compte', 'links' => $mobileSessionLinks],
		])
			->filter(fn ($section) => count($section['links']) > 0)
			->values()
			->all();
	@endphp

	<div class="tt-header-inner tt-noise">
		<div class="tt-header-col tt-header-col-left">
			<div class="tt-logo">
				<a href="/" class="tt-magnetic-item tt-header-logo-link" aria-label="Retour a l accueil">
					<img src="/template/assets/img/logo.png" class="tt-logo-light" alt="ERAH">
					<img src="/template/assets/img/logo.png" class="tt-logo-dark" alt="ERAH">
				</a>
			</div>
		</div>

		<div class="tt-header-col tt-header-col-center">
			<nav class="tt-main-menu tt-m-menu-center" aria-label="Navigation principale">
				<div class="tt-main-menu-holder">
					<div class="tt-main-menu-inner">
						<div class="tt-main-menu-content">
							<ul class="tt-main-menu-list">
								@foreach($primaryNavigation as $item)
									@if(!empty($item['children']))
										<li class="tt-submenu-wrap tt-submenu-master">
											<div class="tt-submenu-trigger">
												<a href="{{ $item['url'] ?? '#' }}">{{ $item['label'] }}</a>
											</div>
											<div class="tt-submenu">
												<ul class="tt-submenu-list">
													@foreach($item['children'] as $child)
														<li><a href="{{ $child['url'] }}">{{ $child['label'] }}</a></li>
													@endforeach
												</ul>
											</div>
										</li>
									@else
										<li><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
									@endif
								@endforeach

								<li class="tt-submenu-wrap tt-submenu-master tt-main-menu-secondary-more">
									<div class="tt-submenu-trigger">
										<a href="#">Plus</a>
									</div>
									<div class="tt-submenu">
										<ul class="tt-submenu-list">
											@foreach($secondaryNavigation as $item)
												<li><a href="{{ $item['url'] }}">{{ $item['label'] }}</a></li>
											@endforeach
										</ul>
									</div>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</nav>
		</div>

		<div class="tt-header-col tt-header-col-right">
			<div class="tt-header-action-stack" aria-label="Actions utilisateur">
				<div class="tt-header-account-cluster">
					<a href="{{ $desktopPrimaryAccountAction['url'] }}"
						class="tt-btn tt-btn-secondary tt-btn-sm tt-magnetic-item tt-header-account-btn tt-header-account-btn-primary">
						<span class="tt-header-account-label" data-hover="{{ $desktopPrimaryAccountAction['label'] }}">{{ $desktopPrimaryAccountAction['label'] }}</span>
					</a>
					@if($desktopAdminAction)
						<a href="{{ $desktopAdminAction['url'] }}"
							class="tt-btn tt-btn-outline tt-btn-sm tt-magnetic-item tt-header-account-btn tt-header-account-btn-admin">
							<span class="tt-header-account-label" data-hover="Admin">Admin</span>
						</a>
					@endif
				</div>

				<div class="tt-header-utility-cluster">
					@if($showSettingsShortcut)
						<a href="{{ route('settings.index') }}"
							class="tt-header-account-icon tt-magnetic-item tt-header-settings-shortcut"
							aria-label="Parametres">
							<i class="fas fa-cog" aria-hidden="true"></i>
						</a>
					@endif

					<div class="tt-style-switch tt-header-style-switch">
						<div class="tt-style-switch-inner tt-magnetic-item">
							<div class="tt-stsw-light"><i class="fas fa-sun"></i></div>
							<div class="tt-stsw-dark"><i class="fas fa-moon"></i></div>
						</div>
					</div>
				</div>
			</div>

			<button
				type="button"
				class="mobile-nav-toggle tt-header-mobile-toggle"
				data-mobile-nav-toggle
				aria-expanded="false"
				aria-controls="app-mobile-nav"
				aria-label="Ouvrir le menu"
			>
				<span class="mobile-nav-toggle-box" aria-hidden="true">
					<span></span>
					<span></span>
					<span></span>
				</span>
				<span>Menu</span>
			</button>
		</div>
	</div>

	<div class="mobile-nav-backdrop" data-mobile-nav-backdrop hidden></div>

	<div id="app-mobile-nav" class="mobile-nav-panel tt-header-mobile-panel" data-mobile-nav-panel hidden>
		<div class="mobile-nav-shell">
			<div class="mobile-nav-head">
				<div>
					<p class="mobile-nav-kicker">Navigation</p>
					<strong>ERAH sur petit ecran</strong>
				</div>
				<button type="button" class="mobile-nav-close" data-mobile-nav-close aria-label="Fermer le menu">
					Fermer
				</button>
			</div>

			@foreach ($mobileSections as $section)
				<section class="mobile-nav-section">
					<p class="mobile-nav-section-label">{{ $section['label'] }}</p>
					<div class="mobile-nav-list">
						@foreach ($section['links'] as $link)
							<a href="{{ $link['url'] }}" class="mobile-nav-link" data-mobile-nav-link>
								{{ $link['label'] }}
							</a>
						@endforeach
					</div>
				</section>
			@endforeach

			@auth
				<form method="POST" action="{{ route('auth.logout') }}" class="mobile-nav-logout">
					@csrf
					<button type="submit" class="tt-btn tt-btn-primary tt-btn-full">
						<span data-hover="Se deconnecter">Se deconnecter</span>
					</button>
				</form>
			@endauth
		</div>
	</div>
</header>
