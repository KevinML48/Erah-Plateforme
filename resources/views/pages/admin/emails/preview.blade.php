@extends('marketing.layouts.template')

@section('title', 'Apercu email admin | ERAH Plateforme')
@section('meta_description', 'Apercu avant envoi d un email admin individuel.')
@section('body_class', 'tt-transition tt-noise tt-magic-cursor tt-smooth-scroll')

@section('head_extra')
    @include('pages.admin.partials.styles')
    <style>
        .adm-mail-meta-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .adm-mail-meta {
            border: 1px solid var(--adm-border-soft);
            border-radius: 16px;
            padding: 14px 16px;
            background: rgba(255, 255, 255, .03);
            display: grid;
            gap: 8px;
        }

        .adm-mail-meta span {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--adm-text-soft);
        }

        .adm-mail-meta strong,
        .adm-mail-preview-body {
            color: var(--adm-text);
        }

        .adm-mail-preview-body {
            border: 1px solid var(--adm-border-soft);
            border-radius: 18px;
            padding: 20px;
            background: rgba(255, 255, 255, .03);
            line-height: 1.7;
            word-break: break-word;
        }

        @media (max-width: 991.98px) {
            .adm-mail-meta-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endsection

@section('content')
    @include('pages.admin.partials.hero', [
        'heroSubtitle' => 'Administration ERAH',
        'heroTitle' => 'Apercu avant envoi',
        'heroDescription' => 'Verification finale du sujet, du destinataire et du contenu avant l envoi reel.',
        'heroMaskDescription' => 'Une confirmation claire pour eviter les erreurs et les doubles envois.',
    ])

    <div id="tt-page-content">
        <div class="tt-section padding-top-60 border-top">
            <div class="tt-section-inner tt-wrap max-width-1800">
                <div class="adm-shell">
                    @include('pages.admin.partials.nav')

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Recapitulatif</h2>
                        </div>

                        <div class="adm-mail-meta-grid">
                            <article class="adm-mail-meta"><span>Destinataire</span><strong>{{ $email->recipient_name ?: 'Destinataire externe' }}<br>{{ $email->recipient_email }}</strong></article>
                            <article class="adm-mail-meta"><span>Categorie</span><strong>{{ $email->categoryLabel() }}</strong></article>
                            <article class="adm-mail-meta"><span>Sujet</span><strong>{{ $email->subject }}</strong></article>
                            <article class="adm-mail-meta"><span>Copie admin</span><strong>{{ data_get($email->meta, 'cc_admin') ? 'Oui' : 'Non' }}</strong></article>
                        </div>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Apercu HTML</h2>
                        </div>

                        <div class="adm-mail-preview-body">{!! $email->body_html !!}</div>
                    </section>

                    <section class="adm-surface">
                        <div class="tt-heading tt-heading-lg margin-bottom-30">
                            <h2 class="tt-heading-title tt-text-reveal">Confirmation</h2>
                        </div>

                        <div class="adm-row-actions">
                            <a href="{{ route('admin.emails.create', ['draft' => $email->id]) }}" class="tt-btn tt-btn-outline tt-magnetic-item">
                                <span data-hover="Revenir modifier">Revenir modifier</span>
                            </a>

                            <form method="POST" action="{{ route('admin.emails.send', $email) }}" onsubmit="return confirm('Envoyer cet email maintenant ?');">
                                @csrf
                                <input type="hidden" name="confirm_token" value="{{ data_get($email->meta, 'confirm_token') }}">
                                <button type="submit" class="tt-btn tt-btn-primary tt-magnetic-item">
                                    <span data-hover="Envoyer maintenant">Envoyer maintenant</span>
                                </button>
                            </form>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page_scripts')
    @include('pages.admin.partials.theme-scripts')
@endsection