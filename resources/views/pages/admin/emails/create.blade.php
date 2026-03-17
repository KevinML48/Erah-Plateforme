@extends('marketing.layouts.template')

@section('title', 'Composer un email admin | ERAH Plateforme')
@section('meta_description', 'Composition d un email administratif individuel depuis la console admin.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
    <style>
        .adm-recipient-card,
        .adm-template-card,
        .adm-search-result {
            border: 1px solid var(--adm-border-soft);
            border-radius: 18px;
            padding: 18px;
            background: rgba(255, 255, 255, .03);
        }

        .adm-search-results {
            display: grid;
            gap: 12px;
        }

        .adm-search-result h3,
        .adm-recipient-card h3,
        .adm-template-card h3 {
            margin: 0 0 8px;
            color: var(--adm-text);
            font-size: 20px;
        }

        .adm-search-result p,
        .adm-recipient-card p,
        .adm-template-card p {
            margin: 0;
        }

        .adm-search-result-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }
    </style>
@endsection

@section('content')
    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'Administration ERAH',
        'heroTitle' => 'Composer un email',
        'heroDescription' => 'Preparation d un email individuel administratif, avec apercu avant envoi.',
        'heroMaskDescription' => 'Recherche du destinataire, choix d un modele et validation finale avant envoi.',
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Recherche destinataire</h2>
                        </div>

                        <form method="GET" action="{{ route('admin.emails.create') }}" class="adm-form adm-form-grid-4 tt-form tt-form-creative tt-form-lg">
                            @if($draft)
                                <input type="hidden" name="draft" value="{{ $draft->id }}">
                            @endif
                            @if($selectedTemplateKey !== '')
                                <input type="hidden" name="template" value="{{ $selectedTemplateKey }}">
                            @endif
                            @if($selectedUser)
                                <input type="hidden" name="recipient_user_id" value="{{ $selectedUser->id }}">
                            @endif
                            <div class="tt-form-group adm-col-span-3">
                                <label for="recipient-search">Recherche utilisateur</label>
                                <input class="tt-form-control" id="recipient-search" name="recipient_search" type="text" value="{{ $recipientSearch }}" placeholder="Nom, email ou pseudo social si disponible">
                            </div>
                            <div class="tt-form-group adm-form-cta">
                                <p class="adm-form-cta-copy">Recherche par nom, email ou identifiant social stocke.</p>
                                <div class="adm-row-actions">
                                    <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Rechercher">Rechercher</span></button>
                                </div>
                            </div>
                        </form>

                        @if($selectedUser)
                            <div class="adm-recipient-card margin-top-20">
                                <h3>Destinataire interne selectionne</h3>
                                <p><strong>{{ $selectedUser->name }}</strong> · {{ $selectedUser->email }}</p>
                                <div class="adm-search-result-meta">
                                    <span class="adm-pill">ID #{{ $selectedUser->id }}</span>
                                    <span class="adm-pill">{{ $selectedUser->role }}</span>
                                    <a href="{{ route('admin.users.show', $selectedUser->id) }}" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Fiche utilisateur">Fiche utilisateur</span></a>
                                    <a href="{{ route('admin.emails.create', array_filter(['template' => $selectedTemplateKey !== '' ? $selectedTemplateKey : null])) }}" class="tt-btn tt-btn-secondary tt-magnetic-item"><span data-hover="Retirer">Retirer</span></a>
                                </div>
                            </div>
                        @endif

                        @if($userResults->count())
                            <div class="adm-search-results margin-top-20">
                                @foreach($userResults as $userResult)
                                    <article class="adm-search-result">
                                        <h3>{{ $userResult->name }}</h3>
                                        <p>{{ $userResult->email }}</p>
                                        <div class="adm-search-result-meta">
                                            <span class="adm-pill">ID #{{ $userResult->id }}</span>
                                            @foreach($userResult->socialAccounts as $account)
                                                @if(filled($account->provider_user_id))
                                                    <span class="adm-pill">{{ $account->provider }}: {{ $account->provider_user_id }}</span>
                                                @endif
                                            @endforeach
                                            <a href="{{ route('admin.emails.create', array_filter(['recipient_user_id' => $userResult->id, 'template' => $selectedTemplateKey !== '' ? $selectedTemplateKey : null])) }}" class="tt-btn tt-btn-primary tt-magnetic-item">
                                                <span data-hover="Choisir">Choisir</span>
                                            </a>
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        @elseif($recipientSearch !== '')
                            <div class="adm-empty margin-top-20">Aucun utilisateur ne correspond a cette recherche.</div>
                        @endif
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Modele et composition</h2>
                        </div>

                        <form method="GET" action="{{ route('admin.emails.create') }}" class="adm-form adm-form-grid-4 tt-form tt-form-creative tt-form-lg margin-bottom-20">
                            @if($selectedUser)
                                <input type="hidden" name="recipient_user_id" value="{{ $selectedUser->id }}">
                            @endif
                            @if($recipientSearch !== '')
                                <input type="hidden" name="recipient_search" value="{{ $recipientSearch }}">
                            @endif
                            @if($draft)
                                <input type="hidden" name="draft" value="{{ $draft->id }}">
                            @endif
                            <div class="tt-form-group adm-col-span-3">
                                <label for="template">Modele</label>
                                <select class="tt-form-control" id="template" name="template">
                                    <option value="">Sans modele</option>
                                    @foreach($templates as $templateItem)
                                        <option value="{{ $templateItem['key'] }}" @selected($selectedTemplateKey === $templateItem['key'])>{{ $templateItem['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="tt-form-group adm-form-cta">
                                <p class="adm-form-cta-copy">Charge un sujet et un corps de depart modifiables.</p>
                                <div class="adm-row-actions">
                                    <button type="submit" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Charger">Charger</span></button>
                                </div>
                            </div>
                        </form>

                        @if($template)
                            <div class="adm-template-card margin-bottom-20">
                                <h3>{{ $template['name'] }}</h3>
                                <p>Categorie par defaut : {{ $categoryLabels[$template['category']] ?? $template['category'] }}</p>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('admin.emails.preview') }}" class="adm-form adm-form-grid-4 tt-form tt-form-creative tt-form-lg">
                            @csrf
                            <input type="hidden" name="submission_token" value="{{ $submissionToken }}">
                            @if($selectedUser)
                                <input type="hidden" name="recipient_user_id" value="{{ $selectedUser->id }}">
                            @endif
                            <input type="hidden" name="template_key" value="{{ $selectedTemplateKey }}">

                            <div class="tt-form-group adm-col-span-2">
                                <label for="recipient_email">Email externe manuel</label>
                                <input class="tt-form-control" id="recipient_email" name="recipient_email" type="email" value="{{ old('recipient_email', $defaultValues['recipient_email']) }}" placeholder="Optionnel si un utilisateur interne est selectionne">
                                <p class="adm-field-help">Laissez vide si vous ciblez l email du membre selectionne.</p>
                                @error('recipient_email')<p class="adm-field-help">{{ $message }}</p>@enderror
                            </div>

                            <div class="tt-form-group">
                                <label for="recipient_name">Nom du destinataire</label>
                                <input class="tt-form-control" id="recipient_name" name="recipient_name" type="text" value="{{ old('recipient_name', $defaultValues['recipient_name']) }}" placeholder="Optionnel pour un email externe">
                                @error('recipient_name')<p class="adm-field-help">{{ $message }}</p>@enderror
                            </div>

                            <div class="tt-form-group">
                                <label for="category">Categorie</label>
                                <select class="tt-form-control" id="category" name="category">
                                    @foreach($categoryLabels as $categoryKey => $categoryLabel)
                                        <option value="{{ $categoryKey }}" @selected(old('category', $defaultValues['category']) === $categoryKey)>{{ $categoryLabel }}</option>
                                    @endforeach
                                </select>
                                @error('category')<p class="adm-field-help">{{ $message }}</p>@enderror
                            </div>

                            <div class="tt-form-group adm-col-span-4">
                                <label for="subject">Sujet</label>
                                <input class="tt-form-control" id="subject" name="subject" type="text" value="{{ old('subject', $defaultValues['subject']) }}" required>
                                <p class="adm-field-help">Variables simples disponibles : {name}, {email}, {platform_name}</p>
                                @error('subject')<p class="adm-field-help">{{ $message }}</p>@enderror
                            </div>

                            <div class="tt-form-group adm-col-span-4">
                                <label for="body_html">Contenu</label>
                                <textarea class="tt-form-control" id="body_html" name="body_html" required>{{ old('body_html', $defaultValues['body_html']) }}</textarea>
                                <p class="adm-field-help">HTML simple autorise : paragraphes, sauts de ligne, gras, italique, listes, liens, blockquotes. Le texte brut simple est aussi accepte.</p>
                                @error('body_html')<p class="adm-field-help">{{ $message }}</p>@enderror
                            </div>

                            <div class="tt-form-group">
                                <label for="cc_admin">Options</label>
                                <div class="tt-form-check">
                                    <input id="cc_admin" type="checkbox" name="cc_admin" value="1" @checked(old('cc_admin', $defaultValues['cc_admin']))>
                                    <label for="cc_admin">Copie a l adresse admin</label>
                                </div>
                            </div>

                            <div class="tt-form-group adm-form-cta adm-col-span-3">
                                <p class="adm-form-cta-copy">L email n est pas encore envoye. L etape suivante affiche un apercu complet avant confirmation.</p>
                                <div class="adm-row-actions">
                                    <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item"><span data-hover="Apercu avant envoi">Apercu avant envoi</span></button>
                                    <a href="{{ route('admin.emails.index') }}" class="tt-btn tt-btn-outline tt-magnetic-item"><span data-hover="Annuler">Annuler</span></a>
                                </div>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    @include('pages.admin.partials.theme-scripts')
@endsection