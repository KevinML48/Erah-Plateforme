import { Link } from '@inertiajs/react';
import { useMemo, useState } from 'react';

import Badge from '../../Components/Badge';
import EmptyState from '../../Components/EmptyState';
import ListItem from '../../Components/ListItem';
import Panel from '../../Components/Panel';
import Tabs from '../../Components/Tabs';
import GameLayout from '../../Layouts/GameLayout';
import useApiData from '../../lib/useApiData';
import { formatDate, formatNumber } from '../../lib/format';

const tabItems = [
    { label: 'En cours', value: 'pending' },
    { label: 'Regles', value: 'settled' },
];

export default function BetsIndex() {
    const [tab, setTab] = useState('pending');
    const betsQuery = useApiData('/bets/me?limit=100');

    const rows = useMemo(() => {
        const list = betsQuery.data ?? [];
        if (tab === 'pending') {
            return list.filter((item) => ['pending', 'placed'].includes(item.status));
        }
        return list.filter((item) => ['won', 'lost', 'void', 'cancelled'].includes(item.status));
    }, [betsQuery.data, tab]);

    return (
        <GameLayout title="Mes paris">
            <div className="space-y-5">
                <Panel title="Historique paris" subtitle="Suivi des bets actifs et regles">
                    <div className="space-y-3">
                        <Tabs items={tabItems} active={tab} onChange={setTab} />
                        {betsQuery.loading && <p className="text-sm text-muted">Chargement...</p>}
                        {betsQuery.error && <p className="text-sm text-red-300">{betsQuery.error}</p>}

                        {!betsQuery.loading && !betsQuery.error && rows.length === 0 && (
                            <EmptyState title="Aucun pari" description="Aucun element pour cet onglet." />
                        )}

                        {!betsQuery.loading && !betsQuery.error && rows.length > 0 && (
                            <div className="space-y-2">
                                {rows.map((bet) => (
                                    <ListItem
                                        key={bet.id}
                                        title={`${bet.match?.home_team ?? 'Team A'} vs ${bet.match?.away_team ?? 'Team B'}`}
                                        meta={`${formatDate(bet.placed_at)} - ${bet.status}`}
                                        action={<Link href={`/app/matches/${bet.match_id}`} className="hud-btn-secondary px-3 py-1.5 text-xs">Match</Link>}
                                    >
                                        <div className="mt-2 flex flex-wrap gap-2">
                                            <Badge variant="status">Mise {formatNumber(bet.stake_points)} bp</Badge>
                                            <Badge variant="status">Potentiel {formatNumber(bet.potential_payout)} bp</Badge>
                                            {bet.settlement_points !== null && (
                                                <Badge variant={bet.settlement_points > 0 ? 'success' : 'status'}>
                                                    Resultat {formatNumber(bet.settlement_points)} bp
                                                </Badge>
                                            )}
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
