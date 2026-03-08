@include('pages.reviews.partials.styles')

<div class="tt-section">
    <div class="tt-section-inner tt-wrap">
        <div class="tt-heading tt-heading-xxxlg tt-heading-center">
            <h2 class="tt-heading-title tt-text-reveal">Vos avis comptent</h2>
            <p class="max-width-500 tt-text-uppercase tt-text-reveal">
                Vos avis refletent notre passion commune. <strong>Merci d etre la, tout simplement.</strong>
            </p>
        </div>

        <br>
        <br>

        @if(($homeReviews ?? collect())->count())
            <div class="tt-sticky-testimonials">
                @foreach($homeReviews as $review)
                    <div class="tt-stte-item">
                        @include('pages.reviews.partials.card', ['review' => $review])
                    </div>
                @endforeach
            </div>

            <div class="review-home-actions">
                <a href="{{ route('reviews.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                    <span data-hover="Voir tous les avis">Voir tous les avis</span>
                </a>
            </div>
        @else
            <div class="tt-heading tt-heading-center">
                <p class="max-width-700">Les premiers avis publies apparaitront ici des qu ils seront valides.</p>
            </div>
        @endif
    </div>
</div>
