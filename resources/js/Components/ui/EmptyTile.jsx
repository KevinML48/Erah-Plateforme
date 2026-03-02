import PillButton from './PillButton';

export default function EmptyTile({ title, description, actionLabel, onAction, className = '' }) {
    return (
        <div className={`toy-empty ${className}`}>
            <p className="font-display text-lg font-bold">{title}</p>
            {description && <p className="mt-1 text-sm text-ui-muted">{description}</p>}
            {actionLabel && onAction && (
                <PillButton className="mt-3" variant="secondary" onClick={onAction}>
                    {actionLabel}
                </PillButton>
            )}
        </div>
    );
}

