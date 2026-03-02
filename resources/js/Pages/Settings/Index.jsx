import { router } from '@inertiajs/react';
import { useEffect, useState } from 'react';

import Button from '../../Components/Button';
import EmptyState from '../../Components/EmptyState';
import Panel from '../../Components/Panel';
import Toggle from '../../Components/Toggle';
import GameLayout from '../../Layouts/GameLayout';
import api, { apiErrorMessage } from '../../lib/api';

const defaultCategories = ['duel', 'clips', 'system', 'match', 'bet'];

export default function SettingsIndex() {
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [saving, setSaving] = useState(false);
    const [channels, setChannels] = useState({ email_opt_in: true, push_opt_in: true });
    const [categories, setCategories] = useState({});

    useEffect(() => {
        const load = async () => {
            setLoading(true);
            setError(null);
            try {
                const response = await api.get('/me/notification-preferences');
                setChannels(response?.data?.channels ?? { email_opt_in: true, push_opt_in: true });
                setCategories(response?.data?.categories ?? {});
            } catch (err) {
                setError(apiErrorMessage(err));
            } finally {
                setLoading(false);
            }
        };

        load();
    }, []);

    const updateCategory = (key, field, value) => {
        setCategories((prev) => ({
            ...prev,
            [key]: {
                ...(prev[key] ?? { email_enabled: true, push_enabled: true }),
                [field]: value,
            },
        }));
    };

    const save = async () => {
        setSaving(true);
        setError(null);
        try {
            await api.put('/me/notification-preferences', {
                channels,
                categories,
            });
        } catch (err) {
            setError(apiErrorMessage(err));
        } finally {
            setSaving(false);
        }
    };

    return (
        <GameLayout title="Settings">
            <div className="space-y-5">
                <Panel title="Preferences notifications" subtitle="Configuration globale + categories">
                    {loading && <p className="text-sm text-muted">Chargement...</p>}
                    {error && <p className="text-sm text-red-300">{error}</p>}

                    {!loading && !error && (
                        <div className="space-y-3">
                            <Toggle
                                label="Email global"
                                description="Autoriser les notifications email"
                                checked={Boolean(channels.email_opt_in)}
                                onChange={(checked) => setChannels((prev) => ({ ...prev, email_opt_in: checked }))}
                            />
                            <Toggle
                                label="Push global"
                                description="Autoriser les notifications push"
                                checked={Boolean(channels.push_opt_in)}
                                onChange={(checked) => setChannels((prev) => ({ ...prev, push_opt_in: checked }))}
                            />

                            {(Object.keys(categories).length ? Object.keys(categories) : defaultCategories).map((category) => (
                                <div key={category} className="rounded-hud border border-erah-border/12 bg-erah-surface p-3">
                                    <p className="mb-2 text-sm font-semibold uppercase">{category}</p>
                                    <div className="grid gap-2 sm:grid-cols-2">
                                        <Toggle
                                            label="Email"
                                            checked={Boolean((categories[category] ?? {}).email_enabled)}
                                            onChange={(checked) => updateCategory(category, 'email_enabled', checked)}
                                        />
                                        <Toggle
                                            label="Push"
                                            checked={Boolean((categories[category] ?? {}).push_enabled)}
                                            onChange={(checked) => updateCategory(category, 'push_enabled', checked)}
                                        />
                                    </div>
                                </div>
                            ))}

                            <Button type="button" onClick={save} disabled={saving}>
                                {saving ? 'Sauvegarde...' : 'Sauvegarder'}
                            </Button>
                        </div>
                    )}
                </Panel>

                <Panel title="Session">
                    <div className="space-y-3">
                        <EmptyState title="Logout securise" description="Finir la session actuelle." />
                        <Button
                            type="button"
                            variant="secondary"
                            onClick={() => router.post('/logout')}
                        >
                            Se deconnecter
                        </Button>
                    </div>
                </Panel>
            </div>
        </GameLayout>
    );
}
