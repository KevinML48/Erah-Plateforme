import { useMemo, useState } from 'react';

import Badge from '../../Components/Badge';
import Button from '../../Components/Button';
import EmptyState from '../../Components/EmptyState';
import Input from '../../Components/Input';
import ListItem from '../../Components/ListItem';
import Panel from '../../Components/Panel';
import Tabs from '../../Components/Tabs';
import Textarea from '../../Components/Textarea';
import GameLayout from '../../Layouts/GameLayout';
import api, { apiErrorMessage } from '../../lib/api';
import useApiData from '../../lib/useApiData';
import { formatDate } from '../../lib/format';

const tabItems = [
    { label: 'Pending', value: 'pending' },
    { label: 'Active', value: 'accepted' },
    { label: 'Finished', value: 'refused' },
];

const uniqueKey = (prefix) => `${prefix}-${Date.now()}-${Math.random().toString(16).slice(2, 9)}`;

export default function DuelsIndex() {
    const [status, setStatus] = useState('pending');
    const [challengedUserId, setChallengedUserId] = useState('');
    const [message, setMessage] = useState('');
    const [actionError, setActionError] = useState(null);
    const [busy, setBusy] = useState(false);

    const duelsQuery = useApiData(`/duels?status=${status}&limit=50`);

    const rows = useMemo(() => duelsQuery.data ?? [], [duelsQuery.data]);

    const createDuel = async (event) => {
        event.preventDefault();
        const target = Number.parseInt(challengedUserId, 10);
        if (!Number.isFinite(target) || target < 1) {
            setActionError('ID utilisateur invalide.');
            return;
        }

        setBusy(true);
        setActionError(null);
        try {
            await api.post('/duels', {
                challenged_user_id: target,
                message: message || null,
                idempotency_key: uniqueKey('app-duel'),
            });
            setChallengedUserId('');
            setMessage('');
            await duelsQuery.reload();
        } catch (err) {
            setActionError(apiErrorMessage(err));
        } finally {
            setBusy(false);
        }
    };

    const act = async (id, action) => {
        setBusy(true);
        setActionError(null);
        try {
            await api.post(`/duels/${id}/${action}`);
            await duelsQuery.reload();
        } catch (err) {
            setActionError(apiErrorMessage(err));
        } finally {
            setBusy(false);
        }
    };

    return (
        <GameLayout title="Duels">
            <div className="space-y-5">
                <Panel title="Creer un duel" subtitle="V1 simple: saisis l'ID utilisateur a defier">
                    <form className="grid gap-3 md:grid-cols-2" onSubmit={createDuel}>
                        <Input
                            label="ID utilisateur cible"
                            value={challengedUserId}
                            onChange={(event) => setChallengedUserId(event.target.value)}
                            type="number"
                            min={1}
                        />
                        <Textarea
                            label="Message (optionnel)"
                            className="md:col-span-2"
                            value={message}
                            onChange={(event) => setMessage(event.target.value)}
                        />
                        <div className="md:col-span-2">
                            <Button type="submit" disabled={busy}>
                                {busy ? 'Envoi...' : 'Envoyer duel'}
                            </Button>
                        </div>
                    </form>
                    {actionError && <p className="mt-2 text-sm text-red-300">{actionError}</p>}
                </Panel>

                <Panel title="Mes duels" subtitle="Workflow pending / active / finished">
                    <div className="space-y-3">
                        <Tabs items={tabItems} active={status} onChange={setStatus} />
                        {duelsQuery.loading && <p className="text-sm text-muted">Chargement...</p>}
                        {duelsQuery.error && <p className="text-sm text-red-300">{duelsQuery.error}</p>}

                        {!duelsQuery.loading && !duelsQuery.error && rows.length === 0 && (
                            <EmptyState title="Aucun duel" description="Aucun duel pour cet onglet." />
                        )}

                        {!duelsQuery.loading && !duelsQuery.error && rows.length > 0 && (
                            <div className="space-y-2">
                                {rows.map((duel) => (
                                    <ListItem
                                        key={duel.id}
                                        title={`${duel.challenger?.name ?? 'Challenger'} vs ${duel.challenged?.name ?? 'Challenged'}`}
                                        meta={`${duel.status} - ${formatDate(duel.requested_at)}`}
                                        action={<Badge variant="status">{duel.status}</Badge>}
                                    >
                                        {duel.message && <p className="mt-1 text-sm text-muted-strong">{duel.message}</p>}
                                        {duel.status === 'pending' && (
                                            <div className="mt-2 flex flex-wrap gap-2">
                                                <Button variant="secondary" className="px-3 py-1.5 text-xs" onClick={() => act(duel.id, 'accept')} disabled={busy}>
                                                    Accepter
                                                </Button>
                                                <Button variant="danger" className="px-3 py-1.5 text-xs" onClick={() => act(duel.id, 'refuse')} disabled={busy}>
                                                    Refuser
                                                </Button>
                                            </div>
                                        )}
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
