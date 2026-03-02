import { useEffect, useState } from 'react';

import Badge from '../../../Components/Badge';
import Button from '../../../Components/Button';
import Input from '../../../Components/Input';
import ListItem from '../../../Components/ListItem';
import Panel from '../../../Components/Panel';
import Textarea from '../../../Components/Textarea';
import GameLayout from '../../../Layouts/GameLayout';
import api, { apiErrorMessage } from '../../../lib/api';
import { formatDate } from '../../../lib/format';

export default function AdminClipsIndex() {
    const [clips, setClips] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [actionError, setActionError] = useState(null);
    const [busy, setBusy] = useState(false);
    const [form, setForm] = useState({
        title: '',
        description: '',
        video_url: '',
        thumbnail_url: '',
    });

    const load = async () => {
        setLoading(true);
        setError(null);
        try {
            const response = await api.get('/clips', { params: { sort: 'recent', limit: 50 } });
            setClips(response?.data?.data ?? []);
        } catch (err) {
            setError(apiErrorMessage(err));
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        load();
    }, []);

    const createClip = async (event) => {
        event.preventDefault();
        setBusy(true);
        setActionError(null);
        try {
            await api.post('/admin/clips', form);
            setForm({ title: '', description: '', video_url: '', thumbnail_url: '' });
            await load();
        } catch (err) {
            setActionError(apiErrorMessage(err));
        } finally {
            setBusy(false);
        }
    };

    const publish = async (id) => {
        setBusy(true);
        setActionError(null);
        try {
            await api.post(`/admin/clips/${id}/publish`);
            await load();
        } catch (err) {
            setActionError(apiErrorMessage(err));
        } finally {
            setBusy(false);
        }
    };

    const remove = async (id) => {
        setBusy(true);
        setActionError(null);
        try {
            await api.delete(`/admin/clips/${id}`);
            await load();
        } catch (err) {
            setActionError(apiErrorMessage(err));
        } finally {
            setBusy(false);
        }
    };

    return (
        <GameLayout title="Admin Clips">
            <div className="space-y-5">
                <Panel title="Nouveau clip" subtitle="Admin create/publish/delete via API">
                    <form className="grid gap-3 md:grid-cols-2" onSubmit={createClip}>
                        <Input label="Titre" value={form.title} onChange={(event) => setForm((prev) => ({ ...prev, title: event.target.value }))} />
                        <Input label="Video URL" value={form.video_url} onChange={(event) => setForm((prev) => ({ ...prev, video_url: event.target.value }))} />
                        <Input className="md:col-span-2" label="Thumbnail URL" value={form.thumbnail_url} onChange={(event) => setForm((prev) => ({ ...prev, thumbnail_url: event.target.value }))} />
                        <Textarea className="md:col-span-2" label="Description" value={form.description} onChange={(event) => setForm((prev) => ({ ...prev, description: event.target.value }))} />
                        <div className="md:col-span-2">
                            <Button type="submit" disabled={busy}>{busy ? 'Creation...' : 'Creer clip'}</Button>
                        </div>
                    </form>
                    {actionError && <p className="mt-2 text-sm text-red-300">{actionError}</p>}
                </Panel>

                <Panel title="Clips publics (feed)" subtitle="Apercu des clips actuellement visibles">
                    {loading && <p className="text-sm text-muted">Chargement...</p>}
                    {error && <p className="text-sm text-red-300">{error}</p>}
                    {!loading && !error && (
                        <div className="space-y-2">
                            {clips.map((clip) => (
                                <ListItem
                                    key={clip.id}
                                    title={clip.title}
                                    meta={formatDate(clip.published_at)}
                                    action={<Badge variant="status">ID {clip.id}</Badge>}
                                >
                                    <div className="mt-2 flex flex-wrap gap-2">
                                        <Button variant="secondary" className="px-3 py-1.5 text-xs" disabled={busy} onClick={() => publish(clip.id)}>
                                            Publish
                                        </Button>
                                        <Button variant="danger" className="px-3 py-1.5 text-xs" disabled={busy} onClick={() => remove(clip.id)}>
                                            Delete
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
