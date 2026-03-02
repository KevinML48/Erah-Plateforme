import { Link, router } from '@inertiajs/react';
import { useMemo, useState } from 'react';

import Badge from '../../Components/Badge';
import Button from '../../Components/Button';
import EmptyState from '../../Components/EmptyState';
import Input from '../../Components/Input';
import Panel from '../../Components/Panel';
import GameLayout from '../../Layouts/GameLayout';
import api, { apiErrorMessage } from '../../lib/api';
import useApiData from '../../lib/useApiData';
import { formatDate, formatNumber } from '../../lib/format';

const predictionOptions = [
    { value: 'home', label: 'Team A' },
    { value: 'away', label: 'Team B' },
    { value: 'draw', label: 'Draw' },
];

const uniqueKey = (prefix) => `${prefix}-${Date.now()}-${Math.random().toString(16).slice(2, 10)}`;

export default function MatchShow({ matchId }) {
    const [prediction, setPrediction] = useState('home');
    const [stake, setStake] = useState('100');
    const [actionError, setActionError] = useState(null);
    const [actionSuccess, setActionSuccess] = useState(null);
    const [busy, setBusy] = useState(false);

    const matchesQuery = useApiData('/matches?limit=100');
    const myBetsQuery = useApiData('/bets/me?limit=100');

    const match = useMemo(() => {
        const rows = matchesQuery.data ?? [];
        return rows.find((item) => Number(item.id) === Number(matchId)) ?? null;
    }, [matchesQuery.data, matchId]);

    const myBet = useMemo(() => {
        const rows = myBetsQuery.data ?? [];
        return rows.find((item) => Number(item.match_id) === Number(matchId)) ?? null;
    }, [myBetsQuery.data, matchId]);

    const placeBet = async () => {
        if (!match) {
            return;
        }

        const stakePoints = Number.parseInt(stake, 10);
        if (!Number.isFinite(stakePoints) || stakePoints < 1) {
            setActionError('La mise doit etre superieure a 0.');
            return;
        }

        setBusy(true);
        setActionError(null);
        setActionSuccess(null);
        try {
            await api.post('/bets', {
                match_id: match.id,
                prediction,
                stake_points: stakePoints,
                idempotency_key: uniqueKey('app-bet'),
            });
            setActionSuccess('Pari place avec succes.');
            await myBetsQuery.reload();
        } catch (err) {
            setActionError(apiErrorMessage(err));
        } finally {
            setBusy(false);
        }
    };

    const cancelBet = async () => {
        if (!myBet) {
            return;
        }

        setBusy(true);
        setActionError(null);
        setActionSuccess(null);

        router.delete(`/bets/${myBet.id}`, {
            data: { idempotency_key: uniqueKey('app-cancel') },
            preserveScroll: true,
            onSuccess: async () => {
                setActionSuccess('Pari annule.');
                await myBetsQuery.reload();
                setBusy(false);
            },
            onError: () => {
                setActionError('Annulation impossible.');
                setBusy(false);
            },
        });
    };

    return (
        <GameLayout title="Detail match">
            <div className="space-y-5">
                <Panel hover={false}>
                    <Link href="/app/matches" className="hud-btn-secondary px-3 py-1.5 text-xs">
                        Retour aux matchs
                    </Link>
                </Panel>

                {(matchesQuery.loading || myBetsQuery.loading) && (
                    <Panel title="Chargement..." />
                )}

                {(matchesQuery.error || myBetsQuery.error) && (
                    <Panel title="Erreur">
                        <p className="text-sm text-red-300">{matchesQuery.error || myBetsQuery.error}</p>
                    </Panel>
                )}

                {!matchesQuery.loading && !matchesQuery.error && !match && (
                    <Panel title="Match introuvable">
                        <EmptyState title="Match non disponible" description="Aucun detail pour ce match." />
                    </Panel>
                )}

                {match && (
                    <>
                        <Panel
                            title={`${match.home_team ?? match.team_a_name ?? 'Team A'} vs ${match.away_team ?? match.team_b_name ?? 'Team B'}`}
                            subtitle={formatDate(match.starts_at)}
                        >
                            <div className="flex flex-wrap gap-2">
                                <Badge variant="status">{match.status}</Badge>
                                <Badge variant="status">{match.match_key ?? 'esport'}</Badge>
                                {match.result && <Badge variant="league">Result: {match.result}</Badge>}
                            </div>
                        </Panel>

                        <Panel title="Parier sur le vainqueur" subtitle="Market WINNER (version actuelle)">
                            <div className="space-y-3">
                                <div className="grid gap-2 sm:grid-cols-3">
                                    {predictionOptions.map((option) => (
                                        <button
                                            key={option.value}
                                            type="button"
                                            className={[
                                                'rounded-hud border px-3 py-3 text-sm font-semibold transition',
                                                prediction === option.value
                                                    ? 'border-erah-red/50 bg-erah-red/20'
                                                    : 'border-erah-border/12 bg-erah-surface hover:border-erah-red/35',
                                            ].join(' ')}
                                            onClick={() => setPrediction(option.value)}
                                        >
                                            {option.label === 'Team A'
                                                ? (match.home_team ?? match.team_a_name ?? 'Team A')
                                                : option.label === 'Team B'
                                                    ? (match.away_team ?? match.team_b_name ?? 'Team B')
                                                    : 'Draw'}
                                        </button>
                                    ))}
                                </div>

                                <Input
                                    label="Mise (bet_points)"
                                    value={stake}
                                    onChange={(event) => setStake(event.target.value)}
                                    type="number"
                                    min={1}
                                />

                                <div className="flex flex-wrap gap-2">
                                    <Button type="button" disabled={busy || Boolean(myBet)} onClick={placeBet}>
                                        {busy ? 'Traitement...' : myBet ? 'Pari deja place' : 'Valider mon pari'}
                                    </Button>
                                    {myBet && (
                                        <Button type="button" variant="secondary" disabled={busy} onClick={cancelBet}>
                                            Annuler mon pari
                                        </Button>
                                    )}
                                </div>

                                {actionError && <p className="text-sm text-red-300">{actionError}</p>}
                                {actionSuccess && <p className="text-sm text-emerald-300">{actionSuccess}</p>}
                            </div>
                        </Panel>

                        <Panel title="Mon pari">
                            {!myBet && (
                                <EmptyState title="Aucun pari actif" description="Place ton premier pari sur ce match." />
                            )}
                            {myBet && (
                                <div className="space-y-2 text-sm">
                                    <p>Prediction: <span className="font-semibold">{myBet.prediction}</span></p>
                                    <p>Mise: <span className="font-semibold">{formatNumber(myBet.stake_points)} bp</span></p>
                                    <p>Potentiel: <span className="font-semibold">{formatNumber(myBet.potential_payout)} bp</span></p>
                                    <p>Statut: <span className="font-semibold">{myBet.status}</span></p>
                                </div>
                            )}
                        </Panel>
                    </>
                )}
            </div>
        </GameLayout>
    );
}
