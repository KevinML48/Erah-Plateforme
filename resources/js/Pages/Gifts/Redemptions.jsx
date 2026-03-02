import Badge from '../../Components/Badge';
import EmptyState from '../../Components/EmptyState';
import ListItem from '../../Components/ListItem';
import Panel from '../../Components/Panel';
import GameLayout from '../../Layouts/GameLayout';
import useApiData from '../../lib/useApiData';
import { formatDate, formatNumber } from '../../lib/format';

export default function GiftRedemptionsPage() {
    const redemptionsQuery = useApiData('/me/redemptions');

    return (
        <GameLayout title="Redemptions">
            <div className="space-y-5">
                <Panel title="Historique cadeaux">
                    {redemptionsQuery.loading && <p className="text-sm text-muted">Chargement...</p>}
                    {redemptionsQuery.error && (
                        <EmptyState title="Endpoint manquant" description={`Impossible de charger les redemptions (${redemptionsQuery.error}).`} />
                    )}

                    {!redemptionsQuery.loading && !redemptionsQuery.error && (redemptionsQuery.data ?? []).length === 0 && (
                        <EmptyState title="Aucune redemption" description="Aucune demande d'echange pour le moment." />
                    )}

                    {!redemptionsQuery.loading && !redemptionsQuery.error && (redemptionsQuery.data ?? []).length > 0 && (
                        <div className="space-y-2">
                            {(redemptionsQuery.data ?? []).map((item) => (
                                <ListItem
                                    key={item.id}
                                    title={item.gift?.title ?? `Gift #${item.gift_id}`}
                                    meta={formatDate(item.requested_at ?? item.created_at)}
                                    action={<Badge variant="status">{item.status ?? 'pending'}</Badge>}
                                >
                                    <div className="mt-2 flex flex-wrap gap-2">
                                        <Badge variant="status">Cost {formatNumber(item.cost_points_snapshot ?? 0)} rp</Badge>
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
