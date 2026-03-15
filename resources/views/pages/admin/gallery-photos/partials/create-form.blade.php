<form method="POST" action="{{ route('admin.gallery-photos.store') }}" enctype="multipart/form-data" class="adm-gallery-form-layout">
    @csrf

    <div class="adm-gallery-form-main">
        <section class="adm-gallery-section-card">
            <div class="adm-gallery-section-head">
                <div>
                    <span class="adm-gallery-section-eyebrow">Upload</span>
                    <h3>Ajouter une photo ou une video</h3>
                </div>
                <p>Import local, meta publiques et statut dans le meme flux pour publier proprement sans changer de page.</p>
            </div>

            <div class="adm-gallery-upload-block">
                <label class="adm-gallery-form-label" for="media_file">Media *</label>
                <input
                    class="tt-form-control"
                    id="media_file"
                    name="media_file"
                    type="file"
                    accept="image/*,video/mp4,video/webm"
                    required
                    data-gallery-upload-input
                >
                <p class="adm-gallery-inline-help">Formats supportes : JPG, PNG, WebP, AVIF, GIF, MP4 et WebM. Taille max 50 Mo.</p>
                @error('media_file')
                    <p class="adm-gallery-error">{{ $message }}</p>
                @enderror
            </div>
        </section>

        <section class="adm-gallery-section-card">
            <div class="adm-gallery-section-head">
                <div>
                    <span class="adm-gallery-section-eyebrow">Contenu</span>
                    <h3>Informations visibles</h3>
                </div>
                <p>Le titre, l'alt text et la description donnent un rendu propre cote public et une lecture rapide cote admin.</p>
            </div>

            <div class="adm-gallery-field-grid adm-gallery-field-grid-2">
                <div class="adm-gallery-field-card">
                    <label class="adm-gallery-form-label" for="title">Titre</label>
                    <input class="tt-form-control" id="title" name="title" type="text" value="{{ old('title') }}" placeholder="HopLan 2025">
                    @error('title')
                        <p class="adm-gallery-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="adm-gallery-field-card">
                    <label class="adm-gallery-form-label" for="alt_text">Alt text</label>
                    <input class="tt-form-control" id="alt_text" name="alt_text" type="text" value="{{ old('alt_text') }}" placeholder="Equipe ERAH en compétition">
                    @error('alt_text')
                        <p class="adm-gallery-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="adm-gallery-field-card adm-span-2">
                    <label class="adm-gallery-form-label" for="description">Description</label>
                    <textarea class="tt-form-control" id="description" name="description" placeholder="Contexte, lieu, ambiance ou détail utile pour l'admin et la page publique.">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="adm-gallery-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        <section class="adm-gallery-section-card">
            <div class="adm-gallery-section-head">
                <div>
                    <span class="adm-gallery-section-eyebrow">Organisation</span>
                    <h3>Filtres et taxonomie publique</h3>
                </div>
                <p>Gardez des libelles lisibles cote front sans perdre les cles techniques deja utilisees par la galerie publique.</p>
            </div>

            <div class="adm-gallery-field-grid adm-gallery-field-grid-2">
                <div class="adm-gallery-field-card">
                    <label class="adm-gallery-form-label" for="filter_key">Filter key</label>
                    <input class="tt-form-control" id="filter_key" name="filter_key" type="text" value="{{ old('filter_key') }}" placeholder="valorant">
                    <p class="adm-gallery-inline-help">Cle technique stable pour les filtres.</p>
                    @error('filter_key')
                        <p class="adm-gallery-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="adm-gallery-field-card">
                    <label class="adm-gallery-form-label" for="filter_label">Filter label</label>
                    <input class="tt-form-control" id="filter_label" name="filter_label" type="text" value="{{ old('filter_label') }}" placeholder="Valorant">
                    <p class="adm-gallery-inline-help">Libelle visible cote public.</p>
                    @error('filter_label')
                        <p class="adm-gallery-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="adm-gallery-field-card">
                    <label class="adm-gallery-form-label" for="category_label">Categorie</label>
                    <input class="tt-form-control" id="category_label" name="category_label" type="text" value="{{ old('category_label') }}" placeholder="Evenement">
                    @error('category_label')
                        <p class="adm-gallery-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="adm-gallery-field-card">
                    <label class="adm-gallery-form-label" for="cursor_label">Libelle de survol</label>
                    <input class="tt-form-control" id="cursor_label" name="cursor_label" type="text" value="{{ old('cursor_label', 'Voir') }}" placeholder="Voir">
                    @error('cursor_label')
                        <p class="adm-gallery-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>
    </div>

    <aside class="adm-gallery-form-side">
        <section class="adm-gallery-section-card">
            <div class="adm-gallery-section-head">
                <div>
                    <span class="adm-gallery-section-eyebrow">Preview</span>
                    <h3>Verifier avant publication</h3>
                </div>
                <p>Controlez le cadrage et le type de media sans quitter le formulaire.</p>
            </div>

            <div class="adm-gallery-upload-preview" data-gallery-upload-preview>
                <div class="adm-gallery-upload-placeholder">
                    <span class="adm-gallery-upload-placeholder-tag">Previsualisation</span>
                    <strong>Le media apparaitra ici</strong>
                    <p>Selectionnez un fichier pour verifier le rendu dans l'admin avant l'enregistrement.</p>
                </div>
            </div>

            <div class="adm-gallery-preview-caption">
                <strong data-gallery-upload-name>Aucun fichier selectionne</strong>
                <span data-gallery-upload-meta>JPG, PNG, WebP, AVIF, GIF, MP4, WebM</span>
            </div>
        </section>

        <section class="adm-gallery-section-card">
            <div class="adm-gallery-section-head">
                <div>
                    <span class="adm-gallery-section-eyebrow">Publication</span>
                    <h3>Ordre et visibilite</h3>
                </div>
                <p>Choisissez la position publique et preparez une mise en ligne immediate ou planifiee.</p>
            </div>

            <div class="adm-gallery-field-grid">
                <div class="adm-gallery-field-card">
                    <label class="adm-gallery-form-label" for="sort_order">Ordre</label>
                    <input class="tt-form-control" id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', 0) }}">
                    <p class="adm-gallery-inline-help">Plus la valeur est basse, plus la photo remonte dans la galerie.</p>
                    @error('sort_order')
                        <p class="adm-gallery-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="adm-gallery-field-card">
                    <label class="adm-gallery-form-label" for="published_at">Publication</label>
                    <input class="tt-form-control" id="published_at" name="published_at" type="datetime-local" value="{{ old('published_at') }}">
                    <p class="adm-gallery-inline-help">Laisser vide pour publier des que le media est actif.</p>
                    @error('published_at')
                        <p class="adm-gallery-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="adm-gallery-toggle-card">
                <div>
                    <span class="adm-gallery-form-label">Statut</span>
                    <strong>Activer des la publication</strong>
                    <p>Desactivez si vous souhaitez preparer le media avant diffusion.</p>
                </div>

                <label class="adm-gallery-switch" for="is_active">
                    <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', true))>
                    <span>Actif</span>
                </label>
            </div>
        </section>

        <section class="adm-gallery-section-card">
            <div class="adm-gallery-section-head">
                <div>
                    <span class="adm-gallery-section-eyebrow">Actions</span>
                    <h3>Finaliser</h3>
                </div>
                <p>Enregistrez la photo maintenant ou refermez le panneau pour revenir a la bibliotheque.</p>
            </div>

            <div class="adm-gallery-form-actions">
                <button class="tt-btn tt-btn-primary tt-magnetic-item adm-gallery-btn" type="submit">
                    <span>Enregistrer la photo</span>
                </button>

                <button class="tt-btn tt-btn-outline tt-magnetic-item adm-gallery-btn" type="button" data-gallery-close-détails>
                    <span>Fermer</span>
                </button>
            </div>
        </section>
    </aside>
</form>
