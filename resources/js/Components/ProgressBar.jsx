export default function ProgressBar({ value = 0, max = 100, label }) {
    const percentage = Math.max(0, Math.min(100, max > 0 ? Math.round((value / max) * 100) : 0));

    return (
        <div className="space-y-1.5">
            {label && <p className="text-xs text-ui-muted">{label}</p>}
            <div className="toy-progress">
                <span style={{ width: `${percentage}%` }} />
            </div>
            <p className="text-xs text-ui-muted">{percentage}%</p>
        </div>
    );
}
