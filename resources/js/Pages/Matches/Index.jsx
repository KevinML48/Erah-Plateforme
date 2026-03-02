import { Link } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';

import Badge from '../../Components/Badge';
import EmptyState from '../../Components/EmptyState';
import ListItem from '../../Components/ListItem';
import Panel from '../../Components/Panel';
import Tabs from '../../Components/Tabs';
import GameLayout from '../../Layouts/GameLayout';
import api, { apiErrorMessage } from '../../lib/api';
import { formatDate } from '../../lib/format';

const tabItems = [
    { label: 'A venir', value: 'scheduled' },
    { label: 'Live', value: 'live' },
    { label: 'Termines', value: 'finished' },
];

export default function MatchesIndex() {
    const [status, setStatus] = useState('scheduled');
    const [rows, setRows] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    const load = async (selectedStatus) => {
        setLoading(true);
        setError(null);
        try {
            const response = await api.get('/matches', { params: { status: selectedStatus, limit: 50 } });
            setRows(response?.data?.data ?? []);
        } catch (err) {
            setError(apiErrorMessage(err));
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        load(status);
    }, [status]);

    const sortedRows = useMemo(() => {
        const cloned = [...rows];
        if (status === 'scheduled') {
            return cloned.sort((a, b) => new Date(a.starts_at) - new Date(b.starts_at));
        }
        return cloned.sort((a, b) => new Date(b.starts_at) - new Date(a.starts_at));
    }, [rows, status]);

    return (
        <GameLayout
            title="Matchs"
            subtitle="Board paris en tuiles"
            topTabs={[
                { label: 'Start', value: 'start' },
                { label: 'My Recent', value: 'recent' },
            ]}
            topTabsActive="start"
        >
            <div className="space-y-5">
                <Panel title="Match feed" subtitle="Suivi des matchs esports et entree vers les paris">
                    <div className="space-y-3">
                        <Tabs items={tabItems} active={status} onChange={setStatus} />
                        {loading && <p className="text-sm text-muted">Chargement des matchs...</p>}
                        {error && <p className="text-sm text-red-300">{error}</p>}

                        {!loading && !error && sortedRows.length === 0 && (
                            <EmptyState title="Aucun match" description="Aucun match disponible pour ce statut." />
                        )}

                        {!loading && !error && sortedRows.length > 0 && (
                            <div className="space-y-2">
                                {sortedRows.map((match) => (
                                    <ListItem
                                        key={match.id}
                                        title={`${match.home_team ?? match.team_a_name ?? 'Team A'} vs ${match.away_team ?? match.team_b_name ?? 'Team B'}`}
                                        meta={`${formatDate(match.starts_at)} - ${match.status}`}
                                        action={
                                            <Link href={`/app/matches/${match.id}`} className="hud-btn-secondary px-3 py-1.5 text-xs">
                                                Voir
                                            </Link>
                                        }
                                    >
                                        <div className="mt-1">
                                            <Badge variant="status">{match.match_key ?? 'esport'}</Badge>
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
