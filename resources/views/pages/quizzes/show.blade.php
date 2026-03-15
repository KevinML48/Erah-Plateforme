@extends('marketing.layouts.template')

@section('title', ($quiz->title ?? 'Quiz').' | ERAH Plateforme')
@section('meta_description', \Illuminate\Support\Str::limit((string) ($quiz->description ?? 'Quiz ERAH'), 150))
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
        $indexRouteName = $isPublicApp ? 'app.quizzes.index' : 'quizzes.index';
        $attemptRouteName = $isPublicApp ? 'app.quizzes.attempt' : 'quizzes.attempt';
    @endphp

    <div id="page-header" class="ph-cap-xxxxlg ph-center ph-image-parallax ph-caption-parallax">
        <div class="page-header-inner tt-wrap">
            <div class="ph-caption">
                <div class="ph-caption-inner">
                    <h2 class="ph-caption-subtitle">Quiz ERAH</h2>
                    <h1 class="ph-caption-title">{{ $quiz->title }}</h1>
                    <div class="ph-caption-description max-width-700">{{ $quiz->description ?: 'Validez le score requis pour debloquer les rewards associees.' }}</div>
                </div>
            </div>
        </div>
    </div>

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 padding-bottom-xlg-120">
            <div class="tt-section-inner tt-wrap max-width-1400">
                <div class="community-head">
                    <div>
                        <h1>{{ $quiz->title }}</h1>
                        <p>{{ $quiz->intro ?: 'Repondez question par question. Une seule bonne réponse par item.' }}</p>
                    </div>
                    <div class="community-actions">
                        <a href="{{ route($indexRouteName) }}" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Retour">Retour aux quiz</span></a>
                    </div>
                </div>

                <div class="community-kpis">
                    <article class="community-kpi"><strong>{{ $quiz->questions->count() }}</strong><span>Questions</span></article>
                    <article class="community-kpi"><strong>{{ (int) $quiz->pass_score }}</strong><span>Score minimum</span></article>
                    <article class="community-kpi"><strong>+{{ (int) $quiz->xp_reward }}</strong><span>XP si valide</span></article>
                    <article class="community-kpi"><strong>+{{ (int) $quiz->reward_points }}</strong><span>Points plateforme si valide</span></article>
                </div>

                <section class="community-surface">
                    <form method="POST" action="{{ route($attemptRouteName, $quiz->slug) }}" class="tt-form">
                        @csrf
                        <div class="community-grid" style="grid-template-columns:1fr;">
                            @foreach($quiz->questions as $question)
                                <article class="community-card">
                                    <div class="community-meta">
                                        <span class="community-pill">Question {{ $loop->iteration }}</span>
                                        <span class="community-pill">{{ (int) $question->points }} pt(s)</span>
                                    </div>
                                    <h3>{{ $question->prompt }}</h3>
                                    @if(filled($question->explanation))
                                        <p class="no-margin">{{ $question->explanation }}</p>
                                    @endif
                                    @if($question->question_type === \App\Models\QuizQuestion::TYPE_SHORT_TEXT)
                                        <div class="community-form-grid">
                                            <label class="community-card" style="padding:14px;">
                                                <span class="community-meta">Reponse courte</span>
                                                <input
                                                    type="text"
                                                    name="answers[{{ $question->id }}]"
                                                    value="{{ old('answers.'.$question->id) }}"
                                                    class="tt-form-control margin-top-10"
                                                    placeholder="Saisissez votre réponse"
                                                    autocomplète="off"
                                                >
                                            </label>
                                        </div>
                                    @else
                                        <div class="community-form-grid">
                                            @foreach($question->answers as $answer)
                                                <label class="community-card" style="padding:14px;">
                                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $answer->id }}" @checked((int) old('answers.'.$question->id) === (int) $answer->id)>
                                                    <span>{{ $answer->label }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                </article>
                            @endforeach
                        </div>

                        <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item margin-top-30">
                            <span data-hover="Valider le quiz">Valider le quiz</span>
                        </button>
                    </form>
                </section>

                <section class="community-surface">
                    <h3 class="no-margin">Mes dernieres tentatives</h3>
                    @if($recentAttempts->count())
                        <table class="community-table margin-top-20">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Score</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentAttempts as $attempt)
                                    <tr>
                                        <td>{{ optional($attempt->finished_at)->format('d/m/Y H:i') }}</td>
                                        <td>{{ (int) $attempt->score }} / {{ (int) $attempt->max_score }}</td>
                                        <td>{{ $attempt->passed ? 'Valide' : 'Non valide' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="community-empty margin-top-20">Aucune tentative pour ce quiz.</div>
                    @endif
                </section>
            </div>
        </div>
    </div>
@endsection
