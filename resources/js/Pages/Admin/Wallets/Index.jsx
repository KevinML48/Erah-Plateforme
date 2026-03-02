import { useState } from 'react';

import Button from '../../../Components/Button';
import EmptyState from '../../../Components/EmptyState';
import Input from '../../../Components/Input';
import Panel from '../../../Components/Panel';
import GameLayout from '../../../Layouts/GameLayout';
import api, { apiErrorMessage } from '../../../lib/api';

export default function AdminWalletsIndex() {
    const [form, setForm] = useState({ user_id: '', amount: '', reason: '' });
    const [busy, setBusy] = useState(false);
    const [error, setError] = useState(null);
    const [success, setSuccess] = useState(null);

    const submit = async (event) => {
        event.preventDefault();
        setBusy(true);
        setError(null);
        setSuccess(null);
        try {
            await api.post('/admin/points/grant', {
                user_id: Number.parseInt(form.user_id, 10),
                amount: Number.parseInt(form.amount, 10),
                kind: 'rank',
                source_type: 'admin_manual',
                source_id: `app-admin-${Date.now()}`,
                metadata: { reason: form.reason || 'manual grant' },
            });
            setSuccess('Points accordes.');
        } catch (err) {
            setError(apiErrorMessage(err));
        } finally {
            setBusy(false);
        }
    };

    return (
        <GameLayout title="Admin Wallets">
            <div className="space-y-5">
                <Panel title="Grant points (admin)" subtitle="Endpoint disponible: /api/admin/points/grant">
                    <form className="grid gap-3 md:grid-cols-2" onSubmit={submit}>
                        <Input label="User ID" type="number" value={form.user_id} onChange={(event) => setForm((prev) => ({ ...prev, user_id: event.target.value }))} />
                        <Input label="Amount" type="number" value={form.amount} onChange={(event) => setForm((prev) => ({ ...prev, amount: event.target.value }))} />
                        <Input className="md:col-span-2" label="Reason" value={form.reason} onChange={(event) => setForm((prev) => ({ ...prev, reason: event.target.value }))} />
                        <div className="md:col-span-2">
                            <Button type="submit" disabled={busy}>{busy ? 'Traitement...' : 'Grant'}</Button>
                        </div>
                    </form>
                    {error && <p className="mt-2 text-sm text-red-300">{error}</p>}
                    {success && <p className="mt-2 text-sm text-emerald-300">{success}</p>}
                </Panel>

                <Panel title="Bet wallet admin">
                    <EmptyState
                        title="Endpoint API wallet grant manquant"
                        description="Le grant bet_points est disponible via l'interface web legacy (/admin/wallets/grant)."
                    />
                </Panel>
            </div>
        </GameLayout>
    );
}
