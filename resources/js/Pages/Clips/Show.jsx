import { Link, useForm } from '@inertiajs/react';
import { useEffect, useState } from 'react';

import Badge from '../../Components/Badge';
import Button from '../../Components/Button';
import EmptyState from '../../Components/EmptyState';
import Input from '../../Components/Input';
import ListItem from '../../Components/ListItem';
import Panel from '../../Components/Panel';
import GameLayout from '../../Layouts/GameLayout';
import api, { apiErrorMessage } from '../../lib/api';
import { formatDate, formatNumber } from '../../lib/format';

export default function ClipShow({ slug }) {
    const [clip, setClip] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [actionError, setActionError] = useState(null);

    const commentForm = useForm({ body: '' });

    const load = async () => {
        setLoading(true);
        setError(null);
        try {
            const response = await api.get(`/clips/${slug}`);
            setClip(response?.data?.data ?? null);
        } catch (err) {
            setError(apiErrorMessage(err));
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        load();
    }, [slug]);

    const doAction = async (action) => {
        if (!clip) {
            return;
        }
        setActionError(null);
        try {
            if (action === 'like') {
                await api.post(`/clips/${clip.id}/like`);
            }
            if (action === 'favorite') {
                await api.post(`/clips/${clip.id}/favorite`);
            }
            if (action === 'share') {
                await api.post(`/clips/${clip.id}/share`);
            }
            await load();
        } catch (err) {
            setActionError(apiErrorMessage(err));
        }
    };

    const submitComment = async (event) => {
        event.preventDefault();
        if (!clip || !commentForm.data.body.trim()) {
            return;
        }

        commentForm.clearErrors();

        try {
            await api.post(`/clips/${clip.id}/comments`, {
                body: commentForm.data.body.trim(),
            });
            commentForm.setData('body', '');
            await load();
        } catch (err) {
            commentForm.setError('body', apiErrorMessage(err));
        }
    };

    return (
        <GameLayout title="Detail clip">
            <div className="space-y-5">
                <Panel title="Retour clips" hover={false}>
                    <Link href="/app/clips" className="hud-btn-secondary px-3 py-1.5 text-xs">
                        Retour au feed
                    </Link>
                </Panel>

                {loading && <Panel title="Chargement..." />}
                {error && <Panel title="Erreur"><p className="text-sm text-red-300">{error}</p></Panel>}

                {!loading && !error && !clip && (
                    <Panel title="Clip introuvable">
                        <EmptyState title="Aucun clip" description="Ce clip n'existe pas ou n'est pas publie." />
                    </Panel>
                )}

                {!loading && !error && clip && (
                    <>
                        <Panel title={clip.title} subtitle={clip.description ?? 'Sans description'}>
                            <div className="space-y-3">
                                <div className="overflow-hidden rounded-hud border border-erah-border/12 bg-black">
                                    {clip.video_url ? (
                                        <div className="aspect-video w-full">
                                            <iframe
                                                className="h-full w-full"
                                                src={clip.video_url}
                                                title={clip.title}
                                                allowFullScreen
                                            />
                                        </div>
                                    ) : (
                                        <div className="flex aspect-video items-center justify-center text-sm text-muted">
                                            Aucune video configuree.
                                        </div>
                                    )}
                                </div>

                                <div className="flex flex-wrap gap-2">
                                    <Button variant="secondary" onClick={() => doAction('like')}>Like</Button>
                                    <Button variant="secondary" onClick={() => doAction('favorite')}>Favori</Button>
                                    <Button variant="secondary" onClick={() => doAction('share')}>Share</Button>
                                </div>

                                {actionError && <p className="text-sm text-red-300">{actionError}</p>}

                                <div className="flex flex-wrap gap-2">
                                    <Badge variant="status">{formatNumber(clip.likes_count)} likes</Badge>
                                    <Badge variant="status">{formatNumber(clip.favorites_count)} favoris</Badge>
                                    <Badge variant="status">{formatNumber(clip.comments_count)} comments</Badge>
                                    <Badge variant="status">{formatDate(clip.published_at)}</Badge>
                                </div>
                            </div>
                        </Panel>

                        <Panel title="Commentaires">
                            <form className="space-y-2" onSubmit={submitComment}>
                                <Input
                                    label="Ajouter un commentaire"
                                    value={commentForm.data.body}
                                    onChange={(event) => commentForm.setData('body', event.target.value)}
                                    error={commentForm.errors.body}
                                />
                                <Button type="submit" disabled={commentForm.processing}>Publier</Button>
                            </form>

                            <div className="mt-4 space-y-2">
                                {(clip.comments ?? []).length === 0 && (
                                    <EmptyState title="Aucun commentaire" description="Sois le premier a commenter ce clip." />
                                )}
                                {(clip.comments ?? []).map((comment) => (
                                    <ListItem
                                        key={comment.id}
                                        title={comment.user?.name ?? 'User'}
                                        meta={formatDate(comment.created_at)}
                                    >
                                        <p className="mt-1 text-sm text-muted-strong">{comment.body}</p>
                                    </ListItem>
                                ))}
                            </div>
                        </Panel>
                    </>
                )}
            </div>
        </GameLayout>
    );
}
