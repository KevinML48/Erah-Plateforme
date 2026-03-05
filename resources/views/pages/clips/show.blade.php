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
    </style>
@endsection

@section('content')
    @php
        $isPublicApp = request()->routeIs('app.*');
        $indexRouteName = $isPublicApp ? 'app.clips.index' : 'clips.index';
        $showRouteName = $isPublicApp ? 'app.clips.show' : 'clips.show';
        $favoritesUrl = $isPublicApp
            ? (auth()->check() ? route('app.clips.favorites') : route('login'))
            : route('clips.favorites');
        $favoritesLabel = auth()->check() ? 'Mes clips favoris' : 'Connexion';
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

                            @if(!$isPublicApp)
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
                                        Mode /app: consultation publique. Connectez-vous a la console pour liker, commenter et gerer les favoris.
                                    </p>
                                    @guest
                                        <a href="{{ route('login') }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                            <span data-hover="Se connecter">Se connecter</span>
                                        </a>
                                    @else
                                        <a href="{{ route('clips.show', $clip->slug) }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                            <span data-hover="Version interactive">Version interactive</span>
                                        </a>
                                    @endguest
                                </div>
                            @endif
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

                <div id="tt-blog-post-comments">
                    <h4 class="tt-bpc-heading">{{ $comments->total() }} commentaire(s)</h4>

                    @if(!$isPublicApp)
                        <form id="tt-post-comment-form" method="POST" action="{{ route('clips.comment', $clip->id) }}">
                            @csrf
                            <h4 class="tt-post-comment-form-heading">Ajouter un commentaire:</h4>
                            <small class="tt-form-text">Votre commentaire sera visible publiquement.</small>
                            <br>
                            <br>

                            <div class="tt-form-group">
                                <label for="body">Commentaire <span class="required">*</span></label>
                                <textarea class="tt-form-control" id="body" name="body" rows="6" required>{{ old('body') }}</textarea>
                            </div>

                            <button type="submit" class="tt-btn tt-btn-primary margin-top-30">
                                <span data-hover="Publier">Publier</span>
                            </button>
                        </form>
                    @else
                        <p class="tt-form-text">Les commentaires sont visibles publiquement. Publication reservee aux utilisateurs connectes dans la console.</p>
                    @endif

                    @if(($comments ?? null) && $comments->count())
                        <ul class="tt-comments-list margin-top-40">
                            @foreach($comments as $comment)
                                @php
                                    $commentAuthor = $comment->user?->name ?? 'Utilisateur';
                                    $avatarUrl = $comment->user?->avatar_url ?? '/template/assets/img/blog/avatar.png';
                                @endphp
                                <li class="tt-comment">
                                    <a href="#0" class="tt-comment-avatar">
                                        <img src="{{ $avatarUrl }}" loading="lazy" alt="{{ $commentAuthor }}">
                                    </a>
                                    <div class="tt-comment-body">
                                        <div class="tt-comment-meta">
                                            <h4 class="tt-comment-heading"><a href="#0">{{ $commentAuthor }}</a></h4>
                                            <span class="tt-comment-time">{{ optional($comment->created_at)->format('d/m/Y H:i') }}</span>
                                        </div>

                                        @if(!$isPublicApp && (auth()->id() === $comment->user_id || auth()->user()?->role === 'admin'))
                                            <span class="tt-comment-reply">
                                                <form method="POST" action="{{ route('clips.comment.delete', [$clip->id, $comment->id]) }}" class="tt-comment-delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit">Supprimer</button>
                                                </form>
                                            </span>
                                        @endif

                                        <div class="tt-comment-text">
                                            <p>{!! nl2br(e((string) $comment->body)) !!}</p>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>

                        <div class="margin-top-40">
                            {{ $comments->links() }}
                        </div>
                    @else
                        <p class="margin-top-40">Aucun commentaire.</p>
                    @endif
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
