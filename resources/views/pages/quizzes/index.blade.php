@extends('marketing.layouts.template')

@section('title', 'Quiz | ERAH Plateforme')
@section('meta_description', 'Quiz communautaires ERAH, scores minimums, recompenses et tentatives.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.community.partials.styles')
@endsection

@section('page_scripts')
    @include('marketing.partials.theme-scripts')
@endsection

@section('content')
    @php
        $isPublicApp = request()->routeIs('app.*');
        $showRouteName = $isPublicApp ? 'app.quizzes.show' : 'quizzes.show';
    @endphp

    <div id="page-header" class="ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">ERAH Community</h2>
                    <h1 class="ph-caption-title">Quiz</h1>
                    <div class="ph-caption-description max-width-700">Repondez, validez le score minimum et alimentez vos missions communautaires.</div>
                </div>
            </div>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 padding-bottom-xlg-120">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="community-head">
                    <div>
                        <h1>Quiz actifs</h1>
                        <p>Les quiz sont lies aux missions, a l XP et aux points plateforme. Chaque fiche affiche le score minimum et le volume de tentatives.</p>
                    </div>
                    <div class="community-actions">
                        <a href="{{ $isPublicApp ? route('app.missions.index') : route('missions.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Missions">Missions</span></a>
                        <a href="{{ $isPublicApp ? route('app.statistics.index') : route('statistics.index') }}" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Statistiques">Statistiques</span></a>
                    </div>
                </div>

                <div class="community-kpis">
                    <article class="community-kpi"><strong>{{ $quizzes->total() }}</strong><span>Quiz disponibles</span></article>
                    <article class="community-kpi"><strong>{{ (int) $quizzes->sum('attempts_count') }}</strong><span>Tentatives suivies</span></article>
                    <article class="community-kpi"><strong>{{ (int) $quizzes->sum('questions_count') }}</strong><span>Questions publiees</span></article>
                    <article class="community-kpi"><strong>{{ optional($quizzes->first())->title ?: 'A venir' }}</strong><span>Dernier quiz mis en avant</span></article>
                </div>

                @if($quizzes->count())
                    <section class="community-grid">
                        @foreach($quizzes as $quiz)
                            <article class="community-card tt-anim-fadeinup">
                                <div class="community-meta">
                                    <span class="community-pill">Quiz</span>
                                    <span class="community-pill">{{ (int) $quiz->questions_count }} question(s)</span>
                                </div>
                                <div>
                                    <h3>{{ $quiz->title }}</h3>
                                    <p class="no-margin">{{ \Illuminate\Support\Str::limit((string) ($quiz->description ?? $quiz->intro), 170) }}</p>
                                </div>
                                <div class="community-meta">
                                    <span class="community-pill">Score mini {{ (int) $quiz->pass_score }}</span>
                                    <span class="community-pill">{{ (int) $quiz->attempts_count }} tentative(s)</span>
                                    <span class="community-pill">+{{ (int) $quiz->xp_reward }} XP</span>
                                </div>
                                <a href="{{ route($showRouteName, $quiz->slug) }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                    <span data-hover="Ouvrir le quiz">Ouvrir le quiz</span>
                                </a>
                            </article>
                        @endforeach
                    </section>

                    <div class="margin-top-40">{{ $quizzes->links() }}</div>
                @else
                    <div class="community-empty">Aucun quiz actif pour le moment.</div>
                @endif
            </div>
        </div>
    </div>
@endsection
