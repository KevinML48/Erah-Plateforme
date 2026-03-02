import { Link } from '@inertiajs/react';

import Badge from '../../Components/Badge';
import Button from '../../Components/Button';
import EmptyState from '../../Components/EmptyState';
import ListItem from '../../Components/ListItem';
import Panel from '../../Components/Panel';
import GameLayout from '../../Layouts/GameLayout';
import api, { apiErrorMessage } from '../../lib/api';
import useApiData from '../../lib/useApiData';
import { formatDate } from '../../lib/format';

export default function NotificationsIndex() {
    const notificationsQuery = useApiData('/notifications?limit=50');

    const markRead = async (notificationId) => {
        try {
            await api.post(`/notifications/${notificationId}/read`);
            await notificationsQuery.reload();
        } catch (err) {
            notificationsQuery.setData((old) => old);
        }
    };

    const markAll = async () => {
        const rows = notificationsQuery.data ?? [];
        const unread = rows.filter((item) => !item.read_at);
        if (!unread.length) {
            return;
        }
        try {
            await Promise.all(unread.map((item) => api.post(`/notifications/${item.id}/read`)));
            await notificationsQuery.reload();
        } catch (err) {
            // fallback: show global error below
            notificationsQuery.setData((old) => old);
            alert(apiErrorMessage(err));
        }
    };

    return (
        <GameLayout title="Notifications">
            <div className="space-y-5">
                <Panel
                    title="Centre notifications"
                    subtitle="In-app notifications"
                    action={
                        <div className="flex flex-wrap gap-2">
                            <Button variant="secondary" className="px-3 py-1.5 text-xs" onClick={markAll}>
                                Tout lire
                            </Button>
                            <Link href="/app/settings" className="hud-btn-secondary px-3 py-1.5 text-xs">
                                Preferences
                            </Link>
                        </div>
                    }
                >
                    {notificationsQuery.loading && <p className="text-sm text-muted">Chargement...</p>}
                    {notificationsQuery.error && <p className="text-sm text-red-300">{notificationsQuery.error}</p>}

                    {!notificationsQuery.loading && !notificationsQuery.error && (notificationsQuery.data ?? []).length === 0 && (
                        <EmptyState title="Aucune notification" description="Tu es a jour." />
                    )}

                    {!notificationsQuery.loading && !notificationsQuery.error && (notificationsQuery.data ?? []).length > 0 && (
                        <div className="space-y-2">
                            {(notificationsQuery.data ?? []).map((notification) => (
                                <ListItem
                                    key={notification.id}
                                    title={notification.title ?? 'Notification'}
                                    meta={formatDate(notification.created_at)}
                                    className={!notification.read_at ? 'border-erah-red/30 bg-erah-surface/95' : ''}
                                    action={
                                        !notification.read_at ? (
                                            <Button variant="secondary" className="px-3 py-1.5 text-xs" onClick={() => markRead(notification.id)}>
                                                Lire
                                            </Button>
                                        ) : null
                                    }
                                >
                                    <div className="mt-1 flex flex-wrap gap-2">
                                        <Badge variant="category">{notification.category ?? 'system'}</Badge>
                                    </div>
                                    <p className="mt-1 text-sm text-muted-strong">{notification.body}</p>
                                </ListItem>
                            ))}
                        </div>
                    )}
                </Panel>
            </div>
        </GameLayout>
    );
}
