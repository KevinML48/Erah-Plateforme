@php
    $reviewExists = $clubReview !== null;
    $reviewIsPublished = ($clubReview?->status ?? null) === \App\Models\ClubReview::STATUS_PUBLISHED;
    $reviewStatusLabel = $clubReview?->statusLabel();
@endphp

<section class="profile-form-card margin-top-30" id="profile-review-panel">
    <div class="profile-review-head">
        <div>
            <h4 class="margin-bottom-10">Mon avis sur le club</h4>
            <p class="tt-form-text no-margin">Un seul avis actif par membre. Vous pouvez le publier, le mettre a jour ou le retirer quand vous voulez.</p>
        </div>

        @if($reviewStatusLabel)
            <span class="profile-review-status {{ $reviewIsPublished ? 'is-published' : 'is-hidden' }}">{{ $reviewStatusLabel }}</span>
        @endif
    </div>

    <form method="POST" action="{{ $reviewExists ? route('profile.reviews.update') : route('profile.reviews.store') }}" class="tt-form tt-form-creative tt-form-lg">
        @csrf
        @if($reviewExists)
            @method('PUT')
        @endif

        <div class="tt-form-group">
            <label for="club-review-content">Votre avis <span class="required">*</span></label>
            <textarea
                class="tt-form-control"
                id="club-review-content"
                name="content"
                rows="6"
                minlength="20"
                maxlength="1200"
                placeholder="Expliquez ce que vous appreciez dans le club, la plateforme ou l experience membre."
                data-review-input
            >{{ old('content', $clubReview?->content) }}</textarea>

            <div class="profile-review-foot">
                <small class="tt-form-text">20 a 1200 caracteres. Les espaces vides ne sont pas pris en compte.</small>
                <span class="profile-review-counter"><strong data-review-count>0</strong> / 1200</span>
            </div>
        </div>

        <div class="profile-inline-actions margin-top-20">
            <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                <span data-hover="{{ $reviewExists ? ($reviewIsPublished ? 'Mettre a jour mon avis' : 'Republier mon avis') : 'Publier mon avis' }}">
                    {{ $reviewExists ? ($reviewIsPublished ? 'Mettre a jour mon avis' : 'Republier mon avis') : 'Publier mon avis' }}
                </span>
            </button>
            <a href="{{ route('reviews.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                <span data-hover="Voir tous les avis">Voir tous les avis</span>
            </a>
        </div>
    </form>

    @if($reviewExists)
        <div class="profile-review-note">
            <span>
                @if($reviewIsPublished)
                    Visible publiquement depuis le {{ optional($clubReview->published_at)->format('d/m/Y H:i') ?: 'maintenant' }}.
                @else
                    Cet avis n est pas visible publiquement pour le moment.
                @endif
            </span>

            <form method="POST" action="{{ route('profile.reviews.destroy') }}" onsubmit="return confirm('Retirer cet avis de l espace public ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item">
                    <span data-hover="Retirer mon avis">Retirer mon avis</span>
                </button>
            </form>
        </div>
    @endif
</section>
