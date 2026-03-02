import { useEffect, useMemo, useState } from 'react';
import { usePage } from '@inertiajs/react';

import Badge from '../../Components/Badge';
import Button from '../../Components/Button';
import Chip from '../../Components/Chip';
import EmptyState from '../../Components/EmptyState';
import Panel from '../../Components/Panel';
import ProgressBar from '../../Components/ProgressBar';
import Select from '../../Components/Select';
import Skeleton from '../../Components/Skeleton';
import GameLayout from '../../Layouts/GameLayout';
import api, { apiErrorMessage } from '../../lib/api';
import { formatNumber } from '../../lib/format';

function rankTone(position) {
    if (position === 1) {
        return 'bg-amber-400/20 text-amber-200 border-amber-300/40';
    }
    if (position === 2) {
        return 'bg-slate-300/20 text-slate-100 border-slate-300/35';
    }
    if (position === 3) {
        return 'bg-orange-500/20 text-orange-200 border-orange-300/35';
    }
    return 'bg-erah-surface text-muted-strong border-erah-border/15';
}

export default function LeaderboardsIndex() {
    const page = usePage();
    const currentUserId = page.props?.auth?.user?.id ?? null;

    const [leagues, setLeagues] = useState([]);
    const [selectedLeague, setSelectedLeague] = useState('');
    const [leaderboard, setLeaderboard] = useState(null);
    const [loadingBootstrap, setLoadingBootstrap] = useState(true);
    const [loadingLeaderboard, setLoadingLeaderboard] = useState(false);
    const [error, setError] = useState(null);

    useEffect(() => {
        const loadBootstrap = async () => {
            setLoadingBootstrap(true);
            setError(null);
            try {
                const leaguesResponse = await api.get('/leagues');
                const leaguesData = leaguesResponse?.data?.data ?? [];

                setLeagues(leaguesData);

                const defaultKey = leaguesData[0]?.key ?? '';
                setSelectedLeague(defaultKey);
            } catch (err) {
                setError(apiErrorMessage(err));
            } finally {
                setLoadingBootstrap(false);
            }
        };

        loadBootstrap();
    }, []);

    const loadLeaderboard = async (leagueKey) => {
        if (!leagueKey) {
            return;
        }

        setLoadingLeaderboard(true);
        setError(null);

        try {
            const response = await api.get(`/leagues/${leagueKey}/leaderboard`, { params: { limit: 100 } });
            setLeaderboard(response?.data ?? null);
        } catch (err) {
            setLeaderboard(null);
            setError(apiErrorMessage(err));
        } finally {
            setLoadingLeaderboard(false);
        }
    };

    useEffect(() => {
        loadLeaderboard(selectedLeague);
    }, [selectedLeague]);

    const entries = leaderboard?.entries ?? [];
    const podium = entries.slice(0, 3);
    const others = entries.slice(3);
    const myRow = entries.find((entry) => entry.user_id === currentUserId) ?? null;

    const myLeague = useMemo(
        () => leagues.find((league) => league.key === selectedLeague) ?? null,
        [leagues, selectedLeague],
    );
    const myLeagueMin = myLeague?.min_rank_points ?? 0;
    const myPoints = myRow?.total_rank_points ?? 0;
    const nextTarget = myLeagueMin + 500;
    const pointsInCurrentStep = Math.max(0, myPoints - myLeagueMin);
    const stepPercent = Math.max(0, Math.min(100, Math.round((pointsInCurrentStep / 500) * 100)));
    const pointsRemaining = Math.max(0, nextTarget - myPoints);

    const selectedLeagueMeta = leagues.find((league) => league.key === selectedLeague) ?? null;

    return (
        <GameLayout
            title="Classement"
            subtitle="Ligue et progression"
            topTabs={[
                { label: 'Start', value: 'start' },
                { label: 'My Recent', value: 'recent' },
            ]}
            topTabsActive="start"
        >
            <div className="space-y-5">
                <Panel
                    title="Vue ranking"
                    subtitle="Ta progression et le classement de ligue en temps reel."
                    action={
                        <Button variant="secondary" className="px-3 py-1.5 text-xs" onClick={() => loadLeaderboard(selectedLeague)}>
                            Rafraichir
                        </Button>
                    }
                >
                    {loadingBootstrap ? (
                        <div className="grid gap-3 md:grid-cols-3">
                            <Skeleton className="h-24" />
                            <Skeleton className="h-24" />
                            <Skeleton className="h-24" />
                        </div>
                    ) : (
                        <div className="grid gap-3 md:grid-cols-3">
                            <article className="rounded-hud border border-erah-border/12 bg-erah-surface p-3">
                                <p className="text-xs uppercase tracking-[0.16em] text-muted">Ligue affichee</p>
                                <div className="mt-2 flex items-center gap-2">
                                    <Badge variant="league">{selectedLeagueMeta?.name ?? 'Aucune'}</Badge>
                                </div>
                            </article>

                            <article className="rounded-hud border border-erah-border/12 bg-erah-surface p-3">
                                <p className="text-xs uppercase tracking-[0.16em] text-muted">Points classement</p>
                                <p className="mt-2 font-display text-3xl font-bold">{myRow ? formatNumber(myPoints) : '-'}</p>
                            </article>

                            <article className="rounded-hud border border-erah-border/12 bg-erah-surface p-3">
                                <p className="text-xs uppercase tracking-[0.16em] text-muted">Ta position ici</p>
                                <p className="mt-2 font-display text-3xl font-bold">{myRow ? `#${myRow.position}` : '-'}</p>
                            </article>
                        </div>
                    )}

                    {!loadingBootstrap && (
                        <div className="mt-4">
                            <ProgressBar
                                value={myRow ? stepPercent : 0}
                                max={100}
                                label={
                                    myRow
                                        ? `Prochaine ligue: ${formatNumber(pointsRemaining)} pts restants`
                                        : 'Parie/commente/joue pour entrer dans ce classement.'
                                }
                            />
                        </div>
                    )}
                </Panel>

                <Panel title="Filtrer une ligue" subtitle="Selection rapide des classements">
                    <div className="space-y-3">
                        <div className="flex gap-2 overflow-x-auto pb-1">
                            {leagues.map((league) => (
                                <Chip
                                    key={league.id}
                                    active={selectedLeague === league.key}
                                    onClick={() => setSelectedLeague(league.key)}
                                >
                                    {league.name}
                                </Chip>
                            ))}
                        </div>

                        <Select
                            label="Ligue (liste complete)"
                            value={selectedLeague}
                            onChange={(event) => setSelectedLeague(event.target.value)}
                        >
                            {leagues.map((league) => (
                                <option key={league.id} value={league.key}>
                                    {league.name}
                                </option>
                            ))}
                        </Select>
                    </div>
                </Panel>

                <Panel
                    title="Podium"
                    subtitle={selectedLeagueMeta ? `${selectedLeagueMeta.name} - Top 3 joueurs` : 'Top 3 joueurs'}
                >
                    {loadingLeaderboard && (
                        <div className="grid gap-3 md:grid-cols-3">
                            <Skeleton className="h-28" />
                            <Skeleton className="h-28" />
                            <Skeleton className="h-28" />
                        </div>
                    )}

                    {!loadingLeaderboard && error && <p className="text-sm text-red-300">{error}</p>}

                    {!loadingLeaderboard && !error && podium.length === 0 && (
                        <EmptyState title="Aucune entree" description="Le leaderboard de cette ligue est vide." />
                    )}

                    {!loadingLeaderboard && !error && podium.length > 0 && (
                        <div className="grid gap-3 md:grid-cols-3">
                            {podium.map((entry) => (
                                <article
                                    key={entry.user_id}
                                    className={[
                                        'rounded-hud border p-4',
                                        rankTone(entry.position),
                                        entry.user_id === currentUserId ? 'ring-1 ring-erah-red/60' : '',
                                    ].join(' ')}
                                >
                                    <div className="flex items-center justify-between">
                                        <span className="text-xs uppercase tracking-[0.16em]">Rang</span>
                                        <span className="font-display text-xl font-bold">#{entry.position}</span>
                                    </div>
                                    <p className="mt-3 truncate text-base font-semibold">{entry.name ?? 'User'}</p>
                                    <p className="mt-1 text-sm text-muted-strong">{formatNumber(entry.total_rank_points)} pts</p>
                                    <p className="text-xs text-muted">XP: {formatNumber(entry.total_xp)}</p>
                                </article>
                            ))}
                        </div>
                    )}
                </Panel>

                <Panel title="Classement complet" subtitle="Tous les joueurs de la ligue selectionnee">
                    {loadingLeaderboard && (
                        <div className="space-y-2">
                            {Array.from({ length: 6 }).map((_, index) => (
                                <Skeleton key={index} className="h-16" />
                            ))}
                        </div>
                    )}

                    {!loadingLeaderboard && !error && entries.length === 0 && (
                        <EmptyState title="Aucun joueur classe" description="Aucune donnee de classement pour cette ligue." />
                    )}

                    {!loadingLeaderboard && !error && entries.length > 0 && (
                        <div className="space-y-2">
                            {entries.map((entry) => (
                                <article
                                    key={entry.user_id}
                                    className={[
                                        'rounded-hud border bg-erah-surface px-3 py-3',
                                        entry.user_id === currentUserId ? 'border-erah-red/45 ring-1 ring-erah-red/35' : 'border-erah-border/12',
                                    ].join(' ')}
                                >
                                    <div className="flex items-center gap-3">
                                        <span className={`inline-flex h-8 w-8 items-center justify-center rounded-full border text-sm font-bold ${rankTone(entry.position)}`}>
                                            {entry.position}
                                        </span>

                                        <div className="min-w-0 flex-1">
                                            <div className="flex items-center gap-2">
                                                <p className="truncate text-sm font-semibold">{entry.name ?? 'User'}</p>
                                                {entry.user_id === currentUserId && <Badge variant="league">Toi</Badge>}
                                            </div>
                                            <p className="text-xs text-muted">XP: {formatNumber(entry.total_xp)}</p>
                                        </div>

                                        <div className="text-right">
                                            <p className="font-display text-lg font-bold">{formatNumber(entry.total_rank_points)}</p>
                                            <p className="text-xs text-muted">pts</p>
                                        </div>
                                    </div>
                                </article>
                            ))}

                            {others.length > 0 && (
                                <p className="pt-1 text-xs text-muted">
                                    {formatNumber(entries.length)} joueurs affiches dans cette ligue.
                                </p>
                            )}
                        </div>
                    )}
                </Panel>
            </div>
        </GameLayout>
    );
}
