import Badge from '../../Components/Badge';
import EmptyState from '../../Components/EmptyState';
import ListItem from '../../Components/ListItem';
import Panel from '../../Components/Panel';
import ProgressBar from '../../Components/ProgressBar';
import GameLayout from '../../Layouts/GameLayout';
import useApiData from '../../lib/useApiData';
import { formatNumber } from '../../lib/format';

function MissionList({ title, query }) {
    return (
        <Panel title={title}>
            {query.loading && <p className="text-sm text-muted">Chargement...</p>}
            {query.error && (
                <EmptyState
                    title="Endpoint manquant"
                    description={`Impossible de charger ${title.toLowerCase()} (API: ${query.error}).`}
                />
            )}
            {!query.loading && !query.error && (query.data ?? []).length === 0 && (
                <EmptyState title="Aucune mission" description="Aucune mission disponible." />
            )}

            {!query.loading && !query.error && (query.data ?? []).length > 0 && (
                <div className="space-y-2">
                    {(query.data ?? []).map((mission) => (
                        <ListItem
                            key={mission.id ?? mission.key}
                            title={mission.title ?? mission.key ?? 'Mission'}
                            meta={mission.description ?? 'Mission progression'}
                        >
                            <div className="mt-2 space-y-2">
                                <ProgressBar value={mission.progress_count ?? 0} max={mission.target_count ?? 1} />
                                <div className="flex flex-wrap gap-2">
                                    <Badge variant="status">
                                        {formatNumber(mission.progress_count ?? 0)}/{formatNumber(mission.target_count ?? 0)}
                                    </Badge>
                                    {mission.rewards?.points ? (
                                        <Badge variant="league">+{mission.rewards.points} points</Badge>
                                    ) : null}
                                </div>
                            </div>
                        </ListItem>
                    ))}
                </div>
            )}
        </Panel>
    );
}

export default function MissionsIndex() {
    const todayQuery = useApiData('/missions/today');
    const weeklyQuery = useApiData('/missions/weekly');

    return (
        <GameLayout title="Missions">
            <div className="space-y-5">
                <MissionList title="Missions du jour" query={todayQuery} />
                <MissionList title="Missions hebdo" query={weeklyQuery} />
            </div>
        </GameLayout>
    );
}
