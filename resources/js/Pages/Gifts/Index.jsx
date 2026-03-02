import { Link } from '@inertiajs/react';
import { useState } from 'react';

import Badge from '../../Components/Badge';
import Button from '../../Components/Button';
import EmptyState from '../../Components/EmptyState';
import ListItem from '../../Components/ListItem';
import Panel from '../../Components/Panel';
import GameLayout from '../../Layouts/GameLayout';
import api, { apiErrorMessage } from '../../lib/api';
import useApiData from '../../lib/useApiData';
import { formatNumber } from '../../lib/format';

const key = (prefix) => `${prefix}-${Date.now()}-${Math.random().toString(16).slice(2, 10)}`;

export default function GiftsIndex() {
    const giftsQuery = useApiData('/gifts');
    const [busyId, setBusyId] = useState(null);
    const [actionError, setActionError] = useState(null);

    const redeem = async (giftId) => {
        setBusyId(giftId);
        setActionError(null);
        try {
            await api.post(`/gifts/${giftId}/redeem`, { idempotency_key: key('gift') });
            await giftsQuery.reload();
        } catch (err) {
            setActionError(apiErrorMessage(err));
        } finally {
            setBusyId(null);
        }
    };

    return (
        <GameLayout title="Cadeaux">
            <div className="space-y-5">
                <Panel
                    title="Rewards store"
                    subtitle="Catalogue cadeaux / redemptions"
                    action={
                        <Link href="/app/gifts/redemptions" className="hud-btn-secondary px-3 py-1.5 text-xs">
                            Mes redemptions
                        </Link>
                    }
                >
                    {giftsQuery.loading && <p className="text-sm text-muted">Chargement...</p>}
                    {giftsQuery.error && (
                        <EmptyState title="Endpoint manquant" description={`Impossible de charger le catalogue (${giftsQuery.error}).`} />
                    )}
                    {actionError && <p className="text-sm text-red-300">{actionError}</p>}

                    {!giftsQuery.loading && !giftsQuery.error && (giftsQuery.data ?? []).length === 0 && (
                        <EmptyState title="Aucun cadeau" description="Catalogue vide pour le moment." />
                    )}

                    {!giftsQuery.loading && !giftsQuery.error && (giftsQuery.data ?? []).length > 0 && (
                        <div className="space-y-2">
                            {(giftsQuery.data ?? []).map((gift) => (
                                <ListItem
                                    key={gift.id}
                                    title={gift.title}
                                    meta={gift.description}
                                    action={
                                        <Button
                                            type="button"
                                            className="px-3 py-1.5 text-xs"
                                            disabled={busyId === gift.id}
                                            onClick={() => redeem(gift.id)}
                                        >
                                            Redeem
                                        </Button>
                                    }
                                >
                                    <div className="mt-2 flex flex-wrap gap-2">
                                        <Badge variant="status">{formatNumber(gift.cost_points ?? 0)} rp</Badge>
                                        <Badge variant="status">Stock {formatNumber(gift.stock ?? 0)}</Badge>
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
