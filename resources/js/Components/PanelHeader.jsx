export default function PanelHeader({ title, subtitle, action }) {
    if (!title && !subtitle && !action) {
        return null;
    }

    return (
        <header className="mb-4 flex items-start justify-between gap-3">
            <div className="min-w-0">
                {title && <h2 className="truncate font-display text-lg font-bold tracking-wide">{title}</h2>}
                {subtitle && <p className="mt-0.5 text-sm text-muted">{subtitle}</p>}
            </div>
            {action && <div className="shrink-0">{action}</div>}
        </header>
    );
}
