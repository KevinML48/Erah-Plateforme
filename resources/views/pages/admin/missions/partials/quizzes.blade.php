<section class="adm-surface">
    <div class="tt-heading tt-heading-lg margin-bottom-20">
        <h2 class="tt-heading-title tt-text-reveal">Quiz communautaires</h2>
        <p class="max-width-700 tt-anim-fadeinup text-gray">CRUD quiz, score minimum, recompenses et rattachement mission.</p>
    </div>

    <form method="POST" action="{{ route('admin.quizzes.store') }}" class="tt-form tt-form-creative adm-form">
        @csrf
        <div class="adm-form-grid-3">
            <div class="tt-form-group">
                <label>Titre</label>
                <input class="tt-form-control" name="title" required>
            </div>
            <div class="tt-form-group">
                <label>Slug</label>
                <input class="tt-form-control" name="slug" placeholder="quiz-erah-s1" required>
            </div>
            <div class="tt-form-group">
                <label>Score minimum</label>
                <input class="tt-form-control" type="number" name="pass_score" min="0" value="1" required>
            </div>
            <div class="tt-form-group">
                <label>XP accordee</label>
                <input class="tt-form-control" type="number" name="xp_reward" min="0" value="80">
            </div>
            <div class="tt-form-group">
                <label>Points plateforme</label>
                <input class="tt-form-control" type="number" name="reward_points" min="0" value="40">
            </div>
            <div class="tt-form-group">
                <label>Template mission lie</label>
                <input class="tt-form-control" type="number" name="mission_template_id" min="1" placeholder="optionnel">
            </div>
            <div class="tt-form-group adm-col-span-3">
                <label>Description</label>
                <textarea class="tt-form-control" name="description" rows="2"></textarea>
            </div>
            <div class="tt-form-group adm-col-span-3">
                <label>Questions JSON</label>
                <textarea class="tt-form-control" name="questions_json" rows="8">[
  {
    "prompt": "Quel jeu represente le mieux ERAH ?",
    "points": 1,
    "answers": [
      { "label": "Rocket League", "is_correct": true },
      { "label": "Chess", "is_correct": false }
    ]
  },
  {
    "prompt": "Quel est le nom du club ?",
    "type": "short_text",
    "accepted_answer": "ERAH",
    "points": 1
  }
]</textarea>
            </div>
        </div>

        <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
            <span data-hover="Creer le quiz">Creer le quiz</span>
        </button>
    </form>

    @if(($quizzes ?? collect())->count())
        <div class="adm-mission-grid margin-top-30">
            @foreach($quizzes as $quiz)
                <article class="adm-mission-card">
                    <div class="adm-mission-head">
                        <h3 class="adm-mission-title">{{ $quiz->title }}</h3>
                        <span class="adm-pill">{{ $quiz->is_active ? 'Actif' : 'Inactif' }}</span>
                    </div>
                    <p class="adm-meta">{{ $quiz->slug }} - {{ (int) $quiz->attempts_count }} tentative(s)</p>
                    <div class="adm-row-actions">
                        <span class="adm-pill">Score mini {{ (int) $quiz->pass_score }}</span>
                        <span class="adm-pill">+{{ (int) $quiz->xp_reward }} XP</span>
                    </div>
                    <form method="POST" action="{{ route('admin.quizzes.destroy', $quiz->id) }}" onsubmit="return confirm('Supprimer ce quiz ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item">
                            <span data-hover="Supprimer">Supprimer</span>
                        </button>
                    </form>
                </article>
            @endforeach
        </div>
    @endif
</section>
