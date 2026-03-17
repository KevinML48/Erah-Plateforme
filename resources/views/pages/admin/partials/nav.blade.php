@php
    $navItems = [
        ['label' => 'Pilotage', 'route' => route('admin.dashboard'), 'active' => request()->routeIs('admin.dashboard') || request()->routeIs('admin.operations')],
        ['label' => 'Utilisateurs', 'route' => route('users.index'), 'active' => request()->routeIs('users.*') || request()->routeIs('admin.users.*')],
        ['label' => 'Clips', 'route' => route('admin.clips.index'), 'active' => request()->routeIs('admin.clips.*')],
        ['label' => 'Matchs', 'route' => route('admin.matches.index'), 'active' => request()->routeIs('admin.matches.*')],
        ['label' => 'Points', 'route' => route('admin.wallets.grant.create'), 'active' => request()->routeIs('admin.wallets.*')],
        ['label' => 'Cadeaux', 'route' => route('admin.gifts.index'), 'active' => request()->routeIs('admin.gifts.*') || request()->routeIs('admin.redemptions.*')],
        ['label' => 'Contacts', 'route' => route('admin.contact-messages.index'), 'active' => request()->routeIs('admin.contact-messages.*')],
        ['label' => 'Emails', 'route' => route('admin.emails.index'), 'active' => request()->routeIs('admin.emails.*')],
        ['label' => 'Missions', 'route' => route('admin.missions.index'), 'active' => request()->routeIs('admin.missions.*')],
        ['label' => 'Galerie', 'route' => route('admin.gallery-photos.index'), 'active' => request()->routeIs('admin.gallery-photos.*')],
        ['label' => 'Videos galerie', 'route' => route('admin.gallery-videos.index'), 'active' => request()->routeIs('admin.gallery-videos.*')],
        ['label' => 'Avis', 'route' => route('admin.reviews.index'), 'active' => request()->routeIs('admin.reviews.*')],
        ['label' => 'Supporters', 'route' => route('admin.supporters.index'), 'active' => request()->routeIs('admin.supporters.*')],
        ['label' => 'Campagnes clips', 'route' => route('admin.clips.campaigns.index'), 'active' => request()->routeIs('admin.clips.campaigns.*')],
    ];
@endphp

<nav class="adm-nav" aria-label="Navigation admin">
    @foreach($navItems as $item)
        <a href="{{ $item['route'] }}" class="tt-btn {{ $item['active'] ? 'tt-btn-primary' : 'tt-btn-outline' }} tt-magnetic-item">
            <span data-hover="{{ $item['label'] }}">{{ $item['label'] }}</span>
        </a>
    @endforeach
</nav>
