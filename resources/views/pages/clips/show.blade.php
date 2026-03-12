@extends('marketing.layouts.template')

@section('title', ($clip->title ?? 'Clip').' | ERAH Plateforme')
@section('meta_description', \Illuminate\Support\Str::limit((string) ($clip->description ?? 'Detail du clip ERAH.'), 155))
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    <link rel="stylesheet" href="/template/assets/css/blog.css">
    <style>
        .clip-media-player,
        .clip-media-embed iframe {
            width: 100%;
            border: 0;
            border-radius: 10px;
            background: #000;
        }

        .ph-video video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .ph-video-embed .ph-video-inner {
            position: relative;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .ph-video-embed iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
            pointer-events: none;
            transform: scale(1.08);
        }

        .clip-media-player {
            max-height: 620px;
        }

        .clip-media-embed {
            position: relative;
            width: 100%;
            padding-top: 56.25%;
            border-radius: 10px;
            overflow: hidden;
            background: #000;
        }

        .clip-media-embed iframe {
            position: absolute;
            inset: 0;
            height: 100%;
        }

        .clip-intro-text {
            margin-bottom: 24px;
        }

        .clip-engagement {
            margin: 14px 0 6px;
            padding-top: 14px;
            border-top: 1px solid rgba(255, 255, 255, .14);
        }

        .clip-engagement-meta {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
            margin-bottom: 12px;
        }

        .clip-engagement-meta-item {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 16px;
            line-height: 1.2;
            color: rgba(255, 255, 255, .92);
        }

        .clip-engagement-meta-item i {
            opacity: .85;
        }

        .clip-engagement-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .clip-engagement-actions form {
            margin: 0;
        }

        .clip-engagement-actions .tt-btn {
            border-radius: 999px;
            white-space: nowrap;
            padding-inline: 18px;
        }

        .clip-public-note {
            flex-basis: 100%;
            margin: 0 0 6px;
            padding: 10px 12px;
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 10px;
            font-size: 14px;
            line-height: 1.4;
        }

        @media (max-width: 991.98px) {
            .clip-engagement-actions {
                gap: 8px;
            }

            .clip-engagement-actions .tt-btn {
                width: 100%;
                text-align: center;
            }
        }

        .tt-comment-delete-form {
            display: inline;
        }

        .tt-comment-delete-form button {
            background: transparent;
            border: 0;
            padding: 0;
            color: inherit;
            cursor: pointer;
        }

        .clip-supporter-card {
            margin-top: 18px;
            padding: 16px;
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 14px;
            background: rgba(255, 255, 255, .03);
        }

        .clip-supporter-actions,
        .clip-supporter-vote-options {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 14px;
        }

        .clip-supporter-vote-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 12px;
            background: rgba(255, 255, 255, .025);
        }

        .clip-supporter-vote-option img {
            width: 72px;
            height: 48px;
            border-radius: 10px;
            object-fit: cover;
        }

        .clip-supporter-note {
            margin-top: 12px;
            color: rgba(255, 255, 255, .72);
            font-size: 14px;
        }

        .clip-comment-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-left: 8px;
            border: 1px solid rgba(255, 255, 255, .16);
            border-radius: 999px;
            padding: 2px 8px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .clip-comments-inline {
            margin-top: 26px;
            padding-top: 22px;
            border-top: 1px solid rgba(255, 255, 255, .12);
        }

        .clip-comments-board {
            margin-top: 48px;
            padding: 26px;
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 18px;
            background: rgba(255, 255, 255, .03);
        }

        .clip-comments-note {
            margin: 12px 0 0;
            color: rgba(255, 255, 255, .72);
        }

        .clip-comments-list {
            list-style: none;
            padding: 0;
            margin: 32px 0 0;
            display: grid;
            gap: 16px;
        }

        .clip-comment-item {
            display: grid;
            grid-template-columns: 64px minmax(0, 1fr);
            gap: 16px;
            padding: 18px;
            border: 1px solid rgba(255, 255, 255, .12);
            border-radius: 16px;
            background: rgba(255, 255, 255, .025);
        }

        .clip-comment-item.is-supporter {
            border-color: rgba(224, 34, 34, .35);
            background: linear-gradient(180deg, rgba(224, 34, 34, .08), rgba(255, 255, 255, .02));
        }

        .clip-comment-avatar {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            overflow: hidden;
            display: block;
            flex-shrink: 0;
            border: 1px solid rgba(255, 255, 255, .12);
            transition: border-color .22s ease, box-shadow .22s ease, transform .22s ease;
        }

        .clip-comment-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .clip-comment-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 10px;
        }

        .clip-comment-author {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .clip-comment-author-name {
            margin: 0;
            font-size: 20px;
            line-height: 1.1;
        }

        .clip-comment-author-name a,
        .clip-comment-avatar {
            color: inherit;
            text-decoration: none;
        }

        .clip-comment-author-name a {
            transition: color .22s ease, text-shadow .22s ease;
        }

        .clip-comment-item:hover .clip-comment-author-name a,
        .clip-comment-author-name a:hover,
        .clip-comment-author-name a:focus-visible {
            color: #e02222;
            text-shadow: 0 0 16px rgba(224, 34, 34, .18);
        }

        .clip-comment-item:hover a.clip-comment-avatar,
        a.clip-comment-avatar:hover,
        a.clip-comment-avatar:focus-visible {
            border-color: rgba(224, 34, 34, .75);
            box-shadow: 0 0 0 4px rgba(224, 34, 34, .12);
            transform: translateY(-2px);
        }

        .clip-comment-meta {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            color: rgba(255, 255, 255, .68);
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .clip-comment-body-text {
            color: rgba(255, 255, 255, .9);
            line-height: 1.65;
        }

        .clip-comment-body-text p:last-child {
            margin-bottom: 0;
        }

        .clip-comment-delete {
            background: transparent;
            border: 0;
            padding: 0;
            color: rgba(255, 255, 255, .8);
            cursor: pointer;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        body.tt-lightmode-on .clip-comments-board {
            border-color: rgba(33, 33, 33, .12);
            background: rgba(255, 255, 255, .76);
            box-shadow: 0 18px 40px rgba(33, 33, 33, .05);
        }

        body.tt-lightmode-on .clip-comments-note,
        body.tt-lightmode-on .clip-comment-meta {
            color: rgba(23, 23, 23, .62);
        }

        body.tt-lightmode-on .clip-comment-item {
            border-color: rgba(33, 33, 33, .1);
            background: rgba(255, 255, 255, .88);
        }

        body.tt-lightmode-on .clip-comment-item.is-supporter {
            border-color: rgba(224, 34, 34, .18);
            background: linear-gradient(180deg, rgba(224, 34, 34, .06), rgba(255, 255, 255, .9));
        }

        body.tt-lightmode-on .clip-comment-avatar {
            border-color: rgba(33, 33, 33, .1);
        }

        body.tt-lightmode-on .clip-comment-author-name,
        body.tt-lightmode-on .clip-comment-body-text,
        body.tt-lightmode-on .clip-comment-delete {
            color: #171717;
        }

        @media (max-width: 767.98px) {
            .clip-engagement-actions,
            .clip-supporter-actions,
            .clip-supporter-vote-options {
                display: grid;
                grid-template-columns: 1fr;
            }

            .clip-engagement-actions > *,
            .clip-engagement-actions form,
            .clip-engagement-actions .tt-btn,
            .clip-supporter-actions > *,
            .clip-supporter-actions form,
            .clip-supporter-actions .tt-btn {
                width: 100%;
            }

            .clip-comments-board {
                padding: 18px;
            }

            .clip-comment-item {
                grid-template-columns: 1fr;
            }

            .clip-comment-avatar {
                width: 52px;
                height: 52px;
            }
        }
    </style>
@endsection

@section('content')
    @php
        $isPublicApp = request()->routeIs('app.*');
        $isGuest = auth()->guest();
        $participationLoginUrl = route('login', ['required' => 'participation']);
        $indexRouteName = $isPublicApp ? 'app.clips.index' : 'clips.index';
        $showRouteName = $isPublicApp ? 'app.clips.show' : 'clips.show';
        $favoritesUrl = $isPublicApp
            ? (auth()->check() ? route('app.clips.favorites') : $participationLoginUrl)
            : ($isGuest ? $participationLoginUrl : route('clips.favorites'));
        $favoritesLabel = auth()->check() ? 'Mes clips favoris' : 'Connexion';
        $commentStoreRouteName = $isPublicApp ? 'app.clips.comment' : 'clips.comment';
        $commentDeleteRouteName = $isPublicApp ? 'app.clips.comment.delete' : 'clips.comment.delete';
        $authorName = $clip->createdBy?->name ?? 'ERAH';
        $thumbnail = $clip->thumbnail_url ?: '/template/assets/img/logo.png';
        $publishedLabel = optional($clip->published_at)->format('d/m/Y H:i') ?: 'Date inconnue';
        $videoUrl = trim((string) ($clip->video_url ?? ''));
        $mediaType = 'link';
        $embedUrl = null;
        $headerEmbedUrl = null;

        if ($videoUrl !== '') {
            if (preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $videoUrl) === 1) {
                $mediaType = 'file';
            } elseif (preg_match('~(?:youtube\.com/watch\?v=|youtu\.be/|youtube\.com/embed/)([A-Za-z0-9_-]{6,})~i', $videoUrl, $matches) === 1) {
                $mediaType = 'embed';
                $youtubeId = $matches[1];
                $embedUrl = 'https://www.youtube.com/embed/'.$youtubeId.'?rel=0&modestbranding=1';
                $headerEmbedUrl = 'https://www.youtube.com/embed/'.$youtubeId.'?autoplay=1&mute=1&controls=0&rel=0&modestbranding=1&playsinline=1&loop=1&playlist='.$youtubeId;
            } elseif (preg_match('~vimeo\.com/(?:video/)?([0-9]{6,})~i', $videoUrl, $matches) === 1) {
                $mediaType = 'embed';
                $vimeoId = $matches[1];
                $embedUrl = 'https://player.vimeo.com/video/'.$vimeoId.'?title=0&byline=0&portrait=0';
                $headerEmbedUrl = 'https://player.vimeo.com/video/'.$vimeoId.'?autoplay=1&muted=1&loop=1&background=1&title=0&byline=0&portrait=0';
            }
        }

        $shareUrl = route($showRouteName, $clip->slug);
        $encodedShareUrl = urlencode($shareUrl);
        $encodedTitle = urlencode((string) $clip->title);
    @endphp

    <div id="page-header" class="ph-cap-lg ph-image-parallax ph-caption-parallax">
        @if($mediaType === 'file')
            <div class="ph-video ph-video-cover-5">
                <div class="ph-video-inner">
                    <video loop muted autoplay playsinline preload="metadata" poster="{{ $thumbnail }}">
                        <source src="{{ $videoUrl }}">
                    </video>
                </div>
            </div>
        @elseif($mediaType === 'embed' && $headerEmbedUrl)
            <div class="ph-video ph-video-cover-5 ph-video-embed">
                <div class="ph-video-inner">
                    <iframe
                        src="{{ $headerEmbedUrl }}"
                        loading="lazy"
                        allow="autoplay; encrypted-media; picture-in-picture"
                        title="Background {{ $clip->title }}">
                    </iframe>
                </div>
            </div>
        @else
            <div class="ph-image ph-image-cover-5">
                <div class="ph-image-inner">
                    <img src="{{ $thumbnail }}" alt="{{ $clip->title }}">
                </div>
            </div>
        @endif

        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <div class="ph-caption-categories">
                        <a href="{{ route($indexRouteName) }}" class="ph-caption-category">Clip</a>
                        <a href="{{ $favoritesUrl }}" class="ph-caption-category">{{ $favoritesLabel }}</a>
                    </div>
                    <h1 class="ph-caption-title">{{ $clip->title }}</h1>
                    <div class="ph-caption-meta">
                        <span class="ph-cap-meta-published">{{ $publishedLabel }}</span>
                        <span class="ph-cap-meta-posted-by">par: <a href="{{ route($indexRouteName) }}">{{ $authorName }}</a></span>
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
                        <li><a href="{{ route($indexRouteName) }}" class="tt-magnetic-item" title="Retour aux clips"><i class="fa-solid fa-arrow-left"></i></a></li>
                        @if($videoUrl !== '')
                            <li><a href="{{ $videoUrl }}" class="tt-magnetic-item" target="_blank" rel="noopener" title="Ouvrir la video"><i class="fa-solid fa-play"></i></a></li>
                        @endif
                        <li><a href="{{ $favoritesUrl }}" class="tt-magnetic-item" title="{{ $favoritesLabel }}"><i class="fa-solid fa-star"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="tt-scroll-down">
            <a href="#tt-page-content" class="tt-scroll-down-inner tt-magnetic-item" data-offset="0">
                <div class="tt-scrd-icon"></div>
                <svg viewBox="0 0 500 500">
                    <defs>
                        <path d="M50,250c0-110.5,89.5-200,200-200s200,89.5,200,200s-89.5,200-200,200S50,360.5,50,250" id="textcircle"></path>
                    </defs>
                    <text dy="30">
                        <textPath xlink:href="#textcircle">Clip Detail - Clip Detail -</textPath>
                    </text>
                </svg>
            </a>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-xlg-120 padding-bottom-xlg-120">
            <div class="tt-section-inner tt-wrap max-width-1000">
                <article class="tt-blog-post">
                    <div class="tt-blog-post-content">
                        <div class="clip-intro-text">
                            @if(filled($clip->description))
                                <p class="text-lg">{!! nl2br(e((string) $clip->description)) !!}</p>
                            @else
                                <p class="text-lg">Aucune description disponible pour ce clip.</p>
                            @endif
                        </div>

                        @if($mediaType === 'file')
                            <video class="clip-media-player margin-bottom-30" controls preload="metadata" poster="{{ $thumbnail }}">
                                <source src="{{ $videoUrl }}">
                            </video>
                        @elseif($mediaType === 'embed' && $embedUrl)
                            <div class="clip-media-embed margin-bottom-30">
                                <iframe
                                    src="{{ $embedUrl }}"
                                    loading="lazy"
                                    allow="autoplay; encrypted-media; picture-in-picture"
                                    allowfullscreen
                                    title="{{ $clip->title }}">
                                </iframe>
                            </div>
                        @elseif($videoUrl !== '')
                            <p class="margin-bottom-30">
                                <a href="{{ $videoUrl }}" class="tt-link" target="_blank" rel="noopener">Ouvrir la video dans un nouvel onglet</a>
                            </p>
                        @endif

                        <div class="clip-engagement">
                            <div class="clip-engagement-meta">
                                <span class="clip-engagement-meta-item">
                                    <i class="fa-solid fa-heart"></i>
                                    <strong>{{ (int) $clip->likes_count }}</strong> likes
                                </span>
                                <span class="clip-engagement-meta-item">
                                    <i class="fa-solid fa-star"></i>
                                    <strong>{{ (int) $clip->favorites_count }}</strong> favoris
                                </span>
                                <span class="clip-engagement-meta-item">
                                    <i class="fa-solid fa-comment"></i>
                                    <strong>{{ (int) $clip->comments_count }}</strong> commentaires
                                </span>
                                <span class="clip-engagement-meta-item">
                                    <i class="fa-solid fa-link"></i>
                                    {{ $clip->slug }}
                                </span>
                            </div>

                            @if(auth()->check())
                                <div class="clip-engagement-actions">
                                    @if($isLiked)
                                        <form method="POST" action="{{ route('clips.unlike', $clip->id) }}">
                                            @csrf
                                            <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item">Retirer le like</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('clips.like', $clip->id) }}">
                                            @csrf
                                            <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">Liker</button>
                                        </form>
                                    @endif

                                    @if($isFavorited)
                                        <form method="POST" action="{{ route('clips.unfavorite', $clip->id) }}">
                                            @csrf
                                            <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item">Retirer des favoris</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('clips.favorite', $clip->id) }}">
                                            @csrf
                                            <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item">Ajouter aux favoris</button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('clips.share', $clip->id) }}">
                                        @csrf
                                        <input type="hidden" name="channel" value="link">
                                        <button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item">Enregistrer un partage</button>
                                    </form>
                                </div>
                            @else
                                <div class="clip-engagement-actions">
                                    <p class="clip-public-note">
                                        Creez un compte pour participer, gagner des points et progresser sur la plateforme.
                                    </p>
                                    @if($isPublicApp && auth()->check())
                                        <a href="{{ route('clips.show', $clip->slug) }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                            <span data-hover="Version interactive">Version interactive</span>
                                        </a>
                                    @else
                                        <a href="{{ $participationLoginUrl }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                            <span data-hover="Se connecter">Se connecter</span>
                                        </a>
                                        <a href="{{ route('register') }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                            <span data-hover="Creer un compte">Creer un compte</span>
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <div class="clip-supporter-card">
                            <h4 class="no-margin">Supporter ERAH</h4>
                            <p class="clip-supporter-note">
                                Tout membre connecte peut commenter. Les supporters actifs profitent seulement des reactions premium, des commentaires prioritaires et des votes clips.
                            </p>

                            @if(!$isPublicApp && $isSupporterActive)
                                <div class="clip-supporter-actions">
                                    @foreach(($supporterReactionOptions ?? []) as $reaction)
                                        @php($hasReaction = in_array($reaction['key'], $userSupporterReactionKeys ?? [], true))
                                        @if($hasReaction)
                                            <form method="POST" action="{{ route('clips.supporter-reactions.destroy', [$clip->id, $reaction['key']]) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item">
                                                    <span data-hover="{{ $reaction['label'] }} ({{ (int) ($supporterReactionCounts[$reaction['key']] ?? 0) }})">{{ $reaction['label'] }} ({{ (int) ($supporterReactionCounts[$reaction['key']] ?? 0) }})</span>
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('clips.supporter-reactions.store', $clip->id) }}">
                                                @csrf
                                                <input type="hidden" name="reaction_key" value="{{ $reaction['key'] }}">
                                                <button type="submit" class="tt-btn tt-btn-secondary tt-magnetic-item">
                                                    <span data-hover="{{ $reaction['label'] }} ({{ (int) ($supporterReactionCounts[$reaction['key']] ?? 0) }})">{{ $reaction['label'] }} ({{ (int) ($supporterReactionCounts[$reaction['key']] ?? 0) }})</span>
                                                </button>
                                            </form>
                                        @endif
                                    @endforeach
                                </div>

                                @forelse(($supporterCampaigns ?? []) as $campaign)
                                    @php($existingVote = $campaign->votes->first())
                                    <div class="clip-supporter-card margin-top-20">
                                        <h5 class="no-margin">{{ $campaign->title }}</h5>
                                        <p class="clip-supporter-note">Fin de campagne: {{ optional($campaign->ends_at)->format('d/m/Y H:i') }} - {{ (int) $campaign->votes_count }} vote(s)</p>

                                        <div class="clip-supporter-vote-options">
                                            @foreach($campaign->entries as $entry)
                                                @if($entry->clip)
                                                    <div class="clip-supporter-vote-option">
                                                        <img src="{{ $entry->clip->thumbnail_url ?: '/template/assets/img/logo.png' }}" alt="{{ $entry->clip->title }}">
                                                        <div>
                                                            <strong>{{ $entry->clip->title }}</strong>
                                                            @if((int) ($existingVote?->clip_id ?? 0) === (int) $entry->clip->id)
                                                                <div class="clip-supporter-note">Votre vote actuel</div>
                                                            @endif
                                                        </div>
                                                        <form method="POST" action="{{ route('clips.campaigns.vote', $campaign->id) }}">
                                                            @csrf
                                                            <input type="hidden" name="clip_id" value="{{ $entry->clip->id }}">
                                                            <button type="submit" class="tt-btn {{ (int) ($existingVote?->clip_id ?? 0) === (int) $entry->clip->id ? 'tt-btn-outline' : 'tt-btn-primary' }} tt-magnetic-item">
                                                                <span data-hover="Voter">Voter</span>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                @empty
                                @endforelse
                            @else
                                <p class="clip-supporter-note no-margin">
                                    @if($isPublicApp)
                                        La version publique affiche les clips sans interactions premium. Ouvrez votre espace membre pour retrouver le mode supporter.
                                    @elseif(auth()->check())
                                        Votre compte n a pas de support actif sur ce moment.
                                    @else
                                        Creez un compte puis activez Supporter ERAH pour debloquer ces interactions.
                                    @endif
                                </p>
                            @endif

                            @include('pages.clips.partials.comments-panel', [
                                'panelClass' => 'clip-comments-inline',
                            ])
                        </div>
                    </div>

                    <div class="tt-blog-post-tags">
                        <ul>
                            <li><span>Tags:</span></li>
                            <li><a href="{{ route($indexRouteName, ['sort' => 'recent']) }}">#recent</a></li>
                            <li><a href="{{ route($indexRouteName, ['sort' => 'popular']) }}">#popular</a></li>
                            <li><a href="{{ $favoritesUrl }}">#favoris</a></li>
                        </ul>
                    </div>

                    <div class="tt-blog-post-share">
                        <div class="tt-bps-text">Partager:</div>
                        <div class="tt-social-buttons">
                            <ul>
                                <li>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedShareUrl }}"
                                        class="tt-magnetic-item"
                                        target="_blank"
                                        rel="noopener"
                                        title="Partager sur Facebook">
                                        <i class="fa-brands fa-facebook-f"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://x.com/intent/tweet?url={{ $encodedShareUrl }}&text={{ $encodedTitle }}"
                                        class="tt-magnetic-item"
                                        target="_blank"
                                        rel="noopener"
                                        title="Partager sur X">
                                        <i class="fa-brands fa-x-twitter"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ $encodedShareUrl }}"
                                        class="tt-magnetic-item"
                                        target="_blank"
                                        rel="noopener"
                                        title="Partager sur LinkedIn">
                                        <i class="fa-brands fa-linkedin-in"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </article>

                <div class="tt-blog-post-nav">
                    <div class="tt-bp-nav-col tt-bp-nav-left">
                        <div class="tt-bp-nav-text">
                            <a href="{{ route($indexRouteName) }}"><span><i class="fa-solid fa-arrow-left"></i></span>Retour</a>
                        </div>
                        <h4 class="tt-bp-nav-title">
                            <a href="{{ route($indexRouteName) }}">Tous les clips</a>
                        </h4>
                    </div>
                    <div class="tt-bp-nav-col tt-bp-nav-right">
                        <div class="tt-bp-nav-text">
                            <a href="{{ $favoritesUrl }}">{{ $favoritesLabel }}<span><i class="fa-solid fa-arrow-right"></i></span></a>
                        </div>
                        <h4 class="tt-bp-nav-title">
                            <a href="{{ $favoritesUrl }}">{{ $favoritesLabel }}</a>
                        </h4>
                    </div>
                </div>

            </div>
        </div>
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
