const variants = {
    default: 'border-ui-border/25 bg-ui-surface text-ui-text',
    new: 'border-ui-red/35 bg-ui-red/20 text-red-100',
    success: 'border-emerald-500/35 bg-emerald-500/20 text-emerald-100',
    warning: 'border-amber-400/35 bg-amber-400/20 text-amber-100',
    danger: 'border-red-500/40 bg-red-500/20 text-red-100',
    dark: 'border-ui-border/20 bg-ui-panel text-ui-muted/95',
};

export default function StatPill({ variant = 'default', children, className = '' }) {
    return (
        <span
            className={`inline-flex items-center rounded-pill border px-2.5 py-1 text-[11px] font-bold uppercase tracking-[0.08em] ${variants[variant] ?? variants.default} ${className}`}
        >
            {children}
        </span>
    );
}

