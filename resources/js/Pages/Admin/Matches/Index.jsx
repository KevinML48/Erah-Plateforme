import { useEffect, useState } from 'react';

import Badge from '../../../Components/Badge';
import Button from '../../../Components/Button';
import Input from '../../../Components/Input';
import ListItem from '../../../Components/ListItem';
import Panel from '../../../Components/Panel';
import GameLayout from '../../../Layouts/GameLayout';
import api, { apiErrorMessage } from '../../../lib/api';
import { formatDate } from '../../../lib/format';

const idem = (prefix) => `${prefix}-${Date.now()}-${Math.random().toString(16).slice(2, 9)}`;

export default function AdminMatchesIndex() {
    const [rows, setRows] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [actionError, setActionError] = useState(null);
    const [busy, setBusy] = useState(false);
    const [form, setForm] = useState({
        team_a_name: '',
        team_b_name: '',
        starts_at: '',
        locked_at: '',
        game_key: '',
    });

    const load = async () => {
        setLoading(true);
        setError(null);
        try {
            const response = await api.get('/matches', { params: { limit: 50 } });
            setRows(response?.data?.data ?? []);
        } catch (err) {
            setError(apiErrorMessage(err));
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        load();
    }, []);

    const create = async (event) => {
        event.preventDefault();
        setBusy(true);
        setActionError(null);
        try {
            await api.post('/admin/matches', form);
            setForm({ team_a_name: '', team_b_name: '', starts_at: '', locked_at: '', game_key: '' });
            await load();
        } catch (err) {
            setActionError(apiErrorMessage(err));
        } finally {
            setBusy(false);
        }
    };

    const settle = async (matchId) => {
        setBusy(true);
        setActionError(null);
        try {
            await api.post(`/admin/matches/${matchId}/settle`, {
                idempotency_key: idem('app-settle'),
            });
            await load();
        } catch (err) {
            setActionError(apiErrorMessage(err));
        } finally {
            setBusy(false);
        }
    };

    return (
        <GameLayout title="Admin Matchs">
            <div className="space-y-5">
                <Panel title="Creer match" subtitle="Admin endpoint /api/admin/matches">
                    <form className="grid gap-3 md:grid-cols-2" onSubmit={create}>
                        <Input label="Team A" value={form.team_a_name} onChange={(event) => setForm((prev) => ({ ...prev, team_a_name: event.target.value }))} />
                        <Input label="Team B" value={form.team_b_name} onChange={(event) => setForm((prev) => ({ ...prev, team_b_name: event.target.value }))} />
                        <Input label="Starts at" type="datetime-local" value={form.starts_at} onChange={(event) => setForm((prev) => ({ ...prev, starts_at: event.target.value }))} />
                        <Input label="Locked at" type="datetime-local" value={form.locked_at} onChange={(event) => setForm((prev) => ({ ...prev, locked_at: event.target.value }))} />
                        <Input className="md:col-span-2" label="Game key" value={form.game_key} onChange={(event) => setForm((prev) => ({ ...prev, game_key: event.target.value }))} />
                        <div className="md:col-span-2">
                            <Button type="submit" disabled={busy}>{busy ? 'Creation...' : 'Creer match'}</Button>
                        </div>
                    </form>
                    {actionError && <p className="mt-2 text-sm text-red-300">{actionError}</p>}
                </Panel>

                <Panel title="Liste matchs" subtitle="Settle idempotent via API">
                    {loading && <p className="text-sm text-muted">Chargement...</p>}
                    {error && <p className="text-sm text-red-300">{error}</p>}
                    {!loading && !error && (
                        <div className="space-y-2">
                            {rows.map((match) => (
                                <ListItem
                                    key={match.id}
                                    title={`${match.home_team} vs ${match.away_team}`}
                                    meta={formatDate(match.starts_at)}
                                    action={<Badge variant="status">{match.status}</Badge>}
                                >
                                    <div className="mt-2 flex flex-wrap gap-2">
                                        <Button variant="secondary" className="px-3 py-1.5 text-xs" disabled={busy} onClick={() => settle(match.id)}>
                                            Settle
                                        </Button>
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
