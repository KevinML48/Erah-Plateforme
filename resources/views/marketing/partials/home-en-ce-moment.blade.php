@php
    $activityItems = $homeEnCeMoment['activity_items'] ?? collect();
@endphp

<style>
    @import url('/template/assets/css/blog.css');

    #home-now-list {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    #home-now-list .blog-list-item {
        border: 1px solid rgba(255, 255, 255, .14);
        border-radius: 14px;
        padding: 14px;
        margin-bottom: 0;
        background: linear-gradient(160deg, rgba(255, 255, 255, .04), rgba(255, 255, 255, .01));
        display: grid;
        min-height: 220px;
    }

    #home-now-list .bli-image-wrap {
        display: none;
    }

    #home-now-list .bli-info {
        width: 100%;
        padding: 0;
    }

    #home-now-list .bli-title {
        margin-top: 6px;
        margin-bottom: 4px;
        font-size: clamp(22px, 2.4vw, 34px);
        line-height: .95;
    }

    #home-now-list .bli-meta {
        margin-bottom: 10px;
        font-size: 13px;
    }

    #home-now-list .bli-desc {
        margin-bottom: 0;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .home-now-status {
        display: inline-flex;
        border: 1px solid rgba(255, 255, 255, .24);
        border-radius: 999px;
        padding: 2px 8px;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .06em;
        margin-left: 8px;
    }

    @media (min-width: 1500px) {
        #home-now-list {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 991.98px) {
        #home-now-list {
            grid-template-columns: 1fr;
        }

        #home-now-list .blog-list-item {
            min-height: 0;
        }
    }
</style>

<div class="tt-section padding-bottom-xlg-120 border-top">
    <div class="tt-section-inner tt-wrap max-width-1800">
        <div class="tt-heading tt-heading-lg margin-bottom-40">
            <h3 class="tt-heading-subtitle tt-text-uppercase">Plateforme</h3>
            <h2 class="tt-heading-title">En ce moment sur la plateforme</h2>
        </div>
        <div id="home-now-list" class="bli-image-cropped">
            @forelse($activityItems as $item)
                @php
                    $itemUrl = (string) ($item['url'] ?? route('marketing.platform'));
                    $itemDate = ! empty($item['date']) ? \Illuminate\Support\Carbon::parse((string) $item['date']) : null;
                    $itemDateLabel = $itemDate ? $itemDate->format('d/m/Y H:i') : now()->format('d/m/Y H:i');
                    $itemImage = (string) ($item['image_url'] ?? '/template/assets/img/blog/1200/blog-1-1200.jpg');
                    $itemExcerpt = trim((string) ($item['excerpt'] ?? ''));
                @endphp
                <article class="blog-list-item">
                    <a href="{{ $itemUrl }}" class="bli-image-wrap" data-cursor="Read<br>More">
                        <figure class="bli-image tt-anim-zoomin">
                            <img src="{{ $itemImage }}" loading="lazy" alt="{{ $item['title'] }}">
                        </figure>
                    </a>
                    <div class="bli-info">
                        <div class="bli-categories">
                            <a href="{{ $itemUrl }}">{{ $item['label'] }}</a>
                            @if(! empty($item['status']))
                                <span class="home-now-status">{{ $item['status'] }}</span>
                            @endif
                        </div>
                        <h2 class="bli-title"><a href="{{ $itemUrl }}">{{ $item['title'] }}</a></h2>
                        <div class="bli-meta">
                            <span class="published">{{ $itemDateLabel }}</span>
                            <span class="posted-by">- <a href="{{ $itemUrl }}">{{ $item['label'] }}</a></span>
                        </div>
                        <div class="bli-desc">
                            {{ $itemExcerpt !== '' ? $itemExcerpt : 'Consultez cette activite pour voir les details.' }}
                        </div>
                    </div>
                </article>
            @empty
                <article class="blog-list-item">
                    <a href="{{ route('login') }}" class="bli-image-wrap" data-cursor="Read<br>More">
                        <figure class="bli-image tt-anim-zoomin">
                            <img src="/template/assets/img/blog/1200/blog-2-1200.jpg" loading="lazy" alt="Aucune activite">
                        </figure>
                    </a>
                    <div class="bli-info">
                        <div class="bli-categories">
                            <a href="{{ route('login') }}">Plateforme</a>
                        </div>
                        <h2 class="bli-title"><a href="{{ route('login') }}">Aucune activite utilisateur pour le moment</a></h2>
                        <div class="bli-meta">
                            <span class="published">{{ now()->format('d/m/Y H:i') }}</span>
                            <span class="posted-by">- <a href="{{ route('login') }}">Se connecter</a></span>
                        </div>
                        <div class="bli-desc">
                            Connectez-vous pour suivre vos points, duels, missions et matchs en cours.
                        </div>
                    </div>
                </article>
            @endforelse
        </div>
    </div>
</div>
