import Badge from '../../Components/Badge';
import EmptyState from '../../Components/EmptyState';
import KpiCard from '../../Components/KpiCard';
import ListItem from '../../Components/ListItem';
import Panel from '../../Components/Panel';
import GameLayout from '../../Layouts/GameLayout';
import useApiData from '../../lib/useApiData';
import { formatDate, formatNumber } from '../../lib/format';

export default function WalletIndex() {
    const betsQuery = useApiData('/bets/me?limit=100');
    const rewardWalletQuery = useApiData('/me/reward-wallet');

    const placed = (betsQuery.data ?? []).reduce((sum, bet) => sum + (Number(bet.stake_points) || 0), 0);
    const settled = (betsQuery.data ?? []).reduce((sum, bet) => sum + (Number(bet.settlement_points) || 0), 0);

    return (
        <GameLayout title="Wallet">
            <div className="space-y-5">
                <Panel title="Wallet snapshot" subtitle="En attente de endpoint wallet detail">
                    <div className="grid gap-3 sm:grid-cols-3">
                        <KpiCard title="Mises totales" value={`${formatNumber(placed)} bp`} help="Somme des stakes visibles" />
                        <KpiCard title="Settlements" value={`${formatNumber(settled)} bp`} help="Gains/pertes visibles" />
                        <KpiCard title="Reward wallet" value={rewardWalletQuery.error ? 'N/A' : 'Endpoint'} help="`/api/me/reward-wallet`" />
                    </div>
                    <p className="mt-3 text-sm text-muted">
                        Endpoint `/api/me/wallet` manquant actuellement dans l'API exposee. Cette carte sera branchee des que disponible.
                    </p>
                    {rewardWalletQuery.error && (
                        <p className="mt-2 text-sm text-amber-300">Reward wallet API indisponible: {rewardWalletQuery.error}</p>
                    )}
                </Panel>

                <Panel title="Transactions derivees des paris" subtitle="Fallback temporaire depuis /api/bets/me">
                    {betsQuery.loading && <p className="text-sm text-muted">Chargement...</p>}
                    {betsQuery.error && <p className="text-sm text-red-300">{betsQuery.error}</p>}

                    {!betsQuery.loading && !betsQuery.error && (betsQuery.data ?? []).length === 0 && (
                        <EmptyState title="Aucune transaction" description="Aucun bet detecte." />
                    )}

                    {!betsQuery.loading && !betsQuery.error && (betsQuery.data ?? []).length > 0 && (
                        <div className="space-y-2">
                            {(betsQuery.data ?? []).map((bet) => (
                                <ListItem
                                    key={bet.id}
                                    title={`Bet #${bet.id}`}
                                    meta={`${formatDate(bet.placed_at)} - ${bet.status}`}
                                >
                                    <div className="mt-2 flex flex-wrap gap-2">
                                        <Badge variant="status">Stake -{formatNumber(bet.stake_points)} bp</Badge>
                                        {Number(bet.settlement_points) > 0 && (
                                            <Badge variant="success">Settlement +{formatNumber(bet.settlement_points)} bp</Badge>
                                        )}
                                    </div>
                                </ListItem>
                            ))}
                        </div>
                    )}
                </Panel>
            </div>
        </GameLayout>
    );
}
