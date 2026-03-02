export default function ListItem({ title, meta, action, children, className = '' }) {
    return (
        <article className={`rounded-hud border border-ui-border/16 bg-ui-surface/92 px-3 py-2.5 ${className}`}>
            <div className="flex items-start justify-between gap-3">
                <div className="min-w-0">
                    <h3 className="truncate text-sm font-semibold text-white">{title}</h3>
                    {meta && <p className="mt-0.5 text-xs text-ui-muted">{meta}</p>}
                    {children}
                </div>
                {action && <div className="shrink-0">{action}</div>}
            </div>
        </article>
    );
}
