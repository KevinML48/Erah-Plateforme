<div id="tt-blog-post-comments" class="{{ $panelClass ?? 'clip-comments-inline' }}">
    <h4 class="tt-bpc-heading">{{ $comments->total() }} commentaire(s)</h4>

    @if(($comments ?? null) && $comments->count())
        <ul class="clip-comments-list">
            @foreach($comments as $comment)
                @php
                    $commentAuthor = $comment->user?->name ?? 'Utilisateur';
                    $avatarUrl = $comment->user?->avatar_url ?? '/template/assets/img/blog/avatar.png';
                    $isSupporterComment = (int) ($comment->supporter_priority ?? 0) === 1;
                    $publicProfileUrl = $comment->user ? route('users.public', $comment->user) : null;
                @endphp
                <li class="clip-comment-item {{ $isSupporterComment ? 'is-supporter' : '' }}">
                    @if($publicProfileUrl)
                        <a href="{{ $publicProfileUrl }}" class="clip-comment-avatar" title="Voir le profil public de {{ $commentAuthor }}">
                            <img src="{{ $avatarUrl }}" loading="lazy" alt="{{ $commentAuthor }}">
                        </a>
                    @else
                        <span class="clip-comment-avatar">
                            <img src="{{ $avatarUrl }}" loading="lazy" alt="{{ $commentAuthor }}">
                        </span>
                    @endif
                    <div>
                        <div class="clip-comment-header">
                            <div class="clip-comment-author">
                                @if($publicProfileUrl)
                                    <h4 class="clip-comment-author-name">
                                        <a href="{{ $publicProfileUrl }}">{{ $commentAuthor }}</a>
                                    </h4>
                                @else
                                    <h4 class="clip-comment-author-name">{{ $commentAuthor }}</h4>
                                @endif
                                @if($isSupporterComment)
                                    <span class="clip-comment-badge">Supporter</span>
                                @endif
                            </div>
                            <div class="clip-comment-meta">
                                <span>{{ optional($comment->created_at)->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>

                        @auth
                            @if(auth()->id() === $comment->user_id || auth()->user()?->role === 'admin')
                                <div class="margin-bottom-10">
                                    <form method="POST" action="{{ route($commentDeleteRouteName, [$clip->id, $comment->id]) }}" class="tt-comment-delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="clip-comment-delete">Supprimer</button>
                                    </form>
                                </div>
                            @endif
                        @endauth

                        <div class="clip-comment-body-text">
                            <p>{!! nl2br(e((string) $comment->body)) !!}</p>
                        </div>
                        @if($isSupporterComment)
                            <div class="clip-comment-meta margin-top-10">
                                <span>Commentaire prioritaire</span>
                            </div>
                        @endif

                        @if(($comment->replies ?? collect())->count())
                            <div class="margin-top-20">
                                @foreach($comment->replies as $reply)
                                    @php
                                        $replyAuthor = $reply->user?->name ?? 'Utilisateur';
                                        $replyProfileUrl = $reply->user ? route('users.public', $reply->user) : null;
                                    @endphp
                                    <div class="clip-comment-item margin-top-15">
                                        <div>
                                            <div class="clip-comment-header">
                                                <div class="clip-comment-author">
                                                    @if($replyProfileUrl)
                                                        <h4 class="clip-comment-author-name">
                                                            <a href="{{ $replyProfileUrl }}">{{ $replyAuthor }}</a>
                                                        </h4>
                                                    @else
                                                        <h4 class="clip-comment-author-name">{{ $replyAuthor }}</h4>
                                                    @endif
                                                    <span class="clip-comment-badge">Reponse</span>
                                                </div>
                                                <div class="clip-comment-meta">
                                                    <span>{{ optional($reply->created_at)->format('d/m/Y H:i') }}</span>
                                                </div>
                                            </div>
                                            <div class="clip-comment-body-text">
                                                <p>{!! nl2br(e((string) $reply->body)) !!}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @auth
                            <form method="POST" action="{{ route($commentStoreRouteName, $clip->id) }}" class="margin-top-20">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                <div class="tt-form-group">
                                    <label for="reply_{{ $comment->id }}">Repondre</label>
                                    <textarea class="tt-form-control" id="reply_{{ $comment->id }}" name="body" rows="3" maxlength="2000"></textarea>
                                </div>
                                <button type="submit" class="tt-btn tt-btn-outline margin-top-10">
                                    <span data-hover="Publier la reponse">Publier la reponse</span>
                                </button>
                            </form>
                        @endauth
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="margin-top-40">
            {{ $comments->links() }}
        </div>
    @else
        <p class="margin-top-40">Aucun commentaire.</p>
    @endif

    @auth
        <form id="tt-post-comment-form" method="POST" action="{{ route($commentStoreRouteName, $clip->id) }}" class="margin-top-40">
            @csrf
            <h4 class="tt-post-comment-form-heading">Ajouter un commentaire:</h4>
            <small class="tt-form-text">Tout utilisateur connecte peut commenter. Les supporters sont affiches en priorite.</small>
            <br>
            <br>

            <div class="tt-form-group">
                <label for="body">Commentaire <span class="required">*</span></label>
                <textarea class="tt-form-control" id="body" name="body" rows="6" required>{{ old('body') }}</textarea>
            </div>

            <button type="submit" class="tt-btn tt-btn-primary margin-top-30">
                <span data-hover="Publier">Publier</span>
            </button>
        </form>
    @else
        <p class="tt-form-text clip-comments-note margin-top-40">Les commentaires sont visibles publiquement. Connectez-vous pour publier votre message.</p>
    @endauth
</div>
