export default function FloatingAction({ onClick, label = 'Action', className = '', children }) {
    return (
        <button
            type="button"
            onClick={onClick}
            aria-label={label}
            className={`inline-flex h-11 w-11 items-center justify-center rounded-full border border-ui-border/25 bg-ui-panel text-white shadow-tile-soft transition hover:-translate-y-0.5 hover:border-ui-red/50 ${className}`}
        >
            {children ?? (
                <svg viewBox="0 0 24 24" className="h-5 w-5" fill="none" stroke="currentColor" strokeWidth="2">
                    <path d="M12 5v14M5 12h14" />
                </svg>
            )}
        </button>
    );
}

