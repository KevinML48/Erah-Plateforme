import { Link, usePage } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';

import Badge from '../../Components/Badge';
import Button from '../../Components/Button';
import EmptyState from '../../Components/EmptyState';
import ListItem from '../../Components/ListItem';
import Panel from '../../Components/Panel';
import Tabs from '../../Components/Tabs';
import GameLayout from '../../Layouts/GameLayout';
import api, { apiErrorMessage } from '../../lib/api';
import { formatDate, formatNumber } from '../../lib/format';

const tabItems = [
    { label: 'Recents', value: 'recent' },
    { label: 'Populaires', value: 'popular' },
];

export default function ClipsIndex() {
    const page = usePage();
    const [sort, setSort] = useState('recent');
    const [clips, setClips] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [actionError, setActionError] = useState(null);

    const load = async (selectedSort) => {
        setLoading(true);
        setError(null);
        try {
            const response = await api.get('/clips', { params: { sort: selectedSort, limit: 30 } });
            setClips(response?.data?.data ?? []);
        } catch (err) {
            setError(apiErrorMessage(err));
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        load(sort);
    }, [sort]);

    const me = page.props.auth?.user;

    const interactionsDisabled = useMemo(() => !me, [me]);

    const onAction = async (clipId, action) => {
        setActionError(null);
        try {
            if (action === 'like') {
                await api.post(`/clips/${clipId}/like`);
            }
            if (action === 'favorite') {
                await api.post(`/clips/${clipId}/favorite`);
            }
            await load(sort);
        } catch (err) {
            setActionError(apiErrorMessage(err));
        }
    };

    return (
        <GameLayout
            title="Clips"
            subtitle="ToyCAD Library style"
            topTabs={[
                { label: 'ToyCAD Library', value: 'library' },
                { label: 'My Library', value: 'mine' },
            ]}
            topTabsActive="library"
        >
            <div className="space-y-5">
                <Panel title="Feed clips" subtitle="Highlights de la communaute" action={<Badge variant="status">{sort}</Badge>}>
                    <div className="space-y-3">
                        <Tabs items={tabItems} active={sort} onChange={setSort} />

                        {loading && <p className="text-sm text-muted">Chargement des clips...</p>}
                        {error && <p className="text-sm text-red-300">{error}</p>}
                        {actionError && <p className="text-sm text-red-300">{actionError}</p>}

                        {!loading && clips.length === 0 && (
                            <EmptyState title="Aucun clip" description="Le feed est vide pour le moment." />
                        )}

                        {!loading && clips.length > 0 && (
                            <div className="space-y-2">
                                {clips.map((clip) => (
                                    <ListItem
                                        key={clip.id}
                                        title={clip.title}
                                        meta={`${formatDate(clip.published_at)} - ${formatNumber(clip.likes_count)} likes`}
                                        action={
                                            <Link href={`/app/clips/${clip.slug}`} className="hud-btn-secondary px-3 py-1.5 text-xs">
                                                Voir
                                            </Link>
                                        }
                                    >
                                        <div className="mt-2 flex flex-wrap gap-2">
                                            <Button
                                                variant="secondary"
                                                className="px-3 py-1 text-xs"
                                                disabled={interactionsDisabled}
                                                onClick={() => onAction(clip.id, 'like')}
                                            >
                                                Like
                                            </Button>
                                            <Button
                                                variant="secondary"
                                                className="px-3 py-1 text-xs"
                                                disabled={interactionsDisabled}
                                                onClick={() => onAction(clip.id, 'favorite')}
                                            >
                                                Favori
                                            </Button>
                                            <Badge variant="status">{formatNumber(clip.favorites_count)} fav</Badge>
                                            <Badge variant="status">{formatNumber(clip.comments_count)} comments</Badge>
                                        </div>
                                    </ListItem>
                                ))}
                            </div>
                        )}
                    </div>
                </Panel>
            </div>
        </GameLayout>
    );
}
