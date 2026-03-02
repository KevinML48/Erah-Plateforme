import ProgressBar from './ProgressBar';
import Tile from './ui/Tile';

export default function KpiCard({ title, value, help, progress, max = 100 }) {
    return (
        <Tile size="s">
            <p className="font-display text-[0.68rem] uppercase tracking-[0.18em] text-ui-muted">{title}</p>
            <p className="hud-kpi mt-2">{value}</p>
            {typeof progress === 'number' && (
                <div className="mt-3">
                    <ProgressBar value={progress} max={max} />
                </div>
            )}
            {help && <p className="mt-2 text-xs text-ui-muted">{help}</p>}
        </Tile>
    );
}
