@php($authorUrl = $review['author_url'] ?? null)

<article class="tt-stte-card cursor-alter review-card">
    <div class="tt-stte-card-counter"></div>
    <div class="tt-stte-card-caption">
        <div class="review-card-head">
            <div class="review-card-author">
                <span class="review-card-avatar" aria-hidden="true">
                    @if(!empty($review['avatar_url']))
                        <img src="{{ $review['avatar_url'] }}" alt="Avatar {{ $review['author_name'] }}">
                    @else
                        <span>{{ $review['initials'] }}</span>
                    @endif
                </span>

                <div class="review-card-author-meta">
                    @if($authorUrl)
                        <a
                            href="{{ $authorUrl }}"
                            class="review-card-name tt-link"
                            @if(empty($review['is_member']))
                                target="_blank"
                                rel="noopener"
                            @endif
                        >
                            {{ $review['author_name'] }}
                        </a>
                    @else
                        <span class="review-card-name">{{ $review['author_name'] }}</span>
                    @endif

                    <span class="review-card-source">
                        {{ !empty($review['is_member']) ? 'Membre ERAH' : ($review['source_label'] ?? 'Avis public') }}
                    </span>
                </div>
            </div>

            @if(!empty($review['supporter_label']))
                <span class="review-card-pill review-card-pill--supporter">{{ $review['supporter_label'] }}</span>
            @endif
        </div>

        <div class="tt-stte-text">
            <em>{!! nl2br(e($review['content'])) !!}</em>
        </div>

        @if(!empty($review['meta']) || !empty($review['badges']))
            <div class="review-card-meta">
                @foreach(($review['meta'] ?? []) as $metaItem)
                    <span class="review-card-pill">{{ $metaItem }}</span>
                @endforeach

                @foreach(($review['badges'] ?? []) as $badge)
                    <span class="review-card-pill review-card-pill--accent">{{ $badge }}</span>
                @endforeach
            </div>
        @endif

        <div class="tt-stte-subtext review-card-footer">
            <span>
                @if(!empty($review['published_at']))
                    Publie le {{ $review['published_at']->format('d/m/Y') }}
                @endif
            </span>

            @if($authorUrl)
                <a
                    href="{{ $authorUrl }}"
                    class="tt-link"
                    @if(empty($review['is_member']))
                        target="_blank"
                        rel="noopener"
                    @endif
                >
                    {{ $review['author_cta'] ?? 'Voir plus' }}
                </a>
            @endif
        </div>
    </div>
</article>
